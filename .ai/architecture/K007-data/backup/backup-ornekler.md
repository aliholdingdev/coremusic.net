---
node_id: "K007-backup-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/migrations"
evidence:
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php:14-20 — yedek talimat bloğu (2026-10-13 okundu)"
  - ".ai/.sql/mysql/coremusic_musics.sql:1-9 — dump başlığı (canlı dump · 2026-10-07)"
validation: "2026-10-15 — 2 gerçek repo örneği + 1 resmi komut kalıbı"
open_questions: []
group: kod
---

# backup — Örnekler (K007)

## §1 Repo gerçeği: yıkıcı migration yedek talimatı

Dosya: `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php`
(satır 14-20, docblock):

```php
// YIKICI — geri alınamaz (DROP TABLE).
// ÖN KOŞUL: mysqldump ile yedek al:
//   mysqldump coremusic_api api_keys > api_keys_backup_$(date +%F).sql
// ADR-014: forward-only — down migration yok.
```

> Not: blok **talimat**tır, çalışan kod değildir — migration CLI repo'da yok (VERIFICATION REQUIRED).

## §2 Repo gerçeği: dump başlığı (`.ai/.sql/mysql/`)

`coremusic_musics.sql:1-9`:

```sql
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz
```

## §3 Resmi kalıp (MySQL 8.4 §6.6.9.3 · 2026-10-15 okundu)

```bash
mysqldump --host=db --all-databases --events --routines --source-data=2 > dump.sql
mysqlbinlog --read-from-remote-server --host=db --raw --stop-never binlog.000999
# kurtarma:
mysql -u root -p < dump.sql
mysqlbinlog --start-position=27284 binlog.001002 binlog.001003 | mysql -u root -p
```

---

**Authority:** Bayram Ali / Vault Steward