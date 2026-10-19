---
title: "K011 · caching — Kod Örnekleri"
type: architecture
kodeme: kademe-2
module: K011-application
parent: caching
status: active
updated: 2026-10-15
group: kod
---

# caching — Kod Örnekleri (çok dilli)

> **Kural:** Her blok **gerçek repo kodundan** türetilmiştir; `Dosya:` +
> `Satır:` kanıtı zorunludur. Uydurma/pseudo-code yasak (zero-hallucination).
> Diller: **PHP · Vanilla JS · SQL · CSS/sunucu-yapılandırması**. Bu dosyanın
> havuz URL'i: `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/ETag`
> (§1'de tek kullanım — cross-folder tekrar YASAK, spec §1.6).
> Repo'da olmayan kod **üretilmez**; eksik alan §16 PLANNED boşluklarıdır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Dil |
|---|---|---|---|---|
| 1 | §1 ETag + 304 — HTTP yeniden doğrulama (havuz URL'i) | kod | etag | PHP |
| 2 | §2 Cache kontratı ve adaptör seçimi | kod | kontrat | PHP |
| 3 | §3 Bellek-içi adaptör (TTL davranışı) | kod | ttl | PHP |
| 4 | §4 APCu adaptörü ve zarif düşüş | kod | fallback | PHP |
| 5 | §5 Sayfa cache'i (auth ayrımı + remember) | kod | sayfa | PHP |
| 6 | §6 Rate limiter — cache sayaçları | kod | limit | PHP |
| 7 | §7 Rate limiter middleware — fail-closed/fail-open | kod | kapi | PHP |
| 8 | §8 Bağılantı noktasında adaptör seçimi (composition root) | kod | wiring | PHP |
| 9 | §9 SPA cache katmanı — TTL + LRU | kod | istemci | JS |
| 10 | §10 Prefetch kuyruğu | kod | istemci | JS |
| 11 | §11 Router ↔ cache bağlantısı | kod | istemci | JS |
| 12 | §12 DB tarafı — rate limit tablosu | kod | sema | SQL |
| 13 | §13 Statik varlık TTL + cache-buster | kod | statik | CSS/Conf |
| 14 | §14 Diller arası sözleşme (TTL/key/JSON) | kod | sozlesme | hepsi |
| 15 | §15 Anti-pattern örnekleri (YAPMA) | kanit | anti | hepsi |
| 16 | §16 PLANNED örnekler (kod YOK) | mimari | bosluk | — |
| 17 | §A Açık sorular | kanit | acik | — |
| 18 | §B Kullanım kuralları | mimari | kural | — |

---

## §1 — ETag + 304 — HTTP yeniden doğrulama (havuz URL'i)

> **Kaynak (bu dosyanın tek havuz URL'i):**
> `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/ETag`
> (exa fetch 2026-10-15 — içerik döndü; HTTP status ⚠️ VERIFICATION REQUIRED)
>
> Kaynaktan okunanlar (parafraz): `ETag` bir **response/representation**
> başlığıdır; kaynağın belirli bir **sürümünün kimliği**dir. İçerik
> değiştiyse **yeni** ETag üretilmelidir. Sözdizimi `ETag: "<value>"` (strong)
> veya `ETag: W/"<value>"` (weak); weak etiketler byte-düzeyinde eşdeğerlik
> garanti etmez. `If-None-Match` ile eşleşen strong ETag, gövdesiz `304 Not
> Modified` ile sonuçlanabilir — bant genişliği tasarrufu sağlar.

**Repo karşılığı — ETag üreten tek yüzey:**

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 68-83 (çekirdek)
// (satır içeriği Select-String ile okunmuştur — 2026-10-15)

private function withETag(array $response): array
{
    $etag = md5((string) json_encode($response));   // Satır: 70 — güçlü hash tabanlı etiket
    header('ETag: "' . $etag . '"');                // Satır: 71 — tırnak içinde basılır (strong biçimi)

    $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    if ($ifNoneMatch === '"' . $etag . '"') {       // Satır: 74 — birebir karşılaştırma
        http_response_code(304);                    // Satır: 75
        $response['httpStatus'] = 304;              // Satır: 77
        // 'halt' bayrağı: gövdesiz 304, exit/DIE YOK (pipeline kırılmaz)
    }
    return $response;
}
```

**Koşullu başlıklar (aynı middleware):**

```php
// Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 88-93
// Cache-Control dallanması: cacheable → public+max-age · aksi → no-store+must-revalidate
header("Cache-Control: public, max-age={$cacheTtl}");              // Satır: 91
header('Cache-Control: no-store, no-cache, must-revalidate');      // Satır: 93
```

| İddia | Repo kanıtı | Karar |
|---|---|---|
| ETag yalnız `cacheable` rotada basılır (no-store + ETag çelişkisi yok) | Aynı dosya · Satır: 18 (docblock), 41-43 | ✅ DOĞRULANDI |
| 304 yolu `exit` ile index.php bypass etmez | Test: `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` · Satır: 34-42 (`assertDoesNotMatchRegularExpression('/\b(exit|die)\b/')`) | ✅ DOĞRULANDI |
| ETag değeri gövdenin md5'idir (zayıf hash → weak semantiği) | Satır: 70 | ⚠️ NOT: Kaynak strong/weak ayrımını tarif eder; repo **her zaman strong biçiminde** (`"..."`) bassa da md5 tabanlı olduğu için **semantik olarak weak'e yakın** bir doğrulayıcıdır — If-Range/range-request senaryosu yok |
| `If-None-Match` birden fazla etiket (virgül listesi) desteklenir | Satır: 74 — `===` birebir eşleşme | ❌ DEĞİL — liste/derece-W/ desteği YOK → §16 P-ET1 |

---

## §2 — Cache kontratı ve adaptör seçimi

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Cache/CacheInterface.php · Satır: 5-11
namespace CoreMusic\Cache;

interface CacheInterface
{
    public function get(string $key): mixed;               // Satır: 7  — miss: false
    public function set(string $key, mixed $value, int $ttl = 0): bool; // Satır: 8 — ttl: saniye, 0 = süresiz
    public function delete(string $key): bool;             // Satır: 9
    public function increment(string $key, int $step = 1): int|false;   // Satır: 10 — sayaçlar için
    public function clear(): bool;                         // Satır: 11
    public function has(string $key): bool;                // Satır: 12
}
```

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Cache/CacheManager.php · Satır: 5-26 (çekirdek)
final class CacheManager
{
    private static ?CacheInterface $adapter = null;        // Satır: 7

    public static function getAdapter(): CacheInterface
    {
        if (self::$adapter === null) {
            if (function_exists('apcu_fetch')) {           // Satır: 12 — APCu var mı?
                self::$adapter = new ApcuAdapter();        // Satır: 13
            } else {
                self::$adapter = new MemoryAdapter();      // Satır: 15 — istek-içi fallback
            }
        }
        return self::$adapter;
    }

    public static function setAdapter(CacheInterface $adapter): void  // Satır: 21 — test seam
    {
        self::$adapter = $adapter;
    }
}
```

**Sözleşme notları:** `get()` miss'te `false` döner (PHP `empty()` tuzağı
bilinçli); `set()` TTL'i **saniye** cinsindedir — JS `CacheLayer` ile aynı
birim (§14). `setAdapter()` test seam'idir: `RateLimiterMiddlewareTest` mock
`CacheInterface` enjekte eder (`shared/tests/Middleware/RateLimiterMiddlewareTest.php`
· Satır: 305-307).

---

## §3 — Bellek-içi adaptör (TTL davranışı)

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Cache/MemoryAdapter.php · Satır: 5-30 (çekirdek)
final class MemoryAdapter implements CacheInterface
{
    /** @var array<string, array{value: mixed, expires: int}> */   // Satır: 7
    private array $store = [];

    public function get(string $key): mixed
    {
        if (!isset($this->store[$key])) {
            return false;                                          // miss
        }
        $entry = $this->store[$key];
        if ($entry['expires'] > 0 && $entry['expires'] < time()) { // Satır: 16 — TTL kontrolü
            unset($this->store[$key]);                             // Satır: 17 — lazy eviction
            return false;
        }
        return $entry['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $this->store[$key] = [
            'value'   => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,             // Satır: 27 — 0 = süresiz
        ];
        return true;
    }
}
```

| Davranış | Satır | Yorum |
|---|---|---|
| TTL dolunca **lazy** silme (okuma anında) | 16-18 | Write-through yerine read-time expiry — bellek sızıntısı sınırlı ama `set` ile unutulan anahtarlar temizlenmez |
| `increment` yalnız int değerlerde çalışır | 38-45 | CacheRateLimiter sayaçları için yeterli; string değerlerde `false` |
| Bellek sınırsızlığı yok | 8 | `MAX` sınırı YOK → PLANNED (§16 P-MEM1) |

---

## §4 — APCu adaptörü ve zarif düşüş

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Cache/ApcuAdapter.php · Satır: 5-54 (parçalı)
final class ApcuAdapter implements CacheInterface
{
    public function get(string $key): mixed
    {
        if (!function_exists('apcu_fetch')) {   // Satır: 9  — uzantı yoksa
            return false;                        // Satır: 10 — zarif düşüş
        }
        $value = apcu_fetch($key, $success);     // Satır: 13
        return $success ? $value : false;
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        if (!function_exists('apcu_store')) { return false; }  // Satır: 19
        return apcu_store($key, $value, $ttl);                 // Satır: 22
    }

    // increment → apcu_inc (Satır: 35-38) · clear → apcu_clear_cache (Satır: 43-46)
    // has → apcu_exists (Satır: 51-54)
}
```

**Regresyon kanıtı (giriş noktasında temizlik yasağı):**

```php
<?php declare(strict_types=1);
// Dosya: home.coremusic.net/tests/Unit/Bootstrap/EntryPointCacheSafetyTest.php · Satır: 11, 34-36
// B-F-24 regresyonu: giriş noktasında koşulsuz apcu_clear_cache() çağrısı aranır ve YASAKLANIR.
'apcu_clear_cache',
'B-F-24 regresyonu: giriş noktasında apcu_clear_cache çağrısı ...'
```

Bu test, `CacheManager::getAdapter()` seçiminin (§2) bozulmamasını da dolaylı
korur: her istekte cache'i temizlemek APCu'nun anlamını sıfırlardı.

---

## §5 — Sayfa cache'i (auth ayrımı + remember)

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Cache/PageCacheAdapter.php · Satır: 28-44 (çekirdek)
public function pageKey(string $uri, bool $authenticated): string
{
    $prefix = $authenticated ? 'page_auth_' : 'page_pub_';   // Satır: 30 — oturum ayrımı
    return $prefix . md5($uri);                              // Satır: 31 — anahtar = önek + md5
}

public function remember(string $key, int $ttl, callable $callback): mixed
{
    $cached = $this->cache->get($key);                       // Satır: 36
    if ($cached !== false) {
        return $cached;                                      // Satır: 38 — hit
    }
    $value = $callback();                                    // Satır: 41 — miss: hesapla
    $this->cache->set($key, $value, $ttl);                   // Satır: 42 — yaz
    return $value;
}
```

`PageRouter` bu adaptörü bağımlılık olarak alır:

- `shared/src/PageRouter/PageRouter.php` · Satır: 7 (`use PageCacheInterface`),
  21 (ctor `?PageCacheInterface`), 30-32 (`getPageCache()`).

HTTP katmanındaki `Vary: Cookie` ile bu dosyadaki `page_auth_/page_pub_`
ayrımı **aynı problemi iki seviyede** çözer (bkz. `caching-web-arastirma.md` §7).

---

## §6 — Rate limiter — cache sayaçları

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Security/CacheRateLimiter.php · Satır: 8-40 (tam metin özet)
final class CacheRateLimiter implements IRateLimiter
{
    private const KEY_PREFIX = 'rl:';                                  // Satır: 10

    public function __construct(private readonly CacheInterface $cache) {}

    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $cacheKey = self::KEY_PREFIX . $key;                           // Satır: 18
        $count = $this->cache->get($cacheKey);                         // Satır: 19
        if ($count === false || $count === null) { return false; }     // Satır: 20-22
        return (int)$count >= $maxAttempts;                            // Satır: 23
    }

    public function increment(string $key, int $windowSeconds): void
    {
        $cacheKey = self::KEY_PREFIX . $key;                           // Satır: 28
        $existing = $this->cache->get($cacheKey);                      // Satır: 29
        if ($existing === false || $existing === null) {
            $this->cache->set($cacheKey, 1, $windowSeconds);           // Satır: 31 — ilk istek
        } else {
            $this->cache->increment($cacheKey, 1);                     // Satır: 33 — atomik artırım
        }
    }

    public function reset(string $key): void
    {
        $this->cache->delete(self::KEY_PREFIX . $key);                 // Satır: 39
    }
}
```

**Sınıf yerleşimi notu:** `CacheRateLimiter` `shared/src/Cache/` altında
**değil**, `shared/src/Security/` altındadır (`Dosya:
shared/src/Security/CacheRateLimiter.php` · Satır: 3 namespace
`CoreMusic\Security`). Kapsam özeti dosyalarındaki "CacheRateLimiter.php"
yolları bu yerleşimi esas almalıdır (duplicate-pr adayı: `RateLimiterMiddleware`
`rl:` önekinin aynısını ayrı sabitle tanımlar — §15 AP-3).

---

## §7 — Rate limiter middleware — fail-closed/fail-open

```php
<?php declare(strict_types=1);
// Dosya: shared/src/Middleware/RateLimiterMiddleware.php · Satır: 50-82 (parçalı)
public function handle(array $request, callable $next): array
{
    $cache = $this->cache ?? $this->resolveCacheAdapter();       // Satır: 52
    if ($cache === null) {
        // ADR-013 §5.4 şart 1a: cache çözülemezse fail-closed (auth kapıları)
        LoggerFactory::getInstance()->securityEvent('rate_limiter_unavailable', [
            'path' => $path, 'mode' => 'fail-closed']);          // Satır: 57
        return [/* 503 */];                                      // Satır: 59-62 (özet)
    }
    // ...
    $key = self::CACHE_KEY_PREFIX . md5($ip);                    // Satır: 72
    $isNew = $cache->set($key, 1, $this->windowSeconds);         // Satır: 74 — ilk yazım
    // ...
    $count = $cache->increment($key, 1);                         // Satır: 79
    // increment başarısızsa güvenlik olay logu + yeniden set    // Satır: 81-82
}
private function resolveCacheAdapter(): ?CacheInterface
{
    return CacheManager::getAdapter();                           // Satır: 132
}
```

| Kapı modu | Tetik | Kanıt |
|---|---|---|
| fail-closed (auth) | cache çözülemiyor + kapı auth | Satır: 53-62 |
| fail-open (diğer) | cache çözülemiyor + kapı auth değil | Satır: 67 |
| sayaç artırım hatası | `increment` false | Satır: 81-82 (recovery: yeniden `set`) |

---

## §8 — Bağılantı noktasında adaptör seçimi (composition root)

```php
<?php declare(strict_types=1);
// Dosya: api.coremusic.net/index.php · Satır: 143-151
$apiCache       = (function_exists('apcu_enabled') && apcu_enabled())
    ? new ApcuAdapter()                    // Satır: 144
    : new MemoryAdapter();                 // Satır: 145

$pipeline = (new ApiMiddlewarePipeline())
    ->pipe(new ResponseNormalizationMiddleware())                // Satır: 148 — ETag/Cache-Control
    // ...
    ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))  // Satır: 151
```

Konteyner kayıtları (SSOT):

| Konteyner | Satır | Kayıt |
|---|---|---|
| `auth.coremusic.net/include/Container/AuthContainer.php` | 62 | `new CacheRateLimiter(CacheManager::getAdapter())` |
| `api.coremusic.net/include/Container/ApiAuthContainer.php` | 104 | `new CacheRateLimiter(CacheManager::getAdapter())` |

**Fark (⚠️ dikkat):** `index.php` kendi seçimini (`apcu_enabled()` kontrolü)
yaparken konteynerler `CacheManager::getAdapter()`'a (fonksiyon-varlığı
kontrolü) güvenir — iki farklı APCu tespit ölçütü. Eşdeğerlikleri
**⚠️ VERIFICATION REQUIRED** (ortam testi diskte yok).

---

## §9 — SPA cache katmanı — TTL + LRU

```js
// Dosya: assets.coremusic.net/js/router/CacheLayer.js · Satır: 2-5 (sabitler)
export default class CacheLayer {
    static DEFAULT_TTL = 600;                                       // Satır: 3
    static MAX_ENTRIES = 100;                                       // Satır: 4
    static TTL_MAP = { static: 3600, user: 120, dynamic: 60, default: 600 }; // Satır: 5
```

```js
// Dosya: assets.coremusic.net/js/router/CacheLayer.js · Satır: 22-40 (çekirdek)
get(url) {
    const key = normalizeCacheKey(url);                             // Satır: 23
    const entry = this.#store.get(key);
    if (!entry) return null;
    if (this.#isExpired(entry)) { this.#store.delete(key); this.#cleanupTags(key); return null; } // 26
    if (!entry.html || typeof entry.html !== 'string') { this.#store.delete(key); ... return null; } // 27
    entry.accessedAt = ++this.#accessCounter;                       // Satır: 28 — LRU sayacı
    return entry;
}

set(url, entry, cacheable = true, tags = []) {
    if (!cacheable) return;                                         // Satır: 33 — cacheable değilse yazma
    const key = normalizeCacheKey(url);
    if (this.#store.size >= this.#maxEntries) this.#evictLRU();     // Satır: 35 — kapasite doldu → LRU
    const ttlType = entry.meta?.ttlType || 'default';               // Satır: 36
    const ttl = this.#ttlMap[ttlType] || this.#defaultTtl;          // Satır: 37 — TTL_MAP seçimi
    this.#store.set(key, { html: entry.html, meta: entry.meta, timestamp: Date.now(), ttl, accessedAt: ++this.#accessCounter }); // 38
    for (const tag of tags) { /* tag → key indeksi */ }             // Satır: 39
}
```

```js
// Dosya: assets.coremusic.net/js/router/CacheLayer.js · Satır: 51-58 (eviction)
#evictLRU() {
    let oldest = null; let oldestKey = null;
    for (const [key, entry] of this.#store) {
        if (!oldest || entry.accessedAt < oldest) { oldest = entry.accessedAt; oldestKey = key; }
    }
    if (oldestKey) this.#store.delete(oldestKey);                   // Satır: 54 — tek entry düşür
}
#isExpired(entry) { return (Date.now() - entry.timestamp) > (entry.ttl * 1000); } // Satır: 58 — ms cinsinden
```

**PHP ile karşılaştırma:** TTL birimi iki tarafta da **saniye** (§14);
`MemoryAdapter` lazy-eviction yapar, `CacheLayer` hem lazy (Satır: 26) hem
kapasite-bazlı LRU (Satır: 35, 51-55) uygular — PHP tarafında LRU **YOK**
(§16 P-LRU1).

---

## §10 — Prefetch kuyruğu

```js
// Dosya: assets.coremusic.net/js/router/PrefetchManager.js · Satır: 13-31 (çekirdek)
constructor(cache, fetcher, logger, maxPrefetch = 2) {              // Satır: 13 — eşzamanlılık sınırı: 2
    this.#cache = cache; this.#fetcher = fetcher; this.#logger = logger; this.#maxPrefetch = maxPrefetch;
}

async prefetch(url) {
    if (this.#prefetching.has(url) || this.#cache.get(url) || this.#prefetching.size >= this.#maxPrefetch) return; // 21
    this.#prefetching.add(url);                                     // Satır: 23
    const ctrl = new AbortController();                             // Satır: 24 — iptal edilebilir
    this.#activeRequests.set(url, { type: REQUEST_TYPES.PREFETCH, controller: ctrl }); // 25
    try {
        const r = await this.#fetcher.fetch(url, { signal: ctrl.signal });   // Satır: 28
        if (r.ok) {
            const ct = r.headers?.get(HEADERS.CONTENT_TYPE) || '';           // Satır: 30
            await ContentFetcher.parseResponse(r, ct, url, this.#cache, this.#logger); // 31
        }
    } catch (err) { /* AbortError sessiz; diğerleri debug log */ }   // Satır: 33-36
    finally { this.#prefetching.delete(url); this.#activeRequests.delete(url); } // 37-39
}
```

| Koruma | Satır | Etki |
|---|---|---|
| Aynı URL iki kez prefetch edilmez | 21 (`#prefetching.has`) | Duplicate istek yok |
| Zaten cache'te varsa atlanır | 21 (`this.#cache.get(url)`) | Cache hit → ağ yok |
| Eşzamanlılık sınırı `maxPrefetch = 2` | 13, 21 | Bant genişliği korunur |
| Abort ile iptal | 24-25 | Navigasyon değişince gereksiz iş yok |

---

## §11 — Router ↔ cache bağlantısı

```js
// Dosya: assets.coremusic.net/js/router/Router.js · Satır: 3, 25-29, 55, 69 (parçalı)
import CacheLayer from './CacheLayer.js';                                  // Satır: 3
const { slowNavigationMs = 3000, memoryCheckInterval = 5000,
        memoryThresholdMB = 100, maxPrefetch = 2, ...rest } = config;      // Satır: 25
this.#cache = config.cache ?? new CacheLayer({}, this.#logger);            // Satır: 29 — DI seam
onPrefetch: (url) => this.#nav.prefetch(url),                              // Satır: 55
async prefetch(url) { await this.#nav?.prefetch(url); }                    // Satır: 69
```

**Not:** `Router` cache'i **dışarıdan enjekte edilebilir** (`config.cache`) —
JS testleri için doğal seam (§14, §16 P-JST1).

---

## §12 — DB tarafı — rate limit tablosu

```sql
-- Dosya: .ai/.sql/mysql/coremusic_api.sql · Satır: 78-92 (tam blok)
CREATE TABLE `rate_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned NOT NULL,
  `endpoint_pattern` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_requests` int unsigned NOT NULL DEFAULT '60',
  `window_sec` int unsigned NOT NULL DEFAULT '60',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rl_key_endpoint` (`api_key_id`,`endpoint_pattern`(255)),
  KEY `idx_rl_active` (`is_active`),
  KEY `idx_rl_endpoint` (`endpoint_pattern`(255)),
  CONSTRAINT `rate_limits_ibfk_1` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**İki katmanlı semantik:** sayaçlar cache'te yaşar (§6-§7); **limit tanımı**
(api_key + endpoint_pattern + max_requests + window_sec) DB'dedir. `api_keys`
silinince limit tanımları da cascade ile düşer (Satır: 91).

---

## §13 — Statik varlık TTL + cache-buster

```apache
# Dosya: assets.coremusic.net/.htaccess · Satır: 20, 25, 30, 35, 38 (parçalı)
Header set Cache-Control "public, max-age=3600"                        # L20 — CSS/js benzeri
# L25: # max-age=3600 bayat modul karisimina (deploy sonrasi eski+yeni kod birlikte)
Header set Cache-Control "no-cache"                                    # L30 — yeniden doğrulama zorunlu
Header set Cache-Control "public, max-age=31536000, immutable"         # L35 — hash'li font/varlık
Header set Cache-Control "public, max-age=604800"                      # L38 — görsel
```

```php
<?php declare(strict_types=1);
// Dosya: home.coremusic.net/vendor/coremusic/shared-infrastructure/src/Device/DeviceRenderer.php
// · Satır: 123 (auth-bundled.css) ve 149-151 (link() helper — cache-buster)
private function link(string $path, string $id): string                // Satır: 149
{
    $url = $this->assetsUrl . '/Css/' . $path . '?v=' . $this->cacheBuster; // Satır: 151
    // ...
}
```

**Cache-buster stratejisi:** dosya adı sabit (`auth-bundled.css`) → sürüm
**query parametresiyle** taşınır (`?v=<cacheBuster>`); `max-age=3600`'lık
CSS için bayat-kod riski bu sayede kapatılır (`.htaccess` L25 yorumu ile
aynı problemi hedefler — iki farklı savunma katmanı).

---

## §14 — Diller arası sözleşme (TTL/key/JSON)

| Sözleşme | PHP | JS | Uyum |
|---|---|---|---|
| TTL birimi | saniye (`CacheInterface::set(..., int $ttl = 0)`) | saniye (`CacheLayer` `ttl*1000` → ms'ye çevirir, Satır: 58) | ✅ |
| Miss semantiği | `get()` → `false` | `get()` → `null` | ⚠️ Farklı (dil natürel) — karışım yok, katmanlar ayrı |
| Key normalizasyonu | ham string + önek (`rl:`, `page_auth_`) | `normalizeCacheKey(url)` (UrlUtils) | ⚠️ Ortak fonksiyon YOK |
| Kapasite sınırı | YOK (PHP) | `MAX_ENTRIES = 100` (JS) | ⚠️ Asimetri (§16 P-MEM1) |
| Sayaç atomikliği | `increment()` kontratı | Yok (JS'te sayaç yok) | ✅ ihtiyaç yok |
| JSON gövde | `json_encode` (ETag girdisi) | `parseResponse` (ContentFetcher) | ✅ |

---

## §15 — Anti-pattern örnekleri (YAPMA)

| # | Anti-pattern | Neden kötü | Repo'da karşılığı | Kanıt |
|---|---|---|---|---|
| AP-1 | Giriş noktasında koşulsuz `apcu_clear_cache()` | Her istekte cache sıfırlanır (B-F-24) | **YOK** — testle yasaklanmış | `EntryPointCacheSafetyTest.php` · Satır: 34-36 |
| AP-2 | `no-store` + `ETag` birlikte | Çelişki: cache dışı yanıttaki doğrulayıcı anlamsız | **YOK** — docblock ile bilinçli | `ResponseNormalizationMiddleware.php` · Satır: 18, 41 |
| AP-3 | `rl:` önekinin iki ayrı sabitte tekrarı | Drift riski (duplicate-pr adayı) | **VAR** — `CacheRateLimiter::KEY_PREFIX` (Satır: 10) + `RateLimiterMiddleware::CACHE_KEY_PREFIX` (Satır: 11) | ⚠️ Taşıyıcı bulgu |
| AP-4 | `exit` ile 304 sonlandırma | Pipeline/index.php bypass → asset sızıntısı | **YOK** — testle denetlenir | `ResponseNormalizationMiddlewareTest.php` · Satır: 34-42 |
| AP-5 | Cache anahtarında `$_SERVER` doğrudan gömme | Test edilemezlik + PII sızıntısı | **YOK** — `md5($ip)`, `md5($uri)` | `RateLimiterMiddleware.php` · Satır: 72; `PageCacheAdapter.php` · Satır: 31 |
| AP-6 | JS'te cache'e `cacheable=false` veriyi yazmak | no-store semantiği ihlali | **YOK** — erken `return` | `CacheLayer.js` · Satır: 33 |

---

## §16 — PLANNED örnekler (kod YOK)

| # | PLANNED örnek | Beklenen davranış | Eksik kanıt |
|---|---|---|---|
| P-ET1 | `If-None-Match` listesi + `W/` desteği | `a, b` listesi ve `W/"x"` karşılaştırması | Repo: `===` birebir (Satır: 74) |
| P-LRU1 | PHP cache'te LRU/kapasite sınırı | `MemoryAdapter` için MAX girdisi | Repo: sınır yok (§3) |
| P-MEM1 | Bellek-içi adaptörde bayat anahtar süpürme | `gc_probability` benzeri periyodik temizlik | Repo: lazy-only (§3) |
| P-JST1 | `CacheLayer`/`PrefetchManager` birim testi | TTL/LRU/prefetch limiti davranışı | Repo: `assets.coremusic.net/tests/` altında CacheLayer spec **YOK** |
| P-CDN1 | CDN/Varnish'te `Age`/`Expires` doğrulaması | Operasyonel ortam ölçümü | Kardeş `caching-dogrulama.md` §4 — operasyonel veri yok |

---

## §A — Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | `index.php` (`apcu_enabled()`) ile `CacheManager` (`function_exists('apcu_fetch')`) ölçütleri her ortamda eşdeğer mi? | ⚠️ VERIFICATION REQUIRED |
| 2 | `CacheLayer` tag indeksi (`#tags`) hangi bileşenlerce kullanılıyor (üretici var mı)? | ⚠️ VERIFICATION REQUIRED |
| 3 | `rate_limits` tablosu production'da **yazılıyor mu** (DB limitleri cache sayaçlarıyla senkron mu)? | ⚠️ VERIFICATION REQUIRED |
| 4 | `?v=` cache-buster değeri (`$cacheBuster`) deploy'da nasıl güncelleniyor? | ⚠️ VERIFICATION REQUIRED |

---

## §B — Kullanım kuralları

1. Bu dosyadaki her kod bloğu **repo kanıtlıdır**; `Dosya: · Satır:` satırı
   olmayan blok bu dosyaya **eklenemez**.
2. ETag havuz URL'i yalnız §1'de kullanılır; ikinci kez hiçbir yerde açılmaz.
3. §15'teki anti-pattern'ler "örnek alındı" olarak **kopyalanamaz**; AP-3
   hariç hepsi repo'da **yokluğu** kanıtlanmış davranışlardır.
4. §16 PLANNED blokları uygulama sırasında **kod olarak üretilmez**; önce
   ADR/onay, sonra test.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
