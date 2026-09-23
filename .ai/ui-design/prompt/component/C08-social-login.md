---
title: "CoreMusic — C08 Social Login Prompt"
type: prompt
category: ui-design
component_id: "C08"
bem_class: ".social-btn"
itcss_layer: "05_Pages"
date: 2026-09-20
version: 2.0.0
status: active
---

# C08 — Social Login Button (.social-btn)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.social-btn` |
| **ITCSS Layer** | `05_Pages` |
| **Target File** | `css/05_Pages/_social-btn.css` |
| **Usage** | Auth sayfası Google/Apple/GitHub login |
| **Type** | Social OAuth login button (icon-only) |

## 2. ASCII Wireframe

```
┌─── SOCIAL BUTTON GROUP ──────────────────────┐
│                                               │
│  ┌────────┐  ┌────────┐  ┌────────┐         │
│  │   G    │  │    A   │  │   ⌨    │         │
│  │ Google │  │ Apple  │  │ GitHub │         │
│  │ 52×52  │  │ 52×52  │  │ 52×52  │         │
│  │ radius │  │ radius │  │ radius │         │
│  │ 12px   │  │ 12px   │  │ 12px   │         │
│  └────────┘  └────────┘  └────────┘         │
│  gap: 16px between buttons                    │
└──────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Button Size | Icon | Gap |
|------|-------------|------|-----|
| Phone | 48×48px | 20px | 12px |
| Embedded | 52×52px | 24px | 16px |
| Desktop | 52×52px | 24px | 16px |
| 4K TV | 64×64px | 28px | 20px |

## 4. Provider Variants

| Provider | Class | Hover Color |
|----------|-------|-------------|
| Google | `.social-btn--google` | #ea4335 (red) |
| Apple | `.social-btn--apple` | #a2aaad (silver) |
| GitHub | `.social-btn--github` | #6e40c9 (purple) |

## 5. States

| State | Visual |
|-------|--------|
| Default | Glass bg, icon centered |
| Hover | bg: white×0.12, shadow-sm, translateY(-2px) |
| Active | translateY(0), no shadow |
| Focus-visible | 3px outline |
| Loading | Spinner overlay |
| Disabled | opacity: 0.5 |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Touch target | min 44×44px (52×52) |
| Label | `aria-label="Google ile giriş yap"` |
| Role | `button` |
| Keyboard | Enter/Space |

## 7. Code Example

```css
/* C08 — Social Login | ITCSS: 05_Pages */
.social-btn-group { display: flex; align-items: center; justify-content: center; gap: 16px; }

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
  cursor: pointer;
  transition: background-color var(--transition-fast), box-shadow var(--transition-fast), transform var(--transition-fast);
  -webkit-tap-highlight-color: transparent;
}

.social-btn__icon { width: 24px; height: 24px; color: var(--color-text); transition: transform var(--transition-fast); }

.social-btn:hover { background: rgba(255, 255, 255, 0.12); box-shadow: var(--shadow-sm); transform: translateY(-2px); }
.social-btn:active { transform: translateY(0); box-shadow: none; }
.social-btn:focus-visible { outline: 3px solid var(--theme-primary); outline-offset: 2px; }

.social-btn--google:hover { border-color: #ea4335; box-shadow: 0 0 0 3px rgba(234, 67, 53, 0.2); }
.social-btn--apple:hover { border-color: #a2aaad; box-shadow: 0 0 0 3px rgba(162, 170, 173, 0.2); }
.social-btn--github:hover { border-color: #6e40c9; box-shadow: 0 0 0 3px rgba(110, 64, 201, 0.2); }
```

```html
<div class="social-btn-group">
  <button class="social-btn social-btn--google" aria-label="Google ile giriş yap">
    <svg class="social-btn__icon">...</svg>
  </button>
  <button class="social-btn social-btn--apple" aria-label="Apple ile giriş yap">
    <svg class="social-btn__icon">...</svg>
  </button>
  <button class="social-btn social-btn--github" aria-label="GitHub ile giriş yap">
    <svg class="social-btn__icon">...</svg>
  </button>
</div>
```

---

*C08 Social Login v2.0.0 — CoreMusic UI Design System*
