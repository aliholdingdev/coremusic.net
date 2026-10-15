/** YouTube Provider — youtubei.js tek erişim noktası (core asla youtubei.js görmez). */
import { Innertube } from 'youtubei.js';
import { config } from '../config.ts';
import type { VideoRecord, SearchFilters } from '../core/types.ts';

let ytPromise: Promise<Innertube> | null = null;

function getInnertube(): Promise<Innertube> {
  if (!ytPromise) ytPromise = Innertube.create({ retrieve_player: false, generate_session_locally: true });
  return ytPromise;
}

/** Anti-ban: 1.5-6 sn jitter + dakika limiti (insan benzeri ritim). */
const timestamps: number[] = [];
async function politeWait(): Promise<void> {
  const now = Date.now();
  while (timestamps.length && now - timestamps[0] < 60_000) timestamps.shift();
  if (timestamps.length >= config.collector.maxRequestsPerMinute) {
    const waitMs = 60_000 - (now - timestamps[0]);
    await sleep(waitMs);
  }
  const jitter =
    config.collector.minDelayMs + Math.random() * (config.collector.maxDelayMs - config.collector.minDelayMs);
  await sleep(jitter);
  timestamps.push(Date.now());
}

function sleep(ms: number): Promise<void> {
  return new Promise((r) => setTimeout(r, ms));
}

function num(v: unknown): number {
  if (typeof v === 'number') return v;
  if (typeof v === 'string') {
    const n = Number(v.replace(/[^\d]/g, ''));
    return Number.isFinite(n) ? n : 0;
  }
  if (v && typeof v === 'object' && 'text' in v) return num((v as { text: unknown }).text);
  return 0;
}

function str(v: unknown): string {
  if (typeof v === 'string') return v;
  if (v && typeof v === 'object' && 'text' in v) return str((v as { text: unknown }).text);
  if (v && typeof v === 'object' && 'simple_text' in v) return str((v as { simple_text: unknown }).simple_text);
  return '';
}

function toRecord(raw: Record<string, unknown>): VideoRecord | null {
  const id = String(raw.id ?? '');
  if (!id) return null;
  const now = new Date().toISOString();
  const author = (raw.author ?? {}) as Record<string, unknown>;
  const thumbs = (raw.thumbnails ?? []) as { url?: string }[];
  const duration = raw.duration as { seconds?: number } | null | undefined;
  return {
    youtubeId: id,
    title: str(raw.title),
    description: str(raw.description ?? raw.snippet ?? ''),
    channelId: String(author.id ?? ''),
    channelName: str(author.name),
    durationSec: typeof duration?.seconds === 'number' ? duration.seconds : 0,
    viewCount: num(raw.view_count ?? raw.short_view_count),
    likeCount: null,
    publishedAt: typeof (raw.published as { text?: unknown } | undefined)?.text === 'string' ? str(raw.published) : null,
    category: null,
    tags: str(raw.keywords ?? ''),
    thumbnailUrl: thumbs.length ? thumbs[thumbs.length - 1]?.url ?? null : null,
    firstSeenAt: now,
    lastSeenAt: now,
  };
}

function sortParam(f: SearchFilters): string | undefined {
  switch (f.sortBy) {
    case 'views': return 'VIDEO_VIEW_COUNT';
    case 'date': return 'UPLOAD_DATE';
    case 'rating': return 'VIDEO_RELEVANCE';
    default: return undefined;
  }
}

/**
 * v18 pagination: getNext/getMore YOK — tek metod getContinuation().
 * Kanıt: probe (2026-10-14) → getContinuation() sayfa2 = 33 video.
 */
async function nextPage(search: unknown): Promise<unknown | null> {
  const s = search as Record<string, (() => Promise<unknown>) | undefined>;
  const cont = s.getContinuation;
  if (typeof cont !== 'function') return null;
  try {
    return await cont.call(search);
  } catch {
    return null; // continuation tükendi → normal durum
  }
}

/**
 * YouTube araması. `scroll` = kaç sayfa sonuç (max COLLECT_MAX_PAGES, ~20-47 sonuç/sayfa).
 */
export async function youtubeSearch(
  query: string,
  filters: SearchFilters,
  scroll: number,
): Promise<{ records: VideoRecord[]; pages: number }> {
  const yt = await getInnertube();
  const pages = Math.max(1, Math.min(scroll, config.collector.maxPages));
  const records: VideoRecord[] = [];
  const seen = new Set<string>();

  await politeWait();
  let search: unknown = await yt.search(query, { sortBy: sortParam(filters) } as never);

  for (let p = 0; p < pages; p++) {
    const vids = ((search as { videos?: unknown[] }).videos ?? []) as Record<string, unknown>[];
    for (const raw of vids) {
      const rec = toRecord(raw);
      if (rec && !seen.has(rec.youtubeId)) {
        seen.add(rec.youtubeId);
        records.push(rec);
      }
    }
    if (p < pages - 1) {
      await politeWait();
      const next = await nextPage(search);
      if (!next) break;
      search = next;
    }
  }
  return { records, pages };
}

