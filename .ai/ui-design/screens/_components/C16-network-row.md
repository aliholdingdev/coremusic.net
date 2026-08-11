---
title: CoreMusic — C16 Network Row Component Spec
date: 2026-08-11
version: 2.0.0
platform: home-1024
---

# C16 — Network / Device Row

## BEM

```css
.network-row { }
.network-row--connected { }
.network-row__icon { }
.network-row__name { }
.network-row__badges { }
.network-row__signal { }
.network-row__action { }
```

## ASCII Art

```
┌────────────────────────────────────────────────────────────────┐
│ [📶] Bayram Ali Home [Güçlü][5GHz] 5GHz·Mükemmel·100% [Bağlan]│
│  ↑       ↑              ↑        ↑              ↑        ↑     │
│  icon    name          badge    signal        strength  btn    │
│  24×24   ~12px         pill     ~10px         ~10px    48px   │
└────────────────────────────────────────────────────────────────┘
Yükseklik: ~48px ✅
```

## ITCSS: 04_Components
## WCAG: ✅ UYGUN (48px)
## Kullanım: WiFi, Bluetooth
