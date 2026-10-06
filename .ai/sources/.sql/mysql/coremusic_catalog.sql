-- CoreMusic Database: coremusic_catalog
-- Version: 7.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_catalog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_catalog;

-- =============================================
-- Table: catalog_genres
-- Purpose: Hierarchical genre classification for music
-- BCNF: Each non-key attribute determines the key (genre_level, sort_order, etc.)
-- =============================================
CREATE TABLE catalog_genres (
    id BINARY(16) NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id BINARY(16),
    genre_level INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_genres_name (name),
    UNIQUE KEY uk_genres_slug (slug),
    INDEX idx_genres_parent_id (parent_id),
    INDEX idx_genres_sort_order (sort_order),
    CONSTRAINT fk_genres_parent FOREIGN KEY (parent_id) 
        REFERENCES catalog_genres(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Hierarchical music genre classification system';

-- =============================================
-- Table: catalog_artist_roles
-- Purpose: Roles that artists can have in music production
-- BCNF: Each non-key attribute determines the key (role_description, sort_order)
-- =============================================
CREATE TABLE catalog_artist_roles (
    id BINARY(16) NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    role_description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_artist_roles_name (role_name),
    INDEX idx_artist_roles_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Roles that artists can have in music production (vocalist, producer, etc.)';

-- =============================================
-- Table: catalog_album_types
-- Purpose: Types of albums (studio, live, compilation, etc.)
-- BCNF: Each non-key attribute determines the key (type_description, sort_order)
-- =============================================
CREATE TABLE catalog_album_types (
    id BINARY(16) NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    type_description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_album_types_name (type_name),
    INDEX idx_album_types_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Types of albums (studio, live, compilation, EP, single, etc.)';

-- =============================================
-- Table: catalog_playlist_types
-- Purpose: Types of playlists (user, AI-generated, editorial, etc.)
-- BCNF: Each non-key attribute determines the key (type_description, sort_order)
-- =============================================
CREATE TABLE catalog_playlist_types (
    id BINARY(16) NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    type_description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_playlist_types_name (type_name),
    INDEX idx_playlist_types_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Types of playlists (user-created, AI-generated, editorial, etc.)';

-- =============================================
-- Table: catalog_instruments
-- Purpose: Musical instruments categorized by type
-- BCNF: Each non-key attribute determines the key (category, description, sort_order)
-- =============================================
CREATE TABLE catalog_instruments (
    id BINARY(16) NOT NULL,
    name VARCHAR(100) NOT NULL,
    category ENUM('string','wind','percussion','keyboard','electronic','other') NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_instruments_name (name),
    INDEX idx_instruments_category (category),
    INDEX idx_instruments_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Musical instruments with category classification';

-- =============================================
-- Table: catalog_moods
-- Purpose: Mood/emotion tags for music classification
-- BCNF: Each non-key attribute determines the key (description, color, sort_order)
-- =============================================
CREATE TABLE catalog_moods (
    id BINARY(16) NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    color VARCHAR(7),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_moods_name (name),
    INDEX idx_moods_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Mood/emotion tags for music classification and recommendations';

-- =============================================
-- Table: catalog_countries
-- Purpose: Country reference data for artists and users
-- BCNF: Each non-key attribute determines the key (name, name_native, continent)
-- =============================================
CREATE TABLE catalog_countries (
    id BINARY(16) NOT NULL,
    code VARCHAR(2) NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_native VARCHAR(100),
    continent ENUM('asia','europe','africa','north_america','south_america','oceania','antarctica'),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_countries_code (code),
    INDEX idx_countries_continent (continent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Country reference data for artists, users, and regional content';

-- =============================================
-- Table: catalog_languages
-- Purpose: Language reference data for multilingual support
-- BCNF: Each non-key attribute determines the key (name, name_native, is_rtl)
-- =============================================
CREATE TABLE catalog_languages (
    id BINARY(16) NOT NULL,
    code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_native VARCHAR(100),
    is_rtl TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_languages_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Language reference data for multilingual interface and content';

-- =============================================
-- CoreMusic coremusic_catalog Database v7.0.0
-- Tables: 8
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
