# CSS Architecture: ITCSS + CSS Grid

## ITCSS (Inverted Triangle CSS) — 7 Layers

Layers are organized by **specificity** (increasing downwards) and **reusability** (decreasing downwards).

### Layer 1: Abstracts (Variables, Mixins, Functions)

**Purpose:** No CSS output, only preprocessor/variable definitions

```css
/* 01_Abstracts/_variables.css */
:root {
  --color-primary: #9d4edd;
  --color-accent: #ff4fd8;
  --font-family-base: "System UI", -apple-system, sans-serif;
  --font-size-base: 16px;
  --space-unit: 8px;
  --space-sm: calc(var(--space-unit) * 1);      /* 8px */
  --space-md: calc(var(--space-unit) * 2);      /* 16px */
  --space-lg: calc(var(--space-unit) * 3);      /* 24px */
  --transition: 200ms ease-out;
}

/* 01_Abstracts/_breakpoints.css */
@media (min-width: 600px) { /* Mobile Wide */ }
@media (min-width: 768px) { /* Tablet */ }
@media (min-width: 1024px) { /* Tablet Wide */ }
@media (min-width: 1280px) { /* Desktop */ }
@media (min-width: 1920px) { /* Desktop Wide */ }
@media (min-width: 2560px) { /* TV/4K */ }
```

---

### Layer 2: Base (Element Defaults)

**Purpose:** Reset, normalize, base element styles

```css
/* 02_Base/_reset.css */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* 02_Base/_typography.css */
body {
  font-family: var(--font-family-base);
  font-size: var(--font-size-base);
  line-height: 1.5;
  color: var(--color-text);
  background: var(--color-bg);
}

h1 { font-size: 48px; font-weight: 600; line-height: 1.2; }
h2 { font-size: 36px; font-weight: 600; line-height: 1.3; }
h3 { font-size: 28px; font-weight: 600; line-height: 1.4; }
p { margin-bottom: var(--space-md); }
a { color: var(--color-primary); text-decoration: none; }
a:focus-visible { outline: 3px solid var(--color-primary); }
```

---

### Layer 3: Layout (Page Structure)

**Purpose:** Grid systems, layout containers, major structural components

```css
/* 03_Layout/_grid.css */
.container {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: var(--space-lg);
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 var(--space-md);
}

/* Responsive at breakpoints */
@media (max-width: 767px) {
  .container {
    grid-template-columns: 1fr;
    gap: var(--space-md);
  }
}

/* 03_Layout/_header.css */
header {
  position: sticky;
  top: 0;
  z-index: 100;
  padding: var(--space-md) 0;
  background: var(--color-bg);
  border-bottom: 1px solid var(--color-border);
}

header nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* 03_Layout/_sidebar.css */
.sidebar {
  grid-column: 1;
  width: 300px;
}

@media (max-width: 1024px) {
  .sidebar {
    width: 100%;
    grid-column: 1 / -1;
    order: -1;
  }
}
```

---

### Layer 4: Components (Reusable UI Elements)

**Purpose:** Component-level styles, highest specificity, modular

```css
/* 04_Components/_button.css */
.button {
  display: inline-block;
  padding: 12px 24px;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background var(--transition);
  min-height: 44px;
  min-width: 44px;
}

.button:hover {
  background: var(--color-primary-dark);
}

.button:focus-visible {
  outline: 3px solid var(--color-accent);
  outline-offset: 2px;
}

.button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.button--secondary {
  background: transparent;
  border: 2px solid var(--color-primary);
  color: var(--color-primary);
}

/* 04_Components/_card.css */
.card {
  background: white;
  border-radius: 8px;
  padding: var(--space-lg);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
  transition: all var(--transition);
}

/* 04_Components/_form.css */
.form-group {
  margin-bottom: var(--space-lg);
}

label {
  display: block;
  margin-bottom: var(--space-sm);
  font-weight: 600;
}

input, textarea, select {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--color-border);
  border-radius: 4px;
  font-family: inherit;
  font-size: inherit;
  min-height: 44px;
}

input:focus-visible, textarea:focus-visible, select:focus-visible {
  outline: 3px solid var(--color-primary);
  outline-offset: 2px;
}

input[aria-invalid="true"] {
  border-color: var(--color-error);
}
```

---

### Layer 5: Pages (Page-Specific Overrides)

**Purpose:** Exceptions, page-level tweaks

```css
/* 05_Pages/_home.css */
.home-hero {
  background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
  padding: var(--space-xl);
  text-align: center;
}

.home-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--space-lg);
}

@media (max-width: 1024px) {
  .home-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 767px) {
  .home-grid {
    grid-template-columns: 1fr;
  }
}
```

