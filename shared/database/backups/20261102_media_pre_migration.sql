-- Backup before ADR-112 migration (2026-11-02)
DROP TABLE IF EXISTS `coremusic_media`.`media_access_pre_adr112`;
CREATE TABLE `media_access_pre_adr112` (
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

DROP TABLE IF EXISTS `coremusic_media`.`media_audit_pre_adr112`;
CREATE TABLE `media_audit_pre_adr112` (
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