/**
 * Derin toplama — hedef kayıt sayısına kadar getContinuation() ile ilerler.
 * Anti-ban: her sayfa arasında politeWait. Güvenlik sınırı: max ceil(target/15)+2 sayfa.
 */
export async function youtubeSearchDeep(
  query: string,
  targetCount: number,
  onProgress?: (found: number, pages: number) => void,
): Promise<{ records: VideoRecord[]; pages: number }> {
  const yt = await getInnertube();
  const records: VideoRecord[] = [];
  const seen = new Set<string>();
  const maxPages = Math.ceil(targetCount / 15) + 2;

  await politeWait();
  let search: unknown = await yt.search(query);

  for (let p = 0; p < maxPages && records.length < targetCount; p++) {
    const vids = ((search as { videos?: unknown[] }).videos ?? []) as Record<string, unknown>[];
    for (const raw of vids) {
      const rec = toRecord(raw);
      if (rec && !seen.has(rec.youtubeId)) {
        seen.add(rec.youtubeId);
        records.push(rec);
      }
    }
    onProgress?.(records.length, p + 1);
    if (records.length >= targetCount) break;
    await politeWait();
    const next = await nextPage(search);
    if (!next) break;
    search = next;
  }
  return { records, pages: Math.ceil(records.length / 20) };
}

/**
 * Video detayı (getInfo) — kanıt (probe 2026-10-14):
 * basic_info: channel_id/author/view_count/like_count(number)/keywords(array)/thumbnail[]
 * microformat.player_microformat: publish_date/category (bazı videolarda null)
 */
export async function youtubeInfo(videoId: string): Promise<VideoRecord | null> {
  const yt = await getInnertube();
  await politeWait();
  try {
    const info = await yt.getInfo(videoId);
    const basic = info.basic_info as Record<string, unknown>;
    const now = new Date().toISOString();
    const micro = (info as unknown as { microformat?: { player_microformat?: Record<string, unknown> } })
      .microformat?.player_microformat;
    return {
      youtubeId: videoId,
      title: String(basic.title ?? ''),
      description: String(basic.short_description ?? ''),
      channelId: String(basic.channel_id ?? ''),
      channelName: String(basic.author ?? ''),
      durationSec: Number(basic.duration ?? 0),
      viewCount: Number(basic.view_count ?? 0),
      likeCount: typeof basic.like_count === 'number' ? basic.like_count : null,
      publishedAt: micro?.publish_date ? String(micro.publish_date) : null,
      category: micro?.category ? String(micro.category) : null,
      tags: Array.isArray(basic.keywords) ? (basic.keywords as string[]).join(' ') : '',
      thumbnailUrl: Array.isArray(basic.thumbnail)
        ? String((basic.thumbnail as { url?: string }[])[0]?.url ?? '') || null
        : null,
      firstSeenAt: now,
      lastSeenAt: now,
    };
  } catch {
    return null;
  }
}

/** "1,2 Mn" / "5,6 B" / "853 B" gibi kısa sayıları tam sayıya çevir (TR/EN karışık). */
function parseCompactCount(text: string): number | null {
  const cleaned = text.replace(/\s/g, '');
  const m = cleaned.match(/^([\d]+(?:[.,][\d]+)?)([KMB]|Bn|Mn|Milyon|Milyar)?$/i);
  if (!m) return null;
  const n = parseFloat(m[1].replace(',', '.'));
  if (!Number.isFinite(n)) return null;
  const suffix = (m[2] ?? '').toLowerCase();
  const mult: Record<string, number> = {
    k: 1e3, m: 1e6, b: 1e9, bn: 1e9, mn: 1e6, milyon: 1e6, milyar: 1e9,
  };
  return Math.round(n * (mult[suffix] ?? 1));
}

/** Kanal detayı — v18: getChannel(id) → metadata.title + header.content.metadata_parts ("4.82K subscribers"). */
export async function youtubeChannel(
  channelId: string,
): Promise<{ id: string; name: string; subs: number | null } | null> {
  if (!channelId || !channelId.startsWith('UC')) return null; // geçersiz ID → ek istek yok
  const yt = await getInnertube();
  await politeWait();
  try {
    const ch = await yt.getChannel(channelId);
    const meta = (ch as unknown as { metadata?: Record<string, unknown> }).metadata;
    const name = str(meta?.title);
    // abone sayısı metadata'da yok → header ağacındaki "4.82K subscribers" metni
    const headerJson = JSON.stringify((ch as unknown as { header?: { content?: unknown } }).header?.content ?? '');
    const m = headerJson.match(/([\d.,]+\s*[KMB]?)\s+subscribers/i);
    const subs = m ? parseCompactCount(m[1]) : null;
    return { id: channelId, name, subs };
  } catch {
    return null; // kanal kaldırılmış / erişilemez → sustur, zenginleştirmeyi durdurma
  }
}
