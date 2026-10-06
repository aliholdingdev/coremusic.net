---
title: "Core Audio Entegrasyonu - k021-macos-core"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Core Audio Entegrasyonu

> Klasör: `k021-macos-core` · Dilim: D01 (k018–k035) · Dosya: `core-audio-macos.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Core Audio üzerinden cihaz yönetimi, akış ve performans metrikleri.

Bu belge; D01 diliminin (macOS Çekirdeği) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Core Audio üzerinden cihaz yönetimi, akış ve performans metrikleri.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (macOS Çekirdeği)
- **Çapraz referanslar:** [[../k029-cross-platform-api/cross-platform-api.md]] · [[../k034-usb-audio/usb-hotplug-enumerasyon.md]] · [[../k033-platform-ses-suruculeri/asio-drivers.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### core-audio-macos.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` (290 satır)

#### core-audio-macos.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § (giriş) — L1–L9

---
title: "CoreAudio macOS Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### CoreAudio macOS Sürücüsü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Genel Bakış` — L10–L12


CoreAudio, Apple'ın macOS ve iOS için geliştirdiği kapsamlı ses altyapısıdır. COREMUSIC, CoreAudio'nun AudioUnit ve HAL (Hardware Abstraction Layer) katmanlarını kullanarak Apple platformlarında profesyonel ses desteği sağlar.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Teknik Detaylar` — L14–L209


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `API / Arayüz` — L211–L264


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Performans Metrikleri` — L266–L274


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Bağımlılıklar` — L276–L282


| Bağımlılık | Tür |
|------------|-----|
| CoreAudio.framework | Apple framework |
| AudioToolbox.framework | Apple framework |
| K1 macOS Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Durum: Implementasyon` — L284–L290


- **Faz 1**: CoreAudio SDK entegrasyonu, temel cihaz yönetimi
- **Faz 2**: AudioUnit implementasyonu
- **Faz 3**: Render callback, zamanlama
- **Faz 4**: Çoklu cihaz desteği
- **Tahmini Süre**: 3 hafta (120 adam-saat)

### macos-core.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` (557 satır)

#### macos-core.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` § (giriş) — L1–L11

---
title: "macOS Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "macOS"
date: 2026-09-20
version: 1.0.1
---

### macOS Core


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` § `Teknik Detaylar` — L16–L455


##### 1. Grand Central Dispatch (GCD)

GCD, Apple'ın yüksek performanslı çoklu çekirdek yönetim sistemidir:

```c
#import <dispatch/dispatch.h>

// Serial queue ile ses işleme sırası koruma
dispatch_queue_t audioQueue = dispatch_queue_create(
    "com.coremusic.audioProcessing",
    DISPATCH_QUEUE_SERIAL);

// Concurrent queue ile paralel işleme
dispatch_queue_t concurrentQueue = dispatch_queue_create(
    "com.coremusic.parallelProcessing",
    DISPATCH_QUEUE_CONCURRENT);

// Async dispatch ile ses buffer işleme
void processAudioBuffer(AudioBuffer *buffer) {
    dispatch_async(audioQueue, ^{
        // Ses verisini işle
        ProcessAudioData(buffer);

        // Sonucu main queue'a gönder
        dispatch_async(dispatch_get_main_queue(), ^{
            UpdateUI(buffer);
        });
    });
}

// Dispatch group ile senkronizasyon
dispatch_group_t group = dispatch_group_create();

for (int i = 0; i < bufferCount; i++) {
    dispatch_group_async(group, concurrentQueue, ^{
        ProcessBuffer(buffers[i]);
    });
}

// Tüm buffer'lar işlendikten sonra bekle
dispatch_group_notify(group, dispatch_get_main_queue(), ^{
    AllBuffersProcessed();
});

// Dispatch semaphore ile kontrol
dispatch_semaphore_t semaphore = dispatch_semaphore_create(0);

dispatch_async(concurrentQueue, ^{
    HeavyProcessing();
    dispatch_semaphore_signal(semaphore);
});

dispatch_semaphore_wait(semaphore, DISPATCH_TIME_FOREVER);
```

##### 2. XPC Communication

XPC, macOS için güvenli process arası iletişim:

```c
#import <xpc/xpc.h>

