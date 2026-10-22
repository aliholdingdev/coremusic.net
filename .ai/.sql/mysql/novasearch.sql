-- =========================================================================
-- NovaSearch - nova-search-engine KOD SEMASI (MySQL dialect)
-- Kaynak : app-nodejs-projects/nova-search-engine/packages/database/src/schema.ts
--          (ddl() fonksiyonu - TEK KAYNAK; migrate() ile ayni sonucu verir)
-- Semasi : 3 tablo (channels, videos, search_log) + 4 indeks - VERI YOK
-- Tarih  : 2026-10-22
-- AYRI PROJE: CoreMusic rebuild/regen dongusune DAHIL DEGIL (bkz. ../CLAUDE.md)
--
-- UYARI (DROP YOKTUR - bilincli):
--   Bu dosya idempotent ve yok edicidir (CREATE ... IF NOT EXISTS). Mevcut bir
--   DB'ye uygulanirsa mevcut tablolara DOKUNMAZ, eksik tablolari ekler.
--   NOT: Kod semasi ile CANLI legacy (knex) semasi ARASINDA kolon farklari VAR
--   (legacy: search_logs, kod: search_log; legacy channels kolonlari farklidir).
--   Legacy canli dump git gecmisindedir: git log -- .ai/.sql/mysql/novasearch.sql
--   Kod yazmalari icin bu dosyanin semasi gecerlidir (bkz. scripts/mysql-smoke.ts notu:
--   canli legacy sema dokunulmaz, test DB novasearch_test kullanilir).
--
-- İndeksler: MySQL CREATE INDEX IF NOT EXISTS desteklemez - indeksler CREATE TABLE
--   icine gomuludur; tekrar import hata uretmez, migrate() ile ayni indeksleri verir.
-- Farkli DB hedefi: asagidaki CREATE DATABASE / USE satirlarini kendi DB adiniza
--   gore degistirin (veya USE satirini silip `mysql -u... hedef_db < dosya` ile
--   import edin; komut satiri DB adi USE ile ezilir).
-- Import: mysql -u root -p < novasearch.sql
-- =========================================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `novasearch`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `novasearch`;

CREATE TABLE IF NOT EXISTS `channels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `youtube_id` VARCHAR(64) NOT NULL UNIQUE,
  `name` VARCHAR(512) NOT NULL,
  `description` TEXT,
  `subscriber_count` BIGINT DEFAULT 0,
  `video_count` BIGINT DEFAULT 0,
  `thumbnail_url` VARCHAR(512),
  `verified` BIGINT DEFAULT 0,
  `created_at` VARCHAR(64) NOT NULL,
  `updated_at` VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `videos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `youtube_id` VARCHAR(64) NOT NULL UNIQUE,
  `channel_youtube_id` VARCHAR(64),
  `channel_name` VARCHAR(512),
  `title` VARCHAR(512) NOT NULL,
  `description` TEXT,
  `duration_seconds` BIGINT DEFAULT 0,
  `view_count` BIGINT DEFAULT 0,
  `like_count` BIGINT DEFAULT 0,
  `comment_count` BIGINT DEFAULT 0,
  `view_count_text` VARCHAR(512),
  `published_at` VARCHAR(64),
  `published_text` VARCHAR(64),
  `category` VARCHAR(64),
  `thumbnail_url` VARCHAR(512),
  `tags` TEXT,
  `thumbnails` TEXT,
  `badges` TEXT,
  `caption_tracks` TEXT,
  `top_comments` TEXT,
  `related_ids` TEXT,
  `is_short` BIGINT DEFAULT 0,
  `is_live` BIGINT DEFAULT 0,
  `is_family_safe` BIGINT DEFAULT 1,
  `is_unlisted` BIGINT DEFAULT 0,
  `is_crawlable` BIGINT DEFAULT 1,
  `channel_subscriber_count` BIGINT DEFAULT 0,
  `channel_verified` BIGINT DEFAULT 0,
  `language` VARCHAR(64),
  `spam_score` DOUBLE DEFAULT 0,
  `quality_score` DOUBLE DEFAULT 0,
  `source` VARCHAR(64) DEFAULT 'youtube',
  `created_at` VARCHAR(64) NOT NULL,
  `updated_at` VARCHAR(64) NOT NULL,
  KEY `idx_videos_title` (`title`),
  KEY `idx_videos_views` (`view_count`),
  KEY `idx_videos_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `search_log` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `query` VARCHAR(512) NOT NULL,
  `results_count` BIGINT DEFAULT 0,
  `source` VARCHAR(64),
  `took_ms` BIGINT DEFAULT 0,
  `created_at` VARCHAR(64) NOT NULL,
  KEY `idx_search_log_query` (`query`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
