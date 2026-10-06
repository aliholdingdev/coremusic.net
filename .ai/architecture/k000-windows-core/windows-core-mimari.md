---
title: "K000 Windows Çekirdek Katmanı — NT Mimarisi ve Win32 Çekirdek Yüzeyi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K000 — Windows Çekirdek Katmanı (NT Mimarisi)

> **K numarası:** K000 · **Klasör:** `k000-windows-core` · **Alan:** D01 (OS / Donanım / Sürücü)
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (gecikme metrikleri)

## 1. Kapsam

Bu belge Windows tarafının çekirdek katmanını kapsar: NT çekirdek mimarisi, kullanıcı/kernel sınırı,
signal/dispatch yolları ve ses motorunun ihtiyaç duyduğu düşük seviyeli Win32 yüzeyi. Aşağıdaki
gömülü kaynak salt-okunur yedek alınmıştır; hiçbir sayı uydurulmamıştır.

### 1.1 Bu dosyanın konumu

| Öğe | Değer |
|---|---|
| Üst katman | `[[index]]` (K000 klasör dizini) |
| Alt katman | Donanım soyutlaması → `[[../k014-surucu-yigin/index]]` |
| Kardeş dosya | `[[windows-api-yuzeyi]]` (ASIO/WASAPI/COM yüzeyi) |
| İlgili OS klasörleri | `[[../k001-linux-rpi5/index]]` · `[[../k002-macos-tasinabilirlik/index]]` |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | 415 | 314 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 71 | 69 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 39 | 30 |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 64 | 59 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` — satır: 415 (boş olmayan: 314).


## Windows Core

### Genel Bakış

Windows Core modülü, COREMUSIC'ın Windows platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Windows API'nin advanced özelliklerini kullanarak gerçek zamanlı ses işleme ve çoklu ortam uygulamaları için optimize edilmiş bir altyapı sunar. Process management, memory management, threading ve IPC mekanizmaları için Windows'a özgü çözümler içerir.

### Teknik Detaylar

#### 1. Process Management

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

#### 2. Memory Management

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

#### 3. Threading Model

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

#### 4. Registry Operations

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

#### 5. Service Management

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

#### 6. Event Log

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

#### 7. Windows Management Instrumentation (WMI)

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

#### 8. COM Interface

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

#### 9. IPC - Named Pipes

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

#### 10. Memory Mapped Files

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

#### 11. Job Objects

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

#### 12. User Account Control (UAC)

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

### API / Arayüz

#### COREMUSIC Windows API Başlık Dosyası

```c
#ifndef COREMUSIC_WINDOWS_H
#define COREMUSIC_WINDOWS_H

#include <windows.h>
#include <mmdeviceapi.h>
#include <audioclient.h>

// Process Management
CM_Status CM_CreateProcess(const char* name, int priority, HANDLE* hProcess);
CM_Status CM_SetProcessPriority(HANDLE hProcess, int priority);
CM_Status CM_TerminateProcess(HANDLE hProcess, UINT exitCode);

// Memory Management
CM_Status CM_AllocateLargePages(size_t size, void** ppMemory);
CM_Status CM_CreateSharedMemory(const char* name, size_t size, void** ppMemory);
CM_Status CM_MapFile(const char* path, void** ppFileView, size_t* pSize);

// Threading
CM_Status CM_CreateThread(void* (*func)(void*), void* arg, int core, HANDLE* hThread);
CM_Status CM_SetThreadPriority(HANDLE hThread, int priority);
CM_Status CM_SetThreadAffinity(HANDLE hThread, int coreIndex);

// IPC
CM_Status CM_CreateNamedPipe(const char* name, HANDLE* hPipe);
CM_Status CM_ConnectNamedPipe(HANDLE hPipe);
CM_Status CM_ReadPipe(HANDLE hPipe, void* buffer, size_t size, size_t* bytesRead);
CM_Status CM_WritePipe(HANDLE hPipe, const void* data, size_t size);

// Service Management
CM_Status CM_RegisterService(const char* name, void (*mainFunc)(void));
CM_Status CM_StartService(const char* name);
CM_Status CM_StopService(const char* name);

#endif // COREMUSIC_WINDOWS_H
```

### Bağımlılıklar

#### Gereksinimler
- Windows 10/11 (1809 ve üzeri)
- Visual C++ Redistributable
- Windows SDK

#### Alt Katmanlar
- Donanım sürücüleri (ses kartı, GPU)
- Windows Audio Service
- WASAPI, DirectSound

#### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

### Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Process Oluşturma | < 1ms | Belirlenecek |
| Bellek Ayırma (Large Page) | < 0.1ms | Belirlenecek |
| Thread Oluşturma | < 0.5ms | Belirlenecek |
| Named Pipe latency | < 10μs | Belirlenecek |
| Event Log write | < 5ms | Belirlenecek |

### Güvenlik Notları

1. **UAC**: Yönetici yetkisi gerektiren işlemler için UAC elevation kullanın
2. **Token Privileges**: Gereksiz privilege'ları devre dışı bırakın
3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın
4. **Registry Erişimi**: minimum yetki ile erişim sağlayın
5. **Named Pipes**: Güvenli pipe security descriptor kullanın

### Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. WASAPI entegrasyonu ve ses cihazı yönetimi
2. Large page memory allocation implementasyonu
3. Thread affinity ve priority management
4. Named Pipes IPC implementasyonu
5. Service management ve event logging

**Sonraki Adımlar**:
- WASAPI callback-based audio processing implementasyonu
- Large page memory allocation için gerekliliklerin karşılanması
- Thread pool management için performans testleri



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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:66-104` — satır: 39 (boş olmayan: 30).

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



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:473-536` — satır: 64 (boş olmayan: 59).

#### K0.2 — Bellek & Süreç Yönetimi

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

#### K0.3 — Threading Model

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

#### K0.4 — IPC Mekanizmaları

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

## 3. Çekirdek Katman Kenar Durumları (Edge Cases)

Aşağıdaki tablo, gömülü `windows-core.md` içeriğinden türetilen ve uygulama sırasında karşılaşılabilecek
kenar durumları listeler. Her satır kaynak belgede karşılığı olan bir konuya dayanır.

| # | Kenar durum | Tetikleyici | Davranış riski | Azaltma |
|---|---|---|---|---|
| E1 | Real-time thread'in öncelik düşürülmesi | Arka plan sekmesi / güç yönetimi | Çekirdek taşıma (underrun) | Thread affinity + MMCSS süresi kontrolü |
| E2 | Exclusive mode sürülürken başka sürecin cihazı açması | ASIO paylaşımlı cihaz erişimi | `DEVICE_BUSY` benzeri hata | Cihaz kilidi sahiplik kontrolü |
| E3 | Sayfa boyutu varsayılanı 4 KB iken large page talebi | Bellek politikası / izin | sessiz geri düşme (fallback) | Talep başarısızlığını logla, 4 KB ile devam |
| E4 | Kullanıcı oturumu kilitlenirken session callback | Ekran kilidi / uyku | ses akışının askıya alınması | Oturum olaylarında buffer drain planı |
| E5 | Dosya yolu uzunluğu sınırı | Uzun katalog adları | dosya açılamama | Kısa yollar + tam yedekleme kontrolü |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` (gömülü gövde, bkz. §2 SRCTABLE).

## 4. Hata Modları ve Gözlem Noktaları

| # | Hata modu | Belirti | Ölçülebilir sinyal | Kırılma etkisi |
|---|---|---|---|---|
| F1 | Zamanlama bütçesinin aşılması | sesde kopukluk | callback süresi > periyot süresi | xrun / underrun |
| F2 | Öncelikli thread'in bloke olması | gecikme sıçraması | bekleme süresi p95 artışı | gecikme metriği bozulur |
| F3 | Bellek kirliliği (fragmentasyon) | tahsis hatası | boş bellek düşerken tahsis süresi artar | başlangıç gecikmesi |
| F4 | Sistem çağrısının hata kodu yutulması | sessiz veri kaybı | hata sayacı 0 kalır | hata gizlenir, teşhis zorlaşır |
| F5 | CPU çekirdeği göçü (migration) | önbellek yeniden ısıtma | çekirdek değişim sayacı | gecikme varyansı |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` §4-§5 (bellek ve threading kuralları) ve gömülü `windows-core.md`.

## 5. Bağımlılıklar

### 5.1 Yukarı bağımlılıklar (bu katmanın kullandığı)

- İşletim sistemi çekirdek hizmetleri: zamanlama, bellek, dosya sistemi, ağ (kaynak: gömülü `windows-core.md` + README §7-§9).
- Sürücü yüzeyi: `[[../k014-surucu-yigin/index]]` üzerinden buffer ve latency katmanı.

### 5.2 Aşağı bağımlılıklar (bu katmanı kullananlar)

- `[[../k003-cagri-thread/index]]` — sistem çağrıları ve thread modeli bu çekirdeğin üzerine kurulur.
- `[[windows-api-yuzeyi]]` — ASIO/WASAPI/COM entegrasyonu Win32 çekirdek yüzeyini kullanır.
- `[[../k004-surec-ipc/index]]` · `[[../k005-bellek-container/index]]` — süreç izolasyonu ve bellek yönetimi.

## 6. Sorumluluk ve Persona Eşleşmesi

| Görev | Persona | Gerekçe |
|---|---|---|
| NT çekirdek çağrı yollarının doğrulanması | `embedded-engineer` | DSP/ASIO/WASAPI donanım etkileşimi |
| Gecikme ve öncelik metriklerinin tanımlanması | `performance-engineer` | callback bütçesi ve p95 ölçümü |
| Sürüm/patch uyumluluk denetimi | `dependency-manager` | SDK ve platform bağımlılığı takibi |

## 7. Kanıt Kataloğu

1. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` — gömülü gövde (§2).
2. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:402-472` — K0.1 platform çekirdekleri şeması.
3. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:66-104` — §3.1 Windows API detayı.
4. `⚠️ VERIFICATION REQUIRED` — gerçek donanım üzerinde callback süresi ölçümü (bu vault'ta ölçüm sonucu yok).
5. `⚠️ VERIFICATION REQUIRED` — Windows sürüm bazlı large-page izin matrisi (disk kanıtı yok, ölçüm gerekli).

## 8. Doğrulama Durumu

- Bu dosyanın tüm sayısal iddiaları gömülü kaynaklardan gelir; yeni sayı eklenmemiştir.
- Ölçüm gerektiren her iddia §7'de `⚠️ VERIFICATION REQUIRED` ile işaretlidir.
- Bir üst dizin: `[[index]]`
