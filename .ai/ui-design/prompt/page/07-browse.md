---
title: "Sayfa Prompt — Göz At (Disk)"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/browse"
layout: "3-column-167-573-220"
---

# Göz At — Disk (Browse)

## Route: `/browse`
## Layout Pattern: 3 Sütun (167+573+220)

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| Disk List | 1 | Sol (167px) | Disk/kategori listesi |
| File Browser | 1 | Orta (573px) | Dosya tarayıcı |
| Info Panel | 1 | Sağ (220px) | Bilgi paneli |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Sütun (167px)

1. **Disk List** — Disk/kategori listesi
   - Soluk arka plan
   - Her satır: disk adı, boyut
   - Seçili: vurgulu
   - Scrollable

## Orta Sütun (573px)

2. **File Browser** — Ana dosya tarayıcısı
   - Breadcrumb navigasyonu
   - Liste görünümü
   - Her satır: dosya adı, boyut, tarih
   - Klasör: 📁 ikonu
   - Dosya: 🎵 🎬 ikonları

## Sağ Sütun (220px)

3. **Info Panel** — Seçili dosya/klasör bilgisi
   - Dosya adı
   - Boyut
   - Tarih
   - Tür
   - Önizme (müzik/dosya)

## ASCII Art Referansı

`00-mockup-index.md` §4.8 — Browse (Disk) ASCII Art
