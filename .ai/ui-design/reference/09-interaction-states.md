---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Interaction States"
type: reference
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/09-interaction-states.md"
  source_of_truth: ".ai/ui-design/02-component-inventory.md · .ai/ui-design/tokens/component-tokens.md"
---

# CoreMusic — Interaction States

**Zorunlu Bağlantılar:** [[02-component-inventory]] · [[tokens/component-tokens]] · [[04-accessibility-gaps]]

---

## 1. Amaç

Tüm UI bileşenlerinin **etkileşim durumlarının** (touch vs mouse) tanımıdır.

---

## 2. Durum Tanımları

### 2.1 Mouse Kullanıcıları

| Durum | CSS | Görsel |
|-------|-----|--------|
| Default | Base stil | Normal görünüm |
| Hover | `:hover` | Hafif bg change, scale |
| Active | `:active` | Basılı appearance |
| Focus | `:focus-visible` | Focus ring |
| Disabled | `:disabled` | Opacity 0.4, cursor not-allowed |

### 2.2 Touch Kullanıcıları

| Durum | CSS | Görsel |
|-------|-----|--------|
| Default | Base stil | Normal görünüm |
| Touch | `:active` | Hafif bg change (no scale) |
| Focus | `:focus-visible` | Focus ring |
| Disabled | `:disabled` | Opacity 0.4 |

---

## 3. Component State Matrix

| Bileşen | Default | Hover | Active | Focus | Disabled |
|---------|---------|-------|--------|-------|----------|
| NavLink | bg: transparent | bg: var(--cm-hover-bg) | bg: var(--cm-selected-bg) | ring: primary | opacity: 0.4 |
| Button Primary | bg: primary | bg: primary-light | bg: primary-dark | ring: primary | opacity: 0.4 |
| Button Secondary | bg: transparent | bg: var(--cm-hover-bg) | bg: var(--cm-active-bg) | ring: primary | opacity: 0.4 |
| Input | bg: surface | border: default | border: primary | ring: primary | opacity: 0.4 |
| Card | bg: glass | bg: glass-hover | — | — | — |
| Toggle | bg: gray-700 | — | bg: primary | ring: primary | opacity: 0.4 |
| Slider | track: gray-700 | — | fill: primary | thumb: ring | opacity: 0.4 |

---

## 4. Media Query: Hover Capability

```css
/* Sadece hover yeteneği olan cihazlarda hover efektleri */
@media (hover: hover) and (pointer: fine) {
  .card:hover {
    transform: translateY(-2px);
    box-shadow: var(--cm-shadow-lg);
  }
  .btn:hover {
    background-color: var(--cm-btn-primary-bg-hover);
  }
}

/* Touch cihazlarda hover efekti yok */
@media (hover: none) and (pointer: coarse) {
  .card:hover {
    transform: none;
    box-shadow: var(--cm-shadow-sm);
  }
}
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Interaction States | 5 |
| Component States | 7 |
| Media Queries | 2 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
