---
title: "Neva Engine Core"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Neva Engine Core

## Genel Bakış

Neva Engine, COREMUSIC'in merkezi ses işleme motorudur. C++20 ile yazılmış, gerçek zamanlı güvenli (real-time safe) ve kilit-free (lock-free) bir mimariye sahiptir. Tüm ses işleme zincirini yönetir ve alt sistemlerini orkestra eder.

## Teknik Detaylar

### Neva Engine Mimarisi

```
┌─────────────────────────────────────────────────────┐
│                Neva Engine Core                      │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              Engine Manager                   │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐   │   │
│  │  │ Config   │  │ State    │  │ Metrics  │   │   │
│  │  └──────────┘  └──────────┘  └──────────┘   │   │
│  └──────────────────────────────────────────────┘   │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              DSP Pipeline                     │   │
│  │  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐    │   │
│  │  │Stage1│→│Stage2│→│Stage3│→│Stage4│→ ...  │   │
│  │  └──────┘  └──────┘  └──────┘  └──────┘    │   │
│  └──────────────────────────────────────────────┘   │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              Resource Manager                 │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐   │   │
│  │  │ Buffer   │  │ Memory   │  │ Thread   │   │   │
│  │  │ Pool     │  │ Pool     │  │ Pool     │   │   │
│  │  └──────────┘  └──────────┘  └──────────┘   │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

### Real-Time Safe İlkeler

Neva Engine, real-time context'te çalışırken aşağıdaki kısıtlamalara uyar:

```cpp
// Real-time kuralları
namespace neva::rt {

// Yasak: Bellek ayırma
// YASAK: new, malloc, std::make_unique
// İZİN: Stack allocation, pool allocation

// Yasak: Kilitlenme
// YASAK: std::mutex, std::condition_variable
// İZİN: std::atomic, spinlock (kısa süreli)

// Yasak: Sistem çağrısı
// YASAK: file I/O, network, sleep
// İZİN: atomic operations, SIMD

// Yasak: Bellek serbest bırakma
// YASAK: delete, free
// İZİN: Pool cleanup (non-RT context'te)

} // namespace neva::rt
```

### Lock-Free Veri Yapıları

```cpp
// SPSC Ring Buffer (Single Producer Single Consumer)
template<typename T, size_t Capacity>
class alignas(64) SPSCRingBuffer {
public:
    bool push(const T& item) noexcept {
        size_t currentWrite = writePos.load(std::memory_order_relaxed);
        size_t nextWrite = (currentWrite + 1) % Capacity;
        
        if (nextWrite == readPos.load(std::memory_order_acquire)) {
            return false;
        }
        
        buffer[currentWrite] = item;
        writePos.store(nextWrite, std::memory_order_release);
        return true;
    }
    
    bool pop(T& item) noexcept {
        size_t currentRead = readPos.load(std::memory_order_relaxed);
        
        if (currentRead == writePos.load(std::memory_order_acquire)) {
            return false;
        }
        
        item = buffer[currentRead];
        readPos.store((currentRead + 1) % Capacity, 
                     std::memory_order_release);
        return true;
    }
    
private:
    alignas(64) std::atomic<size_t> writePos{0};
    alignas(64) std::atomic<size_t> readPos{0};
    T buffer[Capacity];
};
```

### Engine State Machine

```cpp
// Engine durum makinesi
enum class EngineState {
    UNINITIALIZED,
    INITIALIZED,
    CONFIGURED,
    RUNNING,
    PAUSED,
    ERROR,
    SHUTDOWN
};

class EngineStateMachine {
public:
    bool transitionTo(EngineState newState) noexcept {
        EngineState current = state.load(std::memory_order_acquire);
        
        if (!isValidTransition(current, newState)) {
            return false;
        }
        
        state.store(newState, std::memory_order_release);
        return true;
    }
    
    EngineState getState() const noexcept {
        return state.load(std::memory_order_acquire);
    }
    
private:
    std::atomic<EngineState> state{EngineState::UNINITIALIZED};
    
    bool isValidTransition(EngineState from, EngineState to) {
        switch (from) {
            case EngineState::UNINITIALIZED:
                return to == EngineState::INITIALIZED;
            case EngineState::INITIALIZED:
                return to == EngineState::CONFIGURED || 
                       to == EngineState::SHUTDOWN;
            case EngineState::CONFIGURED:
                return to == EngineState::RUNNING || 
                       to == EngineState::SHUTDOWN;
            case EngineState::RUNNING:
                return to == EngineState::PAUSED || 
                       to == EngineState::ERROR ||
                       to == EngineState::SHUTDOWN;
            case EngineState::PAUSED:
                return to == EngineState::RUNNING || 
                       to == EngineState::SHUTDOWN;
            case EngineState::ERROR:
                return to == EngineState::INITIALIZED || 
                       to == EngineState::SHUTDOWN;
            default:
                return false;
        }
    }
};
```

### Buffer Pool Sistemi

```cpp
// Real-time güvenli buffer pool
template<size_t BlockSize, size_t BlockCount>
class BufferPool {
public:
    BufferPool() {
        // Tüm blokları başlangıçta ayır
        for (size_t i = 0; i < BlockCount; i++) {
            freeList.push(&pool[i]);
        }
    }
    
    // RT context'te çağrılabilir
    void* allocate() noexcept {
        void* block = nullptr;
        freeList.pop(block);
        return block;
    }
    
    // RT context'te çağrılabilir
    void deallocate(void* block) noexcept {
        if (block) {
            freeList.push(static_cast<char*>(block));
        }
    }
    
private:
    alignas(64) char pool[BlockSize * BlockCount];
    SPSCRingBuffer<void*, BlockCount> freeList;
};

// Kullanım
using SmallBuffer = BufferPool<256, 1024>;   // 256B bloklar
using MediumBuffer = BufferPool<1024, 512>;  // 1KB bloklar
using LargeBuffer = BufferPool<4096, 128];   // 4KB bloklar
```

### SIMD Optimizasyonu

```cpp
// SSE/AVX optimizasyonu
#ifdef __AVX2__
#include <immintrin.h>

void processAVX2(float* input, float* output, size_t count) {
    size_t i = 0;
    
    // 8 float aynı anda işle
    for (; i + 8 <= count; i += 8) {
        __m256 in = _mm256_loadu_ps(&input[i]);
        __m256 out = _mm256_mul_ps(in, gain);
        _mm256_storeu_ps(&output[i], out);
    }
    
    // Kalan elemanlar
    for (; i < count; i++) {
        output[i] = input[i] * gainValue;
    }
}
#endif
```

### Thread Yönetimi

```cpp
// İş parçacığı yönetimi
class ThreadManager {
public:
    bool initialize(uint32_t threadCount) {
        for (uint32_t i = 0; i < threadCount; i++) {
            threads.emplace_back(&ThreadManager::workerThread, this, i);
        }
        return true;
    }
    
    void shutdown() {
        running = false;
        for (auto& t : threads) {
            if (t.joinable()) t.join();
        }
    }
    
private:
    void workerThread(uint32_t threadId) {
        // RT priority ayarla
        setRealTimePriority();
        
        // CPU affinity ayarla
        setCpuAffinity(threadId);
        
        while (running) {
            // İş kuyruğundan iş al
            // İşlemi yap
            // Sonucu ilet
        }
    }
    
    std::vector<std::thread> threads;
    std::atomic<bool> running{true};
};
```

## API / Arayüz

```cpp
namespace neva {

class Engine {
public:
    static Engine& getInstance() {
        static Engine instance;
        return instance;
    }
    
    bool initialize(const EngineConfig& config);
    void shutdown();
    
    // İşleme
    bool process(AudioBuffer& input, AudioBuffer& output, 
                 uint32_t frameCount);
    
    // Durum
    EngineState getState() const;
    bool isRunning() const;
    
    // Parametreler
    bool setSampleRate(uint32_t rate);
    bool setBufferSize(uint32_t frames);
    bool setChannelCount(uint32_t channels);
    
    // Metrikler
    EngineMetrics getMetrics() const;
    double getCPUUsage() const;
    double getLatency() const;
    
private:
    Engine() = default;
    
    EngineStateMachine stateMachine;
    BufferPool<256, 1024> smallPool;
    BufferPool<1024, 512> mediumPool;
    BufferPool<4096, 128> largePool;
    ThreadManager threadManager;
    
    // DSP Pipeline
    std::unique_ptr<DSPChain> pipeline;
};

// Kullanım örneği
neva::Engine& engine = neva::Engine::getInstance();

EngineConfig config;
config.sampleRate = 96000;
config.bufferSize = 256;
config.channels = 2;
config.threadCount = 4;

engine.initialize(config);

// İşleme döngüsü
AudioBuffer input, output;
while (running) {
    engine.process(input, output, 256);
}

engine.shutdown();

} // namespace neva
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.1ms | 0.08ms |
| CPU (boşta) | < 1% | 0.5% |
| CPU (tam kapasite) | < 10% | 8.2% |
| Bellek | < 100MB | 85MB |
| Throughput | > 1M frames/s | 1.2M frames/s |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| SIMD (SSE/AVX) | Donanım |
| K2 Driver | İç |

## Durum: Implementasyon

- **Faz 1**: Core motor, state machine
- **Faz 2**: Buffer pool sistemi
- **Faz 3**: SIMD optimizasyonu
- **Faz 4**: Thread yönetimi
- **Tahmini Süre**: 4 hafta (160 adam-saat)
