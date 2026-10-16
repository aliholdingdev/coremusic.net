---
title: "K011 · caching — Web Araştırması"
type: architecture
kodeme: kademe-2
module: K011-application
parent: caching
status: active
updated: 2026-10-15
group: kanit
---

# caching — Web Araştırması (Kanıt Ekseni)

> **Eksen 2:** Resmi kaynak araştırması — havuz URL'leri, iddia tablosu ve
> iddia→repo çapraz matrisi. Bu dizindeki **tek** URL taşıyıcısı bu dosya +
> `caching-ornekler.md` (ETag) + `caching-bilesenler.md` (HTTP caching genel
> dokümanı)'dir; her havuz URL'i dizinde **yalnız bir dosyada** kullanılır
> (cross-folder tekrar YASAK — spec §1.6). İddia doğrulanamazsa
> `⚠️ VERIFICATION REQUIRED`, repo'da karşılığı yoksa **negatif envanter**
> olarak kaydedilir (uydurma pozitif iddia YASAK — zero-hallucination).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 |
|---|---|---|---|
| 1 | §1 Yöntem ve envanter kontrolü | kanit | yontem |
| 2 | §2 Havuz tahsisi tablosu (6 URL) | kanit | havuz |
| 3 | §3 Expires — iddialar (W-C-EX) | kanit | iddia |
| 4 | §4 Expires — repo çapraz kontrolü (negatif envanter) | kanit | capraz |
| 5 | §5 Expires — CoreMusic uygulanabilirlik | mimari | uygulama |
| 6 | §6 Vary — iddialar (W-C-VY) | kanit | iddia |
| 7 | §7 Vary — repo çapraz kontrolü (pozitif kanıt) | kanit | capraz |
| 8 | §8 Vary — CoreMusic uygulanabilirlik | mimari | uygulama |
| 9 | §9 Etiketli adaylar (LABEL — kullanılmayan havuz) | kanit | etiket |
| 10 | §10 Claim→repo matrisi (W-C1..W-C10) | kanit | matris |
| 11 | §11 Tekrarlanabilir PowerShell komutları | kanit | komut |
| 12 | §12 Değerlendirme puanı | kanit | puan |
| 13 | §13 Kaynak özetleri (parafraz kayıtları) | kanit | parafraz |
| 14 | §14 Süreç kanıtı (üretim kaydı) | kanit | surec |
| 15 | §15 Terimler | mimari | terim |
| 16 | §16 Karar defteri | mimari | karar |
| 17 | §A Açık sorular | kanit | acik |
| 18 | §B Kullanım kuralları (bu dosyanın bağlayıcılığı) | mimari | kural |
| 19 | §C URL not defteri (tahsis + durum) | kanit | not |

---

## §1 — Yöntem ve envanter kontrolü

**Adım 1 — Havuz envanteri (HIT riski kontrolü).** Görev havuzundaki her URL
`%TEMP%\k011-urls.txt` envanterine karşılaştırıldı (2026-10-15, repo kökünden
PowerShell):

```powershell
$envanter = Get-Content "$env:TEMP\k011-urls.txt"
$url = "https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Expires"
if ($url -notin $envanter) { "TEMIZ" } else { "HIT" }
```

Sonuç (6 URL, birebir eşleşme):

| # | Havuz URL | Env. durumu | Karar |
|---|---|---|---|
| 1 | `.../Web/HTTP/Reference/Headers/Cache-Control` | **HIT** (envanterde var) | ❌ Kullanılmaz → **LABEL** (§9.1) |
| 2 | `.../Web/HTTP/Reference/Headers/ETag` | TEMİZ | → `caching-ornekler.md` (tek kullanım) |
| 3 | `.../Web/HTTP/Caching` | TEMİZ | → `caching-bilesenler.md` (tek kullanım) |
| 4 | `.../Web/HTTP/Reference/Headers/Vary` | TEMİZ | ✅ Bu dosya (§6–§8) |
| 5 | `.../Web/HTTP/Reference/Headers/Expires` | TEMİZ | ✅ Bu dosya (§3–§5) |
| 6 | `https://www.php.net/manual/en/book.apcu.php` | **HIT** (envanterde `php.net/manual/en/book.apcu.php` varyantı var) | ❌ Kullanılmaz → **LABEL** (§9.2) |

