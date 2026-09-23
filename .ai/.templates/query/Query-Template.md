---
title: "Query Template (SQL) — Çekirdek Sorgu Şablonu"
type: template
category: query
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# Query Template (SQL) — Çekirdek Sorgu Şablonu

**Zorunlu Bağlantılar:** [[.ai/index]] · [[.ai/brain]] · [[.ai/.templates/index]] · [[.ai/.sql/index]]

---

## §1 Amaç ve Kapsam

Bu şablon, CoreMusic kod tabanındaki **ham SQL sorgularının** tek tip yazım standardını tanımlar. MySQL 8 (utf8mb4_unicode_ci, InnoDB) için geçerlidir.

| Kural | Değer | Kanıt |
|---|---|---|
| Sorgu yeri | `Database.php` içinde hazır ifadeler + `->bind()` parametre bağlama | `shared/core/Database.php` (248 satır) |
| Önerilen prepared statement | `$stmt->prepare()` + `bind_param` | `Database.php::query()`, `execute()` |
| Şema kaynağı | `.ai/.sql/` (18 şema dosyası) | `ls .ai/.sql/*.sql` |
| Migration gerçek yolu | `shared/database/migrations/*.php` (phinx YOK) | `ls shared/database/migrations/` |
| Binary log | Kayıtlı (server tuned) | `.ai/infrastructure/mysql-server-tuning.md` |

```php
// Database.php — gerçek bağlama örneği
$stmt = Database::query("SELECT * FROM users WHERE id = ?", "i", [$id]);
$row  = Database::fetch($stmt);
```

---

## §2 Frontmatter / Değişkenler

SQL dosyaları frontmatter taşımaz; şablon değişkenleri migration PHP dosyalarında `{{VARIABLE}}` biçimindedir.

| Değişken | Açıklama | Örnek |
|---|---|---|
| `{{TABLE_NAME}}` | Hedef tablo (snake_case) | `oauth_connections` |
| `{{MIGRATION_ID}}` | Tarih-saat damgası | `20260907000001` |
| `{{COLUMN_NAME}}` | Sütun adı | `access_token` |
| `{{INDEX_NAME}}` | İndeks adı (tablo_amaç) | `idx_users_email` |

---

## §3 Çekirdek SQL Şablonları

### §3.1 CREATE TABLE (kanıt: oauth_connections_migration.php)

```sql
CREATE TABLE IF NOT EXISTS `{{TABLE_NAME}}` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `provider` VARCHAR(50) NOT NULL,
  `access_token` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_provider` (`user_id`, `provider`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_{{TABLE_NAME}}_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### §3.2 ALTER TABLE (kanıt: oauth_states + settings alter)

```sql
ALTER TABLE `{{TABLE_NAME}}`
  ADD COLUMN `{{COLUMN_NAME}}` VARCHAR(255) DEFAULT NULL AFTER `{{AFTER_COLUMN}}`,
  ADD KEY `{{INDEX_NAME}}` (`{{COLUMN_NAME}}`);

ALTER TABLE `settings`
  ADD COLUMN `settings_key` VARCHAR(100) NOT NULL,
  ADD COLUMN `settings_value` TEXT,
  ADD UNIQUE KEY `uniq_settings_key` (`settings_key`);
```

### §3.3 CREATE INDEX

```sql
CREATE INDEX `{{INDEX_NAME}}` ON `{{TABLE_NAME}}` (`{{COLUMN_NAME}}`);

CREATE INDEX `idx_content_type_status_published` ON `contents` (`type`, `status`, `published_at`);
```

### §3.4 TRUNCATE (dikkatli — sadece seed/temizlik)

```sql
TRUNCATE TABLE `{{TABLE_NAME}}`;  -- FK varsa önce SET FOREIGN_KEY_CHECKS=0
```

### §3.5 SELECT — JOIN + WHERE + ORDER (kanıt: Database.php sorguları)

```sql
SELECT
    c.id, c.title, c.slug, c.status, c.published_at,
    u.username AS author
FROM `contents` c
INNER JOIN `users` u ON u.id = c.user_id
WHERE c.status = ?
  AND c.published_at <= NOW()
ORDER BY c.published_at DESC
LIMIT ? OFFSET ?;
```

```php
$stmt = Database::query($sql, "sii", [$status, $limit, $offset]);
$rows = Database::fetchAll($stmt);
```

### §3.6 INSERT (ON DUPLICATE KEY — OAuth tabloları deseni)

```sql
INSERT INTO `{{TABLE_NAME}}` (`user_id`, `provider`, `access_token`, `refresh_token`)
VALUES (?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
  `access_token` = VALUES(`access_token`),
  `refresh_token` = VALUES(`refresh_token`),
  `updated_at` = CURRENT_TIMESTAMP;
```

### §3.7 UPDATE + DELETE (idempotent, prepared)

```sql
-- UPDATE: her zaman WHERE ile (toplu güncelleme yasak)
UPDATE `{{TABLE_NAME}}`
SET `{{COLUMN_NAME}}` = ?, `updated_at` = CURRENT_TIMESTAMP
WHERE `id` = ?;

-- DELETE: FK cascade farkında ol
DELETE FROM `{{TABLE_NAME}}` WHERE `user_id` = ? AND `provider` = ?;
```

### §3.8 CTE / Window (MySQL 8 — event/istatistik sorguları)

```sql
WITH Ranked AS (
  SELECT user_id, COUNT(*) AS play_count,
         ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY played_at DESC) AS rn
  FROM `play_history`
  GROUP BY user_id
)
SELECT * FROM Ranked WHERE rn = 1;
```

---

## §4 Migration PHP İskeleti (gerçek format)

`shared/database/migrations/*.php` dosyaları `getQuerys()` ile `$queries` dizisi döndürür:

```php
<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class {{MIGRATION_ID}} extends AbstractMigration
{
    public function up(): void
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `{{TABLE_NAME}}` ( ... ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            'CREATE INDEX `{{INDEX_NAME}}` ON `{{TABLE_NAME}}` (`{{COLUMN_NAME}}`)',
        ];
        foreach ($queries as $query) {
            $this->execute($query);
        }
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS `{{TABLE_NAME}}`');
    }
}
```

⚠️ `composer.json`'larda **phinx paketi YOK** — bu iskelet .ai/brain.md içindeki göç hedefidir; çalıştırma aracı doğrulanana kadar `VERIFICATION REQUIRED`.

---

## §5 MySQL Ortam & Pencere

| Ortam | Sunucu | Çıktı | Kanıt |
|---|---|---|---|
| Yerel (XAMPP/Laragon) | MariaDB/MySQL | CLI `mysql -u root -p` | geliştirme |
| Production | TUNED MySQL 8 | kayıtlı `.my.cnf`/tuning belgesi | `.ai/infrastructure/mysql-server-tuning.md` |

| Parametre | Değer (tuned) |
|---|---|
| `innodb_buffer_pool_size` | server tuning belgesinde |
| `general_log` | KAPALI (prod) |
| `slow_query_log` | AÇIK, eşik kayıtlı |
| Charset | `utf8mb4` / `utf8mb4_unicode_ci` zorunlu |

---

## §6 Doğrulama Checklist

| # | Adım | Komut / Aksiyon |
|---|---|---|
| 1 | Sözdizimi | `mysql -e "EXPLAIN <sorgu>"` |
| 2 | Plan | `EXPLAIN ANALYZE` (MySQL 8) — full scan varsa index ekle |
| 3 | Şema tutarlılığı | `.ai/.sql/` dosyası ile canlı şema karşılaştır |
| 4 | Göç adımı | migration `up()` + `down()` iki yönlü |
| 5 | Güvenlik | tüm user input `?` parametreli — string birleştirme YASAK |
| 6 | Rollback | her migration `down()` ile geri alınabilir |

| Hata | Neden | Çözüm |
|---|---|---|
| `Unknown column` | migration çalışmamış | `shared/database/migrations/` içinde up() çalıştır |
| `Incorrect string value` | latin1 kolasyon | `utf8mb4_unicode_ci` zorunla |
| `Deadlock` | eşzamanlı UPDATE | sıra: PK üzerinde artan sıralama |

---

## §7 Şema Envanteri (kanıt: `.ai/.sql/` — 18 dosya)

| # | SQL dosyası | Domain | Sık kullanım sorgusu tipi |
|---|---|---|---|
| 1 | `users.sql` | kimlik | SELECT by email / INSERT register |
| 2 | `contents.sql` | içerik | JOIN listeleme + sayfalama |
| 3 | `playlists.sql` | müzik | many-to-many JOIN |
| 4 | `comments.sql` | topluluk | INSERT + tree SELECT |
| 5 | `oauth_connections.sql` | auth | ON DUPLICATE KEY |
| 6 | `oauth_states.sql` | auth | kısa ömürlü INSERT/DELETE |
| 7 | `settings.sql` | yapılandırma | key-value SELECT |
| 8 | `events.sql` | etkinlik | tarih penceresi WHERE |
| 9-18 | (kalan şemalar) | çeşitli | `.ai/.sql/index.md` envanteri |

> DRY: tam envanter `.ai/.sql/index.md`'dedir; bu tablo şablon kullanım sıklığı içindir.

### §7.1 Ortak JOIN Kalıpları

```sql
-- Kullanıcı + profil
SELECT u.id, u.username, p.display_name
FROM users u LEFT JOIN profiles p ON p.user_id = u.id
WHERE u.status = 'active';

-- İçerik + yazar + etiket sayımı
SELECT c.id, c.title, COUNT(t.id) AS tag_count
FROM contents c
LEFT JOIN content_tags ct ON ct.content_id = c.id
LEFT JOIN tags t ON t.id = ct.tag_id
GROUP BY c.id
HAVING tag_count > 0;

-- Çalma listesi + şarkı sırası
SELECT pl.title, t.title AS track_title, lp.position
FROM playlists pl
JOIN playlist_tracks lp ON lp.playlist_id = pl.id
JOIN tracks t ON t.id = lp.track_id
WHERE pl.user_id = ?
ORDER BY lp.position ASC;
```

### §7.2 Parçalama (Pagination)

```sql
-- Keyset (timeline için önerilen)
SELECT id, title FROM contents
WHERE id < ?
ORDER BY id DESC
LIMIT 20;

-- Offset (yönetim panelleri)
SELECT id, title FROM contents
ORDER BY created_at DESC
LIMIT 20 OFFSET ?;
```

| Yöntem | Avantaj | Risk |
|---|---|---|
| Keyset | derin sayfada stabil + hızlı | sıralama anahtarını kaybetme |
| Offset | basit | derin OFFSET'te yavaşlık (index tarama) |

---

## §8 İndeks Stratejisi

| Sütun tipi | İndeks | Not |
|---|---|---|
| UNIQUE alan (email, slug) | `UNIQUE KEY` | tekillik DB seviyesinde |
| FK sütunlar | standart `KEY` | JOIN hızı + cascade temizlik |
| Filtre + sıralama | bileşik index `(col1, col2)` | WHERE col1 ORDER BY col2 |
| Sadece okunan rapor | covering index `(col1, col2, col3)` | `Using index` hedefi |
| Düşük kardinalite (status) | tek başına YOK | bileşikte ikinci sırada |

```sql
-- bileşik index örneği
CREATE INDEX idx_contents_type_status_published
  ON contents (type, status, published_at);
```

| Ölçüm | Komut |
|---|---|
| Plan doğruluğu | `EXPLAIN SELECT ...` |
| Gerçek maliyet | `EXPLAIN ANALYZE SELECT ...` (MySQL 8.0.18+) |
| Kullanılmayan indeks | `sys.schema_unused_indexes` |

---

## §9 Güvenlik Kuralları (SQL Injection)

| Kural | Doğru | Yanlış |
|---|---|---|
| Parametre | `?` + `bind()` | `"SELECT ... " . $girdi` |
| LIKE escape | `LIKE ? ESCAPE '\\'` + `%`/`_` kaçış | ham kullanıcı LIKE kalıbı |
| Sıralama sütunu | whitelist (izinli kolon adları) | `ORDER BY $_GET['s']` |
| LIMIT | bağlı parametre (int cast) | ham string |
| Hata mesajı | log'a ayrıntı, kullanıcıya genel | ham SQL hatası göstermek |

```php
// Database.php deseni
$stmt = Database::query(
    "SELECT id FROM users WHERE email = ? AND status = ?",
    "ss",
    [$email, $status]
);
```

---

## §10 Performans Günlüğü

| Belirti | Muhtemel neden | Çözüm |
|---|---|---|
| Full table scan | eksik index | `EXPLAIN` → index ekle |
| `Using filesort` | sıralama index dışı | bileşik index |
| `Using temporary` | GROUP/DISTINCT index dışı | indeksli grup anahtarı |
| N+1 benzeri çok çağrı | döngü içinde SELECT | tek JOIN / IN listesi |
| Yavaş LIKE başa | wildcard başlangıç | FULLTEXT / prefix LIKE |

```sql
-- yavaş: LIKE '%term%'
-- alternatif: FULLTEXT
ALTER TABLE contents ADD FULLTEXT INDEX ft_title_body (title, body);
SELECT id, title FROM contents WHERE MATCH(title, body) AGAINST(? IN BOOLEAN MODE);
```

---

## §11 Doğrulama Checklist (uzantı)

| # | Adım | Beklenen |
|---|---|---|
| 7 | Şema drift | `.ai/.sql/` ↔ canlı `SHOW CREATE TABLE` tutarlı |
| 8 | İndeks kullanımı | hedef sorgularda `Using index` / sabit satır |
| 9 | Backup | deploy öncesi dump: `mysqldump --single-transaction` |
| 10 | Gizli veri | sonuç setinde token/şifre sütunu yok |

| Gizli alan | Aksiyon |
|---|---|
| `password_hash` | dış API'ye ASLA dönme |
| `access_token` / `refresh_token` | maskele / select listeye alma |
| `secret` sütunları | REDACTED politikası |

---

## §12 Şema Oluşturma Galerisi (`.ai/.sql/` pratikleri)

### §12.1 Kalıp: kullanıcı & kimlik

```sql
-- users tabanı (utf8mb4 zorunlu)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `status` ENUM('active','passive','banned') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`),
  UNIQUE KEY `uniq_users_username` (`username`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### §12.2 Kalıp: içerik + etiket bağları

```sql
CREATE TABLE IF NOT EXISTS `content_tags` (
  `content_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`, `tag_id`),
  KEY `idx_tag_id` (`tag_id`),
  CONSTRAINT `fk_ct_content` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### §12.3 Kalıp: idempotent seed (tekrar çalıştırılabilir)

```sql
INSERT INTO `settings` (`settings_key`, `settings_value`)
VALUES ('site_title', 'CoreMusic')
ON DUPLICATE KEY UPDATE `settings_value` = VALUES(`settings_value`);
```

### §12.4 Kalıp: geçici tablo / ara sorgu

```sql
CREATE TEMPORARY TABLE tmp_active_users AS
  SELECT id, email FROM users WHERE status = 'active';
SELECT COUNT(*) FROM tmp_active_users;
-- oturum sonunda otomatik düşer
```

---

## §13 Akış Diyagramı (şablondan üretime)

```
İHTİYAÇ (yeni alan/tablo)
   │
   ▼
§3 şablonu seç (CREATE/ALTER/INDEX)
   │
   ▼
.ai/.sql/{domain}.sql güncelle  ← SSOT şema
   │
   ▼
shared/database/migrations/{{MIGRATION_ID}}.php (up/down)
   │
   ▼
§6 doğrulama (EXPLAIN + up/down provası)
   │
   ▼
staging → prod (mysqldump backup zorunlu)
```

---

## §14 İdame Checklist (aylık)

| # | Kontrol | Sinyal |
|---|---|---|
| 11 | Büyüyen tablo (`information_schema` rows) | İndeks/parçalama planı |
| 12 | Slow query log tarama | §10 masasına al |
| 13 | Kullanılmayan index raporu (`sys`) | DROP öner |
| 14 | `.ai/.sql/` ↔ canlı drift | SSOT güncelle |
| 15 | Backup restore provası | RTO doğruluğu |

---

## §15 Hazır Sorgu Kütüphanesi (hızlı kopyala)

### §15.1 Günlük operasyonel sorgular

```sql
-- Son N içerik (dashboard)
SELECT id, title, status, updated_at FROM contents
ORDER BY updated_at DESC LIMIT 10;

-- Kullanıcı arama (prefix LIKE — index dostu)
SELECT id, username, email FROM users
WHERE username LIKE 'bay%' COLLATE utf8mb4_unicode_ci
ORDER BY username LIMIT 20;

-- Sayı aggregation (aylık istatistik)
SELECT DATE_FORMAT(published_at, '%Y-%m') AS ym, COUNT(*) AS total
FROM contents WHERE status = 'published'
GROUP BY ym ORDER BY ym DESC;

-- Çift kayıt temizliği (dup email raporu)
SELECT email, COUNT(*) AS c FROM users
GROUP BY email HAVING c > 1;
```

### §15.2 Bakım sorguları

```sql
-- Tablo büyüklüğü
SELECT table_name, ROUND((data_length + index_length) / 1024 / 1024, 2) AS mb
FROM information_schema.tables
WHERE table_schema = DATABASE()
ORDER BY mb DESC;

-- Son lock beklemeleri
SELECT * FROM sys.innodb_lock_waits LIMIT 10;

-- İndeks kullanımı (sıcak/soğuk)
SELECT * FROM sys.schema_index_statistics
WHERE table_schema = DATABASE() ORDER BY rows_selected DESC;
```

### §15.3 Güvenli ALTER sırası (prod — online hedef)

```sql
-- 1) Yeni kolon nullable → 2) backfill → 3) NOT NULL + index
ALTER TABLE contents ADD COLUMN `slug` VARCHAR(190) NULL;
-- backfill partileri (LIMIT'li UPDATE)
UPDATE contents SET slug = id WHERE slug IS NULL LIMIT 1000;  -- tekrarla
ALTER TABLE contents ADD UNIQUE KEY `uniq_contents_slug` (`slug`);
```

| Sıra kuralı | Neden |
|---|---|
| ADD NULL → backfill → NOT NULL | kilit (metadata lock) süresi |
| İndeks ayrı adımda | online DDL destek kontrolü |
| Backup önce | §6 #9 |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
