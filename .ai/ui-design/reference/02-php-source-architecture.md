---
title: "PHP Source Architecture Reference"
type: reference
category: backend-architecture
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# PHP Source Architecture Reference

**Zorunlu Baglantilar:** [[ADR-051-platform-rewrite-from-scratch]] · [[ADR-052-hybrid-auth-architecture]] · [[ADR-053-enterprise-router-architecture]] · [[ADR-054-enterprise-composer-stack]]

---

## 1. Amaç

Frontend ile etkileşime giren PHP backend altyapısının referans dokümanıdır. Ajanlar bu dosyadan API yapılandırmasını, auth flow'u ve middleware pipeline'ı okur.

---

## 2. PHP 8.4 Standartları

| Özellik | Değer | Not |
|---------|-------|-----|
| Version | PHP 8.4+ | LTS |
| Strict Types | `declare(strict_types=1)` | Her dosyada zorunlu |
| Coding Standard | PSR-12 | Extended Coding Style |
| Error Reporting | `E_ALL` | Production'da bile |
| Session | `session_start()` | Cookie-based |

### 2.1 Zorunlu Kurallar

```php
<?php
declare(strict_types=1);

// ✅ Doğru
function getUser(int $id): array {
    return ['id' => $id];
}

// ❌ Yanlış — strict_types ihlali
function getUser($id) {
    return $id;
}
```

---

## 3. PDO (No ORM — ADR-002)

| Özellik | Değer |
|---------|-------|
| Driver | `PDO::MYSQL` |
| Charset | `utf8mb4` |
| Collation | `utf8mb4_unicode_ci` |
| Error Mode | `PDO::ERRMODE_EXCEPTION` |
| Fetch Mode | `PDO::FETCH_ASSOC` |
| Prepare | Always prepared statements |

### 3.1 PDO Kullanım Kalıbı

```php
<?php
declare(strict_types=1);

// ✅ Doğru — Prepared Statement + Explicit Columns
$stmt = $pdo->prepare(
    'SELECT id, username, email FROM users WHERE id = :id'
);
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ❌ Yanlış — SELECT *, no prepared
$result = $pdo->query("SELECT * FROM users WHERE id = $userId");
```

### 3.2 Yasak Örüntüler

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `SELECT *` | `SELECT id, name, email` |
| ORM (Eloquent, Doctrine) | Raw PDO |
| String concat in query | Prepared statements |
| `$pdo->query($sql)` | `$pdo->prepare($sql)` |

---

## 4. PageRouter (ADR-053)

| Özellik | Değer |
|---------|-------|
| Library | `nikic/fast-route` |
| Pattern | Attribute-based routing |
| Middleware | PSR-15 compatible |
| Dispatch | `FastRoute\simpleDispatcher` |

### 4.1 Route Tanımlama

```php
<?php
declare(strict_types=1);

use CoreMusic\Routing\Attribute\Route;

class AuthController
{
    #[Route('/login', methods: ['GET', 'POST'])]
    public function login(): void { /* ... */ }

    #[Route('/logout', methods: ['POST'])]
    public function logout(): void { /* ... */ }

    #[Route('/register', methods: ['GET', 'POST'])]
    public function register(): void { /* ... */ }
}
```

### 4.2 Route Dosyaları

| Dosya | Kapsam |
|-------|--------|
| `routes/web.php` | Web sayfaları |
| `routes/api.php` | API endpoint'leri |
| `routes/auth.php` | Auth routes |

---

## 5. Middleware Pipeline (Immutable — ADR-010/011/012/013/022)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

**⚠️ SIRA DEĞİŞTİRİLEMEZ.** CSP nonce üretimi SessionManager içindedir.

### 5.1 Middleware Detayları

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **SessionManager** | Session başlatır, CSP nonce üretir | 3600s idle |
| 2 | **BypassAuth** | Test bypass (`?_bypass=1`), prod'da devre dışı | — |
| 3 | **RateLimiter** | APCu tabanlı, 60 req/60s | 60s |
| 4 | **Auth** | Auth bilgisi inject, RBAC kontrolü | — |
| 5 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | — |

### 5.2 Middleware Kod Kalıbı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class CsrfMiddleware
{
    public function process(
        \Psr\Http\Message\ServerRequestInterface $request,
        Handler $handler
    ): Response {
        $method = $request->getMethod();

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->getParsedBody()['csrf_token']
                ?? $request->getHeaderLine('X-CSRF-Token');

            if (hash_equals($_SESSION['csrf_token'] ?? '', $token) === false) {
                // 403 Forbidden
            }
        }

        return $handler->handle($request);
    }
}
```

---

## 6. CSRF Token (ADR-010)

| Özellik | Değer |
|---------|-------|
| Token Key | `csrf_token` |
| Yasak Key | `_csrf_token` (2026-05-30'da kaldırıldı) |
| Generation | `bin2hex(random_bytes(32))` |
| Validation | `hash_equals()` (timing-safe) |
| Storage | `$_SESSION['csrf_token']` |
| Header | `X-CSRF-Token` |

### 6.1 CSRF Kullanım Kalıbı

```php
<?php
// Token üretimi (login sonrası)
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Form'da kullanımı
echo '<input type="hidden" name="csrf_token" value="'
    . htmlspecialchars($_SESSION['csrf_token']) . '">';

// AJAX'ta kullanımı
fetch('/api/data', {
    method: 'POST',
    headers: {
        'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
    }
});
```

---

## 7. Session Yönetimi (ADR-011)

| Özellik | Değer |
|---------|-------|
| Session Name | `COREMUSIC_SESS` |
| Idle Timeout | 3600 saniye (1 saat) |
| Cookie Path | `/` |
| Cookie Domain | `.coremusic.net` (subdomain'ler arası) |
| Cookie Secure | `true` (HTTPS) |
| Cookie HttpOnly | `true` |
| Cookie SameSite | `Lax` |

### 7.1 Session Başlatma

```php
<?php
declare(strict_types=1);

session_name('COREMUSIC_SESS');
session_set_cookie_params([
    'lifetime' => 3600,
    'path'     => '/',
    'domain'   => '.coremusic.net',
    'secure'   => true,
    'httponly'  => true,
    'samesite' => 'Lax',
]);
session_start();
```

---

## 8. Auth Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/login` | GET/POST | Giriş yap |
| `/logout` | POST | Çıkış yap |
| `/register` | GET/POST | Kayıt ol |
| `/auth/check` | GET | Session kontrolü |
| `/auth/refresh` | POST | Token yenile |
| `/auth/permissions` | GET | Yetki listesi |

### 8.1 Auth Flow

```
1. GET /login → Login formu göster
2. POST /login → Kimlik doğrula
   ├── Başarılı → Session başlat, redirect /
   └── Başarısız → Hata mesajı göster
3. POST /logout → Session sonlandır, redirect /login
```

---

## 9. API Yapılandırması

| Özellik | Değer |
|---------|-------|
| Base URL | `/api/v1/` |
| Format | JSON (`application/json`) |
| Auth | Session cookie + CSRF token |
| Rate Limit | 60 req/60s (APCu) |
| Versioning | URL-based (`/api/v1/`, `/api/v2/`) |

### 9.1 API Response Formatı

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "username": "user"
    },
    "meta": {
        "timestamp": "2026-08-11T12:00:00Z",
        "version": "1.0"
    }
}
```

### 9.2 API Error Formatı

```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Email formatı geçersiz",
        "details": {
            "field": "email",
            "rule": "email"
        }
    }
}
```

---

## 10. Hata Yönetimi

| HTTP Kod | Anlam | Kullanım |
|----------|-------|----------|
| 200 | Başarılı | Normal yanıt |
| 201 | Oluşturuldu | Kayıt başarılı |
| 400 | Bad Request | Geçersiz istek |
| 401 | Unauthorized | Kimlik doğrulama gerekli |
| 403 | Forbidden | Yetki yetersiz |
| 404 | Not Found | Bulunamadı |
| 419 | CSRF Token Exired | Token süresi doldu |
| 422 | Unprocessable | Doğrulama hatası |
| 429 | Too Many Requests | Rate limit |
| 500 | Server Error | Sunucu hatası |

---

## 11. Dosya Yapısı

```
C:\www\coremusic.net\
├── public/
│   ├── index.php              ← Entry point
│   ├── .htaccess               ← URL rewrite
│   └── assets/                 ← Static files
├── src/
│   ├── Controller/             ← Controllers
│   ├── Middleware/              ← Middleware classes
│   ├── Service/                ← Business logic
│   ├── Repository/             ← Data access
│   ├── Entity/                 ← Data models
│   └── Config/                 ← Configuration
├── routes/
│   ├── web.php                 ← Web routes
│   ├── api.php                 ← API routes
│   └── auth.php                ← Auth routes
├── templates/                  ← PHP templates
├── vendor/                     ← Composer dependencies
├── .env                        ← Environment variables
└── composer.json               ← Dependencies
```

---

## 12. Quick Reference

| İhtiyaç | Kaynak |
|---------|--------|
| CSRF token key | `csrf_token` (NOT `_csrf_token`) |
| Session name | `COREMUSIC_SESS` |
| Middleware sırası | SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf |
| PDO | Prepared statements, no ORM, no SELECT * |
| Router | nikic/fast-route, attribute-based |
| PHP version | 8.4+ (strict_types=1) |

---

## 13. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[ADR-051-platform-rewrite-from-scratch]] |
| Bu dosya | [[ADR-052-hybrid-auth-architecture]] |
| Bu dosya | [[ADR-053-enterprise-router-architecture]] |
| Bu dosya | [[ADR-054-enterprise-composer-stack]] |
| Bu dosya | [[ADR-010-csrf-protection-strategy]] |
| Bu dosya | [[ADR-011-session-management]] |
| Bu dosya | [[ADR-002-pdo-mandatory-no-orm]] |

---

## 14. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Sections | 14 |
| ADR Coverage | 051, 052, 053, 054, 010, 011, 002 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
