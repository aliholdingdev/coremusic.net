---
title: "macOS Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "macOS"
date: 2026-09-20
version: 1.0.0
---

# macOS Core

## Genel Bakış

macOS Core modülü, COREMUSIC'ın Apple platformları için temel işletim sistemi işlevlerini sağlar. Bu modül, Grand Central Dispatch, XPC ve macOS'un gelişmiş çoklu ortam teknolojilerini kullanarak gerçek zamanlı ses işleme için optimize edilmiş bir altyapı sunar. Metal GPU hesaplama, IOKit donanım erişimi ve App Store güvenlik gereksinimlerini destekler.

## Teknik Detaylar

### 1. Grand Central Dispatch (GCD)

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

### 2. XPC Communication

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

### 3. Core Foundation

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

### 4. Metal GPU Hesaplama

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

### 5. IOKit - Donanım Erişimi

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

### 6. LaunchAgent / LaunchDaemon

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

### 7. App Sandbox

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

### 8. Hardened Runtime

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

## API / Arayüz

### COREMUSIC macOS API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- macOS 12.0 Monterey ve üzeri
- Xcode 14 ve üzeri
- Metal 3 ve üzeri

### Alt Katmanlar
- Core Audio (AudioToolbox, AudioUnit)
- Core MIDI
- Audio HAL (Hardware Abstraction Layer)
- AVFoundation

### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| GCD dispatch latency | < 10μs | Belirlenecek |
| XPC message latency | < 50μs | Belirlenecek |
| Metal GPU processing | < 1ms | Belirlenecek |
| IOKit device discovery | < 100ms | Belirlenecek |
| LaunchAgent start | < 2s | Belirlenecek |

## Güvenlik Notları

1. **App Sandbox**: Tüm uygulamalar sandboxed olarak çalıştırılmalı
2. **Hardened Runtime**: Code signing ile runtime koruması
3. **XPC**: Güvenli process arası iletişim
4. **IOKit**:最小imal yetki ile donanım erişimi
5. **Notary**: Uygulama doğrulama ve imzalama

## Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Core Audio entegrasyonu ve ses cihazı yönetimi
2. GCD tabanlı çoklu iş parçacığı yönetimi
3. XPC ile güvenli process iletişimi
4. Metal GPU hesaplama implementasyonu
5. App Sandbox ve Hardened Runtime yapılandırması

**Sonraki Adımlar**:
- Core Audio callback-based audio processing implementasyonu
- GCD ile gerçek zamanlı ses işleme testleri
- XPC service communication implementasyonu
