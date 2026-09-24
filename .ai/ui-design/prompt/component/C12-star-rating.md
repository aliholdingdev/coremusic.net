---
title: "CoreMusic — C12 Star Rating Prompt"
type: prompt
category: ui-design
component_id: "C12"
bem_class: ".star-rating"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C12 — Star Rating (.star-rating)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.star-rating` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_star-rating.css` |
| **Usage** | Şarkı/albüm puanlama (1-5 yıldız) |
| **Type** | Interactive star rating input |

## 2. ASCII Wireframe

```
┌─── STAR RATING ──────────────────────────────┐
│  ★ ★ ★ ★ ☆   4.5 (128)                     │
│  filled filled filled filled empty           │
│  20×20px icons, 4px gap, min 44px hit area   │
└──────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Star Size | Hit Area | Font |
|------|-----------|----------|------|
| Phone | 18×18px | 44×44px | 12px |
| Embedded | 20×20px | 44×44px | 14px |
| Desktop | 20×20px | 44×44px | 14px |
| 4K TV | 24×24px | 48×48px | 16px |

## 4. States

| State | Visual |
|-------|--------|
| Empty | stroke: border-subtle |
| Filled | fill: color-star (yellow/orange) |
| Hover preview | All stars up to cursor filled, scale(1.2) |
| Half star (optional) | 50% fill |
| Read-only | No interaction, cursor: default |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="radiogroup"` on container |
| Radio | `role="radio"` on each star |
| Label | `aria-label="4 yıldız ver"` |
| Keyboard | Arrow keys 1-5, Space/Enter to confirm |
| Screen reader | "4/5 yıldız" announced |
| Focus | 2px outline on focused star |

## 6. Code Example

```css
/* C12 — Star Rating | ITCSS: 04_Components */
.star-rating { display: inline-flex; align-items: center; gap: 4px; }

.star-rating__star {
  display: flex; align-items: center; justify-content: center;
  width: 28px; height: 28px; padding: 0;
  border: none; background: none; cursor: pointer;
  color: var(--border-subtle);
  transition: color var(--transition-fast), transform var(--transition-fast);
  min-width: 44px; min-height: 44px;
}

.star-rating__icon { width: 20px; height: 20px; transition: transform 200ms ease, fill 200ms ease; }

.star-rating__star.is-filled,
.star-rating__star[aria-checked="true"] { color: var(--color-star); }
.star-rating__star.is-filled .star-rating__icon { fill: var(--color-star); }
.star-rating__star:hover { transform: scale(1.2); }
.star-rating__star:hover .star-rating__icon { fill: var(--color-star); }
.star-rating__star:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: 2px; border-radius: 4px; }

.star-rating__text { margin-left: 8px; font-size: 14px; font-weight: 600; color: var(--color-text); }
.star-rating__count { margin-left: 4px; font-size: 12px; color: var(--color-text-muted); }

.star-rating--readonly .star-rating__star { cursor: default; pointer-events: none; }
```

```html
<div class="star-rating" role="radiogroup" aria-label="Albüm puanı">
  <button class="star-rating__star is-filled" role="radio" aria-checked="true" aria-label="1 yıldız">
    <svg class="star-rating__icon">★</svg>
  </button>
  <!-- ... 2-4 aynı filled stars ... -->
  <button class="star-rating__star" role="radio" aria-checked="false" aria-label="5 yıldız">
    <svg class="star-rating__icon">☆</svg>
  </button>
  <span class="star-rating__text">4.5</span>
  <span class="star-rating__count">(128)</span>
</div>
```

---

*C12 Star Rating v2.0.0 — CoreMusic UI Design System*
