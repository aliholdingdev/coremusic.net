-- coremusic_media — MEDYA & CİHAZ SENKRONİZASYONU VERİTABANI
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-10
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_media
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_media;

-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 1: CİHAZ YÖNETİMİ (8 tablo)
-- ══════════════════════════════════════════════════════════════════════════════

-- =============================================
-- Table: device_types
-- Purpose: Device type definitions for multi-device support
-- BCNF: Each non-key attribute determines the key (platform, has_screen, etc.)
-- =============================================
CREATE TABLE device_types (
    id BINARY(16) NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    type_description TEXT,
    platform ENUM('windows','linux','macos','android','ios','rpi','other') NOT NULL,
    has_screen TINYINT(1) DEFAULT 1,
    has_audio_output TINYINT(1) DEFAULT 1,
    has_bluetooth TINYINT(1) DEFAULT 0,
    has_wifi TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_device_types_name (type_name),
    INDEX idx_device_types_platform (platform),
    INDEX idx_device_types_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Device type definitions for multi-device support and synchronization';

-- =============================================
-- Table: devices
-- Purpose: User devices for sync, streaming, and multi-device access
-- BCNF: Each non-key attribute determines the key (device_name, device_uuid, etc.)
-- =============================================
CREATE TABLE devices (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    device_type_id BINARY(16) NOT NULL,
    device_name VARCHAR(200) NOT NULL,
    device_uuid VARCHAR(255) NOT NULL,
    device_fingerprint VARCHAR(255),
    os_version VARCHAR(50),
    app_version VARCHAR(50),
    ip_address VARCHAR(45),
    mac_address VARCHAR(17),
    is_active TINYINT(1) DEFAULT 1,
    is_primary TINYINT(1) DEFAULT 0,
    last_synced_at TIMESTAMP NULL,
    last_active_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_devices_uuid (device_uuid),
    INDEX idx_devices_user_id (user_id),
    INDEX idx_devices_device_type_id (device_type_id),
    INDEX idx_devices_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='User devices for synchronization, streaming, and multi-device access';

-- =============================================
-- Table: device_playlists
-- Purpose: Playlist sync status per device
-- BCNF: Each non-key attribute determines the key (sync_status, sync_priority, etc.)
-- =============================================
CREATE TABLE device_playlists (
    id BINARY(16) NOT NULL,
    device_id BINARY(16) NOT NULL,
    playlist_id BINARY(16) NOT NULL,
    sync_status ENUM('synced','pending','failed','disabled') DEFAULT 'pending',
    sync_priority INT DEFAULT 0,
    last_synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_device_playlists_device_playlist (device_id, playlist_id),
    INDEX idx_device_playlists_sync_status (sync_status),
    INDEX idx_device_playlists_sync_priority (sync_priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Playlist synchronization status per device for offline access';

-- =============================================
-- Table: device_tracks
-- Purpose: Track sync status per device for offline playback
-- BCNF: Each non-key attribute determines the key (file_path, file_size, etc.)
-- =============================================
CREATE TABLE device_tracks (
    id BINARY(16) NOT NULL,
    device_id BINARY(16) NOT NULL,
    music_id BINARY(16) NOT NULL,
    file_path VARCHAR(500),
    file_size BIGINT,
    file_format ENUM('mp3','flac','wav','aac','ogg') DEFAULT 'flac',
    sync_status ENUM('synced','pending','failed','deleted') DEFAULT 'pending',
    synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_device_tracks_device_music (device_id, music_id),
    INDEX idx_device_tracks_sync_status (sync_status),
    INDEX idx_device_tracks_file_format (file_format)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Track synchronization status per device for offline playback';

-- =============================================
-- Table: device_sync_history
-- Purpose: Device sync history for debugging and analytics
-- BCNF: Each non-key attribute determines the key (sync_type, sync_status, etc.)
-- =============================================
CREATE TABLE device_sync_history (
    id BINARY(16) NOT NULL,
    device_id BINARY(16) NOT NULL,
    sync_type ENUM('full','incremental','playlist','metadata') NOT NULL,
    sync_status ENUM('started','completed','failed','cancelled') NOT NULL,
    tracks_synced INT DEFAULT 0,
    tracks_failed INT DEFAULT 0,
    bytes_transferred BIGINT DEFAULT 0,
    duration_ms INT DEFAULT 0,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_sync_history_device_id (device_id),
    INDEX idx_sync_history_sync_type (sync_type),
    INDEX idx_sync_history_sync_status (sync_status),
    INDEX idx_sync_history_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Device synchronization history for debugging and analytics';

-- =============================================
-- Table: media_metadata
-- Purpose: Media file metadata for audio and image files
-- BCNF: Each non-key attribute determines the key (file_path, file_name, etc.)
-- =============================================
CREATE TABLE media_metadata (
    id BINARY(16) NOT NULL,
    music_id BINARY(16) NOT NULL,
    file_key VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(500) NOT NULL,
    file_extension VARCHAR(10),
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100),
    checksum_sha256 VARCHAR(64),
    metadata_json JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_media_metadata_file_key (file_key),
    UNIQUE KEY uk_media_metadata_music_id (music_id),
    INDEX idx_media_metadata_file_name (file_name),
    INDEX idx_media_metadata_mime_type (mime_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Media file metadata for audio, images, and other media files';

-- =============================================
-- Table: media_access
-- Purpose: Media file access control permissions
-- BCNF: Each non-key attribute determines the key (access_type, granted_by, etc.)
-- =============================================
CREATE TABLE media_access (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    file_key VARCHAR(255) NOT NULL,
    access_type ENUM('read','write','delete','admin') DEFAULT 'read',
    granted_by BINARY(16),
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_media_access_user_id (user_id),
    INDEX idx_media_access_file_key (file_key),
    INDEX idx_media_access_type (access_type),
    INDEX idx_media_access_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Media file access control permissions for sharing and collaboration';

-- =============================================
-- Table: media_audit
-- Purpose: Media file operation audit trail
-- BCNF: Each non-key attribute determines the key (action, file_key, etc.)
-- =============================================
CREATE TABLE media_audit (
    id BINARY(16) NOT NULL,
    user_id BINARY(16) NOT NULL,
    action ENUM('upload','download','delete','play','share','move','copy') NOT NULL,
    file_key VARCHAR(255) NOT NULL,
    file_path VARCHAR(500),
    file_size BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_media_audit_user_id (user_id),
    INDEX idx_media_audit_action (action),
    INDEX idx_media_audit_file_key (file_key),
    INDEX idx_media_audit_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Media file operation audit trail for security and compliance';

-- =============================================
-- coremusic_media Database v8.0.0
-- Tables: 8 (device_types, devices, device_playlists, device_tracks, device_sync_history, media_metadata, media_access, media_audit)
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- Collation: utf8mb4_unicode_ci
-- 
-- NOT: İndirme tabloları (download_queue, download_history, download_cache, download_sources)
--      coremusic_download veritabanına taşınmıştır.
-- =============================================

-- End of coremusic_media schema
