---
title: "Sayfa Prompt - Albüm Detayı"
type: page-prompt
category: page-prompt
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
route: "/album/:id"
layout: "standard-60-40"
---

# Albüm Detayı (Album Detail)

## Route: `/album/:id`
## Layout Pattern: Standard 60/40

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| C13 Track List | 1 | Sol (60%) | Şarkı listesi |
| C12 Stars | 1 | Sol alt | Puanlama |
| C10 Detail Panel | 1 | Sağ (40%) | Albüm detayı |
| C04 Button | 1 | Sağ | Oynat butonu |
| C05 Button | 1 | Sağ | Rastgele butonu |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Alan (60% = 614px)

1. **C13 Track List** — Şarkı listesi
   - Her satır: #, Başlık, Süre, Kalite ikonu
   - Hover: soluk arka plan
   - Seçili: vurgulu arka plan
   - Scroll: max-height ile

2. **C12 Stars** — Albüm puanı
   - 5 yıldız
   - Yıldızlar arası 4px

## Sağ Alan (40% = 394px)

3. **C10 Detail Panel** — Albüm detayı
   - Büyük albüm kapağı (200×200px)
   - Albüm adı, sanatçı
   - Yıl, tür, parça sayısı
   - Toplam süre

4. **C04 Button** — Oynat (primary)
5. **C05 Button** — Rastgele (secondary)

## ASCII Art Referansı

`00-mockup-index.md` §4.4 — Album Detail ASCII Art

---

*Page Prompt Album Detail v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
