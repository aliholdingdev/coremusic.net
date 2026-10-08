-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_studio`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_studio`;

CREATE TABLE `session_equipment` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `equipment_id` int unsigned NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_se_pair` (`session_id`,`equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `studio_collaborators` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'musician',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `joined_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sc_pair` (`session_id`,`user_id`),
  KEY `idx_sc_user` (`user_id`),
  KEY `idx_sc_role` (`role`),
  CONSTRAINT `studio_collaborators_chk_1` CHECK ((`role` in (_utf8mb4'owner',_utf8mb4'producer',_utf8mb4'engineer',_utf8mb4'musician',_utf8mb4'vocalist',_utf8mb4'mixer',_utf8mb4'mastering')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id`),
  KEY `idx_se_type` (`equip_type`),
  KEY `idx_se_brand` (`brand`),
  KEY `idx_se_available` (`is_available`),
  CONSTRAINT `studio_equipment_chk_1` CHECK ((`equip_type` in (_utf8mb4'microphone',_utf8mb4'interface',_utf8mb4'headphone',_utf8mb4'monitor',_utf8mb4'midi',_utf8mb4'controller',_utf8mb4'cable',_utf8mb4'other')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `studio_presets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `preset_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `use_count` int unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sp_pair` (`user_id`,`name`),
  KEY `idx_sp_type` (`preset_type`),
  KEY `idx_sp_public` (`is_public`),
  CONSTRAINT `studio_presets_chk_1` CHECK ((`preset_type` in (_utf8mb4'eq',_utf8mb4'compressor',_utf8mb4'reverb',_utf8mb4'delay',_utf8mb4'chain',_utf8mb4'master')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `studio_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `host_user_id` int unsigned NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `idx_ss_host` (`host_user_id`),
  KEY `idx_ss_status` (`status`),
  KEY `idx_ss_public` (`is_public`),
  CONSTRAINT `studio_sessions_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'recording',_utf8mb4'mixing',_utf8mb4'mastering',_utf8mb4'completed',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id`),
  KEY `idx_st_session` (`session_id`),
  KEY `idx_st_instrument` (`instrument`),
  KEY `idx_st_number` (`session_id`,`track_number`),
  CONSTRAINT `studio_tracks_chk_1` CHECK ((`channels` in (1,2,4,6,8)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
