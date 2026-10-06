---
title: "Gecikme Ölçüm ve Tuning - k032-latency-optimization"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Gecikme Ölçüm ve Tuning

> Klasör: `k032-latency-optimization` · Dilim: D01 (k018–k035) · Dosya: `gecikme-olcum-ve-tuning.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Round-trip ölçüm yöntemi, hedef metrikler ve ayarlama döngüsü.

Bu belge; D01 diliminin (Gecikme Optimizasyonu) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Round-trip ölçüm yöntemi, hedef metrikler ve ayarlama döngüsü.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Gecikme Optimizasyonu)
- **Çapraz referanslar:** [[../k031-buffer-management/buffer-management.md]] · [[../k025-threading-model/gercek-zamanli-zamanlama.md]] · [[../k018-dma-kesinti-yonetimi/dma-olcum-ve-test.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### latency-optimization.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` (385 satır)

#### latency-optimization.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § (giriş) — L1–L9

---
title: "Latency Optimizasyonu"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Latency Optimizasyonu


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Genel Bakış` — L10–L12


COREMUSIC, minimum round-trip latency hedefler. Latency zincirleri analiz edilerek her bileşende optimize edilir. Buffer boyut seçimi, real-time scheduling ve donanım optimizasyonu ile < 1ms hedeflenir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Teknik Detaylar` — L14–L313


##### Latency Zinciri

```
┌─────────────────────────────────────────────────────────┐
│              Round-Trip Latency Zinciri                 │
│                                                         │
│  [Mikrofon] → [ADC] → [Driver In] → [Processing]       │
│       ↓          ↓          ↓              ↓            │
│  Analog    Sampling   Buffer       K3 DSP               │
│  Gecikme   Gecikme   Gecikme      Gecikme              │
│  (0.1ms)   (0.01ms)  (0.67ms)     (0.1ms)             │
│                                                         │
│                    Toplam Input: 0.88ms                 │
│                                                         │
│  [Processing] → [Driver Out] → [DAC] → [Hoparlör]      │
│       ↓              ↓           ↓           ↓          │
│  K3 DSP        Buffer       Sampling    Analog          │
│  Gecikme       Gecikme      Gecikme     Gecikme         │
│  (0.1ms)       (0.67ms)     (0.01ms)    (0.1ms)        │
│                                                         │
│                    Toplam Output: 0.88ms                │
│                                                         │
│  Round-Trip Toplam: 1.76ms                             │
└─────────────────────────────────────────────────────────┘
```

##### Buffer Boyut Seçimi

Buffer boyutu latency ve stabiliteyi doğrudan etkiler:

| Buffer Boyutu | Örnekleme Hızı | Latency | CPU | Stabilite |
|---------------|----------------|---------|-----|-----------|
| 32 | 96kHz | 0.33ms | Yüksek | Düşük |
| 64 | 96kHz | 0.67ms | Orta | Orta |
| 128 | 96kHz | 1.33ms | Düşük | Yüksek |
| 256 | 96kHz | 2.67ms | Çok Düşük | Çok Yüksek |

```cpp
// Optimal buffer boyutu hesaplama
struct LatencyProfile {
    uint32_t sampleRate;
    uint32_t targetLatencyUs;    // Mikrosaniye
    uint32_t cpuCores;
    bool prioritizeLatency;      // true = latency, false = stability
};

uint32_t calculateOptimalBuffer(const LatencyProfile& profile) {
    // Minimum buffer (donanıma bağlı)
    uint32_t minBuffer = 32;
    
    // Hedef buffer
    uint32_t targetBuffer = (profile.sampleRate * 
                             profile.targetLatencyUs) / 1000000;
    
    // CPU çekirdek sayısı etkisi
    if (profile.cpuCores >= 4) {
        targetBuffer = std::max(targetBuffer, minBuffer);
    } else {
        targetBuffer = std::max(targetBuffer, minBuffer * 2);
    }
    
    // 2'nin kuvvetine yuvarla
    uint32_t optimal = 1;
    while (optimal < targetBuffer) optimal <<= 1;
    
    return optimal;
}
```

