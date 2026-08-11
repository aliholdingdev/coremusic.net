---
title: CoreMusic — C09 Media Card Component Spec
date: 2026-08-11
version: 2.0.0
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
