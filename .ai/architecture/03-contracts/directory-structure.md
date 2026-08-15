---
type: architecture
category: contracts
title: "Directory Structure — Multi-Project Root Architecture"
date: 2026-08-12
updated: 2026-08-12
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Directory Structure — Multi-Project Root Architecture

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic proje kök dizinindeki doğru dizin yapısını tanımlar. **C:\www\coremusic.net tek bir domain değil, TÜM domain'lerin kök dizinidir.** Her subdomain bağımsız bir projedir ve kendi `composer.json`'una sahiptir.

## 2. Temel Kural

> **C:\www\coremusic.net = ROOT DIRECTORY (tüm subdomain'ler için kök)**
>
> **Her subdomain = Bağımsız proje (kendi composer.json, vendor/, config/)**
>
> **shared/ = Ortak kütüphane (tüm subdomain'ler tarafından kullanılır)**
>
> **Root'ta composer.json, phpunit.xml, phpstan.neon YASAKTIR**

## 3. Kök Dizin Yapısı

```
C:\www\coremusic.net\
├── .ai/                                   ← Vault (AI talimatları)
├── .claude/                               ← Claude kuralları
├── .opencode/                             ← OpenCode config
├── .env.example                           ← Env template (root'ta izinli)
│
├── shared/                                ← Ortak Kütüphane (composer path repository)
│   ├── composer.json                      ← Ortak bağımlılıklar
│   ├── src/
│   │   ├── Auth/                          ← Auth Domain
│   │   ├── Security/                      ← Middleware Pipeline
│   │   ├── Http/                          ← HTTP Kernel
│   │   ├── Router/                        ← Enterprise Router
│   │   ├── Container/                     ← DI Container
│   │   └── Event/                         ← Event Dispatcher
│   ├── config/
│   │   ├── services.php
│   │   ├── middleware.php
│   │   ├── cors.php
│   │   └── auth.php
│   └── tests/
│
├── auth.coremusic.net/                    ← Auth Service (bağımsız proje)
│   ├── index.php                          ← Entry point
│   ├── .htaccess                          ← Apache rewrite
│   ├── composer.json                      ← PROJE-specific bağımlılıklar
│   ├── vendor/                            ← PROJE-specific vendor
│   ├── include/
│   │   └── Controller/
│   │       ├── LoginController.php
│   │       ├── RegisterController.php
│   │       ├── LogoutController.php
│   │       ├── PasswordResetController.php
│   │       ├── SessionCheckController.php
│   │       └── TokenController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── reset-password.php
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── music.coremusic.net/                   ← Music Panel (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── MusicController.php
│   │       ├── PlayerController.php
│   │       ├── SearchController.php
│   │       └── LibraryController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── player.php
│   │   ├── search.php
│   │   └── library.php
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css
│   │   │   ├── 01_Abstracts/
│   │   │   ├── 02_Base/
│   │   │   ├── 03_Layout/
│   │   │   ├── 04_Components/
│   │   │   ├── 05_Pages/
│   │   │   ├── 06_Utilities/
│   │   │   ├── 07_Vendors/
│   │   │   ├── 08_Devices/
│   │   │   └── 09_ViewModes/
│   │   ├── js/
│   │   │   ├── Router.js
│   │   │   ├── ThemeManager.js
│   │   │   └── device-loader.js
│   │   └── images/
│   └── tests/
│
├── admin.coremusic.net/                   ← Admin Panel (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── AdminController.php
│   │       ├── UserController.php
│   │       ├── ContentController.php
│   │       └── SystemController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── users.php
│   │   ├── content.php
│   │   └── system.php
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── home.coremusic.net/                    ← Home Media Center (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── HomeController.php
│   │       ├── MediaController.php
│   │       └── SettingsController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   │   ├── index.php
│   │   ├── player.php
│   │   └── settings.php
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── pro.coremusic.net/                     ← Professional Panel (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── ProController.php
│   │       ├── EqController.php
│   │       └── AnalysisController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── studio.coremusic.net/                  ← Studio System (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── StudioController.php
│   │       ├── RecordingController.php
│   │       └── MonitoringController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── car.coremusic.net/                     ← Car Audio (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── CarController.php
│   │       └── MediaController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── media.coremusic.net/                   ← Media Vault (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── MediaController.php
│   │       ├── StreamController.php
│   │       └── MetadataController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   ├── media/                             ← Fiziki medya dosyaları
│   │   ├── music/
│   │   ├── covers/
│   │   └── thumbnails/
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── api.coremusic.net/                     ← API Service (bağımsız proje)
│   ├── index.php
│   ├── .htaccess
│   ├── composer.json
│   ├── vendor/
│   ├── include/
│   │   └── Controller/
│   │       ├── ApiController.php
│   │       ├── SongsController.php
│   │       ├── PlaylistsController.php
│   │       └── SearchController.php
│   ├── config/
│   │   ├── routes.php
│   │   ├── config.php
│   │   └── container.php
│   ├── pages/
│   │   └── index.php
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── tests/
│
├── download.coremusic.net/                ← Download Service (Node.js bağımsız proje)
│   ├── src/
│   │   ├── index.ts
│   │   ├── routes/
│   │   ├── services/
│   │   └── types/
│   ├── package.json
│   ├── tsconfig.json
│   └── tests/
│
├── coremusic.net/                         ← Landing Page (bağımsız proje)
│   ├── index.html
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── pages/
│       ├── about.php
│       └── contact.php
│
└── README.md                              ← Root README (root'ta izinli)
```

## 4. Root Dizin Yasakları

| Dosya | Root'ta YASAK mı? | Nerede Olmalı? |
|-------|-------------------|----------------|
| `composer.json` | YASAK (root'ta) | Her subdomain kendi composer.json'unu taşır |
| `phpunit.xml` | YASAK (root'ta) | Her subdomain kendi phpunit.xml'ini taşır |
| `phpstan.neon` | YASAK (root'ta) | Her subdomain kendi phpstan.neon'unu taşır |
| `.php-cs-fixer.php` | YASAK (root'ta) | Her subdomain kendi config'ini taşır |
| `package.json` | YASAK (root'ta) | Sadece `download.coremusic.net/` ve `coremusic.net/` |
| `vendor/` | YASAK (root'ta) | Her subdomain kendi vendor'unu taşır |
| `.env` | İZİNLI | Root'ta .env.example izinli, .env gitignored |

**Neden?** Her subdomain bağımsız bir projedir. Kendi bağımlılıklarını, testlerini ve config'lerini yönetir. Root'ta config dosyası bırakmak, multi-project yapısını bozar.

## 5. Subdomain Proje Yapısı (Standart Şablon)

Her PHP subdomain şu standart yapıyı takip eder:

```
<subdomain>.coremusic.net/
├── index.php                    ← Entry point (Router'ı başlatır)
├── .htaccess                    ← Apache URL rewrite kuralları
├── composer.json                ← PROJE-specific bağımlılıklar
├── vendor/                      ← PROJE-specific Composer vendor
├── include/
│   └── Controller/              ← Controller sınıfları
│       └── *Controller.php
├── config/
│   ├── routes.php               ← Route tanımları
│   ├── config.php               ← Proje konfigürasyonu
│   └── container.php            ← DI container tanımı
├── pages/                       ← PHP template dosyaları
│   └── *.php
├── assets/
│   ├── css/                     ← CSS dosyaları
│   ├── js/                      ← JavaScript dosyaları
│   └── images/                  ← Görseller
└── tests/                       ← Test dosyaları
    ├── Unit/
    └── Integration/
```

## 6. Subdomain composer.json Örneği

```json
{
    "name": "coremusic/auth-coremusic-net",
    "description": "CoreMusic Auth Service",
    "type": "project",
    "require": {
        "php": ">=8.4",
        "coremusic/shared": "*"
    },
    "autoload": {
        "psr-4": {
            "CoreMusic\\Auth\\": "include/"
        }
    },
    "repositories": [
        {
            "type": "path",
            "url": "../shared",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

**Kritik Not:** `coremusic/shared` paketi `path repository` ile linklenir. Development'ta symlink kullanılır, production'da publish edilir.

## 7. Shared Library Yapısı

```
shared/
├── composer.json                  ← Paket tanımı (coremusic/shared)
├── src/
│   ├── Auth/                      ← Auth Domain Logic
│   │   ├── Domain/
│   │   │   ├── Entity/
│   │   │   │   ├── User.php
│   │   │   │   ├── Role.php
│   │   │   │   ├── Permission.php
│   │   │   │   ├── Session.php
│   │   │   │   └── Token.php
│   │   │   ├── ValueObject/
│   │   │   │   ├── Email.php
│   │   │   │   ├── Password.php
│   │   │   │   ├── UserId.php
│   │   │   │   └── RoleName.php
│   │   │   └── Repository/
│   │   │       ├── UserRepositoryInterface.php
│   │   │       ├── SessionRepositoryInterface.php
│   │   │       ├── RoleRepositoryInterface.php
│   │   │       └── PermissionRepositoryInterface.php
│   │   ├── Application/
│   │   │   ├── UseCase/
│   │   │   │   ├── LoginUseCase.php
│   │   │   │   ├── LogoutUseCase.php
│   │   │   │   ├── RegisterUseCase.php
│   │   │   │   ├── RefreshTokenUseCase.php
│   │   │   │   ├── ValidateSessionUseCase.php
│   │   │   │   └── CheckPermissionUseCase.php
│   │   │   └── DTO/
│   │   │       ├── LoginRequest.php
│   │   │       ├── LoginResponse.php
│   │   │       ├── RegisterRequest.php
│   │   │       ├── SessionDTO.php
│   │   │       └── TokenPair.php
│   │   └── Infrastructure/
│   │       ├── Persistence/
│   │       │   ├── PdoUserRepository.php
│   │       │   ├── PdoSessionRepository.php
│   │       │   ├── PdoRoleRepository.php
│   │       │   └── PdoPermissionRepository.php
│   │       ├── Security/
│   │       │   ├── Argon2idPasswordHasher.php
│   │       │   ├── JwtTokenManager.php
│   │       │   ├── CsrfTokenManager.php
│   │       │   └── SessionManager.php
│   │       └── Http/
│   │           ├── AuthApiClient.php
│   │           └── AuthMiddleware.php
│   ├── Security/                  ← Middleware Pipeline
│   │   ├── Middleware/
│   │   │   ├── SessionManagerMiddleware.php
│   │   │   ├── BypassAuthMiddleware.php
│   │   │   ├── RateLimiterMiddleware.php
│   │   │   ├── AuthMiddleware.php
│   │   │   ├── SecurityHeadersMiddleware.php
│   │   │   └── CsrfMiddleware.php
│   │   └── Service/
│   │       ├── CspNonceGenerator.php
│   │       ├── RateLimiter.php
│   │       └── SecurityHeaderService.php
│   ├── Http/                      ← HTTP Kernel
│   │   ├── Kernel.php
│   │   ├── Request/
│   │   │   └── ServerRequestFactory.php
│   │   └── Response/
│   │       └── ResponseEmitter.php
│   ├── Router/                    ← Enterprise Router
│   │   ├── Router.php
│   │   ├── RouteCollector.php
│   │   ├── RouteDispatcher.php
│   │   ├── Attributes/
│   │   │   ├── Route.php
│   │   │   ├── Middleware.php
│   │   │   └── Guard.php
│   │   └── Cache/
│   │       └── RouteCache.php
│   ├── Container/                 ← DI Container
│   │   └── ContainerFactory.php
│   └── Event/                     ← Event Dispatcher
│       └── EventDispatcherFactory.php
├── config/
│   ├── services.php               ← DI definitions
│   ├── routes.php                 ← Ortak route'lar
│   ├── middleware.php             ← Pipeline tanımı
│   ├── cors.php                   ← CORS whitelist
│   └── auth.php                   ← Auth konfigürasyonu
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── E2E/
└── README.md
```

## 8. Namespace Yapısı

```php
// Shared Library — CoreMusic\Shared namespace'i altında
namespace CoreMusic\Shared\Auth\Domain\Entity;
namespace CoreMusic\Shared\Auth\Domain\Repository;
namespace CoreMusic\Shared\Auth\Application\UseCase;
namespace CoreMusic\Shared\Auth\Infrastructure\Persistence;
namespace CoreMusic\Shared\Auth\Infrastructure\Security;
namespace CoreMusic\Shared\Security\Middleware;
namespace CoreMusic\Shared\Security\Service;
namespace CoreMusic\Shared\Http;
namespace CoreMusic\Shared\Router;
namespace CoreMusic\Shared\Container;
namespace CoreMusic\Shared\Event;

// Subdomain Controllers — kendi namespace'leri altında
namespace CoreMusic\Auth\Controller;
namespace CoreMusic\Music\Controller;
namespace CoreMusic\Admin\Controller;
namespace CoreMusic\Home\Controller;
namespace CoreMusic\Studio\Controller;
namespace CoreMusic\Pro\Controller;
namespace CoreMusic\Car\Controller;
namespace CoreMusic\Media\Controller;
namespace CoreMusic\Api\Controller;
namespace CoreMusic\Download\Controller;
```

## 9. Bağımlılık Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROJE BAĞIMLILIK AKIŞI                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  auth.coremusic.net/composer.json                               │
│    │                                                             │
│    ├── "coremusic/shared": "*" (path repository)                │
│    │                                                             │
│    ├── autoload: CoreMusic\Auth\ → include/                     │
│    │                                                             │
│    └── vendor/coremusic/shared/  ← symlink                      │
│          │                                                       │
│          └── CoreMusic\Shared\Auth\...\LoginUseCase              │
│                                                                 │
│  music.coremusic.net/composer.json                              │
│    │                                                             │
│    ├── "coremusic/shared": "*" (path repository)                │
│    │                                                             │
│    └── vendor/coremusic/shared/  ← symlink                      │
│          │                                                       │
│          └── CoreMusic\Shared\Security\...\SessionManager       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 10. Kurallar

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Root'ta composer.json YASAK | Her subdomain kendi composer.json'unu taşır |
| 2 | Root'ta phpunit.xml YASAK | Her subdomain kendi test config'ini taşır |
| 3 | Root'ta vendor/ YASAK | Her subdomain kendi vendor'unu taşır |
| 4 | shared/ bağımsız paket | `coremusic/shared` olarak publish edilir |
| 5 | Path repository | Development'ta symlink ile linklenir |
| 6 | Subdomain bağımsız deploy | Her subdomain ayrı deploy edilebilir |
| 7 | Bağımlılık aşağı doğru | Subdomain → shared (asla tersi değil) |
| 8 | shared bağımsız | shared/, hiçbir subdomain'e bağımlı değildir |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Root'ta composer.json yasak | Multi-project yapısı bozulur → revert |
| 2 | Her subdomain kendi vendor'unu taşır | Bağımlılık çatışması |
| 3 | shared/ bağımsız包 | Circular dependency → revert |
| 4 | Path repository development | Production'da paket olarak publish |
| 5 | Subdomain'ler arası bağımlılık yasak | Sadece shared üzerinden iletişim |

## 12. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/03-contracts/project-structure]] | Clean Architecture detayı |
| [[architecture/03-contracts/shared-library]] | Shared library paket detayları |
| [[architecture/03-contracts/composer-package-standards]] | Composer paket standartları |
| [[ADR-051-platform-rewrite-from-scratch]] | Platform sıfırdan yazım |
| [[ADR-054-enterprise-composer-stack]] | Enterprise Composer yığını |

## 13. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Kök Dizin | [[ADR-051-platform-rewrite-from-scratch]] | Proje yapısı |
| § 4 Yasaklar | [[ADR-054-enterprise-composer-stack]] | Root config |
| § 7 Shared | [[architecture/03-contracts/shared-library]] | Paket yapısı |
| § 8 Namespace | [[ADR-051-platform-rewrite-from-scratch]] | Namespace standardı |

## 14. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | ~350 |
| **ADR Uyumlu** | ✅ 051, 054 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode
