---
title: "l2-routing/subdomain-routing — Eski Subdomain Routing Dokümanı (stub-with-truth)"
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

# subdomain-routing — Subdomain Routing (stub)

**Durum:** `architecture/l2-routing/subdomain-routing(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **Fiziksel subdomain dizinleri (2026-10-07 glob):** `auth.coremusic.net/` · `home.coremusic.net/` · `api.coremusic.net/` · `media.coremusic.net/` · `assets.coremusic.net/` — **IMPLEMENTED (5)**
- **Yok olan paneller (hedef):** music · admin · download · car · studio · pro · landing — PLANNED
- **Karar:** ADR-043 (auth subdomain consolidation) · ADR-004 (multi-domain SPA)
- **Auth subdomain köprüsü:** `shared/src/PageRouter/AuthUrlBuilder.php` · `AuthGuard.php`
- **Yönlendirme stub'ları:** `.htaccess` dosyaları (her subdomain kökünde)

**Domain tablosu:** [[architecture/16-domain-d07-uygulama-ux]] (K300–K304, K327–K334)
