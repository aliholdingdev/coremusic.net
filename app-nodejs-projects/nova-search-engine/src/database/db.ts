/** Veritabanı katmanı — Node 26 yerleşik node:sqlite (FTS5 doğrulandı). */
import { DatabaseSync } from 'node:sqlite';
import { mkdirSync, existsSync } from 'node:fs';
import { dirname } from 'node:path';
import { config, ensureDirs } from '../config.ts';
import type { VideoRecord, JobRow, JobType } from '../core/types.ts';

let db: DatabaseSync | null = null;

const SCHEMA = `
CREATE TABLE IF NOT EXISTS videos (
  youtube_id TEXT PRIMARY KEY,
  title TEXT NOT NULL,
  description TEXT NOT NULL DEFAULT '',
  channel_id TEXT NOT NULL DEFAULT '',
  channel_name TEXT NOT NULL DEFAULT '',
  channel_url TEXT,
  duration_sec INTEGER NOT NULL DEFAULT 0,
  view_count INTEGER NOT NULL DEFAULT 0,
  like_count INTEGER,
  published_at TEXT,
  category TEXT,
  tags TEXT NOT NULL DEFAULT '',
  thumbnail_url TEXT,
  first_seen_at TEXT NOT NULL,
  last_seen_at TEXT NOT NULL,
  is_short INTEGER NOT NULL DEFAULT 0
);

CREATE VIRTUAL TABLE IF NOT EXISTS videos_fts USING fts5(
  title, description, tags, channel_name,
  content='videos', content_rowid='rowid'
);

CREATE TRIGGER IF NOT EXISTS videos_ai AFTER INSERT ON videos BEGIN
  INSERT INTO videos_fts(rowid, title, description, tags, channel_name)
  VALUES (new.rowid, new.title, new.description, new.tags, new.channel_name);
END;
CREATE TRIGGER IF NOT EXISTS videos_ad AFTER DELETE ON videos BEGIN
  INSERT INTO videos_fts(videos_fts, rowid, title, description, tags, channel_name)
  VALUES ('delete', old.rowid, old.title, old.description, old.tags, old.channel_name);
END;
CREATE TRIGGER IF NOT EXISTS videos_au AFTER UPDATE ON videos BEGIN
  INSERT INTO videos_fts(videos_fts, rowid, title, description, tags, channel_name)
  VALUES ('delete', old.rowid, old.title, old.description, old.tags, old.channel_name);
  INSERT INTO videos_fts(rowid, title, description, tags, channel_name)
  VALUES (new.rowid, new.title, new.description, new.tags, new.channel_name);
END;

CREATE TABLE IF NOT EXISTS channels (
  channel_id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  subs INTEGER,
  fetched_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS playlists (
  playlist_id TEXT PRIMARY KEY,
  title TEXT NOT NULL,
  channel_id TEXT,
  item_count INTEGER DEFAULT 0,
  fetched_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS search_history (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  query TEXT NOT NULL,
  result_count INTEGER NOT NULL DEFAULT 0,
  searched_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_history_query ON search_history(query);

CREATE TABLE IF NOT EXISTS ai_queries (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  raw_query TEXT NOT NULL,
  expanded_json TEXT NOT NULL,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS jobs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  type TEXT NOT NULL,
  payload TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'pending',
  attempts INTEGER NOT NULL DEFAULT 0,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_jobs_status ON jobs(status, id);

CREATE TABLE IF NOT EXISTS meta (
  key TEXT PRIMARY KEY,
  value TEXT NOT NULL
);
`;

export function getDb(): DatabaseSync {
  if (db) return db;
  ensureDirs();
  if (!existsSync(dirname(config.dbPath))) mkdirSync(dirname(config.dbPath), { recursive: true });
  db = new DatabaseSync(config.dbPath);
  db.exec('PRAGMA journal_mode = WAL;');
  db.exec('PRAGMA foreign_keys = ON;');
  db.exec(SCHEMA);
  // migration: eski tablolara is_short kolonu (v1 → v2)
  const cols = db.prepare('PRAGMA table_info(videos)').all() as { name: string }[];
  if (!cols.some((c) => c.name === 'is_short')) {
    db.exec('ALTER TABLE videos ADD COLUMN is_short INTEGER NOT NULL DEFAULT 0');
  }
  // migration: kanal handle bağlantısı (v2 → v3)
  if (!cols.some((c) => c.name === 'channel_url')) {
    db.exec('ALTER TABLE videos ADD COLUMN channel_url TEXT');
  }
  db.prepare("INSERT OR REPLACE INTO meta(key, value) VALUES ('schema_version', '3')").run();
  return db;
}

export function closeDb(): void {
  db?.close();
  db = null;
}

/* ---------- Videos ---------- */

const COLS = `youtube_id, title, description, channel_id, channel_name, channel_url, duration_sec,
  view_count, like_count, published_at, category, tags, thumbnail_url, first_seen_at, last_seen_at, is_short`;

/** publishedAt sözleşmesi ISO — eski satırlardaki göreli metin ("6 months ago") null'a düşer. */
function isIso(v: string | null | undefined): v is string {
  return !!v && /^\d{4}-\d{2}-\d{2}/.test(v);
}

function rowToVideo(r: Record<string, unknown>): VideoRecord {
  return {
    youtubeId: String(r.youtube_id),
    title: String(r.title),
    description: String(r.description ?? ''),
    channelId: String(r.channel_id ?? ''),
    channelName: String(r.channel_name ?? ''),
    channelUrl: (r.channel_url as string) ?? null,
    durationSec: Number(r.duration_sec ?? 0),
    viewCount: Number(r.view_count ?? 0),
    likeCount: r.like_count == null ? null : Number(r.like_count),
    publishedAt: isIso((r.published_at as string) ?? null) ? (r.published_at as string) : null,
    category: (r.category as string) ?? null,
    tags: String(r.tags ?? ''),
    thumbnailUrl: (r.thumbnail_url as string) ?? null,
    firstSeenAt: String(r.first_seen_at ?? ''),
    lastSeenAt: String(r.last_seen_at ?? ''),
    isShort: Number(r.is_short ?? 0) === 1,
  };
}

/** Normalize edilmiş kayıt: varsa yalnız değişen alanları güncelle (duplicate koruması). */
export function upsertVideo(v: VideoRecord): 'inserted' | 'updated' {
  const d = getDb();
  const existing = d.prepare(`SELECT youtube_id, view_count, like_count FROM videos WHERE youtube_id = ?`).get(v.youtubeId) as
    | { youtube_id: string; view_count: number; like_count: number | null }
    | undefined;

  if (!existing) {
    d.prepare(
      `INSERT INTO videos (${COLS}) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)`,
    ).run(
      v.youtubeId, v.title, v.description, v.channelId, v.channelName, v.channelUrl ?? null, v.durationSec,
      v.viewCount, v.likeCount, v.publishedAt, v.category, v.tags, v.thumbnailUrl,
      v.firstSeenAt, v.lastSeenAt, v.isShort ? 1 : 0,
    );
    return 'inserted';
  }

  const changed =
    existing.view_count !== v.viewCount || existing.like_count !== v.likeCount ||
    !existing; // stat güncellemesi
  if (changed) {
    d.prepare(
      `UPDATE videos SET view_count = ?, like_count = ?, channel_url = COALESCE(channel_url, ?), last_seen_at = ? WHERE youtube_id = ?`,
    ).run(v.viewCount, v.likeCount, v.channelUrl ?? null, v.lastSeenAt, v.youtubeId);
    return 'updated';
  }
  d.prepare(`UPDATE videos SET last_seen_at = ? WHERE youtube_id = ?`).run(v.lastSeenAt, v.youtubeId);
  return 'updated';
}

export function getVideo(id: string): VideoRecord | null {
  const r = getDb().prepare(`SELECT ${COLS} FROM videos WHERE youtube_id = ?`).get(id) as
    | Record<string, unknown>
    | undefined;
  return r ? rowToVideo(r) : null;
}

export function countVideos(): number {
  const r = getDb().prepare(`SELECT COUNT(*) AS c FROM videos`).get() as { c: number };
  return Number(r.c);
}

export function trending(limit: number): VideoRecord[] {
  const rows = getDb()
    .prepare(`SELECT ${COLS} FROM videos ORDER BY view_count DESC LIMIT ?`)
    .all(limit) as Record<string, unknown>[];
  return rows.map(rowToVideo);
}

/** Zenginleştirme adayları — like'ı olmayanlar (getInfo her seferinde çeker, kategori anahtarı güvenilmez). */
export function recentForEnrich(limit: number): VideoRecord[] {
  const rows = getDb()
    .prepare(
      `SELECT ${COLS} FROM videos
       WHERE like_count IS NULL
       ORDER BY last_seen_at DESC LIMIT ?`,
    )
    .all(limit) as Record<string, unknown>[];
  return rows.map(rowToVideo);
}

/** Zenginleştirilmiş detayları yaz — mevcut/dolu alanlar KORUNUR, yalnız eksikler dolar. */
export function updateVideoDetail(v: VideoRecord): void {
  getDb()
    .prepare(
      `UPDATE videos SET
         description    = CASE WHEN description = '' THEN ? ELSE description END,
         like_count     = COALESCE(like_count, ?),
         category       = COALESCE(category, ?),
         tags           = CASE WHEN tags = '' THEN ? ELSE tags END,
         duration_sec   = CASE WHEN duration_sec = 0 THEN ? ELSE duration_sec END,
         channel_id     = CASE WHEN channel_id = '' THEN ? ELSE channel_id END,
         channel_name   = CASE WHEN channel_name = '' THEN ? ELSE channel_name END,
         channel_url    = CASE WHEN channel_url IS NULL THEN ? ELSE channel_url END,
         view_count     = CASE WHEN view_count = 0 THEN ? ELSE view_count END,
         thumbnail_url  = COALESCE(?, thumbnail_url),
         published_at   = CASE WHEN published_at IS NULL OR published_at NOT LIKE '____-__-__%'
                               THEN COALESCE(?, published_at) ELSE published_at END,
         last_seen_at   = ?
       WHERE youtube_id = ?`,
    )
    .run(
      v.description, v.likeCount, v.category, v.tags, v.durationSec,
      v.channelId, v.channelName, v.channelUrl ?? null, v.viewCount,
      v.thumbnailUrl, v.publishedAt, v.lastSeenAt, v.youtubeId,
    );
}

export function upsertChannel(ch: { id: string; name: string; subs: number | null }): void {
  getDb()
    .prepare(
      `INSERT INTO channels(channel_id, name, subs, fetched_at) VALUES (?,?,?,?)
       ON CONFLICT(channel_id) DO UPDATE SET
         name = CASE WHEN excluded.name != '' THEN excluded.name ELSE channels.name END,
         subs = COALESCE(excluded.subs, channels.subs),
         fetched_at = excluded.fetched_at`,
    )
    .run(ch.id, ch.name, ch.subs, new Date().toISOString());
}

/* ---------- FTS arama (FTS5 SQL injection'a kapalıdır; terimler quote edilir) ---------- */

function toFtsQuery(q: string): string {
  const terms = q
    .replace(/["']/g, ' ')
    .split(/\s+/)
    .filter((t) => t.length > 0)
    .map((t) => `"${t.replaceAll('*', '')}"`);
  return terms.join(' OR ');
}

export interface FtsOptions {
  limit: number;
  offset: number;
  sortBy?: 'relevance' | 'views' | 'date' | 'rating';
}

export function ftsSearch(query: string, opts: FtsOptions): VideoRecord[] {
  const match = toFtsQuery(query);
  if (!match) return [];
  const d = getDb();
  const order =
    opts.sortBy === 'views' ? 'v.view_count DESC'
    : opts.sortBy === 'date' ? 'v.published_at DESC'
    : 'rank';
  const rows = d
    .prepare(
      `SELECT ${COLS.split(', ').map((c) => `v.${c}`).join(', ')}
       FROM videos_fts f JOIN videos v ON v.rowid = f.rowid
       WHERE videos_fts MATCH ?
       ORDER BY ${order}
       LIMIT ? OFFSET ?`,
    )
    .all(match, opts.limit, opts.offset) as Record<string, unknown>[];
  return rows.map(rowToVideo);
}

/* ---------- History / AIQueries ---------- */

export function saveHistory(query: string, resultCount: number): void {
  getDb()
    .prepare(`INSERT INTO search_history(query, result_count, searched_at) VALUES (?,?,?)`)
    .run(query, resultCount, new Date().toISOString());
}

export function suggest(prefix: string, limit = 8): string[] {
  const rows = getDb()
    .prepare(
      `SELECT query, COUNT(*) AS c FROM search_history
       WHERE query LIKE ? GROUP BY query ORDER BY c DESC LIMIT ?`,
    )
    .all(`${prefix}%`, limit) as { query: string }[];
  return rows.map((r) => r.query);
}

export function saveAiQuery(raw: string, expanded: string[]): void {
  getDb()
    .prepare(`INSERT INTO ai_queries(raw_query, expanded_json, created_at) VALUES (?,?,?)`)
    .run(raw, JSON.stringify(expanded), new Date().toISOString());
}

/* ---------- Jobs (kuyruk) ---------- */

export function enqueueJob(type: JobType, payload: unknown): number {
  const now = new Date().toISOString();
  const r = getDb()
    .prepare(`INSERT INTO jobs(type, payload, status, attempts, created_at, updated_at) VALUES (?,?,'pending',0,?,?)`)
    .run(type, JSON.stringify(payload), now, now);
  return Number(r.lastInsertRowid);
}

export function nextJobs(limit = 5): JobRow[] {
  const rows = getDb()
    .prepare(`SELECT id, type, payload, status, attempts, created_at AS createdAt FROM jobs WHERE status='pending' ORDER BY id LIMIT ?`)
    .all(limit) as unknown as JobRow[];
  return rows;
}

export function completeJob(id: number, ok: boolean): void {
  getDb()
    .prepare(`UPDATE jobs SET status = ?, attempts = attempts + 1, updated_at = ? WHERE id = ?`)
    .run(ok ? 'done' : 'failed', new Date().toISOString(), id);
}
