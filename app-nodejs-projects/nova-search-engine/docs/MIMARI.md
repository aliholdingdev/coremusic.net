---
title: "NovaSearch AI — Mimari"
type: architecture
status: active
version: 0.1.0
updated: 2026-10-17
encoding: UTF-8
---

# NovaSearch AI — Mimari Dokümanı

> **NASIL çalıştığını** anlatır. Ürün kapsamı → [PRD.md](PRD.md) · Tüm tanımlar → [TANIMLAR.md](TANIMLAR.md)
>
> Bu dosya 2026-10-17'de kaynak koddan sıfırdan yeniden yazılmıştır
> (eski sürüm §15'ten sonra mojibake ile kirlenmişti; ayrıca özet kaynaklı
> Express/Data API iddiaları gerçek kodla çakışıyordu — düzeltilmiştir).

---

## 1. Genel Bakış

NovaSearch AI, **Bun + strict TypeScript** ile yazılmış, `pnpm` workspace'inde
düzenlenmiş bağımsız YouTube metadata arama motorudur. YouTube erişimi
**`youtubei.js` (Innertube)** üzerinden yapılır — **YouTube Data API v3 kullanılmaz
(ADR-001)**. HTTP katmanı **Fastify**'dır (Express değil).

**Üst düzey akış (karar zinciri):**

```
Kullanıcı sorgusu (CLI / Web / API)
     │
     ▼
┌──────────────────────────────────────────────────────────┐
│ 1) analiz    analyzeQuery()  — heuristic (+ opsiyonel AI)│
│              → terms, hints, expandedQueries (max 3)     │
│ 2) cache     HybridCache (RAM 500 kayıt/300sn → Disk)    │
│              → isabet varsa YouTube'a SIFIR istek        │
│ 3) db        SearchIndex (ters indeks, TTL 60sn)         │
│              → terim → aday video'lar (MSA bütçesi)      │
│ 4) toplama   [online && yetersizse] CollectorWorker      │
│              → youtubei.js, max 3 sorgu varyantı         │
│ 5) normalize dedup upsert → DB (youtube_id tekil)        │
│              → artımlı indekse ekleme                    │
│ 6) skorlama  rankVideos() → AI ilgi filtresi (ops.)      │
│              → applyFilters → sortVideos → paginate      │
│ 7) cache set + oto-collect enqueue + searchLog           │
│ 8) recommend benzer öneriler (5)                         │
└──────────────────────────────────────────────────────────┘
     │
     ▼
SearchResponse { query, videos[], total, source, offline,
                 tookMs, aiUsed, analysis, recommendations }
```

**Kural (kaynak yorumu):** *"Bir daha aynı veri asla çekilmeyecek."*

---

## 2. Paket Haritası (workspace)

| Paket | Sorumluluk | Kritik bağımlılık |
|---|---|---|
| `common` | Tipler, sabitler, hatalar, utils (jitter/backoff), `.env`, sinyaller (dil+spam), progress bar | `node:crypto` |
| `youtube` | `youtubei.js` 18.x wrapper: client, mapper, search, channel, stream | `youtubei.js` |
| `database` | `DbAdapter` (SQLite\|MySQL), şema, repository (dedup upsert) | `node:sqlite` / `mysql2` |
| `cache` | `RamCache` (LRU+TTL) → `DiskCache` (JSON) → `HybridCache` | `common` |
| `ai` | `OpenRouterClient`, model registry, query-analyzer, skorlama, ilgi filtresi | `common` |
| `search` | Orkestratör: `SearchEngine`, ranking, `SearchIndex`, recommend, bootstrap | hepsi (tek yön) |
| `workers` | `JobQueue`, `TokenBucket`, `CollectorWorker` (+ refresh) | `database`, `youtube` |
| `cli` | 2 CLI: `index.ts` (parametrik) + `wizard.ts` (menü) + `run.ts` + `format.ts` | `search` |
| `apps/api` | Fastify: REST + monitoring + statik web (`@fastify/static`) | tümü |
| `apps/web` | Vanilla JS + ITCSS/BEM (`js/`, `css/01..04`, `index.html`, `admin.html`) | — |

**Katman kuralı (tek yön):**
`cli/api → search → (ai, cache, workers) → (youtube, database) → common`

`common` alt katmandır; üst katmanı tanımaz.

---

## 3. Çekirdek Tipler (packages/common/src/types.ts)

