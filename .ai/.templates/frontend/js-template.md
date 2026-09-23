---
title: "CoreMusic — JavaScript Frontend Development Template"
type: template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
reference_doc: Freelancer Technical Documentation v1.0
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — JavaScript Frontend Development Template

**Teknoloji:** Vanilla JS ES6+ (Framework YASAK — ADR-001)
**Katman:** K11 (UX) / K10 (Uygulama)
**Sorumlu Agent:** UI Designer

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE.md]] · [[brain.md]] · [[architecture/k11-ux/README.md]] · [[architecture/k9-api-routing/spa-router.md]]

## 1. Amaç

Bu şablon, CoreMusic frontend geliştirme standardını (Vanilla JS ES6+ — framework yasak, ITCSS ile hizalı) ve kod iskeletlerini (component, servis, DOM yardımcıları, SPA router, cookie yönetimi) tanımlar. **Guardrail #16:** yeni frontend JS dosyası bu şablondan üretilmek ZORUNLUDUR. Şablon; ADR-001, ADR-004 (multi-domain SPA), ADR-007 (cache namespace, Zero Code Before Plan), ADR-010 (csrf_token), ADR-083 (SPA Router), ADR-084 (API Gateway) kararlarını koda gömer. **ADR-042 hibrit:** `.templates/*` tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

## 2. Kapsam

- **Geçerli dosya tipleri:** `assets/js/` altındaki tüm JS dosyaları (app, components, services, utils, router, config); katmanlar: K11 (UX) / K10 (Uygulama).
- **Kullananlar:** UI Designer (birincil), QA Engineer (vitest + E2E), Security Engineer (XSS/CSRF review), Backend Architect (API sözleşmesi).
- **Kapsam dışı:** CSS (→ `[[./css-template]]`), backend servis kodu (→ `[[../backend/php-template]]` / `[[../backend/nodejs-template]]`).

## 3. Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + Hard Guardrails + dosya yapısı + 5 kod şablonu + ITCSS/ADR/doküman bölümleri, eksiksiz):

````markdown
---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — JavaScript Frontend Development Template"
type: frontend-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** Vanilla JS ES6+ (Framework YASAK — ADR-001)
**Katman:** K11 (UX) / K10 (Uygulama)
**Sorumlu Agent:** UI Designer

---

## 1. Hard Guardrails (Kesinlikle Yasak)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework yasak — Vanilla JS ES6+ (ADR-001) | Kod revert edilir |
| 2 | `var` yasak — `const`/`let` kullan | Kod revert edilir |
| 3 | `innerHTML` yasak — `DOMParser` + `TrustedTypes` | XSS riski |
| 4 | `eval()` / `Function()` yasak | Güvenlik açığı |
| 5 | `localStorage` for auth yasak — Session-based auth | Veri sızıntısı |
| 6 | Hardcoded secret yasak | Veri sızıntısı |
| 7 | DOM manipulation: `document.createElement` + `appendChild` | Güvenli DOM |
| 8 | Event delegation kullan | Performans |

---

## 2. Dosya Yapısı

```
assets/js/
├── app.js                    # Ana uygulama giriş noktası
├── components/
│   ├── {{COMPONENT}}.js      # Bileşen sınıfı
│   └── AbstractComponent.js  # Soyut bileşen
├── services/
│   ├── {{SERVICE}}.js        # Servis sınıfı
│   └── ApiClient.js          # API istemcisi
├── utils/
│   ├── dom.js                # DOM yardımcıları
│   ├── crypto.js             # Güvenli rastgele
│   └── storage.js            # Cookie yönetimi
├── router/
│   └── SPA.js                # SPA router
└── config/
    └── app.js                # Uygulama yapılandırması
```

---

## 3. Bileşen Şablonu

