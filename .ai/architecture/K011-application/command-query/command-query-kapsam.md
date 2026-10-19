---
title: "K011 · command-query — kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/command-query — Kapsam

**Amaç:** Bu belge `command-query` klasörünün sınırını çizer: CoreMusic'te komut (yazma) ve sorgu
(okuma) akışları hangi disk yüzeyleridir, hangileri DEĞİLDİR. Her iddia dosya+satır kanıtlıdır;
kanıtsız iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | ELI15 | mimari | kavram | Komut/sorgu ayrımı basit dilde |
| §2 | Sınır — ne DEĞİL | mimari | negatif | Kapsam dışı yüzeyler |
| §3 | Komut akışları | mimari | yazma | Yazma yönü akış tabloları (HTTP + JS + event) |
| §4 | Sorgu akışları | mimari | okuma | Okuma yönü akış tabloları (HTTP + JS + SQL) |
| §5 | Kardeş klasör farkı | mimari | komşuluk | commands/queries/usecase-akislari ile ayrım |
| §6 | Kapsam dışı | mimari | red | Bu belgeyi aşan konular |
| §7 | Senaryo tabloları | mimari | akış | Komut/sorgu uçtan uca senaryoları |
| §8 | Terimler | mimari | dil | Komut-sorgu sözlüğü (repo-kaynaklı) |
| §9 | Açık sorular | mimari | boşluk | Spec §8 kaydı dahil |

---

## §1 — ELI15 (komut vs sorgu)

Dükkâna gidiyorsunuz. "Şu sütü alıyorum" der kasaya — **komut**: dünya değişir, kasa fişi
(yanıt) döner. "Bu süt ne kadar?" dersiniz — **sorgu**: dünya değişmez, sadece bilgi döner.
CoreMusic'te de aynı ayrım var:

- **Komut (command):** veritabanını veya durumu değiştirir. `POST /api/v1/auth/login`,
  `POST /api/v1/music`, gender kaydetme (`/set-gender`). HTTP methodu **POST/PUT/PATCH/DELETE**.
- **Sorgu (query):** sadece okur, değiştirmez. `GET /api/v1/auth/me`, `GET /api/v1/music`,
  arama (`?query=...`). HTTP methodu **GET/HEAD**.

Bu ayrım CoreMusic'te **ayrı sınıf ağacıyla** değil, mevcut üç katmandaki işaretlerle kurulur:

| İşaret | Nerede | Kanıt |
|--------|--------|-------|
| HTTP methodu | Rota tablosu method-aware eşleşir | `shared/src/Api/Routing/RouteTable.php` (253 satır — method eşleşmesi `Gateway::matchRoute` üzerinden, `shared/src/Api/Gateway.php:109-114`) |
| Route anahtarı | `_route` / `_route_status` / `_route_allow` | `shared/src/Api/Gateway.php:63-65` |
| Request DTO | Sorgu DTO'su `getQuery()` taşır; komut DTO'su `fromArray()` ile doğrulanır | `shared/src/Api/Dto/Request/MusicSearchRequest.php:30` ve `:70-79` |
| Middleware zinciri | Auth + Validation komut/sorgu fark etmez, hep çalışır | `shared/src/Api/Gateway.php:67-86` (route eşleşmesi pipeline dışında, `:62`) |
| JS tarafı | `fetch` methodu belirler; sorgu `URLSearchParams` üretir | `assets/coremusic.net/js/auth/gender-select.js:45-48` (komut `POST`), `assets/coremusic.net/js/router/FetchWrapper.js:52` (sorgu `GET`) |

### §1.1 — Neden ayrı sınıf yok? (repo gerçeği)

2026-10-15 ölçümünde `shared/src` altında `*Command`, `*Query` adlı sınıf **0 adet**;
`Handler` geçen isimler:

| İsabet | Dosya | Yorum |
|--------|-------|-------|
| `ErrorHandler` | `shared/src/PageRouter/ErrorHandler.php:5` | hata sunucusu — CQRS handler'ı DEĞİL |
| `FileHandler` | `shared/src/Log/FileHandler.php:30` | logger — CQRS handler'ı DEĞİL |
| `MusicStreamHandler` | `home.coremusic.net/include/Stream/MusicStreamHandler.php:55` | stream çıkış noktası (`dispatch()` statik, satır 55) — HTTP dispatcher DEĞİL |
| `invokeHandler()` | `shared/src/Api/Gateway.php:152` | Gateway'in callable çözümleyicisi — en yakın CQRS handler karşılığı |
| `ragQuery()` | `shared/src/Contracts/AI/KnowledgeBaseInterface.php:32` | AI RAG sorgusu — API sorgu DTO'su DEĞİL |
| `invokeHandler()` | `shared/src/Api/Gateway.php:152` | en yakın CQRS handler karşılığı (§1.1 tekrar — bkz. satır 152-159) |

> **Ayrıca:** `shared/src/PageRouter/PageRouter.php:35` — `dispatch(array $request, string $csrfToken = '', bool $isSpaRequest = false)`:
> PageRouter'da da bir `dispatch()` vardır; bu HTTP istek dağıtımıdır (sayfa akışı), API
> Gateway dispatch'inden ayrıdır. Komut/sorgu modeli bu klasörde yalnız API Gateway
> dispatch'ini (`shared/src/Api/Gateway.php:50`) merkez alır; PageRouter dispatch'i
> `usecase-akislari/` kardeşinin omurgasıdır.

Sonuç: komut/sorgu ayrımı **akış düzeyinde** (HTTP method + DTO + yön) modellenir; sınıf
katmanı eklenirse bu belge revize edilir.

---

## §2 — Sınır: bu klasör NE DEĞİL

