-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · BCNF & UUID v7 Normalizasyon
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_download`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_download`;

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
DROP TABLE IF EXISTS `download_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `download_cache` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_bytes` bigint unsigned NOT NULL,
  `cached_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_accessed_at` datetime DEFAULT NULL,
  `access_count` int unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dc_hash` (`file_hash`),
  UNIQUE KEY `uq_dc_source_source_id` (`source`,`source_id`),
  KEY `idx_dc_source` (`source`),
  KEY `idx_dc_source_id` (`source`,`source_id`),
  KEY `idx_dc_accessed` (`last_accessed_at` DESC),
  KEY `idx_dc_count` (`access_count` DESC),
  CONSTRAINT `download_cache_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `download_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `download_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size_bytes` bigint unsigned NOT NULL,
  `duration_ms` int unsigned DEFAULT NULL,
  `quality` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `downloaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dh_user` (`user_id`),
  KEY `idx_dh_music` (`music_id`),
  KEY `idx_dh_source` (`source`),
  KEY `idx_dh_downloaded` (`user_id`,`downloaded_at` DESC),
  KEY `idx_dh_hash` (`file_hash`),
  CONSTRAINT `fk_dh_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dh_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `download_history_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `download_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `download_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `priority` tinyint unsigned NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `active_key` tinyint unsigned GENERATED ALWAYS AS (if((`status` in (_utf8mb4'queued',_utf8mb4'processing')),1,NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dq_user_track_active` (`user_id`,`music_id`,`active_key`),
  KEY `idx_dq_user` (`user_id`),
  KEY `idx_dq_music` (`music_id`),
  KEY `idx_dq_status` (`status`),
  KEY `idx_dq_priority` (`user_id`,`priority` DESC),
  KEY `idx_dq_source` (`source`),
  KEY `idx_dq_created` (`created_at` DESC),
  CONSTRAINT `fk_dq_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dq_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `download_queue_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud'))),
  CONSTRAINT `download_queue_chk_2` CHECK ((`status` in (_utf8mb4'queued',_utf8mb4'processing',_utf8mb4'completed',_utf8mb4'failed'))),
  CONSTRAINT `download_queue_chk_3` CHECK ((`priority` between 0 and 255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `download_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `download_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_encrypted` varbinary(512) NOT NULL,
  `api_secret_encrypted` varbinary(512) DEFAULT NULL,
  `quota_remaining` int unsigned DEFAULT NULL,
  `quota_reset_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ds_user_source` (`user_id`,`source`),
  KEY `idx_ds_active` (`is_active`),
  KEY `idx_ds_source` (`source`),
  KEY `idx_ds_quota` (`quota_remaining`),
  CONSTRAINT `fk_ds_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `download_sources_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
