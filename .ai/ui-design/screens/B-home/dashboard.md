---
title: CoreMusic — Home Page Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 3.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Home Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/02-split-home]]
  - [[decisions/accepted/ADR-001-vanilla-js-itcss]]
  - [[decisions/accepted/ADR-044-dynamic-user-theme-engine]]
---

# CoreMusic — Home Page Screen Specification (v3.0.0)

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Home Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 2: Split Home (42/58 split + grid)

---

## 1. PLATFORM

| Property | Value |
|----------|-------|
| Resolution | 1024×600px |
| Platform | Linux Embedded (Raspberry Pi 5) |
| Orientation | Landscape (fixed) |
| Scale Factor | 1x |
| Device Type | `device_type = 'embedded'` |
| CSS Bundle | `d-embedded.css` (ITCSS 08_Devices) |
| Viewport Height | 600px (minus header 60px + footer 90px = 450px content) |
| Accent Color | `#ff4fd8` (tema motoru ile değiştirilebilir) |
| Background | Tam kaplama kadın fotoğrafı, sunset tonları, pembe/mor |
| Rota | `/` (ana sayfa) |
| Hover | YOK (dokunmatik cihaz) |
| Girdi | Parmak (fare yok, klavye yok) |

---

## 2. GENEL LAYOUT ÖLÇÜLERİ

```
┌─────────────────────────────────────────────────────────────┐
│ 1024×600 — Linux Embedded RPi5 — home.coremusic.net        │
├─────────────────────────────────────────────────────────────┤
│ Header: y:0-60, h:60px (fixed)                             │
│ Content: y:60-510, h:450px (scrollable)                    │
│   Content panel: y:71-495 (üstte 11px, altta 15px padding) │
│ Footer: y:510-600, h:90px (fixed)                          │
│ Seek bar: y:510, h:3px, full-width, pembe                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. HEADER DETAYI (y:0-60)

### 3.1 — Yapı

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─────────────────────────────────────────────────────────────────────────────────┐ │
│     │"Core Music"  Ana Sayfa  Keşfet  Albümler  Sanatçılar  Göz At  Geçmiş  Ayarlar │ │
│     │ y:15    (Bickham)  (nav-link × 8, gap:2-4px, Arima 10px)      [Bayram Ali ▾]  │ │
│     │                                                        [📶✳ pill 65×37] [🔋100] │ │
│     │ y:35                                                             [⚙] [⏻]       │ │
│ y:60├─────────────────────────────────────────────────────────────────────────────────┤ │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

### 3.2 — Bileşenler

| # | Bileşen | Konum | Boyut | Token |
|---|---------|-------|-------|-------|
| 1 | Logo ("Core Music") | x:16, y:15 | ~120×30px | `--font-logo: Bickham Script Two` |
| 2 | Nav Link × 8 | x:150-600, y:15 | ~24×24px her biri (WCAG: 48px) | `--text-xs: 10px, Arima` |
| 3 | User Pill (Bayram Ali ▾) | x:800, y:15 | ~150×37px | `--glass-bg, --radius-pill` |
| 4 | WiFi+BT Pill | x:870, y:15 | 65×37.4px | `--glass-bg, --radius-pill` |
| 5 | Battery Pill | x:940, y:15 | 100×37.4px | `--glass-bg, --radius-pill` |
| 6 | Settings Icon (⚙) | x:980, y:35 | 25×25px | — |
| 7 | Power Icon (⏻) | x:1005, y:35 | 25×25px | — |

### 3.3 — Nav Link Detayları

| Nav Link | Aktif mi? | Renk |
|----------|-----------|------|
| Ana Sayfa | ✅ EVET | `#E91E8C` (pembe) |
| Keşfet | ❌ | `rgba(255,255,255,0.85)` |
| Albümler | ❌ | `rgba(255,255,255,0.85)` |
| Sanatçılar | ❌ | `rgba(255,255,255,0.85)` |
| Göz At | ❌ | `rgba(255,255,255,0.85)` |
| Geçmiş | ❌ | `rgba(255,255,255,0.85)` |
| Ayarlar | ❌ | `rgba(255,255,255,0.85)` |
| Hakkımızda | ❌ | `rgba(255,255,255,0.85)` |

### 3.4 — Header BEM Yapısı

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

---

## 4. CONTENT AREA — SOL BÖLGE (x:16-420, ~42%)

### 4.1 — Now Playing Card (y:71-200)

```
┌──────────────────────────────────────────────┐
│  ┌────────┐ Göksel - Sevil Neşelen           │
│  │100×100 │ Hayat Rüya Gibi                  │
│  │album   │ Göksel                           │
│  │art     │                                  │
│  └────────┘ 00:05:00 ═══════════ 00:05:00   │
│                ▲ pembe seek bar h:3px         │
│                                              │
│  "En Son Dinlenen" başlığı                   │
│  ┌──────┐┌──────┐┌──────┐┌──────┐           │
│  │140×  ││140×  ││140×  ││140×  │           │
│  │140   ││140   ││140   ││140   │           │
│  │C09   ││C09   ││C09   ││C09   │           │
│  │kart  ││kart  ││kart  ││kart  │           │
│  └──────┘└──────┘└──────┘└──────┘           │
│                                              │
│  "Oynatma Listeleri" başlığı                 │
│  ┌──────┐┌──────┐┌──────┐┌──────┐┌──────┐  │
│  │140×  ││140×  ││140×  ││140×  ││140×  │  │
│  │140   ││140   ││140   ││140   ││140   │  │
│  └──────┘└──────┘└──────┘└──────┘└──────┘  │
│  ☐ "Oynatma listesini göster"                │
│                                              │
│  "Sıradaki Şarkılar"                         │
│                                              │
│  ┌─ Mini Card ──────────────────────┐        │
│  │ [50×50] Göksel                    │        │
│  │          Sevil Neşelen             │        │
│  │          Hayat Rüya Gibi           │        │
│  └───────────────────────────────────┘        │
└──────────────────────────────────────────────┘
```

### 4.2 — Now Playing Card Detayı

| Özellik | Değer | Token |
|---------|-------|-------|
| Album art | 100×100px, border-radius: 8px | `--card-thumb` |
| Başlık fontu | 14px, 600 | `--text-base, --font-semibold` |
| Alt başlık fontu | 12px, 400 | `--text-sm, --font-normal` |
| Sanatçı fontu | 11px, 400 | `--text-xs` |
| Seek bar | h:3px, pembe, full-width | `--theme-primary` |
| Süre text | 10px, `rgba(255,255,255,0.6)` | `--text-xs, --color-text-muted` |

### 4.3 — Kart Grid (En Son Dinlenen)

| Özellik | Değer |
|---------|-------|
| Grid | 4 sütun, gap: 8px |
| Kart boyutu | ~140×140px (thumb) + text altı |
| Thumb border-radius | 8px (kare) |
| Başlık | 12px, 600, max 2 satır |
| Süre | 10px, pembe accent |
| Container | `overflow-x: auto` (yatay scroll) |

### 4.4 — Oynatma Listeleri Grid

| Özellik | Değer |
|---------|-------|
| Grid | 5 sütun (1 sola kaydırılmış) |
| Kart boyutu | Aynı (140×140px) |
| Checkbox | "Oynatma listesini göster" — toggle |

---

## 5. CONTENT AREA — SAĞ BÖLGE (x:420-1008, ~58%)

### 5.1 — Widget Area (4 Glass Panel)

```
┌──────────────────────────────────────────────┐
│ 🎵 Hoparlörler                               │
│    Core Music - Hoparlör                      │
│    ┌──────────────────────────────────────┐  │
│    │ glass panel ~250×100                  │  │
│    │ (backdrop-filter: blur(8px))          │  │
│    └──────────────────────────────────────┘  │
│                                              │
│ ☁ Hava Durumu                                │
│    İzmir, TR                                 │
│    ┌──────────────────────────────────────┐  │
│    │ glass panel ~250×100                  │  │
│    └──────────────────────────────────────┘  │
│                                              │
│ 📅 07:00 — 5 Ağustos 2026                   │
│    ┌──────────────────────────────────────┐  │
│    │ glass panel ~250×100                  │  │
│    └──────────────────────────────────────┘  │
│                                              │
│ 📂 Klasörlerim                               │
│    [▶][YT][♥][♫][♥]                          │
│    ┌──────────────────────────────────────┐  │
│    │ glass panel ~250×100                  │  │
│    └──────────────────────────────────────┘  │
└──────────────────────────────────────────────┘
```

### 5.2 — Widget Glass Panel Özellikleri

| Özellik | Değer | Token |
|---------|-------|-------|
| Panel boyutu | ~250×100px | — |
| Background | `rgba(255,255,255,0.08)` | `--glass-bg-subtle` |
| Backdrop-filter | `blur(8px)` | `--blur-md` |
| Border | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Border-radius | 12px | `--radius-lg` |
| Padding | 12px | `--space-3` |
| Başlık fontu | 12px, 600 | `--text-sm, --font-semibold` |
| İçerik fontu | 11px, 400 | `--text-xs` |

---

## 6. FOOTER PLAYER (y:510-600)

