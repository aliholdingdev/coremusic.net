---
title: "K017 Bluetooth A2DP Akışı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K017 — Bluetooth A2DP Akışı

> **K numarası:** K017 · **Klasör:** `k017-uzak-bluetooth-usb` · **Dosya:** `bluetooth-a2dp-akis`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `devops-engineer` (ikincil)
> **Klasör amacı:** Bluetooth A2DP akışının, ağ üzerinden ses (network audio) sürücülerinin ve USB-audio sınıf uç cihazların arayüz/kısıt tanımlarını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **Bluetooth A2DP Akışı** konusunu ele alır. Kapsamı: A2DP kaynak/hedef rolleri, codec/packet davranışı, senkronizasyon ve kesinti (dropout) senaryoları.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Uygulama ]
            │
            ▼
      [ A2DP (Bluetooth) / ağ (network audio) ]
            │
            ▼
      [ Ses codec/akış katmanı ]
            │
            ▼
      [ USB-audio class endpoint ]
            │
            ▼
      [ Uç cihaz (donanım) ]
```

**Akış notları:**

1. **Uygulama** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **A2DP (Bluetooth) / ağ (network audio)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Ses codec/akış katmanı** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **USB-audio class endpoint** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Uç cihaz (donanım)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | 251 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | 247 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/bluetooth-a2dp.md`

| Codec | Bit Hızı | Örnekleme Hızı | Bit Derinliği | Latency |
|-------|----------|----------------|---------------|---------|
| SBC | 328 kbps | 48 kHz | 16-bit | 100ms |
| LDAC | 990 kbps | 96 kHz | 24-bit | 200ms |
| aptX HD | 576 kbps | 48 kHz | 24-bit | 120ms |
| LC3 | 345 kbps | 48 kHz | 24-bit | 30ms |

### 4.2 · `k2-surucu/bluetooth-a2dp.md`

| Metrik | SBC | LDAC | aptX HD | LC3 |
|--------|-----|------|---------|-----|
| Bit Hızı | 328k | 990k | 576k | 345k |
| Latency | 100ms | 200ms | 120ms | 30ms |
| Kalite | İyi | Mükemmel | İyi | İyi |
| CPU | 1% | 3% | 2% | 1.5% |

### 4.3 · `k2-surucu/bluetooth-a2dp.md`

| Bağımlılık | Tür |
|------------|-----|
| BlueZ | Linux Bluetooth stack |
| libbluetooth | Sistem kütüphanesi |
| K1 BT Core | İç katman |

### 4.4 · `k2-surucu/network-audio-drivers.md`

| Katman | Protokol | Görev |
|--------|----------|-------|
| L2 | 802.1Qav | Queuing and Forwarding |
| L3 | 802.1AS | Time Synchronization (gPTP) |
| L4 | 1722 | Audio/Video Transport Protocol |
| L5 | 61883 | IEC 61883 (FireWire over AVB) |

### 4.5 · `k2-surucu/network-audio-drivers.md`

| Protokol | Multicast Aralığı |
|----------|-------------------|
| Dante | 239.x.x.x |
| AVB | 91:E0:F0:xx:xx:xx |
| RAVENNA | 239.x.x.x (IGMP) |

### 4.6 · `k2-surucu/network-audio-drivers.md`

| Özellik | Minimum | Önerilen |
|---------|---------|----------|
| Ethernet | 100 Mbps | 1 Gbps |
| Switch | Manageable | AVB destekli |
| PTP | Yazılım | Donanım timestamp |
| CPU | 1 core | 2+ core |
| RAM | 256 MB | 512 MB |

### 4.7 · `k2-surucu/network-audio-drivers.md`

| Metrik | Dante | AVB | RAVENNA |
|--------|-------|-----|---------|
| Latency | 0.15ms | 1ms | 0.25ms |
| Max Channels | 512 | 64 | 512 |
| Bandwidth | 1 Gbps | 1 Gbps | 1 Gbps |
| CPU | 2% | 1.5% | 2% |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/bluetooth-a2dp.md` | H1 | Bluetooth A2DP Sürücüsü |
| 2 | `k2-surucu/bluetooth-a2dp.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/bluetooth-a2dp.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/bluetooth-a2dp.md` | H3 | Bluetooth Ses Akışı |
| 5 | `k2-surucu/bluetooth-a2dp.md` | H3 | Codec Desteği |
| 6 | `k2-surucu/bluetooth-a2dp.md` | H3 | Codec Seçimi |
| 7 | `k2-surucu/bluetooth-a2dp.md` | H3 | LDAC Codec Detayları |
| 8 | `k2-surucu/bluetooth-a2dp.md` | H3 | LC3 Codec Detayları |
| 9 | `k2-surucu/bluetooth-a2dp.md` | H3 | Packet Yapısı |
| 10 | `k2-surucu/bluetooth-a2dp.md` | H3 | Buffer Yönetimi |
| 11 | `k2-surucu/bluetooth-a2dp.md` | H3 | A2DP State Machine |
| 12 | `k2-surucu/bluetooth-a2dp.md` | H2 | API / Arayüz |
| 13 | `k2-surucu/bluetooth-a2dp.md` | H2 | Performans Metrikleri |
| 14 | `k2-surucu/bluetooth-a2dp.md` | H2 | Bağımlılıklar |
| 15 | `k2-surucu/bluetooth-a2dp.md` | H2 | Durum: Implementasyon |
| 16 | `k2-surucu/network-audio-drivers.md` | H1 | Ağ Ses Sürücüleri (Dante, AVB, RAVENNA) |
| 17 | `k2-surucu/network-audio-drivers.md` | H2 | Genel Bakış |
| 18 | `k2-surucu/network-audio-drivers.md` | H2 | Teknik Detaylar |
| 19 | `k2-surucu/network-audio-drivers.md` | H3 | Ağ Ses Protokolleri Karşılaştırması |
| 20 | `k2-surucu/network-audio-drivers.md` | H3 | Dante Protokolü |
| 21 | `k2-surucu/network-audio-drivers.md` | H3 | AVB/TSN Protokolü |
| 22 | `k2-surucu/network-audio-drivers.md` | H3 | RAVENNA Protokolü |
| 23 | `k2-surucu/network-audio-drivers.md` | H3 | PTP Zamanlama |
| 24 | `k2-surucu/network-audio-drivers.md` | H3 | Jitter Buffer |
| 25 | `k2-surucu/network-audio-drivers.md` | H3 | Multicast Yönetim |
| 26 | `k2-surucu/network-audio-drivers.md` | H3 | Donanım Gereksinimleri |
| 27 | `k2-surucu/network-audio-drivers.md` | H2 | API / Arayüz |
| 28 | `k2-surucu/network-audio-drivers.md` | H2 | Performans Metrikleri |
| 29 | `k2-surucu/network-audio-drivers.md` | H2 | Bağımlılıklar |
| 30 | `k2-surucu/network-audio-drivers.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md`


### Bluetooth A2DP Sürücüsü

#### Genel Bakış

Bluetooth A2DP (Advanced Audio Distribution Profile), kablosuz ses iletimi için standart profildir. COREMUSIC, yüksek kaliteli Bluetooth ses codecs'lerini (LDAC, aptX HD, LC3) destekleyerek kablosuz ses kalitesini artırır.

#### Teknik Detaylar

##### Bluetooth Ses Akışı

```
┌─────────────────────────────────────────────────────┐
│            Bluetooth A2DP Akışı                    │
│                                                     │
│  [Kaynak] → [Codec] → [L2CAP] → [HCI] → [RFCOMM]  │
│      ↓         ↓         ↓         ↓         ↓       │
│  [Audio]  [Encode]  [Packet]  [Transfer]  [Air]     │
│                                                     │
│         ↓ Air Interface (Bluetooth) ↓              │
│                                                     │
│  [RFCOMM] → [HCI] → [L2CAP] → [Codec] → [Sink]    │
│      ↓         ↓         ↓         ↓         ↓       │
│  [Receive] [Decode] [Reassemble] [Process] [Output] │
└─────────────────────────────────────────────────────┘
```

##### Codec Desteği

COREMUSIC aşağıdaki Bluetooth ses codecs'lerini destekler:

| Codec | Bit Hızı | Örnekleme Hızı | Bit Derinliği | Latency |
|-------|----------|----------------|---------------|---------|
| SBC | 328 kbps | 48 kHz | 16-bit | 100ms |
| LDAC | 990 kbps | 96 kHz | 24-bit | 200ms |
| aptX HD | 576 kbps | 48 kHz | 24-bit | 120ms |
| LC3 | 345 kbps | 48 kHz | 24-bit | 30ms |

##### Codec Seçimi

```cpp
// Desteklenen codec listesi
enum BluetoothCodec {
    CODEC_SBC,      // Varsayılan (her cihaz destekler)
    CODEC_LDAC,     // Sony (yüksek kalite)
    CODEC_APTX_HD,  // Qualcomm (yüksek kalite)
    CODEC_LC3,      // Bluetooth 5.2 (düşük gecikme)
    CODEC_AAC       // Apple (iyi kalite)
};

