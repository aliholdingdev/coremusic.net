---
title: "CoreMusic — JavaScript Frontend Development Template"
type: template
category: frontend
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — JavaScript Frontend Development Template

**Teknoloji:** Vanilla JS ES6+ (framework yasak — ADR-001) · **Katman:** L3 (sunum) · **Sorumlu Agent:** UI Designer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[css-template]] · [[../../architecture/k9-api-routing/spa-router]]

---

## 1. Amaç

Bu şablon, CoreMusic frontend JavaScript geliştirme standardını (Vanilla JS ES6+, ITCSS ile hizalı, framework yasak) ve kod iskeletlerini (bileşen, servis, olay yayını, SPA router, cookie/CSRF) tanımlar. **Guardrail #16:** yeni frontend `.js` dosyası bu şablondan üretilmek ZORUNLUDUR.

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| Vanilla JS ES6+, framework yasak | ADR-001 | §4 #1 — ES modül (`import`/`export`) kalıbı |
| Multi-domain SPA mimarisi | ADR-004 | §2 kapsam + §3.5 router |
| Cache namespace, Zero Code Before Plan | ADR-007 | §4 #7 — plan yoksa kod yok |
| `csrf_token` zorunlu | ADR-010 | §3.6 cookie + `X-CSRF-Token` |
| SPA Router mimarisi | ADR-083 | §3.5 |
| API Gateway | ADR-084 | §3.3 fetch standardı |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/js/**/*.js` — bileşen, servis, router, yönetici, özellik modülleri | CSS/ITCSS katmanları → `[[css-template]]` |
| Vanilla JS ES6+ modülleri, olay yayını, DOM oluşturma | Backend PHP kodu → `.ai/.templates/backend/php-template.md` |
| `fetch` API çağrısı + CSRF başlıkları (ADR-084 gateway sözleşmesi) | E2E testleri → Playwright (kök `package.json` · `playwright ^1.62.1`) |
| SPA route tanımı ve koruma akışı (ADR-083) | Unit test altyapısı → `[[../testing/vitest-template]]` (diskte KURULU DEĞİL) |

- **Kullananlar:** UI Designer (birincil), QA Engineer (E2E/erişilebilirlik), Security Engineer (XSS/CSRF gözden geçirmesi), Backend Architect (API sözleşmesi).
- **Katman:** L3 (sunum) — L0/L1/L2 koduna doğrudan dokunulmaz (Layer Violation → revert).
- **Ön koşul:** CSS/HTML/JS/layout işinde `.ai/ui-design/` altındaki ilgili mockup okunmadan kod yazılamaz (Mockup Before Frontend); okunamıyorsa DUR.

---

## 3. Mimari

Şablonun gövdesi: disk kanıtıyla doğrulanmış dosya yapısı, beş kod iskeleti ve katman hizası. Yalnızca diskte var olan yollar sayısal kanıtla verilir; doğrulanamayan adlar `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

### 3.1 Disk Kanıtı — Dosya Yapısı

| Yol (disk kanıtı) | Kanıt | Kullanım |
|-------------------|-------|----------|
| `assets.coremusic.net/js/` | 4 kök dosya | Uygulama kurulumu — dosya adları ⚠️ VERIFICATION REQUIRED |
| `js/core/` | `CoreMusicApp.js`, `EventBus.js`, `helper.js`, `footer.init.js` | Çekirdek başlatma + global olay yayını |
| `js/router/` | 29 dosya | SPA route katmanı (ADR-083) |
| `js/coreplayer/` | 5 dosya | Oynatıcı durumu ve kontrolleri |
| `js/components/base/`, `js/components/interactive/` | bileşen klasörleri | Statik ve etkileşimli bileşenler |
| `js/features/` | 6 dosya | Özellik modülleri |
| `js/managers/` | 5 dosya | Durum/yönetici sınıfları |
| `js/auth/` | 2 dosya | Oturum ve yetkilendirme akışı |
| `package.json` (kök) | yalnız `playwright ^1.62.1` | vitest/JSDOM kurulumu YOK — §6 notu |