##### Real-Time Scheduling

Gerçek zamanlı zamanlama, latency için kritiktir:

```cpp
// RT thread yapılandırması
class RealTimeScheduler {
public:
    static bool setRealTimePriority(pthread_t thread, 
                                     int priority = 88) {
        struct sched_param param;
        param.sched_priority = priority;
        
        if (pthread_setschedparam(thread, SCHED_FIFO, &param) != 0) {
            return false;
        }
        return true;
    }
    
    static bool setCpuAffinity(pthread_t thread, int core) {
        cpu_set_t cpuset;
        CPU_ZERO(&cpuset);
        CPU_SET(core, &cpuset);
        
        return pthread_setaffinity_np(thread, sizeof(cpuset), 
                                       &cpuset) == 0;
    }
    
    static bool lockMemory() {
        // Belleği kilitle (swap yok)
        return mlockall(MCL_CURRENT | MCL_FUTURE) == 0;
    }
    
    static bool preFaultStack() {
        // Stack'i önceden hata
        size_t stackSize = 8 * 1024 * 1024; // 8MB
        void* stack = malloc(stackSize);
        if (stack) {
            // Her sayfaya dokun (pre-fault)
            volatile char* p = (volatile char*)stack;
            for (size_t i = 0; i < stackSize; i += 4096) {
                p[i] = 0;
            }
            free(stack);
        }
        return true;
    }
};
```

##### Donanım Clock Optimizasyonu

```cpp
// Donanım clock yönetimi
class HardwareClockOptimizer {
public:
    bool optimizeForLowLatency(uint32_t deviceId) {
        // Örnekleme hızını sabitle
        if (!setFixedSampleRate(deviceId, 96000)) {
            return false;
        }
        
        // PLL kilitleme
        if (!lockPLL(deviceId)) {
            return false;
        }
        
        // Clock source seçimi
        if (!selectBestClockSource(deviceId)) {
            return false;
        }
        
        return true;
    }
    
    bool setFixedSampleRate(uint32_t deviceId, uint32_t rate) {
        // Donanım saatini sabitle
        // Değişimler latency spike'a neden olur
        return hardwareSetSampleRate(deviceId, rate);
    }
    
    bool lockPLL(uint32_t deviceId) {
        // PLL'i kilitle
        // Serbest çalışan PLL jitter yaratır
        return hardwareLockPLL(deviceId);
    }
    
    bool selectBestClockSource(uint32_t deviceId) {
        // İç clock tercihli (daha kararlı)
        // Dış clock kullanılıyorsa, kalitesini kontrol et
        return true;
    }
};
```

##### Latency Monitoring

```cpp
// Gerçek zamanlı latency izleme
class LatencyMonitor {
public:
    void start() {
        startTime = std::chrono::high_resolution_clock::now();
    }
    
    void recordInputLatency(double latency) {
        inputLatencies.push_back(latency);
        if (inputLatencies.size() > 1000) {
            inputLatencies.erase(inputLatencies.begin());
        }
    }
    
    void recordOutputLatency(double latency) {
        outputLatencies.push_back(latency);
        if (outputLatencies.size() > 1000) {
            outputLatencies.erase(outputLatencies.begin());
        }
    }
    
    LatencyStats getStats() const {
        LatencyStats stats;
        
        // Input istatistikleri
        if (!inputLatencies.empty()) {
            stats.inputAvg = calculateAverage(inputLatencies);
            stats.inputMax = calculateMax(inputLatencies);
            stats.inputMin = calculateMin(inputLatencies);
            stats.inputJitter = calculateJitter(inputLatencies);
        }
        
        // Output istatistikleri
        if (!outputLatencies.empty()) {
            stats.outputAvg = calculateAverage(outputLatencies);
            stats.outputMax = calculateMax(outputLatencies);
            stats.outputMin = calculateMin(outputLatencies);
            stats.outputJitter = calculateJitter(outputLatencies);
        }
        
        // Round-trip
        stats.roundTrip = stats.inputAvg + stats.outputAvg;
        
        return stats;
    }
    
private:
    std::chrono::high_resolution_clock::time_point startTime;
    std::vector<double> inputLatencies;
    std::vector<double> outputLatencies;
    
    double calculateAverage(const std::vector<double>& data) {
        double sum = 0;
        for (double d : data) sum += d;
        return sum / data.size();
    }
    
    double calculateMax(const std::vector<double>& data) {
        return *std::max_element(data.begin(), data.end());
    }
    
    double calculateMin(const std::vector<double>& data) {
        return *std::min_element(data.begin(), data.end());
    }
    
    double calculateJitter(const std::vector<double>& data) {
        double avg = calculateAverage(data);
        double sumSq = 0;
        for (double d : data) {
            sumSq += (d - avg) * (d - avg);
        }
        return std::sqrt(sumSq / data.size());
    }
};
```

