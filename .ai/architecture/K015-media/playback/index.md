---
node_id: K015-media/playback
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahiplik: K015 mi K012 mi → ⚠️ VERIFICATION REQUIRED (§7/1)"
---

# playback — K015-media (kademe-2 · subdomain)

**ELI15:** Dosyanın/sinyalin çalınması: okuma, konum, oynatma durumu.

**Sınır (ne DEĞİL):** Ses işleme K003-audio · durum orkestrasyonu K012/playback-service ile çakışma riski.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
