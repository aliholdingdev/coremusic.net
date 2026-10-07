---
title: "l5-services — Eski L5 Servisler Katmanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-layers
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# l5-services — L5 Servisler Katmanı (stub)

**Durum:** `architecture/l5-services.md` **diskte YOK** (eski ağaç silindi) — 3 link referansı
(eski 4-katmanlı mimarinin "Servis Katmanı" satırı — `.ai/CLAUDE.md` §32).

## Bugünkü Karşılığı (gerçek kanıt)

**Uygulanan servisler (2026-10-07 glob):**

| Servis | Dizin | Durum |
|--------|-------|-------|
| Auth (merkezi) | `auth.coremusic.net/` | IMPLEMENTED |
| API Gateway + BFF | `api.coremusic.net/` · `shared/src/Api/` | IMPLEMENTED |
| Media (CLI) | `media.coremusic.net/` | IMPLEMENTED |
| AI çekirdek | `shared/src/AI/` (7 sınıf) | IMPLEMENTED |

**Uygulanmayan hedef servisler (.claude/CLAUDE.md §10):** Control (81) · Audio (9741/9742) ·
Device · Network Audio · Download (3001) → **PLANNED** (dizin yok).

**Hedef yedek veri katmanı (L1 alt tablo):** SQL Server / MongoDB — ⚠️ VERIFICATION REQUIRED
(`.ai/CLAUDE.md` §5 L1 alt tablosu iddiası; `.ai/.sql/mysql/` yalnız MySQL dosyaları içerir).

**Modern karşılık:** [[architecture/15-domain-d06-servis-api]] (K250–K299)
