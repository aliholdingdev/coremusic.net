---
node_id: K010-network/offline-sync
node_type: subdomain
status: draft
parent_id: K010-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
  - "MDN IndexedDB/SW/BackgroundSync/BroadcastChannel/WebStorage + SQLite — 6 URL HTTP 200 (2026-10-15, offline-sync-standart-kanit.md §1–§2)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
  - "Teknik yol kanıtları HTTP 200 ile doğrulandı — 2026-10-15 (offline-sync-standart-kanit.md)"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahiplik: K014 mü K015 mü K012 mi → ⚠️ VERIFICATION REQUIRED (§7/2) — teknik yol S1 kanıtlandı, sahiplik kararı faz C"
  - "Çakışma stratejisi (last-write vs kuyruk sırası) → ⚠️ UNKNOWN (faz C)"
group: mimari
---


# offline-sync — K010-network (kademe-2 · subdomain)

**ELI15:** Bağlantı yokken çalışmaya devam etme ve bağlantı gelince eşitleme akışı.

**Sınır (ne DEĞİL):** Kuyruğun içeriği/sahibi belirsiz (K015/offline · K012) · dış eşitleme K013/synchronization.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `offline-sync-standart-kanit.md` | IndexedDB/SW/BackgroundSync/SQLite — teknik yol URL kanıtı | Kademe-2 |
| kod | `offline-sync-kod-ornekleri.md` | 7 dilde TASARIM ÖRNEĞİ — FIFO offline kuyruk + flush | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Sahiplik: K014 mü K015 mü K012 mi → ⚠️ VERIFICATION REQUIRED (§7/2) | FM open_questions |
| 3 | Çakışma stratejisi (last-write vs kuyruk sırası) → ⚠️ UNKNOWN (faz C) | FM open_questions |
| 4 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
