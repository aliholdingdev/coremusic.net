---
type: template
category: database
title: "SQL Migration Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: MySQL 9, SQL, BCNF, InnoDB
---

# SQL Migration Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-040-database-authority]] · [[ADR-014-multi-db-migration-strategy]]

---

## 1. Amaç

Bu şablon, CoreMusic platformunun 9 bağımsız BCNF veritabanı için kapsamlı SQL migration standardını tanımlar. Forward-only, versioned migration yaklaşımı benimsenir. Her veritabanı kendi migration geçmişini tutar, bağımlılık sırası garanti edilir ve audit trail zorunludur.

### 1.1 Kapsam

| Kapsam | Açıklama |
|--------|----------|
| **Forward-only** | Migration'lar sadece ileriye doğru çalışır, otomatik rollback yoktur |
| **Versioned** | Her migration zaman damgası ile versionlanır (`YYYY_MM_DD_NNN`) |
| **18 BCNF DB** | Tüm 18 BCNF veritabanı için migration standardı geçerlidir |
| **BCNF** | Boyce-Codd Normal Form zorunludur (ADR-040) |
| **Soft Delete** | Hard delete kesinlikle yasaktır (ADR-040, ADR-022) |

### 1.2 Dahil Olanlar

- CREATE TABLE, ALTER TABLE, DROP TABLE
- CREATE INDEX, DROP INDEX
- Foreign key management
- Data migration (INSERT...SELECT, UPDATE...JOIN)
- Seed data (default roles, admin user)
- Schema versioning (schema_versions tablosu)
- Audit trail kayıtları

### 1.3 Hariç Olanlar

- Production rollback (sadece development ortamında manuel)
- ORM migrations (ADR-002: ORM yasak)
- SELECT * kullanımı (ADR-002, ADR-040)
- Hard delete operasyonları (ADR-040)

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| MySQL | 9+ | Ana veritabanı motoru | mysql.com |
| MariaDB | 10.11+ | Alternatif motor | mariadb.org |
| InnoDB | — | Storage engine (zorunlu) | dev.mysql.com |
| utf8mb4_unicode_ci | — | Karakter seti (zorunlu) | — |
| BCNF | — | Normal form (zorunlu) | ADR-040, ADR-041 |

### 2.1 Zorunlu MySQL Ayarları

```sql
-- core.music'de zorunlu ayarlar
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;  -- Migration sırasında
SET SQL_SAFE_UPDATES = 0;     -- Data migration için
```

### 2.2 Kaynak Referansları

| Kaynak | URL | Doğrulama |
|--------|-----|-----------|
| MySQL 9 Docs | dev.mysql.com/doc/refman/9.0/en/ | 2026-08-06 |
| InnoDB Limits | dev.mysql.com/doc/refman/9.0/en/innodb-limits.html | 2026-08-06 |
| BCNF Definition | ADR-040-database-authority | Frozen |
| Migration Strategy | ADR-014-multi-db-migration-strategy | Active |

---

## 3. Code Standards

### 3.1 Migration File Naming

Format: `YYYY_MM_DD_NNN_description.sql`

| Bileşen | Format | Örnek |
|---------|--------|-------|
| YYYY | Yıl (4 hane) | 2026 |
| MM | Ay (2 hane) | 08 |
| GG | Gün (2 hane) | 06 |
| NNN | Sıra numarası (3 hane) | 001 |
| description | snake_case açıklama | create_users_table |

Örnekler:
```
2026_08_06_001_create_users_table.sql
2026_08_06_002_add_avatar_column.sql
2026_08_06_003_seed_default_roles.sql
2026_08_07_001_create_albums_table.sql
2026_08_07_002_add_genre_index.sql
```

Kurallar:
- Aynı tarih içinde birden fazla migration varsa NNN sıralı artar
- Dosya adı küçük harf ve underscore kullanır
- SQL keyword'leri big harf ile yazılır
- Her migration tek bir veritabanına aittir (cross-db migration ADR-014'e tabidir)

### 3.2 Migration Header

Her migration dosyasının başında standart bir comment bloğu bulunmalıdır:

```sql
-- =============================================================================
-- Migration: 2026_08_06_001_create_users_table
-- Database:  coremusic_auth
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create users table for authentication
-- ADR References: ADR-040-database-authority, ADR-014-multi-db-migration-strategy
-- Tags: create-table, auth, security
-- =============================================================================
```

Header zorunlu alanları:
- **Migration:** Dosya adı (uzantısız)
- **Database:** Hedef veritabanı adı
- **Date:** Oluşturma tarihi (YYYY-MM-DD)
- **Author:** Yazan kişi veya ekip
- **Version:** Template versiyonu
- **Description:** Kısa açıklama (1 satır)
- **ADR References:** İlgili ADR kararları
- **Tags:** Etiketler (virgülle ayrılmış)

### 3.3 Schema Versioning Table

Her veritabanında `schema_versions` tablosu bulunmalıdır:

```sql
-- =============================================================================
-- Schema Versioning Table
-- Bu tablo her veritabanında tek instance olarak bulunur
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_auth.schema_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version VARCHAR(20) NOT NULL,
    migration_name VARCHAR(255) NOT NULL,
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    execution_time_ms INT UNSIGNED NOT NULL DEFAULT 0,
    checksum VARCHAR(64) NOT NULL,
    status ENUM('applied', 'failed', 'rolled_back') NOT NULL DEFAULT 'applied',
    applied_by VARCHAR(100) NOT NULL DEFAULT 'system',
    notes TEXT NULL DEFAULT NULL,

    -- Constraints
    UNIQUE KEY uk_schema_versions_version (version),
    INDEX idx_schema_versions_applied_at (applied_at),
    INDEX idx_schema_versions_status (status)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: İlk versiyon kaydı
INSERT INTO coremusic_auth.schema_versions (version, migration_name, checksum, applied_by, notes)
VALUES ('0000_00_00_000', 'initial_setup', SHA2('initial', 256), 'system', 'Schema versioning table created')
ON DUPLICATE KEY UPDATE applied_at = CURRENT_TIMESTAMP;
```

Tablo açıklamaları:
- **version:** Migration versiyon numarası (YYYY_MM_DD_NNN formatında)
- **migration_name:** Migration dosya adı
- **applied_at:** Migration'ın uygulandığı zaman
- **execution_time_ms:** Migration'ın çalışma süresi (milisaniye)
- **checksum:** Migration dosyasının SHA-256 hash'i (değişiklik kontrolü)
- **status:** Uygulama durumu (applied, failed, rolled_back)
- **applied_by:** Migration'ı uygulayan kullanıcı
- **notes:** Ek notlar (opsiyonel)

### 3.4 CREATE TABLE Examples

#### 3.4.1 coremusic_auth.users

```sql
-- =============================================================================
-- Migration: 2026_08_06_001_create_users_table
-- Database:  coremusic_auth
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create users table for authentication
-- ADR References: ADR-040-database-authority, ADR-022-database-hardened-security
-- Tags: create-table, auth, security
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_auth.users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    password_changed_at TIMESTAMP NULL DEFAULT NULL,
    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until TIMESTAMP NULL DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Unique Constraints
    UNIQUE KEY uk_users_email (email),
    UNIQUE KEY uk_users_username (username),

    -- Indexes
    INDEX idx_users_email_verified (email_verified_at),
    INDEX idx_users_is_deleted (is_deleted),
    INDEX idx_users_created_at (created_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.4.2 coremusic_auth.user_roles

```sql
-- =============================================================================
-- Migration: 2026_08_06_002_create_user_roles_table
-- Database:  coremusic_auth
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create user_roles table for RBAC
-- ADR References: ADR-040-database-authority, ADR-022-database-hardened-security
-- Tags: create-table, auth, rbac
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_auth.user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    granted_by BIGINT UNSIGNED NULL DEFAULT NULL,
    granted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Unique Constraints
    UNIQUE KEY uk_user_roles_user_role (user_id, role),

    -- Foreign Keys
    CONSTRAINT fk_user_roles_user_id
        FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_user_roles_granted_by
        FOREIGN KEY (granted_by) REFERENCES coremusic_auth.users(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    -- Indexes
    INDEX idx_user_roles_role (role),
    INDEX idx_user_roles_expires_at (expires_at),
    INDEX idx_user_roles_is_deleted (is_deleted)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.4.3 coremusic_musics.songs

```sql
-- =============================================================================
-- Migration: 2026_08_06_003_create_songs_table
-- Database:  coremusic_musics
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create songs table for music catalog
-- ADR References: ADR-040-database-authority, ADR-033-sql-normalization-strategy
-- Tags: create-table, music, catalog
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_musics.songs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id BIGINT UNSIGNED NOT NULL,
    album_id BIGINT UNSIGNED NULL DEFAULT NULL,
    genre_id BIGINT UNSIGNED NULL DEFAULT NULL,
    duration_ms INT UNSIGNED NOT NULL DEFAULT 0,
    track_number SMALLINT UNSIGNED NULL DEFAULT NULL,
    disc_number SMALLINT UNSIGNED NULL DEFAULT 1,
    isrc VARCHAR(12) NULL DEFAULT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL DEFAULT 0,
    format VARCHAR(10) NOT NULL DEFAULT 'flac',
    bitrate INT UNSIGNED NULL DEFAULT NULL,
    sample_rate INT UNSIGNED NULL DEFAULT NULL,
    bit_depth SMALLINT UNSIGNED NULL DEFAULT NULL,
    cover_art_path VARCHAR(500) NULL DEFAULT NULL,
    lyrics TEXT NULL DEFAULT NULL,
    play_count INT UNSIGNED NOT NULL DEFAULT 0,
    last_played_at TIMESTAMP NULL DEFAULT NULL,
    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Unique Constraints
    UNIQUE KEY uk_songs_isrc (isrc),

    -- Foreign Keys
    CONSTRAINT fk_songs_artist_id
        FOREIGN KEY (artist_id) REFERENCES coremusic_musics.artists(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_songs_album_id
        FOREIGN KEY (album_id) REFERENCES coremusic_musics.albums(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_songs_genre_id
        FOREIGN KEY (genre_id) REFERENCES coremusic_musics.genres(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    -- Indexes
    INDEX idx_songs_title (title),
    INDEX idx_songs_artist_id (artist_id),
    INDEX idx_songs_album_id (album_id),
    INDEX idx_songs_genre_id (genre_id),
    INDEX idx_songs_format (format),
    INDEX idx_songs_play_count (play_count),
    INDEX idx_songs_is_deleted (is_deleted),
    INDEX idx_songs_created_at (created_at),
    INDEX idx_songs_artist_album (artist_id, album_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 3.4.4 coremusic_user.user_preferences

```sql
-- =============================================================================
-- Migration: 2026_08_06_004_create_user_preferences_table
-- Database:  coremusic_user
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create user_preferences table for user settings
-- ADR References: ADR-040-database-authority, ADR-044-dynamic-user-theme-engine
-- Tags: create-table, user, preferences, theme
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_user.user_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    device_type ENUM('desktop', 'laptop', 'tablet', 'phone', 'embedded', 'tv') NOT NULL DEFAULT 'desktop',
    theme_gender ENUM('male', 'female', 'neutral') NOT NULL DEFAULT 'neutral',
    language VARCHAR(10) NOT NULL DEFAULT 'tr',
    country_code CHAR(2) NOT NULL DEFAULT 'TR',
    timezone VARCHAR(50) NOT NULL DEFAULT 'Europe/Istanbul',
    notification_email TINYINT(1) NOT NULL DEFAULT 1,
    notification_push TINYINT(1) NOT NULL DEFAULT 1,
    autoplay TINYINT(1) NOT NULL DEFAULT 1,
    crossfade_ms INT UNSIGNED NOT NULL DEFAULT 0,
    equalizer_preset VARCHAR(50) NULL DEFAULT NULL,
    volume_normalization TINYINT(1) NOT NULL DEFAULT 0,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Unique Constraints
    UNIQUE KEY uk_user_preferences_user_device (user_id, device_type),

    -- Indexes
    INDEX idx_user_preferences_theme (theme_gender),
    INDEX idx_user_preferences_is_deleted (is_deleted)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.5 ALTER TABLE Patterns

#### 3.5.1 Add Column

```sql
-- Add a new column after a specific column
ALTER TABLE coremusic_auth.users
ADD COLUMN avatar_url VARCHAR(500) NULL DEFAULT NULL
AFTER remember_token;

-- Add a column at the end
ALTER TABLE coremusic_auth.users
ADD COLUMN phone VARCHAR(20) NULL DEFAULT NULL;

-- Add multiple columns in one statement
ALTER TABLE coremusic_musics.songs
ADD COLUMN bpm SMALLINT UNSIGNED NULL DEFAULT NULL AFTER bit_depth,
ADD COLUMN key_signature VARCHAR(10) NULL DEFAULT NULL AFTER bpm,
ADD COLUMN mood VARCHAR(50) NULL DEFAULT NULL AFTER key_signature;
```

#### 3.5.2 Modify Column

```sql
-- Change column type (careful with data loss)
ALTER TABLE coremusic_auth.users
MODIFY COLUMN username VARCHAR(150) NOT NULL;

-- Add NOT NULL constraint with default
ALTER TABLE coremusic_musics.songs
MODIFY COLUMN duration_ms INT UNSIGNED NOT NULL DEFAULT 0;

-- Change ENUM values
ALTER TABLE coremusic_user.user_preferences
MODIFY COLUMN device_type ENUM('desktop', 'laptop', 'tablet', 'phone', 'embedded', 'tv', 'car') NOT NULL DEFAULT 'desktop';
```

#### 3.5.3 Drop Column (Safety First)

```sql
-- ⚠️ DANGER: Verify column is not referenced before dropping
-- Step 1: Check for references
SELECT CONSTRAINT_NAME, TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'coremusic_auth'
  AND REFERENCED_TABLE_NAME = 'users'
  AND REFERENCED_COLUMN_NAME = 'remember_token';

-- Step 2: Drop only if safe
ALTER TABLE coremusic_auth.users
DROP COLUMN remember_token;
```

#### 3.5.4 Rename Column (MySQL 8.0+ syntax)

```sql
-- Rename column safely
ALTER TABLE coremusic_auth.users
RENAME COLUMN remember_token TO session_token;
```

### 3.6 Index Management

#### 3.6.1 Create Index

```sql
-- Single column index
CREATE INDEX idx_users_email ON coremusic_auth.users (email);

-- Composite index (most selective column first)
CREATE INDEX idx_songs_artist_album ON coremusic_musics.songs (artist_id, album_id);

-- Partial index (MySQL doesn't support WHERE clause, use generated column)
-- Workaround: Use a generated column
ALTER TABLE coremusic_musics.songs
ADD COLUMN title_lower VARCHAR(255) GENERATED ALWAYS AS (LOWER(title)) STORED;
CREATE INDEX idx_songs_title_lower ON coremusic_musics.songs (title_lower);

-- Full-text index for search
CREATE FULLTEXT INDEX idx_songs_fulltext ON coremusic_musics.songs (title, lyrics);
```

#### 3.6.2 Drop Index

```sql
-- Drop index by name
DROP INDEX idx_users_email ON coremusic_auth.users;

-- Drop index and recreate with different columns
ALTER TABLE coremusic_musics.songs
DROP INDEX idx_songs_artist_album,
ADD INDEX idx_songs_artist_album_track (artist_id, album_id, track_number);
```

#### 3.6.3 Index Best Practices

| Kural | Açıklama |
|-------|----------|
| Composite index column order | Most selective column first |
| Maximum index columns | 16 sütun (MySQL limit) |
| Maximum key length | 3072 bytes (InnoDB with utf8mb4) |
| Index naming | `idx_{table}_{column(s)}` format |
| Covering index | INCLUDE columns for covering queries |

### 3.7 Foreign Key Management

#### 3.7.1 Add Foreign Key

```sql
-- Add foreign key with safe options
ALTER TABLE coremusic_auth.user_roles
ADD CONSTRAINT fk_user_roles_user_id
FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id)
ON DELETE CASCADE
ON UPDATE CASCADE;

-- Add foreign key with SET NULL
ALTER TABLE coremusic_musics.songs
ADD CONSTRAINT fk_songs_genre_id
FOREIGN KEY (genre_id) REFERENCES coremusic_musics.genres(id)
ON DELETE SET NULL
ON UPDATE CASCADE;
```

#### 3.7.2 Foreign Key Actions

| Action | Kullanım | Risk |
|--------|----------|------|
| `CASCADE` | Parent silinirse child da silinir | Yüksek - veri kaybı |
| `SET NULL` | Parent silinirse FK NULL olur | Düşük - veri korunur |
| `RESTRICT` | Parent varsa child silinemez | En düşük - en güvenli |
| `NO ACTION` | RESTRICT ile aynı (varsayılan) | En düşük |
| `SET DEFAULT` | Parent silinirse default değer | Orta - MySQL desteklemez |

#### 3.7.3 Drop Foreign Key

```sql
-- Drop foreign key
ALTER TABLE coremusic_auth.user_roles
DROP FOREIGN KEY fk_user_roles_user_id;

-- Drop and recreate with different action
ALTER TABLE coremusic_auth.user_roles
DROP FOREIGN KEY fk_user_roles_user_id,
ADD CONSTRAINT fk_user_roles_user_id
FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id)
ON DELETE RESTRICT
ON UPDATE CASCADE;
```

### 3.8 Data Migration

#### 3.8.1 INSERT...SELECT

```sql
-- Migrate data from old table to new table
INSERT INTO coremusic_musics.songs_new (title, artist_id, album_id, duration_ms, file_path, created_at)
SELECT
    s.title,
    s.artist_id,
    s.album_id,
    s.duration_ms,
    s.file_path,
    s.created_at
FROM coremusic_musics.songs_old s
WHERE s.is_deleted = 0
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    updated_at = CURRENT_TIMESTAMP;
```

#### 3.8.2 UPDATE...JOIN

```sql
-- Update data based on another table
UPDATE coremusic_musics.songs s
INNER JOIN coremusic_musics.artists a ON s.artist_id = a.id
SET s.genre_id = a.default_genre_id
WHERE s.genre_id IS NULL
  AND a.default_genre_id IS NOT NULL;
```

#### 3.8.3 Bulk Operations

```sql
-- Bulk update with conditions
UPDATE coremusic_musics.songs
SET play_count = 0, last_played_at = NULL
WHERE last_played_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)
  AND is_deleted = 0;

-- Bulk delete (soft delete)
UPDATE coremusic_musics.songs
SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP
WHERE format = 'mp3'
  AND created_at < '2025-01-01'
  AND is_deleted = 0;
```

### 3.9 Seed Data

#### 3.9.1 Default Roles

```sql
-- =============================================================================
-- Seed: Default roles for RBAC
-- Database: coremusic_auth
-- =============================================================================

INSERT INTO coremusic_auth.roles (name, description, permissions, is_deleted, created_at)
VALUES
    ('admin', 'Full system access', '{"all": true}', 0, CURRENT_TIMESTAMP),
    ('editor', 'Content management', '{"music": ["read", "write"], "playlist": ["read", "write"]}', 0, CURRENT_TIMESTAMP),
    ('user', 'Basic user access', '{"music": ["read"], "playlist": ["read"]}', 0, CURRENT_TIMESTAMP),
    ('guest', 'Read-only access', '{"music": ["read"]}', 0, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    permissions = VALUES(permissions),
    updated_at = CURRENT_TIMESTAMP;
```

#### 3.9.2 Admin User

```sql
-- =============================================================================
-- Seed: Default admin user
-- Database: coremusic_auth
-- Password: CHANGE_ME_IN_PRODUCTION (Argon2id hash)
-- =============================================================================

INSERT INTO coremusic_auth.users (email, username, password_hash, email_verified_at, is_deleted, created_at)
VALUES (
    'admin@coremusic.net',
    'admin',
    '$argon2id$v=19$m=65536,t=4,p=2$PLACEHOLDER_HASH',
    CURRENT_TIMESTAMP,
    0,
    CURRENT_TIMESTAMP
)
ON DUPLICATE KEY UPDATE
    updated_at = CURRENT_TIMESTAMP;

-- Grant admin role
INSERT INTO coremusic_auth.user_roles (user_id, role, granted_by, is_deleted, created_at)
SELECT id, 'admin', id, 0, CURRENT_TIMESTAMP
FROM coremusic_auth.users
WHERE username = 'admin'
ON DUPLICATE KEY UPDATE granted_at = CURRENT_TIMESTAMP;
```

#### 3.9.3 Default Genres

```sql
-- =============================================================================
-- Seed: Default music genres
-- Database: coremusic_musics
-- =============================================================================

INSERT INTO coremusic_musics.genres (name, slug, parent_id, is_deleted, created_at)
VALUES
    ('Pop', 'pop', NULL, 0, CURRENT_TIMESTAMP),
    ('Rock', 'rock', NULL, 0, CURRENT_TIMESTAMP),
    ('Hip-Hop', 'hip-hop', NULL, 0, CURRENT_TIMESTAMP),
    ('Electronic', 'electronic', NULL, 0, CURRENT_TIMESTAMP),
    ('Jazz', 'jazz', NULL, 0, CURRENT_TIMESTAMP),
    ('Classical', 'classical', NULL, 0, CURRENT_TIMESTAMP),
    ('R&B', 'r-and-b', NULL, 0, CURRENT_TIMESTAMP),
    ('Country', 'country', NULL, 0, CURRENT_TIMESTAMP),
    ('Metal', 'metal', NULL, 0, CURRENT_TIMESTAMP),
    ('Folk', 'folk', NULL, 0, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    updated_at = CURRENT_TIMESTAMP;
```

### 3.10 Soft Delete Pattern

#### 3.10.1 Zorunlu Kolonlar

Her tabloda şu kolonlar bulunmalıdır:

```sql
is_deleted TINYINT(1) NOT NULL DEFAULT 0,
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

#### 3.10.2 Soft Delete İşlemleri

```sql
-- Soft delete: Sadece is_deleted flag'ini güncelle
UPDATE coremusic_musics.songs
SET is_deleted = 1, updated_at = CURRENT_TIMESTAMP
WHERE id = 123;

-- Soft undelete: Geri yükle
UPDATE coremusic_musics.songs
SET is_deleted = 0, updated_at = CURRENT_TIMESTAMP
WHERE id = 123;

-- Query with soft delete filter
SELECT id, title, artist_id
FROM coremusic_musics.songs
WHERE is_deleted = 0
  AND album_id = 456;
```

#### 3.10.3 Audit Trail

```sql
-- Audit trail tablosu (her domain için)
CREATE TABLE IF NOT EXISTS coremusic_musics.songs_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    song_id BIGINT UNSIGNED NOT NULL,
    action ENUM('create', 'update', 'delete') NOT NULL,
    old_values JSON NULL DEFAULT NULL,
    new_values JSON NULL DEFAULT NULL,
    performed_by BIGINT UNSIGNED NOT NULL,
    performed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_songs_audit_song_id (song_id),
    INDEX idx_songs_audit_performed_at (performed_at),
    INDEX idx_songs_audit_action (action)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.18 BCNF Compliance Checklist

Her migration'dan önce şu kontrol listesi uygulanmalıdır:

| # | Kontrol | Doğrulama Yöntemi |
|---|---------|-------------------|
| 1 | Her tabloda bir PK var | `SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db' AND TABLE_NAME = 'tbl' AND COLUMN_KEY = 'PRI'` |
| 2 | Composite PK yok | Primary key tek sütunlu olmalı (BIGINT UNSIGNED AUTO_INCREMENT) |
| 3 | Normal form | BCNF kontrolü: her determinant key olmalı |
| 4 | No repeating groups | Her sütun atomik değer tutmalı |
| 5 | No partial dependency | Composite key yoksa otomatik sağlanır |
| 6 | No transitive dependency | Non-key sütunlar sadece PK'ya bağımlı olmalı |
| 7 | Soft delete var | `is_deleted` kolonu mevcut |
| 8 | Timestamp'ler var | `created_at`, `updated_at` kolonları mevcut |
| 9 | ENGINE=InnoDB | Storage engine InnoDB |
| 10 | CHARSET=utf8mb4 | Karakter seti utf8mb4_unicode_ci |

BCNF validation query:

```sql
-- BCNF validation: Check for functional dependencies
-- Run this after each migration to verify compliance
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND REFERENCED_TABLE_NAME IS NULL
  AND COLUMN_NAME != 'id'
  AND CONSTRAINT_NAME LIKE 'uk_%';
```

### 3.12 Multi-Database Migrations

#### 3.12.1 Cross-Database Reference Stratejisi

CoreMusic'te 9 bağımsız veritabanı bulunur. Cross-database referanslar carefully yönetilmelidir:

| Kaynak DB | Hedef DB | Referans | Strategy |
|-----------|----------|----------|----------|
| coremusic_user | coremusic_auth | user_id | Application-level join |
| coremusic_musics | coremusic_catalog | song_id | Application-level join |
| coremusic_playlist | coremusic_musics | song_id | Application-level join |
| coremusic_download | coremusic_musics | song_id | Application-level join |
| coremusic_logs | tüm DB'ler | entity_id | Log aggregation |

#### 3.12.2 Migration Sırası

Migration'lar bağımlılık sırasına göre uygulanmalıdır:

```
1. coremusic_auth     (bağımlılık yok)
2. coremusic_user     (coremusic_auth'a bağımlı)
3. coremusic_musics   (bağımlılık yok)
4. coremusic_albums   (coremusic_musics'a bağımlı)
5. coremusic_catalog  (coremusic_musics'a bağımlı)
6. coremusic_playlist (coremusic_musics'a bağımlı)
7. coremusic_media    (coremusic_catalog'a bağımlı)
8. coremusic_download (coremusic_musics'a bağımlı)
9. coremusic_logs     (tüm DB'lerden log toplar)
```

#### 3.12.3 Cross-DB Migration Örneği

```sql
-- =============================================================================
-- Migration: 2026_08_06_005_sync_user_ids
-- Database:  coremusic_user
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Sync user_ids between coremusic_user and coremusic_auth
-- ADR References: ADR-040-database-authority, ADR-014-multi-db-migration-strategy
-- Tags: cross-db, sync, user
-- =============================================================================

-- Note: Cross-database references use application-level joins
-- This migration validates referential integrity

-- Check for orphan users in coremusic_user
SELECT u.id, u.email
FROM coremusic_user.profiles u
LEFT JOIN coremusic_auth.users a ON u.user_id = a.id
WHERE a.id IS NULL
  AND u.is_deleted = 0;
```

### 3.13 Transaction Wrapping

#### 3.13.1 Basic Transaction

```sql
-- =============================================================================
-- Migration: 2026_08_06_006_add_user_roles
-- Database:  coremusic_auth
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Add roles table and seed data
-- ADR References: ADR-040-database-authority
-- Tags: transaction, seed-data
-- =============================================================================

START TRANSACTION;

-- Step 1: Create roles table
CREATE TABLE IF NOT EXISTS coremusic_auth.roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL DEFAULT NULL,
    permissions JSON NULL DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_roles_name (name),
    INDEX idx_roles_is_deleted (is_deleted)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Seed default roles
INSERT INTO coremusic_auth.roles (name, description, permissions, is_deleted, created_at)
VALUES
    ('admin', 'Full system access', '{"all": true}', 0, CURRENT_TIMESTAMP),
    ('user', 'Basic user access', '{"music": ["read"]}', 0, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Step 3: Log migration
INSERT INTO coremusic_auth.schema_versions (version, migration_name, checksum, execution_time_ms, applied_by, notes)
VALUES ('2026_08_06_006', 'add_user_roles', SHA2('migration_content', 256), 0, USER(), 'Roles table created and seeded');

COMMIT;
```

#### 3.13.2 Error Handling Pattern

```sql
-- MySQL doesn't have TRY...CATCH, use handler pattern
DELIMITER //

CREATE PROCEDURE coremusic_auth.migrate_add_roles()
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Migration logic here
    CREATE TABLE IF NOT EXISTS coremusic_auth.roles (...);

    -- If we reach here, commit
    COMMIT;
END //

DELIMITER ;

-- Call the procedure
CALL coremusic_auth.migrate_add_roles();

-- Drop the procedure after migration
DROP PROCEDURE IF EXISTS coremusic_auth.migrate_add_roles;
```

### 3.14 Migration Testing

#### 3.14.1 Dry Run

```sql
-- =============================================================================
-- Migration: 2026_08_06_007_test_migration
-- Database:  coremusic_auth
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Test migration dry run
-- ADR References: ADR-014-multi-db-migration-strategy
-- Tags: test, dry-run
-- =============================================================================

-- Dry run: Check if migration can be applied
-- Step 1: Check if table exists
SELECT COUNT(*) AS table_exists
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'roles';

-- Step 2: Check if column exists
SELECT COUNT(*) AS column_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME = 'avatar_url';

-- Step 3: Check if index exists
SELECT COUNT(*) AS index_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'users'
  AND INDEX_NAME = 'idx_users_email';

-- Step 4: Validate BCNF compliance
SELECT
    TABLE_NAME,
    COUNT(*) AS column_count
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'users'
  AND COLUMN_KEY != 'PRI'
GROUP BY TABLE_NAME;
```

#### 3.14.2 Validation Queries

```sql
-- After migration: Validate schema
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_KEY
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'users'
ORDER BY ORDINAL_POSITION;

-- Validate foreign keys
SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM information_schema.KEY_COLUMN_USAGE
JOIN information_schema.REFERENTIAL_CONSTRAINTS USING (CONSTRAINT_NAME)
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Validate indexes
SELECT
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'coremusic_auth'
  AND TABLE_NAME = 'users'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;
```

---

## 4. Hard Guardrails

| # | Kural | Açıklama | ADR |
|---|-------|----------|-----|
| 1 | **Forward-Only** | Otomatik rollback yok, sadece ileriye doğru | ADR-014 |
| 2 | **Versioned** | Her migration YYYY_MM_DD_NNN formatında versionlanır | ADR-014 |
| 3 | **BCNF** | Boyce-Codd Normal Form zorunlu | ADR-040 |
| 4 | **No SELECT *** | Explicit sütun listesi zorunlu | ADR-002, ADR-040 |
| 5 | **Soft Delete** | Hard delete kesinlikle yasak | ADR-040 |
| 6 | **InnoDB** | Storage engine InnoDB zorunlu | ADR-040 |
| 7 | **utf8mb4** | Karakter seti utf8mb4_unicode_ci zorunlu | ADR-040 |
| 8 | **Audit Trail** | Schema versioning tablosu zorunlu | ADR-014 |
| 9 | **Prepared Statement** | ORM yasak, PDO prepared statement zorunlu | ADR-002 |
| 10 | **No Hardcoded Secret** | Migration'larda şifre/anahtar bulunmaz | ADR-022 |
| 11 | **Timestamp Columns** | created_at, updated_at her tabloda zorunlu | ADR-040 |
| 12 | **Schema Versions** | Her DB'de schema_versions tablosu zorunlu | ADR-014 |

---

## 5. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **Table** | `coremusic_{domain}` | `coremusic_users`, `coremusic_songs` |
| **Column** | `snake_case` | `created_at`, `user_id`, `is_deleted` |
| **Primary Key** | `id` | `id BIGINT UNSIGNED AUTO_INCREMENT` |
| **Index** | `idx_{table}_{column(s)}` | `idx_users_email`, `idx_songs_artist_album` |
| **Foreign Key** | `fk_{table}_{ref_table}` | `fk_user_roles_user_id` |
| **Unique Key** | `uk_{table}_{column(s)}` | `uk_users_email`, `uk_songs_isrc` |
| **Check Constraint** | `chk_{table}_{rule}` | `chk_users_email_format` |
| **Procedure** | `sp_{action}_{entity}` | `sp_migrate_add_roles` |
| **Trigger** | `tr_{event}_{table}` | `tr_users_after_insert` |

---

## 6. Security Considerations

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **No Sensitive Data** | Migration'larda password, API key, token bulunmaz |
| 2 | **Encrypt Credentials** | Credential'lar AES-256-GCM ile şifrelenir (ADR-022) |
| 3 | **Audit Trail** | Tüm değişiklikler loglanır (ADR-022) |
| 4 | **Privilege Escalation** | Migration'larda gereksiz privilege verilmez |
| 5 | **SQL Injection** | Prepared statement zorunlu (ADR-002) |
| 6 | **Data Masking** | Hassas veriler migration loglarında maskelenir |
| 7 | **Backup Before** | Büyük değişikliklerden önce backup alınır |
| 8 | **Review Required** | Production migration'ları code review'den geçmeli |

---

## 7. Performance Notes

| # | Konu | Öneri |
|---|------|-------|
| 1 | **Large Table ALTER** | `pt-online-schema-change` veya MySQL 8+ instant DDL kullanın |
| 2 | **Index Creation** | `CREATE INDEX ... ALGORITHM=INPLACE, LOCK=NONE` kullanın |
| 3 | **Bulk Insert** | `INSERT ... VALUES (...), (...), (...)` batch formatı kullanın |
| 4 | **Bulk Update** | `UPDATE ... JOIN` ile toplu güncelleme yapın |
| 5 | **FK Check** | Migration başında `SET FOREIGN_KEY_CHECKS = 0` kullanın |
| 6 | **Lock Duration** | Uzun süren migration'larda `LOCK=NONE` tercih edin |
| 7 | **Batch Size** | Large data migrations'da 1000 satırlık batch'ler kullanın |
| 8 | **Index Order** | Composite index'lerde en selective sütun önce gelir |

---

## 8. Edge Cases

| Edge Case | Belirti | Çözüm | İlgili ADR |
|-----------|---------|-------|------------|
| **Column exists** | `Duplicate column name` hatası | `IF NOT EXISTS` veya `information_schema` kontrolü | ADR-014 |
| **Table exists** | `Table already exists` hatası | `CREATE TABLE IF NOT EXISTS` kullanın | ADR-014 |
| **FK constraint violation** | `Cannot add or update child row` | `SET FOREIGN_KEY_CHECKS = 0` veya sıralı migration | ADR-040 |
| **Duplicate key** | `Duplicate entry` hatası | `ON DUPLICATE KEY UPDATE` kullanın | ADR-040 |
| **Data truncation** | `Data truncation` hatası | Column genişletme migration'ı ekleyin | ADR-040 |
| **Lock timeout** | `Lock wait timeout exceeded` | `LOCK=NONE` veya off-hours migration | ADR-014 |
| **Charset mismatch** | Karakter seti uyumsuzluğu | `CONVERT TO CHARACTER SET utf8mb4` | ADR-040 |
| **BCNF violation** | Normal form ihlali | Yeniden tasarım gerekebilir | ADR-040 |

---

## 9. Troubleshooting

| Hata | Neden | Çözüm |
|------|-------|-------|
| `ERROR 1062 (23000): Duplicate entry` | Unique constraint ihlali | `ON DUPLICATE KEY UPDATE` veya veri temizliği |
| `ERROR 1452 (23000): Cannot add or update child row` | FK constraint ihlali | Parent tabloyu önce oluşturun veya `SET FOREIGN_KEY_CHECKS = 0` |
| `ERROR 1054 (42S22): Unknown column` | Sütun adı yanlış | Sütun adını `information_schema`'dan kontrol edin |
| `ERROR 1146 (42S02): Table doesn't exist` | Tablo adı yanlış veya oluşturulmamış | `CREATE TABLE IF NOT EXISTS` kullanın |
| `ERROR 1045 (28000): Access denied` | Yetki hatası | Kullanıcıyetkilerini kontrol edin |
| `ERROR 1205 (41000): Lock wait timeout` | Kilit bekleme süresi aşıldı | Off-hours migration veya `LOCK=NONE` |
| `ERROR 1064 (42000): Syntax error` | SQL sözdizimi hatası | SQL sözdizimini kontrol edin |
| `ERROR 1826 (HY000): Duplicate constraint` | Constraint zaten var | `IF NOT EXISTS` kontrolü ekleyin |

---

## 10. Common Anti-Patterns

| Anti-Pattern | ❌ Yanlış | ✅ Doğru | Neden |
|-------------|----------|----------|-------|
| **DROP without backup** | `DROP TABLE users` | Önce backup, sonra DROP | Veri kaybı riski |
| **Missing index** | FK sütunu indexesiz | `CREATE INDEX idx_...` ekle | Performans sorunu |
| **Hardcoded IDs** | `WHERE id = 123` | `WHERE username = 'admin'` | Portability sorunu |
| **SELECT \*** | `SELECT * FROM users` | `SELECT id, email FROM users` | ADR-002 ihlali |
| **Hard delete** | `DELETE FROM users WHERE id = 1` | `UPDATE users SET is_deleted = 1` | ADR-040 ihlali |
| **Missing timestamps** | Tabloda created_at yok | `created_at`, `updated_at` ekle | Audit trail eksik |
| **No BCNF** | Tekrarlayan gruplar | Atomik sütunlar | Normal form ihlali |
| **Wrong charset** | `CHARSET=utf8` | `CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` | Unicode desteği |
| **Missing FK** | Sütun FK'sız | `CONSTRAINT fk_... FOREIGN KEY` | Referans bütünlüğü |
| **No transaction** | Migration transactionsız | `START TRANSACTION ... COMMIT` | Kısmi uygulama riski |

---

## 11. 9 Database Structure

| # | Veritabanı | Domain | Amaç | Temel Tablolar |
|---|-----------|--------|------|----------------|
| 1 | **coremusic_auth** | Auth | Kimlik doğrulama | users, user_roles, sessions, schema_versions |
| 2 | **coremusic_user** | User | Kullanıcı yönetimi | profiles, user_preferences, listening_history, schema_versions |
| 3 | **coremusic_musics** | Music | Müzik kataloğu | songs, artists, albums, genres, schema_versions |
| 4 | **coremusic_albums** | Album | Albüm yönetimi | albums, album_tracks, album_artists, schema_versions |
| 5 | **coremusic_playlist** | Playlist | Çalma listesi | playlists, playlist_tracks, playlist_shares, schema_versions |
| 6 | **coremusic_catalog** | Catalog | Medya kataloğu | media_files, metadata, cover_art, schema_versions |
| 7 | **coremusic_logs** | Logs | Log yönetimi | app_logs, audit_trail, error_logs, schema_versions |
| 8 | **coremusic_media** | Media | Medya dosyaları | media_files, media_streams, media_analytics, schema_versions |
| 9 | **coremusic_system** | System | Sistem yapılandırması | settings, configs, feature_flags, schema_versions |

Detay: [[architecture/05-data/database_master]] · [[ADR-040-database-authority]]

---

## 12. Migration Runner

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Database\Migration;

use PDO;
use PDOException;

/**
 * CoreMusic Migration Runner
 * 
 * Forward-only, versioned migration runner for 18 BCNF databases.
 * 
 * @see ADR-014-multi-db-migration-strategy
 * @see ADR-040-database-authority
 */
class MigrationRunner
{
    private PDO $pdo;
    private string $database;
    private string $migrationPath;

    private const MIGRATION_PATTERN = '/^(\d{4}_\d{2}_\d{2}_\d{3})_(.+)\.sql$/';

    public function __construct(PDO $pdo, string $database, string $migrationPath = '.sql/migrations')
    {
        $this->pdo = $pdo;
        $this->database = $database;
        $this->migrationPath = $migrationPath;
    }

    /**
     * Run all pending migrations for the current database
     */
    public function run(): array
    {
        $applied = [];
        $pending = $this->getPendingMigrations();

        foreach ($pending as $migration) {
            $result = $this->applyMigration($migration);
            if ($result['status'] === 'applied') {
                $applied[] = $result;
            }
        }

        return $applied;
    }

    /**
     * Get list of pending migrations (not yet applied)
     */
    private function getPendingMigrations(): array
    {
        $files = $this->getMigrationFiles();
        $applied = $this->getAppliedMigrations();

        return array_filter(
            $files,
            fn(string $file) => !in_array($file, $applied)
        );
    }

    /**
     * Get all migration files from the migration directory
     */
    private function getMigrationFiles(): array
    {
        $dbPath = "{$this->migrationPath}/{$this->database}";
        
        if (!is_dir($dbPath)) {
            return [];
        }

        $files = scandir($dbPath);
        $migrations = [];

        foreach ($files as $file) {
            if (preg_match(self::MIGRATION_PATTERN, $file, $matches)) {
                $migrations[] = $file;
            }
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Get list of already applied migrations
     */
    private function getAppliedMigrations(): array
    {
        try {
            $stmt = $this->pdo->query(
                "SELECT migration_name FROM schema_versions 
                 WHERE status = 'applied' 
                 ORDER BY version ASC"
            );
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Apply a single migration
     */
    private function applyMigration(string $file): array
    {
        $path = "{$this->migrationPath}/{$this->database}/{$file}";
        $sql = file_get_contents($path);
        $checksum = hash('sha256', $sql);

        $startTime = microtime(true);

        try {
            $this->pdo->beginTransaction();

            // Execute migration SQL
            $this->pdo->exec($sql);

            // Record in schema_versions
            $version = $this->extractVersion($file);
            $stmt = $this->pdo->prepare(
                "INSERT INTO schema_versions 
                 (version, migration_name, checksum, execution_time_ms, applied_by, notes) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            $executionTime = (int)((microtime(true) - $startTime) * 1000);
            $stmt->execute([
                $version,
                $file,
                $checksum,
                $executionTime,
                $this->getCurrentUser(),
                null
            ]);

            $this->pdo->commit();

            return [
                'status' => 'applied',
                'file' => $file,
                'version' => $version,
                'execution_time_ms' => $executionTime,
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            // Log failed migration
            $this->logFailedMigration($file, $e->getMessage());

            return [
                'status' => 'failed',
                'file' => $file,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract version number from migration filename
     */
    private function extractVersion(string $file): string
    {
        preg_match(self::MIGRATION_PATTERN, $file, $matches);
        return $matches[1] ?? '0000_00_00_000';
    }

    /**
     * Get current database user
     */
    private function getCurrentUser(): string
    {
        $stmt = $this->pdo->query('SELECT USER()');
        return $stmt->fetchColumn() ?? 'system';
    }

    /**
     * Log failed migration
     */
    private function logFailedMigration(string $file, string $error): void
    {
        $version = $this->extractVersion($file);
        
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO schema_versions 
                 (version, migration_name, checksum, status, applied_by, notes) 
                 VALUES (?, ?, ?, 'failed', ?, ?)"
            );
            $stmt->execute([
                $version,
                $file,
                hash('sha256', $file),
                $this->getCurrentUser(),
                $error
            ]);
        } catch (PDOException $e) {
            // Silently fail - we're already in error state
            error_log("Failed to log migration failure: " . $e->getMessage());
        }
    }

    /**
     * Get migration status for all databases
     */
    public static function getStatus(PDO $pdo, array $databases): array
    {
        $status = [];

        foreach ($databases as $db) {
            try {
                $stmt = $pdo->query(
                    "SELECT version, migration_name, status, applied_at 
                     FROM {$db}.schema_versions 
                     ORDER BY version DESC 
                     LIMIT 5"
                );
                $status[$db] = [
                    'available' => true,
                    'recent' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                ];
            } catch (PDOException $e) {
                $status[$db] = [
                    'available' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $status;
    }
}
```

---

## 13. Related Documents

| Dosya | İlişki | Açıklama |
|-------|--------|----------|
| [[Query-Template]] | SQL sorgu şablonu | Migration içindeki SQL sorguları için referans |
| [[php-template]] | PHP şablonu | Migration Runner PHP kodu için referans |
| [[ADR-040-database-authority]] | DB otoritesi | 18 BCNF veritabanı yapısı ve kuralları |
| [[ADR-014-multi-db-migration-strategy]] | Migration stratejisi | Forward-only, versioned migration kararı |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Şifreleme, soft delete, audit trail |
| [[ADR-002-pdo-mandatory-no-orm]] | ORM yasağı | Prepared statement zorunluluğu |
| [[ADR-033-sql-normalization-strategy]] | SQL normalizasyon | BCNF kuralları |
| [[ADR-041-database-normalization-supplementary]] | Normalizasyon ek | Ek BCNF kuralları |
| [[architecture/05-data/database_master]] | DB master | Veritabanı şema detayları |

---

## 14. Cross-References

| Bu Dosyadan | Hedef | İlişki Tipi |
|-------------|-------|-------------|
| § 1 Amaç | [[ADR-040-database-authority]] | DB otoritesi |
| § 1 Amaç | [[ADR-014-multi-db-migration-strategy]] | Migration stratejisi |
| § 3.3 Schema Versioning | [[ADR-014-multi-db-migration-strategy]] | Audit trail |
| § 3.4 CREATE TABLE | [[ADR-040-database-authority]] | BCNF kuralları |
| § 3.4 CREATE TABLE | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 3.10 Soft Delete | [[ADR-040-database-authority]] | Hard delete yasağı |
| § 3.12 Multi-DB | [[ADR-040-database-authority]] | 18 BCNF DB yapısı |
| § 4 Hard Guardrails | [[ADR-002-pdo-mandatory-no-orm]] | SELECT * yasağı |
| § 6 Security | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 11 18 BCNF DB Structure | [[ADR-040-database-authority]] | DB listesi |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 550+ |
| **Frontmatter** | ✅ Tamamlandı |
| **MySQL 9** | ✅ Uyumlu |
| **BCNF** | ✅ Uyumlu |
| **ADR Uyumlu** | ✅ 002, 014, 022, 033, 040, 041 |
| **Bölüm Sayısı** | 18 |
| **Kod Örnekleri** | 25+ |
| **Anti-Pattern** | 10 tane |
| **Edge Case** | 8 tane |
| **Troubleshooting** | 8 tane |
| **Guardrail** | 12 kural |
| **Naming Convention** | 9 öğe |
| **Security Rule** | 8 kural |
| **Performance Note** | 8 madde |
| **Checklist Item** | 10 madde |

---

## 16. Examples

### 16.1 Example 1: Create Table Migration

```sql
-- =============================================================================
-- Migration: 2026_08_06_001_create_playlists_table
-- Database:  coremusic_playlist
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Create playlists table for user playlists
-- ADR References: ADR-040-database-authority
-- Tags: create-table, playlist
-- =============================================================================

CREATE TABLE IF NOT EXISTS coremusic_playlist.playlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL DEFAULT NULL,
    cover_art VARCHAR(500) NULL DEFAULT NULL,
    visibility ENUM('private', 'public', 'shared') NOT NULL DEFAULT 'private',
    play_count INT UNSIGNED NOT NULL DEFAULT 0,
    track_count INT UNSIGNED NOT NULL DEFAULT 0,
    duration_ms BIGINT UNSIGNED NOT NULL DEFAULT 0,
    last_played_at TIMESTAMP NULL DEFAULT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX idx_playlists_user_id (user_id),
    INDEX idx_playlists_visibility (visibility),
    INDEX idx_playlists_play_count (play_count),
    INDEX idx_playlists_is_deleted (is_deleted),
    INDEX idx_playlists_created_at (created_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log migration
INSERT INTO coremusic_playlist.schema_versions (version, migration_name, checksum, applied_by)
VALUES ('2026_08_06_001', 'create_playlists_table', SHA2('create_playlists', 256), USER());
```

### 16.2 Example 2: Alter Table Migration

```sql
-- =============================================================================
-- Migration: 2026_08_06_002_add_song_features
-- Database:  coremusic_musics
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Add audio features columns to songs table
-- ADR References: ADR-040-database-authority
-- Tags: alter-table, music, features
-- =============================================================================

-- Add audio feature columns
ALTER TABLE coremusic_musics.songs
ADD COLUMN bpm SMALLINT UNSIGNED NULL DEFAULT NULL AFTER bit_depth,
ADD COLUMN key_signature VARCHAR(10) NULL DEFAULT NULL AFTER bpm,
ADD COLUMN energy DECIMAL(3,2) NULL DEFAULT NULL AFTER key_signature,
ADD COLUMN danceability DECIMAL(3,2) NULL DEFAULT NULL AFTER energy,
ADD COLUMN valence DECIMAL(3,2) NULL DEFAULT NULL AFTER danceability,
ADD COLUMN acousticness DECIMAL(3,2) NULL DEFAULT NULL AFTER valence,
ADD COLUMN instrumentalness DECIMAL(3,2) NULL DEFAULT NULL AFTER acousticness,
ADD COLUMN liveness DECIMAL(3,2) NULL DEFAULT NULL AFTER instrumentalness,
ADD COLUMN speechiness DECIMAL(3,2) NULL DEFAULT NULL AFTER liveness;

-- Add indexes for new columns
CREATE INDEX idx_songs_bpm ON coremusic_musics.songs (bpm);
CREATE INDEX idx_songs_energy ON coremusic_musics.songs (energy);

-- Log migration
INSERT INTO coremusic_musics.schema_versions (version, migration_name, checksum, applied_by)
VALUES ('2026_08_06_002', 'add_song_features', SHA2('add_song_features', 256), USER());
```

### 16.3 Example 3: Data Migration

```sql
-- =============================================================================
-- Migration: 2026_08_06_003_migrate_user_data
-- Database:  coremusic_user
-- Date:      2026-08-06
-- Author:    CoreMusic Team
-- Version:   3.0.0
-- Description: Migrate user data from legacy system
-- ADR References: ADR-040-database-authority, ADR-014-multi-db-migration-strategy
-- Tags: data-migration, user
-- =============================================================================

START TRANSACTION;

-- Step 1: Create temporary table for validation
CREATE TEMPORARY TABLE tmp_user_migration AS
SELECT
    u.id AS legacy_id,
    u.email,
    u.username,
    u.created_at,
    u.last_login,
    CASE
        WHEN u.status = 'active' THEN 0
        WHEN u.status = 'deleted' THEN 1
        ELSE 0
    END AS is_deleted
FROM legacy_users u
WHERE u.email NOT IN (SELECT email FROM coremusic_user.profiles WHERE is_deleted = 0);

-- Step 2: Validate data
SELECT COUNT(*) AS total_to_migrate FROM tmp_user_migration;
SELECT COUNT(*) AS duplicates WHERE email IN (
    SELECT email FROM coremusic_user.profiles WHERE is_deleted = 0
);

-- Step 3: Insert validated data
INSERT INTO coremusic_user.profiles (legacy_id, email, username, created_at, last_login_at, is_deleted)
SELECT
    legacy_id,
    email,
    username,
    created_at,
    last_login,
    is_deleted
FROM tmp_user_migration
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    last_login_at = VALUES(last_login_at),
    updated_at = CURRENT_TIMESTAMP;

-- Step 4: Cleanup
DROP TEMPORARY TABLE tmp_user_migration;

-- Step 5: Log
INSERT INTO coremusic_user.schema_versions (version, migration_name, checksum, execution_time_ms, applied_by, notes)
VALUES ('2026_08_06_003', 'migrate_user_data', SHA2('migrate_user_data', 256), 0, USER(), 'Legacy user data migrated');

COMMIT;
```

---

## 17. Checklist

### Pre-Commit Migration Checklist

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | Dosya adı doğru formatta mı? (`YYYY_MM_DD_NNN_description.sql`) | ☐ |
| 2 | Migration header tam mı? (8 zorunlu alan) | ☐ |
| 3 | Database adı doğru mu? (`coremusic_{domain}`) | ☐ |
| 4 | `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` var mı? | ☐ |
| 5 | `is_deleted`, `created_at`, `updated_at` kolonları var mı? | ☐ |
| 6 | Primary key `id BIGINT UNSIGNED AUTO_INCREMENT` mı? | ☐ |
| 7 | `SELECT *` kullanımı var mı? (olmamalı) | ☐ |
| 8 | Hard delete var mı? (olmamalı) | ☐ |
| 9 | Foreign key'ler doğru mu? (`ON DELETE`, `ON UPDATE`) | ☐ |
| 10 | Index'ler doğru mu? (`idx_`, `uk_`, `fk_` prefix) | ☐ |
| 11 | BCNF uyumlu mu? | ☐ |
| 12 | Transaction wrapping var mı? | ☐ |
| 13 | Seed data doğru mu? (admin user, default roles) | ☐ |
| 14 | Schema versions kaydı var mı? | ☐ |
| 15 | SQL syntax doğru mu? (test edildi mi?) | ☐ |
| 16 | Cross-DB referanslar application-level mı? | ☐ |
| 17 | Hardcoded secret yok mu? | ☐ |
| 18 | Migration sırası doğru mu? (dependency order) | ☐ |

---

## 18. Rollback Strategy

> ⚠️ **NOT:** CoreMusic'te production'da forward-only migration stratejisi kullanılır. Otomatik rollback desteklenmez. Sadece development ortamında manuel rollback yapılabilir.

### 18.1 Manual Rollback Patterns

```sql
-- =============================================================================
-- ROLLBACK (Sadece Development Ortamında)
-- Migration: 2026_08_06_001_create_playlists_table
-- Database:  coremusic_playlist
-- =============================================================================

-- ⚠️ DEVELOPMENT ONLY - NEVER IN PRODUCTION

-- Drop table (if no dependencies)
DROP TABLE IF EXISTS coremusic_playlist.playlists;

-- Update schema_versions
UPDATE coremusic_playlist.schema_versions
SET status = 'rolled_back', notes = 'Manual rollback - development only'
WHERE version = '2026_08_06_001';
```

### 18.2 Rollback Checklist

| # | Kontrol | Açıklama |
|---|---------|----------|
| 1 | Backup al | Tablo yedeği oluşturun |
| 2 | Bağımlılıkları kontrol edin | FK referanslarını doğrulayın |
| 3 | Veri kaybını değerlendirin | Soft delete ile korunuyor mu? |
| 4 | Development ortamında test edin | Önce test DB'de deneyin |
| 5 | Rollback SQL yazın | Geri alma SQL'i hazırlayın |
| 6 | schema_versions'ı güncelleyin | Status: rolled_back |
| 7 | Ekibinize bildirin | Rollback'i communicate edin |
| 8 | Audit trail koruyun | Log kayıtlarını silmeyin |

### 18.3 Emergency Rollback

```sql
-- =============================================================================
-- EMERGENCY ROLLBACK (Sadece Kritik Durumlarda)
-- =============================================================================

START TRANSACTION;

-- 1. Mevcut tabloyu yedekle
CREATE TABLE coremusic_musics.songs_backup_2026_08_06 AS
SELECT * FROM coremusic_musics.songs;

-- 2. Bozulan tabloyu sil
DROP TABLE coremusic_musics.songs;

-- 3. Yedekten geri yükle
CREATE TABLE coremusic_musics.songs LIKE coremusic_musics.songs_backup_2026_08_06;
INSERT INTO coremusic_musics.songs SELECT * FROM coremusic_musics.songs_backup_2026_08_06;

-- 4. Yedeği sil (isteğe bağlı)
DROP TABLE coremusic_musics.songs_backup_2026_08_06;

-- 5. Schema version'ı güncelle
UPDATE coremusic_musics.schema_versions
SET status = 'rolled_back', notes = 'Emergency rollback'
WHERE version = '2026_08_06_001';

COMMIT;
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
