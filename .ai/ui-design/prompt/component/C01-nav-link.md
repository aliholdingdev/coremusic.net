---
title: "C01 — Navigation Link Component Prompt"
type: component-prompt
category: ui-design
component_id: "C01"
bem_class: ".nav-link"
itcss_layer: "03_Layout"
target_file: "css/03_Layout/_nav-link.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C01 — Navigation Link (.nav-link)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.nav-link` |
| **ITCSS Layer** | `03_Layout` |
| **Hedef Dosya** | `css/03_Layout/_nav-link.css` |
| **Kullanım Alanı** | Header navigation, sidebar navigation, tab bar |
| **Bileşen Türü** | Interactive navigation element |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Font boyutu | 10px | Header'da küçük navigasyon linkleri |
| Gap (aralık) | 2–4px | Linkler arası boşluk |
| Hit area | ~24px | WCAG minimum hit area: 48px |
| Padding | 0 | Sadece text + gap |
| Hover area | 48×48px min | Touch friendly |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--color-text-muted` | Pasif link rengi | Normal durum |
| `--color-text` | Aktif link rengi | Hover / Active durum |
| `--font-body` | Font ailesi | `font-family` |
| `--font-size-xs` | Font boyutu | `10px` |
| `--space-2` | Gap | `2px` |
| `--space-4` | Hit area padding | `48px` minimum hit area |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum hit area** | 48×48px | ⚠️ WCAG 2.2 AA zorunlu |
| **Metin boyutu** | ≥10px | Minimum okunabilirlik |
| **Renk kontrastı** | ≥4.5:1 | Normal metin için |
| **Focus indicator** | visible | Keyboard navigasyonu için |
| **Touch target** | 44×44px min | Mobil için zorunlu |

**⚠️ UYARI:** PNG'deki 24px hit area, WCAG 48px standardının altındadır. CSS'te `padding` ile genişletilmelidir.

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C01 — Navigation Link
   ITCSS: 03_Layout
   BEM: .nav-link
   ============================================ */

.nav-link {
  /* Reset */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  cursor: pointer;

  /* Typography */
  font-family: var(--font-body);
  font-size: var(--font-size-xs);
  font-weight: 500;
  line-height: 1;
  color: var(--color-text-muted);

  /* Spacing — WCAG hit area */
  padding: 12px 8px; /* 10px font'i 48px hit area'ya genişlet */
  margin: 0;
  gap: var(--space-2);

  /* Transition */
  transition: color var(--transition-fast),
              background-color var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

.nav-link:hover,
.nav-link:focus-visible {
  color: var(--color-text);
  outline: none;
}

.nav-link:focus-visible {
  box-shadow: 0 0 0 2px var(--theme-primary);
  border-radius: 4px;
}

.nav-link.is-active {
  color: var(--theme-primary);
  font-weight: 600;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-link {
    font-size: 12px;
    padding: 14px 10px; /* Mobilde daha geniş hit area */
  }
}
```

## 6. Notlar

- PNG'deki 10px font, desktop için uygundur; mobilde 12px'e çıkmalıdır
- Hit area, `padding` ile 48px'e genişletilmelidir
- Focus-visible, keyboard navigasyonu için zorunludur
- `aria-current="page"` attribute'u aktif link'e eklenmelidir
