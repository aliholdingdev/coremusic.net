---
title: "CoreMusic — C15 Toggle Prompt"
type: prompt
category: ui-design
component_id: "C15"
bem_class: ".toggle"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C15 — Toggle (.toggle)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.toggle` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_toggle.css` |
| **Usage** | Settings, notification toggle, on/off switches |
| **Type** | Custom checkbox toggle switch |

## 2. ASCII Wireframe

```
┌─── TOGGLE (OFF) ─────────────────────┐
│  ┌──────────────────────────┐        │
│  │  ○                       │ 50×28  │
│  │  thumb 22×22  track bg   │        │
│  └──────────────────────────┘        │
└──────────────────────────────────────┘

┌─── TOGGLE (ON) ──────────────────────┐
│  ┌──────────────────────────┐        │
│  │              ●           │ 50×28  │
│  │   track: primary  thumb  │        │
│  └──────────────────────────┘        │
└──────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Track | Thumb | Hit Area |
|------|-------|-------|----------|
| Phone | 44×24px | 18×18px | 44×44px |
| Embedded | 50×28px | 22×22px | 44×44px |
| Desktop | 50×28px | 22×22px | 44×44px |
| 4K TV | 60×34px | 26×26px | 48×48px |

## 4. States

| State | Visual |
|-------|--------|
| Off | track: border-subtle, thumb: white, left |
| On | track: theme-primary, thumb: white, right (translateX(22px)) |
| Hover | Subtle brightness change |
| Focus-visible | 2px outline on track |
| Disabled | opacity: 0.5, pointer-events: none |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="switch"` |
| Label | `aria-label="WiFi aç/kapat"` |
| Checked | `aria-checked="true"/"false"` |
| Keyboard | Space/Enter to toggle |
| Focus | Visible outline on track |

## 6. Code Example

```css
/* C15 — Toggle | ITCSS: 04_Components */
.toggle {
  position: relative;
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  padding: 8px;
  -webkit-tap-highlight-color: transparent;
}

.toggle__input { position: absolute; opacity: 0; width: 0; height: 0; }

.toggle__track {
  position: relative;
  width: 50px; height: 28px;
  border-radius: 14px;
  background: var(--border-subtle);
  transition: background-color var(--transition-fast);
}

.toggle__thumb {
  position: absolute;
  top: 3px; left: 3px;
  width: 22px; height: 22px;
  border-radius: 50%;
  background: var(--color-white);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  transition: transform var(--transition-fast);
}

.toggle__input:checked + .toggle__track { background: var(--theme-primary); }
.toggle__input:checked + .toggle__track .toggle__thumb { transform: translateX(22px); }

.toggle__input:focus-visible + .toggle__track { outline: 2px solid var(--theme-primary); outline-offset: 2px; }
.toggle__input:disabled + .toggle__track { opacity: 0.5; cursor: not-allowed; }
.toggle:has(.toggle__input:disabled) { cursor: not-allowed; pointer-events: none; }

.toggle__label { margin-left: 12px; font-size: 14px; color: var(--color-text); }

.toggle--sm .toggle__track { width: 40px; height: 22px; border-radius: 11px; }
.toggle--sm .toggle__thumb { width: 18px; height: 18px; top: 2px; left: 2px; }
.toggle--sm .toggle__input:checked + .toggle__track .toggle__thumb { transform: translateX(18px); }
```

```html
<label class="toggle">
  <input type="checkbox" class="toggle__input" role="switch"
         aria-checked="true" aria-label="WiFi aç/kapat" checked>
  <span class="toggle__track">
    <span class="toggle__thumb"></span>
  </span>
  <span class="toggle__label">WiFi</span>
</label>
```

---

*C15 Toggle v2.0.0 — CoreMusic UI Design System*
