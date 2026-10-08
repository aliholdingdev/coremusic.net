-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_patch`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_patch`;

CREATE TABLE `migration_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `tables_affected` int unsigned DEFAULT NULL,
  `rows_affected` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ml_db` (`db_name`),
  KEY `idx_ml_status` (`status`),
  KEY `idx_ml_started` (`started_at` DESC),
  CONSTRAINT `migration_log_chk_1` CHECK ((`direction` in (_utf8mb4'up',_utf8mb4'down'))),
  CONSTRAINT `migration_log_chk_2` CHECK ((`status` in (_utf8mb4'running',_utf8mb4'completed',_utf8mb4'failed',_utf8mb4'rolled_back')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `patches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `patch_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_db` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `patch_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'schema',
  `sql_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `version_from` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_patch_name` (`patch_name`),
  KEY `idx_p_target` (`target_db`),
  KEY `idx_p_status` (`status`),
  KEY `idx_p_type` (`patch_type`),
  CONSTRAINT `patches_chk_1` CHECK ((`patch_type` in (_utf8mb4'schema',_utf8mb4'data',_utf8mb4'fix',_utf8mb4'security'))),
  CONSTRAINT `patches_chk_2` CHECK ((`status` in (_utf8mb4'pending',_utf8mb4'applied',_utf8mb4'skipped',_utf8mb4'failed')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `schema_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `db_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sql_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `applied_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `execution_ms` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'applied',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sv_db_version` (`db_name`,`version`),
  KEY `idx_sv_db` (`db_name`),
  KEY `idx_sv_status` (`status`),
  KEY `idx_sv_applied` (`applied_at` DESC),
  CONSTRAINT `schema_versions_chk_1` CHECK ((`status` in (_utf8mb4'applied',_utf8mb4'rolled_back',_utf8mb4'pending')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
