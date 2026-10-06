---
title: "Tema Motoru"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Tema Motoru

## Genel Bakış

Tema Motoru, COREMUSIC'in dark/light tema geçişini yöneten sistemdir. CSS Custom Properties aracılığıyla runtime'da tema değişikliği sağlar. Kullanıcı tercihleri LocalStorage'da saklanır, sistem tercihine göre otomatik seçim desteklenir.

## Mimari Yapı

```
theme-engine/
├── _variables.scss       ← SCSS değişkenleri (compile-time)
├── _tokens.css           ← CSS custom properties (runtime)
├── theme-toggle.js       ← Tema geçiş mantığı
├── theme-provider.js     ← Tema sağlayıcı (reactive)
└── theme-persist.js      ← LocalStorage persist
```

## Tema Değişkenleri

### Light Theme (Varsayılan)

```scss
// themes/_light.scss
:root,
[data-theme='light'] {
  /* Background */
  --color-bg: #FFFFFF;
  --color-bg-elevated: #F8FAFC;
  --color-bg-subtle: #F1F5F9;
  --color-surface: #FFFFFF;

  /* Text */
  --color-text: #0F172A;
  --color-text-secondary: #475569;
  --color-text-muted: #94A3B8;
  --color-text-inverse: #FFFFFF;

  /* Brand */
  --color-primary: #7C3AED;
  --color-primary-hover: #6D28D9;
  --color-primary-light: #EDE9FE;
  --color-secondary: #14B8A6;
  --color-secondary-hover: #0D9488;

  /* Status */
  --color-success: #22C55E;
  --color-warning: #F59E0B;
  --color-error: #EF4444;
  --color-info: #3B82F6;

  /* Border */
  --color-border: #E2E8F0;
  --color-border-strong: #CBD5E1;

  /* Shadow */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);

  /* Overlay */
  --color-overlay: rgba(0, 0, 0, 0.5);

  /* Player */
  --player-bg: #F8FAFC;
  --player-progress: #E2E8F0;
  --player-progress-fill: #7C3AED;
}
```

### Dark Theme

```scss
// themes/_dark.scss
[data-theme='dark'] {
  /* Background */
  --color-bg: #0D0D0D;
  --color-bg-elevated: #1A1A2E;
  --color-bg-subtle: #16213E;
  --color-surface: #1E293B;

  /* Text */
  --color-text: #F1F5F9;
  --color-text-secondary: #CBD5E1;
  --color-text-muted: #64748B;
  --color-text-inverse: #0F172A;

  /* Brand */
  --color-primary: #A78BFA;
  --color-primary-hover: #8B5CF6;
  --color-primary-light: rgba(167, 139, 250, 0.1);
  --color-secondary: #2DD4BF;
  --color-secondary-hover: #14B8A6;

  /* Status */
  --color-success: #4ADE80;
  --color-warning: #FBBF24;
  --color-error: #F87171;
  --color-info: #60A5FA;

  /* Border */
  --color-border: #334155;
  --color-border-strong: #475569;

  /* Shadow */
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);

  /* Overlay */
  --color-overlay: rgba(0, 0, 0, 0.7);

  /* Player */
  --player-bg: #1E293B;
  --player-progress: #334155;
  --player-progress-fill: #A78BFA;
}
```

## Kod Örnekleri

### Tema Toggle JavaScript

```javascript
// theme-engine/theme-toggle.js
class ThemeToggle {
  constructor() {
    this.STORAGE_KEY = 'coremusic-theme';
    this.prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    this.currentTheme = this.getInitialTheme();
  }

  getInitialTheme() {
    const stored = localStorage.getItem(this.STORAGE_KEY);
    if (stored) return stored;
    return this.prefersDark.matches ? 'dark' : 'light';
  }

  setTheme(theme) {
    this.currentTheme = theme;
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(this.STORAGE_KEY, theme);
    this.updateToggleIcon(theme);
    this.dispatchThemeChange(theme);
  }

  toggle() {
    const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
    this.setTheme(newTheme);
  }

  updateToggleIcon(theme) {
    const toggleBtn = document.querySelector('.theme-toggle');
    if (!toggleBtn) return;

    const icon = toggleBtn.querySelector('.theme-toggle__icon');
    const label = toggleBtn.querySelector('.theme-toggle__label');

    if (theme === 'dark') {
      icon.textContent = '☀️';
      label.textContent = 'Açık tema';
    } else {
      icon.textContent = '🌙';
      label.textContent = 'Koyu tema';
    }
  }

  dispatchThemeChange(theme) {
    window.dispatchEvent(new CustomEvent('themechange', {
      detail: { theme }
    }));
  }

  init() {
    this.setTheme(this.currentTheme);

    this.prefersDark.addEventListener('change', (e) => {
      if (!localStorage.getItem(this.STORAGE_KEY)) {
        this.setTheme(e.matches ? 'dark' : 'light');
      }
    });

    const toggleBtn = document.querySelector('.theme-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => this.toggle());
    }
  }
}

// Initialize
const themeToggle = new ThemeToggle();
themeToggle.init();
```

### Theme Provider (Reactive)

```javascript
// theme-engine/theme-provider.js
class ThemeProvider {
  constructor() {
    this.listeners = new Set();
    this.theme = document.documentElement.getAttribute('data-theme') || 'light';
  }

  subscribe(callback) {
    this.listeners.add(callback);
    return () => this.listeners.delete(callback);
  }

  notify() {
    this.listeners.forEach(cb => cb(this.theme));
  }

  getTheme() {
    return this.theme;
  }

  setTheme(theme) {
    this.theme = theme;
    document.documentElement.setAttribute('data-theme', theme);
    this.notify();
  }
}

// Usage
const themeProvider = new ThemeProvider();

themeProvider.subscribe((theme) => {
  console.log(`Tema değişti: ${theme}`);
  // Component güncellemeleri
});
```

### SCSS Tema Mixin

```scss
// mixins/_theme.scss
@mixin themed($property, $light-value, $dark-value) {
  $var-name: '--tw-' + str-slug(unique-id());

  #{$property}: var(#{$var-name}, #{$light-value});

  [data-theme='dark'] & {
    #{$property}: #{$dark-value};
  }
}

// Kullanım
.card {
  @include themed(background, #FFFFFF, #1A1A2E);
  @include themed(color, #0F172A, #F1F5F9);
}
```

### Tema Geçiş Animasyonu

```scss
// Animasyonlu tema geçişi
html {
  transition: background-color 0.3s ease, color 0.3s ease;
}

body {
  transition: background-color 0.3s ease;
}

*,
*::before,
*::after {
  transition:
    background-color 0.3s ease,
    border-color 0.3s ease,
    color 0.3s ease,
    box-shadow 0.3s ease;
}

// Theme toggle butonu
.theme-toggle {
  position: fixed;
  bottom: $spacing-6;
  right: $spacing-6;
  width: 48px;
  height: 48px;
  border-radius: $radius-full;
  background: var(--color-primary);
  color: var(--color-text-inverse);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  box-shadow: var(--shadow-lg);
  transition: transform 0.2s ease;

  &:hover {
    transform: scale(1.1);
  }

  &__icon,
  &__label {
    transition: opacity 0.2s ease;
  }

  &__label {
    @extend .sr-only;
  }
}
```

### Tema Persist

```javascript
// theme-engine/theme-persist.js
class ThemePersist {
  constructor(storageKey = 'coremusic-theme') {
    this.storageKey = storageKey;
  }

  get() {
    try {
      return localStorage.getItem(this.storageKey);
    } catch {
      return null;
    }
  }

  set(theme) {
    try {
      localStorage.setItem(this.storageKey, theme);
    } catch {
      // LocalStorage dolu veya devre dışı
    }
  }

  remove() {
    try {
      localStorage.removeItem(this.storageKey);
    } catch {
      // ignore
    }
  }

  hasUserPreference() {
    return this.get() !== null;
  }
}
```

### Tema Dosya Yapısı

```
scss/
├── themes/
│   ├── _light.scss        ← Light theme token'ları
│   ├── _dark.scss         ← Dark theme token'ları
│   └── _transitions.scss  ← Tema geçiş animasyonları
├── mixins/
│   └── _theme.scss        ← Tema mixin'leri
└── components/
    └── _theme-toggle.scss ← Toggle butonu stili
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| CSS Custom Properties | - | Runtime tema değişikliği |
| LocalStorage | - | Tercih persist |
| matchMedia | - | Sistem tercihi algılama |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Kullanıcı deneyimi temel özelliği)
