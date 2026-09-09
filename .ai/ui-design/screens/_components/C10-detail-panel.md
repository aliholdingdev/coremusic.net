---
title: "CoreMusic - C10 Detail Panel Component Spec"
type: component-spec
category: component-spec
date: 2026-08-11
updated: 2026-09-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
platform: home-1024
---

# C10 — Right Detail Panel

## BEM

```css
.detail-panel { }
.detail-panel__art { }
.detail-panel__title { }
.detail-panel__actions { }
.detail-panel__metadata { }
```

## ASCII Art

```
┌── DETAIL PANEL (~30-40%) ──────────────────┐
│  ┌────────────┐                             │
│  │  300×300    │ 圆形 veya kare art          │
│  └────────────┘                             │
│  Başlık (16px, 600)                         │
│  Alt başlık (12px, 400, muted)              │
│  [Hemen Çal] (C04)                          │
│  [Karışık Çal] (C05) [...]                  │
│  ── Metadata ──                             │
│  Kalite: 24 Bit / 48 kHz                    │
│  Boyut: 2 GB | İndirme: 2                   │
│  Parça: 12 | Tür: Arabesk                   │
│  Yıl: Bilinmeyen | Dinlenme: 5              │
│  Süre: 00:30:00                             │
└──────────────────────────────────────────────┘
```

## ITCSS: 03_Layout
## WCAG: ✅ Container
## Kullanım: Albums, Artists, Files, Göz At

---

*Component Spec C10 Detail Panel v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
