---
title: "Sürücü Yığını Yönetimi - k030-driver-stack"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Sürücü Yığını Yönetimi

> Klasör: `k030-driver-stack` · Dilim: D01 (k018–k035) · Dosya: `surucu-yigini-yonetimi.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Yığın üzerinde yükleme, güncelleme, hata izolasyonu ve sıcak değişim.

Bu belge; D01 diliminin (Sürücü Yığını) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Yığın üzerinde yükleme, güncelleme, hata izolasyonu ve sıcak değişim.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Sürücü Yığını)
- **Çapraz referanslar:** [[../k031-buffer-management/buffer-management.md]] · [[../k018-dma-kesinti-yonetimi/dma-yonetimi.md]] · [[../k029-cross-platform-api/cross-platform-api.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### driver-stack-mimari.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` (378 satır)

#### driver-stack-mimari.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § (giriş) — L1–L9

---
title: "Driver Stack Mimarisi"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Driver Stack Mimarisi


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Genel Bakış` — L10–L12


COREMUSIC Driver Stack, çok katmanlı bir soyutlama katmanı ile farklı platformlarda (Windows, Linux, macOS) tutarlı bir arayüz sağlar. HAL (Hardware Abstraction Layer) sayesinde üst katmanlar donanım detaylarından bağımsız çalışır.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Teknik Detaylar` — L14–L299


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `API / Arayüz` — L301–L352


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Performans Metrikleri` — L354–L362


| Metrik | Hedef |
|--------|-------|
| Soyutlama Overhead | < 0.01ms |
| Driver Değişim Süresi | < 10ms |
| Hata Kurtarma | < 100ms |
| Bellek Kullanımı | < 10MB |
| CPU Overhead | < 0.5% |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Bağımlılıklar` — L364–L370


| Bağımlılık | Tür |
|------------|-----|
| Tüm K2 sürücüleri | İç |
| K1 OS Interface | İç |
| K3 Engine | İç |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Durum: Implementasyon` — L372–L378


- **Faz 1**: HAL interface tanımları
- **Faz 2**: Driver factory implementasyonu
- **Faz 3**: Error chain ve retry mekanizması
- **Faz 4**: Plugin sistemi
- **Tahmini Süre**: 2 hafta (80 adam-saat)

### index.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` (91 satır)

#### index.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § (giriş) — L1–L9

---
title: "K2 Sürücü Katmanı"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### K2 Sürücü Katmanı


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Genel Bakış` — L10–L12


K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DAC'ler, ağı ses cihazları) için platform-bağımsız bir soyutlama sağlar. Bu katman, gerçek zamanlı ses akışı için gereken düşük gecikmeli (low-latency) veri yollarını yönetir.

#### Mimari Konum

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Mimari Konum` — L14–L20


```
K0 (Donanım) → K1 (OS/Core) → K2 (Sürücü) → K3 (Ses Motoru)
```

K2 katmanı, K1'in sağladığı çekirdek hizmetleri (DMA, kesinti, bellek yönetimi) üzerine inşa edilir ve K3'ün DSP zincirine ham ses verisini iletir.

#### Kapsam ve Kategoriler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Kapsam ve Kategoriler` — L22–L39


##### Platform Sürücüleri
- **ASIO Drivers**: Windows profesyonel ses, Exclusive mode
- **WASAPI**: Windows Audio Session API, modern Windows ses
- **ALSA Native**: Linux çekirdek seviyesi ses
- **PipeWire**: Modern Linux ses yönlendirmesi
- **CoreAudio**: macOS/iOS ses altyapısı

##### Donanım Arabirimleri
- **USB Audio Class 2.0**: USB ses cihazları için evrensel protokol
- **Network Audio**: Dante, AVB, RAVENNA Protokolleri
- **Bluetooth A2DP**: Kablosuz ses iletimi (LDAC, aptX HD, LC3)

##### Mimari Bileşenler
- **Driver Stack**: Çok katmanlı sürücü yığını
- **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar
- **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi

#### Temel İlkeler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Temel İlkeler` — L41–L53


##### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety)
Tüm K2 kodu, kesme.Context içinde çalışabilir: bellek ayırma yasak, kilitlenme (blocking) yasak, sistem çağrısı yasak.

##### 2. Donanım Bağımsızlığı
Aynı API, ASIO, WASAPI, ALSA ve CoreAudio üzerinde çalışır. Üst katmanlar hangi sürücünün kullanıldığını bilmez.

##### 3. Minimum Gecikme
Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında.

##### 4. Hata Toleransı
Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır.

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Bağımlılıklar` — L55–L61


| Katman | İlişki |
|--------|--------|
| K0 | Donanım kaynaklarını kullanır (DMA, IRQ) |
| K1 | İşletim sistemi hizmetlerini çağırır |
| K3 | Ham ses verisini iletir |

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Performans Metrikleri` — L63–L71


| Metrik | Hedef |
|--------|-------|
| Round-trip Latency | < 0.5ms (ASIO Exclusive) |
| Buffer Boyutu | 32-64 sample @ 96kHz |
| CPU Kullanımı | < 5% (boşta) |
| Maksimum Kanal Sayısı | 128 giriş + 128 çıkış |
| Desteklenen Örnekleme Hızları | 44.1k, 48k, 88.2k, 96k, 176.4k, 192k, 352.8k, 384k |

#### Dosya Haritası

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Dosya Haritası` — L73–L87


| Dosya | İçerik |
|-------|--------|
| asio-drivers.md | ASIO Exclusive mode, low latency, buffer yönetimi |
| wasapi-exclusive.md | WASAPI Exclusive/Shared, Windows audio session |
| alsa-native.md | ALSA native audio, PCM aygıtları |
| pipewire-modern.md | PipeWire modern Linux ses, SPA pluginleri |
| core-audio-macos.md | CoreAudio macOS, AudioUnits, HAL |
| usb-audio-class.md | USB Audio Class 2.0, isochronous mode |
| network-audio-drivers.md | Dante, AVB, RAVENNA ağ sesi |
| bluetooth-a2dp.md | Bluetooth A2DP, LDAC, aptX HD, LC3 |
| driver-stack-mimari.md | Çok katmanlı sürücü yığını, HAL soyutlama |
| buffer-management.md | Ring buffer, double buffering, lock-free queues |
| latency-optimization.md | Latency zincirleri, buffer seçimi, RT planlama |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| ERR_BUFFER_OVERFLOW = 1002, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L241 |
| ERR_TIMEOUT = 1006, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L245 |
| return code == ERR_TIMEOUT \|\| | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L256 |
| code == ERR_BUFFER_OVERFLOW; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L257 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| virtual ~IAudioHAL() = default; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L56 |
| default: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L110 |
| logError("Invalid state transition"); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L168 |
| case State::ERROR: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L181 |
| return handleError(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L182 |
| default: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L185 |
| {State::INITIALIZED, {State::CONFIGURED, State::ERROR}}, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L197 |
| {State::CONFIGURED, {State::RUNNING, State::ERROR}}, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L198 |
| {State::RUNNING, {State::PAUSED, State::ERROR, State::DESTROYED}}, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L199 |
| {State::ERROR, {State::INITIALIZED, State::DESTROYED}}, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L201 |
| ### Hata Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L212 |
| Katmanlı hata yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L214 |
| // Hata zinciri | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L217 |
| class ErrorChain { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L218 |
| void reportError(int layer, int code, const std::string& msg) { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L220 |
| ErrorEntry entry; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L221 |
| errors.push_back(entry); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L227 |
| // Retry stratejisi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L232 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| virtual ssize_t write(const float* buffer, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L65 |
| virtual ssize_t read(float* buffer, size_t frames) = 0; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L71 |
| virtual bool setBufferSize(uint32_t frames) = 0; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L75 |
| virtual double getLatency() const = 0; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L79 |
| driver->read(inputBuffer, frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L324 |
| engine->process(inputBuffer, outputBuffer, frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L327 |
| return driver->write(outputBuffer, frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L330 |
| config.bufferSize = 256; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L342 |
| K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DA… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L12 |
| - **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L38 |
| - **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L39 |
| ### 3. Minimum Gecikme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L49 |
| Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L50 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L43 |
| K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASI… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L91 |
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
| `driver-stack` | 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L85 |
| `firmware` | 0 | [kaynakta eşleşme yok] |
| `hotplug` | 0 | [kaynakta eşleşme yok] |
| `fallback` | 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` L120 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## Teknik Detaylar | L14–L299 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## API / Arayüz | L301–L352 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## Performans Metrikleri | L354–L362 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## Bağımlılıklar | L364–L370 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` | ## Durum: Implementasyon | L372–L378 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/driver-stack-mimari.md` § `Durum: Implementasyon` — L372–L378


- **Faz 1**: HAL interface tanımları
- **Faz 2**: Driver factory implementasyonu
- **Faz 3**: Error chain ve retry mekanizması
- **Faz 4**: Plugin sistemi
- **Tahmini Süre**: 2 hafta (80 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
