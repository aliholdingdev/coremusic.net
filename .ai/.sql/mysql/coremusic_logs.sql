-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_logs`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_logs`;

CREATE TABLE `analytics_daily_genre` (
  `id` binary(16) NOT NULL,
  `genre_id` binary(16) NOT NULL,
  `stat_date` date NOT NULL,
  `play_count` int DEFAULT '0',
  `unique_listeners` int DEFAULT '0',
  `unique_artists` int DEFAULT '0',
  `share_count` int DEFAULT '0',
  `trending_score` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_genre_genre_date` (`genre_id`,`stat_date`),
  KEY `idx_daily_genre_stat_date` (`stat_date`),
  KEY `idx_daily_genre_trending_score` (`trending_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily genre performance analytics for trending and genre-based recommendations';

CREATE TABLE `analytics_daily_music` (
  `id` binary(16) NOT NULL,
  `music_id` binary(16) NOT NULL,
  `stat_date` date NOT NULL,
  `play_count` int DEFAULT '0',
  `unique_listeners` int DEFAULT '0',
  `avg_listen_percentage` decimal(5,2) DEFAULT '0.00',
  `skip_rate` decimal(5,2) DEFAULT '0.00',
  `share_count` int DEFAULT '0',
  `download_count` int DEFAULT '0',
  `like_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_music_music_date` (`music_id`,`stat_date`),
  KEY `idx_daily_music_stat_date` (`stat_date`),
  KEY `idx_daily_music_play_count` (`play_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily music performance analytics for trending and recommendations';

CREATE TABLE `analytics_daily_platform` (
  `id` binary(16) NOT NULL,
  `stat_date` date NOT NULL,
  `total_users` int DEFAULT '0',
  `active_users` int DEFAULT '0',
  `new_registrations` int DEFAULT '0',
  `total_plays` bigint DEFAULT '0',
  `total_listening_hours` decimal(12,2) DEFAULT '0.00',
  `total_searches` int DEFAULT '0',
  `total_downloads` int DEFAULT '0',
  `total_shares` int DEFAULT '0',
  `avg_session_duration_sec` int DEFAULT '0',
  `bounce_rate` decimal(5,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_platform_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily platform-wide analytics for business metrics and growth tracking';

CREATE TABLE `analytics_daily_users` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `stat_date` date NOT NULL,
  `total_plays` int DEFAULT '0',
  `total_listening_sec` int DEFAULT '0',
  `total_searches` int DEFAULT '0',
  `total_shares` int DEFAULT '0',
  `total_downloads` int DEFAULT '0',
  `unique_artists_played` int DEFAULT '0',
  `unique_genres_played` int DEFAULT '0',
  `top_artist_id` binary(16) DEFAULT NULL,
  `top_genre_id` binary(16) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_users_user_date` (`user_id`,`stat_date`),
  KEY `idx_daily_users_stat_date` (`stat_date`),
  KEY `idx_daily_users_total_plays` (`total_plays`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily user analytics for reporting, insights, and recommendations';

CREATE TABLE `analytics_performance` (
  `id` binary(16) NOT NULL,
  `metric_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_value` decimal(20,6) NOT NULL,
  `metric_unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_code` int DEFAULT NULL,
  `response_time_ms` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_performance_metric_name` (`metric_name`),
  KEY `idx_performance_created_at` (`created_at`),
  KEY `idx_performance_endpoint` (`endpoint`),
  KEY `idx_performance_metric_created` (`metric_name`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API and system performance metrics for monitoring and optimization';

CREATE TABLE `analytics_realtime_events` (
  `id` binary(16) NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` binary(16) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_realtime_events_type` (`event_type`),
  KEY `idx_realtime_events_user_id` (`user_id`),
  KEY `idx_realtime_events_created_at` (`created_at`),
  KEY `idx_realtime_events_type_created` (`event_type`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Real-time event tracking for live dashboards and monitoring';

CREATE TABLE `analytics_retention` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `retention_period` enum('daily','weekly','monthly','quarterly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `retention_value` decimal(10,2) NOT NULL,
  `metric_type` enum('plays','listening_time','sessions','engagement') COLLATE utf8mb4_unicode_ci NOT NULL,
  `stat_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_retention_user_period_type_date` (`user_id`,`retention_period`,`metric_type`,`stat_date`),
  KEY `idx_retention_stat_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User retention metrics for business analytics and growth tracking';

CREATE TABLE `analytics_storage` (
  `id` binary(16) NOT NULL,
  `storage_type` enum('audio','image','cache','database','log','backup','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT '0',
  `file_count` int DEFAULT '0',
  `last_scanned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_storage_type` (`storage_type`),
  KEY `idx_storage_path` (`storage_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Storage usage analytics for capacity planning and optimization';

CREATE TABLE `audit_logs` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` binary(16) DEFAULT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_logs_user_id` (`user_id`),
  KEY `idx_audit_logs_action` (`action`),
  KEY `idx_audit_logs_entity_type` (`entity_type`),
  KEY `idx_audit_logs_created_at` (`created_at`),
  KEY `idx_audit_logs_user_created` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for all critical system actions and data changes';

CREATE TABLE `daily_stats` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `stats_date` date NOT NULL,
  `user_id` binary(16) DEFAULT NULL COMMENT 'NULL = tum kullanicilar',
  `total_plays` int unsigned NOT NULL DEFAULT '0',
  `total_listen_ms` bigint unsigned NOT NULL DEFAULT '0',
  `total_skips` int unsigned NOT NULL DEFAULT '0',
  `total_searches` int unsigned NOT NULL DEFAULT '0',
  `total_playlist_creates` int unsigned NOT NULL DEFAULT '0',
  `total_playlist_adds` int unsigned NOT NULL DEFAULT '0',
  `total_likes` int unsigned NOT NULL DEFAULT '0',
  `total_shares` int unsigned NOT NULL DEFAULT '0',
  `total_downloads` int unsigned NOT NULL DEFAULT '0',
  `total_unique_tracks` int unsigned NOT NULL DEFAULT '0',
  `total_unique_artists` int unsigned NOT NULL DEFAULT '0',
  `total_page_views` int unsigned NOT NULL DEFAULT '0',
  `avg_session_minutes` decimal(8,2) DEFAULT NULL,
  `top_genre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ds_user_date` (`stats_date`,`user_id`),
  KEY `idx_ds_date` (`stats_date` DESC),
  KEY `idx_ds_user` (`user_id`),
  KEY `idx_ds_plays` (`total_plays` DESC),
  KEY `idx_ds_listen` (`total_listen_ms` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gunluk ozet istatistikler (cron job ile doldurulur)';

CREATE TABLE `error_logs` (
  `id` binary(16) NOT NULL,
  `error_level` enum('info','warning','error','critical','fatal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_line` int DEFAULT NULL,
  `stack_trace` text COLLATE utf8mb4_unicode_ci,
  `context` json DEFAULT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `request_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_body` json DEFAULT NULL,
  `response_code` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_error_logs_level` (`error_level`),
  KEY `idx_error_logs_code` (`error_code`),
  KEY `idx_error_logs_created_at` (`created_at`),
  KEY `idx_error_logs_user_id` (`user_id`),
  KEY `idx_error_logs_level_created` (`error_level`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Application error and exception logging for debugging and monitoring';

CREATE TABLE `log_activity` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `correlation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` binary(16) NOT NULL,
  `action` enum('LOGIN','LOGOUT','REGISTER','PROFILE_UPDATE','PASSWORD_CHANGE','PASSWORD_RESET','MUSIC_PLAY','MUSIC_PAUSE','MUSIC_STOP','MUSIC_SKIP','MUSIC_ADD_TO_PLAYLIST','MUSIC_REMOVE_FROM_PLAYLIST','PLAYLIST_CREATE','PLAYLIST_DELETE','PLAYLIST_UPDATE','ALBUM_VIEW','ARTIST_VIEW','SEARCH','DOWNLOAD_START','DOWNLOAD_COMPLETE','DOWNLOAD_FAILED','UPLOAD_START','UPLOAD_COMPLETE','FILE_MANAGE','SETTINGS_CHANGE','DEVICE_REGISTER','DEVICE_SYNC','SHARING','COMMENT_POST','LIKE','FOLLOW','UNFOLLOW','NAVIGATION','PAGE_VIEW','API_CALL') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kullanici aksiyonu',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Varlik turu (music, album, playlist vb.)',
  `entity_id` binary(16) DEFAULT NULL COMMENT 'Varlik ID',
  `entity_name` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Varlik adi (redacted)',
  `metadata` json DEFAULT NULL COMMENT 'Ek bilgiler',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','car','studio','home') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp(3) NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_la_created_at` (`created_at`),
  KEY `idx_la_user_id` (`user_id`),
  KEY `idx_la_action` (`action`),
  KEY `idx_la_entity_type` (`entity_type`),
  KEY `idx_la_user_action` (`user_id`,`action`),
  KEY `idx_la_user_created` (`user_id`,`created_at`),
  KEY `idx_la_action_created` (`action`,`created_at`),
  FULLTEXT KEY `ft_la_entity_name` (`entity_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici aktivite loglari - CRUD, dinleme, arama';

CREATE TABLE `log_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `correlation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID v4 - istek takip kimligi',
  `level` enum('TRACE','DEBUG','INFO','WARN','ERROR','CRITICAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'app' COMMENT 'app/security/performance/activity/system',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL COMMENT 'Ek veriler (JSON formatinda)',
  `user_id` binary(16) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IPv4/IPv6',
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_uri` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_code` smallint unsigned DEFAULT NULL,
  `memory_usage` int unsigned DEFAULT NULL COMMENT 'Byte cinsinden',
  `peak_memory` int unsigned DEFAULT NULL,
  `created_at` timestamp(3) NULL DEFAULT CURRENT_TIMESTAMP(3) COMMENT 'Milisaniye hassasiyeti',
  PRIMARY KEY (`id`),
  KEY `idx_le_created_at` (`created_at`),
  KEY `idx_le_level` (`level`),
  KEY `idx_le_category` (`category`),
  KEY `idx_le_user_id` (`user_id`),
  KEY `idx_le_correlation_id` (`correlation_id`),
  KEY `idx_le_level_category` (`level`,`category`),
  KEY `idx_le_created_level` (`created_at`,`level`),
  FULLTEXT KEY `ft_le_message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Genel olay loglari - PSR-3 uyumlu deep logging';

CREATE TABLE `log_performance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `correlation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_type` enum('QUERY_TIME','API_RESPONSE_TIME','TTFB','MEMORY_USAGE','PEAK_MEMORY','DISK_IO','CACHE_HIT','CACHE_MISS','DB_CONNECTION_TIME','FILE_UPLOAD_TIME','FFMPEG_PROCESS_TIME','AUDIO_DECODE_TIME','PAGE_RENDER_TIME','MIDDLEWARE_TIME','TOTAL_REQUEST_TIME') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Metrik tipi',
  `metric_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ozel metrik adi (DB query, endpoint vb.)',
  `metric_value` decimal(12,3) NOT NULL COMMENT 'Metrik degeri (milisaniye, byte vb.)',
  `metric_unit` enum('ms','bytes','count','percent','ops') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ms',
  `context` json DEFAULT NULL COMMENT 'Ek detaylar',
  `user_id` binary(16) DEFAULT NULL,
  `request_uri` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp(3) NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_lp_created_at` (`created_at`),
  KEY `idx_lp_metric_type` (`metric_type`),
  KEY `idx_lp_metric_name` (`metric_name`),
  KEY `idx_lp_user_id` (`user_id`),
  KEY `idx_lp_metric_type_created` (`metric_type`,`created_at`),
  KEY `idx_lp_created_metric` (`created_at`,`metric_type`),
  FULLTEXT KEY `ft_lp_metric_name` (`metric_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Performans metrik loglari - query time, TTFB, memory';

CREATE TABLE `log_security` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `correlation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` enum('CSRF_VIOLATION','AUTH_ATTEMPT_SUCCESS','AUTH_ATTEMPT_FAILED','AUTH_BYPASS_DETECTED','RATE_LIMIT_EXCEEDED','BRUTE_FORCE_DETECTED','SESSION_HIJACK_ATTEMPT','XSS_ATTEMPT','SQL_INJECTION_ATTEMPT','PERMISSION_DENIED','CREDENTIAL_VAULT_ACCESS','API_KEY_USED','TOKEN_REFRESH','SESSION_CREATED','SESSION_DESTROYED','CORS_VIOLATION','CONTENT_SECURITY_POLICY_VIOLATION','SUSPICIOUS_REQUEST') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Guvenlik olay tipi',
  `severity` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MEDIUM',
  `user_id` binary(16) DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_uri` varchar(2000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_body` json DEFAULT NULL COMMENT 'Otomatik redaction uygulanmis',
  `threat_indicators` json DEFAULT NULL COMMENT 'Tehdit gostergeleri',
  `blocked` tinyint(1) DEFAULT '0' COMMENT 'Olay engellendi mi?',
  `response_action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uygulanan aksiyon',
  `created_at` timestamp(3) NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_ls_created_at` (`created_at`),
  KEY `idx_ls_event_type` (`event_type`),
  KEY `idx_ls_severity` (`severity`),
  KEY `idx_ls_ip_address` (`ip_address`),
  KEY `idx_ls_user_id` (`user_id`),
  KEY `idx_ls_severity_created` (`severity`,`created_at`),
  KEY `idx_ls_event_type_created` (`event_type`,`created_at`),
  FULLTEXT KEY `ft_ls_uri` (`request_uri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Guvenlik olay loglari - OWASP Top 10:2025 uyumlu';

CREATE TABLE `log_system` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `correlation_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` enum('SERVICE_START','SERVICE_STOP','SERVICE_ERROR','CRON_JOB_START','CRON_JOB_COMPLETE','CRON_JOB_FAILED','DEPLOYMENT_START','DEPLOYMENT_COMPLETE','DEPLOYMENT_FAILED','CONFIG_CHANGE','SCHEMA_MIGRATION','BACKUP_START','BACKUP_COMPLETE','BACKUP_FAILED','HEALTH_CHECK','HEALTH_CHECK_FAILED','DISK_SPACE_WARNING','MEMORY_WARNING','CPU_WARNING','SSL_CERT_EXPIRING','CRON_SCHEDULED','QUEUE_OVERFLOW','WORKER_START','WORKER_STOP','GRACEFUL_SHUTDOWN','EMERGENCY_STOP') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sistem olay tipi',
  `severity` enum('INFO','WARNING','ERROR','CRITICAL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INFO',
  `component` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sistem bileseni (nginx, php-fpm, mysql vb.)',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp(3) NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (`id`),
  KEY `idx_lsys_created_at` (`created_at`),
  KEY `idx_lsys_event_type` (`event_type`),
  KEY `idx_lsys_severity` (`severity`),
  KEY `idx_lsys_component` (`component`),
  KEY `idx_lsys_severity_created` (`severity`,`created_at`),
  KEY `idx_lsys_event_type_created` (`event_type`,`created_at`),
  FULLTEXT KEY `ft_lsys_message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistem olay loglari - servis, cron, deployment';

CREATE TABLE `page_views` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) DEFAULT NULL COMMENT 'NULL = anonim',
  `page_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desktop',
  `screen_width` smallint unsigned DEFAULT NULL,
  `screen_height` smallint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `load_time_ms` int unsigned DEFAULT NULL,
  `viewed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_user` (`user_id`),
  KEY `idx_pv_url` (`page_url`(255)),
  KEY `idx_pv_device` (`device_type`),
  KEY `idx_pv_referrer` (`referrer`(255)),
  KEY `idx_pv_ip` (`ip_address`),
  KEY `idx_pv_country` (`country_code`),
  KEY `idx_pv_session` (`session_id`),
  KEY `idx_pv_loadtime` (`load_time_ms`),
  KEY `idx_pv_viewed` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sayfa goruntuleme loglari (PARTITION yok, basitlestirilmis)';

CREATE TABLE `performance_metrics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `metric_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_value` decimal(12,4) NOT NULL,
  `source` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'app',
  `tags_json` json DEFAULT NULL COMMENT '{"endpoint":"/api/music","method":"GET"}',
  `sampled_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pm_name` (`metric_name`),
  KEY `idx_pm_source` (`source`),
  KEY `idx_pm_sampled` (`sampled_at` DESC),
  KEY `idx_pm_name_time` (`metric_name`,`sampled_at` DESC),
  CONSTRAINT `performance_metrics_chk_1` CHECK ((`source` in (_utf8mb4'app',_utf8mb4'db',_utf8mb4'cache',_utf8mb4'engine',_utf8mb4'download')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Uygulama/sunucu performans metrikleri (API, DB, cache, engine, download)';

CREATE TABLE `rate_limit_logs` (
  `id` binary(16) NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identifier_type` enum('ip','user','api_key') COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_count` int DEFAULT '1',
  `window_start` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `window_end` timestamp NULL DEFAULT NULL,
  `is_blocked` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rate_limit_identifier` (`identifier`,`identifier_type`),
  KEY `idx_rate_limit_window_start` (`window_start`),
  KEY `idx_rate_limit_blocked` (`is_blocked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rate limiting tracking for API security and abuse prevention';

CREATE TABLE `search_logs` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) DEFAULT NULL,
  `search_query` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_type` enum('music','artist','album','playlist','podcast','radio','video') COLLATE utf8mb4_unicode_ci DEFAULT 'music',
  `results_count` int DEFAULT '0',
  `selected_result_id` binary(16) DEFAULT NULL,
  `selected_result_position` int DEFAULT NULL,
  `response_time_ms` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_search_logs_user_id` (`user_id`),
  KEY `idx_search_logs_query` (`search_query`),
  KEY `idx_search_logs_type` (`search_type`),
  KEY `idx_search_logs_created_at` (`created_at`),
  FULLTEXT KEY `ft_search_logs_query` (`search_query`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Search query logging for analytics, search improvement, and trending analysis';

CREATE TABLE `user_activity_logs` (
  `id` binary(16) NOT NULL,
  `user_id` binary(16) NOT NULL,
  `activity_type` enum('login','logout','play','pause','skip','like','unlike','follow','unfollow','download','share','search','playlist_add','playlist_remove') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` binary(16) DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet','car','studio','home') COLLATE utf8mb4_unicode_ci DEFAULT 'desktop',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_activity_user_id` (`user_id`),
  KEY `idx_user_activity_type` (`activity_type`),
  KEY `idx_user_activity_created_at` (`created_at`),
  KEY `idx_user_activity_user_type` (`user_id`,`activity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User activity tracking for analytics, personalization, and recommendations';

CREATE TABLE `user_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'music_id, album_id, arama terimi ...',
  `event_data` json DEFAULT NULL COMMENT 'Ekstra baglamsal veri',
  `page_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occurred_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ue_user` (`user_id`),
  KEY `idx_ue_type` (`event_type`),
  KEY `idx_ue_value` (`event_value`),
  KEY `idx_ue_session` (`session_id`),
  KEY `idx_ue_occurred` (`occurred_at` DESC),
  KEY `idx_ue_page` (`page_url`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici etkilesim olaylari (tiklama, oynatma, arama, ...)';

SET FOREIGN_KEY_CHECKS = 1;
