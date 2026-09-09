---
title: "CoreMusic - C05 Secondary Button Component Spec"
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

# C05 — Secondary Button

## BEM

```css
.btn-secondary { }
```

## ASCII Art

```
┌─────────────────────────┐
│      Karışık Çal         │
│      (48px yükseklik)    │
│      saydam bg           │
│      pembe border        │
│      pembe text          │
│      r: 8px              │
└─────────────────────────┘
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Yükseklik | `--btn-h-sm` (48px) |
| Padding | `--space-2` `--space-5` |
| Background | transparent |
| Border | 1px solid `var(--theme-primary)` |
| Text | `var(--theme-primary)` |
| Font | `--text-sm` (13px) |
| Border-radius | `--radius-md` (8px) |

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (48px)
## Kullanım: Auth, Detail Panel

---

*Component Spec C05 Secondary Button v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
