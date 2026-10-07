---
title: "15-domain-d06-servis-api — Mimari Domain Tablosu d06"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d06 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d06
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 15-domain-d06-servis-api — Domain d06: Servis & API / Routing (K250–K299)

> **Kapsam:** API Gateway, BFF, versioning, service registry, event bus, merkezi servisler (auth, media CLI), SPA/routing kernel (PageRouter).
> **Eski dizin karşılıkları:** [[architecture/k8-servis]] · [[architecture/k9-api-routing]] · **Legacy K8 + K9** → bu domain.
> **Gerçeklik notu (2026-10-07):** Uygulanan servisler = auth · media (CLI) · api gateway · shared altyapısı. Control / Audio / Device / Network Audio / Download servisleri **dizin olarak diskte YOK** (glob 2026-10-07) → PLANNED. CQRS ve OpenAPI **grep 0** (ADR-084 notu, .ai/log.md P2-11).

## Katman Tablosu (K250–K299 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K250 | API Gateway (api.coremusic.net) | IMPLEMENTED | api.coremusic.net/index.php · shared/src/Api/Gateway.php · ADR-084 |
| K251 | SPA BFF | IMPLEMENTED | shared/src/Api/Bff/SpaBff.php |
| K252 | Mobile BFF | IMPLEMENTED | shared/src/Api/Bff/MobileBff.php |
| K253 | Embedded BFF (gzip ultra-minimal) | IMPLEMENTED | shared/src/Api/Bff/EmbeddedBff.php |
| K254 | Desktop BFF | IMPLEMENTED | shared/src/Api/Bff/DesktopBff.php |
| K255 | Admin BFF | PLANNED | ⚠️ VERIFICATION REQUIRED — dosya yok (hedef: .claude/CLAUDE.md §6A.2) |
| K256 | Car BFF | PLANNED | ⚠️ VERIFICATION REQUIRED — dosya yok (hedef: .claude/CLAUDE.md §6A.2) |
| K257 | Route table | IMPLEMENTED | shared/src/Api/Routing/RouteTable.php · api.coremusic.net/config/routes.php |
| K258 | API versioning | IMPLEMENTED | shared/src/Api/Versioning/{ApiVersion,VersionRegistry,VersionResolver}.php |
| K259 | Service registry (ServiceDefinition/Health) | IMPLEMENTED | shared/src/Api/Registry/{ServiceRegistry,ServiceDefinition,ServiceHealth}.php |
| K260 | API DTO (request/response) | IMPLEMENTED | shared/src/Api/Dto/Request/ · shared/src/Api/Dto/Response/ |
| K261 | API middleware katmanı | IMPLEMENTED | shared/src/Api/Middleware/ (dizin — 2026-10-07 ls) |
| K262 | Event dispatcher (PSR-14 sözleşmesi) | IMPLEMENTED | shared/src/Events/EventDispatcher.php · ADR-086 |
| K263 | Domain events (9 adet) | IMPLEMENTED | shared/src/Events/Domain/ (9 dosya: UserLoggedIn, MusicPlayed, PlaylistCreated …) |
| K264 | Integration events (3 adet) | IMPLEMENTED | shared/src/Events/Integration/ (AuthValidated, Notification, SessionCreated) |
| K265 | Production event dispatch wiring | PLANNED | ADR-086 · .ai/log.md P2-11 (dispatch 0 — wiring yok) |
| K266 | Auth servisi (auth.coremusic.net) | IMPLEMENTED | auth.coremusic.net/ (index.php · include/ · pages/ · handler/) |
| K267 | OAuth post handler | IMPLEMENTED | auth.coremusic.net/handler/OAuthPostHandler.php |
| K268 | Media servisi — CLI çekirdek | IMPLEMENTED | media.coremusic.net/src/Media/ (5 dosya) · media.coremusic.net/bin |
| K269 | Media servisi — taxonomy/slug/validator | IMPLEMENTED | media.coremusic.net/src/Media/{Taxonomy,Slugger,Validator}.php |
| K270 | Control servisi (port 81) | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok (hedef: .claude/CLAUDE.md §10) |
| K271 | Audio servisi (9741/9742, C++/WS) | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok (hedef: .claude/CLAUDE.md §10) |
| K272 | Device servisi (BLE/WiFi/USB) | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K273 | Network Audio servisi (WebRTC/multi-room) | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K274 | Download servisi (Node.js, port 3001) | PLANNED | ⚠️ VERIFICATION REQUIRED — dizin yok · ADR-026 |
| K275 | CQRS (command/query ayrımı) | PLANNED | ADR-084 · .ai/log.md P2-11 (grep 0) |
| K276 | OpenAPI sözleşmesi | PLANNED | ADR-084 · ⚠️ VERIFICATION REQUIRED (spec dosyası yok) |
| K277 | API key oluşturma aracı | IMPLEMENTED | bin/api-key-create.php |
| K278 | Rate-limit / 429 API akışı | IMPLEMENTED | ADR-094 · shared/src/Api/Middleware/ |
| K279 | Correlation ID / traceId | IMPLEMENTED | .ai/log.md (P5, 2026-10-07) · shared/src/Log/LoggerFactory.php |
| K280 | Server-authoritative routing kernel | IMPLEMENTED | shared/src/PageRouter/PageRouterKernel.php · ADR-021 |
| K281 | PageRouter çekirdeği (14 sınıf) | IMPLEMENTED | shared/src/PageRouter/ (14 PHP dosyası — 2026-10-07 find) |
| K282 | Request normalizer / URL normalization | IMPLEMENTED | shared/src/PageRouter/RequestNormalizer.php · ADR-016 |
| K283 | ErrorHandler + ResponseEmitter | IMPLEMENTED | shared/src/PageRouter/ErrorHandler.php · ResponseEmitter.php |
| K284 | RouteRegistry + SpaRoute | IMPLEMENTED | shared/src/PageRouter/RouteRegistry.php · SpaRoute.php |
| K285 | HTML shell renderer | IMPLEMENTED | shared/src/PageRouter/HtmlShellRenderer.php |
| K286 | AuthGuard + AuthUrlBuilder | IMPLEMENTED | shared/src/PageRouter/AuthGuard.php · AuthUrlBuilder.php |
| K287 | Clean URL redirect | IMPLEMENTED | ADR-009 · api.coremusic.net/.htaccess |
| K288 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K289 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K290 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K291 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K292 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K293 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K294 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K295 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K296 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K297 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K298 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K299 | Rezerve — d06 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K250–K299) · IMPLEMENTED 28 · PLANNED 22 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-009 · ADR-016 · ADR-021 · ADR-026 · ADR-052 · ADR-058 · ADR-084 · ADR-086 · ADR-094 (`.ai/.decisions/accepted/`)
