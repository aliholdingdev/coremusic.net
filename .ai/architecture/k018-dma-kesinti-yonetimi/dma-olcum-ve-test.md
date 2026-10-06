---
title: "DMA Ölçüm ve Test - k018-dma-kesinti-yonetimi"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# DMA Ölçüm ve Test

> Klasör: `k018-dma-kesinti-yonetimi` · Dilim: D01 (k018–k035) · Dosya: `dma-olcum-ve-test.md`
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi) · `audio-hardware-engineer` (Audio Hardware Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri.

Bu belge; D01 diliminin (DMA ve Kesinti Yönetimi) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** DMA transfer gecikmesi ölçümü ve doğrulama test maddeleri.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (DMA ve Kesinti Yönetimi)
- **Çapraz referanslar:** [[../k022-rpi5-core/rpi5-core.md]] · [[../k031-buffer-management/buffer-management.md]] · [[../k030-driver-stack/driver-stack-mimari.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Performans Metrikleri` — L361–L369


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Round-trip Latency | < 1ms | 0.88ms |
| Jitter | < 10μs | 7.2μs |
| CPU (RT) | < 5% | 3.8% |
| Memory Lock | 100% | 100% |
| Underrun Rate | < 0.01% | 0.005% |

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
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| ### 4. Hata Toleransı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L52 |
| Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L53 |
| static bool preFaultStack() { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L118 |
| // Stack'i önceden hata | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L119 |
| // Her sayfaya dokun (pre-fault) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L123 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DA… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L12 |
| - **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L38 |
| - **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L39 |
| ### 3. Minimum Gecikme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L49 |
| Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L50 |
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

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L43 |
| K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASI… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L91 |
| // Dış clock kullanılıyorsa, kalitesini kontrol et | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L174 |
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
| `DMA` | 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L20 |
| `latency` | 54 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L12 |
| `test` | 9 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` L259 |
| `buffer` | 28 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L38 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Genel Bakış | L10–L12 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Teknik Detaylar | L14–L313 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## API / Arayüz | L315–L359 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Performans Metrikleri | L361–L369 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Bağımlılıklar | L371–L377 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` | ## Durum: Implementasyon | L379–L385 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/latency-optimization.md` § `Durum: Implementasyon` — L379–L385


- **Faz 1**: Latency zinciri analizi
- **Faz 2**: RT scheduling implementasyonu
- **Faz 3**: Donanım clock optimizasyonu
- **Faz 4**: Latency monitoring ve test
- **Tahmini Süre**: 2 hafta (80 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
