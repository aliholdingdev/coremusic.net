---
title: "C14 — Modal Component Prompt"
type: component-prompt
category: ui-design
component_id: "C14"
bem_class: ".modal"
itcss_layer: "04_Components"
target_file: "css/04_Components/_modal.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C14 — Modal (.modal)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.modal` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_modal.css` |
| **Kullanım Alanı** — WiFi/BT connection, Welcome, Settings, Confirm dialogs |
| **Bileşen Türü** — Overlay modal with glass background |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| WiFi/BT Modal | ~380×340px | orta boy modal |
| Welcome Modal | 600×308px | Large boyut |
| Border-radius | 20px | `--radius-xl` |
| Overlay opacity | 0.5–0.7 | Karartma |
| Backdrop blur | `blur(8px)` | Glass efekti |
| Padding | 24px | İç padding |
| Close button | 32×32px | Sağ üst köşe |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Modal arka plan | Glassmorphism |
| `--glass-border` | Modal kenarlık | Glass border |
| `--radius-xl` | Border radius | `border-radius: 20px` |
| `--color-text` | Başlık rengi | Modal başlığı |
| `--color-text-muted` | İçerik rengi | Description text |
| `--space-6` | İç padding | `24px` |
| `--shadow-xl` | Modal gölgesi | `box-shadow` |
| `--blur-md` | Backdrop blur | `blur(8px)` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Role** | `dialog` | Modal için zorunlu |
| **Label** | `aria-labelledby` | Başlık referansı |
| **Description** | `aria-describedby` | İçerik referansı |
| **Focus trap** | Tab within modal | Odak tuzağı |
| **Escape key** | Modal kapatma | Keyboard erişilebilirlik |
| **Close button** | `aria-label` | "Kapat" |
| **Return focus** | Opens on close | Kapatınca eski odağa dön |
| **Overlay click** | Optional close | Arka plan tıklama |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C14 — Modal
   ITCSS: 04_Components
   BEM: .modal
   ============================================ */

/* Overlay */
.modal-overlay {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  z-index: 9999;
  opacity: 0;
  visibility: hidden;
  transition: opacity 200ms ease, visibility 200ms;
}

.modal-overlay.is-open {
  opacity: 1;
  visibility: visible;
}

/* Modal container */
.modal {
  position: relative;
  width: 100%;
  max-width: 380px;
  max-height: 90vh;
  padding: var(--space-6);
  border-radius: var(--radius-xl);
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: var(--glass-border);
  box-shadow: var(--shadow-xl);
  overflow-y: auto;
  transform: translateY(20px) scale(0.95);
  transition: transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-overlay.is-open .modal {
  transform: translateY(0) scale(1);
}

/* Large variant */
.modal--lg {
  max-width: 600px;
}

/* Small variant */
.modal--sm {
  max-width: 320px;
}

/* Header */
.modal__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.modal__title {
  font-family: var(--font-body);
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text);
}

.modal__close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text-muted);
  cursor: pointer;
  transition: background-color 150ms, color 150ms;
}

.modal__close:hover {
  background: rgba(255, 255, 255, 0.15);
  color: var(--color-text);
}

.modal__close:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: 2px;
}

/* Body */
.modal__body {
  font-family: var(--font-body);
  font-size: 14px;
  color: var(--color-text-muted);
  line-height: 1.5;
}

/* Footer */
.modal__footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid var(--border-subtle);
}

/* Focus trap — body scroll lock */
.modal-open {
  overflow: hidden;
}

/* Animation */
@keyframes modal-in {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .modal {
    max-width: calc(100vw - 32px);
    padding: 20px;
    border-radius: 16px;
  }

  .modal--lg {
    max-width: calc(100vw - 32px);
  }

  .modal__footer {
    flex-direction: column;
  }

  .modal__footer .btn-primary,
  .modal__footer .btn-secondary {
    width: 100%;
  }
}
```

## 6. Notlar

- Focus trap: Modal açıkken Tab tuşu sadece modal içinde döner
- Escape tuşu ile modal kapatılır
- `aria-modal="true"` attribute'u zorunlu
- `body.modal-open` ile sayfa scroll'u engellenir
- Kapatınca `returnFocus` ile eski odağa geri dönülür
- WiFi/BT modal: `max-width: 380px`, Welcome modal: `max-width: 600px`
