---
title: "K015 ASIO · WASAPI · CoreAudio Sürücü Yüzeyi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K015 — ASIO · WASAPI · CoreAudio Sürücü Yüzeyi

> **K numarası:** K015 · **Klasör:** `k015-platform-suruculeri` · **Dosya:** `asio-wasapi-coreaudio`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Windows (ASIO, WASAPI exclusive) ve macOS (CoreAudio/HAL) platform ses arayüzlerinin rollerini, akış modlarını ve kısıtlarını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **ASIO · WASAPI · CoreAudio Sürücü Yüzeyi** konusunu ele alır. Kapsamı: ASIO callback protokolü, buffer/sample rate yönetimi ve donanım bağımsız çalışma şekli.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Uygulama (DAW) ]
            │
            ▼
      [ ASIO callback / WASAPI endpoint / CoreAudio HAL ]
            │
            ▼
      [ Platform sürücüsü ]
            │
            ▼
      [ OS ses motoru ]
            │
            ▼
      [ Donanım ]
```

**Akış notları:**

1. **Uygulama (DAW)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **ASIO callback / WASAPI endpoint / CoreAudio HAL** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Platform sürücüsü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **OS ses motoru** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Donanım** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | 180 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | 291 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/asio-drivers.md`

| Seviye | Sorumluluk |
|--------|------------|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Driver Interface | Chipset-specific register erişimi |
| HAL Abstraction | Platform-bağımsız arayüz |
| DMA Engine | Bellek → Donanım veri transferi |

### 4.2 · `k2-surucu/asio-drivers.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

### 4.3 · `k2-surucu/asio-drivers.md`

| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

### 4.4 · `k2-surucu/core-audio-macos.md`

| Property | Açıklama |
|----------|----------|
| `kAudioDevicePropertyDeviceNameCFString` | Cihaz adı |
| `kAudioDevicePropertyDeviceUID` | Benzersiz tanımlayıcı |
| `kAudioDevicePropertyTransportType` | Bağlantı türü |
| `kAudioDevicePropertySupportedSampleRates` | Desteklenen hızlar |
| `kAudioDevicePropertyAvailableNominalSampleRates` | Nominal hız aralığı |
| `kAudioDevicePropertyBufferFrameSize` | Buffer boyutu |

### 4.5 · `k2-surucu/core-audio-macos.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

### 4.6 · `k2-surucu/core-audio-macos.md`

| Bağımlılık | Tür |
|------------|-----|
| CoreAudio.framework | Apple framework |
| AudioToolbox.framework | Apple framework |
| K1 macOS Core | İç katman |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/asio-drivers.md` | H1 | ASIO Sürücüleri |
| 2 | `k2-surucu/asio-drivers.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/asio-drivers.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/asio-drivers.md` | H3 | ASIO Mimarisi |
| 5 | `k2-surucu/asio-drivers.md` | H3 | ASIO Exclusive Mode |
| 6 | `k2-surucu/asio-drivers.md` | H3 | Buffer Yönetimi |
| 7 | `k2-surucu/asio-drivers.md` | H3 | ASIO Callback Zinciri |
| 8 | `k2-surucu/asio-drivers.md` | H3 | Donanım Abstraction |
| 9 | `k2-surucu/asio-drivers.md` | H3 | Latency Hesaplama |
| 10 | `k2-surucu/asio-drivers.md` | H3 | Hata Yönetimi |
| 11 | `k2-surucu/asio-drivers.md` | H2 | API / Arayüz |
| 12 | `k2-surucu/asio-drivers.md` | H2 | Performans Metrikleri |
| 13 | `k2-surucu/asio-drivers.md` | H2 | Bağımlılıklar |
| 14 | `k2-surucu/asio-drivers.md` | H2 | Durum: Implementasyon |
| 15 | `k2-surucu/core-audio-macos.md` | H1 | CoreAudio macOS Sürücüsü |
| 16 | `k2-surucu/core-audio-macos.md` | H2 | Genel Bakış |
| 17 | `k2-surucu/core-audio-macos.md` | H2 | Teknik Detaylar |
| 18 | `k2-surucu/core-audio-macos.md` | H3 | CoreAudio Mimarisi |
| 19 | `k2-surucu/core-audio-macos.md` | H3 | AudioDevice Kullanımı |
| 20 | `k2-surucu/core-audio-macos.md` | H3 | AudioUnit Implementasyonu |
| 21 | `k2-surucu/core-audio-macos.md` | H3 | Callback Yapısı |
| 22 | `k2-surucu/core-audio-macos.md` | H3 | HAL Device Properties |
| 23 | `k2-surucu/core-audio-macos.md` | H3 | Zamanlama ve Senkronizasyon |
| 24 | `k2-surucu/core-audio-macos.md` | H3 | Output Device Seçimi |
| 25 | `k2-surucu/core-audio-macos.md` | H2 | API / Arayüz |
| 26 | `k2-surucu/core-audio-macos.md` | H2 | Performans Metrikleri |
| 27 | `k2-surucu/core-audio-macos.md` | H2 | Bağımlılıklar |
| 28 | `k2-surucu/core-audio-macos.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md`


### ASIO Sürücüleri

#### Genel Bakış

ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli doğrudan donanım erişimi sağlayan sürücü protokolüdür. COREMUSIC, ASIO Exclusive mode ile 0.5ms round-trip latency hedefler.

#### Teknik Detaylar

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

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

#### Bağımlılıklar

| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

#### Durum: Implementasyon

- **Faz 1**: ASIO SDK entegrasyonu ve temel callback yapısı
- **Faz 2**: Exclusive mode implementasyonu
- **Faz 3**: Buffer optimizasyonu ve latency testleri
- **Faz 4**: Hata yönetimi ve graceful degradation
- **Tahmini Süre**: 3 hafta (120 adam-saat)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md`


### CoreAudio macOS Sürücüsü

#### Genel Bakış

CoreAudio, Apple'ın macOS ve iOS için geliştirdiği kapsamlı ses altyapısıdır. COREMUSIC, CoreAudio'nun AudioUnit ve HAL (Hardware Abstraction Layer) katmanlarını kullanarak Apple platformlarında profesyonel ses desteği sağlar.

#### Teknik Detaylar

##### CoreAudio Mimarisi

```
┌─────────────────────────────────────────────┐
│            Uygulama (K3 Neva Engine)        │
├─────────────────────────────────────────────┤
│         AudioUnit Framework                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Converter│  │ Mixer    │  │ Effect   │  │
│  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────┤
│         AudioToolbox Framework              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ File I/O │  │ Codec    │  │ Stream   │  │
│  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────┤
│         CoreAudio HAL                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Device   │  │ Stream   │  │ Clock    │  │
│  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────┤
│         Audio HAL Plugin                    │
│  ┌──────────────────────────────────────┐   │
│  │  Donanım Sürücü (Apple/Core Audio)   │   │
│  └──────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

##### AudioDevice Kullanımı

CoreAudio'da cihazlar `AudioDeviceID` ile temsil edilir:

```cpp
// Cihaz listesini al
AudioObjectPropertyAddress propAddr = {
    kAudioHardwarePropertyDevices,
    kAudioObjectPropertyScopeGlobal,
    kAudioObjectPropertyElementMain
};

UInt32 dataSize = 0;
AudioObjectGetPropertyDataSize(kAudioObjectSystemObject,
                               &propAddr, 0, NULL, &dataSize);

int deviceCount = dataSize / sizeof(AudioDeviceID);
std::vector<AudioDeviceID> devices(deviceCount);
AudioObjectGetPropertyData(kAudioObjectSystemObject,
                           &propAddr, 0, NULL,
                           &dataSize, devices.data());
```

##### AudioUnit Implementasyonu

AudioUnit, CoreAudio'nun temel işlev birimidir:

```cpp
// AudioUnit oluştur
AudioComponentDescription desc = {
    .componentType = kAudioUnitType_Output,
    .componentSubType = kAudioUnitSubType_HALOutput,
    .componentManufacturer = kAudioUnitManufacturer_Apple,
    .componentFlags = 0,
    .componentFlagsMask = 0
};

AudioComponent comp = AudioComponentFindNext(NULL, &desc);
AudioUnit au;
AudioComponentInstanceNew(comp, &au);

// Input/Output akışlarını yapılandır
AudioUnitSetProperty(au,
    kAudioOutputUnitProperty_EnableIO,
    kAudioUnitScope_Input,
    1,  // Input element
    &enable,
    sizeof(enable));

// Formatı ayarla
AudioStreamBasicDescription asbd;
asbd.mSampleRate = 96000;
asbd.mFormatID = kAudioFormatLinearPCM;
asbd.mFormatFlags = kAudioFormatFlagIsFloat | 
                    kAudioFormatFlagIsPacked;
asbd.mBitsPerChannel = 32;
asbd.mChannelsPerFrame = 2;
asbd.mFramesPerPacket = 1;
asbd.mBytesPerFrame = asbd.mChannelsPerFrame * 
                       (asbd.mBitsPerChannel / 8);
asbd.mBytesPerPacket = asbd.mBytesPerFrame;

AudioUnitSetProperty(au,
    kAudioUnitProperty_StreamFormat,
    kAudioUnitScope_Input,
    0,  // Output element
    &asbd,
    sizeof(asbd));
```

##### Callback Yapısı

CoreAudio, render callback ile çalışır:

```cpp
// Render callback
static OSStatus renderCallback(void* inRefCon,
                               AudioUnitRenderActionFlags* ioActionFlags,
                               const AudioTimeStamp* inTimeStamp,
                               UInt32 inBusNumber,
                               UInt32 inNumberFrames,
                               AudioBufferList* ioData) {
    // K3'ten veri al
    float* buffer = (float*)ioData->mBuffers[0].mData;
    
    // Neva Engine'den output oku
    engine->process(buffer, inNumberFrames);
    
    return noErr;
}

// Callback'ı AudioUnit'a bağla
AURenderCallbackStruct callbackStruct;
callbackStruct.inputProc = renderCallback;
callbackStruct.inputProcRefCon = engine;

AudioUnitSetProperty(au,
    kAudioOutputUnitProperty_SetInputCallback,
    kAudioUnitScope_Input,
    0,
    &callbackStruct,
    sizeof(callbackStruct));
```

##### HAL Device Properties

CoreAudio HAL'ı cihaz özelliklerini yönetir:

| Property | Açıklama |
|----------|----------|
| `kAudioDevicePropertyDeviceNameCFString` | Cihaz adı |
| `kAudioDevicePropertyDeviceUID` | Benzersiz tanımlayıcı |
| `kAudioDevicePropertyTransportType` | Bağlantı türü |
| `kAudioDevicePropertySupportedSampleRates` | Desteklenen hızlar |
| `kAudioDevicePropertyAvailableNominalSampleRates` | Nominal hız aralığı |
| `kAudioDevicePropertyBufferFrameSize` | Buffer boyutu |

##### Zamanlama ve Senkronizasyon

CoreAudio, hassas zamanlama sağlar:

```cpp
// AudioDeviceID ve TimeStamp kullanımı
AudioTimeStamp timestamp;
AudioDeviceGetCurrentTime(deviceID, &timestamp);

// Zaman damgası ile senkronizasyon
AudioOutputUnitStart(au);

// Clock rate değişikliği
Float64 sampleRate;
AudioObjectGetPropertyData(deviceID,
    &(AudioObjectPropertyAddress){
        kAudioDevicePropertyNominalSampleRate,
        kAudioObjectPropertyScopeGlobal,
        kAudioObjectPropertyElementMain
    },
    0, NULL, &sampleSize, &sampleRate);
```

##### Output Device Seçimi

macOS'te çıkış cihazı seçimi:

```cpp
// Varsayılan çıkış cihazını al
AudioObjectPropertyAddress propAddr = {
    kAudioHardwarePropertyDefaultOutputDevice,
    kAudioObjectPropertyScopeGlobal,
    kAudioObjectPropertyElementMain
};

