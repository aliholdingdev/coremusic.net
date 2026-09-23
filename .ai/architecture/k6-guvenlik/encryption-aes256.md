---
title: "AES-256-GCM Şifreleme"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# AES-256-GCM Şifreleme

## Genel Bakış

AES-256-GCM (Galois/Counter Mode), COREMUSIC'te verilerin saklandığı ve aktarıldığı sırada şifrelenmesini sağlar. Authenticated encryption sayesinde hem gizlilik hem de bütünlük garantisi verir. Anahtar rotasyonu ile uzun vadeli veri güvenliği sağlanır.

## Teknik Detaylar

### AES-256-GCM Yapısı

```
Plaintext: Ses dosyaları, kullanıcı verileri, API anahtarları
                    │
    ┌───────────────▼───────────────┐
    │  AES-256-GCM Encryption       │
    │  - Key: 256-bit (32 byte)     │
    │  - IV: 96-bit (12 byte)       │
    │  - AAD: Ek authenticated data │
    │  - Tag: 128-bit authentication│
    └───────────────┬───────────────┘
                    │
Ciphertext + IV + Tag → Güvenli depolama
```

### Anahtar Yaşam Döngüsü

1. Anahtar üretilir (Hardware Security Module veya derivation)
2. Anahtar Vault'ta saklanır
3. Periyodik rotation (90 günde bir)
4. Eski anahtarlar deprecated olarak işaretlenir
5. Tüm veriler yeni anahtarla yeniden şifrelenir
6. Eski anahtarlar güvenli olarak imha edilir

### Key Derivation

Master key'den derived key'ler üretilir:
- `deriveKey(masterKey, "audio-encryption")` → Ses dosyaları için
- `deriveKey(masterKey, "user-data")` → Kullanıcı verileri için
- `deriveKey(masterKey, "api-keys")` → API anahtarları için

### Encryption at Rest vs in Transit

- **At Rest**: AES-256-GCM ile disk üzerinde şifreleme
- **In Transit**: TLS 1.3 ile network üzerinde şifreleme
- **Double Encryption**: Hassas veriler için her ikisi birlikte

## Konfigürasyon / Kod

