-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_api — API YÖNETİMİ & RATE LİMİT TABLOLARI
-- COREMUSIC DB v1.0 | Temmuz 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- TABLOLAR:
--   api_keys            → API anahtarları, scope'lar, yetkilendirme
--   rate_limits         → Rate limit kuralları (per-key endpoint bazlı)
--   api_calls           → API çağrı logları (AYLIK PARTITION)
--   webhooks            → Webhook kayıtları (events, callback URL, status)
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_api
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_api;


-- ──────────────────────────────────────────────────────────────────────────────
-- API_KEYS — API anahtarları ve scope'lar
-- Normal Form : BCNF
-- Bağımlılık  : id → {api_key_hash, key_label, scope, allowed_ips, ...}
--
-- ⚠️ GÜVENLİK: api_key_hash = SHA-256(ham_anahtar). Ham anahtar ASLA DB'de saklanmaz.
--    api_key_prefix: "cm_live_" | "cm_test_" + ilk 8 karakter (UI'da göstermek için)
-- scope: virgülle ayrılmış yetki alanları (ör: "music.read,music.write,user.read")
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE api_keys (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    api_key_hash        CHAR(64)            NOT NULL,   -- SHA-256 hex
    api_key_prefix      VARCHAR(20)         NOT NULL,   -- cm_live_XXXX | cm_test_XXXX
    key_label           VARCHAR(255)        NOT NULL,
    scope               VARCHAR(1024)       NOT NULL,   -- virgülle ayrılmış yetki alanları
    allowed_ips         TEXT                    NULL,   -- JSON array: ["192.168.1.0/24","10.0.0.1"]
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,
    expires_at          DATETIME                NULL,
    last_used_at        DATETIME                NULL,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ak_hash        (api_key_hash),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_ak_user        (user_id),
    INDEX idx_ak_active      (is_active),
    INDEX idx_ak_expires     (expires_at),
    INDEX idx_ak_last_used   (last_used_at DESC),
    INDEX idx_ak_deleted     (deleted_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- RATE_LIMITS — Rate limit kuralları (API anahtarı + endpoint bazlı)
-- Normal Form : BCNF
-- Bağımlılık  : id → {api_key_id, endpoint_pattern, max_requests, window_sec}
--
-- endpoint_pattern: "/api/v[0-9]+/music/*" gibi glob pattern
-- window_sec: zaman dilimi saniyesi (60 = dakikalık, 3600 = saatlik, 86400 = günlük)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE rate_limits (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    api_key_id      INT UNSIGNED        NOT NULL,
    endpoint_pattern VARCHAR(500)       NOT NULL,
    max_requests    INT UNSIGNED        NOT NULL DEFAULT 60,
    window_sec      INT UNSIGNED        NOT NULL DEFAULT 60,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_rl_key (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE,

    UNIQUE  KEY uq_rl_key_endpoint (api_key_id, endpoint_pattern(255)),
    INDEX idx_rl_active        (is_active),
    INDEX idx_rl_endpoint      (endpoint_pattern(255))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- API_CALLS — API çağrı logları (AYLIK PARTITION)
-- Normal Form : BCNF
-- Bağımlılık  : id → {api_key_id, endpoint, method, http_status, ...}
--
-- ⚠️ PARTITION: AYLIK RANGE partitioning (called_at tarihine göre).
--    Veri saklama: çağrı anlık log — 6 ay sonra temizlenebilir (app kararı).
--    Yılda bir: ALTER TABLE api_calls DROP PARTITION p_YYYY_MM;
--
-- PARTITION ADI FORMATI: p_YYYY_MM
--   Örnek: p_2026_07, p_2026_08, ...
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE api_calls (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    api_key_id      INT UNSIGNED            NULL,
    user_id         INT UNSIGNED            NULL,
    endpoint        VARCHAR(500)        NOT NULL,
    method          VARCHAR(10)         NOT NULL,
    http_status     SMALLINT UNSIGNED   NOT NULL,
    ip_address      VARCHAR(45)             NULL,
    user_agent      VARCHAR(512)            NULL,
    response_time_ms INT UNSIGNED           NULL,
    request_body    TEXT                    NULL,
    error_message   TEXT                    NULL,
    called_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id, called_at),
    -- ⚠️ api_key_id FK kaldırıldı: MySQL partition + FK desteklemez
    -- Uygulama seviyesinde doğrula (app-level validation)

    INDEX idx_ac_key        (api_key_id),
    INDEX idx_ac_user       (user_id),
    INDEX idx_ac_endpoint   (endpoint(255)),
    INDEX idx_ac_status     (http_status),
    INDEX idx_ac_method     (method),
    INDEX idx_ac_response   (response_time_ms),
    INDEX idx_ac_ip         (ip_address)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci
PARTITION BY RANGE (TO_DAYS(called_at)) (
    PARTITION p_2026_01 VALUES LESS THAN (TO_DAYS('2026-02-01')),
    PARTITION p_2026_02 VALUES LESS THAN (TO_DAYS('2026-03-01')),
    PARTITION p_2026_03 VALUES LESS THAN (TO_DAYS('2026-04-01')),
    PARTITION p_2026_04 VALUES LESS THAN (TO_DAYS('2026-05-01')),
    PARTITION p_2026_05 VALUES LESS THAN (TO_DAYS('2026-06-01')),
    PARTITION p_2026_06 VALUES LESS THAN (TO_DAYS('2026-07-01')),
    PARTITION p_2026_07 VALUES LESS THAN (TO_DAYS('2026-08-01')),
    PARTITION p_2026_08 VALUES LESS THAN (TO_DAYS('2026-09-01')),
    PARTITION p_2026_09 VALUES LESS THAN (TO_DAYS('2026-10-01')),
    PARTITION p_2026_10 VALUES LESS THAN (TO_DAYS('2026-11-01')),
    PARTITION p_2026_11 VALUES LESS THAN (TO_DAYS('2026-12-01')),
    PARTITION p_2026_12 VALUES LESS THAN (TO_DAYS('2027-01-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);


-- ──────────────────────────────────────────────────────────────────────────────
-- WEBHOOKS — Webhook kayıtları
-- Normal Form : BCNF
-- Bağımlılık  : id → {api_key_id, event_type, callback_url, ...}
--
-- event_type: kaynak olay türü (ör: "download.completed", "music.created", "user.updated")
-- callback_url: POST isteği gönderilecek URL
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE webhooks (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    api_key_id      INT UNSIGNED        NOT NULL,
    event_type      VARCHAR(100)        NOT NULL,
    callback_url    VARCHAR(2048)       NOT NULL,
    secret          VARCHAR(255)            NULL,   -- HMAC imzalama anahtarı
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    retry_count     TINYINT UNSIGNED    NOT NULL DEFAULT 3,
    timeout_ms      INT UNSIGNED        NOT NULL DEFAULT 5000,
    last_triggered_at DATETIME              NULL,
    last_http_status  SMALLINT UNSIGNED     NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_wh_key (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE,

    INDEX idx_wh_event      (event_type),
    INDEX idx_wh_active     (is_active),
    INDEX idx_wh_last       (last_triggered_at),

    CHECK (retry_count BETWEEN 0 AND 10),
    CHECK (timeout_ms BETWEEN 1000 AND 30000)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_api schema
