---
type: decision
id: "001"
title: "ADR-001: Vanilla JS + ITCSS, Framework Yasak"
category: "frontend"
status: "frozen"
date: "2026-01-15"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [frontend, vanilla-js, itcss, css, framework, frozen]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[architecture/l3-presentation]]"
---

# ADR-001: Vanilla JS + ITCSS, Framework Yasak

---

## 1. Executive Summary

CoreMusic frontend'i **Vanilla JavaScript ES6+** ve **ITCSS 9-layer** ile yazılır. Framework kullanımı (React, Vue, Angular, jQuery) **kesinlikle yasaktır**. CSS ITCSS + BEM metodolojisi ile yönetilir.

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | critical |

## 3. Decision

### Yasaklı Framework'ler

| Framework | Durum | Neden |
|-----------|-------|-------|
| React | ❌ Yasak | ADR-001 |
| Vue | ❌ Yasak | ADR-001 |
| Angular | ❌ Yasak | ADR-001 |
| jQuery | ❌ Yasak | ADR-001 |
| Svelte | ❌ Yasak | ADR-001 |
| Next.js | ❌ Yasak | ADR-001 |

### ITCSS 9-Layer

| # | Layer | Amaç |
|---|-------|------|
| 1 | Settings | Global değişkenler |
| 2 | Tools | Mixin'ler, fonksiyonlar |
| 3 | Generic | Reset, normalize |
| 4 | Elements | Bare HTML elementleri |
| 5 | Objects | Layout Patterns |
| 6 | Components | BEM bileşenleri |
| 7 | Utilities | Yardımcı sınıflar |
| 8 | Animations | Animasyon tanımları |
| 9 | Overrides | !important override |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Vanilla JS ES6+ | ✅ Zorunlu |
| 2 | Framework yasak | ❌ Yasak |
| 3 | ITCSS 9-layer | ✅ Zorunlu |
| 4 | BEM methodology | ✅ Zorunlu |
| 5 | `var` yasak | ❌ Yasak |
| 6 | `innerHTML` yasak | ❌ Yasak |
| 7 | `eval()` yasak | ❌ Yasak |
| 8 | DOMParser + TrustedTypes | ✅ Zorunlu |
| 9 | ES6 modules | ✅ Zorunlu |
| 10 | `const`/`let` zorunlu | ✅ Zorunlu |

### Kod Örnekleri

```javascript
// ✅ DOĞRU — Vanilla JS ES6+
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

// ❌ YANLIŞ — jQuery
$('#user').load('/api/v1/users/1');

// ❌ YANLIŞ — React
const User = () => <div>{user.name}</div>;

// ❌ YANLIŞ — innerHTML
document.getElementById('content').innerHTML = '<div>Hello</div>';

// ✅ DOĞRU — DOMParser
const parser = new DOMParser();
const doc = parser.parseFromString('<div>Hello</div>', 'text/html');
document.getElementById('content').appendChild(doc.body.firstChild);
```

```css
/* ✅ DOĞRU — ITCSS + BEM */

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

/* ❌ YANLIŞ — jQuery syntax */
.player .controls { display: flex; }
.player .button.active { background: purple; }
```

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-001: Vanilla JS + ITCSS v2.0.0 — CoreMusic Frontend*
*Authority: UI Designer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
