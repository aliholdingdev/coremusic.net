---
node_id: K014-network/network-testing
node_type: subdomain
status: draft
parent_id: K014-network
children: []
depends_on: []
evidence:
  - "v2 §2.14 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.14 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "network-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/6)"
group: mimari
---


# network-testing — K014-network (kademe-2 · subdomain)

**ELI15:** Ağ davranışının testleri: gecikme, kopma, çevrimdışı senaryoları.

**Sınır (ne DEĞİL):** Ortak test altyapısı ve kapsam sahibi K019-testing.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler network-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-4 module kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | network-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/6) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
