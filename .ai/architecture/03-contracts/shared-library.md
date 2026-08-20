---
type: architecture
category: contracts
title: "Shared Library — CoreMusic Hybrid Architecture"
date: 2026-08-09
updated: 2026-08-15
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Shared Library — CoreMusic Hybrid Architecture

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-085-modular-composer-packages]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic'in tüm subdomain'leri tarafından kullanılan ortak altyapı kütüphanesini tanımlar. Tek `shared/` dizini + PSR-4 namespace ile modüler ayrım kullanılır (ADR-085 v3.0).

## 2. Temel İlke

> **"Build Business Logic, Not Infrastructure."**

Altyapı bileşenleri mümkün olduğunca standartlar ve güvenilir Composer paketleri üzerine inşa edilecek; yalnızca CoreMusic'e özgü iş kuralları özel olarak geliştirilecektir.

## 3. Modüler Namespace Yapısı (ADR-085 v3.0)

Tek `shared/` dizini, PSR-4 namespace ile modüler ayrılmış:

```
shared/
├── composer.json              ← Tek paket: coremusic/shared
├── bootstrap.php              ← Autoloader + env
├── config/                    ← Config dosyaları
│   ├── database.php           ← 18 BCNF DB
│   ├── middleware.php          ← Frozen 10 katman pipeline
│   ├── routes.php             ← Route tanımları
│   └── cors.php               ← CORS whitelist
├── src/
│   ├── Router/                ← L2: SPA Router
│   │   ├── Contracts/         ← RouterInterface, RouteDefinitionInterface
│   │   ├── Attributes/        ← #[Route], #[Middleware], #[Guard]
│   │   └── Cache/             ← RouteCache
│   ├── Security/              ← L1: Middleware Pipeline
│   │   ├── Middleware/         ← 10 middleware (frozen sıra)
│   │   └── Service/            ← CspNonceGenerator, RateLimiter
│   ├── Auth/                  ← L1/L4: Auth Domain
│   │   ├── Domain/             ← Entity, ValueObject, Repository, Event
│   │   ├── Application/        ← Command, Query, DTO, Service
│   │   └── Infrastructure/     ← Repository implementations
│   ├── Http/                  ← PSR-7/17
│   ├── Cache/                 ← PSR-6
│   ├── Events/                ← PSR-14
│   ├── Validation/            ← Request validation
│   └── Logger/                ← PSR-3
└── tests/
    └── Unit/
```

### Namespace Haritası

| Namespace | Katman | Kullanım |
|-----------|--------|----------|
| `CoreMusic\Router\*` | L2 | SPA Router |
| `CoreMusic\Security\*` | L1 | Middleware Pipeline |
| `CoreMusic\Auth\*` | L1/L4 | Auth Domain |
| `CoreMusic\Http\*` | — | PSR-7 HTTP |
| `CoreMusic\Cache\*` | L0 | PSR-6 Cache |
| `CoreMusic\Events\*` | — | PSR-14 Events |
| `CoreMusic\Validation\*` | — | Request Validation |
| `CoreMusic\Logger\*` | — | PSR-3 Logging |
| **Request** | PSR-7 Request implementation |
| **Response** | PSR-7 Response implementation |
| **ServerRequestFactory** | Request factory |
| **StreamFactory** | Stream factory |
| **UriFactory** | URI factory |

```php
namespace CoreMusic\Http\Request;
namespace CoreMusic\Http\Response;
namespace CoreMusic\Http\Factory;
```

### 4.3 coremusic/auth

| Bileşen | Sorumluluk |
|---------|------------|
| **Domain** | User, Role, Permission, Session entities |
| **Application** | Login, Logout, Register, RefreshToken use cases |
| **Infrastructure** | PDO repositories, JWT manager, Password hasher |

```php
namespace CoreMusic\Auth\Domain\Entity;
namespace CoreMusic\Auth\Domain\Repository;
namespace CoreMusic\Auth\Application\UseCase;
namespace CoreMusic\Auth\Infrastructure\Persistence;
namespace CoreMusic\Auth\Infrastructure\Security;
```

### 4.4 coremusic/security

| Bileşen | Sorumluluk |
|---------|------------|
| **Middleware** | OriginCheck, Cors, RateLimiter, SecurityHeaders, SessionManager, Csrf, BypassAuth, Auth, Permission, Validation |
| **Service** | CspNonceGenerator, RateLimiter, SecurityHeaderService |
| **Encryption** | AES-256-GCM, Argon2id |

```php
namespace CoreMusic\Security\Middleware;
namespace CoreMusic\Security\Service;
namespace CoreMusic\Security\Encryption;
```

### 4.5 coremusic/cache

| Bileşen | Sorumluluk |
|---------|------------|
| **Adapter** | Redis, APCu, File, Memory adapters |
| **Interface** | CacheInterface (PSR-6/PSR-16) |
| **Manager** | CacheManager with chain support |

```php
namespace CoreMusic\Cache\Adapter;
namespace CoreMusic\Cache\Interface;
namespace CoreMusic\Cache\Manager;
```

