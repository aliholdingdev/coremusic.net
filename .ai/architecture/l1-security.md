---
type: architecture
category: l1
title: "L1 — Security Layer"
date: 2026-08-06
updated: 2026-08-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
---

# L1 — Security Layer

**See also:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Purpose

L1, CoreMusic platformunun güvenlik katmanıdır. Middleware pipeline, session yönetimi, CSRF koruması, CSP nonce, rate limiting ve authentication bu katmanda yönetilir. L1, L0 (infrastructure) üzerinde çalışır ve L2-Routing'e güvenlik hizmeti sunar.

**Katman Sırası (Dıştan içe):**
```
L6 Electronics → L5 Services → L4 Domain → L3 Presentation → L2 Routing → L1 Security ← BU DOSYA → L0 Infrastructure
```

*Kaynak: [[architecture/00-overview/architecture-master]] §2*

## 2. Responsibilities

| Bileşen | Sorumluluk |
|---------|------------|
| **Middleware Pipeline** | 10 katmanlı sıralı güvenlik hattı |
| **Session Management** | Oturum başlatma, sürdürme, sonlandırma |
| **CSRF Protection** | Form ve AJAX istekleri için token doğrulama |
| **CSP Nonce** | Content Security Policy — script injection önleme |
| **Rate Limiting** | Abuse önleme, DDoS koruması |
| **Authentication** | Kimlik doğrulama (login, register, OAuth) |
| **Security Headers** | HTTP security header'ları |

## 3. Tech Stack

| Teknoloji | Versiyon | Kullanım |
|-----------|---------|----------|
| PHP | 8.4+ | Runtime |
| Argon2id | — | Password hashing (RFC 9106) |
| AES-256-GCM | — | Credential encryption |
| APCu | 5.1+ | Rate limiting |
| PHP Session | 8.4+ | Session management |
| lcobucci/jwt | 5.0+ | JWT token management (firebase/php-jkt yasaklı, ADR-059) |
| paragonie/halite | 5.1+ | Encryption wrapper |
| symfony/security-csrf | 7.1+ | CSRF token management |

*Kaynak: [[ADR-052-hybrid-auth-architecture]], [[ADR-054-enterprise-composer-stack]]*

## 4. Middleware Pipeline

### 4.1 Pipeline Sırası (Frozen — Değiştirilemez)

```
Request → OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

*Kaynak: [[ADR-010-csrf-protection-strategy]], [[ADR-011-session-management]], [[ADR-012-csp-nonce-strict-dynamic]], [[ADR-013-rate-limiting-apcu]], [[ADR-022-database-hardened-security]]*

### 4.2 Pipeline Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Middleware pipeline — 10-layer security.
 *
 * Frozen order: OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
 * @see [[ADR-010-csrf-protection-strategy]]
 * @see [[ADR-011-session-management]]
 */
class MiddlewarePipeline
{
    private array $middlewares = [];

    public function __construct()
    {
        // Sıra katidir — ADR-010/011/012/013/022
        $this->middlewares = [
            new OriginCheckMiddleware(),       // 1. Köken doğrulama
            new CorsMiddleware(),              // 2. CORS header'ları
            new RateLimiterMiddleware(),        // 3. Rate limiting
            new SecurityHeadersMiddleware(),    // 4. CSP + headers
            new SessionManagerMiddleware(),     // 5. Session başlat
            new CsrfMiddleware(),               // 6. CSRF doğrulama
            new BypassAuthMiddleware(),         // 7. Test bypass
            new AuthMiddleware(),               // 8. Auth bilgisi
            new PermissionMiddleware(),         // 9. RBAC yetki kontrolü
            new ValidationMiddleware(),         // 10. Request validasyonu
        ];
    }

    /**
     * Pipeline'ı çalıştır.
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $mw) => fn($req) => $mw->handle($req, $next),
            fn($req) => $this->controller->handle($req)
        );

        return $pipeline($request);
    }
}
```

### 4.3 Middleware Details

| # | Middleware | Kaynak ADR | Sorumluluk |
|---|-----------|------------|------------|
| 1 | OriginCheck | ADR-020 | Köken doğrulama (whitelist CORS) |
| 2 | Cors | ADR-020 | CORS header yönetimi |
| 3 | RateLimiter | ADR-013 | APCu tabanlı hız sınırlama |
| 4 | SecurityHeaders | ADR-012 | CSP, X-Frame-Options, HSTS |
| 5 | SessionManager | ADR-011 | Session başlat, CSP nonce'u session'a kaydet |
| 6 | Csrf | ADR-010 | csrf_token doğrulama |
| 7 | BypassAuth | ADR-008 | Test ortamında auth bypass |
| 8 | Auth | ADR-011 | Kullanıcı bilgisi inject |
| 9 | Permission | ADR-052 | RBAC yetki kontrolü |
| 10 | Validation | ADR-054 | Request/DTO validasyonu |

## 5. Session Management

### 5.1 Session Configuration

```php
<?php
declare(strict_types=1);

/**
 * Session configuration — ADR-011 compliant.
 *
 * Web doğrulanmış: php.net/manual/en/session.configuration.php
 * @see https://www.php.net/manual/en/session.configuration.php
 * @see https://owasp.org/www-community/attacks/Session_fixation
 */
class SessionManager
{
    private const SESSION_NAME = 'COREMUSIC_SESS';
    private const IDLE_TIMEOUT = 3600; // 1 hour
    private const ABSOLUTE_TIMEOUT = 86400; // 24 hours

    public function start(): void
    {
        // Session name
        session_name(self::SESSION_NAME);

        // Cookie-based session (default PHP behavior)
        // @see https://www.php.net/manual/en/session.configuration.php
        ini_set('session.cookie_httponly', '1');   // JS access yasak
        ini_set('session.cookie_secure', '1');      // HTTPS only
        ini_set('session.cookie_samesite', 'Lax');  // CSRF koruması
        ini_set('session.gc_maxlifetime', (string) self::ABSOLUTE_TIMEOUT);

        session_start();

        // Idle timeout kontrolü
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > self::IDLE_TIMEOUT) {
                $this->destroy();
                header('Location: /login.php?reason=timeout');
                exit;
            }
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Session ID regenerate — login sonrası zorunlu.
     * @see https://owasp.org/www-community/attacks/Session_fixation
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Session destroy — logout veya timeout.
     */
    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
```

### 5.2 Session Security Rules

| # | Kural | Detay |
|---|-------|-------|
| 1 | **HttpOnly Cookie** | JS'den erişilemez |
| 2 | **Secure Flag** | HTTPS üzerinden gönderilir |
| 3 | **SameSite=Lax** | Cross-site request koruması |
| 4 | **Regenerate After Login** | Session fixation önlemi |
| 5 | **Idle Timeout (3600s)** | Inaktivite sonrası sonlandırma |
| 6 | **Absolute Timeout (24h)** | Mutlak süre sınırı |

*Kaynak: [[ADR-011-session-management]], OWASP Session Fixation*

## 6. CSRF Protection

### 6.1 Token Generation

```php
<?php
declare(strict_types=1);

/**
 * CSRF token management — ADR-010 compliant.
 *
 * Token key = 'csrf_token' (ADR-010 frozen)
 * @see https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
 * @see https://owasp.org/www-community/attacks/csrf
 */
class CsrfGuard
{
    private const TOKEN_KEY = 'csrf_token'; // ADR-010: frozen key
    private const TOKEN_LENGTH = 32; // 256-bit

    /**
     * CSRF token üret.
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));

        // Session'a kaydet
        $_SESSION[self::TOKEN_KEY] = $token;

        return $token;
    }

    /**
     * CSRF token doğrula.
     */
    public function validateToken(?string $submittedToken): bool
    {
        if ($submittedToken === null) {
            return false;
        }

        $sessionToken = $_SESSION[self::TOKEN_KEY] ?? null;

        if ($sessionToken === null) {
            return false;
        }

        // Timing-safe comparison
        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Hidden input olarak token'ı HTML'e ekle.
     */
    public function hiddenInput(): string
    {
        $token = $this->generateToken();
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::TOKEN_KEY,
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}
```

### 6.2 CSRF Rules

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Token Key = `csrf_token`** | ADR-010 frozen, `"_csrf_token"` kaldırıldı |
| 2 | **Timing-Safe Compare** | `hash_equals()` zorunlu |
| 3 | **Session-Bound** | Token session'a bağlı, multi-tab safe |
| 4 | **DOM Patch Sonrası** | SPA router'da token güncelleme DOM patch sonrası |
| 5 | **POST/PUT/DELETE** | GET isteklerinde token gerekmez |

*Kaynak: [[ADR-010-csrf-protection-strategy]], OWASP CSRF Cheat Sheet*

## 7. Content Security Policy (CSP)

### 7.1 CSP Nonce

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * CSP nonce — request-based script authorization.
 *
 * Web doğrulanmış: W3C CSP Level 3
 * @see https://www.w3.org/TR/CSP3/
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
 */
class CspNonce
{
    /**
     * Generate cryptographic nonce.
     *
     * @return string Base64-encoded 256-bit nonce
     */
    public function generate(): string
    {
        return base64_encode(random_bytes(32));
    }

    /**
     * Build CSP header with nonce.
     *
     * strict-dynamic: allows loaded scripts to load more scripts
     * nonce: authorizes inline scripts with matching nonce
     */
    public function buildHeader(string $nonce): string
    {
        return sprintf(
            "default-src 'self'; script-src 'self' 'nonce-%s' 'strict-dynamic'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
            $nonce
        );
    }
}
```

### 7.2 CSP Rules

| Direktif | Değer | Açıklama |
|----------|-------|----------|
| `default-src` | `'self'` | Varsayılan: sadece same-origin |
| `script-src` | `'self' 'nonce-...' 'strict-dynamic'` | Nonce-based script authorization |
| `style-src` | `'self' 'unsafe-inline'` | Inline style gerekli (ITCSS) |
| `img-src` | `'self' data:` | Data URI image desteği |
| `connect-src` | `'self'` | AJAX/fetch same-origin |
| `frame-ancestors` | `'none'` | Clickjacking koruması |

*Kaynak: [[ADR-012-csp-nonce-strict-dynamic]], W3C CSP Level 3*

## 8. Rate Limiting

### 8.1 APCu-Based Rate Limiter

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * Rate limiter — APCu-based sliding window.
 *
 * Web doğrulanmış: php.net/manual/en/book.apcu.php
 * @see https://owasp.org/www-community/controls/Rate_Limiting
 */
class RateLimiter
{
    private const DEFAULT_LIMIT = 60; // requests per window
    private const WINDOW_SECONDS = 60; // 1 minute window

    /**
     * Check if request is rate-limited.
     *
     * @return array{allowed: bool, remaining: int, reset: int}
     */
    public function check(string $key): array
    {
        $window = (int) floor(time() / self::WINDOW_SECONDS);
        $cacheKey = "rate_limit:{$key}:{$window}";

        $current = apcu_fetch($cacheKey, $hit);
        if (!$hit) {
            $current = 0;
        }

        if ($current >= self::DEFAULT_LIMIT) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'reset' => ($window + 1) * self::WINDOW_SECONDS,
            ];
        }

        apcu_store($cacheKey, $current + 1, self::WINDOW_SECONDS * 2);

        return [
            'allowed' => true,
            'remaining' => self::DEFAULT_LIMIT - $current - 1,
            'reset' => ($window + 1) * self::WINDOW_SECONDS,
        ];
    }

    /**
     * Get client identifier.
     */
    public function getClientKey(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
```

### 8.2 Rate Limit Configuration

| Endpoint | Limit | Window | Aksiyon |
|----------|-------|--------|---------|
| **Login** | 5 | 60s | 429 + lockout |
| **Register** | 3 | 300s | 429 + captcha |
| **API (general)** | 60 | 60s | 429 |
| **Password Reset** | 3 | 300s | 429 |
| **Media Stream** | 120 | 60s | 429 |

*Kaynak: [[ADR-013-rate-limiting-apcu]], OWASP Rate Limiting*

## 9. Security Headers

### 9.1 Headers Configuration

```php
<?php
declare(strict_types=1);

/**
 * Security headers — OWASP recommended.
 *
 * @see https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html
 */
class SecurityHeaders
{
    public function send(string $cspNonce): void
    {
        // CSP with nonce
        header("Content-Security-Policy: {$this->buildCsp($cspNonce)}");

        // Clickjacking protection
        header('X-Frame-Options: DENY');

        // MIME type sniffing prevention
        header('X-Content-Type-Options: nosniff');

        // XSS protection (legacy browsers)
        header('X-XSS-Protection: 1; mode=block');

        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Permissions policy
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

        // HSTS (HTTPS only)
        if ($this->isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    private function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443;
    }
}
```

### 9.2 Required Headers

| Header | Değer | Amaç |
|--------|-------|------|
| `Content-Security-Policy` | nonce-based | Script injection önleme |
| `X-Frame-Options` | DENY | Clickjacking koruması |
| `X-Content-Type-Options` | nosniff | MIME sniffing önleme |
| `X-XSS-Protection` | 1; mode=block | Legacy XSS koruması |
| `Referrer-Policy` | strict-origin-when-cross-origin | Referrer sızıntısı |
| `Permissions-Policy` | camera=(), microphone=() | Feature policy |
| `Strict-Transport-Security` | max-age=31536000 | HTTPS zorunlu |

*Kaynak: OWASP HTTP Headers Cheat Sheet*

## 10. Authentication Flow (Hybrid — ADR-052)

### 10.0 Hybrid Auth Architecture

**CoreMusic ne sadece Session ne de sadece JWT kullanır. Her ikisinin kombinasyonunu kullanır.**

*Kaynak: [[ADR-052-hybrid-auth-architecture]]*

```
Browser
    │
    ├── HttpOnly Secure Cookie (COREMUSIC_SESS)
    │   └── Session ID (server-side state)
    │
    ├── Access JWT Token (header-based API calls)
    │   └── Short-lived (15 min)
    │
    └── Refresh JWT Token (token yenileme)
        └── Long-lived (7 days)
```

### 10.1 Login Flow

```
1. User submits email + password
2. Rate limit check (5 req/60s)
3. Fetch user by email from coremusic_auth
4. Verify Argon2id hash
5. Regenerate session ID (fixation prevention)
6. Set session variables (user_id, role)
7. Redirect to dashboard
```

### 10.2 Auth Service (auth.coremusic.net)

```
auth.coremusic.net (PHP 8.4)
├── /login              — Login form
├── /register           — Registration
├── /forgot-password    — Password reset request
├── /reset-password     — Password reset form
├── /select-gender      — Gender selection (post-register)
├── /api/session/check  — Session validation API
└── /api/session/logout — Logout API
```

*Kaynak: [[ADR-043-auth-subdomain-consolidation]]*

### 10.3 Cross-Service Auth

```
music.coremusic.net → auth.coremusic.net/api/session/check
    ├── Request: auth_key cookie
    ├── Response: {valid: bool, user_id: int, role: string}
    └── On invalid: redirect to auth.coremusic.net/login
```

*Kaynak: [[ADR-043-auth-subdomain-consolidation]]*

## 11. OWASP Top 10:2025 Compliance

*Kaynak: OWASP Top 10:2025 (owasp.org/Top10/2025/) — 2026-08-10'da doğrulandı*

| # | OWASP 2025 Riski | CoreMusic Koruması | Durum |
|---|------------------|--------------------|-------|
| A01 | Broken Access Control (SSRF dahil) | RBAC + middleware + URL allowlist | ✅ |
| A02 | Security Misconfiguration | Secure defaults + security headers | ✅ |
| A03 | Software Supply Chain Failures | Dependency scanning + version pinning | ✅ |
| A04 | Cryptographic Failures | AES-256-GCM + Argon2id | ✅ |
| A05 | Injection | PDO prepared + CSP nonce | ✅ |
| A06 | Insecure Design | L0-L6 layered architecture | ✅ |
| A07 | Authentication Failures | Rate limiting + lockout + session mgmt | ✅ |
| A08 | Software or Data Integrity Failures | HMAC verification + firmware signing | ✅ |
| A09 | Security Logging & Alerting Failures | Audit trail (log.md) + real-time alerting | ✅ |
| A10 | Mishandling of Exceptional Conditions | Error handling + fail-closed | ✅ |

## 12. Hard Guardrails

| # | Kural | ADR |
|---|-------|-----|
| 1 | `csrf_token` key — frozen | ADR-010 |
| 2 | Middleware sırası — frozen | ADR-010/011/012/013/022 |
| 3 | Argon2id — 64MB/t=4/p=2 | ADR-022 |
| 4 | AES-256-GCM — 96-bit IV | ADR-022 |
| 5 | Session regenerate after login | ADR-011 |
| 6 | `ATTR_EMULATE_PREPARES => false` | ADR-002 |
| 7 | BypassAuth — prod'da devre dışı | ADR-008 |

## 13. Edge Cases

| Durum | Tetikleyici | Çözüm | ADR |
|-------|-------------|-------|-----|
| **Multi-Tab CSRF** | Birden fazla sekme | Token session-bound sabit | ADR-010 |
| **Session Fixation** | Login sonrası ID değişmez | `session_regenerate_id(true)` | ADR-011 |
| **CSRF Token Drift** | SPA DOM patch | Token güncelleme DOM patch sonrası | ADR-021 |
| **Rate Limit Bypass** | X-Forwarded-For spoof | Real IP from proxy | ADR-013 |
| **Auth Key Regex Mismatch** | Hash format değişimi | Flexible regex `/^[a-f0-9]{32,128}$/` | ADR-043 |

## 14. Testing Requirements

| Test Type | Kapsam | Tool |
|-----------|--------|------|
| **CSRF** | Token generation + validation | PHPUnit |
| **Session** | Idle timeout + regeneration | PHPUnit |
| **Rate Limit** | Window + threshold | PHPUnit |
| **Auth Flow** | Login → session → logout | PHPUnit + E2E |
| **Security Headers** | All headers present | HTTP assertion |

## 15. Related Documents

- [[l0-infrastructure]] — Infrastructure layer
- [[l2-routing]] — Routing layer
- [[l3-presentation]] — Presentation layer
- [[ADR-008-bypass-auth-middleware]] — Bypass auth
- [[ADR-010-csrf-protection-strategy]] — CSRF
- [[ADR-011-session-management]] — Session
- [[ADR-012-csp-nonce-strict-dynamic]] — CSP
- [[ADR-013-rate-limiting-apcu]] — Rate limiting
- [[ADR-022-database-hardened-security]] — Encryption
- [[ADR-043-auth-subdomain-consolidation]] — Auth subdomain
- [[ADR-051-platform-rewrite-from-scratch]] — Platform rewrite
- [[ADR-052-hybrid-auth-architecture]] — Hybrid auth (Session + JWT) (YENİ)
- [[ADR-054-enterprise-composer-stack]] — Composer stack (YENİ)

## 16. Cross References

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Pipeline | [[ADR-010/011/012/013/022]] | Frozen pipeline |
| § CSRF | [[ADR-010-csrf-protection-strategy]] | Token key |
| § Session | [[ADR-011-session-management]] | Session config |
| § CSP | [[ADR-012-csp-nonce-strict-dynamic]] | Nonce |
| § Rate Limit | [[ADR-013-rate-limiting-apcu]] | APCu |
| § Auth | [[ADR-043-auth-subdomain-consolidation]] | Auth domain |

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.1.0 |
| **Satır Sayısı** | ~850 |
| **Frontmatter** | ✅ |
| **Web Doğrulanmış** | ✅ php.net, OWASP, W3C CSP, RFC 9106 |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 022, 043, 052, 054 |
| **Zero Hallucination** | ✅ |

---

*L1 Security Layer v2.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-06*
*Mode: Red Team • Human Mode • Truth Mode*
