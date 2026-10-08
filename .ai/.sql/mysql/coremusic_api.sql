-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_api`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_api`;

CREATE TABLE `api_calls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_status` smallint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_ms` int unsigned DEFAULT NULL,
  `request_body` text COLLATE utf8mb4_unicode_ci,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `called_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`called_at`),
  KEY `idx_ac_key` (`api_key_id`),
  KEY `idx_ac_user` (`user_id`),
  KEY `idx_ac_endpoint` (`endpoint`(255)),
  KEY `idx_ac_status` (`http_status`),
  KEY `idx_ac_method` (`method`),
  KEY `idx_ac_response` (`response_time_ms`),
  KEY `idx_ac_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!50100 PARTITION BY RANGE (to_days(`called_at`))
(PARTITION p_2026_01 VALUES LESS THAN (740013) ENGINE = InnoDB,
 PARTITION p_2026_02 VALUES LESS THAN (740041) ENGINE = InnoDB,
 PARTITION p_2026_03 VALUES LESS THAN (740072) ENGINE = InnoDB,
 PARTITION p_2026_04 VALUES LESS THAN (740102) ENGINE = InnoDB,
 PARTITION p_2026_05 VALUES LESS THAN (740133) ENGINE = InnoDB,
 PARTITION p_2026_06 VALUES LESS THAN (740163) ENGINE = InnoDB,
 PARTITION p_2026_07 VALUES LESS THAN (740194) ENGINE = InnoDB,
 PARTITION p_2026_08 VALUES LESS THAN (740225) ENGINE = InnoDB,
 PARTITION p_2026_09 VALUES LESS THAN (740255) ENGINE = InnoDB,
 PARTITION p_2026_10 VALUES LESS THAN (740286) ENGINE = InnoDB,
 PARTITION p_2026_11 VALUES LESS THAN (740316) ENGINE = InnoDB,
 PARTITION p_2026_12 VALUES LESS THAN (740347) ENGINE = InnoDB,
 PARTITION p_future VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;

CREATE TABLE `api_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `api_key_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allowed_ips` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `expires_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ak_hash` (`api_key_hash`),
  KEY `idx_ak_user` (`user_id`),
  KEY `idx_ak_active` (`is_active`),
  KEY `idx_ak_expires` (`expires_at`),
  KEY `idx_ak_last_used` (`last_used_at` DESC),
  KEY `idx_ak_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rate_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned NOT NULL,
  `endpoint_pattern` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_requests` int unsigned NOT NULL DEFAULT '60',
  `window_sec` int unsigned NOT NULL DEFAULT '60',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rl_key_endpoint` (`api_key_id`,`endpoint_pattern`(255)),
  KEY `idx_rl_active` (`is_active`),
  KEY `idx_rl_endpoint` (`endpoint_pattern`(255)),
  CONSTRAINT `rate_limits_ibfk_1` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `webhooks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `callback_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `retry_count` tinyint unsigned NOT NULL DEFAULT '3',
  `timeout_ms` int unsigned NOT NULL DEFAULT '5000',
  `last_triggered_at` datetime DEFAULT NULL,
  `last_http_status` smallint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_wh_key` (`api_key_id`),
  KEY `idx_wh_event` (`event_type`),
  KEY `idx_wh_active` (`is_active`),
  KEY `idx_wh_last` (`last_triggered_at`),
  CONSTRAINT `webhooks_ibfk_1` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE,
  CONSTRAINT `webhooks_chk_1` CHECK ((`retry_count` between 0 and 10)),
  CONSTRAINT `webhooks_chk_2` CHECK ((`timeout_ms` between 1000 and 30000))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
