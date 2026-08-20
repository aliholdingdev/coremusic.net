---
title: "ADR-001: Vanilla JS + ITCSS, Framework Yasak"
status: frozen
date: 2026-01-15
tags: [frontend, vanilla-js, itcss, css, framework, frozen]
---

# ADR-001: Vanilla JS + ITCSS, Framework Yasak

---

## 1. Executive Summary

CoreMusic frontend'i **Vanilla JavaScript ES6+** ve **ITCSS 9-layer** ile yazÄ±lÄ±r. Framework kullanÄ±mÄ± (React, Vue, Angular, jQuery) **kesinlikle yasaktÄ±r**. CSS ITCSS + BEM metodolojisi ile yÃ¶netilir.

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | critical |

## 3. Decision

### YasaklÄ± Framework'ler

| Framework | Durum | Neden |
|-----------|-------|-------|
| React | âŒ Yasak | ADR-001 |
| Vue | âŒ Yasak | ADR-001 |
| Angular | âŒ Yasak | ADR-001 |
| jQuery | âŒ Yasak | ADR-001 |
| Svelte | âŒ Yasak | ADR-001 |
| Next.js | âŒ Yasak | ADR-001 |

### ITCSS 9-Layer

| # | Layer | AmaÃ§ |
|---|-------|------|
| 1 | Settings | Global deÄŸiÅŸkenler |
| 2 | Tools | Mixin'ler, fonksiyonlar |
| 3 | Generic | Reset, normalize |
| 4 | Elements | Bare HTML elementleri |
| 5 | Objects | Layout Patterns |
| 6 | Components | BEM bileÅŸenleri |
| 7 | Utilities | YardÄ±mcÄ± sÄ±nÄ±flar |
| 8 | Animations | Animasyon tanÄ±mlarÄ± |
| 9 | Overrides | !important override |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Vanilla JS ES6+ | âœ… Zorunlu |
| 2 | Framework yasak | âŒ Yasak |
| 3 | ITCSS 9-layer | âœ… Zorunlu |
| 4 | BEM methodology | âœ… Zorunlu |
| 5 | `var` yasak | âŒ Yasak |
| 6 | `innerHTML` yasak | âŒ Yasak |
| 7 | `eval()` yasak | âŒ Yasak |
| 8 | DOMParser + TrustedTypes | âœ… Zorunlu |
| 9 | ES6 modules | âœ… Zorunlu |
| 10 | `const`/`let` zorunlu | âœ… Zorunlu |

### Kod Ã–rnekleri

```javascript
// âœ… DOÄRU â€” Vanilla JS ES6+
const UserManager = (() => {
    'use strict';

    async function loadUser(userId) {
        const response = await fetch(`/api/v1/users/${userId}`, {
            credentials: 'same-origin',
        });
        return response.json();
    }

    return Object.freeze({ loadUser });
})();

// âŒ YANLIÅ â€” jQuery
$('#user').load('/api/v1/users/1');

// âŒ YANLIÅ â€” React
const User = () => <div>{user.name}</div>;

// âŒ YANLIÅ â€” innerHTML
document.getElementById('content').innerHTML = '<div>Hello</div>';

// âœ… DOÄRU â€” DOMParser
const parser = new DOMParser();
const doc = parser.parseFromString('<div>Hello</div>', 'text/html');
document.getElementById('content').appendChild(doc.body.firstChild);
```

```css
/* âœ… DOÄRU â€” ITCSS + BEM */

/* 1. Settings */
:root {
    --color-primary: #6366f1;
    --color-secondary: #8b5cf6;
    --spacing-unit: 8px;
}

/* 6. Components */
.player {
    &__controls {
        display: flex;
        gap: var(--spacing-unit);
    }

    &__button {
        background: var(--color-primary);
        border: none;
        border-radius: 4px;
        padding: var(--spacing-unit) calc(var(--spacing-unit) * 2);

        &--active {
            background: var(--color-secondary);
        }
    }
}

/* âŒ YANLIÅ â€” jQuery syntax */
.player .controls { display: flex; }
.player .button.active { background: purple; }
```

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-001: Vanilla JS + ITCSS v2.0.0 â€” CoreMusic Frontend*
*Authority: UI Designer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*