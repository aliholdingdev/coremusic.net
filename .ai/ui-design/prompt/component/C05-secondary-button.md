---
title: "CoreMusic — C05 Secondary Button Prompt"
type: prompt
category: ui-design
component_id: "C05"
bem_class: ".btn-secondary"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C05 — Secondary Button (.btn-secondary)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.btn-secondary` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_btn-secondary.css` |
| **Usage** | İkincil aksiyonlar, cancel, back |
| **Type** | Secondary/outline action button |

## 2. ASCII Wireframe

```
┌─── SECONDARY BUTTON (default) ──────────────┐
│  ┌─ border: 1px solid theme-primary ──────┐ │
│  │        Karışık Çal                      │ │
│  └─────────────────────────────────────────┘ │
│  h:48px, border-radius:8px, transparent bg   │
│  font: 14px, weight: 500, color: primary     │
└─────────────────────────────────────────────┘

┌─── SECONDARY BUTTON (hover) ────────────────┐
│  ┌─────────────────────────────────────────┐ │
│  │  bg: theme-primary, color: white (inv)  │ │
│  └─────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Height | Font | Padding |
|------|--------|------|---------|
| Phone | 44px | 14px | 0 16px |
| Embedded | 48px | 14px | 0 20px |
| Desktop | 48px | 14px | 0 20px |
| 4K TV | 56px | 16px | 0 24px |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| Default | `.btn-secondary` | Outline, primary border |
| Ghost | `.btn-secondary--ghost` | No border, transparent |
| Danger | `.btn-secondary--danger` | Red border, red text |
| Small | `.btn-secondary--sm` | h:36px, font:12px |
| Icon Only | `.btn-secondary--icon` | 48×48px kare |

## 5. States

| State | Visual |
|-------|--------|
| Default | Transparent bg, primary border/text |
| Hover | Filled primary bg, white text (inverse) |
| Active | scale(0.98) |
| Focus-visible | 3px outline + offset |
| Disabled | opacity: 0.5, pointer-events: none |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Touch target | min 44×44px |
| Focus visible | Outline + shadow |
| Keyboard | Enter/Space |
| High contrast | Border visible |

## 7. Code Example

```css
/* C05 — Secondary Button | ITCSS: 04_Components */
.btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 48px;
  padding: 0 var(--space-5);
  border: 1px solid var(--theme-primary);
  background: transparent;
  cursor: pointer;
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--theme-primary);
  border-radius: var(--radius-md);
  transition: background-color var(--transition-fast),
              color var(--transition-fast),
              transform var(--transition-fast);
  -webkit-tap-highlight-color: transparent;
}

.btn-secondary:hover { background: var(--theme-primary); color: var(--color-white); }
.btn-secondary:active { transform: scale(0.98); }
.btn-secondary:focus-visible { outline: 3px solid var(--theme-primary); outline-offset: 2px; }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }

.btn-secondary--ghost { border-color: transparent; }
.btn-secondary--ghost:hover { background: rgba(255, 255, 255, 0.08); color: var(--color-text); }

.btn-secondary--danger { border-color: var(--color-danger); color: var(--color-danger); }
.btn-secondary--danger:hover { background: var(--color-danger); color: var(--color-white); }

.btn-secondary--sm { height: 36px; padding: 0 12px; font-size: 12px; border-radius: 6px; }
.btn-secondary--icon { width: 48px; min-width: 0; padding: 0; }
```

```html
<button type="button" class="btn-secondary">Karışık Çal</button>
<button type="button" class="btn-secondary btn-secondary--ghost">← Geri</button>
<button type="button" class="btn-secondary btn-secondary--danger">Sil</button>
```

---

*C05 Secondary Button v2.0.0 — CoreMusic UI Design System*
