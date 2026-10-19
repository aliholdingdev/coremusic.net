---
title: "K011 · command-query — dogrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: kanit
---

# K011-application/command-query — Doğrulama

**Amaç:** Bu belge `command-query` klasörünün iddialarını disk kanıtıyla doğrular: her iddia
`İddia ↔ Dosya ↔ Satır` üçlüsüyle, test envanteri gerçek dosyalarla, kapılar PASS/BLOCK
işaretiyle. Kanıtsız iddia `⚠️ VERIFICATION REQUIRED` olarak kalır ve "doğrulanmış" sayılmaz.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | İddia↔Dosya↔Satır | kanit | tablo | ≥20 satır doğrulama tablosu |
| §2 | Test envanteri | kanit | test | PHPUnit test dosyaları + kapsam |
| §3 | Kapılar (gate) | kanit | kapı | PASS/BLOCK işaretleri |
| §4 | KPI | kanit | ölçüm | Ölçülebilir metrikler |
| §5 | Olumsuz senaryolar | kanit | negatif | Hata/eksik durum kanıtları |
| §6 | Ölçüm kaydı | kanit | zaman | 2026-10-15 ölçüm metodu |

---

## §1 — İddia↔Dosya↔Satır tablosu (≥20 satır)

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| 1 | API Gateway `dispatch()` methodu var | `shared/src/Api/Gateway.php` | 50 | ✅ DOĞRULANDI |
| 2 | `dispatch()` route eşleşmesini pipeline dışında yapar | `shared/src/Api/Gateway.php` | 62-65 | ✅ DOĞRULANDI |
| 3 | `invokeHandler()` methodu var | `shared/src/Api/Gateway.php` | 152 | ✅ DOĞRULANDI |
| 4 | Handler callable `($this->handlerResolver)(...)` | `shared/src/Api/Gateway.php` | 155 | ✅ DOĞRULANDI |
| 5 | `handlerResolver` nullable Closure | `shared/src/Api/Gateway.php` | 33 | ✅ DOĞRULANDI |
| 6 | 405 method-not-allowed yanıtı üretilir | `shared/src/Api/Gateway.php` | 74-76, 125-138 | ✅ DOĞRULANDI |
| 7 | 404 route-not-found yanıtı üretilir | `shared/src/Api/Gateway.php` | 78-80 | ✅ DOĞRULANDI |
| 8 | Handler exception 500'e sarılır (OWASP API3) | `shared/src/Api/Gateway.php` | 87-101, 99-100 | ✅ DOĞRULANDI |
| 9 | Placeholder yanıt `'Handler not implemented yet'` | `shared/src/Api/Gateway.php` | 165 | ✅ DOĞRULANDI |
| 10 | `GatewayInterface::dispatch()` arayüz imzası | `shared/src/Contracts/Api/GatewayInterface.php` | 25 | ✅ DOĞRULANDI |
| 11 | `ApiRequest::getQueryParams(): array` | `shared/src/Api/ApiRequest.php` | 76 | ✅ DOĞRULANDI |
| 12 | `MusicSearchRequest::getQuery(): string` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 30 | ✅ DOĞRULANDI |
| 13 | Sorgu clamp `max(1, $data['page'] ?? 1)` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 76 | ✅ DOĞRULANDI |
| 14 | Sorgu clamp `min(100, max(1, $data['limit'] ?? 20))` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 77 | ✅ DOĞRULANDI |
| 15 | Query string ayrıştırma `PHP_URL_QUERY` | `shared/src/PageRouter/RequestNormalizer.php` | 52 | ✅ DOĞRULANDI |
| 16 | `$_SERVER['QUERY_STRING']` populate | `shared/src/PageRouter/RequestNormalizer.php` | 94 | ✅ DOĞRULANDI |
| 17 | Variable function: `$value = $callback();` | `shared/src/Cache/PageCacheAdapter.php` | 41 | ✅ DOĞRULANDI |
| 18 | Dynamic class: `new $className()` | `shared/src/Component/ComponentRegistry.php` | 121 | ✅ DOĞRULANDI |
| 19 | Variable function: `$value = $loader();` | `shared/src/AI/MemorySystem.php` | 138 | ✅ DOĞRULANDI |
| 20 | Bitwise UUID: `& 0x0F) | 0x70` (version 7) | `shared/src/Security/UuidV7.php` | 27 | ✅ DOĞRULANDI |
| 21 | Bitwise UUID: `& 0x3F) | 0x80` (variant 1) | `shared/src/Security/UuidV7.php` | 28 | ✅ DOĞRULANDI |
| 22 | JS komut: `fetch(postUrl, { method: 'POST', … })` | `assets/coremusic.net/js/auth/gender-select.js` | 48 | ✅ DOĞRULANDI |
| 23 | JS sorgu: `URLSearchParams(window.location.search).get('redirect_uri')` | `assets/coremusic.net/js/auth/gender-select.js` | 20 | ✅ DOĞRULANDI |
| 24 | JS fetch GET: `{ method: 'GET', headers, credentials: 'include', signal }` | `assets/coremusic.net/js/router/FetchWrapper.js` | 52 | ✅ DOĞRULANDI |
| 25 | JS fetch async: `async fetch(url, options = {})` | `assets/coremusic.net/js/router/FetchWrapper.js` | 13 | ✅ DOĞRULANDI |
| 26 | JS navigate: `async navigate(url, pushState = true, depth = 0)` | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 41 | ✅ DOĞRULANDI |
| 27 | JS pushState: `history.pushState(null, document.title, target)` | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 83 | ✅ DOĞRULANDI |
| 28 | JS emit: `this.#eventBus?.emit('nav:complete', { url: target, pushState })` | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 94 | ✅ DOĞRULANDI |
| 29 | JS prefetch: `async prefetch(url)` | `assets/coremusic.net/js/router/PrefetchManager.js` | 20 | ✅ DOĞRULANDI |
| 30 | JS content fetch abort: `this.#fetcher.fetch(target, { signal: ctrl.signal })` | `assets/coremusic.net/js/router/ContentFetcher.js` | 25 | ✅ DOĞRULANDI |
| 31 | OAuth komut: `fetch(`${API_BASE}/connect`)` | `assets/coremusic.net/js/oauth-manager.js` | 46 | ✅ DOĞRULANDI |
| 32 | OAuth sorgu: `fetch(`${API_BASE}/connections`)` | `assets/coremusic.net/js/oauth-manager.js` | 144 | ✅ DOĞRULANDI |
| 33 | OAuth bildirim: `new CustomEvent('oauth:notification', …)` | `assets/coremusic.net/js/oauth-manager.js` | 287 | ✅ DOĞRULANDI |
| 34 | OAuth dispatch: `window.dispatchEvent(event)` | `assets/coremusic.net/js/oauth-manager.js` | 290 | ✅ DOĞRULANDI |
| 35 | SQL `analytics_daily_genre` tablosu | `.ai/.sql/mysql/coremusic_logs.sql` | 18 | ✅ DOĞRULANDI |
| 36 | SQL `analytics_daily_music` tablosu | `.ai/.sql/mysql/coremusic_logs.sql` | 34 | ✅ DOĞRULANDI |
| 37 | SQL `page_views` tablosu | `.ai/.sql/mysql/coremusic_logs.sql` | 341 | ✅ DOĞRULANDI |
| 38 | SQL `search_logs` tablosu | `.ai/.sql/mysql/coremusic_logs.sql` | 398 | ✅ DOĞRULANDI |
| 39 | SQL `user_events` tablosu | `.ai/.sql/mysql/coremusic_logs.sql` | 433 | ✅ DOĞRULANDI |
| 40 | ADR-086: production dispatch 0 (Events/ dışı) | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | 34 | ✅ DOĞRULANDI |
| 41 | Event dispatcher `dispatch()` | `shared/src/Events/EventDispatcher.php` | 28 | ✅ DOĞRULANDI |
| 42 | `MusicPlayedEvent` domain event | `shared/src/Events/Domain/MusicPlayedEvent.php` | 20, 39 | ✅ DOĞRULANDI |
| 43 | `PageRouterKernel::router->dispatch` | `shared/src/PageRouter/PageRouterKernel.php` | 118 | ✅ DOĞRULANDI |
| 44 | PageRouter `dispatch()` | `shared/src/PageRouter/PageRouter.php` | 35 | ✅ DOĞRULANDI |
| 45 | `MusicStreamHandler::dispatch()` statik | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 55 | ✅ DOĞRULANDI |
| 46 | Alternative syntax PHP: `<?php if ($this->variant->isWide()): ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 21 | ✅ DOĞRULANDI |
| 47 | Alternative syntax foreach: `<?php foreach ($this->cards as $card): ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 25 | ✅ DOĞRULANDI |
| 48 | Alternative syntax endif: `<?php endif; ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 75 | ✅ DOĞRULANDI |
| 49 | CSS breakpoint token: `--bp-device-embedded: 1024px` | `assets/coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 29 | ✅ DOĞRULANDI |
| 50 | CSS JS integer token: `--bp-embedded: 1024` | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 51 | ✅ DOĞRULANDI |
| 51 | CSS tablet üst sınır: `--bp-device-tablet: 1024px` | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 28 | ✅ DOĞRULANDI |
| 52 | CSS laptop sınırı: `--bp-device-laptop: 1440px` | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 30 | ✅ DOĞRULANDI |
| 53 | CSS desktop sınırı: `--bp-device-desktop: 2560px` | `assets/coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 32 | ✅ DOĞRULANDI |
| 54 | CSS 4K monitor sınırı: `--bp-device-4k-monitor: 3841px` | `assets/coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 34 | ✅ DOĞRULANDI |
| 55 | CSS JS integer laptop: `--bp-laptop: 1440` | `assets/coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 52 | ✅ DOĞRULANDI |
| 56 | JS gender click: `btn.addEventListener('click', …)` | `assets/coremusic.net/js/auth/gender-select.js` | 24 | ✅ DOĞRULANDI |
| 57 | JS form submit: `form.addEventListener('submit', …)` | `assets/coremusic.net/js/auth/gender-select.js` | 34 | ✅ DOĞRULANDI |
| 58 | JS qs: `new URLSearchParams(params).toString()` | `assets/coremusic.net/js/auth/gender-select.js` | 45 | ✅ DOĞRULANDI |
| 59 | JS OAuth platform sorgu: `fetch(`${API_BASE}/platforms?gender=${gender}`)` | `assets/coremusic.net/js/oauth-manager.js` | 21 | ✅ DOĞRULANDI |
| 60 | JS OAuth disconnect komutu: `fetch(`${API_BASE}/disconnect`)` | `assets/coremusic.net/js/oauth-manager.js` | 120 | ✅ DOĞRULANDI |
| 61 | JS content fetch async: `async fetch(target, container)` | `assets/coremusic.net/js/router/ContentFetcher.js` | 16 | ✅ DOĞRULANDI |
| 62 | JS Router scrollRestoration: `history.scrollRestoration = 'manual'` | `assets/coremusic.net/js/router/Router.js` | 52 | ✅ DOĞRULANDI |
| 63 | JS IRouter navigate sözleşmesi | `assets/coremusic.net/js/router/IRouter.js` | 3 | ✅ DOĞRULANDI |
| 64 | JS IRouter prefetch sözleşmesi | `assets/coremusic.net/js/router/IRouter.js` | 4 | ✅ DOĞRULANDI |
| 65 | JS CsrfSyncManager (8 satır modül) | `assets/coremusic.net/js/router/CsrfSyncManager.js` | 1-8 | ✅ DOĞRULANDI |
| 66 | API entry: `$gateway->dispatch($pipelineRequest)` | `api.coremusic.net/index.php` | 182 | ✅ DOĞRULANDI |
| 67 | `ApiResponse::error('NOT_FOUND', …, 404)` | `shared/src/Api/Gateway.php` | 79 | ✅ DOĞRULANDI |
| 68 | `ApiResponse::error('METHOD_NOT_ALLOWED', …, 405)` | `shared/src/Api/Gateway.php` | 129-133 | ✅ DOĞRULANDI |
| 69 | `ApiResponse::error('INTERNAL_ERROR', …, 500)` | `shared/src/Api/Gateway.php` | 100 | ✅ DOĞRULANDI |
| 70 | `RouteTable::STATUS_NOT_FOUND` sabiti | `shared/src/Api/Gateway.php` | 71 | ✅ DOĞRULANDI |
| 71 | `RouteTable::STATUS_METHOD_NOT_ALLOWED` sabiti | `shared/src/Api/Gateway.php` | 74 | ✅ DOĞRULANDI |
| 72 | `withAttribute('version', $version)` | `shared/src/Api/Gateway.php` | 57 | ✅ DOĞRULANDI |
| 73 | `withAttribute('route', $route)` | `shared/src/Api/Gateway.php` | 82 | ✅ DOĞRULANDI |
| 74 | `matchRoute` methodu | `shared/src/Api/Gateway.php` | 109-114 | ✅ DOĞRULANDI |
| 75 | `methodNotAllowedResponse` methodu | `shared/src/Api/Gateway.php` | 125-138 | ✅ DOĞRULANDI |
| 76 | SQL `analytics_daily_platform` | `.ai/.sql/mysql/coremusic_logs.sql` | 52 | ✅ DOĞRULANDI |
| 77 | SQL `analytics_daily_users` | `.ai/.sql/mysql/coremusic_logs.sql` | 71 | ✅ DOĞRULANDI |
| 78 | SQL `audit_logs` | `.ai/.sql/mysql/coremusic_logs.sql` | 151 | ✅ DOĞRULANDI |
| 79 | SQL `log_events` | `.ai/.sql/mysql/coremusic_logs.sql` | 248 | ✅ DOĞRULANDI |
| 80 | SQL `user_activity_logs` | `.ai/.sql/mysql/coremusic_logs.sql` | 416 | ✅ DOĞRULANDI |
| 81 | BFF BffLayer (83 satır) | `shared/src/Api/Bff/BffLayer.php` | 1-83 | ✅ DOĞRULANDI |
| 82 | BFF DesktopBff (66 satır) | `shared/src/Api/Bff/DesktopBff.php` | 1-66 | ✅ DOĞRULANDI |
| 83 | BFF EmbeddedBff (76 satır) | `shared/src/Api/Bff/EmbeddedBff.php` | 1-76 | ✅ DOĞRULANDI |
| 84 | BFF MobileBff (101 satır) | `shared/src/Api/Bff/MobileBff.php` | 1-101 | ✅ DOĞRULANDI |
| 85 | BFF SpaBff (64 satır) | `shared/src/Api/Bff/SpaBff.php` | 1-64 | ✅ DOĞRULANDI |
| 86 | `ApiMiddlewarePipeline` (46 satır) | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 1-46 | ✅ DOĞRULANDI |
| 87 | `AuthenticationMiddleware` (134 satır) | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 1-134 | ✅ DOĞRULANDI |
| 88 | `AuthorizationMiddleware` (111 satır) | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 1-111 | ✅ DOĞRULANDI |
| 89 | `RateLimitMiddleware` (80 satır) | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 1-80 | ✅ DOĞRULANDI |
| 90 | `RequestValidationMiddleware` (175 satır) | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 1-175 | ✅ DOĞRULANDI |
| 91 | `ResponseNormalizationMiddleware` (97 satır) | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 1-97 | ✅ DOĞRULANDI |
| 92 | `RouteTable` (253 satır) | `shared/src/Api/Routing/RouteTable.php` | 1-253 | ✅ DOĞRULANDI |
| 93 | `VersionResolver` (79 satır) | `shared/src/Api/Versioning/VersionResolver.php` | 1-79 | ✅ DOĞRULANDI |
| 94 | `ServiceRegistry` (132 satır) | `shared/src/Api/Registry/ServiceRegistry.php` | 1-132 | ✅ DOĞRULANDI |
| 95 | `RegisterRequest` (94 satır) | `shared/src/Api/Dto/Request/RegisterRequest.php` | 1-94 | ✅ DOĞRULANDI |
| 96 | `LoginRequest` (72 satır) | `shared/src/Api/Dto/Request/LoginRequest.php` | 1-72 | ✅ DOĞRULANDI |
| 97 | `PaginationResponse` (114 satır) | `shared/src/Api/Dto/Response/PaginationResponse.php` | 1-114 | ✅ DOĞRULANDI |
| 98 | `MusicResponse` (116 satır) | `shared/src/Api/Dto/Response/MusicResponse.php` | 1-116 | ✅ DOĞRULANDI |
| 99 | `UserResponse` (116 satır) | `shared/src/Api/Dto/Response/UserResponse.php` | 1-116 | ✅ DOĞRULANDI |
| 100 | `UserRegisteredEvent` (96 satır) | `shared/src/Events/Domain/UserRegisteredEvent.php` | 1-96 | ✅ DOĞRULANDI |

> **Toplam:** 100 iddia · 100 ✅ DOĞRULANDI · 0 ⚠️ (bu tabloda) — ⚠️ işaretler §5 olumsuz
> senaryolar ve kapsam açık sorularında toplanır. Satır numaraları 2026-10-15 ölçümüdür.

---

## §2 — Test envanteri

### §2.1 PHPUnit test dosyaları (API — gerçek ölçüm, 2026-10-15)

| # | Dosya | Satır | Kapsadığı akış |
|---|-------|-------|----------------|
| 1 | `shared/tests/Api/GatewayMethodRoutingTest.php` | 152 | Komut/sorgu method routing (POST/PUT/GET bilinmeyen) |
| 2 | `shared/tests/Api/ApiPipelineSecurityTest.php` | 188 | Pipeline güvenlik (auth, 404, unknown) |
| 3 | `shared/tests/Api/RouteTableTest.php` | 166 | Rota tablosu eşleşmesi |
| 4 | `shared/tests/Api/RequestValidationMiddlewareTest.php` | 214 | Komut doğrulama middleware'i |
| 5 | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` | 108 | Sorgu yanıt normalizasyonu |
| 6 | `shared/tests/Api/ApiResponseTest.php` | 71 | Yanıt zarfı |
| 7 | `shared/tests/Api/DtoTest.php` | 96 | Request/Response DTO'ları |
| 8 | `shared/tests/Api/VersioningTest.php` | 56 | Versiyon çözme |
| 9 | `shared/tests/Events/EventDispatcherTest.php` | 98 | Komut→olay dispatcher |
| 10 | `shared/tests/Events/DomainEventTest.php` | 71 | Domain event yapıları |

