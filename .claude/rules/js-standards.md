# JavaScript Standards — CoreMusic

**Authority:** ADR-001, ADR-021
**Last Updated:** 2026-08-06
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. Mandatory

- Vanilla JS ES6+ only (ADR-001)
- NO frameworks (React, Vue, Angular, Svelte: ALL FORBIDDEN)
- `const` / `let` only (`var` FORBIDDEN)
- `async` / `await` for all async operations
- AbortController for every fetch request
- Private methods with `#` prefix
- No `eval()` or `Function()` constructor
- No `document.write()`

## 2. DOM Safety

- `DOMParser` + `TrustedTypes` for HTML parsing
- `innerHTML` direct usage: FORBIDDEN
- `requestAnimationFrame` for batch DOM updates
- Debounce/throttle for input and scroll events

## 3. SPA Router

- Cache-first strategy (LRU CacheLayer)
- CSRF update AFTER DOM patch (`#updateCsrf()` post-patch)
- History pushState for navigation
- Guard pipeline for access control

```javascript
// SPA Router — CSRF Update Timing
async #navigate(url, pushState = true) {
    // 1. DOM patch (form input'ları DOM'da oluşur)
    this.#patchDOM(html);

    // 2. CSRF token güncelle — DOM patch SONRASINDA zorunlu
    this.#updateCsrf(this.#getCsrfToken());

    // 3. History push
    if (pushState) history.pushState({ url }, null, url);
}
```

## 4. Module System

- ES6 modules with `import` / `export`
- No CommonJS `require()` in frontend
- Lazy loading for heavy modules via `import()`

## 5. Code Style

- BEM naming convention for CSS classes
- Private methods with `#` prefix
- No magic numbers — use constants
- Single responsibility per function

## 6. Fetch Protocol

```javascript
// ✅ DOĞRU — AbortController + error handling
const controller = new AbortController();
try {
  const response = await fetch(url, { signal: controller.signal });
  if (!response.ok) throw new Error(`HTTP ${response.status}`);
  return await response.json();
} catch (e) {
  if (e.name === 'AbortError') return; // silenced
  console.error('Fetch failed:', e.message);
  throw e;
}
```

## 7. Forbidden

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `var` | `const` / `let` |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `eval()` | Safe alternatives |
| `document.write()` | `DOM` manipulation |
| `require()` (frontend) | `import` / `export` |
| Framework (React, Vue) | Vanilla JS |
| `function` keyword | Arrow functions / classes |
| Callback hell | `async` / `await` |
| Global variables | Module scope |

## 8. Device Loader Integration

```javascript
// device-loader.js — Dynamic CSS loading
window.DeviceLoader = {
    detect: function () { /* ... */ },
    load: load,
    setViewMode: function (mode) { /* ... */ },
    getDevice: function () { return _currentDevice; },
    getViewMode: function () { return _currentView; }
};
```

## 9. Theme Engine Integration (ADR-044)

```javascript
// ThemeManager.js — CSS custom properties ile tema uygulama
// Sayfa yenileme YOK — anında geçiş
document.documentElement.setAttribute('data-gender', 'female');
// → CSS: [data-gender="female"] { --theme-primary: #ff4fd8; }
```

---

*JavaScript Standards v2.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-06*
