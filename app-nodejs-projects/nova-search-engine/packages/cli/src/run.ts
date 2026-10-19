// ===========================================
// Ortak çalıştırma katmanı — her iki CLI da
// (parametrik + menü) aynı işi buradan çağırır.
// ===========================================

import {
  SEARCH,
  parseNumber,
  type ContentFilter,
  type DurationFilter,
  type QualityFilter,
  type SearchProgressEvent,
  type SearchQuery,
  type SearchResponse,
  type SortBy,
  type SearchType,
  type UploadDate,
} from '@novasearch/common';
import { createEngineFromEnv, type EngineContext } from '@novasearch/search';

export type RawOptions = Record<string, string | boolean | undefined>;

export function buildQuery(query: string, o: RawOptions): SearchQuery {
  const result = clampInt(o['result'], SEARCH.defaultResult, 1, SEARCH.maxResult);
  const scroll = clampInt(o['scroll'], Math.max(SEARCH.defaultScroll, result), 1, SEARCH.maxScroll);
  return {
    query,
    ai: Boolean(o['ai']),
    aiQuery: typeof o['query'] === 'string' ? o['query'] : undefined,
    extraQuery: typeof o['extraQuery'] === 'string' ? o['extraQuery'] : undefined,
    result,
    scroll,
    filter: (o['filter'] as ContentFilter) ?? 'all',
    type: (o['type'] as SearchType) ?? 'all',
    uploadDate: (o['uploadDate'] as UploadDate) ?? 'all',
    sortBy: (o['sortBy'] as SortBy) ?? 'relevance',
    minViews: parseNumber(o['minViews'], 0),
    maxViews: parseNumber(o['maxViews'], 0),
    minScore: parseFloat(String(o['minScore'] ?? '0')) || 0,
    duration: (o['duration'] as DurationFilter) ?? 'all',
    quality: (o['quality'] as QualityFilter) ?? 'all',
    page: parseNumber(o['page'], 1),
    limit: parseNumber(o['limit'], result),
    offline: Boolean(o['offline']),
    auto: o['auto'] !== false,
  };
}

function clampInt(value: string | boolean | undefined, def: number, min: number, max: number): number {
  const n = parseNumber(value, def);
  return Math.min(Math.max(n, min), max);
}

export async function withEngine<T>(fn: (ctx: EngineContext) => Promise<T>): Promise<T> {
  const ctx = await createEngineFromEnv();
  try {
    return await fn(ctx);
  } finally {
    await ctx.close();
  }
}

export async function runSearch(
  query: string,
  o: RawOptions,
  onProgress?: (event: SearchProgressEvent) => void,
): Promise<SearchResponse> {
  return withEngine((ctx) => ctx.engine.search(buildQuery(query, o), onProgress));
}
