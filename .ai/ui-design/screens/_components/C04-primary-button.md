---
title: CoreMusic — C04 Primary Button Component Spec
date: 2026-08-11
version: 2.0.0
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
