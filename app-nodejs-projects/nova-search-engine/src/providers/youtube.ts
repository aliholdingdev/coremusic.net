/** YouTube Provider — youtubei.js tek erişim noktası (core asla youtubei.js görmez). */
import { Innertube } from 'youtubei.js';
import { config } from '../config.ts';
import type { VideoRecord, SearchFilters, CommentRecord, RelatedVideo } from '../core/types.ts';

// youtubei.js parser gürültüsü: bilinmeyen renderer'ları JIT üretirken [YOUTUBEJS] prefix'li
// warning basar (InnertubeError/ParsingError dump'ları — zararsız). Bunlar console.warn'a gider;
// gerçek hatalar console.error'a ve bizim try/catch'imize düşmeye devam eder.
// NOVA_YT_LOG=1 ile geri açılır (parser/updater debug için).
if (!config.ytLog) {
  const origWarn = console.warn.bind(console);
  console.warn = (...args: unknown[]) => {
    if (typeof args[0] === 'string' && args[0].startsWith('[YOUTUBEJS]')) return;
    origWarn(...args);
  };
}

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

/** publishedAt sözleşmesi ISO — göreli metin ("6 months ago") bu testten geçmez. */
const ISO_RE = /^\d{4}-\d{2}-\d{2}/;

function toRecord(raw: Record<string, unknown>): VideoRecord | null {
  const id = String(raw.id ?? '');
  if (!id) return null;
  const now = new Date().toISOString();
  const author = (raw.author ?? {}) as Record<string, unknown>;
  const thumbs = (raw.thumbnails ?? []) as { url?: string }[];
  const duration = raw.duration as { seconds?: number } | null | undefined;
  // raw.published.text göreli metindir ("6 months ago") — publishedAt ISO ister, yazma
  const pubRaw = str(raw.published);
  return {
    youtubeId: id,
    title: str(raw.title),
    description: str(raw.description ?? raw.snippet ?? ''),
    channelId: String(author.id ?? ''),
    channelName: str(author.name),
    durationSec: typeof duration?.seconds === 'number' ? duration.seconds : 0,
    viewCount: num(raw.view_count ?? raw.short_view_count),
    likeCount: null,
    publishedAt: ISO_RE.test(pubRaw) ? pubRaw : null,
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

/* ---------- Yeni nesil renderer mapper'ları (LockupView / ShortsLockupView)
   Kanıt: probe 2026-10-15 — varsayılan sayfa = Video 19 + ShortsLockupView 25 + LockupView 10;
   eski toRecord sadece `.id` okuduğu için 35'ini sessizce atıyordu. ---------- */

/** "106M views" / "336K" / "12,345" / "43K" → sayı (compact + binlik ayracı). */
export function parseCountLike(text: string): number {
  const t = text.trim();
  if (!t) return 0;
  const stripped = t.replace(/views?|görüntüleme|izlenme|görüntülenme/gi, '').trim();
  if (/^\d{1,3}(?:[.,]\d{3})+$/.test(stripped)) return Number(stripped.replace(/[.,]/g, ''));
  const c = parseCompactCount(stripped);
  if (c != null) return c;
  const n = Number(stripped.replace(/[^\d]/g, ''));
  return Number.isFinite(n) ? n : 0;
}

const VIEWS_RE = /^\d[\d.,]*\s*[KMB](?:n)?(?:\s+views)?$/i;
const DATE_RE = /\bago\b|premiere|streamed|streaming|yayın/i;

/**
 * LockupMetadataView.metadata_rows → alanlar (satırlar düzleştirilip sınıflandırılır).
 * Kanıt (probe-3/4): ilgili = [[yazar],[views, tarih]] · arama = [[yazar, views, tarih]].
 */
export function rowsToFields(rows: string[][]): {
  channelName: string;
  viewCountText: string;
  viewCount: number;
  publishedText: string | null;
} {
  const parts = rows.flat().map((s) => s.trim()).filter(Boolean);
  const viewCountText = parts.find((p) => VIEWS_RE.test(p)) ?? '';
  const publishedText = parts.find((p) => DATE_RE.test(p)) ?? null;
  const channelName = parts.find((p) => p !== viewCountText && p !== publishedText) ?? '';
  return { channelName, viewCountText, viewCount: parseCountLike(viewCountText), publishedText };
}

function thumbsUrl(list: unknown): string | null {
  const arr = Array.isArray(list) ? (list as { url?: unknown }[]) : [];
  const url = arr.length ? String(arr[arr.length - 1]?.url ?? '') : '';
  return url || null;
}

interface LockupNode {
  type?: string;
  content_id?: string;
  content_type?: string;
  metadata?: { title?: unknown; metadata?: { metadata_rows?: { metadata_parts?: { text?: unknown }[] }[] } };
  content_image?: { image?: unknown; primary_thumbnail?: { image?: unknown } | null } | null;
}

/** LockupView → ortak alanlar (VIDEO/SHORT; PLAYLIST/CHANNEL null). */
export function lockupFields(node: LockupNode): {
  id: string; title: string; channelName: string;
  viewCountText: string; viewCount: number; publishedText: string | null;
  thumbnailUrl: string | null; isShort: boolean;
} | null {
  const id = String(node.content_id ?? '');
  const ctype = String(node.content_type ?? '');
  if (!id || (ctype !== 'VIDEO' && ctype !== 'SHORT')) return null;
  const meta = node.metadata;
  const rows: string[][] = (meta?.metadata?.metadata_rows ?? []).map((r) =>
    (r.metadata_parts ?? []).map((p) => str(p.text)),
  );
  const f = rowsToFields(rows);
  const img = node.content_image;
  const thumb = thumbsUrl(img?.image) ?? thumbsUrl(img?.primary_thumbnail?.image);
  return {
    id,
    title: str(meta?.title),
    channelName: f.channelName,
    viewCountText: f.viewCountText,
    viewCount: f.viewCount,
    publishedText: f.publishedText,
    thumbnailUrl: thumb,
    isShort: ctype === 'SHORT',
  };
}

export function lockupToRecord(node: LockupNode): VideoRecord | null {
  const f = lockupFields(node);
  if (!f || !f.title) return null;
  const now = new Date().toISOString();
  return {
    youtubeId: f.id,
    title: f.title,
    description: '',
    channelId: '',
    channelName: f.channelName,
    durationSec: 0, // lockup'ta süre yok — enrich getInfo ile doldurur
    viewCount: f.viewCount,
    likeCount: null,
    publishedAt: null, // göreli metin ("14 years ago") — enrich ISO yayın tarihiyle doldurur
    category: null,
    tags: '',
    thumbnailUrl: f.thumbnailUrl,
    firstSeenAt: now,
    lastSeenAt: now,
    isShort: f.isShort,
  };
}

interface ShortsLockupNode {
  type?: string;
  entity_id?: string;
  overlay_metadata?: { primary_text?: unknown; secondary_text?: unknown };
  thumbnail?: unknown;
}

/** ShortsLockupView → kayıt. entity_id "shorts-shelf-item-<id>" biçiminde (probe-4 kanıtı). */
export function shortsToRecord(node: ShortsLockupNode): VideoRecord | null {
  const raw = String(node.entity_id ?? '');
  const id = raw.startsWith('shorts-shelf-item-') ? raw.slice('shorts-shelf-item-'.length) : raw;
  if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return null;
  const title = str(node.overlay_metadata?.primary_text);
  if (!title) return null;
  const now = new Date().toISOString();
  const viewText = str(node.overlay_metadata?.secondary_text);
  return {
    youtubeId: id,
    title,
    description: '',
    channelId: '',
    channelName: '', // shelf'te kanal verisi yok
    durationSec: 0,
    viewCount: parseCountLike(viewText),
    likeCount: null,
    publishedAt: null,
    category: null,
    tags: '',
    thumbnailUrl: thumbsUrl(node.thumbnail),
    firstSeenAt: now,
    lastSeenAt: now,
    isShort: true, // shelf kendi short'udur (süre platform tanımıyla sabit)
  };
}

/** Sayfadaki her düğüm tipine doğru mapper'ı uygular (Video/CompactVideo → eski toRecord). */
function nodeToRecord(node: Record<string, unknown>): VideoRecord | null {
  const type = String((node as { type?: unknown }).type ?? '');
  if (type === 'LockupView') return lockupToRecord(node as unknown as LockupNode);
  if (type === 'ShortsLockupView') return shortsToRecord(node as unknown as ShortsLockupNode);
  return toRecord(node);
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
 * YouTube araması. `scroll` = kaç sayfa sonuç (max COLLECT_MAX_PAGES, ~20-54 sonuç/sayfa).
 * Sayfa başı karışık havuz: Video + ShortsLockupView (shorts shelf) + LockupView — hepsi toplanır.
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
      const rec = nodeToRecord(raw);
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
 * Derin toplama — `targetCount` verilirse hedefe kadar, `null` (auto mod) ise
 * continuation tükenene kadar (max COLLECT_AUTO_MAX_PAGES) getContinuation() ile ilerler.
 * Anti-ban: her sayfa arasında politeWait.
 */
export async function youtubeSearchDeep(
  query: string,
  targetCount: number | null,
  onProgress?: (found: number, pages: number) => void,
  filters?: SearchFilters,
): Promise<{ records: VideoRecord[]; pages: number }> {
  const yt = await getInnertube();
  const records: VideoRecord[] = [];
  const seen = new Set<string>();
  const maxPages =
    targetCount == null ? config.collector.autoMaxPages : Math.ceil(targetCount / 15) + 2;
  const targetReached = (): boolean => targetCount != null && records.length >= targetCount;

  await politeWait();
  let search: unknown = await yt.search(query, filters ? ({ sortBy: sortParam(filters) } as never) : undefined);

  let done = 0;
  for (let p = 0; p < maxPages && !targetReached(); p++) {
    const vids = ((search as { videos?: unknown[] }).videos ?? []) as Record<string, unknown>[];
    for (const raw of vids) {
      const rec = nodeToRecord(raw);
      if (rec && !seen.has(rec.youtubeId)) {
        seen.add(rec.youtubeId);
        records.push(rec);
      }
    }
    done = p + 1;
    onProgress?.(records.length, done);
    if (targetReached()) break;
    await politeWait();
    const next = await nextPage(search);
    if (!next) break;
    search = next;
  }
  return { records, pages: done };
}

/**
 * Video detayı (getInfo) — kanıt (probe 2026-10-14/15):
 * basic_info: channel_id/author/duration/view_count/like_count(number)/keywords(array)/category/thumbnail[]
 * page[0].microformat (PlayerMicroformat): publish_date/upload_date/category/description(Text)/channel{id,name,url}/length_seconds
 * (info.microformat.player_microformat v18'de YOK — erişim page üzerinden; eksik okuma kapatıldı.)
 * Açıklama olmayan videolarda microformat.description "N/A" döner → boş sayılır.
 */
export async function youtubeInfo(videoId: string): Promise<VideoRecord | null> {
  const yt = await getInnertube();
  await politeWait();
  try {
    const info = await yt.getInfo(videoId);
    const basic = info.basic_info as Record<string, unknown>;
    const now = new Date().toISOString();
    const mf = info.page[0]?.microformat as unknown as {
      publish_date?: string; upload_date?: string; category?: string;
      description?: unknown; channel?: { id?: string; name?: string; url?: string };
      length_seconds?: number;
    } | undefined;
    const microDesc = str(mf?.description ?? '');
    const description = microDesc && microDesc !== 'N/A' ? microDesc : String(basic.short_description ?? '');
    return {
      youtubeId: videoId,
      title: String(basic.title ?? ''),
      description,
      channelId: String(basic.channel_id ?? mf?.channel?.id ?? ''),
      channelName: String(basic.author ?? mf?.channel?.name ?? ''),
      channelUrl: mf?.channel?.url ? String(mf.channel.url).replace(/^http:\/\//, 'https://') : null,
      durationSec: Number(basic.duration ?? mf?.length_seconds ?? 0),
      viewCount: Number(basic.view_count ?? 0),
      likeCount: typeof basic.like_count === 'number' ? basic.like_count : null,
      publishedAt: mf?.publish_date ?? mf?.upload_date ?? null,
      category: mf?.category ?? (basic.category ? String(basic.category) : null),
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

/**
 * getInfo detayını arama kaydıyla birleştir — boş/eksik alanlar dolar, dolu alanlar KORUNUR
 * (arama viewType'i getInfo'dan daha taze olabilir; like/description gibi alanlar getInfo'da).
 */
export function mergeDetail<T extends VideoRecord>(base: T, info: VideoRecord): T {
  return {
    ...base,
    title: base.title || info.title,
    description: base.description || info.description,
    channelId: base.channelId || info.channelId,
    channelName: base.channelName || info.channelName,
    channelUrl: base.channelUrl ?? info.channelUrl ?? null,
    durationSec: base.durationSec || info.durationSec,
    viewCount: base.viewCount || info.viewCount,
    likeCount: base.likeCount ?? info.likeCount,
    publishedAt: base.publishedAt && ISO_RE.test(base.publishedAt) ? base.publishedAt : (info.publishedAt ?? null),
    category: base.category ?? info.category,
    tags: base.tags || info.tags,
    thumbnailUrl: base.thumbnailUrl ?? info.thumbnailUrl,
  };
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

/* ---------- Video yorumları / benzer videolar (yeni dalga) ----------
   Kanıt: probe 2026-10-15 —
   · yt.getComments(id, 'TOP_COMMENTS') → CommentThread.comment (CommentView): comment_id,
     content(Text), published_time("6 years ago"), like_count("43K"), reply_count("593"),
     author(Author: name/id), is_pinned, author_is_channel_owner · getContinuation() ile sayfa sayfa (20/sayfa).
   · yt.getInfo(id).watch_next_feed → hepsi LockupView (18 VIDEO +2 PLAYLIST) · wn_has_continuation.
   · Parser JIT uyarıları (MenuCustomIconItem vb.) zararsızdır — stderr'e yazar, akışı bozmaz. */

/** CommentView → CommentRecord (saf dönüşüm — youtubei.js nesnesi olmadan test edilebilir). */
export function commentViewToRecord(
  videoId: string,
  cv: {
    comment_id?: unknown; content?: unknown; published_time?: unknown;
    like_count?: unknown; reply_count?: unknown; is_pinned?: unknown;
    author_is_channel_owner?: unknown; author?: { name?: unknown; id?: unknown } | null;
  },
): CommentRecord | null {
  const text = str(cv.content);
  if (!text) return null;
  const commentId = String(cv.comment_id ?? '');
  const authorId = typeof cv.author?.id === 'string' ? cv.author.id : '';
  return {
    commentId,
    authorName: str(cv.author?.name),
    authorChannelId: authorId || null,
    text,
    publishedText: typeof cv.published_time === 'string' ? cv.published_time : null,
    likeCount: parseCountLike(str(cv.like_count)),
    replyCount: parseCountLike(str(cv.reply_count)),
    isPinned: cv.is_pinned === true,
    isChannelOwner: cv.author_is_channel_owner === true,
    url: `https://www.youtube.com/watch?v=${videoId}&lc=${commentId}`,
  };
}

/**
 * Video yorumları — üst düzey yorumlar (replies dahil DEĞİL), limit'e kadar sayfa sayfa.
 * Hata fırlatır (fail-safe video-detail katmanında).
 */
export async function youtubeComments(videoId: string, limit: number): Promise<CommentRecord[]> {
  if (limit <= 0) return [];
  const yt = await getInnertube();
  await politeWait();
  const out: CommentRecord[] = [];
  let page = await yt.getComments(videoId, 'TOP_COMMENTS');
  let guard = Math.ceil(limit / 15) + 2; // 20/sayfa → güvenli sayfa üst sınırı
  while (guard-- > 0) {
    for (const thread of page.contents) {
      if (out.length >= limit) break;
      const cv = (thread as { comment?: Parameters<typeof commentViewToRecord>[1] | null }).comment;
      if (!cv) continue;
      const rec = commentViewToRecord(videoId, cv);
      if (rec) out.push(rec);
    }
    if (out.length >= limit || !page.has_continuation) break;
    await politeWait();
    const next = await page.getContinuation();
    if (!next || !next.contents?.length) break;
    page = next;
  }
  return out;
}

/** LockupView (watch_next_feed) → RelatedVideo. */
export function lockupToRelated(node: LockupNode): RelatedVideo | null {
  const f = lockupFields(node);
  if (!f || !f.title) return null;
  return {
    youtubeId: f.id,
    title: f.title,
    channelName: f.channelName,
    viewCountText: f.viewCountText,
    viewCount: f.viewCount,
    publishedText: f.publishedText,
    contentType: f.isShort ? 'SHORT' : 'VIDEO',
    url: `https://www.youtube.com/watch?v=${f.id}`,
  };
}

/**
 * Benzer/önerilen videolar — getInfo tek yanıtındaki watch_next_feed (ilk sayfa ~20).
 * Hata fırlatır (fail-safe video-detail katmanında).
 */
export async function youtubeRelated(videoId: string, limit: number): Promise<RelatedVideo[]> {
  if (limit <= 0) return [];
  const yt = await getInnertube();
  await politeWait();
  const info = await yt.getInfo(videoId);
  const feed = ((info as unknown as { watch_next_feed?: unknown[] }).watch_next_feed ?? []) as LockupNode[];
  const out: RelatedVideo[] = [];
  for (const node of feed) {
    if (out.length >= limit) break;
    if (node?.type !== 'LockupView') continue;
    const rec = lockupToRelated(node);
    if (rec) out.push(rec);
  }
  return out;
}