AudioDeviceID defaultDevice;
UInt32 dataSize = sizeof(AudioDeviceID);
AudioObjectGetPropertyData(kAudioObjectSystemObject,
                           &propAddr, 0, NULL,
                           &dataSize, &defaultDevice);

// Çıkış cihazını değiştir
AudioObjectSetPropertyData(kAudioObjectSystemObject,
    &propAddr,
    0,
    NULL,
    sizeof(AudioDeviceID),
    &newDeviceID);
```

#### API / Arayüz

```cpp
class CoreAudioDriver {
public:
    bool initialize();
    void shutdown();
    
    // Cihaz yönetimi
    std::vector<CoreAudioDevice> listDevices() const;
    CoreAudioDevice getDefaultOutputDevice() const;
    CoreAudioDevice getDefaultInputDevice() const;
    bool setOutputDevice(uint32_t deviceId);
    
    // Akış yapılandırması
    bool setSampleRate(double rate);
    bool setBufferSize(uint32_t frames);
    bool setChannelCount(uint32_t channels);
    
    // AudioUnit kontrolü
    bool start();
    bool stop();
    bool isRunning() const;
    
    // Callback kayıt
    void registerRenderCallback(RenderCallback callback,
                                void* userData);
    
    // Zamanlama
    double getCurrentTime() const;
    double getLatency() const;
    
    // Cihaz özellikleri
    std::vector<double> getSupportedSampleRates(
        uint32_t deviceId) const;
    std::pair<uint32_t, uint32_t> getBufferSizeRange(
        uint32_t deviceId) const;
};

// Kullanım örneği
CoreAudioDriver driver;
driver.initialize();

auto outputDevice = driver.getDefaultOutputDevice();
driver.setOutputDevice(outputDevice.deviceId);
driver.setSampleRate(96000);
driver.setBufferSize(256);

driver.registerRenderCallback([](float* buffer, uint32_t frames) {
    engine->process(buffer, frames);
}, engine.get());

driver.start();
```

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| CoreAudio.framework | Apple framework |
| AudioToolbox.framework | Apple framework |
| K1 macOS Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: CoreAudio SDK entegrasyonu, temel cihaz yönetimi
- **Faz 2**: AudioUnit implementasyonu
- **Faz 3**: Render callback, zamanlama
- **Faz 4**: Çoklu cihaz desteği
- **Tahmini Süre**: 3 hafta (120 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | ASIO sürücüsü hangi donanıma ait, vault'ta cihaz listesi yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Callback süresi ölçümü (µs/ms) mevcut değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Callback'in bütçe aşması → glitch (kaynak: `asio-drivers`).
2. Sample rate değişikliğinde yeniden başlatma gerekliliği (kaynak: `asio-drivers`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k014-surucu-yigin/index]]` | ↑ | Genel sürücü yığını ve buffer kuralları | `.ai/architecture/k014-surucu-yigin/index.md` |
| `[[../k000-windows-core/index]]` | ↑ | Windows API yüzeyi (Win32/COM) | `.ai/architecture/k000-windows-core/index.md` |
| `[[../k002-macos-tasinabilirlik/index]]` | ↑ | macOS çekirdek/taşınabilirlik | `.ai/architecture/k002-macos-tasinabilirlik/index.md` |
| `[[../k016-linux-ses/index]]` | ↓ | Linux karşılığı ALSA/PipeWire | `.ai/architecture/k016-linux-ses/index.md` |

Yerel dosyalar:

- `[[asio-wasapi-coreaudio]]` — ASIO · WASAPI · CoreAudio Sürücü Yüzeyi
- `[[wasapi-coreaudio-platform-detay]]` — WASAPI ve CoreAudio Platform Detayı

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K015 · ASIO · WASAPI · CoreAudio Sürücü Yüzeyi — SSOT: `.ai/architecture/k015-platform-suruculeri/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
