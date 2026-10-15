/** Video Detail Orchestrator — video + yorumlar + benzer videolar (AI skorlu); üç yüzey (API/CLI/Web) paylaşır. */
import { getVideo, upsertVideo, countVideos } from '../database/db.ts';
import { youtubeInfo, youtubeComments, youtubeRelated } from '../providers/youtube.ts';
import { scoreResults, aiAvailable } from '../ai/engine.ts';
import { DiskCache, cacheKey } from '../cache/disk-cache.ts';
import { config } from '../config.ts';
import type {
  CommentRecord, RelatedVideo, VideoDetailOptions, VideoDetailPayload, VideoRecord,
} from './types.ts';

/** Yorum/benzer verisi hızlı değişir →24 saat TTL (arama cache'i30 gün). */
const detailCache = new DiskCache(24 * 60 * 60_000);

const ID_RE = /^[A-Za-z0-9_-]{11}$/;

/** ID veya izleme URL'si →11 karakter video ID (bulunamazsa null). */
export function extractVideoId(input: string): string | null {
  const s = input.trim();
  if (ID_RE.test(s)) return s;
  try {
    const u = new URL(s);
    if (u.hostname === 'youtu.be') {
      const id = u.pathname.slice(1).split('/')[0] ?? '';
      return ID_RE.test(id) ? id : null;
    }
    const v = u.searchParams.get('v');
    if (v && ID_RE.test(v)) return v;
    const m = u.pathname.match(/^\/(?:shorts|embed|live|v)\/([A-Za-z0-9_-]{11})/);
    return m ? m[1] : null;
  } catch {
    return null;
  }
}

function withUrl(v: VideoRecord): VideoRecord & { url: string } {
  return { ...v, url: `https://www.youtube.com/watch?v=${v.youtubeId}` };
}

/**
 * Video detayı — fail-safe zinciri:
 * · video: DB → (değilse, online ise) getInfo → yoksa `video: null` (diğer diziler yine dolabilir)
 * · yorumlar/benzer: DiskCache → YouTube → hata/yoksa boş dizi + `degraded` (arama asla düşmez)
 * · AI benzerlik skoru: yalnız `ai` + key varsa; fail-safe → skorusuz devam (D-003: üst sınır8 aday)
 */
export async function videoDetail(
  input: string,
  o: VideoDetailOptions,
): Promise<VideoDetailPayload | null> {
  const t0 = Date.now();
  const videoId = extractVideoId(input);
  if (!videoId) return null;
  const offline = o.offline || config.offline;

  // 1) video kaydı
  let video = getVideo(videoId);
  let source: VideoDetailPayload['meta']['source'] = video ? 'db' : 'miss';
  if (!video && !offline) {
    try {
      const fresh = await youtubeInfo(videoId);
      if (fresh) {
        upsertVideo(fresh);
        video = getVideo(videoId) ?? fresh;
        source = 'youtube';
      }
    } catch {
      // fail-safe: aşağıda `video: null` ile döner
    }
  }

  let degraded = false;

  // 2) yorumlar
  let comments: CommentRecord[] = [];
  let commentsFromCache = false;
  if (o.comments > 0) {
    const key = cacheKey(['v1', 'comments', videoId, Math.min(o.comments, 50)]);
    const cached = detailCache.get<CommentRecord[]>(key);
    if (cached) {
      comments = cached;
      commentsFromCache = true;
    } else if (!offline) {
      try {
        comments = await youtubeComments(videoId, Math.min(o.comments, 50));
        detailCache.set(key, comments);
      } catch {
        degraded = true;
      }
    }
  }

  // 3) benzer videolar
  let related: RelatedVideo[] = [];
  let relatedFromCache = false;
  if (o.related > 0) {
    const key = cacheKey(['v1', 'related', videoId, Math.min(o.related, 20), o.ai ? 'ai' : 'noai']);
    const cached = detailCache.get<RelatedVideo[]>(key);
    if (cached) {
      related = cached;
      relatedFromCache = true;
    } else if (!offline) {
      try {
        related = await youtubeRelated(videoId, Math.min(o.related, 20));
        // AI öncesi ham hali önbelleğe alınır (skor her açılışta taze uygulanır)
        detailCache.set(key, related);
      } catch {
        degraded = true;
      }
    }
  }

  // 4) AI benzerlik skoru — parent başlık "sorgu", benzerler "aday" (scoreResults yeniden kullanımı)
  const aiEnabled = o.ai && aiAvailable();
  let aiScored = 0;
  if (aiEnabled && related.length) {
    const top = related.slice(0, 8); // D-003: skor üst sınırı8 aday
    const parentTitle = video?.title ?? videoId;
    const scores = await scoreResults(
      `Referans video ile benzerlik: ${parentTitle}`,
      top.map((r) => ({
        id: r.youtubeId, title: r.title, channel: r.channelName,
        views: r.viewCount, duration: 0, published: null,
      })),
    ).catch(() => null);
    if (scores) {
      for (const r of top) {
        const s = scores[r.youtubeId];
        if (s) {
          r.aiScore = Math.max(0, Math.min(100, Math.round(s.score)));
          r.aiReason = s.reason;
          aiScored++;
        }
      }
    }
  }

  return {
    video: video ? withUrl(video) : null,
    comments,
    related,
    meta: {
      videoId,
      source,
      commentsRequested: o.comments,
      relatedRequested: o.related,
      commentsCount: comments.length,
      relatedCount: related.length,
      commentsFromCache,
      relatedFromCache,
      aiEnabled,
      aiScored,
      offline,
      degraded,
      tookMs: Date.now() - t0,
      dbVideos: countVideos(),
    },
  };
}
