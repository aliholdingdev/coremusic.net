-- ══════════════════════════════════════════════════════════════
-- coremusic_studio — STÜDYO KAYIT YÖNETİMİ VERİTABANI
-- Version     : 8.0.0
-- BCNF        : Yes
-- Author      : CoreMusic Data Engineer
-- Date        : 2026-08-10
-- Tables      : 6
-- Engine      : InnoDB
-- Charset     : utf8mb4
-- Collation   : utf8mb4_unicode_ci
-- UUID        : INT UNSIGNED AUTO_INCREMENT
-- Soft Delete : is_deleted
-- Source      : coremusic_system.sql BÖLÜM 4 (lines 641-789)
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_studio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_studio;

-- =============================================
-- BÖLÜM 1: STÜDYO (6 tablo)
-- =============================================

-- ──────────────────────────────────────────────────────────────────────────────
-- STUDIO_SESSIONS — Kayit oturumlari
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE studio_sessions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    host_user_id    INT UNSIGNED        NOT NULL,
    name            VARCHAR(255)        NOT NULL,
    description     TEXT                    NULL,
    cover_image     VARCHAR(2048)           NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'draft',
    bpm             SMALLINT UNSIGNED       NULL,
    key_signature   VARCHAR(10)             NULL,
    time_signature  VARCHAR(10)         NOT NULL DEFAULT '4/4',
    sample_rate     INT UNSIGNED        NOT NULL DEFAULT 48000,
    bit_depth       TINYINT UNSIGNED    NOT NULL DEFAULT 24,
    total_tracks    INT UNSIGNED        NOT NULL DEFAULT 0,
    duration_seconds INT UNSIGNED           NULL,
    session_path    VARCHAR(2048)           NULL,
    is_public       TINYINT(1)          NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_ss_host       (host_user_id),
    INDEX idx_ss_status     (status),
    INDEX idx_ss_public     (is_public),
    CHECK (status IN ('draft', 'recording', 'mixing', 'mastering', 'completed', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- STUDIO_TRACKS — Parcalar
-- Normal Form : BCNF
-- FK          : session_id → studio_sessions.id
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE studio_tracks (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    session_id      INT UNSIGNED        NOT NULL,
    name            VARCHAR(255)        NOT NULL,
    instrument      VARCHAR(50)             NULL,
    track_number    TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    audio_url       VARCHAR(2048)           NULL,
    audio_format    VARCHAR(10)             NULL,
    duration_seconds INT UNSIGNED           NULL,
    sample_rate     INT UNSIGNED        NOT NULL DEFAULT 48000,
    bit_depth       TINYINT UNSIGNED    NOT NULL DEFAULT 24,
    channels        TINYINT UNSIGNED    NOT NULL DEFAULT 2,
    is_muted        TINYINT(1)          NOT NULL DEFAULT 0,
    is_solo         TINYINT(1)          NOT NULL DEFAULT 0,
    volume_db       DECIMAL(6,2)        NOT NULL DEFAULT 0.00,
    pan_percent     DECIMAL(5,2)        NOT NULL DEFAULT 0.00,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_st_session    (session_id),
    INDEX idx_st_instrument (instrument),
    INDEX idx_st_number     (session_id, track_number),
    CHECK (channels IN (1, 2, 4, 6, 8))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- STUDIO_PRESETS — Ses preset'leri
-- Normal Form : BCNF — (user_id, name) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE studio_presets (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    name            VARCHAR(255)        NOT NULL,
    description     TEXT                    NULL,
    preset_type     VARCHAR(30)         NOT NULL,
    settings        JSON                NOT NULL,
    is_public       TINYINT(1)          NOT NULL DEFAULT 0,
    use_count       INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_sp_pair    (user_id, name),
    INDEX idx_sp_type       (preset_type),
    INDEX idx_sp_public     (is_public),
    CHECK (preset_type IN ('eq', 'compressor', 'reverb', 'delay', 'chain', 'master'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- STUDIO_EQUIPMENT — Ekipman envanteri
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE studio_equipment (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    brand           VARCHAR(100)        NOT NULL,
    model           VARCHAR(255)        NOT NULL,
    serial_number   VARCHAR(255)            NULL,
    equip_type      VARCHAR(50)         NOT NULL,
    description     TEXT                    NULL,
    purchase_date   DATE                    NULL,
    purchase_price  DECIMAL(10,2)           NULL,
    is_available    TINYINT(1)          NOT NULL DEFAULT 1,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_se_type       (equip_type),
    INDEX idx_se_brand      (brand),
    INDEX idx_se_available  (is_available),
    CHECK (equip_type IN ('microphone', 'interface', 'headphone', 'monitor',
                          'midi', 'controller', 'cable', 'other'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- SESSION_EQUIPMENT — Oturum-ekipman eslestirme (junction)
-- Normal Form : BCNF — (session_id, equipment_id) UNIQUE
-- FK          : session_id → studio_sessions.id
-- FK          : equipment_id → studio_equipment.id
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE session_equipment (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    session_id      INT UNSIGNED        NOT NULL,
    equipment_id    INT UNSIGNED        NOT NULL,
    assigned_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_se_pair    (session_id, equipment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- STUDIO_COLLABORATORS — Isbirlikci yonetimi
-- Normal Form : BCNF — (session_id, user_id) UNIQUE
-- FK          : session_id → studio_sessions.id
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE studio_collaborators (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    session_id      INT UNSIGNED        NOT NULL,
    user_id         INT UNSIGNED        NOT NULL,
    role            VARCHAR(30)         NOT NULL DEFAULT 'musician',
    is_accepted     TINYINT(1)          NOT NULL DEFAULT 0,
    joined_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    left_at         DATETIME                NULL,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_sc_pair    (session_id, user_id),
    INDEX idx_sc_user       (user_id),
    INDEX idx_sc_role       (role),
    CHECK (role IN ('owner', 'producer', 'engineer', 'musician',
                    'vocalist', 'mixer', 'mastering'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- coremusic_studio v8.0.0
-- Tables: 6
-- BCNF Compliant: Yes
-- Source: coremusic_system.sql BÖLÜM 4
-- Collation: utf8mb4_unicode_ci
-- ══════════════════════════════════════════════════════════════

-- End of coremusic_studio schema