| # | Bu klasör… | DEĞİL | Neden / kanıt |
|---|-----------|-------|---------------|
| 1 | Akış (akış diyagramı + yön) tarif eder | Sınıf envanteri değil | Sınıf envanteri kardeş `commands/` ve `queries/` klasörlerinin işidir; bu klasör akışın "nereye gittiğini" yazar |
| 2 | HTTP komut/sorgu ayrımını modeller | CQRS framework tanıtımı değil | Repo'da `*Command`/`*Query`/`*Handler` sınıfı **0 adet** (2026-10-15 grep — §1.1 tablosu) |
| 3 | Gateway + router omurgasını kullanır | Yeni dispatcher yazmaz | `shared/src/Api/Gateway.php:50` mevcut tek dispatch kapısı (PHP) · `js/router/NavigationOrchestrator.js:41` (JS) |
| 4 | Veri yönünü (read/write) işaretler | Yetki denetimi yapmaz | Auth/Authorization middleware'leri `shared/src/Api/Middleware/` altındadır (Authentication 134 satır, Authorization 111 satır) — bu klasör onları yalnız "her akışta çalışır" diye kaydeder |
| 5 | PHP + JS + SQL + CSS yüzeyini birlikte tarar | Sadece backend değil | Vanilla JS router `assets/coremusic.net/js/router/` (23 dosya) komut/sorgu üretimini yapar; CSS yalnız komut/sorgu UI durumlarının çıktısıdır |
| 6 | ADR-086 event wiring'ini uygular | Event klasörü değil | Wiring `event-akislari/` kardeşinin konusu; burada yalnız "komut sonucu olay tetikler mi" sorusu geçer (§3.3) |

---

## §3 — Komut akışları (yazma yönü)

### §3.1 HTTP komut akışı (PHP — API Gateway)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Entry | `api.coremusic.net/index.php` | 182 | `$gateway->dispatch($pipelineRequest)` |
| 2 | Request objesi | `shared/src/Api/Gateway.php` | 52-53 | `new ApiRequest()` |
| 3 | Versiyon çözme | `shared/src/Api/Gateway.php` | 56-57 | `withAttribute('version', …)` |
| 4 | Rota eşleşme (method-aware) | `shared/src/Api/Gateway.php` | 62-65 | `_route` / `_route_status` / `_route_allow` |
| 5 | Middleware pipeline | `shared/src/Api/Gateway.php` | 68 | `middlewarePipeline->process(…)` |
| 6 | 405 ayrımı | `shared/src/Api/Gateway.php` | 74-76 | `methodNotAllowedResponse()` — komut methodu sunulmuyorsa |
| 7 | Handler çağrısı | `shared/src/Api/Gateway.php` | 84 → 152-159 | `invokeHandler()` — `($this->handlerResolver)($route, $request)` satır 155 |
| 8 | Placeholder yanıt | `shared/src/Api/Gateway.php` | 162-166 | `Handler not implemented yet` (Faz 1b öncesi dürüst dönüş) |
| 9 | Hata sarma | `shared/src/Api/Gateway.php` | 87-101 | traceId + `INTERNAL_ERROR` 500; exception detayı istemciye dönmez (OWASP API3 notu `:99`) |

### §3.2 JS komut akışı (form → fetch POST)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Olay dinleyici | `assets/coremusic.net/js/auth/gender-select.js` | 24 | `btn.addEventListener('click', …)` |
| 2 | Form submit yakalama | `assets/coremusic.net/js/auth/gender-select.js` | 34 | `form.addEventListener('submit', …)` + `e.preventDefault()` |
| 3 | Komut URL üretimi | `assets/coremusic.net/js/auth/gender-select.js` | 45-46 | `qs` + `postUrl = '/set-gender' + qs` |
| 4 | Komut gönderimi | `assets/coremusic.net/js/auth/gender-select.js` | 48 | `fetch(postUrl, { method: 'POST', … })` |
| 5 | CSRF getirme | `assets/coremusic.net/js/router/CsrfSyncManager.js` | 1-8 | CSRF senkronu (8 satır — küçük modül) |

### §3.3 Komut sonrası olay (PHP event — wiring PLANNED)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Event altyapısı | `shared/src/Events/EventDispatcher.php` | 28-45 | `dispatch(object $event): object` |
| 2 | Domain event örneği | `shared/src/Events/Domain/MusicPlayedEvent.php` | 20-61 | `eventName(): 'music.played'` satır 39 |
| 3 | Wiring durumu | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | 34 | "Production dispatch → **0** (Events/ dışı) → ❌ PLANNED (wiring)" |

### §3.4 Komut DTO doğrulama zinciri

| # | DTO | Dosya | Doğrulama | Satır |
|---|-----|-------|-----------|-------|
| 1 | `RegisterRequest` | `shared/src/Api/Dto/Request/RegisterRequest.php` | `fromArray()` clamp + tip dönüşümü (94 satır) | 1-94 |
| 2 | `LoginRequest` | `shared/src/Api/Dto/Request/LoginRequest.php` | `fromArray()` (72 satır) | 1-72 |
| 3 | `PlaylistCreateRequest` | `shared/src/Api/Dto/Request/PlaylistCreateRequest.php` | `fromArray()` (72 satır) | 1-72 |
| 4 | `UserUpdateRequest` | `shared/src/Api/Dto/Request/UserUpdateRequest.php` | `fromArray()` (72 satır) | 1-72 |
| 5 | `MusicSearchRequest` (sorgu) | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | `max(1, …)` / `min(100, max(1, …))` clamp | 76-77 |

> **Yön işareti:** `fromArray()` komut/sorgu fark etmez; asıl yön işareti HTTP methodudur
> (`RouteTable` method-aware eşleşme). `MusicSearchRequest` GET (sorgu), `RegisterRequest`
> POST (komut) kullanır — DTO tipi tek başına yön vermez.

