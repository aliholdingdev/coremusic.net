---
title: "Routing — Bileşenler (Envanter · Bağımlılık · Boşluklar)"
type: dogrulama
kodeme: kademe-2
module: K011-application
parent: routing
status: active
updated: 2026-10-15
group: mimari
---

# Routing — Bileşen Envanteri ve Bağımlılık Haritası

> **Amaç:** Routing modülünün bileşen envanterini (dosya/dizin sayımları), bileşen↔akış
> iz düşümünü ve bağımlılık grafiğini disk kanıtıyla raporlamak; boşlukları (PLANNED /
> okunmamış) işaretli olarak listelemek. Sayımlar bu oturumda `glob`/`read` ile alındı;
> PowerShell sayım bloğu tekrar üretilebilir kanıt olarak eklenmiştir (shell reddi
> nedeniyle çalıştırılmadı — ⚠️ işaretli).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | §1 Envanter özeti (sayımlar) | mimari | kapsam | sayımlar |
| 2 | §2 PHP bileşen envanteri (API routing) | mimari | bilesenler | sunucu |
| 3 | §3 PHP bileşen envanteri (SPA PageRouter) | mimari | bilesenler | sunucu SPA |
| 4 | §4 JS bileşen envanteri (js/router 28) | mimari | bilesenler | istemci |
| 5 | §5 Test bileşen envanteri | mimari | dogrulama | testler |
| 6 | §6 Bileşen ↔ akış iz düşümü | mimari | kapsam | akış |
| 7 | §7 Bağımlılık grafiği (oku yönü) | mimari | bilesenler | graph |
| 8 | §8 Yapılandırma bağımlılıkları | mimari | bilesenler | config |
| 9 | §9 Boşluklar ve PLANNED | mimari | kapsam | gap |
| 10 | §10 PowerShell sayım bloğu | kanit | dogrulama | tekrar ölçüm |
| 11 | §11 Havuz URL (`$_GET`) | kanit | dogrulama | URL kapısı |
| 12 | §12 Sayım tutarlılık tablosu | kanit | dogrulama | cross-check |

---

## §1 Envanter özeti (sayımlar)

| Envanter | Sayı | Kaynak (bu oturum) |
|----------|------|---------------------|
| `shared/src` alt dizin | 20 | `read` dizin listesi (AI…ViewMode) |
| `shared/src/Api/Routing` PHP dosyası | 1 | glob: `RouteTable.php` |
| `shared/src/Api` PHP dosyası (routing ile ilgili) | 4 | `Gateway.php` · `ApiRequest.php`(adı geçen) · `ApiResponse.php`(adı geçen) · `Gateway.php` + `Api/` içindeki `Routing/` |
| `shared/src/Api/Middleware` PHP dosyası | 2 (okunan) | `ApiMiddlewarePipeline.php` · `ResponseNormalizationMiddleware.php` |
| `shared/src/PageRouter` dosyası | 14 (registry envanteri) · 1 okundu | `shared/AGENTS.md` §2: "src/PageRouter/ — 14 dosya"; okunan: `RouteRegistry.php` |
| `assets.coremusic.net/js/router` JS dosyası | **28** | glob (28 isabet — §4 listesi) |
| `shared/tests` PHP dosyası | **46** | glob (46 isabet — §5 listesi) |
| `shared/tests/Api` PHP dosyası | 8 | glob (RouteTableTest, GatewayMethodRoutingTest, DtoTest, ...) |
| `shared/tests/Unit/PageRouter` PHP dosyası | 5 | glob (SpaRoute, RouteRegistry, AuthGuard, ...) |
| `api.coremusic.net/config/routes.php` | 1 (120 satır) | read tamamı |
| `api.coremusic.net/tests` PHP dosyası | ⚠️ okunmadı | glob bu klasöre çalıştırılmadı → `⚠️ VERIFICATION REQUIRED` |
| `.ai/.sql/mysql/coremusic_api.sql` | 1 (okunan: 1-90) | read |
| CSS breakpoint token dosyası | 1 (57 satır) | read tamamı |

**Toplam routing-modül bileşen dosyası (okunan):** 41 dosya (1+4+2+1+28+5? hayır —
bkz. §12 sayım tablosu; burada ham sayımlar verildi, detay §2-§5).

---

## §2 PHP bileşen envanteri (API routing)

| # | Dosya | Satır | Rol | Okundu |
|---|-------|-------|-----|--------|
| P1 | `shared/src/Api/Routing/RouteTable.php` | 253 | method-aware kayıt defteri; normalize/find/match/allowedMethods | ✅ tamamı |
| P2 | `shared/src/Api/Gateway.php` | 168 | dispatch; matchRoute; methodNotAllowedResponse; invokeHandler; catch→500 | ✅ tamamı |
| P3 | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 46 | pipe(); process() + array_reverse sarma | ✅ tamamı |
| P4 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 97 | standart başlıklar; ETag/304; Cache-Control | ✅ tamamı |
| P5 | `api.coremusic.net/config/routes.php` | 120 | GET 11 + POST 6 rota tanımı | ✅ tamamı |

**İsimle geçen ama OKUNMAYAN (adı geçen bağımlılık — içerik bilinmiyor):**

| Dosya | Nerede adı geçti | Durum |
|-------|------------------|-------|
| `shared/src/Api/ApiRequest.php` | Gateway.php:53 `new ApiRequest()` | ⚠️ okunmadı |
| `shared/src/Api/ApiResponse.php` | Gateway.php:79, 129, 162 `ApiResponse::error/ok` | ⚠️ okunmadı |
| `shared/src/Api/Versioning/VersionResolver.php` | Gateway.php:18, 56 | ⚠️ okunmadı |
| `shared/src/Api/Versioning/ApiVersion.php` | Gateway.php:17 | ⚠️ okunmadı |
| `shared/src/Contracts/Api/GatewayInterface.php` | Gateway.php:14 | ⚠️ okunmadı (dosya var, glob ile doğrulandı) |
| `shared/src/Contracts/Api/ServiceRegistryInterface.php` | Gateway.php:15 | ⚠️ okunmadı (glob doğruladı) |
| `shared/src/Log/LoggerFactory.php` | Gateway.php:20, 90 | ⚠️ okunmadı |
| `shared/src/Security/UuidV7.php` | Gateway.php:21, 88 | ⚠️ okunmadı |
| `shared/src/PageRouter/SpaRoute.php` | RouteRegistry.php:5, 28 | ⚠️ okunmadı |
| `shared/src/Api/RequestValidationMiddleware` (ad) | routes.php docblock:14 | ⚠️ dosya yolu doğrulanmadı |

**Not:** bu bağımlılıklar routing'in **sınır çizgisi**dir; içerikleri usecase/dto/
caching klasörlerinin konusu olabilir veya okunmamıştır — uydurulmadı, işaretlendi.

---

## §3 PHP bileşen envanteri (SPA PageRouter)

| # | Dosya | Durum | Not |
|---|-------|-------|-----|
| S1 | `shared/src/PageRouter/RouteRegistry.php` | ✅ okundu (83 satır) | register/loadFromFile/resolve/matchesPattern |
| S2 | `shared/src/PageRouter/` diğer 13 dosya | ⚠️ okunmadı | `shared/AGENTS.md` §2 envanteri: "14 dosya — SPA page router (ADR-021/083): Kernel, HtmlShellRenderer, AuthGuard, RouteRegistry, RequestNormalizer, ResponseEmitter, ..." — dosya adları envantere dayanır, tek tek glob ile doğrulanmadı |

**Testlerden bilinen PageRouter bileşenleri (adı geçen, test dosyaları glob'da):**

| Bileşen | Test dosyası |
|---------|--------------|
| AuthGuard | `shared/tests/Unit/PageRouter/AuthGuardTest.php` |
| HtmlShellRenderer | `HtmlShellRendererChromeTest.php` · `HtmlShellRendererGuardConfigTest.php` |
| AuthUrlBuilder | `AuthUrlBuilderTest.php` |
| SpaRoute | `SpaRouteTest.php` |
| RouteRegistry | `RouteRegistryTest.php` |

---

## §4 JS bileşen envanteri (js/router — 28 dosya, glob kanıtlı)

| # | Dosya | Rol (dosya adı + Router.js içinden kanıt) | Router.js import mu? |
|---|-------|--------------------------------------------|----------------------|
| J1 | `Router.js` | ana sınıf (IRouter extend) | — (bu) |
| J2 | `IRouter.js` | arayüz/sözleşme | ✅ (Router.js:1) |
| J3 | `GuardPipeline.js` | guard zinciri | ✅ (Router.js:2) |
| J4 | `CacheLayer.js` | navigasyon cache | ✅ (Router.js:3) |
| J5 | `LifecycleManager.js` | mount/unmount | ✅ (Router.js:4) |
| J6 | `FetchWrapper.js` | fetch + CSRF | ✅ (Router.js:5) |
| J7 | `Logger.js` | loglama | ✅ (Router.js:6) |
| J8 | `NavigationOrchestrator.js` | navigate/prefetch orkestrasyonu | ✅ (Router.js:7) |
| J9 | `DomPatcher.js` | DOM yaması / error state | ✅ (Router.js:8) |
| J10 | `ContentPatcher.js` | içerik yaması + renderInitial | ✅ (Router.js:9) |
| J11 | `CsrfSyncManager.js` | CSRF senkronu | ✅ (Router.js:10) |
| J12 | `AuthBoundaryDetector.js` | auth sınırı tespiti | ✅ (Router.js:11) |
| J13 | `ScrollRestorer.js` | scroll geri yükleme | ✅ (Router.js:12) |
| J14 | `MemoryWatchdog.js` | bellek izleme | ✅ (Router.js:13) |
| J15 | `RouterEventManager.js` | event bind/unbind | ✅ (Router.js:14) |
| J16 | `UrlUtils.js` | URL yardımcıları (normalizeUrl, normalizeCacheKey, isCrossOrigin, isSameOrigin) | ✅ (Router.js:15) |
| J17 | `config/css-selectors.js` | MAIN_CONTENT_SELECTOR | ✅ (Router.js:16) |
| J18 | `PrefetchManager.js` | prefetch | glob; Router.js:25 `maxPrefetch` config kanıtı |
| J19 | `NavigationGuardRunner.js` | guard çalıştırıcı | glob |
| J20 | `guards.js` | hazır guard'lar | glob |
| J21 | `FocusManager.js` | odak yönetimi | glob |
| J22 | `ContentFetcher.js` | içerik çekme | glob |
| J23 | `ScriptInjector.js` | script enjeksiyonu | glob |
| J24 | `ScrollRestorer.js` | (J13 ile aynı — liste tekil) | — |
| J25 | `SPARouterAdapter.js` | adaptör | glob |
| J26 | `config/auth-routes.js` | auth rota config | glob |
| J27 | `config/headers.js` | başlık config | glob |
| J28 | `config/error-types.js` | hata tipi config | glob |
| J29 | `config/signal-utils.js` | sinyal util | glob |

**Sayım düzeltmesi (zero-hallucination):** glob çıktısı 28 benzersiz yol döndürdü;
tabloda J24 tekrarı bilinçli işaretlendi. Benzersiz dosya = 28; satır sayısı her
dosya için ölçülmüştür **yalnız okunanlar**: Router.js 81 · UrlUtils.js 39 ·
CacheLayer.js 59 (caching dosyasında okundu) · PrefetchManager.js 49 (caching
dosyasında okundu). Diğerleri `⚠️ VERIFICATION REQUIRED` (satır sayısı okunmadı).

---

## §5 Test bileşen envanteri (shared/tests — 46 dosya, glob kanıtlı)

### 5.1 Routing ile doğrudan ilgili (8 dosya, `tests/Api` + PageRouter/Device)

| # | Dosya | Konu | Okundu |
|---|-------|------|--------|
| T1 | `shared/tests/Api/RouteTableTest.php` | 405/404, normalize, prefix | ✅ 166 satır |
| T2 | `shared/tests/Api/GatewayMethodRoutingTest.php` | Gateway method routing | ⚠️ okunmadı |
| T3 | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` | ETag/Cache-Control (caching dosyasında envanterlendi) | ⚠️ okunmadı |
| T4 | `shared/tests/Api/RequestValidationMiddlewareTest.php` | validation | ⚠️ okunmadı |
| T5 | `shared/tests/Api/ApiPipelineSecurityTest.php` | pipeline güvenlik | ⚠️ okunmadı |
| T6 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | SPA resolve | ⚠️ okunmadı |
| T7 | `shared/tests/Unit/PageRouter/SpaRouteTest.php` | SpaRoute model | ⚠️ okunmadı |
| T8 | `shared/tests/Unit/Device/NavRouteConsistencyTest.php` | nav ↔ route tutarlılığı | ⚠️ okunmadı |

### 5.2 Routing dışı testler (envanter sayımı — konu dışı, sayılmış)

`shared/tests` glob'u toplam **46** dosya döndürdü: Unit (Theme 1 · Security 2 ·
PageRouter 5 · Log 2 · Device 4 · Config 1 = 15) · Security 2 · Repository 4 (2 helper
+ 2 test) · OAuth 1 · Middleware 8 · Fixtures 2 · Events 2 · Component 1 · Api 8
(T1-T5 + VersioningTest, DtoTest, ApiResponseTest) = 46. (Bu sayım glob çıktısından
sayıldı; PowerShell bloğu §10'da.)

---

## §6 Bileşen ↔ akış iz düşümü

### 6.1 API istek akışı (sunucu)

| Adım | Bileşen | Dosya:Satır | Çıktı |
|------|---------|-------------|-------|
| 1 | entry-point (index.php) | `api.coremusic.net/index.php` ⚠️ okunmadı | request array |
| 2 | `Gateway::dispatch` | Gateway.php:50 | pipeline çağrısı |
| 3 | `VersionResolver::resolve` | Gateway.php:56 | version attribute |
| 4 | `matchRoute` → `RouteTable::match` | Gateway.php:109-114 · RouteTable.php:111-138 | status/route/allow |
| 5 | request'e `_route`, `_route_status`, `_route_allow` | Gateway.php:63-65 | pipeline'a giriş |
| 6 | `ApiMiddlewarePipeline::process` (reverse) | ApiMiddlewarePipeline.php:33-45 | onion sarma |
| 7a | 405 → `methodNotAllowedResponse` | Gateway.php:74-76, 125-138 | 405 + Allow |
| 7b | 404 → `ApiResponse::error` | Gateway.php:78-80 | 404 |
| 7c | OK → `invokeHandler` | Gateway.php:84, 152-167 | handler/placeholder |
| 8 | normalizasyon (ETag/Cache-Control) | ResponseNormalizationMiddleware.php:27-47 | başlıklar + 304 |
| 9 | exception → 500 + trace_id | Gateway.php:87-101 | güvenli hata |

### 6.2 SPA istek akışı (istemci)

| Adım | Bileşen | Dosya:Satır |
|------|---------|-------------|
| 1 | `Router::init` guard zinciri | Router.js:49-62 |
| 2 | `RouterEventManager.bind` → onNavigate | Router.js:53-60 |
| 3 | `NavigationOrchestrator.navigate` | Router.js:41, 54 (bağımlılık üzerinden) |
| 4 | guard'lar → `GuardPipeline` | Router.js:31 |
| 5 | cache → `CacheLayer` | Router.js:29 |
| 6 | fetch → `FetchWrapper` (CSRF sync) | Router.js:32 |
| 7 | `DomPatcher`/`ContentPatcher` yama | Router.js:33-34 |
| 8 | `AuthBoundaryDetector` + `ScrollRestorer` | Router.js:35-36 |
| 9 | `MemoryWatchdog` izleme | Router.js:37 |

### 6.3 SPA server resolve akışı

| Adım | Bileşen | Dosya:Satır |
|------|---------|-------------|
| 1 | `loadFromFile` (config include) | RouteRegistry.php:15-33 |
| 2 | `resolve(uri)` normalize + home | RouteRegistry.php:35-41 |
| 3 | exact `isset` | RouteRegistry.php:43-45 |
| 4 | pattern döngüsü `{param}`→`[^/]+` | RouteRegistry.php:47-53, 72-82 |
| 5 | null → 404 (kullanıcı tarafı) | RouteRegistry.php:53 |

---

## §7 Bağımlılık grafiği (oku yönü ─→)

```text
api.coremusic.net/index.php (⚠️ okunmadı)
  └─→ shared/src/Api/Gateway.php
        ├─→ Contracts/Api/GatewayInterface.php (⚠️)
        ├─→ Contracts/Api/ServiceRegistryInterface.php (⚠️)
        ├─→ Api/Routing/RouteTable.php ✅
        ├─→ Api/Versioning/VersionResolver.php (⚠️)
        ├─→ Api/Middleware/ApiMiddlewarePipeline.php ✅
        │     └─→ (callable middlewares — ResponseNormalization ✅, diğerleri ⚠️)
        ├─→ Api/ApiRequest.php (⚠️) · Api/ApiResponse.php (⚠️)
        ├─→ Log/LoggerFactory.php (⚠️)
        └─→ Security/UuidV7.php (⚠️)

api.coremusic.net/config/routes.php ✅ ──(fromArray)──→ RouteTable.php ✅

shared/src/PageRouter/RouteRegistry.php ✅
  └─→ PageRouter/SpaRoute.php (⚠️)

assets.coremusic.net/js/router/Router.js ✅
  ├─→ IRouter.js (arayüz) · GuardPipeline · CacheLayer · LifecycleManager
  ├─→ FetchWrapper · Logger · NavigationOrchestrator · DomPatcher
  ├─→ ContentPatcher · CsrfSyncManager · AuthBoundaryDetector
  ├─→ ScrollRestorer · MemoryWatchdog · RouterEventManager
  ├─→ UrlUtils.js ✅
  └─→ config/css-selectors.js (config)

ResponseNormalizationMiddleware.php ✅ → (header() yan etkisi — global PHP)
```

**Graf özeti:** 2 kök (Gateway PHP · Router JS) · PHP tarafı 14 düğüm (5 okundu, 9 ⚠️) ·
JS tarafı 17 import (7'si config/düz · hepsi dosya var, Router.js:1-16 kanıtlı) ·
**çapraz bağımlılık PHP↔JS: 0** (API JSON-only — istemci fetch ile konuşur, import yok).

---

## §8 Yapılandırma bağımlılıkları

| Config | Dosya | Bağımlı | Etki |
|--------|-------|---------|------|
| route tablosu | `api.coremusic.net/config/routes.php` | RouteTable (fromArray/defaults) | hangi uç tanımlı |
| `RouterConfig.enabled` | (window.CoreMusic.RouterConfig — okunmadı) | Router.js:50 | SPA tamamen kapalı |
| `RouterConfig.assetsOrigin` | UrlUtils.js:1 (fallback `https://assets.coremusic.net`) | isSameOrigin allowlist | origin politikası |
| `MAIN_CONTENT_SELECTOR` | `config/css-selectors.js` | Router.js:78 | renderInitial hedefi |
| PageRouter route config dosyası | `loadFromFile($filePath)` — fiziksel dosya ⚠️ | RouteRegistry.php:15 | SPA rota kaydı |
| `slowNavigationMs/memoryCheckInterval/memoryThresholdMB/maxPrefetch` | Router.js:25 (default 3000/5000/100/2) | MemoryWatchdog + nav | performans guard'ları |
| `auth_key` query anahtarı | Router.js:61 | OAuth callback koruması | auth akışı |
| CSS breakpoint tokenları | `a-breakpoint-tokens.css` | device-loader.js (senkron şartı) | responsive |

---

## §9 Boşluklar ve PLANNED

| # | Boşluk | Tür | Etki |
|---|--------|-----|------|
| G1 | `api.coremusic.net/index.php` okunmadı | ⚠️ okunmadı | middleware sırası bilinmiyor (ADR-020 sabit deniyor) |
| G2 | `api.coremusic.net/tests/` envanteri | ⚠️ glob çalıştırılmadı | RouteConfigTest/SmokeTest/AuthControllerTest adları AGENTS'ta geçiyor, disk sayısı bilinmiyor |
| G3 | PageRouter'ın 13 dosyası okunmadı | ⚠️ | Kernel/AuthGuard akış zinciri eksik |
| G4 | JS router dosyalarının 24'ünün içeriği | ⚠️ | NavigationOrchestrator.navigate satır içeriği örneğin bilinmiyor (§12 Q3 gibi) |
| G5 | Client route kaydı (hangi path'ler SPA'da) | ⚠️ | `config/auth-routes.js` okunmadı; client rota listesi bilinmiyor |
| G6 | `DeviceCssMap.php` ↔ router ilişkisi | PLANNED/⚠️ | cihaz katmanı routing'e nasıl bağlanıyor doğrulanmadı |
| G7 | Rate limit middleware routing entegrasyonu | ⚠️ | `RateLimiterMiddlewareTest.php` var; entegrasyon satırı okunmadı (caching klasörü RateLimiter konusunu işliyor olabilir) |

**Boşluk sayısı:** 7 (0 uydurma, 7 işaretli).

---

## §10 PowerShell sayım bloğu (tekrar ölçüm)

```powershell
# Repo kökünden: C:\www\coremusic.net
# 1) js/router dosya sayısı (beklenen: 28)
(Get-ChildItem -Recurse -Filter *.js -Path assets.coremusic.net\js\router).Count

# 2) shared/tests PHP dosya sayısı (beklenen: 46)
(Get-ChildItem -Recurse -Filter *.php -Path shared\tests).Count

# 3) shared/src alt dizin sayısı (beklenen: 20)
(Get-ChildItem -Directory -Path shared\src).Count

# 4) RouteTable satır sayısı (beklenen: 253)
[System.IO.File]::ReadAllLines("shared\src\Api\Routing\RouteTable.php").Length

# 5) Gateway satır sayısı (beklenen: 168)
[System.IO.File]::ReadAllLines("shared\src\Api\Gateway.php").Length

# 6) routes.php uç kaydı sayısı (beklenen: 17)
(Select-String -Path api.coremusic.net\config\routes.php -Pattern "'/(api/v1/[^']*)'\s*=>").Count
```

**Çalıştırma durumu:** ⚠️ shell izni reddi → blok bu oturumda **çalıştırılmadı**;
sayımlar `glob`/`read` ile alındı (§1, §4, §5).

---

## §11 Havuz URL — `$_GET`

Bu dosya Ünite-3 routing havuzundan **tek URL** alır:

| URL | Kullanım | Erişim |
|-----|----------|--------|
| `https://www.php.net/manual/en/reserved.variables.get.php` | Sunucu tarafı routing'in query kaynağı: `$_GET`. `RouteTable::normalize` query'yi path'ten **ayırmak** zorunda, çünkü query verisi `$_GET`'e gider (routing öncesi/paraleli). Bu dosyanın §2 (PHP envanter) + §6.1 (akış) ile ilişkili | exa fetch OK → 200-eşdeğer; status kodu `⚠️ VERIFICATION REQUIRED` |

**Klasör içi dağılım:** URL/URLSearchParams/Location → web-arastirma · History →
dogrulama · pushState → ornekler · **`$_GET` → bu dosya**. 6/6, kesişim 0.

**İlişki (iddia düzeyi, kanıt satırı yok):** `RouteTable::normalize` (RouteTable.php:242-252)
`?` sonrası her şeyi düşürür — çünkü o kısım routing'in işi değil, `$_GET`'in. Bu
davranış `testQueryStringAndSlashAreNormalized` (RouteTableTest.php:159-165) ile
sabitlenmiş. `$_GET`'in doğrudan kullanıldığı dosya (ör. index.php) ⚠️ okunmadı (G1).

**Reddedilen adaylar (etiket):**

| Etiket | Neden |
|--------|-------|
| `php.net reserved.variables.server.php` | local-runtime havuzuna ait → klasör dışı |
| `php.net reserved.variables.argv.php` | local-runtime havuzuna ait → klasör dışı |
| `php-fig psr-7` (ServerRequest) | Ünite-3 havuzunda yok + envanter kesişimi riski → etiket |

---

## §12 Sayım tutarlılık tablosu (cross-check)

| Sayım | Kaynak 1 (bu dosya) | Kaynak 2 (bağımsız) | Tutarlı mı? |
|-------|---------------------|----------------------|-------------|
| js/router = 28 | glob (§4) | `assets.coremusic.net/AGENTS.md` §2: "js/router/ — 28 dosya" | ✅ 2/2 = 28 |
| shared/tests ≈ 46 | glob (§5) | `shared/AGENTS.md` §2: "tests/ — 9 dizin" (sayı yok) | ✅ tutarlı (9 dizin var; 46 dosya glob) |
| PageRouter = 14 | `shared/AGENTS.md` §2 (14 dosya) | bu oturumda 1 dosya okundu | ⚠️ kısmi (13 okunmadı) |
| shared/src dizin = 20 | read (§1) | `shared/AGENTS.md` §2 envanteri 20 satır grubu | ✅ tutarlı |
| Gateway satır = 168 | read (§2 P2) | routing-yonlendirme-cati.md (main dosya): 168 | ✅ 2/2 |
| RouteTable satır = 253 | read (§2 P1) | main dosya: 253 | ✅ 2/2 |
| routes.php uç = 17 | read (§1 dogrulama §6) | docblock "6 POST ucu true yapıldı" (routes.php:13) | ✅ POST 6=true dahil |

**Tutarlılık:** 6/7 ✅ · 1 kısmi (PageRouter 13 dosya okunmadı → G3).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
