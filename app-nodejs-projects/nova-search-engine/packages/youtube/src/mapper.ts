// ===========================================
// Mapper — youtubei.js düğümleri → normalize Video
// Karar: tüm provider çıktıları ortak Video sözleşmesine
// yazılır; DB'ye yalnız normalize veri girer.
// ===========================================

import type { Video } from '@novasearch/common';
import { detectLanguage, spamScore } from '@novasearch/common';

/* eslint-disable @typescript-eslint/no-explicit-any */

export function textOf(value: unknown): string {
  if (!value) return '';
  if (typeof value === 'string') return value;
  const t = (value as any).text;
  if (typeof t === 'string') return t;
  return String(value);
}

/** "1.234.567 views" / "273 B abone" / "1,2 Mn izlenme" / "4.5B" → sayı */
export function parseViews(text: string): number {
  const t = text.toLowerCase();
  // sayı + (varsa) hemen ardındaki kısaltma eki tek geçişte
  const m = t.match(/(\d+(?:[.,]\d+)*)\s*(mr|milyar|mn|milyon|bin|k|m|b|g)?/);
  if (!m?.[1]) return 0;
  const raw = m[1];
  const suf = m[2];
  const isTr = /abone|izlenme|beğeni|yorum|görüntülenme|kez|bin|milyon|milyar/.test(t);
  // "B" belirsizdir: TR'de bin, EN'de milyar. Varsayılan TR (yerel ayar tr-TR);
  // yalnız açık EN imleri varsa milyar sayılır.
  const isEn = /views|likes|subscribers|million|billion|comments?\b/.test(t);

  const mult =
    suf === 'mr' || suf === 'milyar' ? 1_000_000_000 :
    suf === 'mn' || suf === 'milyon' ? 1_000_000 :
    suf === 'bin' || suf === 'k' ? 1_000 :
    suf === 'm' ? 1_000_000 :
    suf === 'b' ? (isEn && !isTr ? 1_000_000_000 : 1_000) :
    suf === 'g' ? 1_000_000_000 : 1;

  let base: number;
  if (mult === 1) {
    // ek yoksa tüm ayraçlar binliktir ("12.345" → 12345)
    base = parseFloat(raw.replace(/[.,\s]/g, ''));
  } else if (isTr && raw.includes(',')) {
    // TR ondalık: virgül; nokta binlik ("1,2 Mn" → 1.2M)
    base = parseFloat(raw.replace(/\./g, '').replace(',', '.'));
  } else {
    // EN ondalık: nokta ("1.2M" → 1.2M)
    base = parseFloat(raw.replace(/,/g, ''));
  }
  return Number.isFinite(base) ? Math.round(base * mult) : 0;
}

/** "3:45" / "1:02:03" → saniye */
export function parseDuration(text: string): number {
  const parts = text.trim().split(':').map((p) => parseInt(p, 10));
  if (parts.some((p) => !Number.isFinite(p))) return 0;
  return parts.reduce((acc, p) => acc * 60 + p, 0);
}

const TR_MONTHS: Record<string, number> = {
  oca: 0, şub: 1, sub: 1, mar: 2, nis: 3, may: 4, haz: 5,
  tem: 6, ağu: 7, agu: 7, eyl: 8, eki: 9, kas: 10, ara: 11,
  jan: 0, feb: 1, mar2: 2, apr: 3, jun: 5, jul: 6, aug: 7, sep: 8, oct: 9, nov: 10, dec: 11,
};

/**
 * Göreli tarih → yaklaşık ISO ("2 years ago", "3 ay önce", "1 Haz 2016").
 * YouTube arama düğümleri göreli verir; kesin tarih detay sayfasından gelir.
 */
export function parseRelativeDate(text: string): string | null {
  const t = text.trim().toLowerCase();
  if (!t) return null;

  // "X yıl/ay/hafta/gün/saat önce" | "X years/months/weeks/days/hours ago"
  const rel = t.match(/(\d+)\s*(yıl|ay|hafta|gün|saat|dakika|year|month|week|day|hour|minute)s?\s*(önce|ago)/);
  if (rel?.[1]) {
    const n = parseInt(rel[1], 10);
    const unit = rel[2] ?? '';
    const dayMs = 86_400_000;
    const mult =
      unit.startsWith('yıl') || unit.startsWith('year') ? 365 :
      unit.startsWith('ay') || unit.startsWith('month') ? 30 :
      unit.startsWith('hafta') || unit.startsWith('week') ? 7 :
      unit.startsWith('gün') || unit.startsWith('day') ? 1 :
      unit.startsWith('saat') || unit.startsWith('hour') ? 1 / 24 : 1 / 1440;
    return new Date(Date.now() - n * mult * dayMs).toISOString();
  }

  // "1 Haz 2016" / "Jun 1, 2016"
  const tr = t.match(/(\d{1,2})\s+([a-zçğıöşü]{3,})\.?\s+(\d{4})/);
  if (tr?.[2] && tr[3]) {
    const month = TR_MONTHS[tr[2].slice(0, 3)];
    if (month !== undefined) {
      const d = new Date(Date.UTC(parseInt(tr[3], 10), month, parseInt(tr[1] ?? '1', 10)));
      if (!Number.isNaN(d.getTime())) return d.toISOString();
    }
  }
  const en = t.match(/([a-z]{3,})\.?\s+(\d{1,2}),?\s+(\d{4})/);
  if (en?.[1] && en[3]) {
    const month = TR_MONTHS[en[1].slice(0, 3)];
    if (month !== undefined) {
      const d = new Date(Date.UTC(parseInt(en[3], 10), month, parseInt(en[2] ?? '1', 10)));
      if (!Number.isNaN(d.getTime())) return d.toISOString();
    }
  }
  return null;
}

function thumbnailsOf(node: any): string[] {
  const list = (node?.thumbnails as Array<{ url?: string }> | undefined) ?? [];
  return list
    .map((t) => (t?.url ?? '').startsWith('//') ? `https:${t.url}` : (t?.url ?? ''))
    .filter((u) => u.length > 0);
}

function badgesOf(node: any): string[] {
  const arr = Array.isArray(node?.badges) ? node.badges : [];
  return arr
    .map((b: any) => textOf(b?.label ?? b?.text ?? b?.tooltip))
    .filter((s: string) => s.length > 0);
}

/** YouTube izleme URL'i (her video için). */
export function watchUrl(id: string): string {
  return `https://www.youtube.com/watch?v=${id}`;
}

/** Search sonuçlarındaki herhangi bir video düğümünü normalize et. */
export function mapToVideo(node: any): Video | null {
  const id: string = node?.video_id ?? node?.id ?? '';
  const title = textOf(node?.title);
  if (!id || !title) return null;

  const durationSeconds = parseDuration(textOf(node?.length_text ?? node?.duration));
  const isShortStyle = node?.style === 'SHORTS' || node?.type === 'ReelItem' || node?.type === 'ShortsLockupView';
  const badges = badgesOf(node);
  const publishedText = textOf(node?.published);
  const thumbs = thumbnailsOf(node);
  const channelId = String(node?.author?.id ?? '');

  return {
    youtubeId: id,
    title,
    description: textOf(node?.description_snippet ?? node?.description),
    channelId,
    channelName: textOf(node?.author?.name),
    durationSeconds,
    viewCount: parseViews(textOf(node?.view_count ?? node?.short_view_count)),
    likeCount: parseViews(textOf(node?.like_count)),
    publishedAt: parseRelativeDate(publishedText),
    thumbnailUrl: thumbs[0] ?? '',
    tags: [],
    isShort: isShortStyle || (durationSeconds > 0 && durationSeconds <= 60),
    isLive: badges.some((b: string) => b.toLowerCase().includes('live') || b.toLowerCase().includes('canlı')) || node?.is_live === true,
    qualityScore: 0,
    source: 'youtube',

    // Zengin alanlar
    url: watchUrl(id),
    channelUrl: channelId ? `https://www.youtube.com/channel/${channelId}` : undefined,
    viewCountText: textOf(node?.view_count ?? node?.short_view_count) || undefined,
    publishedText: publishedText || undefined,
    thumbnails: thumbs.length > 0 ? thumbs : undefined,
    badges: badges.length > 0 ? badges : undefined,
    language: detectLanguage(title, textOf(node?.description_snippet ?? node?.description)),
    spamScore: spamScore(title, textOf(node?.description_snippet ?? node?.description)),
  };
}

/* eslint-enable @typescript-eslint/no-explicit-any */
