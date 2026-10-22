-- ===========================================================================
-- 20261021_apply_pending.sql — Bekleyen migration parçalarının uygulanması
-- Tarih: 2026-10-21 (bu oturum)
-- ÖNCEKİ DURUM (information_schema teyitli):
--   · playlists/playlist_tracks/collaborators/followers/sys_notifications/history
--     tabloları MEVCUT ve BOŞ (0 satır) → orphan=0 garantili.
--   · FK mevcut: fk_playlist_tracks_playlist, fk_playlist_collaborators_playlist,
--     fk_playlist_followers_playlist, fk_notifications_user,
--     fk_history_user, fk_history_music
--   · FK EKSİK: fk_playlists_user, fk_playlist_tracks_music,
--     fk_playlist_collaborators_user, fk_playlist_followers_user,
--     fk_history_playlist  → bu dosya ekler
--   · fk_system_notifications_user: MEVCUT fk_notifications_user aynı işi
--     görüyor → redundant ikinci FK EKLENMEZ (surgical change).
--   · user_recent_tracks tablosu YOK → CREATE
--   · v_search_log_singular VIEW YOK → CREATE
--   · backend_restructure index'leri: hiçbiri YOK → ADD
-- Idempotent değil (ALTER ADD CONSTRAINT) — tek seferlik apply, yedek alındı:
--   C:\temp\opencode\backup_20261021b\ (5 DB, mysqldump, 2026-10-21)
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- 1) coremusic_playlist FK'ları (create_playlist_tables.sql BÖLÜM 1 karşılığı)
-- ---------------------------------------------------------------------------
ALTER TABLE `coremusic_playlist`.`playlists`
  ADD CONSTRAINT `fk_playlists_user`
  FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `coremusic_playlist`.`playlist_tracks`
  ADD CONSTRAINT `fk_playlist_tracks_music`
  FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `coremusic_playlist`.`playlist_collaborators`
  ADD CONSTRAINT `fk_playlist_collaborators_user`
  FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `coremusic_playlist`.`playlist_followers`
  ADD CONSTRAINT `fk_playlist_followers_user`
  FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- ---------------------------------------------------------------------------
-- 2) user_listening_history: eksik tek FK (listening migration BÖLÜM 1 H-1.3)
-- ---------------------------------------------------------------------------
ALTER TABLE `coremusic_user`.`user_listening_history`
  ADD CONSTRAINT `fk_history_playlist`
  FOREIGN KEY (`playlist_id`) REFERENCES `coremusic_playlist`.`playlists` (`id`)
  ON DELETE RESTRICT ON UPDATE CASCADE;

-- ---------------------------------------------------------------------------
-- 3) user_recent_tracks — YENİ tablo (listening migration UP karşılığı, birebir)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_user`.`user_recent_tracks` (
  `id`              BINARY(16)   NOT NULL COMMENT 'UUID v7 PK (PHP UuidV7::generate)',
  `user_id`         BINARY(16)   NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id`        BINARY(16)   NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `last_played_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Son calinma zamani (UTC)',
  `play_count`      INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Recent penceresinde oynatma sayisi',
  `is_deleted`      TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP    NULL DEFAULT NULL COMMENT 'soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_recent_tracks_pair` (`user_id`,`music_id`),
  KEY `idx_user_recent_tracks_user_last` (`user_id`,`last_played_at`),
  KEY `idx_user_recent_tracks_music` (`music_id`),
  CONSTRAINT `fk_user_recent_tracks_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_user_recent_tracks_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Son dinlenenler (recent tracks) — user+music upsert, last_played_at siralamasi';

-- ---------------------------------------------------------------------------
-- 4) novasearch: tekil ad okuyan tüketiciler için salt-okunur köprü VIEW
--    (bcnf_normalize_active_schemas.sql BÖLÜM 1 birebir)
-- ---------------------------------------------------------------------------
CREATE OR REPLACE VIEW `novasearch`.`v_search_log_singular` AS
SELECT
  `id`,
  `query`,
  `filters`,
  `results_count`,
  `response_time_ms` AS `took_ms`,
  `source`,
  `created_at`
FROM `novasearch`.`search_logs`;

-- ---------------------------------------------------------------------------
-- 5) backend_restructure.sql indeksleri (1-3 birebir)
-- ---------------------------------------------------------------------------
ALTER TABLE `coremusic_musics`.`musics`
  ADD INDEX `idx_musics_active_created` (`is_deleted`, `created_at` DESC),
  ADD INDEX `idx_musics_artist_created` (`artist_id`, `created_at` DESC);

ALTER TABLE `coremusic_musics`.`music_files`
  ADD INDEX `idx_mf_music_primary` (`music_id`, `is_primary`, `is_deleted`);

ALTER TABLE `coremusic_playlist`.`playlists`
  ADD INDEX `idx_pl_user_updated` (`user_id`, `is_deleted`, `updated_at` DESC);

ALTER TABLE `coremusic_playlist`.`playlist_tracks`
  ADD INDEX `idx_pt_playlist_pos_created` (`playlist_id`, `position`, `created_at`);

ALTER TABLE `coremusic_user`.`user_listening_history`
  ADD INDEX `idx_ulh_user_recent` (`user_id`, `started_at` DESC, `music_id`),
  ADD INDEX `idx_ulh_user_music_recent` (`user_id`, `music_id`, `started_at` DESC);

-- BİTTİ — doğrulama: information_schema KEY_COLUMN_USAGE + STATISTICS
