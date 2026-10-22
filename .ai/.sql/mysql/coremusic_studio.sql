-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · BCNF & UUID v7 Normalizasyon
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_studio`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_studio`;

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
DROP TABLE IF EXISTS `session_equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_equipment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `equipment_id` int unsigned NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_se_pair` (`session_id`,`equipment_id`),
  KEY `fk_session_equipment_equipment` (`equipment_id`),
  CONSTRAINT `fk_session_equipment_equipment` FOREIGN KEY (`equipment_id`) REFERENCES `studio_equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_session_equipment_session` FOREIGN KEY (`session_id`) REFERENCES `studio_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studio_collaborators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studio_collaborators` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'musician',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `joined_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sc_pair` (`session_id`,`user_id`),
  KEY `idx_sc_user` (`user_id`),
  KEY `idx_sc_role` (`role`),
  CONSTRAINT `fk_studio_collaborators_session` FOREIGN KEY (`session_id`) REFERENCES `studio_sessions` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_studio_collaborators_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `studio_collaborators_chk_1` CHECK ((`role` in (_utf8mb4'owner',_utf8mb4'producer',_utf8mb4'engineer',_utf8mb4'musician',_utf8mb4'vocalist',_utf8mb4'mixer',_utf8mb4'mastering')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studio_equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studio_equipment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equip_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_se_type` (`equip_type`),
  KEY `idx_se_brand` (`brand`),
  KEY `idx_se_serial` (`serial_number`),
  KEY `idx_se_available` (`is_available`),
  CONSTRAINT `studio_equipment_chk_1` CHECK ((`equip_type` in (_utf8mb4'microphone',_utf8mb4'interface',_utf8mb4'headphone',_utf8mb4'monitor',_utf8mb4'midi',_utf8mb4'controller',_utf8mb4'cable',_utf8mb4'other')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studio_presets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studio_presets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `preset_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `use_count` int unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  `deleted_key` bigint GENERATED ALWAYS AS (if((`is_deleted` = 0),0,to_seconds(`deleted_at`))) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sp_pair` (`user_id`,`name`,`deleted_key`),
  KEY `idx_sp_type` (`preset_type`),
  KEY `idx_sp_public` (`is_public`),
  CONSTRAINT `fk_studio_presets_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `studio_presets_chk_1` CHECK ((`preset_type` in (_utf8mb4'eq',_utf8mb4'compressor',_utf8mb4'reverb',_utf8mb4'delay',_utf8mb4'chain',_utf8mb4'master')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studio_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studio_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `host_user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `bpm` smallint unsigned DEFAULT NULL,
  `key_signature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_signature` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '4/4',
  `sample_rate` int unsigned NOT NULL DEFAULT '48000',
  `bit_depth` tinyint unsigned NOT NULL DEFAULT '24',
  `total_tracks` int unsigned NOT NULL DEFAULT '0',
  `duration_seconds` int unsigned DEFAULT NULL,
  `session_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ss_host` (`host_user_id`),
  KEY `idx_ss_status` (`status`),
  KEY `idx_ss_public` (`is_public`),
  CONSTRAINT `fk_studio_sessions_host` FOREIGN KEY (`host_user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `studio_sessions_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'recording',_utf8mb4'mixing',_utf8mb4'mastering',_utf8mb4'completed',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `studio_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studio_tracks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instrument` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `track_number` tinyint unsigned NOT NULL DEFAULT '1',
  `audio_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_format` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_seconds` int unsigned DEFAULT NULL,
  `sample_rate` int unsigned NOT NULL DEFAULT '48000',
  `bit_depth` tinyint unsigned NOT NULL DEFAULT '24',
  `channels` tinyint unsigned NOT NULL DEFAULT '2',
  `is_muted` tinyint(1) NOT NULL DEFAULT '0',
  `is_solo` tinyint(1) NOT NULL DEFAULT '0',
  `volume_db` decimal(6,2) NOT NULL DEFAULT '0.00',
  `pan_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_studio_tracks_session_number` (`session_id`,`track_number`),
  KEY `idx_st_instrument` (`instrument`),
  CONSTRAINT `fk_studio_tracks_session` FOREIGN KEY (`session_id`) REFERENCES `studio_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `studio_tracks_chk_1` CHECK ((`channels` in (1,2,4,6,8)))
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
