---
node_id: "K007-data/migrations-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/migrations"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "4 URL — dev.mysql.com online DDL (2026-10-15 yeni tarama)"
  - "3 URL — Alembic/Flyway/Liquibase (2026-10-12 havuz §5)"
validation: "2026-10-15 — yeni MySQL taraması + mevcut framework havuzu birleştirildi"
open_questions:
  - "Migration çalıştırma aracı (CLI) → UNKNOWN"
group: kanit
---

# migrations — Web Araştırması (K007)

## §1 Yeni tarama — MySQL 8.4 DDL (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WM-1 | dev.mysql.com/doc/refman/8.4/en/innodb-online-ddl-operations.html | ekleme/silme/yeniden adlandırma: INPLACE + concurrent DML | HTTP 200 |
| WM-2 | …/create-index.html | CREATE INDEX = ALTER TABLE eşlemesi · ALGORITHM/LOCK | HTTP 200 |
| WM-3 | …/optimizing-innodb-ddl-operations.html | önce yükle sonra indeksle; PK planı önceden ver | HTTP 200 |
| WM-4 | …/alter-table.html | ALTER TABLE genel çerçevesi · LOCK=NONE seçenekleri | HTTP 200 |

## §2 Mevcut havuz (data-web-arastirma.md §5 · 2026-10-12)

| # | URL | Ne verdi | Tür |
|---|-----|----------|-----|
| WM-5 | alembic.sqlalchemy.org | Python migration (referans) | T1 |
| WM-6 | flyway.org/quickstart | Java migration · versioned | T1 |
| WM-7 | docs.liquibase.com | changelog modeli | T1 |

**K007 karşılığı:** CoreMusic'te migration PHP dosyalarıdır (repo gerçeği) — WM-5..7
yalnız kalıp referansıdır; WM-1..4 üretime doğrudan uygulanır (veritabanı düzeyinde).

---

**Authority:** Bayram Ali / Vault Steward