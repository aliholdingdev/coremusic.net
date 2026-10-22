-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_playlist`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_playlist`;

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
DROP TABLE IF EXISTS `playlist_collaborators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_collaborators` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz i┼şbirlik├ği tan─▒mlay─▒c─▒',
  `playlist_id` binary(16) NOT NULL COMMENT '├çalma listesi ID',
  `user_id` binary(16) NOT NULL COMMENT 'Kullan─▒c─▒ ID',
  `role` enum('viewer','editor','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewer' COMMENT '─░┼şbirlik├ği rol├╝',
  `invited_by` binary(16) DEFAULT NULL COMMENT 'Davet eden kullan─▒c─▒ ID',
  `invited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Davet zaman─▒',
  `accepted_at` timestamp NULL DEFAULT NULL COMMENT 'Kabul zaman─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_collaborators_unique` (`playlist_id`,`user_id`) COMMENT 'Benzersiz ├ğalma listesi-kullan─▒c─▒ ├ğifti',
  KEY `idx_playlist_collaborators_user` (`user_id`) COMMENT 'Kullan─▒c─▒ filtresi',
  KEY `idx_playlist_collaborators_role` (`role`) COMMENT 'Rol filtresi',
  CONSTRAINT `fk_playlist_collaborators_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_playlist_collaborators_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='├çalma listesi i┼şbirlik├ğileri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlist_followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_followers` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz takip├ği tan─▒mlay─▒c─▒',
  `playlist_id` binary(16) NOT NULL COMMENT '├çalma listesi ID',
  `user_id` binary(16) NOT NULL COMMENT 'Kullan─▒c─▒ ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_followers_unique` (`playlist_id`,`user_id`),
  KEY `idx_playlist_followers_user` (`user_id`) COMMENT 'Kullan─▒c─▒ filtresi',
  CONSTRAINT `fk_playlist_followers_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_playlist_followers_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='├çalma listesi takip├ğileri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlist_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tan─▒mlay─▒c─▒',
  `playlist_id` binary(16) NOT NULL COMMENT '├çalma listesi ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k ├ğalma say─▒s─▒',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftal─▒k ├ğalma say─▒s─▒',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k ├ğalma say─▒s─▒',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `daily_followers` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k takip├ği say─▒s─▒',
  `total_followers` int NOT NULL DEFAULT '0' COMMENT 'Toplam takip├ği say─▒s─▒',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Pop├╝lerlik puan─▒',
  `stats_date` date NOT NULL COMMENT '─░statistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_stats_unique` (`playlist_id`,`stats_date`) COMMENT 'Benzersiz ├ğalma listesi-tarih ├ğifti',
  KEY `idx_playlist_stats_popularity` (`popularity_score`) COMMENT 'Pop├╝lerlik s─▒ralamas─▒',
  CONSTRAINT `fk_playlist_stats_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='├çalma listesi istatistikleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlist_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_tracks` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ili┼şki tan─▒mlay─▒c─▒',
  `playlist_id` binary(16) NOT NULL COMMENT '├çalma listesi ID',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `position` int NOT NULL COMMENT 'S─▒ra numaras─▒',
  `added_by` binary(16) DEFAULT NULL COMMENT 'Ekleyen kullan─▒c─▒ ID',
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Eklenme zaman─▒',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'Not (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_playlist_tracks_unique` (`playlist_id`,`music_id`,`position`),
  KEY `idx_playlist_tracks_playlist_pos` (`playlist_id`,`position`) COMMENT '├çalma listesi s─▒ralamas─▒',
  KEY `idx_playlist_tracks_music` (`music_id`) COMMENT 'M├╝zik filtresi',
  KEY `idx_pt_playlist_pos_created` (`playlist_id`,`position`,`created_at`),
  CONSTRAINT `fk_playlist_tracks_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_playlist_tracks_playlist` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='├çalma listesi par├ğalar─▒ tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ├ğalma listesi tan─▒mlay─▒c─▒',
  `user_id` binary(16) NOT NULL COMMENT 'Olu┼şturan kullan─▒c─▒ ID',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '├çalma listesi ad─▒',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'A├ğ─▒klama',
  `cover_art_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kapak g├Ârseli URL',
  `visibility` enum('public','private','unlisted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'G├Âr├╝n├╝rl├╝k',
  `playlist_type` enum('user','ai','auto_generated','radio','curated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT '├çalma listesi t├╝r├╝',
  `is_editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'D├╝zenlenebilir mi?',
  `total_tracks` int NOT NULL DEFAULT '0' COMMENT 'Toplam par├ğa say─▒s─▒',
  `total_duration_sec` int NOT NULL DEFAULT '0' COMMENT 'Toplam s├╝re (saniye)',
  `follow_count` int NOT NULL DEFAULT '0' COMMENT 'Takip├ği say─▒s─▒',
  `like_count` int NOT NULL DEFAULT '0' COMMENT 'Be─şeni say─▒s─▒',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  KEY `idx_playlists_user` (`user_id`) COMMENT 'Kullan─▒c─▒ filtresi',
  KEY `idx_playlists_visibility` (`visibility`) COMMENT 'G├Âr├╝n├╝rl├╝k filtresi',
  KEY `idx_playlists_type` (`playlist_type`) COMMENT 'T├╝r filtresi',
  KEY `idx_pl_user_updated` (`user_id`,`is_deleted`,`updated_at` DESC),
  FULLTEXT KEY `idx_playlists_fulltext` (`name`,`description`) COMMENT 'Tam metin aramas─▒',
  CONSTRAINT `fk_playlists_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='├çalma listeleri tablosu';
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
