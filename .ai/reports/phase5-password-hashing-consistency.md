---
title: "CoreMusic — Phase 5: Password Hashing Consistency Audit"
type: security-audit
category: password-hashing
date: 2026-09-03
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Phase 5: Password Hashing Consistency Audit

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]]

---

## 1. Amaç

Tüm auth sistemleri arasında şifre hashleme tutarlılığını doğrulamak. Argon2id kullanımı, pepper uygulaması ve alternatif hashleme yöntemlerinin olmaması denetlenir.

---

## 2. Kapsam Alanı

| Dosya | Kategori |
|-------|----------|
| `auth.coremusic.net/include/Domain/ValueObject/Password.php` | Password Value Object |
| `auth.coremusic.net/include/Service/AuthService.php` | Auth Service |
| `auth.coremusic.net/include/Repository/UserRepository.php` | User Repository |
| `shared/src/OAuth/OAuthManager.php` | OAuth Manager |
| `shared/src/OAuth/Provider/BaseOAuthProvider.php` | OAuth Provider |
| `shared/src/Cache/PageCacheAdapter.php` | Cache Adapter |
| `shared/src/Middleware/RateLimiterMiddleware.php` | Rate Limiter |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | API Rate Limiter |
| `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | Response Middleware |
| `shared/src/AI/AIWorkflow.php` | AI Workflow |

---

## 3. Bulgu Özeti

| # | Bulgulama | Durum | Öncelik |
|---|-----------|-------|---------|
| 1 | Argon2id tek şifre hashleme yöntemi | ✅ Tutarlı | PASS |
| 2 | Pepper (hash_hmac sha256) uygulaması doğru | ✅ Doğru | PASS |
| 3 | PASSWORD_BCRYPT / PASSWORD_DEFAULT KULLANIMI YOK | ✅ Temiz | PASS |
| 4 | SHA256 sadece token/cache için kullanılıyor | ✅ Doğru | PASS |
| 5 | MD5 sadece cache key/ETag için kullanılıyor | ✅ Doğru | PASS |
| 6 | Tek giriş noktası: Password sınıfı | ✅ Doğru | PASS |

**Genel Sonuç:** ✅ TUTARLI — Güvenlik açığı tespit edilmedi.

---

## 4. Detaylı Analiz

### 4.1 Password Value Object (Tek Doğruluk Noktası)

**Dosya:** `auth.coremusic.net/include/Domain/ValueObject/Password.php`

```php
// Hash oluşturma (tek nokta)
public function hashWithPepper(string $pepper): string
{
    $peppered = hash_hmac('sha256', $this->raw, $pepper);  // Pepper
    return password_hash($peppered, PASSWORD_ARGON2ID, [    // Argon2id
        'memory_cost' => 65536,   // 64 MB
        'time_cost'   => 4,       // 4 iterasyon
        'threads'     => 2,       // 2 thread
    ]);
}

// Doğrulama (tek nokta)
public function verify(string $hash, string $pepper): bool
{
    $peppered = hash_hmac('sha256', $this->raw, $pepper);
    return password_verify($peppered, $hash);
}
```

**Değerlendirme:**
- ✅ `PASSWORD_ARGON2ID` kullanılıyor (OWASP önerisi)
- ✅ Pepper uygulanıyor (defense-in-depth)
- ✅ Parametreler uygun: 64MB memory, 4 iterasyon, 2 thread
- ✅ Ham şifre asla entity'de saklanmıyor
- ✅ Minimum şifre uzunluğu kontrolü (8 karakter)

### 4.2 AuthService Kullanımı

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php`

| İşlem | Satır | Yöntem | Doğru mu? |
|-------|-------|--------|-----------|
| Kayıt (register) | L151-152 | `Password::create()` → `hashWithPepper()` | ✅ |
| Giriş (login) | L79-80 | `Password::create()` → `verify()` | ✅ |
| Şifre sıfırlama | L268-269 | `Password::create()` → `hashWithPepper()` | ✅ |

**Değerlendirme:** AuthService doğrudan `password_hash()` çağırmaz. Her zaman `Password` Value Object'ini kullanır.

### 4.3 UserRepository Kullanımı

