---
node_id: indexing
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - indexing-kapsam
  - indexing-bilesenler
  - indexing-dogrulama
  - indexing-ornekler
  - indexing-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/search
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 282"
  - ".ai/.sql/mysql/*.sql — KEY/UNIQUE KEY/prefix envanteri"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "EXPLAIN rutini / fullscan ölçümü → UNKNOWN"
group: mimari
---

# indexing — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | indexing |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 282) |
| Durum | draft |

## Ne işe yarar?

Sorgu hızını koruyan B-tree yapıları: UNIQUE · kompozit · normal · prefix (100).
Online DDL (INPLACE) ile kilitsiz indeks ekleme MySQL 8.4'te doğrulanmıştır.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [indexing-kapsam.md](indexing-kapsam.md) | sınır | mimari |
| 2 | [indexing-bilesenler.md](indexing-bilesenler.md) | tür envanteri + karar matrisi | mimari |
| 3 | [indexing-dogrulama.md](indexing-dogrulama.md) | D-I1…D-I5 | kanit |
| 4 | [indexing-ornekler.md](indexing-ornekler.md) | dump'tan SQL | kod |
| 5 | [indexing-web-arastirma.md](indexing-web-arastirma.md) | MySQL/PG 6 URL | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Künye | Kademe-2 |
| mimari | `indexing-kapsam.md` | Kapsam | Kademe-2 |
| mimari | `indexing-bilesenler.md` | Bileşenler | Kademe-2 |
| kanit | `indexing-dogrulama.md` | İddia defteri | Kademe-2 |
| kod | `indexing-ornekler.md` | SQL örnekleri | Kademe-2 |
| kanit | `indexing-web-arastirma.md` | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward