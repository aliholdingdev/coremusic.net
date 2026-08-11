---
title: CoreMusic — C12 Star Rating Component Spec
date: 2026-08-11
version: 2.0.0
platform: home-1024
---

# C12 — Star Rating

## BEM

```css
.star-rating { }
.star-rating__star { }
.star-rating__star--filled { }
.star-rating__star--empty { }
```

## ASCII Art

```
★★★★★  5 dolu (altın)
★★★★☆  4 dolu, 1 boş
★★★☆☆  3 dolu, 2 boş

Dolu: #FFD700 (altın)
Boş: rgba(255,255,255,0.3)
Boyut: 20×20px per star ❌ → 48px olmalı
```

## ITCSS: 04_Components
## WCAG: ❌ İHLAL — hit area 48px olmalı
## Kullanım: Album Detail, Playlist
## Öneri: Tam satırı tıklanabilir yap
