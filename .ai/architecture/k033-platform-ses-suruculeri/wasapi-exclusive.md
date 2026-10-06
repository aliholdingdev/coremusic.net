---
title: "WASAPI Exclusive - k033-platform-ses-suruculeri"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# WASAPI Exclusive

> Klasör: `k033-platform-ses-suruculeri` · Dilim: D01 (k018–k035) · Dosya: `wasapi-exclusive.md`
> Sorumlu persona: `windows-software-engineer` (Windows Software Mühendisi) · `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

WASAPI exclusive ve shared modlar, akış yönetimi, metrikler.

Bu belge; D01 diliminin (Platform Ses Sürücüleri) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** WASAPI exclusive ve shared modlar, akış yönetimi, metrikler.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Platform Ses Sürücüleri)
- **Çapraz referanslar:** [[../k029-cross-platform-api/cross-platform-api.md]] · [[../k030-driver-stack/driver-stack-mimari.md]] · [[../k020-linux-core/alsa-native.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### wasapi-exclusive.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` (199 satır)

#### wasapi-exclusive.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § (giriş) — L1–L9

---
title: "WASAPI Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### WASAPI Sürücüleri


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Genel Bakış` — L10–L12


WASAPI (Windows Audio Session API), Windows Vista ve sonrası için Microsoft'un modern ses API'sidir. COREMUSIC, WASAPI'yi hem Exclusive hem de Shared modda kullanarak Windows platformunda profesyonel ses desteği sağlar.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Teknik Detaylar` — L14–L129


##### WASAPI Çalışma Modları

```
┌─────────────────────────────────────────────────────────┐
│                WASAPI Working Modes                     │
├─────────────────────┬───────────────────────────────────┤
│   Exclusive Mode    │         Shared Mode               │
├─────────────────────┼───────────────────────────────────┤
│ Doğrudan HW erişimi │ Windows Audio Service üzerinden   │
│ Mix organizer yok   │ Mix organizer devrede             │
│ Tek uygulama        │ Çoklu uygulama                    │
│ Bit-perfect output  │ DSP eklenebilir                   │
│ Düşük latency       │ Yüksek latency                    │
│ Donanım formatı     │ Win formats (PCM, IEEE float)     │
└─────────────────────┴───────────────────────────────────┘
```

##### Exclusive Mode Implementasyonu

Exclusive mode, uygulamanın ses kartına doğrudan erişmesini sağlar:

1. **IAudioClient arayüzü**: Ana kontrol noktası
   - `Initialize()`: Exclusive mode ile başlatma
   - `GetBufferDuration()`: Buffer süresi sorgusu
   - `Start()` / `Stop()`: Akış kontrolü

2. **IAudioCaptureClient**: Giriş verisi okuma
   - `GetBuffer()`: Ham veri erişimi
   - `ReleaseBuffer()`: Buffer'ı serbest bırak
   - `GetNextPacketSize()`: Bir sonraki paket boyutu

3. **IAudioRenderClient**: Çıkış verisi yazma
   - `GetBuffer()`: Yazma alanı alma
   - `ReleaseBuffer()`: Veriyi donanıma iletme

##### Exclusive Mode Avantajları

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Latency | 1-3ms | 10-40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP | Donanım | Windows |

##### Buffer Yönetimi

WASAPI buffer yönetimi ASIO'dan farklıdır:

```
Exclusive Mode:
┌──────────────────────────────────────┐
│  App Buffer → Driver Buffer → HW     │
│  (definite)   (definite)             │
└──────────────────────────────────────┘

Shared Mode:
┌──────────────────────────────────────┐
│  App Buffer → Audio Engine → HW      │
│  (definite)   (indefinite)           │
└──────────────────────────────────────┘
```

**Buffer Boyut Hesaplama**:
```
BufferDuration = (BufferSize / SampleRate) * 1,000,000 (μs)

Örnek:
Buffer: 288 samples @ 48kHz
Duration: (288 / 48000) * 1,000,000 = 6000μs = 6ms
```

##### Windows Audio Session

Her WASAPI oturumu aşağıdaki bileşenleri içerir:

| Bileşen | Açıklama |
|---------|----------|
| AudioSessionControl | Oturum kontrolü (ses seviyesi, durdurma) |
| AudioSessionManager | Oturum yönetimi (tercihler, efektler) |
| AudioMeterInformation | Gerçek zamanlı ses seviyesi metering |
| AudioEndpointVolume | Donanım ses seviyesi kontrolü |

##### Format Desteği

WASAPI aşağıdaki ses formatlarını destekler:

```cpp
// Desteklenen formatlar
WAVEFORMATEX formats[] = {
    {WAVE_FORMAT_PCM,     16, 2, 48000},  // 16-bit PCM
    {WAVE_FORMAT_PCM,     24, 2, 96000},  // 24-bit PCM
    {WAVE_FORMAT_IEEE_FLOAT, 32, 2, 192000}, // 32-bit Float
    {WAVE_FORMAT_EXTENSIBLE, 32, 8, 384000}, // 8-kanal 32-bit
};
```

##### Latency Optimizasyonu

WASAPI Exclusive mode'da minimum latency için:

