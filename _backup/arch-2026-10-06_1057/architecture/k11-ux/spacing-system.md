---
title: "Boşluk Sistemi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Boşluk Sistemi

## Genel Bakış

COREMUSIC boşluk sistemi, 8px tabanlı bir scale üzerine inşa edilmiştir. Tutarlı boşluklar, grid sistemi ve layout utility'leri ile sayfa düzeni yönetilir. Tüm boşluk değerleri hem SCSS değişkenleri hem de CSS custom properties olarak tanımlanır.

## Spacing Scale

```
Spacing Token   Değer    Kullanım
─────────────   ─────    ────────
$spacing-0      0px      Sıfır boşluk
$spacing-px     1px      Hairline border
$spacing-0-5    2px      Minimal
$spacing-1      4px      Tight
$spacing-1-5    6px      Compact
$spacing-2      8px      Small (base unit)
$spacing-2-5    10px     Small-medium
$spacing-3      12px     Medium-small
$spacing-3-5    14px     Medium
$spacing-4      16px     Medium (default)
$spacing-5      20px     Medium-large
$spacing-6      24px     Large
$spacing-7      28px     Large-medium
$spacing-8      32px     Extra large
$spacing-9      36px     2x large
$spacing-10     40px     2.5x large
$spacing-11     44px     3x large
$spacing-12     48px     3.5x large
$spacing-14     56px     4x large
$spacing-16     64px     5x large
$spacing-20     80px     6x large
$spacing-24     96px     7x large
$spacing-28     112px    8x large
$spacing-32     128px    9x large
```

## Kod Örnekleri

### SCSS Spacing Değişkenleri

```scss
// settings/_spacing.scss

// Base unit
$spacing-unit: 4px;

// Scale
$spacing-0: 0;
$spacing-px: 1px;
$spacing-0-5: 2px;
$spacing-1: 4px;
$spacing-1-5: 6px;
$spacing-2: 8px;
$spacing-2-5: 10px;
$spacing-3: 12px;
$spacing-3-5: 14px;
$spacing-4: 16px;
$spacing-5: 20px;
$spacing-6: 24px;
$spacing-7: 28px;
$spacing-8: 32px;
$spacing-9: 36px;
$spacing-10: 40px;
$spacing-11: 44px;
$spacing-12: 48px;
$spacing-14: 56px;
$spacing-16: 64px;
$spacing-20: 80px;
$spacing-24: 96px;
$spacing-28: 112px;
$spacing-32: 128px;

// Semantic spacing
$spacing-xs: $spacing-1;      // 4px
$spacing-sm: $spacing-2;      // 8px
$spacing-md: $spacing-4;      // 16px
$spacing-lg: $spacing-6;      // 24px
$spacing-xl: $spacing-8;      // 32px
$spacing-2xl: $spacing-12;    // 48px
$spacing-3xl: $spacing-16;    // 64px

// Component spacing
$spacing-card-padding: $spacing-4;
$spacing-card-gap: $spacing-4;
$spacing-section: $spacing-16;
$spacing-page-padding: $spacing-4;

// Responsive page padding
$spacing-page-padding-sm: $spacing-4;
$spacing-page-padding-md: $spacing-6;
$spacing-page-padding-lg: $spacing-8;
```

### CSS Custom Properties

```css
/* settings/_spacing-tokens.css */
:root {
  /* Scale */
  --space-0: 0px;
  --space-px: 1px;
  --space-0-5: 2px;
  --space-1: 4px;
  --space-1-5: 6px;
  --space-2: 8px;
  --space-2-5: 10px;
  --space-3: 12px;
  --space-3-5: 14px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-7: 28px;
  --space-8: 32px;
  --space-9: 36px;
  --space-10: 40px;
  --space-11: 44px;
  --space-12: 48px;
  --space-14: 56px;
  --space-16: 64px;
  --space-20: 80px;
  --space-24: 96px;
  --space-28: 112px;
  --space-32: 128px;

  /* Semantic */
  --space-xs: var(--space-1);
  --space-sm: var(--space-2);
  --space-md: var(--space-4);
  --space-lg: var(--space-6);
  --space-xl: var(--space-8);
  --space-2xl: var(--space-12);
  --space-3xl: var(--space-16);

  /* Layout */
  --page-padding: var(--space-4);
  --section-spacing: var(--space-16);
  --card-padding: var(--space-4);
  --card-gap: var(--space-4);

  @media (min-width: 768px) {
    --page-padding: var(--space-6);
    --section-spacing: var(--space-20);
  }

  @media (min-width: 992px) {
    --page-padding: var(--space-8);
    --section-spacing: var(--space-24);
  }
}
```

