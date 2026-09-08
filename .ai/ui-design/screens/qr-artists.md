---
title: "Artists Page — Quick Reference"
type: ascii-qr
screen_id: "S05"
resolution: "1024x600"
layout_pattern: "Standard 60/40"
components: [C01, C09]
png: "home-1024/Linux  1024 - Singer Page.png"
full_spec: "C-music/artists.md"
---

# Artists Page (1024x600)

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak 60px]                                                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  <- Sanatcilar / Tum Sanatcilar                                [Sanatci Adi Ara 🔍] [≡]       │
│  "Sanatcilar"                                                                                    │
│  Kutuphanede depolanan tum sanatcilar                                                          │
│                                                                                                  │
│  [Tumu] [Pop] [Arabesk] [Dans] [Oyun Havasi] [Damar] [Org] [Yabanci Pop] [Kpop/Kore] ...      │
│                                                                                                  │
│  ┌── CARD GRID (sol ~70%) ─────────────┐  ┌── DETAIL PANEL (sag ~30%) ────────┐  │
│  │                                       │  │                                    │  │
│  │  Dairesel kartlar (border-radius: 50%)│  │  ┌──────────────────┐              │  │
│  │  5 sutun x 2 satir = 10 kart          │  │  │                  │              │  │
│  │                                       │  │  │    daire 200x200  │              │  │
│  │  ┌──────┐┌──────┐┌──────┐┌─────┐┌────┐│  │  │   Artist Photo  │  Sibel Can   │  │
│  │  │ ○○○○ ││ ○○○○ ││ ○○○○ ││○○○○ ││○○○○││  │  │    (r:50%)      │  Turkce Pop  │  │
│  │  │Sibel ││Dilso'││Ankara││Ankar││Berg││  │  └──────────────────┘              │  │
│  │  │ Can  ││  z   ││li Ay││li Ya-││en  ││  │                                    │  │
│  │  │Turkce││Turkce││Oyun ││semin ││Arab││  │  ♫ 48  🎵 8  📅 1988              │  │
│  │  │ Pop  ││ Pop  ││Havas││      ││esk ││  │                                    │  │
│  │  │45 Sar││48 Sar││42 Sa││42 Sar││45 S││  │  [bio metni — uzun aciklama]       │  │
│  │  └──────┘└──────┘└─────┘└──────┘└────┘│  │  Sibel Can, Turk muziginin en      │  │
│  │                                       │  │  onemli isimlerinden biridir...     │  │
│  │  ┌──────┐┌──────┐┌──────┐┌─────┐┌────┐│  │                                    │  │
│  │  │ ○○○○ ││ ○○○○ ││ ○○○○ ││○○○○ ││○○○○││  │  [Hemen Cal] (C04, pembe)          │  │
│  │  │ ...  ││ ...  ││ ...  ││ ... ││ ... ││  │  [Karisik Cal] (C05, sinir)        │  │
│  │  └──────┘└──────┘└─────┘└──────┘└────┘│  │  [...]                             │  │
│  │                                       │  └────────────────────────────────────┘  │
│  │  Her kart: ~120x170px                │                                           │
│  │    thumb: 100x100px, daire, r:50%    │                                           │
│  │    name: 12px, 600                   │                                           │
│  │    genre: 10px, 400, muted           │                                           │
│  │    count: 10px, 400, accent          │                                           │
│  └───────────────────────────────────────┘                                           │
│                                                                                      │
│ [FOOTER — ortak 90px]                                                                │
└──────────────────────────────────────────────────────────────────────────────────────┘

FARK: Kartlar DAIRESEL (border-radius: 50%), Albumsler'de KARE
```

### Kart Detayı

```
┌──────────┐
│ ○○○○○○○○ │  ← 100x100px daire (border-radius: 50%)
│  Sibel   │  ← 12px, 600
│  Can     │
│ Turkce   │  ← 10px, 400, muted
│  Pop     │
│45 Sarki  │  ← 10px, 400, accent
└──────────┘
Toplam kart: ~120x170px
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | — | h:60px | `--header-h` |
| Footer | — | h:90px | `--footer-h` |
| Content | y:60-510 | h:450px | `--content-h` |
| Sol panel (grid) | — | ~70% | — |
| Sag panel (detail) | — | ~30% | `--detail-panel-w` |
| Kart boyutu | — | ~120x170px (dairesel) | `--card-thumb-size` |
| Kart thumb | — | 100x100px daire (r:50%) | — |
| Kart isim | — | 12px, 600 | — |
| Kart tur | — | 10px, 400, muted | — |
| Kart sayi | — | 10px, 400, accent | — |
| Grid sutun | — | 5 | — |
| Grid gap | — | 6px | `--grid-gap` |
| Genre tab | — | ~32px yukseklik (WCAG: 48px) | `--tab-h` |
| Detail foto | — | ~200x200px daire (r:50%) | `--album-art-size` |
| Detail isim | — | 16px, 600 | — |
| Detail tur | — | 12px, 400, muted | — |
| Detail istatistik | — | ♫ 48 🎵 8 📅 1988 | — |
| Detail bio | — | ~3-4 satir, 11px, 400, muted | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.nav-link` | Header | ~24x24px (WCAG: 48px) |
| C09 | `.artist-card` | Grid, 5x2 = 10 kart | 120x170px, thumb 100x100px daire |

## CSS Hints

```css
/* Layout — 70/30 split */
.artists-layout { display: grid; grid-template-columns: 1fr var(--detail-panel-w); }

/* Artist grid — 5 sutun */
.artists-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }

/* Artist card — dairesel thumb */
.artist-card__thumb { width: 100px; height: 100px; border-radius: var(--radius-full); }

/* Detail panel — glass + daire foto */
.artists-detail__photo { width: 200px; height: 200px; border-radius: var(--radius-full); }
.artists-detail { backdrop-filter: blur(8px) saturate(180%); }
```
