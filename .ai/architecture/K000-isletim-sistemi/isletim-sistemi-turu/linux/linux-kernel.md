---
title: "Linux Kernel — Monolitik+Modüler Çekirdek, Zamanlama (CFS/EEVDF), Bellek, VFS, Modüller, Kesme ve PREEMPT_RT"
type: architecture-deep-dive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-kernel
node_type: module
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/linux
children: []
depends_on:
  - "linux/linux-anayasa.md"
evidence:
  - "https://docs.kernel.org/scheduler/sched-eevdf.html (2026-10-12 ✅ çekildi)"
  - "https://raw.githubusercontent.com/torvalds/linux/v6.12/kernel/Kconfig.preempt (2026-10-12 ✅ arama özeti)"
  - "https://docs.kernel.org/6.12/locking/locktypes.html (2026-10-12 ✅ arama özeti)"
  - "https://kernelnewbies.org/Linux_6.12 (2026-10-12 ✅ arama özeti)"
  - "https://www.phoronix.com/news/Linux-6.12-Does-Real-Time (2026-10-12 ✅ arama özeti)"
  - "https://man7.org/linux/man-pages/man2/syscall.2.html (2026-10-12 ✅ — vdso(7), init_module/delete_module backlinkleri)"
  - "https://docs.kernel.org/core-api/workqueue.html (2026-10-12 ✅ HTTP 200 — cmwq, WQ_* bayrakları, max_active)"
  - "https://man7.org/linux/man-pages/man7/namespaces.7.html (2026-10-12 ✅ HTTP 200 — 8 namespace tipi, CLONE_NEW*)"
  - "https://docs.kernel.org/core-api/timekeeping.html (2026-10-12 ✅ HTTP 200 — ktime accessors)"
  - "https://docs.kernel.org/scheduler/sched-deadline.html (2026-10-12 ✅ HTTP 200 — CBS/EDF/GRUB)"
  - "https://docs.kernel.org/kbuild/index.html (2026-10-12 ✅ HTTP 200 — Kbuild hiyerarşisi)"
  - "https://man7.org/linux/man-pages/man7/random.7.html (2026-10-12 ✅ HTTP 200 — CSPRNG/entropy)"
  - "https://man7.org/linux/man-pages/man2/membarrier.2.html (2026-10-12 ✅ HTTP 200 — CMDGlobal/PRIVATE_EXPEDITED)"
  - "https://man7.org/linux/man-pages/man3/pthread_mutex_init.3.html (2026-10-12 ✅ HTTP 200 — mutex kind, cancellation)"
validation: []
open_questions: []
group: mimari
---

# Linux Kernel (linux-kernel.md)

> **ELI15:** Linux çekirdeği bilgisayarın tüm donanımını yöneten dev bir programdır;
> "monolitik" yani her şey tek program içinde çalışır, ama parçalar halinde
> yüklenebilir (modüler). Ses geldiğinde çekirdek kısa bir iş yapar ve bunu
> öncelikli olarak, gecikmeye izin vermeden tamamlamak ister — PREEMPT_RT dediğimiz
> yapılandırma tam bunu sağlar.

**Durum:** ŞARTNAME/belge IMPLEMENTED · Kod: ⚠️ PLANNED (cpp=0) · Hedef sürüm aralığı:
kernel.org longterm (6.18 LTS odaklı, 2026-10-12 ölçümü).

---

## §1 Mimari: Monolitik + Modüler Çekirdek

Linux klasik anlamda **monolitik** bir çekirdektir: sürücüler, dosya sistemleri ve ağ
yığını çekirdek adres alanında çalışır. Bununla birmit **modülerdir**: pek çok parça
çalışma anında yüklenebilir/çıkarılabilir (`insmod`/`rmmod`).

| Özellik | Monolitik | Mikroçekirdek | Linux'un konumu |
|---|---|---|---|
| Sürücü bağlamı | Çekirdek modu | Kullanıcı modu (tipik) | Monolitik taraf |
| Yüklenebilirlik | Statik derleme | Dinamik servis | Modüler taraf |
| Performans (geçiş maliyeti) | Düşük (fonksiyon çağrısı) | Yüksek (IPC) | Düşük geçiş — ses için avantaj |
| Hata izolasyonu | Sürücü çökmesi çekirdeği etkiler | Servis çöküşü izole | Zayıf yön; RT sürücülerde dikkat ⚠️ |

**Kaynak (mimari bağlam):** docs.kernel.org ağaç yapısı (✅ 2026-10-12 çekildi) —
"Internal API manuals: Core API · Driver APIs · Subsystems (Core subsystems:
Memory Management Documentation, Scheduler, Timers, Locking…)".

### §1.1 Alt sistem envanteri (docs.kernel.org ağacı — ✅)

| Alt sistem | Docs.kernel.org yolu | Ses motoru ilişkisi |
|---|---|---|
| Zamanlayıcı (Scheduler) | `/scheduler/` | RT thread önceliklendirme, EEVDF |
| Bellek yönetimi (MM) | `/mm/` | Buffer ön-tahsisi, `mlockall` |
| Zamanlayıcılar/Zamanlayıcılar (Timers) | `/timers/` | Period zamanlaması, hrtimer ⚠️ |
| Kilitler (Locking) | `/locking/` | PREEMPT_RT kilit dönüşümleri (§7.4) |
| Ses (Sound) | `/sound/` | ALSA çekirdek API'si (→ `linux-ses-yigini.md`) |
| Tracing | `/trace/` | Latency ölçümü (`ftrace` ⚠️) |
| Rust | `/rust/` | Çekirdekte Rust desteği — CoreMusic için ilgisiz, envanter notu |

---

## §2 Süreç Modeli (Process Model)

| Kavram | Tanım | İlgili arayüz | Durum |
|---|---|---|---|
| **task** | Çekirdeğin zamanlayabildiği en küçük birim (`task_struct`) | `clone(2)`/`fork(2)` | ✅ varlık (syscall(2) backlinkleri: clone(2)) |
| **process vs thread** | Linux'ta thread de bir "process"tir; `clone` bayrakları (CLONE_VM vb.) ayrımı belirler | `clone(2)` | ✅ |
| **PID/TID** | Süreç/tkimlik numarası | `getpid(2)`/`gettid(2)` | ✅ (syscall(2) backlinkleri + örnek kodda `SYS_gettid`) |
| **Sinyaller** | Asenkron olay dağıtımı | `rt_sigaction(2)`/`tgkill` (örnek kod ✅) | ✅ |
| **Zombie/exit** | Kaynak geri alma | `wait4(2)`/`exit_group(2)` | ✅ (backlinklerde mevcut) |

**Ses motoru etkisi:** JUCE ses thread'i `clone` ile yaratılır; real-time davranışı için
öncelik (§3) ve bellek ön-tahsisi (§4) gerekir.

---

## §3 Zamanlama: CFS → EEVDF (⚠️ sürüm notu)

### §3.1 Geçiş kronolojisi (✅ docs.kernel.org/scheduler/sched-eevdf.html)

| Sürüm | Olay | Kaynak cümlesi (özet) |
|---|---|---|
| 2.6.23 (2007 ⚠️) | CFS (Completely Fair Scheduler) girdi | EEVDF sayfası CFS'ten "earlier" olarak niteliyor; kesin yıl ⚠️ |
| 6.6 (2024) | EEVDF **seçenek olarak** girdi: "The Linux kernel began transitioning to EEVDF in version 6.6 (as a new option in 2024), moving away from the earlier Completely Fair Scheduler (CFS)" | ✅ birebir |
| 6.12 (2024) | EEVDF "complete support" (kernelnewbies Linux_6.12 özeti ✅) | ✅ |
| 7.3-rc7 (2026-10-12) | docs.kernel.org EEVDF sayfası hâlâ aktif; CFS sayfası da duruyor (`sched-design-CFS.html`) | ✅ |

### §3.2 EEVDF'nin çalışma prensibi (✅ kaynak metinden)

| Kavram | Tanım |
|---|---|
| **Virtual runtime (VRT)** | Her görevin "adil pay" karnesi |
| **Lag** | `gerçek çalışma − adil pay`; pozitif = borçlanılmış (CPU bekliyor), negatif = fazla almış |
| **Eligibility** | Yalnız `lag ≥ 0` olan görevler seçilebilir |
| **Virtual Deadline (VD)** | Her görev için hesaplanan sanal bitiş; **en erken VD'li** görev çalıştırılır |
| **Latency hassasiyeti** | "shorter time slices" (daha dilimler) isteyen gecikme-duyarlı görevler öne çıkar — **ses motoru için doğrudan avantaj** |
| **Sleep oyunu engeli** | Uyuyan görev run queue'da kalır ("deferred dequeue"), lag VRT ile söner — kısa uyuyup negatif lag sıfırlama engellenir |

