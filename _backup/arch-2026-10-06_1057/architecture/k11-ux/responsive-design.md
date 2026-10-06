---
title: "Responsive Tasarım Sistemi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Responsive Tasarım Sistemi

## Genel Bakış

COREMUSIC, mobile-first yaklaşım ile tüm cihazlarda tutarlı bir deneyim sunar. Breakpoint'ler cihaz boyutlarına göre tanımlanır, SCSS mixin'leri ile yönetilir. Fluid typography ve container queries desteklenir.

## Breakpoint Sistemi

```
Breakpoint'ler (mobile-first: min-width):
├── xs:  0px        ← Mobil (default)
├── sm:  576px      ← Büyük mobil
├── md:  768px      ← Tablet
├── lg:  992px      ← Masaüstü
├── xl:  1200px     ← Büyük masaüstü
└── 2xl: 1400px     ← Ultra geniş
```

## Kod Örnekleri

### SCSS Breakpoint Mixin'leri

```scss
// mixins/_breakpoints.scss

// Min-width (mobile-first)
@mixin sm {
  @media (min-width: 576px) { @content; }
}

@mixin md {
  @media (min-width: 768px) { @content; }
}

@mixin lg {
  @media (min-width: 992px) { @content; }
}

@mixin xl {
  @media (min-width: 1200px) { @content; }
}

@mixin xxl {
  @media (min-width: 1400px) { @content; }
}

// Max-width (desktop-first fallback)
@mixin sm-down {
  @media (max-width: 575.98px) { @content; }
}

@mixin md-down {
  @media (max-width: 767.98px) { @content; }
}

@mixin lg-down {
  @media (max-width: 991.98px) { @content; }
}

// Range
@mixin md-only {
  @media (min-width: 768px) and (max-width: 991.98px) { @content; }
}

// Orientation
@mixin landscape {
  @media (orientation: landscape) { @content; }
}

@mixin portrait {
  @media (orientation: portrait) { @content; }
}

// Touch device
@mixin touch {
  @media (hover: none) and (pointer: coarse) { @content; }
}

@mixin mouse {
  @media (hover: hover) and (pointer: fine) { @content; }
}
```

### Grid Sistemi

```scss
// objects/_grid.scss

// Container
.container {
  width: 100%;
  max-width: 1200px;
  margin-inline: auto;
  padding-inline: $spacing-3;

  @include sm { padding-inline: $spacing-4; }
  @include md { padding-inline: $spacing-6; }
  @include lg { padding-inline: $spacing-8; }

  &--fluid {
    max-width: 100%;
  }

  &--narrow {
    max-width: 768px;
  }
}

// Grid
.grid {
  display: grid;
  gap: $spacing-4;

  // Auto-fill columns
  &--auto {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }

  // Fixed columns
  &--2 {
    grid-template-columns: 1fr;

    @include md {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  &--3 {
    grid-template-columns: 1fr;

    @include md {
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
  &--gap-sm { gap: $spacing-2; }
  &--gap-md { gap: $spacing-4; }
  &--gap-lg { gap: $spacing-6; }
}

// Column span
@for $i from 1 through 12 {
  .col-#{$i} {
    grid-column: span 1;

    @include md {
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

  // Direction
  &--row { flex-direction: row; }
  &--col { flex-direction: column; }
  &--row-reverse { flex-direction: row-reverse; }

  // Wrap
  &--wrap { flex-wrap: wrap; }
  &--nowrap { flex-wrap: nowrap; }

  // Justify
  &--start { justify-content: flex-start; }
  &--center { justify-content: center; }
  &--end { justify-content: flex-end; }
  &--between { justify-content: space-between; }
  &--around { justify-content: space-around; }

  // Align
  &--align-start { align-items: flex-start; }
  &--align-center { align-items: center; }
  &--align-end { align-items: flex-end; }
  &--align-stretch { align-items: stretch; }

  // Gap
  &--gap-1 { gap: $spacing-1; }
  &--gap-2 { gap: $spacing-2; }
  &--gap-3 { gap: $spacing-3; }
  &--gap-4 { gap: $spacing-4; }
  &--gap-6 { gap: $spacing-6; }
  &--gap-8 { gap: $spacing-8; }
}

// Flex item
.flex-1 { flex: 1; }
.flex-auto { flex: auto; }
.flex-none { flex: none; }
.flex-shrink-0 { flex-shrink: 0; }
```

### Responsive Card Layout

