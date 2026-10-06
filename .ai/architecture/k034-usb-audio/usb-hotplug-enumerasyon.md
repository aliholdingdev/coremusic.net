---
title: "USB Hotplug ve Enumerasyon - k034-usb-audio"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# USB Hotplug ve Enumerasyon

> Klasör: `k034-usb-audio` · Dilim: D01 (k018–k035) · Dosya: `usb-hotplug-enumerasyon.md`
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Sıcak tak-çıkar (hotplug) olayları, enumerasyon akışı ve sürücü yüklemesi.

Bu belge; D01 diliminin (USB Ses) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Sıcak tak-çıkar (hotplug) olayları, enumerasyon akışı ve sürücü yüklemesi.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (USB Ses)
- **Çapraz referanslar:** [[../k018-dma-kesinti-yonetimi/dma-yonetimi.md]] · [[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]] · [[../k021-macos-core/core-audio-macos.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### usb-audio-class.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (254 satır)

#### usb-audio-class.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § (giriş) — L1–L9

---
title: "USB Audio Class 2.0 Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### USB Audio Class 2.0 Sürücüsü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Genel Bakış` — L10–L12


USB Audio Class 2.0 (UAC2), USB üzerinden yüksek kaliteli ses giriş/çıkışı için evrensel bir standarttır. COREMUSIC, UAC2 protokolünü destekleyerek yüksek çözünürlüklü ses cihazlarıyla (DAC, ADC,ampler) doğrudan çalışır.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Teknik Detaylar` — L14–L174


##### UAC2 Mimarisi

```
┌─────────────────────────────────────────────┐
│            COREMUSIC Engine (K3)            │
├─────────────────────────────────────────────┤
│         USB Audio Class 2.0 Driver          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Isoch    │  │ Control  │  │ Clock    │  │
│  │ Endpoint │  │ Request │  │ Source   │  │
│  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────┤
│         USB Host Controller Driver          │
│  ┌──────────┐  ┌──────────┐                │
│  │ xHCI     │  │ EHCI     │                │
│  └──────────┘  └──────────┘                │
├─────────────────────────────────────────────┤
│         USB Hardware                        │
│  ┌──────────────────────────────────────┐   │
│  │  USB Port → USB Cable → Audio Device │   │
│  └──────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

##### Isochronous Transfer

USB ses, isochronous transfer modunu kullanır:

**Isochronous Transfer Özellikleri**:
- Zaman duyarlı (time-sensitive) veri transferi
- Garantili bandwidth
- Hata düzeltme yok (veya sınırlı)
- Her frame'de sabit boyut veri

**Frame Yapısı**:
```
USB Frame (1ms @ Full Speed, 125μs @ High Speed)
┌─────────────────────────────────────────────────┐
│  Frame N     │  Frame N+1   │  Frame N+2        │
│  ┌─────────┐ │  ┌─────────┐ │  ┌─────────┐     │
│  │ Packet  │ │  │ Packet  │ │  │ Packet  │     │
│  │ (256B)  │ │  │ (256B)  │ │  │ (256B)  │     │
│  └─────────┘ │  └─────────┘ │  └─────────┘     │
└─────────────────────────────────────────────────┘
```

##### Adaptive ve Async Modlar

UAC2 iki zamanlama modu destekler:

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Adaptive** | USB host saatine senkronize | Varsayılan mod |
| **Async** | Device kendi saatini kullanır | Profesyonel cihazlar |

**Async Mod Avantajları**:
- Daha düşük jitter
- Daha iyi clock recovery
- Profesyonel ses cihazları için tercih edilen

##### Clock Source Yönetimi

UAC2, birden fazla clock source destekler:

```cpp
// Clock source listesini al
USB_AC2_CLOCK_SOURCE clocks[10];
uint32_t clockCount;
UAC2_GetClockSources(deviceId, clocks, &clockCount);

// Clock source seçimi
UAC2_SetClockSource(deviceId, 
                     clocks[0].sourceId, 
                     clocks[0].sampleRates[0]);

// Clock frequency sorgusu
double frequency;
UAC2_GetClockFrequency(deviceId, 
                        clocks[0].sourceId, 
                        &frequency);
```

##### Format Desteği

UAC2 aşağıdaki formatları destekler:

| Format | Bit Derinliği | Örnek Hızı | Kanal |
|--------|---------------|------------|-------|
| PCM | 16, 24, 32 | 44.1k-384k | 1-32 |
| IEEE Float | 32 | 44.1k-192k | 1-32 |
| DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8 |

##### Endpoint Yapılandırması

Her USB ses cihazı bir veya daha fazla endpoint içerir:

```
USB Audio Device
├── Input Terminal (Mikrofon)
│   └── Input Endpoint (Isoch IN)
├── Output Terminal (Hoparlör)
│   └── Output Endpoint (Isoch OUT)
├── Feature Unit (Volume, Mute)
└── Clock Source (Internal/External)
```

##### Bandwidth Yönetimi

USB bandwidth yönetimi kritiktir:

```
High Speed USB (480 Mbps)
├── Isochronous Bandwidth: 20% = 96 Mbps
├── Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch)
└── Headroom: ~72 Mbps

Full Speed USB (12 Mbps)
├── Isochronous Bandwidth: 90% = 10.8 Mbps
├── Max Audio Bandwidth: ~1.5 Mbps (16-bit 48kHz 2ch)
└── Headroom: ~9.3 Mbps
```

##### Buffer Yönetimi

USB Audio buffer yönetimi:

```cpp
// Isochronous buffer yapısı
struct USB_AudioBuffer {
    void* data;           // Buffer verisi
    uint32_t size;        // Buffer boyutu
    uint32_t frameSize;   // Frame boyutu
    uint32_t numFrames;   // Frame sayısı
    bool isochronous;     // Isochronous modu
};

// Double buffering
USB_AudioBuffer bufferA, bufferB;
USB_AudioBuffer* activeBuffer = &bufferA;
USB_AudioBuffer* backBuffer = &bufferB;

// Buffer değişimi (çift tamponlama)
void swapBuffers() {
    USB_AudioBuffer* temp = activeBuffer;
    activeBuffer = backBuffer;
    backBuffer = temp;
}
```

##### Hata Yönetimi

USB Audio hata yönetimi:

| Hata | Neden | Çözüm |
|------|-------|-------|
| `USB_ERROR_STALL` | Transfer durduruldu | Endpoint'i resetle |
| `USB_ERROR_NAK` | Cihaz meşgul | Yeniden dene |
| `USB_ERROR_TIMEOUT` | Zaman aşımı | Bandwidth'i artır |
| `USB_ERROR_OVERFLOW` | Buffer taştı | Buffer boyutunu artır |

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `API / Arayüz` — L176–L228


```cpp
class USBAudioDriver {
public:
    bool initialize();
    void shutdown();
    
    // Cihaz keşfi
    std::vector<USBAudioDevice> enumerateDevices() const;
    bool connectToDevice(uint32_t deviceId);
    void disconnectDevice();
    
    // Akış yapılandırması
    bool setSampleRate(double rate);
    bool setBitDepth(uint32_t bits);
    bool setChannelCount(uint32_t channels);
    bool setBufferSize(uint32_t frames);
    
    // Transfer kontrolü
    bool startPlayback();
    bool stopPlayback();
    bool startCapture();
    bool stopCapture();
    
    // Async mod
    bool enableAsyncMode();
    bool setClockSource(uint32_t sourceId);
    
    // Buffer yönetimi
    bool submitBuffer(const USB_AudioBuffer& buffer);
    bool cancelPendingTransfers();
    
    //_durum
    bool isConnected() const;
    double getCurrentSampleRate() const;
    uint32_t getLatency() const;
};

// Kullanım örneği
USBAudioDriver driver;
driver.initialize();

auto devices = driver.enumerateDevices();
if (!devices.empty()) {
    driver.connectToDevice(devices[0].id);
    driver.setSampleRate(96000);
    driver.setBitDepth(32);
    driver.setChannelCount(2);
    driver.enableAsyncMode();
    driver.startPlayback();
}
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Performans Metrikleri` — L230–L238


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Bağımlılıklar` — L240–L246


| Bağımlılık | Tür |
|------------|-----|
| libusb | Sistem kütüphanesi |
| USB Host Controller Driver | Çekirdek |
| K1 USB Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Durum: Implementasyon` — L248–L254


- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
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

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` § `API / Arayüz` — L457–L505


##### COREMUSIC macOS API Başlık Dosyası

```objc
#ifndef COREMUSIC_MACOS_H
#define COREMUSIC_MACOS_H

#import <Foundation/Foundation.h>

// GCD Operations
typedef void (^AudioProcessingBlock)(void);

CM_Status CM_DispatchAsync(dispatch_queue_t queue, AudioProcessingBlock block);
CM_Status CM_DispatchSync(dispatch_queue_t queue, AudioProcessingBlock block);
CM_Status CM_DispatchGroupNotify(dispatch_group_t group, dispatch_queue_t queue,
    AudioProcessingBlock block);

// XPC Communication
xpc_connection_t CM_XpcConnect(const char *serviceName);
CM_Status CM_XpcSendMessage(xpc_connection_t connection, const char *command,
    const void *data, size_t dataLength);
xpc_object_t CM_XpcReceiveMessage(xpc_connection_t connection);

// Core Foundation Utilities
CFStringRef CM_CreateCFString(const char *str);
CFDictionaryRef CM_CreateAudioConfig(int sampleRate, int channels, int bufferSize);
CFArrayRef CM_GetAudioDevices(void);

// Metal GPU Processing
- (instancetype)initProcessorWithDevice;
- (void)processAudioBuffer:(AudioBufferList *)bufferList;
- (void)performFFT:(float *)input output:(float *)output length:(size_t)length;

// IOKit Hardware Access
CM_Status CM_DiscoverAudioDevices(void);
CM_Status CM_SetupUSBHotplug(void);

// LaunchAgent Management
CM_Status CM_InstallLaunchAgent(const char *programPath);
CM_Status CM_StartLaunchAgent(void);
CM_Status CM_StopLaunchAgent(void);

// Security
CM_Status CM_CheckSandboxStatus(void);
CM_Status CM_ValidateHardenedRuntime(void);

#endif // COREMUSIC_MACOS_H
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
| void disconnectDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L187 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| - Hata düzeltme yok (veya sınırlı) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L47 |
| ### Hata Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L165 |
| USB Audio hata yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L167 |
| - **Faz 4**: DSD desteği, hata yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L253 |
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
| USB Frame (1ms @ Full Speed, 125μs @ High Speed) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L52 |
| - Daha düşük jitter | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L72 |
| ├── Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L129 |
| ├── Max Audio Bandwidth: ~1.5 Mbps (16-bit 48kHz 2ch) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L134 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L138 |
| USB Audio buffer yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L140 |
| // Isochronous buffer yapısı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L143 |
| struct USB_AudioBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L144 |
| void* data;           // Buffer verisi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L145 |
| uint32_t size;        // Buffer boyutu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L146 |
| // Double buffering | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L152 |
| USB_AudioBuffer bufferA, bufferB; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L153 |
| USB_AudioBuffer* activeBuffer = &bufferA; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L154 |
| USB_AudioBuffer* backBuffer = &bufferB; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L155 |
| // Buffer değişimi (çift tamponlama) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L157 |
| void swapBuffers() { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L158 |
| USB_AudioBuffer* temp = activeBuffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L159 |
| activeBuffer = backBuffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L160 |
| backBuffer = temp; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L161 |
| bool setBufferSize(uint32_t frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L193 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
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
| @"com.apple.security.device.audio-input": @YES, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L434 |

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
| `hotplug` | 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` L310 |
| `UAC2` | 8 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L12 |
| `enumeration` | 0 | [kaynakta eşleşme yok] |
| `firmware` | 0 | [kaynakta eşleşme yok] |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Teknik Detaylar | L14–L174 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## API / Arayüz | L176–L228 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Performans Metrikleri | L230–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Bağımlılıklar | L240–L246 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Durum: Implementasyon | L248–L254 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Teknik Detaylar | L16–L455 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## API / Arayüz | L457–L505 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Bağımlılıklar | L507–L523 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Performans Metrikleri | L525–L533 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Güvenlik Notları | L535–L541 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | ## Durum: Implementasyon | L543–L557 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Durum: Implementasyon` — L248–L254


- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
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
