---
node_id: K011-application/caching/caching-uygulama-seviyesi-cache
node_type: module
status: draft
parent_id: K011-application/caching
children: []
depends_on:
  - K011-application
evidence: []
validation: []
open_questions: []
group: mimari
---

# caching-uygulama-seviyesi-cache — Uygulama Seviyesi Cache Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K011-application · **Alt klasör:** `caching`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-007 (cache namespace) · ADR-013 (rate limit APCu)
- **İmla koruması:** düğüm adları `identitiy` · `integraion` yazım bozuklukları bilinçli korunmuştur.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1–§3 Amaç, terminoloji, yerleşim | mimari | katman | Cache katmanının K011 konumu (ADR-007 namespace, ADR-013 rate limit) |
| 2 | §4–§6 Web research, standart tabanı, repo kanıtları | kanit | kaynak-kod | Harici başlık referansları + `Dosya: … · Satır: …` izleri |
| 3 | §7–§9 Katmanlar, anahtar, TTL | mimari | politika | Apcu/Memory/PageCache üçlüsü, anahtar şeması, TTL aralıkları |
| 4 | §10–§12 HTTP yanıt önbelleklemesi, cache-aside, `Vary` | mimari | http | ETag / Cache-Control / `Vary` sözleşmesi ve cache-aside akışı |
| 5 | §13–§15 Rate limit ilişkisi, invalidasyon, güvenlik | kanit | denetim | `increment` sayacı, invalidasyon kuralları, cache poisoning savunması |
| 6 | §16–§20 Test, açık sorular, politika, faz, red team | kanit | doğrulama | Test taslakları, 6 açık soru, faz planı, saldırı senaryoları |
| 7 | §21–§27 Kapsam dışı devir, karar matrisi, risk, özet, ölçüm | mimari | ölçüm | Karar kaydı + sayımlar + anahtar şeması uygulama notları |
| 8 | Kardeş dosyalar (`-dogrulama` · `-web-arastirma` · `-ornekler` · `-bilesenler` · `index.md`) | kod/kanit | üretim | İkinci eksen: dosya grubu × kullanım ekseni (Ünite-3 seti) |

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic'in **uygulama seviyesi önbelleğini** tanımlar: adapter arayüzü,
APCu/memori/sayfa katmanları, anahtar ve TTL politikaları, HTTP yanıt önbelleklemesi
(ETag/304/Cache-Control), invalidasyon kuralları ve rate limit'in cache üzerindeki
kullanımı. Amaç, üç ayrı önbelleğin **sınırlarını** ve **güvenlik varsayımlarını**
sözleşme altına almaktır.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | Adapter arayüzü | `get/set/delete/increment/clear/has` | Fiziksel depo altyapısı (K018-operasyon) |
| 2 | APCu katmanı | Fonksiyon varlık denetimi, TTL | Sunucu yapılandırması (ops) |
| 3 | Sayfa cache | URI + kimlik anahtarı, `remember` | Sayfa render (presentation) |
| 4 | HTTP cache | ETag, 304, Cache-Control | CDN/proxy yapılandırması (K014-network) |
| 5 | Anahtar politikası | Önek, namespace, kimlik ayrımı | Veritabanı cache (K005-data) |
| 6 | TTL | Sınıf bazlı ömürler | İş kuralı ömürleri (domain) |
| 7 | Invalidasyon | Yazma sonrası temizlik | Event altyapısı (ADR-086) |
| 8 | Rate limit | APCu sayaç kullanımı, yarış riski | Kota değerleri (ADR-013) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| Adapter | cache adapter | Depo arkasındaki ortak arayüz | `CacheInterface` |
| Önek | key prefix | Anahtar adlandırma alanı | `CacheRateLimiter::KEY_PREFIX` |
| Namespace | key namespace | Grup bazlı temizlik alanı | ADR-007 |
| Yaşam süresi | TTL | Kaydın geçerli saniye sayısı | `set($key, $value, $ttl)` |
| Sayaç | counter | Artırılabilir sayısal kayıt | `increment(key, step)` |
| Cache-aside | cache-aside | Okumada cache'e bak, yoksa çekip yaz | §13 |
| Yazma etiketi | write-through | Yazarken cache'i güncelleme | §16.2 |
| Geçersizleştirme | invalidation | Kaydın ömrünü sonlandırma | `delete` / `clear` |
| ETag | entity tag | Yanıt özeti (değişiklik izi) | `md5(json_encode($response))` |
| Koşullu istek | conditional request | `If-None-Match` ile 304 | §10.3 |
| 304 | not modified | Gövdesiz "değişmedi" | `http_response_code(304)` |
| `no-store` | no-store | İstemci/sunucu saklamasın | `sendCacheControl(false, $cacheTtl)` |
| `max-age` | max-age | Saniye cinsinden ömür | `public, max-age={$cacheTtl}` |
| Vary | vary | Yanıtın hangi başlıklara göre değiştiği | §12 — kodda yok |
| Sayfa anahtarı | page key | URI + kimlik birleşimi | `pageKey($uri, $authenticated)` |
| Hatırlatıcı | remember | Yoksa üretip yazan sarmalayıcı | `remember($key, $ttl, $callback)` |
| Yarış koşulu | race condition | Check-then-set boşluğu | §14.2 |
| Fail-open | fail-open | Bağlantı yokken erişime izin verme | §14.3 |

## §3 — Mimari yerleşim (kademe-3 konumu)

| Katman | Cache bileşeni | Dosya |
|---|---|---|
| Domain | Cache **kullanmaz** (saf iş kuralı) | — |
| Application | Uygulama seviyesi cache çağıran servisler | Servisler |
| Infrastructure | Adapter üçlüsü | `shared/src/Cache/{Apcu,Memory,PageCache}Adapter.php` |
| Cross-cutting | Rate limit sayaç, HTTP yanıt başlıkları | `CacheRateLimiter`, `ResponseNormalizationMiddleware` |

## §4 — Web research (2026-10-13 doğrulaması)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| MDN HTTP caching rehberi, tarayıcı/sunucu önbelleklerini ve yeniden doğrulamayı anlatır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Caching | HTTP 200 | Doğrulandı — envanterde yok |
| MDN `ETag`, yanıt özeti başlığını ve `If-None-Match` ile eşleşmeyi tanımlar | https://developer[.]mozilla[.]org/en-US/docs/Web/HTTP/Headers/ETag | HTTP 200 | Doğrulandı — envanterde VAR (HIT) - noktalı gösterim (2026-10-16) |
| MDN `Vary`, yanıtın hangi istek başlıklarına göre farklılaştığını belirtir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Vary | HTTP 200 | Doğrulandı — envanterde yok |
| ASP.NET Core memory cache dokümanı, bellek içi önbellek TTL ve boyut davranışını belgeler | https://learn.microsoft.com/en-us/aspnet/core/performance/caching/memory | HTTP 200 | Doğrulandı — envanterde yok |
| Azure cache-aside deseni, okuma önbellekleme ve geçersizleştirme sorumluluğunu tanımlar | https://learn.microsoft.com/en-us/azure/architecture/patterns/cache-aside | HTTP 200 | Doğrulandı — envanterde yok |
| PHP `apcu_enabled`, APCu'nun çalışıp çalışmadığını döndürür | https://www.php.net/manual/en/function.apcu-enabled.php | HTTP 200 | Doğrulandı — envanterde yok |

**Doğrulama yöntemi:** altı URL `Invoke-WebRequest` ile çağrıldı → `200`; `.ai/` genelinde
URL öbeği araması → **0 isabet** (dikkat: envanterde `rfc9111.html` biçiminde bir kayıt
vardır; bu dosya onu **kullanmaz**, MDN rehberini kullanır).

## §5 — Standart ve spesifikasyon tabanı

| Kaynak | Ne düzenler | Bu dosyadaki kullanımı |
|---|---|---|
| MDN Caching rehberi | Yeniden doğrulama akışı | §10 |
| MDN `ETag` | Özet başlığı | §10.2 |
| MDN `Vary` | Alt varyant ayrımı | §12 |
| ASP.NET memory cache | TTL/boyut pratikleri | §9 (karşılaştırma) |
| Azure cache-aside | Okuma deseni + invalidasyon | §13 |
| PHP `apcu_enabled` | Varlık denetimi | §7.2 |
| ADR-007 | Cache namespace | §8 |
| ADR-013 | Rate limit (APCu) | §14 |

## §6 — Repo kod kanıtları (2026-10-13 doğrulanmış satırlar)

