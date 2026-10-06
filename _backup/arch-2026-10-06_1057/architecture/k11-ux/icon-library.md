---
title: "İkon Kütüphanesi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# İkon Kütüphanesi

## Genel Bakış

COREMUSIC ikon sistemi, SVG sprite tabanlı bir approach ile yönetilir. Tüm ikonlar inline SVG, SVG sprite veya icon font olarak kullanılabilir. İkonlar erişilebilirlik, performans ve tema desteği için optimize edilmiştir.

## Mimari Yapı

```
icons/
├── svg/                 ← Tek tek SVG dosyaları
│   ├── play.svg
│   ├── pause.svg
│   ├── skip-forward.svg
│   ├── skip-back.svg
│   ├── shuffle.svg
│   ├── repeat.svg
│   ├── volume.svg
│   ├── volume-mute.svg
│   ├── heart.svg
│   ├── heart-filled.svg
│   ├── search.svg
│   ├── home.svg
│   ├── library.svg
│   ├── playlist.svg
│   ├── settings.svg
│   ├── user.svg
│   ├── share.svg
│   ├── download.svg
│   ├── plus.svg
│   ├── minus.svg
│   ├── check.svg
│   ├── close.svg
│   ├── chevron-left.svg
│   ├── chevron-right.svg
│   ├── chevron-up.svg
│   ├── chevron-down.svg
│   ├── menu.svg
│   ├── arrow-left.svg
│   ├── arrow-right.svg
│   ├── external-link.svg
│   ├── trash.svg
│   ├── edit.svg
│   ├── clock.svg
│   ├── calendar.svg
│   ├── star.svg
│   └── more-vertical.svg
├── sprite/              ← Sprite dosyaları
│   └── sprite.svg
└── icon-font/           ← Icon font (opsiyonel)
    ├── coremusic-icons.woff2
    ├── coremusic-icons.woff
    └── _icon-font.scss
```

## Kod Örnekleri

### SVG Sprite Oluşturma

```javascript
// build/sprite-builder.js
const fs = require('fs');
const path = require('path');
const { optimize } = require('svgo');

const SVG_DIR = path.join(__dirname, '../icons/svg');
const OUTPUT = path.join(__dirname, '../icons/sprite/sprite.svg');

function buildSprite() {
  const files = fs.readdirSync(SVG_DIR).filter(f => f.endsWith('.svg'));
  let symbols = '';

  files.forEach(file => {
    const name = path.basename(file, '.svg');
    const svg = fs.readFileSync(path.join(SVG_DIR, file), 'utf8');

    const optimized = optimize(svg, {
      plugins: [
        'preset-default',
        'removeDimensions',
        { name: 'removeAttrs', params: { attrs: '(fill|stroke)' } }
      ]
    });

    const content = optimized.data
      .replace(/<svg[^>]*>/, '')
      .replace(/<\/svg>/, '');

    const viewBox = optimized.data.match(/viewBox="([^"]+)"/)?.[1] || '0 0 24 24';

    symbols += `<symbol id="icon-${name}" viewBox="${viewBox}">${content}</symbol>\n`;
  });

  const sprite = `<svg xmlns="http://www.w3.org/2000/svg" style="display:none">\n${symbols}</svg>`;

  fs.writeFileSync(OUTPUT, sprite);
  console.log(`✅ Sprite built: ${files.length} icons`);
}

buildSprite();
```

### SVG Sprite Kullanımı

```html
<!-- Sprite'ı sayfaya ekle (bir kez) -->
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
  <symbol id="icon-play" viewBox="0 0 24 24">
    <path d="M8 5v14l11-7z"/>
  </symbol>
  <symbol id="icon-pause" viewBox="0 0 24 24">
    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
  </symbol>
  <symbol id="icon-heart" viewBox="0 0 24 24">
    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
  </symbol>
</svg>

<!-- Kullanım -->
<svg class="icon icon--play" aria-hidden="true">
  <use href="#icon-play"></use>
</svg>

<svg class="icon icon--heart" aria-hidden="true">
  <use href="#icon-heart"></use>
</svg>
```

### Inline SVG Kullanımı

```html
<!-- Doğrudan inline SVG -->
<button class="btn btn--icon" aria-label="Oynat">
  <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <polygon points="5 3 19 12 5 21 5 3"/>
  </svg>
</button>
```

### Icon Component (JavaScript)

