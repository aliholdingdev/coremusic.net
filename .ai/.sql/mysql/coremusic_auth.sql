-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_auth`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_auth`;

CREATE TABLE `admin_activity_log` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `admin_user_id` binary(16) NOT NULL COMMENT 'FK → admin_users.id',
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Admin action description',
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Target entity type',
  `target_id` binary(16) DEFAULT NULL COMMENT 'Target entity ID',
  `old_value` json DEFAULT NULL COMMENT 'Previous state (JSON)',
  `new_value` json DEFAULT NULL COMMENT 'New state (JSON)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin IP address',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Audit event timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_admin_log_user` (`admin_user_id`) COMMENT 'Admin activity lookup',
  KEY `idx_admin_log_action` (`action`) COMMENT 'Action type filtering',
  KEY `idx_admin_log_target` (`target_type`) COMMENT 'Target type filtering',
  KEY `idx_admin_log_created` (`created_at`) COMMENT 'Time-based audit queries',
  CONSTRAINT `fk_admin_log_user` FOREIGN KEY (`admin_user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin activity log — actions, changes, audit trail';

CREATE TABLE `admin_users` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id (unique)',
  `admin_level` enum('super_admin','admin','moderator','support') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Admin privilege level',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin department',
  `can_manage_users` tinyint(1) DEFAULT '0' COMMENT 'User management permission',
  `can_manage_content` tinyint(1) DEFAULT '0' COMMENT 'Content management permission',
  `can_manage_system` tinyint(1) DEFAULT '0' COMMENT 'System management permission',
  `can_view_analytics` tinyint(1) DEFAULT '0' COMMENT 'Analytics access permission',
  `last_admin_action_at` timestamp NULL DEFAULT NULL COMMENT 'Last admin action timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_admin_user` (`user_id`) COMMENT 'Unique admin user lookup',
  KEY `idx_admin_level` (`admin_level`) COMMENT 'Admin level filtering',
  CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin users — privilege levels, permissions';

CREATE TABLE `api_keys` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `key_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of API key',
  `key_prefix` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Visible key prefix for identification',
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Human-readable key label',
  `scopes` json DEFAULT NULL COMMENT 'JSON array of allowed API scopes',
  `rate_limit` int DEFAULT '1000' COMMENT 'Requests per hour limit',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Key active flag',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Last usage timestamp',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Key expiration timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  `key_type` enum('user','service','server') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'ADR-020 key yasam dongusu: user|service|server',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_apikeys_hash` (`key_hash`) COMMENT 'Unique key hash lookup',
  KEY `idx_apikeys_user` (`user_id`) COMMENT 'User API keys lookup',
  KEY `idx_apikeys_active` (`is_active`) COMMENT 'Active key filtering',
  KEY `idx_apikeys_prefix` (`key_prefix`) COMMENT 'Key prefix identification',
  KEY `idx_apikeys_type` (`key_type`),
  CONSTRAINT `fk_apikeys_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API keys — external integration authentication';

CREATE TABLE `api_usage` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `api_key_id` binary(16) NOT NULL COMMENT 'FK → api_keys.id',
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Requested API endpoint',
  `method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP method',
  `status_code` int DEFAULT NULL COMMENT 'HTTP response status code',
  `response_time_ms` int DEFAULT NULL COMMENT 'Response time in milliseconds',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP address',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Client user agent',
  `request_body_hash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 hash of request body',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Request timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_apiusage_key` (`api_key_id`) COMMENT 'API key usage lookup',
  KEY `idx_apiusage_created` (`created_at`) COMMENT 'Time-based usage queries',
  KEY `idx_apiusage_endpoint` (`endpoint`) COMMENT 'Endpoint filtering',
  KEY `idx_apiusage_method` (`method`) COMMENT 'Method filtering',
  CONSTRAINT `fk_apiusage_key` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API usage log — requests, responses, analytics';

CREATE TABLE `credential_audit` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credential_id` binary(16) NOT NULL,
  `credential_key_id` int unsigned DEFAULT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'allowed',
  `denied_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accessed_by` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occurred_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cau_cred` (`credential_id`),
  KEY `idx_cau_key` (`credential_key_id`),
  KEY `idx_cau_user` (`user_id`),
  KEY `idx_cau_action` (`action`),
  KEY `idx_cau_status` (`status`),
  KEY `idx_cau_occurred` (`occurred_at` DESC),
  KEY `idx_cau_request` (`request_id`),
  CONSTRAINT `fk_cau_vault` FOREIGN KEY (`credential_id`) REFERENCES `credential_vault` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credential_audit_chk_1` CHECK ((`action` in (_utf8mb4'read',_utf8mb4'write',_utf8mb4'rotate',_utf8mb4'revoke',_utf8mb4'create'))),
  CONSTRAINT `credential_audit_chk_2` CHECK ((`status` in (_utf8mb4'allowed',_utf8mb4'denied')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Credential audit trail — append-only access log';

CREATE TABLE `credential_keys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key_purpose` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `activated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deactivated_at` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ck_name` (`key_name`),
  UNIQUE KEY `uq_ck_hash` (`key_hash`),
  KEY `idx_ck_purpose` (`key_purpose`),
  KEY `idx_ck_active` (`is_active`),
  CONSTRAINT `credential_keys_chk_1` CHECK ((`key_purpose` in (_utf8mb4'credential_encryption',_utf8mb4'jwt_signing',_utf8mb4'session_encryption',_utf8mb4'api_signing')))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Encryption key version management — hash-only, no raw keys';

CREATE TABLE `credential_vault` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `service_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Target service identifier',
  `credential_type` enum('api_key','secret','token','certificate','password') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Credential type',
  `credential_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Credential identifier within service',
  `encrypted_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'AES-256-GCM encrypted credential value',
  `encryption_algorithm` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'AES-256-GCM' COMMENT 'Encryption algorithm used',
  `encryption_iv` binary(12) DEFAULT NULL COMMENT 'AES-GCM initialization vector',
  `encryption_tag` binary(16) DEFAULT NULL COMMENT 'AES-GCM authentication tag',
  `environment` enum('production','staging','development') COLLATE utf8mb4_unicode_ci DEFAULT 'production' COMMENT 'Target environment',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Credential active flag',
  `last_rotated_at` timestamp NULL DEFAULT NULL COMMENT 'Last key rotation timestamp',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Credential expiration timestamp',
  `rotation_interval_days` int DEFAULT '90' COMMENT 'Auto-rotation interval in days',
  `metadata` json DEFAULT NULL COMMENT 'Additional metadata (JSON)',
  `created_by` binary(16) DEFAULT NULL COMMENT 'FK → users.id — creator',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_credential_unique` (`service_name`,`credential_name`) COMMENT 'Unique credential per service',
  KEY `idx_credential_service` (`service_name`) COMMENT 'Service name lookup',
  KEY `idx_credential_type` (`credential_type`) COMMENT 'Credential type filtering',
  KEY `idx_credential_active` (`is_active`) COMMENT 'Active credential filtering',
  KEY `fk_credential_creator` (`created_by`),
  CONSTRAINT `fk_credential_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Credential vault — AES-256-GCM encrypted secrets';

CREATE TABLE `permission_audit` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id — affected user',
  `permission` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Permission string',
  `action` enum('grant','deny','revoke','check') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Permission action',
  `resource_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Target resource type',
  `resource_id` binary(16) DEFAULT NULL COMMENT 'Target resource ID',
  `reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Reason for action',
  `performed_by` binary(16) DEFAULT NULL COMMENT 'FK → users.id — actor',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Audit event timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_perm_audit_user` (`user_id`) COMMENT 'User permission audit lookup',
  KEY `idx_perm_audit_permission` (`permission`) COMMENT 'Permission filtering',
  KEY `idx_perm_audit_action` (`action`) COMMENT 'Action type filtering',
  KEY `idx_perm_audit_created` (`created_at`) COMMENT 'Time-based audit queries',
  KEY `fk_perm_audit_by` (`performed_by`),
  CONSTRAINT `fk_perm_audit_by` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_perm_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Permission audit trail — grants, denials, revocations';

CREATE TABLE `user_assigned_roles` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `role_id` binary(16) NOT NULL COMMENT 'FK → user_roles.id',
  `assigned_by` binary(16) DEFAULT NULL COMMENT 'FK → users.id — who assigned this role',
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Assignment timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_assigned_roles_unique` (`user_id`,`role_id`) COMMENT 'Prevent duplicate assignments',
  KEY `idx_assigned_roles_user` (`user_id`) COMMENT 'User role lookup',
  KEY `idx_assigned_roles_role` (`role_id`) COMMENT 'Role member lookup',
  KEY `fk_assigned_roles_by` (`assigned_by`),
  CONSTRAINT `fk_assigned_roles_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_assigned_roles_role` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assigned_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User-role assignment junction — RBAC bindings';

CREATE TABLE `user_roles` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique role identifier',
  `role_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Human-readable role description',
  `permissions` json DEFAULT NULL COMMENT 'JSON array of permission strings',
  `is_system` tinyint(1) DEFAULT '0' COMMENT 'System role flag (non-deletable)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_roles_name` (`role_name`) COMMENT 'Unique role name lookup'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='RBAC role definitions — permissions, system roles';

CREATE TABLE `user_sessions` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `session_token_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of session token',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client IP (IPv4/IPv6)',
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Browser/client user agent string',
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Device fingerprint hash',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Session active flag',
  `last_activity_at` timestamp NULL DEFAULT NULL COMMENT 'Last activity timestamp',
  `expires_at` timestamp NOT NULL COMMENT 'Session expiration timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_sessions_token` (`session_token_hash`) COMMENT 'Unique token hash lookup',
  KEY `idx_sessions_user` (`user_id`) COMMENT 'User session lookup',
  KEY `idx_sessions_active` (`is_active`) COMMENT 'Active session filtering',
  KEY `idx_sessions_expires` (`expires_at`) COMMENT 'Expiry cleanup index',
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User sessions — authentication, tracking, expiry';

CREATE TABLE `user_tokens` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `token_type` enum('password_reset','email_verify','api_key','refresh','access') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token purpose',
  `token_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of token value',
  `token_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Human-readable token label',
  `scope` json DEFAULT NULL COMMENT 'JSON array of allowed scopes',
  `expires_at` timestamp NOT NULL COMMENT 'Token expiration timestamp',
  `used_at` timestamp NULL DEFAULT NULL COMMENT 'First use timestamp (single-use tokens)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Requesting IP address',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_tokens_user_type` (`user_id`,`token_type`) COMMENT 'User token type lookup',
  KEY `idx_tokens_hash` (`token_hash`) COMMENT 'Token hash lookup',
  KEY `idx_tokens_expires` (`expires_at`) COMMENT 'Expiry cleanup index',
  CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User tokens — reset, verify, API, refresh, access';

CREATE TABLE `users` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique login username',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User email address (unique)',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Argon2id hashed password',
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User display name',
  `gender` enum('male','female','neutral') COLLATE utf8mb4_unicode_ci DEFAULT 'neutral',
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Profile avatar URL',
  `account_type` enum('free','premium','studio','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'free' COMMENT 'Account tier',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Account active flag',
  `is_banned` tinyint(1) DEFAULT '0' COMMENT 'Banned status flag',
  `is_verified` tinyint(1) DEFAULT '0' COMMENT 'Email verified flag',
  `failed_login_attempts` int DEFAULT '0' COMMENT 'Consecutive failed login count',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT 'Last successful login timestamp',
  `last_login_ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Last login IP (IPv4/IPv6)',
  `password_changed_at` timestamp NULL DEFAULT NULL COMMENT 'Last password change timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_username` (`username`) COMMENT 'Unique username lookup',
  UNIQUE KEY `idx_users_email` (`email`) COMMENT 'Unique email lookup',
  KEY `idx_users_is_active` (`is_active`) COMMENT 'Active user filtering',
  KEY `idx_users_account_type` (`account_type`) COMMENT 'Account tier filtering',
  KEY `idx_users_is_banned` (`is_banned`) COMMENT 'Banned user filtering'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Core user accounts — authentication, profiles, state';

SET FOREIGN_KEY_CHECKS = 1;
