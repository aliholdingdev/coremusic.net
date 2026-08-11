---
title: CoreMusic — C11 Genre Tabs Component Spec
date: 2026-08-11
version: 2.0.0
platform: home-1024
---

# C11 — Genre Filter Tabs

## BEM

```css
.genre-tabs { }
.genre-tabs__item { }
.genre-tabs__item--active { }
```

## ASCII Art

```
[Tümü] [Pop] [Arabesk] [Dans] [Oyun Havası] [Damar] [Org] [Yabancı Pop] [Kpop/Kore]
^^^^^^   ^^^^   ^^^^^^^^  ^^^^^   ^^^^^^^^^^^^   ^^^^^   ^^^   ^^^^^^^^^^^^   ^^^^^^^^^^^
active   default  default  default   default      default default  default      default
pembe    sınır    sınır    sınır     sınır        sınır   sınır    sınır        sınır
(yatay scroll)
```

## Ölçüler

| Token | Değer |
|-------|-------|
| Yükseklik | ~32px ❌ → 48px olmalı |
| Padding | `--space-1` `--space-4` |
| Font | `--text-xs` (11px), `--font-medium` (500) |
| Gap | `--space-1` (4px) |
| Active bg | `var(--theme-primary)` |
| Default bg | `rgba(255,255,255,0.08)` |
| Border-radius | `--radius-pill` (50px) |
| Scroll | `overflow-x: auto` |

## ITCSS: 04_Components
## WCAG: ❌ İHLAL — 48px olmalı
## Kullanım: Albums, Artists
