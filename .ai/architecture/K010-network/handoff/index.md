---
node_id: K014-network/handoff
node_type: subdomain
status: draft
parent_id: K014-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
  - "MDN Remote Playback/Presentation/WebSockets + RFC 6455/9110 — 5 URL HTTP 200 (2026-10-15, handoff-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Teslim/kanal kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (handoff-standart-kanit.md)"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "handoff ↔ multi-room sınırı (teslim mi senkron mu) → ⚠️ VERIFICATION REQUIRED (§7/1) — teslim yolu S1 kanıtlandı, sınır kararı faz C"
  - "Teslim anı/pozisyon aktarım paket formatı → ⚠️ UNKNOWN (faz C)"
group: mimari
---


# handoff — K014-network (kademe-2 · subdomain)

**ELI15:** Çalan şeyi bir cihazdan diğerine teslim etme (el değiştirmesi).

**Sınır (ne DEĞİL):** Çalma durumunun sahibi K012/playback-service · zaman hizası multi-room'da.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `handoff-standart-kanit.md` | Remote Playback/Presentation · WS/REST kanalı — URL kanıtı | Kademe-2 |
| kod | `handoff-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — tek devir (offer/accept) token deposu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | handoff ↔ multi-room sınırı (teslim mi senkron mu) → ⚠️ VERIFICATION REQUIRED (§7/1) | FM open_questions |
| 3 | Teslim anı/pozisyon aktarım paket formatı → ⚠️ UNKNOWN (faz C) | FM open_questions |
| 4 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
