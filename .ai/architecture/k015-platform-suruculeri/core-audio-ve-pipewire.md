---
title: "CoreAudio ve PipeWire Sürücüleri - k015-platform-suruculeri"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# CoreAudio ve PipeWire Sürücüleri

> Klasör: `k015-platform-suruculeri` · Dosya: `core-audio-ve-pipewire.md`
> Sorumlu persona: `embedded-engineer` (Embedded Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` (1 satir / 6 bolum) + `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` (1 satir / 6 bolum) — başlık + genel bakış + teknik detay bölümleri aktarılmıştır.
## Genel Bakış

macOS tarafında CoreAudio (AudioUnit, HAL device properties, callback yapısı, zamanlama/senkronizasyon, output device seçimi), modern Linux tarafında PipeWire (mimari, SPA plugin sistemi, grafik işleme, real-time zamanlama, buffer yönetimi, port ve donanım yönetimi). Bu dosya iki kaynaktan teknik detay bölümlerini aktarır; API / performans / bağımlılık bölümleri satır sınırı nedeniyle aktarılmamıştır (bkz. Aktarılmayan Kaynak Bölümleri).


## Kapsam ve Sınırlar

- **Kapsam:** CoreAudio mimarisi, AudioDevice, AudioUnit, callback, HAL properties, zamanlama, output device seçimi; PipeWire mimarisi, SPA plugin, grafik işleme, RT zamanlama, buffer, port yönetimi.
- **Kapsam dışı:** aktarılmayan API / performans / bağımlılık bölümleri (aşağıda L aralıklarıyla listelenmiştir), Windows ASIO/WASAPI (→ [[asio-ve-wasapi.md]]), ALSA native (→ [[../k016-linux-ses/index]]).
- **Bağlı olduğu klasör:** [[index.md]] (Platform Sürücüleri)
- **Çapraz referanslar:** [[../k002-macos-tasinabilirlik/index]] · [[../k038-core-audio-macos/index]] · [[../k016-linux-ses/index]] · [[../k033-platform-ses-suruculeri/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi (`#` → `###`); her blokta kanıt satırı kaynak dosyayı ve gerçek satır aralığını (`L<başlangıç>-L<bitiş>`) gösterir. Bloklar kaynaktan değiştirilmeden kopyalanmıştır.

### core-audio-macos.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` (290 satir)


#### CoreAudio macOS Sürücüsü

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md - L8-L13

### CoreAudio macOS Sürücüsü

#### Genel Bakış

CoreAudio, Apple'ın macOS ve iOS için geliştirdiği kapsamlı ses altyapısıdır. COREMUSIC, CoreAudio'nun AudioUnit ve HAL (Hardware Abstraction Layer) katmanlarını kullanarak Apple platformlarında profesyonel ses desteği sağlar.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md - L14-L210

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

### pipewire-modern.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` (253 satir)


#### PipeWire Modern Sürücü

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md - L8-L13

### PipeWire Modern Sürücü

#### Genel Bakış

PipeWire, modern Linux ses yönlendirmesi için geliştirilen bir framework'tür. ALSA, PulseAudio ve JACK'ın yerini alarak tek bir unified API ile tüm ses ihtiyaçlarını karşılar. COREMUSIC, PipeWire SPA (Simple Plugin API) plugin mimarisini kullanarak yüksek performanslı ses işleme sağlar.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md - L14-L163

#### Teknik Detaylar

##### PipeWire Mimarisi

```
┌─────────────────────────────────────────────┐
│            Uygulama Katmanı                  │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐     │
│  │ Media1  │  │ Media2  │  │ COREMUSIC│     │
│  └────┬────┘  └────┬────┘  └────┬────┘     │
│       │            │            │            │
├───────┴────────────┴────────────┴────────────┤
│            PipeWire Daemon                   │
│  ┌────────────────────────────────────────┐  │
│  │         Graph Manager                  │  │
│  │    (Node connections, scheduling)      │  │
│  └────────────────────────────────────────┘  │
├──────────────────────────────────────────────┤
│            SPA Plugin System                 │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐       │
│  │ ALSA │ │ V4L2 │ │ File │ │ DSP  │       │
│  └──────┘ └──────┘ └──────┘ └──────┘       │
└──────────────────────────────────────────────┘
```

##### SPA Plugin Sistemi

SPA (Simple Plugin API), PipeWire'nin plugin mimarisidir:

**Plugin Tipleri**:
| Tip | Açıklama |
|-----|----------|
| `spa/support` | Destekleyici pluginler (logger, dict) |
| `spa/clock` | Zamanlama servisleri |
| `spa/node` | İşlem node'ları |
| `spa/device` | Donanım cihazları |
| `spa/lib` | Yardımcı kütüphaneler |

**COREMUSIC SPA Plugin'leri**:
```c
// Audio processor plugin
static const struct spa_node_methods impl_node = {
    SPA_VERSION_NODE_METHODS,
    .enum_params = impl_enum_params,
    .set_param = impl_set_param,
    .send_command = impl_send_command,
    .set_callback = impl_set_callback,
    .start = impl_start,
    .pause = impl_pause,
    .stop = impl_stop,
    .send_port = impl_send_port,
    .process = impl_process,      // Ana processing callback
};
```

##### Grafik İşleme

PipeWire, ses akışını bir grafik olarak yönetir:

```
┌──────────────────────────────────────────────┐
│              PipeWire Graph                   │
│                                              │
│  [USB In] → [COREMUSIC DSP] → [Speakers]    │
│      ↓           ↓                ↓          │
│  [Capture]   [Processing]    [Playback]      │
└──────────────────────────────────────────────┘
```

**Grafik Optimizasyonu**:
1. Otomatik mengenaj (degrade) ekleme
2. Format dönüşümü node'ları ekleme
3. Buffer boyutu uyumsuzluklarını çözme
4. Zamanlama (scheduling) optimizasyonu

##### Real-Time Zamanlama

PipeWire, gerçek zamanlı işlemenin gerektirdiği katı zamanlamayı sağlar:

```c
// RT thread yapılandırması
struct sched_param param;
param.sched_priority = 88;  // Yüksek öncelik
pthread_setschedparam(pthread_self(), SCHED_FIFO, &param);

// CPU affinity
cpu_set_t cpuset;
CPU_ZERO(&cpuset);
CPU_SET(2, &cpuset);  // Çekirdek 2'ye ata
pthread_setaffinity_np(pthread_self(), sizeof(cpuset), &cpuset);
```

##### Buffer Yönetimi

PipeWire buffer yönetimi esnek ve yapılandırılabilir:

```
Buffer Akışı:
┌─────────────────────────────────────────────┐
│  Port 0 ──┐                                │
│  Port 1 ──┼→ [Mix] → [Process] → [Split]  │
│  Port 2 ──┘         ↓                      │
│                  [DSP Chain]                │
└─────────────────────────────────────────────┘
```

**Buffer Parametreleri**:
| Parametre | Varsayılan | Açıklama |
|-----------|------------|----------|
| `SPA_PARAM_BUFFERS_size` | 1024 | Buffer boyutu |
| `SPA_PARAM_BUFFERS_blocks` | 1 | Blok sayısı |
| `SPA_PARAM_BUFFERS_stride` | 4 | Byte stride |
| `SPA_PARAM_BUFFERS_align` | 16 | Bellek hizalama |

###/format Uyumluluğu

PipeWire, otomatik format dönüşümü yapar:

```
Kaynak Format → PipeWire → Hedef Format
─────────────────────────────────────────
S16LE Stereo  →  otomatik → F32LE 5.1
F32LE 48kHz   →  otomatik → S24LE 96kHz
```

**Desteklenen Formatlar**:
- `SPA_AUDIO_FORMAT_S16LE`
- `SPA_AUDIO_FORMAT_S24LE`
- `SPA_AUDIO_FORMAT_S32LE`
- `SPA_AUDIO_FORMAT_F32LE`
- `SPA_AUDIO_FORMAT_F64LE`

##### Port ve Donanım Yönetimi

PipeWire donanımı otomatik keşfeder:

```c
// Cihaz dinleme
struct spa_hook device_listener;
spa_device_add_listener(device, &device_listener,
                        &device_events, /*data*/);

// Cihaz değişikliklerini işle
static void on_device_info(void *data, 
                           const struct spa_device_info *info) {
    // Yeni cihaz bulundu
    // COREMUSIC tarafından ele alınır
}
```

## Aktarılmayan Kaynak Bölümleri

| Kaynak dosya | Satır aralığı | Not |
|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | L211-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | L290-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | L164-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | L253-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | CoreAudio macOS Sürücüsü | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | Teknik Detaylar | L14-L210 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | PipeWire Modern Sürücü | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | Teknik Detaylar | L14-L163 | ✓ verbatim |

## Belirsizlik Taraması (kaynak metin)

- Kaynaklarda `UNKNOWN` / `TODO` / `Belirlenecek` / `VERIFICATION REQUIRED` içeren satır **tespit edilmedi** (tam tarama).

## İlgili Dosyalar

[[index.md]] · [[asio-ve-wasapi.md]] · [[../k016-linux-ses/index]] · [[../k038-core-audio-macos/index]] · [[../k002-macos-tasinabilirlik/index]]

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault yedeği (`_backup/arch-2026-10-06_1057/architecture/k2-surucu/`) kanıtına dayanır; diskteki uygulama kodu ile çapraz doğrulama yapılmamıştır.
2. ⚠️ VERIFICATION REQUIRED — kaynak frontmatter `date: 2026-09-20` / `layer: K2` değerleri ile bu dosyanın `updated: 2026-10-06` değeri arasındaki fark üst merci onayı bekler.
