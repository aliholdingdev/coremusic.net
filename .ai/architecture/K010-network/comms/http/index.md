---
node_id: K014-network/comms/http
node_type: bounded-context
status: draft
parent_id: K014-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
  - "RFC 9110/9112/9113/9114/8441 + MDN + ekosistem — 12 URL HTTP 200 (2026-10-15, http-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Standart kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (http-standart-kanit.md)"
open_questions:
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Çekirdek kütüphane seçimi (istemci/sunucu) → ⚠️ VERIFICATION REQUIRED (faz C)"
group: mimari
---


# http — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** İstek/yanıt biçimiyle internet üzerinden konuşmayı sağlayan temel protokol.

**Sınır (ne DEĞİL):** Sürekli canlı bağlantı websocket · doğrudan medya yolu webrtc · anlam K012/K013.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| kanit | `http-standart-kanit.md` | RFC/MDN/ekosistem URL kanıt defteri (12 S1) | Kademe-4 |
| kod | `http-kod-ornekleri.md` | 7 dilde dayanıklı istemci şablonu (TASARIM ÖRNEĞİ) | Kademe-4 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 2 | Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
