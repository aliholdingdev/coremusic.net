-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_user — KULLANICI ETKİLEŞİM TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5.0 → v5.1 EKLENENler]
-- 1. [YENİ] user_playback_queue — Oynatma sırası (kalıcı)
--    coreplayer.js, coreplaylist.js, footer.init.js incelendi.
--    Player'ın queue yönetimi var. Kalıcı queue DB'de tutulur.
--
-- [v4 → v5 korunan düzeltmeler]
-- 2. user_favorites: id tip tanımsız → düzeltildi.
-- 3. user_favorites_like: id/playlist_id/album_id tip tanımsız → düzeltildi.
--    CHECK: üç nullable ID'den sadece biri NOT NULL olabilir.
-- 4. user_follows_singers/playlists/albums: id tip tanımsız, trailing comma → düzeltildi.
-- ══════════════════════════════════════════════════════════════════════════════
--
-- TABLO KAVRAMI:
--   user_favorites      = Kullanıcının yıldız verdiği şarkılar (0.5–5.0)
--   user_favorites_like = Müzik / playlist / albüm beğenisi (like butonu)
--   user_playback_queue = Şu anki çalma sırası (session veya kalıcı)
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_user
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_user;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_FAVORITES — Kullanıcı yıldızlı şarkılar (yarım yıldız destekli)
-- Normal Form : BCNF — (user_id, music_id) UNIQUE
-- star_count  : 0.0 = favoriye eklenmiş ama puanlanmamış | 0.5–5.0 = puan
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_favorites (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    music_id    INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    star_count  DECIMAL(2,1)        NOT NULL DEFAULT 0.0,
    added_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_uf_pair  (user_id, music_id),
    -- ⚠️ Tüm ID'ler cross-DB: FK mümkün değil. Uygulama doğrulaması.

    INDEX idx_uf_music      (music_id),
    INDEX idx_uf_added      (user_id, added_at DESC),
    INDEX idx_uf_star       (user_id, star_count DESC),

    CHECK (star_count IN (0.0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_FAVORITES_LIKE — Kullanıcı beğenileri (like butonu, yıldız değil)
-- Normal Form : BCNF
-- Müzik, playlist veya albüm likelanabilir — sadece biri seçilir (CHECK zorunlu).
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_favorites_like (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    music_id    INT UNSIGNED            NULL,   -- → coremusic_musics.musics.id
    playlist_id INT UNSIGNED            NULL,   -- → coremusic_playlist.playlists.id
    album_id    INT UNSIGNED            NULL,   -- → coremusic_albums.albums.id
    added_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ Tüm ID'ler cross-DB: FK mümkün değil. Uygulama doğrulaması.

    UNIQUE  KEY uq_ufl_music        (user_id, music_id),
    UNIQUE  KEY uq_ufl_playlist     (user_id, playlist_id),
    UNIQUE  KEY uq_ufl_album        (user_id, album_id),

    INDEX idx_ufl_music             (music_id),
    INDEX idx_ufl_playlist          (playlist_id),
    INDEX idx_ufl_album             (album_id),
    INDEX idx_ufl_added             (user_id, added_at DESC),

    -- Üçünden sadece biri NOT NULL olabilir
    CHECK (
        (music_id IS NOT NULL AND playlist_id IS NULL     AND album_id IS NULL) OR
        (music_id IS NULL     AND playlist_id IS NOT NULL AND album_id IS NULL) OR
        (music_id IS NULL     AND playlist_id IS NULL     AND album_id IS NOT NULL)
    )

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_FOLLOWS_SINGERS — Kullanıcı → Sanatçı takibi
-- Normal Form : BCNF — (user_id, singer_id) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_follows_singers (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    singer_id   INT UNSIGNED        NOT NULL,   -- → coremusic_musics.music_singer.id
    followed_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ufs_pair     (user_id, singer_id),

    INDEX idx_ufs_singer        (singer_id),
    INDEX idx_ufs_followed      (user_id, followed_at DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_FOLLOWS_PLAYLISTS — Kullanıcı → Playlist takibi
-- Normal Form : BCNF — (user_id, playlist_id) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_follows_playlists (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    playlist_id INT UNSIGNED        NOT NULL,   -- → coremusic_playlist.playlists.id
    followed_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ufpl_pair    (user_id, playlist_id),

    INDEX idx_ufpl_playlist     (playlist_id),
    INDEX idx_ufpl_followed     (user_id, followed_at DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_FOLLOWS_ALBUMS — Kullanıcı → Albüm takibi
-- Normal Form : BCNF — (user_id, album_id) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_follows_albums (
    id          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    album_id    INT UNSIGNED        NOT NULL,   -- → coremusic_albums.albums.id
    followed_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ufa_pair     (user_id, album_id),

    INDEX idx_ufa_album         (album_id),
    INDEX idx_ufa_followed      (user_id, followed_at DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_PLAYBACK_QUEUE — Kullanıcı oynatma sırası
-- Normal Form : BCNF
-- [v5.1 YENİ] coreplayer.js + coreplaylist.js incelendi → queue yönetimi var.
--
-- Her kullanıcının aktif bir queue'su olur. Queue silinmez, üzerine yazılır.
-- queue_source: nereden oluşturulduğu (playlist, album, search, manual)
-- queue_index: şu an çalınan sıra konumu (uygulama günceller)
-- is_shuffle: karışık sıra aktif mi
-- repeat_mode: tekrar modu
--
-- ⚠️ track_count HESAPLANMIŞ VERİ → SAKLANMAZ.
--    COUNT(*) FROM user_playback_queue_tracks WHERE queue_id = ?
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_playback_queue (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    session_id      VARCHAR(128)            NULL,
    -- Oturum bazlı queue için PHP session ID. NULL = kalıcı (son oturum)
    queue_source    VARCHAR(30)         NOT NULL DEFAULT 'manual',
    -- manual | playlist | album | search | recommendation | radio | device
    source_id       INT UNSIGNED            NULL,
    -- queue_source'a göre: playlist_id, album_id vb.
    queue_index     SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    -- Şu an çalınan şarkının sıradaki konumu (0-based)
    is_shuffle      TINYINT(1)          NOT NULL DEFAULT 0,
    repeat_mode     VARCHAR(10)         NOT NULL DEFAULT 'none',
    -- none | one | all
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_upq_user     (user_id),
    -- Kullanıcı başına tek aktif queue. Yeni queue → UPDATE ile üzerine yaz.

    INDEX idx_upq_session       (session_id),

    CHECK (queue_source IN ('manual','playlist','album','search','recommendation','radio','device')),
    CHECK (repeat_mode  IN ('none','one','all'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_PLAYBACK_QUEUE_TRACKS — Queue içindeki şarkılar
-- Normal Form : BCNF — (queue_id, track_order) UNIQUE
-- music_id → coremusic_musics.musics(id) — cross-DB, uygulama doğrulaması
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_playback_queue_tracks (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    queue_id        INT UNSIGNED        NOT NULL,
    music_id        INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    track_order     SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    added_at        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_upqt_order   (queue_id, track_order),
    FOREIGN KEY fk_upqt_queue   (queue_id) REFERENCES user_playback_queue (id) ON DELETE CASCADE,

    INDEX idx_upqt_music        (music_id),
    INDEX idx_upqt_order        (queue_id, track_order)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
