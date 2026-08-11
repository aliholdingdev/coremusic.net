---
title: "C08 — Social Login Button Component Prompt"
type: component-prompt
category: ui-design
component_id: "C08"
bem_class: ".social-btn"
itcss_layer: "05_Pages"
target_file: "css/05_Pages/_social-btn.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C08 — Social Login Button (.social-btn)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.social-btn` |
| **ITCSS Layer** | `05_Pages` |
| **Hedef Dosya** | `css/05_Pages/_social-btn.css` |
| **Kullanım Alanı** — Auth sayfası Google/Apple/GitHub login |
| **Bileşen Türü** | Social OAuth login button (icon-only) |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Genişlik | 52px | Kare formunda |
| Yükseklik | 52px | Kare formunda |
| Border-radius | 12px | `--radius-lg` |
| Icon boyutu | 24×24px | Merkezde icon |
| Gap (butonlar arası) | 16px | Yatay boşluk |
| Border | 1px solid | Subtle border |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Arka plan | Glassmorphism |
| `--glass-border` | Kenarlık | `1px solid rgba(255,255,255,0.15)` |
| `--radius-lg` | Border radius | `border-radius: 12px` |
| `--color-text` | Icon rengi | Default icon color |
| `--transition-fast` | Animasyon | `150ms ease` |
| `--shadow-sm` | Hover gölgesi | `box-shadow` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum hit area** | 44×44px | ✅ 52×52px |
| **Touch target** | 44×44px | ✅ 52×52px |
| **Icon label** | `aria-label` zorunlu | "Google ile giriş yap" |
| **Focus indicator** | visible | Outline + shadow |
| **Role** | `button` | semantics |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C08 — Social Login Button
   ITCSS: 05_Pages
   BEM: .social-btn
   ============================================ */

.social-btn-group {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 52px;
  height: 52px;
  padding: 0;
  border: var(--glass-border);
  border-radius: var(--radius-lg);
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  cursor: pointer;

  /* Transition */
  transition: background-color var(--transition-fast),
              box-shadow var(--transition-fast),
              transform var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
}

/* Icon */
.social-btn__icon {
  width: 24px;
  height: 24px;
  color: var(--color-text);
  transition: transform var(--transition-fast);
}

/* Hover */
.social-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  box-shadow: var(--shadow-sm);
  transform: translateY(-2px);
}

/* Active */
.social-btn:active {
  transform: translateY(0);
  box-shadow: none;
}

/* Focus visible */
.social-btn:focus-visible {
  outline: 3px solid var(--theme-primary);
  outline-offset: 2px;
}

/* Provider-specific colors on hover */
.social-btn--google:hover {
  border-color: #ea4335;
  box-shadow: 0 0 0 3px rgba(234, 67, 53, 0.2);
}

.social-btn--apple:hover {
  border-color: #a2aaad;
  box-shadow: 0 0 0 3px rgba(162, 170, 173, 0.2);
}

.social-btn--github:hover {
  border-color: #6e40c9;
  box-shadow: 0 0 0 3px rgba(110, 64, 201, 0.2);
}

/* Hidden input */
.social-btn input[type="hidden"] {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}
```

## 6. Notlar

- Her social button `aria-label` ile okunabilir etiket taşımazsa screen reader'da anlamsızdır
- `aria-label="Google ile giriş yap"` gibi lokalize etiketler eklenmelidir
- Hover'da provider rengi ile border glow efekti verilir
- Loading durumunda `is-loading` class'ı ile spinner eklenebilir
- OAuth redirect öncesi disabled state gerekebilir
