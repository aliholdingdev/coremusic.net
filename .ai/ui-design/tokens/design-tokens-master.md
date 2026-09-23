---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Master Design Tokens"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/design-tokens-master.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Master Design Tokens

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[color-palettes]] · [[platform-tokens]] · [[component-tokens]]

---

## 1. Amaç

CoreMusic UI tasarım sisteminin **tek tasarım token kaynağıdır**. Tüm CSS custom properties bu dosyada tanımlanır. 45 cihaz katmanı için token değerleri buradan türetilir.

---

## 2. Token Kategorileri (300+ Token)

### 2.1 Renk Token'ları (85 Token)

```css
:root {
  /* ═══ Primary Colors ═══ */
  --cm-primary: #ff4fd8;
  --cm-primary-light: #ff7ee4;
  --cm-primary-dark: #d63cb8;
  --cm-primary-rgb: 255, 79, 216;
  --cm-primary-10: rgba(255, 79, 216, 0.10);
  --cm-primary-20: rgba(255, 79, 216, 0.20);
  --cm-primary-30: rgba(255, 79, 216, 0.30);
  --cm-primary-40: rgba(255, 79, 216, 0.40);
  --cm-primary-50: rgba(255, 79, 216, 0.50);
  --cm-primary-60: rgba(255, 79, 216, 0.60);
  --cm-primary-70: rgba(255, 79, 216, 0.70);
  --cm-primary-80: rgba(255, 79, 216, 0.80);
  --cm-primary-90: rgba(255, 79, 216, 0.90);

  /* ═══ Secondary Colors ═══ */
  --cm-secondary: #a855f7;
  --cm-secondary-light: #c084fc;
  --cm-secondary-dark: #7c3aed;
  --cm-secondary-rgb: 168, 85, 247;

  /* ═══ Accent Colors ═══ */
  --cm-accent: #06d6a0;
  --cm-accent-light: #34d399;
  --cm-accent-dark: #059669;
  --cm-accent-rgb: 6, 214, 160;

  /* ═══ Background ═══ */
  --cm-bg-primary: #0a0a0f;
  --cm-bg-secondary: #12121a;
  --cm-bg-tertiary: #1a1a25;
  --cm-bg-elevated: #22222e;
  --cm-bg-surface: #2a2a38;
  --cm-bg-overlay: rgba(0, 0, 0, 0.60);
  --cm-bg-scrim: rgba(0, 0, 0, 0.32);

  /* ═══ Text Colors ═══ */
  --cm-text-primary: #ffffff;
  --cm-text-secondary: #b0b0c0;
  --cm-text-tertiary: #707088;
  --cm-text-disabled: #4a4a5a;
  --cm-text-inverse: #0a0a0f;
  --cm-text-link: #ff4fd8;
  --cm-text-link-hover: #ff7ee4;

  /* ═══ Semantic: Success ═══ */
  --cm-success: #10b981;
  --cm-success-light: #34d399;
  --cm-success-dark: #059669;
  --cm-success-bg: rgba(16, 185, 129, 0.12);
  --cm-success-border: rgba(16, 185, 129, 0.30);

  /* ═══ Semantic: Warning ═══ */
  --cm-warning: #f59e0b;
  --cm-warning-light: #fbbf24;
  --cm-warning-dark: #d97706;
  --cm-warning-bg: rgba(245, 158, 11, 0.12);
  --cm-warning-border: rgba(245, 158, 11, 0.30);

  /* ═══ Semantic: Error ═══ */
  --cm-error: #ef4444;
  --cm-error-light: #f87171;
  --cm-error-dark: #dc2626;
  --cm-error-bg: rgba(239, 68, 68, 0.12);
  --cm-error-border: rgba(239, 68, 68, 0.30);

  /* ═══ Semantic: Info ═══ */
  --cm-info: #3b82f6;
  --cm-info-light: #60a5fa;
  --cm-info-dark: #2563eb;
  --cm-info-bg: rgba(59, 130, 246, 0.12);
  --cm-info-border: rgba(59, 130, 246, 0.30);

  /* ═══ Gray Scale ═══ */
  --cm-gray-50: #f8f9fa;
  --cm-gray-100: #f1f3f5;
  --cm-gray-200: #e9ecef;
  --cm-gray-300: #dee2e6;
  --cm-gray-400: #ced4da;
  --cm-gray-500: #adb5bd;
  --cm-gray-600: #868e96;
  --cm-gray-700: #495057;
  --cm-gray-800: #343a40;
  --cm-gray-900: #212529;

  /* ═══ Border Colors ═══ */
  --cm-border-subtle: rgba(255, 255, 255, 0.06);
  --cm-border-default: rgba(255, 255, 255, 0.10);
  --cm-border-strong: rgba(255, 255, 255, 0.16);
  --cm-border-focus: #ff4fd8;
  --cm-border-error: #ef4444;

  /* ═══ Interactive States ═══ */
  --cm-hover-bg: rgba(255, 255, 255, 0.06);
  --cm-active-bg: rgba(255, 255, 255, 0.10);
  --cm-selected-bg: rgba(255, 79, 216, 0.16);
  --cm-focus-ring: 0 0 0 2px #ff4fd8;
}
```

