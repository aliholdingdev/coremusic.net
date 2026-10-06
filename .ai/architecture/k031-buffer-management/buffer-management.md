---
title: "Buffer Yönetimi - k031-buffer-management"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Buffer Yönetimi

> Klasör: `k031-buffer-management` · Dilim: D01 (k018–k035) · Dosya: `buffer-management.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Tampon boyutları, doldurma/boşaltma döngüsü ve performans metrikleri.

Bu belge; D01 diliminin (Buffer Yönetimi) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Tampon boyutları, doldurma/boşaltma döngüsü ve performans metrikleri.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Buffer Yönetimi)
- **Çapraz referanslar:** [[../k032-latency-optimization/latency-optimization.md]] · [[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Genel Bakış` — L10–L12


COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double buffering ve lock-free queue'lar kullanılarak gerçek zamanlı performans elde edilir.

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

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `API / Arayüz` — L303–L355


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Performans Metrikleri` — L357–L365


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Bağımlılıklar` — L367–L373


| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Durum: Implementasyon` — L375–L381


- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)

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
| uint64_t overflowCount; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L334 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| ### 4. Hata Toleransı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L52 |
| Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L53 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| title: "Buffer Yönetimi" | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L2 |
| # Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L8 |
| COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double … | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| ### Ring Buffer Yapısı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L16 |
| Ring Buffer Yapısı: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L19 |
| ### Lock-Free Ring Buffer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L34 |
| // Lock-free ring buffer implementasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L37 |
| class LockFreeRingBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L39 |
| LockFreeRingBuffer(size_t capacity) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L41 |
| : capacity(capacity), buffer(new T[capacity]), | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L42 |
| buffer[(currentWrite + i) % capacity] = data[i]; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L55 |
| data[i] = buffer[(currentRead + i) % capacity]; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L73 |
| std::unique_ptr<T[]> buffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L94 |
| ### Double Buffering | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L100 |
| Double buffering, kesintisiz ses akışı için kullanılır: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L102 |
| Double Buffering Akışı: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L105 |
| │  │  Buffer A    │  │  Buffer B    │                  │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L110 |
| │  │  Buffer A    │  │  Buffer B    │                  │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L118 |
| // Double buffer implementasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L128 |
| class DoubleBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L129 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| // Güvenlik payı (%20) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L245 |
| ### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L43 |
| K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASI… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L91 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| // Ölçümler | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L319 |
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
| `buffer` | 85 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L2 |
| `ring` | 14 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| `lock-free` | 6 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| `latency` | 15 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L235 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Teknik Detaylar | L14–L301 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## API / Arayüz | L303–L355 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Performans Metrikleri | L357–L365 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Bağımlılıklar | L367–L373 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Durum: Implementasyon | L375–L381 | ✓ (verbatim) |
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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Durum: Implementasyon` — L375–L381


- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
