---
title: "K014 Gecikme Optimizasyonu Teknikleri"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K014 — Gecikme Optimizasyonu Teknikleri

> **K numarası:** K014 · **Klasör:** `k014-surucu-yigin` · **Dosya:** `latency-optimization-teknikleri`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Uygulama ile donanım arasındaki sürücü yığınının katmanlarını, ring/period buffer yönetimini ve gecikme optimizasyonu şartlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Gecikme Optimizasyonu Teknikleri** konusunu ele alır. Kapsamı: Ring/period buffer boyutlandırma, xrun/glitch davranışı ve gecikme bütçesi optimizasyonu.

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
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | 382 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | 386 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/buffer-management.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

### 4.2 · `k2-surucu/buffer-management.md`

| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

### 4.3 · `k2-surucu/latency-optimization.md`

| Buffer Boyutu | Örnekleme Hızı | Latency | CPU | Stabilite |
|---------------|----------------|---------|-----|-----------|
| 32 | 96kHz | 0.33ms | Yüksek | Düşük |
| 64 | 96kHz | 0.67ms | Orta | Orta |
| 128 | 96kHz | 1.33ms | Düşük | Yüksek |
| 256 | 96kHz | 2.67ms | Çok Düşük | Çok Yüksek |

### 4.4 · `k2-surucu/latency-optimization.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

### 4.5 · `k2-surucu/latency-optimization.md`

| Bağımlılık | Tür |
|------------|-----|
| K2 Buffer Manager | İç |
| K2 Driver Stack | İç |
| POSIX RT | Sistem |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/buffer-management.md` | H1 | Buffer Yönetimi |
| 2 | `k2-surucu/buffer-management.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/buffer-management.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/buffer-management.md` | H3 | Ring Buffer Yapısı |
| 5 | `k2-surucu/buffer-management.md` | H3 | Lock-Free Ring Buffer |
| 6 | `k2-surucu/buffer-management.md` | H3 | Double Buffering |
| 7 | `k2-surucu/buffer-management.md` | H3 | SPSC Queue (Single Producer Single Consumer) |
| 8 | `k2-surucu/buffer-management.md` | H3 | Buffer Boyut Optimizasyonu |
| 9 | `k2-surucu/buffer-management.md` | H3 | Adapte Buffer Yönetimi |
| 10 | `k2-surucu/buffer-management.md` | H2 | API / Arayüz |
| 11 | `k2-surucu/buffer-management.md` | H2 | Performans Metrikleri |
| 12 | `k2-surucu/buffer-management.md` | H2 | Bağımlılıklar |
| 13 | `k2-surucu/buffer-management.md` | H2 | Durum: Implementasyon |
| 14 | `k2-surucu/latency-optimization.md` | H1 | Latency Optimizasyonu |
| 15 | `k2-surucu/latency-optimization.md` | H2 | Genel Bakış |
| 16 | `k2-surucu/latency-optimization.md` | H2 | Teknik Detaylar |
| 17 | `k2-surucu/latency-optimization.md` | H3 | Latency Zinciri |
| 18 | `k2-surucu/latency-optimization.md` | H3 | Buffer Boyut Seçimi |
| 19 | `k2-surucu/latency-optimization.md` | H3 | Real-Time Scheduling |
| 20 | `k2-surucu/latency-optimization.md` | H3 | Donanım Clock Optimizasyonu |
| 21 | `k2-surucu/latency-optimization.md` | H3 | Latency Monitoring |
| 22 | `k2-surucu/latency-optimization.md` | H3 | Latency Testleri |
| 23 | `k2-surucu/latency-optimization.md` | H2 | API / Arayüz |
| 24 | `k2-surucu/latency-optimization.md` | H2 | Performans Metrikleri |
| 25 | `k2-surucu/latency-optimization.md` | H2 | Bağımlılıklar |
| 26 | `k2-surucu/latency-optimization.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md`


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


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md`


### Latency Optimizasyonu

#### Genel Bakış

COREMUSIC, minimum round-trip latency hedefler. Latency zincirleri analiz edilerek her bileşende optimize edilir. Buffer boyut seçimi, real-time scheduling ve donanım optimizasyonu ile < 1ms hedeflenir.

#### Teknik Detaylar

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

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| K2 Buffer Manager | İç |
| K2 Driver Stack | İç |
| POSIX RT | Sistem |

#### Durum: Implementasyon

- **Faz 1**: Latency zinciri analizi
- **Faz 2**: RT scheduling implementasyonu
- **Faz 3**: Donanım clock optimizasyonu
- **Faz 4**: Latency monitoring ve test
- **Tahmini Süre**: 2 hafta (80 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Gerçek ms cinsinden gecikme ölçümü vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Buffer boyutu ↔ CPU yükü trade-off eğrisi ölçülmüş değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Buffer taşması → dropout/xrun (kaynak: `buffer-management`).
2. Aşırı küçük buffer → yüksek IRQ yükü ve kararsızlık (kaynak: `latency-optimization`).

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

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K014 · Gecikme Optimizasyonu Teknikleri — SSOT: `.ai/architecture/k014-surucu-yigin/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