**İstek mekanizması:** "tasks can request specific time slices using the new
`sched_setattr()` system call" (✅) — JUCE/uygulama katmanından zaman dilimi talebi
mümkün (kullanım CoreMusic'te henüz PLANNED).

### §3.3 Öncelik sınıfları (⚠️ — bu oturumda man sayfası çekilmedi)

| Politika | Amaç | Not |
|---|---|---|
| `SCHED_NORMAL` (EEVDF) | Normal iş yükü | Varsayılan |
| `SCHED_BATCH` | Toplu iş | ⚠️ |
| `SCHED_IDLE` | Arka plan | ⚠️ |
| `SCHED_FIFO` / `SCHED_ROUND_ROBIN` | Gerçek zamanlı öncelik | ⚠️ (kaynak: man7 sched(7) — bu oturumda çekilmedi) |
| `SCHED_DEADLINE` | En sıkı garanti | ⚠️ (kaynak: man7 sched(7)) |

> ⚠️ Not: Sınıf isimleri bilinen Linux arayüzüdür; sayfa-of-record olarak
> `https://man7.org/linux/man-pages/man7/sched.7.html` işaretlenmiştir ama bu oturumda
> çekilmemiştir → `linux-dogrulama.md` Q8.

---

## §4 Bellek Yönetimi (Memory Management)

| Bileşen | Görev | Ses motoru ilişkisi | Durum |
|---|---|---|---|
| Sanal bellek | Süreç başına adres alanı | Uygulama yalıtımı | genel ⚠️ (docs `/mm/` altında) |
| Sayfa önbelleği (page cache) | Dosya I/O hızlandırma | Diskten stream okumada etkili | genel ⚠️ |
| mmap | Dosya/cihaz belleğini adres alanına taşıma | ALSA mmap buffer yolu (→ `linux-api.md` §4) | ✅ (syscall listesinde) |
| mlock/mlockall | Sayfa hatasını engelleme | Real-time thread zorunluluğu | ⚠️ (kullanım notu) |
| Huge pages | TLB azaltma | Düşük gecikme için opsiyonel | ⚠️ |
| cgroup memory | Kullanıcı grubuna bellek sınırı | Container/runtime izolasyonu (K018) | ⚠️ |
| OOM killer | Bellek biterse süreç öldürme | Ses thread'i öldürülürse kesinti → ön-tahsis ile korunur | ⚠️ |

---

## §5 VFS (Virtual File System)

VFS, çekirdeğin dosya erişim soyutlama katmanıdır; her şey "dosya"dır (soketler,
cihazlar, pipe'lar dahil).

| Kavram | Tanım | Ses ilişkisi |
|---|---|---|
| **inode** | Dosyanın metadata (sahiplik, izin, boyut) | `/dev/snd/pcmC0D0p` bir cihaz inode'u |
| **dentry** | Yol ↔ inode eşlemesi (dizin girişi) | `plug:` önekiyle açılan sözde dosya yolları ⚠️ |
| **superblock** | Dosya sistemi kök bilgisi | ext4/xfs/btrfs ⚠️ |
| **file description** | Açık dosya tanımı (fd) | Ses motoru tüm cihaz erişimini fd üzerinden yapar |

**İlgili syscall'lar (✅ varlık):** `openat(2)`/`openat2(2)` · `read(2)`/`write(2)` ·
`mmap(2)` · `ioctl(2)` (backlinklerde `openat2`, `statmount`, `listmount` vb. de görünüyor).

### §5.1 Proc ve sysfs

| Arayüz | Yol | İçerik | Durum |
|---|---|---|---|
| procfs | `/proc` | Süreç bilgileri + `/proc/asound` (ALSA) | ✅ (docs.kernel.org `/sound/alsa-configuration.html#proc-interfaces-proc-asound` başlığı çekildi) |
| sysfs | `/sys` | Cihaz ağacı, kernel nesneleri | genel ⚠️ |
| devtmpfs | `/dev` | Sürücülerin yarattığı cihaz düğümleri | genel ⚠️ |

---

## §6 Modül Yükleme (insmod / kmod / modprobe)

| Araç | Ne yapar | syscall karşılığı | Durum |
|---|---|---|---|
| `insmod` | .ko dosyasını yükler | `init_module(2)` | ✅ (syscall(2) backlink listesinde `init_module(2)` ve `finit_module(2)` mevcut) |
| `rmmod` | Modülü çıkarır | `delete_module(2)` | ✅ (backlink listesinde) |
| `modprobe` | Bağımlılıkları çözerek yükler (kmod paketi) | `finit_module` + kullanıcı alanı çözümleme | ⚠️ (kmod sayfası çekilmedi; `https://git.kernel.org/pub/scm/utils/kernel/kmod/kmod.git` ⚠️) |
| `lsmod` | Listeler | `/proc/modules` okuması | ⚠️ |
| modül imzalama | Secure Boot uyumu için zorunlu yapılandırma ⚠️ | kernel config `CONFIG_MODULE_SIG` ⚠️ | ⚠️ |

**Ses modülleri:** ALSA çekirdek sürücüleri genelde modüldür (ör. HDA/USB-audio sınıf
sürücüleri) — docs.kernel.org `/sound/` ağacı bunları ayrı sayfalarda belgeler (✅
envanter: `hd-audio/`, `cards/`, `codecs/`, `soc/`).

---

## §7 Kesme (Interrupt) ve Alt Kuyruk (Softirq) Modeli

### §7.1 Katmanlar

| Katman | Bağlam | Gecikme davranışı | Ses ilişkisi |
|---|---|---|---|
| **Hard IRQ** | Donanım tetiklemesi | En kısa; preemption kapalı (standart çekirdek) | Ses kartı kesmesi: DMA period tamamlandı |
| **Threaded IRQ** | IRQ thread'inde çalışır | Scheduler kontrolünde; öncelik verilebilir | PREEMPT_RT'nin varsayılanı (§7.3) |
| **Softirq / tasklet** | Alt kuyruk (bottom half) | IRQ sonrası toplu iş | Ağ/RCU'da yoğun; ses yolunda kaçınılır ⚠️ |
| **hrtimer** | Yüksek çözünürlüklü zamanlayıcı | period zamanlaması | Period wakeup'ları ⚠️ (doküman `/timers/`) |

### §7.2 Neden ses için kesme modeli önemli?

Bir ALSA period dolduğunda çekirdek uygulamaya haber verir ("uyandırma"). Uyanma
zamanının **öngörülebilirliği**, ses motorunun gecikme (latency) bütçesini belirler.
Standart çekirdekte kesme kısa sürer ama softirq/yük nedeniyle **worst-case**
belirsizdir; RT çekirdeği bu belirsizliği azaltır (§7.3).

### §7.3 PREEMPT_RT — tur-3 kanıtları (✅ bu oturumda doğrulandı)

**Ana akım birleşme:** PREEMPT_RT, **Linux 6.12**'de (17 Kasım 2024) ana akıma girdi —
~20 yıllık çalışmanın sonucu; son bloker `printk()` yeniden yazımıydı
(kernelnewbies Linux_6.12 ✅). İlk desteklenen mimariler: **ARM64, RISC-V, x86/x86_64**
(Phoronix ✅ arama özeti). Kconfig bayrağı: **`CONFIG_PREEMPT_RT`**
(`kernel/Kconfig.preempt` @v6.12 ✅).

**Kconfig metni (✅ birebir özet):** "turns the kernel into a real-time kernel by
replacing various locking primitives (spinlocks, rwlocks, etc.) with preemptible
priority-inheritance aware variants, enforcing interrupt threading and introducing
mechanisms to break up long non-preemptible sections… except for very low level and
critical code paths (entry code, scheduler, low level interrupt handling)".

**Teori (docs.kernel.org/core-api/real-time/theory ✅ arama özeti):**

| Dönüşüm | Ne değişir | Sonuç |
|---|---|---|
| `spinlock_t` → **rtmutex** | Döngü (spin) yerine uyuma (sleep); öncelik devri (priority inheritance) | Kilit bekleyen yüksek öncelikli görev, kilit sahibine önceliğini "bağışlar" — priority inversion engeli |
| Kesmeler → **threaded interrupts** | IRQ handler'ları scheduler kontrolüne girer | Uygulama öncelikli IRQ hizmeti mümkün |
| Uzun kritik bölgeler → bölünme | Preemption kapalı süreler kısalır | Worst-case gecikme düşer |
| `printk` RT uyumu | Son bloker kalktı (6.12) | Ana akım derleme mümkün |

**Kilit kategorileri (docs.kernel.org/6.12/locking/locktypes ✅ özet):**

| Kilit | RT'de karşılığı | Kural |
|---|---|---|
| `spinlock_t` / `rwlock_t` | rtmutex tabanlı sleeping lock | Preemption artık **kapatılmaz**; `spin_lock_irq*` artık kesmeyi kapatmaz |
| `local_lock` | Per-CPU `spinlock_t` | RT'de kesme/preempt koruması **vermez** |
| `raw_spinlock_t` | Aynı kalır | Scheduler gibi çekirdek içi yolcularda |
| Bit spinlock | Aynı kalır | Tek bit rtmutex'a sığmaz |
| Sıra | sleeping → spinlock_t/rwlock_t/local_lock → raw_spinlock_t | İç içe kuralı |

**EFI notu (✅):** `CONFIG_PREEMPT_RT` ile EFI runtime varsayılan açılır; kapatma için
boot parametresi (`efi=runtime` dışı) — docs `core-api/real-time/kernel-configuration.html`.

### §7.4 Real-time ses için anlamı (CoreMusic çıkarımları)

| Çıkarım | Açıklama | Durum |
|---|---|---|
| Gecikme tahmini | RT çekirdek worst-case gecikmeyi düşürür (throughput bazen düşer — kernelnewbies: "lower latencies (possibly at the expense of throughput)") | ✅ çerçeve |
| Öncelik devri | Ses thread'i SCHED_FIFO ile kilit beklerse kilit sahibi öncelik devriyle öne çıkar → "priority inversion" gecikmesi azalır | ✅ teori |
| Threaded IRQ | IRQ handler scheduler'a tabi → ses uygulaması IRQ'dan yüksek öncelikle hizmet alabilir | ✅ |
| CoreMusic hedefi | Neva Engine Linux hedefi: **6.12+ LTS + `CONFIG_PREEMPT_RT`** (şartname önerisi) | ⚠️ TASARIM — onay + ADR bekliyor |
| RT çekirdek zorunlu mu? | Hayır; standart çekirdekte de çalışır, sadece garanti daralır | ⚠️ Q7 (anayasa) |

---

## §8 Boot (Açılış) Zinciri (özet)

| Aşama | Bileşen | Not | Durum |
|---|---|---|---|
| 1 | Firmware (BIOS/UEFI) | Donanım başlatma | genel ⚠️ |
| 2 | Bootloader (GRUB/systemd-boot ⚠️) | Çekirdek + initramfs yükler | systemd.io'da `/BOOT/` (systemd-boot UEFI Boot Manager) ✅ varlık |
| 3 | initramfs | Erken sürücüler | ⚠️ (systemd.io `/INITRD_INTERFACE/` ✅ varlık) |
| 4 | PID 1 (systemd) | Servis ağacı | ✅ (systemd.io: "runs as PID 1 and starts the rest of the system") |
| 5 | Ses servisleri | PipeWire (kullanıcı oturumunda) | → `linux-framework.md` §1, `linux-ses-yigini.md` |

---

## §9 Sürüm/Çekirdek Yapılandırma Karar Matrisi (CoreMusic hedefi)

| Parametre | Önerilen hedef | Gerekçe | Durum |
|---|---|---|---|
| LTS dalı | 6.18 (longterm, 2026-10-11: 6.18.56 ✅) veya 6.12 (RT birleşim dali ✅) | Uzun destek + RT olgunluğu | ⚠️ TASARIM |
| Preemption modeli | `PREEMPT_RT` (ses stüdyosu senaryosu) veya `PREEMPT_DYNAMIC` (genel masaüstü) | Kconfig metinleri ✅ | ⚠️ TASARIM (Q7) |
| Modül stratejisi | Ses sürücüleri modüler; çekirdek minimal | Distro uyumu | ⚠️ |
| tracing | `ftrace`/perf ile latency doğrulama | Ölçüm kapısı | ⚠️ PLANNED |

---

## §10 Açık Sorular (kernel)

| # | Soru | Not |
|---|---|---|
| K-Q1 | EEVDF'de `sched_setattr` ile dilim talebi JUCE tarafından kullanılabilir mi? | JUCE API'sinde karşılığı araştırılacak ⚠️ |
| K-Q2 | PREEMPT_RT + PipeWire kombinasyonunda beklenen worst-case gecikme ölçüldü mü? | Ölçüm raporu yok (qa-engineer) ⚠️ |
| K-Q3 | Hedef donanım (x86-64 mü, ARM64 mü?) — RT destek mimarileri sınırlı (ARM64/RISC-V/x86 ✅) | Donanım kararı audio-hardware-engineer + root |
| K-Q4 | `local_lock` semantik farkı JUCE/ALSA kodunu etkiler mi? | upstream kod incelemesi gerekir ⚠️ |
| K-Q5 | Çekirdek yapılandırma imzası (secure boot) hedefleniyor mu? | devops + security ⚠️ |

---

## §11 Kaynakça (kernel)

| # | Kaynak | Erişim | Durum |
|---|---|---|---|
| K1 | https://docs.kernel.org/scheduler/sched-eevdf.html | 2026-10-12 | ✅ tam metin çekildi |
| K2 | https://docs.kernel.org/ (7.3.0-rc7 ağaç) | 2026-10-12 | ✅ |
| K3 | https://docs.kernel.org/sound/index.html | 2026-10-12 | ✅ (ALSA/ASoC envanteri) |
| K4 | https://raw.githubusercontent.com/torvalds/linux/v6.12/kernel/Kconfig.preempt | 2026-10-12 | ✅ (PREEMPT_RT + PREEMPT_DYNAMIC help metinleri) |
| K5 | https://docs.kernel.org/6.12/locking/locktypes.html | 2026-10-12 | ✅ (RT kilit dönüşüm kuralları) |
| K6 | https://docs.kernel.org/next/core-api/real-time/theory.html (+ kernel-configuration.html) | 2026-10-12 | ✅ (teori + EFI notu) |
| K7 | https://kernelnewbies.org/Linux_6.12 | 2026-10-12 | ✅ (RELEASED 17 Nov 2024 · printk son bloker) |
| K8 | https://www.phoronix.com/news/Linux-6.12-Does-Real-Time | 2026-10-12 | ✅ (mimari listesi iddiası — üçüncü taraf) |
| K9 | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-12 | ✅ (vdso(7) · module syscall backlinkleri) |
| K10 | https://www.kernel.org/ | 2026-10-12 | ✅ (LTS satırları) |
| K11 | https://man7.org/linux/man-pages/man7/sched.7.html | — | ⚠️ bu oturumda çekilmedi (Q8) |
| K12 | https://git.kernel.org/pub/scm/utils/kernel/kmod/kmod.git | — | ⚠️ çekilmedi |
| K13 | https://docs.kernel.org/core-api/workqueue.html | 2026-10-12 | ✅ HTTP 200 (§12) |
| K14 | https://man7.org/linux/man-pages/man7/namespaces.7.html | 2026-10-12 | ✅ HTTP 200 (§13) |
| K15 | https://docs.kernel.org/core-api/timekeeping.html | 2026-10-12 | ✅ HTTP 200 (§14) |
| K16 | https://docs.kernel.org/scheduler/sched-deadline.html | 2026-10-12 | ✅ HTTP 200 (§15) |
| K17 | https://docs.kernel.org/kbuild/index.html | 2026-10-12 | ✅ HTTP 200 (§16) |
| K18 | https://man7.org/linux/man-pages/man7/random.7.html | 2026-10-12 | ✅ HTTP 200 (§17) |
| K19 | https://man7.org/linux/man-pages/man2/membarrier.2.html | 2026-10-12 | ✅ HTTP 200 (§18) |
| K20 | https://man7.org/linux/man-pages/man3/pthread_mutex_init.3.html | 2026-10-12 | ✅ HTTP 200 (§19) |

---

## §12 Workqueue — Çekirdek Asenkron İş Kuyrukları (cmwq)

> Kaynak: `https://docs.kernel.org/core-api/workqueue.html` (✅ HTTP 200 — 2026-10-12).

| Kavram | Sayfadaki tanım (özet) |
|---|---|
| workqueue (wq) | "the most commonly used" çekirdek iş kuyruğu mekanizması; iş kuyruğa konur, **worker** denen thread yürütür |
| cmwq | "Concurrency Managed Workqueue" — eski MT wq'nin yeniden yazımı: "Maintain compatibility with the original workqueue API" + "Automatically regulate worker pool and level of concurrency" |
| [k]worker | "For threaded workqueues, special purpose threads, called [k]workers, execute the work items" |
| worker-pool | Bound havuzlar CPU'ya bağlı; "for high priority ones, for each possible CPU and some extra" — unbound havuzlar "dynamik" |
| BH workqueue | "a convenience interface to softirq"; "always per-CPU", "BH work items cannot sleep" |

### §12.1 alloc_workqueue() bayrakları (✅ sayfa tablosu)

| Bayrak | Davranış | Ses motoru ilişkisi |
|---|---|---|
| `WQ_BH` | Softirq bağlamında, kuyruk sırasıyla, uyanlamaz (cannot sleep) | Çekirdek-içi; uygulama doğrudan kullanamaz ⚠️ |
| `WQ_PERCPU` | İş öğesi belirli bir CPU'ya bağlı — "right choice when cpu locality is important" | CPU bağlama (affinity) senaryosu |
| `WQ_UNBOUND` | "not bound to any specific CPU"; "sacrifices locality"; geniş eşzamanlılık dalgalanması veya "Long running CPU intensive workloads which can be better managed by the system scheduler" için önerilir | CPU-yoğun arka plan işleri |
| `WQ_FREEZABLE` | Suspend donma evresine katılır; "thawed" edilene kadar yeni iş başlamaz | Ses motoru suspend davranışında dikkat ⚠️ |
| `WQ_MEM_RECLAIM` | Bellek baskısı altında bile yürütme bağlamı garanti edilir; **reclaim yollarında kullanılan tüm wq'lar bunu taşımak zorunda** ("MUST have this flag set") | Dosya/önbellek yolu işleri |
| `WQ_HIGHPRI` | "served by worker threads with elevated nice level" | ⚠️ dikkat: bu **nice** yükseltmesidir, SCHED_FIFO değil |
| `WQ_CPU_INTENSIVE` | "do not contribute to the concurrency level" — CPU-hogging işler scheduler'a bırakılır; "This flag is meaningless for unbound wq" | Uzun CPU işleri için |

### §12.2 max_active ve sıralı kuyruk

| Parametre | Değer | Not |
|---|---|---|
| `@max_active` anlamı | CPU başına eşzamanlı yürütme bağlamı üst sınırı | "always a per-CPU attribute, even for unbound workqueues" |
| Sınır | max **2048**, 0 verilirse varsayılan **1024** | "sufficiently high such that they are not the limiting factor" |
| Öneri | Özel ihtiyac yoksa `0` ver | "concurrency level usually stays well under the default limit" |
| Sıralı kuyruk | `max_active=1` + `WQ_UNBOUND` artık sıralık garanti etmiyor; bunun yerine `alloc_ordered_workqueue()` | ✅ birebir uyarı |

**Ses motoru çıkarımı (CoreMusic):** workqueue, kesme/period bağlamındaki ses callback'inin
YERİNE GEÇMEZ — callback kesme bağlamında çalışır (§7); workqueue yalnızca kontrol-yolu
(device açma/kapama, dosya işlemleri) için düşünülebilir ve RT ses işi `WQ_HIGHPRI`
ile değil, doğrudan öncelikli thread ile yapılmalıdır.

---

## §13 Namespace'ler (namespaces(7))

> Kaynak: `https://man7.org/linux/man-pages/man7/namespaces.7.html` (✅ HTTP 200 — 2026-10-12).
> Sayfa amacı: "One use of namespaces is to implement containers."

| Namespace | Flag | İzole edilen kaynak | Man sayfası |
|---|---|---|---|
| Cgroup | `CLONE_NEWCGROUP` | Cgroup kök dizini | cgroup_namespaces(7) |
| IPC | `CLONE_NEWIPC` | System V IPC, POSIX message queues | ipc_namespaces(7) |
| Mount | `CLONE_NEWNS` | Mount points | mount_namespaces(7) |
| Network | `CLONE_NEWNET` | Network devices, stacks, ports | network_namespaces(7) |
| PID | `CLONE_NEWPID` | Process IDs | pid_namespaces(7) |
| Time | `CLONE_NEWTIME` | Boot and monotonic clocks | time_namespaces(7) |
| User | `CLONE_NEWUSER` | User and group IDs | user_namespaces(7) |
| UTS | `CLONE_NEWUTS` | Hostname and NIS domain name | uts_namespaces(7) |

### §13.1 Namespace API ve yetki kuralı (✅ sayfa metni)

| Arayüz | Rol |
|---|---|
| `clone(2)` | `CLONE_NEW*` bayrakları ile yeni namespace yaratır; çocuk süreç yeni namespace'e üye olur |
| `setns(2)` | Mevcut bir namespace'e katılma — hedef `/proc/pid/ns` dosyasına bağlı file descriptor |
| `unshare(2)` | Çağırılan süreci yeni namespace'e taşır |
| `ioctl(2)` | Namespace keşif işlemleri (ioctl_nsfs(2)) |

**Yetki:** clone(2)/unshare(2) ile namespace yaratmanın çoğu durumda `CAP_SYS_ADMIN` gerektirir;
istisna user namespace — "since Linux 3.8, no privilege is required".

**`/proc/pid/ns/`:** Her süreçte namespace başına bir girdi; "As long as this file descriptor
remains open, the namespace will remain alive". 3.8+ öncesi hard link, 3.8'den itibaren
symbolic link (`uts:[4026531838]` biçiminde). Aynı namespace'deki iki süreç aynı
`st_dev`/`st_ino` değerlerine sahiptir. PID namespace üyeliği süreç ömrü boyunca
değişmez ("never changes"). `readlink(2)` yetkisi `PTRACE_MODE_READ_FSCREDS` kontrolüne tabidir.

**`/proc/sys/user/` (Linux 4.9+):** `max_cgroup_namespaces` · `max_ipc_namespaces` vb. dosyalar
kullanıcı başına namespace üst sınırını açığa çıkarır.

---

## §14 Zaman Tutma — ktime accessors (timekeeping)

> Kaynak: `https://docs.kernel.org/core-api/timekeeping.html` (✅ HTTP 200 — 2026-10-12 —
> sayfa başlığı: "ktime accessors", docs 7.3.0-rc7 ağacı).

**Kural (sayfa):** "Device drivers can read the current time using ktime_get() and the many
related functions declared in linux/timekeeping.h" — "using an accessor with a shorter name
is preferred".

| Fonksiyon | Saat referansı | Sayfanın kullanımı/uyarısı |
|---|---|---|
| `ktime_get()` | `CLOCK_MONOTONIC` | "reliable timestamps and measuring short time intervals accurately. Starts at system boot time but stops during suspend" |
| `ktime_get_boottime()` | `CLOCK_BOOTTIME` | `ktime_get()` gibi ama suspend sırasında DURMAZ; suspend'i aşan eş zamanlılık için |
| `ktime_get_real()` | `CLOCK_REALTIME` | UNIX epoch; "should be avoided for internal uses, since it can jump backwards due to a leap second update, NTP adjustment [veya] settimeofday()" |
| `ktime_get_clocktai()` | `CLOCK_TAI` | TAI referansı; leap second sıçramaz — "rarely useful in the kernel" |
| `ktime_get_raw()` | `CLOCK_MONOTONIC_RAW` | NTP düzeltmesi olmadan donanım clocksource hızında — "rarely needed in the kernel" |

**Çıktı biçimleri:** hepsinin `_ns` (u64 nanosaniye) ve `_ts64` (`struct timespec64`,
saniye+nanosaniye) varyantları; ayrıca `ktime_get_seconds()` (`time64_t`).

**Ses motoru çıkarımı:** periyodik ölçüm/timestamp için sayfanın önerdiği `CLOCK_MONOTONIC`
tabanlı `ktime_get()` çerçevesidir; `CLOCK_REALTIME` NTP/leap-second sıçraması nedeniyle
iç zamanlamada kullanılmaz (sayfa uyarısı birebir bu yönde).

---

## §15 SCHED_DEADLINE (deadline zamanlama politikası)

> Kaynak: `https://docs.kernel.org/scheduler/sched-deadline.html` (✅ HTTP 200 — 2026-10-12).

| Parametre | Garanti (sayfa) |
|---|---|
| `runtime` | Görev her `period` mikrosaniyede `runtime` mikrosaniye yürütme alır |
| `deadline` | Bu `runtime` mikrosaniyeleri, dönemin başından itibaren `deadline` mikrosaniye içinde kullanılabilir |

**Algoritma (§2.1):** Uyanma anında çekirdek "scheduling deadline"ı **CBS** algoritmasıyla
hesaplar; görevler **EDF** ile en erken scheduling-deadline'lı olan sırayla çalıştırılır.
Kalan yürütme süresi 0'a inince görev **throttled** ("depleted") olur ve scheduling
deadline'ına kadar planlanamaz; replenishment anında `deadline += period`,
`runtime += runtime` güncellenir. `SCHED_FLAG_DL_OVERRUN` ile runtime aşımı
`SIGXCPU` ile bildirilebilir.

**Bant geri kazanımı (§2.2):** `SCHED_FLAG_RECLAIM` ile **GRUB** ("Greedy Reclamation of
Unused Bandwidth") devreye girer; görev blokladığında anında Inactive olmaz —
"ActiveNonContending" durumuna geçer ve "0-lag time" anında bant geri kazanılır
(garantiyi bozmadan).

**İlişki (§3.2):** `sched_setattr()` ile dilim/garanti talebi; admission control olmadan
yük altında garanti sağlanamaz ("if the system is overloaded this guarantee cannot be
respected").

**Ses motoru çıkarımı:** en sıkı garanti politikasıdır ama CoreMusic şartnamesinde
henüz kullanılmıyor (§3.3'te `SCHED_DEADLINE` ⚠️ işaretli); K-Q1 kapsamında
değerlendirilecektir.

---

## §16 Kbuild — Çekirdek Yapılandırma/Modül Derleme Sistemi

> Kaynak: `https://docs.kernel.org/kbuild/index.html` (✅ HTTP 200 — 2026-10-12; sayfa
> başlığı "Kernel Build System", Sphinx 5.3.0 site ağacı).

Bu sayfa Kbuild alt dokümanlarının indeksidir; envanter (✅ birebir başlık listesi):

| Alt doküman | Konu |
|---|---|
| Kconfig Language (+ macro language) | Yapılandırma dilini (`CONFIG_*`) tanımlama |
| Kbuild | Yapılandırma sistemi çekirdeği |
| Linux Kernel Makefiles | Çekirdek Makefile düzeni |
| **Building External Modules** | Ağaç-dışı (out-of-tree) modül derleme — ses sürücüsü geliştirme yolu |
| Configuration targets and editors | `menuconfig`/`nconfig` vb. hedefler |
| Exporting kernel headers for use by userspace | Kullanıcı alanına başlık dışa aktarma |
| Reproducible builds · Recursion issues · GCC plugins · Clang/LLVM · DWARF module versioning · Bash completion | Alt konular |

**Ses motoru ilişkisi:** CoreMusic hedefi ağaç-dışı ALSA modülü ise "Building External
Modules" akışı esas alınır (⚠️ PLANNED — diskte modül kodu yok, cpp=0).

---

## §17 Rastgelelik — random(7)

> Kaynak: `https://man7.org/linux/man-pages/man7/random.7.html` (✅ HTTP 200 — 2026-10-12).

**Çekirdek RNG (sayfa):** entropy, sürücülerden/çevresel gürültüden toplanır ve bir
**CSPRNG**'yi tohumlar; "It is designed for security, rather than speed".

| Arayüz | Havuz | Bloklanma | Havuz hazır değilken |
|---|---|---|---|
| `/dev/random` | random pool | Yeterli entropy yoksa bloklanır | "Blocks until enough entropy gathered" |
| `/dev/urandom` | CSPRNG çıktısı | Asla bloklanmaz | "Returns output from uninitialized CSPRNG (may be low entropy and unsuitable for cryptography)" |
| `getrandom(2)` | urandom kaynağı (varsayılan) | Havuz hazır olunca bloklanmaz | Havuz hazır olana kadar bloklanır |
| `getrandom(2)` + `GRND_RANDOM` | `/dev/random` kaynağı | Düşük entropy'de bloklanır | Havuz hazır olana kadar bloklanır |
| `getrandom(2)` + `GRND_NONBLOCK` | urandom | Bloklanmaz | `EAGAIN` |

**Ek kurallar (sayfa):** `getrandom(2)` Linux 3.17'den beri; taşınabilir sarmalayıcı
`getentropy(3)`; "Unless you are doing long-term key generation … you probably shouldn't
be reading from the /dev/random device"; Monte Carlo gibi olasılıksal uygulamalar için
miktarca bol veri çekmek "slow" ve gereksiz — kullanıcı alanı PRNG'sini bu arayüzlerle
tohumlamak yeterli. Kriptografik anahtar için gereken seed eşit büyüklüktür
(örnek: 3072-bit RSA/DH için efektif 128-bit → 16 bayt seed).

**Ses motoru ilişkisi:** ses zinciri için doğrudan kullanılmaz; CoreMusic PHP oturum/
token tarafının çekirdek CSPRNG'sine erişimi bu arayüzlerle olur (ADR-010 kapsamı —
bu dosyada detaylanmaz).

---

## §18 membarrier(2) — Kilitsiz Senkronizasyon Desteği

> Kaynak: `https://man7.org/linux/man-pages/man2/membarrier.2.html` (✅ HTTP 200 — 2026-10-12).

**Fikir (sayfa):** Eşleşen bellek bariyerlerinin "fast side"ı basit compiler bariyerine
(`asm volatile ("" : : : "memory")`), "slow side"ı `membarrier()` çağrısına çevirmek;
"as long as the slow side is infrequent enough" genel performans artar.

| Komut (Linux sürümü) | Garanti |
|---|---|
| `MEMBARRIER_CMD_QUERY` (4.3) | Desteklenen komut bit maskesi; QUERY kendisi maskede yok |
| `MEMBARRIER_CMD_GLOBAL` (4.16) | Sistemdeki tüm süreçlerin TÜM thread'leri syscall giriş-çıkışı arasında program sırasına uyar |
| `MEMBARRIER_CMD_GLOBAL_EXPEDITED` (4.16) | Kayıtlı süreçlerin çalışan thread'lerinde bariyer; "never block, but … extra overhead" |
| `MEMBARRIER_CMD_PRIVATE_EXPEDITED` (4.14) | Aynı süreç içindeki çalışan thread kardeşlerinde bariyer; önce kayıt (`REGISTER`) gerekir |
| `MEMBARRIER_CMD_PRIVATE_EXPEDITED_SYNC_CORE` (4.16) | Özel expedited + "core serializing instruction" garantisi |
| `MEMBARRIER_CMD_PRIVATE_EXPEDITED_RSEQ` (5.10) | rseq kritik bölümlerinin yeniden başlatılması; `MEMBARRIER_CMD_FLAG_CPU` + `cpu_id` ile tek CPU'ya indirgenebilir |
| `MEMBARRIER_CMD_SHARED` (4.3) | `MEMBARRIER_CMD_GLOBAL` takma adı ("header backward compatibility") |

**Kullanım kuralı (sayfa):** flags argümanı `MEMBARRIER_CMD_PRIVATE_EXPEDITED_RSEQ`
dışı tüm komutlarda 0 olmalıdır.

**Ses motoru çıkarımı (CoreMusic):** lock-free ring buffer'lar (ADR-017) atomik
release/acquire ile yürür; `membarrier()` çekirdek-içi senkronizasyonu kullanıcının
lehine çeviren bir seçenektir ama her çağrı bir syscall'dır ve "expedited" komutlar
"extra overhead" taşır — RT ses yolunda ölçülmeden kullanılmaz (K-Q2 ölçüm
kapsamında değerlendirilebilir).

---

## §19 pthread mutex (pthread_mutex_init(3))

> Kaynak: `https://man7.org/linux/man-pages/man3/pthread_mutex_init.3.html` (✅ HTTP 200 —
> 2026-10-12; sayfa: pthread_mutex_init/lock/trylock/unlock/destroy).

| Konu | Sayfa kuralı |
|---|---|
| Mutex durumları | "unlocked (not owned by any thread)" ve "locked (owned by one thread)"; "A mutex can never be owned by two different threads simultaneously" |
| Kind'lar | "fast" (varsayılan), "recursive", "error checking" — LinuxThreads modeli |
| Statik başlatma | `PTHREAD_MUTEX_INITIALIZER` · `PTHREAD_RECURSIVE_MUTEX_INITIALIZER_NP` · `PTHREAD_ERRORCHECK_MUTEX_INITIALIZER_NP` |
| Kendini tekrar kilitleme | fast → "effectively causing the calling thread to deadlock" · error-checking → anında `EDEADLK` · recursive → başarılı, unlock sayımı eşitlenince açılır |
| `pthread_mutex_trylock()` | Bloklamaz; kilit doluysa anında `EBUSY` |
| Error-checking/recursive unlock | Çalışma anında kilitli olup aynı thread'e ait olduğu kontrol edilir; fast mutex'te bu kontrol YOK — "non-portable behavior and must not be relied upon" |
| Cancellation | "None of the mutex functions is a cancelation point, not even pthread_mutex_lock()" — uzun süre mutex tutan deferred-cancellation thread'leri tehlikelidir |
| Async-signal safety | "not async-signal safe … calling pthread_mutex_lock() or pthread_mutex_unlock() from a signal handler may deadlock the calling thread" |
| Hata kodları | lock: `EINVAL` (hatalı başlatma), `EDEADLK` (yalnız error-checking) · trylock: `EBUSY`, `EINVAL` · unlock: `EINVAL` · init: her zaman 0 |

**Ses motoru çıkarımı:** §18 lock-free kuralı (ADR-017) gereği ses callback'i mutex
kullanmaz; bu sayfanın CoreMusic karşılığı, callback dışında kalan kontrol-yolu kodu
içindir — asenkron-signal güvenli olmadığından sinyal handler'ında mutex çağırmak
yasak (ses motoru sinyal handler'larında yalnız lock-free yapı kullanır).

---

## §20 Repo Kanıtları (CoreMusic disk — gerçek satırlar)

> Kural: aşağıdaki her madde `ReadAllLines` ile diskten okunmuş GERÇEK içeriği birebir taşır.

- Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 → `<?php declare(strict_types=1);`
  (uygulama-katmanı strict types — C tarafındaki strict/canonical kuralın karşılığı)
- Dosya: `api.coremusic.net/config/routes.php` / Satır: 36 →
  `'/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],`
  (Implemented/PLANNED ayrımı — bu dosyadaki ⚠️ işaretlerinin repo karşılığı)
- Dosya: `api.coremusic.net/config/routes.php` / Satır: 75 →
  `'password'    => ['required' => true, 'type' => 'string', 'min' => 8],`
  (girdi doğrulama — çekirdek-uzak politika katmanı)
- Dosya: `shared/composer.json` / Satır: 8 → `"php": ">=8.4",` (hedef çalışma zamanı)
- Dosya: `shared/composer.json` / Satır: 24 → `"phpunit/phpunit": "^10.5",` (test altyapısı)
- Dosya: `shared/composer.json` / Satır: 48 → `"stan": "phpstan analyse src --level=5"` (statik analiz)
- Dosya: `shared/src/Api/Routing/RouteTable.php` / Satır: 26 → `final class RouteTable` (routing çekirdeği)
- Dosya: `shared/src/Api/Middleware/RateLimitMiddleware.php` / Satır: 34 →
  `public function __invoke(array $request, callable $next): array` (middleware zinciri)
- Dosya: `shared/src/Cache/ApcuAdapter.php` / Satır: 13 → `$value = apcu_fetch($key, $success);`
  (çekirdek-uzak önbellek — çekirdek RNG/timekeeping ile karıştırılmaz)
- Dosya: `shared/tests/Api/RouteTableTest.php` / Satır: 87 →
  `public function testUnknownPathYields404Not405(): void` (test kapsamı kanıtı)
- Dosya: `.github/workflows/ci.yml` / Satır: 28 → `timeout-minutes: 15` (CI zaman sınırı)

**Çıkarım:** Repo PHP/JS tarafında IMPLEMENTED; `cpp=0` — çekirdeğe/ALSA'ya doğrudan
bağlanan C++ katmanı henüz yok (bu bölüm yalnız çekirdek-uzak disk kanıtlarını taşır).

---

## §21 Web Araştırma Tablosu (kernel — bu turda doğrulanan 8 URL)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| workqueue API dokümantasyonu canlı ve erişilebilir | https://docs.kernel.org/core-api/workqueue.html | 2026-10-12 | ✅ HTTP 200 |
| namespaces(7) man sayfası yayımlanmış | https://man7.org/linux/man-pages/man7/namespaces.7.html | 2026-10-12 | ✅ HTTP 200 |
| ktime accessors dokümanı çekirdek API ağacında | https://docs.kernel.org/core-api/timekeeping.html | 2026-10-12 | ✅ HTTP 200 |
| SCHED_DEADLINE tasarım dokümanı erişilebilir | https://docs.kernel.org/scheduler/sched-deadline.html | 2026-10-12 | ✅ HTTP 200 |
| Kbuild dokümantasyon indeksi yayında | https://docs.kernel.org/kbuild/index.html | 2026-10-12 | ✅ HTTP 200 |
| random(7) genel görünüm sayfası yayımlanmış | https://man7.org/linux/man-pages/man7/random.7.html | 2026-10-12 | ✅ HTTP 200 |
| membarrier(2) komut envanteri erişilebilir | https://man7.org/linux/man-pages/man2/membarrier.2.html | 2026-10-12 | ✅ HTTP 200 |
| pthread mutex arayüzü belgelidir | https://man7.org/linux/man-pages/man3/pthread_mutex_init.3.html | 2026-10-12 | ✅ HTTP 200 |

---

## İlişkili dosyalar

- `linux-anayasa.md` — üst anayasa (sürüm modeli, distro aileleri, katmanlar)
- `linux-api.md` — POSIX + Linux syscall'ları, /dev·sysfs, ALSA ioctl, container ilişkisi
- `linux-ses-stacki.md` — ALSA/PipeWire (bu dosyadaki kesme/RT çerçevenin uygulama alanı)
- `linux-web-arastirma.md` — linux dalı için doğrulanmış web araştırma kayıtları

---

**Authority:** Bayram Ali / Vault Steward · embedded-engineer subagent · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0) · ADR-019 ihlali yok · commit ATILMADI
**Sonraki adım:** `macos/` ve `windows/` P0 derin-dalları (bu görevin kalan bölümü); kernel tarafı açık konular §10 K-Q1–K-Q5.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode