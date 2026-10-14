---
title: "Parçacık Planlaması — Sürücü (Derinlik)"
description: "Bir iş birimini parçacığa çeviren planlama yüzeyi: öncelik, uygunluk kümesi, kaynak limiti, fork guard ve kuyruk seçimi PHP 8.4 kanıtlarıyla"
node_type: module
status: active
date: 2026-10-13
owner: Bayram Ali
group: surucu
children: []
---

# Parçacık Planlaması — Sürücü (Derinlik)

> Bu dosya planlama yüzeyini **sürücü (derinlik)** düzeyinde açar: bir iş birimi
> parçacığa çevrilirken hangi öncelik, hangi uygunluk kümesi, hangi kaynak limiti ve
> hangi fork guard ile teslim edilir. Anayasa `is-parcacigi-planlamasi-anayasa.md`
> neyin izinli olduğunu, sürücü **nasıl kurulduğunu**, örnekler ise repo kodunun
> hangi dosyada nasıl göründüğünü gösterir.

## §0 — Kapsam ve Bağ

| Madde | Değer | Kaynak |
|---|---|---|
| Öğrenilecek | öncelik · uygunluk kümesi · RLIMIT · fork guard · parçacık-havuzu seçimi | POSIX + man7 (§8) |
| Kapsam dışı | çekirdek uyandırıcı yazılımı · EEVDF iç yapısı · FPGA/nöromorfik planlama | `isletim-sistemi-core` dalı |
| Ön koşul | `is-parcacigi-planlamasi-anayasa.md` §1–§7 | bu alt dal |
| Bağ (uygulama) | `AIOrchestrator.php` (eşzamanlı orkestrasyon) · `ServiceHealth.php` (paralel denetim) | repo — §6 |
| Bağ (kardeş) | `zamanlama-scheduler/` (sıra politikası) · `kaynak-dagilimi/` (kota bütçesi) | bu dal |

### §0.1 Sürücü — anayasa farkı

| Katman | Soru | Dosya |
|---|---|---|
| Anayasa | ne yasak, ne izinli? | `-anayasa.md` §2–§3 |
| Sürücü (bu) | izinli olan nasıl kurulur, hangi sırayla? | bu dosya §1–§5 |
| Örnekler | repo'da karşılığı hangi satır? | `-ornekler.md` §1–§5 |
| Doğrulama | iddiaların kanıtı var mı? | `-anayasa.md` §4 · bu dosya §7 |

| Sürücü ilkesi | Açıklama |
|---|---|
| Sıra bağımsızlığı | §1→§2→§3→§4→§5 sırayla uygulanır; atlanan adım kararı bozar |
| Kanıt zorunlu | her tablo satırı ya repo satırı ya da §8 kaynaklıdır |
| Bilinmeyen uydurulmaz | sayısal eşik yoksa `[UNKNOWN]` yazılır |

## §1 — Parçacık Öncelik Yüzeyi

### §1.1 Yüzey kataloğu

| Yüzey | Çağrı | Anlamı | PHP yüzü |
|---|---|---|---|
| Nice değeri | `setpriority(2)` / `getpriority(2)` | süreç grubu önceliği; büyük değer = düşük öncelik | yok (CLI'da `proc_open` çocuğu `nice` ile) |
| Öncelik sınıflı planlama | `sched_setscheduler(2)` | SCHED_FIFO/RR için gerçek zamanlı sınıf | yok (CAP_SYS_NICE gerektirir) |
| Parametre okuma | `sched_getparam(2)` | mevcut öncelik parametresi | repo'da yok — `⚠️ VERIFICATION REQUIRED` |
| Parametre yazma | `sched_setparam(2)` | öncelik parametresini ayarlar | repo'da yok — `⚠️ VERIFICATION REQUIRED` |

`getpriority(2)` man sayfası not eder: tekil `getpriority` çağrısı hata ile
belirsizliği `errno` ile çözür; PHP kullanıcı uzayında bu ayrım `posix_*` ile
değil `proc_open` çocuğu üzerinden dolaylı kurulur. Repo'da doğrudan
`setpriority`/`getpriority` çağıran PHP kodu **YOK** (dürüstlük defteri: `-anayasa.md` §3.3).

### §1.2 Öncelik kademeleri ve karşılığı

| Kademe | POSIX karşılığı | CoreMusic karşılığı | Durum |
|---|---|---|---|
| Gerçek zamanlı (FIFO/RR) | `sched_setscheduler(SCHED_FIFO)` | yok | **[UNKNOWN]** — CAP_SYS_NICE + hedef mimari kararı gerek |
| Normal (CFS/EEVDF) | varsayılan | PHP-FPM/Apache havuzunun kendi kuyruğu | dolaylı — sunucu yapılandırması |
| Düşük öncelik (nice +10) | `setpriority` | yok — repo'da çağıran kod YOK | `⚠️ VERIFICATION REQUIRED` |
| Uygulama önceliği | yok (OS bilmmez) | kuyruk sırası: `AIOrchestrator` child kuyruğu | uygulama katmanı |

### §1.3 Öncelik kararı — PHP gerçeği

| Gerçek | Sonuç |
|---|---|
| PHP betiği `nice`/`sched_setscheduler` çağıramaz | öncelik, süreç başlatma anında (`proc_open` çocuğu) veya sunucu yapılandırmasında belirlenir |
| `AIOrchestrator` fork çocuğu `CHILD_TIMEOUT = 15` sn ile sınırlıdır | öncelik değil, **süre** sınırı uygulanır — bkz. §3.4 |
| Repo'da öncelik devri (priority inheritance) tetikleyen kod | **YOK** — `-anayasa.md` §3.6 V-5: uygulama katmanında "öncelik" yalnız kuyruk sırasıdır |

Repo kanıtı — süre sınırı gerçek kodda:

`app/Modules/AI/AIOrchestrator.php` · Satır 20: `const CHILD_TIMEOUT = 15;`

`app/Modules/AI/AIOrchestrator.php` · Satır 45: `$childPid = pcntl_fork();`

### §1.4 Sunucu süreçlerinin gerçek öncelik yüzeyi (CoreMusic bağlamı)

| Sunucu süreci | Öncelik aracı | Repo kanıtı |
|---|---|---|
| Apache (`home.coremusic.net`) | MPM worker/process havuzu — öncelik yok | `.htaccess` (boot zinciri, `-runtime`) |
| IIS + FastCGI (`api`) | worker process limiti | `api/index.php` (§6.4) |
| PHP-FPM | havuz kuyruğu (adm/dyn/max children) | repo'da `php-fpm.conf` **YOK** — `⚠️ VERIFICATION REQUIRED` |
| `proc_open` çocuğu | `nice` opsiyonel; repo'da kullanılmıyor | `ApiKeyCreateCommandTest.php` L253 |
| Cron/CLI işleri | sistem `cron`/`systemd` zamanlayıcısı | repo'da cron betiği **YOK** — `⚠️ VERIFICATION REQUIRED` |

Öncelik yüzeyi PHP için **süreç yöneticisinin** (sunucu/havuz) tekelindedir;
betik öncelik isteyemez, yalnızca kendi iç kuyruğunu sıralar.

## §2 — Uygunluk Kümesi (CPU Seti)

### §2.1 Katmanlar

| Katman | Mekanizma | Etki | PHP yüzü |
|---|---|---|---|
| Çekirdek | `sched_setaffinity(2)` (envanterde) | parçacığı belirli çekirdeğe bağlar | yok |
| Kapsayıcı | cpuset kontrol grubu (`cpuset(7)`) | kümeler, izinli CPU listesi, bellek düğüm bağlama | yok |
| PHP | — | affinity miras alınır: `AIOrchestrator` hangi çekirdeğe düşeceğini seçemez | dolaylı |

`cpuset(7)` not eder: cpuset içeriği *kapsayıcı* bir modeldir; bir alt cpuset'e
eklenen CPU, üst kümeyi otomatik olarak *daraltmaz*. Planlama tarafında bu,
"parçacığı bu çekirdeğe koy" emrinin **hiyerarşiye uygun** verilmesi demektir.

### §2.2 Uygunluk kümesi karar tablosu

| Durum | Eylem | Sahip |
|---|---|---|
| Tek çekirdek sunucu | affinity ayarı gerekmez | operasyon |
| Çok çekirdek + RT iş | cpuset ile daraltma, sonra `sched_setaffinity` | `-kernel` (policy) |
| Kapsayıcı (Docker) | `--cpuset-cpus` ile sınır | operasyon | docker `engine/containers/run` (kaynak: upstream docs) |
| PHP-FPM havuzu | affinity yok; havuz boyutuyla dolaylı etki | sunucu yapılandırması |
| Repo | affinity çağıran kod **YOK** | dürüstlük defteri — `-anayasa.md` §3.3 |

### §2.3 Eşzamanlı denetim örneği (uygunluk kümesiz gerçek)

| Dosya | Satır | Metin | Yorum |
|---|---|---|---|
| `shared/src/Api/Registry/ServiceHealth.php` | L24 | `* Execute parallel health checks.` | "paralel" iddiası; affinity **yok** |
| `shared/src/Api/Registry/ServiceHealth.php` | L26 | `public function checkAll(): array` | tüm denetimler tek çağrıda |

Sonuç: repo "paralel" derken **mantıksal** paralellik (istek başına eşzamanlılık)
kasteder; çekirdek bağımlı planlama (affinity/cpuset) repo'da **YOK**.

### §2.4 Uygunluk kümesi — uygulama yansıması (ne yapılır, ne yapılmaz)

| # | Kural | Uygulanabilirlik | Gerekçe |
|---|---|---|---|
| A-1 | affinity kararı betikten verilmez | bağlayıcı | PHP'de elçi yok (§2.1) |
| A-2 | cpuset kararı operasyonel katmanda verilir | bağlayıcı | kapsayıcı model (§2.1) |
| A-3 | "paralel" kelimesi kod yorumunda ölçüm iddiası sayılmaz | bağlayıcı | `ServiceHealth.php` L24 — iddia, ölçüm değil |
| A-4 | RT sınıfı yalnız CAP_SYS_NICE ile açılır | bağlayıcı | `sched_setscheduler` (§1.2) |
| A-5 | affinity ölçümü olmadan "çekirdek bağımlı" iddiası yazılmaz | bağlayıcı | `[UNKNOWN]` (§7.7 O-5) |

## §3 — Kaynak Limiti (RLIMIT) Yüzeyi

### §3.1 POSIX limit kataloğu

| Limit | POSIX çağrısı | Parçacık/süreç etkisi | Aşım davranışı |
|---|---|---|---|
| CPU süresi | `setrlimit(RLIMIT_CPU)` | üstel CPU bütçesi | SIGXCPU → sonra SIGKILL |
| Bellek (adres uzayı) | `setrlimit(RLIMIT_AS)` | malloc başarısızlığı | `malloc` NULL → PHP OOM |
| Bellek (veri) | `setrlimit(RLIMIT_DATA)` | brk/sbrk alanı | tahsis hatası |
| Açık dosya | `setrlimit(RLIMIT_NOFILE)` | tüp/soket üst sınırı | EMFILE |
| Çekirdek dosyası | `setrlimit(RLIMIT_CORE)` | çöküşte çekirdek üretimi | sessizce 0'a düşer |
| Süreç sayısı | `setrlimit(RLIMIT_NPROC)` | fork üst sınırı | EAGAIN |

Okuma aracı: `prlimit(2)` bir sürecin limitlerini okur/ayarlar; POSIX karşılığı
`setrlimit`/`getrlimit` (`functions/setrlimit.html`, §8). Repo'da `prlimit`/`setrlimit`
çağıran kod **YOK** — `⚠️ VERIFICATION REQUIRED`.

### §3.2 PHP yumuşak limitleri (uygulama katmanı karşılığı)

| PHP limiti | INI | POSIX sert karşılığı | Repo kanıtı |
|---|---|---|---|
| Bellek | `memory_limit` | `RLIMIT_AS` | `MemoryAdapter.php` ölçüm yüzeyi (§6) |
| İstek süresi | `max_execution_time` | `RLIMIT_CPU` (yaklaşık) | `RuntimeBootstrap` boot akışı |
| Alt süreç süresi | yok — `CHILD_TIMEOUT` sabiti | `RLIMIT_CPU` | `AIOrchestrator.php` L20 |
| Tüpletme süresi | `default_socket_timeout` | yok | repo'da kullanımı **YOK** — `⚠️ VERIFICATION REQUIRED` |
| Asgari | yok | — | PHP'de sert limit yoktur; kural `composer.json` |

`ini_set` elçisi (§8 php.net): çalışma anında INI değeri değiştirilebilir; ancak
bazı ayarlar PHP_INI_SYSTEM sınıfındadır ve betikten değiştirilemez — limit
kararları boot anında (`RuntimeBootstrap`) verilir, istek ortasında değil.

### §3.3 composer.json sert limit kanıtı

| Dosya | Satır | Metin | Anlam |
|---|---|---|---|
| `composer.json` | L9 | `"ext-apcu": "*"` | zorunlu uzantı — eksikse kurulum kırılır |

Bu satır PHP'nin "eklenti bağımlılığını composer ile zorunlu kılma" kalıbını
gösterir: POSIX'te `RLIMIT` nezdindeki "sert sınır" PHP'de **bağımlılık kontrolü**
olarak yaşar.

### §3.4 Süreç başına limit uygulama sırası

| Adım | Eylem | Katman | Durum |
|---|---|---|---|
| 1 | `memory_limit` / `max_execution_time` INI teyidi | boot | repo'da boot `RuntimeBootstrap.php` L9 (§6) |
| 2 | `setrlimit` uygulanacaksa fork öncesi | süreç başlatma | repo'da **YOK** — `⚠️ VERIFICATION REQUIRED` |
| 3 | `CHILD_TIMEOUT` süresi dolarsa child sonlandırılır | uygulama | `AIOrchestrator.php` L20 |
| 4 | Tüp kapanışı / `proc_close` | test yüzeyi | `ApiKeyCreateCommandTest.php` L253 (§6) |
| 5 | Limit aşım logu | observability | `-observability` dalı — burada yalnız adı |

### §3.5 Limit — havuz etkileşimi

| Limit | Havuz etkisi | Sonuç |
|---|---|---|
| `RLIMIT_NPROC` | fork havuzu büyüklüğünü sınırlar | havuz boyutu ≤ NPROC − çalışan |
| `RLIMIT_NOFILE` | her child bir tüp/soket açar | havuz boyutu ≤ NOFILE/çocuk başına fd |
| `RLIMIT_AS` | child başına bellek | havuz = toplam bellek / child başı |
| `RLIMIT_CPU` | child başına CPU | uzun işlerde havuz değil, kuyruk gerekir |

Kural: havuz boyutu **dört limitin en küçüğü** ile belirlenir; hangi limitin
daraltıcı olduğu donanıma bağlıdır ve `[UNKNOWN]` olarak ölçülür (§7).

## §4 — Fork Guard: Aile (Atfork) Protokolü

### §4.1 Guard kataloğu

| Guard | Ne yapar | PHP karşılığı |
|---|---|---|
| `pthread_atfork()` prepare | fork'tan önce kilidi türetir | yok (PHP parçacığı kilidi zaten yönetmez) |
| `pthread_atfork()` parent | ebeveyn kilidi geri koyar | `fork` sonrası `pcntl_signal` yeniden kurulumu |
| `pthread_atfork()` child | çocuk tekilleşir | `childPid = pcntl_fork()` sonrası tek süreç gerçeği |
| Sinyal maskesi mirası | çocuk maskeli doğar | `pcntl_signal` fork sonrası yeniden kurulur |

PHP'de tek doğrulanabilir guard fork sonrası **sinyal ve tekil PID** gerçeğidir.
`pthread_atfork` POSIX'te üç handler kaydı tanımlar (§8); PHP elçisi yoktur —
bu yüzey yalnızca C eklentisiyle açılır ve repo'da böyle bir eklenti **YOK**
(`composer.json` envanteri).

### §4.2 Fork sonrası zorunlu adımlar (PHP)

| # | Adım | Neden | Repo kanıtı |
|---|---|---|---|
| W-1 | `pcntl_fork()` çağrısı | süreç ayrımı | `AIOrchestrator.php` L45 |
| W-2 | `pcntl_signal` yeniden kurulumu | çocukta sinyal maske mirası | repo'da **YOK** — `⚠️ VERIFICATION REQUIRED` |
| W-3 | `posix_getpid()` ile PID teyidi | ebeveyn/çocuk ayrımı | `-ornekler.md` §2 |
| W-4 | `pcntl_waitpid` ile toplama | zombi önleme | `-ornekler.md` §3 |
| W-5 | `pcntl_wexitstatus` ile exit kodu | başarısızlık tespiti | `-ornekler.md` §3 |
| W-6 | Çocukta sinyal yiyicide async-signal-safe çağrı | güvenli kapanış | `-anayasa.md` §3.7 W-3 |

### §4.3 Tekil PID gerçeği (elçi sözleşmesi)

| Elçi | Sözleşmesi | Sonuç |
|---|---|---|
| `posix_getpid` | sonuç = PHP betiğinin işlem kimliği | ebeveyn için sabit |
| `pcntl_fork` | çocuk **farklı** PID alır | iki çağrı asla aynı değeri vermez |
| `pcntl_waitpid` | ebeveyn çocuğu PID ile bekler | PID takibi olmadan child görünmez |

### §4.4 Guard karar tablosu

| Durum | Eylem | Gerekçe |
|---|---|---|
| Fork, kilitli bölüm içinde | **yasak** | çocukta kilit miras kalır → kilitlenme (S-1) |
| Fork sonrası sinyal kurulmadı | child sinyal görmez | `-anayasa.md` §3.7 W-2 |
| Fork, timeout ile | `CHILD_TIMEOUT` uygulanır | `AIOrchestrator.php` L20 |
| Çoklu fork (havuz) | her child ayrı PID + ayrı timeout | §5.2 |
| Repo geneli | tek fork kalıbı | `AIOrchestrator.php` L45 — havuz **YOK** |

### §4.5 Aile (fork ailesi) durum tablosu

| Durum | Ebeveyn | Çocuk |
|---|---|---|
| fork anı | `childPid > 0` | `childPid = 0` |
| fork başarısız | `-1` döner, errno korunur | — |
| Child exit | `pcntl_wexitstatus` ile kod | `exit` ile sonlanır |
| Ebeveyn exit önce | child orphan (PID 1'e bağlanır) | devam eder — zaman aşımı koruması gerekli |
| Child exit önce | zombi → `waitpid` şart | kaynak sızıntısı |

## §5 — Kuyruk ve Havuz Seçimi

### §5.1 Desen kataloğu

| Desen | Ne zaman seçilir | Repo karşılığı |
|---|---|---|
| Sıcak fork | istek başına iş | `AIOrchestrator` fork (L45) |
| Eşzamanlı denetim | eşzamanlı bağımlılık sayısı > 1 | `ServiceHealth` — "Execute parallel health checks" (L24) |
| Seri denetim | eşzamanlılık yasağı | `ServiceHealth::checkAll()` (L26) |
| Uzak kuyruk | iş kapanış (boot) sonrası | repo'da **YOK** — `⚠️ VERIFICATION REQUIRED` |
| Parçacık havuzu | yüksek dönüşüm, düşük gecikme | repo'da **YOK** — composer'da havuz kütüphanesi yok |

### §5.2 Havuz boyutu formülü (dört limit — §3.5)

| Sınır | Formül | Ölçüm durumu |
|---|---|---|
| Süreç sayısı | `RLIMIT_NPROC − çalışan` | `[UNKNOWN]` — hedef sunucu bilinmiyor |
| Dosya tanıtıcı | `RLIMIT_NOFILE / fd-çocuk` | `[UNKNOWN]` — fd başı 2 (tüp) varsayımı |
| Bellek | `bellek bütçesi / child başı` | `[UNKNOWN]` — bütçe kararı üst otorite |
| CPU | `çekirdek × (1 + kuyruk derinliği)` | `[UNKNOWN]` — çekirdek sayısı bilinmiyor |

Formül yazılır, sayı uydurulmaz: dört sınırın hangisinin daraltıcı olduğu ancak
ölçümle (`§7`) belli olur.

### §5.3 Karar sırası (§2.7 anayasa matrisinin yürütme hâli)

| # | Soru | Evet → | Hayır → |
|---|---|---|---|
| 1 | İş 10 ms altında mı? | fork yok | fork değerlendirilir |
| 2 | Kilit paylaşılan mı? | fork yok (parçacık) | fork uygun |
| 3 | Sonuç tek sayı/JSON mı? | `proc_open` tüpü yeter | dosya yok — bilgi işlemesiz |
| 4 | Süre sınırı gerekli mi? | `CHILD_TIMEOUT` benzeri | timeout'suz bırakılmaz |
| 5 | Havuz maliyeti iş maliyetini aşıyor mu? | tek fork | havuz değerlendirilir |
| 6 | Repo'da desen var mı? | desen tekrarlanır | desen **kurulmaz** — P-1 kararı |

### §5.4 Seçim sonrası izleme

| Gözlem | Eşik | Eylem |
|---|---|---|
| Fork başarısızlık sayısı | ilk başarısızlık | NPROC sınırına yaklaşıldığı varsayılır — `[UNKNOWN]` |
| Child timeout sayısı | `CHILD_TIMEOUT` tetiklenme | kuyruk kuyruğa alınır (`-observability`) |
| Tüp kapanış süresi | `proc_close` süresi | fd sızıntısı araştırılır |
| Zombi sayısı | `waitpid` yapılmayan child | W-4 kuralı ihlali — kritik |

### §5.5 Desen ayrıntıları (seçim sonrası davranış)

| Desen | Yaşam döngüsü | Kaynak teslimi | Risk |
|---|---|---|---|
| Sıcak fork | istek → fork → iş → exit → waitpid | child kendi belleği | zombi (E-5), fd sızıntısı (E-6) |
| Eşzamanlı denetim | çağrı → N bağımlılık → toplu sonuç | `ServiceHealth::checkAll()` tek çağrı | eşzamanlılık sınırı belirsiz (L24 iddiası) |
| Seri denetim | çağrı → sırayla N bağımlılık | toplam süre = Σ | yavaş ama izlenebilir |
| Uzak kuyruk | kapanış → kuyruk → worker | worker süreci | repo'da **YOK** — §5.1 |
| Parçacık havuzu | boot → N parçacık → iş kuyruğu | paylaşılan bellek + kilit | kilit yarışı — anayasa §5 |
| Ardışık fork zinciri | fork → child fork → ... | derin süreç ağacı | NPROC tükenmesi (§3.5) |
| Paylaşımlı bellek (shm) | fork sonrası `mmap` paylaşımı | iki süreç aynı sayfa | senkronizasyon — repo'da **YOK** |

> Kural: desen, repo kanıtı olmayan bir kopya üzerinden **kurulmaz**; önce P-1 kararı
> (anayasa §8.1), sonra ölçüm (§7.3), sonra desen seçimi (§5.3) gelir.

| Desen | Repo kanıtı | Durum |
|---|---|---|
| Sıcak fork | `AIOrchestrator.php` L45 | teyitli |
| Eşzamanlı denetim | `ServiceHealth.php` L24 | teyitli (iddia düzeyinde) |
| Seri denetim | `ServiceHealth.php` L26 | teyitli |
| Uzak kuyruk | — | `⚠️ VERIFICATION REQUIRED` |
| Parçacık havuzu | — | `⚠️ VERIFICATION REQUIRED` |

## §6 — Repo Kanıtları (Sürücü Yüzeyi)

### §6.1 Doğrulanmış satır alıntıları

| Dosya | Satır | Metin | Sürücü bağlantısı |
|---|---|---|---|
| `app/Modules/AI/AIOrchestrator.php` | L20 | `const CHILD_TIMEOUT = 15;` | §3.4 — süre limiti |
| `app/Modules/AI/AIOrchestrator.php` | L45 | `$childPid = pcntl_fork();` | §4.2 W-1 — fork guard |
| `shared/src/Api/Registry/ServiceHealth.php` | L24 | `* Execute parallel health checks.` | §5.1 — eşzamanlı denetim |
| `shared/src/Api/Registry/ServiceHealth.php` | L26 | `public function checkAll(): array` | §5.1 — seri denetim |
| `shared/src/Api/Registry/ServiceDefinition.php` | L23 | `private readonly int $timeout = 5,` | §3.2 — servis başına süre |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | L39 | `private array $bucket;` | §3 — uygulama kotası |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | L40 | `private float $window;` | §3 — pencere bütçesi |
| `shared/src/Exception/RateLimitException.php` | L7 | `private int $retryAfter;` | §6.2 — aşım sonrası bekleme |
| `shared/src/Middleware/MiddlewarePipeline.php` | L42 | `$start = hrtime(true);` | §7 — monotonik ölçüm |
| `shared/src/Cache/ApcuAdapter.php` | L22 | APCu `apcu_store` sarmalayıcısı | §7 — bellek ölçüm yüzeyi |
| `shared/src/Cache/MemoryAdapter.php` | L16 | PHP-dahili dizi önbelleği | §7 — bellek ölçüm yüzeyi |
| `shared/tests/Repository/ApiKeyCreateCommandTest.php` | L253 | `$handle = proc_open(` | §5.1 — alt süreç tek kanıt |
| `composer.json` | L9 | `"ext-apcu": "*"` | §3.3 — sert bağımlılık |

Doğrulama yöntemi: her satır `Select-Object -Index (N-1)` ile dosyadan okunup
birebir teyit edilmiştir (2026-10-13).

### §6.2 Aşım sonrası davranış (repo)

| Dosya | Satır | Davranış |
|---|---|---|
| `shared/src/Exception/RateLimitException.php` | L7 | `retryAfter` alanı istemciye bekleme süresini bildirir |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | L39–L40 | kova (bucket) + pencere (window) ile sınır |
| `shared/src/Api/Registry/ServiceDefinition.php` | L23 | `timeout = 5` — servis başına yumuşak sınır |

Yorum: repo limitleri **uygulama katmanında** kurar; POSIX `RLIMIT` yüzeyi
kullanılmaz. İki katman birbirinin yerine geçmez — POSIX sert, uygulama yumuşaktır.

### §6.3 Negatif kanıt (repo — sürücü yüzeyi)

| İddia | Kanıt durumu |
|---|---|
| `sched_setaffinity` çağıran PHP kodu | **YOK** |
| `pthread_*` çağıran PHP kodu | **YOK** (uzantı bağımlılığı composer'da listelenmiyor) |
| `setrlimit`/`prlimit` çağıran kod | **YOK** — `⚠️ VERIFICATION REQUIRED` |
| `nice`/`setpriority` çağıran kod | **YOK** — `⚠️ VERIFICATION REQUIRED` |
| `pcntl_signal` çağıran kod | **YOK** — fork sonrası sinyal kurulumu eksik (W-2) |
| Parçacık havuzu kütüphanesi | **YOK** — composer envanterinde yok |
| Uygulama limiti (yumuşak) | `memory_limit`, `max_execution_time`, `RateLimitMiddleware` mevcut |

### §6.4 Boot ve yönlendirme kanıtları (sürücü-öncesi akış)

| Dosya | Satır | Metin/rol | Sürücü bağlantısı |
|---|---|---|---|
| `shared/src/Bootstrap/RuntimeBootstrap.php` | L7–L20 | boot yapılandırma bloğu | §3.4 adım 1 — INI teyidi boot anında |
| `home.coremusic.net/config/bootstrap.php` | L13 | autoloader kurulumu | süreç fork'tan önce tamamlanır |
| `home.coremusic.net/config/bootstrap.php` | L97 | istek döngüsüne giriş | fork kararı istek düzeyindedir |
| `home.coremusic.net/index.php` | L10 | boot çağrısı | boot → istek zinciri |
| `home.coremusic.net/index.php` | L15 | router çağrısı | istek başına iş = fork adayı |
| `api/index.php` | L143–L147 | API giriş bloğu | uzak istemci işi — fork sınırı |
| `api/index.php` | L169–L171 | yanıt kapanışı | child/parent ayrımı sonrası |
| `config/domain.php` | L4–L18 | alan tanım listesi | boot anında tek sefer |

Yorum: boot zinciri (§6.4) fork'tan **önce** tamamlanır; fork anında konteyner/
autoloader hazır olmalıdır — `pthread_atfork` prepare karşılığının PHP'deki
dolaylı hâli budur (§4.1).

### §6.5 Limitleme — repo yüzeyinin tam karşılaştırması

| Yüzey | POSIX (sert) | Repo (yumuşak) | Fark |
|---|---|---|---|
| Bellek | `RLIMIT_AS` | `memory_limit` INI | POSIX aşımı öldürür, PHP istisna verir |
| Süre | `RLIMIT_CPU` | `max_execution_time` | PHP sinyal değil istisna |
| Child süre | `RLIMIT_CPU` (çocuk) | `CHILD_TIMEOUT = 15` sabiti | uygulama katmanı |
| Servis süre | yok | `timeout = 5` (`ServiceDefinition.php` L23) | uygulama katmanı |
| İstek oranı | yok | `RateLimitMiddleware` kova/pencere (L39–L40) | uygulama katmanı |
| fd | `RLIMIT_NOFILE` | yok | **[UNKNOWN]** — repo'da fd limiti YOK |

### §6.6 Kanıt bütünlüğü kuralları

| Kural | Uygulama |
|---|---|
| Satır alıntısı | yalnız `Select-Object -Index` ile okunup teyit edilen metin yazılır |
| Dosya yolu | repo köküne göreli; yoksa `[UNKNOWN]` |
| Yokluk iddiası | "YOK" yazan satır, repo genelinde arama ile doğrulanır |
| Uydurma yasağı | satır numarası tahmin edilmez; tahmin = ihlal |

## §7 — Hata Modları ve Test

### §7.1 Hata modları

| # | Hata modu | Belirti | Kaynak | Önlem |
|---|---|---|---|---|
| E-1 | Fork sonrası sinyal maske mirası | çocukta sinyal gelmez | §4.2 W-2 | `pcntl_signal` yeniden kurulumu |
| E-2 | PID takibi kaybı | `waitpid` child göremez | §4.3 | tekil fork + exit kodu okuma |
| E-3 | Havuz kilidi | thread deadlock | anayasa §5 | parçacıkta paylaşılan kilit yasak |
| E-4 | Limit aşımı | OOM kill | §3.1 | `memory_limit` + `RLIMIT_AS` (varsa) |
| E-5 | Zombi birikimi | süreç tablosu şişer | §4.5 | her fork için `waitpid` |
| E-6 | fd sızıntısı | `NOFILE` tükenir | §5.2 | `proc_close` zorunluluğu |
| E-7 | Kilitli fork | çocukta kilit mirası | §4.4 | kilitli bölümde fork yasağı |

### §7.2 Test stratejisi (sıra)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| T-1 | fork → waitpid | ebeveyn çocuğu bekler | exit kodu okunur |
| T-2 | PID ayrımı | `posix_getpid` ebeveyn/çocuk | iki değer farklı |
| T-3 | exit kodu | `pcntl_wexitstatus` | beklenen kod döner |
| T-4 | Sinyal maskesi | fork sonrası sinyal gönderimi | çocuk sinyali görür (W-2 sağlanmışsa) |
| T-5 | Bellek limiti | `memory_limit` altında dizi doldurma | PHP sınırında durur, çökmez |
| T-6 | Timeout | `CHILD_TIMEOUT` altında bekleyen child | child zaman aşımında sonlanır |
| T-7 | fd sayımı | ardışık fork + `proc_close` | fd sayısı sabit kalır |

### §7.3 Ölçüm (anayasa §4.4 kalıplarının sürücü karşılığı)

| Ölçüm | Kalıp | Dosya |
|---|---|---|
| Monotonik süre | `hrtime(true)` | `MiddlewarePipeline.php` L42 |
| Fork süresi | ölçüm **YOK** — kalıp yazılır, sayı yok | `[UNKNOWN]` — anayasa §4.5 E-5 |
| Child timeout süresi | `CHILD_TIMEOUT = 15` sabit | `AIOrchestrator.php` L20 |
| Havuz kullanımı | havuz **YOK** — ölçüm yok | `⚠️ VERIFICATION REQUIRED` |

### §7.4 Test riskleri

| Risk | Belirti | Önlem |
|---|---|---|
| Fork testi CI'da yasak | `pcntl` extension yoksa test kırılır | `extension_loaded('pcntl')` kontrolü (§8 php.net) |
| Zamanlayıcı duyarlılığı | timeout testleri flaky | generous eşik + retry yasak |
| Zombie | CI worker süreç biriktirir | `try/finally` içinde `waitpid` |
| Bellek testi OOM | CI makineyi çökertir | küçük `memory_limit` ile sınırlı dizi |

### §7.5 Kabul kriterleri (sürücü kapanışı)

| # | Kriter | Ölçüt | Durum |
|---|---|---|---|
| K-1 | her fork bir `waitpid` ile eşleşir | T-1 geçerse | repo'da tek fork — teyitli |
| K-2 | fork sonrası sinyal kurulumu | T-4 geçerse | repo'da **YOK** (W-2) — açık |
| K-3 | limit katmanı ayrımı yazılı | §6.5 tablosu | tamamlandı |
| K-4 | havuz kararı verilmeden havuz kurulmaz | §5.3 kural 6 | kural olarak yazıldı |
| K-5 | `[UNKNOWN]` eşikler üst otoriteye listelendi | §5.2 | listelendi |

### §7.6 Sürücü → sonraki dal teslimleri

| Teslim | Hedef | İçerik |
|---|---|---|
| Havuz formülü (§5.2) | `kaynak-dagilimi/` | dört limit sınırı |
| Ölçüm kalıpları (§7.3) | `isletim-sistemi-observability` | `hrtime` kalıbı, timeout sayacı |
| Fork guard kuralı (§4.4) | `isletim-sistemi-runtime` | `pcntl_signal` eksikliği |
| Hata modları (§7.1) | `isletim-sistemi-testing` | T-1…T-7 senaryoları |

### §7.7 Açık yüzeyler (dürüstlük defteri — bu dosya)

| # | Açık yüzey | Durum | Kapanış koşulu |
|---|---|---|---|
| O-1 | `pcntl_signal` repo'da yok (W-2) | `⚠️ VERIFICATION REQUIRED` | fork sinyal kararı üst otorite |
| O-2 | fd limiti repo'da yok (§6.5) | `[UNKNOWN]` | hedef sunucu limitlerinin ölçümü |
| O-3 | havuz kütüphanesi yok (§5.1) | `[UNKNOWN]` | P-1 kararı (anayasa §8.1) |
| O-4 | `RLIMIT` çağıran kod yok (§3.1) | `⚠️ VERIFICATION REQUIRED` | süreç yönetimi kararının yazılması |
| O-5 | affinity/cpuset kullanımı yok (§2.2) | `⚠️ VERIFICATION REQUIRED` | çoklu sunucu/topoloji kararı (`web-runtime-topolojisi`) |

Açık yüzeyler kapatılmaz: hiçbiri disk kanıtıyla cevaplanamaz; cevap vermek
uydurma olur. Kapatma kararı orkestratöre aittir (anayasa §8.4 F4).

## §8 — Kaynakça (Web Research)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `sched_setparam`, öncelik parametresini ayarlar; planlama sınıfı seçimi `sched_setscheduler` işidir (§1.1) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_setparam.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi POSIX/IEEE 1003.1) |
| POSIX `sched_getparam`, mevcut öncelik parametresini okur (§1.1) | https://man7.org/linux/man-pages/man2/sched_getparam.2.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| Linux `getpriority(2)`, tekil çağrının belirsizliğini `errno` ile çözer (§1.1) | https://man7.org/linux/man-pages/man2/getpriority.2.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| Linux `cpuset(7)`, kapsayıcı cpuset modelini ve daraltma-semantiğini tanımlar (§2.1) | https://man7.org/linux/man-pages/man7/cpuset.7.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |
| Linux `prlimit(2)`, sürecin limitlerini okur/ayarlar (§3.1) | https://man7.org/linux/man-pages/man2/prlimit.2.html | 2026-10-13 | Doğrulandı (HTTP 200, resmi Linux man sayfası) |

> Not: envanter kesişim denetimi — yukarıdaki 5 URL `url-inventory-2026-10-13.txt`
> (2633 kayıt) içinde **yoktur**; kesişim = 0.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
