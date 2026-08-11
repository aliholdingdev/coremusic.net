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

## Key Principles

✅ **Mobile-First:** Base styles for 320px, enhance with min-width media queries
✅ **CSS Custom Properties:** All colors, spacing, fonts via variables
✅ **CSS Grid:** Page layouts, Flexbox for components
✅ **Modular:** Single responsibility per file/class
✅ **No !important:** Except in utilities layer
✅ **Specificity:** Increases down the triangle (utilities highest)
✅ **Reusability:** Classes usable across pages
✅ **Maintainability:** Clear structure, predictable cascading
