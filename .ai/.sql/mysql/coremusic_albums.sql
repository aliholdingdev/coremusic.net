-- CoreMusic Database: coremusic_albums
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_albums
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_albums;

-- =============================================
-- Table: albums
-- Description: Albümler, EP'ler, single'lar, derleme albümleri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE albums (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz albüm tanımlayıcı',
    title VARCHAR(500) NOT NULL COMMENT 'Albüm başlığı',
    slug VARCHAR(500) NOT NULL COMMENT 'URL dostu başlık',
    artist_id BINARY(16) NOT NULL COMMENT 'Sanatçı ID',
    album_type ENUM('album','ep','single','compilation','live','remix','soundtrack') DEFAULT 'album' NOT NULL COMMENT 'Albüm türü',
    genre_id BINARY(16) NULL COMMENT 'Birincil tür ID',
    release_date DATE NULL COMMENT 'Yayın tarihi',
    record_label VARCHAR(200) NULL COMMENT 'Plak şirketi',
    upc VARCHAR(20) NULL COMMENT 'Evrensel Ürün Kodu',
    cover_art_url VARCHAR(500) NULL COMMENT 'Kapak görseli URL',
    total_discs INT DEFAULT 1 NOT NULL COMMENT 'Toplam disk sayısı',
    total_tracks INT DEFAULT 0 NOT NULL COMMENT 'Toplam parça sayısı',
    total_duration_sec INT NULL COMMENT 'Toplam süre (saniye)',
    is_verified TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Doğrulanmış albüm mü?',
    play_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    like_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam beğeni sayısı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albümler tablosu';

-- Indexes for albums
CREATE INDEX idx_albums_artist ON albums(artist_id) COMMENT 'Sanatçı filtresi';
CREATE INDEX idx_albums_type ON albums(album_type) COMMENT 'Albüm türü filtresi';
CREATE INDEX idx_albums_release ON albums(release_date) COMMENT 'Yayın tarihi sıralaması';
CREATE FULLTEXT INDEX idx_albums_fulltext ON albums(title) COMMENT 'Tam metin araması';

-- Foreign Keys for albums
-- Note: artist_id FK references coremusic_musics.artists - cross-database FK not supported in MySQL
-- Note: genre_id FK references coremusic_musics.genres - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: album_discs
-- Description: Albüm diskleri (çoklu disk albümleri için)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE album_discs (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz disk tanımlayıcı',
    album_id BINARY(16) NOT NULL COMMENT 'Albüm ID',
    disc_number INT NOT NULL COMMENT 'Disk numarası',
    disc_title VARCHAR(200) NULL COMMENT 'Disk başlığı (opsiyonel)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm diskleri tablosu';

-- Indexes for album_discs
CREATE UNIQUE INDEX idx_album_discs_unique ON album_discs(album_id, disc_number) COMMENT 'Benzersiz albüm-disk çifti';

-- Foreign Keys for album_discs
ALTER TABLE album_discs ADD CONSTRAINT fk_album_discs_album
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: album_stats
-- Description: Albüm istatistikleri (günlük/haftalık/aylık)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE album_stats (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
    album_id BINARY(16) NOT NULL COMMENT 'Albüm ID',
    daily_plays INT DEFAULT 0 NOT NULL COMMENT 'Günlük çalma sayısı',
    weekly_plays INT DEFAULT 0 NOT NULL COMMENT 'Haftalık çalma sayısı',
    monthly_plays INT DEFAULT 0 NOT NULL COMMENT 'Aylık çalma sayısı',
    total_plays BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    daily_downloads INT DEFAULT 0 NOT NULL COMMENT 'Günlük indirme sayısı',
    total_downloads BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam indirme sayısı',
    popularity_score DECIMAL(10,2) DEFAULT 0 NOT NULL COMMENT 'Popülerlik puanı',
    stats_date DATE NOT NULL COMMENT 'İstatistik tarihi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm istatistikleri tablosu';

-- Indexes for album_stats
CREATE UNIQUE INDEX idx_album_stats_unique ON album_stats(album_id, stats_date) COMMENT 'Benzersiz albüm-tarih çifti';
CREATE INDEX idx_album_stats_popularity ON album_stats(popularity_score) COMMENT 'Popülerlik sıralaması';

-- Foreign Keys for album_stats
ALTER TABLE album_stats ADD CONSTRAINT fk_album_stats_album
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: album_genres
-- Description: Albüm-tür ilişkilendirme (çoktan bire)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE album_genres (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
    album_id BINARY(16) NOT NULL COMMENT 'Albüm ID',
    genre_id BINARY(16) NOT NULL COMMENT 'Tür ID',
    is_primary TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Birincil tür mü?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm-tür ilişkisi tablosu';

-- Indexes for album_genres
CREATE UNIQUE INDEX idx_album_genres_unique ON album_genres(album_id, genre_id) COMMENT 'Benzersiz albüm-tür çifti';
CREATE INDEX idx_album_genres_genre ON album_genres(genre_id) COMMENT 'Tür filtresi';

-- Foreign Keys for album_genres
ALTER TABLE album_genres ADD CONSTRAINT fk_album_genres_album
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: genre_id FK references coremusic_musics.genres - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: album_credits
-- Description: Albüm kredileri (prodüktör, mühendis, karıştırma vb.)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE album_credits (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tanımlayıcı',
    album_id BINARY(16) NOT NULL COMMENT 'Albüm ID',
    credit_type ENUM('producer','engineer','mixer','mastering','artwork','photography','other') NOT NULL COMMENT 'Kredi türü',
    person_name VARCHAR(200) NULL COMMENT 'Kişi adı (CoreMusic dışı ise)',
    person_id BINARY(16) NULL COMMENT 'Kişi ID (CoreMusic üyesi ise)',
    credit_order INT DEFAULT 0 NOT NULL COMMENT 'Kredi sırası',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm kredileri tablosu';

-- Indexes for album_credits
CREATE INDEX idx_album_credits_album ON album_credits(album_id) COMMENT 'Albüm filtresi';
CREATE INDEX idx_album_credits_type ON album_credits(credit_type) COMMENT 'Kredi türü filtresi';

-- Foreign Keys for album_credits
ALTER TABLE album_credits ADD CONSTRAINT fk_album_credits_album
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: person_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- CoreMusic coremusic_albums Database v8.0.0
-- Tables: 5
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
