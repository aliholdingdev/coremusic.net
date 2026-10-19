// ===========================================
// Collector Worker — metadata toplama + normalize
// + dedup + DB yazma. Arka planda çalışır.
// ===========================================

import type { Database } from '@novasearch/database';
import type { SearchQuery, Video } from '@novasearch/common';
import { searchYouTube, getVideoDetails, getChannelDetails } from '@novasearch/youtube';
import { JobQueue } from './queue.js';
import { TokenBucket } from './rate-limiter.js';

export type CollectPayload =
  | { kind: 'search'; query: string; options: SearchQuery }
  | { kind: 'video'; videoId: string };

export class CollectorWorker {
  private queue: JobQueue<CollectPayload>;
  private bucket: TokenBucket;

  constructor(private db: Database, concurrency = 2) {
    this.bucket = new TokenBucket(concurrency, 12);
    this.queue = new JobQueue<CollectPayload>((payload) => this.handle(payload), concurrency);
  }

  start(): void {
    this.queue.start();
  }

  stop(): void {
    this.queue.stop();
  }

  enqueue(payload: CollectPayload): number {
    return this.queue.push(payload);
  }

  stats() {
    return this.queue.stats();
  }

  /** Senkron toplama (CLI --collect ve arama motoru yetersiz veride çağırır). */
  async collectNow(query: string, options: SearchQuery): Promise<Video[]> {
    await this.bucket.take();
    const videos = await searchYouTube(query, options);
    await this.persist(videos);
    return videos;
  }

  /**
   * Metadata Refresh Engine — bayat kayıtları tazele.
   * Politika (prompt: "her veri aynı sıklıkta güncellenmez"):
   * taze çekimdeki tüm zengin alanlar yazılır; fresh'te boş gelen
   * kritik alanlar eski değerle korunur (kayıp olmasın).
   */
  async refreshStale(maxAgeDays = 7, limit = 50): Promise<number> {
    const cutoff = new Date(Date.now() - maxAgeDays * 86_400_000).toISOString();
    const stale = await this.db.videos.staleVideos(cutoff, limit);
    let updated = 0;
    for (const v of stale) {
      await this.bucket.take();
      try {
        const fresh = await getVideoDetails(v.youtubeId);
        if (!fresh) continue;
        await this.db.videos.upsertMany([
          {
            ...v,
            ...fresh,
            // fresh boş dönen kritik alanlar eskisiyle korunur
            channelId: fresh.channelId || v.channelId,
            channelName: fresh.channelName || v.channelName,
            description: fresh.description || v.description,
            thumbnailUrl: fresh.thumbnailUrl || v.thumbnailUrl,
            publishedAt: fresh.publishedAt ?? v.publishedAt,
            publishedText: fresh.publishedText ?? v.publishedText,
            tags: fresh.tags.length > 0 ? fresh.tags : v.tags,
            likeCount: fresh.likeCount || v.likeCount,
            commentCount: fresh.commentCount ?? v.commentCount,
            thumbnails: fresh.thumbnails ?? v.thumbnails,
            badges: fresh.badges ?? v.badges,
            captionTracks: fresh.captionTracks ?? v.captionTracks,
            topComments: fresh.topComments ?? v.topComments,
            relatedIds: fresh.relatedIds ?? v.relatedIds,
            channelSubscriberCount: fresh.channelSubscriberCount ?? v.channelSubscriberCount,
            channelVerified: fresh.channelVerified ?? v.channelVerified,
          },
        ]);
        updated++;
      } catch {
        // tekil tazeleme hatası döngüyü durdurmaz
      }
    }
    return updated;
  }

  private async handle(payload: CollectPayload): Promise<void> {
    await this.bucket.take();
    if (payload.kind === 'search') {
      const videos = await searchYouTube(payload.query, payload.options);
      await this.persist(videos);
    } else {
      const video = await getVideoDetails(payload.videoId);
      if (video) {
        await this.persist([video]);
        // Kanal detayını da kaz (abone/doğrulama/açıklama)
        if (video.channelId) {
          await this.bucket.take();
          try {
            const channel = await getChannelDetails(video.channelId);
            if (channel) await this.db.channels.upsert(channel);
          } catch {
            // kanal çekimi tekil hatası işi durdurmaz
          }
        }
      }
    }
  }

  /** Normalize veri → dedup upsert (aynı kayıt 2. kez yazılmaz). */
  private async persist(videos: Video[]): Promise<void> {
    if (videos.length === 0) return;
    await this.db.videos.upsertMany(videos);
  }
}
