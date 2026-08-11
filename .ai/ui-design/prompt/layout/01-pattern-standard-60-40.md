---
title: "Layout Pattern — Standard 60/40 Split"
category: layout-pattern
version: "2.0.0"
date: "2026-08-11"
viewport: "1024×600"
platform: "home-1024 (Linux Embedded RPi5)"
references:
  - [[01-1024-embedded]]
  - [[screens/00-ascii-art-views]] §3-6
  - [[01-component-inventory]] C09, C10, C11, C12, C13
---

# Layout Pattern: Standard 60/40 Split (v2.0.0)

## Kullanım Alanları

| Sayfa | Rota | Sol Alan | Sağ Alan |
|-------|------|----------|----------|
| Albums | `/albums` | Card Grid (C09) | Detail Panel (C10) |
| Album Detail | `/album/:id` | Track List (C13) | Detail Panel (C10) |
| Artists | `/artists` | Card Grid (C09, daire) | Detail Panel (C10) |
| Playlist | `/playlist/:id` | Track List (C13) | Detail Panel (C10) |

---

## 1. YAPI

```
┌─────────────────────────────────────────────────────────────────┐
│ 1024×600 — Standard 60/40 Split                                 │
├─────────────────────────────────────────────────────────────────┤
│ Header: y:0-60, h:60px (position: sticky, z-index: 1000)      │
│                                                                 │
│ ┌── SOL ALAN (60% = 614px) ──┐  ┌── SAĞ ALAN (40% = 394px) ─┐│
│ │                              │  │                             ││
│ │  Card Grid / Track List      │  │  Detail Panel               ││
│ │                              │  │                             ││
│ │  x:16 ────────────── gap:16px──  x:646                      ││
│ │                              │  │                             ││
│ │  y:71 ──────────────         │  │  y:71                       ││
│ │                              │  │                             ││
│ │  y:495 ─────────────         │  │  y:495                      ││
│ │                              │  │                             ││
│ └──────────────────────────────┘  └─────────────────────────────┘│
│                                                                 │
│ Footer: y:510-600, h:90px (position: fixed, z-index: 1000)    │
│ Seek bar: y:510, h:3px, full-width, pembe                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. ÖLÇÜLER

| Parametre | Değer | Token |
|-----------|-------|-------|
| Sol alan genişliği | 614px (%60) | — |
| Sağ alan genişliği | 394px (%40) | — |
| Gap (iki alan arası) | 16px | `--space-4` |
| Sol alan padding | 16px | `--space-4` |
| Sağ alan padding | 16px | `--space-4` |
| Header yüksekliği | **60px** | `--header-h: 60px` |
| Footer yüksekliği | **90px** | `--footer-h: 90px` |
| İçerik yüksekliği | 450px (600-60-90) | — |
| İçerik padding-top | 11px | `--content-pt: 11px` |
| İçerik padding-bottom | 15px | `--content-pb: 15px` |
| Sidebar | ❌ YOK | — |

---

## 3. CSS YAPISI

```css
/* Standard 60/40 Split Layout */
.standard-60-40 {
  display: grid;
  grid-template-columns: 614px 16px 1fr;
  /* Sol: 614px, Gap: 16px, Sağ: kalan */
  min-height: calc(100vh - 60px - 90px); /* header + footer */
  padding: 11px 16px 15px 16px;
}

.standard-60-40__left {
  /* Sol alan — Card Grid veya Track List */
  overflow-y: auto;
  scrollbar-width: thin;
}

