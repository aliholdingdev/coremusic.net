---
title: "K014 Driver Stack ve Buffer Mimarisi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K014 — Driver Stack ve Buffer Mimarisi

> **K numarası:** K014 · **Klasör:** `k014-surucu-yigin` · **Dosya:** `driver-stack-ve-buffer`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Uygulama ile donanım arasındaki sürücü yığınının katmanlarını, ring/period buffer yönetimini ve gecikme optimizasyonu şartlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Driver Stack ve Buffer Mimarisi** konusunu ele alır. Kapsamı: Katmanlı soyutlama, HAL arayüzü, driver factory/lifecycle, hata yönetimi ve plugin modeli.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Uygulama (DAW/API çağrısı) ]
            │
            ▼
      [ Soyutlama katmanı (HAL) ]
            │
            ▼
      [ Driver factory / lifecycle ]
            │
            ▼
      [ Platform sürücüsü ]
            │
            ▼
      [ Ring / period buffer ]
            │
            ▼
      [ Donanım (DMA · IRQ) ]
```

**Akış notları:**

1. **Uygulama (DAW/API çağrısı)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **Soyutlama katmanı (HAL)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Driver factory / lifecycle** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Platform sürücüsü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Ring / period buffer** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **Donanım (DMA · IRQ)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | 379 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 382 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/driver-stack-mimari.md`

| Katman | Sorumluluk | Değişim Sıklığı |
|--------|------------|-----------------|
| K2 HAL | Platform bağımsız arayüz | Nadiren |
| K2 Driver | Platform-specific implementasyon | Platform değiştiğinde |
| K2 Platform | OS-specific entegrasyon | OS değiştiğinde |
| K1 OS | Çekirdek servisleri | Nadiren |
| K0 HW | Donanım erişimi | Donanım değiştiğinde |

### 4.2 · `k2-surucu/driver-stack-mimari.md`

| Metrik | Hedef |
|--------|-------|
| Soyutlama Overhead | < 0.01ms |
| Driver Değişim Süresi | < 10ms |
| Hata Kurtarma | < 100ms |
| Bellek Kullanımı | < 10MB |
| CPU Overhead | < 0.5% |

### 4.3 · `k2-surucu/driver-stack-mimari.md`

| Bağımlılık | Tür |
|------------|-----|
| Tüm K2 sürücüleri | İç |
| K1 OS Interface | İç |
| K3 Engine | İç |

### 4.4 · `k2-surucu/buffer-management.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

### 4.5 · `k2-surucu/buffer-management.md`

| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/driver-stack-mimari.md` | H1 | Driver Stack Mimarisi |
| 2 | `k2-surucu/driver-stack-mimari.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/driver-stack-mimari.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/driver-stack-mimari.md` | H3 | Çok Katmanlı Yapı |
| 5 | `k2-surucu/driver-stack-mimari.md` | H3 | HAL Interface Tanımı |
| 6 | `k2-surucu/driver-stack-mimari.md` | H3 | Driver Factory Pattern |
| 7 | `k2-surucu/driver-stack-mimari.md` | H3 | Katmanlı Soyutlama |
| 8 | `k2-surucu/driver-stack-mimari.md` | H3 | DriverLifecycle |
| 9 | `k2-surucu/driver-stack-mimari.md` | H3 | Hata Yönetimi |
| 10 | `k2-surucu/driver-stack-mimari.md` | H3 | Plugin Sistemi |
| 11 | `k2-surucu/driver-stack-mimari.md` | H2 | API / Arayüz |
| 12 | `k2-surucu/driver-stack-mimari.md` | H2 | Performans Metrikleri |
| 13 | `k2-surucu/driver-stack-mimari.md` | H2 | Bağımlılıklar |
| 14 | `k2-surucu/driver-stack-mimari.md` | H2 | Durum: Implementasyon |
| 15 | `k2-surucu/buffer-management.md` | H1 | Buffer Yönetimi |
| 16 | `k2-surucu/buffer-management.md` | H2 | Genel Bakış |
| 17 | `k2-surucu/buffer-management.md` | H2 | Teknik Detaylar |
| 18 | `k2-surucu/buffer-management.md` | H3 | Ring Buffer Yapısı |
| 19 | `k2-surucu/buffer-management.md` | H3 | Lock-Free Ring Buffer |
| 20 | `k2-surucu/buffer-management.md` | H3 | Double Buffering |
| 21 | `k2-surucu/buffer-management.md` | H3 | SPSC Queue (Single Producer Single Consumer) |
| 22 | `k2-surucu/buffer-management.md` | H3 | Buffer Boyut Optimizasyonu |
| 23 | `k2-surucu/buffer-management.md` | H3 | Adapte Buffer Yönetimi |
| 24 | `k2-surucu/buffer-management.md` | H2 | API / Arayüz |
| 25 | `k2-surucu/buffer-management.md` | H2 | Performans Metrikleri |
| 26 | `k2-surucu/buffer-management.md` | H2 | Bağımlılıklar |
| 27 | `k2-surucu/buffer-management.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md`


### Driver Stack Mimarisi

#### Genel Bakış

COREMUSIC Driver Stack, çok katmanlı bir soyutlama katmanı ile farklı platformlarda (Windows, Linux, macOS) tutarlı bir arayüz sağlar. HAL (Hardware Abstraction Layer) sayesinde üst katmanlar donanım detaylarından bağımsız çalışır.

#### Teknik Detaylar

##### Çok Katmanlı Yapı

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

##### HAL Interface Tanımı

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

##### Driver Factory Pattern

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

##### Katmanlı Soyutlama

Her katman belirli sorumluluklara sahiptir:

| Katman | Sorumluluk | Değişim Sıklığı |
|--------|------------|-----------------|
| K2 HAL | Platform bağımsız arayüz | Nadiren |
| K2 Driver | Platform-specific implementasyon | Platform değiştiğinde |
| K2 Platform | OS-specific entegrasyon | OS değiştiğinde |
| K1 OS | Çekirdek servisleri | Nadiren |
| K0 HW | Donanım erişimi | Donanım değiştiğinde |

##### DriverLifecycle

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

##### Hata Yönetimi

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

##### Plugin Sistemi

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

#### API / Arayüz

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

#### Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| Soyutlama Overhead | < 0.01ms |
| Driver Değişim Süresi | < 10ms |
| Hata Kurtarma | < 100ms |
| Bellek Kullanımı | < 10MB |
| CPU Overhead | < 0.5% |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Tüm K2 sürücüleri | İç |
| K1 OS Interface | İç |
| K3 Engine | İç |

#### Durum: Implementasyon

- **Faz 1**: HAL interface tanımları
- **Faz 2**: Driver factory implementasyonu
- **Faz 3**: Error chain ve retry mekanizması
- **Faz 4**: Plugin sistemi
- **Tahmini Süre**: 2 hafta (80 adam-saat)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md`


### Buffer Yönetimi

#### Genel Bakış

COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double buffering ve lock-free queue'lar kullanılarak gerçek zamanlı performans elde edilir.

#### Teknik Detaylar

##### Ring Buffer Yapısı

```
Ring Buffer Yapısı:
┌─────────────────────────────────────────────────────┐
│                                                     │
│  ┌───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐       │
│  │ 0 │ 1 │ 2 │ 3 │ 4 │ 5 │ 6 │ 7 │ 8 │ 9 │       │
│  └───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘       │
│    ↑                                       ↑       │
│   Read Pointer                      Write Pointer  │
│                                                     │
│  Read Pointer: okunacak bir sonraki konum          │
│  Write Pointer: yazılacak bir sonraki konum        │
│                                                     │
└─────────────────────────────────────────────────────┘
```

##### Lock-Free Ring Buffer

```cpp
// Lock-free ring buffer implementasyonu
template<typename T>
class LockFreeRingBuffer {
public:
    LockFreeRingBuffer(size_t capacity) 
        : capacity(capacity), buffer(new T[capacity]),
          readIndex(0), writeIndex(0) {}
    
    // Yazma (tek producer)
    bool write(const T* data, size_t count) {
        size_t currentWrite = writeIndex.load(std::memory_order_relaxed);
        size_t currentRead = readIndex.load(std::memory_order_acquire);
        
        size_t available = capacity - (currentWrite - currentRead);
        if (count > available) return false;
        
        // Veriyi kopyala
        for (size_t i = 0; i < count; i++) {
            buffer[(currentWrite + i) % capacity] = data[i];
        }
        
        writeIndex.store(currentWrite + count, 
                        std::memory_order_release);
        return true;
    }
    
    // Okuma (tek consumer)
    bool read(T* data, size_t count) {
        size_t currentRead = readIndex.load(std::memory_order_relaxed);
        size_t currentWrite = writeIndex.load(std::memory_order_acquire);
        
        size_t available = currentWrite - currentRead;
        if (count > available) return false;
        
        // Veriyi oku
        for (size_t i = 0; i < count; i++) {
            data[i] = buffer[(currentRead + i) % capacity];
        }
        
        readIndex.store(currentRead + count, 
                       std::memory_order_release);
        return true;
    }
    
    // Kullanılabilir veri miktarı
    size_t availableRead() const {
        return writeIndex.load(std::memory_order_acquire) - 
               readIndex.load(std::memory_order_acquire);
    }
    
    // Kullanılabilir alan miktarı
    size_t availableWrite() const {
        return capacity - availableRead();
    }
    
private:
    size_t capacity;
    std::unique_ptr<T[]> buffer;
    std::atomic<size_t> readIndex;
    std::atomic<size_t> writeIndex;
};
```

##### Double Buffering

Double buffering, kesintisiz ses akışı için kullanılır:

```
Double Buffering Akışı:
┌─────────────────────────────────────────────────────┐
│                                                     │
│  Zaman Dilimi 1:                                   │
│  ┌─────────────┐  ┌─────────────┐                  │
│  │  Buffer A    │  │  Buffer B    │                  │
│  │  (Okunuyor) │  │  (Yazılıyor)│                  │
│  └──────┬──────┘  └──────┬──────┘                  │
│         ↓                ↓                          │
│  [Donanım Input]  [Uygulama Output]                │
│                                                     │
│  Zaman Dilimi 2:                                   │
│  ┌─────────────┐  ┌─────────────┐                  │
│  │  Buffer A    │  │  Buffer B    │                  │
│  │  (Yazılıyor)│  │  (Okunuyor) │                  │
│  └──────┬──────┘  └──────┬──────┘                  │
│         ↓                ↓                          │
│  [Uygulama Output]  [Donanım Input]                │
│                                                     │
└─────────────────────────────────────────────────────┘
```

```cpp
// Double buffer implementasyonu
class DoubleBuffer {
public:
    DoubleBuffer(size_t frameSize, size_t channels)
        : bufferSize(frameSize * channels),
          bufferA(new float[bufferSize]),
          bufferB(new float[bufferSize]),
          activeBuffer(bufferA.get()),
          backBuffer(bufferB.get()) {}
    
    // Yazma (back buffer'a)
    void write(const float* data, size_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        size_t offset = writePos * channels;
        std::copy(data, data + frames * channels, 
                  backBuffer + offset);
        writePos += frames;
    }
    
    // Okuma (active buffer'dan)
    void read(float* data, size_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        size_t offset = readPos * channels;
        std::copy(activeBuffer + offset, 
                  activeBuffer + offset + frames * channels, 
                  data);
        readPos += frames;
    }
    
    // Buffer değişimi
    void swap() {
        std::lock_guard<std::mutex> lock(mutex);
        std::swap(activeBuffer, backBuffer);
        writePos = 0;
        readPos = 0;
    }
    
private:
    size_t bufferSize;
    size_t channels = 2;
    std::unique_ptr<float[]> bufferA;
    std::unique_ptr<float[]> bufferB;
    float* activeBuffer;
    float* backBuffer;
    size_t writePos = 0;
    size_t readPos = 0;
    std::mutex mutex;
};
```

##### SPSC Queue (Single Producer Single Consumer)

```cpp
// SPSC lock-free queue
template<typename T>
class SPSCQueue {
public:
    SPSCQueue(size_t capacity) 
        : capacity(capacity), buffer(new T[capacity]) {}
    
    bool push(const T& item) {
        size_t currentWrite = writePos.load(std::memory_order_relaxed);
        size_t nextWrite = (currentWrite + 1) % capacity;
        
        if (nextWrite == readPos.load(std::memory_order_acquire)) {
            return false; // Buffer dolu
        }
        
        buffer[currentWrite] = item;
        writePos.store(nextWrite, std::memory_order_release);
        return true;
    }
    
    bool pop(T& item) {
        size_t currentRead = readPos.load(std::memory_order_relaxed);
        
        if (currentRead == writePos.load(std::memory_order_acquire)) {
            return false; // Buffer boş
        }
        
        item = buffer[currentRead];
        readPos.store((currentRead + 1) % capacity, 
                     std::memory_order_release);
        return true;
    }
    
    size_t size() const {
        size_t w = writePos.load(std::memory_order_acquire);
        size_t r = readPos.load(std::memory_order_acquire);
        return (w - r + capacity) % capacity;
    }
    
private:
    size_t capacity;
    std::unique_ptr<T[]> buffer;
    std::atomic<size_t> writePos{0};
    std::atomic<size_t> readPos{0};
};
```

##### Buffer Boyut Optimizasyonu

```cpp
// Buffer boyutu hesaplama
struct BufferConfig {
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t targetLatencyMs;
    uint32_t minBufferSize;
    uint32_t maxBufferSize;
};

uint32_t calculateOptimalBufferSize(const BufferConfig& config) {
    // Minimum buffer boyutu
    uint32_t minFrames = (config.sampleRate * config.targetLatencyMs) 
                         / 1000;
    
    // Güvenlik payı (%20)
    uint32_t safeFrames = minFrames * 1.2;
    
    // 2'nin kuvvetine yuvarla (performans için)
    uint32_t optimal = 1;
    while (optimal < safeFrames) optimal <<= 1;
    
    // Sınır kontrolü
    optimal = std::max(optimal, config.minBufferSize);
    optimal = std::min(optimal, config.maxBufferSize);
    
    return optimal;
}
```

##### Adapte Buffer Yönetimi

```cpp
// Adaptif buffer
class AdaptiveBuffer {
public:
    AdaptiveBuffer(size_t initialSize) 
        : currentSize(initialSize), targetSize(initialSize) {}
    
    void adjust(double currentJitter, double targetLatency) {
        // Jitter istatistiğini güncelle
        jitterHistory.push_back(currentJitter);
        if (jitterHistory.size() > 100) {
            jitterHistory.erase(jitterHistory.begin());
        }
        
        // Ortalama jitter hesapla
        double avgJitter = std::accumulate(
            jitterHistory.begin(), 
            jitterHistory.end(), 0.0) / jitterHistory.size();
        
        // Buffer boyutunu ayarla
        targetSize = static_cast<size_t>(
            (avgJitter * 2 + targetLatency) * sampleRate / 1000);
        
        // Yumuşak geçiş
        if (targetSize > currentSize) {
            currentSize = std::min(currentSize + 1, targetSize);
        } else if (targetSize < currentSize) {
            currentSize = std::max(currentSize - 1, targetSize);
        }
    }
    
    size_t getCurrentSize() const { return currentSize; }
    
private:
    size_t currentSize;
    size_t targetSize;
    uint32_t sampleRate = 96000;
    std::vector<double> jitterHistory;
};
```

#### API / Arayüz

```cpp
class BufferManager {
public:
    BufferManager(const BufferConfig& config);
    
    // Ana buffer
    bool writeInput(const float* data, size_t frames);
    bool readOutput(float* data, size_t frames);
    void swapBuffers();
    
    // adapte
    void enableAdaptiveMode();
    void updateAdaptive(double jitter);
    
    // Ölçümler
    double getCurrentLatency() const;
    double getJitter() const;
    size_t getAvailableRead() const;
    size_t getAvailableWrite() const;
    
    // Bilgi
    BufferStats getStats() const;
};

struct BufferStats {
    size_t totalBuffers;
    size_t activeBuffers;
    double averageLatency;
    double maxJitter;
    uint64_t overflowCount;
    uint64_t underflowCount;
};

// Kullanım örneği
BufferConfig config;
config.sampleRate = 96000;
config.channels = 2;
config.targetLatencyMs = 1;
config.minBufferSize = 64;
config.maxBufferSize = 1024;

BufferManager bufferMgr(config);
bufferMgr.enableAdaptiveMode();

// İşleme döngüsü
while (running) {
    bufferMgr.writeInput(inputData, 256);
    bufferMgr.readOutput(outputData, 256);
    bufferMgr.swapBuffers();
}
```

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

#### Durum: Implementasyon

- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Gerçek implementasyon dosyaları bu klasörün dışında (vault dokümanı) | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Plugin yükleme başarısızlığı senaryosu vault'ta test edilmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. DriverLifecycle geçişlerinde kaynak sızıntısı riski (kaynak: `driver-stack-mimari` §DriverLifecycle).
2. HAL arayüzü uyuşmazlığında yanlış sürücünün yüklenmesi (kaynak: `driver-stack-mimari` §Driver Factory Pattern).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k015-platform-suruculeri/index]]` | ↓ | ASIO/WASAPI/CoreAudio bu yığının platform uygulamalarıdır | `.ai/architecture/k015-platform-suruculeri/index.md` |
| `[[../k016-linux-ses/index]]` | ↓ | ALSA/PipeWire Linux ayağı | `.ai/architecture/k016-linux-ses/index.md` |
| `[[../k000-windows-core/index]]` | ↑ | Windows API yüzeyi bu yığını besler | `.ai/architecture/k000-windows-core/index.md` |
| `[[../k002-macos-tasinabilirlik/index]]` | ↑ | Çapraz platform soyutlaması | `.ai/architecture/k002-macos-tasinabilirlik/index.md` |

Yerel dosyalar:

- `[[driver-stack-ve-buffer]]` — Driver Stack ve Buffer Mimarisi
- `[[latency-optimization-teknikleri]]` — Gecikme Optimizasyonu Teknikleri

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K014 · Driver Stack ve Buffer Mimarisi — SSOT: `.ai/architecture/k014-surucu-yigin/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
