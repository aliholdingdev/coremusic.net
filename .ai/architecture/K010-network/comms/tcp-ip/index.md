---
node_id: K014-network/comms/tcp-ip
node_type: bounded-context
status: draft
parent_id: K014-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
  - "RFC 9293/8200/3927/6761/8446 + mDNS/DNS-SD/CoAP kesişimi — 8 URL HTTP 200 (2026-10-15, tcp-ip-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (tcp-ip-standart-kanit.md)"
open_questions:
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Port aralığı · keepalive parametreleri → ⚠️ UNKNOWN (faz C)"
group: mimari
---


# tcp-ip — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Yerel ağ dahil paket taşımasının temel katmanları.

**Sınır (ne DEĞİL):** Uygulama protokolleri (http/websocket) üstünde çalışır · donanım K001.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| kanit | `tcp-ip-standart-kanit.md` | RFC 9293/8200/3927/6761/8446 + mDNS/DNS-SD/CoAP kesişimi — URL kanıt defteri | Kademe-3 |
| kod | `tcp-ip-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — uzunluk-önekli (4B BE) çerçeveleme sözleşmesi | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 2 | Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
