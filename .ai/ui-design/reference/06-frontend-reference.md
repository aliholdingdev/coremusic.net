---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Frontend Reference (ITCSS, Vanilla JS, BEM)"
type: reference
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/06-frontend-reference.md"
  source_of_truth: ".ai/CLAUDE.md §12 · .ai/brain.md §18A"
---

# CoreMusic — Frontend Reference (ITCSS, Vanilla JS, BEM)

**Zorunlu Bağlantılar:** [[05-responsive-architecture]] · [[02-component-inventory]] · [[03-implementation-plan]]

---

## 1. Amaç

Frontend geliştirme standartlarının **referans kaynağıdır**. ITCSS katmanları, Vanilla JS kuralları ve BEM formatı burada tanımlanır.

---

## 2. ITCSS 9-Layer Yapısı

```
01_Abstracts/     → Token'lar (renk, boşluk, tipografi)
02_Base/          → Reset, base stiller
03_Layout/        → Header, footer, grid
04_Components/    → Bileşenler (card, button, input)
05_Pages/         → Sayfa özel stiller
06_Utilities/     → Helper classes
07_Vendors/       → Bootstrap (minimal)
08_Devices/       → Cihaz behavioral overrides
09_ViewModes/     → View mode özel stiller
```

---

## 3. BEM Formatı

```
.block {}
.block__element {}
.block--modifier {}
.block__element--modifier {}

/* Örnek */
.card {}
.card__image {}
.card__title {}
.card--compact {}
.card__title--large {}
```

---

## 4. Vanilla JS Kuralları

| Kural | Açıklama |
|-------|----------|
| `const`/`let` | `var` yasak |
| `async/await` | Promise zinciri yerine |
| `DOMParser` | `innerHTML` yasak |
| `AbortController` | Request abort |
| `#private` | Private fields |
| Event delegation | Tek listener, bubbling |
| ES Modules | `import`/`export` |
| No frameworks | React/Vue/Angular yasak |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `var x = 1` | `const x = 1` |
| `el.innerHTML = data` | `DOMParser.parseFromString()` |
| `React.createElement()` | `document.createElement()` |
| `$('#el')` | `document.querySelector()` |
| `$.ajax()` | `fetch()` |
| Global variable | Module scope |

---

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ITCSS Layers | 9 |
| BEM Rules | 5 |
| JS Rules | 8 |
| Forbidden Patterns | 6 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
