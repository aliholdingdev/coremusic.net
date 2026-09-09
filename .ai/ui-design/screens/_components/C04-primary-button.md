---
title: "CoreMusic - C04 Primary Button Component Spec"
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

# C04 — Primary Button

## BEM

```css
.btn-primary { }
.btn-primary--disabled { }
```

## ASCII Art

```
┌─────────────────────────┐
│       Hemen Çal          │
│       (56px yükseklik)   │
│       pembe bg #ff4fd8   │
│       beyaz text #fff    │
│       r: 8px             │
└─────────────────────────┘
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Yükseklik | `--btn-h` (56px) |
| Padding | `--space-3` `--space-6` |
| Background | `var(--theme-primary)` |
| Text | `#ffffff` |
| Font | `--text-base` (14px), `--font-semibold` (600) |
| Border-radius | `--radius-md` (8px) |
| Transition | `--transition-base` (250ms) |

## Durumlar

| Durum | Değişiklik |
|-------|-----------|
| Default | `bg: var(--theme-primary)` |
| Focus-visible | `outline: 2px solid var(--theme-primary)` |
| Disabled | `opacity: 0.5` |

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (56px)
## Kullanım: Auth, Detail Panel

---

*Component Spec C04 Primary Button v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
