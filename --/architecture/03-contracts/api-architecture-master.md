---
title: "api-architecture-master — Eski API Mimarisi Master Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-contracts
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# api-architecture-master — API Mimarisi (stub)

**Durum:** `architecture/03-contracts/api-architecture-master` **diskte YOK** (eski ağaç silindi).
Linkler: `.ai/ROLE.md` §6 · `.ai/index.md` · `.ai/keys.md`.

## Bugünkü Karşılığı (gerçek kanıt)

| İçerik | Nerede |
|--------|--------|
| Gateway / BFF / CQRS / OpenAPI kararları | `.ai/.decisions/accepted/ADR-084-api-gateway-architecture.md` |
| Gateway kodu | `api.coremusic.net/index.php` · `api.coremusic.net/config/{routes,cors}.php` · `shared/src/Api/Gateway.php` |
| BFF (4 adet: SPA/Mobile/Embedded/Desktop) | `shared/src/Api/Bff/` |
| Versioning / Registry / DTO | `shared/src/Api/Versioning/` · `shared/src/Api/Registry/` · `shared/src/Api/Dto/` |
| API pipeline (origin + CSRF) | `.ai/.decisions/accepted/ADR-094-api-pipeline-origin-csrf.md` |
| Katman tablosu | [[architecture/15-domain-d06-servis-api]] (K250–K299) |

⚠️ OpenAPI spec dosyası **yok** (ADR-084: CQRS + OpenAPI = PLANNED, grep 0 — `.ai/log.md` P2-11).
