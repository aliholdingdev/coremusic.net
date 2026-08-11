---
title: "1920×1080 Desktop (Windows/Linux)"
category: screen-prompt
platform: desktop
resolution: "1920x1080"
device: "Masaüstü / Laptop"
os: "Windows 10-11, Linux (Ubuntu, Fedora, Arch)"
input: "Fare + Klavye"
version: 1.0.0
date: 2026-08-11
status: active
authority: Single Source of Truth (SSOT)
---

# Prompt: 1920×1080 Desktop Screen

Bu prompt, CoreMusic UI Designer agent'ı tarafından **1920×1080 çözünürlükteki masaüstü/laptop ekranları** için CSS/HTML üretimi yaparken kullanılır.

---

## 1. Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Çözünürlük | 1920×1080 (Full HD) |
| Cihaz | Masaüstü bilgisayar, laptop |
| İşletim Sistemi | Windows 10-11, Linux (Ubuntu, Fedora, Arch) |
| Giriş Yöntemi | Fare + Klavye |
| Tarayıcı | Chrome, Firefox, Edge, Brave |
| Piksel Yoğunluğu | 1× (varsayılan), 1.25×-2× (Retina/HiDPI) |
| Safe Area | 0px her kenarda |

---

## 2. Layout Kuralları

### 2.1 Header

| Özellik | Değer |
|---------|-------|
| Yükseklik | 70px sabit |
| Pozisyon | `position: sticky; top: 0` |
| z-index | 1000 |
| İçerik | Logo (40px), ana navigasyon, arama, profil |
| Padding | 0 24px |
| Max İçerik | `max-width: 1440px; margin: 0 auto` |

### 2.2 Footer (Now Playing Bar)

| Özellik | Değer |
|---------|-------|
| Yükseklik | 104px sabit |
| Pozisyon | `position: fixed; bottom: 0` |
| z-index | 1000 |
| İçerik | Geniş playback kontrolleri, progress bar, volume, queue |
| Padding | 0 24px |
| Border-top | `1px solid var(--border-subtle)` |
| Background | Glass efekti ile |

### 2.3 Ana İçerik Alanı

```css
.main-content {
  padding-top: 70px;   /* Header yüksekliği */
  padding-bottom: 104px; /* Footer yüksekliği */
  min-height: 100dvh;
  max-width: 1440px;
  margin: 0 auto;
  padding-left: 24px;
  padding-right: 24px;
}
```

### 2.4 Sidebar (Sadece Göz At Sayfası)

| Özellik | Değer |
|---------|-------|
| Genişlik | 200px sabit |
| Sayfa | Sadece `browse.html` / `/browse` |
| Pozisyon | `position: fixed; left: 0; top: 70px; bottom: 104px` |
| Gorunurluk | Diğer sayfalarda `display: none` |
| İçerik | Kategori listesi, filtreler, playlist |
| Padding | 16px |
| Scroll | `overflow-y: auto; scrollbar-width: thin` |
| Border-right | `1px solid var(--border-subtle)` |

### 2.5 Grid Sistemi

| Özellik | Değer |
|---------|-------|
| Max Sütun | 4 |
| Min Sütun Genişliği | 220px |
| Gap | 16px |
| Container | `max-width: 1440px; padding: 0 24px` |
| Grid Tanımı | `grid-template-columns: repeat(auto-fill, minmax(220px, 1fr))` |

### 2.6 Sayfa Variasyonları

| Sayfa | Grid | Sidebar | Notlar |
|-------|------|---------|--------|
| Home | 4 sütun | Yok | Karşılama + öneriler |
| Browse | 4 sütun | 200px | Kategori filtreleme |
| Player | Tam genişlik | Yok | Split view: cover + liste |
| Settings | 2 sütun | Yok | Form görünümü |
| Library | 4 sütun | Yok | Albüm/sanatçı grid |

---

## 3. Touch Target Kuralları

| Özellik | Değer |
|---------|-------|
| Minimum Boyut | ≥44×44px (fare var, dokunmatik gerekli değil) |
| Recommended | 48×48px |
| Spacing | ≥4px boşluk |
| Cursor | `cursor: pointer` tüm interaktif elemanlarda |
| Active State | `transform: scale(0.98)` — 80ms |

```css
.interactive {
  min-width: 44px;
  min-height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 80ms ease, opacity 80ms ease;
}

.interactive:active {
  transform: scale(0.98);
}
```

**Not:** Fare input'u olduğu için touch target kuralları daha esnektir. Ancak dokunmatik ekran desteği de düşünülerek minimum 44px korunur.

---

## 4. Font Ölçeklendirme