```javascript
/**
 * {{COMPONENT}} Bileşeni
 * @module {{COMPONENT}}
 */

'use strict';

const {{COMPONENT}} = (() => {
    'use strict';

    // Private state
    let _container = null;
    let _state = {};

    /**
     * Bileşeni başlatır
     * @param {HTMLElement} container - Kök element
     * @param {Object} initialState - Başlangıç durumu
     */
    function init(container, initialState = {}) {
        _container = container;
        _state = { ...initialState };
        _render();
        _bindEvents();
    }

    /**
     * Bileşeni render eder
     * @private
     */
    function _render() {
        _container.innerHTML = ''; // TrustedTypes policy kullanılmalı

        const fragment = document.createDocumentFragment();
        const element = document.createElement('div');
        element.className = '{{COMPONENT_LOWER}}';
        element.dataset.component = '{{COMPONENT_LOWER}}';

        // İçerik oluştur
        const title = document.createElement('h2');
        title.textContent = _state.title || '';
        element.appendChild(title);

        fragment.appendChild(element);
        _container.appendChild(fragment);
    }

    /**
     * Olayları bağlar
     * @private
     */
    function _bindEvents() {
        _container.addEventListener('click', _handleClick);
    }

    /**
     * Tıklama olayını yönetir
     * @param {Event} event
     * @private
     */
    function _handleClick(event) {
        const target = event.target.closest('[data-action]');
        if (!target) return;

        const action = target.dataset.action;
        switch (action) {
            case 'action1':
                _handleAction1();
                break;
            case 'action2':
                _handleAction2();
                break;
        }
    }

    function _handleAction1() {
        // Action implementation
    }

    function _handleAction2() {
        // Action implementation
    }

    /**
     * Durumu günceller
     * @param {Object} newState
     */
    function updateState(newState) {
        _state = { ..._state, ...newState };
        _render();
    }

    /**
     * Bileşeni temizler
     */
    function destroy() {
        _container.removeEventListener('click', _handleClick);
        _container.innerHTML = '';
        _state = {};
    }

    return { init, updateState, destroy };
})();

export default {{COMPONENT}};
```

---

## 4. Servis Şablonu

```javascript
/**
 * {{SERVICE}} Servisi
 * @module {{SERVICE}}Service
 */

'use strict';

const {{SERVICE}}Service = (() => {
    'use strict';

    const API_BASE = '/api/v1/{{MODULE}}';

    /**
     * Tüm kayıtları getirir
     * @returns {Promise<Array>}
     */
    async function findAll() {
        try {
            const response = await fetch(API_BASE, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error.message);
            throw error;
        }
    }

    /**
     * ID ile kaydı getirir
     * @param {number} id
     * @returns {Promise<Object>}
     */
    async function findById(id) {
        const response = await fetch(`${API_BASE}/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Yeni kayıt oluşturur
     * @param {Object} data
     * @returns {Promise<Object>}
     */
    async function create(data) {
        const csrfToken = _getCsrfToken();

        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(data),
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    }

    /**
     * CSRF token'ı cookie'den alır
     * @returns {string}
     * @private
     */
    function _getCsrfToken() {
        const match = document.cookie.match(/csrf_token=([^;]+)/);
        return match ? match[1] : '';
    }

    return { findAll, findById, create };
})();

export default {{SERVICE}}Service;
```

---

## 5. DOM Yardımcıları

```javascript
/**
 * DOM Yardımcı Fonksiyonları
 * @module utils/dom
 */

'use strict';

const DOMUtils = (() => {
    'use strict';

    /**
     * Güvenli element oluşturur
     * @param {string} tag
     * @param {Object} attrs
     * @param {string} text
     * @returns {HTMLElement}
     */
    function createElement(tag, attrs = {}, text = '') {
        const element = document.createElement(tag);

        Object.entries(attrs).forEach(([key, value]) => {
            if (key === 'className') {
                element.className = value;
            } else if (key.startsWith('data-')) {
                element.dataset[key.slice(5)] = value;
            } else {
                element.setAttribute(key, value);
            }
        });

        if (text) {
            element.textContent = text;
        }

        return element;
    }

    /**
     * Elementi temizler
     * @param {HTMLElement} element
     */
    function clearChildren(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    /**
     * Event delegation
     * @param {HTMLElement} parent
     * @param {string} eventType
     * @param {string} selector
     * @param {Function} handler
     */
    function delegate(parent, eventType, selector, handler) {
        parent.addEventListener(eventType, (event) => {
            const target = event.target.closest(selector);
            if (target && parent.contains(target)) {
                handler.call(target, event, target);
            }
        });
    }

    return { createElement, clearChildren, delegate };
})();

export default DOMUtils;
```

---

## 6. SPA Router Şablonu

```javascript
/**
 * SPA Router
 * @module router/SPA
 */

'use strict';

const SPA = (() => {
    'use strict';

    const _routes = new Map();
    let _currentRoute = null;

    function register(path, handler) {
        _routes.set(path, handler);
    }

    function navigate(path) {
        if (_currentRoute === path) return;

        const handler = _routes.get(path);
        if (!handler) {
            console.warn(`Route not found: ${path}`);
            return;
        }

        _currentRoute = path;
        history.pushState({ path }, '', path);
        handler();
    }

    function init() {
        window.addEventListener('popstate', (event) => {
            const path = event.state?.path || window.location.pathname;
            navigate(path);
        });

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[data-spa]');
            if (link) {
                event.preventDefault();
                navigate(link.getAttribute('href'));
            }
        });
    }

    return { register, navigate, init };
})();

export default SPA;
```

---

## 7. Cookie Yönetimi (Auth için)

```javascript
/**
 * Güvenli Cookie Yönetimi
 * @module utils/storage
 */

'use strict';

const SecureStorage = (() => {
    'use strict';

    /**
     * Cookie'yi okur (sadece okunabilir)
     * @param {string} name
     * @returns {string|null}
     */
    function getCookie(name) {
        const match = document.cookie.match(
            new RegExp(`(?:^|; )${name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}=([^;]*)`)
        );
        return match ? decodeURIComponent(match[1]) : null;
    }

    /**
     * Session token'ı alır (HTTPOnly cookie'den sunucu tarafında okunur)
     * Bu fonksiyon sadece CSRF token için kullanılır
     * @returns {string}
     */
    function getCsrfToken() {
        return getCookie('csrf_token') || '';
    }

    /**
     * LocalStorage KULLANMAZ — Session-based auth
     * Auth verileri sadece HTTPOnly cookie'de saklanır
     */

    return { getCookie, getCsrfToken };
})();

