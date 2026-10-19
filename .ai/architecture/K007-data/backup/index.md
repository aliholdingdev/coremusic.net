---
node_id: backup
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - backup-kapsam
  - backup-bilesenler
  - backup-dogrulama
  - backup-ornekler
  - backup-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/migrations
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 286"
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php:14-20 — yedek talimatı"
  - "dev.mysql.com 8.4 Refman Bölüm 9 — 7 URL HTTP 200 (2026-10-15)"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "S-2 — operasyonel sahiplik: K007 backup mı K018-devops mu → SINIR AÇIK"
  - "RTO/RPO hedef değerleri → UNKNOWN"
group: mimari
---

# backup — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | backup |
| node_type | subdomain |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 286) |
| Durum | draft |

## Ne işe yarar?

Veri **kurtarma planı**: tam yedek (mysqldump) + artık yedek (binlog/PITR) + tatbikat
ölçütü. Politika K007'de yaşar; cron/pipeline K018-devops'a askıdadır (S-2).

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [backup-kapsam.md](backup-kapsam.md) | sınır: plan içi / operasyon dışı | mimari |
| 2 | [backup-bilesenler.md](backup-bilesenler.md) | mysqldump · binlog · tatbikat | mimari |
| 3 | [backup-dogrulama.md](backup-dogrulama.md) | D-B1…D-B6 iddia defteri | kanit |
| 4 | [backup-ornekler.md](backup-ornekler.md) | gerçek yedek talimatı + komut kalıpları | kod |
| 5 | [backup-web-arastirma.md](backup-web-arastirma.md) | MySQL 8.4 Bölüm 9 (7 URL) | kanit |

## §2 Bağımlılık

- Üst düğüm: [../index.md](../index.md) · [../data-master.md](../data-master.md)
- Kardeşler: migrations (yıkıcı DDL yedek ön-koşulu) · schema (dump kapsamı)

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi | Kademe-2 |
| mimari | `backup-kapsam.md` | Kapsam sınırı | Kademe-2 |
| mimari | `backup-bilesenler.md` | Bileşen planı | Kademe-2 |
| kanit | `backup-dogrulama.md` | İddia defteri | Kademe-2 |
| kod | `backup-ornekler.md` | Repo örnekleri | Kademe-2 |
| kanit | `backup-web-arastirma.md` | Resmi kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward