---
node_id: K009-security/rate-limiting/rate-limiting-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/rate-limiting/rate-limiting-anayasa
evidence:
  - shared/src/Middleware/RateLimiterMiddleware.php:20-30
  - shared/src/Middleware/RateLimiterMiddleware.php:53-100
  - shared/src/Security/CacheRateLimiter.php:16-35
  - shared/src/Api/Middleware/RateLimitMiddleware.php:34-68
  - auth.coremusic.net/include/Service/AuthService.php:59-92
  - auth.coremusic.net/include/Controller/AuthController.php:58-62
  - shared/tests/Middleware/RateLimiterMiddlewareTest.php:52-127
validation:
  - AD-01..AD-08 (rate-limiting-dogrulama.md)
open_questions:
  - set() → isNew varsayımı runtime test edilmedi ⚠️
group: kod
---

# K009 · rate-limiting · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kod katmanı — gerçek dosyalardan birebir alıntılar

Tüm alıntılar 2026-10-15 tarihinde diskten okunmuştur; **uydurma kod
yoktur**. Her blok Dosya·Satır referansı taşır.

## BAĞLANTILAR

| Link | Ne verir |
|---|---|
| [[rate-limiting-anayasa]] | Alıntılardaki kararların gerekçesi |
| [[rate-limiting-surucu]] | Akış adımları |
| [[rate-limiting-bilesenler]] | Alıntısı verilen sınıfların envanteri |
| [[rate-limiting-dogrulama]] | Bu kanıtların AD-xx kontrol karşılığı |
| [[authentication-ornekler]] | Auth tarafında sayaç kullanım örnekleri |
| [[security-master]] | K009 güvenlik düğümü ana sayfası |

---

## 1. Fail-closed yol listesi ve depo hatası dalı

**Dosya:** `shared/src/Middleware/RateLimiterMiddleware.php` · **Satır:** 20-30, 53-68

```php
public const AUTH_FAIL_CLOSED_PATHS = [
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/logout',
    '/set-gender',
    '/select-gender',
    '/session',
    '/validate-key',
];
```

Depo (`CacheManager`) çözülemezse iki kollu karar — **satır 53-68**:

```php
$cache = $this->cache ?? $this->resolveCacheAdapter();
if ($cache === null) {
    $path = $this->resolveRequestPath($request);
    if (in_array($path, $this->failClosedPaths, true)) {
        // ADR-013 §5.4 şart 1a: auth uçları fail-closed.
        \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_unavailable', ['path' => $path, 'mode' => 'fail-closed']);
        return [
            'httpStatus' => 503,
            'type'       => 'json',
            'body'       => ['error' => 'rate_limiter_unavailable'],
            'headers'    => ['Retry-After' => (string)$this->windowSeconds],
            'halt'       => true,
        ];
    }
    // ADR-013 §5.4 şart 1a: genel uçlar fail-open — ama gözlemlenebilir olmalı.
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_unavailable', ['path' => $path, 'mode' => 'fail-open']);
    return $next($request);
}
```

**Okuma notu:** Her iki dal da `securityEvent` ile loglanır → fail-open
**gözlemlenebilir** (kör değildir). Bu, anayasadaki veto sorusunun
kanıt zeminidir: davranış logsuz değil, kayıtlıdır.

---

## 2. Sayaç çekirdeği: set → isNew → increment ⚠️

**Dosya:** `shared/src/Middleware/RateLimiterMiddleware.php` · **Satır:** 71-100

```php
$ip  = $this->resolveClientIp($request);
$key = self::CACHE_KEY_PREFIX . md5($ip);

$isNew = $cache->set($key, 1, $this->windowSeconds);
if ($isNew) {
    return $next($request);
}

$count = $cache->increment($key, 1);
if ($count === false) {
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limiter_increment_failed', ['path' => $this->resolveRequestPath($request)]);
    $cache->set($key, 1, $this->windowSeconds);
    return $next($request);
}

if ((int)$count > $this->maxRequests) {
    // P5-32: 429 artık gözlemlenir (öncekte hiç loglanmıyordu).
    \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('rate_limited', [
        'ip'   => $ip,
        'path' => $this->resolveRequestPath($request),
        'code' => 429,
    ]);
    return [
        'httpStatus' => 429,
        'type'       => 'json',
        'body'       => ['error' => 'rate_limit_exceeded'],
        'headers'    => ['Retry-After' => (string)$this->windowSeconds],
        'halt'       => true,
    ];
}

return $next($request);
```

**Üç kritik gözlem (⚠️ işaretli olanlar runtime doğrulaması bekler):**

1. `$isNew = $cache->set(...)` — `set()` dönüşü "anahtar yeni mi" diye
   yorumlanıyor. `MemoryAdapter::set()` daima `true` dönerse `increment`
   yolu **hiç çalışmaz** → sayaç her istekte `1`'e sıfırlanır →
   **pratik fail-open** ⚠️ VERIFICATION REQUIRED.
2. `increment === false` (depo increment desteklemiyorsa/hata verirse)
   → istek **izin alır** (fail-open) + log. Test bu dala sahiptir
   (bkz. §7 `testCacheIncrementFailureAllowsRequest`).
3. Eşik testi **strict greater-than**: `(int)$count > $this->maxRequests`
   → tam eşik değeri (60/60) henüz **geçilmez**, 61. istekte 429 alınır.

---

## 3. IRateLimiter uygulaması: CacheRateLimiter

**Dosya:** `shared/src/Security/CacheRateLimiter.php` · **Satır:** 16-40

```php
public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $cacheKey = self::KEY_PREFIX . $key;
    $count = $this->cache->get($cacheKey);
    if ($count === false || $count === null) {
        return false;
    }
    return (int)$count >= $maxAttempts;
}

public function increment(string $key, int $windowSeconds): void
{
    $cacheKey = self::KEY_PREFIX . $key;
    $existing = $this->cache->get($cacheKey);
    if ($existing === false || $existing === null) {
        $this->cache->set($cacheKey, 1, $windowSeconds);
    } else {
        $this->cache->increment($cacheKey, 1);
    }
}

public function reset(string $key): void
{
    $this->cache->delete(self::KEY_PREFIX . $key);
}
```

**Okuma notu:** `CacheRateLimiter` `set()`'in dönüş değerini **kullanmaz** —
`get()` ile varlık kontrolü yapar. Yani §2'deki `$isNew` varsayımı yalnız
`RateLimiterMiddleware`'e özgüdür; servis katmanı (auth) bu tuzağa düşmez.
Eşik karşılaştırması `>=` (middleware'deki `>`'den farklı!) → servis
katmanı **eşik değerinde de** kilitler.

---

## 4. API katmanı: anahtar şeması ve 429

**Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 34-52

```php
public function __invoke(array $request, callable $next): array
{
    $key = $this->getRateLimitKey();

    // Check if rate limited
    if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
        header('Retry-After: ' . $this->windowSeconds);
        return ApiResponse::error(
            'RATE_LIMIT_EXCEEDED',
            'Too many requests',
            429
        );
    }

    // Increment rate limit counter
    $this->rateLimiter->increment($key, $this->windowSeconds);

    return $next($request);
}
```

**Dosya:** aynı · **Satır:** 57-68 (anahtar üretimi)

```php
private function getRateLimitKey(): string
{
    // Use API key if available, otherwise use IP
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
    if ($apiKey !== null) {
        return 'api:' . hash('sha256', $apiKey);
    }

    // Use IP address
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return 'ip:' . $ip;
}
```

**Okuma notu:**

- API anahtarı `sha256` ile hash'lenerek kota anahtarına girer → ham API
  anahtarı cache anahtarında **tutulmaz**.
- `X-Api-Key` yoksa `ip:` ön eki + `REMOTE_ADDR`. `X-Forwarded-For`
  **okunmaz** → API katmanında proxy arkası IP çözümü yoktur ⚠️ (HTTP
  middleware'den farklı davranış).
- Sıralama: **önce `isLimited`, sonra `increment`** → her geçerli istek
  sayılır (başarısızlık beklemez; API'de tüm istekler sayılır).

---

## 5. Auth servisi: iki boyutlu sayaç

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 59-79

```php
$failedKey   = self::LOGIN_RATE_KEY_PREFIX . $request->clientIp;
// B-F-25: kimlik bazlı ikinci sayaç — parola-sprey (çok hesap/tek IP) ve
// dağıtık tek-hesap saldırısı için iki boyutlu limit. SHA-256 bucket'ı
// geri-dönüştürülemez; enumeration sinyali vermez (var olmayan kimlik de sayılır).
$identityKey = self::LOGIN_RATE_KEY_PREFIX . 'id:' . hash('sha256', strtolower($request->identity));

if ($this->rateLimiter->isLimited($failedKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
    || $this->rateLimiter->isLimited($identityKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
) {
    throw RateLimitException::loginRateLimited(self::LOGIN_WINDOW_SECONDS);
}

if ($request->identity === '' || $request->password === '') {
    throw ValidationException::emptyFields();
}

$row = $this->userRepository->findByCredential($request->identity);
if ($row === null) {
    $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
    $this->rateLimiter->increment($identityKey, self::LOGIN_WINDOW_SECONDS);
    throw AuthenticationException::invalidCredentials();
}
```

**Eşik sabitleri (aynı dosya, satır 31-41):**

```php
private const MAX_LOGIN_ATTEMPTS = 5;
private const LOGIN_WINDOW_SECONDS = 900;
private const MAX_REGISTER_ATTEMPTS = 3;
private const REGISTER_WINDOW_SECONDS = 3600;
private const PASSWORD_RESET_MAX_ATTEMPTS = 3;
private const PASSWORD_RESET_WINDOW_SECONDS = 3600;
```

**Okuma notu:**

- Sıralama: `isLimited` (satır 65-66) **önce**, `increment` (77-78)
  **sonra** → doğru sıra.
- `hash('sha256', strtolower(...))` → kimlik sayaç anahtarı geri
  döndürülemez (enumeration sinyali yok).
- Yalnız **başarısızlık** `increment` tetikler (kullanıcı yok: 77-78;
  parola hatalı: 90-92). Doğru giriş sayaç yazmaz → masum kullanıcı
  kendi başarısızlıklarını aşmadıkça kilitlenmez.

---

## 6. Controller: RateLimitException → 429

**Dosya:** `auth.coremusic.net/include/Controller/AuthController.php` · **Satır:** 11, 58-62

```php
use CoreMusic\Exception\RateLimitException;
// ...
    $e instanceof RateLimitException => [
        'httpStatus' => 429,
        // ...
        'headers'    => ['Retry-After' => (string)$e->getRetryAfter()],
```

**Okuma notu:** `Retry-After` değeri istisnanın içinden gelir
(`getRetryAfter()`), middleware'deki gibi sabit `windowSeconds` değil
istisna üretildiği pencereyi taşır. `RateLimitException` tanımı:
`shared/src/Exception/RateLimitException.php`.

---

## 7. Test kanıtları (birebir)

**Dosya:** `shared/tests/Middleware/RateLimiterMiddlewareTest.php`

**Satır 52-73 — eşik aşımı 429 + Retry-After:**

```php
public function testRateLimitExceededReturns429(): void
{
    // ...
    $this->assertSame(429, $result['httpStatus']);
    $this->assertSame('rate_limit_exceeded', $result['body']['error']);
    $this->assertSame('60', $result['headers']['Retry-After']);
    $this->assertTrue($result['halt']);
```

**Satır 105-127 — increment hatası fail-open:**

```php
public function testCacheIncrementFailureAllowsRequest(): void
{
    // ...
    $this->assertTrue($coreCalled, 'Cache failure should allow request (fail-open)');
```

**Satır 159-176 — cache yoksa fail-open (genel uç):**

```php
public function testNoCacheAvailableAllowsRequest(): void
{
    // ...
    $this->assertTrue($coreCalled, 'No cache should allow request');
```

**Diğer testler (ad, satır):**

| Test | Satır | Ne kanıtlar |
|---|---|---|
| `testFirstRequestAlwaysAllowed` | 23-45 | İlk istek geçer |
| `testRateLimitExactlyAtMaxAllowed` | 76-98 | Tam eşik değeri hâlâ geçer (`>` sınırı) |
| `testCacheIncrementNullAllowsRequest` | 130-152 | `increment` null → fail-open |
| `testClientIpFromRemoteAddr` | 183-207 | Anahtar `rl:` öneki + REMOTE_ADDR |
| `testTrustedProxyXForwardedFor` | 210-240 | Trusted proxy'de XFF okunur |
| `testUntrustedProxyUsesRemoteAddr` | 243-272 | Trusted olmayan proxy'de XFF **yok sayılır** |
| `testCustomMaxRequests` | 279-298 | Özel eşik + `Retry-After: 30` |

> ⚠️ Test listesi 10 adettir; `CacheRateLimiter` (servis katmanı) için
> **ayrı test dosyası görülmemiştir** → test boşluğu.

---

## 8. API pipe bağlantısı (bağlantı kanıtı)

**Dosya:** `api.coremusic.net/index.php` · **Satır:** 140-164 (özet okuma)

Pipe, `RateLimitMiddleware`'i `CacheRateLimiter` ile kurar; depo seçimi
APCu varsa `ApcuAdapter`, yoksa `MemoryAdapter` şeklindedir (2026-10-15
okuması). Bu seçim, anayasadaki "MemoryAdapter → süreç içi sayaç →
worker recycle'ta kayıp ⚠️" senaryosunun kaynağıdır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
