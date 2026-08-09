# CSS Standards — CoreMusic

**Authority:** ADR-001 (Vanilla JS + ITCSS)
**Last Updated:** 2026-08-06
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. Architecture: ITCSS (Inverted Triangle CSS)

```
01_Abstracts   — Variables, tokens (a-*.css)
02_Base        — Reset, typography (b-*.css)
03_Layout      — Header, footer, grid (_header.css, _footer.css)
04_Components  — BEM blocks (c-*.css)
05_Pages       — Page-specific (p-*.css, _home-*.css)
06_Utilities   — Helper classes (u-*.css)
07_Vendors     — Third-party (v-*.css)
08_Devices     — Device-specific responsive (d-*.css)
09_ViewModes   — View mode overrides (v-home.css, v-pro.css, v-studio.css)
```

**Layer Order is IMMUTABLE.** Never reorder imports in main.css.

---

## 2. BEMIT Namespaces (Zorunlu)

| Prefix | Layer | Example |
|--------|-------|---------|
| `a-` | Abstracts (tokens) | `a-colors-token.css` |
| `b-` | Base (reset) | `b-base-core.css` |
| `c-` | Components | `c-footer__seek-slider` |
| `o-` | Objects | `o-media__image` |
| `u-` | Utilities | `u-hidden@print` |
| `p-` | Pages | `p-login-view` |
| `v-` | Vendors | `v-bootstrap-lib` |
| `d-` | Devices | `d-embedded.css` |
| `is-` / `has-` | States | `is-active`, `has-dropdown` |
| `l-` | Layout (custom) | `l-main-structural` |

**BEM Format:** `.block__element--modifier`

```css
/* ✅ DOĞRU */
.c-footer__seek-slider {}
.c-footer__btn--play {}

/* ❌ YANLIŞ */
.footerSeekSlider {}
.footer-btn-play {}
```

---

## 3. File Naming Convention

| Layer | Prefix | Pattern | Example |
|-------|--------|---------|---------|
| 01_Abstracts | `a-` | `a-{name}.css` | `a-colors-token.css` |
| 02_Base | `b-` | `b-{name}.css` | `b-base-core.css` |
| 03_Layout | `_` or `l-` | `_{name}.css` | `_header.css` |
| 04_Components | `c-` | `c-{name}.css` | `c-scrollbar-accent.css` |
| 05_Pages | `p-` or `_` | `p-{name}.css` | `p-login-view.css` |
| 06_Utilities | `u-` | `u-{name}.css` | `u-helpers-utility.css` |
| 07_Vendors | `v-` | `v-{name}.css` | `v-bootstrap-lib.css` |
| 08_Devices | `d-` | `d-{device}.css` | `d-embedded.css` |
| 09_ViewModes | `v-` | `v-{mode}.css` | `v-home.css` |

---

## 4. Import Strategy

### 4.1 main.css — Temel Katmanlar

```css
/* main.css — sadece 01-07 katmanlarını import eder */
@import url("./01_Abstracts/a-fonts-token.css");
@import url("./01_Abstracts/a-colors-token.css");
@import url("./01_Abstracts/a-semantic-token.css");
@import url("./01_Abstracts/a-theme-config.css");
@import url("./01_Abstracts/a-light-glass-tokens.css");
@import url("./01_Abstracts/a-login-tokens.css");
@import url("./01_Abstracts/a-breakpoint-tokens.css");
@import url("./01_Abstracts/a-layout-tokens.css");
@import url("./02_Base/b-base-core.css");
@import url("./02_Base/l-main-structural.css");
@import url("./03_Layout/_header.css");
@import url("./03_Layout/_footer.css");
@import url("./04_Components/c-scrollbar-accent.css");
@import url("./04_Components/c-footer-seek.css");
@import url("./04_Components/c-footer-volume.css");
@import url("./05_Pages/_home.css");
@import url("./05_Pages/p-select-gender.css");
@import url("./05_Pages/p-login-view.css");
@import url("./06_Utilities/u-helpers-utility.css");
@import url("./07_Vendors/v-bootstrap-lib.css");
/* 08_Devices ve 09_ViewModes main.css'e EKLENMEZ — dinamik yüklenir */
```

### 4.2 Device CSS — Self-Contained (Neden?)

Device CSS dosyaları (`d-*.css`) **dinamik olarak yüklenir** (device-loader.js tarafından). Bu yüzden kendi import zincirlerini kendi içlerinde taşırlar.

**Akış:**
```
1. Sayfa yüklenir → main.css yüklenir (01-07 katmanları)
2. device-loader.js çalışır → viewport algılar
3. Doğru d-*.css dosyası dinamik olarak eklenir
4. d-*.css kendi import'unu yapar (gerekli katmanları alır)
5. Device-specific overrides uygulanır
```

**Örnek — d-embedded.css:**
```css
/* d-embedded.css — kendi import zincirini taşır */
@import '../01_Abstracts/a-theme-config.css';
@import '../03_Layout/_header.css';
@import '../03_Layout/_footer.css';
@import '../05_Pages/_home-layout.css';
@import '../05_Pages/_home-components.css';
@import '../05_Pages/_home-inline.css';

/* Device-specific overrides (RPi5 1024x600) */
:root {
  --header-h: 60px;
  --footer-h: 104px;
}
```

**Örnek — d-desktop.css:**
```css
/* d-desktop.css — kendi import zincirini taşır */
@import '../01_Abstracts/a-theme-config.css';
@import '../03_Layout/_header.css';
@import '../03_Layout/_footer.css';
@import '../05_Pages/_home-layout.css';
@import '../05_Pages/_home-components.css';
@import '../05_Pages/_home-inline.css';

/* Device-specific overrides (1441-2560px) */
:root {
  --header-h: 70px;
  --footer-h: 138px;
}
```

**Kural:** `main.css`'e `08_Devices/` import EKLEMEZ. Device CSS'ler bağımsızdır.

### 4.3 device-loader.js — Dinamik Yükleme

```javascript
// device-loader.js — viewport algılama → doğru d-*.css yükler
// Breakpoint'ler PHP DeviceCssMap ile senkronize:
//   ≤767px    → phone
//   768-1024  → tablet
//   1025-1440 → laptop
//   1441-2560 → desktop (varsayılan)
//   2561-3840 → 4k-tv
//   ≥3841     → 4k-monitor

var BREAKPOINTS = [
    { name: 'phone',      max: 767   },
    { name: 'tablet',     max: 1024  },
    { name: 'laptop',     max: 1440  },
    { name: 'desktop',    max: 2560  },
    { name: '4k-tv',      max: 3840  },
    { name: '4k-monitor', max: Infinity }
];

function loadDeviceCSS(device) {
    var path = '08_Devices/d-' + device + '.css';
    // Dinamik <link> oluştur ve <head>'e ekle
}
```

### 4.4 View Mode CSS — Self-Contained

View mode CSS dosyaları (`v-*.css`) da dinamik olarak yüklenir:
```javascript
// device-loader.js
function loadViewModeCSS(mode) {
    var path = '09_ViewModes/v-' + mode + '.css';
    // Cookie: view-mode=home|pro|studio
}
```

### 4.5 Mevcut Dosya Durumu

| Dosya | Durum | Satır |
|-------|-------|-------|
| `d-embedded.css` | ✅ Dolu | 197 satır (RPi5) |
| `d-laptop.css` | ✅ Dolu | 65 satır |
| `d-desktop.css` | ✅ Dolu | 48 satır (varsayılan) |
| `d-4k-tv.css` | ✅ Dolu | 167 satır (10ft UI) |
| `d-4k-monitor.css` | ✅ Dolu | 180 satır |
| `d-phone.css` | ❌ Boş | — |
| `d-tablet.css` | ❌ Boş | — |
| `v-home.css` | ✅ Dolu | 60 satır |
| `v-pro.css` | ✅ Dolu | — |
| `v-studio.css` | ✅ Dolu | — |

**Not:** `d-phone.css` ve `d-tablet.css` boş — mobil hariç (mobil uygulama ayrı olacak).

---

## 5. Token Rules

### 5.1 Zorunluluklar
- **CSS custom properties** kullan (`var(--token-name)`)
- **Hardcoded renk/değer yasak** → `#e8b4b8` yerine `var(--theme-primary)`
- **Token dosyaları:** `01_Abstracts/a-*.css`

### 5.2 Token Kategorileri

```css
:root {
  /* Renkler */
  --color-primary: #ff4fd8;
  --color-bg: #0f0f1a;

  /* Tipografi */
  --font-heading: 'Respective', sans-serif;
  --text-base: 1rem;

  /* Boşluk */
  --space-4: 1rem;

  /* Border Radius */
  --radius-md: 8px;

  /* Transition */
  --transition-base: 250ms ease;

  /* Layout */
  --header-h: 70px;
  --footer-h: 138px;
  --sidebar-w: 280px;
}
```

---

## 6. Responsive Strategy

### 6.1 Device Breakpoints

| Dosya | Cihaz | Genişlik |
|-------|-------|----------|
| `d-embedded.css` | RPi5 | ≤1024px |
| `d-laptop.css` | Laptop | 1025-1440px |
| `d-desktop.css` | Masaüstü | 1441-2560px |
| `d-4k-tv.css` | 4K TV | 2561-3840px |
| `d-4k-monitor.css` | 4K Monitor | ≥3841px |
| `d-phone.css` | Telefon | ≤767px (mobil hariç) |
| `d-tablet.css` | Tablet | 768-1024px (mobil hariç) |

### 6.2 Kurallar
- **Device CSS bağımsızdır** — kendi import'unu kendi yapar
- **main.css device import ETMEZ**
- **Media query yerine** device-loader.js kullanılır
- **Touch-first:** RPi5'de minimum touch target 48px

---

## 7. Accessibility (WCAG 2.2 AA)

| Kural | Minimum |
|-------|---------|
| Renk kontrastı (text) | ≥ 4.5:1 |
| Renk kontrastı (large text) | ≥ 3:1 |
| Touch target (mobil) | ≥ 44px |
| Touch target (dokunmatik) | ≥ 48px |
| Focus indicator | Görünür olmalı |
| Screen reader | ARIA labels zorunlu |
| Klavye navigasyonu | Tüm interaktif elementler |

---

## 8. Forbidden Patterns

| ❌ Yasak | ✅ Doğru | İstisna |
|----------|----------|---------|
| Tailwind CSS, Bootstrap | ITCSS + BEM | — |
| CSS-in-JS | Vanilla CSS | — |
| `!important` | Specificity ile çöz | Utility override |
| Inline style | Class ile çöz | Dinamik değerler |
| Framework | Custom kod | — |
| Hardcoded renk `#fff` | `var(--color-text)` | — |
| Magic number `123px` | `var(--space-4)` | — |

---

## 9. Theme Engine (ADR-044)

### 9.1 Cinsiyet Bazlı Tema
```css
[data-gender="female"] { --theme-primary: #ff4fd8; }  /* pink */
[data-gender="male"]   { --theme-primary: #4f9fff; }  /* blue */
[data-gender="neutral"] { --theme-primary: #a0a0b0; }  /* default */
```

### 9.2 Kurallar
- **`data-gender` attribute:** `<html>`, `<body>` ve container'larda zorunlu
- **Hardcoded tema yasak:** `if female show pink.png` forbidden
- **Sayfa yenileme YOK:** CSS custom properties ile anında geçiş
- **Asset dizinleri:** `res-pink/`, `res-blue/`, `res-default/`
- **Admin paneli:** Bağımsız tema sistemi (kullanıcı temalarından ayrı)

---

## 10. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Yeni token | `01_Abstracts/a-*.css` |
| Yeni bileşen | `04_Components/c-*.css` |
| Yeni sayfa | `05_Pages/p-*.css` |
| Cihaz responsive | `08_Devices/d-*.css` |
| Tema değişikliği | `09_ViewModes/v-*.css` |
| main.css'e import | Sadece 01-07 katmanları |
| Device CSS import | d-*.css içine kendi import'unu yaz |

---

*CSS Standards v2.0.0 — CoreMusic Enterprise Architecture*
*Authority: ADR-001 (Vanilla JS + ITCSS)*
*Last Updated: 2026-08-06*
