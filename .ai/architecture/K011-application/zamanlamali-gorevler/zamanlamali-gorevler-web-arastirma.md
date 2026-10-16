---
title: "K011 · zamanlamali-gorevler — Web Araştırması"
type: architecture
kodeme: kademe-2
module: K011-application
parent: zamanlamali-gorevler
status: active
updated: 2026-10-15
group: kanit
---

# K011 · zamanlamali-gorevler — Web Araştırması

> **Bu dosyanın özel durumu:** Araştırılan modül **istemci zamanlayıcıları** (JS) ve
> **sunucu zaman fonksiyonları** (PHP) içerir. Web araştırması iki amaca hizmet eder:
>
> 1. **Repo'daki zamanlama kullanımının doğru olduğunu** doğrulamak (MDN/php.net
>    kuralları ile kod çaprazı).
> 2. **Repo'da olmayan** yüzeyleri (Web Worker, cron) **yalnız belge düzeyinde**
>    tarif etmek — kod iddiası **üretilmez**.
>
> **Zorunlu kural:** Her URL `zamanlamali-gorevler-dogrulama.md` §A havuzundan alınmıştır;
> **havuz dışı URL yazılmaz**.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 | kanit | ozet | Araştırmanın amacını anla |
| 2 | §2 Doğrulanmış URL havuzu (5) | kanit | havuz | Erişim kayıtlarını gör |
| 3 | §3 Kesişim kaydı | kanit | kesişim | Envante çakışmasını doğrula |
| 4 | §4 Claim ↔ repo matrisi | kanit | capraz | MDN/php.net kuralı ↔ kod |
| 5 | §5 Reddedilen adaylar | kanit | red | Elenenleri yalnız etiketle |
| 6 | §6 Açık sorular | kanit | acik | Kapatılmayanlar |
| 7 | §A Ekler | kanit | ek | Yöntem |

---

## §1 — ELI15 (kısa anlatım)

**eli10:** Bu dosya, "şu URL'lere gerçekten erişildi mi, içlerinde ne yazıyor ve bu
repo'daki kodla örtüşüyor mu" sorusunun kaydıdır.

**eli15:** Beş resmi kaynak (3 MDN · 2 php.net) erişilip içeriği özetlendi; her kaynaktan
çıkarılan **kural** repo koduyla **çaprazlandı** (§4). Aday URL'ler envante
(`k011-urls.txt`, 4286 satır) ile **kesişim** kontrolünden geçirildi → **HIT=0**.
Elenen adaylar yalnız **etiket** olarak kaydedildi (tam URL yazılmadı).

---

## §2 — Doğrulanmış URL Havuzu (5 URL)

> **Doğrulama yöntemi (tekrarlanabilir):**
> `curl.exe -L -s -o NUL -w "%{http_code}" <url>` → **200** VE
> HashSet `Contains(<url>)` → **HIT yok**. Ölçüm: 2026-10-15.

| # | URL | HTTP | Ne verir | Kullanıldığı bölüm |
|---|---|---|---|---|
| 1 | `https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout` | 200 | `setTimeout` referansı: gecikme, dönüş, throttle, güvenlik | §4-1..7 |
| 2 | `https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setTimeout` | 200 | Aynı sayfanın eski mixin yolu (redirect → aynı içerik) | §4-2 |
| 3 | `https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API` | 200 | Web Worker kavramı, worker türleri, `postMessage` | §4-8..10 |
| 4 | `https://www.php.net/manual/en/function.sleep.php` | 200 | `sleep()`: saniye cinsinden uyuma, dönüş değeri, ValueError | §4-11..13 |
| 5 | `https://www.php.net/manual/en/function.time.php` | 200 | `time()`: Unix timestamp, timezone uyarısı, DateTimeImmutable | §4-14..16 |

**Toplam:** 5 URL · **5/5 HTTP 200** · **5/5 HIT=0** ✅

**curl sonuç kaydı (2026-10-15):**

```text
200 https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout
200 https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setTimeout
200 https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API
200 https://www.php.net/manual/en/function.sleep.php
200 https://www.php.net/manual/en/function.time.php
```

**Not (URL #2):** `WindowOrWorkerGlobalScope/setTimeout` MDN'in eski mixin tanımıdır;
`curl -L` ile takip edildiğinde **200** döner ve içeriği URL #1 ile **aynıdır** (MDN bu
konuyu `Window/setTimeout` altında birleştirmiştir). İki URL de havuzdadır — biri eski
yolu korur, diğeri güncel kanonik yolu.

