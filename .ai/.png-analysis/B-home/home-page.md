---
title: CoreMusic — Home Page (1024×600)
date: 2026-09-05
updated: 2026-09-05
type: png-analysis
status: active
version: 1.0.0
source: Linux  1024 - Home Page.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Home Page PNG Analysis

**Source:** Linux  1024 - Home Page.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/B-home/dashboard.md`

## Layout Analysis

### Header (y:0-60)
- "Core Music" logo (Bickham Script + Respective)
- 8 nav link (Arima ~10px): Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda
- WiFi+BT pill (65×37.4px, radius 50px)
- Battery pill (100×37.4px, radius 50px)
- User pill (avatar 35×35 + isim + ok)

### Content (y:60-510, h:450px)
- **Top split 42/58:**
  - Sol 42%: Now Playing card — album art 100×100 + title/album/artist + seek bar (pembe h:3px)
  - Sağ 58%: 4 glass widget panel (Hoparlör, Hava, Saat/Tarih, Kütüphane) + quick apps
- **Bottom 3 sütun (1fr 1.2fr 0.6fr):**
  - Sütun 1: En Son Dinlenen (2×2 mini kart grid)
  - Sütun 2: Playlistler (2×2 mini kart grid + "Playlist listesini görüntüle" butonu)
  - Sütun 3: Sıradaki Şarkılar (tek cam kart 86px)

### Footer (y:510-600, h:90px)
- Seek slider (üstte, full-width)
- Album art 120×120 + 4 satır meta (Şarkı, Albüm, Sanatçı, Süre/Bitrate)
- 5 dairesel kontrol butonu (geri, oynat/pause, durdur, ileri)
- Utility ikonları + volume slider

## Verified Against Spec
- ✅ Header y:0-60, content y:60-510, footer y:510-600
- ✅ Split 42/58 (PNG kesin ölçüm)
- ✅ Content padding: üst 11px, alt 15px
- ✅ Touch target: 48px (WCAG 2.2)
- ✅ Glass: blur(20px) saturate(180%)
- ✅ Hover yok (dokunmatik)

## Edits Made
- v1.0.0 — İlk analiz (2026-09-05)