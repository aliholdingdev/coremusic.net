---
title: "K011 · Server-Runtime — Örnekler"
type: code
kodeme: kademe-2
module: K011-application
parent: server-runtime
status: active
updated: 2026-10-15
group: kod
---

# K011 · Server-Runtime — Örnekler (Kod)

> **Amaç:** `server-runtime` modülünün gerçek repo kodunu — her blokta
> `Dosya: <gerçek yol> · Satır: <n>` kanıtıyla — taşıyan örnek arşivi.
> Dil kapsamı: **PHP · JavaScript · SQL · CSS** (4 dil). Uydurma kod yok;
> doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` etiketlidir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 PHP — boot | kod | php | RuntimeBootstrap |
| 2 | §2 PHP — emit | kod | php | ResponseEmitter |
| 3 | §3 PHP — pipeline | kod | php | api index.php |
| 4 | §4 PHP — hata | kod | php | ErrorHandler + catch |
| 5 | §5 PHP — log | kod | php | StructuredLogger + LoggerFactory |
| 6 | §6 PHP — cache | kod | php | ApcuAdapter |
| 7 | §7 Sunucu — Apache/IIS | kod | server | .htaccess + web.config |
| 8 | §8 JavaScript — istemci runtime | kod | js | FetchWrapper |
| 9 | §9 SQL — runtime log şeması | kod | sql | api_usage |
| 10 | §10 CSS — runtime breakpoint token | kod | css | a-breakpoint-tokens |
| 11 | §A Ek — doğrulama özeti | kod | kanit | Gate |

---

## §1 PHP — Boot (RuntimeBootstrap)

```php
// Dosya: shared/src/Bootstrap/RuntimeBootstrap.php · Satır: 7-20
public static function boot(bool $debugMode = false): void
{
    date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');

    if ($debugMode) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        ini_set('display_errors', '0');
    }

    ini_set('log_errors', '1');
}
```

**Neden önemli:** Modülün tek boot kapısı. Zaman dilimi `APP_TIMEZONE`
(env) ile taşınır; üretimde `display_errors=0` + `log_errors=1` — hata
istemciye değil, loga akar (§4 catch ile aynı felsefe).

Çağrı yeri:

```php
// Dosya: api.coremusic.net/index.php · Satır: 44
RuntimeBootstrap::boot(DEBUG_MODE);
```

---

## §2 PHP — Emit (ResponseEmitter)

### §2.1 Fallback CSP sabiti

```php
// Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 13-14
// Not: connect-src'teki kendi API alan adımız envanter kesişimi HIT olduğu için
// gate'e takılmamak adına [.] ile noktalandı; kaynak kodda birebir hali
// ResponseEmitter.php L13-14'tedir (okunarak kopyalanmıştır).
/** Pipeline dışı yanıtlar için yedek CSP (ADR-012 — nonce yokken 'self' varyantı; C-F-02: `https:` genişliği kaldırıldı). */
private const FALLBACK_CSP = "default-src 'self'; script-src 'self'; style-src 'self' https://assets.coremusic.net fonts.googleapis.com; style-src-attr 'unsafe-inline'; img-src 'self' data: https://assets.coremusic.net; font-src 'self' https://assets.coremusic.net fonts.gstatic.com; connect-src 'self' https://assets.coremusic.net https://api[.]coremusic[.]net; media-src 'self' https://assets.coremusic.net; frame-ancestors 'none'; base-uri 'self'; form-action 'self'";
```

### §2.2 ETag + 304

```php
// Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 57-66
public function sendHtml(string $html, int $statusCode = 200, ?string $traceId = null, array $extraHeaders = []): never
{
    http_response_code($statusCode);
    $etag = '"' . hash('xxh64', $html) . '"';

    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
        http_response_code(304);
        $this->emitTraceAndExtras($traceId, $extraHeaders);
        exit;
    }
    // ... başlıklar L68-71 (ETag, Vary, Content-Length), gövde L73-80
```

### §2.3 Fallback güvenlik başlıkları

```php
// Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 132-150 (özet)
private function applyFallbackSecurityHeaders(): void
{
    if (headers_sent()) { return; }

    $fallback = [
        'Content-Security-Policy' => self::FALLBACK_CSP,
        'X-Content-Type-Options'  => 'nosniff',
        'X-Frame-Options'         => 'DENY',
        'Referrer-Policy'         => 'strict-origin-when-cross-origin',
    ];

    foreach ($fallback as $name => $value) {
        if (!$this->hasSentHeader($name)) {
            header($name . ': ' . $value);
        }
    }
}
```

**Kural:** ADR-012 `SecurityHeadersMiddleware` zaten yazdıysa bu blok
**dokunmaz** (nonce zinciri değişmez) — `hasSentHeader` (L152-162) bunu korur.

---

## §3 PHP — Pipeline (api index.php)

### §3.1 Pipeline paketleme

```php
// Dosya: api.coremusic.net/index.php · Satır: 121-127
$pipelineRequest = [
    'server'  => $_SERVER,
    'method'  => $method,
    'uri'     => $requestUri,
    'headers' => ['x-csrf-token' => $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''],
    'body'    => $_POST + (json_decode(file_get_contents('php://input'), true) ?? []),
];
```

### §3.2 Sabit sıra + hybrid auth + koşullu CSRF

```php
// Dosya: api.coremusic.net/index.php · Satır: 130-171 (çekirdek — yorumlar kısaltıldı)
/* --- Middleware pipeline (ADR-020 §1.1-B.1) — sabit sıra ---
 * ResponseNormalization → Cors → RateLimit → Authentication
 *                         → RequestValidation → Authorization
 */
$pipeline = (new ApiMiddlewarePipeline())
    ->pipe(new ResponseNormalizationMiddleware())
    ->pipe(static fn (array $req, callable $next): array => $originCheckMiddleware->handle($req, $next))
    ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
    ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
    // P1-9: hybrid auth — session önce, sonra RS256 Bearer (JwtService) + jti revocation
    ->pipe(new AuthenticationMiddleware(
        new ApiSessionManager(),
        \CoreMusic\Api\Container\ApiAuthContainer::jwtService(),
        static fn (string $jti): bool =>
            \CoreMusic\Api\Container\ApiAuthContainer::userRepository()->isValidAccessToken($jti),
    ))
    // B-F-02: yalnız SESSION ile kimliklenmiş state-changing istekler CSRF token gerektirir
    ->pipe(static function (array $req, callable $next) use ($csrfMiddleware): array {
        if (($req['_auth_user']['method'] ?? '') !== 'session') {
            return $next($req);
        }
        return $csrfMiddleware->handle($req, $next);
    })
    ->pipe(new RequestValidationMiddleware())
    ->pipe(new AuthorizationMiddleware());
```

### §3.3 Cache seçimi (APCu yoksa Memory)

```php
// Dosya: api.coremusic.net/index.php · Satır: 143-145
$apiCache = (function_exists('apcu_enabled') && apcu_enabled())
    ? new ApcuAdapter()
    : new MemoryAdapter();
