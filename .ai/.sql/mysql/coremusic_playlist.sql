-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_playlist`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_playlist`;

CREATE TABLE `playlist_collaborators` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz işbirlikçi tanımlayıcı',
  `playlist_id` binary(16) NOT NULL COMMENT 'Çalma listesi ID',
  `user_id` binary(16) NOT NULL COMMENT 'Kullanıcı ID',
  `role` enum('viewer','editor','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewer' COMMENT 'İşbirlikçi rolü',
  `invited_by` binary(16) DEFAULT NULL COMMENT 'Davet eden kullanıcı ID',
  `invited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Davet zamanı',
  `accepted_at` timestamp NULL DEFAULT NULL COMMENT 'Kabul zamanı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_collaborators_unique` (`playlist_id`,`user_id`) COMMENT 'Benzersiz çalma listesi-kullanıcı çifti',
  KEY `idx_playlist_collaborators_user` (`user_id`) COMMENT 'Kullanıcı filtresi',
  KEY `idx_playlist_collaborators_role` (`role`) COMMENT 'Rol filtresi',
  CONSTRAINT `fk_playlist_collaborators_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi işbirlikçileri tablosu';

CREATE TABLE `playlist_followers` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz takipçi tanımlayıcı',
  `playlist_id` binary(16) NOT NULL COMMENT 'Çalma listesi ID',
  `user_id` binary(16) NOT NULL COMMENT 'Kullanıcı ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_followers_unique` (`playlist_id`,`user_id`) COMMENT 'Benzersiz çalma listesi-kullanıcı çifti',
  KEY `idx_playlist_followers_user` (`user_id`) COMMENT 'Kullanıcı filtresi',
  CONSTRAINT `fk_playlist_followers_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi takipçileri tablosu';

CREATE TABLE `playlist_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
  `playlist_id` binary(16) NOT NULL COMMENT 'Çalma listesi ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'Günlük çalma sayısı',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftalık çalma sayısı',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Aylık çalma sayısı',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `daily_followers` int NOT NULL DEFAULT '0' COMMENT 'Günlük takipçi sayısı',
  `total_followers` int NOT NULL DEFAULT '0' COMMENT 'Toplam takipçi sayısı',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Popülerlik puanı',
  `stats_date` date NOT NULL COMMENT 'İstatistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_stats_unique` (`playlist_id`,`stats_date`) COMMENT 'Benzersiz çalma listesi-tarih çifti',
  KEY `idx_playlist_stats_popularity` (`popularity_score`) COMMENT 'Popülerlik sıralaması',
  CONSTRAINT `fk_playlist_stats_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi istatistikleri tablosu';

CREATE TABLE `playlist_tracks` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
  `playlist_id` binary(16) NOT NULL COMMENT 'Çalma listesi ID',
  `music_id` binary(16) NOT NULL COMMENT 'Müzik ID',
  `position` int NOT NULL COMMENT 'Sıra numarası',
  `added_by` binary(16) DEFAULT NULL COMMENT 'Ekleyen kullanıcı ID',
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Eklenme zamanı',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'Not (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_tracks_unique` (`playlist_id`,`music_id`,`position`) COMMENT 'Benzersiz çalma listesi-müzik-sıra',
  KEY `idx_playlist_tracks_playlist_pos` (`playlist_id`,`position`) COMMENT 'Çalma listesi sıralaması',
  KEY `idx_playlist_tracks_music` (`music_id`) COMMENT 'Müzik filtresi',
  CONSTRAINT `fk_playlist_tracks_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi parçaları tablosu';

CREATE TABLE `playlists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz çalma listesi tanımlayıcı',
  `user_id` binary(16) NOT NULL COMMENT 'Oluşturan kullanıcı ID',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Çalma listesi adı',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Açıklama',
  `cover_art_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kapak görseli URL',
  `visibility` enum('public','private','unlisted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'Görünürlük',
  `playlist_type` enum('user','ai','auto_generated','radio','curated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'Çalma listesi türü',
  `is_editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Düzenlenebilir mi?',
  `total_tracks` int NOT NULL DEFAULT '0' COMMENT 'Toplam parça sayısı',
  `total_duration_sec` int NOT NULL DEFAULT '0' COMMENT 'Toplam süre (saniye)',
  `follow_count` int NOT NULL DEFAULT '0' COMMENT 'Takipçi sayısı',
  `like_count` int NOT NULL DEFAULT '0' COMMENT 'Beğeni sayısı',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_playlists_user` (`user_id`) COMMENT 'Kullanıcı filtresi',
  KEY `idx_playlists_visibility` (`visibility`) COMMENT 'Görünürlük filtresi',
  KEY `idx_playlists_type` (`playlist_type`) COMMENT 'Tür filtresi',
  FULLTEXT KEY `idx_playlists_fulltext` (`name`,`description`) COMMENT 'Tam metin araması'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listeleri tablosu';

SET FOREIGN_KEY_CHECKS = 1;
