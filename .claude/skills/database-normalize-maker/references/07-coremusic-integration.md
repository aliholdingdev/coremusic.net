# 07. CoreMusic Entegrasyonu

## Genel Bakış

Bu dosya, `database-normalize-maker` skill'inin CoreMusic projesiyle nasıl entegre olduğunu tanımlar.

## PHP PDO Uyumluluğu

Veritabanı şeması, PHP 8.x katı tipleri (`declare(strict_types=1)`) ile uyumlu olmalıdır.

### PHP ↔ MySQL Veri Tipi Eşleştirmesi

| MySQL Tipi | PHP Tipi | Kullanım |
|------------|----------|----------|
| `BIGINT UNSIGNED` | `int` | ID, counter |
| `INT UNSIGNED` | `int` | Sayı |
| `TINYINT(1)` | `bool` | Boolean |
| `VARCHAR` | `string` | Metin |
| `TEXT` | `string` | Uzun metin |
| `DECIMAL(10,2)` | `string` (PDO) | Para birimi |
| `TIMESTAMP` | `string` (PDO) | Tarih/saat |
| `JSON` | `string` (PDO) | Esnek veri |

### PDO Kullanım Kuralları

```php
// DOĞRU: Prepared statement
$stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = :id');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(\PDO::FETCH_ASSOC);

// YASAK: Dinamik SQL
// $pdo->query("SELECT * FROM users WHERE id = $userId");
```

## İsimlendirme Standartları

| Öğe | Format | Örnek |
|-----|--------|-------|
| Tablolar | Çoğul, snake_case | `users`, `order_items` |
| Kolonlar | snake_case | `first_name`, `created_at` |
| Primary Key | `id` | `id BIGINT UNSIGNED` |
| Foreign Key | `{tablo}_id` | `user_id`, `product_id` |
| Index | `idx_{kolon}` | `idx_email` |
| Unique Index | `uk_{kolon}` | `uk_username` |
| FK Constraint | `fk_{çocuk}_{ana}` | `fk_orders_users` |
| Pivot Tablolar | Alfabetik sıra | `role_user`, `product_tag` |

## CoreMusic 11 BCNF Veritabanı

| # | Veritabanı | Amaç | Ana Tablolar |
|---|-----------|------|--------------|
| 1 | `coremusic_auth` | Kimlik doğrulama | users, sessions, tokens, roles, permissions |
| 2 | `coremusic_users` | Kullanıcı yönetimi | users, profiles, addresses, preferences |
| 3 | `coremusic_musics` | Müzik kataloğu | musics, artists, genres, tags |
| 4 | `coremusic_albums` | Albüm yönetimi | albums, album_tracks, album_artists |
| 5 | `coremusic_playlist` | Çalma listeleri | playlists, playlist_tracks |
| 6 | `coremusic_catalog` | Kataloglama | categories, collections, catalog_items |
| 7 | `coremusic_logs` | Log yönetimi | system_logs, error_logs, audit_logs |
| 8 | `coremusic_media` | Medya dosyaları | media_files, media_metadata, thumbnails |
| 9 | `coremusic_system` | Sistem ayarları | settings, configs, cache |
| 10 | `coremusic_social` | Sosyal özellikler | follows, likes, comments, reviews |
| 11 | `coremusic_wireless` | Kablosuz bağlantı | devices, connections, streams |

## Vault Entegrasyonu

| Kaynak | Kullanım |
|--------|----------|
| `.ai/brain.md` | Veritabanı mimarisi kararları |
| `.ai/ADR/` | İlgili ADR'ler |
| `.ai/.sql/` | Mevcut şema dosyaları |
| `.ai/index.md` | Ana katalog |

## Deployment Hedefleri

| Tier | Platform | Veritabanı |
|------|----------|------------|
| Tier1 | Windows | MySQL 9 |
| Tier2 | Linux | MySQL 9 |
| Tier3 | macOS | MySQL 9 |
| Tier4 | RPi5 | MySQL 9 |
| Tier5 | ReactOS | MySQL 9 (deneysel) |
