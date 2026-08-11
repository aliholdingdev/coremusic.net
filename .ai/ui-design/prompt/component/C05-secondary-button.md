---
title: "C05 — Secondary Button Component Prompt"
type: component-prompt
category: ui-design
component_id: "C05"
bem_class: ".btn-secondary"
itcss_layer: "04_Components"
target_file: "css/04_Components/_btn-secondary.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C05 — Secondary Button (.btn-secondary)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.btn-secondary` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_btn-secondary.css` |
| **Kullanım Alanı** | İkincil aksiyonlar, cancel, back,次要 butonlar |
| **Bileşen Türü** | Secondary/ghost action button |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Yükseklik | 48px | Primary'den daha kısa |
| Border-radius | 8px | `--radius-md` |
| Padding | 0 20px | Yatay padding |
| Font boyutu | 14px | Primary'den daha küçük |
| Font weight | 500 | Medium |
| Border | 1px solid | Outline variant |
| Width | Auto | İçerik genişliğinde |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Border rengi | `border-color: var(--theme-primary)` |
| `--theme-primary` | Metin rengi | `color: var(--theme-primary)` |
| `--radius-md` | Border radius | `border-radius: 8px` |
| `--font-body` | Font ailesi | `font-family` |
| `--font-size-sm` | Font boyutu | `14px` |
| `--space-5` | Padding | `0 20px` |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ✅ 48px |
| **Touch target** | 44×44px | ✅ 48px height |
| **Renk kontrastı** | ≥4.5:1 | Theme-primary text on dark bg |
| **Focus indicator** | visible | Outline + shadow |
| **Disabled state** | opacity + aria-disabled | Görünür disabled |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C05 — Secondary Button
   ITCSS: 04_Components
   BEM: .btn-secondary
   ============================================ */

.btn-secondary {
  /* Reset */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 48px;
  padding: 0 var(--space-5);
  border: 1px solid var(--theme-primary);
  cursor: pointer;
  text-decoration: none;
  background: transparent;

  /* Typography */
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--theme-primary);
  line-height: 1;

  /* Shape */
  border-radius: var(--radius-md);

  /* Transition */
  transition: background-color var(--transition-fast),
              color var(--transition-fast),
              box-shadow var(--transition-fast),
              transform var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

/* Hover */
.btn-secondary:hover {
  background: var(--theme-primary);
  color: var(--color-white);
}

/* Active */
.btn-secondary:active {
  transform: scale(0.98);
}

/* Focus visible */
.btn-secondary:focus-visible {
  outline: 3px solid var(--theme-primary);
  outline-offset: 2px;
}

/* Disabled */
.btn-secondary:disabled,
.btn-secondary.is-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

/* Ghost variant (no border) */
.btn-secondary--ghost {
  border-color: transparent;
}

.btn-secondary--ghost:hover {
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text);
}

/* Danger variant */
.btn-secondary--danger {
  border-color: var(--color-danger);
  color: var(--color-danger);
}

.btn-secondary--danger:hover {
  background: var(--color-danger);
  color: var(--color-white);
}

/* Icon only */
.btn-secondary--icon {
  width: 48px;
  min-width: 0;
  padding: 0;
}

/* Small variant */
.btn-secondary--sm {
  height: 36px;
  padding: 0 12px;
  font-size: 12px;
  border-radius: 6px;
}
```

## 6. Notlar

- Ghost variant, header'daki "Back" butonları için kullanılır
- Danger variant, "Delete" veya "Remove" aksiyonları için kullanılır
- Hover'da background dolu, text beyaz olur (inverse)
- Focus-visible, keyboard navigasyonu için zorunludur
