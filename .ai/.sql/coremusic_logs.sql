-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_logs — SİSTEM LOG TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5.1 → v6 DÜZELTMELERİ]
-- 1. [DÜZELTMESİ] logs_auth_events CHECK (ip_type IN (..., NULL)):
--    MySQL'de NULL IN() içinde geçersiz söz dizimidir.
--    Düzeltme: CHECK (ip_type IS NULL OR ip_type IN (...))
--    Aynı sorun logs_user_activity'de de yoktu (ip_type CHECK yoktu) — dokunulmadı.
--
-- [v5.0 → v5.1 korunan — IP Logging Mimarisi]
-- 2. ip_remote, ip_isp_static, ip_isp_cgnat_dynamic, ip_type, source_port, mac_address
--    sütun tasarımı ve açıklamaları korundu.
-- ══════════════════════════════════════════════════════════════════════════════
--
-- ════ IP LOGGING MİMARİSİ ════════════════════════════════════════════════════
--
-- ip_remote (VARCHAR 45): PHP $_SERVER['REMOTE_ADDR'] — TCP karşı ucu
-- ip_isp_static (VARCHAR 45): Statik IP / XFF header
-- ip_isp_cgnat_dynamic (VARCHAR 45): Dinamik/CGNAT — 100.64.0.0/10 (RFC 6598)
-- ip_type (VARCHAR 20): static | cgnat | dynamic | private | ipv6 | vpn_proxy | unknown
-- source_port (SMALLINT UNSIGNED): RFC 6302 CGNAT izlemesi için zorunlu
-- mac_address (VARCHAR 17): SADECE LOCAL/LAN MOD — internet'ten alınamaz
--
-- ═════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_logs
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_logs;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_DEVICE_SYNC — Cihaz senkronizasyon geçmişi (audit log)
-- Normal Form : BCNF
-- Tüm FK'lar cross-DB — MySQL desteklemez. Uygulama seviyesinde doğrula.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_device_sync (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    device_id           INT UNSIGNED        NOT NULL,   -- → coremusic_media.media_devices.id
    device_playlist_id  INT UNSIGNED            NULL,   -- → coremusic_media.media_device_playlists.id
    user_id             INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    action              VARCHAR(30)         NOT NULL,
    -- action: export_started | export_completed | export_failed | track_copied | track_failed
    music_id            INT UNSIGNED            NULL,   -- → coremusic_musics.musics.id
    detail              TEXT                    NULL,
    occurred_at         DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_dsl_device        (device_id, occurred_at DESC),
    INDEX idx_dsl_playlist      (device_playlist_id),
    INDEX idx_dsl_user          (user_id),
    INDEX idx_dsl_music         (music_id),
    INDEX idx_dsl_action        (action),
    INDEX idx_dsl_occurred      (occurred_at),

    CHECK (action IN ('export_started','export_completed','export_failed','track_copied','track_failed'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_USER_ACTIVITY — Kullanıcı aktivite logu (genel)
-- Normal Form : BCNF
-- entity_type : music | album | playlist | singer | device
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_user_activity (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    action          VARCHAR(50)         NOT NULL,
    -- action: play | pause | skip | like | unlike | add_to_playlist | remove_from_playlist
    --         follow_singer | unfollow_singer | export_device | search
    entity_type     VARCHAR(30)             NULL,
    -- music | album | playlist | singer | device
    entity_id       INT UNSIGNED            NULL,
    -- IP alanları — yukarıdaki IP Logging Mimarisi bölümüne bakın
    ip_remote               VARCHAR(45)     NULL,   -- REMOTE_ADDR
    ip_isp_static           VARCHAR(45)     NULL,   -- Statik/public IP
    ip_isp_cgnat_dynamic    VARCHAR(45)     NULL,   -- Dinamik/CGNAT IP
    ip_type                 VARCHAR(20)     NULL,
    -- static | cgnat | dynamic | private | ipv6 | vpn_proxy | unknown
    source_port             SMALLINT UNSIGNED NULL,  -- TCP kaynak portu (RFC 6302)
    user_agent      VARCHAR(512)            NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_ual_user          (user_id, occurred_at DESC),
    INDEX idx_ual_entity        (entity_type, entity_id),
    INDEX idx_ual_action        (action),
    INDEX idx_ual_ip            (ip_isp_static),
    INDEX idx_ual_ip_cgnat      (ip_isp_cgnat_dynamic),
    INDEX idx_ual_occurred      (occurred_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_SEARCH — Arama log tablosu
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_search (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED            NULL,   -- NULL = anonim arama
    query_text      VARCHAR(500)        NOT NULL,
    result_count    INT UNSIGNED        NOT NULL DEFAULT 0,
    search_type     VARCHAR(20)         NOT NULL DEFAULT 'all',
    -- search_type: all | music | singer | album | playlist
    ip_remote       VARCHAR(45)             NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_ls_user       (user_id),
    INDEX idx_ls_query      (query_text(100)),
    INDEX idx_ls_occurred   (occurred_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_AUTH_EVENTS — Kimlik doğrulama olayları
-- Normal Form : BCNF
-- (v4'teki logs_user_login yerine — tam tasarım)
--
-- IP sütunları için yukarıdaki "IP LOGGING MİMARİSİ" bölümünü oku.
--
-- event_type değerleri:
--   login_success | login_failed | logout | session_expired
--   password_reset_request | password_reset_complete
--   email_verify_request | email_verified
--   2fa_challenge | 2fa_success | 2fa_failed
--   account_locked | account_unlocked | token_refresh
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_auth_events (
    id                      BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id                 INT UNSIGNED            NULL,
    -- NULL = bilinmeyen kullanıcı (login_failed, brute force)
    event_type              VARCHAR(40)         NOT NULL,

    -- ── IP ─────────────────────────────────────────────────────────────────
    ip_remote               VARCHAR(45)             NULL,
    -- PHP REMOTE_ADDR — TCP bağlantısının doğrudan karşı ucu
    ip_isp_static           VARCHAR(45)             NULL,
    -- Statik ISP IP: XFF/X-Real-IP'ten alınan veya ip_remote (proxy yoksa)
    ip_isp_cgnat_dynamic    VARCHAR(45)             NULL,
    -- Dinamik/CGNAT IP: genellikle ip_remote ile aynı; CGNAT varsa paylaşılmış public IP
    ip_type                 VARCHAR(20)             NULL,
    -- static | cgnat | dynamic | private | ipv6 | vpn_proxy | unknown
    -- cgnat tespiti: 100.64.0.0/10 (RFC 6598) — kesin CGNAT aralığı
    source_port             SMALLINT UNSIGNED       NULL,
    -- TCP kaynak portu (RFC 6302). CGNAT'ta ISP izlemesi için zorunlu.
    -- ── MAC ─────────────────────────────────────────────────────────────────
    mac_address             VARCHAR(17)             NULL,
    -- "AA:BB:CC:DD:EE:FF" formatında. SADECE local/LAN mod.
    -- Internet üzerinden alınamaz (OSI Layer 2, router'da düşer).
    -- ── Diğer ───────────────────────────────────────────────────────────────
    user_agent              VARCHAR(512)            NULL,
    device_type             VARCHAR(20)             NULL,
    -- web | mobile | desktop | tablet
    session_id              VARCHAR(128)            NULL,
    -- Oluşturulan veya sonlanan PHP session ID (COREMUSIC_SESSION)
    fail_reason             VARCHAR(255)            NULL,
    -- login_failed için: wrong_password | user_not_found | account_banned | account_inactive
    country_code            CHAR(2)                 NULL,
    -- IP geolocation ile elde edilir (async / periyodik enrichment)
    is_suspicious           TINYINT(1)          NOT NULL DEFAULT 0,
    -- 1 = anormal davranış tespiti (rate limit aşımı, farklı ülke vb.)
    occurred_at             DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_lae_user          (user_id, occurred_at DESC),
    INDEX idx_lae_event         (event_type),
    INDEX idx_lae_ip_remote     (ip_remote),
    INDEX idx_lae_ip_static     (ip_isp_static),
    INDEX idx_lae_ip_cgnat      (ip_isp_cgnat_dynamic),
    INDEX idx_lae_ip_type       (ip_type),
    INDEX idx_lae_mac           (mac_address),
    INDEX idx_lae_session       (session_id),
    INDEX idx_lae_suspicious    (is_suspicious),
    INDEX idx_lae_occurred      (occurred_at),

    CHECK (event_type IN (
        'login_success','login_failed','logout','session_expired',
        'password_reset_request','password_reset_complete',
        'email_verify_request','email_verified',
        '2fa_challenge','2fa_success','2fa_failed',
        'account_locked','account_unlocked','token_refresh'
    )),
    -- ⚠️ DÜZELTİLDİ (v6): NULL IN() MySQL'de geçersiz. IS NULL OR IN() kullanıldı.
    CHECK (ip_type IS NULL OR ip_type IN ('static','cgnat','dynamic','private','ipv6','vpn_proxy','unknown')),
    CHECK (device_type IS NULL OR device_type IN ('web','mobile','desktop','tablet'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_AUDIT — Admin panel aksiyonları
-- Normal Form : BCNF
-- AuditLogger.php → include/class/system.security/AuditLogger.php
-- action format: {module}.{operation} — örn: music.deleted, user.banned
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_audit (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    admin_id        INT UNSIGNED        NOT NULL,   -- → coremusic_auth.admins.id
    action          VARCHAR(100)        NOT NULL,
    -- {module}.{operation}: music.deleted, user.banned, system.config_changed
    target_type     VARCHAR(50)             NULL,
    -- music | album | playlist | singer | user | admin | system
    target_id       INT UNSIGNED            NULL,
    old_value       LONGTEXT                NULL,   -- JSON — değişim öncesi
    new_value       LONGTEXT                NULL,   -- JSON — değişim sonrası
    ip_remote       VARCHAR(45)             NULL,
    user_agent      VARCHAR(512)            NULL,
    notes           TEXT                    NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_la_admin      (admin_id, occurred_at DESC),
    INDEX idx_la_action     (action),
    INDEX idx_la_target     (target_type, target_id),
    INDEX idx_la_occurred   (occurred_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_RATE_LIMIT — Rate limiting geçmişi ve ihlalleri
-- Normal Form : BCNF
-- RateLimiter.php → include/class/system.security/RateLimiter.php
-- config.php: RATE_LIMIT_MAX = 60, RATE_LIMIT_WINDOW = 60 (saniye)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_rate_limit (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    identifier      VARCHAR(128)        NOT NULL,
    -- IP adresi veya "user:123" formatında user key
    identifier_type VARCHAR(20)         NOT NULL DEFAULT 'ip',
    -- ip | user | api_key
    endpoint        VARCHAR(255)        NOT NULL,
    request_count   INT UNSIGNED        NOT NULL DEFAULT 1,
    window_start    DATETIME            NOT NULL,
    is_blocked      TINYINT(1)          NOT NULL DEFAULT 0,
    blocked_until   DATETIME                NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_lrl_identifier    (identifier, endpoint, window_start),
    INDEX idx_lrl_blocked       (is_blocked, blocked_until),
    INDEX idx_lrl_occurred      (occurred_at),

    CHECK (identifier_type IN ('ip','user','api_key'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- LOGS_APP_ERRORS — Uygulama hataları ve PHP exception logları
-- Normal Form : BCNF
-- include/class/system.exception/ → 8 exception sınıfı:
--   AuthException, AuthorizationException, ConfigException, CoreMusicException,
--   DatabaseException, RouterException, SecurityException, ValidationException
-- coremusic_php_errors.log (file-based, proje kökü) kritikler buraya da yazılır.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE logs_app_errors (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    error_level     VARCHAR(20)         NOT NULL DEFAULT 'error',
    -- debug | info | notice | warning | error | critical | alert | emergency
    error_code      VARCHAR(100)            NULL,
    -- Exception sınıf adı: DatabaseException, SecurityException vb.
    message         TEXT                NOT NULL,
    file_path       VARCHAR(512)            NULL,
    line_no         INT UNSIGNED            NULL,
    stack_trace     LONGTEXT                NULL,
    context_json    JSON                    NULL,
    user_id         INT UNSIGNED            NULL,
    admin_id        INT UNSIGNED            NULL,
    ip_remote       VARCHAR(45)             NULL,
    request_uri     VARCHAR(2048)           NULL,
    request_method  VARCHAR(10)             NULL,
    -- GET | POST | PUT | DELETE | PATCH
    is_resolved     TINYINT(1)          NOT NULL DEFAULT 0,
    resolved_by     INT UNSIGNED            NULL,
    resolved_at     DATETIME                NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_ler_level     (error_level),
    INDEX idx_ler_code      (error_code),
    INDEX idx_ler_user      (user_id),
    INDEX idx_ler_resolved  (is_resolved),
    INDEX idx_ler_occurred  (occurred_at),
    FULLTEXT KEY ft_ler_msg (message),

    CHECK (error_level IN ('debug','info','notice','warning','error','critical','alert','emergency')),
    CHECK (request_method IS NULL OR request_method IN ('GET','POST','PUT','DELETE','PATCH','HEAD','OPTIONS'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
