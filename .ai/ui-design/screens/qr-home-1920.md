---
title: "Desktop Home - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S06"
resolution: "1920x1080"
layout_pattern: "Top-Band Home"
components: [C01, C02, C03, C09]
png: "home-1920/Linux - 1920 - Home.png"
full_spec: "B-home/dashboard-1920.md"
---

# Desktop Home (1920x1080)

## Layout Wireframe

```
┌─────────────────────────────────────────────────────────────┐
│ 1920x1080 — Linux Desktop — home.coremusic.net              │
├─────────────────────────────────────────────────────────────┤
│ Header: y:0-65, h:65px (fixed, transparent)                 │
│ Content: y:65-1010, h:945px                                 │
│   Ust bant: y:95-290 (Now Playing + Welcome + Widget)       │
│   Chip satir 1: y:335-420 (En Son Dinlenen x9)             │
│   Chip satir 2: y:455-545 (Playlistler x6)                  │
│   Bos alan: y:545-1010 (arka plan gorunur)                  │
│ Footer: y:1010-1080, h:70px (fixed)                         │
│ Seek bar: y:1010, h:3px, full-width, pembe                  │
└─────────────────────────────────────────────────────────────┘
```

### Full View

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌── HEADER 65px ────────────────────────────────────────────────────────────────────────────────────────────────────┐  │
│     │ "Core Music" Ana Sayfa Kesfet Albumsler Sanatcilar Goz At Gecmis Ayarlar Hakkimizda  [Bayram Ali][EQ][O][link|kapa]│  │
│ y:65├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤│
│     │ ┌── NOW PLAYING CARD ─────────┐ ┌── WELCOME BANNER ─┐  ┌── WIDGET BOLGESI ─────────────────────────────────┐    │
│     │ │ (~465x195) glass blur(12)    │ │ (~495x195)        │  │ x:1075-1855 — 3 satir x [genis kart + karo]      │    │
│     │ │ [150x150] ♪ Sarki Adi       │ │ "Hos Geldin"      │  │ S1: [Hoparlör 165x55][Hava 165x55][□□□]          │    │
│     │ │           ● Album / 🎤      │ │ "Bayram Ali"      │  │ S2: [07:00 165x55][EQ 110x55][□□□]                │    │
│     │ │           ★ ★★★★★ pembe     │ │ (buyuk serif 28)  │  │ S3: [Kutuphanemiz 165x55][YT][♥][~][□□□]          │    │
│     │ │           Bit rate: 350 kbps │ │ "Muzik, ruhun     │  │ karo ~55x55 cam; genis cam ~110x160                │    │
│     │ │ ▶ ===========●──── h:4px     │ │  gizli dili..."   │  │ 2.450 | 156 | 87 | 42 | 1.250 (5 istatistik)      │    │
│     │ └─────────────────────────────┘ │ [Kesfetmeye Basla▶]│  └───────────────────────────────────────────────────┘    │
│     │                                  └───────────────────┘                                                      │
│ y:335 "En Son Dinlenen Sarkilar" (14px, 600) — 9 chip (~170x55), glass, gap 12px                                │
│ y:455 "Playlistler" (14px, 600) — 6 chip (~170x55), ayni format, gap 12px                                         │
│ y:560 (bos alan — arka plan fotografi gorunur)                                                                     │
│ y:1010 pembe ilerleme cubugu h:3px                                                                                 │
│     │ [60x60] ♪ Sarki Adi / ● Album / 🎤 Sanatci   ▶ pembe daire 36px  [utility ikonlar] [ses =====] % 100       │
│ y:1080└───────────────────────────────────────────────────────────────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Viewport | 1920x1080 | Full | — |
| Header | y:0-65 | h:65px, transparent | `--header-h` |
| Content | y:65-1010 | h:945px | `--content-h` |
| Footer | y:1010-1080 | h:70px, fixed | `--footer-h` |
| Seek bar | y:1010 | h:3px, full-width | `--theme-primary` |
| Ust bant | y:95-290 | h:~195px | — |
| Now Playing Card | x:55-520 | ~465x195px, blur(12) | — |
| Welcome Banner | x:555-1050 | ~495x195px | — |
| Widget bolgesi | x:1075-1855 | flex:1 | — |
| Widget genis kart | — | ~165x55px | — |
| Widget karo | — | ~55x55px | — |
| Chip boyutu | — | ~170x55px | — |
| Chip thumb | — | 40x40px | — |
| Chip gap | — | 12px | — |
| Chip bg | — | `rgba(255,255,255,0.08)`, blur(12px), r:8px | — |
| Footer album art | — | 60x60px | — |
| Footer play daire | — | 36px cap, pembe | `--transport-size` |
| Logo | x:25-125 | Bickham Script Two | `--font-logo` |
| Nav link | x:135-670 | Arima ~11px, gap ~14px | C01 |
| Right cluster | — | C03 user pill + 3x C02 pill (~50x22, r:50px) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.nav-link` | Header, x:135-670 | Arima ~11px, gap 14px |
| C02 | `.header-widget` | Header sag cluster | ~50x22px pill, r:50px |
| C03 | `.header-user` | Header sag cluster | User pill |
| C09 | `.c-card` | Chip satirlari (mini varyant) | ~170x55px |

## CSS Hints

```css
/* Top-band layout — 3 kolon flex */
.home-1920 { display: grid; grid-template-rows: 65px 1fr 70px; min-height: 100vh; }
.home-1920__topband { display: flex; gap: 16px; padding: 30px 55px 0; min-height: 225px; }

/* Now Playing + Welcome sabit genislik */
.home-1920__nowplaying { width: 465px; flex-shrink: 0; }
.home-1920__welcome    { width: 495px; flex-shrink: 0; }
.home-1920__widgets    { flex: 1; display: flex; flex-direction: column; gap: 15px; }

/* Chip satirlari — yatay scroll */
.home-1920__chips { display: flex; gap: 12px; overflow-x: auto; padding: 0 55px; }
.home-1920__chip  { width: 170px; height: 55px; flex-shrink: 0; background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border-radius: 8px; }
```

---

*QR Home 1920 v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
