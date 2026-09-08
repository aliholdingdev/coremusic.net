---
type: architecture
category: contracts
title: "CoreMusic — 40-Day Detailed Implementation Plan"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_days: 40
total_phases: 5
reference:
  authority: ".ai/architecture/03-contracts/40-day-implementation-plan.md"
  depends_on:
    - ".ai/architecture/03-contracts/master-implementation-plan.md"
    - ".ai/architecture/03-contracts/project-structure.md"
    - ".ai/architecture/03-contracts/api-architecture-master.md"
    - ".ai/architecture/03-contracts/middleware-pipeline.md"
    - ".ai/decisions/accepted/ADR-087-master-implementation-plan.md"
---

# CoreMusic — 40-Day Detailed Implementation Plan

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Genel Bakış

### 1.1 Amaç

Bu belge, CoreMusic kurumsal medya platformunun sıfırdan geliştirilmesi için **40 günlük detaylı uygulama planıdır**. Her gün için spesifik görevler, bağımlılıklar, sorumlu ajanlar ve kabul kriterleri tanımlanmıştır.

**Zero Code Before Plan (ADR-007):** Bu plan onaylanmadan kod yazılmaz.

### 1.2 Hedef

| Kriter | Hedef |
|--------|-------|
| Mimari uyumluluk | L0-L6 katman bağımlılık kuralları %100 |
| Güvenlik | OWASP Top 10:2025 uyumlu |
| Performans | TTFB < 200ms, API < 100ms |
| Test kapsamı | ≥ %80 backend, ≥ %80 frontend |
| Dokümantasyon | Her önemli karar için ADR |
| Deployment | SSH/CD, sıfır kesinti kapasiteli |

### 1.3 Referans ADR'ler

| ADR | Konu | Durum |
|-----|------|-------|
| ADR-001 | Vanilla JS + ITCSS (framework yasak) | Frozen |
| ADR-002 | PDO mandatory, ORM yasak | Frozen |
| ADR-003 | 18 BCNF veritabanı | Frozen |
| ADR-004 | Multi-domain SPA mimarisi | Frozen |
| ADR-010 | CSRF koruma stratejisi | Frozen |
| ADR-011 | Session yönetimi | Frozen |
| ADR-012 | CSP nonce + strict-dynamic | Frozen |
| ADR-013 | Rate limiting (APCu) | Frozen |
| ADR-022 | DB hardened security | Frozen |
| ADR-038 | 8.1 ses kartı çip seçimi | Active |
| ADR-039 | 7-servis platform mimarisi | Active |
| ADR-040 | 18 BCNF DB otoritesi | Active |
| ADR-042 | Vault yeniden yapılandırma | Active |
| ADR-044 | Dynamic user theme engine | Active |
| ADR-083 | SPA Router Architecture | Active |
| ADR-084 | API Gateway Architecture | Active |
| ADR-085 | Shared Library Hybrid | Active |
| ADR-086 | Event Driven Architecture | Active |
| ADR-087 | Master Implementation Plan | Active |

---

## 2. Faz Genel Bakışı

```
Faz 1: Temel Altyapı (Gün 1-8)
  ├── Proje yapısı, shared library, DB şemaları, auth servisi
  └── Çıktı: Çalışan auth sistemi + 18 DB şeması

Faz 2: Backend Servisler (Gün 9-16)
  ├── API Gateway, middleware pipeline, media servisi, download servisi
  └── Çıktı: 7 backend servis + API gateway

Faz 3: Frontend (Gün 17-24)
  ├── SPA Router, CSS mimarisi, bileşenler, sayfalar
  └── Çıktı: Çalışan SPA frontend (10 panel)

Faz 4: Entegrasyon (Gün 25-32)
  ├── Cross-domain auth, WebSocket, real-time, event system
  └── Çıktı: Servisler arası iletişim + real-time özellikler

Faz 5: Üretim Hazırlığı (Gün 33-40)
  ├── CI/CD, monitoring, test, güvenlik denetimi, deployment
  └── Çıktı: Production-ready sistem
```

---

## 3. Faz 1: Temel Altyapı (Gün 1-8)

### 3.1 Gün 1: Proje Yapısı & dizin Oluşturma

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 1.1 | Root dizin yapısını oluştur | `C:\www\coremusic.net\` altındaki tüm alt dizinler | Backend | Tüm dizinler mevcut |
| 1.2 | Shared library scaffolding | `shared/composer.json` | Backend | PSR-4 autoloading (ADR-085) |
| 1.3 | Bootstrap dosyası | `shared/bootstrap.php` | Backend | Autoloader, config loader, error handler |
| 1.4 | Config dizini | `shared/config/` | Backend | services.php, middleware.php, cors.php |
| 1.5 | .env.example oluştur | `shared/.env.example` | Security | Tüm secrets dokümante (gerçek değer yok) |
| 1.6 | Subdomain dizinlerini oluştur | `auth.coremusic.net/`, `music.coremusic.net/`, vb. | Backend | Her subdomain bağımsız proje |

**Bağımlılık:** Yok (ilk gün)
**Çıktı:** Boş ama doğru dizin yapısı

### 3.2 Gün 2: Shared Library — Auth Domain

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 2.1 | User entity | `shared/src/Auth/Domain/Entity/User.php` | Backend | Immutable, readonly properties |
| 2.2 | Value Objects | `shared/src/Auth/Domain/ValueObject/Email.php`, `Password.php`, `UserId.php` | Backend | Immutability, validation |
| 2.3 | Role enum | `shared/src/Auth/Domain/Enum/Role.php` | Backend | 7 rol (guest-user-premium-studio-car-admin-system) |
| 2.4 | Repository interface | `shared/src/Auth/Domain/Repository/UserRepositoryInterface.php` | Backend | Port (Dependency Inversion) |
| 2.5 | Session entity | `shared/src/Auth/Domain/Entity/Session.php` | Backend | Immutable session data |
| 2.6 | Token entity | `shared/src/Auth/Domain/Entity/Token.php` | Backend | JWT/Session token |

**Bağımlılık:** Gün 1 (dizin yapısı)
**Çıktı:** Auth domain katmanı (bağımsız, test edilebilir)

### 3.3 Gün 3: Shared Library — Security Middleware

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 3.1 | Middleware pipeline contracts | `shared/src/Security/Middleware/MiddlewareInterface.php` | Backend | PSR-15 uyumlu |
| 3.2 | SessionManagerMiddleware | `shared/src/Security/Middleware/SessionManagerMiddleware.php` | Security | COREMUSIC_SESS cookie, CSP nonce |
| 3.3 | BypassAuthMiddleware | `shared/src/Security/Middleware/BypassAuthMiddleware.php` | Security | Dev only, `?_bypass=1` |
| 3.4 | RateLimiterMiddleware | `shared/src/Security/Middleware/RateLimiterMiddleware.php` | Security | APCu, 60 req/60s |
| 3.5 | AuthMiddleware | `shared/src/Security/Middleware/AuthMiddleware.php` | Security | Session/JWT auth info inject |
| 3.6 | SecurityHeadersMiddleware | `shared/src/Security/Middleware/SecurityHeadersMiddleware.php` | Security | CSP nonce, HSTS, X-Frame-Options |
| 3.7 | CsrfMiddleware | `shared/src/Security/Middleware/CsrfMiddleware.php` | Security | `csrf_token` key, hash_equals |

**Bağımlılık:** Gün 2 (auth domain)
**Çıktı:** 6 middleware (frozen pipeline order)

### 3.4 Gün 4: Shared Library — HTTP Kernel & Router

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 4.1 | HTTP Kernel | `shared/src/Http/Kernel.php` | Backend | Middleware pipeline orchestration |
| 4.2 | Request Factory | `shared/src/Http/Request/ServerRequestFactory.php` | Backend | PSR-7 uyumlu |
| 4.3 | Response Emitter | `shared/src/Http/Response/ResponseEmitter.php` | Backend | PSR-7 response emission |
| 4.4 | Router | `shared/src/Router/Router.php` | Backend | Attribute-based routing |
| 4.5 | Route Collector | `shared/src/Router/RouteCollector.php` | Backend | Route registration |
| 4.6 | Route Dispatcher | `shared/src/Router/RouteDispatcher.php` | Backend | Request → Handler matching |
| 4.7 | Route Attributes | `shared/src/Router/Attributes/Route.php`, `Middleware.php`, `Group.php` | Backend | PHP 8 attributes |

**Bağımlılık:** Gün 3 (middleware)
**Çıktı:** Çalışan HTTP kernel + router

### 3.5 Gün 5: Veritabanı Şemaları (18 BCNF)

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 5.1 | coremusic_auth | `.sql/mysql/coremusic_auth.sql` | Data | 13 tablo, BCNF |
| 5.2 | coremusic_user | `.sql/mysql/coremusic_user.sql` | Data | 7 tablo, BCNF |
| 5.3 | coremusic_musics | `.sql/mysql/coremusic_musics.sql` | Data | 22 tablo, BCNF |
| 5.4 | coremusic_albums | `.sql/mysql/coremusic_albums.sql` | Data | 5 tablo, BCNF |
| 5.5 | coremusic_playlist | `.sql/mysql/coremusic_playlist.sql` | Data | 5 tablo, BCNF |
| 5.6 | coremusic_catalog | `.sql/mysql/coremusic_catalog.sql` | Data | 8 tablo, BCNF |
| 5.7 | coremusic_logs | `.sql/mysql/coremusic_logs.sql` | Data | 22 tablo, BCNF |
| 5.8 | coremusic_media | `.sql/mysql/coremusic_media.sql` | Data | 8 tablo, BCNF |
| 5.9 | coremusic_system | `.sql/mysql/coremusic_system.sql` | Data | 17 tablo, BCNF |
| 5.10 | coremusic_social | `.sql/mysql/coremusic_social.sql` | Data | 9 tablo, BCNF |
| 5.11 | coremusic_wireless | `.sql/mysql/coremusic_wireless.sql` | Data | 5 tablo, BCNF |
| 5.12 | coremusic_ai | `.sql/mysql/coremusic_ai.sql` | Data | 6 tablo, BCNF |
| 5.13 | coremusic_api | `.sql/mysql/coremusic_api.sql` | Data | 4 tablo, BCNF |
| 5.14 | coremusic_cms | `.sql/mysql/coremusic_cms.sql` | Data | 8 tablo, BCNF |
| 5.15 | coremusic_download | `.sql/mysql/coremusic_download.sql` | Data | 4 tablo, BCNF |
| 5.16 | coremusic_neva | `.sql/mysql/coremusic_neva.sql` | Data | 4 tablo, BCNF |
| 5.17 | coremusic_studio | `.sql/mysql/coremusic_studio.sql` | Data | 6 tablo, BCNF |
| 5.18 | coremusic_patch | `.sql/mysql/coremusic_patch.sql` | Data | 3 tablo, BCNF |

**Bağımlılık:** Yok (bağımsız)
**Çıktı:** 18 BCNF şeması, 156 tablo

### 3.6 Gün 6: Auth Infrastructure

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 6.1 | PdoUserRepository | `shared/src/Auth/Infrastructure/Repository/PdoUserRepository.php` | Data | Prepared statement, no ORM |
| 6.2 | PdoSessionRepository | `shared/src/Auth/Infrastructure/Repository/PdoSessionRepository.php` | Data | Session persistence |
| 6.3 | Argon2id Password Hasher | `shared/src/Auth/Infrastructure/Security/Argon2idPasswordHasher.php` | Security | 64MB, t=4, p=2 |
| 6.4 | JWT Token Manager | `shared/src/Auth/Infrastructure/Security/JwtTokenManager.php` | Security | RS256, 15min access, 7d refresh |
| 6.5 | CSRF Token Manager | `shared/src/Auth/Infrastructure/Security/CsrfTokenManager.php` | Security | random_bytes(32), hash_equals |
| 6.6 | Session Manager | `shared/src/Auth/Infrastructure/Security/SessionManager.php` | Security | COREMUSIC_SESS cookie |

**Bağımlılık:** Gün 5 (DB şemaları) + Gün 2 (Auth domain)
**Çıktı:** Auth infrastructure katmanı

### 3.7 Gün 7: Auth Service Entry Points

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 7.1 | auth.coremusic.net entry point | `auth.coremusic.net/index.php` | Backend | Kernel bootstrap |
| 7.2 | LoginController | `auth.coremusic.net/include/Controller/LoginController.php` | Backend | POST /api/login |
| 7.3 | RegisterController | `auth.coremusic.net/include/Controller/RegisterController.php` | Backend | POST /api/register |
| 7.4 | LogoutController | `auth.coremusic.net/include/Controller/LogoutController.php` | Backend | POST /api/logout |
| 7.5 | TokenController | `auth.coremusic.net/include/Controller/TokenController.php` | Backend | POST /api/refresh |
| 7.6 | SessionCheckController | `auth.coremusic.net/include/Controller/SessionCheckController.php` | Backend | GET /api/session-check |
| 7.7 | Route definitions | `auth.coremusic.net/config/routes.php` | Backend | Tüm auth route'ları |
| 7.8 | Auth Use Cases | `shared/src/Auth/Application/UseCase/LoginUseCase.php`, vb. | Backend | Business logic |

**Bağımlılık:** Gün 6 (Auth infrastructure)
**Çıktı:** Çalışan auth servisi (login, register, logout, refresh, session-check)

### 3.8 Gün 8: DI Container & Config & Test

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 8.1 | DI Container | `shared/src/Container/ContainerFactory.php` | Backend | PSR-11 uyumlu |
| 8.2 | Service definitions | `shared/config/services.php` | Backend | Tüm servis bağımlılıkları |
| 8.3 | Middleware pipeline config | `shared/config/middleware.php` | Backend | Frozen order |
| 8.4 | CORS config | `shared/config/cors.php` | Security | *.coremusic.net whitelist |
| 8.5 | Auth unit test | `shared/tests/Unit/Auth/` | QA | ≥ %80 coverage |
| 8.6 | Middleware unit test | `shared/tests/Unit/Security/` | QA | ≥ %80 coverage |
| 8.7 | Auth integration test | `shared/tests/Integration/Auth/` | QA | Login→Logout akışı |

**Bağımlılık:** Gün 7 (Auth service)
**Çıktı:** Çalışan auth servisi + testler

---

## 4. Faz 2: Backend Servisler (Gün 9-16)

### 4.1 Gün 9: API Gateway

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 9.1 | API Gateway entry point | `api.coremusic.net/index.php` | Backend | Tek giriş noktası |
| 9.2 | Gateway middleware | `api.coremusic.net/include/Middleware/` | Backend | Correlation ID, service discovery |
| 9.3 | Route definitions | `api.coremusic.net/config/routes.php` | Backend | Tüm API route'ları |
| 9.4 | Response normalizer | `api.coremusic.net/include/Service/ResponseNormalizer.php` | Backend | Standart format |
| 9.5 | Service registry | `api.coremusic.net/config/services.php` | Backend | 7 servis kaydı |

**Bağımlılık:** Gün 8 (Shared library)
**Çıktı:** Çalışan API gateway

### 4.2 Gün 10: Media Service

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 10.1 | Media domain entities | `shared/src/Media/Domain/Entity/MediaFile.php`, vb. | Backend | Immutable entities |
| 10.2 | Media repository interface | `shared/src/Media/Domain/Repository/MediaRepositoryInterface.php` | Backend | Port |
| 10.3 | PdoMediaRepository | `shared/src/Media/Infrastructure/Repository/PdoMediaRepository.php` | Data | Prepared statement |
| 10.4 | MediaController | `media.coremusic.net/include/Controller/MediaController.php` | Backend | CRUD endpoints |
| 10.5 | StreamController | `media.coremusic.net/include/Controller/StreamController.php` | Backend | Range request support |
| 10.6 | MetadataController | `media.coremusic.net/include/Controller/MetadataController.php` | Backend | getID3 integration |
| 10.7 | Media routes | `media.coremusic.net/config/routes.php` | Backend | Tüm media route'ları |

**Bağımlılık:** Gün 9 (API Gateway) + Gün 5 (DB şemaları)
**Çıktı:** Media service (upload, stream, metadata)

### 4.3 Gün 11: Download Service

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 11.1 | Download domain entities | `shared/src/Download/Domain/Entity/DownloadJob.php`, vb. | Backend | Immutable |
| 11.2 | Download repository | `shared/src/Download/Infrastructure/Repository/PdoDownloadRepository.php` | Data | Queue persistence |
| 11.3 | deemix integration | `download.coremusic.net/src/services/deemix.ts` | Backend | Deezer FLAC download |
| 11.4 | yt-dlp integration | `download.coremusic.net/src/services/ytdlp.ts` | Backend | YouTube audio extraction |
| 11.5 | DownloadController | `download.coremusic.net/src/routes/download.ts` | Backend | start, status, cancel |
| 11.6 | Anti-ban system | `download.coremusic.net/src/services/antiBan.ts` | Security | Rate limiting, proxy rotation |

**Bağımlılık:** Gün 9 (API Gateway) + Gün 5 (DB şemaları)
**Çıktı:** Download service (Deezer + YouTube)

### 4.4 Gün 12: DI Container & Bootstrap All Services

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 12.1 | Container config (all services) | `shared/config/services.php` | Backend | Tüm servis bağımlılıkları |
| 12.2 | Route aggregation | `shared/config/routes.php` | Backend | Tüm route'lar |
| 12.3 | Bootstrap all subdomains | `auth.coremusic.net/index.php`, vb. | Backend | Her subdomain çalışır |
| 12.4 | Health check endpoints | Tüm servisler `/health` | Backend | 200 OK döner |
| 12.5 | Environment config | `shared/.env` | Security | Tüm secrets (gerçek değerler) |

**Bağımlılık:** Gün 10-11 (servisler)
**Çıktı:** 7 servis çalışır durumda

### 4.5 Gün 13: Frontend Altyapısı — CSS

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 13.1 | ITCSS layer dizinleri | `assets.coremusic.net/css/01_Abstracts/`...`09_ViewModes/` | UI | 9 katman |
| 13.2 | Design tokens | `assets.coremusic.net/css/01_Abstracts/_tokens.css` | UI | Renk, boşluk, tipografi |
| 13.3 | Reset/base | `assets.coremusic.net/css/02_Base/_reset.css`, `_base.css` | UI | Normalize + base |
| 13.4 | Layout tokens | `assets.coremusic.net/css/01_Abstracts/_layout-tokens.css` | UI | Breakpoint'ler, grid |
| 13.5 | Vendor layer | `assets.coremusic.net/css/07_Vendors/` | UI | Bootstrap minimal |
| 13.6 | Device CSS | `assets.coremusic.net/css/08_Devices/` | UI | 7 cihaz CSS'i |
| 13.7 | View mode CSS | `assets.coremusic.net/css/09_ViewModes/` | UI | 4 view mode |

**Bağımlılık:** Yok (bağımsız)
**Çıktı:** ITCSS 9-layer CSS mimarisi

### 4.6 Gün 14: Frontend Altyapısı — JS Core

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 14.1 | main.js entry point | `assets.coremusic.net/js/main.js` | UI | Router + tüm modüller |
| 14.2 | EventBus | `assets.coremusic.net/js/core/EventBus.js` | UI | Pub/sub, bağımsız |
| 14.3 | CoreMusicApp | `assets.coremusic.net/js/core/CoreMusicApp.js` | UI | Lifecycle manager |
| 14.4 | DeviceManager | `assets.coremusic.net/js/managers/DeviceManager.js` | UI | Cihaz tespiti |
| 14.5 | ThemeManager | `assets.coremusic.net/js/managers/ThemeManager.js` | UI | ADR-044 gender theme |
| 14.6 | ViewModeManager | `assets.coremusic.net/js/managers/ViewModeManager.js` | UI | ADR-045 view mode |
| 14.7 | device-loader.js | `assets.coremusic.net/js/device-loader.js` | UI | IIFE, non-module |

**Bağımlılık:** Gün 13 (CSS)
**Çıktı:** JS core modülleri

### 4.7 Gün 15: SPA Router

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 15.1 | Router.js | `assets.coremusic.net/js/router/Router.js` | UI | History API, pushState |
| 15.2 | GuardPipeline | `assets.coremusic.net/js/router/guards.js` | UI | AuthGuard, RoleGuard |
| 15.3 | DomPatcher | `assets.coremusic.net/js/router/DomPatcher.js` | UI | DOMParser + replaceChildren |
| 15.4 | CsrfManager | `assets.coremusic.net/js/CsrfManager.js` | UI | Meta tag + header |
| 15.5 | Route definitions | `assets.coremusic.net/js/router/routes.js` | UI | Tüm sayfa route'ları |
| 15.6 | ScrollManager | `assets.coremusic.net/js/features/ScrollManager.js` | UI | Route scroll restore |

**Bağımlılık:** Gün 14 (JS core)
**Çıktı:** Çalışan SPA router

### 4.8 Gün 16: Backend Test & Integration

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 16.1 | API Gateway test | `api.coremusic.net/tests/` | QA | Route matching, middleware |
| 16.2 | Media service test | `media.coremusic.net/tests/` | QA | Upload, stream, metadata |
| 16.3 | Download service test | `download.coremusic.net/tests/` | QA | Queue, status, cancel |
| 16.4 | Cross-service test | `shared/tests/Integration/` | QA | Auth→API→Media akışı |
| 16.5 | Security test | `shared/tests/Security/` | Security | CSRF, rate limit, headers |
| 16.6 | Performance baseline | — | QA | TTFB, API response time |

**Bağımlılık:** Gün 9-12 (backend servisler)
**Çıktı:** Backend test raporu (≥ %80 coverage)

---

## 5. Faz 3: Frontend (Gün 17-24)

### 5.1 Gün 17: Auth Sayfaları

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 17.1 | Login sayfası | `auth.coremusic.net/pages/login.php` | UI | Email + password form, CSRF |
| 17.2 | Register sayfası | `auth.coremusic.net/pages/register.php` | UI | Registration form, validation |
| 17.3 | Forgot password | `auth.coremusic.net/pages/reset-password.php` | UI | Password reset flow |
| 17.4 | Auth CSS | `assets.coremusic.net/css/05_Pages/_auth.css` | UI | BEM, responsive |
| 17.5 | Auth JS | `auth.coremusic.net/assets/js/auth.js` | UI | Form handling, validation |

**Bağımlılık:** Gün 15 (SPA Router) + Gün 7 (Auth service)
**Çıktı:** Çalışan auth sayfaları

### 5.2 Gün 18: Home Sayfası (Dashboard)

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 18.1 | Home layout | `home.coremusic.net/pages/home.php` | UI | Conditional rendering (3 layout) |
| 18.2 | Header | `home.coremusic.net/header.php` | UI | Phone/Embedded/TV/Desktop |
| 18.3 | Footer | `home.coremusic.net/footer.php` | UI | Player controls, seek slider |
| 18.4 | Home CSS | `assets.coremusic.net/css/05_Pages/_home.css` | UI | BEM, responsive |
| 18.5 | Widget grid | `home.coremusic.net/pages/home.php` | UI | Recently played, recommendations |
| 18.6 | Social row | `home.coremusic.net/pages/home.php` | UI | Social media icons |

**Bağımlılık:** Gün 17 (Auth) + Gün 13 (CSS)
**Çıktı:** Çalışan home sayfası

### 5.3 Gün 19: Music Sayfaları

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 19.1 | Albums page | `music.coremusic.net/pages/albums.php` | UI | Album grid, cover art, pagination |
| 19.2 | Artists page | `music.coremusic.net/pages/artists.php` | UI | Artist list, bio, discography |
| 19.3 | Playlist page | `music.coremusic.net/pages/playlists.php` | UI | Playlist management |
| 19.4 | Song detail | `music.coremusic.net/pages/song.php` | UI | Lyrics, metadata, player |
| 19.5 | Music CSS | `assets.coremusic.net/css/05_Pages/_music.css` | UI | BEM, responsive |
| 19.6 | Music JS | `music.coremusic.net/assets/js/music.js` | UI | Data loading, filtering |

**Bağımlılık:** Gün 18 (Home) + Gün 10 (Media service)
**Çıktı:** Çalışan music sayfaları

### 5.4 Gün 20: Player Component

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 20.1 | PlayerController | `assets.coremusic.net/js/features/PlayerController.js` | UI | State machine (STOPPED/PLAYING/PAUSED) |
| 20.2 | Player UI | `assets.coremusic.net/css/04_Components/_player.css` | UI | Play/pause, seek, volume |
| 20.3 | Queue manager | `assets.coremusic.net/js/features/QueueManager.js` | UI | Play queue management |
| 20.4 | Volume control | `assets.coremusic.net/js/features/VolumeControl.js` | UI | Mute, volume slider |
| 20.5 | Seek slider | `assets.coremusic.net/js/features/SeekSlider.js` | UI | Time display, seek |

**Bağımlılık:** Gün 19 (Music) + Gün 14 (JS core)
**Çıktı:** Çalışan player component

### 5.5 Gün 21: Admin Sayfaları

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 21.1 | Admin dashboard | `admin.coremusic.net/pages/index.php` | UI | System stats, user count |
| 21.2 | User management | `admin.coremusic.net/pages/users.php` | UI | List, edit, disable, roles |
| 21.3 | System settings | `admin.coremusic.net/pages/system.php` | UI | Config editor, feature flags |
| 21.4 | Admin CSS | `assets.coremusic.net/css/05_Pages/_admin.css` | UI | BEM, responsive |
| 21.5 | Admin JS | `admin.coremusic.net/assets/js/admin.js` | UI | CRUD operations |

**Bağımlılık:** Gün 17 (Auth) + Gün 8 (RBAC)
**Çıktı:** Çalışan admin sayfaları

### 5.6 Gün 22: Embedded & Device Sayfaları

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 22.1 | Car page | `car.coremusic.net/pages/index.php` | UI | Minimal, touch-friendly |
| 22.2 | Studio page | `studio.coremusic.net/pages/index.php` | UI | Recording, monitoring |
| 22.3 | Pro page | `pro.coremusic.net/pages/index.php` | UI | EQ, analysis |
| 22.4 | Device CSS | `assets.coremusic.net/css/08_Devices/_embedded.css` | UI | RPi5 1024×600 |
| 22.5 | Touch manager | `assets.coremusic.net/js/features/TouchManager.js` | UI | Embedded touch gestures |

**Bağımlılık:** Gün 18 (Home) + Gün 13 (CSS)
**Çıktı:** Çalışan embedded sayfaları

### 5.7 Gün 23: Search & Global Components

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 23.1 | Search component | `assets.coremusic.net/js/features/SearchComponent.js` | UI | Global search, filters |
| 23.2 | CardManager | `assets.coremusic.net/js/features/CardManager.js` | UI | Event delegation |
| 23.3 | Pagination | `assets.coremusic.net/js/features/Pagination.js` | UI | Infinite scroll / page |
| 23.4 | Modal | `assets.coremusic.net/css/04_Components/_modal.css` | UI | Accessible modal |
| 23.5 | Toast notifications | `assets.coremusic.net/js/features/ToastManager.js` | UI | In-app notifications |

**Bağımlılık:** Gün 19 (Music) + Gün 14 (JS core)
**Çıktı:** Global bileşenler

### 5.8 Gün 24: Frontend Test & Accessibility

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 24.1 | SPA Router test | `assets.coremusic.net/js/tests/` | QA | Route matching, guards |
| 24.2 | Player test | `assets.coremusic.net/js/tests/` | QA | State machine, controls |
| 24.3 | Accessibility audit | WCAG 2.2 AA | QA | Keyboard nav, screen reader |
| 24.4 | Responsive test | 5 cihaz | QA | Phone, tablet, laptop, desktop, 4K |
| 24.5 | Performance test | Core Web Vitals | QA | LCP, INP, CLS |
| 24.6 | Cross-browser test | Chrome, Firefox, Safari | QA | Uyumlu |

**Bağımlılık:** Gün 17-23 (tüm frontend)
**Çıktı:** Frontend test raporu

---

## 6. Faz 4: Entegrasyon (Gün 25-32)

### 6.1 Gün 25: Cross-Domain Auth (SSO)

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 25.1 | Cookie-based SSO | Tüm subdomain'ler | Security | COREMUSIC_SESS *.coremusic.net |
| 25.2 | Session validation API | `auth.coremusic.net/api/session` | Backend | Check session validity |
| 25.3 | Device registration | `auth.coremusic.net/api/device` | Backend | Register, authenticate |
| 25.4 | CORS config (all) | Tüm servisler | Security | Proper origin whitelist |

**Bağımlılık:** Gün 8 (Auth) + Gün 12 (tüm servisler)
**Çıktı:** Cross-domain SSO çalışıyor

### 6.2 Gün 26: WebSocket Altyapısı

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 26.1 | WebSocket server | `packages/websocket/` | Backend | PSR-7 compatible, channel-based |
| 26.2 | Player state sync | `audio.coremusic.net/ws/` | Backend | Real-time playback state |
| 26.3 | Download progress | `download.coremusic.net/ws/` | Backend | Progress updates per job |
| 26.4 | Notification system | `packages/notifications/` | Backend | Push notifications, in-app |

**Bağımlılık:** Gün 12 (servisler) + Gün 20 (Player)
**Çıktı:** WebSocket altyapısı

### 6.3 Gün 27: Event System & CQRS

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 27.1 | Event dispatcher | `packages/events/` | Backend | PSR-14 compatible |
| 27.2 | Domain events | Tüm domain'ler | Backend | UserRegistered, SongAdded, vb. |
| 27.3 | Command handlers | Tüm servisler | Backend | CQRS pattern |
| 27.4 | Query handlers | Tüm servisler | Backend | Read model |
| 27.5 | Event store | `packages/events/` | Backend | Event sourcing for audit |

**Bağımlılık:** Gün 12 (servisler)
**Çıktı:** Event driven architecture çalışıyor

### 6.4 Gün 28: Real-Time Frontend

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 28.1 | WebSocket client | `assets.coremusic.net/js/core/WebSocketClient.js` | UI | Auto-reconnect, channels |
| 28.2 | Real-time player | `assets.coremusic.net/js/features/RealTimePlayer.js` | UI | Multi-device sync |
| 28.3 | Download progress UI | `assets.coremusic.net/js/features/DownloadProgress.js` | UI | Progress bar, status |
| 28.4 | Notification UI | `assets.coremusic.net/js/features/NotificationToast.js` | UI | In-app notifications |

**Bağımlılık:** Gün 26 (WebSocket) + Gün 20 (Player)
**Çıktı:** Real-time frontend özellikleri

### 6.5 Gün 29: Download Service Entegrasyonu

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 29.1 | Download page | `download.coremusic.net/pages/index.php` | UI | Queue view, history |
| 29.2 | Download JS | `download.coremusic.net/assets/js/download.js` | UI | Start, status, cancel |
| 29.3 | Download CSS | `assets.coremusic.net/css/05_Pages/_download.css` | UI | BEM, responsive |
| 29.4 | Deezer integration test | — | QA | FLAC download works |
| 29.5 | YouTube integration test | — | QA | Audio extraction works |

**Bağımlılık:** Gün 11 (Download service) + Gün 26 (WebSocket)
**Çıktı:** Çalışan download servisi

### 6.6 Gün 30: Media Service Entegrasyonu

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 30.1 | Media upload flow | `media.coremusic.net/` | Backend | File upload + metadata |
| 30.2 | Media streaming | `media.coremusic.net/stream/` | Backend | Range request, auth-gated |
| 30.3 | Transcoding | `media.coremusic.net/` | Backend | FFmpeg, FLAC→MP3 |
| 30.4 | Metadata extraction | `media.coremusic.net/` | Backend | getID3, ID3/Vorbis |
| 30.5 | Media test | — | QA | Upload→Stream→Metadata |

**Bağımlılık:** Gün 10 (Media service) + Gün 28 (Real-time)
**Çıktı:** Çalışan media servisi

### 6.7 Gün 31: AI Service Stub

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 31.1 | AI service entry point | `ai.coremusic.net/index.php` | Backend | Stub endpoints |
| 31.2 | Recommendation interface | `ai.coremusic.net/api/recommendations` | Backend | GET /api/recommendations |
| 31.3 | Preference profile | `ai.coremusic.net/api/preferences` | Backend | User preference CRUD |
| 31.4 | AI DB schema | `.sql/mysql/coremusic_ai.sql` | Data | 6 tablo, BCNF |

**Bağımlılık:** Gün 12 (servisler) + Gün 5 (DB)
**Çıktı:** AI service stub

### 6.8 Gün 32: Integration Test & Debug

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 32.1 | E2E auth flow | — | QA | Login→Dashboard→Logout |
| 32.2 | E2E music flow | — | QA | Search→Play→Queue |
| 32.3 | E2E download flow | — | QA | Start→Progress→Complete |
| 32.4 | E2E admin flow | — | QA | User management CRUD |
| 32.5 | Cross-domain test | — | QA | SSO across subdomains |
| 32.6 | Performance test | — | QA | Load test, stress test |
| 32.7 | Security scan | — | Security | OWASP checklist |

**Bağımlılık:** Gün 25-31 (tüm entegrasyon)
**Çıktı:** Entegrasyon test raporu

---

## 7. Faz 5: Üretim Hazırlığı (Gün 33-40)

### 7.1 Gün 33: CI/CD Pipeline

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 33.1 | GitHub Actions workflow | `.github/workflows/ci.yml` | DevOps | Lint, test, security scan |
| 33.2 | PHPStan config | `phpstan.neon` | Backend | Level 8 |
| 33.3 | PHP-CS-Fixer config | `.php-cs-fixer.php` | Backend | PSR-12 |
| 33.4 | PHPUnit config | `phpunit.xml` | QA | Coverage ≥ %80 |
| 33.5 | GitLeaks pre-commit | `.github/workflows/security.yml` | Security | Secret scanning |

**Bağımlılık:** Gün 32 (testler)
**Çıktı:** CI/CD pipeline çalışıyor

### 7.2 Gün 34: Monitoring & Logging

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 34.1 | PSR-3 logger | `packages/logger/` | Backend | Monolog, structured JSON |
| 34.2 | Health dashboard | `packages/monitoring/` | DevOps | Prometheus metrics |
| 34.3 | Error tracking | — | Backend | Error aggregation |
| 34.4 | Performance metrics | — | DevOps | TTFB, API response time |
| 34.5 | Alert rules | — | DevOps | Critical alert triggers |

**Bağımlılık:** Gün 33 (CI/CD)
**Çıktı:** Monitoring altyapısı

### 7.3 Gün 35: Security Hardening

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 35.1 | OWASP Top 10 audit | — | Security | Tam liste kontrol |
| 35.2 | CSP report endpoint | — | Security | Violation reports |
| 35.3 | Rate limit tuning | — | Security | Per-endpoint limits |
| 35.4 | Session hardening | — | Security | Rotation, timeout |
| 35.5 | CORS final check | — | Security | All origins verified |

**Bağımlılık:** Gün 32 (security scan)
**Çıktı:** Security hardening tamamlandı

### 7.4 Gün 36: Performance Optimization

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 36.1 | Query optimization | — | Data | Slow query fixes |
| 36.2 | Cache tuning | — | Backend | APCu hit rate ≥ %90 |
| 36.3 | Asset optimization | — | UI | CSS/JS minification |
| 36.4 | Image optimization | — | UI | WebP, lazy loading |
| 36.5 | TTFB validation | — | QA | < 200ms |

**Bağımlılık:** Gün 34 (monitoring)
**Çıktı:** Performance metrikleri hedeflerde

### 7.5 Gün 37: Deployment Configuration

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 37.1 | Apache vhost configs | — | DevOps | Tüm subdomain'ler |
| 37.2 | PHP-FPM config | — | DevOps | Pool ayarları |
| 37.3 | SSL certificates | — | DevOps | Let's Encrypt |
| 37.4 | DNS records | — | DevOps | A/CNAME records |
| 37.5 | Deployment script | — | DevOps | Zero-downtime capable |

**Bağımlılık:** Gün 35 (security) + Gün 36 (performance)
**Çıktı:** Deployment konfigürasyonu hazır

### 7.6 Gün 38: Documentation

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 38.1 | README.md | `README.md` | Backend | Setup instructions |
| 38.2 | API documentation | `api.coremusic.net/docs/` | Backend | OpenAPI 3.1 spec |
| 38.3 | Architecture docs | `.ai/architecture/` | MO | Tüm mimari güncellendi |
| 38.4 | Contributing guide | `CONTRIBUTING.md` | Backend | Development workflow |
| 38.5 | Deployment guide | `DEPLOYMENT.md` | DevOps | Step-by-step deploy |

**Bağımlılık:** Gün 37 (deployment)
**Çıktı:** Tam dokümantasyon

### 7.7 Gün 39: Final Testing & QA

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 39.1 | Full regression test | — | QA | Tüm testler geçiyor |
| 39.2 | E2E smoke test | — | QA | Critical path'ler |
| 39.3 | Security final scan | — | Security | Sıfır kritik açık |
| 39.4 | Performance final | — | QA | Tüm metrikler hedeflerde |
| 39.5 | Accessibility final | — | QA | WCAG 2.2 AA |
| 39.6 | Cross-browser final | — | QA | Chrome, Firefox, Safari |

**Bağımlılık:** Gün 33-38 (tüm hazırlıklar)
**Çıktı:** Final test raporu

### 7.8 Gün 40: Launch & Post-Launch

| # | Görev | Dosyalar | Sorumlu | Kabul Kriteri |
|---|-------|----------|---------|----------------|
| 40.1 | Production deploy | — | DevOps | Zero-downtime deploy |
| 40.2 | Health check verification | — | DevOps | All services healthy |
| 40.3 | Smoke test (production) | — | QA | Critical flows work |
| 40.4 | Monitoring verification | — | DevOps | Metrics flowing |
| 40.5 | Rollback plan | — | DevOps | Tested rollback procedure |
| 40.6 | Post-launch review | — | Tüm | Retrospective |

**Bağımlılık:** Gün 39 (final test)
**Çıktı:** Production-ready CoreMusic platformu

---

## 8. Bağımlılık Grafisi

```
Gün 1  ──── Proje Yapısı
              │
Gün 2  ──── Auth Domain
              │
Gün 3  ──── Security Middleware
              │
Gün 4  ──── HTTP Kernel & Router
              │
Gün 5  ──── DB Şemaları (18 BCNF) ──────────────────┐
              │                                        │
Gün 6  ──── Auth Infrastructure ─────────────────────┤
              │                                        │
Gün 7  ──── Auth Service ────────────────────────────┤
              │                                        │
Gün 8  ──── DI Container & Config ──────────────────┤
              │                                        │
Gün 9  ──── API Gateway ◄────────────────────────────┤
              │                                        │
Gün 10 ──── Media Service ◄─────────────────────────┤
              │                                        │
Gün 11 ──── Download Service ◄──────────────────────┤
              │                                        │
Gün 12 ──── Bootstrap All Services ◄────────────────┤
              │                                        │
Gün 13 ──── CSS Architecture (bağımsız) ────────────┤
              │                                        │
Gün 14 ──── JS Core ◄───────────────────────────────┤
              │                                        │
Gün 15 ──── SPA Router ◄────────────────────────────┤
              │                                        │
Gün 16 ──── Backend Test ◄──────────────────────────┘
              │
Gün 17 ──── Auth Pages ◄─── Gün 7 + Gün 15
              │
Gün 18 ──── Home Page ◄─── Gün 17 + Gün 13
              │
