---
title: CoreMusic — Singer Page (1024×600)
date: 2026-09-05
updated: 2026-09-05
type: png-analysis
status: active
version: 1.0.0
source: Linux  1024 - Singer Page.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Singer Page PNG Analysis

**Source:** Linux  1024 - Singer Page.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/C-music/artists.md`

## Layout Analysis

### Content (60/40 split — Albums ile aynı pattern)
- **Sol 60%:**
  - Genre tabs: yatay scroll (aynı C11)
  - Card grid: 3 sütun, DAİRESEL thumb 140×140 (border-radius 50%)
  - Kart altı: Artist Name (12px, 600), Genre (10px, muted), "45 Şarkı" (10px, accent)
- **Sağ 40%: Detail Panel (C10)**
  - Artist photo 300×300 DAİRESEL
  - Başlık 16px + bio + istatistikler
  - "Hemen Çal" + "Karışık Çal" + [...] butonları

## Verified Against Spec
- ✅ Artists kartları dairesel (albums kare, artists daire)
- ✅ Aynı 60/40 grid yapısı
- ✅ Detail foto 300×300 dairesel

## Edits Made
- v1.0.0 — İlk analiz (2026-09-05)