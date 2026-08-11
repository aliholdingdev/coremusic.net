---
title: "Mobile (Flutter/PWA) Değişken Çözünürlük"
category: screen-prompt
platform: mobile
resolution: "değişken (360×640 — 430×932)"
device: "Native Mobil (Android/iOS) veya PWA"
os: "Android 10+, iOS 15+, PWA (Chrome Safari)"
input: "Dokunmatik (parmak) + Jesture (swipe, pinch)"
version: 1.0.0
date: 2026-08-11
status: active
authority: Single Source of Truth (SSOT)
---

# Prompt: Mobile (Flutter/PWA) Screen

Bu prompt, CoreMusic UI Designer agent'ı tarafından **değişken çözünürlükteki mobil ekranlar** (Flutter native veya PWA) için CSS/HTML üretimi yaparken kullanılır.

---

## 1. Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Çözünürlük | Değişken (360×640 — 430×932) |
| Cihaz | Android telefon, iPhone, PWA |
| İşletim Sistemi | Android 10+, iOS 15+, PWA (tarayıcı) |
| Giriş Yöntemi | Dokunmatik (parmak) + Jesture |
| Tarayıcı | Chrome Mobile, Safari Mobile, PWA wrapper |
| Piksel Yoğunluğu | 2× — 4× (devicePixelRatio) |
| Safe Area | Dynamic Island, notch, statusBar, homeIndicator |
| Orientation | Portrait (varsayılan), Landscape (destekli) |

---

## 2. Layout Kuralları

### 2.1 Header (Top Bar)

| Özellik | Değer |
|---------|-------|
| Yükseklik | 56px sabit |
| Pozisyon | `position: fixed; top: 0` |
| z-index | 1000 |
| İçerik | Logo (28px), sayfa başlığı, action buttons |
| Padding | 0 12px |
| Safe Area | `padding-top: env(safe-area-inset-top)` |
| Safe Area Min | `min-height: 56px` |

### 2.2 Footer (Tab Bar)

| Özellik | Değer |
|---------|-------|
| Yükseklik | 72px sabit |
| Pozisyon | `position: fixed; bottom: 0` |
| z-index | 1000 |
| İçerik | 4-5 tab ikonu + etiket |
| Padding | 0 8px |
| Safe Area | `padding-bottom: env(safe-area-inset-bottom)` |
| Border-top | `1px solid var(--border-subtle)` |
| Background | Glass efekti (opsiyonel) |
| Active Indicator | Alt çizgi animasyonu |

### 2.3 Ana İçerik Alanı

```css
.main-content {
  padding-top: 56px;
  padding-bottom: 72px;
  min-height: 100dvh;
  padding-left: 0;
  padding-right: 0;
}
```

**Not:** Mobile'da yatay padding yoktur. İçerik tam genişlik kullanır.

### 2.4 Sidebar → Bottom Sheet (Modal)

**Sidebar mobile'da modal olarak gösterilir:**

| Özellik | Değer |
|---------|-------|
| Trigger | Swipe right veya buton |
| Tür | Bottom sheet |
| Yükseklik | Max ekranın %85'i |
| Pozisyon | `position: fixed; bottom: 0; left: 0; right: 0` |
| z-index | 1100 |
| Drag handle | 32×4px handle üstte |
| Swipe down | Kapatma jesture'ı |
| Backdrop | `rgba(0, 0, 0, 0.5)` + tap to close |
| Rounded corners | `border-radius: 16px 16px 0 0` |

```css
.bottom-sheet {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  max-height: 85dvh;
  background: var(--bg-elevated);
  border-radius: 16px 16px 0 0;
  z-index: 1100;
  transform: translateY(100%);
  transition: transform 300ms ease;
}

.bottom-sheet.open {
  transform: translateY(0);
}

.bottom-sheet-handle {
  width: 32px;
  height: 4px;
  background: var(--border-default);
  border-radius: 2px;
  margin: 8px auto;
}
```

### 2.5 Grid Sistemi

| Özellik | Değer |
|---------|-------|
| Max Sütun | 2 |
| Min Sütun Genişliği | 150px |
| Gap | 8px |
| Container | `padding: 0 12px` |
| Grid Tanımı | `grid-template-columns: repeat(2, 1fr)` |

### 2.6 Sayfa Variasyonları

| Sayfa | Grid | Sidebar | Notlar |
|-------|------|---------|--------|
| Home | 2 sütun | Yok | Karşılama + öneriler |
| Browse | 2 sütun | Bottom sheet | Kategori filtreleme |
| Player | Tam genişlik | Yok | Cover art + swipe kontrolleri |
| Settings | Tam genişlik | Yok | Liste görünümü |
| Search | Tam genişlik | Yok | Büyük arama input'u |

---

## 3. Touch Target Kuralları

| Özellik | Değer |
|---------|-------|
| Minimum Boyut | ≥48×48px |
| Recommended | 56×56px |
| Spacing | ≥8px boşluk |
| Active State | `transform: scale(0.95)` — 100ms |
| Focus Ring | Yok (dokunmatik cihaz) |
| Ripple | Material Design ripple efekti |

```css
.touch-target {
  min-width: 48px;
  min-height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
  position: relative;
  overflow: hidden;
}

.touch-target:active {
  transform: scale(0.95);
  transition: transform 100ms ease;
}

/* Ripple efekti */
.touch-target::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle, var(--accent-glow) 10%, transparent 10%);
  background-size: 1000% 1000%;
  background-position: center;
  opacity: 0;
  transition: opacity 300ms;
}

.touch-target:active::after {
  opacity: 1;
  animation: ripple 600ms ease-out;
}

@keyframes ripple {
  0% { background-size: 0% 0%; }
  100% { background-size: 1000% 1000%; opacity: 0; }
}
```

### 3.1 Swipe Gesture Kuralları

| Gesture | Aksiyon |
|---------|---------|
| Swipe Left | Bir sonraki şarkı |
| Swipe Right | Bir önceki şarkı |
| Swipe Up | Now Playing'i genişlet |
| Swipe Down | Now Playing'i daralt |
| Pinch | Cover art zoom |
| Long Press | Context menü |

```css
/* Swipe gesture alanı */
.swipe-container {
  touch-action: pan-y;
  overscroll-behavior-x: contain;
}

/* Horizontal scroll snap */
.scroll-row {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.scroll-row::-webkit-scrollbar {
  display: none;
}

.scroll-row > * {
  scroll-snap-align: start;
  flex-shrink: 0;
}
```

---

## 4. Font Ölçeklendirme

| Özellik | Değer |
|---------|-------|
| Ölçek | 1× (varsayılan) |
| Body Font | `Arima, sans-serif` |
| Display Font | `'Bickham Script Two', cursive` |
| Base Size | 16px |
| Min Font Size | 12px |
| Max Font Size | 22px (heading'ler) |
| Line Height | 1.5 (body), 1.2 (heading) |

### 4.1 Font Hiyerarşisi

| Token | Boyut | Weight | Kullanım |
|-------|-------|--------|----------|
| `--font-size-xs` | 11px | 400 | Badge, counter |
| `--font-size-sm` | 13px | 400 | Caption, timestamp |
| `--font-size-base` | 15px | 400 | Body text |
| `--font-size-lg` | 17px | 600 | Subheadings |
| `--font-size-xl` | 19px | 700 | Section titles |
| `--font-size-2xl` | 22px | 700 | Page titles |

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
  font-size: var(--font-size-base);
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
  -webkit-text-size-adjust: 100%; /* iOS zoom önleme */
}
```

### 4.3 Responsive Font (Opsiyonel)

```css
/* Küçük ekranlar (320-360px) */
@media (max-width: 360px) {
  :root {
    --font-size-base: 14px;
  }
}

