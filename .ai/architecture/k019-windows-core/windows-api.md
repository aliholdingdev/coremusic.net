---
title: "Windows API Entegrasyonu - k019-windows-core"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Windows API Entegrasyonu

> Klasör: `k019-windows-core` · Dilim: D01 (k018–k035) · Dosya: `windows-api.md`
> Sorumlu persona: `windows-software-engineer` (Windows Software Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

ASIO SDK, WASAPI, Windows threading, bellek yönetimi ve COM initialization.

Bu belge; D01 diliminin (Windows Çekirdeği) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** ASIO SDK, WASAPI, Windows threading, bellek yönetimi ve COM initialization.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Windows Çekirdeği)
- **Çapraz referanslar:** [[../k033-platform-ses-suruculeri/asio-drivers.md]] · [[../k033-platform-ses-suruculeri/wasapi-exclusive.md]] · [[../k029-cross-platform-api/cross-platform-api.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### windows-api.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` (265 satır)

#### windows-api.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § (giriş) — L1–L24

---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K0 Windows API Detail"
type: architecture-detail
category: architecture
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

### K0: Windows API Detail

**Platform:** Windows (XP-11, Server 2012 R2+)
**Ana Dil:** C++20, MSVC 17.0+
**Sorumlu Agent:** Windows Software Engineer

---


#### 1. ASIO SDK Entegrasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `1. ASIO SDK Entegrasyonu` — L25–L89


##### 1.1 ASIO 2.3 SDK Yapısı

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

##### 1.2 ASIO Callback Protokolü

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

##### 1.3 ASIO Buffer Konfigürasyonu

| Parametre | Varsayılan | Min | Max |
|-----------|-----------|-----|-----|
| Buffer Size | 512 samples | 64 | 1024 |
| Sample Rate | 48000 Hz | 44100 | 192000 |
| Bit Depth | 32-bit float | 16 | 32 |
| Channels | 8 (8.1) | 1 | 16 |
| Latency | ~10.67ms | ~1.33ms | ~21.33ms |

---

#### 2. WASAPI Entegrasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `2. WASAPI Entegrasyonu` — L91–L148


##### 2.1 WASAPI Modları

| Mod | Gecikme | Kullanım |
|-----|---------|----------|
| Shared Mode | ~15ms | Genel kullanım |
| Exclusive Mode | ~3ms | Low-latency playback |
| Loopback | ~15ms | Ses kaydı |

##### 2.2 WASAPI Akışı

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

#### 3. Windows Threading

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `3. Windows Threading` — L150–L186


##### 3.1 Thread Oluşturma

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

##### 3.2 Senkronizasyon Primitifleri

| Primitif | Kullanım | Audio Thread'de |
|----------|---------|-----------------|
| Critical Section | Mutex | ❌ YASAK |
| SRW Lock | Lightweight mutex | ❌ YASAK |
| Event | Olay beklemesi | ⚠️ Sınırlı |
| Semaphore | Sayaçlı senkronizasyon | ⚠️ Sınırlı |
| Interlocked* | Atomik işlemler | ✅ İzinli |
| atomic<> | Lock-free | ✅ İzinli |

---

#### 4. Bellek Yönetimi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `4. Bellek Yönetimi` — L188–L236


##### 4.1 Large Page Allocation

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

##### 4.2 Memory-Mapped Files

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

#### 5. COM Initialization

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `5. COM Initialization` — L238–L251


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

#### 6. Windows Specific ADR'ler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` § `6. Windows Specific ADR'ler` — L253–L265


| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K0 Windows API Detail v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team · Human Mode · Truth Mode*

### windows-core.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` (422 satır)

#### windows-core.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § (giriş) — L1–L11

---
title: "Windows Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Windows"
date: 2026-09-20
version: 1.0.1
---

### Windows Core


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Teknik Detaylar` — L16–L330


##### 1. Process Management

Windows process management, COREMUSIC uygulamalarının lifecycle'ını kontrol eder:

```c
// Process oluşturma ve yapılandırma
HANDLE hProcess;
PROCESS_INFORMATION pi;
STARTUPINFO si = { sizeof(si) };

// Gerçek zamanlı öncelik ile process oluşturma
CreateProcess(
    NULL,
    "coremusic_worker.exe",
    NULL, NULL, FALSE,
    CREATE_SUSPENDED | REALTIME_PRIORITY_CLASS,
    NULL, NULL, &si, &pi
);

// Process'i başlatmadan önce bellek ayırtma
VirtualAllocEx(pi.hProcess, NULL, bufferSize,
    MEM_COMMIT | MEM_RESERVE, PAGE_READWRITE);

// Thread'i başlatma
ResumeThread(pi.hThread);
```

**Process Öncelik Sınıfları**:
- `REALTIME_PRIORITY_CLASS`: Ses işleme için
- `HIGH_PRIORITY_CLASS`: Kritik işlemler için
- `ABOVE_NORMAL_PRIORITY_CLASS`: Normal çoklu ortam için
- `NORMAL_PRIORITY_CLASS`: Arka plan işlemleri için

##### 2. Memory Management

Windows bellek yönetimi, büyük audio buffer'lar için optimize edilmiştir:

```c
// Large pages ile bellek ayırma (performans için)
HANDLE hToken;
OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES, &hToken);
// SeLockMemoryPrivilege'ı etkinleştir

LPVOID pBuffer = VirtualAlloc(
    NULL,
    LARGE_PAGE_SIZE,  // 2MB large page
    MEM_COMMIT | MEM_RESERVE | MEM_LARGE_PAGES,
    PAGE_READWRITE | PAGE_WRITECOMBINE
);

// Memory mapped files ile çoklu process arası veri paylaşımı
HANDLE hMapFile = CreateFileMapping(
    INVALID_HANDLE_VALUE, NULL,
    PAGE_READWRITE | SEC_COMMIT,
    0, sharedMemorySize,
    L"CoreMusicSharedMemory"
);

LPVOID pSharedMemory = MapViewOfFile(
    hMapFile, FILE_MAP_ALL_ACCESS,
    0, 0, sharedMemorySize
);
```

##### 3. Threading Model

Windows threading, ses işleme için gerçek zamanlı thread yönetimi sağlar:

```c
// Thread oluşturma ve CPU affinity ayarlama
DWORD WINAPI AudioProcessingThread(LPVOID lpParam) {
    // Thread'i belirli bir CPU çekirdeğine bağla
    DWORD_PTR affinityMask = 1 << threadIndex;
    SetThreadAffinityMask(GetCurrentThread(), affinityMask);

    // Thread önceliğini ayarla
    SetThreadPriority(GetCurrentThread(), THREAD_PRIORITY_TIME_CRITICAL);

    // Synchronized audio processing loop
    while (running) {
        WaitForSingleObject(hAudioEvent, INFINITE);
        ProcessAudioBuffer();
    }
    return 0;
}

// Thread pool kullanımı
PTP_POOL threadPool = CreateThreadPool(NULL);
SetThreadPoolThreadMinimum(threadPool, 4);
SetThreadPoolThreadMaximum(threadPool, 8);

// Callback ile thread pool'a iş ekleme
TrySubmitThreadpoolCallback(AudioCallback, &callbackData, NULL);
```

##### 4. Registry Operations

Windows Registry, uygulama yapılandırmasını ve ses cihazı ayarlarını saklar:

```c
// Registry'den ses cihazı ayarlarını okuma
HKEY hKey;
RegOpenKeyEx(HKEY_CURRENT_USER,
    L"SOFTWARE\\CoreMusic\\AudioDevices",
    0, KEY_READ, &hKey);

DWORD sampleRate;
DWORD bufferSize;
DWORD channels;

RegQueryValueEx(hKey, L"SampleRate", NULL, NULL,
    (LPBYTE)&sampleRate, sizeof(DWORD));
RegQueryValueEx(hKey, L"BufferSize", NULL, NULL,
    (LPBYTE)&bufferSize, sizeof(DWORD));
RegQueryValueEx(hKey, L"Channels", NULL, NULL,
    (LPBYTE)&channels, sizeof(DWORD));

RegCloseKey(hKey);
```

##### 5. Service Management

COREMUSIC servis yönetimi, arka plan ses işleme servisleri için:

```c
// Windows servisi oluşturma
SERVICE_TABLE_ENTRY serviceTable[] = {
    { L"CoreMusicService", (LPSERVICE_MAIN_FUNCTION)ServiceMain },
    { NULL, NULL }
};

StartServiceCtrlDispatcher(serviceTable);

// Servis durumunu güncelleme
SERVICE_STATUS serviceStatus = {
    SERVICE_WIN32_OWN_PROCESS,
    SERVICE_RUNNING,
    SERVICE_ACCEPT_STOP | SERVICE_ACCEPT_PAUSE_CONTINUE,
    NO_ERROR, 0, 0, 0
};

SetServiceStatus(hServiceStatus, &serviceStatus);
```

##### 6. Event Log

Windows Event Log, uygulama hata ve bilgi kayıtları için:

```c
// Event Log'a kayıt ekleme
HANDLE hEventLog = RegisterEventSource(NULL, L"CoreMusic");

const char* messages[] = {
    "Audio processing started successfully",
    "Buffer underrun detected",
    "Device disconnected"
};

ReportEvent(hEventLog, EVENTLOG_INFORMATION_TYPE, 0, 0,
    NULL, 1, 0, messages, NULL);

DeregisterEventSource(hEventLog);
```

##### 7. Windows Management Instrumentation (WMI)

WMI, sistem durumu izleme ve donanım bilgisi için:

```c
// WMI ile ses cihazı bilgilerini sorgulama
IWbemLocator *pLocator = NULL;
CoCreateInstance(CLSID_WbemLocator, NULL, CLSCTX_INPROC_SERVER,
    IID_IWbemLocator, (void**)&pLocator);

IWbemServices *pServices = NULL;
pLocator->ConnectServer(
    BSTR(L"ROOT\\CIMV2"), NULL, NULL, NULL,
    0, NULL, NULL, &pServices);

// Sorgu oluştur ve çalıştır
IEnumWbemClassObject *pEnumerator = NULL;
pServices->ExecQuery(
    BSTR(L"WQL"),
    BSTR(L"SELECT * FROM Win32_SoundDevice"),
    WBEM_FLAG_FORWARD_ONLY, NULL, &pEnumerator);
```

##### 8. COM Interface

Windows COM, ses eklentileri ve bileşen entegrasyonu için:

```c
// COM initialization
CoInitializeEx(NULL, COINIT_MULTITHREADED);

// WASAPI (Windows Audio Session API) kullanımı
IAudioClient *pAudioClient;
IMMDeviceEnumerator *pEnumerator;

// Ses cihazını başlatma
pAudioClient->Initialize(
    AUDCLNT_SHAREMODE_EXCLUSIVE,
    AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
    bufferDuration, 0, &format, NULL);

// Event callback ile senkron ses işleme
HANDLE hEvent = CreateEvent(NULL, FALSE, FALSE, NULL);
pAudioClient->SetEventHandle(hEvent);

// Audio loop
while (running) {
    WaitForSingleObject(hEvent, INFINITE);
    pAudioClient->GetCurrentPadding(&padding);
    if (padding < bufferFrameCount) {
        ProcessAudioData();
    }
}
```

##### 9. IPC - Named Pipes

Windows Named Pipes, process arası iletişim için yüksek performanslı çözüm:

```c
// Named Pipe server oluşturma
HANDLE hPipe = CreateNamedPipe(
    L"\\\\.\\pipe\\CoreMusicIPC",
    PIPE_ACCESS_DUPLEX | FILE_FLAG_OVERLAPPED,
    PIPE_TYPE_MESSAGE | PIPE_READMODE_MESSAGE | PIPE_WAIT,
    PIPE_UNLIMITED_INSTANCES,
    bufferSize, bufferSize, 0, NULL);

// Asenkron okuma/yazma için OVERLAPPED yapılandırma
OVERLAPPED overlapped = { 0 };
overlapped.hEvent = CreateEvent(NULL, TRUE, FALSE, NULL);

ReadFile(hPipe, buffer, bufferSize, NULL, &overlapped);
WriteFile(hPipe, response, responseSize, NULL, &overlapped);
```

##### 10. Memory Mapped Files

Büyük ses dosyaları için memory mapped files kullanımı:

```c
// Büyük ses dosyasını memory mapped olarak açma
HANDLE hFile = CreateFile(
    L"C:\\Music\\large_audio.wav",
    GENERIC_READ, FILE_SHARE_READ, NULL,
    OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);

HANDLE hMapping = CreateFileMapping(
    hFile, NULL, PAGE_READONLY,
    0, 0, NULL);

LPVOID pFileView = MapViewOfFile(
    hMapping, FILE_MAP_READ,
    0, 0, 0);

// Doğrudan bellek erişimi ile ses verisini işleme
AudioData* audioData = (AudioData*)pFileView;
ProcessAudioDataDirect(audioData);
```

##### 11. Job Objects

Windows Job Objects, kaynak tüketimi kontrolü ve process grup yönetimi:

```c
// Job object oluşturma
HANDLE hJob = CreateJobObject(NULL, L"CoreMusicJob");

// CPU sınırlaması ekleme
JOBOBJECT_CPU_RATE_CONTROL cpuRate = { 0 };
cpuRate.CpuRate = 50;  // %50 CPU kullanımı
SetInformationJobObject(hJob, JobObjectCpuRateControlInformation,
    &cpuRate, sizeof(cpuRate));

// Memory sınırlaması ekleme
JOBOBJECT_EXTENDED_LIMIT_INFORMATION limitInfo = { 0 };
limitInfo.ProcessMemoryLimit = 1024 * 1024 * 512;  // 512MB
SetInformationJobObject(hJob, JobObjectExtendedLimitInformation,
    &limitInfo, sizeof(limitInfo));

// Process'i job'a atama
AssignProcessToJobObject(hJob, pi.hProcess);
```

##### 12. User Account Control (UAC)

UAC, yetkili işlemler için güvenli privilege escalation:

```c
// UAC elevation ile yönetici yetkileri
SHELLEXECUTEINFO sei = { sizeof(sei) };
sei.lpVerb = L"runas";
sei.lpFile = L"coremusic_admin.exe";
sei.lpParameters = L"--install-driver";
sei.nShow = SW_SHOWNORMAL;

ShellExecuteEx(&sei);

// Token manipulation ile privilege escalation
HANDLE hToken;
OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES | TOKEN_QUERY, &hToken);

TOKEN_PRIVILEGES tp;
LookupPrivilegeValue(NULL, SE_DEBUG_NAME, &tp.Privileges[0].Luid);
tp.PrivilegeCount = 1;
tp.Privileges[0].Attributes = SE_PRIVILEGE_ENABLED;

AdjustTokenPrivileges(hToken, FALSE, &tp, sizeof(tp), NULL, NULL);
```

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Güvenlik Notları` — L400–L406


1. **UAC**: Yönetici yetkisi gerektiren işlemler için UAC elevation kullanın
2. **Token Privileges**: Gereksiz privilege'ları devre dışı bırakın
3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın
4. **Registry Erişimi**: minimum yetki ile erişim sağlayın
5. **Named Pipes**: Güvenli pipe security descriptor kullanın

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - Windows 10/11 (1809 ve üzeri) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L376 |
| - Visual C++ Redistributable | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L377 |
| - Windows SDK | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L378 |
| - Donanım sürücüleri (ses kartı, GPU) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L381 |
| - Windows Audio Service | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L382 |
| - WASAPI, DirectSound | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L383 |
| - Cross-Platform API soyutlama katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L386 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L387 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L388 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| "Buffer underrun detected", | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L172 |
| "Device disconnected" | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L173 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| hr = pEnumerator->GetDefaultAudioEndpoint( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L118 |
| NULL,                           // Default security | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L157 |
| 0,                              // Default stack size | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L158 |
| NO_ERROR, 0, 0, 0 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L156 |
| Windows Event Log, uygulama hata ve bilgi kayıtları için: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L164 |
| 3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L404 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| │   └── wintimer.cpp           # Buffer switch timer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L45 |
| // Buffer switch — ana audio callback | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L56 |
| virtual void bufferSwitch( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L57 |
| long doubleBufferIndex, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L58 |
| // Buffer swap talebi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L67 |
| virtual void bufferRequest( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L68 |
| ASIOBufferInfo* info, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L69 |
| long bufferSize | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L71 |
| ### 1.3 ASIO Buffer Konfigürasyonu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L79 |
| 10 * 1000 * 10,  // 100ms buffer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L135 |
| // Get buffer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L141 |
| // Set CPU affinity (dedicated core) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L168 |
| VirtualAllocEx(pi.hProcess, NULL, bufferSize, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L38 |
| Windows bellek yönetimi, büyük audio buffer'lar için optimize edilmiştir: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L53 |
| LPVOID pBuffer = VirtualAlloc( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L61 |
| // Thread oluşturma ve CPU affinity ayarlama | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L87 |
| // Thread'i belirli bir CPU çekirdeğine bağla | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L89 |
| ProcessAudioBuffer(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L99 |
| DWORD bufferSize; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L125 |
| RegQueryValueEx(hKey, L"BufferSize", NULL, NULL, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L130 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| HANDLE hToken; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L57 |
| OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES, &hToken); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L58 |
| // SeLockMemoryPrivilege'ı etkinleştir | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L59 |
| UAC, yetkili işlemler için güvenli privilege escalation: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L308 |
| // UAC elevation ile yönetici yetkileri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L311 |
| // Token manipulation ile privilege escalation | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L320 |
| HANDLE hToken; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L321 |
| OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES \| TOKEN_QUERY, &hToken); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L322 |
| TOKEN_PRIVILEGES tp; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L324 |
| LookupPrivilegeValue(NULL, SE_DEBUG_NAME, &tp.Privileges[0].Luid); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L325 |
| tp.PrivilegeCount = 1; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L326 |
| tp.Privileges[0].Attributes = SE_PRIVILEGE_ENABLED; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L327 |
| AdjustTokenPrivileges(hToken, FALSE, &tp, sizeof(tp), NULL, NULL); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L329 |
| ## Güvenlik Notları | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L400 |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - Thread pool management için performans testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L422 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **5** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

| Belirsizlik / risk (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |

## Identifier Envanteri

> Identifier'lar kaynak metinden sayım ile üretilmiştir; ilk geçtiği satır kanıt olarak verilmiştir.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `ASIO` | 21 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L25 |
| `WASAPI` | 9 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L91 |
| `COM` | 12 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L31 |
| `latency` | 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L87 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | (başlık + giriş) | L1–L24 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 1. ASIO SDK Entegrasyonu | L25–L89 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 2. WASAPI Entegrasyonu | L91–L148 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 3. Windows Threading | L150–L186 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 4. Bellek Yönetimi | L188–L236 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 5. COM Initialization | L238–L251 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 6. Windows Specific ADR'ler | L253–L265 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Teknik Detaylar | L16–L330 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## API / Arayüz | L332–L371 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Bağımlılıklar | L373–L388 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Performans Metrikleri | L390–L398 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Güvenlik Notları | L400–L406 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Durum: Implementasyon | L408–L422 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Durum: Implementasyon` — L408–L422


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. WASAPI entegrasyonu ve ses cihazı yönetimi
2. Large page memory allocation implementasyonu
3. Thread affinity ve priority management
4. Named Pipes IPC implementasyonu
5. Service management ve event logging

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
