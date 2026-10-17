---
node_id: K010-network/comms/device-communication
node_type: bounded-context
status: draft
parent_id: K010-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
  - "RFC 6762/6763/7252/7641/8323/3927/6761 — yerel protokol adayları — 7 URL HTTP 200 (2026-10-15, device-communication-standart-kanit.md §1–§3)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (device-communication-standart-kanit.md)"
open_questions:
  - "Yerel protokol SEÇİMİ (adaylar S1 ile kanıtlandı: mDNS/DNS-SD · CoAP · link-local) → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
group: mimari
---


# device-communication — K010-network/comms (kademe-3 · bounded-context)

**ELI15:** Evdeki cihazların birbirleriyle konuşma biçimi (yerel ağ içi).

**Sınır (ne DEĞİL):** Cihaz kimliği K0100-identitiy · cihaz envanteri K012/device-service.

**Bağımlılık (özet):** Üstü comms/ → K010-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| kanit | `device-communication-standart-kanit.md` | mDNS/DNS-SD · CoAP · link-local aday kanıtı — URL defteri | Kademe-3 |
| kod | `device-communication-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — yerel cihaz yoklama iskeleti | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Yerel protokol SEÇİMİ (adaylar S1 kanıtlandı) → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 4 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