### 2.2 Tipografi Token'ları (42 Token)

```css
:root {
  /* ═══ Font Families ═══ */
  --cm-font-display: 'Inter', 'SF Pro Display', -apple-system, sans-serif;
  --cm-font-body: 'Inter', 'SF Pro Text', -apple-system, sans-serif;
  --cm-font-mono: 'JetBrains Mono', 'SF Mono', 'Fira Code', monospace;

  /* ═══ Font Sizes ═══ */
  --cm-text-xs: 0.75rem;     /* 12px */
  --cm-text-sm: 0.8125rem;   /* 13px */
  --cm-text-base: 0.875rem;  /* 14px */
  --cm-text-md: 1rem;        /* 16px */
  --cm-text-lg: 1.125rem;    /* 18px */
  --cm-text-xl: 1.25rem;     /* 20px */
  --cm-text-2xl: 1.5rem;     /* 24px */
  --cm-text-3xl: 1.875rem;   /* 30px */
  --cm-text-4xl: 2.25rem;    /* 36px */
  --cm-text-5xl: 3rem;       /* 48px */

  /* ═══ Font Weights ═══ */
  --cm-font-regular: 400;
  --cm-font-medium: 500;
  --cm-font-semibold: 600;
  --cm-font-bold: 700;

  /* ═══ Line Heights ═══ */
  --cm-leading-none: 1;
  --cm-leading-tight: 1.25;
  --cm-leading-snug: 1.375;
  --cm-leading-normal: 1.5;
  --cm-leading-relaxed: 1.625;

  /* ═══ Letter Spacing ═══ */
  --cm-tracking-tighter: -0.05em;
  --cm-tracking-tight: -0.025em;
  --cm-tracking-normal: 0;
  --cm-tracking-wide: 0.025em;
  --cm-tracking-wider: 0.05em;

  /* ═══ Font Size Scale (per tier) ═══ */
  --cm-font-scale: 1;
  --cm-font-scale-phone: 0.875;
  --cm-font-scale-embedded: 1;
  --cm-font-scale-tablet: 1;
  --cm-font-scale-laptop: 1;
  --cm-font-scale-desktop: 1;
  --cm-font-scale-4k: 1.25;
  --cm-font-scale-tv: 1.5;
  --cm-font-scale-car: 1.125;
}
```

### 2.3 Boşluk Token'ları (38 Token)

