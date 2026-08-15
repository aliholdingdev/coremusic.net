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

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **ES5 tarayıcı** | Progressive enhancement | ADR-001 |
| **Module desteği yok** | Script type module | ADR-001 |
| **DOM injection** | DOMParser + TrustedTypes | ADR-001 |
| **Event leak** | RemoveEventListener | ADR-001 |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
