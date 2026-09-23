---
title: "CoreMusic — 02 Tablet Grid Layout Prompt"
type: prompt
category: ui-design
layout_id: "02"
date: 2026-09-20
version: 2.0.0
status: active
---

# 02 — Tablet Grid Layout

## 1. Layout Definition

| Property | Value |
|----------|-------|
| **ID** | `02-tablet-grid` |
| **Target Tiers** | Tablet (768-1024px) |
| **Usage** | Ana sayfa, albüm listesi |
| **Type** | 2-column grid, top navigation |

## 2. ASCII Wireframe

```
┌─── TABLET (820×1180) ────────────────────────────────┐
│  HEADER: h=60px (logo + nav + user)                  │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌── COL 1 (50%) ──┐  ┌── COL 2 (50%) ──┐         │
│  │                   │  │                   │         │
│  │  Card Grid        │  │  Widget Grid      │         │
│  │  2×3 layout       │  │  2×2 layout       │         │
│  │                   │  │                   │         │
│  │  gap: 12px        │  │  gap: 12px        │         │
│  │  padding: 16px    │  │  padding: 16px    │         │
│  │                   │  │                   │         │
│  └───────────────────┘  └───────────────────┘         │
│                                                      │
├──────────────────────────────────────────────────────┤
│  FOOTER PLAYER: h=90px                               │
└──────────────────────────────────────────────────────┘
```

## 3. Grid Structure

```css
/* Tablet Grid Layout */
.layout--tablet {
  display: grid;
  grid-template-rows: 60px 1fr 90px;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
}

.layout--tablet__header { grid-column: 1 / -1; }
.layout--tablet__content { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 16px; overflow-y: auto; }
.layout--tablet__footer { grid-column: 1 / -1; }
```

## 4. Regions

| Region | Grid Area | Height |
|--------|-----------|--------|
| Header | 1 / 1 / 2 / -1 | 60px |
| Content Left | 2 / 1 | flex:1 |
| Content Right | 2 / 2 | flex:1 |
| Footer | 3 / 1 / 4 / -1 | 90px |

## 5. Responsive Behavior

| Breakpoint | Change |
|------------|--------|
| 768px | 2 columns, compact |
| 900px | 2 columns, wider |
| 1024px | Transition to embedded layout |

## 6. Code Example

```css
@media (min-width: 768px) and (max-width: 1024px) {
  .layout--tablet {
    display: grid;
    grid-template-rows: 60px 1fr 90px;
    grid-template-columns: 1fr 1fr;
    min-height: 100vh;
  }

  .layout--tablet__header { grid-column: 1 / -1; }
  .layout--tablet__content {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    padding: 16px;
    overflow-y: auto;
  }
  .layout--tablet__footer { grid-column: 1 / -1; }
}
```

---

*02 Tablet Grid Layout v2.0.0 — CoreMusic UI Design System*