// XPC sunucu oluşturma
void setup_xpc_server() {
    xpc_connection_t listener = xpc_connection_create_mach_service(
        "com.coremusic.audioService",
        dispatch_get_main_queue(),
        XPC_CONNECTION_MACH_SERVICE_LISTENER);

    xpc_connection_set_event_handler(listener, ^(xpc_connection_t connection) {
        xpc_connection_set_event_handler(connection, ^(xpc_object_t event) {
            // XPC mesajını işle
            xpc_type_t type = xpc_get_type(event);
            if (type == XPC_TYPE_DICTIONARY) {
                const char *command = xpc_dictionary_get_string(event, "command");
                if (strcmp(command, "processAudio") == 0) {
                    // Audio işleme isteği
                    xpc_object_t reply = xpc_dictionary_create_reply(event);
                    xpc_dictionary_set_bool(reply, "success", true);
                    xpc_connection_send_message(connection, reply);
                    xpc_release(reply);
                }
            }
        });
        xpc_connection_resume(connection);
    });

    xpc_connection_resume(listener);
}

// XPC client iletişimi
void send_audio_data_to_service(const void *data, size_t length) {
    xpc_connection_t connection = xpc_connection_create(
        "com.coremusic.audioService",
        dispatch_get_main_queue());

    xpc_connection_set_event_handler(connection, ^(xpc_object_t event) {
        // Yanıtı işle
    });

    xpc_connection_resume(connection);

    xpc_object_t message = xpc_dictionary_create(NULL, NULL, 0);
    xpc_dictionary_set_string(message, "command", "processAudio");
    xpc_dictionary_set_data(message, "audioData", data, length);
    xpc_dictionary_set_uint64(message, "sampleRate", 44100);
    xpc_dictionary_set_uint64(message, "channels", 2);

    xpc_connection_send_message(connection, message);
    xpc_release(message);
}
```

##### 3. Core Foundation

Core Foundation, temel veri tipleri ve utilities için:

```c
#import <CoreFoundation/CoreFoundation.h>

// String yönetimi
CFStringRef createAudioDeviceName(int deviceIndex) {
    return CFStringCreateWithFormat(NULL, NULL,
        CFSTR("AudioDevice_%d"), deviceIndex);
}

// Dictionary ile yapılandırma
CFMutableDictionaryRef createAudioConfig() {
    CFMutableDictionaryRef config = CFDictionaryCreateMutable(
        NULL, 0,
        &kCFTypeDictionaryKeyCallBacks,
        &kCFTypeDictionaryValueCallBacks);

    CFDictionaryAddValue(config, CFSTR("SampleRate"),
        CFNumberCreate(NULL, kCFNumberIntType, &(int){44100}));
    CFDictionaryAddValue(config, CFSTR("Channels"),
        CFNumberCreate(NULL, kCFNumberIntType, &(int){2}));
    CFDictionaryAddValue(config, CFSTR("BufferSize"),
        CFNumberCreate(NULL, kCFNumberIntType, &(int){512}));

    return config;
}

// Array ile ses cihazlarını saklama
CFMutableArrayRef getAudioDevices() {
    CFMutableArrayRef devices = CFArrayCreateMutable(NULL, 0, &kCFTypeArrayCallBacks);

    // AudioDeviceID'leri al
    AudioObjectPropertyAddress property = {
        kAudioHardwarePropertyDevices,
        kAudioObjectPropertyScopeGlobal,
        kAudioObjectPropertyElementMaster
    };

    UInt32 dataSize = 0;
    AudioObjectGetPropertyDataSize(kAudioObjectSystemObject, &property,
        0, NULL, &dataSize);

    int deviceCount = dataSize / sizeof(AudioDeviceID);
    AudioDeviceID *deviceIDs = malloc(dataSize);

    AudioObjectGetPropertyData(kAudioObjectSystemObject, &property,
        0, NULL, &dataSize, deviceIDs);

    for (int i = 0; i < deviceCount; i++) {
        CFNumberRef num = CFNumberCreate(NULL, kCFNumberIntType, &deviceIDs[i]);
        CFArrayAppendValue(devices, num);
        CFRelease(num);
    }

    free(deviceIDs);
    return devices;
}

// Timer ile periyodik görevler
CFRunLoopTimerRef createAudioTimer() {
    CFRunLoopTimerContext context = {0, NULL, NULL, NULL, NULL};

    CFRunLoopTimerRef timer = CFRunLoopTimerCreate(
        NULL,
        CFAbsoluteTimeGetCurrent(),
        0.023,  // 23ms = ~44.1Hz
        0, 0,
        audioTimerCallback,
        &context);

    CFRunLoopAddTimer(CFRunLoopGetCurrent(), timer, kCFRunLoopDefaultMode);
    return timer;
}
```

##### 4. Metal GPU Hesaplama

Metal, GPU tabanlı ses işleme için:

```objc
#import <Metal/Metal.h>

@interface AudioGPUPProcessor : NSObject
@property (nonatomic, strong) id<MTLDevice> device;
@property (nonatomic, strong) id<MTLCommandQueue> commandQueue;
@property (nonatomic, strong) id<MTLComputePipelineState> fftPipeline;
@end

@implementation AudioGPUPProcessor

- (instancetype)init {
    self = [super init];
    if (self) {
        _device = MTLCreateSystemDefaultDevice();
        _commandQueue = [_device newCommandQueue];

        // FFT shader'ını yükle
        id<MTLLibrary> library = [_device newDefaultLibrary];
        id<MTLFunction> fftFunction = [library newFunctionWithName:@"fftProcessor"];
        _fftPipeline = [_device newComputePipelineStateWithFunction:fftFunction error:nil];
    }
    return self;
}

- (void)processAudioWithGPU:(AudioBufferList *)bufferList {
    id<MTLCommandBuffer> commandBuffer = [_commandQueue commandBuffer];
    id<MTLComputeCommandEncoder> encoder = [commandBuffer computeCommandEncoder];

    [encoder setComputePipelineState:_fftPipeline];

    // Audio buffer'ları GPU'ya yükle
    for (int i = 0; i < bufferList->mNumberBuffers; i++) {
        AudioBuffer buffer = bufferList->mBuffers[i];
        id<MTLBuffer> gpuBuffer = [_device newBufferWithBytes:buffer.mData
                                                       length:buffer.mDataByteSize
                                                      options:MTLResourceStorageModeShared];
        [encoder setBuffer:gpuBuffer offset:0 atIndex:i];
    }

    // Thread group boyutunu ayarla
    MTLSize gridSize = MTLSizeMake(bufferList->mBuffers[0].mDataByteSize / sizeof(float), 1, 1);
    MTLSize threadGroupSize = MTLSizeMake(_fftPipeline.maxTotalThreadsPerThreadgroup, 1, 1);

    [encoder dispatchThreads:gridSize threadsPerThreadgroup:threadGroupSize];
    [encoder endEncoding];

    [commandBuffer commit];
    [commandBuffer waitUntilCompleted];
}

@end
```

##### 5. IOKit - Donanım Erişimi

IOKit, düşük seviyeli donanım erişimi için:

```c
#import <IOKit/IOKitLib.h>

// Ses cihazı keşfi
void discoverAudioDevices() {
    kern_return_t kr;
    io_iterator_t iterator;
    io_object_t service;

    // Tüm ses cihazlarını bul
    kr = IOServiceGetMatchingServices(kIOMasterPortDefault,
        IOServiceMatching("IOAudioDevice"), &iterator);

    if (kr != KERN_SUCCESS) return;

    while ((service = IOIteratorNext(iterator))) {
        CFMutableDictionaryRef properties;
        kr = IORegistryEntryCreateCFProperties(service, &properties,
            kCFAllocatorDefault, kNilOptions);

        if (kr == KERN_SUCCESS) {
            CFStringRef deviceName;
            CFDictionaryGetValueIfPresent(properties, CFSTR("Product"),
                (const void **)&deviceName);

            char name[256];
            CFStringGetCString(deviceName, name, sizeof(name),
                kCFStringEncodingUTF8);
            printf("Audio Device: %s\n", name);

            CFRelease(properties);
        }
        IOObjectRelease(service);
    }

    IOObjectRelease(iterator);
}

// USB ses cihazı hot-plug izleme
void setup_usb_hotplug() {
    io_iterator_t iterator;
    IOServiceAddMatchingNotification(
        kIOMasterPortDefault,
        kIOFirstMatchNotification,
        IOServiceMatching("IOUSBDevice"),
        usb_device_matched,
        NULL,
        &iterator);

    IOServiceAddMatchingNotification(
        kIOMasterPortDefault,
        kIOTerminatedNotification,
        IOServiceMatching("IOUSBDevice"),
        usb_device_terminated,
        NULL,
        &iterator);
}

