---
type: architecture
category: l3
title: "JS Module Architecture"
date: 2026-08-21
updated: 2026-08-21
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# JS Module Architecture

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[vanilla-js-rules]]

---

## 1. Amaç

home.coremusic.net frontend JavaScript modül yapısını tanımlar. Tek `main.js` dosyası YASAK — modüller ayrı dosyalara bölünmüştür.

---

## 2. Dizin Yapısı

```
assets.coremusic.net/js/
├── core/                        ← Temel altyapı (bağımsız)
│   ├── EventBus.js                Pub/sub (tüm modüller bağımlı)
│   ├── CoreMusicApp.js            Lifecycle manager
│   └── module-loader.js           Dinamik modül yükleme
├── managers/                    ← Durum yönetimi (EventBus'e bağımlı)
│   ├── DeviceManager.js           Cihaz tespiti (device-loader.js bridge)
│   ├── ThemeManager.js            ADR-044 gender theme
│   └── ViewModeManager.js         ADR-045 view mode
├── features/                    ← Sayfa özellikleri (EventBus'e bağımlı)
│   ├── PlayerController.js        State machine (STOPPED/PLAYING/PAUSED)
│   ├── WidgetManager.js           Home widgets
│   ├── CardManager.js             Event delegation
│   ├── ScrollManager.js           Route scroll restore
│   └── TouchManager.js            Embedded touch gestures
├── router/                      ← SPA navigasyonu (mevcut modüller)
│   ├── SPARouterAdapter.js        Router.js bridge
│   ├── Router.js                  Ana SPA router
│   ├── GuardPipeline.js           Client-side guard'lar
│   ├── CacheLayer.js              Route content caching
│   ├── DomPatcher.js              DOM patching (DOMParser)
│   ├── ContentPatcher.js          HTML content update
│   ├── CsrfSyncManager.js         CSRF token sync
│   ├── FetchWrapper.js            HTTP fetch wrapper
│   └── ... (21+ modül)
└── main.js                      ← Entry point: import + init (10-20 satır)
```

---

## 3. Modül Bağımlılık Sırası

```
EventBus (bağımsız — Hiçbir modüle bağımlı değil)
  │
  ├──→ DeviceManager (EventBus)
  │      └──→ TouchManager (EventBus + DeviceManager)
  │
  ├──→ ThemeManager (EventBus)
  │
  ├──→ ViewModeManager (EventBus)
  │
  ├──→ SPARouterAdapter (EventBus + Router.js)
  │      └──→ ScrollManager (EventBus + Router)
  │
  ├──→ PlayerController (EventBus)
  │
  └──→ WidgetManager (EventBus)
         └──→ CardManager (EventBus)
```

---

## 4. Modül Detayları

### 4.1 — EventBus (core/EventBus.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Pub/sub iletişim sistemi |
| **Bağımlılık** | Yok (temel modül) |
| **State** | `#listeners: Map<string, Set<Function>>` |
| **Methods** | `on(event, fn)`, `off(event, fn)`, `emit(event, data)`, `once(event, fn)`, `destroy()` |

### 4.2 — CoreMusicApp (core/CoreMusicApp.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Uygulama lifecycle yönetimi |
| **Bağımlılık** | EventBus |
| **State** | `#modules: Map`, `#state: 'idle'|'booting'|'running'|'destroyed'` |
| **Methods** | `init()`, `destroy()`, `getModule(name)`, `registerModule(name, instance)` |

### 4.3 — DeviceManager (managers/DeviceManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Cihaz tespiti + CSS yükleme |
| **Bağımlılık** | EventBus + device-loader.js |
| **State** | `#currentDevice: string`, `#breakpoints: Object` |
| **Events** | `devicechange { device, previous }` |
| **Breakpoints** | phone≤767, tablet 768-1024(h>600), embedded≤1024(h≤600), laptop≤1440, desktop≤2560, 4k-tv≤3840, 4k-monitor≥3841 |

### 4.4 — ThemeManager (managers/ThemeManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Gender-based tema motoru (ADR-044) |
| **Bağımlılık** | EventBus |
| **State** | `#currentTheme: 'female'|'male'|'neutral'` |
| **Events** | `themechange { gender, tokens }` |
| **CSS Vars** | `--accent: #ff4fd8 (female), #4f8fff (male), #a855f7 (neutral)` |

### 4.5 — ViewModeManager (managers/ViewModeManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Görünüm modu yönetimi (ADR-045) |
| **Bağımlılık** | EventBus |
| **State** | `#currentMode: 'home'|'pro'|'studio'|'car'` |
| **Events** | `viewmodechange { viewMode }` |

### 4.6 — PlayerController (features/PlayerController.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Footer player state machine |
| **Bağımlılık** | EventBus |
| **State** | `#status: 'STOPPED'|'PLAYING'|'PAUSED'`, `#volume`, `#shuffle`, `#repeat` |
| **DOM Targets** | `[data-action]` buttons, `.footer__progress-bar`, `.footer__volume-slider` |
| **Events** | `player:play`, `player:pause`, `player:stop`, `player:next`, `player:prev` |
| **Actions** | play, pause, stop, prev, next, volume, mute, shuffle, repeat |

### 4.7 — WidgetManager (features/WidgetManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Home right-panel widgets |
| **Bağımlılık** | EventBus |
| **State** | `#widgets: Map<string, Widget>` |
| **Widgets** | clock (setInterval), weather (placeholder), speakers (placeholder), folders |
| **DOM Targets** | `.home-widget`, `.home-widget__title` |

### 4.8 — CardManager (features/CardManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Media card etkileşimleri |
| **Bağımlılık** | EventBus |
| **Pattern** | Event delegation (tek listener) |
| **DOM Targets** | `.card-grid`, `.media-card`, `.mini-card` |
| **Events** | `card:click { id, type, title, artist }` |

### 4.9 — ScrollManager (features/ScrollManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Route bazlı scroll pozisyonu |
| **Bağımlılık** | EventBus + Router |
| **State** | `#positions: Map<url, scrollY>` |

### 4.10 — TouchManager (features/TouchManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Embedded touch gestures |
| **Bağımlılık** | EventBus + DeviceManager |
| **Conditions** | Sadece `device === 'embedded'` |
| **Gestures** | swipe (yatay scroll), long-press (500ms) |

---

## 5. main.js Entry Point

```javascript
/**
 * CoreMusic — main.js v5.0.0
 * Entry point: Modülleri import et ve başlat
 * @module main
 */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';
import DeviceManager from './managers/DeviceManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';
import SPARouterAdapter from './router/SPARouterAdapter.js';
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

(function () {
    'use strict';
    if (typeof history.pushState !== 'function') return;

    const app = new CoreMusicApp({
        modules: {
            EventBus, DeviceManager, ThemeManager, ViewModeManager,
            SPARouterAdapter, PlayerController, WidgetManager,
            CardManager, ScrollManager, TouchManager
        }
    });

    document.addEventListener('DOMContentLoaded', () => app.init());

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.App = app;
    window.CoreMusic.version = '5.0.0';
})();
```

---

## 6. Modül Şablonu

Her yeni modül bu şablonu kullanır:

```javascript
/**
 * CoreMusic — [Modül Adı]
 * [Açıklama]
 *
 * @module [kategori]/[ModülAdı]
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class [ModülAdı] {
    /** @type {import('../core/EventBus.js').default} */
    #eventBus;

    /**
     * @param {import('../core/EventBus.js').default} eventBus
     */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    /** Modül başlatma */
    init() {
        this.#bindEvents();
    }

    /** Event'leri bağla */
    #bindEvents() {
        // Event listener'lar
    }

    /** Kaynak temizleme */
    destroy() {
        // RemoveEventListener'lar
    }
}
```

---

## 7. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|--------------|
| 1 | Tek main.js YASAK — modüller ayrı dosya | Kod revert edilir |
| 2 | `var` YASAK — sadece `const`/`let` | Kod geçersiz |
| 3 | `innerHTML` YASAK — `textContent` | XSS açığı |
| 4 | ES Modules `import`/`export` zorunlu | Bağımlılık ihlali |
| 5 | `#private` field (ES2022) | Encapsulation ihlali |
| 6 | Event delegation (tek listener) | Performans ihlali |
| 7 | `AbortController` fetch için | Memory leak |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[vanilla-js-rules]] | JS kuralları |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Modül Sayısı** | 14 (11 özellik + 3 core) |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-21
**Mode:** Red Team · Human Mode · Truth Mode