##### Latency Testleri

```cpp
// Latency testi
class LatencyTester {
public:
    struct TestResult {
        double inputLatency;
        double outputLatency;
        double roundTrip;
        double jitter;
        uint32_t underruns;
        uint32_t overruns;
    };
    
    TestResult runTest(uint32_t durationMs, 
                       uint32_t bufferSize,
                       uint32_t sampleRate) {
        TestResult result = {};
        
        auto startTime = std::chrono::steady_clock::now();
        auto endTime = startTime + 
                       std::chrono::milliseconds(durationMs);
        
        while (std::chrono::steady_clock::now() < endTime) {
            // Input latency ölç
            auto inputStart = std::chrono::steady_clock::now();
            readInput(bufferSize);
            auto inputEnd = std::chrono::steady_clock::now();
            
            result.inputLatency = std::chrono::duration<double, 
                                  std::milli>(
                inputEnd - inputStart).count();
            
            // Processing
            processAudio(bufferSize);
            
            // Output latency ölç
            auto outputStart = std::chrono::steady_clock::now();
            writeOutput(bufferSize);
            auto outputEnd = std::chrono::steady_clock::now();
            
            result.outputLatency = std::chrono::duration<double, 
                                   std::milli>(
                outputEnd - outputStart).count();
            
            // Round-trip
            result.roundTrip = result.inputLatency + 
                              result.outputLatency;
        }
        
        return result;
    }
};
```

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `API / Arayüz` — L315–L359


```cpp
class LatencyOptimizer {
public:
    LatencyOptimizer(const LatencyProfile& profile);
    
    // Başlatma
    bool initialize();
    void shutdown();
    
    // Optimizasyon
    bool optimizeForTarget(double targetLatencyMs);
    bool applyRealTimeSettings();
    bool lockMemory();
    
    // İzleme
    LatencyStats getCurrentStats() const;
    LatencyStats getHistoricalStats() const;
    
    // Test
    TestResult runLatencyTest(uint32_t durationMs);
    
    // Ayarlama
    bool setBufferSize(uint32_t frames);
    bool setPriority(int priority);
    bool setCpuAffinity(int core);
};

// Kullanım örneği
LatencyProfile profile;
profile.sampleRate = 96000;
profile.targetLatencyUs = 500;  // 0.5ms
profile.cpuCores = 8;
profile.prioritizeLatency = true;

LatencyOptimizer optimizer(profile);
optimizer.initialize();
optimizer.optimizeForTarget(0.5);
optimizer.applyRealTimeSettings();

auto stats = optimizer.getCurrentStats();
std::cout << "Round-trip: " << stats.roundTrip << "ms" 
          << std::endl;
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Performans Metrikleri` — L361–L369


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Bağımlılıklar` — L371–L377


| Bağımlılık | Tür |
|------------|-----|
| K2 Buffer Manager | İç |
| K2 Driver Stack | İç |
| POSIX RT | Sistem |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Durum: Implementasyon` — L379–L385


