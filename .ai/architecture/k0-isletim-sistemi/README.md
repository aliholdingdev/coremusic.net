---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K0 İşletim Sistemi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K0: İşletim Sistemi Layer

**Katman:** K0 (En Alt Katman)
**Kapsam:** İşletim sistemi servisleri, API'ler, sürücüler, Bellek yönetimi, Threading
**Sorumlu Agent:** Windows Software Engineer / Embedded Engineer
**Bileşen Sayısı:** 50

---

## 1. Genel Bakış

K0 katmanı, CoreMusic'in tüm katmanlarının temelini oluşturan işletim sistemi hizmetlerini içerir. Bu katman, donanım ile yazılım arasındaki köprüyü kurar ve tüm üst katmanlara temel OS servislerini sunar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Platform Bağımsızlık** | Tüm OS'ler için ortak API soyutlama katmanı |
| **Low-Level Erişim** | Donanım sürücülerine doğrudan erişim |
| **Bellek Yönetimi** | Zero-allocation audio thread için OS desteği |
| **Threading** | Real-time thread önceliği ve senkronizasyon |
| **IPC** | Süreçler arası iletişim |

### 1.2 Desteklenen Platformlar

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| Tier 1 | Windows (XP-11, Server 2012 R2+) | ✅ Ana geliştirme | ASIO, WASAPI |
| Tier 2 | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli | ALSA, PipeWire |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli | CoreAudio |
| Tier 4 | Raspberry Pi (ARM64) | ✅ Destekli | I2S |
| Tier 5 | ReactOS | ⚠️ Experimental | Sınırlı |

---

## 2. Bileşen Haritası

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

## 3. Platform-Specific Detaylar

### 3.1 Windows API (K0-01)

```cpp
// Windows API Soyutlama
namespace coremusic::os::windows {

class WindowsAPI {
public:
    // ASIO Support
    static bool initializeASIO();
    static void shutdownASIO();

    // WASAPI Support
    static bool initializeWASAPI();
    static void shutdownWASAPI();

    // Memory
    static void* allocateLargePages(size_t size);
    static void freeLargePages(void* ptr);

    // Threading
    static void setThreadPriority(int priority);
    static void setThreadAffinity(int core);

    // Timer
    static uint64_t getPerformanceCounter();
    static uint64_t getPerformanceFrequency();
};

} // namespace coremusic::os::windows
```

**Kritik API'ler:**
- `QueryPerformanceCounter` — High-resolution timer
- `VirtualAlloc` — Large page allocation
- `CreateThread` / `SetThreadPriority` — Thread yönetimi
- `WaitForSingleObject` — Senkronizasyon
- `CoInitializeEx` — COM initialization (WASAPI için)

### 3.2 Linux API (K0-02)

```cpp
// Linux API Soyutlama
namespace coremusic::os::linux {

class LinuxAPI {
public:
    // ALSA Support
    static bool initializeALSA();
    static void shutdownALSA();

    // PipeWire Support
    static bool initializePipeWire();
    static void shutdownPipeWire();

    // Memory
    static void* mmapAllocate(size_t size);
    static void mmapFree(void* ptr, size_t size);

    // Threading
    static void setRealtimePriority(int priority);
    static void setCPUAffinity(int core);

    // Timer
    static uint64_t getClockMonotonic();
};

} // namespace coremusic::os::linux
```

**Kritik API'ler:**
- `clock_gettime(CLOCK_MONOTONIC)` — High-resolution timer
- `mmap` — Large page allocation
- `pthread_create` / `pthread_setschedparam` — Thread yönetimi
- `snd_pcm_*` — ALSA API
- `pw_*` — PipeWire API

### 3.3 macOS API (K0-03)

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

## 4. Bellek Yönetimi (K0-05)

### 4.1 Audio Thread Bellek Kuralları

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

### 4.2 Bellek Kısıtlamaları

| Kısıt | Değer | Açıklama |
|-------|-------|----------|
| Audio thread malloc | ❌ YASAK | Ses takılması |
| Audio thread new/delete | ❌ YASAK | Heap corruption riski |
| Audio thread vector push_back | ❌ YASAK | Reallocation |
| Stack allocation | ✅ İzinli | 64KB limit |
| Thread-local storage | ✅ İzinli | Pre-allocated |
| alignas(64) | ✅ Zorunlu | False sharing prevention |

---

## 5. Threading (K0-06)

### 5.1 Thread Öncelik Hiyerarşisi

| Thread | Öncelik | CPU Core | Kullanım |
|--------|---------|----------|----------|
| Audio Processing | TIME_CRITICAL (15) | Dedicated | ASIO callback |
| DSP Processing | HIGHEST (10) | Dedicated | EQ, reverb, compressor |
| Network I/O | ABOVE_NORMAL (8) | Shared | Streaming, DLNA |
| UI Rendering | NORMAL (0) | Shared | Arayüz |
| Background Tasks | BELOW_NORMAL (-1) | Shared | İndirme, indeksleme |
| Idle | LOWEST (-2) | Shared | Cleanup, garbage collection |

### 5.2 Thread Güvenliği

```cpp
// Lock-Free Veri Yapısı
namespace coremusic::os::threading {

class LockFreeQueue {
public:
    void push(void* item) noexcept {
        Node* node = getNode(); // Pre-allocated
        node->data.store(item, std::memory_order_relaxed);

        Node* tail = _tail.load(std::memory_order_relaxed);
        Node* next = tail->next.load(std::memory_order_relaxed);

        if (next == nullptr) {
            if (tail->next.compare_exchange_weak(next, node,
                std::memory_order_release)) {
                _tail.compare_exchange_strong(tail, node,
                    std::memory_order_release);
            }
        }
    }

private:
    struct Node {
        std::atomic<void*> data{nullptr};
        std::atomic<Node*> next{nullptr};
    };

    alignas(64) std::atomic<Node*> _head;
    alignas(64) std::atomic<Node*> _tail;
};

} // namespace coremusic::os::threading
```

---

## 6. IPC Manager (K0-04)

### 6.1 Platform-Specific IPC

| Platform | Yöntem | Kullanım |
|----------|--------|----------|
| Windows | Named Pipes, Shared Memory | Servis iletişimi |
| Linux | Unix Domain Sockets, POSIX SHM | Servis iletişimi |
| macOS | Mach Ports, POSIX SHM | Servis iletişimi |

### 6.2 IPC Mesaj Formatı

```cpp
struct IPCMessage {
    uint32_t type;        // Mesaj tipi
    uint32_t size;        // Payload boyutu
    uint64_t timestamp;   // Zaman damgası
    uint32_t sender_pid;  // Gönderen PID
    uint32_t flags;       // Bayraklar
    uint8_t payload[];    // Değişken boyutlu veri
};
```

---

## 7. Dosya Sistemi (K0-07)

### 7.1 Path Convention

```
Platform-specific paths:
  Windows: C:\Users\{user}\AppData\Local\CoreMusic\
  Linux:   ~/.local/share/coremusic/
  macOS:   ~/Library/Application Support/CoreMusic/
```

### 7.2 Dosya Erişim Kuralları

| Kural | Açıklama |
|-------|----------|
| Atomic write | Temp dosya → rename |
| File locking | flock / LockFileEx |
| Path sanitization | Traverse attack prevention |
| Unicode support | UTF-8 everywhere |

---

## 8. Ağ Stack (K0-08)

### 8.1 Socket Seviyeleri

| Seviye | API | Kullanım |
|--------|-----|----------|
| Raw | BSD Sockets | Low-level network |
| TCP | TCP Sockets | HTTP, WebSocket |
| UDP | UDP Sockets | DNS, mDNS |
| Multicast | IGMP | DLNA, AirPlay |

---

## 9. Zaman Servisi (K0-09)

### 9.1 Zaman Ölçümleri

| Metot | Çözünürlük | Platform |
|-------|-----------|----------|
| QueryPerformanceCounter | ~100ns | Windows |
| clock_gettime(CLOCK_MONOTONIC) | ~1ns | Linux/macOS |
| mach_absolute_time | ~1ns | macOS |
| rdtsc | ~1ns | Tümü (x86) |

### 9.2 ASIO Zamanlama

```cpp
// ASIO TimeInfo
struct ASIOTimeInfo {
    double speed;           // Sample rate ratio
    int64_t systemTime;    // Nanoseconds since epoch
    int64_t samplePosition;// Current sample position
    int64_t sampleRate;    // Current sample rate
};
```

---

## 10. İlgili Dosyalar

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

## 11. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |

---

## Alt Katman Şeması (K0.a.b.c)

> **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** Bağlantı zorunludur: `K0` → `K0.a` (2. katman) → `K0.a.b` (3. katman — her `K0.a` için zorunlu) → `K0.a.b.c` (4. katman — **yalnızca diskte kanıtla** açılır: bir MD başlık satırı veya README tablo satırı; uydurma numara yok). Adlandırma ilkeleri: lowercase-hyphen klasör/dosya adları, belgede K numarası taşınır. Bu katmanda hedef: 2. katman **10** · 3. katman **≥7** (fiilen 18) · 4. katman **210** kanıtlı yaprak (fiilen 210).

### K0 Şema Özeti

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

### K0.1 — Platform Çekirdekleri

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.1.1** | Linux Core (3. katman) | linux-core.md — 17 yaprak |
| K0.1.1.1 | Genel Bakış | linux-core.md L12 |
| K0.1.1.2 | Teknik Detaylar | linux-core.md L16 |
| K0.1.1.3 | API / Arayüz | linux-core.md L512 |
| K0.1.1.4 | Bağımlılıklar | linux-core.md L567 |
| K0.1.1.5 | Performans Metrikleri | linux-core.md L585 |
| K0.1.1.6 | Güvenlik Notları | linux-core.md L595 |
| K0.1.1.7 | Durum: Implementasyon | linux-core.md L603 |
| K0.1.1.8 | Control Groups (cgroups) | linux-core.md L18 |
| K0.1.1.9 | Namespaces | linux-core.md L74 |
| K0.1.1.10 | systemd Integration | linux-core.md L133 |
| K0.1.1.11 | D-Bus Communication | linux-core.md L193 |
| K0.1.1.12 | epoll — Event Notification | linux-core.md L241 |
| K0.1.1.13 | inotify — Dosya Sistemi İzleme | linux-core.md L280 |
| K0.1.1.14 | seccomp — System Call Filtering | linux-core.md L321 |
| K0.1.1.15 | Linux Capabilities | linux-core.md L353 |
| K0.1.1.16 | tmpfs — Geçici Dosya Sistemi | linux-core.md L400 |
| K0.1.1.17 | /proc Filesystem | linux-core.md L423 |
| **K0.1.2** | macOS Core (3. katman) | macos-core.md — 15 yaprak |
| K0.1.2.1 | Genel Bakış | macos-core.md L12 |
| K0.1.2.2 | Teknik Detaylar | macos-core.md L16 |
| K0.1.2.3 | API / Arayüz | macos-core.md L457 |
| K0.1.2.4 | Bağımlılıklar | macos-core.md L507 |
| K0.1.2.5 | Performans Metrikleri | macos-core.md L525 |
| K0.1.2.6 | Güvenlik Notları | macos-core.md L535 |
| K0.1.2.7 | Durum: Implementasyon | macos-core.md L543 |
| K0.1.2.8 | Grand Central Dispatch (GCD) | macos-core.md L18 |
| K0.1.2.9 | XPC Communication | macos-core.md L73 |
| K0.1.2.10 | Core Foundation | macos-core.md L131 |
| K0.1.2.11 | Metal GPU Hesaplama | macos-core.md L209 |
| K0.1.2.12 | IOKit — Donanım Erişimi | macos-core.md L267 |
| K0.1.2.13 | LaunchAgent / LaunchDaemon | macos-core.md L338 |
| K0.1.2.14 | App Sandbox | macos-core.md L385 |
| K0.1.2.15 | Hardened Runtime | macos-core.md L422 |
| **K0.1.3** | Windows Core (3. katman) | windows-core.md — 19 yaprak |
| K0.1.3.1 | Genel Bakış | windows-core.md L12 |
| K0.1.3.2 | Teknik Detaylar | windows-core.md L16 |
| K0.1.3.3 | API / Arayüz | windows-core.md L332 |
| K0.1.3.4 | Bağımlılıklar | windows-core.md L373 |
| K0.1.3.5 | Performans Metrikleri | windows-core.md L390 |
| K0.1.3.6 | Güvenlik Notları | windows-core.md L400 |
| K0.1.3.7 | Durum: Implementasyon | windows-core.md L408 |
| K0.1.3.8 | Process Management | windows-core.md L18 |
| K0.1.3.9 | Memory Management | windows-core.md L51 |
| K0.1.3.10 | Threading Model | windows-core.md L82 |
| K0.1.3.11 | Registry Operations | windows-core.md L113 |
| K0.1.3.12 | Service Management | windows-core.md L138 |
| K0.1.3.13 | Event Log | windows-core.md L162 |
| K0.1.3.14 | Windows Management Instrumentation (WMI) | windows-core.md L182 |
| K0.1.3.15 | COM Interface | windows-core.md L205 |
| K0.1.3.16 | IPC — Named Pipes | windows-core.md L237 |
| K0.1.3.17 | Memory Mapped Files | windows-core.md L258 |
| K0.1.3.18 | Job Objects | windows-core.md L282 |
| K0.1.3.19 | User Account Control (UAC) | windows-core.md L306 |
| **K0.1.4** | RPi5 Core (3. katman) | rpi5-core.md — 11 yaprak |
| K0.1.4.1 | Genel Bakış | rpi5-core.md L12 |
| K0.1.4.2 | Teknik Detaylar | rpi5-core.md L16 |
| K0.1.4.3 | API / Arayüz | rpi5-core.md L338 |
| K0.1.4.4 | Bağımlılıklar | rpi5-core.md L383 |
| K0.1.4.5 | Performans Metrikleri | rpi5-core.md L401 |
| K0.1.4.6 | Donanım Notları | rpi5-core.md L411 |
| K0.1.4.7 | Durum: Implementasyon | rpi5-core.md L419 |
| K0.1.4.8 | GPIO Control | rpi5-core.md L18 |
| K0.1.4.9 | DMA Engine | rpi5-core.md L101 |
| K0.1.4.10 | PWM Audio | rpi5-core.md L171 |
| K0.1.4.11 | I2S Interface | rpi5-core.md L242 |

### K0.2 — Bellek & Süreç Yönetimi

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.2.1** | Memory Management (3. katman) | memory-management.md — 11 yaprak |
| K0.2.1.1 | Genel Bakış | memory-management.md L11 |
| K0.2.1.2 | Teknik Detaylar | memory-management.md L15 |
| K0.2.1.3 | API / Arayüz | memory-management.md L463 |
| K0.2.1.4 | Bağımlılıklar | memory-management.md L513 |
| K0.2.1.5 | Performans Metrikleri | memory-management.md L529 |
| K0.2.1.6 | Durum: Implementasyon | memory-management.md L539 |
| K0.2.1.7 | Virtual Memory | memory-management.md L17 |
| K0.2.1.8 | Page Tables | memory-management.md L75 |
| K0.2.1.9 | Memory Pool | memory-management.md L153 |
| K0.2.1.10 | Slab Allocator | memory-management.md L229 |
| K0.2.1.11 | Buffer Management | memory-management.md L353 |
| **K0.2.2** | Process Isolation (3. katman) | process-isolation.md — 12 yaprak |
| K0.2.2.1 | Genel Bakış | process-isolation.md L11 |
| K0.2.2.2 | Teknik Detaylar | process-isolation.md L15 |
| K0.2.2.3 | API / Arayüz | process-isolation.md L531 |
| K0.2.2.4 | Bağımlılıklar | process-isolation.md L582 |
| K0.2.2.5 | Performans Metrikleri | process-isolation.md L597 |
| K0.2.2.6 | Güvenlik Notları | process-isolation.md L607 |
| K0.2.2.7 | Durum: Implementasyon | process-isolation.md L615 |
| K0.2.2.8 | Process Sandbox | process-isolation.md L17 |
| K0.2.2.9 | Capability Dropping | process-isolation.md L118 |
| K0.2.2.10 | chroot | process-isolation.md L218 |
| K0.2.2.11 | Namespaces | process-isolation.md L298 |
| K0.2.2.12 | seccomp-bpf | process-isolation.md L414 |

