---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Car Simplified Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T29
viewport: 1280x720
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Car Simplified Layout Prompt (1280×720)

## AI Code Generation Prompt

### Context
CoreMusic Android Auto için simplified layout şablonu. 1280×720 viewport, safety-first, large buttons, minimal text.

### Required Inputs
- `touchTargetSize`: Min touch target (varsayılan: 80px)
- `fontScale`: Font ölçekleme (varsayılan: 1.125)
- `maxTextLength`: Maksimum metin uzunluğu (varsayılan: 20 karakter)
- `animationDisabled`: Animasyonlar devre dışı (varsayılan: true)

### ASCII Layout Reference
```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│ STATUS BAR (h:48)                                                                            │
│ 📶  🔋 100%  ⏰ 07:00  🌡️ 22°C  🚗 Drive Mode Active                                      │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ CONTENT (h:624, safe area)                                                                   │
│                                                                                              │
│ ┌─── NOW PLAYING (50%, w:640) ──────┐  ┌─── QUICK ACTIONS (50%, w:640) ────────────────────┐ │
│ │                                    │  │                                                    │ │
│ │  🎵 Album Art (240×240)            │  │  🎵 Shuffle All    🔀    (min 80×80px)            │ │
│ │  Göksel - Sevil Neşelenen          │  │                                                    │ │
│ │  Hayat Rüya Gibi                   │  │  📻 Radio          📡    (min 80×80px)            │ │
│ │                                    │  │                                                    │ │
│ │  ⏱ 00:05:00 ━━━━━━━━━━━━ 100%    │  │  🎤 Podcasts       🎙️    (min 80×80px)            │ │
│ │                                    │  │                                                    │ │
│ │  [⏮] [▶] [⏭]                     │  │  📁 My Music       📂    (min 80×80px)            │ │
│ │  (min 80×80px each)               │  │                                                    │ │
│ │                                    │  │  🔊 Volume: ████████░░ 80%                        │ │
│ └────────────────────────────────────┘  └────────────────────────────────────────────────────┘ │
│                                                                                              │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│ NAVIGATION BAR (h:48)                                                                        │
│ 🏠 Home    🎵 Music    📻 Radio    🎤 Podcast    ⚙️ Settings                                  │
│ (min 80×48px each)                                                                           │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Safety Rules
```
KURALLAR:
1. Minimal Text: Sürücü dikkatini dağıtacak uzun metin yok
2. Large Buttons: Min 80×80px touch target
3. Voice-First: Sesli komut öncelikli
4. Dark Mode: Gece sürüşü için koyu tema
5. High Contrast: Güneş ışığında okunabilirlik
6. No Animations: Sürüş sırasında dikkat dağıtıcı animasyon yok
7. Simple Navigation: Maksimum 5 nav item
8. Quick Actions: Sık kullanılan işlemler ana ekranda
```

### Prompt Template
```json
{
  "task": "Create car simplified layout for CoreMusic",
  "viewport": "1280x720",
  "layout": "simplified",
  "safety": "first",
  "theme": "dark",
  "accentColor": "#ff4fd8",
  "components": ["status-bar", "content-split", "nav-bar"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-bg-dark": "#0a0a0f",
    "--cm-touch-target": "80px",
    "--cm-font-scale": "1.125",
    "--cm-max-text": "20ch",
    "--cm-status-h": "48px",
    "--cm-nav-h": "48px"
  }
}
```

### Expected Output
HTML + CSS with:
- Status bar: h:48, system info
- Content: 50/50 split
- Nav bar: h:48, 5 items
- All buttons ≥80px
- Dark theme
- No animations

### Validation
- [ ] Touch targets ≥80px
- [ ] Font scale 1.125x
- [ ] No animations
- [ ] Dark theme applied
- [ ] Max text 20ch
- [ ] Voice commands available
