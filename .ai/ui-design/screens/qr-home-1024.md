---
title: "Home Page — Quick Reference"
type: ascii-qr
screen_id: "S01"
resolution: "1024x600"
layout_pattern: "Split Home (42/58)"
components: [C01, C02, C03, C04, C09, C15]
png: "home-1024/Linux  1024 - Home Page.png"
full_spec: "B-home/dashboard.md"
---

# Home Page (1024x600)

## Layout Wireframe

```
┌─────────────────────────────────────────────────────────────┐
│ 1024x600 — Linux Embedded RPi5 — home.coremusic.net        │
├─────────────────────────────────────────────────────────────┤
│ Header: y:0-60, h:60px (fixed)                             │
│ Content: y:60-510, h:450px (scrollable)                    │
│   Content panel: y:71-495 (ustte 11px, altta 15px padding) │
│ Footer: y:510-600, h:90px (fixed)                          │
│ Seek bar: y:510, h:3px, full-width, pembe                  │
└─────────────────────────────────────────────────────────────┘
```

### Header (y:0-60)

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─────────────────────────────────────────────────────────────────────────────────┐ │
│     │"Core Music"  Ana Sayfa  Kesfet  Albumsler  Sanatclar  Goz At  Gecmis  Ayarlar │ │
│     │ y:15    (Bickham)  (nav-link x 8, gap:2-4px, Arima 10px)      [Bayram Ali v]  │ │
│     │                                                        [wifi+bt pill 65x37]    │ │
│     │ y:35                                                             [Ayarlar][Kapa]│ │
│ y:60├─────────────────────────────────────────────────────────────────────────────────┤ │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

### Content Sol Bolge (x:16-420, ~42%)

```
┌──────────────────────────────────────────────┐
│  ┌────────┐ Goksel - Sevil Neselen           │
│  │100x100 │ Hayat Ruya Gibi                  │
│  │album   │ Goksel                           │
│  │art     │                                  │
│  └────────┘ 00:05:00 ======= 00:05:00        │
│                pembe seek bar h:3px           │
│                                              │
│  "En Son Dinlenen" basligi                   │
│  ┌──────┐┌──────┐┌──────┐┌──────┐           │
│  │140x  ││140x  ││140x  ││140x  │           │
│  │140   ││140   ││140   ││140   │           │
│  └──────┘└──────┘└──────┘└──────┘           │
│                                              │
│  "Oynatma Listeleri" basligi                 │
│  ┌──────┐┌──────┐┌──────┐┌──────┐┌──────┐  │
│  │140x  ││140x  ││140x  ││140x  ││140x  │  │
│  │140   ││140   ││140   ││140   ││140   │  │
│  └──────┘└──────┘└──────┘└──────┘└──────┘  │
│  "Siradaki Sarkilar"                         │
│  ┌─ Mini Card ──────────────────────┐        │
│  │ [50x50] Goksel                    │        │
│  │          Sevil Neselen            │        │
│  └───────────────────────────────────┘        │
└──────────────────────────────────────────────┘
```

### Content Sag Bolge (x:420-1008, ~58%)

```
┌──────────────────────────────────────────────┐
│ 🎵 Hoparlörler                               │
│    glass panel ~250x100                      │
│ ☁ Hava Durumu                                │
│    glass panel ~250x100                      │
│ 📅 07:00 — 5 Agustos 2026                   │
│    glass panel ~250x100                      │
│ 📂 Klasörlerim                               │
│    glass panel ~250x100                      │
└──────────────────────────────────────────────┘
Her panel: glass bg, blur(8px), r:12px, pad:12px
```

### Footer Player (y:510-600)

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ y:510 pembe ilerleme cubugu h:3px, full-width                                          │
│     │ y:513┌────────┐ ♪ Sarki Adi  : Goksel - Sevil Neselen                           │ │
│     │       │120x120 │ ● Albumum   : Hayat Ruya Gibi                                   │ │
│     │       │album   │ 🎤 Sanatci  : Goksel                                           │ │
│     │       │art     │                                                                 │ │
│     │ y:550└────────┘    [_onceki]  [oyna]  [dur]  [sonraki]   Sure: 09:00:00 / 00:05:00│ │
│     │                       o    O    o    o      Bit rate : 320 kbps                 │ │
│     │ y:566                   (33px cap daireler)    [ses ===== ] % 100               │ │
│ y:600└─────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Viewport | 1024x600 | Full | — |
| Header | y:0-60 | h:60px | `--header-h` |
| Content | y:60-510 | h:450px | `--content-h` |
| Footer | y:510-600 | h:90px | `--footer-h` |
| Seek bar | y:510 | h:3px, full-width | `--theme-primary` |
| Sol panel | x:16-420 | ~430px (42%) | — |
| Sag panel | x:420-1008 | ~578px (58%) | — |
| Content padding | y:71-495 | ust:11px, alt:15px | — |
| Now Playing art | — | 100x100px, r:8px | `--card-thumb` |
| Kart boyutu | — | 140x140px | `--card-thumb-size` |
| Widget panel | — | ~250x100px | — |
| Widget bg | — | `rgba(255,255,255,0.08)` | `--glass-bg-subtle` |
| Widget blur | — | `blur(8px)` | `--blur-md` |
| Widget border | — | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Widget radius | — | 12px | `--radius-lg` |
| Transport daire | — | 33px cap | `--transport-size` |
| Play daire | — | 38px cap | `--transport-size-lg` |
| Footer album art | x:16, y:513 | 120x120px | `--card-thumb` |
| Volume slider | x:850, y:550 | 145px | `--theme-primary` |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.nav-link` | Header, x:150-600, y:15 | ~24x24px (WCAG: 48px) |
| C02 | `.header-widget` | Header sag, x:870, y:15 | 65x37.4px pill |
| C03 | `.header-user` | Header, x:800, y:15 | ~150x37px pill |
| C04 | `.btn-primary` | Footer / baslangic butonu | 48px+ touch target |
| C09 | `.c-card` | Sol panel kart grid | 140x140px, r:8px |
| C10 | `.detail-panel` | — | — |
| C15 | `.mini-card` | Sag alt sabit | 50x50 thumb + metin |

## CSS Hints

```css
/* Split layout */
.home-layout { display: grid; grid-template-columns: 42% 58%; gap: var(--grid-gap); }

/* Now Playing glass */
.now-playing { background: var(--glass-bg); backdrop-filter: blur(8px); border-radius: var(--radius-card); }

/* Widget area */
.widget { background: rgba(255,255,255,0.08); backdrop-filter: blur(8px); border-radius: 12px; min-height: 100px; }

/* Card grid */
.card-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }

/* Mini card fixed */
.mini-card { position: fixed; bottom: calc(90px + 16px); right: 16px; z-index: var(--z-player); }
```