| # | Dosya | Satır | Ne gösterir |
|---|---|---|---|
| 1 | `shared/src/Cache/CacheInterface.php` | 7 | `public function get(string $key): mixed;` |
| 2 | `shared/src/Cache/CacheInterface.php` | 8 | `public function set(string $key, mixed $value, int $ttl = 0): bool;` |
| 3 | `shared/src/Cache/CacheInterface.php` | 9 | `public function delete(string $key): bool;` |
| 4 | `shared/src/Cache/CacheInterface.php` | 10 | `public function increment(string $key, int $step = 1): int\|false;` |
| 5 | `shared/src/Cache/CacheInterface.php` | 11 | `public function clear(): bool;` |
| 6 | `shared/src/Cache/CacheInterface.php` | 12 | `public function has(string $key): bool;` |
| 7 | `shared/src/Cache/ApcuAdapter.php` | 5 | `final class ApcuAdapter implements CacheInterface` |
| 8 | `shared/src/Cache/ApcuAdapter.php` | 9 | `if (!function_exists('apcu_fetch')) { return false; }` — varlık denetimi |
| 9 | `shared/src/Cache/ApcuAdapter.php` | 17 | `public function set(string $key, mixed $value, int $ttl = 0): bool` |
| 10 | `shared/src/Cache/ApcuAdapter.php` | 22 | `return apcu_store($key, $value, $ttl);` |
| 11 | `shared/src/Cache/ApcuAdapter.php` | 33 | `public function increment(string $key, int $step = 1): int\|false` |
| 12 | `shared/src/Cache/ApcuAdapter.php` | 35 | `if (!function_exists('apcu_inc')) { return false; }` |
| 13 | `shared/src/Cache/ApcuAdapter.php` | 38 | `return apcu_inc($key, $step);` |
| 14 | `shared/src/Cache/MemoryAdapter.php` | 5 | `final class MemoryAdapter implements CacheInterface` |
| 15 | `shared/src/Cache/PageCacheAdapter.php` | 5 | `final class PageCacheAdapter implements PageCacheInterface` |
| 16 | `shared/src/Cache/PageCacheAdapter.php` | 11 | `__construct(?CacheInterface $cache = null, bool $enabled = true, int $ttlPage = 600)` |
| 17 | `shared/src/Cache/PageCacheAdapter.php` | 28 | `public function pageKey(string $uri, bool $authenticated): string` |
| 18 | `shared/src/Cache/PageCacheAdapter.php` | 34 | `public function remember(string $key, int $ttl, callable $callback): mixed` |
| 19 | `shared/src/Cache/PageCacheInterface.php` | 7 | `public function isPageCacheEnabled(): bool;` |
| 20 | `shared/src/Cache/PageCacheInterface.php` | 9 | `public function pageKey(string $uri, bool $authenticated): string;` |
| 21 | `shared/src/Security/CacheRateLimiter.php` | 10 | `private const KEY_PREFIX = 'rl:';` — önek |
| 22 | `shared/src/Security/CacheRateLimiter.php` | 16 | `public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool` |
| 23 | `shared/src/Security/CacheRateLimiter.php` | 23 | `return (int)$count >= $maxAttempts;` — eşik karşılaştırması |
| 24 | `shared/src/Security/CacheRateLimiter.php` | 31 | `$this->cache->set($cacheKey, 1, $windowSeconds);` — ilk sayaç |
| 25 | `shared/src/Security/CacheRateLimiter.php` | 33 | `$this->cache->increment($cacheKey, 1);` — sonraki sayaç |
| 26 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 27 | `public function __invoke(array $request, callable $next): array` |
| 27 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 35 | `$cacheable = is_array($route) && !empty($route['cacheable']);` |
| 28 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 43 | `$response = $this->withETag($response);` |
| 29 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 68 | `private function withETag(array $response): array` |
| 30 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 71 | `header('ETag: "' . $etag . '"');` |
| 31 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 75 | `http_response_code(304);` |
| 32 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 88 | `private function sendCacheControl(bool $cacheable, int $cacheTtl): void` |
| 33 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 91 | `header("Cache-Control: public, max-age={$cacheTtl}");` |
| 34 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 93 | `header('Cache-Control: no-store, no-cache, must-revalidate');` |
| 35 | `api.coremusic.net/config/routes.php` | 16 | `cacheable / cacheTtl : ResponseNormalization ETag + Cache-Control` |

## §7 — Cache katmanları

### §7.1 Üç adapter