- **Faz 1**: Latency zinciri analizi
- **Faz 2**: RT scheduling implementasyonu
- **Faz 3**: Donanım clock optimizasyonu
- **Faz 4**: Latency monitoring ve test
- **Tahmini Süre**: 2 hafta (80 adam-saat)

### buffer-management.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` (381 satır)

#### buffer-management.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § (giriş) — L1–L9

---
title: "Buffer Yönetimi"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Buffer Yönetimi


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Teknik Detaylar` — L14–L301


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

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Performans Metrikleri` — L357–L365


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| uint32_t underruns; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L270 |
| uint32_t overruns; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L271 |
| uint64_t overflowCount; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L334 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| static bool preFaultStack() { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L118 |
| // Stack'i önceden hata | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L119 |
| // Her sayfaya dokun (pre-fault) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L123 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| title: "Latency Optimizasyonu" | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L2 |
| # Latency Optimizasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L8 |
| COREMUSIC, minimum round-trip latency hedefler. Latency zincirleri analiz edilerek her bileşende optimize edilir. Buffer boyut seçimi, real… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L12 |
| ### Latency Zinciri | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L16 |
| │              Round-Trip Latency Zinciri                 │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L20 |
| │  Analog    Sampling   Buffer       K3 DSP               │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L24 |
| │  Gecikme   Gecikme   Gecikme      Gecikme              │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L25 |
| │  (0.1ms)   (0.01ms)  (0.67ms)     (0.1ms)             │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L26 |
| │                    Toplam Input: 0.88ms                 │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L28 |
| │  K3 DSP        Buffer       Sampling    Analog          │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L32 |
| │  Gecikme       Gecikme      Gecikme     Gecikme         │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L33 |
| │  (0.1ms)       (0.67ms)     (0.01ms)    (0.1ms)        │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L34 |
| │                    Toplam Output: 0.88ms                │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L36 |
| │  Round-Trip Toplam: 1.76ms                             │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L38 |
| ### Buffer Boyut Seçimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L42 |
| Buffer boyutu latency ve stabiliteyi doğrudan etkiler: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L44 |
| // Optimal buffer boyutu hesaplama | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L54 |
| struct LatencyProfile { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L55 |
| uint32_t targetLatencyUs;    // Mikrosaniye | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L57 |
| uint32_t cpuCores; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L58 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| // Dış clock kullanılıyorsa, kalitesini kontrol et | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L174 |
| // Güvenlik payı (%20) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L245 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| ### Latency Testleri | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L259 |
| // Latency testi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L262 |
| class LatencyTester { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L263 |
| struct TestResult { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L265 |
| TestResult runTest(uint32_t durationMs, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L274 |
| TestResult result = {}; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L277 |
| TestResult runLatencyTest(uint32_t durationMs); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L336 |
| - **Faz 4**: Latency monitoring ve test | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L384 |
| // Ölçümler | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L319 |

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
| `latency` | 57 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L2 |
| `jitter` | 20 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L168 |
| `test` | 9 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L259 |
| `buffer` | 99 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L12 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Teknik Detaylar | L14–L313 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## API / Arayüz | L315–L359 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Performans Metrikleri | L361–L369 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Bağımlılıklar | L371–L377 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Durum: Implementasyon | L379–L385 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Genel Bakış | L10–L12 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Teknik Detaylar | L14–L301 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## API / Arayüz | L303–L355 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Performans Metrikleri | L357–L365 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Bağımlılıklar | L367–L373 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Durum: Implementasyon | L375–L381 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Durum: Implementasyon` — L379–L385


- **Faz 1**: Latency zinciri analizi
- **Faz 2**: RT scheduling implementasyonu
- **Faz 3**: Donanım clock optimizasyonu
- **Faz 4**: Latency monitoring ve test
- **Tahmini Süre**: 2 hafta (80 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Durum: Implementasyon` — L375–L381


- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
