---
title: "File List - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S10"
resolution: "1024x600"
layout_pattern: "3-column + detail"
components: [C01, C04, C09, C16]
png: "home-1024/Linux  1024 - Göz At - Tıklama Clikced.png"
full_spec: "E-filemanager/file-list.md"
---

# File List (Göz At — Tıklama)

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← → Dosya Yöneticisi / Musics : Root    c:\users\Bayram Ali\Music    [Dosya Ara 🔍]         │
│                                                                                                  │
│  ┌─ SOL SIDEBAR (167px) ──────────────┐  ┌─ ORTA LİSTE (573px) ─────────────────────────┐   │
│  │ ● Tüm Şarkılar           1000      │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre  │   │
│  │ ● Son Eklenenler          100       │  │ [♪] Pop Şarkıları Ali                        │   │
│  │ ● Son Dinlenenler          50       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Favoriler                20       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Oynatma Listeleri         5       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00:  │   │
│  │ ● Sanatçılar              100       │  │                                             │   │
│  │ ● Albümler                 40       │  │                                             │   │
│  │ ● Türler                   50       │  │                                             │   │
│  │ ● Videolar                125       │  │                                             │   │
│  │ ● Podcast                  12       │  │                                             │   │
│  │                                    │  │                                             │   │
│  │ Core Tropu                       │  │                                             │   │
│  │ ▼ System Disk (C:)              │  │                                             │   │
│  │ ▼ USB Disk (E:) ← PEMBE SEÇİLİ  │  │                                             │   │
│  └────────────────────────────────────┘  └────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌─ SAĞ BİLGİ PANELİ (220px) ───────────────────────────────────────────────────────────┐   │
│  │ SSD Disk (E:)                                        [sil][düzenle][kopyala][yapış]   │   │
│  │ Sanat Güneştepe23                                                                │   │
│  │ ┌──────────┐                                                                           │   │
│  │ │Donut Chart│  65 GB — Kullanılan 49.5 GB, Boş 15.5 GB                                │   │
│  │ └──────────┘                                                                           │   │
│  │ [Göz At] (pembe)                                                                       │   │
│  │                                                                                         │   │
│  │ ── Disk Kullanım Bilgisi / Music ──                                                    │   │
│  │ ┌─ Bar Charts ──────────────────────┐                                                 │   │
│  │ │ [mavi bar] 1000 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar]  948 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar]  300 | [pembe bar]     │                                                 │   │
│  │ │ [mavi bar] 1200 | [pembe bar]     │                                                 │   │
│  │ └───────────────────────────────────┘                                                 │   │
│  │ ┌─ Genre Pie Chart ──────────────────┐                                                │   │
│  │ │ [pie chart — renkli dilimler]       │                                                │   │
│  │ │ Pop: 1000 | Arabesk: 600 | Dans:.. │                                                │   │
│  │ └───────────────────────────────────┘                                                 │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Fark: Sol sidebar'da kategori listesi + sayılar
Sağ panel'de donut chart + bar charts + pie chart
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | top | 60px | `--header-h` |
| Footer | bottom | 90px | `--footer-h` |
| İçerik | middle | 450px | `--content-h` |
| Sol sidebar | left | 167px | `--sidebar-w` |
| Orta liste | center | 573px | — |
| Sağ panel | right | 224px | — |
| Satır yüksekliği | — | ~40px | — |
| Thumb | — | 30×30px | — |
| Donut chart | sağ panel | ~100×100px | — |
| Bar charts | sağ panel alt | 4-5 yatay bar | — |
| Pie chart | sağ panel alt | ~100px çap | — |
| Touch target | — | ≥48px | `--touch-min` |
| Glass blur | — | blur(8px) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.header` | top | 100%×60px |
| C04 | `.footer` | bottom | 100%×90px |
| C09 | `.file-table` | orta liste | 573px, grid: 40px 1fr 120px 100px 80px |
| C09 | `.file-row` | table rows | 100%×48px min |
| C16 | `.file-sidebar` | sol sidebar | 167px, kategori listesi |
| C16 | `.file-info` | sağ panel | 224px, donut+bar+pie |

## Sidebar Categories

| Kategori | Sayı |
|----------|------|
| Tüm Şarkılar | 1000 |
| Son Eklenenler | 100 |
| Son Dinlenenler | 50 |
| Favoriler | 20 |
| Oynatma Listeleri | 5 |
| Sanatçılar | 100 |
| Albümler | 40 |
| Türler | 50 |
| Videolar | 125 |
| Podcast | 12 |

## Charts Detail

| Chart | Boyut | İçerik |
|-------|-------|--------|
| Donut Chart | ~100×100px | Kullanılan/Boş disk alanı (mavi/pembe) |
| Bar Charts | 4-5 yatay bar | Mavi + pembe segment, kategori adı + sayı |
| Genre Pie Chart | ~100px çap | Pop: 1000, Arabesk: 600, Dans:.. |

## CSS Hints

```css
.file-layout {
  display: grid;
  grid-template-columns: 167px 1fr 224px;
  height: var(--content-h);
}
.file-sidebar__category.is-selected {
  background: var(--accent-bg);
}
.file-table__header {
  display: grid;
  grid-template-columns: 40px 1fr 120px 100px 80px;
  position: sticky; top: 0;
}
.file-info {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur) var(--glass-saturate);
  border: var(--card-border);
  border-radius: var(--card-radius);
}
```

---

*QR File List v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
