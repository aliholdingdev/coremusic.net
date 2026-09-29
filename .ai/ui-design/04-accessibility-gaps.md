---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Accessibility Gaps (WCAG 2.2 AA)"
type: analysis
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 3.1.0
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

> **⚠️ Touch Target Gate — 48px (zorunlu):** CoreMusic touch tabanı **48px**'tir ve bu dosyadaki **tüm** touch hedefleri buna göre değerlendirilir. Kaynak: `00-device-matrix` §3 touch sütunu (48px-128px) · `05-responsive-architecture` §6 `--cm-touch-target: 48px`. 44px yalnız tarihsel eştir (bkz. §2 uzlaştırması).

---

## 2. Touch Target Analizi

**Ölçüm tabanı (üç değer, karıştırılmaz):**

| Değer | Kaynak | Hangi SC | Bağlayıcılık |
|-------|--------|----------|--------------|
| **24 px** | WCAG 2.2 **SC 2.5.8** Target Size (Minimum) — **AA** | AA | Bağlayıcı (Inner Join: 24×24 CSS px) |
| 44 px | WCAG 2.2 **SC 2.5.5** Target Size (Enhanced) — **AAA** | AAA | Bağlayıcı **değil** — yalnız referans |
| **48 px** | Proje zemini: `00-device-matrix` §3 (48-128 px) + `05-responsive-architecture` §6 `--cm-touch-target: 48px` | — | Proje teslimat eşiği |

> **Uzlaştırma kuralı:** Eski tabloda "WCAG Hedefi = 44×44px" yazıyordu; bu **AAA** eşiğidir ve AA teslimatını ölçmez. Yeni ölçüm: her satır hem **AA (24 px)** hem **proje zemini (48 px)** ile karşılaştırılır. AA'yı karşılayıp 48 px'i karşılamayan satır AA gap'i değil, **proje zemini gap'idir** (`⚠️`); ikisini de karşılamayan gerçek AA gap'idir (`❌`).

| Bileşen | Minimum | WCAG 2.2 AA (SC 2.5.8, 24px) | Proje zemini (48px) | Durum |
|---------|---------|------------------------------|---------------------|-------|
| NavLink (C01) | 44px | ≥24 ✅ | ≥48 ❌ | ⚠️ AA ✅ · proje zemini 4 px eksik |
| Button (C04) | 36px | ≥24 ✅ | ≥48 ❌ | ⚠️ AA ✅ · sm variant + 48 px zemini eksik |
| Input (C05) | 40px | ≥24 ✅ | ≥48 ❌ | ⚠️ AA ✅ · proje zemini eksik |
| Toggle (C08) | 44×24px | genişlik ≥24 ✅ · yükseklik 24 tam sınır ⚠️ | 48×48 ❌ | ❌ AA yükseklik sınırda · 48 px zemini eksik |
| Slider (C09) | 14px thumb | 14 < 24 ❌ | 48 ❌ | ❌ **Gerçek AA gap** (SC 2.5.8 24 px altında) |
| Tab (C06) | 36px | ≥24 ✅ | ≥48 ❌ | ⚠️ AA ✅ · proje zemini eksik |
| Badge (C10) | 24px | ≥24 ✅ (dekoratif) | — | ✅ AA · dekoratif, proje zemini kapsam dışı |
| Avatar (C11) | 24px | ≥24 ✅ (dekoratif) | — | ✅ AA · dekoratif, proje zemini kapsam dışı |
| Dropdown (C14) | 32px | ≥24 ✅ | ≥48 ❌ | ⚠️ AA ✅ · item height 48 px zemininde eksik |

**Gap sayımı (QR §8 ile birebir):** Touch Target Issues **7** = AA gap'leri (Slider ❌ + Toggle yükseklik sınırı ❌ dahil) + proje zemini gap'leri (NavLink, Input, Tab, Dropdown… `⚠️`). Badge/Avatar dekoratif kabul edilir, sayılmaz.

### Touch Target Düzeltme Önerileri

```css
/* ÖLÇÜM: proje zemini 48px (00-device-matrix §3 + 05-responsive-architecture §6).
   AA tabanı SC 2.5.8 = 24px; 44px = SC 2.5.5 AAA (bağlayıcı değil).
   Aşağıdaki düzeltmeler 48px zeminine göre yazılır. */

/* Toggle: 48px touch target */
.toggle {
  min-width: 48px;
  min-height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Slider: 48px touch area (thumb 14px → AA 24px alt sınırı da aşar) */
.slider {
  position: relative;
  height: 48px;
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
  height: 48px;
}

/* Dropdown item: 48px height */
.dropdown__item {
  min-height: 48px;
  display: flex;
  align-items: center;
}
```

> **TV / 4K notu:** `05-responsive-architecture` §6'daki `--cm-touch-target: 32px` (TV) ve `24px` (4K) zeminleri **AA'yı** (SC 2.5.8: 24 px) karşılar veya tam sınırdadır; 48 px proje zeminine göre eksiktir. Bu satırlar `⚠️` (proje zemini gap'i) olarak raporlanır — **doymuş viewport/touch verisi olmadığı için** gerçek ölçüm `⚠️ VERIFICATION REQUIRED` + `status: planlanmış` kalır.

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
| Version | 3.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 8 |
| Touch Target Issues | 7 (2 ❌ AA + 5 ⚠️ proje zemini) |
| Contrast Issues | 2 |
| Keyboard Issues | 4 |
| Screen Reader Issues | 4 |
| Total Gaps | 17 |
| WCAG Ölçüm Tabanı | SC 2.5.8 (AA, 24px) + proje zemini 48px; 44px = SC 2.5.5 AAA (bağlayıcı değil) |
| Cross References | 3 |
| Last Updated | 2026-09-29 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
