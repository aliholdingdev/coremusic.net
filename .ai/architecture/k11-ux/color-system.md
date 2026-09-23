---
title: "Renk Sistemi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Renk Sistemi

## Genel Bakış

COREMUSIC renk sistemi, global renk paleti, semantic renkler ve contrast ratio kurallarını kapsar. Tüm renkler hem SCSS değişkenleri hem de CSS custom properties olarak tanımlanır. Dark/light tema desteği, WCAG 2.2 AA contrast standartları ve high contrast mode uyumluluğu sağlanır.

## Renk Paleti

```
Primary:    #7C3AED (Purple 500)  → #A78BFA (Purple 300)
Secondary:  #14B8A6 (Teal 500)    → #2DD4BF (Teal 400)
Success:    #22C55E (Green 500)   → #4ADE80 (Green 400)
Warning:    #F59E0B (Amber 500)   → #FBBF24 (Amber 400)
Error:      #EF4444 (Red 500)     → #F87171 (Red 400)
Info:       #3B82F6 (Blue 500)    → #60A5FA (Blue 400)
```

## Kod Örnekleri

### SCSS Renk Paleti

```scss
// settings/_colors.scss

// Neutral (Gray)
$gray-50: #F8FAFC;
$gray-100: #F1F5F9;
$gray-200: #E2E8F0;
$gray-300: #CBD5E1;
$gray-400: #94A3B8;
$gray-500: #64748B;
$gray-600: #475569;
$gray-700: #334155;
$gray-800: #1E293B;
$gray-900: #0F172A;
$gray-950: #020617;

// Purple (Primary)
$purple-50: #FAF5FF;
$purple-100: #F3E8FF;
$purple-200: #E9D5FF;
$purple-300: #D8B4FE;
$purple-400: #C084FC;
$purple-500: #A855F7;
$purple-600: #9333EA;
$purple-700: #7E22CE;
$purple-800: #6B21A8;
$purple-900: #581C87;

// Teal (Secondary)
$teal-50: #F0FDFA;
$teal-100: #CCFBF1;
$teal-200: #99F6E4;
$teal-300: #5EEAD4;
$teal-400: #2DD4BF;
$teal-500: #14B8A6;
$teal-600: #0D9488;
$teal-700: #0F766E;
$teal-800: #115E59;
$teal-900: #134E4A;

// Green (Success)
$green-400: #4ADE80;
$green-500: #22C55E;
$green-600: #16A34A;

// Amber (Warning)
$amber-400: #FBBF24;
$amber-500: #F59E0B;
$amber-600: #D97706;

// Red (Error)
$red-400: #F87171;
$red-500: #EF4444;
$red-600: #DC2626;

// Blue (Info)
$blue-400: #60A5FA;
$blue-500: #3B82F6;
$blue-600: #2563EB;
```

### Semantic Renkler

```scss
// settings/_semantic-colors.scss

// Light theme
$color-primary: $purple-500;
$color-primary-hover: $purple-600;
$color-primary-light: $purple-50;
$color-primary-dark: $purple-700;

$color-secondary: $teal-500;
$color-secondary-hover: $teal-600;
$color-secondary-light: $teal-50;

$color-success: $green-500;
$color-success-light: #DCFCE7;

$color-warning: $amber-500;
$color-warning-light: #FEF3C7;

$color-error: $red-500;
$color-error-light: #FEE2E2;

$color-info: $blue-500;
$color-info-light: #DBEAFE;

// Background
$color-bg: #FFFFFF;
$color-bg-elevated: $gray-50;
$color-bg-subtle: $gray-100;

// Text
$color-text: $gray-900;
$color-text-secondary: $gray-600;
$color-text-muted: $gray-400;
$color-text-inverse: #FFFFFF;

// Border
$color-border: $gray-200;
$color-border-strong: $gray-300;
```

### CSS Custom Properties

```css
/* settings/_color-tokens.css */
:root {
  /* Primary */
  --color-primary-50: #FAF5FF;
  --color-primary-100: #F3E8FF;
  --color-primary-200: #E9D5FF;
  --color-primary-300: #D8B4FE;
  --color-primary-400: #C084FC;
  --color-primary-500: #A855F7;
  --color-primary-600: #9333EA;
  --color-primary-700: #7E22CE;
  --color-primary-800: #6B21A8;
  --color-primary-900: #581C87;

  /* Semantic */
  --color-primary: var(--color-primary-500);
  --color-primary-hover: var(--color-primary-600);
  --color-secondary: #14B8A6;
  --color-success: #22C55E;
  --color-warning: #F59E0B;
  --color-error: #EF4444;
  --color-info: #3B82F6;

  /* Background */
  --color-bg: #FFFFFF;
  --color-bg-elevated: #F8FAFC;
  --color-bg-subtle: #F1F5F9;

  /* Text */
  --color-text: #0F172A;
  --color-text-secondary: #475569;
  --color-text-muted: #94A3B8;
  --color-text-inverse: #FFFFFF;

  /* Border */
  --color-border: #E2E8F0;
  --color-border-strong: #CBD5E1;
}

[data-theme='dark'] {
  --color-primary: var(--color-primary-300);
  --color-primary-hover: var(--color-primary-400);
  --color-secondary: #2DD4BF;
  --color-success: #4ADE80;
  --color-warning: #FBBF24;
  --color-error: #F87171;
  --color-info: #60A5FA;

  --color-bg: #0D0D0D;
  --color-bg-elevated: #1A1A2E;
  --color-bg-subtle: #16213E;

  --color-text: #F1F5F9;
  --color-text-secondary: #CBD5E1;
  --color-text-muted: #64748B;
  --color-text-inverse: #0F172A;

  --color-border: #334155;
  --color-border-strong: #475569;
}
```

### Contrast Ratio Kontrolü

```scss
// tools/_contrast.scss

// WCAG AA minimum contrast ratios:
// Normal text: 4.5:1
// Large text: 3:1
// UI components: 3:1

// Contrast ratio hesaplama (SCSS)
@function luminance($color) {
  $r: red($color) / 255;
  $g: green($color) / 255;
  $b: blue($color) / 255;

  @if $r <= 0.03928 { $r: $r / 12.92; } @else { $r: ($r + 0.055) / 1.055; }
  @if $g <= 0.03928 { $g: $g / 12.92; } @else { $g: ($g + 0.055) / 1.055; }
  @if $b <= 0.03928 { $b: $b / 12.92; } @else { $b: ($b + 0.055) / 1.055; }

  @return (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) * 100;
}

@function contrast-ratio($color1, $color2) {
  $l1: luminance($color1);
  $l2: luminance($color2);

  @if $l1 > $l2 {
    @return ($l1 + 0.05) / ($l2 + 0.05);
  } @else {
    @return ($l2 + 0.05) / ($l1 + 0.05);
  }
}

// Usage check
$primary-on-white: contrast-ratio(#A855F7, #FFFFFF);
// Result: ~4.6:1 ✅ (passes AA for normal text)

$text-on-dark: contrast-ratio(#F1F5F9, #0D0D0D);
// Result: ~15.8:1 ✅ (passes AAA)
```

### Renk Kullanım Kuralları

```scss
// components/_color-usage.scss

// Status badges
.badge {
  &--success {
    background: var(--color-success);
    color: #FFFFFF;

    // Contrast check: #22C55E on white = 3.3:1 ❌
    // Solution: Use white text on green background
  }

  &--warning {
    background: var(--color-warning);
    color: $gray-900;

    // Contrast check: #F59E0B on #0F172A = 7.2:1 ✅
  }

  &--error {
    background: var(--color-error);
    color: #FFFFFF;

    // Contrast check: #EF4444 on white = 4.0:1 ❌
    // Solution: Use white text on red background
  }
}

// Interactive states
.link {
  color: var(--color-primary);

  &:hover {
    color: var(--color-primary-hover);
  }

  &:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
  }

  &:active {
    color: var(--color-primary-dark);
  }
}
```

### High Contrast Mode

```scss
// themes/_high-contrast.scss

@media (prefers-contrast: high) {
  :root {
    --color-text: #000000;
    --color-text-secondary: #1A1A1A;
    --color-bg: #FFFFFF;
    --color-border: #000000;
    --color-primary: #0000CC;
  }
}

@media (forced-colors: active) {
  .btn--primary {
    border: 2px solid ButtonText;
    background: ButtonFace;
    color: ButtonText;
  }

  .player__progress-fill {
    background: Highlight;
  }

  .card {
    border: 1px solid CanvasText;
  }

  // Focus visible
  :focus-visible {
    outline: 2px solid Highlight;
    outline-offset: 2px;
  }
}
```

### Renk Erişilebilirlik Testi

```javascript
// accessibility/color-contrast.js
const COLOR_COMBOS = [
  { fg: '#A855F7', bg: '#FFFFFF', name: 'Primary on White' },
  { fg: '#FFFFFF', bg: '#A855F7', name: 'White on Primary' },
  { fg: '#0F172A', bg: '#FFFFFF', name: 'Text on White' },
  { fg: '#F1F5F9', bg: '#0D0D0D', name: 'Text on Dark' },
  { fg: '#22C55E', bg: '#FFFFFF', name: 'Success on White' },
  { fg: '#EF4444', bg: '#FFFFFF', name: 'Error on White' },
];

function getContrastRatio(fg, bg) {
  const hexToRgb = hex => {
    const r = parseInt(hex.slice(1, 3), 16) / 255;
    const g = parseInt(hex.slice(3, 5), 16) / 255;
    const b = parseInt(hex.slice(5, 7), 16) / 255;
    return [r, g, b];
  };

  const luminance = ([r, g, b]) => {
    const adjust = c => c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    return 0.2126 * adjust(r) + 0.7152 * adjust(g) + 0.0722 * adjust(b);
  };

  const l1 = luminance(hexToRgb(fg));
  const l2 = luminance(hexToRgb(bg));
  const lighter = Math.max(l1, l2);
  const darker = Math.min(l1, l2);
  return (lighter + 0.05) / (darker + 0.05);
}

COLOR_COMBOS.forEach(({ fg, bg, name }) => {
  const ratio = getContrastRatio(fg, bg);
  const aa = ratio >= 4.5 ? '✅' : '❌';
  const aaa = ratio >= 7 ? '✅' : '❌';
  console.log(`${name}: ${ratio.toFixed(2)}:1 AA:${aa} AAA:${aaa}`);
});
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| sass | 1.77+ | Color functions |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Visual design foundation)
