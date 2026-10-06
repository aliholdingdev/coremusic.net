-- CoreMusic Database: coremusic_user
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_user
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_user;

-- =============================================
-- Table: user_profiles
-- Purpose: Extended user profile data
-- BCNF: user_id is candidate key; all attributes fully depend on it
-- =============================================
CREATE TABLE user_profiles (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id (unique)',
    bio TEXT NULL COMMENT 'User biography',
    date_of_birth DATE NULL COMMENT 'Date of birth',
    country VARCHAR(2) NULL COMMENT 'ISO 3166-1 alpha-2 country code',
    language VARCHAR(10) DEFAULT 'en' COMMENT 'Preferred language code',
    timezone VARCHAR(50) DEFAULT 'UTC' COMMENT 'IANA timezone identifier',
    theme_gender ENUM('male','female','neutral') DEFAULT 'neutral' COMMENT 'Theme engine gender preference',
    notification_email TINYINT(1) DEFAULT 1 COMMENT 'Email notification preference',
    notification_push TINYINT(1) DEFAULT 1 COMMENT 'Push notification preference',
    notification_sms TINYINT(1) DEFAULT 0 COMMENT 'SMS notification preference',
    profile_visibility ENUM('public','private','friends') DEFAULT 'public' COMMENT 'Profile visibility level',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User profiles — bio, preferences, notification settings';

-- Indexes
CREATE UNIQUE INDEX idx_profiles_user ON user_profiles (user_id) COMMENT 'Unique user profile lookup';
CREATE INDEX idx_profiles_country ON user_profiles (country) COMMENT 'Country-based filtering';
CREATE INDEX idx_profiles_language ON user_profiles (language) COMMENT 'Language-based filtering';

-- Foreign Keys
ALTER TABLE user_profiles
    ADD CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_preferences
-- Purpose: Per-user and per-device preferences
-- BCNF: user_id is candidate key; preferences fully depend on it
-- =============================================
CREATE TABLE user_preferences (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id (unique)',
    device_type ENUM('desktop','mobile','tablet','car','studio','home') DEFAULT 'desktop' COMMENT 'Target device type',
    theme VARCHAR(50) DEFAULT 'default' COMMENT 'Theme identifier',
    audio_quality ENUM('low','medium','high','lossless') DEFAULT 'high' COMMENT 'Default audio quality',
    auto_play TINYINT(1) DEFAULT 1 COMMENT 'Auto-play next track',
    crossfade_duration INT DEFAULT 0 COMMENT 'Crossfade duration in seconds',
    normalize_audio TINYINT(1) DEFAULT 1 COMMENT 'Audio normalization enabled',
    explicit_content TINYINT(1) DEFAULT 1 COMMENT 'Allow explicit content',
    autoplay_recommendations TINYINT(1) DEFAULT 1 COMMENT 'Auto-play AI recommendations',
    download_over_wifi_only TINYINT(1) DEFAULT 1 COMMENT 'WiFi-only download restriction',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User preferences — audio, theme, playback settings';

-- Indexes
CREATE UNIQUE INDEX idx_preferences_user ON user_preferences (user_id) COMMENT 'Unique user preferences lookup';
CREATE INDEX idx_preferences_device ON user_preferences (device_type) COMMENT 'Device type filtering';

-- Foreign Keys
ALTER TABLE user_preferences
    ADD CONSTRAINT fk_preferences_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_listening_history
-- Purpose: Detailed listening event log
-- BCNF: (user_id, music_id, started_at) — immutable event log
-- =============================================
CREATE TABLE user_listening_history (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
    music_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
    playlist_id BINARY(16) NULL COMMENT 'FK → playlist if from playlist context',
    listen_duration_sec INT NOT NULL COMMENT 'Total listen duration in seconds',
    listen_percentage DECIMAL(5,2) NULL COMMENT 'Percentage of track listened (0.00–100.00)',
    started_at TIMESTAMP NOT NULL COMMENT 'Playback start timestamp',
    ended_at TIMESTAMP NULL COMMENT 'Playback end timestamp',
    device_type ENUM('desktop','mobile','tablet','car','studio','home') DEFAULT 'desktop' COMMENT 'Device used for listening',
    ip_address VARCHAR(45) NULL COMMENT 'Client IP address',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Listening history — tracks, duration, device context';

-- Indexes
CREATE INDEX idx_history_user ON user_listening_history (user_id) COMMENT 'User history lookup';
CREATE INDEX idx_history_music ON user_listening_history (music_id) COMMENT 'Track play count lookup';
CREATE INDEX idx_history_started ON user_listening_history (started_at) COMMENT 'Time-based history queries';
CREATE INDEX idx_history_user_started ON user_listening_history (user_id, started_at) COMMENT 'User chronological history';

-- Foreign Keys
ALTER TABLE user_listening_history
    ADD CONSTRAINT fk_history_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_history_music FOREIGN KEY (music_id) REFERENCES coremusic_musics.musics (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_favorites
-- Purpose: Polymorphic favorites (music, album, playlist, artist, podcast)
-- BCNF: (user_id, item_type, item_id) is candidate key — unique constraint enforces BCNF
-- =============================================
CREATE TABLE user_favorites (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
    item_type ENUM('music','album','playlist','artist','podcast') NOT NULL COMMENT 'Favorited item type',
    item_id BINARY(16) NOT NULL COMMENT 'Favorited item ID (polymorphic)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Favorite timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User favorites — polymorphic item references';

-- Indexes
CREATE UNIQUE INDEX idx_favorites_unique ON user_favorites (user_id, item_type, item_id) COMMENT 'Unique favorite per user-item pair';
CREATE INDEX idx_favorites_item ON user_favorites (item_type, item_id) COMMENT 'Item popularity lookup';

-- Foreign Keys
ALTER TABLE user_favorites
    ADD CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_follows
-- Purpose: User-to-user follow relationships
-- BCNF: (follower_id, following_id) is candidate key
-- =============================================
CREATE TABLE user_follows (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    follower_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id — follower',
    following_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id — followed user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Follow timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User follow relationships — social graph';

-- Indexes
CREATE UNIQUE INDEX idx_follows_unique ON user_follows (follower_id, following_id) COMMENT 'Unique follow relationship';
CREATE INDEX idx_follows_following ON user_follows (following_id) COMMENT 'Followers of user lookup';

-- Foreign Keys
ALTER TABLE user_follows
    ADD CONSTRAINT fk_follows_follower FOREIGN KEY (follower_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_follows_following FOREIGN KEY (following_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: playback_queue
-- Purpose: Per-user playback queue with source context
-- BCNF: (user_id, position) — position is candidate key within user context
-- =============================================
CREATE TABLE playback_queue (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
    music_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
    position INT NOT NULL COMMENT 'Queue position (0-based)',
    source ENUM('user','ai','radio','podcast') DEFAULT 'user' COMMENT 'Queue source type',
    source_id BINARY(16) NULL COMMENT 'Source entity ID (playlist, radio station, etc.)',
    is_playing TINYINT(1) DEFAULT 0 COMMENT 'Currently playing flag',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Playback queue — per-user track queue with ordering';

-- Indexes
CREATE INDEX idx_queue_user_position ON playback_queue (user_id, position) COMMENT 'User queue ordered retrieval';
CREATE INDEX idx_queue_music ON playback_queue (music_id) COMMENT 'Track queue lookup';

-- Foreign Keys
ALTER TABLE playback_queue
    ADD CONSTRAINT fk_queue_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_queue_music FOREIGN KEY (music_id) REFERENCES coremusic_musics.musics (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_downloads
-- Purpose: User download history and status tracking
-- BCNF: (user_id, music_id) is candidate key — one download per user per track
-- =============================================
CREATE TABLE user_downloads (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_auth.users.id',
    music_id BINARY(16) NOT NULL COMMENT 'FK → coremusic_musics.musics.id',
    download_status ENUM('pending','downloading','completed','failed','expired') DEFAULT 'pending' COMMENT 'Download status',
    file_path VARCHAR(500) NULL COMMENT 'Local file path',
    file_size BIGINT NULL COMMENT 'File size in bytes',
    file_format ENUM('mp3','flac','wav','aac','ogg') DEFAULT 'flac' COMMENT 'Downloaded file format',
    quality ENUM('128','192','320','lossless') DEFAULT 'lossless' COMMENT 'Audio quality tier',
    downloaded_at TIMESTAMP NULL COMMENT 'Download completion timestamp',
    expires_at TIMESTAMP NULL COMMENT 'Download expiration timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User downloads — status, format, file tracking';

-- Indexes
CREATE INDEX idx_downloads_user ON user_downloads (user_id) COMMENT 'User downloads lookup';
CREATE INDEX idx_downloads_music ON user_downloads (music_id) COMMENT 'Track download lookup';
CREATE INDEX idx_downloads_status ON user_downloads (download_status) COMMENT 'Status filtering';
CREATE UNIQUE INDEX idx_downloads_unique ON user_downloads (user_id, music_id) COMMENT 'One download per user per track';

-- Foreign Keys
ALTER TABLE user_downloads
    ADD CONSTRAINT fk_downloads_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_downloads_music FOREIGN KEY (music_id) REFERENCES coremusic_musics.musics (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- CoreMusic coremusic_user Database v8.0.0
-- Tables: 7
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
