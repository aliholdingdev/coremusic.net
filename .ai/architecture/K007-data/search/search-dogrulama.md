---
node_id: "K007-search-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/search-web-arastirma"
evidence:
  - "satır D-S1…D-S6 — 2026-10-15 kaydı"
validation: "2026-10-15 — 6 iddia: 2 T4, 2 T3, 2 UNKNOWN"
open_questions:
  - "S-1 sahiplik ADR'si bekleniyor"
group: kanit
---

# search — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-S1 | novasearch şeması canlıda var (7 tablo) | `.ai/.sql/mysql/novasearch.sql` | — | T4 | ✅ DOĞRULANDI |
| D-S2 | FULLTEXT sadece InnoDB/MyISAM; CHAR/VARCHAR/TEXT | — | dev.mysql.com/en/fulltext-search.html | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-S3 | InnoDB boolean mode BM25/TF-IDF sıralar | — | …/fulltext-boolean.html | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-S4 | Repo kodunda MATCH…AGAINST kullanımı | okunan dosyalarda YOK | — | — | ⚠️ VERIFICATION REQUIRED |
| D-S5 | novasearch'ta FULLTEXT indeks tanımı | dump'ta görünmedi | — | — | **UNKNOWN** |
| D-S6 | Dış motor seçimi | YOK | — | — | **UNKNOWN** (K-06) |

---

**Authority:** Bayram Ali / Vault Steward