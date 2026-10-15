/** Search Orchestrator — RAM → disk → FTS/DB → (yoksa) Collector → normalize → dedupe → kayıt. */
import { MemoryCache } from '../cache/memory-cache.ts';
import { DiskCache, cacheKey } from '../cache/disk-cache.ts';
import { ftsSearch, upsertVideo, saveHistory, saveAiQuery, countVideos, enqueueJob } from '../database/db.ts';
import { youtubeSearch } from '../providers/youtube.ts';
import { analyzeQuery, scoreResults, expandQueries, aiAvailable } from '../ai/engine.ts';
import { config } from '../config.ts';
import type { SearchOptions, SearchResultPayload, ScoredResult, VideoRecord, SearchFilters } from '../core/types.ts';

const ram = new MemoryCache();
const disk = new DiskCache();

/* ---------- Deterministik skor (AI yoksa da çalışır) ---------- */

function deterministicScore(v: VideoRecord, qTerms: string[]): number {
  let s = 40;
  const hay = `${v.title} ${v.tags} ${v.channelName}`.toLowerCase();
  const hits = qTerms.filter((t) => hay.includes(t)).length;
  if (qTerms.length) s += Math.round((hits / qTerms.length) * 35);
  if (v.viewCount > 1_000_000) s += 10;
  else if (v.viewCount > 100_000) s += 6;
  else if (v.viewCount > 10_000) s += 3;
  if (v.durationSec >= 60 && v.durationSec <= 1200) s += 8; // makul süre
  if (v.publishedAt) {
    const age = Date.now() - Date.parse(v.publishedAt);
    if (Number.isFinite(age) && age < 3 * 365 * 86400_000) s += 5;
  }
  if (v.channelName) s += 2;
  return Math.max(0, Math.min(100, s));
}

/* ---------- Filtre uygulama ---------- */

function parseDuration(d: string): { min?: number; max?: number } {
  const range = d.match(/^(\d+)\s*-\s*(\d+)$/);
  if (range) return { min: Number(range[1]), max: Number(range[2]) };
  const gt = d.match(/^>\s*(\d+)$/);
  if (gt) return { min: Number(gt[1]) };
  const lt = d.match(/^<\s*(\d+)$/);
  if (lt) return { max: Number(lt[1]) };
  const n = Number(d);
  return Number.isFinite(n) ? { min: n } : {};
}

function applyFilters(list: VideoRecord[], o: SearchOptions): VideoRecord[] {
  let out = list;
  // sh: shorts (is_short kolonu — shorts shelf'te süre verisi yok) veya ≤60s bilinen videolar
  if (o.filter === 'sh') out = out.filter((v) => v.isShort || (v.durationSec > 0 && v.durationSec <= 60));
  else if (o.filter === 's') out = out.filter((v) => v.durationSec >= 90 && v.durationSec <= 900);
  else if (o.filter === 'v') out = out.filter((v) => !v.isShort && v.durationSec > 60);
  if (o.minViews != null) out = out.filter((v) => v.viewCount >= o.minViews!);
  if (o.maxViews != null) out = out.filter((v) => v.viewCount <= o.maxViews!);
  if (o.duration) {
    const { min, max } = parseDuration(o.duration);
    out = out.filter((v) => (min == null || v.durationSec >= min) && (max == null || v.durationSec <= max));
  }
  if (o.type) {
    const t = o.type.toLowerCase();
    out = out.filter((v) => (v.category ?? '').toLowerCase().includes(t));
  }
  if (o.uploadDate) {
    const y = o.uploadDate.match(/^\d{4}$/);
    const rel = o.uploadDate.match(/^(<=|<|>=|>)\s*(\d+)\s*(y|mo|d)$/i);
    out = out.filter((v) => {
      if (!v.publishedAt) return false;
      if (y) return v.publishedAt.startsWith(o.uploadDate!);
      if (rel) {
        const ms = { d: 86400_000, mo: 30 * 86400_000, y: 365 * 86400_000 }[rel[3].toLowerCase() as 'd' | 'mo' | 'y'];
        const age = Date.now() - Date.parse(v.publishedAt);
        return rel[1] === '<=' || rel[1] === '<' ? age <= Number(rel[2]) * ms : age >= Number(rel[2]) * ms;
      }
      return true;
    });
  }
  return out;
}

function sortList(list: ScoredResult[], sortBy: SearchOptions['sortBy']): ScoredResult[] {
  switch (sortBy) {
    case 'views': return [...list].sort((a, b) => b.viewCount - a.viewCount);
    case 'date': return [...list].sort((a, b) => (b.publishedAt ?? '').localeCompare(a.publishedAt ?? ''));
    default: return [...list].sort((a, b) => (b.aiScore ?? b.score) - (a.aiScore ?? b.score));
  }
}

/* ---------- Collector (veri yoksa) ---------- */

async function collect(o: SearchOptions, query: string): Promise<{ records: VideoRecord[]; pages: number }> {
  if (o.offline || config.offline) return { records: [], pages: 0 };
  try {
    const { records, pages } = await youtubeSearch(query, o, o.scroll || 1);
    let inserted = 0;
    for (const r of records) if (upsertVideo(r) === 'inserted') inserted++;
    if (inserted < records.length || records.length === 0) {
      // yetersiz + veritabanında eski kayıtlar olabilir → arka plan refresh işi
      enqueueJob('refresh', { query, inserted });
    }
    return { records, pages };
  } catch (e) {
    // YouTube erişilemez (ağ/ban) → yerel veriye düş (fail-safe)
    return { records: [], pages: 0 };
  }
}

/* ---------- Ana akış ---------- */

export async function search(o: SearchOptions): Promise<SearchResultPayload> {
  const t0 = Date.now();
  const query = o.extraQuery ? `${o.query} ${o.extraQuery}` : o.query;
  // v2: aiPrompt + sürüm tuzu — eski (url'siz) cache girişleri devre dışı
  const key = cacheKey(['v2', query, o.limit, o.page, o.filter, o.sortBy, o.minViews, o.maxViews, o.duration, o.type, o.uploadDate, o.ai, o.deep, o.explain, o.aiPrompt]);

  // 1) RAM
  const hit = ram.get(key);
  if (hit) return { ...hit, meta: { ...hit.meta, tookMs: Date.now() - t0, source: 'ram' } };

  // 2) Disk (offline burada yakalanır)
  const dHit = disk.get(key);
  if (dHit) {
    ram.set(key, dHit);
    return { ...dHit, meta: { ...dHit.meta, tookMs: Date.now() - t0, source: 'disk' } };
  }

  // 3) DB (FTS)
  const offset = (Math.max(1, o.page) - 1) * o.limit;
  let pool = ftsSearch(query, { limit: o.limit * 4 + 200, offset: 0, sortBy: o.sortBy });
  let source: SearchResultPayload['meta']['source'] = 'db';
  let collected = 0;
  let expandedQueries: string[] | undefined;

  // 4) AI: analiz (filtre + ek sorgu) ile toplamayı PARALEL başlat (hız)
  const aiOn = (o.ai || o.explain) && aiAvailable();
  const analysisP = aiOn ? analyzeQuery(o.query, o.extraQuery, o.aiPrompt) : Promise.resolve(null);
  const offline = o.offline || config.offline;
  const collectP = !offline && pool.length < o.limit
    ? collect(o, o.query)
    : Promise.resolve({ records: [] as VideoRecord[], pages: 0 });

  const analysis = await analysisP;
  if (analysis?.expandedQueries?.length) expandedQueries = analysis.expandedQueries;
  if (o.deep) {
    const extra = await expandQueries(o.query, o.aiPrompt).catch(() => []);
    if (extra.length) expandedQueries = [...new Set([...(expandedQueries ?? []), ...extra])];
  }
  if (expandedQueries?.length) {
    saveAiQuery(o.query, expandedQueries);
    const seen = new Set(pool.map((v) => v.youtubeId));
    for (const eq of expandedQueries) {
      for (const v of ftsSearch(eq, { limit: 100, offset: 0, sortBy: o.sortBy })) {
        if (!seen.has(v.youtubeId)) {
          seen.add(v.youtubeId);
          pool.push(v);
        }
      }
    }
  }

  // 5) Collector (başlatılmıştı — sonucunu bekle; boşsa offline dayanıklılık)
  const c = await collectP;
  if (c.records.length) {
    source = 'youtube';
    collected = c.records.length;
    const seen = new Set(pool.map((v) => v.youtubeId));
    for (const v of c.records) if (!seen.has(v.youtubeId)) { seen.add(v.youtubeId); pool.push(v); }
  }
  if (!offline && pool.length === 0 && countVideos() > 0) {
    pool = ftsSearch(query, { limit: o.limit * 4, offset: 0, sortBy: o.sortBy });
  }

  // 6) Filtre + skor — AI filtreleri yalnız CLI sessizse devreye girer; boş sonuç → geri sar
  const cliFilters = o;
  const aiF = aiOn ? analysis?.filters : undefined;
  let poolF = applyFilters(pool, cliFilters);
  if (aiF && poolF.length) {
    const merged: SearchOptions = {
      ...cliFilters,
      ...(cliFilters.filter ? {} : aiF.filter ? { filter: aiF.filter } : {}),
      ...(cliFilters.minViews != null ? {} : aiF.minViews != null ? { minViews: aiF.minViews } : {}),
      ...(cliFilters.duration ? {} : aiF.duration ? { duration: aiF.duration } : {}),
      ...(cliFilters.uploadDate ? {} : aiF.uploadDate ? { uploadDate: aiF.uploadDate } : {}),
      ...(cliFilters.sortBy && cliFilters.sortBy !== 'relevance' ? {} : aiF.sortBy ? { sortBy: aiF.sortBy } : {}),
    };
    const withAi = applyFilters(poolF, merged);
    poolF = withAi.length ? withAi : poolF; // AI filtresi her şeyi elerse CLI filtresine dön
  }
  const qTerms = query.toLowerCase().split(/\s+/).filter(Boolean);
  let results: ScoredResult[] = poolF.map((v) => ({ ...v, score: deterministicScore(v, qTerms) }));

  // 7) AI skor + gerekçe (ai/explain açıkken, ilk 8 aday — reasoning modellerde latency dengesi)
  if (aiOn && results.length) {
    const top = results.slice(0, 8);
    const scores = await scoreResults(
      query,
      top.map((r) => ({ id: r.youtubeId, title: r.title, channel: r.channelName, views: r.viewCount, duration: r.durationSec, published: r.publishedAt })),
      o.aiPrompt,
    );
    if (config.logLevel === 'debug') {
      console.error(`[ai] scoreResults=${scores ? `anahtar: ${Object.keys(scores).length}` : 'NULL (timeout/model hatası)'}`);
    }
    if (scores) {
      for (const r of top) {
        const s = scores[r.youtubeId];
        if (s) {
          r.aiScore = Math.max(0, Math.min(100, Math.round(s.score)));
          r.aiReason = s.reason;
          if (o.explain && r.matchPct == null) r.matchPct = r.aiScore;
        }
      }
    }
  }
  if (o.minScore != null) results = results.filter((r) => (r.aiScore ?? r.score) >= o.minScore!);
  if (o.quality != null) results = results.filter((r) => (r.aiScore ?? r.score) >= o.quality!);

  results = sortList(results, o.sortBy).slice(offset, offset + o.limit);
  const withUrl = results.map((r) => ({ ...r, url: `https://www.youtube.com/watch?v=${r.youtubeId}` }));

  // uygulanan filtreler (zengin meta için)
  const filters: Partial<SearchFilters> = {};
  if (o.filter) filters.filter = o.filter;
  if (o.sortBy) filters.sortBy = o.sortBy;
  if (o.minViews != null) filters.minViews = o.minViews;
  if (o.maxViews != null) filters.maxViews = o.maxViews;
  if (o.duration) filters.duration = o.duration;
  if (o.type) filters.type = o.type;
  if (o.uploadDate) filters.uploadDate = o.uploadDate;
  if (o.minScore != null) filters.minScore = o.minScore;
  if (o.quality != null) filters.quality = o.quality;

  const payload: SearchResultPayload = {
    results: withUrl,
    meta: {
      query,
      source,
      total: withUrl.length,
      tookMs: Date.now() - t0,
      aiEnabled: aiOn,
      offline,
      ...(expandedQueries ? { expandedQueries } : {}),
      collected,
      limit: o.limit,
      page: o.page,
      aiScored: withUrl.filter((r) => r.aiScore != null).length,
      dbVideos: countVideos(),
      ...(o.aiPrompt ? { aiPrompt: o.aiPrompt, aiPromptApplied: aiOn } : {}),
      ...(Object.keys(filters).length ? { filters } : {}),
    },
  };

  ram.set(key, payload);
  disk.set(key, payload);
  saveHistory(query, withUrl.length);
  return payload;
}
