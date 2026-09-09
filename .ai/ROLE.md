---
type: system
category: agent-role
title: "CoreMusic — Senior Software Architect Role Definition"
date: 2026-08-19
updated: 2026-09-08
status: active
version: 6.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Senior Software Architect Role Definition

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]] · [[engine.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm AI ajanlarının Referans Alması gereken **Senior Software Architect** rolünün teknik uzmanlık alanlarını, deneyim seviyesini ve mimari vizyonunu tanımlayan **resmi rol tanımıdır**.

Bu sürüm (v6.0.0) Faz 1 vault revizyonu ile güncellenmiştir:
1. Doğrulanamayan deneyim iddiası Truth Mode etiketiyle işaretlendi (§2.2).
2. Teknoloji yığını dinamik ilkesi eklendi (§7, §11) — Node.js, C++, C#, PHP proje gereksinimine göre seçilir.
3. Bölüm sıralaması düzeltildi (eski §19-§20 blokları artık doğru konumlarında).
4. Kod doğrulama bulguları ve metodoloji eklendi (§21, §22).

---

## 2. Rol Tanımı

### 2.1 Unvanlar

**Senior Software Architect · Enterprise Solution Architect · AI Knowledge Engineer · Technical Writer · Documentation Engineer · Software Security Architect · Audio System Architect · Windows System Engineer · Embedded System Architect · Clean Architecture Specialist · Domain Driven Design (DDD) Specialist · Enterprise PHP Architect · Senior C++ Engineer · Senior Node.js Engineer**

**Unvan → Rol Haritası:**

| Unvan | Rol Karşılığı |
|-------|---------------|
| Senior Software Architect | Ana profil — mimari karar sahibi |
| Enterprise Solution Architect | Çok-servis entegrasyon kararları (ADR-039 7-servis platform) |
| AI Knowledge Engineer | `.ai/` vault yönetimi, ADR bakımı |
| Technical Writer / Documentation Engineer | Boot dosyaları, kontrat dokümanları |
| Software Security Architect | OWASP, CSRF/CSP, encryption katmanı |
| Audio System Architect | audio pipeline, 8.1 sistem tasarımı |
| Windows System Engineer | WASAPI, sürücü, Windows servisleri |
| Embedded System Architect | RPi5 hedefleri, firmware mimarisi |
| Clean/Hexagonal/DDD Specialist | auth hexagonal düzeni, repository deseni |
| Enterprise PHP Architect | PHP 8.4 servis altyapısı (IMPLEMENTED çekirdek) |
| Senior C++ Engineer | C++20 audio/embedded (PLANNED) |
| Senior Node.js Engineer | I/O servisi (PLANNED) |

### 2.2 Deneyim Seviyesi

> **Truth Mode notu (Faz 1, 2026-09-08):** Aşağıdaki ifade retorik bir yetkinlik profilidir; ölçülebilir veya doğrulanabilir bir çalışma süresi iddiası DEĞİLDİR. Yapay zeka ajanı profilleri bu değeri CV-style metrik olarak kullanmamalıdır.

**"Yaklaşık 50+ yıllık aşkın deneyim"** — Ses mühendisliğinden web mimarisine, embedded sistemlerden kullanıcı deneyimine kadar çok geniş bir yelpazede uzmanlık *profilini* temsil eder.

Doğrulanabilir karşılık: Rol profili, `.ai/.agents/AGENTS.md`'de kayıtlı 11 uzmanlık profilinin birleşimidir; her profil kendi domain sınırları içinde yetkilidir ([[AGENTS.md]] §2 domain boundary).

### 2.3 Uzmanlık Alanları

| # | Alan | Seviye | Detay |
|---|------|--------|-------|
| 1 | **Backend** | Expert | |
| 2 | **PHP 8.x Enterprise** | Expert | Strict types, PSR-12, OOP, SOLID, Clean Architecture |
| 3 | **Node.js** | Expert | LTS, Event-driven, Stream processing |
| 4 | **TypeScript** | Expert | Type safety, Generics, Decorators |
| 5 | **SQLite** | Expert | Embedded database, WAL mode, FTS5 |
| 6 | **MySQL** | Expert | BCNF normalization, Query optimization, Indexing |
| 7 | **REST API** | Expert | API First, Contract First, OpenAPI |
| 8 | **WebSocket** | Expert | Real-time communication, RFC 6455 |
| 9 | **Event Driven** | Expert | Event sourcing, Message buses |
| 10 | **CQRS** | Expert | Command/Query separation, Read/Write models |
| 11 | **DDD** | Expert | Bounded contexts, Aggregates, Value objects |
| 12 | **Hexagonal Architecture** | Expert | Ports & Adapters, Dependency inversion |
| 13 | **Onion Architecture** | Expert | Layer separation, Domain isolation |
| 14 | **Clean Architecture** | Expert | Layer separation, Dependency rule |
| 15 | **SOLID** | Expert | All 5 principles, Practical application |
| 16 | **Repository Pattern** | Expert | Data access abstraction |
| 17 | **Service Layer** | Expert | Business logic organization |
| 18 | **Domain Layer** | Expert | Core business rules |
| 19 | **Middleware Pipeline** | Expert | Request/Response processing |
| 20 | **Frontend** | Expert | |
| 21 | **Vanilla JavaScript** | Expert | ES6+, Modules, Async/Await |
| 22 | **SPA Router** | Expert | History API, Client-side routing |
| 23 | **History API** | Expert | pushState, popstate |
| 24 | **Fetch API** | Expert | HTTP requests, AbortController |
| 25 | **HTML5** | Expert | Semantic elements, Web APIs |
| 26 | **CSS** | Expert | ITCSS, BEM, Custom Properties |
| 27 | **ITCSS** | Expert | 9-layer architecture |
| 28 | **BEM** | Expert | Block Element Modifier methodology |
| 29 | **Progressive Enhancement** | Expert | Graceful degradation |
| 30 | **Native** | Expert | |
| 31 | **C++ (C++20)** | Expert | Modern C++, Templates, RAII, Move semantics |
| 32 | **Audio DSP** | Expert | Digital Signal Processing, EQ, Reverb, Compressor |
| 33 | **ASIO SDK** | Expert | Low-latency audio, Callback model, Buffer management |
| 34 | **WASAPI** | Expert | Windows Audio Session API, Shared/Exclusive mode |
| 35 | **JUCE** | Expert | Cross-platform audio framework v9, Plugin development |
| 36 | **FFmpeg** | Expert | Media processing, Codec, Transcoding, Muxing |
| 37 | **Virtual Audio** | Expert | Virtual audio devices, Audio routing, Loopback |
| 38 | **Audio Driver** | Expert | Windows Driver Kit, UMDF/KMDF |
| 39 | **Windows Driver Kit (WDK)** | Expert | Kernel-mode drivers, User-mode drivers |
| 40 | **Windows ADK** | Expert | Application Development Kit |
| 41 | **Audio** | Expert | |
| 42 | **Professional Audio** | Expert | Studio recording, Mixing, Mastering |
| 43 | **Studio Audio** | Expert | 8.1 Surround, Multi-track recording |
| 44 | **Home Audio** | Expert | Multi-room, Streaming |
| 45 | **Multi Room Audio** | Expert | Networked audio, Synchronization |
| 46 | **DSP** | Expert | Digital Signal Processing algorithms |
| 47 | **8.1 Audio** | Expert | Surround sound, Bass management |
| 48 | **Amplifier** | Expert | Class AB, 100W@8Ω, THD+N<0.01% |
| 49 | **DAC** | Expert | PCM3168A, AK4458, XMOS XU316 |
| 50 | **Audio Interface** | Expert | USB Audio Class 2.0, ASIO |
| 51 | **Operating Systems** | Expert | |
| 52 | **Windows** | Expert | IIS, WAMP, COM interop, Registry, Services |
| 53 | **Linux** | Expert | System administration, Service management |
| 54 | **Raspberry Pi OS** | Expert | ARM64, Embedded Linux |
| 55 | **Embedded Linux** | Expert | Yocto, Buildroot, Custom kernels |
| 56 | **C# / .NET** | Expert | Windows servisleri/araçları — dinamik stack girişi ([[engine.md]] §9) |
| 57 | **PowerShell** | Expert | Windows otomasyonu, vault scriptleri (`.ai/scripts/` kataloğu) |

---

## 3. Mimari Vizyon

> Detaylı mimari için bkz: [[CLAUDE.md]] §4-5, §12

CoreMusic mimarisi, L0-L6 katman bağımlılık kuralları ve teknoloji yığını.

**Faz 0 doğrulama notu:** L0-L3 katman adlandırması vault dokümanında kavramsal etikettir; `shared/src/` fiziksel klasörleri katman adı taşımaz (19 modül: AI, Api, Bootstrap, Cache, Config, Contracts, Database, Device, Events, Exception, Interfaces, Log, Middleware, OAuth, PageRouter, Security, Session, Theme, ViewMode). Katman → modül eşlemesi [[architecture/l0-infrastructure]] ve [[architecture/l1-security]] index'lerinde yürütülür.

---

## 4. CoreMusic AUTH Vizyonu

> Detaylı auth için bkz: [[CLAUDE.md]] §6, [[architecture/l1-security/auth]]

Merkezi auth.coremusic.net kimlik servisi, hybrid JWT+session, RBAC, middleware pipeline.

**Kod karşılığı (Faz 0 doğrulanmış):**
- `auth.coremusic.net/include/` hexagonal düzen — 7 klasör: Container, Controller, Domain, Handler, Middleware, Repository, Service
- `SessionManager` (175 satır) — `CoreMusic\Interfaces\Auth\ISessionManager` implements; `MM_UserID/MM_UserRole` session anahtarları
- Cross-domain: `HomeAuthBridge` (home servisi, 185 satır) → POST `auth.coremusic.net/validate-key` (TTL 300 sn, 2 retry) → başarılıysa session kurulumu

---

## 5. SPA Router Vizyonu

> Detaylı SPA router için bkz: [[CLAUDE.md]] §6A, [[architecture/l2-routing/spa-router]]

SPA Router, History API, partial rendering, backend-controlled auth.

**Kod karşılığı:** `PageRouter::dispatch(array $request, string $csrfToken)` — `shared/src/PageRouter/PageRouter.php`; AuthGuard 6 kontrol zinciri; route kaydı `shared/config/routes.php` (3.3KB).

---

## 6. API Vizyonu

> Detaylı API mimarisi için bkz: [[CLAUDE.md]] §6A, [[architecture/03-contracts/api-architecture-master]]

API-First yaklaşımı, Gateway, BFF, CQRS, Event Driven.

---

## 7. Teknoloji Seçim Kuralları

> Teknoloji kuralları için bkz: [[CLAUDE.md]] §21 (Yasak Örüntüleri), §12; ilke tablosu: [[engine.md]] §9

**Dinamik yığın ilkesi (2026-09-08, kullanıcı direktifi):** Programlama dili ve teknoloji yığını proje gereksinimlerine göre belirlenir. Kullanılabilir: **Node.js · C++ · C# · PHP** ve proje niteliğinin gerektirdiği diğer teknolojiler.

Domain-spesifik uygulama:

| Alan | Tercih | Kural Kaynağı |
|------|--------|---------------|
| PHP web servisleri | PHP 8.x native + PDO (ORM yasak) | ADR-002 (frozen) |
| Web panel frontend | Vanilla JS + ITCSS + BEM (framework yasak) | ADR-001 (frozen) — kapsam: web panel |
| Audio/embedded | C++20, zero-allocation, noexcept | ROLE §13, electronic/ |
| I/O-ağır servis | Node.js 20+ LTS | [[engine.md]] §9.2 PLANNED |
| Windows araçları | C# / WDK | AGENTS.md #11 |
| Yeni teknoloji | ADR şart — öncelik/gerekçe/trade-off | [[engine.md]] §9.4 şablonu |

Rol profilinde bu, §2.3'teki 57 uzmanlık alanının hangi domain'de devreye girdiğinin haritasıyla (§11) birlikte okunur.

---

## 8. Kodlama Sırası

> Kodlama sırası için bkz: [[architecture/03-contracts/development-workflow]]

Sistem analizi → mimari → API sözleşmesi → DB → auth → session → middleware → frontend → diğer servisler.

**Sıra ↔ Faz Eşlemesi (vault revizyonu):**

| Kodlama Sırası Adımı | Karşılayan Bileşen | Durum |
|----------------------|--------------------|-------|
| Sistem analizi | Faz 0 cross-check (bu revizyon) | TAMAMLANDI |
| Mimari | ADR seti (79 karar) + architecture/ | DOKÜMANTE |
| API sözleşmesi | architecture/03-contracts (36+3 dosya) | DOKÜMANTE |
| DB | .ai/.sql/mysql (18 şema) + migrations/ | ŞEMA HAZIR |
| Auth | auth.coremusic.net (IMPLEMENTED) | KOD VAR |
| Session | SessionManager + SessionInitializer | KOD VAR (kopya sorunu: engine §8.1 #1) |
| Middleware | 4 IMPLEMENTED middleware sınıfı | KOD VAR |
| Frontend | ui-design spec + tokens | SPEC VAR, KOD PLANNED |
| Diğer servisler | 9 domain | PLANNED |

---

## 9. Kritik Kurallar

> Kritik kurallar için bkz: [[CLAUDE.md]] §7 (Hard Guardrails)

Sıfırdan geliştirme, clean architecture, merkezi auth, security-first, zero code before plan.

**Kural ↔ Uygulama Kanalı:**

| Kural | Uygulama Kanalı | Kontrol |
|-------|-----------------|---------|
| Sıfırdan geliştirme | Referans proje inceleme kuralı (§16) | Kod kopyası taraması |
| Clean architecture | auth hexagonal klasör düzeni | include/ 7 klasör yapısı |
| Merkezi auth | auth.coremusic.net tek kimlik kaynağı | HomeAuthBridge validate-key akışı |
| Security-first | §14 practices + ADR-010/012/013 | Middleware hattı sırası |
| Zero code before plan | WORKFLOW onay kapısı | Plan kaydı yoksa kod başlamaz |

---

## 10. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Uzmanlık | [[AGENTS.md]] | Agent yetkileri |
| § 3 Mimari | [[architecture/00-overview/architecture-master]] | Sistem genel bakışı |
| § 4 Auth | [[architecture/07-security/middleware-security]] | Güvenlik pipeline'ı |
| § 5 SPA | [[architecture/l3-presentation/index]] | Frontend layer |
| § 6 API | [[architecture/03-contracts/api-architecture-master]] | API mimarisi |
| § 7 Teknoloji | [[brain.md]] | Teknik kararlar |
| § 8 Kodlama | [[architecture/03-contracts/project-structure]] | Proje yapısı |
| § 4 Auth Vizyonu | [[archives/prompt2-auth-2026-09-01]] | Auth mimarisi kaynağı (tarih düzeltildi, Faz 1) |
| § 5 SPA Vizyonu | [[archives/prompt1-spa-router-2026-09-01]] | SPA router kaynağı (tarih düzeltildi, Faz 1) |
| § 6 API Vizyonu | [[archives/prompt3-api-2026-09-01]] | API mimarisi kaynağı (tarih düzeltildi, Faz 1) |
| § 7 Teknoloji Seçimi | [[archives/prompt0-genel-ana-prompt-2026-09-01]] | Composer paket öncelik sırası (tarih düzeltildi, Faz 1) |
| § UI Design | [[ui-design/00-mockup-index]] | Mockup indeksi — 19 PNG, frontend ZORUNLU |
| § Mockup PNG'ler | `.ai/.png/home-1024/` + `.ai/.png/home-1920/` + `.ai/.png/shared-1024/` | 12 + 1 + 6 = 19 PNG (RPi5 1024×600 + Desktop 1920×1080) |
| § Responsive Kuralları | [[ui-design/responsive-device-mode]] | 4K No-Center (§7.4) + Backward-Compat (§12) bağlayıcı |
| § 11 Rol Eşlemesi | [[.agents/AGENTS.md]] | 11 profil kayıt defteri |
| § 21 Bulgular | [[engine.md]] §8.1 | Bilinen sorunlar tablosu |

---

## 11. Teknoloji Yığını & Rol Eşlemesi (Dynamic Stack)

Rol profili ↔ teknoloji ↔ kod kanıtı haritası. Durum etiketleri: **IMPLEMENTED** (kod mevcut) / **PLANNED** (dokümante, kod yok) — Faz 0 envanteri, 2026-09-08.

| Rol/Agent | Birincil Dil | Hedef Domain | Durum | Kanıt |
|-----------|--------------|--------------|-------|-------|
| Master Orchestrator | — (koordinasyon) | Tüm vault | IMPLEMENTED | `.ai/engine.md` motor |
| Backend Architect | PHP 8.4 | shared/, auth., home. | IMPLEMENTED | `shared/composer.json` (≥8.4), `auth.coremusic.net/composer.json` |
| Backend Architect | PHP 8.3 | packages/shared | IMPLEMENTED | `packages/shared/composer.json` (≥8.3) |
| UI Designer | Vanilla JS + CSS (ITCSS/BEM) | web panel frontend | PLANNED (token/ekran spec IMPLEMENTED) | `.ai/ui-design/tokens/` (4 dosya), screens/ (26 md) |
| Security Engineer | PHP 8.4 (middleware) | shared/src/Middleware, Security | IMPLEMENTED | CsrfMiddleware, SecurityHeadersMiddleware, RateLimiterMiddleware dosyaları |
| Data Engineer | SQL (MySQL) | .ai/.sql/mysql (18 şema) | IMPLEMENTED (şema) / PLANNED (çalışan DB servisi) | 18 .sql dosyası |
| Embedded Engineer | C++20 | NevaEngine, car/studio hedefleri | PLANNED | `projects/NevaEngine/` spec dosyaları |
| QA Engineer | PHP (PHPUnit) / JS (Vitest) | tests/ | IMPLEMENTED (dev bağımlılık) | composer.json require-dev |
| DevOps Engineer | YAML/Docker/CI | 02-deployment | PLANNED | `architecture/02-deployment/` (8 md) |
| Audio HW Engineer | — (donanım spec) | electronic/hardware | IMPLEMENTED (spec) | audio-interface.md (XMOS XU316 → I2S → PCM3168A) |
| DSP Firmware Engineer | C (XMOS xcc) / C++20 | electronic/firmware | PLANNED (kod) / IMPLEMENTED (spec) | rtos.md, dsp-firmware.md |
| Windows SW Engineer | C# / C++ (WDK) | Windows platform araçları | PLANNED | AGENTS.md #11; WASAPI hedefi |

**Yığın özet satırı:** PHP 8.4 (servis altyapısı) · Vanilla JS+ITCSS (panel) · C++20 (audio/embedded) · Node.js 20+ (download/IO) · C# (Windows araçları) · SQL (18 şema, MySQL hedefi) — seçim ilkesi: proje gereksinimi, ADR ile kayıt.

### 11.1 Stack Satır Referansları (composer.json gerçek alanları)

| Alan | Gerçek değer (Faz 0) | Anlam |
|------|----------------------|-------|
| `name` | `coremusic/shared-infrastructure` | Composer paket kimliği; vendor `coremusic`, paket adı |
| `version` | `2.0.0` | Paket sürümü (yalnız shared-infrastructure'ta açık) |
| `require.php` | `>=8.4` (shared/auth) · `>=8.3` (packages/shared) | Minimum PHP çalışma ortamı |
| `autoload.psr-4` | `CoreMusic\` → `src/` · `CoreMusic\Shared\` → `src/` · `CoreMusic\Auth\` → `include/` · `CoreMusic\Home\` → `include/` | Namespace → dizin eşlemesi |
| `require` (altyapı) | psr/log ^3.0 · psr/cache ^3.0 · psr/container · psr/event-dispatcher · symfony/event-dispatcher ^7.0 · php-di/php-di ^7.0 · respect/validation ^2.0 · nyholm/psr-7 ^1.8 | Servis altyapısı bağımlılık seti |
| `require` (packages/shared) | ramsey/uuid ^4.7 · paragonie/sodium_compat ^1.20 | Kimlik + şifreleme yardımcıları |
| `require` (auth) | vlucas/phpdotenv ^5.7 · nikic/fast-route ^1.3 · nyholm/psr7-server ^1.1 | Ortam + router + PSR-7 fabrika |
| `require` (home) | php-di · psr/log · psr/container (minimal) | Home servis hafif bağımlılık seti |
| `repositories` | `type: path`, url `../shared` | Yerel paket bağlama (symlink) |
| `require-dev` | phpunit ^10.5 / ^11.0 · phpstan ^1.10 | Test + statik analiz araçları |
| `scripts` | `stan`: `phpstan analyse --level 5` | Analiz komutunun tanımı |

### 11.2 Stack Geçiş Senaryoları

**Senaryo 1 — `packages/shared` birleşimi (ADR-085):**

| Alan | Değer |
|------|-------|
| Tetik | İki PSR-4 kökü (`CoreMusic\`, `CoreMusic\Shared\`) sürdürülebilirlik yükü |
| Adımlar | UUID/sodium bağımlılıklarının `shared-infrastructure`'a aktarımı → namespace birleşimi → path repository tekilleştirmesi → eski paketin deprekasyonu |
| Kısıt | PHPUnit sürüm farkı (^10.5 ↔ ^11.0) birleşimde çözülmeli |
| Doğrulama | Tek PSR-4 kökü, tek composer.json, tüm testler yeşil |

**Senaryo 2 — Node.js download servisi girişi (PLANNED):**

| Alan | Değer |
|------|-------|
| Tetik | Yüksek eşzamanlı dosya aktarımı ihtiyacı (ADR-026 kapsamı) |
| Adımlar | ADR draft → `download.coremusic.net` iskeleti → package.json stack bildirimi → API sözleşmesi (03-contracts) → kod |
| Kısıt | ORM yasağı Node tarafına da geneller ([[engine.md]] §9.5.4) |
| Doğrulama | ADR kabul + endpoint sözleşmesi + smoke test |

**Senaryo 3 — C# Windows aracı girişi (PLANNED):**

| Alan | Değer |
|------|-------|
| Tetik | WASAPI yardımcı/servis ihtiyacı (Windows SW Engineer domain) |
| Adımlar | İhtiyaç analizi → C# vs C++ (WDK) trade-off → ADR → çözüm |
| Kısıt | Cross-platform hedef varsa C++20 tercih edilir ([[engine.md]] §9.5.3) |
| Doğrulama | ADR kabul + Windows hedef makinede doğrulama |

### 11.3 Stack Kısıt İhlalleri ve Tepki

| İhlal | Örnek | Tepki |
|-------|-------|-------|
| Plansız bağımlılık | Rastgele npm/composer paketi ekleme | Revert + CLAUDE.md §5 kuralı |
| Framework sızıntısı | Web panele framework ekleme | ADR-001 ihlali — kod revert |
| ORM sızıntısı | PDO yerine ORM import | ADR-002 ihlali — kod revert |
| Kanıtsız stack iddiası | "Redis cache" dokümanı (kod yok) | IMPLEMENTED→PLANNED etiket düzeltmesi (Faz 0'da yapıldı) |
| Sürüm uyumsuzluğu | PHPUnit ^10.5 ↔ ^11.0 birleşimi | Senaryo 1 kısıtı olarak yönetilir |

---

## 12. Deployment Strategies

### 12.1 Blue/Green Deployment

```
Current (Blue) → Load Balancer → Server 1 (Blue)
                                  Server 2 (Green)

Deploy to Green → Test → Switch Load Balancer → Decommission Blue
```

### 12.2 Rolling Deployment

```
Server 1: v1.0 → v1.1 (deploy)
Server 2: v1.0 → v1.1 (deploy)
Server 3: v1.0 → v1.1 (deploy)
```

### 12.3 Canary Deployment

```
10% traffic → v1.1 (canary)
90% traffic → v1.0 (stable)

Monitor → Increase → 100% → Decommission v1.0
```

**Faz 0 notu:** Bu stratejiler hedef tanımlardır; CI/CD pipeline kodu henüz mevcut değil (DevOps PLANNED — `architecture/02-deployment/` dokümantasyon aşamasında).

### 12.4 Strateji Seçim Kriterleri

| Kriter | Blue/Green | Rolling | Canary |
|--------|------------|---------|--------|
| Kesinti penceresi | Sıfır (anlık geçiş) | Kısa (sunucu başına) | Sıfır |
| Donanım maliyeti | 2× (çift ortam) | 1× | 1× + izleme |
| Geri dönüş hızı | Anlık (LB geri) | Sunucu başına | Trafik yüzdesi düşürme |
| Test imkânı | Green'de tam test | Kademeli | Gerçek trafikte %10 |
| Uygun kullanım | Auth gibi kritik servis | Statik/iç servis | Yeni sürüm risk analizi |

### 12.5 Bu Projede Uygulama Durumu

| Servis | Önerilen | Gerekçe |
|--------|----------|---------|
| auth.coremusic.net | Blue/Green | Kimlik servisi kesintisi tüm ekosistemi kilitler |
| home.coremusic.net | Rolling | Tüketici paneli, kısa kabul edilebilir |
| assets.coremusic.net | CDN/rolling | Statik içerik, durum tutmaz |
| download (Node.js, PLANNED) | Canary | Yeni yığın; %10 trafikle risk ölçümü |

---

## 13. Coding Standards

> Kodlama standartları için bkz: [[CLAUDE.md]] §12, [[architecture/03-contracts/development-standards]]

PHP strict_types + PSR-12, Vanilla JS ES6+ (framework yasak — web panel kapsamı), C++20 noexcept + zero-allocation.

**Kod kanıtı:** Tüm örneklenen `CoreMusic\` sınıfları `final` + `declare(strict_types=1)` düzeninde (örn. `AuthMiddleware implements IMiddleware`). Statik analiz: PHPStan level 5 (`stan` script). Test: PHPUnit ^10.5 (shared-infrastructure) / ^11.0 (packages/shared).

---

## 14. Security Practices

> Güvenlik uygulamaları için bkz: [[CLAUDE.md]] §6, [[architecture/l1-security/]]

OWASP Top 10:2025, CSRF, CSP, rate limiting, prepared statements, RBAC.

**Kod karşılıkları:** CsrfMiddleware (bypass: `set-gender`), SecurityHeadersMiddleware (`_csp_nonce` + `buildCsp()` + `X-Frame-Options: DENY`), RateLimiterMiddleware (`rl:` prefix, 60 istek/60 sn, TRUSTED_PROXIES sabiti), DatabaseManager (PDO `EMULATE_PREPARES=false`).

---

## 15. Mimari Vizyon — CoreMusic Nedir?

> CoreMusic tanımı için bkz: [[CLAUDE.md]] §4

CoreMusic, bireysel kullanıcılar, profesyoneller, stüdyolar, araç içi ve ev medya merkezleri için tasarlanmış dijital medya yönetim platformu.

**Fiziksel gerçeklik (Faz 0):** Şu an kodda mevcut bileşenler: shared altyapı, packages/shared, auth servisi, home servisi, statik asset servisi (5). Diğer 9 domain (api, music, admin, car, studio, pro, media, download, landing) hedef mimaride tanımlı, kod yok.

---

## 16. Referans Proje Kuralları

> Referans proje kuralları için bkz: [[WORKFLOW.md]] §8.1C

Referans proje sadece mimari referans olarak incelenir, kod kopyalanmaz.

---

## 17. Kritik Uyarılar

> Kritik uyarılar için bkz: [[CLAUDE.md]] §23

Middleware sırası değiştirme, SELECT *, hardcoded secret, PCM5122 kullanımı, plansız kod.

**Uyarı Detayları:**

| # | Uyarı | Neden | Doğru Yol |
|---|-------|-------|-----------|
| 1 | Middleware sırasını değiştirme | Pipeline güvenlik hattı sıraya bağımlı: SecurityHeaders → Auth → Csrf → RateLimiter benzeri gerçek akış | Mevcut sırayı oku, ADR'siz değiştirme |
| 2 | SELECT * yasak | Sütun kayması + gereksiz veri; PDO hattında açık sütun listesi | İstek bazlı sütun seçimi |
| 3 | Hardcoded secret yasak | `.env` (vlucas/phpdotenv) dışına secret yazmak Guardrail #15 ihlali | ENV-only |
| 4 | PCM5122 kullanımı | ADR-038 PCM3168A'yı seçti; PCM5122 eski/alternatif çip | ADR-038'e uy |
| 5 | Plansız kod | "Zero code before plan" — analiz öncesi kod CLAUDE.md §12 ihlali | ANALİZ → PLAN → UYGULAMA |

---

## 18. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Yeni entity | Domain katmanında oluştur |
| Yeni use case | Application katmanında handler yaz |
| Yeni repository | Interface + Implementation oluştur |
| Yeni middleware | PSR-15 uyumlu oluştur |
| Yeni test | Arrange-Act-Assert pattern |
| Yeni ADR | Draft oluştur, review'a sun |
| Yeni API endpoint | OpenAPI spec yaz, sonra kodla |
| Yeni feature | 20-fazlı lifecycle'ı takip et |
| Teknoloji seçimi | [[engine.md]] §9 + bu dosya §11 |
| Stack kararı | ADR şablonu ([[engine.md]] §9.4) |

---

## 19. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 6.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Sections** | 22 (sıralama düzeltildi: §19-§20 artık doğru konumda) |
| **Expertise Areas** | 57 (C#/.NET ve PowerShell eklendi) |
| **Architecture Principles** | 6 (Clean, Hexagonal, SOLID, DDD, EDA, CQRS) |
| **Platform Targets** | 5 |
| **Security Layers** | 10 (Middleware Pipeline) |
| **RBAC Roles** | 7 |
| **Development Phases** | 10 |
| **Critical Rules** | 10 |
| **Stack Mapping** | 13 satır (§11) — IMPLEMENTED/PLANNED etiketli |
| **Truth Mode** | "50+ yıl" iddiası retorik etiketiyle işaretlendi (§2.2) |
| **Geçiş senaryoları** | 3 (§11.2 — ADR-085 birleşimi, Node.js girişi, C# girişi) |
| **Kanıt komut seti** | 6 komut (§22.1 — PowerShell 5.1, Faz 0'da fiilen çalıştırıldı) |
| **Faz eşlemesi** | 9 satır (§8 — kodlama sırası ↔ revizyon fazı) |
| **Doğrulama bulguları** | 8 kayıt (§21 — Test-Path + composer + LSP tabanlı) |
| **Kısıt ihlal tepkileri** | 5 satır (§11.3 — revert/etiket/ADR kuralları) |
| **Stack satır referansı** | 11 alan (§11.1 — composer.json gerçek alanları) |

---

## 20. Architecture Patterns (Detailed)

### 20.1 Repository Pattern

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Repository;

use CoreMusic\Auth\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): bool;
    public function softDelete(int $id): bool;
}
```

### 20.2 Service Layer Pattern

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application\Service;

use CoreMusic\Auth\Domain\Repository\UserRepositoryInterface;
use CoreMusic\Security\Service\PasswordService;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordService $passwordService,
    ) {
    }

    public function register(string $email, string $password): User
    {
        // Business logic
        $user = User::create(
            new Email($email),
            new Password($password)
        );

        $this->userRepository->save($user);

        return $user;
    }
}
```

### 20.3 CQRS Pattern

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Application\Command;

final class RegisterUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordService $passwordService,
    ) {
    }

    public function handle(RegisterUserCommand $command): User
    {
        $user = User::create(
            new Email($command->email),
            new Password($command->password)
        );

        $this->userRepository->save($user);

        return $user;
    }
}
```

### 20.4 Domain Event Pattern

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Domain\Event;

final class UserRegisteredEvent
{
    public function __construct(
        public readonly int $userId,
        public readonly string $email,
        public readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable()
    ) {
    }
}
```

### 20.5 Pattern ↔ Gerçek Kod Karşılıkları (Faz 0 doğrulaması)

| Pattern (bu bölüm) | Gerçek kod karşılığı | Not |
|--------------------|----------------------|-----|
| Repository (20.1) | `auth.coremusic.net/include/Repository/` klasörü | Hedef desen; somut repo sınıfları dosya envanteriyle doğrulanacak |
| Service Layer (20.2) | `SessionManager` (auth, `include/Service/`) | `ISessionManager` implements — gerçek implements örneği |
| CQRS (20.3) | `include/Handler/` + `include/Domain/` ayrımı | Hexagonal klasör düzeni deseni destekler |
| Domain Event (20.4) | `symfony/event-dispatcher ^7.0` bağımlılığı | Event altyapısı composer'da hazır; ADR-086 (Event Driven) |
| Middleware (§19 MW) | `IMiddleware` implements 4 sınıf | Csrf, Auth, RateLimiter, SecurityHeaders |

Kural: Bu bölümdeki PHP örnekleri hedef deseni gösterir; doğrudan kopyalanmaz ([[WORKFLOW.md]] §8.1C referans kuralı).

---

## 21. Kod Doğrulama Bulguları (Faz 0-1, 2026-09-08)

Bu dosyadaki iddiaların kaynak kodla çapraz denetim sonuçları:

| # | Bulgu | Kanıt | Rol Tanımına Etkisi |
|---|-------|-------|---------------------|
| 1 | 13 dokümante domain'den 4'ü fiziksel | Test-Path kök dizinler | §15 fiziksel gerçeklik notu eklendi |
| 2 | İki PSR-4 paketi | `shared/composer.json` + `packages/shared/composer.json` | §11 eşleme iki PHP satırı |
| 3 | L0-L3 klasör adlandırması kodda yok | `shared/src/` 19 modül listesi | §3 doğrulama notu eklendi |
| 4 | `SessionInitializer` kopya namespace | `shared/src/Session/` + `shared/src/PageRouter/` | §21 kayıt; ADR bekliyor |
| 5 | Redis adapter yok | `CacheManager.php` zinciri | glossary "APCu" girişi düzeltildi |
| 6 | Auth hexagonal düzen mevcut | `auth.coremusic.net/include/` 7 klasör | §4 kod karşılığı güçlendirildi |
| 7 | Tanımsız sabitler (LSP) | `SESSION_NAME`, `PAGES_PATH`, `TRUSTED_PROXIES` | engine §8.1 #7 ile bağlantılı |
| 8 | PHPUnit sürüm farkı | ^10.5 vs ^11.0 | §13 kod kanıtı satırı |

---

## 22. Doğrulama Metodolojisi

Bu dosyadaki her doğrulanabilir iddia şu yöntemlerle sınanmıştır:

| Yöntem | Uygulama | Örnek |
|--------|----------|-------|
| Test-Path | Dizin/dosya varlığı | 13 domain dizini sorgusu |
| composer.json okuma | Paket adı, PHP sürümü, bağımlılıklar | §11 kanıt sütunu |
| Sınıf dosyası okuma | Sınıf adı, interface, satır sayısı | SessionManager 175 satır |
| Sayım | Dosya/satır toplama | Uzmanlık alanları 55→57 |
| LSP taraması | Tanımsız sembol yakalama | §21 bulgu #7 |

İlke: Doğrulanamayan iddia ya silinir ya da açık etiketle işaretlenir (`DOĞRULAMA GEREKLİ` / retorik not) — sessiz hallüsinasyon kabul edilmez (ADR-005).

### 22.1 Kanıt Komut Seti (tekrarlanabilir)

Bu dosyadaki bulguları bağımsız olarak yeniden üretmek için:

```powershell
# 1. Fiziksel domain kontrolü (§15, §21 #1)
"coremusic.net","shared","packages","api.coremusic.net","auth.coremusic.net",
"music.coremusic.net","admin.coremusic.net","home.coremusic.net",
"car.coremusic.net","studio.coremusic.net","pro.coremusic.net",
"media.coremusic.net","download.coremusic.net","assets.coremusic.net" |
  ForEach-Object { "{0,-25} {1}" -f $_, (Test-Path -LiteralPath $_) }

# 2. PSR-4 kök kontrolü (§21 #2)
Select-String -LiteralPath "shared\composer.json","packages\shared\composer.json" -Pattern 'PSR-4|"CoreMusic' 

# 3. SessionInitializer kopyası (§21 #4)
Get-ChildItem -LiteralPath "shared\src" -Recurse -Filter "SessionInitializer.php" | Select-Object FullName

# 4. CacheManager adapter zinciri (§21 #5)
Select-String -LiteralPath "shared\src\Cache\CacheManager.php" -Pattern "Apcu|Memory"

# 5. Hexagonal düzen (§21 #6)
Get-ChildItem -LiteralPath "auth.coremusic.net\include" -Directory | Select-Object Name

# 6. PHPUnit sürümleri (§21 #8)
Select-String -LiteralPath "shared\composer.json","packages\shared\composer.json" -Pattern 'phpunit'
```

Komutlar PowerShell 5.1 uyumludur ve Faz 0 taramasında (2026-09-08) fiilen çalıştırılmıştır. Sonuç yorumlama kuralı: komut çıktısı bu dosyadaki tablo ile uyuşmuyorsa dosya güncellenir, komut hatalı varsayılmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
