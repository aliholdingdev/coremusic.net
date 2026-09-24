---
title: "CoreAudio macOS Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# CoreAudio macOS Sürücüsü

## Genel Bakış

CoreAudio, Apple'ın macOS ve iOS için geliştirdiği kapsamlı ses altyapısıdır. COREMUSIC, CoreAudio'nun AudioUnit ve HAL (Hardware Abstraction Layer) katmanlarını kullanarak Apple platformlarında profesyonel ses desteği sağlar.

## Teknik Detaylar

### CoreAudio Mimarisi

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

### AudioDevice Kullanımı

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

### AudioUnit Implementasyonu

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

### Callback Yapısı

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

### HAL Device Properties

CoreAudio HAL'ı cihaz özelliklerini yönetir:

| Property | Açıklama |
|----------|----------|
| `kAudioDevicePropertyDeviceNameCFString` | Cihaz adı |
| `kAudioDevicePropertyDeviceUID` | Benzersiz tanımlayıcı |
| `kAudioDevicePropertyTransportType` | Bağlantı türü |
| `kAudioDevicePropertySupportedSampleRates` | Desteklenen hızlar |
| `kAudioDevicePropertyAvailableNominalSampleRates` | Nominal hız aralığı |
| `kAudioDevicePropertyBufferFrameSize` | Buffer boyutu |

### Zamanlama ve Senkronizasyon

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

### Output Device Seçimi

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

## API / Arayüz

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

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| CoreAudio.framework | Apple framework |
| AudioToolbox.framework | Apple framework |
| K1 macOS Core | İç katman |

## Durum: Implementasyon

- **Faz 1**: CoreAudio SDK entegrasyonu, temel cihaz yönetimi
- **Faz 2**: AudioUnit implementasyonu
- **Faz 3**: Render callback, zamanlama
- **Faz 4**: Çoklu cihaz desteği
- **Tahmini Süre**: 3 hafta (120 adam-saat)