Gün 19 ──── Music Pages ◄─── Gün 18 + Gün 10
              │
Gün 20 ──── Player Component ◄─── Gün 19 + Gün 14
              │
Gün 21 ──── Admin Pages ◄─── Gün 17 + Gün 8
              │
Gün 22 ──── Embedded Pages ◄─── Gün 18 + Gün 13
              │
Gün 23 ──── Search & Global ◄─── Gün 19 + Gün 14
              │
Gün 24 ──── Frontend Test
              │
Gün 25 ──── Cross-Domain SSO ◄─── Gün 8 + Gün 12
              │
Gün 26 ──── WebSocket ◄─── Gün 12 + Gün 20
              │
Gün 27 ──── Event System ◄─── Gün 12
              │
Gün 28 ──── Real-Time Frontend ◄─── Gün 26 + Gün 20
              │
Gün 29 ──── Download Integration ◄─── Gün 11 + Gün 26
              │
Gün 30 ──── Media Integration ◄─── Gün 10 + Gün 28
              │
Gün 31 ──── AI Service Stub ◄─── Gün 12 + Gün 5
              │
Gün 32 ──── Integration Test
              │
Gün 33 ──── CI/CD ◄─── Gün 32
              │
Gün 34 ──── Monitoring ◄─── Gün 33
              │
