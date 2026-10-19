---
title: "macOS Kernel — XNU: Mach + BSD Hibrit, IOProc/QoS Zamanlaması, IOKit"
type: architecture-deepdive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-kernel
node_type: module
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/macos
children: []
siblings: [macos-anayasa, macos-api, macos-framework, macos-ses-yigini, macos-dogrulama]
evidence:
  - "https://github.com/apple-oss-distributions/xnu (README + dosya ağacı, 2026-10-12)"
  - "https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/Mach/Mach.html (Mach, 2026-10-12)"
  - "https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/scheduler/scheduler.html (zamanlayıcı, 2026-10-12)"
  - "https://github.com/apple-oss-distributions/xnu/blob/main/osfmk/kern/thread_policy.c (QoS öncelik tablosu, 2026-10-12)"
  - "https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/IOKit/IOKit.html (IOKit, 2026-10-12)"
  - "https://en.wikipedia.org/wiki/Xnu (tarih/sözleşme, 2026-10-12)"
validation: []
open_questions:
  - "macOS 27 Golden Gate XNU kaynak ağacında hangi dosyalar değişti? (xnu-13432 diff'ı bu turda incelenmedi)"
  - "Gerçek-zamanlı band davranışının Apple silicon P/E çekirdeklerinde ölçümü → qa-engineer / performance-engineer"
  - "QoS numeric eşdeğerlikleri (BASEPRI_*) sembolik; tam sayısal değerler ARCH koda bağlı → ⚠️"
group: mimari
---

# macOS Kernel (XNU) — K000 · isletim-sistemi-turu · macos

**ELI15:** macOS'un kalbi **XNU** adlı hibrit çekirdektir: Carnegie Mellon'un **Mach**
microkernel parçaları + **BSD** (Unix) katmanı + **IOKit** (sürücü framework'ü) tek çekirdek
adres uzayında toplanmıştır. "X is Not Unix" demektir. Ses için kritik kısmı: zamanlayıcı
**gerçek-zamanlı (real-time) iş parçacığı bandını** destekler ve Apple bunu doğrudan
CoreAudio ile kullanır.

---

## §1 Amaç ve Kapsam

| Bu dosya | Kapsam dışı |
|---|---|
| XNU'nun üç bileşeni (Mach/BSD/IOKit) | Kullanıcı uzayı API listesi → `macos-api.md` |
| Mach scheduling + QoS sınıfları + RT bandı | Ses yolunun uçtan uca diyagramı → `macos-ses-yigini.md` |
| BSD syscall katmanının rolü | Framework envanteri → `macos-framework.md` |
| IOKit sürücü modeli (families/drivers/nubs) | İddia·URL doğrulama tablosu → `macos-dogrulama.md` |
| Apple açık kaynak deposu kanıtı | macOS sürüm ailesi → `macos-anayasa.md` §4 |

**Durum etiketi:** Şartname `IMPLEMENTED (belge)` · XNU kodu Apple'a ait (APSL, dış
bağımlılık) · CoreMusic çekirdek katmanına **girmez** (kod: `⚠️ PLANNED`, cpp=0).

---

## §2 XNU Nedir?

| Öğe | Değer | Kaynak (2026-10-12 doğrulandı) |
|---|---|---|
| Akronim | **X is Not Unix** | GitHub apple-oss-distributions/xnu README |
| Tanım | "XNU is a hybrid kernel combining the Mach kernel developed at Carnegie Mellon University with components from FreeBSD and a C++ API for writing drivers called IOKit" | aynı README |
| Mimari | "XNU runs on x86_64 and ARM64 for both single processor and multi-processor configurations" | aynı README |
| İlk yayın | Aralık 1996 (NeXTSTEP çekirdeği olarak) | Wikipedia Xnu: "Initial release: December 1996" |
| Dil | C (Mach, BSD) + kısımlı C++ (IOKit) + assembly | Wikipedia Xnu: "Written in: C, C++, assembly language" |
| Lisans | Apple Public Source License 2.0 | Wikipedia Xnu lisans satırı + depo `APPLE_LICENSE` dosyası |
| Temel soy | OSF MK 7.3 (Mach 3.0 soyu) + 4.4BSD + FreeBSD parçaları | Wikipedia Xnu: "heavily modified (hybrid) Open Software Foundation Mach kernel (OSF MK) 7.3" |
| NeXT soyu | NeXTSTEP'te DriverKit (Objective-C) → IOKit ile değiştirildi | Wikipedia Xnu + CCC 2007 sunumu |
| Sertifika | 10.5 Leopard'da POSIX/UNIX teyidi (yalnız 32-bit i386 yapılandırması) | CCC 2007 "Inside the Mac OS X Kernel" |

### 2.1 "Hibrit" ne demek — üç ölü iddianın düzeltilmesi (CCC 2007)

| Yaygın iddia | Gerçek (kanıtla) |
|---|---|
| "OS X kernel'i Mach'tır" | **Hayır** — çekirdeğin adı XNU; Mach + BSD + IOKit'ten oluşur |
| "OS X mikrokernel'dir" | **Hayır** — BSD ve sürücüler çekirdek modunda; monolitik-ağırlıklı hibrit |
| "FreeBSD üzerine kuruludur" | **Hayır** — BSD kısmı 4.4BSD tabanlı; FreeBSD/NetBSD'den parça almıştır |
| "C++ ile yazılmıştır" | **Kısmen** — yalnız IOKit kısımlı C++; Mach/BSD C'dir |
| "Kaynak tam açıktır" | **Hayır** — ARM bitlerinin çoğu ve ticari sır sayılabilecek parçalar kapalı; yine de derlenmiş çekirdek sistem yerine geçebilir |

### 2.2 Tek adres uzayında çalışma gerekçesi (Apple KPG)

> "in OS X, Mach is linked with other kernel components into a single kernel address
> space. This is primarily for performance; it is much faster to make a direct call
> between linked components than it is to send messages or do remote procedure calls
> (RPC) between separate tasks." — *Kernel Programming Guide, Mach*

Sonuç: Mach burada **haberleşme merkezi değil**, soyutlamaların (port, task, VM,
zamanlama) sağlayıcısıdır. Mikrokernel avantajı yerine **dolaylı çağrı maliyeti**
kullanıcı uzayına bırakılmıştır (daemon'lar).

---

## §3 Açık Kaynak Depo Kanıtı (apple-oss-distributions)

### 3.1 Depo künyesi (2026-10-12 çekilen)

| Alan | Değer |
|---|---|
| URL | https://github.com/apple-oss-distributions/xnu |
| Tür | Public repository |
| Oluşturma | 2021-10-04T21:38:29Z |
| Yıldız / Fork | ~3.7k star · ~432 fork (GitHub sayacı) |
| Varsayılan dal | `main` · `main` üzerinde 30 commit |
| Releases | Depoda GitHub release **yok** ("There aren't any releases here") |
| Homepage | https://opensource.apple.com/releases |
| Lisans dosyası | `APPLE_LICENSE` |
| Eski ayna | github.com/apple/darwin-xnu → "Legacy mirror … Replaced by apple-oss-distributions/xnu" |

### 3.2 Kaynak ağacı (README + dosya listesi)

| Dizin | İçerik (README birebir) | Ses açısından ilgisi |
|---|---|---|
| `osfmk/` | "Mach kernel based subsystems" | Zamanlama, IPC, VM — RT bandı burada |
| `bsd/` | "BSD subsystems code" | POSIX syscall, süreç, VFS, ağ |
| `iokit/` | IOKit sürücü framework'ü | Audio family sürücü eşlemesi |
| `libkern/` | "C++ IOKit library code for handling of drivers and kexts" | Kext yaşam döngüsü + AMFI (`libkern/amfi`) |
| `libsyscall/` | "syscall library interface for userspace programs" | libSystem parçası |
| `security/` | "Mandatory Access Check policy interfaces" | MAC politikaları |
| `pexpert/` | "Platform specific code like interrupt handling, atomics" | Donanım zamanlayıcı girişi |
| `config/` | "configurations of exported apis for supported architecture and platform" | Sürüm yapılandırmaları |
| `SETUP/` | "configuring the kernel, versioning and kextsymbol management" | Sürümleme |
| `EXTERNAL_HEADERS/` | Bağımlılık döngüsü kırıcı başlıklar (mach-o, sys, corecrypto, image4 …) | — |
| `libsa/` | "kernel bootstrap code for startup" | İlk önyükleme |
| `libkdd/` | Kullanıcı uzayı chunked-data çözümleyici | Teşhis |
| `tools/` | "testing, debugging and profiling kernel" | LLDB makroları (`tools/lldbmacros`) |
| `tests/` · `osfmk/tests/` · `bsd/tests/` | XNUPOST + kullanıcı seviyesi testler | — |
| `doc/` | Build dokümanları (ör. `doc/building/xnu_version.md`) | Sürüm formülü |
| `makedefs/` · `san/` | Üst seviye kurallar / sanitizer destekleri | — |

`osfmk/` alt dalları (dosya listesinden): `arm`, `arm64`, `atm`, `bank`, `console`,
`corecrypto`, `corpses`, `default_pager`, `device`, `gssd`, `i386`, `ipc`, `kdp`,
`kern`, `kextd`, `kperf`, `libsa`, `lockd`, `mach`, `mach_debug`, `machine`, `man`,
`prng`, `tests`, `vm`, `voucher`, `x86_64` → **`kern/thread_policy.c`** buradadır
(QoS tablosu §5.3).

### 3.3 Derleme ve sürümleme (README)

| Öğe | Komut/detay |
|---|---|
| Geliştirme çekirdeği | `make SDKROOT=<sdk> ARCH_CONFIGS=<arch> KERNEL_CONFIGS=<variant>` |
| Varyantlar | `debug`, `development`, `release`, `profile` |
| ARM64 örneği | `make SDKROOT=macosx.internal ARCH_CONFIGS=X86_64 KERNEL_CONFIGS="RELEASE DEVELOPMENT DEBUG"` |
| Sürüm kaynağı | SDK/KDK'nın `System/Library/Extensions/System.kext/Info.plist` → `CFBundleVersion` |
| Sürüm ezme | `RC_DARWIN_KERNEL_VERSION` değişkeni (detay: `doc/building/xnu_version.md`) |
| Kernelcache | `kextcache` ile veya `kextd` otomatik izlemeyle (`/System/Library/Extensions`) |
| Boot testi | `bless --mount / --setBoot --nextonly` + `boot.plist` (teklikte geri dönüş) |
| Debug | `debug=0x14e` boot-arg → panic bekler; `xcrun -sdk macosx lldb` + `gdb-remote` |
| Kullanıcı testleri | `make RC_ProjectName=xnu_tests SDKROOT=<sdk>` (`tools/tests/unit_tests/README.md`) |

**Sürüm gözlemi:** macOS sürümü ile xnu sürümü farklı numaralandırılır (macOS 27 →
`xnu-13432.1.9~1`; macOS 26 → `xnu-12377.*`) → `macos-anayasa.md` §4.2 tablosuna bağlıdır.

### 3.4 Başlık kurulumu (SDK'ya düşen yerler — README)

| Başlık yeri | Kim kullanır |
|---|---|
| `System/Library/Frameworks/Kernel.framework/Headers` | KEXT'ler |
| `/usr/include` · `System.framework` | Kullanıcı uzayı uygulamaları |
| `IOKit.framework/Headers` | IOKit kullanıcı uzayı istemcileri |
| `/System/DriverKit/usr/include` | DriverKit sürücüleri (kullanıcı uzayı) |
| `PrivateHeaders` | **Yalnız Apple iç geliştirme** (bizim erişimimiz yok → ⚠️) |

Şeffaflık anahtarları: `PRIVATE`, `KERNEL_PRIVATE`, `BSD_KERNEL_PRIVATE`,
`MACH_KERNEL_PRIVATE`, `XNU_KERNEL_PRIVATE`, `KERNEL`, `DRIVERKIT` — başlığın hangi
katmanda görünür olduğunu belirler (README §Conditional Compilation).

### 3.5 Sürümün çekirdekte nasıl okunduğu (README §Versioning)

| Yön | Komut/anahtar | Düşen değer |
|---|---|---|
| Sürümden sürüme | `System/Library/Extensions/System.kext/Info.plist → CFBundleVersion` | `xnu-13432.1.9~1` sınıfı biçim (README birebir) |
| Kullanıcı uzayı | `uname -r` | Darwin `major.minor.patch` (ör. `27.0.0`) |
| Build ezme | `RC_DARWIN_KERNEL_VERSION` | Yalnız yapı ortamı (detay `doc/building/xnu_version.md`) |
| Build varyantı | `KERNEL_CONFIGS="RELEASE DEVELOPMENT DEBUG"` | Üretim/debug çekirdeği |

> **CoreMusic etkisi:** macOS sürüm numarası (27) ≠ Darwin (`27.0.0`) ≠ xnu
> (`13432.1.9~1`) üçlüsü **ayrı okunur** → `macos-anayasa.md` §4.2 D1 deseni;
> CI sürüm-kıyas testi `uname -r`'a bağlanır (PLANNED).

---

## §4 Üç Bileşen

### 4.1 Mimari yerleşim

```text
                    ┌────────────────────────────────────────────┐
   syscall girişi   │                 XNU çekirdeği              │
  ─────────────────►│  pozitif no ──► bsd/      (POSIX, VFS)     │
  (tek entry:       │  negatif no ──► osfmk/    (Mach: sched,IPC)│
   int 0x80/sysenter│                                            │
   /sc)             │  iokit/ + libkern/  (sürücü framework'ü)    │
                    └───────────────┬────────────────────────────┘
                                    │ IOService matching
                    ┌───────────────▼────────────────────────────┐
                    │ KEXT / DriverKit / Audio Server Plug-in    │
                    └───────────────┬────────────────────────────┘
                                    ▼
                        Donanım (Apple silicon, USB audio …)
```

*Syscall numaralandırma ve tek giriş noktası: CCC 2007 sunumu; linklenme gerekçesi: Apple KPG.*

### 4.2 Mach (`osfmk/`) — özet tablo

| Soyutlama | Anlamı (KPG birebir özet) | Ses ilişkisi |
|---|---|---|
| **Task** | Kaynak sahibi birim: sanal adres uzayı + port hakları + 0-n thread | Süreç = task + BSD zarfı |
| **Thread** | CPU çalıştırma birimi | IOProc render thread'i |
| **Port** | Simplex, yetki tabanlı haberleşme kanalı (`port right`) | Kernel→user bildirimleri |
| **Port rights** | Send/receive yetkileri (güvenlik temeli) | — |
| **IPC** | `mach_msg` ailesi (legacy/deprecated) + `mach_ipc` ailesi (tercih edilen) | — |
| **Memory object** | Kalıcı depoya bağlı sayfa gösterimleri | Paylaşımlı ring buffer |
| **Address space** | İnce (sparse) sanal uzay + paylaşımlı bölgeler | — |
| **Time** | Saat, zamanlayıcı, bekleme | `mach_absolute_time` (zaman damgası) |
| **MIG** | Arayüz tanımından stub üreten araç | — |

Ek KPG notları: "highly parallel execution, including preemptively scheduled threads
and support for SMP"; "a flexible scheduling framework, with support for real-time
usage"; "security and resource management as a fundamental principle of design; all
resources are virtualized".

### 4.3 BSD (`bsd/`) — özet tablo

| Alan | İçerik | Kaynak |
|---|---|---|
| POSIX yüzeyi | `open`, `fork`, `read`, `write`, `mmap`, sinyaller | Wikipedia Xnu + KPG |
| Süreç modeli | UNIX süreçleri Mach task'lerin üstüne kurulmuş | Wikipedia Xnu |
| Dosya sistemi | VFS + HFS, HFS+, **APFS**, NFS (istemci+sunucu) | Wikipedia Xnu |
| Ağ | TCP/IP yığını (BSD tabanlı) | Wikipedia Xnu |
| Güvenlik | uid/gid, izinler, audit alt sistemi, **MAC** (mandatory access control) | Wikipedia Xnu + `security/` ağacı |
| Kripto çerçevesi | `corecrypto` (EXTERNAL_HEADERS'da) | GitHub ağacı |
| IPC | UNIX System V IPC | Wikipedia Xnu |
| Kilit | Bazı kilitleme primitifleri | Wikipedia Xnu |
| FreeBSD ilişkisi | "most recently synchronised with that from the FreeBSD kernel … code sharing still occurs as of 2009" | Wikipedia Xnu |

### 4.4 IOKit (`iokit/` + `libkern/`) — üç kavram

| Kavram | Tanım (Apple IOKit Overview birebir özet) |
|---|---|
| **Family** | Bir kategori cihazın ortak soyutlamaları (SCSI, USB, FireWire, depolama, ağ, HID) — C + C++ sınıfları, dinamik yüklenebilir |
| **Driver** | Belirli cihazı/veri yolunu yöneten IOKit nesnesi; client-provider ilişkisi (ör. SCSI sürücüsü = PCI ailesinin istemcisi + SCSI ailesinin sağlayıcısı) |
| **Nub** | Sürücünün bağlanacağı bağlantı noktası nesnesi (ör. `IOPCIDevice`, `IOSCSIDevice`); eşleştirme, arbitraj, güç yönetimi sağlar |

| Özellik | Değer | Kaynak |
|---|---|---|
| Dil | Embedded C++ alt kümesi: **exception yok, multiple inheritance yok, template yok** | Wikipedia Xnu |
| Eşleştirme akışı | PCI sürücüsü nub üretir → doğru sürücü bulunur → bağımlı aileler yüklenir → sürücü yüklenir (4 adım örneği) | IOKit Overview §Connection Example |
| Çoklu çekirdek | "multi-threaded, symmetric multiprocessing (SMP)-safe" | Wikipedia Xnu |
| Sıcak takma | Hot-plug + otomatik dinamik yapılandırma destekli | Wikipedia Xnu |
| Neden BSD sürücü modeli değil | BSD modeli otomatik yapılandırma, stacking, güç yönetimi, dinamik yükleme vermiyordu | IOKit Overview gerekçe paragrafı |
| KEXT biçimi | Bundle; genelde `/System/Library/Extensions`; ağ (NKE) kext'leri de mümkün | CCC 2007 sunumu |
| Güvenlik | `libkern/amfi` (Apple Mobile File Integrity) + imzalama zorunluluğu | GitHub xnu ağacı |

---

## §5 Zamanlama: Bantlar, QoS ve Gerçek-Zamanlı Ses

### 5.1 Öncelik bantları (Apple Kernel Programming Guide, 4 bant)

| Bant | Özellik (KPG tablosu) |
|---|---|
| **Normal** | Normal uygulama iş parçacığı öncelikleri |
| **System high priority** | Normal thread'lerin üstüne çıkarılmış thread'ler |
| **Kernel mode only** | Yalnız çekirdek içindeki thread'ler (IOKit workloop'ları gibi) — tüm kullanıcı thread'lerinden yüksek |
| **Real-time** | "threads whose priority is based on getting a well-defined fraction of total clock cycles … (in an audio player application, for example)" |

### 5.2 Gerçek-zamanlı bandın çalışma prensibi (KPG)

1. Thread, scheduler'a der ki: "önümüzdeki `B` çevrimden `A` çevrimi bana ver"
   (ör. 7000 çevrimden 3000).
2. `preemptible` bayrağı bu çevrimlerin **bitişik** olup olmayacağını belirtir;
   bitişik uzun kvantalar "general frowned upon" (genelde sakıncalıdır) ama bazı
   gerçek-zamanlı senaryolarda zorunludur.
3. **Yumuşak (soft) real-time:** çekirdek "will make every effort" ama **garanti
   vermez**; thread gereğinden fazla talep edip hesap-bağımlı (compute-bound) davranırsa
   **normal banda düşürülebilir** (demotion).
4. POSIX pthread öncelikleri **yalnız en düşük bantta (0–63)** çalıştırılabilir;
   RT için `thread_policy_set` gerekir → KPG: "This is commonly used when interacting
   with **CoreAudio**."

**Politika listesi (KPG Tablo 10-2):**

| Politika | Anlamı |
|---|---|
| `THREAD_STANDARD_POLICY` | Varsayılan |
| `THREAD_TIME_CONSTRAINT_POLICY` | Gerçek-zamanlı davranış (period/computation/constraint/preemptible) |
| `THREAD_PRECEDENCE_POLICY` | Aynı task içindeki göreli önem (`importance`; min `IDLE_PRI`=0) |
| `THREAD_EXTENDED_POLICY` | timeshare sabitleme (Chromium örneğinde `timeshare=0`) |

**Chromium gerçek-zamanlı ses ayarı (kaynak kod kanıtı — `platform_thread_mac.mm`):**

| Parametre | Değer | Açıklama |
|---|---|---|
| `kGuaranteedAudioDutyCycle` | 0.75 | Kvantanın %75'i garanti |
| `kMaxAudioDutyCycle` | 0.85 | Azami %85 |
| `kTimeQuantum` | 2.9 ms | "About 128 frames @44.1KHz" |
| `preemptible` | 0 | Süreler bitişik zorunlu değil ama kesintiye dirençli ayar |
| `precedence.importance` | 63 | Sabit öncelik yükseltmesi |
| Hata toleransı | `thread_policy_set` ağır yükte **başarısız olabilir** → erken dönüş | Yorum satırı: "if the kernel decides the system is under heavy load" |

### 5.3 QoS sınıfları (xnu `osfmk/kern/thread_policy.c` — depo kanıtı)

```c
const qos_policy_params_t thread_qos_policy_params = {
  .qos_pri[THREAD_QOS_UNSPECIFIED]        = 0,     /* not consulted */
  .qos_pri[THREAD_QOS_USER_INTERACTIVE]   = BASEPRI_BACKGROUND, /* i.e. 46 */
  .qos_pri[THREAD_QOS_USER_INITIATED]     = BASEPRI_USER_INITIATED,
  .qos_pri[THREAD_QOS_LEGACY]             = BASEPRI_DEFAULT,
  .qos_pri[THREAD_QOS_UTILITY]            = BASEPRI_UTILITY,
  .qos_pri[THREAD_QOS_BACKGROUND]         = MAXPRI_THROTTLE,
  .qos_pri[THREAD_QOS_MAINTENANCE]        = MAXPRI_THROTTLE,
  /* io tier / throughput / latency tabloları için kaynak: aynı dosya */
};
```

| QoS sınıfı | IO tier (kaynak) | Latency tier (kaynak) | Throughput tier (kaynak) |
|---|---|---|---|
| USER_INTERACTIVE | THROTTLE_LEVEL_TIER0 | LATENCY_QOS_TIER_0 | THROUGHPUT_QOS_TIER_0 |
| USER_INITIATED | TIER0 | TIER_1 | TIER_1 |
| LEGACY | TIER0 | TIER_1 | TIER_1 |
| UTILITY | TIER1 | TIER_3 | TIER_2 |
| BACKGROUND | TIER2 | TIER_3 | TIER_5 |
| MAINTENANCE | TIER3 | TIER_3 | TIER_5 |

**Kullanıcı uzayı arayüzü (Apple dokümanları):**

| API | Kullanım |
|---|---|
| `pthread_set_qos_class_self_np` | pthread'in QoS sınıfını çalışma anında değiştirme |
| `pthread_attr_set_qos_class_np` | pthread oluşturma anında QoS atama |
| `dispatch_queue_attr_make_with_qos_class` | GCD kuyruğuna QoS |
| `qos_class_main()` | Ana thread QoS'ini okuma (uygulamada `user-interactive`) |
| `spindump -timeline` | Kod parçasının hangi QoS ile çalıştığını denetleme |

### 5.4 QoS ↔ Apple silicon çekirdek seçimi

> "On Apple silicon, a task's QoS class influences whether the system runs that task.
> For example, the system is more likely to run background tasks on lower performance
> cores" — *Tuning your code's performance for Apple silicon*

| Kural | Apple önerisi |
|---|---|
| Spin lock yasağı | "eliminate spin locks and other spin-wait code" → `os_unfair_lock`, condition variable, mutex |
| Yield yasağı | `pthread_yield_np` yerine **bloklama**; yield alt öncelikli productive işi engeller |
| QoS emanetleri | Eski `pthread_setschedparam`/`thread_set_policy` kullanan kod QoS API'lerine taşınmalı |
| Priority inversion | `dispatch_sync/wait` ve `pthread_mutex_lock` durumlarında sistem alttaki işi QoS'e çıkarır; **ancak birden fazla lock arasında çapraz promote yapmaz** |

**Enerji rehberi notu (Energy Efficiency Guide for Mac Apps):** QoS atanmazsa GCD
global kuyruk `default` seviyesinde kalır; main thread `user-interactive`; XPC servis
main thread'i `default`.

---

## §6 BSD Syscall Katmanı

| Konu | Gerçek | Kaynak |
|---|---|---|
| Tek giriş | `int 0x80` / `sysenter` / `sc` (mimariye göre) | CCC 2007 |
| Numara yönü | **Pozitif** syscall no → BSD · **negatif** → Mach | CCC 2007 |
| Kullanıcı kütüphanesi | Uygulamalar syscall'a doğrudan çıkmaz → `libSystem` (libc karşılığı) | CCC 2007 |
| Kütüphane bileşenleri | libSystem = libc + libdispatch + libobjc + … (Apple dokümanı; bileşen listesi ⚠️ tam liste bu turda çekilmedi) | `macos-anayasa.md` §7 |
| API ailesi | POSIX/BSD: dosya, süreç, sinyal, ağ, pthread | Wikipedia Xnu |
| Mach API'si | `mach_msg` (legacy/deprecated) ve `mach_ipc` (tercih edilen, transaction destekli) | KPG Mach |
| Kullanım emniyeti | Kernel dokümanı: uygulamalar private interface'i çağırmaz, libSystem üzerinden gider | KPG |

**Ses açısından:** IOProc callback'i syscall dönüşünde değil, **zamanlayıcı/kesme
tetiklemesiyle** çalışır; uygulama tarafı ise `pthread`/`dispatch` ile syscall yapar.
Bu yüzden gerçek-zamanlı dalın syscall yoğunluğu düşük tutulmalıdır (CoreMusic
şartnamesi: render döngüsünde allocation/lock yok → ROLE "Zero-allocation, lock-free").

---

## §7 Sürücü Katmanı ve Güvenlik Sınırı

| Soru | Cevap | Kaynak |
|---|---|---|
| Sürücü nerede çalışır? | Klasik: çekirdek (KEXT); modern: kullanıcı uzayı (DriverKit / Audio Server Plug-in) | XNU README başlık yolları (`/System/DriverKit/usr/include`) + CoreAudio docs |
| Sürücü nasıl bulunur? | IOKit **matching**: nub üretimi → aday sürücü arama → bağımlılık çözümü → yükleme | IOKit Overview |
| Sürücü yükü nasıl denetlenir? | AMFI (`libkern/amfi`) + imzalama; SIP çekirdek alanını korur | GitHub xnu ağacı · `macos-anayasa.md` §7.2 |
| Çekirdek uzantısı yazmak mümkün mü? | Teknik olarak evet (açık kaynak), pratikte CoreMusic **yazmayacak** (rol yasağı: firmware/OTA güvenlik → DUR + handover) | ROLE §Konsültasyon |
| Ses sürücüsü kimin? | Apple Audio Server Plug-in'ler + üçüncü taraf; IOKit Audio family'nin üstünde Audio HAL | `macos-ses-yigini.md` §3 |

---

## §8 Ses için Çekirdeğin Anlamı (özet → tam analiz `macos-ses-yigini.md`)

| # | Çekirdek yeteneği | Ses sonucu |
|---|---|---|
| 1 | `THREAD_TIME_CONSTRAINT_POLICY` (RT bandı) | IOProc thread'i gecikmesiz çalışır; KPG açıkça CoreAudio'yu örnek verir |
| 2 | QoS sınıfları + P/E çekirdek seçimi | Render thread yüksek performanslı çekirdekte tutulabilir (Apple silicon) |
| 3 | Soft-RT + demotion riski | Aşırı `computation` talebi thread'i düşürür → xrun (glitch) riski |
| 4 | IOKit hot-plug + eşleştirme | USB ses cihazı takılma/çıkma olayları sistem düzeyinde yönetilir |
| 5 | Mach time (`mach_absolute_time`) | HAL'ın ring buffer tahmini için mutlak zaman kaynağı |
| 6 | IOkit workloop'ları "kernel mode only" bandında | Sürücü tarafı kullanıcı uygulamalarını ezmez |
| 7 | Kullanıcı uzayında sürücü trendi | Audio Server Plug-in + DriverKit → çökme domaini uygulamadan ayrı |

---

## §9 Çapraz Referanslar ve Durum Etiketleri

| İddia | Dosya | Durum |
|---|---|---|
| XNU hibrit çekirdek, açık kaynak | bu dosya §2–§3 | IMPLEMENTED (belge, URL'li) |
| CoreMusic çekirdek katmanına girmiyor | bu dosya §1 | Kural (ROLE) |
| Zamanlayıcı CoreAudio ile kullanılıyor | bu dosya §5.2 | IMPLEMENTED (Apple KPG alıntısı) |
| macOS ses backend'i = CoreAudio | `macos-anayasa.md` §8 | ⚠️ PLANNED (kod yok, cpp=0) |
| QoS sayısal eşdeğerleri (BASEPRI_*) | bu dosya §5.3 | ⚠️ sembolik — tam sayısal değerler mimariye bağlı |
| Golden Gate XNU farkları | bu dosya §3.3 | ⚠️ VERIFICATION REQUIRED (diff incelenmedi) |

---

## §10 Açık Sorular

| # | Soru | Yönlendirme |
|---|---|---|
| Q1 | `xnu-13432` (Golden Gate) ağacında scheduling/QoS değişikliği var mı? | Teyit → sonraki tur |
| Q2 | IOProc için önerilen `computation/constraint` değerleri JUCE tarafında nasıl set ediliyor? | `macos-ses-yigini.md` §6 + JUCE kaynağı |
| Q3 | Apple silicon'da `kernel_task` ısı yönetimi ses thread'ini nasıl etkiler? | performance-engineer ölçümü (PLANNED) |
| Q4 | DriverKit ile audio plug-in yazımı CoreMusic kapsamına girer mi? | Kapsam kararı → **root**/ADR |
| Q5 | `PRIVATE`/`KERNEL_PRIVATE` başlıkları SDK'da görünür mü (bizim erişim)? | Xcode CLT SDK denetimi |

---

## §11 Kaynakça

1. https://github.com/apple-oss-distributions/xnu — README, dosya ağacı, build (2026-10-12)
2. https://github.com/apple/darwin-xnu — legacy mirror ifadesi (2026-10-12)
3. https://opensource.apple.com/releases — resmi sürüm listesi (2026-10-12)
4. https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/Mach/Mach.html — Mach soyutlamaları (2026-10-12)
5. https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/scheduler/scheduler.html — bantlar, RT politikası (2026-10-12)
6. https://developer.apple.com/library/archive/documentation/Darwin/Conceptual/KernelProgramming/IOKit/IOKit.html — families/drivers/nubs (2026-10-12)
7. https://github.com/apple-oss-distributions/xnu/blob/main/osfmk/kern/thread_policy.c — QoS öncelik/io/latency tabloları (2026-10-12)
8. https://developer.apple.com/documentation/apple-silicon/tuning-your-code-s-performance-for-apple-silicon.md — QoS + spin lock/yield yasakları (2026-10-12)
9. https://developer.apple.com/library/archive/documentation/Performance/Conceptual/power_efficiency_guidelines_osx/PrioritizeWorkAtTheTaskLevel.html — QoS sınıfları, priority inversion (2026-10-12)
10. https://fahrplan.events.ccc.de/congress/2007/Fahrplan/attachments/986_inside_the_mac_osx_kernel.pdf — syscall numaralandırma, "3 ölü iddia" (2026-10-12)
11. https://en.wikipedia.org/wiki/Xnu — tarih, APSL, OSF MK 7.3 (2026-10-12)
12. https://chromium.googlesource.com/chromium/src/+/…/platform_thread_mac.mm — RT ses duty-cycle değerleri (2026-10-12)
13. https://docs.huihoo.com/darwin/kernel-programming-guide/scheduler/chapter_8_section_4.html — ayna metin (2026-10-12)

---

## §12 AudioServerPlugInDriverInterface — IO Çevrimi Arayüzü

Kullanıcı uzayı ses sunucu plug-in'inin çekirdek-iletişim arayüzüdür; §3.2'deki
IOKit/DriverKit ayrımının **kullanıcı-uzayı sürücü** yüzüdür. Aşağıdaki beş üye
(bağlantı noktası) bu turda resmi sayfaları **erişilebilir** olarak doğrulandı
(HTTP 200 · 2026-10-12); sayfa metinleri Apple'ın doküman uygulaması tarafında
JS ile sunulduğundan bu turda **alıntı yapılamadı** → her üye için işlev tanımı
`⚠️ VERIFICATION REQUIRED` işaretlidir (uydurma yok).

| Üye | Doğrulanmış sayfa | İddia düzeyi |
|---|---|---|
| `GetZeroTimeStamp` | `CoreAudio/AudioServerPlugInDriverInterface/GetZeroTimeStamp` | sayfa erişilebilir (HTTP 200); işlev tanımı ⚠️ VERIFICATION REQUIRED |
| `WillDoIOOperation` | `CoreAudio/AudioServerPlugInDriverInterface/WillDoIOOperation` | sayfa erişilebilir (HTTP 200); işlev tanımı ⚠️ VERIFICATION REQUIRED |
| `DoIOOperation` | `CoreAudio/AudioServerPlugInDriverInterface/DoIOOperation` | sayfa erişilebilir (HTTP 200); işlev tanımı ⚠️ VERIFICATION REQUIRED |
| `BeginIOOperation` | `CoreAudio/AudioServerPlugInDriverInterface/BeginIOOperation` | sayfa erişilebilir (HTTP 200); işlev tanımı ⚠️ VERIFICATION REQUIRED |
| `EndIOOperation` | `CoreAudio/AudioServerPlugInDriverInterface/EndIOOperation` | sayfa erişilebilir (HTTP 200); işlev tanımı ⚠️ VERIFICATION REQUIRED |

**Mimari konum:** beş üye tek arayüzün (interface vtable) parçalarıdır — bu dosya
yalnızca **varlık + ad** düzeyini kanıtlar; çağrı sırası ve imzalar sonraki turda
başlık dosyası (`AudioServerPlugIn.h`, §15 qa1811 atfı) üzerinden doğrulanacaktır.

## §13 IO Çevrimi Veri Yapıları (AudioServerPlugInIOCycleInfo · AudioHardwareIOProcStreamUsage)

| Yapı | Doğrulanmış sayfa + durum |
|---|---|
| `AudioServerPlugInIOCycleInfo` | `CoreAudio/AudioServerPlugInIOCycleInfo` — sayfa erişilebilir (HTTP 200); yapı alanları JS sayfasından bu turda alınamadı → alan listesi `⚠️ VERIFICATION REQUIRED` |
| `AudioHardwareIOProcStreamUsage` | `CoreAudio/AudioHardwareIOProcStreamUsage` — sayfa erişilebilir; **özet birebir**: "This structure describes which streams a given AudioDeviceIOProc will use. It is used in conjunction with kAudioDevicePropertyIOProcStreamUsage." |

**Çıkarım:** `AudioHardwareIOProcStreamUsage` özeti iki bilgiyi birlikte verir: (1) yapı
IOProc'un **hangi stream'leri** kullanacağını tarif eder, (2) özelliği
`kAudioDevicePropertyIOProcStreamUsage` ile birlikte kullanılır — yani yapı-özel-
özellik (property) çifti olarak okunmalıdır. CoreMusic 8.1 hedefinde stream-seçimi
(device property üzerinden) bu eşleşmeye bağlıdır.

## §14 AVFAudio Köprüsü (AVAudioSession/IOType · AVAudioNode/lastRenderTime · outputPresentationLatency)

| Öğe | Resmi özet (birebir · meta description) |
|---|---|
| `AVAudioSession.IOType` | "Constant values used to specify the audio session's aggregated I/O behavior." |
| `AVAudioNode.lastRenderTime` | "The most recent render time." |
| `AVAudioNode.outputPresentationLatency` | "The maximum render pipeline latency downstream of the node, in seconds." |

**Zincir ilişkisi:** `IOType` oturum-bazlı toplu-I/O davranışını sabitler;
`lastRenderTime` en son render anını taşır; `outputPresentationLatency` ise düğümden
aşağısı (downstream) en büyük boru-hattı gecikmesini **saniye** cinsinden verir — üçü
birlikte "hangi I/O modu · hangi an · ne kadar gecikme" üçlüsünü kurar. CoreMusic
gecikme bütçesi (sartname: 512 örnek @ 48 kHz ≈ 10.67 ms — `macos-device-ve-format`
§19-A hesabı) ile karşılaştırıldığında `outputPresentationLatency` sunum-öncesi gecikmenin
ölçülebilir kısmıdır; ölçüm PLANNED.

## §15 qa1811 (Archive Technical Note) — AudioServerPlugIn'ın Sınırlı Ortamı

> Kaynak: `https://developer.apple.com/library/archive/qa/qa1811/_index.html`
> (✅ HTTP 200 · 2026-10-12 — içeriği bu turda çekildi).

**Soru (birebir):** "How does my user-space plug-in talk to other processes?"

**Cevap özet kanıtları (birebir alıntılar):**

| Kural | qa1811 metni (birebir) |
|---|---|
| HAL API yasağı | "an AudioServerPlugIn may not make any calls to the client HAL API in the CoreAudio.framework. This will result in undefined (but generally bad) behavior." |
| Sandbox | "the host process is sandboxed … may only read files in its bundle in addition to the system libraries and frameworks. It may not access user documents or write to any filesystem locations other than the system's cache and temporary directories" |
| Kalıcı depolama | "The host provides a means for the plug-in to store and retrieve data from persistent storage." |
| Süreçlerarası iletişim | mach service isimleri plug-in bundle'ının `info.plist`'inde `AudioServerPlugIn_MachServices` anahtarında listelenmelidir — değer "an array of the names of the mach services that need to be accessed" |
| Başlık referansı | "See the AudioServerPlugIn.h header file for further details regarding Audio Server plug-ins." |
| Sürüm notu | İlk sürüm 2013-11-19 (Archive revizyon geçmişi) |

**Çıkarım:** plug-in yalnız kendi bundle'ı + sistem kütüphanelerini okur; dosyaya
yazma Apple API'si üzerinden cache/temp ile sınırlıdır; başka süreçlere erişim
yalnızca `AudioServerPlugIn_MachServices` ile beyan edilirse mümkündür. CoreMusic
tasarımında plug-in-içi kalıcı ayar host üzerinden taşınır (dosyaya doğrudan yazma yok).

## §16 Web Araştırma Tablosu (bu tur eklenen 11 URL)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| GetZeroTimeStamp sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInDriverInterface/GetZeroTimeStamp | 2026-10-12 | ✅ HTTP 200 (JS sayfası — içerik alıntısı yok, §12) |
| DoIOOperation sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInDriverInterface/DoIOOperation | 2026-10-12 | ✅ HTTP 200 (JS sayfası — içerik alıntısı yok, §12) |
| WillDoIOOperation sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInDriverInterface/WillDoIOOperation | 2026-10-12 | ✅ HTTP 200 (JS sayfası — içerik alıntısı yok, §12) |
| BeginIOOperation sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInDriverInterface/BeginIOOperation | 2026-10-12 | ✅ HTTP 200 (JS sayfası — içerik alıntısı yok, §12) |
| EndIOOperation sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInDriverInterface/EndIOOperation | 2026-10-12 | ✅ HTTP 200 (JS sayfası — içerik alıntısı yok, §12) |
| AudioServerPlugInIOCycleInfo sayfası erişilebilir | https://developer.apple.com/documentation/CoreAudio/AudioServerPlugInIOCycleInfo | 2026-10-12 | ✅ HTTP 200 (JS sayfası — alan listesi ⚠️, §13) |
| AVAudioSession/IOType özeti alındı | https://developer.apple.com/documentation/AVFAudio/AVAudioSession/IOType | 2026-10-12 | ✅ HTTP 200 (meta description birebir) |
| AudioHardwareIOProcStreamUsage özeti alındı | https://developer.apple.com/documentation/CoreAudio/AudioHardwareIOProcStreamUsage | 2026-10-12 | ✅ HTTP 200 (meta description birebir) |
| AVAudioNode/lastRenderTime özeti alındı | https://developer.apple.com/documentation/AVFAudio/AVAudioNode/lastRenderTime | 2026-10-12 | ✅ HTTP 200 (meta description birebir) |
| AVAudioNode/outputPresentationLatency özeti alındı | https://developer.apple.com/documentation/AVFAudio/AVAudioNode/outputPresentationLatency | 2026-10-12 | ✅ HTTP 200 (meta description birebir) |
| qa1811 arşiv notu tam metin çekildi | https://developer.apple.com/library/archive/qa/qa1811/_index.html | 2026-10-12 | ✅ HTTP 200 (tam metin alıntı §15) |

## §17 Repo Kanıtı (CoreMusic disk — gerçek satırlar)

> `ReadAllLines` ile diskten okunmuş GERÇEK içerik (repo'da `.cpp/.h` = 0).

- Dosya: `package.json` / Satır: 8 → `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"`
- Dosya: `shared/composer.json` / Satır: 12 → `"ext-pdo": "*",`
- Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 → `<?php declare(strict_types=1);`
- Dosya: `.github/workflows/ci.yml` / Satır: 14 → `permissions:`

---

## İlişkili dosyalar

- `macos-anayasa.md` — macOS dalı anayasası (sürüm modeli, katmanlar)
- `macos-coreaudio-stacki.md` — CoreAudio nesne modeli (bu dosyadaki arayüzlerin tükettiği API)
- `macos-device-ve-format.md` — format/tampon modeli (IO çevriminin taşıdığı veri)
- `macos-audio-unit-arayuzleri.md` — AU render katmanı

---

**Durum:** Şartname IMPLEMENTED (belge) · CoreMusic çekirdek katmanı: YOK (kod yok, cpp=0) · commit ATILMADI

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode