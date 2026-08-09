---
type: adr
category: architecture
title: "ADR-055: Detailed Project Structure & Implementation Plan"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-055: Detailed Project Structure & Implementation Plan

**Status:** Active
**Kategorisi:** Architecture
**İlgili Agent:** [[.agents/backend-architect]]
**İlgili Division:** Software Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunun sıfırdan yazımı için **dosya bazlı detaylı implementasyon planını** tanımlar. Her dosyanın amacı, içeriği ve bağımlılıkları belirlenmiştir.

---

## 2. Bağlam

### 2.1 İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-051 | Platform Rewrite from Scratch — proje yapısı |
| ADR-052 | Hybrid Auth Architecture — Session + JWT |
| ADR-053 | Enterprise Router Architecture — PSR-15 router |
| ADR-054 | Enterprise Composer Stack — 30 paket |

### 2.2 Uygulama Sırası

```
Faz 1: ADR-051/052/053/054 ✅ Tamamlandı
Faz 2: Bu dosya — Detaylı proje yapısı (ŞU AN)
Faz 3: Auth module implementation plan
Faz 4: Router + Middleware implementation plan
Faz 5: Kullanıcı onayı → Kodlama başlar
```

---

## 3. Karar — Dosya Bazlı Implementasyon Planı

### 3.1 Root Dosyalar

#### `composer.json` (Root)

```json
{
    "name": "coremusic/platform",
    "description": "CoreMusic Digital Media Management Platform",
    "type": "project",
    "license": "proprietary",
    "require": {
        "php": "^8.4",
        "nyholm/psr7": "^1.8",
        "nyholm/psr7-server": "^1.1",
        "psr/http-message": "^2.0",
        "psr/http-server-handler": "^2.0",
        "psr/http-server-middleware": "^2.0",
        "psr/http-factory": "^1.1",
        "psr/container": "^2.0",
        "psr/event-dispatcher": "^1.0",
        "psr/log": "^3.0",
        "psr/cache": "^3.0",
        "php-di/php-di": "^7.0",
        "nikic/fast-route": "^1.3",
        "firebase/php-jwt": "^6.10",
        "ramsey/uuid": "^4.7",
        "monolog/monolog": "^3.7",
        "vlucas/phpdotenv": "^5.6",
        "respect/validation": "^1.17",
        "symfony/security-csrf": "^7.1",
        "symfony/rate-limiter": "^7.1",
        "symfony/event-dispatcher": "^7.1",
        "symfony/cache": "^7.1",
        "paragonie/halite": "^5.1",
        "ezyang/htmlpurifier": "^4.17",
        "guzzlehttp/guzzle": "^7.9",
        "dragonmantank/cron-expression": "^3.4",
        "robmorgan/phinx": "^0.16"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^1.12",
        "friendsofphp/php-cs-fixer": "^3.65",
        "rector/rector": "^2.0",
        "roave/security-advisories": "^0.14"
    },
    "autoload": {
        "psr-4": {
            "CoreMusic\\": "shared/src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "CoreMusic\\Tests\\": "shared/tests/"
        }
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true,
        "allow-plugins": {
            "phpstan/extension-installer": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Yok
**Çıktı:** `composer.json` dosyası

---

#### `.env.example`

```ini
# ============================================
# CoreMusic Environment Configuration
# ============================================

# --- Application ---
APP_ENV=development
APP_DEBUG=true
APP_URL=http://music.coremusic.net:81
APP_SECRET=CHANGE_ME_32_CHAR_RANDOM_STRING

# --- Database (coremusic_auth) ---
DB_AUTH_HOST=127.0.0.1
DB_AUTH_PORT=3306
DB_AUTH_NAME=coremusic_auth
DB_AUTH_USER=root
DB_AUTH_PASS=CHANGE_ME

# --- Database (coremusic_user) ---
DB_USER_HOST=127.0.0.1
DB_USER_PORT=3306
DB_USER_NAME=coremusic_user
DB_USER_USER=root
DB_USER_PASS=CHANGE_ME

# --- Database (coremusic_musics) ---
DB_MUSICS_HOST=127.0.0.1
DB_MUSICS_PORT=3306
DB_MUSICS_NAME=coremusic_musics
DB_MUSICS_USER=root
DB_MUSICS_PASS=CHANGE_ME

# --- JWT ---
JWT_SECRET=CHANGE_ME_64_CHAR_RANDOM_STRING
JWT_ACCESS_TTL=900
JWT_REFRESH_TTL=604800
JWT_ISSUER=auth.coremusic.net

# --- Session ---
SESSION_LIFETIME=3600
SESSION_IDLE_TIMEOUT=1800
SESSION_NAME=COREMUSIC_SESS

# --- Redis ---
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0

# --- Rate Limiting ---
RATE_LIMIT_MAX=60
RATE_LIMIT_INTERVAL=60

# --- Auth Service ---
AUTH_SERVICE_URL=http://auth.coremusic.net

# --- CORS ---
CORS_ALLOWED_ORIGINS=http://music.coremusic.net:81,http://admin.coremusic.net

# --- LogLevel ---
LOG_LEVEL=DEBUG
LOG_FILE=../storage/logs/app.log
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Yok
**Çıktı:** `.env.example` dosyası

---

#### `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>shared/tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>shared/tests/Integration</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>shared/src</directory>
        </include>
    </source>
    <coverage>
        <report>
            <html outputDirectory="coverage"/>
            <text outputFile="php://stdout"/>
        </report>
    </coverage>
</phpunit>
```

**Sorumlu:** QA Engineer
**Ön Koşul:** `composer.json`
**Çıktı:** `phpunit.xml` dosyası

---

#### `phpstan.neon`

```neon
parameters:
    level: 10
    paths:
        - shared/src
    tmpDir: .phpstan.cache
    treatPhpDocTypesAsCertain: false
```

**Sorumlu:** QA Engineer
**Ön Koşul:** `composer.json`
**Çıktı:** `phpstan.neon` dosyası

---

#### `.php-cs-fixer.php`

```php
<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/shared/src',
        __DIR__ . '/shared/tests',
    ])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        '@PHP84Migration' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
        'void_return' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true);
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `composer.json`
**Çıktı:** `.php-cs-fixer.php` dosyası

---

### 3.2 Public (Web Root)

#### `public/index.php` — Front Controller

```php
<?php

declare(strict_types=1);

// 1. Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// 3. Error reporting
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// 4. Bootstrap container
$container = require __DIR__ . '/../shared/config/container.php';

// 5. Resolve HTTP kernel
$httpKernel = $container->get(CoreMusic\Http\HttpKernel::class);

// 6. Create PSR-7 request
$serverRequestFactory = $container->get(Psr\Http\Message\ServerRequestFactoryInterface::class);
$request = $serverRequestFactory->createServerRequest(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER
);

// 7. Dispatch
$response = $httpKernel->handle($request);

// 8. Emit response
$responseEmitter = $container->get(CoreMusic\Http\Response\ResponseEmitter::class);
$responseEmitter->emit($response);
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `vendor/` kurulu olmalı
**Çıktı:** `public/index.php` dosyası

---

#### `public/.htaccess`

```apache
RewriteEngine On

# Handle assets directly
RewriteCond %{REQUEST_URI} \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$
RewriteRule ^ - [L]

# Front controller
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**Sorumlu:** DevOps Engineer
**Ön Koşul:** Yok
**Çıktı:** `public/.htaccess` dosyası

---

### 3.3 Shared Infrastructure

#### `shared/composer.json` — Shared Composer

```json
{
    "name": "coremusic/shared",
    "description": "CoreMusic Shared Infrastructure",
    "type": "library",
    "autoload": {
        "psr-4": {
            "CoreMusic\\": "src/"
        }
    }
}
```

**Not:** Bu dosya sadece shared modül için. Root `composer.json` zaten PSR-4 autoload tanımlıyor.

---

#### `shared/src/` — Kaynak Kod Yapısı

```
shared/src/
├── Auth/                               ← Auth Domain
│   ├── Domain/
│   │   ├── User.php                    ← User entity
│   │   ├── Role.php                    ← Role entity (enum)
│   │   ├── Session.php                 ← Session entity
│   │   ├── Token.php                   ← Token entity (JWT)
│   │   └── Repository/
│   │       ├── UserRepositoryInterface.php
│   │       ├── SessionRepositoryInterface.php
│   │       └── TokenRepositoryInterface.php
│   ├── Application/
│   │   ├── LoginUseCase.php
│   │   ├── LogoutUseCase.php
│   │   ├── RegisterUseCase.php
│   │   ├── RefreshTokenUseCase.php
│   │   ├── ValidateSessionUseCase.php
│   │   └── DTO/
│   │       ├── LoginRequest.php
│   │       ├── LoginResponse.php
│   │       └── TokenPair.php
│   └── Infrastructure/
│       ├── Persistence/
│       │   ├── PdoUserRepository.php
│       │   ├── PdoSessionRepository.php
│       │   └── PdoTokenRepository.php
│       ├── Security/
│       │   ├── Argon2idPasswordHasher.php
│       │   ├── JwtTokenManager.php
│       │   └── CsrfTokenManager.php
│       └── Http/
│           ├── AuthApiClient.php
│           └── AuthMiddleware.php
├── Security/                           ← Security Domain
│   ├── Middleware/
│   │   ├── SessionManagerMiddleware.php
│   │   ├── BypassAuthMiddleware.php
│   │   ├── RateLimiterMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   ├── SecurityHeadersMiddleware.php
│   │   └── CsrfMiddleware.php
│   └── Service/
│       ├── CspNonceGenerator.php
│       ├── RateLimiter.php
│       └── SecurityHeaderService.php
├── Http/                               ← HTTP Layer
│   ├── HttpKernel.php
│   ├── Request/
│   │   └── ServerRequestFactory.php
│   └── Response/
│       └── ResponseEmitter.php
├── Router/                             ← Enterprise Router
│   ├── Router.php
│   ├── RouteCollector.php
│   ├── RouteDispatcher.php
│   ├── Attributes/
│   │   ├── Route.php
│   │   ├── Middleware.php
│   │   └── Guard.php
│   └── Cache/
│       └── RouteCache.php
├── Container/                          ← DI Container
│   └── ContainerFactory.php
└── Event/                              ← Event Dispatcher
    └── EventDispatcherFactory.php
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `composer.json`
**Çıktı:** 25+ PHP dosyası

---

#### `shared/config/` — Konfigürasyon

```
shared/config/
├── services.php        ← DI definitions (PHP-DI)
├── routes.php          ← Route definitions (nikic/fast-route)
├── middleware.php       ← Middleware pipeline
└── containers.php      ← Container configuration
```

**`shared/config/services.php` Örneği:**

```php
<?php

declare(strict_types=1);

use CoreMusic\Auth\Infrastructure\Persistence\PdoUserRepository;
use CoreMusic\Auth\Infrastructure\Persistence\PdoSessionRepository;
use CoreMusic\Auth\Infrastructure\Persistence\PdoTokenRepository;
use CoreMusic\Auth\Infrastructure\Security\Argon2idPasswordHasher;
use CoreMusic\Auth\Infrastructure\Security\JwtTokenManager;
use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\SessionRepositoryInterface;
use CoreMusic\Auth\Domain\Repository\TokenRepositoryInterface;
use CoreMusic\Security\Service\RateLimiter;
use CoreMusic\Security\Service\CspNonceGenerator;
use CoreMusic\Security\Service\SecurityHeaderService;
use CoreMusic\Http\HttpKernel;
use CoreMusic\Router\Router;
use CoreMusic\Router\RouteCollector;

return [
    // Repositories
    UserRepositoryInterface::class => \DI\autowire(PdoUserRepository::class),
    SessionRepositoryInterface::class => \DI\autowire(PdoSessionRepository::class),
    TokenRepositoryInterface::class => \DI\autowire(PdoTokenRepository::class),

    // Security
    Argon2idPasswordHasher::class => \DI\autowire(Argon2idPasswordHasher::class),
    JwtTokenManager::class => \DI\autowire(JwtTokenManager::class),

    // Services
    RateLimiter::class => \DI\autowire(RateLimiter::class),
    CspNonceGenerator::class => \DI\autowire(CspNonceGenerator::class),
    SecurityHeaderService::class => \DI\autowire(SecurityHeaderService::class),

    // Router
    Router::class => function (\Psr\Container\ContainerInterface $c) {
        $collector = new RouteCollector();
        // Routes will be loaded dynamically
        return new Router($collector);
    },

    // HTTP Kernel
    HttpKernel::class => function (\Psr\Container\ContainerInterface $c) {
        return new HttpKernel(
            $c->get(Router::class),
            $c->get('middleware.pipeline')
        );
    },
];
```

**Sorumlu:** Backend Architect
**Ön Koşul:** Tüm `shared/src/` dosyaları
**Çıktı:** 4 konfigürasyon dosyası

---

### 3.4 Service Entry Points

#### `auth.coremusic.net/index.php`

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

// Auth service — handles login, register, logout, session check
// Routes:
//   GET  /login              → LoginController@show
//   POST /login              → LoginController@login
//   GET  /register           → RegisterController@show
//   POST /register           → RegisterController@register
//   POST /logout             → LogoutController@logout
//   GET  /api/session/check  → SessionCheckController@check
//   POST /api/token/refresh  → TokenController@refresh
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `shared/bootstrap.php`
**Çıktı:** `auth.coremusic.net/index.php` + Controller'lar

---

#### `music.coremusic.net/index.php`

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../shared/bootstrap.php';

// Music panel — main media interface
// Routes:
//   GET  /                    → MusicController@home
//   GET  /kesfet              → MusicController@discover
//   GET  /playlist/{id}       → PlaylistController@show
//   GET  /api/music/search    → ApiController@search
//   GET  /api/music/{id}      → ApiController@get
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `shared/bootstrap.php`
**Çıktı:** `music.coremusic.net/index.php` + Controller'lar

---

#### `shared/bootstrap.php` — Ortak Bootstrap

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

$container = require __DIR__ . '/config/container.php';

$httpKernel = $container->get(CoreMusic\Http\HttpKernel::class);

$serverRequestFactory = $container->get(Psr\Http\Message\ServerRequestFactoryInterface::class);
$request = $serverRequestFactory->createServerRequest(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER
);

$response = $httpKernel->handle($request);

$responseEmitter = $container->get(CoreMusic\Http\Response\ResponseEmitter::class);
$responseEmitter->emit($response);
```

**Sorumlu:** Backend Architect
**Ön Koşul:** `composer install` tamamlanmış
**Çıktı:** `shared/bootstrap.php` dosyası

---

### 3.5 Frontend Assets

```
public/css/
├── 01_Settings/         ← Variables, custom properties
├── 02_Tools/            ← Mixins, functions
├── 03_Generic/          ← Reset, normalize
├── 04_Elements/         ← HTML elements
├── 05_Objects/          ← Structural objects
├── 06_Components/       ← UI components
├── 07_Trumps/           ← Utilities
└── main.css             ← Aggregator (@import)

public/js/
├── Router.js            ← SPA router (pushState)
├── Auth.js              ← Auth client (cookie-based)
├── App.js               ← Main application
├── Components/          ← UI components
└── Utils/               ← Utilities

public/assets/
├── images/
├── fonts/
└── icons/
```

**Sorumlu:** UI Designer
**Ön Koşul:** ADR-001 (Vanilla JS + ITCSS)
**Çıktı:** Frontend asset yapısı

---

## 4. Implementasyon Sırası

| # | Dosya | Sorumlu | Tahmini | Bağımlılık |
|---|-------|---------|---------|------------|
| 1 | `composer.json` (root) | Backend Architect | 5 dk | — |
| 2 | `.env.example` | Backend Architect | 5 dk | — |
| 3 | `phpunit.xml` | QA Engineer | 5 dk | — |
| 4 | `phpstan.neon` | QA Engineer | 3 dk | — |
| 5 | `.php-cs-fixer.php` | Backend Architect | 5 dk | — |
| 6 | `shared/bootstrap.php` | Backend Architect | 10 dk | #1 |
| 7 | `shared/config/container.php` | Backend Architect | 15 dk | #1 |
| 8 | `shared/config/services.php` | Backend Architect | 20 dk | #7 |
| 9 | `shared/config/middleware.php` | Backend Architect | 10 dk | #7 |
| 10 | `public/index.php` | Backend Architect | 10 dk | #6 |
| 11 | `public/.htaccess` | DevOps Engineer | 5 dk | — |
| 12 | `shared/src/Http/HttpKernel.php` | Backend Architect | 30 dk | #7, #9 |
| 13 | `shared/src/Router/Router.php` | Backend Architect | 45 dk | #7 |
| 14 | `shared/src/Security/Middleware/*` | Security Engineer | 60 dk | #7, #9 |
| 15 | `shared/src/Auth/*` | Security Engineer | 90 dk | #7 |
| 16 | `auth.coremusic.net/index.php` | Backend Architect | 10 dk | #6 |
| 17 | `music.coremusic.net/index.php` | Backend Architect | 10 dk | #6 |
| 18 | `public/css/*` | UI Designer | 60 dk | — |
| 19 | `public/js/*` | UI Designer | 60 dk | — |
| 20 | Tests | QA Engineer | 120 dk | #12-#17 |

**Toplam Tahmini:** ~8.5 saat (ilk implementasyon)

---

## 5. Hard Guardrails

| # | Guardrail | Uygulama |
|---|-----------|----------|
| G1 | Zero Code Before Plan | Bu plan onaylanmadan kod yok |
| G2 | MSA Limit = 15 dosya | Görev başına max 15 dosya |
| G3 | ORM yasak | Sadece PDO prepared statement |
| G4 | Framework yasak | Sadece Vanilla JS + PHP native |
| G5 | Middleware sırası frozen | Değiştirilmez |
| G6 | csrf_token key | Değiştirilmez |

---

## 6. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| ADR-051 | Platform Rewrite | Proje yapısı |
| ADR-052 | Hybrid Auth | Auth modülü |
| ADR-053 | Enterprise Router | Router modülü |
| ADR-054 | Composer Stack | Paket listesi |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 7 |
| Root Dosya | 5 |
| Shared Src Dosya | 25+ |
| Config Dosya | 4 |
| Service Entry | 2+ |
| Frontend Dosya | 10+ |
| Implementasyon Adım | 20 |
| Tahmini Süre | ~8.5 saat |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
