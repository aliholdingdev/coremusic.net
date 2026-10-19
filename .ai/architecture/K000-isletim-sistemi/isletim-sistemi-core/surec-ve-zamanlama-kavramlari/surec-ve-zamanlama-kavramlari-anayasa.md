---
node_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari/surec-ve-zamanlama-kavramlari-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/surec-ve-zamanlama-kavramlari
evidence:
  - shared/src/Middleware/MiddlewarePipeline.php:42
  - shared/src/AI/AIWorkflow.php:51
  - shared/tests/Repository/ApiKeyCreateCommandTest.php:253
validation: "2026-10-13 · 4 yeni URL HTTP 200 (envanter kesişimi 0) · repo satırları ReadAllLines ile birebir · dolgu 0"
open_questions:
  - "Zamanlama politikası (CFS/EEVDF detayı) bu dalda işlenmez → `isletim-sistemi-plan` sınırı (Q-S1)"
  - "Hedef platform timer API seti (Q4) → UNKNOWN"
group: mimari
---

# surec-ve-zamanlama-kavramlari — Anayasa

> Bu dosya **bağlayıcı tanımlar ve ilkeler** taşır: "süreç nedir, zamanlama nedir, hangi
> kural ihlal edilemez". Mekanizma envanteri `-surucu.md`'de, repo kanıtları
> `-ornekler.md`'dedir. Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs.

## §0 ELI15

İşletim sistemi bir orkestratör gibidir: her programa bir "süreç" (rol) verir, CPU'yu
saat dilimleriyle paylaştırır ve sırayı kuyruk kuralıyla belirler. Bu dosya bu oyunun
**kurallarını** yazar; kimin nasıl oynadığını `-surucu.md`, bizim sahnedeki karşılıklarımızı
`-ornekler.md` anlatır.

## §1 Amaç

| # | Amaç | Karşılığı |
|---|---|---|
| 1 | Süreç/zamanlama terimlerini tek anlamda sabitlemek | §3 |
| 2 | Bağlayıcı ilkeleri (ihlal edilemez kurallar) listelemek | §4 |
| 3 | Platformlar-arası eşdeğerlikleri işaretlemek | §5 |
| 4 | Repo sargılarını kavrama bağlamak | §6 |

Web araştırması (bu dosya · envanter kesişimi 0):

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `sched_getaffinity(2)`, sürecin CPU kümesini (bağlı olduğu çekirdekleri) sorgular | https://www.man7.org/linux/man-pages/man2/sched_getaffinity.2.html | 2026-10-13 | ✓ HTTP 200 |
| Windows süreç/iş parçacığı API seti ayrı sayfada tanımlanır (procthread) | https://learn.microsoft.com/en-us/windows/win32/procthread/process-and-thread-functions | 2026-10-13 | ✓ HTTP 200 |
| Apple `Thread`, iplik yaşam döngüsü ve öncelik API'sini tanımlar | https://developer.apple.com/documentation/foundation/thread | 2026-10-13 | ✓ HTTP 200 |
| `CreateThread`, yeni iplik yaratıp giriş noktasını çalıştırır (Windows) | https://learn.microsoft.com/en-us/windows/win32/procthread/creating-threads | 2026-10-13 | ✓ HTTP 200 |

## §2 Kapsam

### §2.1 Kapsam içinde (bu dal)

| # | Konu | Not |
|---|---|---|
| 1 | Süreç kavramı (kimlik, adres alanı, yaşam döngüsü) | tanım düzeyi |
| 2 | İplik kavramı (paylaşılan adres alanı, ayrı yığın) | tanım düzeyi |
| 3 | Zamanlama kavramları (kuyruk, zaman dilimi, geçiş, öncelik) | mekanizma `-surucu.md`'de |
| 4 | Zaman ölçümü (mono/gerçek saat, çözünürlük) | repo sargıları `-ornekler.md`'de |
| 5 | Zamanlayıcı ve sinyal kavramları | yalnız tanım + upstream |
| 6 | Bekleyiş/uyandırma ve senkronizasyon primitifleri | yalnız tanım + upstream |
| 7 | PHP'nin süreç sargıları (`proc_open` vb.) | repo kanıtı ile |

### §2.2 Kapsam dışı (komşuya gider)

| # | Konu | Gittiği dal |
|---|---|---|
| 1 | Kuyruk politikasının kendisi (CFS/EEVDF hesabı, nice eğrisi) | `isletim-sistemi-plan` |
| 2 | Çekirdek içi veri yapıları (task_struct, runqueue) | `isletim-sistemi-kernel` |
| 3 | Syscall yüzeyinin tamamı (`clone(2)`, `sched_setscheduler(2)`) | `isletim-sistemi-api` |
| 4 | `setuid` ile kimlik değiştirme | `isletim-sistemi-security` |
| 5 | ftrace/perf ile zamanlama gözlemi | `isletim-sistemi-observability` |
| 6 | kselftest ile zamanlama testi | `isletim-sistemi-testing` |
| 7 | Windows/Linux/macOS zamanlama karşılaştırma raporu | `isletim-sistemi-turu` |

## §3 Temel tanımlar (bağlayıcı sözlük)

| # | Terim | Bağlayıcı tanım | Eşdeğer/yakın terim |
|---|---|---|---|
| 1 | Süreç | Çalıştırılan programın **çalışan** örneği; kendi adres alanı ve kimliği vardır | task, job (UI'da) |
| 2 | İplik | Süreç içinde ayrı yürütme akışı; adres alanını kardeşleriyle **paylaşır** | thread, task (macOS terimi) |
| 3 | PCB | Çekirdeğin süreç hakkında tuttuğu kayıt kümesi (durum, kayıt seti, kimlik) | task_struct (Linux) |
| 4 | TCB | İplik hakkında tuttuğu kayıt kümesi | THREADENTRY (Windows) |
| 5 | Adres alanı | Sürecin gördüğü bellek haritası (kod, veri, yığın, eşlenmiş) | address space |
| 6 | Durum (state) | Hazır / çalışır / bekler / sonlanmış — dört ana geçiş durumu | runnable, blocked, zombie |
| 7 | Kuyruk | CPU bekleyen hazır süreçlerin sırası (politika belirler) | runqueue |
| 8 | Zaman dilimi | Kuyruktan alınınca verilen azami CPU süresi dilimi | quantum, timeslice |
| 9 | Geçiş (context switch) | Bir yürütmeden diğerine kayıt setinin değiştirilmesi | context switch |
| 10 | Preemption | Zaman dolumu/öncelikle yürütmenin zorla kesilmesi | preemption |
| 11 | Gönüllü bırakma | Yürütmenin kendisi beklemeye geçmesi (`yield`) | voluntary switch |
| 12 | Mono saat | Geriye gitmeyen ölçüm saati; aralıklar için zorunlu | `CLOCK_MONOTONIC` |
| 13 | Gerçek saat | Takvim saati; NTP/düzeltme ile kayabilir | `CLOCK_REALTIME` |
| 14 | Zamanlayıcı | Belirli bir anda/belirli aralıklarla tetiklenen mekanizma | timer |
| 15 | Sinyal | Sürece gönderilen asenkron olay bildirimi | signal |
| 16 | Async-signal-safe | Sinyal yığınından güvenle çağrılabilecek fonksiyon kümesi | `signal-safety(7)` |
| 17 | Zombie | Bitmiş ama `wait` edilmemiş süreç kaydı | defunct |
| 18 | Yetim | Babası ölmüş, adopte edilmiş süreç | orphan |
| 19 | Bekleyiş | Bir kaynağın/olayın serbestleşmesini bekleme durumu | blocking, wait |
| 20 | Uyandırma | Bekleyen yürütmenin tekrar hazır hâle getirilmesi | wakeup |
| 21 | Mutex | Karşılıklı dışlama kilidi; sahibi olan iplikte kilitleme | critical section lock |
| 22 | Semafor | Sayaç bazlı izin verme primitifi | counting semaphore |
| 23 | Olay/koşul | "Bir şey oldu" bildirimi; bekleme/uyandırma çifti | event, condition variable |
| 24 | Ölü kilit | İki+ yürütmenin karşılıklı beklemesi | deadlock |
| 25 | Öncelik terslemesi | Düşük önceliklinin yüksek öncelikliyi bekletmesi | priority inversion |
| 26 | Öncelik devri | Terslemede geçici öncelik yükseltme | priority inheritance |
| 27 | Bağlılık (affinity) | İpliğin çalıştırılabildiği CPU kümesi | CPU affinity |
| 28 | Öncelik | Kuyruktaki sırayı belirleyen sayısal/hiyerarşik değer | priority, nice |
| 29 | CPU zamanı | İpliğin gerçekten işlemci üzerinde geçirdiği süre | CPU time |
| 30 | Duvar süresi | Gözlemcinin beklediği gerçek süre | wall-clock time |
| 31 | Kilit beklemesi | Kilidi almak için geçirilen uyuyan süre (CPU zamanı değildir) | lock contention |
| 32 | Ölçüm çözünürlüğü | Saatin bildirebildiği en küçük adım | resolution |
| 33 | Uyku (sleep) | Sürecin belirli süre beklemeye geçmesi | sleep, delay |
| 34 | Zaman aşımı | Beklenen olayın gelmemesi durumu (uygulama politikası) | timeout |
| 35 | Çıkış kodu | Sürecin bitişinde bıraktığı 8-bit sonuç | exit status |
| 36 | Ortak|iş (job) | Kullanıcı açısından tek bir çalıştırma | batch job |
| 37 | Atama (scheduler) | Kuyruktan hangi yürütmenin alınacağına karar veren politika | scheduler |
| 38 | Denetleyici | Zaman dilimi dolduğunda kesme üreten çekirdek birimi | timer interrupt |
| 39 | Kesme | Donanım/çekirdeğin yürütme akışını zorla değiştirmesi | interrupt |
| 40 | Yığın (stack) | Çağrı çerçevelerinin tutulduğu iplik-bazlı bellek alanı | stack |

### §3.4 Durum geçiş matrisi

| # | Durum | Geçtiği durumlar | Tetikleyen | Repo sargısı |
|---|---|---|---|---|
| 1 | Yeni (created) | Hazır | `fork`/`exec`/`CreateProcessW` | `proc_open` |
| 2 | Hazır (ready) | Çalışır | atama (scheduler) | yok |
| 3 | Çalışır (running) | Hazır · Bekler · Sonlandı | zaman dolumu · I/O · exit | `hrtime` ölçümü |
| 4 | Bekler (blocked) | Hazır | kaynak/olak serbestleşmesi | `proc_close` bekleme |
| 5 | Sonlandı (zombie) | Yok | `wait` ile kayıt okunması | `proc_close` |
| 6 | Askıya alınmış | Hazır | yönetici/sinyal (suspend) | yok (PHP'de sınırlı) |

### §3.5 Ölçüm birimleri ve dönüşümleri

| # | Birim | Kaynak | Dönüşüm | Uygulama karşılığı |
|---|---|---|---|---|
| 1 | nanosaniye (ns) | `hrtime(true)` | 1 s = 1e9 ns | mono ölçüm |
| 2 | milisaniye (ms) | ns / 1_000_000 | 1 s = 1000 ms | raporlama birimi (P8) |
| 3 | mikrosaniye (µs) | ns / 1000 | 1 ms = 1000 µs | ince profil |
| 4 | saniye (float) | `microtime(true)` | tarih damgası | başlangıç/bitiş (P2) |
| 5 | ms (int) | `(int)(microtime*1000)` | damga yuvarlama | UUIDv7 (P9) |
| 6 | 100 ns birim | Windows `FILETIME` | 1e7 birim = 1 s | platform farkı |
| 7 | CPU zamanı | `getrusage`/`GetProcessTimes` | duvar süresinden farklı | yalnız işlemci |
| 8 | duvar süresi | gerçek saat farkı | bekleme dahil | uçtan uca gecikme |
| 9 | çözünürlük | `clock_getres` | en küçük adım | eşik yorumu |
| 10 | zaman aşımı | uygulama politikası | ms cinsinden sınır | her beklemede (P17) |

## §4 Bağlayıcı ilkeler (ihlal edilemez kurallar)

| # | İlke | Gerekçe | Kanıt/delil |
|---|---|---|---|
| P1 | Ara süre ölçümü mono saatten alınır | duvar saati kayar, ölçüm bozulur | `MiddlewarePipeline.php:42` (`hrtime`) |
| P2 | Takvim damgası gerçek saatten alınır | dosya/log sıralaması tarihsel olmalı | `microtime(true)` sargıları |
| P3 | Alt süreç başlatma `proc_open` ailesiyle olur; `exec` doğrudan çağrılmaz | PHP zaten sargıdır | `ApiKeyCreateCommandTest.php:253` |
| P4 | Alt sürecin çıkış kodu okunur; sessizce atılmaz | hata gözlemlenmezse gizlenir | `ApiKeyCreateCommandTest.php:268` |
| P5 | Borular (pipe) kapatılır; açık boru süreci kilitler | EOF yoksa alt süreç asla bitmez | `ApiKeyCreateCommandTest.php:255` |
| P6 | Zamanlayıcılar süreç-bazlı değil, olay-bazlı kullanılır | bekleme döngüsü CPU yakar | upstream `timer_create(2)` |
| P7 | Sinyal yığınında async-signal-safe olmayan çağrı yapılmaz | veri bozulması | upstream `signal-safety(7)` |
| P8 | Ölçüm sonuçları `ms` cinsinden tek birimde raporlanır | birim karışıklığı yanlış eşik üretir | `MiddlewarePipeline.php:46` |
| P9 | Zaman damgası ms'e yuvarlanırken kayıp bilinçli kabul edilir | kayıp yok sayılırsa eşikler kayar | `UuidV7.php:19` |
| P10 | Bootstrap'ta zaman dilimi sabitlenir | farklar log sıralamasını bozar | `RuntimeBootstrap.php:9` |
| P11 | Uzun işlerde başlangıç/bitiş çifti yazılır | tek uçlu ölçüm yarım iş gösterir | `AIWorkflow.php:51` ve `:80` |
| P12 | Oturum başlatma tek noktadan yapılır | ikinci `session_start` yarış üretir | `SessionInitializer.php:23` |
| P13 | CPU bağlığı (affinity) uygulama düzeyinde sabitlenmez | taşınabilirlik kaybı | upstream `sched_getaffinity(2)` |
| P14 | Öncelik/`nice` uygulama düzeyinde değiştirilmez | yetki ve öngörülemezlik riski | upstream (`nice(2)`) |
| P15 | `yield` bir "hızlandırma" değildir; yalnızca sıradanış bildirir | yanlış kullanımı regresyon yapar | upstream (`sched_yield(2)`) |
| P16 | Zombie oluşumu bekleme (`wait`) ile önlenir | kayıt sızıntısı | upstream (`waitpid(2)`) |
| P17 | Zaman aşımı her beklemede tanımlanır | sonsuz bekleme kilidi andırır | uygulama politikası |
| P18 | Ölçüm döngüsü dışında `sleep` ile senkron yapılmaz | testler bayraklı olur | QA kuralı |
| P19 | Mono/gerçek saat karışımı bir ölçümde kullanılmaz | fark negatif/bozuk çıkar | P1+P2 |
| P20 | Zamanlayıcı tetiklerinde yapılan iş kısa tutulur | uzun iş diğer tetikleri kaydırır | upstream (`timer_create(2)`) |
| P21 | İplikler arası durum paylaşımı atomik/senkronize edilir | veri yarışı | `-surucu.md` §4 |
| P22 | Bekleme sonsuz döngüye bağlanmaz; sayaç/zaman aşımı eklenir | asılı süreç | uygulama politikası |
| P23 | Çıkış kodu 8-bit okunur (`& 0xFF`) | taşma şaşkınlığı | upstream (`exit(3)`) |
| P24 | Ölçüm körlüğü (metrik yokluğu) "sağlıklı" sayılmaz | gözlenmeyen ihlal var sayılmaz | `-dogrulama.md` §B |

## §5 Platform matrisi (eşdeğerlik tablosu)

| # | Kavram | Linux | Windows | macOS | PHP sargısı |
|---|---|---|---|---|---|
| 1 | Süreç başlatma | `fork`+`execve` · `clone` | `CreateProcessW` | `posix_spawn` | `proc_open` · `exec` |
| 2 | İplik yaratma | `pthread_create` | `CreateThread` | `pthread_create` · `NSThread` | PHP ZTS (sınırlı) |
| 3 | Bekleme/exit | `waitpid` | `WaitForSingleObject` | `waitpid` | `proc_close` |
| 4 | Mono saat | `clock_gettime(CLOCK_MONOTONIC)` | `QueryPerformanceCounter` | `mach_absolute_time` | `hrtime(true)` |
| 5 | Gerçek saat | `clock_gettime(CLOCK_REALTIME)` | `GetSystemTimeAsFileTime` | `gettimeofday` | `microtime(true)` |
| 6 | Saat çözünürlüğü | `clock_getres` | `GetSystemTimeAdjustment` | `mach_timebase_info` | PHP'de yok → UNKNOWN |
| 7 | Zamanlayıcı | `timer_create`/`timer_settime` | `CreateWaitableTimerW` | `dispatch_source` | `pcntl_alarm` (yalnız CLI) |
| 8 | Uyku | `nanosleep` | `Sleep` | `sleep` | `usleep` · `time_nanosleep` |
| 9 | CPU bağlığı | `sched_setaffinity` | `SetThreadAffinityMask` | `thread_affinity_policy` | yok |
| 10 | Öncelik | `nice` · `setpriority` | `SetThreadPriority` | `setpriority` | yok |
| 11 | CPU zamanı | `clock_gettime(CLOCK_PROCESS_CPUTIME_ID)` | `GetProcessTimes` | `getrusage` | `getrusage` sargısı |
| 12 | Kuyruk politikası | CFS/EEVDF (çekirdek içi) | Windows kuyruk sınıfı | Mach politika | yok (PHP) |
| 13 | Sinyal | `sigaction` | sınırlı (Console event) | `sigaction` | `pcntl_signal` (CLI) |
| 14 | Zombie önleme | `wait`/`waitpid` | nesne tabanlı bekleme | `waitpid` | `proc_close` |
| 15 | Bağlı iplik/yığın | iplik başına yığın | iplik başına yığın | iplik başına yığın | PHP istek-ipliği |
| 16 | Ortak|iş kuyruğu | cgroup/`systemd` | Job object | `libdispatch` | yok |
| 17 | Kesme/uyandırma | futex | `WaitOnAddress` | `os_sync` | yok |
| 18 | Ölçüm birimi | ns | 100 ns birim | ns (zaman tabanı) | ns (`hrtime`) / s (`microtime`) |

### §5.1 Linux syscall ↔ PHP sargı eşlemesi

| # | Linux syscall | PHP karşılığı | Not |
|---|---|---|---|
| 1 | `clone`/`fork` | `proc_open` · `popen` | PHP sargı kullanır |
| 2 | `execve` | `exec` · `shell_exec` | doğrudan exec yok |
| 3 | `wait4` | `proc_close` · `proc_get_status` | çıkış kodu buradan |
| 4 | `pipe` | `proc_open` descriptor spec | boru PHP içinde |
| 5 | `nanosleep` | `usleep` · `time_nanosleep` | bloklayıcı |
| 6 | `clock_gettime` | `hrtime(true)` | mono/real ayrımı PHP'de yok |
| 7 | `gettimeofday` | `microtime(true)` | gerçek saat |
| 8 | `getrusage` | `getrusage` (PHP fonksiyonu) | CPU zamanı |
| 9 | `sched_yield` | yok | bilinçli yok sayma |
| 10 | `setpriority`/`nice` | yok | P14 |
| 11 | `sched_setaffinity` | yok | P13 |
| 12 | `timer_create` | `pcntl_alarm` (CLI) · `pcntl_signal` | web SAPI'de yok |
| 13 | `sigaction` | `pcntl_signal` (CLI) | web SAPI'de yok |
| 14 | `kill` (sinyal) | `proc_terminate` | sınırlı |
| 15 | `posix_spawn` | `proc_open` | sargı |
| 16 | `prctl`/`PR_SET_NAME` | yok | adlandırma yok |
| 17 | `unshare`/namespace | yok | sandbox ayrı dal |
| 18 | `futex` | yok | çekirdek içi |
| 19 | `sysinfo` | yok | `-bellek` dalı |
| 20 | `getpid` | `getmypid()` | süreç kimliği |

### §5.2 İplik modelleri karşılaştırması

| # | Özellik | POSIX iplik | Windows iplik | PHP istek-ipliği |
|---|---|---|---|---|
| 1 | Adres alanı | süreç içinde paylaşılır | süreç içinde paylaşılır | istek içinde |
| 2 | Yığın | iplik başına | iplik başına | istek başına |
| 3 | Kimlik | süreç kimliği ortak | token ortak | ortak |
| 4 | Oluşturma | `pthread_create` | `CreateThread` | web sunucusu yönetir |
| 5 | Bitirme | `pthread_join` | `WaitForSingleObject` | istek sonunda |
| 6 | Kilitleme | `pthread_mutex` | `SRWLOCK`/`CRITICAL_SECTION` | PHP'de sınırlı (ZTS) |
| 7 | Ölçüm | `clock_gettime` | `QueryPerformanceCounter` | `hrtime` |
| 8 | Kullanım durumu | CLI uzantıları | yerel eklenti | çekirdekte nadir |
| 9 | CoreMusic durumu | yok | yok | istek-ipliği tek model |
| 10 | Risk | veri yarışı | veri yarışı | paylaşımlı durum (session/cache) |

## §6 Repo sargıları (kavram ↔ Dosya/Satır)

> Aşağıdaki satırlar `[IO.File]::ReadAllLines` ile doğrulanmıştır. Bunlar **sargıdır**;
> çekirdek içi zamanlama kodu repoda YOKTUR.

| # | Kavram | Dosya | Satır | Okunan içerik |
|---|---|---|---|---|
| 1 | mono saat başlangıcı | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` |
| 2 | süre hesabı (ns → ms) | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| 3 | gerçek saat başlangıcı | `shared/src/AI/AIWorkflow.php` | 51 | `$startTime = microtime(true);` |
| 4 | çalışma süresi farkı | `shared/src/AI/AIWorkflow.php` | 80 | `$executionTime = microtime(true) - $startTime;` |
| 5 | ms damgası | `shared/src/Security/UuidV7.php` | 19 | `$ms = (int)(microtime(true) * 1000);` |
| 6 | oturum başlatma | `shared/src/PageRouter/SessionInitializer.php` | 23 | `session_start();` |
| 7 | oturum durum bayrağı | `shared/src/PageRouter/SessionInitializer.php` | 31 | `$result['started'] = true;` |
| 8 | bootstrap zaman dilimi | `shared/src/Bootstrap/RuntimeBootstrap.php` | 9 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` |
| 9 | bootstrap hata raporu | `shared/src/Bootstrap/RuntimeBootstrap.php` | 12 | `error_reporting(E_ALL);` |
| 10 | bootstrap log anahtarı | `shared/src/Bootstrap/RuntimeBootstrap.php` | 19 | `ini_set('log_errors', '1');` |
| 11 | alt süreç başlatma | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `$handle = proc_open(` |
| 12 | boru tanıtıcıları | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 255 | `[0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],` |
| 13 | çıkış kodu okuma | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 268 | `return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];` |
| 14 | komut çalıştırma | `shared/src/AI/AIEngine.php` | 295 | `$output = shell_exec($cmd);` |
| 15 | argüman kaçışı | `shared/src/AI/AIEngine.php` | 292 | `escapeshellarg($filePath)` |

### §6.1 Satırların kavramsal yorumu

| # | Satır | Yorum |
|---|---|---|
| 1 | `MiddlewarePipeline.php:42` | mono saat başlangıcı — P1'in kanıtı |
| 2 | `MiddlewarePipeline.php:46` | ns → ms dönüşümü — P8 birim kuralı |
| 3 | `AIWorkflow.php:51` | gerçek saat başlangıcı — P2 |
| 4 | `AIWorkflow.php:80` | başlangıç/bitiş çifti — P11 |
| 5 | `UuidV7.php:19` | ms damgası + bilinçli yuvarlama — P9 |
| 6 | `SessionInitializer.php:23` | tek `session_start` — P12 |
| 7 | `SessionInitializer.php:31` | başlatma sonucu bayrağı (gözlemlenebilirlik) |
| 8 | `RuntimeBootstrap.php:9` | tz sabitleme — P10 |
| 9 | `RuntimeBootstrap.php:12` | hata raporu tam açık — gözlem kuralı |
| 10 | `RuntimeBootstrap.php:19` | log anahtarı — gözlem kuralı |
| 11 | `ApiKeyCreateCommandTest.php:253` | süreç başlatma — P3 |
| 12 | `ApiKeyCreateCommandTest.php:255` | boru tanıtıcıları — P5 |
| 13 | `ApiKeyCreateCommandTest.php:268` | çıkış kodu okuma — P4 |
| 14 | `AIEngine.php:295` | `shell_exec` → `null` ayrımı (hata) |
| 15 | `AIEngine.php:292` | argüman kaçışı — komut enjeksiyonu sınırı |

## §7 Zamanlama senaryoları (gözlenebilir beklenen davranış)

| # | Senaryo | Gözlem | Beklenen | İhlal ne demek |
|---|---|---|---|---|
| Z1 | Mono saat ilerler | iki `hrtime` okuması | ikinci ≥ birinci | saat geriye gidiyor (yanlış API) |
| Z2 | Ölçüm negatif olamaz | zaman damgası farkı | ≥ 0 | sistem saati kaymış (karışım) |
| Z3 | Uzun iş süresi raporlar | başlangıç/bitiş farkı | pozitif, birim ms | çift eksik (P11 ihlali) |
| Z4 | `proc_open` bilinmeyen komut | exit kodu | 0 değil | hata yutuluyor (P4 ihlali) |
| Z5 | Boru kapatılınca alt süreç biter | `proc_close` dönüyor | temiz bitiş | boru açık kalmış (P5 ihlali) |
| Z6 | Bootstrap tz sabittir | iki istek arasında log sıralaması | tutarlı | tz sabitlenmemiş (P10 ihlali) |
| Z7 | Oturum iki kez başlatılmaz | `session_start` çağrısı | tek çağrı | yarış riski (P12 ihlali) |
| Z8 | Zamanlayıcı döngüsü kısa iş yapar | tetik süresi | iş süresi < aralık | birikme (P20 ihlali) |
| Z9 | Zombie oluşmaz | `ps` durum sütunu | `Z` yok | `wait` eksik (P16 ihlali) |
| Z10 | Ölçüm çözünürlüğü bilinir | `clock_getres` sonucu | bilinen adım | eşikler yanlış yorumlanır |
| Z11 | CPU bağlığı uygulama ile sabitlenmez | affinity çağrısı yok | yok | P13 ihlali |
| Z12 | `yield` regresyon yaratmaz | `sched_yield` kullanımı | yok/gerekçeli | P15 ihlali |
| Z13 | Uyku senkron değildir | testlerde `sleep` | sayı ≤ eşik | bayraklı test |
| Z14 | Zaman aşımı tanımlıdır | bekleme başına süre sınırı | her beklemede | sonsuz bekleme riski |
| Z15 | Çıkış kodu maskelenir | `exit 300` gözlemi | 44 | P23 ihlali |
| Z16 | CPU zamanı ≠ duvar süresi | iki sayaç farkı | farklı değerler | yanlış yorum |
| Z17 | Kilitleme beklemesi CPU zamanı değildir | kilit bekleyişi ölçümü | düşük CPU + yüksek duvar | yanlış profil |
| Z18 | Sinyal yığını güvenlidir | sinyal içi çağrı listesi | yalnız safe set | P7 ihlali |
| Z19 | Öncelik sabitlenmez | uygulamada `nice` çağrısı | yok | P14 ihlali |
| Z20 | Ölçüm körlüğü sayılmaz | metrik yokluğu | "sağlıklı" değil | P24 ihlali |

### §7.1 Senaryo ↔ ölçüm kaydı

| # | Senaryo | Ölçüm | Kayıt biçimi |
|---|---|---|---|
| 1 | Z1 | iki `hrtime` okuması farkı | ms, negatifse ihlal |
| 2 | Z2 | 1000 damga farkının min değeri | negatif varsa ihlal |
| 3 | Z3 | başlangıç/bitiş çifti sayısı | çiftsiz kayıt ihlal |
| 4 | Z4 | bilinmeyen komut exit kodu | 0 ise ihlal |
| 5 | Z5 | `proc_close` bekleme süresi | sınırsız ise ihlal |
| 6 | Z6 | log satır zaman sırası | ters sıra varsa ihlal |
| 7 | Z7 | istek başına `session_start` sayısı | 2 ise ihlal |
| 8 | Z8 | tetik aralığı vs iş süresi | iş > aralık ise ihlal |
| 9 | Z9 | `Z` durum süresi | > 0 ise ihlal |
| 10 | Z10 | `clock_getres` sonucu | bilinmeyen ise UNKNOWN |
| 11 | Z14 | bekleme başına maksimum süre | tanımsız ise ihlal |
| 12 | Z15 | `exit 300` → gözlenen kod | 44 değilse ihlal |

### §7.2 Doğrulama kapıları (bu dal)

| # | Kapı | Şart | Aksi durum |
|---|---|---|---|
| 1 | K1 — tanım kapısı | §3'teki her terim tek anlamda | çoğul anlam → revizyon |
| 2 | K2 — ilke kapısı | §4'teki P1-P24 ihlali tespit edilirse kayıt | sessiz geçiş yasak |
| 3 | K3 — kanıt kapısı | her repo iddiası `Dosya:`/`Satır:` taşır | satırsız iddia sayılmaz |
| 4 | K4 — web kapısı | her URL HTTP 200 + envanter dışı | 404/kopya → tablodan çıkar |
| 5 | K5 — sınır kapısı | komşu dal konusu burada işlenmez | işlenmişse taşı |
| 6 | K6 — dürüstlük kapısı | repo'da olmayan yüzey işaretli | "yapıldı" sayılmaz |
| 7 | K7 — tekrarlanabilirlik kapısı | senaryo adımı tek yönerge | tekrar edilemezse ELI15'e dön |
| 8 | K8 — birim kapısı | tüm süreler ms/bilinen birimde | birimsiz kayıt sayılmaz |
| 9 | K9 | `⚠️ VERIFICATION REQUIRED` işaretleri | kapatılmaz, yalnız kanıtla düşer |
| 10 | K10 | UNKNOWN işaretleri | tahminle doldurulmaz |

## §8 Hata ve durum modları

| # | Durum | Belirti | Kök neden | Karşılık |
|---|---|---|---|---|
| 1 | `ENOENT` (süreç) | çalıştırılamadı | komut yolu yanlış | `escapeshellarg` + yol denetimi |
| 2 | `E2BIG` | komut satırı taştı | argüman çok uzun | argüman sınırla |
| 3 | `EAGAIN` | kaynak yetersiz | süreç/iplik sınırı | kuyruğa al/geri çek |
| 4 | `EINTR` | çağrı bölündü | sinyal | yeniden dene (PHP sargısı yutar → UNKNOWN) |
| 5 | `ECHILD` | beklenecek çocuk yok | süreç zaten `wait` edilmiş | çağrı tekrarı |
| 6 | Zombie | `Z` durumu | `wait` edilmemiş | `proc_close`/`wait` çağrısı |
| 7 | Zaman kayması | ölçüm negatif | mono/gerçek karışımı | P19 |
| 8 | Ölçüm patlaması | devasa ms değeri | birim hatası (ns/s) | P8 |
| 9 | Kilit yarışı | veri bozukluğu | atomiklik yok | `-surucu.md` §4 |
| 10 | Ölü kilit | iplikler sonsuz bekliyor | karşılıklı kilitleme | sıralı kilitleme/timeout |
| 11 | Öncelik terslemesi | düşük öncelikli geciktiriyor | kilidi uzun tutan | öncelik devri |
| 12 | CPU yanması | `%100` sürekli | döngü/yield kötü kullanımı | profil + sınır |
| 13 | Uykuya dalma | istek yanıtsız | senkron bekleme | asenkron/sınır |
| 14 | Zamanlayıcı birikmesi | tetikler üst üste | iş aralıktan uzun | işi böl/kuyruklar |
| 15 | Boru tıkanması | alt süreç asılmıyor | stdout dolu, okunmuyor | boruyu oku/kapat |
| 16 | `wait` unutulmuş | kayıt sızıntısı | hata yolunda kapatma | `finally` ile kapatma |
| 17 | tz tutarsızlığı | log sırası bozuk | bootstrap eksik | P10 |
| 18 | Damga taşması | ms → s dönüşümü kayıp | yuvarlama | P9 (bilinçli kabul) |
| 19 | `EACCES` (süreç) | yetki reddi | kimlik/izin | `-izin-ve-guvenlik` dalı |
| 20 | Ölçüm kaybı | metrik eksik | istisna yutma | log + alarm |

### §8.1 errno ↔ belirti tablosu (süreç/zamanlama)

| # | errno | Anlamı | Süreç/zamanlama belirtisi |
|---|---|---|---|
| 1 | `EPERM` | yetki yok | öncelik/sinyal reddi |
| 2 | `ESRCH` | süreç yok | yanlış PID |
| 3 | `EINTR` | çağrı bölündü | sinyal geldi |
| 4 | `EIO` | g/ç hatası | boru/ölçüm kaynağı |
| 5 | `E2BIG` | argüman taşması | komut satırı |
| 6 | `ENOEXEC` | çalıştırılabilir değil | biçim hatası |
| 7 | `ECHILD` | çocuk yok | tekrar `wait` |
| 8 | `EAGAIN` | kaynak yetersiz | süreç/iplik sınırı |
| 9 | `ENOMEM` | bellek yetersiz | iplik yığını |
| 10 | `EACCES` | izin yok | yürütme izni |
| 11 | `EFAULT` | geçersiz adres | yanlış işaretçi |
| 12 | `EDEADLK` | ölü kilit | kilitleme sırası |
| 13 | `ERANGE` | aralık dışı | zaman değeri |
| 14 | `EINVAL` | geçersiz argüman | zamanlayıcı yapılandırma |
| 15 | `ENOSYS` | desteklenmiyor | platform farkı |
| 16 | `ETIMEDOUT` | zaman aşımı | bekleme sınırı |
| 17 | `EOWNERDEAD` | kilit sahibi öldü | paylaşımlı kilit |
| 18 | `ENOTRECOVERABLE` | kurtarılamıyor | paylaşımlı kilit |

### §8.2 Kurtarma stratejileri

| # | Hata | Kurtarma | Sahip |
|---|---|---|---|
| 1 | zombie | `wait`/`proc_close` zorunlu (`finally`) | uygulama |
| 2 | boru tıkanması | boruyu oku + kapat | uygulama |
| 3 | sonsuz bekleme | zaman aşımı + sayaç | uygulama |
| 4 | ölü kilit | sıralı kilitleme + timeout | `-surucu.md` |
| 5 | ölçüm patlaması | birim denetimi (P8) | uygulama |
| 6 | zaman kayması | mono/gerçek ayrımı (P19) | uygulama |
| 7 | süreç reddi (`EACCES`) | kimlik/izin incelemesi | `-izin-ve-guvenlik` |
| 8 | kaynak yetersiz (`EAGAIN`) | kuyruğa alma/geri çekme | uygulama |
| 9 | platform farkı (`ENOSYS`) | sargı var mı kontrolü | uygulama |
| 10 | log sırası bozuk | tz sabitleme (P10) | bootstrap |
| 11 | metrik yok | alarm + gözlem dalı | `-observability` |
| 12 | test bayrağı | senkron `sleep` yasağı (P18) | QA |

## §9 Komşu sınırı ve doğrulama kuralları

| # | Konu | Gittiği dal | Bu daldaki sınır |
|---|---|---|---|
| 1 | Kuyruk politikası hesabı | `isletim-sistemi-plan` | yalnız ad + tanım |
| 2 | `task_struct`/runqueue | `isletim-sistemi-kernel` | yalnız PCB/TCB tanımı |
| 3 | `clone(2)`/`sched_setscheduler(2)` detayı | `isletim-sistemi-api` | yalnız eşdeğerlik satırı |
| 4 | `setuid`/yetki | `isletim-sistemi-security` | kapsam dışı |
| 5 | ftrace/perf/perfetto | `isletim-sistemi-observability` | ölçüm sargısı örnek |
| 6 | kselftest/KUnit | `isletim-sistemi-testing` | senaryo dokümanı |
| 7 | OS ailesi karşılaştırma | `isletim-sistemi-turu` | §5 matrisi örnek düzeyi |

Doğrulama kuralları:

| # | Kural | Kapı |
|---|---|---|
| 1 | Her tanım upstream kaynağa bağlanır | §1 web tablosu + `-surucu.md` §7 |
| 2 | Her repo iddiası `Dosya:`/`Satır:` taşır | `-ornekler.md` §2 |
| 3 | `⚠️ VERIFICATION REQUIRED` işareti kapatılmaz | §10 |
| 4 | Ölçüm iddiası tekrarlanabilir adımla verilir | §7 senaryoları |
| 5 | Repo'da olmayan yüzey "yapıldı" sayılmaz | `-ornekler.md` §5 |

### §9.1 Bağımlılık ve devir notları

| # | Bağımlılık | Yön | Not |
|---|---|---|---|
| 1 | `isletim-sistemi-core` (üst düğüm) | bu dosya ← üst | kavram #1 kaynağı |
| 2 | `-surucu.md` | anayasa → sürücü | ilkeler mekanizmaya bağlanır |
| 3 | `-ornekler.md` | anayasa → örnekler | ilkelerin repo kanıtı |
| 4 | `-dogrulama.md` (kök) | örnekler → doğrulama | iddia kapıları |
| 5 | `isletim-sistemi-plan` | sınır | politika işlenmez |
| 6 | `isletim-sistemi-api` | sınır | syscall yüzeyi |
| 7 | `isletim-sistemi-observability` | devir | ölçüm aletleri |

### §9.2 Komşuya devir protokolü (süreç/zamanlama → sınır dal)

| # | Devir | Tetikleyen | Çıktı formatı |
|---|---|---|---|
| 1 | → `isletim-sistemi-plan` | kuyruk politikası hesabı gerekiyorsa (P2 ihlali) | "kavram adı + bu daldaki tanım + soru cümlesi" |
| 2 | → `isletim-sistemi-kernel` | PCB/TCB iç yapısı sorulduğunda (P4) | "tanım + gözlenen semptom + upstream referansı" |
| 3 | → `isletim-sistemi-api` | syscall imzası gerekiyorsa (P6) | "eşdeğerlik satırı + PHP sargısı" |
| 4 | → `isletim-sistemi-observability` | ölçüm aleti istendiğinde (P8) | "senaryo kimliği (Zn) + beklenen ölçüme" |
| 5 | → `-izin-ve-guvenlik-kavramlari` | süreç reddi `EACCES` (§8.2 satır 7) | "hata kodu + tetikleyen işlem" |
| 6 | → `-dosya-sistemi-kavramlari` | süreç I/O yolu (Z3 boru) | "yol + beklenen davranış" |

Devir kuralı: devredilen konu **bu daldaki tanımıyla** taşınır (parçalanmaz); alan dal
"işledim" diyemez, kanıt üretmeden konuyu geri gönderemez (K5).

### §4.2 İlke → gözlenebilir belirti sözlüğü (P-kısa karşılık)

| İlke | Kısa ad | Gözlenebilir belirti (ihlal halinde) |
|---|---|---|
| P1 | süreç ≠ iplik | `getmypid()` sabit, `getmytid()` artıyor |
| P2 | kuyruk ≠ yürütme | yanıt sırası istek sırasından farklı |
| P3 | zaman dilimi tekillik | iki log satırı aynı `time()` değeri |
| P4 | PCB/TCB | süreç bitti ama kaynak sayacı dönmedi |
| P5 | fork/exec ayrımı | `exec` sonrası eski değişken değerleri duruyor |
| P6 | syscall yüzeyi | PHP'de çağrılan işlevin libc karşılığı yok |
| P7 | mono ≠ gerçek | `hrtime` farkı negatif çıkıyor |
| P8 | ölçüm birimi | ms cinsinden sayı saniye gibi davranıyor |
| P9 | sinyal yeniden tetikleme | tek sinyalde iki iş çalışıyor |
| P10 | tz sabitleme | iki sunucuda farklı sıralı log |
| P11 | zombie | süreç tablosunda `Z` durumlu kayıt |
| P12 | kilit sırası | iki süreç birbirini bekliyor (deadlock belirtisi) |
| P13 | kuyruk doygunluğu | tetik aralığı < iş süresi (Z8) |
| P14 | öncelik tersi yok | yüksek öncelikli iş düşük önceliklinin arkasında bekliyor |
| P15 | kaynak sınırı | `EAGAIN`/`EMFILE` log kaydı |
| P16 | süreç grubu | tek süreç kapanınca çocuklar da kapandı |
| P17 | uyku çözünürlüğü | `usleep(1)` gerçekte ~15 ms yatıyor |
| P18 | testte senkron `sleep` | test süresi makineye göre değişiyor |
| P19 | saati geri alma | zaman damgası sırası bozuluyor |
| P20 | kilitli dosya | ikinci süreç `EAGAIN` alıyor |
| P21 | FIFO sırası | bekleyen işler LIFO aktı |
| P22 | timer doğruluğu | `clock_getres` çözünürlüğü ölçüm adımıyla uyuşmuyor |
| P23 | atama (affinity) | iş sabit çekirdekte kalmıyor |
| P24 | gerçek zaman yok | PHP'de `SCHED_FIFO` çağrısı `ENOSYS` veriyor |

## §10 Açık sorular ve referanslar

| # | Soru | Etiket |
|---|---|---|
| Q-S1 | Zamanlama politikası hangi dalın tekelinde? | ⚠️ VERIFICATION REQUIRED |
| Q-S2 | `isletim-sistemi-plan` ile sınır tanımı v2'de yok | ⚠️ VERIFICATION REQUIRED |
| Q-S3 | Hedef platform timer seti (Q4) | UNKNOWN |
| Q-S4 | PHP ZTS iplik modeli hangi dalda? | UNKNOWN |
| Q-S5 | Ölçüm çözünürlüğü PHP'de sorgulanamıyor → eşikler hangi donanımda doğrulandı? | UNKNOWN |

| # | Referans | Durum |
|---|---|---|
| 1 | `sched_getaffinity(2)` (man7) | ✓ 2026-10-13 HTTP 200 |
| 2 | procthread süreç/iş fonksiyonları (Microsoft Learn) | ✓ 2026-10-13 HTTP 200 |
| 3 | `Thread` (Apple Developer) | ✓ 2026-10-13 HTTP 200 |
| 4 | `CreateThread` (Microsoft Learn) | ✓ 2026-10-13 HTTP 200 |
| 5 | `os-master.md` §3 kavram #1 | disk kanıtı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
