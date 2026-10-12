---
node_id: K014-network/comms/tcp-ip
node_type: bounded-context
status: draft
parent_id: K014-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
---

# tcp-ip — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Yerel ağ dahil paket taşımasının temel katmanları.

**Sınır (ne DEĞİL):** Uygulama protokolleri (http/websocket) üstünde çalışır · donanım K001.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.
