---
title: "NovaSearch AI — Tanımlar (Tüm Tip/Arayüz/Sınıf tanımları)"
type: reference
status: active
version: 0.1.0
updated: 2026-10-17
encoding: UTF-8
---

# TANIMLAR — NovaSearch AI Tip Sözlüğü

> Kaynak koddaki **tüm** `interface` / `type` / `class` / `enum` / önemli `function`
> tanımlarının sözde kod karşılığı. Doğrudan `packages/*/src/*.ts` ve
> `apps/api/src/*.ts` dosyalarından türetilmiştir (**zero-hallucination**).
>
> Kod ile bu belge çelişirse **kod doğrudur**.

Dizin:

1. [common](#1-common--packagescommontyps-ve-digerleri)
2. [search](#2-search--packagessearch)
3. [ai](#3-ai--packagesai)
4. [database](#4-database--packagesdatabase)
5. [cache](#5-cache--packagescache)
6. [workers](#6-workers--packagesworkers)
7. [youtube](#7-youtube--packagesyoutube)
8. [cli](#8-cli--packagescli)
9. [apps/api](#9-appsapi)
10. [Hızlı eşleme tablosu](#10-hızlı-eşleme-tablosu)

---

## 1. common — `packages/common/src/*`

### 1.1 — `types.ts` — çekirdek veri tipleri

```ts
/** Normalize edilmiş video kaydı (tüm provider'lar bu sözleşmeye yazar). */
interface Video {
  youtubeId: string;
  title: string;
  description: string;
  channelId: string;
  channelName: string;
  durationSeconds: number;
  viewCount: number;
  likeCount: number;
  publishedAt: string | null;
  thumbnailUrl: string;
  tags: string[];
  isShort: boolean;
  isLive: boolean;
  qualityScore: number;
  source: 'youtube' | 'local';

  // --- Zengin metadata ---
  url: string;                          // canonical watch URL
  channelUrl?: string;
  viewCountText?: string;               // "1.234.567 izlenme"
  publishedText?: string;               // "2 years ago"
  commentCount?: number;
  category?: string;
  thumbnails?: string[];                // tüm çözünürlükler
  badges?: string[];                    // "4K", "HD", "CC", "Yeni"
  isFamilySafe?: boolean;
  isUnlisted?: boolean;
  isCrawlable?: boolean;
  channelSubscriberCount?: number;
  channelVerified?: boolean;
  captionTracks?: Array<{ language: string; name: string; url: string; auto: boolean }>;
  topComments?: Array<{ author: string; text: string; likes: number }>;
  relatedIds?: string[];
  language?: 'tr' | 'en' | 'other';
  spamScore?: number;                   // 0-1; yalnız işarettir
}

interface Channel {
  youtubeId: string;
  name: string;
  description: string;
  subscriberCount: number;
  videoCount: number;
  thumbnailUrl: string;
  verified: boolean;
}

/** Kullanıcı arama isteği (CLI/Web/API aynı sözleşmeyi kullanır). */
interface SearchQuery {
  query: string;
  ai: boolean;                // AI destekli genişletme/süzme
  aiQuery?: string;           // AI'a ek yönlendirme promptu
  extraQuery?: string;
  result: number;             // gösterilecek sonuç
  scroll: number;             // kaynaktan çekilecek (overscan)
  filter: ContentFilter;
  type: SearchType;
  uploadDate: UploadDate;
  sortBy: SortBy;
  minViews: number;
  maxViews: number;
  minScore: number;           // 0-1
  duration: DurationFilter;
  quality: QualityFilter;
  page: number;
  limit: number;
  offline: boolean;
  auto: boolean;              // arka plan oto-collect
}

type ContentFilter = 'all' | 'v' | 's' | 'sh';
type SearchType    = 'all' | 'video' | 'channel' | 'playlist';
type UploadDate    = 'all' | 'hour' | 'today' | 'week' | 'month' | 'year';
type SortBy        = 'relevance' | 'rating' | 'upload_date' | 'view_count';
type DurationFilter = 'all' | 'short' | 'medium' | 'long';
type QualityFilter  = 'all' | 'high' | 'medium' | 'low';

/** Zenginleştirilmiş sonuç: skor + eşleşen terimler + gerekçeler. */
interface EnrichedVideo extends Video {
  score: number;
  matchedTerms: string[];
  reasons: string[];          // "Neden önerildi?"
}

/** Motor ilerleme olayı (CLI progressbar / API). */
interface SearchProgressEvent {
  step: 'analiz' | 'cache' | 'db' | 'toplama' | 'skorlama' | 'bitti';
  current?: number;
  total?: number;
  note?: string;
}

interface SearchResponse {
  query: string;
  videos: EnrichedVideo[];
  total: number;
  source: 'cache' | 'local-db' | 'youtube' | 'mixed';
  offline: boolean;
  tookMs: number;
  aiUsed: boolean;
  analysis: {
    expandedQueries: string[];
    hints: Record<string, unknown>;
    directive?: string;       // kullanıcının AI yönlendirmesi
  };
  recommendations?: EnrichedVideo[];
}
```

### 1.2 — `constants.ts`

```ts
const CACHE_TTL = { ram: 300, disk: 3600, ai: 86400 } as const;      // saniye
const COLLECTOR = {
  defaultCooldownMs: 2500, jitterPct: 20, maxRetries: 3, backoffBaseMs: 1500,
} as const;
const SEARCH = { defaultResult: 20, defaultScroll: 50, maxScroll: 5000, maxResult: 1000 } as const;
const QUALITY_THRESHOLDS = { high: 0.65, medium: 0.4 } as const;
```

### 1.3 — `errors.ts`

```ts
type NovaErrorCode =
  | 'VALIDATION' | 'NETWORK' | 'PROVIDER' | 'DATABASE'
  | 'AI' | 'RATE_LIMITED' | 'NOT_FOUND' | 'INTERNAL';

class NovaSearchError extends Error {
  readonly code: NovaErrorCode;
  constructor(code: NovaErrorCode, message: string, cause?: unknown);
}

function isNovaSearchError(err: unknown): err is NovaSearchError;
function isNetworkError(err: unknown): boolean;   // offline fallback kararları için
```

### 1.4 — `utils.ts`

```ts
function jitter(baseMs: number, jitterPct = 20): number;      // ±%jitter
function backoffMs(attempt: number, baseMs = 1500, maxMs = 60_000): number;
function sleep(ms: number): Promise<void>;
function queryKey(parts: Record<string, unknown>): string;    // sha1 (cache key)
function clamp(value: number, min: number, max: number): number;
function formatDuration(totalSeconds: number): string;        // "12:34" / "1:02:03"
function formatViews(n: number): string;                      // 1.2M / 34K
function parseNumber(value: unknown, defaultValue: number): number;
function safeJson<T>(text: string, fallback: T): T;
```

### 1.5 — `env.ts`

```ts
function loadEnvFile(path = '.env'): void;   // process.loadEnvFile; yok/bozuksa sessizce devam
```

### 1.6 — `progress.ts`

```ts
class ProgressBar {
  constructor(width = 28);
  handle(event: SearchProgressEvent): void;   // step'e göre çizim; 'bitti' → finish
  draw(ratio: number, label: string): void;   // ratio<0 → spinner
  finish(note: string): void;
  clear(): void;                              // stderr'e yazar (--json çıktısını bozmaz)
}
```

### 1.7 — `signals.ts` (içerik sinyalleri — OS sinyali değil)

```ts
function detectLanguage(...texts: string[]): 'tr' | 'en' | 'other';
function spamScore(title: string, description = ''): number;   // 0-1 tıklama tuzağı işareti
```

---

## 2. search — `packages/search/src/*`

### 2.1 — `engine.ts`

```ts
type ProgressCallback = (event: SearchProgressEvent) => void;

interface SearchEngineDeps {
  db: Database;
  cache: HybridCache<Video[]>;
  collector: CollectorWorker;
  aiClient?: OpenRouterClient;     // yoksa loadAIConfig() ile kurulur
}

class SearchEngine {
  constructor(deps: SearchEngineDeps);
  indexStats(): { videos: number; terms: number; loaded: boolean };
  search(q: SearchQuery, onProgress?: ProgressCallback): Promise<SearchResponse>;
  // private: respond(...) — rank + AI filtre + filtre/sıralama/sayfalama
  //          + cache set + oto-collect + searchLog + recommendations
}
```

### 2.2 — `ranking.ts` (saf fonksiyonlar)

```ts
function applyFilters(videos: ScoredVideo[], q: SearchQuery): ScoredVideo[];
function paginate<T>(items: T[], q: SearchQuery): T[];
function sortVideos(videos: ScoredVideo[], q: SearchQuery): ScoredVideo[];
function toEnriched(scored: ScoredVideo[]): EnrichedVideo[];
```

### 2.3 — `search-index.ts`

```ts
interface IndexStats { videos: number; terms: number; loaded: boolean; }

class SearchIndex {
  constructor(db: Database);
  ensureLoaded(maxRows = 20_000): Promise<void>;   // TTL 60sn
  add(videos: Video[]): void;                      // artımlı
  update(videos: Video[]): void;                   // tazeleme sonrası
  candidates(terms: string[], limit = 20): Promise<Video[]>;   // ters indeks + prefix
  stats(): IndexStats;
}
```

### 2.4 — `recommend.ts`

```ts
function recommend(index: SearchIndex, seeds: Video[], terms: string[], limit = 5): Promise<Video[]>;
```

### 2.5 — `bootstrap.ts`

```ts
interface EngineContext {
  engine: SearchEngine;
  db: Database;
  collector: CollectorWorker;
  cache: HybridCache<Video[]>;
  cacheStats(): CacheStats;
  close(): Promise<void>;
}

function createEngineFromEnv(): Promise<EngineContext>;
// DB_TYPE → createDatabase · CACHE_DIR → HybridCache ·
// COLLECTOR_MAX_CONCURRENT → CollectorWorker(start) · SearchEngine({db,cache,collector})
```

---

## 3. ai — `packages/ai/src/*`

### 3.1 — `config.ts`

```ts
interface AIConfig {
  apiKey: string;                 // OPENROUTER_API_KEY
  apiUrl: string;                 // OPENROUTER_API_URL
  useFreeModels: boolean;         // AI_USE_FREE_MODELS
  rateLimitPerMinute: number;     // AI_RATE_LIMIT_PER_MINUTE (20)
  timeoutMs: number;              // AI_TIMEOUT_MS (30000)
  maxRetries: number;             // AI_MAX_RETRIES (2)
}

function loadAIConfig(): AIConfig;
function isAIEnabled(): boolean;  // apiKey.length > 0
```

### 3.2 — `client.ts`

```ts
interface ChatMessage { role: 'system' | 'user' | 'assistant'; content: string; }

class RateLimiter {
  constructor(perMinute: number);
  waitIfNeeded(): Promise<void>;   // dk penceresi dolarsa bekle
}

class OpenRouterClient {
  constructor(config: AIConfig);
  get enabled(): boolean;
  chat(messages: ChatMessage[], requestedModel?: string): Promise<string>;  // fallback zinciri
  chatJSON<T>(messages: ChatMessage[], fallback: T, requestedModel?: string): Promise<T>;
}

function extractJson<T>(text: string, fallback: T): T;   // kirli yanıttan ilk JSON bloğu
```

### 3.3 — `models.ts`

```ts
interface ModelInfo { name: string; isFree: boolean; maxTokens: number; }

const FREE_MODELS: ModelInfo[];   // openrouter/free, gemma-4, nemotron-3, inkling
const PAID_MODELS: ModelInfo[];   // deepseek-chat, llama-3.1-8b-instruct

function getModel(name: string): ModelInfo | undefined;
function selectModels(preferFree: boolean, requested?: string): string[];   // fallback sırası
```

### 3.4 — `query-analyzer.ts`

```ts
interface QueryHints {
  wantsLive: boolean;
  wantsOfficial: boolean;
  wantsCover: boolean;
  wantsRemix: boolean;
  language: 'tr' | 'en' | 'unknown';
  yearFrom?: number;
  yearTo?: number;
}

interface QueryAnalysis {
  original: string;
  terms: string[];             // stopword atılmış token'lar
  hints: QueryHints;
  expandedQueries: string[];   // deep search varyantları
}

interface AnalyzeOptions {
  ai?: boolean;
  aiQuery?: string;
  extraQuery?: string;
  client?: OpenRouterClient;
}

function analyzeQuery(query: string, opts?: AnalyzeOptions): Promise<QueryAnalysis>;
// her zaman heuristic; AI varsa genişletir, yoksa ASLA bloklamaz
```

### 3.5 — `result-enricher.ts`

```ts
interface ScoredVideo extends Video {
  score: number;              // 0..1
  matchedTerms: string[];
  reasons: string[];
}

function scoreVideo(video: Video, analysis: QueryAnalysis): ScoredVideo;
function rankVideos(videos: Video[], analysis: QueryAnalysis): ScoredVideo[];
function buildReasons(video: Video, analysis: QueryAnalysis,
                      matchedTerms: string[], score: number): string[];   // max 4
```

### 3.6 — `ai-filter.ts`

```ts
interface AIJudgment { id: string; relevance: number; }

function aiRelevanceFilter(
  client: OpenRouterClient | undefined,
  query: string,
  directive: string | undefined,
  items: Array<{ youtubeId: string; title: string; description: string }>,
): Promise<Map<string, number>>;   // youtubeId → 0..1; boş harita = değerlendirme yok
```

---

## 4. database — `packages/database/src/*`

### 4.1 — `adapter.ts`

```ts
type Dialect = 'sqlite' | 'mysql';
interface RunResult { changes: number; lastId: number; }

interface DbAdapter {
  readonly dialect: Dialect;
  all<T>(sql: string, params?: unknown[]): Promise<T[]>;
  get<T>(sql: string, params?: unknown[]): Promise<T | undefined>;
  run(sql: string, params?: unknown[]): Promise<RunResult>;
  exec(sql: string): Promise<void>;
  transaction<T>(fn: (tx: DbAdapter) => Promise<T>): Promise<T>;
  close(): Promise<void>;
}

function nowIso(): string;
```

### 4.2 — `index.ts`

```ts
interface Database {
  adapter: DbAdapter;
  videos: VideoRepo;
  channels: ChannelRepo;
  searchLog: SearchLogRepo;
  close(): Promise<void>;
}

interface DatabaseOptions {
  type?: 'sqlite' | 'mysql';
  sqlitePath?: string;
  mysql?: { host; port; database; user; password; ssl: boolean };
}

function createDatabase(opts?: DatabaseOptions): Promise<Database>;
```

### 4.3 — `repositories.ts`

```ts
class VideoRepo {
  constructor(db: DbAdapter);
  upsertMany(videos: Video[]): Promise<number>;   // dedup upsert (ON CONFLICT / ON DUPLICATE)
  findByYoutubeId(youtubeId: string): Promise<Video | undefined>;
  searchLocal(terms: string[], limit: number): Promise<Video[]>;   // LIKE eşleşmesi
  staleVideos(cutoffIso: string, limit: number): Promise<Video[]>; // refresh için
  count(): Promise<number>;
  topViewed(limit: number): Promise<Video[]>;
  recent(limit: number): Promise<Video[]>;
}

class ChannelRepo {
  constructor(db: DbAdapter);
  upsert(channel: Channel): Promise<void>;
}

class SearchLogRepo {
  constructor(db: DbAdapter);
  log(query: string, resultsCount: number, source: string, tookMs: number): Promise<void>;
  recent(limit: number): Promise<Array<{ query; results_count; created_at }>>;
  topQueries(limit: number): Promise<Array<{ query; n }>>;
}
```

### 4.4 — `sqlite.ts` / `mysql.ts` / `schema.ts`

```ts
class SqliteAdapter implements DbAdapter { /* node:sqlite DatabaseSync, WAL, foreign_keys=ON */ }
class MysqlAdapter  implements DbAdapter { /* mysql2/promise pool, connectionLimit=10 */ }
function migrate(db: DbAdapter): Promise<void>;   // idempotent DDL + ADD COLUMN (varsa yoksay)
```

**Tablolar:** `videos` · `channels` · `search_log`

---

## 5. cache — `packages/cache/src/*`

```ts
class RamCache<T = unknown> {
  constructor(maxEntries = 500, defaultTtlSec = 300);
  get(key: string): T | undefined;      // LRU: erişileni sona taşır
  set(key: string, value: T, ttlSec?): void;
  delete(key: string): void;
  clear(): void;
  get size(): number;
}

class DiskCache<T = unknown> {
  constructor(dir: string, defaultTtlSec = 3600);
  get(key: string): T | undefined;      // bozuk/süresi dolmuş kayıt silinir
  set(key: string, value: T, ttlSec?): void;
  delete(key: string): void;
  clear(): void;
}

interface CacheStats { hits: number; misses: number; ramSize: number; }

class HybridCache<T = unknown> {
  constructor(dir = './.cache/novasearch');
  get(key: string): T | undefined;      // RAM → Disk (diskten gelince RAM'e yazar)
  set(key: string, value: T, ttlSec?): void;   // ikisine birden
  delete(key: string): void;
  stats(): CacheStats;
}

function cacheKeyFor(scope: string, parts: Record<string, unknown>): string;
```

---

## 6. workers — `packages/workers/src/*`

```ts
interface Job<T = unknown> { id: number; payload: T; attempts: number; }
type JobHandler<T> = (payload: T) => Promise<void>;
interface QueueStats { queued; processing; completed; failed: number; }

class JobQueue<T> {
  constructor(handler: JobHandler<T>, concurrency = 2, maxAttempts = 3);
  push(payload: T): number;      // id döner
  start(): void;
  stop(): void;
  stats(): QueueStats;
}

class TokenBucket {
  constructor(capacity: number, perMinute: number, jitterPct = 20);
  take(): Promise<void>;         // izin çıkana kadar bekle (jitter'lı)
}

type CollectPayload =
  | { kind: 'search'; query: string; options: SearchQuery }
  | { kind: 'video'; videoId: string };

class CollectorWorker {
  constructor(db: Database, concurrency = 2);
  start(): void;
  stop(): void;
  enqueue(payload: CollectPayload): number;
  stats(): QueueStats;
  collectNow(query: string, options: SearchQuery): Promise<Video[]>;   // senkron toplama
  refreshStale(maxAgeDays = 7, limit = 50): Promise<number>;           // metadata tazeleme
}
```

---

## 7. youtube — `packages/youtube/src/*`

```ts
class YouTubeClient {                 // youtubei.js Innertube singleton'ı
  call<T>(fn: (yt: Innertube) => Promise<T>): Promise<T>;   // throttle + retry
}
function getYouTubeClient(): YouTubeClient;

function searchYouTube(query: string, q: SearchQuery): Promise<Video[]>;
function getSearchSuggestions(query: string): Promise<string[]>;
function getVideoDetails(videoId: string): Promise<Video | null>;   // tam metadata kazıma
function getChannelDetails(channelId: string): Promise<Channel | null>;
function getStreamInfo(videoId: string): Promise<StreamInfo | null>;

interface StreamFormat {
  itag: number;
  quality: string;
  mimeType: string;
  url: string;
  hasAudio: boolean;
  hasVideo: boolean;
  contentLength?: string;
}

interface StreamInfo {
  youtubeId: string;
  title: string;
  durationSeconds: number;
  isLive: boolean;
  formats: StreamFormat[];
}

// mapper.ts (saf fonksiyonlar)
function textOf(value: unknown): string;
function parseViews(text: string): number;
function parseDuration(text: string): number;              // "10:13" → 613
function parseRelativeDate(text: string): string | null;   // → ISO
function watchUrl(id: string): string;
function mapToVideo(node: any): Video | null;
```

---

## 8. cli — `packages/cli/src/*`

```ts
// run.ts — iki CLI'nin ortak katmanı (tek kaynak)
type RawOptions = Record<string, string | boolean | undefined>;
function buildQuery(query: string, o: RawOptions): SearchQuery;
function withEngine<T>(fn: (ctx: EngineContext) => Promise<T>): Promise<T>;
function runSearch(query: string, o: RawOptions,
                   onProgress?: (e: SearchProgressEvent) => Promise<SearchResponse> | void): Promise<SearchResponse>;

// format.ts — Unicode box tablo + ANSI renk
function boxTable(headers: string[], rows: string[][], colWidths: number[]): string;
function box(title: string, lines: string[]): string;
function renderResults(res: SearchResponse): string;
function renderAnalysis(res: SearchResponse): string;
function renderStats(stats: { videoCount; cache; queue; index? }): string;
function renderTrending(top, recent): string;
function renderStream(info): string;

// index.ts  → parametrik CLI (commander): search · collect · stats · trending · refresh · backup · stream
// wizard.ts → etkileşimli menü CLI (stdin pipe'ta cevaplar önceden okunur)
```

---

## 9. apps/api

```ts
// server.ts (Fastify)
function parseSearchQuery(q: Record<string, string | undefined>):
  { query: string; options: SearchQuery };

function buildServer(ctx: EngineContext): Promise<FastifyInstance>;
function buildWebServer(): Promise<FastifyInstance>;   // @fastify/static → apps/web
function start(): Promise<void>;                       // API :3001 + Web :3002

// logger.ts
function logLine(msg: string, color?): void;
function requestLog(method: string, url: string, status: number, ms: number): void;
function banner(lines: string[]): void;
```

---

## 10. Hızlı eşleme tablosu

| İsim | Tür | Dosya |
|---|---|---|
| `Video` · `Channel` · `SearchQuery` · `EnrichedVideo` · `SearchResponse` · `SearchProgressEvent` | interface | `common/src/types.ts` |
| `ContentFilter` · `SearchType` · `UploadDate` · `SortBy` · `DurationFilter` · `QualityFilter` | type | `common/src/types.ts` |
| `CACHE_TTL` · `COLLECTOR` · `SEARCH` · `QUALITY_THRESHOLDS` | const | `common/src/constants.ts` |
| `NovaSearchError` · `NovaErrorCode` | class / type | `common/src/errors.ts` |
| `ProgressBar` | class | `common/src/progress.ts` |
| `SearchEngine` · `SearchEngineDeps` · `ProgressCallback` | class / interface / type | `search/src/engine.ts` |
| `SearchIndex` · `IndexStats` | class / interface | `search/src/search-index.ts` |
| `EngineContext` · `createEngineFromEnv` | interface / function | `search/src/bootstrap.ts` |
| `AIConfig` | interface | `ai/src/config.ts` |
| `OpenRouterClient` · `RateLimiter` · `ChatMessage` | class / interface | `ai/src/client.ts` |
| `ModelInfo` · `FREE_MODELS` · `PAID_MODELS` | interface / const | `ai/src/models.ts` |
| `QueryAnalysis` · `QueryHints` · `AnalyzeOptions` | interface | `ai/src/query-analyzer.ts` |
| `ScoredVideo` | interface | `ai/src/result-enricher.ts` |
| `AIJudgment` | interface | `ai/src/ai-filter.ts` |
| `DbAdapter` · `Dialect` · `RunResult` | interface / type | `database/src/adapter.ts` |
| `Database` · `DatabaseOptions` | interface | `database/src/index.ts` |
| `VideoRepo` · `ChannelRepo` · `SearchLogRepo` | class | `database/src/repositories.ts` |
| `SqliteAdapter` · `MysqlAdapter` · `migrate` | class / function | `database/src/sqlite.ts` · `mysql.ts` · `schema.ts` |
| `RamCache` · `DiskCache` · `HybridCache` · `CacheStats` | class / interface | `cache/src/*` |
| `JobQueue` · `Job` · `QueueStats` · `JobHandler` | class / interface | `workers/src/queue.ts` |
| `TokenBucket` | class | `workers/src/rate-limiter.ts` |
| `CollectorWorker` · `CollectPayload` | class / type | `workers/src/collector-worker.ts` |
| `YouTubeClient` · `StreamInfo` · `StreamFormat` | class / interface | `youtube/src/client.ts` · `stream.ts` |
| `RawOptions` · `buildQuery` · `withEngine` · `runSearch` | type / function | `cli/src/run.ts` |
| `parseSearchQuery` · `buildServer` · `buildWebServer` · `start` | function | `apps/api/src/server.ts` |
| `detectLanguage` · `spamScore` | function | `common/src/signals.ts` |
| `jitter` · `backoffMs` · `queryKey` · `clamp` · `formatDuration` · `formatViews` | function | `common/src/utils.ts` |

---

> **Doğrulama:** Bu belge 2026-10-17'de kaynak dosyalar okunarak derlenmiştir.
> Eksik/güncellenmesi gereken bir tanım fark edilirse hedef `packages/*/src/*.ts`
> incelenerek bu dosya düzeltilir.
