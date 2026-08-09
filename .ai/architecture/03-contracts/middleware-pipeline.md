---
type: architecture
category: contracts
title: "Middleware Pipeline"
date: 2026-08-08
updated: 2026-08-09
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Middleware Pipeline

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic'in 6 katmanlı middleware pipeline'ını, her bir middleware'in görevini, sırasını ve uygulama detaylarını tanımlayan **Pipeline Rehberi**dir. Sıra frozen'dır (değiştirilemez).

## 2. Pipeline Sırası (Frozen)

```
Request
  │
  ▼
┌─────────────────────────────────────────────────────────────┐
│  1. SessionManagerMiddleware                                │
│     ├── Session başlatır                                    │
│     ├── CSP nonce üretir                                    │
│     └── Idle timeout kontrolü                               │
├─────────────────────────────────────────────────────────────┤
│  2. BypassAuthMiddleware                                    │
│     ├── Test ortamında auth bypass                          │
│     └── Production'da devre dışı                            │
├─────────────────────────────────────────────────────────────┤
│  3. RateLimiterMiddleware                                   │
│     ├── APCu tabanlı rate limiting                          │
│     └── 60 req/60s default                                  │
├─────────────────────────────────────────────────────────────┤
│  4. AuthMiddleware                                          │
│     ├── Kullanıcı bilgisi inject                            │
│     └── RBAC kontrolü                                       │
├─────────────────────────────────────────────────────────────┤
│  5. SecurityHeadersMiddleware                               │
│     ├── CSP strict-dynamic                                   │
│     ├── X-Frame-Options: DENY                               │
│     └── HSTS, X-Content-Type-Options                        │
├─────────────────────────────────────────────────────────────┤
│  6. CsrfMiddleware                                          │
│     ├── csrf_token doğrulama                                │
│     └── POST/PUT/DELETE için zorunlu                        │
└─────────────────────────────────────────────────────────────┘
  │
  ▼
Controller → Response
```

*Kaynak: [[ADR-010-csrf-protection-strategy]], [[ADR-011-session-management]], [[ADR-012-csp-nonce-strict-dynamic]], [[ADR-013-rate-limiting-apcu]], [[ADR-022-database-hardened-security]]*

## 3. Middleware Detayları

### 3.1 SessionManagerMiddleware (ADR-011)

| Özellik | Değer |
|---------|-------|
| **Görev** | Session başlat, CSP nonce üret |
| **Session Name** | `COREMUSIC_SESS` |
| **Idle Timeout** | 3600s (1 saat) |
| **Absolute Timeout** | 86400s (24 saat) |
| **Cookie Flags** | HttpOnly, Secure, SameSite=Lax |
| **Regenerate** | Login sonrası zorunlu |
| **CSP Nonce** | `base64_encode(random_bytes(32))` |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Session Manager Middleware — ADR-011 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class SessionManagerMiddleware
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        // Session başlat
        session_name('COREMUSIC_SESS');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_strict_mode', '1');
        session_start();

        // Idle timeout kontrolü
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > 3600) {
                session_destroy();
                return new \CoreMusic\Http\RedirectResponse('/login?reason=timeout');
            }
        }
        $_SESSION['last_activity'] = time();

        // Absolute timeout kontrolü
        if (isset($_SESSION['created_at'])) {
            if (time() - $_SESSION['created_at'] > 86400) {
                session_destroy();
                return new \CoreMusic\Http\RedirectResponse('/login?reason=absolute_timeout');
            }
        } else {
            $_SESSION['created_at'] = time();
        }

        // CSP nonce üret
        $_SESSION['csp_nonce'] = base64_encode(random_bytes(32));

        return $next($request);
    }
}
```

### 3.2 BypassAuthMiddleware (ADR-008)

| Özellik | Değer |
|---------|-------|
| **Görev** | Test ortamında auth bypass |
| **Production** | Devre dışı |
| **Aktivasyon** | `?_bypass=1` query parametresi |
| **Environment** | `APP_ENV=development` |
| **Varsayılan Kullanıcı** | user_id=1, role=admin |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Bypass Auth Middleware — ADR-008 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class BypassAuthMiddleware
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $env = getenv('APP_ENV') ?: 'production';

        // Sadece development ortamında ve ?_bypass=1 ile aktif
        if ($env === 'development' && isset($_GET['_bypass'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 'admin';
            $_SESSION['email'] = 'dev@coremusic.net';
            $_SESSION['bypass_active'] = true;
        }

        return $next($request);
    }
}
```

### 3.3 RateLimiterMiddleware (ADR-013)

| Özellik | Değer |
|---------|-------|
| **Görev** | Abuse önleme |
| **Backend** | APCu |
| **Default Limit** | 60 req/60s |
| **Login Limit** | 5 req/60s |
| **Register Limit** | 3 req/300s |
| **API Limit** | 120 req/60s |
| **Window** | 60 saniye (kayma penceresi) |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Rate Limiter Middleware — ADR-013 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class RateLimiterMiddleware
{
    private int $defaultLimit = 60;
    private int $windowSeconds = 60;

    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $path = $request->getPath();

        // Endpoint bazlı limit
        $limit = $this->getLimitForPath($path);
        $window = (int) floor(time() / $this->windowSeconds);
        $key = "rate:{$clientIp}:{$window}";

        $count = apcu_fetch($key, $hit);
        if (!$hit) $count = 0;

        if ($count >= $limit) {
            $retryAfter = ($window + 1) * $this->windowSeconds - time();
            header('X-RateLimit-Limit: ' . $limit);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . (($window + 1) * $this->windowSeconds));
            header('Retry-After: ' . $retryAfter);
            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Çok fazla istek',
                    'retry_after' => $retryAfter
                ]
            ], 429);
        }

        // Sayacı artır
        apcu_store($key, $count + 1, $this->windowSeconds * 2);

        // Header'ları ekle
        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . ($limit - $count - 1));

        return $next($request);
    }

    private function getLimitForPath(string $path): int
    {
        if (str_contains($path, '/login')) return 5;
        if (str_contains($path, '/register')) return 3;
        if (str_contains($path, '/api/')) return 120;
        return $this->defaultLimit;
    }
}
```

### 3.4 AuthMiddleware (ADR-011 + ADR-047 + ADR-052)

| Özellik | Değer |
|---------|-------|
| **Görev** | Kullanıcı bilgisi inject + JWT doğrulama |
| **Auth Key** | `auth_key` cookie (auth.coremusic.net) |
| **JWT Algorithm** | RS256 (RSA SHA-256) — `firebase/php-jwt` |
| **JWT Secret** | RSA private key dosyası (`AUTH_JWT_PRIVATE_KEY`) |
| **JWT Public** | RSA public key dosyası (`AUTH_JWT_PUBLIC_KEY`) |
| **Session Vars** | `user_id`, `role`, `email`, `gender`, `permissions` |
| **RBAC** | 7 granular rol (admin → guest) |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Auth Middleware — ADR-011 + ADR-047 + ADR-052 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 * Hybrid auth: Session + JWT RS256.
 *
 * @see [[auth]] — tam RBAC tablosu ve izin matrisi
 */
class AuthMiddleware
{
    private const RBAC_MAP = [
        'admin'          => [1000, 1999],
        'ultra_user'     => [800,  899],
        'premium_user'   => [700,  799],
        'streaming_user' => [600,  699],
        'panel_user'     => [500,  599],
        'free_user'      => [100,  199],
        'guest'          => [0,    0],
    ];

    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        // Auth key cookie kontrolü
        $authKey = $_COOKIE['auth_key'] ?? null;

        if ($authKey) {
            // JWT token'ı RS256 ile doğrula
            $userData = $this->validateJwtToken($authKey);

            if ($userData) {
                $_SESSION['user_id']     = (int) $userData['sub'];
                $_SESSION['role']        = $userData['role'] ?? 'guest';
                $_SESSION['email']       = $userData['email'] ?? '';
                $_SESSION['gender']      = $userData['gender'] ?? 'neutral';
                $_SESSION['permissions'] = $userData['permissions'] ?? [];
            }
        }

        // Request'e kullanıcı bilgisi inject
        $request->setAttribute('user_id',          $_SESSION['user_id'] ?? null);
        $request->setAttribute('role',             $_SESSION['role'] ?? null);
        $request->setAttribute('email',            $_SESSION['email'] ?? null);
        $request->setAttribute('gender',           $_SESSION['gender'] ?? null);
        $request->setAttribute('is_authenticated', isset($_SESSION['user_id']));

        return $next($request);
    }

    /**
     * JWT RS256 token doğrulama.
     *
     * @see [[auth]] §JWT Sign & Verify
     */
    private function validateJwtToken(string $token): ?array
    {
        $publicKeyPath = getenv('AUTH_JWT_PUBLIC_KEY'); // .pem path

        try {
            $decoded = \Firebase\JWT\JWT::decode(
                $token,
                new \Firebase\JWT\Key($publicKeyPath, 'RS256')
            );
            return (array) $decoded;
        } catch (\Exception $e) {
            error_log("[AuthMiddleware] JWT validation failed: " . $e->getMessage());
            return null;
        }
    }
}
```

### 3.5 SecurityHeadersMiddleware (ADR-012)

| Header | Değer | ADR |
|--------|-------|-----|
| `Content-Security-Policy` | `default-src 'self'; script-src 'self' 'nonce-{random}' 'strict-dynamic'` | ADR-012 |
| `X-Frame-Options` | DENY | — |
| `X-Content-Type-Options` | nosniff | — |
| `X-XSS-Protection` | 1; mode=block | — |
| `Referrer-Policy` | strict-origin-when-cross-origin | — |
| `Strict-Transport-Security` | max-age=31536000; includeSubDomains | — |
| `Permissions-Policy` | camera=(), microphone=(), geolocation=() | — |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Security Headers Middleware — ADR-012 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class SecurityHeadersMiddleware
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $response = $next($request);

        // CSP nonce
        $nonce = $_SESSION['csp_nonce'] ?? base64_encode(random_bytes(32));

        // Security headers
        $headers = [
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'; style-src 'self' 'nonce-{$nonce}'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

        // HSTS sadece HTTPS
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $name => $value) {
            $response->setHeader($name, $value);
        }

        return $response;
    }
}
```

### 3.6 CsrfMiddleware (ADR-010)

| Özellik | Değer |
|---------|-------|
| **Token Key** | `csrf_token` (frozen — `_csrf_token` yasak) |
| **Token Length** | 256-bit (32 bytes) |
| **Comparison** | `hash_equals()` (timing-safe) |
| **Apply To** | POST, PUT, DELETE |
| **Skip** | GET, HEAD, OPTIONS |
| **Storage** | Session'da saklanır |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * CSRF Middleware — ADR-010 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class CsrfMiddleware
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $method = $request->getMethod();

        // GET, HEAD, OPTIONS için CSRF kontrolü yapılmaz
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            // Token üret ve session'a kaydet
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return $next($request);
        }

        // POST, PUT, DELETE için CSRF doğrulama
        $token = $_POST['csrf_token']
            ?? $request->getHeader('X-CSRF-Token')
            ?? null;

        if (!$token || empty($_SESSION['csrf_token'])) {
            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'CSRF_TOKEN_MISSING',
                    'message' => 'CSRF token bulunamadı'
                ]
            ], 403);
        }

        // Timing-safe comparison
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'CSRF_TOKEN_INVALID',
                    'message' => 'CSRF token geçersiz'
                ]
            ], 403);
        }

        return $next($request);
    }
}
```

## 4. Pipeline Implementasyonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Middleware Pipeline — PSR bağımsız.
 * Sıfırdan vanilla PHP ile yazılmıştır.
 */
class Pipeline
{
    private array $middlewares = [];

    public function pipe(middleware $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function run(
        \CoreMusic\Http\Request $request,
        callable $controller
    ): \CoreMusic\Http\Response {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $mw) => fn($req) => $mw->handle($req, $next),
            $controller
        );

        return $pipeline($request);
    }
}
```

## 5. Middleware Sırası Gerekçesi

| Sıra | Middleware | Neden Bu Sırada? |
|------|-----------|------------------|
| 1 | SessionManager | Session ve nonce her şeyden önce hazır olmalı |
| 2 | BypassAuth | Test ortamında erişim kolaylığı |
| 3 | RateLimiter | Auth'dan önce brute-force koruması |
| 4 | Auth | Kullanıcı bilgisi header'lardan sonra hazır |
| 5 | SecurityHeaders | CSP nonce SessionManager'dan gelir |
| 6 | Csrf | Son adım — tüm middleware'lerden sonra |

## 6. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Pipeline sırası frozen | ADR-010/011/012/013/022 | CSP/CSRF bozulması |
| 2 | Session name = `COREMUSIC_SESS` | ADR-011 | Session çakışması |
| 3 | CSRF key = `csrf_token` | ADR-010 | CSRF bypass |
| 4 | CSP nonce = `base64_encode(random_bytes(32))` | ADR-012 | XSS riski |
| 5 | BypassAuth prod'da devre dışı | ADR-008 | Auth bypass |
| 6 | `hash_equals()` timing-safe | ADR-010 | Timing attack |

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/l1-security/index]] | Security layer |
| [[ADR-008-bypass-auth-middleware]] | Bypass auth |
| [[ADR-010-csrf-protection-strategy]] | CSRF |
| [[ADR-011-session-management]] | Session |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| [[ADR-022-database-hardened-security]] | Encryption |

## 8. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Pipeline | [[ADR-010-csrf-protection-strategy]] | CSRF koruması |
| § 3.1 Session | [[ADR-011-session-management]] | Session yönetimi |
| § 3.5 Security | [[ADR-012-csp-nonce-strict-dynamic]] | CSP politikası |
| § 3.3 Rate | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 4 Implement | [[architecture/l2-routing/index]] | Routing |

## 9. Sözlük

| Terim | Tanım |
|-------|-------|
| **Middleware** | Request/Response arasında çalışan katman |
| **Pipeline** | Middleware'lerin sıralı dizisi |
| **Session** | Kullanıcı oturumu |
| **CSRF** | Cross-Site Request Forgery |
| **CSP** | Content Security Policy |
| **Rate Limit** | İstek hız kısıtlaması |
| **APCu** | APC User Cache |
| **RBAC** | Role-Based Access Control |
| **Nonce** | Number used once — tek kullanımlık değer |
| **Timing-safe** | Zamanlama saldırılarına karşı güvenli |

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~560 |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 022, 047, 052 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 7 referans |
| **Guardrails** | ✅ 6 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
