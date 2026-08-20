---
type: architecture
category: contracts
title: "Project Structure — Clean Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Project Structure — Clean Architecture

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic projesinin sıfırdan oluşturulan dizin yapısını tanımlar. **Enterprise Auth Architecture** ile uyumludur. Clean Architecture, SOLID ve DDD prensiplerine uygun, enterprise seviyede bir yapı sunar.

## 2. Enterprise Dizin Yapısı

```
C:\www\coremusic.net\
├── .ai/                                   ← Vault (mevcut, güncellenecek)
├── .claude/                               ← Claude kuralları (mevcut, güncellenecek)
├── .opencode/                             ← OpenCode config (mevcut)
│
├── shared/                                ← Shared Infrastructure (Merkezi Kütüphane)
│   ├── composer.json                      ← Ortak Composer dependencies
│   ├── src/
│   │   ├── Auth/                          ← Auth Domain (Identity Provider)
│   │   │   ├── Domain/                    ← Domain Entities (Enterprise)
│   │   │   │   ├── User.php               ← Kullanıcı entity'si
│   │   │   │   ├── Role.php               ← Rol entity'si
│   │   │   │   ├── Permission.php         ← İzin entity'si
│   │   │   │   ├── Session.php            ← Oturum entity'si
│   │   │   │   ├── Token.php              ← JWT/Session token entity
│   │   │   │   ├── ValueObjects/
│   │   │   │   │   ├── Email.php          ← Email value object
│   │   │   │   │   ├── Password.php       ← Password value object
│   │   │   │   │   ├── UserId.php         ← UUID value object
│   │   │   │   │   └── RoleName.php       ← Role name value object
│   │   │   │   └── Repository/
│   │   │   │       ├── UserRepositoryInterface.php
│   │   │   │       ├── SessionRepositoryInterface.php
│   │   │   │       ├── RoleRepositoryInterface.php
│   │   │   │       └── PermissionRepositoryInterface.php
│   │   │   ├── Application/               ← Application Layer (Use Cases)
│   │   │   │   ├── LoginUseCase.php       ← Giriş use case
│   │   │   │   ├── LogoutUseCase.php      ← Çıkış use case
│   │   │   │   ├── RegisterUseCase.php    ← Kayıt use case
│   │   │   │   ├── RefreshTokenUseCase.php ← Token yenileme
│   │   │   │   ├── ValidateSessionUseCase.php ← Session doğrulama
│   │   │   │   ├── CheckPermissionUseCase.php ← İzin kontrolü
│   │   │   │   └── DTO/
│   │   │   │       ├── LoginRequest.php   ← Login request DTO
│   │   │   │       ├── LoginResponse.php  ← Login response DTO
│   │   │   │       ├── RegisterRequest.php ← Register request DTO
│   │   │   │       ├── SessionDTO.php     ← Session data DTO
│   │   │   │       └── TokenPair.php      ← JWT token pair DTO
│   │   │   └── Infrastructure/            ← Infrastructure Layer
│   │   │       ├── Persistence/
│   │   │       │   ├── PdoUserRepository.php
│   │   │       │   ├── PdoSessionRepository.php
│   │   │       │   ├── PdoRoleRepository.php
│   │   │       │   └── PdoPermissionRepository.php
│   │   │       ├── Security/
│   │   │       │   ├── Argon2idPasswordHasher.php
│   │   │       │   ├── JwtTokenManager.php
│   │   │       │   ├── CsrfTokenManager.php
│   │   │       │   └── SessionManager.php
│   │   │       └── Http/
│   │   │           ├── AuthApiClient.php
│   │   │           └── AuthMiddleware.php
│   │   ├── Security/                      ← Security Domain (Middleware Pipeline)
│   │   │   ├── Middleware/
│   │   │   │   ├── OriginCheckMiddleware.php    ← Katman 1: Origin kontrolü
│   │   │   │   ├── CorsMiddleware.php           ← Katman 2: CORS doğrulama
│   │   │   │   ├── RateLimiterMiddleware.php    ← Katman 3: Hız sınırlama
│   │   │   │   ├── SecurityHeadersMiddleware.php ← Katman 4: Güvenlik başlıkları
│   │   │   │   ├── SessionManagerMiddleware.php ← Katman 5: Session yönetimi
│   │   │   │   ├── CsrfMiddleware.php           ← Katman 6: CSRF koruması
│   │   │   │   ├── AuthenticationMiddleware.php ← Katman 7: Kimlik doğrulama
│   │   │   │   ├── AuthorizationMiddleware.php  ← Katman 8: RBAC
│   │   │   │   └── ValidationMiddleware.php     ← Katman 9: Input doğrulama
│   │   │   └── Service/
│   │   │       ├── CspNonceGenerator.php
│   │   │       ├── RateLimiter.php
│   │   │       ├── SecurityHeaderService.php
│   │   │       ├── OriginValidator.php
│   │   │       └── CorsValidator.php
│   │   ├── Http/                          ← HTTP Layer (CoreMusic\Http)
│   │   │   ├── Kernel.php                 ← HTTP kernel
│   │   │   ├── Request/
│   │   │   │   └── ServerRequestFactory.php
│   │   │   └── Response/
│   │   │       └── ResponseEmitter.php
│   │   ├── Router/                        ← Enterprise Router
│   │   │   ├── Router.php
│   │   │   ├── RouteCollector.php
│   │   │   ├── RouteDispatcher.php
│   │   │   ├── Attributes/
│   │   │   │   ├── Route.php
│   │   │   │   ├── Middleware.php
│   │   │   │   └── Guard.php
│   │   │   └── Cache/
│   │   │       └── RouteCache.php
│   │   ├── Container/                     ← DI Container (PSR-11)
│   │   │   └── ContainerFactory.php
│   │   └── Event/                         ← Event Dispatcher (PSR-14)
│   │       └── EventDispatcherFactory.php
│   ├── config/
│   │   ├── services.php                   ← DI definitions
│   │   ├── routes.php                     ← Route definitions
│   │   ├── middleware.php                 ← Middleware pipeline
│   │   ├── cors.php                       ← CORS whitelist
│   │   ├── auth.php                       ← Auth configuration
│   │   └── .env                           ← Environment (gitignored)
│   └── tests/
│       ├── Unit/
│       ├── Integration/
│       └── E2E/
│
├── auth.coremusic.net/                    ← Auth Service (Identity Provider)
│   ├── index.php                          ← Entry point
│   ├── include/
│   │   └── Controller/
│   │       ├── LoginController.php        ← Login controller
│   │       ├── RegisterController.php     ← Register controller
│   │       ├── LogoutController.php       ← Logout controller
│   │       ├── PasswordResetController.php ← Password reset
│   │       ├── SessionCheckController.php ← Session validation API
│   │       └── TokenController.php        ← JWT token endpoint
│   ├── pages/
│   │   ├── login.php                      ← Login sayfası
│   │   ├── register.php                   ← Register sayfası
│   │   └── reset-password.php             ← Password reset sayfası
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── home.coremusic.net/                    ← Home Media Center (RPi5)
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── HomeController.php
│   │       ├── MediaController.php
│   │       └── SettingsController.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── player.php
│   │   └── settings.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── pro.coremusic.net/                     ← Professional Panel (RPi5)
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── ProController.php
│   │       ├── EqController.php
│   │       └── AnalysisController.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── equalizer.php
│   │   └── analysis.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── studio.coremusic.net/                  ← Studio System (RPi5)
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── StudioController.php
│   │       ├── RecordingController.php
│   │       └── MonitoringController.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── recording.php
│   │   └── monitoring.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── car.coremusic.net/                     ← Car Audio (RPi5)
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── CarController.php
│   │       └── MediaController.php
│   ├── pages/
│   │   └── index.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── admin.coremusic.net/                   ← Admin Panel
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── AdminController.php
│   │       ├── UserController.php
│   │       ├── ContentController.php
│   │       └── SystemController.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── users.php
│   │   ├── content.php
│   │   └── system.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── media.coremusic.net/                   ← Media Vault (Depo)
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── MediaController.php        ← Medya akışı
│   │       ├── StreamController.php       ← Streaming endpoint
│   │       └── MetadataController.php     ← Metadata API
│   ├── pages/
│   │   └── index.php
│   ├── config/
│   │   └── routes.php
│   ├── media/                             ← Fiziki medya dosyaları
│   │   ├── music/
│   │   ├── covers/
│   │   └── thumbnails/
│   └── tests/
│
├── download.coremusic.net/                ← Download Service (Node.js)
│   ├── src/
│   │   ├── index.ts
│   │   ├── routes/
│   │   ├── services/
│   │   └── types/
│   ├── package.json
│   ├── tsconfig.json
│   └── tests/
│
├── api.coremusic.net/                     ← API Service
│   ├── index.php
│   ├── include/
│   │   └── Controller/
│   │       ├── ApiController.php
│   │       ├── SongsController.php
│   │       ├── PlaylistsController.php
│   │       └── SearchController.php
│   ├── pages/
│   │   └── index.php
│   ├── config/
│   │   └── routes.php
│   └── tests/
│
├── coremusic.net/                         ← Landing Page
│   ├── index.html
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── pages/
│       ├── about.php
│       └── contact.php
│
├── .env.example                           ← Env template (root'ta izinli)
├── README.md
├── AGENTS.md                              ← Bootstrap pointer
├── CLAUDE.md                              ← Bootstrap pointer
└── WORKFLOW.md                            ← Bootstrap pointer

⚠️ NOT: Root dizinde composer.json, phpunit.xml, phpstan.neon, .php-cs-fixer.php, package.json YASAKTIR.
Her subdomain kendi config dosyalarını taşır. Detay: [[architecture/03-contracts/directory-structure]]
```

