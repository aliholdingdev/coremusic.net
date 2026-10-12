---
node_id: K013-integraion/synchronization
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on: []
evidence:
  - "v2 §2.13 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.13 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahiplik: K013 mü K015 mi (aynı ad) → ⚠️ VERIFICATION REQUIRED (§7/2)"
---

# synchronization — K013-integraion (kademe-2 · subdomain)

**ELI15:** Dış sistemle verinin eşitlenmesi: ne zaman, hangi yön, çakışmada ne olur.

**Sınır (ne DEĞİL):** Kalıcı veri K007-data · medya katalog senkronu K015 ile sınır belirsiz.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
