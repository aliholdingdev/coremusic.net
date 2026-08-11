---
title: "Sayfa Prompt — Sanatçılar"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/artists"
layout: "standard-60-40"
---

# Sanatçılar (Artists)

## Route: `/artists`
## Layout Pattern: Standard 60/40

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| C09 Card (circular) | 9 | Sol (60%) | Dairesel sanatçı kartları |
| C11 Tabs | 1 | Sol üst | Kategori sekmeleri |
| C10 Detail Panel | 1 | Sağ (40%) | Sanatçı detayı |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Alan (60% = 614px)

1. **C11 Tabs** — Üstte, kategori sekmeleri (Tümü, Takip Edilen, Önerilen)
2. **C09 Card Grid** — 3×3 ızgarada dairesel sanatçı kartları
   - Her kart: dairesel görsel + alt kısımda ad
   - Kart boyutu: 170×190px
   - Gap: 16px

## Sağ Alan (40% = 394px)

3. **C10 Detail Panel** — Seçilen sanatçının detayı
   - Büyük dairesel görsel (150×150px)
   - Sanatçı adı
   - Takipçi sayısı
   - Albüm sayısı
   - Takip butonu

## ASCII Art Referansı

`00-mockup-index.md` §4.5 — Artists ASCII Art

## C09 Circular Kart Yapısı

```
    ┌─────────┐
   /           \
  │   [Foto]   │  140×140px circle
   \           /
    └─────────┘
    Sanatçı Adı    14px, bold, center
    12 Albüm       12px, muted, center
```
