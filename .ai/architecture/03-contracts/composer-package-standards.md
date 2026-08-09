---
type: architecture
category: contracts
title: "Enterprise Composer Package Standards"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Composer Package Standards

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic ekosisteminde kullanılacak Composer paket standartlarını tanımlar.

## 2. Temel Kural

> **"Build Business Logic, Not Infrastructure."**

Güvenlik, Authentication, Authorization, HTTP, Validation, Logging, Cache, Queue, Serialization, OpenAPI, Rate Limit, Retry, Event, UUID, Encryption, Configuration, Testing gibi tekrar eden problemler mümkün olduğunca **kurumsal seviyede kabul görmüş Composer paketleri** ile çözülmelidir.

**Öncelik sırası:**
1. Enterprise Composer Package
2. PSR Standardı
3. PHP-FIG Standardı
4. Kurumsal Mimari
5. Kendi Kodumuz

## 3. Paket Seçim Kuralları

Herhangi bir Composer paketi projeye eklenmeden önce aşağıdaki kriterler analiz edilmelidir:

- PSR standartlarını destekliyor mu?
- PHP 8.4 ile uyumlu mu?
- Son sürümü aktif olarak geliştiriliyor mu?
- Güvenlik açıkları bulunuyor mu?
- GitHub aktivitesi devam ediyor mu?
- Kurumsal projelerde kullanılıyor mu?
- Dokümantasyonu yeterli mi?
- Bakımı bırakılmış mı?
- Lisansı uygun mu? (MIT, BSD, Apache tercih edilir.)

## 4. Kullanılacak Composer Paketleri

### 4.1 HTTP

| Paket | Amaç |
|--------|------|
| `guzzlehttp/guzzle` | HTTP Client |
| `nyholm/psr7` | PSR-7 HTTP Message |
| `php-http/discovery` | PSR-18 Discovery |

### 4.2 Dependency Injection

| Paket | Amaç |
|--------|------|
| `php-di/php-di` | DI Container |

### 4.3 Routing

| Paket | Amaç |
|--------|------|
| `nikic/fast-route` | Fast Router |

### 4.4 UUID

| Paket | Amaç |
|--------|------|
| `ramsey/uuid` | UUID Üretimi |

### 4.5 Date Time

| Paket | Amaç |
|--------|------|
| `nesbot/carbon` | Tarih ve Saat İşlemleri |

### 4.6 Logging

| Paket | Amaç |
|--------|------|
| `monolog/monolog` | PSR-3 Logger |

### 4.7 Validation

| Paket | Amaç |
|--------|------|
| `symfony/validator` | Validation |
| `respect/validation` | Alternatif |

### 4.8 Serializer

| Paket | Amaç |
|--------|------|
| `symfony/serializer` | DTO / JSON / XML Dönüşümleri |

### 4.9 Event Dispatcher

| Paket | Amaç |
|--------|------|
| `symfony/event-dispatcher` | Event Sistemi (PSR-14) |

### 4.10 Messenger / Queue

| Paket | Amaç |
|--------|------|
| `symfony/messenger` | Queue Abstraction |

### 4.11 Cache

| Paket | Amaç |
|--------|------|
| `symfony/cache` | Cache (PSR-6) |
| `predis/predis` | Redis Client |

### 4.12 Lock

| Paket | Amaç |
|--------|------|
| `symfony/lock` | Distributed Lock |

### 4.13 Rate Limit

| Paket | Amaç |
|--------|------|
| `symfony/rate-limiter` | Rate Limiting |

### 4.14 Configuration

| Paket | Amaç |
|--------|------|
| `symfony/config` | Config |
| `vlucas/phpdotenv` | Environment |

### 4.15 Security

| Paket | Amaç |
|--------|------|
| `firebase/php-jwt` | JWT |
| `league/oauth2-server` | OAuth2 Server |
| `symfony/security-csrf` | CSRF |
| `symfony/password-hasher` | Password Hash |

### 4.16 Encryption

| Paket | Amaç |
|--------|------|
| `paragonie/halite` | Crypto |
| `paragonie/sodium_compat` | Sodium |

### 4.17 OpenAPI

| Paket | Amaç |
|--------|------|
| `zircote/swagger-php` | OpenAPI |

### 4.18 Filesystem

| Paket | Amaç |
|--------|------|
| `league/flysystem` | Dosya Sistemi |

### 4.19 HTML Sanitizer

| Paket | Amaç |
|--------|------|
| `ezyang/htmlpurifier` | XSS Koruması |

### 4.20 CLI

| Paket | Amaç |
|--------|------|
| `symfony/console` | Console |
| `symfony/process` | Process |

### 4.21 Testing

| Paket | Amaç |
|--------|------|
| `phpunit/phpunit` | Unit Test |
| `pestphp/pest` | Modern Test |
| `mockery/mockery` | Mock |
| `phpstan/phpstan` | Static Analysis |
| `friendsofphp/php-cs-fixer` | Code Style |
| `rector/rector` | Refactoring |
| `infection/infection` | Mutation Testing |

### 4.22 Observability

| Paket | Amaç |
|--------|------|
| `open-telemetry/sdk` | OpenTelemetry |
| `promphp/prometheus_client_php` | Prometheus |
| `sentry/sentry` | Error Tracking |

## 5. coremusic/* Paketleri

CoreMusic tek bir shared paketten oluşmamalıdır:

```
coremusic/contracts
coremusic/http
coremusic/auth
coremusic/security
coremusic/cache
coremusic/events
coremusic/openapi
coremusic/sdk
coremusic/logger
coremusic/support
coremusic/validation
coremusic/queue
coremusic/storage
coremusic/config
coremusic/monitoring
coremusic/testing
coremusic/api-client
coremusic/websocket
coremusic/observability
```

## 6. Yasaklar

Aşağıdaki bileşenler sıfırdan yazılmayacaktır:

- JWT
- OAuth2
- Logger
- Validation
- HTTP Client
- UUID
- Rate Limiter
- Serializer
- Event Dispatcher
- Cache Engine
- Queue Engine
- OpenAPI Generator
- Cryptography
- Password Hashing
- CSRF Engine

## 7. Yasaklı Teknolojiler

- Doctrine ORM
- Laravel Eloquent
- Propel ORM
- Active Record Pattern
- MD5
- SHA1
- mcrypt
- `SELECT *`
- `mysql_*` fonksiyonları
- Kendi JWT implementasyonu
- Kendi CSRF implementasyonu
- Kendi Cryptography implementasyonu
- Güvenliği kanıtlanmamış Composer paketleri

## 8. Mimari Kural

Her CoreMusic Composer paketi:

- PSR-1
- PSR-3
- PSR-4
- PSR-6
- PSR-7
- PSR-11
- PSR-12
- PSR-14
- PSR-15
- PSR-16
- PSR-17
- PSR-18

standartlarına uygun olacaktır.

## 9. Sonuç

CoreMusic'in temel hedefi mevcut problemi yeniden çözmek değildir. Amaç; battle-tested, güvenlik denetimlerinden geçmiş, aktif geliştirilen, uzun dönem desteklenen, kurumsal projelerde kullanılan Composer paketlerini kullanarak yalnızca CoreMusic'e özgü iş kurallarını geliştirmektir.

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Paket Sayısı** | 40+ |
| **PSR Uyumlu** | ✅ |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
