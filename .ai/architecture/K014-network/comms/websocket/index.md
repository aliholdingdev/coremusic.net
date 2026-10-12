---
node_id: K014-network/comms/websocket
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

# websocket — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Sunucu ile istemci arasında sürekli, çift yönlü canlı bağlantı.

**Sınır (ne DEĞİL):** İstek/yanıt http · doğrudan medya yolu webrtc · paket güvenliği K009.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.
