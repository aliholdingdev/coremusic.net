-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_musics — SANATÇI & ŞARKI TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v4 → v5 DÜZELTİLEN HATALAR]
-- 1. [KRİTİK] music_singer_roles: `id` tip tanımsız → CREATE TABLE çalışmazdı.
--    PK, FK, UNIQUE KEY, INDEX yoktu. Tam tanım eklendi.
-- 2. [KRİTİK] similar_singers: `id` tip tanımsız, PK/FK yoktu, trailing comma.
--    Tam tanım eklendi. CHECK: singer_id <> similar_singer_id.
-- 3. [KRİTİK] singer_stats: `id` tip tanımsız, PK/FK yoktu.
--    Ek sorun: play_count + like_count + share_count ile
--    total_play_count + follower_count + popularity_score çakışıyordu (duplikasyon).
--    Karar: id kaldırıldı (1:1 ilişki → singer_id PK, album_stats ile tutarlı).
--    play_count → total_play_count ile birleştirildi.
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_musics
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_musics;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_SINGER — Sanatçı profilleri
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_singer (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    singer_name     VARCHAR(255)        NOT NULL,
    bio             TEXT                    NULL,
    start_year      SMALLINT UNSIGNED       NULL,
    country_code    CHAR(2)                 NULL,   -- ISO 3166-1: TR, US, GB, KR
    profile_img_url VARCHAR(2048)           NULL,
    cover_img_url   VARCHAR(2048)           NULL,
    is_verified     TINYINT(1)          NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    INDEX idx_ms_name       (singer_name),
    INDEX idx_ms_active     (is_active),
    INDEX idx_ms_deleted    (deleted_at),
    INDEX idx_ms_country    (country_code),
    FULLTEXT KEY ft_ms_name (singer_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_SINGER_ROLES — Sanatçı ↔ Rol (M2M)
-- Normal Form : BCNF — (singer_id, role_id) UNIQUE
-- ⚠️ role_id → coremusic_catalog.singer_role_list(id) — cross-DB, app doğrulaması
--
-- [v5 DÜZELTMESİ]: id tip tanımsızdı → INT UNSIGNED AUTO_INCREMENT yapıldı.
--   PRIMARY KEY, UNIQUE KEY, FK, INDEX eklendi.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_singer_roles (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    singer_id   INT UNSIGNED        NOT NULL,
    role_id     TINYINT UNSIGNED    NOT NULL,
    -- → coremusic_catalog.singer_role_list(id) — cross-DB, uygulama doğrulaması

    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_msr_pair     (singer_id, role_id),
    FOREIGN KEY fk_msr_singer   (singer_id) REFERENCES music_singer (id) ON DELETE CASCADE,

    INDEX idx_msr_role          (role_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SINGER_GENRES — Sanatçı ↔ Tür (M2M)
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE singer_genres (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    singer_id   INT UNSIGNED        NOT NULL,
    genre_id    SMALLINT UNSIGNED   NOT NULL,
    is_primary  TINYINT(1)          NOT NULL DEFAULT 0,

    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_sg_pair      (singer_id, genre_id),
    FOREIGN KEY fk_sg_singer    (singer_id) REFERENCES music_singer (id) ON DELETE CASCADE,

    INDEX idx_sg_genre          (genre_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SIMILAR_SINGERS — Benzer sanatçılar (M2M self-reference)
-- Normal Form : BCNF
--
-- [v5 DÜZELTMESİ]: id tip tanımsızdı, PK/FK yoktu, trailing comma vardı.
--   Tam tanım eklendi. CHECK: singer_id <> similar_singer_id (kendine benzer olamaz).
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE similar_singers (
    id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    singer_id           INT UNSIGNED    NOT NULL,
    similar_singer_id   INT UNSIGNED    NOT NULL,
    similarity_score    DECIMAL(5,4)    NOT NULL DEFAULT 0.0000,
    -- 0.0000 = benzerlik yok | 1.0000 = tam benzer

    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ss_pair      (singer_id, similar_singer_id),
    FOREIGN KEY fk_ss_singer    (singer_id)         REFERENCES music_singer (id) ON DELETE CASCADE,
    FOREIGN KEY fk_ss_similar   (similar_singer_id) REFERENCES music_singer (id) ON DELETE CASCADE,

    INDEX idx_ss_similar        (similar_singer_id),
    INDEX idx_ss_score          (similarity_score DESC),

    CHECK (singer_id <> similar_singer_id),
    CHECK (similarity_score >= 0.0000 AND similarity_score <= 1.0000)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SINGER_STATS — Sanatçı istatistikleri (yüksek yazım frekanslı)
-- Normal Form : BCNF — (1:1 → singer_id PK ve FK)
--
-- [v5 DÜZELTMESİ]:
--   - `id` satırı kaldırıldı (1:1 ilişki → singer_id PK, album_stats ile tutarlı)
--   - play_count + like_count + share_count (v4 duplikasyonu) kaldırıldı
--   - total_play_count, like_count, share_count tek sütun olarak birleştirildi
--   - PRIMARY KEY (singer_id), FK, INDEX eklendi
--
-- monthly_listeners  : Son 28 gündeki tekil dinleyici — haftalık job günceller
-- follower_count     : coremusic_user.user_follows_singers aggregate
-- total_play_count   : Sanatçının tüm şarkılarının oynatma toplamı
-- popularity_score   : Weighted skor (play × freshness × follower_growth)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE singer_stats (
    singer_id           INT UNSIGNED        NOT NULL,
    follower_count      BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    monthly_listeners   BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    total_play_count    BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    like_count          BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    share_count         BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    popularity_score    DECIMAL(8,4)        NOT NULL DEFAULT 0.0000,

    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (singer_id),
    FOREIGN KEY fk_sst_singer (singer_id) REFERENCES music_singer (id) ON DELETE CASCADE,

    INDEX idx_sst_play      (total_play_count DESC),
    INDEX idx_sst_popular   (popularity_score DESC),
    INDEX idx_sst_followers (follower_count DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSICS — Şarkılar
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE musics (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_name      VARCHAR(500)        NOT NULL,
    duration_sec    INT UNSIGNED        NOT NULL DEFAULT 0,
    content_type    VARCHAR(20)         NOT NULL DEFAULT 'audio',
    -- content_type: audio | video | podcast | audiobook
    is_duet         TINYINT(1)          NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    INDEX idx_m_name        (music_name),
    INDEX idx_m_type        (content_type),
    INDEX idx_m_active      (is_active),
    INDEX idx_m_deleted     (deleted_at),
    INDEX idx_m_duet        (is_duet),
    FULLTEXT KEY ft_m_name  (music_name),

    CHECK (content_type IN ('audio','video','podcast','audiobook'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_TRACK_SINGER — Şarkı ↔ Sanatçı (M2M) | Düet & Feat
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_track_singer (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id        INT UNSIGNED        NOT NULL,
    singer_id       INT UNSIGNED        NOT NULL,
    role            VARCHAR(30)         NOT NULL DEFAULT 'main_artist',
    is_duet         TINYINT(1)          NOT NULL DEFAULT 0,
    artist_order    TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_mts_pair     (music_id, singer_id),
    FOREIGN KEY fk_mts_music    (music_id)  REFERENCES musics       (id) ON DELETE CASCADE,
    FOREIGN KEY fk_mts_singer   (singer_id) REFERENCES music_singer (id) ON DELETE CASCADE,

    INDEX idx_mts_singer    (singer_id),
    INDEX idx_mts_role      (role),
    INDEX idx_mts_duet      (is_duet),

    CHECK (role IN ('main_artist','featured','producer','composer','remixer','lyricist','conductor'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_GENRE — Şarkı ↔ Tür (M2M)
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_genre (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id    INT UNSIGNED        NOT NULL,
    genre_id    SMALLINT UNSIGNED   NOT NULL,
    is_primary  TINYINT(1)          NOT NULL DEFAULT 0,
    is_active   TINYINT(1)          NOT NULL DEFAULT 1,

    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    deleted_at  DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_mg_pair  (music_id, genre_id),
    FOREIGN KEY fk_mg_music (music_id) REFERENCES musics (id) ON DELETE CASCADE,

    INDEX idx_mg_genre      (genre_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_DETAIL — Şarkı teknik & metadata (1:1)
-- Normal Form : BCNF — music_id PK (1:1, consistent with music_stats/singer_stats)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_detail (
    music_id        INT UNSIGNED        NOT NULL,   -- PK + FK (1:1, consistent with stats tables)
    language_code   CHAR(5)                 NULL,
    bpm             SMALLINT UNSIGNED       NULL,
    musical_key     VARCHAR(10)             NULL,
    isrc            CHAR(12)                NULL,
    release_year    SMALLINT UNSIGNED       NULL,
    release_date    DATE                    NULL,
    explicit        TINYINT(1)          NOT NULL DEFAULT 0,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (music_id),
    UNIQUE  KEY uq_md_isrc  (isrc),
    FOREIGN KEY fk_md_music (music_id) REFERENCES musics (id) ON DELETE CASCADE,

    INDEX idx_md_lang       (language_code),
    INDEX idx_md_release    (release_date)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_LYRICS — Şarkı sözleri
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_lyrics (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id        INT UNSIGNED        NOT NULL,
    language_code   CHAR(5)             NOT NULL DEFAULT 'tr',
    lyrics_format   VARCHAR(20)         NOT NULL DEFAULT 'plain',
    -- lyrics_format: plain | lrc (senkronize/karaoke) | srt
    lyrics_text     LONGTEXT                NULL,
    lyrics_url      VARCHAR(2048)           NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ml_lang  (music_id, language_code),
    FOREIGN KEY fk_ml_music (music_id) REFERENCES musics (id) ON DELETE CASCADE,

    INDEX idx_ml_music      (music_id),
    INDEX idx_ml_active     (is_active)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_DETAIL_FILES — Şarkı dosyaları (farklı kalite/format)
-- Normal Form : BCNF
-- file_size : BIGINT UNSIGNED BYTE. Dönüşüm app'te:
--   ≥1TB→"X.X TB" | ≥1GB→"X.XX GB" | ≥1MB→"X.XX MB" | else→"X KB"
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_detail_files (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id        INT UNSIGNED        NOT NULL,
    lyrics_id       INT UNSIGNED            NULL,
    file_type       VARCHAR(20)         NOT NULL DEFAULT 'audio',
    -- file_type: audio | video | thumbnail | waveform
    quality_label   VARCHAR(30)             NULL,   -- low | medium | high | lossless | hd_video
    bitrate_kbps    SMALLINT UNSIGNED       NULL,   -- 64, 128, 192, 320
    sample_rate_hz  MEDIUMINT UNSIGNED      NULL,   -- 44100, 48000, 96000
    bit_depth       TINYINT UNSIGNED        NULL,   -- 16, 24, 32
    mime_type       VARCHAR(100)            NULL,   -- audio/mpeg, audio/flac, video/mp4
    file_url        VARCHAR(2048)       NOT NULL,
    file_size       BIGINT UNSIGNED         NULL,   -- BYTE cinsinden
    checksum_sha256 CHAR(64)                NULL,
    duration_sec    INT UNSIGNED            NULL,
    width_px        SMALLINT UNSIGNED       NULL,
    height_px       SMALLINT UNSIGNED       NULL,
    metadata_json   JSON                    NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY fk_mdf_music    (music_id)  REFERENCES musics       (id) ON DELETE CASCADE,
    FOREIGN KEY fk_mdf_lyrics   (lyrics_id) REFERENCES music_lyrics (id) ON DELETE SET NULL,

    UNIQUE KEY uq_mdf_quality   (music_id, file_type, quality_label),
    INDEX idx_mdf_music         (music_id),
    INDEX idx_mdf_type          (file_type),
    INDEX idx_mdf_active        (is_active),

    CHECK (file_type IN ('audio','video','thumbnail','waveform'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_STATS — Şarkı istatistikleri (1:1, yüksek yazım frekanslı)
-- Normal Form : BCNF
-- ⚠️ Hesaplanmış veri saklanmaz. SADECE atomic increment sayaçları.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_stats (
    music_id            INT UNSIGNED        NOT NULL,
    play_count          BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    like_count          BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    share_count         BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    skip_count          BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    playlist_add_count  BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    completion_rate     DECIMAL(5,4)        NOT NULL DEFAULT 0.0000,
    popularity_score    DECIMAL(8,4)        NOT NULL DEFAULT 0.0000,

    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (music_id),
    FOREIGN KEY fk_mst_music (music_id) REFERENCES musics (id) ON DELETE CASCADE,

    INDEX idx_mst_play      (play_count DESC),
    INDEX idx_mst_popular   (popularity_score DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
