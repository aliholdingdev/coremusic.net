# NovaSearch Engine

Bağımsız YouTube metadata arama motoru — önce yerel SQLite/FTS5 + cache'e bakar, veri yoksa `youtubei.js` ile toplar, her sonucu kalıcı DB'ye yazar. **Tamamen komut/parametre ile çalışır, menü/wizard yoktur.**

## Gereksinimler

- Node.js ≥ 22.5 (yerleşik `node:sqlite` + FTS5 kullanılır — native modül kurulumu yok)
- `npm install` (tek bağımlılık: `youtubei.js`)

## Kurulum

```bash
npm install
cp .env.example .env      # OPENROUTER_API_KEY isteğe bağlı
```

## Kullanım

```bash
npm run nova -- search "Akasya Durağı" -ai -explain
npm run nova -- search "adele" -result 100 -scroll 3 -f v -sb views
npm run nova -- search "lofi" -q "yavaş sakin piyano" -ai -deep
npm run nova -- search "tarkan" -f s -minv 100000 -offline
npm run nova -- collect "jazz" --target 500   # derin toplama + ilerleme çubuğu
npm run nova -- enrich --limit 20             # video detay + kanal (like/abone) doldurur
npm run nova -- stats                         # DB/cache sayaçları
npm run nova -- cache --prune                 # süresi dolmuş cache temizliği
npm run nova -- serve                         # API 3100 + Web UI 3110
npm run nova -- --help                        # tüm parametreler
```

### Parametre tablosu

| Kısa | Uzun | Anlam |
|---|---|---|
| `-ai` | — | AI katmanını açar (analiz + skor + gerekçe) |
| `-deep` | — | AI 3 ek sorgu üretip birleştirir |
| `-explain` | — | "Neden bu video?" açıklaması |
| `-offline` | — | YouTube'a asla gitmez |
| `-q` | `--query` | Ek AI sorgusu |
| `-eq` | `--extraQuery` | Ek arama terimi |
| `-s` | `--scroll` | Toplanacak sayfa (max 5) |
| `-r` | `--result` | Sonuç limiti |
| `-l` | `--limit` | Sayfa başına sonuç (varsayılan 24) |
| `-f` | `--filter` | `v` video / `s` song / `sh` short |
| `-sb` | `--sortBy` | `relevance\|views\|date\|rating` |
| `-minv` | `--minViews` | Alt görüntülenme |
| `-maxv` | `--maxViews` | Üst görüntülenme |
| `-mins` | `--minScore` | Alt AI skoru (0-100) |
| `-qa` | `--quality` | Kalite eşiği (0-100) |
| `-d` | `--duration` | `>600` / `60-300` (sn) |
| `-p` | `--page` | Sayfa |
| `-t` | `--type` | Kategori |
| `-ud` | `--uploadDate` | `2015` / `<=1y` |
| `-target` | `--target` | `collect` hedef kayıt sayısı (derin mod, progress bar) |
| `-l` | `--limit` | `enrich` video sınırı |

## Mimari

```
src/
├── cli/        parametrik parser + Unicode box tablo + giriş (menü YOK)
├── core/       types + Search Orchestrator (RAM → disk → FTS → collector)
├── cache/      LRU RAM cache + 30 gün disk cache
├── database/   node:sqlite (FTS5, triggers, jobs kuyruğu, enrich)
├── providers/  youtubei.js tek erişim noktası (core asla provider tipi görmez)
├── ai/         OpenRouter: analiz / skor / deep — fail-safe, paylaşılan bütçe
└── apps/       REST API (3100) + Web UI (3110) + collector + enrich
```

**Zincir:** RAM cache → disk cache → SQLite FTS → (veri yoksa) Collector → normalize → dedupe → kayıt.

**Anti-ban:** 1.5–6 sn jitter + dakika max 12 istek + max 5 sayfa.

**AI fail-safe:** key yok / 404 / timeout → AI katmanı devre dışı, arama bozulmaz.

**Çıktı:** sonuçlar Unicode box tabloda (`┌─┬┐…`) + numaralı bağlantı/gerekçe satırları; toplama ve zenginleştirmede `[####....] %NN` ilerleme çubuğu.

**Portlar:** API `3100` (`API_PORT`), Web UI `3110` (`WEB_PORT`) — `nova serve` ikisini birden başlatır; arayüz `public/index.html` (vanilla JS, bağımlılık yok).

## Test

```bash
npm test           # node --test (parse + format)
npm run typecheck  # tsc --noEmit
```

## API

```bash
GET /api/v1/health
GET /api/v1/search?q=...&limit=24&ai=1&offline=1&filter=v&sort=views
GET /api/v1/suggest?prefix=...
GET /api/v1/trending?limit=24
GET /api/v1/videos/:id
```

Zarf: `{ ok, data, meta }` / hata `{ ok: false, error: { code, message } }` · hız sınırı 60 istek/dk.
