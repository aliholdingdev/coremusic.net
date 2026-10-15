# NovaSearch — REST API

**Base URL:** `http://127.0.0.1:3100` (`.env`: `API_HOST` + `API_PORT`) · Başlatma: `npm run serve`

> Web GUI (3110) bu API'ye `fetch` ile bağlanır; API tek başına da kullanılabilir.

## Genel kurallar

| | |
|---|---|
| Yöntem | `GET` + `OPTIONS` (CORS ön-elyazımı) |
| CORS | `Access-Control-Allow-Origin: *` |
| Cache | `Cache-Control: no-store` (istemci önbelleği yok; sunucu kendi katmanlarını kullanır) |
| Hız sınırı | IP başına **60 istek/dk** → aşım: `429 RATE_LIMIT` |
| Zarf (başarılı) | `{ "ok": true, "data": ..., "meta"?: ... }` |
| Zarf (hata) | `{ "ok": false, "error": { "code": "...", "message": "..." } }` |

Hata kodları: `400 BAD_REQUEST` (eksik `q`) · `404 NOT_FOUND` (bilinmeyen uç / video yok) · `429 RATE_LIMIT` · `500 INTERNAL`.

---

## `GET /api/v1/health`

```json
{ "ok": true, "data": { "status": "up", "videos": 101, "aiEnabled": true, "offline": false } }
```

## `GET /api/v1/search`

Tek zorunlu param: `q`.

| Param | Varsayılan | Değerler | Açıklama |
|---|---|---|---|
| `q` | *(zorunlu)* | metin | Sorgu |
| `limit` | `24` | 1–5000 | Sayfa başına sonuç (5000+ destekli) |
| `page` | `1` | N | Sayfa |
| `ai` | — | `1` | AI analiz + skor + gerekçe |
| `deep` | — | `1` | AI 3 ek sorgu üretir |
| `explain` | — | `1` | `aiReason` + `matchPct` alanlarını doldurur (AI'yi kendiliğinden açar) |
| `offline` | — | `1` | YouTube'a gitmez (`NOVA_OFFLINE=1` de aynı etki) |
| `prompt` | — | metin | AI'ya gidecek özel prompt (analiz + skor + genişletmeye eklenir; `meta.aiPrompt` olarak yansır) |
| `scroll` | `1` | N | Toplanacak sayfa (YouTube modu) |
| `filter` | — | `v\|s\|sh` | video / song / short |
| `sort` | `relevance` | `relevance\|views\|date\|rating` | Sıralama |
| `minViews` / `maxViews` | — | N | Görüntülenme aralığı |
| `minScore` / `quality` | — | 0-100 | AI skor eşiği |
| `duration` | — | `">600"` / `"60-300"` | Süre (sn) |
| `type` | — | metin | Kategori |
| `uploadDate` | — | `"2015"` / `"<=1y"` | Yükleme tarihi |

**Yanıt:** `data` = `ScoredResult[]` (her eleman tam `VideoRecord` + skorlar + `url`), `meta` = arama meta bilgisi.

### `ScoredResult` (data elemanı)

| Alan | Tip | Not |
|---|---|---|
| `youtubeId` | string | Video ID |
| `title` / `description` | string | |
| `channelId` / `channelName` | string | |
| `durationSec` / `viewCount` | number | |
| `likeCount` | number \| null | `enrich` doldurur |
| `publishedAt` | string \| null | ISO |
| `category` | string \| null | |
| `tags` | string | FTS için boşluklu |
| `thumbnailUrl` | string \| null | |
| `firstSeenAt` / `lastSeenAt` | string | ISO |
| `score` | number 0-100 | Deterministik skor (her zaman var) |
| `aiScore` | number 0-100? | AI açık + başarırsa |
| `aiReason` | string? | Türkçe gerekçe |
| `matchPct` | number? | `-explain`/`explain=1` ile eşleşen uyum |
| `url` | string | Tam izleme bağlantısı: `https://www.youtube.com/watch?v=<id>` |

### `meta`

| Alan | Tip | Not |
|---|---|---|
| `query` | string | Sorgu |
| `source` | `ram\|disk\|db\|youtube` | Sonucun kaynağı |
| `total` | number | Bu sayfadaki sonuç sayısı |
| `tookMs` | number | Toplam süre |
| `aiEnabled` | boolean | AI kullanılabilir mi |
| `offline` | boolean | |
| `expandedQueries` | string[]? | `deep` genişletmeleri |
| `collected` | number? | Bu sorguda YouTube'dan toplanan |
| `limit` / `page` | number | Uygulanan sayfalama (limit max 5000) |
| `aiScored` | number | Bu sayfada AI skoru alan sonuç sayısı |
| `dbVideos` | number | DB'deki toplam video |
| `aiPrompt` | string? | Gönderilen özel AI prompt'u (varsa) |
| `aiPromptApplied` | boolean? | Prompt, AI açıkken uygulandı mı |
| `filters` | object? | Girilen filtreler (`filter`,`sortBy`,`minViews`,`maxViews`,`duration`,`type`,`uploadDate`,`minScore`,`quality` — verilenler) |

### Örnek

```bash
curl "http://127.0.0.1:3100/api/v1/search?q=tarkan&limit=3&ai=1&explain=1&prompt=sadece%20resmi%20klipler"
```

```json
{
  "ok": true,
  "data": [
    {
      "youtubeId": "NAHRpEqgcL4",
      "title": "TARKAN - Kuzu Kuzu (Official Music Video)",
      "channelName": "Tarkan",
      "durationSec": 242,
      "viewCount": 168289777,
      "score": 95,
      "aiScore": 95,
      "aiReason": "Sorguya tam uygun, başlıkta Kuzu Kuzu geçiyor ve Tarkan'ın resmi klibi.",
      "url": "https://www.youtube.com/watch?v=NAHRpEqgcL4"
    }
  ],
  "meta": { "query": "tarkan", "source": "db", "tookMs": 19942, "aiEnabled": true, "offline": false,
            "limit": 3, "page": 1, "aiScored": 3, "dbVideos": 128,
            "aiPrompt": "sadece resmi klipler", "aiPromptApplied": true }
}
```

> AI hizmeti başarısız olursa (`key` yok / timeout / 429) yanıt yine `200` döner; yalnız `aiScore`/`aiReason` eksik olur — **arama asla düşmez**.

İstemci örneği (tarayıcı/Node):

```js
const r = await fetch("http://127.0.0.1:3100/api/v1/search?q=jazz&limit=12&sort=views");
const { ok, data, meta, error } = await r.json();
if (!ok) throw new Error(error.message);
console.log(data.length, meta.source, meta.tookMs);
```

## `GET /api/v1/suggest`

| Param | Varsayılan | Not |
|---|---|---|
| `prefix` | `""` | Tamamlama öneki |
| `limit` | `8` | Sonuç sayısı |

`data`: `string[]` — geçmiş sorgu ön ekleri.

## `GET /api/v1/trending`

| Param | Varsayılan |
|---|---|
| `limit` | `24` |

`data`: `VideoRecord[]` — en çok görülen/son eklenenler.

## `GET /api/v1/videos/{youtubeId}`

`data`: tek `VideoRecord` · bulunamazsa `404 NOT_FOUND` (`video yok: <id>`).

## İlgili dokümanlar

- Kurulum → [INSTALL.md](INSTALL.md) · CLI → [CLI.md](CLI.md) · Web → [WEB.md](WEB.md)
