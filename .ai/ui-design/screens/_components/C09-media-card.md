---
title: "CoreMusic - C09 Media Card Component Spec"
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

# C09 — Media Card (Album/Artist)

## BEM

```css
.media-card { }
.media-card__thumb { }
.media-card__title { }
.media-card__artist { }
.media-card__duration { }
.media-card--circular { }
```

## ASCII Art

```
KARE (Album):
┌────────────────┐
│ ┌──────────┐   │
│ │  140×140 │   │  thumb, r:8px
│ └──────────┘   │
│ Album Title     │  12px, 600
│ Artist Name     │  10px, 400, muted
│ 00:10:05        │  10px, 400, accent
└────────────────┘

DAİRESEL (Artist):
┌────────────────┐
│   ┌────────┐   │
│   │ 140×140│   │  thumb, r:50%
│   └────────┘   │
│ Artist Name     │
│ Genre           │
│ 45 Şarkı        │
└────────────────┘
```

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (~140×180px)
## Kullanım: Albums, Artists, Home

---

*Component Spec C09 Media Card v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
