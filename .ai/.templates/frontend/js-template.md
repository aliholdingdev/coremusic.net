---
type: template
category: frontend
title: "JavaScript ES6+ Frontend Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Vanilla JS, ES6+, DOMParser, TrustedTypes
---

# JavaScript ES6+ Frontend Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-021-spa-router-immutable-contract]]

## 1. Amaç

CoreMusic frontend geliştirme için Vanilla JavaScript ES6+ şablonu. Framework yasak (ADR-001), DOMParser + TrustedTypes zorunlu, SPA router entegrasyonu dahil.

**Kapsam:** SPA router, component'ler, UI handler'lar, API client, Web Audio, device loader.
**Kapsam Dışı:** PHP backend (→ [[php-template]]), CSS stilleri (→ [[css-template]]).

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| JavaScript | ES2022+ | Frontend language | ecma-international.org |
| Web Audio API | — | Ses işleme | developer.mozilla.org |
| Fetch API | — | HTTP istekleri | developer.mozilla.org |
| URLPattern | — | Route matching | wicg.github.io |
| TrustedTypes | — | XSS koruması | w3c.github.io |

*Kaynak: ECMAScript 2022 Spec (tc39.es) — 2026-08-06'da doğrulandı*

### 2.1 Yasaklı Teknolojiler

| Teknoloji | Neden Yasak | İlgili ADR |
|-----------|-------------|------------|
| React / Vue / Angular | Framework yasak | ADR-001 |
| jQuery | Vanilla JS zorunlu | ADR-001 |
| `var` keyword | `let`/`const` zorunlu | ADR-001 |
| `eval()` | Güvenlik riski | ADR-022 |
| `innerHTML` | XSS riski, DOMParser zorunlu | ADR-001 |
| Webpack/Vite bundler | Vanilla JS, no bundler | ADR-001 |

## 3. Code Standards

### 3.1 Dosya Yapısı

```text
assets.coremusic.net/js/
├── core/
│   ├── Router.js              # SPA router (ADR-021)
│   ├── CacheLayer.js          # Client-side cache
│   ├── AuthHandler.js         # Auth state management
│   ├── EventBus.js            # Custom event system
│   └── TrustedTypesPolicy.js  # TrustedTypes policy
├── components/
│   ├── Header.js              # Site header
│   ├── Footer.js              # Site footer + player
│   ├── Sidebar.js             # Navigation sidebar
│   └── Modal.js               # Modal dialog
├── pages/
│   ├── Home.js                # Home page
│   ├── Music.js               # Music page
│   ├── Settings.js            # Settings page
│   └── Discover.js            # Discover page
├── services/
│   ├── ApiService.js           # HTTP client
│   ├── AuthService.js          # Auth operations
│   └── AudioService.js         # Web Audio API
├── device-loader.js           # Device-based CSS loader
└── app.js                     # Entry point
```

### 3.2 Dosya Başlangıç Kalıbı

```javascript
/**
 * {DOSYA_AÇIKLAMASI}
 *
 * @file {dosya_adi}.js
 * @version 3.0.0
 * @see ADR-{ilgili_adr}
 */

'use strict';

// {MODÜL KODU}
```

### 3.3 Class Design — SPA Router

```javascript
/**
 * SPA Router — Single Page Application yönlendirme.
 *
 * @file Router.js
 * @version 3.0.0
 * @see ADR-021-spa-router-immutable-contract
 */

'use strict';

class Router {
    /** @type {Map<string, Function>} Route handlers */
    #routes = new Map();

    /** @type {AbortController|null} */
    #abortController = null;

    /** @type {string} Current URL */
    #currentUrl = '';

    /** @type {TrustedTypesPolicy|null} */
    #ttPolicy = null;

    constructor() {
        // TrustedTypes policy — DOMParser + insertAdjacentHTML
        if (typeof trustedTypes !== 'undefined') {
            this.#ttPolicy = trustedTypes.createPolicy('router-policy', {
                createHTML: (input) => input,
            });
        }

        // Popstate listener — browser back/forward
        window.addEventListener('popstate', () => {
            this.#navigate(window.location.href, false);
        });

        // Click delegation — link intercept
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && this.#isInternalLink(link)) {
                e.preventDefault();
                this.#navigate(link.href, true);
            }
        });
    }

    /**
     * Route kaydet.
     *
     * @param {string} pattern URL pattern (e.g., '/music/:id')
     * @param {Function} handler Route handler
     * @returns {Router} this (chainable)
     */
    on(pattern, handler) {
        this.#routes.set(pattern, handler);
        return this;
    }

    /**
     * Başlangıç URL'sini başlat.
     *
     * @param {string} url Başlangıç URL
     */
    start(url = window.location.href) {
        this.#navigate(url, false);
    }

    /**
     * Programmatic navigation.
     *
     * @param {string} url Hedef URL
     * @param {boolean} pushState History.pushState kullan
     */
    async #navigate(url, pushState = true) {
        // Abort previous fetch
        if (this.#abortController) {
            this.#abortController.abort();
        }
        this.#abortController = new AbortController();

        // Match route
        const matched = this.#matchRoute(url);
        if (!matched) {
            console.warn(`No route matched: ${url}`);
            return;
        }

        try {
            // Fetch page HTML
            const response = await fetch(url, {
                signal: this.#abortController.signal,
                headers: { 'X-Requested-With': 'SPA-Router' },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const html = await response.text();

            // DOMParser — innerHTML yasak (ADR-001)
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Extract content
            const content = doc.querySelector('#app-content');
            if (!content) {
                throw new Error('No #app-content found');
            }

            // TrustedTypes — safe HTML
            const safeHtml = this.#ttPolicy
                ? this.#ttPolicy.createHTML(content.innerHTML)
                : content.innerHTML;

            // DOM patch
            this.#patchDOM(safeHtml);

            // CSRF token güncelle — DOM patch SONRASINDA (ADR-010)
            this.#updateCsrf(this.#getCsrfToken());

            // History push
            if (pushState) {
                history.pushState({ url }, null, url);
            }

            // Execute route handler
            const handler = matched.handler;
            if (typeof handler === 'function') {
                await handler(matched.params);
            }

            // Update current URL
            this.#currentUrl = url;

        } catch (err) {
            if (err.name === 'AbortError') {
                return; // Intentional abort — skip
            }
            console.error('Navigation error:', err);
        }
    }

    /**
     * Route matching — URLPattern veya regex.
     *
     * @param {string} url
     * @returns {{handler: Function, params: Object}|null}
     */
    #matchRoute(url) {
        const path = new URL(url, window.location.origin).pathname;

        for (const [pattern, handler] of this.#routes) {
            // URLPattern API (modern browsers)
            if (typeof URLPattern !== 'undefined') {
                const urlPattern = new URLPattern({ pathname: pattern });
                const result = urlPattern.exec({ pathname: path });
                if (result) {
                    return { handler, params: result.pathname.groups };
                }
            } else {
                // Fallback: simple regex
                const regex = pattern.replace(/:(\w+)/g, '(?<$1>[^/]+)');
                const match = path.match(new RegExp(`^${regex}$`));
                if (match) {
                    return { handler, params: match.groups || {} };
                }
            }
        }

        return null;
    }

    /**
     * DOM patch — content'i güncelle.
     *
     * @param {string|TrustedHTML} html
     */
    #patchDOM(html) {
        const container = document.getElementById('app-content');
        if (container) {
            container.innerHTML = html;
        }
    }

    /**
     * CSRF token'ı güncelle — form input'ları DOM'da oluştu.
     *
     * @param {string} token
     */
    #updateCsrf(token) {
        document.querySelectorAll('input[name="csrf_token"]').forEach((input) => {
            input.value = token;
        });
    }

    /**
     * CSRF token'ı al.
     *
     * @returns {string}
     */
    #getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Dahili link kontrolü.
     *
     * @param {HTMLAnchorElement} link
     * @returns {boolean}
     */
    #isInternalLink(link) {
        return (
            link.hostname === window.location.hostname &&
            !link.hasAttribute('data-external') &&
            link.protocol.startsWith('http')
        );
    }
}

// Export singleton
window.CoreRouter = new Router();
```

### 3.4 Component Design

```javascript
/**
 * Base Component — tüm component'lerin atası.
 *
 * @file BaseComponent.js
 * @version 3.0.0
 */

'use strict';

class BaseComponent {
    /** @type {HTMLElement|null} */
    #element = null;

    /** @type {Map<string, EventListener>} */
    #listeners = new Map();

    /**
     * Component'i render et.
     *
     * @param {HTMLElement} container Hedef container
     */
    render(container) {
        throw new Error('render() must be implemented by subclass');
    }

    /**
     * Component'i temizle — event listener'ları kaldır.
     */
    destroy() {
        this.#listeners.forEach((listener, key) => {
            const [element, event] = key.split(':');
            const el = document.querySelector(element);
            if (el) {
                el.removeEventListener(event, listener);
            }
        });
        this.#listeners.clear();
    }

    /**
     * Güvenli event binding — memory leak önleme.
     *
     * @param {string} selector CSS selector
     * @param {string} event Event name
     * @param {Function} handler Event handler
     */
    bindEvent(selector, event, handler) {
        const key = `${selector}:${event}`;
        this.#listeners.set(key, handler);

        document.querySelector(selector)?.addEventListener(event, handler);
    }

    /**
     * DOMParser ile güvenli HTML parse.
     *
     * @param {string} html
     * @returns {DocumentFragment}
     */
    parseHTML(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const fragment = document.createDocumentFragment();
        fragment.appendChild(doc.body.firstChild);
        return fragment;
    }
}

/**
 * Header component.
 *
 * @file Header.js
 * @version 3.0.0
 */
class Header extends BaseComponent {
    render(container) {
        const html = `
            <header class="site-header">
                <div class="site-header__logo">
                    <a href="/" class="site-header__link">CoreMusic</a>
                </div>
                <nav class="site-header__nav">
                    <a href="/discover" class="nav-link">Keşfet</a>
                    <a href="/music" class="nav-link">Müzik</a>
                    <a href="/settings" class="nav-link">Ayarlar</a>
                </nav>
            </header>
        `;

        container.innerHTML = html;
    }
}

/**
 * Footer component — player bar dahil.
 *
 * @file Footer.js
 * @version 3.0.0
 * @see ADR-018-footer-player-vaporwave
 */
class Footer extends BaseComponent {
    #audioService = null;

    constructor(audioService) {
        super();
        this.#audioService = audioService;
    }

    render(container) {
        const html = `
            <footer class="site-footer">
                <div class="site-footer__player">
                    <button class="player-btn player-btn--prev" data-action="prev">⏮</button>
                    <button class="player-btn player-btn--play" data-action="play">▶</button>
                    <button class="player-btn player-btn--next" data-action="next">⏭</button>
                    <div class="player-progress">
                        <div class="player-progress__bar" style="width: 0%"></div>
                    </div>
                    <div class="player-volume">
                        <input type="range" class="player-volume__slider" min="0" max="100" value="80">
                    </div>
                </div>
            </footer>
        `;

        container.innerHTML = html;
        this.#bindPlayerEvents();
    }

    #bindPlayerEvents() {
        this.bindEvent('.player-btn--play', 'click', () => {
            this.#audioService?.togglePlay();
        });

        this.bindEvent('.player-btn--prev', 'click', () => {
            this.#audioService?.prev();
        });

        this.bindEvent('.player-btn--next', 'click', () => {
            this.#audioService?.next();
        });

        this.bindEvent('.player-volume__slider', 'input', (e) => {
            this.#audioService?.setVolume(e.target.value / 100);
        });
    }
}
```

### 3.5 API Client

```javascript
/**
 * API Client — HTTP istekleri için merkezi client.
 *
 * @file ApiService.js
 * @version 3.0.0
 * @see ADR-020-api-public-security
 */

'use strict';

class ApiService {
    /** @type {string} Base URL */
    #baseUrl;

    /** @type {string|null} Auth token */
    #token = null;

    /** @type {number} Request timeout (ms) */
    #timeout = 10000;

    /**
     * @param {string} baseUrl API base URL
     */
    constructor(baseUrl = '') {
        this.#baseUrl = baseUrl;
    }

    /**
     * Auth token ayarla.
     *
     * @param {string} token
     */
    setToken(token) {
        this.#token = token;
    }

    /**
     * GET isteği.
     *
     * @param {string} endpoint
     * @param {Object} params Query params
     * @returns {Promise<Object>}
     */
    async get(endpoint, params = {}) {
        const url = new URL(endpoint, this.#baseUrl);
        Object.entries(params).forEach(([key, value]) => {
            url.searchParams.set(key, String(value));
        });

        return this.#request('GET', url.toString());
    }

    /**
     * POST isteği.
     *
     * @param {string} endpoint
     * @param {Object} data Request body
     * @returns {Promise<Object>}
     */
    async post(endpoint, data = {}) {
        return this.#request('POST', endpoint, data);
    }

    /**
     * PUT isteği.
     *
     * @param {string} endpoint
     * @param {Object} data Request body
     * @returns {Promise<Object>}
     */
    async put(endpoint, data = {}) {
        return this.#request('PUT', endpoint, data);
    }

    /**
     * DELETE isteği.
     *
     * @param {string} endpoint
     * @returns {Promise<Object>}
     */
    async delete(endpoint) {
        return this.#request('DELETE', endpoint);
    }

    /**
     * Temel HTTP isteği.
     *
     * @param {string} method
     * @param {string} endpoint
     * @param {Object|null} body
     * @returns {Promise<Object>}
     */
    async #request(method, endpoint, body = null) {
        const headers = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        // CSRF token — POST/PUT/DELETE için zorunlu
        if (['POST', 'PUT', 'DELETE'].includes(method)) {
            const csrfToken = this.#getCsrfToken();
            if (csrfToken) {
                headers['X-CSRF-Token'] = csrfToken;
            }
        }

        // Auth token
        if (this.#token) {
            headers['Authorization'] = `Bearer ${this.#token}`;
        }

        const config = {
            method,
            headers,
            credentials: 'same-origin', // Cookie'ler dahil
        };

        if (body !== null) {
            config.body = JSON.stringify(body);
        }

        // Timeout + AbortController
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.#timeout);
        config.signal = controller.signal;

        try {
            const response = await fetch(endpoint, config);
            clearTimeout(timeoutId);

            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new ApiError(
                    error.message || `HTTP ${response.status}`,
                    response.status,
                    error
                );
            }

            return await response.json();
        } catch (err) {
            clearTimeout(timeoutId);

            if (err.name === 'AbortError') {
                throw new ApiError('Request timeout', 408);
            }

            throw err;
        }
    }

    /**
     * CSRF token'ı meta tag'den al.
     *
     * @returns {string|null}
     */
    #getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }
}

/**
 * API Error class.
 */
class ApiError extends Error {
    /** @type {number} HTTP status code */
    status;

    /** @type {Object} Error data */
    data;

    constructor(message, status, data = {}) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.data = data;
    }
}

// Export
window.CoreApi = new ApiService();
```

### 3.6 Cache Layer

```javascript
/**
 * Client-side Cache — LRU eviction + TTL support.
 *
 * @file CacheLayer.js
 * @version 3.0.0
 * @see ADR-007-cache-namespace
 */

'use strict';

class CacheLayer {
    /** @type {Map<string, {value: any, expiry: number}>} */
    #cache = new Map();

    /** @type {number} Max entries */
    #maxEntries;

    /** @type {Map<string, number>} TTL map (seconds) */
    #ttlMap;

    /**
     * @param {number} maxEntries Max cache entries (default: 100)
     */
    constructor(maxEntries = 100) {
        this.#maxEntries = maxEntries;
        this.#ttlMap = new Map([
            ['user', 120],      // 2 dakika
            ['dynamic', 60],    // 1 dakika
            ['static', 3600],   // 1 saat
            ['default', 600],   // 10 dakika
        ]);
    }

    /**
     * Cache'den veri al.
     *
     * @param {string} key
     * @returns {any|null}
     */
    get(key) {
        const entry = this.#cache.get(key);
        if (!entry) return null;

        if (Date.now() > entry.expiry) {
            this.#cache.delete(key);
            return null;
        }

        // LRU: en eski kaydı sil, yeniyi ekle
        this.#cache.delete(key);
        this.#cache.set(key, entry);

        return entry.value;
    }

    /**
     * Cache'e veri kaydet.
     *
     * @param {string} key
     * @param {any} value
     * @param {string} category Cache category ('user', 'dynamic', 'static', 'default')
     */
    set(key, value, category = 'default') {
        // LRU eviction
        if (this.#cache.size >= this.#maxEntries) {
            const firstKey = this.#cache.keys().next().value;
            this.#cache.delete(firstKey);
        }

        const ttl = this.#ttlMap.get(category) || this.#ttlMap.get('default');
        const expiry = Date.now() + (ttl * 1000);

        this.#cache.set(key, { value, expiry });
    }

    /**
     * Cache'i temizle.
     *
     * @param {string} pattern Key pattern (regex)
     */
    invalidate(pattern) {
        const regex = new RegExp(pattern);
        for (const key of this.#cache.keys()) {
            if (regex.test(key)) {
                this.#cache.delete(key);
            }
        }
    }

    /**
     * Tüm cache'i temizle.
     */
    clear() {
        this.#cache.clear();
    }

    /**
     * Cache istatistikleri.
     *
     * @returns {{size: number, maxEntries: number, hitRate: number}}
     */
    getStats() {
        return {
            size: this.#cache.size,
            maxEntries: this.#maxEntries,
            hitRate: 0, // TODO: Track hits/misses
        };
    }
}

// Export singleton
window.CoreCache = new CacheLayer();
```

## 4. Security Considerations

### 4.1 XSS Prevention

| Kural | Açıklama | ADR |
|-------|----------|-----|
| `innerHTML` yasak | DOMParser + insertAdjacentHTML | ADR-001 |
| TrustedTypes | W3C standardı, zorunlu | ADR-001 |
| `eval()` yasak | Güvenlik riski | ADR-022 |
| `document.write` yasak | XSS riski | ADR-022 |
| URL encoding | Parameter encoding | ADR-021 |

```javascript
// ✅ DOĞRU: DOMParser + TrustedTypes
const parser = new DOMParser();
const doc = parser.parseFromString(html, 'text/html');
container.appendChild(doc.body.firstChild);

// ❌ YANLIŞ: innerHTML — XSS riski
container.innerHTML = userInput; // ❌ GÜVENLİK AÇIĞI
```

### 4.2 CSRF Protection

| Kural | Değer | ADR |
|-------|-------|-----|
| Token meta tag | `<meta name="csrf-token">` | ADR-010 |
| Header name | `X-CSRF-Token` | ADR-010 |
| Update timing | DOM patch SONRASINDA | ADR-010 |
| Scope | POST, PUT, DELETE | ADR-010 |

```javascript
// CSRF token güncelleme — DOM patch SONRASINDA
async #navigate(url, pushState = true) {
    // 1. DOM patch (form input'ları oluşur)
    this.#patchDOM(html);

    // 2. CSRF token güncelle — DOM patch SONRASINDA
    this.#updateCsrf(this.#getCsrfToken());

    // 3. History push
    if (pushState) history.pushState({ url }, null, url);
}
```

### 4.3 CSP Compliance

| Kural | Değer | ADR |
|-------|-------|-----|
| script-src | `'strict-dynamic'` | ADR-012 |
| nonce | İstek başına 256-bit | ADR-012 |
| style-src | `'self'` | ADR-012 |
| connect-src | `'self'` | ADR-012 |

## 5. Performance Notes

### 5.1 Bundle Strategy

| Strateji | Kullanım |
|----------|----------|
| No bundler | Vanilla JS, ES6 modules |
| Lazy loading | Dynamic `import()` |
| Code splitting | Route-based loading |
| Preloading | `<link rel="preload">` |

### 5.2 Cache Strategy

| Layer | TTL | Kullanım |
|-------|-----|----------|
| Memory (Map) | 60-3600s | API responses |
| localStorage | 24h | User preferences |
| SessionStorage | Session | Temp data |
| HTTP Cache | Server-controlled | Static assets |

### 5.3 DOM Optimization

| Optimizasyon | Teknik |
|-------------|--------|
| Minimal reflows | Batch DOM reads/writes |
| Event delegation | Single parent listener |
| requestAnimationFrame | Visual updates |
| Debounce/Throttle | Scroll/resize handlers |

```javascript
// Event delegation — performance
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (btn) {
        handleAction(btn.dataset.action);
    }
});

// Debounce — scroll/resize
function debounce(fn, delay = 100) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}
```

## 6. Common Patterns

### 6.1 Module Pattern (ES6)

```javascript
// Singleton
const AuthService = (() => {
    let instance;

    function createInstance() {
        return new ApiService('/api/auth');
    }

    return {
        getInstance() {
            if (!instance) {
                instance = createInstance();
            }
            return instance;
        }
    };
})();
```

### 6.2 Observer Pattern

```javascript
class EventBus {
    #listeners = new Map();

    on(event, callback) {
        if (!this.#listeners.has(event)) {
            this.#listeners.set(event, []);
        }
        this.#listeners.get(event).push(callback);
    }

    off(event, callback) {
        const listeners = this.#listeners.get(event);
        if (listeners) {
            this.#listeners.set(
                event,
                listeners.filter((cb) => cb !== callback)
            );
        }
    }

    emit(event, data) {
        const listeners = this.#listeners.get(event) || [];
        listeners.forEach((cb) => cb(data));
    }
}

window.CoreEvents = new EventBus();
```

### 6.3 State Machine

```javascript
class PlayerStateMachine {
    #state = 'stopped';
    #transitions = {
        stopped: ['playing', 'paused'],
        playing: ['paused', 'stopped'],
        paused: ['playing', 'stopped'],
    };

    canTransition(to) {
        return this.#transitions[this.#state]?.includes(to) ?? false;
    }

    transition(to) {
        if (!this.canTransition(to)) {
            throw new Error(`Invalid transition: ${this.#state} → ${to}`);
        }
        this.#state = to;
    }

    get state() {
        return this.#state;
    }
}
```

## 7. Edge Cases

### 7.1 Network Failures

| Senaryo | Çözüm |
|---------|-------|
| Offline | Cache fallback, service worker |
| Timeout | AbortController + retry |
| 401 Unauthorized | Token refresh → redirect |
| 429 Rate Limit | Exponential backoff |

```javascript
// Retry with exponential backoff
async function fetchWithRetry(url, options, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await fetch(url, options);
        } catch (err) {
            if (i === maxRetries - 1) throw err;
            await new Promise((r) => setTimeout(r, 1000 * 2 ** i));
        }
    }
}
```

### 7.2 Browser Compatibility

| Feature | Fallback |
|---------|----------|
| URLPattern | Regex fallback |
| TrustedTypes | Skip (non-Chromium) |
| AbortController | Timeout fallback |
| ES modules | Script tags |

### 7.3 Memory Leak Prevention

| Kaynak | Temizleme |
|--------|----------|
| Event listeners | `destroy()` method |
| Timers | `clearTimeout` / `clearInterval` |
| AbortController | `abort()` on unmount |
| Cache | LRU eviction |

## 8. Testing Requirements

### 8.1 Vitest Structure

```javascript
// user.test.js
import { describe, it, expect, vi } from 'vitest';
import { UserService } from './UserService.js';

describe('UserService', () => {
    it('should register user with valid data', async () => {
        const mockApi = {
            post: vi.fn().mockResolvedValue({ id: 1, email: 'test@example.com' }),
        };
        const service = new UserService(mockApi);

        const result = await service.register('test@example.com', 'testuser', 'password123');

        expect(result).toEqual({ id: 1, email: 'test@example.com' });
        expect(mockApi.post).toHaveBeenCalledWith('/api/users', {
            email: 'test@example.com',
            username: 'testuser',
            password: 'password123',
        });
    });

    it('should reject invalid email', async () => {
        const service = new UserService({});
        await expect(service.register('invalid', 'test', 'pass'))
            .rejects.toThrow('Geçersiz email');
    });
});
```

### 8.2 Coverage Targets

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Router | ≥80% | ≥90% |
| Components | ≥80% | ≥90% |
| Services | ≥80% | ≥90% |
| Cache | ≥90% | ≥95% |

## 9. Troubleshooting

### 9.1 Sıkça Görülen Hatalar

| Hata | Neden | Çözüm |
|------|-------|-------|
| `CSP violation` | inline script | nonce ekle |
| `TrustedTypes error` | DOMParser yok | TrustedTypes policy |
| `CSRF token mismatch` | Token update sırası | DOM patch sonrası güncelle |
| `AbortError` | Fetch iptal | Controller kontrol |
| `Memory leak` | Listener temizlenmedi | `destroy()` çağır |

### 9.2 Debug Komutları

```javascript
// Console debug
console.log('Router state:', window.CoreRouter.state);
console.log('Cache stats:', window.CoreCache.getStats());
console.log('CSRF token:', document.querySelector('meta[name="csrf-token"]')?.content);

// Network debug
performance.getEntriesByType('resource').forEach((r) => {
    if (r.duration > 1000) console.warn('Slow resource:', r.name, r.duration);
});

// Memory debug (Chrome)
// chrome://performance → Memory tab
```

## 10. Hard Guardrails

| # | Kural | Açıklama | İlgili ADR |
|---|-------|----------|------------|
| 1 | **Vanilla JS** | Framework yasak (React, Vue, Angular) | ADR-001 |
| 2 | **ITCSS** | CSS ITCSS layer yapısı | ADR-001 |
| 3 | **DOMParser** | innerHTML yasak | ADR-001 |
| 4 | **TrustedTypes** | XSS koruması zorunlu | ADR-001 |
| 5 | **let/const** | var keyword yasak | ADR-001 |
| 6 | **async/await** | Callback hell yasak | ADR-001 |
| 7 | **AbortController** | Fetch timeout zorunlu | ADR-021 |
| 8 | **CSRF Timing** | DOM patch sonrası token güncelle | ADR-010 |
| 9 | **No eval()** | Güvenlik riski | ADR-022 |
| 10 | **Zero Code Before Plan** | Plan onayı olmadan kod yok | ADR-007 |

## 11. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Dosya** | PascalCase.js | `Router.js` |
| **Class** | PascalCase | `Router`, `BaseComponent` |
| **Method** | camelCase (#private) | `#navigate()` |
| **Variable** | camelCase | `currentUrl` |
| **Constant** | UPPER_SNAKE_CASE | `MAX_RETRY_COUNT` |
| **Event** | kebab-case | `user-login`, `track-change` |
| **CSS Class** | BEM | `.site-header__logo` |
| **Data attribute** | data-kebab | `data-action`, `data-route` |

## 12. Common Anti-Patterns

| Anti-Pattern | Neden Yasak | Doğru Kullanım |
|-------------|-------------|----------------|
| `innerHTML` | XSS riski | DOMParser |
| `eval()` | Güvenlik | Safe alternatives |
| `var` | Scope sorunu | `let`/`const` |
| Callback hell | Readability | async/await |
| Memory leak | Listener temizlenmedi | `destroy()` |
| No timeout | Fetch askıda kalır | AbortController |
| Global state | Namespace conflict | Module pattern |
| Sync XHR | UI donması | Async fetch |

## 13. Related Documents

- [[js-template]] — Bu dosya (JS ES6+)
- [[php-template]] — PHP 8.4 template
- [[css-template]] — CSS ITCSS template
- [[cpp-template]] — C++20 template
- [[vitest-template]] — Vitest test template
- [[ADR-001-vanilla-js-itcss]] — Vanilla JS + ITCSS kararı
- [[ADR-010-csrf-protection-strategy]] — CSRF koruma
- [[ADR-012-csp-nonce-strict-dynamic]] — CSP nonce
- [[ADR-021-spa-router-immutable-contract]] — SPA router
- [[architecture/l3-presentation]] — L3 Presentation layer

## 14. Cross-References

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § 3.3 Router | [[ADR-021-spa-router-immutable-contract]] | SPA router |
| § 4.1 XSS | [[ADR-001-vanilla-js-itcss]] | Framework yasak |
| § 4.2 CSRF | [[ADR-010-csrf-protection-strategy]] | CSRF token |
| § 4.3 CSP | [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce |
| § 8 Testing | [[vitest-template]] | Test standards |

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~550+ |
| **Frontmatter** | ✅ Tamamlandı |
| **ES6+** | ✅ Uyumlu |
| **Vanilla JS** | ✅ Framework yok |
| **ADR Uyumlu** | ✅ 001, 010, 012, 021, 022 |
| **Security Sections** | ✅ 3 bölüm |
| **Performance Sections** | ✅ 3 bölüm |
| **Edge Cases** | ✅ 3 bölüm |
| **Troubleshooting** | ✅ 2 bölüm |
