---
title: CoreMusic — Albumler Page (1024×600)
date: 2026-09-05
updated: 2026-09-05
type: png-analysis
status: active
version: 1.0.0
source: Linux  1024 - Albumler Page.png
dimensions: 1024x600
platform: Linux Embedded / Raspberry Pi 5
---

# CoreMusic — Albumler Page PNG Analysis

**Source:** Linux  1024 - Albumler Page.png
**Dimensions:** 1024×600px (RPi5 embedded)
**Status:** Verified against `screens/C-music/albums.md`

## Layout Analysis

### Header (y:0-60)
- Standart header (logo + nav + pills + user)

### Content (60/40 split — Standard Pattern)
- **Sol 60% (614px):**
  - Geri ok: sol üst 44×44px
  - Genre tabs: yatay scroll, ~13 sekme, h:48px (WCAG) — "Tümü Pop Arabesk Dans Oyun Havası Damar Org Yabancı Pop Kpop/Kore..."
  - Card grid: 3 sütun, 140×140px round-square thumb, gap 8px
  - Kart altı: Title (12px, 600), Artist (10px, muted), Süre (10px, accent)
- **Sağ 40% (390px):**
  - Detail panel: album art 300×300 (DAİRE), title (16px), subtitle (12px)
  - "Hemen Çal" (C04, pembe, 56px) + "Karışık Çal" (C05, border, 48px)
  - Metadata: Kalite, Boyut, Parça, Tür, Yıl, Dinlenme, Süre

## Verified Against Spec
- ✅ 60/40 split (sol 614px + sağ 390px = 1004px + gap)
- ✅ Cards 140×140, grid 3 sütun
- ✅ Detail art 300×300 dairesel
- ✅ Genre tab yüksekliği 48px (WCAG C11)
- ✅ Hemen Çal 56px, Karışık Çal 48px

## Edits Made
- v1.0.0 — İlk analiz (2026-09-05)