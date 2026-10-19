---
title: "NovaSearch AI — Ürün Gereksinimleri"
type: prd
status: active
version: 0.1.0
updated: 2026-10-17
encoding: UTF-8
---

# PRD — Product Requirements Document

> **Ne yaptığını** anlatır. Teknik detay → [MIMARI.md](MIMARI.md) · Tanımlar → [TANIMLAR.md](TANIMLAR.md)
>
> Bu belge 2026-10-17'de kaynak koddan doğrulanarak yazılmıştır (zero-hallucination).

---

## 1. Ürün Tanımı

NovaSearch AI, **bağımsız (self-hosted) bir YouTube metadata arama motorudur**.
YouTube'u yalnızca *veri sağlayıcı* olarak kullanır; her keşfedilen içerik kendi
veritabanına yazılır. Sistem kendi bilgisini biriktirir: bir içerik bir kez
keşfedildikten sonra aynı metadata internetten tekrar çekilmez.

**Çekirdek iddia:** Klasik arama araçları her sorguda dış kaynağa gider ve aynı veriyi
tekrar çeker. NovaSearch önce kendi verisine bakar (`Cache → Yerel DB`), yalnızca
yetersizse ve online ise kaynaktan toplar. İnternet kapalıyken bile daha önce
keşfedilenler aranabilir (`--offline`).

**Üç kullanıcı yüzeyi:**
1. **Parametrik CLI** (`nova-search`) — `--flag` tabanlı, menüsüz
2. **Menü Sihirbazı** (`nova-search-wizard`) — etkileşimli, acemi dostu
3. **Web arayüzü + REST API** — tarayıcıdan arama, filtre, istatistik

**Ürün adı:** NovaSearch AI · **Paket:** `novasearch` · **Sürüm:** 0.1.0 · **Lisans:** Yerel/kişisel kullanım

---

## 2. Hedef Kitle

| Persona | İhtiyaç |
|---|---|
| Yerel arama meraklısı | Dış servise (Google/YouTube UI) bağımlı olmadan metadata arama |
| İçerik araştırmacısı | Kalıcı, büyüyen yerel arşiv + "neden önerildi?" gerekçeleri |
| Teknik kullanıcı | CLI + API ile otomasyon; kendi sunucusunda çalıştırma |
| Geliştirici | `pnpm` workspace, strict TypeScript, test kapsamı |

---

## 3. Kullanıcı Hikâyeleri

### Epic A — Arama

- **US-01:** Kullanıcı sorgu yazarsa (`"Akasya Durağı"`) sistem önce **Cache (RAM→Disk)**
  ve **yerel DB**'ye bakar; yetersizse ve online ise YouTube'dan toplar; normalize +
  dedup + sıralama ile sonuç döner.
- **US-02:** Kullanıcı `--ai` derse sorgu 3–5 varyanta genişletilir (deep search) ve
  sonuçlar AI ile ilgi puanlanır; alakasızlar elenir, puanlar harmanlanır.
- **US-03:** Kullanıcı `--offline` derse yalnız yerel veri aranır (internet kullanılmaz).
- **US-04:** Kullanıcı `--result 100 --scroll 500` ile gösterilecek/çekilecek (overscan)
  sayıları belirler.
- **US-05:** Sonuçlar `page`/`limit` ile sayfalanır.
- **US-06:** Kullanıcı `--filter v|s|sh`, `--type`, `--upload-date`, `--duration`,
  `--sort-by` süzgeçleriyle sonuçları daraltır.
- **US-07:** Kullanıcı `--min-views/--max-views/--min-score/--quality` eşikleri verirse
  sonuçlar süzülür (eşik kalite `QUALITY_THRESHOLDS` — high 0.65, medium 0.4).
- **US-08:** AI anahtarı yoksa veya AI hata verirse arama **asla bozulmaz** — heuristic
  analiz devam eder (fallback).

### Epic B — Kalıcılık (bağımsızlık)

- **US-09:** Bir içerik bir kez keşfedildiğinde aynı metadata ikinci kez çekilmez
  (`youtube_id` dedup upsert).
- **US-10:** `auto-collect` (varsayılan açık) arka planda derin toplama + detay
  zenginleştirme yapar; kullanıcı beklemez.
- **US-11:** Kullanıcı `refresh` ile bayat metadata kayıtlarını tazeler (kayıp olmadan).
- **US-12:** Kullanıcı `backup` ile SQLite anlık görüntüsü alır (`VACUUM INTO`).

### Epic C — Sorgu Analizi & Öneri

- **US-13:** Sorgudan ipuçları çıkarılır: `canlı/live`, `resmi/official`, `cover`,
  `remix`, yıl aralığı (`2010-2015`), dil (tr/en) — bunlar sıralamayı etkiler.
- **US-14:** Her sonuç için "Neden önerildi?" gerekçeleri üretilir (başlık/açıklama
  eşleşmesi, yüksek izlenme, güncellik, format).
- **US-15:** Sonuçların altında **benzer öneriler** sunulur (indeks terim benzerliği).

### Epic D — Kaynak & Sorumlu Toplama

- **US-16:** Kaynak `youtubei.js` (Innertube) üzerinden çalışır — **YouTube Data API v3
  kullanılmaz** (ADR-001).
- **US-17:** İstekler insansı ritimle gider: cooldown + jitter, token bucket (12/dk),
  exponential backoff — amaç sürdürülebilir çalışmadır.
- **US-18:** Sorgu başına max 3 varyant, continuation'da 50 sayfa güvenlik tavanı.

