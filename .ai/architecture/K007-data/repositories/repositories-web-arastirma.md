---
node_id: "K007-repositories-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "3 URL — php.net PDO (2026-10-12 tarama, 2026-10-15 derleme)"
validation: "2026-10-15 — mevcut havuz §10 derlendi"
open_questions: []
group: kanit
---

# repositories — Web Araştırması (K007)

| # | URL | Ne verdi | Tür | Durum |
|---|-----|----------|-----|-------|
| WR-1 | php.net/manual/en/book.pdo.php | PDO katman tanımı | T1 | HTTP 200 |
| WR-2 | php.net/manual/en/ref.pdo-mysql.php | ATTR_EMULATE_PREPARES · ATTR_PERSISTENT | T3 | HTTP 200 |
| WR-3 | php.net/manual/en/pdo.prepared-statements.php | prepare/execute semantiği | T3 | HTTP 200 |

**K007 karşılığı:** D-R1/D-R4 bu üç sayfanın DatabaseManager satırlarıyla **birebir eşleşmesidir**
(data-web-arastirma.md §10, en güçlü T4 hattı).

---

**Authority:** Bayram Ali / Vault Steward