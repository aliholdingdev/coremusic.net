---
title: "1024×600 Embedded (RPi5 7” Touch)"
category: screen-prompt
platform: linux-embedded
resolution: "1024x600"
device: "Raspberry Pi 5 — 7” dokunmatik LCD"
os: "Debian Bookworm / Linux ARM64"
input: "Dokunmatik (kapasitif, 5 nokta)"
version: 1.0.0
date: 2026-08-11
status: active
authority: Single Source of Truth (SSOT)
---

# Prompt: 1024×600 Embedded Screen

Bu prompt, CoreMusic UI Designer agent'ı tarafından **1024×600 çözünürlükteki 7" dokunmatik ekranlar** (Raspberry Pi 5) için CSS/HTML üretimi yaparken kullanılır.

---

## 1. Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Çözünürlük | 1024×600 |
| Cihaz | Raspberry Pi 5 + 7" dokunmatik LCD |
| İşletim Sistemi | Debian Bookworm / Linux ARM64 |
| Giriş Yöntemi | Kapasitif dokunmatik (5 nokta) |
| Tarayıcı | Chromium (kiosk mode) |
| Piksel Yoğunluğu | 1× (163 PPI) |
| Safe Area | 0px her kenarda |

---

## 2. Layout Kuralları

### 2.1 Header

| Özellik | Değer |
|---------|-------|
| Yükseklik | 60px sabit |
| Pozisyon | `position: sticky; top: 0` |
| z-index | 1000 |
| İçerik | Logo (32px), sayfa başlığı, quick-actions |
| Padding | 0 16px |

### 2.2 Footer (Now Playing Bar)

| Özellik | Değer |
|---------|-------|
| Yükseklik | 90px sabit |
| Pozisyon | `position: fixed; bottom: 0` |
| z-index | 1000 |
| İçerik | Playback controls, progress bar, volume |
| Padding | 0 16px |
| Border-top | `1px solid var(--border-subtle)` |

### 2.3 Ana İçerik Alanı

```css
.main-content {
  padding-top: 60px;   /* Header yüksekliği */
  padding-bottom: 90px; /* Footer yüksekliği */
  min-height: 100dvh;
}
```

### 2.4 Sidebar (Sadece Göz At Sayfası)

| Özellik | Değer |
|---------|-------|
| Genişlik | 167px sabit |
| Sayfa | Sadece `browse.html` / `/browse` |
| Pozisyon | `position: fixed; left: 0; top: 60px; bottom: 90px` |
| Gorunurluk | Diğer sayfalarda `display: none` |
| İçerik | Kategori listesi, filtreler |
| Padding | 12px |
| Scroll | `overflow-y: auto; scrollbar-width: thin` |

### 2.5 Grid Sistemi

| Özellik | Değer |
|---------|-------|
| Max Sütun | 3 |
| Min Sütun Genişliği | 280px |
| Gap | 12px |
| Container | `max-width: 100%; padding: 0 16px` |
| Grid Tanımı | `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))` |

### 2.6 Sayfa Variasyonları

| Sayfa | Grid | Sidebar | Notlar |
|-------|------|---------|--------|
| Home | 2 sütun | Yok | Karşılama + öneriler |
| Browse | 3 sütun | 167px | Kategori filtreleme |
| Player | Tam genişlik | Yok | Cover art + kontroller |
| Settings | Tam genişlik | Yok | Liste görünümü |

---

## 3. Touch Target Kuralları

| Özellik | Değer |
|---------|-------|
| Minimum Boyut | ≥48×48px |
| Recomended | 56×56px |
| Spacing | ≥8px boşluk |
| Active State | `transform: scale(0.97)` — 100ms |
| Focus Ring | `2px solid var(--accent)` + `2px offset` |

```css
.touch-target {
  min-width: 48px;
  min-height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}

.touch-target:active {
  transform: scale(0.97);
  transition: transform 100ms ease;
}
```

**Dikkat:** `touch-action: manipulation` ile pinch-to-zoom devre dışı bırakılır, sadece scroll ve tap izin verilir.

---

## 4. Font Ölçeklendirme

| Özellik | Değer |
|---------|-------|
| Ölçek | 1× (varsayılan) |
| Body Font | `Arima, sans-serif` |
| Display Font | `'Bickham Script Two', cursive` |
| Base Size | 16px |
| Min Font Size | 14px |
| Max Font Size | 24px (heading'ler) |
| Line Height | 1.5 (body), 1.2 (heading) |

### 4.1 Font Hiyerarşisi

| Token | Boyut | Weight | Kullanım |
|-------|-------|--------|----------|
| `--font-size-xs` | 12px | 400 | Caption, timestamp |
| `--font-size-sm` | 14px | 400 | Body small, labels |
| `--font-size-base` | 16px | 400 | Body text |
| `--font-size-lg` | 18px | 600 | Subheadings |
| `--font-size-xl` | 20px | 700 | Section titles |
| `--font-size-2xl` | 24px | 700 | Page titles |

### 4.2 Font CSS

```css
:root {
  --font-family-body: 'Arima', sans-serif;
  --font-family-display: 'Bickham Script Two', cursive;
  --font-size-base: 16px;
  --font-scale: 1;
}

body {
  font-family: var(--font-family-body);
  font-size: calc(var(--font-size-base) * var(--font-scale));
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
}
```

---

## 5. Token Overrides

### 5.1 Renk Tokenları

```css
:root {
  /* Background */
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-elevated: #1a1a24;
  --bg-glass: rgba(18, 18, 26, 0.72);

  /* Border */
  --border-subtle: rgba(255, 255, 255, 0.08);
  --border-default: rgba(255, 255, 255, 0.12);
  --border-strong: rgba(255, 255, 255, 0.20);

  /* Text */
  --text-primary: #f0f0f5;
  --text-secondary: #a0a0b0;
  --text-muted: #606070;

  /* Accent — ADR-044 tema motoru ile değiştirilir */
  --theme-primary: #ff4fd8;        /* female (pembe) — varsayılan */
  --theme-primary-hover: #e645c0;  /* %10 daha koyu */
  --theme-primary-rgb: 255, 79, 216;
  --theme-primary-glow: rgba(255, 79, 216, 0.25);

  /* Status */
  --success: #4caf50;
  --warning: #ff9800;
  --error: #f44336;

  /* Spacing */
  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 12px;
  --space-lg: 16px;
  --space-xl: 24px;
  --space-2xl: 32px;

  /* Border Radius */
  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-full: 9999px;

  /* Shadow */
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.5);

  /* Z-Index */
  --z-header: 1000;
  --z-footer: 1000;
  --z-sidebar: 900;
  --z-modal: 1100;
  --z-tooltip: 1200;

  /* Header/Footer */
  --header-height: 60px;
  --footer-height: 90px;
  --sidebar-width: 167px;

  /* Grid */
  --grid-max-columns: 3;
  --grid-min-width: 280px;
  --grid-gap: 12px;

  /* Touch */
  --touch-min-size: 48px;
  --touch-target-size: 56px;
  --touch-spacing: 8px;
}
```

### 5.2 Glass Efekt Tokenları

```css
.glass {
  background: var(--bg-glass);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
}
```

---

## 6. Hover Davranışı

**⚠️ Hover YOKTUR.** Bu platform sadece dokunmatik giriş destekler.

```css
/* Hover state'leri devre dışı */
*:hover {
  /* Boş — hover efekti uygulanmaz */
}

/* Sadece focus-visible */
*:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
}

/* Focus ring animasyonu */
*:focus-visible {
  animation: focus-pulse 1.5s ease-in-out infinite;
}

@keyframes focus-pulse {
  0%, 100% {   outline-color: var(--theme-primary); }
  50% { outline-color: var(--theme-primary-hover); }
}
```

**Kural:** `@media (hover: hover)` Blokları kullanarak hover stillerini masaüstü ile paylaşmayın. Bu dosyada hover tamamen yok.

---

## 7. CSS Üretim Talimatları

### 7.1 Agent Talimatı

```
Bu prompt'u kullanarak 1024×600 embedded ekran için CSS üret.
Tüm stiller bu dosyadaki token değerlerini kullanmalı.
Responsive breakpoint kullanma — bu tek boyutlu platform.
```

### 7.2 Zorunlu CSS Yapıları

1. **Reset:** `*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }`
2. **Body:** `overflow-x: hidden; background: var(--bg-primary); color: var(--text-primary);`
3. **Scroll Behavior:** `html { scroll-behavior: smooth; }`
4. **Selection:** `::selection { background: var(--accent); color: white; }`
5. **Scrollbar:** `scrollbar-width: thin; scrollbar-color: var(--border-default) transparent;`

### 7.3 Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `hover:` medya sorgusu | Sadece `focus-visible` |
| `vw/vh` birimleri | `px` veya `dvh` |
| `position: fixed` (sidebar dışı) | `sticky` veya `absolute` |
| `z-index > 2000` | Max `1200` |
| `font-size < 12px` | Min `12px` |
| `touch-action: none` | `touch-action: manipulation` |

### 7.4 Çıktı Formatı

CSS çıktısı şu sırayla olmalı:
1. Custom properties (`:root`)
2. Reset
3. Typography
4. Layout (header, footer, sidebar, main)
5. Components (cards, buttons, inputs)
6. Glass effects
7. States (focus-visible)
8. Animations
9. Utilities

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS katmanları |
| [[ADR-044-dynamic-user-theme-engine]] | Tema motoru, cinsiyet bazlı |
| [[ADR-045-multi-domain-view-mode-architecture]] | View mode mimarisi |
| [[ADR-048-view-transition-api-integration]] | View Transition API |

---

## 9. Checkpoint

CSS üretilmeden önce bu kontrol listesi doğrulanmalı:

- [ ] Header yüksekliği 60px
- [ ] Footer yüksekliği 90px
- [ ] Sidebar genişliği 167px (sadece browse)
- [ ] Touch target ≥48×48px
- [ ] Font scale 1×
- [ ] Glass blur(20px) saturate(180%)
- [ ] Grid max 3 sütun
- [ ] Hover yok, sadece focus-visible
- [ ] Tüm token değerleri bu dosyadan
- [ ] Responsive breakpoint yok (tek boyut)

---

*Screen Prompt v1.0.0 — CoreMusic Embedded Platform*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