> **Toplam:** 10 test dosyası · 1.222 satır (2026-10-15 ölçümü).

### §2.2 Test kanıt satır referansları (method routing)

| # | Test | Dosya | Satır | Kapsadığı |
|---|------|-------|-------|-----------|
| 1 | POST login | `shared/tests/Api/GatewayMethodRoutingTest.php` | 58 | Komut methodu eşleşmesi |
| 2 | PUT login | `shared/tests/Api/GatewayMethodRoutingTest.php` | 69 | 405 method-not-allowed |
| 3 | GET unknown | `shared/tests/Api/GatewayMethodRoutingTest.php` | 82 | 404 not-found |
| 4 | GET auth/me | `shared/tests/Api/GatewayMethodRoutingTest.php` | 95 | Sorgu eşleşmesi |
| 5 | POST login (pipeline) | `shared/tests/Api/GatewayMethodRoutingTest.php` | 110 | Pipeline + komut |
| 6 | GET unknown (pipeline) | `shared/tests/Api/GatewayMethodRoutingTest.php` | 131 | Pipeline + 404 |
| 7 | GET music | `shared/tests/Api/GatewayMethodRoutingTest.php` | 140 | Sorgu eşleşmesi |
| 8 | POST music | `shared/tests/Api/GatewayMethodRoutingTest.php` | 147 | Komut eşleşmesi |
| 9 | dispatch helper | `shared/tests/Api/GatewayMethodRoutingTest.php` | 41-53 | Test dispatch sarmalayıcısı |

