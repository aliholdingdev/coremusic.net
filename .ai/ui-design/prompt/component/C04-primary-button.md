---
title: "CoreMusic — C04 Primary Button Prompt"
type: prompt
category: ui-design
component_id: "C04"
bem_class: ".btn-primary"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C04 — Primary Button (.btn-primary)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.btn-primary` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_btn-primary.css` |
| **Usage** | Form submit, ana aksiyonlar, CTA |
| **Type** | Primary action button with gradient |

## 2. ASCII Wireframe

```
┌─── PRIMARY BUTTON (default) ─────────────────┐
│          [ Gradient Background ]              │
│              GİRİŞ YAP                        │
│  h:56px, full-width, border-radius:8px        │
│  font: 16px, weight: 600, color: white        │
└───────────────────────────────────────────────┘

┌─── PRIMARY BUTTON (loading) ─────────────────┐
│              [ ◌ spinner ]                    │
│  text: transparent, pointer-events: none      │
└───────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Height | Font | Padding |
|------|--------|------|---------|
| Phone | 52px | 16px | 0 20px |
| Embedded | 56px | 16px | 0 24px |
| Desktop | 56px | 16px | 0 24px |
| 4K TV | 64px | 18px | 0 32px |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| Default | `.btn-primary` | Gradient arka plan |
| Small | `.btn-primary--sm` | h:40px, font:14px |
| Icon Only | `.btn-primary--icon` | 56×56px kare |
| Full Width | `.btn-primary` (in form) | width:100% |

## 5. States

| State | Visual |
|-------|--------|
| Default | Gradient bg, white text |
| Hover | %10 darker gradient, subtle shadow |
| Active | scale(0.97), 100ms transition |
| Focus-visible | 3px outline + 6px shadow ring |
| Disabled | opacity: 0.5, cursor: not-allowed |
| Loading | Spinner, text transparent |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Touch target | min 44×44px (56px height) |
| Focus visible | 3px outline + shadow ring |
| ARIA | `aria-busy="true"` when loading, `aria-disabled="true"` |
| Keyboard | Enter/Space to activate |
| High contrast | Border visible in forced-colors mode |

## 7. Code Example

```css
/* C04 — Primary Button | ITCSS: 04_Components */
.btn-primary {
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
  font-family: var(--font-body);
  font-size: var(--font-size-base);
  font-weight: 600;
  color: var(--color-white);
  background: var(--theme-primary);
  border-radius: var(--radius-md);
  transition: background-color var(--transition-fast),
              box-shadow var(--transition-fast),
              transform var(--transition-fast);
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

.btn-primary:focus-visible {
  outline: 3px solid var(--theme-primary);
  outline-offset: 2px;
  box-shadow: 0 0 0 6px rgba(var(--theme-primary-rgb), 0.2);
}

.btn-primary:active { transform: scale(0.97); }

.btn-primary:disabled,
.btn-primary.is-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

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

@keyframes btn-spin { to { transform: rotate(360deg); } }

.btn-primary--sm { height: 40px; padding: 0 16px; font-size: 14px; border-radius: 6px; }
.btn-primary--icon { width: 56px; min-width: 0; padding: 0; }
```

```html
<button type="submit" class="btn-primary">GİRİŞ YAP</button>
<button type="button" class="btn-primary is-loading" aria-busy="true">Yükleniyor...</button>
<button type="button" class="btn-primary btn-primary--sm">Kaydet</button>
```

---

*C04 Primary Button v2.0.0 — CoreMusic UI Design System*
