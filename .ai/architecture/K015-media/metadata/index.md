---
node_id: K015-media/metadata
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
  - "Bilginin kaynağı (dış servis mi yerel mi) → ⚠️ VERIFICATION REQUIRED (§7/8)"
---

# metadata — K015-media (kademe-2 · subdomain)

**ELI15:** Şarkı bilgileri: sanatçı, albüm, süre, tür vb. açıklama verisi.

**Sınır (ne DEĞİL):** Dosya/akış K015/streaming · dış kaynak K013-integraion · şema K007-data.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
