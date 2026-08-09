-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_playlist — PLAYLIST TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5.0 → v5.1 DÜZELTMESİ]
-- 1. [KRİTİK] playlist_stats: `id` satırı upload edilen v5.0 dosyasının 142.
--    satırında hâlâ bare (tip yok) kalmıştı → CREATE TABLE çalışmazdı.
--    Bu satır tamamen kaldırıldı. PK = playlist_id (1:1, album_stats ile tutarlı).
--
-- [v4 → v5 korunan düzeltmeler]
-- 2. playlist_collaborators: id tip tanımsız, PK/FK/INDEX/parantez eksikti → düzeltildi.
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_playlist
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_playlist;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 1: NORMAL PLAYLIST SİSTEMİ
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLISTS — Kullanıcı çalma listeleri
-- Normal Form : BCNF
-- Bağımlılık  : id → {playlist_name, owner_user_id, playlist_type_id, ...}
-- playlist_type_id → coremusic_catalog.playlist_types(id) — cross-DB, app doğrulaması
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlists (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    playlist_name       VARCHAR(500)        NOT NULL,
    playlist_desc       TEXT                    NULL,
    playlist_type_id    TINYINT UNSIGNED        NULL,
    owner_user_id       INT UNSIGNED        NOT NULL,
    cover_img_url       VARCHAR(2048)           NULL,
    source_year         SMALLINT UNSIGNED       NULL,
    is_public           TINYINT(1)          NOT NULL DEFAULT 1,
    is_collaborative    TINYINT(1)          NOT NULL DEFAULT 0,
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,

    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    INDEX idx_pl_owner      (owner_user_id),
    INDEX idx_pl_public     (is_public),
    INDEX idx_pl_active     (is_active),
    INDEX idx_pl_deleted    (deleted_at),
    FULLTEXT KEY ft_pl_name (playlist_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLIST_GENRES — Playlist ↔ Tür (M2M)
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlist_genres (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    playlist_id INT UNSIGNED        NOT NULL,
    genre_id    SMALLINT UNSIGNED   NOT NULL,   -- → coremusic_catalog.genre_list.id

    PRIMARY KEY (id),
    UNIQUE  KEY uq_pg_pair      (playlist_id, genre_id),
    FOREIGN KEY fk_pg_playlist  (playlist_id) REFERENCES playlists (id) ON DELETE CASCADE,

    INDEX idx_pg_genre          (genre_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLIST_MUSIC — Playlist ↔ Şarkı (M2M)
-- Normal Form : BCNF
-- music_id → coremusic_musics.musics(id) — cross-DB, uygulama doğrulaması
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlist_music (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    playlist_id     INT UNSIGNED        NOT NULL,
    music_id        INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    music_order     SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    added_by_user   INT UNSIGNED            NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    added_at        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_pm_pair      (playlist_id, music_id),
    FOREIGN KEY fk_pm_playlist  (playlist_id) REFERENCES playlists (id) ON DELETE CASCADE,

    INDEX idx_pm_music          (music_id),
    INDEX idx_pm_order          (playlist_id, music_order)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLIST_COLLABORATORS — Ortak düzenleme yetkileri
-- Normal Form : BCNF — (playlist_id, user_id) UNIQUE
-- accepted_at NULL = davet bekliyor | DATETIME = kabul edildi
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlist_collaborators (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    playlist_id     INT UNSIGNED        NOT NULL,
    user_id         INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    can_add         TINYINT(1)          NOT NULL DEFAULT 1,
    can_remove      TINYINT(1)          NOT NULL DEFAULT 0,
    invited_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    accepted_at     DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_pc_pair      (playlist_id, user_id),
    FOREIGN KEY fk_pc_playlist  (playlist_id) REFERENCES playlists (id) ON DELETE CASCADE,
    -- ⚠️ user_id → coremusic_auth DB: cross-DB FK yok.

    INDEX idx_pc_user           (user_id),
    INDEX idx_pc_accepted       (accepted_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLIST_STATS — Playlist istatistikleri (yüksek yazım frekanslı)
-- Normal Form : BCNF — (1:1 → playlist_id PK ve FK)
-- ⚠️ track_count HESAPLANMIŞ VERİ → SAKLANMAZ.
--    Sorgu: COUNT(*) FROM playlist_music WHERE playlist_id = ? AND is_active = 1
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlist_stats (
    playlist_id     INT UNSIGNED        NOT NULL,
    follower_count  BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    play_count      BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    share_count     BIGINT UNSIGNED     NOT NULL DEFAULT 0,

    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (playlist_id),
    FOREIGN KEY fk_pst_playlist (playlist_id) REFERENCES playlists (id) ON DELETE CASCADE,

    INDEX idx_pst_play      (play_count DESC),
    INDEX idx_pst_followers (follower_count DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 2: CİHAZ / OFFLİNE OYNATMA SİSTEMİ → coremusic_media DB'sine taşındı.
-- ══════════════════════════════════════════════════════════════════════════════
-- Tablolar  : media_device_types, media_devices,
--             media_device_playlists, media_device_playlist_tracks
-- SP        : coremusic_media.sp_create_device_playlist(device_id, user_id, name, src_plist_id)
-- Bağlantı  : source_playlist_id → coremusic_playlist.playlists.id (cross-DB, app doğrulaması)
