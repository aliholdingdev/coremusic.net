---
title: "k9-api-routing/spa-router — SPA Router (stub-with-truth)"
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

# spa-router — K9 SPA Router (stub)

**Durum:** `architecture/k9-api-routing/spa-router` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **JS router:** `assets.coremusic.net/js/router/` (23 dosya) + `config/` (5) · çekirdek `js/core/CoreMusicApp.js`
- **Server kernel (server-authoritative):** `shared/src/PageRouter/` (14 sınıf: PageRouterKernel, RouteRegistry, SpaRoute, RequestNormalizer …)
- **Kararlar:** ADR-021 (router immutable contract) · ADR-004 (multi-domain SPA) · ADR-093 (view-modes single-load) · ADR-016 (URL normalization)
- **Aynı konu stub'ları:** `l2-routing/spa-router.md` · `l2-routing/js-router.md`

**Domain:** d06+d07 → [[architecture/15-domain-d06-servis-api]] (K280–K285) · [[architecture/16-domain-d07-uygulama-ux]] (K305–K308)
