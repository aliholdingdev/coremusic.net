-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_user`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_user`;

CREATE TABLE `playback_queue` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
  `position` int NOT NULL COMMENT 'Queue position (0-based)',
  `source` enum('user','ai','radio','podcast') COLLATE utf8mb4_unicode_ci DEFAULT 'user' COMMENT 'Queue source type',
  `source_id` binary(16) DEFAULT NULL COMMENT 'Source entity ID (playlist, radio station, etc.)',
  `is_playing` tinyint(1) DEFAULT '0' COMMENT 'Currently playing flag',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_queue_user_position` (`user_id`,`position`) COMMENT 'User queue ordered retrieval',
  KEY `idx_queue_music` (`music_id`) COMMENT 'Track queue lookup',
  CONSTRAINT `fk_queue_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_queue_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Playback queue — per-user track queue with ordering';

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

CREATE TABLE `user_follows` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `follower_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id — follower',
  `following_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id — followed user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Follow timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_follows_unique` (`follower_id`,`following_id`) COMMENT 'Unique follow relationship',
  KEY `idx_follows_following` (`following_id`) COMMENT 'Followers of user lookup',
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_follows_following` FOREIGN KEY (`following_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User follow relationships — social graph';

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

CREATE TABLE `user_preferences` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id (unique)',
  `device_type` enum('desktop','mobile','tablet','car','studio','home') COLLATE utf8mb4_unicode_ci DEFAULT 'desktop' COMMENT 'Target device type',
  `theme` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'default' COMMENT 'Theme identifier',
  `audio_quality` enum('low','medium','high','lossless') COLLATE utf8mb4_unicode_ci DEFAULT 'high' COMMENT 'Default audio quality',
  `auto_play` tinyint(1) DEFAULT '1' COMMENT 'Auto-play next track',
  `crossfade_duration` int DEFAULT '0' COMMENT 'Crossfade duration in seconds',
  `normalize_audio` tinyint(1) DEFAULT '1' COMMENT 'Audio normalization enabled',
  `explicit_content` tinyint(1) DEFAULT '1' COMMENT 'Allow explicit content',
  `autoplay_recommendations` tinyint(1) DEFAULT '1' COMMENT 'Auto-play AI recommendations',
  `download_over_wifi_only` tinyint(1) DEFAULT '1' COMMENT 'WiFi-only download restriction',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_preferences_user` (`user_id`) COMMENT 'Unique user preferences lookup',
  KEY `idx_preferences_device` (`device_type`) COMMENT 'Device type filtering',
  CONSTRAINT `fk_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User preferences — audio, theme, playback settings';

CREATE TABLE `user_profiles` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id (unique)',
  `bio` text COLLATE utf8mb4_unicode_ci COMMENT 'User biography',
  `date_of_birth` date DEFAULT NULL COMMENT 'Date of birth',
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'en' COMMENT 'Preferred language code',
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UTC' COMMENT 'IANA timezone identifier',
  `theme_gender` enum('male','female','neutral') COLLATE utf8mb4_unicode_ci DEFAULT 'neutral' COMMENT 'Theme engine gender preference',
  `notification_email` tinyint(1) DEFAULT '1' COMMENT 'Email notification preference',
  `notification_push` tinyint(1) DEFAULT '1' COMMENT 'Push notification preference',
  `notification_sms` tinyint(1) DEFAULT '0' COMMENT 'SMS notification preference',
  `profile_visibility` enum('public','private','friends') COLLATE utf8mb4_unicode_ci DEFAULT 'public' COMMENT 'Profile visibility level',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_profiles_user` (`user_id`) COMMENT 'Unique user profile lookup',
  KEY `idx_profiles_country` (`country`) COMMENT 'Country-based filtering',
  KEY `idx_profiles_language` (`language`) COMMENT 'Language-based filtering',
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User profiles — bio, preferences, notification settings';

SET FOREIGN_KEY_CHECKS = 1;
