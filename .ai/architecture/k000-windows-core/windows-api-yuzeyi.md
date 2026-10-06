---
title: "K000 Windows API Yüzeyi — ASIO SDK, WASAPI Akışı ve COM Başlatımı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K000 — Windows API Yüzeyi (ASIO / WASAPI / COM)

> **K numarası:** K000 · **Klasör:** `k000-windows-core` · **Dosya:** `windows-api-yuzeyi`
> **Sorumlu persona:** `embedded-engineer` · **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (ADR numarası disk kanıtında yok)

## 1. Kapsam ve Bağlam

Windows API yüzeyi, ses motorunun doğrudan temas ettiği üç katmanı tanımlar: ASIO SDK callback
protokolü, WASAPI akış modları ve COM başlatma kuralları. Bu belge, çekirdek mimariyi anlatan
`[[windows-core-mimari]]` dosyasının tamamlayıcısıdır ve sürücü yığınıyla
`[[../k014-surucu-yigin/index]]` üzerinden sınır oluşturur.

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Kardeş (çekirdek) | `[[windows-core-mimari]]` |
| Klasör dizini | `[[index]]` |
| Windows altındaki sürücü katmanı | `[[../k014-surucu-yigin/index]]` |
| Platform sürücüleri | `[[../k015-platform-suruculeri/index]]` |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | 251 | 198 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 77 | 71 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` | 48 | 34 |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 27 | 24 |
| 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 25 | 19 |
| 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 46 | 33 |
| 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 14 | 11 |
| 8 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 43 | 39 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` — satır: 251 (boş olmayan: 198).


## K0: Windows API Detail

**Platform:** Windows (XP-11, Server 2012 R2+)
**Ana Dil:** C++20, MSVC 17.0+
**Sorumlu Agent:** Windows Software Engineer

---

### 1. ASIO SDK Entegrasyonu

#### 1.1 ASIO 2.3 SDK Yapısı

```
ASIOSDK2/
├── common/
│   ├── asio.h                 # ASIO C definition
│   ├── asiodrivers.h          # Driver management
│   ├── asiolist.h             # Driver enumeration
│   ├── ASIOConvertSamples.h   # Sample conversion
│   └── asio.cpp               # Host interface
├── host/
│   ├── asiodrivers.h/cpp      # Driver instantiation
│   └── ASIOConvertSamples.h/cpp
├── host/pc/
│   ├── asiolist.h/cpp         # COM-based driver list
│   └── ginclude.h             # Platform definitions
├── driver/asiosample/
│   ├── asiosmpl.h/cpp         # Sample driver
│   └── wintimer.cpp           # Buffer switch timer
└── host/sample/
    └── hostsample.cpp         # Host application example
```

#### 1.2 ASIO Callback Protokolü

```cpp
// ASIO Callback Structure
class ASIOCallbacks {
public:
    // Buffer switch — ana audio callback
    virtual void bufferSwitch(
        long doubleBufferIndex,
        ASIOBool directProcess
    ) = 0;

    // Sample rate değişikliği
    virtual void sampleRateDidChange(
        ASIOSampleRate sRate
    ) = 0;

    // Buffer swap talebi
    virtual void bufferRequest(
        ASIOBufferInfo* info,
        int numChannels,
        long bufferSize
    ) = 0;

    // Mesaj iletimi
    virtual ASIOBool ioSamplesNeeded() = 0;
};
```

#### 1.3 ASIO Buffer Konfigürasyonu

| Parametre | Varsayılan | Min | Max |
|-----------|-----------|-----|-----|
| Buffer Size | 512 samples | 64 | 1024 |
| Sample Rate | 48000 Hz | 44100 | 192000 |
| Bit Depth | 32-bit float | 16 | 32 |
| Channels | 8 (8.1) | 1 | 16 |
| Latency | ~10.67ms | ~1.33ms | ~21.33ms |

---

### 2. WASAPI Entegrasyonu

#### 2.1 WASAPI Modları

| Mod | Gecikme | Kullanım |
|-----|---------|----------|
| Shared Mode | ~15ms | Genel kullanım |
| Exclusive Mode | ~3ms | Low-latency playback |
| Loopback | ~15ms | Ses kaydı |

#### 2.2 WASAPI Akışı

