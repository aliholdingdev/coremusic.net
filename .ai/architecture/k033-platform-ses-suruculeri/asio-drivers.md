---
title: "ASIO Sürücüleri - k033-platform-ses-suruculeri"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# ASIO Sürücüleri

> Klasör: `k033-platform-ses-suruculeri` · Dilim: D01 (k018–k035) · Dosya: `asio-drivers.md`
> Sorumlu persona: `windows-software-engineer` (Windows Software Mühendisi) · `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

ASIO sürücü yolu, SDK entegrasyonu ve exclusive-mode davranışı.

Bu belge; D01 diliminin (Platform Ses Sürücüleri) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** ASIO sürücü yolu, SDK entegrasyonu ve exclusive-mode davranışı.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Platform Ses Sürücüleri)
- **Çapraz referanslar:** [[../k029-cross-platform-api/cross-platform-api.md]] · [[../k030-driver-stack/driver-stack-mimari.md]] · [[../k020-linux-core/alsa-native.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### asio-drivers.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` (179 satır)

#### asio-drivers.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § (giriş) — L1–L9

---
title: "ASIO Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### ASIO Sürücüleri


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Genel Bakış` — L10–L12


ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli doğrudan donanım erişimi sağlayan sürücü protokolüdür. COREMUSIC, ASIO Exclusive mode ile 0.5ms round-trip latency hedefler.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Teknik Detaylar` — L14–L112


##### ASIO Mimarisi

ASIO, Windows ses alt yapısını (WDM/MME/DirectSound) tamamen atlayarak uygulama ile ses kartı arasında doğrudan bir veri yolu oluşturur. Bu sayede:

- **Kernel geçişleri minimize edilir**: Veri kopyalama yalnızca bir kez gerçekleşir
- **Buffer boyutu uygulama tarafından kontrol edilir**: 32 sample'a kadar düşürülebilir
- **Interrupt-driven processing**: Donanım kesmesi tetikleme ile çalışır

##### ASIO Exclusive Mode

COREMUSIC, iki mod destekler:

```
┌─────────────────────────────────────────────┐
│           ASIO Working Modes                │
├─────────────────┬───────────────────────────┤
│ Exclusive Mode  │ Shared Mode               │
├─────────────────┼───────────────────────────┤
│ Doğrudan HW     │ Windows Mixed ile_paylaşım│
│ < 0.5ms latency │ 2-10ms latency            │
│ Tek uygulama    │ Çoklu uygulama             │
│ Kesin kontrol   │ Sınırlı kontrol            │
└─────────────────┴───────────────────────────┘
```

##### Buffer Yönetimi

ASIO buffer yönetimi kritik önem taşır:

1. **Double Buffering**: İki buffer arasında kesintisiz geçiş
   - Buffer A okunurken Buffer B yazılır
   - Geçiş: `callbackDrivenMode` ile tetiklenir

2. **Buffer Boyut Seçimi**:
   - 32 sample @ 96kHz = 0.33ms (minimum)
   - 64 sample @ 96kHz = 0.67ms (dengeli)
   - 128 sample @ 96kHz = 1.33ms (güvenli)

3. **Ring Buffer Implementasyonu**:
   ```
   Head → [data] → [data] → [data] → Tail
   Head, donanım tarafından güncellenir
   Tail, uygulama tarafından güncellenir
   ```

##### ASIO Callback Zinciri

```cpp
void ASIOCallback(long index, long process) {
    // 1. Input buffer'ı oku
    readInputBuffer(index, inputBuffers);
    
    // 2. K3 Ses Motoru'na ilet
    feedToEngine(inputBuffers, sampleCount);
    
    // 3. K3'ten output buffer'ı al
    readFromEngine(outputBuffers, sampleCount);
    
    // 4. Output buffer'ı donanıma yaz
    writeOutputBuffer(index, outputBuffers);
}
```

##### Donanım Abstraction

ASIO sürücüsü aşağıdaki soyutlama katmanlarını kullanır:

| Seviye | Sorumluluk |
|--------|------------|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Driver Interface | Chipset-specific register erişimi |
| HAL Abstraction | Platform-bağımsız arayüz |
| DMA Engine | Bellek → Donanım veri transferi |

##### Latency Hesaplama

```
Total Latency = Input Buffer + Processing + Output Buffer + Driver Overhead

Örnek (96kHz, 64 sample):
Input:    64/96000 = 0.667ms
Process:  ~0.1ms (K3 DSP)
Output:   64/96000 = 0.667ms
Driver:   ~0.05ms
─────────────────────────────
Total:    ~1.48ms (one-way)
RTT:      ~2.96ms (round-trip)
```

##### Hata Yönetimi

ASIO sürücüsü aşağıdaki hata durumlarını işler:

- **ASIOError_InvalidMode**: Exclusive mode kullanılamıyorsa Shared mode'a geç
- **ASIOError_BufferSize**: Buffer boyutu donanım tarafından desteklenmiyorsa
- **ASIOError_HardwareFailure**: Donanım hatası, K3'ü durdur
- **ASIOError_UnableToStart**: Başlatma hatası, 3 yeniden deneme

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `API / Arayüz` — L114–L152


