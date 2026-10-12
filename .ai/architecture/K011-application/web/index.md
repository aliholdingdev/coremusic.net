---
node_id: K011-application/web
node_type: subdomain
status: draft
parent_id: K011-application
children: []
depends_on: []
evidence:
  - "v2 §2.11 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.11 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kademe-3 kapsamı: hangi BC'ler web'e özel? → ⚠️ VERIFICATION REQUIRED"
---

# web — K011-application (kademe-2 · subdomain)

**ELI15:** Tarayıcıda çalışan arayüz katmanı: sayfa, bileşen, stil ve form akışları.

**Sınır (ne DEĞİL):** İş kuralı K008-domain'de · servis sahibi K012 · ağ taşıması K014.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler application-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
