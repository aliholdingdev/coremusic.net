---
title: "K011 · odem-servisleri — bilesenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: odem-servisleri
status: active
updated: 2026-10-15
group: mimari
---

# K011 · odem-servisleri — Bileşenler & Envanter

**Amaç:** Ödeme servisleri için **mevcut altyapının** gerçek glob sayımlarıyla envanteri, bileşen↔akış iz düşümü ve **boşlukların** iz düşümü.

## §1 — Dizin/Dosya Envanteri (ölçüm 2026-10-15)

| Yol | Sayım | İçerik |
|-----|-------|--------|
| `shared\src\Middleware\` (`*.php`) | **11** | Auth, BypassAuth, Cors, Csrf, MiddlewarePipeline, OriginCheck, Permission, RateLimiter, SecurityHeaders, SessionManager, Validation |
| `shared\src\Api\Middleware\` (`*.php`) | **7** | ApiMiddlewarePipeline, Auth, Authorization, RateLimit, RequestValidation, ResponseNormalization, Validator |
| `shared\src\Security\` (`*.php`) | **6** | CacheRateLimiter, JwtService, ReturnUrlPolicy, SecurityHelper, SessionKeys, UuidV7 |
| `shared\src\Api\Dto\` (`*.php`) | **11** | 6 Request + 5 Response |
| Repo geneli `*payment*|*billing*|*checkout*|*stripe*` dosya adı | **2** | `.claude\agent\payment-integration.md` · `.claude\skills\prompt-maker\references\25-fintech-payment-patterns.md` |

> **Kritik ölçüm notu:** Son satırdaki 2 dosya **kaynak kod DEĞİLDİR** — biri agent rol tanımı, diğeri skill referansıdır. Repo'da `*.php`/`*.js`/`*.sql` ödeme **kaynak kodu = 0**.

## §2 — Boşlukların İz Düşümü

| Boşluk | Beklenen Bileşen | Durum | Etkilenen Akış |
|--------|------------------|-------|----------------|
| Ödeme servisi | `PaymentService.php` | ❌ YOK | Tüm ödeme akışı |
| Ödeme DTO'su | `PaymentRequest`/`PaymentResponse` | ❌ YOK | Giriş/çıkış |
| Webhook imza doğrulayıcı | `WebhookSignatureVerifier.php` | ❌ YOK | Webhook akışı |
| Idempotency store | `IdempotencyKeyRepository.php` | ❌ YOK | Tekrar koruması |
| Ödeme şeması | `coremusic_payment.sql` | ❌ YOK | Ledger |
| PCI anahtar listesi | `Redactor::SENSITIVE_KEYS` genişletmesi | ⚠️ Kısmi | Log maskeleme |
| Ödeme testi | `*PaymentTest.php` | ❌ YOK | QA kapısı |
| PCI/Stripe ADR'si | `.decisions/` altı | ❌ YOK | Mimari karar |

## §3 — Mevcut Temel ↔ Hedef Bileşen Eşlemesi

| Mevcut Temel (✅ var) | Hedef Bileşen (❌ eksik) | Hangi Rolde |
|-----------------------|--------------------------|-------------|
| `SecurityHeadersMiddleware.php` | — | Clickjacking/CSP koruması |
| `CsrfMiddleware.php` + `CsrfSyncManager.js` | — | Ödeme POST CSRF koruması |
| `RateLimitMiddleware.php` (`withLimits`) | — | Kötü niyetli deneme sınırlama |
| `Redactor.php` | PCI anahtar listesi | PAN/token maskeleme |
| `EnvParser.php` | Webhook secret yönetimi | `.env` secret okuma |
| `OriginCheckMiddleware.php` | Webhook origin kontrolü | Sahte webhook reddi |
| `JwtService.php` | — | Oturum/ölçüm jetonu |
| `Api\Dto\*` (11 dosya) | `PaymentRequest`/`PaymentResponse` | Serileştirme deseni |
| `api_calls` gözetim tablosu | `payment_events` tablosu | Denetim izi |

## §4 — Bağımlılık Grafiği (hedef — PLANNED)

```text
[Kullanıcı Ödeme Formu]
        │ CSRF token (✅ CsrfSyncManager)
        ▼
[FetchWrapper] ──► [RateLimitMiddleware ✅]
        │                  │
        ▼                  ▼
[PaymentService ❌] ──► [Stripe API ❌]
        │
        ▼
[PaymentResponse DTO ❌] ──► [toArray] ──► JSON
        │
        ▼
[payment_events SQL ❌] + [Redactor ✅]
        ▲
[Webhook ❌] ──► [SignatureVerifier ❌] ──► [Idempotency ❌]
```

**Yön:** mevcut güvenlik katmanı (✅) ödeme katmanının (❌) **altında** durur ve onu taşır. Eksik olan yalnızca üst katmandır.

## §5 — Öncelikli Boşluk Kapatma Sırası

| # | Boşluk | Öncelik | Öncel Koşul |
|---|--------|---------|-------------|
| 1 | PCI/Stripe ADR'si (SAQ sınıfı kararı) | **CRITICAL** | Diğer her şey buna bağlı |
| 2 | Sağlayıcı seçimi + `.env` secret deseni | **CRITICAL** | ADR #1 |
| 3 | `PaymentRequest`/`PaymentResponse` DTO | HIGH | K011 dto-ve-mapper deseni hazır |
| 4 | Webhook imza doğrulayıcı + idempotency | HIGH | ADR #1 |
| 5 | `Redactor` PCI anahtar listesi | HIGH | ADR #1 |
| 6 | `payment_events` şeması | MEDIUM | DTO'lar hazır |
| 7 | Ödeme testleri | MEDIUM | Tüm üstteki |

## §6 — Ölçüm Notu (Zero-Hallucination)

Yukarıdaki **tüm sayımlar** bu oturumda PowerShell `Get-ChildItem` ile diskten alınmıştır. `payment|billing|checkout|invoice|stripe` ad-taraması repo genelinde çalıştırılmıştır; çıkan 2 dosyanın **agent/skill dokümanı olduğu** doğrulanmıştır — kaynak kod olarak sayılmamıştır. Hiçbir eksik bileşen "var" olarak sunulmamıştır; tamamı ❌/⚠️ işaretlidir.

## §7 — HTTP Middleware Bileşen Envanteri (11 dosya · satır-satır okuma)

> Ölçüm: `[System.IO.File]::ReadAllLines(...)` · `shared\src\Middleware\*.php` · 2026-10-16.

| # | Dosya | Satır | Rol (ödeme yüzeyi) |
|---|-------|-------|--------------------|
| 1 | `RateLimiterMiddleware.php` | **146** | APCu tabanlı genel limit (`withLimits` ile kova ayırma) |
| 2 | `CorsMiddleware.php` | **143** | origin politikası — ödeme API çağrısında çapraz kaynak |
| 3 | `OriginCheckMiddleware.php` | **122** | istek origin doğrulama — sahte webhook reddi iskeleti |
| 4 | `SecurityHeadersMiddleware.php` | **104** | CSP/HSTS başlıkları (§1 · `ornekler` §8-§9) |
| 5 | `ValidationMiddleware.php` | **84** | istek doğrulama |
| 6 | `BypassAuthMiddleware.php` | **66** | ADR-008 kapsamı — **kapsam genişletme yasak** |
| 7 | `CsrfMiddleware.php` | **65** | ödeme POST CSRF denetimi (§4 eşlemesi) |
| 8 | `PermissionMiddleware.php` | **64** | yetki kontrolü |
| 9 | `MiddlewarePipeline.php` | **54** | zincir yürütücüsü — yeni middleware **kaydı burada** |
| 10 | `AuthMiddleware.php` | **35** | oturum doğrulama |
| 11 | `SessionManagerMiddleware.php` | **25** | oturum yaşam döngüsü |
| — | **Toplam** | **908** (11 dosya · ort **82,5**) | — |

- **Çıkarım (kanıt: tablo):** en ağır 3 dosya (146+143+122 = **411**) limit, origin ve
  güvenlik başlığıdır → ödeme için gereken üç iskelet **zaten en kalın dosyalarda** duruyor.
- `MiddlewarePipeline.php` (54) tek kayıt noktası: ödeme middleware'i eklemek = bu dosyaya
  satır (**kod yazımı bu görevin kapsamında değil** — `PLANNED`).

## §8 — API Middleware Bileşen Envanteri (7 dosya · 788 satır)

| # | Dosya | Satır | Not |
|---|-------|-------|-----|
| 1 | `RequestValidationMiddleware.php` | **175** | en ağır — şema doğrulama |
| 2 | `Validator.php` | **145** | kural motoru |
| 3 | `AuthenticationMiddleware.php` | **134** | API kimlik doğrulama |
| 4 | `AuthorizationMiddleware.php` | **111** | yetki |
| 5 | `ResponseNormalizationMiddleware.php` | **97** | yanıt şeması temizliği (ADR-020 API3 ile kesişir) |
| 6 | `RateLimitMiddleware.php` | **80** | `withLimits` + `api:<sha256>` key (`ornekler` §11) |
| 7 | `ApiMiddlewarePipeline.php` | **46** | API zinciri kaydı |
| — | **Toplam** | **788** (ort **112,6**) | — |

- API katmanı, HTTP katmanından **dosya başına daha ağır** (112,6 ↔ 82,5) — doğrulama/
  kimlik işi API tarafında yoğunlaşır.
- `⚠️ VERIFICATION REQUIRED`: ADR-020'in "`RateLimitMiddleware` pipeline kaydı" sorusu
  bu görevde **kapatılmadı** (`ApiMiddlewarePipeline.php` gövdesi okunmadı).

## §9 — Security + Log Bileşen Envanteri (6 + 3 dosya)

### §9.1 `shared\src\Security\` — 551 satır (6 dosya · ort 91,8)

| Dosya | Satır | Ödeme ilgisi |
|---|---|---|
| `ReturnUrlPolicy.php` | **185** | en ağır — geri dönüş URL'si politikası (ödeme sonrası redirect) |
| `JwtService.php` | **137** | jeton üretimi/doğrulama |
| `UuidV7.php` | **104** | kronolojik id — ödeme id/idempotency adayı |
| `SecurityHelper.php` | **56** | yardımcılar |
| `CacheRateLimiter.php` | **41** | **`RateLimitMiddleware`'in deposu** — `ornekler` §11.2'deki fail-safe boşluğunun sahibi |
| `SessionKeys.php` | **28** | oturum anahtar sabitleri |

### §9.2 `shared\src\Log\` — 393 satır (3 dosya)

| Dosya | Satır | Not |
|---|---|---|
| `FileHandler.php` | **253** | dosya yazıcı |
| `Redactor.php` | **106** | maskeleme (`ornekler` §10 · 3 adım) |
| `LoggerFactory.php` | **34** | fabrika |

- **İz düşümü:** log zinciri `LoggerFactory (34) → FileHandler (253) → Redactor (106)`;
  `Redactor` sınıfı yorumunda **"Tüm log yazma yolları … satırı BURADAN geçirir; tek
  maskeleme noktası"** der (Redactor.php:10-11) → ödeme logu **tek kapıdan** geçer;
  kapı `SENSITIVE_KEYS` içinde kart anahtarı **bulunmadığı için** (§17) yarı açık kalır.

## §10 — Config Bileşen Envanteri (4 dosya · 414 satır)

| Dosya | Satır | Not |
|---|---|---|
| `ConfigManager.php` | **160** | merkez |
| `DomainConfig.php` | **132** | `isHttps()`/`getUrl('assets'|'api')` — CSP origin kaynağı (`ornekler` §8.4) |
| `AuthRouteConfig.php` | **71** | auth rotaları |
| `EnvParser.php` | **51** | **`.env` okuma** → ödeme secret'ı (`STRIPE_SECRET` vb.) buraya girer |

- **Boşluk bağlantısı:** §2'deki "sağlayıcı secret deseni" satırı bu 51 satırlık sınıfa
  bağlıdır; sınıf **var**, ödeme anahtarları **yok** (`PLANNED`).

## §11 — PageRouter Bileşen Envanteri (14 dosya · 1880 satır)

| Dosya | Satır | Dosya | Satır |
|---|---|---|---|
| `PageRouterKernel.php` | **351** | `RouteRegistry.php` | **83** |
| `HtmlShellRenderer.php` | **311** | `StructuredLogger.php` | **81** |
| `PageRouter.php` | **192** | `AuthGuard.php` | **80** |
| `RequestNormalizer.php` | **177** | `ErrorHandler.php` | **77** |
| `ResponseEmitter.php` | **163** | `RouteResult.php` | **88** |
| `SessionInitializer.php` | **133** | `AuthUrlBuilder.php` | **96** |
| `PageRouterHelper.php` | **29** | `SpaRoute.php` | **19** |
| `SpaRoute` hariç **14 php** | — | **Toplam** | **1880** |

- `PageRouterKernel.php` (351) + `HtmlShellRenderer.php` (311) = **35 %** → ödemeli sayfa
  render'ı bu iki dosyadan geçer.
- `StructuredLogger.php` (81) — `Redactor` yorumunda adı geçen üç yazma yolundan biri
  (`Redactor.php:10-11`).

## §12 — DTO Bileşen Envanteri (11 dosya · 999 satır · ort 90,8)

| Grup | Dosya | Satır |
|---|---|---|
| Request (6) | `MusicSearchRequest.php` | **94** |
| | `RegisterRequest.php` | **94** |
| | `LoginRequest.php` | **72** |
| | `PlaylistAddTrackRequest.php` | **72** |
| | `PlaylistCreateRequest.php` | **72** |
| | `UserUpdateRequest.php` | **72** |
| | **Request alt toplam** | **476** |
| Response (5) | `MusicResponse.php` | **116** |
| | `UserResponse.php` | **116** |
| | `PaginationResponse.php` | **114** |
| | `PlaylistResponse.php` | **94** |
| | `LoginResponse.php` | **83** |
| | **Response alt toplam** | **523** |
| — | **Toplam (11)** | **999 · ort 90,8** |

- **Düzeltme (2026-10-16):** eski "1.011 satır / ort 91,9" kaydı **yanlış**; bu tablo
  dosya bazında **tekrar ölçülmüştür** (§15'teki komutla doğrulanabilir).
- **Ödeme eşlemesi (§3):** `PaymentRequest`/`PaymentResponse` bu 11 dosyanın **deseniyle**
  yazılacak; en yakın boyut referansı `LoginRequest` (72) / `LoginResponse` (83).

## §13 — Test Bileşen Envanteri (9 dizin · 45 php · 6147 satır)

| Dizin | Dosya | Satır | `phpunit.xml` suite? |
|---|---|---|---|
| `Middleware/` | **9** | **1858** | ✅ `Middleware` |
| `Api/` | **8** | **1051** | ✅ `Api` |
| `Unit/` | **16** | **1690** | ✅ `Unit` |
| `Repository/` | **4** | **661** | ✅ `Repository` |
| `Security/` | **2** | **347** | ✅ `Security` |
| `Events/` | **2** | **169** | ✅ `Events` |
| `OAuth/` | **1** | **160** | ✅ `OAuth` |
| `Component/` | **1** | **109** | ✅ `Component` |
| `Fixtures/` | **2** | **2** | ❌ **suite yok** (yalnız veri) |
| — | **45** | **6147** | 8 suite |

- `shared\phpunit.xml` **8 `<testsuite>`** tanımlar; dizin sayısı **9** → `Fixtures/`
  bilinçli olarak suite dışıdır (2 dosya / 2 satır — iskelet).
- **Ödeme testi boşluğu (§2 #7):** `*PaymentTest.php` **0**; ADR-023 satır 63 bunu
  **açıkça** `⚠️ VERIFICATION REQUIRED` ile işaretler: *"payment testinin hiç var olmaması
  (payment kodu/kapsamı ADR-023'e dahil değil — ayrı karar)"*.
- `auth.coremusic.net` tarafı: **2580 php** (vendor hariç **37**) ·
  `auth.coremusic.net\tests` dizini **vardır** (`config, handler, include, pages, routes, tests, vendor`).

## §14 — CSS Bileşen Envanteri (ITCSS katmanları · dosya + satır)

### §14.1 `Css\04_Components\` — 15 dosya · 3360 satır (ort 224)

| Dosya | Satır | Dosya | Satır |
|---|---|---|---|
| `_home-components.css` | **718** | `c-progress.css` | **149** |
| `c-forms.css` | **356** | `c-home-song-btn.css` | **133** |
| `c-card.css` | **291** | `c-footer-volume.css` | **155** |
| `c-buttons.css` | **289** | `c-badge.css` | **197** |
| `c-modal.css` | **256** | `c-toggle.css` | **202** |
| `c-toast.css` | **243** | `c-scrollbar-accent.css` | **13** |
| `_player-info.css` | **239** | `c-footer-seek.css` | **78** |
| `_welcome-banner.css` | **41** | **Toplam** | **3360** |

- **Ödeme formu** için doğrudan girecek bileşenler: `c-forms.css` (356) · `c-buttons.css` (289)
  · `c-modal.css` (256) · `c-toast.css` (243) — **toplam 1144 satır hazır yüzey**.

### §14.2 Katman sayım tablosu (dosya)

| Katman | Dosya | Not |
|---|---|---|
| `01_Abstracts` | **19** | token tek kaynağı (ham hex/px yalnız burada) |
| `03_Layout` | **4** | `_footer`, `_header`, `_sidebar`, `_widget-grid` |
| `04_Components` | **15** | §14.1 |
| `05_Pages` | **13** | sayfa stilleri |
| `08_Devices` | **15** | cihaz ayrımı |
| **`Css\` geneli** | **94 dosya / 62.378 satır** | `Recurse` · `*.css` · 2026-10-16 |

## §15 — JS Bileşen Envanteri (95 dosya · 13.369 satır · `js\` altı)

| Alt dizin | js | Öne çıkan satır kanıtı |
|---|---|---|
| `js\components\` | **37** | `ComponentBase` 281 · `ComponentAccessibility` 235 · `ComponentEvents` 168 · `ComponentLoader` 158 · `ComponentRegistry` 116 |
| `js\router\` | **28** | `DomPatcher` 127 · `SPARouterAdapter` 123 · `NavigationOrchestrator` 106 · `Router` 81 · `FetchWrapper` 63 · **`CsrfSyncManager` 8** |
| `js\features\` | **7** | `PlayerController` 183 · `welcome-modal` 151 · `PageScaleTargets` 115 |
| `js\core\` | **6** | (6 dosya) |
| `js\managers\` | **5** | `SidebarManager` **1057** · `ScaleManager` **509** · `ThemeManager` 248 |
| `js\coreplayer\` | **5** | (5 dosya) |
| `js\auth\` | **2** | (2 dosya) |
| **`js\` toplam** | **95** | **13.369 satır** · bunun **7'si `*.spec.js`** |

### §15.1 Alt kümeler (tam okuma listeleri)

- **`components\primitives\` (6 bileşen + 6 spec):** `SliderComponent` 197/178 ·
  `ButtonComponent` 155/141 · `InputComponent` 153/153 · `BadgeComponent` 121/135 ·
  `ToggleComponent` 123/145 · `SkeletonComponent` 105/132 (biçim: `uygulama/spec`).
- **`components\composites\` (12):** `ToastComponent` 221 · `DropdownComponent` 207 ·
  `CardComponent` 177 · `ModalComponent` 177 · `TabsComponent` 175 · `HeroComponent` 137 ·
  `MiniCardComponent` 142 · `AvatarComponent` 124 · `TooltipComponent` 123 ·
  `QuickAppsComponent` 113 · `NavLinkComponent` 98 · `ProgressComponent` 98 ·
  `WidgetAreaComponent` 92 → **13 kayıt** (12 composites + sıralama).
- **`components\interactive\` (7):** `ToastComponent` 208 · `PlayerInfoComponent` 179 ·
  `InfiniteScroll` 139 · `AccordionComponent` 135 · `DropdownComponent` 130 ·
  `TabsComponent` 122 · `HistoryComponent` 75.
- **`js\` kök dosyalar (5):** `device-layout-updater` 536 · `device-loader` 423 ·
  `oauth-manager` 321 · `main` 193 · `devices.config` 44.
- **`tests\` (js\` dışında):** `tests\components\player-info.spec.js` 598 ·
  `tests\components\history.spec.js` 136 · `tests\mocks\player-api.mock.js` 184 ·
  `vitest.config.js` 33 → `assets.coremusic.net` geneli **99 js / 9 spec**
  (`js\` 95+7 ile bu 4 dosyanın toplamı).

### §15.2 `js\router\config\` — 5 dosya (ödeme isteği yapılandırması)

| Dosya | Satır | İçerik |
|---|---|---|
| `auth-routes.js` | 22 | auth rotaları |
| `signal-utils.js` | 19 | `createTimeout`/`combineSignals` (`ornekler` §13) |
| `error-types.js` | 10 | `ABORT_ERROR_NAME` |
| `headers.js` | 9 | `HEADERS`, `AJAX_VALUE`, `CONTENT_TYPES` |
| `css-selectors.js` | 3 | seçici sabitleri |

## §16 — Olay / İstisna / Sözleşme / OAuth Envanteri

| Kümе | Sayı | Kanıt |
|---|---|---|
| `shared\src\Events\Domain\` | **9** php | `Get-ChildItem -Filter *.php` |
| `shared\src\Events\Integration\` | **3** php | aynı |
| `shared\src\Exception\` | **8** php | aynı |
| `shared\src\Contracts\` (recursive) | **19** php | aynı |
| `shared\src\OAuth\` (+Provider) | **14** php (Provider: **12**) | aynı |
| `shared\src` (recursive) | **165** php | 2026-10-15/16 ölçümü |

- **Ödeme olayı boşluğu:** `Events\Domain` 9 dosyanın hiçbiri `*Payment*` adlı değil
  (ad-tarama §17) → `PaymentSucceeded`/`PaymentFailed` **yok** (`PLANNED`).

## §17 — Boşluk Kanıtları (her satır ayrı ölçüm)

| # | İddia | Ölçüm | Sonuç |
|---|---|---|---|
| B1 | Repo'da ödeme **kaynak kodu** yok | `*.php|*.js|*.css|*.sql` içinde adı `payment|billing|checkout|invoice|stripe` geçen dosya | **0** |
| B2 | Ad-taraması **tüm** dosyalarda | aynı desen, uzantı filtresi yok | **2**: `.claude\agent\payment-integration.md` · `.claude\skills\prompt-maker\references\25-fintech-payment-patterns.md` → **ikisi de doküman** (§1 ile aynı) |
| B3 | Ödeme ADR'si yok | `.ai\.decisions` içinde `stripe|payment|PCI` geçen ADR'ler | ADR-008 (fail-closed ilkesi) · ADR-013 (Stripe **rate-limit araştırması**) · ADR-020 (Stripe **API-key deseni**) · ADR-022 (**PII grep** satır 48) · ADR-023 (**payment test 0** satır 63) · ADR-036 (skill dosyası referansı) → **ödemeye özgü ADR = 0** |
| B4 | Ödeme testi yok | `*PaymentTest.php` / `*Stripe*Test.php` | **0** (ADR-023:63 ile çelişmiyor — o da "yok" diyor) |
| B5 | PCI kart anahtarı logda maskeli değil | `Redactor.php:31` — `card_number`/`pan`/`cvc` **yok** | **kısmi** (§2 #6) |
| B6 | Ödeme şeması yok | `.ai\.sql\mysql\*payment*` | **0** (§2 #5) |
| B7 | HSTS yok | `SecurityHeadersMiddleware.php` `Strict-Transport` | **0** (`ornekler` §9.1) |
| B8 | Ödeme `POST` yok | `FetchWrapper.js:52` `method: 'GET'` sabit | **0** (`ornekler` §12.3) |

- **ADR-022 satır 48 kanıtı:** `.ai/.sql/mysql/*.sql` içinde `phone|address|payment_card|card_number`
  grep'i → yalnız `ip_address`/`mac_address`/`email` → **"PII field encryption PLANNED"**.
  Yani vault, ödeme verisi şemasının **bugün yok olduğunu** ADR metninde de yazmıştır.

## §18 — Bileşen ↔ Akış İz Düşümü (katman bazlı, satır kanıtlı)

| Katman | Mevcut bileşen (dosya · satır) | Ödeme rolü | Eksik |
|---|---|---|---|
| L0 Config | `EnvParser.php` 51 · `ConfigManager.php` 160 · `DomainConfig.php` 132 | secret + origin | sağlayıcı secret deseni ❌ |
| L1 Middleware (HTTP) | `CsrfMiddleware.php` 65 · `RateLimiterMiddleware.php` 146 · `SecurityHeadersMiddleware.php` 104 · `OriginCheckMiddleware.php` 122 · `CorsMiddleware.php` 143 | CSRF · limit · CSP · sahte webhook reddi · CORS | ödeme middleware'i ❌ (kayıt `MiddlewarePipeline.php` 54) |
| L2 API | `RateLimitMiddleware.php` 80 · `RequestValidationMiddleware.php` 175 · `AuthenticationMiddleware.php` 134 · `ResponseNormalizationMiddleware.php` 97 | istek limiti · şema · auth · temiz yanıt | `PaymentService` ❌ |
| L3 Router/Render | `PageRouterKernel.php` 351 · `HtmlShellRenderer.php` 311 · `AuthGuard.php` 80 · `StructuredLogger.php` 81 | ödeme sayfası render + guard + log | ödeme rotası ❌ |
| L4 DTO | 11 dosya / 999 satır (§12) | serileştirme deseni | `PaymentRequest/Response` ❌ |
| L5 Log | `LoggerFactory.php` 34 · `FileHandler.php` 253 · `Redactor.php` 106 | maskeleme (tek kapı) | PCI anahtar listesi ⚠️ |
| L6 Security/Store | `CacheRateLimiter.php` 41 · `JwtService.php` 137 · `UuidV7.php` 104 | sayaç · jeton · id | idempotency store ❌ |
| L7 Event | Domain 9 · Integration 3 | olay yayılımı | `PaymentSucceeded/Failed` ❌ |
| L8 DB | `.ai\.sql\mysql\coremusic_api.sql` (`api_calls`, `api_route_logs`) | gözetim iskeleti | `payment_events` ❌ |
| L9 Frontend | `FetchWrapper.js` 63 · `CsrfSyncManager.js` 8 · `OAuth` JS `oauth-manager.js` 321 | istek · CSRF · üçüncü taraf deseni | ödeme formu bileşeni ❌ |
| L10 Test | 45 php / 6147 satır · 7+2 spec (§13, §15) | QA kapısı | `*PaymentTest*` ❌ |

**Çıkarım:** eksik olan **tek bir katman değil**, her katmandaki **tek bileşen**;
alt katmanların tamamı ölçülmüş ve doludur (toplam: 165 php + 95 js + 94 css).

## §19 — Pipeline Bileşen Derin Okuma (3 dosya · 141 satır)

> Üç dosya da **satır satır okundu** (2026-10-16, `read_text_file`); sayımlar
> `ReadAllLines` ile §18 tablosuyla birebir: 54 + 46 + 41 = **141**.

| Dosya | Satır | Çıkan semboller (satır no) |
|---|---|---|
| `shared\src\Middleware\MiddlewarePipeline.php` | 54 | `pipe(IMiddleware)` 17 · `run` 23 · `buildPipeline` 29 · `wrap` 38 · `hrtime(true)` 42/46 · `timing('MiddlewarePipeline', …)` 48 · `?StructuredLogger` 6/14 |
| `shared\src\Api\Middleware\ApiMiddlewarePipeline.php` | 46 | `pipe(callable)` 24 · `process` 33 · `array_reverse` 38 · `@see ADR-084-api-gateway-architecture` 9 |
| `shared\src\Security\CacheRateLimiter.php` | 41 | `KEY_PREFIX='rl:'` 10/18/28/39 · `isLimited` 16 · `(int)$count >= $maxAttempts` 23 · `increment` 26 (`cache->set` 31 · `cache->increment` 33) · `reset` 37 |

**Çıkarımlar (yalnız okumadan):**

1. **İki pipeline farklı sözleşmede:** HTTP tarafı `IMiddleware` nesnesi ister
   (`MiddlewarePipeline.php:17`), API tarafı çıplak `callable` ister
   (`ApiMiddlewarePipeline.php:24`). Aynı `pipe/run` ismi, **iki farklı tip
   sözleşmesi** — ödeme middleware'i eklenirken hangi pipeline'a yazılacağı
   kararı önceden verilmelidir (`PLANNED`).
2. **API pipeline'ında gözlem yok:** `ApiMiddlewarePipeline.php` içinde
   `hrtime` · `timing` · `StructuredLogger` · `try`/`catch` araması → **0 isabet**
   (dosya içi tam tarama). HTTP tarafı ise her sarmalayıcıda süre ölçüp
   `logger->timing` yazar (`MiddlewarePipeline.php:42-48`) ve logger
   `null` olabilir (`:14`) — logger yoksa ölçüm sessizce atlanır.
3. **`CacheRateLimiter` hata yutuyor değil:** `try`/`catch` **0 isabet**;
   `cache->get()` false/null ise `isLimited` `false` döner
   (`CacheRateLimiter.php:20-21`) — yani **sayaç okunamazsa istek geçer**
   (fail-open görünümü). Cache çağrısı **exception fırlatırsa** yakalayan yok →
   hata yukarı yayılır; çağıran katmanın davranışı `⚠️ VERIFICATION REQUIRED`
   (henüz okunmadı). `increment`'ta mevcut anahtar için TTL **yenilenmez**
   (`:33` yalnız `cache->increment`, `:31` yalnız ilk set) — pencere kayması
   riski, gözlemsel.
4. **Sıra tersleme ortak:** iki pipeline da `array_reverse` ile dıştan içe
   sarar (`MiddlewarePipeline.php:31` · `ApiMiddlewarePipeline.php:38`) →
   kayıt sırası **ilk eklenen dış** katman olur.

## §20 — Sözleşme (Contract) Envanteri

| Dosya | Satır | İçerik (okundu) |
|---|---|---|
| `shared\src\Contracts\Middleware\IMiddleware.php` | 9 | tek method: `handle(array $request, callable $next): array` |
| `shared\src\Contracts\Security\IRateLimiter.php` | 11 | 3 method: `isLimited(string,int,int): bool` · `increment(string,int): void` · `reset(string): void` |

- `shared\src\Contracts\` kök dizini: **8 alt dizin** + `CLAUDE.md` →
  `AI` · `Api` · `Auth` · `Config` · `Database` · `Events` · `Middleware` · `Security`
  (`list_directory` 2026-10-16).
- `CacheRateLimiter implements IRateLimiter` (`CacheRateLimiter.php:8`) —
  3 methodun imzası arayüzle **birebir** (okuma karşılaştırması).
- **Ödeme sözleşmesi boşluğu:** `Contracts\` altında `Payment*`/`Billing*`
  dosya adı **yok** (§17 B1 ad-taraması ile aynı ölçüm) → `IPaymentService`
  `PLANNED`.

## §21 — ADR-084 (API Gateway) İlişkisi

| Öğe | Kanıt |
|---|---|
| Dosya | `.ai\.decisions\accepted\ADR-084-api-gateway-architecture.md` (**104 satır**) |
| `status` | `accepted` (frontmatter `status: accepted`, satır 10) |
| `date` / `updated` | `2026-10-07` / `2026-10-07` |
| Authority satırı | "route/DTO/pipeline sözleşmesi kodda IMPLEMENTED, CQRS ve OpenAPI spec PLANNED" |
| Kodda iz | `ApiMiddlewarePipeline.php:9` — `@see ADR-084-api-gateway-architecture` |
| ADR envanteri | `.ai\.decisions\**\ADR-*.md` = **83 dosya** (2026-10-16 glob) |

- **Çıkarım:** API pipeline'ının ADR dayanağı **var ve accepted**; boşluk
  pipeline'ın kendisinde değil, **ödeme pipeline kaydında** (§18 L1-L2 ❌).
- `.decisions/` salt okunur — bu görevde ADR **yazılmadı/değiştirilmedi**.

## §22 — Claim Matrisi (Pipeline · W13-W18) ve Açık Sorular

| # | Claim | Kanıt | Durum |
|---|---|---|---|
| W13 | HTTP ve API pipeline farklı tip sözleşmesi kullanır | `MiddlewarePipeline.php:17` (`IMiddleware`) ↔ `ApiMiddlewarePipeline.php:24` (`callable`) | ✅ **okundu** |
| W14 | API pipeline'ında süre/loglama yok | dosya içi `hrtime|timing|StructuredLogger` taraması → 0 | ✅ **okundu** |
| W15 | `CacheRateLimiter` hataları yutar (catch) | `try|catch` → 0; false/null kolları satır 20-21/30 | ❌ **iddia çürütüldü** |
| W16 | Sayaç okunamazsa istek geçer (fail-open) | `isLimited` false/null → `return false` (20-21) | ✅ **okundu** (çağıranın davranışı ayrı) |
| W17 | API pipeline'ın ADR dayanağı kabul edilmiş | ADR-084 `status: accepted` · 104 satır · kodda `@see` | ✅ **okundu** |
| W18 | `Contracts\` altında ödeme arayüzü yok | 8 alt dizin adı + B1 ad-taraması | ✅ **okundu** → `PLANNED` |

**Açık sorular (yanıtsız — sonraki okuma):**

1. `cache->get` **exception** fırlatırsa (cache down) çağıran ne yapar —
   `AuthenticationMiddleware`/`RateLimitMiddleware` içinde try/catch var mı?
   (`⚠️ VERIFICATION REQUIRED`)
2. Ödeme middleware'i **hangi** pipeline'a (`IMiddleware` mi, `callable` mı)
   yazılacak? Karar `PLANNED` — bu belge yalnız iki seçenek arasındaki farkı
   kanıtlar.
3. `CacheRateLimiter::increment` TTL yenilememesi (§19 çıkarım 3) bilinçli mi,
   pencere-pencere sayma mı hedefleniyor? (`⚠️ VERIFICATION REQUIRED`)

## §23 — Ölçüm Tekrarlanabilirliği (PowerShell · okuma)

```powershell
$root='C:\www\coremusic.net'
function L($p){[System.IO.File]::ReadAllLines($p).Length}
# §7-§12 satır sayıları
Get-ChildItem -LiteralPath "$root\shared\src\Middleware" -File -Filter *.php |
  ForEach-Object { '{0} -> {1}' -f $_.Name, (L $_.FullName) }          # 11 satır, toplam 908
$dto = Get-ChildItem -LiteralPath "$root\shared\src\Api\Dto" -Recurse -File -Filter *.php
'{0} dosya / {1} satir' -f $dto.Count, (($dto|%{L $_.FullName}|Measure-Object -Sum).Sum)  # 11 / 999
# §13 test
(Get-ChildItem -LiteralPath "$root\shared\tests" -Recurse -File -Filter *.php|Measure-Object).Count  # 45
# §14-§15 CSS/JS
(Get-ChildItem -LiteralPath "$root\assets.coremusic.net\Css" -Recurse -File -Filter *.css|Measure-Object).Count  # 94
(Get-ChildItem -LiteralPath "$root\assets.coremusic.net\js" -Recurse -File -Filter *.js|Measure-Object).Count   # 95
# §17 B1/B2 boşluk taraması
Get-ChildItem -LiteralPath $root -Recurse -File |
  Where-Object { $_.Name -match 'payment|billing|checkout|invoice|stripe' } |
  ForEach-Object { $_.FullName.Substring($root.Length+1) }             # 2 doküman, 0 kaynak
```

- Beklenen çıktılar 2026-10-16 koşusunda **birebir** alınmıştır.
- `.ai/scripts/*.ps1` **çalıştırılmadı** (§13.6 kuralı + görev yasağı); tüm sayımlar
  bu bloktaki standart komutlarla **doğrudan diskten** alınmıştır.

## §24 — Kapsam İçi / Kapsam Dışı ve Etiketler

| Öğe | Etiket | Gerekçe |
|---|---|---|
| §7-§16 sayımları | ✅ **okundu (2026-10-16)** | satır sayacı + glob |
| Ödeme servisi/DTO/test/şema | ❌ **YOK → PLANNED** | §17 B1-B6 |
| Ödeme middleware'i ekleme kodu | `PLANNED` | `MiddlewarePipeline` 54 satır — **bu görevde yazmadı** |
| `ApiMiddlewarePipeline` pipeline kaydı | `⚠️ VERIFICATION REQUIRED` | §8 notu |
| `CacheRateLimiter` fail-safe davranışı | `⚠️ VERIFICATION REQUIRED` | `ornekler` §11.2 (41 satır okundu, gövde analizi yok) |
| ADR-023 kapsamı | `⚠️ VERIFICATION REQUIRED` | ADR metni: "payment test … ayrı karar" |
| `.ai/.decisions/**` içeriği | salt okunur | frozen ADR — değiştirilmedi |
| `*.php/*.js/*.css/*.sql` | **yazılmadı** | görev yasağı |

## §25 — YAPMA ↔ YAP

| # | YAPMA | YAP | Kanıt |
|---|---|---|---|
| 1 | "Ödeme altyapısı var" demek (ad-taramasına bakmadan) | B1/B2 ölçümünü çalıştırıp **0 kaynak** demek | §17 B1/B2 |
| 2 | ADR-013'teki Stripe'ı "Stripe entegre" diye okumak | o **araştırma kaynağıdır**, kod değildir | `.decisions/ADR-013` satır 59-71 |
| 3 | `Fixtures/` dizinini "test suite" saymak | 9 dizin / 8 suite ayrımı | §13 |
| 4 | `auth.coremusic.net` php = 2580 demek (vendor dahil) | **vendor hariç 37** yazmak | §13 notu |
| 5 | `assets js 95` ile `99`'u karıştırmak | kapsam belirt: `js\` 95/7 · repo geneli 99/9 | §15 |
| 6 | Eksik bileşeni "kısmen var" demek | §2'deki ❌/⚠️ işaretini korumak | §2 · §17 |
| 7 | `shared/src` içinde ödeme sınıfı arayıp bulamayınca "her yerde yok" demek | kapsamı `shared\src` (165 php) ile sınırlı yazmak | §16 |

## §26 — Özet ve Sonraki Okuma

- §1-§6 **korunarak** §7-§26 eklendi: middleware (11/7) · security+log (6+3) · config (4)
  · PageRouter (14/1880) · DTO (11/999) · test (45/6147) · CSS (15/3360; katman 94/62378)
  · JS (95/13369; components 37 · router 28) · olay/istisna/sözleşme (9+3/8/19) ·
  8 boşluk kanıtı (B1-B8) · katmanlı iz düşümü · tekrarlanabilir ölçüm · etiket + YAPMA↔YAP ·
  **§19-§22 derin okuma** (pipeline 141 satır · contract 9+11 satır · ADR-084 104 satır ·
  W13-W18 claim matrisi + 3 açık soru).
- **Kesişim/URL notu:** bu belge **web-arastirma dosyası değildir**; kesişim satırı yalnız
  iki `*-web-arastirma.md` dosyasında bulunur ve orada **0 HIT** ile doğrulanmıştır
  (envanter 4286 URL, 2026-10-15).
- **Sonraki okuma:** `shared\src\Security\CacheRateLimiter.php` (41 satır — fail-safe
  gövdesi) → `shared\src\Api\Middleware\ApiMiddlewarePipeline.php` (46 — pipeline kaydı)
  → `assets.coremusic.net\js\components\base\ComponentBase.js` (281 — ödeme formu
  bileşen temeli).

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