---

### Layer 6: Utilities (Single-Purpose Helper Classes)

**Purpose:** Minimal, single-responsibility classes

```css
/* 06_Utilities/_responsive.css */
.hide-mobile { display: none; }
@media (min-width: 768px) {
  .hide-mobile { display: block; }
}

.show-mobile { display: block; }
@media (min-width: 768px) {
  .show-mobile { display: none; }
}

/* 06_Utilities/_accessibility.css */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

/* 06_Utilities/_spacing.css */
.mt-sm { margin-top: var(--space-sm); }
.mt-md { margin-top: var(--space-md); }
.mb-sm { margin-bottom: var(--space-sm); }
.mb-md { margin-bottom: var(--space-md); }
```

---

### Layer 7: Vendors (Third-Party Overrides)

**Purpose:** Override third-party library styles

```css
/* 07_Vendors/_bootstrap-overrides.css (if using Bootstrap) */
.btn { min-height: 44px; } /* Ensure 44px min touch target */
```

---

## CSS Grid for Page Layout

### 12-Column Grid (All Breakpoints)

```css
.container {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: var(--space-lg);
}

/* Components span different widths */
.sidebar { grid-column: 1 / 4; }     /* Cols 1-3 */
.main { grid-column: 4 / -1; }       /* Cols 4-12 */

/* Responsive: collapse at tablet */
@media (max-width: 1024px) {
  .sidebar { grid-column: 1 / -1; order: -1; }
  .main { grid-column: 1 / -1; }
}
```

### Breakpoint-Specific Grid

```css
/* Base: 320px (1 column) */
.grid { grid-template-columns: 1fr; }

/* Tablet: 768px (2 columns) */
@media (min-width: 768px) {
  .grid { grid-template-columns: repeat(2, 1fr); }
}

/* Desktop: 1280px (3 columns) */
@media (min-width: 1280px) {
  .grid { grid-template-columns: repeat(3, 1fr); }
}
```

---

## Master CSS File (Import Order)

```css
/* main.css */

/* CSS @layer Declaration (ITCSS + Cascade Layers) */
@layer reset, tokens, base, layout, components, pages, utilities, vendors;

/* LAYER 1: Abstracts */
@import "01_Abstracts/_variables.css";
@import "01_Abstracts/_breakpoints.css";

/* LAYER 2: Base */
@import "02_Base/_reset.css";
@import "02_Base/_typography.css";

/* LAYER 3: Layout */
@import "03_Layout/_grid.css";
@import "03_Layout/_header.css";
@import "03_Layout/_sidebar.css";
@import "03_Layout/_footer.css";

/* LAYER 4: Components */
@import "04_Components/_button.css";
@import "04_Components/_card.css";
@import "04_Components/_form.css";
@import "04_Components/_navigation.css";
@import "04_Components/_modal.css";

/* LAYER 5: Pages */
@import "05_Pages/_home.css";
@import "05_Pages/_about.css";
@import "05_Pages/_contact.css";

/* LAYER 6: Utilities */
@import "06_Utilities/_responsive.css";
@import "06_Utilities/_accessibility.css";
@import "06_Utilities/_spacing.css";

/* LAYER 7: Vendors */
@import "07_Vendors/_external.css";
```

---

## CSS @layer + ITCSS Entegrasyonu

CSS Cascade Layers (`@layer`), ITCSS'in yapısal mantığını tarayıcı düzeyinde garanti altına alır.

```css
/* 01_Abstracts/_tokens.css */
@layer tokens {
  :root {
    /* Primitive tokens */
    --cm-gray-100: #f3f4f6;
    --cm-gray-900: #111827;
    --cm-blue-500: #3b82f6;
    
    /* Semantic tokens */
    --cm-color-text: var(--cm-gray-900);
    --cm-color-surface: #ffffff;
    --cm-color-border: var(--cm-gray-100);
    --cm-color-action: var(--cm-blue-500);
    
    /* light-dark() desteği */
    color-scheme: light dark;
  }
  
  /* Dark mode - sadece semantic token'ları değiştir */
  @media (prefers-color-scheme: dark) {
    :root {
      --cm-color-text: #f9fafb;
      --cm-color-surface: #111827;
      --cm-color-border: #374151;
      --cm-color-action: #60a5fa;
    }
  }
  
  /* Veya class-based dark mode */
  [data-theme="dark"] {
    --cm-color-text: #f9fafb;
    --cm-color-surface: #111827;
    --cm-color-border: #374151;
    --cm-color-action: #60a5fa;
  }
}

/* 02_Base/_reset.css */
@layer reset {
  *, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }
}

/* 04_Components/_button.css */
@layer components {
  .button {
    padding: 0.75rem 1.5rem;
    background: var(--cm-color-action);
    color: white;
    border: none;
    border-radius: var(--cm-radius-md, 0.375rem);
    cursor: pointer;
    transition: background var(--cm-transition, 200ms ease-out);
    min-height: 44px;
  }
  
  .button:hover {
    background: var(--cm-color-action-hover);
  }
}

/* 06_Utilities/_helpers.css */
@layer utilities {
  .hidden { display: none; }
  .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
  }
}
```

---

## :where() ile Specificity Yönetimi

`:where()` pseudo-class'ı specificity'yi sıfıra düşürür. Override kolaylaştırır.

```css
@layer components {
  /* Düşük specificity - kolay override edilebilir */
  :where(.button) {
    padding: 0.75rem 1.5rem;
    background: var(--cm-color-action);
  }
  
  /* Yüksek specificity - zor override */
  .button--primary {
    background: var(--cm-color-primary);
  }
}
```

---

## @property ile Animasyonlu Custom Properties

CSS custom property'leri animasyon için `@property` ile tanımlanmalıdır.

```css
@property --cm-gradient-angle {
  syntax: "<angle>";
  inherits: false;
  initial-value: 0deg;
}

@property --cm-scale {
  syntax: "<number>";
  inherits: false;
  initial-value: 1;
}

.card {
  --cm-gradient-angle: 0deg;
  background: linear-gradient(
    var(--cm-gradient-angle),
    var(--cm-color-primary),
    var(--cm-color-accent)
  );
  transition: --cm-gradient-angle 300ms ease;
}

.card:hover {
  --cm-gradient-angle: 180deg;
}

.button {
  --cm-scale: 1;
  transition: transform 200ms ease;
}

.button:active {
  --cm-scale: 0.95;
  transform: scale(var(--cm-scale));
}
```

---

## Logical Properties (RTL Hazırlığı)

Physical properties yerine logical properties kullanarak RTL desteği sağla.

```css
/* ❌ Physical - RTL'de çalışmaz */
.card {
  margin-left: 16px;
  padding-right: 24px;
  border-left: 3px solid var(--cm-color-action);
  text-align: left;
}

/* ✅ Logical - RTL otomatik desteklenir */
.card {
  margin-inline-start: 16px;
  padding-inline-end: 24px;
  border-inline-start: 3px solid var(--cm-color-action);
  text-align: start;
}

/* Logical spacing */
.container {
  padding-block: 2rem;
  padding-inline: 1rem;
}

/* Logical sizing */
.sidebar {
  width: 250px;  /* Physical - block layout için tamam */
  min-inline-size: 200px;  /* Logical - inline direction */
}
```

---

## Fluid Typography (clamp())

Media query olmadan responsive typography.

```css
:root {
  /* Fluid heading sizes */
  --cm-text-h1: clamp(2rem, 4vw + 1rem, 3.5rem);
  --cm-text-h2: clamp(1.5rem, 3vw + 0.75rem, 2.5rem);
  --cm-text-h3: clamp(1.25rem, 2vw + 0.5rem, 1.75rem);
  
  /* Fluid spacing */
  --cm-space-section: clamp(3rem, 5vw, 6rem);
}

h1 { font-size: var(--cm-text-h1); }
h2 { font-size: var(--cm-text-h2); }
h3 { font-size: var(--cm-text-h3); }

.section { padding-block: var(--cm-space-section); }
```

---

## Key Principles

✅ **Mobile-First:** Base styles for 320px, enhance with min-width media queries
✅ **CSS Custom Properties:** All colors, spacing, fonts via variables
✅ **CSS Grid:** Page layouts, Flexbox for components
✅ **CSS @layer:** Cascade management with ITCSS integration
✅ **light-dark() & prefers-color-scheme:** Automatic dark mode support
✅ **:where():** Low specificity for easy overrides
✅ **Logical Properties:** RTL-ready with margin-inline, padding-block
✅ **@property:** Animatable custom properties for smooth transitions
✅ **clamp():** Fluid typography without media queries
✅ **Modular:** Single responsibility per file/class
✅ **No !important:** Except in utilities layer
✅ **Specificity:** Increases down the triangle (utilities highest)
✅ **Reusability:** Classes usable across pages
✅ **Maintainability:** Clear structure, predictable cascading
