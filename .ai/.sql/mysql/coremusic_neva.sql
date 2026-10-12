-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_neva`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_neva`;

CREATE TABLE `dsp_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `slot_order` tinyint unsigned NOT NULL,
  `effect_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `params_json` json NOT NULL,
  `is_bypassed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dsp_slot` (`user_id`,`slot_order`),
  KEY `idx_dsp_user` (`user_id`),
  KEY `idx_dsp_type` (`effect_type`),
  KEY `idx_dsp_order` (`slot_order`),
  KEY `idx_dsp_bypass` (`is_bypassed`),
  CONSTRAINT `dsp_settings_chk_1` CHECK ((`slot_order` between 0 and 15)),
  CONSTRAINT `dsp_settings_chk_2` CHECK ((`effect_type` in (_utf8mb4'equalizer',_utf8mb4'compressor',_utf8mb4'gate',_utf8mb4'reverb',_utf8mb4'delay',_utf8mb4'chorus',_utf8mb4'spatial',_utf8mb4'limiter')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eq_presets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `preset_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eq_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'graphic',
  `band_count` tinyint unsigned NOT NULL DEFAULT '10',
  `bands_json` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_eqp_user_name` (`user_id`,`preset_name`),
  KEY `idx_eqp_user` (`user_id`),
  KEY `idx_eqp_type` (`eq_type`),
  KEY `idx_eqp_active` (`user_id`,`is_active`),
  KEY `idx_eqp_system` (`is_system`),
  KEY `idx_eqp_order` (`sort_order`),
  CONSTRAINT `eq_presets_chk_1` CHECK ((`eq_type` in (_utf8mb4'graphic',_utf8mb4'parametric',_utf8mb4'shelving',_utf8mb4'peak'))),
  CONSTRAINT `eq_presets_chk_2` CHECK ((`band_count` in (2,5,10,15,31)))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `routing_matrix` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_channel` tinyint unsigned NOT NULL DEFAULT '0',
  `output_destination` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `output_channel` tinyint unsigned NOT NULL DEFAULT '0',
  `gain_db` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_muted` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rm_user_name` (`user_id`,`route_name`),
  KEY `idx_rm_user` (`user_id`),
  KEY `idx_rm_input` (`input_source`),
  KEY `idx_rm_output` (`output_destination`),
  KEY `idx_rm_active` (`is_active`),
  CONSTRAINT `routing_matrix_chk_1` CHECK ((`input_source` in (_utf8mb4'system',_utf8mb4'microphone',_utf8mb4'file',_utf8mb4'stream',_utf8mb4'plugin'))),
  CONSTRAINT `routing_matrix_chk_2` CHECK ((`output_destination` in (_utf8mb4'speakers',_utf8mb4'headphones',_utf8mb4'bluetooth',_utf8mb4'file',_utf8mb4'stream'))),
  CONSTRAINT `routing_matrix_chk_3` CHECK ((`gain_db` between -(60.00) and 24.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `spectrum_analysis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `bin_count` smallint unsigned NOT NULL DEFAULT '256',
  `fft_size` int unsigned NOT NULL DEFAULT '4096',
  `sample_rate_hz` int unsigned NOT NULL DEFAULT '48000',
  `min_freq_hz` int unsigned NOT NULL DEFAULT '20',
  `max_freq_hz` int unsigned NOT NULL DEFAULT '24000',
  `spectrum_json` json NOT NULL,
  `rms_level_db` decimal(5,2) DEFAULT NULL,
  `peak_level_db` decimal(5,2) DEFAULT NULL,
  `sampled_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sa_session` (`session_id`),
  KEY `idx_sa_user` (`user_id`),
  KEY `idx_sa_sampled` (`sampled_at` DESC),
  KEY `idx_sa_bins` (`bin_count`),
  CONSTRAINT `spectrum_analysis_chk_1` CHECK ((`bin_count` in (128,256,512,1024))),
  CONSTRAINT `spectrum_analysis_chk_2` CHECK ((`fft_size` in (1024,2048,4096,8192,16384))),
  CONSTRAINT `spectrum_analysis_chk_3` CHECK ((`sample_rate_hz` in (44100,48000,96000,192000))),
  CONSTRAINT `spectrum_analysis_chk_4` CHECK (((`rms_level_db` is null) or (`rms_level_db` between -(96.00) and 24.00))),
  CONSTRAINT `spectrum_analysis_chk_5` CHECK (((`peak_level_db` is null) or (`peak_level_db` between -(96.00) and 24.00)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
