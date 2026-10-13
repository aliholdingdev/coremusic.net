---
title: "Linux API — POSIX + Sistem Çağrıları, glibc/musl, /dev·sysfs, ALSA ioctl, Namespace/cgroup"
type: architecture-deep-dive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-api
node_type: module
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/linux
children: []
depends_on:
  - "linux/linux-anayasa.md"
  - "linux/linux-kernel.md"
evidence:
  - "https://man7.org/linux/man-pages/man2/syscall.2.html (2026-10-12 ✅ — tam metin: ABI tabloları, syscall() örneği)"
  - "https://musl.libc.org/ (2026-10-12 ✅ — 1.2.6, time64 notu, CVE danışmaları)"
  - "https://systemd.io/CGROUP_DELEGATION/ + /CONTAINER_INTERFACE/ (2026-10-12 ✅ — sayfa varlıkları)"
  - "github.com/juce-framework/JUCE juce_audio_devices.cpp (2026-10-12 ✅ arama — JUCE_ALSA/LINUX bloğu)"
validation: []
open_questions: []
group: mimari
---

# Linux API (linux-api.md)

> **ELI15:** Uygulama bilgisayardaki her şeyi doğrudan yapamaz; "lütfen dosyayı yaz"
> gibi talepleri **sistem çağrısı** (syscall) denen özel kapılardan geçirir. Bu kapıların
> liste/adresi mimariden mimariye değişir; libc bu kapıların sarmalayıcısıdır.