| Tip | Amaç |
|---|---|
| `Video` | Normalize kayıt: `youtubeId`, başlık, kanal, süre, izlenme, beğeni, `tags`, `isShort/isLive`, `qualityScore`, `source` + zengin alanlar (`url`, `thumbnails`, `badges`, `captionTracks`, `topComments`, `relatedIds`, `language`, `spamScore` …) |
| `Channel` | Kanal: abone sayısı, video sayısı, `verified` |
| `SearchQuery` | Kullanıcı isteği: `query`, `ai`, `aiQuery`, `extraQuery`, `result`, `scroll`, `filter`, `type`, `uploadDate`, `sortBy`, `minViews/maxViews`, `minScore`, `duration`, `quality`, `page`, `limit`, `offline`, `auto` |
| `EnrichedVideo` | `Video` + `score` + `matchedTerms[]` + `reasons[]` |
| `SearchResponse` | Yanıt: `videos`, `total`, `source` (`cache/local-db/youtube/mixed`), `offline`, `tookMs`, `aiUsed`, `analysis`, `recommendations` |
| `SearchProgressEvent` | İlerleme: `step` (`analiz/cache/db/toplama/skorlama/bitti`), `current/total/note` |
| Süzgeç tipleri | `ContentFilter` · `SearchType` · `UploadDate` · `SortBy` · `DurationFilter` · `QualityFilter` |

> **Tam tanım listesi (sözde kod)** → [TANIMLAR.md](TANIMLAR.md)

---

## 4. SearchEngine — Karar Zinciri (packages/search/src/engine.ts)

`SearchEngine.search(q, onProgress)` sırasıyla:

