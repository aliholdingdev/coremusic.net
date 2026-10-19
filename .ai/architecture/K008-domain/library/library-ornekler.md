---
node_id: "K008-library-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-library-kapsam"
evidence:
  - ".ai/.sql/mysql/coremusic_user.sql satır 35–57 · 59–69 · 83–102 (üç CREATE TABLE birebir okundu)"
  - "2026-10-15 grep: repo PHP/JS `favorite|user_favorites` = 0 isabet → uygulama örneği YOK (bkz [[library-dogrulama]] D-L6)"
validation: "2026-10-15 — 3 örnek (SQL ×3); her satır disk'ten birebir kopyalandı"
open_questions:
  - "PHP/JS favori servis/toggle örneği → kanıt YOK → UNKNOWN (D-L11)"
  - "Dinleme geçmişi/indirme uygulama kodu taraması yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# library — Örnekler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Bu dalda uygulama (PHP/JS) örneği **yok** — [[library-dogrulama]] D-L6 repo
> taraması 0 isabet. Üç örnek de **şema örneğidir** ve `coremusic_user.sql`
> satırlarından birebir kopyalanmıştır.

## §1 ÖRNEK 1 — SQL: user_favorites (polymorphic favori · satır 59–69)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 59-69
CREATE TABLE `user_favorites` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
  `item_type` enum('music','album','playlist','artist','podcast') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Favorited item type',
  `item_id` binary(16) NOT NULL COMMENT 'Favorited item ID (polymorphic)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Favorite timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_favorites_unique` (`user_id`,`item_type`,`item_id`) COMMENT 'Unique favorite per user-item pair',
  KEY `idx_favorites_item` (`item_type`,`item_id`) COMMENT 'Item popularity lookup',
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User favorites — polymorphic item references';
```

**Ne kanıtlıyor:** favori tekilliği DB'de zorlanıyor (satır 66), referans polymorphic
(satır 62–63) — yani "koleksiyon" kavramı tek tabloda 5 varlık tipine yayılmış.

## §2 ÖRNEK 2 — SQL: user_downloads (indirilen koleksiyon · satır 35–57)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 35-57
CREATE TABLE `user_downloads` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
  `download_status` enum('pending','downloading','completed','failed','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT 'Download status',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Local file path',
  `file_size` bigint DEFAULT NULL COMMENT 'File size in bytes',
  `file_format` enum('mp3','flac','wav','aac','ogg') COLLATE utf8mb4_unicode_ci DEFAULT 'flac' COMMENT 'Downloaded file format',
  `quality` enum('128','192','320','lossless') COLLATE utf8mb4_unicode_ci DEFAULT 'lossless' COMMENT 'Audio quality tier',
  `downloaded_at` timestamp NULL DEFAULT NULL COMMENT 'Download completion timestamp',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Download expiration timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_downloads_unique` (`user_id`,`music_id`) COMMENT 'One download per user per track',
  KEY `idx_downloads_user` (`user_id`) COMMENT 'User downloads lookup',
  KEY `idx_downloads_music` (`music_id`) COMMENT 'Track download lookup',
  KEY `idx_downloads_status` (`download_status`) COMMENT 'Status filtering',
  CONSTRAINT `fk_downloads_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_downloads_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User downloads — status, format, file tracking';
```

**Ne kanıtlıyor:** indirme bir *süreçtir* — durum makinesi enum'u (satır 39), format/kalite
tiersi (satır 42–43), çevrimdışı erişim süresi (`expires_at`, satır 45) ve kullanıcı-başına
tekillik (satır 51) şemada gömülü.

## §3 ÖRNEK 3 — SQL: user_listening_history (dinleme geçmişi · satır 83–102)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_user.sql · satır 83-102
CREATE TABLE `user_listening_history` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
  `playlist_id` binary(16) DEFAULT NULL COMMENT 'FK → playlist if from playlist context',
  `listen_duration_sec` int NOT NULL COMMENT 'Total listen duration in seconds',
  `listen_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of track listened (0.00–100.00)',
  `started_at` timestamp NOT NULL COMMENT 'Playback start timestamp',
  `ended_at` timestamp NULL DEFAULT NULL COMMENT 'Playback end timestamp',
  `device_type` enum('desktop','mobile','tablet','car','studio','home') COLLATE utf8mb4_unicode_ci DEFAULT 'desktop' COMMENT 'Device used for listening',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_history_user` (`user_id`) COMMENT 'User history lookup',
  KEY `idx_history_music` (`music_id`) COMMENT 'Track play count lookup',
  KEY `idx_history_started` (`started_at`) COMMENT 'Time-based history queries',
  KEY `idx_history_user_started` (`user_id`,`started_at`) COMMENT 'User chronological history',
  CONSTRAINT `fk_history_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Listening history — tracks, duration, device context';
```

**Ne kanıtlıyor:** geçmiş kaydı "çaldı mı" değil, *ne kadar dinledi* sorusunu yanıtlar
(satır 88–89: süre + yüzde), cihaz bağlamını taşır (satır 92) ve `playlist_id`
bağlamıyla playlist ile sınır komşusudur (satır 87).

## §4 ÖRNEK YOK (negatif kayıt)

| Aranan örnek türü | Durum | Kanıt |
|---|---|---|
| Favori toggle servisi (PHP) | **YOK** | [[library-dogrulama]] D-L6 — grep `favorite\|user_favorites` = 0 isabet (2026-10-15) |
| Favori UI kodu (JS) | **YOK** | aynı |
| Dinleme geçmişi yazıcısı (PHP/JS) | **UNKNOWN** | bu sözcükler için repo taraması yapılmadı → ⚠️ VERIFICATION REQUIRED |
| İndirme yöneticisi (PHP/JS) | **UNKNOWN** | aynı |

**Kardeş dosyalar:** [[library-kapsam]] · [[library-dogrulama]] · [[library-web-arastirma]] · [[library-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
