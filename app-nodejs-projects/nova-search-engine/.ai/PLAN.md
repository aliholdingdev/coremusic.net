---
title: "NovaSearch — Faz Planı"
type: plan
project: nova-search-engine
status: active
updated: 2026-10-15
---

# NovaSearch Engine — Faz Planı (9 Adım)

Bağımsız YouTube metadata arama motoru. Komut/parametre CLI (menü/wizard YOK),
kalıcı SQLite+FTS5, OpenRouter AI katmanı fail-safe, anti-ban ritmi.

## Adım Durumu

| # | Adım | Durum | Kanıt |
|---|------|-------|-------|
| 1 | `.env` + AI canlı test | ✅ | `-ai -explain -offline` → AI skor + Türkçe gerekçe |
| 2 | Pagination probe | ✅ | v18'de `getContinuation()` tek metod; sayfa 2 = 33 video |
| 3 | Format + progress | ✅ | Unicode box tablo (`format.ts`) + `progressBar()` |
| 4 | AI prompt (filtre/ek sorgu) | ✅ | `analyzeQuery` filtre şeması → CLI parametrelerine eşlenir; `expandedQueries` sonuç havuzuna eklenir |
| 5 | Derin toplama (`--target`) | ✅ | 61 kayıt / 5 sayfa / 29.4 sn + ilerleme çubuğu |
| 6 | Enrich (detay + kanal) | ✅ | `like_count` + `channel_id` + kanal adı + `subs` (4.82K → 4820) |
| 7 | Web UI (3110) | ✅ | `public/index.html` + `web.ts`; API 3100, Web 3110, `nova serve` ikisini birden başlatır |
| 8 | `.ai` vault (bu dosya) | ✅ | PLAN + DECISIONS |
| 9 | Test + typecheck + commit | ✅ | `tsc --noEmit` 0 · `node --test` 13/13 · smoke 9/9 · tarayıcı doğrulandı (1 fetch · 7/8 AI skor · konsol temiz) · commit atıldı |

## Mimari (özet)

```
src/
  cli/       main.ts · parse.ts · format.ts (box tablo) · help.ts
  core/      search.ts (orchestrator) · types.ts
  ai/        engine.ts (OpenRouter, fail-safe, paylaşılan bütçe)
  providers/ youtube.ts (youtubei.js v18 · getContinuation · getInfo · getChannel)
  database/  db.ts (node:sqlite + FTS5 · enrich fonksiyonları)
  apps/      collector.ts · enrich.ts · api.ts (3100) · web.ts (3110)
  cache/     memory-cache.ts · disk-cache.ts
  config.ts  (.env yükler · portlar · AI · anti-ban)
public/index.html   tek dosya arayüz (vanilla JS, bağımlılık yok)
```

## Komut Referansı

```
node src/cli/main.ts search "<sorgu>" [-ai] [-deep] [-explain] [-offline] [-r N] [-s N]
node src/cli/main.ts collect "<sorgu>" --target N     derin toplama + progress bar
node src/cli/main.ts enrich [--limit N]               video detay + kanal + subs
node src/cli/main.ts stats | cache --prune | serve    serve = API 3100 + Web 3110
```

## Kabul Kriterleri (faz sonu)

- [x] `tsc --noEmit` = 0 hata
- [x] `node --test` = 13/13 geçti
- [x] Smoke: search/collect/enrich/stats/serve çıkış kodları 0
- [x] Web UI tarayıcıda render + canlı arama + konsol hatasız
- [x] Secret yalnız `.env`'de (`.gitignore:4`)

## Sonraki Potansiyel Fazlar (bu faz dışı)

- Playlist toplama (`getPlaylist` — v18'de henüz denenmedi)
- Kanal abone sayısının tüm eski satırlarda yenilenmesi (tek seferlik `enrich --channels`)
- Web UI'da SSE ile gerçek yüzdeli ilerleme
- Skor kalibrasyonu: gerçek tercih verisiyle eşik ayarı
