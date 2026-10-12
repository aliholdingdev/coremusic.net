---
node_id: K012-services/service-contracts
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
  - "Evrensel Playlist/Track spec'i (hub-and-spoke) burada mı? → TASARIM İDDİASI (§7/4)"
---

# service-contracts — K012-services (kademe-2 · subdomain)

**ELI15:** Servislerin birbirine söylediği sözleşmeler (mesaj/DTO biçimi).

**Sınır (ne DEĞİL):** Kontrat sahipliği başka domain'e kaymamalı; dış API kontratı K013'tedir.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
