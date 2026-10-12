---
title: "CoreMusic — shared/src/PageRouter Bağlam"
type: context
folder: "shared/src/PageRouter"
category: layer2-routing
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# PageRouter — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k9-api-routing]]

---

## 1. Bağlam

SPA sayfa router çekirdeği (ADR-083/021). 14 dosya ile middleware orchestration, route eşleştirme, HTML shell üretimi ve response emit işlemlerini yönetir. Server-side hybrid rendering'in merkezi.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 14 PHP dosyası |
| ADR | ADR-021 (SPA router contract), ADR-083 (SPA Router Architecture) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `PageRouterKernel.php` | Ana kernel, middleware orchestrasyonu |
| `PageRouter.php` | Tekil sayfa çözücü, route eşleştirme |
| `HtmlShellRenderer.php` | HTML shell üretimi (Theme + ViewMode entegre) |
| `AuthGuard.php` | Auth guard pipeline |
| `RouteRegistry.php` | Route kaydı ve eşleştirme |
| `RequestNormalizer.php` | Request normalize, cleanup |
| `ResponseEmitter.php` | Response emit (header, body, status) |
| `Route.php` | Route entity tanımı |
| `RouteCollection.php` | Route koleksiyonu yönetimi |
| `RouteMatcher.php` | URL pattern eşleştirme |
| `MiddlewareResolver.php` | Middleware zinciri çözücü |
| `Request.php` | PSR-7 Request wrapper |
| `Response.php` | PSR-7 Response wrapper |
| `templates/` | HTML şablonları |

---

## 3. Request Akışı

```
1. index.php → PageRouterKernel::handle()
2. RequestNormalizer → Request normalize
3. RouteRegistry → Route eşleştir
4. AuthGuard → Auth kontrolü
5. MiddlewareResolver → Middleware zincirini çalıştır
6. Controller → İş mantığı
7. HtmlShellRenderer → HTML shell üret
8. ResponseEmitter → Response emit
```

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../../auth.coremusic.net/CLAUDE.md]] | Auth route'ları |
| Kullanıcı | [[../../home.coremusic.net/CLAUDE.md]] | Home route'ları |
| Bağımlı | [[../Middleware/CLAUDE.md]] | Middleware pipeline |
| Bağımlı | [[../Session/CLAUDE.md]] | Session yönetimi |
| Referans | [[../../.ai/architecture/k9-api-routing]] | API/Router mimarisi |
| ADR | [[../../.ai/decisions/accepted/ADR-083-spa-router]] | SPA Router |

---

## 5. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Route ekleme ohne registry | Route kaybı |
| 2 | Auth check atlatma | Güvenlik açığı |
| 3 | Response emit olmadan return | Boş sayfa |
| 4 | Template dosyasını değiştirme | Runtime hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
