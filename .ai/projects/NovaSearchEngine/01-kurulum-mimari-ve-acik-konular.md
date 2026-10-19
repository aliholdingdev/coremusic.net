# NovaSearchEngine — 01 Kurulum, Mimari ve Açık Konular

> Sürüm: 1.0.0 · Tarih: 2026-10-16 · Durum: Aktif (kurulum fazı tamam, sağlamlık fazı açık)
> Proje kökü: `C:\www\coremusic.net\app-nodejs-projects\nova-search-engine`
> İlgili: [[PROJECTS.md]] §7 (Medya Depolama modülü) · Proje içi vault: `.ai/decisions/ADR-001..007` · `docs/PRD.md` (391 satır)

---

## 1. Konum Düzeltmesi (2026-10-16 — bağlayıcı kayıt)

| Konum | Durum | Kanıt |
|-------|-------|-------|
| `C:\www\coremusic.net\app-nodejs-projects\nova-search-engine` | ✅ **AKTİF PROJE KÖKÜ** | git 5 commit (2026-10-16), working tree temiz (0 untracked), CreationTime 2026-10-16 13:27 |
| `C:\www\nova-search-engine` | ⛔ **ESKİ/TERK EDİLMİŞ** — referans only | son commit `f267b86` 2026-09-01, son dosya değişimi 2026-09-03, 12 dirty satır, junction DEĞİL (bağımsız kopya) |

**Kural:** Tüm geliştirmeler, vault kayıtları ve çalıştırma komutları yeni konum üzerinden yapılır. Eski kopya silinmez ama kaynak alınmaz; çelişkide yeni konum kazanır.

## 2. Ürün Tanımı

NovaSearch AI — YouTube'u **yalnızca veri sağlayıcı** olarak kullanan; her aramayı/keşfi kendi veritabanına yazan; offline'da daha önce keşfedilmiş içeriği arayabilen; AI (OpenRouter free) destekli **kişisel metadata arama motoru**. YouTube klonu DEĞİL — arama + metadata yönetimi. Kullanım: tek kullanıcı, yerel/kişisel, localhost (HOST=127.0.0.1).

3 yüzey: **CLI** (2 adet) · **Web** (Vanilla JS) · **API** (Fastify, port 3001).

## 3. Commit Tarihi (2026-10-16 — 5 commit, hepsi bugün)

| Hash | Konu |
|------|------|
| `a3430e3` | feat: NovaSearch AI — sıfırdan kurulum (CLI x2 + Web + API) |
| `488d6fe` | build: toolchain Bun'a taşındı — esbuild/tsx/vitest kaldırıldı |
| `3ecb250` | feat: derin çıktı + port 3001 yapılandırması |
| `3992552` | feat: web tamamlandı + PRD + wizard test edilebilirlik |
| `df62629` | chore: youtubei.js parser gürültüsü susturuldu (Log.setLevel ERROR) |

## 4. Mimari (monorepo, pnpm workspace + Bun)

```
nova-search-engine/
├── apps/
│   ├── api/          Fastify — 5 endpoint (health, search, videos, channels, stats)
│   └── collector/    toplama uygulaması
├── packages/
│   ├── ai/           OpenRouter client, model-router, query-analyzer, result-enricher
│   ├── cache/        hybrid cache (RAM→disk)
│   ├── cli/          2 komut: parametrik (flag) + wizard (menü)
│   ├── common/       types, utils
│   ├── database/     DatabaseAdapter → sqlite.ts / mysql.ts, repositories, schema
│   ├── search/       engine, ranking, search-index, bootstrap
│   ├── workers/      collector-worker, queue, rate-limiter (anti-ban)
│   └── youtube/      youtubei.js client, search, mapper, stream
├── tests/            bun test (6 dosya, 40 test)
├── docs/PRD.md       391 satır — kapsam kilidi + kabul kriterleri
└── .ai/              proje vault: index, log, MEMORY, decisions/ADR-001..007
```

**Toolchain (2026-10-16 güncel):** Bun 1.4.2 = çalıştırıcı VE test koşucu. `esbuild/tsx/vitest` KULLANILMAZ. `bun test tests/` · `bun packages/cli/src/index.ts` · `bun apps/api/src/index.ts`. TS denetimi `tsc --noEmit` (`@types/bun`, `types: ["node","bun"]`).

## 5. Kararlar (debate 3 tur / 20 persona + proje ADR'leri)

### 5.1 Proje içi ADR'ler (`.ai/decisions/` — proje vault'u)

| ADR | Karar |
|-----|-------|
| ADR-001 | YouTube Data API v3 YASAK — yalnız youtubei.js |
| ADR-002 | Cache-first / offline-first |
| ADR-003 | DB: SQLite (dev) + MySQL (prod) — PostgreSQL kararı revize edildi |
| ADR-004 | Web: Vanilla JS + TypeScript — React/Vue/Angular YOK (kullanıcı onaylı korundu) |
| ADR-005 | Monorepo (pnpm workspace) |
| ADR-006 | 2 CLI: parametrik flag'li + menü sihirbazı |
| ADR-007 | AI: OpenRouter free modeller + `.env` anahtarı |

### 5.2 Debate kararları D1–D7 (2026-10-16, 3 tur uzlaşması)

