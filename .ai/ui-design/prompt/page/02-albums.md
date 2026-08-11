---
title: "Sayfa Prompt — Albümler"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/albums"
layout: "standard-60-40"
---

# Albümler (Albums)

## Route: `/albums`
## Layout Pattern: Standard 60/40

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| C09 Card Grid | 9 | Sol (60%) | Albüm kartları 3×3 |
| C11 Tabs | 1 | Sol üst | Kategori sekmeleri |
| C10 Detail Panel | 1 | Sağ (40%) | Albüm detayı |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Alan (60% = 614px)

1. **C11 Tabs** — Üstte, kategori sekmeleri (Tümü, Yeni, Popüler, Favori)
2. **C09 Card Grid** — 3×3 ızgarada albüm kartları
   - Her kart: 190×230px
   - Gap: 16px

## Sağ Alan (40% = 394px)

3. **C10 Detail Panel** — Seçilen albümün detayı
   - Albüm kapağı
   - Albüm adı, sanatçı
   - Parça sayısı, süre
   - Oynat butonu

## ASCII Art Referansı

`00-mockup-index.md` §4.3 — Albums ASCII Art

## C09 Kart Yapısı

```
┌─────────────┐
│  [Kapak]    │  190×120px
│             │
├─────────────┤
│  Albüm Adı  │  14px, bold
│  Sanatçı    │  12px, muted
└─────────────┘
  190×230px toplam
```