Gün 35 ──── Security Hardening ◄─── Gün 32
              │
Gün 36 ──── Performance ◄─── Gün 34
              │
Gün 37 ──── Deployment Config ◄─── Gün 35 + Gün 36
              │
Gün 38 ──── Documentation ◄─── Gün 37
              │
Gün 39 ──── Final Testing ◄─── Gün 33-38
              │
Gün 40 ──── Launch ◄─── Gün 39
```

---

## 9. Risk Matrisi

| # | Risk | Olasılık | Etki | Öncelik | Azaltma |
|---|------|----------|------|---------|---------|
| 1 | Auth bypass hatası | Orta | Yüksek | CRITICAL | OWASP audit, penetration test |
| 2 | SQL injection | Düşük | Yüksek | CRITICAL | Prepared statement, code review |
| 3 | CSRF exploit | Orta | Yüksek | CRITICAL | Token validation, SameSite cookie |
| 4 | Performance bottleneck | Yüksek | Orta | HIGH | Load testing, caching, CDN |
| 5 | Cross-domain SSO failure | Orta | Yüksek | CRITICAL | Integration test, fallback |
| 6 | WebSocket memory leak | Düşük | Orta | HIGH | Connection limits, monitoring |
| 7 | Download service abuse | Yüksek | Orta | HIGH | Rate limiting, anti-ban |
| 8 | CSP violation | Orta | Düşük | MEDIUM | Nonce testing, report endpoint |
| 9 | DB connection pool exhaustion | Düşük | Yüksek | HIGH | Connection limits, monitoring |
| 10 | Deployment failure | Düşük | Yüksek | CRITICAL | Rollback plan, blue/green |

---

## 10. Kalite Kapıları (Quality Gates)

| Faz | Kapı | Kriter | Sorumlu |
|-----|------|--------|---------|
| Faz 1 Sonu | Temel Altyapı Gate | Auth servisi çalışıyor, 18 DB şeması mevcut, ≥ %80 test | QA |
| Faz 2 Sonu | Backend Gate | 7 servis çalışıyor, API gateway çalışıyor, ≥ %80 test | QA |
| Faz 3 Sonu | Frontend Gate | 10 panel çalışıyor, responsive, accessibility ≥ AA | QA |
| Faz 4 Sonu | Entegrasyon Gate | Cross-domain SSO, WebSocket, real-time çalışıyor | QA |
| Faz 5 Sonu | Üretim Gate | CI/CD çalışıyor, monitoring aktif, security audit geçti | DevOps |

---

## 11. Sorumlu Ajanlar

| Agent | Görevler | Fazlar |
|-------|----------|--------|
| Backend Architect | Auth, API Gateway, Router, DI Container | 1-4 |
| UI Designer | CSS, JS, SPA Router, Components, Pages | 3-4 |
| Security Engineer | Middleware, CSRF, CSP, Rate Limit, Security Audit | 1-5 |
| Data Engineer | DB Schemas, Repositories, Migrations | 1-2 |
| QA Engineer | Unit Tests, Integration Tests, E2E, Performance | 1-5 |
| DevOps Engineer | CI/CD, Deployment, Monitoring, Infrastructure | 5 |
| Master Orchestrator | Coordination, Vault Updates, Documentation | 1-5 |

---

## 12. Kabul Kriterleri Özeti

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Test Coverage (Backend) | ≥ %80 | PHPUnit |
| Test Coverage (Frontend) | ≥ %80 | Vitest |
| TTFB | < 200ms | Load test |
| API Response Time | < 100ms | Load test |
| LCP | < 2.5s | Lighthouse |
| INP | < 200ms | Lighthouse |
| CLS | < 0.1 | Lighthouse |
| Security | OWASP Top 10:2025 | Audit |
| Accessibility | WCAG 2.2 AA | Manual + Automated |
| Browser Support | Chrome, Firefox, Safari | Manual test |
| Device Support | Phone, Tablet, Laptop, Desktop, 4K | Manual test |

---

## 13. Değişiklik Kaydı

| Versiyon | Tarih | Değişiklik |
|----------|-------|------------|
| 1.0.0 | 2026-08-16 | İlk oluşturulma — 40 günlük detaylı plan |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
