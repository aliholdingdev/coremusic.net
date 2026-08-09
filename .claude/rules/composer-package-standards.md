# Composer Package Standards — CoreMusic

**Authority:** ADR-054, ADR-059
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Core Principle

> **"Build Business Logic, Not Infrastructure."**

Security, Authentication, Authorization, HTTP, Validation, Logging, Cache, Queue, Serialization, OpenAPI, Rate Limit, Retry, Event, UUID, Encryption, Configuration, Testing gibi tekrar eden problemler mümkün olduğunca **kurumsal seviyede kabul görmüş Composer paketleri** ile çözülmelidir.

**Öncelik sırası:**
1. Enterprise Composer Package
2. PSR Standardı
3. PHP-FIG Standardı
4. Kurumsal Mimari
5. Kendi Kodumuz

## 2. Mandatory PSR Standards

Her CoreMusic Composer paketi下列 PSR'leri desteklemek zorundadır:

- PSR-1 (Coding Standard)
- PSR-3 (Logger Interface)
- PSR-4 (Autoloading)
- PSR-6 (Caching Interface)
- PSR-7 (HTTP Message)
- PSR-11 (Container Interface)
- PSR-12 (Coding Style)
- PSR-14 (Event Dispatcher)
- PSR-15 (HTTP Server Request Handlers)
- PSR-16 (Simple Cache)
- PSR-17 (HTTP Factories)
- PSR-18 (HTTP Client)

## 3. Enterprise Composer Packages

### 3.1 HTTP

| Paket | Amaç |
|--------|------|
| `guzzlehttp/guzzle` | HTTP Client |
| `nyholm/psr7` | PSR-7 HTTP Message |
| `php-http/discovery` | PSR-18 Discovery |

### 3.2 Dependency Injection

| Paket | Amaç |
|--------|------|
| `php-di/php-di` | DI Container |

### 3.3 Routing

| Paket | Amaç |
|--------|------|
| `nikic/fast-route` | Fast Router |

### 3.4 UUID

| Paket | Amaç |
|--------|------|
| `ramsey/uuid` | UUID Üretimi |

### 3.5 Date Time

| Paket | Amaç |
|--------|------|
| `nesbot/carbon` | Tarih ve Saat İşlemleri |

### 3.6 Logging

| Paket | Amaç |
|--------|------|
| `monolog/monolog` | PSR-3 Logger |

### 3.7 Validation

| Paket | Amaç |
|--------|------|
| `symfony/validator` | Validation |
| `respect/validation` | Alternatif |

### 3.8 Serializer

| Paket | Amaç |
|--------|------|
| `symfony/serializer` | DTO / JSON / XML Dönüşümleri |

### 3.9 Event Dispatcher

| Paket | Amaç |
|--------|------|
| `symfony/event-dispatcher` | Event Sistemi (PSR-14) |

### 3.10 Messenger / Queue

| Paket | Amaç |
|--------|------|
| `symfony/messenger` | Queue Abstraction |

### 3.11 Cache

| Paket | Amaç |
|--------|------|
| `symfony/cache` | Cache (PSR-6) |
| `predis/predis` | Redis Client |

### 3.12 Lock

| Paket | Amaç |
|--------|------|
| `symfony/lock` | Distributed Lock |

### 3.13 Rate Limit

| Paket | Amaç |
|--------|------|
| `symfony/rate-limiter` | Rate Limiting |

### 3.14 Configuration

| Paket | Amaç |
|--------|------|
| `symfony/config` | Config |
| `vlucas/phpdotenv` | Environment |

### 3.15 Security

| Paket | Amaç |
|--------|------|
| `firebase/php-jwt` | JWT |
| `league/oauth2-server` | OAuth2 Server |
| `symfony/security-csrf` | CSRF |
| `symfony/password-hasher` | Password Hash |

### 3.16 Encryption

| Paket | Amaç |
|--------|------|
| `paragonie/halite` | Crypto |
| `paragonie/sodium_compat` | Sodium |

### 3.17 OpenAPI

| Paket | Amaç |
|--------|------|
| `zircote/swagger-php` | OpenAPI |

### 3.18 Filesystem

| Paket | Amaç |
|--------|------|
| `league/flysystem` | Dosya Sistemi |

### 3.19 HTML Sanitizer

| Paket | Amaç |
|--------|------|
| `ezyang/htmlpurifier` | XSS Koruması |

### 3.20 CLI

| Paket | Amaç |
|--------|------|
| `symfony/console` | Console |
| `symfony/process` | Process |

### 3.21 Testing

| Paket | Amaç |
|--------|------|
| `phpunit/phpunit` | Unit Test |
| `pestphp/pest` | Modern Test |
| `mockery/mockery` | Mock |
| `phpstan/phpstan` | Static Analysis |
| `friendsofphp/php-cs-fixer` | Code Style |
| `rector/rector` | Refactoring |
| `infection/infection` | Mutation Testing |

### 3.22 Observability

| Paket | Amaç |
|--------|------|
| `open-telemetry/sdk` | OpenTelemetry |
| `promphp/prometheus_client_php` | Prometheus |
| `sentry/sentry` | Error Tracking |

## 4. coremusic/* Packages

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

**Kural:** Tek monolitik `shared/` paketi yasak. Her modül bağımsız Composer paketi olarak yayınlanır.

## 5. Package Selection Criteria

Herhangi bir Composer paketi projeye eklenmeden önce:

- PSR standartlarını destekliyor mu?
- PHP 8.4 ile uyumlu mu?
- Son sürümü aktif olarak geliştiriliyor mu?
- Güvenlik açıkları bulunuyor mu?
- GitHub aktivitesi devam ediyor mu?
- Kurumsal projelerde kullanılıyor mu?
- Dokümantasyonu yeterli mi?
- Bakımı bırakılmış mı?
- Lisansı uygun mu? (MIT, BSD, Apache tercih edilir.)

## 6. Forbidden Packages

Aşağıdaki paketler kesinlikle kullanılmayacaktır:

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

## 7. Forbidden Patterns

- Kendi JWT algoritmasını yazmak
- Kendi şifreleme algoritmasını yazmak
- Kendi Hash algoritmasını yazmak
- Güvenliği kanıtlanmamış Composer paketleri kullanmak
- Bakımsız Composer paketleri kullanmak
- `SELECT *` kullanmak
- ORM kullanmak
- Güvenlik açısından kritik bileşenleri yeniden geliştirmek

## 8. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PSR Standards | Kod revert edilir |
| 2 | PHP 8.4+ | Uyumsuzluk |
| 3 | No Custom JWT/Crypto/Hash | Güvenlik açığı |
| 4 | No ORM | SQL injection |
| 5 | No `SELECT *` | Güvenlik açığı |
| 6 | No Framework | Bağımlılık artışı |
| 7 | No Hardcoded Secrets | Güvenlik açığı |
| 8 | No `eval()` | Güvenlik açığı |

---

*Composer Package Standards v1.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
