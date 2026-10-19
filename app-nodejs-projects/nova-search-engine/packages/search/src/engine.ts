// ===========================================
// Search Engine — Karar Zinciri (prompt'taki akış):
//
//   Kullanıcı → Cache (RAM→Disk) → Yerel DB
//     → yetersiz & online ise YouTube (collector)
//     → normalize + dedup + DB yaz → rank → yanıt
//
// Kural: "Bir daha aynı veri asla çekilmeyecek."
// ===========================================

import {
  SEARCH,
  clamp,
  isNetworkError,
  type EnrichedVideo,
  type SearchProgressEvent,
  type SearchQuery,
  type SearchResponse,
  type Video,
} from '@novasearch/common';
import { HybridCache, cacheKeyFor } from '@novasearch/cache';
import type { Database } from '@novasearch/database';
import {
  OpenRouterClient,
  aiRelevanceFilter,
  analyzeQuery,
  loadAIConfig,
  rankVideos,
  type QueryAnalysis,
  type ScoredVideo,
} from '@novasearch/ai';
import type { CollectorWorker } from '@novasearch/workers';
import { applyFilters, paginate, sortVideos, toEnriched } from './ranking.js';
import { recommend } from './recommend.js';
import { SearchIndex } from './search-index.js';

export type ProgressCallback = (event: SearchProgressEvent) => void;

export interface SearchEngineDeps {
  db: Database;
  cache: HybridCache<Video[]>;
  collector: CollectorWorker;
  aiClient?: OpenRouterClient;
}

export class SearchEngine {
  private db: Database;
  private cache: HybridCache<Video[]>;
  private collector: CollectorWorker;
  private ai: OpenRouterClient;
  private index: SearchIndex;

  constructor(deps: SearchEngineDeps) {
    this.db = deps.db;
    this.cache = deps.cache;
    this.collector = deps.collector;
    this.ai = deps.aiClient ?? new OpenRouterClient(loadAIConfig());
    this.index = new SearchIndex(deps.db);
  }

  /** Dışarıdan indeks durumu (stats ekranı için). */
  indexStats() {
    return this.index.stats();
  }

  async search(q: SearchQuery, onProgress?: ProgressCallback): Promise<SearchResponse> {
    const started = Date.now();
    const offline = q.offline;
    const progress: ProgressCallback = onProgress ?? (() => undefined);

    // 1) Sorguyu anla + yerel katmanları PARALEL hazırla (hız: AI çağrısı
    //    yerel yüklemeyi bloklamaz). YouTube'a giden istekler ise kendi
    //    ritminde (jitter + token bucket) kalır — ban koruması değişmez.
    progress({ step: 'analiz', note: q.ai ? 'AI sorgu analizi…' : 'Sorgu analizi…' });
    const analysisPromise = analyzeQuery(q.query, {
      ai: q.ai,
      aiQuery: q.aiQuery,
      extraQuery: q.extraQuery,
      client: q.ai ? this.ai : undefined,
    });

    progress({ step: 'cache', note: 'Önbellek kontrolü…' });
    const key = cacheKeyFor('search', {
      q: q.query,
      f: q.filter,
      t: q.type,
      u: q.uploadDate,
      d: q.duration,
      s: q.sortBy,
    });
    const cached = this.cache.get(key);

    progress({ step: 'db', note: 'Search Index + yerel veritabanı…' });
    // Ağır indeks yüklemesi AI çağrısıyla paralel yürür; aday seçimi
    // analiz bittikten sonra (terimlerle) yapılır — doğruluk korunur.
    const loadPromise = this.index.ensureLoaded();

    const analysis = await analysisPromise;

    if (cached && cached.length > 0) {
      return this.respond(q, analysis, cached, 'cache', started, offline, progress);
    }

    // 3) Search Index → yerel adaylar (MSA: önce indexle, sonra alakalılar)
    await loadPromise;
    let local = await this.index.candidates(analysis.terms, Math.max(q.scroll, q.result, 100));
    let source: SearchResponse['source'] = 'local-db';

    // 4) Yeterli değilse ve online isek: YouTube'dan topla
    const wanted = clamp(q.result, 1, SEARCH.maxResult);
    if (!offline && local.length < wanted) {
      try {
        const collected: Video[] = [];
        const variants = analysis.expandedQueries.slice(0, 3);
        // Deep search: birincil sorgu + AI genişletmeleri (max 3 istek)
        for (let i = 0; i < variants.length; i++) {
          const query = variants[i];
          if (!query) continue;
          progress({
            step: 'toplama',
            current: i + 1,
            total: variants.length,
            note: `Toplanıyor: "${query}"`,
          });
          collected.push(...(await this.collector.collectNow(query, q)));
        }
        if (collected.length > 0) {
          const seen = new Set(local.map((v) => v.youtubeId));
          // İç dedup: farklı sorgu varyantlarından aynı video 2. kez eklenmesin
          const fresh = collected.filter((v) => {
            if (seen.has(v.youtubeId)) return false;
            seen.add(v.youtubeId);
            return true;
          });
          // Yeni kayıtlar indekse artımlı eklenir (sonraki aramada anında)
          this.index.add(fresh);
          local = [...local, ...fresh];
          source = local.length > collected.length ? 'mixed' : 'youtube';
        }
      } catch (err) {
        // Ağ yoksa sessizce yerel veriyle devam (offline deneyimi bozma)
        if (!isNetworkError(err)) throw err;
      }
    }

    return this.respond(q, analysis, local, source, started, offline, progress);
  }

  private async respond(
    q: SearchQuery,
    analysis: QueryAnalysis,
    videos: Video[],
    source: SearchResponse['source'],
    started: number,
    offline: boolean,
    progress: ProgressCallback,
  ): Promise<SearchResponse> {
    progress({ step: 'skorlama', note: 'Skorlama + sıralama…' });
    // Güvenlik dedup'u: aynı youtubeId tek sonuç olsun
    const unique = [...new Map(videos.map((v) => [v.youtubeId, v])).values()];
    let ranked = rankVideos(unique, analysis);

    // AI ilgi filtresi: --ai açıkken sonuçlar sorguya göre AI ile puanlanır;
    // alakasızlar elenir, puanlar harmanlanır. AI yoksa aynen devam.
    if (q.ai && this.ai.enabled && ranked.length > 0) {
      progress({ step: 'skorlama', note: 'AI ilgi filtresi…' });
      const judgments = await aiRelevanceFilter(
        this.ai,
        q.query,
        [q.aiQuery, q.extraQuery].filter(Boolean).join(' | ') || undefined,
        ranked.map((v) => ({ youtubeId: v.youtubeId, title: v.title, description: v.description })),
      );
      if (judgments.size > 0) {
        ranked = ranked
          .filter((v) => {
            const j = judgments.get(v.youtubeId);
            return j === undefined || j >= 0.2; // AI'ın el dediği çıkar
          })
          .map((v): ScoredVideo => {
            const j = judgments.get(v.youtubeId);
            if (j === undefined) return v;
            const blended = Math.max(0, Math.min(1, Number((v.score * 0.6 + j * 0.4).toFixed(4))));
            return {
              ...v,
              score: blended,
              reasons:
                j >= 0.75
                  ? [`AI ilgi puanı yüksek (%${Math.round(j * 100)})`, ...v.reasons].slice(0, 4)
                  : v.reasons,
            };
          });
      }
    }

    const filtered = applyFilters(ranked, q);
    const sorted = sortVideos(filtered, q);
    const paged = paginate(sorted, q);

    if (source !== 'cache') {
      const key = cacheKeyFor('search', {
        q: q.query,
        f: q.filter,
        t: q.type,
        u: q.uploadDate,
        d: q.duration,
        s: q.sortBy,
      });
      this.cache.set(key, toEnriched(sorted) as Video[]);
    }

    // Oto-collect: arka planda derin toplama + detay zenginleştirme.
    // Kullanıcı beklemez; kuyruk işleri CollectorWorker tarafından yutulur.
    if (q.auto && !offline && source !== 'cache') {
      // 1) İnce sonuç geldiyse derinlik ikiye katlanarak toplanır
      if (sorted.length < q.result) {
        this.collector.enqueue({
          kind: 'search',
          query: q.query,
          options: { ...q, scroll: Math.min(q.scroll * 2, 5000), auto: false },
        });
      }
      // 2) Eksik zengin alanlar (beğeni/tarih/etiket/kategori) için
      //    ilk sonuçların detayları arka planda kazınır
      for (const v of sorted.slice(0, 10)) {
        if (v.likeCount === 0 && v.tags.length === 0) {
          this.collector.enqueue({ kind: 'video', videoId: v.youtubeId });
        }
      }
    }

    void this.db.searchLog.log(q.query, paged.length, source, Date.now() - started);
    progress({ step: 'bitti', note: `${paged.length} sonuç` });

    // Benzer öneriler (Recommendation Engine — indeks terim benzerliği)
    const recommendations = toEnriched(
      rankVideos(await recommend(this.index, unique, analysis.terms), analysis),
    ).slice(0, 5);

    return {
      query: q.query,
      videos: toEnriched(paged),
      total: sorted.length,
      source,
      offline,
      tookMs: Date.now() - started,
      aiUsed: q.ai && this.ai.enabled,
      analysis: {
        expandedQueries: analysis.expandedQueries,
        hints: analysis.hints as unknown as Record<string, unknown>,
        directive: [q.aiQuery, q.extraQuery].filter(Boolean).join(' | ') || undefined,
      },
      recommendations,
    };
  }
}