### §3.5 OAuth komut akışı (bağlantı kurma/sökme)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Platform listesi (sorgu) | `assets/coremusic.net/js/oauth-manager.js` | 21 | `fetch(`${API_BASE}/platforms?gender=${gender}`)` |
| 2 | Connect komutu | `assets/coremusic.net/js/oauth-manager.js` | 46 | `fetch(`${API_BASE}/connect`)` |
| 3 | Disconnect komutu | `assets/coremusic.net/js/oauth-manager.js` | 120 | `fetch(`${API_BASE}/disconnect`)` |
| 4 | Bağlantı listesi (sorgu) | `assets/coremusic.net/js/oauth-manager.js` | 144 | `fetch(`${API_BASE}/connections`)` |

### §3.6 Komut güvenlik yüzeyi (her komutta zorunlu)

| # | Katman | Dosya | Satır/Kapsam | Not |
|---|--------|-------|--------------|-----|
| 1 | CSRF | `shared/src/Middleware/` (CSRF ADR-010) | pipeline | Komut istekleri CSRF token ister; JS tarafı `CsrfSyncManager.js` (8 satır) |
| 2 | Origin/CORS | `shared/src/Middleware/` (CORS, Origin) | pipeline | Yabancı origin'den komut reddi |
| 3 | Rate limit | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 80 satır | Komut seli kısıtı |
| 4 | Validation | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 175 satır | Komut gövdesi doğrulama |
| 5 | Authentication | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | 134 satır | Kimlik kontrolü |
| 6 | Authorization | `shared/src/Api/Middleware/AuthorizationMiddleware.php` | 111 satır | Yetki kontrolü |
| 7 | Hata gizleme | `shared/src/Api/Gateway.php` | 99-100 | OWASP API3: exception detayı istemciye dönmez |

> **Not (yön bağımsız):** Bu katmanlar komut ve sorguda AYRI çalıştırılmaz — pipeline
> (`shared/src/Api/Middleware/ApiMiddlewarePipeline.php`, 46 satır) method bağımsız çalışır;
> komut/sorgu farkı yalnız handler çıktısında (yazma vs okuma) ortaya çıkar.

### §3.7 Komut yanıt zarfı (response DTO)

| # | Response DTO | Dosya | Satır | Yön |
|---|--------------|-------|-------|-----|
| 1 | `LoginResponse` | `shared/src/Api/Dto/Response/LoginResponse.php` | 83 satır | komut (login POST) sonucu |
| 2 | `UserResponse` | `shared/src/Api/Dto/Response/UserResponse.php` | 116 satır | komut (register) / sorgu (me) ortak |
| 3 | `PlaylistResponse` | `shared/src/Api/Dto/Response/PlaylistResponse.php` | 94 satır | komut (create) / sorgu (list) ortak |
| 4 | `MusicResponse` | `shared/src/Api/Dto/Response/MusicResponse.php` | 116 satır | sorgu (list/detail) |
| 5 | `PaginationResponse` | `shared/src/Api/Dto/Response/PaginationResponse.php` | 114 satır | sorgu (listeleme sayfalama) |

> **Yön notu:** Response DTO'ları yön taşımaz — komut ve sorgu aynı zarfı kullanabilir;
> asıl ayrım request tarafında (method + `_route_status`) kurulur (§1 tablosu).

### §3.8 Komut method dağılımı (rota tablosu — method eşleşmesi)

| # | Method | Tip | Gateway davranışı | Kanıt |
|---|--------|-----|-------------------|-------|
| 1 | `POST` | komut | Rota eşleşmezse 405 `methodNotAllowedResponse()` | `shared/src/Api/Gateway.php:74-76,125-138` |
| 2 | `PUT` | komut | Aynı 405 yolu | `Gateway.php:125-138` (`Allow` başlığı üretimi `:135`) |
| 3 | `PATCH` | komut | Aynı 405 yolu | `Gateway.php:125-138` |
| 4 | `DELETE` | komut | Aynı 405 yolu | `Gateway.php:125-138` |
| 5 | `GET` | sorgu | 404 `NOT_FOUND` | `Gateway.php:78-80` |
| 6 | `HEAD` | sorgu | `RouteTable` method-aware eşleşir (253 satır) | `shared/src/Api/Routing/RouteTable.php` |
| 7 | `OPTIONS` | sorgu (CORS preflight) | CORS middleware tarafından karşılanır | `shared/src/Middleware/` (CORS) — pipeline |
| 8 | Bilinmeyen | — | `STATUS_METHOD_NOT_ALLOWED` ayrımı | `Gateway.php:71,74` |
| 9 | `TRACE`/`CONNECT` | — | Varsayılan reddedilir (güvenlik) | ⚠️ VERIFICATION REQUIRED (RouteTable iç mantık okunmadı) |

> **Test kanıtı:** method routing testi — `shared/tests/Api/GatewayMethodRoutingTest.php`
> (152 satır): `POST /api/v1/auth/login` satır 58 · `PUT /api/v1/auth/login` satır 69 ·
> `GET /api/v1/unknown-endpoint` satır 82 · `GET /api/v1/auth/me` satır 95 ·
> `POST /api/v1/music` satır 147.

### §3.9 Komut akış riskleri (olumsuz senaryo özeti — detay `*-dogrulama.md`)

