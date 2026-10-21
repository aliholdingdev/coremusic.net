-- ===========================================================================
-- 20261021_create_playlist_tables.sql
-- Playlist tabloları — canonical tanım (idempotent ensure) + FK'lar (Karar 3)
-- ===========================================================================
-- DURUM  : playlists, playlist_tracks, playlist_stats, playlist_collaborators,
--          playlist_followers ZATEN VAR (coremusic_playlist.sql dump, 2026-10-21).
--          Bu migration mevcut DB'de NO-OP (IF NOT EXISTS guard), fresh-DB'de kurulum.
-- PK     : Mevcut pattern korunur — playlists BINARY(16) UUID-v7 PK (musics.id ile aynı),
--          junction tablolar BIGINT UNSIGNED AI PK (music_videos deseni).
-- UNIQUE : playlist_tracks UNIQUE(playlist_id, music_id, position) KORUNUR (Karar 4 — DOKUNMA).
-- FK'lar : Karar 3 — cross-DB FK bu dosyaya TAŞINDI (bcnf_normalize'dan).
--          playlist -> coremusic_auth.users / coremusic_musics.musics · ON DELETE CASCADE.
-- Charset: utf8mb4 / utf8mb4_unicode_ci (dump teyitli).
-- Idempotent: CREATE TABLE IF NOT EXISTS ✓ · ALTER ADD CONSTRAINT ✗ (MySQL 8.4 native;
--          mevcut DB'de FK yoksa eklenir; tekrar "duplicate key" verir → pre-check Sonra).
-- ===========================================================================

-- ===========================================================================
-- BÖLÜM 0 — PRE-CHECK (salt-okunur; FK eklemeden ÖNCE orphan=0 doğrula)
-- ===========================================================================
-- Beklenen: her sorgu 0. Mevcut DB'de (tablo doluysa) orphan varsa FK eklenmez.
SELECT COUNT(*) AS orphan_playlist_owner
FROM `coremusic_playlist`.`playlists` p
LEFT JOIN `coremusic_auth`.`users` u ON u.`id` = p.`user_id`
WHERE u.`id` IS NULL AND p.`user_id` IS NOT NULL;

SELECT COUNT(*) AS orphan_playlist_track_music
FROM `coremusic_playlist`.`playlist_tracks` pt
LEFT JOIN `coremusic_musics`.`musics` m ON m.`id` = pt.`music_id`
WHERE m.`id` IS NULL AND pt.`music_id` IS NOT NULL;

SELECT COUNT(*) AS orphan_collab_user
FROM `coremusic_playlist`.`playlist_collaborators` pc
LEFT JOIN `coremusic_auth`.`users` u ON u.`id` = pc.`user_id`
WHERE u.`id` IS NULL AND pc.`user_id` IS NOT NULL;

SELECT COUNT(*) AS orphan_follower_user
FROM `coremusic_playlist`.`playlist_followers` pf
LEFT JOIN `coremusic_auth`.`users` u ON u.`id` = pf.`user_id`
WHERE u.`id` IS NULL AND pf.`user_id` IS NOT NULL;

-- ===========================================================================
-- UP
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- playlists — kullanıcı playlist tanımı (BINARY(16) UUID-v7 PK, dump satır 94-103)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_playlist`.`playlists` (
  `id`              BINARY(16)   NOT NULL COMMENT 'UUID v7 PK (musics.id deseni — PHP UuidV7::generate)',
  `user_id`         BINARY(16)   NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `name`            VARCHAR(255) NOT NULL,
  `description`     TEXT         NULL,
  `visibility`      ENUM('public','followers_only','private') NOT NULL DEFAULT 'private',
  `playlist_type`   ENUM('custom','liked','purchased','system','smart') NOT NULL DEFAULT 'custom',
  `cover_url`       VARCHAR(500) NULL,
  `track_count`     INT UNSIGNED NOT NULL DEFAULT 0,
  `total_duration`  BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'saniye',
  `is_pinned`       TINYINT(1)   NOT NULL DEFAULT 0,
  `is_deleted`      TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP    NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  KEY `idx_playlists_user` (`user_id`),
  FULLTEXT KEY `ft_playlists_name_desc` (`name`,`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kullanici playlist tanimlari';

-- ---------------------------------------------------------------------------
-- playlist_tracks — playlist <-> music junction (BIGINT AI PK, satır 70-80)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_playlist`.`playlist_tracks` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `playlist_id`     BINARY(16)  NOT NULL COMMENT 'FK -> playlists.id',
  `music_id`        BINARY(16)  NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `position`        INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'siralama',
  `added_by`        BINARY(16)  NULL COMMENT 'collaborator eklediyse -> coremusic_auth.users.id',
  `added_at`        TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted`      TINYINT(1)  NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP   NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_playlist_tracks_pos` (`playlist_id`,`music_id`,`position`),
  KEY `idx_playlist_tracks_playlist` (`playlist_id`),
  KEY `idx_playlist_tracks_music` (`music_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Playlist icindeki sarkilar (junction)';

-- ---------------------------------------------------------------------------
-- playlist_stats — playlist aggregate (satır 105-115)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_playlist`.`playlist_stats` (
  `playlist_id`     BINARY(16)  NOT NULL COMMENT 'PK/FK -> playlists.id',
  `total_plays`     BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `total_likes`     INT UNSIGNED NOT NULL DEFAULT 0,
  `total_shares`    INT UNSIGNED NOT NULL DEFAULT 0,
  `last_played_at`  TIMESTAMP   NULL DEFAULT NULL,
  `last_liked_at`   TIMESTAMP   NULL DEFAULT NULL,
  `created_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP   NULL DEFAULT NULL COMMENT 'soft delete (inherit)',
  PRIMARY KEY (`playlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Playlist istatistikleri (aggregate)';

-- ---------------------------------------------------------------------------
-- playlist_collaborators — playlist <-> user many-to-many (satır 20-32)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_playlist`.`playlist_collaborators` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `playlist_id`     BINARY(16) NOT NULL COMMENT 'FK -> playlists.id',
  `user_id`         BINARY(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id',
  `role`            ENUM('viewer','contributor','editor') NOT NULL DEFAULT 'viewer',
  `invited_by`      BINARY(16) NULL COMMENT 'FK -> coremusic_auth.users.id',
  `invited_at`      TIMESTAMP  NULL DEFAULT NULL,
  `joined_at`       TIMESTAMP  NULL DEFAULT NULL,
  `is_deleted`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP  NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_playlist_collab_pair` (`playlist_id`,`user_id`),
  KEY `idx_playlist_collab_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Playlist ortak katkici iliskileri';

-- ---------------------------------------------------------------------------
-- playlist_followers — playlist <-> user takip (satır 34-44)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_playlist`.`playlist_followers` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `playlist_id`     BINARY(16) NOT NULL COMMENT 'FK -> playlists.id',
  `user_id`         BINARY(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id',
  `followed_at`     TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP  NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_playlist_follow_pair` (`playlist_id`,`user_id`),
  KEY `idx_playlist_follow_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Playlist takip iliskileri';

-- ===========================================================================
-- BÖLÜM 1 — cross-DB FK'lar (Karar 3: bcnf_normalize'dan TAŞINDI, CASCADE)
-- ===========================================================================
-- pre-check (BÖLÜM 0) 0 dönerse çalıştır. MySQL ADD CONSTRAINT idempotent değil
-- → mevcut DB'de tekrar "duplicate key" verir; fresh DB'de CREATE inline FK içermez,
--   bu ALTER'lar ekler. Index'ler zaten CREATE'de mevcut (idx_*, §6.4).

-- P-1.1 playlist sahibi -> users (CASCADE)
ALTER TABLE `coremusic_playlist`.`playlists`
  ADD CONSTRAINT `fk_playlists_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- P-1.2 playlist_tracks -> musics (CASCADE)
ALTER TABLE `coremusic_playlist`.`playlist_tracks`
  ADD CONSTRAINT `fk_playlist_tracks_music`
    FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- P-1.3 playlist_collaborators -> users (CASCADE)
ALTER TABLE `coremusic_playlist`.`playlist_collaborators`
  ADD CONSTRAINT `fk_playlist_collaborators_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- P-1.4 playlist_followers -> users (CASCADE)
ALTER TABLE `coremusic_playlist`.`playlist_followers`
  ADD CONSTRAINT `fk_playlist_followers_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- ===========================================================================
-- DOWN (ROLLBACK — yalnız referans; ADR-014 forward-only: production DOWN çalıştırmaz)
-- ===========================================================================
-- ALTER TABLE `coremusic_playlist`.`playlist_followers` DROP FOREIGN KEY `fk_playlist_followers_user`;
-- ALTER TABLE `coremusic_playlist`.`playlist_collaborators` DROP FOREIGN KEY `fk_playlist_collaborators_user`;
-- ALTER TABLE `coremusic_playlist`.`playlist_tracks` DROP FOREIGN KEY `fk_playlist_tracks_music`;
-- ALTER TABLE `coremusic_playlist`.`playlists` DROP FOREIGN KEY `fk_playlists_user`;
-- DROP TABLE IF EXISTS `coremusic_playlist`.`playlist_followers`;
-- DROP TABLE IF EXISTS `coremusic_playlist`.`playlist_collaborators`;
-- DROP TABLE IF EXISTS `coremusic_playlist`.`playlist_stats`;
-- DROP TABLE IF EXISTS `coremusic_playlist`.`playlist_tracks`;
-- DROP TABLE IF EXISTS `coremusic_playlist`.`playlists`;
