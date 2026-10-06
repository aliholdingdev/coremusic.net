---
title: "Stream Buffer"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Stream Buffer (Jitter Buffer, Adaptif Buffering)

## Genel Bakış

COREMUSIC Stream Buffer, ağ ve dosya tabanlı ses akışları için jitter buffer ve adaptif buffering sağlar. Network stream'lerde gecikme dalgalanmalarını (jitter) kontrol ederek kesintisiz ses reprossing sağlar.

## Teknik Detaylar

### Jitter Buffer Yapısı

```
┌─────────────────────────────────────────────────────┐
│              Jitter Buffer Mimarisi                 │
│                                                     │
│  [Network] → [Packet Receiver] → [Buffer Pool]     │
│      ↓              ↓                   ↓           │
│  [Timestamp]   [Sequence]         [Slot Manager]   │
│      ↓              ↓                   ↓           │
│  [Jitter      [Buffer           [Playback         │
│   Calculator]  Manager]          Controller]       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### Adaptif Jitter Buffer

```cpp
class AdaptiveJitterBuffer {
public:
    AdaptiveJitterBuffer(uint32_t sampleRate, 
                         uint32_t channels) 
        : sampleRate(sampleRate), channels(channels) {
        
        // Başlangıç buffer boyutu
        minBufferMs = 20;
        maxBufferMs = 200;
        currentBufferMs = 50;
        
        // Buffer slotları
        bufferSlots.resize(maxSlots);
    }
    
    // Paket ekleme
    bool addPacket(const AudioPacket& packet) {
        std::lock_guard<std::mutex> lock(mutex);
        
        // Sıra numarası kontrolü
        if (packet.sequence < expectedSequence) {
            return false;  // Eski paket
        }
        
        // Buffer'a ekle
        uint32_t slot = packet.sequence % maxSlots;
        bufferSlots[slot] = packet;
        bufferSlots[slot].valid = true;
        
        // Jitter hesapla
        updateJitter(packet.timestamp);
        
        // Buffer boyutunu ayarla
        adjustBufferSize();
        
        expectedSequence = packet.sequence + 1;
        return true;
    }
    
    // Okuma
    bool read(float* output, uint32_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        
        uint32_t framesRead = 0;
        
        while (framesRead < frames && !isEmpty()) {
            AudioPacket& packet = bufferSlots[readSlot % maxSlots];
            
            if (packet.valid) {
                uint32_t framesToCopy = std::min(
                    frames - framesRead,
                    packet.frameCount - packet.readPos
                );
                
                std::copy(
                    packet.data + packet.readPos * channels,
                    packet.data + (packet.readPos + framesToCopy) * channels,
                    output + framesRead * channels
                );
                
                packet.readPos += framesToCopy;
                framesRead += framesToCopy;
                
                if (packet.readPos >= packet.frameCount) {
                    packet.valid = false;
                    readSlot++;
                }
            } else {
                // Packet yok, silence ekle
                uint32_t framesToSilence = std::min(
                    frames - framesRead,
                    framesPerPacket - remainingFrames
                );
                
                std::fill(
                    output + framesRead * channels,
                    output + (framesRead + framesToSilence) * channels,
                    0.0f
                );
                
                framesRead += framesToSilence;
                readSlot++;
            }
        }
        
        return framesRead > 0;
    }
    
    // Jitter hesaplama
    void updateJitter(uint32_t timestamp) {
        if (lastTimestamp > 0) {
            uint32_t delta = timestamp - lastTimestamp;
            
            // Exponential moving average
            jitterEstimate = 0.9f * jitterEstimate + 
                            0.1f * std::abs(static_cast<int32_t>(delta - expectedDelta));
        }
        
        lastTimestamp = timestamp;
        expectedDelta = framesPerPacket;
    }
    
    // Buffer boyutu ayarlama
    void adjustBufferSize() {
        // Jitter'e göre buffer boyutunu ayarla
        uint32_t targetBufferMs = static_cast<uint32_t>(
            jitterEstimate * 3);  // 3x safety margin
        
        targetBufferMs = std::clamp(targetBufferMs, 
                                     minBufferMs, maxBufferMs);
        
        // Yumuşak geçiş
        if (targetBufferMs > currentBufferMs) {
            currentBufferMs = std::min(currentBufferMs + 1, 
                                       targetBufferMs);
        } else if (targetBufferMs < currentBufferMs) {
            currentBufferMs = std::max(currentBufferMs - 1, 
                                       targetBufferMs);
        }
    }
    
    // Durum
    float getJitter() const { return jitterEstimate; }
    uint32_t getBufferMs() const { return currentBufferMs; }
    bool isEmpty() const {
        return bufferSlots[readSlot % maxSlots].valid == false;
    }
    
private:
    static const uint32_t maxSlots = 64;
    static const uint32_t framesPerPacket = 480;  // 10ms @ 48kHz
    
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t minBufferMs;
    uint32_t maxBufferMs;
    uint32_t currentBufferMs;
    
