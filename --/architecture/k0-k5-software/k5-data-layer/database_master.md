---
title: "database_master — Eski Veritabanı Master Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-data
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# database_master — Veritabanı Master (stub)

**Durum:** `architecture/k0-k5-software/k5-data-layer/database_master(.md)` **diskte YOK**
(eski ağaç silindi) — 5 link referansı (`.ai/CLAUDE.md` §18, `.ai/brain.md`, `.ai/index.md` …).

## Bugünkü Karşılığı (gerçek kanıt — 2026-10-07 disk ölçümü)

| Öğe | Değer | Kanıt |
|-----|-------|-------|
| Şema dosyaları | **20 .sql** (18 `coremusic_*` + `media_catalog` + `novasearch`) | `.ai/.sql/mysql/` (glob) |
| CREATE TABLE ifadesi | **173** (20 dosya toplamı) | grep 2026-10-07 |
| 18 BCNF DB / 156 tablo (sayım birimi) | `.ai/CLAUDE.md` §18 · ADR-040 | `.ai/.sql/mysql/` (ilk 18 dosya) |
| `media_catalog` (9 tablo) | PLANNED — canlıda yok | `.ai/.sql/mysql/media_catalog.sql` |
| `novasearch` (7 tablo, knex, 18'lik sayım dışı) | IMPLEMENTED | `.ai/.sql/mysql/novasearch.sql` |
| Erişim katmanı | PDO + Repository + Cache (APCu) | `shared/src/Database/` · `shared/src/Repository/` · `shared/src/Cache/` |

**Domain tablosu (tek kaynak):** [[architecture/13-domain-d04-veri-yonetimi]] (K150–K199)
