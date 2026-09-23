---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Platform Tokens (45-Tier)"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/platform-tokens.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md · .ai/ui-design/tokens/design-tokens-master.md"
---

# CoreMusic — Platform Tokens (45-Tier)

**Zorunlu Bağlantılar:** [[design-tokens-master]] · [[00-device-matrix]] · [[component-tokens]]

---

## 1. Amaç

45 cihaz katmanı için **platform bazlı token değerlerinin** tek kaynağıdır. Her tier için header/footer yüksekliği, sidebar genişliği, dokunma hedefi ve ölçek değerleri burada tanımlanır.

---

## 2. Tier Bazlı Layout Token'ları

### T01: Phone HD (≤480px)

```css
@media (max-width: 480px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 80px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-3);
    --cm-card-padding: var(--cm-space-3);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 48px;
    --cm-font-scale: 0.875;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 0.875;
    --cm-show-sidebar: none;
    --cm-show-volume: none;
    --cm-show-seekbar: none;
    --cm-nav-items: 3;
  }
}
```

### T02: Phone FHD (≤767px)

```css
@media (max-width: 767px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 80px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-3);
    --cm-card-padding: var(--cm-space-3);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 48px;
    --cm-font-scale: 0.9375;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 0.9375;
    --cm-show-sidebar: none;
    --cm-show-volume: none;
    --cm-show-seekbar: none;
    --cm-nav-items: 3;
  }
}
```

### T03: Phone QHD (≤767px + high DPI)

```css
@media (max-width: 767px) and (min-resolution: 2dppx) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 84px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-3-5);
    --cm-card-padding: var(--cm-space-3-5);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 3;
  }
}
```

### T04: Phone 4K (≤767px + extreme DPI)

```css
@media (max-width: 767px) and (min-resolution: 3.5dppx) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 88px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-4);
    --cm-card-padding: var(--cm-space-4);
    --cm-card-gap: var(--cm-space-3-5);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 3;
  }
}
```

### T05: Tablet Small (≤820px)

```css
@media (max-width: 820px) and (min-width: 481px) {
  :root {
    --cm-header-h: 56px;
    --cm-footer-h: 96px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-4);
    --cm-card-padding: var(--cm-space-4);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 5;
  }
}
```

### T06: Tablet Large (≤1024px)

```css
@media (max-width: 1024px) and (min-width: 821px) {
  :root {
    --cm-header-h: 60px;
    --cm-footer-h: 96px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-5);
    --cm-card-padding: var(--cm-space-5);
    --cm-card-gap: var(--cm-space-4);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 5;
  }
}
```

### T07: Embedded 1024×600 (RPi5 7")

```css
@media (max-width: 1024px) and (max-height: 600px) {
  :root {
    --cm-header-h: 60px;
    --cm-footer-h: 90px;
    --cm-sidebar-w: 0;
    --cm-content-h: 450px;
    --cm-content-padding: var(--cm-space-4);
    --cm-card-padding: var(--cm-space-3);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 4;
    --cm-show-welcome: block;
  }
}
```

### T08: Embedded 1280×800 (RPi5 10")

```css
@media (max-width: 1280px) and (max-height: 800px) {
  :root {
    --cm-header-h: 64px;
    --cm-footer-h: 96px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-5);
    --cm-card-padding: var(--cm-space-4);
    --cm-card-gap: var(--cm-space-4);
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 5;
  }
}
```

### T09: Laptop 1366×768