### Margin ve Padding Utility'leri

```scss
// utilities/_spacing.scss

// Generate spacing utilities
$spacing-values: (
  0: 0,
  px: 1px,
  0-5: 2px,
  1: 4px,
  1-5: 6px,
  2: 8px,
  2-5: 10px,
  3: 12px,
  3-5: 14px,
  4: 16px,
  5: 20px,
  6: 24px,
  7: 28px,
  8: 32px,
  9: 36px,
  10: 40px,
  11: 44px,
  12: 48px,
  14: 56px,
  16: 64px,
  20: 80px,
  24: 96px
);

@each $key, $value in $spacing-values {
  // Margin
  .m-#{$key} { margin: $value; }
  .mt-#{$key} { margin-top: $value; }
  .mb-#{$key} { margin-bottom: $value; }
  .ml-#{$key} { margin-left: $value; }
  .mr-#{$key} { margin-right: $value; }
  .mx-#{$key} { margin-left: $value; margin-right: $value; }
  .my-#{$key} { margin-top: $value; margin-bottom: $value; }

  // Padding
  .p-#{$key} { padding: $value; }
  .pt-#{$key} { padding-top: $value; }
  .pb-#{$key} { padding-bottom: $value; }
  .pl-#{$key} { padding-left: $value; }
  .pr-#{$key} { padding-right: $value; }
  .px-#{$key} { padding-left: $value; padding-right: $value; }
  .py-#{$key} { padding-top: $value; padding-bottom: $value; }
}

// Negative margin
@each $key, $value in $spacing-values {
  @if $value != 0 and $value != 1px {
    .-mt-#{$key} { margin-top: -$value; }
    .-mb-#{$key} { margin-bottom: -$value; }
    .-ml-#{$key} { margin-left: -$value; }
    .-mr-#{$key} { margin-right: -$value; }
  }
}

// Auto margin
.mx-auto { margin-left: auto; margin-right: auto; }
.ml-auto { margin-left: auto; }
.mr-auto { margin-right: auto; }
```

### Grid Sistemi

```scss
// objects/_grid.scss

// Container
.container {
  width: 100%;
  max-width: 1200px;
  margin-inline: auto;
  padding-inline: var(--page-padding);

  &--fluid {
    max-width: 100%;
  }

  &--narrow {
    max-width: 768px;
  }

  &--wide {
    max-width: 1400px;
  }
}

// Grid
.grid {
  display: grid;
  gap: $spacing-4;

  @include md {
    gap: $spacing-6;
  }

  // Auto-fit columns
  &--auto {
    grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
  }

  &--auto-sm {
    grid-template-columns: repeat(auto-fill, minmax(min(200px, 100%), 1fr));
  }

  &--auto-lg {
    grid-template-columns: repeat(auto-fill, minmax(min(350px, 100%), 1fr));
  }

  // Fixed columns (responsive)
  &--2 {
    grid-template-columns: 1fr;

    @include md {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  &--3 {
    grid-template-columns: 1fr;

    @include sm {
      grid-template-columns: repeat(2, 1fr);
    }

    @include lg {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  &--4 {
    grid-template-columns: 1fr;

    @include sm {
      grid-template-columns: repeat(2, 1fr);
    }

    @include lg {
      grid-template-columns: repeat(3, 1fr);
    }

    @include xl {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  // Gap variants
  &--gap-2 { gap: $spacing-2; }
  &--gap-3 { gap: $spacing-3; }
  &--gap-4 { gap: $spacing-4; }
  &--gap-6 { gap: $spacing-6; }
  &--gap-8 { gap: $spacing-8; }
}

// Grid span
@each $i from 1 through 12 {
  .col-#{$i} {
    grid-column: span #{$i};
  }

  .col-start-#{$i} {
    grid-column-start: #{$i};
  }
}

// Responsive column overrides
@include md {
  @for $i from 1 through 6 {
    .md\:col-#{$i} {
      grid-column: span #{$i};
    }
  }
}

@include lg {
  @for $i from 1 through 12 {
    .lg\:col-#{$i} {
      grid-column: span #{$i};
    }
  }
}
```

### Flex Layout

```scss
// objects/_flex.scss

.flex {
  display: flex;

  &--inline { display: inline-flex; }
  &--col { flex-direction: column; }
  &--col-reverse { flex-direction: column-reverse; }
  &--row-reverse { flex-direction: row-reverse; }
  &--wrap { flex-wrap: wrap; }
  &--nowrap { flex-wrap: nowrap; }

  // Justify
  &--justify-start { justify-content: flex-start; }
  &--justify-center { justify-content: center; }
  &--justify-end { justify-content: flex-end; }
  &--justify-between { justify-content: space-between; }
  &--justify-around { justify-content: space-around; }
  &--justify-evenly { justify-content: space-evenly; }

  // Align
  &--items-start { align-items: flex-start; }
  &--items-center { align-items: center; }
  &--items-end { align-items: flex-end; }
  &--items-stretch { align-items: stretch; }
  &--items-baseline { align-items: baseline; }

  // Self
  &--self-start { align-self: flex-start; }
  &--self-center { align-self: center; }
  &--self-end { align-self: flex-end; }
  &--self-stretch { align-self: stretch; }

  // Gap
  &--gap-1 { gap: $spacing-1; }
  &--gap-2 { gap: $spacing-2; }
  &--gap-3 { gap: $spacing-3; }
  &--gap-4 { gap: $spacing-4; }
  &--gap-5 { gap: $spacing-5; }
  &--gap-6 { gap: $spacing-6; }
  &--gap-8 { gap: $spacing-8; }
  &--gap-10 { gap: $spacing-10; }
  &--gap-12 { gap: $spacing-12; }
}

// Flex item
.flex-1 { flex: 1 1 0%; }
.flex-auto { flex: 1 1 auto; }
.flex-none { flex: none; }
.flex-grow { flex-grow: 1; }
.flex-shrink-0 { flex-shrink: 0; }
.flex-grow-0 { flex-grow: 0; }
```

### Section Spacing

```scss
// objects/_sections.scss

.section {
  padding-block: var(--section-spacing);

  &--sm {
    padding-block: $spacing-8;
  }

  &--lg {
    padding-block: $spacing-24;
  }

  &--xl {
    padding-block: $spacing-32;
  }

  // Dividers
  & + & {
    border-top: 1px solid var(--color-border);
  }
}

// Stack layout (vertical spacing)
.stack {
  display: flex;
  flex-direction: column;

  & > * + * {
    margin-top: var(--stack-gap, $spacing-4);
  }

  &--xs { --stack-gap: #{$spacing-2}; }
  &--sm { --stack-gap: #{$spacing-3}; }
  &--md { --stack-gap: #{$spacing-4}; }
  &--lg { --stack-gap: #{$spacing-6}; }
  &--xl { --stack-gap: #{$spacing-8}; }
}

// Cluster layout (horizontal spacing)
.cluster {
  display: flex;
  flex-wrap: wrap;
  gap: var(--cluster-gap, $spacing-2);

  &--xs { --cluster-gap: #{$spacing-1}; }
  &--sm { --cluster-gap: #{$spacing-2}; }
  &--md { --cluster-gap: #{$spacing-3}; }
  &--lg { --cluster-gap: #{$spacing-4}; }
  &--xl { --cluster-gap: #{$spacing-6}; }
}
```

### Auto Spacing (Margin Tricks)

```scss
// utilities/_auto-space.scss

// Flow layout with auto margins
.flow {
  display: flex;
  flex-direction: column;

  > * + * {
    margin-block-start: auto;
  }

  > * + .flow--push-down {
    margin-block-start: auto;
  }
}

// Center content
.center {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-inline: auto;
  max-width: max-content;
}

// Repel layout
.repel {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: $spacing-4;
}

// Switcher layout
.switcher {
  display: flex;
  flex-wrap: wrap;
  gap: var(--switcher-gap, $spacing-4);

  > * {
    flex-grow: 1;
    flex-basis: calc((var(--switcher-threshold, 30rem) - 100%) * 999);
  }

  > * + * {
    margin-inline-start: auto;
  }
}
```

## Boşluk Kullanım Kuralları

| Kural | Açıklama |
|---|---|
| Base unit | 4px multiples kullan |
| Component padding | $spacing-4 (16px) default |
| Card gap | $spacing-4 (16px) mobil, $spacing-6 (24px) masaüstü |
| Section spacing | $spacing-16 (64px) default |
| Page padding | Responsive: 16px → 24px → 32px |
| Stack spacing | Kardinal değerlerle tutarlı olan boşluk |
| Negative margin | Overlay ve pull-up efektleri için |

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| dart-sass | 1.77+ | SCSS compilation |
| postcss | 8.4+ | CSS transform |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Layout foundation)
