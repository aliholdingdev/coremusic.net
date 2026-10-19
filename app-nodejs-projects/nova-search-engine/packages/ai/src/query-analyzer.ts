// ===========================================
// Query Analyzer — sorguyu "anla"
// Çekirdek: her zaman çalışır (heuristic).
// AI: varsa genişletir, yoksa aramayı ASLA bloklamaz.
// ===========================================

import type { OpenRouterClient } from './client.js';

export interface QueryHints {
  wantsLive: boolean;
  wantsOfficial: boolean;
  wantsCover: boolean;
  wantsRemix: boolean;
  language: 'tr' | 'en' | 'unknown';
  yearFrom?: number;
  yearTo?: number;
}

export interface QueryAnalysis {
  original: string;
  terms: string[];
  hints: QueryHints;
  /** YouTube'a gidecek sorgu varyantları (deep search için) */
  expandedQueries: string[];
}

const STOPWORDS = new Set([
  've', 'ile', 'için', 'bir', 'bu', 'şu', 'the', 'of', 'a', 'an', 'in', 'on', 'for', 'to', 'by',
]);

function tokenize(query: string): string[] {
  return query
    .toLowerCase()
    .replace(/[^\p{L}\p{N}\s]/gu, ' ')
    .split(/\s+/)
    .filter((t) => t.length > 1 && !STOPWORDS.has(t));
}

function detectHints(query: string, terms: string[]): QueryHints {
  const q = query.toLowerCase();
  const hints: QueryHints = {
    wantsLive: /canlı|live/.test(q),
    wantsOfficial: /resmi|official/.test(q),
    wantsCover: /cover/.test(q),
    wantsRemix: /remix|rmx/.test(q),
    language: /[çğıöşü]|türkçe|akasya|şarkı|konser/.test(q) ? 'tr' : /[a-z]/.test(q) ? 'en' : 'unknown',
  };
  // Yıl aralığı: "2010-2015" veya "2015"
  const range = q.match(/\b(19|20)\d{2}\s*[-–]\s*(19|20)\d{2}\b/);
  if (range) {
    const [from, to] = range[0].split(/[-–]/).map((s) => parseInt(s.trim(), 10));
    if (from && to) {
      hints.yearFrom = from;
      hints.yearTo = to;
    }
  } else {
    const year = q.match(/\b((?:19|20)\d{2})\b/);
    if (year?.[1]) {
      hints.yearFrom = parseInt(year[1], 10);
      hints.yearTo = hints.yearFrom;
    }
  }
  void terms;
  return hints;
}

/** Heuristic genişletme: ipucu bazlı varyantlar (deep search tohumları). */
function heuristicExpand(query: string, hints: QueryHints): string[] {
  const variants = new Set<string>([query]);
  if (hints.wantsLive) variants.add(`${query} live`);
  if (hints.wantsOfficial) variants.add(`${query} official`);
  if (hints.yearFrom && hints.yearTo && hints.yearFrom !== hints.yearTo) {
    for (let y = hints.yearFrom; y <= hints.yearTo; y++) {
      variants.add(`${query} ${y}`);
    }
  }
  return [...variants].slice(0, 8);
}

export interface AnalyzeOptions {
  ai?: boolean;
  aiQuery?: string;
  extraQuery?: string;
  client?: OpenRouterClient;
}

export async function analyzeQuery(query: string, opts: AnalyzeOptions = {}): Promise<QueryAnalysis> {
  const terms = tokenize(query);
  const hints = detectHints(query, terms);
  let expandedQueries = heuristicExpand(query, hints);

  const directive = [opts.aiQuery, opts.extraQuery].filter(Boolean).join(' | ');
  if (opts.ai && opts.client?.enabled) {
    const aiResult = await opts.client.chatJSON<{
      expandedQueries?: string[];
      wantsLive?: boolean;
      wantsOfficial?: boolean;
    }>(
      [
        {
          role: 'system',
          content:
            'Sen bir arama sorgusu uzmanısın. Kullanıcının YouTube aramasını daha isabetli yapmak için ' +
            '3-5 arama sorgusu varyantı üret. SADECE JSON döndür: {"expandedQueries": ["...","..."]}',
        },
        {
          role: 'user',
          content: JSON.stringify({ query, directive: directive || undefined }),
        },
      ],
      { expandedQueries: [] },
    );
    if (Array.isArray(aiResult.expandedQueries) && aiResult.expandedQueries.length > 0) {
      expandedQueries = [
        ...new Set([...expandedQueries, ...aiResult.expandedQueries.map((s) => String(s)).slice(0, 5)]),
      ];
    }
    if (aiResult.wantsLive === true) hints.wantsLive = true;
    if (aiResult.wantsOfficial === true) hints.wantsOfficial = true;
  }

  return { original: query, terms, hints, expandedQueries };
}
