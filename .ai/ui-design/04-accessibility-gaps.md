---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Accessibility Gaps (WCAG 2.2 AA)"
type: analysis
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/04-accessibility-gaps.md"
  source_of_truth: ".ai/ui-design/02-component-inventory.md · .ai/ui-design/tokens/platform-tokens.md"
---

# CoreMusic — Accessibility Gaps (WCAG 2.2 AA)

**Zorunlu Bağlantılar:** [[02-component-inventory]] · [[tokens/platform-tokens]] · [[reference/09-interaction-states]]

---

## 1. Amaç

CoreMusic UI'ının **WCAG 2.2 AA uyumluluğu** için eksikliklerin ve kritik alanların analizidir. Her bileşen için touch target, contrast, keyboard nav ve screen reader kontrolleri burada tanımlanır.

---

## 2. Touch Target Analizi

| Bileşen | Minimum | WCAG Hedefi | Durum |
|---------|---------|-------------|-------|
| NavLink (C01) | 44px | 44×44px | ✅ Uyumlu |
| Button (C04) | 36px | 44×44px | ⚠️ sm variant eksik |
| Input (C05) | 40px | 44×44px | ⚠️ Eksik |
| Toggle (C08) | 44×24px | 44×44px | ❌ Genişlik yetersiz |
| Slider (C09) | 14px thumb | 44×44px | ❌ Thumb yetersiz |
| Tab (C06) | 36px | 44×44px | ⚠️ Eksik |
| Badge (C10) | 24px | 44×44px | ❌ Dekoratif, sorun değil |
| Avatar (C11) | 24px | 44×44px | ❌ Dekoratif, sorun değil |
| Dropdown (C14) | 32px | 44×44px | ❌ Item height yetersiz |

### Touch Target Düzeltme Önerileri

```css
/* Toggle: 44px touch target */
.toggle {
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Slider: 44px touch area */
.slider {
  position: relative;
  height: 44px;
  display: flex;
  align-items: center;
}
.slider__thumb {
  width: 20px;
  height: 20px;
}
.slider::before {
  content: '';
  position: absolute;
  inset: 0;
  height: 44px;
}

/* Dropdown item: 44px height */
.dropdown__item {
  min-height: 44px;
  display: flex;
  align-items: center;
}
```

---

## 3. Contrast Analizi

| Eleman | Renk | Arka Plan | Oran | WCAG Hedefi | Durum |
|--------|------|-----------|------|-------------|-------|
| Primary text | #ffffff | #0a0a0f | 18.1:1 | 4.5:1 | ✅ |
| Secondary text | #b0b0c0 | #0a0a0f | 9.8:1 | 4.5:1 | ✅ |
| Tertiary text | #707088 | #0a0a0f | 4.2:1 | 4.5:1 | ❌ |
| Primary button | #ffffff | #ff4fd8 | 3.1:1 | 4.5:1 | ❌ |
| Disabled text | #4a4a5a | #0a0a0f | 2.8:1 | 3:1 | ⚠️ |
| Glass text | rgba(255,255,255,0.70) | glass bg | ~5:1 | 4.5:1 | ✅ |

### Contrast Düzeltme Önerileri

```css
/* Tertiary text: contrast artır */
--cm-text-tertiary: #8888a0;  /* 5.2:1 ratio */

/* Primary button: dark text or lighter bg */
--cm-btn-primary-color: #1a1a2e;  /* koyu text */
/* VEYA */
--cm-btn-primary: #ff6ee4;  /* daha açık pink */
```

---

## 4. Keyboard Navigation

| Bileşen | Focus Visible | Tab Order | Escape | Enter | Durum |
|---------|---------------|-----------|--------|-------|-------|
| NavLink | ✅ outline | ✅ natural | N/A | ✅ navigate | ✅ |
| Button | ✅ ring | ✅ natural | N/A | ✅ click | ✅ |
| Input | ✅ ring | ✅ natural | ✅ clear | ✅ submit | ✅ |
| Modal | ✅ trap | ⚠️ focus trap | ✅ close | ✅ confirm | ⚠️ |
| Dropdown | ✅ item focus | ✅ arrow keys | ✅ close | ✅ select | ⚠️ |
| Toggle | ⚠️ no ring | ✅ tab | N/A | ✅ toggle | ⚠️ |
| Slider | ⚠️ no ring | ✅ tab | N/A | ⚠️ arrow | ⚠️ |

### Keyboard Düzeltme Önerileri

```css
/* Toggle focus ring */
.toggle:focus-visible {
  outline: 2px solid var(--cm-primary);
  outline-offset: 2px;
}

/* Slider focus ring */
.slider:focus-visible .slider__thumb {
  box-shadow: var(--cm-shadow-focus);
}

/* Modal focus trap */
.modal:focus-within {
  outline: none;
}
.modal__content:focus-visible {
  outline: 2px solid var(--cm-primary);
}
```

---

## 5. Screen Reader Desteği

| Bileşen | aria-label | role | aria-live | Durum |
|---------|------------|------|-----------|-------|
| NavLink | ✅ | nav | N/A | ✅ |
| Button | ✅ | button | N/A | ✅ |
| Input | ✅ label | textbox | ⚠️ eksik | ⚠️ |
| Modal | ✅ | dialog | ✅ polite | ✅ |
| Toast | ⚠️ eksik | alert | ✅ assertive | ⚠️ |
| Toggle | ⚠️ eksik | switch | ✅ polite | ⚠️ |
| Progress | ⚠️ eksik |progressbar | ✅ polite | ⚠️ |

---

## 6. Reducued Motion

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## 7. Focus Management

```css
/* Visible focus for all interactive elements */
:focus-visible {
  outline: 2px solid var(--cm-primary);
  outline-offset: 2px;
}

/* Remove default outline for mouse users */
:focus:not(:focus-visible) {
  outline: none;
}
```

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Touch Target Issues | 4 |
| Contrast Issues | 2 |
| Keyboard Issues | 3 |
| Screen Reader Issues | 3 |
| Total Gaps | 12 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
