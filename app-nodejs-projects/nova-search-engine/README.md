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
npm run nova -- search "tarkan" -result 3 -detail -json   # boş alanları getInfo ile doldur
npm run nova -- search "tarkan" -s -result 200      # -s değersiz: tüm sayfaları otomatik kaydırıp topla
npm run nova -- search "lofi" -q "yavaş sakin piyano" -ai -deep
npm run nova -- search "tarkan" -f s -minv 100000 -offline
npm run nova -- collect "jazz" --target 5000   # derin toplama + ilerleme çubuğu
npm run nova -- collect "tarkan" -s             # tüm sayfaları otomatik kaydırır (short/lockup dahil, max 60 sayfa)
npm run nova -- video 4tLOG4us6zc -comments 10 -related 10 -ai   # video detayı: yorumlar + AI skorlu benzerler
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
| `-q` | `--query` | Ek AI sorgusu (positional sorgu varken = AI prompt) |
| `-aip` | `--aiPrompt` | AI'ya gidecek özel prompt (analiz + skor + genişletmeye eklenir) |
| `-eq` | `--extraQuery` | Ek arama terimi |
| `-s` | `--scroll` | N sayfa (max 5) · **değersiz `-s` = tüm sayfaları topla (auto, max 60)** |
| `-detail` | — | Her sonuç için getInfo: açıklama/like/süre/tarih/kategori/kanal handle doldurur + DB'ye yazar (yavaş) |
| `-r` | `--result` | Sonuç limiti (max 5000) |
| `-l` | `--limit` | Sayfa başına sonuç (varsayılan 24, max 5000) |
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
| `-comments` | — | `video` yorum sayısı (0-50) |
| `-related` | — | `video` benzer video sayısı (0-20) |
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

**Anti-ban:** 1.5–6 sn jitter + dakika max 12 istek · sayısal `-s` max 5 sayfa · `-s` (değersiz/auto) tavanı `COLLECT_AUTO_MAX_PAGES` (60) — dört toplama modu tablosu → [docs/CLI.md](docs/CLI.md#toplama-scroll-modları).

**AI fail-safe:** key yok / 404 / timeout → AI katmanı devre dışı, arama bozulmaz.

**Çıktı:** sonuçlar Unicode box tabloda (`┌─┬┐…`) + numaralı bağlantı/gerekçe satırları; toplama ve zenginleştirmede `[####....] %NN` ilerleme çubuğu.

**Portlar:** API `3100` (`API_PORT`), Web UI `3110` (`WEB_PORT`) — `nova serve` ikisini birden başlatır; arayüz `public/index.html` (vanilla JS, bağımlılık yok).

## Dokümantasyon

| Doküman | İçerik |
|---|---|
| [docs/INSTALL.md](docs/INSTALL.md) | Kurulum, `.env`, doğrulama, sorun giderme |
| [docs/CLI.md](docs/CLI.md) | Komutlar, tüm parametreler, çıktı biçimi, çıkış kodları |
| [docs/API.md](docs/API.md) | REST uçları, parametreler, zarf şeması, örnekler |
| [docs/WEB.md](docs/WEB.md) | Web GUI kullanımı, kontroller, rozetler |

## Test

```bash
npm test           # node --test (parse + format + mapper)
npm run typecheck  # tsc --noEmit
```

## API

```bash
GET /api/v1/health
GET /api/v1/search?q=...&limit=24&ai=1&offline=1&filter=v&sort=views
GET /api/v1/suggest?prefix=...
GET /api/v1/trending?limit=24
GET /api/v1/videos/:id                              # basit: VideoRecord
GET /api/v1/videos/:id?comments=10&related=10&ai=1  # derin: yorumlar + AI skorlu benzerler
```

Zarf: `{ ok, data, meta }` / hata `{ ok: false, error: { code, message } }` · hız sınırı 60 istek/dk.

**Toplama kapsamı:** Sayfa başı eski nesil `videoRenderer` + yeni nesil **shorts** (`shortsLockupViewModel`, `is_short` işaretli) + **lockup** kayıtları toplanır — arama sonucu sayfası tam süzülür (probe kanıtı: varsayılan sayfada 19 video + 25 short + 10 lockup vardı; eskisi yalnız 19'ini alıyordu).
