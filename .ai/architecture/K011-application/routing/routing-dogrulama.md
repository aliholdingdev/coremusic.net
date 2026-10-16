---
title: "Routing — Doğrulama (İddia ↔ Dosya ↔ Satır)"
type: dogrulama
kodeme: kademe-2
module: K011-application
parent: routing
status: active
updated: 2026-10-15
group: kanit
---

# Routing — Doğrulama Raporu

> **Amaç:** Routing modülünün (API RouteTable + Gateway + SPA PageRouter + client-side
> router) iddialarını gerçek repo dosyaları ve satır numaralarıyla doğrulamak; test
> envanterini listelemek; PASS/BLOCK kapısını kanıtla vermek. Uydurma satır yoktur —
> her satır referansı bu oturumda okunmuş dosyalardan alınmıştır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | §1 Kapsam ve iddia envanteri | kanit | kapsam | sınır |
| 2 | §2 İddia ↔ Dosya ↔ Satır matrisi | kanit | dogrulama | ana kanıt |
| 3 | §3 İddia detay blokları | kod | bilesenler | kod + yorum |
| 4 | §4 Test envanteri | kanit | dogrulama | test dosyaları |
| 5 | §5 Test davranış tablosu | kanit | dogrulama | ne doğruluyor |
| 6 | §6 Rota envanteri (routes.php) | mimari | kapsam | uç envanteri |
| 7 | §7 Normalizasyon kenar durumları | kanit | dogrulama | edge case |
| 8 | §8 Allow başlığı semantiği | kanit | dogrulama | 405 sözleşmesi |
| 9 | §9 Kapı: PASS/BLOCK | kanit | dogrulama | gate |
| 10 | §10 KPI | kanit | dogrulama | ölçüm |
| 11 | §11 Olumsuz senaryolar (N1–N16) | kanit | dogrulama | negatif test |
| 12 | §12 Havuz URL ve reddedilenler | kanit | dogrulama | URL kapısı |
| 13 | §13 PowerShell envanter bloğu | kanit | dogrulama | tekrar ölçüm |
| 14 | §14 Açık sorular ve ⚠️ işaretleri | kanit | kapsam | boşluk |

---

## §1 Kapsam ve iddia envanteri

**Kapsam içi (bu dosyanın doğruladığı):**

| Katman | Dosya (repo) | Rol |
|--------|--------------|-----|
| API routing çekirdeği | `shared/src/Api/Routing/RouteTable.php` (253 satır) | method-aware rota eşleştirme |
| API routing girişi | `shared/src/Api/Gateway.php` (168 satır) | pipeline dışı rota kararı |
| API middleware zinciri | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` (46 satır) | reverse-order pipeline |
| API yanıt normalizasyonu | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` (97 satır) | ETag + Cache-Control |
| Rota tanımı | `api.coremusic.net/config/routes.php` (120 satır) | GET 11 / POST 6 kayıt |
| SPA page router | `shared/src/PageRouter/RouteRegistry.php` (83 satır) | `{param}` pattern resolve |
| Client-side router | `assets.coremusic.net/js/router/Router.js` (81 satır) | pushState guard + fallback |
| Client URL yardımcıları | `assets.coremusic.net/js/router/UrlUtils.js` (39 satır) | origin/query normalize |

**Kapsam dışı (bu dosya doğrulamaz → ilgili klasöre devredilir):**

| Konu | Neden | Gideceği yer |
|------|-------|--------------|
| Cache TTL/ETag KPI'ları (detay) | caching klasörünün konusu | `caching/` |
| DTO'dan response üretimi | dto klasörünün konusu | `dto/` |
| Event dispatch (auth login sonrası) | usecase klasörünün konusu | `usecase/` |

**İddia envanteri:** 24 iddia (A1–A24). Tümü bu oturumda okunmuş dosyalara dayanır;
hiçbiri model hafızasından yazılmamıştır.

---

## §2 İddia ↔ Dosya ↔ Satır matrisi

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| A1 | RouteTable method-aware'tir; 405 ile 404'ü ayırır | `shared/src/Api/Routing/RouteTable.php` | 111-138 (match) · 28-30 (STATUS sabitleri) | ✅ |
| A2 | Normalize: `?` sonrası strip + trailing-slash `rtrim` | `shared/src/Api/Routing/RouteTable.php` | 242-252 (normalize) | ✅ |
| A3 | Eşleşme sırası: exact → isDeclared kontrolü → segment-sınırlı prefix | `shared/src/Api/Routing/RouteTable.php` | 197-221 (find) · 229-240 (isDeclared) | ✅ |
| A4 | `implemented=false` tanımlı ama controller'sız uç → 405 + Allow | `api.coremusic.net/config/routes.php` | 12-13, 36-39 | ✅ |
| A5 | Gateway rota kararını middleware pipeline'ı DIŞINDA verir | `shared/src/Api/Gateway.php` | 59-65 | ✅ |
| A6 | 404/405 yanıtı Gateway'de üretilir | `shared/src/Api/Gateway.php` | 125-138 (methodNotAllowedResponse) · 78-80 (NOT_FOUND) | ✅ |
| A7 | Pipeline `array_reverse` ile ters sıraya sarılır | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | 37-42 | ✅ |
| A8 | ETag yalnız `cacheable` route'da basılır | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 41-44 | ✅ |
| A9 | ETag = `md5(json_encode(response))`; eşleşirse 304 + `halt` + boş gövde | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 68-83 | ✅ |
| A10 | `no-store` yolunda `Pragma: no-cache` da basılır | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 88-96 | ✅ |
| A11 | 304 yolu `exit` kullanmaz (index.php akışı + CORS korunur) | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 19-20, 77-79 | ✅ |
| A12 | GET prefix kayıtları 6 servis tanır (auth/user/music/playlist/media/download) | `api.coremusic.net/config/routes.php` | 27-32 | ✅ |
| A13 | Faz 1b: 6 POST ucu `implemented=true` + `action` + `validation` | `api.coremusic.net/config/routes.php` | 51-118 | ✅ |
| A14 | `GET /api/v1/auth/me` tek implemented GET uçtur | `api.coremusic.net/config/routes.php` | 42-48 | ✅ |
| A15 | PageRouter `register` path'i `trim('/', ...)` ile normalize eder | `shared/src/PageRouter/RouteRegistry.php` | 10-13 | ✅ |
| A16 | `resolve`: boş URI → `home`; exact → pattern sırası | `shared/src/PageRouter/RouteRegistry.php` | 35-54 | ✅ |
| A17 | `{param}` → `[^/]+` regex dönüşümü | `shared/src/PageRouter/RouteRegistry.php` | 72-82 | ✅ |
| A18 | `loadFromFile` array olmayan config'de RuntimeException fırlatır | `shared/src/PageRouter/RouteRegistry.php` | 15-33 | ✅ |
| A19 | SPA router `history.pushState` fonksiyonel değilse hiç başlamaz | `assets.coremusic.net/js/router/Router.js` | 49-51 | ✅ |
| A20 | `auth_key=` sorgusu varsa query strip yapılmaz (callback korunur) | `assets.coremusic.net/js/router/Router.js` | 61-62 | ✅ |
| A21 | `init()` query'yi `replaceState` ile temizler, sonra `initUrl(pathname)` | `assets.coremusic.net/js/router/Router.js` | 62-63 | ✅ |
| A22 | `normalizeUrl`: cross-origin → tam `href`; same-origin → `pathname + search` | `assets.coremusic.net/js/router/UrlUtils.js` | 8-21 | ✅ |
| A23 | Parse başarısızsa hash düşer, query korunur | `assets.coremusic.net/js/router/UrlUtils.js` | 16-20 | ✅ |
| A24 | `normalizeCacheKey`: hash sil + çiftleri `localeCompare` ile sırala | `assets.coremusic.net/js/router/UrlUtils.js` | 31-39 | ✅ |

**Sayım:** 24/24 ✅ · uydurma/eksik kanıt: 0 · `⚠️ VERIFICATION REQUIRED`: 0 (bu dosyada).

---

## §3 İddia detay blokları

### A1 + A2 + A3 — RouteTable çekirdeği

```text
Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 242-252
-- private static function normalize(string $path): string
--   $queryPos = strpos($path, '?');
--   if ($queryPos !== false) { $path = substr($path, 0, $queryPos); }
--   $path = rtrim($path, '/');
--   return $path === '' ? '/' : $path;
```

```text
Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 197-221 (find)
-- 1) $this->routes[$method] içinde exact ($entry['path'] === $uri) arama
-- 2) isDeclared($uri) false ise → null (prefix fallback YOK → 404)
-- 3) segment-sınırlı prefix: str_starts_with($uri, $entry['path'] . '/')
```

```text
Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 111-138 (match)
-- STATUS_OK (implemented=true) / STATUS_METHOD_NOT_ALLOWED (implemented=false
--   veya allow dolu) / STATUS_NOT_FOUND kararı; 405'te route hâlâ döner,
--   404'te route null + allow [].
```

**Yorum:** A2 + A3 birlikte, `/api/v1/auth/login?next=/home` ve `/api/v1/auth/login/`
girişlerinin aynı exact kayıtla eşleşmesini garantiler — bu davranış test ile sabitlenmiş
(A16 test davranış tablosu §5, `testQueryStringAndSlashAreNormalized`).

### A4 — implemented=false sözleşmesi

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 12-13
-- `implemented` : false → route TANIMLI ama controller yok → 405 + Allow
--                 (404 DEĞİL); Faz 1b'de 6 POST ucu true yapıldı
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 36-39
-- GET /api/v1/auth/{login,register,forgot-password,reset-password}
--   => ['public' => true, 'implemented' => false]
```

**Yorum:** Eski davranışta bu uçlara GET gelince 404 dönüyordu; yeni sözleşmede
rota tanımlı olduğu için 405 + `Allow: POST, OPTIONS` döner (bilgi sızdırmaz, doğru
durumu söyler).

### A5 + A6 — Gateway

```text
Dosya: shared/src/Api/Gateway.php · Satır: 59-65
-- Route eşleşmesi pipeline'ın DIŞINDA yapılır ki `_route` (ve 405/404
-- durumu) Authentication / Authorization / RequestValidation /
-- ResponseNormalization middleware'lerine ulaşsın (Faz 2 notu #2).
```

```text
Dosya: shared/src/Api/Gateway.php · Satır: 74-80, 125-138
-- STATUS_METHOD_NOT_ALLOWED → methodNotAllowedResponse($allow):
--   ApiResponse::error('METHOD_NOT_ALLOWED', ..., 405, ['allow' => $header])
--   + $response['headers'] = ['Allow' => $header];
-- STATUS != OK → ApiResponse::error('NOT_FOUND', 'Route not found', 404)
```

### A7 — Pipeline ters sarma

```text
Dosya: shared/src/Api/Middleware/ApiMiddlewarePipeline.php · Satır: 37-42
-- foreach (array_reverse($this->middlewares) as $middleware)
--   $pipeline = fn(array $request) => $middleware($request, $pipeline);
```

**Yorum:** `pipe()` sırası okuma sırasıdır; çalıştırma sırası tersidir (klasik onion).
Her middleware `$next` olarak sonraki katmanı çağırır; `halt` bayrağı zinciri erken
keser (bkz. A9).

### A8 + A9 + A10 + A11 — ResponseNormalization

```text
Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 41-44
-- if ($cacheable && isset($response['data']) && !empty($response['data']))
--     $response = $this->withETag($response);
```

```text
Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 68-83
-- $etag = md5((string) json_encode($response));
-- header('ETag: "' . $etag . '"');
-- $ifNoneMatch === '"'.$etag.'"' → http_response_code(304);
--   $response['httpStatus'] = 304; $response['halt'] = true; $response['body'] = '';
```

```text
Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 88-96
-- cacheable && cacheTtl > 0 → "Cache-Control: public, max-age={$cacheTtl}"
-- aksi    → "Cache-Control: no-store, no-cache, must-revalidate" + "Pragma: no-cache"
```

```text
Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 19-20
-- 304 yolu `exit` ile index.php'yi bypass etmiyor; `halt` + boş gövde döner,
--   header kopyalama/index.php akışı korunur.
```

### A12 + A13 + A14 — routes.php envanteri

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 27-32
-- GET prefix: /api/v1/{auth,user,music,playlist,media,download}
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 53-118
-- POST /api/v1/auth/{login,register,forgot-password,reset-password,set-gender,logout}
--   her birinde 'action' + 'implemented' => true; ilk 4'ünde 'validation' seti
```

```text
Dosya: api.coremusic.net/config/routes.php · Satır: 42-48
-- GET /api/v1/auth/me => ['action' => 'me', 'public' => false, 'implemented' => true]
```

### A15 + A16 + A17 + A18 — PageRouter RouteRegistry

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 10-13
-- $this->routes[trim($route->path, '/')] = $route;
```

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 35-54
-- $normalized = trim($uri, '/'); boş → $this->routes['home'] ?? null;
-- exact isset kontrolü → desen döngüsü (matchesPattern) → null
```

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 72-82
-- '{' içermeyen pattern → false
-- preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '[^/]+', $pattern)
-- '#^'.$regex.'$#' ile preg_match
```

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 15-33
-- is_file değilse RuntimeException; include array değilse RuntimeException;
--   SpaRoute olmayan satırlar sessizce atlanır (continue)
```

### A19 + A20 + A21 — SPA Router.js

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 49-51
-- init(): if (window.CoreMusic?.RouterConfig?.enabled === false) return;
--         if (typeof history.pushState !== 'function') return;
```

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 61-63
-- if (window.location.search?.includes('auth_key=')) return;
-- if (window.location.search) history.replaceState(null, '', window.location.pathname);
-- this.#nav.initUrl(window.location.pathname);
```

**Yorum:** A20, OAuth callback'inde `auth_key` taşıyan URL'nin query'sinin silinmesini
engeller (A21 sadece auth_key YOKSA çalışır). A19, eski tarayıcıda SPA'nın hiç
başlamaması yerine normal link navigasyonuna bırakması demektir — güvenli fallback.

### A22 + A23 — UrlUtils normalizeUrl

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 8-15
-- const u = new URL(url, base || window.location.href);
-- if (u.origin !== window.location.origin) return u.href;
-- // C-F-14: query KORUNUR — rota ANAHTARI pathname'dir
-- return u.pathname + u.search;
```

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 16-20
-- catch: hash düşer, query korunur — hIdx !== -1 ? substring(0, hIdx) : url
```

### A24 — normalizeCacheKey

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 31-39
-- u.hash = '';
-- const params = [...u.searchParams.entries()].sort(([a], [b]) => a.localeCompare(b));
-- u.search = params.map(([k, v]) => `${k}=${v}`).join('&');
-- return u.pathname + u.search;  // catch → url (ham)
```

**Yorum:** `?b=2&a=1` ve `?a=1&b=2` aynı cache anahtarına iner — cache-dostu determinizm
(caching klasörünün cache-key bölümü ile kesişir; bu dosyada yalnız routing perspektifi
raporlanır).

---

## §4 Test envanteri

Routing ile doğrudan ilgili test dosyaları (bu oturumda glob ile bulundu + okundu):

| # | Test dosyası | Satır | Okundu mu | Ne kapsıyor |
|---|--------------|-------|-----------|-------------|
| T1 | `shared/tests/Api/RouteTableTest.php` | 166 | ✅ tamamı | 405/404 ayrımı, normalize, prefix, `all()`, `defaults()` |
| T2 | `shared/tests/Unit/PageRouter/RouteRegistryTest.php` | ⚠️ okunmadı | ❌ | PageRouter registry (dosya adı kanıtı) |
| T3 | `shared/tests/Unit/PageRouter/SpaRouteTest.php` | ⚠️ okunmadı | ❌ | SpaRoute modeli (dosya adı kanıtı) |
| T4 | `shared/tests/Unit/Device/NavRouteConsistencyTest.php` | ⚠️ okunmadı | ❌ | nav ↔ route tutarlılığı (dosya adı kanıtı) |
| T5 | `api.coremusic.net/tests/RouteConfigTest.php` | ⚠️ okunmadı | ❌ | routes.php sözleşmesi (dosya adı kanıtı; T1 docblock'unda tetikleyici olarak anılıyor — satır 99-101) |

**Not (zero-hallucination):** T2–T5 satır sayıları ve test metotları okunmadığı için
`⚠️ VERIFICATION REQUIRED` işaretlidir; dosya adları glob çıktısıyla kanıtlıdır.
T1'in 11 test metodu §5'te davranış bazında raporlanır.

### T1 test metot envanteri (RouteTableTest — okundu)

| # | Test metodu | Satır | Doğruladığı iddia |
|---|-------------|-------|-------------------|
| 1 | `testGetExactRouteMatchesAsOk` | 38-46 | A1 (STATUS_OK + public korunur + allow []) |
| 2 | `testExactMatchBeatsPrefixMatch` | 48-54 | A2 + A3 (trailing slash → exact) |
| 3 | `testPrefixMatchStillWorksForDeclaredResource` | 56-65 | A3 (declared prefix fallback) |
| 4 | `testPostLoginDeclaredButNotImplementedYields405WithAllow` | 67-76 | A1 + A4 (405 + Allow: GET, OPTIONS; POST yok) |
| 5 | `testUnknownMethodOnKnownPathYields405NotNullRoute` | 78-85 | A1 (PUT → 405 + route null + Allow: GET) |
| 6 | `testUnknownPathYields404Not405` | 87-96 | A1 (bilinmeyen yol 4 methodta da 404 + allow []) |
| 7 | `testUnknownSubPathUnderDeclaredPrefixYields404` | 103-119 | A3 (prefix altı tanımsız yol → 404, fallback YOK) |
| 8 | `testPostOnResourceWithoutPostRouteYields405` | 121-128 | A1 (GET-only kaynakta POST → 405) |
| 9 | `testDefaultsPreserveLegacyGetPrefixBehaviour` | 130-141 | A12 (`defaults()` 6 servis GET + auth POST 405) |
| 10 | `testAllExposesMethodAwareRegistry` | 143-157 | A13 (`all()` method-aware satır üretir; implemented=false korunur) |
| 11 | `testQueryStringAndSlashAreNormalized` | 159-165 | A2 (`?next=/home` + lowercase method normalize) |

**Docblock kanıtı (test dosyasının kendi ağzından sözleşme):**

```text
Dosya: shared/tests/Api/RouteTableTest.php · Satır: 15-18
-- "alt kaynak" yalnızca TABLODA açıkça kayıtla vardır.
--   - /api/v1/auth/profile POST kaydıyla → GET prefix fallback çalışır
--   - /api/v1/auth/settings GET kaydıyla  → POST 405 + Allow: GET
--   - hiç kaydı olmayan alt yol → 404
```

```text
Dosya: shared/tests/Api/RouteTableTest.php · Satır: 98-102
-- "Prefix + tanımsız alt yol → 404 (RouteConfigTest tetikleyicisi: eski davranışta
--   prefix fallback ile private route'a düşüp 401 dönüyordu)."
```

---

## §5 Test davranış tablosu (kod → beklenti)

| Davranış | Test | Giriş | Beklenen | Kanıt satırı |
|----------|------|-------|----------|--------------|
| Exact eşleşme | T1-1 | `GET /api/v1/auth/login` | STATUS_OK, public=true | RouteTableTest:38-46 |
| Trailing slash | T1-2 | `GET /api/v1/auth/login/` | STATUS_OK, path `/api/v1/auth/login` | RouteTableTest:48-54 |
| Query strip | T1-11 | `GET /api/v1/auth/login?next=/home` | STATUS_OK, method `GET` (büyük harfe) | RouteTableTest:159-165 |
| Declared prefix | T1-3 | `GET /api/v1/auth/profile` | STATUS_OK, path `/api/v1/auth` | RouteTableTest:56-65 |
| Prefix altı belirsiz | T1-7 | `*/api/v1/auth/does-not-exist` | STATUS_NOT_FOUND, route null | RouteTableTest:103-119 |
| implemented=false | T1-4 | `POST /api/v1/auth/login` (test tablosunda) | 405 + Allow{GET,OPTIONS} − POST | RouteTableTest:67-76 |
| Bilinmeyen method | T1-5 | `PUT /api/v1/auth/login` | 405 + Allow{GET} + route null | RouteTableTest:78-85 |
| Bilinmeyen path | T1-6 | 4 method × `/api/v1/does-not-exist` | 404 + allow [] | RouteTableTest:87-96 |
| GET-only kaynak | T1-8 | `POST /api/v1/auth/settings` | 405 + Allow{GET} | RouteTableTest:121-128 |
| Legacy prefix | T1-9 | 6 servis × GET | STATUS_OK + service eşleşir | RouteTableTest:130-141 |
| Method-aware `all()` | T1-10 | `all()` | GET+POST satırları; implemented=false | RouteTableTest:143-157 |

---

## §6 Rota envanteri (routes.php — üretim tablosu)

`api.coremusic.net/config/routes.php` bu oturumda satır satır okundu; sayımlar dosyanın
kendisinden:

| Method | Path | public | implemented | action | validation | Satır |
|--------|------|--------|-------------|--------|------------|-------|
| GET | `/api/v1/auth` | — | — | — | — | 27 |
| GET | `/api/v1/user` | — | — | — | — | 28 |
| GET | `/api/v1/music` | — | — | — | — | 29 |
| GET | `/api/v1/playlist` | — | — | — | — | 30 |
| GET | `/api/v1/media` | — | — | — | — | 31 |
| GET | `/api/v1/download` | — | — | — | — | 32 |
| GET | `/api/v1/auth/login` | true | **false** | — | — | 36 |
| GET | `/api/v1/auth/register` | true | **false** | — | — | 37 |
| GET | `/api/v1/auth/forgot-password` | true | **false** | — | — | 38 |
| GET | `/api/v1/auth/reset-password` | true | **false** | — | — | 39 |
| GET | `/api/v1/auth/me` | false | **true** | me | — | 42-48 |
| POST | `/api/v1/auth/login` | true | true | login | email/password/gender | 53-65 |
| POST | `/api/v1/auth/register` | true | true | register | username/email/password/gender/agree_terms | 66-79 |
| POST | `/api/v1/auth/forgot-password` | true | true | forgotPassword | email | 80-89 |
| POST | `/api/v1/auth/reset-password` | true | true | resetPassword | token/password | 90-100 |
| POST | `/api/v1/auth/set-gender` | true | true | setGender | gender ∈ {male,female,neutral} | 101-111 |
| POST | `/api/v1/auth/logout` | false | true | logout | — | 112-118 |

**Sayım:** GET 11 · POST 6 · implemented=true 7 (me + 6 POST) · implemented=false 4.
Dosya başlık docblock'u bu sayımı destekler (satır 13: "Faz 1b'de 6 POST ucu true
yapıldı"; satır 22: "yalnızca GET /v1/auth/me 200/401").

---

## §7 Normalizasyon kenar durumları

| # | Giriş | Normalize sonucu (beklenen) | Kaynak |
|---|-------|------------------------------|--------|
| E1 | `/api/v1/auth/login?next=/home` | `/api/v1/auth/login` (strpos+substr `?` strip) | RouteTable.php:244-247 · Test:159-165 |
| E2 | `/api/v1/auth/login/` | `/api/v1/auth/login` (rtrim `/`) | RouteTable.php:249 · Test:48-54 |
| E3 | `//api//v1//auth` | normalize `//` KIRMAZ (yalnız `?` + trailing `/`) → `//api//v1//auth` kalır → 404 | RouteTable.php:242-252 (davranış: str_replace YOK) |
| E4 | `/` veya `` (boş) | normalize → `/` (boş koruması, RouteTable) | RouteTable.php:251 |
| E4b | `/` veya `` (boş URI, PageRouter) | `home` anahtarı | RouteRegistry.php:37-41 |
| E5 | `/music/track/42` + desen `music/track/{id}` | regex `#^music/track/[^/]+$#` eşleşir | RouteRegistry.php:72-82 |
| E6 | `%2F` encode slash | ⚠️ VERIFICATION REQUIRED — decode davranışı RouteTable.php'de okunmadı | — |
| E7 | Büyük/küçük method (`get`) | test `match('...','get')` STATUS_OK bekler → method case-insensitive | RouteTableTest.php:161 |
| E8 | `#hash` (client) | `normalizeUrl` hash'i düşürür, query korunur | UrlUtils.js:16-20 |

**E6 notu:** percent-decode davranışı dosyada okunmadı; uydurulmadı → `⚠️ VERIFICATION REQUIRED`.

---

## §8 Allow başlığı semantiği (405 sözleşmesi)

| Senaryo | Durum | Allow içeriği | route | Kanıt |
|---------|-------|---------------|-------|-------|
| POST → implemented=false GET kaydı olan uç | 405 | `GET, OPTIONS` (POST yok) | döner (tanımlı) | routes.php:12-13 · RouteTableTest:67-76 |
| PUT → bilinen path | 405 | `GET` | null | RouteTableTest:78-85 |
| POST → GET-only kaynak | 405 | `GET` | döner | RouteTableTest:121-128 |
| Herhangi method → bilinmeyen path | 404 | `[]` (Allow yok) | null | RouteTableTest:87-96 |
| Prefix altı tanımsız alt yol | 404 | `[]` | null | RouteTableTest:103-119 |

**Sözleşme özeti:** 405 = "yol var, method yok" (Allow ile söylenir); 404 = "yol yok"
(Allow basılmaz, bilgi sızdırmaz). Bu ayrım ADR-084 Faz 1a'nın temel iddiasıdır
(dosya docblock'u: `shared/tests/Api/RouteTableTest.php:9`).

---

## §9 Kapı: PASS/BLOCK

| Kapı | Kriter | Ölçüm | Sonuç |
|------|--------|-------|-------|
| K1 | ≥8 doğrulama satırı | 24 iddia (A1–A24) | ✅ PASS |
| K2 | Her iddia dosya+satır kanıtlı | 24/24 §2 tablosunda | ✅ PASS |
| K3 | Uydurma kod/satır yok | tüm bloklar okunan dosyalardan | ✅ PASS |
| K4 | Test envanteri + davranış | §4 + §5 (T1: 11 metot) | ✅ PASS |
| K5 | Olumsuz senaryo seti | §11 (N1–N16) | ✅ PASS |
| K6 | KPI raporu | §10 | ✅ PASS |
| K7 | URL kapısı (tek URL, kendi dosyasında) | §12 (History) | ✅ PASS |
| K8 | Okunmayan test dosyaları işaretli | T2–T5 ⚠️ | ✅ PASS (işaretli eksik) |

**GENEL KAPı: PASS** (tek eksikler işaretli: T2–T5 okunmadı, E6 decode).

---

## §10 KPI

| KPI | Değer | Kaynak |
|-----|-------|--------|
| routes.php toplam uç | 17 (GET 11 + POST 6) | routes.php:24-120 |
| implemented oranı | 7/17 true · 4/17 false (GET tarafı) | routes.php:36-48 |
| RouteTable test metodu | 11 | RouteTableTest.php:38-165 |
| RouteTable test geçiş iddiası | ⚠️ VERIFICATION REQUIRED (test bu oturumda ÇALIŞTIRILMADI — shell reddi) | — |
| Routing kaynak dosyası (bu modül) | 8 dosya · 967 satır (toplam satır: 253+168+46+97+120+83+81+39) | dosya okumaları |
| Client router dosya sayısı (js/router) | 28 dosya | assets AGENTS.md §2 envanteri |
| Pipeline middleware ters sarma | `array_reverse` tek çağrı | ApiMiddlewarePipeline.php:38 |
| ETag algoritması | md5(json) | ResponseNormalizationMiddleware.php:70 |

---

## §11 Olumsuz senaryolar (N1–N16)

| # | Senaryo | Beklenen davranış | Kanıt |
|---|---------|-------------------|-------|
| N1 | Bilinmeyen path'e GET | 404 + Allow yok | RouteTableTest:87-96 |
| N2 | Bilinen path'e bilinmeyen method (PUT) | 405 + Allow: GET + route null | RouteTableTest:78-85 |
| N3 | implemented=false uca GET | 405 + Allow: POST, OPTIONS | routes.php:35-39 |
| N4 | Prefix altı tanımsız alt yol | 404 (eski: 401 — kırıldı, kasıtlı) | RouteTableTest:98-119 |
| N5 | Route config dosyası yok | RuntimeException (`loadFromFile`) | RouteRegistry.php:17-19 |
| N6 | Route config array değilse | RuntimeException | RouteRegistry.php:23-25 |
| N7 | SpaRoute olmayan satır | sessizce atlanır (continue) | RouteRegistry.php:28-30 |
| N8 | pushState desteklenmeyen tarayıcı | router hiç başlamaz (fallback: normal link) | Router.js:51 |
| N9 | `RouterConfig.enabled === false` | init() erken döner | Router.js:50 |
| N10 | URL parse hatası (client) | hash düşer, query korunur | UrlUtils.js:16-20 |
| N11 | `normalizeCacheKey` parse hatası | ham URL döner | UrlUtils.js:38 |
| N12 | ETag eşleşen 304 isteği | `halt=true` + boş body; exit yok (CORS korunur) | ResponseNormalizationMiddleware.php:74-79 |
| N13 | `cacheable=false` yanıtta ETag sızması | basılmaz (sadece Cache-Control no-store) | ResponseNormalizationMiddleware.php:41-44 |
| N14 | `auth_key=` içeren callback URL | query strip YOK (korunur) | Router.js:61 |
| N15 | Bilinmeyen host'a link (cross-origin) | tam `href` döner (pathname'e düşürülmez) | UrlUtils.js:11 |
| N16 | Empty body + cacheable route | ETag basılmaz (`!empty($response['data'])` şartı) | ResponseNormalizationMiddleware.php:42 |

---

## §12 Havuz URL ve reddedilenler

Bu dosya Ünite-3 routing havuzundan **tek URL** alır:

| URL | Kullanım | Erişim |
|-----|----------|--------|
| `https://developer.mozilla.org/en-US/docs/Web/API/History` | §9 kapı + A19 pushState guard'ın standart bağlamı (History API) | exa fetch OK → 200-eşdeğer; status kodu `⚠️ VERIFICATION REQUIRED` (shell/curl yok) |

**Klasör içi dağılım (tek-dosya kuralı):**

| URL | Dosya |
|-----|-------|
| `Web/API/URL` · `Web/API/URLSearchParams` · `Web/API/Location` | routing-web-arastirma.md |
| `Web/API/History` | **bu dosya** |
| `Web/API/History/pushState` | routing-ornekler.md |
| `php.net reserved.variables.get.php` | routing-bilesenler.md |

**Reddedilen adaylar (etiket — tam URL gövdeye alınmadı):**

| Etiket | Neden |
|--------|-------|
| `MDN Window.history.replaceState` | Ayrı havuz URL'si yok; iddia düzeyinde (A21) kaldı |
| `php-fig psr-15` | Envanter kesişimi (K009/K0100) → etiket |
| `php-fig psr-17` | Envanter kesişimi (K006) → etiket |
| `IETF RFC 7231 §7.1.4 (Allow)` | Ünite-3 havuzunda tanımlı değil → havuz dışı yasağı |

