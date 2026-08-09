-- coremusic_musics — MUSIK, PODCAST, VIDEO, RADYO VERITABANI
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at
-- Tables: 22 (12 musics + 4 podcast + 3 video + 3 radio)

CREATE DATABASE IF NOT EXISTS coremusic_musics
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_musics;

-- ══════════════════════════════════════════════════════════════════════════════
-- MÜZİK TABLOLARI
-- ══════════════════════════════════════════════════════════════════════════════

-- =============================================
-- Table: artists
-- Description: Müzik sanatçıları (solo, band, duo, orkestra)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE artists (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz sanatçı tanımlayıcı',
    name VARCHAR(200) NOT NULL COMMENT 'Sanatçı adı',
    slug VARCHAR(200) NOT NULL COMMENT 'URL dostu sanatçı adı',
    biography TEXT NULL COMMENT 'Sanatçı biyografisi',
    country VARCHAR(2) NULL COMMENT 'Ülke kodu (ISO 3166-1 alpha-2)',
    formed_year INT NULL COMMENT 'Kuruluş/yıl',
    artist_type ENUM('solo','band','duo','orchestra','other') DEFAULT 'solo' NOT NULL COMMENT 'Sanatçı türü',
    image_url VARCHAR(500) NULL COMMENT 'Sanatçı fotoğrafı URL',
    is_verified TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Doğrulanmış sanatçı mı?',
    is_active TINYINT(1) DEFAULT 1 NOT NULL COMMENT 'Aktif sanatçı mı?',
    monthly_listeners INT DEFAULT 0 NOT NULL COMMENT 'Aylık dinleyici sayısı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanatçılar tablosu';

-- Indexes for artists
CREATE UNIQUE INDEX idx_artists_slug ON artists(slug) COMMENT 'URL benzersizlik';
CREATE INDEX idx_artists_name ON artists(name) COMMENT 'İsim araması';
CREATE INDEX idx_artists_country ON artists(country) COMMENT 'Ülke filtresi';
CREATE INDEX idx_artists_type ON artists(artist_type) COMMENT 'Sanatçı türü filtresi';
CREATE FULLTEXT INDEX idx_artists_fulltext ON artists(name, biography) COMMENT 'Tam metin araması';

-- =============================================
-- Table: genres
-- Description: Müzik türleri (hiyerarşik yapı)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE genres (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz tür tanımlayıcı',
    name VARCHAR(100) NOT NULL COMMENT 'Tür adı',
    slug VARCHAR(100) NOT NULL COMMENT 'URL dostu tür adı',
    description TEXT NULL COMMENT 'Tür açıklaması',
    parent_id BINARY(16) NULL COMMENT 'Üst tür ID (hiyerarşik)',
    genre_level INT DEFAULT 0 NOT NULL COMMENT 'Hiyerarşi seviyesi (0: kök)',
    is_active TINYINT(1) DEFAULT 1 NOT NULL COMMENT 'Aktif tür mü?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik türleri tablosu';

-- Indexes for genres
CREATE UNIQUE INDEX idx_genres_slug ON genres(slug) COMMENT 'URL benzersizlik';
CREATE INDEX idx_genres_parent ON genres(parent_id) COMMENT 'Üst tür ilişkisi';
CREATE INDEX idx_genres_level ON genres(genre_level) COMMENT 'Hiyerarşi seviyesi';

-- Foreign Keys for genres
ALTER TABLE genres ADD CONSTRAINT fk_genres_parent
    FOREIGN KEY (parent_id) REFERENCES genres(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- Table: musics
-- Description: Şarkılar, parçalar
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE musics (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz müzik tanımlayıcı',
    title VARCHAR(500) NOT NULL COMMENT 'Şarkı başlığı',
    slug VARCHAR(500) NOT NULL COMMENT 'URL dostu başlık',
    artist_id BINARY(16) NOT NULL COMMENT 'Sanatçı ID',
    album_id BINARY(16) NULL COMMENT 'Albüm ID (opsiyonel)',
    genre_id BINARY(16) NULL COMMENT 'Birincil tür ID',
    track_number INT NULL COMMENT 'Albüm içindeki sıra numarası',
    disc_number INT DEFAULT 1 NOT NULL COMMENT 'Disk numarası',
    duration_sec INT NULL COMMENT 'Süre (saniye)',
    release_date DATE NULL COMMENT 'Yayın tarihi',
    isrc VARCHAR(20) NULL COMMENT 'Uluslararası Standart Kayıt Kodu',
    upc VARCHAR(20) NULL COMMENT 'Evrensel Ürün Kodu',
    bpm INT NULL COMMENT 'Tempo (darbe/dakika)',
    key_signature VARCHAR(20) NULL COMMENT 'Müzik anahtarı',
    mood ENUM('happy','sad','energetic','calm','dark','uplifting','neutral') DEFAULT 'neutral' NOT NULL COMMENT 'Ruh hali',
    is_explicit TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'İçerik uyarısı',
    is_verified TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Doğrulanmış müzik',
    is_instrumental TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Enstrümantal parça mı?',
    play_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    like_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam beğeni sayısı',
    download_count BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam indirme sayısı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik parçaları tablosu';

-- Indexes for musics
CREATE INDEX idx_musics_artist ON musics(artist_id) COMMENT 'Sanatçı filtresi';
CREATE INDEX idx_musics_album ON musics(album_id) COMMENT 'Albüm filtresi';
CREATE INDEX idx_musics_genre ON musics(genre_id) COMMENT 'Tür filtresi';
CREATE INDEX idx_musics_release ON musics(release_date) COMMENT 'Yayın tarihi sıralaması';
CREATE INDEX idx_musics_mood ON musics(mood) COMMENT 'Ruh hali filtresi';
CREATE FULLTEXT INDEX idx_musics_fulltext ON musics(title) COMMENT 'Tam metin araması';

-- Foreign Keys for musics
ALTER TABLE musics ADD CONSTRAINT fk_musics_artist
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE musics ADD CONSTRAINT fk_musics_genre
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL ON UPDATE CASCADE;
-- Note: album_id FK references coremusic_albums.albums - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: music_files
-- Description: Müzik dosya bilgileri (format, kalite, yol)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_files (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz dosya tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    file_format ENUM('mp3','flac','wav','aac','ogg','dsd','mqa') NOT NULL COMMENT 'Dosya formatı',
    file_path VARCHAR(500) NOT NULL COMMENT 'Dosya yolu',
    file_size BIGINT NOT NULL COMMENT 'Dosya boyutu (byte)',
    bit_rate INT NULL COMMENT 'Bit hızı (kbps)',
    sample_rate INT NULL COMMENT 'Örnekleme hızı (Hz)',
    bit_depth INT NULL COMMENT 'Bit derinliği',
    channels INT DEFAULT 2 NOT NULL COMMENT 'Kanal sayısı',
    codec VARCHAR(50) NULL COMMENT 'Codec bilgisi',
    checksum_sha256 VARCHAR(64) NULL COMMENT 'SHA256 checksum',
    is_primary TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Birincil dosya mı?',
    quality_level ENUM('low_128','medium_192','high_320','lossless','hi_res') DEFAULT 'high_320' NOT NULL COMMENT 'Kalite seviyesi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik dosyaları tablosu';

-- Indexes for music_files
CREATE INDEX idx_music_files_music ON music_files(music_id) COMMENT 'Müzik ilişkisi';
CREATE INDEX idx_music_files_format ON music_files(file_format) COMMENT 'Format filtresi';
CREATE INDEX idx_music_files_quality ON music_files(quality_level) COMMENT 'Kalite filtresi';
CREATE INDEX idx_music_files_primary ON music_files(is_primary) COMMENT 'Birincil dosya filtresi';

-- Foreign Keys for music_files
ALTER TABLE music_files ADD CONSTRAINT fk_music_files_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_lyrics
-- Description: Şarkı sözleri (senkronize/esenkronize)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_lyrics (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz söz tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    lyrics_text LONGTEXT NULL COMMENT 'Şarkı sözleri metni',
    lyrics_language VARCHAR(10) DEFAULT 'en' NOT NULL COMMENT 'Dil kodu (ISO 639-1)',
    lyrics_source ENUM('manual','genius','musixmatch','ai_generated','verified') DEFAULT 'manual' NOT NULL COMMENT 'Söz kaynağı',
    synced_lyrics TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Senkronize söz var mı?',
    synced_lyrics_data JSON NULL COMMENT 'Senkronize söz verisi (LRC formatı)',
    is_verified TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Doğrulanmış sözler mi?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Şarkı sözleri tablosu';

-- Indexes for music_lyrics
CREATE UNIQUE INDEX idx_music_lyrics_music ON music_lyrics(music_id) COMMENT 'Benzersiz müzik ilişkisi';
CREATE INDEX idx_music_lyrics_lang ON music_lyrics(lyrics_language) COMMENT 'Dil filtresi';
CREATE FULLTEXT INDEX idx_music_lyrics_fulltext ON music_lyrics(lyrics_text) COMMENT 'Söz araması';

-- Foreign Keys for music_lyrics
ALTER TABLE music_lyrics ADD CONSTRAINT fk_music_lyrics_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_genres
-- Description: Müzik-tür ilişkilendirme (çoktan bire)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_genres (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    genre_id BINARY(16) NOT NULL COMMENT 'Tür ID',
    is_primary TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Birincil tür mü?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik-tür ilişkisi tablosu';

-- Indexes for music_genres
CREATE UNIQUE INDEX idx_music_genres_unique ON music_genres(music_id, genre_id) COMMENT 'Benzersiz müzik-tür çifti';
CREATE INDEX idx_music_genres_genre ON music_genres(genre_id) COMMENT 'Tür filtresi';

-- Foreign Keys for music_genres
ALTER TABLE music_genres ADD CONSTRAINT fk_music_genres_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE music_genres ADD CONSTRAINT fk_music_genres_genre
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_tags
-- Description: Müzik etiketleri (anahtar kelimeler)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_tags (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz etiket tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    tag VARCHAR(50) NOT NULL COMMENT 'Etiket adı',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik etiketleri tablosu';

-- Indexes for music_tags
CREATE INDEX idx_music_tags_music ON music_tags(music_id) COMMENT 'Müzik filtresi';
CREATE INDEX idx_music_tags_tag ON music_tags(tag) COMMENT 'Etiket araması';
CREATE UNIQUE INDEX idx_music_tags_unique ON music_tags(music_id, tag) COMMENT 'Benzersiz müzik-etiket çifti';

-- Foreign Keys for music_tags
ALTER TABLE music_tags ADD CONSTRAINT fk_music_tags_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_stats
-- Description: Müzik istatistikleri (günlük/haftalık/aylık)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_stats (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    daily_plays INT DEFAULT 0 NOT NULL COMMENT 'Günlük çalma sayısı',
    weekly_plays INT DEFAULT 0 NOT NULL COMMENT 'Haftalık çalma sayısı',
    monthly_plays INT DEFAULT 0 NOT NULL COMMENT 'Aylık çalma sayısı',
    total_plays BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam çalma sayısı',
    daily_downloads INT DEFAULT 0 NOT NULL COMMENT 'Günlük indirme sayısı',
    weekly_downloads INT DEFAULT 0 NOT NULL COMMENT 'Haftalık indirme sayısı',
    monthly_downloads INT DEFAULT 0 NOT NULL COMMENT 'Aylık indirme sayısı',
    total_downloads BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam indirme sayısı',
    daily_shares INT DEFAULT 0 NOT NULL COMMENT 'Günlük paylaşım sayısı',
    weekly_shares INT DEFAULT 0 NOT NULL COMMENT 'Haftalık paylaşım sayısı',
    monthly_shares INT DEFAULT 0 NOT NULL COMMENT 'Aylık paylaşım sayısı',
    total_shares BIGINT DEFAULT 0 NOT NULL COMMENT 'Toplam paylaşım sayısı',
    popularity_score DECIMAL(10,2) DEFAULT 0 NOT NULL COMMENT 'Popülerlik puanı',
    trending_score DECIMAL(10,2) DEFAULT 0 NOT NULL COMMENT 'Trend puanı',
    last_played_at TIMESTAMP NULL COMMENT 'Son çalınma zamanı',
    stats_date DATE NOT NULL COMMENT 'İstatistik tarihi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik istatistikleri tablosu';

-- Indexes for music_stats
CREATE UNIQUE INDEX idx_music_stats_unique ON music_stats(music_id, stats_date) COMMENT 'Benzersiz müzik-tarih çifti';
CREATE INDEX idx_music_stats_popularity ON music_stats(popularity_score) COMMENT 'Popülerlik sıralaması';
CREATE INDEX idx_music_stats_trending ON music_stats(trending_score) COMMENT 'Trend sıralaması';

-- Foreign Keys for music_stats
ALTER TABLE music_stats ADD CONSTRAINT fk_music_stats_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_similar
-- Description: Benzer müzik önerileri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_similar (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz benzerlik tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    similar_music_id BINARY(16) NOT NULL COMMENT 'Benzer müzik ID',
    similarity_score DECIMAL(3,2) NOT NULL COMMENT 'Benzerlik puanı (0.00-1.00)',
    algorithm ENUM('collaborative','content_based','hybrid') DEFAULT 'hybrid' NOT NULL COMMENT 'Benzerlik algoritması',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Benzer müzik tablosu';

-- Indexes for music_similar
CREATE UNIQUE INDEX idx_music_similar_unique ON music_similar(music_id, similar_music_id) COMMENT 'Benzersiz müzik çifti';
CREATE INDEX idx_music_similar_target ON music_similar(similar_music_id) COMMENT 'Hedef müzik filtresi';
CREATE INDEX idx_music_similar_score ON music_similar(similarity_score) COMMENT 'Benzerlik puanı sıralaması';

-- Foreign Keys for music_similar
ALTER TABLE music_similar ADD CONSTRAINT fk_music_similar_source
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE music_similar ADD CONSTRAINT fk_music_similar_target
    FOREIGN KEY (similar_music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: artist_members
-- Description: Sanatçı grubu üyeleri
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE artist_members (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz üye tanımlayıcı',
    artist_id BINARY(16) NOT NULL COMMENT 'Sanatçı/grup ID',
    user_id BINARY(16) NULL COMMENT 'Kullanıcı ID (CoreMusic üyesi ise)',
    role VARCHAR(100) DEFAULT 'member' NOT NULL COMMENT 'Grup içindeki rol',
    joined_at DATE NULL COMMENT 'Katılım tarihi',
    left_at DATE NULL COMMENT 'Ayrılma tarihi',
    is_active TINYINT(1) DEFAULT 1 NOT NULL COMMENT 'Aktif üye mi?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    is_deleted TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Yumuşak silme bayrağı',
    deleted_at TIMESTAMP NULL COMMENT 'Silinme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanatçı üye tablosu';

-- Indexes for artist_members
CREATE INDEX idx_artist_members_artist ON artist_members(artist_id) COMMENT 'Sanatçı filtresi';
CREATE INDEX idx_artist_members_user ON artist_members(user_id) COMMENT 'Kullanıcı filtresi';
CREATE INDEX idx_artist_members_active ON artist_members(is_active) COMMENT 'Aktif üye filtresi';

-- Foreign Keys for artist_members
ALTER TABLE artist_members ADD CONSTRAINT fk_artist_members_artist
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- =============================================
-- Table: music_audio_features
-- Description: Müzik ses özellikleri (dans edilebilirlik, enerji vb.)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_audio_features (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz özellik tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    danceability DECIMAL(4,3) NULL COMMENT 'Dans edilebilirlik (0.000-1.000)',
    energy DECIMAL(4,3) NULL COMMENT 'Enerji seviyesi (0.000-1.000)',
    valence DECIMAL(4,3) NULL COMMENT 'Pozitiflik (0.000-1.000)',
    acousticness DECIMAL(4,3) NULL COMMENT 'Akustiklik (0.000-1.000)',
    instrumentalness DECIMAL(4,3) NULL COMMENT 'Enstrümantallık (0.000-1.000)',
    liveness DECIMAL(4,3) NULL COMMENT 'Canlılık (0.000-1.000)',
    speechiness DECIMAL(4,3) NULL COMMENT 'Konuşma benzerliği (0.000-1.000)',
    loudness DECIMAL(6,3) NULL COMMENT 'Ses şiddeti (dB)',
    tempo DECIMAL(6,2) NULL COMMENT 'Tempo (BPM)',
    key_confidence DECIMAL(4,3) NULL COMMENT 'Anahtar güvenilirliği (0.000-1.000)',
    mode_confidence DECIMAL(4,3) NULL COMMENT 'Mod güvenilirliği (0.000-1.000)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL COMMENT 'Güncellenme zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik ses özellikleri tablosu';

-- Indexes for music_audio_features
CREATE UNIQUE INDEX idx_music_audio_features_music ON music_audio_features(music_id) COMMENT 'Benzersiz müzik ilişkisi';

-- Foreign Keys for music_audio_features
ALTER TABLE music_audio_features ADD CONSTRAINT fk_music_audio_features_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: music_credits
-- Description: Müzik kredileri (besteci, söz yazarı, prodüktör vb.)
-- BCNF: ✅ Her determinant candidate key
-- =============================================
CREATE TABLE music_credits (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tanımlayıcı',
    music_id BINARY(16) NOT NULL COMMENT 'Müzik ID',
    credit_type ENUM('composer','lyricist','producer','engineer','mixer','mastering','feature','other') NOT NULL COMMENT 'Kredi türü',
    person_name VARCHAR(200) NULL COMMENT 'Kişi adı (CoreMusic dışı ise)',
    person_id BINARY(16) NULL COMMENT 'Kişi ID (CoreMusic üyesi ise)',
    credit_order INT DEFAULT 0 NOT NULL COMMENT 'Kredi sırası',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT 'Oluşturulma zamanı',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Müzik kredileri tablosu';

-- Indexes for music_credits
CREATE INDEX idx_music_credits_music ON music_credits(music_id) COMMENT 'Müzik filtresi';
CREATE INDEX idx_music_credits_type ON music_credits(credit_type) COMMENT 'Kredi türü filtresi';
CREATE INDEX idx_music_credits_person ON music_credits(person_id) COMMENT 'Kişi filtresi';

-- Foreign Keys for music_credits
ALTER TABLE music_credits ADD CONSTRAINT fk_music_credits_music
    FOREIGN KEY (music_id) REFERENCES musics(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: person_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity

-- ══════════════════════════════════════════════════════════════════════════════
-- PODCAST TABLOLARI
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- PODCAST_SHOWS — Podcast gösterileri
-- Normal Form : BCNF — id → {title, author, category, ...}
-- status      : draft | active | paused | archived
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE podcast_shows (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    author_user_id  INT UNSIGNED        NOT NULL,   -- cross-DB FK → coremusic_auth.users.id
    title           VARCHAR(500)        NOT NULL,
    slug            VARCHAR(500)        NOT NULL,
    description     TEXT                    NULL,
    cover_image     VARCHAR(2048)           NULL,
    author_name     VARCHAR(255)        NOT NULL,
    category        VARCHAR(100)            NULL,
    language        VARCHAR(10)         NOT NULL DEFAULT 'tr',
    website_url     VARCHAR(2048)           NULL,
    episode_count   INT UNSIGNED        NOT NULL DEFAULT 0,
    subscriber_count INT UNSIGNED       NOT NULL DEFAULT 0,
    status          VARCHAR(20)         NOT NULL DEFAULT 'active',
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ps_slug     (slug),

    INDEX idx_ps_author     (author_user_id),
    INDEX idx_ps_category   (category),
    INDEX idx_ps_language   (language),
    INDEX idx_ps_status     (status),

    CHECK (status IN ('draft', 'active', 'paused', 'archived'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Podcast gösterileri — series, episodes, subscriptions';


-- ──────────────────────────────────────────────────────────────────────────────
-- PODCAST_EPISODES — Bölümler
-- Normal Form : BCNF — id → {show_id, title, duration, ...}
-- status      : draft | published | scheduled | archived
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE podcast_episodes (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    show_id         INT UNSIGNED        NOT NULL,   -- FK → podcast_shows.id
    title           VARCHAR(500)        NOT NULL,
    slug            VARCHAR(500)        NOT NULL,
    description     TEXT                    NULL,
    audio_url       VARCHAR(2048)       NOT NULL,
    audio_format    VARCHAR(10)         NOT NULL DEFAULT 'mp3',
    audio_size_bytes BIGINT UNSIGNED        NULL,
    duration_seconds INT UNSIGNED           NULL,
    episode_number  INT UNSIGNED            NULL,
    season_number   INT UNSIGNED            NULL,
    publish_date    DATETIME                NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'draft',
    play_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_pe_slug     (show_id, slug),

    INDEX idx_pe_show       (show_id),
    INDEX idx_pe_publish    (publish_date DESC),
    INDEX idx_pe_status     (status),

    CHECK (status IN ('draft', 'published', 'scheduled', 'archived')),
    CHECK (audio_format IN ('mp3', 'aac', 'ogg', 'flac', 'wav'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Podcast bölümleri — episodes, audio, scheduling';

-- Foreign Keys for podcast_episodes
ALTER TABLE podcast_episodes
    ADD CONSTRAINT fk_pe_show FOREIGN KEY (show_id) REFERENCES podcast_shows (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- ──────────────────────────────────────────────────────────────────────────────
-- PODCAST_SUBSCRIPTIONS — Kullanıcı abonelikleri
-- Normal Form : BCNF — (user_id, show_id) UNIQUE
-- notify      : Yeni bölüm bildirimi
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE podcast_subscriptions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- cross-DB FK → coremusic_auth.users.id
    show_id         INT UNSIGNED        NOT NULL,   -- FK → podcast_shows.id
    notify_new_episode TINYINT(1)       NOT NULL DEFAULT 1,
    last_played_episode_id INT UNSIGNED     NULL,   -- FK → podcast_episodes.id
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ps_pair    (user_id, show_id),

    INDEX idx_ps_sub_show       (show_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Podcast abonelikleri — user-show bindings';

-- Foreign Keys for podcast_subscriptions
ALTER TABLE podcast_subscriptions
    ADD CONSTRAINT fk_psub_show FOREIGN KEY (show_id) REFERENCES podcast_shows (id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity


-- ──────────────────────────────────────────────────────────────────────────────
-- PODCAST_TRANSCRIPTS — Otomatik transkripsiyon
-- Normal Form : BCNF — (episode_id, language) UNIQUE
-- AI Service  : Otomatik transkripsiyon üretimi
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE podcast_transcripts (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    episode_id      INT UNSIGNED        NOT NULL,   -- FK → podcast_episodes.id
    language        VARCHAR(10)         NOT NULL DEFAULT 'tr',
    content         LONGTEXT            NOT NULL,
    format          VARCHAR(20)         NOT NULL DEFAULT 'plain',
    model_version   VARCHAR(50)             NULL,   -- Kullanılan AI model versiyonu
    confidence      DECIMAL(3,2)            NULL,   -- 0.00-1.00
    generated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_pt_pair    (episode_id, language),

    INDEX idx_pt_language    (language)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Podcast transkripsiyonları — AI-generated transcripts';

-- Foreign Keys for podcast_transcripts
ALTER TABLE podcast_transcripts
    ADD CONSTRAINT fk_pt_episode FOREIGN KEY (episode_id) REFERENCES podcast_episodes (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- ══════════════════════════════════════════════════════════════════════════════
-- VİDEO TABLOLARI
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- MUSIC_VIDEOS — Müzik videoları
-- Normal Form : BCNF — music_id UNIQUE (bir şarkının tek bir resmi videosu)
-- resolution  : 480p | 720p | 1080p | 1440p | 2160p | 4320p
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE music_videos (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    music_id        BINARY(16)          NOT NULL,   -- FK → musics.id (same DB)
    title           VARCHAR(500)        NOT NULL,
    description     TEXT                    NULL,
    video_url       VARCHAR(2048)       NOT NULL,
    thumbnail_url   VARCHAR(2048)           NULL,
    video_format    VARCHAR(10)         NOT NULL DEFAULT 'mp4',
    duration_seconds INT UNSIGNED           NULL,
    resolution      VARCHAR(10)             NULL,   -- 1080p, 4K, ...
    width_px        INT UNSIGNED            NULL,
    height_px       INT UNSIGNED            NULL,
    video_size_bytes BIGINT UNSIGNED        NULL,
    view_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    like_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_official     TINYINT(1)          NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_mv_music    (music_id),

    INDEX idx_mv_resolution  (resolution),
    INDEX idx_mv_official    (is_official),
    INDEX idx_mv_views       (view_count DESC),

    CHECK (video_format IN ('mp4', 'webm', 'mkv', 'avi')),
    CHECK (resolution IN ('480p', '720p', '1080p', '1440p', '2160p', '4320p'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Müzik videoları — music video metadata';

-- Foreign Keys for music_videos
ALTER TABLE music_videos
    ADD CONSTRAINT fk_mv_music FOREIGN KEY (music_id) REFERENCES musics (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- ──────────────────────────────────────────────────────────────────────────────
-- VIDEO_PLAYBACK_HISTORY — Video oynatma geçmişi
-- Normal Form : BCNF — id → {user_id, video_id, ...}
-- last_position: Son oynatma pozisyonu (saniye)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE video_playback_history (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- cross-DB FK → coremusic_auth.users.id
    video_id        INT UNSIGNED        NOT NULL,   -- FK → music_videos.id
    last_position   INT UNSIGNED        NOT NULL DEFAULT 0,   -- saniye
    watch_count     INT UNSIGNED        NOT NULL DEFAULT 1,
    completion_pct  DECIMAL(5,2)        NOT NULL DEFAULT 0.00, -- 0.00-100.00
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_vph_user       (user_id),
    INDEX idx_vph_video      (video_id),
    INDEX idx_vph_updated    (updated_at DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Video oynatma geçmişi — playback position, watch count';

-- Foreign Keys for video_playback_history
ALTER TABLE video_playback_history
    ADD CONSTRAINT fk_vph_video FOREIGN KEY (video_id) REFERENCES music_videos (id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Note: user_id FK references coremusic_auth.users - cross-database FK not supported in MySQL
-- Application layer must enforce referential integrity


-- ──────────────────────────────────────────────────────────────────────────────
-- VIDEO_SUBTITLES — Altyazı/dil destek
-- Normal Form : BCNF — (video_id, language) UNIQUE
-- format      : srt | vtt | ass
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE video_subtitles (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    video_id        INT UNSIGNED        NOT NULL,   -- FK → music_videos.id
    language        VARCHAR(10)         NOT NULL,   -- tr, en, de, fr, ...
    label           VARCHAR(100)        NOT NULL,   -- "Türkçe", "English", ...
    subtitle_url    VARCHAR(2048)       NOT NULL,
    format          VARCHAR(10)         NOT NULL DEFAULT 'srt',
    is_auto_generated TINYINT(1)        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_vs_pair    (video_id, language),

    INDEX idx_vs_language    (language),

    CHECK (format IN ('srt', 'vtt', 'ass'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Video altyazıları — subtitle tracks, multi-language';

-- Foreign Keys for video_subtitles
ALTER TABLE video_subtitles
    ADD CONSTRAINT fk_vs_video FOREIGN KEY (video_id) REFERENCES music_videos (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- ══════════════════════════════════════════════════════════════════════════════
-- RADYO TABLOLARI
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- RADIO_STATIONS — Radyo istasyonları
-- Normal Form : BCNF — id → {name, genre, frequency, ...}
-- genre       : pop | rock | jazz | classical | electronic | hip_hop | country
-- stream_type : icecast | shoutcast | hls | direct
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE radio_stations (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    name            VARCHAR(255)        NOT NULL,
    slug            VARCHAR(255)        NOT NULL,
    description     TEXT                    NULL,
    genre           VARCHAR(50)             NULL,
    frequency_mhz   DECIMAL(6,2)            NULL,   -- FM frekansı (ör: 92.50)
    stream_url      VARCHAR(2048)       NOT NULL,
    stream_type     VARCHAR(20)         NOT NULL DEFAULT 'icecast',
    website_url     VARCHAR(2048)           NULL,
    logo_url        VARCHAR(2048)           NULL,
    country         VARCHAR(50)             NULL,
    city            VARCHAR(100)            NULL,
    bitrate_kbps    INT UNSIGNED            NULL,
    listener_count  INT UNSIGNED        NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_rs_slug     (slug),

    INDEX idx_rs_genre      (genre),
    INDEX idx_rs_country    (country),
    INDEX idx_rs_active     (is_active),

    CHECK (stream_type IN ('icecast', 'shoutcast', 'hls', 'direct'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Radyo istasyonları — stream URLs, genres, locations';


-- ──────────────────────────────────────────────────────────────────────────────
-- RADIO_SCHEDULES — Yayın programları
-- Normal Form : BCNF — (station_id, day_of_week, start_time) UNIQUE
-- day_of_week : 0=Pazar, 1=Pazartesi ... 6=Cumartesi
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE radio_schedules (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    station_id      INT UNSIGNED        NOT NULL,   -- FK → radio_stations.id
    day_of_week     TINYINT UNSIGNED    NOT NULL,   -- 0=Pazar ... 6=Cumartesi
    start_time      TIME                NOT NULL,
    end_time        TIME                NOT NULL,
    program_name    VARCHAR(255)        NOT NULL,
    host_name       VARCHAR(255)            NULL,
    description     TEXT                    NULL,
    is_recurring    TINYINT(1)          NOT NULL DEFAULT 1,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_rs_schedule (station_id, day_of_week, start_time),

    INDEX idx_rs_day        (day_of_week),
    INDEX idx_rs_time       (start_time),

    CHECK (day_of_week >= 0 AND day_of_week <= 6)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Radyo yayın programları — weekly schedules';

-- Foreign Keys for radio_schedules
ALTER TABLE radio_schedules
    ADD CONSTRAINT fk_rsch_station FOREIGN KEY (station_id) REFERENCES radio_stations (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- ──────────────────────────────────────────────────────────────────────────────
-- RADIO_NOW_PLAYING — Anlık yayın durumu
-- Normal Form : BCNF — station_id UNIQUE (tek başına bir istasyon aktif)
-- Her istasyon için sadece bir aktif kayıt
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE radio_now_playing (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    station_id      INT UNSIGNED        NOT NULL,   -- FK → radio_stations.id
    track_title     VARCHAR(500)        NOT NULL,
    artist_name     VARCHAR(255)            NULL,
    album_name      VARCHAR(255)            NULL,
    genre           VARCHAR(50)             NULL,
    duration_seconds INT UNSIGNED            NULL,
    started_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_rnp_station  (station_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Radyo anlık yayın durumu — now playing info';

-- Foreign Keys for radio_now_playing
ALTER TABLE radio_now_playing
    ADD CONSTRAINT fk_rnp_station FOREIGN KEY (station_id) REFERENCES radio_stations (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- =============================================
-- coremusic_musics Database v8.0.0
-- Tables: 22 (12 musics + 4 podcast + 3 video + 3 radio)
-- BCNF Compliant: Yes
-- UUID: BINARY(16) for musics tables, INT UNSIGNED for podcast/video/radio
-- Soft Delete: is_deleted + deleted_at
-- =============================================
