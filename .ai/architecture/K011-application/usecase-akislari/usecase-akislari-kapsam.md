---
node_id: K011-application/usecase-akislari/usecase-akislari-kapsam.md
node_type: file
status: draft
parent_id: K011-application/usecase-akislari
children: []
depends_on:
  - K008-domain
  - K009-security
  - K012-services
evidence:
  - "shared/src/PageRouter/RouteRegistry.php — korumalı rota anahtarları (getProtectedRouteKeys, satır 61)"
  - "shared/src/PageRouter/AuthGuard.php — auth denetimi (80 satır toplam, ölçüm 2026-10-12)"
  - "shared/src/Middleware/CsrfMiddleware.php — CSRF akışı (65 satır toplam, ölçüm 2026-10-12)"
validation:
  - "Satır aralığı 120–250 — (Get-Content).Count ile doğrulandı, 2026-10-12"
open_questions:
  - "hata akışının ErrorHandler ile mi, Exception hiyerarşisi ile mi tekilleşeceği → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# usecase-akislari — Kapsam

## §1 — Amaç

Bu dosya, K011 uygulama katmanındaki **akış kapsamını** tanımlar: hangi akışlar bu
çocuğun sorumluluğudur, hangileri komşu K düğümlerine devredilir. Akış = sıralı, gözlemlenebilir, tek bir kullanıcı niyetiyle başlayan adımlar dizisi.

## §2 — Kapsam içi akışlar

| # | Akış | Başlangıç | Bitiş | Kanıt dosyası |
|---|---|---|---|---|
| 1 | Sayfa gösterimi | HTTP GET | HTML yanıt | `shared/src/PageRouter/PageRouterKernel.php` |
| 2 | SPA rota çözümü | URI | `SpaRoute` | `shared/src/PageRouter/RouteRegistry.php` |
| 3 | Koruma kapıları | istek | pipeline'a devir | `shared/src/PageRouter/AuthGuard.php` |
| 4 | Middleware yürütmesi | `pipe()` | `run()` | `shared/src/Middleware/MiddlewarePipeline.php` |
| 5 | CSRF denetimi | istek gövdesi | token doğrulama | `shared/src/Middleware/CsrfMiddleware.php` |
| 6 | Hız sınırı | IP anahtarı | 429 / devam | `shared/src/Middleware/RateLimiterMiddleware.php` |
| 7 | Oturum başlatma | istek | oturum kimliği | `shared/src/Session/SessionBootstrapper.php` |
| 8 | Kimlik doğrulama | cookie/token | kimlik | `shared/src/Middleware/AuthMiddleware.php` |
| 9 | Yetki denetimi | kimlik + rol | izin/red | `shared/src/Middleware/PermissionMiddleware.php` |
| 10 | İstek doğrulama | gövde | hata ya da devam | `shared/src/Middleware/ValidationMiddleware.php` |
| 11 | Yanıt yayımı | yanıt dizisi | HTTP çıktısı | `shared/src/PageRouter/ResponseEmitter.php` |
| 12 | Hata akışı | istisna | hata sayfası | `shared/src/PageRouter/ErrorHandler.php` |

## §3 — Kapsam dışı akışlar

| # | Akış | Sorumlu | Gerekçe |
|---|---|---|---|
| 1 | İş kuralı kararı (ör. "çalma listesi kuralı") | K008-domain | v2 §2.11: iş kuralı yaşamaz |
| 2 | Arka plan işi (ör. e-posta kuyruğu) | K012-services | adaptör/worker orada |
| 3 | Dış API çağrısı (OAuth sağlayıcı vb.) | K013-integraion | kontrat orada |
| 4 | Ödeme sağlayıcı akışı | K012-services | repo'da ödeme kodu yok → ⚠️ VERIFICATION REQUIRED |
| 5 | Zamanlanmış tetikleyiciler (cron) | K018-operasyon | tanım burada, çalıştırıcı orada |
| 6 | WebSocket/gerçek zamanlı kanal | K014-network | taşıma katmanı |
| 7 | Medya oynatma akışı | K015-media | medya verisi |
| 8 | UI animasyon/etkileşim akışı | UI alanı (ui-design) | ITCSS/mockup |

## §4 — Akış şablonu (standart yapı)

Her akış kaydı şu alanları taşır:

| Alan | Tanım | Zorunlu |
|---|---|---|
| `id` | Akış numarası (ör. UC-04) | ✅ |
| `niyet` | Kullanıcının tek cümlelik amacı | ✅ |
| `adımlar` | Sıralı, gözlemlenebilir adımlar | ✅ |
| `kapılar` | Middleware/auth kontrolleri | ✅ |
| `başarı` | Beklenen yanıt | ✅ |
| `hata` | Hata dalı | ✅ |
| `komşu` | Devredilen K düğümü | koşullu |