```cpp
// WASAPI Audio Client
IMMDeviceEnumerator* pEnumerator = nullptr;
IMMDevice* pDevice = nullptr;
IAudioClient* pAudioClient = nullptr;
IAudioRenderClient* pRenderClient = nullptr;

// Initialize
hr = CoCreateInstance(
    __uuidof(MMDeviceEnumerator),
    NULL, CLSCTX_ALL,
    __uuidof(IMMDeviceEnumerator),
    (void**)&pEnumerator
);

hr = pEnumerator->GetDefaultAudioEndpoint(
    eRender, eConsole, &pDevice
);

hr = pDevice->Activate(
    __uuidof(IAudioClient),
    CLSCTX_ALL, NULL,
    (void**)&pAudioClient
);

// Initialize with desired format
WAVEFORMATEX* pwfx = nullptr;
hr = pAudioClient->GetMixFormat(&pwfx);

hr = pAudioClient->Initialize(
    AUDCLNT_SHAREMODE_EXCLUSIVE,  // or AUDCLNT_SHAREMODE_SHARED
    AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
    10 * 1000 * 10,  // 100ms buffer
    0,
    pwfx,
    NULL
);

// Get buffer
hr = pAudioClient->GetService(
    __uuidof(IAudioRenderClient),
    (void**)&pRenderClient
);
```

---

### 3. Windows Threading

#### 3.1 Thread Oluşturma

```cpp
// Real-time audio thread
HANDLE hThread = CreateThread(
    NULL,                           // Default security
    0,                              // Default stack size
    AudioThreadProc,               // Thread function
    lpParam,                        // Parameter
    0,                              // Run immediately
    &dwThreadId                     // Thread ID
);

// Set real-time priority
SetThreadPriority(hThread, THREAD_PRIORITY_TIME_CRITICAL);

// Set CPU affinity (dedicated core)
SetThreadAffinityMask(hThread, 1 << audioCoreIndex);

// Set thread name (for debugging)
SetThreadDescription(hThread, L"Audio Processing Thread");
```

#### 3.2 Senkronizasyon Primitifleri

| Primitif | Kullanım | Audio Thread'de |
|----------|---------|-----------------|
| Critical Section | Mutex | ❌ YASAK |
| SRW Lock | Lightweight mutex | ❌ YASAK |
| Event | Olay beklemesi | ⚠️ Sınırlı |
| Semaphore | Sayaçlı senkronizasyon | ⚠️ Sınırlı |
| Interlocked* | Atomik işlemler | ✅ İzinli |
| atomic<> | Lock-free | ✅ İzinli |

---

### 4. Bellek Yönetimi

#### 4.1 Large Page Allocation

```cpp
// Large page allocation (2MB pages)
SIZE_T largePageSize = GetLargePageMinimum();
LPVOID pMem = VirtualAlloc(
    NULL,
    largePageSize,
    MEM_COMMIT | MEM_RESERVE | MEM_LARGE_PAGES,
    PAGE_READWRITE
);

// Free
VirtualFree(pMem, 0, MEM_RELEASE);
```

#### 4.2 Memory-Mapped Files

```cpp
// Memory-mapped file for large audio files
HANDLE hFile = CreateFile(
    audioFilePath,
    GENERIC_READ,
    FILE_SHARE_READ,
    NULL,
    OPEN_EXISTING,
    FILE_ATTRIBUTE_NORMAL,
    NULL
);

HANDLE hMapping = CreateFileMapping(
    hFile,
    NULL,
    PAGE_READONLY,
    0, 0,
    NULL
);

LPVOID pView = MapViewOfFile(
    hMapping,
    FILE_MAP_READ,
    0, 0,
    0
);
```

---

### 5. COM Initialization

```cpp
// COM initialization for WASAPI
HRESULT hr = CoInitializeEx(
    NULL,
    COINIT_MULTITHREADED | COINIT_DISABLE_OLE1DDE
);

// Uninitialize
CoUninitialize();
```

---

### 6. Windows Specific ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K0 Windows API Detail v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team · Human Mode · Truth Mode*



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:603-679` — satır: 77 (boş olmayan: 71).

#### K0.8 — Windows API

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

#### K0.9 — Dosya Sistemi / Ağ / Zaman

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

#### K0.10 — Yönetişim & Bileşen Haritası

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:343-356` — satır: 14 (boş olmayan: 11).

#### 9.2 ASIO Zamanlama

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:538-580` — satır: 43 (boş olmayan: 39).

#### K0.5 — Sistem Çağrıları

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

#### K0.6 — Cross-Platform API

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


## 3. API Yüzeyi Karşılaştırması

Gömülü `windows-api.md` içindeki iki ana akışın karşılaştırması:

| Ölçüt | ASIO yolu | WASAPI yolu |
|---|---|---|
| Sahiplik modeli | sürücü sahipli (exclusive) | oturum bazlı (shared / exclusive) |
| Zamanlama kaynağı | sürücü callback'i | oturum olayları |
| Buffer sahipliği | uygulama ↔ sürücü çift tampon | oturum tamponu |
| Eşzamanlılık riski | callback aynı anda tekil olmalı | akış durdurma/yeniden başlatma yarışı |
| Hata yüzeyi | callback hata kodları | oturum durum geçişleri |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` (§1 ASIO SDK, §2 WASAPI).

## 4. Kenar Durumları

| # | Kenar durum | Koşul | Sonuç | Yaklaşım |
|---|---|---|---|---|
| E1 | COM birimi birden fazla thread'de başlatılıyor | arayüz thread'i + worker thread | beklenmedik marshalling hatası | her thread'de tek initialize, çift kapatma |
| E2 | Buffer çifti yeniden boyutlandırılıyor | kullanıcı örnekleme oranı değiştiriyor | eski tamponda veri kalması | tampon takası tam kapanıştan sonra |
| E3 | Exclusive modda paylaşımlı erişim | iki uygulama aynı cihazı açıyor | ikinci açılış reddedilir | sahiplik kontrolü + açık hata |
| E4 | Callback'te bloklayıcı çağrı | kilitli dosya/ ağ isteği | gecikme bütçesi aşılır | callback içinde yalnız tamponsuz iş |
| E5 | Sürücü kaldırma sırasında akış | sürücü güncellemesi | askıda kalan iş parçacığı | kapanış sırası: akış → tampon → sürücü |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` §1.2-§1.3, §2.2, §5.

## 5. Hata Modları

| # | Hata modu | Belirti | Sinyal | Sonuç |
|---|---|---|---|---|
| F1 | Callback zincirinin kırılması | ses kesilmesi | sayaç sıfırlanır | underrun |
| F2 | COM başlatma sırasının ihlali | arayüz bulunamama | hata kodu | işlevsellik kaybı |
| F3 | Tampon boyutu ile periyot uyuşmazlığı | taşma / eksik veri | buffer bayt sayacı | bozuk çıkış |
| F4 | Exclusive → shared sessiz geçiş | farklı gecikme | metrik sıçraması | gecikme regresyonu |
| F5 | Hata kodunun yutulması | hata sayacı sabit 0 | log eksik | teşhis edilemez kesinti |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` §9.2 (ASIO zamanlama) ve gömülü `windows-api.md` §6.

## 6. Bağımlılıklar

### 6.1 Kullandığı (yukarı)

- `[[windows-core-mimari]]` — NT çekirdek yüzeyi, bellek ve thread primitifleri.
- Gömülü `README.md:603-679` — K0.8 Windows API ve K0.9 dosya/ağ/zaman şemaları.

### 6.2 Kullanan (aşağı)

- `[[../k015-platform-suruculeri/index]]` — ASIO/WASAPI/CoreAudio sürücü implementasyonları.
- `[[../k014-surucu-yigin/index]]` — buffer yönetimi ve latency bütçesi.

## 7. Guardrail Özeti (gömülü CLAUDE.md)

Gömülü `k0-isletim-sistemi/CLAUDE.md` dosyasındaki hard guardrail'ler bu yüzey için de bağlayıcıdır:

1. Yasaklı örüntüler uygulanmaz (kaynak: gövde §3).
2. Teknoloji kısıtlamaları ihlal edilmez (kaynak: gövde §2).
3. ADR referansları değiştirilemez; bulunmayan ADR `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 8. Kanıt Kataloğu

1. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` — gömülü gövde.
2. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:603-679` — K0.8/K0.9 şemaları.
3. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/CLAUDE.md` — guardrail metni.
4. `⚠️ VERIFICATION REQUIRED` — gerçek sürücü kurulumunda ölçülmiş gecikme değerleri (vault'ta yok).
5. `⚠️ VERIFICATION REQUIRED` — Windows sürümüne göre WASAPI mod davranışı (disk kanıtı yok).

## 9. Doğrulama Durumu

- Yeni iddia üretilmedi; tüm tablolar gömülü kaynakların özetidir.
- Üst dizin: `[[index]]`
