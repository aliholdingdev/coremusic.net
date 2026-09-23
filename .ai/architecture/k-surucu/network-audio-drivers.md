---
title: "Ağ Ses Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# Ağ Ses Sürücüleri (Dante, AVB, RAVENNA)

## Genel Bakış

Ağ ses protokolleri, profesyonel ses endüstrisinde ses sinyallerinin Ethernet üzerinden iletilmesini sağlar. COREMUSIC, Dante, AVB ve RAVENNA protokollerini destekleyerek yüksek kanallı, düşük gecikmeli ağ sesi sağlar.

## Teknik Detaylar

### Ağ Ses Protokolleri Karşılaştırması

```
┌─────────────────────────────────────────────────────────┐
│              Ağ Ses Protokolleri                        │
├─────────────┬─────────────┬─────────────┬───────────────┤
│             │   Dante     │    AVB      │   RAVENNA     │
├─────────────┼─────────────┼─────────────┼───────────────┤
│ Organization│ Audinate    │ IEEE        │ AL Networks   │
│ Standart    │ Proprietary │ IEEE 802.1  │ AES67         │
│ Bandwidth   │ 1 Gbps      │ 1 Gbps      │ 1 Gbps        │
│ Latency     │ 0.15-5ms    │ 1-10ms      │ 0.25-5ms      │
│ Max Channels│ 512         │ 64          │ 512           │
│ Sync        │ PTPv2       │ gPTP        │ PTPv2         │
│ Kompress    │ Evet        │ Hayır       │ Hayır         │
│ Sample Rate │ Up to 192k  │ Up to 96k   │ Up to 192k    │
└─────────────┴─────────────┴─────────────┴───────────────┘
```

### Dante Protokolü

Audinate tarafından geliştirilen Dante:

**Dante Akış Akışı**:
```
┌──────────────────────────────────────────────────────┐
│                Dante Network                         │
│                                                      │
│  [Source] → [Dante Encoder] → [Ethernet Switch]     │
│      ↓                           ↓                   │
│  [Audio In]               [Multicast Group]         │
│                              ↓                       │
│                     [Dante Decoder] → [Sink]         │
└──────────────────────────────────────────────────────┘
```

**Dante Özellikleri**:
- Automatic device discovery
- Dynamic audio routing
- Redundant networking (primary/secondary)
- Dante Controller yazılımı ile yönetim

### AVB/TSN Protokolü

IEEE 802.1 Time-Sensitive Networking:

**AVB Katmanları**:
| Katman | Protokol | Görev |
|--------|----------|-------|
| L2 | 802.1Qav | Queuing and Forwarding |
| L3 | 802.1AS | Time Synchronization (gPTP) |
| L4 | 1722 | Audio/Video Transport Protocol |
| L5 | 61883 | IEC 61883 (FireWire over AVB) |

### RAVENNA Protokolü

ALC Networks tarafından AES67 standardı üzerine inşa edilmiştir:

```
RAVENNA Akışı:
┌─────────────────────────────────────────────────────┐
│  [Audio Source] → [AES67 Packet] → [Ethernet]     │
│       ↓                    ↓              ↓         │
│  [PTP Sync]         [Multicast]    [IGMP Snooping] │
│       ↓                    ↓              ↓         │
│  [Clock Recovery]  [Audio Decode]  [Buffering]      │
│       ↓                    ↓              ↓         │
│  [Audio Sink] → [K3 Engine] → [Playback]          │
└─────────────────────────────────────────────────────┘
```

### PTP Zamanlama

Tüm ağ ses protokolleri Precision Time Protocol (PTP) kullanır:

```cpp
// PTP zaman damgası
struct PTPTimestamp {
    uint64_t seconds;
    uint32_t nanoseconds;
};

// PTP senkronizasyonu
void synchronizePTP() {
    // Grandmaster clock'ı keşfet
    // Offset hesapla
    // Local clock'ı ayarla
    // Drift compensation uygula
}
```

**PTP Doğruluğu**:
- Donanım timestamp: < 1μs
- Yazılım timestamp: < 100μs

### Jitter Buffer

