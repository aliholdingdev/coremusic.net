---
title: "Sayfa Prompt - Playlist"
type: page-prompt
category: page-prompt
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
route: "/playlist/:id"
layout: "standard-60-40"
---

# Playlist

## Route: `/playlist/:id`
## Layout Pattern: Standard 60/40

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| C01 Header | 1 | Üst (h=56) | Logo, arama, profil |
| C13 Track Table | 1 | Sol (60%) | Şarkı tablosu |
| C12 Stars | 1 | Sol alt | Puanlama |
| Action Icons | 3 | Sol | Oynat, karıştır, tekrarla |
| C10 Detail Panel | 1 | Sağ (40%) | Playlist detayı |
| C14 Footer Player | 1 | Alt (h=64) | Mini player |

## Sol Alan (60% = 614px)

1. **Action Icons** — Üstte, 3 ikon
   - ▶ Oynat (primary)
   - 🔀 Karıştır
   - 🔁 Tekrarla

2. **C13 Track Table** — Şarkı tablosu
   - Sütunlar: #, Başlık, Sanatçı, Süre
   - Her satır: h=44px
   - Hover: soluk arka plan
   - Seçili: vurgulu arka plan

3. **C12 Stars** — Playlist puanı
   - 5 yıldız, sol alt köşe

## Sağ Alan (40% = 394px)

4. **C10 Detail Panel** — Playlist detayı
   - Playlist kapağı (200×200px)
   - Playlist adı
   - Oluşturan kişi
   - Parça sayısı, toplam süre
   - Paylaş butonu

## ASCII Art Referansı

`00-mockup-index.md` §4.6 — Playlist ASCII Art

---

*Page Prompt Playlist v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