---

## §13 PowerShell envanter bloğu (tekrar ölçüm)

```powershell
# Repo kökünden çalıştırılır (C:\www\coremusic.net)
# 1) Routing test dosyaları
Get-ChildItem -Recurse -Filter *Route*.php -Path shared\tests, api.coremusic.net\tests |
  ForEach-Object { "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.FullName }

# 2) RouteTableTest metot sayısı
(Select-String -Path shared\tests\Api\RouteTableTest.php -Pattern 'public function test').Count

# 3) routes.php uç sayımı (satır bazlı grok)
(Select-String -Path api.coremusic.net\config\routes.php -Pattern "'/(api/v1/[^']*)'\s*=>").Count
```

**Çalıştırma durumu:** ⚠️ shell izni reddi nedeniyle bu oturumda **çalıştırılmadı**.
Manuel okuma karşılıkları: T1 = 11 metot (RouteTableTest:38-165 sayıldı), routes.php
17 uç (§6 tablosu, satır satır okundu).

---

## §14 Açık sorular ve ⚠️ işaretleri

| # | Soru/işaret | Etki |
|---|-------------|------|
| Q1 | T2–T5 test dosyaları okunmadı (içerik/spesifik metot bilinmiyor) | envanter eksik işaretli; routing iddiaları T1 + kaynak ile kanıtlandı |
| Q2 | `curl` yok → History URL status kodu gözlemlenmedi | `⚠️ VERIFICATION REQUIRED` (erişilebilirlik fetch ile doğrulandı) |
| Q3 | E6: percent-decode (`%2F`) davranışı | `⚠️ VERIFICATION REQUIRED` |
| Q4 | Testler bu oturumda çalıştırılmadı (shell reddi) | PASS iddiası "kod kanıtı" düzeyinde; çalıştırma sonucu `⚠️ VERIFICATION REQUIRED` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
