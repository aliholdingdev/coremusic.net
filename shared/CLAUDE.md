---
title: "CoreMusic — shared Library Bağlam"
type: context
folder: "shared"
category: shared
date: 2026-09-21
updated: 2026-10-12
status: active
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
 authority: "shared/CLAUDE.md"
 source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md · .ai/WORKFLOW.md"
---

# shared — CLAUDE.md (Detaylı Versiyon)

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

---

## 1. Bağlam & Amaç

Tüm subdomainler bu kütüphaneye bağımlıdır. **Değişiklik yayılımı en geniş klasördür**: tek middleware değişikliği 9 domaini etkileyebilir. Tek ortak kütüphanedir (ADR-085: Shared Library Hybrid).

```
shared/
├── composer.json ← Tek paket: coremusic/shared
├── config/ ← Route, domain, OAuth yapılandırması
├── database/migrations/ ← oauth_connections + oauth_states
├── src/ ← 20 alt klasör, tüm shared kaynak kodu
│ ├── AI/ ← AIEngine, Orchestrator, KnowledgeBase, MemorySystem
│ ├── Api/ ← Gateway, BFF×4, DTO, Middleware×6, Registry, Versioning
│ ├── Bootstrap/ ← RuntimeBootstrap.php
│ ├── Cache/ ← Apcu, Memory, PageCache (ADR-007)
│ ├── Component/ ← Paylaşılan UI bileşen katmanı
│ ├── Config/ ← ConfigManager, DomainConfig, EnvParser (ADR-015)
│ ├── Contracts/ ← Api + Events sözleşmeleri
│ ├── Database/ ← DatabaseManager, DatabaseRegistry (ADR-003/022)
│ ├── Device/ ← DeviceDetector, DeviceManager, DeviceCssMap
│ ├── Events/ ← EventDispatcher + 9 domain + 3 integration event (ADR-086)
│ ├── Exception/ ← 8 exception (BaseCoreMusicException hiyerarşisi)
│ ├── Log/ ← LoggerFactory, FileHandler
│ ├── Middleware/ ← 11 dosya HTTP middleware + MiddlewarePipeline (CSRF, RateLimit, Security, Session...)
│ ├── OAuth/ + Provider/ ← OAuthManager + 12 provider
│ ├── PageRouter/ ← 14 dosya — SPA page router (ADR-021/083)
│ ├── Repository/ ← Depo katmanı (ApiKey vb.)
│ ├── Security/ ← CacheRateLimiter, ReturnUrlPolicy, SecurityHelper
│ ├── Session/ ← SessionBootstrapper, Config, Initializer, Lifecycle (ADR-011)
│ ├── Theme/ ← ThemeManager.php (ADR-044)
│ └── ViewMode/ ← ViewModeManager.php (ADR-045)
└── tests/ ← 9 dizin: Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit
```

---

## 2. Mevcut Durum (Detaylı)

| Durum | Değer |
|-------|-------|
| src alt klasör sayısı | 20 |
| Middleware sayısı | 11 (10 middleware + MiddlewarePipeline) + 6 (API) |
| Domain event | 9, Integration event | 3 |
| OAuth provider | 12 (Discord, Facebook, Instagram, LinkedIn, Pinterest, Reddit, Snapchat, TikTok, X, YouTube...) |
| PageRouter dosyası | 14 (Kernel, HtmlShellRenderer, AuthGuard, RouteRegistry, RequestNormalizer, ResponseEmitter...) |
| API BFF | 4 (Desktop, Embedded, Mobile, Spa) + BffLayer |
| API Middleware | 6 (Authentication, Authorization, RateLimit, RequestValidation, ResponseNormalization, Validator) |
| API Versioning | ApiVersion, VersionRegistry, VersionResolver |
| Test dizinleri | 9 (Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit) |
| Bilinen risk | `Theme`/`ViewMode` tek dosyalık — test kapsamı dışında |

---

## 3. Mimari Konum

```
Presentation → Routing/PageRouter → Security/Middleware → Infrastructure/Database
 (tek yön: alt katman üst katmana bağımlı değildir)
 shared/ kütüphanesi bu katmanların hepsini kapsar
```

**Bağımlılık kuralları:**
- İZİNLİ — shared → subdomain: (shared alt sağlar)
- YASAK — subdomain → shared içi: (shared interface kullanılır)
- YASAK — shared → subdomain içi: (shared subdomain bileşeni import edemez)

K-space katman mimarisi (K000-K020, A0-A5, L0-L6) kaldırıldı (2026-10-12).

---

## 4. Komşu İlişkiler (Detaylı)

| Yön | Hedef | İlişki | Etki |
|-----|-------|--------|------|
| Parent | [[../AGENTS.md]] | Kök registry | — |
| Tüketen | [[../auth.coremusic.net/CLAUDE.md]] | Middleware + Session + Security | Yüksek (auth akışı) |
| Tüketen | [[../home.coremusic.net/CLAUDE.md]] | RuntimeBootstrap + Config + Session | Yüksek (ana panel) |
| Tüketen | [[../assets.coremusic.net/CLAUDE.md]] | DeviceCssMap ↔ devices.config.js | Orta (cihaz tespiti) |

Mimari referanslar `.ai/CLAUDE.md` ve `.ai/.decisions/index.md` üzerinden verilir.

---

## 5. Kritik Bileşenler

### 5.1 Middleware Pipeline (10 adım — Sıra Değişmez)

```
1. OriginCheckMiddleware → Köken doğrulama (whitelist CORS)
2. CorsMiddleware → CORS header'ları (whitelist only)
3. RateLimiterMiddleware → APCu: 60 req/60s
4. SecurityHeadersMiddleware → CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware → Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware → csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware → Test bypass (production'da devre dışı)
8. AuthMiddleware → Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware → RBAC yetki kontrolü
10. ValidationMiddleware → Request/DTO validasyonu
→ Controller
```

### 5.2 SPA PageRouter (14 dosya)

| Dosya | Amaç |
|-------|------|
| PageRouterKernel.php | Ana kernel, middleware orchestrasyonu |
| PageRouter.php | Tekil sayfa çözücü |
| HtmlShellRenderer.php | HTML shell üretimi (Theme + ViewMode entegre) |
| AuthGuard.php | Auth guard pipeline |
| RouteRegistry.php | Route kaydı ve eşleştirme |
| RequestNormalizer.php | Request normalize |
| ResponseEmitter.php | Response emit |
| + 7 diğer | Helper, Config, Exception vb. |

### 5.3 API Gateway & BFF

| BFF | Hedef İstemci | Response Tipi |
|-----|---------------|---------------|
| SpaBff | SPA (React/Vanilla) | Tam veri |
| MobileBff | Mobil uygulama | Minimal |
| EmbeddedBff | RPi5 (embedded) | Ultra-minimal, gzip |
| DesktopBff | Masaüstü uygulama | Orta boy |
| AdminBff | Admin paneli | Full + audit |
| CarBff | Araç içi | Touch-optimized |

---

## 6. Değişiklik Protokolü (Detaylı)

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | Katman değişikliği tespit | İlgili vault dosyası okunur (`@.ai/RAG.md` §3 konu→dosya indeksi) |
| 2 | Uyumluluk kontrolü | Katman bağımlılık matrisi kontrol edilir |
| 3 | Test yazma | Değişiklik için test yazılır |
| 4 | Security audit | Güvenlik etkiliyse security-audit workflow |
| 5 | Vault etkisi | Vault etkisi varsa vault-sync çalıştırılır |
| 6 | Audit trail | `.ai/log.md`'ye kayıt |

---

## 7. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | `src/` içine subdomain'e özel iş kuralı | Bağımlılık ihlali |
| 2 | BypassAuthMiddleware kapsam genişletme | ADR-008 kapsamı ADR ile değişir |
| 3 | ORM, var, eval | Güvenlik + performans |
| 4 | `config/domain.php` domain listesini onaysız değiştirmek | Sistem bütünlüğü |
| 5 | PDO dışında DB bağlantısı | ADR-002 tekillik |
| 6 | Hardcoded secret | `.env` / credential vault |

---

## 8. İlgili Kaynaklar

| Kaynak | Yol | İçerik |
|--------|-----|--------|
| Vault anayasa | `../.ai/CLAUDE.md` | Guardrails, LINT |
| Vault katalog | `../.ai/index.md` | Dosya indeksi |
| PHP şablonu | `../.ai/.templates/backend/php-template.md` | Yeni PHP dosyası |
| ADR-084 | `../.ai/.decisions/accepted/ADR-084-api-gateway-architecture.md` | API Gateway |
| ADR-085 | `../.ai/.decisions/accepted/ADR-085-modular-composer-packages.md` | Shared Library |
| ADR-086 | `../.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | Event Driven |

Not: `.ai/architecture/**` K-space katman tabloları ve `.ai/decisions/` (noktasız) yolu kaldırıldı (2026-10-12) — ADR-083'ün ayrı dosyası diskte yok.

---

## 9. Test Yapısı

| Test Grubu | Konum | Kapsam |
|------------|-------|--------|
| Api Tests | `tests/Api/` | API endpoint testleri |
| Component Tests | `tests/Component/` | Bileşen testleri |
| Events Tests | `tests/Events/` | Domain event testleri |
| Middleware Tests | `tests/Middleware/` | Pipeline + middleware testleri |
| OAuth Tests | `tests/OAuth/` | OAuth provider testleri |
| Repository Tests | `tests/Repository/` | Depo + SQLite çift testleri |
| Security Tests | `tests/Security/` | Güvenlik katmanı testleri |
| Unit Tests | `tests/Unit/` | Config, Device, Log, Middleware, PageRouter, Security, Theme |
| Fixtures | `tests/Fixtures/` | Test verisi (test verisi üretmez, sağlar) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
