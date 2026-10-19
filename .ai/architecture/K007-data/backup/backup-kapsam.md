---
node_id: "K007-backup-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/migrations"
evidence:
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php:14-20 — yıkıcı migration öncesi yedek talimatı (2026-10-13 okundu)"
  - ".ai/.sql/CLAUDE.md §3 — full_dump.php ile 20 dump · birebir canlı dump (2026-10-07)"
  - "dev.mysql.com/doc/refman/8.4/en/backup-and-recovery.html — HTTP 200 (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; 2 repo kanıtı + 4 MySQL 8.4 resmi URL okundu"
open_questions:
  - "S-2 — operasyonel sahiplik K007 mi K018-devops mu → SINIR AÇIK"
  - "RTO/RPO hedefleri tanımsız → UNKNOWN"
  - "Kurtarma tatbikatı yapılmış mı → repo'da kanıt YOK"
group: mimari
---

# backup — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Yedek **politikası** tasarımı (tam/yedek/binlog) | MySQL 8.4 §9 (backup-and-recovery) |
| 2 | PITR (point-in-time recovery) planı | dev.mysql.com §9.5.1/9.5.2 (2026-10-15 okundu) |
| 3 | Yıkıcı migration öncesi yedek ön-koşulu | `2026-10_07_drop_…migration.php:14-20` |
| 4 | KVKK imha ↔ yedek izi ayrımı | soft-delete `deleted_at` (dump'lar) |
| 5 | RTO/RPO ölçüt tanımı (henüz değer yok) | UNKNOWN |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Yedek **çalıştırma** (cron, pipeline) | K17-devops | operasyon ≠ plan (S-2) |
| Fiziksel disk/hedef depolama | K000-isletim-sistemi | donanım sahipliği |
| Şifreli yedek anahtarı | K009-security | anahtar sahibi security |
| 18 DB'nin dump içeriği | `.ai/.sql/mysql/` | veri SSOT orada |

## §3 Sınır cümlesi

backup, K007'nin **kurtarma planı**dır: "veri kaybolursa ne geri gelir, ne kadar sürede
geri gelir?" sorusunu yanıtlar. Cron'u çalıştırmaz — planı yazar ve tatbikat ölçütünü tanımlar.

---

**Authority:** Bayram Ali / Vault Steward