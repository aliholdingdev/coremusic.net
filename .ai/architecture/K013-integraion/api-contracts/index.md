---
node_id: K013-integraion/api-contracts
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
  - "Sürümleme stratejisi (v1/v2 vb.) → ⚠️ UNKNOWN"
---

# api-contracts — K013-integraion (kademe-2 · subdomain)

**ELI15:** Dış API'nin konuşacağı sözleşmeler: istek/yanıt tipleri, alan adları, sürüm.

**Sınır (ne DEĞİL):** Adaptör uygulaması adapters/child · taşıma K014 · servis çağıran K012/integration-service.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler integraion-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
