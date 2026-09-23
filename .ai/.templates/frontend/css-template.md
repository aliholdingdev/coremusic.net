---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — CSS/ITCSS Development Template"
type: css-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** ITCSS 9-layer, BEM naming, CSS Custom Properties
**Katman:** K11 (UX)
**Sorumlu Agent:** UI Designer

---

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Hardcoded pixel/değer yasak — CSS variables使用 | Kod revert edilir |
| 2 | `!important` yasak (maks. 3 istisna) | Kod revert edilir |
| 3 | Inline style yasak (PHP'de sunum kararı yok) | Kod revert edilir |
| 4 | Tek component sistemi + responsive CSS | Ayrı HTML yasak |
| 5 | Device CSS = sadece behavioral overrides | Layout CSS device'da yasak |
| 6 | BEM naming zorunlu | Kod revert edilir |
| 7 | ITCSS 9-layer sırası değişmez | Katman ihlali |

---

## 2. Dosya Yapısı

```
assets/css/
├── 01_Settings/
│   ├── _variables.css          # CSS custom properties
│   ├── _colors.css             # Renk token'ları
│   ├── _typography.css         # Tipografi token'ları
│   └── _spacing.css            # Boşluk token'ları
├── 02_Tools/
│   ├── _mixins.scss            # SCSS mixins (opsiyonel)
│   └── _functions.scss         # SCSS functions
├── 03_Generic/
│   ├── _reset.css              # CSS reset
│   └── _normalize.css          # Normalize
├── 04_Elements/
│   ├── _base.css               # Bare HTML elements
│   ├── _headings.css           # Başlık stilleri
│   └── _links.css              # Link stilleri
├── 05_Objects/
│   ├── _layout.css             # Layout patterns
│   ├── _grid.css               # Grid sistemi
│   └── _container.css          # Container
├── 06_Components/
│   ├── _header.css             # Header bileşeni
│   ├── _footer.css             # Footer bileşeni
│   ├── _player.css             # Player bileşeni
│   ├── _sidebar.css            # Sidebar bileşeni
│   └── _card.css               # Card bileşeni
├── 07_Utilities/
│   ├── _helpers.css            # Helper classes
│   └── _visibility.css         # Görünürlük
├── 08_Devices/
│   ├── d-embedded.css          # RPi5 embedded overrides
│   ├── d-desktop.css           # Desktop overrides
│   ├── d-tablet.css            # Tablet overrides
│   └── d-mobile.css            # Mobile overrides
├── 09_Themes/
│   ├── _theme-default.css      # Varsayılan tema
│   ├── _theme-dark.css         # Dark tema
│   └── _theme-glassmorphism.css # Glassmorphism tema
└── main.css                    # Ana CSS (01-07 import)
```

---

## 3. CSS Custom Properties (Design Tokens)

```css
/* 01_Settings/_variables.css */
:root {
    /* === RENKLER === */
    --color-primary: #6366f1;
    --color-primary-hover: #4f46e5;
    --color-secondary: #ec4899;
    --color-accent: #06b6d4;
    --color-success: #10b981;
    --color-warning: #f59e0b;
    --color-danger: #ef4444;

    /* === ARKAPLAN === */
    --bg-primary: #0f172a;
    --bg-secondary: #1e293b;
    --bg-tertiary: #334155;
    --bg-glass: rgba(15, 23, 42, 0.8);
    --bg-glass-blur: blur(12px);

    /* === YAZI === */
    --text-primary: #f8fafc;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;

    /* === BOŞLUKLAR === */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --space-2xl: 48px;

    /* === YÜKSEKLİKLER === */
    --header-h: 60px;
    --footer-h: 90px;
    --content-h: calc(100vh - var(--header-h) - var(--footer-h));
    --sidebar-w: 280px;

    /* === KÖŞE YUVARLAKLIKLARI === */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-full: 9999px;

    /* === GÖLGELER === */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);
    --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.4);

    /* === Z-INDEX === */
    --z-dropdown: 100;
    --z-sticky: 200;
    --z-modal: 300;
    --z-toast: 400;

    /* === TRANSITION === */
    --transition-fast: 150ms ease;
    --transition-normal: 250ms ease;
    --transition-slow: 350ms ease;

    /* === FONT === */
    --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-md: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;

    /* === TOUCH TARGET (WCAG) === */
    --touch-min: 44px;
}
```

---

## 4. Responsive Media Query

```css
/* 01_Settings/_variables.css içinde */

/* === EMBEDDED (1024x600 — RPi5) === */
@media (max-width: 1024px) {
    :root {
        --header-h: 60px;
        --footer-h: 90px;
        --sidebar-w: 0px;
    }
}

/* === TABLET (768-1024) === */
@media (min-width: 768px) and (max-width: 1024px) {
    :root {
        --sidebar-w: 0px;
    }
}

/* === MOBİL (≤767) === */
@media (max-width: 767px) {
    :root {
        --header-h: 0px;
        --footer-h: 80px;
        --sidebar-w: 0px;
    }
}

/* === DESKTOP (≥1920) === */
@media (min-width: 1920px) {
    :root {
        --header-h: 70px;
        --footer-h: 104px;
        --sidebar-w: 300px;
    }
}

/* === 4K TV (≥3840) === */
@media (min-width: 3840px) {
    :root {
        --header-h: 80px;
        --footer-h: 120px;
        --sidebar-w: 350px;
        --font-size-md: 1.25rem;
    }
}
```

---

## 5. BEM Naming Kılavuzu

```css
/* BEM Formatı: .block__element--modifier */

/* Block */
.player { }

/* Element */
.player__controls { }
.player__progress { }
.player__volume { }

/* Modifier */
.player--expanded { }
.player__controls--hidden { }
.player__progress--live { }

/* State */
.player.is-playing { }
.player.is-paused { }
.player.is-loading { }

/* Theme */
.player[data-theme="dark"] { }
.player[data-theme="glassmorphism"] { }
```

---

## 6. Component CSS Şablonu

```css
/* 06_Components/_player.css */

.player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    background: var(--bg-glass);
    backdrop-filter: var(--bg-glass-blur);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    z-index: var(--z-sticky);
    transition: height var(--transition-normal);
}

.player__controls {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    padding: var(--space-sm) var(--space-lg);
}

.player__progress {
    width: 100%;
    height: 4px;
    background: var(--bg-tertiary);
    cursor: pointer;
    position: relative;
}

.player__progress-fill {
    height: 100%;
    background: var(--color-primary);
    transition: width var(--transition-fast);
}

.player__volume {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

/* State */
.player.is-playing .player__play-icon { display: none; }
.player.is-playing .player__pause-icon { display: block; }
.player.is-paused .player__play-icon { display: block; }
.player.is-paused .player__pause-icon { display: none; }

/* Device Overrides */
@media (max-width: 767px) {
    .player {
        height: 80px;
        padding: var(--space-xs);
    }

    .player__volume { display: none; }
    .player__progress { height: 3px; }
}

@media (max-width: 1024px) and (min-height: 601px) {
    .player {
        height: var(--footer-h);
    }
}
```

---

## 7. Glassmorphism Tema

```css
/* 09_Themes/_theme-glassmorphism.css */

[data-theme="glassmorphism"] {
    --bg-glass: rgba(15, 23, 42, 0.7);
    --bg-glass-blur: blur(16px);
    --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.5);
    --border-glass: 1px solid rgba(255, 255, 255, 0.15);

    background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
}

.glass-panel {
    background: var(--bg-glass);
    backdrop-filter: var(--bg-glass-blur);
    -webkit-backdrop-filter: var(--bg-glass-blur);
    border: var(--border-glass);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-glass);
}
```

---

## 8. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-018 | Footer player vaporwave |
| ADR-044 | Cinsiyet bazlı dinamik tema |
| ADR-045 | Multi-domain view mode |

---

*CSS/ITCSS Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
