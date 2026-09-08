---
title: CoreMusic — Albumler Details Detay Page (1024×600)
date: 2026-09-05
updated: 2026-09-05
type: png-analysis
status: active
version: 1.0.0
source: Linux  1024 - Albumler Details Detay Page.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Albumler Details Detay Page PNG Analysis

**Source:** Linux  1024 - Albumler Details Detay Page.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/C-music/album-detail.md`

## Layout Analysis

### Content (60/40 split)
- **Sol 60%: Track List (C13)**
  - Satır: [♪ thumb 20×20] Title (12px, 500) + Duration (10px) + Stars (★★★★★)
  - Satır yüksekliği: 48px (WCAG C13)
  - Aktif satır: rgba(255,79,216,0.15) bg + 3px pembe sol border
  - Tablo başlığı: sabit, sıralanabilir
- **Sağ 40%: Detail Panel (C10)**
  - Album art 300×300 dairesel
  - Başlık 16px/600, alt başlık 12px/muted
  - "Hemen Çal" (C04) + "Karışık Çal" (C05)
  - Metadata satırları (Kalite 24 Bit/48 kHz, Boyut 2 GB, Parça 12, Tür, Yıl, Dinlenme, Süre)

### Star Rating (C12)
- 5 yıldız: dolu #FFD700, boş rgba(255,255,255,0.3)
- Container min-height 48px (WCAG düzeltildi)

## Verified Against Spec
- ✅ Track row 48px (WCAG C13)
- ✅ Star hit area 48px (WCAG C12)
- ✅ Detail art 300×300
- ✅ Aktif satır pembe vurgu

## Edits Made
- v1.0.0 — İlk analiz (2026-09-05)