---
title: CoreMusic — C02 Status Widget Component Spec
date: 2026-08-11
version: 2.0.0
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
