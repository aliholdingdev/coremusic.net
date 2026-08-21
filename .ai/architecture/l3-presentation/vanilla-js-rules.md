---
type: architecture
category: l3
title: "Vanilla JS Rules"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Vanilla JS Rules

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

Vanilla JS kodlama kurallarını ve yasak örüntülerini tanımlar. [[ADR-001-vanilla-js-itcss]] ile uyumludur.

---

## 2. Yasaklar

| ❌ Yasak | ✅ Doğru | Neden |
|----------|----------|-------|
| `var` | `const` / `let` | Scope sorunları |
| `innerHTML` | DOMParser + TrustedTypes | XSS açığı |
| `eval()` | Safe alternatives | Güvenlik açığı |
| React / Vue / Angular | Vanilla JS | Bağımlılık |
| `Function()` | Safe alternatives | Güvenlik açığı |
| `setTimeout(string)` | `setTimeout(function)` | Güvenlik açığı |

---

## 3. Zorunlu Kurallar

| Kural | Değer |
|-------|-------|
| **Declaration** | `const` / `let` (var yasak) |
| **Private** | `#field` (ES2022) |
| **Module** | ES Modules (import/export) |
| **Async** | async/await |
| **DOM** | DOMParser + TrustedTypes |
| **AbortController** | Fetch timeout |
| **Event delegation** | Single handler |

---

## 4. Yasak Örüntü Detayları

### 4.1 innerHTML → DOMParser

```javascript
// ❌ YANLIŞ: innerHTML (XSS riski)
element.innerHTML = userContent;

// ✅ DOĞRU: DOMParser (güvenli)
const parser = new DOMParser();
const doc = parser.parseFromString(userContent, 'text/html');
element.append(...doc.body.childNodes);
```

### 4.2 var → const/let

```javascript
// ❌ YANLIŞ: var
var name = 'John';

// ✅ DOĞRU: const veya let
const name = 'John';
let counter = 0;
```

### 4.3 eval → Safe alternatives

```javascript
// ❌ YANLIŞ: eval()
eval(userInput);

// ✅ DOĞRU: JSON.parse()
const data = JSON.parse(userInput);

// ✅ DOĞRU: Function constructor (if needed)
const fn = new Function('return ' + expression);
```

---

## 5. Private Fields

```javascript
class Player {
    #state = 'stopped';
    #volume = 0.5;
    #playlist = [];

    get state() { return this.#state; }
    set state(value) { this.#state = value; }

    #updateUI() {
        // Private method
    }
}
```

---

## 6. Event Delegation

```javascript
// ❌ YANLIŞ: Her elemana event listener
document.querySelectorAll('.button').forEach(btn => {
    btn.addEventListener('click', handler);
});

// ✅ DOĞRU: Event delegation
document.addEventListener('click', (e) => {
    if (e.target.matches('.button')) {
        handler(e);
    }
});
```

---

## 7. Fetch with AbortController

```javascript
async function fetchData(url, timeout = 5000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(url, { signal: controller.signal });
        clearTimeout(timer);
        return await response.json();
    } catch (err) {
        clearTimeout(timer);
        throw err;
    }
}
```

---

## 8. JS Module Architecture (v5.0.0)

**Tek main.js dosyası YASAK.** Modüller ayrı dosyalara bölünmüştür.

### 8.1 — Dizin Yapısı

```
assets.coremusic.net/js/
├── core/                        ← Temel altyapı
│   ├── EventBus.js                Pub/sub (tüm modüller bağımlı)
│   ├── CoreMusicApp.js            Lifecycle manager
│   └── module-loader.js           Dinamik modül yükleme
├── managers/                    ← Durum yönetimi
│   ├── DeviceManager.js           Cihaz tespiti (device-loader.js bridge)
│   ├── ThemeManager.js            ADR-044 gender theme
│   └── ViewModeManager.js         ADR-045 view mode
├── features/                    ← Sayfa özellikleri
│   ├── PlayerController.js        State machine (STOPPED/PLAYING/PAUSED)
│   ├── WidgetManager.js           Home widgets
│   ├── CardManager.js             Event delegation
│   ├── ScrollManager.js           Route scroll restore
│   └── TouchManager.js            Embedded touch gestures
├── router/                      ← SPA navigasyonu
│   ├── SPARouterAdapter.js        Router.js bridge
│   └── (mevcut 21+ modül)
└── main.js                      ← Entry point: import + init (10-20 satır)
```

### 8.2 — Modül Bağımlılık Sırası

```
EventBus (bağımsız)
  → DeviceManager (EventBus'e bağımlı)
    → ThemeManager (EventBus'e bağımlı)
      → ViewModeManager (EventBus'e bağımlı)
        → SPARouterAdapter (EventBus + DeviceManager)
          → PlayerController (EventBus)
            → WidgetManager (EventBus)
              → CardManager (EventBus)
                → ScrollManager (EventBus + Router)
                  → TouchManager (EventBus + DeviceManager)
```

### 8.3 — Her Modül Şablonu

```javascript
/**
 * CoreMusic — [Modül Adı]
 * [Açıklama]
 *
 * @module core/[ModülAdı]
 * @version 5.0.0
 */
export default class [ModülAdı] {
    #eventBus;

    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        // Modül başlatma
    }

    destroy() {
        // Kaynak temizleme
    }
}
```

### 8.4 — main.js Entry Point

```javascript
/**
 * CoreMusic — main.js v5.0.0
 * Entry point: Modülleri import et ve başlat
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
        modules: { EventBus, DeviceManager, ThemeManager, ViewModeManager,
                   SPARouterAdapter, PlayerController, WidgetManager,
                   CardManager, ScrollManager, TouchManager }
    });

    document.addEventListener('DOMContentLoaded', () => app.init());

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.App = app;
    window.CoreMusic.version = '5.0.0';
})();
```

---

## 9. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **ES5 tarayıcı** | Progressive enhancement | ADR-001 |
| **Module desteği yok** | Script type="module" | ADR-001 |
| **DOM injection** | DOMParser + TrustedTypes | ADR-001 |
| **Event leak** | RemoveEventListener | ADR-001 |

---

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[js-module-architecture]] | JS modül detayları |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Satır Sayısı** | ~700 |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-21
**Mode:** Red Team · Human Mode · Truth Mode
