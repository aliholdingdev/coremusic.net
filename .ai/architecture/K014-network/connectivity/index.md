---
node_id: K014-network/connectivity
node_type: subdomain
status: draft
parent_id: K014-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Yeniden bağlanma ve fallback stratejisi → ⚠️ UNKNOWN"
---

# connectivity — K014-network (kademe-2 · subdomain)

**ELI15:** Bağlantıyı kurma, sürdürme ve koptuğunda yeniden kurma; bağlantı durumunun tutulması.

**Sınır (ne DEĞİL):** Fiziksel donanım K001-donanım · şifreleme K009-security · anlam/protokol comms/altında.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.
