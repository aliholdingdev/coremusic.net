---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-core
evidence:
  - "https://docs.kernel.org/admin-guide/cgroup-v2.html"
  - "https://man7.org/linux/man-pages/man2/getrlimit.2.html"
  - "https://man7.org/linux/man-pages/man2/ioprio_set.2.html"
  - "https://man7.org/linux/man-pages/man1/ionice.1.html"
  - "https://docs.kernel.org/mm/numa.html"
  - "https://man7.org/linux/man-pages/man2/nice.2.html"
  - "shared/src/Cache/PageCacheAdapter.php:11"
  - "shared/src/Security/CacheRateLimiter.php:16"
validation:
  - "2026-10-13: 8 URL HTTP 200 · sayfa içeriği parçası alıntılanarak okundu · envanter kesişimi 0"
  - "2026-10-13: komşu sınırı §7 — 8 komşu, ihlal 0"
  - "2026-10-13: repo satırları Read ile birebir okundu"
open_questions:
  - "Q3 — kapsam OS kaynak dağıtımı mı, uygulama kotası mı? [UNKNOWN]"
  - "Q-kd-1 — cgroup/NUMA kotası üretimde uygulanıyor mu? [⚠️ VERIFICATION REQUIRED]"
group: mimari
---

# kaynak-dagilimi — Anayasa

> **Tek cümle:** Bu dosya `kaynak-dagilimi` alt-dalının **anayasasıdır** — kaynağın kime, ne
> kadar ve ne zaman verileceğini, hangi kavramlarla tanımlandığını, hangi komşuya ne
> bıraktığını ve hangi soruyu **kapatmadan** bıraktığını tanımlar; ayrıntılı kota/mekanizma
> `-surucu.md`'dedir, çalışan repo hâli `-ornekler.md`'dedir.

## §0 ELI15

| Soru | Kısa yanıt | Derin cevap |
|---|---|---|
| Kaynak nedir? | CPU zamanı, bellek, disk G/Ç, süreç sayısı | §2–§4 |
| Kime verilir? | Süreç/parçacık (ve onu çalıştıran cgroup'a) | §2.1 |
| Ne kadar verilir? | nice/CPU bant genişliği · bellek sınırı · IO BPS/IOPS · RLIMIT | §2.5 · §3.2 · §3.3 · §4.2 |
| Ne zaman verilir? | çalıştırılabilir olduğunda ve kuyruk sırası geldiğinde | §4.1 · §6.1 |
| Ölçmeden verilir mi? | hayır — utilization ve run queue ölçülmeden kota adil değildir | §6 |
| Repo'da karşılığı ne? | yalnız **uygulama katmanı** kotaları (TTL · rate limit · timeout) | `-ornekler.md §1–§2` |

> **Sınır cümlesi:** Bu dosya **kuralı** yazar; kuyruğun **içinde nasıl çalıştığını**
> `isletim-sistemi-kernel`, hangi **sıranın** verildiğini `zamanlama-scheduler` yazar.

## §1 Kimlik ve konum

### §1.1 Dosya künyesi

| Alan | Değer |
|---|---|
| `node_id` | `K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-anayasa` |
| `node_type` | `module` |
| `status` | `draft` |
| `parent_id` | `K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi` |
| `group` | `mimari` |
| Kademe | 4 (alt-dal içerik) · ebeveyn `isletim-sistemi-plan` (Kademe-2) |
| Eksen sorusu | kaynak kime, ne kadar, ne zaman verilir? |
| Kardeşler | `-surucu.md` (mekanizma) · `-ornekler.md` (kanıt) · `index.md` (künye) |

### §1.2 Bu dosyanın neyi taşımıyor

| # | Taşınmaz | Nereye gider | Ayırıcı |
|---|---|---|---|
| 1 | Zamanlama algoritması (RR · CFS · EDF) | `zamanlama-scheduler` | sıra (orada) vs kota (burada) |
| 2 | Parçacık durum makinesi, affinity | `is-parcacigi-planlamasi` | birim (orada) vs pay (burada) |
| 3 | cgroup dosya ayrıntısı, disiplin dizini | `kaynak-dagilimi-surucu.md` | kural (burada) | uygulama (surucu) |
| 4 | Çalışan repo kodu, anti-pattern | `kaynak-dagilimi-ornekler.md` | anayasa vs kanıt |
| 5 | Bellek yönetimi çekirdek içi (page reclaim, compaction) | `isletim-sistemi-kernel` | politika (burada) | mekanizma (kernel) |
| 6 | Donanım kapasitesi, çekirdek topolojisi donanım tarafı | `K001-donanım` | kapsam dışı |

## §2 CPU çekirdeği / paylaşım

### §2.1 Time-sharing — neden "kota" diyoruz

| # | Kavram | Tek cümle | Kaynak |
|---|---|---|---|
| 1 | Time-sharing | Kaynak (CPU) ince dilimlere bölünür; her süreç sırasını bekler | `sched.7` (kardeş envanter) |
| 2 | Koşan kuyruk (run queue) | Çalıştırılabilir süreçlerin beklediği sıra | `-surucu §5.2` |
| 3 | nice değeri | Sürecin "ne kadar acele edeceğini" gösteren öncelik girdisi | https://man7.org/linux/man-pages/man2/nice.2.html |
| 4 | cgroup | Süreç grubuna CPU/bellek/IO üst sınırı koyan çekirdek mekanizması | https://docs.kernel.org/admin-guide/cgroup-v2.html |
| 5 | RLIMIT | Tek süreç için çekirdek sınırı (süreç başına, grup değil) | https://man7.org/linux/man-pages/man2/getrlimit.2.html |
| 6 | Zaman dilimi (quantum) | Kesintisiz çalışma üst sınırı — ayrı dosya: `zamanlama-scheduler-surucu §3` | kardeş dosya |

### §2.2 nice / setpriority — öncelik girdisi

| # | İfade | Ne yapar | Doğrulanma |
|---|---|---|---|
| 1 | `nice` değeri | Sürecin öncelik girdisi — düşük nice = daha yüksek öncelik | nice(2) sayfası, HTTP 200 · 2026-10-13 |
| 2 | Yetkisiz süreç `RLIMIT_NICE` soft limiti varsa hedef sürecin nice değerini **düşürebilir** | Linux 2.6.12'den beri | nice(2)/setpriority(2) metni (alıntı §2.3) |
| 3 | `getpriority()` | çağıran parçacığın nice değerini döndürür, **negatif olabilir** | setpriority(2) metni |
| 4 | `RLIMIT_NICE` aralığı | `1` → nice `19`, `40` → nice `-20`; negatif sayı limit değeri olarak yazılamadığı için `20 - rlim_cur` kullanılır | getrlimit(2) metni (alıntı §2.3) |

> **Dürüstlük notu:** nice **öncelik girdisidir, garanti değildir** — hangi politikanın bu
> girdiyi nasıl kullandığı `zamanlama-scheduler` kapsamındadır (Plan1 `[UNKNOWN]`).

### §2.3 Alıntı bloğu (resmi kaynak metni — 2026-10-13)

| Kaynak | Alıntı (özgün) | Ne kanıtlar |
|---|---|---|
| getrlimit(2) | `RLIMIT_NICE` — "calculated as 20 - rlim_cur ... from 1 (corresponding to a nice value of 19) to 40 (corresponding to a nice value of -20)" | nice aralığı ve limit dönüşümü |
| getrlimit(2) | `RLIMIT_RTPRIO` — "a ceiling on the real-time priority that may be set for this process using sched_setscheduler(2)" | RT öncelik üst sınırı da bir kota |
| nice(2) | "an unprivileged process can decrease the nice value of a target process that has a suitable RLIMIT_NICE soft limit" | yetki sınırı kota ile birlikte okunur |
| setpriority(2) | "getpriority() returns the calling thread's nice value, which may be a negative number" | nice negatif olabilir |

### §2.4 Çekirdek/topoloji — kota kaça bölünür

| # | Soru | Bu dosyanın cevabı | Kanıt durumu |
|---|---|---|---|
| 1 | Kaç mantıksal çekirdek var? | Çalışma zamanında sorgulanır (`sysconf`, `getcpu`) | kardeş envanter N-W06/N-W16 · HTTP 200 |
| 2 | Hedef cihaz kaç çekirdekli? | `K001-donanım` kapsamı — bu dosyada **yazılmaz** | ⚠️ VERIFICATION REQUIRED (kapsam dışı) |
| 3 | Çekirdek izolasyonu (affinity/isolation) | `is-parcacigi-planlamasi` + `isletim-sistemi-kernel` | kardeş dosyalar |
| 4 | Heterojen çekirdek (büyük/küçük) | `zamanlama-scheduler-anayasa §10.4` | kardeş dosya |

### §2.5 CPU kotası — iki farklı mekanizma

| # | Mekanizma | Neyi sınırlar | Erişim | Resmi kaynak |
|---|---|---|---|---|
| 1 | `nice` / `setpriority` | **göreli** öncelik (kim önce çalışır) | süreç | https://man7.org/linux/man-pages/man2/nice.2.html |
| 2 | cgroup `cpu.max` | **mutlak** bant genişliği: `$MAX $PERIOD` biçiminde, `$MAX` süre içinde en fazla o kadar tüketim; `"max 100000"` varsayılan, `"max"` = sınır yok | süreç grubu | https://docs.kernel.org/admin-guide/cgroup-v2.html |
| 3 | cgroup `cpu.weight` | "proportionally distributes CPU cycles to active children" — **oransal** pay | süreç grubu | aynı kaynak |
| 4 | `RLIMIT_RTPRIO` | gerçek zamanlı öncelik üst sınırı | süreç | getrlimit(2) |
| 5 | `RLIMIT_CPU` | CPU süresi (saniye) üst sınırı | süreç | getrlimit(2) |

> **Ayrım kuralı:** *göreli* öncelik (nice) **sırayı** etkiler, *mutlak* kota (cpu.max) **tüketimi**
> sınırlar. İkisini aynı cümlede karıştırmak bu alt-dalın en sık hatasıdır.

### §2.6 Kolu seçme kararı (hangi kota ne için)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Sınır **grup** için mi, tek süreç için mi? | → cgroup (`cpu.max`) | 2'ye geç |
| 2 | Sınır **tüketime** mi yoksa **sıraya** mı dokunuyor? | → `cpu.max` / `io.max` | `nice` / `cpu.weight` |
| 3 | Süreç **kendi** kendini sınırlamak mı istiyor? | → `RLIMIT_*` | cgroup'a geç |
| 4 | Beklenti **gerçek zamanlı** öncelik mi? | → `RLIMIT_RTPRIO` (+ `RLIMIT_RTTIME`) | normal sınıfa geç |
| 5 | Beklenti **disk** sırası mı? | → `IOPRIO_CLASS_*` (§4.2) | CPU sınıfına dön |
| 6 | Ölçüm yok mu? | → kota **yazma**; önce §6 | kota yazılabilir |

> **Son satır bağlayıcıdır:** ölçüm olmadan kota yazmak bu alt-dalda **hata** sayılır
> (§6.2 #1). Ölçümün adı da yazılır — `hrtime` ≠ çekirdek sayacı (§6.2 #4).

### §2.7 Bağlama (affinity) — kota yüzeyi okuması (durum makinesi değil)

> **Sınır uyarısı:** Bu bölüm bağlamayı yalnız **kota** (hangi CPU'da çalışabilir) olarak okur;
> parçacık durum makinesi `is-parcacigi-planlamasi`'ndadır (§1.2 #2 — çizgi korunur).
> Kaynak: https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html · HTTP 200 · 2026-10-13

| # | Alıntı (özgün) | Ne kanıtlar |
|---|---|---|
| 1 | "A thread's CPU affinity mask determines the set of CPUs on which it is eligible to run." | bağlama = çalışılabilir CPU kümesi = bir **kota** |
| 2 | "If the thread specified by pid is not currently running on one of the CPUs specified in mask, then that thread is migrated to one of the CPUs specified in mask." | kota değişince **göç** zorunlu sonucu vardır |
| 3 | "the set of CPUs on which the thread will actually run is the intersection of the set specified in the mask argument and the set of CPUs actually present on the system" | istenen küme ≠ çalışan küme |
| 4 | "The system may further restrict the set of CPUs ... if the "cpuset" mechanism ... is being used. These restrictions ... are silently imposed by the kernel." | cpuset üst sınırı **sessiz** uygulanır |
| 5 | "A child created via fork(2) inherits its parent's CPU affinity mask. The affinity mask is preserved across an execve(2)." | kota **miras** alınır ve kalıcıdır |
| 6 | "The affinity mask is a per-thread attribute that can be adjusted independently for each of the threads in a thread group." | kota birimi = **parçacık** |

**Hata kataloğu (ERRORS bölümü, özgün):**

| # | `errno` | Koşul (özgün) |
|---|---|---|
| 1 | `EFAULT` | "A supplied memory address was invalid." |
| 2 | `EINVAL` | "The affinity bit mask mask contains no processors that are currently physically on the system and permitted to the thread according to any restrictions that may be imposed by cpuset cgroups" |
| 3 | `EPERM` | "The calling thread does not have appropriate privileges ... or it must possess the CAP_SYS_NICE capability in the user namespace of the thread pid." |
| 4 | `ESRCH` | "The thread whose ID is pid could not be found." |

> **Repo durumu:** `sched_setaffinity` · `sched_getaffinity` · `affinity` → `*.php` içinde
> **0 eşleşme** (2026-10-13 grep; `-parcacigi-surucu §` aynı sonucu yazar). Bu bölüm bir
> **kural** satırıdır, çalışan kod kanıtı değil.

## §3 Bellek atribütleri (NUMA · cgroup)

### §3.1 NUMA — bellek "her yere eşit" değildir

| # | Kavram | Tanım | Kaynak |
|---|---|---|---|
| 1 | NUMA | Düğümler arası bellek erişimi eşit değildir; yerel düğüm hızlı, uzak düğüm yavaş | https://docs.kernel.org/mm/numa.html |
| 2 | Yerleşim (placement) | Belleğin hangi düğüme ayrıldığı | aynı kaynak |
| 3 | Bağlama (affinity) | İşin/ belleğin belirli düğüme bağlanması | `is-parcacigi-planlamasi` (komşu) |
| 4 | Repo durumu | PHP uygulama katmanında NUMA okuması **yok** | grep → 0 (bkz. `-ornekler §5`) |

### §3.2 Bellek kotası — cgroup v2 dosyaları

| # | Dosya | Varsayılan | Ne yapar (resmi metinden) | Kaynak |
|---|---|---|---|---|
| 1 | `memory.max` | `"max"` | "Memory usage hard limit. This is the main mechanism to limit memory usage of a cgroup" | https://docs.kernel.org/admin-guide/cgroup-v2.html |
| 2 | `memory.high` | `"max"` | "Memory usage throttle limit" — aşımda süreçler throttling + yoğun reclaim; "never invokes the OOM killer" | aynı |
| 3 | `cpu.max` | `"max 100000"` | bant genişliği üst sınırı (`$MAX $PERIOD`) | aynı |
| 4 | `io.max` | — | "limits the maximum BPS and/or IOPS that a cgroup can consume on an IO device" | aynı |
| 5 | `cpu.stat` | okuma-yalnız | `usage_usec` · `user_usec` · `system_usec` (mikrosaniye) | aynı |

> **Sıra kuralı:** `memory.high` (throttle) → `memory.max` (hard) → OOM. Kota tasarımı bu
> üç sütunu **ayrı** düşünür; tek sayı "bellek limiti" diye yazılmaz.

### §3.3 RLIMIT — süreç başına sınır listesi

`getrlimit(2)` sayfasından okunan tam küme (17 adet, HTTP 200 · 2026-10-13):

| # | RLIMIT | Ne sınırlandırır |
|---|---|---|
| 1 | `RLIMIT_AS` | süreç sanal bellek (adres uzayı) — bayt |
| 2 | `RLIMIT_CORE` | çekirdek dosyası boyutu |
| 3 | `RLIMIT_CPU` | CPU süresi (saniye) |
| 4 | `RLIMIT_DATA` | veri segmenti (heap) |
| 5 | `RLIMIT_FSIZE` | dosya boyutu üst sınırı |
| 6 | `RLIMIT_LOCKS` | dosya kilidi adedi |
| 7 | `RLIMIT_MEMLOCK` | kilitlenmiş bellek |
| 8 | `RLIMIT_MSGQUEUE` | POSIX mesaj kuyruğu belleği |
| 9 | `RLIMIT_NICE` | nice değeri için izin (§2.3) |
| 10 | `RLIMIT_NOFILE` | açık dosya tanımı adedi |
| 11 | `RLIMIT_OFILE` | `RLIMIT_NOFILE` takma adı |
| 12 | `RLIMIT_NPROC` | kullanıcı başına süreç adedi |
| 13 | `RLIMIT_RSS` | RSS üst sınırı (eski/sınırlı etki) |
| 14 | `RLIMIT_RTPRIO` | gerçek zamanlı öncelik üst sınırı |
| 15 | `RLIMIT_RTTIME` | RT öncelikte kesintisiz çalışma süresi |
| 16 | `RLIMIT_SIGPENDING` | bekleyen sinyal adedi |
| 17 | `RLIMIT_STACK` | yığın (stack) boyutu |

### §3.4 cgroup vs RLIMIT — ayrım tablosu

| Ölçüt | cgroup | RLIMIT |
|---|---|---|
| Kapsam | süreç **grubu** + alt ağaç | tek süreç (+ çocuklar) |
| Sayım | ağacın tamamı | süreç başına |
| Bellek | `memory.max` / `memory.high` | `RLIMIT_AS` / `RLIMIT_DATA` / `RLIMIT_STACK` |
| CPU | `cpu.max` / `cpu.weight` | `RLIMIT_CPU` (süre) / `RLIMIT_RTPRIO` (öncelik) |
| G/Ç | `io.max` (BPS/IOPS) | yok |
| Dosya | çekirdekte `cgroup.*` arayüzü | `setrlimit(2)` çağrısı |
| Repo durumu | **yok** (0 kanıt) | **yok** (0 kanıt) → `-ornekler §5` |

### §3.5 NUMA yerleşim kipleri — `set_mempolicy(2)` kota kümesi

> Kaynak: https://man7.org/linux/man-pages/man2/set_mempolicy.2.html · HTTP 200 · 2026-10-13
> "set_mempolicy() sets the NUMA memory policy of the calling thread ... The memory policy
> defines from which node memory is allocated for the thread."

| # | Kip | Ne der (özgün özet) | Kota okuması |
|---|---|---|---|
| 1 | `MPOL_DEFAULT` | "falls back" to system default — "local allocation"; `nodemask` must be `NULL` | kota yok |
| 2 | `MPOL_BIND` | "strict policy that restricts memory allocation to the nodes specified in nodemask" · "Pages will not be allocated from any node not specified in the nodemask." | **mutlak** kota |
| 3 | `MPOL_INTERLEAVE` | "interleaves page allocations across the nodes ... in numeric node ID order ... optimizes for bandwidth instead of latency" | oransal dağıtım |
| 4 | `MPOL_PREFERRED` | "try to allocate pages from this node first and fall back to "near by" nodes" | yumuşak tercih |
| 5 | `MPOL_PREFERRED_MANY` (5.15) | "accepts a set of nodes versus a single node" | tercih **kümesi** |
| 6 | `MPOL_LOCAL` (3.8) | "local allocation ... on the node of the CPU that triggered the allocation" | kota yok |
| 7 | `MPOL_WEIGHTED_INTERLEAVE` (6.9) | "according to the weights in /sys/kernel/mm/mempolicy/weighted_interleave" — 4:7:9 oranı örneği | **ağırlıklı** oransal |

**Mod bayrakları:**

| # | Bayrak | Ne der (özgün) |
|---|---|---|
| 1 | `MPOL_F_NUMA_BALANCING` (5.12) | "When mode is MPOL_BIND, enable the kernel NUMA balancing ... If the flag isn't supported ... -1 is returned and errno is set to EINVAL." |
| 2 | `MPOL_F_RELATIVE_NODES` (2.6.26) | "node IDs that are relative to the set of node IDs allowed by the process's current cpuset." |
| 3 | `MPOL_F_STATIC_NODES` (2.6.26) | "physical node IDs. Linux will not remap the nodemask when the process moves to a different cpuset context" |

**Bağlayıcı kural satırları:**

| # | Kural (özgün) |
|---|---|
| 1 | "The policy is applied only when a new page is allocated for the thread. For anonymous memory this is when the page is first touched by the thread." — kota **yalnız yeni sayfada** geçerlidir |
| 2 | "The thread memory policy is preserved across an execve(2), and is inherited by child threads created using fork(2) or clone(2)." |
| 3 | "Memory policy is not remembered if the page is swapped out." — takas kota hafızasını siler |
| 4 | `EINVAL`: "mode is MPOL_DEFAULT and nodemask is nonempty, or mode is MPOL_BIND or MPOL_INTERLEAVE and nodemask is empty" — boşluk/çelişki kota hatasıdır |
| 5 | Repo: `set_mempolicy` · `mbind` · `MPOL_` → `*.php` **0 eşleşme** (2026-10-13 grep) |

### §3.6 cgroup v2 hiyerarşi kuralları — `cgroups(7)` (kural katmanı)

> **Sınır uyarısı:** Burada yalnız **hiyerarşi kuralları** (kural katmanı) taşınır; kota dosyası
> ayrıntısı `kaynak-dagilimi-surucu §1`'dedir (§1.2 #3 çizgisi korunur).
> Kaynak: https://man7.org/linux/man-pages/man7/cgroups.7.html · HTTP 200 · 2026-10-13

| # | Kural | Alıntı (özgün) |
|---|---|---|
| 1 | Birleşik hiyerarşi | "In cgroups v2, all mounted controllers reside in a single unified hierarchy." |
| 2 | İç süreç yok | ""Internal" processes are not permitted. With the exception of the root cgroup, processes may reside only in leaf nodes (cgroups that do not themselves contain child cgroups)." |
| 3 | Hassas kural | "a (nonroot) cgroup can't both (1) have member processes, and (2) distribute resources into child cgroups—that is, have a nonempty cgroup.subtree_control file." |
| 4 | Üst sınır mirası | "the limits placed on a cgroup at a higher level in the hierarchy cannot be exceeded by descendant cgroups." |
| 5 | Aktif kontrolör | "modified by writing strings to this file containing space-delimited controller names, each preceded by '+' (to enable a controller) or '-' (to disable a controller)" — `echo '+pids -memory' > x/y/cgroup.subtree_control` |
| 6 | Derinlik kotası | `cgroup.max.depth` — "An attempt to create a descendant whose nesting level exceeds the limit fails (mkdir(2) fails with the error EAGAIN)." · varsayılan `"max"` |
| 7 | Sayı kotası | `cgroup.max.descendants` — "An attempt to create more descendants than allowed by the limit fails (mkdir(2) fails with the error EAGAIN)." · varsayılan `"max"` |
| 8 | Boşluk bildirimi | `cgroup.events` → `populated 1|0` · "frozen 1|0" (5.2) — "key-value pairs ... providing state information about the cgroup" |

> **Kota okuması:** hiyerarşi = **üst sınırın aşağıya akması** (kural 4) + **yaprak disiplini**
> (kurallar 2–3). Her ikisi de "kota kime yazılır" sorusunun cevabıdır; repo'da karşılığı
> **yoktur** (§3.4 son satır).

## §4 G/Ç kuyrukları

### §4.1 Disk G/Ç — sırayı kim belirler

| # | Katman | Sıra/limit | Kaynak |
|---|---|---|---|
| 1 | Çekirdek IO kuyruğu | kuyruk disiplini (kısa: `-surucu §3`) | https://docs.kernel.org/block/index.html |
| 2 | Süreç IO öncelik sınıfı | `IOPRIO_CLASS_RT` > `IOPRIO_CLASS_BE` > `IOPRIO_CLASS_IDLE` | https://man7.org/linux/man-pages/man2/ioprio_set.2.html |
| 3 | Kullanıcı aracı | `ionice(1)` — sınıf + öncelik no | https://man7.org/linux/man-pages/man1/ionice.1.html |
| 4 | cgroup IO limiti | `io.max` — BPS/IOPS üst sınırı | https://docs.kernel.org/admin-guide/cgroup-v2.html |

### §4.2 IO öncelik sınıfları (ioprio_set(2) metni)

| # | Sınıf | No | Davranış (alıntı/özet) |
|---|---|---|---|
| 1 | `IOPRIO_CLASS_RT` | 1 | "real-time I/O class ... processes from this class are given first access to the disk every time" — en yüksek |
| 2 | `IOPRIO_CLASS_BE` | 2 | "best-effort scheduling class, which is the default for any process that hasn't set a specific I/O priority" |
| 3 | `IOPRIO_CLASS_IDLE` | — | en düşük; ionice(1): "only get disk time when no other program has asked for disk I/O for a defined grace period" |

> **Sayı yönü:** "a lower priority number means a higher priority level" — IO önceliğinde
> küçük sayı = yüksek öncelik. Nice ile **aynı** yön, cgroup `cpu.weight` ile **farklı** okunur.

### §4.3 Ağ ve kuyruk — bu dosyanın sınırı

| # | Konu | Burada mı? | Nereye |
|---|---|---|---|
| 1 | Ağ kuyruğu / socket buffer | ❌ | `isletim-sistemi-kernel` / `isletim-sistemi-api` |
| 2 | Rate-limit penceresi (uygulama katmanı) | ✅ yalnız **örnek** olarak | `-ornekler §1.4–§1.5` |
| 3 | `Retry-After` davranışı | ✅ yalnız **örnek** olarak | `-ornekler §1.6` |
| 4 | Disk IO disiplin detayı | ❌ | `kaynak-dagilimi-surucu §3` |

### §4.4 Uygulama katmanı ayrımı (kritik)

| Katman | Ne kotalar | Örnek | OS katmanı mı? |
|---|---|---|---|
| Çekirdek | CPU, bellek, IO, süreç | `cpu.max` · `memory.max` · `io.max` | ✅ evet |
| Çalışma zamanı (PHP-FPM vb.) | istek başına süre/süreç | ⚠️ repo'da yapılandırma **yok** | kısmen |
| Uygulama (`shared/src`) | istek başına hak, önbellek ömrü, HTTP zaman aşımı | `RateLimitMiddleware` 60/60 · `PageCacheAdapter` TTL 600 · `ServiceDefinition` timeout 5 | ❌ **hayır** |

> **Bağlayıcı uyarı:** `-ornekler.md`'deki her örnek bu tablonun **3. satırındandır**.
> "Repo'da rate limit var" cümlesi, "repo cgroup kullanıyor" anlamına **gelmez**.

## §5 Öncelik devralma (priority inheritance)

### §5.1 Kavram

| # | Terim | Tanım |
|---|---|---|
| 1 | Öncelik devralma | Düşük öncelikli bir süreç kilit tutuyorsa, onu bekleyen yüksek öncelikli süreç kilit sahibini **geçici olarak** kendi önceliğine çıkarır |
| 2 | Öncelik tersine çevrilme (priority inversion) | Devralma yoksa orta öncelikli süreç yüksek öncelikliyi dolaylı yoldan geciktirir |
| 3 | Tıkanma (starvation) | Düşük öncelikli işin sürekli geç kalması | kardeş: `zamanlama-scheduler-surucu §5` |
| 4 | Kilitlenme (deadlock) | Birbirini bekleyen iki süreç — öncelik devralma **çözüm değildir** | §5.3 |

### §5.2 Zincir (nasıl yayılır)

| # | Adım | Ne olur |
|---|---|---|
| 1 | Yüksek öncelikli iş `L`'yi bekler | bekleme başlar |
| 2 | `L`'yi tutan düşük öncelikli iş `A`'nın önceliği yükseltilir | devralma |
| 3 | `A` çalışır ve `L`'yi bırakır | gecikme `A`'nın çalışmasına indirgenir |
| 4 | `A` eski önceliğine döndürülür | zincir kapanır |
| 5 | Zincirleme devralma | `A` da başka bir kilit bekliyorsa devralma yayılır — **üst sınır gerekir** |

### §5.3 Repo durumu (dürüstlük)

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| 1 | Repo'da öncelik devralma kodu var mı? | `pcntl_|pthread_|sched_|affinity` grep → **0 eşleşme** | **YOK** |
| 2 | Repo'da kilit (mutex/lock) kullanan iş parçası kodu var mı? | iş parçası API'si yok (yukarıdaki grep) | **YOK** |
| 3 | Uygulama katmanında eşdeğer davranış? | `RateLimitMiddleware` 429 ile "bekle" der — **devralma değil, reddetme** | farklı mekanizma |
| 4 | Öncelik devralma bu projede gerekli mi? | tek iş parçalı istek modeli (PHP-FPM) → ⚠️ VERIFICATION REQUIRED | **UNKNOWN** |

## §6 Ölçüm (utilization · run queue)

### §6.1 Ne ölçülür

| # | Ölçüm | Tanım | Kaynak |
|---|---|---|---|
| 1 | CPU utilization | Doluluk oranı — çekirdek başına | `isletim-sistemi-observability` (toplama) |
| 2 | Run queue length | Kuyrukta bekleyen çalıştırılabilir iş sayısı | `zamanlama-scheduler-surucu §6` |
| 3 | Context switch latency | Geçiş gecikmesi | kardeş dosya |
| 4 | `cpu.stat` | `usage_usec` · `user_usec` · `system_usec` | https://docs.kernel.org/admin-guide/cgroup-v2.html |
| 5 | `proc(5)` | süreç başına `stat`/`status` satırları | kardeş envanter N-W19 · HTTP 200 |
| 6 | IO beklemesi | kuyrukta geçen süre | `kaynak-dagilimi-surucu §5.4` |

### §6.2 Ölçüm prensipleri (bağlayıcı)

| # | Prensip | Gerekçe |
|---|---|---|
| 1 | Ölçüm yoksa kota **kör** yazılır | `memory.max` yanlış seçilirse ya OOM ya israf |
| 2 | Ortalama yerine **tepe** (p95/p99) okunur | kota tepe anında kırılır |
| 3 | Ölçüm penceresi kota penceresiyle **eşleştirilir** | 60 sn rate-limit penceresi ≠ 1 saatlik ortalama |
| 4 | Ölçüm aleti ölçtüğü şeyi **adlandırır** | uygulama `hrtime` ölçer ≠ çekirdek run queue ölçer |

### §6.3 Repo'daki ölçüm (sınırlı)

| # | Dosya | Satır | Ne ölçülür | OS ölçümü mü? |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 · 46 | `hrtime(true)` ile middleware süresi (ms) | ❌ hayır |
| 2 | `.github/workflows/ci.yml` | 28 · 70 · 113 · 145 · 164 · 181 | `timeout-minutes` — iş başına süre kotası | ❌ hayır (CI) |
| 3 | `shared/src/Api/Registry/ServiceRegistry.php` | 104 | HTTP isteği `timeout` değeri | ❌ hayır |
| 4 | çekirdek sayaçları (`cpu.stat`, `proc(5)`) | — | — | ✅ evet — **repo'da yok** |

> **Ölçüm protokolü** `kaynak-dagilimi-surucu §5.4`'te PLANNED olarak tanımlıdır; burada
> yalnız **adı ve sırası** verilir, sayı **uydurulmaz**.

### §6.4 Ölçüm → kota kararı matrisi

| # | Gözlem | Olası neden | Doğru kola vuran bölüm |
|---|---|---|---|
| 1 | CPU doluluğu sürekli yüksek | ya kota yok ya `cpu.weight` yanlış | §2.5 #2–#3 |
| 2 | Bellek tepe anında OOM | `memory.high` tanımsız, doğrudan `memory.max` | §3.2 #1–#2 |
| 3 | Disk gecikmesi tepe yaparken CPU düşük | IO kuyruğu / sınıf | §4.1–§4.2 |
| 4 | Kuyrukta bekleyen iş sayısı artıyor | sıra politikası — **burası değil** | `zamanlama-scheduler` |
| 5 | Uygulama 429 döndürüyor | uygulama katmanı kotası (§4.4 #3) | `-ornekler §4` |
| 6 | CI işi kesiliyor | `timeout-minutes` kotası | `-ornekler §2.2` |

> **Yönlendirme kuralı:** Bu tablo **şebekedeki** belirtileri eşler; kök neden analizi
> `isletim-sistemi-observability` (toplama) ve `isletim-sistemi-kernel` (mekanizma) işidir.

### §6.5 Ölçümün resmi adları — `getrusage(2)` (repo'da yok)

> Kaynak: https://man7.org/linux/man-pages/man2/getrusage.2.html · HTTP 200 · 2026-10-13
> `getrusage()` "returns resource usage measures for `who`" — `RUSAGE_SELF` (çağıran süreç) ·
> `RUSAGE_CHILDREN` (bitmiş çocuklar) · `RUSAGE_THREAD` (Linux'a özgü).

| # | Alan | Tanım (özgün) | Repo |
|---|---|---|---|
| 1 | `ru_utime` | "total amount of time spent executing in user mode" (`timeval`) | ❌ |
| 2 | `ru_stime` | "total amount of time spent executing in kernel mode" | ❌ |
| 3 | `ru_maxrss` | "maximum resident set size used (in KiB)" | ❌ |
| 4 | `ru_minflt` | "page faults serviced without any I/O activity" | ❌ |
| 5 | `ru_majflt` | "page faults serviced that required I/O activity" | ❌ |
| 6 | `ru_inblock` | "number of times the filesystem had to perform input" | ❌ |
| 7 | `ru_oublock` | "number of times the filesystem had to perform output" | ❌ |
| 8 | `ru_nvcsw` | "voluntary context switches" — "voluntarily giving up the processor before its time slice was completed (usually to await availability of a resource)" | ❌ |
| 9 | `ru_nivcsw` | "involuntary context switches" — "higher priority process becoming runnable or because the current process exceeded its time slice" | ❌ |

**Dürüstlük ve kapsam notları:**

| # | Not (özgün) |
|---|---|
| 1 | "Not all fields are completed; unmaintained fields are set to zero by the kernel." — `ru_ixrss` · `ru_idrss` · `ru_isrss` · `ru_nswap` · `ru_msgsnd` · `ru_msgrcv` · `ru_nsignals` Linux'ta "currently unused" |
| 2 | "POSIX.1 specifies getrusage(), but specifies only the fields ru_utime and ru_stime." — POSIX kapsamı **iki alanla** sınırlıdır |
| 3 | Hatalar: `EFAULT` ("usage points outside the accessible address space") · `EINVAL` ("who is invalid") |
| 4 | Repo: `getrusage` · `RUSAGE_` · `ru_maxrss` · `ru_nvcsw` → `*.php` **0 eşleşme** (2026-10-13 grep) |

**Ölçüm yüzeyi karşılaştırması (bu dosyanın bağlayıcı matrisi):**

| # | Ölçüm yüzeyi | Ne verir | Katman | Repo |
|---|---|---|---|---|
| 1 | `getrusage(2)` | CPU/bellek/geçiş sayacı (süreç + parçacık) | OS | ❌ yok |
| 2 | `cpu.stat` (cgroup) | `usage_usec` · `user_usec` · `system_usec` | OS (grup) | ❌ yok |
| 3 | `hrtime(true)` (`MiddlewarePipeline.php:42`) | uygulama süresi (ms) | uygulama | ✅ §6.3 #1 |
| 4 | `timeout-minutes` (`ci.yml`) | iş başına üst süre (dk) | CI | ✅ §6.3 #2 |

> **Adlandırma kuralı (§6.2 #4'ün uygulaması):** repo'daki ölçüm **uygulama süresidir**;
> yukarıdaki 1–2 satırlarındaki sayaçların adı **asla** `hrtime` sonucu için kullanılmaz.

## §7 Komşu sınırı

| # | Komşu | Bırakılan | Alınan | Çizgi |
|---|---|---|---|---|
| 1 | `isletim-sistemi-kernel` | kota kuralı | kuyruk/kesme mekanizması | kural vs mekanizma |
| 2 | `zamanlama-scheduler` | kota = politika girdisi | sıralama kararı | ne kadar vs hangi sıra |
| 3 | `is-parcacigi-planlamasi` | kota iş **birimi** başına | birim tanımı/durumu | pay vs birim |
| 4 | `isletim-sistemi-core` | süreç kavramının kaynak okuması | süreç tanımı | ortak tip |
| 5 | `isletim-sistemi-runtime` | ölçüm adı | toplama/boru hattı | okuma vs toplama |
| 6 | `isletim-sistemi-security` | limit ihtiyacının tanımı | yetki modeli | kota vs yetki |
| 7 | `isletim-sistemi-observability` | eşik/uyarı eşiği | metrik üretimi | eşik vs akış |
| 8 | `isletim-sistemi-api` | uygulama kotası örneği | API sözleşmesi | örnek vs sözleşme |

### §7.1 Sınır ihlali kataloğu

| # | İhlal | Belirti | Düzeltme |
|---|---|---|---|
| 1 | cgroup ayrıntısını buraya yazmak | `-surucu §1` ile tekrar | taşı → `-surucu` |
| 2 | Repo TTL/RateLimit'i "OS kotası" diye sunmak | §4.4 ihlali | katmanı yaz |
| 3 | Sıra/algoritma tartışması | `zamanlama-scheduler` kapsamı | devret |
| 4 | Ölçüm sayısını uydurmak | kanıtsız sayı | `⚠️ VERIFICATION REQUIRED` |

## §8 Açık sorular

| # | Soru | Etiket | Etkilediği bölüm | Kapanış koşulu |
|---|---|---|---|---|
| Q3 | Kapsam OS kaynak dağıtımı mı, uygulama kotası mı? | `[UNKNOWN]` | §4.4 | üst otorite kararı — `isletim-sistemi-plan-kapsam.md §C.4` |
| Plan1 | Scheduling içeriği `kernel`/`runtime`'da mı, burada mı? | `[UNKNOWN]` | §2.1 · §6.1 | komşu okuma + teyit — `zamanlama-scheduler-anayasa §7` |
| Q-kd-1 | Üretimde cgroup kotası uygulanıyor mu? | `⚠️` | §3.4 | deploy envanteri okunması |
| Q-kd-2 | Hedef cihaz çekirdek/topoloji sayısı? | `UNKNOWN` | §2.4 | `K001-donanım` (kapsam dışı) |
| Q-kd-3 | Öncelik devralma tek iş parçalı modelde gerekli mi? | `⚠️` | §5.3 | mimari onay |

## §9 Kaynakça

### §9.1 Web doğrulama (2026-10-13 · GET · envanter kesişimi 0)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | cgroup v2 kota dosyaları (`cpu.max` · `memory.max` · `memory.high` · `io.max` · `cpu.stat`) | https://docs.kernel.org/admin-guide/cgroup-v2.html | 2026-10-13 | ✅ HTTP 200 |
| 2 | `RLIMIT_*` kümesi ve `RLIMIT_NICE` dönüşümü | https://man7.org/linux/man-pages/man2/getrlimit.2.html | 2026-10-13 | ✅ HTTP 200 |
| 3 | nice öncelik girdisi ve yetki sınırı | https://man7.org/linux/man-pages/man2/nice.2.html | 2026-10-13 | ✅ HTTP 200 |
| 4 | `getpriority()` negatif nice döndürebilir | https://man7.org/linux/man-pages/man2/setpriority.2.html | 2026-10-13 | ✅ HTTP 200 |
| 5 | IO öncelik sınıfları (`RT` · `BE` · `IDLE`) | https://man7.org/linux/man-pages/man2/ioprio_set.2.html | 2026-10-13 | ✅ HTTP 200 |
| 6 | `ionice(1)` davranış tanımı (idle → disk boşluğu) | https://man7.org/linux/man-pages/man1/ionice.1.html | 2026-10-13 | ✅ HTTP 200 |
| 7 | NUMA (eşit olmayan bellek erişimi) | https://docs.kernel.org/mm/numa.html | 2026-10-13 | ✅ HTTP 200 (kardeş N-W04 teyidi) |
| 8 | blok katmanı genel bakış (IO kuyruğu) | https://docs.kernel.org/block/index.html | 2026-10-13 | ✅ HTTP 200 |
| 9 | `proc(5)` süreç başına kaynak satırları | https://man7.org/linux/man-pages/man5/proc.5.html | 2026-10-13 | ✅ HTTP 200 (kardeş N-W19 teyidi) |
| 10 | `sched(7)` zamanlama kavramları | kardeş envanter R-7 | 2026-10-13 | ✅ yeniden kullanım |
| 11 | `sysconf(3p)` çekirdek sayısı sorgusu | kardeş envanter N-W16 | 2026-10-13 | ✅ yeniden kullanım |
| 12 | cgroup v2 hiyerarşi kuralları ("no internal processes" · `cgroup.subtree_control` · `cgroup.max.depth`) — §3.6 | https://man7.org/linux/man-pages/man7/cgroups.7.html | 2026-10-13 | ✅ HTTP 200 (kardeş R-12 teyidi) |
| 13 | NUMA yerleşim kipleri (`MPOL_*`) ve mod bayrakları — §3.5 | https://man7.org/linux/man-pages/man2/set_mempolicy.2.html | 2026-10-13 | ✅ HTTP 200 · içerik okundu |
| 14 | iş bağlama maskesi ve `errno` kümesi (`EFAULT` · `EINVAL` · `EPERM` · `ESRCH`) — §2.7 | https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html | 2026-10-13 | ✅ HTTP 200 (kardeş R-5 teyidi) |
| 15 | süreç ölçüm alanı kümesi (`ru_*`) — §6.5 | https://man7.org/linux/man-pages/man2/getrusage.2.html | 2026-10-13 | ✅ HTTP 200 (kardeş core-dogrulama teyidi) |

> **Yeniden kullanım notu (2026-10-13):** 4 URL bu görevde içerikleri yeniden okunarak alıntılanmıştır;
> 3'ü kardeş envanterde (`isletim-sistemi-plan-dogrulama.md` R-5 · R-12 ve
> `isletim-sistemi-core-dogrulama.md`) zaten HTTP 200 kayıtlıdır — yeni alan adı **eklenmemiştir**.

### §9.2 Repo kanıtları (bu dosyada kullanılan)

| # | Dosya | Satır | Ne görünür |
|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 · 46 | `hrtime(true)` süre ölçümü |
| 2 | `shared/src/Api/Registry/ServiceRegistry.php` | 104 | HTTP `timeout` |
| 3 | `shared/src/Cache/PageCacheAdapter.php` | 11 | `ttlPage = 600` (uygulama kotası) |
| 4 | `.github/workflows/ci.yml` | 28 · 70 · 113 · 145 · 164 · 181 | `timeout-minutes` |

### §9.3 İlişkili vault dosyaları

| Dosya | Bağ |
|---|---|
| `kaynak-dagilimi-surucu.md` | mekanizma ayrıntısı (§1–§5) |
| `kaynak-dagilimi-ornekler.md` | çalışan repo kanıtı + dürüstlük defteri |
| `zamanlama-scheduler-anayasa.md` | politika girdisi olarak kota |
| `is-parcacigi-planlamasi-anayasa.md` | kota iş birimi başına |
| `isletim-sistemi-plan-kapsam.md` | §B.4/§B.6 bölüm planı + §C.4 Q3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
