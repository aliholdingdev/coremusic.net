---
title: "Tipografi Ölçeği"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Tipografi Ölçeği

## Genel Bakış

COREMUSIC tipografi sistemi, modular scale yaklaşımı ile tutarlı ve okunabilir bir tipografi hiyerarşisi oluşturur. Font stack, line height, letter spacing ve responsive typography kuralları bu dosyada tanımlanır.

## Font Stack

```scss
// settings/_fonts.scss

// Primary font family
$font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI',
  Roboto, Oxygen, Ubuntu, Cantarell, 'Fira Sans', 'Droid Sans',
  'Helvetica Neue', Arial, sans-serif;

// Monospace (code, metadata)
$font-family-mono: 'JetBrains Mono', 'Fira Code', 'SF Mono',
  'Cascadia Code', 'Source Code Pro', Menlo, Monaco, Consolas,
  'Courier New', monospace;

// Display (başlıklar için opsiyonel)
$font-family-display: 'Plus Jakarta Sans', 'Inter', $font-family-sans;
```

## Modular Scale

```
Scale Ratio: 1.250 (Major Third)

Level   Scale    Size       Use Case
─────   ─────    ────       ────────
xs      0.75rem  12px       Caption, badge
sm      0.875rem 14px       Small text, meta
base    1rem     16px       Body text
md      1.125rem 18px       Large body
lg      1.25rem  20px       H6, lead text
xl      1.5rem   24px       H5
2xl     1.875rem 30px       H4
3xl     2.25rem  36px       H3
4xl     3rem     48px       H2
5xl     3.75rem  60px       H1
6xl     4.5rem   72px       Display
```

## Kod Örnekleri

### SCSS Değişkenleri

```scss
// settings/_typography.scss

// Font sizes (modular scale)
$font-size-2xs: 0.625rem;  // 10px
$font-size-xs: 0.75rem;    // 12px
$font-size-sm: 0.875rem;   // 14px
$font-size-base: 1rem;     // 16px
$font-size-md: 1.125rem;   // 18px
$font-size-lg: 1.25rem;    // 20px
$font-size-xl: 1.5rem;     // 24px
$font-size-2xl: 1.875rem;  // 30px
$font-size-3xl: 2.25rem;   // 36px
$font-size-4xl: 3rem;      // 48px
$font-size-5xl: 3.75rem;   // 60px
$font-size-6xl: 4.5rem;    // 72px

// Font weights
$font-weight-thin: 100;
$font-weight-extralight: 200;
$font-weight-light: 300;
$font-weight-regular: 400;
$font-weight-medium: 500;
$font-weight-semibold: 600;
$font-weight-bold: 700;
$font-weight-extrabold: 800;
$font-weight-black: 900;

// Line heights
$line-height-none: 1;
$line-height-tight: 1.25;
$line-height-snug: 1.375;
$line-height-normal: 1.5;
$line-height-relaxed: 1.625;
$line-height-loose: 2;

// Letter spacing
$tracking-tighter: -0.05em;
$tracking-tight: -0.025em;
$tracking-normal: 0em;
$tracking-wide: 0.025em;
$tracking-wider: 0.05em;
$tracking-widest: 0.1em;
```

### CSS Custom Properties

```css
/* settings/_typography-tokens.css */
:root {
  /* Font families */
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI',
    Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', 'SF Mono', monospace;
  --font-display: 'Plus Jakarta Sans', 'Inter', var(--font-sans);

  /* Font sizes */
  --text-2xs: 0.625rem;
  --text-xs: 0.75rem;
  --text-sm: 0.875rem;
  --text-base: 1rem;
  --text-md: 1.125rem;
  --text-lg: 1.25rem;
  --text-xl: 1.5rem;
  --text-2xl: 1.875rem;
  --text-3xl: 2.25rem;
  --text-4xl: 3rem;
  --text-5xl: 3.75rem;
  --text-6xl: 4.5rem;

  /* Font weights */
  --font-light: 300;
  --font-regular: 400;
  --font-medium: 500;
  --font-semibold: 600;
  --font-bold: 700;
  --font-extrabold: 800;

  /* Line heights */
  --leading-none: 1;
  --leading-tight: 1.25;
  --leading-snug: 1.375;
  --leading-normal: 1.5;
  --leading-relaxed: 1.625;
  --leading-loose: 2;

  /* Letter spacing */
  --tracking-tighter: -0.05em;
  --tracking-tight: -0.025em;
  --tracking-normal: 0em;
  --tracking-wide: 0.025em;
  --tracking-wider: 0.05em;
  --tracking-widest: 0.1em;
}
```

### Base Typography

