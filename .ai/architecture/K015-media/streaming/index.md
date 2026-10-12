---
node_id: K015-media/streaming
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
  - "Akış biçimi/protokolü → ⚠️ UNKNOWN (taşıma K014'te)"
---

# streaming — K015-media (kademe-2 · subdomain)

**ELI15:** Akışla dinleme: kaynağından parça parça okuyup çalma.

**Sınır (ne DEĞİL):** Taşıma K014-network · dış kaynak K013-integraion · çalma K015/playback.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
