---
title: CoreMusic — Home Page Welcome Popup (1024×600)
date: 2026-09-05
updated: 2026-09-05
type: png-analysis
status: active
version: 1.0.0
source: Linux  1024 - Home Page Welcome Popup.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Home Page Welcome Popup PNG Analysis

**Source:** Linux  1024 - Home Page Welcome Popup.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/B-home/welcome-popup.md`

## Layout Analysis

### Overlay (tam ekran)
- rgba(0,0,0,0.5) + backdrop blur(4px)
- Z-index: modal üstte

### Modal (600×308px, merkez x:512, y:299.5)
- Background: welcome-popup-girl.png (kapak, ortalanmış)
- Border: 1px solid rgba(255,255,255,0.25)
- Radius: 16px
- Shadow: 0 16px 48px rgba(0,0,0,0.55)

### İçerik (dikey merkezli)
- Logo img (max-h 48px) + "CoreMusic" yazısı
- Başlık: "Hoş geldin" (20px, 600)
- Kullanıcı: "Prenses Işıl Peri" (Bickham Script, 24px, italic)
- Açıklama: "Sana özel seçilen melodiler..." (13px, 1.5 line-height)
- "Başla" butonu: 105×48px (WCAG düzeltildi — eskiden 25px), gradient pink

### Kapat Butonu
- Sağ üst (16px sağ, 12px üst)
- 48×48px min touch (WCAG 2.5.8)

## Verified Against Spec
- ✅ Modal 600×308 tam ortalanmış
- ✅ Başla butonu 48px touch hedefi (WCAG — kodda düzeltildi)
- ✅ Kapat butonu 48×48px min
- ✅ Backdrop click ile kapatma
- ✅ Escape ile kapatma

## Edits Made
- v1.0.0 — İlk analiz (2026-09-05)