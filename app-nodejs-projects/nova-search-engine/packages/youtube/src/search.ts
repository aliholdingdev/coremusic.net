// ===========================================
// Search — youtubei.js arama + scroll (continuation)
// Temel: ilk aramada geniş overscan, sonra hep yerel.
// ===========================================

import type { SearchQuery, Video } from '@novasearch/common';
import { clamp } from '@novasearch/common';
import { getYouTubeClient } from './client.js';
import { mapToVideo, parseRelativeDate, parseViews, textOf, watchUrl } from './mapper.js';
import { detectLanguage, spamScore } from '@novasearch/common';

/* eslint-disable @typescript-eslint/no-explicit-any */

function buildFilters(q: SearchQuery): Record<string, unknown> {
  const filters: Record<string, unknown> = {};
  if (q.type === 'video' || q.filter !== 'all') filters['type'] = 'video';
  if (q.type === 'channel') filters['type'] = 'channel';
  if (q.type === 'playlist') filters['type'] = 'playlist';
  if (q.uploadDate !== 'all') filters['upload_date'] = q.uploadDate;
  if (q.duration !== 'all') filters['duration'] = q.duration;
  return filters;
}

/**
 * YouTube'da arama yap; `scroll` adet sonuç için continuation ile devam et.
 * NOT: bu fonksiyon yalnız yerelde yoksa çağrılır (search motoru karar verir).
 */
export async function searchYouTube(query: string, q: SearchQuery): Promise<Video[]> {
  const wanted = clamp(q.scroll, 1, 500);
  const videos: Video[] = [];
  const seen = new Set<string>();

  const client = getYouTubeClient();
  const first = await client.call((yt) => yt.search(query, buildFilters(q) as never));

  const pushResults = (nodes: any[]) => {
    for (const node of nodes) {
      const v = mapToVideo(node);
      if (v && !seen.has(v.youtubeId)) {
        seen.add(v.youtubeId);
        videos.push(v);
      }
    }
  };

  pushResults([...(first as any).videos]);

  // Scroll: continuation ile devam ("max sınır yok" — 50 sayfa güvenlik tavanı ≈ 1000+ video)
  let page = 0;
  let current: any = first;
  while (videos.length < wanted && current?.has_continuation && page < 50) {
    current = await client.call(() => current.getContinuation());
    pushResults([...(current as any).videos]);
    page++;
  }

  // İçerik tipi süzgeci (shorts/song/video) — yerel uygulanır
  let filtered = videos;
  if (q.filter === 'sh') filtered = filtered.filter((v) => v.isShort);
  if (q.filter === 'v') filtered = filtered.filter((v) => !v.isShort);
  if (q.filter === 's') {
    filtered = filtered.filter((v) => /audio|lyric|şarkı|song|official music/i.test(v.title));
  }

  return filtered.slice(0, wanted);
}

/** Arama önerileri (autocomplete) — kullanıcı deneyimi için. */
export async function getSearchSuggestions(query: string): Promise<string[]> {
  const client = getYouTubeClient();
  return client.call((yt) => yt.getSearchSuggestions(query));
}

/** Tek video bilgisi (detay ekranı için) — tam metadata kazıma. */
export async function getVideoDetails(videoId: string): Promise<Video | null> {
  const client = getYouTubeClient();
  return client.call(async (yt) => {
    const info = await yt.getInfo(videoId);
    const basic = (info as any).basic_info ?? {};
    const primary = (info as any).primary_info ?? {};
    const secondary = (info as any).secondary_info ?? {};
    const captions = (info as any).captions ?? {};

    const publishedText = textOf(primary?.published) || textOf(primary?.relative_date);
    const thumbs = ((basic?.thumbnail ?? []) as Array<{ url?: string }>)
      .map((t) => (t?.url ?? '').startsWith('//') ? `https:${t.url}` : (t?.url ?? ''))
      .filter((u) => u.length > 0);
    const badges = (Array.isArray(primary?.badges) ? primary.badges : [])
      .map((b: any) => textOf(b?.label ?? b?.text ?? b?.tooltip))
      .filter((s: string) => s.length > 0);

    const publishedAt = parseRelativeDate(publishedText);

    // İzlenme metni (VideoViewCount nesnesinden güvenli çekim)
    const viewCountCandidate = textOf(primary?.view_count?.text ?? primary?.view_count?.views) ||
      textOf(primary?.view_count);
    const viewCountText = viewCountCandidate && viewCountCandidate !== '[object Object]'
      ? viewCountCandidate
      : undefined;

    // Kanal sahibi (secondary_info.owner — probe ile doğrulandı)
    const owner = secondary?.owner ?? {};
    const subscriberText = textOf(owner?.subscriber_count) || textOf(owner?.author?.subscriber_count);
    const verified = owner?.author?.is_verified === true;

    // Altyazı izleri: dil + URL
    const captionTracks = (Array.isArray(captions?.caption_tracks) ? captions.caption_tracks : [])
      .map((t: any) => ({
        language: String(t?.language_code ?? ''),
        name: textOf(t?.name) || String(t?.language_code ?? ''),
        url: String(t?.base_url ?? ''),
        auto: t?.kind === 'asr',
      }))
      .filter((t: { language: string }) => t.language.length > 0);

    // İlk yorumlar (top 5) — hata sessizce yutulur
    let topComments: Array<{ author: string; text: string; likes: number }> | undefined;
    let commentCount: number | undefined;
    try {
      const comments = await yt.getComments(videoId, 'TOP_COMMENTS');
      const threads = [...((comments as any).contents ?? [])];
      topComments = [];
      for (const th of threads.slice(0, 5)) {
        const c = th?.comment ?? th;
        const text = textOf(c?.content);
        if (!text) continue;
        topComments.push({
          author: textOf(c?.author?.name) || '—',
          text: text.slice(0, 300),
          likes: parseViews(textOf(c?.like_count)),
        });
      }
      const headerCount = textOf((comments as any).header?.comments_count);
      commentCount = parseViews(headerCount) || undefined;
      if (topComments.length === 0) topComments = undefined;
    } catch {
      // yorumlar kapalı/engelli olabilir
    }

    // İlgili videolar (watch_next_feed — probe ile doğrulandı) — savunmacı tarama
    const relatedIds = collectRelatedIds(
      (info as any).watch_next_feed ?? (info as any).watch_next_response,
      20,
    );

    return {
      youtubeId: String(basic.id ?? videoId),
      title: String(basic.title ?? ''),
      description: String(basic.short_description ?? textOf(secondary?.description?.text ?? secondary?.description)),
      channelId: String(basic.channel_id ?? ''),
      channelName: String(basic.author ?? textOf(owner?.author?.name)),
      durationSeconds: Number(basic.duration ?? 0),
      viewCount: Number(basic.view_count ?? 0),
      likeCount: Number((info as any).like_count ?? (collectLikeCount(primary) || 0)),
      publishedAt,
      thumbnailUrl: thumbs[0] ?? '',
      tags: Array.isArray(basic.keywords) ? basic.keywords.map(String) : [],
      isShort: Number(basic.duration ?? 0) > 0 && Number(basic.duration ?? 0) <= 60,
      isLive: Boolean(basic.is_live ?? basic.is_live_content),
      qualityScore: 0,
      source: 'youtube',

      // Zengin alanlar
      url: watchUrl(String(basic.id ?? videoId)),
      channelUrl: basic.channel_id ? `https://www.youtube.com/channel/${basic.channel_id}` : undefined,
      viewCountText,
      publishedText: publishedText || undefined,
      thumbnails: thumbs.length > 0 ? thumbs : undefined,
      badges: badges.length > 0 ? badges : undefined,
      isUnlisted: basic.is_private === true ? true : undefined,
      isCrawlable: typeof basic.is_crawlable === 'boolean' ? basic.is_crawlable : undefined,
      channelSubscriberCount: parseViews(subscriberText) || undefined,
      channelVerified: verified ? true : undefined,
      captionTracks: captionTracks.length > 0 ? captionTracks : undefined,
      topComments,
      commentCount,
      relatedIds: relatedIds.length > 0 ? relatedIds : undefined,
      language: detectLanguage(String(basic.title ?? ''), String(basic.short_description ?? '')),
      spamScore: spamScore(String(basic.title ?? ''), String(basic.short_description ?? '')),
    } satisfies Video;
  });
}

/** watch_next yanıtından video kimliklerini topla (derin, savunmacı tarama). */
function collectRelatedIds(root: unknown, max: number): string[] {
  const seen = new Set<string>();
  const walk = (node: unknown, depth: number) => {
    if (!node || depth > 8 || seen.size >= max) return;
    if (Array.isArray(node)) {
      for (const item of node) walk(item, depth + 1);
      return;
    }
    if (typeof node === 'object') {
      const rec = node as Record<string, unknown>;
      const id = rec['videoId'] ?? rec['video_id'];
      if (typeof id === 'string' && /^[A-Za-z0-9_-]{6,20}$/.test(id)) seen.add(id);
      for (const value of Object.values(rec)) walk(value, depth + 1);
    }
  };
  walk(root, 0);
  return [...seen].slice(0, max);
}

/**
 * Beğeni sayısı (SegmentedLikeDislikeButtonView.like_count —
 * probe ile doğrulandı: primary_info.menu.top_level_buttons içinde).
 * Savunmacı tarama; bulunamazsa 0.
 */
export function collectLikeCount(root: unknown, depth = 0): number {
  if (!root || depth > 7) return 0;
  if (Array.isArray(root)) {
    for (const item of root) {
      const n = collectLikeCount(item, depth + 1);
      if (n > 0) return n;
    }
    return 0;
  }
  if (typeof root === 'object') {
    for (const [key, value] of Object.entries(root as Record<string, unknown>)) {
      if ((key === 'like_count' || key === 'short_like_count') && typeof value === 'number' && value > 0) {
        return value;
      }
    }
    for (const value of Object.values(root as Record<string, unknown>)) {
      const n = collectLikeCount(value, depth + 1);
      if (n > 0) return n;
    }
  }
  return 0;
}

/* eslint-enable @typescript-eslint/no-explicit-any */
