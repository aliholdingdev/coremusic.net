---
node_id: K012-services/background-jobs
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
  - "İş motoru (kuyruk/zamanlayıcı) teknolojisi → ⚠️ UNKNOWN (§7/6)"
---

# background-jobs — K012-services (kademe-2 · subdomain)

**ELI15:** Zamanlanmış ve arka plana atılmış işleri kuyruğa alıp koşan yapı.

**Sınır (ne DEĞİL):** Uygulama içi çok adımlı akış K011/workflows'da olabilir → sınır ⚠️.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
