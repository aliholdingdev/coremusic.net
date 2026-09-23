---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Mobile Stack Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T01
viewport: 720x1280
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Mobile Stack Layout Prompt (720×1280)

## AI Code Generation Prompt

### Context
CoreMusic Phone HD için stack layout şablonu. 720×1280 viewport, tek sütun, bottom tab navigation.

### Required Inputs
- `tabCount`: Bottom tab sayısı (varsayılan: 3)
- `headerHeight`: Header yüksekliği (varsayılan: 56px)
- `statusBarHeight`: Status bar yüksekliği (varsayılan: 24px)
- `tabBarHeight`: Tab bar yüksekliği (varsayılan: 80px)

### ASCII Layout Reference
```
┌──────────────────────────────┐
│ STATUS BAR (h:24)            │
├──────────────────────────────┤
│ HEADER (h:56)                │
│ Logo       🔍  👤            │
├──────────────────────────────┤
│ CONTENT (h:1100)             │
│ ┌──────────────────────────┐ │
│ │ Now Playing (w:100%)     │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ Cards (w:100%)           │ │
│ └──────────────────────────┘ │
│ ┌──────────────────────────┐ │
│ │ More Content (w:100%)    │ │
│ └──────────────────────────┘ │
├──────────────────────────────┤
│ BOTTOM TAB NAV (h:80)        │
│ 🏠 Ana Sayfa │ 📚 Kütüphane │ ⚙️ │
└──────────────────────────────┘
```

### Prompt Template
```json
{
  "task": "Create mobile stack layout for CoreMusic",
  "viewport": "720x1280",
  "layout": "stack",
  "theme": "glassmorphism",
  "accentColor": "#ff4fd8",
  "components": ["status-bar", "header", "content-stack", "tab-nav"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-bg-glass": "rgba(255,255,255,0.15)",
    "--cm-radius-md": "12px",
    "--cm-status-h": "24px",
    "--cm-header-h": "56px",
    "--cm-tab-h": "80px",
    "--cm-content-h": "1100px"
  }
}
```

### Expected Output
HTML + CSS with:
- Status bar: h:24, fixed top
- Header: h:56, glass bg
- Content: scrollable, stack layout
- Tab nav: h:80, fixed bottom, 3 tabs

### Validation
- [ ] Status bar h:24px
- [ ] Header h:56px
- [ ] Tab nav h:80px
- [ ] Content scrollable
- [ ] Touch targets ≥48px
- [ ] Glass effect applied
