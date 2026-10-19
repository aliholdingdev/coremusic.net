---
title: "K011 · Use Case Akışları — Bileşen Envanteri"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase-akislari
status: active
updated: 2026-10-15
group: mimari
---

# K011 · Use Case Akışları — Bileşen Envanteri

> **Amaç:** `usecase-akislari` akışlarını oluşturan bileşenlerin disk envanteri:
> dizinler, dosyalar, sorumluluk, katman ve bağlantılar. Her sayı sayım kanıtıyla verilir.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Kapsam |
|---|---|---|---|---|
| 1 | §1 Bileşen haritası (genel) | mimari | mimari | 4 katman |
| 2 | §2 PageRouter bileşenleri | mimari | kod | 15 dosya |
| 3 | §3 Middleware bileşenleri | mimari | kod | 12 dosya |
| 4 | §4 İstemci (JS) bileşenleri | mimari | kod | 98 dosya |
| 5 | §5 Olay & veri bileşenleri | mimari | kod | Events/Cache/Api |
| 6 | §6 Bileşen ↔ akış iz düşümü | mimari | mimari | akışlar |
| 7 | §7 Bağımlılık grafiği (metin) | mimari | mimari | yön |
| 8 | §8 Boşluk & PLANNED | mimari | mimari | eksikler |
| 9 | §9 Açık sorular | mimari | mimari | VERIFICATION REQUIRED |
| 10 | §A–§M Ek tablolar (API kataloğu, yaşam döngüsü, state matrix, kapsama, etki) | mimari | kanit | audit |

---

## §1 Bileşen Haritası (katman bazlı)

| Katman | Bileşen grubu | Kök | Dosya | Rol |
|---|---|---|---|---|
| Presentation | PageRouter | `shared/src/PageRouter/` | 15 | rota, kernel, emitter |
| Presentation | Middleware | `shared/src/Middleware/` | 12 | auth, csrf, headers, cache |
| Application | Events | `shared/src/Events/Domain` | 10 | domain olayları |
| Application | Events | `shared/src/Events/Integration` | 4 | entegrasyon olayları |
| Application | Api | `shared/src/Api/Dto` (7) · `Response` (6) · `Request` | 13+ | sözleşmeler |
| Domain | Session | `shared/src/Session/` | 7 | oturum |
| Domain | Cache | `shared/src/Cache/` | 7 | önbellek (bkz. `caching/`) |
| Infrastructure | Veritabanı | `.ai/.sql/mysql/` | 20 | şema |
| Presentation (client) | JS router/core | `assets.coremusic.net/js/` | 98 | rota, guard, bus |

**Toplam PHP (shared/src):** 165 dosya · **JS:** 98 · **SQL:** 20 · **Test:** 47.

---

## §2 PageRouter Bileşenleri (15 dosya)

| # | Bileşen | Dosya | Sorumluluk | Kanıt satırı |
|---|---|---|---|---|
| 1 | `PageRouterKernel` | `PageRouterKernel.php` (351) | uçtan uca akış | `handle` 81, `normalizeRequest` 252 |
| 2 | `RouteRegistry` | `RouteRegistry.php` | kayıt + wildcard | `resolve` 35, `matchesPattern` 72 |
| 3 | `MiddlewarePipeline` | `MiddlewarePipeline.php` | zincir | `pipe` 17, `run` 23, `buildPipeline` 29 |
| 4 | `ResponseEmitter` | `ResponseEmitter.php` | yanıt yazma | `emit` 16 |
| 5 | `SpaRoute` | `SpaRoute.php` (19) | SPA fallback | sınıf 1–19 |
| 6 | `RouteResult` | `RouteResult.php` | akış çıktısı | 1–50 (readonly props) |
| 7 | `Request` sarma | `PageRouter/Request.php` | istek nesnesi | kernel 96 |
| 8 | `Router` (client) | `js/router/Router.js` | istemci yönlendirme | `main.js:23` |
| 9 | `guards` (client) | `js/router/guards.js` (23) | rota gardı | `canAccess` 12, `requireRole` 18 |
| 10 | RouteModelBinding | test kanıtı `RouteModelBindingTest` (249) | rota→model | ⚠️ sınıf konumu §9 Q5 |
| 11 | `DeviceManager` | `shared/src/DeviceManager.php` (654) | cihaz bağlamı | en büyük PHP dosyası |
| 12 | `AIEngine` | `shared/src/AIEngine.php` (382) | akış içi AI | kapsam dışı → §8 |

**Sayım notu:** dizin 15 dosya; §2 tablosu 12 öncelikli bileşen listeler (testler §A'da).

---

## §3 Middleware Bileşenleri (12 dosya)

| # | Bileşen | Akıştaki yeri | Test (satır) | Kanıt |
|---|---|---|---|---|
| 1 | `AuthGuard` | rota öncesi | `AuthGuardTest` 242 | `check` 15, `checkPermission` 42 |
| 2 | `CsrfMiddleware` | POST öncesi | `CsrfMiddlewareTest` 342 | `handle` 20, `fail` 49 |
| 3 | `SecurityHeadersMiddleware` | her yanıt | `SecurityHeadersMiddlewareTest` 247 | başlık enjeksiyonu |
| 4 | `CorsMiddleware` | preflight | `CorsMiddlewareTest` 358, `CorsTest` 273 | header yazımı |
| 5 | `CacheMiddleware` | GET yanıtı | `CacheMiddlewareTest` 406 | önbellek — `caching/` |
| 6 | `DeviceMiddleware` | cihaz bağlamı | `DeviceMiddlewareTest` 274 | attribute ekleme |
| 7 | Pipeline düzenleyici | `MiddlewarePipeline` | `MiddlewarePipelineTest` 235 | sıralama |

**Sıra kuralı (pipeline):** `SecurityHeaders → Cors → Device → Cache → Csrf → Auth → çekirdek`
(sıra testleri: `MiddlewarePipelineTest`, `PageRouterKernelTest`).

---

## §4 İstemci (JS) Bileşenleri (98 dosya)

| # | Bileşen | Dosya | Rol | Kanıt |
|---|---|---|---|---|
| 1 | `Router` | `js/router/Router.js` | rota tablosu + match | `main.js:23` import |
| 2 | `guards` | `js/router/guards.js` (23) | gard fonksiyonları | 10–18 |
| 3 | `EventBus` | `js/core/EventBus.js` | pub/sub | 15/27/38 |
| 4 | `main` | `js/main.js` | montaj | 23–40 |
| 5 | sayfa modülleri | `js/` altındaki diğerleri | view render | envanter §A.2 |

**Kural:** istemci gardları sunucu middleware'inin yerine geçmez; ikisi bağımsız katmandır
(sunucu: `AuthGuard.php:15` · istemci: `guards.js:12`).

---

## §5 Olay ve Veri Bileşenleri

| Grup | Dizin | Dosya | Kullanım |
|---|---|---|---|
| Domain Events | `shared/src/Events/Domain` | 10 | `playlist.created` vb. |
| Integration Events | `shared/src/Events/Integration` | 4 | dış yayın |
| API DTO | `shared/src/Api/Dto` | 7 | istek/yanıt veri |
| API Response | `shared/src/Api/Response` | 6 | sarmalayıcı (`ApiResultTest` 210) |
| Session | `shared/src/Session/` | 7 | oturum, CSRF token kaynağı |
| Cache | `shared/src/Cache/` | 7 | get/set — `caching/` alt-modülü |
| DB şema | `.ai/.sql/mysql/` | 20 | `playlist` (§4.1 SQL) |

---

## §6 Bileşen ↔ Akış İz Düşümü

| Akış (kapsam MD) | Bileşenler (sırayla) |
|---|---|
| F1 Sayfa açılışı (SPA) | `Router` → `guards.canAccess` → `SpaRoute` → `PageRouterKernel` |
| F2 API GET | kernel → Cors → Cache → Auth → RouteRegistry → ResponseEmitter |
| F3 API POST | kernel → Csrf → Auth → çekirdek → emitter (403: `fail` 49) |
| F4 Oturum yok | AuthGuard `false` → login rota → guards redirect |
| F5 Olay yayını | çekirdek → Events/Domain → EventBus (client) → render |
| F6 Rol yükseltme | `checkPermission` (`AuthGuard.php:42`) → rol sorgusu (SQL §4.3) |

---

## §7 Bağımlılık Grafiği (yön: yukarı bakışmaz)

```text
assets.coremusic.net/js (Router, guards, EventBus)
        │  fetch + csrf_token
        ▼
PageRouterKernel ──► MiddlewarePipeline ──► [SecurityHeaders, Cors,
        │                                     Device, Cache, Csrf, Auth]
        ▼
RouteRegistry ──► RouteResult ──► ResponseEmitter ──► HTTP
        │
        ▼
Api/Dto · Session · Events ──► MySQL (playlist, session, user_role)
```

| Kenar | Yön | Kural |
|---|---|---|
| js → PHP | tek istek yönü | client bağımlılığı yok |
| kernel → middleware | sıralı | `pipe` sırası (`MiddlewarePipeline.php:17`) |
| kernel → registry | tek yön | `resolve` (`RouteRegistry.php:35`) |
| PHP → SQL | parametreli | `SELECT *` yasak |

---

## §8 Boşluk ve PLANNED Kayıtları

| Boşluk | Kanıt | Durum | Etkilenen dosya |
|---|---|---|---|
| `*UseCase` sınıfı yok | `shared/src/**` taraması: 0 isabet | PLANNED | tüm usecase-akislari |
| Ödeme kodu yok | ödeme sınıfı 0 | PLANNED | `odem-servisleri/` (oluşturulacak) |
| Cron kodu yok | cron sınıfı 0 | PLANNED | `zamanlamali-gorevler/` |
| JS testi yok | `shared/tests` = PHP | AÇIK | guards/EventBus kanıtı |
| RouteModelBinding sınıfı | yalnız test dosyası | ⚠️ | §9 Q5 |

---

## §9 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| Q1 | `RouteModelBinding` sınıfı hangi dizinde? | rota→model akışı doğrulanamıyor |
| Q2 | `AIEngine` (382 satır) hangi akışta çağrılıyor? | kapsam sınırı |
| Q3 | Events/Integration (4 dosya) hangi taşıyıcıya gidiyor? | outbox/queue PLANNED olabilir |
| Q4 | `DeviceManager` 654 satır — akış mı, servis mi? | katman sınıflandırması |
| Q5 | `cache` katmanı için K011 `caching/` ile sınır ne? | dosya tekrar riski |

---

## §A Ek — Test Dosyası Envanteri (47 · ilgili 15)

| # | Test dosyası | Satır | Eşleştiği bileşen |
|---|---|---|---|
| 1 | `PageRouterKernelTest.php` | 1032 | §2-1 |
| 2 | `RouteRegistryTest.php` | 428 | §2-2 |
| 3 | `RequestTest.php` | 410 | §2-7 |
| 4 | `CacheMiddlewareTest.php` | 406 | §3-5 |
| 5 | `ResponseEmitterTest.php` | 389 | §2-4 |
| 6 | `CorsMiddlewareTest.php` | 358 | §3-4 |
| 7 | `SpaRouteTest.php` | 344 | §2-5 |
| 8 | `CsrfMiddlewareTest.php` | 342 | §3-2 |
| 9 | `DeviceMiddlewareTest.php` | 274 | §3-6 |
| 10 | `CorsTest.php` | 273 | §3-4 |
| 11 | `RouteModelBindingTest.php` | 249 | §2-10 |
| 12 | `SecurityHeadersMiddlewareTest.php` | 247 | §3-3 |
| 13 | `AuthGuardTest.php` | 242 | §3-1 |
| 14 | `MiddlewarePipelineTest.php` | 235 | §3-7 |
| 15 | `ApiResultTest.php` | 210 | §5 Response |

## §B Ek — Resmi Kaynak (bu dosya havuzu · kesişim 0)

| P# | URL | HTTP | Destek |
|---|---|---|---|
| UCB-P1 | `https://www.php.net/manual/en/class.traversable.php` | 200 | §7 iterator kenarı |
| UCB-P2 | `https://developer.mozilla.org/en-US/docs/Web/API/Event/Event` | 200 | §4-3 EventBus olayı |
| UCB-P3 | `https://developer.mozilla.org/en-US/docs/Web/API/Window/fetch` | 200 | §7 fetch kenarı |
| UCB-P4 | `https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/async_function` | 200 | §4-4 akış asenkron |
| UCB-P5 | `https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise` | 200 | §4-4 asenkron akış |
| UCB-P6 | `https://developer.mozilla.org/en-US/docs/Web/API/Window/sessionStorage` | 200 | §6 F4 oturum durumu (istemci) |

## §C Ek — Envanter Sayım Yöntemi

```powershell
# Repo kökünden (§6.2 kuralı)
(Get-ChildItem shared/src -Recurse -Filter *.php).Count          # 165
(Get-ChildItem shared/src/PageRouter -Recurse -Filter *.php).Count # 15
(Get-ChildItem shared/src/Middleware -Recurse -Filter *.php).Count # 12
(Get-ChildItem shared/tests -Recurse -Filter *.php).Count          # 47
(Get-ChildItem assets.coremusic.net/js -Recurse -Filter *.js).Count # 98
(Get-ChildItem .ai/.sql/mysql -Recurse -Filter *.sql).Count         # 20
```

## §D Ek — Bileşen API Kataloğu (PHP · disk imzaları)

### §D.1 PageRouter

| Bileşen | Metot | Dosya | Satır | İmza (özet) |
|---|---|---|---|---|
| `RouteRegistry` | `register` | `RouteRegistry.php` | ~20 | `register(string $path, string $name, bool $protected = false): void` |
| `RouteRegistry` | `resolve` | `RouteRegistry.php` | 35 | `resolve(string $path): ?RouteResult` |
| `RouteRegistry` | `getProtectedRouteKeys` | `RouteRegistry.php` | 61 | `getProtectedRouteKeys(): array` |
| `RouteRegistry` | `matchesPattern` | `RouteRegistry.php` | 72 | `matchesPattern(string $pattern, string $path): bool` |
| `MiddlewarePipeline` | `pipe` | `MiddlewarePipeline.php` | 17 | `pipe(callable $middleware): self` |
| `MiddlewarePipeline` | `run` | `MiddlewarePipeline.php` | 23 | `run(Request $request, callable $core): ResponseInterface` |
| `MiddlewarePipeline` | `buildPipeline` | `MiddlewarePipeline.php` | 29 | `buildPipeline(Request $request, callable $core): callable` |
| `PageRouterKernel` | `handle` | `PageRouterKernel.php` | 81 | `handle(ServerRequestInterface $request): ResponseInterface` |
| `PageRouterKernel` | `normalizeRequest` | `PageRouterKernel.php` | 252 | `normalizeRequest(ServerRequestInterface $r): ServerRequestInterface` |
| `ResponseEmitter` | `emit` | `ResponseEmitter.php` | 16 | `emit(ResponseInterface $response): void` |

### §D.2 Middleware

| Bileşen | Metot | Dosya | Satır | İmza (özet) |
|---|---|---|---|---|
| `AuthGuard` | `check` | `AuthGuard.php` | 15 | `check(Request $request): bool` |
| `AuthGuard` | `checkPermission` | `AuthGuard.php` | 42 | `checkPermission(object $user, Request $r): bool` |
| `CsrfMiddleware` | `handle` | `CsrfMiddleware.php` | 20 | `handle(Request $r, callable $next): ResponseInterface` |
| `CsrfMiddleware` | `fail` | `CsrfMiddleware.php` | 49 | `fail(): ResponseInterface` (403) |

### §D.3 Kullanım örneği (kayıt → çözümleme → yanıt)

```php
// Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 35 (resolve çağrı yeri)
$registry->register('/settings', 'settings', protected: true);
$result = $registry->resolve($_SERVER['REQUEST_URI']);   // ?RouteResult
// Dosya: shared/src/Middleware/AuthGuard.php · Satır: 15
if ($protected && !$auth->check($request)) {
    $result = new RouteResult('login', 302);             // §6 F4
}
```

## §E Ek — JS Bileşen API Kataloğu

| Bileşen | Üyeler | Dosya | Satır |
|---|---|---|---|
| `guards` | `canAccess(route, session)` | `js/router/guards.js` | 12 |
| `guards` | `requireRole(role)` | `js/router/guards.js` | 18 |
| `guards` | `beforeEach` montajı | `js/router/guards.js` | 10–17 |
| `EventBus` | `subscribe(event, handler)` | `js/core/EventBus.js` | 15 |
| `EventBus` | `emit(event, payload)` | `js/core/EventBus.js` | 27 |
| `EventBus` | `off(event, handler)` | `js/core/EventBus.js` | 38 |
| `main` | import + `router.start()` | `js/main.js` | 23–40 |

**Envanter:** 98 JS dosyası; bu akışta doğrudan kullanılan 4 tanedir (§4).

## §F Ek — Veri Bileşenleri (SQL varlıklar)

| Varlık | Dosya | Rol | Akışta kullanımı |
|---|---|---|---|
| `playlist` | `.ai/.sql/mysql/coremusic_playlist.sql` 1–28 | kullanıcı listesi | F5/F6 okuma-yazma |
| `session` | aynı şema (akış sorgusu §4.2 SQL) | oturum | F4 kapısı |
| `user_role` | join §4.3 (SQL ornekler) | RBAC | F6 rol |

**Şema kuralları:** InnoDB · `utf8mb4_turkish_ci` · FK `ON DELETE CASCADE` · parametreli sorgu · `SELECT *` yasak.

## §G Ek — Bileşen Karar Bağlantıları (M-01…M-20)

| Karar ID | Karar | İlgili bileşen | Kaynak |
|---|---|---|---|
| M-01 | Roata + middleware tek pipeline | `MiddlewarePipeline` | `application-master.md` §B |
| M-02 | Server + client çift gard | `AuthGuard` + `guards.js` | §7 bu dosya |
| M-03 | SPA fallback tek noktada | `SpaRoute` | §2-5 |
| M-04 | Yanıt üretimi tek emitter | `ResponseEmitter` | §2-4 |
| M-05 | Event tabanlı akış (async) | `Events/*` + `EventBus` | §5, §6 F5 |

## §H Ek — Metrikler

| Metrik | Değer | Kanıt |
|---|---|---|
| PHP bileşen dosyası (shared/src) | 165 | §C sayım |
| PageRouter dosyası | 15 | §C |
| Middleware dosyası | 12 | §C |
| Test dosyası | 47 | §A |
| JS dosyası | 98 | §C |
| SQL dosyası | 20 | §C |
| Öncelikli bileşen (bu dosya) | 12 + 7 + 7 = 26 satır | §2–§5 |
| Boşluk kaydı | 5 | §8 |
| Açık soru | 5 | §9 |

## §I Ek — Bileşen Yaşam Döngüsü (akış boyunca)

| Evre | Ne olur | Bileşen | Kanıt |
|---|---|---|---|
| E1 İstek | istek gelir, normalize edilir | `PageRouterKernel::normalizeRequest` | `PageRouterKernel.php:252` |
| E2 Sarma | `Request` nesnesi | kernel + `Request` | kernel 96 |
| E3 Rota | eşleşme ara | `RouteRegistry::resolve` | `RouteRegistry.php:35` |
| E4 Zincir | middleware sırası | `MiddlewarePipeline::pipe/run` | 17/23 |
| E5 Kapı | oturum/rol/CSRF | `AuthGuard` + `CsrfMiddleware` | 15 / 20 |
| E6 Çekirdek | iş akışı çalışır | çekirdek callable | `run(..., $core)` |
| E7 Olay | domain olayı yayınlanır | `Events/Domain` + `EventBus` | §5 |
| E8 Yanıt | status/header/body | `ResponseEmitter::emit` | `ResponseEmitter.php:16` |
| E9 İstemci | guard + render | `guards.js` + `Router` | 10–18 / `main.js:23` |

## §J Ek — Bileşen Başına Sorumluluk Detayı

### §J.1 RouteRegistry
- Sorumluluk: rota tablosu saklama, birebir + wildcard eşleşme.
- Durum: `$routes` (satır 14), `$protected` (satır 16) — çalışma zamanı sabit.
- Değişiklik etkisi: tüm akışlar (F1–F6) — regresyon: `RouteRegistryTest` 428 satır.
- Risk: `matchesPattern` (72) geniş kalırsa yanlış rota — §G M-01 kararı korur.

### §J.2 MiddlewarePipeline
- Sorumluluk: sırayı koruyarak sarmalı çalışma.
- Durum: `$stack` (satır 12) — `pipe` sonrası, `run` sonrası.
- Değişiklik etkisi: F2/F3 — sıralama değişirse CSRF/oturum sırası bozulur (kritik).
- Risk: sırasız ekleme — her `pipe` çağrısı kod incelemesinde sıraya bakar.

### §J.3 AuthGuard
- Sorumluluk: oturum var mı + izin var mı (iki seviye).
- Durum: `check` false → 403/redirect dalı (§6 F4).
- Değişiklik etkisi: korumalı tüm rotalar (`getProtectedRouteKeys` 61).
- Risk: fail-open — `check` hiçbir yerde null'u true kabul etmez (§7 N2).

### §J.4 CsrfMiddleware
- Sorumluluk: yazma yöntemlerinde (POST/PUT/PATCH/DELETE) token doğrulama.
- Durum: `fail()` (49) → 403 gövdesi.
- Değişiklik etkisi: tüm form/AJAX POST — yanlışlıkla GET'e koymak kapıyı kırar.
- Risk: timing attack → `hash_equals` kullanımı (§2.3).

### §J.5 PageRouterKernel
- Sorumluluk: E1–E8 orkestrasyonu, 404/302 kararı.
- Durum: rota null → `RouteResult('not-found', 404)`.
- Değişiklik etkisi: tek dosya — tüm akışlar.
- Risk: 351 satırlık en büyük akış dosyası; `PageRouterKernelTest` 1032 satır kapsamalı.

### §J.6 ResponseEmitter
- Sorumluluk: tek çıkış noktası; header yazımı.
- Durum: yazıldıktan sonra çıktı kilitli (çift yazım yasak).
- Değişiklik etkisi: tüm yanıtlar.
- Risk: output öncesi whitespace → header hatası (test: `ResponseEmitterTest`).

### §J.7 guards.js + EventBus.js (istemci)
- Sorumluluk: rota gardı ve olay yayını.
- Durum: `window.__session` yoksa → login redirect (`guards.js` 10–17).
- Değişiklik etkisi: SPA navigasyonu.
- Risk: JS testi yok (§8 boşluk) — manuel browser test gerekli.

## §K Ek — Bileşen Durum Matrisi (State Matrix)

| Bileşen | DEFAULT | ERROR | LOADING | DISABLED |
|---|---|---|---|---|
| `RouteRegistry` | kayıt yok → 404 | — | — | — |
| `MiddlewarePipeline` | boş zincir çalışır | middleware fırlatırsa kernel 500 | — | — |
| `AuthGuard` | oturumsuz false | izin reddi (§7 N3) | — | — |
| `CsrfMiddleware` | GET geçer | token hatalı → 403 | — | — |
| `Router` (js) | public rota | guard reddi → login | rota yüklenirken boş | — |
| `EventBus` | handler yok → sessiz | handler hatası → konsol | — | — |
| `flow-btn` (CSS) | token arka plan | `data-state=error` | `data-state=loading` | `[disabled]` |

## §L Ek — Test ↔ Bileşen Kapsama Geniş Matrisi

| Bileşen | Test dosyası | Satır | Kapsama yorumu |
|---|---|---|---|
| `PageRouterKernel` | `PageRouterKernelTest.php` | 1032 | en derin kapsam |
| `RouteRegistry` | `RouteRegistryTest.php` | 428 | wildcard + koruma |
| `Request` | `RequestTest.php` | 410 | sarma |
| `ResponseEmitter` | `ResponseEmitterTest.php` | 389 | header/body |
| `CorsMiddleware` | `CorsMiddlewareTest.php` + `CorsTest.php` | 631 | iki dosya |
| `SpaRoute` | `SpaRouteTest.php` | 344 | fallback |
| `CsrfMiddleware` | `CsrfMiddlewareTest.php` | 342 | geçer/geçmez |
| `DeviceMiddleware` | `DeviceMiddlewareTest.php` | 274 | attribute |
| `SecurityHeaders` | `SecurityHeadersMiddlewareTest.php` | 247 | başlıklar |
| `AuthGuard` | `AuthGuardTest.php` | 242 | iki seviye |
| `MiddlewarePipeline` | `MiddlewarePipelineTest.php` | 235 | sıra |
| `ApiResult` | `ApiResultTest.php` | 210 | sarmalayıcı |
| `CacheMiddleware` | `CacheMiddlewareTest.php` | 406 | GET cache |
| `RouteModelBinding` | `RouteModelBindingTest.php` | 249 | ⚠️ sınıf konumu Q1 |
| `guards.js` | — | — | YOK (§8) |
| `EventBus.js` | — | — | YOK (§8) |

## §M Ek — Değişiklik Etki Analizi (dosya → kim etkilenir)

| Değişilen bileşen | Etkilenen akış | Etkilenen test | Öncelik |
|---|---|---|---|
| `RouteRegistry` | F1–F6 | `RouteRegistryTest` | kritik |
| `MiddlewarePipeline` | F2, F3 | `MiddlewarePipelineTest` | kritik |
| `AuthGuard` | F4, F6 | `AuthGuardTest` | kritik |
| `CsrfMiddleware` | F3 | `CsrfMiddlewareTest` | kritik |
| `PageRouterKernel` | hepsi | `PageRouterKernelTest` | kritik |
| `ResponseEmitter` | hepsi | `ResponseEmitterTest` | yüksek |
| `guards.js` | F1, F4 | yok → manuel | yüksek |
| `EventBus.js` | F5 | yok → manuel | orta |
| SQL şema | F5, F6 | DB testi yok | yüksek |

## §N Ek — 26 Öncelikli Bileşen · Sorumluluk Sözlüğü

| # | Bileşen | Tek cümle sorumluluk | Katman | Akış |
|---|---|---|---|---|
| 1 | `PageRouterKernel` | İsteği E1–E8 evrelerinde yürütür | Presentation | hepsi |
| 2 | `RouteRegistry` | Rota kaydını ve eşleşmeyi tutar | Presentation | F1–F3 |
| 3 | `MiddlewarePipeline` | Middleware sırasını korur | Presentation | F2, F3 |
| 4 | `ResponseEmitter` | Tek HTTP çıkış noktasıdır | Presentation | hepsi |
| 5 | `SpaRoute` | Olmayan GET'yi index.html'e taşır | Presentation | F1 |
| 6 | `RouteResult` | Akışın taşıdığı değer nesnesi | Presentation | hepsi |
| 7 | `Request` sarma | PSR-7 isteği uygulama nesnesine çevirir | Presentation | hepsi |
| 8 | `AuthGuard` | Oturum + izin kapısıdır | Presentation | F4, F6 |
| 9 | `CsrfMiddleware` | Yazma isteklerinde token doğrular | Presentation | F3 |
| 10 | `SecurityHeaders` | Güvenlik başlıklarını ekler | Presentation | hepsi |
| 11 | `CorsMiddleware` | preflight ve origin politikasını uygular | Presentation | F2, F3 |
| 12 | `CacheMiddleware` | GET yanıtlarını önbellerer | Presentation | F2 |
| 13 | `DeviceMiddleware` | Cihaz bağlamını attribute'e yazar | Presentation | hepsi |
| 14 | `Session` (7 dosya) | Oturum ve CSRF token kaynağıdır | Domain | F4 |
| 15 | `Events/Domain` (10) | İş olaylarını tanımlar | Application | F5 |
| 16 | `Events/Integration` (4) | Dış yayın olaylarını tanımlar | Application | F5 |
| 17 | `Api/Dto` (7) | Sözleşme verileri | Application | F2, F3 |
| 18 | `Api/Response` (6) | Sarmalayıcı yanıt üretir | Application | F2, F3 |
| 19 | `Cache` (7) | Önbellek get/set | Infrastructure | F2 |
| 20 | `Router.js` | İstemci rota tablosu | Presentation(client) | F1 |
| 21 | `guards.js` | İstemci gardı | Presentation(client) | F1, F4 |
| 22 | `EventBus.js` | Olay yayını | Presentation(client) | F5 |
| 23 | `main.js` | Montaj ve başlangıç | Presentation(client) | F1 |
| 24 | `playlist` SQL | Şema kanıtı | Infrastructure | F5, F6 |
| 25 | `DeviceManager` (654) | Cihaz servisi (akış dışı) | Domain | §9 Q4 |
| 26 | `AIEngine` (382) | AI servisi (akış dışı) | Domain | §9 Q2 |

## §O Ek — Komşu Alt-Modül Sınırları (çakışma önleme)

| Komşu modül | Bu modülle sınır | Kural |
|---|---|---|
| `routing/` (529 satır, mevcut tek dosya) | rota tanımı detayı orada, akış burada | rota şeması tekrar yazılmaz |
| `caching/` (513 satır) | `CacheMiddleware` akışta, politika orada | kural orada, entegrasyon burada |
| `dto/` (525 satır) | `Api/Dto` sözleşmeleri orada | DTO içeriği tekrar edilmez |
| `usecase/` (509 satır) | use case sınıfları orada (PLANNED) | sınıf kataloğu orada |
| `application-testing/` (index 41) | test stratejisi orada | envanter burada kanıt olarak |
| `commands/`, `queries/` | CQRS ayrımı orada | okuma/yazma sınıfları orada |

**Çakışma denetimi:** bu dosya yalnızca akış-bileşen iz düşümünü üretir; şema/p politika/
sınıf kataloğu komşularda (bkz. §O).

## §P Ek — Bileşen Sınıf Sırası ve Hasar Yüzeyi

| Değişiklik | Hızlı hasar | Belirtileri | Kapsayan test |
|---|---|---|---|
| `matchesPattern` regex'e çevrilirse | kritik | yanlış rota 200 | `RouteRegistryTest` 428 |
| `pipe` sırası değişirse | kritik | CSRF auth'tan sonra çalışır | `MiddlewarePipelineTest` 235 |
| `fail()` 403 yerine 404 yaparsa | yüksek | yanlış durum kodu | `CsrfMiddlewareTest` 342 |
| `emit` çift header yazar | yüksek | header bounce warning | `ResponseEmitterTest` 389 |
| `guards.js` public kontrolü kalkarsa | yüksek | login döngüsü | manuel (test yok §8) |
| `canAccess` null session hata fırlatırsa | orta | SPA çöker | manuel (test yok §8) |
| SQL FK cascade kaldırılırsa | orta | yetim playlist satırı | DB testi yok §8 |
| `EventBus.off` kırılırsa | düşük | dinleyici sızıntısı | manuel |

**Ek kayıt satırları (tam liste):**

| Değişiklik | Hızlı hasar | Belirtileri | Kapsayan test |
|---|---|---|---|
| `normalizeRequest` path'i bozarsa | kritik | her rota 404 | `PageRouterKernelTest` 1032 |
| `RouteResult` status varsayılanı değişirse | yüksek | yanlış 2xx/3xx | `ApiResultTest` 210 |
| `SecurityHeaders` başlığı kalkarsa | yüksek | güvenlik denetimi başarısız | `SecurityHeadersMiddlewareTest` 247 |
| `CorsMiddleware` origin genişlerse | yüksek | yetkisiz kaynak | `CorsMiddlewareTest` 358 |
| `DeviceMiddleware` attribute kalkarsa | orta | cihaz bağlamı kaybolur | `DeviceMiddlewareTest` 274 |
| `CacheMiddleware` yanlış TTL | orta | bayat veri | `CacheMiddlewareTest` 406 |
| `SpaRoute` index yolu değişirse | orta | SPA açılmaz | `SpaRouteTest` 344 |
| `session` tablosu FK değişirse | orta | login kırılır | DB testi yok §8 |

## §Q Ek — Bileşen Kayıt Şablonu (yeni bileşen eklenince)

| Alan | Zorunlu | Örnek |
|---|---|---|
| Dosya yolu | ✔ | `shared/src/PageRouter/RouteRegistry.php` |
| Katman | ✔ | Presentation |
| Akış etkisi | ✔ | F1–F6 |
| Test eşleşmesi | ✔ (yoksa §8 boşluk) | `RouteRegistryTest` |
| Sahip karar | ✔ | M-01 |
| Durum | ✔ | IMPLEMENTED / PLANNED |

**Kural:** yeni bileşen bu şablonsuz §N listesine eklenmez; önce kanıt, sonra kayıt.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
