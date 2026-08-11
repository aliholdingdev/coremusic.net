---
title: "C07 — Gender Button Component Prompt"
type: component-prompt
category: ui-design
component_id: "C07"
bem_class: ".gender-btn"
itcss_layer: "05_Pages"
target_file: "css/05_Pages/_gender-btn.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C07 — Gender Button (.gender-btn)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.gender-btn` |
| **ITCSS Layer** | `05_Pages` |
| **Hedef Dosya** | `css/05_Pages/_gender-btn.css` |
| **Kullanım Alanı** | Auth sayfası gender seçimi (female/male/neutral) |
| **Bileşen Türü** | Gender selection button (card-style) |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Genişlik | ~284px | Auth layout'a göre |
| Yükseklik | 60px | Tam genişlik buton |
| Border-radius | 12px | `--radius-lg` |
| Padding | 16px | İç padding |
| Icon boyutu | 24×24px | Sol tarafta gender icon |
| Gap | 12px | Icon ile text arası |
| Font boyutu | 16px | Primary text |
| Border | 2px solid | Seçili durumda |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Seçili border | Aktif durum border |
| `--theme-primary` | Seçili arka plan | %10 opacity |
| `--radius-lg` | Border radius | `border-radius: 12px` |
| `--glass-bg` | Normal arka plan | Glassmorphism |
| `--border-subtle` | Normal border | `1px solid` |
| `--color-text` | Button text | Metin rengi |
| `--color-text-muted` | Description text | İkincil metin |
| `--font-size-base` | Font boyutu | `16px` |
| `--space-4` | Padding | `16px` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ✅ 60px |
| **Touch target** | 44×44px | ✅ 60px height |
| **Renk kontrastı** | ≥4.5:1 | Text on glass bg |
| **Focus indicator** | visible | Border + shadow |
| **Radio group** | `role="radiogroup"` | Container için |
| **Radio button** | `role="radio"` | Her button için |
| **Selected state** | `aria-checked` | Seçili durum |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C07 — Gender Button
   ITCSS: 05_Pages
   BEM: .gender-btn
   ============================================ */

.gender-btn {
  position: relative;
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  height: 60px;
  padding: 0 var(--space-4);
  border: 2px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  cursor: pointer;

  /* Typography */
  font-family: var(--font-body);
  font-size: var(--font-size-base);
  font-weight: 500;
  color: var(--color-text);

  /* Transition */
  transition: border-color var(--transition-fast),
              background-color var(--transition-fast),
              box-shadow var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

/* Hover */
.gender-btn:hover {
  border-color: rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.08);
}

/* Selected / Checked */
.gender-btn.is-selected,
.gender-btn[aria-checked="true"] {
  border-color: var(--theme-primary);
  background: rgba(var(--theme-primary-rgb), 0.1);
  box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.15);
}

/* Focus visible */
.gender-btn:focus-visible {
  outline: none;
  border-color: var(--theme-primary);
  box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.2);
}

/* Icon */
.gender-btn__icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  color: var(--color-text-muted);
  transition: color var(--transition-fast);
}

.gender-btn.is-selected .gender-btn__icon {
  color: var(--theme-primary);
}

/* Text group */
.gender-btn__text {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.gender-btn__title {
  font-weight: 600;
  line-height: 1.2;
}

.gender-btn__desc {
  font-size: 12px;
  color: var(--color-text-muted);
  font-weight: 400;
}

/* Radio input (hidden) */
.gender-btn input[type="radio"] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .gender-btn {
    height: 56px;
    padding: 0 12px;
  }
}
```

## 6. Notlar

- Gender button'ları `role="radiogroup"` container'ı içinde olmalıdır
- Her button `role="radio"` ve `aria-checked` attribute'u taşımalıdır
- `data-gender` attribute'u theme engine ile entegre çalışır
- Seçili durumda `box-shadow` ile visual feedback sağlanır
- Keyboard'da Arrow keys ile seçim yapılmalıdır
