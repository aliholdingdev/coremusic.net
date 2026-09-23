---
title: "Windows Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Windows"
date: 2026-09-20
version: 1.0.0
---

# Windows Core

## Genel Bakış

Windows Core modülü, COREMUSIC'ın Windows platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Windows API'nin advanced özelliklerini kullanarak gerçek zamanlı ses işleme ve çoklu ortam uygulamaları için optimize edilmiş bir altyapı sunar. Process management, memory management, threading ve IPC mekanizmaları için Windows'a özgü çözümler içerir.

## Teknik Detaylar

### 1. Process Management

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

### 2. Memory Management

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

### 3. Threading Model

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

### 4. Registry Operations

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

### 5. Service Management

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

### 6. Event Log

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

### 7. Windows Management Instrumentation (WMI)

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

### 8. COM Interface

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

### 9. IPC - Named Pipes

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

### 10. Memory Mapped Files

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

### 11. Job Objects

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

### 12. User Account Control (UAC)

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

## API / Arayüz

### COREMUSIC Windows API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- Windows 10/11 (1809 ve üzeri)
- Visual C++ Redistributable
- Windows SDK

### Alt Katmanlar
- Donanım sürücüleri (ses kartı, GPU)
- Windows Audio Service
- WASAPI, DirectSound

### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Process Oluşturma | < 1ms | Belirlenecek |
| Bellek Ayırma (Large Page) | < 0.1ms | Belirlenecek |
| Thread Oluşturma | < 0.5ms | Belirlenecek |
| Named Pipe latency | < 10μs | Belirlenecek |
| Event Log write | < 5ms | Belirlenecek |

## Güvenlik Notları

1. **UAC**: Yönetici yetkisi gerektiren işlemler için UAC elevation kullanın
2. **Token Privileges**: Gereksiz privilege'ları devre dışı bırakın
3. **Job Objects**: Kaynak sınırlamaları ile process koruması sağlayın
4. **Registry Erişimi**:最小imal yetki ile erişim sağlayın
5. **Named Pipes**: Güvenli pipe security descriptor kullanın

## Durum: Implementasyon

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
