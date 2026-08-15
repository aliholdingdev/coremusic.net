-- coremusic_download — İNDİRME SERVİSİ VERİTABANI
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-10
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- PK: INT UNSIGNED AUTO_INCREMENT (download tabloları INT korunur)
-- Soft Delete: Yok (download logları kalıcı)
-- Timestamps: created_at, updated_at (tablolar arası farklılık gösterebilir)
--
-- Cross-DB Foreign Keys:
--   download_queue.user_id    → coremusic_auth.users.id
--   download_queue.track_id   → coremusic_musics.musics.id
--   download_history.user_id  → coremusic_auth.users.id
--   download_history.track_id → coremusic_musics.musics.id
--   download_sources.user_id  → coremusic_auth.users.id
--
-- NOT: Bu dosya coremusic_media.sql'den ayrıştırılmıştır.
--     4 tablo: download_queue, download_history, download_cache, download_sources

CREATE DATABASE IF NOT EXISTS coremusic_download
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_download;

-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 1: İNDİRME SERVİSİ (4 tablo)
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- DOWNLOAD_QUEUE — Aktif indirme iş kuyruğu
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, track_id, source, url, status, priority, ...}
--
-- status: queued → processing → completed | failed
-- priority: 0 (en yüksek) ile 255 (en düşük) arası
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE download_queue (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    track_id        INT UNSIGNED        NOT NULL,
    source          VARCHAR(20)         NOT NULL,
    url             VARCHAR(2048)       NOT NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'queued',
    priority        TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    error_message   TEXT                    NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at      DATETIME                NULL,
    completed_at    DATETIME                NULL,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK
    -- ⚠️ track_id → coremusic_musics.musics.id: cross-DB FK

    INDEX idx_dq_user         (user_id),
    INDEX idx_dq_track        (track_id),
    INDEX idx_dq_status       (status),
    INDEX idx_dq_priority     (user_id, priority DESC),
    INDEX idx_dq_source       (source),
    INDEX idx_dq_created      (created_at DESC),

    CHECK (source IN ('deezer','youtube','spotify','soundcloud')),
    CHECK (status  IN ('queued','processing','completed','failed')),
    CHECK (priority BETWEEN 0 AND 255)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- DOWNLOAD_HISTORY — Tamamlanan indirme arşivi
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, track_id, source, file_hash, file_size_bytes, ...}
--
-- file_hash: SHA-256 hex, kayıp dosya tespiti için
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE download_history (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    track_id        INT UNSIGNED        NOT NULL,
    source          VARCHAR(20)         NOT NULL,
    file_hash       CHAR(64)            NOT NULL,
    file_size_bytes BIGINT UNSIGNED     NOT NULL,
    duration_ms     INT UNSIGNED            NULL,
    quality         VARCHAR(20)             NULL,
    downloaded_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK
    -- ⚠️ track_id → coremusic_musics.musics.id: cross-DB FK

    INDEX idx_dh_user        (user_id),
    INDEX idx_dh_track       (track_id),
    INDEX idx_dh_source      (source),
    INDEX idx_dh_downloaded  (user_id, downloaded_at DESC),
    INDEX idx_dh_hash        (file_hash),

    CHECK (source IN ('deezer','youtube','spotify','soundcloud'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- DOWNLOAD_CACHE — Önbelleğe alınmış dosya referansları
-- Normal Form : BCNF
-- Bağımlılık  : id → {file_hash, source, source_id, file_path, size_bytes, ...}
--
-- Aynı dosya hash'i farklı kullanıcılar için tekrar indirilirse bu tablo kullanılır.
-- access_count: önbellek temizleme stratejisi için (LRU benzeri)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE download_cache (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    file_hash       CHAR(64)            NOT NULL,
    source          VARCHAR(20)         NOT NULL,
    source_id       VARCHAR(255)        NOT NULL,
    file_path       VARCHAR(512)        NOT NULL,
    size_bytes      BIGINT UNSIGNED     NOT NULL,
    cached_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_accessed_at DATETIME               NULL,
    access_count    INT UNSIGNED        NOT NULL DEFAULT 0,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_dc_hash           (file_hash),

    INDEX idx_dc_source       (source),
    INDEX idx_dc_source_id    (source, source_id),
    INDEX idx_dc_accessed     (last_accessed_at DESC),
    INDEX idx_dc_count        (access_count DESC),

    CHECK (source IN ('deezer','youtube','spotify','soundcloud'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- DOWNLOAD_SOURCES — Kaynak başına API kimlik bilgileri
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, source, api_key_encrypted, ...}
--
-- ⚠️ GÜVENLİK: api_key_encrypted ve api_secret_encrypted AES-256-GCM şifreli.
--    ASLA plain text saklanmamalı. Şifreleme anahtarı environment variable'da.
--    Kullanıcı başına her kaynak için en fazla 1 kayıt.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE download_sources (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    source              VARCHAR(20)         NOT NULL,
    api_key_encrypted   VARBINARY(512)      NOT NULL,
    api_secret_encrypted VARBINARY(512)         NULL,
    quota_remaining     INT UNSIGNED            NULL,
    quota_reset_at      DATETIME                NULL,
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ds_user_source      (user_id, source),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_ds_active       (is_active),
    INDEX idx_ds_source       (source),
    INDEX idx_ds_quota        (quota_remaining),

    CHECK (source IN ('deezer','youtube','spotify','soundcloud'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- coremusic_download Database v8.0.0
-- Tables: 4 (download_queue, download_history, download_cache, download_sources)
-- BCNF Compliant: Yes
-- PK: INT UNSIGNED AUTO_INCREMENT
-- Soft Delete: No (download logs are permanent)
-- Collation: utf8mb4_unicode_ci
-- =============================================

-- End of coremusic_download schema
