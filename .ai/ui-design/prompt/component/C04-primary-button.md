---
title: "C04 — Primary Button Component Prompt"
type: component-prompt
category: ui-design
component_id: "C04"
bem_class: ".btn-primary"
itcss_layer: "04_Components"
target_file: "css/04_Components/_btn-primary.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C04 — Primary Button (.btn-primary)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.btn-primary` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_btn-primary.css` |
| **Kullanım Alanı** | Form submit, ana aksiyonlar, CTA |
| **Bileşen Türü** | Primary action button |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Yükseklik | 56px | Tam genişlik butonu |
| Border-radius | 8px | `--radius-md` |
| Padding | 0 24px | Yatay padding |
| Font boyutu | 16px | Primary text |
| Font weight | 600 | Bold |
| Width | 100% | Tam genişlik (form içinde) |
| Min width | 120px |最小 genişlik (inline) |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Arka plan rengi | `background: var(--theme-primary)` |
| `--color-white` | Metin rengi | `color: #fff` |
| `--radius-md` | Border radius | `border-radius: 8px` |
| `--font-body` | Font ailesi | `font-family` |
| `--font-size-base` | Font boyutu | `16px` |
| `--space-6` | Padding | `0 24px` |
| `--shadow-sm` | Hover gölgesi | `box-shadow` |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ✅ 56px (WCAG'den büyük) |
| **Touch target** | 44×44px | ✅ 56px height |
| **Renk kontrastı** | ≥4.5:1 | Beyaz text trên theme-primary |
| **Focus indicator** | visible | `box-shadow` veya `outline` |
| **Disabled state** | opacity + aria-disabled | Görünür disabled |
| **Loading state** | spinner + aria-busy | Async aksiyonlar için |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C04 — Primary Button
   ITCSS: 04_Components
   BEM: .btn-primary
   ============================================ */

.btn-primary {
  /* Reset */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  min-width: 120px;
  height: 56px;
  padding: 0 var(--space-6);
  border: none;
  cursor: pointer;
  text-decoration: none;

  /* Typography */
  font-family: var(--font-body);
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--color-white);
  line-height: 1;
  letter-spacing: 0.3px;

  /* Background & Shape */
  background: var(--theme-primary);
  border-radius: var(--radius-md);

  /* Transition */
  transition: background-color var(--transition-fast),
              box-shadow var(--transition-fast),
              transform var(--transition-fast),
              opacity var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

/* Focus visible — RPi5 touch-only, hover yok */
.btn-primary:focus-visible {
  outline: 3px solid var(--theme-primary);
  outline-offset: 2px;
  box-shadow: 0 0 0 6px rgba(var(--theme-primary-rgb), 0.2);
}

/* Active — parmak basıldığında */
.btn-primary:active {
  transform: scale(0.97);
  transition: transform 100ms ease;
}

/* Disabled */
.btn-primary:disabled,
.btn-primary.is-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
  transform: none;
}

/* Loading */
.btn-primary.is-loading {
  color: transparent;
  pointer-events: none;
  position: relative;
}

.btn-primary.is-loading::after {
  content: "";
  position: absolute;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: var(--color-white);
  border-radius: 50%;
  animation: btn-spin 600ms linear infinite;
}

@keyframes btn-spin {
  to { transform: rotate(360deg); }
}

/* Icon only variant */
.btn-primary--icon {
  width: 56px;
  min-width: 0;
  padding: 0;
}

/* Small variant */
.btn-primary--sm {
  height: 40px;
  padding: 0 16px;
  font-size: 14px;
  border-radius: 6px;
}
```

## 6. Notlar

- `--theme-primary-hover`, theme-primary'den %10 koyu otomatik türetilmeli
- Loading state, async form submit'lerde kullanılır
- `aria-busy="true"` loading state'de zorunlu
- Disabled state'de `aria-disabled="true"` eklenmelidir
- High contrast mode'da border eklenmelidir