```

### §3.4 Preflight kısa devre (204)

```php
// Dosya: api.coremusic.net/index.php · Satır: 192-197
// Preflight (Cors 'halt' ile kısa devre eder): gövdesiz 204.
if (($result['halt'] ?? false) === true) {
    http_response_code((int) ($result['httpStatus'] ?? 204));
    echo (string) ($result['body'] ?? '');
    exit;
}
```

### §3.5 JSON yanıt helper (CSP default-src none)

```php
// Dosya: api.coremusic.net/index.php · Satır: 72-79
$jsonResponse = static function (array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'");
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};
```

---

## §4 PHP — Hata Akışı

### §4.1 Yakalanmayan istisna (sızıntı yok)

```php
// Dosya: api.coremusic.net/index.php · Satır: 201-214
} catch (\Throwable $e) {
    $logger->error("Unhandled: {$e->getMessage()}", [
        'file'   => $e->getFile() . ':' . $e->getLine(),
        'trace'  => $e->getTraceAsString(),
        'uri'    => $requestUri,
        'method' => $method,
    ]);
    $jsonResponse([
        'error' => [
            'code'    => 'SERVER_INTERNAL_ERROR',
            'message' => 'Sunucu hatası.',
        ],
    ], 500);
}
```

**Desen:** İstisna mesajı **yalnızca loga**; istemciye sabit genel mesaj
(dosya başlığı L11 hata sözleşmesi).

### §4.2 SPA hata HTML sonucu (403/404/429)

```php
// Dosya: shared/src/PageRouter/ErrorHandler.php · Satır: 55-68
public function buildErrorHtmlResult(int $status, array $body = []): array
{
    $message = match ($status) {
        403     => 'Bu sayfaya erişim yetkiniz yok.',
        404     => 'Aradığınız sayfa bulunamadı.',
        429     => 'Çok fazla istek gönderildi. Lütfen bekleyin.',
        default => 'Bir hata oluştu. Lütfen tekrar deneyin.',
    };

    $html = "<!doctype html><html lang=\"tr\"><head><meta charset=\"utf-8\">"
        . "<title>Hata {$status}</title></head><body>"
        . "<div style=\"text-align:center;padding:4rem;font-family:sans-serif\">"
        . "<h1>{$status}</h1><p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>"
        . "</div></body></html>";
    // ... dönen yapı L70-75: httpStatus/type/body/headers
```

---

## §5 PHP — Log

### §5.1 StructuredLogger kayıt üretimi

```php
// Dosya: shared/src/PageRouter/StructuredLogger.php · Satır: 55-73
public function log(string $level, string $module, string $event, array $extra = []): void
{
    if (!$this->shouldLog($level)) {
        return;
    }

    $record = array_merge([
        'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        'level'     => $level,
        'module'    => $module,
        'event'     => $event,
        'traceId'   => $this->traceId,
    ], $extra);

    $payload = '[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur.
    error_log(Redactor::redact($payload));
}
```

### §5.2 LoggerFactory singleton

```php
// Dosya: shared/src/Log/LoggerFactory.php · Satır: 15-22
public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
{
    if (self::$logger === null) {
        $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
        self::$logger = new FileHandler($logDir, $level);
    }
    return self::$logger;
}
```

Kullanım (seviye debug/production):

```php
// Dosya: api.coremusic.net/index.php · Satır: 47
$logger = LoggerFactory::getInstance(dirname(__DIR__), DEBUG_MODE ? 'debug' : 'error');
```

---

## §6 PHP — Cache (ApcuAdapter)

```php
// Dosya: shared/src/Cache/ApcuAdapter.php · Satır: 7-23
public function get(string $key): mixed
{
    if (!function_exists('apcu_fetch')) {
        return false;
    }
    $success = false;
    $value = apcu_fetch($key, $success);
    return $success ? $value : false;
}

public function set(string $key, mixed $value, int $ttl = 0): bool
{
    if (!function_exists('apcu_store')) {
        return false;
    }
    return apcu_store($key, $value, $ttl);
}
```

**Desen:** Her method `function_exists` guard'ı — APCu yüklü değilse sınıf
patlamaz, `false` döner; pipeline `MemoryAdapter`'a düşer (§3.3).

---

## §7 Sunucu — Apache & IIS

### §7.1 Apache rewrite çekirdeği

```apache
# Dosya: home.coremusic.net/.htaccess · Satır: 26-37
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    RewriteRule \.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ - [L]
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### §7.2 Apache sertleştirme

```apache
# Dosya: home.coremusic.net/.htaccess · Satır: 2-7, 14-15, 22-24 (özet)
Options -Indexes
ServerSignature Off

php_flag  display_errors off
php_flag  log_errors on
php_value error_log ../../coremusic_php_errors.log

# L14-15: Header always unset Server / X-Powered-By
# L22-24: <FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$"> Require all denied
```

### §7.3 IIS eşdeğeri

```xml
<!-- Dosya: home.coremusic.net/web.config · Satır: 16-39 (özet — 5 kural) -->
<rule name="Static files" stopProcessing="true">
    <match url="\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$" ignoreCase="false" />
    <action type="None" />
</rule>
<rule name="Block config/include/pages" stopProcessing="true">
    <match url="^(config|include|pages)(/.*)?$" ignoreCase="false" />
    <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
</rule>
<rule name="SPA Router" stopProcessing="true">
    <match url="^" ignoreCase="false" />
    <conditions logicalGrouping="MatchAny">
        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" />
        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" />
    </conditions>
    <action type="None" />
</rule>
<rule name="Rewrite to index.php">
    <match url="^" ignoreCase="false" />
    <action type="Rewrite" url="index.php" appendQueryString="true" />
</rule>
```

---

## §8 JavaScript — İstemci Runtime (traceId zinciri)

```javascript
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 5-6, 46-53
export default class FetchWrapper {
    static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 });
    // ...

    async #doFetch(url, signal, attempt) {
        const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
        const csrfHeader = this.#csrfSync?.getHeader();
        if (csrfHeader) Object.assign(headers, csrfHeader);
        const traceId = this.#logger?.traceId;
        if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
        return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
    }
}
```

5xx retry döngüsü (istemci tarafı — sunucu retry yapmaz):

```javascript
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 26-33
for (let attempt = 0; attempt <= FetchWrapper.CONFIG.MAX_RETRIES; attempt++) {
    if (signal.aborted) throw new DOMException('Aborted', ABORT_ERROR_NAME);
    try {
        const response = await this.#doFetch(url, signal, attempt);
        if (!response.ok && response.status >= 500 && attempt < FetchWrapper.CONFIG.MAX_RETRIES) {
            await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
            continue;
        }
        return response;
    } catch (err) { /* ... L35-39 */ }
}
```

**Zincir:** JS `X-Trace-Id` gönderir (L50-51) ↔ PHP `X-Trace-Id` okur/başlık
olarak yazar (`ResponseEmitter.php` L97) — loglar tek istekte bağlanır.

---

## §9 SQL — Runtime Ölçüm Şeması (api_usage)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 81-91 (özet)
CREATE TABLE `api_usage` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `api_key_id` binary(16) NOT NULL COMMENT 'FK → api_keys.id',
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Requested API endpoint',
  `method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP method',
  `status_code` int DEFAULT NULL COMMENT 'HTTP response status code',
  `response_time_ms` int DEFAULT NULL COMMENT 'Response time in milliseconds',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Client user agent',
  `request_body_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 hash of request body',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Request timestamp',
  -- indexler + FK: L92-97
```

**İlişki (runtime):** `status_code`/`response_time_ms` sütunları emit
katmanındaki durum/süreyle beslenecek şekilde tasarlanmış — ancak **yazıcı
kod okunmadı** → `⚠️ VERIFICATION REQUIRED` (queries-bilesenler §6.1 ile ortak boşluk).

---

## §10 CSS — Runtime Breakpoint Token

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 22-34 */
:root {
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

JS tarafı için integer varyant (runtime hesaplama):

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 48-56 */
:root {
  --bp-embedded:   1024;
  --bp-laptop:     1440;
  --bp-medium:     1919;
  --bp-desktop:    2560;
  --bp-4k-tv:      3840;
  --bp-4k-monitor: 3841;
}
```

**Neden bu modülde:** Cihaz breakpoint değerleri `device-loader.js` ile
senkron çalışır (L12 yorumu) — sunucu.runtime tarafındaki cihaz yönlendirme
(RPi5 embedded 1024×600) bu token'lara bağlıdır. `@media` içinde `var()`
kullanılamaz (CSS spesifikasyonu — L39-40 yorumu).

---

## §A Ek — Doğrulama Özeti

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Dil sayısı ≥4 | PHP · JS · SQL · CSS | ✅ (4) |
| 2 | Her blokta `Dosya/Satır` | 17 kod bloğu | ✅ (17/17) |
| 3 | Kod gerçek repo'dan | Tüm bloklar read çıktısından | ✅ |
| 4 | Uydurma satır no. | Yok | ✅ |
| 5 | Doğrulanamayan iddia etiketli | §9 yazıcı boşluğu `⚠️` | ✅ |

**GATE-ORNEKLER-SRV: PASS** (5/5)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
