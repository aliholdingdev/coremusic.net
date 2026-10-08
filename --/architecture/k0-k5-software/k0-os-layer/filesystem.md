---
title: "k0-os-layer/filesystem — Eski OS Katmanı Filesystem Dokümanı (stub-with-truth)"
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

# filesystem (k0-os-layer) — OS Katmanı Filesystem (stub)

**Durum:** `architecture/k0-k5-software/k0-os-layer/filesystem.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Kaynak | Durum |
|------|--------|-------|
| Medya dosya dizin ekseni (ULID) | ADR-092 · `media.coremusic.net/src/Media/Ulid.php` · `CatalogWriter.php` | PARTIAL |
| Statik dosya servisi | `assets.coremusic.net/` (.htaccess, Css/, js/, Fonts/) | IMPLEMENTED |
| Log dosyaları (kök stream) | `coremusic_php_*.log` (5 dosya) | IMPLEMENTED |
| Vault dosya erişimi | `.ai/scripts/` (validate.mjs vb. — salt-okunur okuyucu) | IMPLEMENTED |
| Offline-first dosya kuyruğu (SQLite) | `.ai/CLAUDE.md` §22 (edge case) | ⚠️ VERIFICATION REQUIRED (kod yok) |

**Domain:** d01 → [[architecture/10-domain-d01-isletim-sistemi-platform]] · d04 → [[architecture/13-domain-d04-veri-yonetimi]] (K187)
