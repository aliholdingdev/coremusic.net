-- =========================================================================
-- NovaSearch - nova-search-engine KOD SEMASI (SQLite dialect)
-- Kaynak : app-nodejs-projects/nova-search-engine/packages/database/src/schema.ts
--          (ddl() + INDEXES - TEK KAYNAK; migrate() ile birebir ayni sema)
-- Semasi : 3 tablo (channels, videos, search_log) + 4 indeks - VERI YOK
-- Tarih  : 2026-10-22
-- AYRI PROJE: CoreMusic rebuild/regen dongusune DAHIL DEGIL (bkz. ../CLAUDE.md)
--
-- DROP YOKTUR - idempotent (CREATE TABLE/INDEX IF NOT EXISTS); veri silinmez.
-- Import : sqlite3 novasearch.db < novasearch.sql
--    veya: node:sqlite DatabaseSync.exec() / uygulamanin kendi migrate()
--    (SqliteAdapter varsayilan yol: ./data/novasearch.db)
-- =========================================================================

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS channels (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  youtube_id TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  description TEXT,
  subscriber_count INTEGER DEFAULT 0,
  video_count INTEGER DEFAULT 0,
  thumbnail_url TEXT,
  verified INTEGER DEFAULT 0,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS videos (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  youtube_id TEXT NOT NULL UNIQUE,
  channel_youtube_id TEXT,
  channel_name TEXT,
  title TEXT NOT NULL,
  description TEXT,
  duration_seconds INTEGER DEFAULT 0,
  view_count INTEGER DEFAULT 0,
  like_count INTEGER DEFAULT 0,
  comment_count INTEGER DEFAULT 0,
  view_count_text TEXT,
  published_at TEXT,
  published_text TEXT,
  category TEXT,
  thumbnail_url TEXT,
  tags TEXT,
  thumbnails TEXT,
  badges TEXT,
  caption_tracks TEXT,
  top_comments TEXT,
  related_ids TEXT,
  is_short INTEGER DEFAULT 0,
  is_live INTEGER DEFAULT 0,
  is_family_safe INTEGER DEFAULT 1,
  is_unlisted INTEGER DEFAULT 0,
  is_crawlable INTEGER DEFAULT 1,
  channel_subscriber_count INTEGER DEFAULT 0,
  channel_verified INTEGER DEFAULT 0,
  language TEXT,
  spam_score REAL DEFAULT 0,
  quality_score REAL DEFAULT 0,
  source TEXT DEFAULT 'youtube',
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS search_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  query TEXT NOT NULL,
  results_count INTEGER DEFAULT 0,
  source TEXT,
  took_ms INTEGER DEFAULT 0,
  created_at TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_videos_title ON videos (title);
CREATE INDEX IF NOT EXISTS idx_videos_views ON videos (view_count);
CREATE INDEX IF NOT EXISTS idx_videos_published ON videos (published_at);
CREATE INDEX IF NOT EXISTS idx_search_log_query ON search_log (query);
