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
| `enrich [--limit N]` | Eksik videoları `getInfo` ile doldurur (like/tarih/etiket) + kanal adı/abone çeker |
| `stats` | DB / cache / kuyruk sayaçları |
| `cache --prune` | Süresi dolmuş disk cache girişlerini siler |
| `serve` | API (3100) + Web GUI (3110) birlikte başlatılır |
| `--help` / `--version` | Yardım / sürüm |

## Arama parametreleri

| Kısa | Uzun | Değer | Açıklama |
|---|---|---|---|
| `-ai` | — | flag | AI katmanı: analiz + filtre + skor + gerekçe |
| `-deep` | — | flag | Deep Search: AI 3 ek sorgu üretir, havuza ekler |
| `-explain` | — | flag | Sonuçlara `> gerekçe` satırı (AI'yi kendiliğinden açar) |
| `-offline` | — | flag | YouTube'a asla gitmez |
| `-json` | — | flag | Box tablo yerine ham JSON (`payload` tam hâli) |
| `-q` | `--query` | metin | Ek AI sorgusu |
| `-eq` | `--extraQuery` | metin | Ek arama terimi |
| `-s` | `--scroll` | N | Toplanacak sayfa (max 5) |
| `-r` | `--result` | N | Sonuç limiti |
| `-l` | `--limit` | N | Sayfa başına sonuç (varsayılan 24) |
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

`collect`/`enrich` bayrakları: `--once` (tek tur), `--limit` (enrich video sınırı), `--prune` (cache).

## Örnekler

```bash
npm run nova -- search "Akasya Durağı" -ai -explain
npm run nova -- search "adele" -result 100 -scroll 3 -f v -sb views
npm run nova -- search "lofi" -q "yavaş sakin piyano" -ai -deep
npm run nova -- search "tarkan" -f s -minv 100000 -offline
npm run nova -- search "jazz" -d ">600" -mins 70 -json
npm run nova -- collect "kayıp şarkılar" --target 500
npm run nova -- enrich --limit 30
```

## Çıktı biçimi

Sonuçlar **Unicode box tabloda** sunulur; altında numaralı bağlantı (+ `-explain` ise gerekçe):

```
 "tarkan kuzu kuzu" · 3 sonuç · kaynak: db · 10ms · OFFLINE

┌─────┬───────────────────────────────────────────┬──────────────────┬─────────┬─────────┬─────────┐
│ #   │ Başlık                                    │ Kanal            │    Süre │ İzlenme │    Skor │
├─────┼───────────────────────────────────────────┼──────────────────┼─────────┼─────────┼─────────┤
│ 01  │ TARKAN - Kuzu Kuzu (Official Music Video) │ Tarkan           │    4:02 │ 168.3 M │  AI 95  │
│ 02  │ TARKAN - Hüp (Official Music Video)       │ Tarkan           │    4:03 │  39.4 M │      72 │
└─────┴───────────────────────────────────────────┴──────────────────┴─────────┴─────────┴─────────┘
 01 https://youtu.be/NAHRpEqgcL4
     > Video tam olarak aranan 'Kuzu Kuzu' klipidir ve metadata eksiksiz.
 02 https://youtu.be/4tLOG4us6zc
```

- **Skor sütunu:** `AI nn` (sarı) = OpenRouter skoru; düz `nn` = deterministik skor (AI yoksa/bütçe dolarsa).
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

Tam `payload` hâli: `{ "results": ScoredResult[], "meta": {...} }` — alan şeması [API.md](API.md) ile aynıdır.

## Çıkış kodları

| Kod | Anlam |
|---|---|
| `0` | Başarılı |
| `2` | Parametre hatası (bilinmeyen komut/flag, eksik değer) |
| `1` | Beklenmeyen hata |

## İlgili dokümanlar

- Kurulum → [INSTALL.md](INSTALL.md) · API → [API.md](API.md) · Web → [WEB.md](WEB.md)