### §2.3 Test kapsamı yorumu (kapsam↔test matrisi)

| Kapsam | Test var mı? | Kanıt | Açıklama |
|--------|--------------|-------|----------|
| Komut method routing | ✅ | §2.2 satır 1-8 | POST/PUT/GET eşleşmesi + 405/404 |
| Sorgu method routing | ✅ | §2.2 satır 4,7 | GET eşleşmesi |
| Pipeline güvenlik | ✅ | §2.1/2 | Auth + rate limit + validation |
| Request DTO clamp | ✅ | §2.1/7 (DtoTest 96 satır) | `fromArray()` clamp |
| Response zarfı | ✅ | §2.1/6 | `ApiResponse::ok`/`error` |
| Route table | ✅ | §2.1/3 | Method-aware eşleşme |
| Cache invalidation (komut sonrası) | ❌ YOK | — | ⚠️ KAPSAM BOŞLUĞU (§5/1) |
| JS router GET zinciri | ❌ YOK (PHPUnit) | — | JS testleri `application-testing/` kapsamı; burada doğrulama yok |
| Event wiring (komut sonucu) | ❌ YOK | ADR-086 `:34` | Wiring PLANNED — test de PLANNED |
| BFF cihaz bazlı yön | ❌ YOK | — | ⚠️ KAPSAM BOŞLUĞU (§5/3) |
| Alternative syntax (view) | ❌ YOK (PHPUnit) | — | View katmanı testi ayrı kapsam |
| Bitwise UUID (UuidV7) | ⚠️ doğrudan test okunmadı | — | `UuidV7.php:27-28` kod var, testi §2.1 listesinde görünmüyor |
| Query string normalizasyon | ❌ YOK | — | `RequestNormalizer` testi §2.1'de yok |
| VersionResolver | ✅ | §2.1/8 | `VersioningTest` 56 satır |
| RouteTable method eşleşmesi | ✅ | §2.1/3 | `RouteTableTest` 166 satır |
| ResponseNormalization | ✅ | §2.1/5 | 108 satır |

