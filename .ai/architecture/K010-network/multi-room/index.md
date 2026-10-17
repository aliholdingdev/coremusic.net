---
node_id: K010-network/multi-room
node_type: subdomain
status: draft
parent_id: K010-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
  - "RFC 5905 (NTP) · RFC 3550 (RTP) · MDN Performance.now() — 3 URL HTTP 200 (2026-10-15, multi-room-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Araç kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (multi-room-standart-kanit.md)"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Zaman hizalama YÖNTEMİ → ⚠️ UNKNOWN (§7/8) — araçlar S1 kanıtlandı (NTP/RTP/monotonik saat), seçim faz C"
  - "Tolerans eşiği (kabul edilebilir kayma ms) → ⚠️ UNKNOWN (faz C)"
group: mimari
---


# multi-room — K010-network (kademe-2 · subdomain)

**ELI15:** Birden fazla odada/şehirde çalmanın zaman hizasını koruma.

**Sınır (ne DEĞİL):** Oda/çihaz envanteri K012/device-service · ses üretimi K003-audio.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `multi-room-standart-kanit.md` | NTP/RTP/monotonik saat — zaman hizası araç kanıtı | Kademe-2 |
| kod | `multi-room-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — NTP tarzı offset tahmini (min-RTT) | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Zaman hizalama yöntemi → ⚠️ UNKNOWN (§7/8) | FM open_questions |
| 3 | Tolerans eşiği (kabul edilebilir kayma ms) → ⚠️ UNKNOWN (faz C) | FM open_questions |
| 4 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
