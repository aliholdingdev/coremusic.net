---
title: "k0-os-layer/database — Eski OS-Katmanı DB Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-os
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# database (k0-os-layer) — OS Katmanı Veritabanı (stub)

**Durum:** `architecture/k0-k5-software/k0-os-layer/database.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **Şemalar (20 dosya):** `.ai/.sql/mysql/` — 18 `coremusic_*` + `media_catalog` + `novasearch`
- **Erişim katmanı:** `shared/src/Database/` · `shared/src/Database/Config/` (PDO, ADR-002)
- **Kararlar:** ADR-003 (multi-DB BCNF) · ADR-040 (database authority) · ADR-014 (migration)
- **Domain tablosu:** [[architecture/13-domain-d04-veri-yonetimi]] (K150–K199)
- **Aynı konunun master stub'u:** [[architecture/k0-k5-software/k5-data-layer/database_master]]
