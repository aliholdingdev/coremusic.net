---
node_id: "K007-backup-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - "dev.mysql.com/doc/refman/8.4/en/using-mysqldump.html — HTTP 200 (2026-10-15)"
  - "dev.mysql.com/doc/refman/8.4/en/mysqlbinlog-backup.html — HTTP 200 (2026-10-15)"
  - ".ai/.sql/CLAUDE.md §3 — full_dump.php üreteci (2026-10-07)"
validation: "2026-10-15 — 3 bileşen; her biri resmi MySQL URL + repo kanıtına bağlı"
open_questions:
  - "Fiziksel yedek (MySQL Enterprise Backup / xtrabackup) adayı → UNKNOWN"
group: mimari
---

# backup — Bileşenler (K007)

| # | Bileşen | İş | Kanıt | Durum |
|---|---------|-----|-------|-------|
| 1 | **Logical dump (mysqldump)** | Şema+veri SQL çıktısı; `--single-transaction` ile kilitsize InnoDB | MySQL 8.4 §9.4 · `full_dump.php` (.ai/.sql) | plan |
| 2 | **Binlog (artık yedek)** | Full dump sonrası değişim akışı → PITR | MySQL 8.4 §9.5.1 · `--source-data=2` | plan |
| 3 | **Kurtarma provası** | Geri alma tatbikatı → RTO ölçümü | repo kanıtı YOK | ⏳ plan |

## §1 Bileşen bağımlılık zinciri

```text
mysqldump --single-transaction --source-data=2  →  dump.sql (+ binlog koordinatı)
        ↓ (kayıp anında)
mysql < dump.sql                                  →  tam yedek anına dönüş
        ↓
mysqlbinlog --start-position=… binlog.*  | mysql →  hedef ana zamana PITR
```

(Kaynak: MySQL 8.4 §6.6.9.3 örneği — 2026-10-15 okundu.)

## §2 Kademe-3 ipuçları

- 18 DB (`.ai/.sql/CLAUDE.md §3`) → her DB için ayrı dump mı, `--all-databases` mi? **UNKNOWN**
- `media_catalog` canlıda YOK (PLANNED) → yedek kapsamına girmez.

---

**Authority:** Bayram Ali / Vault Steward