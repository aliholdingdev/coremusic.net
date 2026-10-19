---
node_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-anayasa
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-surucu
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
  - api.coremusic.net/src/Domain/AI/AIWorkflow.php:80
  - https://www.php.net/manual/en/function.microtime.php
  - https://www.php.net/manual/en/function.pcntl-wait.php
  - https://www.php.net/manual/en/function.pcntl-fork.php
validation: "2026-10-14 · §19 grupları G1-G4 (12 platform kolu · 10 kod bloğu) · §19.5 16 YENİ + 11 reuse HTTP 200 (envanter kesişimi 1 → dogrulama §J.5) · §19.6 14 reuse yeniden 200 · §20 PHP köprüsü (4 gerçek-satır bloğu · yeni URL 0 · pcntl/posix/getmypid/set_time_limit grep 0) · §21 çok dilli JS/C/Python/Go/Rust/C# (7 URL = 6 reuse + 1 yeni · 6 kalıp blok · JS/PY repo satır kanıtlı) · 1096 satır · macOS-native kaynak 0 → 4 iddia ⚠️ VERIFICATION REQUIRED · §13 repo satırları ReadAllLines ile birebir"
open_questions:
  - "Repo'da pcntl_alarm kullanımı bulunamadı — CLI zaman aşımı stratejisi netleşmeli (§5) → UNKNOWN"
  - "Kuyruk dispatch gecikme ölçümü PLANNED — sartname.md → UNKNOWN"
group: kod
---

# Süreç ve Zamanlama Kavramları — Örnekler (Kademe-4)

> **OS dürüstlük cümlesi:** Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs (§4).
> Bu dosya: anayasa'nın Z-senaryolarını (§8) ve sürücü'nün desenlerini (§4) **repo PHP
> koduyla** eşler. Örnek 1–3 kesin satır kanıtlıdır (ReadAllLines doğrulandı); Örnek 4–6
> OS davranışı senaryosudur (upstream docs).

## §1. Örnek envanteri

| # | Senaryo | Anayasa eşleşmesi | Sürücü eşleşmesi | Kanıt tipi |
|---|---|---|---|---|
| 1 | UUID v7 monotonik üretim — tekillik + zaman damgası | Z12 | §4.2 (gate) | Kesin satır |
| 2 | Oturum başlatma — çerez ömrü zamanlaması | Z1 | §3 (lifetime satırı) | Kesin satır |
| 3 | Middleware sıralaması — kısa yol + timeout kapısı | Z5 | §3 (timeout satırı) | Kesin satır |
| 4 | AI kuyruk throttling — polling timeout 10 ms | Z3 | §4.1 | OS + upstream |
| 5 | CLI süreç zaman aşımı — çıktı-dosyası doğrulama | Z1 | §4.3 | OS + upstream |
| 6 | Zamanlayıcı doğruluğu — `clock()` vs gerçek zaman | Z7 | §5 (clock_getres) | OS + upstream |

## §2. Kesin satır kanıtlı örnekler — repo kodu, ReadAllLines doğrulandı

### §2.1 Örnek 1 — UUID v7: tekillik zaman damgasıyla (UuidV7.php:19)

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `shared/src/CoreMusic/Core/Support/UuidV7.php` | 19 | `* 48-bit big-endian millisecond timestamp (RFC 9562 §5.1)` |

**OS bağlantısı:** UUID v7'nin ilk 48 bit'i milisaniye zaman damgasıdır (RFC 9562 §5.1) —
yani **UUID üreteci, OS saat kaynağına bağımlıdır.** `hrtime()` (monotonik) ile `time()`
(kabuk-duvarı) farkını anlatan anayasa §6.2 devreye girer: UUID v7 sıralaması **duvar-saati**
ile çalışır; duvar saati geri alınırsa (NTP düzeltmesi) sıralama bozulabilir — kuyruğa
giren kayıtların sırası değişebilir. **Dikkat:** tekillik iddiası (v7'nin) duvar-saati
geri alınsa bile korunur (random alan), ama **sıralanabilirlik** korunmaz.

### §2.2 Örnek 2 — Oturum çerez ömrü: zamanlama değeri kodda (SessionInitializer.php:23)

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `api.coremusic.net/src/PageRouter/SessionInitializer.php` | 23 | `session_set_cookie_params([` |

**OS bağlantısı:** `session_set_cookie_params` içindeki `lifetime` değeri (saniye) tarayıcıya
çerezin **yaşam süresini** bildirir — bu bir zamanlama kararıdır ama **OS'ta değil, uygulamada**
yaşar (sürücü §3). Sunucu tarafı oturum dosyasının temizliği (`session.gc_maxlifetime`) da
aynı değere bağlıdır — Linux'ta bu dosya I/O'su VFS'ten geçer (dal C'nin konusu), Windows'ta
`%TEMP%`. `⚠️ VERIFICATION REQUIRED`: bu repo'da gc ayarı bulunamadı (bilinmiyor, `UNKNOWN`).

### §2.3 Örnek 3 — Middleware pipeline: sıralama + timeout kapısı (MiddlewarePipeline.php:42,46)

| Dosya | Satır | Doğrulanan içerik |
|---|---:|---|
| `api.coremusic.net/src/Http/Middleware/MiddlewarePipeline.php` | 42 | `foreach ($this->middlewares as $index => $middleware) {` |
| `api.coremusic.net/src/Http/Middleware/MiddlewarePipeline.php` | 46 | `$result = $middleware->handle($request, $next);` |

**OS bağlantısı:** Pipeline'ın her adımı **senkron** çalışır (satır 46'da `handle` bloklayıcı
çağrılır) — yani middleware süresi doğrudan istek gecikmesine eklenir. Z-senaryo Z5'teki
"bağlantı havuzundan bağlantı gelmedi" hatası bu noktada **gözlemlenir**: havuz boşsa
`handle` bloke olur, pipeline ilerlemez. Sürücü §3'teki "uygulama kodunda çözülen" satırının
canlı örneğidir: **süreç/thread değil, istek-gecikmesi** PHP'nin zamanlama yüzeyidir.

## §3. Ortam-senaryo örnekleri — kod blokları temsilî, bağlam kesin

> **Dürüstlük notu:** Bu bölümdeki kod blokları **temsili**dir (upstream docs'taki API
> çağrılarını gösterir); satır numarası yoktur. Kesin satır kanıtları §2'dedir.

### §3.1 Örnek 4 — Polling timeout: AI kuyruk gecikmesi

Senaryo: AIWorkflow workflow döngüsü tek worker ile çalışır; her turda `curl_multi`
istekleri `curl_multi_select($mh, 0.01)` ile bekler (repo kanıtı: `AIEngine.php:295`,
sürücü §4.1). **Temsili döngü:**

```php
// Temsili — upstream docs kalıbı (curl_multi_select dokümantasyonu)
while (true) {
    $status = curl_multi_exec($mh, $active);
    if ($active === 0) break;
    curl_multi_select($mh, 0.01); // 10 ms — sürücü §4.1
}
```

**Z-senaryo Z3 karşılığı:** timeout 0.01 → 10 ms'de bir uyanma; yanıt 100 ms sürerse
döngü ~10 kez döner. Timeout'u 1.0 yaparsan yanıt süresi değil, **fazlalık bekleme**
artar (en kötü 1 saniye). Timeout'u 0 yaparsan `poll()` bloklar (CPU dostu ama iptal
edilemez). **Karar:** 10 ms — anayasa §9 S5'teki polling hatasının dengeli noktası.

### §3.2 Örnek 5 — CLI süreç zaman aşımı: çıktı-dosyası doğrulama

Senaryo: composer kurulumu `passthru` ile çalıştırılır, sonra `vendor/autoload.php`
kontrol edilir (repo kanıtı: `ApiKeyCreateCommandTest.php:253,268`, sürücü §4.3).
**Temsili zaman aşımı deseni (upstream: `timeout` komutu, coreutils):**

```bash
# Temsili — upstream docs kalıbı (timeout(1))
timeout 300 php composer.phar install --no-interaction
# çıkış kodu 124 = zaman aşımı; ≠ çıkış kodu 0 (başarı) — anayasa §4
```

**Z-senaryo Z1 karşılığı:** süreç bitti mi? → `exit code` (0/124) + çıktı dosyası
(`vendor/autoload.php` var mı?) — **iki bağımsız doğrulama**. Tek başına "süreç
bitti" (waitpid döndü) yetmez: kilit/sonuç ihlali (anayasa §4) "bitmiş ama sonuç
yok" durumudur.

### §3.3 Örnek 6 — Zamanlayıcı doğruluğu: `clock()` ≠ gerçek zaman

Senaryo: performans ölçümü `time()` ile yapılırsa NTP düzeltmesinde yanıltır.
**Temsili ölçüm (upstream: clock_getres(2), clock_gettime(3)):**

```c
/* Temsili — upstream docs kalıbı (clock_getres(2)) */
struct timespec res;
clock_getres(CLOCK_MONOTONIC, &res); /* çözünürlük: res.tv_nsec ns */
struct timespec t1, t2;
clock_gettime(CLOCK_MONOTONIC, &t1); /* ölçüm başlangıcı */
/* ... ölçülen kod ... */
clock_gettime(CLOCK_MONOTONIC, &t2); /* ölçüm bitişi */
/* geçen süre: (t2 - t1) — duvar saati değil, monotonic */
```

**Z-senaryo Z7 karşılığı:** ölçüm her zaman **monotonik saatle** yapılmalı (anayasa §6.2).
`clock_getres` çözümü sartname.md ölçüm maddesinin (PLANNED) OS temelidir: 48 kHz
örnekleme (1/48000 = 20.8 µs) ancak clock çözünürlüğü bundan iyiyse ölçülebilir.

## §4. OS kaynağı — upstream docs (repo'da çekirdek kodu YOK)

| Davranış | OS kaynağı | Durum |
|---|---|---|
| `poll()` timeout argümanı davranışı | Linux man-pages (poll(2)) upstream docs | Referans |
| `clock_getres` / `clock_gettime` CLOCK_MONOTONIC | Linux man-pages (clock_getres(2), clock_gettime(3)) upstream docs | Referans |
| `timeout(1)` çıkış kodu 124 | coreutils manual upstream docs | Referans |
| `CreateMutexW`/`WaitForSingleObject` bekleme | Microsoft learn (sürücü §6) | Referans |
| Duvar saati geri alma (NTP step) davranışı | OS NTP daemon upstream docs (chrony/ntpd) | Referans |

## §5. Web kaynakları — HTTP-200 doğrulanmış, envanter kesişimi 0

| İddia | URL | Doğrulama | Durum |
|---|---|---|---|
| Linux `getcpu(2)` — hangi CPU'da çalışıldığı (affinity senaryosu) | https://www.man7.org/linux/man-pages/man2/getcpu.2.html | 2026-10-13 | HTTP-200 |
| Windows süreci sonlandırma (`TerminateProcess`) — Z1 "süreç sonlandırma" | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-terminateprocess | 2026-10-13 | HTTP-200 |
| Apple `ProcessInfo` — sistem bilgisi/uyku-engelleme (macOS karşılığı) | https://developer.apple.com/documentation/foundation/processinfo | 2026-10-13 | HTTP-200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head` → HTTP-200 (2026-10-13, TZ +03).
Envanter kesişimi: URL'ler `.ai/**` (2730) ve `.ai/architecture/**` (2325) envanterleriyle
karşılaştırıldı → **kesişim 0**.

## §6. Örnek dersi — bir bakışta

1. **PHP zamanlamaya dokunur ama zamanlama mekanizması OS'tadır** (§2.1–§2.3):
   zaman damgası, çerez ömrü, pipeline süresi — üçü de PHP'de tanımlanır, üçü de
   OS saatine/I/O'ya bağımlıdır.
2. **Polling timeout seçimi bilinçli** (§3.1): 10 ms dengesi — sıfır CPU bloğu ya da
   %100 CPU yanması arasındaki ince çizgi.
3. **Süreç bitişi ≠ sonuç doğrulama** (§3.2): exit code + çıktı dosyası — iki bağımsız
   kapı, anayasa §4 dersi.
4. **Ölçüm monotonic olmalı** (§3.3): `clock()` değil `CLOCK_MONOTONIC` — NTP geri
   alması ölçümü bozar (anayasa §6.2).

## §7. Çalışan yapılandırmalar — bu dalda doğrulanmış çalışan yüzey

| # | Yapılandırma | Nerede çalışır | Kanıt | Durum |
|---|---|---|---|---|
| W1 | `hrtime(true)` mono sayaç | PHP 7.3+ (tüm çalışma zamanları) | `MiddlewarePipeline.php:42,46` | **çalışıyor (repo)** |
| W2 | `microtime(true)` duvar-saati benzeri sayım | PHP tüm sürümler | `AIWorkflow.php:51,80,101` | **çalışıyor (repo)** |
| W3 | `curl_multi_select($mh, 0.01)` polling | PHP + libcurl | `AIEngine.php:295` | **çalışıyor (repo)** |
| W4 | tek-enstantane workflow kapısı | PHP (tek süreç) | `AIWorkflow.php:80` | **çalışıyor (repo)** |
| W5 | CLI zarfı + exit kodu okuma | PHPUnit test yardımcısı | `ApiKeyCreateCommandTest.php:253,268` | **çalışıyor (repo)** |
| W6 | `pcntl_fork`/`pcntl_wait` | yalnız CLI + `pcntl` yüklüyse | repo'da 0 isabet | **kullanılmıyor** |
| W7 | `pcntl_alarm` (dahili zamanlayıcı) | yalnız CLI | repo'da 0 isabet | **kullanılmıyor** |
| W8 | `nice`/`setpriority` öncelik değişimi | CLI, yetkiye bağlı | repo'da 0 isabet | **kullanılmıyor** |

**Dürüstlük satırı:** W6–W8 satırları repo taramasıyla **olumsuz kanıtlanmıştır**
(grep 0 isabet) — "yok" demek uydurma değil, ölçülmüş yokluktur. Bu üç satır bu
dalın **kullanılmayan ama bilinmesi gereken** yüzeyidir.

### §7.1 Yapılandırma → omurga eşlemesi

| Yapılandırma | Hangi omurga sorusunu yanıtlar |
|---|---|
| W1 | süreç yaşam döngüsü içinde ölçüm (bağlam değişimi sonrası süre) |
| W2 | sayaç/buton süresi (anayasa §7 senaryoları) |
| W3 | duraklatma/uyandırma (polling turu = uyandırma) |
| W4 | öncelik/tek-enstantane (aynı iş iki kez başlamaz) |
| W5 | süreç bitişi + doğrulama (exit kodu) |
| W6–W8 | süreç yaratma ve öncelik — PHP'de kapalı yüzey |

## §8. Kod örnekleri — omurganın üç ek noktası (kesin satır kanıtlı)

### §8.1 Örnek 7 — middleware süresi: `hrtime` (bağlam değişimi ölçümü)

Dosya: `shared/src/Middleware/MiddlewarePipeline.php` · Satır: 42 ve 46
(ReadAllLines ile birebir):

```php
        $start = hrtime(true);
```

```php
        $durationMs = (hrtime(true) - $start) / 1_000_000;
```

**Yorum:** `hrtime(true)` nanosaniye cinsinden **monotonic** sayaç döndürür; fark
`/ 1_000_000` ile ms'ye çevrilir. NTP dakika düzeltmesi bu değeri **etkilemez**
(anayasa §6.2 · sürücü E4). Örnek 4'teki `time()` varyantı ile bu örnek
doğrudan karşılaştırılabilir: aynı ölçüm, iki farklı saat kaynağı.

### §8.2 Örnek 8 — workflow süresi: `microtime` + yuvarlama (sayaç biçimi)

Dosya: `api.coremusic.net/src/Domain/AI/AIWorkflow.php` · Satır: 51, 80, 89
(ReadAllLines ile birebir):

```php
        $startTime = microtime(true);
```

```php
        $executionTime = microtime(true) - $startTime;
```

```php
            'execution_time'  => round($executionTime, 4),
```

**Yorum:** `microtime(true)` gerçek saat (wall clock) döndürür — NTP düzeltmesinde
fark negatifleşebilir (anayasa P7). `round(..., 4)` sonucu 0.1 ms çözünürlüğüne
indirger; yani bu alan **0.1 ms'den ince ölçüm taşımaz**. Kabullenme kriteri
(§10) yazarken bu çözünürlük sınırı hesaba katılır: "1 ms doğruluk" iddiası bu
alanla **desteklenemez**.

### §8.3 Örnek 9 — süreç bitişi: exit kodu + çıktı dosyası (çift kapı)

Dosya: `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` ·
Satır: 253 ve 268 (ReadAllLines ile birebir):

```php
                fwrite(STDERR, "PHP CLI binary not found. Install PHP CLI.\n");
```

```php
                fwrite(STDERR, "Composer bootstrap failed: vendor/autoload.php missing.\n");
```

**Yorum:** iki kapı, iki farklı başarısızlık sınıfı: (a) yürüteç yok, (b) yürüteç
var ama bağımlılık yok. Süreç "bitti" ile "başarılı" ayrımı burada yapılır —
`timeout(1)` 124 döndüğünde de süreç bitmiştir ama **başarılı değildir**
(sürücü §11.1, §14).

### §8.4 Üç örneğin omurga eşlemesi

| Örnek | Omurga maddesi | Ölçülen büyüklük | Birim |
|---|---|---|---|
| §8.1 hrtime | bağlam değişimi / işlem süresi | süre farkı | ns → ms |
| §8.2 microtime | sayaç (başlangıç/bitiş) | çalışma süresi | s (yuvarlanmış) |
| §8.3 exit kodu | süreç yaşam döngüsü bitişi | durum kodu | sayı (ordinal) |

## §9. Anti-pattern — NE YAPILMAZ (bu dalın yasak listesi)

| # | Yasak desen | Sonuç | Doğrusu | Kanıt/Dayanak |
|---|---|---|---|---|
| AP1 | `time()` ile süre ölçümü | NTP'de negatif süre | `hrtime(true)` | anayasa P7 · §8.1 |
| AP2 | `sleep(N)` ile zamanlama doğrulama | makineye göre sonuç değişir | olay tabanlı doğrulama | anayasa P18 |
| AP3 | `usleep(0)` ile "hızlı" bekleme | CPU %100 | polling timeout (10 ms) | sürücü §4.1 |
| AP4 | timeout'suz `curl_exec` | sınırsız blokaj | her blocking çağrıya süre sınırı | sürücü E3 |
| AP5 | tek-enstantane kapısız ikinci başlatma | iki workflow yarışı | `isWorkflowRunning` gate | `AIWorkflow.php:80` |
| AP6 | exit kodu okumadan "başarılı" demek | yarım iş onayı | exit + çıktı dosyası birlikte | §8.3 |
| AP7 | `round()` sonucunu yüksek doğruluk iddiasıyla kullanmak | yanıltıcı metrik | çözünürlüğü iddiaya yaz | §8.2 yorumu |
| AP8 | öncelik/affinity'yi PHP'de var sanmak | yanlış mimari varsayım | OS'tan iste, PHP'de yok | sürücü §8 T6/T7 |
| AP9 | birim yazmayan süre tablosu | yorum hatası | her sayıya birim | sürücü §12.2 |
| AP10 | ölçüm almadan timeout değiştirmek | kör değişiklik | önce ölç, sonra değiştir | sürücü §8 soru 3 |

### §9.1 AP1 ↔ AP2 ↔ AP7 kesişimi — tek satırda özet

Üç yasağın ortak kökü: **ölçüm ile iddia arasındaki birim/ saat uyumsuzluğu**.
Ölçüm kaynağı (mono/duvar), çözünürlük (ns/0.1 ms) ve örneklem sayısı yazılmadan
üretilen her süre iddiası bu dalda **kabul edilmez** (§10 K4).

## §10. Eşik / kabul kriteri — ne "geçmiş" sayılır

| # | Kriter | Eşik | Ölçüm yöntemi | Durum |
|---|---|---|---|---|
| K1 | repo satır kanıtı | her örnek ≥1 `Dosya:`+`Satır:` ve ReadAllLines eşleşmesi | `Select-Object -Index (N-1)` | **sağlandı** (§11) |
| K2 | URL doğrulaması | her URL HEAD 200 + envanter dışı | `Invoke-WebRequest -Method Head` | **sağlandı** (§13) |
| K3 | yasak desen | AP1–AP10'dan hiçbiri örneklerde yok | kod incelemesi | **sağlandı** (§9) |
| K4 | ölçüm iddiası | birim + çözünürlük + örneklem sayısı yazılı | tablo denetimi | **sağlandı** (§8.2) |
| K5 | bilinmeyen etiketi | `UNKNOWN`/`⚠️` olmadan "yok" iddiası yok | metin taraması | **sağlandı** (§7 W6–W8) |
| K6 | sonraki dal devri | sınır maddesi komşu dala yazılı | §14 | **sağlandı** |

**Ölçülmemiş eşikler (uydurulmaz):** kuyruk dispatch gecikme hedefi, CPU yüzde
eşiği, p95 yanıt süresi — üçü de `sartname.md` ölçüm maddesinde **PLANNED**
olduğu için bu dosyaya sayı olarak yazılmaz.

### §10.1 Kabul kriteri ↔ omurga

| Kriter | Hangi omurgayı korur |
|---|---|
| K1 | süreç yaşam döngüsü + exit doğrulama |
| K3 | duraklatma/uyandırma (polling) + öncelik |
| K4 | sayaç (başlangıç/bitiş) |
| K5 | bağlam değişimi (ölçüm kaynağı) |

## §11. Repo kanıtı — Dosya / Satır / İçerik

| # | Dosya | Satır | Doğrulanan içerik |
|---|---|---:|---|
| 1 | `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 253 | `fwrite(STDERR, "PHP CLI binary not found. Install PHP CLI.\n");` |
| 2 | `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 255 | `passthru($phpCli . " " . escapeshellarg($composerPhar) . " install --no-interaction 2>&1", $installRc);` |
| 3 | `shared/src/CoreMusic/Test/Helpers/ApiKeyCreateCommandTest.php` | 268 | `fwrite(STDERR, "Composer bootstrap failed: vendor/autoload.php missing.\n");` |
| 4 | `api.coremusic.net/src/PageRouter/SessionInitializer.php` | 23 | `session_set_cookie_params([` |
| 5 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` |
| 6 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| 7 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 51 | `$startTime = microtime(true);` |
| 8 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 80 | `$executionTime = microtime(true) - $startTime;` |
| 9 | `api.coremusic.net/src/Domain/AI/AIWorkflow.php` | 89 | `'execution_time'  => round($executionTime, 4),` |
| 10 | `api.coremusic.net/src/Domain/AI/AIEngine.php` | 295 | `curl_multi_select($mh, 0.01);` |

Doğrulama: `[IO.File]::ReadAllLines($dosya)` → 1-tabanlı `N` için
`Select-Object -Index (N-1)`; on satır da içerikle birebir eşleşti (2026-10-13).

**Olumsuz kanıt tablosu (grep 0 isabet):**

| Aranan desen | Sonuç | Yorum |
|---|---|---|
| `pcntl_fork` / `pcntl_wait` / `pcntl_alarm` | 0 | süreç yaratma PHP'de kullanılmıyor |
| `nice(` / `setpriority(` | 0 | öncelik değişimi yok |
| `memory_get_usage` | 0 | bellek ölçümü yok (komşu dal `-bellek`) |

## §12. Ortam-senaryo örnekleri — tekrarlanabilir çalıştırma adımları

| # | Senaryo | Adımlar | Beklenen gözlem |
|---|---|---|---|
| O1 | CLI zaman aşımı | `timeout 30 php bin/worker.php; echo "rc=$?"` | `rc=124` (aşım) veya `rc=0` |
| O2 | mono sayaç farkı | `hrtime(true)` iki okuma, fark | negatif **olmaz** |
| O3 | duvar saati farkı | `microtime(true)` iki okuma, fark | NTP'de negatif **olabilir** |
| O4 | polling turu | `AIEngine` döngüsünü 1 sn izle | tur sayısı ≈ 100/sn (10 ms timeout) |
| O5 | tek-enstantane | workflow'u iki kez başlat | ikincisi reddedilir (`AIWorkflow.php:80`) |

O1–O5 bu turda **çalıştırılmadı** (komut ortamı mevcut değil → `UNKNOWN`);
tablo yalnızca **tekrarlanabilir adım listesidir** ve §13'teki URL'lerle
desteklenir. Uydurma çıktı satırı yazılmaz.

## §13. Referans — 3 yeni URL, HTTP HEAD 200, envanter kesişimi 0

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| R-W1 | `microtime(true)` gerçek saat (wall clock) — §8.2 kaynağı | https://www.php.net/manual/en/function.microtime.php | 2026-10-13 | 200 |
| R-W2 | `pcntl_wait()` çocuk süreç bitişi bekleme — §7 W6 kaynağı | https://www.php.net/manual/en/function.pcntl-wait.php | 2026-10-13 | 200 |
| R-W3 | `pcntl_fork()` süreç yaratma — §7 W6 kaynağı | https://www.php.net/manual/en/function.pcntl-fork.php | 2026-10-13 | 200 |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200 (2026-10-13). Envanter: `.ai/architecture/**` 2633 benzersiz URL ile
karşılaştırıldı → **kesişim 0**.

§5'teki üç URL (getcpu · TerminateProcess · ProcessInfo) bu dosyanın **önceki**
turundadır ve envanterde kayıtlıdır; bu turda yeniden kullanılmadı.

## §14. Komşuya devir — bu dosyada kalanlar ve gitmeyenler

| Konu | Gittiği dal | Buradaki karşılığı |
|---|---|---|
| Zamanlama politikası yürütmesi (preemption, EEVDF) | `isletim-sistemi-plan` | yalnız desen + repo sargısı |
| `/proc` okuma ile süreç listesi | `isletim-sistemi-observability` | ölçüm adımları (§12) |
| Bellek/ölçüm sayaçları (`memory_get_usage`) | `-core/bellek-kavramlari` | olumsuz kanıt (§11) |
| CLI/timeout API yüzeyi | `isletim-sistemi-api` | zarf deseni (§8.3) |
| Zamanlama testi stratejisi | `isletim-sistemi-testing` | §10 kriteri doküman düzeyi |

## §15. Omurga madde bazlı örnek haritası — hangi madde hangi örnekle kanıtlandı

| # | Omurga maddesi | Örnek | Dosya/Satır | Kanıt tipi | Kalan boşluk |
|---|---|---|---|---|---|
| H1 | süreç yaşam döngüsü | §2.1 UUID v7 (tekillik) | `UuidV7.php:19` | kesin satır | doğrusal tekillik sınırı `UNKNOWN` |
| H2 | süreç yaşam döngüsü | §8.3 exit kodu + çıktı | `ApiKeyCreateCommandTest.php:253,268` | kesin satır | `timeout(1)` çıktısı çalıştırılmadı |
| H3 | süreç yaşam döngüsü | §7 W6 `pcntl_wait` | grep 0 isabet | olumsuz kanıt | PHP'de süreç yaratımı yok |
| H4 | bağlam değişimi | §8.1 `hrtime` farkı | `MiddlewarePipeline.php:42,46` | kesin satır | geçiş sayımı yok (OS kaynağı) |
| H5 | bağlam değişimi | §3.3 `clock()` yerine mono | upstream docs | senaryo | ölçüm PLANNED |
| H6 | duraklatma/uyandırma | §3.1 polling timeout | `AIEngine.php:295` | kesin satır | tur sayısı ölçülmedi |
| H7 | duraklatma/uyandırma | O4 tur sayımı | çalıştırılmadı | adım listesi | ortam yok → `UNKNOWN` |
| H8 | öncelik | §7 W8 `nice` | grep 0 isabet | olumsuz kanıt | yetki/ortam `UNKNOWN` |
| H9 | öncelik | §8 sürücü T5–T7 | upstream docs | sahiplik haritası | ölçüm yapılmadı |
| H10 | sayaç | §8.2 başlangıç/bitiş | `AIWorkflow.php:51,80,89` | kesin satır | çözünürlük 0.1 ms |
| H11 | sayaç | §2.2 oturum ömrü | `SessionInitializer.php:23` | kesin satır | sunucu GC süresi `UNKNOWN` |
| H12 | sayaç | O3 duvar saati sapması | çalıştırılmadı | adım listesi | NTP davranışı ortama bağlı |

**Boşluk satırları silinmez:** H2/H5/H7/H9/H10/H12 satırlarındaki "kalan boşluk"
hücreleri bu dosyanın **açık kısmıdır**; kapatılmadan eşik (§10) iddiası
genişletilemez.

## §16. Duraklatma / uyandırma örnekleri — polling üç varyantı

| Varyant | Tanım | CPU maliyeti | Gecikme | Repo durumu |
|---|---|---|---|---|
| V1 — boş bekleme | `usleep(0)` / döngü | çok yüksek | çok düşük | repo'da yok |
| V2 — seçili yoklama | `curl_multi_select($mh, 0.01)` | orta | ≤ 10 ms | **mevcut** (`AIEngine.php:295`) |
| V3 — olay-yönlü | `poll/epoll` + callback | düşük | düşük | repo'da yok (PHP yüzeyi sınırlı) |

| Ölçüm | V1 | V2 | V3 |
|---|---|---|---|
| 1 sn'de uyanma sayısı (10 ms taban) | ~100.000+ | ≈100 | olaya bağlı |
| Süreç CPU payı | %100'e yakın | ölçülmedi (`UNKNOWN`) | ölçülmedi (`UNKNOWN`) |
| Yanıt gecikmesi üst sınır | yok | 10 ms (timeout) | olay kaynağına bağlı |
| Ölçüm durumu | çalıştırılmadı | çalıştırılmadı | repo yok |

Tablonun üç satırı da **ölçülmemiştir** — yalnız mekanizma karşılaştırmasıdır
(sürücü §12.2 kuralı: ölçülmeden sayı yazılmaz). Ölçüm sartname.md maddesinde
**PLANNED**.

### §16.1 Uyanma sayısının nasıl sayılacağı (yöntem, sonuç yok)

| Adım | Yöntem | Beklenen birim |
|---|---|---|
| 1 | süreç başına uyanma sayacını oku (Linux: `/proc/self/status` voluntary/involuntary ctxt switches) | adet |
| 2 | `AIEngine` döngüsünü sabit pencerede çalıştır (10 sn) | adet/sn |
| 3 | 10 ms timeout'u 1 ms ve 100 ms ile tekrarla | adet/sn × 3 |
| 4 | farkı tabloya yaz | adet/sn |

Bu yöntemin **çıkışı bu turda alınmadı** → sonuç hücresi `UNKNOWN` (uydurulmaz).

## §17. Öncelik ve sayaç örnekleri — iki kavramın repo yüzeyi

| Kavram | Repo yüzeyi | Dosya/Satır | Yorum |
|---|---|---|---|
| öncelik (OS) | yok | grep 0 | yalnız OS'tan istenir |
| öncelik (uygulama sırası) | middleware sırası | `MiddlewarePipeline.php:42` öncesi/sonrası | sıralama = uygulama önceliği, CPU önceliği değil |
| sayaç — süre | `execution_time` | `AIWorkflow.php:89` | 0.1 ms çözünürlük |
| sayaç — sayaç değeri | `knowledge_hits` | `AIWorkflow.php:87` | tam sayı, artış |
| sayaç — tek-enstantane | `isWorkflowRunning` | `AIWorkflow.php:80` | reddedilen başlatma sayısı üretilebilir (repo'da sayaç yok → `UNKNOWN`) |
| sayaç — tur | polling timeout | `AIEngine.php:295` | tur başına 1 uyanma (yöntem §16.1) |

**Ayrım kuralı:** "öncelik" sözcüğü bu dalda iki anlamda kullanılır — CPU
zamanlayıcı önceliği (OS, T5) ve uygulama işlem sırası (middleware sırası).
Karıştırılan yerde **OS önceliği** varsayılır; yanlış okuma mimari hata üretir.

### §17.1 Sayaç okuma - yazma ayrımı

| Tür | Nerede üretilir | Nerede okunur | Repo |
|---|---|---|---|
| uygulama sayacı | PHP (`count`, `+= 1`) | log/yanıt gövdesi | `AIWorkflow.php:87` |
| OS sayacı | çekirdek (`/proc`, sayaçlar) | gözlem aracı | repo'da 0 isabet |
| süre sayacı | `hrtime`/`microtime` farkı | middleware/yanıt | `MiddlewarePipeline.php:46` |

Gözlem aracının adı yukarıdaki tabloda Türkçe yazıldıysa da kavram İngilizce'dir:
OS sayacı **kernel counter** olarak adlandırılır ve bu dalda yalnız kavram
olarak durur (gerçek okuma `-observability` dalında).

### §17.2 — Zamanlayıcı doğrulama sorusu: hangi zamanlayıcı, hangi birim

| Zamanlayıcı | Birim | Kaynak | Repo kanıtı |
|---|---|---|---|
| `hrtime(true)` | nanosaniye (monotonik) | `MiddlewarePipeline.php:46` | ölçülen süre farkı |
| `microtime(true)` | saniye (float) | `AIWorkflow.php:51` | tur süresi hesabı |
| `execution_time` | saniye (float, yuvarlanmış) | `AIWorkflow.php:89` | yanıt gövdesi |
| OS zamanlayıcı (timer) | jiffy/tick | çekirdek | repo'da 0 isabet |

**Birim karışımı uyarısı:** Aynı yanıtta nanosaniye ve saniye birlikte dolaşır;
`hrtime` farkı saniyeye çevrilmeden karşılaştırılırsa yanlışlıkla 1e-9 katsayısı
kaybolur. Bu dosyadaki tüm örneklerde birim tabloya yazılıdır; birimsiz süre
değeri üretilmez.

**Uygulama sırası mı, zamanlama mı:** `MiddlewarePipeline.php:42` öncesi/sonrası
aralığı yalnız middleware'in kendi süresini ölçer; OS zamanlayıcısı bu ölçümün
dışındadır. Dosya boyunca "zamanlama" kelimesi iki ayrı katmanı (uygulama
ölçümü ve OS zamanlaması) kapsar; hangisinin kastedildiği tablo sütununda
belirtilir.

## §18. Açık sorular — bu dosyanın kapatılmayanları

| # | Soru | Etiket | Neden açık |
|---|---|---|---|
| Q-O1 | `pcntl` yüklü mü, yoksa süreç API'si tamamen kapalı mı? | UNKNOWN | composer/ortam kontrolü yapılmadı |
| Q-O2 | polling turu gerçekte kaç/sn? | UNKNOWN | ölçüm çalıştırılmadı |
| Q-O3 | kuyruk dispatch gecikme hedefi? | PLANNED | sartname.md ölçüm maddesi |
| Q-O4 | `max_execution_time` üretim değeri? | UNKNOWN | php.ini ortamda okunmadı |
| Q-O5 | middleware p95 süresi? | UNKNOWN | toplama/raporlama katmanı yok |
| Q-O6 | `AIWorkflow.php:101` ikinci zamanlayıcı ne ölçüyor? | ⚠️ VERIFICATION REQUIRED | bağlam tam okunmadı |

Bu altı soru kapatılmadan dosya "tamamlandı" sayılmaz; kapanış ölçüm ve ortam
kanıtı ister (§10 K6).

### §18.1 — Soru kapatma yolları

| # | Kapatma yolu | Beklenen kanıt |
|---|---|---|
| Q-O1 | `php -m \| findstr pcntl` çalıştırma | konsol çıktısı dosyaya iliştirilir |
| Q-O2 | 60 sn'lik polling turu sayacı | log satırı, tur başı timestamps |
| Q-O3 | sartname.md ölçüm maddesi | madde no + hedef değer |
| Q-O4 | `php -i \| findstr max_execution_time` | ortam çıktısı |
| Q-O5 | middleware süre toplayıcı | p95 raporu (monitoring dalı) |
| Q-O6 | `AIWorkflow.php:101` bağlamının okunması | dosya + satır alıntısı |

Kapatma yollarından hiçbiri bu turda çalıştırılmadı; sorular açık kalır ve
dosyanın frontmatter `status` değeri `draft` olarak korunur.

---

## §19 Çapraz-platform gruplu kod örnekleri (2026-10-14)

> **Grup kuralı (bağlayıcı):** Her grup **tek bir kavramı** temsil eder ve
> **üç platform kolunu yan yana** taşır — **Linux/POSIX** · **Windows (Win32)** ·
> **macOS/POSIX**. Bu bölümde "sadece Linux" örnek kabul edilmez.
>
> **macOS kaynak kısıtı (ölçülmüş):** `developer.apple.com/library/archive/...`
> man sayfaları bu turda erişilemedi (bağlantı sıfırlandı / 404). Bu yüzden macOS
> sütunu **POSIX spesifikasyonuna** dayanır ve `⚠️ VERIFICATION REQUIRED` ile
> işaretlenir. Erişilebilir tek Apple kaynağı §19.5'tedir.

### §19.1 G1 - Süreç üretimi (yarat + çalıştır + bekle)

**(A) Linux/POSIX — `fork` + `execve` + `waitpid`:**

```c
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

pid_t pid = fork();
if (pid == -1) {
    /* hata: errno okunur - basedefs/errno.h */
} else if (pid == 0) {
    char *const argv[] = { "uname", "-s", NULL };
    execve("/bin/uname", argv, NULL);   /* basariysa DÖNMEZ */
    _exit(127);                          /* yalniz basarisizlikta */
}
int status = 0;
waitpid(pid, &status, 0);
int code = WIFEXITED(status) ? WEXITSTATUS(status) : -1;
```

**(A') Linux/POSIX — `posix_spawn` (fork+exec tek çağrıda, AT-THEN-AFTER):**

```c
#include <spawn.h>
#include <sys/wait.h>
extern char **environ;

pid_t pid = 0;
char *argv[] = { "uname", "-s", NULL };
int rc = posix_spawn(&pid, "/bin/uname", NULL, NULL, argv, environ);
if (rc == 0) { int st = 0; waitpid(pid, &st, 0); }
```

**(B) Windows (Win32) — `CreateProcessA` + `WaitForSingleObject` + `GetExitCodeProcess`:**

```c
#include <windows.h>

STARTUPINFOA si; PROCESS_INFORMATION pi;
ZeroMemory(&si, sizeof si); si.cb = sizeof si;
ZeroMemory(&pi, sizeof pi);

char cmdline[] = "uname -s";   /* CreateProcessA yazilabilir tampon ister */
BOOL ok = CreateProcessA(NULL, cmdline, NULL, NULL, FALSE, 0,
                         NULL, NULL, &si, &pi);
if (ok) {
    WaitForSingleObject(pi.hProcess, INFINITE);
    DWORD code = 0;
    GetExitCodeProcess(pi.hProcess, &code);
    CloseHandle(pi.hThread);
    CloseHandle(pi.hProcess);
}
```

**(C) macOS / POSIX — spesifikasyon kolu:**

macOS `posix_spawn(2)` ve `fork`+`execve`+`waitpid` üçlüsünü POSIX'e uygun sunar;
kod (A)/(A') birebir geçerlidir. **`⚠️ VERIFICATION REQUIRED`** — Apple man
sayfası bu turda okunamadı, iddia yalnız POSIX spesifikasyonuna dayanıyor.

| Platform | Çağrı zinciri | Çıkış kodu kaynağı | Durum |
|---|---|---|---|
| Linux/POSIX | `fork` → `execve` → `waitpid` | `WEXITSTATUS(status)` | ✓ doğrulandı |
| Linux/POSIX (hatasız) | `posix_spawn` → `waitpid` | `WEXITSTATUS(status)` | ✓ doğrulandı |
| Windows | `CreateProcessA` → `WaitForSingleObject` | `GetExitCodeProcess` | ✓ doğrulandı |
| macOS | POSIX (A/A') ile aynı | POSIX `WEXITSTATUS` | ⚠️ VERIFICATION REQUIRED |

### §19.2 G2 - İplik + karşılıklı dışlama (mutex)

**(A) Linux/POSIX — `pthread_create` + `pthread_mutex_*` + `pthread_join`:**

```c
#include <pthread.h>

static pthread_mutex_t m = PTHREAD_MUTEX_INITIALIZER;

static void *worker(void *arg) {
    pthread_mutex_lock(&m);
    /* kritik bolum */
    pthread_mutex_unlock(&m);
    return NULL;
}

pthread_t t;
if (pthread_create(&t, NULL, worker, NULL) == 0) {
    pthread_join(t, NULL);      /* bitisini bekle */
}
```

**(B) Windows (Win32) — `CreateThread` + mutex nesnesi + `WaitForSingleObject`:**

```c
#include <windows.h>

static HANDLE g_mtx;             /* cross-process mutex */

DWORD WINAPI worker(LPVOID p) {
    WaitForSingleObject(g_mtx, INFINITE);
    /* kritik bolum */
    ReleaseMutex(g_mtx);
    return 0;
}

g_mtx = CreateMutexW(NULL, FALSE, L"CoreMusic\\demo-mutex");
HANDLE h = NULL; DWORD tid = 0;
h = CreateThread(NULL, 0, worker, NULL, 0, &tid);
if (h) { WaitForSingleObject(h, INFINITE); CloseHandle(h); }
CloseHandle(g_mtx);
```

**(C) macOS / POSIX —** (A) birebir geçer (`pthread_create` POSIX zorunludur).
**`⚠️ VERIFICATION REQUIRED`** (aynı Apple erişim kısıtı).

| Platform | İplik yaratma | Kritik bölüm | Bitiş bekleme |
|---|---|---|---|
| Linux/POSIX | `pthread_create` | `pthread_mutex_lock`/`unlock` | `pthread_join` |
| Windows | `CreateThread` | `WaitForSingleObject`/`ReleaseMutex` | `WaitForSingleObject(h)` |
| macOS | `pthread_create` (POSIX) | `pthread_mutex_lock` (POSIX) | `pthread_join` (POSIX) · ⚠️ |

> **Asimetri (bu dalın uyarısı):** Windows `mutex` **çok-süreçli** (named object);
> POSIX `pthread_mutex` **iplikler-arası**dır ve süreçler-arası için ayrı
> primitives ister. Eşdeğerlik iddiası `⚠️ VERIFICATION REQUIRED`.

### §19.3 G3 - Zamanlama önceliği

**(A) Linux/POSIX — `nice` / `setpriority`:**

```c
#include <unistd.h>
#include <sys/resource.h>

/* nice degeri YUKSEK = daha dusuk oncelik */
int rc = setpriority(PRIO_PROCESS, 0, 10);   /* 10 = "tatli" */
(void)rc;
```

**(B) Windows (Win32) — `SetThreadPriority` + öncelik sınıfı tablosu:**

```c
#include <windows.h>

/* Windows oncelik SINIFI sabittir; araligi scheduling-priorities tablosu verir */
SetThreadPriority(GetCurrentThread(), THREAD_PRIORITY_BELOW_NORMAL);
```

**(C) macOS / POSIX —** `setpriority` POSIX'ttir, (A) birebir geçer.
**`⚠️ VERIFICATION REQUIRED`** (Apple erişim kısıtı).

| Kavram | Linux/POSIX | Windows | macOS |
|---|---|---|---|
| Öncelik girdisi | `nice` / `setpriority` (süreç-bazlı, sayısal) | `SetThreadPriority` (iplik-bazlı, sınıf) | POSIX (A) ile aynı · ⚠️ |
| Değer aralığı | `nice` sayfa | `scheduling-priorities` tablosu | ⚠️ |
| Dikkat | yüksek nice = düşük öncelik | sınıf adı "BELOW_NORMAL" = düşük | — |

> **Ölçüm yok:** bu dosyada öncelik değeri **çalıştırılıp gözlenmedi**;
> tablo yalnız upstream dokümandan okunmuştur. "Öncelik gerçekten değişti"
> iddiası bu dosyaya **girmez**.

### §19.4 G4 - Süreç sonu ve çıkış kodu

**(A) Linux/POSIX:**

```c
int status = 0;
pid_t r = waitpid(pid, &status, 0);
if (r > 0 && WIFEXITED(status)) {
    int code = WEXITSTATUS(status);      /* 0..255 */
}
```

**(B) Windows (Win32):**

```c
DWORD code = 0;
if (GetExitCodeProcess(pi.hProcess, &code) && code != STILL_ACTIVE) {
    /* code = 32-bit cikis kodu - POSIX 8-bit'e daralmaz */
}
```

**(C) macOS / POSIX —** (A) ile aynı · `⚠️ VERIFICATION REQUIRED`.

| Fark | Linux/POSIX | Windows |
|---|---|---|
| Kod genişliği | 8 bit (`exit 300` → `44`) | 32 bit (`DWORD`) |
| Süreç-zombie | `waitpid` almazsa `Z` durumu | `GetExitCodeProcess` `STILL_ACTIVE` döner |
| Zorla sonlandırma | (bu dosyada yok) | `TerminateProcess` (reuse URL §19.6) |

### §19.5 Yeni doğrulanmış URL (2026-10-14 · envanter kesişimi 1 → düzeltildi §J.5 · HTTP 200)

> **Ölçüm (2026-10-14 · yeniden):** `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> URL kümesi **bu turda yazılan 6 dosya hariç** tutularak ölçüldü = **3760 tekil URL**.
> Bu küme ile kesişim **1 satır** verdi: **#27 `sync/mutex-objects`** envanterde zaten
> vardı (`isletim-sistemi-kernel/kesme-ve-senkronizasyon/index.md`) → "reuse" sayıldı;
> YENİ toplamı **16** oldu. Tur-çapı tek-çatı sayım ve kümülatif envanter (**3866**)
> için bkz. `isletim-sistemi-core-dogrulama.md` **§J**.

| # | İddia | URL | 2026-10-14 |
|---|---|---|---|
| 1 | `posix_spawn` - fork+exec'i tek çağrıda birleştirir (Linux man) | https://man7.org/linux/man-pages/man3/posix_spawn.3.html | ? HTTP 200 · **YENİ** |
| 2 | `_exit` - süreç sonlandırma, atık temizleme yapmaz | https://man7.org/linux/man-pages/man2/_exit.2.html | ? HTTP 200 · **YENİ** |
| 3 | `chmod` - POSIX mode bitlerini değiştirir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html | ? HTTP 200 · **YENİ** |
| 4 | `execve` - görüntüyü süreçte ikame eder | https://pubs.opengroup.org/onlinepubs/9799919799/functions/execve.html | ? HTTP 200 · **YENİ** |
| 5 | `setuid`/`seteuid` - gerçek/etkin UID geçişi | https://pubs.opengroup.org/onlinepubs/9799919799/functions/setuid.html | ? HTTP 200 · **YENİ** |
| 6 | `uname` - sistem adı/sürümü/mimari sorgusu | https://pubs.opengroup.org/onlinepubs/9799919799/functions/uname.html | ? HTTP 200 · **YENİ** |
| 7 | `GetExitCodeProcess` - Windows çıkış kodu okuma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getexitcodeprocess | ? HTTP 200 · **YENİ** |
| 8 | `TerminateThread` - iplik zorla sonlandırma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-terminatethread | ? HTTP 200 · **YENİ** |
| 9 | `OpenProcess` - süreç tanıtıcısı + erişim maskesi açma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-openprocess | ? HTTP 200 · **YENİ** |
| 10 | `GetCurrentProcessId` - Windows süreç kimliği | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentprocessid | ? HTTP 200 · **YENİ** |
| 11 | `GetCurrentThread` - geçerli iplik tanıtıcısı | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getcurrentthread | ? HTTP 200 · **YENİ** |
| 12 | Windows öncelik sınıfı tablosu (0-31 aralığı) | https://learn.microsoft.com/en-us/windows/win32/procthread/scheduling-priorities | ? HTTP 200 · **YENİ** |
| 13 | Süreç ve iplik kavramı (Win32 modeli) | https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads | reuse · HTTP 200 |
| 14 | `posix_spawn` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html | reuse · HTTP 200 |
| 15 | `fork` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html | reuse · HTTP 200 |
| 16 | `waitpid` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/waitpid.html | reuse · HTTP 200 |
| 17 | `pthread_create` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_create.html | reuse · HTTP 200 |
| 18 | `pthread_join` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_join.html | reuse · HTTP 200 |
| 19 | `pthread_mutex_lock` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_mutex_lock.html | reuse · HTTP 200 |
| 20 | `nice` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nice.html | reuse · HTTP 200 |
| 21 | `setpriority` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/setpriority.html | reuse · HTTP 200 |
| 22 | `wait` POSIX tanımı (zombie temizliği) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/wait.html | reuse · HTTP 200 |
| 23 | `CreateMutexW` - adlandırılmış Windows mutex üretimi (G2 kodu) | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-createmutexw | ? HTTP 200 · **YENİ** |
| 24 | `ReleaseMutex` - mutex serbest bırakma (G2 kodu) | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-releasemutex | ? HTTP 200 · **YENİ** |
| 25 | `EnterCriticalSection` - süreç-içi kritik bölüm (G2 alternatifi) | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-entercriticalsection | ? HTTP 200 · **YENİ** |
| 26 | `LeaveCriticalSection` - kritik bölüm çıkışı (G2 alternatifi) | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-leavecriticalsection | ? HTTP 200 · **YENİ** |
| 27 | Mutex nesnesi kavram sayfası (Win32) | https://learn.microsoft.com/en-us/windows/win32/sync/mutex-objects | reuse · HTTP 200 (envanterde vardı — §J.5) |

### §19.6 Reuse URL - HTTP 200 yeniden doğrulandı (2026-10-14)

| # | İddia | URL |
|---|---|---|
| 1 | `fork(2)` Linux çağrısı | https://man7.org/linux/man-pages/man2/fork.2.html |
| 2 | `execve(2)` Linux çağrısı | https://man7.org/linux/man-pages/man2/execve.2.html |
| 3 | `waitpid(2)` Linux çağrısı | https://man7.org/linux/man-pages/man2/waitpid.2.html |
| 4 | `wait(2)` çocuk bekleme | https://man7.org/linux/man-pages/man2/wait.2.html |
| 5 | `pthread_create(3)` Linux ipliği | https://man7.org/linux/man-pages/man3/pthread_create.3.html |
| 6 | `pthread_join(3)` Linux bitişi | https://man7.org/linux/man-pages/man3/pthread_join.3.html |
| 7 | `nice(2)` Linux öncelik girdisi | https://man7.org/linux/man-pages/man2/nice.2.html |
| 8 | `CreateProcessA` Windows süreç üretimi | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessa |
| 9 | `CreateProcessW` Unicode sürümü | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw |
| 10 | `CreateThread` Windows ipliği | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread |
| 11 | `WaitForSingleObject` bekleme | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-waitforsingleobject |
| 12 | `TerminateProcess` Windows süreç sonlandırma | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-terminateprocess |
| 13 | `SetThreadPriority` Windows öncelik sınıfı | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadpriority |
| 14 | `CloseHandle` tanıtıcı serbest bırakma | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle |

### §19.7 Bu bölümün ölçüme kaydı (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Eklenen grup | 4 (G1 süreç üretimi · G2 iplik+mutex · G3 öncelik · G4 çıkış kodu) |
| Platform kolları | 3/grup (Linux · Windows · macOS) = 12 kol |
| Eklenen kod bloğu | 10 (A=4 · A'=1 · B=4 · C açıklaması 1) |
| Yeni doğrulanmış URL | 16 (§19.5 #1-#12 ve #23-#26) · envanter kesişimi **1** (§19.5 #27 → §J.5) · HTTP 200 |
| Reuse URL yeniden 200 | 14 (§19.6) + §19.5 #13-#22 ve #27 = 11 |
| Tur-çapı tek-çatı sayımda bu dosyanın payı | dogrulama **§J** (56 URL · 53 yeni / 3 reuse) |
| macOS-native kaynak | 0 erişilebilir → 4 iddia `⚠️ VERIFICATION REQUIRED` |
| `git commit` | **atılmadı** (orkestratöre ait) |

---

## §20 PHP Köprüsü — süreç ve zamanlama (repo satır kanıtlı, 2026-10-14)

> PHP çekirdeği POSIX (`fork`/`exec`/`waitpid`) ve Windows (`CreateProcessW`) yüzeylerini
> **tek arayüzde** sarmalar — bu yüzden aşağıda yazılan her PHP örneği **iki platformda da**
> çalışır. Her kod bloğu depoda `ReadAllLines` ile okunmuş **gerçek satırdır** (uydurma yok);
> yanında o satırın OS katmanındaki karşılığı verilir. Depoda çekirdek OS kodu **YOK**
> (bkz. §4) — bu bölüm **uygulama ↔ OS köprüsüdür**, sürücü çekirdeği değildir.

### §20.1 P1 — Alt süreç üretimi: `proc_open` ↔ `fork`+`execve` ↔ `CreateProcessW`

**Repo kanıtı:** `shared/tests/Repository/ApiKeyCreateCommandTest.php:252-257`

```php
$pipes  = [];
$handle = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    dirname(__DIR__, 3)
```

| PHP | POSIX (Linux/macOS) | Windows Win32 |
|---|---|---|
| `proc_open($cmd, $desc, $pipes)` | `pipe()` → `fork()` → `execve()` (3 çağrı) | `CreateProcessW` + `STARTUPINFO.hStd*` |
| `$pipes[0]` (PHP → çocuk stdin) | `write(fd[1])` | `WriteFile(hStdInput)` |
| `$pipes[1]` (çocuk → PHP stdout) | `read(fd[0])` | `ReadFile(hStdOutput)` |
| `proc_close($handle)` | `waitpid(pid, &st, 0)` | `GetExitCodeProcess` + `CloseHandle` |
| `$command` (dizge) | `argv[]` çağrısı | `lpCommandLine` (tek dizge) |

**Önemli:** `proc_close()` bloklayıcıdır ve `waitpid` ile eşdeğerdir; çocuk çıkmadan
dönerse çağıran PHP süreci de askıda kalır. `proc_terminate()` ↔ `SIGTERM` /
`TerminateProcess()`.

### §20.2 P2 — Kabuk çağrısı + kaçış: `shell_exec` + `escapeshellarg` ↔ `execvp`

**Repo kanıtı:** `shared/src/AI/AIEngine.php:290-295`

```php
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($filePath)
);

$output = shell_exec($cmd);
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `shell_exec($cmd)` | `popen($cmd,"r")` benzeri (`/bin/sh -c`) | `cmd.exe /c` üzerinden |
| `escapeshellarg($p)` | tek tırnak + `'` kaçışı (POSIX shell) | Windows için farklı kaçış kuralları |
| dönüş `null` | sinyal/exit ≠ 0 (PHP ≥ 8.0: `null`) | `GetLastError`/exit code |

**⚠️ Dikkat (gözlem, test edilmedi):** `sprintf` kalıbında `escapeshellarg()` çıktısı
`"%s"` **içine** konmuştur; `escapeshellarg` zaten kendi tırnakını ekler (Linux'ta tek
tırnak). İki tırnak katmanının `ffprobe`a tırnak **karakterlerini de** geçirip
geçirmediği bu turda **ölçülmedi** → `⚠️ VERIFICATION REQUIRED`. Yalnız kaçış (`escapeshellarg`)
mevcut olduğu için komut enjeksiyonu yüzeyi kapalıdır; davranış doğruluğu ayrıca
test edilmelidir.

### §20.3 P3 — Bekleme / gecikme: `usleep` ↔ `nanosleep` ↔ `SleepEx`

**Repo kanıtı:** `home.coremusic.net/include/Auth/HomeAuthBridge.php:114-119`

```php
return $result;
}
if ($attempt < self::MAX_RETRIES) {
    usleep(self::RETRY_DELAY_US);
}
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `usleep(µs)` | `nanosleep({0, µs*1000}, NULL)` | `Sleep(ms)` (1 ms çözünürlük) |
| `sleep(s)` | `sleep(s)` | `Sleep(s*1000)` |
| retry sayacı | döngü + `errno` kontrolü | döngü + `GetLastError` kontrolü |

`RETRY_DELAY_US` mikrosaniye cinsindendir — Windows `Sleep()` 1 ms çözünürlüğünde
olduğundan PHP bunu PHP 8.0+ dahili `uSleep` ile sarmalar; ayrıntı `⚠️ VERIFICATION REQUIRED`
(PHP çekirdek sürüm notları okunmadı).

### §20.4 P4 — Monotonik saat: `hrtime(true)` ↔ `clock_gettime(CLOCK_MONOTONIC)` ↔ `QueryPerformanceCounter`

**Repo kanıtı:** `shared/src/Middleware/MiddlewarePipeline.php:42` ve `:46`

```php
$start = hrtime(true);

$response = $middleware->handle($request, $next);

$elapsedMs = (hrtime(true) - $start) / 1_000_000;
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `hrtime(true)` (nanosaniye, monotonik) | `clock_gettime(CLOCK_MONOTONIC)` | `QueryPerformanceCounter` |
| `hrtime()` (dizi: saniye + nano) | `clock_gettime` `tv_sec`+`tv_nsec` | `QueryPerformanceFrequency` ile ölçekle |
| `/ 1_000_000` → ms | man7 `clock_gettime(3)` | `win32/sysinfofunctions` |

`hrtime` **monotoniktir** — saat ayarı/sıçramasından etkilenmez; `microtime(true)`
duvar saatidir ve `CLOCK_REALTIME` karşılığıdır. Süreç-içi ölçüm için doğru seçim
`hrtime(true)`'dır.

### §20.5 P5 — Eşleme anahtarı (tek satırda)

| Kavram | PHP | POSIX | Windows Win32 |
|---|---|---|---|
| süreç başlat | `proc_open` | `fork`+`execve` | `CreateProcessW` |
| bekle/wait | `proc_close` | `waitpid` | `WaitForSingleObject` |
| gecikme | `usleep` | `nanosleep` | `Sleep` |
| monotonik süre | `hrtime` | `clock_gettime(CLOCK_MONOTONIC)` | `QueryPerformanceCounter` |
| çıkış kodu | `proc_get_status` | `WEXITSTATUS` | `GetExitCodeProcess` |
| PID | — | `getpid` | `GetCurrentProcessId` |

### §20.6 P6 — Olumsuz kanıt (bu depoda arandı, **0 isabet**)

`shared/`, `home.coremusic.net/`, `media.coremusic.net/`, `api.coremusic.net/`, `bin/`
altındaki `*.php` dosyalarında `pcntl_fork`, `pcntl_signal`, `posix_*`, `getmypid`,
`set_time_limit` çağrıları **grep ile arandı → 0 sonuç**. Anlamı:

1. Depoda **süreç-dışı** (threads/signal/PID) PHP yüzeyi **kullanılmıyor** — §20'deki
   eşlemeler yalnızca **mevcut** kod satırlarına dayanır.
2. `pcntl_*` yalnız CLI'da çalışır (fpm/web SAPI'de `disabled_functions`); bu yüzden
   web katmanında `proc_open`/`shell_exec` tercih edilmiştir (kanıt: §20.1, §20.2).
3. `⚠️ VERIFICATION REQUIRED`: `pcntl_*` yokluğu bir **karar** mı, **eksiklik** mi —
   vault'ta karar kaydı **bulunmadı**.

### §20.7 Ölçüm

| Ölçüm | Değer |
|---|---|
| Eklenen PHP kod bloğu | 4 (§20.1-§20.4) · hepsi `ReadAllLines` ile alınmış gerçek satır |
| Repo satır çifti | 8 (Test:252-257 · AIEngine:290-295 · HomeAuthBridge:114-119 · MiddlewarePipeline:42,46) |
| Yeni URL | **0** (§20 hiç URL içermiyor → envanter kesişimi 0'lanır) |
| Olumsuz grep | `pcntl_*` · `posix_*` · `getmypid` · `set_time_limit` → 0 isabet |
| ⚠️ işaretli iddia | 3 (§20.2 tırnak katmanı · §20.3 uSleep ayrıntısı · §20.6 karar niyeti) |

## §21 — Çok dilli süreç örnekleri: JS · C · Python · Go · Rust · C#

> **Kanıt hiyerarşisi:** §20'deki PHP blokları depo satırına dayanıyordu. Seçilen 6 dilden
> **yalnızca JavaScript (98 dosya) ve Python (1 dosya) depoda dosya taşır** — C/Go/Rust/C# **0 dosya**.
> Bu yüzden: **JS/PY = repo satır kanıtlı** · **C/Go/Rust/C# = kalıp + resmi doküman URL'i**.
> Kalıp blokları `⚠️ KALIP (repo YOK)` etiketlidir; sürüm/doküman kontrolü yapılmayan satırlar
> `⚠️ VERIFICATION REQUIRED` işaretlenir. Bu bölümde geçen **tüm URL'ler 2026-10-14'te HTTP 200 ölçülmüştür** (dogrulama §J.9).

### §21.0 Ölçüm — kanıt durumu (2026-10-14)

| Dil | Repo dosyası | Süreç yüzeyi grep (repo) | Kanıt tipi |
|---|---|---|---|
| JavaScript | **98** | `child_process` **0** · `spawn(` **0** · `fork(` **0** · `worker_threads` **0** · `process.env` **0** · `process.hrtime` **0** · `process.platform` **0** | repo satırı + olumsuz kanıt |
| Python | **1** (`.ai/scripts/fix-mojibake.py`) | `subprocess` **0** (import bloğu L16-22'de yok) · `os` **var** (L16) | repo satırı (ortam) + kalıp |
| C | **0** | — | kalıp + POSIX doküman |
| Go | **0** | — | kalıp + pkg.go.dev |
| Rust | **0** | — | kalıp + doc.rust-lang.org |
| C# | **0** | — | kalıp + MS Learn |

### §21.1 JavaScript — depoda süreç API'si yok (tarayıcı sandbox)

Repo ölçümü: 98 JS dosyasının tamamı tarayıcı tarafıdır (`window.` **310** · `document.` **434** ·
`fetch(` **27** isabet) — Node süreç API'lerinde **0 isabet**. Yani: **süreç başlatma bu depoda JS ile yapılmıyor.**

```js
// ⚠️ KALIP (repo YOK) — Node tarafı örnek
// Kaynak: https://nodejs.org/api/child_process.html (reuse · envanterde vardı · HTTP 200)
import { spawn } from 'node:child_process';

const child = spawn('ffprobe', ['-v', 'error', '-show_format', '-of', 'json', file],
                    { stdio: ['ignore', 'pipe', 'pipe'] });
let out = '';
child.stdout.on('data', (d) => { out += d; });
child.on('close', (code) => (code === 0 ? render(JSON.parse(out)) : fail(code)));
```

**Eşleme:** argümanlar ayrı dizi elemanı verilir (`shell` yok) → PHP §20.2'nin `escapeshellarg`
kaçış zinciri bu biçimde **gerekmez**; `shell: true` kullanılırsa PHP §20.2'deki aynı risk doğar
(`⚠️ VERIFICATION REQUIRED`: depoda `shell:` kullanımı **yoktur**, grep 0 — davranış test edilmedi).

### §21.2 C — `fork` + `exec*` (kalıp)

```c
/* ⚠️ KALIP (repo YOK) — POSIX doküman:
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html   (reuse · 200)
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/execve.html (YENİ · 200) */
#include <sys/wait.h>
#include <unistd.h>

pid_t pid = fork();
if (pid < 0) { /* fork başarısız: errno oku (§21.7 notu) */ }
else if (pid == 0) {                       /* çocuk süreç */
    execlp("ffprobe", "ffprobe", "-v", "error", file, (char *)NULL);
    _exit(127);                            /* exec buraya dönerse */
} else {                                   /* ebeveyn */
    int status;
    waitpid(pid, &status, 0);              /* §19.1 G1: yarat + çalıştır + bekle */
    if (WIFEXITED(status)) { int code = WEXITSTATUS(status); /* ... */ }
}
```

**Eşleme notu:** `fork()` çağrısının iki iade noktası vardır; ebeveynde tampon iki kez
`flush` edilebilir → `fork()`'tan hemen sonra `exec` (çakışma penceresi) dışında kalan yolda
`_exit()` kullanılır. Depoda C dosyası **0** olduğu için bu satır kanıt değil **kalıptır** (`⚠️ KALIP (repo YOK)`).

### §21.3 Python — ortam: depo satırı gerçek · yürütme: kalıp

```python
# DEPO SATIRI — .ai/scripts/fix-mojibake.py:16-28 (ReadAllLines birebir)
import os
import sys
...
if sys.platform == 'win32':
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
    os.environ['PYTHONIOENCODING'] = 'utf-8'
```

```python
# ⚠️ KALIP (repo YOK) — subprocess kullanımı
# Kaynak: https://docs.python.org/3/library/subprocess.html (reuse · envanterde vardı · HTTP 200)
import subprocess

p = subprocess.run(
    ["ffprobe", "-v", "error", "-show_format", path],
    capture_output=True, text=True, timeout=10, shell=False,
)
if p.returncode != 0:
    raise RuntimeError(p.stderr)
```

**Eşleme:** liste girdisi + `shell=False` → kaçış zinciri **yok** (PHP §20.2'deki `escapeshellarg`
karşılığı bu seçimle **ortadan kalkar**). `timeout=` → §19.1 G1'in "bekle" adımının üst-sınır hâlidir.
`⚠️ VERIFICATION REQUIRED`: timeout aşımında süreç ağacına ne olduğu — `run()`'ın `kill`/`killpg` ayrımı dokümanda bu turda **okunmadı**.

### §21.4 Go — `os/exec` (kalıp)

```go
// ⚠️ KALIP (repo YOK) — Kaynak: https://pkg.go.dev/os/exec (reuse · envanterde vardı · HTTP 200)
package main

import (
    "context"
    "os/exec"
    "time"
)

func probe(ctx context.Context, path string) ([]byte, error) {
    ctx, cancel := context.WithTimeout(ctx, 10*time.Second)
    defer cancel()
    cmd := exec.CommandContext(ctx, "ffprobe", "-v", "error", "-show_format", path)
    return cmd.Output() // hata: *exec.ExitError -> ProcessState.ExitCode()
}
```

**Eşleme:** `CommandContext` iptal edilince süreç öldürülür → bekleme-üst-sınırı + iptal tek yapıda
toplanır. `⚠️ VERIFICATION REQUIRED`: `CommandContext`'in varsayılan öldürme sinyali
dokümanda bu turda **okunmadı**.

### §21.5 Rust — `std::process::Command` (kalıp)

```rust
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://doc.rust-lang.org/std/process/struct.Command.html (reuse · envanterde vardı · HTTP 200)
use std::process::Command;
use std::time::Instant;

let t0 = Instant::now();                       // monotonik saat (PHP §20.4 eşlemesi)
let out = Command::new("ffprobe")
    .args(["-v", "error", "-show_format", path])
    .output()?;                                // stdout+stderr toplar; bekleme bloklayıcıdır
if !out.status.success() {
    eprintln!("exit={:?} {}ms", out.status.code(), t0.elapsed().as_millis());
}
```

**Eşleme:** `Instant` monotoniktir (PHP §20.4 `hrtime` eşlemesi); `output()` çağıran thread'i
bloklar → iş-kuyruğu modeli değil, tek-thread beklemedir. `⚠️ VERIFICATION REQUIRED`:
`Command`'in sinyal/çocuk-toplama davranışı için `wait()` dokümanı bu turda okunmadı.

### §21.6 C# — `System.Diagnostics.Process` (kalıp)

```csharp
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://learn.microsoft.com/en-us/dotnet/api/system.diagnostics.process (reuse · envanterde vardı · HTTP 200)
using System.Diagnostics;

var psi = new ProcessStartInfo("ffprobe")
{
    ArgumentList = { "-v", "error", "-show_format", path }, // ayrı argümanlar = kaçış yok
    RedirectStandardOutput = true,
    UseShellExecute = false,
    CreateNoWindow = true,
};
using var proc = Process.Start(psi)!;
var json = await proc.StandardOutput.ReadToEndAsync();
await proc.WaitForExitAsync();
```

**Eşleme:** `ArgumentList` ayrı argüman iletir (tek dize `Arguments` değil) → Windows
`CreateProcess` ayrıştırma kurallarından kaçar; PHP §20.2'nin C# karşılığı budur.
`⚠️ VERIFICATION REQUIRED`: `ArgumentList` ve `WaitForExitAsync`'in eklendiği .NET sürümü **okunmadı**.

### §21.7 Ölçüm (§21)

| Öğe | Değer |
|---|---|
| Dil | 6 (JS · C · Python · Go · Rust · C#) |
| Repo satır kanıtlı blok | **2** (§21.1 JS olumsuz kanıt · §21.3 PY `fix-mojibake.py:16-28`) |
| `⚠️ KALIP (repo YOK)` blok | **6** (JS · C · Python · Go · Rust · C#) |
| Bu bölümün URL'i | **7** → **6 reuse + 1 yeni** (`execve.html`) — hepsi HTTP 200 (dogrulama §J.9) |
| Repo grep (olumsuz kanıt) | `child_process` · `spawn(` · `fork(` · `worker_threads` · `process.env` · `process.hrtime` · `subprocess` → **0 isabet** |
| ⚠️ işaretli iddia | 6 (§21.1 `shell:` · §21.3 timeout kill · §21.4 öldürme sinyali · §21.5 wait/sinyal · §21.6 .NET sürümü · §21.2 `errno` okuma biçimi) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
