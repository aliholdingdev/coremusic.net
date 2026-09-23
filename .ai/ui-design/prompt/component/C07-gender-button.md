---
title: "CoreMusic — C07 Gender Button Prompt"
type: prompt
category: ui-design
component_id: "C07"
bem_class: ".gender-btn"
itcss_layer: "05_Pages"
date: 2026-09-20
version: 2.0.0
status: active
---

# C07 — Gender Button (.gender-btn)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.gender-btn` |
| **ITCSS Layer** | `05_Pages` |
| **Target File** | `css/05_Pages/_gender-btn.css` |
| **Usage** | Auth sayfası gender seçimi (female/male/neutral) |
| **Type** | Gender selection button (card-style) |

## 2. ASCII Wireframe

```
┌─── GENDER BUTTON (default) ──────────────────┐
│  👩  Kadın                                    │
│      pembe tema                               │
│  h:60px, border: 2px solid subtle, glass bg   │
└───────────────────────────────────────────────┘

┌─── GENDER BUTTON (selected) ─────────────────┐
│  ┌─ border: 2px solid theme-primary ───────┐ │
│  │  👨  Erkek                               │ │
│  │      mavi tema                           │ │
│  │  bg: primary×0.1, shadow: primary×0.15  │ │
│  └──────────────────────────────────────────┘ │
└───────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Height | Font | Icon |
|------|--------|------|------|
| Phone | 56px | 14px | 20px |
| Embedded | 60px | 16px | 24px |
| Desktop | 60px | 16px | 24px |
| 4K TV | 72px | 18px | 28px |

## 4. States

| State | Visual |
|-------|--------|
| Default | Glass bg, border-subtle |
| Hover | border: rgba(255,255,255,0.2), bg: white×0.08 |
| Selected | border: theme-primary, bg: primary×0.1, shadow ring |
| Focus-visible | border: primary, shadow ring |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="radiogroup"` on container |
| Radio | `role="radio"` on each button |
| Selected | `aria-checked="true"` |
| Keyboard | Arrow keys to move, Space/Enter to select |
| Touch target | min 44×44px (60px height) |

## 6. Code Example

```css
/* C07 — Gender Button | ITCSS: 05_Pages */
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
  cursor: pointer;
  font-family: var(--font-body);
  font-size: var(--font-size-base);
  font-weight: 500;
  color: var(--color-text);
  transition: border-color var(--transition-fast), background-color var(--transition-fast);
}

.gender-btn:hover { border-color: rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.08); }

.gender-btn.is-selected,
.gender-btn[aria-checked="true"] {
  border-color: var(--theme-primary);
  background: rgba(var(--theme-primary-rgb), 0.1);
  box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.15);
}

.gender-btn:focus-visible { outline: none; border-color: var(--theme-primary); box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.2); }

.gender-btn__icon { width: 24px; height: 24px; flex-shrink: 0; color: var(--color-text-muted); transition: color var(--transition-fast); }
.gender-btn.is-selected .gender-btn__icon { color: var(--theme-primary); }

.gender-btn__text { display: flex; flex-direction: column; gap: 2px; }
.gender-btn__title { font-weight: 600; line-height: 1.2; }
.gender-btn__desc { font-size: 12px; color: var(--color-text-muted); font-weight: 400; }

.gender-btn input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
```

```html
<div class="gender-group" role="radiogroup" aria-label="Cinsiyet seçimi">
  <button class="gender-btn" role="radio" aria-checked="false" data-gender="female">
    <span class="gender-btn__icon">👩</span>
    <span class="gender-btn__text">
      <span class="gender-btn__title">Kadın</span>
      <span class="gender-btn__desc">pembe tema</span>
    </span>
  </button>
  <button class="gender-btn" role="radio" aria-checked="true" data-gender="male">
    <span class="gender-btn__icon">👨</span>
    <span class="gender-btn__text">
      <span class="gender-btn__title">Erkek</span>
      <span class="gender-btn__desc">mavi tema</span>
    </span>
  </button>
  <button class="gender-btn" role="radio" aria-checked="false" data-gender="neutral">
    <span class="gender-btn__icon">🌐</span>
    <span class="gender-btn__text">
      <span class="gender-btn__title">Nötr</span>
      <span class="gender-btn__desc">varsayılan tema</span>
    </span>
  </button>
</div>
```

---

*C07 Gender Button v2.0.0 — CoreMusic UI Design System*
