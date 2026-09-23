---
title: "CoreMusic — Screen Prompt Index"
type: prompt-index
category: ui-design
date: 2026-09-20
version: 2.0.0
status: active
---

# Screen Prompt Index

10 tier bazlı ekran promptu. Her dosya; tier tanımı, layout pattern, token overrides, code example içerir.

| # | Tier | Viewport | Cihaz | Input | Layout |
|---|------|----------|-------|-------|--------|
| 1 | T1-phone | max-width: 767px | Telefon | Touch | Stack (dikey scroll) |
| 2 | T2-tablet-small | 768-1023px | Tablet küçük | Touch+Stylus | 2-column grid |
| 3 | T3-tablet-large | 1024-1279px | Tablet büyük | Touch+Stylus | 2-column split |
| 4 | T4-embedded | 1024×600 | RPi5 7" LCD | Touch (5-nokta) | Split 42/58 |
| 5 | T5-laptop | 1280-1919px | Laptop | Mouse+Klavye | Sidebar+Content |
| 6 | T6-desktop | 1920px (FHD) | Masaüstü | Mouse+Klavye | 3-column |
| 7 | T7-desktop-4k | 2560-3839px | 4K Monitör | Mouse+Klavye | Expanded 3-column |
| 8 | T8-tv | 3840px+ | Smart TV | D-pad (uzaktan kumanda) | Focus mode |
| 9 | T9-car | Değişken | Araç içi | Large touch+Voice | Simplified grid |
| 10 | T10-watch | ≤400px | Akıllı saat | Crown/Digital crown | Micro UI |

## Kullanım

1. Tier'ı belirle
2. İlgili `T{N}-{tier}.md` dosyasını oku
3. Token değerlerini ve layout pattern'i uygula
4. Code example'leri referans al
