---
title: "NovaSearch AI — Test URL Listesi"
type: test-urls
status: active
version: 0.1.0
updated: 2026-10-17
encoding: UTF-8
---

# TEST URL LİSTESİ — NovaSearch AI

> Başlat: `pnpm api` → **API http://novasearch.coremusic.net:3001** ·
> **Web http://novasearch.coremusic.net:3002**
>
> Her `/api/*` yolu için `/api/v1/*` kopyası da vardır (Postman uyumu).
> Endpoint'ler `apps/api/src/server.ts` ile doğrulanmıştır (2026-10-17).

---

## 1. Hızlı Sağlık Kontrolü (ilk bunları dene)

| # | URL | Beklenen |
|---|---|---|
| 1 | `http://novasearch.coremusic.net:3001/api/health` | `{"status":"ok","uptime":…}` |
| 2 | `http://novasearch.coremusic.net:3002` | Web arayüzü (HTTP 200) |
| 3 | `http://novasearch.coremusic.net:3001/api/stats` | `videoCount` / `cache` / `queue` / `index` / `recent` / `topQueries` |
| 4 | `http://novasearch.coremusic.net:3001/` | 3002'deki web'e yönlendirme |

---

## 2. Arama — API (Postman / curl)

### 2.1 AI'lı derin arama (en zengin çıktı)

```
GET http://novasearch.coremusic.net:3001/api/v1/search?ai=true&q=Akasaya Durağı&type=video&extraQuery=akasya durağı full bölümler
```

### 2.2 Basit arama (offline — hızlı, yerel veri)

```
GET http://novasearch.coremusic.net:3001/api/v1/search?q=Tarkan&result=5&offline=true
```

### 2.3 Tam süzgeçli arama

```
GET http://novasearch.coremusic.net:3001/api/v1/search?q=sezen aksu&ai=true&aiQuery=resmi klipler&filter=all&duration=medium&uploadDate=year&sortBy=view_count&minViews=1000000&quality=high&result=10&scroll=60
```

### 2.4 Parametre sözlüğü (query params)

> Kaynak: `apps/api/src/server.ts::parseSearchQuery`

| Param | Değerler | Not |
|---|---|---|
| `q` | metin | zorunlu (yoksa `{error:"q parametresi gerekli"}`) |
| `ai` | `true` `1` `yes` `on` | AI sorgu genişletme + ilgi filtresi |
| `aiQuery` | metin | AI yönlendirme promptu |
| `extraQuery` | metin | ek AI sorgusu |
| `filter` | `all` `v` `s` `sh` | video / şarkı / shorts |
| `type` | `all` `video` `channel` `playlist` | |
| `uploadDate` | `all` `hour` `today` `week` `month` `year` | |
| `duration` | `all` `short` `medium` `long` | short<300sn · medium 300–1200 · long>1200 |
| `sortBy` | `relevance` `rating` `upload_date` `view_count` | |
| `minViews` / `maxViews` | sayı | 0 = sınırsız |
| `minScore` | 0–1 | minimum skor eşiği |
| `quality` | `all` `high` `medium` `low` | high≥0.65 · medium≥0.4 |
| `result` / `scroll` | sayı | gösterilecek / kaynaktan çekilecek (max 5000) |
| `page` / `limit` | sayı | sayfalama (limit max 1000) |
| `offline` | `true` | yalnız yerel veri |
| `auto` | `false` | oto-collect kapat (varsayılan: açık) |

### 2.5 Video detay + akış bilgisi

```
GET http://novasearch.coremusic.net:3001/api/v1/videos/UF8-6izpAV0
GET http://novasearch.coremusic.net:3001/api/v1/videos/UF8-6izpAV0/stream
```

(stream → `StreamInfo`: itag/kalite/mime/googlevideo URL listesi)

### 2.6 Otomatik tamamlama

```
GET http://novasearch.coremusic.net:3001/api/v1/suggest?q=sezen
```

### 2.7 Diğer endpoint'ler

```
GET /api/metrics                  # istek/hata/ortalama ms sayaçları
GET /api/trending?limit=5         # en çok izlenen + son eklenen
GET /api/refresh?days=7&limit=20  # bayat metadata tazeleme
GET /api/backup                   # SQLite VACUUM INTO snapshot
```

---

## 3. CLI Test Komutları

```bash
# AI'lı arama (box tablo + neden listesi + progressbar)
pnpm nova -- search "Akasya Durağı" -ai -q "full bölümler" -r 5 -s 20

# Offline (hızlı, yalnız yerel veri)
pnpm nova -- search "Tarkan" --offline -r 3

# JSON (tam metadata — progress stderr'e gider, çıktı bozulmaz)
pnpm nova -- search "Ebru Gündeş" -r 1 --json

# Süzgeçli
pnpm nova -- search "sezen aksu" -f v -d medium --sb view_count --minv 1000000 -r 5

# Toplama + istatistik + trend
pnpm nova -- collect "mor ve ötesi" -s 100
pnpm nova -- stats
pnpm nova -- trending -r 5

# Tazeleme + yedek + akış
pnpm nova -- refresh --days 7 -l 50
pnpm nova -- backup
pnpm nova -- stream UF8-6izpAV0

# Menü sihirbazı (2. CLI)
pnpm wizard
```

---

## 4. Web Test Akışı (tarayıcı)

1. `http://novasearch.coremusic.net:3002` aç
2. Arama kutusuna `Ebru Gündeş` yaz → otomatik öneriler düşmeli (`datalist` + `/api/suggest`)
3. Filtre paneli: içerik, yüklenme, süre, sıralama, AI, sonuç sayısı
4. **Ara** → sonuç kartları: başlık, kanal, izlenme, süre, uyum skoru, "neden önerildi"
5. AI işaretle + `aiQuery` ver → `analysis.expandedQueries` kutusu görünmeli
6. `admin.html` → sistem durumu + tazeleme/yedek/trend butonları

---

## 5. Beklenen Zengin JSON Alanları (video nesnesi)

> Kaynak: `packages/common/src/types.ts::Video`

```
youtubeId · title · description · channelId · channelName · channelUrl ·
durationSeconds · viewCount · viewCountText · likeCount · commentCount ·
publishedAt · publishedText · thumbnailUrl · thumbnails[] · tags[] · badges[] ·
category · topComments[] · captionTracks[] · relatedIds[] ·
channelSubscriberCount · channelVerified · isShort · isLive ·
isFamilySafe · isUnlisted · isCrawlable · url ·
language · spamScore · qualityScore · source
```

**Yanıt üst seviyesi (`SearchResponse`):** `query · videos[] · total · source ·
offline · tookMs · aiUsed · analysis{expandedQueries, hints, directive} · recommendations[]`

**Her videoda (`EnrichedVideo`):** `score (0–1) · matchedTerms[] · reasons[]`

Not: `likeCount` genelde `0` döner (Innertube anonim istekte YouTube bu alanı
sunmayabilir); `stream`/detay yolunda `collectLikeCount` ile bazı kayıtlarda dolabilir.

---

## 6. Sorun Giderme

| Belirti | Neden | Çözüm |
|---|---|---|
| Eski/boş alanlar dönüyor | bayat sunucu/eski indeks | sunucuyu yeniden başlat (SearchIndex TTL 60sn) |
| `Port 3001 zaten kullanımda` | başka süreç | çalışanı durdur veya `PORT=3002 pnpm api` |
| Yavaş ilk arama | canlı toplama (cooldown 2500ms) | normal; sonraki aramalar cache'ten |
| Sonuç az | `scroll` düşük | `scroll` büyüt (max 5000) |
| AI çalışmıyor | `OPENROUTER_API_KEY` boş | `.env`'e anahtar ekle — yoksa heuristic devam eder |
| Arama `youtube` dönmüyor | offline/ağ | `offline=false` + ağ kontrolü; ağ hatasında yerel veriye düşer |
