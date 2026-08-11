---
title: "C15 — Toggle Component Prompt"
type: component-prompt
category: ui-design
component_id: "C15"
bem_class: ".toggle"
itcss_layer: "04_Components"
target_file: "css/04_Components/_toggle.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C15 — Toggle (.toggle)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.toggle` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_toggle.css` |
| **Kullanım Alanı** — Settings, notification toggle, on/off switches |
| **Bileşen Türü** — Custom checkbox toggle switch |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Genişlik | 50px | Track genişliği |
| Yükseklik | 28px | Track yüksekliği |
| Thumb boyutu | 22×22px | Dairesel thumb |
| Border-radius | 14px | `--radius-full` (track) |
| Thumb border-radius | 50% | Tam yuvarlak |
| Thumb offset | 3px | Kenar boşluğu |
| ⚠️ WCAG hit area | 32×32px minimum | 28px → 32px genişletilmeli |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Aktif track bg | Açık durum |
| `--border-subtle` | Pasif track bg | Kapalı durum |
| `--color-white` | Thumb rengi | Dairesel thumb |
| `--radius-full` | Track border radius | `14px` |
| `--transition-fast` | Animasyon | `200ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum hit area** | 44×44px | ⚠️ 28px → 44px |
| **Role** | `switch` | Custom toggle için |
| **Label** | `aria-label` | "Bildirimleri aç/kapat" |
| **Checked state** | `aria-checked` | Açık/kapalı durum |
| **Keyboard** | Space/Enter | Toggle |
| **Focus indicator** | visible | Outline |
| **Disabled** | `aria-disabled` | Pasif toggle |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C15 — Toggle
   ITCSS: 04_Components
   BEM: .toggle
   ============================================ */

.toggle {
  position: relative;
  display: inline-flex;
  align-items: center;
  cursor: pointer;

  /* WCAG: hit area via padding */
  padding: 8px;

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
}

/* Hidden checkbox */
.toggle__input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

/* Track */
.toggle__track {
  position: relative;
  width: 50px;
  height: 28px;
  border-radius: 14px;
  background: var(--border-subtle);
  transition: background-color var(--transition-fast);
}

/* Thumb */
.toggle__thumb {
  position: absolute;
  top: 3px;
  left: 3px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--color-white);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  transition: transform var(--transition-fast);
}

/* Checked / On state */
.toggle__input:checked + .toggle__track {
  background: var(--theme-primary);
}

.toggle__input:checked + .toggle__track .toggle__thumb {
  transform: translateX(22px);
}

/* Focus visible */
.toggle__input:focus-visible + .toggle__track {
  outline: 2px solid var(--theme-primary);
  outline-offset: 2px;
}

/* Disabled */
.toggle__input:disabled + .toggle__track {
  opacity: 0.5;
  cursor: not-allowed;
}

.toggle:has(.toggle__input:disabled) {
  cursor: not-allowed;
  pointer-events: none;
}

/* Label text (optional) */
.toggle__label {
  margin-left: 12px;
  font-family: var(--font-body);
  font-size: 14px;
  color: var(--color-text);
}

/* Small variant */
.toggle--sm .toggle__track {
  width: 40px;
  height: 22px;
  border-radius: 11px;
}

.toggle--sm .toggle__thumb {
  width: 18px;
  height: 18px;
  top: 2px;
  left: 2px;
}

.toggle--sm .toggle__input:checked + .toggle__track .toggle__thumb {
  transform: translateX(18px);
}

/* Responsive */
@media (max-width: 768px) {
  .toggle {
    padding: 10px; /* Larger hit area on mobile */
  }
}
```

## 6. Notlar

- Toggle, custom checkbox ile yapılır (`input[type="checkbox"]` hidden)
- `role="switch"` screen reader için zorunlu
- `aria-checked` ile durum bildirilir
- `padding: 8px` ile hit area 44×44px'e genişletilir
- Keyboard: Space veya Enter ile toggle
- Disabled state'de `pointer-events: none` ve `opacity: 0.5`
