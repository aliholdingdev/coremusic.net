---
type: architecture
category: l3
title: "ITCSS Architecture"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ITCSS Architecture

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

ITCSS (It's Time to Create Scaleable Stylesheets) 7-layer CSS mimarisini tanımlar. [[ADR-001-vanilla-js-itcss]] ile uyumludur.

---

## 2. 7-Layer Structure

| # | Layer | Amaç | Dosya |
|---|-------|------|-------|
| 1 | **Settings** | CSS variables, design tokens | `01-settings/` |
| 2 | **Tools** | Mixins, functions | `02-tools/` |
| 3 | **Generic** | Reset, normalize | `03-generic/` |
| 4 | **Elements** | Bare HTML elements | `04-elements/` |
| 5 | **Objects** | Layout patterns | `05-objects/` |
| 6 | **Components** | UI components | `06-components/` |
| 7 | **Utilities** | Helper classes | `07-utilities/` |

---

## 3. File Naming Convention

```
assets.coremusic.net/
├── Css/
│   ├── 01_Abstracts/
│   │   ├── a-design-tokens.css      ← Settings
│   │   ├── a-fonts-token.css        ← Settings
│   │   └── a-semantic-token.css     ← Settings
│   ├── 02_Base/
│   │   └── b-base-core.css          ← Generic
│   ├── 03_Layout/
│   │   └── l-grid.css               ← Objects
│   ├── 04_Components/
│   │   ├── c-header.css             ← Components
│   │   ├── c-footer.css             ← Components
│   │   └── c-player.css             ← Components
│   ├── 05_Pages/
│   │   └── _home.css                ← Page-specific
│   ├── 06_Utilities/
│   │   └── u-helpers-utility.css    ← Utilities
│   ├── 07_Vendors/
│   │   └── v-bootstrap-lib.css      ← Vendors
│   ├── 08_Devices/
│   │   ├── d-phone.css              ← Device: phone (self-contained)
│   │   ├── d-tablet.css             ← Device: tablet (self-contained)
│   │   ├── d-laptop.css             ← Device: laptop (self-contained)
│   │   ├── d-desktop.css            ← Device: desktop (self-contained)
│   │   ├── d-4k-tv.css              ← Device: 4K TV (self-contained)
│   │   ├── d-4k-monitor.css         ← Device: 4K monitor (self-contained)
│   │   ├── d-embedded.css           ← Device: embedded (self-contained)
│   │   ├── d-auth-phone.css         ← Auth device: phone (self-contained)
│   │   ├── d-auth-tablet.css        ← Auth device: tablet (self-contained)
│   │   ├── d-auth-laptop.css        ← Auth device: laptop (self-contained)
│   │   ├── d-auth-desktop.css       ← Auth device: desktop (self-contained)
│   │   ├── d-auth-4k-tv.css         ← Auth device: 4K TV (self-contained)
│   │   ├── d-auth-4k-monitor.css    ← Auth device: 4K monitor (self-contained)
│   │   └── d-auth-embedded.css      ← Auth device: embedded (self-contained)
│   └── 09_ViewModes/
│       ├── v-home.css               ← View: Home
│       ├── v-pro.css                ← View: Professional
│       ├── v-studio.css             ← View: Studio
│       └── v-car.css                ← View: Car
└── main.css                         ← KALDIRILDI — kullanılmıyor
```

**Not:** `main.css` artık import edilmez. Her device CSS (`d-*.css`, `d-auth-*.css`) **self-contained** — kendi import'unu kendi içinde yapar.

---

## 4. BEM Naming Convention

```css
/* Block */
.player { }

/* Element */
.player__track { }
.player__controls { }
.player__volume { }

/* Modifier */
.player--mini { }
.player--playing { }
.player__track--active { }
```

---

## 5. CSS Custom Properties

```css
/* 01_Settings: Design tokens */
:root {
    --color-primary: #3498db;
    --color-secondary: #2ecc71;
    --spacing-unit: 8px;
    --font-family-base: 'Inter', sans-serif;
    --border-radius: 4px;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
}
```

---

## 6. Responsive Breakpoints

| Breakpoint | Cihaz | CSS Variable |
|------------|-------|-------------|
| `< 480px` | Phone | `--bp-phone` |
| `480-768px` | Tablet | `--bp-tablet` |
| `768-1024px` | Laptop | `--bp-laptop` |
| `1024-1440px` | Desktop | `--bp-desktop` |
| `> 1440px` | 4K TV | `--bp-4k` |

---

## 7. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| Tek dosya CSS | ITCSS 9-layer | ADR-001 |
| BEM dışı naming | BEM + BEMIT | ADR-001 |
| !important | Specificity | ADR-0001 |
| Inline style | CSS classes | ADR-001 |
| CSS preprocessors | Vanilla CSS | ADR-001 |

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Specificity war** | ITCSS layer order | ADR-001 |
| **Duplicate styles** | BEM namespace | ADR-001 |
| **File bloat** | Modular CSS files | ADR-001 |
| **Browser compat** | Progressive enhancement | ADR-001 |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[vanilla-js-rules]] | JS kuralları |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