| # | Risk | Sonuç | Kırılma anı | Kanıt |
|---|------|-------|-------------|-------|
| 1 | Handler bağlı değil | Placeholder yanıt (`Handler not implemented yet`) | Faz 1b öncesi | `shared/src/Api/Gateway.php:162-166` |
| 2 | Method sunulmuyor | 405 + `Allow` başlığı | Rota var, method yok | `Gateway.php:125-138` |
| 3 | Route yok | 404 `NOT_FOUND` | Matcher başarısız | `Gateway.php:78-80` |
| 4 | Handler exception | 500 `INTERNAL_ERROR` (detay log'a, istemciye değil) | `invokeHandler` throw | `Gateway.php:87-101` |
| 5 | Event wiring yok | Domain event üretilmez (ölü ağırlık) | Bugünkü durum | ADR-086 `:34` (0 dispatch) |

---

## §4 — Sorgu akışları (okuma yönü)

### §4.1 HTTP sorgu akışı (PHP)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Query string ayrıştırma | `shared/src/PageRouter/RequestNormalizer.php` | 52 | `parse_url($uri, PHP_URL_QUERY)` |
| 2 | Global query yazımı | `shared/src/PageRouter/RequestNormalizer.php` | 94 | `$_SERVER['QUERY_STRING']` |
| 3 | Param okuma | `shared/src/Api/ApiRequest.php` | 76 | `getQueryParams(): array` |
| 4 | Sorgu DTO üretimi | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 70-79 | `fromArray()` — `max(1,…)` / `min(100,…)` clamp |
| 5 | Sorgu alanı | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 30-33 | `getQuery(): string` |
| 6 | Rota eşleşmesi (GET) | `shared/src/Api/Gateway.php` | 62-80 | `_route_status !== OK` → 404 `ApiResponse::error('NOT_FOUND', …)` |
| 7 | Response sarma | `shared/src/Api/ApiResponse.php` | 1-131 | standart yanıt zarfı (131 satır) |

### §4.2 JS sorgu akışı (router — GET zinciri)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Navigasyon çağrısı | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 41 | `async navigate(url, pushState = true, depth = 0)` |
| 2 | Fetch sarmalayıcı | `assets/coremusic.net/js/router/FetchWrapper.js` | 13 | `async fetch(url, options = {})` |
| 3 | GET varsayılanı | `assets/coremusic.net/js/router/FetchWrapper.js` | 52 | `fetch(url, { method: 'GET', headers, credentials: 'include', signal })` |
| 4 | Cache denemesi | `assets/coremusic.net/js/router/CacheLayer.js` | 1-59 | sorgu yanıtı cache (59 satır) |
| 5 | DOM yama | `assets/coremusic.net/js/router/DomPatcher.js` | 1-127 | sorgu sonucu DOM'a yazılır (127 satır) |
| 6 | History güncelleme | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 83 | `history.pushState(null, document.title, target)` |
| 7 | Tamam olayı | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 94 | `eventBus?.emit('nav:complete', …)` |

### §4.3 SQL sorgu yüzeyi (şema — read tabloları)

| # | Tablo | Dosya | Satır | Amaç |
|---|-------|-------|-------|------|
| 1 | `analytics_daily_genre` | `.ai/.sql/mysql/coremusic_logs.sql` | 18 | günlük tür analitiği (okuma) |
| 2 | `analytics_daily_music` | `.ai/.sql/mysql/coremusic_logs.sql` | 34 | günlük müzik analitiği (okuma) |
| 3 | `page_views` | `.ai/.sql/mysql/coremusic_logs.sql` | 341 | sayfa görüntüleme (okuma) |
| 4 | `search_logs` | `.ai/.sql/mysql/coremusic_logs.sql` | 398 | arama sorgu kaydı (sorgu meta-verisi) |
| 5 | `user_events` | `.ai/.sql/mysql/coremusic_logs.sql` | 433 | kullanıcı olay kaydı (komut sonucu yazımı) |

> Not: `.ai/.sql/mysql/*.sql` **şema (DDL)** dosyalarıdır — canlı SELECT sorguları içermez;
> buradaki "akış" şema düzeyinde read/write tablo ayrımıdır. Uygulama-içi PDO sorguları
> `shared/src/Database/` üzerinden yürür (ADR-002: prepared statement yalnız).

### §4.4 Sorgu cache katmanı (PHP + JS)

| # | Katman | Dosya | Satır | Not |
|---|--------|-------|-------|-----|
| 1 | PHP APCu adapter | `shared/src/Cache/` (Apcu/Memory/PageCache, ADR-007) | 1-∞ | komut sonrası cache geçersizleştirme stratejisi tanımsız → ⚠️ |
| 2 | Page cache callable | `shared/src/Cache/PageCacheAdapter.php` | 41 | `$value = $callback();` — sorgu sonucu cache'lenir |
| 3 | JS cache | `assets/coremusic.net/js/router/CacheLayer.js` | 1-59 | sorgu yanıtı cache (59 satır) |

### §4.5 Sorgu akış component envanteri (JS router — 23 dosya)

| # | Component | Dosya | Satır | Sorgu rolü |
|---|-----------|-------|-------|-----------|
| 1 | `IRouter` | `assets/coremusic.net/js/router/IRouter.js` | 6 | arayüz: `navigate`/`prefetch` (satır 3-4) |
| 2 | `Router` | `assets/coremusic.net/js/router/Router.js` | 81 | `history.scrollRestoration = 'manual'` (satır 52) |
| 3 | `NavigationOrchestrator` | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 106 | `navigate()` satır 41 · `pushState` satır 83 · `emit('nav:complete')` satır 94 |
| 4 | `FetchWrapper` | `assets/coremusic.net/js/router/FetchWrapper.js` | 63 | GET sorgu kapısı satır 52 |
| 5 | `ContentFetcher` | `assets/coremusic.net/js/router/ContentFetcher.js` | 62 | `fetch(target, container)` satır 16 · abort sinyali satır 25 |
| 6 | `PrefetchManager` | `assets/coremusic.net/js/router/PrefetchManager.js` | 49 | `prefetch(url)` satır 20 |
| 7 | `CacheLayer` | `assets/coremusic.net/js/router/CacheLayer.js` | 59 | sorgu cache |
| 8 | `DomPatcher` | `assets/coremusic.net/js/router/DomPatcher.js` | 127 | sorgu sonucu DOM |
| 9 | `GuardPipeline` | `assets/coremusic.net/js/router/GuardPipeline.js` | 54 | navigasyon guard'ları |
| 10 | `NavigationGuardRunner` | `assets/coremusic.net/js/router/NavigationGuardRunner.js` | 30 | guard çalıştırıcı |
| 11 | `ScrollRestorer` | `assets/coremusic.net/js/router/ScrollRestorer.js` | 15 | sorgu sonrası scroll |
| 12 | `FocusManager` | `assets/coremusic.net/js/router/FocusManager.js` | 30 | sorgu sonrası focus (WCAG) |
| 13 | `CsrfSyncManager` | `assets/coremusic.net/js/router/CsrfSyncManager.js` | 8 | CSRF senkronu |
| 14 | `UrlUtils` | `assets/coremusic.net/js/router/UrlUtils.js` | 39 | URL normalizasyonu |
| 15 | `SPARouterAdapter` | `assets/coremusic.net/js/router/SPARouterAdapter.js` | 123 | SPA adapter |
| 16 | `ContentPatcher` | `assets/coremusic.net/js/router/ContentPatcher.js` | 41 | içerik yama |
| 17 | `AuthBoundaryDetector` | `assets/coremusic.net/js/router/AuthBoundaryDetector.js` | 33 | auth sınır tespiti |
| 18 | `LifecycleManager` | `assets/coremusic.net/js/router/LifecycleManager.js` | 52 | yaşam döngüsü |
| 19 | `RouterEventManager` | `assets/coremusic.net/js/router/RouterEventManager.js` | 48 | router olay yönetimi |
| 20 | `MemoryWatchdog` | `assets/coremusic.net/js/router/MemoryWatchdog.js` | 33 | bellek nöbetçisi |
| 21 | `ScriptInjector` | `assets/coremusic.net/js/router/ScriptInjector.js` | 25 | script enjeksiyonu |
| 22 | `Logger` | `assets/coremusic.net/js/router/Logger.js` | 36 | router loglama |
| 23 | `guards` | `assets/coremusic.net/js/router/guards.js` | 23 | guard tanımları |

### §4.6 Sorgu response zinciri (server → client)

| # | Adım | Dosya | Satır | Not |
|---|------|-------|-------|-----|
| 1 | Handler yanıt döner | `shared/src/Api/Gateway.php` | 157-159 | `is_array($response)` kontrolü |
| 2 | Response normalization | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 97 satır | pipeline son katman |
| 3 | Zarf | `shared/src/Api/ApiResponse.php` | 1-131 | standart `ok`/`error` |
| 4 | JS response okuma | `assets/coremusic.net/js/router/FetchWrapper.js` | 13-52 | `fetch` sarmalayıcı |
| 5 | Abort sinyali | `assets/coremusic.net/js/router/ContentFetcher.js` | 25 | `this.#fetcher.fetch(target, { signal: ctrl.signal })` |
| 6 | DOM yama | `assets/coremusic.net/js/router/DomPatcher.js` | 1-127 | sorgu sonucu DOM'a yazılır |
| 7 | Tamam olayı | `assets/coremusic.net/js/router/NavigationOrchestrator.js` | 94 | `emit('nav:complete', …)` |

### §4.7 Sorgu akış riskleri (olumsuz senaryo özeti — detay `*-dogrulama.md`)

| # | Risk | Sonuç | Kanıt |
|---|------|-------|-------|
| 1 | `?query=` boş | `getQuery()` `''` döner (clamp yok) | `shared/src/Api/Dto/Request/MusicSearchRequest.php:73` |
| 2 | `limit` > 100 | `min(100, …)` ile kırpılır (clamp) | `MusicSearchRequest.php:77` |
| 3 | `page` < 1 | `max(1, …)` ile düzeltilir | `MusicSearchRequest.php:76` |
| 4 | Cache'de eski veri | Invalidation stratejisi tanımsız → ⚠️ | `shared/src/Cache/` (ADR-007) |
| 5 | Abort edilmiş istek | `signal` ile iptal | `ContentFetcher.js:25` |
| 6 | Guard reddi | Navigasyon durur | `GuardPipeline.js` (54 satır) |

---

## §5 — Kardeş klasör farkı (komşuluk matrisi)

| Klasör | Eksen | Bu klasörle kesişim | Ayrım çizgisi |
|--------|-------|---------------------|---------------|
| `commands/` | `*Command` tip envanteri | İkisi de yazma yönü | O: "hangi tip var?" · Bu: "akış nereye gidiyor?" |
| `queries/` | `*Query` tip envanteri | İkisi de okuma yönü | O: "hangi tip var?" · Bu: "akış nereye gidiyor?" |
| `usecase-akislari/` | Kullanıcı senaryosu | İkisi de akış | O: senaryo eksenli (login, playback) · Bu: veri yönü eksenli (read/write) |
| `routing/` | Rota tablosu detayı | Komut/sorgu işareti burada doğar | O: matcher içi · Bu: matcher çıktısını yön olarak okur |
| `dto/` | DTO envanteri | Sorgu DTO'ları `getQuery` taşır | O: tip envanteri · Bu: akış içindeki rolü |
| `application-services/` | Servis nesneleri | Komut/sorgu servislerden geçer | O: servis içi · Bu: servis-öncesi yönlendirme |
| `event-akislari/` (kardeş) | Event akışları | Komut sonucu olay tetikler | O: olay yaşam döngüsü · Bu: yalnız komut→olay kapısı (§3.3) |

### §5.1 Eksen karşılaştırma tablosu (4 eksen)

| Eksen | Soru | Klasör | Örnek |
|-------|------|--------|-------|
| Veri yönü (read/write) | "İstek dünyayı değiştiriyor mu?" | **bu klasör** | `POST /set-gender` (komut) vs `GET /api/v1/music` (sorgu) |
| Senaryo (kullanıcı niyeti) | "Kullanıcı hangi hedefi sürüyor?" | `usecase-akislari/` | "Login ol" senaryosu hem komut (login POST) hem sorgu (me GET) içerir |
| Tip (sınıf/envanter) | "Hangi tipler var?" | `commands/`, `queries/` | `*Command` sınıfı var mı? (bugün: 0) |
| Servis (nesne/grup) | "Hangi servis taşıyor?" | `application-services/` | Gateway, BFF, Registry |

### §5.2 Kesişim matrisi (klasör çiftleri)

| Çift | Kesişim var mı? | Örnek |
|------|-----------------|-------|
| bu klasör × `usecase-akislari/` | ✅ | Login senaryosu = komut akışı (§7.1 benzeri) + sorgu akışı |
| bu klasör × `commands/` | ✅ | Komut akışı sonunda `*Command` tipi (henüz 0 — PLANNED) |
| bu klasör × `queries/` | ✅ | Sorgu akışı sonunda `*Query` tipi (henüz 0 — PLANNED) |
| bu klasör × `routing/` | ✅ | Yön işareti `_route_status` matcher'dan doğar |
| bu klasör × `dto/` | ✅ | `getQuery()` sorgu · `fromArray()` komut doğrulama |
| bu klasör × `event-akislari/` | ✅ | Komut sonucu domain event (PLANNED wiring) |
| bu klasör × `caching/` | ✅ | Sorgu cache (§4.4) — invalidation ⚠️ |
| bu klasör × `desktop/`, `web/` | kısmi | BFF katmanı komut/sorgu akışını taşır (§6/7) |
| bu klasör × `server-runtime/` | kısmi | Sunucu çalışma zamanı komut/sorgu kabuğu |
| bu klasör × `local-runtime/` | kısmi | Yerel runtime komut/sorgu kabuğu |
| bu klasör × `application-testing/` | ✅ | Method routing + pipeline testleri (§3.8, §7) |

---

## §6 — Kapsam dışı (bu belge AŞMAZ)

| # | Aşan konu | Giden klasör | Aşma nedeni |
|---|-----------|--------------|-------------|
| 1 | Middleware iç detayı (auth, rate-limit, validation algoritması) | `middleware`/`security` başlıkları | Burada yalnız "her akışta çalışır" kaydı geçerli |
| 2 | Rota matcher algoritması (tablo yapısı, 405 Allow üretimi iç mantığı) | `routing/` | Matcher çıktısı yön işareti olarak okunur, içerisi yazılmaz |
| 3 | Event wiring uygulaması (ADR-086 PLANNED safhası) | `event-akislari/` (kardeş) | Burada yalnız komut→olay kapısı sorusu geçer (§3.3) |
| 4 | Veritabanı BCNF tasarımı | `.ai/.sql/mysql/` + data-engineer | Burada yalnız read/write tablo yönü tablosu vardır (§4.3) |
| 5 | CSS/JS tasarım token'ları | frontend katmanı | Burada yalnız komut/sorgu UI karşılığı (form submit, fetch) yer alır |
| 6 | AI RAG sorguları (`ragQuery`, `shared/src/Contracts/AI/KnowledgeBaseInterface.php:32`) | AI modülü | Bu klasör yalnız HTTP sorgu DTO'larını modeller |
| 7 | BFF katmanı iç yapısı (`shared/src/Api/Bff/` — 5 dosya: BffLayer 83, Desktop 66, Embedded 76, Mobile 101, Spa 64 satır) | `desktop/`, `web/`, `local-runtime/` kardeşleri | Burada BFF yalnız komut/sorgu akışının geçtiği katman olarak anılır |
| 8 | ServiceRegistry sağlık tanımları (`shared/src/Api/Registry/` — 3 dosya: ServiceDefinition 120, ServiceHealth 100, ServiceRegistry 132 satır) | `application-services/` | Burada yalnız "handler hangi servise gider" sorusu sorulur |

### §6.1 Kapsam dışı karar gerekçeleri (neden bu klasör var)

| # | Soru | Cevap | Klasörün varlık nedeni |
|---|------|-------|------------------------|
| 1 | "Komut/sorgu ayrımı nerede?" | HTTP methodu + DTO + yön | Sınıf yoksa akış vardır; akışı yazan bu klasördür |
| 2 | "Neden ayrı klasör, usecase-akislari yeterli değil mi?" | Hayır — senaryo eksenli vs veri yönü eksenli | Bir senaryo hem komut hem sorgu içerir (§7.3); iki eksen kesişir ama aynı değildir |
| 3 | "commands/queries kardeşleri neden yeterli değil?" | Onlar tip envanteri | Tip envanteri "akış nereye gidiyor" sorusunu yanıtlamaz |
| 4 | "JS neden dahil?" | Komut/sorgu üretiminin %100'ü UI'da JS'ten başlar | `gender-select.js:48` (komut), `FetchWrapper.js:52` (sorgu) |

### §6.2 Sınır karar matrisi (hangi yüzey hangi klasöre)

| Yüzey | Komut/sorgu ilgisi | Bu klasörde? | Gerekçe |
|-------|--------------------|--------------|---------|
| `shared/src/Api/Gateway.php` | dispatch + invokeHandler = yön kapısı | ✅ ANA | Komut/sorgu ayrımının doğduğu yer |
| `shared/src/Api/Dto/Request/*.php` | 6 request DTO — clamp + getter | ✅ | Yöne göre rol ayrımı (§3.4) |
| `shared/src/Api/Middleware/*.php` | pipeline — yön bağımsız | ✅ (özet) | İç algoritma kapsam dışı, yalnız "her akışta çalışır" |
| `shared/src/Api/Routing/RouteTable.php` | method-aware eşleşme | ✅ (işaret) | Yön işareti buradan doğar; matcher içi `routing/` |
| `shared/src/PageRouter/*.php` | query string normalizasyon | ✅ (sorgu) | `RequestNormalizer.php:52,94` |
| `shared/src/Cache/*.php` | sorgu cache | ✅ (sorgu, ⚠️ strateji) | ADR-007; invalidation tanımsız |
| `shared/src/Events/*.php` | komut sonucu olay | ✅ (kapı, PLANNED) | Wiring `event-akislari/` |
| `assets/coremusic.net/js/router/*.js` | GET zinciri (23 dosya) | ✅ ANA | JS sorgu omurgası |
| `assets/coremusic.net/js/auth/gender-select.js` | POST komut | ✅ | JS komut örneği |
| `assets/coremusic.net/js/oauth-manager.js` | komut+sorgu çifti | ✅ | §7.3 senaryosu |
| `shared/src/Database/*.php` | PDO katmanı (ADR-002) | ❌ | Data-engineer; burada yalnız SQL şema yönü (§4.3) |
| `shared/src/Security/*.php` | güvenlik yardımcıları | ❌ | Security-engineer; pipeline zaten kapsar |
| `home.coremusic.net/include/Stream/*.php` | stream dağıtımı | ❌ (işaretli) | `MusicStreamHandler::dispatch` farklı dispatch — §9/6 |
| `shared/src/AI/*.php` | RAG sorguları | ❌ | AI modülü ayrı konu |
| `shared/src/Contracts/Api/GatewayInterface.php` | dispatch arayüzü | ✅ (işaret) | `dispatch()` satır 25 — Gateway sözleşmesi |
| `shared/src/Api/Versioning/*.php` | versiyon çözme | ✅ (işaret) | `VersionResolver` (79 satır) komut/sorgu öncesi çalışır |
| `shared/src/PageRouter/PageRouterKernel.php` | kernel yaşam döngüsü | ❌ (işaret) | `router->dispatch` satır 118 — sayfa akışı, API değil |

### §6.3 Yönlendirme kısayolları (bu klasörden kardeşlere)

| İhtiyaç | Giden klasör | Ne alınır |
|---------|--------------|-----------|
| Route matcher detayı | `routing/` | `RouteTable` iç yapısı |
| DTO tip listesi | `dto/` | 6 request + 5 response envanteri |
| Senaryo akışı | `usecase-akislari/` | Login, playback senaryoları |
| Event yaşam döngüsü | `event-akislari/` | Dispatcher + 12 event |
| Servis envanteri | `application-services/` | BFF, Registry, Gateway nesneleri |
| Test senaryoları | `application-testing/` | PHPUnit + Vitest kapsamı |
| Caching stratejisi | `caching/` | ADR-007 adapter'ları (APCu, Memory, PageCache) |
| Masaüstü akışı | `desktop/` | Electron + masaüstü BFF |
| Web akışı | `web/` | Web SPA + SpaBff |
| Workflow/komut | `workflows/` | İş akışı komutları |
| Local runtime | `local-runtime/` | Yerel çalışma zamanı akışları |
| Electron | `electron/` | Masaüstü kabuk akışları |

---

## §7 — Senaryo tabloları (uçtan uca)

### §7.1 Komut senaryosu: Gender seçimi kaydetme

| # | Katman | Adım | Dosya:Satır |
|---|--------|------|-------------|
| 1 | UI | Kullanıcı butona tıklar | `assets/coremusic.net/js/auth/gender-select.js:24` |
| 2 | UI | Submit engellenir, URL kurulur | `gender-select.js:34-46` |
| 3 | UI | `POST /set-gender?redirect_uri=…` | `gender-select.js:48` |
| 4 | Server | Middleware pipeline | `shared/src/Api/Gateway.php:68` |
| 5 | Server | Rota eşleşir (POST) | `shared/src/Api/Gateway.php:62-65` |
| 6 | Server | Handler çağrılır | `shared/src/Api/Gateway.php:155` |
| 7 | DB | Yazım (prepared statement) | `shared/src/Database/` (ADR-002) |
| 8 | Event | Domain event tetiklenir mi? | `shared/src/Events/Domain/GenderSetEvent.php` (76 satır — var, wiring PLANNED) |

### §7.2 Sorgu senaryosu: Ana sayfa şarkı listesi

| # | Katman | Adım | Dosya:Satır |
|---|--------|------|-------------|
| 1 | UI | Router navigasyon | `assets/coremusic.net/js/router/NavigationOrchestrator.js:41` |
| 2 | UI | Fetch wrapper GET | `assets/coremusic.net/js/router/FetchWrapper.js:52` |
| 3 | Server | Query string normalizasyon | `shared/src/PageRouter/RequestNormalizer.php:52` |
| 4 | Server | Route match (GET) | `shared/src/Api/Gateway.php:62-80` |
| 5 | Server | Handler yanıt üretir | `shared/src/Api/Gateway.php:152-159` |
| 6 | UI | DOM patch | `assets/coremusic.net/js/router/DomPatcher.js:1-127` |
| 7 | UI | History pushState | `NavigationOrchestrator.js:83` |

### §7.3 Komut-sorgu çifti: OAuth connect (komut) → connections (sorgu)

| # | Yön | Adım | Dosya:Satır |
|---|-----|------|-------------|
| 1 | Komut | `fetch POST …/connect` | `assets/coremusic.net/js/oauth-manager.js:46` |
| 2 | Sorgu | `fetch GET …/connections` | `oauth-manager.js:144` |
| 3 | Sonuç | Bildirim CustomEvent | `oauth-manager.js:287-290` |

### §7.4 Komut senaryosu: Kayıt (register) — DTO clamp zinciri

| # | Katman | Adım | Dosya:Satır |
|---|--------|------|-------------|
| 1 | UI | Form submit (komut) | ⚠️ VERIFICATION REQUIRED (register JS dosyası okunmadı) |
| 2 | Server | `POST /api/v1/auth/register` route eşleşir | `shared/src/Api/Routing/RouteTable.php` (253 satır) |
| 3 | Server | Validation middleware | `shared/src/Api/Middleware/RequestValidationMiddleware.php:1-175` |
| 4 | DTO | `RegisterRequest::fromArray()` clamp | `shared/src/Api/Dto/Request/RegisterRequest.php:1-94` |
| 5 | Handler | `invokeHandler` çağrısı | `shared/src/Api/Gateway.php:155` |
| 6 | Yanıt | `UserResponse` zarfı | `shared/src/Api/Dto/Response/UserResponse.php:1-116` |
| 7 | Event | `UserRegisteredEvent` (wiring PLANNED) | `shared/src/Events/Domain/UserRegisteredEvent.php:1-96` |

---

## §8 — Terimler (repo-kaynaklı sözlük)

| Terim | Tanım | Repo karşılığı |
|-------|-------|----------------|
| **Komut (command)** | Durumu/değiştiren istek | HTTP POST/PUT/PATCH/DELETE — `shared/src/Api/Routing/RouteTable.php` method eşleşmesi |
| **Sorgu (query)** | Yalnızca okuyan istek | HTTP GET/HEAD — `ApiRequest::getQueryParams()` (`shared/src/Api/ApiRequest.php:76`) |
| **Handler** | Komut/sorgu işleyen callable | `Gateway::invokeHandler()` (`shared/src/Api/Gateway.php:152-159`) |
| **Dispatcher** | İsteği doğru handler'a yönler | `Gateway::dispatch()` (`shared/src/Api/Gateway.php:50`) |
| **DTO** | Doğrulanmış veri taşıyıcı | `shared/src/Api/Dto/Request/*.php` (6 request DTO) |
| **Clamp** | Sayısal sınır kırpma | `min(100, max(1, …))` (`MusicSearchRequest.php:77`) |
| **Placeholder yanıt** | Handler henüz bağlı değil | `'Handler not implemented yet'` (`Gateway.php:165`) |
| **Cache geçersizleştirme** | Komut sonrası cache temizliği | ⚠️ tanımsız (§4.4) |
| **Method-aware eşleşme** | Rota + HTTP method birlikte eşleşir | `RouteTable` (253 satır) + `Gateway::matchRoute` (`Gateway.php:109-114`) |
| **Allow başlığı** | 405'te sunulan methodları listeler | `Gateway.php:127-137` |
| **Pipeline (API)** | Middleware sırası | `ApiMiddlewarePipeline.php` (46 satır) |
| **Abort sinyali** | Fetch iptali | `ContentFetcher.js:25` |
| **Guard** | Navigasyon denetimi | `GuardPipeline.js` (54 satır) |
| **BFF** | Backend-for-Frontend katmanı | `shared/src/Api/Bff/` (5 dosya — §6/7) |
| **TraceId** | Hata izleme anahtarı | `Gateway.php:88` (`UuidV7::generate()`); logger damgası `PageRouterKernel.php:91` |
| **Wiring** | Event'i production akışına bağlama | ADR-086 §5.2 (PLANNED) |

---

## §9 — Açık sorular

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Repo'da `*Command`/`*Query`/`*Handler` sınıfı 0 — ayrım HTTP methodu ile mi kalıcı, sınıf katmanı mı eklenecek? | Mimari yön | ⚠️ VERIFICATION REQUIRED |
| 2 | `$handlerResolver` (`shared/src/Api/Gateway.php:33,155`) komut/sorgu ayrımını taşıyacak mı yoksa yalnız controller çözümleyici mi? | Gateway genişlemesi | ⚠️ VERIFICATION REQUIRED |
| 3 | Komut sonrası event tetikleme (ADR-086 wiring) hangi komutla başlayacak? ADR §5.2: UserLoggedIn/Out → audit, MusicPlayed → istatistik, PasswordResetRequested → mail. | Wiring faz planı | PLANNED (ADR-086 §5.2) |
| 4 | Sorgu cache'i (APCu `shared/src/Cache/`) komut yazımından nasıl izole edilecek (cache invalidation stratejisi)? | Tutarlılık | ⚠️ VERIFICATION REQUIRED |
| 5 | JS router `GET` sorguları (`FetchWrapper.js:52`) PHP sorgu cache'i ile aynı anahtar uzayını kullanacak mı? | Önbellek tutarlılığı | ⚠️ VERIFICATION REQUIRED |
| 6 | `MusicStreamHandler::dispatch()` (`home.coremusic.net/include/Stream/MusicStreamHandler.php:55`) komut/sorgu modeline nasıl oturur — stream bir komut mu sorgu mu? | Terminoloji | ⚠️ VERIFICATION REQUIRED |
| 7 | `RouteTable` method eşleşmesinde `HEAD` GET'e mi düşer yoksa ayrı mı tanımlı? (253 satır dosya içi mantık okunmadı) | Sorgu methodu kapsamı | ⚠️ VERIFICATION REQUIRED |
| 8 | BFF katmanı (`shared/src/Api/Bff/`, 5 dosya) komut/sorgu akışını cihaz bazlı ayrıyor mu (Desktop/Mobile/Embedded/Spa)? | Cihaz bazlı yön | ⚠️ VERIFICATION REQUIRED |
| 9 | `search_logs` (`coremusic_logs.sql:398`) sorgu meta-verisi olarak kullanılıyor mu — canlı yazım var mı? | SQL sorgu kaydı | ⚠️ VERIFICATION REQUIRED |

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