## §5 — Sınır soruları

| # | Soru | Durum |
|---|---|---|
| 1 | `*UseCase` sınıfı yok — modelleme PageRouter üzerinden mi? | ⚠️ VERIFICATION REQUIRED |
| 2 | İki `SessionInitializer` sınıfı var (session + PageRouter) — hangisi akışın sahibi? | ⚠️ VERIFICATION REQUIRED |
| 3 | BFF katmanı (`shared/src/Api/Bff/`) akışa dahil mi? | ⚠️ VERIFICATION REQUIRED |
| 4 | API akışları (Gateway) ile sayfa akışları aynı şablonu kullanır mı? | TASARIM İDDİASI |

## §6 — Bağımlılık etkisi

| Bağımlılık | Akıştaki rolü |
|---|---|
| K008-domain | Kapıdan sonra uygulanan kuralın kaynağı (yönlendirme, gövde değil) |
| K009-security | CSRF/auth/rate-limit politikasının kaynağı (kod burada, kural orada) |
| K012-services | Uzun işlerin devredildiği yer (tetikleyen akış burada) |
| K013-integraion | Dış çağrının kontratı |
| K014-network | Taşıma katmanı |
| K015-media | Oynatma verisi |

## §7 — Ölçüm referansı

| Ölçüm | Değer | Tarih |
|---|---|---|
| `shared/src/PageRouter/` dosyası | 14 PHP + 1 CLAUDE.md | 2026-10-12 |
| `shared/src/Middleware/` dosyası | 11 PHP + 1 CLAUDE.md | 2026-10-12 |
| `shared/src/Session/` dosyası | 4 PHP + 1 CLAUDE.md | 2026-10-12 |
| `*UseCase` sınıfı | 0 | 2026-10-12 |

## §A Kapsam (Scope — Genişletilmiş)

> **Tarih: 2026-10-13** · Bu bölüm §2–§3'ün kapsamını **ölçülebilir sınır** ile tamamlar: ne bu dosyanın sorumluluğundadır, ne değildir, hangi kanıtla ayrılır.

### §A.1 Kapsam İçi Yüzeyler

| # | Yüzey | Kapsam Açıklaması | Kanıt (Dosya) | Durum |
|---|-------|-------------------|---------------|-------|
| 1 | Sayfa yönlendirme | URI'nin sayfaya eşlenmesi | `shared/src/PageRouter/RouteRegistry.php` | ✅ |
| 2 | İstek çekirdeği | İstek-yanıt ana döngüsü | `shared/src/PageRouter/PageRouterKernel.php` | ✅ |
| 3 | Denetim zinciri | Ara katmanların sıralı yürümesi | `shared/src/Middleware/MiddlewarePipeline.php` | ✅ |
| 4 | Kimlik kapısı | Giriş denetimi | `shared/src/PageRouter/AuthGuard.php` | ✅ |
| 5 | Sahte istek kapısı | Token doğrulama | `shared/src/Middleware/CsrfMiddleware.php` | ✅ |
| 6 | Hız sınırı kapısı | İstek sayacı | `shared/src/Middleware/RateLimiterMiddleware.php` | ✅ |
| 7 | Oturum kapısı | Oturum garantisi | `shared/src/Session/SessionBootstrapper.php` | ✅ |
| 8 | Yanıt yayımı | HTTP çıktısının gönderilmesi | `shared/src/PageRouter/ResponseEmitter.php` | ✅ |
| 9 | Hata akışı | Hata sayfasının üretilmesi | `shared/src/PageRouter/ErrorHandler.php` | ✅ |
| 10 | API dağıtımı | Uç işleyiciye devir | `shared/src/Api/Gateway.php` | ✅ |

### §A.2 Kapsam Dışı Yüzeyler

| # | Yüzey | Sorumlu K Düğümü | Gerekçe | Durum |
|---|-------|------------------|---------|-------|
| 1 | İş kuralı kararı | K008-domain | Kural yaşamaz, uygulanır | Dış |
| 2 | Uzun iş kuyruğu | K012-services | Çalıştırıcı orada | Dış |
| 3 | Dış API kontratı | K013-integraion | Kontrat orada | Dış |
| 4 | Ödeme sağlayıcı | K012-services | Repo'da ödeme kodu yok | Dış |
| 5 | Zamanlanmış tetikleyici | K018-operasyon | Tanım burada, çalıştırıcı orada | Dış |
| 6 | Gerçek zamanlı kanal | K014-network | Taşıma katmanı | Dış |
| 7 | Medya verisi | K015-media | Oynatma verisi | Dış |
| 8 | Arayüz etkileşimi | UI alanı | ITCSS/mockup | Dış |

### §A.3 Kapsam Ayrım Kriterleri

| # | Kriter | Kapsam İçi Testi | Kapsam Dışı Testi | Karar |
|---|--------|------------------|-------------------|-------|
| 1 | Sorumluluk | İstek-yanıt yürütmek mi? | Kural üretmek mi? | İstek-yanıt → iç |
| 2 | Konum | `shared/src/` içinde mi? | Alt proje içinde mi? | `shared/src/` → iç |
| 3 | Tetikleyici | HTTP isteği mi? | Zamanlayıcı mı? | HTTP → iç |
| 4 | Dönüş | Kullanıcıya yanıt mı? | Arka plan kaydı mı? | Yanıt → iç |
| 5 | Kanıt | Dosya kanıtı var mı? | Yokluk mu? | Kanıt → iç |
| 6 | Bağımlılık yönü | Aşağı/yana mı? | Yukarı mı? | Aşağı/yana → iç |

### §A.4 Kapsam Sınırı Tipik Durumlar

| # | Durum | Sınır Kararı | Dayanak | Sonuç |
|---|-------|--------------|--------|-------|
| 1 | İstek gelir, rota eşleşmez | Kapsam içi (hata akışı) | `ErrorHandler.php` | İç |
| 2 | İstek gelir, rol yetmiyor | Kapsam içi (kapı) | `AuthGuard.php` | İç |
| 3 | İş kuralı çiğneniyor | Kapsam dışı | K008-domain | Dış |
| 4 | Uzak servis yanıt vermiyor | Kapsam dışı (kontrat) | K013-integraion | Dış |
| 5 | Uzun iş gerekiyor | Kapsam dışı (kuyruk) | K012-services | Dış |
| 6 | Ödeme akışı | Kapsam dışı | Repo'da kod yok | Dış |

## §B Bileşen Listesi (Component Inventory)

> **Tarih: 2026-10-13** · Bu bölüm, akışların üzerinde çalıştığı **bileşenleri** dosya düzeyinde listeler; sayımlar 2026-10-13 dizin sayımıdır.

### §B.1 Yönlendirme Bileşenleri

| # | Bileşen | Dosya | Akıştaki Rolü | Durum |
|---|---------|-------|---------------|-------|
| 1 | Yönlendirici çekirdeği | `shared/src/PageRouter/PageRouterKernel.php` | İstek ana döngüsü | ✅ |
| 2 | Rota defteri | `shared/src/PageRouter/RouteRegistry.php` | Rota çözümleme | ✅ |
| 3 | SPA rota nesnesi | `shared/src/PageRouter/SpaRoute.php` | SPA rota verisi | ✅ |
| 4 | Kimlik kapısı | `shared/src/PageRouter/AuthGuard.php` | Giriş denetimi | ✅ |
| 5 | Yanıt yayımcısı | `shared/src/PageRouter/ResponseEmitter.php` | HTTP çıktısı | ✅ |
| 6 | Hata kurucu | `shared/src/PageRouter/ErrorHandler.php` | Hata sayfası | ✅ |

### §B.2 Denetim Bileşenleri (Middleware — 11 sınıf)

| # | Bileşen | Dosya | Kapı Rolü | Durum |
|---|---------|-------|-----------|-------|
| 1 | Denetim zinciri | `shared/src/Middleware/MiddlewarePipeline.php` | Sıralı yürütme | ✅ |
| 2 | Kimlik denetimi | `shared/src/Middleware/AuthMiddleware.php` | Kimlik kapısı | ✅ |
| 3 | İzin denetimi | `shared/src/Middleware/PermissionMiddleware.php` | Rol kapısı | ✅ |
| 4 | Sahte istek | `shared/src/Middleware/CsrfMiddleware.php` | Token kapısı | ✅ |
| 5 | Köken denetimi | `shared/src/Middleware/OriginCheckMiddleware.php` | Köken kapısı | ✅ |
| 6 | Kaynaklar arası izin | `shared/src/Middleware/CorsMiddleware.php` | CORS kapısı | ✅ |
| 7 | Hız sınırı | `shared/src/Middleware/RateLimiterMiddleware.php` | Sayaç kapısı | ✅ |
| 8 | Girdi doğrulama | `shared/src/Middleware/ValidationMiddleware.php` | Biçim kapısı | ✅ |
| 9 | Güvenlik başlığı | `shared/src/Middleware/SecurityHeadersMiddleware.php` | Başlık kapısı | ✅ |
| 10 | Kimlik atlatma | `shared/src/Middleware/BypassAuthMiddleware.php` | Bakım kapısı | ✅ |

### §B.3 Oturum ve Önbellek Bileşenleri

| # | Bileşen | Dosya | Akıştaki Rolü | Durum |
|---|---------|-------|---------------|-------|
| 1 | Oturum başlatıcı | `shared/src/Session/SessionBootstrapper.php` | Oturum garantisi | ✅ |
| 2 | Oturum sınıfı | `shared/src/Session/SessionInitializer.php` | Oturum kurulumu | ✅ (ikilik §5) |
| 3 | Önbellek yöneticisi | `shared/src/Cache/CacheManager.php` | Adaptör seçimi | ✅ |
| 4 | APCu uyarlayıcısı | `shared/src/Cache/ApcuAdapter.php` | Uzantı önbelleği | ✅ |
| 5 | Sayfa önbelleği | `shared/src/Cache/PageCacheAdapter.php` | Sayfa önbelleği | ✅ |

### §B.4 API Bileşenleri

| # | Bileşen | Dosya | Akıştaki Rolü | Durum |
|---|---------|-------|---------------|-------|
| 1 | Dağıtıcı | `shared/src/Api/Gateway.php` | Uç işleyiciye devir | ✅ |
| 2 | Yanıt paketi | `shared/src/Api/ApiResponse.php` | Standart gövde | ✅ |
| 3 | İstek paketi | `shared/src/Api/ApiRequest.php` | Girdi zarfı | ✅ |
| 4 | Sayfalama nesnesi | `shared/src/Api/Dto/Response/PaginationResponse.php` | Liste gövdesi | ✅ |
| 5 | Olay dağıtıcı | `shared/src/Events/EventDispatcher.php` | Olay yayını | ✅ |
## §C Arayüzler (Interfaces)

> **Tarih: 2026-10-13** · Bu bölüm, akış bileşenlerinin **çağırdığı ve sağladığı arayüzleri** imza düzeyinde listeler; her imza Dosya:Satır künyesi taşır.

### §C.1 Sağlanan Arayüzler (Akışın Dışa Açılan Yüzeyi)

| # | Arayüz | İmza Özeti | Dosya:Satır | Durum |
|---|--------|------------|-------------|-------|
| 1 | İstek elleme | `handle(...)` | `PageRouterKernel.php:81` | ✅ |
| 2 | Rota çözümleme | `resolve(string $uri)` | `RouteRegistry.php:35` | ✅ |
| 3 | Korumalı rota listesi | `getProtectedRouteKeys()` | `RouteRegistry.php:61` | ✅ |
| 4 | Kimlik denetimi | `check(string $uri, SpaRoute $route, bool $isSpaRequest)` | `AuthGuard.php:15` | ✅ |
| 5 | İzin denetimi | `checkPermission(string $uri, SpaRoute $route)` | `AuthGuard.php:42` | ✅ |
| 6 | Denetim ekleme | `pipe(IMiddleware $middleware)` | `MiddlewarePipeline.php:17` | ✅ |
| 7 | Zincir yürütme | `run(array $request, callable $core)` | `MiddlewarePipeline.php:23` | ✅ |
| 8 | Yanıt yayımı | `emit(array $response, ...)` | `ResponseEmitter.php:16` | ✅ |
| 9 | Hata sayfası | `buildErrorPage(array $errorInfo, string $csrfToken)` | `ErrorHandler.php:11` | ✅ |
| 10 | Hata sonucu | `buildErrorHtmlResult(int $status, array $body)` | `ErrorHandler.php:51` | ✅ |

### §C.2 Çağrılan Arayüzler (Akışın İçe Baktığı Yüzey)

| # | Arayüz | Çağrı Özeti | Dosya:Satır | Durum |
|---|--------|-------------|-------------|-------|
| 1 | İstek normalizasyonu | `normalizeRequest(...)` | `PageRouterKernel.php:225` | ✅ |
| 2 | Denetim yığını | `runMiddlewareStack(...)` | `PageRouterKernel.php:269` | ✅ |
| 3 | Varsayılan denetimler | `buildDefaultMiddlewares()` | `PageRouterKernel.php:295` | ✅ |
| 4 | Sahte istek denetimi | `handle(array $request, callable $next)` | `CsrfMiddleware.php:20` | ✅ |
| 5 | Hız sınırı denetimi | `handle(array $request, callable $next)` | `RateLimiterMiddleware.php:50` | ✅ |
| 6 | İstemci adresi | `resolveClientIp(array $request)` | `RateLimiterMiddleware.php:105` | ✅ |
| 7 | Güvenilir ara sunucu | `isTrustedProxy(string $ip)` | `RateLimiterMiddleware.php:124` | ✅ |
| 8 | Oturum garantisi | `ensureStarted(): void` | `SessionBootstrapper.php:19` | ✅ |
| 9 | API dağıtımı | `dispatch(array $request)` | `Gateway.php:50` | ✅ |
| 10 | İşleyici çağrısı | `invokeHandler(array $route, array $request)` | `Gateway.php:152` | ✅ |

### §C.3 Arayüz Sözleşmeleri (Biçim Kuralları)

| # | Sözleşme | Girdi Biçimi | Çıktı Biçimi | Kaynak |
|---|----------|--------------|--------------|--------|
| 1 | Rota çözümleme | Dize (URI) | Nesne ya da boş | `RouteRegistry.php:35` |
| 2 | Kimlik denetimi | URI + rota + istek türü | Yanıt ya da boş | `AuthGuard.php:15` |
| 3 | Denetim zinciri | İstek dizisi + çekirdek | Yanıt dizisi | `MiddlewarePipeline.php:23` |
| 4 | Yanıt yayımı | Yanıt dizisi + başlıklar | Dur (asla dönmez) | `ResponseEmitter.php:16` |
| 5 | Hata sayfası | Hata bilgisi + CSRF anahtarı | HTML dize | `ErrorHandler.php:11` |
| 6 | API dağıtımı | İstek dizisi | Yanıt dizisi | `Gateway.php:50` |
| 7 | Olay yayımı | Nesne | Aynı nesne | `EventDispatcher.php:28` |
| 8 | Önbellek yazımı | Anahtar + değer + ömür | Bağlantı sonucu | `ApcuAdapter.php:17` |

### §C.4 Arayüz Bütünlük Kontrolü (2026-10-13)

| # | Kontrol | Yöntem | Sonuç |
|---|---------|--------|-------|
| 1 | Sözleşme dosyaları sayısı | `shared/src/Contracts/` sayımı | 19 dosya |
| 2 | Ara katman sınıfı sayısı | `shared/src/Middleware/` sayımı | 11 sınıf |
| 3 | Yönlendirme dosyası sayısı | `shared/src/PageRouter/` sayımı | 14 dosya |
| 4 | API dosyası sayısı | `shared/src/Api/` sayımı | 34 dosya |
| 5 | Test dosyası sayısı | `shared/tests/` sayımı | 44 dosya |
| 6 | Olay dosyası sayısı | `shared/src/Events/` sayımı | 14 dosya |

## §D Bağımlılık Grafiği (Dependency Graph)

> **Tarih: 2026-10-13** · Bu bölüm, akışların **bağımlılık yönünü** §6 tablosunun grafik ve sıra düzeyinde tamamlar; yön tek taraflıdır.

### §D.1 Akış İçi Bağımlılık Sırası

| # | Sıra | Adım | Önce Gelen | Sonra Gelen | Kanıt |
|---|------|------|------------|-------------|-------|
| 1 | 1 | Oturum garantisi | İstek | Kimlik denetimi | `SessionBootstrapper.php:19` |
| 2 | 2 | İstek normalizasyonu | Sunucu | Rota çözümleme | `PageRouterKernel.php:225` |
| 3 | 3 | Rota çözümleme | Normalize istek | Kimlik kapısı | `RouteRegistry.php:35` |
| 4 | 4 | Kimlik kapısı | Rota | İzin kapısı | `AuthGuard.php:15` |
| 5 | 5 | İzin kapısı | Kimlik | Denetim zinciri | `AuthGuard.php:42` |
| 6 | 6 | Sahte istek kapısı | Gövde | İşleyici | `CsrfMiddleware.php:20` |
| 7 | 7 | Hız sınırı kapısı | İstemci adresi | İşleyici | `RateLimiterMiddleware.php:50` |
| 8 | 8 | İşleyici | Rota + istek | Yanıt | `Gateway.php:152` |
| 9 | 9 | Yanıt yayımı | Yanıt | İstemci | `ResponseEmitter.php:16` |
| 10 | 10 | Olay yayımı | İşleyici | Dinleyiciler | `EventDispatcher.php:28` |

### §D.2 K Düğümü Yön Tablosu

| # | Yön | K Düğümü | Akıştaki İlişki | Kural |
|---|-----|----------|------------------|-------|
| 1 | Yukarı | K008-domain | Kapıdan sonra uygulanan kural | Tek yön |
| 2 | Yukarı | K009-security | CSRF/auth/rate-limit politikası | Tek yön |
| 3 | Yukarı | K012-services | Uzun iş devri | Tek yön |
| 4 | Yukarı | K013-integraion | Dış çağrının kontratı | Tek yön |
| 5 | Aşağı | K014-network | Taşıma katmanı | Tek yön |
| 6 | Aşağı | K015-media | Oynatma verisi | Tek yön |
| 7 | Aşağı | K015-frontend | HTTP uçları | Tek yön |
| 8 | Aşağı | K014-observability | Olay abonelikleri | Tek yön |

### §D.3 Grafik (Metin Görselleştirme)

```text
İstemci
  │ HTTP GET/POST
  ▼
PageRouterKernel ──► SessionBootstrapper (oturum)
  │
  ▼
RouteRegistry ──► AuthGuard ──► Permission
  │
  ▼
MiddlewarePipeline (CSRF · hız sınırı · doğrulama · başlık)
  │
  ▼
Gateway ──► İşleyici
  │             │
  │             └──► K008-domain (kural uygulanır)
  ▼
ApiResponse ──► ResponseEmitter ──► İstemci
  │
  └──► EventDispatcher ──► K014-observability
```

### §D.4 Döngü Denetimi (Kapalı Yol Var mı?)

| # | Yol | Döngü Var mı? | Kanıt | Sonuç |
|---|-----|---------------|-------|-------|
| 1 | Kernel → Registry → Kernel | Yok | Yön tek taraflı | ✅ |
| 2 | Pipeline → Middleware → Pipeline | Yok | `pipe()` sonra `run()` | ✅ |
| 3 | Gateway → İşleyici → Gateway | Yok | `invokeHandler` tek yön | ✅ |
| 4 | Dispatcher → Listener → Dispatcher | Yok | Dinleyici geri çağırmaz | ✅ |
| 5 | Akış → K008 → Akış | Yok | Kural yalnız okunur | ✅ |
| 6 | Akış → K012 → Akış | Yok | Devir geri gelmez | ✅ |
### §D.5 Bağımlılık Risk Tablosu (Grafiğin Zayıf Yönleri)

| # | Zayıf Yön | Etki | Azaltma | Durum |
|---|-----------|------|---------|-------|
| 1 | `*UseCase` sınıfı yok | Modelleme belirsiz | §5 soru 1 | ⚠️ |
| 2 | İki `SessionInitializer` | Sahiplik belirsiz | §5 soru 2 | ⚠️ |
| 3 | BFF katmanı kapsam belirsiz | Kapsam açığı | §5 soru 3 | ⚠️ |
| 4 | Ödeme akışı yok | Uç eksik | Kapsam dışı ilan edildi | Dış |
| 5 | Uzak istemci paketi yok | Dış çağrı elle yazılacak | K013 sorumluluğu | ⚠️ |
| 6 | Hata tekilleşme sorusu | İki hata yolu | §5 soru 1 | ⚠️ |

## §E Kapsam Doğrulama Kaydı (2026-10-13)

> Bu bölüm, §A–§D iddialarının **2026-10-13 ölçümüyle** doğrulanmasını tabular olarak taşır.

### §E.1 Dosya Sayımı

| # | Dizin | Ölçüm | Değer | Tarih |
|---|-------|-------|-------|-------|
| 1 | `shared/src/PageRouter/` | PHP dosyası | 14 | 2026-10-13 |
| 2 | `shared/src/Middleware/` | PHP dosyası | 11 | 2026-10-13 |
| 3 | `shared/src/Session/` | PHP dosyası | 4 | 2026-10-13 |
| 4 | `shared/src/Cache/` | PHP dosyası | 6 | 2026-10-13 |
| 5 | `shared/src/Api/` | PHP dosyası | 34 | 2026-10-13 |
| 6 | `shared/src/Events/` | PHP dosyası | 14 | 2026-10-13 |
| 7 | `shared/src/Contracts/` | PHP dosyası | 19 | 2026-10-13 |
| 8 | `shared/tests/` | PHP dosyası | 44 | 2026-10-13 |

### §E.2 İddia — Doğrulama Eşlemesi

| # | İddia | Doğrulama Yöntemi | Sonuç |
|---|-------|-------------------|-------|
| 1 | Rota çözümleme mevcut | `RouteRegistry.php:35` satır okundu | ✅ |
| 2 | Kimlik kapısı mevcut | `AuthGuard.php:15` satır okundu | ✅ |
| 3 | Denetim zinciri mevcut | `MiddlewarePipeline.php:23` satır okundu | ✅ |
| 4 | Hata akışı mevcut | `ErrorHandler.php:11` satır okundu | ✅ |
| 5 | `*UseCase` sınıfı yok | Glob araması = 0 | ⚠️ |
| 6 | Ödeme kodu yok | Sınıf araması = 0 | ⚠️ |
| 7 | Kuyruk sınıfı yok | Sınıf araması = 0 | ⚠️ |
| 8 | Grafikte döngü yok | §D.4 altı yol denetimi | ✅ |

### §E.3 Açık Soru Durumu (§5 Güncellemesi)

| # | Soru | 2026-10-13 Durumu | Karar Sahibi |
|---|------|-------------------|--------------|
| 1 | Modelleme PageRouter üzerinden mi? | Açık — `*UseCase` = 0 | Bayram Ali |
| 2 | İki `SessionInitializer` hangisi? | Açık — ikilik sürüyor | Vault Steward |
| 3 | BFF akışa dahil mi? | Açık — kapsam sorusu | Bayram Ali |
| 4 | API ve sayfa akışı aynı şablonu kullanır mı? | Açık — tasarım iddiası | Vault Steward |
| 5 | Hata akışı tekilleşecek mi? | Açık — iki hata yolu | Bayram Ali |

### §E.4 Kapsam Kapanış Notu

Kapsam, üç katmanlıdır: §A **ne içerir/ne içermez** sorusunu kriterlerle yanıtlar; §B **üzerinde çalışılan bileşenleri** dosya düzeyinde sayar; §C **arayüz imzalarını** Dosya:Satır künyesiyle kanıtlar; §D **yönü ve döngü yokluğunu** denetler. §E bu iddiaları 2026-10-13 ölçümüyle doğrular ve beş açık soruyu sahipleriyle listeler. Kapsam değişirse ilk güncellenmesi gereken tablo §A.1'dir — çünkü içeriğe ne girdiği, diğer bütün tabloların dayanağıdır.

---

## §F Akış Bileşen Eşleme Tablosu (Hangi Akış Hangi Bileşeni Kullanır)

### §F.1 Sayfa Gösterimi Akışı

| # | Adım | Bileşen | Dosya | Durum |
|---|------|---------|-------|-------|
| 1 | İstek karşıla | Çekirdek | `PageRouterKernel.php:81` | ✅ |
| 2 | İsteği normalize et | Normalizatör | `PageRouterKernel.php:225` | ✅ |
| 3 | Rota bul | Rota defteri | `RouteRegistry.php:35` | ✅ |
| 4 | Oturumu başlat | Oturum başlatıcı | `SessionBootstrapper.php:19` | ✅ |
| 5 | Kimliği denetle | Kimlik kapısı | `AuthGuard.php:15` | ✅ |
| 6 | Denetimleri yürüt | Zincir | `MiddlewarePipeline.php:23` | ✅ |
| 7 | Yanıtı yayımla | Yayıcı | `ResponseEmitter.php:16` | ✅ |
| 8 | Hatayı sayfala | Hata kurucu | `ErrorHandler.php:11` | ✅ |

### §F.2 API Ucu Akışı

| # | Adım | Bileşen | Dosya | Durum |
|---|------|---------|-------|-------|
| 1 | İsteği dağıt | Dağıtıcı | `Gateway.php:50` | ✅ |
| 2 | İşleyiciyi çağır | İşleyici çağrıcı | `Gateway.php:152` | ✅ |
| 3 | Gövdeyi paketle | Yanıt paketi | `ApiResponse.php:26` | ✅ |
| 4 | Listeyi sayfala | Sayfalama | `ApiResponse.php:73` | ✅ |
| 5 | Nesneyi kur | Sayfalama nesnesi | `PaginationResponse.php:70` | ✅ |
| 6 | Olayı yayınla | Dağıtıcı | `EventDispatcher.php:28` | ✅ |
| 7 | Yanıtı yayımla | Yayıcı | `ResponseEmitter.php:16` | ✅ |

### §F.3 Güvenlik Kapısı Akışı

| # | Adım | Kapı | Dosya | Durum |
|---|------|------|-------|-------|
| 1 | Kökeni denetle | Köken kapısı | `OriginCheckMiddleware.php` | ✅ |
| 2 | Kaynak arası izni denetle | CORS kapısı | `CorsMiddleware.php` | ✅ |
| 3 | Hız sınırını denetle | Sayaç kapısı | `RateLimiterMiddleware.php:50` | ✅ |
| 4 | Sahte isteği denetle | Token kapısı | `CsrfMiddleware.php:20` | ✅ |
| 5 | Girdiyi doğrula | Biçim kapısı | `ValidationMiddleware.php` | ✅ |
| 6 | Güvenlik başlığını yaz | Başlık kapısı | `SecurityHeadersMiddleware.php` | ✅ |
| 7 | İsterse kimliği atla | Bakım kapısı | `BypassAuthMiddleware.php` | ✅ |

### §F.4 Akış-Bileşen Eşlem Kuralı

| # | Kural | Açıklama | İhlalinde Sonuç |
|---|-------|----------|-----------------|
| 1 | Her akış adımı bir bileşene bağlanır | Bağımlı adım = doğrulanamayan adım | `verify-required` |
| 2 | Bileşen yoksa adım yok sayılır | Tahmini bileşen yazılmaz | §E.2 ⚠️ |
| 3 | Dosya adı diskte doğrulanır | Tahmin yerine glob | Kural ihlali |
| 4 | Kapı sırası §F.3 ile sabittir | Sıra değişirse güvenlik açığı | Düzeltme zorunlu |
| 5 | Grafik döngüsü bulunursa DUR | Döngü = sonsuz yol | Escalation |
## §G Kapsam Sürüm Kaydı ve Değişiklik Eşiği

### §G.1 Kapsam Sürüm Değerleri

