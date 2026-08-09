-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_albums — ALBÜM TABLOLARI
-- COREMUSIC DB v5.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v3 → v4 DÜZELTİLEN HATALAR]
-- 1. [KRİTİK] album_stats tablosunda:
--    "id" satırı vardı ama tip tanımı yoktu → CREATE TABLE çalışmazdı.
--    album_stats'ın PK'sı album_id (1:1 ilişki). Surrogate id gereksiz.
--    Karar: `id` satırı kaldırıldı. PK = album_id (zaten doğru yapı).
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_albums
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_albums;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUMS — Albümler
-- Normal Form : BCNF
-- Bağımlılık  : id → {album_name, album_type_id, release_year, cover_img_url, ...}
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE albums (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    album_name      VARCHAR(500)        NOT NULL,
    album_desc      TEXT                    NULL,
    album_type_id   TINYINT UNSIGNED        NULL,   -- FK → coremusic_catalog.album_types
    release_year    SMALLINT UNSIGNED       NULL,
    release_date    DATE                    NULL,
    cover_img_url   VARCHAR(2048)           NULL,
    label           VARCHAR(255)            NULL,   -- Plak şirketi
    total_tracks    SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    -- ⚠️ total_tracks: Bilinçli denormalizasyon (hız için).
    -- album_musics'e INSERT/DELETE her yapıldığında bu sayacı da güncelle.
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    INDEX idx_al_name       (album_name),
    INDEX idx_al_year       (release_year),
    INDEX idx_al_release    (release_date),
    INDEX idx_al_active     (is_active),
    INDEX idx_al_deleted    (deleted_at),
    FULLTEXT KEY ft_al_name (album_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUM_SINGERS — Albüm ↔ Sanatçı (M2M)
-- Normal Form : BCNF — (album_id, singer_id) composite PK
-- 1NF Düzeltme: albums.singer_id tekil → junction tablo
-- role: main_artist | featured | producer | composer | remixer
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE album_singers (
    album_id        INT UNSIGNED        NOT NULL,
    singer_id       INT UNSIGNED        NOT NULL,   -- → coremusic_musics.music_singer.id
    role            VARCHAR(30)         NOT NULL DEFAULT 'main_artist',
    singer_order    TINYINT UNSIGNED    NOT NULL DEFAULT 0,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (album_id, singer_id),
    FOREIGN KEY fk_als_album (album_id) REFERENCES albums (id) ON DELETE CASCADE,
    -- ⚠️ singer_id → coremusic_musics DB: cross-DB FK yok.

    INDEX idx_als_singer    (singer_id),
    CHECK (role IN ('main_artist','featured','producer','composer','remixer'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUM_GENRES — Albüm ↔ Tür (M2M)
-- Normal Form : BCNF — (album_id, genre_id) composite PK
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE album_genres (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    album_id    INT UNSIGNED        NOT NULL,
    genre_id    SMALLINT UNSIGNED   NOT NULL,   -- → coremusic_catalog.genre_list.id
    is_primary  TINYINT(1)          NOT NULL DEFAULT 0,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ag_pair  (album_id, genre_id),
    FOREIGN KEY fk_ag_album (album_id) REFERENCES albums (id) ON DELETE CASCADE,

    INDEX idx_ag_genre      (genre_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUM_MUSICS — Albüm ↔ Şarkı (M2M) | Sıra + disk desteği
-- Normal Form : BCNF
-- Not         : WHERE album_id = $id → o albümün şarkılarını getirir.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE album_musics (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    album_id        INT UNSIGNED        NOT NULL,
    music_id        INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    disc_number     TINYINT UNSIGNED    NOT NULL DEFAULT 1,
    track_order     SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_am_pair      (album_id, music_id),
    UNIQUE  KEY uq_am_position  (album_id, disc_number, track_order),
    FOREIGN KEY fk_am_album     (album_id) REFERENCES albums (id) ON DELETE CASCADE,
    -- ⚠️ music_id → coremusic_musics DB: cross-DB FK yok.

    INDEX idx_am_music          (music_id),
    INDEX idx_am_active         (is_active)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUM_STATS — Albüm istatistikleri (yüksek yazım frekanslı)
-- Normal Form : BCNF — (1:1 → album_id PK ve FK)
-- ⚠️ HESAPLANMIŞ VERİ SAKLANMAZ:
--   music_count    → COUNT(*) FROM album_musics WHERE album_id = ?
--   total_duration → SUM(m.duration_sec) FROM album_musics JOIN coremusic_musics.musics m
-- [v4 DÜZELTMESİ]: `id` satırı kaldırıldı — tip tanımsızdı, syntax hatasıydı.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE album_stats (
    album_id        INT UNSIGNED        NOT NULL,   -- PK + FK
    play_count      BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    like_count      BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    follower_count  BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    share_count     BIGINT UNSIGNED     NOT NULL DEFAULT 0,

    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (album_id),
    FOREIGN KEY fk_ast_album (album_id) REFERENCES albums (id) ON DELETE CASCADE,

    INDEX idx_ast_plays (play_count DESC),
    INDEX idx_ast_likes (like_count DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;