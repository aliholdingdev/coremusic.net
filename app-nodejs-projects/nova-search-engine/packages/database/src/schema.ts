// ===========================================
// Şema + Migration — tek kaynak, iki dialekt
// ===========================================

import type { DbAdapter } from './adapter.js';

function ddl(dialect: 'sqlite' | 'mysql'): string {
  const pk =
    dialect === 'sqlite'
      ? 'INTEGER PRIMARY KEY AUTOINCREMENT'
      : 'INT AUTO_INCREMENT PRIMARY KEY';
  const text = dialect === 'sqlite' ? 'TEXT' : 'TEXT';
  const varchar = dialect === 'sqlite' ? 'TEXT' : 'VARCHAR(512)';
  const idVar = dialect === 'sqlite' ? 'TEXT' : 'VARCHAR(64)';
  const int = dialect === 'sqlite' ? 'INTEGER' : 'BIGINT';
  const real = dialect === 'sqlite' ? 'REAL' : 'DOUBLE';

  return `
CREATE TABLE IF NOT EXISTS channels (
  id ${pk},
  youtube_id ${idVar} NOT NULL UNIQUE,
  name ${varchar} NOT NULL,
  description ${text},
  subscriber_count ${int} DEFAULT 0,
  video_count ${int} DEFAULT 0,
  thumbnail_url ${varchar},
  verified ${int} DEFAULT 0,
  created_at ${idVar} NOT NULL,
  updated_at ${idVar} NOT NULL
);

CREATE TABLE IF NOT EXISTS videos (
  id ${pk},
  youtube_id ${idVar} NOT NULL UNIQUE,
  channel_youtube_id ${idVar},
  channel_name ${varchar},
  title ${varchar} NOT NULL,
  description ${text},
  duration_seconds ${int} DEFAULT 0,
  view_count ${int} DEFAULT 0,
  like_count ${int} DEFAULT 0,
  comment_count ${int} DEFAULT 0,
  view_count_text ${varchar},
  published_at ${idVar},
  published_text ${idVar},
  category ${idVar},
  thumbnail_url ${varchar},
  tags ${text},
  thumbnails ${text},
  badges ${text},
  caption_tracks ${text},
  top_comments ${text},
  related_ids ${text},
  is_short ${int} DEFAULT 0,
  is_live ${int} DEFAULT 0,
  is_family_safe ${int} DEFAULT 1,
  is_unlisted ${int} DEFAULT 0,
  is_crawlable ${int} DEFAULT 1,
  channel_subscriber_count ${int} DEFAULT 0,
  channel_verified ${int} DEFAULT 0,
  language ${idVar},
  spam_score ${real} DEFAULT 0,
  quality_score ${real} DEFAULT 0,
  source ${idVar} DEFAULT 'youtube',
  created_at ${idVar} NOT NULL,
  updated_at ${idVar} NOT NULL
);

CREATE TABLE IF NOT EXISTS search_log (
  id ${pk},
  query ${varchar} NOT NULL,
  results_count ${int} DEFAULT 0,
  source ${idVar},
  took_ms ${int} DEFAULT 0,
  created_at ${idVar} NOT NULL
);
`;
}

const INDEXES = [
  'CREATE INDEX IF NOT EXISTS idx_videos_title ON videos (title)',
  'CREATE INDEX IF NOT EXISTS idx_videos_views ON videos (view_count)',
  'CREATE INDEX IF NOT EXISTS idx_videos_published ON videos (published_at)',
  'CREATE INDEX IF NOT EXISTS idx_search_log_query ON search_log (query)',
];

/** Şemayı kur (idempotent). */
export async function migrate(db: DbAdapter): Promise<void> {
  // DDL'i tek tek yürüt: MySQL havuzları `multipleStatements:false` (SQL injection
  // güvenliği) ile çalışır ve çoklu ifadeyi reddeder; SQLite yerel destekler.
  // Bölünen ifadeler iki dialektte de aynıdır — tek kaynak `ddl()`.
  const statements = ddl(db.dialect)
    .split(';')
    .map((s) => s.trim())
    .filter((s) => s.length > 0);
  for (const sql of statements) {
    await db.exec(sql);
  }
  for (const idx of INDEXES) {
    try {
      await db.exec(idx);
    } catch {
      // MySQL <8 IF NOT EXISTS desteklemez — yoksay, tablo yeni ise zaten yoktur
    }
  }
  // Eski kurulumlar için kolon tamamlama (ADD COLUMN — varsa yoksayılır)
  const int = db.dialect === 'sqlite' ? 'INTEGER' : 'BIGINT';
  const varc = db.dialect === 'sqlite' ? 'TEXT' : 'VARCHAR(512)';
  const real = db.dialect === 'sqlite' ? 'REAL' : 'DOUBLE';
  const alters = [
    `ALTER TABLE videos ADD COLUMN channel_name ${varc}`,
    `ALTER TABLE videos ADD COLUMN comment_count ${int} DEFAULT 0`,
    `ALTER TABLE videos ADD COLUMN published_text ${varc}`,
    `ALTER TABLE videos ADD COLUMN category ${varc}`,
    `ALTER TABLE videos ADD COLUMN thumbnails ${db.dialect === 'sqlite' ? 'TEXT' : 'TEXT'}`,
    `ALTER TABLE videos ADD COLUMN badges ${db.dialect === 'sqlite' ? 'TEXT' : 'TEXT'}`,
    `ALTER TABLE videos ADD COLUMN is_family_safe ${int} DEFAULT 1`,
    `ALTER TABLE videos ADD COLUMN is_unlisted ${int} DEFAULT 0`,
    `ALTER TABLE videos ADD COLUMN is_crawlable ${int} DEFAULT 1`,
    `ALTER TABLE videos ADD COLUMN channel_subscriber_count ${int} DEFAULT 0`,
    `ALTER TABLE videos ADD COLUMN channel_verified ${int} DEFAULT 0`,
    `ALTER TABLE videos ADD COLUMN view_count_text ${varc}`,
    `ALTER TABLE videos ADD COLUMN caption_tracks ${db.dialect === 'sqlite' ? 'TEXT' : 'TEXT'}`,
    `ALTER TABLE videos ADD COLUMN top_comments ${db.dialect === 'sqlite' ? 'TEXT' : 'TEXT'}`,
    `ALTER TABLE videos ADD COLUMN related_ids ${db.dialect === 'sqlite' ? 'TEXT' : 'TEXT'}`,
    `ALTER TABLE videos ADD COLUMN language ${varc}`,
    `ALTER TABLE videos ADD COLUMN spam_score ${real} DEFAULT 0`,
  ];
  for (const sql of alters) {
    try {
      await db.exec(sql);
    } catch {
      // kolon zaten var
    }
  }
}
