---
title: "C12 — Star Rating Component Prompt"
type: component-prompt
category: ui-design
component_id: "C12"
bem_class: ".star-rating"
itcss_layer: "04_Components"
target_file: "css/04_Components/_star-rating.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C12 — Star Rating (.star-rating)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.star-rating` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_star-rating.css` |
| **Kullanım Alanı** | Şarkı/albüm puanlama (1-5 yıldız) |
| **Bileşen Türü** | Interactive star rating input |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Yıldız boyutu | 20×20px | Tek yıldız |
| Toplam genişlik | ~104px | 5 yıldız + gap |
| Gap | 4px | Yıldızlar arası |
| ⚠️ WCAG hit area | 48×48px minimum | 20px → 48px genişletilmeli |
| Renk (dolu) | `--color-star` | Sarı/turuncu |
| Renk (boş) | `--border-subtle` | Gri |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--color-star` | Dolu yıldız rengi | `fill: var(--color-star)` |
| `--border-subtle` | Boş yıldız rengi | `stroke: var(--border-subtle)` |
| `--transition-fast` | Animasyon | `150ms ease` |
| `--color-text-muted` | Rating text | "4.5" gibi |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum hit area** | 44×44px | ⚠️ 20px → 48px |
| **Role** | `radiogroup` | Container için |
| **Radio role** | `radio` | Her yıldız için |
| **Label** | `aria-label` | "4 yıldız ver" |
| **Keyboard** | Arrow keys | Yıldız seçimi |
| **Focus indicator** | visible | Yıldız outline |
| **Screen reader** | "4/5 yıldız" | Mevcut değer |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C12 — Star Rating
   ITCSS: 04_Components
   BEM: .star-rating
   ============================================ */

.star-rating {
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

/* Individual star button */
.star-rating__star {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;   /* Base size */
  height: 28px;
  padding: 0;
  border: none;
  background: none;
  cursor: pointer;
  color: var(--border-subtle);
  transition: color var(--transition-fast),
              transform var(--transition-fast);

  /* WCAG: hit area via padding */
  min-width: 44px;
  min-height: 44px;
}

/* Star icon */
.star-rating__icon {
  width: 20px;
  height: 20px;
  transition: transform 200ms ease, fill 200ms ease;
}

/* Filled state */
.star-rating__star.is-filled,
.star-rating__star[aria-checked="true"] {
  color: var(--color-star);
}

.star-rating__star.is-filled .star-rating__icon {
  fill: var(--color-star);
}

/* Hover preview */
.star-rating__star:hover {
  transform: scale(1.2);
}

.star-rating__star:hover .star-rating__icon {
  fill: var(--color-star);
  stroke: var(--color-star);
}

/* Focus visible */
.star-rating__star:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: 2px;
  border-radius: 4px;
}

/* Half star (optional) */
.star-rating__star--half {
  position: relative;
  overflow: hidden;
}

/* Rating text */
.star-rating__text {
  margin-left: 8px;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text);
}

.star-rating__count {
  margin-left: 4px;
  font-size: 12px;
  font-weight: 400;
  color: var(--color-text-muted);
}

/* Read-only mode */
.star-rating--readonly .star-rating__star {
  cursor: default;
  pointer-events: none;
}

/* Responsive */
@media (max-width: 768px) {
  .star-rating__star {
    min-width: 44px;
    min-height: 44px;
  }
}
```

## 6. Notlar

- PNG'deki 20px yıldız, WCAG hit area standardının altındadır → `min-width/min-height: 44px` ile düzeltilmeli
- Hover'da yıldız preview (mouse'un altında kalan yıldızlara kadar dolu göster)
- Keyboard: Arrow keys ile 1-5 arası seçim
- `aria-label="4 yıldız ver"` gibi okunabilir etiket
- Read-only mode: Sadece gösterim, etkileşim yok