```scss
// elements/_typography.scss

body {
  font-family: $font-family-sans;
  font-size: $font-size-base;
  font-weight: $font-weight-regular;
  line-height: $line-height-normal;
  color: var(--color-text);
  text-rendering: optimizeLegibility;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

// Headings
h1, .h1 {
  font-size: $font-size-4xl;
  font-weight: $font-weight-extrabold;
  line-height: $line-height-tight;
  letter-spacing: $tracking-tight;

  @include md {
    font-size: $font-size-5xl;
  }

  @include lg {
    font-size: $font-size-6xl;
  }
}

h2, .h2 {
  font-size: $font-size-3xl;
  font-weight: $font-weight-bold;
  line-height: $line-height-tight;
  letter-spacing: $tracking-tight;

  @include md {
    font-size: $font-size-4xl;
  }
}

h3, .h3 {
  font-size: $font-size-2xl;
  font-weight: $font-weight-bold;
  line-height: $line-height-snug;

  @include md {
    font-size: $font-size-3xl;
  }
}

h4, .h4 {
  font-size: $font-size-xl;
  font-weight: $font-weight-semibold;
  line-height: $line-height-snug;
}

h5, .h5 {
  font-size: $font-size-lg;
  font-weight: $font-weight-semibold;
  line-height: $line-height-snug;
}

h6, .h6 {
  font-size: $font-size-md;
  font-weight: $font-weight-medium;
  line-height: $line-height-normal;
}

// Body text
p {
  margin-bottom: $spacing-4;
  max-width: 70ch; // Optimal reading width

  &:last-child {
    margin-bottom: 0;
  }
}

// Lead text
.lead {
  font-size: $font-size-lg;
  font-weight: $font-weight-regular;
  line-height: $line-height-relaxed;
  color: var(--color-text-secondary);
}

// Small text
small, .text-sm {
  font-size: $font-size-sm;
  line-height: $line-height-normal;
}

// Caption
.caption {
  font-size: $font-size-xs;
  font-weight: $font-weight-medium;
  letter-spacing: $tracking-wide;
  text-transform: uppercase;
  color: var(--color-text-muted);
}

// Monospace
code, .code {
  font-family: $font-family-mono;
  font-size: 0.875em;
  padding: 0.125em 0.375em;
  background: var(--color-bg-subtle);
  border-radius: $radius-sm;
}

pre {
  font-family: $font-family-mono;
  font-size: $font-size-sm;
  line-height: $line-height-relaxed;
  padding: $spacing-4;
  background: var(--color-bg-subtle);
  border-radius: $radius-md;
  overflow-x: auto;

  code {
    padding: 0;
    background: none;
  }
}

// Links
a {
  color: var(--color-primary);
  text-decoration: underline;
  text-decoration-thickness: 1px;
  text-underline-offset: 2px;
  transition: color 0.2s ease;

  &:hover {
    color: var(--color-primary-hover);
  }
}

// Lists
ul, ol {
  padding-left: $spacing-5;
  margin-bottom: $spacing-4;

  li {
    margin-bottom: $spacing-1;
  }
}

// Blockquote
blockquote {
  padding-left: $spacing-4;
  border-left: 3px solid var(--color-primary);
  font-style: italic;
  color: var(--color-text-secondary);

  cite {
    display: block;
    margin-top: $spacing-2;
    font-size: $font-size-sm;
    font-style: normal;
    color: var(--color-text-muted);
  }
}
```

### Fluid Typography

```scss
// mixins/_fluid-type.scss

@mixin fluid-type($min-size, $max-size, $min-width: 320px, $max-width: 1200px) {
  $slope: calc(($max-size - $min-size) / ($max-width - $min-width));
  $intercept: calc($min-size - $slope * $min-width);

  font-size: clamp(
    #{$min-size},
    calc(#{$intercept} + #{$slope} * 100vw),
    #{$max-size}
  );
}

// Kullanım
body {
  @include fluid-type($font-size-base, $font-size-md);
}

h1 {
  @include fluid-type($font-size-3xl, $font-size-6xl);
}

h2 {
  @include fluid-type($font-size-2xl, $font-size-5xl);
}

h3 {
  @include fluid-type($font-size-xl, $font-size-3xl);
}
```

### Text Utilities

```scss
// utilities/_text.scss

.text-left { text-align: left; }
.text-center { text-align: center; }
.text-right { text-align: right; }

.text-uppercase { text-transform: uppercase; }
.text-lowercase { text-transform: lowercase; }
.text-capitalize { text-transform: capitalize; }

.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.text-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.text-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.text-nowrap { white-space: nowrap; }
.text-wrap { white-space: normal; }

.font-light { font-weight: $font-weight-light; }
.font-regular { font-weight: $font-weight-regular; }
.font-medium { font-weight: $font-weight-medium; }
.font-semibold { font-weight: $font-weight-semibold; }
.font-bold { font-weight: $font-weight-bold; }
.font-extrabold { font-weight: $font-weight-extrabold; }

.leading-none { line-height: $line-height-none; }
.leading-tight { line-height: $line-height-tight; }
.leading-normal { line-height: $line-height-normal; }
.leading-relaxed { line-height: $line-height-relaxed; }
```

### Google Fonts Entegrasyonu

```html
<!--<head>-->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
  rel="stylesheet"
/>
```

```css
/* Font loading optimization */
@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 100 900;
  font-display: swap;
  src: url('/fonts/inter-variable.woff2') format('woff2-variations');
}
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| Google Fonts | - | Font CDN |
| fontsource | 5.0+ | Self-hosted fonts |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Readability foundation)
