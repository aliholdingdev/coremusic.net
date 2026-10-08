-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_social`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_social`;

CREATE TABLE `achievements` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `achievement_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Benzersiz basari tipi kodu',
  `achievement_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gorunur ad',
  `achievement_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `achievement_icon` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ikon URL',
  `rarity` enum('common','uncommon','rare','epic','legendary') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'common' COMMENT 'Nadirlik (karma ENUM — karar #5)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme (UTC)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_achievements_type` (`achievement_type`),
  KEY `idx_achievements_rarity` (`rarity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Basari/rozet lookup tablosu (3NF — karar #5)';

CREATE TABLE `activity_feed` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Aktiviteyi yapan kullanici ID',
  `activity_type` enum('like','unlike','follow','unfollow','comment','share','playlist_create','playlist_update','album_release','now_playing','achievement') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Aktivite tipi',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Iliskili varlik tipi',
  `entity_id` binary(16) DEFAULT NULL COMMENT 'Iliskili varlik ID',
  `target_user_id` binary(16) DEFAULT NULL COMMENT 'Hedef kullanici ID (takip, begeni vb.)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `visibility` enum('public','friends','private') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'Gorunurluk',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Aktivite zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_activity_created` (`created_at`),
  KEY `idx_activity_target` (`target_user_id`),
  KEY `idx_activity_visibility` (`visibility`),
  KEY `idx_activity_user_created` (`user_id`,`created_at`),
  CONSTRAINT `fk_activity_target` FOREIGN KEY (`target_user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici aktivite akisi (begeni, takip, yorum, paylasim)';

CREATE TABLE `comment_likes` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `comment_id` binary(16) NOT NULL COMMENT 'Begenilen yorum ID',
  `user_id` binary(16) NOT NULL COMMENT 'Begenen kullanici ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Begenme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_comment_likes_comment_user` (`comment_id`,`user_id`),
  KEY `idx_comment_likes_user` (`user_id`),
  CONSTRAINT `fk_comment_likes_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comment_likes_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Yorum begenileri (kullanici bazli benzersiz)';

CREATE TABLE `comments` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Yorum yapan kullanici ID',
  `entity_type` enum('music','album','playlist','podcast','radio','video') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yorumlanan varlik tipi',
  `entity_id` binary(16) NOT NULL COMMENT 'Yorumlanan varlik ID',
  `parent_id` binary(16) DEFAULT NULL COMMENT 'Ust yorum ID (cevap icin, NULL = kok yorum)',
  `comment_text` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yorum metni',
  `like_count` int NOT NULL DEFAULT '0' COMMENT 'Begeni sayisi',
  `reply_count` int NOT NULL DEFAULT '0' COMMENT 'Cevap sayisi',
  `is_edited` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Duzenlendi mi?',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Sabitlendi mi?',
  `is_flagged` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Isaretlendi mi?',
  `edited_at` timestamp NULL DEFAULT NULL COMMENT 'Duzenleme zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_comments_user` (`user_id`),
  KEY `idx_comments_entity` (`entity_type`,`entity_id`),
  KEY `idx_comments_parent` (`parent_id`),
  KEY `idx_comments_pinned` (`is_pinned`),
  KEY `idx_comments_created` (`created_at`),
  FULLTEXT KEY `ftx_comments_text` (`comment_text`),
  CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Yorumlar (hiyerarsik cevap zincirleri ile)';

CREATE TABLE `listening_room_members` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `room_id` binary(16) NOT NULL COMMENT 'Oda ID',
  `user_id` binary(16) NOT NULL COMMENT 'Uye kullanici ID',
  `role` enum('host','co_host','member','listener') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member' COMMENT 'Uye rolu',
  `is_muted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Susturuldu mu?',
  `is_online` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Cevrimici mi?',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Katilma zamani (UTC)',
  `last_active_at` timestamp NULL DEFAULT NULL COMMENT 'Son aktivite zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_room_members_room_user` (`room_id`,`user_id`),
  KEY `idx_room_members_user` (`user_id`),
  KEY `idx_room_members_role` (`role`),
  KEY `idx_room_members_online` (`is_online`),
  CONSTRAINT `fk_room_members_room` FOREIGN KEY (`room_id`) REFERENCES `listening_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_room_members_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dinleme odasi uyeleri (host, co-host, member, listener)';

CREATE TABLE `listening_room_queue` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `room_id` binary(16) NOT NULL COMMENT 'Oda ID',
  `music_id` binary(16) NOT NULL COMMENT 'Sarki ID',
  `added_by` binary(16) NOT NULL COMMENT 'Ekleyen kullanici ID',
  `position` int NOT NULL COMMENT 'Siradaki pozisyon',
  `is_playing` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Suan caliniyor mu?',
  `played_at` timestamp NULL DEFAULT NULL COMMENT 'Calinma zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Eklenme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_room_queue_room_position` (`room_id`,`position`),
  KEY `idx_room_queue_music` (`music_id`),
  KEY `idx_room_queue_added_by` (`added_by`),
  CONSTRAINT `fk_room_queue_added_by` FOREIGN KEY (`added_by`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_room_queue_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_room_queue_room` FOREIGN KEY (`room_id`) REFERENCES `listening_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dinleme odasi sarki sirasi (kullanici bazli ekleme)';

CREATE TABLE `listening_rooms` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `host_user_id` binary(16) NOT NULL COMMENT 'Oda sahibi kullanici ID',
  `room_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Oda adi',
  `room_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Oda kodu (benzersiz, 6-10 karakter)',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Oda aciklamasi',
  `room_type` enum('public','private','invite_only') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private' COMMENT 'Oda tipi',
  `max_members` int NOT NULL DEFAULT '10' COMMENT 'Maksimum uye sayisi',
  `current_music_id` binary(16) DEFAULT NULL COMMENT 'Suan calinan sarki ID',
  `current_position_sec` int NOT NULL DEFAULT '0' COMMENT 'Suan konum (saniye)',
  `is_playing` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Suan caliniyor mu?',
  `member_count` int NOT NULL DEFAULT '1' COMMENT 'Uye sayisi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_listening_rooms_code` (`room_code`),
  KEY `idx_listening_rooms_host` (`host_user_id`),
  KEY `idx_listening_rooms_type` (`room_type`),
  KEY `idx_listening_rooms_playing` (`is_playing`),
  KEY `fk_listening_rooms_music` (`current_music_id`),
  CONSTRAINT `fk_listening_rooms_host` FOREIGN KEY (`host_user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_listening_rooms_music` FOREIGN KEY (`current_music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dinleme odalari (gercek zamanli paylasimli dinleme)';

CREATE TABLE `shares` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Paylasan kullanici ID',
  `entity_type` enum('music','album','playlist','podcast','radio','video') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Paylasilan varlik tipi',
  `entity_id` binary(16) NOT NULL COMMENT 'Paylasilan varlik ID',
  `share_platform` enum('facebook','twitter','instagram','whatsapp','telegram','email','copy_link','embed') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Paylasim platformu',
  `share_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Paylasim URL',
  `click_count` int NOT NULL DEFAULT '0' COMMENT 'Tiklama sayisi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Paylasim zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_shares_user` (`user_id`),
  KEY `idx_shares_entity` (`entity_type`,`entity_id`),
  KEY `idx_shares_platform` (`share_platform`),
  KEY `idx_shares_created` (`created_at`),
  CONSTRAINT `fk_shares_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Paylasim kayitlari (Facebook, Twitter, Instagram, WhatsApp, Telegram)';

CREATE TABLE `social_notifications` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Hedef kullanici ID',
  `from_user_id` binary(16) DEFAULT NULL COMMENT 'Gonderen kullanici ID (sistem icin NULL)',
  `notification_type` enum('follow','like_comment','like_playlist','comment_reply','room_invite','share','mention','achievement') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bildirim tipi',
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Iliskili varlik tipi',
  `entity_id` binary(16) DEFAULT NULL COMMENT 'Iliskili varlik ID',
  `message` text COLLATE utf8mb4_unicode_ci COMMENT 'Bildirim mesaji',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Okundu mu?',
  `is_dismissed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Gormezden gelindi mi?',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'Okunma zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_social_notif_user` (`user_id`),
  KEY `idx_social_notif_from` (`from_user_id`),
  KEY `idx_social_notif_type` (`notification_type`),
  KEY `idx_social_notif_read` (`is_read`),
  KEY `idx_social_notif_created` (`created_at`),
  CONSTRAINT `fk_social_notif_from` FOREIGN KEY (`from_user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_social_notif_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sosyal bildirimler (takip, begeni, yorum, oda daveti, bahsetme)';

CREATE TABLE `user_achievements` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Kullanici ID',
  `achievement_id` binary(16) DEFAULT NULL COMMENT 'FK -> achievements.id (NULL = backfill oncesi)',
  `achievement_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Basari tipi (benzersiz)',
  `achievement_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Basari adi',
  `achievement_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Basari aciklamasi',
  `achievement_icon` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Basari ikonu URL',
  `rarity` enum('common','uncommon','rare','epic','legendary') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'common' COMMENT 'Nadirlik',
  `progress_current` int NOT NULL DEFAULT '0' COMMENT 'Mevcut ilerleme',
  `progress_target` int NOT NULL DEFAULT '1' COMMENT 'Hedef ilerleme',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Tamamlandi mi?',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Tamamlanma zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_achievements_user_type` (`user_id`,`achievement_type`),
  KEY `idx_achievements_user` (`user_id`),
  KEY `idx_achievements_type` (`achievement_type`),
  KEY `idx_achievements_completed` (`is_completed`),
  KEY `idx_achievements_rarity` (`rarity`),
  KEY `idx_achievements_ach` (`achievement_id`),
  CONSTRAINT `fk_achievements_ach` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_achievements_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici basarilari ve rozetleri (common, uncommon, rare, epic, legendary)';

SET FOREIGN_KEY_CHECKS = 1;
