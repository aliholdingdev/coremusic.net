-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_download`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_download`;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dc_hash` (`file_hash`),
  KEY `idx_dc_source` (`source`),
  KEY `idx_dc_source_id` (`source`,`source_id`),
  KEY `idx_dc_accessed` (`last_accessed_at` DESC),
  KEY `idx_dc_count` (`access_count` DESC),
  CONSTRAINT `download_cache_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `download_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `track_id` int unsigned NOT NULL,
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size_bytes` bigint unsigned NOT NULL,
  `duration_ms` int unsigned DEFAULT NULL,
  `quality` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `downloaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_dh_user` (`user_id`),
  KEY `idx_dh_track` (`track_id`),
  KEY `idx_dh_source` (`source`),
  KEY `idx_dh_downloaded` (`user_id`,`downloaded_at` DESC),
  KEY `idx_dh_hash` (`file_hash`),
  CONSTRAINT `download_history_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `download_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `track_id` int unsigned NOT NULL,
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `priority` tinyint unsigned NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_dq_user` (`user_id`),
  KEY `idx_dq_track` (`track_id`),
  KEY `idx_dq_status` (`status`),
  KEY `idx_dq_priority` (`user_id`,`priority` DESC),
  KEY `idx_dq_source` (`source`),
  KEY `idx_dq_created` (`created_at` DESC),
  CONSTRAINT `download_queue_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud'))),
  CONSTRAINT `download_queue_chk_2` CHECK ((`status` in (_utf8mb4'queued',_utf8mb4'processing',_utf8mb4'completed',_utf8mb4'failed'))),
  CONSTRAINT `download_queue_chk_3` CHECK ((`priority` between 0 and 255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `download_sources` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_encrypted` varbinary(512) NOT NULL,
  `api_secret_encrypted` varbinary(512) DEFAULT NULL,
  `quota_remaining` int unsigned DEFAULT NULL,
  `quota_reset_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ds_user_source` (`user_id`,`source`),
  KEY `idx_ds_active` (`is_active`),
  KEY `idx_ds_source` (`source`),
  KEY `idx_ds_quota` (`quota_remaining`),
  CONSTRAINT `download_sources_chk_1` CHECK ((`source` in (_utf8mb4'deezer',_utf8mb4'youtube',_utf8mb4'spotify',_utf8mb4'soundcloud')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