### 6.1 — Yapı

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ y:510├─────────────────────────────────────────────────────────────────────────────────┤ │
│     │▲ pembe ilerleme çubuğu h:3px, full-width, y=0                                    │ │
│     │ y:513┌────────┐ ♪ Şarkı Adı  : Göksel - Sevil Neşelen                           │ │
│     │       │120×120 │ ● Albümüm   : Hayat Rüya Gibi                                   │ │
│     │       │album   │ 🎤 Sanatçı  : Göksel                                           │ │
│     │       │art     │                                                                 │ │
│     │ y:550└────────┘    [⏮]  [▶]  [⏹]  [⏭]     Süre: 09:00:00 / 00:05:00           │ │
│     │                       ◯    ◉    ◯    ◯      Bit rate : 320 kbps                 │ │
│     │ y:566                   (33px çap daireler)    [🔊 ═══▲═══ ] % 100               │ │
│ y:600└─────────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

### 6.2 — Footer Bileşenleri

| # | Bileşen | Konum | Boyut | Token |
|---|---------|-------|-------|-------|
| 1 | Seek bar | y:510, full-width | h:3px | `--theme-primary` |
| 2 | Album art | x:16, y:513 | 120×120px | `--card-thumb` |
| 3 | Şarkı bilgisi | x:146, y:513 | — | `--text-xs` |
| 4 | Transport (⏮▶⏹⏭) | x:450, y:550 | 33px çap daireler | `--touch-recommended` |
| 5 | Süre | x:650, y:550 | — | `--text-xs` |
| 6 | Bit rate | x:650, y:566 | — | `--text-xs` |
| 7 | Volume | x:850, y:550 | 145px | `--theme-primary` |
| 8 | Volume % | x:1000, y:550 | — | `--text-xs` |

### 6.3 — Transport Daireleri

| Özellik | Değer | Token |
|---------|-------|-------|
| Normal daire | 33px çap | `--transport-size` |
| Play daire | 38px çap | `--transport-size-lg` |
| Background | `rgba(255,255,255,0.15)` | `--glass-bg` |
| Border | none | — |
| Icon | Beyaz, 16px | `--color-white` |
| Active | pembe border | `--theme-primary` |

---

## 7. ARKA PLAN

| Özellik | Değer |
|---------|-------|
| Görsel | Tam kaplama kadın fotoğrafı |
| Pozisyon | `background-size: cover; background-position: center` |
| Efekt | `backdrop-filter: blur(20px) saturate(180%)` (glass panellerde) |
| Overlay | `rgba(0,0,0,0.1)` (hafif karartma) |

---

## 8. CSS DOSYA YAPISI

```
assets.coremusic.net/Css/
├── 01_Abstracts/
│   ├── a-layout-tokens.css        ← Footer height güncelle (90px)
│   ├── a-design-tokens.css        ← Tema token'ları
│   └── a-fonts-token.css          ← Arima + Bickham Script Two
├── 03_Layout/
│   ├── _header.css                ← Header BEM
│   └── _footer.css                ← Footer player
├── 04_Components/
│   ├── c-modal.css                ← WiFi, BT, Welcome modal
│   ├── c-media-card.css           ← C09 kartlar
│   └── c-widget.css               ← Glass widget paneller
├── 05_Pages/
│   ├── _home-layout.css           ← Split layout (42/58)
│   ├── _home-components.css       ← Now Playing, Mini Card
│   └── _home.css                  ← Ana sayfa stilleri
└── 08_Devices/
    └── d-embedded.css             ← RPi5-specific overrides
```

---

## 9. ERİŞİLEBİLİRLİK (WCAG 2.2 AA)

| Öğe | Durum | Not |
|-----|-------|-----|
| Header nav links | ⚠️ 24px touch | 48px'e çıkarılmalı |
| Widget paneller | ✅ | Büyük, dokunulabilir |
| Transport daireleri | ✅ 33px | RPi5 için yeterli |
| Volume slider | ✅ | Dokunulabilir |
| Seek bar | ⚠️ 3px | Hit area genişletilmeli |
| Album art | ✅ | Büyük |
| Mini card | ✅ | Tıklanabilir |

---

## 10. TEMEL KURALLAR

| # | Kural |
|---|-------|
| 1 | Sidebar YOK — global sidebar token'ı kullanılmaz |
| 2 | Split layout: Sol %42 / Sağ %58 |
| 3 | Glass paneller: `backdrop-filter: blur(8px)` |
| 4 | Footer: 90px yükseklik (PNG ölçümü) |
| 5 | Header: 60px yükseklik (PNG ölçümü) |
| 6 | İçerik: y:71-495 (11px üst, 15px alt padding) |
| 7 | Tema: `var(--theme-primary)` ile değiştirme |
| 8 | Hover YOK — focus-visible kullan |

---

*Home Page Screen Spec v3.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