```typescript
import crypto from 'crypto';

// AES-256-GCM Sabitleri
const ALGORITHM = 'aes-256-gcm';
const IV_LENGTH = 12; // 96-bit
const TAG_LENGTH = 16; // 128-bit
const KEY_LENGTH = 32; // 256-bit

// Anahtar Üretimi
function generateMasterKey(): Buffer {
  return crypto.randomBytes(KEY_LENGTH);
}

// Key Derivation (HKDF)
function deriveKey(
  masterKey: Buffer,
  context: string,
  salt?: Buffer
): Buffer {
  const derivedKey = crypto.createHmac('sha256', masterKey)
    .update(context)
    .update(salt || crypto.randomBytes(16))
    .digest();

  return derivedKey;
}

// Şifreleme Fonksiyonu
async function encrypt(
  plaintext: Buffer,
  key: Buffer,
  aad?: Buffer
): Promise<{
  ciphertext: Buffer;
  iv: Buffer;
  tag: Buffer;
  salt?: Buffer;
}> {
  const iv = crypto.randomBytes(IV_LENGTH);
  const cipher = crypto.createCipheriv(ALGORITHM, key, iv, {
    authTagLength: TAG_LENGTH,
  });

  if (aad) {
    cipher.setAAD(aad, { plaintextLength: plaintext.length });
  }

  const encrypted = Buffer.concat([
    cipher.update(plaintext),
    cipher.final(),
  ]);

  const tag = cipher.getAuthTag();

  return { ciphertext: encrypted, iv, tag };
}

// Şifre Çözme Fonksiyonu
async function decrypt(
  ciphertext: Buffer,
  key: Buffer,
  iv: Buffer,
  tag: Buffer,
  aad?: Buffer
): Promise<Buffer> {
  const decipher = crypto.createDecipheriv(ALGORITHM, key, iv, {
    authTagLength: TAG_LENGTH,
  });

  decipher.setAuthTag(tag);

  if (aad) {
    decipher.setAAD(aad, { plaintextLength: ciphertext.length });
  }

  const decrypted = Buffer.concat([
    decipher.update(ciphertext),
    decipher.final(),
  ]);

  return decrypted;
}

// Stream Şifreleme (Büyük Dosyalar İçin)
async function encryptStream(
  inputStream: ReadableStream,
  key: Buffer,
  chunkSize: number = 64 * 1024
): Promise<ReadableStream> {
  const iv = crypto.randomBytes(IV_LENGTH);

  return new ReadableStream({
    async start(controller) {
      // IV'yi ilk chunk olarak gönder
      controller.enqueue(iv);

      const cipher = crypto.createCipheriv(ALGORITHM, key, iv, {
        authTagLength: TAG_LENGTH,
      });

      const reader = inputStream.getReader();

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        const encrypted = cipher.update(value);
        controller.enqueue(encrypted);
      }

      controller.enqueue(cipher.final());
      controller.enqueue(cipher.getAuthTag());
      controller.close();
    },
  });
}

// Encrypt-then-MAC Wrapper
function encryptThenMac(
  data: Buffer,
  key: Buffer,
  macKey: Buffer
): {
  ciphertext: Buffer;
  iv: Buffer;
  tag: Buffer;
  mac: Buffer;
} {
  const { ciphertext, iv, tag } = encryptSync(data, key);

  // MAC hesapla (additional integrity)
  const mac = crypto.createHmac('sha256', macKey)
    .update(iv)
    .update(ciphertext)
    .update(tag)
    .digest();

  return { ciphertext, iv, tag, mac };
}

// Anahtar Rotasyonu
class KeyRotation {
  private currentKeyId: string;
  private keys: Map<string, { key: Buffer; createdAt: Date }>;

  constructor() {
    this.keys = new Map();
    this.currentKeyId = '';
  }

  async rotateKey(newKey: Buffer): Promise<string> {
    const keyId = `key_${Date.now()}`;

    // Yeni anahtarı kaydet
    this.keys.set(keyId, {
      key: newKey,
      createdAt: new Date(),
    });

    // Eski anahtarı deprecated olarak işaretle
    if (this.currentKeyId) {
      await this.deprecateKey(this.currentKeyId);
    }

    this.currentKeyId = keyId;

    // Vault'a kaydet
    await this.saveToVault(keyId, newKey);

    return keyId;
  }

  async getKey(keyId: string): Promise<Buffer | null> {
    const keyData = this.keys.get(keyId);
    if (!keyData) {
      // Vault'tan yükle
      const vaultKey = await this.loadFromVault(keyId);
      if (vaultKey) {
        this.keys.set(keyId, {
          key: vaultKey,
          createdAt: new Date(),
        });
        return vaultKey;
      }
      return null;
    }
    return keyData.key;
  }

  async getCurrentKey(): Promise<Buffer> {
    const key = await this.getKey(this.currentKeyId);
    if (!key) throw new Error('No current encryption key');
    return key;
  }

  async reEncryptData(
    data: Buffer,
    oldKeyId: string,
    newKeyId: string
  ): Promise<Buffer> {
    const oldKey = await this.getKey(oldKeyId);
    const newKey = await this.getKey(newKeyId);

    if (!oldKey || !newKey) {
      throw new Error('Keys not found for re-encryption');
    }

    // Eski anahtar ile şifre çöz
    const decrypted = await decryptFromStorage(data, oldKey);

    // Yeni anahtar ile yeniden şifrele
    const reEncrypted = await encryptForStorage(decrypted, newKey);

    return reEncrypted;
  }

  private async deprecateKey(keyId: string): Promise<void> {
    // Vault'ta deprecated olarak işaretle
    await vault.write(`secret/data/deprecated-keys/${keyId}`, {
      deprecatedAt: new Date().toISOString(),
    });
  }

  private async saveToVault(keyId: string, key: Buffer): Promise<void> {
    await vault.write(`secret/data/encryption-keys/${keyId}`, {
      key: key.toString('base64'),
      algorithm: ALGORITHM,
      createdAt: new Date().toISOString(),
    });
  }

  private async loadFromVault(keyId: string): Promise<Buffer | null> {
    const data = await vault.read(`secret/data/encryption-keys/${keyId}`);
    if (!data) return null;
    return Buffer.from(data.key, 'base64');
  }
}

// Storage Entry/Exit Noktaları
async function encryptForStorage(
  data: Buffer,
  key: Buffer
): Promise<Buffer> {
  const { ciphertext, iv, tag } = await encrypt(data, key);

  // Format: [IV(12)][TAG(16)][CIPHERTEXT]
  return Buffer.concat([iv, tag, ciphertext]);
}

async function decryptFromStorage(
  encryptedData: Buffer,
  key: Buffer
): Promise<Buffer> {
  const iv = encryptedData.subarray(0, IV_LENGTH);
  const tag = encryptedData.subarray(IV_LENGTH, IV_LENGTH + TAG_LENGTH);
  const ciphertext = encryptedData.subarray(IV_LENGTH + TAG_LENGTH);

  return decrypt(ciphertext, key, iv, tag);
}
```

## Güvenlik Kontrolleri

- [ ] AES-256-GCM zorunlu algoritma olmalı
- [ ] IV asla tekrar kullanılmamalı (random generation)
- [ ] Auth tag her zaman doğrulanmalı
- [ ] Key rotation 90 günde bir yapılmalı
- [ ] Eski anahtarlar güvenli olarak imha edilmeli
- [ ] Master key HSM'de saklanmalı
- [ ] Derived key'ler context ile üretilmeli
- [ ] Şifreleme logları tutulmamalı (sadece metadata)
- [ ] Memory'den silme (zeroization) uygulanmalı
- [ ] Parallel encryption için unique IV zorunlu olmalı

## Bağımlılıklar

- **vault-secrets.md**: Anahtar yönetimi
- **encryption-aes256.md**: Şifreleme altyapısı
- **audit-logging.md**: Anahtar rotasyonu logları
- **K3 Ses Motoru**: Ses dosyası şifreleme

## Durum: Implementasyon

- [x] Algoritma seçimi yapıldı (AES-256-GCM)
- [x] Anahtar yaşam döngüsü tasarlandı
- [ ] Core şifreleme/çözme fonksiyonları implemente edilecek
- [ ] Stream encryption implemente edilecek
- [ ] Key rotation mekanizması kurulacak
- [ ] Vault entegrasyonu yapılacak
- [ ] Re-encryption scripti yazılacak
- [ ] Performance testleri yapılacak