### K0.3 — Threading Model

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.3.1** | Threading Model (3. katman) | threading-model.md — 11 yaprak |
| K0.3.1.1 | Genel Bakış | threading-model.md L11 |
| K0.3.1.2 | Teknik Detaylar | threading-model.md L15 |
| K0.3.1.3 | API / Arayüz | threading-model.md L623 |
| K0.3.1.4 | Bağımlılıklar | threading-model.md L696 |
| K0.3.1.5 | Performans Metrikleri | threading-model.md L711 |
| K0.3.1.6 | Durum: Implementasyon | threading-model.md L722 |
| K0.3.1.7 | Thread Pools | threading-model.md L17 |
| K0.3.1.8 | Lock-free Structures | threading-model.md L182 |
| K0.3.1.9 | Atomic Operations | threading-model.md L316 |
| K0.3.1.10 | Condition Variables | threading-model.md L443 |
| K0.3.1.11 | Mutex Hierarchy | threading-model.md L520 |

### K0.4 — IPC Mekanizmaları

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.4.1** | IPC Mekanizmaları (3. katman) | ipc-mekanizmalari.md — 12 yaprak |
| K0.4.1.1 | Genel Bakış | ipc-mekanizmalari.md L11 |
| K0.4.1.2 | Teknik Detaylar | ipc-mekanizmalari.md L15 |
| K0.4.1.3 | API / Arayüz | ipc-mekanizmalari.md L554 |
| K0.4.1.4 | Bağımlılıklar | ipc-mekanizmalari.md L609 |
| K0.4.1.5 | Performans Metrikleri | ipc-mekanizmalari.md L626 |
| K0.4.1.6 | Güvenlik Notları | ipc-mekanizmalari.md L636 |
| K0.4.1.7 | Durum: Implementasyon | ipc-mekanizmalari.md L644 |
| K0.4.1.8 | Unix Domain Sockets | ipc-mekanizmalari.md L17 |
| K0.4.1.9 | Windows Named Pipes | ipc-mekanizmalari.md L104 |
| K0.4.1.10 | Shared Memory | ipc-mekanizmalari.md L211 |
| K0.4.1.11 | Message Queues | ipc-mekanizmalari.md L317 |
| K0.4.1.12 | gRPC | ipc-mekanizmalari.md L384 |

### K0.5 — Sistem Çağrıları

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.5.1** | System Calls (3. katman) | system-calls.md — 15 yaprak |
| K0.5.1.1 | Genel Bakış | system-calls.md L11 |
| K0.5.1.2 | Teknik Detaylar | system-calls.md L15 |
| K0.5.1.3 | API / Arayüz | system-calls.md L592 |
| K0.5.1.4 | Bağımlılıklar | system-calls.md L650 |
| K0.5.1.5 | Performans Metrikleri | system-calls.md L667 |
| K0.5.1.6 | Durum: Implementasyon | system-calls.md L678 |
| K0.5.1.7 | POSIX Syscall Interface | system-calls.md L17 |
| K0.5.1.8 | Windows NT API | system-calls.md L121 |
| K0.5.1.9 | Linux io_uring | system-calls.md L265 |
| K0.5.1.10 | epoll (Linux) | system-calls.md L403 |
| K0.5.1.11 | kqueue (macOS/BSD) | system-calls.md L486 |
| K0.5.1.12 | COREMUSIC System Calls API Başlık Dosyası | system-calls.md L594 |
| K0.5.1.13 | Gereksinimler | system-calls.md L652 |
| K0.5.1.14 | Alt Katmanlar | system-calls.md L658 |
| K0.5.1.15 | Üst Katmanlar | system-calls.md L662 |

### K0.6 — Cross-Platform API

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.6.1** | Cross-Platform API (3. katman) | cross-platform-api.md — 16 yaprak |
| K0.6.1.1 | Genel Bakış | cross-platform-api.md L11 |
| K0.6.1.2 | Teknik Detaylar | cross-platform-api.md L15 |
| K0.6.1.3 | API / Arayüz | cross-platform-api.md L541 |
| K0.6.1.4 | Bağımlılıklar | cross-platform-api.md L614 |
| K0.6.1.5 | Performans Metrikleri | cross-platform-api.md L634 |
| K0.6.1.6 | Durum: Implementasyon | cross-platform-api.md L644 |
| K0.6.1.7 | POSIX Threads (pthreads) | cross-platform-api.md L17 |
| K0.6.1.8 | SDL2 — Simple DirectMedia Layer | cross-platform-api.md L114 |
| K0.6.1.9 | libuv — Asenkron I/O | cross-platform-api.md L170 |
| K0.6.1.10 | libevent | cross-platform-api.md L272 |
| K0.6.1.11 | Boost.Asio (C++) | cross-platform-api.md L332 |
| K0.6.1.12 | Rust tokio | cross-platform-api.md L433 |
| K0.6.1.13 | COREMUSIC Cross-Platform API Başlık Dosyası | cross-platform-api.md L543 |
| K0.6.1.14 | Gereksinimler | cross-platform-api.md L616 |
| K0.6.1.15 | Alt Katmanlar | cross-platform-api.md L625 |
| K0.6.1.16 | Üst Katmanlar | cross-platform-api.md L629 |

### K0.7 — Konteyner Runtime

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.7.1** | Container Runtime (3. katman) | container-runtime.md — 16 yaprak |
| K0.7.1.1 | Genel Bakış | container-runtime.md L11 |
| K0.7.1.2 | Teknik Detaylar | container-runtime.md L15 |
| K0.7.1.3 | API / Arayüz | container-runtime.md L414 |
| K0.7.1.4 | Bağımlılıklar | container-runtime.md L461 |
| K0.7.1.5 | Performans Metrikleri | container-runtime.md L479 |
| K0.7.1.6 | Güvenlik Notları | container-runtime.md L489 |
| K0.7.1.7 | Durum: Implementasyon | container-runtime.md L497 |
| K0.7.1.8 | Docker Engine Entegrasyonu | container-runtime.md L17 |
| K0.7.1.9 | containerd Entegrasyonu | container-runtime.md L53 |
| K0.7.1.10 | Kubernetes Entegrasyonu | container-runtime.md L113 |
| K0.7.1.11 | Docker Compose | container-runtime.md L245 |
| K0.7.1.12 | Health Check Implementasyonu | container-runtime.md L330 |
| K0.7.1.13 | COREMUSIC Container API Başlık Dosyası | container-runtime.md L416 |
| K0.7.1.14 | Gereksinimler | container-runtime.md L463 |
| K0.7.1.15 | Alt Katmanlar | container-runtime.md L469 |
| K0.7.1.16 | Üst Katmanlar | container-runtime.md L474 |

### K0.8 — Windows API

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.8.1** | Windows API (3. katman) | windows-api.md — 15 yaprak |
| K0.8.1.1 | ASIO SDK Entegrasyonu | windows-api.md L25 |
| K0.8.1.2 | WASAPI Entegrasyonu | windows-api.md L91 |
| K0.8.1.3 | Windows Threading | windows-api.md L150 |
| K0.8.1.4 | Bellek Yönetimi | windows-api.md L188 |
| K0.8.1.5 | COM Initialization | windows-api.md L238 |
| K0.8.1.6 | Windows Specific ADR'ler | windows-api.md L253 |
| K0.8.1.7 | ASIO 2.3 SDK Yapısı | windows-api.md L27 |
| K0.8.1.8 | ASIO Callback Protokolü | windows-api.md L50 |
| K0.8.1.9 | ASIO Buffer Konfigürasyonu | windows-api.md L79 |
| K0.8.1.10 | WASAPI Modları | windows-api.md L93 |
| K0.8.1.11 | WASAPI Akışı | windows-api.md L101 |
| K0.8.1.12 | Thread Oluşturma | windows-api.md L152 |
| K0.8.1.13 | Senkronizasyon Primitifleri | windows-api.md L175 |
| K0.8.1.14 | Large Page Allocation | windows-api.md L190 |
| K0.8.1.15 | Memory-Mapped Files | windows-api.md L206 |

