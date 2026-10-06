-- ══════════════════════════════════════════════════════════════
-- coremusic_ai — YAPAY ZEKA & ÖNERİ SİSTEMİ VERİTABANI
-- Version     : 8.0.0
-- BCNF        : Yes
-- Author      : CoreMusic Data Engineer
-- Date        : 2026-08-10
-- Tables      : 6
-- Engine      : InnoDB
-- Charset     : utf8mb4
-- Collation   : utf8mb4_unicode_ci
-- UUID        : BINARY(16)
-- Soft Delete : is_deleted + deleted_at
-- ══════════════════════════════════════════════════════════════
-- Cross-DB Dependencies:
--   user_id       → coremusic_auth.users(id)
--   music_id      → coremusic_musics.musics(id)
--   model_version_id → coremusic_ai.model_versions(id)
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_ai
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_ai;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_PREFERENCE_PROFILES — Kullanici tercih profilleri
-- Normal Form : BCNF — user_id UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_preference_profiles (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    genre_weights   JSON                    NULL,
    mood_preferences JSON                   NULL,
    tempo_preference VARCHAR(20)             NULL,
    energy_preference DECIMAL(3,2)           NULL,
    acoustic_preference DECIMAL(3,2)         NULL,
    diversity_score DECIMAL(3,2)             NULL,
    last_analyzed_at DATETIME                NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_upp_user    (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FK: user_id → coremusic_auth.users(id)


-- ──────────────────────────────────────────────────────────────────────────────
-- LISTENING_FEATURES — Dinleme ozellikleri (ML features)
-- Normal Form : BCNF — (user_id, music_id) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE listening_features (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    music_id        INT UNSIGNED        NOT NULL,
    features        JSON                    NULL,
    skip_rate       DECIMAL(3,2)            NULL,
    listen_ratio    DECIMAL(3,2)            NULL,
    repeat_count    INT UNSIGNED        NOT NULL DEFAULT 0,
    last_played_at  DATETIME                NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_lf_pair     (user_id, music_id),
    INDEX idx_lf_music       (music_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FK: user_id  → coremusic_auth.users(id)
-- FK: music_id → coremusic_musics.musics(id)


-- ──────────────────────────────────────────────────────────────────────────────
-- RECOMMENDATION_HISTORY — Oneri gecmisi
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE recommendation_history (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    music_id        INT UNSIGNED        NOT NULL,
    algorithm       VARCHAR(30)         NOT NULL,
    model_version   VARCHAR(50)             NULL,
    confidence      DECIMAL(3,2)            NULL,
    feedback        VARCHAR(20)         NOT NULL DEFAULT 'none',
    position        TINYINT UNSIGNED        NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_rh_user       (user_id),
    INDEX idx_rh_music      (music_id),
    INDEX idx_rh_algorithm  (algorithm),
    INDEX idx_rh_feedback   (feedback),
    INDEX idx_rh_created    (created_at DESC),
    CHECK (algorithm IN ('collaborative', 'content_based', 'hybrid', 'ai_curated')),
    CHECK (feedback IN ('none', 'liked', 'disliked', 'skipped', 'saved'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FK: user_id  → coremusic_auth.users(id)
-- FK: music_id → coremusic_musics.musics(id)


-- ──────────────────────────────────────────────────────────────────────────────
-- AUDIO_FEATURES — Ses analiz ozellikleri
-- Normal Form : BCNF — music_id UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE audio_features (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id        INT UNSIGNED        NOT NULL,
    bpm             SMALLINT UNSIGNED       NULL,
    key_signature   VARCHAR(10)             NULL,
    mode            VARCHAR(5)              NULL,
    time_signature  VARCHAR(10)             NULL,
    energy          DECIMAL(3,2)            NULL,
    danceability    DECIMAL(3,2)            NULL,
    valence         DECIMAL(3,2)            NULL,
    acousticness    DECIMAL(3,2)            NULL,
    instrumentalness DECIMAL(3,2)           NULL,
    liveness        DECIMAL(3,2)            NULL,
    speechiness     DECIMAL(3,2)            NULL,
    loudness_db     DECIMAL(6,2)            NULL,
    features        JSON                    NULL,
    analyzed_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_af_music    (music_id),
    INDEX idx_af_bpm        (bpm),
    INDEX idx_af_key        (key_signature)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FK: music_id → coremusic_musics.musics(id)


-- ──────────────────────────────────────────────────────────────────────────────
-- MODEL_VERSIONS — AI model versiyonlari
-- Normal Form : BCNF — (model_name, version) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE model_versions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    model_name      VARCHAR(100)        NOT NULL,
    version         VARCHAR(50)         NOT NULL,
    model_type      VARCHAR(30)         NOT NULL,
    description     TEXT                    NULL,
    accuracy_score  DECIMAL(5,4)            NULL,
    f1_score        DECIMAL(5,4)            NULL,
    training_data_size BIGINT UNSIGNED       NULL,
    training_duration_seconds INT UNSIGNED   NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'training',
    deployed_at     DATETIME                NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_mv_pair     (model_name, version),
    INDEX idx_mv_type       (model_type),
    INDEX idx_mv_status     (status),
    CHECK (model_type IN ('recommendation', 'audio_analysis', 'nlp', 'classification')),
    CHECK (status IN ('training', 'active', 'deprecated', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- TRAINING_JOBS — Egitim isleri
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE training_jobs (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    model_version_id INT UNSIGNED       NOT NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'queued',
    started_at      DATETIME                NULL,
    completed_at    DATETIME                NULL,
    error_message   TEXT                    NULL,
    metrics         JSON                    NULL,
    gpu_hours       DECIMAL(8,2)            NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_tj_model      (model_version_id),
    INDEX idx_tj_status     (status),
    CHECK (status IN ('queued', 'running', 'completed', 'failed', 'cancelled'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FK: model_version_id → coremusic_ai.model_versions(id)


-- ══════════════════════════════════════════════════════════════
-- coremusic_ai — 6 tablo tamamlandı
-- ══════════════════════════════════════════════════════════════