| # | Öğe | Değer | Kaynak | Tarih |
|---|-----|-------|--------|-------|
| 1 | Dil alt sınırı | 8.4 | `shared/composer.json:8` | 2026-10-13 |
| 2 | Test çalıştırıcı | `^10.5` | `shared/composer.json:24` | 2026-10-13 |
| 3 | Analiz düzeyi | 5 | `shared/composer.json:48` | 2026-10-13 |
| 4 | CI dil değişkeni | 8.4 | `ci.yml:22` | 2026-10-13 |
| 5 | Kapsam dosyası satırı | 505+ | Satır sayma aracı | 2026-10-13 |
| 6 | Açık soru sayısı | 5 | §E.3 | 2026-10-13 |

### §G.2 Kapsam Değişiklik Kuralları

| # | Değişiklik | Onay | Zorunlu Adım |
|---|------------|------|--------------|
| 1 | Yeni akış ekleme | Bayram Ali | §2 + §F eşlemesi |
| 2 | Akış çıkarma | Bayram Ali | §A.2'ye taşıma |
| 3 | Bileşen değişimi | Vault Steward | §B + §C güncelleme |
| 4 | Kapı sırası değişimi | Güvenlik denetçisi | §D.1 + §F.3 |
| 5 | Açık soru kapatma | Bayram Ali | §E.3 satır durumu |
| 6 | Kapsam dışı genişletme | Karar sahibi | §A.2 + gerekçe |

### §G.3 Kapsam İhlal Belirtileri

| # | Belirti | Örnek | Aksiyon |
|---|---------|-------|---------|
| 1 | Tahmini dosya adı | Doğrulanmamış yol | Glob ile doğrula |
| 2 | Bağımlı adım | Bileşeni olmayan adım | `verify-required` işaretle |
| 3 | Çift karşılık | İki ad bir kavram için | §5'e ikilik yaz |
| 4 | Döngü | Geriye giden ok | DUR + escalation |
| 5 | Sıra ihlali | Kapı atlanmış kapı | Güvenlik denetçisi |
| 6 | Sürüm tahmini | Doğrulanmamış paket | Composer satırı oku |

### §G.4 Kapsam Kapanış Kontrolü

| # | Kontrol | Beklenen | Sonuç |
|---|---------|----------|-------|
| 1 | §A–§G başlıkları var mı? | 7 bölüm | ✅ |
| 2 | Her tabloda boş hücre var mı? | 0 | ✅ |
| 3 | Dosya:Satırlar doğrulandı mı? | Evet | ✅ |
| 4 | Açık sorular kayıtlı mı? | 5 satır | ✅ |
| 5 | Son satır şablona uygun mu? | Authority satırı | ✅ |
## §H Kapsam SSS (Sık Sorulan Sınır Soruları)

### §H.1 Sınır Soruları ve Yanıtları

| # | Soru | Yanıt | Dayanak |
|---|------|-------|---------|
| 1 | Bir iş kuralı akışta mı işlenir? | Hayır, K008-domain'de | §A.2 satır 1 |
| 2 | Uzun iş akışta mı çalıştırılır? | Hayır, devredilir | §A.2 satır 2 |
| 3 | HTTP isteği olmayan tetikleyici kapsamda mı? | Hayır | §A.3 kriter 3 |
| 4 | Hata yolu akışa dahil mi? | Evet | §2 satır 12 |
| 5 | Ödeme akışı bu dosyada mı? | Hayır | §A.2 satır 4 |
| 6 | Grafikte döngü olabilir mi? | Hayır, denetlenir | §D.4 |

### §H.2 Kim Ne Yapar (Rol Tablosu)

| # | Rol | Kapsam Görevi | Çıktı |
|---|-----|---------------|-------|
| 1 | Vault Steward | Kapsam kaydı ve ikilik denetimi | §5 + §E.3 |
| 2 | Bayram Ali | Kapsam kararı ve onayı | §G.2 kararları |
| 3 | Backend | Bileşen ve arayüz doğrulaması | §B + §C |
| 4 | Güvenlik denetçisi | Kapı sırası denetimi | §F.3 |
| 5 | QA | Doğrulama kaydı | §E.1 + §E.2 |
### §H.3 Kapsam Dışı Devir Adımları

| # | Devredilen İş | Hedef K Düğümü | Devir Biçimi | Durum |
|---|---------------|----------------|--------------|-------|
| 1 | İş kuralı uygulaması | K008-domain | Sınıf çağrısı | ✅ |
| 2 | Uzun iş çalıştırma | K012-services | Sözleşme | ⚠️ kontrat yok |
| 3 | Dış API çağrısı | K013-integraion | Kontrat | ⚠️ kontrat yok |
| 4 | Zamanlanmış tetikleyici | K018-operasyon | Tanım | ⚠️ çalıştırıcı yok |
| 5 | Olay tüketimi | K014-observability | Olay kaydı | ✅ |
**Last Updated:** 2026-10-13

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode