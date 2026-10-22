-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_user`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_user`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `playback_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playback_queue` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_musics.musics.id',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Playback queue ÔÇö per-user track queue with ordering';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_downloads` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_musics.musics.id',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User downloads ÔÇö status, format, file tracking';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_favorites` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id',
  `item_type` enum('music','album','playlist','artist','podcast') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Favorited item type',
  `item_id` binary(16) NOT NULL COMMENT 'Favorited item ID (polymorphic)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Favorite timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_favorites_unique` (`user_id`,`item_type`,`item_id`) COMMENT 'Unique favorite per user-item pair',
  KEY `idx_favorites_item` (`item_type`,`item_id`) COMMENT 'Item popularity lookup',
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User favorites ÔÇö polymorphic item references';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_follows` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `follower_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id ÔÇö follower',
  `following_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id ÔÇö followed user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Follow timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_follows_unique` (`follower_id`,`following_id`) COMMENT 'Unique follow relationship',
  KEY `idx_follows_following` (`following_id`) COMMENT 'Followers of user lookup',
  CONSTRAINT `fk_follows_follower` FOREIGN KEY (`follower_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_follows_following` FOREIGN KEY (`following_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User follow relationships ÔÇö social graph';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_listening_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_listening_history` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id',
  `music_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_musics.musics.id',
  `playlist_id` binary(16) DEFAULT NULL COMMENT 'FK ÔåÆ playlist if from playlist context',
  `listen_duration_sec` int NOT NULL COMMENT 'Total listen duration in seconds',
  `listen_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of track listened (0.00ÔÇô100.00)',
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
  KEY `fk_history_playlist` (`playlist_id`),
  KEY `idx_ulh_user_recent` (`user_id`,`started_at` DESC,`music_id`),
  KEY `idx_ulh_user_music_recent` (`user_id`,`music_id`,`started_at` DESC),
  CONSTRAINT `fk_history_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_history_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `coremusic_playlist`.`playlists` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_history_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Listening history ÔÇö tracks, duration, device context';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id (unique)',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User preferences ÔÇö audio, theme, playback settings';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK ÔåÆ coremusic_auth.users.id (unique)',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User profiles ÔÇö bio, preferences, notification settings';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_recent_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_recent_tracks` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 PK (PHP UuidV7::generate)',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `last_played_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Son calinma zamani (UTC)',
  `play_count` int unsigned NOT NULL DEFAULT '1' COMMENT 'Recent penceresinde oynatma sayisi',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_recent_tracks_pair` (`user_id`,`music_id`),
  KEY `idx_user_recent_tracks_user_last` (`user_id`,`last_played_at`),
  KEY `idx_user_recent_tracks_music` (`music_id`),
  CONSTRAINT `fk_user_recent_tracks_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_user_recent_tracks_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Son dinlenenler (recent tracks) ÔÇö user+music upsert, last_played_at siralamasi';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS = 1;
