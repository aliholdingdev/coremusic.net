// ===========================================
// Repository Katmanı — Video / Channel / SearchLog
// Dedup kuralı: youtube_id tekildir; tekrar gelen
// kayıt yeniden yazılmaz, yalnız değişen alanlar
// güncellenir (prompt: "aynı veri 2. kez kaydedilmez").
// ===========================================

import type { DbAdapter } from './adapter.js';
import { nowIso } from './adapter.js';
import type { Channel, Video } from '@novasearch/common';
import { detectLanguage, spamScore } from '@novasearch/common';

interface VideoRow {
  youtube_id: string;
  channel_youtube_id: string | null;
  channel_name: string | null;
  title: string;
  description: string | null;
  duration_seconds: number;
  view_count: number;
  like_count: number;
  comment_count: number;
  published_at: string | null;
  published_text: string | null;
  category: string | null;
  thumbnail_url: string | null;
  tags: string | null;
  thumbnails: string | null;
  badges: string | null;
  is_short: number;
  is_live: number;
  is_family_safe: number;
  is_unlisted: number;
  is_crawlable: number;
  channel_subscriber_count: number;
  channel_verified: number;
  view_count_text: string | null;
  caption_tracks: string | null;
  top_comments: string | null;
  related_ids: string | null;
  language: string | null;
  spam_score: number;
  quality_score: number;
  source: string;
}

function parseJsonArray(text: string | null): string[] | undefined {
  if (!text) return undefined;
  try {
    const arr = JSON.parse(text) as unknown;
    return Array.isArray(arr) ? arr.map(String) : undefined;
  } catch {
    return undefined;
  }
}

function parseJson<T>(text: string | null): T | undefined {
  if (!text) return undefined;
  try {
    return JSON.parse(text) as T;
  } catch {
    return undefined;
  }
}

function rowToVideo(row: VideoRow): Video {
  return {
    youtubeId: row.youtube_id,
    title: row.title,
    description: row.description ?? '',
    channelId: row.channel_youtube_id ?? '',
    channelName: row.channel_name ?? '',
    durationSeconds: Number(row.duration_seconds ?? 0),
    viewCount: Number(row.view_count ?? 0),
    likeCount: Number(row.like_count ?? 0),
    publishedAt: row.published_at,
    thumbnailUrl: row.thumbnail_url ?? '',
    tags: parseJsonArray(row.tags) ?? [],
    isShort: Number(row.is_short) === 1,
    isLive: Number(row.is_live) === 1,
    qualityScore: Number(row.quality_score ?? 0),
    source: 'local',

    // Zengin alanlar
    url: `https://www.youtube.com/watch?v=${row.youtube_id}`,
    channelUrl: row.channel_youtube_id ? `https://www.youtube.com/channel/${row.channel_youtube_id}` : undefined,
    viewCountText: row.view_count_text ?? undefined,
    publishedText: row.published_text ?? undefined,
    commentCount: Number(row.comment_count ?? 0),
    category: row.category ?? undefined,
    thumbnails: parseJsonArray(row.thumbnails),
    badges: parseJsonArray(row.badges),
    isFamilySafe: Number(row.is_family_safe ?? 1) === 1,
    isUnlisted: Number(row.is_unlisted ?? 0) === 1,
    isCrawlable: Number(row.is_crawlable ?? 1) === 1,
    channelSubscriberCount: Number(row.channel_subscriber_count ?? 0),
    channelVerified: Number(row.channel_verified ?? 0) === 1,
    captionTracks: parseJson(row.caption_tracks),
    topComments: parseJson(row.top_comments),
    relatedIds: parseJsonArray(row.related_ids),
    // Sinyaller saf fonksiyondur; kolon boşsa okuma anında hesaplanır
    // (eski kayıtlar anında zenginleşir).
    language: (row.language as Video['language']) ?? detectLanguage(row.title, row.description ?? ''),
    spamScore: Number(row.spam_score ?? 0) || spamScore(row.title, row.description ?? ''),
  };
}

export class VideoRepo {
  constructor(private db: DbAdapter) {}

  /** Dedup upsert: aynı youtube_id tekrar gelirse yalnız güncellenir. */
  async upsertMany(videos: Video[]): Promise<number> {
    if (videos.length === 0) return 0;
    const now = nowIso();
    const cols = [
      'youtube_id', 'channel_youtube_id', 'channel_name', 'title', 'description', 'duration_seconds',
      'view_count', 'like_count', 'comment_count', 'view_count_text', 'published_at', 'published_text', 'category',
      'thumbnail_url', 'tags', 'thumbnails', 'badges', 'caption_tracks', 'top_comments', 'related_ids',
      'is_short', 'is_live', 'is_family_safe', 'is_unlisted', 'is_crawlable',
      'channel_subscriber_count', 'channel_verified',
      'language', 'spam_score',
      'quality_score', 'source', 'created_at', 'updated_at',
    ];
    const setClause =
      this.db.dialect === 'sqlite'
        ? `ON CONFLICT(youtube_id) DO UPDATE SET
             title=excluded.title, description=excluded.description,
             duration_seconds=excluded.duration_seconds, view_count=excluded.view_count,
             like_count=excluded.like_count, comment_count=excluded.comment_count,
             view_count_text=excluded.view_count_text,
             published_at=excluded.published_at, published_text=excluded.published_text,
             category=excluded.category,
             thumbnail_url=excluded.thumbnail_url, tags=excluded.tags,
             thumbnails=excluded.thumbnails, badges=excluded.badges,
             caption_tracks=excluded.caption_tracks, top_comments=excluded.top_comments,
             related_ids=excluded.related_ids,
             is_short=excluded.is_short, is_live=excluded.is_live,
             is_family_safe=excluded.is_family_safe, is_unlisted=excluded.is_unlisted,
             is_crawlable=excluded.is_crawlable,
             channel_subscriber_count=excluded.channel_subscriber_count,
             channel_verified=excluded.channel_verified,
             channel_name=excluded.channel_name,
             language=excluded.language, spam_score=excluded.spam_score,
             quality_score=excluded.quality_score, updated_at=excluded.updated_at`
        : `ON DUPLICATE KEY UPDATE
             title=VALUES(title), description=VALUES(description),
             duration_seconds=VALUES(duration_seconds), view_count=VALUES(view_count),
             like_count=VALUES(like_count), comment_count=VALUES(comment_count),
             view_count_text=VALUES(view_count_text),
             published_at=VALUES(published_at), published_text=VALUES(published_text),
             category=VALUES(category),
             thumbnail_url=VALUES(thumbnail_url), tags=VALUES(tags),
             thumbnails=VALUES(thumbnails), badges=VALUES(badges),
             caption_tracks=VALUES(caption_tracks), top_comments=VALUES(top_comments),
             related_ids=VALUES(related_ids),
             is_short=VALUES(is_short), is_live=VALUES(is_live),
             is_family_safe=VALUES(is_family_safe), is_unlisted=VALUES(is_unlisted),
             is_crawlable=VALUES(is_crawlable),
             channel_subscriber_count=VALUES(channel_subscriber_count),
             channel_verified=VALUES(channel_verified),
             channel_name=VALUES(channel_name),
             language=VALUES(language), spam_score=VALUES(spam_score),
             quality_score=VALUES(quality_score), updated_at=VALUES(updated_at)`;

    const placeholders = `(${cols.map(() => '?').join(',')})`;
    const sql = `INSERT INTO videos (${cols.join(',')}) VALUES ${placeholders} ${setClause}`;
    let written = 0;
    await this.db.transaction(async (tx) => {
      for (const v of videos) {
        await tx.run(sql, [
          v.youtubeId, v.channelId || null, v.channelName || null, v.title, v.description,
          v.durationSeconds, v.viewCount, v.likeCount, v.commentCount ?? 0,
          v.viewCountText ?? null, v.publishedAt, v.publishedText ?? null, v.category ?? null,
          v.thumbnailUrl, JSON.stringify(v.tags),
          v.thumbnails ? JSON.stringify(v.thumbnails) : null,
          v.badges ? JSON.stringify(v.badges) : null,
          v.captionTracks ? JSON.stringify(v.captionTracks) : null,
          v.topComments ? JSON.stringify(v.topComments) : null,
          v.relatedIds ? JSON.stringify(v.relatedIds) : null,
          v.isShort ? 1 : 0, v.isLive ? 1 : 0,
          v.isFamilySafe === false ? 0 : 1,
          v.isUnlisted === true ? 1 : 0,
          v.isCrawlable === false ? 0 : 1,
          v.channelSubscriberCount ?? 0,
          v.channelVerified === true ? 1 : 0,
          v.language ?? null,
          v.spamScore ?? 0,
          v.qualityScore, v.source,
          now, now,
        ]);
        written++;
      }
    });
    return written;
  }

  async findByYoutubeId(youtubeId: string): Promise<Video | undefined> {
    const row = await this.db.get<VideoRow>('SELECT * FROM videos WHERE youtube_id = ?', [youtubeId]);
    return row ? rowToVideo(row) : undefined;
  }

  /** Yerel arama: terimlerin LIKE ile eşleşmesi (sıralama search paketinde). */
  async searchLocal(terms: string[], limit: number): Promise<Video[]> {
    if (terms.length === 0) return [];
    const likes = terms.map(() => '(title LIKE ? OR description LIKE ? OR tags LIKE ?)').join(' OR ');
    const params: unknown[] = [];
    for (const t of terms) {
      const like = `%${t}%`;
      params.push(like, like, like);
    }
    const rows = await this.db.all<VideoRow>(
      `SELECT * FROM videos WHERE ${likes} ORDER BY view_count DESC LIMIT ?`,
      [...params, limit],
    );
    return rows.map(rowToVideo);
  }

  /** Tazeleme için bayat kayıtlar (updated_at < kesim tarihi). */
  async staleVideos(cutoffIso: string, limit: number): Promise<Video[]> {
    const rows = await this.db.all<VideoRow>(
      'SELECT * FROM videos WHERE updated_at < ? ORDER BY view_count DESC LIMIT ?',
      [cutoffIso, limit],
    );
    return rows.map(rowToVideo);
  }

  async count(): Promise<number> {
    const row = await this.db.get<{ n: number }>('SELECT COUNT(*) AS n FROM videos');
    return Number(row?.n ?? 0);
  }

  async topViewed(limit: number): Promise<Video[]> {
    const rows = await this.db.all<VideoRow>(
      'SELECT * FROM videos ORDER BY view_count DESC LIMIT ?',
      [limit],
    );
    return rows.map(rowToVideo);
  }

  async recent(limit: number): Promise<Video[]> {
    const rows = await this.db.all<VideoRow>(
      'SELECT * FROM videos ORDER BY created_at DESC LIMIT ?',
      [limit],
    );
    return rows.map(rowToVideo);
  }
}

export class ChannelRepo {
  constructor(private db: DbAdapter) {}

  async upsert(channel: Channel): Promise<void> {
    const now = nowIso();
    const sql =
      this.db.dialect === 'sqlite'
        ? `INSERT INTO channels (youtube_id, name, description, subscriber_count, video_count, thumbnail_url, verified, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?)
           ON CONFLICT(youtube_id) DO UPDATE SET
             name=excluded.name, description=excluded.description,
             subscriber_count=excluded.subscriber_count, video_count=excluded.video_count,
             thumbnail_url=excluded.thumbnail_url, verified=excluded.verified, updated_at=excluded.updated_at`
        : `INSERT INTO channels (youtube_id, name, description, subscriber_count, video_count, thumbnail_url, verified, created_at, updated_at)
           VALUES (?,?,?,?,?,?,?,?,?)
           ON DUPLICATE KEY UPDATE
             name=VALUES(name), description=VALUES(description),
             subscriber_count=VALUES(subscriber_count), video_count=VALUES(video_count),
             thumbnail_url=VALUES(thumbnail_url), verified=VALUES(verified), updated_at=VALUES(updated_at)`;
    await this.db.run(sql, [
      channel.youtubeId, channel.name, channel.description,
      channel.subscriberCount, channel.videoCount, channel.thumbnailUrl,
      channel.verified ? 1 : 0, now, now,
    ]);
  }
}

export class SearchLogRepo {
  constructor(private db: DbAdapter) {}

  async log(query: string, resultsCount: number, source: string, tookMs: number): Promise<void> {
    await this.db.run(
      'INSERT INTO search_log (query, results_count, source, took_ms, created_at) VALUES (?,?,?,?,?)',
      [query, resultsCount, source, tookMs, nowIso()],
    );
  }

  async recent(limit: number): Promise<Array<{ query: string; results_count: number; created_at: string }>> {
    return this.db.all('SELECT query, results_count, created_at FROM search_log ORDER BY created_at DESC LIMIT ?', [limit]);
  }

  async topQueries(limit: number): Promise<Array<{ query: string; n: number }>> {
    return this.db.all('SELECT query, COUNT(*) AS n FROM search_log GROUP BY query ORDER BY n DESC LIMIT ?', [limit]);
  }
}
