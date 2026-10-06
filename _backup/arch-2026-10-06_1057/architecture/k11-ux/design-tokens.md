---
title: "Design Tokens"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Design Tokens

## Genel Bakış

Design Tokens, tasarım kararlarının merkezi ve platformdan bağımsız tanımıdır. Renkler, tipografi, boşluklar ve efektler token olarak tanımlanır ve Style Dictionary aracılığıyla SCSS, CSS, Android, iOS formatlarına dönüştürülür. COREMUSIC'te token'lar hem compile zamanında (SCSS) hem de runtime'da (CSS Custom Properties) çalışır.

## Token Kategorileri

```
tokens/
├── global/              ← Global token'lar (ham değerler)
│   ├── _colors.json
│   ├── _typography.json
│   ├── _spacing.json
│   └── _effects.json
├── semantic/            ← Semantic token'lar (anlam tabanlı)
│   ├── _colors.json
│   ├── _typography.json
│   └── _spacing.json
├── component/           ← Component token'ları
│   ├── _button.json
│   ├── _card.json
│   └── _player.json
└── theme/               ← Tema token'ları
    ├── _light.json
    └── _dark.json
```

## Token Tabakaları

### 1. Global Token'lar

Ham değerler. Platformdan bağımsız, doğrudan kullanılmaz.

```json
{
  "color": {
    "purple": {
      "50": "#F3F0FF",
      "100": "#E5DEFF",
      "200": "#C4B5FD",
      "300": "#A78BFA",
      "400": "#8B5CF6",
      "500": "#7C3AED",
      "600": "#6D28D9",
      "700": "#5B21B6",
      "800": "#4C1D95",
      "900": "#3B0764"
    },
    "teal": {
      "400": "#2DD4BF",
      "500": "#14B8A6",
      "600": "#0D9488"
    }
  },
  "space": {
    "0": "0px",
    "1": "4px",
    "2": "8px",
    "3": "12px",
    "4": "16px",
    "5": "20px",
    "6": "24px",
    "8": "32px",
    "10": "40px",
    "12": "48px",
    "16": "64px"
  }
}
```

### 2. Semantic Token'lar

Anlam tabanlı token'lar. Global token'lara referans verir.

```json
{
  "color": {
    "primary": "{color.purple.500}",
    "primary-hover": "{color.purple.600}",
    "primary-light": "{color.purple.100}",
    "secondary": "{color.teal.500}",
    "secondary-hover": "{color.teal.600}",
    "background": {
      "default": "#FFFFFF",
      "elevated": "#F8FAFC",
      "surface": "#F1F5F9"
    },
    "text": {
      "primary": "#0F172A",
      "secondary": "#475569",
      "muted": "#94A3B8",
      "inverse": "#FFFFFF"
    },
    "border": {
      "default": "#E2E8F0",
      "strong": "#CBD5E1"
    },
    "status": {
      "success": "#22C55E",
      "warning": "#F59E0B",
      "error": "#EF4444",
      "info": "#3B82F6"
    }
  }
}
```

### 3. Component Token'ları

Bileşen özelinde token'lar.

```json
{
  "button": {
    "primary": {
      "bg": "{color.primary}",
      "bg-hover": "{color.primary-hover}",
      "text": "{color.text.inverse}",
      "border": "{color.primary}"
    },
    "secondary": {
      "bg": "transparent",
      "bg-hover": "{color.primary-light}",
      "text": "{color.primary}",
      "border": "{color.primary}"
    },
    "padding": {
      "sm": "{space.2} {space.4}",
      "md": "{space.3} {space.6}",
      "lg": "{space.4} {space.8}"
    },
    "border-radius": "{radius.md}",
    "font-size": {
      "sm": "{fontSize.xs}",
      "md": "{fontSize.sm}",
      "lg": "{fontSize.md}"
    }
  }
}
```

## Kod Örnekleri

### SCSS Token'ları

