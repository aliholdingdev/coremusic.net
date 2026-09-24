---
title: "CoreMusic — shared Agent Talimatları"
type: agent-registry
folder: "shared"
category: shared
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# shared — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]] · [[../.ai/architecture/index]]

---

## 1. Amaç

Paylaşılan PHP altyapısı (ADR-039): tüm subdomainlerin ortak middleware, router, güvenlik, konfigürasyon, oturum, cache, event ve API katmanı. L0-L2 kod karşılığının merkezi.

---

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `composer.json`, `phpunit.xml` | Paket + test yapılandırması |
| `config/routes.php`, `auth-routes.php` | Route tanımları |
| `config/domain.php` | Domain haritası (9 subdomain) |
| `config/oauth-platforms.php` | OAuth sağlayıcı listesi |
| `database/migrations/` | oauth_connections + oauth_states migration'ları |
| `src/AI/` + `src/AI/Contracts/` | AIEngine, Orchestrator, KnowledgeBase, MemorySystem, PromptEngine, ToolCalling (ADR-030) |
| `src/Api/` | Gateway, ApiRequest/Response |
| `src/Api/Bff/` | BffLayer + Desktop/Embedded/Mobile/Spa BFF (ADR-084) |
| `src/Api/Dto/Request\|Response/` | API DTO'ları |
| `src/Api/Middleware/` | 6 API middleware (Auth, Authorization, RateLimit, Validation, ...) |
| `src/Api/Registry/` | ServiceRegistry + sağlık tanımları |
| `src/Api/Versioning/` | ApiVersion, VersionRegistry, VersionResolver |
| `src/Bootstrap/RuntimeBootstrap.php` | Ortak çalışma zamanı kurulumu |
| `src/Cache/` | Apcu, Memory, PageCache adapter'ları (ADR-007) |
| `src/Config/` | ConfigManager, DomainConfig, EnvParser (ADR-015), AuthRouteConfig |
| `src/Contracts/` | Api + Events sözleşmeleri |
| `src/Database/` | DatabaseManager, DatabaseRegistry (ADR-003/022) |
| `src/Device/` | DeviceDetector, DeviceManager, DeviceCssMap |
| `src/Events/` + `Domain/` + `Integration/` | EventDispatcher + 9 domain event + 3 integration event (ADR-086) |
| `src/Exception/` | 8 exception (BaseCoreMusicException hiyerarşisi) |
| `src/Interfaces/` | Auth, Config, Database, Middleware, Security interface'leri |
| `src/Log/` | LoggerFactory, FileHandler |
| `src/Middleware/` | 10+ HTTP middleware (Auth, BypassAuth ADR-008, CSRF ADR-010, RateLimiter ADR-013, SecurityHeaders, Session, CORS, Origin, Permission) |
| `src/OAuth/` + `Provider/` | OAuthManager + 12 provider (Base + Discord, Facebook, Instagram, LinkedIn, Pinterest, Reddit, Snapchat, TikTok, ...) |
| `src/PageRouter/` | 14 dosya — SPA page router (ADR-021/083): Kernel, HtmlShellRenderer, AuthGuard, RouteRegistry, RequestNormalizer, ResponseEmitter, ... |
| `src/Security/` | CacheRateLimiter, ReturnUrlPolicy, SecurityHelper, SessionKeys, UuidV7 |
| `src/Session/` | SessionBootstrapper, Config, Initializer, Lifecycle (ADR-011) |
| `src/Theme/ThemeManager.php` | Tema motoru (ADR-044) |
| `src/ViewMode/ViewModeManager.php` | Görünüm modu (ADR-045) |
| `tests/` | Api, Events, OAuth, Unit (Config, Device, PageRouter, Security) |

---

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Backend Architect | Tüm `src/` değişiklikleri; hexagonal sınırlar |
| Security Engineer | Middleware/Security/Session/OAuth katmanı; her değişiklik security-audit workflow'u |
| Data Engineer | `database/migrations/` + Database katmanı (BCNF, ADR-040) |
| QA Engineer | Test ekleme/koruma; phpunit suite |

---

## 4. Kurallar

### Zorunlu
1. PDO prepared statement yalnız (ADR-002); DatabaseManager dışında DB bağlantısı açılmaz
2. Middleware ekleme → `MiddlewarePipeline` kaydı + `[[../shared/config/]]` güncelleme gerekirse
3. Yeni domain event → `src/Events/Domain/` + `DomainEventInterface` + test
4. Migration → `[[../.ai/.templates/infrastructure/migration-template.md]]`'den türetilir
5. PHP dosyaları `[[../.ai/.templates/backend/php-template.md]]`'den türetilir

### Yasak
1. `src/` içine subdomain'e özel iş kuralı gömmek (o kod subdomain klasörüne gider)
2. `BypassAuthMiddleware` kapsam genişletme (ADR-008 kapsamı ADR ile değişir)
3. ORM (ADR-002), var, eval
4. `config/domain.php` domain listesini onaysız değiştirmek

---

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Mimari master | `[[../.ai/architecture/index]]` |
| API gateway | `[[../.ai/architecture/k9-api-routing/]]` |
| Router katmanı | `[[../.ai/architecture/k9-api-routing/]]` |
| Güvenlik katmanı | `[[../.ai/architecture/k6-guvenlik/]]` |
| PHP şablonu | `[[../.ai/.templates/backend/php-template.md]]` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