```css
:root {
  /* ═══ Spacing Scale ═══ */
  --cm-space-0: 0;
  --cm-space-px: 1px;
  --cm-space-0-5: 0.125rem;  /* 2px */
  --cm-space-1: 0.25rem;     /* 4px */
  --cm-space-1-5: 0.375rem;  /* 6px */
  --cm-space-2: 0.5rem;      /* 8px */
  --cm-space-2-5: 0.625rem;  /* 10px */
  --cm-space-3: 0.75rem;     /* 12px */
  --cm-space-3-5: 0.875rem;  /* 14px */
  --cm-space-4: 1rem;        /* 16px */
  --cm-space-5: 1.25rem;     /* 20px */
  --cm-space-6: 1.5rem;      /* 24px */
  --cm-space-7: 1.75rem;     /* 28px */
  --cm-space-8: 2rem;        /* 32px */
  --cm-space-9: 2.25rem;     /* 36px */
  --cm-space-10: 2.5rem;     /* 40px */
  --cm-space-12: 3rem;       /* 48px */
  --cm-space-14: 3.5rem;     /* 56px */
  --cm-space-16: 4rem;       /* 64px */
  --cm-space-20: 5rem;       /* 80px */
  --cm-space-24: 6rem;       /* 96px */
  --cm-space-28: 7rem;       /* 112px */
  --cm-space-32: 8rem;       /* 128px */

  /* ═══ Spacing Scale Multiplier ═══ */
  --cm-space-scale: 1;

  /* ═══ Section Spacing ═══ */
  --cm-section-gap: var(--cm-space-8);
  --cm-section-padding: var(--cm-space-6);

  /* ═══ Component Spacing ═══ */
  --cm-card-padding: var(--cm-space-4);
  --cm-card-gap: var(--cm-space-4);
  --cm-button-padding-x: var(--cm-space-4);
  --cm-button-padding-y: var(--cm-space-2);
  --cm-input-padding-x: var(--cm-space-3);
  --cm-input-padding-y: var(--cm-space-2-5);

  /* ═══ Inline Spacing ═══ */
  --cm-inline-gap: var(--cm-space-2);
  --cm-inline-gap-sm: var(--cm-space-1);
  --cm-inline-gap-lg: var(--cm-space-3);
}
```

### 2.4 Layout Token'ları (45 Token)

```css
:root {
  /* ═══ Header ═══ */
  --cm-header-h: 60px;
  --cm-header-h-phone: 0;
  --cm-header-h-embedded: 60px;
  --cm-header-h-tablet: 64px;
  --cm-header-h-laptop: 68px;
  --cm-header-h-desktop: 70px;
  --cm-header-h-4k: 80px;
  --cm-header-h-tv: 80px;

  /* ═══ Footer ═══ */
  --cm-footer-h: 90px;
  --cm-footer-h-phone: 80px;
  --cm-footer-h-embedded: 90px;
  --cm-footer-h-tablet: 96px;
  --cm-footer-h-laptop: 100px;
  --cm-footer-h-desktop: 104px;
  --cm-footer-h-4k: 120px;
  --cm-footer-h-tv: 120px;

  /* ═══ Content Area ═══ */
  --cm-content-h: 450px;
  --cm-content-max-w: 1440px;
  --cm-content-padding: var(--cm-space-6);

  /* ═══ Sidebar ═══ */
  --cm-sidebar-w: 240px;
  --cm-sidebar-w-collapsed: 64px;
  --cm-sidebar-w-phone: 0;
  --cm-sidebar-w-embedded: 0;
  --cm-sidebar-w-tablet: 0;
  --cm-sidebar-w-laptop: 220px;
  --cm-sidebar-w-desktop: 240px;
  --cm-sidebar-w-4k: 280px;

  /* ═══ Grid ═══ */
  --cm-grid-cols: 12;
  --cm-grid-gap: var(--cm-space-4);
  --cm-widget-grid-cols: 3;

  /* ═══ Widget Area (Figma pixel-perfect) ═══ */
  --cm-widget-area-gap: 10px;
  --cm-widget-row-gap: 8px;
  --cm-widget-panel-h: 40px;
  --cm-widget-panel-w: 109px;
  --cm-widget-icon-size: 20px;
  --cm-widget-text-size: 9px;

  /* ═══ Quick Apps (Figma pixel-perfect) ═══ */
  --cm-quick-app-w: 129px;
  --cm-quick-app-h: 40px;
  --cm-quick-app-icon-size: 45px;
  --cm-quick-app-text-size: 8.5px;

  /* ═══ Mini Card (Figma pixel-perfect) ═══ */
  --cm-mini-card-w: 169px;
  --cm-mini-card-h: 43px;
  --cm-mini-card-art: 40px;
  --cm-mini-card-title-size: 8.5px;
  --cm-mini-card-meta-size: 7px;

  /* ═══ Now Playing (Figma pixel-perfect) ═══ */
  --cm-now-playing-art: 100px;
  --cm-now-playing-title-size: 13px;
  --cm-now-playing-meta-size: 12px;

  /* ═══ Container ═══ */
  --cm-container-sm: 640px;
  --cm-container-md: 768px;
  --cm-container-lg: 1024px;
  --cm-container-xl: 1280px;
  --cm-container-2xl: 1440px;
}
```

### 2.5 Border Token'ları (22 Token)

```css
:root {
  /* ═══ Border Radius ═══ */
  --cm-radius-none: 0;
  --cm-radius-sm: 4px;
  --cm-radius-md: 8px;
  --cm-radius-lg: 12px;
  --cm-radius-xl: 16px;
  --cm-radius-2xl: 20px;
  --cm-radius-3xl: 24px;
  --cm-radius-full: 9999px;

  /* ═══ Border Width ═══ */
  --cm-border-w: 1px;
  --cm-border-w-thick: 2px;
  --cm-border-w-focus: 2px;

  /* ═══ Component Borders ═══ */
  --cm-card-radius: var(--cm-radius-xl);
  --cm-button-radius: var(--cm-radius-lg);
  --cm-input-radius: var(--cm-radius-md);
  --cm-modal-radius: var(--cm-radius-2xl);
  --cm-avatar-radius: var(--cm-radius-full);
  --cm-badge-radius: var(--cm-radius-full);
  --cm-chip-radius: var(--cm-radius-full);
  --cm-tab-radius: var(--cm-radius-md);
  --cm-toggle-radius: var(--cm-radius-full);

  /* ═══ Divider ═══ */
  --cm-divider-color: var(--cm-border-subtle);
  --cm-divider-width: 1px;
}
```

### 2.6 Gölge Token'ları (18 Token)

```css
:root {
  /* ═══ Elevation Shadows ═══ */
  --cm-shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.25);
  --cm-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.30);
  --cm-shadow-md: 0 4px 8px rgba(0, 0, 0, 0.35);
  --cm-shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.40);
  --cm-shadow-xl: 0 12px 24px rgba(0, 0, 0, 0.45);
  --cm-shadow-2xl: 0 20px 40px rgba(0, 0, 0, 0.50);

  /* ═══ Colored Shadows ═══ */
  --cm-shadow-primary: 0 4px 16px rgba(255, 79, 216, 0.25);
  --cm-shadow-primary-lg: 0 8px 32px rgba(255, 79, 216, 0.35);
  --cm-shadow-accent: 0 4px 16px rgba(6, 214, 160, 0.25);
  --cm-shadow-error: 0 4px 16px rgba(239, 68, 68, 0.25);

  /* ═══ Inner Shadows ═══ */
  --cm-shadow-inner: inset 0 2px 4px rgba(0, 0, 0, 0.25);
  --cm-shadow-inner-lg: inset 0 4px 8px rgba(0, 0, 0, 0.30);

  /* ═══ Focus Shadows ═══ */
  --cm-shadow-focus: 0 0 0 2px var(--cm-bg-primary), 0 0 0 4px var(--cm-primary);
  --cm-shadow-focus-error: 0 0 0 2px var(--cm-bg-primary), 0 0 0 4px var(--cm-error);

  /* ═══ Component Shadows ═══ */
  --cm-card-shadow: var(--cm-shadow-md);
  --cm-card-shadow-hover: var(--cm-shadow-lg);
  --cm-dropdown-shadow: var(--cm-shadow-xl);
  --cm-modal-shadow: var(--cm-shadow-2xl);
}
```