| Adapter | Ortam | Sınıf | Not |
|---|---|---|---|
| APCu | Sunucu belleği (paylaşımlı) | `ApcuAdapter` | Üretim adayı |
| Memory | İşlem belleği | `MemoryAdapter` | Test/tek istek |
| Page | Sayfa özeli | `PageCacheAdapter` | `ttlPage = 600` varsayılanı |

### §7.2 APCu varlık denetimi

| Fonksiyon | Denetim satırı | Yoksa davranış |
|---|---|---|
| `apcu_fetch` | `ApcuAdapter.php:9` | `false` döner |
| `apcu_store` | `ApcuAdapter.php:19` | `false` döner |
| `apcu_inc` | `ApcuAdapter.php:35` | `false` döner |
| `apcu_delete` | `ApcuAdapter.php:27` | `false` döner |
| `apcu_clear_cache` | `ApcuAdapter.php:43` | `false` döner |

**Önemli gözlem:** `function_exists` denetimi, PHP'nin `apcu_enabled()` API'si yerine
fonksiyon varlığına bakar; `apcu.enabled = 0` durumu bu denetimle **yakalanmaz**
(§19 açık soru #1).

### §7.3 Arayüz kapsamı

| Metot | Sözleşme | Rate limit ilişkisi |
|---|---|---|
| `get` | Yoksa `false`/`null` | Sayaç okuma (`CacheRateLimiter.php:19`) |
| `set` | TTL saniye; `0` = süresiz | İlk sayaç (`:31`) |
| `delete` | Tek anahtar | `reset` (`:39`) |
| `increment` | Adımlı artırma | Sayaç (`:33`) |
| `clear` | Tümü | **Dikkatli kullanılmalı** (§16.4) |
| `has` | Varlık | İkincil kontrol |

## §8 — Anahtar politikası

| Kural | Değer | Kanıt |
|---|---|---|
| Rate limit öneki | `rl:` | `CacheRateLimiter.php:10` |
| Sayfa anahtarı | URI + `authenticated` bool | `PageCacheAdapter.php:28` |
| Namespace | ADR-007 ile öneklenir | ADR-007 |
| Karakter seti | `a-z0-9:_-` önerilir | Sözleşme (§20 #2) |
| Uzunluk sınırı | 64 bayt önerilir | Sözleşme (§20 #2) |
| Kişisel veri | Anahtara **asla** ham PII yazılmaz | §17.2 |
| Kullanıcı ayrımı | Kimlikli sayfalarda anahtara kimlik parçası girer | `pageKey($uri, $authenticated)` |

### §8.1 Örnek anahtarlar

| Amaç | Örnek biçim | Kaynak |
|---|---|---|
| Rate limit | `rl:` + istemci anahtarı | `CacheRateLimiter.php:10,18` |
| Sayfa (anonim) | URI + `false` | `PageCacheAdapter.php:28` |
| Sayfa (kimlikli) | URI + `true` | Aynı |
| ETag | Yanıt gövdesi özeti | `ResponseNormalizationMiddleware.php:70` |

## §9 — TTL politikası

| Sınıf | Önerilen TTL | Dayanak | Durum |
|---|---|---|---|
| Sayfa cache | 600 sn | `PageCacheAdapter.php:11` (`$ttlPage = 600`) | KANITLI (varsayılan) |
| Rate limit penceresi | Çağrı parametresi | `CacheRateLimiter.php:16` (`$windowSeconds`) | Çağrıya bağlı |
| Yanıt `max-age` | Rota `cacheTtl` alanı | `routes.php:16`, `:91` | Rota bazlı |
| `no-store` rota | 0 (yalnız `max-age` yok) | `:93` | KANITLI |
| Token/oturum | Cache yok (session) | K010 | KAPSAM DIŞI |
| Domain verisi | UNKNOWN | Karar yok | §19 #3 |

**Kural:** TTL ne kadar kısa olursa tutarlılık o kadar yüksek, isabet o kadar düşük;
`cacheTtl` değeri rota **sahibi** tarafından yazılır (routing §7.1).

## §10 — HTTP yanıt önbelleklemesi

### §10.1 Dal

| Durum | Davranış | Kanıt |
|---|---|---|
| `route['cacheable']` dolu | ETag basılır | `:35`, `:43` |
| `cacheable` değil | `no-store, no-cache, must-revalidate` | `:93` |
| ETag üretilir | `md5(json_encode($response))` | `:70` |
| `If-None-Match` eşleşirse | Gövdesiz 304 | `:75`, `:77` |
| `max-age` | `public, max-age={$cacheTtl}` | `:91` |

### §10.2 ETag kuralları

| Kural | Değer |
|---|---|
| Özet algoritması | `md5` (`:70`) — içerik özeti, güvenlik imzası değil |
| Tırnak | Başlık değeri tırnak içinde (`:71`) |
| Kimlikli yanıt | `cacheable` değilse ETag yok (`:41` yorumu) |
| Değişiklik | Gövde değişirse ETag değişir |

### §10.3 304 yolu

| Kural | Değer | Kanıt |
|---|---|---|
| `exit` kullanımı | **YOK** — pipeline bypass edilmez | `:19` ve `:62` yorumları |
| Yanıt | `http_status = 304`, boş gövde | `:75`, `:77` |
| Gerekçe | `index.php` akışını bozmamak | `:19` yorumu |

### §10.4 `no-store` tutarlılığı

| İlke | Uygulama |
|---|---|
| `no-store` yanıtta ETag basılmaz | `:18` yorumu (çelişki yok) |
| Hassas veri | `no-store` zorunlu | §17.3 |
| Varsayılan | `no-store` (güvenli varsayılan) | `:93` |

## §11 — Cache-aside deseni (uygulama)

| # | Adım | İşlem | Hata |
|---|---|---|---|
| 1 | Oku | `get(key)` | `false` → devam |
| 2 | İsabet | Değer var mı | Dön |
| 3 | Ezi | Kaynağa git (DB/HTTP) | Hata → hatayı geç |
| 4 | Yaz | `set(key, value, ttl)` | Başarısız → yine de sonuç dön |
| 5 | Geçersizleştir | Yazmada `delete` | §16 |

**Azure karşılığı:** cache-aside deseni, önbellek **tutarsızlığının sorumluluğunu**
uygulamaya yükler (§13 kaynağı: cache-aside dokümanı).

## §12 — `Vary` başlığı

| Gözlem | Değer |
|---|---|
| `Vary` kullanımı | Kodda **0 isabet** (2026-10-13 taraması: `ResponseNormalizationMiddleware` içinde yok) |
| Risk | `Accept-Encoding`/`Accept-Language` varyantlarında yanlış yanıt sunulabilir |
| Karar | Eklenmeli mi? → §19 açık soru #4 |
| Kaynak | MDN `Vary` (§4) |

## §13 — Rate limit ile cache ilişkisi

### §13.1 Akış

| # | Adım | Kod |
|---|---|---|
| 1 | Sayaç oku | `get('rl:' . $key)` (`:18-19`) |
| 2 | Eşik kontrolü | `(int)$count >= $maxAttempts` (`:23`) |
| 3 | İlk deneme | `set($cacheKey, 1, $windowSeconds)` (`:31`) |
| 4 | Sonraki deneme | `increment($cacheKey, 1)` (`:33`) |
| 5 | Sıfırlama | `delete('rl:' . $key)` (`:39`) |

### §13.2 Sabit pencere gözlemi

| Gözlem | Sonuç |
|---|---|
| TTL yalnız ilk `set`te yazılır (`:31`) | Pencere ilk denemeden başlar (sabit pencere) |
| `increment` TTL'ı yenilemez | Kayma (sliding) yok |
| Sonuç | 10 istemci-sunucu gecikmesinde eşik taşabilir | 
| Karar | Kayma pencere gerekiyor mu? → §19 #5 |

### §13.3 Yarış koşulu (TOCTOU)

| Gözlem | Kanıt | Risk |
|---|---|---|
| `get` + `set` ayrı çağrılar | `:29-31` | Aynı anda iki istek `1` yazabilir → sayaç kaybı |
| `increment` atomik | `apcu_inc` (`:38`) | Sonraki adımlar atomik |
| Etki | Eşiğin bir miktar aşılabilir | Düşük/orta — §20 #3 |
| Azaltma | Atomik `add` benzeri opsiyon | ADR-013 kararı |

### §13.4 Fail-open bulgusu

| Durum | Davranış | Sonuç |
|---|---|---|
| APCu yok (`function_exists` false) | `get` → `false` (`ApcuAdapter.php:9-11`) | Sayaç `false` sayılır → `isLimited` = **false** |
| Sonuç | Rate limit **uygulanmaz** | **fail-open** |
| İlke ihlali? | Güvenlik kuralı fail-closed ister | §20 #1 — işaretli |

## §14 — Invalidasyon

| # | Olay | Aksiyon | Durum |
|---|---|---|---|
| 1 | Domain yazma | İlgili anahtar `delete` | Sözleşme (§20 #6) |
| 2 | Sayfa değişimi | Page anahtarı temizliği | Sözleşme |
| 3 | Rota `cacheTtl` değişimi | Yanıt cache'i doğal olarak dolar | Kendiliğinden |
| 4 | `clear()` çağrısı | **Tüm** anahtarlar silinir | Sadece bakım |
| 5 | Süre dolumu | TTL | Kendiliğinden |
| 6 | Deploy | `clear()` önerilir | Ops kararı (UNKNOWN) |

**Yaygın hata:** invalidasyonu atlamak → bayat veri. Cache-aside dokümanı invalidasyonu
**uygulamanın sorumluluğu** olarak tanımlar (§4).

## §15 — Güvenlik

| # | Risk | Savunma | Kanıt/Durum |
|---|---|---|---|
| 1 | Cache poisoning (yanıt manipülasyonu) | Anahtarda varyant başlığı (§12) | `Vary` yok → işaretli |
| 2 | PII'nin anahtara sızması | Anahtar şeması (§8) | Sözleşme |
| 3 | Hassas yanıtın cache edilmesi | Varsayılan `no-store` | `:93` |
| 4 | Rate limit bypass (APCu yok) | Fail-open bulgusu | §13.4 → §20 #1 |
| 5 | Anonim/kimlikli karışması | `pageKey(string $uri, bool $authenticated)` ayrımı | `PageCacheAdapter.php:28` |
| 6 | TTL = 0 ile kalıcı kayıt | `set($key, $value, 0)` süresiz yazım | `CacheInterface.php:8` — kullanım denetimi §19 #7 |
| 7 | `clear()` ile hizmet durdurma | Yetki kısıtı | Sınir yok → §20 #7 |

## §16 — Test senaryoları (taslak)

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | `set` + `get` turu | Değer korunur |
| 2 | TTL dolunca | `get` → `false` |
| 3 | `increment` ardışık | 1 → 2 → 3 |
| 4 | APCu yokken `get` | `false` (`ApcuAdapter.php:9-11`) |
| 5 | `cacheable` rota + `If-None-Match` | 304, gövde boş |
| 6 | `cacheable` değil | `no-store, no-cache, must-revalidate` |
| 7 | Eşik dolunca | `isLimited` true |
| 8 | `reset` sonrası | Sayaç sıfır |
| 9 | Kimlikli/anonim sayfa anahtarı | Farklı anahtarlar |
| 10 | `pageKey` TTL | 600 sn varsayılan |
| 11 | Yarış simülasyonu | §13.3 davranışı gözlenir |
| 12 | `clear` sonrası | Tüm anahtarlar yok |

## §17 — Açık sorular ve doğrulanamayan iddialar

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | `apcu.enabled = 0` durumunda ne oluyor? (`function_exists` bunu görmüyor) | `⚠️ VERIFICATION REQUIRED` | security-engineer |
| 2 | Cache anahtar karakter/uzunluk şeması yazılı mı? | `UNKNOWN` (ADR-007 içeriği okunmadı) | architect |
| 3 | Domain verisi için TTL kararları var mı? | `UNKNOWN` | backend-architect |
| 4 | `Vary` başlığı eklenecek mi? | `UNKNOWN` | backend-architect |
| 5 | Sabit pencere yeterli mi, kayma pencere mi? | `UNKNOWN` (ADR-013 kararı) | security-engineer |
| 6 | Yazma sonrası invalidasyon hangi katmanda? | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 7 | `clear()` çağıracak yetki sınırı var mı? | `⚠️ VERIFICATION REQUIRED` | security-engineer |
| 8 | `cacheTtl` rota bazlı değerleri ölçüldü mü? | `UNKNOWN` | performance-engineer |

## §18 — Politika ↔ kod karşılaştırma tablosu

| Politika | Kod karşılığı | Durum |
|---|---|---|
| Ortak arayüz | `CacheInterface.php:7-12` | UYGUN |
| APCu adaptörü | `ApcuAdapter.php:5-55` | UYGUN |
| Sayaç öneki | `CacheRateLimiter.php:10` | UYGUN |
| Eşik karşılaştırması | `:23` | UYGUN |
| `cacheable` → ETag | `:35`, `:43` | UYGUN |
| `no-store` varsayılan | `:93` | UYGUN |
| `Vary` | Yok | **EKSİK** (§12) |
| Fail-closed rate limit | APCu yokken fail-open | **ÇELİŞİ** (§13.4) |
| Yarış güvenliği | `get`+`set` ayrı | **ZAYIF** (§13.3) |
| PII anahtarı | Sözleşme, kod okunmadı | `⚠️ VERIFICATION REQUIRED` |

## §19 — Faz planı

| Faz | Kapsam | Durum |
|---|---|---|
| 0 | Bu sözleşme dosyası | YAZILDI (draft) |
| 1 | Rate limit fail-open düzeltmesi (§13.4) | PLANNED — P0 |
| 2 | `Vary` başlığı kararı ve uygulaması (§12) | PLANNED |
| 3 | Kayma pencere değerlendirmesi (§13.2) | PLANNED |
| 4 | Invalidasyon sahipliğinin yazılması (§14) | PLANNED |
| 5 | Cache metriklerinin loglanması (isabet oranı) | PLANNED |

## §20 — Red team: saldırı senaryoları

| # | Saldırı | Öncül | Savunma |
|---|---|---|---|
| 1 | Rate limit bypass | APCu kapalıyken | §13.4 — fail-open |
| 2 | Sayaç yarışıyla eşik aşımı | Eşzamanlı istekler | §13.3 |
| 3 | Cache poisoning | Varyant manipülasyonu | §12 (`Vary` yok) |
| 4 | Anonim/kimlikli sızıntı | Yanlış `pageKey` | `bool` ayrımı (`:28`) |
| 5 | Hassas veri saklama | `cacheable` yanlış `true` | Routing bayrağı denetimi |
| 6 | Bayat veri ile yetki değişimi | Invalidasyon eksikliği | §14 |
| 7 | `clear()` ile hizmet yavaşlatma | Yetkisiz çağrı | §15 #7 |

## §21 — Faz dışı özet (kapsam dışı devir)

| Konu | Çıkış yeri | Gerekçe |
|---|---|---|
| Rota `cacheable` tanımı | `routing` §7.1 | Bayrak routing'de |
| HTTP durum kodu listesi | `routing` §12 | Kod listesi orada |
| ETag/md5 kararı | Bu dosya (§10.2) | — |
| CDN/proxy | `K014-network` | Altyapı ayrı |
| Veritabanı query cache | `K005-data` | Sorgu katmanı ayrı |
| Session cache | `K010-identitiy/session` | Kimlik ayrı |
| Kota değerleri | `K010` / ADR-013 | Politika ayrı |
| Log/ölçüm | `K013-audit` | Gözlem ayrı |
| BFF cache | Yok (bilinmiyor) | §17 #6 |
| Tarayıcı cache JS | Frontend dosyaları | Sunucu dışı |

## §22 — Karar matrisi (bu dosyada verilen kararlar)

| # | Karar | Değer | Dayanak |
|---|---|---|---|
| 1 | Varsayılan yanıt davranışı | `no-store` | `:93` |
| 2 | ETag yalnız `cacheable` rota | Evet | `:41-43` |
| 3 | 304'te `exit` yok | Evet | `:19` |
| 4 | Sayaç öneki | `rl:` | `:10` |
| 5 | Pencere tipi | Sabit (gözlem) | §13.2 |
| 6 | Fail-open bulgusu | Düzeltme gerekli | §13.4 |
| 7 | `Vary` | Henüz yok | §12 |
| 8 | Sayfa TTL | 600 sn | `PageCacheAdapter.php:11` |

## §23 — Risk kaydı

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Rate limit fail-open | Yüksek | Orta | Faz 1 P0 |
| 2 | `Vary` eksikliği | Orta | Orta | Faz 2 |
| 3 | Invalidasyon sahipliğinin belirsizliği | Yüksek | Orta | Faz 4 |
| 4 | Yarışla eşik aşımı | Orta | Düşük | Faz 3 |
| 5 | PII anahtara sızması | Yüksek | Düşük | §8 + denetim |
| 6 | `cacheTtl` yanlış değeri | Orta | Orta | Rota denetimi (routing) |

## §24 — Özet tablo

| Boyut | Karar | Durum |
|---|---|---|
| Adapter sayısı | 3 (APCu, Memory, Page) | ÖLÇÜLDÜ |
| Arayüz metodu | 6 | KANITLI (`CacheInterface.php:7-12`) |
| ETag üretimi | `md5(json_encode($response))` | KANITLI (`:70`) |
| `no-store` varsayılan | Var | KANITLI (`:93`) |
| 304 yolu | `exit` yok | KANITLI (`:19`) |
| `Vary` | Yok | EKSİK (§12) |
| Rate limit öneki | `rl:` | KANITLI (`:10`) |
| Fail-open bulgusu | Var | İŞARETLİ (§13.4) |
| Yarış koşulu | Var | İŞARETLİ (§13.3) |
| Sayfa TTL | 600 sn | KANITLI (`PageCacheAdapter.php:11`) |

## §25 — Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış repo kod alıntısı | 35 | §6 tablosu |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu (hepsi HTTP 200) |
| Envanter kesişimi | 0 | §4 notu (`rfc9111.html` kasten kullanılmadı) |
| Açık soru | 8 | §17 |
| Saldırı senaryosu | 7 | §20 |
| Test senaryosu | 12 | §16 |
| Faz | 6 (0–5) | §19 |
| Bulunan güvenlik bulgusu | 2 | §13.3 · §13.4 |

## §26 — Sonraki adımlar

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | §13.4 fail-open davranışını kapat (fail-closed) | P0 | security-engineer |
| 2 | §12 `Vary` kararını ver | P1 | backend-architect |
| 3 | §14 invalidasyon sahipliğini yaz | P1 | backend-architect |
| 4 | §16 testlerini yaz (qa-engineer) | P1 | qa-engineer |
| 5 | §13.2 pencere tipini ADR-013 ile netleştir | P2 | security-engineer |
| 6 | Cache isabet metriğini ekle (faz 5) | P2 | sre-engineer |

## §27 — Uygulama notları: anahtar şeması ve ölçüm

### §27.1 Anahtar şeması tablosu (sözleşme)

| Alan | Önek | Ayırıcı | Örnek birleşim | Kaynak |
|---|---|---|---|---|
| Rate limit | `rl:` | yok | `rl:` + istemci anahtarı | `CacheRateLimiter.php:10` |
| Sayfa önbelleği | yok (URI ham) | yok | URI + oturum ayracı | `PageCacheAdapter.php:28` |
| Uygulama verisi | servis-bazlı | `:` | `{modül}:{kayıt}:{id}` | §8 kararı |
| Oturum sayaçları | oturum-bazlı | `:` | `{oturum}:{sayaç}` | §6 tablosu |

### §27.2 Ölçüm tablosu (faz 5 hedefleri)

| Metrik | Tanım | Hedef | Ölçüm aracı |
|---|---|---|---|
| Cache hit oranı | isabet / toplam okuma | ≥ %80 | APCu istatistikleri |
| 304 oranı | 304 / cacheable yanıt | ≥ %60 | erişim log'u |
| Ortalama cache get süresi | tek `get` çağrısı | < 1 ms | microtime turu |
| Fail-open olayı | APCu yokken rate limit atlatma | 0 | güvenlik alarmı |
| PII sızıntısı | anahtarda email/parola | 0 | §8 denetimi |

### §27.3 Karar tablosu (bu bölümde özetlenen)

| # | Karar | Dayanak | Durum |
|---|---|---|---|
| 1 | Uygulama cache'i `CacheInterface` üzerinden erişilir | `CacheInterface.php:7-12` | KANITLI |
| 2 | HTTP yanıt önbelleği `PageCacheAdapter` ile 600 sn | `PageCacheAdapter.php:11` | KANITLI |
| 3 | Rate limit `rl:` öneki + APCu | `CacheRateLimiter.php:10` | KANITLI |
| 4 | Hassas yanıtlar varsayılan `no-store` | `ResponseNormalizationMiddleware.php:93` | KANITLI |
| 5 | `Vary` başlığı henüz üretilmiyor | kodda 0 eşleşme | EKSİK (§12) |
| 6 | APCu yokken rate limit fail-open | `ApcuAdapter.php:9` | BULGU (§13.4) |
| 7 | `remember` sarmalayıcısı get+set yarışını içerir | §14.3 | BULGU (§13.3) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
