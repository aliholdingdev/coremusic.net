---
title: "l3-presentation/web-audio — Eski Web Audio Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-l3
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# web-audio — Web Audio (stub)

**Durum:** `architecture/l3-presentation/web-audio.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Bileşen | Dosya | Durum |
|---------|-------|-------|
| Core player (kontroller, seekbar) | `assets.coremusic.net/js/coreplayer/coreplayer.controls.js` · `coreplayer.seekbar.js` (5 dosya) | IMPLEMENTED |
| Oynatıcı manager'ları | `assets.coremusic.net/js/managers/` (5 dosya, ThemeManager dahil) | IMPLEMENTED |
| Footer player | `assets.coremusic.net/js/core/footer.init.js` · ADR-018 | IMPLEMENTED |
| Web Audio API / native DSP | — | ⚠️ VERIFICATION REQUIRED — tarayıcı Web Audio kullanımı kanıtlanmadı; native DSP = DESIGN (d02) |

**Domain tablosu:** [[architecture/11-domain-d02-ses-motoru-dsp]] (K050–K053)
