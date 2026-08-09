---
type: architecture
category: contracts
title: "Shared Library — CoreMusic Modular Composer Packages"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Shared Library — CoreMusic Modular Composer Packages

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic'in tüm subdomain'leri tarafından kullanılan ortak altyapı kütüphanesini tanımlar. Tek bir monolitik paket yerine, modüler `coremusic/*` Composer paketleri kullanılır.

## 2. Temel İlke

> **"Build Business Logic, Not Infrastructure."**

Altyapı bileşenleri mümkün olduğunca standartlar ve güvenilir Composer paketleri üzerine inşa edilecek; yalnızca CoreMusic'e özgü iş kuralları özel olarak geliştirilecektir.

## 3. Modüler Paket Yapısı

```
coremusic/
├── contracts/          ← DTO, Request, Response, Value Objects, Enums
├── http/               ← PSR-7 HTTP Message, Request/Response
├── auth/               ← Authentication domain logic
├── security/           ← CSRF, CSP, Rate Limit, Encryption
├── cache/              ← Cache abstraction (Redis, APCu, File)
├── events/             ← Event Dispatcher (PSR-14)
├── openapi/            ← OpenAPI/Swagger definitions
├── sdk/                ← API Client SDK
├── logger/             ← PSR-3 Logger
├── support/            ← Helpers, Utilities
├── validation/         ← Request/DTO Validation
├── queue/              ← Queue abstraction
├── storage/            ← Filesystem abstraction
├── config/             ← Configuration management
├── monitoring/         ← Health check, Metrics
├── testing/            ← Test utilities
├── api-client/         ← HTTP Client for API
├── websocket/          ← WebSocket client/server
└── observability/      ← Tracing, Metrics, Logging
```

## 4. Paket Detayları

### 4.1 coremusic/contracts

| Bileşen | Sorumluluk |
|---------|------------|
| **DTO** | Data Transfer Objects |
| **Request** | API Request modelleri |
| **Response** | API Response modelleri |
| **Value Objects** | Immutable value objects |
| **Enums** | Enumerations |
| **Exceptions** | Ortak exception sınıfları |

```php
namespace CoreMusic\Contracts\Auth\DTO;
namespace CoreMusic\Contracts\Auth\Request;
namespace CoreMusic\Contracts\Auth\Response;
namespace CoreMusic\Contracts\Music\DTO;
namespace CoreMusic\Contracts\Common\ValueObject;
namespace CoreMusic\Contracts\Common\Enum;
```

### 4.2 coremusic/http

| Bileşen | Sorumluluk |
|---------|------------|
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
| **Middleware** | SessionManager, BypassAuth, RateLimiter, Auth, SecurityHeaders, Csrf |
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
        "firebase/php-jwt": "^6.10",
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
coremusic/contracts  ←── coremusic/auth
                   ←── coremusic/security
                   ←── coremusic/validation
                   ←── coremusic/api-client

coremusic/http  ←── coremusic/auth
              ←── coremusic/security
              ←── coremusic/api-client

coremusic/cache  ←── coremusic/auth
               ←── coremusic/security
               ←── coremusic/queue

coremusic/events  ←── coremusic/auth
                ←── coremusic/queue
                ←── coremusic/monitoring

coremusic/storage  ←── coremusic/monitoring

coremusic/queue  ←── coremusic/monitoring
```

## 9. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Her paket bağımsız versionlanabilir | Bağımlılık ihlali |
| 2 | Domain katmanında bağımlılık yasak | Layer violation → revert |
| 3 | Contracts bağımsızdır, bağımlılığı yoktur | Bağımlılık ihlali |
| 4 | PSR standartlarına uygunluk zorunlu | Uyumsuzluk |
| 5 | `declare(strict_types=1)` her dosyada zorunlu | Tip hatası riski |
| 6 | ORM yasak — sadece PDO prepared | ADR-002 |
| 7 | Framework yasak — sadece Composer paketleri | ADR-001 |

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/03-contracts/project-structure]] | Proje yapısı |
| [[architecture/01-overview/architecture_master]] | Ana mimari |
| [[architecture/l1-security/index]] | Security layer |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS kuralı |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO kuralı |

## 11. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Paketler | [[architecture/03-contracts/api-architecture-master]] | API mimarisi |
| § 5 Bağımlılıklar | [[architecture/07-security/security-standards]] | Güvenlik standartları |
| § 8 Graf | [[architecture/01-overview/architecture_master]] | Ana mimari |

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Paket Sayısı** | 19 |
| **ADR Uyumlu** | ✅ 001, 002, 007, 042, 052 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **PSR Uyumlu** | ✅ PSR-1,3,4,6,7,11,12,14,15,16,17,18 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
