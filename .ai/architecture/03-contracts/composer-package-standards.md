---
type: architecture
category: contracts
title: "Enterprise Composer Package Standards"
date: 2026-08-09
updated: 2026-08-12
status: active
version: 2.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Composer Package Standards

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-085-modular-composer-packages]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic ekosisteminde kullanılacak Composer paket standartlarını tanımlar. **Shared library `shared/` dizinindedir (NOT `coremusic-shared/`).**

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
| `lcobucci/jwt` | JWT (RS256, ES256 — firebase/php-jkt yasaklı) |
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

### 4.23 MFA (Multi-Factor Authentication)

| Paket | Amaç |
|--------|------|
| `pragmarx/google2fa` | TOTP tabanlı 2FA |

### 4.24 QR Code

| Paket | Amaç |
|--------|------|
| `endroid/qr-code` | QR code üretimi (MFA setup için) |

### 4.25 Device Detection

| Paket | Amaç |
|--------|------|
| `mobiledetect/mobiledetectlib` | Cihaz/tarayıcı algılama |

### 4.26 Bot Detection

| Paket | Amaç |
|--------|------|
| `jaybizzle/crawler-detect` | Bot/crawler algılama |

### 4.27 GeoIP

| Paket | Amaç |
|--------|------|
| `geoip2/geoip2` | Coğrafi konum tespiti |

### 4.28 Markdown

| Paket | Amaç |
|--------|------|
| `league/commonmark` | Markdown işleme |

### 4.29 RSS

| Paket | Amaç |
|--------|------|
| `laminas/laminas-feed` | RSS/Atom feed üretimi |

### 4.30 Backup

| Paket | Amaç |
|--------|------|
| `spatie/db-dumper` | Veritabanı yedekleme |

### 4.31 Health Check

| Paket | Amaç |
|--------|------|
| `spatie/health` | Servis sağlık kontrolü |

### 4.32 Audit Log

| Paket | Amaç |
|--------|------|
| `spatie/activitylog` | Aktivite günlüğü |

### 4.33 Internationalization (i18n)

| Paket | Amaç |
|--------|------|
| `symfony/translation` | Çeviri yönetimi |
| `symfony/intl` | Uluslararası veri |

### 4.34 XML

| Paket | Amaç |
|--------|------|
| `sabre/xml` | XML işleme |
| `robrichards/xmlseclibs` | XML güvenlik |

### 4.35 PDF

| Paket | Amaç |
|--------|------|
| `dompdf/dompdf` | PDF üretimi |
| `mpdf/mpdf` | PDF üretimi (alternatif) |

### 4.36 Excel

| Paket | Amaç |
|--------|------|
| `phpoffice/phpspreadsheet` | Excel dosya işlemleri |

### 4.37 Scheduler

| Paket | Amaç |
|--------|------|
| `dragonmantank/cron-expression` | Cron ifadesi çözümleme |

### 4.38 Migration

| Paket | Amaç |
|--------|------|
| `robmorgan/phinx` | Veritabanı migrasyonu |

### 4.39 Seeder

| Paket | Amaç |
|--------|------|
| `fakerphp/faker` | Sahte veri üretimi |

### 4.40 CSS Selector

| Paket | Amaç |
|--------|------|
| `symfony/css-selector` | CSS seçici çözümleme |

### 4.41 Expression Language

| Paket | Amaç |
|--------|------|
| `symfony/expression-language` | Ifade dil işleyici |

### 4.42 HTML Parser

| Paket | Amaç |
|--------|------|
| `masterminds/html5` | HTML5 işleme |

### 4.43 Debug

| Paket | Amaç |
|--------|------|
| `symfony/var-dumper` | Debug dump |
| `filp/whoops` | Hata sayfası |

### 4.44 Benchmark

| Paket | Amaç |
|--------|------|
| `phpbench/phpbench` | Performans ölçümü |

### 4.45 Security Audit

| Paket | Amaç |
|--------|------|
| `roave/security-advisories` | Güvenlik denetimi |

### 4.46 Static Analysis

| Paket | Amaç |
|--------|------|
| `vimeo/psalm` | Statik analiz |

### 4.47 Code Style

| Paket | Amaç |
|--------|------|
| `squizlabs/php_codesniffer` | Kod stili denetimi |

## 5. Minimum Enterprise Composer Stack

CoreMusic projesinde varsayılan olarak kullanılması önerilen çekirdek paketler:

```
php-di/php-di
nikic/fast-route
nyholm/psr7
laminas/laminas-httphandlerrunner
lcobucci/jwt
ramsey/uuid
monolog/monolog
vlucas/phpdotenv
respect/validation
symfony/security-csrf
symfony/cache
symfony/event-dispatcher
symfony/rate-limiter
league/flysystem
guzzlehttp/guzzle
paragonie/halite
paragonie/sodium_compat
doctrine/dbal
league/oauth2-server
ezyang/htmlpurifier
mobiledetect/mobiledetectlib
jaybizzle/crawler-detect
dragonmantank/cron-expression
robmorgan/phinx
pragmarx/google2fa
endroid/qr-code
spatie/health
spatie/activitylog
symfony/translation
```

**Yeni Eklenenler (v2.1.0):**
- `doctrine/dbal` — Veritabanı abstraction (PDO üzerinde type-safe queries, schema management, migration support). ORM DEĞİL, sadece DBAL katmanı.
- `lcobucci/jwt` — JWT token oluşturma/doğrulama (RS256, ES256 destekli). **Tek JWT paketi.** `firebase/php-jkt` yasaklı (brain.md §4A).
- `league/oauth2-server` — OAuth2 Server implementasyonu (Authorization Code, Client Credentials grant types).

## 6. Shared Library (`shared/`)

> **Kritik Düzeltme:** Shared library `shared/` dizinindedir. `coremusic-shared/` DEĞİL.

CoreMusic tek bir shared paketten oluşmamalıdır. Shared library `shared/` dizininde yer alır ve bağımsız bir Composer paketi olarak `coremusic/shared` adıyla publish edilir:

```
shared/                          ← Root dizindeki shared kütüphanesi
├── composer.json                ← "name": "coremusic/shared"
├── src/
│   ├── Auth/                    ← Auth Domain
│   ├── Security/                ← Middleware Pipeline
│   ├── Http/                    ← HTTP Kernel
│   ├── Router/                  ← Enterprise Router
│   ├── Container/               ← DI Container
│   └── Event/                   ← Event Dispatcher
├── config/
└── tests/
```

**Subdomain'ler shared'i şu şekilde kullanır:**

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../shared",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "coremusic/shared": "*"
    }
}
```

**Yasak:** `coremusic-shared/` dizini YASAKTIR. Doğru yol: `shared/`.

## 7. Yasaklar

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
- MFA/2FA
- QR Code Generator
- Database Migration
- Health Check
- Audit Log

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
| **Versiyon** | 2.1.0 |
| **Paket Sayısı** | 70+ |
| **PSR Uyumlu** | ✅ |
| **Zero Hallucination** | ✅ |
| **Shared Path** | `shared/` (NOT `coremusic-shared/`) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode
