---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik
evidence:
  - https://man7.org/linux/man-pages/man3/pthread_create.3.html
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - https://man7.org/linux/man-pages/man7/signal.7.html
  - https://man7.org/linux/man-pages/man2/waitpid.2.html
  - https://man7.org/linux/man-pages/man2/vfork.2.html
  - https://man7.org/linux/man-pages/man2/clone.2.html
  - https://man7.org/linux/man-pages/man3/pthread_join.3.html
  - https://man7.org/linux/man-pages/man7/pthreads.7.html
  - https://man7.org/linux/man-pages/man7/sched.7.html
  - https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw
  - https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads
  - https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-exitprocess
  - shared/src/Cache/ApcuAdapter.php:1-56
  - shared/composer.json:8-9
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269
  - shared/src/AI/AIEngine.php:288-300
validation: "2026-10-13 · 12 URL HTTP 200 (3 devralınan + 9 bu tur eklenen — §17.1) · repo satırları Read ile birebir · PHP yüzeyi desen taraması (§10) · içerik 380-460 satır bandında"
open_questions:
  - "R2: Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Uygulama katmanında thread oluşturucu kod yok. [UNKNOWN]"
  - "R9: APCu per-process bellek modeli — çok-worker topolojide cache tutarlılığı nasıl sağlanıyor? Repo'da invalidation stratejisi görünmüyor. [⚠️ VERIFICATION REQUIRED]"
  - "R10: Süreç kuyruğu (background job / queue) hangi süreç modelinde çalışıyor? Repo'da ayrı worker process'i tanımlı değil. [UNKNOWN]"
group: mimari
---

# surec-ve-parcacik-anayasa — Bağlayıcı Tanımlar ve İlkeler (Kademe-3)

## §0 ELI15

İşletim sistemi, uygulamayı bir **süreç** (process) olarak başlatır: her sürecin kendi
bellek alanı, kendi çalışma zamanı ve kendi sonlanma anı vardır. Aynı sürecin içinde
paylaşılan adres alanında çalışan küçük birimlere **iş-parçacığı** (thread) denir.
Çekirdek, süreçlere **sinyal**le dış olayları bildirir (bitir, yeniden boyutlandır).
CoreMusic PHP tarafında bellek process-başına olduğundan APCu önbelleği de
process-başınadır. Bu anayasa beş kavramı — **süreç · iş-parçacığı · sinyal ·
per-process bellek · çıkış semantiği** — bağlayıcı biçimde tanımlar; repo yüzeyi
`-surucu` ve `-ornekler`dadır.

