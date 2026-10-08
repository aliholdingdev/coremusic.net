---
title: "l3-presentation/itcss-architecture — Eski ITCSS Mimarisi Dokümanı (stub-with-truth)"
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

# itcss-architecture — ITCSS Mimarisi (stub)

**Durum:** `architecture/l3-presentation/itcss-architecture(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt — 11 katman, ls 2026-10-07)

```text
assets.coremusic.net/Css/
├── 01_Abstracts/   (a-design-tokens, a-colors-token, a-layout-tokens-{mobile,1024,1920,3540,3840} …)
├── 02_Base/ · 03_Layout/ · 04_Components/ · 05_Pages/ · 06_Utilities/
├── 07_Vendors/ (dokunulmaz) · 08_Devices/ · 09_ViewModes/ · 10_Helpers/ · 11_OAuth/
└── auth-bundled.css
```

- **Karar:** ADR-001 (Vanilla JS + ITCSS — Frozen)
- **Şablon otoritesi:** `.ai/.templates/frontend/css-template.md` (11 katman sırası 01→11) · kök `notes.md`
- ⚠️ Vault iddiası "ITCSS 9-layer" (`.claude/CLAUDE.md` §12) — **disk ölçümü 11 dizin** (bu satır ölçümdür)

**Domain tablosu:** [[architecture/16-domain-d07-uygulama-ux]] (K314–K318)
