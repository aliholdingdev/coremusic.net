---
node_id: "K007-backup-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/backup-web-arastirma"
evidence:
  - "satır D-B1…D-B6 — 2026-10-15 kaydı"
validation: "2026-10-15 — 6 iddia: 3 T4 (repo+web çift uç), 2 T3 (resmi doküman), 1 UNKNOWN"
open_questions:
  - "RTO/RPO değeri → UNKNOWN (ölçüm yapılmadı)"
group: kanit
---

# backup — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-B1 | Yıkıcı migration öncesi yedek zorunlu | `2026-10_07_drop_…migration.php:14-20` | — | T4 | ✅ DOĞRULANDI |
| D-B2 | Dump üretimi `full_dump.php` ile yapılır | `.ai/.sql/CLAUDE.md §3` | — | T4 | ✅ DOĞRULANDI |
| D-B3 | mysqldump mantıksal yedek üretir (SQL statement kümesi) | — | dev.mysql.com/en/mysqldump.html | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-B4 | PITR binlog ile yapılır; MySQL 8.4'te binlog varsayılan açık | — | dev.mysql.com/en/point-in-time-recovery-binlog.html | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-B5 | `--single-transaction` InnoDB'de kilitsize yedek sağlar | — | dev.mysql.com/en/backup-methods.html | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-B6 | RTO/RPO hedef değerleri | YOK | YOK | — | **UNKNOWN** |

## §1 Kapanış koşulu (S-2)

Sahiplik ADR'si yazılana kadar backup `status: draft` kalır; tatbikat kanıtı olmadan
"yedek var" iddiası T4'e çıkamaz (K007-data/data-web-arastirma.md §13 bekleme kuralı).

---

**Authority:** Bayram Ali / Vault Steward