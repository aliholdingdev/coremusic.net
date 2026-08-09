---
type: adr
category: security
title: "ADR-052: Hybrid Auth Architecture (Session + JWT)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-052: Hybrid Auth Architecture (Session + JWT)

**Status:** Active
**Kategorisi:** Security
**İlgili Agent:** [[.agents/security-engineer]]
**İlgili Division:** Security Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunda **Hybrid Authentication Architecture** (Session + JWT) kullanımını, merkezi auth sunucusunu (auth.coremusic.net), cross-domain session mekanizmasını ve PSR uyumlu Composer paketleriyle entegrasyonu tanımlar.

CoreMusic ne sadece Session ne de sadece JWT kullanacaktır. **Her ikisinin kombinasyonu** kullanılacaktır.

---

## 2. Bağlam

### 2.1 Problem

| # | Problem | Açıklama |
|---|---------|----------|
| P1 | Sadece Session | Cross-domain'de sorunlu, stateful |
| P2 | Sadece JWT | Refresh token yönetimi karmaşık |
| P3 | Dağınık auth | Her panel kendi auth'u |
| P4 | Güvenlik | Tek başına yetersiz koruma |

### 2.2 Gereksinimler

| # | Gereksinim | Değer |
|---|------------|-------|
| R1 | Hybrid | Session + JWT kombinasyonu |
| R2 | Merkezi | auth.coremusic.net tek endpoint |
| R3 | Cross-domain | 10 panel arası session |
| R4 | PSR uyumlu | PSR-7, PSR-15, PSR-17 |
| R5 | Composer | Güvenilir paketler |
| R6 | RBAC | Rol bazlı erişim |
| R7 | Güvenlik | OWASP Top 10:2025 |
| R8 | RPI5 uyumlu | Hafif frontend |

---

## 3. Karar

CoreMusic'te **Hybrid Authentication Architecture** kullanılacaktır.

### 3.1 Hybrid Auth Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                        BROWSER                                  │
│                                                                 │
│  HttpOnly Secure Cookie (COREMUSIC_SESS)                       │
│  └── Session ID (server-side state)                            │
│                                                                 │
│  Access JWT Token (header-based API calls)                     │
│  └── Short-lived (15 min)                                      │
│                                                                 │
│  Refresh JWT Token (token yenileme)                            │
│  └── Long-lived (7 days)                                       │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                     auth.coremusic.net                          │
│                                                                 │
│  POST /login → Validate → Create Session + Issue JWT Pair      │
│  POST /refresh → Validate Refresh Token → Issue New Pair       │
│  GET /check → Validate Session/JWT → Return User Info          │
│  POST /logout → Destroy Session + Revoke Tokens                │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│                   PROTECTED SERVICES                            │
│                                                                 │
│  music.coremusic.net ─┐                                        │
│  admin.coremusic.net ─┤── Cookie + JWT ──→ auth.coremusic.net │
│  home.coremusic.net ──┤                                        │
│  studio.coremusic.net ┤                                        │
│  pro.coremusic.net ───┤                                        │
│  car.coremusic.net ───┘                                        │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Session Politikası

| Parametre | Değer | ADR |
|-----------|-------|-----|
| Cookie Name | `COREMUSIC_SESS` | ADR-011 |
| Cookie Domain | `.coremusic.net` | ADR-043 |
| HttpOnly | `true` | ADR-011 |
| Secure | `true` (HTTPS) | ADR-011 |
| SameSite | `Lax` | ADR-011 |
| Idle Timeout | 3600s | ADR-011 |
| Absolute Timeout | 86400s (24h) | ADR-011 |
| Rotation | Login sonrası zorunlu | ADR-011 |
| Regeneration | `session_regenerate_id(true)` | ADR-011 |

### 3.3 JWT Politikası

| Parametre | Değer |
|-----------|-------|
| Algorithm | RS256 (asymmetric) |
| Access Token TTL | 900s (15 min) |
| Refresh Token TTL | 604800s (7 days) |
| Issuer | `auth.coremusic.net` |
| Audience | `*.coremusic.net` |
| Key Rotation | 90 günde bir |
| Blacklist | Redis/APCu |
| Device Binding | User-Agent hash |

### 3.4 JWT Token Yapısı

