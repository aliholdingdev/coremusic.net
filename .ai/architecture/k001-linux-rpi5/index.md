---
title: "K001 Linux + Raspberry Pi 5 — Klasör Dizini (index)"
type: architecture-index
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K001 — Linux / Raspberry Pi 5 Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K001 |
| **Ad** | Linux Çekirdek + Raspberry Pi 5 Gömülü Platform |
| **Amaç** | POSIX çekirdek katmanı ile ARM64 gömülü platformun donanım sınırını tek klasörde toplamak |
| **Bağımlılık** | Yukarı: sistem/ekipman · aşağı: `[[../k016-linux-ses/index]]`, `[[../k014-surucu-yigin/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `performance-engineer` (ölçüm), `devops-engineer` (ortam) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (salt-okunur yedek) |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç |
|---|---|---|---|
| 1 | `[[linux-cekirdek-mimari]]` | Linux Çekirdek Mimarisi | POSIX çekirdek, zamanlama/bellek semantiği, platform API şeması |
| 2 | `[[rpi5-gomulu-platform]]` | Raspberry Pi 5 Platformu | ARM64 yerleşim, ağ/zaman servisleri, gömülü donanım sınırları |
| 3 | `[[index]]` | Bu dizin | Klasör özeti, kaynak envanteri, komşu klasör bağlantıları |

**Okuma sırası:** `linux-cekirdek-mimari` → `rpi5-gomulu-platform` → `[[../k016-linux-ses/index]]`.

## 2. Alan (D01) Genel Bakış — Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` | 90 | 73 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` | 48 | 34 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 64 | 50 |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 27 | 24 |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 77 | 58 |
| 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 46 | 33 |
| 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 45 | 35 |
| 8 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 37 | 30 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` — satır: 90 (boş olmayan: 73).


## K0 - İşletim Sistemi Katmanı

### Genel Bakış

K0 katmanı, COREMUSIC'ın en düşük seviyeli yazılım katmanıdır ve doğrudan donanım ile yazılım arasındaki köprüyü oluşturur. Bu katman, tüm platformlarda tutarlı bir işletim sistemi arayüzü sağlamak için soyutlama katmanları sunar. Ses işleme, çoklu ortam ve gerçek zamanlı işleme gereksinimlerini karşılamak üzere optimize edilmiştir.