```scss
// components/_card-grid.scss

.card-grid {
  display: grid;
  gap: $spacing-4;

  // Mobil: tek sütun
  grid-template-columns: 1fr;

  // Tablet: iki sütun
  @include md {
    grid-template-columns: repeat(2, 1fr);
    gap: $spacing-6;
  }

  // Masaüstü: üç sütun
  @include lg {
    grid-template-columns: repeat(3, 1fr);
  }

  // Geniş: dört sütun
  @include xl {
    grid-template-columns: repeat(4, 1fr);
    gap: $spacing-8;
  }

  // Compact variant
  &--compact {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }

  // Featured item
  &__item--featured {
    @include md {
      grid-column: span 2;
      grid-row: span 2;
    }
  }
}
```

### Player Responsive Layout

```scss
// components/_player.scss

.player {
  display: flex;
  flex-direction: column;
  background: var(--player-bg);
  padding: $spacing-3;

  // Tablet
  @include md {
    flex-direction: row;
    align-items: center;
    padding: $spacing-4 $spacing-6;
    gap: $spacing-6;
  }

  // Masaüstü
  @include lg {
    padding: $spacing-6 $spacing-8;
    gap: $spacing-8;
  }

  &__artwork {
    width: 100%;
    max-width: 200px;
    aspect-ratio: 1;
    border-radius: $radius-md;
    overflow: hidden;

    @include md {
      width: 80px;
      max-width: 80px;
    }

    @include lg {
      width: 100px;
      max-width: 100px;
    }
  }

  &__info {
    flex: 1;
    text-align: center;

    @include md {
      text-align: left;
    }
  }

  &__controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: $spacing-4;

    @include md {
      flex: 0;
    }
  }

  &__progress {
    width: 100%;

    @include md {
      flex: 1;
      max-width: 300px;
    }

    @include lg {
      max-width: 500px;
    }
  }

  &__volume {
    display: none;

    @include lg {
      display: flex;
      align-items: center;
      gap: $spacing-2;
      width: 120px;
    }
  }
}
```

### Sidebar Layout

```scss
// objects/_layout-sidebar.scss

.layout {
  display: grid;
  grid-template-columns: 1fr;
  min-height: 100vh;

  @include lg {
    grid-template-columns: 280px 1fr;
  }

  @include xl {
    grid-template-columns: 300px 1fr;
  }

  &__sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 280px;
    transform: translateX(-100%);
    z-index: 1000;
    background: var(--color-surface);
    border-right: 1px solid var(--color-border);
    transition: transform 0.3s ease;

    &.is-open {
      transform: translateX(0);
    }

    @include lg {
      position: sticky;
      transform: none;
    }
  }

  &__main {
    padding-top: 60px;

    @include lg {
      padding-top: 0;
    }
  }

  &__overlay {
    display: none;

    &.is-visible {
      display: block;
      position: fixed;
      inset: 0;
      background: var(--color-overlay);
      z-index: 999;
    }

    @include lg {
      display: none !important;
    }
  }
}
```

### Fluid Typography

```scss
// mixins/_fluid-type.scss

@mixin fluid-type($min-size, $max-size, $min-width: 320px, $max-width: 1200px) {
  font-size: clamp(
    #{$min-size},
    calc(#{$min-size} + (#{$max-size} - #{$min-size}) * ((100vw - #{$min-width}) / (#{$max-width} - #{$min-width}))),
    #{$max-size}
  );
}

// Kullanım
h1 {
  @include fluid-type(1.75rem, 3rem);
}

h2 {
  @include fluid-type(1.5rem, 2.25rem);
}

h3 {
  @include fluid-type(1.25rem, 1.75rem);
}

p {
  @include fluid-type(0.875rem, 1rem);
}
```

### Touch Target Size

```css
/* WCAG 2.2 - Target Size minimum 24x24 */
.player__btn {
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Mobilde daha büyük touch target */
@media (hover: none) and (pointer: coarse) {
  .btn {
    min-height: 48px;
    padding: 12px 24px;
  }

  .nav__link {
    min-height: 44px;
    display: flex;
    align-items: center;
  }
}
```

### Responsive Visibility

```scss
// utilities/_responsive-visibility.scss

// Mobilde görünür, masaüstünde gizli
.visible-mobile {
  display: block;

  @include lg {
    display: none;
  }
}

// Masaüstünde görünür, mobilde gizli
.visible-desktop {
  display: none;

  @include lg {
    display: block;
  }
}

// Tablet ve üstünde görünür
.visible-tablet-up {
  display: none;

  @include md {
    display: block;
  }
}

// Sadece mobilde gizli
.hide-mobile {
  display: block;

  @include md-down {
    display: none;
  }
}
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| dart-sass | 1.77+ | SCSS compilation |
| postcss-preset-env | 9.0+ | Modern CSS features |
| caniuse-lite | latest | Browser support data |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Multi-device experience)
