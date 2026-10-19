---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi
children: []
depends_on:
  - isletim-sistemi-kernel
  - zamanlama-scheduler
evidence:
  - "https://docs.kernel.org/admin-guide/cgroup-v2.html"
  - "https://docs.kernel.org/block/index.html"
  - "https://man7.org/linux/man-pages/man2/ioprio_set.2.html"
  - "https://docs.kernel.org/mm/numa.html"
  - "shared/src/Middleware/MiddlewarePipeline.php:42"
  - "shared/src/Api/Registry/ServiceRegistry.php:56"
validation:
  - "2026-10-13: 5 URL HTTP 200 · cgroup-v2/block sayfa içeriği okunarak alıntılandı"
  - "2026-10-13: §6 repo satırları Read ile birebir teyit edildi"
open_questions:
  - "Q-kd-1 — cgroup/NUMA kotası üretimde uygulanıyor mu? [⚠️ VERIFICATION REQUIRED]"
  - "Plan1 — kuyruk disiplini uygulaması kimde? [UNKNOWN → isletim-sistemi-kernel]"
group: mimari
---

# kaynak-dagilimi — Sürücü

> **Tek cümle:** Bu dosya `kaynak-dagilimi` alt-dalının **sürücüsüdür** — kota mekanizmasının
> dosya/parametre ayrıntısını, NUMA yerleşimini, IO kuyruğu disiplinlerini, öncelik devralma
> zincirini ve ölçüm sırasını **adım adım** kurar; kural `-anayasa.md`'de, çalışan hâl
> `-ornekler.md`'dedir.

## §0 ELI15

| Soru | Kısa yanıt | Bölüm |
|---|---|---|
| Kota nereye yazılır? | cgroup v2 kontrol dosyalarına (`cpu.max`, `memory.max`, `io.max`) | §1 |
| Bellek sınırı nasıl uygulanır? | önce `memory.high` (throttle), sonra `memory.max` (hard) | §1.3 |
| NUMA'da ne yapılır? | belleği/ işi yerel düğüme bağlama politikası seçilir | §2 |
| Disk sırası nasıl bölünür? | IO öncelik sınıfı + kuyruk disiplini | §3 |
| Yüksek öncelik neden bekler? | kilit düşük öncelikteyse — devralma zinciri | §4 |
| Ne ölçülür? | utilization · run queue · latency · `cpu.stat` | §5 |
| Repo'da karşılığı? | yalnız uygulama katmanı sayaçları (`hrtime`, `timeout`) | §6 |

## §1 cgroup kota ayrıntısı

### §1.1 Kontrol dosyaları — harita

| # | Alan | Dosya | Tür | Varsayılan (resmi metin) |
|---|---|---|---|---|
| 1 | CPU (mutlak) | `cpu.max` | oku-yaz, 2 değer | `"max 100000"` |
| 2 | CPU (oransal) | `cpu.weight` | oku-yaz | dokümanda varsayılan `"max"` (Limitler bloğu) |
| 3 | CPU (istatistik) | `cpu.stat` | oku-yalnız | `usage_usec` · `user_usec` · `system_usec` |
| 4 | Bellek (hard) | `memory.max` | oku-yaz | `"max"` |
| 5 | Bellek (throttle) | `memory.high` | oku-yaz | `"max"` |
| 6 | IO | `io.max` | oku-yaz | BPS/IOPS üst sınırı |
| 7 | Kök cgroup | — | — | tek kök; alt cgroup `mkdir` ile oluşturulur |

> Kaynak: https://docs.kernel.org/admin-guide/cgroup-v2.html · HTTP 200 · 2026-10-13
> (sayfa içeriği getirilip ilgili parçalar okunarak alıntılanmıştır).

### §1.2 CPU kotası — adım adım

| # | Adım | Komut/dosya | Not |
|---|---|---|---|
| 1 | cgroup oluştur | alt dizin `mkdir` | "Initially, only the root cgroup exists" (cgroup-v2) |
| 2 | Kontrolcüyü etkinleştir | denetleyici yazımı | ⚠️ dağıtıma bağlı — bu repo'da **yok** |
| 3 | Bant genişliği yaz | `cpu.max` → `$MAX $PERIOD` | "the group may consume up to $MAX in each $PERIOD duration" |
| 4 | Oransal pay yaz | `cpu.weight` | "proportionally distributes CPU cycles to active children" |
| 5 | Sınırsız bırak | `"max"` | `"max" for $MAX indicates no limit` |
| 6 | İzle | `cpu.stat` | mikrosaniye cinsinden |

### §1.3 Bellek kotası — üç sütun

| # | Sütun | Eşik | Aşım davranışı (resmi metin) | Sıra |
|---|---|---|---|---|
| 1 | `memory.high` | throttle | "the processes of the cgroup are throttled and put under heavy reclaim pressure" — OOM **çağırmaz** | 1 |
| 2 | `memory.max` | hard limit | "This is the main mechanism to limit memory usage of a cgroup" | 2 |
| 3 | OOM | çekirdek | `memory.high` asla OOM çağırmadığından **hard** sınır son savunma hattıdır | 3 |

> **Tasarım kuralı:** Önce `memory.high` (yumuşak), sonra `memory.max` (sert). Tek sütunlu
> kota, tepe anında ya OOM'a ya da sınırsız büyümeye götürür.

### §1.4 IO kotası

| # | Dosya | Ne sınırlar | Resmi metin |
|---|---|---|---|
| 1 | `io.max` | cgroup'ın bir IO aygıtında tüketebileceği **en yüksek BPS/IOPS** | "limits the maximum BPS and/or IOPS that a cgroup can consume on an IO device" |

### §1.5 Bu kotalar repo'da var mı? (dürüstlük)

| # | Kontrol | Yöntem | Sonuç |
|---|---|---|---|
| 1 | `cgroup` / `cpu.max` / `memory.max` geçen PHP/yapılandırma | repo taraması | **0 kanıt** |
| 2 | Deploy envanterinde cgroup tanımı | bu oturumda okunmadı | **UNKNOWN** |
| 3 | Sonuç | — | ⚠️ VERIFICATION REQUIRED — `-ornekler §5` defterine işlenmiştir |

### §1.6 Kota yazma sırası (uygulama listesi)

| # | Sıra | İş | Tamamlanma ölçütü |
|---|---|---|---|
| 1 | Önce ölç | `cpu.stat` / ilgili sayaç oku | taban çizgisi kaydı var |
| 2 | Grubu ayır | `mkdir` ile cgroup | dizin oluştu |
| 3 | Belleği koru | önce `memory.high`, sonra `memory.max` | iki dosya yazıldı |
| 4 | CPU'yu böl | `cpu.max` **veya** `cpu.weight` — hangisi kota olarak seçildiğine **karar verilip** yazılır | seçim kaydı var |
| 5 | IO'yu sınırla | `io.max` (BPS/IOPS) | dosya yazıldı |
| 6 | İzle | `cpu.stat` okuma döngüsü | µs üçlüsü kayıt altında |
| 7 | Aşımı doğrula | §1.2–§1.4'e göre davranış teyidi | §5.3 sırası işletildi |

> **Sıra neden önemli:** 3. adım atlanıp yalnız `memory.max` yazılırsa yumuşak throttle
> yoktur; 4. adımda hangi mekanizmanın kota sayıldığı **yazılmazsa** ölçüm (§5.1) yanlış
> yoruma açık kalır — bu nedenle seçim **açıkça kaydedilir**.

### §1.7 cgroup yaşam döngüsü ve hata kataloğu — `cgroups(7)` resmi metni

> Kaynak: https://man7.org/linux/man-pages/man7/cgroups.7.html · HTTP 200 · 2026-10-13
> (sayfa içeriği getirilip ilgili parçalar okunarak alıntılanmıştır). Bu bölüm
> §1.2–§1.4'ün **adım ve hata** karşılığıdır: kota **değeri** değil, kota
> **yazımının kuralları** buradadır.

| # | Adım | Resmi kural (alıntı) | Sonuç / hata |
|---|---|---|---|
| 1 | Başlangıç | "A cgroup filesystem initially contains a single root cgroup, '/', which all processes belong to" | tek kök |
| 2 | Oluşturma | "A new cgroup is created by creating a directory in the cgroup filesystem" (`mkdir /sys/fs/cgroup/cpu/cg1`) | dizin oluşmazsa cgroup yok |
| 3 | Süreç taşıma | `echo $$ > …/cgroup.procs` | "Only one PID at a time should be written to this file" |
| 4 | `0` yazmak | "Writing the value 0 to a cgroup.procs file causes the writing process to be moved to the corresponding cgroup" | yazan süreç taşınır |
| 5 | Thread | "When writing a PID into the cgroup.procs, all threads in the process are moved into the new cgroup at once" | parçacık düzeyinde bölünme yok |
| 6 | Üyelik | "Within a hierarchy, a process can be a member of exactly one cgroup" | ikinci üyelik birinciyi düşürür |
| 7 | Devir | "automatically removes it from the cgroup of which it was previously a member" | elle çıkış gerekmez |
| 8 | Silme | "it must first have no child cgroups and contain no (nonzombie) processes" | dolu cgroup silinemez |
| 9 | Denetleyici açma | `echo '+pids -memory' > x/y/cgroup.subtree_control` | — |
| 10 | Hata `ENOENT` | "An attempt to enable a controller that is not present in cgroup.controllers leads to an ENOENT error" | tanımsız denetleyici açılamaz |
| 11 | Hata `EAGAIN` | `cgroup.max.depth` · `cgroup.max.descendants` aşıldığında "mkdir(2) fails with the error EAGAIN" | iç içe derinlik/yaşayan sayı kotası |
| 12 | Hata `EPERM` | `nsdelegate` altında "Writes to controller interface files in the root directory of the namespace will fail with the error EPERM" | yetki sınırı |
| 13 | Hata `ENOENT` (göç) | "Attempts to migrate processes across the namespace boundary are denied (with the error ENOENT)" | ad alanı sınırı |
| 14 | İç süreç kuralı | "processes may reside only in leaf nodes (cgroups that do not themselves contain child cgroups)" | iç cgroup'ta süreç barınamaz |
| 15 | Hiyerarşi üstünlüğü | "the limits placed on a cgroup at a higher level in the hierarchy cannot be exceeded by descendant cgroups" | alt cgroup üst kotayı aşamaz |
| 16 | Ölçüm dosyası | `cgroup.stat` → `nr_descendants` · `nr_dying_descendants` | §5.4'e aday ikinci sayaç |
| 17 | Ölü cgroup | "A process can't be made a member of a dying cgroup, and a dying cgroup can't be brought back to life" | geri dönüş yok |
| 18 | Thread mode | "As at Linux 4.19, the following controllers are threaded: cpu, perf_event, and pids" | yalnız bu üçü parçacık düzeyinde |
| 19 | Süreç sayısı kotası | `pids` denetleyicisi: "limiting the number of process that may be created in a cgroup (and its descendants)" | bellek/CPU dışı dördüncü kota yüzeyi |
| 20 | Repo durumu | bu tablonun tamamı çekirdek + kullanıcı aracı yüzeyidir | repo'da **0 kanıt** (§1.5 · §6.4) |

