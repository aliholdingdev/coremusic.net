-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_media`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_media`;

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
DROP TABLE IF EXISTS `device_playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_playlists` (
  `id` binary(16) NOT NULL,
  `device_id` binary(16) NOT NULL,
  `playlist_id` binary(16) NOT NULL,
  `sync_status` enum('synced','pending','failed','disabled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `sync_priority` int DEFAULT '0',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_device_playlists_device_playlist` (`device_id`,`playlist_id`),
  KEY `idx_device_playlists_sync_status` (`sync_status`),
  KEY `idx_device_playlists_sync_priority` (`sync_priority`),
  KEY `idx_device_playlists_playlist_id` (`playlist_id`),
  CONSTRAINT `fk_device_playlists_devices` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Playlist synchronization status per device for offline access';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `device_sync_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_sync_history` (
  `id` binary(16) NOT NULL,
  `device_id` binary(16) NOT NULL,
  `sync_type` enum('full','incremental','playlist','metadata') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sync_status` enum('started','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracks_synced` int DEFAULT '0',
  `tracks_failed` int DEFAULT '0',
  `bytes_transferred` bigint DEFAULT '0',
  `duration_ms` int DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sync_history_device_id` (`device_id`),
  KEY `idx_sync_history_sync_type` (`sync_type`),
  KEY `idx_sync_history_sync_status` (`sync_status`),
  KEY `idx_sync_history_created_at` (`created_at`),
  CONSTRAINT `fk_device_sync_history_devices` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Device synchronization history for debugging and analytics';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `device_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_tracks` (
  `id` binary(16) NOT NULL,
  `device_id` binary(16) NOT NULL,
  `music_id` binary(16) NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `file_format` enum('mp3','flac','wav','aac','ogg') COLLATE utf8mb4_unicode_ci DEFAULT 'flac',
  `sync_status` enum('synced','pending','failed','deleted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_device_tracks_device_music` (`device_id`,`music_id`),
  KEY `idx_device_tracks_sync_status` (`sync_status`),
  KEY `idx_device_tracks_file_format` (`file_format`),
  KEY `idx_device_tracks_music_id` (`music_id`),
  CONSTRAINT `fk_device_tracks_devices` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track synchronization status per device for offline playback';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `device_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_types` (
  `id` binary(16) NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_description` text COLLATE utf8mb4_unicode_ci,
  `platform` enum('windows','linux','macos','android','ios','rpi','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `has_screen` tinyint(1) DEFAULT '1',
  `has_audio_output` tinyint(1) DEFAULT '1',
  `has_bluetooth` tinyint(1) DEFAULT '0',
  `has_wifi` tinyint(1) DEFAULT '0',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_device_types_name` (`type_name`),
  KEY `idx_device_types_platform` (`platform`),
  KEY `idx_device_types_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Device type definitions for multi-device support and synchronization';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `device_type_id` binary(16) NOT NULL,
  `device_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mac_address` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_primary` tinyint(1) DEFAULT '0',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_devices_uuid` (`device_uuid`),
  KEY `idx_devices_user_id` (`user_id`),
  KEY `idx_devices_device_type_id` (`device_type_id`),
  KEY `idx_devices_is_active` (`is_active`),
  CONSTRAINT `fk_devices_device_types` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User devices for synchronization, streaming, and multi-device access';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_access` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `file_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_type` enum('read','write','delete','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'read',
  `granted_by` binary(16) DEFAULT NULL,
  `granted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_access_grant` (`user_id`,`file_key`,`access_type`),
  KEY `idx_media_access_user_id` (`user_id`),
  KEY `idx_media_access_file_key` (`file_key`),
  KEY `idx_media_access_type` (`access_type`),
  KEY `idx_media_access_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Media file access control permissions for sharing and collaboration';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_audit` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `action` enum('upload','download','delete','play','share','move','copy') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_media_audit_user_id` (`user_id`),
  KEY `idx_media_audit_action` (`action`),
  KEY `idx_media_audit_file_key` (`file_key`),
  KEY `idx_media_audit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Media file operation audit trail for security and compliance';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_metadata` (
  `id` binary(16) NOT NULL,
  `music_id` binary(16) NOT NULL,
  `file_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checksum_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_metadata_file_key` (`file_key`),
  UNIQUE KEY `uk_media_metadata_music_id` (`music_id`),
  KEY `idx_media_metadata_file_name` (`file_name`),
  KEY `idx_media_metadata_mime_type` (`mime_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Media file metadata for audio, images, and other media files';
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