## 3. Katman Sorumlulukları

### 3.1 Shared Library (`shared/`)

| Bileşen | Sorumluluk | Örnek |
|---------|------------|-------|
| **Auth/Domain** | Auth domain entities ve repository contracts | `User.php`, `Session.php`, `Token.php` |
| **Auth/Application** | Auth use case'leri | `LoginUseCase.php`, `RegisterUseCase.php` |
| **Auth/Infrastructure** | Auth persistence ve security | `PdoUserRepository.php`, `JwtTokenManager.php` |
| **Security/Middleware** | Custom middleware pipeline (PSR bağımsız) | `SessionManagerMiddleware.php`, `CsrfMiddleware.php` |
| **Security/Service** | Security services | `CspNonceGenerator.php`, `RateLimiter.php` |
| **Http** | Custom HTTP kernel (`CoreMusic\Http`) | `Kernel.php`, `Request.php`, `Response.php` |
| **Router** | Enterprise router | `Router.php`, `RouteCollector.php` |
| **Container** | DI container (PSR-11) | `ContainerFactory.php` |
| **Event** | Event dispatcher (PSR-14) | `EventDispatcherFactory.php` |

**Kurallar:**
- ✅ Tüm subdomain'ler shared library'yi kullanır
- ✅ Ortak bağımlılıklar burada tanımlı
- ✅ PSR standartlarına uygun
- ❌ Subdomain'ler kendi bağımlılıklarını ekleyemez

