---
title: "Sayfa Prompt — Ana Sayfa"
category: page-prompt
version: "2.0.0"
date: "2026-08-11"
route: "/"
layout: "split-home-42-58"
platform: "home-1024 (Linux Embedded RPi5, 1024×600)"
references:
  - [[screens/B-home/dashboard]]
  - [[screens/00-ascii-art-views]] §1
  - [[screens/_layout-patterns/02-split-home]]
  - [[01-component-inventory]] C01, C02, C03, C09, C14
  - [[01-1024-embedded]]
---

# Ana Sayfa (Home Page) — Detaylı Prompt

## Route: `/`
## Layout Pattern: Split Home (42/58)
## Platform: home-1024 (1024×600, RPi5 7" Touch)

---

## 1. GENEL LAYOUT

```
┌─────────────────────────────────────────────────────────────────┐
│ 1024×600 — home.coremusic.net — Pattern 2: Split Home          │
├─────────────────────────────────────────────────────────────────┤
│ Header: y:0-60, h:60px (position: sticky, z-index: 1000)      │
│ Content: y:60-510, h:450px (scrollable)                        │
│   Content panel: y:71-495 (üstte 11px, altta 15px padding)     │
│ Footer: y:510-600, h:90px (position: fixed, z-index: 1000)    │
│ Seek bar: y:510, h:3px, full-width, pembe                      │
└─────────────────────────────────────────────────────────────────┘
```

| Bölge | Yükseklik | Pozisyon | z-index |
|-------|-----------|----------|---------|
| Header | 60px | `sticky; top: 0` | 1000 |
| İçerik | 450px | `overflow-y: auto` | — |
| Footer | 90px | `fixed; bottom: 0` | 1000 |

---

## 2. SPLIT LAYOUT

```
┌── SOL ALAN (%42 = ~430px) ──┐  ┌── SAĞ ALAN (%58 = ~578px) ──┐
│                              │  │                               │
│  Now Playing Card            │  │  Widget Area (4× glass panel) │
│  ┌──────────────────────┐    │  │  ┌─────────────────────────┐  │
│  │ Album Art 100×100    │    │  │  │ 🎵 Hoparlörler          │  │
│  │ Göksel - Sevil Neş.  │    │  │  │ glass panel ~250×100    │  │
│  │ Hayat Rüya Gibi      │    │  │  └─────────────────────────┘  │
│  │ Göksel               │    │  │  ┌─────────────────────────┐  │
│  │ 00:05:00 ═══ 00:05:00│    │  │  │ ☁ Hava Durumu           │  │
│  └──────────────────────┘    │  │  │ glass panel ~250×100    │  │
│                              │  │  └─────────────────────────┘  │
│  "En Son Dinlenen"           │  │  ┌─────────────────────────┐  │
│  ┌────┐┌────┐┌────┐┌────┐   │  │  │ 📅 Takvim                │  │
│  │C09 ││C09 ││C09 ││C09 │   │  │  │ glass panel ~250×100    │  │
│  └────┘└────┘└────┘└────┘   │  │  └─────────────────────────┘  │
│                              │  │  ┌─────────────────────────┐  │
│  "Oynatma Listeleri"         │  │  │ 📂 Klasörlerim           │  │
│  ┌────┐┌────┐┌────┐┌────┐   │  │  │ glass panel ~250×100    │  │
│  │C09 ││C09 ││C09 ││C09 │   │  │  └─────────────────────────┘  │
│  └────┘└────┘└────┘└────┘   │  │                               │
│                              │  │  Mini Card (alt kısım)        │
│  "Sıradaki Şarkılar"         │  │  ┌─────────────────────────┐  │
│  ┌─ Mini Card ──────────┐   │  │  │ [50×50] Göksel           │  │
│  │ [50×50] Artist Info  │   │  │  │ Sevil Neşelen             │  │
│  └──────────────────────┘   │  │  └─────────────────────────┘  │
│                              │  │                               │
└──────────────────────────────┘  └───────────────────────────────┘
```

---

## 3. HEADER YAPISI (h:60px)

