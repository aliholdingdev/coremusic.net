---
type: adr
category: infrastructure
title: "ADR-054: Enterprise Composer Stack"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-054: Enterprise Composer Stack

**Status:** Active
**Kategorisi:** Infrastructure
**İlgili Agent:** [[.agents/backend-architect]]
**İlgili Division:** Software Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunda kullanılacak **tüm Composer paketlerinin** listesini, seçim kriterlerini, minimum Enterprise stack'ini ve yasaklı teknolojileri tanımlar.

Temel ilke: **"Build Business Logic, Not Infrastructure."**

Altyapı bileşenleri mümkün olduğunca standartlar ve güvenilir Composer paketleri üzerine inşa edilecek; yalnızca CoreMusic'e özgü iş kuralları özel olarak geliştirilecektir.

---

## 2. Bağlam

### 2.1 Gereksinimler

| # | Gereksinim | Açıklama |
|---|------------|----------|
| R1 | PSR Uyumlu | Tüm paketler PSR standartlarına uygun |
| R2 | PHP 8.4 | Güncel PHP sürümü desteği |
| R3 | Aktif Geliştirme | Bakımı devam eden paketler |
| R4 | Güvenlik | CVE geçmişi temiz |
| R5 | Lisans | MIT, BSD, Apache 2.0 tercih |
| R6 | Enterprise | Kurumsal projelerde kullanılmış |
| R7 | Dokümantasyon | Yeterli dokümantasyon |
| R8 | Test | Yeterli test kapsamı |

### 2.2 Seçim Kriterleri

Her paket eklenmeden önce kontrol edilir:

| # | Kriter | Kontrol |
|---|--------|---------|
| 1 | PSR uyumluluğu | PSR-3, PSR-7, PSR-11, PSR-14, PSR-15, PSR-16 |
| 2 | PHP 8.4 desteği | `composer.json` → `php: ^8.4` |
| 3 | Son sürüm | Packagist → son 6 ay içinde release |
| 4 | GitHub aktivitesi | Son 3 ayda commit |
| 5 | Güvenlik | `composer audit` temiz |
| 6 | Lisans | MIT / BSD / Apache 2.0 |
| 7 | Enterprise kullanım | GitHub stars, downloads |
| 8 | Bakım durumu | Deprecated / Archived / Abandoned değil |

---

## 3. Karar

CoreMusic'te **Minimum Enterprise Composer Stack** kullanılacaktır.

### 3.1 Minimum Enterprise Composer Stack

```json
{
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
    }
}
```

### 3.2 Paket Kategorileri

#### PSR Standartları

| Paket | PSR | Amaç |
|-------|-----|------|
| `psr/log` | PSR-3 | Logger interface |
| `psr/container` | PSR-11 | Container interface |
| `psr/http-message` | PSR-7 | HTTP message interface |
| `psr/http-server-handler` | PSR-15 | Server request handler |
| `psr/http-server-middleware` | PSR-15 | Server middleware |
| `psr/http-factory` | PSR-17 | HTTP factory |
| `psr/event-dispatcher` | PSR-14 | Event dispatcher |
| `psr/cache` | PSR-6 | Cache interface |

#### HTTP Layer

| Paket | Amaç | Neden |
|-------|------|-------|
| `nyholm/psr7` | PSR-7 implementation | Hafif, PHP 8.4 uyumlu |
| `nyholm/psr7-server` | PSR-15 server | Request/Response factory |
| `guzzlehttp/guzzle` | HTTP client | Auth API calls |

#### Router

| Paket | Amaç | Neden |
|-------|------|-------|
| `nikic/fast-route` | Router engine | Performans, PHP 8.4 uyumlu |

#### Dependency Injection

| Paket | Amaç | Neden |
|-------|------|-------|
| `php-di/php-di` | DI Container | PSR-11, attribute desteği |

#### Security

| Paket | Amaç | Neden |
|-------|------|-------|
| `firebase/php-jwt` | JWT encoding/decoding | Enterprise standard |
| `symfony/security-csrf` | CSRF token management | PSR uyumlu, güvenilir |
| `paragonie/halite` | Encryption | Sodium wrapper, güvenilir |
| `ezyang/htmlpurifier` | HTML sanitization | OWASP uyumlu |

#### Validation

| Paket | Amaç | Neden |
|-------|------|-------|
| `respect/validation` | Input validation | Fluent API, PSR uyumlu |

#### Rate Limiting

| Paket | Amaç | Neden |
|-------|------|-------|
| `symfony/rate-limiter` | Rate limiting | PSR uyumlu, esnek |

#### Cache

| Paket | Amaç | Neden |
|-------|------|-------|
| `symfony/cache` | PSR-6 cache | APCu, Redis, file adapters |

#### Event System

| Paket | Amaç | Neden |
|-------|------|-------|
| `symfony/event-dispatcher` | Event dispatcher | PSR-14 uyumlu |

#### UUID

| Paket | Amaç | Neden |
|-------|------|-------|
| `ramsey/uuid` | UUID generation | v4, v7 desteği |

#### Environment

| Paket | Amaç | Neden |
|-------|------|-------|
| `vlucas/phpdotenv` | .env file loading | Industry standard |

#### Logger

| Paket | Amaç | Neden |
|-------|------|-------|
| `monolog/monolog` | PSR-3 logger | Enterprise standard |

#### Database Migration

| Paket | Amaç | Neden |
|-------|------|-------|
| `robmorgan/phinx` | Database migration | PHP tabanlı, esnek |

#### Scheduler

| Paket | Amaç | Neden |
|-------|------|-------|
| `dragonmantank/cron-expression` | Cron scheduling | Cron expression parser |

#### Testing (Dev)

| Paket | Amaç | Neden |
|-------|------|-------|
| `phpunit/phpunit` | Unit testing | PHPUnit 11 |
| `phpstan/phpstan` | Static analysis | Level 10 |
| `friendsofphp/php-cs-fixer` | Code style | PSR-12 |
| `rector/rector` | Automated refactoring | PHP 8.4 migration |
| `roave/security-advisories` | Security | CVE prevention |

### 3.3 Password Hashing (PHP Native — Ek Paket Gerekmez)

```php
<?php
declare(strict_types=1);

// Hash — PHP native, ek paket gerektirmez
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64MB
    'time_cost' => 4,        // 4 iterations
    'threads' => 2,          // 2 threads
]);

// Verify
if (password_verify($input, $storedHash)) {
    // Başarılı
}
```

### 3.4 Database (PDO — ORM Yasak)

```php
<?php
declare(strict_types=1);

// ORM KULLANIMI YASAKTIR (ADR-002)
// Sadece PDO prepared statement

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,  // Zorunlu!
]);

// ✅ DOĞRU — Prepared statement
$stmt = $pdo->prepare(
    'SELECT id, title, artist_id FROM songs WHERE is_deleted = 0 AND id = :id'
);
$stmt->execute([':id' => $songId]);
$song = $stmt->fetch();

// ❌ YASAK — SQL injection riski
// $result = $pdo->query("SELECT * FROM songs WHERE id = $songId");
```

---

## 4. Yasaklı Teknolojiler

| # | Yasaklı | Neden | Alternatif |
|---|---------|-------|------------|
| 1 | Doctrine ORM | ORM yasak (ADR-002) | PDO |
| 2 | Laravel Eloquent | ORM yasak (ADR-002) | PDO |
| 3 | Propel ORM | ORM yasak (ADR-002) | PDO |
| 4 | Active Record | ORM yasak (ADR-002) | Repository Pattern |
| 5 | MD5 | Güvensiz | Argon2id |
| 6 | SHA1 | Güvensiz | Argon2id |
| 7 | mcrypt | Deprecated | paragonie/halite |
| 8 | `SELECT *` | SQL injection riski | Açık sütun listesi |
| 9 | `mysql_*` fonksiyonları | Deprecated | PDO |
| 10 | Kendi JWT implementasyonu | Güvenlik riski | firebase/php-jwt |
| 11 | Kendi CSRF implementasyonu | Gerek yok | symfony/security-csrf |
| 12 | Kendi Cryptography | Güvenlik riski | paragonie/halite |
| 13 | Framework (Laravel, Symfony app) | Bağımlılık | Vanilla PHP |
| 14 | Bakımsız paketler | Güvenlik riski | Aktif paketler |

---

## 5. Yasak Örüntüleri

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | ORM | PDO prepared statement | ADR-002 |
| 2 | `SELECT *` | Açık sütun listesi | ADR-002 |
| 3 | Hardcoded secret | `.env` / credential vault | ADR-034 |
| 4 | Kendi JWT | `firebase/php-jwt` | Bu ADR |
| 5 | Kendi CSRF | `symfony/security-csrf` | Bu ADR |
| 6 | Kendi şifreleme | `paragonie/halite` | ADR-022 |
| 7 | Deprecated paket | Güncel alternatif | Bu ADR |
| 8 | Güvenilmez paket | `composer audit` temiz | Bu ADR |

---

## 6. Edge Cases

| # | Edge Case | Çözüm | ADR |
|---|-----------|-------|-----|
| 1 | Paket güvenlik açığı | `composer audit` + update | Bu ADR |
| 2 | Paket deprecated | Alternatif bul, migrate | Bu ADR |
| 3 | PHP 8.4 uyumsuzluğu | Versiyon kısıtlaması | ADR-042 |
| 4 | Lisans değişikliği | Lisans kontrolü | Bu ADR |
| 5 | Bağımlılık çelişkisi | `composer why-not` | Bu ADR |
| 6 | Performans sorunu | Benchmark + alternatif | Bu ADR |
| 7 | Bakım bırakma | Monitoring + migration planı | Bu ADR |
| 8 | CVE çıkışı | Hızlı patch + security update | Bu ADR |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | ORM yasak | Sadece PDO | SQL injection |
| G2 | `SELECT *` yasak | Açık sütun listesi | SQL injection |
| G3 | `composer audit` temiz | Güvenlik taraması | Güvenlik açığı |
| G4 | PSR uyumlu | Tüm paketler | Uyumsuzluk |
| G5 | PHP 8.4 | Güncel sürüm | Uyumsuzluk |
| G6 | Aktif geliştirme | Bakımı devam eden | Teknik borç |
| G7 | MIT/BSD/Apache | Lisans uygunluğu | Hukuki risk |
| G8 | Deprecated değil | Güncel alternatif | Teknik borç |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS | Frontend stack |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | Veritabanı |
| [[ADR-022-database-hardened-security]] | Güvenlik | Şifreleme |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Secret yönetimi |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | PHP 8.4 |
| [[ADR-051-platform-rewrite-from-scratch]] | Platform rewrite | Sıfırdan yazım |
| [[ADR-052-hybrid-auth-architecture]] | Hybrid auth | Auth paketleri |
| [[ADR-053-enterprise-router-architecture]] | Enterprise router | Router paketi |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 9 |
| Toplam Paket | 25 (require) + 5 (require-dev) |
| PSR Standardı | 8 (PSR-3/6/7/11/14/15/17) |
| Kategori | 12 |
| Yasaklı Teknoloji | 14 |
| Yasak Örüntü | 8 |
| Edge Cases | 8 |
| Hard Guardrails | 8 |
| İlgili ADR | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
