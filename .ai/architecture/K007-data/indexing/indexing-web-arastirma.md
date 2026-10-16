---
node_id: "K007-indexing-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "5 URL — dev.mysql.com 8.4 + postgresql.org (2026-10-15 / 2026-10-13)"
validation: "2026-10-15 — yeni MySQL taraması + mevcut PostgreSQL havuzu"
open_questions: []
group: kanit
---

# indexing — Web Araştırması (K007)

## §1 Yeni tarama (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WI-1 | dev.mysql.com/doc/refman/8.4/en/column-indexes.html | FULLTEXT prefix desteklemez; optimizasyonlar | HTTP 200 |
| WI-2 | …/create-index.html | `CREATE INDEX` = ALTER TABLE eşlemesi; `ALGORITHM/LOCK` | HTTP 200 |
| WI-3 | …/innodb-online-ddl-operations.html | secondary index ekleme: INPLACE, concurrent DML OK | HTTP 200 |
| WI-4 | …/optimizing-innodb-ddl-operations.html | önce yükle sonra indeksle; PK planı önceden ver | HTTP 200 |

## §2 Mevcut havuz (2026-10-13)

| # | URL | Ne verdi |
|---|-----|----------|
| WI-5 | postgresql.org/docs/current/indexes.html | B-tree temelleri |
| WI-6 | postgresql.org/docs/current/indexes-multicolumn.html | composite sırası |

---

**Authority:** Bayram Ali / Vault Steward