```html
<header class="site-header">
  <div class="site-header__inner">
    <a href="/" class="site-header__logo">Core Music</a>
    <nav class="site-header__nav" aria-label="Ana navigasyon">
      <a href="/home" class="nav-link active" aria-current="page">Ana Sayfa</a>
      <a href="/kesfet" class="nav-link">Keşfet</a>
      <a href="/albums" class="nav-link">Albümler</a>
      <a href="/artists" class="nav-link">Sanatçılar</a>
      <a href="/browse" class="nav-link">Göz At</a>
      <a href="/history" class="nav-link">Geçmiş</a>
      <a href="/settings" class="nav-link">Ayarlar</a>
      <a href="/about" class="nav-link">Hakkımızda</a>
    </nav>
    <div class="site-header__actions">
      <div class="header-border">
        <div class="header-widget header-widget--signal">📶</div>
        <div class="header-widget header-widget--bt">✳</div>
      </div>
      <div class="header-border">🔋 %100</div>
      <div class="header-user">[avatar] Bayram Ali ▾</div>
      <button class="header-icon" aria-label="Ayarlar">⚙</button>
      <button class="header-icon" aria-label="Kapat">⏻</button>
    </div>
  </div>
</header>
```

| Bileşen | Boyut | Token |
|---------|-------|-------|
| Logo | ~120×30px | `--font-logo: Bickham Script Two` |
| Nav Link ×8 | ~24×24px (WCAG: 48px) | `--text-xs: 10px, Arima` |
| User Pill | ~150×37px | `--glass-bg, --radius-pill` |
| WiFi+BT Pill | 65×37.4px | `--glass-bg, --radius-pill` |
| Battery Pill | 100×37.4px | `--glass-bg, --radius-pill` |

---

## 4. NOW PLAYING CARD

```
┌──────────────────────────────────────────────┐
│  ┌────────┐ Göksel - Sevil Neşelen           │
│  │100×100 │ Hayat Rüya Gibi                  │
│  │album   │ Göksel                           │
│  │art     │                                  │
│  └────────┘ 00:05:00 ═══════════ 00:05:00   │
│                ▲ pembe seek bar h:3px         │
└──────────────────────────────────────────────┘
```

| Özellik | Değer | Token |
|---------|-------|-------|
| Album art | 100×100px, border-radius: 8px | `--card-thumb` |
| Başlık | 14px, 600 | `--text-base, --font-semibold` |
| Alt başlık | 12px, 400 | `--text-sm` |
| Sanatçı | 11px, 400 | `--text-xs` |
| Seek bar | h:3px, full-width, pembe | `--theme-primary` |
| Süre | 10px, `rgba(255,255,255,0.6)` | `--text-xs, --color-text-muted` |

---

## 5. KART GRID

### 5.1 — En Son Dinlenen (4 kart)

```
┌──────┐┌──────┐┌──────┐┌──────┐
│140×  ││140×  ││140×  ││140×  │
│140   ││140   ││140   ││140   │
│C09   ││C09   ││C09   ││C09   │
│kart  ││kart  ││kart  ││kart  │
└──────┘└──────┘└──────┘└──────┘
```

### 5.2 — Oynatma Listeleri (4+ kart)

```
┌──────┐┌──────┐┌──────┐┌──────┐┌──────┐
│140×  ││140×  ││140×  ││140×  ││140×  │
│140   ││140   ││140   ││140   ││140   │
│kart  ││kart  ││kart  ││kart  ││kart  │
└──────┘└──────┘└──────┘└──────┘└──────┘
☐ "Oynatma listesini göster"
```

| Özellik | Değer |
|---------|-------|
| Grid | `display: grid; grid-template-columns: repeat(4, 140px); gap: 8px;` |
| Kart boyutu | 140×140px (thumb) + text altı |
| Thumb border-radius | 8px |
| Başlık | 12px, 600, max 2 satır |
| Süre | 10px, pembe accent |
| Container | `overflow-x: auto` (yatay scroll) |

---

## 6. WIDGET ALANI (4 Glass Panel)

```
┌──────────────────────────────────────┐
│ 🎵 Hoparlörler                       │
│    Core Music - Hoparlör              │
│    ┌──────────────────────────────┐  │
│    │ glass panel ~250×100          │  │
│    │ backdrop-filter: blur(8px)    │  │
│    └──────────────────────────────┘  │
│                                      │
│ ☁ Hava Durumu                        │
│    İzmir, TR                         │
│    ┌──────────────────────────────┐  │
│    │ glass panel ~250×100          │  │
│    └──────────────────────────────┘  │
│                                      │
│ 📅 07:00 — 5 Ağustos 2026           │
│    ┌──────────────────────────────┐  │
│    │ glass panel ~250×100          │  │
│    └──────────────────────────────┘  │
│                                      │
│ 📂 Klasörlerim                       │
│    [▶][YT][♥][♫][♥]                  │
│    ┌──────────────────────────────┐  │
│    │ glass panel ~250×100          │  │
│    └──────────────────────────────┘  │
└──────────────────────────────────────┘
```

