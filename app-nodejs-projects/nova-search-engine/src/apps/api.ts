/** API Gateway — REST uçları, zarf formatı, hız sınırı. Port: API_PORT (3100). */
import { createServer, type IncomingMessage, type ServerResponse } from 'node:http';
import { search } from '../core/search.ts';
import { videoDetail } from '../core/video-detail.ts';
import { countVideos, trending, suggest, getVideo, saveHistory } from '../database/db.ts';
import { aiAvailable } from '../ai/engine.ts';
import { config } from '../config.ts';
import type { SearchOptions } from '../core/types.ts';

interface Envelope {
  ok: boolean;
  data?: unknown;
  meta?: Record<string, unknown>;
  error?: { code: string; message: string };
}

/** Basit IP hız sınırı: 60 istek/dk. */
const hits = new Map<string, number[]>();
function rateLimited(ip: string): boolean {
  const now = Date.now();
  const arr = (hits.get(ip) ?? []).filter((t) => now - t < 60_000);
  if (arr.length >= 60) {
    hits.set(ip, arr);
    return true;
  }
  arr.push(now);
  hits.set(ip, arr);
  return false;
}

function send(res: ServerResponse, status: number, body: Envelope): void {
  const json = JSON.stringify(body);
  res.writeHead(status, {
    'Content-Type': 'application/json; charset=utf-8',
    'Access-Control-Allow-Origin': '*',
    'Cache-Control': 'no-store',
  });
  res.end(json);
}

function q(url: URL, key: string): string | undefined {
  return url.searchParams.get(key) ?? undefined;
}

function numParam(url: URL, key: string): number | undefined {
  const v = url.searchParams.get(key);
  if (v == null) return undefined;
  const n = Number(v);
  return Number.isFinite(n) ? n : undefined;
}

async function route(req: IncomingMessage, res: ServerResponse): Promise<void> {
  const url = new URL(req.url ?? '/', `http://${req.headers.host ?? 'localhost'}`);
  const path = url.pathname.replace(/\/+$/, '') || '/';

  if (req.method === 'OPTIONS') {
    res.writeHead(204, {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'GET,OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type',
    });
    res.end();
    return;
  }

  if (path.startsWith('/api/v1/')) {
    const ip = req.socket.remoteAddress ?? 'unknown';
    if (rateLimited(ip)) {
      send(res, 429, { ok: false, error: { code: 'RATE_LIMIT', message: 'Dakikada 60 istek sınırı' } });
      return;
    }

    if (path === '/api/v1/health') {
      send(res, 200, {
        ok: true,
        data: { status: 'up', videos: countVideos(), aiEnabled: aiAvailable(), offline: config.offline },
      });
      return;
    }

    if (path === '/api/v1/search') {
      const query = q(url, 'q');
      if (!query) {
        send(res, 400, { ok: false, error: { code: 'BAD_REQUEST', message: 'q parametresi gerekli' } });
        return;
      }
      const filterRaw = q(url, 'filter');
      const sortRaw = q(url, 'sort');
      const opts: SearchOptions = {
        query,
        limit: Math.min(Math.max(numParam(url, 'limit') ?? 24, 1), 5000),
        page: numParam(url, 'page') ?? 1,
        ai: q(url, 'ai') === '1',
        deep: q(url, 'deep') === '1',
        explain: q(url, 'explain') === '1',
        offline: q(url, 'offline') === '1' || config.offline,
        // scroll: N sayfa · scroll=all|auto → -1 (tüm sayfalar, COLLECT_AUTO_MAX_PAGES)
        scroll: q(url, 'scroll') === 'all' || q(url, 'scroll') === 'auto'
          ? -1
          : numParam(url, 'scroll') ?? 1,
        ...(filterRaw === 'v' || filterRaw === 's' || filterRaw === 'sh' ? { filter: filterRaw } : {}),
        ...(sortRaw === 'views' || sortRaw === 'date' || sortRaw === 'rating' || sortRaw === 'relevance'
          ? { sortBy: sortRaw as SearchOptions['sortBy'] }
          : {}),
        ...(numParam(url, 'minViews') !== undefined ? { minViews: numParam(url, 'minViews') } : {}),
        ...(numParam(url, 'maxViews') !== undefined ? { maxViews: numParam(url, 'maxViews') } : {}),
        ...(numParam(url, 'minScore') !== undefined ? { minScore: numParam(url, 'minScore') } : {}),
        ...(numParam(url, 'quality') !== undefined ? { quality: numParam(url, 'quality') } : {}),
        ...(q(url, 'duration') ? { duration: q(url, 'duration')! } : {}),
        ...(q(url, 'type') ? { type: q(url, 'type')! } : {}),
        ...(q(url, 'uploadDate') ? { uploadDate: q(url, 'uploadDate')! } : {}),
        ...(q(url, 'prompt') ? { aiPrompt: q(url, 'prompt')! } : {}),
      };
      const payload = await search(opts);
      send(res, 200, { ok: true, data: payload.results, meta: payload.meta });
      return;
    }

    if (path === '/api/v1/suggest') {
      const prefix = q(url, 'prefix') ?? '';
      send(res, 200, { ok: true, data: suggest(prefix, numParam(url, 'limit') ?? 8) });
      return;
    }

    if (path === '/api/v1/trending') {
      send(res, 200, { ok: true, data: trending(numParam(url, 'limit') ?? 24) });
      return;
    }

    if (path.startsWith('/api/v1/videos/')) {
      const rawId = decodeURIComponent(path.slice('/api/v1/videos/'.length));
      const wantsDeep =
        url.searchParams.has('comments') || url.searchParams.has('related') || url.searchParams.has('ai');
      if (!wantsDeep) {
        // eski davranış (backward compatible): sadece DB kaydı
        const v = getVideo(rawId);
        if (!v) {
          send(res, 404, { ok: false, error: { code: 'NOT_FOUND', message: `video yok: ${rawId}` } });
          return;
        }
        send(res, 200, { ok: true, data: v });
        return;
      }
      // derin detay: video + yorumlar + benzer videolar (AI skorlu) — tek zarf, fail-safe
      const payload = await videoDetail(rawId, {
        comments: Math.min(Math.max(numParam(url, 'comments') ?? 0, 0), 50),
        related: Math.min(Math.max(numParam(url, 'related') ?? 0, 0), 20),
        ai: q(url, 'ai') === '1',
        offline: q(url, 'offline') === '1' || config.offline,
      });
      if (!payload) {
        send(res, 400, { ok: false, error: { code: 'BAD_REQUEST', message: `geçersiz video ID/URL: ${rawId}` } });
        return;
      }
      send(res, 200, { ok: true, data: payload });
      return;
    }

    send(res, 404, { ok: false, error: { code: 'NOT_FOUND', message: `bilinmeyen uç: ${path}` } });
    return;
  }

  send(res, 404, { ok: false, error: { code: 'NOT_FOUND', message: 'yalnız /api/v1/*' } });
}

export function startApi(): Promise<void> {
  const server = createServer((req, res) => {
    route(req, res).catch((e: unknown) => {
      send(res, 500, { ok: false, error: { code: 'INTERNAL', message: e instanceof Error ? e.message : 'hata' } });
    });
  });
  return new Promise((resolve, reject) => {
    server.once('error', reject); // EADDRINUSE vb. → serve yakalar ve dostça basar
    server.listen(config.api.port, config.api.host, () => {
      console.log(`NovaSearch API: http://${config.api.host}:${config.api.port}/api/v1/health`);
      resolve();
    });
  });
}
