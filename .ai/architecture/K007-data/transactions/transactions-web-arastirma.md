---
node_id: "K007-data/transactions-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/transactions"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "4 URL — dev.mysql.com InnoDB (2026-10-15 yeni tarama)"
  - "2 URL — PDO transaction API (2026-10-12 havuz)"
validation: "2026-10-15 — yeni MySQL taraması + mevcut PDO havuzu"
open_questions:
  - "İzolasyon seviyesi değiştirilmiş mi (SET TRANSACTION) → config okunmadı → UNKNOWN"
group: kanit
---

# transactions — Web Araştırması (K007)

## §1 Yeni tarama — MySQL 8.4 InnoDB (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WT-1 | dev.mysql.com/doc/refman/8.4/en/innodb-transaction-isolation-levels.html | 4 seviye · REPEATABLE READ varsayılan · SET TRANSACTION | HTTP 200 |
| WT-2 | …/innodb-locking.html | sütun/tablo satır kilidi · shared/exclusive | HTTP 200 |
| WT-3 | …/locks-set-by-statements.html | statement kilitleri · implicit commit | HTTP 200 |
| WT-4 | …/innodb-deadlocks.html | deadlock otomatik rollback · deadlock sayısı | HTTP 200 |

## §2 Mevcut havuz (data-web-arastirma.md §9 · 2026-10-12)

| # | URL | Ne verdi | Tür |
|---|-----|----------|-----|
| WT-5 | php.net/manual/en/pdo.begintransaction.php | begin/commit/rollBack API | T3 |
| WT-6 | php.net/manual/en/ref.pdo-mysql.php | InnoDB + ATTR yapısı | T3 |

**K007 karşılığı:** IDatabaseManager.php:8-14 üç metodu bu resmi API'nin sözleşmesidir
(D-T1 çift uçlu T4).

---

**Authority:** Bayram Ali / Vault Steward