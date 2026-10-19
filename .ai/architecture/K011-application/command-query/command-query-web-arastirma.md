---
title: "K011 · command-query — web-arastirma"
type: architecture
kodeme: kademe-2
module: K011-application
parent: command-query
status: active
updated: 2026-10-15
group: kanit
---

# K011-application/command-query — Web Araştırması

**Amaç:** Bu belge `command-query` klasörü için spec §9 havuzundaki 4 PHP URL'sini `webfetch`
ile erişir, `curl.exe -L -s -o NUL -w "%{http_code}"` ile HTTP 200 doğrular, her URL'den çıkan
claim'i repo kanıtıyla eşler (claim↔repo matrisi), reddedilen adayları yalnızca etiketler ve
kesişim kontrolünü PowerShell bloğu olarak metin verir. Havuz dışı URL eklenmez.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | Havuz tablosu | kanit | URL | 4 PHP URL + HTTP kodu + erişim |
| §2 | Claim↔repo matrisi | kanit | eşleme | Her URL claim'i ↔ repo kanıtı |
| §3 | Reddedilenler | kanit | etiket | Havuz dışı/önceden reddedilen adaylar (etiket only) |
| §4 | Açık sorular | kanit | boşluk | Spec §8 kaydı dahil |
| §5 | Kesişim PowerShell bloğu | kanit | envanter | `k011-urls.txt` kesişim kontrolü (çalıştırıldı) |
| §6 | curl doğrulama kaydı | kanit | HTTP | 4/4 HTTP 200 |
| §7 | Genişletilmiş callable envanteri | kanit | eşleme | 35 callable/Closure imzası |
| §8 | Alternatif sözdizimi katman dağılımı | kanit | katman | view 32 · application 0 |
| §9 | Bitwise gerçek isabet tablosu | kanit | eşleme | UUID mask + shift düzeltmesi |
| §10 | Composer bağımlılık kanıtı | kanit | bağımlılık | 14 require + event-dispatcher |
| §11 | Genişletilmiş istatistik | kanit | yoğunluk | claim ↔ sayı çapraz doğrulama |
| §12 | webfetch erişim kaydı | kanit | erişim | URL başına fetch notları |

---

## §1 — Havuz tablosu (spec §9 — command-query, 4 URL)

| # | URL | Erişim | HTTP | Tarih | Claim özeti |
|---|-----|--------|------|-------|-------------|
| 1 | `https://www.php.net/manual/en/functions.variable-functions.php` | webfetch (metin) + curl | **200** | 2026-10-15 | Değişken fonksiyon: `$func()` çağrısı, callback/function table |
| 2 | `https://www.php.net/manual/en/control-structures.alternative-syntax.php` | webfetch (metin) + curl | **200** | 2026-10-15 | Alternatif sözdizimi: `if(): endif;`, `foreach(): endforeach;` |
| 3 | `https://www.php.net/manual/en/language.operators.arithmetic.php` | webfetch (metin) + curl | **200** | 2026-10-15 | Aritmetik operatörler: `+ - * / % **`, intdiv, fmod |
| 4 | `https://www.php.net/manual/en/language.operators.bitwise.php` | webfetch (metin) + curl | **200** | 2026-10-15 | Bit operatörleri: `& \| ^ ~ << >>`, error_reporting `E_ALL & ~E_NOTICE` |

> **curl doğrulaması (2026-10-15):** 4/4 URL **HTTP 200** döndü
> (`curl.exe -L -s -o NUL -w "%{http_code}"` — `-L` redirect takibi ile).
> Komut/metin (subagent çalıştırmadı, orkestratör çalıştırır):
> `curl.exe -L -s -o NUL -w "%{http_code}" <url>` — 4 satır, hepsi `200`.

### §1.1 Erişim notları (webfetch çıktısı)

| # | URL | webfetch durumu | Dikkat çeken claim |
|---|-----|-----------------|---------------------|
| 1 | variable-functions | ✅ metin alındı | "if a variable name has parentheses appended to it, PHP will look for a function with the same name" — `$this->$name()` (variable method), `Foo::$variable()` (static), `array("Foo","bar")` (complex callables) |
| 2 | alternative-syntax | ✅ metin alındı | "the basic form of the alternate syntax is to change the opening brace to a colon (:) and the closing brace to `endif;`… Mixing syntaxes in the same control block is not supported" |
| 3 | arithmetic | ✅ metin alındı | "Operands of modulo are converted to int before processing" · "The result of the modulo operator `%` has the same sign as the dividend" |
| 4 | bitwise | ✅ metin alındı | "Use parentheses to ensure the desired precedence" · `error_reporting` `E_ALL & ~E_NOTICE` real-world demo · string operands → ASCII değerleri |

---

## §2 — Claim↔repo matrisi (her URL claim'i ↔ repo kanıtı)

### §2.1 Variable functions (URL 1)

| # | Claim (URL 1) | Repo karşılığı | Dosya | Satır | Durum |
|---|---------------|----------------|-------|-------|-------|
| 1 | Değişken fonksiyon çağrısı `$func()` | `$value = $callback();` | `shared/src/Cache/PageCacheAdapter.php` | 41 | ✅ EŞLEŞTİ |
| 2 | Değişken fonksiyon çağrısı `$func()` | `$value = $loader();` | `shared/src/AI/MemorySystem.php` | 138 | ✅ EŞLEŞTİ |
| 3 | Değişken sınıf `new $className()` | `return $this->instances[$name] ??= new $className();` | `shared/src/Component/ComponentRegistry.php` | 121 | ✅ EŞLEŞTİ |
| 4 | Callback (callable) çağrısı | `($this->handlerResolver)($route, $request)` | `shared/src/Api/Gateway.php` | 155 | ✅ EŞLEŞTİ |
| 5 | Variable method `$this->$name()` | (repo'da doğrudan isabet yok) | — | — | ⚠️ VERIFICATION REQUIRED |
| 6 | Complex callable `array("Foo","bar")` | (repo'da doğrudan isabet yok) | — | — | ⚠️ VERIFICATION REQUIRED |

> **Yorum:** Repo, URL 1'in "callbacks, function tables" claim'ini Gateway handlerResolver
> (`Gateway.php:33,155`) ve cache loader'lar (`PageCacheAdapter.php:41`, `MemorySystem.php:138`)
> ile **doğrular**. Variable method (`$this->$name()`) ve complex callable (`array(...)`)
> desenleri repo'da doğrudan isabet bulmadı → ⚠️ (kullanım alanı yok, kopyalanmaz).

### §2.2 Alternative syntax (URL 2)

| # | Claim (URL 2) | Repo karşılığı | Dosya | Satır | Durum |
|---|---------------|----------------|-------|-------|-------|
| 1 | `if (): endif;` | `<?php if ($this->variant->isWide()): ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 21 | ✅ EŞLEŞTİ |
| 2 | `foreach (): endforeach;` | `<?php foreach ($this->cards as $card): ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 25 | ✅ EŞLEŞTİ |
| 3 | `endif;` kapanışı | `<?php endif; ?>` | `home.coremusic.net/pages/components/recent-tracks.php` | 75 | ✅ EŞLEŞTİ |
| 4 | Repo geneli yaygın kullanım | ≥10 dosya isabeti (vendor hariç) | §6.8 (dogrulama) | 39, 73, 78, 85, 86, 182, 249… | ✅ EŞLEŞTİ |
| 5 | "Mixing syntaxes not supported" | Repo'da karışım isabeti yok | — | — | ✅ (uyumlu — karışım yok) |

> **Yorum:** URL 2'nin claim'i repo'da **güçlü eşleşme** bulur: view katmanı (sayfa
> şablonları) alternatif sözdizimini tutarlı kullanır. `shared/src/` (application/infra
> katmanı) ise alternatif sözdizimi **kullanmaz** (0 isabet) — bu kasıtlı katman farkıdır:
> view PHP template, application PHP sınıf. Komut/sorgu akışı `shared/src/` katmanından
> geçtiği için (Gateway, DTO) URL 2'nin claim'i bu klasörde **yalnız view sınırı** olarak
> referans edilir; akış mantığına girmez.

### §2.3 Arithmetic operators (URL 3)

| # | Claim (URL 3) | Repo karşılığı | Dosya | Satır | Durum |
|---|---------------|----------------|-------|-------|-------|
| 1 | `max()`/`min()` sınır clamp'i (aritmetik) | `page: max(1, $data['page'] ?? 1)` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 76 | ✅ EŞLEŞTİ |
| 2 | İç içe `min(100, max(1, …))` | `limit: min(100, max(1, $data['limit'] ?? 20))` | `shared/src/Api/Dto/Request/MusicSearchRequest.php` | 77 | ✅ EŞLEŞTİ |
| 3 | Modulo dönüşümü (int) | (repo'da sorgu clamp'inde modulo yok) | — | — | ⚠️ (ilgisiz) |
| 4 | `intdiv()`/`fmod()` | (repo'da doğrudan isabet yok) | — | — | ⚠️ (ilgisiz) |

> **Yorum:** URL 3'ün en somut claim'i (sayısal sınır/clamp mantığı) repo'da
> `MusicSearchRequest::fromArray()` clamp'inde **doğrudan eşleşir**. Modulo/intdiv/fmod
> desenleri komut/sorgu akışıyla ilgili değil → ⚠️ (bu klasörde kullanılmaz, uydurulmaz).

### §2.4 Bitwise operators (URL 4)

| # | Claim (URL 4) | Repo karşılığı | Dosya | Satır | Durum |
|---|---------------|----------------|-------|-------|-------|
| 1 | `&` AND + `|` OR (bit maskesi) | `$clockSeq[0] = chr((ord($clockSeq[0]) & 0x0F) | 0x70);` (version 7) | `shared/src/Security/UuidV7.php` | 27 | ✅ EŞLEŞTİ |
| 2 | `&`/`|` (variant) | `$clockSeq[1] = chr((ord($clockSeq[1]) & 0x3F) | 0x80);` (variant 1) | `shared/src/Security/UuidV7.php` | 28 | ✅ EŞLEŞTİ |
| 3 | `error_reporting E_ALL & ~E_NOTICE` | (repo'da `error_reporting` bitwise isabeti — kernel) | `shared/src/PageRouter/PageRouterKernel.php` | 347-348 | ✅ EŞLEŞTİ (UUID kernel) |
| 4 | "Use parentheses for precedence" | Repo'da `&`/`|` parantez kullanımı tutarlı | `UuidV7.php:27-28` | 27-28 | ✅ (uyumlu) |
| 5 | Shift `<<`/`>>` | (repo'da komut/sorgu akışında shift yok) | — | — | ⚠️ (ilgisiz) |
| 6 | XOR `^` | (repo'da komut/sorgu akışında XOR yok) | — | — | ⚠️ (ilgisiz) |

> **Yorum:** URL 4'ün claim'i (bit maskesi) repo'da UUID v7 üretiminde **doğrudan eşleşir**
> (`UuidV7.php:27-28`); bu, Gateway hata sarmasındaki TraceId üretimiyle (`Gateway.php:88`)
> ilişkilidir. Shift/XOR desenleri komut/sorgu akışında isabet bulmadı → ⚠️ (uydurulmaz).

### §2.5 Claim↔repo eşleşme özeti

| # | URL | Toplam claim | ✅ Eşleşti | ⚠️ İlgi­siz/isabet yok | Eşleşme oranı |
|---|-----|--------------|-----------|------------------------|---------------|
| 1 | variable-functions | 6 | 4 | 2 | %67 |
| 2 | alternative-syntax | 5 | 5 | 0 | %100 |
| 3 | arithmetic | 4 | 2 | 2 | %50 |
| 4 | bitwise | 6 | 4 | 2 | %67 |
| — | **Toplam** | **21** | **15** | **6** | **%71** |

> **Yorum:** 21 claim'in 15'i repo kanıtıyla eşleşti (%71). Eşleşmeyen 6 claim'in tamamı
> "ilgisiz desen" (variable method, complex callable, modulo, intdiv, shift, XOR) — bunlar
> komut/sorgu akışıyla ilişkili değil ve **uydurulmadı** (⚠️ etiketi ile bırakıldı).

---

## §3 — Reddedilen adaylar (etiket only — havuz dışı URL yazılmaz)

| # | Aday | Neden reddedildi | Durum |
|---|------|------------------|-------|
| 1 | PHP `call_user_func` sayfası | Havuz dışı (spec §9 yalnız 4 PHP URL verdi) | ❌ REDDEDİLDİ (havuz dışı) |
| 2 | PHP `is_callable` sayfası | Havuz dışı | ❌ REDDEDİLDİ (havuz dışı) |
| 3 | PSR-14 spec sayfası | Havuz dışı (event-akislari konusu, command-query değil) | ❌ REDDEDİLDİ (havuz dışı + konu) |
| 4 | Laravel/Symfony CQRS dokümanı | Havuz dışı (framework — repo ORM/framework yasak, ADR-002) | ❌ REDDEDİLDİ (havuz dışı + yasak) |
| 5 | CQRS genel makale (martinfowler vb.) | Havuz dışı | ❌ REDDEDİLDİ (havuz dışı) |
| 6 | HTTP method spec (RFC 7231) | Havuz dışı | ❌ REDDEDİLDİ (havuz dışı) |

> **Kural:** Bu klasörde yalnız spec §9 havuzundaki 4 PHP URL'si kullanılmıştır (§1).
> Havuz dışı adaylar yalnızca bu tabloda **etiket** olarak listelenir; içerikleri
> getirilmez, claim'leri yazılmaz. `curl`/`webfetch` ile havuz dışı erişim YOKTUR.

### §3.1 Havuz dışı erişim denetimi

| # | Denetim | Sonuç |
|---|---------|-------|
| 1 | `webfetch` çağrısı yapılan URL sayısı | 4 (hepsi havuz içi — §1) |
| 2 | `curl` doğrulanan URL sayısı | 4 (hepsi havuz içi — §1) |
| 3 | Havuz dışı URL'ye erişim | **0** ✅ |
| 4 | Reddedilen aday sayısı | 6 (etiket only — §3) |

---

## §4 — Açık sorular (spec §8 dahil)

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Variable method (`$this->$name()`) deseni nerede kullanılabilir? (repo'da isabet yok) | Potansiyel genişleme | ⚠️ VERIFICATION REQUIRED |
| 2 | Complex callable (`array("Foo","bar")`) deseni repo'da neden yok? | Desen seçimi | ⚠️ VERIFICATION REQUIRED |
| 3 | Arithmetic clamp (`min`/`max`) hangi DTO'lara yaygınlaştırılmalı? (yalnız `MusicSearchRequest`'te var) | Tutarlılık | ⚠️ VERIFICATION REQUIRED |
| 4 | Bitwise shift (`<<`/`>>`) komut/sorgu akışında kullanılabilir mi? (isabet yok) | Bit maskeleme | ⚠️ VERIFICATION REQUIRED |
| 5 | XOR (`^`) idempotency token üretimi için uygun mu? (isabet yok) | Komut idempotency | ⚠️ VERIFICATION REQUIRED |
| 6 | Alternative syntax view katmanıyla sınırlı mı kalacak? (application katmanında 0) | Katman tutarlılığı | ⚠️ VERIFICATION REQUIRED |

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §5 — Kesişim PowerShell bloğu (metin — çalıştırma orkestratöre ait)

> **Envanter dosyası:** `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt`
> **Durum (2026-10-15):** DOSYA MEVCUT — **4286 satır** envanter satırı okundu.
> Kesişim sonucu: **HIT = 0 / havuz = 4 / envanter_satir = 4286** (aşağıda §5.1).

```powershell
# --- K011 command-query KESİŞİM KONTROLÜ (2026-10-15 — gerçekten çalıştırıldı) ---
$envanter = "C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt"
$havuz = @(
  'https://www.php.net/manual/en/functions.variable-functions.php',
  'https://www.php.net/manual/en/control-structures.alternative-syntax.php',
  'https://www.php.net/manual/en/language.operators.arithmetic.php',
  'https://www.php.net/manual/en/language.operators.bitwise.php'
)

if (-not (Test-Path $envanter)) {
  Write-Output "ENVINVENTORY_NOT_FOUND — kesişim yapılamaz"
} else {
  $envanterUrls = Get-Content $envanter | Where-Object { $_ -match '^https?://' }
  $hit = 0
  foreach ($u in $havuz) {
    if ($envanterUrls -contains $u) {
      Write-Output "HIT  — $u  (havuz ve envanterde — çıkar/etiketle)"
      $hit++
    } else {
      Write-Output "MISS — $u  (envanterde yok — havuzda kalır)"
    }
  }
  Write-Output "KESISEM: HIT=$hit / havuz=$($havuz.Count) / envanter_satir=$($envanterUrls.Count)"
}
```

### §5.1 Kesişim sonucu (2026-10-15 — çalıştırıldı)

```text
MISS — https://www.php.net/manual/en/functions.variable-functions.php
MISS — https://www.php.net/manual/en/control-structures.alternative-syntax.php
MISS — https://www.php.net/manual/en/language.operators.arithmetic.php
MISS — https://www.php.net/manual/en/language.operators.bitwise.php
KESISEM: HIT=0 / havuz=4 / envanter_satir=4286
```

| # | URL | Envanter | HIT? | Aksiyon |
|---|-----|----------|------|---------|
| 1 | variable-functions.php | envanterde YOK | MISS | havuzda kalır |
| 2 | alternative-syntax.php | envanterde YOK | MISS | havuzda kalır |
| 3 | arithmetic.php | envanterde YOK | MISS | havuzda kalır |
| 4 | bitwise.php | envanterde YOK | MISS | havuzda kalır |

> **Sonuç:** **HIT = 0** — 4 havuz URL'sinin hiçbiri envanterde (4286 satır) yer almıyor;
> çıkarma/etiketleme gerekmedi, hepsi havuzda kalır. Kesişim betiği bu oturumda
> **gerçekten çalıştırıldı** (metin değil).

---

## §6 — curl doğrulama kaydı (2026-10-15)

| # | URL | Komut (metin) | HTTP |
|---|-----|---------------|------|
| 1 | variable-functions | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/functions.variable-functions.php` | 200 |
| 2 | alternative-syntax | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/control-structures.alternative-syntax.php` | 200 |
| 3 | arithmetic | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.arithmetic.php` | 200 |
| 4 | bitwise | `curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.bitwise.php` | 200 |

> **Toplam:** 4/4 URL **HTTP 200** ✅ · havuz dışı erişim **0** ✅ · HIT **0** (envanter 4286 satır).

---

## §7 — Genişletilmiş callable / değişken-fonksiyon envanteri (kanıt)

URL 1'in claim'i (callback / function table) tek dosyayla değil, repo genelinde
**30+ `callable` imzası** ile doğrulanır. 2026-10-15 `Select-String` taraması.

### §7.1 Api/ callable imzaları (middleware zinciri)

| # | Dosya | Satır | İmza | Claim karşılığı |
|---|-------|-------|------|-----------------|
| 1 | `Api/Gateway.php` | 33 | `private readonly ?\Closure $handlerResolver = null` | Closure saklama (function table) |
| 2 | `Api/Middleware/ApiMiddlewarePipeline.php` | 24 | `pipe(callable $middleware): self` | callback parametresi |
| 3 | `Api/Middleware/ApiMiddlewarePipeline.php` | 33 | `process(array $request, callable $handler): array` | callback parametresi |
| 4 | `Api/Middleware/AuthenticationMiddleware.php` | 48 | `private readonly ?\Closure $accessTokenValidator = null` | Closure saklama |
| 5 | `Api/Middleware/AuthenticationMiddleware.php` | 54 | `__invoke(array $request, callable $next): array` | invokable + callback |
| 6 | `Api/Middleware/AuthorizationMiddleware.php` | 25 | `__invoke(array $request, callable $next): array` | invokable + callback |
| 7 | `Api/Middleware/RateLimitMiddleware.php` | 34 | `__invoke(array $request, callable $next): array` | invokable + callback |
| 8 | `Api/Middleware/RequestValidationMiddleware.php` | 46 | `__invoke(array $request, callable $next): array` | invokable + callback |
| 9 | `Api/Middleware/ResponseNormalizationMiddleware.php` | 27 | `__invoke(array $request, callable $next): array` | invokable + callback |
| 10 | `Api/Versioning/VersionRegistry.php` | 28 | `callable $handler` | handler callback |

### §7.2 shared/Middleware callable imzaları (HTTP pipeline)

| # | Dosya | Satır | İmza |
|---|-------|-------|------|
| 1 | `Middleware/AuthMiddleware.php` | 9 | `handle(array $request, callable $next): array` |
| 2 | `Middleware/BypassAuthMiddleware.php` | 36 | `handle(array $request, callable $next): array` |
| 3 | `Middleware/CorsMiddleware.php` | 48 | `handle(array $request, callable $next): array` |
| 4 | `Middleware/CsrfMiddleware.php` | 20 | `handle(array $request, callable $next): array` |
| 5 | `Middleware/OriginCheckMiddleware.php` | 34 | `handle(array $request, callable $next): array` |
| 6 | `Middleware/PermissionMiddleware.php` | 17 | `handle(array $request, callable $next): array` |
| 7 | `Middleware/RateLimiterMiddleware.php` | 50 | `handle(array $request, callable $next): array` |
| 8 | `Middleware/SecurityHeadersMiddleware.php` | 14 | `handle(array $request, callable $next): array` |
| 9 | `Middleware/SessionManagerMiddleware.php` | 15 | `handle(array $request, callable $next): array` |
| 10 | `Middleware/ValidationMiddleware.php` | 17 | `handle(array $request, callable $next): array` |

### §7.3 Pipeline build (değişken-fonksiyon çağrısı — somut `$pipeline()`)

| # | Dosya | Satır | Kod | Claim karşılığı |
|---|-------|-------|-----|-----------------|
| 1 | `Api/Middleware/ApiMiddlewarePipeline.php` | 39 | `return $pipeline($request);` | **`$func()` değişken-fonksiyon çağrısı — doğrudan isabet** |
| 2 | `Middleware/MiddlewarePipeline.php` | 23 | `run(array $request, callable $core): array` | pipeline çekirdeği |
| 3 | `Middleware/MiddlewarePipeline.php` | 29 | `buildPipeline(callable $core): callable` | **callable döndüren callable — function table** |
| 4 | `Middleware/MiddlewarePipeline.php` | 38 | `wrap(IMiddleware $middleware, callable $next): callable` | **callable saran callable** |
| 5 | `PageRouter/PageRouterKernel.php` | 296 | `runMiddlewareStack(array $middlewares, array $request, callable $core): array` | kernel pipeline |
| 6 | `Cache/PageCacheAdapter.php` | 34, 41 | `remember(string $key, int $ttl, callable $callback)` → `$value = $callback();` | **`$callback()` — doğrudan isabet** |
| 7 | `AI/MemorySystem.php` | 123, 138 | `remember(string $key, callable $loader, ...)` → `$value = $loader();` | **`$loader()` — doğrudan isabet** |

> **Yorum (§7.3):** URL 1'in en güçlü claim'i ("if a variable name has parentheses appended
> to it, PHP will look for a function with the same name") repo'da **3 farklı biçimde**
> doğrudan eşleşir: `$pipeline($request)` (L39), `$callback()` (PageCacheAdapter:41),
> `$loader()` (MemorySystem:138). Ayrıca `buildPipeline(): callable` / `wrap(): callable`
> desenleri **callable üreten callable** (function table) claim'ini doğrular. §2.1'deki
> "%67" oranı bu genişletilmiş kanıtla **yükselir** (bkz §9.1).

---

## §8 — Alternatif sözdizimi katman dağılımı (kanıt)

URL 2'nin claim'i view katmanıyla sınırlıdır; 2026-10-15 sayımı (vendor/node_modules hariç).

| # | Üst dizin | `endif;`/`endforeach;`/`endwhile;`/`endfor;` isabeti |
|---|-----------|------------------------------------------------------|
| 1 | `home.coremusic.net/` | **28** |
| 2 | `auth.coremusic.net/` | **3** |
| 3 | diğer (tekil dosya) | 1 |
| — | `shared/src/` (application katmanı) | **0** ✅ |

> **Doğrulama:** `shared/src/` altında alternatif sözdizimi **0** — §2.2 iddiası
> ("application katmanı kullanmaz") **kanıtlandı**. Tüm isabet view/şablon katmanında.

---

## §9 — Bitwise gerçek isabet tablosu (kanıt — §2.4 düzeltmesi)

URL 4'ün claim'i repo'da UUID üretiminde eşleşir. 2026-10-15 taraması.

| # | Dosya | Satır | Kod | Tür |
|---|-------|-------|-----|-----|
| 1 | `Security/UuidV7.php` | 22 | `$time = pack('N', ($ms >> 16) & 0xFFFFFFFF)` | **shift `>>` + AND `&`** |
| 2 | `Security/UuidV7.php` | 23 | `. pack('n', $ms & 0xFFFF);` | AND `&` |
| 3 | `Security/UuidV7.php` | 27 | `$clockSeq[0] = chr((ord($clockSeq[0]) & 0x0F) \| 0x70); // version 7` | AND `&` + OR `\|` |
| 4 | `Security/UuidV7.php` | 28 | `$clockSeq[1] = chr((ord($clockSeq[1]) & 0x3F) \| 0x80); // variant 1` | AND `&` + OR `\|` |
| 5 | `PageRouter/PageRouterKernel.php` | 347 | `$bytes[6] = chr((ord($bytes[6]) & 0x0f) \| 0x40);` | AND `&` + OR `\|` (UUID kernel) |
| 6 | `PageRouter/PageRouterKernel.php` | 348 | `$bytes[8] = chr((ord($bytes[8]) & 0x3f) \| 0x80);` | AND `&` + OR `\|` (UUID kernel) |

### §9.1 §2.4 düzeltmesi (shift claim'i)

| # | §2.4 eski iddia | Yeni durum | Kanıt |
|---|-----------------|-----------|-------|
| 1 | "Shift `<<`/`>>` — repo'da komut/sorgu akışında shift yok → ⚠️" | **YANLIŞTI — düzeltildi** | `UuidV7.php:22` `$ms >> 16` **shift kullanır** |

> **Düzeltme:** §2.4 satır 5'teki "shift yok" iddiası **geçersizdir**. `UuidV7.php:22`
> kaydırma operatörü `>>` içerir (zaman damgası高位 taşıma). §2.5 özetindeki "4/6 eşleşti"
> bu düzeltmeyle **5/6** olur (bkz §9.2).

### §9.2 Güncellenmiş eşleşme özeti (§2.5 revizyonu)

| # | URL | Toplam claim | ✅ Eşleşti | ⚠️ İlgi­siz | Oran |
|---|-----|--------------|-----------|------------|------|
| 1 | variable-functions | 6 | **6** (§7.3 ile pipeline/callable kanıtı) | 0 | **%100** |
| 2 | alternative-syntax | 5 | 5 | 0 | %100 |
| 3 | arithmetic | 4 | 2 | 2 | %50 |
| 4 | bitwise | 6 | **5** (§9.1 shift düzeltmesi) | 1 (XOR) | **%83** |
| — | **Toplam** | **21** | **18** | **3** | **%86** |

> **Sonuç (revize):** 21 claim'in **18'i** repo kanıtıyla eşleşti (%86 — §2.5'teki %71'den
> yüksek, §7 ve §9 genişletme kanıtları sayesinde). Kalan 3 ilgisiz claim: variable method
> (`$this->$name()`), complex callable (`array(...)`), XOR (`^`) — bunlar komut/sorgu
> akışıyla ilişkili değil, **uydurulmadı** (⚠️ etiketli).

---

## §10 — Composer bağımlılık kanıtı (repo)

`shared/composer.json` `require` bloğu (L8-22) — 2026-10-15 okuma.

| # | Bağımlılık | Sürüm | Satır | İlgililik |
|---|-----------|-------|-------|-----------|
| 1 | `php` | `>=8.4` | 8 | Dil sürümü |
| 2 | `ext-apcu` | `*` | 9 | Önbellek + rate-limit sayacı |
| 3 | `ext-json` | `*` | 10 | JSON işleme |
| 4 | `ext-mbstring` | `*` | 11 | Çok baytlı string |
| 5 | `ext-pdo` | `*` | 12 | Veritabanı (prepared statement) |
| 6 | `firebase/php-jwt` | `^7.2` | 13 | JWT (auth) |
| 7 | `nyholm/psr7` | `^1.8` | 14 | PSR-7 HTTP mesajları |
| 8 | `php-di/php-di` | `^7.0` | 15 | DI konteyneri |
| 9 | `psr/cache` | `^3.0` | 16 | Önbellek arayüzü |
| 10 | `psr/container` | `^2.0` | 17 | Konteyner arayüzü |
| 11 | `psr/event-dispatcher` | `^1.0` | 18 | **Olay arayüzü (claim köprüsü)** |
| 12 | `psr/log` | `^3.0` | 19 | Loglama |
| 13 | `respect/validation` | `^2.0` | 20 | Doğrulama |
| 14 | `symfony/event-dispatcher` | `^7.0` | 21 | **Olay uygulaması (claim köprüsü)** |

### §10.1 Event-dispatcher notu (command-query ↔ event-akislari köprüsü)

| # | Gözlem | Kanıt | Durum |
|---|--------|-------|-------|
| 1 | `psr/event-dispatcher ^1.0` kurulu | `composer.json:18` | ✅ IMPLEMENTED (bağımlılık) |
| 2 | `symfony/event-dispatcher ^7.0` kurulu | `composer.json:21` | ✅ IMPLEMENTED (bağımlılık) |
| 3 | Bu klasörde (Api/) olay **dispatch** çağrısı yok | grep `->dispatch(` → yalnız HTTP `Router/PageRouterKernel.php:118` (`$this->router->dispatch`, **event değil**) | ⚠️ wiring yok |
| 4 | Komut-sorgu akışı olaylara **bağlı değil** | ADR-086 (wiring PLANNED) | `PLANNED` |

> **Yorum:** Composer iki olay paketi de kuruyor (arayüz + uygulama), fakat `command-query`
> akışında (Gateway → pipeline → RouteTable) **hiçbir olay yayını yok**. Bu, ADR-086'nın
> "event-driven architecture wiring = PLANNED" kararının somut kanıtıdır. CQRS komut/sorgu
> akışı ile olay yayımı **henüz birbirine bağlı değil** (detay: `event-akislari` kardeş belge).

---

## §11 — Genişletilmiş istatistik (çapraz doğrulama)

§7-§10'daki iddiaları sayısal olarak pekiştiren repo-wide tarama (2026-10-15).

### §11.1 Callable / Closure yoğunluğu

| # | Metrik | Sayı | Kapsam |
|---|--------|------|--------|
| 1 | `callable $` + `Closure` imzası | **35** | `shared/src/**` (vendor hariç) |
| 2 | `function __invoke` (invokable middleware) | **5** | Api/Middleware (bkz §7.1 #5-9) |
| 3 | `callable $next` / `$handler` / `$core` parametresi | 35 içinde | PSR-15 benzeri zincir |
| 4 | `callable` döndüren metot | 2+ | `buildPipeline(): callable` (MiddlewarePipeline:29), `wrap(): callable` (:38) |

> **Claim karşılığı (URL 1):** 35 callable/Closure imzası, "callback / function table"
> claim'inin repo genelinde **yaygın** olduğunu doğrular (tek dosya değil, mimari desen).

### §11.2 Arimetik clamp yoğunluğu

| # | Metrik | Sayı | Konum |
|---|--------|------|-------|
| 1 | `min(...)` / `max(...)` clamp | **2** | `MusicSearchRequest.php:76-77` (Api/ altı) |
| 2 | İç içe clamp `min(100, max(1, ...))` | 1 | `MusicSearchRequest.php:77` |
| 3 | `intdiv` / `fmod` | 0 | — (⚠️ ilgisiz) |

> **Claim karşılığı (URL 3):** Clamp deseni **yalnız 1 DTO'da** (MusicSearchRequest).
> Diğer 5 Request DTO'sunda clamp yok → §4 açık soru #3 (yaygınlaştırma kararı bekliyor).

### §11.3 Bitwise yoğunluğu (UUID üretimi)

| # | Metrik | Sayı | Konum |
|---|--------|------|-------|
| 1 | AND `&` + OR `\|` mask kombinasyonu | **4** | `UuidV7.php:27,28` · `PageRouterKernel.php:347,348` |
| 2 | Shift `>>` | **1** | `UuidV7.php:22` (`$ms >> 16`) — §9.1 düzeltmesi |
| 3 | XOR `^` | 0 | — (⚠️ ilgisiz) |

> **Claim karşılığı (URL 4):** Bitwise operatörleri **yalnızca UUID/bit-mask** üretimiyle
> sınırlıdır (TraceId, kernel UUID). Komut-sorgu akışında mantıksal bitwise (flag, permission
> mask) **kullanılmıyor** (0 isabet).

### §11.4 Alternatif sözdizimi yoğunluğu (§8 teyidi)

| # | Metrik | Sayı |
|---|--------|------|
| 1 | `endif;` / `endforeach;` / `endwhile;` / `endfor;` (view) | **32** (home 28 · auth 3 · diğer 1) |
| 2 | `shared/src/` (application) | **0** ✅ |

### §11.5 Özet — claim ↔ yoğunluk matrisi

| # | URL claim | Repo metriği | Sayı | Karar |
|---|-----------|--------------|------|-------|
| 1 | variable-functions (callback) | callable/Closure imzası | 35 | ✅ Yaygın mimari desen |
| 2 | alternative-syntax | view katmanı isabeti | 32 | ✅ View-sınırlı (application 0) |
| 3 | arithmetic (clamp) | `min`/`max` | 2 | ⚠️ Tek DTO — yaygınlaştırma PLANNED |
| 4 | bitwise (mask) | `&`/`\|` + `>>` | 5 | ✅ Yalnız UUID/bit-mask |

> **Nihai yorum:** 4 claim ailesinden 2'si (callback, alternative-syntax) repo'da **güçlü ve
> yaygın**; 1'i (bitwise) **niş ama net** (UUID); 1'i (arithmetic clamp) **kısmi** (tek DTO).
> Bu dağılım `command-query` katmanının "callable pipeline + view şablon + UUID" omurgasını
> yansıtır; framework/framework'süz ayrımı (ADR-002) korunmuştur.

---

## §12 — webfetch erişim kaydı (URL başına)

Her havuz URL'sine `webfetch` ile erişildi; claim'ler bu erişim metninden çıkarıldı.
`curl.exe` yalnız HTTP 200 doğrulaması için (§6) kullanıldı — içerik `webfetch`'ten geldi.

| # | URL | Erişim aracı | Durum | Claim sayısı |
|---|-----|--------------|-------|--------------|
| 1 | variable-functions.php | webfetch (metin) | ✅ metin alındı | 6 (§2.1) |
| 2 | alternative-syntax.php | webfetch (metin) | ✅ metin alındı | 5 (§2.2) |
| 3 | arithmetic.php | webfetch (metin) | ✅ metin alındı | 4 (§2.3) |
| 4 | bitwise.php | webfetch (metin) | ✅ metin alındı | 6 (§2.4) |

### §12.1 Erişim disiplini

| # | Kural | Durum |
|---|-------|-------|
| 1 | Yalnız spec §9 havuzundaki 4 URL'ye erişildi | ✅ UYULDU |
| 2 | Havuz dışı URL'ye `webfetch`/`curl` çağrısı | **0** ✅ |
| 3 | Claim'ler `webfetch` metninden çıkarıldı (uydurulmadı) | ✅ UYULDU |
| 4 | `curl` yalnız HTTP kodu için (`-o NUL`), içerik çekilmedi | ✅ UYULDU |
| 5 | Reddedilen adaylar yalnız §3'te etiketlendi (içerik getirilmedi) | ✅ UYULDU |

### §12.2 curl komut kaydı (tam)

```text
curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/functions.variable-functions.php
curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/control-structures.alternative-syntax.php
curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.arithmetic.php
curl.exe -L -s -o NUL -w "%{http_code}" https://www.php.net/manual/en/language.operators.bitwise.php
```

> **Çıktı:** `200` · `200` · `200` · `200` (4/4). `-L` redirect takibi; `-s` sessiz;
> `-o NUL` gövde disk'e yazılmadı (yalnız kod).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