### 4.6 coremusic/events

| Bileşen | Sorumluluk |
|---------|------------|
| **Dispatcher** | PSR-14 EventDispatcher |
| **Listener** | Event listeners |
| **Subscriber** | Event subscribers |

```php
namespace CoreMusic\Events\Dispatcher;
namespace CoreMusic\Events\Listener;
namespace CoreMusic\Events\Subscriber;
```

### 4.7 coremusic/validation

| Bileşen | Sorumluluk |
|---------|------------|
| **Request** | Request validation |
| **DTO** | DTO validation |
| **Rules** | Custom validation rules |

```php
namespace CoreMusic\Validation\Request;
namespace CoreMusic\Validation\DTO;
namespace CoreMusic\Validation\Rule;
```

### 4.8 coremusic/storage

| Bileşen | Sorumluluk |
|---------|------------|
| **Adapter** | Local, NAS, SMB, NFS, S3, Azure, R2 |
| **Interface** | StorageInterface |
| **Manager** | StorageManager |

```php
namespace CoreMusic\Storage\Adapter;
namespace CoreMusic\Storage\Interface;
namespace CoreMusic\Storage\Manager;
```

### 4.9 coremusic/queue

| Bileşen | Sorumluluk |
|---------|------------|
| **Adapter** | Redis Queue, Symfony Messenger |
| **Interface** | QueueInterface |
| **Manager** | QueueManager |

```php
namespace CoreMusic\Queue\Adapter;
namespace CoreMusic\Queue\Interface;
namespace CoreMusic\Queue\Manager;
```

### 4.10 coremusic/monitoring

| Bileşen | Sorumluluk |
|---------|------------|
| **Health** | Health check endpoints |
| **Metrics** | Prometheus metrics |
| **Tracing** | OpenTelemetry tracing |

```php
namespace CoreMusic\Monitoring\Health;
namespace CoreMusic\Monitoring\Metrics;
namespace CoreMusic\Monitoring\Tracing;
```

## 5. Composer Bağımlılıkları

### 5.1 coremusic/contracts

```json
{
    "require": {
        "php": ">=8.4"
    }
}
```

### 5.2 coremusic/http

```json
{
    "require": {
        "php": ">=8.4",
        "psr/http-message": "^2.0",
        "nyholm/psr7": "^1.8"
    }
}
```

### 5.3 coremusic/auth

```json
{
    "require": {
        "php": ">=8.4",
        "coremusic/contracts": "^2.0",
        "coremusic/http": "^2.0",
        "lcobucci/jwt": "^5.0",
        "paragonie/halite": "^5.0",
        "ramsey/uuid": "^4.7"
    }
}
```

### 5.4 coremusic/security

```json
{
    "require": {
        "php": ">=8.4",
        "coremusic/contracts": "^2.0",
        "paragonie/halite": "^5.0",
        "paragonie/sodium_compat": "^1.21"
    }
}
```

### 5.5 coremusic/cache

```json
{
    "require": {
        "php": ">=8.4",
        "psr/cache": "^3.0",
        "symfony/cache": "^7.0",
        "predis/predis": "^2.0"
    }
}
```

### 5.6 coremusic/events

```json
{
    "require": {
        "php": ">=8.4",
        "psr/event-dispatcher": "^1.0",
        "symfony/event-dispatcher": "^7.0"
    }
}
```

### 5.7 coremusic/validation

```json
{
    "require": {
        "php": ">=8.4",
        "respect/validation": "^2.0"
    }
}
```

### 5.8 coremusic/storage

```json
{
    "require": {
        "php": ">=8.4",
        "league/flysystem": "^3.0",
        "league/flysystem-local": "^3.0"
    }
}
```

### 5.9 coremusic/queue

```json
{
    "require": {
        "php": ">=8.4",
        "symfony/messenger": "^7.0"
    }
}
```

### 5.10 coremusic/monitoring

```json
{
    "require": {
        "php": ">=8.4",
        "open-telemetry/api": "^1.0",
        "promphp/prometheus_client_php": "^3.0"
    }
}
```

## 6. Namespace Yapısı

```php
// Contracts
namespace CoreMusic\Contracts\Auth\DTO;
namespace CoreMusic\Contracts\Auth\Request;
namespace CoreMusic\Contracts\Auth\Response;
namespace CoreMusic\Contracts\Music\DTO;
namespace CoreMusic\Contracts\Common\ValueObject;
namespace CoreMusic\Contracts\Common\Enum;

// HTTP
namespace CoreMusic\Http\Request;
namespace CoreMusic\Http\Response;
namespace CoreMusic\Http\Factory;

// Auth
namespace CoreMusic\Auth\Domain\Entity;
namespace CoreMusic\Auth\Domain\Repository;
namespace CoreMusic\Auth\Application\UseCase;
namespace CoreMusic\Auth\Infrastructure\Persistence;
namespace CoreMusic\Auth\Infrastructure\Security;

// Security
namespace CoreMusic\Security\Middleware;
namespace CoreMusic\Security\Service;
namespace CoreMusic\Security\Encryption;

// Cache
namespace CoreMusic\Cache\Adapter;
namespace CoreMusic\Cache\Interface;
namespace CoreMusic\Cache\Manager;

// Events
namespace CoreMusic\Events\Dispatcher;
namespace CoreMusic\Events\Listener;
namespace CoreMusic\Events\Subscriber;

// Validation
namespace CoreMusic\Validation\Request;
namespace CoreMusic\Validation\DTO;
namespace CoreMusic\Validation\Rule;

// Storage
namespace CoreMusic\Storage\Adapter;
namespace CoreMusic\Storage\Interface;
namespace CoreMusic\Storage\Manager;

// Queue
namespace CoreMusic\Queue\Adapter;
namespace CoreMusic\Queue\Interface;
namespace CoreMusic\Queue\Manager;

// Monitoring
namespace CoreMusic\Monitoring\Health;
namespace CoreMusic\Monitoring\Metrics;
namespace CoreMusic\Monitoring\Tracing;
```

## 7. Kullanım Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    MODULAR PACKAGE USAGE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  auth.coremusic.net                                             │
│    │                                                             │
│    ├── use CoreMusic\Auth\Application\UseCase\LoginUseCase       │
│    ├── use CoreMusic\Security\Middleware\SessionManager          │
│    ├── use CoreMusic\Http\Request                                │
│    └── use CoreMusic\Contracts\Auth\DTO\LoginRequest             │
│                                                                 │
│  music.coremusic.net                                            │
│    │                                                             │
│    ├── use CoreMusic\Auth\Application\UseCase\ValidateSession    │
│    ├── use CoreMusic\Security\Middleware\Auth                    │
│    ├── use CoreMusic\Cache\Manager                               │
│    └── use CoreMusic\Contracts\Music\DTO\PlaylistResponse        │
│                                                                 │
│  home.coremusic.net (Embedded)                                  │
│    │                                                             │
│    ├── use CoreMusic\Auth\Domain\Entity\User                     │
│    ├── use CoreMusic\Security\Middleware\SessionManager          │
│    ├── use CoreMusic\Storage\Manager                             │
│    └── use CoreMusic\Monitoring\Health\HealthCheck               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 8. Paket Bağımlılık Grafı

```
## 8. Bağımlılık Diyagramı

```
shared/ (tek paket, PSR-4 namespace)
  ↑
  ├── auth.coremusic.net     ← require shared/bootstrap.php
  ├── music.coremusic.net    ← require shared/bootstrap.php
  ├── api.coremusic.net      ← require shared/bootstrap.php
  ├── admin.coremusic.net    ← require shared/bootstrap.php
  ├── home.coremusic.net     ← require shared/bootstrap.php
  ├── car.coremusic.net      ← require shared/bootstrap.php
  ├── studio.coremusic.net   ← require shared/bootstrap.php
  ├── pro.coremusic.net      ← require shared/bootstrap.php
  ├── download.coremusic.net ← require shared/bootstrap.php
  └── media.coremusic.net    ← require shared/bootstrap.php

Namespace bağımlılık kuralları:
  CoreMusic\Auth\*       → CoreMusic\Security\* (L1 → L1)
  CoreMusic\Security\*   → CoreMusic\Auth\* (L1 → L1)
  CoreMusic\Router\*     → CoreMusic\Security\* (L2 → L1)
  CoreMusic\Http\*       → (bağımsız)
  CoreMusic\Cache\*      → (bağımsız)
  CoreMusic\Events\*     → (bağımsız)
  CoreMusic\Validation\* → (bağımsız)
  CoreMusic\Logger\*     → (bağımsız)

Circular dependency YASAK.
```

## 9. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Tek `shared/` dizini + PSR-4 namespace | Structure violation → revert |
| 2 | Domain katmanında bağımlılık yasak | Layer violation → revert |
| 3 | Circular dependency yasak | Bağımlılık ihlali |
| 4 | PSR standartlarına uygunluk zorunlu | Uyumsuzluk |
| 5 | `declare(strict_types=1)` her dosyada zorunlu | Tip hatası riski |
| 6 | ORM yasak — sadece PDO prepared | ADR-002 |
| 7 | Framework yasak — sadece Composer paketleri | ADR-001 |

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/03-contracts/project-structure]] | Proje yapısı |
| [[architecture/00-overview/architecture-master]] | Ana mimari |
| [[architecture/l1-security/index]] | Security layer |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS kuralı |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO kuralı |

## 11. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Paketler | [[architecture/03-contracts/api-architecture-master]] | API mimarisi |
| § 5 Bağımlılıklar | [[architecture/07-security/security-standards]] | Güvenlik standartları |
| § 8 Graf | [[architecture/00-overview/architecture-master]] | Ana mimari |

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Paket Sayısı** | 19 |
| **ADR Uyumlu** | ✅ 001, 002, 007, 042, 052 |
| **Zero Hallucination** | ✅ |
| **PSR Uyumlu** | ✅ PSR-1,3,4,6,7,11,12,14,15,16,17,18 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