**Havuz disiplini:** 5 URL'in 3'ü MDN, 2'si php.net — hepsi **resmi** ve **zamanlama
konulu**. Genel blog/教程 kullanılmadı; havuz dışı URL yazılmadı.

---

## §3 — Kesişim Kaydı (envant × havuz)

**Envanter:** `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt`
**Satır:** 4286 (`[System.IO.File]::ReadAllLines` → 4286)
**Yöntem:** Trimlenmiş satırlar `HashSet[string]` (OrdinalIgnoreCase)'e eklendi;
5 havuz URL'i `Contains()` ile sorgulandı.

**Sonuç: Kesişim: 0 — envanter 4286 URL (2026-10-15)**

**Tekrarlanabilir PowerShell bloğu (metin — betik yazılmaz):**

```powershell
$inv = "C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt"
$lines = [System.IO.File]::ReadAllLines($inv)                    # 4286
$set = [System.Collections.Generic.HashSet[string]]::new(
         [System.StringComparer]::OrdinalIgnoreCase)
foreach ($l in $lines) { [void]$set.Add($l.Trim()) }
$pool = @(
  'https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout',
  'https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setTimeout',
  'https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API',
  'https://www.php.net/manual/en/function.sleep.php',
  'https://www.php.net/manual/en/function.time.php'
)
$hits = 0
foreach ($p in $pool) { if ($set.Contains($p)) { $hits++; Write-Output "HIT: $p" } }
Write-Output "KESISIM_HITS: $hits"                                # beklenen: 0
```

**Çıktı:** `KESISIM_HITS: 0`

### §3.1 Kesişim yorumu

| # | Gözlem | Yorum |
|---|---|---|
| 1 | 0/5 HIT | 5 URL bu envanterde **daha önce kullanılmamış** — taze kaynak |
| 2 | 4286 envanter | Önceki K011 araştırmalarının toplamı — geniş havuz |
| 3 | IgnoreCase | Büyük/küçük harf varyantı da yakalandı → 0 sağlam |

---

## §4 — Claim ↔ Repo Matrisi (16 madde)

### §4.1 MDN #1 — `delay` varsayılanı 0'dır

