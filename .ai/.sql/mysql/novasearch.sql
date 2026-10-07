-- ====================================================================
-- novasearch — YouTube arama/kanal/video indeks servisi veritabanı
-- ====================================================================
-- Kaynak: CANLI şemadan salt-okunur dump (SHOW CREATE TABLE, READ ONLY
--         transaction) — 2026-10-07, P4-22/A-F03 kapsamı.
-- Durum : CANLI VAR, ilk kez şema-SSOT'a yazıldı (öncece hiçbir .sql'te yoktu).
-- Yönetim: Node/knex (knex_migrations + knex_migrations_lock tabloları) —
--         ADR-014 özel PHP runner DIŞINDA kurulmuş. Sahiplik/kayıt kararı
--         ADR-040 envanterine işlenmeli (P4-22 takibi — DOĞRULAMA GEREKLİ).
-- Not   : uuid() sunucu fonksiyonu (MySQL 8+) kullanır; ADR-040'ın BINARY(16)
--         UUID v7 deseninden FARKLIDIR (varchar(36) PK'lar) — bu dosya
--         mevcut canlı gerçeği yansıtır, normalize edilmez (destructive yok).
-- ====================================================================

CREATE TABLE `cache_entries` (
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `cache_key` varchar(255) NOT NULL,
  `cache_value` text NOT NULL,
  `cache_type` varchar(50) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cache_key` (`cache_key`),
  KEY `idx_cache_key` (`cache_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `channels` (
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `subscriber_count` bigint DEFAULT '0',
  `video_count` int DEFAULT '0',
  `view_count` bigint DEFAULT '0',
  `thumbnail_url` varchar(1024) DEFAULT '',
  `banner_url` varchar(1024) DEFAULT '',
  `country` varchar(10) DEFAULT '',
  `language` varchar(10) DEFAULT '',
  `published_at` datetime DEFAULT NULL,
  `hidden_subscriber_count` tinyint(1) DEFAULT '0',
  `keywords` text,
  `verification_status` varchar(20) DEFAULT 'none',
  `channel_type` varchar(20) DEFAULT 'personal',
  `available_tabs` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `youtube_id` (`youtube_id`),
  KEY `idx_youtube_id` (`youtube_id`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `knex_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
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
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `channel_id` varchar(36) NOT NULL,
  `channel_name` varchar(255) DEFAULT '',
  `video_count` int DEFAULT '0',
  `thumbnail_url` varchar(1024) DEFAULT '',
  `published_at` datetime DEFAULT NULL,
  `privacy_status` varchar(20) DEFAULT 'public',
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
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `query` text NOT NULL,
  `filters` text,
  `results_count` int DEFAULT '0',
  `response_time_ms` int DEFAULT '0',
  `source` varchar(50) DEFAULT 'api',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_query` (`query`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `videos` (
  `id` varchar(36) NOT NULL DEFAULT (uuid()),
  `youtube_id` varchar(20) NOT NULL,
  `title` text NOT NULL,
  `description` text,
  `channel_id` varchar(36) NOT NULL,
  `channel_name` varchar(255) DEFAULT '',
  `duration` int DEFAULT '0',
  `view_count` bigint DEFAULT '0',
  `like_count` bigint DEFAULT '0',
  `comment_count` bigint DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `thumbnail_url` varchar(1024) DEFAULT '',
  `thumbnails` text,
  `tags` text,
  `category` varchar(100) DEFAULT '',
  `language` varchar(10) DEFAULT '',
  `is_live` tinyint(1) DEFAULT '0',
  `is_private` tinyint(1) DEFAULT '0',
  `definition` varchar(5) DEFAULT NULL,
  `projection` varchar(20) DEFAULT NULL,
  `has_custom_thumbnail` tinyint(1) DEFAULT '0',
  `is_crawlable` tinyint(1) DEFAULT '1',
  `is_unlisted` tinyint(1) DEFAULT '0',
  `recording_date` date DEFAULT NULL,
  `embed_html` text,
  `embed_width` int DEFAULT NULL,
  `embed_height` int DEFAULT NULL,
  `short_url` varchar(255) DEFAULT NULL,
  `published_at_precision` varchar(10) DEFAULT 'exact',
  `live_broadcast_content` varchar(10) DEFAULT 'none',
  `view_count_accuracy` varchar(15) DEFAULT 'exact',
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
