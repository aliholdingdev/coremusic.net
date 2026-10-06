---
title: "Latency Optimizasyonu"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# Latency Optimizasyonu

## Genel Bakış

COREMUSIC, minimum round-trip latency hedefler. Latency zincirleri analiz edilerek her bileşende optimize edilir. Buffer boyut seçimi, real-time scheduling ve donanım optimizasyonu ile < 1ms hedeflenir.

## Teknik Detaylar

### Latency Zinciri

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

### Buffer Boyut Seçimi

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

### Real-Time Scheduling

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

### Donanım Clock Optimizasyonu

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

### Latency Monitoring

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

### Latency Testleri

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

## API / Arayüz

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

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| K2 Buffer Manager | İç |
| K2 Driver Stack | İç |
| POSIX RT | Sistem |

## Durum: Implementasyon

- **Faz 1**: Latency zinciri analizi
- **Faz 2**: RT scheduling implementasyonu
- **Faz 3**: Donanım clock optimizasyonu
- **Faz 4**: Latency monitoring ve test
- **Tahmini Süre**: 2 hafta (80 adam-saat)