export default SecureStorage;
```

---

## 8. ITCSS Layer Referansı

| Layer | İsim | Kullanım |
|-------|------|----------|
| 01 | Settings | CSS custom properties, variables |
| 02 | Tools | Mixins, functions |
| 03 | Generic | Reset, normalize |
| 04 | Elements | Bare HTML elements |
| 05 | Objects | Layout patterns |
| 06 | Components | BEM bileşenleri |
| 07 | Utilities | Helper classes |
| 08 | Devices | Behavioral overrides (hover, touch) |
| 09 | Themes | Theme-specific overrides |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-004 | Multi-domain SPA mimarisi |
| ADR-007 | Cache namespace, Zero Code Before Plan |
| ADR-010 | csrf_token key zorunlu |
| ADR-083 | SPA Router Architecture |
| ADR-084 | API Gateway Architecture |

---

## 10. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[brain.md]] | Mimari kararlar |
| [[architecture/k11-ux/README.md]] | UX mimarisi |
| [[architecture/k9-api-routing/spa-router.md]] | SPA Router |

---

*JavaScript Frontend Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

## 4. Kurallar

**Hard Guardrails (iskelet §1 — kesinlikle yasak, ihlalde kod revert/XSS riski):**

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework yasak — Vanilla JS ES6+ (ADR-001) | Kod revert edilir |
| 2 | `var` yasak — `const`/`let` kullan | Kod revert edilir |
| 3 | `innerHTML` yasak — `DOMParser` + `TrustedTypes` | XSS riski |
| 4 | `eval()` / `Function()` yasak | Güvenlik açığı |
| 5 | `localStorage` for auth yasak — Session-based auth | Veri sızıntısı |
| 6 | Hardcoded secret yasak | Veri sızıntısı |
| 7 | DOM manipulation: `document.createElement` + `appendChild` | Güvenli DOM |
| 8 | Event delegation kullan | Performans |

**Genel kurallar:**

1. **Guardrail #16:** Yeni JS dosyası bu şablondan üretilir; dış iskelet silinemez (dosya yapısı: app → components → services → utils → router → config).
2. **Auth modeli:** Session-based auth; token'lar yalnızca HTTPOnly cookie'de, CSRF `csrf_token` cookie'sinden okunur (ADR-010); `localStorage` auth için kullanılmaz.
3. **Güvenli DOM:** Render `document.createDocumentFragment` + `createElement` + `textContent`; `innerHTML` yalnızca boşaltma (`''`) içindir ve TrustedTypes policy'sine bağlıdır; tüm tıklamalar `[data-action]` event delegation ile yakalanır.
4. **API çağrısı standardı:** `fetch` + `Content-Type: application/json` + `X-Requested-With: XMLHttpRequest` + `credentials: 'same-origin'`; POST'larda `X-CSRF-Token`.
5. **ITCSS hizası (iskelet §8):** 01 Settings → 09 Themes katman sırası JS değişkenleri için de geçerli.
6. **İlgili ADR'ler:** ADR-001 · ADR-004 · ADR-007 · ADR-010 · ADR-083 · ADR-084.
7. **Bilinmeyen API:** `⚠️ VERIFICATION REQUIRED` etiketi kullanılır.

## 5. Workflow

ŞABLONU SEÇ → KOPYALA → `{{PLACEHOLDER}}` DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT

1. **ŞABLONU SEÇ:** `frontend/js-template.md`.
2. **KOPYALA:** Hedef katmanın dizinine kopyala (bileşen → `assets/js/components/`, servis → `assets/js/services/`, router/utils → ilgili alt dizin).
3. **`{{PLACEHOLDER}}` DOLDUR:** `{{COMPONENT}}`, `{{COMPONENT_LOWER}}`, `{{SERVICE}}`, `{{MODULE}}`, `{{TITLE}}`, `{{DATE}}` alanlarını gerçek değerlerle değiştir; action1/action2 handler'larını uygula; API_BASE yolunu modüle uyarla.
4. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi + §4 Hard Guardrails (8 madde) + mockup kontrolü (`[[ui-design/01-mockup-index]]`).
5. **COMMIT:** `vitest` + (QA tarafında) E2E ile commit; registry + log güncel.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu (`{{COMPONENT}}`, `{{SERVICE}}`, `{{MODULE}}`, `{{DATE}}` dahil)
- [ ] dosya bu şablona uygun (frontend JS dosyası)
- [ ] Hard Guardrails 8/8 + `var`/`innerHTML`/`eval` yok + event delegation + CSRF header doğrulandı

**REFACTOR REPORT:** FILE: js-template.md · PURPOSE: Vanilla JS frontend geliştirme standardı + kod iskeletleri (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (17 placeholder, 7 kod bloğu korundu) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[CLAUDE.md]] — ana sözleşme (iskelet §10 İlgili Dokümanlar tablosu)
- [[brain.md]] — mimari kararlar
- [[architecture/k11-ux/README.md]] — UX mimarisi
- [[architecture/k9-api-routing/spa-router.md]] — SPA Router
- [[.templates/index]] — Template Registry
- [[../CLAUDE.md]] · [[../../AGENTS.md]] — vault ana sözleşme + agent registry
- [[./css-template]] — eşdeğer frontend şablonu (CSS)

**İlgili ADR'ler:** ADR-001 (Vanilla JS + ITCSS, framework yasak) · ADR-004 (Multi-domain SPA) · ADR-007 (Cache namespace, Zero Code Before Plan) · ADR-010 (csrf_token) · ADR-083 (SPA Router Architecture) · ADR-084 (API Gateway Architecture)
