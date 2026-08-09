-- CoreMusic Database: coremusic_social
-- Version: 7.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_social
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_social;

-- =============================================
-- Table: comments
-- Purpose: Yorumlar (hiyerarsik, cevap zincirleri)
-- BCNF: user_id + entity_type + entity_id + created_at candidate key
-- FK: user_id -> coremusic_auth.users.id, parent_id -> comments.id (self)
-- =============================================
CREATE TABLE comments (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Yorum yapan kullanici ID',
  entity_type ENUM('music','album','playlist','podcast','radio','video') NOT NULL COMMENT 'Yorumlanan varlik tipi',
  entity_id BINARY(16) NOT NULL COMMENT 'Yorumlanan varlik ID',
  parent_id BINARY(16) NULL COMMENT 'Ust yorum ID (cevap icin, NULL = kok yorum)',
  comment_text TEXT NOT NULL COMMENT 'Yorum metni',
  like_count INT NOT NULL DEFAULT 0 COMMENT 'Begeni sayisi',
  reply_count INT NOT NULL DEFAULT 0 COMMENT 'Cevap sayisi',
  is_edited TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Duzenlendi mi?',
  is_pinned TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Sabitlendi mi?',
  is_flagged TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Isaretlendi mi?',
  edited_at TIMESTAMP NULL COMMENT 'Duzenleme zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_comments_user (user_id),
  INDEX idx_comments_entity (entity_type, entity_id),
  INDEX idx_comments_parent (parent_id),
  INDEX idx_comments_pinned (is_pinned),
  INDEX idx_comments_created (created_at),
  FULLTEXT INDEX ftx_comments_text (comment_text),
  CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comments_parent FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Yorumlar (hiyerarsik cevap zincirleri ile)';

-- =============================================
-- Table: comment_likes
-- Purpose: Yorum begenileri
-- BCNF: comment_id + user_id composite unique key
-- FK: comment_id -> comments.id, user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE comment_likes (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  comment_id BINARY(16) NOT NULL COMMENT 'Begenilen yorum ID',
  user_id BINARY(16) NOT NULL COMMENT 'Begenen kullanici ID',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Begenme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_comment_likes_comment_user (comment_id, user_id),
  INDEX idx_comment_likes_user (user_id),
  CONSTRAINT fk_comment_likes_comment FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_comment_likes_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Yorum begenileri (kullanici bazli benzersiz)';

-- =============================================
-- Table: shares
-- Purpose: Paylasim kayitlari
-- BCNF: user_id + entity_type + entity_id + share_platform candidate key
-- FK: user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE shares (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Paylasan kullanici ID',
  entity_type ENUM('music','album','playlist','podcast','radio','video') NOT NULL COMMENT 'Paylasilan varlik tipi',
  entity_id BINARY(16) NOT NULL COMMENT 'Paylasilan varlik ID',
  share_platform ENUM('facebook','twitter','instagram','whatsapp','telegram','email','copy_link','embed') NOT NULL COMMENT 'Paylasim platformu',
  share_url VARCHAR(500) NULL COMMENT 'Paylasim URL',
  click_count INT NOT NULL DEFAULT 0 COMMENT 'Tiklama sayisi',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Paylasim zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_shares_user (user_id),
  INDEX idx_shares_entity (entity_type, entity_id),
  INDEX idx_shares_platform (share_platform),
  INDEX idx_shares_created (created_at),
  CONSTRAINT fk_shares_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Paylasim kayitlari (Facebook, Twitter, Instagram, WhatsApp, Telegram)';

-- =============================================
-- Table: activity_feed
-- Purpose: Kullanici aktivite akisi
-- BCNF: user_id + activity_type + entity_type + entity_id + created_at candidate key
-- FK: user_id -> coremusic_auth.users.id, target_user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE activity_feed (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Aktiviteyi yapan kullanici ID',
  activity_type ENUM('like','unlike','follow','unfollow','comment','share','playlist_create','playlist_update','album_release','now_playing','achievement') NOT NULL COMMENT 'Aktivite tipi',
  entity_type VARCHAR(50) NULL COMMENT 'Iliskili varlik tipi',
  entity_id BINARY(16) NULL COMMENT 'Iliskili varlik ID',
  target_user_id BINARY(16) NULL COMMENT 'Hedef kullanici ID (takip, begeni vb.)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  visibility ENUM('public','friends','private') NOT NULL DEFAULT 'public' COMMENT 'Gorunurluk',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Aktivite zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_activity_user (user_id),
  INDEX idx_activity_type (activity_type),
  INDEX idx_activity_created (created_at),
  INDEX idx_activity_target (target_user_id),
  INDEX idx_activity_visibility (visibility),
  INDEX idx_activity_user_created (user_id, created_at),
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_activity_target FOREIGN KEY (target_user_id) REFERENCES coremusic_auth.users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kullanici aktivite akisi (begeni, takip, yorum, paylasim)';

-- =============================================
-- Table: listening_rooms
-- Purpose: Dinleme odalari (gercek zamanli paylasimli dinleme)
-- BCNF: room_code candidate key
-- FK: host_user_id -> coremusic_auth.users.id, current_music_id -> coremusic_musics.musics.id
-- =============================================
CREATE TABLE listening_rooms (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  host_user_id BINARY(16) NOT NULL COMMENT 'Oda sahibi kullanici ID',
  room_name VARCHAR(200) NOT NULL COMMENT 'Oda adi',
  room_code VARCHAR(10) NOT NULL COMMENT 'Oda kodu (benzersiz, 6-10 karakter)',
  description TEXT NULL COMMENT 'Oda aciklamasi',
  room_type ENUM('public','private','invite_only') NOT NULL DEFAULT 'private' COMMENT 'Oda tipi',
  max_members INT NOT NULL DEFAULT 10 COMMENT 'Maksimum uye sayisi',
  current_music_id BINARY(16) NULL COMMENT 'Suan calinan sarki ID',
  current_position_sec INT NOT NULL DEFAULT 0 COMMENT 'Suan konum (saniye)',
  is_playing TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Suan caliniyor mu?',
  member_count INT NOT NULL DEFAULT 1 COMMENT 'Uye sayisi',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_listening_rooms_code (room_code),
  INDEX idx_listening_rooms_host (host_user_id),
  INDEX idx_listening_rooms_type (room_type),
  INDEX idx_listening_rooms_playing (is_playing),
  CONSTRAINT fk_listening_rooms_host FOREIGN KEY (host_user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_listening_rooms_music FOREIGN KEY (current_music_id) REFERENCES coremusic_musics.musics(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dinleme odalari (gercek zamanli paylasimli dinleme)';

-- =============================================
-- Table: listening_room_members
-- Purpose: Dinleme odasi uyeleri
-- BCNF: room_id + user_id composite unique key
-- FK: room_id -> listening_rooms.id, user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE listening_room_members (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  room_id BINARY(16) NOT NULL COMMENT 'Oda ID',
  user_id BINARY(16) NOT NULL COMMENT 'Uye kullanici ID',
  role ENUM('host','co_host','member','listener') NOT NULL DEFAULT 'member' COMMENT 'Uye rolu',
  is_muted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Susturuldu mu?',
  is_online TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Cevrimici mi?',
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Katilma zamani (UTC)',
  last_active_at TIMESTAMP NULL COMMENT 'Son aktivite zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_room_members_room_user (room_id, user_id),
  INDEX idx_room_members_user (user_id),
  INDEX idx_room_members_role (role),
  INDEX idx_room_members_online (is_online),
  CONSTRAINT fk_room_members_room FOREIGN KEY (room_id) REFERENCES listening_rooms(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_room_members_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dinleme odasi uyeleri (host, co-host, member, listener)';

-- =============================================
-- Table: listening_room_queue
-- Purpose: Dinleme odasi sarki sirasi
-- BCNF: room_id + position + music_id candidate key
-- FK: room_id -> listening_rooms.id, music_id -> coremusic_musics.musics.id, added_by -> coremusic_auth.users.id
-- =============================================
CREATE TABLE listening_room_queue (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  room_id BINARY(16) NOT NULL COMMENT 'Oda ID',
  music_id BINARY(16) NOT NULL COMMENT 'Sarki ID',
  added_by BINARY(16) NOT NULL COMMENT 'Ekleyen kullanici ID',
  position INT NOT NULL COMMENT 'Siradaki pozisyon',
  is_playing TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Suan caliniyor mu?',
  played_at TIMESTAMP NULL COMMENT 'Calinma zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Eklenme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_room_queue_room_position (room_id, position),
  INDEX idx_room_queue_music (music_id),
  INDEX idx_room_queue_added_by (added_by),
  CONSTRAINT fk_room_queue_room FOREIGN KEY (room_id) REFERENCES listening_rooms(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_room_queue_music FOREIGN KEY (music_id) REFERENCES coremusic_musics.musics(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_room_queue_added_by FOREIGN KEY (added_by) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dinleme odasi sarki sirasi (kullanici bazli ekleme)';

-- =============================================
-- Table: user_achievements
-- Purpose: Kullanici basarilari ve rozetleri
-- BCNF: user_id + achievement_type composite unique key
-- FK: user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE user_achievements (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Kullanici ID',
  achievement_type VARCHAR(100) NOT NULL COMMENT 'Basari tipi (benzersiz)',
  achievement_name VARCHAR(200) NOT NULL COMMENT 'Basari adi',
  achievement_description TEXT NULL COMMENT 'Basari aciklamasi',
  achievement_icon VARCHAR(500) NULL COMMENT 'Basari ikonu URL',
  rarity ENUM('common','uncommon','rare','epic','legendary') NOT NULL DEFAULT 'common' COMMENT 'Nadirlik',
  progress_current INT NOT NULL DEFAULT 0 COMMENT 'Mevcut ilerleme',
  progress_target INT NOT NULL DEFAULT 1 COMMENT 'Hedef ilerleme',
  is_completed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Tamamlandi mi?',
  completed_at TIMESTAMP NULL COMMENT 'Tamamlanma zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_achievements_user (user_id),
  INDEX idx_achievements_type (achievement_type),
  INDEX idx_achievements_completed (is_completed),
  INDEX idx_achievements_rarity (rarity),
  UNIQUE KEY uk_achievements_user_type (user_id, achievement_type),
  CONSTRAINT fk_achievements_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kullanici basarilari ve rozetleri (common, uncommon, rare, epic, legendary)';

-- =============================================
-- Table: social_notifications
-- Purpose: Sosyal bildirimler (takip, begeni, yorum, oda daveti)
-- BCNF: user_id + notification_type + entity_type + entity_id + created_at candidate key
-- FK: user_id -> coremusic_auth.users.id, from_user_id -> coremusic_auth.users.id
-- =============================================
CREATE TABLE social_notifications (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Hedef kullanici ID',
  from_user_id BINARY(16) NULL COMMENT 'Gonderen kullanici ID (sistem icin NULL)',
  notification_type ENUM('follow','like_comment','like_playlist','comment_reply','room_invite','share','mention','achievement') NOT NULL COMMENT 'Bildirim tipi',
  entity_type VARCHAR(50) NULL COMMENT 'Iliskili varlik tipi',
  entity_id BINARY(16) NULL COMMENT 'Iliskili varlik ID',
  message TEXT NULL COMMENT 'Bildirim mesaji',
  is_read TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Okundu mu?',
  is_dismissed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Gormezden gelindi mi?',
  read_at TIMESTAMP NULL COMMENT 'Okunma zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_social_notif_user (user_id),
  INDEX idx_social_notif_from (from_user_id),
  INDEX idx_social_notif_type (notification_type),
  INDEX idx_social_notif_read (is_read),
  INDEX idx_social_notif_created (created_at),
  CONSTRAINT fk_social_notif_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_social_notif_from FOREIGN KEY (from_user_id) REFERENCES coremusic_auth.users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sosyal bildirimler (takip, begeni, yorum, oda daveti, bahsetme)';

-- =============================================
-- CoreMusic coremusic_social Database v7.0.0
-- Tables: 9
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