### Mimari Diyagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    K0 İŞLETİM SİSTEMİ KATMANI                  │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐            │
│  │   Windows   │  │    Linux    │  │    macOS    │            │
│  │   Core      │  │    Core     │  │    Core     │            │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘            │
│         │                │                │                     │
│  ┌──────┴────────────────┴────────────────┴──────┐            │
│  │         Cross-Platform API Soyutlama           │            │
│  └──────┬────────────────┬────────────────┬──────┘            │
│         │                │                │                     │
│  ┌──────┴──────┐  ┌──────┴──────┐  ┌──────┴──────┐            │
│  │    IPC      │  │   Memory    │  │  Threading  │            │
│  │ Mekanizmalar│  │  Management │  │    Model    │            │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘            │
│         │                │                │                     │
│  ┌──────┴──────┐  ┌──────┴──────┐  ┌──────┴──────┐            │
│  │   Process   │  │  System     │  │ Container   │            │
│  │  Isolation  │  │   Calls     │  │  Runtime    │            │
│  └─────────────┘  └─────────────┘  └─────────────┘            │
│                                                                 │
│  ┌─────────────┐  ┌─────────────┐                              │
│  │    RPi5     │  │   macOS     │                              │
│  │    Core     │  │   Core      │                              │
│  └─────────────┘  └─────────────┘                              │
└─────────────────────────────────────────────────────────────────┘
```

### Bileşen Listesi

| Bileşen | Dosya | Açıklama | Durum |
|---------|-------|----------|-------|
| Windows Core | `windows-core.md` | Windows Process Management, Memory, Threading, Registry, Service Management, Event Log, WMI, COM, IPC, Memory Mapped Files, Job Objects, UAC | Planlandı |
| Linux Core | `linux-core.md` | cgroups, namespaces, systemd, D-Bus, epoll, inotify, seccomp, capabilities, tmpfs, /proc filesystem | Planlandı |
| macOS Core | `macos-core.md` | Grand Central Dispatch, XPC, Core Foundation, Metal, IOKit, LaunchAgent, App Sandbox, Hardened Runtime | Planlandı |
| RPi5 Core | `rpi5-core.md` | GPIO Control, DMA Engine, PWM Audio, I2S Interface | Planlandı |
| Container Runtime | `container-runtime.md` | Docker Engine, containerd, Kubernetes, Docker Compose, Health Checks | Planlandı |
| Cross-Platform API | `cross-platform-api.md` | POSIX Threads, SDL2, libuv, libevent, Boost.Asio, Rust tokio | Planlandı |
| IPC Mekanizmaları | `ipc-mekanizmalari.md` | Unix Domain Sockets, Windows Named Pipes, Shared Memory, Message Queues, gRPC | Planlandı |
| Memory Management | `memory-management.md` | Virtual Memory, Page Tables, Memory Pool, Slab Allocator, Buffer Management | Planlandı |
| Threading Model | `threading-model.md` | Thread Pools, Lock-free Structures, Atomic Operations, Condition Variables, Mutex Hierarchy | Planlandı |
| Process Isolation | `process-isolation.md` | Process Sandbox, Capability dropping, chroot, namespaces, seccomp-bpf | Planlandı |
| System Calls | `system-calls.md` | POSIX syscall interface, Windows NT API, Linux io_uring, epoll/kqueue | Planlandı |

### Bağımlılıklar

#### Alt Katmanlar
- **Donanım**: CPU, Bellek, Disk, Ağ, Ses Kartı, GPIO (RPi5)
- ** Firmware/BIOS/UEFI**: Boot süreci, donanım başlatma

#### Üst Katmanlar
- **K1 - Ses Motoru**: Ses buffer yönetimi, DMA transferi
- **K2 - Ağ Katmanı**: Socket yönetimi, IPC kullanımı
- **K3 - Uygulama Katmanı**: Process oluşturma, bellek ayırma

### Temel İlkeler

1. **Gerçek Zamanlılık**: Ses işleme için deterministik davranış
2. **Düşük Gecikme**: Minimal overhead ile maksimum throughput
3. **Çapraz Platform**: Tüm işletim sistemlerinde tutarlı API
4. **Güvenlik**: Process izolasyonu, capability-based güvenlik
5. **Ölçeklenebilirlik**: Yeni platform desteği için genişletilebilir mimari

### Durum: Implementasyon

**Mevcut Durum**: Planlama aşaması tamamlandı, implementasyon başlayacak.

**Öncelik Sırası**:
1. Cross-Platform API soyutlama katmanı
2. Memory Management optimizasyonu
3. Threading Model implementasyonu
4. Platform-specific core modülleri
5. Container Runtime entegrasyonu

**Sonraki Adımlar**:
- Cross-Platform API tasarım dokümanı yazımı
- Memory Management algoritmalarının seçilmesi
- Threading Model için performans gereksinimlerinin tanımlanması



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` — satır: 48 (boş olmayan: 34).


## K0 İşletim Sistemi — CLAUDE.md

**Bu dosya K0 katmanı için özel AI talimatlarını içerir.**

### 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Audio thread malloc yasak | Crash |
| 2 | Audio thread mutex yasak | Deadlock |
| 3 | Platform API wrapper ile soyutlanmalı | Portability hatası |
| 4 | Thread öncelik hiyerarşisine uy | Ses takılması |

### 2. Teknoloji Kısıtlamaları

| Kısıt | Değer |
|-------|-------|
| Dil | C++20 |
| Minimum Standard | C++17 |
| Compiler | MSVC 17+, GCC 12+, Clang 15+ |
| Memory Model | C++11 memory model |
| Threading | std::thread, platform-specific |

### 3. Yasaklı Örüntüler

```cpp
// ❌ YASAK — Audio thread'de
malloc(), free(), new, delete, std::vector::push_back

// ✅ DOĞRU
alignas(64) float buffer[4096];
std::atomic<size_t> head;
```

### 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K0 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:1-64` — satır: 64 (boş olmayan: 50).


## K0: İşletim Sistemi Layer

**Katman:** K0 (En Alt Katman)
**Kapsam:** İşletim sistemi servisleri, API'ler, sürücüler, Bellek yönetimi, Threading
**Sorumlu Agent:** Windows Software Engineer / Embedded Engineer
**Bileşen Sayısı:** 50

---

### 1. Genel Bakış

K0 katmanı, CoreMusic'in tüm katmanlarının temelini oluşturan işletim sistemi hizmetlerini içerir. Bu katman, donanım ile yazılım arasındaki köprüyü kurar ve tüm üst katmanlara temel OS servislerini sunar.

#### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Platform Bağımsızlık** | Tüm OS'ler için ortak API soyutlama katmanı |
| **Low-Level Erişim** | Donanım sürücülerine doğrudan erişim |
| **Bellek Yönetimi** | Zero-allocation audio thread için OS desteği |
| **Threading** | Real-time thread önceliği ve senkronizasyon |
| **IPC** | Süreçler arası iletişim |

#### 1.2 Desteklenen Platformlar

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| Tier 1 | Windows (XP-11, Server 2012 R2+) | ✅ Ana geliştirme | ASIO, WASAPI |
| Tier 2 | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli | ALSA, PipeWire |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli | CoreAudio |
| Tier 4 | Raspberry Pi (ARM64) | ✅ Destekli | I2S |
| Tier 5 | ReactOS | ⚠️ Experimental | Sınırlı |

---

### 2. Bileşen Haritası

| # | Bileşen | Amaç | Teknoloji |
|---|---------|------|-----------|
| K0-01 | Windows API | Win32 API soyutlama | C++20, WinRT |
| K0-02 | Linux Kernel | Kernel servisleri | Linux API, ioctl |
| K0-03 | macOS Core | macOS servisleri | CoreFoundation, IOKit |
| K0-04 | IPC Manager | Süreçler arası iletişim | Shared Memory, Pipes |
| K0-05 | Memory Manager | Bellek yönetimi | VirtualAlloc, mmap |
| K0-06 | Thread Manager | İş parçacığı yönetimi | pthreads, Win32 Threads |
| K0-07 | File System | Dosya sistemi erişimi | std::filesystem |
| K0-08 | Network Stack | Ağ iletişimi | BSD Sockets, WinSock |
| K0-09 | Time Service | Zaman servisleri | QueryPerformanceCounter |
| K0-10 | Event System | Olay sistemi | OS event objects |
| K0-11 | Timer Service | Zamanlayıcı servisi | multimedia timer |
| K0-12 | Mutex/Spinlock | Senkronizasyon | OS primitives |
| K0-13 | Condition Variable | Koşul değişkenleri | pthread_cond, CV |
| K0-14 | Semaphore | Semafor | OS semaphores |
| K0-15 | Atomic Operations | Atomik işlemler | std::atomic, OS atomics |
| K0-16 | Cache Line | Önbellek satırı | alignas(64) |
| K0-17 | CPU Detection | İşlemci tespiti | CPUID |
| K0-18 | SIMD Support | SIMD desteği | SSE2, AVX2, NEON |
| K0-19 | Power Management | Güç yönetimi | ACPI, battery API |
| K0-20 | Thermal Management | Termal yönetim | thermal zone API |

---

### 3. Platform-Specific Detaylar


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:37-63` — satır: 27 (boş olmayan: 24).

### 2. Bileşen Haritası

| # | Bileşen | Amaç | Teknoloji |
|---|---------|------|-----------|
| K0-01 | Windows API | Win32 API soyutlama | C++20, WinRT |
| K0-02 | Linux Kernel | Kernel servisleri | Linux API, ioctl |
| K0-03 | macOS Core | macOS servisleri | CoreFoundation, IOKit |
| K0-04 | IPC Manager | Süreçler arası iletişim | Shared Memory, Pipes |
| K0-05 | Memory Manager | Bellek yönetimi | VirtualAlloc, mmap |
| K0-06 | Thread Manager | İş parçacığı yönetimi | pthreads, Win32 Threads |
| K0-07 | File System | Dosya sistemi erişimi | std::filesystem |
| K0-08 | Network Stack | Ağ iletişimi | BSD Sockets, WinSock |
| K0-09 | Time Service | Zaman servisleri | QueryPerformanceCounter |
| K0-10 | Event System | Olay sistemi | OS event objects |
| K0-11 | Timer Service | Zamanlayıcı servisi | multimedia timer |
| K0-12 | Mutex/Spinlock | Senkronizasyon | OS primitives |
| K0-13 | Condition Variable | Koşul değişkenleri | pthread_cond, CV |
| K0-14 | Semaphore | Semafor | OS semaphores |
| K0-15 | Atomic Operations | Atomik işlemler | std::atomic, OS atomics |
| K0-16 | Cache Line | Önbellek satırı | alignas(64) |
| K0-17 | CPU Detection | İşlemci tespiti | CPUID |
| K0-18 | SIMD Support | SIMD desteği | SSE2, AVX2, NEON |
| K0-19 | Power Management | Güç yönetimi | ACPI, battery API |
| K0-20 | Thermal Management | Termal yönetim | thermal zone API |

---



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:143-219` — satır: 77 (boş olmayan: 58).

#### 3.3 macOS API (K0-03)

```cpp
// macOS API Soyutlama
namespace coremusic::os::macos {

class MacOSAPI {
public:
    // CoreAudio Support
    static bool initializeCoreAudio();
    static void shutdownCoreAudio();

    // Memory
    static void* vm_allocate(size_t size);
    static void vm_deallocate(void* ptr, size_t size);

    // Threading
    static void setThreadQoS(int qos);
    static void setThreadAffinity(int core);

    // Timer
    static uint64_t getMachAbsoluteTime();
};

} // namespace coremusic::os::macos
```

---

### 4. Bellek Yönetimi (K0-05)

#### 4.1 Audio Thread Bellek Kuralları

```cpp
// Zero-Allocation Bellek Stratejisi
namespace coremusic::os::memory {

class AudioMemoryManager {
public:
    // Pre-allocated pool (compile-time)
    template<size_t Size>
    struct alignas(64) AudioBlock {
        uint8_t data[Size];
    };

    // Stack allocation (audio thread'de)
    template<size_t Size>
    static AudioBlock<Size>* getStackBlock() {
        thread_local AudioBlock<Size> block;
        return &block;
    }

    // Large page allocation (setup time'da)
    static void* allocateLargePages(size_t size);
    static void freeLargePages(void* ptr);

    // Lock-free allocator
    static void* lockFreeAlloc(size_t size);
    static void lockFreeFree(void* ptr);
};

} // namespace coremusic::os::memory
```

#### 4.2 Bellek Kısıtlamaları

| Kısıt | Değer | Açıklama |
|-------|-------|----------|
| Audio thread malloc | ❌ YASAK | Ses takılması |
| Audio thread new/delete | ❌ YASAK | Heap corruption riski |
| Audio thread vector push_back | ❌ YASAK | Reallocation |
| Stack allocation | ✅ İzinli | 64KB limit |
| Thread-local storage | ✅ İzinli | Pre-allocated |
| alignas(64) | ✅ Zorunlu | False sharing prevention |

---



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:297-342` — satır: 46 (boş olmayan: 33).

### 7. Dosya Sistemi (K0-07)

#### 7.1 Path Convention

```
Platform-specific paths:
  Windows: C:\Users\{user}\AppData\Local\CoreMusic\
  Linux:   ~/.local/share/coremusic/
  macOS:   ~/Library/Application Support/CoreMusic/
```

#### 7.2 Dosya Erişim Kuralları

| Kural | Açıklama |
|-------|----------|
| Atomic write | Temp dosya → rename |
| File locking | flock / LockFileEx |
| Path sanitization | Traverse attack prevention |
| Unicode support | UTF-8 everywhere |

---

### 8. Ağ Stack (K0-08)

#### 8.1 Socket Seviyeleri

| Seviye | API | Kullanım |
|--------|-----|----------|
| Raw | BSD Sockets | Low-level network |
| TCP | TCP Sockets | HTTP, WebSocket |
| UDP | UDP Sockets | DNS, mDNS |
| Multicast | IGMP | DLNA, AirPlay |

---

### 9. Zaman Servisi (K0-09)

#### 9.1 Zaman Ölçümleri

| Metot | Çözünürlük | Platform |
|-------|-----------|----------|
| QueryPerformanceCounter | ~100ns | Windows |
| clock_gettime(CLOCK_MONOTONIC) | ~1ns | Linux/macOS |
| mach_absolute_time | ~1ns | macOS |
| rdtsc | ~1ns | Tümü (x86) |



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:357-401` — satır: 45 (boş olmayan: 35).

### 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k0-isletim-sistemi/README.md` | Bu dosya |
| `architecture/k0-isletim-sistemi/windows-api.md` | Windows detayları |
| `architecture/k0-isletim-sistemi/linux-kernel.md` | Linux detayları |
| `architecture/k0-isletim-sistemi/macos-coreaudio.md` | macOS detayları |
| `architecture/k0-isletim-sistemi/rpi5-arm.md` | RPi5 optimizasyonu |
| `architecture/k0-isletim-sistemi/docker-container.md` | Container stratejisi |
| `architecture/k2-surucu/README.md` | K2 Sürücü katmanı (ADR-024 birleşim hedefi) |
| `architecture/k3-ses-motoru/README.md` | K3 Ses motoru |

---

### 11. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |

---

### Alt Katman Şeması (K0.a.b.c)

> **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** Bağlantı zorunludur: `K0` → `K0.a` (2. katman) → `K0.a.b` (3. katman — her `K0.a` için zorunlu) → `K0.a.b.c` (4. katman — **yalnızca diskte kanıtla** açılır: bir MD başlık satırı veya README tablo satırı; uydurma numara yok). Adlandırma ilkeleri: lowercase-hyphen klasör/dosya adları, belgede K numarası taşınır. Bu katmanda hedef: 2. katman **10** · 3. katman **≥7** (fiilen 18) · 4. katman **210** kanıtlı yaprak (fiilen 210).

#### K0 Şema Özeti

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K0.1 | Platform Çekirdekleri | 4 | 62 | linux/macos/windows/rpi5-core.md |
| K0.2 | Bellek & Süreç Yönetimi | 2 | 23 | memory-management.md, process-isolation.md |
| K0.3 | Threading Model | 1 | 11 | threading-model.md |
| K0.4 | IPC Mekanizmaları | 1 | 12 | ipc-mekanizmalari.md |
| K0.5 | Sistem Çağrıları | 1 | 15 | system-calls.md |
| K0.6 | Cross-Platform API | 1 | 16 | cross-platform-api.md |
| K0.7 | Konteyner Runtime | 1 | 16 | container-runtime.md |
| K0.8 | Windows API | 1 | 15 | windows-api.md |
| K0.9 | Dosya Sistemi / Ağ / Zaman | 3 | 8 | README.md §7-§9 |
| K0.10 | Yönetişim & Bileşen Haritası | 3 | 32 | CLAUDE.md, index.md, README §2 |
| **TOPLAM** | | **18** | **210** | |



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:680-716` — satır: 37 (boş olmayan: 30).

### Kanıt Kataloğu (K0)

> Bu dizin, K0 şemasındaki 210 yaprağın **dosya bazlı** kaynağını listeler. "Yaprak" sütunu dosyanın şemaya giren H2+H3 başlıklarının toplamını, "Kapsanan" sütunu bunlardan şemaya alınanları gösterir (ayrıntı: Alt Katman Şeması bölümü). Satır aralığı = dosyadaki ilk–son başlık satırıdır.

| # | Dosya | Rol | H2 | H3 | Kapsanan Yaprak | Satır Aralığı |
|---|-------|-----|----|----|-----------------|---------------|
| 1 | README.md | Katman özeti + bileşen haritası | 11 | 16 | 28 | L26–L387 |
| 2 | CLAUDE.md | Hard guardrails | 4 | 0 | 4 | L16–L46 |
| 3 | index.md | Mimari indeks | 6 | 2 | 8 | L11–L82 |
| 4 | linux-core.md | Linux çekirdek erişimi | 7 | 14 | 17 | L12–L603 |
| 5 | macos-core.md | macOS çekirdek erişimi | 7 | 12 | 15 | L12–L543 |
| 6 | windows-core.md | Windows çekirdek erişimi | 7 | 16 | 19 | L12–L408 |
| 7 | rpi5-core.md | Raspberry Pi 5 erişimi | 7 | 8 | 11 | L12–L419 |
| 8 | memory-management.md | Bellek yönetimi | 6 | 9 | 11 | L11–L539 |
| 9 | process-isolation.md | Süreç izolasyonu | 7 | 9 | 12 | L11–L615 |
| 10 | threading-model.md | İş parçacığı modeli | 6 | 9 | 11 | L11–L722 |
| 11 | ipc-mekanizmalari.md | Süreçler arası iletişim | 7 | 9 | 12 | L11–L644 |
| 12 | system-calls.md | Sistem çağrıları | 6 | 9 | 15 | L11–L678 |
| 13 | cross-platform-api.md | Çapraz platform API | 6 | 10 | 16 | L11–L644 |
| 14 | container-runtime.md | Konteyner runtime | 7 | 9 | 16 | L11–L497 |
| 15 | windows-api.md | ASIO/WASAPI/Win32 | 6 | 9 | 15 | L25–L253 |

**Katalog notları:**

1. **Şema toplamı:** 15 kanıt dosyası → 2. katman 10 · 3. katman 18 · 4. katman 210 yaprak (hedef 210/210).
2. **Kapsanmayan H3 (bilinçli):** 8 dosyadaki şablon başlıkları ("Gereksinimler", "Alt Katmanlar", "Üst Katmanlar", "…API Başlık Dosyası") yalnız K0.5–K0.7'de bırakıldı; 32 şablon H3 şemaya alınmadı — dizinde H3 sütununda görünür, kayıp yok.
3. **README dışı kanıt yokluğu:** K0.9 (Dosya/Ağ/Zaman) için ayrı içerik MD'si diskte yok; 4. katman yalnız README §7-§9 satırlarıyla kanıtlandı (uydurma dosya yok).
4. **Bileşen kanıtı:** K0.10.3, README §2'deki K0-01..K0-20 tablo satırlarıdır (L56–L75).
5. **Kaynak sayımı:** H2/H3 sayıları 2026-09-24 taramasıyla (grep '^## ' ve '^### ') doğrulandı; frontend-restructuring-plan.md §2.1-2.2 satırları bu katmanda gerekli olmadı (kanıt yeterli).

---

*K0 İşletim Sistemi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*


## 3. Klasör İçi Dosya Başlıkları (otomatik)

## 1. Kapsam
## 2. Gömülü Kaynaklar
## Linux Core
### Genel Bakış
### Teknik Detaylar
#### 1. Control Groups (cgroups)
#### 2. Namespaces
#### 3. systemd Integration
#### 4. D-Bus Communication
#### 5. epoll - Event Notification
#### 6. inotify - Dosya Sistemi İzleme
#### 7. seccomp - System Call Filtering
#### 8. Linux Capabilities
#### 9. tmpfs - Geçici Dosya Sistemi
#### 10. /proc Filesystem
### API / Arayüz
#### COREMUSIC Linux API Başlık Dosyası
### Bağımlılıklar
#### Gereksinimler
#### Alt Katmanlar
#### Üst Katmanlar
### Performans Metrikleri
### Güvenlik Notları
### Durum: Implementasyon
#### 3.2 Linux API (K0-02)
## 3. Kenar Durumları
## 4. Hata Modları
## 5. Bağımlılıklar
## 6. Sorumluluk Matrix
## 7. Kanıt Kataloğu
## 8. Doğrulama Durumu

## 1. Kapsam
## 2. Gömülü Kaynaklar
## RPi5 Core
### Genel Bakış
### Teknik Detaylar
#### 1. GPIO Control
#### 2. DMA Engine
#### 3. PWM Audio
#### 4. I2S Interface
### API / Arayüz
#### COREMUSIC RPi5 API Başlık Dosyası
### Bağımlılıklar
#### Gereksinimler
#### Alt Katmanlar
#### Üst Katmanlar
### Performans Metrikleri
### Donanım Notları
### Durum: Implementasyon
#### K0.8 — Windows API
#### K0.9 — Dosya Sistemi / Ağ / Zaman
#### K0.10 — Yönetişim & Bileşen Haritası
### 8. Ağ Stack (K0-08)
#### 8.1 Socket Seviyeleri
### 9. Zaman Servisi (K0-09)
#### 9.1 Zaman Ölçümleri
#### 9.2 ASIO Zamanlama
#### K0.7 — Konteyner Runtime
## 3. Platform Kenar Durumları
## 4. Hata Modları
## 5. Bağımlılıklar
## 6. Ölçüm ve Persona
## 7. Kanıt Kataloğu
## 8. Doğrulama Durumu

## 4. Komşu Klasörler (D01 içinde wiki-link)

| K | Klasör | Dosyalar (wiki-link) | Amaç |
|---|--------|----------------------|------|
| `K000` | k000-windows-core | [[../k000-windows-core/windows-core-mimari]] · [[../k000-windows-core/windows-api-yuzeyi]] · [[../k000-windows-core/index]] | Windows cekirdek katmani: NT mimarisi, Win32/WinRT API yuzeyi |
| `K002` | k002-macos-tasinabilirlik | [[../k002-macos-tasinabilirlik/macos-core-mimari]] · [[../k002-macos-tasinabilirlik/cross-platform-api-soyutlama]] · [[../k002-macos-tasinabilirlik/index]] | macOS XNU cekirdek + platform-bagimsiz API soyutlamasi |
| `K003` | k003-cagri-thread | [[../k003-cagri-thread/system-calls-rehberi]] · [[../k003-cagri-thread/threading-model-detay]] · [[../k003-cagri-thread/index]] | Sistem cagri yollari ve coklu is parcacigi modeli |
| `K004` | k004-surec-ipc | [[../k004-surec-ipc/process-isolation-stratejileri]] · [[../k004-surec-ipc/ipc-mekanizmalari-detay]] · [[../k004-surec-ipc/index]] | Surec izolasyonu ve surec-arasi iletisim mekanizmalari |
| `K005` | k005-bellek-container | [[../k005-bellek-container/bellek-yonetimi]] · [[../k005-bellek-container/container-runtime-ortami]] · [[../k005-bellek-container/index]] | Bellek yonetimi ve container calisma ortami |
| `K006` | k006-dac-adc-zinciri | [[../k006-dac-adc-zinciri/dac-adc-zinciri]] · [[../k006-dac-adc-zinciri/index]] | DAC/ADC cevrimi ve PCM3168A konfigurasyonu |
| `K007` | k007-ak4458-xmos | [[../k007-ak4458-xmos/ak4458-xmos-islemci]] · [[../k007-ak4458-xmos/index]] | AK4458 DAC + XMOS XU316 dijital islemci |
| `K008` | k008-analog-giris | [[../k008-analog-giris/analog-sinyal-yolu]] · [[../k008-analog-giris/index]] | Analog sinyal yolu ve diferansiyel giris asamasi |
| `K009` | k009-vas-feedback | [[../k009-vas-feedback/vas-feedback-tasarimi]] · [[../k009-vas-feedback/index]] | VAS guc asamasi ve geri besleme agi |
| `K010` | k010-class-ab-cikis | [[../k010-class-ab-cikis/class-ab-cikis-asamasi]] · [[../k010-class-ab-cikis/index]] | Class AB amplifikator, cikis asamasi, guc transistörleri |
| `K011` | k011-guc-koruma-termal | [[../k011-guc-koruma-termal/guc-koruma-termal]] · [[../k011-guc-koruma-termal/index]] | Analog guc kaynagi, koruma devreleri, termal yonetim |
| `K012` | k012-dijital-arayuz | [[../k012-dijital-arayuz/dijital-arayuzler]] · [[../k012-dijital-arayuz/index]] | I2S/USB arayuzleri ve konnektor secimi |
| `K013` | k013-pcb-hoparlor | [[../k013-pcb-hoparlor/pcb-ve-hoparlor]] · [[../k013-pcb-hoparlor/index]] | PCB tasarim ilkeleri, hoparlor dizilimi, ozet durum |
| `K014` | k014-surucu-yigin | [[../k014-surucu-yigin/driver-stack-ve-buffer]] · [[../k014-surucu-yigin/latency-optimization-teknikleri]] · [[../k014-surucu-yigin/index]] | Surucu yigin mimarisi, buffer yonetimi, gecikme optimizasyonu |
| `K015` | k015-platform-suruculeri | [[../k015-platform-suruculeri/asio-wasapi-coreaudio]] · [[../k015-platform-suruculeri/index]] | ASIO/WASAPI (Windows) ve CoreAudio (macOS) suruculeri |
| `K016` | k016-linux-ses | [[../k016-linux-ses/linux-ses-yigini]] · [[../k016-linux-ses/index]] | ALSA native ve PipeWire modern ses yiginı |
| `K017` | k017-uzak-bluetooth-usb | [[../k017-uzak-bluetooth-usb/uzak-ses-suruculeri]] · [[../k017-uzak-bluetooth-usb/index]] | USB-audio class, Bluetooth A2DP, ag (network) ses suruculeri |

## 5. Klasör Dosya Kunyeleri

| Dosya | Başlık | Satır |
|-------|--------|------:|
| [[linux-cekirdek-mimari]] | K001 — Linux Çekirdek Mimarisi (POSIX Çekirdek) | 637 |
| [[rpi5-gomulu-platform]] | K001 — Raspberry Pi 5 Gömülü Platform | 527 |
| [[index]] | K001 — Linux / Raspberry Pi 5 Klasör Dizini | 506 |

## 6. Kaynak Envanteri (salt-okunur alan dizini)

| # | Kaynak dosya | Satır | Boş olmayan | Durum |
|---|--------------|------:|------------:|-------|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` | 58 | 44 | okundu (salt-okunur) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 732 | 609 | okundu (salt-okunur) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 512 | 440 | okundu (salt-okunur) |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | 659 | 527 | okundu (salt-okunur) |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` | 97 | 80 | okundu (salt-okunur) |
| 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | 659 | 519 | okundu (salt-okunur) |
| 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 618 | 494 | okundu (salt-okunur) |
| 8 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | 558 | 440 | okundu (salt-okunur) |
| 9 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/memory-management.md` | 554 | 448 | okundu (salt-okunur) |
| 10 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | 630 | 498 | okundu (salt-okunur) |
| 11 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 435 | 346 | okundu (salt-okunur) |
| 12 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | 693 | 548 | okundu (salt-okunur) |
| 13 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | 737 | 592 | okundu (salt-okunur) |
| 14 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 266 | 213 | okundu (salt-okunur) |
| 15 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 423 | 322 | okundu (salt-okunur) |

## 7. Bağımlılık ve Sınır Notları

| Sınır | Yön | Hedef | Not |
|---|---|---|---|
| Linux ↔ Windows | yatay | `[[../k000-windows-core/index]]` | aynı katman, farklı platform |
| Linux ↔ macOS | yatay | `[[../k002-macos-tasinabilirlik/index]]` | taşınabilirlik karşılaştırma kaynağı |
| Çekirdek → çağrı/thread | aşağı | `[[../k003-cagri-thread/index]]` | POSIX çağrı ve thread modeli |
| Çekirdek → bellek/container | aşağı | `[[../k005-bellek-container/index]]` | konteyner çalışma ortamı |
| Platform → Linux ses | aşağı | `[[../k016-linux-ses/index]]` | ALSA/PipeWire sürücüleri |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` (katman bağımlılıkları).

## 8. Kanıt ve Doğrulama

1. `Kanıt:` tüm içerik gömülü salt-okunur kaynaklardan gelir (SRCTABLE §2).
2. `⚠️ VERIFICATION REQUIRED` — RPi5 donanımında ölçülmüş gecikme/xrun değerleri.
3. `⚠️ VERIFICATION REQUIRED` — çekirdek sürümüne özel RT yama durumu (disk kanıtı yok).
4. Dosya adları değiştirilmemiştir; yeni bağımlılık eklenmemiştir.

---
*Bu dizin `k001-linux-rpi5/` klasörünün SSOT özetidir.*
