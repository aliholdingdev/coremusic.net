-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_media`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_media`;

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
  KEY `idx_device_playlists_sync_priority` (`sync_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Playlist synchronization status per device for offline access';

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
  KEY `idx_sync_history_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Device synchronization history for debugging and analytics';

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
  KEY `idx_device_tracks_file_format` (`file_format`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track synchronization status per device for offline playback';

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
  KEY `idx_devices_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User devices for synchronization, streaming, and multi-device access';

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
  KEY `idx_media_access_user_id` (`user_id`),
  KEY `idx_media_access_file_key` (`file_key`),
  KEY `idx_media_access_type` (`access_type`),
  KEY `idx_media_access_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Media file access control permissions for sharing and collaboration';

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

SET FOREIGN_KEY_CHECKS = 1;
