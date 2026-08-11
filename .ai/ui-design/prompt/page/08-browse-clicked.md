---
title: "Sayfa Prompt — Göz At (Tıklama)"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/browse"
layout: "3-column"
---

# Göz At — Tıklama (Browse Clicked)

## Route: `/browse` (tıklanmış durum)
## Layout Pattern: 3 Sütun

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| Category List | 1 | Sol (167px) | Kategori listesi |
| File List | 1 | Orta (573px) | Dosya listesi |
| Charts Area | 3 | Sağ (220px) | Grafikler |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Sütun (167px)

1. **Category List** — Kategori listesi
   - Seçili kategori vurgulu
   - Tıklanmış durumda renk değişimi

## Orta Sütun (573px)

2. **File List** — Tıklanan kategorinin dosyaları
   - Liste görünümü
   - Her satır: dosya adı, boyut, tarih
   - Seçili: arka plan değişimi

## Sağ Sütun (220px)

3. **Charts Area** — 3 grafik
   - **Donut Chart:** Tür dağılımı
   - **Bar Chart:** Sanatçı dağılımı
   - **Pie Chart:** Format dağılımı
   - Her grafik: max-height 100px

## Grafik Detayları

| Grafik | Tür | Veri |
|--------|-----|------|
| Donut | Tür dağılımı | Rock %30, Pop %25, Jazz %20, Diğer %25 |
| Bar | Sanatçı | En çok dinlenen 5 sanatçı |
| Pie | Format | FLAC %60, MP3 %30, Other %10 |

## ASCII Art Referansı

`00-mockup-index.md` §4.9 — Browse (Clicked) ASCII Art
