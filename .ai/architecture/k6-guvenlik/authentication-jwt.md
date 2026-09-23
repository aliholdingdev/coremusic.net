---
title: "JWT Kimlik Doğrulama Sistemi"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# JWT Kimlik Doğrulama Sistemi

## Genel Bakış

JWT (JSON Web Token) tabanlı kimlik doğrulama sistemi, COREMUSIC'in tüm API isteklerinde güvenli oturum yönetimini sağlar. Access Token ve Refresh Token ayrımı ile kısa süreli erişim ve uzun süreli oturum kontrolü uygulanır. Token rotation stratejisi ile çalınan token'ların kullanım süresi minimize edilir.

## Teknik Detaylar

### Token Yapısı

```
Header: {
  "alg": "RS256",
  "typ": "JWT",
  "kid": "coremusic-key-2026-01"
}

Payload: {
  "sub": "user_12345",
  "iss": "coremusic-api",
  "aud": "coremusic-client",
  "exp": 1758412800,
  "iat": 1758409200,
  "jti": "unique-token-id-uuid",
  "roles": ["user", "editor"],
  "permissions": ["audio:read", "audio:write", "playlist:manage"]
}

Signature: RS256(base64(header) + "." + base64(payload), privateKey)
```

### Token Yaşam Döngüsü

1. Kullanıcı kimlik bilgilerini gönderir
2. Sunucu doğrulama yapar
3. Access Token (15 dk) ve Refresh Token (7 gün) oluşturulur
4. Refresh Token veritabanına kaydedilir
5. Access Token süresi dolunca Refresh Token ile yenilenir
6. Refresh Token her kullanımda rotation uygulanır
7. Refresh Token compromised ise tüm oturumlar sonlandırılır

### Token Rotation Stratejisi

Her refresh isteğinde yeni refresh token üretilir ve eski token geçersiz kılınır. Hırsızlık tespit edildiğinde tüm token zinciri iptal edilir.

### RS256 Asimetrik Şifreleme

JWT imzası RSA-2048 kullanarak imzalanır. Public key paysahiplere dağıtılır, private key sadece sunucuda saklanır. Asimetrik yapı sayesinde token doğrulama private key'e ihtiyaç duymaz.

## Konfigürasyon / Kod

```typescript
// JWT Token Oluşturma
import jwt from 'jsonwebtoken';
import { v4 as uuidv4 } from 'uuid';

interface TokenPayload {
  sub: string;
  iss: string;
  aud: string;
  exp: number;
  iat: number;
  jti: string;
  roles: string[];
  permissions: string[];
}

async function generateTokenPair(
  userId: string,
  roles: string[],
  permissions: string[]
): Promise<{ accessToken: string; refreshToken: string }> {
  const jti = uuidv4();
  const now = Math.floor(Date.now() / 1000);

  const accessTokenPayload: TokenPayload = {
    sub: userId,
    iss: 'coremusic-api',
    aud: 'coremusic-client',
    exp: now + 900, // 15 dakika
    iat: now,
    jti,
    roles,
    permissions,
  };

  const accessToken = jwt.sign(
    accessTokenPayload,
    { key: PRIVATE_KEY, passphrase: process.env.JWT_PASSPHRASE },
    { algorithm: 'RS256' }
  );

  const refreshToken = await generateRefreshToken(userId, jti);

  return { accessToken, refreshToken };
}

// Refresh Token Oluşturma
async function generateRefreshToken(
  userId: string,
  parentJti: string
): Promise<string> {
  const token = uuidv4();
  const expiresAt = new Date();
  expiresAt.setDate(expiresAt.getDate() + 7);

  await db.refreshTokens.create({
    data: {
      token,
      userId,
      parentJti,
      expiresAt,
      isRevoked: false,
      usedCount: 0,
    },
  });

  return token;
}

// Token Yenileme (Rotation ile)
async function refreshAccessToken(
  refreshToken: string
): Promise<{ accessToken: string; refreshToken: string }> {
  const storedToken = await db.refreshTokens.findUnique({
    where: { token: refreshToken },
  });

  if (!storedToken || storedToken.isRevoked) {
    // Token çalınmış olabilir - tüm token'ları iptal et
    await revokeAllUserTokens(storedToken?.userId);
    throw new UnauthorizedError('Refresh token compromised');
  }

  if (new Date() > storedToken.expiresAt) {
    throw new UnauthorizedError('Refresh token expired');
  }

  // Eski token'ı iptal et
  await db.refreshTokens.update({
    where: { token: refreshToken },
    data: { isRevoked: true },
  });

  // Yeni token çifti oluştur
  const user = await getUserById(storedToken.userId);
  return generateTokenPair(user.id, user.roles, user.permissions);
}

// Token Doğrulama
async function verifyAccessToken(token: string): Promise<TokenPayload> {
  const payload = jwt.verify(token, PUBLIC_KEY, {
    algorithms: ['RS256'],
    issuer: 'coremusic-api',
    audience: 'coremusic-client',
  }) as TokenPayload;

  // Token'ın blacklist'te olup olmadığını kontrol et
  const isBlacklisted = await redis.get(`bl:${payload.jti}`);
  if (isBlacklisted) {
    throw new UnauthorizedError('Token is blacklisted');
  }

  return payload;
}

// Token İptal Etme (Blacklisting)
async function revokeToken(jti: string, exp: number): Promise<void> {
  const ttl = exp - Math.floor(Date.now() / 1000);
  if (ttl > 0) {
    await redis.setex(`bl:${jti}`, ttl, '1');
  }
}

async function revokeAllUserTokens(userId: string): Promise<void> {
  await db.refreshTokens.updateMany({
    where: { userId, isRevoked: false },
    data: { isRevoked: true },
  });
}
```

## Güvenlik Kontrolleri

- [ ] RS256 algoritması zorunlu tutulmalı (HS256NotAllowed)
- [ ] Token süresi 15 dakikayı aşmamalı
- [ ] Refresh Token rotation aktif olmalı
- [ ] Token blacklist kontrolü her istekte yapılmalı
- [ ] Private key Environment Variable'dan okunmalı
- [ ] Token jti ileher token benzersiz olmalı
- [ ] Compromised tespitinde tüm token zinciri iptal edilmeli
- [ ] Token'lar loglara yazılmalı (payload hash olarak)
- [ ] Clock skew tolerance 30 sn ile sınırlı olmalı
- [ ] Audience ve issuer kontrolü zorunlu olmalı

## Bağımlılıklar

- **redis**: Token blacklist ve rate limiting için
- **PostgreSQL**: Refresh token depolama
- **K3 Ses Motoru**: Hassas ses verisi erişim yetkilendirmesi
- **K4 AI Katmanı**: ML model erişim izinleri

## Durum: Implementasyon

- [x] JWT yapısı tasarlandı
- [x] RS256 anahtar çifti üretimi planlandı
- [ ] Token oluşturma servisi implemente edilecek
- [ ] Refresh rotation mekanizması kurulacak
- [ ] Redis blacklist entegrasyonu yapılacak
- [ ] Token middleware yazılacak
- [ ] Unit test yazılacak
