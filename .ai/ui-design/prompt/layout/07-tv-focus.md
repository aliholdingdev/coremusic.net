---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — TV Focus Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T25
viewport: 1920x1080
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# TV Focus Layout Prompt (1920×1080)

## AI Code Generation Prompt

### Context
CoreMusic Smart TV için focus-based layout şablonu. 1920×1080 viewport, D-pad navigation, large touch targets.

### Required Inputs
- `focusIndicatorSize`: Focus göstergesi boyutu (varsayılan: 4px)
- `touchTargetSize`: Min touch target (varsayılan: 80px)
- `fontScale`: Font ölçekleme (varsayılan: 1.5)
- `safeArea`: TV safe area (varsayılan: 40px)

### ASCII Layout Reference
```
┌────────────────────────────────────────────────────────────────────────────────────────────────┐
│ HEADER (h:80, font: 1.5x)                                                                      │
│ Logo    Nav Links(8)                          User Avatar + Theme                              │
├────────────────────────────────────────────────────────────────────────────────────────────────┤
│ CONTENT (h:900, safe area: 40px)                                                               │
│                                                                                                │
│ ┌─── Focus Container ───────────────────────────────────────────────────────────────────────┐  │
│ │                                                                                          │  │
│ │  Focus Indicator: 4px solid #ff4fd8 + box-shadow: 0 0 20px rgba(255,79,216,0.5)          │  │
│ │                                                                                          │  │
│ │  ┌─── Focusable Item (min 80×80px) ────────────────────────────────────────────────────┐  │  │
│ │  │                                                                                      │  │  │
│ │  │  🎵 Album Art (200×200)                                                              │  │  │
│ │  │  Göksel - Sevil Neşelenen                                                            │  │  │
│ │  │  Hayat Rüya Gibi                                                                     │  │  │
│ │  │                                                                                      │  │  │
│ │  └──────────────────────────────────────────────────────────────────────────────────────┘  │  │
│ │                                                                                          │  │
│ └──────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                │
├────────────────────────────────────────────────────────────────────────────────────────────────┤
│ FOOTER PLAYER (h:100, font: 1.5x)                                                             │
│ 🎵 Info    [⏮][▶][⏹][⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ %100 │
└────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### D-pad Navigation
```
Navigation Sırası:
┌─────────────────────────────────────┐
│ Header → Content (sol üst) →       │
│ Content (sağ üst) →                │
│ Content (sol orta) →               │
│ Content (sağ orta) →               │
│ Content (sol alt) →                │
│ Content (sağ alt) →                │
│ Footer Player                      │
└─────────────────────────────────────┘

Focus Ring: 4px solid #ff4fd8
Focus Shadow: 0 0 20px rgba(255,79,216,0.5)
Focus Offset: 8px
```

### Prompt Template
```json
{
  "task": "Create TV focus layout for CoreMusic",
  "viewport": "1920x1080",
  "layout": "focus",
  "navigation": "dpad",
  "theme": "glassmorphism",
  "accentColor": "#ff4fd8",
  "components": ["header", "content-focus", "footer-player"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-focus-outline": "4px solid #ff4fd8",
    "--cm-focus-shadow": "0 0 20px rgba(255,79,216,0.5)",
    "--cm-touch-target": "80px",
    "--cm-font-scale": "1.5",
    "--cm-safe-area": "40px",
    "--cm-header-h": "80px",
    "--cm-footer-h": "100px"
  }
}
```

### Expected Output
HTML + CSS with:
- Header: h:80, large font
- Content: focus-based navigation
- Footer: h:100, large controls
- Focus indicators on all interactive elements

### Validation
- [ ] Focus indicators visible (4px outline)
- [ ] Touch targets ≥80px
- [ ] Font scale 1.5x
- [ ] Safe area respected
- [ ] D-pad navigation works
