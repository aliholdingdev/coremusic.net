---
title: "K002 macOS + Çapraz Platform API — Klasör Dizini (index)"
type: architecture-index
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K002 — macOS / Taşınabilirlik Klasör Dizini

| Alan | Değer |
|---|---|
| **K numarası** | K002 |
| **Ad** | macOS Çekirdek + Çapraz Platform API Soyutlaması |
| **Amaç** | XNU çekirdek katmanını ve üç platformu birleştiren API soyutlama seçeneklerini tek klasörde toplamak |
| **Bağımlılık** | Yukarı: sistem/ekipman · aşağı: `[[../k003-cagri-thread/index]]`, `[[../k015-platform-suruculeri/index]]` |
| **Sorumlu persona** | `embedded-engineer` (birincil), `performance-engineer` (ölçüm), `code-reviewer` (soyutlama) |
| **Kanıt** | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (salt-okunur yedek) |

## 1. Klasör Dosyaları (inner MD + wiki-link)

| # | Dosya (wiki-link) | Ad | Amaç |
|---|---|---|---|
| 1 | `[[macos-core-mimari]]` | macOS Çekirdek Mimarisi | XNU/Darwin yerleşimi, platforma özgü davranış ve hata modları |
| 2 | `[[cross-platform-api-soyutlama]]` | Çapraz Platform API | pthread / SDL2 / libuv / libevent / Boost.Asio / tokio karşılaştırması |
| 3 | `[[index]]` | Bu dizin | Klasör özeti, kaynak envanteri, komşu klasör bağlantıları |

**Okuma sırası:** `macos-core-mimari` → `cross-platform-api-soyutlama` → `[[../k003-cagri-thread/index]]`.

## 2. Alan (D01) Genel Bakış — Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` | 90 | 73 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` | 48 | 34 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 26 | 19 |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 41 | 31 |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 48 | 37 |
| 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 52 | 40 |
| 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 25 | 19 |
| 8 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 71 | 69 |
| 9 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 22 | 20 |
| 10 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 37 | 30 |


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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:11-36` — satır: 26 (boş olmayan: 19).

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:64-104` — satır: 41 (boş olmayan: 31).

### 3. Platform-Specific Detaylar

#### 3.1 Windows API (K0-01)

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:172-219` — satır: 48 (boş olmayan: 37).

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:220-271` — satır: 52 (boş olmayan: 40).

### 5. Threading (K0-06)

#### 5.1 Thread Öncelik Hiyerarşisi

| Thread | Öncelik | CPU Core | Kullanım |
|--------|---------|----------|----------|
| Audio Processing | TIME_CRITICAL (15) | Dedicated | ASIO callback |
| DSP Processing | HIGHEST (10) | Dedicated | EQ, reverb, compressor |
| Network I/O | ABOVE_NORMAL (8) | Shared | Streaming, DLNA |
| UI Rendering | NORMAL (0) | Shared | Arayüz |
| Background Tasks | BELOW_NORMAL (-1) | Shared | İndirme, indeksleme |
| Idle | LOWEST (-2) | Shared | Cleanup, garbage collection |

#### 5.2 Thread Güvenliği

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:272-296` — satır: 25 (boş olmayan: 19).

### 6. IPC Manager (K0-04)

#### 6.1 Platform-Specific IPC

| Platform | Yöntem | Kullanım |
|----------|--------|----------|
| Windows | Named Pipes, Shared Memory | Servis iletişimi |
| Linux | Unix Domain Sockets, POSIX SHM | Servis iletişimi |
| macOS | Mach Ports, POSIX SHM | Servis iletişimi |

#### 6.2 IPC Mesaj Formatı

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:402-472` — satır: 71 (boş olmayan: 69).

#### K0.1 — Platform Çekirdekleri

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:581-602` — satır: 22 (boş olmayan: 20).

#### K0.7 — Konteyner Runtime

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
## macOS Core
### Genel Bakış
### Teknik Detaylar
#### 1. Grand Central Dispatch (GCD)
#### 2. XPC Communication
#### 3. Core Foundation
#### 4. Metal GPU Hesaplama
#### 5. IOKit - Donanım Erişimi
#### 6. LaunchAgent / LaunchDaemon
#### 7. App Sandbox
#### 8. Hardened Runtime
### API / Arayüz
#### COREMUSIC macOS API Başlık Dosyası
### Bağımlılıklar
#### Gereksinimler
#### Alt Katmanlar
#### Üst Katmanlar
### Performans Metrikleri
### Güvenlik Notları
### Durum: Implementasyon
#### 3.3 macOS API (K0-03)
#### K0.6 — Cross-Platform API
## 3. Kenar Durumları
## 4. Hata Modları
## 5. Bağımlılıklar
## 6. Persona
## 7. Kanıt Kataloğu
## 8. Doğrulama Durumu

## 1. Kapsam
## 2. Gömülü Kaynaklar
## Cross-Platform API
### Genel Bakış
### Teknik Detaylar
#### 1. POSIX Threads (pthreads)
#### 2. SDL2 - Simple DirectMedia Layer
#### 3. libuv - Asenkron I/O
#### 4. libevent
#### 5. Boost.Asio (C++)
#### 6. Rust tokio
### API / Arayüz
#### COREMUSIC Cross-Platform API Başlık Dosyası
### Bağımlılıklar
#### Gereksinimler
#### Alt Katmanlar
#### Üst Katmanlar
### Performans Metrikleri
### Durum: Implementasyon
#### K0.6 — Cross-Platform API
#### K0.7 — Konteyner Runtime
## 3. Seçenek Karşılaştırması (gömülü kaynaktan)
## 4. Kenar Durumları
## 5. Hata Modları
## 6. Bağımlılıklar
## 7. Karar Kriterleri (persona)
## 8. Kanıt Kataloğu
## 9. Doğrulama Durumu

## 4. Komşu Klasörler (D01 içinde wiki-link)

| K | Klasör | Dosyalar (wiki-link) | Amaç |
|---|--------|----------------------|------|
| `K000` | k000-windows-core | [[../k000-windows-core/windows-core-mimari]] · [[../k000-windows-core/windows-api-yuzeyi]] · [[../k000-windows-core/index]] | Windows cekirdek katmani: NT mimarisi, Win32/WinRT API yuzeyi |
| `K001` | k001-linux-rpi5 | [[../k001-linux-rpi5/linux-cekirdek-mimari]] · [[../k001-linux-rpi5/rpi5-gomulu-platform]] · [[../k001-linux-rpi5/index]] | Linux cekirdek + Raspberry Pi 5 gomulu platform |
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
| [[macos-core-mimari]] | K002 — macOS Çekirdek Mimarisi (XNU / Darwin) | 541 |
| [[cross-platform-api-soyutlama]] | K002 — Çapraz Platform API Soyutlaması | 635 |
| [[index]] | K002 — macOS / Taşınabilirlik Klasör Dizini | 510 |

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
| macOS ↔ Windows | yatay | `[[../k000-windows-core/index]]` | platform çekirdeği karşılaştırması |
| macOS ↔ Linux | yatay | `[[../k001-linux-rpi5/index]]` | POSIX tabanı ortak |
| Taşınabilirlik → çağrı/thread | aşağı | `[[../k003-cagri-thread/index]]` | soyutlama katmanının altı |
| Taşınabilirlik → süreç/IPC | aşağı | `[[../k004-surec-ipc/index]]` | olay döngüsü / izolasyon |
| macOS → sürücü | aşağı | `[[../k015-platform-suruculeri/index]]` | CoreAudio yüzeyi |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/index.md` ve `README.md` §10-§11.

## 8. Kanıt ve Doğrulama

1. `Kanıt:` tüm içerik gömülü salt-okunur kaynaklardan gelir (SRCTABLE §2).
2. `⚠️ VERIFICATION REQUIRED` — seçilen kütüphane/SDK sürümleri (disk kanıtında yok).
3. `⚠️ VERIFICATION REQUIRED` — macOS donanımında ölçülmüş gecikme değerleri.
4. Dosya adları değiştirilmemiştir; ADR numaraları değiştirilmemiştir.

---
*Bu dizin `k002-macos-tasinabilirlik/` klasörünün SSOT özetidir.*
