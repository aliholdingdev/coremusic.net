---
title: "Driver Stack Mimarisi"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# Driver Stack Mimarisi

## Genel Bakış

COREMUSIC Driver Stack, çok katmanlı bir soyutlama katmanı ile farklı platformlarda (Windows, Linux, macOS) tutarlı bir arayüz sağlar. HAL (Hardware Abstraction Layer) sayesinde üst katmanlar donanım detaylarından bağımsız çalışır.

## Teknik Detaylar

### Çok Katmanlı Yapı

```
┌─────────────────────────────────────────────────────┐
│                  K3 Neva Engine                      │
│              (Platform Bağımsız)                    │
├─────────────────────────────────────────────────────┤
│                  K2 HAL Interface                    │
│         ┌──────────┬──────────┬──────────┐          │
│         │ Playback │ Capture  │ Control  │          │
│         └──────────┴──────────┴──────────┘          │
├─────────────────────────────────────────────────────┤
│              K2 Driver Abstraction                   │
│  ┌──────────┬──────────┬──────────┬──────────┐      │
│  │   ASIO   │  WASAPI  │   ALSA   │ CoreAudio│      │
│  └──────────┴──────────┴──────────┴──────────┘      │
├─────────────────────────────────────────────────────┤
│              K2 Platform Specific                    │
│  ┌──────────┬──────────┬──────────┬──────────┐      │
│  │ Windows  │  Linux   │  macOS   │ Bluetooth│      │
│  └──────────┴──────────┴──────────┴──────────┘      │
├─────────────────────────────────────────────────────┤
│              K1 OS/Kernel                            │
│  ┌──────────┬──────────┬──────────┬──────────┐      │
│  │  WDM     │  ALSA    │  IOKit   │  HCI     │      │
│  └──────────┴──────────┴──────────┴──────────┘      │
├─────────────────────────────────────────────────────┤
│              K0 Hardware                             │
│  ┌──────────┬──────────┬──────────┬──────────┐      │
│  │   DAC    │   ADC    │   DSP    │   USB    │      │
│  └──────────┴──────────┴──────────┴──────────┘      │
└─────────────────────────────────────────────────────┘
```

### HAL Interface Tanımı

```cpp
// Ana HAL arayüzü
class IAudioHAL {
public:
    virtual ~IAudioHAL() = default;
    
    // Başlatma/kapatma
    virtual bool initialize(const AudioConfig& config) = 0;
    virtual void shutdown() = 0;
    
    // Playback
    virtual bool startPlayback() = 0;
    virtual bool stopPlayback() = 0;
    virtual ssize_t write(const float* buffer, 
                          size_t frames) = 0;
    
    // Capture
    virtual bool startCapture() = 0;
    virtual bool stopCapture() = 0;
    virtual ssize_t read(float* buffer, size_t frames) = 0;
    
    // Control
    virtual bool setSampleRate(uint32_t rate) = 0;
    virtual bool setBufferSize(uint32_t frames) = 0;
    virtual bool setChannelCount(uint32_t channels) = 0;
    
    // Durum
    virtual double getLatency() const = 0;
    virtual bool isRunning() const = 0;
    virtual AudioDeviceInfo getDeviceInfo() const = 0;
};

// Platform-specific implementasyonlar
class ASIODriver : public IAudioHAL { /* ... */ };
class WASAPIDriver : public IAudioHAL { /* ... */ };
class ALSADriver : public IAudioHAL { /* ... */ };
class CoreAudioDriver : public IAudioHAL { /* ... */ };
```

### Driver Factory Pattern

```cpp
// Driver oluşturma fabrikası
class AudioDriverFactory {
public:
    static std::unique_ptr<IAudioHAL> create(
        AudioDriverType type,
        const AudioConfig& config) {
        
        switch (type) {
            case DRIVER_TYPE_ASIO:
                return std::make_unique<ASIODriver>(config);
            case DRIVER_TYPE_WASAPI:
                return std::make_unique<WASAPIDriver>(config);
            case DRIVER_TYPE_ALSA:
                return std::make_unique<ALSADriver>(config);
            case DRIVER_TYPE_COREAUDIO:
                return std::make_unique<CoreAudioDriver>(config);
            default:
                return nullptr;
        }
    }
    
    // Otomatik algılama
    static std::unique_ptr<IAudioHAL> createAutoDetect(
        const AudioConfig& config) {
        
        #ifdef _WIN32
            // ASIO tercihli, WASAPI fallback
            if (isASIOAvailable())
                return create(DRIVER_TYPE_ASIO, config);
            return create(DRIVER_TYPE_WASAPI, config);
        #elif __linux__
            // PipeWire tercihli, ALSA fallback
            if (isPipeWireAvailable())
                return create(DRIVER_TYPE_PIPEWIRE, config);
            return create(DRIVER_TYPE_ALSA, config);
        #elif __APPLE__
            return create(DRIVER_TYPE_COREAUDIO, config);
        #endif
    }
};
```

### Katmanlı Soyutlama

Her katman belirli sorumluluklara sahiptir:

| Katman | Sorumluluk | Değişim Sıklığı |
|--------|------------|-----------------|
| K2 HAL | Platform bağımsız arayüz | Nadiren |
| K2 Driver | Platform-specific implementasyon | Platform değiştiğinde |
| K2 Platform | OS-specific entegrasyon | OS değiştiğinde |
| K1 OS | Çekirdek servisleri | Nadiren |
| K0 HW | Donanım erişimi | Donanım değiştiğinde |

### DriverLifecycle

```cpp
// Driver yaşam döngüsü
class DriverLifecycle {
public:
    enum class State {
        CREATED,
        INITIALIZED,
        CONFIGURED,
        RUNNING,
        PAUSED,
        ERROR,
        DESTROYED
    };
    
    State getState() const { return state; }
    
    bool transitionTo(State newState) {
        if (!isValidTransition(state, newState)) {
            logError("Invalid state transition");
            return false;
        }
        
        switch (newState) {
            case State::INITIALIZED:
                return initialize();
            case State::CONFIGURED:
                return configure();
            case State::RUNNING:
                return start();
            case State::PAUSED:
                return pause();
            case State::ERROR:
                return handleError();
            case State::DESTROYED:
                return destroy();
            default:
                return false;
        }
    }
    
private:
    State state = State::CREATED;
    
    bool isValidTransition(State from, State to) {
        // Geçerli geçişleri tanımla
        static const std::map<State, std::vector<State>> transitions = {
            {State::CREATED, {State::INITIALIZED}},
            {State::INITIALIZED, {State::CONFIGURED, State::ERROR}},
            {State::CONFIGURED, {State::RUNNING, State::ERROR}},
            {State::RUNNING, {State::PAUSED, State::ERROR, State::DESTROYED}},
            {State::PAUSED, {State::RUNNING, State::DESTROYED}},
            {State::ERROR, {State::INITIALIZED, State::DESTROYED}},
        };
        
        auto it = transitions.find(from);
        if (it == transitions.end()) return false;
        return std::find(it->second.begin(), it->second.end(), to) 
               != it->second.end();
    }
};
```

### Hata Yönetimi

Katmanlı hata yönetimi:

```cpp
// Hata zinciri
class ErrorChain {
public:
    void reportError(int layer, int code, const std::string& msg) {
        ErrorEntry entry;
        entry.layer = layer;
        entry.code = code;
        entry.message = msg;
        entry.timestamp = getCurrentTime();
        
        errors.push_back(entry);
        
        // Üst katmana bildir
        notifyUpperLayer(entry);
        
        // Retry stratejisi
        if (shouldRetry(code)) {
            retryOperation(layer, code);
        }
    }
    
    // Hata kodları
    enum ErrorCode {
        ERR_DEVICE_NOT_FOUND = 1001,
        ERR_BUFFER_OVERFLOW = 1002,
        ERR_FORMAT_NOT_SUPPORTED = 1003,
        ERR_PERMISSION_DENIED = 1004,
        ERR_HARDWARE_FAILURE = 1005,
        ERR_TIMEOUT = 1006,
    };
    
private:
    std::vector<ErrorEntry> errors;
    
    void notifyUpperLayer(const ErrorEntry& entry) {
        // K3'e hata bildirimi
    }
    
    bool shouldRetry(int code) {
        return code == ERR_TIMEOUT || 
               code == ERR_BUFFER_OVERFLOW;
    }
};
```

### Plugin Sistemi

Yeni sürücüler eklenebilir plugin yapısı:

```cpp
// Plugin arayüzü
class IDriverPlugin {
public:
    virtual ~IDriverPlugin() = default;
    virtual const char* getName() const = 0;
    virtual const char* getVersion() const = 0;
    virtual std::unique_ptr<IAudioHAL> create(
        const AudioConfig& config) = 0;
};

// Plugin kaydı
class PluginRegistry {
public:
    static void registerPlugin(std::shared_ptr<IDriverPlugin> plugin) {
        plugins[plugin->getName()] = plugin;
    }
    
    static std::unique_ptr<IAudioHAL> create(
        const char* name, 
        const AudioConfig& config) {
        
        auto it = plugins.find(name);
        if (it != plugins.end()) {
            return it->second->create(config);
        }
        return nullptr;
    }
    
private:
    static std::map<std::string, 
                    std::shared_ptr<IDriverPlugin>> plugins;
};
```

## API / Arayüz

```cpp
// Üst katman kullanımı
class AudioSystem {
public:
    bool initialize(const AudioConfig& config) {
        // Otomatik driver seçimi
        driver = AudioDriverFactory::createAutoDetect(config);
        if (!driver) return false;
        
        // Başlatma
        return driver->initialize(config);
    }
    
    bool start() {
        if (!driver) return false;
        return driver->startPlayback();
    }
    
    ssize_t process(const float* input, float* output, 
                    size_t frames) {
        // Input oku
        driver->read(inputBuffer, frames);
        
        // K3'te işle
        engine->process(inputBuffer, outputBuffer, frames);
        
        // Output yaz
        return driver->write(outputBuffer, frames);
    }
    
private:
    std::unique_ptr<IAudioHAL> driver;
    std::unique_ptr<NevaEngine> engine;
};

// Kullanım
AudioSystem system;
AudioConfig config;
config.sampleRate = 96000;
config.bufferSize = 256;
config.channels = 2;

system.initialize(config);
system.start();

// İşleme döngüsü
while (running) {
    system.process(input, output, 256);
}
```

## Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| Soyutlama Overhead | < 0.01ms |
| Driver Değişim Süresi | < 10ms |
| Hata Kurtarma | < 100ms |
| Bellek Kullanımı | < 10MB |
| CPU Overhead | < 0.5% |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Tüm K2 sürücüleri | İç |
| K1 OS Interface | İç |
| K3 Engine | İç |

## Durum: Implementasyon

- **Faz 1**: HAL interface tanımları
- **Faz 2**: Driver factory implementasyonu
- **Faz 3**: Error chain ve retry mekanizması
- **Faz 4**: Plugin sistemi
- **Tahmini Süre**: 2 hafta (80 adam-saat)