Ağ sesi için jitter buffer kritiktir:

```cpp
// Jitter buffer yapısı
struct NetworkJitterBuffer {
    void** slots;           // Buffer slotları
    uint32_t slotCount;     // Slot sayısı
    uint32_t readIndex;     // Okuma indeksi
    uint32_t writeIndex;    // Yazma indeksi
    uint32_t latencyMs;     // Hedef gecikme
    bool adaptive;          // Adaptif mod
};

// Adaptif jitter buffer
void updateJitterBuffer(NetworkJitterBuffer* buf, 
                         uint32_t currentJitter) {
    // Jitter istatistiğini güncelle
    // Buffer boyutunu ayarla
    // Overrun/underrun kontrolü
}
```

### Multicast Yönetim

Ağ sesi multicast adresleri yönetimi:

| Protokol | Multicast Aralığı |
|----------|-------------------|
| Dante | 239.x.x.x |
| AVB | 91:E0:F0:xx:xx:xx |
| RAVENNA | 239.x.x.x (IGMP) |

### Donanım Gereksinimleri

Ağ sesi için donanım gereksinimleri:

| Özellik | Minimum | Önerilen |
|---------|---------|----------|
| Ethernet | 100 Mbps | 1 Gbps |
| Switch | Manageable | AVB destekli |
| PTP | Yazılım | Donanım timestamp |
| CPU | 1 core | 2+ core |
| RAM | 256 MB | 512 MB |

## API / Arayüz

```cpp
class NetworkAudioDriver {
public:
    bool initialize(NetworkAudioProtocol protocol);
    void shutdown();
    
    // Cihaz keşfi
    std::vector<NetworkDevice> discoverDevices() const;
    bool connectToDevice(const std::string& deviceId);
    void disconnectDevice();
    
    // Akış yapılandırması
    bool createStream(const NetworkStreamConfig& config);
    bool deleteStream(uint32_t streamId);
    bool startStream(uint32_t streamId);
    bool stopStream(uint32_t streamId);
    
    // Yönlendirme
    bool routeAudio(uint32_t srcStreamId, 
                    uint32_t dstStreamId);
    bool unrouteAudio(uint32_t streamId);
    
    // Multicast
    bool joinMulticastGroup(const std::string& address);
    bool leaveMulticastGroup(const std::string& address);
    
    // PTP
    bool synchronizePTP();
    PTPTimestamp getCurrentTime() const;
    
    // İstatistikler
    NetworkStats getStats() const;
};

struct NetworkStreamConfig {
    std::string name;
    uint32_t channels;
    uint32_t sampleRate;
    uint32_t bitDepth;
    uint32_t packetSize;
    bool redundant;         // Primary/Secondary
    NetworkAudioProtocol protocol;
};

// Kullanım örneği
NetworkAudioDriver driver;
driver.initialize(NETWORK_AUDIO_DANTE);

auto devices = driver.discoverDevices();
driver.connectToDevice(devices[0].id);

NetworkStreamConfig config;
config.name = "COREMUSIC Main";
config.channels = 8;
config.sampleRate = 96000;
config.bitDepth = 24;
config.packetSize = 48;
config.protocol = NETWORK_AUDIO_DANTE;

uint32_t streamId = driver.createStream(config);
driver.startStream(streamId);
```

## Performans Metrikleri

| Metrik | Dante | AVB | RAVENNA |
|--------|-------|-----|---------|
| Latency | 0.15ms | 1ms | 0.25ms |
| Max Channels | 512 | 64 | 512 |
| Bandwidth | 1 Gbps | 1 Gbps | 1 Gbps |
| CPU | 2% | 1.5% | 2% |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Dante SDK | Dış (Audinate) |
| AVB Libraries | Dış (IEEE) |
| libpcap | Ağ |
| K1 Network | İç katman |

## Durum: Implementasyon

- **Faz 1**: Dante SDK entegrasyonu
- **Faz 2**: AVB/TSN desteği
- **Faz 3**: RAVENNA/AES67
- **Faz 4**: Multicast ve PTP optimizasyonu
- **Tahmini Süre**: 4 hafta (160 adam-saat)