**Access Token:**
```json
{
  "sub": "12345",
  "email": "user@coremusic.net",
  "roles": ["user", "premium"],
  "iss": "auth.coremusic.net",
  "aud": "music.coremusic.net",
  "iat": 1691234567,
  "exp": 1691235467,
  "jti": "unique-token-id",
  "device": "sha256-of-user-agent"
}
```

**Refresh Token:**
```json
{
  "sub": "12345",
  "iss": "auth.coremusic.net",
  "iat": 1691234567,
  "exp": 1691839367,
  "jti": "unique-refresh-id",
  "family": "token-family-id",
  "device": "sha256-of-user-agent"
}
```

### 3.5 RBAC Roller

| # | Rol | Yetki | Panel Erişimi |
|---|-----|-------|---------------|
| 1 | `guest` | Temel | Landing, Music (sınırlı) |
| 2 | `user` | Normal | Music, Download, Home, Car, Studio, Pro |
| 3 | `premium` | Üyelik | Tüm user + ek özellikler |
| 4 | `admin` | Yönetici | Admin + tüm paneller |
| 5 | `super_admin` | Süper | Tüm sistem |

### 3.6 PSR Uyumlu Composer Paketleri

| Alan | Paket | PSR |
|------|-------|-----|
| HTTP Message | `nyholm/psr7` | PSR-7 |
| HTTP Server Handler | `psr/http-server-handler` | PSR-15 |
| HTTP Middleware | `psr/http-server-middleware` | PSR-15 |
| DI Container | `php-di/php-di` | PSR-11 |
| Router | `nikic/fast-route` | — |
| JWT | `firebase/php-jwt` | — |
| UUID | `ramsey/uuid` | — |
| Logger | `monolog/monolog` | PSR-3 |
| Cache | `symfony/cache` | PSR-6 |
| Event Dispatcher | `symfony/event-dispatcher` | PSR-14 |
| CSRF | `symfony/security-csrf` | — |
| Validation | `respect/validation` | — |
| Rate Limiter | `symfony/rate-limiter` | — |
| Env | `vlucas/phpdotenv` | — |
| HTML Purifier | `ezyang/htmlpurifier` | — |
| Password | PHP native `password_hash()` | — |
| Encryption | `paragonie/halite` | — |

### 3.7 Auth Endpoint'leri

| # | Endpoint | Method | Amaç |
|---|----------|--------|------|
| 1 | `/login` | GET | Login formu |
| 2 | `/login` | POST | Login doğrulama |
| 3 | `/register` | GET | Kayıt formu |
| 4 | `/register` | POST | Kayıt işlemi |
| 5 | `/logout` | POST | Oturum kapatma |
| 6 | `/password/forgot` | GET | Şifre sıfırlama isteği |
| 7 | `/password/reset` | GET | Şifre sıfırlama formu |
| 8 | `/password/reset` | POST | Şifre sıfırlama işlemi |
| 9 | `/api/session/check` | GET | Session/JWT doğrulama |
| 10 | `/api/token/refresh` | POST | Token yenileme |
| 11 | `/api/token/revoke` | POST | Token iptal |

### 3.8 Cross-Domain Auth Flow

```
1. Kullanıcı → music.coremusic.net/playlist/123
2. Auth middleware: Cookie var mı?
   ├── Evet → /api/session/check (auth.coremusic.net)
   │         ├── 200 OK → User bilgisi inject → Devam
   │         └── 401 → Redirect login
   └── Hayır → Redirect auth.coremusic.net/login?return=/playlist/123

3. auth.coremusic.net/login
   ├── Login formu göster
   ├── Kullanıcı email + password girer
   ├── POST /login
   │   ├── Rate limit kontrolü (5/15dk)
   │   ├── Argon2id hash karşılaştır
   │   ├── Başarılı →
   │   │   ├── Yeni session ID üret (fixation koruması)
   │   │   ├── Session DB'ye kaydet
   │   │   ├── JWT pair oluştur (access + refresh)
   │   │   ├── Cookie set (.coremusic.net, HttpOnly, Secure)
   │   │   └── Return URL'ye redirect
   │   └── Başarısız → Hata + kalan deneme

4. music.coremusic.net
   ├── Auth middleware: /api/session/check → 200 OK
   ├── User bilgisi request'e inject
   └── Controller devam eder
```

---

## 4. Teknik Detaylar

### 4.1 Password Hashing (PHP Native)

```php
<?php
declare(strict_types=1);

// Hashing
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64MB
    'time_cost' => 4,        // 4 iterations
    'threads' => 2,          // 2 threads
]);

// Verification
if (password_verify($password, $storedHash)) {
    // Başarılı
    // Rehash gerekli mi kontrol
    if (password_needs_rehash($storedHash, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 2,
    ])) {
        // Yeni hash ile güncelle
    }
}
```

### 4.2 CSRF Token (Symfony Component)

```php
<?php
declare(strict_types=1);

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

$tokenStorage = new SessionTokenStorage();
$csrfManager = new CsrfTokenManager(null, $tokenStorage);

// Token üret
$token = $csrfManager->getToken('auth-form');

// Token doğrula
$isValid = $csrfManager->isTokenValid('auth-form', $submittedToken);
```

### 4.3 Rate Limiter (Symfony Component)

```php
<?php
declare(strict_types=1);

use Symfony\Component\RateLimiter\RateLimiterFactory;

$loginLimiter = $rateLimiterFactory->create('login', 5, '15-minute');

// Kontrol et
$limit = $loginLimiter->consume(1);
if (!$limit->isAccepted()) {
    // 429 Too Many Requests
    $retryAfter = $limit->getRetryAfter()->getTimestamp() - time();
    header('Retry-After: ' . $retryAfter);
}
```

### 4.4 Logger (PSR-3 / Monolog)

```php
<?php
declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('auth');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/auth.log', Logger::WARNING));

// Kullanım
$logger->warning('Failed login attempt', [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR'],
    'attempts' => $attemptCount,
]);
```

---

## 5. Yasak Örüntüleri

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Sadece Session | Hybrid (Session + JWT) | Bu ADR |
| 2 | Sadece JWT | Hybrid (Session + JWT) | Bu ADR |
| 3 | Kendi JWT implementasyonu | `firebase/php-jwt` | Bu ADR |
| 4 | Kendi şifreleme algoritması | PHP native + paragonie/halite | ADR-022 |
| 5 | `localStorage` auth | HttpOnly cookie | ADR-011 |
| 6 | Plaintext password | Argon2id hash | ADR-022 |
| 7 | Timing attack | `hash_equals()` | ADR-010 |
| 8 | Open redirect | Return URL validation | ADR-047 |

---

## 6. Edge Cases

| # | Edge Case | Çözüm | ADR |
|---|-----------|-------|-----|
| 1 | Cookie reddi | Fallback: header-based JWT | Bu ADR |
| 2 | JWT çalınması | Short-lived + refresh rotation | Bu ADR |
| 3 | Token blacklist | Redis/APCu blacklisting | Bu ADR |
| 4 | Device değişimi | Device binding (User-Agent hash) | Bu ADR |
| 5 | Concurrent login | Eski session'ları öldürme | ADR-011 |
| 6 | Auth servisi down | Cached auth fallback (5dk) | ADR-043 |
| 7 | Key rotation | RS256 key pair rotation (90 gün) | Bu ADR |
| 8 | Token family attack | Refresh token family tracking | Bu ADR |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Hybrid zorunlu | Session + JWT birlikte | Güvenlik açığı |
| G2 | Merkezi auth | auth.coremusic.net | Dağınık auth |
| G3 | Argon2id zorunlu | Şifre hashleme | Veri sızıntısı |
| G4 | HttpOnly cookie | JS erişimi yasak | Token sızıntısı |
| G5 | Short-lived JWT | 15 dk access token | Token istismarı |
| G6 | Refresh rotation | Her refresh'te yeni token | Token reuse |
| G7 | Key rotation | 90 günde bir | Kripto zafiyet |
| G8 | CSRF zorunlu | Login formu | CSRF saldırısı |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | csrf_token |
| [[ADR-011-session-management]] | Session yönetimi | Cookie, timeout |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Argon2id, AES-256-GCM |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Secret yönetimi |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu | Merkezi auth |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect | Return URL |
| [[ADR-051-platform-rewrite-from-scratch]] | Platform rewrite | Sıfırdan yazım |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 9 |
| Auth Endpoint | 11 |
| RBAC Roller | 5 |
| JWT Claims | 8 |
| Composer Paket | 17 |
| Yasak Örüntü | 8 |
| Edge Cases | 8 |
| Hard Guardrails | 8 |
| İlgili ADR | 7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
