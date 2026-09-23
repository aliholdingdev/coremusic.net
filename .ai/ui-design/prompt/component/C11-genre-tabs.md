---
title: "CoreMusic — C11 Genre Tabs Prompt"
type: prompt
category: ui-design
component_id: "C11"
bem_class: ".genre-tabs"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C11 — Genre Tabs (.genre-tabs)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.genre-tabs` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_genre-tabs.css` |
| **Usage** | Müzik türü filtreleme (Pop, Rock, Jazz, vb.) |
| **Type** | Horizontal scrollable tab bar |

## 2. ASCII Wireframe

```
┌─── GENRE TABS (scrollable) ─────────────────────────────┐
│  [Pop] [Rock] [Jazz] [Klasik] [Arabesk] [Hip-Hop] [→]  │
│   ●     ○     ○      ○        ○         ○               │
│  active                                                   │
│  h:32px content + 8px padding = 48px hit area            │
│  radius: 20px (pill shape)                               │
└──────────────────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Height | Font | Gap |
|------|--------|------|-----|
| Phone | 44px | 13px | 6px |
| Embedded | 48px | 13px | 8px |
| Desktop | 48px | 14px | 8px |
| 4K TV | 56px | 16px | 10px |

## 4. States

| State | Visual |
|-------|--------|
| Default | Glass bg, muted text |
| Hover | bg: white×0.1, text: normal |
| Active | bg: theme-primary, text: white |
| Focus-visible | 2px outline + offset |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="tablist"` on container |
| Tab | `role="tab"` on each |
| Selected | `aria-selected="true"` |
| Keyboard | Arrow keys between tabs |
| Scroll | Visible scroll indicators |

## 6. Code Example

```css
/* C11 — Genre Tabs | ITCSS: 04_Components */
.genre-tabs {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding: 8px 0;
}

.genre-tabs::-webkit-scrollbar { display: none; }

.genre-tab {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  height: 32px;
  padding: 0 16px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--glass-bg);
  cursor: pointer;
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text-muted);
  white-space: nowrap;
  min-height: 44px;
  transition: background-color var(--transition-fast), color var(--transition-fast);
}

.genre-tab:hover { background: rgba(255, 255, 255, 0.1); color: var(--color-text); }
.genre-tab[aria-selected="true"],
.genre-tab.is-active { background: var(--theme-primary); color: var(--color-white); }
.genre-tab:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: 2px; }

.genre-tab__count {
  margin-left: 6px;
  padding: 1px 6px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.15);
  font-size: 10px;
  font-weight: 600;
}

.genre-tab.is-active .genre-tab__count { background: rgba(255, 255, 255, 0.25); }
```

```html
<div class="genre-tabs" role="tablist" aria-label="Müzik türleri">
  <button class="genre-tab" role="tab" aria-selected="true">Pop <span class="genre-tab__count">124</span></button>
  <button class="genre-tab" role="tab" aria-selected="false">Rock <span class="genre-tab__count">89</span></button>
  <button class="genre-tab" role="tab" aria-selected="false">Jazz <span class="genre-tab__count">45</span></button>
</div>
```

---

*C11 Genre Tabs v2.0.0 — CoreMusic UI Design System*
