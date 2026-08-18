---
title: "CoreMusic — CSS Design Tokens (Custom Properties)"
type: reference
category: design-system
date: 2026-08-17
updated: 2026-08-17
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
platforms: [rpi5-1024, desktop-1920, mobile-375, tv-3840]
themes: [female, male, neutral]
reference:
  authority: ".ai/ui-design/reference/css-design-tokens.md"
  related:
    - ".ai/ui-design/tokens/design-tokens-master.md"
    - ".ai/ui-design/tokens/color-palettes.md"
    - ".ai/ui-design/tokens/platform-tokens.md"
---

# CoreMusic — CSS Design Tokens

**Doğrudan kopyalanabilecek CSS custom properties.** Tüm tema ve platform farkları dahildir.

> **⚠️ Bu dosya `:root` ve `[data-gender]` bloklarını içerir.** CSS dosyasının en üstüne kopyalanır.

---

## 1. Temel CSS Reset & Variables

```css
/* ============================================
   CoreMusic Design Tokens — CSS Custom Properties
   Version: 1.0.0
   Last Updated: 2026-08-17
   ============================================ */

/* === CSS RESET === */
*, *::before, *::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  line-height: var(--leading-normal);
  color: var(--white);
  background: var(--black);
  overflow-x: hidden;
}

a {
  color: var(--accent);
  text-decoration: none;
  transition: var(--transition-colors);
}

a:hover {
  color: var(--accent-hover);
}

button {
  font-family: inherit;
  cursor: pointer;
  border: none;
  background: none;
  color: inherit;
}

input, textarea, select {
  font-family: inherit;
  font-size: inherit;
  color: inherit;
  background: none;
  border: none;
  outline: none;
}

ul, ol {
  list-style: none;
}

img {
  max-width: 100%;
  height: auto;
  display: block;
}

/* === VARSAYILAN TEMA (FEMALE) === */
:root {
  /* --- Renk Token'ları --- */
  --accent: #ff4fd8;
  --accent-rgb: 255,79,216;
  --accent-hover: #e63dc0;
  --accent-active: #cc2ba8;
  --accent-light: #ff7fe6;
  --accent-dark: #cc3fad;
  --accent-subtle: #fff0fb;

  /* --- Arka Plan Renkleri --- */
  --accent-bg: rgba(255,79,216,0.15);
  --accent-bg-hover: rgba(255,79,216,0.25);
  --accent-bg-active: rgba(255,79,216,0.35);
  --accent-bg-subtle: rgba(255,79,216,0.08);
  --accent-border: rgba(255,79,216,0.3);
  --accent-border-hover: rgba(255,79,216,0.5);
  --accent-glow: 0 0 20px rgba(255,79,216,0.4);
  --accent-glow-lg: 0 0 40px rgba(255,79,216,0.6);

  /* --- Gradient'ler --- */
  --accent-gradient: linear-gradient(135deg, #ff4fd8, #ff7fe6);
  --accent-gradient-hover: linear-gradient(135deg, #e63dc0, #ff4fd8);
  --accent-gradient-vertical: linear-gradient(180deg, #ff4fd8, #cc3fad);
  --accent-gradient-radial: radial-gradient(circle, #ff4fd8, #cc3fad);

  /* --- Semantik Renkler --- */
  --success: #22c55e;
  --success-hover: #16a34a;
  --success-bg: rgba(34,197,94,0.15);
  --success-border: rgba(34,197,94,0.3);
  --warning: #eab308;
  --warning-hover: #ca8a04;
  --warning-bg: rgba(234,179,8,0.15);
  --warning-border: rgba(234,179,8,0.3);
  --error: #ef4444;
  --error-hover: #dc2626;
  --error-bg: rgba(239,68,68,0.15);
  --error-border: rgba(239,68,68,0.3);
  --info: #3b82f6;
  --info-hover: #2563eb;
  --info-bg: rgba(59,130,246,0.15);
  --info-border: rgba(59,130,246,0.3);

  /* --- Statik Renkler --- */
  --white: #ffffff;
  --white-50: rgba(255,255,255,0.5);
  --white-70: rgba(255,255,255,0.7);
  --white-85: rgba(255,255,255,0.85);
  --black: #000000;
  --black-50: rgba(0,0,0,0.5);
  --black-70: rgba(0,0,0,0.7);
  --gray-50: #f9fafb;
  --gray-100: #f3f4f6;
  --gray-200: #e5e7eb;
  --gray-300: #d1d5db;
  --gray-400: #9ca3af;
  --gray-500: #6b7280;
  --gray-600: #4b5563;
  --gray-700: #374151;
  --gray-800: #1f2937;
  --gray-900: #111827;

  /* --- Glass Efekt Renkleri --- */
  --glass-bg: rgba(255,255,255,0.08);
  --glass-bg-hover: rgba(255,255,255,0.12);
  --glass-bg-active: rgba(255,255,255,0.16);
  --glass-border: rgba(255,255,255,0.1);
  --glass-border-hover: rgba(255,255,255,0.2);
  --glass-blur: blur(20px);
  --glass-saturate: saturate(180%);
  --glass-blur-light: blur(8px);
  --glass-blur-heavy: blur(40px);
  --overlay-bg: rgba(0,0,0,0.5);
  --overlay-blur: blur(4px);

  /* --- Font Token'ları --- */
  --font-body: 'Arima', sans-serif;
  --font-logo: 'Bickham Script Two', cursive;
  --font-heading: 'Arima', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  /* --- Font Boyutları --- */
  --text-xs: 10px;
  --text-sm: 11px;
  --text-base: 12px;
  --text-md: 13px;
  --text-lg: 14px;
  --text-xl: 16px;
  --text-2xl: 20px;
  --text-3xl: 24px;
  --text-4xl: 32px;
  --text-5xl: 40px;

  /* --- Font Ağırlıkları --- */
  --font-light: 300;
  --font-regular: 400;
  --font-medium: 500;
  --font-semibold: 600;
  --font-bold: 700;

  /* --- Satır Yükseklikleri --- */
  --leading-none: 1;
  --leading-tight: 1.25;
  --leading-normal: 1.5;
  --leading-relaxed: 1.75;

  /* --- Spacing Token'ları --- */
  --space-0: 0;
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
  --space-10: 40px;
  --space-12: 48px;
  --space-16: 64px;
  --space-20: 80px;
  --space-24: 96px;

  /* --- Bileşen İçi Spacing --- */
  --card-padding: 12px;
  --card-padding-lg: 16px;
  --input-padding-x: 12px;
  --input-padding-y: 14px;
  --button-padding-x: 16px;
  --button-padding-y: 12px;
  --modal-padding: 24px;
  --section-gap: 16px;

  /* --- Layout Token'ları (RPi5 Varsayılan) --- */
  --header-h: 60px;
  --footer-h: 90px;
  --content-h: 450px;
  --content-padding-top: 11px;
  --content-padding-bottom: 15px;
  --page-padding-x: 16px;
  --sidebar-w: 167px;
  --detail-panel-w: 366px;
  --grid-gap: 8px;
  --grid-gap-lg: 16px;
  --grid-columns: 12;
  --grid-max-width: 1024px;

  /* --- Border Token'ları --- */
  --radius-none: 0;
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-2xl: 20px;
  --radius-full: 9999px;
  --radius-card: 12px;
  --radius-input: 8px;
  --radius-button: 8px;
  --radius-pill: 20px;
  --border-none: 0;
  --border-thin: 1px;
  --border-medium: 2px;
  --border-thick: 3px;
  --border-accent: 3px;

  /* --- Shadow Token'ları --- */
  --shadow-none: none;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.1);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.15);
  --shadow-lg: 0 10px 15px rgba(0,0,0,0.2);
  --shadow-xl: 0 20px 25px rgba(0,0,0,0.25);
  --shadow-2xl: 0 25px 50px rgba(0,0,0,0.3);
  --shadow-inner: inset 0 2px 4px rgba(0,0,0,0.1);
  --glass-shadow: 0 8px 32px rgba(0,0,0,0.3);
  --glass-shadow-lg: 0 16px 48px rgba(0,0,0,0.4);

  /* --- Animation Token'ları --- */
  --duration-fast: 100ms;
  --duration-normal: 200ms;
  --duration-slow: 300ms;
  --duration-slower: 500ms;
  --duration-slowest: 700ms;
  --ease-linear: linear;
  --ease-in: ease-in;
  --ease-out: ease-out;
  --ease-in-out: ease-in-out;
  --ease-bounce: cubic-bezier(0.68,-0.55,0.265,1.55);
  --ease-smooth: cubic-bezier(0.4,0,0.2,1);
  --transition-colors: color var(--duration-normal) var(--ease-smooth),
                       background-color var(--duration-normal) var(--ease-smooth),
                       border-color var(--duration-normal) var(--ease-smooth);
  --transition-transform: transform var(--duration-normal) var(--ease-smooth);
  --transition-opacity: opacity var(--duration-normal) var(--ease-smooth);
  --transition-all: all var(--duration-normal) var(--ease-smooth);

  /* --- Touch Target Token'ları --- */
  --touch-min: 48px;
  --touch-target-sm: 48px;
  --touch-target-md: 56px;
  --touch-target-lg: 64px;

  /* --- Z-Index Token'ları --- */
  --z-base: 0;
  --z-dropdown: 100;
  --z-sticky: 200;
  --z-overlay: 300;
  --z-modal: 400;
  --z-popover: 500;
  --z-tooltip: 600;
  --z-toast: 700;
  --z-player: 800;
  --z-header: 900;
  --z-max: 9999;

  /* --- Opacity Token'ları --- */
  --opacity-0: 0;
  --opacity-5: 0.05;
  --opacity-10: 0.1;
  --opacity-15: 0.15;
  --opacity-20: 0.2;
  --opacity-25: 0.25;
  --opacity-50: 0.5;
  --opacity-75: 0.75;
  --opacity-100: 1;

  /* --- Bileşen Token'ları --- */
  --card-bg: var(--glass-bg);
  --card-border: 1px solid var(--glass-border);
  --card-radius: var(--radius-card);
  --card-shadow: var(--shadow-md);
  --card-hover-bg: var(--glass-bg-hover);
  --card-active-bg: var(--glass-bg-active);
  --card-thumb-size: 140px;
  --card-thumb-radius: var(--radius-md);
  --card-thumb-gap: var(--space-2);

  --btn-h: 48px;
  --btn-h-sm: 36px;
  --btn-h-lg: 56px;
  --btn-radius: var(--radius-button);
  --btn-padding-x: var(--button-padding-x);
  --btn-padding-y: var(--button-padding-y);
  --btn-font-size: var(--text-base);
  --btn-font-weight: var(--font-medium);
  --btn-primary-bg: var(--accent);
  --btn-primary-color: var(--white);
  --btn-primary-hover: var(--accent-hover);
  --btn-secondary-bg: transparent;
  --btn-secondary-color: var(--white);
  --btn-secondary-border: 1px solid rgba(255,255,255,0.3);

  --input-h: 48px;
  --input-h-lg: 56px;
  --input-radius: var(--radius-input);
  --input-padding-x: var(--input-padding-x);
  --input-padding-y: var(--input-padding-y);
  --input-bg: rgba(255,255,255,0.1);
  --input-border: 1px solid rgba(255,255,255,0.2);
  --input-focus-border: 1px solid var(--accent);
  --input-color: var(--white);
  --input-placeholder: rgba(255,255,255,0.5);
  --input-label-size: var(--text-sm);
  --input-label-color: rgba(255,255,255,0.7);

  --tab-h: 32px;
  --tab-h-lg: 40px;
  --tab-radius: var(--radius-pill);
  --tab-padding-x: 12px;
  --tab-font-size: var(--text-xs);
  --tab-bg: transparent;
  --tab-active-bg: var(--accent);
  --tab-color: rgba(255,255,255,0.7);
  --tab-active-color: var(--white);
  --tab-border: 1px solid rgba(255,255,255,0.2);
  --tab-gap: 4px;

  --modal-radius: var(--radius-xl);
  --modal-padding: var(--modal-padding);
  --modal-bg: var(--glass-bg);
  --modal-border: 1px solid var(--glass-border);
  --modal-shadow: var(--glass-shadow-lg);
  --modal-blur: var(--glass-blur);
  --modal-saturate: var(--glass-saturate);
  --modal-backdrop: var(--overlay-bg);
  --modal-backdrop-blur: var(--overlay-blur);

  --toggle-w: 50px;
  --toggle-h: 28px;
  --toggle-radius: var(--radius-full);
  --toggle-bg-off: rgba(255,255,255,0.2);
  --toggle-bg-on: var(--accent);
  --toggle-knob-size: 22px;
  --toggle-knob-color: var(--white);

  --network-row-h: 48px;
  --network-row-radius: var(--radius-md);
  --network-row-bg: var(--glass-bg);
  --network-row-padding: 8px 12px;

  --badge-h: 20px;
  --badge-radius: var(--radius-full);
  --badge-padding-x: 6px;
  --badge-font-size: 9px;
  --badge-success-bg: var(--success-bg);
  --badge-success-color: var(--success);
  --badge-warning-bg: var(--warning-bg);
  --badge-warning-color: var(--warning);
  --badge-error-bg: var(--error-bg);
  --badge-error-color: var(--error);
}
```

---

## 2. Tema Değişkenleri

```css
/* === MALE TEMA === */
[data-gender="male"] {
  --accent: #4f9fff;
  --accent-rgb: 79,159,255;
  --accent-hover: #3d8ae6;
  --accent-active: #2c79cc;
  --accent-light: #7fbfff;
  --accent-dark: #3f80cc;
  --accent-subtle: #eff6ff;
  --accent-bg: rgba(79,159,255,0.15);
  --accent-bg-hover: rgba(79,159,255,0.25);
  --accent-bg-active: rgba(79,159,255,0.35);
  --accent-bg-subtle: rgba(79,159,255,0.08);
  --accent-border: rgba(79,159,255,0.3);
  --accent-border-hover: rgba(79,159,255,0.5);
  --accent-glow: 0 0 20px rgba(79,159,255,0.4);
  --accent-glow-lg: 0 0 40px rgba(79,159,255,0.6);
  --accent-gradient: linear-gradient(135deg, #4f9fff, #7fbfff);
  --accent-gradient-hover: linear-gradient(135deg, #3d8ae6, #4f9fff);
  --accent-gradient-vertical: linear-gradient(180deg, #4f9fff, #3f80cc);
  --accent-gradient-radial: radial-gradient(circle, #4f9fff, #3f80cc);
}

/* === NEUTRAL TEMA === */
[data-gender="neutral"] {
  --accent: #a0a0b0;
  --accent-rgb: 160,160,176;
  --accent-hover: #8a8a9a;
  --accent-active: #74747f;
  --accent-light: #b8b8c4;
  --accent-dark: #808089;
  --accent-subtle: #f5f5f7;
  --accent-bg: rgba(160,160,176,0.15);
  --accent-bg-hover: rgba(160,160,176,0.25);
  --accent-bg-active: rgba(160,160,176,0.35);
  --accent-bg-subtle: rgba(160,160,176,0.08);
  --accent-border: rgba(160,160,176,0.3);
  --accent-border-hover: rgba(160,160,176,0.5);
  --accent-glow: 0 0 20px rgba(160,160,176,0.4);
  --accent-glow-lg: 0 0 40px rgba(160,160,176,0.6);
  --accent-gradient: linear-gradient(135deg, #a0a0b0, #b8b8c4);
  --accent-gradient-hover: linear-gradient(135deg, #8a8a9a, #a0a0b0);
  --accent-gradient-vertical: linear-gradient(180deg, #a0a0b0, #808089);
  --accent-gradient-radial: radial-gradient(circle, #a0a0b0, #808089);
}
```

---

## 3. Platform Bazlı Değişiklikler

```css
/* === DESKTOP (1920×1080) === */
@media (min-width: 1024px) {
  :root {
    --header-h: 70px;
    --footer-h: 104px;
    --content-h: 906px;
    --content-padding-top: 14px;
    --content-padding-bottom: 20px;
    --page-padding-x: 24px;
    --sidebar-w: 280px;
    --detail-panel-w: 480px;
    --grid-gap: 12px;
    --card-thumb-size: 180px;
    --card-padding: 16px;
    --touch-min: 44px;
    --text-xs: 12px;
    --text-sm: 13px;
    --text-base: 14px;
    --text-lg: 16px;
    --text-xl: 18px;
    --text-2xl: 24px;
    --text-3xl: 30px;
  }
}

/* === MOBILE (375×812) === */
@media (max-width: 767px) {
  :root {
    --header-h: 56px;
    --footer-h: 72px;
    --content-h: 684px;
    --content-padding-top: 8px;
    --content-padding-bottom: 12px;
    --page-padding-x: 16px;
    --sidebar-w: 0;
    --detail-panel-w: 100%;
    --grid-gap: 8px;
    --card-thumb-size: 120px;
    --card-padding: 12px;
    --touch-min: 48px;
    --glass-blur: none;
    --glass-saturate: none;
  }
}

/* === TV (3840×2160) === */
@media (min-width: 1920px) {
  :root {
    --header-h: 90px;
    --footer-h: 138px;
    --content-h: 1932px;
    --content-padding-top: 18px;
    --content-padding-bottom: 24px;
    --page-padding-x: 32px;
    --sidebar-w: 320px;
    --detail-panel-w: 640px;
    --grid-gap: 16px;
    --card-thumb-size: 280px;
    --card-padding: 20px;
    --touch-min: 60px;
    --glass-blur: blur(4px);
    --glass-saturate: none;
    --text-xs: 16px;
    --text-sm: 18px;
    --text-base: 20px;
    --text-lg: 24px;
    --text-xl: 28px;
    --text-2xl: 36px;
    --text-3xl: 48px;
  }
}
```

---

## 4. Utility Sınıfları

```css
/* === GLASS EFECT === */
.glass {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur) var(--glass-saturate);
  -webkit-backdrop-filter: var(--glass-blur) var(--glass-saturate);
  border: var(--card-border);
  border-radius: var(--card-radius);
  box-shadow: var(--glass-shadow);
}

.glass-light {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur-light);
  -webkit-backdrop-filter: var(--glass-blur-light);
  border: var(--card-border);
  border-radius: var(--card-radius);
}

.glass-heavy {
  background: var(--glass-bg);
  backdrop-filter: var(--glass-blur-heavy) var(--glass-saturate);
  -webkit-backdrop-filter: var(--glass-blur-heavy) var(--glass-saturate);
  border: var(--card-border);
  border-radius: var(--card-radius);
  box-shadow: var(--glass-shadow-lg);
}

/* === ACCENT RENKLERİ === */
.text-accent { color: var(--accent); }
.text-accent-hover { color: var(--accent-hover); }
.bg-accent { background-color: var(--accent); }
.bg-accent-hover { background-color: var(--accent-hover); }
.border-accent { border-color: var(--accent); }

/* === SEMANTİK RENKLER === */
.text-success { color: var(--success); }
.text-warning { color: var(--warning); }
.text-error { color: var(--error); }
.text-info { color: var(--info); }
.bg-success { background-color: var(--success); }
.bg-warning { background-color: var(--warning); }
.bg-error { background-color: var(--error); }
.bg-info { background-color: var(--info); }

/* === TEXT RENKLERİ === */
.text-white { color: var(--white); }
.text-white-50 { color: var(--white-50); }
.text-white-70 { color: var(--white-70); }
.text-white-85 { color: var(--white-85); }
.text-muted { color: var(--gray-400); }

/* === FONT === */
.font-body { font-family: var(--font-body); }
.font-logo { font-family: var(--font-logo); }
.font-heading { font-family: var(--font-heading); }
.font-mono { font-family: var(--font-mono); }

/* === FONT BOYUTLARI === */
.text-xs { font-size: var(--text-xs); }
.text-sm { font-size: var(--text-sm); }
.text-base { font-size: var(--text-base); }
.text-lg { font-size: var(--text-lg); }
.text-xl { font-size: var(--text-xl); }
.text-2xl { font-size: var(--text-2xl); }
.text-3xl { font-size: var(--text-3xl); }

/* === SPACING === */
.p-0 { padding: var(--space-0); }
.p-1 { padding: var(--space-1); }
.p-2 { padding: var(--space-2); }
.p-3 { padding: var(--space-3); }
.p-4 { padding: var(--space-4); }
.p-6 { padding: var(--space-6); }
.p-8 { padding: var(--space-8); }
.m-0 { margin: var(--space-0); }
.m-1 { margin: var(--space-1); }
.m-2 { margin: var(--space-2); }
.m-3 { margin: var(--space-3); }
.m-4 { margin: var(--space-4); }
.gap-1 { gap: var(--space-1); }
.gap-2 { gap: var(--space-2); }
.gap-3 { gap: var(--space-3); }
.gap-4 { gap: var(--space-4); }
.gap-6 { gap: var(--space-6); }
.gap-8 { gap: var(--space-8); }

/* === BORDER RADIUS === */
.rounded-none { border-radius: var(--radius-none); }
.rounded-sm { border-radius: var(--radius-sm); }
.rounded-md { border-radius: var(--radius-md); }
.rounded-lg { border-radius: var(--radius-lg); }
.rounded-xl { border-radius: var(--radius-xl); }
.rounded-2xl { border-radius: var(--radius-2xl); }
.rounded-full { border-radius: var(--radius-full); }

/* === SHADOW === */
.shadow-none { box-shadow: var(--shadow-none); }
.shadow-sm { box-shadow: var(--shadow-sm); }
.shadow-md { box-shadow: var(--shadow-md); }
.shadow-lg { box-shadow: var(--shadow-lg); }
.shadow-xl { box-shadow: var(--shadow-xl); }
.shadow-2xl { box-shadow: var(--shadow-2xl); }

/* === TRANSITION === */
.transition-colors { transition: var(--transition-colors); }
.transition-transform { transition: var(--transition-transform); }
.transition-opacity { transition: var(--transition-opacity); }
.transition-all { transition: var(--transition-all); }

/* === FLEXBOX === */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-row { flex-direction: row; }
.flex-wrap { flex-wrap: wrap; }
.items-center { align-items: center; }
.items-start { align-items: flex-start; }
.items-end { align-items: flex-end; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.justify-end { justify-content: flex-end; }
.flex-1 { flex: 1; }
.flex-shrink-0 { flex-shrink: 0; }

/* === GRID === */
.grid { display: grid; }
.grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
.grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
.grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
.grid-cols-5 { grid-template-columns: repeat(5, 1fr); }

/* === OVERFLOW === */
.overflow-hidden { overflow: hidden; }
.overflow-auto { overflow: auto; }
.overflow-x-auto { overflow-x: auto; }
.overflow-y-auto { overflow-y: auto; }

/* === POSITION === */
.relative { position: relative; }
.absolute { position: absolute; }
.fixed { position: fixed; }
.sticky { position: sticky; }

/* === Z-INDEX === */
.z-header { z-index: var(--z-header); }
.z-player { z-index: var(--z-player); }
.z-modal { z-index: var(--z-modal); }
.z-overlay { z-index: var(--z-overlay); }
.z-dropdown { z-index: var(--z-dropdown); }
.z-toast { z-index: var(--z-toast); }
.z-tooltip { z-index: var(--z-tooltip); }
.z-max { z-index: var(--z-max); }
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| CSS Variables | 200+ |
| Utility Classes | 60+ |
| Themes | 3 |
| Platforms | 4 |
| Media Queries | 3 |

---

*CSS Design Tokens v1.0.0 — CoreMusic Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-17*
*Mode: Red Team · Human Mode · Truth Mode*
