// ===========================================
// NovaSearch API — Fastify
// Yüzey: /api/search · /api/videos/:id · /api/stats
//        /api/suggest · /api/health + statik web
// ===========================================

import Fastify from 'fastify';
import cors from '@fastify/cors';
import fastifyStatic from '@fastify/static';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';
import { ProgressBar } from '@novasearch/common';
import { createEngineFromEnv, type EngineContext } from '@novasearch/search';
import type { SearchQuery } from '@novasearch/common';
import { banner, logLine, requestLog } from './logger.js';

const __dirname = dirname(fileURLToPath(import.meta.url));

function parseSearchQuery(q: Record<string, string | undefined>): { query: string; options: SearchQuery } {
  const query = q['q'] ?? '';
  const aiRaw = String(q['ai'] ?? '').toLowerCase();
  return {
    query,
    options: {
      query,
      ai: aiRaw === 'true' || aiRaw === '1' || aiRaw === 'yes' || aiRaw === 'on',
      aiQuery: q['aiQuery'],
      extraQuery: q['extraQuery'],
      result: parseInt(q['result'] ?? '20', 10) || 20,
      scroll: parseInt(q['scroll'] ?? '50', 10) || 50,
      filter: (q['filter'] as SearchQuery['filter']) ?? 'all',
      type: (q['type'] as SearchQuery['type']) ?? 'all',
      uploadDate: (q['uploadDate'] as SearchQuery['uploadDate']) ?? 'all',
      sortBy: (q['sortBy'] as SearchQuery['sortBy']) ?? 'relevance',
      minViews: parseInt(q['minViews'] ?? '0', 10) || 0,
      maxViews: parseInt(q['maxViews'] ?? '0', 10) || 0,
      minScore: parseFloat(q['minScore'] ?? '0') || 0,
      duration: (q['duration'] as SearchQuery['duration']) ?? 'all',
      quality: (q['quality'] as SearchQuery['quality']) ?? 'all',
      page: parseInt(q['page'] ?? '1', 10) || 1,
      limit: parseInt(q['limit'] ?? '20', 10) || 20,
      offline: q['offline'] === 'true',
      auto: q['auto'] !== 'false',
    },
  };
}

export async function buildServer(ctx: EngineContext) {
  const app = Fastify({ logger: false }); // JSON log yok; okunur terminal logu kullanılır

  // --- İstek logu (terminal stili) + monitoring sayaçları ---
  const metrics = { requests: 0, errors: 0, totalMs: 0, byPath: {} as Record<string, number> };
  app.addHook('onResponse', (req, reply, done) => {
    const ms = reply.elapsedTime ?? 0;
    metrics.requests++;
    metrics.totalMs += ms;
    if (reply.statusCode >= 400) metrics.errors++;
    const key = req.routeOptions?.url ?? req.url.split('?')[0] ?? 'unknown';
    metrics.byPath[key] = (metrics.byPath[key] ?? 0) + 1;
    requestLog(req.method, req.url, reply.statusCode, ms);
    done();
  });

  await app.register(cors, { origin: true });

  // API kökü → web portuna yönlendirir
  app.get('/', async (_req, reply) => {
    const webPort = process.env['WEB_PORT'] ?? '3002';
    reply.redirect(`http://novasearch.coremusic.net:${webPort}`);
  });

  app.get('/api/health', async () => ({ status: 'ok', uptime: process.uptime() }));

  const searchHandler = async (req: { query: unknown }) => {
    const { query, options } = parseSearchQuery(req.query as Record<string, string | undefined>);
    if (!query) return { error: 'q parametresi gerekli' };
    // Duruma göre progressbar (terminal)
    const bar = new ProgressBar();
    return ctx.engine.search(options, (e) => bar.handle(e));
  };
  const statsHandler = async () => ({
    videoCount: await ctx.db.videos.count(),
    cache: ctx.cacheStats(),
    queue: ctx.collector.stats(),
    index: ctx.engine.indexStats(),
    recent: await ctx.db.searchLog.recent(10),
    topQueries: await ctx.db.searchLog.topQueries(10),
  });
  const videosHandler = async (req: { params: unknown }) => {
    const { id } = req.params as { id: string };
    const video = await ctx.db.videos.findByYoutubeId(id);
    return video ?? { error: 'video bulunamadı' };
  };

  // Hem /api/* hem /api/v1/* (Postman koleksiyonlarıyla uyum)
  app.get('/api/search', searchHandler);
  app.get('/api/v1/search', searchHandler);
  app.get('/api/stats', statsHandler);
  app.get('/api/v1/stats', statsHandler);
  app.get('/api/videos/:id', videosHandler);
  app.get('/api/v1/videos/:id', videosHandler);

  // Monitoring metrikleri
  const metricsHandler = async () => ({
    ...metrics,
    avgMs: metrics.requests > 0 ? Math.round(metrics.totalMs / metrics.requests) : 0,
    uptime: Math.round(process.uptime()),
  });
  app.get('/api/metrics', metricsHandler);
  app.get('/api/v1/metrics', metricsHandler);

  // Trending (CLI `trending` ile aynı)
  const trendingHandler = async (req: { query: unknown }) => {
    const q = req.query as Record<string, string | undefined>;
    const n = Math.max(1, parseInt(q['limit'] ?? '5', 10) || 5);
    return { top: await ctx.db.videos.topViewed(n), recent: await ctx.db.videos.recent(n) };
  };
  app.get('/api/trending', trendingHandler);
  app.get('/api/v1/trending', trendingHandler);

  // Metadata tazeleme (CLI `refresh` ile aynı)
  const refreshHandler = async (req: { query: unknown }) => {
    const q = req.query as Record<string, string | undefined>;
    const { parseNumber } = await import('@novasearch/common');
    const days = Math.max(0, parseNumber(q['days'], 7));
    const limit = Math.max(1, parseNumber(q['limit'], 20));
    const updated = await ctx.collector.refreshStale(days, limit);
    return { updated, days, limit };
  };
  app.get('/api/refresh', refreshHandler);
  app.get('/api/v1/refresh', refreshHandler);

  // Yedek (CLI `backup` ile aynı)
  const backupHandler = async () => {
    const { mkdirSync } = await import('node:fs');
    const { resolve } = await import('node:path');
    const dir = resolve(process.cwd(), 'data', 'backups');
    mkdirSync(dir, { recursive: true });
    const stamp = new Date().toISOString().replace(/[:.]/g, '-');
    if (ctx.db.adapter.dialect === 'sqlite') {
      const target = resolve(dir, `novasearch-${stamp}.db`);
      await ctx.db.adapter.exec(`VACUUM INTO '${target.replace(/'/g, "''")}'`);
      return { file: target };
    }
    return { note: 'MySQL için mysqldump kullanın' };
  };
  app.get('/api/backup', backupHandler);
  app.get('/api/v1/backup', backupHandler);

  // Derin veri: video akış/format bilgisi (GoogleVideo)
  const streamHandler = async (req: { params: unknown }) => {
    const { id } = req.params as { id: string };
    try {
      const { getStreamInfo } = await import('@novasearch/youtube');
      const info = await getStreamInfo(id);
      return info ?? { error: 'akış bilgisi bulunamadı' };
    } catch (err) {
      return { error: err instanceof Error ? err.message : 'akış alınamadı' };
    }
  };
  app.get('/api/videos/:id/stream', streamHandler);
  app.get('/api/v1/videos/:id/stream', streamHandler);

  const suggestHandler = async (req: { query: unknown }) => {
    const { q } = req.query as { q?: string };
    if (!q) return { suggestions: [] };
    try {
      const { getSearchSuggestions } = await import('@novasearch/youtube');
      return { suggestions: await getSearchSuggestions(q) };
    } catch {
      return { suggestions: [] };
    }
  };
  app.get('/api/suggest', suggestHandler);
  app.get('/api/v1/suggest', suggestHandler);

  return app;
}

/** Web için statik sunucu (ayrı port). */
export async function buildWebServer() {
  const web = Fastify({ logger: false });
  await web.register(fastifyStatic, {
    root: join(__dirname, '..', '..', 'web'),
    prefix: '/',
  });
  return web;
}

export async function start(): Promise<void> {
  const ctx = await createEngineFromEnv();
  const api = await buildServer(ctx);
  const web = await buildWebServer();

  const apiPort = parseInt(process.env['PORT'] ?? '3001', 10);
  const webPort = parseInt(process.env['WEB_PORT'] ?? '3002', 10);
  const host = process.env['HOST'] ?? '127.0.0.1';

  await api.listen({ port: apiPort, host });
  await web.listen({ port: webPort, host });

  banner([
    'NovaSearch AI çalışıyor',
    `API  : http://novasearch.coremusic.net:${apiPort}`,
    `Web  : http://novasearch.coremusic.net:${webPort}`,
    `Admin: http://novasearch.coremusic.net:${webPort}/admin.html`,
  ]);
  logLine('İstekler bu terminalde okunur biçimde görünür.', 'cyan');
}
