---
node_id: K014-network/comms/http
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

# http — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** İstek/yanıt biçimiyle internet üzerinden konuşmayı sağlayan temel protokol.

**Sınır (ne DEĞİL):** Sürekli canlı bağlantı websocket · doğrudan medya yolu webrtc · anlam K012/K013.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.