**Yerleştirme kuralı:** aynı klasörde ikinci eşdeğer dosya açılmaz; kod en spesifik mevcut alt dizine gider. `assets/js/`, `src/js/` gibi eski yollar kullanılmaz — tek geçerli kök `assets.coremusic.net/js/`'dir.

### 3.2 Bileşen Şablonu

```javascript
/**
 * {{COMPONENT}} bileşeni — Guardrail #16 ile bu şablondan türetilir.
 * @module assets.coremusic.net/js/components/interactive/{{COMPONENT_LOWER}}
 * @requires ADR-001 (framework yasak), ADR-010 (csrf_token)
 */

'use strict';

/**
 * @typedef {Object} {{COMPONENT}}State
 * @property {string} title
 * @property {boolean} expanded
 */

/** @type {{COMPONENT}}State */
const state = {
    title: '',
    expanded: false,
};

let rootEl = null;

/**
 * Bileşeni DOM'a yerleştirir (güvenli DOM: createElement + textContent).
 * @param {HTMLElement} container
 * @param {{COMPONENT}}State [initialState]
 * @returns {HTMLElement} kök eleman
 */
export function init(container, initialState = {}) {
    Object.assign(state, initialState);

    const fragment = document.createDocumentFragment();
    const root = document.createElement('section');
    root.className = '{{COMPONENT_LOWER}}';
    root.dataset.component = '{{COMPONENT_LOWER}}';

    const title = document.createElement('h2');
    title.className = '{{COMPONENT_LOWER}}__title';
    title.textContent = state.title;

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = '{{COMPONENT_LOWER}}__toggle';
    toggle.dataset.action = 'toggle';
    toggle.setAttribute('aria-expanded', String(state.expanded));
    toggle.textContent = 'Detay';

    const body = document.createElement('div');
    body.className = '{{COMPONENT_LOWER}}__body';
    body.hidden = !state.expanded;

    root.append(title, toggle, body);
    fragment.appendChild(root);
    container.appendChild(fragment);

    rootEl = root;
    bindEvents(root);
    return root;
}

/**
 * Tek olay dinleyicisi: kökte bir kez bağlanır, elemanlar delegation ile yakalanır.
 * @param {HTMLElement} root
 */
function bindEvents(root) {
    root.addEventListener('click', onClick);
}

/**
 * @param {MouseEvent} event
 */
function onClick(event) {
    const target = event.target instanceof Element
        ? event.target.closest('[data-action]')
        : null;
    if (!target || !rootEl?.contains(target)) return;

    switch (target.dataset.action) {
        case 'toggle':
            setExpanded(!state.expanded);
            break;
        case 'close':
            setExpanded(false);
            break;
        default:
            break;
    }
}

/**
 * @param {boolean} expanded
 */
export function setExpanded(expanded) {
    state.expanded = expanded;
    if (!rootEl) return;

    const body = rootEl.querySelector('.{{COMPONENT_LOWER}}__body');
    const toggle = rootEl.querySelector('[data-action="toggle"]');
    if (body instanceof HTMLElement) body.hidden = !expanded;
    if (toggle instanceof HTMLElement) {
        toggle.setAttribute('aria-expanded', String(expanded));
    }
}

/**
 * Durumu günceller ve gerekiyorsa yeniden çizer.
 * @param {Partial<{{COMPONENT}}State} }next
 */
export function update(next) {
    Object.assign(state, next);
    const title = rootEl?.querySelector('.{{COMPONENT_LOWER}}__title');
    if (title instanceof HTMLElement) title.textContent = state.title;
    setExpanded(state.expanded);
}

/**
 * Dinleyicileri kaldırır ve kökü temizler (sızıntı yok).
 */
export function destroy() {
    if (!rootEl) return;
    rootEl.removeEventListener('click', onClick);
    rootEl.remove();
    rootEl = null;
    state.title = '';
    state.expanded = false;
}
```

### 3.3 Servis / API Şablonu (ADR-084 Gateway Sözleşmesi)

```javascript
/**
 * {{MODULE}} API istemcisi — tek fetch kapısı.
 * @module assets.coremusic.net/js/services/{{MODULE}}Service
 * @requires ADR-010 (X-CSRF-Token), ADR-084 (API Gateway)
 */

'use strict';

const API_BASE = '/api/v1/{{MODULE}}';

/**
 * csrf_token cookie değerini okur (HTTPOnly oturum çerezi DEĞİL, CSRF çerezi).
 * @returns {string}
 */
export function getCsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)csrf_token=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

/**
 * Ortak fetch sarmalayıcısı: başlıklar, aynı-köken çerezleri, hata standardı.
 * @param {string} path
 * @param {RequestInit} [init]
 * @returns {Promise<any>}
 */
async function request(path, init = {}) {
    const method = (init.method ?? 'GET').toUpperCase();
    const headers = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(method !== 'GET' ? { 'X-CSRF-Token': getCsrfToken() } : {}),
        ...(init.headers ?? {}),
    };

    const response = await fetch(`${API_BASE}${path}`, {
        ...init,
        method,
        headers,
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    return response.status === 204 ? null : response.json();
}

/**
 * @param {{ search?: string, limit?: number, offset?: number }} [params]
 * @returns {Promise<Object[]>}
 */
export function findAll(params = {}) {
    const query = new URLSearchParams();
    if (params.search) query.set('search', params.search);
    query.set('limit', String(params.limit ?? 20));
    query.set('offset', String(params.offset ?? 0));
    const qs = query.toString();
    return request(qs ? `?${qs}` : '');
}

/**
 * @param {number|string} id
 * @returns {Promise<Object|null>}
 */
export async function findById(id) {
    return request(`/${encodeURIComponent(String(id))}`);
}

/**
 * @param {Object} payload
 * @returns {Promise<Object>}
 */
export function create(payload) {
    return request('', {
        method: 'POST',
        body: JSON.stringify(payload),
    });
}

/**
 * @param {number|string} id
 * @param {Object} payload
 * @returns {Promise<Object>}
 */
export function update(id, payload) {
    return request(`/${encodeURIComponent(String(id))}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
    });
}

/**
 * Soft delete: sunucu tarafı `is_deleted = 1` uygular (ADR-040 BCNF katmanı).
 * @param {number|string} id
 * @returns {Promise<null>}
 */
export function remove(id) {
    return request(`/${encodeURIComponent(String(id))}`, { method: 'DELETE' });
}
```

### 3.4 Olay Yayını ve DOM Yardımcıları

```javascript
/**
 * Merkezi olay yayıncısı — modüller arası gevşek bağlantı.
 * @module assets.coremusic.net/js/core/EventBus
 * @note Diskteki gerçek `js/core/EventBus.js` dosyasıyla aynı sözleşme korunur.
 */

'use strict';

/** @type {Map<string, Set<Function>>} */
const listeners = new Map();

/**
 * @param {string} event
 * @param {Function} handler
 * @returns {() => void} abonelik kaldırma
 */
export function on(event, handler) {
    if (!listeners.has(event)) listeners.set(event, new Set());
    listeners.get(event).add(handler);
    return () => off(event, handler);
}

/**
 * @param {string} event
 * @param {Function} handler
 */
export function off(event, handler) {
    listeners.get(event)?.delete(handler);
}

/**
 * @param {string} event
 * @param {any} [payload]
 */
export function emit(event, payload) {
    const handlers = listeners.get(event);
    if (!handlers) return;
    for (const handler of [...handlers]) {
        try {
            handler(payload);
        } catch (error) {
            console.error(`[EventBus] ${event}`, error);
        }
    }
}

/**
 * Tüm abonelikleri temizler (test/teardown içindir).
 */
export function reset() {
    listeners.clear();
}
```

```javascript
/**
 * Güvenli DOM yardımcıları — innerHTML YASAK (§4 #3).
 * @module assets.coremusic.net/js/utils/dom
 */

'use strict';

/**
 * @param {string} tag
 * @param {Record<string, string>} [attrs]
 * @param {string} [text]
 * @returns {HTMLElement}
 */
export function el(tag, attrs = {}, text = '') {
    const node = document.createElement(tag);
    for (const [key, value] of Object.entries(attrs)) {
        if (key === 'class') node.className = value;
        else if (key.startsWith('data-')) node.dataset[key.slice(5)] = value;
        else node.setAttribute(key, value);
    }
    if (text) node.textContent = text;
    return node;
}

/**
 * @param {HTMLElement} parent
 */
export function clear(parent) {
    while (parent.firstChild) parent.removeChild(parent.firstChild);
}

/**
 * Tek delegation dinleyicisi: yeni eklenen elemanlar için yeniden bağlama gerekmez.
 * @param {HTMLElement} root
 * @param {string} type
 * @param {string} selector
 * @param {(target: Element, event: Event) => void} handler
 * @returns {() => void}
 */
export function delegate(root, type, selector, handler) {
    const listener = (event) => {
        const target = event.target instanceof Element
            ? event.target.closest(selector)
            : null;
        if (target && root.contains(target)) handler(target, event);
    };
    root.addEventListener(type, listener);
    return () => root.removeEventListener(type, listener);
}
```

### 3.5 SPA Router Şablonu (ADR-083)

```javascript
/**
 * Tek sayfa yönlendirici — pushState tabanlı, koruma kapılı.
 * @module assets.coremusic.net/js/router/spa
 * @requires ADR-004 (multi-domain SPA), ADR-083 (SPA Router)
 */

'use strict';

/** @type {Map<string, { render: () => void|Promise<void>, guard?: () => boolean }>} */
const routes = new Map();

let currentPath = null;
let started = false;

/**
 * @param {string} path örn. "/player"
 * @param {{ render: () => void|Promise<void>, guard?: () => boolean }} route
 */
export function register(path, route) {
    routes.set(path, route);
}

/**
 * @param {string} path
 * @param {{ replace?: boolean }} [options]
 * @returns {Promise<void>}
 */
export async function navigate(path, options = {}) {
    const route = routes.get(path);
    if (!route) {
        console.warn(`[SPA] tanimsiz rota: ${path}`);
        return;
    }
    if (route.guard && !route.guard()) {
        history.replaceState({ path: '/login' }, '', '/login');
        await run(routes.get('/login'));
        return;
    }
    if (currentPath === path) return;

    currentPath = path;
    if (options.replace) history.replaceState({ path }, '', path);
    else history.pushState({ path }, '', path);
    await run(route);
}

/**
 * @param {{ render: () => void|Promise<void> }} route
 */
async function run(route) {
    try {
        await route.render();
    } catch (error) {
        console.error('[SPA] render hatasi', error);
    }
}

/**
 * Tarayıcı geri/ileri olaylarını ve iç bağlantıyı yakalar; yalnızca bir kez bağlanır.
 */
export function start() {
    if (started) return;
    started = true;

    window.addEventListener('popstate', (event) => {
        const path = event.state?.path ?? window.location.pathname;
        currentPath = null;
        void navigate(path, { replace: true });
    });

    document.addEventListener('click', (event) => {
        const link = event.target instanceof Element
            ? event.target.closest('a[data-spa]')
            : null;
        if (!link) return;
        event.preventDefault();
        void navigate(link.getAttribute('href') ?? '/');
    });

    void navigate(window.location.pathname || '/', { replace: true });
}
```

### 3.6 Cookie ve CSRF Yönetimi (ADR-010)

```javascript
/**
 * Çerez okuma katmanı — oturum verisi BU DOSYADA SAKLANMAZ.
 * @module assets.coremusic.net/js/utils/cookie
 * @requires ADR-010 (csrf_token), ADR-001 (localStorage auth yasak)
 */

