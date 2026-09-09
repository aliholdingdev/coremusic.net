---
title: "Album Detail - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S04"
resolution: "1024x600"
layout_pattern: "Standard 60/40"
components: [C01, C09, C10, C12, C13]
png: "home-1024/Linux  1024 - Albumler Details Detay Page.png"
full_spec: "C-music/album-detail.md"
---

# Album Detail (1024x600)

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak 60px]                                                                           │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  <- Album Detayi                                                 [Sarki Ara 🔍]               │
│                                                                                                  │
│  ┌─ TRACK LIST (sol ~70%) ─────────────────────────────────────┐  ┌─ DETAIL (sag ~30%) ──┐    │
│  │                                                               │  │                       │    │
│  │  Album : Goksel - Hayat Ruya Gibi / Sarki Adi    Sure  ★    │  │  ┌──────────────┐     │    │
│  │  ─────────────────────────────────────────────────────────── │  │  │  daire        │     │    │
│  │  [□] Goksel - Sevil Neselen              00:00:00  ★★★★★    │  │  │  ~200x200     │     │    │
│  │  [□] Goksel - Kabahat Senin              00:00:00  ★★★★★    │  │  │  Album Art    │     │    │
│  │  [□] Goksel - Sevil Neselen              00:00:00  ★★★★★    │  │  └──────────────┘     │    │
│  │  [□] Goksel - Sevil Neselen              00:00:00  ★★★★☆    │  │  Hayat Ruya Gibi      │    │
│  │  [□] Goksel - Sevil Neselen  ← PEMBE    00:00:00  ★★★★★    │  │  Goksel               │    │
│  │  [□] Goksel - Sevil Neselen              00:00:00  ★★★★★    │  │                       │    │
│  │  [□] Goksel - Sevil Neselen              00:00:00  ★★★★★    │  │  [Hemen Cal] pembe    │    │
│  │                                                               │  │  [Karisik Cal] sinir  │    │
│  │  Aktif satır: pembe bg + pembe border-left                   │  │  [...]                 │    │
│  │                                                               │  │                       │    │
│  │  ┌─ Sol Alt (kucuk album karti) ───────┐                    │  │  ── Metadata ──       │    │
│  │  │ [□ ~80x80]  Hayat Ruya Gibi         │                    │  │  Kalite: 24B/48kHz   │    │
│  │  │              Goksel ★★★★★            │                    │  │  Boyut: 3 GB         │    │
│  │  │              350 Kbps                │                    │  │  Indirme: 2          │    │
│  │  │              2024.12 Sarki.00:30:00  │                    │  │  Parca: 11           │    │
│  │  └──────────────────────────────────────┘                    │  │  Tur: Arabesk        │    │
│  │                                                               │  │  Yil: Bilinmeyen     │    │
│  └───────────────────────────────────────────────────────────────┘  │  Dinlenme: 10        │    │
│                                                                     │  Sure: 00:30:00      │    │
│                                                                     └───────────────────────┘    │
│ [FOOTER — ortak 90px]                                                                           │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Aktif Satir Detayı

```
┌═════════════════════════════════════════════════════════════════════════════════════════════┐
│ [□] Goksel - Sevil Neselen  ← PEMBE VURGU              00:00:00  ★★★★★                   │
│  background: rgba(255,79,216,0.15)                                                          │
│  border-left: 3px solid var(--theme-primary)                                                │
│  text color: var(--theme-primary) (baslik icin)                                             │
└═════════════════════════════════════════════════════════════════════════════════════════════┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | — | h:60px | `--header-h` |
| Footer | — | h:90px | `--footer-h` |
| Content | y:60-510 | h:450px | `--content-h` |
| Sol panel (track list) | — | ~70% | — |
| Sag panel (detail) | — | ~30%, 366px | `--detail-panel-w` |
| Track satir yuksekligi | — | ~40px (WCAG: 48px) | `--row-h` |
| Track thumb | — | 24x24px, r:4px | — |
| Track sutun: thumb | — | 24px | — |
| Track sutun: baslik | — | ~%55 | — |
| Track sutun: sure | — | ~60px | — |
| Track sutun: yildiz | — | ~100px | — |
| Aktif satir bg | — | `rgba(255,79,216,0.15)` | — |
| Aktif border-left | — | 3px solid `var(--theme-primary)` | — |
| Yildiz boyutu | — | 20x20px (WCAG: 48px) | `--star-size` |
| Yildiz bos | — | `rgba(255,255,255,0.3)` | — |
| Yildiz dolu | — | `#FFD700` (altin) | — |
| Detail art | — | ~200x200px daire (r:50%) | `--album-art-size` |
| Detail baslik | — | 16px, 600 | — |
| Detail alt baslik | — | 12px, 400, muted | — |
| Metadata font | — | 11px, 400, muted | — |
| Tablo basligi | — | sticky, glass bg | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.nav-link` | Header | ~24x24px (WCAG: 48px) |
| C09 | `.c-card` | Sol alt kucuk kart | 80x80px thumb |
| C10 | `.album-detail-panel` | Sag panel | ~30% width, glass |
| C12 | `.star-rating` | Track list sag sutun | 20x20px per star |
| C13 | `.track-list` | Sol panel, dikey scroll | ~70% width |

## CSS Hints

```css
/* Layout — 70/30 split */
.album-detail-layout { display: grid; grid-template-columns: 1fr var(--detail-panel-w); }

/* Track row — grid icinde grid */
.track-row { display: grid; grid-template-columns: 24px 1fr 80px 100px; min-height: 48px; }

/* Aktif satir */
.track-row.is-active { background: rgba(255,79,216,0.15); border-left: 3px solid var(--theme-primary); }

/* Detail panel — glass */
.album-detail-panel { backdrop-filter: blur(8px) saturate(180%); }

/* Album art — daire */
.album-detail-panel__art { border-radius: var(--radius-full); }
```

---

*QR Album Detail v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