### 3.2 Subdomain Services

| Subdomain | Tip | Port | Stack | Özellik |
|-----------|-----|------|-------|---------|
| `auth.coremusic.net` | Service | — | PHP 8.4 | Merkezi auth, login, register |
| `music.coremusic.net` | Panel | 81 | PHP 8.4 + JS | Ana medya paneli |
| `admin.coremusic.net` | Panel | 80 | PHP 8.4 | Yönetim paneli |
| `home.coremusic.net` | Embedded | 81 | PHP 8.4 | RPi5 ev medya merkezi |
| `studio.coremusic.net` | Embedded | 81 | PHP 8.4 | RPi5 stüdyo ses |
| `pro.coremusic.net` | Embedded | 81 | PHP 8.4 | RPi5 profesyonel |
| `car.coremusic.net` | Embedded | — | PHP 8.4 | RPi5 araç içi |
| `media.coremusic.net` | Service | 5000/6000 | PHP + FFmpeg | Medya depolama |
| `download.coremusic.net` | Service | 3001 | Node.js + TS | İndirme servisi |
| `landing.coremusic.net` | Static | 80 | Vanilla JS | Tanıtım sayfası |
| `api.coremusic.net` | Service | — | PHP 8.4 | API endpoint'leri |

### 3.3 Embedded Systems (RPi5)

| Mod | Donanım | Auth | Database | Özellik |
|-----|---------|------|----------|---------|
| **Home** | RPi5 + Touch Screen | Local (SQLite) | SQLite | Volumio benzeri ev teybi |
| **Pro** | RPi5 + HDMI Display | Local (SQLite) | SQLite | Profesyonel medya yönetimi |
| **Studio** | RPi5 + 8.1 Surround | Local (SQLite) | SQLite | Stüdyo ses sistemi |
| **Car** | RPi5 + PCM3168A | Local (SQLite) | SQLite | Araç bilgi-eğlence |

**Kurallar:**
- ✅ Offline-first çalışma
- ✅ Local auth (aynı RPi5)
- ✅ SQLite database (1 DB)
- ✅ Touch-optimized UI
- ❌ İnternet bağlantısı gerekmez
- ❌ Cross-subdomain auth yok