```cpp
// ASIO Driver Manager
class ASIODriverManager {
public:
    bool initialize(const AudioDeviceConfig& config);
    bool start();
    void stop();
    
    // Buffer yapılandırması
    bool setBufferSize(long minSize, long maxSize, long* preferred);
    bool canSampleRate(ASIOSampleRate rate);
    bool setSampleRate(ASIOSampleRate rate);
    
    // Callback kayıt
    void registerCallback(ASIOCallback* callback);
    
    // Exclusive mode kontrolü
    bool enableExclusiveMode();
    bool isExclusiveModeActive() const;
    
    // Latency bilgisi
    double getInputLatency() const;
    double getOutputLatency() const;
};

// Örnek kullanım
ASIODriverManager driver;
AudioDeviceConfig config;
config.deviceId = getDefaultASIODevice();
config.sampleRate = 96000;
config.bufferSize = 64;
config.exclusiveMode = true;

driver.initialize(config);
driver.setBufferSize(32, 128, &config.bufferSize);
driver.start();
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Performans Metrikleri` — L154–L163


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Bağımlılıklar` — L165–L171


| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Durum: Implementasyon` — L173–L179


- **Faz 1**: ASIO SDK entegrasyonu ve temel callback yapısı
- **Faz 2**: Exclusive mode implementasyonu
- **Faz 3**: Buffer optimizasyonu ve latency testleri
- **Faz 4**: Hata yönetimi ve graceful degradation
- **Tahmini Süre**: 3 hafta (120 adam-saat)

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
| ### Hata Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L105 |
| ASIO sürücüsü aşağıdaki hata durumlarını işler: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L107 |
| - **ASIOError_InvalidMode**: Exclusive mode kullanılamıyorsa Shared mode'a geç | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L109 |
| - **ASIOError_BufferSize**: Buffer boyutu donanım tarafından desteklenmiyorsa | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L110 |
| - **ASIOError_HardwareFailure**: Donanım hatası, K3'ü durdur | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L111 |
| - **ASIOError_UnableToStart**: Başlatma hatası, 3 yeniden deneme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L112 |
| config.deviceId = getDefaultASIODevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L144 |
| - **Faz 4**: Hata yönetimi ve graceful degradation | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L178 |
| hr = pEnumerator->GetDefaultAudioEndpoint( | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L118 |
| NULL,                           // Default security | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L157 |
| 0,                              // Default stack size | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L158 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli do… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L12 |
| - **Buffer boyutu uygulama tarafından kontrol edilir**: 32 sample'a kadar düşürülebilir | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L21 |
| │ < 0.5ms latency │ 2-10ms latency            │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L35 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L41 |
| ASIO buffer yönetimi kritik önem taşır: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L43 |
| 1. **Double Buffering**: İki buffer arasında kesintisiz geçiş | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L45 |
| - Buffer A okunurken Buffer B yazılır | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L46 |
| 2. **Buffer Boyut Seçimi**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L49 |
| - 32 sample @ 96kHz = 0.33ms (minimum) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L50 |
| - 64 sample @ 96kHz = 0.67ms (dengeli) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L51 |
| - 128 sample @ 96kHz = 1.33ms (güvenli) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L52 |
| 3. **Ring Buffer Implementasyonu**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L54 |
| // 1. Input buffer'ı oku | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L65 |
| readInputBuffer(index, inputBuffers); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L66 |
| feedToEngine(inputBuffers, sampleCount); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L69 |
| // 3. K3'ten output buffer'ı al | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L71 |
| readFromEngine(outputBuffers, sampleCount); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L72 |
| // 4. Output buffer'ı donanıma yaz | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L74 |
| writeOutputBuffer(index, outputBuffers); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L75 |
| ### Latency Hesaplama | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L90 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - **Faz 3**: Buffer optimizasyonu ve latency testleri | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L177 |
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
| `ASIO` | 47 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L2 |
| `WASAPI` | 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` L91 |
| `latency` | 13 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L12 |
| `buffer` | 35 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` L21 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## Teknik Detaylar | L14–L112 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## API / Arayüz | L114–L152 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## Performans Metrikleri | L154–L163 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## Bağımlılıklar | L165–L171 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ## Durum: Implementasyon | L173–L179 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | (başlık + giriş) | L1–L24 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 1. ASIO SDK Entegrasyonu | L25–L89 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 2. WASAPI Entegrasyonu | L91–L148 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 3. Windows Threading | L150–L186 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 4. Bellek Yönetimi | L188–L236 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 5. COM Initialization | L238–L251 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/windows-api.md` | ## 6. Windows Specific ADR'ler | L253–L265 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` § `Durum: Implementasyon` — L173–L179


- **Faz 1**: ASIO SDK entegrasyonu ve temel callback yapısı
- **Faz 2**: Exclusive mode implementasyonu
- **Faz 3**: Buffer optimizasyonu ve latency testleri
- **Faz 4**: Hata yönetimi ve graceful degradation
- **Tahmini Süre**: 3 hafta (120 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
