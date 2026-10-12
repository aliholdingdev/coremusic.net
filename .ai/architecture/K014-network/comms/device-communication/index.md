---
node_id: K014-network/comms/device-communication
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

# device-communication — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Evdeki cihazların birbirleriyle konuşma biçimi (yerel ağ içi).

**Sınır (ne DEĞİL):** Cihaz kimliği K010-identitiy · cihaz envanteri K012/device-service.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.
