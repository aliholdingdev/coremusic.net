---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K0 Windows API Detail"
type: architecture-detail
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

# K0: Windows API Detail

**Platform:** Windows (XP-11, Server 2012 R2+)
**Ana Dil:** C++20, MSVC 17.0+
**Sorumlu Agent:** Windows Software Engineer

---

## 1. ASIO SDK Entegrasyonu

### 1.1 ASIO 2.3 SDK Yapısı

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

### 1.2 ASIO Callback Protokolü

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

### 1.3 ASIO Buffer Konfigürasyonu

| Parametre | Varsayılan | Min | Max |
|-----------|-----------|-----|-----|
| Buffer Size | 512 samples | 64 | 1024 |
| Sample Rate | 48000 Hz | 44100 | 192000 |
| Bit Depth | 32-bit float | 16 | 32 |
| Channels | 8 (8.1) | 1 | 16 |
| Latency | ~10.67ms | ~1.33ms | ~21.33ms |

---

## 2. WASAPI Entegrasyonu

### 2.1 WASAPI Modları

| Mod | Gecikme | Kullanım |
|-----|---------|----------|
| Shared Mode | ~15ms | Genel kullanım |
| Exclusive Mode | ~3ms | Low-latency playback |
| Loopback | ~15ms | Ses kaydı |

### 2.2 WASAPI Akışı

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

## 3. Windows Threading

### 3.1 Thread Oluşturma

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

### 3.2 Senkronizasyon Primitifleri

| Primitif | Kullanım | Audio Thread'de |
|----------|---------|-----------------|
| Critical Section | Mutex | ❌ YASAK |
| SRW Lock | Lightweight mutex | ❌ YASAK |
| Event | Olay beklemesi | ⚠️ Sınırlı |
| Semaphore | Sayaçlı senkronizasyon | ⚠️ Sınırlı |
| Interlocked* | Atomik işlemler | ✅ İzinli |
| atomic<> | Lock-free | ✅ İzinli |

---

## 4. Bellek Yönetimi

### 4.1 Large Page Allocation

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

### 4.2 Memory-Mapped Files

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

## 5. COM Initialization

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

## 6. Windows Specific ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K0 Windows API Detail v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
