---
title: "K011 · command-query — bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/command-query — Bileşen Envanteri

**Amaç:** Bu belge `command-query` klasörünün (API omurgası + SPA router) gerçek bileşen
envanterini disk ölçümüyle çıkarır: dosya/satır sayıları, bağımlılık grafiği, rota tablosu
ve boşluklar. Tüm sayımlar 2026-10-15'te `Get-ChildItem` + `[System.IO.File]::ReadAllLines()`
ile doğrudan diskten alınmıştır; uydurma iddia yoktur (zero-hallucination).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | Api/ PHP omurgası | mimari | envanter | 34 dosya · satır bazlı · alt dizin |
| §2 | js/router omurgası | mimari | envanter | 28 dosya · satır bazlı |
| §3 | Test envanteri | mimari | kanit | 19 test dosyası · satır bazlı |
| §4 | Bağımlılık grafiği | mimari | akış | Gateway → pipeline → RouteTable → handler |
| §5 | RouteTable rotaları | mimari | rota | 6 GET rota · metot haritası |
| §6 | Middleware pipeline | mimari | akış | callable pipeline · ters sıralama |
| §7 | Registry + Versioning | mimari | servis | ServiceRegistry · VersionResolver |
| §8 | Boşluklar | mimari | boşluk | PLANNED / ⚠️ VERIFICATION REQUIRED |
| §9 | Açık sorular | mimari | spec §8 | Kullanıcı onayı kaydı dahil |

---

## §1 — Api/ PHP omurgası (envanter)

`shared/src/Api/` altında **34 PHP dosyası**, toplam **3677 satır** (2026-10-15 ölçümü).

### §1.1 Alt dizin özeti

| # | Alt dizin | Dosya | Toplam satır | Rol |
|---|-----------|-------|--------------|-----|
| 1 | `Api/` (kök) | 3 | 456 | Request/Response temel + Gateway |
| 2 | `Api/Auth/` | 1 | 216 | Oturum yönetimi |
| 3 | `Api/Bff/` | 5 | 390 | Backend-for-Frontend katmanları |
| 4 | `Api/Dto/Request/` | 6 | 476 | İstek DTO'ları |
| 5 | `Api/Dto/Response/` | 5 | 523 | Yanıt DTO'ları |
| 6 | `Api/Middleware/` | 7 | 788 | Pipeline + 6 middleware |
| 7 | `Api/Registry/` | 3 | 352 | Servis kaydı ve sağlık |
| 8 | `Api/Routing/` | 1 | 253 | RouteTable |
| 9 | `Api/Versioning/` | 3 | 223 | API sürüm çözümleme |
| — | **Toplam** | **34** | **3677** | — |

### §1.2 Dosya bazlı satır sayıları

#### §1.2.1 Kök (3 dosya — 456 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `ApiRequest.php` | 157 | İstek sarmalayıcı |
| 2 | `ApiResponse.php` | 131 | Yanıt sarmalayıcı (5 statik fabrika) |
| 3 | `Gateway.php` | 168 | Tek giriş noktası (dispatch) |

##### §1.2.1.1 ApiRequest metotları (157 satır)

| # | Metot | Erişim | Satır |
|---|-------|--------|-------|
| 1 | `__construct` | public | 26 |
| 2 | `getMethod` | public | 38 |
| 3 | `getUri` | public | 46 |
| 4 | `getBody` | public | 54 |
| 5 | `getHeader` | public | 62 |
| 6 | `getQueryParams` | public | 76 |
| 7 | `getAttribute` | public | 84 |
| 8 | `withAttribute` | public | 92 |
| 9 | `parseUri` | private | 101 |
| 10 | `parseHeaders` | private | 116 |
| 11 | `parseBody` | private | 137 |

##### §1.2.1.2 ApiResponse metotları (131 satır — statik fabrika)

| # | Metot / Sabit | Erişim | Satır | Döner |
|---|---------------|--------|-------|-------|
| 1 | `API_VERSION = '1.0.0'` | private const | 21 | — |
| 2 | `ok(array $data = [], int $statusCode = 200)` | public static | 26 | `array` |
| 3 | `error(...)` | public static | 45 | `array` |
| 4 | `paginated(...)` | public static | 73 | `array` |
| 5 | `noContent()` | public static | 104 | `array` |
| 6 | `created(array $data)` | public static | 116 | `array` |

> **Kullanım kanıtı:** `Gateway.php:79` → `return ApiResponse::error('NOT_FOUND', 'Route not found', 404);`

##### §1.2.1.3 Gateway metot haritası (168 satır)

| # | Metot | Erişim | Satır |
|---|-------|--------|-------|
| 1 | `__construct` | public | 28 |
| 2 | `getServiceRegistry` | public | 39 |
| 3 | `dispatch` | public | 50 |
| 4 | `matchRoute` | private | 109 |
| 5 | `methodNotAllowedResponse` | private | 125 |
| 6 | `invokeHandler` | private | 152 |

#### §1.2.2 Auth (1 dosya — 216 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Auth/ApiSessionManager.php` | 216 | API oturumu (session) yönetimi |

#### §1.2.3 Bff (5 dosya — 390 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Bff/BffLayer.php` | 83 | Bff soyut katmanı |
| 2 | `Bff/DesktopBff.php` | 66 | Masaüstü Bff |
| 3 | `Bff/EmbeddedBff.php` | 76 | Gömülü (embedded) Bff |
| 4 | `Bff/MobileBff.php` | 101 | Mobil Bff |
| 5 | `Bff/SpaBff.php` | 64 | SPA Bff |

#### §1.2.4 Dto/Request (6 dosya — 476 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Dto/Request/LoginRequest.php` | 72 | Giriş isteği |
| 2 | `Dto/Request/MusicSearchRequest.php` | 94 | Müzik arama + clamp (`min`/`max`) |
| 3 | `Dto/Request/PlaylistAddTrackRequest.php` | 72 | Playlist'e şarkı ekleme |
| 4 | `Dto/Request/PlaylistCreateRequest.php` | 72 | Playlist oluşturma |
| 5 | `Dto/Request/RegisterRequest.php` | 94 | Kayıt isteği |
| 6 | `Dto/Request/UserUpdateRequest.php` | 72 | Kullanıcı güncelleme |

#### §1.2.5 Dto/Response (5 dosya — 523 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Dto/Response/LoginResponse.php` | 83 | Giriş yanıtı |
| 2 | `Dto/Response/MusicResponse.php` | 116 | Müzik yanıtı |
| 3 | `Dto/Response/PaginationResponse.php` | 114 | Sayfalama yanıtı |
| 4 | `Dto/Response/PlaylistResponse.php` | 94 | Playlist yanıtı |
| 5 | `Dto/Response/UserResponse.php` | 116 | Kullanıcı yanıtı |

#### §1.2.6 Middleware (7 dosya — 788 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Middleware/ApiMiddlewarePipeline.php` | 46 | Callable pipeline çekirdeği |
| 2 | `Middleware/AuthenticationMiddleware.php` | 134 | Kimlik doğrulama |
| 3 | `Middleware/AuthorizationMiddleware.php` | 111 | Yetkilendirme |
| 4 | `Middleware/RateLimitMiddleware.php` | 80 | Hız sınırlama |
| 5 | `Middleware/RequestValidationMiddleware.php` | 175 | İstek doğrulama |
| 6 | `Middleware/ResponseNormalizationMiddleware.php` | 97 | Yanıt normalizasyonu |
| 7 | `Middleware/Validator.php` | 145 | Doğrulama yardımcıları |

#### §1.2.7 Registry (3 dosya — 352 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Registry/ServiceDefinition.php` | 120 | Servis tanımı |
| 2 | `Registry/ServiceHealth.php` | 100 | Sağlık durumu |
| 3 | `Registry/ServiceRegistry.php` | 132 | Servis kaydı + keşif |

#### §1.2.8 Routing (1 dosya — 253 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Routing/RouteTable.php` | 253 | Rota tablosu + eşleştirme |

#### §1.2.9 Versioning (3 dosya — 223 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `Versioning/ApiVersion.php` | 24 | Sürüm sabiti (en küçük dosya) |
| 2 | `Versioning/VersionRegistry.php` | 120 | Sürüm kaydı |
| 3 | `Versioning/VersionResolver.php` | 79 | Sürüm çözümleme |

### §1.3 Ölçüm notu (metod)

| # | Komut | Sonuç |
|---|-------|-------|
| 1 | `Get-ChildItem shared\src\Api -Recurse -Filter *.php` | 34 dosya |
| 2 | `[System.IO.File]::ReadAllLines($_.FullName).Length` | satır/satır (UTF-8, mojibake yok) |
| 3 | Alt dizin toplamları | §1.1 tablosu (toplam 3677) |

---

## §2 — js/router omurgası (envanter)

`assets.coremusic.net/js/router/` altında **28 JS dosyası**, toplam **1206 satır**
(23 kök dosya 1143 satır + 5 config dosyası 63 satır).

### §2.1 Kök dosyalar (23 dosya — 1143 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `DomPatcher.js` | 127 | DOM yama (en büyük router dosyası) |
| 2 | `SPARouterAdapter.js` | 123 | SPA router adaptörü |
| 3 | `NavigationOrchestrator.js` | 106 | Navigasyon orkestratörü |
| 4 | `Router.js` | 81 | Ana router |
| 5 | `ContentFetcher.js` | 62 | İçerik getirme |
| 6 | `FetchWrapper.js` | 63 | Fetch sarmalayıcı |
| 7 | `CacheLayer.js` | 59 | Önbellek katmanı |
| 8 | `GuardPipeline.js` | 54 | Guard pipeline |
| 9 | `LifecycleManager.js` | 52 | Yaşam döngüsü |
| 10 | `PrefetchManager.js` | 49 | Öngetch |
| 11 | `RouterEventManager.js` | 48 | DOM olay yöneticisi |
| 12 | `ContentPatcher.js` | 41 | İçerik yama |
| 13 | `UrlUtils.js` | 39 | URL yardımcıları |
| 14 | `Logger.js` | 36 | Loglama |
| 15 | `AuthBoundaryDetector.js` | 33 | Auth sınırı algılama |
| 16 | `MemoryWatchdog.js` | 33 | Bellek izleme |
| 17 | `FocusManager.js` | 30 | Odak yönetimi |
| 18 | `NavigationGuardRunner.js` | 30 | Guard çalıştırıcı |
| 19 | `ScriptInjector.js` | 25 | Script enjeksiyonu |
| 20 | `guards.js` | 23 | Guard tanımları |
| 21 | `ScrollRestorer.js` | 15 | Scroll geri yükleme |
| 22 | `IRouter.js` | 6 | Router arayüzü (en küçük dosya) |
| 23 | `CsrfSyncManager.js` | 8 | CSRF senkronizasyonu |

### §2.2 Config dosyaları (5 dosya — 63 satır)

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `config/auth-routes.js` | 22 | Auth rota listesi |
| 2 | `config/signal-utils.js` | 19 | Signal yardımcıları |
| 3 | `config/error-types.js` | 10 | Hata tipleri |
| 4 | `config/headers.js` | 9 | HTTP başlıkları |
| 5 | `config/css-selectors.js` | 3 | CSS seçicileri |

### §2.3 Ölçüm notu (metod)

| # | Komut | Sonuç |
|---|-------|-------|
| 1 | `Get-ChildItem js\router -Recurse -Filter *.js` | 28 dosya (23 kök + 5 config) |
| 2 | `[System.IO.File]::ReadAllLines()` | satır/satır |
| 3 | Toplam | 1143 + 63 = 1206 satır |

> **Önceki ölçüm düzeltmesi:** Daha önce `js/router` "24 kök + 6 config" olarak
> raporlanmıştı; 2026-10-15 taze ölçümü **23 kök + 5 config = 28 dosya** veriyor.
> Geçerli sayı budur.

---

## §3 — Test envanteri (kanıt)

`shared/tests/` altında `Api/`, `Middleware/` ve `Repository/` klasörlerinde toplam
**19 test dosyası**, **3440 satır** (2026-10-15 ölçümü).

### §3.1 Api/ testleri (8 dosya — 1051 satır)

| # | Dosya | Satır | Ne test eder |
|---|-------|-------|--------------|
| 1 | `tests/Api/RequestValidationMiddlewareTest.php` | 214 | İstek doğrulama middleware'i |
| 2 | `tests/Api/ApiPipelineSecurityTest.php` | 188 | Pipeline güvenlik (CSRF, auth) |
| 3 | `tests/Api/RouteTableTest.php` | 166 | Rota eşleştirme |
| 4 | `tests/Api/GatewayMethodRoutingTest.php` | 152 | Gateway method yönlendirme |
| 5 | `tests/Api/ResponseNormalizationMiddlewareTest.php` | 108 | Yanıt normalizasyonu |
| 6 | `tests/Api/DtoTest.php` | 96 | DTO'lar |
| 7 | `tests/Api/ApiResponseTest.php` | 71 | ApiResponse sarmalayıcı |
| 8 | `tests/Api/VersioningTest.php` | 56 | Sürüm çözümleme |

### §3.2 Middleware/ testleri (9 dosya — 1858 satır)

| # | Dosya | Satır | Ne test eder |
|---|-------|-------|--------------|
| 1 | `tests/Middleware/CsrfMiddlewareTest.php` | 350 | CSRF middleware (en büyük test) |
| 2 | `tests/Middleware/RateLimiterMiddlewareTest.php` | 319 | Hız sınırlama |
| 3 | `tests/Middleware/MiddlewarePipelineTest.php` | 311 | Pipeline sıralama |
| 4 | `tests/Middleware/BypassAuthMiddlewareTest.php` | 245 | Auth bypass (ADR-008) |
| 5 | `tests/Middleware/SessionManagerMiddlewareTest.php` | 238 | Oturum yönetimi |
| 6 | `tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | 128 | Bypass fail-closed |
| 7 | `tests/Middleware/ValidationMiddlewareTest.php` | 106 | Doğrulama |
| 8 | `tests/Middleware/AuthMiddlewareTest.php` | 86 | Kimlik doğrulama |
| 9 | `tests/Middleware/OriginCheckMiddlewareTest.php` | 75 | Origin kontrolü (CORS) |

### §3.3 Repository/ ApiKey testleri (2 dosya — 531 satır)

| # | Dosya | Satır | Ne test eder |
|---|-------|-------|--------------|
| 1 | `tests/Repository/ApiKeyCreateCommandTest.php` | 279 | ApiKey oluşturma komutu |
| 2 | `tests/Repository/ApiKeyRepositoryTest.php` | 252 | ApiKey repository |

### §3.4 Test özeti

| # | Kümе | Dosya | Satır |
|---|------|-------|-------|
| 1 | Api/ | 8 | 1051 |
| 2 | Middleware/ | 9 | 1858 |
| 3 | Repository/ (ApiKey) | 2 | 531 |
| — | **Toplam** | **19** | **3440** |

---

## §4 — Bağımlılık grafiği

`command-query` akışının gerçek bağımlılık zinciri (dosya/satır kanıtlı):

```text
[İstek]
  │
  ▼
Gateway.php:50  dispatch()  ◄── tek giriş noktası
  │                 ▲
  │                 └── Gateway.php:39  getServiceRegistry()
  ▼
ApiMiddlewarePipeline.php:27  process($request, $handler)
  │   ├── pipe() L19  ← middleware kaydı (ters sıralama L33 array_reverse)
  │   └── 6 middleware (Authentication L134 · Authorization L111 ·
  │        RateLimit L80 · RequestValidation L175 · ResponseNormalization L97)
  ▼
RouteTable.php:111  match()
  │   ├── add() L42  · all() L143
  │   ├── find() L197 (private)  · isDeclared() L229 (private)
  │   └── allowedMethods() L160 (private)
  ▼
Handler (RouteTable.php:96-101 → authController / userController / ...)
  │
  ▼
ApiResponse.php (131 satır)  ── yanıt sarmalayıcı
```

### §4.1 Bağımlılık tablosu

| # | Bağımlılık | Kaynak (dosya:satır) | Hedef | Tür |
|---|-----------|----------------------|-------|-----|
| 1 | Gateway → Pipeline | `Gateway.php` (dispatch L50) | `ApiMiddlewarePipeline.php` (process L27) | çağırır |
| 2 | Pipeline → RouteTable | `ApiMiddlewarePipeline.php` (process L27) | `RouteTable.php` (match L111) | handler zinciri |
| 3 | RouteTable → Handler | `RouteTable.php` (L96-101) | Controller'lar | string eşleme |
| 4 | Gateway → Registry | `Gateway.php` (getServiceRegistry L39) | `ServiceRegistry.php` | servis çözme |
| 5 | Registry → Definition | `ServiceRegistry.php` (register L26) | `ServiceDefinition.php` | kayıt |
| 6 | Registry → Health | `ServiceRegistry.php` (healthCheck L52) | `ServiceHealth.php` | sağlık |
| 7 | Middleware → Validator | `RequestValidationMiddleware.php` (L175) | `Validator.php` | doğrulama |
| 8 | DTO → Response | `MusicSearchRequest.php` (clamp L76-77) | `PaginationResponse.php` | veri akışı |
| 9 | Versioning → RouteTable | `VersionResolver.php` (L79) | `RouteTable.php` (`/api/v1/...`) | sürüm |

### §4.2 router bağımlılık zinciri (JS)

```text
[Link click / popstate]
  │
  ▼
RouterEventManager.js:10  popstate → onNavigate
RouterEventManager.js:11  click → #handleClick (aynı origin + SPA linki → preventDefault L38)
  │
  ▼
NavigationOrchestrator.js:94  emit('nav:complete', { url, pushState })
  │
  ├──► ContentFetcher.js (62)  ─ fetch
  ├──► DomPatcher.js (127)    ─ DOM patch
  ├──► GuardPipeline.js (54)  ─ guard'lar (guards.js 23 · NavigationGuardRunner.js 30)
  ├──► CacheLayer.js (59)     ─ önbellek
  └──► ScrollRestorer.js (15) ─ scroll
        │
        ▼
      SPARouterAdapter.js:113  emit('nav:complete', { url })
      SPARouterAdapter.js:116  emit('nav:error',   { url, error })
        │
        ▼
      ScrollManager.js:26  on('nav:complete')  ─ dinleyici
```

### §4.3 Gateway::dispatch akışı (L50-95 — adım adım)

| # | Adım | Kod | Satır |
|---|------|-----|-------|
| 1 | İstek nesnesi oluştur | `$apiRequest = new ApiRequest();` | 53 |
| 2 | Sürüm çöz | `$version = $this->versionResolver->resolve($apiRequest);` | 56 |
| 3 | Sürümlü attribute ekle | `$apiRequest = $apiRequest->withAttribute('version', $version);` | 57 |
| 4 | Rota eşleştir (**pipeline DIŞINDA**) | `$match = $this->matchRoute($apiRequest);` | 62 |
| 5 | `_route` anahtarlarını isteğe yaz | `$request['_route'] = $match['route'];` (+ `_route_status`, `_route_allow`) | 63-65 |
| 6 | Pipeline'ı çalıştır | `$this->middlewarePipeline->process($request, function (...) {...})` | 68 |
| 7 | 405 kontrolü | `if ($status === RouteTable::STATUS_METHOD_NOT_ALLOWED) return $this->methodNotAllowedResponse(...)` | 74-76 |
| 8 | 404 kontrolü | `if ($status !== RouteTable::STATUS_OK \|\| !is_array($route)) return ApiResponse::error('NOT_FOUND', ...)` | 78-80 |
| 9 | Handler çağır | `return $this->invokeHandler($route, $request);` | 84 |
| 10 | Hata yakala + TraceId | `catch (\Throwable $e) { $traceId = UuidV7::generate(); ... }` | 87-88 |

> **Mimari not (L59-61 yorumu):** Rota eşlemesi **pipeline dışında** yapılır ki `_route`
> (ve 405/404 durumu) Authentication/Authorization/RequestValidation/ResponseNormalization
> middleware'lerine ulaşsın (Faz 2 notu #2).

### §4.4 Bff katman detayı (5 dosya)

| # | Dosya | Satır | `transform` | `getClientType` | Rol |
|---|-------|-------|-------------|-----------------|-----|
| 1 | `Bff/BffLayer.php` | 83 | L34 | — | Soyut katman + `register` (L26) |
| 2 | `Bff/DesktopBff.php` | 66 | L24 | L50 | Masaüstü transform |
| 3 | `Bff/EmbeddedBff.php` | 76 | L24 | L48 | Gömülü transform |
| 4 | `Bff/MobileBff.php` | 101 | L24 | L57 | Mobil transform |
| 5 | `Bff/SpaBff.php` | 64 | L24 | L48 | SPA transform |

> **Desen:** Her Bff `transform` + `getClientType` uygular (Template Method). İstemci
> tipine göre yanıt transformu Bff katmanında toplanır.

---

## §5 — RouteTable rotaları

`RouteTable.php` **253 satır**; GET rota tablosu **L95-101** (6 rota).

### §5.1 GET rotaları (RouteTable.php:96-101)

| # | Rota | Service | Handler | Satır |
|---|------|---------|---------|-------|
| 1 | `/api/v1/auth` | `auth` | `authController` | 96 |
| 2 | `/api/v1/user` | `user` | `userController` | 97 |
| 3 | `/api/v1/music` | `music` | `musicController` | 98 |
| 4 | `/api/v1/playlist` | `playlist` | `playlistController` | 99 |
| 5 | `/api/v1/media` | `media` | `mediaController` | 100 |
| 6 | `/api/v1/download` | `download` | `downloadController` | 101 |

> **Biçim notu (RouteTable.php:67):** `['GET' => ['/api/v1/auth' => ['service' => 'auth', ...]], 'POST' => [...]]`
> — HTTP method anahtarı, altında rota ⇒ `{service, handler}` eşlemesi.

### §5.2 RouteTable metot haritası

| # | Metot | Erişim | Satır | Ne yapar |
|---|-------|--------|-------|----------|
| 1 | `add` | public | 42 | Rota ekleme |
| 2 | `match` | public | 111 | Rota eşleştirme (ana akış) |
| 3 | `all` | public | 143 | Tüm rotaları listeleme |
| 4 | `allowedMethods` | private | 160 | İzin verilen methodlar |
| 5 | `find` | private | 197 | İç rota bulma |
| 6 | `isDeclared` | private | 229 | Rota beyanı kontrolü |

### §5.3 Durum sabitleri (RouteTable.php:28-30)

| # | Sabit | Değer | Satır | Kullanım |
|---|-------|-------|-------|----------|
| 1 | `STATUS_OK` | `'ok'` | 28 | Eşleşme başarılı → `['status' => STATUS_OK, 'route' => ..., 'allow' => []]` (L120) |
| 2 | `STATUS_METHOD_NOT_ALLOWED` | `'method_not_allowed'` | 29 | Method rota için tanımlı değil (405) → `Gateway.php:74` |
| 3 | `STATUS_NOT_FOUND` | `'not_found'` | 30 | Rota yok → `['status' => STATUS_NOT_FOUND, 'route' => null, 'allow' => []]` (L137) |

---

## §6 — Middleware pipeline (detay)

`ApiMiddlewarePipeline.php` **46 satır** — callable tabanlı, framework'süz (ADR-002 yasağı).

### §6.1 Metotlar

| # | Metot | Satır | Ne yapar |
|---|-------|-------|----------|
| 1 | `pipe(callable $middleware): self` | 19 | Middleware kaydı (fluent — `self` döner) |
| 2 | `process(array $request, callable $handler): array` | 27 | Pipeline'ı çalıştırır |

### §6.2 Ters sıralama mantığı (L33-40)

| # | Adım | Kod | Satır |
|---|------|-----|-------|
| 1 | Handler'ı başlangıç pipeline'ı yap | `$pipeline = $handler;` | 31 |
| 2 | Middleware'leri **ters** sırayla sarmala | `foreach (array_reverse($this->middlewares) as $middleware)` | 33 |
| 3 | Her middleware'i `$pipeline`'a sar | `$pipeline = function (array $request) use ($middleware, $pipeline) { return $middleware($request, $pipeline); }` | 34-37 |
| 4 | İlk istekle çalıştır | `return $pipeline($request);` | 39 |

> **Okuma:** İlk `pipe()` edilen middleware **en dışta** çalışır (Onion modeli).
> `array_reverse` sayesinde kayıt sırası = çalışma sırası (son eklenen en içte).

### §6.3 Middleware envanteri (6 + çekirdek)

| # | Middleware | Satır | Görev |
|---|-----------|-------|-------|
| 1 | `AuthenticationMiddleware.php` | 134 | Kimlik doğrulama (token/session) |
| 2 | `AuthorizationMiddleware.php` | 111 | Yetki kontrolü (rol/permission) |
| 3 | `RateLimitMiddleware.php` | 80 | Hız sınırlama (APCu sayaç) |
| 4 | `RequestValidationMiddleware.php` | 175 | İstek doğrulama (Validator) |
| 5 | `ResponseNormalizationMiddleware.php` | 97 | Yanıt biçimi normalizasyonu |
| 6 | `Validator.php` | 145 | Doğrulama kural yardımcıları |
| — | `ApiMiddlewarePipeline.php` | 46 | Çekirdek pipeline |

---

## §7 — Registry + Versioning

### §7.1 ServiceRegistry metotları (132 satır)

| # | Metot | Erişim | Satır | Ne yapar |
|---|-------|--------|-------|----------|
| 1 | `register` | public | 26 | Servis kaydı |
| 2 | `discover` | public | 38 | Servis keşfi |
| 3 | `healthCheck` | public | 52 | Sağlık kontrolü |
| 4 | `getHealthyServices` | public | 66 | Sağlıklı servis listesi |
| 5 | `loadFromConfig` | public | 83 | Config'ten yükleme |
| 6 | `checkServiceHealth` | private | 97 | Tek servis sağlık kontrolü |

### §7.2 Versioning envanteri

| # | Dosya | Satır | Rol |
|---|-------|-------|-----|
| 1 | `ApiVersion.php` | 24 | Sürüm sabiti (`v1`) |
| 2 | `VersionRegistry.php` | 120 | Sürüm kaydı |
| 3 | `VersionResolver.php` | 79 | İstekten sürüm çözümleme |

> **İlişki:** `VersionResolver` (§4 bağımlılık #9) sürümü çözer; `RouteTable`'daki
> rotalar `/api/v1/` önekiyle sabitlenmiş (§5.1). Çok-sürüm (v2) desteği **PLANNED** (§8).

---

## §8 — Boşluklar (PLANNED / ⚠️)

| # | Boşluk | Kanıt | Etki | Durum |
|---|--------|-------|------|-------|
| B1 | Rota tablosunda yalnız `'GET'` bloğu tanımlı | RouteTable.php:95-101 yalnız `'GET' => [...]`; fakat **405 makinesi hazır**: sabit `STATUS_METHOD_NOT_ALLOWED` (L29) + `allowedMethods()` (L160) + `Gateway::methodNotAllowedResponse()` (L125) | Write akışları (playlist create, user update) rota tablosunda tanımlı değil; 405 yanıtı üretilebilir ama rota yok | `PLANNED` (ekleme bekliyor) |
| B2 | Çok-sürüm (v2) desteği yok | RouteTable rotaları sabit `/api/v1/` | v2 geçişi için rota tablosu yeniden yazılmalı | `PLANNED` |
| B3 | Handler sınıfları bu klasörde değil | RouteTable.php:96-101 handler'lar **string** (`authController` vb.) | Gerçek controller dosyaları `Api/` dışında (konum: ⚠️ VERIFICATION REQUIRED) | `PLANNED` |
| B4 | `composer.json`'da olay dispatcher bağımlılığı, wired değil | `shared/composer.json:18` `psr/event-dispatcher: ^1.0` · `:21` `symfony/event-dispatcher: ^7.0` | Komut-sorgu akışı olaylara bağlanmamış (ADR-086 wiring PLANNED) | `PLANNED` |
| B5 | router'da `player:progress` / `nav:navigate` emitter'ı yok | `PlayerController.js:87` `on('player:progress')` · `SPARouterAdapter.js:35` `on('nav:navigate')` → emit çağrısı 0 isabet | Ölü abonelik ya da gizli emitter | `⚠️ VERIFICATION REQUIRED` |
| B6 | `IRouter.js` 6 satır — soyut arayüz | `IRouter.js` (6 satır) | Tek dosyalık interface; implementasyon `Router.js` (81) |bilgi |
| B7 | Handler/Controller testleri bu kümede değil | `Api/` testleri 8 dosya (§3.1) — controller testi yok | RouteTable string handler test ediliyor, gerçek controller değil | `⚠️ VERIFICATION REQUIRED` |

> **B1 notu:** `RouteTable.php:67` biçimi `'POST' => [...]` anahtarını **destekler**;
> fakat L95-101'de yalnız `'GET'` bloğu tanımlı. POST rotaları başka dosyada olabilir
> (⚠️ VERIFICATION REQUIRED) veya henüz eklenmemiş olabilir (`PLANNED`).

---

## §9 — Açık sorular (spec §8 dahil)

Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır;
belge seviyesinde kayıt.

| # | Soru | Etki |
|---|------|------|
| 1 | POST/PUT/DELETE rotaları nerede tanımlı? (B1) | Write akışı bütünlüğü |
| 2 | Handler/controller sınıflarının gerçek konumu? (B3) | Bağımlılık grafiği eksik halkası |
| 3 | `symfony/event-dispatcher` (^7.0, composer.json:21) neden kurulu ama wired değil? (B4) | CQRS/event köprüsü |
| 4 | v2 sürümü için rota tablosu stratejisi? (B2) | Versiyonlama mimarisi |

---

## §10 — Authority

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
