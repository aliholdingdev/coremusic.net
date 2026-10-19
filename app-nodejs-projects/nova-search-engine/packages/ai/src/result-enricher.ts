// ===========================================
// Result Enricher — kalite/relevans skoru (0..1)
// Saf fonksiyon: test edilebilir, AI'sız da çalışır.
// Skor: başlık eşleşmesi + izlenme + güncellik + sinyal.
// ===========================================

import type { Video } from '@novasearch/common';
import { detectLanguage, formatViews, spamScore } from '@novasearch/common';
import type { QueryAnalysis } from './query-analyzer.js';

export interface ScoredVideo extends Video {
  score: number;
  matchedTerms: string[];
  reasons: string[];
}

function normalize(text: string): string {
  return text.toLowerCase();
}

/** İzlenme popülerlik skoru: logaritmik, 10M+ ≈ 1.0 */
function popularityScore(views: number): number {
  return Math.min(1, Math.log10(views + 1) / 7);
}

/** Güncelleme skoru: 2 yıl içindekiler tam puan, eskidikçe düşer. */
function recencyScore(publishedAt: string | null): number {
  if (!publishedAt) return 0.3;
  const ageDays = (Date.now() - new Date(publishedAt).getTime()) / 86_400_000;
  if (!Number.isFinite(ageDays) || ageDays < 0) return 0.3;
  return Math.max(0.1, 1 - ageDays / 1460);
}

/** "Neden önerildi?" gerekçeleri — kullanıcıya açıklama (max 4). */
export function buildReasons(
  video: Video,
  analysis: QueryAnalysis,
  matchedTerms: string[],
  score: number,
): string[] {
  const title = normalize(video.title);
  const reasons: string[] = [];

  const titleMatches = matchedTerms.filter((t) => title.includes(t));
  if (titleMatches.length > 0) reasons.push(`Başlıkta eşleşme: "${titleMatches.slice(0, 3).join('", "')}"`);
  const descOnly = matchedTerms.filter((t) => !title.includes(t) && normalize(video.description).includes(t));
  if (descOnly.length > 0) reasons.push(`Açıklamada eşleşme: "${descOnly.slice(0, 3).join('", "')}"`);

  if (analysis.hints.wantsLive && (video.isLive || /live|canlı/.test(title))) reasons.push('Canlı performans aramasıyla uyumlu');
  if (analysis.hints.wantsOfficial && /official|resmi/.test(title)) reasons.push('Resmi içerik');
  if (video.viewCount >= 10_000_000) reasons.push(`Yüksek izlenme (${formatViews(video.viewCount)})`);
  else if (video.viewCount >= 1_000_000) reasons.push(`Popüler (${formatViews(video.viewCount)} izlenme)`);
  if (recencyScore(video.publishedAt) > 0.8) reasons.push('Güncel içerik');
  if (video.isShort) reasons.push('Shorts formatı');
  if (score >= 0.75) reasons.push(`Genel uyum yüksek (${Math.round(score * 100)}%)`);

  return reasons.slice(0, 4);
}

export function scoreVideo(video: Video, analysis: QueryAnalysis): ScoredVideo {
  const title = normalize(video.title);
  const desc = normalize(video.description);
  const matchedTerms = analysis.terms.filter((t) => title.includes(t) || desc.includes(t));
  const termScore = analysis.terms.length > 0 ? matchedTerms.length / analysis.terms.length : 0;

  const titleBonus = analysis.terms.some((t) => title.includes(t)) ? 0.15 : 0;

  let signalScore = 0;
  if (analysis.hints.wantsLive && (video.isLive || /live|canlı/.test(title))) signalScore += 0.1;
  if (analysis.hints.wantsOfficial && /official|resmi/.test(title)) signalScore += 0.1;
  if (video.isLive && !analysis.hints.wantsLive) signalScore -= 0.05;

  const score =
    termScore * 0.45 +
    popularityScore(video.viewCount) * 0.25 +
    recencyScore(video.publishedAt) * 0.15 +
    Math.min(0.15, video.qualityScore * 0.15) +
    titleBonus +
    signalScore;

  const clamped = Math.max(0, Math.min(1, Number(score.toFixed(4))));

  return {
    ...video,
    // Sinyaller her yanıtta tamamlanır (cache/DB kaynağı fark etmez)
    language: video.language ?? detectLanguage(video.title, video.description),
    spamScore: video.spamScore ?? spamScore(video.title, video.description),
    score: clamped,
    matchedTerms,
    reasons: buildReasons(video, analysis, matchedTerms, clamped),
  };
}

export function rankVideos(videos: Video[], analysis: QueryAnalysis): ScoredVideo[] {
  return videos
    .map((v) => scoreVideo(v, analysis))
    .sort((a, b) => b.score - a.score);
}
