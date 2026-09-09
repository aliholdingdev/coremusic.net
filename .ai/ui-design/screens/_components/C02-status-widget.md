---
title: "CoreMusic - C02 Status Widget Component Spec"
type: component-spec
category: component-spec
date: 2026-08-11
updated: 2026-09-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
platform: home-1024
---

# C02 — System Status Widget

## BEM

```css
.header-border { }
.header-widget { }
.header-widget--signal { }
.header-widget--bt { }
.header-widget--battery { }
```

## ASCII Art

```
┌── WiFi+BT Group ──┐  ┌── Battery ──────────┐
│ [📶 WiFi] [✳ BT]  │  │ [🔋] %100           │
│ 65×37.4px         │  │ 100px wide           │
│ r:50px            │  │ r:50px               │
└───────────────────┘  └──────────────────────┘
```

## Ölçüler

| Token | Değer |
|-------|-------|
| WiFi+BT pill | 65×37.4px, `r:50px` |
| Battery pill | 100px wide, `r:50px` |
| Border | 1px solid `rgba(255,255,255,0.2)` |
| İkon | 25×25px |
| Background | `rgba(255,255,255,0.1)` |

## ITCSS: 03_Layout
## WCAG: ✅ UYGUN
## Kullanım: Tüm app ekranları (header)

---

*Component Spec C02 Status Widget v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