/* Büyük telefon (430px+) */
@media (min-width: 430px) {
  :root {
    --font-size-base: 16px;
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
  --bg-glass: rgba(18, 18, 26, 0.85); /* Daha opak — mobil için */

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
  --z-bottom-sheet: 1100;
  --z-modal: 1200;
  --z-tooltip: 1300;

  /* Header/Footer */
  --header-height: 56px;
  --footer-height: 72px;

  /* Grid */
  --grid-max-columns: 2;
  --grid-min-width: 150px;
  --grid-gap: 8px;

  /* Touch */
  --touch-min-size: 48px;
  --touch-target-size: 56px;
  --touch-spacing: 8px;

  /* Safe Area */
  --safe-area-top: env(safe-area-inset-top, 0px);
  --safe-area-bottom: env(safe-area-inset-bottom, 0px);
  --safe-area-left: env(safe-area-inset-left, 0px);
  --safe-area-right: env(safe-area-inset-right, 0px);

  /* Status Bar */
  --status-bar-height: var(--safe-area-top);
  --home-indicator-height: var(--safe-area-bottom);
}
```

### 5.2 Glass Efekt Tokenları (Performans)

```css
.glass {
  background: var(--bg-glass);
  /* Mobilde backdrop-filter kullanılmaz — performans için */
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
}
```

**⚠️ Mobil Performans Notu:** `backdrop-filter` mobil cihazlarda çok pahalıdır. **Kullanılmaz.** Bunun yerine yarı-saydam solid renk veya gradient kullanılır.

---

## 6. Hover Davranışı

**Hover YOKTUR.** Mobil cihazlarda hover input'u yoktur.

```css
/* Hover state'leri devre dışı */
*:hover {
  /* Boş — hover efekti uygulanmaz */
}

/* Touch state'leri */
*:active {
  opacity: 0.8;
  transform: scale(0.98);
  transition: all 100ms ease;
}

/* Long press */
.long-press {
  transition: transform 200ms ease;
}

.long-press:active {
  transform: scale(0.95);
  transition: transform 500ms ease; /* Long press algılama */
}
```

### 6.1 Touch State Kuralları

| State | Efekt | Süre |
|-------|-------|------|
| `:active` (tap) | scale(0.98), opacity 0.8 | 100ms |
| Long Press | scale(0.95) | 500ms |
| Swipe | translateX/Y | 200ms |
| Release | scale(1), opacity 1 | 100ms |

---

## 7. CSS Üretim Talimatları

### 7.1 Agent Talimatı

```
Bu prompt'u kullanarak mobil ekran için CSS üret.
Tüm stiller bu dosyadaki token değerlerini kullanmalı.
Touch gesture'ları desteklenmeli.
Backdrop-filter kullanılmaz — performans için.
Safe area env() fonksiyonları kullanılmalı.
```

### 7.2 Zorunlu CSS Yapıları

1. **Reset:** `*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }`
2. **Body:** `overflow-x: hidden; background: var(--bg-primary); color: var(--text-primary);`
3. **Scroll Behavior:** `html { scroll-behavior: smooth; }`
4. **Selection:** `::selection { background: var(--accent); color: white; }`
5. **Scrollbar:** `scrollbar-width: none;` (mobilde scroll bar gizli)
6. **Safe Area:** `env(safe-area-inset-*)` fonksiyonları
7. **Touch:** `touch-action: manipulation` (sadece scroll ve tap)
8. **No Hover:** Hover state'leri devre dışı
9. **No Backdrop-filter:** Performans için yok
10. **Text Size Adjust:** `webkit-text-size-adjust: 100%` (iOS zoom önleme)

### 7.3 Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `hover:` medya sorgusu | Sadece `:active` ve touch state'leri |
| `backdrop-filter` | Solid/gradient arka plan |
| `vw/vh` birimleri (header/footer) | `px` veya `dvh` |
| `z-index > 2000` | Max `1300` |
| `font-size < 11px` | Min `11px` |
| `touch-action: none` | `touch-action: manipulation` |
| Sidebar (fixed) | Bottom sheet (modal) |
| Scroll bar görünür | Scroll bar gizli |

### 7.4 Çıktı Formatı

CSS çıktısı şu sırayla olmalı:
1. Custom properties (`:root`)
2. Reset
3. Typography (1× scale, responsive font)
4. Safe area handling
5. Layout (header, footer, main)
6. Components (cards, buttons, inputs, list items)
7. Bottom sheet (sidebar modal)
8. Touch states
9. Swipe gestures
10. Animations
11. Utilities

---

## 8. Flutter Entegrasyonu

### 8.1 CSS → Flutter Mapping

| CSS | Flutter Widget |
|-----|---------------|
| `.bottom-sheet` | `DraggableScrollableSheet` |
| `.tab-bar` | `BottomNavigationBar` |
| `.scroll-row` | `ListView.builder(scrollDirection: Axis.horizontal)` |
| `.card` | `Card` widget |
| `.touch-target` | `InkWell` + `Ink` |
| `.glass` | `BackdropFilter` + `ImageFilter.blur` |

### 8.2 PWA Manifest

```json
{
  "name": "CoreMusic",
  "short_name": "CoreMusic",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#0a0a0f",
  "theme_color": "#7c5cff",
  "orientation": "portrait",
  "icons": [
    { "src": "/icons/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icons/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}
```

---

## 9. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS katmanları |
| [[ADR-031-mobile-strategy-pwa-flutter]] | PWA + Flutter stratejisi |
| [[ADR-044-dynamic-user-theme-engine]] | Tema motoru, cinsiyet bazlı |
| [[ADR-045-multi-domain-view-mode-architecture]] | View mode mimarisi |
| [[ADR-048-view-transition-api-integration]] | View Transition API |

---

## 10. Checkpoint

CSS üretilmeden önce bu kontrol listesi doğrulanmalı:

- [ ] Header yüksekliği 56px
- [ ] Footer yüksekliği 72px
- [ ] Sidebar → Bottom sheet (modal)
- [ ] Touch target ≥48×48px
- [ ] Font scale 1×
- [ ] Glass efekti yok (backdrop-filter yok)
- [ ] Grid max 2 sütun
- [ ] Hover yok, sadece touch state
- [ ] Safe area env() fonksiyonları
- [ ] Scroll bar gizli
- [ ] Touch action: manipulation
- [ ] iOS text-size-adjust: 100%
- [ ] Tüm token değerleri bu dosyadan
- [ ] Responsive breakpoint'ler (opsiyonel)

---

*Screen Prompt v1.0.0 — CoreMusic Mobile Platform*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
