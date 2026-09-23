---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Desktop 3-Column Layout Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T17
viewport: 1920x1080
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Desktop 3-Column Layout Prompt (1920×1080)

## AI Code Generation Prompt

### Context
CoreMusic Desktop FHD için 3-sütun layout şablonu. 1920×1080 viewport, persistent sidebar, mouse+keyboard.

### Required Inputs
- `sidebarWidth`: Sidebar genişliği (varsayılan: 240px)
- `headerHeight`: Header yüksekliği (varsayılan: 60px)
- `footerHeight`: Footer yüksekliği (varsayılan: 90px)
- `columnCount`: Sütun sayısı (varsayılan: 3)

### ASCII Layout Reference
```
┌────────────────────────────────────────────────────────────────────────────────────────────────┐
│ HEADER (h:60)                                                                                  │
│ Logo    Nav Links(8)                          User Avatar + Theme + Search                    │
├──────────────┬─────────────────────────────────────────────────────────────────────────────────┤
│ SIDEBAR      │ CONTENT (3 columns)                                                            │
│ (w:240px)    │                                                                                │
│              │ ┌─── Col 1 (33%) ──┐ ┌─── Col 2 (34%) ──┐ ┌─── Col 3 (33%) ──┐              │
│ 🏠 Ana Sayfa │ │ Now Playing      │ │ Welcome Banner   │ │ Widgets          │              │
│ 🎵 Keşfet    │ │ Album Art        │ │ Stats            │ │ Status           │              │
│ 💿 Albümler  │ │ Song Info        │ │ Quick Actions    │ │ Weather          │              │
│ 🎤 Sanatçılar│ │ Controls         │ │                  │ │ Social           │              │
│ 📂 Göz At    │ └──────────────────┘ └──────────────────┘ └──────────────────┘              │
│ 📜 Geçmiş    │                                                                                │
│ ⚙️ Ayarlar   │ ┌─── Card List (horizontal scroll) ───────────────────────────────────────┐  │
│ ℹ️ Hakkımızda│ │ 🎵 Card 1 │ 🎵 Card 2 │ 🎵 Card 3 │ 🎵 Card 4 │ 🎵 Card 5 │ 🎵 Card 6 │  │
│              │ └──────────────────────────────────────────────────────────────────────────┘  │
├──────────────┴─────────────────────────────────────────────────────────────────────────────────┤
│ FOOTER PLAYER (h:90)                                                                           │
│ 🎵 Info    [⏮][▶][⏹][⏭]    🔀🔁    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ %100 │
└────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### Prompt Template
```json
{
  "task": "Create desktop 3-column layout for CoreMusic",
  "viewport": "1920x1080",
  "layout": "3-column",
  "sidebar": "persistent",
  "theme": "glassmorphism",
  "accentColor": "#ff4fd8",
  "components": ["header", "sidebar", "content-3col", "footer-player"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-bg-glass": "rgba(255,255,255,0.15)",
    "--cm-sidebar-w": "240px",
    "--cm-header-h": "60px",
    "--cm-footer-h": "90px",
    "--cm-radius-md": "12px"
  }
}
```

### Expected Output
HTML + CSS with:
- Header: h:60, full width
- Sidebar: w:240px, persistent, glass bg
- Content: 3 columns, grid
- Footer: h:90, full width

### Validation
- [ ] Sidebar w:240px
- [ ] Header h:60px
- [ ] Footer h:90px
- [ ] 3 columns render correctly
- [ ] Mouse hover states work
- [ ] Glass effect applied