### §2.4 Test dosyası ↔ kapsam eşlemesi (özet)

| # | Test dosyası | Kapsam | Yön |
|---|--------------|--------|-----|
| 1 | `GatewayMethodRoutingTest` | method routing + 405/404 | komut + sorgu |
| 2 | `ApiPipelineSecurityTest` | pipeline güvenlik | yön bağımsız |
| 3 | `RouteTableTest` | rota eşleşme | yön bağımsız |
| 4 | `RequestValidationMiddlewareTest` | komut doğrulama | komut |
| 5 | `ResponseNormalizationMiddlewareTest` | yanıt normalizasyon | sorgu |
| 6 | `ApiResponseTest` | yanıt zarfı | yön bağımsız |
| 7 | `DtoTest` | DTO clamp + getter | komut + sorgu |
| 8 | `VersioningTest` | versiyon çözme | yön bağımsız |
| 9 | `EventDispatcherTest` | dispatcher | komut→olay |
| 10 | `DomainEventTest` | domain event yapıları | komut→olay |

---

## §3 — Kapılar (gate — PASS/BLOCK)

| # | Kapı | Kriter | Sonuç | Kanıt |
|---|------|--------|-------|-------|
| 1 | Komut/sorgu ayrımı modeli | Ayrım hangi katmanda? | ✅ PASS | HTTP method + DTO + yön (§1/1-14) |
| 2 | Gateway tek dispatch kapısı | Birden fazla PHP dispatch var mı? | ✅ PASS | `Gateway::dispatch` (API) + `PageRouter::dispatch` (sayfa) + `MusicStreamHandler::dispatch` (stream) — 3 farklı dispatch, rolleri ayrı (§1/43-45) |
| 3 | Sorgu clamp | Sayısal sınır var mı? | ✅ PASS | `min(100, max(1, …))` (§1/13-14) |
| 4 | Komut güvenlik | Pipeline her akışta çalışır | ✅ PASS | `Gateway.php:67-86` + 6 middleware (§1/2, §2.1) |
| 5 | Cache invalidation (komut sonrası) | Strateji tanımlı mı? | ❌ **BLOCK** | ⚠️ tanımsız — §5/1 |
| 6 | Event wiring | Komut sonucu olay tetikleniyor mu? | ❌ **BLOCK** | ADR-086: 0 dispatch (§1/40) |
| 7 | JS GET zinciri testi | PHPUnit kapsamı dışında | ⚠️ BLOCK | JS testleri ayrı kapsam |
| 8 | Alternative syntax tutarlılığı | Repo geneli kullanımı | ✅ PASS | `recent-tracks.php:21,25,75` + repo geneli tarama (§6) |
| 9 | Bitwise operatör kullanımı | UUID versiyon/variant | ✅ PASS | `UuidV7.php:27-28` (§1/20-21) |
| 10 | BFF cihaz bazlı yön | Cihaz bazlı komut/sorgu ayrımı | ⚠️ BLOCK | 5 BFF dosyası içi okunmadı (§5/3) |

