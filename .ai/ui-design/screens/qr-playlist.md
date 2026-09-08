---
title: "Playlist Page — Quick Reference"
type: ascii-qr
screen_id: "S07"
resolution: "1024x600"
layout_pattern: "Standard 65/35"
components: [C01, C13, C04]
png: "home-1024/Linux  1024 - Playlist Page.png"
full_spec: "D-player/playlist.md"
---

# Playlist Page

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Şimdi Oynatılıyor                                              [Şarkı Ara 🔍]            │
│                                                                                                  │
│  ┌── TABLO (sol ~65%) ─────────────────────────────────────────────────────────────────────┐   │
│  │ /  | Şarkı Adı          | Albüm Adı          | Sanatçı  | Süre     | Favori Yıldızı    │   │
│  ├─────────────────────────────────────────────────────────────────────────────────────────┤   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★☆☆☆          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | PEMBE VURGU     │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  │ [♪] Göksel - Sevil Neşelen | Hayat Rüya Gibi  | Göksel   | 00:00:00 | ★★★★★          │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌── SAĞ PANEL (~350px) ──────────────────────────────────────────────────────────────────┐   │
│  │ [○ Artist Photo ~150×150]                                                               │   │
│  │ Göksel - Sevil Neşelen                                                                 │   │
│  │ Göksel, Hayat Rüya Gibi                                                                │   │
│  │                                                                                         │   │
│  │ [♫][♥][▼][⋯]  (aksiyon ikonları, daire, pembe border)                                 │   │
│  │                                                                                         │   │
│  │ ── Önerilen Sanatçılar ──            ── Takip Edilen Sanatçılar ──                    │   │
│  │ [○×4 dairesel thumb]                 [○×4 dairesel thumb]                              │   │
│  │                                                                                         │   │
│  │ ── Son Öneriler ──                                                                     │   │
│  │ [○×4 dairesel thumb]                                                                   │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Aktif satır: pembe arka plan (opacity)
Tablo başlığı: sabit üstte, sıralanabilir
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | top | 60px | `--header-h` |
| Footer | bottom | 90px | `--footer-h` |
| İçerik | middle | 450px | `--content-h` |
| Sol panel (tablo) | left ~65% | — | — |
| Sağ panel (detail) | right ~35% | 350px | `--detail-panel-w` |
| Satır yüksekliği | — | ~40px | — |
| Artist photo | sağ panel üst | 150×150px | — |
| Touch target | — | ≥48px | `--touch-min` |
| Tablo başlığı | sabit üstte | — | — |
| Font ölçeği | — | 1× | — |

## Table Columns

| Sütun | Genişlik | İçerik |
|-------|----------|--------|
| # | ~30px | Sıra numarası |
| Şarkı Adı | ~%35 | Şarkı başlığı + thumb |
| Albüm Adı | ~%25 | Albüm adı |
| Sanatçı | ~%15 | Sanatçı adı |
| Süre | ~60px | 00:00:00 |
| Favori | ~100px | 5 yıldız (C12) |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.header` | top | 100%×60px |
| C04 | `.footer` | bottom | 100%×90px |
| C13 | `.playlist-table` | sol ~65% | grid columns: 40px 1fr 120px 100px 80px 100px |
| C13 | `.playlist-row` | table rows | 100%×48px min |
| C13 | `.playlist-detail` | sağ ~35% | 350px wide, flex-col |

## Panel Detail

| Bölüm | İçerik |
|-------|--------|
| Üst | Artist Photo (150×150px daire) + şarkı adı + albüm |
| Aksiyon | ♫ ♥ ▼ ⋯ ikonları (daire, pembe border, 44×44px) |
| Önerilen Sanatçılar | 4× dairesel thumb (50×50px) |
| Takip Edilenler | 4× dairesel thumb (50×50px) |
| Son Öneriler | 4× dairesel thumb (50×50px) |

## CSS Hints

```css
.playlist-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  height: var(--content-h);
}
.playlist-row.is-active {
  background: rgba(255,79,216,0.15);
  border-left: 3px solid var(--accent);
}
.playlist-detail__artist-photo {
  width: 150px; height: 150px; border-radius: 50%;
}
.playlist-detail__action {
  width: 44px; height: 44px; border-radius: 50%; border: 1px solid var(--glass-border);
}
```
