---
title: "l2-routing/route-config — Eski Route Config Dokümanı (stub-with-truth)"
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

# route-config — Route Config (stub)

**Durum:** `architecture/l2-routing/route-config(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Katman | Dosya |
|--------|-------|
| API route tablosu | `api.coremusic.net/config/routes.php` · `shared/src/Api/Routing/RouteTable.php` |
| Server route registry | `shared/src/PageRouter/RouteRegistry.php` · `SpaRoute.php` |
| JS route config | `assets.coremusic.net/js/router/config/auth-routes.js` (+ 4 dosya) |
| .htaccess rewrite | `api.coremusic.net/.htaccess` · `auth.coremusic.net/.htaccess` |

**Domain tablosu:** [[architecture/15-domain-d06-servis-api]] (K257, K284)
