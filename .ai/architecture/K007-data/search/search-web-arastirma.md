---
node_id: "K007-search-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "6 URL — dev.mysql.com FULLTEXT (2026-10-15 yeni tarama)"
  - "5 URL — Meilisearch/Solr/ES (2026-10-12 havuz)"
validation: "2026-10-15 — yeni + mevcut birleştirildi"
open_questions:
  - "K-06 motor kararı → UNKNOWN"
group: kanit
---

# search — Web Araştırması (K007)

## §1 Yeni tarama — MySQL FULLTEXT (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WS-1 | dev.mysql.com/doc/refman/8.4/en/fulltext-search.html | FULLTEXT çerçevesi · 3 arama tipi | HTTP 200 |
| WS-2 | …/fulltext-natural-language.html | natural language · stopword · 50% eşiği (MyISAM) | HTTP 200 |
| WS-3 | …/fulltext-boolean.html | +,-,*,~,@distance operatörleri; BM25/TF-IDF | HTTP 200 |
| WS-4 | …/fulltext-query-expansion.html | blind query expansion (kısa ifade) | HTTP 200 |
| WS-5 | …/innodb-fulltext-index.html | inverted index · DOC_ID · commit-time | HTTP 200 |
| WS-6 | …/fulltext-restrictions.html | ucs2 yasak; DML commit'te indekslenir | HTTP 200 |

## §2 Mevcut havuz (data-web-arastirma.md §3 · 2026-10-12)

| # | URL | Tür |
|---|-----|-----|
| WS-7 | meilisearch.com/docs · github.com/meilisearch | T1 ⚠️ (hız iddiası bağımsız değil) |
| WS-8 | lucene.apache.org/solr | T1 |
| WS-9 | elastic.co/docs | T1 |
| WS-10 | karşılaştırmalı makale (W35/W36) | T1 ⚠️ yazar görüşü |

---

**Authority:** Bayram Ali / Vault Steward