// Codec seçimi
BluetoothCodec selectCodec(const BluetoothDevice& device) {
    // Cihazın desteklediği codec'leri kontrol et
    if (device.supportsCodec(CODEC_LC3)) return CODEC_LC3;
    if (device.supportsCodec(CODEC_LDAC)) return CODEC_LDAC;
    if (device.supportsCodec(CODEC_APTX_HD)) return CODEC_APTX_HD;
    if (device.supportsCodec(CODEC_AAC)) return CODEC_AAC;
    return CODEC_SBC;
}
```

##### LDAC Codec Detayları

Sony LDAC, yüksek çözünürlüklü Bluetooth ses için:

```
LDAC Modları:
┌──────────────┬────────────┬──────────────┬────────────┐
│    Mod       │  Bit Hızı  │ Örnekleme    │ Kalite     │
├──────────────┼────────────┼──────────────┼────────────┤
│ Quality      │ 990 kbps   │ 96 kHz       │ En İyi     │
│ Standard     │ 660 kbps   │ 48 kHz       │ İyi        │
│ Mobile       │ 330 kbps   │ 44.1 kHz     │ Orta       │
└──────────────┴────────────┴──────────────┴────────────┘
```

##### LC3 Codec Detayları

Bluetooth 5.2 ile gelen LC3 (Low Complexity Communication Codec):

```
LC3 Özellikleri:
- Düşük gecikme: 30ms (10ms frame)
- Esnek bit hızı: 160-345 kbps
- Yüksek verimlilik: SBC'den %50 daha iyi
- Esnek örnekleme hızı: 8k-48kHz
```

##### Packet Yapısı

Bluetooth ses paketleri:

```cpp
// A2DP Media Packet
struct A2DPMediaPacket {
    uint8_t version;        // 2 bits
    uint8_t padding;        // 1 bit
    uint8_t extension;      // 1 bit
    uint8_t cc;             // 4 bits (codec dependent)
    uint8_t marker;         // 1 bit
    uint8_t pt;             // 7 bits (payload type)
    uint16_t sequenceNumber;
    uint32_t timestamp;
    uint32_t ssrc;
    // Payload (codec encoded data)
    uint8_t payload[];      // Değişken boyut
};

// Packet boyutu hesaplama
uint32_t calculatePacketSize(uint32_t frameSize, 
                              BluetoothCodec codec) {
    switch (codec) {
        case CODEC_SBC:    return 79;    // max
        case CODEC_LDAC:   return 690;   // 990kbps
        case CODEC_APTX_HD: return 680;  // 576kbps
        case CODEC_LC3:    return 155;   // 345kbps
        default:           return 79;
    }
}
```

##### Buffer Yönetimi

Bluetooth ses buffer yönetimi:

```cpp
// Bluetooth Jitter Buffer
struct BT_JitterBuffer {
    void** slots;
    uint32_t slotCount;     // Genellikle 3-5
    uint32_t latencyMs;     // 50-200ms
    bool adaptive;
    uint32_t jitterStats;   // Jitter istatistiği
};

// Buffer boyutu hesaplama
uint32_t calculateBTBuffer(BluetoothCodec codec, 
                            uint32_t targetLatency) {
    uint32_t frameSize;
    switch (codec) {
        case CODEC_SBC:    frameSize = 128; break;
        case CODEC_LDAC:   frameSize = 256; break;
        case CODEC_APTX_HD: frameSize = 256; break;
        case CODEC_LC3:    frameSize = 120; break;
        default:           frameSize = 128;
    }
    return (targetLatency * frameSize) / 1000;
}
```

##### A2DP State Machine

A2DP bağlantı yönetimi:

```
A2DP States:
┌─────────────────────────────────────────────┐
│                                             │
│  [Idle] → [Connecting] → [Configuring]     │
│                              ↓              │
│  [Disconnected] ← [Streaming] ← [Ready]    │
│                                             │
└─────────────────────────────────────────────┘

State Transitions:
- Idle → Connecting: Cihaz keşfi
- Connecting → Configuring: Codec seçimi
- Configuring → Ready: Yapılandırma tamam
- Ready → Streaming: Akış başlat
- Streaming → Disconnected: Bağlantı kesildi
```

#### API / Arayüz

```cpp
class BluetoothA2DPDriver {
public:
    bool initialize();
    void shutdown();
    
    // Cihaz yönetimi
    std::vector<BTDevice> scanDevices() const;
    bool pairDevice(const std::string& deviceId);
    bool connectDevice(const std::string& deviceId);
    void disconnectDevice();
    
    // Codec yapılandırması
    bool setCodec(BluetoothCodec codec);
    BluetoothCodec getCurrentCodec() const;
    std::vector<BluetoothCodec> getSupportedCodecs(
        const std::string& deviceId) const;
    
    // Akış kontrolü
    bool startPlayback();
    bool stopPlayback();
    bool isStreaming() const;
    
    // Buffer yapılandırması
    bool setBufferSize(uint32_t frames);
    bool setLatency(uint32_t ms);
    
    // Durum
    BTConnectionState getConnectionState() const;
    uint32_t getSignalStrength() const;
    double getCurrentBitrate() const;
};

// Kullanım örneği
BluetoothA2DPDriver driver;
driver.initialize();

auto devices = driver.scanDevices();
if (!devices.empty()) {
    driver.connectDevice(devices[0].id);
    driver.setCodec(CODEC_LDAC);
    driver.setBufferSize(256);
    driver.startPlayback();
}
```

#### Performans Metrikleri

| Metrik | SBC | LDAC | aptX HD | LC3 |
|--------|-----|------|---------|-----|
| Bit Hızı | 328k | 990k | 576k | 345k |
| Latency | 100ms | 200ms | 120ms | 30ms |
| Kalite | İyi | Mükemmel | İyi | İyi |
| CPU | 1% | 3% | 2% | 1.5% |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| BlueZ | Linux Bluetooth stack |
| libbluetooth | Sistem kütüphanesi |
| K1 BT Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: SBC codec, temel A2DP
- **Faz 2**: LDAC, aptX HD desteği
- **Faz 3**: LC3 (Bluetooth 5.2)
- **Faz 4**: Buffer optimizasyonu, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md`


### Ağ Ses Sürücüleri (Dante, AVB, RAVENNA)

#### Genel Bakış