void usb_device_matched(void *refCon, io_iterator_t iterator) {
    io_object_t service;
    while ((service = IOIteratorNext(iterator))) {
        printf("USB Audio Device Connected\n");
        IOObjectRelease(service);
    }
}
```

##### 6. LaunchAgent / LaunchDaemon

macOS servis yönetimi için launchd entegrasyonu:

```c
// LaunchAgent plist oluşturma
void create_launch_agent() {
    NSString *plist = @"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
        "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" "
        "\"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n"
        "<plist version=\"1.0\">\n"
        "<dict>\n"
        "    <key>Label</key>\n"
        "    <string>com.coremusic.audioservice</string>\n"
        "    <key>ProgramArguments</key>\n"
        "    <array>\n"
        "        <string>/usr/local/bin/coremusic</string>\n"
        "        <string>--daemon</string>\n"
        "    </array>\n"
        "    <key>RunAtLoad</key>\n"
        "    <true/>\n"
        "    <key>KeepAlive</key>\n"
        "    <true/>\n"
        "    <key>StandardOutPath</key>\n"
        "    <string>/var/log/coremusic.log</string>\n"
        "    <key>StandardErrorPath</key>\n"
        "    <string>/var/log/coremusic.err</string>\n"
        "    <key>ProcessType</key>\n"
        "    <string>Background</string>\n"
        "    <key>Nice</key>\n"
        "    <integer>-10</integer>\n"
        "</dict>\n"
        "</plist>";

    NSString *path = [NSHomeDirectory() stringByAppendingPathComponent:
        @"Library/LaunchAgents/com.coremusic.audioservice.plist"];
    [plist writeToFile:path atomically:YES encoding:NSUTF8StringEncoding error:nil];
}

// launchctl ile servis kontrolü
void control_service(const char *action) {
    char command[256];
    snprintf(command, sizeof(command), "launchctl %s com.coremusic.audioservice", action);
    system(command);
}
```

##### 7. App Sandbox

App Sandbox, uygulama izolasyonu için güvenlik mekanizması:

```xml
<!-- App Sandbox entitlements -->
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN"
  "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>com.apple.security.app-sandbox</key>
    <true/>

    <!-- Ses erişimi -->
    <key>com.apple.security.device.audio-input</key>
    <true/>

    <!-- Dosya erişimi -->
    <key>com.apple.security.files.user-selected.read-write</key>
    <true/>

    <!-- Ağ erişimi -->
    <key>com.apple.security.network.client</key>
    <true/>

    <!-- Eşzamanlı Processing -->
    <key>com.apple.security.processing</key>
    <true/>

    <!-- Musical Kit -->
    <key>com.apple.security.personal-information.music-library</key>
    <true/>
</dict>
</plist>
```

##### 8. Hardened Runtime

Hardened Runtime, uygulama güvenliğini sağlamak için:

```objc
// Hardened Runtime ile code signing
void setup_hardened_runtime() {
    //itlements ekle
    NSDictionary *entitlements = @{
        @"com.apple.security.cs.allow-jit": @YES,
        @"com.apple.security.cs.allow-unsigned-executable-memory": @YES,
        @"com.apple.security.cs.disable-library-validation": @YES,
        @"com.apple.security.device.audio-input": @YES,
        @"com.apple.security.network.client": @YES,
    };

    // notary ile doğrulama
    NSLog(@"Hardened Runtime aktif");
}

