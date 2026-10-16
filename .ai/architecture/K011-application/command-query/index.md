---
title: "K011 · command-query — index"
type: architecture
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/command-query — Komut & Sorgu Akışları

**Amaç:** Bu klasör, CoreMusic uygulama katmanında "yazma (command)" ve "okuma (query)" akışlarının
nereye denk düştüğünü disk kanıtıyla tarif eder. Repo'da klasik CQRS sınıfları (`*Command`, `*Query`,
`*Handler`) **yoktur**; komut/sorgu ayrımı mevcut iki omurga üzerinde modellenir: PHP API Gateway
(`shared/src/Api/Gateway.php` — istek → pipeline → handler) ve JS SPA router
(`assets.coremusic.net/js/router/` — navigate → fetch → patch).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | ELI15 | mimari | kavram | Komut/sorgu ayrımı tek cümlede |
| §2 | GRUPLAR | mimari | katalog | Bu dosyanın bölüm haritası |
| §3 | Omurga | kanit | disk | Gateway + router + DTO gerçek satırları |
| §4 | Kardeş klasörler | mimari | sınır | commands/queries/usecase-akislari ile fark |
| §5 | Açık sorular | kanit | boşluk | CQRS sınıf eksikliği dürüst kaydı |

## §1 — ELI15

Bir istek gelir: ya dünyayı **değiştirir** (komut — "şarkıyı ekle", `POST /api/v1/music`) ya da
**sadece okur** (sorgu — "şarkıyı listele", `GET /api/v1/music`). CoreMusic'te bu ayrım sınıf
isimlerinde değil, HTTP methodu + route tablosu + DTO şeklindedir: `RouteTable` method-aware
eşleşir (`shared/src/Api/Routing/RouteTable.php`, 253 satır), Gateway aynı `dispatch()` yolundan
geçirir (`shared/src/Api/Gateway.php:50`), farkı middleware ve handler belirler.

## §2 — GRUPLAR (dosya kataloğu)

| Dosya | Grup | İçerik |
|-------|------|--------|
| `command-query-kapsam.md` | mimari | ELI15, sınır (ne DEĞİL), komut/sorgu akış tabloları, kapsam dışı, açık sorular |
| `command-query-dogrulama.md` | kanit | İddia↔Dosya↔Satır (≥20 satır), test envanteri, kapı PASS/BLOCK, KPI, olumsuz senaryolar |
| `command-query-web-arastirma.md` | kanit | PHP havuzu 4 URL + claim↔repo matrisi + kesişim kontrolü |
| `command-query-ornekler.md` | kod | PHP + Vanilla JS + SQL + CSS gerçek bloklar, YAPMA↔YAP, `PLANNED` boşluklar |
| `command-query-bilesenler.md` | mimari | Api/ 34 dosya + js/router/ 23 dosya SAYIM envanteri, bağımlılık grafiği, boşluklar |

## §3 — Omurga (disk kanıtı, 2026-10-15 ölçümü)

| # | Katman | Dosya | Kanıt |
|---|--------|-------|-------|
| 1 | Arayüz | `shared/src/Contracts/Api/GatewayInterface.php` | `dispatch(array $request): array` — satır 25 |
| 2 | Gateway | `shared/src/Api/Gateway.php` | `dispatch()` satır 50 · `invokeHandler()` satır 152 · callable çağrısı satır 155 (168 satır toplam) |
| 3 | Rota tablosu | `shared/src/Api/Routing/RouteTable.php` | method-aware eşleşme (253 satır toplam) |
| 4 | Request DTO | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | `getQuery()` satır 30 · `fromArray()` clamp `max(1,…)`/`min(100,…)` satır 76–77 (94 satır) |
| 5 | Query okuma | `shared/src/Api/ApiRequest.php` | `getQueryParams(): array` — satır 76 (157 satır) |
| 6 | Sorgu string | `shared/src/PageRouter/RequestNormalizer.php` | `PHP_URL_QUERY` satır 52 · `$_SERVER['QUERY_STRING']` satır 94 |
| 7 | JS sorgu | `assets.coremusic.net/js/auth/gender-select.js` | `URLSearchParams` satır 20 · `fetch(postUrl,…)` satır 48 |
| 8 | JS fetch sarmalayıcı | `assets.coremusic.net/js/router/FetchWrapper.js` | `fetch(url,{method:'GET',…})` satır 52 (63 satır) |
| 9 | SQL okuma yüzeyi | `.ai/.sql/mysql/coremusic_logs.sql` | `search_logs` satır 398 · `page_views` satır 341 · `analytics_daily_*` satır 18/34/52/71 |

## §4 — Kardeş klasörler ile fark

| Klasör | Fark (bu klasör ne YAPMAZ) |
|--------|----------------------------|
| `commands/`, `queries/` | Sınıf-seviyesi envanter (varsa `*Command`/`*Query` tipleri) — bu klasör **akış** ( akış ≠ tip envanteri) |
| `usecase-akislari/` | Kullanıcı senaryosu zinciri (PageRouter + Middleware omurgası) — bu klasör HTTP methodu + veri yönü eksenidir |
| `routing/` | Rota tablosu/matcher detayı — bu klasör yalnız route'u veri yönü ayıracı olarak kullanır |
| `application-services/` | Servis nesneleri — bu klasör servis içi değil, servis-öncesi/sonrası akışları tarif eder |

## §5 — Açık sorular (özet)

1. Repo'da `*Command` / `*Query` / `*Handler` adlı CQRS sınıfı **0 adet** — ayrım yalnız
   HTTP method + DTO ile mi kurulacak, yoksa sınıf katmanı eklenecek mi? → ⚠️ VERIFICATION REQUIRED
2. `Gateway::invokeHandler` satır 155'teki callable (`$handlerResolver`) command/query
   ayrımını taşıyacak mı, sadece controller çözümleyici mi? → ⚠️ VERIFICATION REQUIRED
3. Sorgu tarafı cache stratejisi (APCu, `shared/src/Cache/`) komut tarafından nasıl
   izole edilecek? → ⚠️ VERIFICATION REQUIRED

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
