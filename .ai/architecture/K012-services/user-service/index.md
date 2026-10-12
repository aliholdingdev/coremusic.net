---
node_id: K012-services/user-service
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
  - "profil ile kimlik verisi ayrımı → ⚠️ VERIFICATION REQUIRED"
---

# user-service — K012-services (kademe-2 · subdomain)

**ELI15:** Kullanıcı profili, tercihi ve abonelik işlerini yürüten servis.

**Sınır (ne DEĞİL):** Kimlik doğrulama K010-identitiy'de · profil verisi K007-data'da.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