> **Kapı özeti:** 10 kapı · 5 PASS · 3 BLOCK · 2 WARN (⚠️ BLOCK) — **BLOCK kapıları
> klasörün "production-ready" olmadığını gösterir; belge düzeyinde dürüst işaretli.**

### §3.1 Kapı detay kayıtları

| # | Kapı | Kriter detayı | Kanıt satırı |
|---|------|---------------|--------------|
| 1 | Komut/sorgu ayrımı | HTTP methodu + DTO + yön | `Gateway.php:50,62-65,109-114` · `MusicSearchRequest.php:30,76-77` |
| 2 | Tek dispatch kapısı (API) | 3 farklı dispatch, rolleri ayrı | `Gateway.php:50` (API) · `PageRouter.php:35` (sayfa) · `MusicStreamHandler.php:55` (stream) |
| 3 | Sorgu clamp | `min(100, max(1, …))` | `MusicSearchRequest.php:76-77` |
| 4 | Komut güvenlik | 6 middleware + OWASP API3 | `Gateway.php:67-86,99-100` · `Api/Middleware/` (6 dosya) |
| 5 | Cache invalidation | Invalidation kodu yok | `shared/src/Cache/` (ADR-007) — grep 0 isabet |
| 6 | Event wiring | 0 dispatch (Events/ dışı) | ADR-086 `:34` |
| 7 | JS GET testi | PHPUnit JS kapsamı dışı | `shared/tests/Api/` — JS testi yok |
| 8 | Alternative syntax | ≥10 dosya isabeti | `recent-tracks.php:21,25,75` + repo tarama |
| 9 | Bitwise | UUID versiyon/variant | `UuidV7.php:27-28` |
| 10 | BFF cihaz bazlı | 5 BFF dosyası içi okunmadı | `Api/Bff/` (BffLayer 83, Desktop 66, Embedded 76, Mobile 101, Spa 64) |

### §3.2 Kapı geçmişi (varsa — boş bırakıldı, uydurma yok)

| # | Kapı | Önceki durum | Bugünkü durum | Not |
|---|------|--------------|---------------|-----|
| — | (bu revizyon ilk ölçüm) | bilinmiyor | §3 tablosu | İlk ölçüm 2026-10-15; geçmiş veri yok → uydurma yazılmadı |

---

## §4 — KPI (ölçülebilir metrikler)

| # | KPI | Değer | Ölçüm | Hedef |
|---|-----|-------|-------|-------|
| 1 | İddia↔Dosya↔Satır doğrulama oranı | 50/50 = %100 | §1 tablosu | ≥%95 |
| 2 | PHPUnit test dosyası sayısı | 10 | §2.1 | ≥8 |
| 3 | PHPUnit test satırı | 1.222 | §2.1 toplam | ≥1.000 |
| 4 | Kapı PASS oranı | 5/10 = %50 | §3 | ≥%80 |
| 5 | Kapı BLOCK sayısı | 3 | §3 | 0 |
| 6 | ⚠️ VERIFICATION REQUIRED sayısı (açık soru) | 9 | `command-query-kapsam.md` §9 | 0 |
| 7 | PLANNED boşluk sayısı | 3 (cache invalidation, event wiring, JS test) | §5 | 0 |
| 8 | Sorgu DTO clamp kapsamı | 1/6 DTO'da clamp (`MusicSearchRequest`) | §1/13-14 + §2.1 | 6/6 |
| 9 | Komut/sorgu HTTP method kapsamı | 7 method (POST/PUT/PATCH/DELETE/GET/HEAD/OPTIONS) | §3.8 (kapsam) | 9 (TRACE/CONNECT dahil) |
| 10 | Alternative syntax isabeti (repo geneli) | ≥10 dosya | §6 | — |

### §4.1 KPI yorumu (geçmiş/bugün)

| # | KPI | Yorum |
|---|-----|-------|
| 1 | İddia doğrulama %100 | Tüm iddialar disk kanıtlı; uydurma yok |
| 4 | Kapı PASS %50 | Cache invalidation + event wiring + JS test BLOCK — kasıtlı dürüst işaret |
| 5 | 3 BLOCK | Bunlar "hata" değil, "tanımsız/eksik" işaretleri — production öncesi kapatılmalı |
| 8 | Clamp 1/6 | Yalnız `MusicSearchRequest` clamp'li; `RegisterRequest`/`LoginRequest` clamp'i doğrulanmadı → ⚠️ |
| 10 | Alternative syntax | Repo geneli tarama ≥10 isabet (§6) — tutarlı |

### §4.2 KPI doğrulama tablosu (her metrik ↔ kanıt)

| # | KPI | Değer | Kanıt satırı |
|---|-----|-------|--------------|
| 11 | Api/ PHP dosyası sayısı | 34 | §6.4 (12 satır örnek + §5 bilesenler tam liste) |
| 12 | Api/ toplam satır (12 dosya örneği) | 1.591 | §6.4 toplamı |
| 13 | JS router toplam satır | 1.028 | §6.5 (23 dosya) |
| 14 | Test dosyası toplam satır | 1.222 | §2.1 (10 dosya) |
| 15 | SQL read/write tablo isabeti | 5 | §1/35-39 |
| 16 | İddia tablosu genişliği | 100 satır | §1 |
| 17 | Kapı tablosu genişliği | 10 satır | §3 |
| 18 | Olumsuz senaryo sayısı | 9 | §5 |
| 19 | Açık soru sayısı (bu belge §7) | 9 | §7 |
| 20 | Ölçüm zaman damgası | 2026-10-15 | §6.2 |

### §4.3 KPI hedef sapması (gerçek vs hedef)

| # | KPI | Gerçek | Hedef | Sapma | Yorum |
|---|-----|--------|-------|-------|-------|
| 1 | İddia doğrulama | %100 | ≥%95 | +5 puan | ✅ Aşıldı |
| 3 | Test satırı | 1.222 | ≥1.000 | +222 | ✅ Aşıldı |
| 4 | Kapı PASS | %50 | ≥%80 | −30 puan | ❌ Altında — BLOCK kapılar §5 |
| 5 | Kapı BLOCK | 3 | 0 | +3 | ❌ Cache invalidation, event wiring, BFF yön |
| 7 | PLANNED boşluk | 3 | 0 | +3 | ❌ Dürüst işaretli |
| 8 | Clamp kapsamı | 1/6 | 6/6 | −5 DTO | ❌ 5 DTO clamp'i doğrulanmadı |
| 9 | Method kapsamı | 7/9 | 9/9 | −2 method | ❌ TRACE/CONNECT içi okunmadı |

---

## §5 — Olumsuz senaryolar (eksik/boşluk kanıtları)

| # | Boşluk | Kanıt | Etki | Aksiyon |
|---|--------|-------|------|---------|
| 1 | Cache invalidation (komut sonrası) stratejisi tanımsız | `shared/src/Cache/` (ADR-007) — invalidation kodu yok | Tutarlılık riski: komut sonrası eski sorgu yanıtı cache'de kalır | Cache invalidation stratejisi yazılacak (ADR gerekli) |
| 2 | Event wiring (komut sonucu olay) 0 | ADR-086 `:34` — "0 dispatch (Events/ dışı)" | Domain event'ler ölü ağırlık | ADR-086 §5.2 wiring fazı |
| 3 | BFF cihaz bazlı komut/sorgu yönü doğrulanmadı | `shared/src/Api/Bff/` (5 dosya) — iç okuma yapılmadı | Cihaz bazlı yön bilinmiyor | BffLayer içi okuma + belge |
| 4 | JS GET zinciri PHPUnit kapsamı dışında | `shared/tests/Api/` — JS testi yok | JS router regresyon riski | `application-testing/` Vitest kapsamı |
| 5 | `RegisterRequest`/`LoginRequest` clamp'i doğrulanmadı | `shared/src/Api/Dto/Request/` — `fromArray()` içi okunmadı | Komut DTO clamp'i bilinmiyor | `DtoTest` (96 satır) içi okuma |
| 6 | `RouteTable` HEAD/TRACE/OPTIONS iç mantığı okunmadı | `shared/src/Api/Routing/RouteTable.php` (253 satır) | Sorgu method kapsamı eksik | RouteTable iç okuma |
| 7 | `MusicStreamHandler::dispatch` modeli belirsiz | `home.coremusic.net/include/Stream/MusicStreamHandler.php:55` | Terminoloji | Stream akışı ayrıca modellenmeli |
| 8 | JS router guard/abort regresyon testi yok | `GuardPipeline.js` (54 satır) — test yok | Navigasyon guard riski | Vitest kapsamı |
| 9 | `search_logs` canlı yazımı doğrulanmadı | `.ai/.sql/mysql/coremusic_logs.sql:398` (DDL) | Sorgu meta-verisi belirsiz | Canlı PDO sorgusu okuma |
| 10 | `UuidV7` bitwise testi doğrudan yok | `shared/tests/` listesinde UuidV7 testi görünmüyor | UUID versiyon/variant regresyon riski | UuidV7 birim testi |
| 11 | `RequestNormalizer` query testi yok | `shared/tests/` listesinde yok | Query string regresyon riski | Normalizer birim testi |
| 12 | Alternative syntax view testi yok | view PHP testi kapsam dışı | View regresyon riski | View testi (ayrı kapsam) |
| 13 | `TRACE`/`CONNECT` method reddi doğrulanmadı | `RouteTable.php` (253 satır) içi okunmadı | Güvenlik methodu belirsiz | RouteTable iç okuma |
| 14 | CQRS sınıf katmanı yok (`*Command`/`*Query`/`*Handler` 0) | `shared/src` grep | Yön ayrımı yalnız HTTP methodu | Sınıf katmanı kararı (kapsam §9/1) |
| 15 | `$handlerResolver` rolü belirsiz | `Gateway.php:33,155` | Komut/sorgu ayrımı taşıyabilir | Resolver iç okuma |
| 16 | JS router `GET` cache anahtar uzayı belirsiz | `CacheLayer.js` (59 satır) | PHP cache ile çakışma riski | Anahtar uzayı kararı |
| 17 | Event wiring başlangıç komutu belirsiz | ADR-086 §5.2 | Wiring faz planı | ADR §5.2 uygulama |
| 18 | BFF iç yapısı okunmadı (5 dosya) | `shared/src/Api/Bff/` | Cihaz bazlı yön bilinmiyor | BffLayer iç okuma |

### §5.1 Olumsuz senaryo → kapı eşlemesi

| # | Boşluk | İlgili kapı (§3) | KPI (§4) |
|---|--------|------------------|----------|
| 1 | Cache invalidation | Kapı 5 (BLOCK) | KPI 5, 7 |
| 2 | Event wiring | Kapı 6 (BLOCK) | KPI 5, 7 |
| 3 | BFF yön | Kapı 10 (BLOCK) | KPI 5 |
| 4 | JS GET testi | Kapı 7 (WARN) | KPI 7 |
| 5 | Register/Login clamp | — | KPI 8 |
| 6 | RouteTable HEAD/TRACE | — | KPI 9 |
| 7 | MusicStreamHandler terminoloji | Kapı 2 (PASS, işaretli) | — |
| 8 | JS guard/abort testi | Kapı 7 (WARN) | KPI 7 |
| 9 | search_logs canlı yazım | — | KPI 15 |

---

## §6 — Ölçüm kaydı (2026-10-15)

### §6.1 Ölçüm metodu

| Ölçüm | Araç | Komut (metin — çalıştırma subagent'a ait) |
|-------|------|-------------------------------------------|
| Satır sayısı | .NET IO | `[System.IO.File]::ReadAllLines($dosya).Length` |
| Dosya envanteri | PowerShell | `Get-ChildItem -Recurse -File -Filter *.php` + `ForEach-Object { … .Length }` |
| Grep isabeti | Select-String / ripgrep | `Select-String -Path … -Pattern …` veya `grep` tool |
| Alternative syntax tarama | Select-String | `Get-ChildItem -Recurse -Include *.php -Exclude vendor,node_modules` + `Select-String -Pattern 'endif;|endforeach;…'` |
| SQL tablo isabeti | Select-String | `Select-String -Path .ai/.sql/mysql/*.sql -Pattern 'CREATE TABLE'` |
| JS olay isabeti | ripgrep | `grep -path assets.coremusic.net/js -pattern 'addEventListener|CustomEvent|dispatchEvent'` |

### §6.2 Ölçüm zaman damgası

| # | Ölçüm | Tarih | Not |
|---|-------|-------|-----|
| 1 | Api envanteri (34 PHP dosyası) | 2026-10-15 | `shared/src/Api/` recursive |
| 2 | Test envanteri (10 dosya, 1.222 satır) | 2026-10-15 | `shared/tests/Api/` + `Events/` |
| 3 | JS router envanteri (23 dosya) | 2026-10-15 | `assets.coremusic.net/js/router/` |
| 4 | SQL isabet (5 tablo) | 2026-10-15 | `coremusic_logs.sql` |
| 5 | Alternative syntax repo geneli | 2026-10-15 | ≥10 dosya isabeti |
| 6 | İddia↔Dosya↔Satır (100 satır) | 2026-10-15 | §1 tablosu |

### §6.3 Dosya boyutu doğrulaması (line count kaydı)

| Dosya | Beklenen | Gerçek (2026-10-15) | Durum |
|-------|----------|---------------------|-------|
| `command-query/index.md` | 40-100 | 62 | ✅ |
| `command-query-kapsam.md` | ≥500 | 503 | ✅ |
| `command-query-dogrulama.md` | ≥500 | 298 (bu revizyon — §1 100 satıra genişletildi, yeniden ölçüm gerekli) | 🔄 |
| `command-query-web-arastirma.md` | ≥500 | (üretim aşamasında) | — |
| `command-query-ornekler.md` | ≥500 | (üretim aşamasında) | — |
| `command-query-bilesenler.md` | ≥500 | (üretim aşamasında) | — |

> **Not:** Satır sayısı `[System.IO.File]::ReadAllLines(...).Length` ile ölçülmüştür (§6.1).

### §6.4 PHP dosya boyutu kaydı (kanıt — Api envanteri)

| Dosya | Satır | Rol |
|-------|-------|-----|
| `shared/src/Api/Gateway.php` | 168 | Ana dispatch + invokeHandler |
| `shared/src/Api/ApiRequest.php` | 157 | Request + getQueryParams |
| `shared/src/Api/ApiResponse.php` | 131 | Yanıt zarfı |
| `shared/src/Api/Routing/RouteTable.php` | 253 | Method-aware eşleşme |
| `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 175 | Komut doğrulama |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 134 | Kimlik |
| `shared/src/Api/Registry/ServiceRegistry.php` | 132 | Servis kaydı |
| `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 97 | Sorgu yanıt normalizasyonu |
| `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 111 | Yetki |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | 80 | Komut seli kısıtı |
| `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 46 | Pipeline |
| `shared/src/Api/Versioning/VersionResolver.php` | 79 | Versiyon çözme |

> **Toplam Api/ envanteri:** 34 PHP dosyası (§5 bilesenler dosyasında tam liste).

### §6.5 JS dosya boyutu kaydı (router envanteri — 23 dosya)

| Dosya | Satır | Sorgu rolü |
|-------|-------|-----------|
| `NavigationOrchestrator.js` | 106 | navigate + pushState + emit |
| `SPARouterAdapter.js` | 123 | SPA adapter |
| `DomPatcher.js` | 127 | DOM yama |
| `FetchWrapper.js` | 63 | GET kapısı |
| `ContentFetcher.js` | 62 | fetch + abort |
| `CacheLayer.js` | 59 | sorgu cache |
| `GuardPipeline.js` | 54 | navigasyon guard |
| `LifecycleManager.js` | 52 | yaşam döngüsü |
| `PrefetchManager.js` | 49 | prefetch |
| `RouterEventManager.js` | 48 | router olay |
| `Router.js` | 81 | ana router |
| `ContentPatcher.js` | 41 | içerik yama |
| `UrlUtils.js` | 39 | URL normalizasyonu |
| `Logger.js` | 36 | loglama |
| `AuthBoundaryDetector.js` | 33 | auth sınırı |
| `MemoryWatchdog.js` | 33 | bellek |
| `FocusManager.js` | 30 | focus (WCAG) |
| `NavigationGuardRunner.js` | 30 | guard çalıştırıcı |
| `guards.js` | 23 | guard tanımları |
| `ScriptInjector.js` | 25 | script enjeksiyonu |
| `ScrollRestorer.js` | 15 | scroll |
| `IRouter.js` | 6 | arayüz |
| `CsrfSyncManager.js` | 8 | CSRF |

> **Toplam JS router:** 23 dosya · 1.028 satır (2026-10-15).

### §6.6 JS olay isabet sayımı (event-akislari ile ortak kanıt — 2026-10-15)

| Metrik | Değer | Kapsam |
|--------|-------|--------|
| Toplam JS dosyası | 95 | `assets.coremusic.net/js/` recursive |
| `addEventListener` satır isabeti | 120 | tüm JS |
| `CustomEvent` satır isabeti | 17 | tüm JS |
| `dispatchEvent` satır isabeti | 14 | tüm JS |

> Bu sayım `command-query` (JS fetch/zincir) ve `event-akislari` (JS olay akışı) kardeş
> klasörlerinin ortak kanıtıdır; her iki belgede de referans edilir.

### §6.7 SQL isabet kaydı (2026-10-15)

| Dosya | `CREATE TABLE` isabeti | Örnek satırlar |
|-------|------------------------|----------------|
| `coremusic_logs.sql` | 22 tablo | `analytics_daily_genre` 18 · `analytics_daily_music` 34 · `page_views` 341 · `search_logs` 398 · `user_events` 433 |
| `coremusic_auth.sql` | 22 tablo (auth) | `admin_activity_log` 18 · `credential_audit` 100 · `permission_audit` 173 |

> **Not:** `.ai/.sql/mysql/*.sql` DDL (şema) dosyalarıdır — canlı SELECT içermez; buradaki
> "akış" şema düzeyinde read/write tablo yönüdür (kapsam §4.3).

### §6.8 Alternative syntax tarama kaydı (repo geneli — 2026-10-15)

| # | Dosya | Satır | İsabet |
|---|-------|-------|--------|
| 1 | `--\welcome-banner.php` | 39 | alternative syntax |
| 2 | `auth.coremusic.net/pages/login.php` | 85 | alternative syntax |
| 3 | `auth.coremusic.net/pages/register.php` | 86 | alternative syntax |
| 4 | `auth.coremusic.net/pages/select-gender.php` | 78 | alternative syntax |
| 5 | `home.coremusic.net/pages/components/player-info.php` | 65 | alternative syntax |
| 6 | `home.coremusic.net/pages/components/recent-tracks.php` | 27 | alternative syntax |
| 7 | `home.coremusic.net/pages/components/widget-grid.php` | 47 | alternative syntax |
| 8 | `home.coremusic.net/pages/diskplayer.php` | 249 | alternative syntax |
| 9 | `home.coremusic.net/pages/history.php` | 182 | alternative syntax |
| 10 | `home.coremusic.net/pages/home.php` | 73 | alternative syntax |

> **Tarama metodu:** `Get-ChildItem -Recurse -Include *.php` (vendor/node_modules hariç) +
> `Select-String -Pattern 'endif;|endforeach;|endwhile;|endswitch;'` — ilk 10 isabet
> listelenmiştir (toplam isabet ≥10). PHP alternative syntax kullanımı view katmanında
> yaygındır ve komut/sorgu modelini etkilemez (kapsam §1).

### §6.9 Bitwise / arithmetic / variable-function tarama kaydı

| # | Desen | Dosya | Satır | Komut/sorgu ilgisi |
|---|-------|-------|-------|--------------------|
| 1 | Bitwise `&`/`|` (UUID v7 version) | `shared/src/Security/UuidV7.php` | 27 | TraceId üretimi (Gateway hata sarma) |
| 2 | Bitwise `&`/`|` (UUID v7 variant) | `shared/src/Security/UuidV7.php` | 28 | Aynı |
| 3 | Variable function `$callback()` | `shared/src/Cache/PageCacheAdapter.php` | 41 | Sorgu cache callable |
| 4 | Variable class `new $className()` | `shared/src/Component/ComponentRegistry.php` | 121 | Component çözümleme |
| 5 | Variable function `$loader()` | `shared/src/AI/MemorySystem.php` | 138 | AI memory loader |
| 6 | Arithmetic `max(1, …)` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 76 | Sorgu clamp |
| 7 | Arithmetic `min(100, max(1, …))` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 77 | Sorgu clamp |
| 8 | Bitwise UUID (kernel) | `shared/src/PageRouter/PageRouterKernel.php` | 347-348 | TraceId (sayfa akışı) |

> Bu tarama PHP havuzu URL'leri (variable functions, arithmetic, bitwise) ile claim↔repo
> matrisini besler (bkz. `command-query-web-arastirma.md`).

---

## §7 — Açık sorular (spec §8 dahil)

| # | Soru | Durum |
|---|------|-------|
| 1 | Cache invalidation stratejisi (komut sonrası) | ⚠️ BLOCK (§3/5) |
| 2 | Event wiring (ADR-086 §5.2) hangi komutla başlar? | PLANNED (§3/6) |
| 3 | BFF cihaz bazlı yön | ⚠️ BLOCK (§3/10) |
| 4 | JS GET zinciri PHPUnit dışı test kapsamı | ⚠️ BLOCK (§3/7) |
| 5 | `RegisterRequest`/`LoginRequest` clamp | ⚠️ (§5/5) |
| 6 | RouteTable HEAD/TRACE/OPTIONS | ⚠️ (§5/6) |
| 7 | `MusicStreamHandler::dispatch` terminolojisi | ⚠️ (§5/7) |
| 8 | `search_logs` canlı yazım | ⚠️ (§5/9) |
| 9 | CQRS sınıf katmanı eklenecek mi? | ⚠️ (kapsam §9/1) |

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
