-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `novasearch`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `novasearch`;

CREATE TABLE `cache_entries` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `cache_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cache_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cache_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cache_key` (`cache_key`),
  KEY `idx_cache_key` (`cache_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `channels` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `subscriber_count` bigint DEFAULT '0',
  `video_count` int DEFAULT '0',
  `view_count` bigint DEFAULT '0',
  `thumbnail_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `banner_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `country` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `published_at` datetime DEFAULT NULL,
  `hidden_subscriber_count` tinyint(1) DEFAULT '0',
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `verification_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `channel_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'personal',
  `available_tabs` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `idx_youtube_id` (`youtube_id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `knex_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch` int DEFAULT NULL,
  `migration_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `knex_migrations_lock` (
  `index` int unsigned NOT NULL AUTO_INCREMENT,
  `is_locked` int DEFAULT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `playlists` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `channel_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `video_count` int DEFAULT '0',
  `thumbnail_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `published_at` datetime DEFAULT NULL,
  `privacy_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `view_count` bigint DEFAULT '0',
  `last_updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `channel_id` (`channel_id`),
  KEY `idx_youtube_id` (`youtube_id`),
  CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `search_logs` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `query` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `filters` text COLLATE utf8mb4_unicode_ci,
  `results_count` int DEFAULT '0',
  `response_time_ms` int DEFAULT '0',
  `source` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'api',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_query` (`query`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `videos` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `channel_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `duration` int DEFAULT '0',
  `view_count` bigint DEFAULT '0',
  `like_count` bigint DEFAULT '0',
  `comment_count` bigint DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `thumbnail_url` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `thumbnails` text COLLATE utf8mb4_unicode_ci,
  `tags` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_live` tinyint(1) DEFAULT '0',
  `is_private` tinyint(1) DEFAULT '0',
  `definition` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `projection` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_custom_thumbnail` tinyint(1) DEFAULT '0',
  `is_crawlable` tinyint(1) DEFAULT '1',
  `is_unlisted` tinyint(1) DEFAULT '0',
  `recording_date` date DEFAULT NULL,
  `embed_html` text COLLATE utf8mb4_unicode_ci,
  `embed_width` int DEFAULT NULL,
  `embed_height` int DEFAULT NULL,
  `short_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at_precision` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'exact',
  `live_broadcast_content` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `view_count_accuracy` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT 'exact',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `idx_youtube_id` (`youtube_id`),
  KEY `idx_channel_id` (`channel_id`),
  KEY `idx_title` (`title`(100)),
  KEY `idx_view_count` (`view_count` DESC),
  CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