### Epic E — Kurulum & Kullanım

- **US-19:** Kullanıcı `.env` kopyalar; `OPENROUTER_API_KEY` yalnızca AI için gerekli
  (AI'sız da çalışır). `pnpm install` → `pnpm nova -- search "sorgu"` çalışır.
- **US-20:** `pnpm api` → API (3001) + statik Web (3002) birlikte ayağa kalkar.
- **US-21:** `pnpm test` (bun test) ve `pnpm exec tsc --noEmit` yeşil olmalıdır.

---

## 4. Kapsam

### 4.1 — Dahil (v0.1.0)

- Cache-first / offline-first arama motoru (RAM LRU → Disk JSON → DB → YouTube)
- Ters indeks (`SearchIndex`) ile aday seçimi — tüm DB her sorguda taranmaz
- `youtubei.js` ile arama, detay kazıma, akış/format bilgisi, kanal detayı, öneri
- SQLite (`node:sqlite`, sıfır bağımlılık, WAL) / MySQL (`mysql2` havuz) — tek şema
- Hybrid cache + dedup upsert repository
- `JobQueue` + `TokenBucket` + `CollectorWorker` (auto-collect, refresh)
- OpenRouter AI: sorgu genişletme + ilgi filtresi (free model fallback zinciri)
- Parametrik CLI + menü sihirbazı + Unicode box tablo çıktı
- Fastify REST API + monitoring sayaçları + Vanilla JS Web (ITCSS/BEM)
- 9 test dosyası (bun test)

### 4.2 — Hariç (bu sürümde YOK)

- YouTube Data API v3 (ADR-001 — bilinçli olarak kullanılmaz)
- Semantic/embedding arama, FTS (V2)
- Admin kimlik doğrulama (V2)
- Next.js / React / Tailwind / Vercel (bilinçli olarak yok)
- Çoklu provider (Spotify, Vimeo…) — `Video` sözleşmesi hazır, provider yok
- Mobil uygulama

---

## 5. Başarı Kriterleri

| Kriter | Hedef | Ölçüm |
|---|---|---|
| Tekrar çekim yok | Aynı içerik 2. kez internetten çekilmez | dedup upsert + test |
| Offline arama | `--offline` milisaniye düzeyinde sonuç verir | yerel test |
| AI'sız çalışabilirlik | Anahtar yokken arama bozulmaz | fallback test (`ai-filter.test`) |
| Type safety | `tsc --noEmit` 0 hata | CI |
| Test | `pnpm test` yeşil | CI |
| Sorumlu toplama | İstekler arasında jitter/cooldown | `YouTubeClient.throttle` |
| Gerekçe şeffaflığı | Her sonuçta "neden önerildi" | `buildReasons` |

---

## 6. Ortam Değişkenleri (Gereksinim Listesi)

> Tam liste ve varsayılanlar → [README §12](../README.md) · Kaynak: `.env.example`

| Değişken | Gerekli mi | Açıklama |
|---|---|---|
| `OPENROUTER_API_KEY` | Hayır (AI için) | AI anahtarı; yoksa AI modu kapalı, arama çalışır |
| `DB_TYPE` | Hayır | `sqlite` (varsayılan) / `mysql` |
| `DB_SQLITE_PATH` | Hayır | `./data/novasearch.db` |
| `MYSQL_*` | Hayır | MySQL bağlantı (host/port/database/user/password/ssl) |
| `COLLECTOR_MAX_CONCURRENT` | Hayır | Worker eşzamanlılığı (2) |
| `COLLECTOR_COOLDOWN_MS` / `COLLECTOR_JITTER_PCT` | Hayır | İstek arası gecikme / jitter |
| `PORT` / `HOST` / `WEB_PORT` | Hayır | 3001 / 127.0.0.1 / 3002 |
| `CACHE_DIR` | Hayır | `./.cache/novasearch` |

> **Not:** `YOUTUBE_API_KEY` diye bir değişken **yoktur** — kaynak youtubei.js'tir (ADR-001).

---

## 7. Kabul Kriterleri

1. **Arama:** `pnpm nova -- search "sorgu"` → box tablo + neden listesi döner.
2. **Öncelik:** Cache hit'inde YouTube'a **0** istek gider.
3. **Dedup:** Aynı `youtube_id` DB'ye 2. kez ayrı satır olarak yazılmaz.
4. **AI'sız:** `OPENROUTER_API_KEY` boşken `--ai` ile arama hata vermez, heuristic devam eder.
5. **Offline:** `--offline` yalnız yerel veriden sonuç döner.
6. **API:** `GET /api/search?q=...` JSON döner; `/api/health` `{"status":"ok"}` verir.
7. **Test:** `pnpm test` yeşil · `pnpm exec tsc --noEmit` 0 hata.

---

## 8. Riskler ve Bağımlılıklar

| Risk | Etki | Azaltma |
|---|---|---|
| Innertube yanıt yapısı değişimi | Kazıma kırılabilir | `mapper` savunmacı parse + `NovaSearchError('PROVIDER')` |
| Ban/kota | Toplama durur | cooldown + jitter + token bucket + backoff + cache-first |
| AI servisi kesintisi | Genişletme/puanlama yok | heuristic fallback; arama asla bozulmaz |
| DB şema değişimi | `migrate` idempotent (ADD COLUMN yoksayılır) | tek şema, iki dialekt |
| Eski doküman bozulması (mojibake) | Yanlış bilgi | 2026-10-17 tüm docs kod üzerinden yeniden yazıldı |
