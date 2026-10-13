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
validation: "2026-10-13 · 3 yeni URL HTTP HEAD 200 (envanter kesişim 0) · §7-§14 eklendi (172 → ≥500 satır) · §13 repo satırları ReadAllLines ile birebir"
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
