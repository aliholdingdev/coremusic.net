---
title: "Şifre Hashleme"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Şifre Hashleme

## Genel Bakış

COREMUSIC'te kullanıcı şifreleri asla düz metin olarak saklanmaz. Argon2id (önerilen) ve bcrypt algoritmaları ile hashlenmiş olarak depolanır. Güçlü şifre politikası, salt rotation ve timing attack koruması ile çok katmanlı şifre güvenliği sağlanır.

## Teknik Detaylar

### Algoritma Karşılaştırması

| Özellik | Argon2id | bcrypt | scrypt |
|---------|----------|--------|--------|
| Memory Hardness | ✅ | ❌ | ✅ |
| GPU/ASIC Direnci | Yüksek | Orta | Yüksek |
| OWASP Önerisi | ✅ Birinci | ✅ İkinci | ⚠️ |
| Time Cost | 1-5 saniye | 10-12 rounds | - |
| Memory Cost | 64MB-1GB | - | 16MB-1GB |
| Parallelism | 1-8 threads | - | - |

### Argon2id Parametreleri

```
Algorithm: Argon2id
Memory: 65536 KB (64 MB)
Iterations: 3
Parallelism: 4
Salt: 16 byte (random)
Output: 32 byte
```

### Şifre Politikası

- Minimum 12 karakter
- Büyük harf, küçük harf, rakam, özel karakter zorunlu
- Son 10 şifre tekrarlanamaz
- Breach database kontrolü (HaveIBeenPwned API)
- Şifre strength meter zorunlu

### Salt Stratejisi

Her kullanıcı için benzersiz 16 byte salt üretilir. Salt, hash'in bir parçası olarak saklanır. Salt'lar asla tekrar kullanılmaz.

## Konfigürasyon / Kod

```typescript
import argon2 from 'argon2';
import bcrypt from 'bcrypt';

// Argon2id Konfigürasyonu
const ARGON2_OPTIONS: argon2.Options = {
  type: argon2.argon2id,
  memoryCost: 65536, // 64 MB
  timeCost: 3,
  parallelism: 4,
  saltLength: 16,
  hashLength: 32,
};

// Şifre Hashleme (Argon2id)
async function hashPassword(password: string): Promise<string> {
  const hash = await argon2.hash(password, ARGON2_OPTIONS);
  return hash;
}

// Şifre Doğrulama (Argon2id)
async function verifyPassword(
  password: string,
  hash: string
): Promise<boolean> {
  try {
    return await argon2.verify(hash, password);
  } catch {
    return false;
  }
}

// bcrypt Fallback (Legacy Support)
async function hashPasswordBcrypt(
  password: string,
  saltRounds: number = 12
): Promise<string> {
  return bcrypt.hash(password, saltRounds);
}

async function verifyPasswordBcrypt(
  password: string,
  hash: string
): Promise<boolean> {
  return bcrypt.compare(password, hash);
}

// Şifre Politikası Kontrolü
interface PasswordPolicyResult {
  isValid: boolean;
  errors: string[];
  strength: 'weak' | 'fair' | 'good' | 'strong';
  score: number;
}

function validatePasswordPolicy(
  password: string,
  userMetadata?: {
    username?: string;
    email?: string;
    previousPasswords?: string[];
  }
): PasswordPolicyResult {
  const errors: string[] = [];
  let score = 0;

  // Uzunluk kontrolü
  if (password.length < 12) {
    errors.push('Şifre en az 12 karakter olmalıdır');
  } else if (password.length >= 16) {
    score += 2;
  } else {
    score += 1;
  }

  // Büyük harf kontrolü
  if (!/[A-Z]/.test(password)) {
    errors.push('En az bir büyük harf içermelidir');
  } else {
    score += 1;
  }

  // Küçük harf kontrolü
  if (!/[a-z]/.test(password)) {
    errors.push('En az bir küçük harf içermelidir');
  } else {
    score += 1;
  }

  // Rakam kontrolü
  if (!/[0-9]/.test(password)) {
    errors.push('En az bir rakam içermelidir');
  } else {
    score += 1;
  }

  // Özel karakter kontrolü
  if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
    errors.push('En az bir özel karakter içermelidir');
  } else {
    score += 1;
  }

  // Kullanıcı bilgileriyle aynı olamaz
  if (userMetadata) {
    const lowerPassword = password.toLowerCase();
    if (
      userMetadata.username &&
      lowerPassword.includes(userMetadata.username.toLowerCase())
    ) {
      errors.push('Şifre kullanıcı adını içermemelidir');
    }
    if (
      userMetadata.email &&
      lowerPassword.includes(userMetadata.email.split('@')[0].toLowerCase())
    ) {
      errors.push('Şifre e-posta adresini içermemelidir');
    }
  }

  // Önceki şifrelerle aynı olamaz
  if (userMetadata?.previousPasswords) {
    for (const prevHash of userMetadata.previousPasswords) {
      const isSame = await verifyPassword(password, prevHash);
      if (isSame) {
        errors.push('Şifre son 10 şifrenizden biriyle aynı olamaz');
        break;
      }
    }
  }

  // Yaygın şifre listesi kontrolü
  const commonPasswords = await getCommonPasswords();
  if (commonPasswords.includes(password.toLowerCase())) {
    errors.push('Bu şifre çok yaygın kullanılmaktadır');
    score = 0;
  }

  // Strength hesapla
  let strength: PasswordPolicyResult['strength'] = 'weak';
  if (score >= 6) strength = 'strong';
  else if (score >= 5) strength = 'good';
  else if (score >= 3) strength = 'fair';

  return {
    isValid: errors.length === 0,
    errors,
    strength,
    score,
  };
}

// HaveIBeenPwned Entegrasyonu
async function checkBreachDatabase(
  password: string
): Promise<boolean> {
  const sha1 = crypto.createHash('sha1')
    .update(password)
    .digest('hex')
    .toUpperCase();

  const prefix = sha1.substring(0, 5);
  const suffix = sha1.substring(5);

  const response = await fetch(
    `https://api.pwnedpasswords.com/range/${prefix}`
  );

  const text = await response.text();
  const hashes = text.split('\n');

  for (const hash of hashes) {
    const [hashSuffix, count] = hash.split(':');
    if (hashSuffix === suffix) {
      return true; // Şifre sızıntıda
    }
  }

  return false; // Şifre güvenli
}