1. **Buffer Süresi**: 3-6ms arası (donanıma bağlı)
2. **Period Goddessi**: `IAudioClient::SetEventHandle()` ile kesme zamanlaması
3. **Thread Önceliği**: `THREAD_PRIORITY_TIME_CRITICAL` ile yüksek öncelik
4. **CPU Affinity**: Ses thread'ini belirli CPU çekirdeğine ata

##### Hata Durumları

| Hata | Kod | Çözüm |
|------|-----|-------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Shared mode'a geç |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Formatı değiştir |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Yetki kontrolü |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer boyutunu ayarla |

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `API / Arayüz` — L131–L173


```cpp
class WASAPIDriver {
public:
    bool initialize(const AudioConfig& config);
    bool startExclusive();
    bool startShared();
    void stop();
    
    // Buffer yönetimi
    bool setBufferDuration(uint32_t durationMs);
    uint32_t getBufferDuration() const;
    
    // Format yapılandırması
    bool setFormat(const WAVEFORMATEX& format);
    bool isFormatSupported(const WAVEFORMATEX& format, 
                           EDataFlow dataFlow);
    
    // Session yönetimi
    bool setSessionName(const wchar_t* name);
    bool setSessionIconPath(const wchar_t* path);
    
    // Volume kontrolü
    bool setMasterVolume(float volume);
    bool setMute(bool mute);
    
    // Metering
    float getPeakLevel() const;
    float getRMSLevel() const;
};

// Exclusive mode başlatma örneği
WASAPIDriver driver;
AudioConfig config;
config.sampleRate = 96000;
config.bitsPerSample = 32;
config.channels = 2;
config.bufferDurationMs = 4;

driver.initialize(config);
driver.startExclusive();
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Performans Metrikleri` — L175–L183


| Metrik | Exclusive | Shared |
|--------|-----------|--------|
| Input Latency | 1.5ms | 15ms |
| Output Latency | 1.5ms | 15ms |
| Round-trip | 3ms | 30ms |
| CPU (boşta) | 0.5% | 2% |
| Bit-perfect | Evet | Hayır |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Bağımlılıklar` — L185–L191


| Bağımlılık | Tür |
|------------|-----|
| Windows SDK | Dış |
| K1 Windows Core | İç |
| K3 Engine | İç |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Durum: Implementasyon` — L193–L199


- **Faz 1**: WASAPI SDK entegrasyonu, temel Exclusive mode
- **Faz 2**: Shared mode, session yönetimi
- **Faz 3**: Metering ve volume kontrolü
- **Faz 4**: Format dönüşümleri, hata yönetimi
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)

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

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| ### Hata Durumları | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L122 |
| - **Faz 4**: Format dönüşümleri, hata yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L198 |
| hr = pEnumerator->GetDefaultAudioEndpoint( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L118 |
| NULL,                           // Default security | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L157 |
| 0,                              // Default stack size | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L158 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| │ Düşük latency       │ Yüksek latency                    │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L28 |
| - `GetBufferDuration()`: Buffer süresi sorgusu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L39 |
| - `GetBuffer()`: Ham veri erişimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L43 |
| - `ReleaseBuffer()`: Buffer'ı serbest bırak | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L44 |
| - `GetBuffer()`: Yazma alanı alma | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L48 |
| - `ReleaseBuffer()`: Veriyi donanıma iletme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L49 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L61 |
| WASAPI buffer yönetimi ASIO'dan farklıdır: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L63 |
| │  App Buffer → Driver Buffer → HW     │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L68 |
| │  App Buffer → Audio Engine → HW      │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L74 |
| **Buffer Boyut Hesaplama**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L79 |
| BufferDuration = (BufferSize / SampleRate) * 1,000,000 (μs) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L81 |
| Buffer: 288 samples @ 48kHz | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L84 |
| Duration: (288 / 48000) * 1,000,000 = 6000μs = 6ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L85 |
| ### Latency Optimizasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L113 |
| WASAPI Exclusive mode'da minimum latency için: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L115 |
| 1. **Buffer Süresi**: 3-6ms arası (donanıma bağlı) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L117 |
| 4. **CPU Affinity**: Ses thread'ini belirli CPU çekirdeğine ata | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L120 |
| // Buffer yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L141 |
| bool setBufferDuration(uint32_t durationMs); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L142 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| config.bitsPerSample = 32; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L167 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **0** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

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
| `WASAPI` | 17 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L2 |
| `ASIO` | 22 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L63 |
| `exclusive` | 17 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L12 |
| `latency` | 8 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` L28 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## Teknik Detaylar | L14–L129 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## API / Arayüz | L131–L173 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## Performans Metrikleri | L175–L183 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## Bağımlılıklar | L185–L191 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | ## Durum: Implementasyon | L193–L199 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | (başlık + giriş) | L1–L24 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 1. ASIO SDK Entegrasyonu | L25–L89 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 2. WASAPI Entegrasyonu | L91–L148 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 3. Windows Threading | L150–L186 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 4. Bellek Yönetimi | L188–L236 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 5. COM Initialization | L238–L251 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 6. Windows Specific ADR'ler | L253–L265 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` § `Durum: Implementasyon` — L193–L199


- **Faz 1**: WASAPI SDK entegrasyonu, temel Exclusive mode
- **Faz 2**: Shared mode, session yönetimi
- **Faz 3**: Metering ve volume kontrolü
- **Faz 4**: Format dönüşümleri, hata yönetimi
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
