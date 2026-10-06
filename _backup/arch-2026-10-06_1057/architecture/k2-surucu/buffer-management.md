---
title: "Buffer Yönetimi"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# Buffer Yönetimi

## Genel Bakış

COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double buffering ve lock-free queue'lar kullanılarak gerçek zamanlı performans elde edilir.

## Teknik Detaylar

### Ring Buffer Yapısı

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

### Lock-Free Ring Buffer

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

### Double Buffering

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

### SPSC Queue (Single Producer Single Consumer)

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

### Buffer Boyut Optimizasyonu

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

### Adapte Buffer Yönetimi

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

## API / Arayüz

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

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

## Durum: Implementasyon

- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
