-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_neva — NEVA ENGINE DSP & EKOLAYZER TABLOLARI
-- COREMUSIC DB v1.0 | Temmuz 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- TABLOLAR:
--   eq_presets          → Ekolayzer preset tanımları (31-band / 10-band / parametric)
--   dsp_settings        → DSP efekt zinciri ayarları (compressor, reverb, gain, ...)
--   routing_matrix      → Ses yönlendirme matrisi (giriş → efekt → çıkış)
--   spectrum_analysis   → Frekans spektrumu örnekleme verileri
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_neva
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_neva;


-- ──────────────────────────────────────────────────────────────────────────────
-- EQ_PRESETS — Ekolayzer preset tanımları
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, preset_name, band_count, bands_json, ...}
--
-- bands_json formatı (31-band örnek):
-- [{"hz":20,"gain":0},{"hz":25,"gain":0},...,{"hz":20000,"gain":0}]
-- gain değerleri: -12.0 ile +12.0 dB arası (0.5 adımlarla)
--
-- eq_type: graphic | parametric | shelving | peak
-- band_count: 2 | 5 | 10 | 15 | 31
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
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_eqp_user_name   (user_id, preset_name),
    INDEX idx_eqp_user         (user_id),
    INDEX idx_eqp_type         (eq_type),
    INDEX idx_eqp_active       (user_id, is_active),
    INDEX idx_eqp_system       (is_system),
    INDEX idx_eqp_order        (sort_order),

    CHECK (eq_type    IN ('graphic','parametric','shelving','peak')),
    CHECK (band_count IN (2, 5, 10, 15, 31))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- DSP_SETTINGS — DSP efekt zinciri ayarları
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, slot_order, effect_type, params_json, is_bypassed}
--
-- slot_order: efekt zincirindeki sıra (0 = ilk)
-- effect_type:
--   equalizer   → Ana EQ (preset_id ile referans)
--   compressor  → threshold, ratio, attack_ms, release_ms, makeup_gain
--   gate        → threshold, attack_ms, release_ms, hold_ms
--   reverb      → room_size, damping, wet_dry, pre_delay_ms
--   delay       → time_ms, feedback, wet_dry
--   chorus      → rate_hz, depth, delay_ms, wet_dry
--   spatial     → width, center_pct, stereo_phase
--   limiter     → ceiling_db, attack_ms, release_ms
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
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_dsp_slot   (user_id, slot_order),
    INDEX idx_dsp_user        (user_id),
    INDEX idx_dsp_type        (effect_type),
    INDEX idx_dsp_order       (slot_order),
    INDEX idx_dsp_bypass      (is_bypassed),

    CHECK (slot_order  BETWEEN 0 AND 15),
    CHECK (effect_type IN ('equalizer','compressor','gate','reverb','delay','chorus','spatial','limiter'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ROUTING_MATRIX — Ses yönlendirme matrisi
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, route_name, input_source, output_destination, ...}
--
-- input_source: system | microphone | file | stream | plugin
-- output_destination: speakers | headphones | bluetooth | file | stream
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
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_rm_user_name   (user_id, route_name),
    INDEX idx_rm_user         (user_id),
    INDEX idx_rm_input        (input_source),
    INDEX idx_rm_output       (output_destination),
    INDEX idx_rm_active       (is_active),

    CHECK (input_source        IN ('system','microphone','file','stream','plugin')),
    CHECK (output_destination  IN ('speakers','headphones','bluetooth','file','stream')),
    CHECK (gain_db BETWEEN -60.00 AND 24.00)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SPECTRUM_ANALYSIS — Frekans spektrumu örnekleme verileri
-- Normal Form : BCNF
-- Bağımlılık  : id → {session_id, bin_count, fft_size, spectrum_json, ...}
--
-- Bu tablo, Neva Engine'in real-time FFT spektrum verilerini zaman damgalı
-- olarak depolar. Genellikle görselleştirme için kullanılır.
-- Periyodik temizleme: sampled_at < NOW() - INTERVAL 24 HOUR → DELETE
--
-- spectrum_json format: {"bins":[0.12,0.45,0.78,...,0.05],"max_freq":24000}
-- bins sayısı = bin_count (128 | 256 | 512 | 1024)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE spectrum_analysis (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    session_id      VARCHAR(64)         NOT NULL,   -- Oturum/sunucu kimliği
    user_id         INT UNSIGNED        NOT NULL,
    bin_count       SMALLINT UNSIGNED   NOT NULL DEFAULT 256,
    fft_size        INT UNSIGNED        NOT NULL DEFAULT 4096,
    sample_rate_hz  INT UNSIGNED        NOT NULL DEFAULT 48000,
    min_freq_hz     INT UNSIGNED        NOT NULL DEFAULT 20,
    max_freq_hz     INT UNSIGNED        NOT NULL DEFAULT 24000,
    spectrum_json   JSON                NOT NULL,   -- FFT bin verileri
    rms_level_db    DECIMAL(5,2)            NULL,   -- RMS ses seviyesi (dB)
    peak_level_db   DECIMAL(5,2)            NULL,   -- Tepe ses seviyesi (dB)
    sampled_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_sa_session    (session_id),
    INDEX idx_sa_user       (user_id),
    INDEX idx_sa_sampled    (sampled_at DESC),
    INDEX idx_sa_bins       (bin_count),

    CHECK (bin_count   IN (128, 256, 512, 1024)),
    CHECK (fft_size    IN (1024, 2048, 4096, 8192, 16384)),
    CHECK (sample_rate_hz IN (44100, 48000, 96000, 192000)),
    CHECK (rms_level_db  IS NULL OR (rms_level_db  BETWEEN -96.00 AND 24.00)),
    CHECK (peak_level_db IS NULL OR (peak_level_db BETWEEN -96.00 AND 24.00))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- ══════════════════════════════════════════════════════════════════════════════

-- Sistem EQ preset'leri (is_system=1, user_id=0)
INSERT INTO eq_presets (user_id, preset_name, eq_type, band_count, bands_json, is_active, is_system, sort_order) VALUES
(0, 'Flat (Varsayılan)', 'graphic', 10,
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
(0, 'Bas Arttır', 'graphic', 10,
 '[{"hz":32,"gain":6},{"hz":64,"gain":5},{"hz":125,"gain":4},{"hz":250,"gain":2},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":0},{"hz":8000,"gain":0},{"hz":16000,"gain":0}]',
 0, 1, 5),
(0, 'Vokal', 'graphic', 10,
 '[{"hz":32,"gain":-2},{"hz":64,"gain":-1},{"hz":125,"gain":0},{"hz":250,"gain":1},{"hz":500,"gain":2},{"hz":1000,"gain":3},{"hz":2000,"gain":3},{"hz":4000,"gain":2},{"hz":8000,"gain":1},{"hz":16000,"gain":0}]',
 0, 1, 6);

-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_neva schema
