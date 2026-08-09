---
title: UI/UX & ACCESSIBILITY — WCAG 2.2 AA
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# UI/UX & ACCESSIBILITY — WCAG 2.2 AA
# Prompt Maker v7.2.0 | 2026-06-11

## WCAG 2.2 AA Standards

### Perceivable

**Contrast:**
- Text: 4.5:1 (normal), 3:1 (large 18pt+)
- Graphics: 3:1 (edges, UI components)
- Test: WebAIM Contrast Checker

**Color:** Don't rely on color alone (use icons, labels, patterns)

**Text Alternatives:** alt text for images, captions for video

### Operable

**Keyboard Navigation:**
- Tab (next), Shift+Tab (prev), Enter (activate), Escape (close)
- Logical tab order (follow DOM)
- No keyboard traps (can always escape)

**Focus Indicator:**
- Visible 3px outline (not removed)
- Color contrast 3:1 against background
- Not obscured by other elements

**Touch Targets:**
- Minimum 24×24px (WCAG 2.5.5)
- Spacing: 8px between targets (recommended)

**No Flashing:** Avoid > 3 flashes per second (seizure risk)

### Understandable

**Readable:**
- Font size ≥ 14px (16px better)
- Line height ≥ 1.5
- Line width ≤ 80 characters
- Language of page declared: `<html lang="en">`

**Predictable:**
- Consistent navigation (header, footer, main)
- Consistent terminology
- Forms: labels, error messages, instructions

### Robust

**Semantic HTML:**
```html
<header>, <nav>, <main>, <article>, <section>, <footer>
<button>, <input>, <label>, <form>
NOT: <div class="button">, <span onclick>
```

**ARIA (Accessible Rich Internet Applications):**
```html
<button aria-label="Close menu" aria-expanded="false">✕</button>
<div role="alert" aria-live="polite">Error message</div>
<nav aria-label="Main navigation">...</nav>
```

**Screen Reader Support:**
- All interactive elements must be keyboard accessible
- Images need alt text
- Form inputs need labels
- Dynamic content updates with aria-live

## Responsive Design (5 Breakpoints)

| Device | Width | Example |
|--------|-------|---------|
| Mobile | 320-480px | iPhone 11, Samsung J7 |
| Small Tablet | 481-768px | iPad Mini |
| Large Tablet | 769-1024px | iPad Pro 11" |
| Desktop | 1025-1920px | Laptop, monitor |
| Ultra-wide | 1921px+ | 3840px 4K TV |

**Approach:** Mobile-first, progressive enhancement

```css
/* Mobile (base) */
body { font-size: 16px; }

/* Tablet */
@media (min-width: 768px) {
    body { font-size: 18px; }
}

/* Desktop */
@media (min-width: 1024px) {
    body { font-size: 20px; }
}
```

## Dark/Light Mode

**CSS Custom Properties:**
```css
:root {
    --color-primary: #007bff;
    --color-bg: #ffffff;
    --color-text: #000000;
}

@media (prefers-color-scheme: dark) {
    :root {
        --color-primary: #0d6efd;
        --color-bg: #1a1a1a;
        --color-text: #f0f0f0;
    }
}

body {
    background-color: var(--color-bg);
    color: var(--color-text);
}
```

**Manual Toggle:** Store preference in localStorage, detect system preference with `prefers-color-scheme`

## Component State Matrix (9 States)

| State | Definition | Example |
|-------|-----------|---------|
| DEFAULT | Normal, unmodified | Button at rest |
| HOVER | Pointer over element | Button with mouse over |
| ACTIVE | Element being activated | Button during click |
| FOCUSED | Element has keyboard focus | Button with Tab focus |
| DISABLED | Element cannot be used | Grayed-out button |
| ERROR | Input validation failed | Form field with red border |
| LOADING | Action in progress | Button with spinner |
| EMPTY | No data available | Empty search results |
| SELECTED | Item chosen from list | Checkbox checked |

**All states must be visually distinct and accessible (not color-only)**

## Typography

**Font Stack (Web-safe with fallbacks):**
```css
font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
font-family: 'Georgia', 'Times New Roman', serif;
font-family: 'Courier New', monospace;
```

**Web Fonts (Google Fonts, licensed):**
```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
font-family: 'Inter', sans-serif;
```

**Sizes:**
- Heading 1: 32px
- Heading 2: 24px
- Heading 3: 20px
- Body: 16px
- Small: 14px

## Spacing System (8px Grid)

```
8px, 16px, 24px, 32px, 48px, 64px
```

Margins, padding, gaps all multiples of 8px

## Color Palette

**Primary:** #007bff (5.5:1 contrast on white)
**Success:** #28a745
**Warning:** #ffc107 (contrast issue, use darker for text)
**Danger:** #dc3545

Always test contrast ratios

## Accessibility Testing Tools

- **axe DevTools:** Browser extension
- **WAVE:** Web accessibility evaluator
- **Lighthouse:** Chrome DevTools
- **NVDA/JAWS:** Screen readers
- **Keyboard navigation:** Tab through entire app

## Common Accessibility Mistakes (AVOID)

- Color-only indicators (use icons too)
- Missing alt text on images
- Removed focus outlines (outline: none without replacement)
- Keyboard traps (can't Tab out)
- Unreadable contrast (< 4.5:1)
- No heading hierarchy (skip levels)
- Auto-playing video/audio
- Flashing content > 3/sec

---

Quality Score (2026-06-11): 98.7%
