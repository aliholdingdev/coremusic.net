# Responsive Breakpoint Strategy (7-Tier)

## The 7 Breakpoints

| # | Tier | Width | Device | Base Font | Columns | Use Case |
|---|------|-------|--------|-----------|---------|----------|
| 1 | Mobile | 320px | iPhone 11, SE | 16px | 1 | Initial design, small phones |
| 2 | Mobile-Wide | 600px | Landscape phones, small tablets | 16px | 1-2 | Wide mobile phones |
| 3 | Tablet | 768px | iPad, Android tablets | 18px | 2-3 | Standard tablet |
| 4 | Tablet-Wide | 1024px | iPad Pro, large tablets | 18px | 3 | Landscape tablet |
| 5 | Desktop | 1280px | Laptop, monitor (HD) | 18px | 3-4 | Desktop default |
| 6 | Desktop-Wide | 1920px | Monitor (FHD), Ultrawide | 18px | 3-4 | Large monitors |
| 7 | TV/4K | 2560px+ | 4K monitors, Smart TVs | 20px | 4 | Ultra-wide, TV |

---

## Fluid Typography with clamp() (Recommended)

Modern CSS allows fluid typography without media queries using `clamp()`.

```css
:root {
  /* Fluid heading sizes */
  --text-h1: clamp(2rem, 4vw + 1rem, 4rem);
  --text-h2: clamp(1.5rem, 3vw + 0.75rem, 3rem);
  --text-h3: clamp(1.25rem, 2vw + 0.5rem, 2rem);
  --text-h4: clamp(1.125rem, 1.5vw + 0.375rem, 1.5rem);
  
  /* Fluid body text */
  --text-body: clamp(1rem, 0.5vw + 0.75rem, 1.125rem);
  --text-small: clamp(0.875rem, 0.25vw + 0.75rem, 0.875rem);
  
  /* Fluid spacing */
  --space-section: clamp(3rem, 5vw, 6rem);
  --space-container: clamp(1rem, 3vw, 2rem);
}

h1 { font-size: var(--text-h1); }
h2 { font-size: var(--text-h2); }
h3 { font-size: var(--text-h3); }
body { font-size: var(--text-body); }

.section { padding-block: var(--space-section); }
.container { padding-inline: var(--space-container); }
```

**Benefits:**
- Single line replaces multiple media queries
- Smooth scaling between viewport sizes
- Better UX across all devices

---

## Mobile-First Media Query Pattern (Fallback)

For cases where clamp() isn't sufficient (layout changes, not just sizing):

```css
/* Base: 320px (no media query) */
body { font-size: 16px; }
.grid { grid-template-columns: 1fr; }

/* Mobile-Wide: 600px */
@media (min-width: 600px) {
  body { font-size: 16px; }
  .grid { grid-template-columns: repeat(2, 1fr); }
}

/* Tablet: 768px */
@media (min-width: 768px) {
  body { font-size: 18px; }
  .grid { grid-template-columns: repeat(3, 1fr); }
}

/* Tablet-Wide: 1024px */
@media (min-width: 1024px) {
  /* Fine-tuning */
}

/* Desktop: 1280px (Base design) */
@media (min-width: 1280px) {
  body { font-size: 18px; }
  .container { max-width: 1200px; }
}

/* Desktop-Wide: 1920px */
@media (min-width: 1920px) {
  .sidebar { width: 280px; }
  .main { padding: 0 40px; }
}

/* TV/4K: 2560px+ */
@media (min-width: 2560px) {
  body { font-size: 20px; }
  .container { max-width: 2000px; }
}
```

---

## Container Queries (Component-Level Responsive)

Container queries allow components to respond to their container size, not viewport.

```css
/* Define container */
.card-container {
  container-type: inline-size;
  container-name: card;
}

/* Responsive based on container width */
@container card (min-width: 400px) {
  .card {
    display: grid;
    grid-template-columns: 200px 1fr;
  }
}

@container card (min-width: 600px) {
  .card {
    grid-template-columns: 250px 1fr;
  }
  .card__title {
    font-size: 1.5rem;
  }
}
```

**Use Cases:**
- Cards that adapt to sidebar width
- Widgets that work in different layout regions
- Components reused across multiple page layouts

---

## Responsive Typography

```css
/* Base: 320px */
h1 { font-size: 32px; line-height: 1.2; }
h2 { font-size: 24px; line-height: 1.3; }
h3 { font-size: 20px; line-height: 1.4; }
body { font-size: 16px; line-height: 1.5; }

/* Tablet: 768px (increase size) */
@media (min-width: 768px) {
  h1 { font-size: 40px; }
  h2 { font-size: 28px; }
  h3 { font-size: 24px; }
  body { font-size: 18px; }
}

/* Desktop: 1280px (base design) */
@media (min-width: 1280px) {
  h1 { font-size: 48px; }
  h2 { font-size: 36px; }
  h3 { font-size: 28px; }
  body { font-size: 18px; }
}

/* 4K: 2560px */
@media (min-width: 2560px) {
  h1 { font-size: 56px; }
  h2 { font-size: 44px; }
  h3 { font-size: 32px; }
  body { font-size: 20px; }
}
```

---

## Responsive Spacing

```css
/* Base: 320px */
--space-md: 16px;
--space-lg: 24px;
.card { padding: var(--space-md); margin-bottom: var(--space-md); }

/* Tablet: 768px (more breathing room) */
@media (min-width: 768px) {
  :root {
    --space-md: 20px;
    --space-lg: 32px;
  }
}

/* Desktop: 1280px */
@media (min-width: 1280px) {
  :root {
    --space-md: 24px;
    --space-lg: 40px;
  }
}

/* 4K: 2560px */
@media (min-width: 2560px) {
  :root {
    --space-md: 32px;
    --space-lg: 56px;
  }
}
```

---

## Common Responsive Patterns

### Hide/Show by Breakpoint
```css
.hide-mobile { display: none; }
@media (min-width: 768px) { .hide-mobile { display: block; } }

.show-mobile { display: block; }
@media (min-width: 768px) { .show-mobile { display: none; } }

.hide-desktop { display: block; }
@media (min-width: 1280px) { .hide-desktop { display: none; } }
```

### Grid Layout Transformation
```css
/* 320px: 1 column */
.grid { grid-template-columns: 1fr; }

/* 768px: 2 columns */
@media (min-width: 768px) {
  .grid { grid-template-columns: repeat(2, 1fr); }
}

/* 1280px: 3 columns */
@media (min-width: 1280px) {
  .grid { grid-template-columns: repeat(3, 1fr); }
}

/* 2560px: 4 columns */
@media (min-width: 2560px) {
  .grid { grid-template-columns: repeat(4, 1fr); }
}
```

### Flexible Container
```css
.container {
  max-width: 100%;
  padding: var(--space-md);
}

@media (min-width: 768px) {
  .container { padding: var(--space-lg); }
}

@media (min-width: 1280px) {
  .container { max-width: 1200px; margin: 0 auto; }
}

@media (min-width: 1920px) {
  .container { max-width: 1400px; }
}

@media (min-width: 2560px) {
  .container { max-width: 2000px; }
}
```

---

## Testing Breakpoints

Use browser DevTools to manually test:
1. **320px:** Resize to smallest phone width
2. **600px:** Landscape phone
3. **768px:** Tablet portrait
4. **1024px:** Tablet landscape
5. **1280px:** Laptop (base design)
6. **1920px:** Large monitor
7. **2560px+:** 4K monitor or resize to max

---

## Key Rules

✅ **Mobile-first:** min-width media queries only
✅ **No desktop-first:** Never use max-width (harder to maintain)
✅ **Progressive enhancement:** Content readable at all sizes
✅ **No horizontal scroll:** At any breakpoint
✅ **Fluid typography:** Font sizes scale smoothly (consider calc())
✅ **Touch targets:** ≥ 24px at all breakpoints
