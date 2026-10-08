-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_musics`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_musics`;

CREATE TABLE `artist_credit` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `display_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gorunur birlesik ad ("Queen & David Bowie")',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_artist_credit_display` (`display_name`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Artist credit basligi (MusicBrainz hizali — karar #10)';

CREATE TABLE `artist_credit_names` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `artist_credit_id` binary(16) NOT NULL COMMENT 'FK -> artist_credit.id',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> artists.id',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Sira',
  `join_phrase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Birlestirme metni (" & ", ", ")',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_acn_credit_pos` (`artist_credit_id`,`position`),
  KEY `idx_acn_artist` (`artist_id`),
  CONSTRAINT `fk_acn_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_acn_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Artist credit bilesenleri (N:M + join_phrase — karar #10)';

CREATE TABLE `artist_members` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz üye tanımlayıcı',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanatçı/grup ID',
  `user_id` binary(16) DEFAULT NULL COMMENT 'Kullanıcı ID (CoreMusic üyesi ise)',
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member' COMMENT 'Grup içindeki rol',
  `joined_at` date DEFAULT NULL COMMENT 'Katılım tarihi',
  `left_at` date DEFAULT NULL COMMENT 'Ayrılma tarihi',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif üye mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_artist_members_artist` (`artist_id`) COMMENT 'Sanatçı filtresi',
  KEY `idx_artist_members_user` (`user_id`) COMMENT 'Kullanıcı filtresi',
  KEY `idx_artist_members_active` (`is_active`) COMMENT 'Aktif üye filtresi',
  CONSTRAINT `fk_artist_members_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanatçı üye tablosu';

CREATE TABLE `artists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz sanatçı tanımlayıcı',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sanatçı adı',
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu sanatçı adı',
  `biography` text COLLATE utf8mb4_unicode_ci COMMENT 'Sanatçı biyografisi',
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ülke kodu (ISO 3166-1 alpha-2)',
  `formed_year` int DEFAULT NULL COMMENT 'Kuruluş/yıl',
  `artist_type` enum('solo','band','duo','orchestra','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solo' COMMENT 'Sanatçı türü',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sanatçı fotoğrafı URL',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Doğrulanmış sanatçı mı?',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif sanatçı mı?',
  `monthly_listeners` int NOT NULL DEFAULT '0' COMMENT 'Aylık dinleyici sayısı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_artists_slug` (`slug`) COMMENT 'URL benzersizlik',
  KEY `idx_artists_name` (`name`) COMMENT 'İsim araması',
  KEY `idx_artists_country` (`country`) COMMENT 'Ülke filtresi',
  KEY `idx_artists_type` (`artist_type`) COMMENT 'Sanatçı türü filtresi',
  FULLTEXT KEY `idx_artists_fulltext` (`name`,`biography`) COMMENT 'Tam metin araması'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanatçılar tablosu';

CREATE TABLE `genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz tür tanımlayıcı',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tür adı',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu tür adı',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Tür açıklaması',
  `parent_id` binary(16) DEFAULT NULL COMMENT 'Üst tür ID (hiyerarşik)',
  `genre_level` int NOT NULL DEFAULT '0' COMMENT 'Hiyerarşi seviyesi (0: kök)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif tür mü?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_genres_slug` (`slug`) COMMENT 'URL benzersizlik',
  KEY `idx_genres_parent` (`parent_id`) COMMENT 'Üst tür ilişkisi',
  KEY `idx_genres_level` (`genre_level`) COMMENT 'Hiyerarşi seviyesi',
  CONSTRAINT `fk_genres_parent` FOREIGN KEY (`parent_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik türleri tablosu';

CREATE TABLE `music_artists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> musics.id (recording adayi)',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> artists.id',
  `artist_role_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_catalog.catalog_artist_roles.id (opsiyonel rol)',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Sira (1 = birincil sanatci)',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil sanatci bayragi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme (UTC)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_music_artists_pair` (`music_id`,`artist_id`),
  UNIQUE KEY `uk_music_artists_pos` (`music_id`,`position`),
  KEY `idx_music_artists_artist` (`artist_id`),
  KEY `idx_music_artists_role` (`artist_role_id`),
  CONSTRAINT `fk_music_artists_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_artists_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_artists_role` FOREIGN KEY (`artist_role_id`) REFERENCES `coremusic_catalog`.`catalog_artist_roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=' Sarki-artc N:M junction + rol + pozisyon (4NF — karar #3)';

CREATE TABLE `music_audio_features` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz özellik tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `danceability` decimal(4,3) DEFAULT NULL COMMENT 'Dans edilebilirlik (0.000-1.000)',
  `energy` decimal(4,3) DEFAULT NULL COMMENT 'Enerji seviyesi (0.000-1.000)',
  `valence` decimal(4,3) DEFAULT NULL COMMENT 'Pozitiflik (0.000-1.000)',
  `acousticness` decimal(4,3) DEFAULT NULL COMMENT 'Akustiklik (0.000-1.000)',
  `instrumentalness` decimal(4,3) DEFAULT NULL COMMENT 'Enstrümantallık (0.000-1.000)',
  `liveness` decimal(4,3) DEFAULT NULL COMMENT 'Canlılık (0.000-1.000)',
  `speechiness` decimal(4,3) DEFAULT NULL COMMENT 'Konuşma benzerliği (0.000-1.000)',
  `loudness` decimal(6,3) DEFAULT NULL COMMENT 'Ses şiddeti (dB)',
  `tempo` decimal(6,2) DEFAULT NULL COMMENT 'Tempo (BPM)',
  `key_confidence` decimal(4,3) DEFAULT NULL COMMENT 'Anahtar güvenilirliği (0.000-1.000)',
  `mode_confidence` decimal(4,3) DEFAULT NULL COMMENT 'Mod güvenilirliği (0.000-1.000)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_audio_features_music` (`music_id`) COMMENT 'Benzersiz müzik ilişkisi',
  CONSTRAINT `fk_music_audio_features_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik ses özellikleri tablosu';

CREATE TABLE `music_credits` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `credit_type` enum('composer','lyricist','producer','engineer','mixer','mastering','feature','other') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kredi türü',
  `person_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kişi adı (CoreMusic dışı ise)',
  `person_id` binary(16) DEFAULT NULL COMMENT 'Kişi ID (CoreMusic üyesi ise)',
  `credit_order` int NOT NULL DEFAULT '0' COMMENT 'Kredi sırası',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_music_credits_music` (`music_id`) COMMENT 'Müzik filtresi',
  KEY `idx_music_credits_type` (`credit_type`) COMMENT 'Kredi türü filtresi',
  KEY `idx_music_credits_person` (`person_id`) COMMENT 'Kişi filtresi',
  CONSTRAINT `fk_music_credits_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik kredileri tablosu';

CREATE TABLE `music_files` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz dosya tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `file_format` enum('mp3','flac','wav','aac','ogg','dsd','mqa') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya formatı',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya yolu',
  `file_size` bigint NOT NULL COMMENT 'Dosya boyutu (byte)',
  `bit_rate` int DEFAULT NULL COMMENT 'Bit hızı (kbps)',
  `sample_rate` int DEFAULT NULL COMMENT 'Örnekleme hızı (Hz)',
  `bit_depth` int DEFAULT NULL COMMENT 'Bit derinliği',
  `channels` int NOT NULL DEFAULT '2' COMMENT 'Kanal sayısı',
  `codec` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codec bilgisi',
  `checksum_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA256 checksum',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil dosya mı?',
  `quality_level` enum('low_128','medium_192','high_320','lossless','hi_res') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'high_320' COMMENT 'Kalite seviyesi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_music_files_music` (`music_id`) COMMENT 'Müzik ilişkisi',
  KEY `idx_music_files_format` (`file_format`) COMMENT 'Format filtresi',
  KEY `idx_music_files_quality` (`quality_level`) COMMENT 'Kalite filtresi',
  KEY `idx_music_files_primary` (`is_primary`) COMMENT 'Birincil dosya filtresi',
  CONSTRAINT `fk_music_files_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik dosyaları tablosu';

CREATE TABLE `music_genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `genre_id` binary(16) NOT NULL COMMENT 'Tür ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil tür mü?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_genres_unique` (`music_id`,`genre_id`) COMMENT 'Benzersiz müzik-tür çifti',
  KEY `idx_music_genres_genre` (`genre_id`) COMMENT 'Tür filtresi',
  CONSTRAINT `fk_music_genres_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_genres_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik-tür ilişkisi tablosu';

CREATE TABLE `music_lyrics` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz söz tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `lyrics_text` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Şarkı sözleri metni',
  `lyrics_language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en' COMMENT 'Dil kodu (ISO 639-1)',
  `lyrics_source` enum('manual','genius','musixmatch','ai_generated','verified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'Söz kaynağı',
  `synced_lyrics` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Senkronize söz var mı?',
  `synced_lyrics_data` json DEFAULT NULL COMMENT 'Senkronize söz verisi (LRC formatı)',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Doğrulanmış sözler mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_lyrics_music` (`music_id`) COMMENT 'Benzersiz müzik ilişkisi',
  KEY `idx_music_lyrics_lang` (`lyrics_language`) COMMENT 'Dil filtresi',
  FULLTEXT KEY `idx_music_lyrics_fulltext` (`lyrics_text`) COMMENT 'Söz araması',
  CONSTRAINT `fk_music_lyrics_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Şarkı sözleri tablosu';

CREATE TABLE `music_similar` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz benzerlik tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `similar_music_id` binary(16) NOT NULL COMMENT 'Benzer müzik ID',
  `similarity_score` decimal(3,2) NOT NULL COMMENT 'Benzerlik puanı (0.00-1.00)',
  `algorithm` enum('collaborative','content_based','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hybrid' COMMENT 'Benzerlik algoritması',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_similar_unique` (`music_id`,`similar_music_id`) COMMENT 'Benzersiz müzik çifti',
  KEY `idx_music_similar_target` (`similar_music_id`) COMMENT 'Hedef müzik filtresi',
  KEY `idx_music_similar_score` (`similarity_score`) COMMENT 'Benzerlik puanı sıralaması',
  CONSTRAINT `fk_music_similar_source` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_similar_target` FOREIGN KEY (`similar_music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Benzer müzik tablosu';

CREATE TABLE `music_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'Günlük çalma sayısı',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftalık çalma sayısı',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Aylık çalma sayısı',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `daily_downloads` int NOT NULL DEFAULT '0' COMMENT 'Günlük indirme sayısı',
  `weekly_downloads` int NOT NULL DEFAULT '0' COMMENT 'Haftalık indirme sayısı',
  `monthly_downloads` int NOT NULL DEFAULT '0' COMMENT 'Aylık indirme sayısı',
  `total_downloads` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme sayısı',
  `daily_shares` int NOT NULL DEFAULT '0' COMMENT 'Günlük paylaşım sayısı',
  `weekly_shares` int NOT NULL DEFAULT '0' COMMENT 'Haftalık paylaşım sayısı',
  `monthly_shares` int NOT NULL DEFAULT '0' COMMENT 'Aylık paylaşım sayısı',
  `total_shares` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam paylaşım sayısı',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Popülerlik puanı',
  `trending_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Trend puanı',
  `last_played_at` timestamp NULL DEFAULT NULL COMMENT 'Son çalınma zamanı',
  `stats_date` date NOT NULL COMMENT 'İstatistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_stats_unique` (`music_id`,`stats_date`) COMMENT 'Benzersiz müzik-tarih çifti',
  KEY `idx_music_stats_popularity` (`popularity_score`) COMMENT 'Popülerlik sıralaması',
  KEY `idx_music_stats_trending` (`trending_score`) COMMENT 'Trend sıralaması',
  CONSTRAINT `fk_music_stats_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik istatistikleri tablosu';

CREATE TABLE `music_tags` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz etiket tanımlayıcı',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `tag` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Etiket adı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_tags_unique` (`music_id`,`tag`) COMMENT 'Benzersiz müzik-etiket çifti',
  KEY `idx_music_tags_music` (`music_id`) COMMENT 'Müzik filtresi',
  KEY `idx_music_tags_tag` (`tag`) COMMENT 'Etiket araması',
  CONSTRAINT `fk_music_tags_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik etiketleri tablosu';

CREATE TABLE `music_videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `music_id` binary(16) NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mp4',
  `duration_seconds` int unsigned DEFAULT NULL,
  `resolution` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width_px` int unsigned DEFAULT NULL,
  `height_px` int unsigned DEFAULT NULL,
  `video_size_bytes` bigint unsigned DEFAULT NULL,
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `like_count` int unsigned NOT NULL DEFAULT '0',
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mv_music` (`music_id`),
  KEY `idx_mv_resolution` (`resolution`),
  KEY `idx_mv_official` (`is_official`),
  KEY `idx_mv_views` (`view_count` DESC),
  CONSTRAINT `fk_mv_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `music_videos_chk_1` CHECK ((`video_format` in (_utf8mb4'mp4',_utf8mb4'webm',_utf8mb4'mkv',_utf8mb4'avi'))),
  CONSTRAINT `music_videos_chk_2` CHECK ((`resolution` in (_utf8mb4'480p',_utf8mb4'720p',_utf8mb4'1080p',_utf8mb4'1440p',_utf8mb4'2160p',_utf8mb4'4320p')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik videoları — music video metadata';

CREATE TABLE `musics` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz müzik tanımlayıcı',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Şarkı başlığı',
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu başlık',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanatçı ID',
  `album_id` binary(16) DEFAULT NULL COMMENT 'Albüm ID (opsiyonel)',
  `genre_id` binary(16) DEFAULT NULL COMMENT 'Birincil tür ID',
  `track_number` int DEFAULT NULL COMMENT 'Albüm içindeki sıra numarası',
  `disc_number` int NOT NULL DEFAULT '1' COMMENT 'Disk numarası',
  `duration_sec` int DEFAULT NULL COMMENT 'Süre (saniye)',
  `release_date` date DEFAULT NULL COMMENT 'Yayın tarihi',
  `isrc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uluslararası Standart Kayıt Kodu',
  `upc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Evrensel Ürün Kodu',
  `bpm` int DEFAULT NULL COMMENT 'Tempo (darbe/dakika)',
  `key_signature` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Müzik anahtarı',
  `mood` enum('happy','sad','energetic','calm','dark','uplifting','neutral') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'neutral' COMMENT 'Ruh hali',
  `is_explicit` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'İçerik uyarısı',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Doğrulanmış müzik',
  `is_instrumental` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Enstrümantal parça mı?',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `like_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam beğeni sayısı',
  `download_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme sayısı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_musics_artist` (`artist_id`) COMMENT 'Sanatçı filtresi',
  KEY `idx_musics_album` (`album_id`) COMMENT 'Albüm filtresi',
  KEY `idx_musics_genre` (`genre_id`) COMMENT 'Tür filtresi',
  KEY `idx_musics_release` (`release_date`) COMMENT 'Yayın tarihi sıralaması',
  KEY `idx_musics_mood` (`mood`) COMMENT 'Ruh hali filtresi',
  FULLTEXT KEY `idx_musics_fulltext` (`title`) COMMENT 'Tam metin araması',
  CONSTRAINT `fk_musics_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_musics_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik parçaları tablosu';

CREATE TABLE `podcast_episodes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `show_id` int unsigned NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `audio_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mp3',
  `audio_size_bytes` bigint unsigned DEFAULT NULL,
  `duration_seconds` int unsigned DEFAULT NULL,
  `episode_number` int unsigned DEFAULT NULL,
  `season_number` int unsigned DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `play_count` int unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pe_slug` (`show_id`,`slug`),
  KEY `idx_pe_show` (`show_id`),
  KEY `idx_pe_publish` (`publish_date` DESC),
  KEY `idx_pe_status` (`status`),
  CONSTRAINT `fk_pe_show` FOREIGN KEY (`show_id`) REFERENCES `podcast_shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `podcast_episodes_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'published',_utf8mb4'scheduled',_utf8mb4'archived'))),
  CONSTRAINT `podcast_episodes_chk_2` CHECK ((`audio_format` in (_utf8mb4'mp3',_utf8mb4'aac',_utf8mb4'ogg',_utf8mb4'flac',_utf8mb4'wav')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast bölümleri — episodes, audio, scheduling';

CREATE TABLE `podcast_shows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `author_user_id` int unsigned NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `website_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `episode_count` int unsigned NOT NULL DEFAULT '0',
  `subscriber_count` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ps_slug` (`slug`),
  KEY `idx_ps_author` (`author_user_id`),
  KEY `idx_ps_category` (`category`),
  KEY `idx_ps_language` (`language`),
  KEY `idx_ps_status` (`status`),
  CONSTRAINT `podcast_shows_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'active',_utf8mb4'paused',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast gösterileri — series, episodes, subscriptions';

CREATE TABLE `podcast_subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `show_id` int unsigned NOT NULL,
  `notify_new_episode` tinyint(1) NOT NULL DEFAULT '1',
  `last_played_episode_id` int unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ps_pair` (`user_id`,`show_id`),
  KEY `idx_ps_sub_show` (`show_id`),
  CONSTRAINT `fk_psub_show` FOREIGN KEY (`show_id`) REFERENCES `podcast_shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast abonelikleri — user-show bindings';

CREATE TABLE `podcast_transcripts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `episode_id` int unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plain',
  `model_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence` decimal(3,2) DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_pair` (`episode_id`,`language`),
  KEY `idx_pt_language` (`language`),
  CONSTRAINT `fk_pt_episode` FOREIGN KEY (`episode_id`) REFERENCES `podcast_episodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast transkripsiyonları — AI-generated transcripts';

CREATE TABLE `radio_now_playing` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int unsigned NOT NULL,
  `music_id` binary(16) DEFAULT NULL COMMENT 'FK -> musics.id (snapshot kaynagi; NULL = bilinmiyor)',
  `track_title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `artist_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `album_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_seconds` int unsigned DEFAULT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rnp_station` (`station_id`),
  KEY `idx_rnp_music` (`music_id`),
  CONSTRAINT `fk_rnp_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rnp_station` FOREIGN KEY (`station_id`) REFERENCES `radio_stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo anlık yayın durumu — now playing info';

CREATE TABLE `radio_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int unsigned NOT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rs_schedule` (`station_id`,`day_of_week`,`start_time`),
  KEY `idx_rs_day` (`day_of_week`),
  KEY `idx_rs_time` (`start_time`),
  CONSTRAINT `fk_rsch_station` FOREIGN KEY (`station_id`) REFERENCES `radio_stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `radio_schedules_chk_1` CHECK (((`day_of_week` >= 0) and (`day_of_week` <= 6)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo yayın programları — weekly schedules';

CREATE TABLE `radio_stations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `genre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_mhz` decimal(6,2) DEFAULT NULL,
  `stream_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stream_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'icecast',
  `website_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bitrate_kbps` int unsigned DEFAULT NULL,
  `listener_count` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rs_slug` (`slug`),
  KEY `idx_rs_genre` (`genre`),
  KEY `idx_rs_country` (`country`),
  KEY `idx_rs_active` (`is_active`),
  CONSTRAINT `radio_stations_chk_1` CHECK ((`stream_type` in (_utf8mb4'icecast',_utf8mb4'shoutcast',_utf8mb4'hls',_utf8mb4'direct')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo istasyonları — stream URLs, genres, locations';

CREATE TABLE `recordings` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kayit adi',
  `duration_ms` int unsigned DEFAULT NULL COMMENT 'Sure (milisaniye) [EXTERNAL VERIFIED]',
  `isrc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3901 ISRC (musics.isrc tasinacak)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> artist_credit.id',
  `work_id` binary(16) DEFAULT NULL COMMENT 'FK -> works.id',
  `legacy_music_id` binary(16) DEFAULT NULL COMMENT 'TAŞIMA İZİ: musics.id (backfill FAZ 2b; FK YOK — iz korunur)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rec_isrc` (`isrc`),
  KEY `idx_rec_legacy` (`legacy_music_id`),
  KEY `idx_rec_credit` (`artist_credit_id`),
  KEY `idx_rec_work` (`work_id`),
  CONSTRAINT `fk_rec_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_work` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Recording — ozgun ses (MusicBrainz hizali — karar #10)';

-- VIEW: v_music_recording_map
CREATE ALGORITHM=UNDEFINED DEFINER=`Ali`@`%` SQL SECURITY DEFINER VIEW `coremusic_musics`.`v_music_recording_map` AS select `m`.`id` AS `music_id`,`r`.`id` AS `recording_id`,`m`.`title` AS `title`,`r`.`isrc` AS `isrc` from (`coremusic_musics`.`musics` `m` left join `coremusic_musics`.`recordings` `r` on((`r`.`legacy_music_id` = `m`.`id`)));

CREATE TABLE `video_playback_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `video_id` int unsigned NOT NULL,
  `last_position` int unsigned NOT NULL DEFAULT '0',
  `watch_count` int unsigned NOT NULL DEFAULT '1',
  `completion_pct` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vph_user` (`user_id`),
  KEY `idx_vph_video` (`video_id`),
  KEY `idx_vph_updated` (`updated_at` DESC),
  CONSTRAINT `fk_vph_video` FOREIGN KEY (`video_id`) REFERENCES `music_videos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Video oynatma geçmişi — playback position, watch count';

CREATE TABLE `video_subtitles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'srt',
  `is_auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vs_pair` (`video_id`,`language`),
  KEY `idx_vs_language` (`language`),
  CONSTRAINT `fk_vs_video` FOREIGN KEY (`video_id`) REFERENCES `music_videos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `video_subtitles_chk_1` CHECK ((`format` in (_utf8mb4'srt',_utf8mb4'vtt',_utf8mb4'ass')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Video altyazıları — subtitle tracks, multi-language';

CREATE TABLE `works` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Eser adi',
  `iswc` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISWC (opsiyonel, [VERIFY REQUIRED] format kontrolu)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> artist_credit.id (söz/müzik kredisi)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_works_iswc` (`iswc`),
  KEY `idx_works_title` (`title`(100)),
  KEY `fk_works_credit` (`artist_credit_id`),
  CONSTRAINT `fk_works_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Eser/Work varligi (MusicBrainz hizali — karar #10)';

SET FOREIGN_KEY_CHECKS = 1;
