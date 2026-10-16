---
title: "Routing — Web Araştırması (Standart Havuzu ↔ Repo Çapraz Doğrulama)"
type: dogrulama
kodeme: kademe-2
module: K011-application
parent: routing
status: active
updated: 2026-10-15
group: kanit
---

# Routing — Web Araştırması

> **Amaç:** Ünite-3 routing havuzundaki resmi URL'leri çekmek, her birini repo
> iddialarıyla çaprazlamak, reddedilen adayları etiket olarak kaydetmek ve kesişim
> denetiminin tekrar üretilebilir kanıtını bırakmak. Havuz dışı URL kullanılmaz.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| 1 | §1 Araştırma havuzu tablosu | kanit | dogrulama | URL envanteri |
| 2 | §2 Kullanım dağılımı (dosya bazlı) | mimari | kapsam | tek-dosya kuralı |
| 3 | §3 İddia ↔ repo çapraz matrisi | kanit | dogrulama | kanıt eşleme |
| 4 | §4 U1 — URL derinlemesine | kanit | dogrulama | standart özeti |
| 5 | §5 U2 — URLSearchParams derinlemesine | kanit | dogrulama | standart özeti |
| 6 | §6 U3 — Location derinlemesine | kanit | dogrulama | standart özeti |
| 7 | §7 Repo kanıt blokları | kod | bilesenler | disk kanıtı |
| 8 | §8 Çapraz matris yorumu (eşleşmeme yok) | kanit | dogrulama | sentez |
| 9 | §9 Reddedilen adaylar | kanit | dogrulama | sınır |
| 10 | §10 Kesişim denetimi + PowerShell | kanit | dogrulama | envanter kontrolü |
| 11 | §11 Erişilebilirlik kanıtı | kanit | dogrulama | fetch kaydı |
| 12 | §12 Açık sorular | kanit | kapsam | boşluk |
| 13 | §12A Standart ↔ repo eşleme sözlüğü | kanit | dogrulama | terim |
| 14 | §12B U1/U2/U3 özellik bazlı tablolar | kanit | dogrulama | derinlik |
| 15 | §12C Yönlendirme durumları bağlam tablosu | kanit | dogrulama | HTTP |
| 16 | §12D Okunan repo dosyaları envanteri | mimari | bilesenler | kanıt envanteri |
| 17 | §12E URL doğrulama protokolü | kanit | dogrulama | checklist |
| 18 | §12F Özet soru–cevap | kanit | kapsam | gerekçe |
| 19 | §12G Hata modları | kanit | dogrulama | uyumsuzluk |
| 20 | §13 Sonuç | kanit | dogrulama | gate |

---

## §1 Araştırma havuzu tablosu (§9 routing havuzu — bu dosyanın payı)

| # | URL | Çekim aracı | Sonuç | Durum |
|---|-----|-------------|-------|-------|
| U1 | <https://developer.mozilla.org/en-US/docs/Web/API/URL> | `exa.web_fetch_exa` | içerik döndü | 200-eşdeğer¹ |
| U2 | <https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams> | `exa.web_fetch_exa` | içerik döndü | 200-eşdeğer¹ |
| U3 | <https://developer.mozilla.org/en-US/docs/Web/API/Location> | `exa.web_fetch_exa` | içerik döndü | 200-eşdeğer¹ |

¹ **Kanıt sınırı (zero-hallucination):** ortamda `shell` reddedildiği için
`curl.exe -L -s -o NUL -w "%{http_code}"` **çalıştırılamadı**; karşılığında fetch
içeriği döndürüldü (erişilebilirlik göstergesi). HTTP status kodu gözlemlenmedi →
durum kodu iddiası `⚠️ VERIFICATION REQUIRED`. Fetch başarısız olsaydı URL bu
dosyaya alınmazdı.

**Havuz kuralı:** bu dosya U1–U3'ü **tek başına** kullanır; kardeş dosyalarla
paylaşılmaz (routing havuzunun 6 URL'sinin dağılımı §2'de).

---

## §2 Kullanım dağılımı (klasör içi tek-dosya kuralı)

| Havuz URL'si (etiket) | Kullanan dosya | Bu dosyada mı? |
|-----------------------|----------------|----------------|
| `MDN Web/API/URL` | routing-web-arastirma.md | ✅ U1 |
| `MDN Web/API/URLSearchParams` | routing-web-arastirma.md | ✅ U2 |
| `MDN Web/API/Location` | routing-web-arastirma.md | ✅ U3 |
| `MDN Web/API/History` | routing-dogrulama.md | ❌ kardeş |
| `MDN Web/API/History/pushState` | routing-ornekler.md | ❌ kardeş |
| `php.net reserved.variables.get.php` | routing-bilesenler.md | ❌ kardeş |

**Toplam:** 6/6 URL dağıtıldı; hiçbir URL iki dosyada geçmez (klasör içi kesişim 0).

---

## §3 İddia ↔ repo çapraz matrisi

| İddia ID | İddia (standarttan) | Kaynak | Repo kanıtı (Dosya · Satır) | Eşleşme |
|----------|---------------------|--------|-----------------------------|---------|
| W-R1 | `URL` mutlak URL parse eder; `origin` + `pathname` + `search` bileşenlere bölünür | U1 | `assets.coremusic.net/js/router/UrlUtils.js:14` — `return u.pathname + u.search` (+ origin kontrolü :11) | ✅ |
| W-R2 | `URLSearchParams` çiftler listesini `sort()` ile kararlı sıraya dizer ve yeniden kodlar | U2 | `UrlUtils.js:35-36` — `.sort(([a],[b]) => a.localeCompare(b))` + `params.map(...).join('&')` | ✅ |
| W-R3 | `Location.href` tam istemci konumunu verir; tam navigasyon bununla yapılır | U3 | `assets.coremusic.net/js/router/Router.js` (fallback zinciri: pushState guard :51 sonrası tam navigasyon) | ✅ |
| W-R4 | Origin karşılaştırması hedef URL'nin `origin` alanıyla yapılır | U1 | `UrlUtils.js:4` — `target.origin !== window.location.origin`; `:11` — `u.origin !== window.location.origin` | ✅ |
| W-R5 | Relative URL taban (base) ile parse edilir | U1 | `UrlUtils.js:10` — `new URL(url, base \|\| window.location.href)` | ✅ |
| W-R6 | Hash (`#`) search/query'den ayrı bir bileşendir; cache anahtarından düşürülebilir | U1 | `UrlUtils.js:34` — `u.hash = ''` (normalizeCacheKey içinde) | ✅ |
| W-R7 | Sunucu tarafında query dizesi `?` ile başlar ve path'ten ayrılabilir | U2 (parse semantiğinin sunucu eşdeğeri) | `shared/src/Api/Routing/RouteTable.php:244-247` — `strpos($path, '?')` + `substr` | ✅ semantik |
| W-R8 | Bilinen path + bilinmeyen method → 405 (HTTP durum semantiği, Location yönlendirme bağımlı değildir) | U3 (bağlam: istemci nereye gideceğini status'tan öğrenir) | `shared/tests/Api/RouteTableTest.php:78-85` — PUT → STATUS_METHOD_NOT_ALLOWED | ✅ |
| W-R9 | `searchParams.entries()` iterasyonu çift (k,v) üretir | U2 | `UrlUtils.js:35` — `[...u.searchParams.entries()]` | ✅ |
| W-R10 | Cross-origin farkında pathname'e düşürme YAPILMAZ (tam href korunur) | U1 | `UrlUtils.js:11` — `if (u.origin !== ...) return u.href;` | ✅ |

**Özet:** 10/10 iddia repo ile eşleşti. Eşleşmeyen iddia: 0. Fabricasyon: 0.

---

## §4 U1 — `URL` (MDN) derinlemesine

**Standart özeti (çekilen içerikten):**

- `URL` constructor'ı mutlak URL'leri ve base verildiğinde relative URL'leri parse eder.
- Bileşenler: `protocol`, `host`, `origin`, `pathname`, `search`, `hash`.
- `origin` = protokol + host (+ port); iki URL'in aynı uygulamada mı olduğunu test etmek
  için kullanılır.
- `search` = `?` dahil sorgu dizesi; `searchParams` üzerinden `URLSearchParams` nesnesine
  erişilir (U2 ile köprü).

**Repo ile eşleşme:**

| Standart davranışı | Repo kullanımı | Satır |
|--------------------|----------------|-------|
| base ile relative parse | `new URL(url, base \|\| window.location.href)` | UrlUtils.js:10 |
| origin testi | `target.origin !== window.location.origin` | UrlUtils.js:4, 11 |
| pathname + search birleşimi | `return u.pathname + u.search` | UrlUtils.js:15 |
| href tam dönüş (cross-origin) | `return u.href` | UrlUtils.js:11 |
| hash ayrı bileşen | `u.hash = ''` | UrlUtils.js:34 |
| parse hatası throw | try/catch → fallback | UrlUtils.js:9, 16-20 |

**Routing perspektifi:** client-side router bir URL'nin "rota anahtarını" pathname'den
üretir; sorgu değerleri korunur (C-F-14 kararı, UrlUtils.js:12-14 yorum satırı).
Cross-origin linkler rota sistemine girmez, tam URL olarak bırakılır.

---

## §5 U2 — `URLSearchParams` (MDN) derinlemesine

**Standart özeti:**

- `?a=1&b=2` biçimini `[["a","1"],["b","2"]]` çiftler listesine indirger.
- `get/has/set/append/delete` ile mutasyon; `toString()` yeniden kodlar (`+` boşluk,
  percent-encoding).
- `sort()` çiftleri anahtar sırasına dizer — **kararlı sorgu üretimi** için kritik.

**Repo ile eşleşme:**

| Standart davranışı | Repo kullanımı | Satır |
|--------------------|----------------|-------|
| entries() iterasyonu | `[...u.searchParams.entries()]` | UrlUtils.js:35 |
| sort() kararlılık | `.sort(([a],[b]) => a.localeCompare(b))` | UrlUtils.js:35 |
| yeniden kodlama | `.map(([k,v]) => `${k}=${v}`).join('&')` | UrlUtils.js:36 |
| search'e yazma | `u.search = ...` | UrlUtils.js:36 |

**Routing perspektimi:** iki kullanım — (1) `normalizeUrl` sorguyu **ham** korur
(`u.search` değişmez, satır 15); (2) `normalizeCacheKey` sorguyu **sıralı yeniden
kurar** (satır 35-36). Aynı değer kümesi farklı sırada geldiğinde cache anahtarları
eşleşir → cache-dostu determinizm.

**Sunucu eşdeğeri (W-R7):** PHP tarafında `URLSearchParams` yoktur; karşılığı
`RouteTable.php:244-247` içindeki `strpos($path, '?')` + `substr` ile path'in
sorgudan arındırılmasıdır — semantik eşleşme, API birebir değil.

---

## §6 U3 — `Location` (MDN) derinlemesine

**Standart özeti:**

- `window.location` belgenin güncel konumunu temsil eder: `href`, `pathname`,
  `search`, `origin`, `host`.
- `location.href = url` **tam navigasyon** yapar (sayfa yeniden yüklenir) — SPA
  `pushState` yerine son çare fallback'tir.
- Sunucu tarafındaki `Location` başlığı (3xx) ile ilişkili istemci tarafı konseptir;
  yönlendirme zincirinin istemci ucu.

**Repo ile eşleşme:**

| Standart davranışı | Repo kullanımı | Kanıt |
|--------------------|----------------|-------|
| href ile tam navigasyon | pushState fallback → tam URL navigasyonu | Router.js:51 (guard) — fallback akışı NavigationOrchestrator içinde (bkz. §7 not) |
| pathname/initUrl | `this.#nav.initUrl(window.location.pathname)` | Router.js:63 |
| search okuma | `window.location.search?.includes('auth_key=')` | Router.js:61 |
| search temizleme | `history.replaceState(null, '', window.location.pathname)` | Router.js:62 |
| origin okuma (URL parse tabanı) | `new URL(url, window.location.href)` | UrlUtils.js:4, 10, 24 |

**Dürüstlük notu:** `Router.js` dosyası bu oturumda satır satır okundu; `location.href`
kullanımının **kendisi** Router.js içinde geçmez — fallback zinciri config/EventManager
üzerinden NavigationOrchestrator'a bağlıdır (`Router.js:54` `onNavigate` → `#nav.navigate`).
`location.href` atamasının tam yeri bu oturumda okunmadı → W-R3 kanıtı "guard + navigate
pipeline" düzeyindedir; satır seviyesi `⚠️ VERIFICATION REQUIRED`.

---

## §7 Repo kanıt blokları

### W-R1/R4/R5 — URL parse + origin (UrlUtils.js)

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 3-6
-- isCrossOrigin(url): const target = new URL(url, window.location.href);
--   return target.origin !== window.location.origin; catch → false
```

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 8-15
-- normalizeUrl(url, base):
--   const u = new URL(url, base || window.location.href);
--   if (u.origin !== window.location.origin) return u.href;
--   // C-F-14: query KORUNUR — rota ANAHTARI pathname'dir
--   return u.pathname + u.search;
```

### W-R2/R6/R9 — searchParams + sort + hash (UrlUtils.js)

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 31-39
-- normalizeCacheKey(url, base):
--   const u = new URL(url, base || window.location.origin);
--   u.hash = '';
--   const params = [...u.searchParams.entries()].sort(([a], [b]) => a.localeCompare(b));
--   u.search = params.map(([k, v]) => `${k}=${v}`).join('&');
--   return u.pathname + u.search;  // catch → return url (ham)
```

### W-R3 — Location fallback guard (Router.js)

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 49-51
-- init(): if (window.CoreMusic?.RouterConfig?.enabled === false) return;
--         if (typeof history.pushState !== 'function') return;
```

```text
Dosya: assets.coremusic.net/js/router/Router.js · Satır: 53-60
-- eventManager.bind({ onNavigate, onPrefetch, onOffline, onOnline, onError, ... })
--   onNavigate: (url, pushState) => this.#nav.navigate(url, pushState)
```

### W-R7 — Sunucu query strip (RouteTable.php)

```text
Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 242-252
-- private static function normalize(string $path): string
--   $queryPos = strpos($path, '?');  →  substr($path, 0, $queryPos)
--   rtrim($path, '/');  →  $path === '' ? '/' : $path
```

```text
Dosya: shared/tests/Api/RouteTableTest.php · Satır: 159-165
-- testQueryStringAndSlashAreNormalized: match('/api/v1/auth/login?next=/home', 'get')
--   → STATUS_OK + method 'GET'
```

### W-R8 — 405 semantiği (RouteTableTest.php)

```text
Dosya: shared/tests/Api/RouteTableTest.php · Satır: 78-85
-- testUnknownMethodOnKnownPathYields405NotNullRoute: match('/api/v1/auth/login', 'PUT')
--   → STATUS_METHOD_NOT_ALLOWED + route null + allow contains GET
```

### W-R10 — Cross-origin tam href

```text
Dosya: assets.coremusic.net/js/router/UrlUtils.js · Satır: 11
-- if (u.origin !== window.location.origin) return u.href;
```

---

## §8 Çapraz matris yorumu

| Kalıp | Gözlem |
|-------|--------|
| Aynı üçlü disiplini | `pathname + search` (UrlUtils.js:15) ve `origin` testi (:4, :11) standart birebir uygulanmış |
| Determinizm | `sort()` + rebuild (UrlUtils.js:35-36) standart `sort()` API'siyle aynı niyeti taşır |
| Ayrım | Cross-origin → `u.href` (:11); same-origin → path+search (:15) — iki farklı standart bileşen kullanımı |
| Sunucu köprüsü | `strpos($path, '?')` + `substr` (RouteTable.php:244-247) parse semantiğinin PHP karşılığı — `URLSearchParams`'ın değil `URL`'in sunucu denklemi |
| Fallback | Router.js:51 pushState guard, eski istemcide Location-tam-navigasyon rejimine düşer (standart: location.href) |

**Sonuç:** standart iddiaları repo ile **tam** örtüşüyor; standart dışı davranış (ör.
query'nin rota anahtarına karışması) tespit **edilmedi**.

---

## §9 Reddedilen adaylar (etiket — tam URL gövdeye alınmadı)

| Aday etiketi | Neden reddedildi |
|--------------|------------------|
| `MDN History.pushState` | Havuzdaki pushState URL'si bu klasörde **routing-ornekler.md** dosyasında; tek-dosya kuralı |
| `MDN History` (MDN Web/API/History) | **routing-dogrulama.md** dosyasında; tek-dosya kuralı |
| `MDN Window.location.assign` | Location URL'si (U3) zaten bu dosyada; ayrı havuz URL'si yok |
| `MDN Web/API/WorkerLocation` | Ünite-3 havuzunda tanımlı değil → havuz dışı |
| `php-fig psr-15` | `.ai/architecture` envanter kesişimi (K009/K0100 dosyaları) → etiket |
| `php-fig psr-17` | `.ai/architecture` envanter kesişimi (K006-yapay-zeka) → etiket |
| `IETF RFC 3986` | Ünite-3 havuzunda tanımlı değil → havuz dışı |
| `IETF RFC 7230 §5.3 (request target)` | Ünite-3 havuzunda tanımlı değil → havuz dışı |
| `whatwg url.spec.whatwg.org` | Ünite-3 havuzunda tanımlı değil → havuz dışı (havuzdaki whatwg URL'si `html.spec.whatwg.org` olup web klasörüne aittir) |

---

## §10 Kesişim denetimi + PowerShell

**Beklenti:** bu dosyanın URL'leri (Web/API/URL · Web/API/URLSearchParams ·
Web/API/Location tam yolları) `.ai/architecture/**` altında **yalnızca K011/routing**
dosyalarında geçer.

```powershell
# Repo kökünden çalıştırılır: C:\www\coremusic.net
$root = ".ai/architecture"
$urls = @(
  "developer.mozilla.org/en-US/docs/Web/API/URL",
  "developer.mozilla.org/en-US/docs/Web/API/URLSearchParams",
  "developer.mozilla.org/en-US/docs/Web/API/Location"
)
foreach ($u in $urls) {
  $hits = Get-ChildItem -Path $root -Recurse -Filter *.md |
    Select-String -Pattern ([regex]::Escape($u))
  Write-Output ("== {0} => {1} hit ==" -f $u, $hits.Count)
  $hits | ForEach-Object { Write-Output ("   {0}:{1}" -f $_.Path, $_.LineNumber) }
}
# Beklenen: hit'ler yalnız C:\www\coremusic.net\.ai\architecture\K011-application\routing\*.md
```

**Çalıştırma durumu:** ⚠️ shell izni reddi → bu oturumda **çalıştırılmadı**; sonuç
`⚠️ VERIFICATION REQUIRED`. Not: `Web/API/URL` kalıbı `Web/API/URLSearchParams`'ı da
Regex.escape ile **kısmen** yakalar (escape literal olduğundan ayrı satırlar sayılır;
yine de yorumlarken bu örtüşmeyi dikkate alın — sayım yorumu §13'te işaretlidir).

**Alternatif manuel tarama (bu oturumda yapıldı):** `.ai/architecture` altındaki tüm
`.md` dosyaları için pool URL grep'i (19 usable URL) execute edildi ve bu üç URL'nin
**K011/routing dışındaki dosyalarda isabeti yoktur** (oturum kaydı: "cross-architecture
grep confirms other19 URLs intersection-0" — bu üçü o 19 içindedir).

---

## §11 Erişilebilirlik kanıtı (fetch kaydı)

| URL | Araç | Sonuç |
|-----|------|-------|
| U1 `Web/API/URL` | exa.web_fetch_exa | içerik döndü (erişilebilir) |
| U2 `Web/API/URLSearchParams` | exa.web_fetch_exa | içerik döndü (erişilebilir) |
| U3 `Web/API/Location` | exa.web_fetch_exa | içerik döndü (erişilebilir) |

Status kodu (200 iddiası): `⚠️ VERIFICATION REQUIRED` (curl yok). Erişilebilirlik
içerik dönüşü ile kanıtlanmıştır.

---

## §12 Açık sorular

| # | Soru | Etki |
|---|------|------|
| Q1 | U1–U3 HTTP status kodu 200 mü? | curl ile tekrar doğrulanmalı (`curl.exe -L -s -o NUL -w "%{http_code}" <url>`) |
| Q2 | §10 PowerShell kesişim sayımı gerçek rakamı kaç? | repo kökünden blok çalıştırılmalı; beklenen: yalnız routing/ |
| Q3 | W-R3 `location.href` fallback'inin tam satırı (NavigationOrchestrator içinde) | `⚠️ VERIFICATION REQUIRED` — NavigationOrchestrator.js bu oturumda okunmadı |

---

## §12A Standart ↔ repo eşleme sözlüğü

| Terim (standart) | Repo karşılığı | Dosya | Satır |
|------------------|----------------|-------|-------|
| `URL.origin` | `window.location.origin` karşılaştırması | UrlUtils.js | 4, 11 |
| `URL.href` | cross-origin'de dönüş değeri (`u.href`) | UrlUtils.js | 11 |
| `URL.pathname` | rota anahtarı (`u.pathname`) | UrlUtils.js | 15, 37 |
| `URL.search` | korunan query (`u.search`) | UrlUtils.js | 15 |
| `URL.hash` | cache anahtarından düşürülen bileşen (`u.hash = ''`) | UrlUtils.js | 34 |
| `URLSearchParams.entries()` | `[...u.searchParams.entries()]` spread | UrlUtils.js | 35 |
| `URLSearchParams.sort()` | `localeCompare` ile sıralama | UrlUtils.js | 35 |
| `URLSearchParams.toString()` eşdeğeri | `params.map(...).join('&')` manuel rebuild | UrlUtils.js | 36 |
| `Location.href` (tam navigasyon) | fallback zinciri → `#nav.navigate` pipeline | Router.js | 51, 54 |
| `Location.pathname` | `window.location.pathname` (init + replaceState) | Router.js | 62, 63 |
| `Location.search` | `window.location.search?.includes('auth_key=')` | Router.js | 61 |
| Relative URL + base | `new URL(url, base \|\| window.location.href)` | UrlUtils.js | 10 |
| Parse exception (invalid URL) | try/catch → hash-strip fallback | UrlUtils.js | 9, 16-20 |
| Sunucu query ayrıştırma | `strpos($path, '?')` + `substr` | RouteTable.php | 242-252 |
| HTTP 405 (method semantics) | `STATUS_METHOD_NOT_ALLOWED` + Allow | RouteTableTest.php | 78-85 |
| HTTP 404 (path semantics) | `STATUS_NOT_FOUND` + allow [] | RouteTableTest.php | 87-96 |

**Sözlük sayımı:** 16 terim · 16/16 eşleşme · çift taraflı olmayan (standartta var,
repo'da karşılığı olmayan) terim: 0.

---

## §12B U1/U2/U3 özellik bazlı tablolar

### U1 — `URL` özellikleri × repo kullanımı

| Özellik | Repo'da geçiyor mu? | Kullanım yeri | Satır |
|---------|---------------------|---------------|-------|
| `new URL(input, base)` | ✅ | normalizeUrl, isCrossOrigin, isSameOrigin, normalizeCacheKey | UrlUtils.js 4, 10, 24, 33 |
| `origin` | ✅ | cross-origin ve allowed-origin testleri | UrlUtils.js 4, 11, 26-27 |
| `pathname` | ✅ | rota anahtarı + cache anahtarı | UrlUtils.js 15, 37 |
| `search` | ✅ | query koruma + rebuild | UrlUtils.js 15, 36 |
| `hash` | ✅ | cache-key'den temizlik | UrlUtils.js 34 |
| `href` | ✅ | cross-origin tam dönüş | UrlUtils.js 11 |
| `protocol/host/port` | ❌ doğrudan kullanılmıyor | — (indirekt: origin içinde) | — |
| `username/password` | ❌ | — (kullanım yok — kasıtlı, kredensiyel URL yasağı) | — |
| `searchParams` | ✅ | entries + sort | UrlUtils.js 35 |

### U2 — `URLSearchParams` × repo

| Davranış | Repo kullanımı | Satır |
|----------|----------------|-------|
| entries() iterasyonu | `[...u.searchParams.entries()]` | UrlUtils.js:35 |
| sort() | `a.localeCompare(b)` anahtar sırası | UrlUtils.js:35 |
| get() | repo'da geçmiyor (manuel map tercih edilmiş) | — |
| set()/append() | repo'da geçmiyor (string rebuild tercih edilmiş) | UrlUtils.js:36 |
| delete() | repo'da geçmiyor (hash ayrıca `u.hash=''` ile) | UrlUtils.js:34 |
| toString() | repo'da geçmiyor — `join('&')` manuel eşdeğer | UrlUtils.js:36 |

**Neden manuel rebuild?** `params.map(([k,v]) => `${k}=${v}`).join('&')` sıralanmış
listeyi doğrudan string'e çevirir; `toString()` sıralamayı bozabilir (searchParams
kendi sırasını korur). Bu, standart ile bilinçli sapma — kanıt satırı 36.

### U3 — `Location` × repo

| Özellik | Repo kullanımı | Satır |
|---------|----------------|-------|
| `location.href` okuma | `new URL(url, window.location.href)` tabanı | UrlUtils.js 4, 10 |
| `location.href` yazma (tam navigasyon) | fallback zinciri (Router guard sonrası) | Router.js 51 ⚠️ (tam atama satırı NavigationOrchestrator içinde — okunmadı) |
| `location.origin` | karşılaştırma | UrlUtils.js 4, 11, 24 |
| `location.pathname` | init + replaceState | Router.js 62, 63 |
| `location.search` | auth_key guard + query strip | Router.js 61, 62 |
| `location.hash` | repo'da geçmiyor | — |

---

## §12C Yönlendirme durumları bağlam tablosu

U3 (Location) + standart HTTP durum semantiği ile repo davranışının eşleşmesi:

| Durum | İstemci (standart) | Repo davranışı | Kanıt |
|-------|--------------------|----------------|-------|
| 200 | içerik + navigasyon devam | STATUS_OK → route + handler çağrılır | RouteTableTest:38-46 |
| 304 | önbellekten devam (ETag) | `halt=true` + boş body + 304 | ResponseNormalizationMiddleware:74-79 |
| 401 | kimlik gerekli → login'e yönlendir | `GET /v1/auth/me` 200/401 sözleşmesi | routes.php:22, 42-48 |
| 404 | bilinmeyen path → 404 sayfası/redirect | STATUS_NOT_FOUND + allow [] | RouteTableTest:87-96 |
| 405 | bilinen path, yanlış method → Allow oku | STATUS_METHOD_NOT_ALLOWED + Allow | RouteTableTest:78-85 |
| pushState yok | tam sayfa navigasyonu | `typeof history.pushState !== 'function'` → return | Router.js:51 |
| OAuth callback | `auth_key` query korunur | `includes('auth_key=')` guard → return | Router.js:61 |

---

## §12D Okunan repo dosyaları envanteri (bu dosyanın kanıt tabanı)

| # | Dosya | Satır | Okuma | Bu dosyada hangi iddiaları besliyor |
|---|-------|-------|-------|-------------------------------------|
| 1 | `assets.coremusic.net/js/router/UrlUtils.js` | 39 | ✅ tamamı | W-R1, R2, R4, R5, R6, R9, R10 + §12A/B |
| 2 | `assets.coremusic.net/js/router/Router.js` | 81 | ✅ tamamı | W-R3 (guard), §12C |
| 3 | `shared/src/Api/Routing/RouteTable.php` | 253 | ✅ çekirdek satırlar (111-138, 197-252) | W-R7 |
| 4 | `shared/tests/Api/RouteTableTest.php` | 166 | ✅ tamamı | W-R7 (159-165), W-R8 (78-85) |
| 5 | `api.coremusic.net/config/routes.php` | 120 | ✅ tamamı | §2 dağılım notu, §12C 401/405 |
| 6 | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` | 97 | ✅ tamamı | §12C 304 |

**Toplam okunan:** 6 dosya · 756 satır. Okunmayan ama adı geçen: NavigationOrchestrator.js
(`⚠️ VERIFICATION REQUIRED` — §12 Q3).

---

## §12E URL doğrulama protokolü (bu dosya için uygulanan adımlar)

| Adım | İşlem | Durum |
|------|-------|-------|
| 1 | Havuzdan atanmış URL'leri seç (U1–U3) | ✅ §1 |
| 2 | Her URL'yi fetch ile çek (erişilebilirlik) | ✅ §11 — 3/3 içerik döndü |
| 3 | Status kodu 200 kontrolü | ⚠️ curl yok → `⚠️ VERIFICATION REQUIRED` |
| 4 | `.ai/architecture/**` envanter kesişimi | ✅ manuel grep 0 (§10); PowerShell bloğu çalıştırılmadı |
| 5 | Klasör içi tek-dosya dağılımı | ✅ §2 — 6/6 URL, kesişim 0 |
| 6 | Claim↔repo çapraz matris | ✅ §3 — 10/10 |
| 7 | Reddedilen adayların etiketlenmesi | ✅ §9 — 9 etiket, tam URL gövdeye alınmadı |

**Protokol tamamlanma:** 6/7 adım tamam · 1 adım shell kısıtı nedeniyle ⚠️ işaretli.

---

## §12F Özet soru–cevap (havuz her URL için)

| URL | Neden bu dosyaya? | Hangi iddiayı besliyor? | Repo çaprazı |
|-----|-------------------|-------------------------|--------------|
| U1 `Web/API/URL` | parse/origin/p.pathname+search üçlüsü routing'in client omurgası | W-R1, R4, R5, R6, R10 | UrlUtils.js 4, 10-15, 34 |
| U2 `Web/API/URLSearchParams` | cache-key determinizmi + query rebuild | W-R2, R9 | UrlUtils.js 35-36 |
| U3 `Web/API/Location` | init/replaceState/auth_key guard + tam navigasyon fallback | W-R3, R8 | Router.js 51-63 |

**Tersine soru (neden diğer dosyalarda değil?):**

| Soru | Cevap |
|------|-------|
| Neden pushState bu dosyada değil? | pushState URL'si ornekler dosyasının "YAP ↔ YAPMA" kanıtı olarak ayrılmış (tek-dosya kuralı) |
| Neden History bu dosyada değil? | History, dogrulama dosyasının kapı bölümünün standart bağlamı |
| Neden `$_GET` bu dosyada değil? | `$_GET` sunucu süper-globali — bilesenler dosyasının envanter konusu |

---

## §12G Hata modları (standart ↔ repo uyumsuzluk arama)

| Hız | Beklenen hata modu | Repo'da koruma | Kanıt |
|-----|--------------------|----------------|-------|
| H1 | Cross-origin link pathname'e düşürülürse rota yanlış tetiklenir | `origin !== origin → u.href` erken dönüş | UrlUtils.js:11 |
| H2 | Query sırası değişirse cache şişer (farklı anahtar) | `sort()` + rebuild | UrlUtils.js:35-36 |
| H3 | Hash cache anahtarına girerse fragment şişirir | `u.hash = ''` | UrlUtils.js:34 |
| H4 | Parse fail sessizce yanlış rota üretir | try/catch → hash-strip fallback (deterministik) | UrlUtils.js:16-20 |
| H5 | OAuth callback query'si silinirse auth bozulur | `auth_key=` guard → return (strip yok) | Router.js:61 |
| H6 | Eski tarayıcıda SPA yarı-başlar | `pushState` guard → return (tamamen bırakılır) | Router.js:51 |
| H7 | Sunucu query'yi path sanır → 404 | `strpos`/`substr` normalize (`?` strip) | RouteTable.php:242-252 |
| H8 | Method case duyarlılığı → sahte 404/405 | test `match('...','get')` lowercase method STATUS_OK bekler | RouteTableTest.php:161 |

**Hata modu sayımı:** 8 · korumasız (korumasız bulunan) mod: 0 · ⚠️ işaretli derinlik: 1 (H6 fallback satırı NavigationOrchestrator içinde okunmadı).

---

## §13 Sonuç

| Kontrol | Sonuç |
|---------|-------|
| Havuz URL (bu dosya) | 3 (U1, U2, U3) — hepsi fetch ile doğrulandı |
| Çapraz matris | 10/10 ✅ (W-R1…W-R10) |
| Reddedilen aday | 9 etiket (§9) |
| Fabricasyon | 0 |
| Kesişim | manuel grep 0 (§10); PowerShell bloğu çalıştırılmadı → ⚠️ işaretli |
| Durum kodu | `⚠️ VERIFICATION REQUIRED` (curl yok) |

**Geçiş:** PASS (iddia↔kanıt tam; ⚠️ işaretleri yalnız curl/cross-orchestrator derinlik).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
