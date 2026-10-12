---
node_id: K013-integraion/event-contracts
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
  - "Taşıma biçimi: webhook mu olay akışı mı → ⚠️ UNKNOWN"
---

# event-contracts — K013-integraion (kademe-2 · subdomain)

**ELI15:** Dış sistemlerden gelen olayların sözleşmesi (biçim, alan, teslim şekli).

**Sınır (ne DEĞİL):** Olayların iç dağıtımı K012/K016'ya ait olabilir → ⚠️ UNKNOWN.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