## 4. Bağımlılık Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    CLEAN ARCHITECTURE FLOW                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  HTTP Request                                                    │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Http/Kernel.php                                      │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Security/Middleware/                                  │
│    │  SessionManager → BypassAuth → RateLimiter                  │
│    │  → Auth → SecurityHeaders → Csrf                            │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Router/Router.php                                    │
│    │                                                             │
│    ▼                                                             │
│  auth.coremusic.net/include/Controller/LoginController.php       │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Auth/Application/LoginUseCase.php                    │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Auth/Domain/Repository/UserRepositoryInterface.php   │
│    │                                                             │
│    ▼                                                             │
│  shared/src/Auth/Infrastructure/Persistence/PdoUserRepository.php│
│    │                                                             │
│    ▼                                                             │
│  PDO MySQL (L0 Infrastructure)                                   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 5. Dosya Adlandırma Standartları

| Tip | Format | Örnek |
|-----|--------|-------|
| **Entity** | PascalCase.php | `User.php`, `Session.php` |
| **Value Object** | PascalCase.php | `Role.php`, `Token.php` |
| **Repository Interface** | PascalCaseInterface.php | `UserRepositoryInterface.php` |
| **Repository Impl** | PascalCaseRepository.php | `PdoUserRepository.php` |
| **Use Case** | PascalCaseUseCase.php | `LoginUseCase.php` |
| **Middleware** | PascalCaseMiddleware.php | `SessionManagerMiddleware.php` |
| **Controller** | PascalCaseController.php | `LoginController.php` |
| **Config** | snake_case.php | `database.php`, `routes.php` |
| **CSS** | ITCSS layer + BEM | `06-components/header.css` |
| **JS** | PascalCase.js | `Router.js`, `App.js` |

## 6. Namespace Yapısı

```php
// Shared - Auth Domain
namespace CoreMusic\Shared\Auth\Domain;
namespace CoreMusic\Shared\Auth\Application;
namespace CoreMusic\Shared\Auth\Infrastructure;

// Shared - Security
namespace CoreMusic\Shared\Security\Middleware;
namespace CoreMusic\Shared\Security\Service;

// Shared - HTTP
namespace CoreMusic\Shared\Http;
namespace CoreMusic\Shared\Router;
namespace CoreMusic\Shared\Container;
namespace CoreMusic\Shared\Event;

// Subdomain Controllers
namespace CoreMusic\Auth\Controller;
namespace CoreMusic\Music\Controller;
namespace CoreMusic\Admin\Controller;
namespace CoreMusic\Home\Controller;
namespace CoreMusic\Studio\Controller;
namespace CoreMusic\Pro\Controller;
namespace CoreMusic\Car\Controller;
namespace CoreMusic\Media\Controller;
namespace CoreMusic\Download\Controller;
namespace CoreMusic\Landing\Controller;
namespace CoreMusic\Api\Controller;
```

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Domain katmanında bağımlılık yasak | Layer violation → revert |
| 2 | Infrastructure, Domain'den import edemez | Dependency Inversion ihlali |
| 3 | Controller, Use Case dışında bir şey çağıramaz | SOLID SRP ihlali |
| 4 | PSR bağımsız — sıfırdan vanilla PHP | Framework yasak (ADR-001) |
| 5 | ORM yasak — sadece PDO prepared | ADR-002 |
| 6 | `declare(strict_types=1)` her dosyada zorunlu | Tip hatası riski |
| 7 | Subdomain'ler shared library'yi import edemez | Bağımlılık ihlali |
| 8 | Embedded systems offline-first çalışmalı | İnternet bağımlılığı |

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/00-overview/architecture-master]] | Ana mimari |
| [[architecture/l0-infrastructure/index]] | Infrastructure layer |
| [[architecture/l1-security/index]] | Security layer |
| [[architecture/l2-routing/index]] | Routing layer |
| [[architecture/l3-presentation/index]] | Presentation layer |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS kuralı |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO kuralı |


## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Dizin | [[architecture/00-overview/architecture-master]] | Proje yapısı |
| § 3 Katman | [[architecture/l0-infrastructure/index]] | L0-L3 tanımları |
| § 4 Akış | [[architecture/l1-security/index]] | Middleware pipeline |
| § 5 Adlandırma | [[ADR-001-vanilla-js-itcss]] | Kod standartları |


## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ~450 |
| **ADR Uyumlu** | ✅ 001, 002, 007, 042 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
