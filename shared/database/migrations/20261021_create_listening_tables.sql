-- ===========================================================================
-- 20261021_create_listening_tables.sql
-- Dinleme tabloları — user_listening_history (ensure) + user_recent_tracks (YENİ)
-- ===========================================================================
-- DURUM  : user_listening_history ZATEN VAR (coremusic_user.sql dump satır 81-93).
--          Görevdeki "coremusic_listening_history" = bu tablonun karşılığı →
--          ikinci bir DUPLICATE tablo üretilmedi (zero-hallucination).
-- YENİ   : user_recent_tracks — diskte YOK doğrulandı. playback_queue FARKLI kavramdır.
-- PK     : Karar 1 — HER İKİ tablo da BINARY(16) UUID-v7 PK (musics.id deseni,
--          NO DEFAULT → PHP UuidV7::generate() üretir; shared/src/Security/UuidV7.php
--          doğrulandı). `UUID_TO_BIN(...,1)` DEĞİL — mevcut pattern PHP üretimi.
--          BIGINT UNSIGNED AI KALDIRILDI.
-- FK     : Karar 3 — cross-DB FK bu dosyaya TAŞINDI. history/recent_tracks FK'ları
--          ON DELETE RESTRICT (veri kaybını önle — audit/trail korunur).
-- Charset: utf8mb4 / utf8mb4_unicode_ci (coremusic_user dump teyitli, satır 113).
-- Idempotent: CREATE TABLE IF NOT EXISTS ✓ (FK'lar inline, yeni tablo boş → güvenli).
-- ⚠️ RİSK  : user_listening_history MEVCUT prod tablosu BIGINT AI PK ile durur.
--          IF NOT EXISTS = no-op → prod PK DEĞİŞMEZ. PHP BINARY(16) insert'e geçerse
--          mevcut BIGINT tablo reddeder → Ayrı expand-contract PK-migrasyon migration'ı
--          GEREKLİ (uygulama-seviyesi backfill; bu saf-SQL dosyasında YOK — rapor §6).
-- ===========================================================================

-- ===========================================================================
-- UP
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- user_listening_history — TÜM dinleme olayları (append-only, mevcut — ensure)
-- PK: BINARY(16) UUID-v7 (Karar 1). Fresh-DB'de bu tanım; mevcut prod'da BIGINT kalır.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coremusic_user`.`user_listening_history` (
  `id`                  BINARY(16)  NOT NULL COMMENT 'UUID v7 PK (PHP UuidV7::generate)',
  `user_id`             BINARY(16)  NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id`            BINARY(16)  NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `artist_id`           BINARY(16)  NULL COMMENT 'FK -> coremusic_musics.artists.id (UUID v7)',
  `playlist_id`         BINARY(16)  NULL COMMENT 'FK -> coremusic_playlist.playlists.id (opsiyonel)',
  `context_type`        ENUM('search','playlist','album','radio','direct','recommendation','recent') NOT NULL DEFAULT 'direct',
  `listen_duration_sec` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'dinlenen sure (saniye)',
  `listen_percentage`   DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '0-100, FLOAT yerine DECIMAL (§10)',
  `started_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at`            TIMESTAMP   NULL DEFAULT NULL,
  `device_type`         VARCHAR(50) NULL COMMENT 'desktop|mobile|tablet|web (snapshot, ENUM degil domain verisi)',
  `ip_address`          VARCHAR(45) NULL COMMENT 'IPv4/IPv6 (privacy-aware)',
  `is_deleted`          TINYINT(1)  NOT NULL DEFAULT 0,
  `created_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`          TIMESTAMP   NULL DEFAULT NULL COMMENT 'soft delete (audit trail korunur)',
  PRIMARY KEY (`id`),
  KEY `idx_user_lh_user_started` (`user_id`,`started_at`),
  KEY `idx_user_lh_music` (`music_id`),
  KEY `idx_user_lh_artist` (`artist_id`),
  KEY `idx_user_lh_playlist` (`playlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Kullanici dinleme gecmisi (append-only olay logu)';

-- ---------------------------------------------------------------------------
-- user_recent_tracks — SON DINLENENLER (YENİ — görevde gerçekten eksik tablo)
-- ---------------------------------------------------------------------------
-- Amaç: "Recent tracks" sorgusu için hızlı okuma (user + last_played_at DESC).
-- Model: user+music başına TEK satır (upsert). Son oynatmada last_played_at güncellenir.
-- PK: BINARY(16) UUID-v7 (Karar 1, NO DEFAULT → PHP üretir).
-- Polymorphic YOK (§10). Soft delete: deleted_at.
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

-- ===========================================================================
-- BÖLÜM 1 — cross-DB FK: user_listening_history (Karar 3: TAŞINDI, RESTRICT)
-- ===========================================================================
-- user_recent_tracks FK'ları inline yukarıda (yeni tablo, boş → orphan yok).
-- user_listening_history MEVCUT tablo → FK'lar ALTER ile. pre-check: orphan=0 doğrula.
-- Index'ler zaten CREATE'de (idx_user_lh_*, §6.4).

SELECT COUNT(*) AS orphan_history_user
FROM `coremusic_user`.`user_listening_history` h
LEFT JOIN `coremusic_auth`.`users` u ON u.`id` = h.`user_id`
WHERE u.`id` IS NULL AND h.`user_id` IS NOT NULL;

SELECT COUNT(*) AS orphan_history_music
FROM `coremusic_user`.`user_listening_history` h
LEFT JOIN `coremusic_musics`.`musics` m ON m.`id` = h.`music_id`
WHERE m.`id` IS NULL AND h.`music_id` IS NOT NULL;

SELECT COUNT(*) AS orphan_history_playlist
FROM `coremusic_user`.`user_listening_history` h
LEFT JOIN `coremusic_playlist`.`playlists` p ON p.`id` = h.`playlist_id`
WHERE p.`id` IS NULL AND h.`playlist_id` IS NOT NULL;

-- H-1.1 user_listening_history -> users (RESTRICT — audit koru)
ALTER TABLE `coremusic_user`.`user_listening_history`
  ADD CONSTRAINT `fk_history_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- H-1.2 user_listening_history -> musics (RESTRICT)
ALTER TABLE `coremusic_user`.`user_listening_history`
  ADD CONSTRAINT `fk_history_music`
    FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- H-1.3 user_listening_history -> playlists (RESTRICT, opsiyonel playlist context)
ALTER TABLE `coremusic_user`.`user_listening_history`
  ADD CONSTRAINT `fk_history_playlist`
    FOREIGN KEY (`playlist_id`) REFERENCES `coremusic_playlist`.`playlists` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- ===========================================================================
-- DOWN (ROLLBACK — yalnız referans; ADR-014 forward-only: production DOWN çalıştırmaz)
-- ===========================================================================
-- ALTER TABLE `coremusic_user`.`user_listening_history` DROP FOREIGN KEY `fk_history_playlist`;
-- ALTER TABLE `coremusic_user`.`user_listening_history` DROP FOREIGN KEY `fk_history_music`;
-- ALTER TABLE `coremusic_user`.`user_listening_history` DROP FOREIGN KEY `fk_history_user`;
-- DROP TABLE IF EXISTS `coremusic_user`.`user_recent_tracks`;
-- (user_listening_history mevcut veri barındırır → ASLA DROP edilmez, §9.4.)
