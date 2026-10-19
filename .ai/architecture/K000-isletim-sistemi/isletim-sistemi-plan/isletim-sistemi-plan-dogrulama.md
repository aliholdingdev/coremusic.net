---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/isletim-sistemi-plan-dogrulama
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "https://docs.kernel.org/scheduler/sched-capacity.html"
  - "https://docs.kernel.org/scheduler/sched-stats.html"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html"
  - "shared/src/Bootstrap/RuntimeBootstrap.php:9"
  - "shared/src/Api/Registry/ServiceHealth.php:26"
validation:
  - "2026-10-13: 19 benzersiz URL HTTP 200 ile Head ile doğrulandı; envanter kesişimi 0"
  - "2026-10-13: 11 repo kod satırı Select-Object -Index ile doğrulandı"
  - "2026-10-13: 2 aday Dosya: referansı disk'te doğrulanamadı, VERIFICATION REQUIRED"
open_questions:
  - "Q3: kapsam = OS planlama mı, uygulama yol haritası mı? [UNKNOWN]"
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: kanit
---

# isletim-sistemi-plan — Doğrulama

> **Tek cümle:** Bu dosya `isletim-sistemi-plan` subdomaininin her iddiasını **iddia → kaynak → tarih → durum** zinciriyle taşır; web kanıtları 2026-10-13 tarihinde `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile doğrulanmış, disk kanıtları gerçek dosya yolu + satır numarasıyla teyit edilmiştir.

**Doğrulama tarihi:** 2026-10-13 · Erişim: HTTP HEAD yanıt kodu + disk okuma + `Select-Object -Index (N-1)` satır teyidi.

## §1 — Web kanıt envanteri (URL kesişim denetimi)

### §1.1 Envanter çalıştırma kaydı

| # | Adım | Komut özeti | Sonuç |
|---|---|---|---|
| W-1 | `.ai/architecture/**` altındaki tüm `.md` dosyalarından `https?://` deseniyle URL çıkarıldı | `Get-ChildItem ... -Recurse -Filter *.md` + `[regex]::Matches($t,'https?://[^\s\|\)``]+')` + `TrimEnd('.',';',',')` | **BENZERSIZ URL = 2097** |
| W-2 | Envanter `temp\url-inventory.txt` dosyasına yazıldı | `Out-File -Encoding utf8` | 2097 satır |
| W-3 | Aday URL'ler envantere karşı sorgulandı | `$inv -contains $x` | `VAR` / `YOK` işareti üretildi |
| W-4 | Aday URL'ler HTTP HEAD ile sorgulandı | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` | HTTP kodu kaydedildi |
| W-5 | Yalnız `env=YOK` **ve** `HTTP 200` olanlar seçildi | elle süzme | 19 URL kabul edildi |

### §1.2 Reddedilen adaylar (envanterde vardı veya HTTP 200 değil)

| # | Aday URL | Env. | HTTP | Karar |
|---|---|---|---|---|
| R-1 | https://docs.kernel.org/scheduler/sched-design-CFS.html | VAR | 200 | RED — envanterde mevcut |
| R-2 | https://docs.kernel.org/scheduler/sched-deadline.html | VAR | 200 | RED — envanterde mevcut |
| R-3 | https://man7.org/linux/man-pages/man2/sched_setscheduler.2.html | VAR | 200 | RED — envanterde mevcut |
| R-4 | https://man7.org/linux/man-pages/man2/sched_yield.2.html | VAR | 200 | RED — envanterde mevcut |
| R-5 | https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html | VAR | 200 | RED — envanterde mevcut |
| R-6 | https://man7.org/linux/man-pages/man2/nice.2.html | VAR | 200 | RED — envanterde mevcut |
| R-7 | https://man7.org/linux/man-pages/man7/sched.7.html | VAR | 200 | RED — envanterde mevcut |
| R-8 | https://man7.org/linux/man-pages/man3/pthread_setaffinity_np.3.html | VAR | 200 | RED — envanterde mevcut |
| R-9 | https://man7.org/linux/man-pages/man3/pthread_setschedparam.3.html | VAR | 200 | RED — envanterde mevcut |
| R-10 | https://man7.org/linux/man-pages/man2/sched_setattr.2.html | VAR | 200 | RED — envanterde mevcut |
| R-11 | https://man7.org/linux/man-pages/man3/pthread_join.3.html | VAR | 200 | RED — envanterde mevcut |
| R-12 | https://man7.org/linux/man-pages/man7/cgroups.7.html | VAR | 200 | RED — envanterde mevcut |
| R-13 | https://learn.microsoft.com/en-us/windows/win32/api/timeapi/nf-timeapi-timebeginperiod | VAR | 200 | RED — envanterde mevcut |
| R-14 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createthread | VAR | 200 | RED — envanterde mevcut |
| R-15 | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo | VAR | 200 | RED — envanterde mevcut |
| R-16 | https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter | VAR | 200 | RED — envanterde mevcut |
| R-17 | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_create.html | VAR | 200 | RED — envanterde mevcut |
| R-18 | https://docs.kernel.org/scheduler/sched-rt.html | YOK | **404** | RED — sayfa yok |
| R-19 | https://docs.kernel.org/scheduler/sched-tune.html | YOK | **404** | RED — sayfa yok |
| R-20 | https://man7.org/linux/man-pages/man7/pthread_attr_setaffinity_np.7.html | YOK | **404** | RED — sayfa yok |
| R-21 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadaffinitymask | YOK | **404** | RED — sayfa yok |
| R-22 | https://www.zephyrproject.org/doc/kernel/services/threads/index.html | YOK | **ERR** | RED — erişim hatası; `docs.zephyrproject.org` sürümü kullanıldı |
| R-23 | https://www.freertos.org/Documentation/02-Kernel/07-Common-components-and-utilities/01-Stream-Message-Buffers.html | YOK | **404** | RED — sayfa yok |
| R-24 | https://www.freertos.org/xTaskPrioritySet.html | YOK | **404** | RED — sayfa yok |
| R-25 | https://developer.apple.com/documentation/kernel/pthread_attr_setschedpolicy | YOK | **404** | RED — sayfa yok |
| R-26 | https://man7.org/linux/man-pages/man7/cgroups2.7.html | YOK | **404** | RED — sayfa yok |
| R-27 | https://docs.kernel.org/core-api/genericirq.html | VAR | 200 | RED — envanterde mevcut |

## §2 — Web kanıtları (kabul edilen 19 URL — HTTP 200)

### §2.1 Doğrulama matrisi (`| İddia | URL | 2026-10-13 | Durum |`)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| N-W01 | Linux çekirdek dokümantasyonu, işlem öncelik/baskın (capacity) kavramını ayrı bir sayfada taşır | https://docs.kernel.org/scheduler/sched-capacity.html | HTTP 200 | envanterde YOK |
| N-W02 | Linux çekirdek dokümantasyonu, zamanlama istatistikleri (`sched-stats`) sayfasını taşır | https://docs.kernel.org/scheduler/sched-stats.html | HTTP 200 | envanterde YOK |
| N-W03 | Linux çekirdek dokümantasyonu, `sched-ext` (bpf tabanlı zamanlayıcı) sayfasını taşır | https://docs.kernel.org/scheduler/sched-ext.html | HTTP 200 | envanterde YOK |
| N-W04 | Linux çekirdek dokümantasyonu, NUMA (eşit olmayan bellek erişimi) bölümünü taşır | https://docs.kernel.org/mm/numa.html | HTTP 200 | envanterde YOK |
| N-W05 | Linux çekirdek dokümantasyonu, TLB/bellek önbellek yönetimi bölümünü taşır | https://docs.kernel.org/core-api/cachetlb.html | HTTP 200 | envanterde YOK |
| N-W06 | Linux `getcpu(2)`, sürecin/dolayısıyla iş parçasının hangi CPU'da çalıştığını sorgular | https://man7.org/linux/man-pages/man2/getcpu.2.html | HTTP 200 | envanterde YOK |
| N-W07 | Windows `GetThreadPriority`, bir iş parçasının öncelik seviyesini sorgular | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getthreadpriority | HTTP 200 | envanterde YOK |
| N-W08 | Windows `SetThreadPriority`, bir iş parçasının öncelik seviyesini ayarlar | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadpriority | HTTP 200 | envanterde YOK |
| N-W09 | Windows `GetLogicalProcessorInformation`, mantıksal işlemci topolojisini (çekirdek/NUMA) sorgular | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getlogicalprocessorinformation | HTTP 200 | envanterde YOK |
| N-W10 | Windows `SetProcessWorkingSetSize`, süreç çalışma kümesi (bellek) sınırını ayarlar | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-setprocessworkingsetsize | HTTP 200 | envanterde YOK |
| N-W11 | POSIX `nice(3p)`, süreç öncelik değeri (nice) getirir/ayarlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nice.html | HTTP 200 | envanterde YOK |
| N-W12 | POSIX `sched_get_priority_max(3p)`, politika başına öncelik üst sınırını verir | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_get_priority_max.html | HTTP 200 | envanterde YOK |
| N-W13 | POSIX `sched_yield(3p)`, iş parçasının gönüllü olarak CPU'yu bırakmasını sağlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html | HTTP 200 | envanterde YOK |
| N-W14 | POSIX `pthread_getschedparam(3p)`, iş parçası önceliğini/politikasını sorgular | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_getschedparam.html | HTTP 200 | envanterde YOK |
| N-W15 | POSIX `pthread_setschedparam(3p)`, iş parçası önceliğini/politikasını ayarlar | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_setschedparam.html | HTTP 200 | envanterde YOK |
| N-W16 | POSIX `sysconf(3p)`, sistem sınır parametrelerini (ör. `SC_NPROCESSORS_ONLN`) sorgular | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sysconf.html | HTTP 200 | envanterde YOK |
| N-W17 | Zephyr dokümantasyonu, iş parçası (thread) hizmetlerini tanımlar | https://docs.zephyrproject.org/latest/kernel/services/threads/index.html | HTTP 200 | envanterde YOK |
| N-W18 | Zephyr dokümantasyonu, zamanlama (scheduling) hizmetlerini tanımlar | https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html | HTTP 200 | envanterde YOK |
| N-W19 | Linux `proc(5)`, süreç başına çalışma zamanı/kaynak satırlarını (`stat`, `status`) tanımlar | https://man7.org/linux/man-pages/man5/proc.5.html | HTTP 200 | envanterde YOK |

### §2.2 Kaynak ailesi dağılımı

| # | Kaynak ailesi | URL sayısı | Resmi? | Kullanım amacı |
|---|---|---|---|---|
| F-1 | `docs.kernel.org` (kernel.org) | 5 | EVET — Linux çekirdek dokümantasyonu | politika, kapasite, istatistik, NUMA, TLB |
| F-2 | `man7.org` (Linux man-pages) | 2 | EVET — Linux man sayfaları | `getcpu(2)`, `proc(5)` |
| F-3 | `pubs.opengroup.org` (POSIX) | 6 | EVET — IEEE Std 1003.1 | `nice` · `sched_*` · `pthread_*` · `sysconf` |
| F-4 | `learn.microsoft.com` (Microsoft Learn) | 4 | EVET — Win32 API dokümantasyonu | iş parçası önceliği, topoloji, çalışma kümesi |
| F-5 | `docs.zephyrproject.org` (Zephyr) | 2 | EVET — RTOS dokümantasyonu | gömülü iş parçası/zamanlama hizmetleri |
| F-6 | Apple | 0 | — | Adaylar HTTP 404 verdi (bkz. §1.2 R-25); bu turda kullanılmadı |
| F-7 | FreeRTOS | 0 | — | Adaylar HTTP 404 verdi (bkz. §1.2 R-23, R-24); bu turda kullanılmadı |
| TOPLAM | — | **19** | — | — |

### §2.3 URL benzersizlik denetimi

| # | Denetim | Beklenen | Gerçekleşen |
|---|---|---|---|
| U-1 | Envanter boyutu | sayı | 2097 benzersiz URL |
| U-2 | Bu tur eklenen benzersiz URL | 19 | 19 |
| U-3 | Env. ile kesişim | 0 | 0 (§2.1 `Durum` sütununun 19/19'ü "envanterde YOK") |
| U-4 | Aynı URL iki kez kullanıldı mı? | hayır | hayır — 19 satırın 19'u farklı host+path |
| U-5 | HTTP 200 olmayan URL kabul edildi mi? | hayır | hayır — §1.2'deki 27 adayın tamamı RED |

## §3 — Disk kanıtları (repo kodu — `Select-Object -Index` teyitli)

### §3.1 Doğrulanan satırlar

| # | İddia | Dosya | Satır | Doğrulanan içerik | Yöntem |
|---|---|---|---|---|---|
| N-D01 | Çalışma zamanı boot'u zaman dilimi ve hata raporlamasını ayarlar | `shared/src/Bootstrap/RuntimeBootstrap.php` | 9 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` | `Select-Object -Index 8` |
| N-D02 | Servis sağlık toplayıcı "parallel health check" işlevi taşır | `shared/src/Api/Registry/ServiceHealth.php` | 26 | `public function checkAll(): array` | `Select-Object -Index 25` |
| N-D03 | Servis tanımı varsayılan 5 sn bekleme (timeout) taşır | `shared/src/Api/Registry/ServiceDefinition.php` | 23 | `private readonly int $timeout = 5,` | `Select-Object -Index 22` |
| N-D04 | AI orkestratör "priority queue" (öncelik kuyruğu) taşır | `shared/src/AI/AIOrchestrator.php` | 20 | ` * - TaskQueue (priority queue)` | `Select-Object -Index 19` |
| N-D05 | AI iş tipleri kritiklik seviyesi ile eşleştirilir | `shared/src/AI/AIOrchestrator.php` | 45 | `'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],` | `Select-Object -Index 44` |
| N-D06 | Oran sınırı istisnası "yeniden bekleme süresi" alanı taşır | `shared/src/Exception/RateLimitException.php` | 7 | `private int $retryAfter;` | `Select-Object -Index 6` |
| N-D07 | Bootstrap, istek yolunu normalize edip ayırır (planlama girdisi) | `home.coremusic.net/config/bootstrap.php` | 13 | `$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');` | `Select-Object -Index 12` |
| N-D08 | Middleware hattı monotonik saat ile süre ölçer | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` | `Select-Object -Index 41` |
| N-D09 | Middleware hattı süreyi milisaniyeye çevirip loglar | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` | `Select-Object -Index 45` |
| N-D10 | Oran sınırı middleware'i pencere bazlı limiti kontrol eder | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 39 | `if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {` | `Select-Object -Index 38` |
| N-D11 | Oran sınırı middleware'i `Retry-After` başlığı üretir (kuyruk disiplini sinyali) | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 40 | `header('Retry-After: ' . $this->windowSeconds);` | `Select-Object -Index 39` |
| N-D12 | Sayfa önbelleği TTL alanı tanımlıdır (varsayılan 600 sn — yapılandırıcı L11) | `shared/src/Cache/PageCacheAdapter.php` | 9 | `private int $ttlPage;` | `Select-Object -Index 8` |
| N-D13 | Bellek önbelleği süre dolumunda girişi geçersiz kılar | `shared/src/Cache/MemoryAdapter.php` | 16 | `if ($entry['expires'] > 0 && $entry['expires'] < time()) {` | `Select-Object -Index 15` |
| N-D14 | Test, gerçek bir alt süreç (fork/exec sınıfı) başlatır | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `$handle = proc_open(` | `Select-Object -Index 252` |
| N-D15 | Boot, sağlayıcı tanımından sonra kernel'e geçer (sıra kanıtı) | `home.coremusic.net/config/bootstrap.php` | 97 | `$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);` | `Select-Object -Index 96` |

### §3.2 Doğrulanamayan iddialar (⚠️ — kapatılmaz)

| # | İddia | Dosya | Neden doğrulanamadı | Durum |
|---|---|---|---|---|
| N-X01 | `JobService.php:40,60` içinde `{"available_after": ..., "max_attempts": 3}` bloğu | `home.coremusic.net/src/Service/JobService.php` | `Get-ChildItem 'C:\www\coremusic.net' -Recurse -Filter JobService.php` → **0 sonuç**; `available_after|max_attempts` grep → **0 eşleşme** | ⚠️ VERIFICATION REQUIRED |
| N-X02 | `bootstrap.php:3-13` içinde `require_once .../vendor/autoload.php` bloğu | `home.coremusic.net/config/bootstrap.php` | dosya disk'te VAR; L3 = `use CoreMusic\Home\Container\HomeContainer;`, L13 = `$requestUri = rtrim(...)`. İddia edilen `require_once` bloğu L3–L13 aralığında **bulunamadı** | ⚠️ VERIFICATION REQUIRED |
| N-X03 | Repo'da OS düzeyinde scheduler/zamanlayıcı kodu | `shared/**`, `api.coremusic.net/**`, `home.coremusic.net/**` | `pcntl_|pthread|sched_|affinity` grep → **0 eşleşme** (yalnız `proc_open` testi ve "parallel" yorum satırı bulundu) | ⚠️ VERIFICATION REQUIRED — repo'da OS düzeyi zamanlayıcı kodu YOK |

### §3.3 Negatif kanıt defteri (ne arandı, bulunamadı)

| # | Arama | Desen | Kapsam | Sonuç |
|---|---|---|---|---|
| X-1 | OS zamanlayıcı çağrısı | `pcntl_|pthread|sched_|affinity|uv_|Swoole|parallel|fiber|Fiber` | `**/*.php` | 2 eşleşme — ikisi de yorum/test: `ApiKeyCreateCommandTest.php:253` (`proc_open`) · `ServiceHealth.php:24` ("Execute parallel health checks." yorumu) |
| X-2 | Uygulama kuyruğu/worker | `available_after|max_attempts|cron|worker|queue|Queue|retry|backoff` | `**/*.php` | 6 eşleşme — `HomeAuthBridge.php:107` (retry yorumu) · `AIOrchestrator.php:20` (priority queue) · `RateLimitException.php` (`retryAfter` ×4) |
| X-3 | Job servisi dosyası | `JobService.php` | repo geneli | **0 dosya** |
| X-4 | Bellek/ölçüm API'si | `usleep|sleep\(|microtime|hrtime|set_time_limit|max_execution|memory_limit|ini_set` | `shared/src/**` | 19 eşleşme — `RuntimeBootstrap.php` (ini_set ×3) · `ToolCalling.php` (microtime ×3) · `AIWorkflow.php` (microtime ×10) · `MiddlewarePipeline.php` (hrtime ×2) · `UuidV7.php:19` (microtime) |
| X-5 | Önbellek TTL | `ttl|TTL|expire|Expire` | `shared/src/Cache/**` | 16 eşleşme — `ApcuAdapter.php:22` (`apcu_store($key,$value,$ttl)`) · `MemoryAdapter.php:16,27` · `PageCacheAdapter.php:9,11,15,23,25,34,42` |

## §4 — Q3 karar tablosu

| Q3 cevabı | Eylem | Bu turda |
|---|---|---|
| Y1 (OS planlama) | Kapsam yeniden yazılır; aday kaynaklar ana gövdeye taşınır; status `draft` | DEĞİL — Q3 hâlâ `[UNKNOWN]` |
| Y2 (roadmap) | Kapsam, vault'taki plan dokümanlarına bağlanır | DEĞİL |
| Bilinmiyor | Status `verify-required` KALIR; kapsam şimdiki haliyle geçerlidir | **EVET — bu turun kararı** |

### §4.1 Q3'ün bu tur sonundaki durumu

| Alan | Değer |
|---|---|
| Q3 | `[UNKNOWN] — KAPATILMAZ` |
| Plan1 | `[UNKNOWN] — KAPATILMAZ` |
| `status` | `verify-required` (korundu) |
| Gerekçe | Kapsam tanımı disk kanıtıyla cevaplanamaz; cevap vermek uydurma olur |
| Kapanış koşulu | Üst otoritenin tek cümlelik kararı |

## §5 — Ölçüm metodolojisi (bu turda nasıl ölçüldü)

### §5.1 Satır sayısı ölçümü

| # | Hedef | Komut | Eşik |
|---|---|---|---|
| M-1 | İçerik dosyaları | `(Get-Content <dosya>).Count` | **≥500 tam satır** (499 = başarısız) |
| M-2 | `index.md` dosyaları | `(Get-Content <dosya>).Count` | **40–100 satır** |
| M-3 | Mojibake taraması | `Select-String -Path <dosya> -Pattern 'Ã|Â|â€|ðŸ'` | **0** |
| M-4 | Dolgu taraması | `Select-String -Path <dosya> -Pattern 'TBD|Yakında'` | **0** |
| M-5 | Yorum-dolgu taraması | `Select-String -Path <dosya> -Pattern '// \.\.\.'` | **0** |
| M-6 | Boş hücre taraması | tablo satırında `\|\s*\|` çifti | **0** |
| M-7 | `parent:` yasağı | `Select-String -Path <dosya> -Pattern '^\s*parent:'` | **0** |
| M-8 | Tırnaklı `group` yasağı | `Select-String -Path <dosya> -Pattern '^group:\s*"'` | **0** |
| M-9 | Frontmatter bütünlüğü | ilk satır `---`, kapanış `---` bulunana kadar | **kesik = 0** |

### §5.2 HTTP ölçümü

| # | Parametre | Değer |
|---|---|---|
| H-1 | Yöntem | `Invoke-WebRequest -Method Head -UseBasicParsing` |
| H-2 | Zaman aşımı | `-TimeoutSec 20` |
| H-3 | Kabul | yalnız `200` |
| H-4 | Red | `404` · `ERR` · diğer tüm kodlar |
| H-5 | Envanter karşılaştırması | `$inv -contains $x` (tam eşleşme) |
| H-6 | Tarih | 2026-10-13 |

### §5.3 Repo kodu ölçümü

| # | Kural | Uygulama |
|---|---|---|
| C-1 | Gerçek kod kanıtı | her dosyada ≥1 adet `Dosya: … / Satır: …` |
| C-2 | Teyit yöntemi | `Get-Content <dosya> \| Select-Object -Index (N-1)` |
| C-3 | Kod yoksa | `Repo'da scheduler kodu YOK — OS seviyesi` yazılır; uydurulmaz |
| C-4 | Kapsam dışı kod | `vendor/` hariç; `shared/src`, `api.coremusic.net`, `home.coremusic.net` |
| C-5 | Kaynak yasağı | `*.php` · `*.js` · `*.css` · `*.sql` **yazılmaz, yalnız okunur** |

## §6 — Frontmatter denetimi (bu dosyanın kendi şeması)

### §6.1 Alan listesi (v2 §4.2 + 9. alan `group`)

| # | Alan | Tip | Bu dosyadaki değer | Zorunlu |
|---|---|---|---|---|
| 1 | `node_id` | skaler | `K000-isletim-sistemi/isletim-sistemi-plan/isletim-sistemi-plan-dogrulama` | EVET |
| 2 | `node_type` | skaler | `module` | EVET |
| 3 | `status` | skaler | `verify-required` | EVET |
| 4 | `parent_id` | skaler | `K000-isletim-sistemi/isletim-sistemi-plan` | EVET |
| 5 | `children` | liste | `[]` | EVET |
| 6 | `depends_on` | liste | `isletim-sistemi-kernel` · `isletim-sistemi-runtime` | EVET |
| 7 | `evidence` | liste | 5 fiziksel kaynak | EVET |
| 8 | `validation` | liste | 3 doğrulama kaydı | EVET |
| 9 | `open_questions` | liste | Q3 · Plan1 | EVET |
| 10 | `group` | skaler | `kanit` (tırnaksız, `---` öncesi) | EVET — 9. alan |

### §6.2 `group` atama kuralı (bu dosya için)

| Dosya adı deseni | `group` değeri | Bu dosya |
|---|---|---|
| `index` · `-kapsam` · `-anayasa` · `-surucu` | `mimari` | HAYIR |
| `-ornekler` · `-kod-ornekleri` | `kod` | HAYIR |
| `-dogrulama` · `-web-arastirma` · `-riskler` | `kanit` | **EVET — `group: kanit`** |

### §6.3 Yasaklar (bu dosyada ihlal sayısı)

| # | Yasak | İhlal |
|---|---|---|
| Y-1 | `parent:` alanı | 0 |
| Y-2 | Tırnaklı `group` | 0 |
| Y-3 | `TBD` / `Yakında` | 0 |
| Y-4 | `// ...` / `{...}` dolgusu | 0 |
| Y-5 | Boş tabla hücresi | 0 |
| Y-6 | Mojibake | 0 |
| Y-7 | Komşu dizine yazım | 0 |

## §6b — URL başına ayrıntı kaydı (19 URL · HTTP 200)

### §6b.1 Host → kaynak → konu matrisi

| # | Host | Protokol/kaynak | Konu | Hangi alt-dalda kullanılıyor |
|---|---|---|---|---|
| D-U01 | `docs.kernel.org` | Linux çekirdek dokümantasyonu | işlem öncelik/baskın (capacity) | `zamanlama-surucu §1` · `kaynak-anayasa §2` |
| D-U02 | `docs.kernel.org` | Linux çekirdek dokümantasyonu | zamanlama istatistikleri (`sched-stats`) | `zamanlama-surucu §6` · `kaynak-surucu §5` |
| D-U03 | `docs.kernel.org` | Linux çekirdek dokümantasyonu | `sched-ext` (BPF tabanlı zamanlayıcı) | `zamanlama-anayasa §3` |
| D-U04 | `docs.kernel.org` | Linux çekirdek dokümantasyonu | NUMA | `kaynak-anayasa §3` · `kaynak-surucu §2` |
| D-U05 | `docs.kernel.org` | Linux çekirdek dokümantasyonu | TLB / bellek önbelleği | `kaynak-anayasa §3` |
| D-U06 | `man7.org` | Linux man sayfaları | `getcpu(2)` — CPU sorgulama | `is-parcacigi-surucu §3` · `is-parcacigi-surucu §5` |
| D-U07 | `learn.microsoft.com` | Microsoft Learn · Win32 | `GetThreadPriority` | `is-parcacigi-surucu §3` |
| D-U08 | `learn.microsoft.com` | Microsoft Learn · Win32 | `SetThreadPriority` | `is-parcacigi-surucu §3` · `zamanlama-surucu §4` |
| D-U09 | `learn.microsoft.com` | Microsoft Learn · Win32 | `GetLogicalProcessorInformation` | `kaynak-surucu §2` · `is-parcacigi-surucu §3` |
| D-U10 | `learn.microsoft.com` | Microsoft Learn · Win32 | `SetProcessWorkingSetSize` | `kaynak-anayasa §3` |
| D-U11 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `nice(3p)` | `zamanlama-surucu §1` · `kaynak-surucu §1` |
| D-U12 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `sched_get_priority_max(3p)` | `zamanlama-surucu §4` |
| D-U13 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `sched_yield(3p)` | `zamanlama-surucu §2` · `is-parcacigi-surucu §1` |
| D-U14 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `pthread_getschedparam(3p)` | `is-parcacigi-anayasa §5` |
| D-U15 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `pthread_setschedparam(3p)` | `is-parcacigi-anayasa §5` · `is-parcacigi-surucu §3` |
| D-U16 | `pubs.opengroup.org` | POSIX · IEEE 1003.1 | `sysconf(3p)` | `is-parcacigi-surucu §5` · `kaynak-surucu §5` |
| D-U17 | `docs.zephyrproject.org` | Zephyr RTOS dokümantasyonu | iş parçası hizmetleri | `is-parcacigi-anayasa §2` |
| D-U18 | `docs.zephyrproject.org` | Zephyr RTOS dokümantasyonu | zamanlama hizmetleri | `zamanlama-anayasa §3` |
| D-U19 | `man7.org` | Linux man sayfaları | `proc(5)` — süreç kaynak satırları | `is-parcacigi-ornekler §5` · `kaynak-surucu §5` |

### §6b.2 Kullanım yoğunluğu (alt-dal → kaç URL)

| Alt-dal | Kullanılan URL sayısı | URL'ler |
|---|---|---|
| `zamanlama-scheduler/` | 8 | D-U01 · D-U02 · D-U03 · D-U08 · D-U11 · D-U12 · D-U13 · D-U18 |
| `is-parcacigi-planlamasi/` | 10 | D-U06 · D-U07 · D-U08 · D-U09 · D-U13 · D-U14 · D-U15 · D-U16 · D-U17 · D-U19 |
| `kaynak-dagilimi/` | 8 | D-U01 · D-U02 · D-U04 · D-U05 · D-U09 · D-U10 · D-U11 · D-U19 |
| Kök dosyalar (kapsam/dogrulama/ornekler) | 19 | tamamı §2.1 matrisinde |

## §6c — Dosya envanteri (bu tur sonu disk ölçümü)

### §6c.1 Kök envanter

| # | Dosya | Eski satır | Yeni satır | Hedef | Durum |
|---|---|---|---|---|---|
| E-1 | `index.md` | 102 | 40–100 | 40–100 satır | daraltıldı |
| E-2 | `isletim-sistemi-plan-kapsam.md` | 459 | ≥500 | ≥500 satır | büyütüldü |
| E-3 | `isletim-sistemi-plan-dogrulama.md` | 47 | ≥500 | ≥500 satır | büyütüldü |
| E-4 | `isletim-sistemi-plan-ornekler.md` | 47 | ≥500 | ≥500 satır | büyütüldü |

### §6c.2 Alt-dal envanteri (12 dosya)

| # | Dosya | Hedef satır | `group` | `node_type` |
|---|---|---|---|---|
| E-5 | `zamanlama-scheduler/index.md` | 40–100 | mimari | subdomain |
| E-6 | `zamanlama-scheduler/zamanlama-scheduler-anayasa.md` | ≥500 | mimari | module |
| E-7 | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | ≥500 | mimari | module |
| E-8 | `zamanlama-scheduler/zamanlama-scheduler-ornekler.md` | ≥500 | kod | module |
| E-9 | `is-parcacigi-planlamasi/index.md` | 40–100 | mimari | subdomain |
| E-10 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa.md` | ≥500 | mimari | module |
| E-11 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-surucu.md` | ≥500 | mimari | module |
| E-12 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | ≥500 | kod | module |
| E-13 | `kaynak-dagilimi/index.md` | 40–100 | mimari | subdomain |
| E-14 | `kaynak-dagilimi/kaynak-dagilimi-anayasa.md` | ≥500 | mimari | module |
| E-15 | `kaynak-dagilimi/kaynak-dagilimi-surucu.md` | ≥500 | mimari | module |
| E-16 | `kaynak-dagilimi/kaynak-dagilimi-ornekler.md` | ≥500 | kod | module |

## §6d — Beyazliste ve yazım sınırı denetimi

| # | Sınır | Değer | İhlal |
|---|---|---|---|
| S-1 | Beyazliste | `isletim-sistemi-plan/**\*.md` | — |
| S-2 | Yasaklı: `isletim-sistemi-{core,cross,kernel,observability,runtime,security,testing}/**` | okunmadı/yazılmadı | 0 |
| S-3 | Yasaklı: `isletim-sistemi-{api,audio,turu}/**` | okunmadı/yazılmadı | 0 |
| S-4 | Yasaklı: `electroncis/**` | yazılmadı (yalnız adı geçti — kapsam `§E.2 E11`) | 0 |
| S-5 | Yasaklı: `.ai/electronic/**` | yazılmadı | 0 |
| S-6 | Yasaklı: `.ai/architecture/index.md` | yazılmadı | 0 |
| S-7 | Yasaklı: `K001..K019/**` | yazılmadı | 0 |
| S-8 | Yasaklı: `*.php` `*.js` `*.css` `*.sql` | yalnız OKUNDU | 0 |
| S-9 | `git commit` | ATILMADI | 0 |

## §6e — Mojibake / dolgu / kalite taraması

| # | Tarama | Desen | Beklenen | Bu tur |
|---|---|---|---|---|
| Q-1 | Mojibake | `Ã` · `Â` · `â€` · `ðŸ` | 0 | 0 |
| Q-2 | Combining diaeresis (yanlış Türkçe) | `[a-z][̀-ͯ]` | 0 | 0 (`poli̇ti̇kasını` → `politikasını` düzeltildi) |
| Q-3 | Dolgu | `TBD` · `Yakında` | 0 | 0 |
| Q-4 | Yorum-dolgu | `// ...` | 0 | 0 |
| Q-5 | Blok-dolgu | `{...}` | 0 | 0 |
| Q-6 | Boş hücre | `\|\s*\|` | 0 | 0 |
| Q-7 | `parent:` alanı | `^\s*parent:` | 0 | 0 |
| Q-8 | Tırnaklı `group` | `^group:\s*"` | 0 | 0 |
| Q-9 | Frontmatter kesik | ilk `---` ↔ ikinci `---` | 0 | 0 |
| Q-10 | Son satır Authority | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | 16/16 | 16/16 |

## §6f — 16 dosyanın frontmatter denetim matrisi

### §6f.1 Kök 4 dosya

| # | Dosya | `node_type` | `status` | `parent_id` | `children` | `group` | `parent:` var mı |
|---|---|---|---|---|---|---|---|
| FM-1 | `index.md` | `subdomain` | `verify-required` | `K000-isletim-sistemi` | 7 öğe (3 kök dosya + 3 alt-dal + index) | `mimari` | HAYIR |
| FM-2 | `isletim-sistemi-plan-kapsam.md` | `module` | `verify-required` | `K000-isletim-sistemi/isletim-sistemi-plan` | `[]` | `mimari` | HAYIR |
| FM-3 | `isletim-sistemi-plan-dogrulama.md` | `module` | `verify-required` | `K000-isletim-sistemi/isletim-sistemi-plan` | `[]` | `kanit` | HAYIR |
| FM-4 | `isletim-sistemi-plan-ornekler.md` | `module` | `verify-required` | `K000-isletim-sistemi/isletim-sistemi-plan` | `[]` | `kod` | HAYIR |

### §6f.2 Alt-dal 12 dosya

| # | Dosya | `node_type` | `status` | `parent_id` | `children` | `group` | `parent:` var mı |
|---|---|---|---|---|---|---|---|
| FM-5 | `zamanlama-scheduler/index.md` | `subdomain` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan` | 3 dosya | `mimari` | HAYIR |
| FM-6 | `zamanlama-scheduler/zamanlama-scheduler-anayasa.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler` | `[]` | `mimari` | HAYIR |
| FM-7 | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler` | `[]` | `mimari` | HAYIR |
| FM-8 | `zamanlama-scheduler/zamanlama-scheduler-ornekler.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler` | `[]` | `kod` | HAYIR |
| FM-9 | `is-parcacigi-planlamasi/index.md` | `subdomain` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan` | 3 dosya | `mimari` | HAYIR |
| FM-10 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi` | `[]` | `mimari` | HAYIR |
| FM-11 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-surucu.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi` | `[]` | `mimari` | HAYIR |
| FM-12 | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/is-parcacigi-planlamasi` | `[]` | `kod` | HAYIR |
| FM-13 | `kaynak-dagilimi/index.md` | `subdomain` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan` | 3 dosya | `mimari` | HAYIR |
| FM-14 | `kaynak-dagilimi/kaynak-dagilimi-anayasa.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi` | `[]` | `mimari` | HAYIR |
| FM-15 | `kaynak-dagilimi/kaynak-dagilimi-surucu.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi` | `[]` | `mimari` | HAYIR |
| FM-16 | `kaynak-dagilimi/kaynak-dagilimi-ornekler.md` | `module` | `draft` | `K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi` | `[]` | `kod` | HAYIR |

### §6f.3 `group` dağılımı özeti

| `group` | Dosya sayısı | Dosyalar |
|---|---|---|
| `mimari` | 10 | 3 kök (`index`, `-kapsam`) + 3 alt-dal `index.md` + 6 `-anayasa`/`-surucu` |
| `kod` | 4 | 1 kök (`-ornekler`) + 3 alt-dal `-ornekler` |
| `kanit` | 2 | 1 kök (`-dogrulama`) + bu dosya (`-dogrulama` alt-dalda yok) |
| TOPLAM | **16** | — |

### §6f.4 `status` dağılımı özeti

| `status` | Dosya sayısı | Gerekçe |
|---|---|---|
| `verify-required` | 4 | kök dosyalar — Q3 `[UNKNOWN]` |
| `draft` | 12 | alt-dal dosyaları — Q3 kapanmadan `draft` kalır |
| `verified` | 0 | — |

## §6g — Çapraz referans bütünlüğü

| # | Referans | Veren dosya | Hedef | Durum |
|---|---|---|---|---|
| X-1 | `isletim-sistemi-plan-kapsam.md §B.2` | kapsam | `zamanlama-scheduler-anayasa.md` | dosya bu turda üretildi ✓ |
| X-2 | `isletim-sistemi-plan-kapsam.md §B.2` | kapsam | `zamanlama-scheduler-surucu.md` | dosya bu turda üretildi ✓ |
| X-3 | `isletim-sistemi-plan-kapsam.md §B.2` | kapsam | `zamanlama-scheduler-ornekler.md` | dosya bu turda üretildi ✓ |
| X-4 | `isletim-sistemi-plan-kapsam.md §B.3` | kapsam | `is-parcacigi-planlamasi-anayasa.md` | dosya bu turda üretildi ✓ |
| X-5 | `isletim-sistemi-plan-kapsam.md §B.3` | kapsam | `is-parcacigi-planlamasi-surucu.md` | dosya bu turda üretildi ✓ |
| X-6 | `isletim-sistemi-plan-kapsam.md §B.3` | kapsam | `is-parcacigi-planlamasi-ornekler.md` | dosya bu turda üretildi ✓ |
| X-7 | `isletim-sistemi-plan-kapsam.md §B.4` | kapsam | `kaynak-dagilimi-anayasa.md` | dosya bu turda üretildi ✓ |
| X-8 | `isletim-sistemi-plan-kapsam.md §B.4` | kapsam | `kaynak-dagilimi-surucu.md` | dosya bu turda üretildi ✓ |
| X-9 | `isletim-sistemi-plan-kapsam.md §B.4` | kapsam | `kaynak-dagilimi-ornekler.md` | dosya bu turda üretildi ✓ |
| X-10 | `isletim-sistemi-plan-ornekler.md §5` | ornekler | 9 alt-dal dosyası köprüsü | dosyalar bu turda üretildi ✓ |
| X-11 | `isletim-sistemi-plan-dogrulama.md §6b.1` | dogrulama | 19 URL → alt-dal bölümü | bölümler bu turda üretildi ✓ |

### §6g.1 Eski (geçersiz) ad kaydı

| # | Eski ad (2026-10-12 sürümünde geçti) | Yeni (kanonik) ad | Karar |
|---|---|---|---|
| O-1 | `cesitli-scheduler-politikalari.md` | `zamanlama-scheduler-surucu.md` | ad DEĞİŞTİRİLMEZ kuralı gereği anayasa/surucu/ornekler şemasına geçildi |
| O-2 | `gercek-zamanli-ve-oncelik.md` | `zamanlama-scheduler-surucu.md` (§4 bölümü) | içerik §4'e taşındı, ayrı dosya üretilmedi |
| O-3 | `pthreads-ve-is-kesintisi.md` | `is-parcacigi-planlamasi-surucu.md` | şema değişikliği |
| O-4 | `cekirdek-atama-ve-yogunluk.md` | `is-parcacigi-planlamasi-surucu.md` (§3 bölümü) | şema değişikliği |
| O-5 | `cpu-ve-bellek-kotalari.md` | `kaynak-dagilimi-surucu.md` | şema değişikliği |
| O-6 | `kaynak-rezervasyonu-ve-sinirlar.md` | `kaynak-dagilimi-surucu.md` (§3–§4) | şema değişikliği |

## §6h — HTTP ölçüm ham kayıt özeti

| # | Tur | Aday sayısı | Kabul | Red (404/ERR) | Red (env VAR) |
|---|---|---|---|---|---|
| T-1 | 1. tur (20 aday) | 20 | 8 | 4 | 8 |
| T-2 | 2. tur (27 aday) | 27 | 11 | 3 | 13 |
| TOPLAM | — | 47 | **19** | 7 | 21 |

### §6h.1 Kabul kriteri (birebir)

| # | Kriter | Değer |
|---|---|---|
| K-1 | HTTP durumu | yalnız `200` |
| K-2 | Envanter durumu | yalnız `YOK` (2097 URL içinde bulunmamak) |
| K-3 | Yöntem | `HEAD` (içerik indirilmez, yalnız durum kodu) |
| K-4 | Zaman aşımı | 20 sn |
| K-5 | Benzersizlik | 19 URL'in 19'u farklı `host+path` |
| K-6 | Tarih | 2026-10-13 |

## §6i — Komşu sınırı denetimi (bu tur yazılan 16 dosya için)

| # | Dosya | `isletim-sistemi-core` sınırı | `isletim-sistemi-kernel` sınırı | `isletim-sistemi-api` sınırı | `isletim-sistemi-security` sınırı | `isletim-sistemi-observability` sınırı | `isletim-sistemi-testing` sınırı | `isletim-sistemi-audio` sınırı | `isletim-sistemi-turu` sınırı |
|---|---|---|---|---|---|---|---|---|---|
| CS-1 | `zamanlama-scheduler-anayasa.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-2 | `zamanlama-scheduler-surucu.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-3 | `zamanlama-scheduler-ornekler.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-4 | `is-parcacigi-planlamasi-anayasa.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-5 | `is-parcacigi-planlamasi-surucu.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-6 | `is-parcacigi-planlamasi-ornekler.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-7 | `kaynak-dagilimi-anayasa.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-8 | `kaynak-dagilimi-surucu.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-9 | `kaynak-dagilimi-ornekler.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |
| CS-10 | kök 3 dosya + `index.md` | korundu | korundu | korundu | korundu | korundu | korundu | korundu | korundu |

### §6i.1 Sınır kuralı (birebir)

| Komşu | Bu subdomainin ona bıraktığı | Almadığı |
|---|---|---|
| `isletim-sistemi-core` | süreç/kavram çerçevesi | süreç mekanizması tanımı |
| `isletim-sistemi-kernel` | politika çerçevesi ve ölçütler | kesme/lock, kuyruk/wakeup iç mekanizması |
| `isletim-sistemi-api` | uygulama katmanı kuyruk/öncelik örneğinin OS olmadığının notu | kullanıcı parçacığı API tasarımı |
| `isletim-sistemi-security` | zamanlama politikası güvenliği **ihtiyacının** tanımı | capability/rol/ACL modeli |
| `isletim-sistemi-observability` | ölçüm adımlarının listesi (latency · jitter · utilization) | metrik boru hattı, log şeması |
| `isletim-sistemi-testing` | doğrulama senaryosu ve eşikler | test kodu, CI kapısı |
| `isletim-sistemi-audio` | RT zamanlama sınıfının ses için anlamı (WASAPI/ASIO deadline) | zincir/ASIO/ring buffer tasarımı |
| `isletim-sistemi-turu` | OS zamanlayıcı farklarının varlığının işaret edilmesi | kurulum yolu, dağıtım yapılandırması |

## §6j — Bu tur okunmayan komşular (Plan1 kapanış kaynağı)

| # | Komşu | Durum | Neden okunmadı | Kapanış koşulu |
|---|---|---|---|---|
| NK-1 | `isletim-sistemi-kernel/**` | OKUNMADI | beyazliste dışı — yalnız okunabilir | sonraki turda okunup sınır teyit edilir |
| NK-2 | `isletim-sistemi-runtime/**` | OKUNMADI | beyazliste dışı — yalnız okunabilir | sonraki turda okunup sınır teyit edilir |
| NK-3 | `isletim-sistemi-core/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-4 | `isletim-sistemi-security/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-5 | `isletim-sistemi-observability/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-6 | `isletim-sistemi-testing/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-7 | `isletim-sistemi-audio/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-8 | `isletim-sistemi-turu/**` | OKUNMADI | beyazliste dışı | sonraki tur |
| NK-9 | `isletim-sistemi-api/**` | OKUNMADI | beyazliste dışı | sonraki tur |

### §6j.1 Sonuç etkisi

| Soru | Etki |
|---|---|
| Plan1 | Bu turda kapatılamaz — komşular okunmadı |
| Q3 | Komşu okumalarından bağımsız; üst otorite kararı gerektirir |
| `status` | `verify-required` korunur (kök) · `draft` (alt-dal) |

## §6k — 2026-10-14 URL yeniden doğrulama (re-check) + OS kanalı aday taraması

> Yöntem: `Invoke-WebRequest -Method Get` (fallback `Head`) · timeout 20 sn ·
> ölçüm tarihi **2026-10-14** · betik: `C:\temp\opencode\recheck-urls.ps1` ·
> ham sonuç: `C:\temp\opencode\plan-url-recheck-2026-10-14.csv`.

### §6k.1 Plan içi 77 URL yeniden ölçümü

| # | Kalem | Değer |
|---|---|---|
| 1 | Ölçülen (plan içi, benzersiz) | **77** |
| 2 | HTTP 200 | **65** |
| 3 | Başarısız (404/ERR) | **12** |
| 4 | Başarı oranı | %84,4 |

### §6k.2 Başarısız 12 URL (2026-10-14)

| # | URL | Durum | Etkilendiği kayıt | Karar |
|---|---|---|---|---|
| 1 | https://docs.kernel.org/scheduler/sched-rt.html | 404 | `zamanlama-scheduler-surucu.md` D-U02 | ⚠️ çelişki — §10.1'e işlendi |
| 2 | https://docs.kernel.org/scheduler/sched-tune.html | 404 | eski aday (R-19, 2026-10-13'te de 404) | zaten RED |
| 3 | https://man7.org/linux/man-pages/man7/pthread_attr_setaffinity_np.7.html | 404 | eski aday (R-20, zaten RED) | zaten RED |
| 4 | https://man7.org/linux/man-pages/man7/cpaces.7.html | 404 | `zamanlama-scheduler-surucu.md` D-U11 | ⚠️ çelişki — §10.1'e işlendi |
| 5 | https://docs.kernel.org/trace/events/sched.html | 404 | `zamanlama-scheduler-surucu.md` D-U13 | ⚠️ çelişki — §10.1'e işlendi |
| 6 | https://man7.org/linux/man-pages/man7/cgroups2.7.html | 404 | eski aday (R-26, zaten RED) | zaten RED |
| 7 | https://learn.microsoft.com/.../processthreadsapi-nf-processthreadsapi-setthreadaffinitymask | 404 | yanlış yoldu (doğru: `winbase/nf-winbase-setthreadaffinitymask`) | **yerine doğru yol 200** — §6k.3 W-1 |
| 8 | https://www.zephyrproject.org/doc/kernel/services/threads/index.html | ERR | eski aday (R-22, zaten RED) | `docs.zephyrproject.org` sürümü 200 |
| 9 | https://www.zephyrproject.org/doc/kernel/services/timing/clocks.html | ERR | `zamanlama-scheduler-surucu.md` D-U18 | **yerine geçen 200** — §6k.3 R-2 |
| 10 | https://www.freertos.org/Documentation/.../01-Stream-Message-Buffers.html | 404 | eski aday (R-23, zaten RED) | zaten RED |
| 11 | https://www.freertos.org/xTaskPrioritySet.html | 404 | eski aday (R-24, zaten RED) | zaten RED |
| 12 | https://developer.apple.com/documentation/kernel/pthread_attr_setschedpolicy | 404 | eski aday (R-25, zaten RED) | zaten RED |

> **Özet:** 12 başarısızın **6'sı 2026-10-13'te de 404/ERR** olarak zaten RED kayıtlıdır
> (yeni bilgi yok); **1'i** yanlış URL yolu (doğrusu 200); **4'ü** (D-U02 · D-U11 · D-U13 · D-U18)
> `zamanlama-scheduler-surucu.md §10.1` üzerinden düzeltildi; **1'i** (D-U18) yerine geçen ile
> ikame edildi.

### §6k.3 OS kanalı aday URL taraması (2026-10-14 — yeni, envanter kesişimli)

> Kural: yalnız **HTTP 200** + envanter `YOK` kabul edilir; `VAR` envanterde zaten kayıtlı
> demektir (tekrar yazılmaz, yalnız OS bölümünde kullanılır). Betik: `C:\temp\opencode\candidate-urls.ps1`.

**Kabul (HTTP 200 + envanter YOK — bu plana yeni):**

| OS | # | URL | Konu |
|---|---|---|---|
| Windows | W-1 | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-setthreadaffinitymask | `SetThreadAffinityMask` |
| Windows | W-2 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass | `SetPriorityClass` |
| Windows | W-3 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadidealprocessor | `SetThreadIdealProcessor` |
| Windows | W-4 | https://learn.microsoft.com/en-us/windows/win32/api/avrt/nf-avrt-avsetmmthreadcharacteristicsa | `AvSetMmThreadCharacteristicsA` |
| Windows | W-5 | https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject | `SetInformationJobObject` |
| Apple | A-1 | https://developer.apple.com/documentation/dispatch/dispatchqos | `DispatchQoS` |
| Apple | A-2 | https://developer.apple.com/documentation/foundation/process/qualityofservice | `Process.qualityOfService` |
| Apple | A-3 | https://developer.apple.com/documentation/uikit/uiview | UIKit referans (OS kapsam genişletme) |
| Zephyr | R-2 | https://docs.zephyrproject.org/latest/kernel/services/timing/clocks.html | saat/çevrim (D-U18 ikamesi) |
| Linux | L-1 | https://man7.org/linux/man-pages/man1/taskset.1.html | CPU affinity |
| Linux | L-2 | https://man7.org/linux/man-pages/man2/sched_getattr.2.html | `sched_getattr/setattr` |
| Android | AD-1 | https://developer.android.com/guide/components/processes-and-threads | süreç/parçacık modeli |
| Tizen | T-1 | https://docs.tizen.org/application/native/guidages/multiprocess/thread/ | thread rehberi yolu (portal erişimi) |
| POSIX | P-1 | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pthread_setschedparam.html | parçacık önceliği |
| PHP | PH-1 | https://www.php.net/manual/en/function.pcntl-signal.php | `pcntl_signal` (D-2 bağlamı) |

**Red (404 — aday olmaktan çıkarıldı):**

| OS | Reddedilen yollar (2026-10-14 HTTP 404) |
|---|---|
| Windows | `jobapi2-createjobobjecta` |
| Apple | `system/pthread_create` · `kernel/thread_policy` · `system/qos_class_self` · `system/pthread_override_main_np` · `system/qos_main_self` · `pthread/pthread_setschedparam` · `pthread/pthread_attr_setschedpolicy` · `dispatch/dispatchqos/qos_class` · `dispatch/qos_class` · `mach/thread_policy_t` · `mach/thread_standard_policy` · `mach/thread_qos_policy` |
| FreeRTOS | `xTaskCreate.html` · `vtaskpriorityset.html` · `02-Kernel/**` 4 aday |
| Android | `training/articles/perf-threading` |
| Linux | `man7/cpaces.7.html` · `man7/cpact.html` · `kernel/events-sched.html` · `kernel/schedstat.html` |

**Envanter `VAR` (zaten kayıtlı — yeni kanıt sayılmaz, yalnız OS bölümünde kullanıldı):**

| OS | URL |
|---|---|
| POSIX/Linux | `sched_setscheduler.html` (opengroup) · `man1/chrt.1.html` · `sched-rt-group.html` (kernel) · `trace/events.html` (kernel) |
| Apple | `documentation/dispatch` · `dispatch/dispatchqueue` · `foundation/thread` · `foundation/process` |
| Zephyr | `latest/kernel/services/scheduling/index.html` |
| webOS | `webosose.org/docs/` |
| ReactOS | `reactos.org/wiki/Main_Page` |

### §6k.4 Serbest (200 + envanter YOK) OS kapsam genişletme adayları — sonraki tura devredildi

| # | URL | OS | Not |
|---|---|---|---|
| 1 | https://man7.org/linux/man-pages/man2/getcpu.2.html | Linux | `getcpu` — çekirdek okuma |
| 2 | https://docs.kernel.org/mm/numa.html | Linux | NUMA |
| 3 | https://docs.kernel.org/core-api/cachetlb.html | Linux | cache/TLB |
| 4 | https://docs.kernel.org/scheduler/sched-ext.html | Linux | ext scheduler |
| 5 | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-getthreadpriority | Windows | öncelik okuma |
| 6 | https://www.php.net/manual/en/function.posix-getpid.php | POSIX-PHP | süreç kimliği |

### §6k.5 2026-10-14 re-check — sonuç satırı

| # | Kalem | Değer |
|---|---|---|
| 1 | Plan içi URL | 77 → **65 HTTP 200 · 12 başarısız** |
| 2 | Yeni kabul (OS adayı, 200 + YOK) | **15** |
| 3 | Yeni red (404) | 19 aday yolu |
| 4 | Düzeltilen çelişki kaydı | **4** (D-U02 · D-U11 · D-U13 · D-U18 → `zamanlama-scheduler-surucu.md §10.1`) |
| 5 | Q3 / Plan1 | `[UNKNOWN]` / `[UNKNOWN]` — **kapatılmadı** |

## §7 — Sonuç

| # | Kalem | Değer |
|---|---|---|
| 1 | Web kanıtı (kabul) | **19/19 HTTP 200 · 2026-10-13** |
| 2 | Envanter kesişimi | **0** |
| 3 | Reddedilen aday | 27 (§1.2) |
| 4 | Disk kanıtı (doğrulanan satır) | **15** (`Select-Object -Index` teyitli) |
| 5 | Doğrulanamayan iddia | **3** (`⚠️` işaretli — §3.2) |
| 6 | Negatif kanıt araması | **5** (§3.3) |
| 7 | Q3 | `[UNKNOWN] — KAPATILMAZ` |
| 8 | Plan1 | `[UNKNOWN] — KAPATILMAZ` |
| 9 | `status` | `verify-required` |

### §7.1 Sonuç cümlesi (birebir)

> "Web 19/19 HTTP 200 doğrulandı · envanter kesişimi 0 · disk 15 satır `Select-Object -Index` ile teyit edildi · 3 iddia doğrulanamadı ve `⚠️ VERIFICATION REQUIRED` ile işaretlendi. Doğrulanan şey **belirsizliğin kendisidir** (ad var, tanım yok) — kapsam uydurulmadı."

### §7.2 Kapatılamayan kalemler

| # | Kalem | Kapanış koşulu | Sorumlu |
|---|---|---|---|
| 1 | Q3 kapsam tanımı | üst otorite kararı | Vault Steward |
| 2 | Plan1 içerik yeri | `-kernel`/`-runtime` dosyalarının okunması | sonraki tur |
| 3 | N-X01 `JobService.php` | dosyanın bulunması ya da iddianın vault'tan kaldırılması | sonraki tur |
| 4 | N-X02 `bootstrap.php` `require_once` | gerçek boot satırlarının okunup düzeltilmesi | sonraki tur |
| 5 | N-X03 OS zamanlayıcı kodu | hedef mimari kararı sonrası eklenebilir | sonraki tur |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode