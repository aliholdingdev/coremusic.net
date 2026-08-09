---
type: architecture
category: l1
title: "L1 — Middleware Pipeline"
date: 2026-08-08
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L1 — Middleware Pipeline

**See also:** [[index]] · [[session]] · [[csrf]] · [[csp]] · [[auth]]

## 1. Amaç

CoreMusic middleware pipeline'ı, her HTTP isteğinin geçmesi gereken 6 katmanlı sıralı güvenlik hattıdır. Her middleware belirli bir güvenlik sorumluluğunu üstlenir ve zincir halinde çalışır. Sıra **frozen**'dır — değiştirilemez.

```
Request → SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf → Controller
```

*Kaynak: [[ADR-010-csrf-protection-strategy]], [[ADR-011-session-management]], [[ADR-012-csp-nonce-strict-dynamic]], [[ADR-013-rate-limiting-apcu]], [[ADR-022-database-hardened-security]]*

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 6 middleware tanımı | Controller iş mantığı |
| Pipeline orchestration | Veritabanı işlemleri |
| Request/Response akışı | Frontend kodu |
| Error handling (middleware seviyesi) | UI error sayfaları |
| Middleware lifecycle | Deployment |

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **Middleware** | İstek/yanıt zincirinde sıralı güvenlik katmanı |
| **Pipeline** | Middleware'lerin sıralı çalıştığı zincir |
| **Frozen Order** | Değiştirilemez sıralama kuralı |
| **Handler** | Middleware'in çalıştırıldığı ana fonksiyon |
| **Next** | Sıradaki middleware'e geçiş fonksiyonu |
| **Short-Circuit** | Zinciri erken sonlandırma (429, 401, redirect) |
| **ServerRequestInterface** | Custom istek nesnesi (`CoreMusic\Http\Request`) |
| **ResponseInterface** | Custom yanıt nesnesi (`CoreMusic\Http\Response`) |

## 4. Pipeline Mimarisi

### 4.1 Sıra (Frozen — ADR-010/011/012/013/022)

```
┌─────────────────────────────────────────────────────────────────┐
│                     MIDDLEWARE PIPELINE                         │
├─────────────────────────────────────────────────────────────────┤
│  1. SessionManagerMiddleware()    — Session başlat, CSP nonce   │
│  2. BypassAuthMiddleware()        — Test bypass (prod'da OFF)   │
│  3. RateLimiterMiddleware()       — APCu: 60 req/60s            │
│  4. AuthMiddleware()              — Auth bilgisi inject          │
│  5. SecurityHeadersMiddleware()   — CSP + X-Frame + HSTS        │
│  6. CsrfMiddleware()              — csrf_token doğrulama         │
└─────────────────────────────────────────────────────────────────┘
```

**Kritik Not:** CSP nonce üretimi SessionManager içindedir. Sıra değiştirilirse CSP bozulur.

### 4.2 Bağımlılık Matrisi

| Middleware | Bağımlı Olduğu | Bağımlılık Tipi |
|------------|----------------|-----------------|
| SessionManager | — | Bağımsız (ilk) |
| BypassAuth | SessionManager | Session okuması |
| RateLimiter | — | Bağımsız |
| Auth | SessionManager | Session'dan user okuma |
| SecurityHeaders | SessionManager | CSP nonce okuması |
| Csrf | SessionManager | Session'dan token okuması |

## 5. Pipeline Implementation

### 5.1 Ana Pipeline Sınıfı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Middleware pipeline — PSR bağımsız.
 * Sıfırdan vanilla PHP ile yazılmıştır.
 *
 * Frozen order: SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
 * @see [[ADR-010-csrf-protection-strategy]]
 * @see [[ADR-011-session-management]]
 */
class Pipeline
{
    private array $middlewares = [];

    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Pipeline'ı çalıştır.
     *
     * Her middleware bir sonraki middleware'e $next fonksiyonunu çağırarak geçer.
     * Bir middleware short-circuit yapabilir (429, 401, redirect).
     */
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

### 5.2 Middleware Arayüzü

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Temel middleware arayüzü.
 *
 * Tüm middleware'ler bu arayüzü uygulamalıdır.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
interface MiddlewareInterface
{
    /**
     * İsteği işle.
     *
     * @param \CoreMusic\Http\Request $request PSR bağımsız istek
     * @param callable $next Sıradaki middleware
     * @return \CoreMusic\Http\Response PSR bağımsız yanıt
     */
    public function handle(
        \CoreMusic\Http\Request $request,
        callable $next
    ): \CoreMusic\Http\Response;
}
```

### 5.3 Short-Circuit Örneği

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Rate limiter middleware — short-circuit example.
 *
 * Eğer rate limit aşılırsa 429 döner ve pipeline'ı durdurur.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class RateLimiterMiddleware implements MiddlewareInterface
{
    private int $defaultLimit = 60;
    private int $windowSeconds = 60;

    public function handle(
        \CoreMusic\Http\Request $request,
        callable $next
    ): \CoreMusic\Http\Response {
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
            header('Retry-After: ' . $retryAfter);

            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Cok fazla istek',
                    'retry_after' => $retryAfter,
                ],
            ], 429);
        }

        // Sayacı artır
        apcu_store($key, $count + 1, $this->windowSeconds * 2);

        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . ($limit - $count - 1));

        return $next($request);
    }

    private function getLimitForPath(string $path): int
    {
        if (str_contains($path, '/login'))    return 5;
        if (str_contains($path, '/register')) return 3;
        if (str_contains($path, '/api/'))     return 120;
        return $this->defaultLimit;
    }
}
```

## 6. Middleware Detayları

### 6.1 SessionManagerMiddleware (ADR-011)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Session başlatır, CSP nonce üretir |
| **Timeout** | 3600s idle, 86400s absolute |
| **Cookie** | HttpOnly, Secure, SameSite=Lax |
| **Nonce** | `base64_encode(random_bytes(32))` |
| **Short-Circuit** | Session timeout → redirect /login |

Detay: [[session]]

### 6.2 BypassAuthMiddleware (ADR-008)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Test ortamında auth bypass |
| **Trigger** | `?_bypass=1` parametresi |
| **Prod Durumu** | Devre dışı (hardcoded) |
| **Short-Circuit** | Yok (her zaman devam eder) |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Bypass auth middleware — test only.
 *
 * Production'da devre dışı. Sadece test ortamında aktif.
 * @see [[ADR-008-bypass-auth-middleware]]
 */
class BypassAuthMiddleware implements MiddlewareInterface
{
    private const IS_PROD = true; // Production flag

