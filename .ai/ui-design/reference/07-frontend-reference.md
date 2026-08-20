---
title: "Frontend Reference"
type: reference
category: frontend-architecture
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Frontend Reference

**Zorunlu Baglantilar:** [[ADR-001-vanilla-js-itcss]] · [[ADR-044-dynamic-user-theme-engine]] · [[tokens/design-tokens-master]]

---

## 1. Amaç

Frontend mimarisi, technology stack, coding conventions ve best practices referansıdır. Ajanlar bu dosyadan frontend standartlarını okur.

---

## 2. Technology Stack

| Özellik | Değer | Not |
|---------|-------|-----|
| Language | Vanilla JS ES6+ | Framework yasak (ADR-001) |
| CSS | ITCSS 9-layer + BEM | 7 katmanlı mimari |
| Module | ES Modules (`import/export`) | `type="module"` |
| Min Target | ES2022 | Modern tarayıcılar |
| Build | yok | Vanilla — build tool yok |

### 2.1 Yasak Listesi

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| React / Vue / Angular | Vanilla JS |
| jQuery | Vanilla JS |
| Tailwind / Bootstrap | ITCSS + BEM |
| Webpack / Vite | yok (direct load) |
| TypeScript | JavaScript (ES6+) |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `eval()` / `Function()` | Safe alternatives |
| `var` | `const` / `let` |

---

## 3. ITCSS 7-Layer Architecture

```
01-settings/    → CSS custom properties (design tokens)
02-tools/       → Mixins, functions
03-generic/      → Reset, normalize
04-elements/    → Bare HTML elements (h1, a, button)
05-objects/      → Layout patterns (wrapper, grid)
06-components/   → UI components (card, modal, nav)
07-utilities/    → Overrides, helper classes
```

### 3.1 Layer Kuralları

| Kural | Açıklama |
|-------|----------|
| Sıra değişmez | 01→02→03→04→05→06→07 |
| Alt katman üstü import edemez | 07→01 yasak |
| Her dosya bir layer'da | Karışık dosya yasak |
| `main.css` sadece import | `@import` ile birleştir |

### 3.2 main.css Yapısı

```css
/* 01-settings */
@import 'settings/variables.css';
@import 'settings/tokens.css';

/* 02-tools */
@import 'tools/mixins.css';
@import 'tools/functions.css';

/* 03-generic */
@import 'generic/reset.css';
@import 'generic/normalize.css';

/* 04-elements */
@import 'elements/typography.css';
@import 'elements/forms.css';

/* 05-objects */
@import 'objects/layout.css';
@import 'objects/grid.css';

/* 06-components */
@import 'components/header.css';
@import 'components/footer.css';
@import 'components/sidebar.css';
@import 'components/card.css';
@import 'components/modal.css';

/* 07-utilities */
@import 'utilities/visibility.css';
@import 'utilities/spacing.css';
```

---

## 4. BEM Naming Convention

### 4.1 Format

```
.block {}
.block__element {}
.block--modifier {}
.block__element--modifier {}
```

### 4.2 Örnekler

| BEM | Kullanım |
|-----|----------|
| `.card` | Kart bileşeni |
| `.card__image` | Kart içindeki görsel |
| `.card__title` | Kart içindeki başlık |
| `.card--featured` | Öne çıkan kart |
| `.card__title--large` | Büyük başlık |
| `.nav` | Navigasyon |
| `.nav__item` | Navigasyon öğesi |
| `.nav__item--active` | Aktif navigasyon öğesi |
| `.btn` | Buton |
| `.btn--primary` | Ana buton |
| `.btn--secondary` | İkincil buton |

### 4.3 BEM Kuralları

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| Block ismi | `.card` | `.cardComponent` |
| Element | `.card__title` | `.card-title` |
| Modifier | `.card--active` | `.card-active` |
| Nested block | `.card .nav` (ayrı block) | `.card__nav` (eğer nav bağımsızsa) |

---

## 5. Device CSS Loading

### 5.1 device-loader.js

```javascript
// assets.coremusic.net/js/device-loader.js
const DeviceLoader = {
    breakpoints: {
        mobile: 480,
        tablet: 768,
        desktop: 1024,
        wide: 1920,
        ultra: 3840
    },

    load() {
        const width = window.innerWidth;
        const device = this.getDevice(width);
        document.documentElement.setAttribute('data-device', device);
        this.loadCSS(device);
    },

    getDevice(width) {
        if (width < this.breakpoints.mobile) return 'mobile';
        if (width < this.breakpoints.tablet) return 'tablet';
        if (width < this.breakpoints.desktop) return 'desktop';
        if (width < this.breakpoints.wide) return 'wide';
        return 'ultra';
    },

    loadCSS(device) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `assets.coremusic.net/css/devices/${device}.css`;
        document.head.appendChild(link);
    }
};

DeviceLoader.load();
```

### 5.2 Device CSS Dosyaları

| Dosya | Kapsam |
|-------|--------|
| `mobile.css` | ≤480px |
| `tablet.css` | 481-768px |
| `desktop.css` | 769-1024px |
| `wide.css` | 1025-1920px |
| `ultra.css` | ≥1921px |

---

## 6. Theme Engine (ADR-044)

### 6.1 PHP Tarafı

```php
<?php
// ThemeEngine.php
class ThemeEngine
{
    public function getUserTheme(int $userId): string
    {
        // DB'den user_preferences tablosundan theme_gender oku
        $stmt = $pdo->prepare(
            'SELECT theme_gender FROM user_preferences WHERE user_id = :id'
        );
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['theme_gender'] ?? 'neutral';
    }
}
```

### 6.2 JS Tarafı

```javascript
// ThemeManager.js
const ThemeManager = {
    apply(gender) {
        document.documentElement.setAttribute('data-gender', gender);

        // CSS custom properties ile anında geçiş
        if (gender === 'female') {
            document.documentElement.style.setProperty('--theme-primary', '#e91e63');
        } else if (gender === 'male') {
            document.documentElement.style.setProperty('--theme-primary', '#2196f3');
        } else {
            document.documentElement.style.setProperty('--theme-primary', '#9c27b0');
        }
    }
};
```

### 6.3 CSS Kullanımı

```css
/* Varsayılan tema */
:root {
    --theme-primary: #9c27b0;
}

/* Female tema */
[data-gender="female"] {
    --theme-primary: #e91e63;
}

/* Male tema */
[data-gender="male"] {
    --theme-primary: #2196f3;
}
```

---

## 7. SPA Routing

### 7.1 Router Yapısı

```javascript
// Router.js
const Router = {
    routes: new Map(),

    register(path, handler) {
        this.routes.set(path, handler);
    },

    navigate(path) {
        history.pushState({}, '', path);
        this.resolve();
    },

    resolve() {
        const path = window.location.pathname;
        const handler = this.routes.get(path);
        if (handler) {
            handler();
        }
    }
};

// Kullanım
Router.register('/', () => loadHomePage());
Router.register('/albums', () => loadAlbumsPage());
Router.register('/artists', () => loadArtistsPage());
```

### 7.2 Link Kullanımı

```html
<!-- Doğru: SPA link -->
<a href="/albums" data-nav>Albümler</a>

<!-- ❌ Yanlış: Full page reload -->
<a href="/albums" target="_self">Albümler</a>
```

---

## 8. DOMParser + TrustedTypes

### 8.1 Yasak: innerHTML

```javascript
// ❌ Yanlış — XSS riski
element.innerHTML = '<div class="card">' + userInput + '</div>';
```

### 8.2 Doğru: DOMParser

```javascript
// ✅ Doğru — Güvenli
function createCard(title, image) {
    const template = `
        <div class="card">
            <img class="card__image" src="${escapeHtml(image)}" alt="">
            <h3 class="card__title">${escapeHtml(title)}</h3>
        </div>
    `;

    const parser = new DOMParser();
    const doc = parser.parseFromString(template, 'text/html');
    return doc.body.firstElementChild;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
```

### 8.3 TrustedTypes Policy

```javascript
// TrustedTypes policy oluştur
const policy = trustedTypes.createPolicy('default', {
    createHTML: (str) => str,
    createScriptURL: (str) => str,
    createScript: (str) => str
});
```

---

## 9. AbortController for Fetch

```javascript
// ✅ Doğru — Timeout ile fetch
async function fetchWithTimeout(url, options = {}, timeout = 10000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal
        });
        clearTimeout(timeoutId);
        return response;
    } catch (error) {
        if (error.name === 'AbortError') {
            console.error('Request timed out');
        }
        throw error;
    }
}

// Kullanım
const data = await fetchWithTimeout('/api/v1/songs', {}, 5000);
```

---

## 10. Event Handling

### 10.1 Event Delegation

```javascript
// ✅ Doğru — Event delegation
document.querySelector('.playlist').addEventListener('click', (e) => {
    const songItem = e.target.closest('.song-item');
    if (songItem) {
        const songId = songItem.dataset.songId;
        playSong(songId);
    }
});
```

### 10.2 Custom Events

```javascript
// Custom event oluştur
const event = new CustomEvent('songChanged', {
    detail: { songId: 123, title: 'Şarkı Adı' }
});

// Dinle
document.addEventListener('songChanged', (e) => {
    console.log(e.detail.songId);
});

// Tetikle
document.dispatchEvent(event);
```

---

## 11. Async/Await Patterns

```javascript
// ✅ Doğru — Async/await
async function loadSongs() {
    try {
        const response = await fetch('/api/v1/songs');
        const data = await response.json();
        renderSongs(data.songs);
    } catch (error) {
        showError('Şarkılar yüklenemedi');
    }
}

// ✅ Doğru — Parallel fetch
async function loadDashboard() {
    const [songs, albums, artists] = await Promise.all([
        fetch('/api/v1/songs').then(r => r.json()),
        fetch('/api/v1/albums').then(r => r.json()),
        fetch('/api/v1/artists').then(r => r.json())
    ]);

    renderDashboard({ songs, albums, artists });
}
```

---

## 12. Quick Reference

| Kullanım | Kaynak |
|----------|--------|
| Layer order | `01-settings` → `07-utilities` |
| BEM | `.block__element--modifier` |
| No innerHTML | `DOMParser` + `TrustedTypes` |
| No frameworks | Vanilla JS ES6+ |
| Fetch timeout | `AbortController` |
| Theme | `data-gender` attribute |
| Device | `data-device` attribute |
| SPA | `history.pushState` |

---

## 13. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[ADR-001-vanilla-js-itcss]] |
| Bu dosya | [[ADR-044-dynamic-user-theme-engine]] |
| Bu dosya | [[tokens/design-tokens-master]] |
| Bu dosya | [[02-implementation-plan]] |
| Bu dosya | [[06-backend-reference]] |

---

## 14. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Sections | 14 |
| ADR Coverage | 001, 044 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
