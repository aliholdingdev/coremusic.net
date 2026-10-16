---
node_id: search
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - search-kapsam
  - search-bilesenler
  - search-dogrulama
  - search-ornekler
  - search-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/indexing
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 284"
  - ".ai/.sql/mysql/novasearch.sql — 7 tablo canlı dump"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "S-1 — arama sahipliği K007 ↔ K020-discovery → SINIR AÇIK"
  - "K-06 dış motor seçimi → UNKNOWN"
group: mimari
---

# search — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | search |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 284) |
| Durum | draft |

## Ne işe yarar?

Şarkı/sanatçı/albüm metinlerinde bulma. Veri yüzü: `novasearch` şeması (7 tablo, canlı)
+ MySQL FULLTEXT (natural/boolean/QE). Motor seçimi K-06, sahiplik S-1 ile askıda.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [search-kapsam.md](search-kapsam.md) | sınır | mimari |
| 2 | [search-bilesenler.md](search-bilesenler.md) | novasearch envanteri + motor matrisi | mimari |
| 3 | [search-dogrulama.md](search-dogrulama.md) | D-S1…D-S6 | kanit |
| 4 | [search-ornekler.md](search-ornekler.md) | dump SQL + FULLTEXT kalıbı | kod |
| 5 | [search-web-arastirma.md](search-web-arastirma.md) | MySQL FULLTEXT 6 URL + motor havuzu | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Künye | Kademe-2 |
| mimari | `search-kapsam.md` | Kapsam | Kademe-2 |
| mimari | `search-bilesenler.md` | Bileşenler | Kademe-2 |
| kanit | `search-dogrulama.md` | İddia defteri | Kademe-2 |
| kod | `search-ornekler.md` | SQL örnekleri | Kademe-2 |
| kanit | `search-web-arastirma.md` | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward