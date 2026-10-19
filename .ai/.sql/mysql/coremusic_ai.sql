-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_ai`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_ai`;

CREATE TABLE `audio_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `music_id` int unsigned NOT NULL,
  `bpm` smallint unsigned DEFAULT NULL,
  `key_signature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_signature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `energy` decimal(3,2) DEFAULT NULL,
  `danceability` decimal(3,2) DEFAULT NULL,
  `valence` decimal(3,2) DEFAULT NULL,
  `acousticness` decimal(3,2) DEFAULT NULL,
  `instrumentalness` decimal(3,2) DEFAULT NULL,
  `liveness` decimal(3,2) DEFAULT NULL,
  `speechiness` decimal(3,2) DEFAULT NULL,
  `loudness_db` decimal(6,2) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `analyzed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_af_music` (`music_id`),
  KEY `idx_af_bpm` (`bpm`),
  KEY `idx_af_key` (`key_signature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `listening_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `music_id` int unsigned NOT NULL,
  `features` json DEFAULT NULL,
  `skip_rate` decimal(3,2) DEFAULT NULL,
  `listen_ratio` decimal(3,2) DEFAULT NULL,
  `repeat_count` int unsigned NOT NULL DEFAULT '0',
  `last_played_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lf_pair` (`user_id`,`music_id`),
  KEY `idx_lf_music` (`music_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `accuracy_score` decimal(5,4) DEFAULT NULL,
  `f1_score` decimal(5,4) DEFAULT NULL,
  `training_data_size` bigint unsigned DEFAULT NULL,
  `training_duration_seconds` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'training',
  `deployed_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mv_pair` (`model_name`,`version`),
  KEY `idx_mv_type` (`model_type`),
  KEY `idx_mv_status` (`status`),
  CONSTRAINT `model_versions_chk_1` CHECK ((`model_type` in (_utf8mb4'recommendation',_utf8mb4'audio_analysis',_utf8mb4'nlp',_utf8mb4'classification'))),
  CONSTRAINT `model_versions_chk_2` CHECK ((`status` in (_utf8mb4'training',_utf8mb4'active',_utf8mb4'deprecated',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recommendation_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `music_id` int unsigned NOT NULL,
  `algorithm` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence` decimal(3,2) DEFAULT NULL,
  `feedback` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `position` tinyint unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_music` (`music_id`),
  KEY `idx_rh_algorithm` (`algorithm`),
  KEY `idx_rh_feedback` (`feedback`),
  KEY `idx_rh_created` (`created_at` DESC),
  CONSTRAINT `recommendation_history_chk_1` CHECK ((`algorithm` in (_utf8mb4'collaborative',_utf8mb4'content_based',_utf8mb4'hybrid',_utf8mb4'ai_curated'))),
  CONSTRAINT `recommendation_history_chk_2` CHECK ((`feedback` in (_utf8mb4'none',_utf8mb4'liked',_utf8mb4'disliked',_utf8mb4'skipped',_utf8mb4'saved')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `training_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_version_id` int unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `metrics` json DEFAULT NULL,
  `gpu_hours` decimal(8,2) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tj_model` (`model_version_id`),
  KEY `idx_tj_status` (`status`),
  CONSTRAINT `training_jobs_chk_1` CHECK ((`status` in (_utf8mb4'queued',_utf8mb4'running',_utf8mb4'completed',_utf8mb4'failed',_utf8mb4'cancelled')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_preference_profiles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `genre_weights` json DEFAULT NULL,
  `mood_preferences` json DEFAULT NULL,
  `tempo_preference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `energy_preference` decimal(3,2) DEFAULT NULL,
  `acoustic_preference` decimal(3,2) DEFAULT NULL,
  `diversity_score` decimal(3,2) DEFAULT NULL,
  `last_analyzed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_upp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
