// ===========================================
// Ranking — saf süzme/sıralama fonksiyonları
// ===========================================

import type { EnrichedVideo, SearchQuery, Video } from '@novasearch/common';
import { QUALITY_THRESHOLDS, clamp } from '@novasearch/common';
import type { ScoredVideo } from '@novasearch/ai';

/** Kullanıcı süzgeçlerini uygula (views/duration/quality/score). */
export function applyFilters(videos: ScoredVideo[], q: SearchQuery): ScoredVideo[] {
  return videos.filter((v) => {
    if (q.minViews > 0 && v.viewCount < q.minViews) return false;
    if (q.maxViews > 0 && v.viewCount > q.maxViews) return false;
    if (q.minScore > 0 && v.score < q.minScore) return false;
    if (q.duration === 'short' && !(v.durationSeconds > 0 && v.durationSeconds < 300)) return false;
    if (q.duration === 'medium' && !(v.durationSeconds >= 300 && v.durationSeconds <= 1200)) return false;
    if (q.duration === 'long' && !(v.durationSeconds > 1200)) return false;
    if (q.quality === 'high' && v.score < QUALITY_THRESHOLDS.high) return false;
    if (q.quality === 'medium' && v.score < QUALITY_THRESHOLDS.medium) return false;
    return true;
  });
}

/** Sayfalama: page/limit uygula. */
export function paginate<T>(items: T[], q: SearchQuery): T[] {
  const pageSize = clamp(q.limit || q.result, 1, 1000);
  const page = Math.max(1, q.page);
  return items.slice((page - 1) * pageSize, page * pageSize);
}

/** sortBy seçeneğine göre sırala (skor, tarih, izlenme, puan). */
export function sortVideos(videos: ScoredVideo[], q: SearchQuery): ScoredVideo[] {
  const sorted = [...videos];
  switch (q.sortBy) {
    case 'view_count':
      sorted.sort((a, b) => b.viewCount - a.viewCount);
      break;
    case 'upload_date':
      sorted.sort((a, b) => (b.publishedAt ?? '').localeCompare(a.publishedAt ?? ''));
      break;
    case 'rating':
      sorted.sort((a, b) => b.likeCount - a.likeCount);
      break;
    default:
      sorted.sort((a, b) => b.score - a.score);
  }
  return sorted;
}

/** Zenginleştirilmiş sonuçlar — skor/neden bilgisi korunur (API derin veri). */
export function toEnriched(scored: ScoredVideo[]): EnrichedVideo[] {
  return scored.map(({ score, matchedTerms, reasons, ...video }) => ({
    ...video,
    score,
    matchedTerms,
    reasons,
  }));
}
