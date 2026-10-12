---
node_id: K012-services/ai-service
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
  - "Bağlı olduğu düğüm K006 mı K016 mı → ⚠️ VERIFICATION REQUIRED (§7/1)"
---

# ai-service — K012-services (kademe-2 · subdomain)

**ELI15:** Yapay zeka işlerini diğer servislere sunan arayüz servisi.

**Sınır (ne DEĞİL):** YZ yeteneği K006-yapay-zeka veya K016-AI'dadır; hangisi belli değil.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
