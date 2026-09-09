---
title: "CoreMusic - C06 Form Input Component Spec"
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

# C06 — Form Input

## BEM

```css
.form-input { }
.form-input__label { }
.form-input__field { }
.form-input--error { }
```

## ASCII Art

```
┌─────────────────────────────────────┐
│  E-posta, Telefon veya Kullanıcı Adı│  label
│  ┌─────────────────────────────────┐│
│  │  E-postanızı yazınız            ││  input, 56px
│  └─────────────────────────────────┘│
└─────────────────────────────────────┘
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Input yüksekliği | `--input-h` (56px) |
| Padding | `--space-3` `--space-4` |
| Background | `rgba(255,255,255,0.1)` |
| Border | 1px solid `rgba(255,255,255,0.2)` |
| Border-radius | `--radius-md` (8px) |
| Text | `#ffffff` |
| Placeholder | `rgba(255,255,255,0.4)` |
| Focus border | `var(--theme-primary)` |
| Font | `--text-base` (14px) |

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (56px)
## Kullanım: Auth, WiFi Connect

---

*Component Spec C06 Form Input v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
