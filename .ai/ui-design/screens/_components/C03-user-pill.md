---
title: "CoreMusic - C03 User Pill Component Spec"
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

# C03 — User Profile Pill

## BEM

```css
.header-user { }
.header-user__avatar { }
.header-user__name { }
.header-user__arrow { }
```

## ASCII Art

```
┌─────────────────────────────────┐
│ [🧑 avatar 35×35] Bayram Ali  ▾│
└─────────────────────────────────┘
Toplam genişlik: ~150px
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Avatar | 35×35px, `r:50%` |
| Font | `--text-sm` (12px) |
| Renk | `rgba(255,255,255,0.9)` |
| Padding | `--space-2` `--space-3` |
| Background | `rgba(255,255,255,0.1)` |
| Border-radius | `--radius-pill` (50px) |

## ITCSS: 03_Layout
## WCAG: ✅ UYGUN (~52px)
## Kullanım: Tüm app ekranları (header)

---

*Component Spec C03 User Pill v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