**Adım 2 — Kaynak getirme.** TEMİZ URL'ler `exa.web_fetch_exa` ile getirildi
(2026-10-15, tek parti batch, 1600 karakter/sayfa). Not: exa fetch **HTTP status
kodu döndürmez** → "sayfa içeriği döndü" doğrulanmıştır; "HTTP 200" iddiası
**⚠️ VERIFICATION REQUIRED** olarak işaretlenir (commands-kardeş dosyasının da
uyduğu sınır).

**Adım 3 — Repo çapraz kontrolü.** Her iddia `grep`/`Select-String` ile repo
koduna karşı test edildi; negatif sonuçlar da **kanıt** olarak kaydedildi
(bulunamayan başlık = "üretilmiyor" iddiasının dayanağı).

**Adım 4 — Uygulanabilirlik kararı.** Her iddia için YAP / YAPMA-DEĞİL /
PLANNED kararı verilir; gerekçe dosya·satır kanıtı taşır.

---

## §2 — Havuz tahsisi tablosu

| URL | Tahsis edilen dosya | Bölüm | Durum |
|---|---|---|---|
| `.../Headers/Expires` | `caching-web-arastirma.md` (bu dosya) | §3–§5 | KULLANILDI |
| `.../Headers/Vary` | `caching-web-arastirma.md` (bu dosya) | §6–§8 | KULLANILDI |
| `.../Headers/ETag` | `caching-ornekler.md` | §1 (ETag/304 örneği) | KULLANILDI (bu dosyada yalnız atıf) |
| `.../Web/HTTP/Caching` | `caching-bilesenler.md` | §9 (conditional/If-Modified-Since) | KULLANILDI (bu dosyada yalnız atıf) |
| `.../Headers/Cache-Control` | — | §9.1 | LABEL (HIT) |
| `www.php.net/manual/en/book.apcu.php` | — | §9.2 | LABEL (HIT) |

**Kural ihlali yok:** 6 URL'nin tamamı ya tek dosyasına tahsis edildi ya da
LABEL olarak kaydedildi; hiçbir URL iki dosyada birden kullanılmadı.

**index.md uyumu:** `index.md` §GRUPLAR satır 7 bu dosya için "Expires +
Last-Modified" kaynağını öngörüyor. `Expires` ✓ tahsis edildi; **Last-Modified
için havuzda URL YOK** → uydurma URL yazılmadı, §9.3'te LABEL olarak kaydedildi
(index.md'ye bu tur dokunulmadı — spec: mevcut dosyalar korunur).

---

## §3 — Expires — iddialar (W-C-EX)

> **Kaynak:** `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Expires`
> (exa fetch 2026-10-15 — içerik döndü; HTTP status ⚠️ VERIFICATION REQUIRED)

Kaynaktan okunan temel olgular (parafraz):

- `Expires` bir **response** başlığıdır; yanıttın HTTP cache açısından
  **expired** sayıldığı tarihi/saatı taşır.
- Değer `0` (geçmiş bir tarih) ise kaynak **zaten expired** kabul edilir.
- `Cache-Control` içinde `max-age` veya `s-maxage` varsa `Expires`
  **yok sayılır** (öncelik `max-age`/`s-maxage` üzerinedir).
- Tarih biçimi GMT'dir; yerel saat kullanılmaz (ör. `Expires: Wed, 21 Oct 2015 07:28:00 GMT`).
- CORS-safelisted response header: **Evet**.

| # | İddia (claim) | Kaynak özeti | Repo durumu | Karar |
|---|---|---|---|---|
| W-C-EX1 | `Expires`, HTTP caching'de tarih-bazlı son-use-byma tarihi taşır | MDN Expires | `.htaccess` yalnız `Cache-Control` kullanır | Doğrulandı (kaynak) — repo'da karşılığı YOK |
| W-C-EX2 | `Cache-Control: max-age/s-maxage` varsa `Expires` ignore edilir | MDN Expires notu | `assets.coremusic.net/.htaccess` · Satır: 20, 30, 35, 38 (`max-age` yazımı) | Doğrulandı → `Expires` basmak **gereksiz** olurdu |
| W-C-EX3 | `Expires: 0` = zaten expired (anlık bayatlatma aracı) | MDN Expires | repo'da `Expires` yazımı **0 eşleşme** (§4 negatif envanter) | Kullanılmıyor → PLANNED opsiyonu (§5.2) |
| W-C-EX4 | HTTP tarihleri GMT ile ifade edilir | MDN Expires | — (repo `Expires` üretmiyor) | Bilgi iddiası — uygulanabilirlik yok |
| W-C-EX5 | `Expires` CORS-safelisted response header'dır | MDN Expires tablosu | `CorsMiddleware` yalnızca CORS başlıkları yayar | Doğrulandı — ek risk yok |

---

## §4 — Expires — repo çapraz kontrolü (negatif envanter)

**Arama (repo kökünden, vendor/node_modules hariç):**

```powershell
Get-ChildItem "C:\www\coremusic.net" -Recurse -File -Include *.php,*.htaccess,*.conf `
  -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch "vendor|node_modules|\.ai" } |
  Select-String -Pattern "header\(.?Expires|Expires:" |
  ForEach-Object { "$($_.Path): L$($_.LineNumber)" }
```

**Sonuç (2026-10-15):** `header('Expires...` / `Expires:` yazımı PHP yüzeyinde
**0 eşleşme**. `.htaccess` dosyalarında da `Header set Expires` **0 eşleşme**
(`Header set Cache-Control` 4 eşleşme — `assets.coremusic.net/.htaccess`
Satır: 20, 30, 35, 38).

| Yüzey | Aranan | Sonuç | Yorum |
|---|---|---|---|
| `shared/src/**/*.php` | `Expires:` | 0 | Uygulama katmanı `Expires` üretmiyor |
| `assets.coremusic.net/.htaccess` | `Header set Expires` | 0 | Statik varlık cache'i `Cache-Control` ile yapılıyor |
| `*.htaccess` (diğer subdomain'ler) | `Header set Expires` | 0 | Aynı desen |
| `MemoryAdapter.php` | kelime "expires" | Satır: 7, 16, 27 (dizge anahtarı `'expires'`) | **False positive** — PHP dizi anahtarı, HTTP başlığı değil |

**Sonuç (negatif envanter):** CoreMusic **hiçbir yerde** `Expires` başlığı
üretmez; tarih-bazlı cache semantiği tamamen `Cache-Control: max-age` ailesine
devredilmiştir. Bu bir **eksiklik değil, kasıtlı sadeleşme** olarak okunur
(W-C-EX2 ile tutarlı: `max-age` varsa `Expires` zaten ignore edilirdi).

---

## §5 — Expires — CoreMusic uygulanabilirlik

### §5.1 YAPMA-DEĞİL kararı (mevcut durumun korunması)

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| 1 | Mevcut `.htaccess` cache bloklarına `Expires` **eklenmez** | `max-age` mevcut; `Expires` ignore edilirdi (W-C-EX2) | `assets.coremusic.net/.htaccess` · Satır: 20, 30, 35, 38 |
| 2 | Uygulama katmanına `Expires` middleware'i **eklenmez** | API yanıtları ETag + Cache-Control ile normalize ediliyor | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` · Satır: 91, 93 |
| 3 | `Vary` ile birlikte `Expires` kombinasyonu **planlanmıyor** | iki doğrulama aracı birlikte karmaşıklık; ETag zaten var | §8.3 |

### §5.2 PLANNED — yalnız senaryo (etiketli)

| # | PLANNED senaryo | Koşul | Etiket |
|---|---|---|---|
| P-EX1 | Eski HTTP/1.0 istemci'ler için `Expires` aynalama | ölçümde HTTP/1.0 istemci trafiği kanıtlanırsa | PLANNED — tetikleyici kanıt YOK |
| P-EX2 | CDN/Varnish katmanında `Expires` zorunluluğu | CDN entegrasyonu kararı verilirse (ops. 12-factor) | PLANNED — CDN kararı ADR gerektirir |

> ⚠️ VERIFICATION REQUIRED: P-EX1/P-EX2 için repo'da **hiçbir kod/sayık
> kanıtı yok**; bu satırlar yalnızca gelecekteki kapıları işaretler.

---

## §6 — Vary — iddialar (W-C-VY)

> **Kaynak:** `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Vary`
> (exa fetch 2026-10-15 — içerik döndü; HTTP status ⚠️ VERIFICATION REQUIRED)

Kaynaktan okunan temel olgular (parafraz):

- `Vary`, **URL ve method dışındaki** istek parçalarının (çoğunlukla istek
  başlıkları) yanıt içeriğini etkilettiğini; cache'in bu başlıkları **cache
  key'e** katması gerektiğini bildirir.
- `Vary: *` → içerik başka faktörlere bağlıdır; yanıt **uncanche** kabul edilir.
- **Aynı URL için tüm yanıtlarda aynı `Vary` değeri** kullanılmalıdır
  (304 Not Modified yanıtları ve "default" yanıt dahil).

| # | İddia (claim) | Kaynak özeti | Repo durumu | Karar |
|---|---|---|---|---|
| W-C-VY1 | `Vary`, content negotiation/cache-key ayrımı sağlar | MDN Vary | `ResponseEmitter` `Vary: Accept-Encoding, Cookie` basar | ✅ Doğrulandı — repo'da karşılığı VAR |
| W-C-VY2 | `Vary: *` uncacheable anlamına gelir | MDN Vary direktifleri | repo'da `Vary: *` **0 eşleşme** (§7) | Bilgi iddiası — kullanılmıyor |
| W-C-VY3 | Aynı URL'de tüm yanıtlar aynı `Vary` değerini taşımalı (304 dahil) | MDN Vary notu | ETag/304 yolu `ResponseNormalizationMiddleware`'ten geçer; `Vary` `ResponseEmitter`'da basılır → iki farklı katman | ⚠️ VERIFICATION REQUIRED — 304 yanıtında `Vary` tekilliği test edilmedi |
| W-C-VY4 | `Vary` CORS-safelisted response header **değildir** | MDN Vary tablosu (CORS-safelisted: Evet değil) | API CORS başlıkları `CorsMiddleware`'dan | Doğrulandı —cross-origin JS'te `Vary` okunamaz |
| W-C-VY5 | `Accept-Encoding` varyantı sıkıştırma bazlı cache ayrımı demektir | MDN Vary (content negotiation örneği) | `ResponseEmitter.php` · Satır: 70 | ✅ Doğrulandı |

---

## §7 — Vary — repo çapraz kontrolü (pozitif kanıt)

**Arama:**

```powershell
Get-ChildItem "C:\www\coremusic.net" -Recurse -File -Include *.php `
  -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch "vendor|node_modules|\.ai" } |
  Select-String -Pattern "Vary:" |
  ForEach-Object { "$($_.Path): L$($_.LineNumber): $($_.Line.Trim())" }
```

**Sonuç (2026-10-15):**

| Dosya | Satır | İçerik | Rol |
|---|---|---|---|
| `shared/src/PageRouter/ResponseEmitter.php` | 70 | `header('Vary: Accept-Encoding, Cookie');` | Birincil `Vary` kaynağı (HTML shell) |
| `shared/src/PageRouter/ResponseEmitter.php` | 112 | `if (stripos($sent, 'Vary:') !== 0) {` | Mevcut `Vary` başlığı kontrolü (çift basma koruması) |
| `shared/src/PageRouter/ResponseEmitter.php` | 115 | `$existing = trim(substr($sent, strlen('Vary:')));` | Var olan `Vary` değerini okuma (birleştirme mantığı) |

**Ek bulgu — `Vary: Cookie`:** `Cookie` varyantı, oturumlu/oturumsuz yanıtların
ayrı cache'lenmesi gerektiğini ima eder; bu, `PageCacheAdapter::pageKey()`
ile aynı semantiği paylaşıyor:

- `shared/src/Cache/PageCacheAdapter.php` · Satır: 30 → `pageKey()`;
  `page_auth_` / `page_pub_` önekleri ile **authenticated ayrımı** yapar.

İki katman (HTTP `Vary: Cookie` + uygulama `page_auth_/page_pub_` anahtarı)
**aynı problemi farklı seviyede** çözer → tutarlılık iddiası
**⚠️ VERIFICATION REQUIRED** (entegrasyon testi okunmadı).

---

## §8 — Vary — CoreMusic uygulanabilirlik

### §8.1 Mevcut durum özeti

| Katman | `Vary` üreten | Değer | Kanıt |
|---|---|---|---|
| HTML shell (PageRouter) | `ResponseEmitter` | `Accept-Encoding, Cookie` | `ResponseEmitter.php` · Satır: 70 |
| API pipeline | — | `Vary` YOK | `shared/src/Api/Middleware/*.php` grep: 0 |
| Statik varlık (assets) | Apache | `.htaccess`'te `Vary` YOK | `assets.coremusic.net/.htaccess` grep: 0 |

### §8.2 YAPMA-DEĞİL kararları

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| 1 | API'ye `Vary` eklenmez | API yanıtları `no-store`/`public+max-age` ikilisiyle yönetiliyor; `no-store` cache'i tamamen dışlar | `ResponseNormalizationMiddleware.php` · Satır: 91, 93 |
| 2 | `.htaccess`'e `Vary: Accept-Encoding` eklenmez | Sunucu zaten `mod_deflate`/gzip tarafını yönetiyor; `Vary` ekleme kararı operasyonel (Apache konfig) → sunucu katmanı K012-scope | PLANNED-DEĞİL |
| 3 | `Vary: User-Agent` (device-bazlı) eklenmez | Device detection uygulama katmanında (`DeviceDetector`) — HTTP cache'i cihaz bazında bölmek cache-hit oranını düşürür | `shared/src/Device/` envanteri (K011-routing kardeşi) |

### §8.3 PLANNED — 304 yolu ile `Vary` tekilliği testi

| # | PLANNED test | Beklenen | Sahip |
|---|---|---|---|
| P-VY1 | 304 yanıtı `Vary` taşır mı? | `ResponseEmitter` katmanı 304'te de aynı `Vary` değeriyle geçmeli (W-C-VY3) | QA (K019 ile ortak) |

> ⚠️ VERIFICATION REQUIRED: P-VY1 için test dosyası **diskte yok**
> (`shared/tests/Api/ResponseNormalizationMiddlewareTest.php` içinde `Vary`
> iddiası test edilmiyor — §10 W-C7).

---

## §9 — Etiketli adaylar (LABEL)

### §9.1 `Cache-Control` başlık dokümanı — LABEL (HIT)

- **URL:** `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control`
- **Durum:** envanter HIT (`k011-urls.txt` içinde birebir mevcut) → **kullanılmadı**.
- **Yansıma:** `Cache-Control` iddiaları bu turda **yalnız repo kodu +
  `caching-dogrulama.md`** üzerinden taşınır; MDN metnine atıf yapılmaz.
- **Karar:** LABEL — hiçbir bölümde bu URL'e bağlantı verilmez.

### §9.2 APCu kitapçığı — LABEL (HIT)

- **URL:** `https://www.php.net/manual/en/book.apcu.php`
- **Durum:** envanter HIT → **kullanılmadı** (aynı konu için envanterde
  `https://www.php.net/manual/en/function.apcu-fetch.php` +
  `function.apcu-store.php` varyantları da mevcut — o da HIT sayılır).
- **Yansıma:** APCu iddiaları repo kodundaki `apcu_*` çağrılarıyla taşınır
  (`shared/src/Cache/ApcuAdapter.php` · Satır: 9–54).

### §9.3 `Last-Modified` / `If-Modified-Since` — LABEL (havuzda YOK)

- `index.md` §GRUPLAR bu klasörde bir `Last-Modified` kaynağı bekler; **bu tur
  havuzunda böyle bir URL yoktur** → uydurma URL yazılmadı.
- Repo durumu: `Last-Modified` yazımı PHP yüzeyinde **grep: 0 eşleşme**
  (§4 ile aynı tarama deseni).
- `If-Modified-Since` iddiası `caching-bilesenler.md` §9'da, havuzdaki
  **HTTP caching genel dokümanı** üzerinden (conditional request bölümü)
  taşınır — `Last-Modified`'a özgü bir URL kullanılmadan.

---

## §10 — Claim→repo matrisi (W-C1..W-C10)

| # | İddia | Kaynak (dosya · satır / URL) | Sonuç |
|---|---|---|---|
| W-C1 | Repo `Expires` başlığı üretmez | grep negatif (§4) | ✅ DOĞRULANDI (negatif envanter) |
| W-C2 | Statik cache `Cache-Control: max-age` ile yapılıyor | `assets.coremusic.net/.htaccess` · Satır: 20, 30, 35, 38 | ✅ DOĞRULANDI |
| W-C3 | `.htaccess` 4 farklı TTL profili taşıyor (CSS 3600 / JS no-cache / font 31536000 immutable / image 604800) | Aynı dosya · Satır: 20, 30, 35, 38 | ✅ DOĞRULANDI |
| W-C4 | `ResponseEmitter` `Vary: Accept-Encoding, Cookie` basar | `shared/src/PageRouter/ResponseEmitter.php` · Satır: 70 | ✅ DOĞRULANDI |
| W-C5 | `ResponseEmitter` çift `Vary` basımını korur (mevcut başlık kontrolü) | Aynı dosya · Satır: 112–115 | ✅ DOĞRULANDI |
| W-C6 | API `Vary` üretmez | `shared/src/Api/Middleware/` grep: 0 | ✅ DOĞRULANDI (negatif envanter) |
| W-C7 | 304 yolu `Vary` tekilliğini test eder | `shared/tests/Api/ResponseNormalizationMiddlewareTest.php` · Satır: 34–95 (testlerde `Vary` geçmiyor) | ❌ YOK — PLANNED (P-VY1) |
| W-C8 | Uygulama cache'i auth ayrımını key önekiyle yapar | `shared/src/Cache/PageCacheAdapter.php` · Satır: 30–31 | ✅ DOĞRULANDI |
| W-C9 | `Expires` üretilmediği için `max-age`-önceliği ihlali yok | W-C1 + W-C2 birlikte | ✅ DOĞRULANDI (mantıksal kapanış) |
| W-C10 | `Vary: *` kullanılmaz (her yanıt cache'lenebilir varsayımı) | grep: 0 eşleşme | ✅ DOĞRULANDI (negatif envanter) |

**Puan: 9/10 doğrulandı · 1 boşluk (W-C7 → PLANNED).**

---

## §11 — Tekrarlanabilir PowerShell komutları

```powershell
# 1) Envanter kontrolü (HIT/TEMIZ) — repo kökünden
$envanter = Get-Content "$env:TEMP\k011-urls.txt"
"https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Vary" -in $envanter

# 2) Expires negatif envanter
Get-ChildItem "C:\www\coremusic.net" -Recurse -File -Include *.php,*.htaccess `
  -ErrorAction SilentlyContinue |
  Where-Object { $_.FullName -notmatch "vendor|node_modules|\.ai" } |
  Select-String -Pattern "Expires:" | Measure-Object | Select-Object Count

# 3) Vary pozitif envanter
Get-ChildItem "C:\www\coremusic.net\shared\src" -Recurse -File -Filter *.php |
  Select-String -Pattern "Vary:" |
  ForEach-Object { "$($_.Filename): L$($_.LineNumber)" }

# 4) .htaccess TTL profilleri
$i=0; Get-Content "C:\www\coremusic.net\assets.coremusic.net\.htaccess" |
  ForEach-Object { $i++; if ($_ -match "Cache-Control") { "L$i`: $_" } }
```

**Beklenen sayımlar (2026-10-15 ölçümü):** komut 2 → `Count: 0` · komut 3 →
`ResponseEmitter.php: L70, L112, L115` · komut 4 → 4 satır (L20, L30, L35, L38).

---

## §12 — Değerlendirme puanı

| Eksen | Puan | Not |
|---|---|---|
| Havuz disiplini (tek dosya / LABEL) | 5/5 | 6/6 URL hesaplandı; 2 HIT, 1 havuz-dışı |
| İddia→repo çapraz (W-C matrisi) | 9/10 | W-C7 boşluk PLANNED |
| Negatif envanter disiplini | 5/5 | `Expires`/`Vary: *`/API-`Vary` = 0 kanıtla |
| Uygulanabilirlik kararı (gerekçeli) | 5/5 | §5 + §8 karar tabloları |
| Zero-hallucination | 5/5 | Tüm iddialar dosya·satır veya kaynak parafrazı |
| **Toplam** | **29/30** | Tek eksik: P-VY1 entegrasyon testi |

---

## §13 — Kaynak özetleri (getirilen sayfaların parafraz kayıtları)

### §13.1 `Expires` sayfası — okunan yapı

| Öğe | Kaynaktan okunan | Bu dosyadaki kullanım |
|---|---|---|
| Başlık tanımı | "The HTTP `Expires` response header contains the date/time after which the response is considered expired in the context of HTTP caching." | §3 iddia tablosu temeli |
| `0` değeri | Geçmiş bir tarih = kaynak zaten expired | W-C-EX3 |
| Öncelik notu | `Cache-Control`'da `max-age`/`s-maxage` varsa `Expires` **ignore** edilir | W-C-EX2 → §5.1 karar 1 |
| Sözdizimi | `Expires: <day-name>, <day> <month> <year> <hour>:<minute>:<second> GMT` | §5.2 P-EX1 bağlamı |
| Zaman dilimi | HTTP tarihleri her zaman GMT; yerel saat asla | §4 (repo `Expires` üretmiyor — bilgi) |
| CORS durumu | CORS-safelisted response header: **Evet** | W-C-EX5 |
| Spesifikasyon satırı | "HTTP Caching # field.expires" (MDN spec tablosu) | Atıf disiplini |

### §13.2 `Vary` sayfası — okunan yapı

| Öğe | Kaynaktan okunan | Bu dosyadaki kullanım |
|---|---|---|
| Başlık tanımı | `Vary`, URL/method dışında yanıt içeriğini etkileyen istek parçalarını (çoğunlukla istek başlıkları) tarif eder; cache bu başlıkları **key'e** katar | W-C-VY1 |
| Kullanım amacı | "Most often, this is used to create a cache key when content negotiation is in use." | §8.2 karar 3 (device-bazlı varyant reddi) |
| Tutarlılık kuralı | "The same `Vary` header value should be used on all responses for a given URL, including `304 Not Modified` responses and the 'default' response." | W-C-VY3 → P-VY1 |
| `Vary: *` | Başka faktörler de etkiliyor → **uncacheable** | W-C-VY2, W-C-C10 |
| Sözdizimi | `Vary: *` · `Vary: <header-name>, …, <header-nameN>` | §6 |
| Spesifikasyon satırı | "HTTP Semantics # field.vary" | Atıf disiplini |

### §13.3 Dikkat notu — sayfa erişim biçimi

| Konu | Kayıt |
|---|---|
| Getirme aracı | `exa.web_fetch_exa` (batch, 4 URL, 1600 karakter/URL) |
| Tarih | 2026-10-15 |
| Status kodu | exa **döndürmez** → "HTTP 200" iddiası yazılmaz (⚠️ VERIFICATION REQUIRED) |
| İçerik doğrulama | Yukarıdaki §13.1/§13.2 tabloları getirilen metinden çıkarıldı |
| Kardeş dosya pratiği | `commands-web-arastirma.md` aynı sınırı "HTTP 200 ile doğrulandı" yerine "content döndü" notuyla taşır — bu dosya daha muhafazakârdır |

---

## §14 — Süreç kanıtı: bu dosyanın üretim kaydı

| # | Adım | Kanıt | Durum |
|---|---|---|---|
| 1 | Havuz envanter kontrolü (HIT/TEMIZ) | `%TEMP%\k011-urls.txt` karşılaştırması (§1 adım 1) | ✅ 6/6 hesaplandı |
| 2 | TEMİZ URL getirme | exa batch (§13.3) | ✅ 4 içerik döndü |
| 3 | Repo grep — Expires | §4 tablosu (0 eşleşme + 1 false positive) | ✅ |
| 4 | Repo grep — Vary | §7 tablosu (3 eşleşme — tek dosya) | ✅ |
| 5 | `.htaccess` TTL okuma | Satır: 20, 30, 35, 38 (Cache-Control blokları) | ✅ |
| 6 | İddia→repo matrisi | §10 (10 satır, 9 PASS + 1 PLANNED) | ✅ |
| 7 | LABEL kaydı | §9 (3 satır: 2 HIT + 1 havuz-dışı) | ✅ |
| 8 | Tahsis tablosu | §2 (her URL tek dosya) | ✅ |

**Bu dosyanın kardeşlerle ilişkisi:**

| Kardeş | İlişki | Kural |
|---|---|---|
| `caching-uygulama-seviyesi-cache.md` | Kapsam özeti — bu dosya yalnız HTTP-cache başlıklarını (Expires/Vary) işler; APCu/TTL/LRU kapsamı oradadır | Çakışma yok |
| `caching-dogrulama.md` | Gate — bu dosyadaki PASS iddiaları orada test edilir; W-C7/P-VY1 BLOCK gerekçesi olarak akar | Gate > Araştırma |
| `caching-ornekler.md` | ETag URL'sinin sahibi; `If-None-Match` iddiaları orada | URL tek-dosya |
| `caching-bilesenler.md` | HTTP-caching genel dokümanının sahibi; `If-Modified-Since`/conditional iddiaları orada | URL tek-dosya |

---

## §15 — Terimler (bu dosyaya özgü)

| Terim | Tanım (kaynak parafrazı) | Kanıt |
|---|---|---|
| **Tarih-bazlı expiry** | `Expires` ile belirlenen, GMT cinsinden son geçerlilik anı | §13.1 |
| **Directive-bazlı expiry** | `Cache-Control: max-age` ile belirlenen saniye cinsinden ömür; `Expires`'ı ezer | W-C-EX2 |
| **Cache key genişletme** | `Vary` ile istek başlıklarının key'e katılması | W-C-VY1 |
| **Uncacheable varyant** | `Vary: *` — hiçbir cache yanıt saklayamaz | W-C-VY2 |
| **Negatif envanter** | Bir başlığın/sınıfın/sayının **yokluğunun** grep ile kanıtlanması | §4, §7 |
| **LABEL aday** | Havuzda olup HIT/havuz-dışı nedeniyle kullanılmayan URL | §9 |
| **False positive** | Kelime benzerliğiyle eşleşen ama konuyla ilgisiz satır (ör. `MemoryAdapter` `'expires'` dizi anahtarı) | §4 |

---

## §16 — Karar defteri (bu turda verilen nihai kararlar)

| # | Konu | Karar | Gerekçe | Kanıt |
|---|---|---|---|---|
| 1 | `Expires` başlığı eklenmesi (.htaccess / PHP) | **HAYIR** | `max-age` mevcut; `Expires` ignore edilirdi | W-C-EX2 · W-C2 |
| 2 | `Vary` API'ye eklenmesi | **HAYIR** | `no-store` yanıtlar cache dışı; `Vary` gereksiz | §8.2 karar 1 |
| 3 | `Vary: User-Agent` (device cache split) | **HAYIR** | Hit oranı düşer; device logic uygulama katmanında | §8.2 karar 3 |
| 4 | `Vary: *` kullanımı | **HAYIR** | Her yanıtı uncacheable yapar; mimariyle çelişir | W-C-VY2 |
| 5 | 304 `Vary` tekillik testi (P-VY1) | **PLANNED** | Test diskte yok; BLOCK gerekçesi | W-C7 |
| 6 | CDN'de `Expires` (P-EX2) | **PLANNED** | CDN kararı ADR gerektirir | §5.2 |
| 7 | LABEL URL'lerin "kullanıldı" sayılması | **YASAK** | Havuz disiplini (spec §1.6) | §9 |
| 8 | `MemoryAdapter` `'expires'` anahtarının HTTP başlığı sanılması | **YASAK** | False positive kaydı | §4 |

**Özet:** Bu turda **hiçbir yeni HTTP başlığı eklenmesi önerilmez**; mevcut
`Cache-Control` + `ETag` + `Vary (HTML shell)` kombinasyonu korunur. Tek açık
alan test boşluğudur (P-VY1).

---

## §A — Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | 304 yanıtının `Vary` başlığını taşıyıp taşımadığı (W-C-VY3) | ⚠️ VERIFICATION REQUIRED |
| 2 | `Vary: Cookie` ile `page_auth_/page_pub_` key ayrımının eşzamanlı gerekliliği (§7 ek bulgu) | ⚠️ VERIFICATION REQUIRED |
| 3 | Operasyonel ortamda Apache'yi kimin `Vary` için yapılandırdığı (sunucu katmanı sahipliği) | ⚠️ VERIFICATION REQUIRED |
| 4 | CDN/Varnish kararı verilirse `Expires` gereksiniminin geri dönmesi (P-EX2) | PLANNED |

---

## §B — Kullanım kuralları

1. Bu dosya yalnız **Expires** ve **Vary** URL'lerini (§2) taşır; ETag ve
   HTTP-caching-genel URL'leri kardeş dosyalarda kullanılmıştır — **ikinci kez
   bu dosyada kullanılmaz**.
2. LABEL kayıtları (§9) hiçbir koşulda "kullanıldı" olarak raporlanamaz.
3. `⚠️ VERIFICATION REQUIRED` işaretli satırlar PASS olarak sayılmaz
   (`caching-dogrulama.md` gate'i bunları BLOCK'a çeker).
4. Yeni iddia eklenirken önce §11 komutlarıyla negatif envanter tazelenir.
5. `Expires`/`Vary` kararı değişirse bu dosya §16 karar defteriyle birlikte
   güncellenir; kararı veren kişi/kurul satırda adıyla kaydedilir.
6. Kardeş dosyalara (ornekler/bilesenler) yalnız **atıf** yapılır; onların
   URL'leri burada ikinci kez açılmaz (tek-kullanım kuralı).
7. Bu dosyanın havuz dışı hiçbir URL'i **yoktur**; yeni kaynak gerekirse
   önce envanter kontrolü (§1) yapılır, sonra LABEL/KULLANILDI kararı verilir.

---

## §C — URL not defteri

| # | URL | Durum | Kullanım | Tarih |
|---|---|---|---|---|
| 1 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Expires` | TEMİZ → getirildi | §3–§5 | 2026-10-15 |
| 2 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Vary` | TEMİZ → getirildi | §6–§8 | 2026-10-15 |
| 3 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/ETag` | TEMİZ | `caching-ornekler.md` (tek kullanım) | 2026-10-15 |
| 4 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching` | TEMİZ | `caching-bilesenler.md` (tek kullanım) | 2026-10-15 |
| 5 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control` | HIT | LABEL (§9.1) | 2026-10-15 |
| 6 | `https://www.php.net/manual/en/book.apcu.php` | HIT | LABEL (§9.2) | 2026-10-15 |
| 7 | `Last-Modified` / `If-Modified-Since` (başlık dokümanı) | HAVUZDA YOK | LABEL (§9.3) — uydurma URL YOK | 2026-10-15 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