'use strict';

/**
 * Salt-okunur çerez erişimi.
 * @param {string} name
 * @returns {string|null}
 */
export function readCookie(name) {
    const escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const match = document.cookie.match(new RegExp(`(?:^|;\\s*)${escaped}=([^;]*)`));
    return match ? decodeURIComponent(match[1]) : null;
}

/**
 * @returns {string} CSRF jetonu (yoksa boş dize → servis hata üretir)
 */
export function csrfToken() {
    return readCookie('csrf_token') ?? '';
}

/**
 * Yazma istekleri için standart başlık kümesi.
 * @param {string} method
 * @returns {Record<string, string>}
 */
export function writeHeaders(method) {
    const base = {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (method.toUpperCase() === 'GET') return base;
    return { ...base, 'X-CSRF-Token': csrfToken() };
}

// KURAL: localStorage / sessionStorage auth amaçlı KULLANILMAZ.
// Erişim belirteci yalnızca HTTPOnly sunucu çerezinde yaşar; JS yalnızca
// csrf_token çerezini okur. Bu fonksiyonlar token yazmaz, yalnızca okur.
```

### 3.7 JS ↔ ITCSS Katman Hizası

| JS katmanı | Hedef dizin | Hizalı CSS katmanı (disk kanıtı) |
|------------|-------------|----------------------------------|
| Çekirdek başlatma | `js/core/` | `Css/02_Base/` |
| Statik bileşen | `js/components/base/` | `Css/04_Components/` |
| Etkileşimli bileşen | `js/components/interactive/` | `Css/04_Components/` |
| Özellik modülü | `js/features/` | `Css/05_Pages/` |
| Cihaz/davranış uyarlaması | `js/managers/` | `Css/08_Devices/` |
| Tasarım jetonu okuması | herhangi bir modül | `Css/01_Abstracts/` custom property |

### 3.8 Dosya Yerleştirme ve Adlandırma

| Yazılacak kod tipi | Hedef dizin | Ad kalıbı | Not |
|---------------------|-------------|-----------|-----|
| Statik bileşen | `js/components/base/` | `{{component-name}}.js` | BEM bloğu ile aynı ad |
| Etkileşimli bileşen | `js/components/interactive/` | `{{component-name}}.js` | delegation zorunlu (§4 #8) |
| API servisi | `js/features/` veya ilgili feature klasörü | `{{module}}Service.js` | §3.3 kalıbı |
| Yönetici/durum | `js/managers/` | `{{thing}}Manager.js` | global durum tek kaynak |
| Route tanımı | `js/router/` | `{{page}}Route.js` | ADR-083 |
| Yardımcı | ilgili klasör içi | `{{subject}}.js` | en spesifik dizin |
| Oynatıcı mantığı | `js/coreplayer/` | mevcut dosya adları korunur | dosya ADI DEĞİŞTİRİLEMEZ (In-Place Refactoring) |

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework yasak — Vanilla JS ES6+ (ADR-001) | Kod revert edilir |
| 2 | `var` yasak; `const`/`let` kullanılır | Kod revert edilir |
| 3 | `innerHTML` yazımı yasak; `createElement` + `textContent` | XSS riski |
| 4 | `eval()`, `new Function()`, `setInterval` ile kod üretimi yasak | Güvenlik açığı |
| 5 | `localStorage`/`sessionStorage` auth amaçlı yasak | Veri sızıntısı |
| 6 | Hardcoded secret/token yasak | Veri sızıntısı |
| 7 | Dosya adı ve yolu değiştirilemez; yerinde refactor yapılır | Kırık referans |
| 8 | Event delegation (`[data-action]`) zorunlu | Bellek sızıntısı |

### 4.2 Ek Kurallar

- **Zorunlu:** tüm modüller ES modülüdür (`import`/`export`); global sızıntı yoktur, her modül tek sorumluluk sahibidir.
- **Zorunlu:** yazma istekleri `X-CSRF-Token` başlığı taşır (§3.3/§3.6, ADR-010); başlıksız yazma isteği sunucuda 403 ile sonuçlanır.
- **Zorunlu:** ağ hataları tek sarmalayıcıda yakalanır ve `HTTP {kod}: {metin}` biçiminde standart hata üretilir (§3.3).
- **Zorunlu:** `Zero Code Before Plan` (ADR-007): önce `.ai/` içinde plan/ADR, sonra kod.
- **Yasak:** `.ai/log.md` dışına audit yazmak; `.ai/log.md` yalnızca append (parent birleştirme adımında).
- **Yasak:** diskte bulunmayan dosya/yol adı üretmek; doğrulanamayan bilgi `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
- **Not (test altyapısı):** kök `package.json` içinde yalnız `playwright` vardır; vitest/JSDOM kurulumu diskte YOKTUR — unit test eklenecekse `[[../testing/vitest-template]]` kurulum adımlarıyla başlanır.

---

## 5. Workflow

```
MOCKUP OKU → ŞABLONU SEÇ → KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **MOCKUP OKU:** `.ai/ui-design/` altındaki ilgili mockup + envanter; okunamıyorsa DUR.
2. **ŞABLONU SEÇ:** `.ai/.templates/frontend/js-template.md` (Guardrail #16).
3. **KOPYALA:** §3.8 tablosuna göre hedef dizine iskeleti kopyala (`assets.coremusic.net/js/...`).
4. **`{{VARIABLE}}` DOLDUR:** `{{COMPONENT}}`, `{{COMPONENT_LOWER}}`, `{{MODULE}}`, `{{TITLE}}`; API yolu ve aksiyon adlarını gerçek değerlerle değiştir.
5. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi + §4.1 (8 madde) + mockup uyumu.
6. **COMMIT:** yerel doğrulama (tarayıcı konsolu temiz, ağ istekleri 2xx/4xx) + registry/log güncel; `.ai/log.md` append'i parent yapar.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan (title, type, category, date, updated, version, status, authority) |
| 2 | Bölüm yapısı | §1-§7 numaralı, en fazla 3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Hard Guardrails | §4.1 8/8 — `var`/`innerHTML`/`eval`/localStorage-auth yok |
| 5 | CSRF | Yazma isteğinde `X-CSRF-Token` başlığı var |
| 6 | Delegation | Dinleyici kökte tek sefer bağlanmış |
| 7 | Dosya yolu | `assets.coremusic.net/js/` altına yazıldı, ad değişmedi |
| 8 | Halüsinasyon | Diskte olmayan dosya/yol iddia edilmedi; belirsizlik `⚠️ VERIFICATION REQUIRED` |
| 9 | Mojibake | Türkçe karakterler bozuk değil; `vault-utf8-writer verify` temiz |
| 10 | Mockup | `.ai/ui-design/` referansı okundu |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Şablon envanteri (DRY — burada tekrarlanmaz) |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, ADR-042 hibrit |
| Agent registry | [[../../AGENTS.md]] | §6 yönlendirme: JS → UI Designer |
| CSS şablonu | [[css-template]] | ITCSS katman hizası |
| Eşdeğer test şablonu | [[../testing/vitest-template]] | Unit test kurulumu (diskte kurulu değil) |
| SPA Router mimarisi | [[../../architecture/k9-api-routing/spa-router]] | ADR-083 ayrıntısı |
| Mockup indeksi | `.ai/ui-design/` | Mockup Before Frontend ön koşulu |
| İlgili ADR'ler | ADR-001 · ADR-004 · ADR-007 · ADR-010 · ADR-083 · ADR-084 | §1 tablosunda eşleştirilmiştir |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
