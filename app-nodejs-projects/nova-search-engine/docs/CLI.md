# NovaSearch — CLI

> Menü/wizard **yok** — her şey komut + parametre. Üç çağrı şekli aynıdır:
>
> ```bash
> npm run nova -- <komut> [params]     # npm üzerinden
> node src/cli/main.ts <komut> [...]   # doğrudan
> nova <komut> [...]                   # bin (npm link sonrası)
> ```

## Komutlar

| Komut | Ne yapar |
|---|---|
| `search "<sorgu>"` | Arama: RAM → disk → DB/FTS → (veri yoksa) YouTube toplama → AI skor |
| `collect "<sorgu>" [--target N]` | Doğrudan toplama; `--target` = derin mod (hedefe kadar sayfa sayfa, ilerleme çubuğu) |
| `collect "<sorgu>" --auto` | **Auto-scroll**: continuation tükenene kadar sayfa sayfa (max `COLLECT_AUTO_MAX_PAGES`, varsayılan 60) |
| `video <id\|url>` | Video detayı: DB kaydı + yorumlar + benzer videolar (AI skorlu) |
| `enrich [--limit N]` | Eksik videoları `getInfo` ile doldurur (like/tarih/etiket) + kanal adı/abone çeker |
| `stats` | DB / cache / kuyruk sayaçları |
| `cache --prune` | Süresi dolmuş disk cache girişlerini siler |
| `serve` | API (3100) + Web GUI (3110) birlikte başlatılır |
| `--help` / `--version` | Yardım / sürüm |

> **Toplama kapsamı:** Sayfa başı **Video + Shorts (shelf) + LockupView** kayıtları toplanır
> (eski nesil `videoRenderer` + yeni nesil `shortsLockupViewModel`/`lockupViewModel`).
> Short'lar `is_short=1` ile işaretlenir; süreleri shelf'te bulunmadığı için `0`'dır
> (`enrich` getInfo ile doldurur). `nova collect ... --auto` ile song/short havuzu
> otomatik kaydırma ile büyür; `-f sh` / `-f s` arama anında bu havuzu süzer.

## Arama parametreleri

| Kısa | Uzun | Değer | Açıklama |
|---|---|---|---|
| `-ai` | — | flag | AI katmanı: analiz + filtre + skor + gerekçe |
| `-deep` | — | flag | Deep Search: AI 3 ek sorgu üretir, havuza ekler |
| `-explain` | — | flag | Sonuçlara `> gerekçe` satırı (AI'yi kendiliğinden açar) |
| `-offline` | — | flag | YouTube'a asla gitmez |
| `-json` | — | flag | Box tablo yerine ham JSON (`payload` tam hâli) |
| `-q` | `--query` | metin | Ek AI sorgusu (positional sorgu varken -q = AI prompt) |
| `-aip` | `--aiPrompt` | metin | AI'ya gidecek özel prompt (analiz + skor + genişletmeye eklenir) |
| `-eq` | `--extraQuery` | metin | Ek arama terimi |
| `-s` | `--scroll` | N | Toplanacak sayfa (max 5) |
| `-r` | `--result` | N | Sonuç limiti (max 5000) |
| `-l` | `--limit` | N | Sayfa başına sonuç (varsayılan 24, max 5000) |
| `-f` | `--filter` | `v\|s\|sh` | video / song / short |
| `-sb` | `--sortBy` | `relevance\|views\|date\|rating` | Sıralama |
| `-minv` | `--minViews` | N | Alt görüntülenme |
| `-maxv` | `--maxViews` | N | Üst görüntülenme |
| `-mins` | `--minScore` | 0-100 | Alt AI skoru |
| `-qa` | `--quality` | 0-100 | Kalite eşiği |
| `-d` | `--duration` | `">600"` / `"60-300"` | Süre aralığı (sn) |
| `-p` | `--page` | N | Sayfa |
| `-t` | `--type` | metin | Kategori |
| `-ud` | `--uploadDate` | `"2015"` / `"<=1y"` | Yükleme tarihi |
| `-target` | `--target` | N | `collect` hedef kayıt (derin mod + progress bar) |
| `-auto` | `--auto` | flag | `collect`: continuation bitene kadar otomatik kaydırma (hedef yoksa) |

`collect`/`enrich` bayrakları: `--once` (tek tur), `--limit` (enrich video sınırı), `--prune` (cache).

## `video` parametreleri

| Kısa | Uzun | Değer | Açıklama |
|---|---|---|---|
| `-comments` | — | N | Yorum çeker (0-50; üst düzey yorumlar, sayfa sayfa) |
| `-related` | — | N | Benzer/önerilen videolar (0-20; watch_next_feed) |
| `-ai` | — | flag | Benzer videolara **AI benzerlik skoru + gerekçe** (üst sınır 8 aday) |
| `-json` | — | flag | Ham `VideoDetailPayload` |
| `-offline` | — | flag | YouTube'a gitmez; yalnız DB + disk cache |

- İlk argüman **11 karakterlik ID veya tam URL** olabilir (`watch?v=` · `youtu.be` · `/shorts/` · `/embed/` · `/live/`).
- Yorum/benzer verisi **24 saat DiskCache**'te (aynı video ikinci açılışta anında).
- YouTube erişilemezse: `DEGRADED` rozeti + boş bölümler — komut asla çökmez.
- Kısa yorum URL biçimi: `https://www.youtube.com/watch?v=<id>&lc=<commentId>`.

## Örnekler

```bash
npm run nova -- search "Akasya Durağı" -ai -explain
npm run nova -- search "adele" -result 100 -scroll 3 -f v -sb views
npm run nova -- search "lofi" -q "yavaş sakin piyano" -ai -deep
npm run nova -- search "tarkan" -ai -explain -aip "sadece resmi klipleri tercih et"
npm run nova -- search "tarkan" -f s -minv 100000 -offline
npm run nova -- search "jazz" -d ">600" -mins 70 -json
npm run nova -- collect "kayıp şarkılar" --target 5000
npm run nova -- collect "tarkan" --auto
npm run nova -- video 4tLOG4us6zc -comments 10 -related 10
npm run nova -- video "https://youtu.be/4tLOG4us6zc" -ai -comments 5
npm run nova -- enrich --limit 30
```

## Çıktı biçimi

Sonuçlar **Unicode box tabloda** sunulur — **Video ID** ve **URL** tablo kolonudur; `-explain` ise gerekçeler tablo altında numaralı görünür:

```
 "tarkan" · 3 sonuç · kaynak: db · 16ms · OFFLINE

┌────┬─────────────────────┬────────────────┬─────────┬─────────┬─────────┬─────────────┬──────────────────────────────┐
│ #  │ Başlık              │ Kanal          │    Süre │ İzlenme │    Skor │ Video ID    │ URL                          │
├────┼─────────────────────┼────────────────┼─────────┼─────────┼─────────┼─────────────┼──────────────────────────────┤
│ 01 │ TARKAN - Hüp (Remi… │ Tarkan         │    5:00 │   1.2 M │      95 │ OziL_hKtL8Q │ https://youtu.be/OziL_hKtL8Q │
│ 02 │ TARKAN - Hüp (Offi… │ Tarkan         │    4:03 │  39.4 M │      95 │ 4tLOG4us6zc │ https://youtu.be/4tLOG4us6zc │
│ 03 │ TARKAN - Acımayaca… │ Tarkan         │    4:30 │  12.1 M │      95 │ 6SIyWpxuSP0 │ https://youtu.be/6SIyWpxuSP0 │
└────┴─────────────────────┴────────────────┴─────────┴─────────┴─────────┴─────────────┴──────────────────────────────┘
```

- **Skor sütunu:** `AI nn` (sarı) = OpenRouter skoru; düz `nn` = deterministik skor (AI yoksa/bütçe dolarsa).
- **URL sütunu:** kısa tam link `https://youtu.be/<id>` (konsol genişliği için); JSON/API'deki `url` alanı tam biçimdir: `https://www.youtube.com/watch?v=<id>`.
- **Meta satırı:** `kaynak` ∈ `ram|disk|db|youtube`, `OFFLINE` rozeti, `deep:` genişletilmiş sorgular.

### İlerleme çubukları

Toplama ve zenginleştirme canlı çubuk basar (stdout'a `\r` ile):

```
Toplanıyor: [########################] %100 61/60 · sayfa 5
Zenginleştiriliyor: [##########..........] % 50 1/2 iZ2K8oGlvcA
```

### `-json`

```bash
npm run nova -- search "jazz" -r 2 -json
```

Tam `payload` hâli: `{ "results": ScoredResult[], "meta": {...} }` — her sonuçta `url` (tam izleme bağlantısı); `meta` içinde `limit` · `page` · `aiScored` · `dbVideos` · `aiPrompt` · `aiPromptApplied` · `filters`. Alan şeması [API.md](API.md) ile aynıdır.

### `video` çıktısı

```bash
npm run nova -- video 4tLOG4us6zc -comments 5 -related 8 -ai
```

```
 TARKAN - Hüp (Official Music Video)
 Tarkan · 4:03 · 39.4 M izlenme · 14 years ago
 https://www.youtube.com/watch?v=4tLOG4us6zc
 kaynak: db · yorum: 5/5 · benzer: 6/6 · AI skorlu: 6 · 27818ms

 YORUMLAR (5)
┌────┬──────────────────┬─────────────────────────────────────────┬────────┬─────────────┐
│ #  │ Yazar            │ Yorum                                   │ Beğeni │ Tarih       │
├────┼──────────────────┼─────────────────────────────────────────┼────────┼─────────────┤
│ 01 │ @harbikafalar01… │ Aynı kırmızı gömleği ben giysem tırna…  │  2.6 B │ 5 years ago │
└────┴──────────────────┴─────────────────────────────────────────┴────────┴─────────────┘

 BENZER VİDEOLAR (6)
┌────┬───────────────────────────────────┬──────────────┬─────────┬───────────┬─────────┬──────────────────────────────┐
│ #  │ Başlık                            │ Kanal        │ İzlenme │ Tarih     │      AI │ URL                          │
├────┼───────────────────────────────────┼──────────────┼─────────┼───────────┼─────────┼──────────────────────────────┤
│ 01 │ TARKAN - Gülümse Kaderine (Offic… │ Tarkan       │  21.0 M │ 14y ago   │   AI 85 │ https://youtu.be/Skus894Lh_c │
└────┴───────────────────────────────────┴──────────────┴─────────┴───────────┴─────────┴──────────────────────────────┘
```

- **AI sütunu:** `AI nn` = OpenRouter benzerlik skoru (0-100; üst sınır 8 aday); `—` = AI yok.
- Her benzer satırın altında AI gerekçesi `› ...` ile listelenir.
- Kısa satır: `kaynak: db · yorum: 5/5 · benzer: 6/6 · AI skorlu: 6 · 27818ms`; `OFFLINE`/`DEGRADED` rozetleri de meta'da görünür.

## Çıkış kodları

| Kod | Anlam |
|---|---|
| `0` | Başarılı |
| `2` | Parametre hatası (bilinmeyen komut/flag, eksik değer) |
| `1` | Beklenmeyen hata |

## İlgili dokümanlar

- Kurulum → [INSTALL.md](INSTALL.md) · API → [API.md](API.md) · Web → [WEB.md](WEB.md)