```css
@media (min-width: 1366px) and (max-height: 768px) {
  :root {
    --cm-header-h: 64px;
    --cm-footer-h: 100px;
    --cm-sidebar-w: 220px;
    --cm-content-padding: var(--cm-space-5);
    --cm-card-padding: var(--cm-space-4);
    --cm-card-gap: var(--cm-space-4);
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T10: Laptop FHD (1920×1080)

```css
@media (min-width: 1920px) and (max-height: 1080px) {
  :root {
    --cm-header-h: 68px;
    --cm-footer-h: 104px;
    --cm-sidebar-w: 240px;
    --cm-content-padding: var(--cm-space-6);
    --cm-card-padding: var(--cm-space-5);
    --cm-card-gap: var(--cm-space-4);
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T11: Desktop HD (2560×1440)

```css
@media (min-width: 2560px) and (max-height: 1440px) {
  :root {
    --cm-header-h: 70px;
    --cm-footer-h: 108px;
    --cm-sidebar-w: 260px;
    --cm-content-padding: var(--cm-space-7);
    --cm-card-padding: var(--cm-space-6);
    --cm-card-gap: var(--cm-space-5);
    --cm-touch-target: 28px;
    --cm-font-scale: 1;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T12: Desktop 4K (3840×2160)

```css
@media (min-width: 3840px) {
  :root {
    --cm-header-h: 80px;
    --cm-footer-h: 120px;
    --cm-sidebar-w: 300px;
    --cm-content-padding: var(--cm-space-8);
    --cm-card-padding: var(--cm-space-7);
    --cm-card-gap: var(--cm-space-6);
    --cm-touch-target: 24px;
    --cm-font-scale: 1.25;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1.25;
    --cm-nav-items: 8;
  }
}
```

### T13: Ultrawide 34" (3440×1440)

```css
@media (min-width: 3440px) and (max-height: 1440px) {
  :root {
    --cm-header-h: 72px;
    --cm-footer-h: 112px;
    --cm-sidebar-w: 280px;
    --cm-content-padding: var(--cm-space-7);
    --cm-card-padding: var(--cm-space-6);
    --cm-card-gap: var(--cm-space-5);
    --cm-touch-target: 28px;
    --cm-font-scale: 1;
    --cm-grid-cols: 5;
    --cm-widget-grid-cols: 5;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T14: Ultrawide 49" (5120×1440)

```css
@media (min-width: 5120px) {
  :root {
    --cm-header-h: 76px;
    --cm-footer-h: 116px;
    --cm-sidebar-w: 300px;
    --cm-content-padding: var(--cm-space-8);
    --cm-card-padding: var(--cm-space-6);
    --cm-card-gap: var(--cm-space-6);
    --cm-touch-target: 24px;
    --cm-font-scale: 1.125;
    --cm-grid-cols: 6;
    --cm-widget-grid-cols: 6;
    --cm-border-radius-scale: 1.125;
    --cm-nav-items: 8;
  }
}
```

### T15: Smart TV 43" FHD

```css
@media (min-width: 1920px) and (pointer: coarse) {
  :root {
    --cm-header-h: 80px;
    --cm-footer-h: 120px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-8);
    --cm-card-padding: var(--cm-space-6);
    --cm-card-gap: var(--cm-space-5);
    --cm-touch-target: 80px;
    --cm-font-scale: 1.5;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1.5;
    --cm-nav-items: 7;
  }
}
```

### T16: Smart TV 55" 4K

```css
@media (min-width: 3840px) and (pointer: coarse) {
  :root {
    --cm-header-h: 88px;
    --cm-footer-h: 130px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-10);
    --cm-card-padding: var(--cm-space-8);
    --cm-card-gap: var(--cm-space-6);
    --cm-touch-target: 96px;
    --cm-font-scale: 1.75;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1.75;
    --cm-nav-items: 7;
  }
}
```

### T17: Smart TV 65" 4K

```css
@media (min-width: 3840px) and (pointer: coarse) and (min-height: 2160px) {
  :root {
    --cm-header-h: 96px;
    --cm-footer-h: 140px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-12);
    --cm-card-padding: var(--cm-space-8);
    --cm-card-gap: var(--cm-space-7);
    --cm-touch-target: 104px;
    --cm-font-scale: 2;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 2;
    --cm-nav-items: 7;
  }
}
```

### T18: Smart TV 77" 8K

```css
@media (min-width: 7680px) and (pointer: coarse) {
  :root {
    --cm-header-h: 104px;
    --cm-footer-h: 150px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-14);
    --cm-card-padding: var(--cm-space-10);
    --cm-card-gap: var(--cm-space-8);
    --cm-touch-target: 120px;
    --cm-font-scale: 2.5;
    --cm-grid-cols: 5;
    --cm-widget-grid-cols: 5;
    --cm-border-radius-scale: 2.5;
    --cm-nav-items: 7;
  }
}
```

### T19: Car (Android Auto / CarPlay)

```css
[data-device="car"] {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 100px;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-4);
    --cm-card-padding: var(--cm-space-3);
    --cm-card-gap: var(--cm-space-3);
    --cm-touch-target: 80px;
    --cm-font-scale: 1.125;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1.5;
    --cm-nav-items: 4;
    --cm-show-volume: block;
    --cm-show-seekbar: block;
  }
}
```

### T20: Smart Watch 40mm

```css
@media (max-width: 192px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 0;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-2);
    --cm-card-padding: var(--cm-space-2);
    --cm-card-gap: var(--cm-space-1);
    --cm-touch-target: 44px;
    --cm-font-scale: 0.75;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 0.75;
    --cm-nav-items: 2;
  }
}
```

### T21: Smart Watch 45mm

```css
@media (min-width: 193px) and (max-width: 205px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 0;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-2);
    --cm-card-padding: var(--cm-space-2);
    --cm-card-gap: var(--cm-space-1-5);
    --cm-touch-target: 44px;
    --cm-font-scale: 0.8125;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 0.8125;
    --cm-nav-items: 2;
  }
}
```

### T22: Smart Watch Ultra 49mm

```css
@media (min-width: 206px) and (max-width: 218px) {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 0;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-2-5);
    --cm-card-padding: var(--cm-space-2-5);
    --cm-card-gap: var(--cm-space-2);
    --cm-touch-target: 44px;
    --cm-font-scale: 0.875;
    --cm-grid-cols: 1;
    --cm-widget-grid-cols: 1;
    --cm-border-radius-scale: 0.875;
    --cm-nav-items: 2;
  }
}
```

### T23: Console PS5 (1920×1080, TV)

```css
[data-device="console"][data-platform="ps5"] {
  :root {
    --cm-header-h: 80px;
    --cm-footer-h: 120px;
    --cm-sidebar-w: 0;
    --cm-touch-target: 80px;
    --cm-font-scale: 1.5;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1.5;
    --cm-nav-items: 6;
  }
}
```

### T24: Console Xbox Series X (3840×2160, TV)

```css
[data-device="console"][data-platform="xbox"] {
  :root {
    --cm-header-h: 88px;
    --cm-footer-h: 130px;
    --cm-sidebar-w: 0;
    --cm-touch-target: 96px;
    --cm-font-scale: 1.75;
    --cm-grid-cols: 4;
    --cm-widget-grid-cols: 4;
    --cm-border-radius-scale: 1.75;
    --cm-nav-items: 6;
  }
}
```

### T25: Console Nintendo Switch (1280×720, handheld)

```css
[data-device="console"][data-platform="switch"] {
  :root {
    --cm-header-h: 56px;
    --cm-footer-h: 88px;
    --cm-sidebar-w: 0;
    --cm-touch-target: 56px;
    --cm-font-scale: 1;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 5;
  }
}
```

### T26: Console Steam Deck (1280×800, handheld)

```css
[data-device="console"][data-platform="steamdeck"] {
  :root {
    --cm-header-h: 56px;
    --cm-footer-h: 88px;
    --cm-sidebar-w: 0;
    --cm-touch-target: 48px;
    --cm-font-scale: 1;
    --cm-grid-cols: 2;
    --cm-widget-grid-cols: 2;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 5;
  }
}
```

### T27: Desktop App Electron

```css
[data-device="desktop-app"][data-runtime="electron"] {
  :root {
    --cm-header-h: 32px;
    --cm-footer-h: 104px;
    --cm-sidebar-w: 240px;
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T28: Desktop App Tauri

```css
[data-device="desktop-app"][data-runtime="tauri"] {
  :root {
    --cm-header-h: 36px;
    --cm-footer-h: 104px;
    --cm-sidebar-w: 240px;
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T29: Desktop App PWA

```css
[data-device="desktop-app"][data-runtime="pwa"] {
  :root {
    --cm-header-h: 48px;
    --cm-footer-h: 104px;
    --cm-sidebar-w: 240px;
    --cm-touch-target: 32px;
    --cm-font-scale: 1;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1;
    --cm-nav-items: 8;
  }
}
```

### T30: AR/VR Meta Quest 3

```css
[data-device="ar-vr"][data-platform="quest3"] {
  :root {
    --cm-header-h: 0;
    --cm-footer-h: 0;
    --cm-sidebar-w: 0;
    --cm-content-padding: var(--cm-space-8);
    --cm-card-padding: var(--cm-space-6);
    --cm-card-gap: var(--cm-space-6);
    --cm-touch-target: 64px;
    --cm-font-scale: 1.25;
    --cm-grid-cols: 3;
    --cm-widget-grid-cols: 3;
    --cm-border-radius-scale: 1.5;
    --cm-nav-items: 0;
  }
}
```

---

## 3. Platform Token Matrisi

| Tier | Cihaz | Header | Footer | Sidebar | Touch | Font Scale | Grid |
|------|-------|--------|--------|---------|-------|------------|------|
| T01 | Phone HD | 0 | 80px | 0 | 48px | 0.875 | 1 |
| T02 | Phone FHD | 0 | 80px | 0 | 48px | 0.9375 | 1 |
| T03 | Phone QHD | 0 | 84px | 0 | 48px | 1 | 1 |
| T04 | Phone 4K | 0 | 88px | 0 | 48px | 1 | 1 |
| T05 | Tablet Sm | 56px | 96px | 0 | 48px | 1 | 2 |
| T06 | Tablet Lg | 60px | 96px | 0 | 48px | 1 | 2 |
| T07 | Embedded 7" | 60px | 90px | 0 | 48px | 1 | 2 |
| T08 | Embedded 10" | 64px | 96px | 0 | 48px | 1 | 3 |
| T09 | Laptop 13" | 64px | 100px | 220px | 32px | 1 | 3 |
| T10 | Laptop 15" | 68px | 104px | 240px | 32px | 1 | 3 |
| T11 | Desktop HD | 70px | 108px | 260px | 28px | 1 | 4 |
| T12 | Desktop 4K | 80px | 120px | 300px | 24px | 1.25 | 4 |
| T13 | UW 34" | 72px | 112px | 280px | 28px | 1 | 5 |
| T14 | UW 49" | 76px | 116px | 300px | 24px | 1.125 | 6 |
| T15 | TV 43" | 80px | 120px | 0 | 80px | 1.5 | 4 |
| T16 | TV 55" | 88px | 130px | 0 | 96px | 1.75 | 4 |
| T17 | TV 65" | 96px | 140px | 0 | 104px | 2 | 4 |
| T18 | TV 77" | 104px | 150px | 0 | 120px | 2.5 | 5 |
| T19 | Car | 0 | 100px | 0 | 80px | 1.125 | 2 |
| T20 | Watch 40mm | 0 | 0 | 0 | 44px | 0.75 | 1 |
| T21 | Watch 45mm | 0 | 0 | 0 | 44px | 0.8125 | 1 |
| T22 | Watch Ultra | 0 | 0 | 0 | 44px | 0.875 | 1 |
| T23 | PS5 | 80px | 120px | 0 | 80px | 1.5 | 4 |
| T24 | Xbox X | 88px | 130px | 0 | 96px | 1.75 | 4 |
| T25 | Switch | 56px | 88px | 0 | 56px | 1 | 2 |
| T26 | Steam Deck | 56px | 88px | 0 | 48px | 1 | 2 |
| T27 | Electron | 32px | 104px | 240px | 32px | 1 | 3 |
| T28 | Tauri | 36px | 104px | 240px | 32px | 1 | 3 |
| T29 | PWA | 48px | 104px | 240px | 32px | 1 | 3 |
| T30 | Quest 3 | 0 | 0 | 0 | 64px | 1.25 | 3 |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Tiers | 30 (T01-T30) |
| Full Device Count | 45 (with sub-variants) |
| Token Categories | 8 (Header, Footer, Sidebar, Touch, Font, Grid, Radius, Nav) |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
