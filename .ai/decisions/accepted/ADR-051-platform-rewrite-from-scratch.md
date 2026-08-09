---
type: adr
category: architecture
title: "ADR-051: Platform Rewrite from Scratch"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-051: Platform Rewrite from Scratch

**Status:** Active
**Kategorisi:** Architecture
**İlgili Agent:** [[.agents/backend-architect]], [[.agents/ui-designer]]
**İlgili Division:** Software Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformunun `C:\www\coremusic.net` dizininde **sıfırdan** yeniden yazılmasını, referans projeden (`C:\www\coremusic.net - Kopya (3)`) sadece mimarinin referans olarak alınmasını, kodun kesinlikle kopyalanmamasını ve yeni mimarinin temel prensiplerini tanımlar.

---

## 2. Bağlam

### 2.1 Referans Proje

| Alan | Değer |
|------|-------|
| Konum | `C:\www\coremusic.net - Kopya (3)` |
| Kullanım | **Sadece mimari referans** |
| Kod Kopyalama | **KESİNLİKLE YASAK** |
| Amaç | Mevcut sistem analiz edilerek yeni mimari tasarlanması |

### 2.2 Hedef Proje

| Alan | Değer |
|------|-------|
| Konum | `C:\www\coremusic.net` |
| Durum | Sıfırdan geliştirme |
| Mevcut | Sadece `.ai/` vault ve `.claude/` kuralları |

### 2.3 Referans Proje Yapısı (Sadece Mimari Referans)

Referans projede şu yapı incelenmiştir:

```
C:\www\coremusic.net - Kopya (3)\
├── coremusic-shared/          ← Shared infrastructure (composer, junction)
├── assets.coremusic.net/      ← CSS/JS assets (ITCSS, SPA router)
├── music.coremusic.net/       ← Music panel (PHP backend)
├── admin.coremusic.net/       ← Admin panel
├── auth.coremusic.net/        ← Auth service
├── home.coremusic.net/        ← Home media center
├── media.coremusic.net/       ← Media service
├── download.coremusic.net/    ← Download service (Node.js)
├── car.coremusic.net/         ← Car audio
├── studio.coremusic.net/      ← Studio audio
├── pro.coremusic.net/         ← Professional panel
└── .ai/                       ← Vault
```

### 2.4 Problemler

| # | Problem | Açıklama |
|---|---------|----------|
| P1 | Mimari bozulma | Katman ihlalleri, SOLID ihlalleri mevcut |
| P2 | Teknik borç | Kod kalitesi düşük, bakımı zor |
| P3 | Güvenlik açıkları | Eski auth mekanizması yetersiz |
| P4 | Ölçeklenebilirlik | Mevcut yapı yeni gereksinimleri karşılamıyor |
| P5 | Bakım maliyeti | Dağınık kod, tekrarlar, ölü kod |

### 2.5 Gereksinimler

| # | Gereksinim | Kaynak |
|---|------------|--------|
| R1 | Sıfırdan geliştirme | Kullanıcı talimatı |
| R2 | Referans projeden kod kopyalanmaz | Kullanıcı talimatı |
| R3 | PHP 8.4 backend | ADR-042 |
| R4 | Vanilla JS frontend | ADR-001 |
| R5 | SPA Router | ADR-021 |
| R6 | Merkezi auth | ADR-043 |
| R7 | PSR standartları | Kullanıcı talimatı |
| R8 | Composer paketleri | Kullanıcı talimatı |
| R9 | SOLID + Clean + Hexagonal | Kullanıcı talimatı |
| R10 | Enterprise seviye | Kullanıcı talimatı |

---

## 3. Karar

CoreMusic platformu `C:\www\coremusic.net` dizininde **sıfırdan** yeniden yazılacaktır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| Sıfırdan yazım | Mevcut kod kopyalanmaz | Bu ADR |
| Mimari referans | Referans proje sadece yapı için incelenir | Bu ADR |
| PHP 8.4 | strict_types, PSR-12 | ADR-042 |
| Vanilla JS | Framework yasak | ADR-001 |
| SPA Router | pushState tabanlı | ADR-021 |
| Merkezi Auth | auth.coremusic.net | ADR-043 |
| Hybrid Auth | Session + JWT | ADR-052 |
| PSR Standards | PSR-7, PSR-11, PSR-15, PSR-17 | ADR-054 |
| Composer | Enterprise paketler | ADR-054 |
| Clean Architecture | L0-L3 katmanları | Bu ADR |
| Hexagonal | Adapter/Port pattern | Bu ADR |
| SOLID | Tüm katmanlarda | Bu ADR |
| DDD | Domain-Driven Design | Bu ADR |

### 3.2 Proje Yapısı (Yeni)

```
C:\www\coremusic.net\
├── .ai/                                   ← Vault (mevcut, güncellenecek)
├── .claude/                               ← Claude kuralları (mevcut, güncellenecek)
├── .opencode/                             ← OpenCode config (mevcut)
├── shared/                                ← Shared infrastructure
│   ├── composer.json                      ← Ortak Composer dependencies
│   ├── src/
│   │   ├── Auth/                          ← Auth domain
│   │   │   ├── Domain/
│   │   │   │   ├── User.php               ← Domain entity
│   │   │   │   ├── Role.php               ← Domain entity
│   │   │   │   ├── Session.php            ← Domain entity
│   │   │   │   ├── Token.php              ← Domain entity (JWT)
│   │   │   │   └── Repository/
│   │   │   │       ├── UserRepositoryInterface.php
│   │   │   │       ├── SessionRepositoryInterface.php
│   │   │   │       └── TokenRepositoryInterface.php
│   │   │   ├── Application/
│   │   │   │   ├── LoginUseCase.php
│   │   │   │   ├── LogoutUseCase.php
│   │   │   │   ├── RegisterUseCase.php
│   │   │   │   ├── RefreshTokenUseCase.php
│   │   │   │   ├── ValidateSessionUseCase.php
│   │   │   │   └── DTO/
│   │   │   │       ├── LoginRequest.php
│   │   │   │       ├── LoginResponse.php
│   │   │   │       └── TokenPair.php
│   │   │   └── Infrastructure/
│   │   │       ├── Persistence/
│   │   │       │   ├── PdoUserRepository.php
│   │   │       │   ├── PdoSessionRepository.php
│   │   │       │   └── PdoTokenRepository.php
│   │   │       ├── Security/
│   │   │       │   ├── Argon2idPasswordHasher.php
│   │   │       │   ├── JwtTokenManager.php
│   │   │       │   └── CsrfTokenManager.php
│   │   │       └── Http/
│   │   │           ├── AuthApiClient.php
│   │   │           └── AuthMiddleware.php
│   │   ├── Security/                      ← Security domain
│   │   │   ├── Middleware/
│   │   │   │   ├── SessionManagerMiddleware.php
│   │   │   │   ├── BypassAuthMiddleware.php
│   │   │   │   ├── RateLimiterMiddleware.php
│   │   │   │   ├── AuthMiddleware.php
│   │   │   │   ├── SecurityHeadersMiddleware.php
│   │   │   │   └── CsrfMiddleware.php
│   │   │   └── Service/
│   │   │       ├── CspNonceGenerator.php
│   │   │       ├── RateLimiter.php
│   │   │       └── SecurityHeaderService.php
│   │   ├── Http/                          ← HTTP layer (PSR-7)
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
│   │   └── .env                           ← Environment (gitignored)
│   └── tests/
│       ├── Unit/
│       ├── Integration/
│       └── E2E/
├── auth.coremusic.net/                    ← Auth service
│   └── index.php                          ← Entry point
│   ├── include/
│   │   ├── Controller/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   ├── LogoutController.php
│   │   │   ├── PasswordResetController.php
│   │   │   └── SessionCheckController.php
│   ├──pages/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── reset-password.php
│   └── config/
│   │       └── routes.php
│   └── tests/
├── music.coremusic.net/                   ← Music panel
│   └── index.php
│   ├── include/
│   │   ├── Controller/
│   │   │   ├── MusicController.php
│   │   │   ├── PlaylistController.php
│   │   │   └── ApiController.php
│   ├──pages/
│   │   ├── home.php
│   │   ├── kesfet.php
│   │   └── playlist.php
│   └── config/
│   │       └── routes.php
│   └── tests/
├── admin.coremusic.net/                   ← Admin panel
│   └── index.php
│   ├── include/
│   ├── pages/
│   └── tests/
├── home.coremusic.net/                    ← Home media center (RPI5)
│   └── index.php
│   ├── include/
│   ├── pages/
│   └── tests/
├── studio.coremusic.net/                  ← Studio audio (RPI5)
├── pro.coremusic.net/                     ← Professional (RPI5)
├── car.coremusic.net/                     ← Car audio (RPI5)
├── media.coremusic.net/                   ← Media service
├── download.coremusic.net/                ← Download service
├── landing.coremusic.net/                 ← Landing page
├── api.coremusic.net/                     ← API service
├── composer.json                          ← Root composer
├── package.json                           ← Root npm
├── .env.example                           ← Env template
├── phpunit.xml                            ← PHPUnit config
├── phpstan.neon                           ← PHPStan config
├── .php-cs-fixer.php                      ← CS Fixer config
├── README.md
├── AGENTS.md                              ← Bootstrap pointer
├── CLAUDE.md                              ← Bootstrap pointer
└── WORKFLOW.md                            ← Bootstrap pointer
```

### 3.3 Katman Mimarisi (Clean Architecture)

```
┌─────────────────────────────────────────────────────┐
│                  L3 PRESENTATION                     │
│  Vanilla JS · ITCSS · SPA Router · DOMParser         │
│  TrustedTypes · Web Audio API                        │
├─────────────────────────────────────────────────────┤
│                  L2 ROUTING                          │
│  Enterprise Router · PSR-15 Middleware               │
│  Route Attributes · DI Container (PSR-11)            │
│  Controller Dispatch · Route Cache                   │
├─────────────────────────────────────────────────────┤
│                  L1 SECURITY                         │
│  SessionManager · Auth · CSRF · CSP · RateLimiter    │
│  SecurityHeaders · JWT · Argon2id · AES-256-GCM      │
├─────────────────────────────────────────────────────┤
│                  L0 INFRASTRUCTURE                   │
│  PDO · MySQL 9 BCNF · APCu · Redis · Filesystem      │
│  PSR-3 Logger · PSR-6 Cache · Event Dispatcher       │
└─────────────────────────────────────────────────────┘
```

**Bağımlılık kuralları:**
- ✅ L3 → L2, L2 → L1, L1 → L0
- ❌ L0 → L2/L3, L1 → L3, L3 → L0

### 3.4 Middleware Pipeline (Frozen — ADR-010/011/012/013/022)

```
Request → SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf → Controller
```

### 3.5 Auth Akışı (Merkezi — ADR-043/047)

```
Panel (music/home/studio/pro/car)
    │
    ├─ Cookie var mı?
    │   ├── Evet → auth.coremusic.net/api/session/check
    │   │         ├── 200 OK → Devam
    │   │         └── 401 → Redirect login
    │   └── Hayır → Redirect auth.coremusic.net/login?return=/path
    │
    └─ auth.coremusic.net
        ├── Login formu göster
        ├── POST /login → Argon2id doğrula
        ├── Başarılı → Cookie set (.coremusic.net) → Redirect return
        └── Başarısız → Hata + rate limit
```

---

## 4. Teknik Detaylar

### 4.1 PHP Konfigürasyonu

| Ayar | Değer |
|------|-------|
| Version | 8.4+ |
| strict_types | Zorunlu her dosyada |
| Coding Standard | PSR-12 |
| Autoloading | Composer PSR-4 |
| Error Reporting | E_ALL (dev) / 0 (prod) |
| Display Errors | On (dev) / Off (prod) |

### 4.2 Frontend Konfigürasyonu

| Ayar | Değer |
|------|-------|
| JS | Vanilla ES6+ (framework yasak) |
| CSS | ITCSS 7-layer + BEM |
| Router | SPA pushState (ADR-021) |
| Security | DOMParser + TrustedTypes |
| State | Session-based (localStorage auth yasak) |

### 4.3 Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net | HTTP |
| 443 | Tüm servisler | HTTPS (prod) |
| 4433 | Geliştirme ortamı | HTTPS (dev) |
| 3306 | MySQL 9 | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |

### 4.4 Desteklenen Domainler

```
coremusic.net              → Landing page
music.coremusic.net        → Music panel (port 81)
admin.coremusic.net        → Admin panel (port 80)
api.coremusic.net          → API service
media.coremusic.net        → Media service (port 5000/6000)
download.coremusic.net     → Download service (port 3001)
auth.coremusic.net         → Auth service
home.coremusic.net         → Home media center (RPI5)
studio.coremusic.net       → Studio audio (RPI5)
pro.coremusic.net          → Professional (RPI5)
car.coremusic.net          → Car audio (RPI5)
```

### 4.5 Development vs Production

| Özellik | Development | Production |
|---------|-------------|------------|
| Protocol | HTTP | HTTPS |
| Ports | 80, 81, 4433 | 80, 81, 443 |
| Error Display | On | Off |
| CSP | Report-only | Enforce |
| Rate Limit | Devre dışı | Aktif |
| BypassAuth | Aktif (`?_bypass=1`) | Devre dışı |
| Cache | Off | APCu + Redis |
| Log Level | DEBUG | WARNING |

---

## 5. Yasak Örüntüleri

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Referans projeden kod kopyalama | Sadece mimari referans | Bu ADR |
| 2 | Framework kullanımı | Vanilla JS + PHP native | ADR-001 |
| 3 | ORM kullanımı | Raw PDO | ADR-002 |
| 4 | `SELECT *` | Açık sütun listesi | ADR-002 |
| 5 | Hardcoded secret | `.env` / credential vault | ADR-034 |
| 6 | `localStorage` auth | Cookie-based session | ADR-011 |
| 7 | `_csrf_token` | `csrf_token` | ADR-010 |
| 8 | Middleware sırası değiştirme | Frozen pipeline | ADR-010/011/012/013/022 |
| 9 | Dağınık auth | Merkezi auth.coremusic.net | ADR-043 |
| 10 | Plan olmadan kod | Zero Code Before Plan | ADR-007 |

---

## 6. Edge Cases

| # | Edge Case | Çözüm | ADR |
|---|-----------|-------|-----|
| 1 | Referans proje erişimi yok | Vault docs yeterli | Bu ADR |
| 2 | Eski session'lar | Migration scripti | ADR-014 |
| 3 | Cookie domain uyumsuzluğu | `.coremusic.net` wildcard | ADR-043 |
| 4 | Auth servisi down | Cached auth fallback | ADR-043 |
| 5 | Eski URL'ler | 301 redirect mapping | ADR-009 |
| 6 | RPI5 kaynak kısıtı | Lightweight frontend | Bu ADR |
| 7 | Multi-domain CSRF | Session-bound token | ADR-010 |
| 8 | Eski veritabanı | BCNF migration | ADR-040 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Kod kopyalama yasak | Referans proje sadece referans | Mimari bozulma |
| G2 | Zero Code Before Plan | Plan onayı olmadan kod yok | Mimari bozulma |
| G3 | MSA Limit = 15 dosya | Görev başına max 15 dosya | Token aşımı |
| G4 | Middleware sırası frozen | Değiştirilmez | CSP/CSRF bozulması |
| G5 | csrf_token key | Değiştirilmez | CSRF bozulması |
| G6 | ORM yasak | Sadece PDO | SQL injection |
| G7 | Framework yasak | Sadece Vanilla JS | Bağımlılık artışı |
| G8 | Merkezi auth | auth.coremusic.net | Dağınık güvenlik |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend standardı |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | Veritabanı |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Subdomain mimarisi |
| [[ADR-007-cache-namespace]] | Zero Code Before Plan | Süreç |
| [[ADR-009-clean-url-redirect]] | Clean URL | URL yapısı |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | Güvenlik |
| [[ADR-011-session-management]] | Session yönetimi | Oturum |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Routing |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | PHP 8.4, port 81 |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu | Merkezi auth |
| [[ADR-052-hybrid-auth-architecture]] | Hybrid auth | Session + JWT |
| [[ADR-053-enterprise-router-architecture]] | Enterprise router | PSR-based router |
| [[ADR-054-enterprise-composer-stack]] | Composer stack | Paket politikası |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2.1 | Referans proje | Mimari referans |
| § 3.2 | Yeni proje yapısı | Sıfırdan tasarım |
| § 3.3 | [[architecture/l0-infrastructure]] | L0-L3 katmanları |
| § 3.4 | [[architecture/l1-security]] | Middleware pipeline |
| § 3.5 | [[ADR-043-auth-subdomain-consolidation]] | Auth akışı |
| § 4.1 | [[.claude/rules/php-standards.md]] | PHP kuralları |
| § 4.2 | [[.claude/rules/js-standards.md]] | JS kuralları |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Sıfırdan Yazım** | Mevcut kod kullanılmadan yeni geliştirme |
| **Mimari Referans** | Sadece yapı ve tasarım için inceleme |
| **Clean Architecture** | Katmanlı mimari yaklaşım |
| **Hexagonal Architecture** | Adapter/Port pattern |
| **SOLID** | Tek Sorumluluk, Açık Kapalılık vb. |
| **DDD** | Domain-Driven Design |
| **PSR** | PHP Standards Recommendations |
| **Enterprise** | Kurumsal seviye kalite standartı |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 1.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-09 |
| Sections | 11 |
| Gereksinim | 10 |
| Yasak Örüntü | 10 |
| Edge Cases | 8 |
| Hard Guardrails | 8 |
| İlgili ADR | 13 |
| Çapraz Referans | 7 |
| Sözlük Terim | 9 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
