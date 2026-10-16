---
node_id: K014-network/comms/webrtc
node_type: bounded-context
status: draft
parent_id: K014-network/comms
children: []
depends_on: []
evidence:
  - "v2 §2.14 — comms torun adı (birebir)"
  - "W3C WebRTC 1.0 + IETF RFC 8825/8826/8445/5389/5766/9147/3711/8446 — 9+1 URL HTTP 200 (2026-10-15, webrtc-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "master §7/4 (WebRTC resmi kaynağı) KAPANDI — W3C + IETF S1 kanıtı 2026-10-15"
open_questions:
  - "Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "TURN sunucusu sahibi · sinyal kanalı taşıyıcısı → ⚠️ faz C"
group: mimari
---


# webrtc — K014-network/comms (kademe-3 · bounded-context)

**ELI15:** Doğrudan (uçtan uca) medya/veri yolu kuran protokol ailesi.

**Sınır (ne DEĞİL):** Kurulum sinyallemesi ayrı kanaldır (sınır ⚠️ UNKNOWN) · şifreleme K009.

**Bağımlılık (özet):** Üstü comms/ → K014-network; ayrıntı network-bagimliliklar.md §1.

**Durum:** draft · kademe-4 (module) ADR-097 K4 fazı (C) bekler · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| kanit | `webrtc-standart-kanit.md` | W3C/IETF URL kanıt defteri (10 S1 — master §7/4 kapandı) | Kademe-4 |
| kod | `webrtc-kod-ornekleri.md` | 7 dilde peer/sinyal şablonu (TASARIM ÖRNEĞİ) | Kademe-4 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kendi kuralları (bounded-context sözleşmesi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 2 | Kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
