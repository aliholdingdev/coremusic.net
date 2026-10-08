---
title: "l2-routing/spa-router — Eski L2 SPA Router Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-routing
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# spa-router — L2 SPA Router (stub)

**Durum:** `architecture/l2-routing/spa-router(.md)` **diskte YOK** (eski ağaç silindi) — 2 link.

## Bugünkü Karşılığı (gerçek kanıt)

| Katman | Dosya |
|--------|-------|
| JS SPA router çekirdeği (23 dosya) | `assets.coremusic.net/js/router/` (ContentFetcher, AuthBoundaryDetector, CacheLayer …) |
| Router config (5 dosya) | `assets.coremusic.net/js/router/config/` (auth-routes, headers, css-selectors …) |
| Server-authoritative kernel | `shared/src/PageRouter/PageRouterKernel.php` · `RouteRegistry.php` · `SpaRoute.php` |
| Kararlar | ADR-021 (router immutable contract) · ADR-093 (view-modes single load path) · ADR-016 (URL normalization) |

**Domain tablosu:** [[architecture/15-domain-d06-servis-api]] (K280–K285) · [[architecture/16-domain-d07-uygulama-ux]] (K305–K306)