```scss
// tokens/_colors.scss - Global token'lar
$color-purple-50: #F3F0FF;
$color-purple-100: #E5DEFF;
$color-purple-200: #C4B5FD;
$color-purple-300: #A78BFA;
$color-purple-400: #8B5CF6;
$color-purple-500: #7C3AED;
$color-purple-600: #6D28D9;
$color-purple-700: #5B21B6;

$color-teal-400: #2DD4BF;
$color-teal-500: #14B8A6;

// Semantic token'lar
$color-primary: $color-purple-500;
$color-primary-hover: $color-purple-600;
$color-primary-light: $color-purple-100;

$color-bg: #FFFFFF;
$color-bg-elevated: #F8FAFC;
$color-surface: #F1F5F9;

$color-text: #0F172A;
$color-text-muted: #94A3B8;

// Component token'ları
$btn-primary-bg: $color-primary;
$btn-primary-text: #FFFFFF;
$btn-padding-sm: $spacing-2 $spacing-4;
$btn-padding-md: $spacing-3 $spacing-6;
$btn-radius: $radius-md;
```

### CSS Custom Properties

```css
/* tokens/_css-variables.css */
:root {
  /* Global */
  --color-purple-500: #7C3AED;
  --color-teal-500: #14B8A6;

  /* Semantic */
  --color-primary: var(--color-purple-500);
  --color-primary-hover: var(--color-purple-600);
  --color-secondary: var(--color-teal-500);

  --color-bg: #FFFFFF;
  --color-bg-elevated: #F8FAFC;
  --color-surface: #F1F5F9;

  --color-text: #0F172A;
  --color-text-secondary: #475569;
  --color-text-muted: #94A3B8;

  --color-border: #E2E8F0;

  --color-success: #22C55E;
  --color-warning: #F59E0B;
  --color-error: #EF4444;
  --color-info: #3B82F6;

  /* Spacing */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-6: 24px;
  --space-8: 32px;

  /* Typography */
  --font-size-xs: 0.75rem;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  --font-size-2xl: 1.5rem;
  --font-size-3xl: 2rem;

  /* Radius */
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
  --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
}
```

### Style Dictionary Build

```javascript
// style-dictionary.config.js
module.exports = {
  source: ['tokens/**/*.json'],
  platforms: {
    scss: {
      transformGroup: 'scss',
      buildPath: 'dist/scss/',
      files: [
        {
          destination: '_variables.scss',
          format: 'scss/variables'
        },
        {
          destination: '_variables-map.scss',
          format: 'scss/map-deep'
        }
      ]
    },
    css: {
      transformGroup: 'css',
      buildPath: 'dist/css/',
      files: [
        {
          destination: 'tokens.css',
          format: 'css/variables'
        }
      ]
    },
    android: {
      transformGroup: 'android',
      buildPath: 'dist/android/',
      files: [
        {
          destination: 'tokens.xml',
          format: 'android/resources'
        }
      ]
    },
    ios: {
      transformGroup: 'ios-swift',
      buildPath: 'dist/ios/',
      files: [
        {
          destination: 'Tokens.swift',
          format: 'ios-swift/class.swift'
        }
      ]
    }
  }
};
```

### Token Kullanım Örnekleri

```scss
// ✅ DOĞRU: Semantic token kullan
.btn--primary {
  background: $color-primary;
  color: $color-text-inverse;

  &:hover {
    background: $color-primary-hover;
  }
}

// ❌ YANLIŞ: Global token doğrudan kullan
.btn--primary {
  background: $color-purple-500;  // Tema değişikliğinde kırılır
}
```

### Dark Theme Token'ları

```scss
// themes/_dark.scss
[data-theme='dark'] {
  --color-primary: #A29BFE;     // purple-300
  --color-primary-hover: #C4B5FD; // purple-200

  --color-bg: #0D0D0D;
  --color-bg-elevated: #1A1A2E;
  --color-surface: #16213E;

  --color-text: #FAFAFA;
  --color-text-secondary: #CBD5E1;
  --color-text-muted: #64748B;

  --color-border: #2D2D44;
}
```

## Style Dictionary Build Script

```json
// package.json
{
  "scripts": {
    "tokens": "style-dictionary build",
    "tokens:watch": "chokidar 'tokens/**/*.json' -c 'npm run tokens'"
  }
}
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| style-dictionary | 3.9+ | Token transform |
| chokidar | 3.6+ | File watch |
| dart-sass | 1.77+ | SCSS compilation |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Tasarım sistemi temel taşı)
