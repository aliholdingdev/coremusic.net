# NovaSearch — Mimari

Katmanlar, veri akışı, DB şeması, cache, anti-ban ve AI fail-safe — kaynak koda birebir hizalı (2026-10-15).

## Katmanlar

```
┌─────────────────────────────────────────────────────────────────┐
│  GİREN YÜZLER                                                   │
│  cli/main.ts (komut+parametre, menü YOK) · apps/api.ts (REST)   │
│  apps/web.ts (statik dosya sunucusu → public/index.html)        │
├─────────────────────────────────────────────────────────────────┤
│  ORKİSTRASYON                                                   │
│  core/search.ts (Search Orchestrator) · core/video-detail.ts    │
│  apps/collector.ts (toplama) · apps/enrich.ts (toplu kazıma)    │
├─────────────────────────────────────────────────────────────────┤
│  SERVİSLER                                                      │
│  ai/engine.ts (OpenRouter — fail-safe) · cache/* · database/db  │
├─────────────────────────────────────────────────────────────────┤
│  SAĞLAYICI (tek erişim noktası)                                 │
│  providers/youtube.ts — youtubei.js: search / getInfo /         │
│  comments / related / channel (core asla provider tipi görmez)  │
├─────────────────────────────────────────────────────────────────┤
│  KALICI DEPO                                                    │
│  node:sqlite — data/novasearch.db (FTS5 + triggers + jobs)      │
└─────────────────────────────────────────────────────────────────┘
```

Kural: **tek yönlü bağımlılık** — giren yüz → orkestrasyon → servis → sağlayıcı → depo. `core/` yalnız `types.ts`'e bakar (domain katmanı, dış bağımlılık yok); `providers/` tek dosyadır — youtubei.js'i değiştirmek tek dosyalık iştir.

## Arama veri akışı (Search Orchestrator)

```
sorgu + parametreler
  ↓ cacheKey(query, filtreler, sayfa, sort)     ← SHA-256, 32 hex
① RAM cache   (LRU 200 giriş, 5 dk)             → varsa: meta.source=ram, ~0ms
② Disk cache  (.cache/search/*.json, 30 gün)    → varsa: meta.source=disk, ~5ms
③ SQLite FTS5 (title/description/tags/channel)  → varsa: meta.source=db
     sonuç yok / yetersiz ↓
④ Collector   (YouTube arama sayfası)
     videoRenderer + shortsLockupViewModel + lockupViewModel
     (shorts → is_short=1; lockup → shorts/song havuzu)
     her kayıt normalize → dedupe → upsert videos (+FTS trigger)
⑤ normalize → deterministik skor → filtreler (f/sb/minv/d/mins/qa…)
⑥ detail zenginleştirme (DEFAULT AÇIK — aşağıda)
⑦ AI skorlama (ai/explain/açıkken; fail-safe)
⑧ sayfalama (limit/page) → results + meta → RAM+Disk cache'e yaz
```

`meta.source` bu zincirin hangi adımda durulduğunu söyler: `ram|disk|db|youtube`.

## Detail zenginleştirme (default açık, DB-first)

Arama mapperları yalnız **sayfa meta verisi** verir (başlık/kanal/izlenme/göreli tarih). Eksik alanlar (`description` · `likeCount` · `durationSec` · `publishedAt` · `category` · `tags` · `channelUrl`) `getInfo` ile **webden kazınır**.

```
sonuç listesi (her video için):
  dbRow = DB.get(id)
  dbRow TAM mı?  (channelId · channelName · durationSec>0 · likeCount!=null
                  · publishedAt ISO · category)
    EVET → lokal merge (web isteği YOK)      meta.detailFromDb++
    HAYIR → yt.getInfo(id)  [1.5-6 sn jitter]
             → mergeDetail (dolu alanlar korunur; publishedAt ISO ezmesi)
             → updateVideoDetail (yalnız EKSİK alanlar CASE WHEN/COALESCE ile yazılır)
                                              meta.detailFetched++
  + aynı aramada tekrar eden ID tek işlenir   meta.detailEnriched = toplam
```

- **Kalıcılık:** kazıma kalıcıdır — sonraki arama aynı videoyu `web=0 · lokal=N` ile anında döner (detail kapalı olsa bile alanlar DB'dedir).
- **Boş kalabilen alanlar:** `tags` (shorts'larda keywords yok), `description` (bazı short'larda başlıkla aynı) — bilerek "tam" ölçütüne girmez; boşuna istek atılmasın.
- **Toplu mod:** `enrich --limit N` → `like_count IS NULL` olan en yeni N kayıt (`last_seen_at DESC`) aynı getInfo yoluyla doldurulur + kanal (abone) çekilir.
- **`publishedAt` savunması:** göreli metin ("6 months ago") ISO sayılmaz (`rowToVideo` → null; `isCompleteDetail` regex reddeder) → enrichment ISO ile doldurur.

## Veritabanı (şema v3)

`data/novasearch.db` — Node ≥22.5 yerleşik `node:sqlite` (native modül yok). İlk açılışta otomatik oluşur.

| Tablo | Rol |
|---|---|
| `videos` | Ana depo: 17 kolon (`youtube_id` PK · başlık/açıklama · kanal 3'lü: `channel_id`/`channel_name`/`channel_url` · `duration_sec` · `view_count` · `like_count` · `published_at` ISO · `category` · `tags` (boşluklu) · `thumbnail_url` · `first_seen_at`/`last_seen_at` · `is_short`) |
| `videos_fts` | FTS5 sanal tablo (`title, description, tags, channel_name`, external content) |
| `channels` | Kanal adı + abone (enrich) |
| `playlists` | Playlist başlık/öğe sayısı |
| `search_history` | Arama geçmişi (suggest kaynağı) |
| `ai_queries` | AI genişletilmiş sorgu geçmişi |
| `jobs` | Kuyruk (`pending/done/failed`, `idx_jobs_status`) |
| `meta` | Şema sürümü (`schema_version=3`) |

- **Triggers:** `videos_ai` / `videos_ad` / `videos_au` — insert/delete/update'te FTS indeksi senkron tutar (external-content FTS5 zorunlu).
- **Migrasyonlar (guard'lı ALTER):** v1→v2 `is_short` · v2→v3 `channel_url` — mevcut DB'ler otomatik yükselir.
- **Upsert:** `INSERT … ON CONFLICT(youtube_id)` — arama verisi taze ise `view_count`/`last_seen_at` güncellenir.
- **`updateVideoDetail`:** yalnız boş/0/NULL alanları günceller (CASE WHEN + COALESCE) — enrichment arama verisini ezmez.

## Cache katmanları

| Katman | Yer | TTL | Kapasite |
|---|---|---|---|
| RAM (LRU) | bellek | 5 dk | 200 giriş |
| Disk (arama) | `.cache/search/<sha256>.json` | 30 gün | sınırsız · `cache --prune` ile temizlenir |
| Disk (yorum/benzer) | `.cache/` (aynı DiskCache sınıfı) | 24 saat | `video` detayı ikinci açılışta anında |

Cache anahtarı: sorgu + filtre objesi JSON → SHA-256 (ilk 32 hex). `detail` anahtara dahildir → zenginleştirilmiş/zenginleştirilmemiş yanıtlar ayrı saklanır.

## Anti-ban

| Kural | Değer (.env) |
|---|---|
| İstekler arası jitter | `COLLECT_MIN_DELAY_MS=1500` … `COLLECT_MAX_DELAY_MS=6000` |
| Dakika tavanı | `COLLECT_RPM=12` |
| Sayılı scroll | `-s N` → `COLLECT_MAX_PAGES=5` |
| Auto scroll | `-s` değersiz → `COLLECT_AUTO_MAX_PAGES=60` |
| Parser gürültüsü | `[YOUTUBEJS]` console.warn susturulur (`NOVA_YT_LOG=1` geri açar) |

`offline` (`-offline` / `offline=1` / `NOVA_OFFLINE=1`): YouTube'a hiç gitmez — yalnız RAM/disk/DB zinciri. Ulaşılamayan kaynaklar sonuçtan çıkar, komut çökmez.

## AI (OpenRouter) — fail-safe

- **Görevler:** sorgu analizi (`analyzeQuery`), sonuç skoru + Türkçe gerekçe (`scoreResults`), deep genişletme (`expandQueries` — 3 ek sorgu), `video` benzerlik skoru (üst sınır 8 aday), `prompt` (özel talimat sisteme eklenir).
- **Model zinciri:** ana model + `OPENROUTER_FALLBACK_MODELS` — paylaşılan `AI_TIMEOUT_MS=30000` bütçe.
- **Fail-safe:** key yok / 404 / timeout / 429 → AI katmanı devre dışı; **arama asla düşmez**, skor sütunu deterministik kalır (`aiScored: 0`, gri rozet).
- **Deterministik skor:** her zaman hesaplanır (başlık/etiket/kanal eşleşmesi) — AI opsiyonel katmandır.

## API zarfı + güvenlik

- Zarf: `{ ok: true, data, meta? }` · hata `{ ok: false, error: { code, message } }`.
- Uçlar: `health` · `search` · `suggest` · `trending` · `videos/:id` (basit/derin) — **asla 500 vermez** (fail-safe); `400` yalnız geçersiz ID/eksik `q`.
- Hız sınırı: IP başına 60 istek/dk → `429 RATE_LIMIT`. CORS `*`. `Cache-Control: no-store`.
- Sırlar yalnız `.env`'de (gitignored) — `.md`/`.json`/`.log`'a yazılmaz.

## Dizin haritası

```
src/
├── cli/         main.ts (giriş) · parse.ts (bayraklar) · format.ts (box tablo/progress) · help.ts
├── core/        types.ts · search.ts (orkestratör) · video-detail.ts
├── cache/       memory-cache.ts (LRU) · disk-cache.ts (TTL+prune)
├── database/    db.ts (şema/migrasyon/FTS/CRUD/enrich/jobs)
├── providers/   youtube.ts (youtubei.js sarmalayıcı + mapper'lar)
├── ai/          engine.ts (OpenRouter)
├── apps/        api.ts (REST) · web.ts (statik) · collector.ts · enrich.ts
└── config.ts    .env yükleyici + tüm ayarlar
public/index.html   Web GUI (vanilla JS, bağımlılık yok)
test/               parse · api-params (boolParam) · format · video (mapper/mergeDetail) · search (isCompleteDetail)
data/novasearch.db  Kalıcı DB (gitignored) · .cache/ DiskCache (gitignored)
```

İlgili: [CLI](CLI.md) · [API](API.md) · [Web](WEB.md) · [Kurulum](INSTALL.md)
