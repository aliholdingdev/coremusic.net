---
title: "l2-routing/url-normalization — Eski URL Normalization Dokümanı (stub-with-truth)"
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

# url-normalization — URL Normalization (stub)

**Durum:** `architecture/l2-routing/url-normalization(.md)` **diskte YOK** (eski ağaç silindi) — 2 link.

## Bugünkü Karşılığı (gerçek kanıt)

- **Kod:** `shared/src/PageRouter/RequestNormalizer.php` — IMPLEMENTED
- **Karar:** ADR-016 (`url-normalization`) · ADR-009 (`clean-url-redirect`) — `.ai/.decisions/accepted/`
- **Köprü:** `api.coremusic.net/.htaccess` (rewrite) · `shared/src/PageRouter/ResponseEmitter.php`
- **CSRF/origin göçü:** ADR-094 (query param koruma — `.ai/log.md` P3-17 kaydı)

**Domain tablosu:** [[architecture/15-domain-d06-servis-api]] (K282, K287)
