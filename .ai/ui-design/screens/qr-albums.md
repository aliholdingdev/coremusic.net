---
title: "Albums Page - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S03"
resolution: "1024x600"
layout_pattern: "Standard 60/40"
components: [C01, C09, C11]
png: "home-1024/Linux  1024 - Albumler Page.png"
full_spec: "C-music/albums.md"
---

# Albums Page (1024x600)

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024x600 — Pattern 1: Standard 60/40 — /albums                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [HEADER — ortak 60px]                                                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  <- Albumsler / Tum Albumsler                              [Sanatci Adi Ara 🔍] [≡]            │
│  "Albumsler"                                                                                     │
│  Kutuphanede depolanan tum albumler                                                             │
│                                                                                                  │
│  ┌─ GENRE TABS (C11) ───────────────────────────────────────────────────────────────────────┐  │
│  │ [Tumu] [Pop] [Arabesk] [Dans] [Oyun Havasi] [Damar] [Org] [Yabanci Pop] [Kpop/Kore]     │  │
│  │ ^(aktif)                                                                                │  │
│  │ ~13 sekme, yatay scroll, pembe arka plan (aktif)                                       │  │
│  └──────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                  │
│  ┌── CARD GRID (sol ~614px, %60) ──────────┐  ┌── DETAIL PANEL (sag ~390px, %40) ────────┐  │
│  │                                           │  │                                            │  │
│  │  4 sutun x 3 satir = 12 kart             │  │  ┌──────────────────┐                     │  │
│  │  Gap: 8px                                 │  │  │                  │                     │  │
│  │                                           │  │  │    300x300       │                     │  │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌──────┐│  │   daire Album Art  │  Nobetci Eczane     │  │
│  │  │140x160 │ │140x160 │ │140x160 │ │140x  ││  │    (r:50%)        │  Ferhat Kasetleri   │  │
│  │  │ album  │ │ album  │ │ album  │ │ album ││  │                  │  Kaset              │  │
│  │  │ thumb  │ │ thumb  │ │ thumb  │ │ thumb ││  └──────────────────┘                     │  │
│  │  │────────│ │────────│ │────────│ │───────││                                            │  │
│  │  │Nobetci │ │Bergen  │ │Civanert│ │Bergen ││  ┌────────────────────────────────────┐    │  │
│  │  │Eczane  │ │-Tum    │ │-Tum    │ │-Tum   ││  │          Hemen Cal                 │    │  │
│  │  │Ferhat  │ │Sarkila │ │Sarkila │ │Sarkila││  │          (pembe, full-width)        │    │  │
│  │  │Kasetle │ │ri      │ │r       │ │ri     ││  └────────────────────────────────────┘    │  │
│  │  │ri      │ │00:10:05│ │00:10:05│ │00:10:0││  ┌────────────────────────────────────┐    │  │
│  │  │00:10:05│ │       │ │       │ │ 5     ││  │          Karisik Cal               │    │  │
│  │  └────────┘ └────────┘ └────────┘ └───────┘│  │          (sinir, full-width)        │    │  │
│  │                                           │  └────────────────────────────────────┘    │  │
│  │  (3 satir, dikey scroll)                  │                                             │  │
│  └───────────────────────────────────────────┘  └────────────────────────────────────────────┘  │
│                                                                                                  │
│ [FOOTER — ortak 90px]                                                                           │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Kart Detayı (C09)

```
┌─────────────────┐
│  ┌───────────┐  │
│  │  140x160  │  │  ← album art (dikdortgen, r:8px)
│  │  thumb    │  │
│  └───────────┘  │
│  Album Title     │  ← 12px, 600, max 2 satir
│  Artist Name     │  ← 10px, 400, muted
│  00:10:05        │  ← 10px, 400, accent
└─────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | — | h:60px | `--header-h` |
| Footer | — | h:90px | `--footer-h` |
| Content | y:60-510 | h:450px | `--content-h` |
| Sol panel (grid) | x:16-630 | 614px (~60%) | — |
| Sag panel (detail) | x:642-1008 | 366px (~40%) | `--detail-panel-w` |
| Genre tab yuksekligi | — | ~32px (WCAG: 48px) | `--tab-h` |
| Genre gap | — | 4px | — |
| Genre font | — | 11px, 500 | — |
| Kart thumb | — | 140x160px, r:8px | `--card-thumb-size` |
| Kart toplam | — | ~144x190px | — |
| Grid sutun | — | 4 | — |
| Grid gap | — | 8px | `--grid-gap` |
| Detail art | — | 300x300px daire (r:50%) | `--album-art-size` |
| Detail padding | — | 16px | `--space-4` |
| Glass blur | — | `blur(8px)` | `--glass-blur` |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.nav-link` | Header | ~24x24px (WCAG: 48px) |
| C09 | `.c-card` | Grid, 4x3 = 12 kart | 140x160px thumb |
| C11 | `.albums-tabs__tab` | Ust bant, yatay scroll | ~32px yukseklik |

## CSS Hints

```css
/* Layout — 60/40 split */
.albums-layout { display: grid; grid-template-columns: 1fr var(--detail-panel-w); }

/* Genre tabs — yatay scroll */
.albums-tabs { display: flex; gap: 4px; overflow-x: auto; }

/* Card grid — 4 sutun */
.albums-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; overflow-y: auto; }

/* Detail panel — glass */
.albums-detail { backdrop-filter: blur(8px) saturate(180%); border-radius: var(--card-radius); }

/* Album art — daire */
.albums-detail__art { border-radius: var(--radius-full); width: 300px; height: 300px; }
```

---

*QR Albums v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