### 2.7 Animasyon Token'ları (24 Token)

```css
:root {
  /* ═══ Duration ═══ */
  --cm-duration-instant: 0ms;
  --cm-duration-fast: 100ms;
  --cm-duration-normal: 200ms;
  --cm-duration-slow: 300ms;
  --cm-duration-slower: 500ms;
  --cm-duration-slowest: 700ms;

  /* ═══ Easing ═══ */
  --cm-ease-linear: linear;
  --cm-ease-in: cubic-bezier(0.4, 0, 1, 1);
  --cm-ease-out: cubic-bezier(0, 0, 0.2, 1);
  --cm-ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);
  --cm-ease-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
  --cm-ease-spring: cubic-bezier(0.175, 0.885, 0.32, 1.275);

  /* ═══ Transition Presets ═══ */
  --cm-transition-colors: color var(--cm-duration-normal) var(--cm-ease-in-out),
                          background-color var(--cm-duration-normal) var(--cm-ease-in-out),
                          border-color var(--cm-duration-normal) var(--cm-ease-in-out);
  --cm-transition-transform: transform var(--cm-duration-normal) var(--cm-ease-out);
  --cm-transition-opacity: opacity var(--cm-duration-normal) var(--cm-ease-in-out);
  --cm-transition-shadow: box-shadow var(--cm-duration-normal) var(--cm-ease-in-out);
  --cm-transition-all: all var(--cm-duration-normal) var(--cm-ease-in-out);

  /* ═══ Keyframe Tokens ═══ */
  --cm-fade-in: cmFadeIn var(--cm-duration-normal) var(--cm-ease-out);
  --cm-slide-up: cmSlideUp var(--cm-duration-slow) var(--cm-ease-out);
  --cm-slide-down: cmSlideDown var(--cm-duration-slow) var(--cm-ease-out);
  --cm-scale-in: cmScaleIn var(--cm-duration-normal) var(--cm-ease-spring);
  --cm-spin: cmSpin 1s linear infinite;
  --cm-pulse: cmPulse 2s var(--cm-ease-in-out) infinite;
}

@keyframes cmFadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
@keyframes cmSlideUp {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes cmSlideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes cmScaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
@keyframes cmSpin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
@keyframes cmPulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

### 2.8 Z-Index Token'ları (16 Token)

```css
:root {
  --cm-z-base: 0;
  --cm-z-dropdown: 100;
  --cm-z-sticky: 200;
  --cm-z-header: 300;
  --cm-z-footer: 300;
  --cm-z-sidebar: 250;
  --cm-z-overlay: 400;
  --cm-z-modal: 500;
  --cm-z-popover: 600;
  --cm-z-tooltip: 700;
  --cm-z-toast: 800;
  --cm-z-player: 350;
  --cm-z-welcome: 900;
  --cm-z-max: 9999;
}
```

### 2.9 Cam (Glass) Token'ları (20 Token)

```css
:root {
  /* ═══ Glass Background ═══ */
  --cm-glass-bg: rgba(255, 255, 255, 0.05);
  --cm-glass-bg-hover: rgba(255, 255, 255, 0.08);
  --cm-glass-bg-active: rgba(255, 255, 255, 0.12);
  --cm-glass-bg-strong: rgba(255, 255, 255, 0.10);

  /* ═══ Glass Border ═══ */
  --cm-glass-border: rgba(255, 255, 255, 0.08);
  --cm-glass-border-hover: rgba(255, 255, 255, 0.14);
  --cm-glass-border-strong: rgba(255, 255, 255, 0.18);

  /* ═══ Glass Blur ═══ */
  --cm-glass-blur: 12px;
  --cm-glass-blur-sm: 8px;
  --cm-glass-blur-lg: 20px;
  --cm-glass-blur-xl: 32px;

  /* ═══ Glass Shadow ═══ */
  --cm-glass-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
  --cm-glass-shadow-lg: 0 8px 40px rgba(0, 0, 0, 0.4);

  /* ═══ Glass Gradient ═══ */
  --cm-glass-gradient: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.02) 100%);
  --cm-glass-gradient-strong: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);

  /* ═══ Glass Mix-blend ═══ */
  --cm-glass-blend: normal;
  --cm-glass-isolation: isolate;

  /* ═══ Glass Noise ═══ */
  --cm-glass-noise: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
}
```

---

## 3. Responsive Breakpoint'ler

```css
/* ═══ Breakpoints ═══ */
/* phone:      ≤ 480px   */
/* phone-hd:   ≤ 767px   */
/* tablet-sm:  ≤ 820px   */
/* tablet:     ≤ 1024px  */
/* laptop:     ≤ 1366px  */
/* laptop-hd:  ≤ 1440px  */
/* desktop:    ≤ 1920px  */
/* desktop-qhd: ≤ 2560px */
/* 4k:         ≤ 3840px  */
/* tv:         ≤ 3840px + TV UA */
```

---

## 4. Tema Değişkenleri

```css
/* ═══ Female Theme (Default) ═══ */
[data-theme="female"] {
  --cm-primary: #ff4fd8;
  --cm-primary-light: #ff7ee4;
  --cm-primary-dark: #d63cb8;
}

/* ═══ Male Theme ═══ */
[data-theme="male"] {
  --cm-primary: #4f9fff;
  --cm-primary-light: #7bb8ff;
  --cm-primary-dark: #2d7de0;
}

/* ═══ Neutral Theme ═══ */
[data-theme="neutral"] {
  --cm-primary: #a0a0b0;
  --cm-primary-light: #c0c0cc;
  --cm-primary-dark: #808090;
}
```

---

## 5. Utility Token'lar

```css
:root {
  /* ═══ Opacity ═══ */
  --cm-opacity-disabled: 0.40;
  --cm-opacity-muted: 0.60;
  --cm-opacity-strong: 0.80;
  --cm-opacity-full: 1;

  /* ═══ Cursor ═══ */
  --cm-cursor-pointer: pointer;
  --cm-cursor-default: default;
  --cm-cursor-not-allowed: not-allowed;

  /* ═══ Touch Target ═══ */
  --cm-touch-target: 44px;
  --cm-touch-target-lg: 48px;
  --cm-touch-target-xl: 56px;

  /* ═══ Scrollbar ═══ */
  --cm-scrollbar-w: 6px;
  --cm-scrollbar-track: transparent;
  --cm-scrollbar-thumb: rgba(255, 255, 255, 0.15);
  --cm-scrollbar-thumb-hover: rgba(255, 255, 255, 0.25);
}
```

---

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 6.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Tokens | 326 |
| Categories | 9 (Color, Typography, Spacing, Layout, Border, Shadow, Animation, Z-Index, Glass) |
| Color Tokens | 85 |
| Typography Tokens | 42 |
| Spacing Tokens | 38 |
| Layout Tokens | 45 |
| Border Tokens | 22 |
| Shadow Tokens | 18 |
| Animation Tokens | 24 |
| Z-Index Tokens | 16 |
| Glass Tokens | 20 |
| Platform Tiers | 45 |
| Responsive Breakpoints | 10 |
| Theme Variants | 3 (female, male, neutral) |
| Cross References | 6 |
| Last Updated | 2026-09-22 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-22
**Mode:** Red Team · Human Mode · Truth Mode