### K0.9 — Dosya Sistemi / Ağ / Zaman

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.9.1** | Dosya Sistemi (3. katman) | README.md §7 — 3 yaprak |
| K0.9.1.1 | Dosya Sistemi (K0-07) | README.md L312 |
| K0.9.1.2 | Path Convention | README.md L314 |
| K0.9.1.3 | Dosya Erişim Kuralları | README.md L323 |
| **K0.9.2** | Ağ Stack (3. katman) | README.md §8 — 2 yaprak |
| K0.9.2.1 | Ağ Stack (K0-08) | README.md L334 |
| K0.9.2.2 | Socket Seviyeleri | README.md L336 |
| **K0.9.3** | Zaman Servisi (3. katman) | README.md §9 — 3 yaprak |
| K0.9.3.1 | Zaman Servisi (K0-09) | README.md L347 |
| K0.9.3.2 | Zaman Ölçümleri | README.md L349 |
| K0.9.3.3 | ASIO Zamanlama | README.md L358 |

### K0.10 — Yönetişim & Bileşen Haritası

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.10.1** | CLAUDE.md Guardrails (3. katman) | CLAUDE.md — 4 yaprak |
| K0.10.1.1 | Hard Guardrails | CLAUDE.md L16 |
| K0.10.1.2 | Teknoloji Kısıtlamaları | CLAUDE.md L25 |
| K0.10.1.3 | Yasaklı Örüntüler | CLAUDE.md L35 |
| K0.10.1.4 | İlgili ADR'ler | CLAUDE.md L46 |
| **K0.10.2** | index.md (3. katman) | index.md — 8 yaprak |
| K0.10.2.1 | Genel Bakış | index.md L11 |
| K0.10.2.2 | Mimari Diyagram | index.md L15 |
| K0.10.2.3 | Bileşen Listesi | index.md L47 |
| K0.10.2.4 | Bağımlılıklar | index.md L63 |
| K0.10.2.5 | Alt Katmanlar | index.md L65 |
| K0.10.2.6 | Üst Katmanlar | index.md L69 |
| K0.10.2.7 | Temel İlkeler | index.md L74 |
| K0.10.2.8 | Durum: Implementasyon | index.md L82 |
| **K0.10.3** | README Bileşen Haritası (3. katman) | README.md §2 — 20 yaprak |
| K0.10.3.1 | K0-01 Windows API | README.md L56 |
| K0.10.3.2 | K0-02 Linux Kernel | README.md L57 |
| K0.10.3.3 | K0-03 macOS Core | README.md L58 |
| K0.10.3.4 | K0-04 IPC Manager | README.md L59 |
| K0.10.3.5 | K0-05 Memory Manager | README.md L60 |
| K0.10.3.6 | K0-06 Thread Manager | README.md L61 |
| K0.10.3.7 | K0-07 File System | README.md L62 |
| K0.10.3.8 | K0-08 Network Stack | README.md L63 |
| K0.10.3.9 | K0-09 Time Service | README.md L64 |
| K0.10.3.10 | K0-10 Event System | README.md L65 |
| K0.10.3.11 | K0-11 Timer Service | README.md L66 |
| K0.10.3.12 | K0-12 Mutex/Spinlock | README.md L67 |
| K0.10.3.13 | K0-13 Condition Variable | README.md L68 |
| K0.10.3.14 | K0-14 Semaphore | README.md L69 |
| K0.10.3.15 | K0-15 Atomic Operations | README.md L70 |
| K0.10.3.16 | K0-16 Cache Line | README.md L71 |
| K0.10.3.17 | K0-17 CPU Detection | README.md L72 |
| K0.10.3.18 | K0-18 SIMD Support | README.md L73 |
| K0.10.3.19 | K0-19 Power Management | README.md L74 |
| K0.10.3.20 | K0-20 Thermal Management | README.md L75 |

## Kanıt Kataloğu (K0)

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
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
