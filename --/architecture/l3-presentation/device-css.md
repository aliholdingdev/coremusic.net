---
title: "l3-presentation/device-css — Eski Cihaz CSS Dokümanı (stub-with-truth)"
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

# device-css — Cihaz CSS Katmanı (stub)

**Durum:** `architecture/l3-presentation/device-css(.md)` **diskte YOK** (eski ağaç silindi) — 2 link.

## Bugünkü Karşılığı (gerçek kanıt)

| Katman | Dosya |
|--------|-------|
| Device override CSS | `assets.coremusic.net/Css/08_Devices/` (dizin) |
| Breakpoint token'ları | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` |
| Layout token'ları per cihaz | `a-layout-tokens-{mobile,1024,1920,3540,3840}.css` (01_Abstracts) |
| Cihaz matrisi (45-tier) | `.ai/ui-design/01-mockup-index.md` · `.ai/scripts/device-matrix-catid.ps1` |
| Responsive mimari kuralları | `.ai/ui-design/05-responsive-architecture.md` (§7.4 4K No-Center · §12 fallback) |
| 4-tier Device Manager tasarımı | `.ai/brain.md` §18B |

**Kural:** Guardrail #17 (Single Component Responsive — 1024x600 pixel reference).
**Domain tablosu:** [[architecture/16-domain-d07-uygulama-ux]] (K317, K326)
