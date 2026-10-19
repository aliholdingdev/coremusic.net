---
node_id: "K007-backup-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "5 URL — dev.mysql.com 8.4 Refman (2026-10-15 HTTP 200)"
  - "3 URL — Veeam/Backblaze 3-2-1 (2026-10-12, ikincil kaynak ⚠️)"
validation: "2026-10-15 — yeni tarama (MySQL resmi) + mevcut havuz (3-2-1) birleştirildi"
open_questions:
  - "Fiziksel yedek aracı seçimi (mysqlbackup/xtrabackup) → UNKNOWN"
group: kanit
---

# backup — Web Araştırması (K007)

## §1 Yeni tarama — MySQL 8.4 resmi (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WB-1 | dev.mysql.com/doc/refman/8.4/en/backup-and-recovery.html | Bölüm 9 çerçevesi: mantıksal/fiziksel · tam/artık · PITR | HTTP 200 |
| WB-2 | …/using-mysqldump.html | `--single-transaction` kilitsize InnoDB; `--tab` CSV varyantı | HTTP 200 |
| WB-3 | …/mysqldump.html | mantıksal yedek tanımı; büyük veri için yavaş olduğu uyarısı; `--source-data` koordinat | HTTP 200 |
| WB-4 | …/point-in-time-recovery-binlog.html | PITR = full dump + binlog tekrarı; `SHOW BINARY LOG STATUS` | HTTP 200 |
| WB-5 | …/point-in-time-recovery-positions.html | `--start-position/--stop-position` ile hassas geri alma | HTTP 200 |
| WB-6 | …/backup-methods.html | incremental = binlog; `FLUSH LOGS` rotasyonu | HTTP 200 |
| WB-7 | …/mysqlbinlog-backup.html | canlı binlog yedeği (`--raw --stop-never`) | HTTP 200 |

## §2 Mevcut havuzdan (data-web-arastirma.md §9 · 2026-10-12)

| # | URL | Tür | Not |
|---|-----|-----|-----|
| WB-8 | veeam.com/blog/321-backup-rule.html | T2 ⚠️ | 3-2-1 — satıcı bloğu |
| WB-9 | backblaze.com/blog/the-3-2-1-backup-strategy/ | T2 ⚠️ | 3-2-1 — satıcı bloğu |

## §3 K007 karşılığı

- **D-B3/D-B4/D-B5** bu taramanın T3 çıktılarıdır (backup-dogrulama.md).
- Operasyonel sahiplik (cron/RTO) **S-2 ile K17-devops'a askıda**.

---

**Authority:** Bayram Ali / Vault Steward