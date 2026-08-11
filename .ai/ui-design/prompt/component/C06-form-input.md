---
title: "C06 — Form Input Component Prompt"
type: component-prompt
category: ui-design
component_id: "C06"
bem_class: ".form-input"
itcss_layer: "04_Components"
target_file: "css/04_Components/_form-input.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C06 — Form Input (.form-input)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.form-input` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_form-input.css` |
| **Kullanım Alanı** | Login, register, search,所有 form alanları |
| **Bileşen Türü** | Text input with glass background |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Yükseklik | 56px | Tam genişlik input |
| Border-radius | 8px | `--radius-md` |
| Padding | 0 16px | Yatay padding |
| Border | 1px solid | Subtle border |
| Font boyutu | 16px | iOS zoom engeli için |
| Placeholder opacity | 0.5 | Pasif text |
| Icon (varsa) | 20×20px | Sol tarafta icon |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Arka plan | Glassmorphism |
| `--border-subtle` | Normal border | `1px solid rgba(255,255,255,0.1)` |
| `--theme-primary` | Focus border | `border-color: var(--theme-primary)` |
| `--color-text` | Input text | Metin rengi |
| `--color-text-muted` | Placeholder | Placeholder rengi |
| `--color-danger` | Hata border | Hata durumunda |
| `--radius-md` | Border radius | `border-radius: 8px` |
| `--font-size-base` | Font boyutu | `16px` |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ✅ 56px |
| **Touch target** | 44×44px | ✅ 56px height |
| **Label** | `<label>` zorunlu | `for` attribute ile |
| **Error message** | `aria-describedby` | Hata mesajı bağlama |
| **Required** | `aria-required` | Zorunlu alan |
| **Autocomplete** | `autocomplete` attribute | Şifre/email için |
| **Font size** | ≥16px | iOS zoom engeli |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C06 — Form Input
   ITCSS: 04_Components
   BEM: .form-input
   ============================================ */

.form-input {
  position: relative;
  width: 100%;
}

/* Label */
.form-input__label {
  display: block;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-muted);
  margin-bottom: 6px;
}

.form-input__label--required::after {
  content: " *";
  color: var(--color-danger);
}

/* Input field */
.form-input__field {
  width: 100%;
  height: 56px;
  padding: 0 16px;
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);

  /* Typography */
  font-family: var(--font-body);
  font-size: 16px;
  font-weight: 400;
  color: var(--color-text);

  /* Transition */
  transition: border-color var(--transition-fast),
              box-shadow var(--transition-fast);

  /* Reset */
  outline: none;
  -webkit-appearance: none;
  appearance: none;
}

/* Placeholder */
.form-input__field::placeholder {
  color: var(--color-text-muted);
  opacity: 0.5;
}

/* Focus */
.form-input__field:focus {
  border-color: var(--theme-primary);
  box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.15);
}

/* Error state */
.form-input--error .form-input__field {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.form-input__error {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: var(--color-danger);
  line-height: 1.4;
}

/* Success state */
.form-input--success .form-input__field {
  border-color: var(--color-success);
}

/* With icon */
.form-input--icon {
  position: relative;
}

.form-input__icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
  height: 20px;
  color: var(--color-text-muted);
  pointer-events: none;
}

.form-input--icon .form-input__field {
  padding-left: 48px;
}

/* Password toggle */
.form-input__toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: none;
  cursor: pointer;
  color: var(--color-text-muted);
  border-radius: 6px;
}

.form-input__toggle:hover {
  color: var(--color-text);
  background: rgba(255, 255, 255, 0.08);
}

/* Disabled */
.form-input__field:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .form-input__field {
    font-size: 16px; /* iOS zoom prevention */
  }
}
```

## 6. Notlar

- Font boyutu `16px` olmalıdır, aksi halde iOS'ta otomatik zoom tetiklenir
- Label, `for` attribute'u ile input'a bağlanmalıdır
- Hata mesajı, `aria-describedby` ile input'a referans vermeli
- `autocomplete` attribute'u, şifre ve email alanları için zorunludur
- Autofill arka plan rengi CSS ileOverride edilmeli