Ağ ses protokolleri, profesyonel ses endüstrisinde ses sinyallerinin Ethernet üzerinden iletilmesini sağlar. COREMUSIC, Dante, AVB ve RAVENNA protokollerini destekleyerek yüksek kanallı, düşük gecikmeli ağ sesi sağlar.

#### Teknik Detaylar

##### Ağ Ses Protokolleri Karşılaştırması

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

##### Dante Protokolü

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

##### AVB/TSN Protokolü

IEEE 802.1 Time-Sensitive Networking:

**AVB Katmanları**:
| Katman | Protokol | Görev |
|--------|----------|-------|
| L2 | 802.1Qav | Queuing and Forwarding |
| L3 | 802.1AS | Time Synchronization (gPTP) |
| L4 | 1722 | Audio/Video Transport Protocol |
| L5 | 61883 | IEC 61883 (FireWire over AVB) |

##### RAVENNA Protokolü

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

##### PTP Zamanlama

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

##### Jitter Buffer

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

##### Multicast Yönetim

Ağ sesi multicast adresleri yönetimi:

| Protokol | Multicast Aralığı |
|----------|-------------------|
| Dante | 239.x.x.x |
| AVB | 91:E0:F0:xx:xx:xx |
| RAVENNA | 239.x.x.x (IGMP) |

##### Donanım Gereksinimleri

Ağ sesi için donanım gereksinimleri:

| Özellik | Minimum | Önerilen |
|---------|---------|----------|
| Ethernet | 100 Mbps | 1 Gbps |
| Switch | Manageable | AVB destekli |
| PTP | Yazılım | Donanım timestamp |
| CPU | 1 core | 2+ core |
| RAM | 256 MB | 512 MB |

#### API / Arayüz

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

#### Performans Metrikleri

| Metrik | Dante | AVB | RAVENNA |
|--------|-------|-----|---------|
| Latency | 0.15ms | 1ms | 0.25ms |
| Max Channels | 512 | 64 | 512 |
| Bandwidth | 1 Gbps | 1 Gbps | 1 Gbps |
| CPU | 2% | 1.5% | 2% |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Dante SDK | Dış (Audinate) |
| AVB Libraries | Dış (IEEE) |
| libpcap | Ağ |
| K1 Network | İç katman |

#### Durum: Implementasyon

- **Faz 1**: Dante SDK entegrasyonu
- **Faz 2**: AVB/TSN desteği
- **Faz 3**: RAVENNA/AES67
- **Faz 4**: Multicast ve PTP optimizasyonu
- **Tahmini Süre**: 4 hafta (160 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Kullanılan codec (SBC/AAC vb.) vault'ta açıkça sabitlenmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Ölçülmüş Bluetooth gecikme/paket kaybı istatistiği yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Radyo girişiminde paket kaybı → atlamalı ses (kaynak: `bluetooth-a2dp`).
2. A2DP hedef modda başka kaynağından çakışma (kaynak: `bluetooth-a2dp`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k016-linux-ses/index]]` | ↑ | ALSA/PipeWire üzerinden uç cihazlara ulaşım | `.ai/architecture/k016-linux-ses/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↑ | Sürücü yığını ve buffer kuralları | `.ai/architecture/k014-surucu-yigin/index.md` |
| `[[../k012-dijital-arayuz/index]]` | ↑ | USB-audio fiziksel/dijital arayüzü | `.ai/architecture/k012-dijital-arayuz/index.md` |
| `[[../k015-platform-suruculeri/index]]` | ↔ | Platform bazlı uç nokta davranışları | `.ai/architecture/k015-platform-suruculeri/index.md` |

Yerel dosyalar:

- `[[bluetooth-a2dp-akis]]` — Bluetooth A2DP Akışı
- `[[uzak-ses-suruculeri]]` — Uzak Ses Sürücüleri (USB · Ağ)

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K017 · Bluetooth A2DP Akışı — SSOT: `.ai/architecture/k017-uzak-bluetooth-usb/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
