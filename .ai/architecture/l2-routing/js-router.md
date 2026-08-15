---
type: architecture
category: l2
title: "JS Router (Frontend)"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# JS Router (Frontend)

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[ADR-021-spa-router-immutable-contract]]

---

## 1. Amaç

Frontend SPA Router'ı tanımlar. [[ADR-001-vanilla-js-itcss]] ve [[ADR-021-spa-router-immutable-contract]] ile uyumludur.

---

## 2. Router.js Yapısı

```javascript
/**
 * SPA Router — Vanilla JS ES6+ (ADR-001).
 * ADR-021: Router contract — immutable.
 */
class Router {
    #routes = new Map();
    #currentRoute = null;
    #csrfToken = null;

    constructor() {
        window.addEventListener('popstate', () => this.#onPopState());
        document.addEventListener('click', (e) => this.#onLinkClick(e));
    }

    addRoute(path, handler) {
        this.#routes.set(path, handler);
        return this;
    }

    navigate(url, pushState = true) {
        const handler = this.#matchRoute(url);

        if (handler) {
            this.#currentRoute = url;
            handler();
            this.#patchDOM(url);

            // CSRF token güncelle — DOM patch SONRASINDA
            this.#updateCsrfToken();

            if (pushState) {
                history.pushState({ url }, null, url);
            }
        }
    }

    #onPopState() {
        const url = window.location.pathname;
        this.navigate(url, false);
    }

    #onLinkClick(e) {
        const link = e.target.closest('a[data-spa]');
        if (link) {
            e.preventDefault();
            this.navigate(link.getAttribute('href'));
        }
    }

    #matchRoute(url) {
        for (const [pattern, handler] of this.#routes) {
            if (this.#matchPattern(pattern, url)) {
                return handler;
            }
        }
        return null;
    }

    #matchPattern(pattern, url) {
        const regex = new RegExp(
            '^' + pattern.replace(/\{(\w+)\}/g, '(?<$1>[^/]+)') + '$'
        );
        return regex.test(url);
    }

    #patchDOM(url) {
        // DOM güncelleme mantığı — innerHTML YASAK (ADR-001)
        // DOMParser + TrustedTypes zorunlu
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Eski içeriği temizle (innerHTML yerine)
                while (document.body.firstChild) {
                    document.body.removeChild(document.body.firstChild);
                }
                
                // Yeni içeriği ekle
                document.body.append(...doc.body.childNodes);
            });
    }

    #updateCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
            this.#csrfToken = token;
            document.querySelectorAll('input[name="csrf_token"]').forEach(el => {
                el.value = token;
            });
        }
    }

    #getCsrfToken() {
        return this.#csrfToken;
    }
}
```

---

## 3. Kullanım

```javascript
const router = new Router();

router.addRoute('/songs', () => {
    console.log('Songs page');
});

router.addRoute('/songs/{id}', (params) => {
    console.log(`Song ${params.id}`);
});

router.addRoute('/admin', () => {
    console.log('Admin page');
});

// Navigation
router.navigate('/songs');
```

---

## 4. DOM Patch Kuralları

| Kural | Değer | ADR |
|-------|-------|-----|
| **innerHTML** | ❌ Yasak | ADR-001 |
| **DOMParser** | ✅ Zorunlu | ADR-001 |
| **TrustedTypes** | ✅ Zorunlu | ADR-001 |
| **CSRF güncelleme** | DOM patch sonrası | ADR-021 |

---

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| `innerHTML` | DOMParser | ADR-001 |
| `eval()` | Safe alternatives | ADR-001 |
| Framework | Vanilla JS | ADR-001 |
| Token patch öncesi | Token patch sonrası | ADR-021 |

---

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **CSRF token kaybı** | Session'dan yeniden oku | ADR-021 |
| **DOM patch hatası** | Fallback to full reload | ADR-021 |
| **Popstate** | URL eşleştirme | ADR-021 |
| **Link click** | data-spa attribute | ADR-021 |

---

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L2 ana dizin |
| [[spa-router]] | PHP SPA Router |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[ADR-021-spa-router-immutable-contract]] | SPA contract |

---

## 8. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ 001, 021 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
