-- ══════════════════════════════════════════════════════════════
-- coremusic_neva — NEVA ENGINE DSP & EKOLAYZER VERİTABANI
-- Version     : 8.0.0
-- BCNF        : Yes
-- Author      : CoreMusic Data Engineer
-- Date        : 2026-08-10
-- Tables      : 4
-- Seed Data   : 7 EQ presets
-- Engine      : InnoDB
-- Charset     : utf8mb4
-- Collation   : utf8mb4_unicode_ci
-- UUID        : BINARY(16)
-- Soft Delete : is_deleted + deleted_at (spectrum_analysis hariç)
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_neva
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_neva;

-- ──────────────────────────────────────────────────────────────────────────────
-- EQ_PRESETS — Ekolayzer preset tanimlari
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE eq_presets (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    preset_name     VARCHAR(100)        NOT NULL,
    eq_type         VARCHAR(20)         NOT NULL DEFAULT 'graphic',
    band_count      TINYINT UNSIGNED    NOT NULL DEFAULT 10,
    bands_json      JSON                NOT NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 0,
    is_system       TINYINT(1)          NOT NULL DEFAULT 0,
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_eqp_user_name   (user_id, preset_name),
    INDEX idx_eqp_user         (user_id),
    INDEX idx_eqp_type         (eq_type),
    INDEX idx_eqp_active       (user_id, is_active),
    INDEX idx_eqp_system       (is_system),
    INDEX idx_eqp_order        (sort_order),
    CHECK (eq_type    IN ('graphic','parametric','shelving','peak')),
    CHECK (band_count IN (2, 5, 10, 15, 31))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- DSP_SETTINGS — DSP efekt zinciri ayarlari
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE dsp_settings (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    slot_order      TINYINT UNSIGNED    NOT NULL,
    effect_type     VARCHAR(20)         NOT NULL,
    params_json     JSON                NOT NULL,
    is_bypassed     TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_dsp_slot   (user_id, slot_order),
    INDEX idx_dsp_user        (user_id),
    INDEX idx_dsp_type        (effect_type),
    INDEX idx_dsp_order       (slot_order),
    INDEX idx_dsp_bypass      (is_bypassed),
    CHECK (slot_order  BETWEEN 0 AND 15),
    CHECK (effect_type IN ('equalizer','compressor','gate','reverb','delay','chorus','spatial','limiter'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- ROUTING_MATRIX — Ses yonlendirme matrisi
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE routing_matrix (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    route_name          VARCHAR(100)        NOT NULL,
    input_source        VARCHAR(30)         NOT NULL,
    input_channel       TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    output_destination  VARCHAR(30)         NOT NULL,
    output_channel      TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    gain_db             DECIMAL(5,2)        NOT NULL DEFAULT 0.00,
    is_muted            TINYINT(1)          NOT NULL DEFAULT 0,
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_rm_user_name   (user_id, route_name),
    INDEX idx_rm_user         (user_id),
    INDEX idx_rm_input        (input_source),
    INDEX idx_rm_output       (output_destination),
    INDEX idx_rm_active       (is_active),
    CHECK (input_source        IN ('system','microphone','file','stream','plugin')),
    CHECK (output_destination  IN ('speakers','headphones','bluetooth','file','stream')),
    CHECK (gain_db BETWEEN -60.00 AND 24.00)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- SPECTRUM_ANALYSIS — Frekans spektrumu ornekleme verileri
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE spectrum_analysis (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    session_id      VARCHAR(64)         NOT NULL,
    user_id         INT UNSIGNED        NOT NULL,
    bin_count       SMALLINT UNSIGNED   NOT NULL DEFAULT 256,
    fft_size        INT UNSIGNED        NOT NULL DEFAULT 4096,
    sample_rate_hz  INT UNSIGNED        NOT NULL DEFAULT 48000,
    min_freq_hz     INT UNSIGNED        NOT NULL DEFAULT 20,
    max_freq_hz     INT UNSIGNED        NOT NULL DEFAULT 24000,
    spectrum_json   JSON                NOT NULL,
    rms_level_db    DECIMAL(5,2)            NULL,
    peak_level_db   DECIMAL(5,2)            NULL,
    sampled_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_sa_session    (session_id),
    INDEX idx_sa_user       (user_id),
    INDEX idx_sa_sampled    (sampled_at DESC),
    INDEX idx_sa_bins       (bin_count),
    CHECK (bin_count   IN (128, 256, 512, 1024)),
    CHECK (fft_size    IN (1024, 2048, 4096, 8192, 16384)),
    CHECK (sample_rate_hz IN (44100, 48000, 96000, 192000)),
    CHECK (rms_level_db  IS NULL OR (rms_level_db  BETWEEN -96.00 AND 24.00)),
    CHECK (peak_level_db IS NULL OR (peak_level_db BETWEEN -96.00 AND 24.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- VARSAYILAN VERILER — NEVA EQ PRESETS
-- =============================================
INSERT INTO eq_presets (user_id, preset_name, eq_type, band_count, bands_json, is_active, is_system, sort_order) VALUES
(0, 'Flat (Varsayilan)', 'graphic', 10,
 '[{"hz":32,"gain":0},{"hz":64,"gain":0},{"hz":125,"gain":0},{"hz":250,"gain":0},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":0},{"hz":8000,"gain":0},{"hz":16000,"gain":0}]',
 0, 1, 0),
(0, 'Pop', 'graphic', 10,
 '[{"hz":32,"gain":1},{"hz":64,"gain":2},{"hz":125,"gain":2},{"hz":250,"gain":0},{"hz":500,"gain":-1},{"hz":1000,"gain":0},{"hz":2000,"gain":1},{"hz":4000,"gain":2},{"hz":8000,"gain":3},{"hz":16000,"gain":3}]',
 0, 1, 1),
(0, 'Rock', 'graphic', 10,
 '[{"hz":32,"gain":4},{"hz":64,"gain":3},{"hz":125,"gain":2},{"hz":250,"gain":1},{"hz":500,"gain":0},{"hz":1000,"gain":-1},{"hz":2000,"gain":0},{"hz":4000,"gain":2},{"hz":8000,"gain":3},{"hz":16000,"gain":4}]',
 0, 1, 2),
(0, 'Jazz', 'graphic', 10,
 '[{"hz":32,"gain":2},{"hz":64,"gain":2},{"hz":125,"gain":1},{"hz":250,"gain":1},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":1},{"hz":4000,"gain":1},{"hz":8000,"gain":2},{"hz":16000,"gain":2}]',
 0, 1, 3),
(0, 'Klasik', 'graphic', 10,
 '[{"hz":32,"gain":3},{"hz":64,"gain":2},{"hz":125,"gain":1},{"hz":250,"gain":0},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":1},{"hz":8000,"gain":2},{"hz":16000,"gain":3}]',
 0, 1, 4),
(0, 'Bas Arttir', 'graphic', 10,
 '[{"hz":32,"gain":6},{"hz":64,"gain":5},{"hz":125,"gain":4},{"hz":250,"gain":2},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":0},{"hz":8000,"gain":0},{"hz":16000,"gain":0}]',
 0, 1, 5),
(0, 'Vokal', 'graphic', 10,
 '[{"hz":32,"gain":-2},{"hz":64,"gain":-1},{"hz":125,"gain":0},{"hz":250,"gain":1},{"hz":500,"gain":2},{"hz":1000,"gain":3},{"hz":2000,"gain":3},{"hz":4000,"gain":2},{"hz":8000,"gain":1},{"hz":16000,"gain":0}]',
 0, 1, 6);

-- =============================================
-- coremusic_neva Database v8.0.0
-- Tables: 4 (eq_presets, dsp_settings, routing_matrix, spectrum_analysis)
-- BCNF Compliant: Yes
-- Soft Delete: is_deleted + deleted_at (spectrum_analysis hariç)
-- Collation: utf8mb4_unicode_ci
-- =============================================

-- End of coremusic_neva schema