## §1 Kimlik

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik/anayasa` |
| node_type / status | `module` / `draft` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-runtime/surec-ve-parcacik` |
| Kademe | Kademe-3 (bu dalın `index.md`'si ile aynı) |
| group | `mimari` (ADR-097 §5.5 · registry §8.1 — 9. FM alanı) |
| Bağımlılık | üst düğüm `surec-ve-parcacik` → `isletim-sistemi-runtime` → `K000-isletim-sistemi` |
| Komşular | `web-runtime-topolojisi` (sunucu süreç modeli) · `baslangic-ve-bootstrap` (boot) · `bellek-kavramlari` (sayfa/önbellek) |
| Kapsam kaynağı | `isletim-sistemi-runtime-kapsam.md` §3 · §8 (R2/R9/R10) |
| Doğrulama kaynağı | `isletim-sistemi-runtime-dogrulama.md` §13 satır 1-3 · §14 |

## §2 Kavram modeli — beş omurga maddesi

### M1 — Süreç (process)

| Alan | Tanım |
|---|---|
| Nedir | Çalışan programın OS karşılığı: bellek alanı, yığın, dosya tanımları ve bir PID ile temsil edilen birim |
| Başlatma (POSIX) | `fork(2)` mevcut sürecin kopyasını üretir; `exec` ailesi yeni program yükler — ikisi birlikte klasik başlatma zinciridir |
| Başlatma (Windows) | `CreateProcess` ailesi yeni süreç doğrudan oluşturur; `fork` eşdeğeri yoktur |
| Kaynak | `https://man7.org/linux/man-pages/man2/fork.2.html` (2026-10-13 HTTP 200 · bu tur envantere eklendi) |
| Sınır | Süreç **yaratım semantiği** burada; süreç tablosu/zamanlayıcı iç yapısı `isletim-sistemi-kernel` |
| Bu repoda | Uygulama katmanında süreç oluşturma yalnız testte (`proc_open`, `-ornekler` §2 E-3) |

### M2 — İş-parçacığı (thread)

| Alan | Tanım |
|---|---|
| Nedir | Süreç içinde çalışan, adres alanını (kod/veri/heap) kardeş parçacıklarla **paylaşan** yürütme birimi |
| Oluşturma (POSIX) | `pthread_create` yeni parçacık üretir; `pthread_join` bitişini bekler, `pthread_detach` beklemeden kaynakları ayırır |
| Paylaşım | Heap ve global değişkenler ortaktır; yığın (stack) ve program sayacı parçacıktır |
| Kaynak | `https://man7.org/linux/man-pages/man3/pthread_create.3.html` (2026-10-13 HTTP 200 · bu tur envantere eklendi) |
| Sınır | Parçacık **kavramı** burada; pthread fonksiyon listesi ve senkronizasyon primitive'leri `isletim-sistemi-api` |
| Bu repoda | Üretim kodunda thread oluşturucu **yok** — R2 [UNKNOWN]; PHP'de eşzamanlı model süreç-tabanlıdır |

### M3 — Sinyal

| Alan | Tanım |
|---|---|
| Nedir | Çekirdeğin ya da bir sürecin başka bir sürece gönderdiği **asenkron bildirim** (sonlandırma, durdurma, yeniden boyutlandırma) |
| Teslim | Sinyal **sürece** teslim edilir; parçacıklar arası ayrım POSIX'te tanımlı değildir (süreç genelinde tek işleyici) |
| Varsayılan eylem | Çoğu sinyalin varsayılanı sonlandırma ya da durdurma; süreç yakalamazsa bu eylem uygulanır |
| Kaynak | `https://man7.org/linux/man-pages/man7/signal.7.html` (2026-10-13 HTTP 200 · bu tur envantere eklendi) |
| Sınır | Sinyal **kavramı** burada; PHP/uygulama yüzeyi `baslangic-ve-bootstrap` (kapanış evresi) |
| Bu repoda | Uygulama kodunda sinyal işleyici **gözlenmedi**; web sunucusu süreç yönetimini kendisi yapar |

### M4 — Per-process bellek ve APCu

| Alan | Tanım |
|---|---|
| Nedir | PHP çalışma zamanının her sürecinin kendi adres alanında tuttuğu önbellek alanı (APCu) |
| Sonuç | Çok process (worker/app-pool) topolojisinde **her process'in kendi APCu önbelleği vardır**; bir process'te yazılan diğer process'te görünmeyebilir |
| Repo yüzeyi | `shared/src/Cache/ApcuAdapter.php:1-56` — altı metodun her biri `function_exists` korumalıdır |
| Zorunluluk | `shared/composer.json:9` `ext-apcu` zorunlu bağımlılıktır; kod tarafı yine de eklenti kapalı ortama dayanıklıdır |
| Sınır | Önbellek **uygulaması** bu daldır; sayfa/istek önbelleği kavramları `bellek-kavramlari` dalı |
| Açık | Tutarlılık stratejisi (invalidation) repo'da görünmüyor — R9 [⚠️ VERIFICATION REQUIRED] |

### M5 — SAPI: PHP ↔ süreç arayüzü

| Alan | Tanım |
|---|---|
| Nedir | PHP'nin çalıştığı sürecin türüne göre arayüz katmanı: Apache mod_php, PHP-FPM, CLI, IIS ISAPI |
| Sonuç | Boot sırası SAPI'dan bağımsız yazılır (bkz. `baslangic-ve-bootstrap`); ini kaynakları SAPI'ya göre ayrılır |
| Repo yüzeyi | `.htaccess` `php_flag` direktifleri yalnız Apache modu altında etkindir (R3 ⚠️) |
| Sınır | Sunucu topolojisi `web-runtime-topolojisi`; burada yalnız süreç-bağımlılık kuralı |
| Kaynak | PHP-FPM kurulum dokümanı bu ağacın kök `index.md` Web Research bölümünde kayıtlıdır |

### M6 — Çıkış semantiği (exit · atexit · shutdown)

| Alan | Tanım |
|---|---|
| Nedir | Sürecin sonlanması: `exit(3)` atexit temizleyicilerini çalıştırıp süreci sonlandırır |
| Uygulama katmanı | PHP `exit` çağrısı istek yaşam döngüsünü kapatır; kapanış (shutdown) evresi burada başlar |
| Repo yüzeyi | `home.coremusic.net/config/bootstrap.php` `/health` ucunda `exit` (15-26) |
| Sınır | Shutdown **evresi tanımı** `baslangic-ve-bootstrap`; burada OS semantiği |
| Açık | Kapanışta iş bitirme (drenaç) kanıtı yok — R6 [UNKNOWN] |

### M7 — Eşzamanlı sayaç ve atomiklik

| Alan | Tanım |
|---|---|
| Nedir | Çok process erişiminde sayaç/limit gibi değerler ya atomik primitive ile güncellenir ya da process-başına ayrı tutulur |
| Repo yüzeyi | `ApcuAdapter::increment` (`apcu_inc`) rate-limit sayaçlarında kullanılır |
| Sonuç | Sayaç process-başına ise toplam değer worker sayısına göre sapar; atomik paylaşım yoksa R9 ile aynı boşluk doğar |
| Sınır | Sayaç **politikası** `isletim-sistemi-security` (rate limit); burada atomiklik kavramı |
| Ölçüm | Gözlenen sapma → §4 madde 3 |

### §2.1 — Bu dalın kavram → dosya dağılımı

| # | Kavram | Gittiği dosya | Bölüm |
|---|---|---|---|
| 1 | Süreç · fork/exec · CreateProcess | `-anayasa` (bu dosya) | §2 M1 · §3 A1 |
| 2 | İş-parçacığı (pthread_create/join/detach) | `-anayasa` + `-surucu` | §2 M2 · §3 A2 |
| 3 | Sinyal | `-anayasa` | §2 M3 · §3 A3 |
| 4 | Per-process bellek (APCu) · atomik sayaç | `-anayasa` + `-ornekler` | §2 M4 · M7 · §3 A4 |
| 5 | Çıkış semantiği (exit/atexit/shutdown) | `-anayasa` + `baslangic-ve-bootstrap` | §2 M6 · §3 A5 |
| 6 | Repo gerçek örnekleri, anti-pattern | `-ornekler` | §2-§3 |

### §2.2 — Platform yansımaları (kavram sabit, ad değişir)

| Kavram | POSIX/Linux | Windows | Not |
|---|---|---|---|
| Süreç başlatma | `fork` + `exec` | `CreateProcess` ailesi | fork.2 (bu tur) · Windows API bu ağacın kök `index.md` Web Research bölümünde |
| İş-parçacığı | `pthreads` | Win32 thread API | Windows adları bu turda **doğrulanmadı → kullanılmaz** |
| Bildirim | sinyal | farklı mekanizma (mesaj/aşama) | sinyal.7 (bu tur) · Windows eşdeğeri **UNKNOWN** |
| Per-process bellek | süreç-başına APCu | süreç-başına APCu | PHP eklentisi platformdan bağımsız süreç modeline uyar |
| Kuyruk süreci | ayrı süreç (R10) | ayrı süreç (R10) | repo'da tanımlı değil — [UNKNOWN] |

## §3 Mekanizma akışı — beş olay zinciri (A1-A5)

### A1 — Süreç başlatma zinciri (POSIX)

| # | Adım | Ne olur | Sonuç |
|---|---|---|---|
| 1 | `fork` çağrısı | Mevcut sürecin bellek alanı kopyalanır | İki süreç (ebeveyn + çocuk) |
| 2 | Çocukta karar | pid 0 → çocuk dalı | `exec` ile yeni program |
| 3 | `exec` | Yeni program belleğe yüklenir | Aynı PID, yeni kod |
| 4 | Ebeveyn | Çocuğun bitişini bekleyebilir (`wait`) ya da devam eder | Yetim (orphan) davranışı |
| 5 | Sonuç | Yeni süreç tamamlandı | `fork.2` (§9 № 2) |

> Bu zincir web sunucularının klasik worker üretme yöntemidir (prefork MPM vb.);
> sunucu topolojisi `web-runtime-topolojisi` dalındadır.

### A2 — İş-parçacığı yaşam döngüsü zinciri

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | `pthread_create` | Yeni parçacık başlatılır; giriş işlevi ve veri parametresi alınır | pthread_create.3 (§9 № 1) |
| 2 | Çalışma | Parçacık adres alanını kardeşleriyle paylaşır | §2 M2 |
| 3a | `pthread_join` | Çağıran parçacık bitişini bekler, kaynaklar serbest kalır | bu ağacın `surec-ve-parcacik/index.md` Web Research bölümü |
| 3b | `pthread_detach` | Beklemeden kaynaklar parçacıktan ayrılır | bu ağacın `surec-ve-parcacik/index.md` Web Research bölümü |
| 4 | Bitiş | Parçacık adres alanı terk edilir; süreç sürer | pthread_create.3 |

> R2 durumu: repo üretim kodunda bu zinciri tetikleyen çağrı **yoktur**; zincir
> hedef mimaride pthread kullanılması hâlinde geçerli olur.

### A3 — Sinyal teslim zinciri

| # | Adım | Ne olur | Hata/sonuç |
|---|---|---|---|
| 1 | Olay | Kaynak (çekirdek/üst süreç) sinyali belirler | — |
| 2 | Kuyruğa alma | Sinyal hedef sürecin sinyal kuyruğuna konur | — |
| 3 | Teslim | Süreç uygun olduğunda sinyal teslim edilir | varsayılan eylem uygulanabilir |
| 4 | İşleyici | Süreç yakalamışsa atanmış işleyici çalışır | sinyal.7 (§9 № 3) |
| 5 | Sonuç | Süreç sonlanır, durur ya da işleyiciyle devam eder | repo'da işleyici gözlenmedi |

### A4 — İstek başına süreç yaşam döngüsü (web worker)

| # | Adım | Ne olur | Repo sargısı |
|---|---|---|---|
| 1 | Sunucu kabul | Worker process/app-pool isteği kabul eder | `web-runtime-topolojisi` dalı |
| 2 | PHP devri | İstek PHP çalışma zamanına SAPI üzerinden girer | §2 M5 |
| 3 | Boot | Uygulama boot sırası çalışır (process başı bir kez ya da istek başı) | `baslangic-ve-bootstrap` dalı |
| 4 | Çalışma | Uygulama; APCu process-başına erişilir | §2 M4 · `-ornekler` §2 E-1 |
| 5 | Sonuç | Yanıt yazılır, istek kapanır | R6 drenaç [UNKNOWN] |

### A5 — Çıkış zinciri (exit → shutdown → son)

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | `exit` çağrısı | Uygulama isteği kapatmak ister | repo: `bootstrap.php:15-26` `/health` |
| 2 | atexit temizleyicileri | Kayıtlı temizleyiciler çalıştırılır | `exit(3)` bu ağacın `surec-ve-parcacik/index.md` Web Research bölümü |
| 3 | Kapanış evresi | PHP shutdown çağrılır (duran istek dâhil) | `baslangic-ve-bootstrap` §3 |
| 4 | Son | Süreç sonlanır; OS kaynakları geri alınır | §2 M6 |

## §4 Ölçüm / eşik — bu dalın ölçüm sözleşmesi

| # | Ne ölçülür | Araç / yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | Worker process sayısı | sunucu yapılandırması (MPM/havuz) | R7 [⚠️] — değer uydurulmaz | UNKNOWN |
| 2 | APCu erişimi varlığı | `apcu_enabled()` (repo: `api/index.php:143`) | devre ise ApcuAdapter | OKU (2026-10-13) |
| 3 | Rate-limit sayaç sapması | çok-worker ortamda sayaç karşılaştırması | sapma gözlenebilir | PLANNED (R9) |
| 4 | Thread oluşturucu kullanım | üretim kodu taraması (`proc_open`/`pthread`) | yalnız testte `proc_open` | OKU (2026-10-13) |
| 5 | Middleware süresi | `hrtime` (repo: `MiddlewarePipeline.php:38-52`) | gerçek ölçüm mevcut | OKU (2026-10-13) |
| 6 | Alt süreç çıkış kodu | `proc_close` (repo: `ApiKeyCreateCommandTest.php:248-269`) | testte asserted | OKU (2026-10-13) |
| 7 | Sinyal işleyici sayısı | üretim kodu taraması | 0 gözlenen | OKU (2026-10-13) |

> Ölçüm kuralı: eşik değeri uydurulmaz; değer üretilemiyorsa hücre `UNKNOWN` kalır
> (`isletim-sistemi-runtime-kapsam.md` §8 · `-dogrulama` §14).

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| Sunucu süreç modeli (MPM, app-pool, FPM havuzu) | `web-runtime-topolojisi` | süreç **semantiği** kalır, topoloji gitmez |
| Boot sırası ve shutdown evresi uygulaması | `baslangic-ve-bootstrap` | çıkış **kavramı** kalır, boot gitmez |
| pthread fonksiyon listesi, mutex/semaphore API | `isletim-sistemi-api` | parçacık **tanımı** kalır, çağrı gitmez |
| Sinyal yakalama API'si (`pcntl_*` vb.) | `isletim-sistemi-api` | sinyal **kavramı** kalır |
| Önbellek sayfa/istek katmanı kavramları | `isletim-sistemi-core` / `bellek-kavramlari` | per-process APCu kavramı burada |
| Rate-limit politikası ve sayaç eşikleri | `isletim-sistemi-security` | atomiklik kavramı kalır |
| Kuyruk/zamanlayıcı süreci (R10) | `isletim-sistemi-plan` | süreç-başına kural kalır |
| Test süreci sağlama (`proc_open`) | `isletim-sistemi-testing` | süreç **örneği** `-ornekler` §2 E-3 |

## §6 Terim sözlüğü

| Terim | Tanım |
|---|---|
| **Süreç (process)** | Bellek alanı ve PID ile temsil edilen çalışan program birimi |
| **PID** | Sürecin sistem numarası |
| **`fork`** | Mevcut sürecin kopyasını üreten POSIX çağrısı |
| **`exec`** | Sürecin belleğine yeni program yükleyen POSIX çağrı ailesi |
| **İş-parçacığı (thread)** | Süreç içinde adres alanını paylaşan yürütme birimi |
| **Adres alanı** | Kod + veri + heap bellek bölgesi; parçacıklar arası ortaktır |
| **`pthread_create`** | Yeni iş-parçacığı başlatan POSIX çağrısı |
| **`pthread_join`** | Parçacık bitişini bekleyen POSIX çağrısı |
| **`pthread_detach`** | Parçacığı beklemeden kaynaklarından ayıran POSIX çağrısı |
| **Sinyal** | Sürece teslim edilen asenkron bildirim |
| **Varsayılan eylem** | İşleyici atanmadığında sinyalin uyguladığı davranış |
| **SAPI** | PHP'nin çalıştığı süreç türüne arayüz katmanı (mod_php, FPM, CLI) |
| **APCu** | PHP process-başına paylaşılan istek-arası önbellek |
| **Per-process bellek** | Her sürecin kendi adres alanı; süreçler arası doğrudan paylaşım yok |
| **Atomik artırım** | Kesintisiz gerçekleştirilen sayaç güncellemesi (`apcu_inc`) |
| **`exit(3)`** | atexit temizleyicilerini çalıştırıp süreci sonlandıran POSIX çağrısı |
| **Shutdown evresi** | `exit` sonrası kapanış çağrılarının çalıştığı dönem |
| **Yetim süreç** | Ebeveyni biten çocuk süreç |
| **Worker process** | Sunucunun istek işleyen çocuk süreci (MPM/app-pool) |
| **Drenaç** | Kapanışta bekleyen istekleri bitirme davranışı (R6) |

## §7 Kapsam dışı — bu dosyaya yazılmaz

| # | Konu | Nereye |
|---|---|---|
| 1 | pthread/mutex fonksiyon imza listesi | `isletim-sistemi-api` |
| 2 | Sunucu MPM/FPM havuz yapılandırması | `web-runtime-topolojisi` |
| 3 | Boot sırası, ini/hata raporlama uygulaması | `baslangic-ve-bootstrap` |
| 4 | Zamanlayıcı ve çekirdek süreç tablosu | `isletim-sistemi-kernel` |
| 5 | Rate-limit politikası ve eşikleri | `isletim-sistemi-security` |
| 6 | Kuyruk işi kuyruğu tasarımı (R10) | `isletim-sistemi-plan` |
| 7 | Test senaryosu yazımı | `isletim-sistemi-testing` |
| 8 | Repo somut örnekleri ve anti-pattern listesi | bu dalın `-ornekler.md` dosyası |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R2 | Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? Üretim kodunda thread oluşturucu yok. | [UNKNOWN] |
| R9 | APCu per-process bellek modeli — çok-worker topolojide cache tutarlılığı nasıl sağlanıyor? Repo'da invalidation stratejisi görünmüyor. | [⚠️ VERIFICATION REQUIRED] |
| R10 | Süreç kuyruğu (background job / queue) hangi süreç modelinde çalışıyor? Repo'da ayrı worker process'i tanımlı değil. | [UNKNOWN] |

## §9 Kaynakça — 3 URL, HTTP 200, 2026-10-13

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `pthread_create(3)` — iş-parçacığı oluşturma/bitiş semantiği (join/detach dahil) | https://man7.org/linux/man-pages/man3/pthread_create.3.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 2 | `fork(2)` — süreç kopyalama ve exec zinciri | https://man7.org/linux/man-pages/man2/fork.2.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 3 | `signal(7)` — sinyal kavramı, teslim ve varsayılan eylemler | https://man7.org/linux/man-pages/man7/signal.7.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |

> `pthread_join` · `pthread_detach` · `exit(3)` kaynakları bu dalın `index.md`'sinde
> kayıtlıdır; bu dosyada tekrarlanmaz (aynı URL iki dosyada geçmez).

## §10 — Repo Süreç/Parçacık Yüzeyi (bu tur disk taraması · 2026-10-13)

> **Yöntem:** repo kökündeki tüm `*.php` dosyaları (`vendor/` ve `node_modules/` hariç)
> desen taramasıyla incelendi; eşleşme sayıları bu tur üretildi. `->exec(` (PDO) eşleşmeleri
> "bağımsız `exec()`" sayımından **çıkarıldı** (yalnızca `(?<![>:\w])exec\s*\(` sayıldı).

| # | Aranan yüzey | Desen | Eşleşme | Bulunduğu yer |
|---|---|---|---|---|
| 1 | `proc_open` | `proc_open` | **1** | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253` |
| 2 | `proc_close` | `proc_close` | **1** | aynı dosya: `268` |
| 3 | `shell_exec` | `shell_exec` | **1** | `shared/src/AI/AIEngine.php:295` |
| 4 | `escapeshellarg` | `escapeshellarg` | **1** | `shared/src/AI/AIEngine.php:292` |
| 5 | `escapeshellcmd` | `escapeshellcmd` | **0** | — |
| 6 | `popen` | `popen` | **0** | — |
| 7 | bağımsız `exec()` | `(?<![>:\w])exec\s*\(` | **0** | (`->exec(` = PDO, süreç çağrısı değil) |
| 8 | `system()` | `system(` | **0** | — |
| 9 | `passthru` | `passthru` | **0** | — |
| 10 | PHP sinyal uzantısı | `pcntl_` | **0** | Linux sinyal kurulumu yok |
| 11 | PHP parçacık uzantısı | `pthread_` | **0** | R2 ile tutarlı |
| 12 | `curl_exec` | `curl_exec` | **4** | `HomeAuthBridge.php:155` · `ToolCalling.php:268` · `BaseOAuthProvider.php:85` ve `:119` |

**Katman ayrımı (bağlayıcı):** tablodaki 12 satırın tamamı **PHP uygulama katmanı**dır —
bunlar OS'nin `fork`/`clone`/`pthread_create` çağrılımları değil, PHP'nin kendi süreç/alt-süreç
arayüzleridir. OS-primitif düzeyinde semantik bu dosyanın §13-§17'sinde; repo'da bu primitifleri
çağıran kod **gözlenmedi** (satır 6-11: 0 eşleşme).

## §11 — Tek süreç oluşturma yüzeyi (birebir · `ApiKeyCreateCommandTest.php:248-269`)

| Satır | Kod (birebir) | Süreç anlamı |
|---|---|---|
| 248 | `private function runCli(array $args): array` | CLI çalıştırma yardımcısı |
| 250 | `$command = array_merge([PHP_BINARY, self::BIN], $args);` | dizi tabanlı argüman — shell yok |
| 253 | `$handle = proc_open(` | süreç başlatma |
| 255 | `[0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],` | stdin kapatılır, stdout/stderr borusu alınır |
| 257 | `dirname(__DIR__, 3)` | çalışma dizini repo köküne sabitlenir |
| 260 | `$this->assertIsResource($handle, 'CLI alt süreci açılamadı');` | başlatma başarısızsa test durur |
| 262-266 | `fclose($pipes[0]);` → `stream_get_contents($pipes[1])` / `($pipes[2])` → `fclose` ×2 | boru okuma ve kapatma sırası |
| 268 | `return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];` | çıkış kodu toplanır |

> **Semantik bağlantısı:** `proc_close` çocuk sürecin bitişini bekleyip çıkış kodunu döndürür —
> POSIX `waitpid(2)` davranışının PHP sarmalayıcısıdır (§13/5). Bu dosyada süreç **yalnız testte**
> vardır; üretim kodunda süreç başlatan çağrı yok (§10 satır 6-9).

## §12 — Tek yardımcı süreç çağrısı (birebir · `AIEngine.php:288-300`)

| Satır | Kod (birebir) | Not |
|---|---|---|
| 288 | `private function extractMetadata(string $filePath): array` | ffprobe metadata çıkarımı |
| 290-293 | `$cmd = sprintf('ffprobe -v quiet -print_format json -show_format -show_streams "%s"', escapeshellarg($filePath));` | komut dizesi inşa ediliyor |
| 295 | `$output = shell_exec($cmd);` | tek yönlü, yalnız stdout |
| 296-297 | `if ($output === null) { return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2]; }` | başarısızlıkta sabit varsayılan |
| 300 | `$data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);` | JSON ayrıştırma; istisna yükseltebilir |

**Gözlem (rapor, düzeltme yok):** satır 292 `escapeshellarg()` sonucunu yeniden `"%s"` tırnakları
içine alır — tırnak-çiftleme davranışı kasıtlı mı, hata mı **kanıtlanamaz** → `[⚠️ VERIFICATION REQUIRED]`.
Bu dosya kapsamı dışına çıkmaz (düzeltme bu dalın işi değil).

## §13 — POSIX başlatma/bitiş zinciri (fork → exec → wait)

| # | Adım | Çağrı | Doğrulanmış metin (2026-10-13) | Kaynak |
|---|---|---|---|---|
| 1 | kopyala | `fork(2)` | "fork() creates a new process by duplicating the calling process." | fork.2 (§9/2) |
| 2 | ayır | `fork(2)` | "The child process and the parent process run in separate memory spaces." | fork.2 |
| 3 | tek parçacık | `fork(2)` | "The child process is created with a single thread—the one that called fork()." | fork.2 |
| 4 | yükle | `exec` ailesi | süreç görüntüsü yeni programla değiştirilir (bu dal `index.md` SEE ALSO zinciri) | fork.2 / index |
| 5 | bekle | `waitpid(2)` | "The waitpid() system call suspends execution of the calling thread until a child specified by pid argument has changed state." | waitpid.2 (§17.1/1) |
| 6 | zombi | `wait(2)` | "if a wait is not performed, then the terminated child remains in a \"zombie\" state" | waitpid.2 |
| 7 | sonlandır | `exit(3)` | atexit temizleyicileri çalıştırıp süreci sonlandırır | bu dal `index.md` (exit.3) |

**fork'un child'a geçirmediği / sıfırladığı nitelikler (fork.2 listesinden 7 satır):**

| # | Nitelik | fork.2 ifadesi |
|---|---|---|
| 1 | PID | "The child has its own unique process ID" |
| 2 | kaynak kullanımı | "Process resource utilizations ... and CPU time counters ... are reset to zero in the child." |
| 3 | bekleyen sinyaller | "The child's set of pending signals is initially empty" |
| 4 | bellek kilitleri | "The child does not inherit its parent's memory locks (mlock(2), mlockall(2))." |
| 5 | semafor ayarları | "The child does not inherit semaphore adjustments from its parent (semop(2))." |
| 6 | dosya kaydı kilitleri | "does not inherit process-associated record locks" |
| 7 | timer'lar | "The child does not inherit timers from its parent" |

## §14 — İkame başlatıcılar (vfork / clone)

| # | Çağrı | Farkı | Doğrulanmış metin | Kaynak |
|---|---|---|---|---|
| 1 | `vfork(2)` | ebeveyn duraklar | "the calling thread is suspended until the child terminates ... or it makes a call to execve(2)." | vfork.2 (§17.1/2) |
| 2 | `vfork(2)` | sayfa tablosu kopyalanmaz | "It is used to create new processes without copying the page tables of the parent process." | vfork.2 |
| 3 | `clone(2)` | paylaşım bayraklarıyla | "these system calls provide more precise control over what pieces of execution context are shared between the calling process and the child process." | clone.2 (§17.1/3) |
| 4 | `clone(2)` | paylaşım seçilebilir | "the caller can control whether or not the two processes share the virtual address space, the table of file descriptors, and the table of signal handlers." | clone.2 |
| 5 | `vfork(2)` | POSIX sınırı | "behavior is undefined if the process created by vfork() either modifies any data other than a variable of type pid_t ... or returns from the function in which vfork() was called" | vfork.2 |

> **Repo bağlantısı:** bu çağrılımların hiçbiri PHP uygulama kodunda gözlenmedi (§10 satır 6-11);
> tablo yalnız **semantik referans**tır — repo kanıtı değildir.

## §15 — Parçacık modeli (pthread_create → join · paylaşılan nitelikler)

| # | Kural | Doğrulanmış metin | Kaynak |
|---|---|---|---|
| 1 | oluşturma | "The pthread_create() function starts a new thread in the calling process." | pthread_create.3 (§9/1) |
| 2 | bitiş yolları | `pthread_exit(3)` · start_routine'den dönüş · `pthread_cancel(3)` · sürecin herhangi bir thread'inin `exit(3)` çağırması | pthread_create.3 |
| 3 | join | "The pthread_join() function waits for the thread specified by thread to terminate." | pthread_join.3 (§17.1/4) |
| 4 | joinable şartı | "The thread specified by thread must be joinable." | pthread_join.3 |
| 5 | eşzamanlı join | "If multiple threads simultaneously try to join with the same thread, the results are undefined." | pthread_join.3 |
| 6 | hatalar | `EDEADLK` (karşılıklı join) · `EINVAL` (joinable değil / başkası bekliyor) · `ESRCH` (thread yok) | pthread_join.3 |
| 7 | paylaşılan süreç nitelikleri | process ID · parent process ID · process group/session ID · open file descriptors · signal dispositions · umask · nice value · resource limits | pthreads.7 (§17.1/5) |
| 8 | parçacağa özgü nitelikler | thread ID (pthread_t) · signal mask · errno · alternatif sinyal yığını · gerçek zamanlı politika/öncelik | pthreads.7 |
| 9 | thread ID yaşam döngüsü | "The system may reuse a thread ID after a terminated thread has been joined, or a detached thread has terminated." | pthreads.7 |
| 10 | fonksiyon dönüşleri | "Most pthreads functions return 0 on success, and an error number on failure." — `errno` ayarlanmaz | pthreads.7 |

## §16 — Zamanlayıcı ve öncelik sınıfları (sched(7))

| # | Politika | `sched_priority` | Kural | Kaynak |
|---|---|---|---|---|
| 1 | `SCHED_OTHER` | **0** | "For threads scheduled under one of the normal scheduling policies ... sched_priority is not used in scheduling decisions (it must be specified as 0)." | sched.7 (§17.1/6) |
| 2 | `SCHED_IDLE` | 0 | normal politika grubu | sched.7 |
| 3 | `SCHED_BATCH` | 0 | normal politika grubu | sched.7 |
| 4 | `SCHED_FIFO` | **1 (düşük) – 99 (yüksek)** | "real-time threads always have higher priority than normal threads" | sched.7 |
| 5 | `SCHED_RR` | 1-99 | gerçek zamanlı, quantum `sched_rr_get_interval(2)` ile alınır | sched.7 |
| 6 | çekirdek | CFS | "Since Linux 2.6.23, the default scheduler is CFS" | sched.7 |
| 7 | taşınabilirlik | min 32 seviye | "POSIX.1 requires an implementation to support only a minimum 32 distinct priority levels for the real-time policies" | sched.7 |
| 8 | CPU bırakma | — | `sched_yield(2)` → "Cause the caller to relinquish the CPU" | sched.7 |
| 9 | PHP karşılığı | — | gerçek zamanlı politika kuran PHP kodu repo'da **0 eşleşme** (`pcntl_`, `posix_` taraması) | §10 satır 10-11 |

## §17 — Windows yansımaları (aynı kavram, farklı primitif)

| # | Kavram | Windows çağrısı | Doğrulanmış metin | Kaynak |
|---|---|---|---|---|
| 1 | süreç + birincil parçacık | `CreateProcessW` | "Creates a new process and its primary thread. The new process runs in the security context of the calling process." | CreateProcessW (§17.1/7) |
| 2 | süreç tanımı | — | "A process, in the simplest terms, is an executing program." | processes-and-threads (§17.1/8) |
| 3 | parçacık tanımı | — | "A thread is the basic unit to which the operating system allocates processor time." | processes-and-threads |
| 4 | iş havuzu | — | "A thread pool is a collection of worker threads that efficiently execute asynchronous callbacks on behalf of the application." | processes-and-threads |
| 5 | fiber | — | "A fiber is a unit of execution that must be manually scheduled by the application." | processes-and-threads |
| 6 | sonlandırma | `ExitProcess` | "Ends the calling process and all its threads." | ExitProcess (§17.1/9) |

**POSIX ↔ Windows karşılık matrisi:**

| POSIX kavramı | Windows karşılığı | Bu dosyadaki § | Durum |
|---|---|---|---|
| `fork` + `exec` | `CreateProcessW` | §13 · §17/1 | doğrulandı |
| `pthread_create` | `CreateThread` | §15/1 | ⚠️ API sayfası bu tur çekilmedi |
| `waitpid` | süreç handle'ı ile bekleme | §13/5 | ⚠️ API adı doğrulanmadı |
| `exit(3)` | `ExitProcess` | §17/6 | doğrulandı |
| `SIGTERM` ile bitirme | `TerminateProcess` | §3 (M5) | ⚠️ API sayfası bu tur çekilmedi |

### §17.1 — URL envanteri (bu tur yeni doğrulanmış · 2026-10-13 HTTP 200)

| # | URL | Kullanım |
|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/waitpid.2.html | §13/5-6 |
| 2 | https://man7.org/linux/man-pages/man2/vfork.2.html | §14 |
| 3 | https://man7.org/linux/man-pages/man2/clone.2.html | §14/3-4 |
| 4 | https://man7.org/linux/man-pages/man3/pthread_join.3.html | §15/3-6 |
| 5 | https://man7.org/linux/man-pages/man7/pthreads.7.html | §15/7-10 |
| 6 | https://man7.org/linux/man-pages/man7/sched.7.html | §16 |
| 7 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw | §17/1 |
| 8 | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | §17/2-5 |
| 9 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-exitprocess | §17/6 |

> Bu dokuz URL bu dosyaya **bu tur** eklendi; dizin içi tekil kullanımdır (aynı URL iki dosyada geçmez).

## §18 — Bu tur kanıt özeti (§10-§17) ve tekrarlanabilir ölçüm

| Bölüm | Kanıt türü | Kaynak | Ölçü |
|---|---|---|---|
| §10 | repo desen taraması | tüm `*.php` (vendor/node_modules hariç) | 12 desen · 7'si 0 eşleşme |
| §11 | birebir kod satırı | `ApiKeyCreateCommandTest.php:248-269` | 8 satır okundu |
| §12 | birebir kod satırı | `AIEngine.php:288-300` | 5 satır okundu |
| §13 | resmi metin alıntısı | fork.2 · waitpid.2 | 7 + 7 satır |
| §14 | resmi metin alıntısı | vfork.2 · clone.2 | 5 satır |
| §15 | resmi metin alıntısı | pthread_create.3 · pthread_join.3 · pthreads.7 | 10 satır |
| §16 | resmi metin alıntısı | sched.7 | 9 satır |
| §17 | resmi metin alıntısı | CreateProcessW · processes-and-threads · ExitProcess | 6 satır |
| §17.1 | URL doğrulama | man7 ×6 · learn.microsoft.com ×3 | 9 URL HTTP 200 |

**Tekrarlanabilir ölçüm (repo kökünden):**

| # | Amaç | Komut | Beklenen |
|---|---|---|---|
| 1 | süreç oluşturma sayısı | `Select-String -Path (Get-ChildItem -Recurse -Include *.php) -Pattern 'proc_open'` | 1 |
| 2 | shell yürütme sayısı | desen `'shell_exec'` | 1 |
| 3 | pcntl/pthread yokluğu | desen `'pcntl_|pthread_'` | 0 |
| 4 | bağımsız exec() yokluğu | desen `'(?<![>:\w])exec\s*\('` | 0 |
| 5 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |
| 6 | mojibake | `[IO.File]::ReadAllLines($f)` UTF-8 ayrıştırma | 0 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode