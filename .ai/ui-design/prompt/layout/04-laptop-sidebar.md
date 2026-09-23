---
title: "CoreMusic — 04 Laptop Sidebar Layout Prompt"
type: prompt
category: ui-design
layout_id: "04"
date: 2026-09-20
version: 2.0.0
status: active
---

# 04 — Laptop Sidebar Layout

## 1. Layout Definition

| Property | Value |
|----------|-------|
| **ID** | `04-laptop-sidebar` |
| **Target Tiers** | Laptop (1025-1440px) |
| **Usage** | Göz At sayfası, ayarlar |
| **Type** | Sidebar + content, fixed width sidebar |

## 2. ASCII Wireframe

```
┌─── LAPTOP (1366×768) ─────────────────────────────────────┐
│  HEADER: h=70px (logo + nav + user)                        │
├────────────┬───────────────────────────────────────────────┤
│            │                                               │
│  SIDEBAR   │  CONTENT AREA                                 │
│  w=240px   │  flex: 1                                      │
│            │                                               │
│  🏠 Ana Sf │  ┌──────────────────────────────────────┐    │
│  🎵 Keşfet │  │                                      │    │
│  💿 Albüml │  │  Page content                        │    │
│  👤 Sanatç │  │  scrollable                          │    │
│  📁 Göz At │  │                                      │    │
│  📜 Geçmiş │  │                                      │    │
│  ⚙ Ayarlar│  │                                      │    │
│  ℹ Hakkım │  └──────────────────────────────────────┘    │
│            │                                               │
├────────────┴───────────────────────────────────────────────┤
│  FOOTER: h=104px                                           │
└────────────────────────────────────────────────────────────┘
```

## 3. Grid Structure

```css
/* Laptop Sidebar Layout */
.layout--laptop {
  display: grid;
  grid-template-rows: 70px 1fr 104px;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
}

.layout--laptop__header { grid-column: 1 / -1; }
.layout--laptop__sidebar { grid-row: 2; overflow-y: auto; }
.layout--laptop__content { grid-row: 2; overflow-y: auto; padding: 20px; }
.layout--laptop__footer { grid-column: 1 / -1; }
```

## 4. Regions

| Region | Grid Position | Size |
|--------|---------------|------|
| Header | row 1, col 1/-1 | 70px |
| Sidebar | row 2, col 1 | 240px, full height |
| Content | row 2, col 2 | flex:1, scrollable |
| Footer | row 3, col 1/-1 | 104px |

## 5. Responsive Behavior

| Breakpoint | Change |
|------------|--------|
| 1025px | Sidebar appears, 240px |
| 1200px | Wider content area |
| 1440px | Transition to desktop 3-col |

## 6. Code Example

```css
@media (min-width: 1025px) and (max-width: 1440px) {
  .layout--laptop {
    display: grid;
    grid-template-rows: 70px 1fr 104px;
    grid-template-columns: 240px 1fr;
    min-height: 100vh;
  }

  .layout--laptop__header { grid-column: 1 / -1; }

  .layout--laptop__sidebar {
    grid-row: 2;
    padding: 16px 0;
    border-right: 1px solid var(--border-subtle);
    background: var(--glass-bg);
    overflow-y: auto;
  }

  .layout--laptop__sidebar-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    font-size: 14px;
    color: var(--color-text-muted);
    text-decoration: none;
    transition: background-color 150ms;
  }

  .layout--laptop__sidebar-item:hover { background: rgba(255, 255, 255, 0.05); }
  .layout--laptop__sidebar-item.is-active { color: var(--theme-primary); background: rgba(var(--theme-primary-rgb), 0.08); }

  .layout--laptop__content { grid-row: 2; padding: 20px 24px; overflow-y: auto; }
  .layout--laptop__footer { grid-column: 1 / -1; }
}
```

---

*04 Laptop Sidebar Layout v2.0.0 — CoreMusic UI Design System*
