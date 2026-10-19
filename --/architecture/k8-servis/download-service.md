---
title: "download-service — Download Servisi (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-services
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# download-service — Download Servisi (stub)

**Durum:** `architecture/k8-servis/download-service` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Kaynak | Durum |
|------|--------|-------|
| Servis tanımı (Node.js + TS, port 3001) | `.claude/CLAUDE.md` §10 (7. satır) · §11 Port Register | PLANNED |
| Kod | ⚠️ VERIFICATION REQUIRED — `download.coremusic.net` dizini yok (glob 2026-10-07) |
| Mimari karar | `.ai/.decisions/accepted/ADR-026-download-service-architecture.md` | ADR |
| Anti-ban kararı | `.ai/.decisions/accepted/ADR-028-anti-ban-system.md` | ADR |
| Veritabanı (queue/history/cache) | `.ai/.sql/mysql/coremusic_download.sql` | IMPLEMENTED (veri) |
| JS stack hedefi | Kök `CLAUDE.md` §Skill Registry — download service Vitest hedefi | PLANNED |

**Domain:** d06 → [[architecture/15-domain-d06-servis-api]] (K274 = PLANNED) · d08 (K374–K377)
