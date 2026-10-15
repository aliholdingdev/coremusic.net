---
title: "NovaSearch — Mimari Kararlar (ADR-lite)"
type: decisions
project: nova-search-engine
status: active
updated: 2026-10-15
---

# Karar Kayıtları

## D-001 · youtubei.js v18.1.0 + `getContinuation()` pagination

- **Durum:** Kabul (2026-10-14 probe ile doğrulandı)
- **Bağlam:** v18'de `getNext`/`getMore` metotları YOK — bu adı çağıran kod `undefined is not a function` veriyordu.
- **Karar:** Sayfa zinciri tek metodla: `searchContinuation → getContinuation() → getContinuation() …`; tükendiğinde güvenli `break`.
- **Etki:** `youtubeSearchDeep(query, target, onProgress)` hedefe kadar sayfa sayfa iner (1000+ video kapasitesi).
- **Alternatif:** Eski scraper (reddedildi — ban riski, kırılgan).

## D-002 · `node:sqlite` (DatabaseSync + FTS5)

- **Durum:** Kabul
- **Bağlam:** Sıfır bağımlılık; Node 26 yerleşik `node:sqlite`.
- **Karar:** `DatabaseSync` + FTS5 sanal tablo; enriched alanlar için `LIKE`/`CATEGORY`/`TAGS` kolonları (migration'sız, `ALTER TABLE` ifade korumalı).
- **Kısıt:** Node strip-only modda çalışıyor — TypeScript'te `parameter` property yasak (constructor'a option geçilemez).

## D-003 · OpenRouter fail-safe + paylaşılan AI bütçesi

- **Durum:** Kabul (2026-10-15 timeout krizi sonrası revize)
- **Bağlam:** Reasoning modeller (nemotron) 12 madde skorlamada 25-35 sn sürebiliyor; eski zincir her modele **tam** timeout veriyordu (worst-case 3×timeout, skorlama null → AI rozeti/gerekçe kaybı).
- **Karar:**
  - `chat()` içinde **tek paylaşılan deadline** = `AI_TIMEOUT_MS` (30000); kalan bütçe <3 sn ise fallback yok, null dön.
  - Skorlama üst sınırı 12 → **8 aday** (latency dengesi).
  - `response_format: {type:'json_object'}` + `max_tokens: 2000` — JSON doğruluğu.
  - Skor prompt'unda **"HER id puanlanmalı"** zorunluluğu (kısmi yanıt önlendi).
- **Garanti:** AI asla aramayı düşürmez — null → deterministik skor devrede.

## D-004 · AI filtreleri yalnız CLI sessizse uygulanır (çakışma kuralı)

- **Durum:** Kabul
- **Kural:** `analyzeQuery().filters` (filter/sortBy/minViews/duration/uploadDate) yalnız kullanıcı o parametreyi **vermediğinde** devreye girer; kullanıcı değeri her zaman kazanır.
- **Güvenlik ağı:** AI filtresi tüm havuzu elerse → CLI filtresine **geri sar** (`withAi.length ? withAi : poolF`).

## D-005 · Anti-ban ritmi

- **Durum:** Kabul
- **Değerler:** jitter 1.5–6 sn (rastgele), 12 istek/dk, `NOVA_OFFLINE=1` ile tamamen çevrimdışı.
- **Ek:** Kanal istekleri yalnız `UC` ön ekli ID'lerde atılır (geçersiz ID → istek yok).

## D-006 · Portlar: API 3100 · Web 3110

- **Durum:** Kabul
- **Kaynak:** `.env` → `API_PORT=3100`, `WEB_PORT=3110`.
- **Çalışma:** `nova serve` iki sunucuyu da aynı süreçte başlatır; Web UI `fetch` ile 3100'e gider (CORS `*`).

## D-007 · Çıktı: Unicode box tablo + progress bar

- **Durum:** Kabul (kullanıcı talebi)
- **Karar:** Sonuçlar `┌─┬┐ ├┼┤ └┴┘` tabloda (# / Başlık / Kanal / Süre / İzlenme / Skor); altında numaralı URL + `> gerekçe` satırları. Toplama/zenginleştirmede `[####....] %NN` çubuğu (`format.ts: progressBar`).
- **Genişlik:** Ekran genişliğine göre başlık sütunu esner; emoji/ Türkçe karakter genişliği hesaplanır (`dispWidth`).

## D-008 · Kanal `subs` kaynağı metadata değil header ağacı

- **Durum:** Kabul (probe ile doğrulandı)
- **Gerçek:** `channel.metadata` içinde abone sayısı **yok** (35 anahtarın hiçbiri). Değer `header.content.metadata_parts[].text` içinde `"4.82K subscribers"` metnidir.
- **Karar:** regex `([\d.,]+\s*[KMB]?)\s+subscribers` + `parseCompactCount` → sayı. Bulunamazsa `NULL` (alan zaten nullable).
