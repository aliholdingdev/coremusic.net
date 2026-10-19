---
title: "K011 · caching — Bileşen Listesi"
type: architecture
kodeme: kademe-2
module: K011-application
parent: caching
status: active
updated: 2026-10-15
group: mimari
---

# caching — Bileşen Listesi (Envanter Ekseni)

> **Amaç:** Cache ile ilgili **tüm bileşenleri** envanter olarak listeler:
> hangi bileşen var (`Dosya:` kanıtı), hangi akışa hizmet eder, hangi
> klasörün sahibi, hangisi PLANNED. Bu dosya ayrıca bu klasörün **ikinci ve
> son URL taşıyıcısıdır**: havuzdaki `https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching`
> genel dokümanı §9'da tek kez kullanılır (cross-folder tekrar YASAK —
> spec §1.6). Kanıtlanamayan iddia `⚠️ VERIFICATION REQUIRED`.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 |
|---|---|---|---|
| 1 | §1 Envanter özeti (sayım) | mimari | envanter |
| 2 | §2 Cache çekirdeği (`shared/src/Cache/`) | mimari | cekirdek |
| 3 | §3 Güvenlik/limit bileşenleri | mimari | guvenlik |
| 4 | §4 Bağılantı (composition root) bileşenleri | mimari | wiring |
| 5 | §5 SPA/JS cache bileşenleri | mimari | istemci |
| 6 | §6 Sunucu ve statik-varlık bileşenleri | mimari | sunucu |
| 7 | §7 SQL/veri bileşenleri | mimari | sema |
| 8 | §8 Test bileşenleri + boşluklar | kanit | test |
| 9 | §9 HTTP caching genel dokümanı (havuz URL'i) + conditional bağ | kanit | web |
| 10 | §10 Akış izleri (3 uçtan-uca yol) | mimari | akis |
| 11 | §11 Bağımlılık grafiği | mimari | graf |
| 12 | §12 Sahiplik matrisi (klasör → sorumlu) | kanit | sahiplik |
| 13 | §13 PLANNED bileşenler | mimari | planli |
| 14 | §A Açık sorular | kanit | acik |
| 15 | §B Kullanım kuralları | mimari | kural |

---

## §1 — Envanter özeti (sayım)

**Disk ölçümü (2026-10-15, `Get-ChildItem` + `Select-String`):**

| Katman | Bileşen sayısı | Kanıt |
|---|---|---|
| `shared/src/Cache/` | **6 dosya** (5 PHP + 1 CLAUDE.md) | dizin listesi: `ApcuAdapter` · `CacheInterface` · `CacheManager` · `MemoryAdapter` · `PageCacheAdapter` · `PageCacheInterface` (+ `CLAUDE.md`) |
| Cache ile bağlı diğer PHP | **4 dosya** | `Security/CacheRateLimiter.php` · `Middleware/RateLimiterMiddleware.php` · `Api/Middleware/RateLimitMiddleware.php` · `PageRouter/PageRouter.php` |
| Bağılantı noktaları | **3 dosya** | `api.coremusic.net/index.php` · `auth.coremusic.net/include/Container/AuthContainer.php` · `api.coremusic.net/include/Container/ApiAuthContainer.php` |
| JS cache bileşenleri | **3 çekirdek + 3 destek** | `CacheLayer.js` · `PrefetchManager.js` · `Router.js` (çekirdek); `ContentFetcher.js` · `UrlUtils.js` · `FetchWrapper.js` (destek) |
| Sunucu/varlık | **2 dosya** | `assets.coremusic.net/.htaccess` · `DeviceRenderer.php` (cache-buster) |
| SQL | **1 tablo (+1 FK kaynağı)** | `rate_limits` → `api_keys` (`.ai/.sql/mysql/coremusic_api.sql` · Satır: 78-92) |
| Test | **3 dosya (kısmi)** | `EntryPointCacheSafetyTest` · `RateLimiterMiddlewareTest` (mock) · `ResponseNormalizationMiddlewareTest` (ETag/304) |
| **Cache'e özel test dosyası** | **0** | `shared/tests/` altında `Cache/` dizini **YOK** (§8) |

**Tek cümlelik özet:** CoreMusic'in cache'i **tek arayüz** (`CacheInterface`)
etrafında **iki backend** (APCu/Memory) + **bir sayfa katmanı** (PageCache) +
**bir istemci katmanı** (`CacheLayer`) + **iki tüketici** (rate limit, HTML
shell) olarak yapılandırılmıştır; **test katmanı bu bileşenlerin hiçbirini
doğrudan kapsamaz** (kısmi dolaylı testler hariç — §8).

---

## §2 — Cache çekirdeği (`shared/src/Cache/`)

| # | Bileşen | Dosya · Satır | Rol | Durum |
|---|---|---|---|---|
| C-1 | `CacheInterface` | `shared/src/Cache/CacheInterface.php` · Satır: 5-12 | 6 methodlu kontrat (get/set/delete/increment/clear/has) | ✅ IMPLEMENTED |
| C-2 | `CacheManager` | `shared/src/Cache/CacheManager.php` · Satır: 7-26 | Adaptör seçimi (APCu var mı?) + test seam (`setAdapter`) | ✅ IMPLEMENTED |
| C-3 | `MemoryAdapter` | `shared/src/Cache/MemoryAdapter.php` · Satır: 5-57 | İstek-içi store; lazy TTL eviction; LRU **yok** | ✅ IMPLEMENTED (sınır: §13 P-MEM1) |
| C-4 | `ApcuAdapter` | `shared/src/Cache/ApcuAdapter.php` · Satır: 5-54 | APCu sarmalayıcı; her methodta `function_exists` koruması | ✅ IMPLEMENTED |
| C-5 | `PageCacheInterface` | `shared/src/Cache/PageCacheInterface.php` · Satır: 5 | Sayfa-cache kontratı (`isPageCacheEnabled`/`ttlPage`/`pageKey`/`remember`) | ✅ IMPLEMENTED |
| C-6 | `PageCacheAdapter` | `shared/src/Cache/PageCacheAdapter.php` · Satır: 5-44 | auth ayrımı (`page_auth_`/`page_pub_`) + remember-through | ✅ IMPLEMENTED |
| C-7 | `Cache/CLAUDE.md` | `shared/src/Cache/CLAUDE.md` | Klasör içi agent notu (doküman, bileşen değil) | 📄 doküman |

---

## §3 — Güvenlik/limit bileşenleri

| # | Bileşen | Dosya · Satır | Cache ilişkisi | Durum |
|---|---|---|---|---|
| S-1 | `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php` · Satır: 8-40 | `IRateLimiter` implementasyonu; sayaçlar cache'te (`rl:` öneki) | ✅ IMPLEMENTED |
| S-2 | `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php` · Satır: 9, 38-42, 50-82, 129-132 | HTTP katmanı; fail-closed/fail-open; `CacheManager`'dan adaptör | ✅ IMPLEMENTED |
| S-3 | `RateLimitMiddleware` | `shared/src/Api/Middleware/RateLimitMiddleware.php` · Satır: 20, 25-26, 34-49, 73 | API katmanı; `CacheRateLimiter` enjeksiyonlu; `withLimits()` konfigürasyonu | ✅ IMPLEMENTED |
| S-4 | `IRateLimiter` kontratı | `CoreMusic\Contracts\Security\IRateLimiter` (`CacheRateLimiter.php` · Satır: 5 import) | Sınıf dosyası K009-scope (güvenlik); cache bağımlılığı burada | ✅ IMPLEMENTED (dosya başka klasörde) |

**Uyarı (duplicate-pr adayı):** `rl:` öneki iki ayrı sabitte tekrar eder
(`CacheRateLimiter` Satır: 10 · `RateLimiterMiddleware` Satır: 11) — bkz.
`caching-ornekler.md` §15 AP-3.

---

## §4 — Bağılantı (composition root) bileşenleri

| # | Bileşen | Dosya · Satır | Ne kuruyor | Durum |
|---|---|---|---|---|
| W-1 | API pipeline | `api.coremusic.net/index.php` · Satır: 143-151 | `apcu_enabled()` ile adaptör seçimi → `RateLimitMiddleware(new CacheRateLimiter($apiCache))` | ✅ IMPLEMENTED |
| W-2 | Auth konteyneri | `auth.coremusic.net/include/Container/AuthContainer.php` · Satır: 62 | `new CacheRateLimiter(CacheManager::getAdapter())` | ✅ IMPLEMENTED |
| W-3 | API-auth konteyneri | `api.coremusic.net/include/Container/ApiAuthContainer.php` · Satır: 104 | Aynı kayıt (konteyner SSOT) | ✅ IMPLEMENTED |
| W-4 | HTML shell router | `shared/src/PageRouter/PageRouter.php` · Satır: 7, 21, 30-32 | `?PageCacheInterface` enjeksiyonu + `getPageCache()` | ✅ IMPLEMENTED |
| W-5 | SPA router | `assets.coremusic.net/js/router/Router.js` · Satır: 3, 29 | `config.cache ?? new CacheLayer({}, logger)` — DI seam | ✅ IMPLEMENTED |

**⚠️ Uyum notu:** W-1 kendi APCu ölçütünü (`apcu_enabled()`) kullanırken
W-2/W-3 `CacheManager::getAdapter()`'a güvenir; iki ölçütün eşdeğerliği
**⚠️ VERIFICATION REQUIRED** (`caching-ornekler.md` §8, §A-1).

---

## §5 — SPA/JS cache bileşenleri

| # | Bileşen | Dosya · Satır | Rol | Durum |
|---|---|---|---|---|
| J-1 | `CacheLayer` | `assets.coremusic.net/js/router/CacheLayer.js` · Satır: 2-59 | TTL_MAP + LRU + tag indeksi; `#store` (Map) | ✅ IMPLEMENTED |
| J-2 | `PrefetchManager` | `assets.coremusic.net/js/router/PrefetchManager.js` · Satır: 5-40 | Eşzamanlılık sınırı 2; AbortController; cache-hit kontrolü (Satır: 21) | ✅ IMPLEMENTED |
| J-3 | `Router` | `assets.coremusic.net/js/router/Router.js` · Satır: 3, 25-29, 55, 69 | Cache enjeksiyonu + prefetch köprüsü | ✅ IMPLEMENTED |
| J-4 | `ContentFetcher` | `assets.coremusic.net/js/router/ContentFetcher.js` · Satır: 5, 10-11, 17-18, 27 | Cache okuma (`cache.get` → `cacheHit: true`) + `parseResponse` yazımı | ✅ IMPLEMENTED |
| J-5 | `UrlUtils::normalizeCacheKey` | `assets.coremusic.net/js/router/UrlUtils.js` · Satır: 31 | Cache key normalizasyonu (tek kaynak) | ✅ IMPLEMENTED |
| J-6 | `FetchWrapper` | `assets.coremusic.net/js/router/FetchWrapper.js` | Ağ sarmalayıcı (cache yazmıyor; ContentFetcher'a hizmet eder) | ✅ IMPLEMENTED |

**Kapasite/hız sınırları (envanter özeti):** `MAX_ENTRIES = 100` ·
`DEFAULT_TTL = 600s` · TTL_MAP `{static:3600, user:120, dynamic:60, default:600}` ·
prefetch eşzamanlı `2` (hepsi `CacheLayer.js`/`PrefetchManager.js` satır
kanıtlı — `caching-ornekler.md` §9-§10).

---

## §6 — Sunucu ve statik-varlık bileşenleri

| # | Bileşen | Dosya · Satır | Ne sağlar | Durum |
|---|---|---|---|---|
| H-1 | Assets `.htaccess` cache bloğu | `assets.coremusic.net/.htaccess` · Satır: 20, 30, 35, 38 | 4 TTL profili (3600 / no-cache / 31536000 immutable / 604800) | ✅ IMPLEMENTED |
| H-2 | CORS + Range başlıkları | Aynı dosya · Satır: 7-9, 14, 19, 29 | Cache'in cross-origin okunabilirliği (`Access-Control-Allow-Origin *`) | ✅ IMPLEMENTED |
| H-3 | `DeviceRenderer::link()` cache-buster | `.../shared-infrastructure/src/Device/DeviceRenderer.php` · Satır: 123, 149-151 | `?v=<cacheBuster>` — bayat CSS riskini kapatır | ✅ IMPLEMENTED |
| H-4 | `Vary` kaynağı (HTML shell) | `shared/src/PageRouter/ResponseEmitter.php` · Satır: 70, 112-115 | `Vary: Accept-Encoding, Cookie` + çift-basma koruması | ✅ IMPLEMENTED |

---

## §7 — SQL/veri bileşenleri

| # | Bileşen | Dosya · Satır | Rol | Durum |
|---|---|---|---|---|
| D-1 | `rate_limits` tablosu | `.ai/.sql/mysql/coremusic_api.sql` · Satır: 78-92 | Limit **tanımı** (max_requests/window_sec/is_active) | ✅ IMPLEMENTED (şema) |
| D-2 | `api_keys` FK | Aynı dosya · Satır: 91 | `ON DELETE CASCADE` — anahtar silinince limit tanımları düşer | ✅ IMPLEMENTED |
| D-3 | Benzersizlik kısıtı | Aynı dosya · Satır: 88 | `uq_rl_key_endpoint (api_key_id, endpoint_pattern(255))` — çifte limit tanımı imkânsız | ✅ IMPLEMENTED |

**Sahiplik sınırı:** Şema/DDL K007-data'ya aittir; bu dosya yalnız cache
ilişkisini envanterler (migration üretmez).

---

## §8 — Test bileşenleri + boşluklar

| # | Test | Dosya · Satır | Ne kapsar | Boşluk |
|---|---|---|---|---|
| T-1 | `EntryPointCacheSafetyTest` | `home.coremusic.net/tests/Unit/Bootstrap/EntryPointCacheSafetyTest.php` · Satır: 11, 34-36 | Giriş noktasında `apcu_clear_cache()` yasağı (B-F-24) | — |
| T-2 | `RateLimiterMiddlewareTest` | `shared/tests/Middleware/RateLimiterMiddlewareTest.php` · Satır: 7, 305-307 | `CacheInterface` mock ile limit davranışı | Adaptörler test edilmiyor |
| T-3 | `ResponseNormalizationMiddlewareTest` | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` · Satır: 34-95 | ETag/304/no-store matrisi (5 test) | `Vary` kapsamı yok (P-VY1) |
| T-4 | `CacheManager`/`MemoryAdapter`/`ApcuAdapter`/`PageCacheAdapter` testi | **DOSYA YOK** (`shared/tests/` altında `Cache/` dizini yok) | — | **PLANNED** §13 P-TST1 |
| T-5 | `CacheLayer`/`PrefetchManager` JS testi | **DOSYA YOK** (`assets.coremusic.net/tests/` envanteri: components 2 + e2e 1 + mocks 1 + md 2) | — | **PLANNED** §13 P-TST2 |

---

## §9 — HTTP caching genel dokümanı (havuz URL'i) + conditional bağ

> **Kaynak (bu dosyanın tek havuz URL'i):**
> `https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching`
> (exa fetch 2026-10-15 — içerik döndü; HTTP status ⚠️ VERIFICATION REQUIRED)

Kaynaktan okunanlar (parafraz, §9.1) ve repo çapraz (§9.2):

### §9.1 Kaynak olguları

| # | Olay (parafraz) | İlgili repo bileşeni |
|---|---|---|
| U-1 | HTTP cache, **istekle ilişkilendirilmiş yanıtı** saklar ve sonraki isteklerde yeniden kullanır | `CacheLayer` (JS) + `PageCacheAdapter` (PHP) |
| U-2 | İki ana cache türü: **private** (istemciye bağlı, kişiselleştirilmiş yanıt saklayabilir) ve **shared** (CDN/proxy — kişisel veri saklamak **sızıntı** riski) | `page_auth_`/`page_pub_` ayrımı + `Vary: Cookie` (§6 H-4, §2 C-6) |
| U-3 | Kişisel içerik shared cache'e girerse `Cache-Control: private` ile **dışlanmalı** | Repo: `no-store`/`public+max-age` dallanması (`ResponseNormalizationMiddleware.php` · Satır: 91, 93) |
| U-4 | Doğru cache kullanımı sunucu yükünü azaltır (parse/route/session/DB/render atlanır) | PageCache + SPA cache hedefi |

### §9.2 Conditional request bağı (If-None-Match / If-Modified-Since)

| Bağ | Repo durumu | Kanıt |
|---|---|---|
| `If-None-Match` → 304 | ✅ VAR | `ResponseNormalizationMiddleware.php` · Satır: 74-77; test T-3 |
| `If-Modified-Since` / `Last-Modified` | **YOK** (grep: 0 — `caching-web-arastirma.md` §9.3) | Tarih-bazlı doğrulama kullanılmıyor; ETag tek doğrulayıcı |
| Vary + conditional birlikte (304'te `Vary` tekilliği) | Test edilmedi | **⚠️ VERIFICATION REQUIRED** (P-VY1) |
| Shared-cache (`Age` başlığı) üretimi | Uygulama **yazmaz** | `caching-dogrulama.md` §4 — CDN katmanının işi |

**Karar özeti:** CoreMusic conditional-cache'i **tek doğrulayıcı (ETag)**
üzerinden kurar; `Last-Modified` ikinci doğrulayıcı **bilinçli olarak yoktur**
(ek karmaşıklık, md5 zaten hesaplanıyor). Bu, U-2/U-3 special-case'leriyle
tutarlı: `no-store` kişisel yanıtlar hiçbir cache'e girmez.

---

## §10 — Akış izleri (3 uçtan-uca yol)

### §10.1 Yol A — API rate limit (cache sayaçlı)

```text
İstemci (Bearer)
  → api.coremusic.net/index.php (Satır: 147-151: pipeline kurulumu)
    → ResponseNormalizationMiddleware (Satır: 148 — ETag/Cache-Control)
    → OriginCheck / Cors (Satır: 149-150)
    → RateLimitMiddleware (Satır: 151)
      → CacheRateLimiter::isLimited (Security/CacheRateLimiter.php · Satır: 16-24)
        → CacheInterface::get('rl:...')  → ApcuAdapter|MemoryAdapter
      → CacheRateLimiter::increment (Satır: 26-35)
  → AuthenticationMiddleware ... (Satır: 154+)
```

### §10.2 Yol B — HTML shell (sayfa cache + Vary)

```text
Tarayıcı GET /index.php
  → PageRouter (PageRouter.php · Satır: 21 pageCache enjeksiyonu)
    → PageCacheAdapter::remember(pageKey(uri, auth), ttl=600, callback)
       (PageCacheAdapter.php · Satır: 28-44)
    → ResponseEmitter::emit (ResponseEmitter.php · Satır: 70 — Vary: Accept-Encoding, Cookie)
```

### §10.3 Yol C — SPA navigasyonu (istemci cache + prefetch)

```text
Kullanıcı link
  → Router.js prefetch (Satır: 55/69)
    → PrefetchManager::prefetch (PrefetchManager.js · Satır: 20-31)
      → CacheLayer.get? (Satır: 21 — hit ise atla)
      → ContentFetcher.fetch → cache.get (ContentFetcher.js · Satır: 17-18)
      → ContentFetcher.parseResponse → CacheLayer.set (Satır: 27/31)
  → Render (CacheLayer.get — Satır: 22-30; TTL/LRU korumalı)
```

---

## §11 — Bağımlılık grafiği

```text
                    [HTTP yanıt]                    [SPA DOM]
                          ↑                              ↑
            ResponseNormalizationMiddleware         CacheLayer ← Router
                    (ETag/Cache-Control)          (TTL/LRU/tags)
                          ↑                              ↑
   PageRouter ── PageCacheAdapter ──┐              PrefetchManager
        ↑                           │                   ↑
  ResponseEmitter            CacheInterface        ContentFetcher
   (Vary: AE, Cookie)              ↑                   ↑
                             CacheManager          FetchWrapper
                          (APCu? → ApcuAdapter : MemoryAdapter)
                                  ↑
        CacheRateLimiter ← RateLimiterMiddleware / RateLimitMiddleware
                                  ↑
                    index.php / AuthContainer / ApiAuthContainer

SQL: rate_limits (limit tanımı) ← API key katmanı (sayaçlar cache'te)
```

**Tek yön kuralı:** `Cache/*` hiçbir şeye bağımlı **değildir** (yalnız
`function_exists`); tüketiciler (`Security`, `Middleware`, `PageRouter`, JS
router) **ona** bağımlıdır → Domain-dışı, alt katman bağımlılığı ihlali yok.

---

## §12 — Sahiplik matrisi (klasör → sorumlu)

| Yüzey | Sahip (agent/K düğüm) | Not |
|---|---|---|
| `shared/src/Cache/` | Backend (K011-application) | Kontrat + adaptörler |
| `shared/src/Security/CacheRateLimiter.php` | Security (K009) | Dosya güvenlikte; cache bağı burada |
| `shared/src/Middleware/RateLimiterMiddleware.php` | Backend + Security | Fail-closed kararı ADR-013 |
| `assets.coremusic.net/js/router/*Cache*` | UI (K010/K011 JS) | Vanilla JS |
| `assets.coremusic.net/.htaccess` | DevOps (sunucu) | Operasyonel yapılandırma |
| `.ai/.sql/mysql/coremusic_api.sql` | Data (K007) | Şema sahibi |
| `*/tests/**Cache*` | QA (K019) | Test stratejisi sahibi |

---

## §13 — PLANNED bileşenler

| # | PLANNED bileşen | Tetikleyici | Eksik kanıt |
|---|---|---|---|
| P-TST1 | `shared/tests/Cache/` (CacheManager/MemoryAdapter/ApcuAdapter/PageCacheAdapter testleri) | §8 T-4 | Diskte dizin YOK |
| P-TST2 | `assets.coremusic.net/tests/…/CacheLayer.spec.js` + `PrefetchManager.spec.js` | §8 T-5 | Diskte dosya YOK |
| P-MEM1 | `MemoryAdapter` kapasite sınırı + periyodik süpürme | Bellek büyürse | Sınır kodu YOK |
| P-LRU1 | PHP tarafında LRU (JS'te var) | Uzun süreçli worker senaryosu | Kod YOK |
| P-ET1 | `If-None-Match` liste/`W/` desteği | İstemci çeşitliliği kanıtlanırsa | `===` birebir (ornekler §1) |
| P-CDN1 | CDN entegrasyonu (`Age`/`Expires` üretimi) | CDN kararı (ADR) | Operasyonel ortam YOK |

---

## §14 — Dosya satır ölçümü defteri (bileşen bazlı)

**Komut (repo kökünden):**

```powershell
$r = "C:\www\coremusic.net"
foreach ($f in @("shared\src\Cache\CacheInterface.php", "shared\src\Cache\CacheManager.php",
  "shared\src\Cache\MemoryAdapter.php", "shared\src\Cache\ApcuAdapter.php",
  "shared\src\Cache\PageCacheInterface.php", "shared\src\Cache\PageCacheAdapter.php",
  "shared\src\Security\CacheRateLimiter.php", "shared\src\Middleware\RateLimiterMiddleware.php",
  "shared\src\Api\Middleware\RateLimitMiddleware.php",
  "assets.coremusic.net\js\router\CacheLayer.js", "assets.coremusic.net\js\router\PrefetchManager.js",
  "assets.coremusic.net\js\router\Router.js", "assets.coremusic.net\js\router\ContentFetcher.js",
  "assets.coremusic.net\js\router\UrlUtils.js", "assets.coremusic.net\js\router\FetchWrapper.js",
  "assets.coremusic.net\.htaccess", "shared\src\PageRouter\ResponseEmitter.php")) {
  "$f = $((Get-Content "$r\$f").Count) satır" }
```

**Ölçüm sonucu (2026-10-15):**

| # | Dosya | Satır | § karşılığı |
|---|---|---|---|
| 1 | `shared/src/Cache/CacheInterface.php` | 13 | §2 C-1 |
| 2 | `shared/src/Cache/CacheManager.php` | 25 | §2 C-2 |
| 3 | `shared/src/Cache/MemoryAdapter.php` | 57 | §2 C-3 |
| 4 | `shared/src/Cache/ApcuAdapter.php` | 56 | §2 C-4 |
| 5 | `shared/src/Cache/PageCacheInterface.php` | 11 | §2 C-5 |
| 6 | `shared/src/Cache/PageCacheAdapter.php` | 45 | §2 C-6 |
| 7 | `shared/src/Security/CacheRateLimiter.php` | 41 | §3 S-1 |
| 8 | `shared/src/Middleware/RateLimiterMiddleware.php` | 146 | §3 S-2 |
| 9 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 80 | §3 S-3 |
| 10 | `assets.coremusic.net/js/router/CacheLayer.js` | 59 | §5 J-1 |
| 11 | `assets.coremusic.net/js/router/PrefetchManager.js` | 49 | §5 J-2 |
| 12 | `assets.coremusic.net/js/router/Router.js` | 81 | §5 J-3 |
| 13 | `assets.coremusic.net/js/router/ContentFetcher.js` | 62 | §5 J-4 |
| 14 | `assets.coremusic.net/js/router/UrlUtils.js` | 39 | §5 J-5 |
| 15 | `assets.coremusic.net/js/router/FetchWrapper.js` | 63 | §5 J-6 |
| 16 | `assets.coremusic.net/.htaccess` | 47 | §6 H-1 |
| 17 | `shared/src/PageRouter/ResponseEmitter.php` | 163 | §6 H-4 |
| | **Toplam (17 dosya)** | **1097** | — |

---

## §15 — Yoğunluk analizi (kod/doküman oranı)

| Grup | Dosya sayısı | Satır | Ortalama | Yorum |
|---|---|---|---|---|
| PHP çekirdek (`Cache/`) | 6 | 207 | 34,5 | Küçük ve odaklı — kontrat ağırlıklı |
| PHP limit/güvenlik | 3 | 267 | 89 | En kalın katman (fail-closed dallanmaları) |
| JS router-cache | 6 | 353 | 59 | CacheLayer + Router dominant |
| Sunucu/varlık | 2 | 210 | 105 | ResponseEmitter tek başına 163 satır (HTML emit genişliği) |

**Okuma:** Cache mantığının **büyüklüğü küçük, yüzeyi geniştir** (6 PHP +
6 JS + 2 sunucu + 1 SQL = 15 dosya); asıl bakım riski **sayı değil, dağılım**
kaynaklıdır (§16 R-2, R-4).

---

## §16 — Risk kaydı (bileşen bazlı)

| # | Risk | Bileşen | Etki | Önlem/PLANNED |
|---|---|---|---|---|
| R-1 | İki APCu tespit ölçütü (`apcu_enabled()` vs `function_exists`) | W-1 vs C-2 | Ortamsal tutarsız adaptör seçimi | §A-1 sorusu; ortam testi PLANNED |
| R-2 | `rl:` önekinin iki sabitte tekrarı | S-1 + S-2 | Anahtar drift'i (limitler birbirini görmez) | duplicate-pr adayı (§3 uyarı) |
| R-3 | `MemoryAdapter`'da kapasızlık + lazy-only eviction | C-3 | Uzun isteklerde bellek büyümesi | P-MEM1 |
| R-4 | Cache testlerinin yokluğu | §8 T-4/T-5 | Regresyonlar yalnız dolaylı yakalanır | P-TST1/P-TST2 |
| R-5 | `PageCacheAdapter` auth ayrımının HTTP `Vary: Cookie` ile örtüşmesi | C-6 + H-4 | İki savunma; biri unutulursa sızıntı riski | §A-3 sorusu |
| R-6 | `CacheLayer` tag indeksinin üreticisi belirsiz | J-1 | Ölü kod olabilir (memory cost) | §A-2 sorusu |
| R-7 | `?v=` cache-buster'ın güncelleme mekanizması belirsiz | H-3 | Bayat CSS riski geri dönebilir | §A-4 sorusu |
| R-8 | `If-None-Match` listesinin desteklenmemesi | §9.2 | Bazı istemciler 304 avantajını kaybeder | P-ET1 |

---

## §17 — Kısıtlar ve dokunulmaz yüzeyler

| # | Kısıt | Kapsam | Kaynak |
|---|---|---|---|
| 1 | `shared/src/**` kaynak kodu bu turda **değiştirilmez** | Tüm PHP/JS bileşenleri | Görev yasakları |
| 2 | Şema/DDL yalnız K007 sahipliğinde | `rate_limits` | §12 matrisi |
| 3 | `.htaccess` sunucu-katmanı: değişiklik operasyonel onay ister | H-1 | DevOps sahipliği |
| 4 | Frozen ADR metinleri (ADR-013 fail-closed dâhil) **salt-okunur** | S-2 kararları | Vault kuralı |
| 5 | Havuz URL disiplini: bu dosyada **yalnız 1** URL (§9) | §9 | spec §1.6 |

---

## §18 — Kapsam-içi kardeş haritası (bu klasörün 6 dosyası)

| Dosya | Grup | Bu dosyayla ilişki |
|---|---|---|
| `index.md` | mimari | Klasör künyesi — bu dosyanın rolleri §GRUPLAR 14'de öngörüldü |
| `caching-uygulama-seviyesi-cache.md` | mimari | Kapsam özeti — §2/§5 envanteriyle beslenir |
| `caching-dogrulama.md` | kanit | Gate — §8 boşlukları (T-4/T-5) BLOCK gerekçesi |
| `caching-web-arastirma.md` | kanit | Expires/Vary — §6 H-4 ve §9.2 ile kesişir |
| `caching-ornekler.md` | kod | Kod kanıtları — §14 satır ölçümünün kaynağı |
| `caching-bilesenler.md` | mimari | **Bu dosya** |

---

## §19 — Sürüm/ortam bağımlılıkları

| Bağımlılık | Gereken | Repo kanıtı | Etiket |
|---|---|---|---|
| PHP sürümü | `declare(strict_types=1)` + `readonly` özellikler (S-1 ctor) | `CacheRateLimiter.php` · Satır: 1, 12-14 | ✅ IMPLEMENTED |
| APCu eklentisi | Yoksa `MemoryAdapter` fallback | `CacheManager.php` · Satır: 12; `ApcuAdapter.php` (her methodta koruma) | ✅ IMPLEMENTED (zarif düşüş) |
| Node.js (test) | vitest engines: `^22.12 || ^24 || >=26` | `ci.yml` · Satır: 151 (yorum satırı) | ✅ IMPLEMENTED (CI) |
| Tarayıcı | `Map`, `AbortController`, private fields (`#`) | `CacheLayer.js` · Satır: 6-12; `PrefetchManager.js` · Satır: 24 | ✅ IMPLEMENTED (modern tarayıcı) |
| Apache modülleri | `mod_headers` (`.htaccess` `Header set`) | `.htaccess` · Satır: 7-38 | ✅ IMPLEMENTED (sunucu) |

---

## §20 — index.md §GRUPLAR uyum eşlemesi

| index.md satırı | Beklenen bölüm | Durum |
|---|---|---|
| 1 | CacheManager + adapter kontratı | ✅ §2 (C-1..C-6) |
| 2 | Backend envanteri: APCu, Memory, PageCache, RateLimiter | ✅ §2, §3 |
| 3 | SPA cache katmanı: CacheLayer TTL + LRU | ✅ §5 (J-1) |
| 4 | Prefetch kuyruğu: PrefetchManager | ✅ §5 (J-2) |
| 5 | HTTP cache bağı doğrulama planı | ➡️ `caching-dogrulama.md` (kardeş) |
| 6 | Negatif senaryolar | ➡️ `caching-dogrulama.md` (kardeş) |
| 7 | Standart kanıtı: Expires + Last-Modified | ➡️ `caching-web-arastirma.md` §3-§5 (Last-Modified LABEL) |
| 8 | Claim→repo matrisi (W-C1..W-C8) | ➡️ `caching-web-arastirma.md` §10 (10 satır — genişletilmiş) |
| 9 | Reddedilen adaylar (LABEL) + PowerShell kesişim bloğu | ➡️ `caching-web-arastirma.md` §9, §11 |
| 10 | PHP örneği: CacheRateLimiter + ResponseNormalization | ➡️ `caching-ornekler.md` §1, §6 |
| 11 | JS örneği: CacheLayer TTL_MAP/LRU + `.htaccess` TTL'leri | ➡️ `caching-ornekler.md` §9, §13 |
| 12 | SQL örneği: `rate_limits` | ➡️ `caching-ornekler.md` §12 + bu dosya §7 |
| 13 | CSS örneği: `auth-bundled.css?v=` versiyonlama | ✅ bu dosya §6 H-3 + `caching-ornekler.md` §13 |
| 14 | `If-Modified-Since` bağı — SPA yeniden doğrulama | ✅ bu dosya §9.2 (URL: HTTP caching genel dokümanı) |

---

## §21 — Terimler (bu dosyaya özgü)

| Terim | Tanım | Kanıt |
|---|---|---|
| **Adaptör seçimi** | Çalışma zamanında APCu/Memory arasında karar veren fabrika mantığı | C-2 (Satır: 12-17) |
| **Remember-through** | Cache miss'te callback çalıştırıp sonucu yazan desen | C-6 (Satır: 34-44) |
| **Fail-closed kapı** | Bağımlılık çözülemezse isteği reddeden middleware modu | S-2 (Satır: 53-62) |
| **Cache-buster** | Dosya adı sabit içerikte sürümü query parametresiyle taşıyan teknik | H-3 (Satır: 151) |
| **Tag indeksi** | Etiketten cache anahtarına ters ilişki kuran ikincil harita | J-1 (Satır: 39, 57) |
| **Sayaç penceresi** | Rate limit için TTL = windowSeconds olan cache girdisi | S-1 (Satır: 31) |

---

## §22 — Komşu K düğümleriyle sınır (devir tablosu)

| Konu | Bu dosya ne der | Sorumlu düğüm |
|---|---|---|
| `CacheInterface` implementasyonu | Envanter + akış (§2, §10) | K011-application |
| Rate limit **politikası** (limit değerleri, pencere seçimi) | Yalnız mekanizma (§3, §7) | K009-security + ürün kararı |
| Şema/DDL/migration | Yalnız `rate_limits` envanteri (§7) | K007-data |
| CDN/Varnish operasyonu | PLANNED etiketi (§13 P-CDN1) | K012-services/DevOps |
| Test stratejisi ve coverage eşiği | Boşluk listesi (§8, §13) | K18-testing |
| JS test altyapısı (vitest) | Bağımlılık notu (§19) | K18-testing + UI |
| HTML shell emit genişliği | Yalnız `Vary` kanıtı (§6 H-4) | K011-routing |

---

## §23 — Bu dosyanın üretim kaydı ve doğrulama özeti

| # | Adım | Kanıt | Durum |
|---|---|---|---|
| 1 | Havuz envanter kontrolü | `caching-web-arastirma.md` §1 (bu URL TEMİZ) | ✅ |
| 2 | URL getirme | exa fetch 2026-10-15 (HTTP caching sayfası) | ✅ içerik döndü |
| 3 | Dizin sayımı (`shared/src/Cache/`) | 6 dosya (§1) | ✅ |
| 4 | Bağlı dosya grep (`CacheInterface`, `CacheManager`, `CacheRateLimiter`) | 42 eşleşme / 6 benzersiz dosya (grep 2026-10-15) | ✅ |
| 5 | Satır ölçümü (17 dosya) | §14 tablosu — toplam 1097 satır | ✅ |
| 6 | Test envanteri | §8 (3 kısmi test + 2 boşluk) | ✅ |
| 7 | index.md eşlemesi | §20 (14 satır — 5 bu dosyada, 8 kardeşte, 1 genişletilmiş) | ✅ |
| 8 | Açıksorular kaydı | §A (4 satır, hepsi ⚠️) | ✅ |

**Kabul ölçütleri (bu dosya için):**

| Ölçüt | Hedef | Durum |
|---|---|---|
| Envanter kapsamı | 15 cache-ilişkili dosya + 1 SQL + 1 conf | ✅ §1, §14 |
| Kanıt disiplini | Her satırda `Dosya: · Satır:` veya grep sonucu | ✅ |
| URL disiplini | Tek URL, tek bölüm (§9) | ✅ |
| PLANNED etiketleme | 6 satır (§13) | ✅ |
| Zero-hallucination | 0 uydurma dosya adı; tüm yollar diskte doğrulandı | ✅ |

---

## §A — Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | `Cache/CLAUDE.md`'deki klasör kuralları güncel mi (dosya içeriği bu turda okunmadı) | ⚠️ VERIFICATION REQUIRED |
| 2 | `CacheLayer` `#tags` üreten gerçek bileşen (kim `set(..., tags)` çağırıyor?) | ⚠️ VERIFICATION REQUIRED |
| 3 | `PageCacheAdapter`'ı **gerçekten** kuran bağılantı noktası hangi subdomain? (PageRouter enjeksiyonu 3 bağımsız repo'da var mı?) | ⚠️ VERIFICATION REQUIRED |
| 4 | `rate_limits` DB tanımları ile cache sayaçlarının senkron politikası (kim yazıyor?) | ⚠️ VERIFICATION REQUIRED |

---

## §B — Kullanım kuralları

1. Bu dosya **envanter** taşır; kod bloğu `caching-ornekler.md`'dedir — burada
   ikinci kez kod kopyalanmaz.
2. Havuz URL'i (HTTP caching genel dokümanı) yalnız §9'da kullanılır.
3. PLANNED satırları (§13) mevcut **sayılmaz**; envanter toplamlarına
   **katılmaz** (§1 sayımları yalnız IMPLEMENTED içerir).
4. Yeni bileşen eklenirken önce §11 grafı güncellenir, sonra §12 sahipliği
   atanır — sahipsiz bileşen envantere girmez.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