.standard-60-40__right {
  /* Sağ alan — Detail Panel */
  overflow-y: auto;
  scrollbar-width: thin;
}
```

---

## 4. BİLEŞEN EŞLEMESİ

### 4.1 — Sol Alan (60%)

| Sayfa | İçerik | Bileşenler |
|-------|--------|------------|
| Albums | Card Grid | C09 ×12 (4×3 grid) |
| Album Detail | Track List | C13 ×7+ (tablo) |
| Artists | Card Grid | C09 ×10 (5×2, daire) |
| Playlist | Track List | C13 ×7+ (tablo) |

### 4.2 — Sağ Alan (40%)

| Sayfa | İçerik | Bileşenler |
|-------|--------|------------|
| Albums | Album Detail Panel | C10 (300×300 daire art + butonlar + metadata) |
| Album Detail | Album Detail Panel | C10 (aynı) |
| Artists | Artist Detail Panel | C10 (300×圆形 photo + bio + istatistikler) |
| Playlist | Playlist Info Panel | C10 (artist photo + aksiyonlar + öneriler) |

---

## 5. CARD GRID (Sol Alan)

```
┌────────────────────────────────────────────────┐
│ ┌───────┐┌───────┐┌───────┐┌───────┐          │
│ │140×140││140×140││140×140││140×140│          │
│ │ album ││ album ││ album ││ album │          │
│ │ thumb ││ thumb ││ thumb ││ thumb │          │
│ │───────││───────││───────││───────│          │
│ │Title  ││Title  ││Title  ││Title  │          │
│ │Artist ││Artist ││Artist ││Artist │          │
│ │00:10:05│00:10:05│00:10:05│00:10:05│          │
│ └───────┘└───────┘└───────┘└───────┘          │
│ ┌───────┐┌───────┐┌───────┐┌───────┐          │
│ │ ...   ││ ...   ││ ...   ││ ...   │          │
│ └───────┘└───────┘└───────┘└───────┘          │
│                                                │
│ Grid: 4 sütun, gap: 8px                       │
│ Kart: 140×140px thumb + text                  │
└────────────────────────────────────────────────┘
```

| Özellik | Değer |
|---------|-------|
| Grid | `grid-template-columns: repeat(4, 140px); gap: 8px;` |
| Thumb | 140×140px, border-radius: 8px |
| Başlık | 12px, 600, max 2 satır, `text-overflow: ellipsis` |
| Alt metin | 10px, 400, `rgba(255,255,255,0.6)` |
| Süre | 10px, pembe accent |
| Scroll | `overflow-x: auto` (yatay) |

---

## 6. TRACK LIST (Sol Alan — Album Detail/Playlist)

```
┌────────────────────────────────────────────────────────────┐
│ / | Şarkı Adı         | Albüm Adı    | Süre  | ★ Yıldız  │
│───│───────────────────│──────────────│───────│───────────│
│[♪]│Göksel-Sevil Neş.  │Hayat Rüya    │00:05:00│ ★★★★★    │
│[♪]│Göksel-Kabahat     │Hayat Rüya    │00:00:00│ ★★★★★    │
│[♪]│Göksel-Sevil Neş.←PEMBE          │00:00:00│ ★★★★★    │
│[♪]│Göksel-Sevil Neş.  │Hayat Rüya    │00:00:00│ ★★★★☆    │
│[♪]│Göksel-Sevil Neş.  │Hayat Rüya    │00:00:00│ ★★★★★    │
└────────────────────────────────────────────────────────────┘
```

| Özellik | Değer |
|---------|-------|
| Satır yüksekliği | min-height: 48px (WCAG) |
| Padding | 8px 12px |
| Başlık fontu | 12px, 500 |
| Süre fontu | 10px, 400 |
| Aktif satır | `background: rgba(255,79,216,0.15)` |
| Aktif border-left | 3px solid `var(--theme-primary)` |
| Hover | ❌ YOK (dokunmatik) |
| Focus-visible | `outline: 2px solid var(--theme-primary)` |

---

## 7. DETAIL PANEL (Sağ Alan)

```
┌── DETAIL PANEL (394px) ─────────────────────────┐
│                                                  │
│  ┌────────────┐                                  │
│  │ 圆形 300×300│  Album/Artist Adı               │
│  │ Album Art   │  Sanatçı Adı                    │
│  │ (r:50%)     │                                  │
│  └────────────┘                                  │
│                                                  │
│  [Hemen Çal] (C04, pembe, full-width)            │
│  [Karışık Çal] (C05, sınır) [...]                │
│                                                  │
│  ── Metadata ──                                  │
│  Kalite: 24 Bit / 48 kHz                         │
│  Boyut: 2 GB | İndirme: 2                        │
│  Parça: 12 | Tür: Arabesk                        │
│  Yıl: Bilinmeyen | Dinlenme: 5                   │
│  Süre: 00:30:00                                  │
│                                                  │
└──────────────────────────────────────────────────┘
```

| Özellik | Değer | Token |
|---------|-------|-------|
| Art boyutu | 300×300px | `--detail-art` |
| Art border-radius (album) | 12px | `--radius-lg` |
| Art border-radius (artist) | 50% (daire) | `--radius-full` |
| Başlık | 16px, 600 | `--text-lg, --font-semibold` |
| Metadata | 11px, 400 | `--text-xs` |
| Metadata rengi | `rgba(255,255,255,0.6)` | `--color-text-muted` |
| Padding | 16px | `--space-4` |

---

## 8. RESPONSIVE NOTLARI

| Platform | Değişiklik |
|----------|-----------|
| 1024px (RPi5) | Mevcut layout (bu dosya) |
| 1920px (Desktop) | Sol 660px, Sağ 440px, gap 20px |
| 3840px (4K TV) | Sol 1100px, Sağ 700px, gap 24px, font 1.6× |
| Mobile | Ayrı layout (modal detail panel) |

---

## 9. KURALLAR

| # | Kural |
|---|-------|
| 1 | Header: **60px** (1024px için) |
| 2 | Footer: **90px** (1024px için) |
| 3 | Gap: 16px sabit |
| 4 | Sidebar: ❌ YOK |
| 5 | Hover: ❌ YOK |
| 6 | Focus-visible: ✅ |
| 7 | Touch target: ≥48px |
| 8 | Glass: `backdrop-filter: blur(8px)` |

---

*Standard 60/40 Layout Pattern v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
