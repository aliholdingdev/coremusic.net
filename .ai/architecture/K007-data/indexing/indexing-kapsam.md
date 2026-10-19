---
node_id: "K007-indexing-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/search"
evidence:
  - ".ai/.sql/mysql/coremusic_musics.sql — uk_acn_credit_pos · idx_artist_credit_display(100) (2026-10-15)"
  - ".ai/.sql/mysql/coremusic_catalog.sql — uk_album_types_name · idx_album_types_sort_order"
  - "dev.mysql.com/doc/refman/8.4/en/column-indexes.html — HTTP 200 (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; dump örnekleri + MySQL resmi URL"
open_questions:
  - "EXPLAIN tabanlı inceleme rutini repo'da YOK → UNKNOWN"
group: mimari
---

# indexing — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | B-tree indeks kararı (WHERE/JOIN kolonu) | dump'larda `KEY`/`UNIQUE KEY` |
| 2 | Prefix (dizgi) indeks — uzun varchar | `idx_artist_credit_display(100)` |
| 3 | Composite indeks sırası | `uk_acn_credit_pos(artist_credit_id, position)` |
| 4 | Düşük kardinalite tercihleri | `idx_album_types_sort_order` |
| 5 | Online DDL ile indeks ekleme | MySQL 8.4 §17.12.1 (2026-10-15) |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| FULLTEXT indeks davranış arama dili | search | FULLTEXT ≠ B-tree |
| Şema genel tasarımı | schema | indeks şemanın bir parçası, bu çocuk yalnız **hız** sorusunu sorar |
| Slow query log işletimi | K016-observability | ölçüm akışı |
| Migration ile indeks taşıma | migrations | uygulama yüzeyi |

## §3 Sınır cümlesi

indexing, "bu sorgu tabloyu tarıyor mu, indeksten mi geçiyor?" sorusunun sahibidir —
okuma hızını korur, yazma/mekân maliyetini bilir.

---

**Authority:** Bayram Ali / Vault Steward