---
title: "Bluetooth A2DP - k035-ag-ve-bluetooth-ses"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Bluetooth A2DP

> Klasör: `k035-ag-ve-bluetooth-ses` · Dilim: D01 (k018–k035) · Dosya: `bluetooth-a2dp.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

A2DP profili, codec müzakeresi, gecikme ve sürücü davranışı.

Bu belge; D01 diliminin (Ağ ve Bluetooth Ses) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** A2DP profili, codec müzakeresi, gecikme ve sürücü davranışı.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Ağ ve Bluetooth Ses)
- **Çapraz referanslar:** [[../k034-usb-audio/usb-audio-class.md]] · [[../k032-latency-optimization/latency-optimization.md]] · [[../k031-buffer-management/buffer-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### bluetooth-a2dp.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` (250 satır)

#### bluetooth-a2dp.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § (giriş) — L1–L9

---
title: "Bluetooth A2DP Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Bluetooth A2DP Sürücüsü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Genel Bakış` — L10–L12


Bluetooth A2DP (Advanced Audio Distribution Profile), kablosuz ses iletimi için standart profildir. COREMUSIC, yüksek kaliteli Bluetooth ses codecs'lerini (LDAC, aptX HD, LC3) destekleyerek kablosuz ses kalitesini artırır.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Teknik Detaylar` — L14–L177


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `API / Arayüz` — L179–L225


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Performans Metrikleri` — L227–L234


| Metrik | SBC | LDAC | aptX HD | LC3 |
|--------|-----|------|---------|-----|
| Bit Hızı | 328k | 990k | 576k | 345k |
| Latency | 100ms | 200ms | 120ms | 30ms |
| Kalite | İyi | Mükemmel | İyi | İyi |
| CPU | 1% | 3% | 2% | 1.5% |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Bağımlılıklar` — L236–L242


| Bağımlılık | Tür |
|------------|-----|
| BlueZ | Linux Bluetooth stack |
| libbluetooth | Sistem kütüphanesi |
| K1 BT Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Durum: Implementasyon` — L244–L250


- **Faz 1**: SBC codec, temel A2DP
- **Faz 2**: LDAC, aptX HD desteği
- **Faz 3**: LC3 (Bluetooth 5.2)
- **Faz 4**: Buffer optimizasyonu, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

### network-audio-drivers.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` (246 satır)

#### network-audio-drivers.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § (giriş) — L1–L9

---
title: "Ağ Ses Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Ağ Ses Sürücüleri (Dante, AVB, RAVENNA)


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Genel Bakış` — L10–L12


Ağ ses protokolleri, profesyonel ses endüstrisinde ses sinyallerinin Ethernet üzerinden iletilmesini sağlar. COREMUSIC, Dante, AVB ve RAVENNA protokollerini destekleyerek yüksek kanallı, düşük gecikmeli ağ sesi sağlar.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Teknik Detaylar` — L14–L155


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `API / Arayüz` — L157–L220


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Performans Metrikleri` — L222–L229


| Metrik | Dante | AVB | RAVENNA |
|--------|-------|-----|---------|
| Latency | 0.15ms | 1ms | 0.25ms |
| Max Channels | 512 | 64 | 512 |
| Bandwidth | 1 Gbps | 1 Gbps | 1 Gbps |
| CPU | 2% | 1.5% | 2% |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Bağımlılıklar` — L231–L238


| Bağımlılık | Tür |
|------------|-----|
| Dante SDK | Dış (Audinate) |
| AVB Libraries | Dış (IEEE) |
| libpcap | Ağ |
| K1 Network | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: Dante SDK entegrasyonu
- **Faz 2**: AVB/TSN desteği
- **Faz 3**: RAVENNA/AES67
- **Faz 4**: Multicast ve PTP optimizasyonu
- **Tahmini Süre**: 4 hafta (160 adam-saat)

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| │  [Disconnected] ← [Streaming] ← [Ready]    │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L167 |
| - Streaming → Disconnected: Bağlantı kesildi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L176 |
| void disconnectDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L191 |
| // Overrun/underrun kontrolü | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L131 |
| void disconnectDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L168 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| default:           return 79; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L123 |
| default:           frameSize = 128; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L151 |
| - **Faz 4**: Buffer optimizasyonu, hata yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L249 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| CODEC_LC3,      // Bluetooth 5.2 (düşük gecikme) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L53 |
| │ Quality      │ 990 kbps   │ 96 kHz       │ En İyi     │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L77 |
| │ Standard     │ 660 kbps   │ 48 kHz       │ İyi        │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L78 |
| │ Mobile       │ 330 kbps   │ 44.1 kHz     │ Orta       │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L79 |
| - Düşük gecikme: 30ms (10ms frame) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L89 |
| - Yüksek verimlilik: SBC'den %50 daha iyi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L91 |
| - Esnek örnekleme hızı: 8k-48kHz | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L92 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L128 |
| Bluetooth ses buffer yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L130 |
| // Bluetooth Jitter Buffer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L133 |
| struct BT_JitterBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L134 |
| uint32_t latencyMs;     // 50-200ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L137 |
| uint32_t jitterStats;   // Jitter istatistiği | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L139 |
| // Buffer boyutu hesaplama | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L142 |
| uint32_t calculateBTBuffer(BluetoothCodec codec, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L143 |
| uint32_t targetLatency) { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L144 |
| return (targetLatency * frameSize) / 1000; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L153 |
| // Buffer yapılandırması | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L204 |
| bool setBufferSize(uint32_t frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L205 |
| bool setLatency(uint32_t ms); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L206 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| CODEC_SBC,      // Varsayılan (her cihaz destekler) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L50 |
| │ Standart    │ Proprietary │ IEEE 802.1  │ AES67         │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L25 |
| ALC Networks tarafından AES67 standardı üzerine inşa edilmiştir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L72 |
| │  [Audio Source] → [AES67 Packet] → [Ethernet]     │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L77 |
| - **Faz 3**: RAVENNA/AES67 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L244 |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| bool createStream(const NetworkStreamConfig& config); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L171 |
| bool deleteStream(uint32_t streamId); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L172 |
| uint32_t streamId = driver.createStream(config); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L218 |
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
| `A2DP` | 12 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L2 |
| `Bluetooth` | 25 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L2 |
| `latency` | 9 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L38 |
| `buffer` | 19 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` L128 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## Teknik Detaylar | L14–L177 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## API / Arayüz | L179–L225 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## Performans Metrikleri | L227–L234 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## Bağımlılıklar | L236–L242 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | ## Durum: Implementasyon | L244–L250 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Teknik Detaylar | L14–L155 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## API / Arayüz | L157–L220 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Performans Metrikleri | L222–L229 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Bağımlılıklar | L231–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Durum: Implementasyon | L240–L246 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` § `Durum: Implementasyon` — L244–L250


- **Faz 1**: SBC codec, temel A2DP
- **Faz 2**: LDAC, aptX HD desteği
- **Faz 3**: LC3 (Bluetooth 5.2)
- **Faz 4**: Buffer optimizasyonu, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: Dante SDK entegrasyonu
- **Faz 2**: AVB/TSN desteği
- **Faz 3**: RAVENNA/AES67
- **Faz 4**: Multicast ve PTP optimizasyonu
- **Tahmini Süre**: 4 hafta (160 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
