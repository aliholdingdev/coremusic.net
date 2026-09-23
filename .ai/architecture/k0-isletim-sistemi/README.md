---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K0 İşletim Sistemi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
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
| `architecture/k-surucu/README.md` | K2 Sürücü katmanı |
| `architecture/k3-ses-motoru/README.md` | K3 Ses motoru |

---

## 11. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |

---

*K0 İşletim Sistemi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