| # | Karar |
|---|-------|
| D1 | Wizard CLI = `packages/cli` içinde 2. komut — iş mantığı mevcut command modüllerini çağırır (DRY, tek build hattı) |
| D2 | Sağlamlık sırası: temizlik → offline/cache doğrulama → MySQL adapter canlı test → test kapsamı → (web TAMAM, kapandı) |
| D3 | Anti-ban = sorumlu toplama: token-bucket rate limit + exponential backoff + ±%20 jitter + TTL (`.env`: COLLECTOR_MAX_CONCURRENT=2, COOLDOWN_MS=2500, JITTER_PCT=20 — diskte doğrulandı) |
| D4 | Web Vanilla JS + ITCSS/BEM, `@fastify/static` ile API'den sunum; ekranlar: arama/sonuç/filtre/detay — **3992552'de tamamlandı** |
| D5 | Test: Bun test + coverage gate %75 (şu an ALTINDA — bkz §7) |
| D6 | Embedding/OCR/speech/knowledge-graph → V3 (ertelendi) |
| D7 | DatabaseAdapter arayüzü ile SQLite/MySQL drift yönetimi — `packages/database/src/adapter.ts` mevcut (coverage %100) |

## 6. OpenRouter AI — Canlı Doğrulama (2026-10-16)

- **Anahtar:** `.env → OPENROUTER_API_KEY` (73 karakter) — kullanıcı tarafından verilen yeni anahtarla **birebir eşleşiyor** ✓. `.gitignore` `.env`'i kapsıyor ✓.
- **Canlı test (chat completion):** HTTP 200 · cost 0 · `nvidia/nemotron-3-super-120b-a12b:free` ✓ · `nvidia/nemotron-3.5-lightning:free` ✓ · `thinkingmachines/inkling:free` ✓. Anahtar geçerli (OpenRouter user_id döndü).
- **Rate limit:** `google/gemma-4-*-it:free` → 429 (ücretsiz kota dar) — fallback zinciri şart.
- **Ölü slug:** `deepseek/deepseek-chat-v3-0324:free` → 404 "unavailable for free" (paid).
- **Model registry** (`packages/ai/src/models.ts`, aynı gün doğrulanmış): free = `openrouter/free`, `google/gemma-4-26b-a4b-it:free`, `nvidia/nemotron-3-super-120b-a12b:free`, `thinkingmachines/inkling:free` · paid = `deepseek/deepseek-chat`, `meta-llama/llama-3.1-8b-instruct` (artık ÜCRETLİ — dikkat).
- **Free model havuzu:** OpenRouter `/api/v1/models` → 465 model, 15 tanesi `:free` (2026-10-16 ölçümü; havuz günlük değişir — registry ayda bir yeniden taranmalı).
- AI env: `AI_USE_FREE_MODELS=true` · `AI_RATE_LIMIT_PER_MINUTE=20` · `AI_TIMEOUT_MS=30000` · `AI_MAX_RETRIES=2`.

## 7. Doğrulanmış Mevcut Durum (2026-10-16 ölçümü)

| Metrik | Değer | Kapı | Durum |
|--------|-------|------|-------|
| `bun test` | **40 pass / 0 fail · 169 expect · 634ms · 6 dosya** | — | ✅ |
| `tsc --noEmit` | **0 hata** | 0 | ✅ |
| Coverage (lines) | **%60.30** | %75 | ❌ AÇIK |
| Coverage (funcs) | **%52.21** | %75 | ❌ AÇIK |
| Web | çalışır (E2E doğrulandı: 8 öneri, uyum %90, 60 neden, detay paneli) | — | ✅ |
| API | 5 endpoint + `/api/health` | — | ✅ |
| CLI x2 | parametrik (Unicode box tablo + NEDEN-NEREDE kutusu) + wizard (pipe-dostu) | — | ✅ |
| DB | DB_TYPE=sqlite (dev) · MySQL adapter YAZILI ama CANLI TEST EDİLMEDİ | canlı test | ❌ AÇIK |
| Port | 3001 · `http://novasearch.coremusic.net:3001` (hosts tanımlı) | — | ✅ |

### Kapsamsız dosyalar (coverage 0% — kapı için öncelik sırası)

1. `packages/database/src/mysql.ts` (%6 satır) — canlı MySQL testi bekliyor
2. `packages/search/src/engine.ts` (%7) + `bootstrap.ts` (%14) — offline arama çekirdeği
3. `packages/youtube/{client,mapper,search,stream}.ts` (%0–9) — ağ bağımlı, mock ile kapatılabilir
4. `packages/workers/{queue,rate-limiter,collector-worker}.ts` (%0–14) — anti-ban birimi

## 8. Açık Konular (sıralı — sonraki oturum başlangıç noktası)

1. **MySQL adapter canlı test** — docker-compose servisi hazır değil; yerel MySQL 127.0.0.1:3306 ile denenebilir (`.env`: MYSQL_DATABASE=novasearch, user=root, password boş).
2. **Coverage %75 kapısı** — 40 testten muhtemelen 70–90 test gerekir; önce §7 sıfır-kapsamlı 4 paketten `search` + `database` başlar (ağ bağımlı youtube/workers mock'lanır).
3. **likeCount/publishedAt** — youtube arama düğümlerinde gelmiyor (V2 olarak işaretli).
4. **Free model havuzu taraması** — aylık yenileme (registry bayatlayabilir, §6).

## 9. Değiştirilemez Kurallar (kullanıcı bağlayıcı)

1. CLI flag'li çalışır — menü yalnız 2. wizard komutunda.
2. Web = Vanilla JS + TS (ADR-004); React/Vue istenmedi.
3. Anahtar/secret yalnız `.env`'de; `.md`/`.log`'a yazılmaz (bu dosya hariç — anahtar metni YOK).
4. Git commit orkestratöre ait; subagent atmaz.
5. Araç: **Bun** (vitest/esbuild/tsx değil).
6. localhost-only kullanım — hukuki/ToS kaydı: kişisel yerel araç, tek kullanıcı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-16
**Mode:** Red Team · Human Mode · Truth Mode
