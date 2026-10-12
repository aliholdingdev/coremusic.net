---
node_id: K012-services/integration-service
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on: []
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.12 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "K013 ile çağrı yönü ve hata sınırı → ⚠️ VERIFICATION REQUIRED"
---

# integration-service — K012-services (kademe-2 · subdomain)

**ELI15:** Dış sistem çağrılarını barındıran servis; K013-integraion kontratlarını çağırır.

**Sınır (ne DEĞİL):** Protokol/adapter/hata yönetimi K013'tedir; bu servis yalnız çağrı yapar.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
