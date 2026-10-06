---
title: "Windows Çekirdeği - k019-windows-core"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Windows Çekirdeği

> Klasör: `k019-windows-core` · Dilim: D01 (k018–k035) · Dosya: `windows-core.md`
> Sorumlu persona: `windows-software-engineer` (Windows Software Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Windows çekirdek servisleri, mimari konum ve bağımlılıklar.

Bu belge; D01 diliminin (Windows Çekirdeği) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Windows çekirdek servisleri, mimari konum ve bağımlılıklar.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Windows Çekirdeği)
- **Çapraz referanslar:** [[../k033-platform-ses-suruculeri/asio-drivers.md]] · [[../k033-platform-ses-suruculeri/wasapi-exclusive.md]] · [[../k029-cross-platform-api/cross-platform-api.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Genel Bakış` — L12–L14


Windows Core modülü, COREMUSIC'ın Windows platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Windows API'nin advanced özelliklerini kullanarak gerçek zamanlı ses işleme ve çoklu ortam uygulamaları için optimize edilmiş bir altyapı sunar. Process management, memory management, threading ve IPC mekanizmaları için Windows'a özgü çözümler içerir.

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

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `API / Arayüz` — L332–L371


##### COREMUSIC Windows API Başlık Dosyası

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

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Bağımlılıklar` — L373–L388


##### Gereksinimler
- Windows 10/11 (1809 ve üzeri)
- Visual C++ Redistributable
- Windows SDK

##### Alt Katmanlar
- Donanım sürücüleri (ses kartı, GPU)
- Windows Audio Service
- WASAPI, DirectSound

##### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Performans Metrikleri` — L390–L398


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Process Oluşturma | < 1ms | Belirlenecek |
| Bellek Ayırma (Large Page) | < 0.1ms | Belirlenecek |
| Thread Oluşturma | < 0.5ms | Belirlenecek |
| Named Pipe latency | < 10μs | Belirlenecek |
| Event Log write | < 5ms | Belirlenecek |

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Güvenlik Notları` — L400–L406


1. **UAC**: Yönetici yetkisi gerektiren işlemler için UAC elevation kullanın
2. **Token Privileges**: Gereksiz privilege'ları devre dışı bırakın
3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın
4. **Registry Erişimi**: minimum yetki ile erişim sağlayın
5. **Named Pipes**: Güvenli pipe security descriptor kullanın

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` § `Durum: Implementasyon` — L408–L422


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
| NO_ERROR, 0, 0, 0 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L156 |
| Windows Event Log, uygulama hata ve bilgi kayıtları için: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L164 |
| 3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L404 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| VirtualAllocEx(pi.hProcess, NULL, bufferSize, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L38 |
| Windows bellek yönetimi, büyük audio buffer'lar için optimize edilmiştir: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L53 |
| LPVOID pBuffer = VirtualAlloc( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L61 |
| // Thread oluşturma ve CPU affinity ayarlama | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L87 |
| // Thread'i belirli bir CPU çekirdeğine bağla | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L89 |
| ProcessAudioBuffer(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L99 |
| DWORD bufferSize; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L125 |
| RegQueryValueEx(hKey, L"BufferSize", NULL, NULL, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L130 |
| (LPBYTE)&bufferSize, sizeof(DWORD)); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L131 |
| bufferDuration, 0, &format, NULL); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L221 |
| if (padding < bufferFrameCount) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L231 |
| bufferSize, bufferSize, 0, NULL); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L248 |
| ReadFile(hPipe, buffer, bufferSize, NULL, &overlapped); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L254 |
| // CPU sınırlaması ekleme | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L290 |
| JOBOBJECT_CPU_RATE_CONTROL cpuRate = { 0 }; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L291 |
| cpuRate.CpuRate = 50;  // %50 CPU kullanımı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L292 |
| SetInformationJobObject(hJob, JobObjectCpuRateControlInformation, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L293 |
| &cpuRate, sizeof(cpuRate)); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L294 |
| CM_Status CM_ReadPipe(HANDLE hPipe, void* buffer, size_t size, size_t* bytesRead); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L362 |

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
| `WASAPI` | 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L213 |
| `ASIO` | 0 | [kaynakta eşleşme yok] |
| `COM` | 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` L39 |
| `firmware` | 0 | [kaynakta eşleşme yok] |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Genel Bakış | L12–L14 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Teknik Detaylar | L16–L330 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## API / Arayüz | L332–L371 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Bağımlılıklar | L373–L388 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Performans Metrikleri | L390–L398 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Güvenlik Notları | L400–L406 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-core.md` | ## Durum: Implementasyon | L408–L422 | ✓ (verbatim) |

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
