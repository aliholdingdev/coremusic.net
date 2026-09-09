---
type: architecture
category: l2
title: "Middleware Pipeline"
date: 2026-08-08
updated: 2026-08-12
status: active
version: 6.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Middleware Pipeline

**Zorunlu Bağlantılar:** [[index]] · [[ADR-010-csrf-protection-strategy]] · [[ADR-011-session-management]] · [[ADR-087-master-implementation-plan]]

---

## 1. Amaç

Middleware pipeline orchestration ve sırasını tanımlar. [[ADR-010/011/012/013/022]] ile uyumludur.

---

## 2. Pipeline Sırası (Immutable)

```
Request → OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Handler → Response
```

| # | Middleware | Görev | ADR |
|---|-----------|-------|-----|
| 1 | OriginCheck | Köken doğrulama (whitelist CORS) | ADR-020 |
| 2 | Cors | CORS header yönetimi | ADR-020 |
| 3 | RateLimiter | Hız sınırlama (60 req/60s) | ADR-013 |
| 4 | SecurityHeaders | CSP, HSTS, X-Frame-Options | ADR-012 |
| 5 | SessionManager | Session başlat, CSP nonce'u session'a kaydet | ADR-011 |
| 6 | Csrf | CSRF token doğrulama | ADR-010 |
| 7 | BypassAuth | Test bypass (prod'da devre dışı) | ADR-008 |
| 8 | Auth | Auth bilgisi inject | ADR-011 |
| 9 | Permission | RBAC yetki kontrolü | ADR-011 |
| 10 | Validation | Request/DTO validasyonu | ADR-010 |

**⚠️ Middleware sırası DEĞİŞTİRİLEMEZ!**

---

## 3. Middleware Runner (PSR-15 Uyumlu)

**Kaynak:** PSR-15 standardı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware Pipeline — PSR-15 compliant.
 * nikic/fast-route + php-di/php-di ile entegre çalışır.
 *
 */
class Pipeline
{
    private array $middlewares = [];

    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function run(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): \Psr\Http\Message\ResponseInterface {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $mw) => fn($req) => $mw->process($req, $next),
            $handler
        );

        return $pipeline($request);
    }
}
```

**PSR-15 Uyumluluğu:**
- `Psr\Http\Server\MiddlewareInterface` — tüm middleware'ler bu arayüzü implemente eder
- `Psr\Http\Server\RequestHandlerInterface` — son handler (controller) bu arayüzü implemente eder
- `process()` metodu — PSR-15 standardı (`handle()` değil)
- `ServerRequestInterface` — PSR-7 request (nyholm/psr7)

---

## 4. Middleware Detayları

### 4.1 SessionManager (ADR-011)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Session Manager Middleware — ADR-011 compliant.
 * PSR bağımsız — sıfırdan vanilla PHP.
 */
class SessionManagerMiddleware implements MiddlewareInterface
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
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

### 4.2 BypassAuth (ADR-008)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Bypass Auth Middleware — ADR-008 compliant.
 * Sadece development ortamında ve ?_bypass=1 ile aktif.
 */
class BypassAuthMiddleware implements MiddlewareInterface
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $env = getenv('APP_ENV') ?: 'production';

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

### 4.3 RateLimiter (ADR-013)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Rate Limiter Middleware — ADR-013 compliant.
 * APCu tabanlı, endpoint bazlı limit.
 */
class RateLimiterMiddleware implements MiddlewareInterface
{
    private int $defaultLimit = 60;
    private int $windowSeconds = 60;

    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $path = $request->getPath();

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

### 4.4 Auth (ADR-011)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Auth Middleware — ADR-011 compliant.
 * Hybrid auth: Session + JWT RS256.
 *
 * JWT Token Politikası:
 * - Access Token: 15 dakika, RS256
 * - Refresh Token: 7 gün, RS256
 * - Key Rotation: 90 gün
 *
 * @see [[auth]] — tam RBAC tablosu ve izin matrisi
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): \Psr\Http\Message\ResponseInterface {
        $authKey = $_COOKIE['auth_key'] ?? null;

        if ($authKey) {
            $payload = $this->validateJwtToken($authKey);
            if ($payload !== null) {
                $_SESSION['user_id']     = (int) $payload['user_id'];
                $_SESSION['role']        = $payload['role'] ?? 'guest';
                $_SESSION['email']       = $payload['email'] ?? '';
                $_SESSION['gender']      = $payload['gender'] ?? 'neutral';
                $_SESSION['permissions'] = $payload['permissions'] ?? [];
            }
        }

        $request = $request->withAttribute('user_id',          $_SESSION['user_id'] ?? null)
                           ->withAttribute('role',             $_SESSION['role'] ?? null)
                           ->withAttribute('email',            $_SESSION['email'] ?? null)
                           ->withAttribute('gender',           $_SESSION['gender'] ?? null)
                           ->withAttribute('is_authenticated', isset($_SESSION['user_id']));

        return $handler->handle($request);
    }

    private function validateJwtToken(string $token): ?array
    {
        $publicKeyPath = getenv('AUTH_JWT_PUBLIC_KEY');

        try {
            $decoded = \Firebase\JWT\JWT::decode(
                $token,
                new \Firebase\JWT\Key($publicKeyPath, 'RS256')
            );

            // Token blacklist kontrolü (Redis/APCu)
            $tokenId = $decoded->jti ?? null;
            if ($tokenId && $this->isTokenBlacklisted($tokenId)) {
                return null;
            }

            return (array) $decoded;
        } catch (\Exception $e) {
            error_log("[AuthMiddleware] JWT failed: " . $e->getMessage());
            return null;
        }
    }

    private function isTokenBlacklisted(string $tokenId): bool
    {
        // Redis/APCu blacklist kontrolü
        return apcu_exists("blacklisted:jwt:{$tokenId}");
    }
}
```

### 4.5 SecurityHeaders (ADR-012)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * Security Headers Middleware — ADR-012 compliant.
 * CSP nonce-based, strict-dynamic.
 */
class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $response = $next($request);

        $nonce = $_SESSION['csp_nonce'] ?? base64_encode(random_bytes(32));

        $headers = [
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'; style-src 'self' 'nonce-{$nonce}'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'",
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

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

### 4.6 Csrf (ADR-010)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

/**
 * CSRF Middleware — ADR-010 compliant.
 * csrf_token key frozen — _csrf_token yasak.
 */
class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(\CoreMusic\Http\Request $request, callable $next): \CoreMusic\Http\Response
    {
        $method = $request->getMethod();

        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return $next($request);
        }

        $token = $_POST['csrf_token']
            ?? $request->getHeader('X-CSRF-Token')
            ?? null;

        if (!$token || empty($_SESSION['csrf_token'])) {
            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'CSRF_TOKEN_MISSING',
                    'message' => 'CSRF token bulunamadi',
                ],
            ], 403);
        }

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            return new \CoreMusic\Http\JsonResponse([
                'success' => false,
                'error' => [
                    'code' => 'CSRF_TOKEN_INVALID',
                    'message' => 'CSRF token gecersiz',
                ],
            ], 403);
        }

        return $next($request);
    }
}
```

---

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| Sıra değiştirme | Immutable sıra | ADR-010/011/012/013 |
| CSRF olmadan | CSRF zorunlu | ADR-010 |
| CSP olmadan | CSP zorunlu | ADR-012 |
| Rate limit yok | Rate limit zorunlu | ADR-013 |

---

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Middleware hatası** | try-catch + log | ADR-010 |
| **Timeout** | Default timeout | ADR-013 |
| **Circular dependency** | Pipeline sıra kontrolü | ADR-010 |
| **CSP nonce kaybı** | Session'dan yeniden oku | ADR-012 |

---

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Pipeline sırası frozen | ADR-010/011/012/013/022 | CSP/CSRF bozulması |
| 2 | Session name = `COREMUSIC_SESS` | ADR-011 | Session çakışması |
| 3 | CSRF key = `csrf_token` | ADR-010 | CSRF bypass |
| 4 | CSP nonce = `base64_encode(random_bytes(32))` | ADR-012 | XSS riski |
| 5 | BypassAuth prod'da devre dışı | ADR-008 | Auth bypass |
| 6 | `hash_equals()` timing-safe | ADR-010 | Timing attack |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L2 ana dizin |
| [[ADR-010-csrf-protection-strategy]] | CSRF |
| [[ADR-011-session-management]] | Session |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP |
| [[ADR-013-rate-limiting-apcu]] | Rate limit |
| [[ADR-022-database-hardened-security]] | Encryption |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Pipeline | [[ADR-010-csrf-protection-strategy]] | CSRF koruması |
| § 3 Runner | [[architecture/l2-routing/index]] | Routing |
| § 4.1 Session | [[ADR-011-session-management]] | Session yönetimi |
| § 4.4 Auth | [[ADR-011-session-management]] | JWT + Session auth |
| § 4.5 Security | [[ADR-012-csp-nonce-strict-dynamic]] | CSP politikası |
| § 4.3 Rate | [[ADR-013-rate-limiting-apcu]] | Rate limiting |

---

## 10. Sözlük

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

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
## 17. Risk Kaydı (Pipeline)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Örnek kodun gerçek sanılması | Yüksek (şti) | Yüksek | §12 eşleştirme tablosu — Truth Mode etiketi |
| 2 | JWT örneklerinin PLANNED olarak unutulması | Orta | Orta | §12 JWT notu + l1 index §21 |
| 3 | Middleware eklenirken sıra ihlali | Düşük | Kritik | §2 immutable + review gate |
| 4 | Rate limit örnek prefix'inin kopyalanması (`rate:`) | Orta | Orta | Gerçek `rl:` standardı (ADR-007) |
| 5 | BypassAuth örnek mekanizmasının kodlanması | Düşük | Yüksek | Gerçek: TEST_MODE/FORCE_AUTH_BYPASS |

---

## 18. Ek SSS

**S: Pipeline kodu bu dosyadan kopyalanır mı?**
C: Hayır — hepsi hedef desen ([[WORKFLOW.md]] §8.1C). Gerçek 4 sınıf `shared/src/Middleware/`'da IMPLEMENTED'dir; §12 tablosu farkları resmileştirir.

**S: SessionManager örneği nonce'u her istekte yeniliyor — gerçek davranış aynı mı?**
C: Örnek öyle; gerçek üretim satırı SecurityHeaders/SessionInitializer zincirinde teyit bekliyor (csp.md §16). Çelişki tespit edilene dek "her istekte yeni nonce" hedef davranış olarak okunur.

**S: Neden Validation middleware'i örneklenmemiş?**
C: PLANNED — `respect/validation` bağımlılığı hazır, sınıf yok. Yazılmamış kod için örnek göstermek kafa karıştırır; pipeline tablosu yeterlidir.

**S: Response emitter neden yok?**
C: PSR-7 emitter PLANNED (l0 §17); pipeline ResponseInterface üretir, gönderim katmanı ayrıdır. nyholm/psr7 hazır, laminas-httphandlerrunner hedef pakettir (brain §4A).

**S: Bu dosya ile l1 csrf/csp dokümanları çelişir mi?**
C: Hayır — burası örnek-desen (hedef), l1 dosyaları gerçek kod eşleştirmeli. Çelişki göründüğünde §12 tablosu kazanan tarafı söyler: gerçek kod üstün.

**S: Middleware birim testleri nerede?**
C: PLANNED — shared/tests/ altyapısı hazır (phpunit ^10.5); §12'de IMPLEMENTED 4 sınıf için test iskeletleri l1 index §34 senaryolarından türetilir. Test yazımı Faz 2f (07-security) ile paralel planlanır.

---

---

## 19. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 7.0.0 |
| **ADR Uyumlu** | ✅ 008, 010, 011, 012, 013, 022 |
| **Kod Karşılığı** | §12 — 4 IMPLEMENTED / 1 KISMEN / 5 PLANNED |
| **Zero Hallucination** | ✅ (hedef-desen etiketi açık) |
| **Kod Hatası Düzeltmesi** | ✅ çift implements (satır 268) |
| **Cross-Reference** | ✅ 6 referans + §12 tablo |
| **Örnek-Desen Etiketi** | ✅ §12 — JWT/rate:/Bypass farkları belgeli |
| **Kod Hatası** | ✅ düzeltildi (çift implements — v7.0.0) |
| **Diagnostics** | §14 — 5 komut (JWT yokluğu teyidi dahil) |
| **Risk Kaydı** | §17 — 5 kalem |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode

---

---

## 12. Örnek Kod ↔ Gerçek Kod Eşleştirmesi (Faz 2c — 2026-09-08)

**Truth Mode ayrımı:** §4'teki sınıflar prompt arşivi **hedef desenleridir**. Gerçek üretim kodu:

| Pipeline # | Bu Dosyadaki Örnek | Gerçek Kod | Fark |
|-----------|--------------------|-----------|------|
| #3 RateLimiter | `rate:{ip}:{window}` + doğrudan `apcu_*` | `CacheRateLimiter` (`rl:` prefix, IRateLimiter) + `RateLimiterMiddleware` (93s) | Önek ve soyutlama farklı — gerçek ADR-007 namespace'li |
| #4 SecurityHeaders | `$_SESSION['csp_nonce']` + dizi header | `_csp_nonce` + `buildCsp()` | Gerçek: tek metod üretim+gönderim |
| #5 SessionManager | `SessionManagerMiddleware` sınıfı | `SessionInitializer::ensureStarted()` (52s) | Sınıf adı/konum farklı — davranış aynı (`COREMUSIC_SESS`) |
| #6 Csrf | `CSRF_TOKEN_MISSING/INVALID` JSON | IMiddleware sözleşmeli `CsrfMiddleware` + bypass `set-gender` | Davranış aynı; bypass örnekte yok |
| #8 Auth | JWT RS256 decode (`Firebase\JWT`) | `MM_UserID/MM_UserRole` session okuma → `_auth` | **Kritik fark: JWT gerçek kodda YOK** — hedef desen |
| #7 BypassAuth | `?_bypass=1` + `user_id=1` | `TEST_MODE`/`FORCE_AUTH_BYPASS` sabitleri (guard-pipeline §4) | Mekanizma farklı |

**JWT notu:** §4.4 örneği RS256 access/refresh politikası içerir (15dk/7gün/90gün rotasyon) — bu **hedef mimaridir**; gerçek AuthMiddleware session tabanlıdır. JWT PLANNED etiketi geçerlidir ([[../l1-security/index]] §21). `Firebase\JWT` paketi de yasaklı listededir (brain §4A: `lcobucci/jwt` hedef).

---

## 13. Pipeline Tamamlanma Oranı

| Durum | Sayı | Bileşenler |
|-------|------|------------|
| IMPLEMENTED | 4 | RateLimiter, SecurityHeaders, Csrf, Auth |
| KISMEN | 1 | SessionManager (SessionInitializer köprüsü mevcut) |
| PLANNED | 5 | OriginCheck, Cors, BypassAuth, Permission, Validation |
| **Oran** | **%40 kod** | 4/10 + 0.5 |

`respect/validation ^2.0` (Validation için) ve `nyholm/psr-7` (PSR-15 tabanı) composer'da hazırdır — bağımlılık varlığı ≠ implementasyon.

---

## 14. Pipeline Diagnostics

```powershell
# 1. Gerçek middleware sınıfları (beklenen: 4)
Get-ChildItem -LiteralPath "shared\src\Middleware" -Filter "*.php" | Select-Object Name

# 2. IMiddleware implements taraması
Select-String -LiteralPath "shared\src\Middleware\*.php" -Pattern "implements IMiddleware"

# 3. Prefix karşılaştırma (beklenen: rl: — 'rate:' örnek kodda kalır)
Select-String -LiteralPath "shared\src\Middleware\RateLimiterMiddleware.php" -Pattern "rl:|rate:"

# 4. Session adı (beklenen: COREMUSIC_SESS)
Select-String -LiteralPath "shared\src\Session\SessionInitializer.php" -Pattern "COREMUSIC_SESS"

# 5. JWT yokluğu teyidi (beklenen: 0 sonuç)
Get-ChildItem -LiteralPath "shared\src\Middleware" -Recurse -Include "*.php" | Select-String -Pattern "Firebase|JWT" -ErrorAction SilentlyContinue
```

---

## 15. Sözlük Ek

| Terim | Tanım |
|-------|-------|
| **Runner** | Pipeline'ı tersine sarıp çalıştıran yapı (§3 array_reduce deseni) |
| **Handler** | Pipeline sonundaki terminal çağrı (controller) |
| **Hedef Desen** | Prompt arşivinden gelen örnek kod — üretim kanıtı DEĞİLDİR |
| **MM_* Sözleşme** | Session anahtar standardı — gerçek auth taşıyıcısı |
| **`rl:` Prefix** | Gerçek rate limit namespace'i (ADR-007) |

---

## 16. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 6.0.0 | 2026-08-09 | Pipeline dokümanı (prompt arşivi tabanlı) |
| 7.0.0 | 2026-09-08 | Faz 2c: §12 örnek↔gerçek eşleştirme (JWT PLANNED netleşti); §13 tamamlanma oranı; §14 diagnostics; kod hatası düzeltildi (çift implements) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
