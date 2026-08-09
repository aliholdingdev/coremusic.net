---
type: adr
category: security
title: "ADR-059 Enterprise Authentication & Security Standards"
date: 2026-08-09
version: 1.0.0
status: active
author: Bayram Ali
governance: Red Team · Human Mode · Truth Mode
---

# ADR-059: Enterprise Authentication & Security Standards

## Bağlam

CoreMusic platformunda kimlik doğrulama, yetkilendirme ve güvenlik katmanları PSR standartlarına uygun, Composer üzerinden yönetilen, aktif olarak geliştirilen, güvenlik denetiminden geçmiş, Enterprise seviyede kabul görmüş bileşenler kullanılarak geliştirilmelidir.

## Karar

### 1. Temel Prensip

> **Standart varsa onu kullan. PSR varsa ona uy. Güvenilir Composer paketi varsa onu tercih et. Güvenlik açısından kritik bileşenleri (JWT, CSRF, Kriptografi) sıfırdan yazma. Yalnızca projeye özgü iş kuralları ve domain mantığı özel olarak geliştirilecektir.**

### 2. Öncelik Sırası

1. PHP Native (built-in fonksiyonlar)
2. PSR Standardı (PSR-7, PSR-15, PSR-11 vb.)
3. Composer Paketi (güvenilir, aktif, PSR uyumlu)
4. Kuruma özel Domain Logic

### 3. Zorunlu Composer Paketleri

#### Authentication & Security
```json
{
  "firebase/php-jwt": "^6.10",
  "paragonie/halite": "^5.0",
  "symfony/security-csrf": "^7.1",
  "respect/validation": "^2.0",
  "symfony/rate-limiter": "^7.1",
  "ezyang/htmlpurifier": "^4.17"
}
```

#### HTTP & Router
```json
{
  "nyholm/psr7": "^1.8",
  "nyholm/psr7-server": "^1.2",
  "nikic/fast-route": "^1.3",
  "php-di/php-di": "^7.0",
  "guzzlehttp/guzzle": "^7.9"
}
```

#### Infrastructure
```json
{
  "vlucas/phpdotenv": "^5.6",
  "ramsey/uuid": "^4.7",
  "monolog/monolog": "^3.10",
  "symfony/cache": "^7.1",
  "symfony/event-dispatcher": "^7.1",
  "robmorgan/phinx": "^0.16"
}
```

#### Dev Tools
```json
{
  "phpunit/phpunit": "^11.0",
  "phpstan/phpstan": "^1.12",
  "friendsofphp/php-cs-fixer": "^3.65",
  "rector/rector": "^2.0",
  "roave/security-advisories": "^0.14"
}
```

### 4. Yasaklı Teknolojiler

| Yasaklı | Neden | Alternatif |
|---------|-------|------------|
| Doctrine ORM | Complex, heavy | PDO prepared statement |
| Laravel Eloquent | Framework bağımlılığı | PDO prepared statement |
| Propel ORM | Bakımsız | PDO prepared statement |
| MD5 | Güvensiz | Argon2id |
| SHA1 | Güvensiz | Argon2id |
| mcrypt | Deprecated | sodium/paragonie/halite |
| `SELECT *` | SQL injection riski | Explicit column list |
| `mysql_*` fonksiyonları | Deprecated | PDO |

### 5. Domain Logic Politikası

Sadece aşağıdaki alanlar projeye özel geliştirilecektir:

- Business Logic (CoreMusic'e özgü kurallar)
- Domain Rules (Kullanıcı, rol, izin kuralları)
- Audio Engine Logic (DSP, EQ, mixer)
- Playlist Engine (Oluşturma, yönetme)
- Recommendation Engine (AI önerileri)
- Media Engine (İndirme, dönüştürme)
- CoreMusic Service Layer (Servis entegrasyonu)
- Domain Events (Olay tetikleme)
- Application Services (Use case implementasyonları)

### 6. Güvenlik Standartları

#### Authentication Security
- Argon2id (64MB/t=4/p=2)
- Password Rehash (algorithm upgrade)
- Password Policy (min 8 karakter)
- Account Lockout (5 başarısız → 15dk lock)
- Failed Login Audit

#### Request Security
- CSRF Protection (csrf_token key, hash_equals)
- CSP (strict-dynamic, nonce-based)
- HSTS (HTTPS only)
- CORS (sadece tanımlı CoreMusic domainleri)
- Origin Validation
- Host Validation

#### Session Security
- HttpOnly Cookie
- Secure Cookie (HTTPS'de)
- SameSite=Lax
- Session Rotation (1800s)
- Session Regeneration (login sonrası)
- Idle Timeout (3600s)
- Absolute Timeout (86400s)
- Session Fingerprint (IP + User-Agent hash)

#### API Security
- JWT Validation (RS256)
- Rate Limiting (60 req/60s)
- API Key Validation (opsiyonel)
- Nonce Validation
- Timestamp Validation

### 7. PSR Standartları

| PSR | Amaç | Paket |
|-----|------|-------|
| PSR-3 | Logger | monolog/monolog |
| PSR-6 | Cache | symfony/cache |
| PSR-7 | HTTP Message | nyholm/psr7 |
| PSR-11 | Container | php-di/php-di |
| PSR-14 | Event Dispatcher | symfony/event-dispatcher |
| PSR-15 | HTTP Server Middleware | psr/http-server-middleware |
| PSR-17 | HTTP Factories | psr/http-factory |
| PSR-18 | HTTP Client | guzzlehttp/guzzle |

### 8. Composer Paket Seçim Kriterleri

Herhangi bir paket eklenmeden önce:

1. PSR standartlarını destekliyor mu?
2. PHP 8.4 ile uyumlu mu?
3. Son sürümü aktif olarak geliştiriliyor mu?
4. Güvenlik açıkları bulunuyor mu? (CVE, GitHub Advisory)
5. GitHub aktivitesi devam ediyor mu?
6. Enterprise projelerde kullanılıyor mu?
7. Dokümantasyonu yeterli mi?
8. Bakımı bırakılmış mı?
9. Lisansı uygun mu? (MIT, BSD, Apache tercih)

## Sonuçlar

### Olumlu
- Enterprise seviyesinde güvenlik sağlar
- PSR uyumluluğu ile interoperabilite
- Bakım maliyeti düşer
- Teknik borç azalır
- Gereksiz kod yazımı engellenir

### Olumsuz
- İlk yatırım maliyeti yüksek
- Paket bağımlılıkları artar
- Güncelleme takibi gerektirir

## İlgili ADR'ler

- [[ADR-052-hybrid-auth-architecture]] — Hybrid Auth mimarisi
- [[ADR-053-enterprise-router-architecture]] — Enterprise Router
- [[ADR-054-enterprise-composer-stack]] — Composer paketleri
- [[ADR-010-csrf-protection-strategy]] — CSRF koruması
- [[ADR-022-database-hardened-security]] — DB güvenlik

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