**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED · ABI-of-record: x86-64 (CoreMusic hedefi
— donanım kararı K-Q3'te açık).

---

## §1 POSIX ile Linux Arasındaki Fark

| Katman | Ne kapsar | Örnek | Durum |
|---|---|---|---|
| **POSIX** | Taşınabilir sistem arayüzü standardı (C API + davranış) | `open`, `read`, `pthread_create`, `mmap` | genel ⚠️ (ISO/IEEE standardı — bu oturumda sayfa çekilmedi) |
| **Linux** | POSIX'e ek, Linux'a özgü syscall'lar ve davranışlar | `epoll`, `eventfd`, `inotify`, `clone3`, `io_uring` ⚠️ | ✅ varlık (syscall(2) backlinklerinde `openat2`, `pidfd_*`, `userfaultfd`, `membarrier` vb.) |
| **glibc uzantıları** | GNU'ya özgü kütüphane fonksiyonları | `GNU_SOURCE` | ⚠️ |

**Kural (CoreMusic):** Ses motoru kodu POSIX çekirdeğinde kalmaya çalışır; Linux'a özgü
yol (epoll/mlockall) platform kabuğunda tutulur (JUCE soyutlaması zaten bunu yapar —
✅ upstream kanıt: `juce_audio_devices.cpp` platform blokları).

---

## §2 Sistem Çağrısı Mekanizması (✅ syscall(2) — man-pages 6.19, 2026-02-02)

### §2.1 Çağrı zinciri

```text
uygulama  →  libc sarmalayıcı (glibc/musl)  →  syscall talimatı (kullanıcı→çekirdek)
          ←  dönüş + errno(3)
```

- `syscall()` fonksiyonu (uzun çağrı numarası + değişken uzunlukta argüman listesi),
  sarmalayıcısı olmayan syscall'ları çağırmak
  için kullanılır (✅ birebir: "invoking a system call that has no wrapper function in
  the C library").
- Hata: `-1` dönüşü + `errno` (✅); `ENOSYS` = numara bu çekirdekte yok (✅).
- Sembolik numaralar: `<sys/syscall.h>` (✅).

### §2.2 Mimari ABI tabloları (✅ syscall(2) NOTES — birebir özet)

**x86-64 için:**

| Öğe | Değer |
|---|---|
| Çekirdeğe geçiş talimatı | `syscall` |
| Çağrı numarası registerı | `eax` |
| Dönüş registerları | `rax` (+ hata sinyali yok — hata `rax = -errno`) |
| Argüman registerları | `rdi`, `rsi`, `rdx`, `r10`, `r8`, `r9` |

> **Dikkat:** argüman 4'ün `r10` olması glibc sarmalayıcısının `rcx`'i korumak için
> gerçekleştirdiği dönüşümdür (syscall talimatı `rcx`'i ezer) — bu, `syscall()` ile elle
> çağırırken bilinmesi gereken tek sürprizdir (tablodan çıkarım ⚠️).

**Diğer mimarilerden seçmeler (✅ tam tablo syscall(2) sayfasında):**

| Mimari | Talimat | Numara reg. | Argüman regları |
|---|---|---|---|
| arm64 | `svc #0` | `w8` | `x0..x5` |
| i386 | `int $0x80` | `eax` | `ebx,ecx,edx,esi,edi,ebp` |
| riscv | `ecall` | `a7` | `a0..a5` |
| loongarch | `syscall 0` | `a7` | `a0..a6` |
| powerpc64 | `sc` | `r0` | `r3..r8` |

> **32-bit tehlikesi (✅):** ARM EABI'de 64-bit argüman çift register'a hizalanır;
> `syscall()` ile elle çağırırken `pread64`/`readahead` gibi çağrılar parçalanmak zorundadır
> (sayfadaki `readahead` örneği). MIPS O32'de argüman 5–8 yığındadır.

### §2.3 vDSO

`syscall(2)` SEE ALSO zinciri `vdso(7)`'yi işaret eder (✅) — `clock_gettime` gibi sıcak
yollar çekirdeğe gitmeden kullanıcı alanındaki sanal sayfadan çözülür (çekirdek
versiyonu ⚠️: vdso(7) bu oturumda çekilmedi).

---

## §3 Sistem Çağrısı Envanteri (CoreMusic ilgi alanına göre gruplu)

Aşağıdaki tablo **varlıklarını** `syscall(2)` backlink listesi + örneksel metinle
doğrulanmış çağrılardan ve bilinen POSIX arayüzünden kurar. x86-64 **numara** sütunu
`arch/x86/entry/syscalls/syscall_64.tbl` kaynaklıdır; bu oturumda dosya birebir
çekilmediği için numaralar `⚠️` işaretlidir (dogrulama dosyası Q9).

### §3.1 Dosya I/O

| Çağrı | Görev | Num (x86-64) ⚠️ | Ses motoru notu |
|---|---|---|---|
| `read` / `write` | Bloklu transfer | 0 / 1 | ALSA read/write yolu |
| `pread64` / `pwrite64` | Pozisyonlu transfer | 17 / 18 | Dosyadan seek'li okuma |
| `open` / `openat` / `openat2` | Dosya açma | 2 / 257 / 437 | `/dev/snd/pcmC*D*p` açılışı |
| `close` | Kapatma | 3 | Cihaz serbest bırakma |
| `fsync` / `fdatasync` | Disk senkronu | 74 / 75 | Konfigürasyon yazımı |
| `fcntl` | Dosya kontrolü (bloklama modu dahil) | 72 | `O_NONBLOCK` yönetimi |
| `ioctl` | Cihaza özel komut | 16 | **ALSA kalbi** (§5) |
| `mmap` / `munmap` / `mprotect` | Bellek eşleme | 9 / 11 / 10 | ALSA mmap buffer |
| `madvise` / `mlock` / `mlockall` | Bellek tavsiyesi/kilit | 28 / 149 / 152 | Real-time ön-tahsis |
| `readv` / `writev` / `preadv2` / `pwritev2` | Scatter/gather | 19 / 20 / 328 / 329 | `preadv2` backlink ✅ |

### §3.2 Süreç ve iplik (thread)

| Çağrı | Görev | Num ⚠️ | Not |
|---|---|---|---|
| `clone` / `clone3` | Süreç/iplik yaratma | 56 / 435 | `clone3` backlink ✅ |
| `fork` / `vfork` | Kopya süreç | 57 / 58 | |
| `execve` / `execveat` | Program çalıştırma | 59 / 322 | |
| `exit` / `exit_group` | Sonlanma | 60 / 231 | `exit_group` backlink ✅ |
| `wait4` / `waitid` | Çocuk bekleme | 61 / 247 | |
| `getpid` / `gettid` | Kimlik | 39 / 186 | örnek kod ✅ |
| `sched_setattr` / `sched_getattr` | Zamanlama öznitelikleri | 314 / 315 | EEVDF dilim talebi (§3.3) |
| `sched_setaffinity` | CPU bağlama | 203 | Real-time thread pinning |
| `setpriority` / `ioprio_set` | Öncelik | 141 / 251 | `ioprio_set` backlink ✅ |

### §3.3 Zamanlama/sinyal/timer

| Çağrı | Görev | Num ⚠️ | Not |
|---|---|---|---|
| `clock_gettime` / `clock_settime` | Saat okuma | 228 / 227 | vdso hızlandırması ⚠️ |
| `nanosleep` / `clock_nanosleep` | Uyku | 35 / 230 | |
| `timerfd_create` / `timerfd_settime` | Zamanlayıcı fd | 283 / 286 | Ses tick alternatifi ⚠️ |
| `rt_sigaction` / `rt_sigprocmask` | Sinyal kurulumu | 13 / 14 | |
| `tgkill` | Süreç-iplik sinyali | 234 | örnek kod ✅ |
| `epoll_create1` / `epoll_ctl` / `epoll_wait` | I/O demultiplex | 291 / 233 / 232 | `epoll_ctl` backlink ✅ |
| `poll` / `ppoll` | I/O bekleme | 7 / 271 | |
| `select` / `pselect6` | Eski bekleme | 23 / 270 | |

### §3.4 Bellek ve IPC

| Çağrı | Görev | Num ⚠️ | Not |
|---|---|---|---|
| `brk` | Yığın genişletme | 12 | |
| `mmap`/`munmap` | Harici bellek | 9 / 11 | PipeWire "shared ringbuffer" ilişkisi → `linux-ses-yigini.md` |
| `pipe2` | Boru | 293 | `pipe` backlink ✅ |
| `eventfd2` | Olay sayacı | 290 | Uyandırma mekanizması ⚠️ |
| `futex` | Hızlı kullanıcı kiliti | 202 | Ses kilidi yolları ⚠️ |
| `memfd_create` | Bellek dosyası | 319 | Paylaşımlı buffer adayı ⚠️ |
| `socket`-aile | Ağ | 41–59 | D-Bus/PipeWire socket yolu |

### §3.5 Güvenlik/izolasyon ve container

| Çağrı | Görev | Num ⚠️ | İlişki |
|---|---|---|---|
| `unshare` | Namespace ayırma | 272 | container (§6) |
| `setns` | Namespace katılma | 308 | |
| `clone3` + `CLONE_NEW*` | Namespace'li süreç | 435 | |
| `seccomp` | Syscall filtresi | 317 | backlink ✅ (seccomp(2)) |
| `bpf` / `perf_event_open` | Çekirdek içi program/ölçüm | 321 / 298 | latency ölçümü; backlink ✅ (`perf_event_open(2)`) |
| `capget` / `capset` | Yetki kümesi | 125 / 126 | backlink ✅ (`capget(2)`) |
| `userfaultfd` | Kullanıcı sayfa hatası | 323 | backlink ✅ |
| `pidfd_open` / `pidfd_getfd` | Süreç fd'si | 434 / 438 | backlink ✅ |

---

## §4 Sıcak Yol Profili — Ses Motoru Çağrı Seti (CoreMusic şartname çıkarımı)

| Yol | Çağrı seti | Sıklık | Not |
|---|---|---|---|
| Başlatma | `openat` → `ioctl(HW_PARAMS)` → `mmap` → `ioctl(PREPARE)` | bir kez | mmap tercih edilir ⚠️ |
| Akış döngüsü | `ioctl(WRITEI_FRAMES)` veya mmap + `ioctl(START/DROP)` + `poll/epoll` | her period | ioctl isimleri §5 |
| Zamanlama | `clock_gettime` (vdso) veya `epoll` timeout | her döngü | |
| Kapanış | `ioctl(DROP)` → `munmap` → `close` | bir kez | |
| Real-time koruma | `mlockall(MCL_CURRENT|MCL_FUTURE)` + `sched_setaffinity` | bir kez | PREEMPT_RT ile anlamlı (kernel §7.4) |

> ⚠️ Bu profil şartname düzeyindedir; JUCE ALSA backend'i zaten bu yolu kendi içinde
> yapar (upstream kanıt: `juce_ALSA_linux.cpp` — §5.4). Neva Engine'in **ham ALSA**
> kullanıp kullanmayacağı karar değil, açık sorudur (Q10).

---

## §5 /dev, sysfs ve Ses Cihaz Arayüzü (ioctl ile ALSA)

### §5.1 Dosya sistemi arayüzleri

| Arayüz | Yol | İçerik | Çekirdek sağlayıcı |
|---|---|---|---|
| devtmpfs | `/dev` | Cihaz düğümleri (ör. `/dev/snd/*`) | sürücüler |
| procfs | `/proc` | Süreçler + `/proc/asound` (ALSA) | ✅ (kernel docs: `alsa-configuration.html#proc-interfaces-proc-asound`) |
| sysfs | `/sys` | Cihaz ağacı/özellikler | kernel |
| udev | `/dev` yönetimini kullanıcı alanında tamamlayan | systemd-udev | ⚠️ (systemd.io ana sayfasında "utilities to control basic system configuration" genel çerçevesi ✅; udev sayfası çekilmedi) |

### §5.2 ALSA cihaz düğümleri (⚠️ çekirdek docs'tan türetilmiş — sayfa çekildi, düğüm adları genel ALSA sözleşmesi)

| Düğüm | Anlamı |
|---|---|
| `/dev/snd/controlC0` | Kart 0 mixer/kontrol |
| `/dev/snd/pcmC0D0p` | Kart 0 · Cihaz 0 · **playback** |
| `/dev/snd/pcmC0D0c` | Kart 0 · Cihaz 0 · **capture** |
| `/dev/snd/timer` | ALSA zamanlayıcı |
| `/dev/snd/seq` | ALSA sequencer (MIDI) — kernel docs: `designs/seq-oss.html` ✅ varlık |

### §5.3 ioctl komut ailesi (⚠️ — ALSA kelimeleri; of-record: alsa-lib `control.h`/`pcm.h`)

| ioctl | Görev |
|---|---|
| `SNDRV_PCM_IOCTL_HW_PARAMS` | Format/kanal/örneklem/period/buffer negotiate |
| `SNDRV_PCM_IOCTL_HW_FREE` | Parametreleri serbest bırak |
| `SNDRV_PCM_IOCTL_PREPARE` | Cihazı akışa hazırla |
| `SNDRV_PCM_IOCTL_START` / `DROP` / `DRAIN` | Başlat / kes / bitişini bekle |
| `SNDRV_PCM_IOCTL_WRITEI_FRAMES` / `READI_FRAMES` | İkili (interleaved) veri transferi |
| `SNDRV_PCM_IOCTL_SYNC_PTR` | Durum paylaşma (mmap modu) |

> ⚠️ Bu isimler ALSA'nın genel sözleşmesidir; bu oturumda alsa-lib başlıkları
> çekilmedi (alsa-project.org wiki'si 500 döndü — `linux-dogrulama.md` Q10).
> Çekirdek tarafı of-record: docs.kernel.org `/sound/kernel-api/alsa-driver-api.html` ✅ varlık.

### §5.4 JUCE ALSA backend zinciri (✅ upstream kanıt — 2026-10-12)

| Adım | Kanıt (juce-framework/JUCE `juce_audio_devices.cpp`) |
|---|---|
| Platform seçimi | `#elif JUCE_LINUX || JUCE_BSD` bloğu |
| Kütüphane | `#include <alsa/asoundlib.h>` + "The package you need to install to get ALSA support is **libasound2-dev**" |
| Zayıf bağlama | `native/juce_ALSA_weak_linux.h` (dönüşük sürümlere uyum ⚠️ yorum) |
| Backend dosyası | `native/juce_ALSA_linux.cpp` |
| Fabrika | `AudioIODeviceType::createAudioIODeviceType_ALSA()` → `createAudioIODeviceType_ALSA_PCMDevices()` (✅) |
| JACK alternatifi | `JUCE_JACK` → `native/juce_JackAudio.cpp` (Linux/BSD/Mac/Windows) ✅ |

---

## §6 Namespace ve cgroup (Container ilişkisi — K018 bağlantısı)

### §6.1 Namespace türleri (⚠️ genel Linux arayüzü; syscall varlıkları ✅)

| Tür | Ne yalıtır | Container rolü |
|---|---|---|
| `pid` | Süreç numarası uzayı | İçerideki "PID 1" |
| `mnt` | Dosya sistemi ağacı | Root filesystem |
| `net` | Ağ arayüzleri/portlar | Ağ izolasyonu |
| `uts` | Hostname | |
| `ipc` | SysV/POSIX IPC | |
| `user` | Kullanıcı kimlikleri | Root olmadan root hissi |
| `cgroup` | cgroup ağacı görünümü | |
| `time` ⚠️ | Zaman namespace'i | yeni; durum doğrulanmadı |

### §6.2 cgroup v2 (✅ systemd.io varlıkları)

- systemd "keeps track of processes using **Linux control groups**" (✅ systemd.io metni).
- Resmi arayüz sayfaları: `/CGROUP_DELEGATION/` · `/CONTAINER_INTERFACE/` ·
  `/VM_INTERFACE/` · `/PAX_CONTROL_GROUPS/` (✅ bağlantı envanteri).
- Tipik denetleyiciler (⚠️ — sayfa metni çekilmedi, genel bilgi): `memory`, `cpu`,
  `io`, `pids`, `cpuset`.

### §6.3 K018 bağlantısı (disk kanıtı)

| İddia | Kanıt |
|---|---|
| "CI/CD, konteyner, deploy" sahipliği K018-devops'tadır | `K000-isletim-sistemi/os-master.md` satır 37 |
| "Build/çalıştırma ortamı OS sürümüne bağlıdır" (K018 → K000 bağımlılığı) | `os-bagimliliklar.md` satır 32 |
| Bu dal namespace/cgroup'u **yalnız kavramsal** anlatır; orkestrasyon K018'de | `os-master.md` satır 149 ("yalnız hizmet sağlar" iddiası — ⚠️ onay bekliyor) |

**Sınır:** Docker/Podman/k8s operasyonel detayı bu dosyada **yoktur** (K018-devops).

---

## §7 libc Karşılaştırması: glibc vs musl

| Öğe | glibc (GNU C Library) | musl |
|---|---|---|
| Tanım | Linux'un yaygın libc'si (varsayılan ⚠️) | "an implementation of the C standard library built on top of the Linux system call API" ✅ (musl.libc.org) |
| Son sürüm (2026-10-12) | ⚠️ bu oturumda doğrulanmadı | **1.2.6** ✅ (sayfada "Latest release: musl-1.2.6.tar.gz") |
| Lisans | LGPL ⚠️ | MIT ⚠️ |
| Boyut/performans | Tam özellikli, daha büyük ⚠️ | "lightweight, fast, simple" ✅ (kendi tanımı) |
| Distro evi | Debian/Ubuntu/Fedora/SUSE/Arch (§5 tablosu) | Alpine tarzı sistemler ⚠️ (eşleşme doğrulanmadı) |
| Bilinen güvenlik danışmaları (2026-10-12 ✅ musl sitesi) | — | CVE-2026-40200 (32-bit) · CVE-2026-6042 (iconv) · CVE-2025-26519 · CVE-2020-28928 |
| 32-bit notu | — | time64 geçişi: 1.1.x → 1.2 serisinde 64-bit `time_t` (✅ "should read the time64 release notes") |
| Çapraz derleme | toolchain ⚠️ | ✅ "Automated cross toolchain builder: musl-cross-make" |

**CoreMusic çıkarımı:** PHP (ext-apcu, ext-pdo) ve JUCE/libasound bağımlılıkları glibc
ekosisteminde doğrulanmıştır; musl hedefi **ayrı bir doğrulama** gerektirir
(anayasa K3 / Q6).

---

## §8 Sarmalayıcı Kütüphaneler ve Katman İlişkisi

```text
Neva Engine (C++20) ──── JUCE AudioDeviceManager
                           │
                           ├─ (Linux) juce_ALSA_linux.cpp ─→ libasound (alsa-lib)
                           │                                   │ ioctl/mmap
                           │                                   ▼
                           │                              /dev/snd/pcmC*D*p
                           │                                   │
                           └─ (Linux opsiyon) PipeWire client → PipeWire daemon
                                                                  │
                                                     ALSA hw erişimi (sunucu sahibi)
```

| Katman | Yazar | Durum |
|---|---|---|
| Ham ALSA ioctl | Uygulama | opsiyonel ⚠️ |
| alsa-lib (libasound) | ALSA projesi | ⚠️ ana sayfa erişilemedi (500) — kanıt boşluğu |
| JUCE ALSA backend | JUCE upstream | ✅ (§5.4) |
| PipeWire + SPA | PipeWire projesi (1.6.9 ✅) | → `linux-ses-yigini.md` |

---

## §9 Açık Sorular (API)

| # | Soru | Yönlendirme |
|---|---|---|
| Q8 | `sched(7)` sayfası çekilmedi — öncelik sınıfları ve izin modeli (RLIMIT_RTPRIO) teyit edilecek | research |
| Q9 | x86-64 syscall numaraları `syscall_64.tbl` üzerinden birebir doğrulanacak | research |
| Q10 | alsa-lib ioctl isimleri `alsa/pcm.h` üzerinden teyit edilecek (wiki 500 döndü) | research |
| Q11 | `io_uring` ses yollarında kullanılabilir mi? (bu envanterde kapsam dışı bırakıldı) | embedded-engineer |
| Q12 | musl hedefi gerçek mi? (PHP eklentileri dahil bağımlılık matrisi) | backend-architect |
| Q13 | Namespace `time` desteği hedef kernel'de var mı? | research |

---

## §10 Kaynakça (API)

| # | Kaynak | Erişim | Durum |
|---|---|---|---|
| A1 | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-12 | ✅ tam metin (ABI tabloları, örnek) |
| A2 | https://man7.org/linux/man-pages/man2/syscalls.2.html | — | ⚠️ çekilmedi (envanter of-record) |
| A3 | https://man7.org/linux/man-pages/man7/epoll.7.html | — | ⚠️ çekilmedi |
| A4 | https://man7.org/linux/man-pages/man7/vdso.7.html | — | ⚠️ (backlink ✅ var) |
| A5 | https://musl.libc.org/ | 2026-10-12 | ✅ |
| A6 | https://systemd.io/ (CGROUP_DELEGATION · CONTAINER_INTERFACE · VM_INTERFACE) | 2026-10-12 | ✅ (bağlantı envanteri) |
| A7 | https://docs.kernel.org/sound/index.html | 2026-10-12 | ✅ (`/proc/asound`, `seq-oss`, `kernel-api`) |
| A8 | https://github.com/juce-framework/JUCE (juce_audio_devices.cpp) | 2026-10-12 | ✅ (ALSA/JACK/ASIO blokları) |
| A9 | https://git.kernel.org/pub/scm/linux/kernel/git/torvalds/linux.git/tree/arch/x86/entry/syscalls/syscall_64.tbl | — | ⚠️ (numara of-record — Q9) |
| A10 | https://www.alsa-project.org/ | 2026-10-12 | ⚠️ wiki 500 döndü (Q10) |

---

## §11 POSIX Hizalama Sütunu — Ailelerin man Sayfalarına Göre Gruplanması

§1'deki "POSIX çekirdeğinde kal" kuralı burada somutlaştırılır: hangi arayüz ailesi
hangi standart gövdesine bağlanır, hangisi Linux uzantısıdır.

| Aile | Örnek arayüzler | Dayandığı gövde | Durum |
|---|---|---|---|
| Standart dosya/IPC | dosya tanıtıcısı taşıma, adlandırılmış boru (named pipe) | POSIX | ✅ varlık (aşağıdaki §12) |
| Zamanlama/öncelik | süreç/iş parçacığı öncelik arayüzleri | POSIX + Linux uzantıları | ⚠️ (bkz. §18) |
| Bellek | paylaşılan bellek, sayfa taşıma | POSIX + Linux uzantıları | ⚠️ (bkz. §12 · §15) |
| Olay bildirimi | epoll, inotify | **yalnız Linux** | ✅ Linux uzantısı |
| Asenkron E/S | aio | POSIX.1-2001 (opsiyonel bölüm) | ⚠️ (bkz. §14) |
| Uyum kontrolü | Standartların listelendiği genel sayfa | POSIX uyum belgesi | https://man7.org/linux/man-pages/man7/standards.7.html |

### §11.1 Hizalama kuralının ses motoruna uygulanması

| Kural | Uygulama | Dosya |
|---|---|---|
| POSIX çekirdeği | ring buffer senkronizasyonu taşınabilir semafor/fifo üzerinde kurulur | bu dosya §12 |
| Linux uzantısı | epoll tabanlı olay döngüsü platform kabuğunda tutulur | bu dosya §13 |
| Sınır | Linux'a özgü arayüz CoreMusic'in paylaşılan C++20 çekirdeğine sızdırılmaz | `linux-anayasa.md` §6.1 |

---

## §12 Bellek Paylaşımı ve Dosya Tabanlı Senkronizasyon

| Arayüz | Ne sağlar | CoreMusic ilgisi | Kaynak |
|---|---|---|---|
| Paylaşılan bellek | Bellek haritası üzerinden süreçler arası veri paylaşımı | IO thread'i ile DSP zinciri arasında kopyasız veri paylaşımı tasarımı (kod PLANNED) | https://man7.org/linux/man-pages/man7/shm_overview.7.html |
| Adlandırılmış boru (FIFO) | Dosya sistemi adıyla sunulan tek yönlü kanal | düşük hacimli kontrol mesajları için aday; ses verisi için değil | https://man7.org/linux/man-pages/man7/fifo.7.html |
| POSIX semafor | dosya sistemi üzerinden adlandırılmış senkronizasyon | paylaşılan bellek ile birlikte düşünülür | §17 (kapsam notu) |

### §12.1 Paylaşılan bellek sözleşmesi (taslak — kod yok)

| Sözleşme elemanı | Karar | Gerekçe |
|---|---|---|
| Veri tipi | 32-bit float (ADR-017) | vault kuralı |
| Boyut | 512 örnek tampon, 48 kHz (sartname) | vault kuralı |
| Erişim | tek yazıcı / çok okuyucu | paylaşımlı bellek modeli |
| Durum | ⚠️ PLANNED | repoda C++ kodu yok (`projects/NevaEngine` 0 dosya) |

---

## §13 Olay Döngüsü Arayüzleri (Linux uzantısı)

| Arayüz | Ne yapar | Uygun olduğu yer | Kaynak |
|---|---|---|---|
| epoll oluşturma | Olay ilgi kümesi oluşturur ve çoklu beklemeyi tek çağırımda toplar | IO thread'inin cihaz/pipe beklemesi | https://man7.org/linux/man-pages/man2/epoll_create.2.html |
| inotify | Dosya sistemi değişiklik bildirimi | yapılandırma dosyası izleme (ses motoru ses yolunda değil) | https://man7.org/linux/man-pages/man7/inotify.7.html |
| POSIX karşılığı | poll/select ailesi | taşınabilirlik gereken katman | §11 tablosu |

### §13.1 Olay döngüsü deseninin CoreMusic eşlemesi

| Katman | Desen | Durum |
|---|---|---|
| IO thread'i | epoll ile cihaz olayı bekleme | tasarım iddiası ⚠️ (kod yok) |
| Web katmanı | PHP event döngüsü yok (istemci-yan istek modeli) | repo gerçeği (§19) |
| Test katmanı | Node test çalıştırıcısı olay döngüsü kullanır | package.json kanıtı (§19) |

---

## §14 Asenkron E/S — Çekirdek Arayüzü ve Kapsam Dışı Bırakılma Gerekçesi

| Arayüz | Ne yapar | CoreMusic tutumu | Kaynak |
|---|---|---|---|
| aio kurulumu | Çekirdek düzeyinde asenkron E/S bağlamı oluşturur | dosya tabanlı E/S için tasarlandı; ses cihazı E/S'si ALSA üzerinden yürür | https://man7.org/linux/man-pages/man2/io_setup.2.html |
| aio genel görünüm | arayüz ailesinin tamamı | kapsam dışı bırakma gerekçesi bu tabloda | https://man7.org/linux/man-pages/man7/aio.7.html |
| Alternatif | epoll + eşzamanlı (sync) ALSA yolu | ses yığını dosyasının konusu | `linux-ses-yigini.md` |

### §14.1 Neden aio kullanılmıyor (belge düzeyi gerekçe)

| Gerekçe | Açıklama | İşaret |
|---|---|---|
| Uyum | ses cihazı E/S'si dosya E/S'si değildir | ✅ kavramsal |
| Ölçüm | gerçek gecikme ölçümü yapılmadı | ⚠️ AÇIK (Q11) |
| Karar | bu dosya yeni karar almaz, yalnız boşluğu işaretler | §9 K-serisi |

---

## §15 Bellek ve Zamanlama Kontrol Arayüzleri

| Arayüz | Ne düzenler | CoreMusic ilgisi | Kaynak |
|---|---|---|---|
| Sayfa taşıma | Bellek sayfalarını başka NUMA düğümüne taşıma | gerçek zamanlı hedefte sayfa göçü istenmez | https://man7.org/linux/man-pages/man2/migrate_pages.2.html |
| userfaultfd | Sayfa hatası bildirimini kullanıcı alanına açar | ön-tahsis doğrulaması için araştırma adayı | https://man7.org/linux/man-pages/man2/userfaultfd.2.html |
| prctl | Süreç ayarları (kaynak limitleri, ad, ölüm davranışı) | IO thread'i ve çekirdek politikası için aday | https://man7.org/linux/man-pages/man2/prctl.2.html |
| ioprio_set | Giriş/çıkış öncelik sınıfı | disk E/S önceliklendirme; ses verisi disk E/S değildir | https://man7.org/linux/man-pages/man2/ioprio_set.2.html |

### §15.1 Sayfa kilit disiplini ile ilişkisi

| Önlem | Arayüz | Durum |
|---|---|---|
| Bellek kilidi | `mlock`/`mlockall` ailesi | ⚠️ bu envanterde sayfa çekilmedi (Q-serisi) |
| Göç önleme | migrate_pages tersinden | §15 satırı |
| Doğrulama | sayfa hatası sayacı | ⚠️ VERIFICATION REQUIRED (kod yok) |

---

## §16 Güvenlik ve Kısıt Arayüzleri

| Arayüz | Ne sağlar | CoreMusic ilgisi | Kaynak |
|---|---|---|---|
| landlock kuralları | Süreç için dosya sistemi kısıt politikası kurar | web servisi için savunma derinliği adayı | https://man7.org/linux/man-pages/man2/landlock_create_ruleset.2.html |
| unshare | Süreç için yeni ad alanı (namespace) kopyası oluşturur | konteyner/izolasyon konusunun komut düzeyi yüzeyi | https://man7.org/linux/man-pages/man2/unshare.2.html |

### §16.1 Ad alanı / kısıt ilişkisi (K018 sınırı)

| Konu | Sahibi | Bu dosyadaki rolü |
|---|---|---|
| namespace/cgroup operasyonu | K018-devops | yalnız API yüzeyi tanıtılır |
| konteyner image | K018-devops | kapsam dışı (`linux-anayasa.md` §8) |
| kısıt politikası tasarımı | security-engineer | ⚠️ AÇIK (bu dosya karar almaz) |

---

## §17 POSIX SysV IPC ve Kuyruklar — Kapsam Dışı Bırakılma

| Arayüz | Ne sağlar | CoreMusic tutumu | Kaynak |
|---|---|---|---|
| SysV IPC genel görünüm | Semaför, mesaj kuyruğu, paylaşılan bellek üçlüsü | yalnız tanımlayıcı tablo | https://man7.org/linux/man-pages/man7/sysvipc.7.html |
| POSIX mesaj kuyruğu | Dosya sistemi tabanlı kuyruk | ses verisi için ölçek uygun değil | https://man7.org/linux/man-pages/man7/mq_overview.7.html |
| POSIX semafor | Adlandırılmış/anonim semafor | paylaşılan bellek senkronizasyonu için aday | https://man7.org/linux/man-pages/man7/sem_overview.7.html |

### §17.1 IPC seçimi karar matrisi

| Seçenek | Artı | Eksi | Karar |
|---|---|---|---|
| SysV semafor | eski ve yaygın | arayüz kaba | ⚠️ değerlendirmede |
| POSIX semafor | taşınabilir | isim yönetimi | ⚠️ değerlendirmede |
| atomik + kilitsiz kuyruk | kilit yok | tasarım yükü | çekirdek tasarım adayı (§12.1) |

---

## §18 İpuçları: İş Parçacığı Arayüzleri (pthread) ile Zamanlama Kontrolü

| Arayüz | Ne yapar | CoreMusic ilgisi | Kaynak |
|---|---|---|---|
| pthread öznitelik başlatma | İş parçacığı yığın/ayırt etme özniteliklerini hazırlar | IO thread'i oluşturma önceliği (yığın boyutu) | https://man7.org/linux/man-pages/man3/pthread_attr_init.3.html |
| pthread çekirdek bağlama | İş parçacığını belirli işlemci çekirdeğine bağlar | gerçek zamanlı thread'in çekirdek sabitlemesi | https://man7.org/linux/man-pages/man3/pthread_setaffinity_np.3.html |

### §18.1 pthread arayüzünün CoreMusic eşlemesi

| Thread | Gerekli kontrol | Durum |
|---|---|---|
| IO thread'i | çekirdek bağlama + öncelik | ⚠️ PLANNED (kod yok) |
| DSP zinciri thread'i | çekirdek bağlama + sayfa kilidi | ⚠️ PLANNED |
| Test çalıştırıcısı | gerek yok | package.json kanıtı (§19) |

---

## §19 Repo Disk Kanıtları — PHP/Node Yüzeyinin Gerçek Çağrı Profili

Bu bölüm API envanterini repodaki gerçek kodla eşler. Satır numaraları 2026-10-12'de
okunmuştur; uydurma satır numarası kullanılmamıştır.

| Kanıt | Dosya / Satır | İçerik | API katmanı karşılığı |
|---|---|---|---|
| strict_types | Dosya: api.coremusic.net\config\routes.php / Satır: 1 → `<?php declare(strict_types=1);` | dil düzeyi tip disiplini | syscall öncesi kullanıcı alanı katmanı |
| servis yönlendirme | Dosya: api.coremusic.net\config\routes.php / Satır: 27 → `'/api/v1/auth'     => ['service' => 'auth',     'handler' => 'authController'],` | yönlendirme tablosu satırı | §13'teki olay yönlendirmesinin PHP karşılığı |
| uygulanmamış rota | Dosya: api.coremusic.net\config\routes.php / Satır: 36 → `'/api/v1/auth/login'          => ['service' => 'auth', 'handler' => 'authController', 'public' => true, 'implemented' => false],` | rota beyan edilmiş ama uygulanmamış | API sözleşmesi disiplini (IMPLEMENTED/PLANNED) |
| girdi doğrulama | Dosya: api.coremusic.net\config\routes.php / Satır: 75 → `'password'    => ['required' => true, 'type' => 'string', 'min' => 8],` | şema doğrulama kuralı | kullanıcı girdisi sınırları |
| route tablosu sınıfı | Dosya: shared\src\Api\Routing\RouteTable.php / Satır: 26 → `final class RouteTable` | nihai sınıf | API katmanı iskeleti |
| middleware | Dosya: shared\src\Api\Middleware\RateLimitMiddleware.php / Satır: 34 → `public function __invoke(array $request, callable $next): array` | ara katman zinciri | istek sınıflandırma |
| test davranışı | Dosya: shared\tests\Api\RouteTableTest.php / Satır: 67 → `public function testPostLoginDeclaredButNotImplementedYields405WithAllow(): void` | uygulanmamış rota testi | doğrulama disiplini |
| test davranışı | Dosya: shared\tests\Api\RouteTableTest.php / Satır: 87 → `public function testUnknownPathYields404Not405(): void` | bilinmeyen yol testi | hata sınıflandırma |
| önbellek arayüzü | Dosya: shared\src\Cache\ApcuAdapter.php / Satır: 9 → `if (!function_exists('apcu_fetch')) {` | çalışma zamanı yetenek kontrolü | §15.1'deki "capability check" deseni |
| önbellek okuma | Dosya: shared\src\Cache\ApcuAdapter.php / Satır: 13 → `$value = apcu_fetch($key, $success);` | bellek içi okuma | paylaşımlı bellek okuma modeli (§12) |
| uzantı zorunluluğu | Dosya: shared\composer.json / Satır: 12 → `"ext-pdo": "*",` | PHP eklenti bağımlılığı | çalışma zamanı yetenek envanteri |
| test sürümü | Dosya: shared\composer.json / Satır: 24 → `"phpunit/phpunit": "^10.5",` | test çatısı | doğrulama zinciri |
| statik analiz | Dosya: shared\composer.json / Satır: 48 → `"stan": "phpstan analyse src --level=5"` | statik analiz komutu | derleme-öncesi denetim |
| JS testi | Dosya: package.json / Satır: 5 → `"test:js": "vitest run --root assets.coremusic.net",` | JS test betiği | Node test zinciri |
| E2E testi | Dosya: package.json / Satır: 8 → `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"` | E2E test betiği | uçtan uca doğrulama |

### §19.1 Çağrı profili özeti

| Yüzey | Gerçek kanıt | Linux API bağlamı |
|---|---|---|
| PHP uzantıları | composer.json L12 (`ext-pdo`) + ApcuAdapter L9/L13 | çalışma zamanı yetenek kontrolü §15.1 |
| Statik denetim | composer.json L48 (phpstan) | derleme-öncesi denetim |
| Test zinciri | package.json L5 · L8 · composer.json L24 | §19 tablosu |
| Sözleşme disiplini | routes.php L36 + RouteTableTest L67/L87 | IMPLEMENTED/PLANNED ayrımı |

---

## §20 Web Araştırma Tablosu (bu dosyanın doğrulanan iddiaları)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| POSIX uyum belgesi yayımlanmıştır | https://man7.org/linux/man-pages/man7/standards.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| paylaşılan bellek genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/shm_overview.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| adlandırılmış boru belgesi yayımlanmıştır | https://man7.org/linux/man-pages/man7/fifo.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| epoll oluşturma arayüzü belgelidir | https://man7.org/linux/man-pages/man2/epoll_create.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| inotify genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/inotify.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| aio kurulum arayüzü belgelidir | https://man7.org/linux/man-pages/man2/io_setup.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| aio genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/aio.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| sayfa taşıma arayüzü belgelidir | https://man7.org/linux/man-pages/man2/migrate_pages.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| userfaultfd arayüzü belgelidir | https://man7.org/linux/man-pages/man2/userfaultfd.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| prctl arayüzü belgelidir | https://man7.org/linux/man-pages/man2/prctl.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| ioprio_set arayüzü belgelidir | https://man7.org/linux/man-pages/man2/ioprio_set.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| landlock kural oluşturma arayüzü belgelidir | https://man7.org/linux/man-pages/man2/landlock_create_ruleset.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| unshare arayüzü belgelidir | https://man7.org/linux/man-pages/man2/unshare.2.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| SysV IPC genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/sysvipc.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| POSIX mesaj kuyruğu genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/mq_overview.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| POSIX semafor genel görünümü yayımlanmıştır | https://man7.org/linux/man-pages/man7/sem_overview.7.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| pthread öznitelik başlatma belgelidir | https://man7.org/linux/man-pages/man3/pthread_attr_init.3.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| pthread çekirdek bağlama belgelidir | https://man7.org/linux/man-pages/man3/pthread_setaffinity_np.3.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |

---

## İlişkili dosyalar

- `linux-kernel.md` — syscall'ların hedefi olan çekirdek (EEVDF, PREEMPT_RT)
- `linux-ses-stacki.md` — §5 ALSA arayüzünün tamamlanmış yığını
- `linux-web-arastirma.md` — linux dalı için doğrulanmış web araştırma kayıtları
- `../isletim-sistemi-api/` — üst kademe API alt dalı (sınır: bu dosya Linux-özgündür)

---

**Authority:** Bayram Ali / Vault Steward · embedded-engineer subagent · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0) · commit ATILMADI
**Sonraki adım:** `linux-kernel.md` derinleştirme bu görevde tamamlandı (§12–§21); sıradaki: `macos/` ve `windows/` P0 derin-dalları.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode