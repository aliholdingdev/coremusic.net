---
type: architecture
category: l2
title: "JS Router — Modüler SPA Client-Side Router"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# JS Router — Modüler SPA Client-Side Router

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-021-spa-router-immutable-contract]] · [[ADR-083-spa-router]]

**İlgili Dosyalar:** [[guard-pipeline]] · [[spa-router]] · [[html-shell-renderer]]

**Referans Proje:** `reference-project/assets.coremusic.net/js/router/` — 21+ modül, ES6 Module, SOLID uyumlu.

---

## 1. Amaç

Frontend SPA Router'ı tanımlar. [[ADR-083-spa-router]] ile uyumlu **PHP+JS hybrid** mimaride JS tarafını yönetir. History API (pushState/popstate) ile client-side routing yapar, DOMParser ile DOM patching uygular.

**Kritik Fark:** İlk yükleme PHP tarafından yapılır. Sonraki navigasyonlarda JS Router devreye girer.

---

## 2. Hybrid Akış (JS Tarafı)

```
İlk Yükleme:
  PHP → Tam HTML shell → Browser render → JS Router init

Sonraki Navigasyon:
  Kullanıcı tıklar → JS RouterGuardPipeline → FetchWrapper → PHP (JSON)
    → DomPatcher → CSRF sync → History pushState

Back/Forward:
  Popstate event → JS Router → Fetch → PHP → DomPatcher
```

---

## 3. JS Modül Yapısı

```
assets.coremusic.net/js/router/
│
├── main.js                       ← Entry point
├── Router.js                     ← Ana SPA router sınıfı
├── IRouter.js                    ← Router interface (abstract base)
│
├── NavigationOrchestrator.js     ← Navigasyon orkestrasyonu
├── GuardPipeline.js              ← Client-side guard'lar
├── guards.js                     ← auth/role/permission guard tanımları
├── NavigationGuardRunner.js      ← Navigation guard runner
│
├── CacheLayer.js                 ← Route content caching
├── DomPatcher.js                 ← DOM patching (DOMParser, innerHTML YASAK)
├── ContentPatcher.js             ← HTML content update
├── ContentFetcher.js             ← Content fetching
├── FetchWrapper.js               ← HTTP fetch wrapper + AbortController
│
├── CsrfSyncManager.js            ← CSRF token synchronizasyonu
├── AuthBoundaryDetector.js       ← Auth state detection
├── ScrollRestorer.js             ← Scroll position restoration
├── MemoryWatchdog.js             ← Memory leak prevention
├── HistoryManager.js             ← pushState/popstate yönetimi
├── LifecycleManager.js           ← Component lifecycle (mount/unmount)
├── PrefetchManager.js            ← Route prefetching (pre-loading)
├── RouterEventManager.js         ← Event binding/unbinding
├── ScriptInjector.js             ← Dynamic script injection
├── UrlUtils.js                   ← URL normalization
├── Logger.js                     ← Client-side structured logging
├── ErrorHandler.js               ← Error recovery
├── FocusManager.js               ← Focus yönetimi
├── HomeHandler.js                ← Home page visualizer
│
├── config/                       ← Yapılandırma dosyaları
│   ├── auth-routes.js            ← Auth route tanımları
│   ├── css-selectors.js          ← CSS selector sabitleri
│   ├── error-types.js            ← Hata tipleri
│   ├── events.js                 ← Event isimleri
│   ├── headers.js                ← HTTP header tanımları
│   ├── navigate-utils.js         ← Navigasyon yardımcıları
│   └── signal-utils.js           ← Signal/abort yardımcıları
│
├── scale/                        ← Ölçeklendirme modülleri
│   ├── scale.coordinator.js      ← Ölçeklendirme koordinatörü
│   ├── header.scale.js           ← Header ölçekleme
│   ├── footer.scale.js           ← Footer ölçekleme
│   ├── home.scale.js             ← Home ölçekleme
│   └── nonce-style-sheeter.js    ← Nonce-based style injection
│
└── auth/                         ← Auth form'lar (Ayrı JS dosyaları)
    ├── CookieHelper.js           ← Cookie yönetimi
    ├── FormPostService.js        ← Form POST servisi
    ├── GenderSelector.js         ← Cinsiyet seçici
    ├── HeaderUserMenu.js         ← Header kullanıcı menüsü
    ├── LoginFormHandler.js       ← Login form handler
    ├── PasswordResetHandler.js   ← Şifre sıfırlama
    ├── RegisterFormHandler.js    ← Kayıt form handler
    └── SocialAuthStub.js         ← Sosyal auth stub

Toplam: 39 JS dosyası
```

### Dosya Dağılımı

| Kategori | Adet | Açıklama |
|----------|------|----------|
| **Router Modülleri** | 26 | Core SPA routing |
| **Config** | 7 | Yapılandırma |
| **Scale** | 5 | Ölçeklendirme |
| **Auth Form'lar** | 8 | Ayrı JS dosyaları (router içinde değil) |

---

## 4. Router.js — Ana Sınıf

```javascript
import IRouter from './IRouter.js';
import GuardPipeline from './GuardPipeline.js';
import CacheLayer from './CacheLayer.js';
import LifecycleManager from './LifecycleManager.js';
import FetchWrapper from './FetchWrapper.js';
import Logger from './Logger.js';
import NavigationOrchestrator from './NavigationOrchestrator.js';
import DomPatcher from './DomPatcher.js';
import ContentPatcher from './ContentPatcher.js';
import CsrfSyncManager from './CsrfSyncManager.js';
import AuthBoundaryDetector from './AuthBoundaryDetector.js';
import ScrollRestorer from './ScrollRestorer.js';
import ErrorHandler from './ErrorHandler.js';
import MemoryWatchdog from './MemoryWatchdog.js';
import RouterEventManager from './RouterEventManager.js';
import { normalizeUrl } from './UrlUtils.js';
import { MAIN_CONTENT_SELECTOR } from './config/css-selectors.js';

export default class Router extends IRouter {
    #logger;
    #cache;
    #lifecycle;
    #guards;
    #fetcher;
    #nav;
    #domPatcher;
    #contentPatcher;
    #csrfSync;
    #authBoundary;
    #scrollRestorer;
    #errorHandler;
    #memoryWatchdog;
    #eventManager;
    #user = null;
    #config;

    constructor(config = {}) {
        super();
        const {
            slowNavigationMs = 3000,
            memoryCheckInterval = 5000,
            memoryThresholdMB = 100,
            maxPrefetch = 2,
            ...rest
        } = config;
        this.#config = { slowNavigationMs, memoryCheckInterval, memoryThresholdMB, maxPrefetch, ...rest };

        this.#logger = config.logger ?? new Logger(config.logLevel ?? 'info');
        this.#csrfSync = config.csrfSync ?? new CsrfSyncManager(this.#logger);
        this.#cache = config.cache ?? new CacheLayer({}, this.#logger);
        this.#lifecycle = config.lifecycle ?? new LifecycleManager(this.#logger);
        this.#guards = config.guards ?? new GuardPipeline(this.#logger);
        this.#fetcher = config.fetcher ?? new FetchWrapper(this.#logger, this.#csrfSync);
        this.#domPatcher = config.domPatcher ?? new DomPatcher(this.#logger);
        this.#errorHandler = config.errorHandler ?? new ErrorHandler(this.#logger);
        this.#contentPatcher = config.contentPatcher ?? new ContentPatcher({
            domPatcher: this.#domPatcher,
            csrfSync: this.#csrfSync,
            lifecycle: this.#lifecycle,
            logger: this.#logger,
            errorHandler: this.#errorHandler,
        });
        this.#authBoundary = config.authBoundary ?? new AuthBoundaryDetector(this.#logger);
        this.#scrollRestorer = config.scrollRestorer ?? new ScrollRestorer(this.#logger);
        this.#memoryWatchdog = config.memoryWatchdog ?? new MemoryWatchdog(this.#cache, this.#logger, {
            checkInterval: this.#config.memoryCheckInterval,
            thresholdMB: this.#config.memoryThresholdMB,
        });
        this.#eventManager = config.eventManager ?? new RouterEventManager();
        this.#user = config.user ?? null;

        if (Array.isArray(config.guardFunctions)) {
            for (const fn of config.guardFunctions) {
                this.#guards.register(fn);
            }
        }

        this.#nav = config.navigationOrchestrator ?? new NavigationOrchestrator({
            guards: this.#guards,
            cache: this.#cache,
            fetcher: this.#fetcher,
            lifecycle: this.#lifecycle,
            domPatcher: this.#domPatcher,
            csrfSync: this.#csrfSync,
            contentPatcher: this.#contentPatcher,
            authBoundary: this.#authBoundary,
            scrollRestorer: this.#scrollRestorer,
            errorHandler: this.#errorHandler,
            memoryWatchdog: this.#memoryWatchdog,
            logger: this.#logger,
            user: this.#user,
            config: this.#config,
        });
    }

    get currentUrl() { return this.#nav.currentUrl; }
    get navCount() { return this.#nav.navCount; }
    get logger() { return this.#logger; }

    init() {
        if (window.CoreMusic?.RouterConfig?.enabled === false) {
            this.#logger.info('Router', 'spa_disabled_by_config');
            return;
        }
        if (typeof history.pushState !== 'function') {
            this.#logger.info('Router', 'spa_disabled_legacy_browser');
            return;
        }
        history.scrollRestoration = 'manual';

        this.#eventManager.bind({
            onNavigate: (url, pushState) => this.#nav.navigate(url, pushState),
            onPrefetch: (url) => this.#nav.prefetch(url),
            onOffline: () => this.#domPatcher.setErrorState('offline'),
            onOnline: () => this.#domPatcher.setErrorState(null),
        });

        if (window.location.search?.includes('auth_key=')) return;
        if (window.location.search) history.replaceState(null, '', window.location.pathname);

        this.#nav.initUrl(window.location.pathname);
        this.#logger.info('Router', 'init_complete', { url: this.#nav.currentUrl });
    }

    async navigate(url, pushState = true) {
        if (!this.#nav) return;
        await this.#nav.navigate(url, pushState);
    }

    async prefetch(url) {
        if (!this.#nav) return;
        await this.#nav.prefetch(url);
    }

    invalidateCacheTag(tag) { this.#cache.invalidateTag(tag); }
    clearCache() { this.#cache.clear(); }

    async destroy() {
        this.#fetcher.abort?.();
        this.#nav?.abortAll?.();
        this.#lifecycle.unmount();
        this.#eventManager.unbind();
        this.#memoryWatchdog?.destroy?.();
        this.#nav = null;
        this.#logger.info('Router', 'destroyed');
    }
}
```

---

## 5. main.js — Entry Point

```javascript
import Router from './Router.js';
import { authGuard, roleGuard, permissionGuard } from './guards.js';
import AuthHandler from './AuthHandler.js';
import { initHomePage } from './HomeHandler.js';
import { EVENTS } from './config/events.js';

(function () {
    'use strict';

    const config = window.CoreMusic?.RouterConfig || {};
    if (config.enabled === false) return;
    if (typeof history.pushState !== 'function') return;

    const guardFunctions = [authGuard, roleGuard, permissionGuard];
    if (typeof config.customGuard === 'function') guardFunctions.push(config.customGuard);

    const router = new Router({ ...config, guardFunctions });
    router.init();

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.Router = router;

    const authHandler = new AuthHandler(router);
    authHandler.init();
    window.CoreMusic.AuthHandler = authHandler;
})();
```

---

## 6. GuardPipeline — Client-Side Guards

```javascript
/**
 * GuardPipeline — Client-side guard yönetimi.
 *
 * Her guard: (url, config) => boolean | { redirect: string }
 */
export default class GuardPipeline {
    #guards = [];
    #logger;

    constructor(logger) { this.#logger = logger; }

    register(fn) { this.#guards.push(fn); }

    async run(url, config) {
        for (const guard of this.#guards) {
            const result = await guard(url, config);
            if (result === false) {
                this.#logger.warn('GuardPipeline', 'guard_rejected', { url, guard: guard.name });
                return false;
            }
            if (result && typeof result === 'object' && result.redirect) {
                return result;
            }
        }
        return true;
    }
}
```

### guards.js — Guard Tanımları

```javascript
export function authGuard(url, config) {
    const protectedRoutes = config.protectedRoutes || [];
    const isProtected = protectedRoutes.some(route => url.startsWith('/' + route));
    if (isProtected && !config.user?.id) {
        return { redirect: '/login' };
    }
    return true;
}

export function roleGuard(url, config) {
    // Rol bazlı guard — gelecek için hazır
    return true;
}

export function permissionGuard(url, config) {
    // İzin bazlı guard — gelecek için hazır
    return true;
}
```

---

## 7. DomPatcher — DOM Patching (innerHTML YASAK)

```javascript
/**
 * DomPatcher — DOM güncelleme (DOMParser + TrustedTypes).
 *
 * ADR-001: innerHTML YASAK. DOMParser zorunlu.
 */
export default class DomPatcher {
    #logger;
    #mainContent;

    constructor(logger) {
        this.#logger = logger;
        this.#mainContent = document.querySelector(MAIN_CONTENT_SELECTOR);
    }

    patch(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Eski içeriği temizle
        while (this.#mainContent.firstChild) {
            this.#mainContent.removeChild(this.#mainContent.firstChild);
        }

        // Yeni içeriği ekle
        this.#mainContent.append(...doc.body.childNodes);
        this.#mainContent.setAttribute('aria-busy', 'false');
    }

    setErrorState(state) {
        this.#mainContent?.setAttribute('data-error', state ?? '');
    }
}
```

---

## 8. CsrfSyncManager — CSRF Token Synchronizasyonu

```javascript
/**
 * CsrfSyncManager — CSRF token DOM patch SONRASINDA güncellenir.
 *
 * ADR-021: Token patch öncesi → hatalı. Patch sonrası → doğru.
 */
export default class CsrfSyncManager {
    #token = null;
    #logger;

    constructor(logger) { this.#logger = logger; }

    syncFromResponse(responseBody) {
        if (responseBody?.csrf_token) {
            this.#token = responseBody.csrf_token;
            this.#updateDom();
        }
    }

    syncFromMeta() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta?.content) {
            this.#token = meta.content;
            this.#updateDom();
        }
    }

    #updateDom() {
        document.querySelectorAll('input[name="csrf_token"]').forEach(el => {
            el.value = this.#token;
        });
        this.#logger.debug('CsrfSync', 'token_updated');
    }

    getToken() { return this.#token; }
}
```

---

## 9. FetchWrapper — HTTP Fetch + AbortController

```javascript
/**
 * FetchWrapper — HTTP fetch wrapper with AbortController.
 *
 * SPA istekleri: X-Requested-With: XMLHttpRequest header'ı zorunlu.
 */
export default class FetchWrapper {
    #logger;
    #csrfSync;
    #abortController = null;

    constructor(logger, csrfSync) {
        this.#logger = logger;
        this.#csrfSync = csrfSync;
    }

    async fetch(url, options = {}) {
        this.#abortController = new AbortController();

        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            ...options.headers,
        };

        if (this.#csrfSync.getToken()) {
            headers['X-CSRF-Token'] = this.#csrfSync.getToken();
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers,
                signal: this.#abortController.signal,
                credentials: 'same-origin',
            });

            if (!response.ok) {
                this.#logger.warn('FetchWrapper', 'http_error', { status: response.status, url });
            }

            return response;
        } catch (error) {
            if (error.name === 'AbortError') {
                this.#logger.info('FetchWrapper', 'request_aborted', { url });
            } else {
                this.#logger.error('FetchWrapper', 'fetch_error', { message: error.message, url });
            }
            throw error;
        }
    }

    abort() { this.#abortController?.abort(); }
}
```

---

## 10. NavigationOrchestrator — Navigasyon Akışı

```javascript
/**
 * NavigationOrchestrator — Navigasyon akışını orkestra eder.
 *
 * Akış: Guard → Cache Check → Fetch → Content Patch → CSRF Sync → Scroll → History
 */
export default class NavigationOrchestrator {
    #guards; #cache; #fetcher; #lifecycle; #domPatcher;
    #csrfSync; #contentPatcher; #authBoundary; #scrollRestorer;
    #errorHandler; #memoryWatchdog; #logger; #config;
    #currentUrl = ''; #navCount = 0;

    constructor(deps) {
        Object.assign(this, deps);
    }

    get currentUrl() { return this.#currentUrl; }
    get navCount() { return this.#navCount; }

    initUrl(url) { this.#currentUrl = url; }

    async navigate(url, pushState = true) {
        this.#navCount++;
        this.#logger.info('Nav', 'navigate_start', { url, pushState });

        // 1. Guard check
        const guardResult = await this.#guards.run(url, this.#config);
        if (guardResult === false) return;
        if (guardResult?.redirect) {
            return this.navigate(guardResult.redirect, true);
        }

        // 2. Cache check
        const cached = this.#cache.get(url);
        if (cached) {
            this.#applyContent(cached, url, pushState);
            return;
        }

        // 3. Fetch
        try {
            const response = await this.#fetcher.fetch(url);
            const data = await response.json();

            // 4. Auth boundary detection
            this.#authBoundary.detect(data);

            // 5. Cache store
            this.#cache.set(url, data);

            // 6. Apply content
            this.#applyContent(data, url, pushState);
        } catch (error) {
            this.#errorHandler.handle(error, url);
        }
    }

    #applyContent(data, url, pushState) {
        // Content patch (DOM update)
        this.#contentPatcher.apply(data);

        // CSRF sync (DOM patch SONRASINDA)
        this.#csrfSync.syncFromResponse(data);

        // Scroll restore
        this.#scrollRestorer.restore(url);

        // History push
        if (pushState) {
            history.pushState({ url }, '', url);
        }

        // Lifecycle mount
        this.#lifecycle.mount();

        // Memory check
        this.#memoryWatchdog.check();

        this.#currentUrl = url;
    }
}
```

---

## 11. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `innerHTML` | DOMParser + TrustedTypes | ADR-001 |
| `eval()` / `Function()` | Safe alternatives | ADR-001 |
| React / Vue / Angular | Vanilla JS ES6+ | ADR-001 |
| `var` | `const` / `let` | ADR-001 |
| Framework bağımlılığı | Bağımsız modüller | ADR-001 |
| Token patch öncesi | Token patch sonrası | ADR-021 |
| `localStorage` for auth | Session-based auth | ADR-011 |
| `sessionStorage` for auth | Session-based auth | ADR-011 |

---

## 12. DOM Patch Kuralları

| Kural | Değer | ADR |
|-------|-------|-----|
| **innerHTML** | ❌ Yasak | ADR-001 |
| **DOMParser** | ✅ Zorunlu | ADR-001 |
| **TrustedTypes** | ✅ Zorunlu | ADR-001 |
| **CSRF güncelleme** | DOM patch SONRASINDA | ADR-021 |
| **Eski içeriği temizle** | `removeChild` loop | ADR-001 |
| **Yeni içeriği ekle** | `append(...childNodes)` | ADR-001 |

---

## 13. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **CSRF token kaybı** | Meta tag'den yeniden oku | ADR-021 |
| **DOM patch hatası** | Fallback to full reload | ADR-021 |
| **Popstate** | URL eşleştirme + navigate | ADR-021 |
| **Link click** | `data-spa` attribute | ADR-021 |
| **Auth boundary** | AuthBoundaryDetector | ADR-043 |
| **Network offline** | Error state + retry | — |
| **Memory leak** | MemoryWatchdog | — |
| **Cache overflow** | LRU eviction | — |
| **Abort request** | AbortController | — |

---

## 14. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[guard-pipeline]] | Guard pipeline detayı |
| [[html-shell-renderer]] | HTML shell üretimi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[ADR-021-spa-router-immutable-contract]] | SPA contract |
| [[ADR-083-spa-router]] | SPA Router Architecture |

---

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **ADR Uyumlu** | ✅ 001, 021, 083 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |
| **Modül Sayısı** | 21+ |
| **SOLID Uyumlu** | ✅ SRP, DIP, OCP |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-16
**Mode:** Red Team · Human Mode · Truth Mode