| # | `step` | İş |
|---|---|---|
| 1 | `analiz` | `analyzeQuery(q.query, {ai, aiQuery, extraQuery, client})` — **AI çağrısı yerel yüklemeyi bloklamaz** (paralel promise) |
| 2 | `cache` | `cacheKeyFor('search', {q,f,t,u,d,s})` → `HybridCache.get` — isabet ise `respond(...)` |
| 3 | `db` | `SearchIndex.ensureLoaded()` (TTL 60sn) + `candidates(terms, max(scroll,result,100))` |
| 4 | `toplama` | Yetersiz ve online ise: `analysis.expandedQueries.slice(0,3)` için `collector.collectNow` → iç dedup → `index.add(fresh)` |
| 5 | `skorlama` | `rankVideos(unique, analysis)` → (AI modunda) `aiRelevanceFilter` → `applyFilters` → `sortVideos` → `paginate` |
| 6 | — | `cache.set` (kaynak `cache` değilse) · oto-collect enqueue (derin toplama + ilk 10'un detay zenginleştirme) · `searchLog.log` |
| 7 | `bitti` | `recommend(...)` → `recommendations` (5) + nihai `SearchResponse` |

**Bağımlılıklar (`SearchEngineDeps`):** `db: Database` · `cache: HybridCache<Video[]>` ·
`collector: CollectorWorker` · `aiClient?: OpenRouterClient` (yoksa `new OpenRouterClient(loadAIConfig())`).

**AI ilgi filtresi (moda açıkken):** judgments `undefined` veya `>= 0.2` olanlar kalır;
skor `score*0.6 + ai*0.4` ile harmanlanır; `ai >= 0.75` ise gerekçeye "AI ilgi puanı yüksek" eklenir.

**Ağ hatası davranışı:** `isNetworkError` ise sessizce yerel veriyle devam (offline deneyimi bozulmaz).

---

## 5. SearchIndex — Ters İndeks (packages/search/src/search-index.ts)

> *"Her mesajda tüm veritabanını tarama. Önce indexle. Ardından yalnız alakalı max N kayda bak."*

- **Yapı:** `postings: Map<terim, Set<youtubeId>>` + `store: Map<youtubeId, Video>`
- **Tokenize:** küçük harf, harf/rakam dışı atılır, `length > 2`
- **Yükleme:** `ensureLoaded(maxRows=20_000)` — `db.videos.topViewed`; **TTL 60sn**
  (dış süreçlerden gelen refresh/o-collect güncellemeleri görünür olsun)
- **Aday seçimi:** `candidates(terms, limit)` — önce tam eşleşme, yoksa **prefix match**
  (`"tark"` → `"tarkan"`; maliyet tavanı 500); sıralama = terim eşitliği → popülerlik
- **Artımlı:** `add(videos)` toplama sonrası; `update(videos)` tazeleme sonrası

---

## 6. Ranking (packages/search/src/ranking.ts)

| Fonksiyon | İş |
|---|---|
| `applyFilters` | `minViews/maxViews/minScore`, süre (`short<300`, `medium 300–1200`, `long>1200`), kalite (`high≥0.65`, `medium≥0.4`) |
| `paginate` | `pageSize = clamp(limit || result, 1, 1000)`, `page>=1` |
| `sortVideos` | `view_count` / `upload_date` / `rating` / varsayılan `score` |
| `toEnriched` | `ScoredVideo` → `EnrichedVideo` (`score/matchedTerms/reasons` korunur) |

**Skor çekirdeği (`ai/result-enricher.ts::scoreVideo`):**
`termScore*0.45 + popülerlik(log)*0.25 + güncellik*0.15 + qualityScore*0.15 + başlıkBonusu(0.15) + sinyal(wantsLive/wantsOfficial)` — `0..1` arası.

**Gerekçeler (`buildReasons`, max 4):** başlıkta eşleşme · açıklamada eşleşme ·
canlı/resmi uyumu · yüksek izlenme · güncellik · shorts · genel uyum %.

---

## 7. AI Katmanı (packages/ai)

```
loadAIConfig() ──► AIConfig {apiKey(OPENROUTER_API_KEY), apiUrl, useFreeModels,
                             rateLimitPerMinute, timeoutMs, maxRetries}
       │
       ▼
OpenRouterClient ── RateLimiter (dk/dakika) ── model fallback zinciri
  chat()        : 4xx → sonraki model; ağ/zaman aşımı → backoff+jitter tekrar
  chatJSON()    : kirli yanıttan ilk JSON bloğunu çıkarır (extractJson); hata → fallback
       │
  ┌────┴─────────────────┬──────────────────────┐
  ▼                      ▼                      ▼
analyzeQuery()      aiRelevanceFilter()     rankVideos()
sorgu → terms       top 20 adayı 0..1       heuristic skor (saf)
hints (live/official/ cover/remix/yıl/dil)  puanlar harmanlanır
expandedQueries
```

- **Modeller (`models.ts`, 2026-10-16 doğrulandı):**
  `openrouter/free` → `google/gemma-4-26b-a4b-it:free` →
  `nvidia/nemotron-3-super-120b-a12b:free` → `thinkingmachines/inkling:free`
  (ücretli: `deepseek/deepseek-chat`, `meta-llama/llama-3.1-8b-instruct`)
- **`isAIEnabled()`:** `apiKey.length > 0` — anahtar yoksa AI **tamamen devre dışı**,
  heuristic analiz her koşulda çalışır.
- AI anahtarı **yalnızca header'da** taşınır (`Authorization: Bearer`), log'a yazılmaz.

---

## 8. Veritabanı (packages/database)

```
createDatabase(opts) ──► DbAdapter (sözleşme)
                          ├─ SqliteAdapter → node:sqlite (DatabaseSync, WAL, foreign_keys=ON)
                          └─ MysqlAdapter  → mysql2/promise pool (connectionLimit=10)
                       migrate(adapter) → şema (idempotent, tek kaynak ddl())
                       VideoRepo · ChannelRepo · SearchLogRepo
```

- **Tablolar:** `videos` · `channels` · `search_log`
- **İndeksler:** `title`, `view_count`, `published_at`, `search_log.query`
- **Dedup upsert:** `ON CONFLICT(youtube_id) DO UPDATE` (SQLite) /
  `ON DUPLICATE KEY UPDATE` (MySQL) — aynı veri 2. kez satır oluşturmaz
- **`migrate`:** DDL tek tek + `ALTER TABLE ADD COLUMN` (varsa yoksay) — MySQL havuzu
  `multipleStatements:false` (SQL injection güvenliği) ile uyumlu
- **`rowToVideo`:** JSON kolonları (`tags/thumbnails/badges/captionTracks/topComments/relatedIds`)
  parse edilir; `language`/`spamScore` boşsa **okuma anında** saf fonksiyonla hesaplanır
- **Çekirdek kural:** tüm yazmalar parametreli (`?` placeholder)

---

## 9. Cache (packages/cache)

| Katman | Yapı | TTL |
|---|---|---|
| `RamCache` | `Map` + **LRU** (max 500 kayıt) + TTL | `CACHE_TTL.ram = 300sn` |
| `DiskCache` | JSON dosyalar (`<key>.json`), bozuk kayıt silinir | `CACHE_TTL.disk = 3600sn` |
| `HybridCache` | get: RAM → Disk (diskten gelince RAM'e geri koy) · set: ikisine birden | — |

- `CACHE_TTL.ai = 86400` (AI yanıtları için sabit)
- `cacheKeyFor(scope, parts)` → `scope:sha1(normalize)` — **tek biçim anahtar**
- `CacheStats { hits, misses, ramSize }`

---

## 10. Workers (packages/workers)

| Sınıf | Algoritma | Kullanım |
|---|---|---|
| `JobQueue<T>` | FIFO + eşzamanlılık (vars. 2) + max 3 deneme + backoff | Arka plan işleri; kullanıcı araması scraping'i beklemez |
| `TokenBucket` | Dakikada 12 token, kapasite = eşzamanlılık, **jitter ±%20** | YouTube istek bütçesi |
| `CollectorWorker` | `enqueue` / `collectNow` / `refreshStale` | toplama + dedup upsert + kanal detayı |

- **`CollectPayload`:** `{kind:'search', query, options}` veya `{kind:'video', videoId}`
- **`refreshStale(maxAgeDays, limit)`:** bayat kayıtları tazeler; **fresh boş dönen
  kritik alanlar eski değerle korunur** (kayıp olmaz)
- **`start()`** bootstrap'te çağrılır, `close()` → `stop()` (süreç çıkışını engeller)

---

## 11. YouTube Kaynağı (packages/youtube) — ADR-001

> **YouTube Data API v3 KULLANILMAZ.** Kaynak: `youtubei.js` 18.x **Innertube**
> (`lang:'tr', location:'TR'`).

| Sınıf/Fonksiyon | İş |
|---|---|
| `YouTubeClient` | Singleton; `call(fn)` = **throttle (cooldown 2500ms ±%20 jitter)** + retry (3 deneme, backoff) + oturum tazeleme |
| `searchYouTube(query, q)` | `client.search` + **continuation ile scroll** (max 50 sayfa ≈ 1000+ video) + içerik tipi süzgeci (`sh/v/s`) |
| `getVideoDetails(id)` | Tam metadata kazıma: temel bilgi + rozetler + abone + altyazı izleri + ilk 5 yorum + `watch_next` ilgili ID'ler + `collectLikeCount` |
| `getSearchSuggestions(q)` | Otomatik tamamlama (`/api/suggest`) |
| `getChannelDetails(id)` | Kanal kaydı (`Channel`) |
| `getStreamInfo(id)` | GoogleVideo format bilgisi — **IOS istemcisi** ile doğrudan URL (2026-10 doğrulandı); şifreliyse `decipher` |
| `mapper.ts` | `mapToVideo`, `parseViews`, `parseDuration`, `parseRelativeDate`, `textOf`, `watchUrl` |

**Sorumlu toplama (anti-ban değil, sürdürülebilir ritim):** cooldown+jitter · token
bucket 12/dk · exponential backoff+jitter (max 3) · max 3 sorgu varyantı · 50 sayfa tavanı ·
cache-first = tekrarlanan aramada YouTube maliyeti **0**.

---

## 12. HTTP API (apps/api/src/server.ts) — Fastify

| Endpoint | İş |
|---|---|
| `GET /` | Web portuna yönlendirme (3002) |
| `GET /api/health` | `{status:'ok', uptime}` |
| `GET /api/search` | `parseSearchQuery` → `engine.search` + terminal `ProgressBar` |
| `GET /api/stats` | DB/video + cache + queue + index + son aramalar + topQueries |
| `GET /api/videos/:id` | Yerel DB video kaydı |
| `GET /api/videos/:id/stream` | `getStreamInfo` (GoogleVideo) |
| `GET /api/suggest?q=` | `getSearchSuggestions` |
| `GET /api/trending?limit=` | En çok izlenen + son eklenen |
| `GET /api/refresh?days=&limit=` | `collector.refreshStale` |
| `GET /api/backup` | SQLite `VACUUM INTO` snapshot |
| `GET /api/metrics` | Bellek-içi istek/hata/ortalama ms sayaçları (`onResponse` hook) |

> **Parite kuralı:** her `/api/*` yolu için `/api/v1/*` kopyası da vardır (Postman uyumu).

- **Süreçler:** `start()` → `createEngineFromEnv()` → **API :3001** + **Web :3002**
  (`@fastify/static` ile `apps/web` servisi) — iki ayrı Fastify instance'ı
- **Log:** JSON log **yok**; okunur terminal logu (`logger.ts`: `[20:16:03] GET /api/search → 200 · 120ms`)
- `parseSearchQuery` query-string → `SearchQuery` çevirisi (AI: `true|1|yes|on`)

---

## 13. CLI (packages/cli)

- **`index.ts` (parametrik):** `commander` — `search` · `collect` · `stats` · `trending` ·
  `refresh` · `backup` · `stream`. `normalizeArgv` tek tireli bayrakları (`-ai`) `--ai`'ye çevirir.
  `--ai` + `-q` yok + TTY ise interaktif soru sorar. `--json` çıktıyı bozmaz (progress **stderr**'e).
- **`wizard.ts` (menü):** stdin TTY değilse cevaplar önceden okunur (test edilebilirlik).
- **`run.ts` (tek kaynak):** `buildQuery` (SearchQuery üretimi) · `withEngine` · `runSearch`.
- **`format.ts`:** `boxTable` · `box` · `renderResults` · `renderAnalysis` · `renderStats` ·
  `renderTrending` · `renderStream` — Unicode `┌─┐│├┤└┘` çerçeveli, ANSI renkli.

---

## 14. Web (apps/web)

- **Vanilla JS + ITCSS/BEM** (ADR-004) — framework yok
- `js/app.js` (arama + filtre + sonuç) · `js/config.js` · `js/admin.js`
- `css/01-abstracts.css` (token'lar `--cm-*`) · `02-base` · `03-layout` · `04-components`
- `index.html` (ana arayüz, `datalist` otomatik tamamlama) · `admin.html` (sistem durumu)
- WCAG: 3px görünür odak, `aria-live` sonuç bölgesi, mobil öncelikli grid

---

## 15. Yapılandırma & Sinyaller

- **`.env`:** `common/env.ts::loadEnvFile` → `process.loadEnvFile` (Node gömülü API);
  yoksa/bozuksa sessizce devam. `.env` git'e girmez.
- **Sabitler (`constants.ts`):** `CACHE_TTL{ram:300,disk:3600,ai:86400}` ·
  `COLLECTOR{cooldown:2500ms, jitter:%20, retries:3, backoff:1500}` ·
  `SEARCH{defaultResult:20, defaultScroll:50, maxScroll:5000, maxResult:1000}` ·
  `QUALITY_THRESHOLDS{high:0.65, medium:0.4}`
- **Sinyaller (`signals.ts`)** — *OS sinyalleri değil*, içerik sinyalleri:
  `detectLanguage(title, desc)` → `tr|en|other` · `spamScore()` → tıklama tuzağı 0–1
  (büyük harf oranı, `!!!`, clickbait kelime, emoji)

---

## 16. Hata Yönetimi (packages/common/src/errors.ts)

- **`NovaSearchError(code, message, cause?)`** — `code: NovaErrorCode` =
  `VALIDATION | NETWORK | PROVIDER | DATABASE | AI | RATE_LIMITED | NOT_FOUND | INTERNAL`
- Yardımcılar: `isNovaSearchError` · `isNetworkError` (offline fallback kararı)
- API'de yakalanır → `{ error: '...' }` gövdesi (ör. `q parametresi gerekli`)

---

## 17. Test Mimarisi

- **Çalıştırıcı:** `pnpm test` → **`bun test tests/`** (esbuild yok; Bun'ın kendi test çalıştırıcısı)
- **9 test dosyası:** `common-utils` · `cache` · `database` · `ranking` · `enricher` ·
  `mapper` · `search-index` · `ai-filter` · `signals` (+ `helpers.ts`)
- **Mock:** `SearchEngineDeps` / saf fonksiyonlar (`scoreVideo`, `applyFilters`, mapper)
  sayesinde ağ olmadan test edilir
- **`pnpm test:coverage`** kapsam raporu üretir

---

## 18. Dağıtım & Geliştirme

```bash
pnpm install                       # bağımlılıklar
pnpm exec tsc --noEmit             # typecheck — 0 hata
pnpm lint                          # eslint
pnpm test                          # bun test
pnpm nova -- search "sorgu"        # CLI
pnpm wizard                        # menü sihirbazı
pnpm api                           # API(3001) + Web(3002)
```

- **Süreç modeli:** tek Node/Bun süreci; dikey ölçeklenir
- **Depolama:** `data/novasearch.db` (SQLite) + `.cache/novasearch` (disk cache) + `data/backups`
- **Bağımsızlık:** Next.js/Vercel/GA bağımlılığı **yok**

---

## 19. Sürüm Geçmişi (docs)

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 0.1.0 (docs) | 2026-10-17 | Tüm dokümanlar (README/PRD/MIMARI/TANIMLAR/TEST-URLS) mojibake sonrası **kaynak kod okunarak** sıfırdan yeniden yazıldı; Express/Data API/pnpm-build iddiaları düzeltildi (Fastify + youtubei.js + bun) |