| Özellik | Değer | Token |
|---------|-------|-------|
| Panel boyutu | ~250×100px | — |
| Background | `rgba(255,255,255,0.08)` | `--glass-bg-subtle` |
| Backdrop-filter | `blur(8px)` | `--blur-md` |
| Border | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Border-radius | 12px | `--radius-lg` |
| Padding | 12px | `--space-3` |
| Başlık | 12px, 600 | `--text-sm, --font-semibold` |
| İçerik | 11px, 400 | `--text-xs` |

---

## 7. FOOTER PLAYER (h:90px)

```
┌─────────────────────────────────────────────────────────────────┐
│ y:510├─────────────────────────────────────────────────────────┤ │
│     │▲ pembe ilerleme çubuğu h:3px, full-width, y=0            │ │
│     │ y:513┌────────┐ ♪ Şarkı Adı  : Göksel - Sevil Neşelen   │ │
│     │       │120×120 │ ● Albümüm   : Hayat Rüya Gibi           │ │
│     │       │album   │ 🎤 Sanatçı  : Göksel                   │ │
│     │       │art     │                                         │ │
│     │ y:550└────────┘    [⏮]  [▶]  [⏹]  [⏭]                 │ │
│     │                       ◯    ◉    ◯    ◯    Süre: 00:05:00│ │
│     │ y:566               (33px)    Bit rate: 320 kbps         │ │
│     │                              [🔊 ═══▲═══ ] % 100         │ │
│ y:600└─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

| Bileşen | Boyut | Touch Target |
|---------|-------|-------------|
| Album Art | 120×120px | ✅ |
| Şarkı bilgisi | ~300×60px | N/A |
| Prev/Play/Stop/Next | 33px çap daire | ✅ (pad ile 48px) |
| Seek bar | Full-width × 3px | ✅ (hit area genişlet) |
| Volume slider | 145px × 15px | ✅ |
| Volume ikon | 20×20px | ✅ (pad ile 44px) |

---

## 8. CSS DOSYA YAPISI

```
assets.coremusic.net/Css/
├── 01_Abstracts/
│   ├── a-layout-tokens.css        ← Footer: 90px, Header: 60px
│   ├── a-design-tokens.css        ← --theme-primary: #ff4fd8
│   └── a-fonts-token.css          ← Arima + Bickham Script Two
├── 03_Layout/
│   ├── _header.css                ← .site-header BEM
│   └── _footer.css                ← .footer-player
├── 04_Components/
│   ├── c-media-card.css           ← C09 kartlar
│   └── c-widget.css               ← Glass widget paneller
├── 05_Pages/
│   ├── _home-layout.css           ← Split layout (42/58)
│   ├── _home-components.css       ← Now Playing, Mini Card
│   └── _home.css                  ← Ana sayfa stilleri
└── 08_Devices/
    └── d-embedded.css             ← RPi5 overrides
```

---

## 9. ERİŞİLEBİLİRLİK

| Öğe | Durum | Not |
|-----|-------|-----|
| Header nav links | ⚠️ 24px | 48px'e çıkarılmalı |
| Widget paneller | ✅ | Büyük, dokunulabilir |
| Transport daireleri | ✅ 33px | Pad ile 48px |
| Volume slider | ✅ | Dokunulabilir |
| Seek bar | ⚠️ 3px | Hit area genişletilmeli |
| Album art | ✅ | Büyük |
| Mini card | ✅ | Tıklanabilir |

---

## 10. KURALLAR

| # | Kural |
|---|-------|
| 1 | Sidebar YOK — sadece Göz At sayfasında |
| 2 | Split layout: Sol %42 / Sağ %58 |
| 3 | Glass paneller: `backdrop-filter: blur(8px)` |
| 4 | Footer: **90px** (PNG ölçümü, CSS'teki 104px değil) |
| 5 | Header: **60px** (PNG ölçümü) |
| 6 | İçerik: y:71-495 (11px üst, 15px alt padding) |
| 7 | Tema: `var(--theme-primary)` ile değiştirme |
| 8 | **Hover YOK** — sadece focus-visible |
| 9 | `innerHTML` YASAK — DOMParser + TrustedTypes |
| 10 | Framework YASAK — Vanilla JS (ADR-001) |

---

*Home Page Prompt v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