**Dosya:** `auth.coremusic.net/include/Repository/UserRepository.php`

| İşlem | Satır | Yöntem | Doğru mu? |
|-------|-------|--------|-----------|
| Kullanıcı oluşturma | L122 | `$userData['password_hash']` (AuthService'den gelir) | ✅ |
| Şifre güncelleme | L211 | `['password_hash' => $newPasswordHash]` (AuthService'den gelir) | ✅ |

**Değerlendirme:** Repository sadece hash'i depolar. Hash oluşturma/doğrulama AuthService üzerindendir.

### 4.4 SHA256 Kullanımı (Şifre Değil)

| Dosya | Amaç | Doğru mu? |
|-------|------|-----------|
| AuthService.php L241 | Token hashleme (`hash('sha256', $rawToken)`) | ✅ |
| AuthService.php L262 | Token hashleme (`hash('sha256', $token)`) | ✅ |
| UserRepository.php L221 | Auth key hashleme (`hash('sha256', $authKey)`) | ✅ |
| UserRepository.php L238 | Auth key hashleme (`hash('sha256', $authKey)`) | ✅ |
| OAuthManager.php L202 | OAuth state hashleme (`hash('sha256', $state)`) | ✅ |
| OAuthManager.php L214 | OAuth state hashleme (`hash('sha256', $hash)`) | ✅ |
| BaseOAuthProvider.php L60 | PKCE code challenge (`hash('sha256', $codeVerifier)`) | ✅ |
| RateLimitMiddleware.php L62 | API key hashleme (`hash('sha256', $apiKey)`) | ✅ |

**Değerlendirme:** Tüm SHA256 kullanımları şifre hashleme AMACI TAŞIMIYOR. Token, OAuth state, PKCE ve API key hashleme için kullanılıyor. Bu doğru bir uygulama.

### 4.5 MD5 Kullanımı (Şifre Değil)

| Dosya | Amaç | Doğru mu? |
|-------|------|-----------|
| PageCacheAdapter.php L31 | Cache key (`md5($uri)`) | ✅ |
| RateLimiterMiddleware.php L35 | Cache key (`md5($ip)`) | ✅ |
| ResponseNormalizationMiddleware.php L55 | ETag (`md5(json_encode($response))`) | ✅ |
| AIWorkflow.php L168 | Cache key (`md5(serialize($roomProfile))`) | ✅ |

**Değerlendirme:** Tüm MD5 kullanımları cache key/ETag amaçlıdır. Şifre hashleme için kullanılmıyor.

---

## 5. OWASP Kontrol Listesi

| # | OWASP Kuralı | Durum | Açıklama |
|---|-------------|-------|----------|
| 1 | Argon2id veya bcrypt kullanılmalı | ✅ | Argon2id kullanılıyor |
| 2 | Password_hash() fonksiyonu kullanılmalı | ✅ | password_hash() + pepper |
| 3 | Pepper uygulanmalı | ✅ | hash_hmac('sha256', $raw, $pepper) |
| 4 | Minimum uzunluk kontrolü | ✅ | 8 karakter minimum |
| 5 | Doğrulama parametreleri güçlü olmalı | ✅ | 64MB memory, 4 iterasyon |
| 6 | MD5/SHA1 şifre için KULLANILMAMALI | ✅ | Hiçbir şifre hashlemede kullanılmıyor |
| 7 | Tek giriş noktası olmalı | ✅ | Password Value Object |

---

## 6. Sonuç

| Metrik | Değer |
|--------|-------|
| Toplam incelenen dosya | 10 |
| Password hashleme yöntemi | Argon2id (tek) |
| Pepper uygulaması | ✅ Aktif |
| MD5/SHA1 şifre kullanımı | ❌ Yok |
| OWASP uyumluluğu | ✅ %100 |
| Güvenlik açığı | ❌ Tespit edilmedi |
| Tutarlılık durumu | ✅ Tutarlı |

---

## 7. Öneri

Mevcut uygulama OWASP 2025 ve ADR-022 standartlarına tam uyumludur. Değişiklik gerekmez.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-03
**Mode:** Red Team · Human Mode · Truth Mode