```javascript
// components/icon.js
class Icon {
  static sprites = null;

  static async loadSprites() {
    if (this.sprites) return this.sprites;

    try {
      const response = await fetch('/icons/sprite/sprite.svg');
      const text = await response.text();

      const container = document.createElement('div');
      container.innerHTML = text;
      container.style.display = 'none';
      document.body.appendChild(container);

      this.sprites = container;
      return this.sprites;
    } catch (error) {
      console.error('Icon sprite load failed:', error);
      return null;
    }
  }

  static create(name, options = {}) {
    const {
      size = 24,
      className = 'icon',
      ariaLabel = null,
      ariaHidden = true
    } = options;

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.classList.add(className);
    svg.setAttribute('width', size);
    svg.setAttribute('height', size);
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '2');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');

    if (ariaLabel) {
      svg.setAttribute('role', 'img');
      svg.setAttribute('aria-label', ariaLabel);
    } else {
      svg.setAttribute('aria-hidden', 'true');
    }

    const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
    use.setAttributeNS('http://www.w3.org/1999/xlink', 'href', `#icon-${name}`);
    svg.appendChild(use);

    return svg;
  }

  static render(name, options = {}) {
    const svg = this.create(name, options);
    return svg.outerHTML;
  }
}

// Usage
document.querySelectorAll('[data-icon]').forEach(el => {
  const name = el.dataset.icon;
  const size = parseInt(el.dataset.iconSize) || 24;
  const icon = Icon.create(name, { size });
  el.appendChild(icon);
});
```

### SCSS Icon Stilleri

```scss
// components/_icon.scss

.icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1em;
  height: 1em;
  vertical-align: middle;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;

  // Sizes
  &--xs { font-size: 0.75rem; }
  &--sm { font-size: 1rem; }
  &--md { font-size: 1.25rem; }
  &--lg { font-size: 1.5rem; }
  &--xl { font-size: 2rem; }
  &--2xl { font-size: 2.5rem; }

  // Colors
  &--primary { color: var(--color-primary); }
  &--secondary { color: var(--color-secondary); }
  &--muted { color: var(--color-text-muted); }
  &--success { color: var(--color-success); }
  &--warning { color: var(--color-warning); }
  &--error { color: var(--color-error); }

  // Filled variant
  &--filled {
    fill: currentColor;
    stroke: none;
  }

  // Spin animation
  &--spin {
    animation: icon-spin 1s linear infinite;
  }
}

@keyframes icon-spin {
  to { transform: rotate(360deg); }
}

// Icon button wrapper
.icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  padding: 0;
  background: transparent;
  border: none;
  border-radius: $radius-full;
  color: var(--color-text);
  cursor: pointer;
  transition: background 0.2s ease;

  &:hover {
    background: var(--color-bg-subtle);
  }

  .icon {
    width: 20px;
    height: 20px;
  }
}
```

### SVG Icon stilleri

```scss
// icons/_svg-icon.scss

// İkonları SVG sprite ile kullanırken theme desteği
.svg-icon {
  width: 24px;
  height: 24px;
  fill: currentColor;

  // Theme-aware colors
  .theme--light & {
    color: var(--color-text);
  }

  .theme--dark & {
    color: var(--color-text);
  }

  // Hover state
  .btn:hover &,
  .icon-btn:hover & {
    color: var(--color-primary);
  }
}

// Specific icon overrides
.icon--play {
  fill: currentColor;
}

.icon--heart {
  transition: transform 0.2s ease, fill 0.2s ease;

  &.is-active {
    fill: var(--color-error);
    animation: icon-pop 0.4s ease;
  }
}

@keyframes icon-pop {
  0% { transform: scale(1); }
  50% { transform: scale(1.3); }
  100% { transform: scale(1); }
}
```

### Icon Font (Opsiyonel)

```scss
// icon-font/_icon-font.scss

@font-face {
  font-family: 'COREMUSIC Icons';
  src: url('/fonts/coremusic-icons.woff2') format('woff2'),
       url('/fonts/coremusic-icons.woff') format('woff');
  font-weight: normal;
  font-style: normal;
  font-display: swap;
}

[class^="icon-"],
[class*=" icon-"] {
  font-family: 'COREMUSIC Icons' !important;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  text-transform: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.icon-play::before { content: "\e900"; }
.icon-pause::before { content: "\e901"; }
.icon-heart::before { content: "\e902"; }
.icon-search::before { content: "\e903"; }
// ... diğer ikonlar
```

## İkon Boyut Rehberi

| Kullanım | Boyut | Stroke Width |
|---|---|---|
| Inline text | 16px | 2px |
| Button icon | 20px | 2px |
| Navigation | 24px | 2px |
| Hero/icon display | 32px | 1.5px |
| Large feature | 48px | 1.5px |

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| svgo | 3.0+ | SVG optimization |
| svg-sprite | 1.5+ | Sprite generation |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Orta (Visual consistency)
