---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler/zamanlama-scheduler-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/zamanlama-scheduler
children: []
depends_on:
  - zamanlama-scheduler-anayasa
  - zamanlama-scheduler-surucu
  - is-parcacigi-planlamasi
evidence:
  - "shared/src/AI/AIOrchestrator.php:71"
  - "shared/src/Middleware/MiddlewarePipeline.php:42"
  - "shared/src/Api/Registry/ServiceDefinition.php:23"
  - "shared/src/Api/Middleware/RateLimitMiddleware.php:39"
  - "https://docs.kernel.org/scheduler/sched-design-CFS.html"
  - "https://docs.kernel.org/scheduler/sched-deadline.html"
validation:
  - "2026-10-13: OS zamanlama API'si grep → 0 (§5 D-1) · repo satırları Read ile teyitli"
  - "2026-10-13: 5 yeni URL HTTP 200 · sayfa içeriği okunarak alıntılandı"
open_questions:
  - "Plan1 — scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
  - "Q-zs-1 — kardeş dosyalardaki iş parçası iddiaları repo grep'iyle desteklenmiyor [⚠️ VERIFICATION REQUIRED]"
group: kod
---

# zamanlama-scheduler — Örnekler (Kod Kanıtı)

> **Bu dosyanın dürüstlüğü tek cümlesi:** Bu depoda **işletim sistemi zamanlayıcısına
> dokunan tek bir satır kod yoktur** (`pcntl_` · `pthread_` · `sched_` · `affinity`
> → 0 eşleşme). Buradaki her örnek **PHP uygulama katmanı** kuyruk/öncelik/mikro-süre
> örneğidir ve OS zamanlamasının **yerine geçmez**.

## §0 ELI15

| Madde | Değer |
|---|---|
| Okuma sırası | `-anayasa.md` (kural) → `-surucu.md` (mekanizma) → bu dosya (kanıt) |
| Kapsam | öncelik haritası · atama · süre ölçümü · politika karşılaştırması · dürüstlük defteri |
| Kapsam dışı | OS zamanlayıcı kodu — repo'da **yok** (§5 D-1) |
| Katman uyarısı | her örnek uygulama katmanı — `isletim-sistemi-api` sınırındadır |
| Kanıt dili | Türkçe yorum + İngilizce teknik terim; alıntı özgün |

### §0.1 Dosyalar arası okuma haritası

| Bu dosya | Kardeş | Bağ |
|---|---|---|
| §1 | `-anayasa §3` | politika ailesi özeti |
| §2 | `-surucu §6` | ölçüm (context switch · jitter) |
| §4 | `-surucu §1` · `§4` | RR · CFS · priority · EDF |
| §5 | `-anayasa §7` | açık sorular |

### §0.2 Soru → bölüm indeksi

| Soru | Bölüm |
|---|---|
| Repo'da zamanlama benzeri ne var? | §1 |
| Ne ölçülüyor? | §2 |
| Ne yanlış? | §3 |
| Politikalar nasıl ayrılır? | §4 |
| Repo'da **yok** olan ne? | §5 |
| OS'ler kendi aralarında nasıl örneklenir? | §7 |

## §1 Gerçek repo kodu (uygulama katmanı — satır alıntıları)

### §1.1 Görev önceliği haritası — `AIOrchestrator.php`

| Satır | Alıntı (özgün) |
|---|---|
| 20 | `* - TaskQueue (priority queue)` (docblock) |
| 41 | `'recommendation' => ['agent' => 'backend', 'priority' => 'medium'],` |
| 45 | `'fault' => ['agent' => 'dsp-fw', 'priority' => 'critical'],` |
| 46 | `'security-audit' => ['agent' => 'security', 'priority' => 'critical'],` |
| 71 | `public function dispatchTask(string $type, array $params, string $priority = 'medium'): string` |
| 84 | `'priority' => $priority,` |

| Görev | `priority` | Sıra |
|---|---|---|
| `fault` · `security-audit` | `critical` | 1 |
| `analysis` · `hardware` | `high` | 2 |
| `recommendation` · `eq-optimization` · `testing` | `medium` | 3 |
| `documentation` | `low` | 4 |

> **Katman uyarısı:** Bu bir **uygulama öncelik alanıdır**; çekirdeğe `nice`/`SCHED_*`
> olarak **yansımaz** (§5 D-1). Öncelik **haritası** vardır; öncelikli kuyruk **yoktur** (§5 D-2).

### §1.2 Atama (dispatch) — ne yapılıyor, ne yapılmıyor

| # | Satır davranış | Var mı? | Not |
|---|---|---|---|
| 1 | Öncelik **alanı** alınıyor | ✅ L71 · L84 | varsayılan `medium` |
| 2 | Önceliğe göre **sıralama** | ❌ | kuyruk sınıfı yok (§5 D-2) |
| 3 | Öncelik **dönüşümü** (uygulama → OS) | ❌ | `nice` çağrısı yok |
| 4 | Görev **zaman aşımı** | ⚠️ | bu dosyada okunmadı — `⚠️ VERIFICATION REQUIRED` |
| 5 | Kuyrukta bekleme **süresi ölçümü** | ❌ | yalnız middleware süresi (§2.1) |

### §1.3 Süre ölçümü — `MiddlewarePipeline.php`

| Satır | Alıntı |
|---|---|
| 42 | `$start = hrtime(true);` |
| 44 | `$response = $middleware->handle($request, $next);` |
| 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| 48 | `$this->logger->timing('MiddlewarePipeline', $name, $durationMs);` |

| Soru | Yanıt |
|---|---|
| Ne ölçülür? | tek middleware'in **uçtan uca** süresi (ms) |
| Yüksek çözünürlük mü? | `hrtime(true)` — **monotonik**, nanosaniye |
| OS bağlam değiştirme ölçer mi? | ❌ hayır — uygulama katmanı ölçümü |

### §1.4 Kabul/bekletme — `RateLimitMiddleware.php` (atama kapısı)

| Satır | Alıntı | Zamanlama açısından |
|---|---|---|
| 39 | `if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {` | **kabul kontrolü** |
| 40 | `header('Retry-After: ' . $this->windowSeconds);` | istemciye **bekleme** bildirimi |
| 41–45 | `ApiResponse::error('RATE_LIMIT_EXCEEDED', …, 429)` | **reddetme** (kuyruğa almama) |
| 49 | `$this->rateLimiter->increment($key, $this->windowSeconds);` | sayaç |

> **Kavram ayrımı:** Kuyruğa **alma** (queueing) ile **reddetme** (shedding) aynı şey
> değildir; bu repo **reddetmeyi** seçer. Kuyruğa alma `isletim-sistemi-api`/`kernel` işidir.

### §1.5 Bekleme bütçesi — `ServiceDefinition.php` / `ServiceRegistry.php`

| Dosya | Satır | Alıntı | Ne sınırlandırır |
|---|---|---|---|
| `ServiceDefinition.php` | 23 | `private readonly int $timeout = 5,` | tek sağlık isteği bekleme (sn) |
| `ServiceRegistry.php` | 104 | `'timeout' => $service->getTimeout(),` | gerçek uygulama |
| `ServiceRegistry.php` | 56 | `foreach ($this->services as $name => $service) {` | **seri** yürütme |
| `ServiceRegistry.php` | 109 | `$response = @file_get_contents($url, false, $context);` | bloklayan çağrı |

> En kötü durum toplamı = `N × 5 sn` — **formül kanıtlıdır, N okunmadığı için sayı `UNKNOWN`**.

### §1.6 CI iş bağımlılık grafiği — **CI katmanı, OS zamanlaması DEĞİL**

> **Katman etiketi (bağlayıcı):** Bu alt bölüm **GitHub Actions iş sıralamasıdır** —
> çekirdek zamanlayıcısı değildir. Analoji kurulabilir, **eşitlenemez** (bkz. §5.8 matrisi).
> Kanıt: `.github/workflows/ci.yml` (203 satır) · 2026-10-13 Read.

| # | Yapı | Satır | Değer (özgün) | OS katmanı mı? |
|---|---|---|---|---|
| 1 | iş adı | 8 | `name: CI Pipeline` | ❌ |
| 2 | tetikleyici | 10–12 | `on` → `push` · `pull_request` | ❌ |
| 3 | izinler | 14–15 | `permissions:` → `contents: read` | ❌ |
| 4 | eşzamanlılık | 17–19 | `concurrency:` → `cancel-in-progress: true` | ❌ |
| 5 | ortam | 21–22 | `env:` → `PHP_VERSION: '8.4'` | ❌ |
| 6 | bağımlılık | 68 | `needs: php-lint` (`php-test`) | ❌ |
| 7 | bağımlılık | 162 | `needs: js-test` (`js-coverage`) | ❌ |

**İş grafiği (satır başlıkları 25 · 66 · 110 · 142 · 160 · 178):**

| # | İş | `needs` | Bağımlılık | Yumuşak job? |
|---|---|---|---|---|
| 1 | `php-lint` | — | paralel kök | hayır |
| 2 | `php-test` | `php-lint` (L68) | sıralı | hayır |
| 3 | `composer-audit` | — | paralel kök | hayır |
| 4 | `js-test` | — | paralel kök | hayır |
| 5 | `js-coverage` | `js-test` (L162) | sıralı | **evet** — `continue-on-error: true` (L167) |
| 6 | `js-e2e` | **yok** (L178–183 okundu — `needs` satırı **bulunmadı**) | paralel kök | **evet** — `continue-on-error: true` (L183) |

| Gözlem | OS zamanlamasındaki karşılığı | **Eşit mi?** |
|---|---|---|
| 4 iş paralel, 2 iş sıralı | çalıştırılabilir kuyrukta sıra | ❌ hayır — bunlar **bağımlılık** (DAG), **sıra** değil |
| `cancel-in-progress: true` | eski işi düşürmek (shedding) | ❌ hayır — CI koşusunun **iptali**, süreç öldürme değil |
| `continue-on-error: true` (×2) | "hata olsa da devam" | ❌ hayır — iş sonucu **raporlanır, bloklanmaz** |
| `timeout-minutes` (15·15·10·10·15·20) | süreç zaman aşımı | ⚠️ en yakın analoji — **üst sınır** (85 dk) |

> **Dürüstlük satırı:** `js-e2e` için `needs` **okunmadı/`yok`** — bu bir yokluk kanıtıdır
> (satır 178–183 arası `Read`); "paralel olduğu **tahmin** edilmez, satırda `needs`
> bulunmaz" denir. Ayrıca `.github/AGENTS.md` §7 "3 job" der; bu dosyanın ölçümü
> `ci.yml` + `secret-scan.yml` = **7 iş** (203 + 31 satır) → ⚠️ iki sayım farklı katmanları
> sayıyor olabilir (CI planı vs disk) — **kapatılmadı**, işaretli kaldı.

## §2 Ölçüm örnekleri

### §2.1 Uygulama katmanı ölçümleri (repo'da var)

| # | Ölçüm | Birim | Dosya:Satır | Alet |
|---|---|---|---|---|
| 1 | Middleware süresi | ms | `MiddlewarePipeline.php:42·46` | `hrtime(true)` |
| 2 | Middleware adı → süre logu | ms | `MiddlewarePipeline.php:48` | `logger->timing` |
| 3 | Sağlık isteği bekleme bütçesi | sn | `ServiceRegistry.php:104` | stream context `timeout` |
| 4 | CI iş bütçesi | dk | `ci.yml:28·70·113·145·164·181` | `timeout-minutes` |
| 5 | Rate-limit penceresi | sn | `RateLimitMiddleware.php:23` | sabit `60` |

### §2.2 Ölçüm → katman eşlemesi

| # | İstenen ölçüm (anayasa/sürücü) | Repo karşılığı | Durum |
|---|---|---|---|
| 1 | context switch latency | — | ❌ yok |
| 2 | jitter | — | ❌ yok |
| 3 | run queue length | — | ❌ yok |
| 4 | scheduler istatistikleri (`sched-stats`) | — | ❌ yok |
| 5 | uygulama süresi | §2.1 #1–#2 | ✅ var |
| 6 | CI bütçesi | §2.1 #4 | ✅ var |

### §2.3 Ölçüm yapısı — `hrtime` okuması

| # | Özellik | Değer | Neden önemli |
|---|---|---|---|
| 1 | Kaynak | `hrtime(true)` | `time()`'dan **farklı**: milisaniye/monotonik |
| 2 | Dönüşüm | `/ 1_000_000` | ns → ms |
| 3 | Kapsam | tek middleware sarmalayıcısı | zincirin **bir halkası** |
| 4 | Eksik | zincir toplamı, kuyrukta bekleme | ölçülmemiş |

### §2.4 Ölçüm protokolü (PLANNED — sayı yok, tarif var)

| # | Adım | Ne ölçülür | Durum |
|---|---|---|---|
| 1 | Boş sistemde taban çizgisi | middleware ms | PLANNED |
| 2 | Yük altında tepe (p95/p99) | aynı | PLANNED |
| 3 | Eşik yazımı | üst otorite onayı | ⚠️ VERIFICATION REQUIRED |
| 4 | Çekirdek sayacı okuma | `sched-stats` | ❌ repo'da yok (§5 D-4) |

### §2.5 Ölçümün resmi adları — `getrusage(2)` (repo'da yok · kıyas yüzeyi)

> Kaynak: https://man7.org/linux/man-pages/man2/getrusage.2.html · HTTP 200 · 2026-10-13
> `getrusage()` "returns resource usage measures for `who`" — `RUSAGE_SELF` ·
> `RUSAGE_CHILDREN` · `RUSAGE_THREAD` (Linux'a özgü). Bu bölüm, §2.2'deki ❌ satırların
> **adını** verir; repo'da karşılıkları **yoktur**.

| # | Alan | Tanım (özgün) | §2.2 karşılığı | Repo |
|---|---|---|---|---|
| 1 | `ru_utime` | "total amount of time spent executing in user mode" | #5 (uygulama süresi — **farklı** varlık) | ❌ |
| 2 | `ru_stime` | "total amount of time spent executing in kernel mode" | #5 | ❌ |
| 3 | `ru_nvcsw` | "voluntary context switches" — "voluntarily giving up the processor before its time slice was completed (usually to await availability of a resource)" | **#1 context switch latency** | ❌ |
| 4 | `ru_nivcsw` | "involuntary context switches" — "higher priority process becoming runnable or because the current process exceeded its time slice" | **#1** | ❌ |
| 5 | `ru_maxrss` | "maximum resident set size used (in KiB)" | — (bellek) | ❌ |
| 6 | `ru_inblock` · `ru_oublock` | "number of times the filesystem had to perform input" / "output" | — (IO) | ❌ |

**Bağlayıcı ayrım tablosu:**

| # | Ölçüm | Kaynak | Kim ölçer | Repo |
|---|---|---|---|---|
| 1 | `ru_nvcsw` / `ru_nivcsw` | `getrusage(2)` | **çekirdek**, süreç/parçacık sayacı | ❌ yok (grep → 0) |
| 2 | `hrtime` ms | `MiddlewarePipeline.php:42·46` | PHP uygulaması | ✅ var |
| 3 | `usage_usec` | cgroup `cpu.stat` | çekirdek, **grup** sayacı | ❌ yok (§5 D-4) |

> **Not (kapsam):** "Not all fields are completed; unmaintained fields are set to zero by
> the kernel." — `ru_ixrss` · `ru_idrss` · `ru_isrss` · `ru_nswap` · `ru_msgsnd` ·
> `ru_msgrcv` · `ru_nsignals` Linux'ta "currently unused" sayılır; POSIX.1 yalnız
> `ru_utime` ve `ru_stime`'ı **zorunlu** tutar.

## §3 Kötü uygulama kataloğu

### §3.1 Katalog

| # | Anti-pattern | Kanıt | Etki | Düzeltme |
|---|---|---|---|---|
| 1 | Docblock'ta kuyruk vaadi, kodda kuyruk yok | `AIOrchestrator.php:20` ↔ §5 D-2 | yanıltıcı doküman | yorumu düzelt veya kuyruğu ekle |
| 2 | Önceliğin OS'a **yansıtılmaması** | `priority` alanı yalnız veri (L84) | `critical` == `low` (işlem sırası aynı) | dönüşüm katmanı veya kaldırma |
| 3 | Seri + bloklayan sağlık denetimi | `ServiceRegistry.php:56·109` | N × 5 sn | paralel istek |
| 4 | "parallel" yorumu ile davranışın uyuşmaması | `ServiceHealth.php:24` ↔ `ServiceRegistry.php:56` | yanıltıcı doküman | yorumu düzelt |
| 5 | Dış süreci senkron `shell_exec`, timeout yok | `AIEngine.php:295` | iş kilitlenmesi | timeout + kill |
| 6 | Ölçüm olmadan öncelikli davranış iddiası | §2.2 #1–#4 | kanıtsız iyileştirme | önce ölç |

### §3.2 #1 + #2 — öncelik alanı neden zamanlama değildir?

| # | Soru | Yanıt | Kanıt |
|---|---|---|---|
| 1 | Öncelik değeri saklanıyor mu? | evet | `AIOrchestrator.php:84` |
| 2 | Sıralama yapılıyor mu? | hayır | kuyruk sınıfı yok (grep → 0) |
| 3 | `nice`/`setpriority` çağrılıyor mu? | hayır | §5 D-1 |
| 4 | Zamanlama politikasına çevriliyor mu? | hayır | §5 D-1 |
| 5 | Sonuç | alan **etiket**, zamanlama **değil** | bu satırlar |

### §3.3 #4 — doküman/çelişki (detay)

| Dosya | Satır | İddia |
|---|---|---|
| `ServiceHealth.php` | 24 | `* Execute parallel health checks.` |
| `ServiceHealth.php` | 26 | `public function checkAll(): array` |
| `ServiceRegistry.php` | 52 | `public function healthCheck(): array` |
| `ServiceRegistry.php` | 56–58 | `foreach` içinde tek tek `checkServiceHealth(...)` |

> **Bulgu:** yorum "parallel", davranış **seri**. Doğru olan **davranıştır**; yorum
> düzeltilmelidir. `⚠️ VERIFICATION REQUIRED` değil, **gözlem**tir (satırlar okundu).

### §3.4 #5 — `shell_exec` (detay)

| Satır | Alıntı |
|---|---|
| 290–293 | `sprintf('ffprobe -v quiet -print_format json -show_format -show_streams "%s"', escapeshellarg($filePath))` |
| 295 | `$output = shell_exec($cmd);` |

> `extractMetadata()` içinde timeout/kill **görülmedi** (L288–L300 okundu);
> dosyanın bütünü okunmadığı için `⚠️ VERIFICATION REQUIRED`.

### §3.5 Senaryo → sonuç matrisi

| # | Senaryo | Bugünkü sonuç | İstenen sonuç |
|---|---|---|---|
| 1 | `critical` görev `medium` ile aynı anda gelir | sıralama **yok** | öncelikli sıralama (§5 D-2) |
| 2 | `ffprobe` asılırsa | istek asılı kalabilir | timeout → hata |
| 3 | 8 servis sağlıksızsa | 8 × 5 sn seri | paralel / kısa bütçe |
| 4 | Ölçüm olmadan öncelik eklenirse | kanıtsız | §2.4 sırası |

## §4 Politika karşılaştırma matrisi

### §4.1 Dört aile (resmi kaynak metni — 2026-10-13 okundu)

| # | Politika | Ne yapar (resmi metin) | Kaynak |
|---|---|---|---|
| 1 | **CFS** | "CFS stands for 'Completely Fair Scheduler' … models an 'ideal, precise multi-tasking CPU' … each task at … 1/nr_running speed" | https://docs.kernel.org/scheduler/sched-design-CFS.html |
| 2 | **SCHED_DEADLINE (EDF)** | "an implementation of the Earliest Deadline First (EDF) scheduling algorithm, augmented with … Constant Bandwidth Server (CBS)" | https://docs.kernel.org/scheduler/sched-deadline.html |
| 3 | **sched-ext (BPF)** | "a scheduler class whose behavior can be defined by a set of BPF programs" — hata/duraklama anında varsayılana döner | https://docs.kernel.org/scheduler/sched-ext.html |
| 4 | **Round-robin / priority** | politika ailesi özeti kardeşte; öncelik aralığı `sched_get_priority_max(3p)` ile sorgulanır | kardeş envanter N-W12 (HTTP 200) |

> **Not:** CFS sayfası ayrıca "CFS is making room for EEVDF" der — yani aile **değişiyor**;
> bu dosya sürüm iddiası **yazmaz**.

### §4.2 `SCHED_DEADLINE` parametreleri (resmi metin)

| Parametre | Anlam (alıntı) |
|---|---|
| `runtime` | "should receive 'runtime' microseconds of execution time" |
| `period` | "every 'period' microseconds" |
| `deadline` | teslim anı |

> Uyarı (kaynak metinden): "Fiddling with these settings can result in an unpredictable
> or even unstable system behavior."

### §4.3 Karşılaştırma

| Ölçüt | RR / priority | CFS | EDF (`SCHED_DEADLINE`) | sched-ext (BPF) |
|---|---|---|---|---|
| Dayanak | öncelik sınıfı | adillik modeli | son teslim | kullanıcı BPF programı |
| Girdi | öncelik no | çalıştırılan iş sayısı | runtime/period/deadline | BPF programı |
| Amaç | öngörülebilir sıra | adalet | zamanlama taahhüdü | özelleştirilmiş politika |
| Risk | düşük öncelikte tükenme | — | parametre hatası (uyarı §4.2) | hata → otomatik geri dönüş |
| Repo karşılığı | yok (§5 D-1) | yok | yok | yok |
| Ölçüm kaynağı | — | `sched-stats` | — | — |

### §4.4 Kapasite (heterojen çekirdek)

| Kavram | Resmi tanım (alıntı) | Kaynak |
|---|---|---|
| CPU capacity | "a measure of the performance a CPU can reach, normalized against the most performant CPU in the system" | https://docs.kernel.org/scheduler/sched-capacity.html |
| Heterojen sistem | "asymmetric CPU capacity systems" | aynı |

> Heterojen/topoloji etkisi `zamanlama-scheduler-anayasa §10.4`'te kardeşte işlenir.

### §4.5 Ölçüm kaynağı — `sched-stats`

| Kalem | Bilgi |
|---|---|
| Sayfa | "Scheduler Statistics" — https://docs.kernel.org/scheduler/sched-stats.html (HTTP 200 · 2026-10-13) |
| Sürüm notu | "Version 17 of schedstats removed 'lb_imbalance' field … added … `lb_imbalance_load` … `lb_imbalance_util` … `lb_imbalance_task` … `lb_imbalance_misfit`" |
| Repo durumu | **kullanılmıyor** (§5 D-4) |

### §4.6 `sched_yield()` — POSIX "sıra bırakma" yüzeyi (repo'da yok)

> Kaynak: The Open Group Base Specifications Issue 8 · IEEE Std 1003.1-2024 —
> https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html ·
> HTTP 200 · 2026-10-13

| # | Alıntı (özgün) | Ne kanıtlar |
|---|---|---|
| 1 | "The sched_yield() function shall force the running thread to relinquish the processor until it again becomes the head of its thread list. It takes no arguments." | "sıra bırakma" **yalnız** çalışan parçacığın kendi kararıdır |
| 2 | "The sched_yield() function shall return 0 if it completes successfully; otherwise, it shall return a value of -1 and set errno to indicate the error." | dönüş sözleşmesi |
| 3 | "ERRORS — No errors are defined." | POSIX bu çağrıya **hata tanımlamaz** |
| 4 | "portable applications should not make any assumptions regarding whether threads from other processes will be on the same thread list." | taşınabilirlik sınırı: **başka sürecin** parçacıklarını varsaymak yasak |

| # | Soru | Yanıt | Kanıt |
|---|---|---|---|
| 1 | Repo'da `sched_yield` çağrısı var mı? | **hayır** | grep `sched_` → 0 (§5 D-1) |
| 2 | Uygulama katmanında eşdeğer "sıra bırakma" var mı? | **hayır** — repo `Retry-After` ile **beklemeyi istemciye** devreder | `RateLimitMiddleware.php:40` (§1.4) |
| 3 | "yield" ile "reddetme" aynı mı? | **hayır** — yield = sıraya geri dön, reddetme = kuyruğa almama | §1.4 kavram ayrımı |

> **Katman cümlesi:** POSIX `sched_yield()` bir **OS/parçacık** yüzeyidir; bu dosyadaki
> hiçbir örnek onun yerine geçmez (§5.8 matrisinin 3. satırı).

## §5 Repo'da olmayan (dürüstlük defteri)

### §5.1 Defter kuralları

| # | Kural |
|---|---|
| 1 | Yokluk **arama deseni + kapsam + sonuç** ile kanıtlanır |
| 2 | Yokluk, "hiçbir yerde olamaz" **demez** — yalnız "bu kapsamda yok" der |
| 3 | Kardeş dosya iddiası bu defterle çelişiyorsa ⚠️ işaretlenir, **silinmez** |
| 4 | Kayıt yalnız kanıtla kapanır |

### §5.2 Kayıtlar

| # | Kayıt | Aranan desen | Kapsam | Sonuç |
|---|---|---|---|---|
| D-1 | OS zamanlama/parçacık API'si | `pcntl_` · `pthread_` · `sched_set*` · `sched_get*` · `affinity` · `setpriority(` | `*.php` (repo) | **0 eşleşme** |
| D-2 | Öncelikli kuyruk sınıfı | `TaskQueue` · `priority_queue` · `SplQueue` | `*.php` | **0 sınıf** (yalnız 1 docblock atfı: `AIOrchestrator.php:20`) |
| D-3 | `SCHED_*` / `nice(` çağrısı | `SCHED_FIFO` · `SCHED_RR` · `SCHED_DEADLINE` · `nice(` | `*.php` | **0 eşleşme** |
| D-4 | `sched-stats` / çekirdek sayacı okuması | `schedstat` · `/proc/stat` · `cpu.stat` | PHP/CI | **0 eşleşme** |
| D-5 | Eşzamanlı yürütme | `curl_multi` · `pcntl_fork` · `proc_open` (üretim kodu) | `shared/src` | **0 üretim kanıtı** (yalnız test dosyasında `proc_open`) |

### §5.3 ⚠️ Kardeş iddia uyuşmazlığı

| # | İddia (kardeş dosya) | Bu oturumun ölçümü | Etiket |
|---|---|---|---|
| 1 | `is-parcacigi-planlamasi-ornekler.md` → `pcntl_fork()` çağrısı | `pcntl_` deseni repo genelinde **0 eşleşme** | ⚠️ VERIFICATION REQUIRED |
| 2 | Aynı dosya → `CHILD_TIMEOUT = 15` sabiti | ilgili desen **bulunamadı** | ⚠️ VERIFICATION REQUIRED |
| 3 | `ServiceHealth.php:24` "parallel" | `ServiceRegistry.php:56` **seri** döngü | gözlem (§3.3) |
| 4 | `AIOrchestrator.php:20` "TaskQueue" | sınıf **yok** | gözlem (D-2) |

> **Kural:** Bu satırlar **düzeltilemez** (başkasının dosyası) — yalnız işaretlenir ve
> ilgili alt-dalın sorumlusuna devredilir. Kaynak: bu oturumda yapılan grep + Read.

### §5.4 Komşu dal devri

| # | Defter | Nereye devredilir |
|---|---|---|
| D-1 · D-3 | OS API yokluğu | `is-parcacigi-planlamasi` · `isletim-sistemi-kernel` |
| D-2 · D-5 | kuyruk/paralellik | `is-parcacigi-planlamasi` · `isletim-sistemi-runtime` |
| D-4 | ölçüm | `isletim-sistemi-observability` |

### §5.5 Bu dosyanın kendi yüzeyi

| # | Yüzey | Durum |
|---|---|---|
| 1 | Repo satır alıntıları | ✅ Read ile teyitli |
| 2 | Politika karşılaştırması | ✅ resmi kaynak metni (2026-10-13) |
| 3 | Ölçüm sayıları | ❌ **yok** — tarif var, sayı uydurulmadı |
| 4 | Kardeş iddia uyuşmazlığı | ⚠️ işaretli, **kapatılmadı** |

### §5.6 Şablon: kanıt eşiği (bu alt-dalda iddia yazma kuralı)

| İddia sınıfı | Minimum kanıt | Kabul |
|---|---|---|
| Repo davranışı (satır) | `Read` ile dosya okuma + satır numarası | Evet |
| Repo davranışı (sayı/sınır) | dosyanın kendi satırı (ör. `$ttl`, `$maxItems`) | Evet |
| Repo davranışı (yokluk) | `grep` sonucu: 0 eşleşme (arama terimleri yazılır) | Evet |
| Resmi doküman | HTTP 200 + `validation: 2026-10-13` | Evet |
| Kardeş vault iddiası | kardeş dosya + satır + **bu oturumda grep/Read ile teyit** | Teyitliyse evet; değilse ⚠️ (§5.3) |
| Ölçüm değeri (N, süre, eşik) | repo ya da resmi kaynakta sayı | Yoksa formül + `UNKNOWN` (§3.2) |
| Sistem/kullanıcı ayrımı | anayasa §2.3 + repo kanıtı | Evet |
| Tahmin / çıkarım | — | **Hayır** — açık `⚠️ VERIFICATION REQUIRED` |

### §5.7 Şablon: kabul kriterleri (dosya tamamlanma listesi)

- [x] §0 ELI15 + `## §1`…`## §6` başlıkları eksiksiz (sayım yapıldı).
- [x] Son satır birebir: `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode`
- [x] Frontmatter 9 alan (`parent:` alanı **yok**; `group` tırnaksız `kod`).
- [x] Tüm S1-S4 kanıtları bu oturumda `Read`/`Get-Content` ile okundu; okunmayan satır yazılmadı.
- [x] URL'ler doğrulanmış envanterden (`isletim-sistemi-plan-dogrulama`) ya da bu oturumda HTTP 200 ile; validation alanı dolu.
- [x] Ölçüm değeri kaynakta yoksa formül + `UNKNOWN` yazıldı; sayı uydurulmadı.
- [x] Kardeş `-plan` dosyasındaki teyitsiz iddia kullanılmadı; §5.3'e ⚠️ olarak kaydedildi.
- [x] `TBD`/`TODO`/boş hücre yok; mojibake yok.
- [x] `is-parcacigi-planlamasi-ornekler.md` **düzenlenmedi** (salt-okunur kaynak).

### §5.8 Katman sınıflandırma matrisi (bu dosyadaki her kanıtın yeri)

| # | Kanıt / yüzey | Katman | Zamanlayıcıya dokunuyor mu? | Bölüm |
|---|---|---|---|---|
| 1 | `AIOrchestrator` öncelik alanı (`critical`…`low`) | uygulama (PHP) | ❌ hayır — etiket | §1.1–§1.2 |
| 2 | `hrtime(true)` middleware süresi | uygulama (PHP) | ❌ hayır — süre ölçümü | §1.3 · §2.1 · §2.3 |
| 3 | `RateLimitMiddleware` 429 + `Retry-After` | uygulama (HTTP) | ❌ hayır — reddetme | §1.4 |
| 4 | `ServiceDefinition` timeout 5 sn | uygulama (HTTP bekleme) | ❌ hayır | §1.5 |
| 5 | `ci.yml` `timeout-minutes` · `needs` · `concurrency` · `continue-on-error` | **CI (GitHub Actions)** | ❌ hayır — iş grafiği | §1.6 · §2.1 #4 |
| 6 | `.htaccess` / sunucu yapılandırması | sunucu (Apache) | ❌ hayır | (bkz. `kaynak-dagilimi-ornekler §1.9`) |
| 7 | CFS · `SCHED_DEADLINE` · sched-ext · `sched-stats` | **OS (çekirdek)** | ✅ evet | §4.1–§4.5 |
| 8 | `sched_yield()` · `getrusage()` · affinity · `MPOL_*` | **OS (çekirdek/süreç)** | ✅ evet | §2.5 · §4.6 · §5 D-1 |

> **Bu dosyanın tezi (matrisle kanıtlanan):** satır 1–6 bu depoda **vardır ve okunmuştur**;
> satır 7–8 bu depoda **yoktur** (§5.2 D-1 · D-3 · D-4). Aradaki çizgi **kapatılmaz**;
> "uygulama önceliği = zamanlama" cümlesi bu matrisle **yalanlanır** (§3.2).

## §6 Kaynakça

### §6.1 Web doğrulama (2026-10-13 · GET · içerik okundu)

| # | İddia | URL | Durum |
|---|---|---|---|
| 1 | CFS tanımı + `nr_running` adalet modeli | https://docs.kernel.org/scheduler/sched-design-CFS.html | ✅ HTTP 200 |
| 2 | `SCHED_DEADLINE` = EDF + CBS · `runtime/period/deadline` | https://docs.kernel.org/scheduler/sched-deadline.html | ✅ HTTP 200 |
| 3 | `sched-ext` BPF tabanlı sınıf + otomatik geri dönüş | https://docs.kernel.org/scheduler/sched-ext.html | ✅ HTTP 200 |
| 4 | `sched-stats` sürüm notları | https://docs.kernel.org/scheduler/sched-stats.html | ✅ HTTP 200 |
| 5 | CPU capacity tanımı | https://docs.kernel.org/scheduler/sched-capacity.html | ✅ HTTP 200 |
| 6 | politika öncelik aralığı sorgusu | kardeş envanter N-W12 (`sched_get_priority_max`) | ✅ yeniden kullanım |
| 7 | `nice` öncelik girdisi | kardeş envanter R-6 (`nice.2`) | ✅ HTTP 200 (bu oturumda teyit) |
| 8 | POSIX seçenek tanımı | kardeş envanter (`posixoptions.7`) | ✅ yeniden kullanım |
| 9 | süreç ölçüm alanı kümesi (`ru_nvcsw` · `ru_nivcsw`) — §2.5 | https://man7.org/linux/man-pages/man2/getrusage.2.html | ✅ HTTP 200 · içerik okundu (2026-10-13) |
| 10 | POSIX `sched_yield()` — "relinquish the processor" — §4.6 | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html | ✅ HTTP 200 · içerik okundu (2026-10-13) |
| 11 | CI iş bağımlılığı/`timeout-minutes` yapılandırma kanıtı — §1.6 | repo dosyası `.github/workflows/ci.yml` (203 satır) · `.github/workflows/secret-scan.yml` (31 satır) | ✅ Read ile birebir (2026-10-13) |

> **Alan adı notu (2026-10-13):** `pubs.opengroup.org` bu dosyada **ilk kez** kullanılmıştır
> (diğerleri kardeş envanterle kesişir — yeni alan adı eklenmemiştir). CI satırı bir **repo
> kanıtıdır**, web URL'si **değildir** — §6.1 başlığı yalnız web doğrulamasını taşır.

### §6.2 Repo kanıtları özeti

| # | Dosya | Satır |
|---|---|---|
| 1 | `shared/src/AI/AIOrchestrator.php` | 20 · 41–48 · 71 · 84 |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 · 44 · 46 · 48 |
| 3 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 23 · 39 · 40 · 41–45 · 49 |
| 4 | `shared/src/Api/Registry/ServiceDefinition.php` | 23 |
| 5 | `shared/src/Api/Registry/ServiceRegistry.php` | 52 · 56 · 104 · 109 |
| 6 | `shared/src/Api/Registry/ServiceHealth.php` | 24 · 26 |
| 7 | `shared/src/AI/AIEngine.php` | 290–298 |
| 8 | `.github/workflows/ci.yml` | 27 · 28 · 69 · 70 · 112 · 113 · 144 · 145 · 163 · 164 · 180 · 181 |
| 9 | `.github/workflows/ci.yml` | 8 · 10–12 · 14–15 · 17–19 · 21–22 · 68 · 162 · 167 · 178–183 (§1.6) |

### §6.3 İlişkili vault dosyaları

| Dosya | Bağ |
|---|---|
| `zamanlama-scheduler-anayasa.md` | kural + §7 açık sorular |
| `zamanlama-scheduler-surucu.md` | mekanizma + §7 repo kanıtları |
| `is-parcacigi-planlamasi-ornekler.md` | kardeş dürüstlük defteri (§5.3 çelişkisi) |
| `kaynak-dagilimi-ornekler.md` | kota/kabul örneği |
| `isletim-sistemi-api` | uygulama katmanı sınırı |

## §7 OS bazlı zamanlama örnekleri (her OS kendi altında)

> **Tek satır dürüstlük:** Aşağıdaki hiçbir örneğin bu depoda karşılığı **yoktur**
> (`pcntl_` · `pthread_` · `sched_` → 0 eşleşme, §5 D-1) — hepsi **OS seviyesi** referanstır.
> Her URL **2026-10-14 HTTP 200** ile ölçülmüştür.

### §7.1 Linux / POSIX (Raspberry Pi 5 hedefi)

| # | Zamanlama örneği | Kaynak | 2026-10-14 |
|---|---|---|---|
| Z-L1 | `chrt -r <prio> cmd` — SCHED_RR (varsayılan RT politikası); `-f` FIFO; `-d` DEADLINE `runtime ⇐ deadline ⇐ period` ilişkisi | https://man7.org/linux/man-pages/man1/chrt.1.html | HTTP 200 |
| Z-L2 | `sched_setscheduler(pid, policy, param)` — `pid=0` çağıran; POSIX'te `SCHED_SPORADIC` dahil; `sched_priority` politikanın öncelik aralığında | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_setscheduler.html | HTTP 200 |
| Z-L3 | `sched_getattr(pid, attr, size, flags)` / `sched_setattr` — `SCHED_OTHER/BATCH/IDLE` + `SCHED_FIFO/RR/DEADLINE`; glibc wrapper yok → `syscall(2)` | https://man7.org/linux/man-pages/man2/sched_getattr.2.html | HTTP 200 |
| Z-L4 | RT grup bütçesi: `sched_rt_period_us` içinde `sched_rt_runtime_us`; kullanılmayan kısım `SCHED_OTHER`'a; 25 fps renderer: period 0.04s → budget 0.032s | https://docs.kernel.org/scheduler/sched-rt-group.html | HTTP 200 |
| Z-L5 | `sched_yield()` — kuyruğun sonuna bırak (POSIX REALTIME) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sched_yield.html | HTTP 200 |

```c
/* OS seviyesi — depoda karşılığı YOK (kaynak: Z-L2, 2026-10-14 HTTP 200) */
struct sched_param p = { .sched_priority = 80 };
sched_setscheduler(0, SCHED_FIFO, &p);   /* pid=0: çağıran parçacık */
```

### §7.2 Windows (WASAPI hedefi — `.ai/AGENTS.md §4` #11)

| # | Zamanlama örneği | Kaynak | 2026-10-14 |
|---|---|---|---|
| Z-W1 | `SetThreadIdealProcessor(hThread, dwIdealProcessor)` — "sistem parçacıkları tercih edilen işlemcilerde programlar"; dönüş önceki tercih; hata `(DWORD)-1` | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadidealprocessor | HTTP 200 |
| Z-W2 | `SetPriorityClass(hProcess, dwPriorityClass)` — `HIGH_PRIORITY_CLASS` "zamanında yürütülmesi gereken" süreç; `PROCESS_MODE_BACKGROUND_BEGIN` kaynak kullanımını düşürür | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass | HTTP 200 |
| Z-W3 | `AvSetMmThreadCharacteristicsA(TaskName, TaskIndex)` — thread'i Multimedya SystemProfile görevine bağlar (ses için "Audio" görevi); bitişte `AvRevertMmThreadCharacteristics` | https://learn.microsoft.com/en-us/windows/win32/api/avrt/nf-avrt-avsetmmthreadcharacteristicsa | HTTP 200 |
| Z-W4 | `SetThreadPriority` / `GetThreadPriority` — parçacık öncelik seviyesi oku/ayarla | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setthreadpriority | HTTP 200 |

```c
/* OS seviyesi — depoda karşılığı YOK (kaynak: Z-W3, 2026-10-14 HTTP 200) */
DWORD idx = 0;
HANDLE hTask = AvSetMmThreadCharacteristicsA("Audio", &idx); /* ses parçacığı */
/* ... iş bitti: AvRevertMmThreadCharacteristics(hTask); */
```

### §7.3 macOS / iOS (aday platform)

| # | Zamanlama örneği | Kaynak | 2026-10-14 |
|---|---|---|---|
| Z-A1 | `DispatchQoS` — QoS = "yürütme önceliği"; `userInteractive` > `userInitiated` > `default` > `utility` > `background`; "sistem daha yüksek servis sınıflarını önceliklendirir" | https://developer.apple.com/documentation/dispatch/dispatchqos | HTTP 200 |
| Z-A2 | `DispatchQueue` — QoS sınıfıyla kuyruk oluşturulur | https://developer.apple.com/documentation/dispatch/dispatchqueue | HTTP 200 |
| Z-A3 | `Thread` — Foundation parçacık sarmalayıcısı | https://developer.apple.com/documentation/foundation/thread | HTTP 200 |

```swift
/* OS seviyesi — depoda karşılığı YOK (kaynak: Z-A1, 2026-10-14 HTTP 200) */
DispatchQueue(label: "render", qos: .userInteractive).async { /* RT benzeri iş */ }
```

> `pthread_attr_setschedpolicy` Apple dokümanı 2026-10-14'te **HTTP 404** — doğrulanamadı,
> uydurulmadı; POSIX karşılığı §7.1 Z-L2.

### §7.4 RTOS — Zephyr (gömülü hedef)

| # | Zamanlama örneği | Kaynak | 2026-10-14 |
|---|---|---|---|
| Z-R1 | "En yüksek öncelikli hazır parçacık seçilir; eşit öncelikte en uzun süredir bekleyen seçilir"; EDF: `CONFIG_SCHED_DEADLINE` + `k_thread_deadline_set()`; reschedule noktaları: `k_sem_take()`/`k_sleep()`/`k_yield()` + ISR dönüşü | https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html | HTTP 200 |
| Z-R2 | Hazır kuyruk seçenekleri: `CONFIG_SCHED_SIMPLE` (≈2k tasarruf) · `CONFIG_SCHED_SCALABLE` (kırmızı-siyah ağaç, >20 parçacık) · `CONFIG_SCHED_MULTIQ` (O(1) klasik) | https://docs.zephyrproject.org/latest/kernel/services/scheduling/index.html | HTTP 200 |
| Z-R3 | `k_cycle_get_32()`/`k_cycle_get_64()` — en hızlı sayaç; frekans `sys_clock_hw_cycles_per_sec()` (sabit `CONFIG_SYS_CLOCK_HW_CYCLES_PER_SEC` ya da çalışma zamanı güncellenen platformlar) | https://docs.zephyrproject.org/latest/kernel/services/timing/clocks.html | HTTP 200 |

> **FreeRTOS boşluğu:** `freertos.org` 6 aday URL'si 2026-10-14'te **tamamı HTTP 404** →
> FreeRTOS için doğrulanmış örnek **yok**; `⚠️ VERIFICATION REQUIRED` açık kalır.

### §7.5 OS matrisi (zamanlama yeteneği → OS)

| Yetenek | Linux/POSIX | Windows | macOS/iOS | Zephyr RTOS |
|---|---|---|---|---|
| Politika ata | Z-L2 · Z-L3 | Z-W2 (sınıf) | Z-A1 (QoS) | sabit (statik öncelik) |
| RT politikası | Z-L1 FIFO/RR/DEADLINE | yok (sınıf tabanlı) | yok (QoS tabanlı) | öncelik + EDF (Z-R1) |
| Çekirdek bağlama | `taskset`(§ kök L-1) | `SetThreadAffinityMask`(kök W-1) | resmi API **404 — ⚠️** | tek çekirdek varsayılan |
| Öncelik oku/ata | Z-L3 | Z-W4 | Z-A1 (dolaylı) | `k_thread_*` |
| Ölçüm kaynağı | `sched` olayları ⚠️ (bkz §6 düzeltmesi) | Performans sayaçları | QoS ihlali logu | Z-R3 çevrim sayacı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
