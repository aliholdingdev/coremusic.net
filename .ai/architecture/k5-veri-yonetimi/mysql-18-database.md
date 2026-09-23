---
title: "MySQL 8.0 Veritabanı"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 18
dependencies: [K0, K1]
---

# MySQL 18 Database - Merkezi Veritabanı

## Genel Bakış

MySQL 8.0 tabanlı 18 BCNF (Boyce-Codd Normal Form) veritabanı, COREMUSIC sisteminin temel veri depolama altyapısıdır. 156 tablo ile müzik metadata, kullanıcı verileri, ödeme işlemleri ve sistem konfigürasyonlarını yönetir. High availability, read replication ve partitioning stratejileri ile enterprise-grade performans sağlar.

## Teknik Detaylar

### Veritabanı Şeması Tasarımı

```sql
-- =====================================================
-- Kullanıcı Veritabanı (coremusic_users)
-- =====================================================

CREATE DATABASE coremusic_users
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Kullanıcılar Tablosu
CREATE TABLE users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    avatar_url VARCHAR(500),
    date_of_birth DATE,
    country_code CHAR(2),
    language_code CHAR(5) DEFAULT 'tr-TR',
    timezone VARCHAR(50) DEFAULT 'Europe/Istanbul',
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    account_status ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
    subscription_tier ENUM('free', 'premium', 'family') DEFAULT 'free',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_status (account_status),
    INDEX idx_users_created (created_at)
) ENGINE=InnoDB;

-- Kullanıcı Ayarları
CREATE TABLE user_settings (
    setting_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_user_setting (user_id, setting_key),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Kullanıcı Oturumları
CREATE TABLE user_sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_type ENUM('web', 'ios', 'android', 'desktop', 'other') DEFAULT 'other',
    device_name VARCHAR(100),
    location_country CHAR(2),
    location_city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    last_activity_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    
    INDEX idx_sessions_user (user_id),
    INDEX idx_sessions_expires (expires_at),
    INDEX idx_sessions_active (is_active),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Müzik Metadata Veritabanı (coremusic_music)
-- =====================================================

CREATE DATABASE coremusic_music
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Sanatçılar
CREATE TABLE artists (
    artist_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    bio TEXT,
    image_url VARCHAR(500),
    verified BOOLEAN DEFAULT FALSE,
    monthly_listeners INT UNSIGNED DEFAULT 0,
    genre_primary VARCHAR(50),
    country_of_origin CHAR(2),
    formed_year YEAR,
    website_url VARCHAR(500),
    social_links JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_artists_name (name),
    INDEX idx_artists_slug (slug),
    INDEX idx_artists_genre (genre_primary),
    FULLTEXT INDEX ft_artists_search (name, bio)
) ENGINE=InnoDB;

-- Albümler
CREATE TABLE albums (
    album_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    artist_id BIGINT UNSIGNED NOT NULL,
    release_date DATE,
    album_type ENUM('studio', 'live', 'compilation', 'ep', 'single') DEFAULT 'studio',
    cover_art_url VARCHAR(500),
    cover_art_small_url VARCHAR(500),
    total_discs TINYINT UNSIGNED DEFAULT 1,
    total_tracks SMALLINT UNSIGNED DEFAULT 0,
    total_duration_ms BIGINT UNSIGNED DEFAULT 0,
    label VARCHAR(255),
    copyright VARCHAR(255),
    upc VARCHAR(20),
    metadata_source VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_albums_artist (artist_id),
    INDEX idx_albums_release (release_date),
    INDEX idx_albums_slug (slug),
    FULLTEXT INDEX ft_albums_search (title),
    FOREIGN KEY (artist_id) REFERENCES artists(artist_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Şarkılar
CREATE TABLE tracks (
    track_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    album_id BIGINT UNSIGNED,
    artist_id BIGINT UNSIGNED NOT NULL,
    duration_ms INT UNSIGNED NOT NULL,
    track_number SMALLINT UNSIGNED,
    disc_number TINYINT UNSIGNED DEFAULT 1,
    isrc VARCHAR(12),
    is_explicit BOOLEAN DEFAULT FALSE,
    is_instrumental BOOLEAN DEFAULT FALSE,
    
    -- Audio metadata
    bpm SMALLINT UNSIGNED,
    key_signature VARCHAR(10),
    time_signature VARCHAR(10),
    loudness_lufs DECIMAL(6,2),
    
    -- File info
    file_format VARCHAR(20),
    sample_rate INT UNSIGNED,
    bit_depth TINYINT UNSIGNED,
    bitrate_kbps INT UNSIGNED,
    file_size_bytes BIGINT UNSIGNED,
    file_url VARCHAR(500),
    preview_url VARCHAR(500),
    
    -- AI features
    mood_valence DECIMAL(3,3),
    mood_arousal DECIMAL(3,3),
    energy_level DECIMAL(3,3),
    danceability DECIMAL(3,3),
    
    -- Analytics
    play_count BIGINT UNSIGNED DEFAULT 0,
    like_count INT UNSIGNED DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tracks_album (album_id),
    INDEX idx_tracks_artist (artist_id),
    INDEX idx_tracks_slug (slug),
    INDEX idx_tracks_isrc (isrc),
    INDEX idx_tracks_bpm (bpm),
    FULLTEXT INDEX ft_tracks_search (title),
    FOREIGN KEY (album_id) REFERENCES albums(album_id) ON DELETE SET NULL,
    FOREIGN KEY (artist_id) REFERENCES artists(artist_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Ses Parmak İzi Tablosu
CREATE TABLE audio_fingerprints (
    fingerprint_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    track_id BIGINT UNSIGNED NOT NULL,
    algorithm ENUM('chromaprint', 'echoprint', 'dejavu') NOT NULL,
    fingerprint_data JSON NOT NULL,
    hash_value VARCHAR(64) NOT NULL,
    duration_seconds DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_fingerprints_track (track_id),
    INDEX idx_fingerprints_hash (hash_value),
    INDEX idx_fingerprints_algo (algorithm),
    FOREIGN KEY (track_id) REFERENCES tracks(track_id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### Partition Stratejisi

```sql
-- Analytics tabloları için partition
CREATE TABLE user_listens (
    listen_id BIGINT UNSIGNED AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    track_id BIGINT UNSIGNED NOT NULL,
    listened_at TIMESTAMP NOT NULL,
    listen_duration_ms INT UNSIGNED,
    completion_percentage DECIMAL(5,2),
    device_type VARCHAR(20),
    country_code CHAR(2),
    
    PRIMARY KEY (listen_id, listened_at),
    INDEX idx_listens_user (user_id, listened_at),
    INDEX idx_listens_track (track_id, listened_at)
) ENGINE=InnoDB
PARTITION BY RANGE (UNIX_TIMESTAMP(listened_at)) (
    PARTITION p2024_q1 VALUES LESS THAN (UNIX_TIMESTAMP('2024-04-01')),
    PARTITION p2024_q2 VALUES LESS THAN (UNIX_TIMESTAMP('2024-07-01')),
    PARTITION p2024_q3 VALUES LESS THAN (UNIX_TIMESTAMP('2024-10-01')),
    PARTITION p2024_q4 VALUES LESS THAN (UNIX_TIMESTAMP('2025-01-01')),
    PARTITION p2025_q1 VALUES LESS THAN (UNIX_TIMESTAMP('2025-04-01')),
    PARTITION p2025_q2 VALUES LESS THAN (UNIX_TIMESTAMP('2025-07-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### View Tanımları

```sql
-- Popüler şarkılar view
CREATE VIEW vw_popular_tracks AS
SELECT 
    t.track_id,
    t.title,
    t.slug,
    a.name AS artist_name,
    al.title AS album_title,
    t.play_count,
    t.like_count,
    t.duration_ms,
    t.bpm,
    t.mood_valence,
    t.mood_arousal,
    t.cover_art_url
FROM tracks t
JOIN artists a ON t.artist_id = a.artist_id
LEFT JOIN albums al ON t.album_id = al.album_id
WHERE t.play_count > 1000
ORDER BY t.play_count DESC;

-- Kullanıcı profil özeti
CREATE VIEW vw_user_profile_summary AS
SELECT 
    u.user_id,
    u.username,
    u.display_name,
    u.avatar_url,
    u.subscription_tier,
    COUNT(DISTINCT ul.track_id) AS unique_tracks_played,
    SUM(ul.listen_duration_ms) / 3600000 AS total_hours_listened,
    MAX(ul.listened_at) AS last_listen_at
FROM users u
LEFT JOIN user_listens ul ON u.user_id = ul.user_id
GROUP BY u.user_id;
```

## Performans / Ölçeklenebilirlik

### Index Stratejisi

```sql
-- Composite indexes for common queries
CREATE INDEX idx_tracks_search ON tracks (is_explicit, play_count, duration_ms);
CREATE INDEX idx_albums_search ON albums (release_date, artist_id, album_type);
CREATE INDEX idx_user_activity ON user_listens (user_id, listened_at DESC);

-- Covering indexes
CREATE INDEX idx_tracks_cover ON tracks (album_id, artist_id) 
    INCLUDE (title, duration_ms, play_count);
```

### Query Optimizasyonu

```sql
-- Slow query log
SET GLOBAL slow_query_log = 1;
SET GLOBAL slow_query_log_file = '/var/log/mysql/slow.log';
SET GLOBAL long_query_time = 1;  -- 1 saniye

-- Query cache (MySQL 8.0'da kaldırıldı, alternatif)
-- ProxySQL veya uygulama seviyesinde cache kullanılmalı
```

### Read Replicas

```yaml
replication:
  primary:
    host: "db-primary.coremusic.internal"
    port: 3306
    max_connections: 500
  
  replicas:
    - host: "db-replica-1.coremusic.internal"
      port: 3306
      max_connections: 200
      read_only: true
    
    - host: "db-replica-2.coremusic.internal"
      port: 3306
      max_connections: 200
      read_only: true
  
  replication_mode: "async"
  semi_sync: true
```

## API / Konfigürasyon

```yaml
# config/database.yaml
mysql:
  host: "localhost"
  port: 3306
  username: "coremusic_app"
  password: "${DB_PASSWORD}"
  
  databases:
    users: "coremusic_users"
    music: "coremusic_music"
    audio: "coremusic_audio"
    playlists: "coremusic_playlists"
    analytics: "coremusic_analytics"
    ai: "coremusic_ai"
    social: "coremusic_social"
    store: "coremusic_store"
    payments: "coremusic_payments"
    content: "coremusic_content"
    search: "coremusic_search"
    notifications: "coremusic_notifications"
    sessions: "coremusic_sessions"
    logs: "coremusic_logs"
    config: "coremusic_config"
    integrations: "coremusic_integrations"
    local: "coremusic_local"
    cache: "coremusic_cache"
  
  pool:
    min_connections: 5
    max_connections: 100
    timeout_seconds: 30
    idle_timeout_seconds: 600
  
  charset: "utf8mb4"
  collation: "utf8mb4_unicode_ci"
```

## Durum: Implementasyon

MySQL 18 Database modülü **stable** durumdadır. 18 BCNF veritabanı ve 156 tablo production-ready. Partition stratejisi ve read replication aktif olarak kullanılmaktadır.
