-- CoreMusic Database: coremusic_playlist
-- Version: 7.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_playlist
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_playlist;

-- =============================================
-- Table: playlists
-- Description: Kullanıcı çalma listeleri, AI çalma listeleri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE playlists (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz çalma listesi tanımlayıcı',
    user_id BINARY(16) NOT NULL COMMENT 'Oluşturan kullanıcı ID',
    name VARCHAR(200) NOT NULL COMMENT 'Çalma listesi adı',
    description TEXT NULL COMMENT 'Açıklama',
    cover_art_url VARCHAR(500) NULL COMMENT 'Kapak görseli URL',
    visibility ENUM('public','private','unlisted') DEFAULT 'public' NOT NULL COMMENT 'Görünürlük',
    playlist_type ENUM('user','ai','auto_generated','radio','curated') DEFAULT 'user' NOT NULL COMMENT 'Çalma listesi türü',
    is_editable TINYINT(1) DEFAULT 1 NOT NULL COMMENT 'Düzenlenebilir mi?',
    total_tracks INT DEFAULT 0 NOT NULL COMMENT 'Toplam parça sayısı',
    total_duration_sec INT DEFAULT 0 NOT NULL COMMENT 'Toplam süre (saniye)',
    follow_count INT DEFAULT 0 NOT NULL COMMENT 'Takipçi sayısı',
    like_count INT DEFAULT 0 NOT NULL COMMENT 'Beğeni sayısı',
    play_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listeleri tablosu';

-- Indexes for playlists
CREATE INDEX idx_playlists_user ON playlists(user_id) COMMENT 'Kullanıcı filtresi';
CREATE INDEX idx_playlists_visibility ON playlists(visibility) COMMENT 'Görünürlük filtresi';
CREATE INDEX idx_playlists_type ON playlists(playlist_type) COMMENT 'Tür filtresi';
CREATE FULLTEXT INDEX idx_playlists_fulltext ON playlists(name, description) COMMENT 'Tam metin araması';

-- Foreign Keys for playlists
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: playlist_tracks
-- Description: Çalma listesi parçaları (sıralı)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE playlist_tracks (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
    playlist_id BINARY(16) NOT NULL COMMENT 'Çalma listesi ID',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    position INT NOT NULL COMMENT 'Sıra numarası',
    added_by BINARY(16) NULL COMMENT 'Ekleyen kullanıcı ID',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Eklenme zamanı',
    note TEXT NULL COMMENT 'Not (opsiyonel)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi parçaları tablosu';

-- Indexes for playlist_tracks
CREATE INDEX idx_playlist_tracks_playlist_pos ON playlist_tracks(playlist_id, position) COMMENT 'Çalma listesi sıralaması';
CREATE INDEX idx_playlist_tracks_music ON playlist_tracks(music_id) COMMENT 'Müzik filtresi';
CREATE UNIQUE INDEX idx_playlist_tracks_unique ON playlist_tracks(playlist_id, music_id, position) COMMENT 'Benzersiz çalma listesi-müzik-sıra';

-- Foreign Keys for playlist_tracks
ALTER TABLE playlist_tracks ADD CONSTRAINT fk_playlist_tracks_playlist
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: music_id FK references coremusic_musics.musics - cross-database FK not supported in MySQL
-- Note: added_by FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: playlist_collaborators
-- Description: Çalma listesi işbirlikçileri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE playlist_collaborators (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz işbirlikçi tanımlayıcı',
    playlist_id BINARY(16) NOT NULL COMMENT 'Çalma listesi ID',
    user_id BINARY(16) NOT NULL COMMENT 'Kullanıcı ID',
    role ENUM('viewer','editor','admin') DEFAULT 'viewer' NOT NULL COMMENT 'İşbirlikçi rolü',
    invited_by BINARY(16) NULL COMMENT 'Davet eden kullanıcı ID',
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Davet zamanı',
    accepted_at TIMESTAMP NULL COMMENT 'Kabul zamanı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi işbirlikçileri tablosu';

-- Indexes for playlist_collaborators
CREATE UNIQUE INDEX idx_playlist_collaborators_unique ON playlist_collaborators(playlist_id, user_id) COMMENT 'Benzersiz çalma listesi-kullanıcı çifti';
CREATE INDEX idx_playlist_collaborators_user ON playlist_collaborators(user_id) COMMENT 'Kullanıcı filtresi';
CREATE INDEX idx_playlist_collaborators_role ON playlist_collaborators(role) COMMENT 'Rol filtresi';

-- Foreign Keys for playlist_collaborators
ALTER TABLE playlist_collaborators ADD CONSTRAINT fk_playlist_collaborators_playlist
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Note: invited_by FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: playlist_followers
-- Description: Çalma listesi takipçileri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE playlist_followers (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz takipçi tanımlayıcı',
    playlist_id BINARY(16) NOT NULL COMMENT 'Çalma listesi ID',
    user_id BINARY(16) NOT NULL COMMENT 'Kullanıcı ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi takipçileri tablosu';

-- Indexes for playlist_followers
CREATE UNIQUE INDEX idx_playlist_followers_unique ON playlist_followers(playlist_id, user_id) COMMENT 'Benzersiz çalma listesi-kullanıcı çifti';
CREATE INDEX idx_playlist_followers_user ON playlist_followers(user_id) COMMENT 'Kullanıcı filtresi';

-- Foreign Keys for playlist_followers
ALTER TABLE playlist_followers ADD CONSTRAINT fk_playlist_followers_playlist
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: playlist_stats
-- Description: Çalma listesi istatistikleri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE playlist_stats (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
    playlist_id BINARY(16) NOT NULL COMMENT 'Çalma listesi ID',
    daily_plays INT DEFAULT 0 NOT NULL COMMENT 'Günlük çalma sayısı',
    weekly_plays INT DEFAULT 0 NOT NULL COMMENT 'Haftalık çalma sayısı',
    monthly_plays INT DEFAULT 0 NOT NULL COMMENT 'Aylık çalma sayısı',
    total_plays BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    daily_followers INT DEFAULT 0 NOT NULL COMMENT 'Günlük takipçi sayısı',
    total_followers INT DEFAULT 0 NOT NULL COMMENT 'Toplam takipçi sayısı',
    popularity_score DECIMAL(10,2) DEFAULT 0 NOT NULL COMMENT 'Popülerlik puanı',
    stats_date DATE NOT NULL COMMENT 'İstatistik tarihi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Çalma listesi istatistikleri tablosu';

-- Indexes for playlist_stats
CREATE UNIQUE INDEX idx_playlist_stats_unique ON playlist_stats(playlist_id, stats_date) COMMENT 'Benzersiz çalma listesi-tarih çifti';
CREATE INDEX idx_playlist_stats_popularity ON playlist_stats(popularity_score) COMMENT 'Popülerlik sıralaması';

-- Foreign Keys for playlist_stats
ALTER TABLE playlist_stats ADD CONSTRAINT fk_playlist_stats_playlist
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- CoreMusic coremusic_playlist Database v7.0.0
-- Tables: 5
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