    public function handle(
        ServerRequestInterface $request,
        callable $next
    ): ResponseInterface {
        // Production'da her zaman devam et
        if (self::IS_PROD) {
            return $next($request);
        }

        // Test modunda bypass kontrolü
        $params = $request->getQueryParams();
        if (isset($params['_bypass']) && $params['_bypass'] === '1') {
            // Test kullanıcısı inject et
            $request = $request->withAttribute('user_id', 1);
            $request = $request->withAttribute('role', 'admin');
        }

        return $next($request);
    }
}
```

### 6.3 RateLimiterMiddleware (ADR-013)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | APCu tabanlı hız sınırlama |
| **Limit** | 60 req/60s (varsayılan) |
| **Login Limit** | 5 req/60s |
| **Short-Circuit** | 429 Too Many Requests |

Detay: [[middleware]] §8 (rate limiting detayı)

### 6.4 AuthMiddleware (ADR-011 + ADR-047)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Kullanıcı bilgisi inject + JWT doğrulama |
| **Auth Key** | `auth_key` cookie (auth.coremusic.net) |
| **JWT Algorithm** | RS256 (RSA SHA-256) — `firebase/php-jwt` |
| **Session Vars** | `user_id`, `role`, `email`, `gender`, `permissions` |
| **RBAC** | 7 granular roller (admin → guest) |
| **Short-Circuit** | Yok (AuthMiddleware sadece bilgi inject eder) |

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Auth Middleware — ADR-011 + ADR-047 + ADR-052 compliant.
 * Hybrid auth: Session + JWT RS256.
 *
 * auth_key cookie'den JWT token'ı doğrular, session'a yazar,
 * request'e kullanıcı bilgisi inject eder.
 *
 * @see [[auth]] — tam RBAC tablosu ve izin matrisi
 */
class AuthMiddleware implements MiddlewareInterface
{
    /** @var array<string, array{int,int}> Rol → [min, max] izin aralığı */
    private const RBAC_MAP = [
        'admin'          => [1000, 1999],
        'ultra_user'     => [800,  899],
        'premium_user'   => [700,  799],
        'streaming_user' => [600,  699],
        'panel_user'     => [500,  599],
        'free_user'      => [100,  199],
        'guest'          => [0,    0],
    ];

    public function handle(
        \CoreMusic\Http\Request $request,
        callable $next
    ): \CoreMusic\Http\Response {
        $authKey = $_COOKIE['auth_key'] ?? null;

        if ($authKey) {
            $payload = $this->validateJwtToken($authKey);
            if ($payload !== null) {
                // Session'a yaz
                $_SESSION['user_id']    = (int) $payload['sub'];
                $_SESSION['role']       = $payload['role'] ?? 'guest';
                $_SESSION['email']      = $payload['email'] ?? '';
                $_SESSION['gender']     = $payload['gender'] ?? 'neutral';
                $_SESSION['permissions']= $payload['permissions'] ?? [];
            }
        }

        // Request'e inject
        $request->setAttribute('user_id',         $_SESSION['user_id'] ?? null);
        $request->setAttribute('role',            $_SESSION['role'] ?? null);
        $request->setAttribute('email',           $_SESSION['email'] ?? null);
        $request->setAttribute('gender',          $_SESSION['gender'] ?? null);
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

### 6.5 SecurityHeadersMiddleware (ADR-012)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | CSP, X-Frame-Options, HSTS |
| **CSP** | nonce-based, strict-dynamic |
| **Short-Circuit** | Yok (her zaman devam eder) |

Detay: [[csp]]

### 6.6 CsrfMiddleware (ADR-010)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | csrf_token doğrulama |
| **Token Key** | `csrf_token` (frozen) |
| **Doğrulama** | `hash_equals()` (timing-safe) |
| **Short-Circuit** | 403 Forbidden (token geçersiz) |

Detay: [[csrf]]

## 7. Request/Response Akışı

### 7.1 Normal Akış (Başarılı)

```
HTTP POST /api/music
  │
  ├─→ SessionManagerMiddleware
  │     ├─ session_start()
  │     ├─ CSP nonce üret
  │     └─ → next($request)
  │
  ├─→ BypassAuthMiddleware
  │     ├─ Prod modunda atla
  │     └─ → next($request)
  │
  ├─→ RateLimiterMiddleware
  │     ├─ apcu_fetch(rate_limit:ip:window)
  │     ├─ 60 < limit → devam
  │     └─ → next($request)
  │
  ├─→ AuthMiddleware
  │     ├─ Session'dan user_id al
  │     ├─ request->withAttribute(user_id)
  │     └─ → next($request)
  │
  ├─→ SecurityHeadersMiddleware
  │     ├─ header("Content-Security-Policy: ...")
  │     ├─ header("X-Frame-Options: DENY")
  │     └─ → next($request)
  │
  ├─→ CsrfMiddleware
  │     ├─ $_POST['csrf_token'] oku
  │     ├─ hash_equals(session_token, submitted)
  │     └─ → next($request)
  │
  └─→ Controller
        ├─ İş mantığını çalıştır
        └─ Response dön
```

### 7.2 Short-Circuit Akışları

```
Rate Limit Aşımı:
  RateLimiter → 429 Response (pipeline durur)

Session Timeout:
  SessionManager → redirect /login (pipeline durur)

CSRF Geçersiz:
  Csrf → 403 Forbidden (pipeline durur)
```

## 8. Error Handling

### 8.1 Middleware Hata Türleri

| Hata Türü | Kaynak | Yanıt | HTTP Status |
|-----------|--------|-------|-------------|
| Session Timeout | SessionManager | Redirect | 302 |
| Rate Limit | RateLimiter | JSON error | 429 |
| Auth Required | Auth | Redirect | 302 |
| CSRF Invalid | Csrf | JSON error | 403 |
| CSP Violation | Browser | Console error | — |
| Middleware Exception | Herhangi biri | 500 error | 500 |

### 8.2 Exception Handling

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Pipeline exception handler.
 *
 * Middleware'lerden biri exception fırlatırsa 500 döner.
 */
class PipelineExceptionHandler
{
    public function handle(\Throwable $e): void
    {
        // Log error
        error_log("[L1] Pipeline error: " . $e->getMessage());

        // Security: Hassas bilgiyi gizle
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal server error',
            'code' => 500,
        ]);
        exit;
    }
}
```

## 9. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Middleware sırasını değiştirme | Frozen sırayı koruma | CSP/CSRF bozulması |
| CSRF'de `_csrf_token` kullanma | `csrf_token` kullanma | Token reddedilir |
| Rate limit'de `$_SERVER['HTTP_X_FORWARDED_FOR']` | `REMOTE_ADDR` kullanma | Bypass riski |
| Auth'da `$_SESSION` dışında auth | Session-based auth | Güvenlik açığı |
| CSP'de `unsafe-eval` | Nonce-based | Script injection |
| BypassAuth'ı prod'da aktif etme | Prod'da devre dışı | Auth bypass |
| Pipeline'a yeni middleware ekleme (onay olmadan) | Onay ile ekleme | Mimari ihlal |

## 10. Edge Cases

| Durum | Tetikleyici | Çözüm | ADR |
|-------|-------------|-------|-----|
| **Eşzamanlı istekler** | Çoklu tab | Session-locking (PHP varsayılan) | ADR-011 |
| **Middleware exception** | Kod hatası | Exception handler → 500 | — |
| **CSP nonce sızıntısı** | Log'da nonce yazma | Nonce asla loglanmaz | ADR-012 |
| **Rate limit false positive** | Proxy arkası | X-Forwarded-For güvenilir mi? | ADR-013 |
| **Session fixation** | Login sonrası ID değişmez | `session_regenerate_id(true)` | ADR-011 |
| **CSRF token drift** | SPA DOM patch | Token DOM patch sonrası güncelle | ADR-021 |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Middleware sırası **frozen** — değiştirilemez | CSP/CSRF bozulması, revert |
| 2 | CSP nonce **sadece** SessionManager'da üretilir | CSP bozulması |
| 3 | BypassAuth **prod'da devre dışı** | Auth bypass açığı |
| 4 | RateLimiter **short-circuit** yapar (429) | Abuse riski |
| 5 | CsrfMiddleware **short-circuit** yapar (403) | CSRF saldırısı |
| 6 | Her middleware **`CoreMusic\Http`** arayüzü kullanır | Uyumsuzluk |
| 7 | Pipeline'a yeni middleware **onay ile** eklenir | Mimari ihlal |
| 8 | JWT RS256 **firebase/php-jwt** ile doğrulanır | Token bypass |
| 9 | RBAC **7 granular rol** haritasına uygun | Yetki ihlali |
| 10 | Hassas veriler **`[REDACTED]`** ile loglanır | Veri sızıntısı |

## 12. İlgili Dosyalar

| Dosya | Kapsam |
|-------|--------|
| [[index]] | L1 Security Layer genel bakış |
| [[session]] | Session yönetimi detayları |
| [[csrf]] | CSRF koruması detayları |
| [[csp]] | CSP nonce + strict-dynamic detayları |
| [[auth]] | Authentication detayları |
| [[ADR-010-csrf-protection-strategy]] | CSRF karar dokümanı |
| [[ADR-011-session-management]] | Session karar dokümanı |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP karar dokümanı |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting karar dokümanı |
| [[ADR-008-bypass-auth-middleware]] | BypassAuth karar dokümanı |
| [[ADR-022-database-hardened-security]] | Encryption karar dokümanı |

## 13. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Pipeline | [[ADR-010/011/012/013/022]] | Frozen pipeline sırası |
| § SessionManager | [[session]] | Session detayları |
| § CsrfMiddleware | [[csrf]] | CSRF detayları |
| § SecurityHeaders | [[csp]] | CSP detayları |
| § AuthMiddleware | [[auth]] | Auth detayları |
| § RateLimiter | [[ADR-013-rate-limiting-apcu]] | APCu rate limit |
| § BypassAuth | [[ADR-008-bypass-auth-middleware]] | Test bypass |

## 14. Sözlük

| Terim | Tanım |
|-------|-------|
| **Pipeline** | Middleware'lerin sıralı çalıştığı zincir |
| **Frozen Order** | Değiştirilemez sıralama kuralı |
| **Short-Circuit** | Zinciri erken sonlandırma |
| **PSR-7** | PHP Standartları Önerisi — HTTP mesajları (referans, kullanılmıyor) |
| **Middleware** | İstek/yanıt zincirinde güvenlik katmanı |
| **Nonce** | Tek kullanımlık rastgele değer (CSP) |
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **CSRF** | Cross-Site Request Forgery |
| **CSP** | Content Security Policy |
| **HSTS** | HTTP Strict Transport Security |
| **RBAC** | Role-Based Access Control |
| **Argon2id** | Şifreleme algoritması (RFC 9106) |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | 560+ |
| **Frontmatter** | ✅ |
| **Bölüm Sayısı** | 15 |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 022, 047, 052 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Guardrails** | ✅ 10 kural |

---

*L1 Middleware Pipeline v2.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-09*
*Mode: Red Team · Human Mode · Truth Mode*
