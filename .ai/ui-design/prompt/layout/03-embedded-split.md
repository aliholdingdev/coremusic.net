---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Embedded Split Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Embedded Split Layout Prompt (1024×600)

## AI Code Generation Prompt

### Context
CoreMusic RPi5 7" touch device için split layout şablonu. 1024×600 viewport, glassmorphism tema, pembe (#ff4fd8) accent.

### Required Inputs
- `splitRatio`: Sol/sağ oranı (varsayılan: 42/58)
- `headerHeight`: Header yüksekliği (varsayılan: 60px)
- `footerHeight`: Footer yüksekliği (varsayılan: 90px)
- `glassBlur`: Cam efekti blur miktarı (varsayılan: 20px)

### ASCII Layout Reference
```
┌────────────────────────────────────────────────────────────────┐
│ HEADER (h:60)                                                  │
│ Logo(120×40)  Nav Links(8)                  User + Theme + 🔍  │
├────────────────────────────────────────────────────────────────┤
│ CONTENT (h:450)                                                │
│ ┌─── LEFT (42%) ─────┐  ┌─── RIGHT (58%) ───────────────────┐│
│ │ Now Playing / Main  │  │ Widgets / Detail / Secondary      ││
│ │ Content Area        │  │ Content Area                      ││
│ └─────────────────────┘  └───────────────────────────────────┘│
├────────────────────────────────────────────────────────────────┤
│ FOOTER PLAYER (h:90)                                          │
│ 🎵 Info    [⏮][▶][⏹][⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━ %100 │
└────────────────────────────────────────────────────────────────┘
```

### Prompt Template
```json
{
  "task": "Create embedded split layout for CoreMusic",
  "viewport": "1024x600",
  "layout": "split",
  "splitRatio": "42/58",
  "theme": "glassmorphism",
  "accentColor": "#ff4fd8",
  "components": ["header", "content-split", "footer-player"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-bg-glass": "rgba(255,255,255,0.15)",
    "--cm-blur": "blur(20px)",
    "--cm-radius-md": "12px",
    "--cm-header-h": "60px",
    "--cm-footer-h": "90px",
    "--cm-content-h": "450px"
  }
}
```

### Expected Output
HTML + CSS with:
- Header: flex, space-between, glass bg
- Content: grid, 2 columns (42% 58%)
- Footer: fixed bottom, glass bg
- All tokens applied

### Validation
- [ ] Header h:60px
- [ ] Footer h:90px
- [ ] Content h:450px (600-60-90)
- [ ] Split ratio matches input
- [ ] Glass effect applied
- [ ] Touch targets ≥48px