// Runtime check
void check_hardened_runtime() {
    // dyld diagnostic info
    const char *dyldInfo = _dyld_get_all_image_infos();
    if (dyldInfo) {
        NSLog(@"Runtime bilgisi alındı");
    }

    // Library validation kontrolü
    if (amfi_check_dyld_policy_self(DYLD_LIBRARY_VALIDATION_POLICY)) {
        NSLog(@"Library validation başarılı");
    }
}
```

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - macOS 12.0 Monterey ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L510 |
| - Xcode 14 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L511 |
| - Metal 3 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L512 |
| - Core Audio (AudioToolbox, AudioUnit) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L515 |
| - Core MIDI | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L516 |
| - Audio HAL (Hardware Abstraction Layer) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L517 |
| - AVFoundation | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L518 |
| - Cross-Platform API soyutlama katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L521 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L522 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L523 |

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
| kAudioHardwarePropertyDefaultOutputDevice, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L191 |
| AudioDeviceID defaultDevice; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L196 |
| &dataSize, &defaultDevice); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L200 |
| CoreAudioDevice getDefaultOutputDevice() const; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L221 |
| CoreAudioDevice getDefaultInputDevice() const; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L222 |
| auto outputDevice = driver.getDefaultOutputDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L254 |
| // Serial queue ile ses işleme sırası koruma | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L25 |
| CFRunLoopAddTimer(CFRunLoopGetCurrent(), timer, kCFRunLoopDefaultMode); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L204 |
| _device = MTLCreateSystemDefaultDevice(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L227 |
| id<MTLLibrary> library = [_device newDefaultLibrary]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L231 |
| _fftPipeline = [_device newComputePipelineStateWithFunction:fftFunction error:nil]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L233 |
| kr = IOServiceGetMatchingServices(kIOMasterPortDefault, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L281 |
| kCFAllocatorDefault, kNilOptions); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L289 |
| kIOMasterPortDefault, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L313 |
| kIOMasterPortDefault, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L321 |
| "    <key>StandardErrorPath</key>\n" | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L363 |
| [plist writeToFile:path atomically:YES encoding:NSUTF8StringEncoding error:nil]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L374 |
| 2. **Hardened Runtime**: Code signing ile runtime koruması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L538 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| AudioBufferList* ioData) { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L125 |
| float* buffer = (float*)ioData->mBuffers[0].mData; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L127 |
| engine->process(buffer, inNumberFrames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L130 |
| bool setBufferSize(uint32_t frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L227 |
| double getLatency() const; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L241 |
| std::pair<uint32_t, uint32_t> getBufferSizeRange( | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L246 |
| driver.setBufferSize(256); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L257 |
| driver.registerRenderCallback([](float* buffer, uint32_t frames) { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L259 |
| engine->process(buffer, frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L260 |
| // Async dispatch ile ses buffer işleme | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L35 |
| void processAudioBuffer(AudioBuffer *buffer) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L36 |
| ProcessAudioData(buffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L39 |
| UpdateUI(buffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L43 |
| for (int i = 0; i < bufferCount; i++) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L51 |
| ProcessBuffer(buffers[i]); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L53 |
| // Tüm buffer'lar işlendikten sonra bekle | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L57 |
| AllBuffersProcessed(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L59 |
| CFDictionaryAddValue(config, CFSTR("BufferSize"), | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L155 |
| 0.023,  // 23ms = ~44.1Hz | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L199 |
| - (void)processAudioWithGPU:(AudioBufferList *)bufferList { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L238 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| // Varsayılan çıkış cihazını al | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L189 |
| macOS Core modülü, COREMUSIC'ın Apple platformları için temel işletim sistemi işlevlerini sağlar. Bu modül, Grand Central Dispatch, XPC ve … | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L14 |
| ### 7. App Sandbox | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L385 |
| App Sandbox, uygulama izolasyonu için güvenlik mekanizması: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L387 |
| <!-- App Sandbox entitlements --> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L390 |
| <key>com.apple.security.app-sandbox</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L396 |
| <key>com.apple.security.device.audio-input</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L400 |
| <key>com.apple.security.files.user-selected.read-write</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L404 |
| <key>com.apple.security.network.client</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L408 |
| <key>com.apple.security.processing</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L412 |
| <key>com.apple.security.personal-information.music-library</key> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L416 |
| @"com.apple.security.cs.allow-jit": @YES, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L431 |
| @"com.apple.security.cs.allow-unsigned-executable-memory": @YES, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L432 |
| @"com.apple.security.cs.disable-library-validation": @YES, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L433 |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| // notary ile doğrulama | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L438 |
| 5. **Notary**: Uygulama doğrulama ve imzalama | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L541 |
| - GCD ile gerçek zamanlı ses işleme testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L556 |
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
| `CoreAudio` | 17 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L2 |
| `hotplug` | 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L310 |
| `kqueue` | 0 | [kaynakta eşleşme yok] |
| `latency` | 4 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` L241 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## Teknik Detaylar | L14–L209 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## API / Arayüz | L211–L264 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## Performans Metrikleri | L266–L274 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## Bağımlılıklar | L276–L282 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` | ## Durum: Implementasyon | L284–L290 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Teknik Detaylar | L16–L455 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## API / Arayüz | L457–L505 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Bağımlılıklar | L507–L523 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Performans Metrikleri | L525–L533 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Güvenlik Notları | L535–L541 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Durum: Implementasyon | L543–L557 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` § `Durum: Implementasyon` — L284–L290


- **Faz 1**: CoreAudio SDK entegrasyonu, temel cihaz yönetimi
- **Faz 2**: AudioUnit implementasyonu
- **Faz 3**: Render callback, zamanlama
- **Faz 4**: Çoklu cihaz desteği
- **Tahmini Süre**: 3 hafta (120 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` § `Durum: Implementasyon` — L543–L557


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Core Audio entegrasyonu ve ses cihazı yönetimi
2. GCD tabanlı çoklu iş parçacığı yönetimi
3. XPC ile güvenli process iletişimi
4. Metal GPU hesaplama implementasyonu
5. App Sandbox ve Hardened Runtime yapılandırması

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
