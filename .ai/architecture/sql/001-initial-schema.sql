-- ============================================================================
-- CoreMusic — Initial Schema (MySQL 9 Uyumlu)
-- ============================================================================
-- Bu dosya: 11 mantıksal veritabanının tam DDL şemasını içerir
-- Sürüm: 1.0.0
-- Tarih: 2026-09-03
-- MySQL: 9.x uyumlu
-- Charset: utf8mb4_unicode_ci
-- Engine: InnoDB
-- ============================================================================
-- Kurallar:
--   - BCNF zorunlu (ADR-040)
--   - snake_case naming
--   - Soft delete: is_deleted TINYINT(1) NOT NULL DEFAULT 0
--   - UUID: CHAR(36) formatında
--   - Prepared statement zorunlu (ADR-002)
--   - SELECT * yasak (ADR-002)
--   - ORM yasak (ADR-002)
-- ============================================================================

-- ============================================================================
-- 1. coremusic_auth — Kimlik Doğrulama
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_auth`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_auth`;

-- Kullanıcı hesapları
CREATE TABLE `users` (
  `id` CHAR(36) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `status` ENUM('active', 'inactive', 'suspended', 'banned') NOT NULL DEFAULT 'active',
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  INDEX `idx_users_status` (`status`),
  INDEX `idx_users_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistem rolleri
CREATE TABLE `roles` (
  `id` CHAR(36) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_name` (`name`),
  INDEX `idx_roles_is_active` (`is_active`),
  INDEX `idx_roles_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kullanıcı-rol eşleme
CREATE TABLE `user_roles` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `role_id` CHAR(36) NOT NULL,
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_roles_user_role` (`user_id`, `role_id`),
  INDEX `idx_user_roles_role_id` (`role_id`),
  INDEX `idx_user_roles_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sistem izinleri
CREATE TABLE `permissions` (
  `id` CHAR(36) NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permissions_module_action` (`module`, `action`),
  INDEX `idx_permissions_is_active` (`is_active`),
  INDEX `idx_permissions_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rol-izin eşleme
CREATE TABLE `role_permissions` (
  `id` CHAR(36) NOT NULL,
  `role_id` CHAR(36) NOT NULL,
  `permission_id` CHAR(36) NOT NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_permissions_role_permission` (`role_id`, `permission_id`),
  INDEX `idx_role_permissions_permission_id` (`permission_id`),
  INDEX `idx_role_permissions_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- JWT yenileme tokenları
CREATE TABLE `refresh_tokens` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `token` VARCHAR(500) NOT NULL,
  `device_name` VARCHAR(100),
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `expires_at` TIMESTAMP NOT NULL,
  `is_revoked` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_refresh_tokens_token` (`token`),
  INDEX `idx_refresh_tokens_user_id` (`user_id`),
  INDEX `idx_refresh_tokens_expires_at` (`expires_at`),
  INDEX `idx_refresh_tokens_is_revoked` (`is_revoked`),
  INDEX `idx_refresh_tokens_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kimlik doğrulama logları
CREATE TABLE `auth_audit_log` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36),
  `event_type` ENUM('login', 'logout', 'login_failed', 'password_change', 'password_reset', 'token_refresh', 'token_revoke') NOT NULL,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `details` JSON,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_auth_audit_log_user_id` (`user_id`),
  INDEX `idx_auth_audit_log_event_type` (`event_type`),
  INDEX `idx_auth_audit_log_created_at` (`created_at`),
  INDEX `idx_auth_audit_log_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aktif oturumlar
CREATE TABLE `user_sessions` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `session_token` VARCHAR(500) NOT NULL,
  `device_name` VARCHAR(100),
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `last_activity_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_sessions_token` (`session_token`),
  INDEX `idx_user_sessions_user_id` (`user_id`),
  INDEX `idx_user_sessions_expires_at` (`expires_at`),
  INDEX `idx_user_sessions_is_active` (`is_active`),
  INDEX `idx_user_sessions_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Şifre sıfırlama talepleri
CREATE TABLE `password_resets` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `token` VARCHAR(500) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `is_used` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_password_resets_token` (`token`),
  INDEX `idx_password_resets_user_id` (`user_id`),
  INDEX `idx_password_resets_expires_at` (`expires_at`),
  INDEX `idx_password_resets_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 2. coremusic_user — Kullanıcı Profilleri
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_user`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_user`;

-- Kullanıcı profilleri
CREATE TABLE `user_profiles` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `first_name` VARCHAR(100),
  `last_name` VARCHAR(100),
  `display_name` VARCHAR(100),
  `avatar_url` VARCHAR(500),
  `bio` TEXT,
  `date_of_birth` DATE,
  `gender` ENUM('male', 'female', 'non_binary', 'prefer_not_to_say'),
  `country_code` CHAR(2),
  `language_code` CHAR(5),
  `timezone` VARCHAR(50),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_profiles_user_id` (`user_id`),
  INDEX `idx_user_profiles_country_code` (`country_code`),
  INDEX `idx_user_profiles_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kullanıcı tercihleri
CREATE TABLE `user_preferences` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `theme` VARCHAR(50) DEFAULT 'system',
  `language` VARCHAR(10) DEFAULT 'tr',
  `notifications_email` TINYINT(1) NOT NULL DEFAULT 1,
  `notifications_push` TINYINT(1) NOT NULL DEFAULT 1,
  `notifications_sms` TINYINT(1) NOT NULL DEFAULT 0,
  `privacy_profile` ENUM('public', 'private', 'friends') NOT NULL DEFAULT 'public',
  `playback_quality` ENUM('low', 'medium', 'high', 'lossless') NOT NULL DEFAULT 'high',
  `auto_download` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_preferences_user_id` (`user_id`),
  INDEX `idx_user_preferences_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 3. coremusic_media — Medya Yönetimi
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_media`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_media`;

-- Medya dosya kayıtları
CREATE TABLE `media_files` (
  `id` CHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `artist` VARCHAR(255),
  `album` VARCHAR(255),
  `album_artist` VARCHAR(255),
  `genre` VARCHAR(100),
  `track_number` INT UNSIGNED,
  `disc_number` INT UNSIGNED,
  `year` SMALLINT UNSIGNED,
  `duration_ms` INT UNSIGNED,
  `file_path` VARCHAR(1000) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` BIGINT UNSIGNED,
  `file_hash` CHAR(64),
  `mime_type` VARCHAR(100),
  `bit_rate` INT UNSIGNED,
  `sample_rate` INT UNSIGNED,
  `channels` TINYINT UNSIGNED,
  `bit_depth` TINYINT UNSIGNED,
  `codec` VARCHAR(50),
  `cover_art_url` VARCHAR(500),
  `lyrics` TEXT,
  `is_favorite` TINYINT(1) NOT NULL DEFAULT 0,
  `play_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_played_at` TIMESTAMP NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_files_file_hash` (`file_hash`),
  INDEX `idx_media_files_artist` (`artist`),
  INDEX `idx_media_files_album` (`album`),
  INDEX `idx_media_files_genre` (`genre`),
  INDEX `idx_media_files_year` (`year`),
  INDEX `idx_media_files_is_favorite` (`is_favorite`),
  INDEX `idx_media_files_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medya etiketleri
CREATE TABLE `media_tags` (
  `id` CHAR(36) NOT NULL,
  `media_id` CHAR(36) NOT NULL,
  `tag_name` VARCHAR(100) NOT NULL,
  `tag_value` VARCHAR(500),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_tags_media_tag` (`media_id`, `tag_name`),
  INDEX `idx_media_tags_tag_name` (`tag_name`),
  INDEX `idx_media_tags_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 4. coremusic_playlist — Çalma Listeleri
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_playlist`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_playlist`;

-- Çalma listeleri
CREATE TABLE `playlists` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `cover_art_url` VARCHAR(500),
  `visibility` ENUM('public', 'private', 'friends') NOT NULL DEFAULT 'private',
  `item_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_duration_ms` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_playlists_user_id` (`user_id`),
  INDEX `idx_playlists_visibility` (`visibility`),
  INDEX `idx_playlists_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Çalma listesi öğeleri
CREATE TABLE `playlist_items` (
  `id` CHAR(36) NOT NULL,
  `playlist_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36) NOT NULL,
  `position` INT UNSIGNED NOT NULL,
  `added_by_user_id` CHAR(36),
  `notes` TEXT,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_playlist_items_playlist_position` (`playlist_id`, `position`),
  INDEX `idx_playlist_items_media_id` (`media_id`),
  INDEX `idx_playlist_items_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 5. coremusic_library — Kütüphane ve Favoriler
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_library`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_library`;

-- Kullanıcı kütüphaneleri
CREATE TABLE `user_libraries` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `type` ENUM('personal', 'shared', 'system') NOT NULL DEFAULT 'personal',
  `item_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_libraries_user_id` (`user_id`),
  INDEX `idx_user_libraries_type` (`type`),
  INDEX `idx_user_libraries_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kullanıcı favorileri
CREATE TABLE `user_favorites` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36) NOT NULL,
  `added_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_favorites_user_media` (`user_id`, `media_id`),
  INDEX `idx_user_favorites_media_id` (`media_id`),
  INDEX `idx_user_favorites_added_at` (`added_at`),
  INDEX `idx_user_favorites_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 6. coremusic_activity — Aktivite ve Olaylar
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_activity`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_activity`;

-- Dinleme geçmişi
CREATE TABLE `listening_history` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36) NOT NULL,
  `started_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` TIMESTAMP NULL,
  `duration_ms` INT UNSIGNED,
  `completed` TINYINT(1) NOT NULL DEFAULT 0,
  `device_type` VARCHAR(50),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_listening_history_user_id` (`user_id`),
  INDEX `idx_listening_history_media_id` (`media_id`),
  INDEX `idx_listening_history_started_at` (`started_at`),
  INDEX `idx_listening_history_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İndirme kuyruğu
CREATE TABLE `download_queue` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36),
  `source_url` VARCHAR(1000),
  `source_type` ENUM('youtube', 'deezer', 'spotify', 'local', 'other') NOT NULL,
  `status` ENUM('pending', 'downloading', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  `priority` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `error_message` TEXT,
  `retry_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `max_retries` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_download_queue_user_id` (`user_id`),
  INDEX `idx_download_queue_status` (`status`),
  INDEX `idx_download_queue_priority` (`priority`),
  INDEX `idx_download_queue_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- İndirme geçmişi
CREATE TABLE `download_history` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36),
  `source_url` VARCHAR(1000),
  `source_type` ENUM('youtube', 'deezer', 'spotify', 'local', 'other') NOT NULL,
  `file_path` VARCHAR(1000),
  `file_size` BIGINT UNSIGNED,
  `file_hash` CHAR(64),
  `status` ENUM('completed', 'failed') NOT NULL,
  `error_message` TEXT,
  `download_duration_ms` INT UNSIGNED,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_download_history_user_id` (`user_id`),
  INDEX `idx_download_history_media_id` (`media_id`),
  INDEX `idx_download_history_status` (`status`),
  INDEX `idx_download_history_created_at` (`created_at`),
  INDEX `idx_download_history_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Oynatma olayları
CREATE TABLE `play_events` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `media_id` CHAR(36) NOT NULL,
  `event_type` ENUM('play', 'pause', 'stop', 'skip', 'seek', 'repeat', 'shuffle') NOT NULL,
  `position_ms` INT UNSIGNED,
  `duration_ms` INT UNSIGNED,
  `device_type` VARCHAR(50),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_play_events_user_id` (`user_id`),
  INDEX `idx_play_events_media_id` (`media_id`),
  INDEX `idx_play_events_event_type` (`event_type`),
  INDEX `idx_play_events_created_at` (`created_at`),
  INDEX `idx_play_events_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Arama olayları
CREATE TABLE `search_events` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36),
  `query` VARCHAR(500) NOT NULL,
  `results_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `selected_index` INT UNSIGNED,
  `search_type` ENUM('global', 'artist', 'album', 'track', 'playlist', 'podcast') NOT NULL DEFAULT 'global',
  `duration_ms` INT UNSIGNED,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_search_events_user_id` (`user_id`),
  INDEX `idx_search_events_query` (`query`(100)),
  INDEX `idx_search_events_search_type` (`search_type`),
  INDEX `idx_search_events_created_at` (`created_at`),
  INDEX `idx_search_events_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 7. coremusic_notification — Bildirimler
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_notification`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_notification`;

-- Kullanıcı bildirimleri
CREATE TABLE `notifications` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `type` ENUM('info', 'warning', 'error', 'success', 'system', 'social', 'download', 'playlist') NOT NULL DEFAULT 'info',
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `action_url` VARCHAR(500),
  `action_label` VARCHAR(100),
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `read_at` TIMESTAMP NULL,
  `expires_at` TIMESTAMP NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_notifications_user_id` (`user_id`),
  INDEX `idx_notifications_type` (`type`),
  INDEX `idx_notifications_is_read` (`is_read`),
  INDEX `idx_notifications_created_at` (`created_at`),
  INDEX `idx_notifications_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bildirim tercihleri
CREATE TABLE `notification_settings` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `email_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `push_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `sms_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `download_complete` TINYINT(1) NOT NULL DEFAULT 1,
  `new_release` TINYINT(1) NOT NULL DEFAULT 1,
  `social_activity` TINYINT(1) NOT NULL DEFAULT 1,
  `system_updates` TINYINT(1) NOT NULL DEFAULT 1,
  `marketing` TINYINT(1) NOT NULL DEFAULT 0,
  `quiet_hours_start` TIME,
  `quiet_hours_end` TIME,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notification_settings_user_id` (`user_id`),
  INDEX `idx_notification_settings_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 8. coremusic_device — Cihaz Yönetimi
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_device`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_device`;

-- Kayıtlı cihazlar
CREATE TABLE `registered_devices` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `device_name` VARCHAR(100) NOT NULL,
  `device_type` ENUM('desktop', 'mobile', 'tablet', 'smart_speaker', 'car', 'tv', 'embedded', 'other') NOT NULL,
  `device_fingerprint` VARCHAR(255) NOT NULL,
  `os_type` VARCHAR(50),
  `os_version` VARCHAR(50),
  `app_version` VARCHAR(50),
  `last_active_at` TIMESTAMP NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_trusted` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_registered_devices_fingerprint` (`device_fingerprint`),
  INDEX `idx_registered_devices_user_id` (`user_id`),
  INDEX `idx_registered_devices_device_type` (`device_type`),
  INDEX `idx_registered_devices_is_active` (`is_active`),
  INDEX `idx_registered_devices_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cihaz oturumları
CREATE TABLE `device_sessions` (
  `id` CHAR(36) NOT NULL,
  `device_id` CHAR(36) NOT NULL,
  `user_id` CHAR(36) NOT NULL,
  `session_token` VARCHAR(500) NOT NULL,
  `ip_address` VARCHAR(45),
  `location_country` CHAR(2),
  `location_city` VARCHAR(100),
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_active_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NOT NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_device_sessions_token` (`session_token`),
  INDEX `idx_device_sessions_device_id` (`device_id`),
  INDEX `idx_device_sessions_user_id` (`user_id`),
  INDEX `idx_device_sessions_is_active` (`is_active`),
  INDEX `idx_device_sessions_expires_at` (`expires_at`),
  INDEX `idx_device_sessions_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 9. coremusic_search — Arama Motoru
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_search`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_search`;

-- Arama indeksi
CREATE TABLE `search_index` (
  `id` CHAR(36) NOT NULL,
  `entity_type` ENUM('artist', 'album', 'track', 'playlist', 'podcast', 'user') NOT NULL,
  `entity_id` CHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255),
  `description` TEXT,
  `tags` JSON,
  `popularity_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_search_index_entity` (`entity_type`, `entity_id`),
  FULLTEXT INDEX `ft_search_index_title` (`title`, `subtitle`),
  INDEX `idx_search_index_popularity` (`popularity_score`),
  INDEX `idx_search_index_is_active` (`is_active`),
  INDEX `idx_search_index_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Arama önerileri
CREATE TABLE `search_suggestions` (
  `id` CHAR(36) NOT NULL,
  `query` VARCHAR(255) NOT NULL,
  `normalized_query` VARCHAR(255) NOT NULL,
  `search_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `last_searched_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_trending` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_search_suggestions_normalized` (`normalized_query`),
  INDEX `idx_search_suggestions_search_count` (`search_count`),
  INDEX `idx_search_suggestions_is_trending` (`is_trending`),
  INDEX `idx_search_suggestions_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 10. coremusic_config — Sistem Yapılandırması
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_config`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_config`;

-- Sistem ayarları
CREATE TABLE `system_config` (
  `id` CHAR(36) NOT NULL,
  `config_key` VARCHAR(255) NOT NULL,
  `config_value` TEXT,
  `config_type` ENUM('string', 'integer', 'float', 'boolean', 'json', 'text') NOT NULL DEFAULT 'string',
  `category` VARCHAR(100) NOT NULL DEFAULT 'general',
  `description` TEXT,
  `is_public` TINYINT(1) NOT NULL DEFAULT 0,
  `is_sensitive` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_system_config_key` (`config_key`),
  INDEX `idx_system_config_category` (`category`),
  INDEX `idx_system_config_is_public` (`is_public`),
  INDEX `idx_system_config_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Özellik bayrakları
CREATE TABLE `feature_flags` (
  `id` CHAR(36) NOT NULL,
  `flag_key` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `rollout_percentage` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `target_audience` ENUM('all', 'admin', 'premium', 'beta', 'none') NOT NULL DEFAULT 'none',
  `start_date` TIMESTAMP NULL,
  `end_date` TIMESTAMP NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_feature_flags_key` (`flag_key`),
  INDEX `idx_feature_flags_is_enabled` (`is_enabled`),
  INDEX `idx_feature_flags_target_audience` (`target_audience`),
  INDEX `idx_feature_flags_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 11. coremusic_analytics — Analitik
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `coremusic_analytics`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_analytics`;

-- Günlük istatistikler
CREATE TABLE `daily_stats` (
  `id` CHAR(36) NOT NULL,
  `stat_date` DATE NOT NULL,
  `metric_name` VARCHAR(100) NOT NULL,
  `metric_value` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `dimension` VARCHAR(100),
  `dimension_value` VARCHAR(255),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_stats_date_metric` (`stat_date`, `metric_name`, `dimension`, `dimension_value`),
  INDEX `idx_daily_stats_stat_date` (`stat_date`),
  INDEX `idx_daily_stats_metric_name` (`metric_name`),
  INDEX `idx_daily_stats_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Performans metrikleri
CREATE TABLE `performance_metrics` (
  `id` CHAR(36) NOT NULL,
  `metric_name` VARCHAR(100) NOT NULL,
  `metric_value` DECIMAL(15,6) NOT NULL,
  `unit` VARCHAR(20),
  `source` VARCHAR(100),
  `tags` JSON,
  `recorded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_performance_metrics_metric_name` (`metric_name`),
  INDEX `idx_performance_metrics_recorded_at` (`recorded_at`),
  INDEX `idx_performance_metrics_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hata kayıtları
CREATE TABLE `error_logs` (
  `id` CHAR(36) NOT NULL,
  `error_level` ENUM('debug', 'info', 'warning', 'error', 'critical', 'fatal') NOT NULL DEFAULT 'error',
  `message` TEXT NOT NULL,
  `file` VARCHAR(500),
  `line` INT UNSIGNED,
  `function` VARCHAR(255),
  `trace` TEXT,
  `context` JSON,
  `url` VARCHAR(500),
  `user_id` CHAR(36),
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `is_resolved` TINYINT(1) NOT NULL DEFAULT 0,
  `resolved_at` TIMESTAMP NULL,
  `resolved_by` CHAR(36),
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_error_logs_error_level` (`error_level`),
  INDEX `idx_error_logs_user_id` (`user_id`),
  INDEX `idx_error_logs_created_at` (`created_at`),
  INDEX `idx_error_logs_is_resolved` (`is_resolved`),
  INDEX `idx_error_logs_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Denetim kayıtları
CREATE TABLE `audit_logs` (
  `id` CHAR(36) NOT NULL,
  `user_id` CHAR(36),
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(100) NOT NULL,
  `entity_id` CHAR(36),
  `old_values` JSON,
  `new_values` JSON,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_audit_logs_user_id` (`user_id`),
  INDEX `idx_audit_logs_action` (`action`),
  INDEX `idx_audit_logs_entity_type` (`entity_type`),
  INDEX `idx_audit_logs_entity_id` (`entity_id`),
  INDEX `idx_audit_logs_created_at` (`created_at`),
  INDEX `idx_audit_logs_is_deleted` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- SON
-- ============================================================================
