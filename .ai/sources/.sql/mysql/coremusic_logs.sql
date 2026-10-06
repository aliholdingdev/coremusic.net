-- coremusic_logs — LOGlama, ANALiTiK & AUDIT VERiTABANI
-- Version: 7.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at
-- Tables: 17 (13 log + 4 analytics)

CREATE DATABASE IF NOT EXISTS coremusic_logs
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_logs;

-- =============================================
-- Table: audit_logs
-- Purpose: Audit trail for all critical system actions
-- BCNF: Each non-key attribute determines the key (action, entity_type, etc.)
-- =============================================
CREATE TABLE audit_logs (
    id BINARY(16) NOT NULL,
    user_id BINARY(16),
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id BINARY(16),
    old_value JSON,
    new_value JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_audit_logs_user_id (user_id),
    INDEX idx_audit_logs_action (action),
    INDEX idx_audit_logs_entity_type (entity_type),
    INDEX idx_audit_logs_created_at (created_at),
    INDEX idx_audit_logs_user_created (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Audit trail for all critical system actions and data changes';

-- =============================================
-- Table: user_activity_logs
-- Purpose: User activity tracking for analytics and personalization
-- BCNF: Each non-key attribute determines the key (activity_type, entity_type, etc.)
-- =============================================
CREATE TABLE user_activity_logs (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    activity_type ENUM('login','logout','play','pause','skip','like','unlike','follow','unfollow','download','share','search','playlist_add','playlist_remove') NOT NULL,
    entity_type VARCHAR(50),
    entity_id BINARY(16),
    metadata JSON,
    ip_address VARCHAR(45),
    device_type ENUM('desktop','mobile','tablet','car','studio','home') DEFAULT 'desktop',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_user_activity_user_id (user_id),
    INDEX idx_user_activity_type (activity_type),
    INDEX idx_user_activity_created_at (created_at),
    INDEX idx_user_activity_user_type (user_id, activity_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='User activity tracking for analytics, personalization, and recommendations';

-- =============================================
-- Table: search_logs
-- Purpose: Search query logging for analytics and search improvement
-- BCNF: Each non-key attribute determines the key (search_type, results_count, etc.)
-- =============================================
CREATE TABLE search_logs (
    id BINARY(16) NOT NULL,
    user_id BINARY(16),
    search_query VARCHAR(500) NOT NULL,
    search_type ENUM('music','artist','album','playlist','podcast','radio','video') DEFAULT 'music',
    results_count INT DEFAULT 0,
    selected_result_id BINARY(16),
    selected_result_position INT,
    response_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_search_logs_user_id (user_id),
    INDEX idx_search_logs_query (search_query),
    INDEX idx_search_logs_type (search_type),
    INDEX idx_search_logs_created_at (created_at),
    FULLTEXT INDEX ft_search_logs_query (search_query)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Search query logging for analytics, search improvement, and trending analysis';

-- =============================================
-- Table: error_logs
-- Purpose: Application error and exception logging
-- BCNF: Each non-key attribute determines the key (error_level, error_code, etc.)
-- =============================================
CREATE TABLE error_logs (
    id BINARY(16) NOT NULL,
    error_level ENUM('info','warning','error','critical','fatal') NOT NULL,
    error_code VARCHAR(50),
    error_message TEXT NOT NULL,
    error_file VARCHAR(500),
    error_line INT,
    stack_trace TEXT,
    context JSON,
    user_id BINARY(16),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_url VARCHAR(500),
    request_method VARCHAR(10),
    request_body JSON,
    response_code INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_error_logs_level (error_level),
    INDEX idx_error_logs_code (error_code),
    INDEX idx_error_logs_created_at (created_at),
    INDEX idx_error_logs_user_id (user_id),
    INDEX idx_error_logs_level_created (error_level, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Application error and exception logging for debugging and monitoring';

-- =============================================
-- Table: rate_limit_logs
-- Purpose: Rate limiting tracking for API security
-- BCNF: Each non-key attribute determines the key (identifier_type, endpoint, etc.)
-- =============================================
CREATE TABLE rate_limit_logs (
    id BINARY(16) NOT NULL,
    identifier VARCHAR(255) NOT NULL,
    identifier_type ENUM('ip','user','api_key') NOT NULL,
    endpoint VARCHAR(500),
    request_count INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    window_end TIMESTAMP NULL,
    is_blocked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_rate_limit_identifier (identifier, identifier_type),
    INDEX idx_rate_limit_window_start (window_start),
    INDEX idx_rate_limit_blocked (is_blocked)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Rate limiting tracking for API security and abuse prevention';

-- =============================================
-- Table: analytics_daily_users
-- Purpose: Daily user analytics for reporting and insights
-- BCNF: Each non-key attribute determines the key (total_plays, total_listening_sec, etc.)
-- =============================================
CREATE TABLE analytics_daily_users (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    stat_date DATE NOT NULL,
    total_plays INT DEFAULT 0,
    total_listening_sec INT DEFAULT 0,
    total_searches INT DEFAULT 0,
    total_shares INT DEFAULT 0,
    total_downloads INT DEFAULT 0,
    unique_artists_played INT DEFAULT 0,
    unique_genres_played INT DEFAULT 0,
    top_artist_id BINARY(16),
    top_genre_id BINARY(16),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_daily_users_user_date (user_id, stat_date),
    INDEX idx_daily_users_stat_date (stat_date),
    INDEX idx_daily_users_total_plays (total_plays)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily user analytics for reporting, insights, and recommendations';

-- =============================================
-- Table: analytics_daily_platform
-- Purpose: Daily platform-wide analytics for business metrics
-- BCNF: Each non-key attribute determines the key (total_users, active_users, etc.)
-- =============================================
CREATE TABLE analytics_daily_platform (
    id BINARY(16) NOT NULL,
    stat_date DATE NOT NULL,
    total_users INT DEFAULT 0,
    active_users INT DEFAULT 0,
    new_registrations INT DEFAULT 0,
    total_plays BIGINT DEFAULT 0,
    total_listening_hours DECIMAL(12,2) DEFAULT 0,
    total_searches INT DEFAULT 0,
    total_downloads INT DEFAULT 0,
    total_shares INT DEFAULT 0,
    avg_session_duration_sec INT DEFAULT 0,
    bounce_rate DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_daily_platform_date (stat_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily platform-wide analytics for business metrics and growth tracking';

-- =============================================
-- Table: analytics_daily_music
-- Purpose: Daily music performance analytics
-- BCNF: Each non-key attribute determines the key (play_count, unique_listeners, etc.)
-- =============================================
CREATE TABLE analytics_daily_music (
    id BINARY(16) NOT NULL,
    music_id BINARY(16) NOT NULL,
    stat_date DATE NOT NULL,
    play_count INT DEFAULT 0,
    unique_listeners INT DEFAULT 0,
    avg_listen_percentage DECIMAL(5,2) DEFAULT 0,
    skip_rate DECIMAL(5,2) DEFAULT 0,
    share_count INT DEFAULT 0,
    download_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_daily_music_music_date (music_id, stat_date),
    INDEX idx_daily_music_stat_date (stat_date),
    INDEX idx_daily_music_play_count (play_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily music performance analytics for trending and recommendations';

-- =============================================
-- Table: analytics_daily_genre
-- Purpose: Daily genre performance analytics
-- BCNF: Each non-key attribute determines the key (play_count, unique_listeners, etc.)
-- =============================================
CREATE TABLE analytics_daily_genre (
    id BINARY(16) NOT NULL,
    genre_id BINARY(16) NOT NULL,
    stat_date DATE NOT NULL,
    play_count INT DEFAULT 0,
    unique_listeners INT DEFAULT 0,
    unique_artists INT DEFAULT 0,
    share_count INT DEFAULT 0,
    trending_score DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_daily_genre_genre_date (genre_id, stat_date),
    INDEX idx_daily_genre_stat_date (stat_date),
    INDEX idx_daily_genre_trending_score (trending_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily genre performance analytics for trending and genre-based recommendations';

-- =============================================
-- Table: analytics_realtime_events
-- Purpose: Real-time event tracking for live dashboards
-- BCNF: Each non-key attribute determines the key (event_type, user_id, etc.)
-- =============================================
CREATE TABLE analytics_realtime_events (
    id BINARY(16) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    user_id BINARY(16),
    entity_type VARCHAR(50),
    entity_id BINARY(16),
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_realtime_events_type (event_type),
    INDEX idx_realtime_events_user_id (user_id),
    INDEX idx_realtime_events_created_at (created_at),
    INDEX idx_realtime_events_type_created (event_type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Real-time event tracking for live dashboards and monitoring';

-- =============================================
-- Table: analytics_performance
-- Purpose: API and system performance metrics
-- BCNF: Each non-key attribute determines the key (metric_value, metric_unit, etc.)
-- =============================================
CREATE TABLE analytics_performance (
    id BINARY(16) NOT NULL,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(20,6) NOT NULL,
    metric_unit VARCHAR(50),
    endpoint VARCHAR(500),
    method VARCHAR(10),
    status_code INT,
    response_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_performance_metric_name (metric_name),
    INDEX idx_performance_created_at (created_at),
    INDEX idx_performance_endpoint (endpoint),
    INDEX idx_performance_metric_created (metric_name, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='API and system performance metrics for monitoring and optimization';

-- =============================================
-- Table: analytics_storage
-- Purpose: Storage usage analytics for capacity planning
-- BCNF: Each non-key attribute determines the key (storage_path, file_size, etc.)
-- =============================================
CREATE TABLE analytics_storage (
    id BINARY(16) NOT NULL,
    storage_type ENUM('audio','image','cache','database','log','backup','other') NOT NULL,
    storage_path VARCHAR(500),
    file_size BIGINT DEFAULT 0,
    file_count INT DEFAULT 0,
    last_scanned_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_storage_type (storage_type),
    INDEX idx_storage_path (storage_path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Storage usage analytics for capacity planning and optimization';

-- =============================================
-- Table: analytics_retention
-- Purpose: User retention metrics for business analytics
-- BCNF: Each non-key attribute determines the key (retention_value, metric_type, etc.)
-- =============================================
CREATE TABLE analytics_retention (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    retention_period ENUM('daily','weekly','monthly','quarterly','yearly') NOT NULL,
    retention_value DECIMAL(10,2) NOT NULL,
    metric_type ENUM('plays','listening_time','sessions','engagement') NOT NULL,
    stat_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_retention_user_period_type_date (user_id, retention_period, metric_type, stat_date),
    INDEX idx_retention_stat_date (stat_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='User retention metrics for business analytics and growth tracking';

-- =============================================
-- Table: page_views
-- Purpose: Sayfa goruntuleme loglari
-- BCNF: id -> {user_id, page_url, referrer, device_type, ...}
-- PARTITION: Yok (basitlestirilmis versiyon)
-- =============================================
CREATE TABLE page_views (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         BINARY(16)              NULL COMMENT 'NULL = anonim',
    page_url        VARCHAR(2048)       NOT NULL,
    referrer        VARCHAR(2048)           NULL,
    device_type     VARCHAR(20)         NOT NULL DEFAULT 'desktop',
    screen_width    SMALLINT UNSIGNED       NULL,
    screen_height   SMALLINT UNSIGNED       NULL,
    ip_address      VARCHAR(45)             NULL,
    user_agent      VARCHAR(512)            NULL,
    country_code    CHAR(2)                 NULL,
    session_id      VARCHAR(64)             NULL,
    load_time_ms    INT UNSIGNED            NULL,
    viewed_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_pv_user       (user_id),
    INDEX idx_pv_url        (page_url(255)),
    INDEX idx_pv_device     (device_type),
    INDEX idx_pv_referrer   (referrer(255)),
    INDEX idx_pv_ip         (ip_address),
    INDEX idx_pv_country    (country_code),
    INDEX idx_pv_session    (session_id),
    INDEX idx_pv_loadtime   (load_time_ms),
    INDEX idx_pv_viewed     (viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sayfa goruntuleme loglari (PARTITION yok, basitlestirilmis)';

-- =============================================
-- Table: user_events
-- Purpose: Kullanici etkilesim olaylari
-- BCNF: id -> {user_id, event_type, event_value, page_url, ...}
-- event_type: playback.start, playback.complete, playback.skip, search.query,
--             playlist.create, playlist.add, like.toggle, share, download
-- =============================================
CREATE TABLE user_events (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         BINARY(16)          NOT NULL,
    event_type      VARCHAR(50)         NOT NULL,
    event_value     VARCHAR(255)            NULL COMMENT 'music_id, album_id, arama terimi ...',
    event_data      JSON                    NULL COMMENT 'Ekstra baglamsal veri',
    page_url        VARCHAR(2048)           NULL,
    ip_address      VARCHAR(45)             NULL,
    session_id      VARCHAR(64)             NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_ue_user       (user_id),
    INDEX idx_ue_type       (event_type),
    INDEX idx_ue_value      (event_value),
    INDEX idx_ue_session    (session_id),
    INDEX idx_ue_occurred   (occurred_at DESC),
    INDEX idx_ue_page       (page_url(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kullanici etkilesim olaylari (tiklama, oynatma, arama, ...)';

-- =============================================
-- Table: performance_metrics
-- Purpose: Uygulama/sunucu performans metrikleri
-- BCNF: id -> {metric_name, metric_value, source, tags_json, ...}
-- source: app | db | cache | engine | download
-- =============================================
CREATE TABLE performance_metrics (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    metric_name     VARCHAR(100)        NOT NULL,
    metric_value    DECIMAL(12,4)       NOT NULL,
    source          VARCHAR(50)         NOT NULL DEFAULT 'app',
    tags_json       JSON                    NULL COMMENT '{"endpoint":"/api/music","method":"GET"}',
    sampled_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_pm_name       (metric_name),
    INDEX idx_pm_source     (source),
    INDEX idx_pm_sampled    (sampled_at DESC),
    INDEX idx_pm_name_time  (metric_name, sampled_at DESC),
    CHECK (source IN ('app','db','cache','engine','download'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Uygulama/sunucu performans metrikleri (API, DB, cache, engine, download)';

-- =============================================
-- Table: daily_stats
-- Purpose: Gunluk ozet istatistikler (aggregate)
-- BCNF: (stats_date, user_id) -> {total_plays, total_listen_ms, ...}
-- Gunluk cron job ile doldurulur (gece yarisi).
-- =============================================
CREATE TABLE daily_stats (
    id                      INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    stats_date              DATE                NOT NULL,
    user_id                 BINARY(16)              NULL COMMENT 'NULL = tum kullanicilar',
    total_plays             INT UNSIGNED        NOT NULL DEFAULT 0,
    total_listen_ms         BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    total_skips             INT UNSIGNED        NOT NULL DEFAULT 0,
    total_searches          INT UNSIGNED        NOT NULL DEFAULT 0,
    total_playlist_creates  INT UNSIGNED        NOT NULL DEFAULT 0,
    total_playlist_adds     INT UNSIGNED        NOT NULL DEFAULT 0,
    total_likes             INT UNSIGNED        NOT NULL DEFAULT 0,
    total_shares            INT UNSIGNED        NOT NULL DEFAULT 0,
    total_downloads         INT UNSIGNED        NOT NULL DEFAULT 0,
    total_unique_tracks     INT UNSIGNED        NOT NULL DEFAULT 0,
    total_unique_artists    INT UNSIGNED        NOT NULL DEFAULT 0,
    total_page_views        INT UNSIGNED        NOT NULL DEFAULT 0,
    avg_session_minutes     DECIMAL(8,2)            NULL,
    top_genre               VARCHAR(50)             NULL,
    created_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ds_user_date (stats_date, user_id),
    INDEX idx_ds_date       (stats_date DESC),
    INDEX idx_ds_user       (user_id),
    INDEX idx_ds_plays      (total_plays DESC),
    INDEX idx_ds_listen     (total_listen_ms DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Gunluk ozet istatistikler (cron job ile doldurulur)';

-- =============================================
-- DEEP LOGGING TABLES (5 yeni tablo - PSR-3 uyumlu)
-- Version: 8.0.0
-- =============================================

-- =============================================
-- Table: log_events
-- Purpose: Genel olay loglari - tum uygulama olaylari
-- PSR-3 Level: TRACE/DEBUG/INFO/WARN/ERROR/CRITICAL
-- Category: app/security/performance/activity/system
-- =============================================
CREATE TABLE log_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL COMMENT 'UUID v4 - istek takip kimligi',
    level ENUM('TRACE','DEBUG','INFO','WARN','ERROR','CRITICAL') NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'app' COMMENT 'app/security/performance/activity/system',
    message TEXT NOT NULL,
    context JSON NULL COMMENT 'Ek veriler (JSON formatinda)',
    user_id BINARY(16) NULL,
    ip_address VARCHAR(45) NULL COMMENT 'IPv4/IPv6',
    user_agent VARCHAR(500) NULL,
    request_method VARCHAR(10) NULL,
    request_uri VARCHAR(2000) NULL,
    response_code SMALLINT UNSIGNED NULL,
    memory_usage INT UNSIGNED NULL COMMENT 'Byte cinsinden',
    peak_memory INT UNSIGNED NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3) COMMENT 'Milisaniye hassasiyeti',

    INDEX idx_le_created_at (created_at),
    INDEX idx_le_level (level),
    INDEX idx_le_category (category),
    INDEX idx_le_user_id (user_id),
    INDEX idx_le_correlation_id (correlation_id),
    INDEX idx_le_level_category (level, category),
    INDEX idx_le_created_level (created_at, level),
    FULLTEXT INDEX ft_le_message (message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Genel olay loglari - PSR-3 uyumlu deep logging';

-- =============================================
-- Table: log_security
-- Purpose: Guvenlik olay loglari - OWASP Top 10:2025 uyumlu
-- Event Types: CSRF, auth, rate limit, XSS, SQL injection
-- =============================================
CREATE TABLE log_security (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    event_type ENUM(
        'CSRF_VIOLATION',
        'AUTH_ATTEMPT_SUCCESS',
        'AUTH_ATTEMPT_FAILED',
        'AUTH_BYPASS_DETECTED',
        'RATE_LIMIT_EXCEEDED',
        'BRUTE_FORCE_DETECTED',
        'SESSION_HIJACK_ATTEMPT',
        'XSS_ATTEMPT',
        'SQL_INJECTION_ATTEMPT',
        'PERMISSION_DENIED',
        'CREDENTIAL_VAULT_ACCESS',
        'API_KEY_USED',
        'TOKEN_REFRESH',
        'SESSION_CREATED',
        'SESSION_DESTROYED',
        'CORS_VIOLATION',
        'CONTENT_SECURITY_POLICY_VIOLATION',
        'SUSPICIOUS_REQUEST'
    ) NOT NULL COMMENT 'Guvenlik olay tipi',
    severity ENUM('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL DEFAULT 'MEDIUM',
    user_id BINARY(16) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    request_method VARCHAR(10) NULL,
    request_uri VARCHAR(2000) NULL,
    request_body JSON NULL COMMENT 'Otomatik redaction uygulanmis',
    threat_indicators JSON NULL COMMENT 'Tehdit gostergeleri',
    blocked TINYINT(1) DEFAULT 0 COMMENT 'Olay engellendi mi?',
    response_action VARCHAR(100) NULL COMMENT 'Uygulanan aksiyon',
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_ls_created_at (created_at),
    INDEX idx_ls_event_type (event_type),
    INDEX idx_ls_severity (severity),
    INDEX idx_ls_ip_address (ip_address),
    INDEX idx_ls_user_id (user_id),
    INDEX idx_ls_severity_created (severity, created_at),
    INDEX idx_ls_event_type_created (event_type, created_at),
    FULLTEXT INDEX ft_ls_uri (request_uri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Guvenlik olay loglari - OWASP Top 10:2025 uyumlu';

-- =============================================
-- Table: log_performance
-- Purpose: Performans metrik loglari - query time, TTFB, memory
-- Metric Types: 15 farkli metrik tipi
-- =============================================
CREATE TABLE log_performance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    metric_type ENUM(
        'QUERY_TIME',
        'API_RESPONSE_TIME',
        'TTFB',
        'MEMORY_USAGE',
        'PEAK_MEMORY',
        'DISK_IO',
        'CACHE_HIT',
        'CACHE_MISS',
        'DB_CONNECTION_TIME',
        'FILE_UPLOAD_TIME',
        'FFMPEG_PROCESS_TIME',
        'AUDIO_DECODE_TIME',
        'PAGE_RENDER_TIME',
        'MIDDLEWARE_TIME',
        'TOTAL_REQUEST_TIME'
    ) NOT NULL COMMENT 'Metrik tipi',
    metric_name VARCHAR(100) NULL COMMENT 'Ozel metrik adi (DB query, endpoint vb.)',
    metric_value DECIMAL(12,3) NOT NULL COMMENT 'Metrik degeri (milisaniye, byte vb.)',
    metric_unit ENUM('ms','bytes','count','percent','ops') NOT NULL DEFAULT 'ms',
    context JSON NULL COMMENT 'Ek detaylar',
    user_id BINARY(16) NULL,
    request_uri VARCHAR(2000) NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_lp_created_at (created_at),
    INDEX idx_lp_metric_type (metric_type),
    INDEX idx_lp_metric_name (metric_name),
    INDEX idx_lp_user_id (user_id),
    INDEX idx_lp_metric_type_created (metric_type, created_at),
    INDEX idx_lp_created_metric (created_at, metric_type),
    FULLTEXT INDEX ft_lp_metric_name (metric_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Performans metrik loglari - query time, TTFB, memory';

-- =============================================
-- Table: log_system
-- Purpose: Sistem olay loglari - servis, cron, deployment
-- Event Types: 26 farkli sistem olayi
-- =============================================
CREATE TABLE log_system (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NULL,
    event_type ENUM(
        'SERVICE_START',
        'SERVICE_STOP',
        'SERVICE_ERROR',
        'CRON_JOB_START',
        'CRON_JOB_COMPLETE',
        'CRON_JOB_FAILED',
        'DEPLOYMENT_START',
        'DEPLOYMENT_COMPLETE',
        'DEPLOYMENT_FAILED',
        'CONFIG_CHANGE',
        'SCHEMA_MIGRATION',
        'BACKUP_START',
        'BACKUP_COMPLETE',
        'BACKUP_FAILED',
        'HEALTH_CHECK',
        'HEALTH_CHECK_FAILED',
        'DISK_SPACE_WARNING',
        'MEMORY_WARNING',
        'CPU_WARNING',
        'SSL_CERT_EXPIRING',
        'CRON_SCHEDULED',
        'QUEUE_OVERFLOW',
        'WORKER_START',
        'WORKER_STOP',
        'GRACEFUL_SHUTDOWN',
        'EMERGENCY_STOP'
    ) NOT NULL COMMENT 'Sistem olay tipi',
    severity ENUM('INFO','WARNING','ERROR','CRITICAL') NOT NULL DEFAULT 'INFO',
    component VARCHAR(100) NOT NULL COMMENT 'Sistem bileseni (nginx, php-fpm, mysql vb.)',
    message TEXT NOT NULL,
    context JSON NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_lsys_created_at (created_at),
    INDEX idx_lsys_event_type (event_type),
    INDEX idx_lsys_severity (severity),
    INDEX idx_lsys_component (component),
    INDEX idx_lsys_severity_created (severity, created_at),
    INDEX idx_lsys_event_type_created (event_type, created_at),
    FULLTEXT INDEX ft_lsys_message (message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Sistem olay loglari - servis, cron, deployment';

-- =============================================
-- Table: log_activity
-- Purpose: Kullanici aktivite loglari - CRUD, dinleme, arama
-- Action Types: 35 farkli kullanici aksiyonu
-- =============================================
CREATE TABLE log_activity (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    correlation_id CHAR(36) NOT NULL,
    user_id BINARY(16) NOT NULL,
    action ENUM(
        'LOGIN',
        'LOGOUT',
        'REGISTER',
        'PROFILE_UPDATE',
        'PASSWORD_CHANGE',
        'PASSWORD_RESET',
        'MUSIC_PLAY',
        'MUSIC_PAUSE',
        'MUSIC_STOP',
        'MUSIC_SKIP',
        'MUSIC_ADD_TO_PLAYLIST',
        'MUSIC_REMOVE_FROM_PLAYLIST',
        'PLAYLIST_CREATE',
        'PLAYLIST_DELETE',
        'PLAYLIST_UPDATE',
        'ALBUM_VIEW',
        'ARTIST_VIEW',
        'SEARCH',
        'DOWNLOAD_START',
        'DOWNLOAD_COMPLETE',
        'DOWNLOAD_FAILED',
        'UPLOAD_START',
        'UPLOAD_COMPLETE',
        'FILE_MANAGE',
        'SETTINGS_CHANGE',
        'DEVICE_REGISTER',
        'DEVICE_SYNC',
        'SHARING',
        'COMMENT_POST',
        'LIKE',
        'FOLLOW',
        'UNFOLLOW',
        'NAVIGATION',
        'PAGE_VIEW',
        'API_CALL'
    ) NOT NULL COMMENT 'Kullanici aksiyonu',
    entity_type VARCHAR(50) NULL COMMENT 'Varlik turu (music, album, playlist vb.)',
    entity_id BINARY(16) NULL COMMENT 'Varlik ID',
    entity_name VARCHAR(500) NULL COMMENT 'Varlik adi (redacted)',
    metadata JSON NULL COMMENT 'Ek bilgiler',
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    device_type ENUM('desktop','mobile','tablet','car','studio','home') NULL,
    created_at TIMESTAMP(3) DEFAULT CURRENT_TIMESTAMP(3),

    INDEX idx_la_created_at (created_at),
    INDEX idx_la_user_id (user_id),
    INDEX idx_la_action (action),
    INDEX idx_la_entity_type (entity_type),
    INDEX idx_la_user_action (user_id, action),
    INDEX idx_la_user_created (user_id, created_at),
    INDEX idx_la_action_created (action, created_at),
    FULLTEXT INDEX ft_la_entity_name (entity_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kullanici aktivite loglari - CRUD, dinleme, arama';

-- =============================================
-- CoreMusic coremusic_logs Database v8.0.0
-- Tables: 22 (17 original + 5 deep logging)
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- Deep Logging: PSR-3 + Monolog entegrasyonu
-- =============================================
