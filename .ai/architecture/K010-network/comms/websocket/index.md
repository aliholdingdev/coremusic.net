---
node_id: K014-network/comms/websocket
node_type: bounded-context
status: draft
parent_id: K014-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
  - "RFC 6455/8441/9110 + MDN + Ratchet/libwebsockets — 8 URL HTTP 200 (2026-10-15, websocket-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (websocket-standart-kanit.md)"
open_questions:
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Keep-alive periyodu · mesaj çerçevesi · sunucu kütüphanesi → ⚠️ faz C"
group: mimari
---


# websocket — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Sunucu ile istemci arasında sürekli, çift yönlü canlı bağlantı.

**Sınır (ne DEĞİL):** İstek/yanıt http · doğrudan medya yolu webrtc · paket güvenliği K009.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| kanit | `websocket-standart-kanit.md` | RFC/MDN/ekosistem URL kanıt defteri (8 S1) | Kademe-4 |
| kod | `websocket-kod-ornekleri.md` | 7 dilde reconnect+keepalive şablonu (TASARIM ÖRNEĞİ) | Kademe-4 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 2 | Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
