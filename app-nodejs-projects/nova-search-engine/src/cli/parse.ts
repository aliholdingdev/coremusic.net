/** Parametrik argüman ayrıştırıcı — menü yok. Hem `-q` hem `--query` hem `-query` kabul eder. */
import type { SearchOptions } from '../core/types.ts';
import { extractVideoId } from '../core/video-detail.ts';

export class ArgError extends Error {
  constructor(message: string) {
    super(message);
    this.name = 'ArgError';
  }
}

export interface ParsedArgs {
  command: string;
  positionals: string[];
  flags: Map<string, string | true>;
}

const VALUE_FLAGS = new Set([
  'query', 'q', 'extraQuery', 'eq', 'result', 'r', 'limit', 'l',
  'filter', 'f', 'sortBy', 'sb', 'minViews', 'minv', 'maxViews', 'maxv',
  'minScore', 'mins', 'quality', 'qa', 'duration', 'd', 'page', 'p',
  'type', 't', 'uploadDate', 'udate', 'ud', 'aiQuery', 'aiPrompt', 'aip', 'target',
  'comments', 'related',
]);

/** Opsiyonel değerli bayraklar: `-s 3` = 3 sayfa · `-s` (değersiz) = auto (tüm sayfalar). */
const OPTIONAL_VALUE_FLAGS = new Set(['scroll', 's']);

const BOOL_FLAGS = new Set(['ai', 'deep', 'explain', 'offline', 'json', 'help', 'h', 'version', 'v', 'once', 'prune']);

/** normalize: `-query` → `query`, `--query` → `query`, `-Q` → `q` (tek harf) */
function normFlag(raw: string): string {
  let f = raw.replace(/^--?/, '');
  if (f.length === 1) return f.toLowerCase(); // tek harf kısayollar: q, s, r...
  return f;
}

export function parseArgs(argv: string[]): ParsedArgs {
  const flags = new Map<string, string | true>();
  const positionals: string[] = [];
  let command = '';
  let i = 0;

  while (i < argv.length) {
    const a = argv[i];
    if (a.startsWith('-')) {
      const raw = a.replace(/^--?/, '');
      const eq = raw.indexOf('=');
      if (eq > 0) {
        flags.set(raw.slice(0, eq), raw.slice(eq + 1));
        i++;
        continue;
      }
      const name = normFlag(a);
      if (OPTIONAL_VALUE_FLAGS.has(name)) {
        // `-s 3` = değerli · `-s` (sonraki yok veya bayrak) = değersiz → 'auto'
        const next = argv[i + 1];
        if (next !== undefined && (!next.startsWith('-') || /^-\d/.test(next))) {
          flags.set(name, next);
          i += 2;
        } else {
          flags.set(name, 'auto');
          i++;
        }
      } else if (VALUE_FLAGS.has(name)) {
        const next = argv[i + 1];
        if (next === undefined || (next.startsWith('-') && !/^-\d/.test(next))) {
          throw new ArgError(`-${name} bir değer bekliyor (örn: -${name} 10)`);
        }
        flags.set(name, next);
        i += 2;
      } else if (BOOL_FLAGS.has(name)) {
        flags.set(name, true);
        i++;
      } else {
        throw new ArgError(`Bilinmeyen parametre: ${a} — -help ile listeyi görün`);
      }
    } else {
      if (!command) command = a;
      else positionals.push(a);
      i++;
    }
  }

  if (!command && positionals.length) command = positionals.shift()!;
  return { command, positionals, flags };
}

function num(v: string | true | undefined, name: string): number | undefined {
  if (v === undefined || v === true) return undefined;
  const n = Number(v);
  if (!Number.isFinite(n)) throw new ArgError(`-${name} sayısal olmalı (gelen: ${v})`);
  return n;
}

const SORT = new Set(['relevance', 'views', 'date', 'rating']);
const FILTER = new Set(['v', 's', 'sh']);

/** -s/--scroll: sayı = N sayfa · 'auto' (değersiz) = -1 → tüm sayfalar (COLLECT_AUTO_MAX_PAGES). */
export function scrollOf(flags: ParsedArgs['flags']): number {
  const v = flags.get('s') ?? flags.get('scroll');
  if (v === undefined || v === true) return 1;
  if (v === 'auto' || v === 'all') return -1;
  const n = Number(v);
  if (!Number.isFinite(n)) throw new ArgError(`-s sayısal olmalı veya auto (gelen: ${v})`);
  return Math.max(1, Math.trunc(n));
}

export function buildSearchOptions(parsed: ParsedArgs): SearchOptions {
  const f = parsed.flags;
  const query = parsed.positionals.join(' ').trim() || (typeof f.get('query') === 'string' ? '' : '');
  const explicitQuery = typeof f.get('q') === 'string' ? (f.get('q') as string) : undefined;

  // sorgu: positional ÖNCELIKLİ; -q tek başına verilmişse sorgu olarak kullanılır
  let finalQuery = query;
  let promptFromQ: string | undefined;
  if (!finalQuery && explicitQuery) finalQuery = explicitQuery;
  else promptFromQ = explicitQuery;

  if (!finalQuery) throw new ArgError('Sorgu gerekli: nova search "adele"');

  // AI prompt: -aip / --aiPrompt / --aiQuery; hiçbiri yoksa positional sorgu varken -q değeri
  const str = (k: string): string | undefined => (typeof f.get(k) === 'string' ? (f.get(k) as string) : undefined);
  const aiPrompt = str('aip') ?? str('aiPrompt') ?? str('aiQuery') ?? promptFromQ;

  const filter = typeof f.get('f') === 'string' ? (f.get('f') as string) : typeof f.get('filter') === 'string' ? (f.get('filter') as string) : undefined;
  if (filter && !FILTER.has(filter)) throw new ArgError(`-f yalnız v|s|sh (gelen: ${filter})`);

  const sortByRaw = typeof f.get('sb') === 'string' ? (f.get('sb') as string) : typeof f.get('sortBy') === 'string' ? (f.get('sortBy') as string) : undefined;
  if (sortByRaw && !SORT.has(sortByRaw)) throw new ArgError(`-sb relevance|views|date|rating (gelen: ${sortByRaw})`);

  const limit = num(f.get('r') ?? f.get('l'), 'r') ?? num(f.get('limit'), 'limit') ?? 24;
  const resultLimit = num(f.get('result'), 'result');
  const finalLimit = resultLimit ?? limit;

  return {
    query: finalQuery,
    ...(aiPrompt ? { aiPrompt } : {}),
    ...(typeof f.get('eq') === 'string' ? { extraQuery: f.get('eq') as string } : {}),
    ai: f.get('ai') === true,
    deep: f.get('deep') === true,
    explain: f.get('explain') === true,
    offline: f.get('offline') === true || configOffline(),
    scroll: scrollOf(f),
    limit: Math.max(1, Math.min(finalLimit, 5000)), // üst sınır 5000+
    page: Math.max(1, num(f.get('p') ?? f.get('page'), 'p') ?? 1),
    ...(filter ? { filter: filter as 'v' | 's' | 'sh' } : {}),
    ...(sortByRaw ? { sortBy: sortByRaw as SearchOptions['sortBy'] } : {}),
    ...(num(f.get('minv') ?? f.get('minViews'), 'minv') !== undefined ? { minViews: num(f.get('minv') ?? f.get('minViews'), 'minv')! } : {}),
    ...(num(f.get('maxv') ?? f.get('maxViews'), 'maxv') !== undefined ? { maxViews: num(f.get('maxv') ?? f.get('maxViews'), 'maxv')! } : {}),
    ...(num(f.get('mins') ?? f.get('minScore'), 'mins') !== undefined ? { minScore: num(f.get('mins') ?? f.get('minScore'), 'mins')! } : {}),
    ...(num(f.get('qa') ?? f.get('quality'), 'qa') !== undefined ? { quality: num(f.get('qa') ?? f.get('quality'), 'qa')! } : {}),
    ...(typeof f.get('d') === 'string' ? { duration: f.get('d') as string } : {}),
    ...(typeof f.get('t') === 'string' ? { type: f.get('t') as string } : {}),
    ...(typeof f.get('ud') === 'string' || typeof f.get('udate') === 'string'
      ? { uploadDate: (f.get('ud') ?? f.get('udate')) as string }
      : {}),
  };
}

function configOffline(): boolean {
  return process.env.NOVA_OFFLINE === '1';
}

export interface VideoOptions {
  videoId: string;
  comments: number;
  related: number;
  ai: boolean;
  json: boolean;
  offline: boolean;
}

/** `nova video <id|url>` seçenekleri — ID/URL ayrıştırma + clamp0-50 /0-20. */
export function buildVideoOptions(parsed: ParsedArgs): VideoOptions {
  const f = parsed.flags;
  const input = parsed.positionals.join(' ').trim();
  if (!input) throw new ArgError('Video ID veya URL gerekli: nova video <id|url>');
  const id = extractVideoId(input);
  if (!id) throw new ArgError(`Geçersiz video ID/URL: ${input}`);

  const clamp = (name: string, max: number): number => {
    const n = num(f.get(name), name);
    if (n == null) return 0;
    return Math.max(0, Math.min(Math.trunc(n), max));
  };

  return {
    videoId: id,
    comments: clamp('comments', 50),
    related: clamp('related', 20),
    ai: f.get('ai') === true,
    json: f.get('json') === true,
    offline: f.get('offline') === true || configOffline(),
  };
}
