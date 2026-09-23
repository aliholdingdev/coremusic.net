---
title: "ITCSS 9 Katmanlı CSS Mimarisi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# ITCSS 9 Katmanlı CSS Mimarisi

## Genel Bakış

ITCSS (Inverted Triangle CSS), CSS kodunun maintainability ve scalability için organize edildiği bir mimari paternidir. COREMUSIC, spesifiklik seviyesine göre artan 9 katman kullanır. Her katman yalnızca bir amaca hizmet eder ve katmanlar arası bağımlılık tek yönlüdür.

## Mimari Yapı

```
itcss/
├── 1-settings/       ← Değişkenler, design token'lar
├── 2-tools/          ← Mixin'ler, fonksiyonlar
├── 3-generic/        ← Reset, normalize, box-sizing
├── 4-elements/       ← HTML element stilleri
├── 5-objects/        ← Layout, grid, container
├── 6-components/     ← Bileşen stilleri
├── 7-utilities/      ← Yardımcı sınıflar
├── 8-themes/         ← Tema varyantları
└── 9-trumps/         ← Override'lar, !important
```

## Katman Detayları

### 1-Settings

Design token'lar ve global değişkenler. Hiçbir stil üretmez, yalnızca değer tanımlar.

```scss
// settings/_variables.scss
$color-primary: #6C5CE7;
$color-secondary: #00CEC9;
$color-dark: #0D0D0D;
$color-light: #FAFAFA;

$font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
$font-family-mono: 'JetBrains Mono', 'Fira Code', monospace;

$font-size-base: 16px;
$line-height-base: 1.6;

$spacing-unit: 8px;
$border-radius: 4px;

$breakpoint-sm: 576px;
$breakpoint-md: 768px;
$breakpoint-lg: 992px;
$breakpoint-xl: 1200px;
$breakpoint-2xl: 1400px;

$z-index-dropdown: 1000;
$z-index-sticky: 1020;
$z-index-fixed: 1030;
$z-index-modal-backdrop: 1040;
$z-index-modal: 1050;
$z-index-popover: 1060;
$z-index-tooltip: 1070;
```

### 2-Tools

SCSS mixin'leri ve fonksiyonları. Stil üretmez, yalnızca yardımcı araçlar sağlar.

```scss
// tools/_mixins.scss

// Breakpoint mixin'leri
@mixin respond-above($breakpoint) {
  @if $breakpoint == sm {
    @media (min-width: $breakpoint-sm) { @content; }
  } @else if $breakpoint == md {
    @media (min-width: $breakpoint-md) { @content; }
  } @else if $breakpoint == lg {
    @media (min-width: $breakpoint-lg) { @content; }
  } @else if $breakpoint == xl {
    @media (min-width: $breakpoint-xl) { @content; }
  } @else if $breakpoint == 2xl {
    @media (min-width: $breakpoint-2xl) { @content; }
  }
}

@mixin respond-below($breakpoint) {
  @if $breakpoint == sm {
    @media (max-width: ($breakpoint-sm - 1px)) { @content; }
  } @else if $breakpoint == md {
    @media (max-width: ($breakpoint-md - 1px)) { @content; }
  }
}

// Focus visible mixin
@mixin focus-visible {
  &:focus-visible {
    outline: 2px solid $color-primary;
    outline-offset: 2px;
  }
}

// Truncate text
@mixin truncate($lines: 1) {
  @if $lines == 1 {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  } @else {
    display: -webkit-box;
    -webkit-line-clamp: $lines;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}

// Glassmorphism
@mixin glass($opacity: 0.1) {
  background: rgba(255, 255, 255, $opacity);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}
```

### 3-Generic

Reset ve normalize. Element seçiciler kullanır, class-based değildir.

```scss
// generic/_reset.scss
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  -webkit-text-size-adjust: 100%;
  -moz-text-size-adjust: 100%;
  text-size-adjust: 100%;
  scroll-behavior: smooth;
}

body {
  min-height: 100vh;
  text-rendering: optimizeLegibility;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

img, picture, video, canvas, svg {
  display: block;
  max-width: 100%;
}

input, button, textarea, select {
  font: inherit;
  color: inherit;
}

p, h1, h2, h3, h4, h5, h6 {
  overflow-wrap: break-word;
}

a {
  color: inherit;
  text-decoration: none;
}
```

### 4-Elements

HTML elementlerinin default stilleri.

```scss
// elements/_typography.scss
body {
  font-family: $font-family-base;
  font-size: $font-size-base;
  line-height: $line-height-base;
  color: $color-text;
  background-color: $color-bg;
}

h1 { font-size: 2.5rem; font-weight: 800; line-height: 1.2; }
h2 { font-size: 2rem; font-weight: 700; line-height: 1.25; }
h3 { font-size: 1.5rem; font-weight: 600; line-height: 1.3; }
h4 { font-size: 1.25rem; font-weight: 600; line-height: 1.35; }
h5 { font-size: 1rem; font-weight: 500; line-height: 1.4; }
h6 { font-size: 0.875rem; font-weight: 500; line-height: 1.45; }

code, pre, kbd, samp {
  font-family: $font-family-mono;
}

a:not([class]) {
  color: $color-primary;
  text-decoration: underline;
  text-decoration-thickness: 1px;
  text-underline-offset: 2px;

  &:hover {
    color: darken($color-primary, 10%);
  }
}
```

### 5-Objects

Layout pattern'leri ve grid sistemi.

```scss
// objects/_grid.scss
.container {
  width: 100%;
  max-width: 1200px;
  margin-inline: auto;
  padding-inline: $spacing-unit * 3;

  &--fluid {
    max-width: 100%;
  }

  &--narrow {
    max-width: 768px;
  }
}

.grid {
  display: grid;
  gap: $spacing-unit * 3;

  &--2 { grid-template-columns: repeat(2, 1fr); }
  &--3 { grid-template-columns: repeat(3, 1fr); }
  &--4 { grid-template-columns: repeat(4, 1fr); }

  @include respond-below(md) {
    &--2, &--3, &--4 {
      grid-template-columns: 1fr;
    }
  }
}

.flex {
  display: flex;

  &--center { align-items: center; justify-content: center; }
  &--between { align-items: center; justify-content: space-between; }
  &--col { flex-direction: column; }
  &--wrap { flex-wrap: wrap; }
  &--gap-sm { gap: $spacing-unit; }
  &--gap-md { gap: $spacing-unit * 2; }
  &--gap-lg { gap: $spacing-unit * 3; }
}
```

### 6-Components

Bileşen stilleri. BEM isimlendirme kullanır.

```scss
// components/_buttons.scss
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: $spacing-unit;
  padding: $spacing-unit * 1.5 $spacing-unit * 3;
  font-size: 0.875rem;
  font-weight: 600;
  line-height: 1;
  border: 1px solid transparent;
  border-radius: $border-radius;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;

  @include focus-visible;

  &--primary {
    background: $color-primary;
    color: $color-white;

    &:hover {
      background: darken($color-primary, 8%);
    }
  }

  &--secondary {
    background: transparent;
    color: $color-primary;
    border-color: $color-primary;

    &:hover {
      background: $color-primary;
      color: $color-white;
    }
  }

  &--ghost {
    background: transparent;
    color: $color-text;

    &:hover {
      background: rgba($color-text, 0.05);
    }
  }

  &--sm {
    padding: $spacing-unit $spacing-unit * 2;
    font-size: 0.75rem;
  }

  &--lg {
    padding: $spacing-unit * 2 $spacing-unit * 4;
    font-size: 1rem;
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }
}
```

### 7-Utilities

Yardımcı sınıflar. Inline utility'ler.

```scss
// utilities/_spacing.scss
@each $space in (0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16) {
  .mt-#{$space} { margin-top: $spacing-unit * $space; }
  .mb-#{$space} { margin-bottom: $spacing-unit * $space; }
  .ml-#{$space} { margin-left: $spacing-unit * $space; }
  .mr-#{$space} { margin-right: $spacing-unit * $space; }
  .mx-#{$space} { margin-inline: $spacing-unit * $space; }
  .my-#{$space} { margin-block: $spacing-unit * $space; }

  .pt-#{$space} { padding-top: $spacing-unit * $space; }
  .pb-#{$space} { padding-bottom: $spacing-unit * $space; }
  .pl-#{$space} { padding-left: $spacing-unit * $space; }
  .pr-#{$space} { padding-right: $spacing-unit * $space; }
  .px-#{$space} { padding-inline: $spacing-unit * $space; }
  .py-#{$space} { padding-block: $spacing-unit * $space; }
}

// utilities/_visibility.scss
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.hidden { display: none !important; }
.visible { visibility: visible; }
.invisible { visibility: hidden; }
```

### 8-Themes

Tema varyantları.

```scss
// themes/_dark.scss
[data-theme='dark'] {
  --color-bg: #0D0D0D;
  --color-bg-elevated: #1A1A2E;
  --color-surface: #16213E;
  --color-text: #FAFAFA;
  --color-text-muted: #A0A0A0;
  --color-primary: #6C5CE7;
  --color-secondary: #00CEC9;
  --color-border: #2D2D44;
  --color-shadow: rgba(0, 0, 0, 0.4);
}
```

### 9-Trumps

Override'lar ve !important kullanım kuralları.

```scss
// trumps/_accessibility.scss
.sr-only-focusable {
  &:not(:focus) {
    @extend .sr-only;
  }
}

// trumps/_overrides.scss
// Özel durumlar için son müdahale katmanı
```

## Kod Örnekleri

### Katman Kullanım Kuralı

```scss
// ✅ DOĞRU: Tools katmanı settings'e bağımlı
@import '../1-settings/variables';
@import '../1-settings/tokens';

// ✅ DOĞRU: Components objects'e bağımlı
@import '../5-objects/grid';

// ❌ YANLIŞ: Settings'in Components'e bağımlı olmaması
// components/_buttons.scss içinde: @import '../6-components/'; // asla olmaz
```

### Custom Property Entegrasyonu

```scss
// settings'te SCSS değişkenleri
$color-primary: #6C5CE7;

// themes'te CSS custom properties
[data-theme='dark'] {
  --color-primary: #A29BFE;
}

// components'te her ikisini de kullan
.btn--primary {
  background: $color-primary; // Compile zamanı
  color: var(--color-text);   // Runtime (tema desteği)
}
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| Dart Sass | 1.77+ | SCSS compilation |
| PostCSS | 8.4+ | CSS transformation |
| autoprefixer | 10.4+ | Vendor prefix ekleme |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (CSS mimarisi temel taşı)