    std::mutex mutex;
    std::vector<AudioPacket> bufferSlots;
    
    uint32_t readSlot = 0;
    uint32_t expectedSequence = 0;
    uint32_t lastTimestamp = 0;
    uint32_t expectedDelta = 0;
    float jitterEstimate = 0.0f;
    uint32_t remainingFrames = 0;
};
```

### Network Stream Handler

```cpp
class NetworkStreamHandler {
public:
    NetworkStreamHandler(uint32_t sampleRate, 
                         uint32_t channels) 
        : jitterBuffer(sampleRate, channels),
          sampleRate(sampleRate),
          channels(channels) {}
    
    // UDP paket alımı
    void onPacketReceived(const uint8_t* data, size_t size) {
        // Paketi parse et
        AudioPacket packet;
        parsePacket(data, size, packet);
        
        // Jitter buffer'a ekle
        jitterBuffer.addPacket(packet);
    }
    
    // Okuma
    void process(float** output, uint32_t frameCount) {
        float* tempBuffer = new float[frameCount * channels];
        
        jitterBuffer.read(tempBuffer, frameCount);
        
        // De-interleave
        for (uint32_t ch = 0; ch < channels; ch++) {
            for (uint32_t i = 0; i < frameCount; i++) {
                output[ch][i] = tempBuffer[i * channels + ch];
            }
        }
        
        delete[] tempBuffer;
    }
    
    // İstatistikler
    float getJitter() const { return jitterBuffer.getJitter(); }
    uint32_t getBufferMs() const { return jitterBuffer.getBufferMs(); }
    uint32_t getPacketsReceived() const { return packetsReceived; }
    uint32_t getPacketsLost() const { return packetsLost; }
    
private:
    AdaptiveJitterBuffer jitterBuffer;
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t packetsReceived = 0;
    uint32_t packetsLost = 0;
    
    void parsePacket(const uint8_t* data, size_t size, 
                     AudioPacket& packet) {
        // Packet header parse
        uint32_t headerSize = 16;
        
        packet.sequence = *reinterpret_cast<const uint32_t*>(data);
        packet.timestamp = *reinterpret_cast<const uint32_t*>(data + 4);
        packet.frameCount = (size - headerSize) / (channels * sizeof(float));
        
        // Audio data
        packet.data = reinterpret_cast<const float*>(data + headerSize);
        packet.readPos = 0;
        packet.valid = true;
    }
};
```

### Buffer Pool

```cpp
// Pool-based buffer yönetimi
class BufferPool {
public:
    BufferPool(uint32_t poolSize, uint32_t bufferSize) 
        : poolSize(poolSize), bufferSize(bufferSize) {
        
        pool.resize(poolSize);
        for (uint32_t i = 0; i < poolSize; i++) {
            pool[i].data = new float[bufferSize];
            pool[i].inUse = false;
        }
    }
    
    ~BufferPool() {
        for (auto& slot : pool) {
            delete[] slot.data;
        }
    }
    
    float* acquire() {
        std::lock_guard<std::mutex> lock(mutex);
        
        for (auto& slot : pool) {
            if (!slot.inUse) {
                slot.inUse = true;
                return slot.data;
            }
        }
        
        // Pool dolu, yeni buffer oluştur
        return new float[bufferSize];
    }
    
    void release(float* buffer) {
        std::lock_guard<std::mutex> lock(mutex);
        
        for (auto& slot : pool) {
            if (slot.data == buffer) {
                slot.inUse = false;
                return;
            }
        }
        
        // Pool'da değil, serbest bırak
        delete[] buffer;
    }
    
private:
    struct BufferSlot {
        float* data;
        bool inUse;
    };
    
    uint32_t poolSize;
    uint32_t bufferSize;
    std::vector<BufferSlot> pool;
    std::mutex mutex;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class StreamBufferModule {
public:
    StreamBufferModule(uint32_t sampleRate, uint32_t channels);
    
    // Network stream
    void onNetworkPacket(const uint8_t* data, size_t size);
    
    // Dosya stream
    bool openFile(const std::string& url);
    void closeFile();
    
    // Okuma
    void process(float** output, uint32_t frameCount);
    
    // Buffer ayarları
    void setBufferMs(uint32_t ms);
    void setAdaptiveMode(bool adaptive);
    void setMaxJitter(uint32_t maxMs);
    
    // İstatistikler
    float getCurrentJitter() const;
    uint32_t getBufferMs() const;
    uint32_t getPacketsReceived() const;
    uint32_t getPacketsLost() const;
    float getPacketLossRate() const;
    
private:
    NetworkStreamHandler networkHandler;
    BufferPool bufferPool;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Jitter Toleransı | 100ms | 150ms |
| Buffer Latency | 20-200ms | 50ms |
| CPU | < 1% | 0.5% |
| Bellek | < 10MB | 8MB |
| Packet Loss Recovery | > 99% | 99.5% |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Jitter buffer
- **Faz 2**: Adaptif buffering
- **Faz 3**: Network stream handler
- **Faz 4**: Buffer pool
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