> **Kural:** 10–13. satırlar **hata kataloğu**dur — kota yazımının başarısızlık
> yüzeyidir. Bunları üretimde görmek için önce §1.5'teki envanterin kapanması gerekir
> (Q-kd-1 ⚠️ VERIFICATION REQUIRED); envanter kapalıysa bu tablo **tarif** olarak kalır.

## §2 NUMA atribütleri ve yerleşim

### §2.1 Kavram

| # | Terim | Ne anlama gelir | Kaynak |
|---|---|---|---|
| 1 | NUMA düğümü | Belleğin "yerel" olduğu fiziksel düğüm | https://docs.kernel.org/mm/numa.html |
| 2 | Yerel erişim | Aynı düğümden okuma/yazma — hızlı | aynı |
| 3 | Uzak erişim | Başka düğümden erişim — yavaş | aynı |
| 4 | Yerleşim politikası | Belleğin hangi düğümde açılacağı | aynı |
| 5 | İş bağlama | İşin hangi düğüm/çekirdekte koşacağı | `is-parcacigi-planlamasi` (komşu) |

### §2.2 Politika seçenekleri

| # | Yaklaşım | Avantaj | Dezavantaj | Repo durumu |
|---|---|---|---|---|
| 1 | Yerel yerleşim (local) | düşük gecikme | düğüm dolarsa uzak tahsis | ❌ yok |
| 2 | Dengeleme (interleave) | yük dağılımı | yerel avantaj kaybolur | ❌ yok |
| 3 | Öncelik/puantaj (preferred) | belirli düğüm tercihi | tek düğüm şişebilir | ❌ yok |
| 4 | İş bağlama (affinity) | öngörülebilir dağılım | esneklik kaybı | ❌ yok (grep → 0) |

> **Dürüstlük:** Bu tablo **seçenek** tablosudur; repo'da **hiçbiri uygulanmamıştır**
> (0 kanıt). Seçim, hedef donanım bilgisiyle (`K001-donanım`, kapsam dışı) yapılır.

### §2.3 Tek düğümlü sistemlerde ne olur?

| # | Durum | Sonuç |
|---|---|---|
| 1 | Tek NUMA düğümü | §2.2 seçeneklerinin **tamamı** etkisiz/boş |
| 2 | Çok düğümlü sunucu | yerleşim ölçülemezse §6.4'teki "ölçmeden yazma" kuralı devreye girer |
| 3 | Bu proje | hedef cihaz topolojisi **UNKNOWN** (kapsam dışı) |

### §2.4 Bellek yerleşim kipleri — `set_mempolicy(2)` resmi kümesi

> Kaynak: https://man7.org/linux/man-pages/man2/set_mempolicy.2.html · HTTP 200 · 2026-10-13
> §2.2'deki üç satırlık "yaklaşım" listesi, bu sayfadaki **tam resmi kip kümesinin**
> sadeleşmiş hâlidir; bu bölüm kipi eksiksiz taşır.

| # | Kip | Ne yapar (resmi metin) | §2.2 karşılığı |
|---|---|---|---|
| 1 | `MPOL_DEFAULT` | "any nondefault thread memory policy be removed, so that the memory policy 'falls back' to the system default policy … 'local allocation'" | yerel yerleşim |
| 2 | `MPOL_BIND` | "a strict policy that restricts memory allocation to the nodes specified in nodemask" — "Pages will not be allocated from any node not specified in the nodemask" | — (sıkı bağlama) |
| 3 | `MPOL_INTERLEAVE` | "interleaves page allocations across the nodes … This optimizes for bandwidth instead of latency" | dengeleme |
| 4 | `MPOL_WEIGHTED_INTERLEAVE` | "according to the weights in /sys/kernel/mm/mempolicy/weighted_interleave" — örnek: "in a 4:7:9 ratio" | dengeleme (ağırlıklı) |
| 5 | `MPOL_PREFERRED` | "sets the preferred node for allocation … fall back to 'near by' nodes if the preferred node is low on free memory" | öncelik/puantaj |
| 6 | `MPOL_PREFERRED_MANY` | "It differs from MPOL_PREFERRED in that it accepts a set of nodes versus a single node" | öncelik (küme) |
| 7 | `MPOL_LOCAL` | "the memory is allocated on the node of the CPU that triggered the allocation (the 'local node')" | yerel yerleşim |

| # | Bağlayıcı kural (alıntı) | Sonuç |
|---|---|---|
| 1 | "A NUMA machine has different memory controllers with different distances to specific CPUs. The memory policy defines from which node memory is allocated for the thread." | §2.1 #1–#4'ün doğrudan kaynağı |
| 2 | "The policy is applied only when a new page is allocated for the thread. For anonymous memory this is when the page is first touched by the thread." | kip **sonradan** değişse de eski sayfalar **değişmez** |
| 3 | "The thread memory policy is preserved across an execve(2), and is inherited by child threads created using fork(2) or clone(2)." | miras |
| 4 | "Memory policy is not remembered if the page is swapped out." | swap sonrası unutulur |
| 5 | Hata yüzeyi: `EFAULT` · `EINVAL` · `ENOMEM` — "mode is MPOL_BIND or MPOL_INTERLEAVE and nodemask is empty" dahil | kip ↔ nodemask eşleşmezse reddedilir |

> **Dürüstlük:** kiplerin **hiçbiri** bu depoda çağrılmamıştır (`numa` · `mbind` →
> §6.4 #3'te 0 eşleşme). Bu tablo bir **seçim kataloğu**dur; §2.2'nin "Repo durumu ❌"
> sütunu bu ek kipler için de **değişmez**. Kip seçimi hedef donanıma bağlıdır
> (`K001-donanım`, kapsam dışı).

### §2.5 İş bağlama (affinity) — `sched_setaffinity(2)` resmi yüzeyi

> Kaynak: https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html · HTTP 200 · 2026-10-13
> §2.2 #4'ün ("İş bağlama (affinity)") çağrı yüzeyi; bu dosyada **adı** geçiyordu,
> burada resmi tanımı ve hatasıyla taşınır.

| # | Konu | Resmi metin (alıntı) |
|---|---|---|
| 1 | Tanım | "A thread's CPU affinity mask determines the set of CPUs on which it is eligible to run." |
| 2 | Yarar | "by dedicating one CPU to a particular thread … it is possible to ensure maximum execution speed for that thread" |
| 3 | Önbellek | "avoids the performance cost caused by the cache invalidation that occurs when a thread ceases to execute on one CPU and then recommences execution on a different CPU" |
| 4 | Göç | "If the thread specified by pid is not currently running on one of the CPUs specified in mask, then that thread is migrated to one of the CPUs specified in mask" |
| 5 | Kesişim kuralı | "the set of CPUs on which the thread will actually run is the intersection of the set specified in the mask argument and the set of CPUs actually present on the system" |
| 6 | cpuset üstünlüğü | "The system may further restrict the set of CPUs … if the 'cpuset' mechanism … is being used. These restrictions … are silently imposed by the kernel." |
| 7 | Miras | "A child created via fork(2) inherits its parent's CPU affinity mask. The affinity mask is preserved across an execve(2)." |
| 8 | Çekirdek izolasyonu | "The isolcpus boot option can be used to isolate one or more CPUs at boot time, so that no processes are scheduled onto those CPUs." |

| # | Hata | Koşul (alıntı) |
|---|---|---|
| 1 | `EINVAL` | "mask contains no processors that are currently physically on the system and permitted to the thread according to any restrictions that may be imposed by cpuset cgroups" |
| 2 | `EINVAL` | "cpusetsize is smaller than the size of the affinity mask used by the kernel" |
| 3 | `EPERM` | "The caller needs an effective user ID equal to the real user ID … or it must possess the CAP_SYS_NICE capability" |
| 4 | `ESRCH` | "The thread whose ID is pid could not be found" |
| 5 | `EFAULT` | "A supplied memory address was invalid" |

> **Çekirdek sayısı sorgusu — resmi liste:** "inspecting the contents of /proc/cpuinfo;
> using sysconf(3) to obtain the values of the _SC_NPROCESSORS_CONF and
> _SC_NPROCESSORS_ONLN parameters; and inspecting the list of CPU directories under
> /sys/devices/system/cpu/" — §2.3 #1'in (kardeş envanter N-W16) tam kaynağı.
> **Repo durumu:** `affinity` → 0 eşleşme (§6.4 #1); bu yüzeyin tamamı bu depoda
> **uygulanmamıştır** ve hedef topoloji `K001-donanım` kapsamındadır.

## §3 IO kuyruğu disiplinleri

### §3.1 Çekirdek blok katmanı — doküman haritası (2026-10-13 okundu)

| # | Başlık (sayfada listelenen) | Ne hakkında |
|---|---|---|
| 1 | Multi-Queue Block IO Queueing Mechanism (`blk-mq`) | çoklu kuyruk mekanizması |
| 2 | BFQ (Budget Fair Queueing) | bütçe adilliği temelli disiplin |
| 3 | Kyber I/O scheduler tunables | kyber disiplini ayarları |
| 4 | Deadline IO scheduler tunables | son teslim disiplini ayarları |
| 5 | Block io priorities | blok IO önceliği |
| 6 | Switching Scheduler | disiplin değiştirme |
| 7 | Block layer statistics in `/sys/block/<dev>/stat` | ölçüm sayacı |

> Kaynak: https://docs.kernel.org/block/index.html · HTTP 200 · 2026-10-13
> **Sınır:** Bu dosya disiplinlerin **adını** taşır; çalışma ilkesi ayrıntısı
> `isletim-sistemi-kernel` içindir (Plan1 `[UNKNOWN]`).

### §3.2 Süreç tarafı — IO öncelik sınıfı

| # | Sınıf | No | Sıra davranışı |
|---|---|---|---|
| 1 | `IOPRIO_CLASS_RT` | 1 | "processes from this class are given first access to the disk every time" |
| 2 | `IOPRIO_CLASS_BE` | 2 | varsayılan; "class data (priority) determines how much I/O bandwidth" |
| 3 | `IOPRIO_CLASS_IDLE` | — | yalnız başka istek olmadığında disk süresi (ionice(1)) |

> Kaynak: https://man7.org/linux/man-pages/man2/ioprio_set.2.html +
> https://man7.org/linux/man-pages/man1/ionice.1.html · HTTP 200 · 2026-10-13

### §3.3 Üç katmanın birlikte okunması

| # | Katman | Neyi belirler | Örnek |
|---|---|---|---|
| 1 | Kuyruk disiplini | sıra algoritması | BFQ · kyber · deadline |
| 2 | IO öncelik sınıfı | bu sürecin kuyruktaki **önceliği** | `IOPRIO_CLASS_*` |
| 3 | cgroup `io.max` | bu grubun **toplam** tüketim tavanı | BPS/IOPS |

> **Ayrım:** 1 ve 2 *sırayı*, 3 *tüketimi* sınırlar — anayasa §2.5'teki CPU ayrımının G/Ç eşdeğeri.

### §3.4 Tanı haritası — belirti hangi katmana ait?

| # | Belirti | Önce bakılacak katman | Doğru kol | Yanlış kol |
|---|---|---|---|---|
| 1 | Disk gecikmesi yüksek, CPU düşük | blok kuyruğu (§3.1) | disiplin/`io.max` | `cpu.max` |
| 2 | CPU sürekli dolu, disk normal | CPU kuyruğu (anayasa §2.5) | `cpu.max`/`cpu.weight` | IO sınıfı |
| 3 | Tek istek yavaş, sistem boş | uygulama `timeout` (§3.6 #3) | `ServiceDefinition` timeout | cgroup |
| 4 | Belirli istemci kısıtlanıyor | uygulama rate-limit (§3.6 #2) | 60/60 penceresi | IO sınıfı |
| 5 | CI işi yarıda kesiliyor | CI bütçesi (§3.6 #4) | `timeout-minutes` | üretim kotası |
| 6 | Bellek tepe anında süreç sonu | bellek (anayasa §3.2) | `memory.high` → `memory.max` | CPU kotası |

> **Kural:** Yanlış katmana vurulan kota **semptomu** gizler. Bu tablo, `-anayasa §4.4`
> katman ayrımının **teşhis** hâlidir.

### §3.5 Disiplin × sınıf × kota — seçim matrisi

| # | İhtiyaç | Hangi kol | Örnek değer biçimi | Kaynak |
|---|---|---|---|---|
| 1 | Toplam G/Ç tavanı | cgroup `io.max` | BPS/IOPS | cgroup-v2 (§1.4) |
| 2 | İşin diske önce erişimi | `IOPRIO_CLASS_RT` | sınıf no | ioprio_set(2) |
| 3 | "boşken yaz" (yedek iş) | `IOPRIO_CLASS_IDLE` | sınıf | ionice(1) |
| 4 | Adil dağılım | kuyruk disiplini (BFQ · kyber · deadline) | çekirdek yapılandırması | block/index |
| 5 | Sıra **algoritması** değişimi | `Switching Scheduler` | çekirdek | block/index |
| 6 | Ölçüm | `/sys/block/<dev>/stat` | sayaç | block/index |

> **Kapsam çizgisi:** 4–6. satırların **uygulaması** `isletim-sistemi-kernel` içindir;
> bu dosya yalnız haritayı ve doğru kolu **seçtirir** (Plan1 `[UNKNOWN]`).

### §3.6 Uygulama katmanı G/Ç örneği — dürüst okuma

| # | Örnek | Dosya:Satır | Ne yapar | OS G/Ç'si mi? |
|---|---|---|---|---|
| 1 | Sayfa cache TTL 600 | `PageCacheAdapter.php:11` | disk/DB okumasını tekrarlar | ❌ |
| 2 | Rate-limit 60/60 | `RateLimitMiddleware.php:22–23` | istek sayısını sınırlar | ❌ |
| 3 | `Retry-After` | `RateLimitMiddleware.php:40` | istemciyi bekletir | ❌ |
| 4 | Sağlık denetimi timeout 5 sn | `ServiceRegistry.php:104` | ağ bekleme sınırı | ❌ |

## §4 Priority inheritance zinciri

### §4.1 Zincir — adım adım

| # | Adım | Olay | Kontrol noktası |
|---|---|---|---|
| 1 | `H` (yüksek öncelik) `L`'yi bekler | bekleme başlar | gecikme ölç |
| 2 | `A` (düşük öncelik) `L`'yi tutar | devralma tetiklenir | öncelik yükseldi mi? |
| 3 | `A` çalışır | bekleme `A`'nın çalışmasına iner | tepe gecikme |
| 4 | `L` bırakılır | `A` eski önceliğine döner | zincir kapanır |
| 5 | `A` da `L2` bekliyor | devralma **yayılır** | üst sınır gerekli (§4.2) |

### §4.2 Yayılım ve sınırlar

| # | Risk | Sonuç | Önlem |
|---|---|---|---|
| 1 | Zincirsiz devralma | sonsuz yayılım | devralma zinciri üst sınırı |
| 2 | Devralma + tersine çevirme yok | dolaylı gecikme (priority inversion) | her beklemede devralma |
| 3 | Devralma + kilitlenme | deadlock | kilit sırası / timeout — **devralma çözüm değildir** |
| 4 | Ölçülmeyen zincir | gizli tepe gecikme | §5.3 ölçümü |

### §4.3 Repo durumu (dürüstlük)

| # | Soru | Kanıt | Sonuç |
|---|---|---|---|
| 1 | İş parçası / süreç öncelik kodu var mı? | `pcntl_|pthread_|sched_|affinity` → **0 eşleşme** | YOK |
| 2 | Kilit/kuyruk kitaplığı var mı? | iş parçası API'si yok | YOK |
| 3 | Eşdeğer uygulama davranışı? | 429 ile reddetme = **kuyruğa almama**, devralma değil | farklı kavram |
| 4 | Gereklilik | tek iş parçalı istek modeli → ⚠️ VERIFICATION REQUIRED | UNKNOWN |

## §5 Ölçüm

### §5.1 Utilization

| # | Sayaç | Kaynak | Birim |
|---|---|---|---|
| 1 | `usage_usec` | cgroup `cpu.stat` | µs |
| 2 | `user_usec` | cgroup `cpu.stat` | µs |
| 3 | `system_usec` | cgroup `cpu.stat` | µs |
| 4 | Uygulama süresi | `MiddlewarePipeline` `hrtime(true)` | ms |

> 1–3 repo'da **yok** (§6.4). 4 repo'da **var** ama OS ölçümü değildir.

### §5.2 Run queue

| # | Ölçüm | Tanım | Nereden | Repo |
|---|---|---|---|---|
| 1 | Kuyruk uzunluğu | çalıştırılabilir bekleyen iş sayısı | çekirdek sayacı | ❌ |
| 2 | Bekleme süresi | işin kuyrukta geçirdiği süre | çekirdek sayacı | ❌ |
| 3 | Bağlam değiştirme gecikmesi | geçiş latency | kardeş `-surucu §6` | ❌ |
| 4 | Uygulama kuyruğu süresi | middleware/atama süresi | `hrtime` | ✅ (§5.1 #4) |

### §5.3 Latency ölçümü — sıra

| # | Adım | Ne ölçülür | Çıktı |
|---|---|---|---|
| 1 | Taban çizgisi | boş sistemde gecikme | µs |
| 2 | Yük altında | tepe (p95/p99) | µs |
| 3 | Kota altında | `cpu.max` yazıldıktan sonra | µs |
| 4 | Aşım anı | kota kırıldığında davranış | throttled mi, reddedildi mi |

> Bu tablo **tariftir**; ölçüm **sayısı yoktur** ve uydurulmaz (anayasa §6.3).

### §5.4 Ölçüm protokolü (PLANNED)

| # | Adım | Alet | Çıktı | Durum |
|---|---|---|---|---|
| 1 | cgroup sayacı okuma | `cpu.stat` | µs üçlüsü | PLANNED (repo'da yok) |
| 2 | Süreç kaynağı okuma | `proc(5)` `stat`/`status` | satır listesi | PLANNED |
| 3 | Uygulama süresi | `hrtime` logu | ms | ✅ var (§6.1) |
| 4 | Eşik karşılaştırma | anayasa §6.4 matrisi | karar | PLANNED |
| 5 | CI zaman bütçesi | `timeout-minutes` | dakika | ✅ var (§6.2) |

### §5.5 Eşik yoksa ne yapılır (bağlayıcı kural)

| # | Durum | Yapılacak | Yapılmayacak |
|---|---|---|---|
| 1 | Ölçüm var, eşik yok | taban çizgisini yaz → üst otoriteye **onaylat** | eşik **uydurma** |
| 2 | Ölçüm yok, kota isteniyor | önce §5.4 adım 1–3 | kota yazma |
| 3 | Tek platform ölçümü | "bu ortamda" diye **sınırlı** raporla | genel diye sunma |
| 4 | Uygulama ölçümü (`hrtime`) | "uygulama katmanı" diye adlandır | çekirdek ölçümü sanma |
| 5 | Çelişki | `⚠️ VERIFICATION REQUIRED` + üst karar | sessizce geçme |

### §5.6 Ölçüm → rapor şeması (PLANNED çıktı)

| Alan | Değer | Durum |
|---|---|---|
| Ölçüm adı | örn. `cpu.stat::usage_usec` | PLANNED |
| Katman | çekirdek / uygulama / CI | ✅ tanımlı (§5.1 · §6) |
| Birim | µs / ms / sn / dk | ✅ tanımlı |
| Pencere | ölçüm periyodu | ⚠️ VERIFICATION REQUIRED |
| Eşik | kabul değeri | ⚠️ VERIFICATION REQUIRED |
| Aşım tepkisi | §4.1'e göre | ✅ tanımlı |

### §5.7 Ölçümün OS karşılığı — `getrusage(2)` alan kümesi (repo'da yok)

> Kaynak: https://man7.org/linux/man-pages/man2/getrusage.2.html · HTTP 200 · 2026-10-13
> §5.1–§5.3 "neyi ölçmeliyiz" der; bu bölüm o ölçümün çekirdeğin **hazır verdiği**
> adını ve alanını taşır. `hrtime` (§6.1 #1) bunların **hiçbiri değildir**.

| # | Alan | Ne sayar (resmi metin) | §5 karşılığı |
|---|---|---|---|
| 1 | `ru_utime` | "total amount of time spent executing in user mode" (timeval: sn + µs) | §5.1 (kullanıcı süresi) |
| 2 | `ru_stime` | "total amount of time spent executing in kernel mode" | §5.1 (çekirdek süresi) |
| 3 | `ru_maxrss` | "maximum resident set size used (in KiB)" | bellek tepe değeri |
| 4 | `ru_minflt` | "page faults serviced without any I/O activity" | sayfa hatası (yazılı) |
| 5 | `ru_majflt` | "page faults serviced that required I/O activity" | sayfa hatası (disk) |
| 6 | `ru_inblock` | "The number of times the filesystem had to perform input." | IO beklemesi |
| 7 | `ru_oublock` | "The number of times the filesystem had to perform output." | IO beklemesi |
| 8 | `ru_nvcsw` | "a context switch resulted due to a process voluntarily giving up the processor before its time slice was completed (usually to await availability of a resource)" | §5.2 #3 — **gönüllü** geçiş |
| 9 | `ru_nivcsw` | "a context switch resulted due to a higher priority process becoming runnable or because the current process exceeded its time slice" | §5.2 #3 — **zorla** geçiş |
| 10 | `ru_ixrss` · `ru_idrss` · `ru_isrss` · `ru_nswap` · `ru_msgsnd` · `ru_msgrcv` · `ru_nsignals` | "unmaintained fields are set to zero by the kernel" | **sayı okunsa bile 0'dır** — eşik yazma |

| # | Kapsam (`who`) | Resmi tanım |
|---|---|---|
| 1 | `RUSAGE_SELF` | "resource usage statistics for the calling process, which is the sum of resources used by all threads in the process" |
| 2 | `RUSAGE_CHILDREN` | "for all children of the calling process that have terminated and been waited for" |
| 3 | `RUSAGE_THREAD` | "for the calling thread" — "RUSAGE_THREAD is Linux-specific" |

> **Ölçüm disiplini notları (bağlayıcı):** "POSIX.1 specifies getrusage(), but specifies
> only the fields `ru_utime` and `ru_stime`" → taşımacılıkta yalnız iki alan **garantilidir**;
> kalanlar Linux'a özgüdür ve §5.5 #3 ("bu ortamda" diye sınırlı raporla) kuralını
> işletir. "Resource usage metrics are preserved across an execve(2)."
> **Repo durumu:** `getrusage` · `RUSAGE_` · `ru_nvcsw` → bu depoda **0 eşleşme**;
> §5.4'teki tüm adımlar bu yüzden PLANNED olarak kalır (sayı **uydurulmaz**).

## §6 Repo disk kanıtları

### §6.1 Doğrulanmış satırlar (S1 · Read ile birebir)

| # | Dosya | Satır | Alıntı (özgün) |
|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `$start = hrtime(true);` |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | `$durationMs = (hrtime(true) - $start) / 1_000_000;` |
| 3 | `shared/src/Api/Registry/ServiceRegistry.php` | 56 | `foreach ($this->services as $name => $service) {` |
| 4 | `shared/src/Api/Registry/ServiceRegistry.php` | 104 | `'timeout' => $service->getTimeout(),` |
| 5 | `shared/src/Api/Registry/ServiceDefinition.php` | 23 | `private readonly int $timeout = 5,` |
| 6 | `shared/src/Cache/PageCacheAdapter.php` | 11 | `int $ttlPage = 600` |
| 7 | `.github/workflows/ci.yml` | 28 | `timeout-minutes: 15` |

### §6.2 Bu satırlar neyi kanıtlar / kanıtlamaz

| # | Satır | Kanıtlar | Kanıtlamaz |
|---|---|---|---|
| 1 | `hrtime` (42·46) | uygulama katmanında süre ölçümü **var** | çekirdek latency ölçümü yok |
| 2 | `foreach` (56) | sağlık denetimi **seri** çalışıyor | paralel çalıştığını (yorumun aksine) |
| 3 | `timeout` (104·23) | HTTP bekleme sınırı 5 sn | süreç süresi sınırı değil |
| 4 | `ttlPage = 600` | önbellek ömrü 600 sn (uygulama kotası) | bellek kotası değil |
| 5 | `timeout-minutes` (28) | CI işi 15 dk sonra kesilir | üretimde limit yok |

### §6.3 Ölçüm — repo gerçeği

| # | Ölçüm | Repo durumu | Kanıt |
|---|---|---|---|
| 1 | Uygulama süresi (ms) | ✅ var | §6.1 #1–#2 |
| 2 | CI süre bütçesi (dk) | ✅ var | §6.1 #7 (6 işte de) |
| 3 | HTTP bekleme (sn) | ✅ var | §6.1 #3–#4 |
| 4 | cgroup sayaçları (µs) | ❌ yok | §1.5 |
| 5 | `proc(5)` süreç sayaçları | ❌ yok | grep → 0 |

### §6.4 Negatif kanıt (bu dosyanın dayanağı)

| # | Aranan | Kapsam | Sonuç |
|---|---|---|---|
| 1 | `pcntl_` · `pthread_` · `sched_` · `affinity` | `*.php` (repo) | **0 eşleşme** |
| 2 | `cgroup` · `cpu.max` · `memory.max` | yapılandırma | **0 kanıt** |
| 3 | NUMA okuması | `shared/src` | **0 eşleşme** |
| 4 | Sonuç | — | OS düzeyi kota/ölçüm kodu bu depoda **yoktur** |

### §6.5 CI zaman-bütçesi envanteri (`.github/workflows/` — 2026-10-13 Read)

> **Katman notu:** Bu tablolar **CI/süreç zamanlaması** kanıtıdır; işletim sistemi zamanlayıcısı değildir. İlişki yalnızca analojiktir (bkz. §6.2 #5).

| # | Dosya | Toplam satır | İş sayısı |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | 203 | 6 |
| 2 | `.github/workflows/secret-scan.yml` | 31 | 1 (`gitleaks`) |

| # | İş (`ci.yml`) | Başlık satırı | `timeout-minutes` |
|---|---|---|---|
| 1 | `php-lint` | 25 | 15 (satır 28) |
| 2 | `php-test` | 66 | 15 (satır 70) |
| 3 | `composer-audit` | 110 | 10 (satır 113) |
| 4 | `js-test` | 142 | 10 (satır 145) |
| 5 | `js-coverage` | 160 | 15 (satır 164) |
| 6 | `js-e2e` | 178 | 20 (satır 181) |

| # | Yapı | Satır | Değer (özgün) |
|---|---|---|---|
| 1 | iş adı | 8 | `name: CI Pipeline` |
| 2 | tetikleyici | 10–12 | `on` · `push` · `pull_request` |
| 3 | izinler | 14–15 | `permissions:` → `contents: read` |
| 4 | eşzamanlılık | 17–19 | `concurrency:` → `cancel-in-progress: true` |
| 5 | ortam | 21–22 | `env:` → `PHP_VERSION: '8.4'` |
| 6 | bağımlılık | 68 · 162 | `needs: php-lint` · `needs: js-test` |
| 7 | toplam | — | 15+15+10+10+15+20 = **85 dk** (iş üst sınırı, kritik yol değil) |

**Kanıtlar / kanıtlamaz:** üstteki değerler yalnız bu dosyalarda yazan **süre ve izin kotalarını** kanıtlar; işlerin gerçekte ne kadar sürdüğünü ve CI'ın geçtiğini **kanıtlamaz** (bkz. `.ai/AGENTS.md` §25.2: "çalışma durumu doğrulanmadı").

## §7 Kaynakça

### §7.1 Web doğrulama (2026-10-13 · GET)

| # | İddia | URL | Durum |
|---|---|---|---|
| 1 | cgroup v2 kontrol dosyaları ve varsayılanları | https://docs.kernel.org/admin-guide/cgroup-v2.html | ✅ HTTP 200 · içerik okundu |
| 2 | blok katmanı disiplin haritası (BFQ · kyber · deadline · blk-mq) | https://docs.kernel.org/block/index.html | ✅ HTTP 200 · içerik okundu |
| 3 | IO öncelik sınıfları | https://man7.org/linux/man-pages/man2/ioprio_set.2.html | ✅ HTTP 200 |
| 4 | `ionice(1)` idle/behavior | https://man7.org/linux/man-pages/man1/ionice.1.html | ✅ HTTP 200 |
| 5 | NUMA yerleşimi | https://docs.kernel.org/mm/numa.html | ✅ HTTP 200 |
| 6 | `RLIMIT_*` (anayasa §3.3) | https://man7.org/linux/man-pages/man2/getrlimit.2.html | ✅ HTTP 200 |
| 7 | `proc(5)` süreç sayaçları | https://man7.org/linux/man-pages/man5/proc.5.html | ✅ HTTP 200 |
| 8 | cgroup v2 yaşam döngüsü ve hata kataloğu (§1.7) | https://man7.org/linux/man-pages/man7/cgroups.7.html | ✅ HTTP 200 · içerik okundu |
| 9 | bellek yerleşim kipleri (§2.4) | https://man7.org/linux/man-pages/man2/set_mempolicy.2.html | ✅ HTTP 200 · içerik okundu |
| 10 | iş bağlama (§2.5) | https://man7.org/linux/man-pages/man2/sched_setaffinity.2.html | ✅ HTTP 200 · içerik okundu |
| 11 | süreç ölçüm alanı kümesi (§5.7) | https://man7.org/linux/man-pages/man2/getrusage.2.html | ✅ HTTP 200 · içerik okundu |

> **Yeniden kullanım notu (2026-10-13):** 4 URL bu görevde ilk kez GET edildi (HTTP 200 · tarih 2026-10-13). `docs.kernel.org` · `man7.org` satırları `isletim-sistemi-plan-dogrulama.md` (R-1..R-27 · N-W01..N-W19) envanteriyle aynı alan adlarıdır; yeni alan adı eklenmemiştir.

### §7.2 İlişkili vault dosyaları

| Dosya | Bağ |
|---|---|
| `kaynak-dagilimi-anayasa.md` | kural (§2–§6) |
| `kaynak-dagilimi-ornekler.md` | çalışan kanıt + dürüstlük defteri |
| `zamanlama-scheduler-surucu.md` | kuyruk disiplini uygulaması (Plan1) |
| `is-parcacigi-planlamasi-surucu.md` | iş birimi başına kota |
| `isletim-sistemi-cross/platform-yuzeyleri` | platform yüzeyi farkları |

### §7.3 Belirsizlik tablosu

| # | Belirsizlik | Etiket |
|---|---|---|
| 1 | cgroup uygulaması üretimde var mı? | ⚠️ VERIFICATION REQUIRED |
| 2 | NUMA topolojisi (hedef cihaz) | UNKNOWN (kapsam dışı) |
| 3 | Kuyruk disiplini uygulaması kimde? | UNKNOWN (Plan1) |
| 4 | Ölçüm eşikleri | ⚠️ VERIFICATION REQUIRED (sayı yok) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
