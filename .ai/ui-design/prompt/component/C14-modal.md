---
title: "CoreMusic — C14 Modal Prompt"
type: prompt
category: ui-design
component_id: "C14"
bem_class: ".modal"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C14 — Modal (.modal)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.modal` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_modal.css` |
| **Usage** | WiFi/BT connection, Welcome, Settings, Confirm dialogs |
| **Type** | Overlay modal with glass background |

## 2. ASCII Wireframe

```
┌─── OVERLAY ──────────────────────────────────────────┐
│  rgba(0,0,0,0.6) + backdrop-filter: blur(8px)       │
│                                                       │
│      ┌─── MODAL ──────────────────────────────┐      │
│      │  Glass: blur(16px)                      │      │
│      │  ┌─ Header ──────────── [×] Close ─┐   │      │
│      │  │  WiFi Ayarları                   │   │      │
│      │  ├─ Body ──────────────────────────┤   │      │
│      │  │  Content here                    │   │      │
│      │  ├─ Footer ────────────────────────┤   │      │
│      │  │  [Cancel]  [Save]               │   │      │
│      │  └─────────────────────────────────┘   │      │
│      └────────────────────────────────────────┘      │
│  max-width: 380px (default), 600px (large)           │
│  border-radius: 20px                                 │
└───────────────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Max Width | Padding | Border Radius |
|------|-----------|---------|---------------|
| Phone | calc(100vw - 32px) | 20px | 16px |
| Embedded | 380px | 24px | 20px |
| Desktop | 380px | 24px | 20px |
| 4K TV | 480px | 32px | 24px |

## 4. Variants

| Variant | Class | Width |
|---------|-------|-------|
| Default | `.modal` | 380px |
| Large | `.modal--lg` | 600px |
| Small | `.modal--sm` | 320px |
| Fullscreen | `.modal--fullscreen` | 100vw |

## 5. States

| State | Visual |
|-------|--------|
| Closed | opacity: 0, visibility: hidden, translateY(20px) |
| Open | opacity: 1, visible, translateY(0), spring animation |
| Focus trap | Tab cycles within modal only |
| Scroll lock | body.modal-open, overflow: hidden |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="dialog"` |
| Label | `aria-labelledby` (title) |
| Description | `aria-describedby` (body) |
| Modal | `aria-modal="true"` |
| Close | `aria-label="Kapat"` |
| Focus trap | Tab within modal |
| Escape | Close modal |
| Return focus | Back to trigger element |

## 7. Code Example

```css
/* C14 — Modal | ITCSS: 04_Components */
.modal-overlay {
  position: fixed; inset: 0;
  display: flex; align-items: center; justify-content: center;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  z-index: 9999;
  opacity: 0; visibility: hidden;
  transition: opacity 200ms ease, visibility 200ms;
}

.modal-overlay.is-open { opacity: 1; visibility: visible; }

.modal {
  position: relative;
  width: 100%; max-width: 380px; max-height: 90vh;
  padding: var(--space-6);
  border-radius: var(--radius-xl);
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  border: var(--glass-border);
  box-shadow: var(--shadow-xl);
  overflow-y: auto;
  transform: translateY(20px) scale(0.95);
  transition: transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-overlay.is-open .modal { transform: translateY(0) scale(1); }
.modal--lg { max-width: 600px; }
.modal--sm { max-width: 320px; }

.modal__header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.modal__title { font-size: 18px; font-weight: 600; color: var(--color-text); }

.modal__close {
  display: flex; align-items: center; justify-content: center;
  width: 32px; height: 32px;
  border: none; border-radius: 8px;
  background: rgba(255, 255, 255, 0.08); color: var(--color-text-muted);
  cursor: pointer;
}
.modal__close:hover { background: rgba(255, 255, 255, 0.15); color: var(--color-text); }
.modal__close:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: 2px; }

.modal__body { font-size: 14px; color: var(--color-text-muted); line-height: 1.5; }

.modal__footer { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-subtle); }

.modal-open { overflow: hidden; }

@media (max-width: 768px) {
  .modal { max-width: calc(100vw - 32px); padding: 20px; border-radius: 16px; }
  .modal__footer { flex-direction: column; }
  .modal__footer .btn-primary,
  .modal__footer .btn-secondary { width: 100%; }
}
```

```html
<div class="modal-overlay" id="wifi-modal" aria-hidden="true">
  <div class="modal" role="dialog" aria-labelledby="wifi-title" aria-modal="true">
    <div class="modal__header">
      <h2 class="modal__title" id="wifi-title">WiFi Ayarları</h2>
      <button class="modal__close" aria-label="Kapat">✕</button>
    </div>
    <div class="modal__body">
      <!-- Content: C15 toggle + C16 network rows -->
    </div>
  </div>
</div>
```

---

*C14 Modal v2.0.0 — CoreMusic UI Design System*
