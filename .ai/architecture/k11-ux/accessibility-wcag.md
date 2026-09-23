---
title: "WCAG 2.2 AA Erişilebilirlik"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# WCAG 2.2 AA Erişilebilirlik

## Genel Bakış

COREMUSIC, WCAG 2.2 AA standartlarına tam uyumluluk hedefler. Tüm kullanıcılar (görme, işitme, motor ve bilişsel engelli) için erişilebilir bir deneyim sunar. ARIA attributes, focus management, screen reader desteği ve klavye navigasyonu temel gereksinimlerdir.

## WCAG 2.2 Yeni Kriterler

```
WCAG 2.2 Ekstra Kriterler (2.2'de eklenenler):
├── 2.4.11 Focus Not Obscured (Minimum)
├── 2.4.12 Focus Not Obscured (Enhanced)
├── 2.4.13 Focus Appearance
├── 2.5.7 Dragging Movements
├── 2.5.8 Target Size (Minimum)
├── 3.2.6 Consistent Help
├── 3.3.7 Redundant Entry
└── 3.3.8 Accessible Authentication (Minimum)
```

## Temel İlkeler

### 1. Perceivable (Algılanabilir)

```html
<!-- Tüm images için alt text -->
<img src="cover.webp" alt="Tarkan - Madonna albüm kapağı" />

<!-- Dekoratif imgs için boş alt -->
<img src="decoration.svg" alt="" role="presentation" />

<!-- Video için altyazı -->
<video controls>
  <source src="music.mp4" type="video/mp4" />
  <track kind="captions" src="captions.tr.vtt" srclang="tr" label="Türkçe" default />
</video>

<!-- Renk tek başına bilgi taşımaz -->
<span class="status status--success" aria-label="Başarılı">
  <svg aria-hidden="true"><!-- check icon --></svg>
  Kaydedildi
</span>
```

### 2. Operable (Kullanılabilir)

```html
<!-- Klavye erişilebilirliği -->
<div class="player" role="region" aria-label="Müzik player">
  <!-- Tüm interactive elementler keyboard ile erişilebilir -->
  <button class="player__btn" tabindex="0" aria-label="Oynat">
    <svg aria-hidden="true"><!-- play icon --></svg>
  </button>

  <!-- Skip link -->
  <a href="#main-content" class="skip-link">
    Ana içeriğe geç
  </a>

  <!-- Focus trap modal içinde -->
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <h2 id="modal-title">Şarkı Ekle</h2>
    <form>
      <input type="text" aria-label="Şarkı adı" />
      <button type="submit">Ekle</button>
      <button type="button" data-dismiss="modal">Kapat</button>
    </form>
  </div>
</div>
```

### 3. Understandable (Anlaşılabilir)

```html
<!-- Form erişilebilirliği -->
<form novalidate>
  <div class="form__group">
    <label class="form__label" for="email">
      E-posta adresiniz
      <span class="form__required" aria-hidden="true">*</span>
    </label>
    <input
      class="form__input"
      type="email"
      id="email"
      name="email"
      required
      aria-required="true"
      aria-invalid="false"
      aria-describedby="email-hint email-error"
      autocomplete="email"
    />
    <span class="form__hint" id="email-hint">
      Örnek: ornek@domain.com
    </span>
    <span class="form__error" id="email-error" role="alert" hidden>
      Geçerli bir e-posta adresi girin.
    </span>
  </div>
</form>
```

### 4. Robust (Sağlam)

```html
<!-- Semantik HTML kullanımı -->
<header role="banner">
  <nav role="navigation" aria-label="Ana menü">
    <ul>
      <li><a href="/" aria-current="page">Ana Sayfa</a></li>
      <li><a href="/library">Kütüphane</a></li>
      <li><a href="/search">Ara</a></li>
    </ul>
  </nav>
</header>

<main role="main" id="main-content">
  <h1>COREMUSIC</h1>
</main>

<footer role="contentinfo">
  <p>&copy; 2026 COREMUSIC</p>
</footer>

<!-- ARIA live regions -->
<div aria-live="polite" aria-atomic="true" class="sr-only" id="announcer">
  <!-- Dinamik duyurular buraya -->
</div>
```

## Focus Management

```css
/* Focus stilleri */
:focus-visible {
  outline: 3px solid var(--color-primary);
  outline-offset: 2px;
}

/* Focus ring - improved visibility */
.focus-ring {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
  border-radius: var(--radius-md);
}

/* Focus within */
.form__group:focus-within {
  background: var(--color-primary-light);
}

/* Skip link */
.skip-link {
  position: absolute;
  top: -100%;
  left: 0;
  z-index: 9999;
  padding: var(--space-3) var(--space-4);
  background: var(--color-primary);
  color: var(--color-text-inverse);
  font-weight: 600;
  text-decoration: none;
  border-radius: 0 0 var(--radius-md) 0;
}

.skip-link:focus {
  top: 0;
}

/* Focus trap */
[data-focus-trap] {
  overflow: hidden;
}

[data-focus-trap] .modal {
  position: fixed;
  inset: 0;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Focus within for cards */
.card:focus-within {
  box-shadow: 0 0 0 2px var(--color-primary);
}
```

## Kod Örnekleri

### Focus Trap JavaScript

```javascript
// accessibility/focus-trap.js
class FocusTrap {
  constructor(element) {
    this.element = element;
    this.focusableElements = [];
    this.firstFocusable = null;
    this.lastFocusable = null;
    this.previousFocus = null;
  }

  init() {
    this.focusableElements = this.element.querySelectorAll(
      'a[href], button:not([disabled]), textarea:not([disabled]), ' +
      'input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
    );

    this.firstFocusable = this.focusableElements[0];
    this.lastFocusable = this.focusableElements[this.focusableElements.length - 1];

    this.element.addEventListener('keydown', this.handleKeydown.bind(this));

    // Focus first element
    if (this.firstFocusable) {
      this.firstFocusable.focus();
    }
  }

  handleKeydown(event) {
    if (event.key !== 'Tab') return;

    if (event.shiftKey) {
      // Shift + Tab
      if (document.activeElement === this.firstFocusable) {
        event.preventDefault();
        this.lastFocusable.focus();
      }
    } else {
      // Tab
      if (document.activeElement === this.lastFocusable) {
        event.preventDefault();
        this.firstFocusable.focus();
      }
    }
  }

  activate() {
    this.previousFocus = document.activeElement;
    document.body.setAttribute('data-focus-trap', '');
    this.init();
  }

  deactivate() {
    this.element.removeEventListener('keydown', this.handleKeydown);
    document.body.removeAttribute('data-focus-trap');

    if (this.previousFocus) {
      this.previousFocus.focus();
    }
  }
}
```

### Screen Reader Announcer

```javascript
// accessibility/announcer.js
class ScreenReaderAnnouncer {
  constructor() {
    this.announcer = document.getElementById('announcer');
    if (!this.announcer) {
      this.announcer = document.createElement('div');
      this.announcer.id = 'announcer';
      this.announcer.setAttribute('aria-live', 'polite');
      this.announcer.setAttribute('aria-atomic', 'true');
      this.announcer.classList.add('sr-only');
      document.body.appendChild(this.announcer);
    }
  }

  announce(message, priority = 'polite') {
    this.announcer.setAttribute('aria-live', priority);
    this.announcer.textContent = '';

    // Force reflow
    void this.announcer.offsetHeight;

    this.announcer.textContent = message;
  }

  announceAssertive(message) {
    this.announce(message, 'assertive');
  }
}

// Usage
const announcer = new ScreenReaderAnnouncer();
announcer.announce('Şarkı değiştirildi: Yeni Şarkı Adı');
announcer.announce('Oynatma listesine eklendi');
```

### Keyboard Navigation

```javascript
// accessibility/keyboard-nav.js
class KeyboardNavigation {
  constructor(container, items, options = {}) {
    this.container = container;
    this.items = items;
    this.currentIndex = 0;
    this.orientation = options.orientation || 'vertical';
    this.onSelect = options.onSelect || (() => {});
    this.onFocus = options.onFocus || (() => {});

    this.init();
  }

  init() {
    this.container.addEventListener('keydown', this.handleKeydown.bind(this));
    this.items[0]?.setAttribute('tabindex', '0');

    this.items.forEach((item, index) => {
      item.setAttribute('tabindex', index === 0 ? '0' : '-1');
    });
  }

  handleKeydown(event) {
    const { key } = event;
    let newIndex = this.currentIndex;

    switch (key) {
      case 'ArrowDown':
      case 'ArrowRight':
        event.preventDefault();
        newIndex = (this.currentIndex + 1) % this.items.length;
        break;

      case 'ArrowUp':
      case 'ArrowLeft':
        event.preventDefault();
        newIndex = (this.currentIndex - 1 + this.items.length) % this.items.length;
        break;

      case 'Home':
        event.preventDefault();
        newIndex = 0;
        break;

      case 'End':
        event.preventDefault();
        newIndex = this.items.length - 1;
        break;

      case 'Enter':
      case ' ':
        event.preventDefault();
        this.onSelect(this.items[this.currentIndex], this.currentIndex);
        return;

      default:
        return;
    }

    this.moveTo(newIndex);
  }

  moveTo(index) {
    this.items[this.currentIndex].setAttribute('tabindex', '-1');
    this.items[index].setAttribute('tabindex', '0');
    this.items[index].focus();
    this.currentIndex = index;
    this.onFocus(this.items[index], index);
  }
}
```

### ARIA Roles for Music Player

```html
<!-- Player ARIA yapısı -->
<div class="player" role="region" aria-label="Müzik player">
  <!-- Şarkı bilgisi -->
  <div class="player__info">
    <h2 class="player__title" id="track-title">Şarkı Adı</h2>
    <p class="player__artist" id="track-artist">Sanatçı</p>
  </div>

  <!-- Kontroller -->
  <div class="player__controls" role="toolbar" aria-label="Oynatma kontrolleri">
    <button class="player__btn" aria-label="Karıştır" aria-pressed="false">
      <svg aria-hidden="true"><!-- shuffle icon --></svg>
    </button>
    <button class="player__btn" aria-label="Önceki şarkı">
      <svg aria-hidden="true"><!-- prev icon --></svg>
    </button>
    <button class="player__btn player__btn--play" aria-label="Oynat" aria-pressed="false">
      <svg aria-hidden="true"><!-- play/pause icon --></svg>
    </button>
    <button class="player__btn" aria-label="Sonraki şarkı">
      <svg aria-hidden="true"><!-- next icon --></svg>
    </button>
    <button class="player__btn" aria-label="Tekrarla" aria-pressed="false">
      <svg aria-hidden="true"><!-- repeat icon --></svg>
    </button>
  </div>

  <!-- Progress -->
  <div class="player__progress">
    <span class="sr-only" id="time-current">1:23</span>
    <span class="sr-only" id="time-total">3:45</span>
    <div
      class="player__progress-bar"
      role="slider"
      aria-label="İlerleme"
      aria-valuenow="45"
      aria-valuemin="0"
      aria-valuemax="100"
      aria-valuetext="1:23 / 3:45, %45"
      tabindex="0"
    >
      <div class="player__progress-fill" style="width: 45%"></div>
    </div>
  </div>

  <!-- Volume -->
  <div class="player__volume">
    <button class="player__btn" aria-label="Ses: Yüksek" aria-pressed="false">
      <svg aria-hidden="true"><!-- volume icon --></svg>
    </button>
    <div
      class="player__volume-slider"
      role="slider"
      aria-label="Ses seviyesi"
      aria-valuenow="75"
      aria-valuemin="0"
      aria-valuemax="100"
      aria-valuetext="%75"
      tabindex="0"
    >
      <div class="player__volume-fill" style="width: 75%"></div>
    </div>
  </div>
</div>
```

### Reduced Motion

```css
/* Animasyon tercihlerine saygı */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

### High Contrast

```css
/* Yüksek kontrast desteği */
@media (prefers-contrast: high) {
  :root {
    --color-border: #000000;
    --color-text: #000000;
    --color-bg: #FFFFFF;
    --color-primary: #0000FF;
  }

  .btn {
    border: 2px solid currentColor;
  }

  .card {
    border: 2px solid currentColor;
  }
}

/* Forced colors (Windows High Contrast) */
@media (forced-colors: active) {
  .btn--primary {
    border: 2px solid ButtonText;
  }

  .player__progress-fill {
    background: Highlight;
  }
}
```

## Erişilebilirlik Testleri

```javascript
// accessibility/axe-test.js
import axe from 'axe-core';

// Automated testing
async function runA11yTests() {
  const results = await axe.run(document, {
    rules: {
      'color-contrast': { enabled: true },
      'label': { enabled: true },
      'aria-roles': { enabled: true },
      'keyboard': { enabled: true }
    }
  });

  // Violations
  results.violations.forEach(violation => {
    console.error(`❌ ${violation.id}: ${violation.description}`);
    console.error(`   Impact: ${violation.impact}`);
    violation.nodes.forEach(node => {
      console.error(`   Element: ${node.html}`);
    });
  });

  // Passes
  console.log(`✅ ${results.passes.length} tests passed`);

  return results;
}
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| axe-core | 4.8+ | Automated a11y testing |
| @axe-core/react | 4.8+ | React integration |
| pa11y | 6.1+ | CI/CD a11y testing |
| lighthouse | 11.0+ | A11y audit |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Yasal gereklilik + erişilebilirlik)
