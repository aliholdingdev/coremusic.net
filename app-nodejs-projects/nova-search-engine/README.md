# NovaSearch AI — Bağımsız YouTube Metadata Arama Motoru

> **Özet:** YouTube'u yalnızca *veri sağlayıcı* olarak kullanan; her aramayı, her keşfi
> kendi veritabanına yazan, zamanla büyüyen, internet kapalıyken bile aranabilen
> AI destekli bağımsız arama motoru. Üç yüzey: **CLI (2 adet)** · **Web** · **API**.

| | |
|---|---|
| Teknoloji | Bun 1.4+ (çalıştırıcı/test) · TypeScript (strict) · pnpm workspace |
| Veri | SQLite (dev) / MySQL (prod) · `node:sqlite` — sıfır bağımlılık |
| AI | OpenRouter **free** modeller (`openrouter/free` + fallback zinciri) |
| Kaynak | `youtubei.js` 18.x (YouTube Data API v3 **kullanılmaz** — ADR-001) |
| Web | Vanilla JS + ITCSS/BEM (ADR-004) · `@fastify/static` |
| Node | `>=22.0.0` (package.json `engines`) |
| Lisans | Yerel/kişisel kullanım |

> **Docs seti:** `README.md` (bu dosya) · [`docs/PRD.md`](docs/PRD.md) (NE yaptığını) ·
> [`docs/MIMARI.md`](docs/MIMARI.md) (NASIL çalıştığını) · [`docs/TANIMLAR.md`](docs/TANIMLAR.md)
> (tüm tipler/arayüzler/sözleşmeler) · [`docs/TEST-URLS.md`](docs/TEST-URLS.md) (test URL'leri)

---

## 1. Felsefe — neden "bağımsız"?

Klasik bir arama aracı her sorguda dış kaynağa gider; aynı veri tekrar tekrar çekilir,
sistem kendi bilgisini oluşturamaz. NovaSearch farklı çalışır:

```
Kullanıcı → Cache (RAM→Disk) → Yerel DB → [yetersizse] YouTube → normalize → DB → yanıt
```

- Bir içerik **bir kez** keşfedildikten sonra aynı metadata internetten tekrar çekilmez.
- Her arama, her keşif kalıcıdır: sistem gün geçtikçe büyür (500 → 5.000 → 500.000…).
- İnternet kapalıyken daha önce keşfedilenler **offline** aranabilir (`--offline`).
- YouTube'a giden istek sayısı minimumdur; istekler insansı ritimle (jitter + backoff)
  gönderilir — amaç *sürdürülebilir* çalışmadır (sorumlu toplama, ADR-001).

---

## 2. Kurulum

```bash
# 1) Bağımlılıklar
pnpm install

# 2) Yapılandırma
#    .env dosyasını kontrol edin (bkz. .env.example)
#    OPENROUTER_API_KEY yalnız .env'de durur — ASLA commit edilmez

# 3) Doğrulama
pnpm exec tsc --noEmit   # typecheck — 0 hata zorunlu
pnpm test                # birim testler (bun test)
```

### Çalıştırma

```bash
pnpm nova   -- search "Akasya Durağı" -r 10   # parametrik CLI
pnpm wizard                                  # menü sihirbazı (2. CLI)
pnpm api                                     # API + Web (bkz. §6 portlar)
```

> `pnpm nova` / `pnpm wizard` / `pnpm api` komutları **Bun** ile çalışır
> (`package.json` → `scripts`, `bun <entry>`). `tsc --noEmit` ve `eslint` Node ile de çalışır.

---

## 3. CLI — 1. Parametrik (`nova-search`)

Menü yok; her şey `--flag` ile. Örnek kullanım:

```bash
pnpm nova -- search "Akasya Durağı" -ai -query "bölüm 1" -result 100 -scroll 500
```

> Tek tireli uzun bayraklar (`-ai`, `-query`…) commander uyumu için otomatik
> `--ai`, `--query`'ye çevrilir (`normalizeArgv`).

### `search` bayrakları

| Bayrak | Kısaltma | Açıklama | Varsayılan |
|---|---|---|---|
| `--ai` | `-ai` | AI destekli sorgu genişletme + skorlama | kapalı |
| `--query <text>` | `-q` | AI'a ek yönlendirme promptu | — |
| `--extra-query <text>` | `-eq` | Ekstra AI sorgusu | — |
| `--result <n>` | `-r` | Gösterilecek sonuç | 20 |
| `--scroll <n>` | `-s` | Kaynaktan çekilecek (overscan) | 50 |
| `--filter <v\|s\|sh\|all>` | `-f` | Video / şarkı / shorts | all |
| `--type <video\|channel\|playlist\|all>` | `-t` | YouTube tipi | all |
| `--upload-date <hour\|today\|week\|month\|year\|all>` | `--udate` | Yüklenme zamanı | all |
| `--duration <short\|medium\|long\|all>` | `-d` | Süre | all |
| `--sort-by <relevance\|rating\|upload_date\|view_count>` | `--sb` | Sıralama | relevance |
| `--min-views <n>` | `--minv` | Min. izlenme | 0 |
| `--max-views <n>` | `--maxv` | Maks. izlenme (0=sınırsız) | 0 |
| `--min-score <n>` | `--mins` | Min. skor (0–1) | 0 |
| `--quality <high\|medium\|low\|all>` | `-qa` | Kalite süzgeci | all |
| `--page <n>` | `-p` | Sayfa | 1 |
| `--limit <n>` | `-l` | Sayfa başına sonuç | =result |
| `--offline` | — | Yalnız yerel veri, internet yok | kapalı |
| `--no-auto` | — | Arka plan oto-collect'i kapat | auto açık |
| `--json` | — | JSON çıktı | kapalı |

> `--ai` verilip AI sorgusu (`-q`) girilmezse ve terminal TTY ise CLI interaktif olarak
> AI yönlendirme sorgusunu sorar.

### Diğer komutlar

```bash
pnpm nova -- collect "sezen aksu" -s 200    # topla + DB'ye yaz
pnpm nova -- stats                          # DB / cache / index / kuyruk
pnpm nova -- trending -r 5                  # en çok izlenen + son eklenen
pnpm nova -- refresh --days 7 -l 50         # bayat metadata tazeleme
pnpm nova -- backup                         # SQLite snapshot (VACUUM INTO)
pnpm nova -- stream <videoId>               # akış/format bilgisi (GoogleVideo)
```

## 4. CLI — 2. Menü Sihirbazı (`nova-search-wizard`)

Acemi kullanıcılar için etkileşimli menü. **Tüm iş mantığı parametrik CLI ile aynı
katmandan** (`packages/cli/src/run.ts`) yürür — iki CLI, tek kaynak.

```
1) Hızlı arama
2) AI destekli arama
3) Metadata topla (collect)
4) İstatistikler
5) Çıkış
```

> Sihirbaz stdin TTY değilse (pipe) cevaplar önceden okunur — test edilebilirlik.

---

## 5. Web Arayüzü

`pnpm api` → tarayıcıda **http://novasearch.coremusic.net:3002** (WEB_PORT).

- Arama kutusu + otomatik tamamlama (`datalist` + `/api/suggest`)
- Filtre paneli (içerik, yüklenme, süre, sıralama, AI, sonuç sayısı)
- Sonuç kartları: başlık, kanal, izlenme, süre, uyum skoru, "neden önerildi", detay
- Vanilla JS + ITCSS katmanları (`apps/web/css/` → `01-abstracts.css` … `04-components.css`)
- Tasarım token'ları yalnız `01-abstracts.css` içinde `--cm-*` ile
- WCAG 2.2: 3px görünür odak halkası, `aria-live` sonuç bölgesi, mobil öncelikli grid
- `admin.html`: sistem durumu + tazeleme/yedek/trend butonları (yerel kullanım)

---

## 6. API

API **3001** (PORT), statik web **3002** (WEB_PORT) ayrı portlarda; API kökü `/` web'e yönlendirir.

| Endpoint | Açıklama |
|---|---|
| `GET /api/search?q=...&ai=true&result=20&filter=all&...` | Arama (CLI ile aynı sözleşmesi) |
| `GET /api/videos/:id` | Yerel DB'den video kaydı |
| `GET /api/videos/:id/stream` | Akış/format bilgisi (GoogleVideo) |
| `GET /api/stats` | DB / cache / kuyruk + son aramalar |
| `GET /api/metrics` | Bellek-içi istek/hata sayaçları |
| `GET /api/trending?limit=5` | En çok izlenen + son eklenen |
| `GET /api/refresh?days=7&limit=20` | Bayat metadata tazeleme |
| `GET /api/backup` | SQLite `VACUUM INTO` snapshot |
| `GET /api/suggest?q=...` | Arama önerileri |
| `GET /api/health` | Sağlık (`{"status":"ok","uptime":…}`) |

> Her `/api/*` yolu için `/api/v1/*` kopyası da vardır (Postman/inci sürüm uyumu).

Örnek:

```bash
curl "http://novasearch.coremusic.net:3001/api/search?q=adele+hello&ai=true&result=5"
```

---

## 7. Mimari

```
nova-search-engine/
├── packages/
│   ├── common/      # tipler, sabitler, hatalar, jitter/backoff, .env, sinyaller, progress
│   ├── youtube/     # youtubei.js 18.x wrapper (client, mapper, search, channel, stream)
│   ├── database/    # DbAdapter (SQLite|MySQL), şema, repository (dedup upsert)
│   ├── cache/       # RAM LRU (L1) → Disk JSON (L2) hybrid cache
│   ├── ai/          # OpenRouter client, model registry, query-analyzer, skorlama, filtre
│   ├── search/      # orkestratör motor: engine, ranking, SearchIndex, recommend, bootstrap
│   ├── workers/     # JobQueue, TokenBucket, CollectorWorker (+refresh)
│   └── cli/         # 2 CLI: index.ts (parametrik) + wizard.ts (menü) + run.ts + format.ts
├── apps/
│   ├── api/         # Fastify: REST + monitoring + statik web sunumu
│   └── web/         # Vanilla JS + ITCSS/BEM
├── tests/           # bun test (9 test dosyası + helpers)
├── data/            # SQLite (git'e girmez)
└── .cache/          # disk cache (git'e girmez)
```

### Katman kuralı

`cli/api → search → (ai, cache, workers) → (youtube, database) → common`

Tek yön bağımlılık; alt katman üst katmanı tanımaz.

### Karar zinciri (her aramada)

1. **Analiz** — `analyzeQuery` (heuristic + opsiyonel AI) → terimler, ipuçları, genişletilmiş sorgular
2. **Cache** (RAM → Disk) — isabet varsa YouTube'a **sıfır** istek
3. **Search Index** — ters indeks (terim → id) üzerinden yalnız alakalı adaylar (MSA)
4. **Toplama** — (online && yetersizse) `CollectorWorker` ile YouTube (max 3 sorgu varyantı)
5. **Normalize + dedup** → DB upsert (aynı `youtube_id` asla 2. kez yazılmaz)
6. **Rank + AI filtre** → filtre, sıralama, sayfalama → yanıt (score + reasons) + cache

---

## 8. Sorumlu Toplama (Anti-Ban)

> Amaç "ban atlatmak" değil; **uzun süre kesintisiz çalışabilen** sürdürülebilir ritimdir.

- Token bucket: dakikada istek sınırı (12/dk)
- İnsansı gecikme: her istek arası `jitter(COLLECTOR_COOLDOWN_MS, ±20%)`
- Hata durumunda exponential backoff + jitter (`backoffMs`, max 3 deneme)
- Sorgu başına max 3 varyant, sayfa başına max 50 continuation (güvenlik tavanı)
- Cache-first sayesinde tekrarlanan aramaların YouTube maliyeti **0**'dır

---

## 9. AI Entegrasyonu (OpenRouter free)

- Anahtar: `.env` → `OPENROUTER_API_KEY` (commit edilmez; `.gitignore` içinde)
- Modeller (`packages/ai/src/models.ts` — 2026-10-16 doğrulandı):
  `openrouter/free` → `google/gemma-4-26b-a4b-it:free` →
  `nvidia/nemotron-3-super-120b-a12b:free` → `thinkingmachines/inkling:free`
- **Fallback zinciri:** anahtar yoksa veya AI hata verirse arama ASLA bozulmaz;
  heuristic analiz devreye girer
- AI ne yapar: sorguyu 3–5 varyanta genişletir (deep search) + istekte `aiRelevanceFilter`
  ile sonuçları puanlar; skorlama çekirdeği heuristic kalır (deterministik + test edilebilir)
- AI rate limit: `AI_RATE_LIMIT_PER_MINUTE` (varsayılan 20/dk)

---

## 10. Veritabanı

| | SQLite (dev) | MySQL (prod) |
|---|---|---|
| Sürücü | `node:sqlite` (`DatabaseSync`, gömülü) | `mysql2` havuzu |
| Seçim | `DB_TYPE=sqlite` | `DB_TYPE=mysql` |
| Şema | tek kaynak (`packages/database/src/schema.ts`), iki dialekt | |
| Ayar | WAL + `foreign_keys=ON` | `connectionLimit=10` |

Tablolar: `videos` · `channels` · `search_log`. Tüm yazma işlemleri
**prepared statement** ile (SQL injection disiplini). Upsert dedup:
`ON CONFLICT(youtube_id) DO UPDATE` (SQLite) / `ON DUPLICATE KEY UPDATE` (MySQL).

---

## 11. Testler

```bash
pnpm test           # bun test (esbuild YOK; Bun kendi çalıştırıcısı)
pnpm test:coverage  # kapsam raporu
```

Kapsam (9 test dosyası): jitter/backoff/queryKey/format · LRU+TTL+hybrid cache ·
SQLite upsert dedup + yerel arama + stale · filtre/sıralama/sayfalama · skorlama+gerekçe ·
mapper (göreli tarih, TR/EN kısaltma, alan çıkarımı) · SearchIndex (ters indeks, MSA) ·
AI filtre (fallback + JSON kurtarma) · sinyaller (dil tespiti, spam skoru).

---

## 12. Yapılandırma (.env)

| Değişken | Açıklama | Varsayılan |
|---|---|---|
| `OPENROUTER_API_KEY` | AI anahtarı (yalnız `.env`) | — |
| `OPENROUTER_API_URL` | OpenRouter kökü | `https://openrouter.ai/api/v1` |
| `AI_USE_FREE_MODELS` | Yalnız ücretsiz modeller | `true` |
| `AI_RATE_LIMIT_PER_MINUTE` | AI istek/dk | 20 |
| `AI_TIMEOUT_MS` | AI timeout | 30000 |
| `AI_MAX_RETRIES` | AI yeniden deneme | 2 |
| `DB_TYPE` | `sqlite` \| `mysql` | sqlite |
| `DB_SQLITE_PATH` | SQLite dosya yolu | `./data/novasearch.db` |
| `MYSQL_HOST/PORT/DATABASE/USER/PASSWORD/SSL` | MySQL bağlantı | — |
| `COLLECTOR_COOLDOWN_MS` | İstek arası temel gecikme | 2500 |
| `COLLECTOR_JITTER_PCT` | Jitter yüzdesi | 20 |
| `COLLECTOR_MAX_CONCURRENT` | Worker eşzamanlılığı | 2 |
| `PORT` / `HOST` | API | 3001 / 127.0.0.1 |
| `WEB_PORT` | Statik web | 3002 |
| `CACHE_DIR` | Disk cache dizini | `./.cache/novasearch` |
| `LOG_LEVEL` | Log seviyesi | info |
| `NODE_ENV` | Ortam | development |

---

## 13. SSS

**Aynı sonuçlar neden tekrar tekrar listelenmiyor?**
Dedup iki kademeli: DB'de `youtube_id` tekildir; yanıt katmanında da güvenlik
dedup'u uygulanır (farklı sorgu varyantlarından gelen aynı video birleşir).

**İnternet yok, arama yapabilir miyim?**
Evet. `--offline` (veya bağlantı yoksa otomatik) — cache + yerel DB ile milisaniyeler içinde.

**AI anahtarı olmadan çalışır mı?**
Evet. `--ai` istenirse bile AI yoksa heuristic analizle devam eder; arama bozulmaz.

**Neden YouTube Data API v3 yok?**
ADR-001: kota sınırı, maliyet ve bağımlılık yerine `youtubei.js` (Innertube) kullanılır.

**Yeni kaynak (Spotify, Vimeo…) eklenebilir mi?**
Evet. `packages/youtube` yerine/birlikte yeni bir provider yazılır; `Video`
sözleşmesine normalize edildiği sürece motor, DB ve web değişmez.

**`likeCount` neden hep 0?**
YouTube bu alanı anonim Innertube isteğinde sunmuyor (platform kısıtı). `stream`/detay
yolunda `collectLikeCount` ile bazı kayıtlarda dolabilir; genelde 0'dır.

---

## 14. Yol Haritası

- **V1 (bu sürüm):** CLI ×2 · Web · API · cache-first · offline · AI free · SQLite/MySQL
- **V2:** semantic/embedding arama, FTS, öneri motoru (kosinüs), admin kimlik doğrulama
- **V3:** bilgi grafiği, speech/OCR analizi, kişiselleştirme, dağıtık kuyruk, çoklu provider

---

## 15. Geliştirme Notları

- `pnpm exec tsc --noEmit` — strict TypeScript, hata toleransı yok
- Yeni özellik = önce test, sonra kod (mevcut testler yeşil tutulur)
- Sabit değer yalnız `apps/web/css/01-abstracts.css` içinde (`--cm-*`) veya `packages/common/src/constants.ts`
- Her `/api/*` endpoint'i için `/api/v1/*` paritesi eklenir
- Commit kararı orkestratöre aittir (subagent commit atmaz)
- `.ai/` vault: kararlar (`.ai/decisions/`), hafıza (`MEMORY.md`), log (`log.md`)
