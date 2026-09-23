---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Watch Micro Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T31
viewport: 396x484
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Watch Micro Layout Prompt (396×484)

## AI Code Generation Prompt

### Context
CoreMusic Apple Watch için micro layout şablonu. 396×484 viewport, OLED power save, crown rotation.

### Required Inputs
- `touchTargetSize`: Min touch target (varsayılan: 44px)
- `fontScale`: Font ölçekleme (varsayılan: 0.75)
- `oledBlack`: OLED siyah arka plan (varsayılan: true)
- `crownSupport`: Crown rotasyon desteği (varsayılan: true)

### ASCII Layout Reference
```
┌──────────────────────────────────────────────┐
│ STATUS (h:20)                                │
│ 🔋 100%  ⏰ 07:00                            │
├──────────────────────────────────────────────┤
│ CONTENT (h:444)                              │
│                                              │
│  ┌─── ALBUM ART (120×120, circle) ─────────┐│
│  │         🎵                              ││
│  │    ┌──────────┐                         ││
│  │    │          │                         ││
│  │    │  Album   │                         ││
│  │    │   Art    │                         ││
│  │    │          │                         ││
│  │    └──────────┘                         ││
│  └──────────────────────────────────────────┘│
│                                              │
│  Göksel - Sevil Neşelenen                    │
│  Hayat Rüya Gibi                             │
│  Göksel                                      │
│                                              │
│  ⏱ 00:05:00 ━━━━━━━━━━━━ 100%              │
│                                              │
│  [⏮] [▶] [⏭]                                │
│  (min 44×44px each)                          │
│                                              │
│  🔀  🔁  ❤️                                  │
│  (min 44×44px each)                          │
│                                              │
└──────────────────────────────────────────────┘
```

### Watch Gestures
```
GESTURES:
┌─────────────────────────────────────┐
│ Crown Rotation: Volume kontrolü     │
│ Haptic Feedback: Buton tıklamasında │
│ Always-On Display: Düşük güç modu   │
│ OLED Power Save: Siyah arka plan    │
│ Swipe Left: Sonraki şarkı           │
│ Swipe Right: Önceki şarkı           │
│ Swipe Up: Volume                    │
│ Swipe Down: Kapat                   │
│ Force Touch: Menü                   │
└─────────────────────────────────────┘
```

### Prompt Template
```json
{
  "task": "Create watch micro layout for CoreMusic",
  "viewport": "396x484",
  "layout": "micro",
  "theme": "oled-dark",
  "accentColor": "#ff4fd8",
  "components": ["status", "content", "controls"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-bg-oled": "#000000",
    "--cm-touch-target": "44px",
    "--cm-font-scale": "0.75",
    "--cm-art-size": "120px",
    "--cm-status-h": "20px"
  }
}
```

### Expected Output
HTML + CSS with:
- Status: h:20, minimal
- Content: centered, micro layout
- Album art: 120×120, circle
- Controls: 44×44px buttons
- OLED black background

### Validation
- [ ] Touch targets ≥44px
- [ ] Font scale 0.75x
- [ ] OLED black bg
- [ ] Crown rotation works
- [ ] Haptic feedback enabled