| Özellik | Değer |
|---------|-------|
| Ölçek | 1.2× |
| Body Font | `Arima, sans-serif` |
| Display Font | `'Bickham Script Two', cursive` |
| Base Size | 16px × 1.2 = 19.2px |
| Min Font Size | 14px |
| Max Font Size | 32px (heading'ler) |
| Line Height | 1.5 (body), 1.2 (heading) |

### 4.1 Font Hiyerarşisi

| Token | Boyut (1.2×) | Weight | Kullanım |
|-------|-------------|--------|----------|
| `--font-size-xs` | 14.4px | 400 | Caption, timestamp |
| `--font-size-sm` | 16.8px | 400 | Body small, labels |
| `--font-size-base` | 19.2px | 400 | Body text |
| `--font-size-lg` | 21.6px | 600 | Subheadings |
| `--font-size-xl` | 24px | 700 | Section titles |
| `--font-size-2xl` | 28.8px | 700 | Page titles |
| `--font-size-3xl` | 32px | 700 | Hero headings |

### 4.2 Font CSS

```css
:root {
  --font-family-body: 'Arima', sans-serif;
  --font-family-display: 'Bickham Script Two', cursive;
  --font-size-base: 19.2px;
  --font-scale: 1.2;
}

body {
  font-family: var(--font-family-body);
  font-size: calc(var(--font-size-base) * var(--font-scale));
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
```

### 4.3 HiDPI Desteği

```css
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  body {
    -webkit-font-smoothing: antialiased;
  }
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

  /* Accent */
  --accent: #7c5cff;
  --accent-hover: #9070ff;
  --accent-glow: rgba(124, 92, 255, 0.25);

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
  --space-3xl: 48px;

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
  --shadow-xl: 0 16px 64px rgba(0, 0, 0, 0.6);

  /* Z-Index */
  --z-header: 1000;
  --z-footer: 1000;
  --z-sidebar: 900;
  --z-modal: 1100;
  --z-tooltip: 1200;

  /* Header/Footer */
  --header-height: 70px;
  --footer-height: 104px;
  --sidebar-width: 200px;

  /* Grid */
  --grid-max-columns: 4;
  --grid-min-width: 220px;
  --grid-gap: 16px;

  /* Touch/Cursor */
  --cursor-pointer: pointer;
  --min-interactive: 44px;
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

**Hover VAR.** Fare tabanlı etkileşim desteklenir.

```css
/* Hover state'leri aktif */
.interactive:hover {
  background: var(--bg-elevated);
  border-color: var(--border-strong);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
  transition: all 150ms ease;
}

.interactive:hover .icon {
  color: var(--accent-hover);
  transform: scale(1.05);
  transition: all 150ms ease;
}

/* Card hover */
.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  border-color: var(--accent);
}

/* Link hover */
a:hover {
  color: var(--accent-hover);
  text-decoration: underline;
  text-underline-offset: 3px;
}

/* Button hover variants */
.btn-primary:hover {
  background: var(--accent-hover);
  box-shadow: 0 0 20px var(--accent-glow);
}

.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.08);
}

/* Hover transition base */
* {
  transition-property: background-color, border-color, color, box-shadow, transform;
  transition-duration: 150ms;
  transition-timing-function: ease;
}

/* Focus-visible (fare ile klavye geçişi) */
*:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
  border-radius: var(--radius-sm);
}
```

### 6.1 Hover Kuralları

| Kural | Değer |
|-------|-------|
| Geçiş süresi | 150ms |
| Timing | `ease` |
| Efektler | `transform`, `box-shadow`, `background-color` |
| Scale | max 1.05× |
| Shadow | `var(--shadow-md)` → `var(--shadow-lg)` |
| Cursor | Tüm interaktif elemanlarda `pointer` |

---

## 7. CSS Üretim Talimatları

### 7.1 Agent Talimatı

```
Bu prompt'u kullanarak 1920×1080 desktop ekran için CSS üret.
Tüm stiller bu dosyadaki token değerlerini kullanmalı.
Hover efektleri aktif olmalı.
Font scale 1.2× uygulanmalı.
```

### 7.2 Zorunlu CSS Yapıları

1. **Reset:** `*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }`
2. **Body:** `overflow-x: hidden; background: var(--bg-primary); color: var(--text-primary);`
3. **Scroll Behavior:** `html { scroll-behavior: smooth; }`
4. **Selection:** `::selection { background: var(--accent); color: white; }`
5. **Scrollbar:** `scrollbar-width: thin; scrollbar-color: var(--border-default) transparent;`
6. **Hover Transitions:** Tüm interaktif elemanlarda 150ms ease
7. **Cursor:** Tüm interaktif elemanlarda `pointer`

### 7.3 Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `touch-action: manipulation` | Gerekmez (fare var) |
| `vw/vh` birimleri (header/footer) | `px` birimleri |
| `position: fixed` (sidebar dışı) | `sticky` veya `absolute` |
| `z-index > 2000` | Max `1200` |
| `font-size < 12px` | Min `14.4px` (1.2×) |
| Hover olmayan interaktif eleman | Tüm elemanlarda hover |

### 7.4 Çıktı Formatı

CSS çıktısı şu sırayla olmalı:
1. Custom properties (`:root`)
2. Reset
3. Typography (1.2× scale)
4. Layout (header, footer, sidebar, main)
5. Components (cards, buttons, inputs)
6. Glass effects
7. Hover states
8. Focus states
9. Animations
10. Utilities

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

- [ ] Header yüksekliği 70px
- [ ] Footer yüksekliği 104px
- [ ] Sidebar genişliği 200px (sadece browse)
- [ ] Touch/cursor target ≥44×44px
- [ ] Font scale 1.2×
- [ ] Glass blur(20px) saturate(180%)
- [ ] Grid max 4 sütun
- [ ] Hover efektleri aktif (150ms ease)
- [ ] Focus-visible outline var
- [ ] Tüm token değerleri bu dosyadan
- [ ] Responsive breakpoint yok (tek boyut)

---

*Screen Prompt v1.0.0 — CoreMusic Desktop Platform*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