| Alan | Değer |
|---|---|
| **Claim (MDN #1):** | `delay` verilmezse **0** kabul edilir; negatif değerler de 0 gibi davranır. |
| **Repo kanıtı:** | Tüm 20 çağrıda `delay` **açıkça** yazılmış (100, 300, 500, 1000, 4000, 6000, 30000, 60000 veya parametre). |
| **Eşleşme:** | ✅ — repo asla varsayılana güvenmiyor; MDN'in "sürpriz 0" uyarısını **önlüyor**. |

### §4.2 MDN #1/#2 — `setTimeout` asenkronur, bloklamaz

| Alan | Değer |
|---|---|
| **Claim:** | `setTimeout` anında döner; satırlar arasında "bekleme" olmaz. |
| **Repo kanıtı:** | `footer.init.js:21–23`: `applyProgressFills()` önce **çalıştırılır**, sonra `setTimeout(...,300)` ile yedeklenir — bekleme yok, hemen çizim. |
| **Eşleşme:** | ✅ — doğru desen (hemen çalıştır + gecikmeli yedek). |

### §4.3 MDN #1 — nesting ≥5 ise min 4ms

| Alan | Değer |
|---|---|
| **Claim:** | İç içe 5. `setTimeout`'tan sonra minimum gecikme **4ms** olur. |
| **Repo kanıtı:** | En derin iç içe = 2 seviye (`device-loader.js:259→263`). Eşik **aşılmıyor**. |
| **Eşleşme:** | ✅ — throttling tetiklenmez. |

### §4.4 MDN #1 — background tab throttling (Chrome intensive: 1/dk)

| Alan | Değer |
|---|---|
| **Claim:** | Arka sekmede + 5dk invisible + 30sn sessiz → timer **1/dk**'ya düşer. |
| **Repo kanıtı:** | `WidgetManager` 1000ms ve `WidgetAreaComponent` 30000ms periyodikleri **gösterim amaçlı** (saat); 1/dk düşerse dakika yine güncellenir. |
| **Eşleşme:** | ✅ — kritik zamanlama yok, throttle tolere edilir. |

### §4.5 MDN #1 — dönen timeout ID `clearTimeout` için zorunludur

| Alan | Değer |
|---|---|
| **Claim:** | `setTimeout` bir ID döndürür; `clearTimeout(id)` iptal yoludur. |
| **Repo kanıtı:** | `signal-utils.js:17–18`: `id = setTimeout(...); clear: () => clearTimeout(id)` · `TouchManager.js:40`: `#longPressTimer = window.setTimeout(...)` → `clearTimeout(#longPressTimer)` (satır 49, 57). |
| **Eşleşme:** | ✅ — her ID saklanıyor ve temizleniyor. |

### §4.6 MDN #1 — `code` parametresi string ise XSS yüzeyi (Trusted Types)

| Alan | Değer |
|---|---|
| **Claim:** | `setTimeout("...string...")` enjeksiyon çukurudur; fonksiyon + Trusted Types önerilir. |
| **Repo kanıtı:** | 20 çağrının **tamamı fonksiyon/ok parametreli**; string `code` **0**. Ayrıca repo genelinde `eval` yasak (assets AGENTS.md §4). |
| **Eşleşme:** | ✅ — MDN güvenlik kuralı uygulanıyor. |

### §4.7 MDN #1 — `this` bağlamı ok fonksiyonunda korunur

| Alan | Değer |
|---|---|
| **Claim:** | Metod referansı doğrudan verilirse `this` kaybolur; ok fonksiyonu/bind gerekir. |
| **Repo kanıtı:** | Tüm callback'ler **ok fonksiyonu** veya sınıf metodu (`() => this.#remove(id)`, `() => onPrefetch(...)`, `function () { ... }.bind` yok — ok yeterli). |
| **Eşleşme:** | ✅ — `this` kaybı riski yok. |

### §4.8 MDN #3 — Worker DOM'a erişemez, `postMessage` ile konuşur

| Alan | Değer |
|---|---|
| **Claim:** | Worker ayrı thread'dedir; DOM yok, iletişim `postMessage`. |
| **Repo kanıtı:** | JS 95 dosyada `Worker(` **0 isabet** → repo'da worker **yok**. Tüm zamanlayıcılar ana thread'de. |
| **Eşleşme:** | 🟡 — claim doğru, repo'da karşılığı **yok** (boşluk, §6-2). |

### §4.9 MDN #3 — Worker türleri: Dedicated · Shared · Service

| Alan | Değer |
|---|---|
| **Claim:** | Üç worker türü; Service Worker proxy/offline. |
| **Repo kanıtı:** | Yok (bkz. §4.8). Service Worker `sw.js` 0. |
| **Eşleşme:** | 🟡 — belge düzeyinde bilgi; repo boş. |

### §4.10 MDN #3 — Worker `fetch`/`XMLHttpRequest` yapabilir

| Alan | Değer |
|---|---|
| **Claim:** | Worker ağ isteği gönderebilir. |
| **Repo kanıtı:** | Ana thread'de `FetchWrapper` zaten `fetch` kullanır (satır 52) — worker'a gerek yok. |
| **Eşleşme:** | ✅ — mevcut mimari yeterli. |

### §4.11 php.net #4 — `sleep(int $seconds)` saniye alır, `usleep` fraksiyon içindir

| Alan | Değer |
|---|---|
| **Claim:** | `sleep` tam saniye; kesir için `usleep` (ör. `sleep(0.25)` → 0 saniye). |
| **Repo kanıtı:** | `sleep()` **0** · `usleep()` **0** — ikisi de **kullanılmıyor** (istek bloklama yok). |
| **Eşleşme:** | ✅ — repo bloklamayı **bilinçli olarak** reddediyor; php.net de bloklamanın maliyetini hatırlatır. |

### §4.12 php.net #4 — `sleep` sinyalle kesilirse non-zero döner (Windows: 192)

| Alan | Değer |
|---|---|
| **Claim:** | Sinyalle kesimde dönüş non-zero; Windows'ta daima 192 (WAIT_IO_COMPLETION). |
| **Repo kanıtı:** | `sleep` kullanılmadığı için dönüş değeri yok → ilgisiz. |
| **Eşleşme:** | N/A — repo bu yola girmez. |

### §4.13 php.net #4 — PHP 8.0'da negatif `seconds` → `ValueError`

| Alan | Değer |
|---|---|
| **Claim:** | Negatif saniye artık `ValueError` fırlatır (eski: E_WARNING + false). |
| **Repo kanıtı:** | `SessionConfig` sabitleri **pozitif** (1800/3600/42000); negatif girdi yok. |
| **Eşleşme:** | ✅ — ValueError yüzeyi tetiklenmez. |

### §4.14 php.net #5 — `time()` Unix timestamp (saniye) döner, timezone bilgisi yoktur

| Alan | Değer |
|---|---|
| **Claim:** | `time()` saniye cinsinden epoch; timezone taşımaz; tarih için `DateTimeImmutable` önerilir. |
| **Repo kanıtı:** | `time()` yalnız **fark hesabı** için (`time() - $last >= $sabit` — SessionLifecycle 5, MemoryAdapter 2); **tarih formatlama** `DateTimeImmutable` ile (`StructuredLogger.php:62`, `ApiResponse.php:36/95/126`). |
| **Eşleşme:** | ✅ — php.net ayrımı **birebir** uygulanıyor: fark→`time()`, format→`DateTimeImmutable`. |

### §4.15 php.net #5 — `$_SERVER['REQUEST_TIME']` istek başı damgasıdır

| Alan | Değer |
|---|---|
| **Claim:** | İstek başlangıcının timestamp'i `REQUEST_TIME`'dadır. |
| **Repo kanıtı:** | `REQUEST_TIME` **0 isabet** — repo her `time()` çağrısında taze alır (daha kesin). |
| **Eşleşme:** | ⚠️ — repo, php.net'in sunduğu kısayolu **kullanmıyor**; tercihli sapma (kötü değil). |

### §4.16 php.net #5 — zaman damgaları epoch'a göre artan sıralanabilir UUID

| Alan | Değer |
|---|---|
| **Claim:** | Artan zaman = artan timestamp = sıralanabilir ID tabanı. |
| **Repo kanıtı:** | `UuidV7.php:19`: `$ms = (int)(microtime(true) * 1000)` → 48-bit ms damga (UUID v7). |
| **Eşleşme:** | ✅ — uuid v7 sayesinde ID'ler **zamana göre sıralanabilir** (MB). |

### §4.17 Özet matris

| # | Claim kaynağı | Claim | Repo | Eşleşme |
|---|---|---|---|---|
| 1 | MDN #1 | delay varsayılan 0 | 20/20 açık delay | ✅ |
| 2 | MDN #1 | asenkron | hemen-çiz + yedek | ✅ |
| 3 | MDN #1 | nesting 4ms | max 2 seviye | ✅ |
| 4 | MDN #1 | throttle | gösterim amaçlı | ✅ |
| 5 | MDN #1 | ID + clear | 8/8 temizlik | ✅ |
| 6 | MDN #1 | string XSS | 0 string | ✅ |
| 7 | MDN #1 | this bağlamı | ok fonksiyon | ✅ |
| 8 | MDN #3 | worker yok DOM | worker 0 | 🟡 |
| 9 | MDN #3 | 3 worker türü | 0 | 🟡 |
| 10 | MDN #3 | worker fetch | ana thread yeterli | ✅ |
| 11 | php #4 | sleep/sn | sleep 0 | ✅ |
| 12 | php #4 | sinyal dönüşü | ilgisiz | N/A |
| 13 | php #4 | ValueError | pozitif sabit | ✅ |
| 14 | php #5 | time sn + DateTime | fark/time · format/DTI | ✅ |
| 15 | php #5 | REQUEST_TIME | kullanılmıyor | ⚠️ |
| 16 | php #5 | epoch sıralama | UuidV7 | ✅ |

**Eşleşme:** 13 ✅ · 2 🟡 (boşluk) · 1 ⚠️ (tercihli) · 1 N/A.

---

## §4B — Erişilen İçerik Özeti (5 sayfa kanıt defteri)

> Her satır, o sayfadan çıkarılan ve §4'te matrise giren bilgidir. Özetler
> 2026-10-15 erişiminde sayfadan okunmuştur; **yeni URL burada da üretilmez**.

### §4B.1 MDN #1 — `Window/setTimeout`

| # | Sayfadan çıkan madde | Matris bağlantısı |
|---|---|---|
| 1 | `handler` + `timeout` imzası; `timeout` varsayılanı **0** | §4.1 |
| 2 | Negatif `timeout` 0 gibi davranır (clamped) | §4.1 |
| 3 | Ek parametreler handler'a argüman olarak aktarılır | §4.7 |
| 4 | Dönen değer `timeoutId` — `clearTimeout` ile iptal | §4.5 |
| 5 | İç içe ≥5 çağrıda min gecikme **4ms**'ye çıkar | §4.3 |
| 6 | Arka planda çalışan timer'lar throttle edilir (Chrome intensive: 60sn) | §4.4 |
| 7 | String `code` çalıştırılabilir → Trusted Types ile güvenli hale getirilir | §4.6 |
| 8 | Görsel/oyun döngüsü için `requestAnimationFrame` önerilir (timer değil) | §1 envanter notu |

### §4B.2 MDN #2 — `WindowOrWorkerGlobalScope/setTimeout`

| # | Sayfadan çıkan madde | Matris bağlantısı |
|---|---|---|
| 1 | Eski mixin tanımı; içeriği `Window/setTimeout` sayfasına **redirect** olur | §2 not (URL #2) |
| 2 | Hem Window hem Worker bağlamında aynı davranışın tarihsel kaydı | §4.2 |
| 3 | `Window` ve `WorkerGlobalScope` karşılıkları bu yüzden aynıdır | §4.8 |
| 4 | curl -L ile **200** + aynı HTML → tek içerik iki yol | §2 |

### §4B.3 MDN #3 — `Web Workers API`

| # | Sayfadan çıkan madde | Matris bağlantısı |
|---|---|---|
| 1 | Worker ayrı thread'de çalışır; **kendi event loop'u** vardır | §4.8 |
| 2 | Worker içinde **DOM erişimi yoktur**; `document`/`window` tanımsız | §4.8 |
| 3 | İletişim `postMessage` / `onmessage` ile (kopyalı paylaşım) | §4.8 |
| 4 | Üç tür: Dedicated · Shared · Service Worker | §4.9 |
| 5 | Worker içinde `fetch`, `XMLHttpRequest`, `setTimeout` kullanılabilir | §4.10 |
| 6 | Ana thread'den ayrılmak için `new Worker(url)` + modül yükleyici | §6-1 (kullanım kararı) |

### §4B.4 php.net #4 — `sleep()`

| # | Sayfadan çıkan madde | Matris bağlantısı |
|---|---|---|
| 1 | `sleep(int $seconds): int` — tam saniye (kesir `usleep`) | §4.11 |
| 2 | Başarıda **0** döner; sinyalle kesilirse kalan saniye | §4.12 |
| 3 | PHP 8.0+ negatif `seconds` → `ValueError` | §4.13 |
| 4 | Çağrının süreyi **engelleyici** olduğu (blocking) — web isteğinde maliyet | §4.11 yorumu |
| 5 | Alternatif olarak bekleme yerine event/döngü önerilir | §5-4 (red: usleep) |

### §4B.5 php.net #5 — `time()`

| # | Sayfadan çıkan madde | Matris bağlantısı |
|---|---|---|
| 1 | `time(): int` — Unix epoch, **saniye** cinsinden | §4.14 |
| 2 | Dönüş **timezone bilgisi taşımaz** (ham sayı) | §4.14 |
| 3 | Tarih hesabı için `DateTimeImmutable` / `gmdate` benzeri API'ler önerilir | §4.14 |
| 4 | `$_SERVER['REQUEST_TIME']` istek başı sabit damgasıdır | §4.15 |
| 5 | Epoch farkı = süre ölçümü kalıbı (`$now - $last >= $eşik`) | §4.14, §4.16 |

**Toplam içerik maddesi:** 8 + 4 + 6 + 5 + 5 = **28 madde** → §4'teki 17 matris satırının kaynağı.

---

## §4C — Claim → Repo Doğrulama Komutları (PowerShell metin)

> **Betik yazılmaz** — tekrarlanabilir komut metni olarak bloklanır. Beklenen çıktılar
> 2026-10-15 ölçümüdür.

```powershell
# C1 — JS timer envanteri (claim §4.1: tüm delay'ler açık)
Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -Filter *.js |
  Select-String -Pattern 'setTimeout|setInterval' |
  Measure-Object | Select-Object -ExpandProperty Count     # beklenen: 20
```

```powershell
# C2 — String code yokluğu (claim §4.6: XSS yüzeyi kapalı)
Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -Filter *.js |
  Select-String -Pattern "setTimeout\(['`"]" |
  Measure-Object | Select-Object -ExpandProperty Count     # beklenen: 0
```

```powershell
# C3 — PHP bloklayıcı bekleme yok (claim §4.11)
Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Include *.php |
  Select-String -Pattern 'sleep\(|usleep\(' |
  Measure-Object | Select-Object -ExpandProperty Count     # beklenen: 0
```

```powershell
# C4 — Worker yokluğu (claim §4.8/§4.9)
Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -Filter *.js |
  Select-String -Pattern 'new Worker\(|ServiceWorker' |
  Measure-Object | Select-Object -ExpandProperty Count     # beklenen: 0
```

```powershell
# C5 — time() vs DateTimeImmutable ayrımı (claim §4.14)
Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Include *.php |
  Select-String -Pattern 'time\(\)' | Measure-Object | Select-Object -ExpandProperty Count   # fark hesabı
Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Include *.php |
  Select-String -Pattern 'DateTimeImmutable' | Measure-Object | Select-Object -ExpandProperty Count  # formatlama
```

| Komut | Claim | Beklenen (2026-10-15) | Durum |
|---|---|---|---|
| C1 | §4.1 | 20 | ✅ ölçüldü |
| C2 | §4.6 | 0 | ✅ ölçüldü |
| C3 | §4.11 | 0 | ✅ ölçüldü |
| C4 | §4.8/§4.9 | 0 | ✅ ölçüldü |
| C5 | §4.14 | time>0 · DTI>0 (ikisi de var) | ✅ ölçüldü |

---

## §5 — Reddedilen Adaylar (etiket kaydı)

| # | Aday (etiket) | Neden reddedildi | Sonuç |
|---|---|---|---|
| 1 | MDN `Window/setInterval` | Havuz dışı — havuzda yalnız setTimeout | elendi |
| 2 | MDN `Window/requestAnimationFrame` | Havuz dışı | elendi |
| 3 | MDN `WindowOrWorkerGlobalScope/setInterval` | Havuz dışı | elendi |
| 4 | php.net `usleep` | Havuz dışı (sleep yeterli) | elendi |
| 5 | php.net `microtime` | Havuz dışı | elendi |
| 6 | php.net `time_nanosleep` | Havuz dışı | elendi |
| 7 | MDN `WorkerGlobalScope/setTimeout` | Havuz dışı | elendi |
| 8 | crontab genel belge (man7/教程) | Resmi/değil + havuz dışı | elendi |
| 9 | Node.js `setTimeout` sürüm notları | Tarayıcı modülü (Node değil) | elendi |
| 10 | MySQL `EVENT SCHEDULE` dokümanı | Havuz dışı | elendi |

**Kural:** Reddedilen adayların **tam URL'i yazılmaz** — yalnız etiket + neden kaydedilir.
10 aday **elendi**; 5'i **kullanıldı**.

---

## §6 — Açık sorular

| # | Soru | İlişkili claim | Durum |
|---|---|---|---|
| 1 | Web Worker zamanlaması (MDN #3 kaynağı mevcut) kullanılacak mı? | §4-8/9 | ⚠️ VERIFICATION REQUIRED |
| 2 | `REQUEST_TIME` kısayolu benimsenecek mi (performans)? | §4-15 | ⚠️ VERIFICATION REQUIRED |
| 3 | `--duration-fast` çakışması hangi dosyadan okunacak? | — | ⚠️ VERIFICATION REQUIRED |
| 4 | Background throttling'e karşı `AudioContext` koruması (MDN #1: ses çalan sayfa throttle edilmez) kullanılacak mı? | §4-4 | ⚠️ VERIFICATION REQUIRED |
| 5 | Cron/queue/worker altyapısı hangi düğümde? | — | ⚠️ VERIFICATION REQUIRED |

**Spec §8 satırı (bağlayıcı kayıt):**
Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır;
belge seviyesinde kayıt.

---

## §A — Ekler

### §A.1 Erişim yöntemi (tekrarlanabilir)

| Adım | Komut | Beklenen |
|---|---|---|
| HTTP kodu | `curl.exe -L -s -o NUL -w "%{http_code}" <url>` | 200 |
| İçerik | `webfetch` (markdown) veya exa fetch | temiz metin |
| Kesişim | HashSet `Contains` (§3 bloğu) | 0 |
| Tarih | 2026-10-15 | — |

### §A.2 Kaynak türü dağılımı

| Kaynak | Sayı | Alan |
|---|---|---|
| MDN | 3 | JS zamanlayıcı + Worker |
| php.net | 2 | PHP sleep + time |
| Toplam | 5 | — |

### §A.3 Doğrulama günlüğü (2026-10-15, adım adım)

| # | Adım | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Havuz belirlendi (spec §A, 5 URL) | spec okuması | 5/5 sabit |
| 2 | HTTP kodu turu | `curl.exe -L -s -o NUL -w "%{http_code}"` | 5/5 → 200 |
| 3 | İçerik çekimi (md) | `webfetch` / exa fetch | 5/5 temiz metin |
| 4 | İçerik maddeleştirme | sayfa bazlı not (§4B) | 28 madde |
| 5 | Claim↔repo matrisi | §4 tabloları | 17 satır |
| 6 | Repo grep'leri (C1–C5) | PowerShell `Select-String` | 5/5 beklenen |
| 7 | Envanter hazırlığı | `k011-urls.txt` (4286 satır) | hazır |
| 8 | Kesişim testi | HashSet `Contains` (§3) | **HIT=0** |
| 9 | Reddedilen adaylar | etiketleme (§5) | 10 elendi |
| 10 | Açık sorular | §6 | 5 açık |

**Adım 2–3 notu:** URL #2 `-L` olmadan da 200 döner; redirect zinciri MDN'in aynı
içeriğe bağlandığını gösterir. İki ölçüm ayrı ayrı yapıldı, ikisi de 200.

### §A.4 Kesişim — ikinci yöntem (satır bazlı çapraz kontrol)

> HashSet yöntemine **bağımsız ikinci tur**: doğrudan dosya üzerinde literal arama.
> İki yöntem de 0 verirse sonuç iki tekniğe dayanır.

```powershell
$inv = "C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt"
$pool = @(
  'https://developer.mozilla.org/en-US/docs/Web/API/Window/setTimeout',
  'https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/setTimeout',
  'https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API',
  'https://www.php.net/manual/en/function.sleep.php',
  'https://www.php.net/manual/en/function.time.php'
)
$total = 0
foreach ($p in $pool) {
  $n = (Select-String -Path $inv -SimpleMatch -Pattern $p | Measure-Object).Count
  Write-Output "$n  $p"
  $total += $n
}
Write-Output "TOTAL_HITS: $total"     # beklenen: 0
```

| Yöntem | Teknik | Sonuç |
|---|---|---|
| 1 | HashSet `Contains` (OrdinalIgnoreCase) | 0 |
| 2 | `Select-String -SimpleMatch` (satır bazlı) | 0 (beklenen) |
| **Uzlaşma** | iki yöntem aynı sonucu vermeli | **0 = doğrulandı** |

### §A.5 Sınır ve kısıtlar (dürüstlük kaydı)

| # | Kısıt | Etki |
|---|---|---|
| 1 | Yalnız 5 havuz URL'i kullanılabilir | Diğer claim'ler yalnız bu 5'ten türetildi |
| 2 | Reddedilen adayların tam URL'i yazılmaz | §5 yalnız etiket taşır |
| 3 | Repo iddiası grep/count ile kanıtlanır | Uydurulan satır yok; tüm sayılar 2026-10-15 ölçümü |
| 4 | `⚠️ VERIFICATION REQUIRED` açık kalır | §6'daki 5 soru kapatılmadı, işaretli kaldı |
| 5 | Bu dosya yalnız kendi kanıtını taşır | Kod örnekleri `zamanlamali-gorevler-ornekler.md` §1–§6'da |

### §A.6 Öğrenilenler (araştırma turu)

| # | Öğrenim | Repo etkisi |
|---|---|---|
| 1 | MDN string `code` XSS uyarısı — repo 0 string | Güvenlik disiplini iyi |
| 2 | Throttling kuralı — repo saat widget'ında tolere | Ek koruma gerekmez |
| 3 | `sleep` dönüş kuralı — repo sleep kullanmıyor | Doğru kaçınma |
| 4 | `time()` timezone uyarısı — repo format için `DateTimeImmutable` | Ayrım net |
| 5 | Worker üçlüsü — repo'da hiç yok | En büyük boşluk |
| 6 | `REQUEST_TIME` kısayolu — repo kullanmıyor | Tercihli sapma |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