// Şifre Hash Migration
async function migratePasswordHash(
  password: string,
  oldHash: string
): Promise<{ newHash: string; needsMigration: boolean }> {
  // Eski hash'in tipini kontrol et
  const isArgon2 = oldHash.startsWith('$argon2');
  const isBcrypt = oldHash.startsWith('$2b$') || oldHash.startsWith('$2a$');

  if (isArgon2) {
    return { newHash: oldHash, needsMigration: false };
  }

  // bcrypt'ten Argon2id'e migration
  if (isBcrypt) {
    const isValid = await verifyPasswordBcrypt(password, oldHash);
    if (isValid) {
      const newHash = await hashPassword(password);
      return { newHash, needsMigration: true };
    }
  }

  throw new Error('Password verification failed during migration');
}

// Timing Attack Koruması
function constantTimeCompare(a: string, b: string): boolean {
  if (a.length !== b.length) {
    // Hala constant time olmalı
    let result = 0;
    for (let i = 0; i < a.length; i++) {
      result |= a.charCodeAt(i) ^ b.charCodeAt(b.length - 1);
    }
    return false;
  }

  let result = 0;
  for (let i = 0; i < a.length; i++) {
    result |= a.charCodeAt(i) ^ b.charCodeAt(i);
  }
  return result === 0;
}

// Şifre Sıfırlama Token'ı
async function generatePasswordResetToken(
  userId: string
): Promise<string> {
  const token = crypto.randomBytes(32).toString('hex');
  const hashedToken = crypto
    .createHash('sha256')
    .update(token)
    .digest('hex');

  await db.passwordResets.create({
    data: {
      userId,
      token: hashedToken,
      expiresAt: new Date(Date.now() + 3600000), // 1 saat
      used: false,
    },
  });

  return token;
}

async function verifyPasswordResetToken(
  token: string
): Promise<string | null> {
  const hashedToken = crypto
    .createHash('sha256')
    .update(token)
    .digest('hex');

  const resetRecord = await db.passwordResets.findFirst({
    where: {
      token: hashedToken,
      used: false,
      expiresAt: { gt: new Date() },
    },
  });

  if (!resetRecord) return null;

  // Token'ı tek kullanımlık yap
  await db.passwordResets.update({
    where: { id: resetRecord.id },
    data: { used: true },
  });

  return resetRecord.userId;
}

// Yaygın Şifre Listesi
async function getCommonPasswords(): Promise<string[]> {
  // RockYou ve diğer breach listelerinden
  // Gerçek implementasyonda dosyadan okunur
  return [
    'password', '123456', '12345678', 'qwerty', 'abc123',
    'monkey', '1234567', 'letmein', 'trustno1', 'dragon',
    'baseball', 'iloveyou', 'master', 'sunshine', 'ashley',
    'michael', 'shadow', '123123', '654321', 'superman',
  ];
}
```

## Güvenlik Kontrolleri

- [ ] Argon2id varsayılan algoritma olmalı
- [ ] bcrypt fallback sadece migration için kullanılmalı
- [ ] Salt benzersiz ve rastgele olmalı
- [ ] Şifre politikası zorunlu tutulmalı
- [ ] HaveIBeenPwned kontrolü aktif olmalı
- [ ] Timing attack koruması uygulanmalı
- [ ] Şifre hash'leri loglara yazılmamalı
- [ ] Şifre sıfırlama token'ları 1 saat geçerli olmalı
- [ ] Token tek kullanımlık olmalı
- [ ] Memory'den silme (zeroization) uygulanmalı

## Bağımlılıklar

- **PostgreSQL**: Hash ve metadata depolama
- **audit-logging.md**: Şifre değiştirme logları
- **session-management.md**: Şifre sonrası oturum yenileme
- **vault-secrets.md**: Argon2 parametreleri için secure storage

## Durum: Implementasyon

- [x] Algoritma seçimi yapıldı (Argon2id)
- [x] Şifre politikası tanımlandı
- [ ] Argon2 hash fonksiyonları implemente edilecek
- [ ] Password policy validator yazılacak
- [ ] HaveIBeenPwned entegrasyonu yapılacak
- [ ] Hash migration scripti yazılacak
- [ ] Password reset flow'u kurulacak
- [ ] Unit test yazılacak
