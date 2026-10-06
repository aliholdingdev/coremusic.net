---
title: "K017 Uzak Ses Sürücüleri (USB · Ağ)"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K017 — Uzak Ses Sürücüleri (USB · Ağ)

> **K numarası:** K017 · **Klasör:** `k017-uzak-bluetooth-usb` · **Dosya:** `uzak-ses-suruculeri`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `devops-engineer` (ikincil)
> **Klasör amacı:** Bluetooth A2DP akışının, ağ üzerinden ses (network audio) sürücülerinin ve USB-audio sınıf uç cihazların arayüz/kısıt tanımlarını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **Uzak Ses Sürücüleri (USB · Ağ)** konusunu ele alır. Kapsamı: Ağ üzerinden ses sürücülerinin keşif/senkronizasyonu ile USB-audio class uç cihazların endpoint davranışı.

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
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | 247 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | 255 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/network-audio-drivers.md`

| Katman | Protokol | Görev |
|--------|----------|-------|
| L2 | 802.1Qav | Queuing and Forwarding |
| L3 | 802.1AS | Time Synchronization (gPTP) |
| L4 | 1722 | Audio/Video Transport Protocol |
| L5 | 61883 | IEC 61883 (FireWire over AVB) |

### 4.2 · `k2-surucu/network-audio-drivers.md`

| Protokol | Multicast Aralığı |
|----------|-------------------|
| Dante | 239.x.x.x |
| AVB | 91:E0:F0:xx:xx:xx |
| RAVENNA | 239.x.x.x (IGMP) |

### 4.3 · `k2-surucu/network-audio-drivers.md`

| Özellik | Minimum | Önerilen |
|---------|---------|----------|
| Ethernet | 100 Mbps | 1 Gbps |
| Switch | Manageable | AVB destekli |
| PTP | Yazılım | Donanım timestamp |
| CPU | 1 core | 2+ core |
| RAM | 256 MB | 512 MB |

### 4.4 · `k2-surucu/network-audio-drivers.md`

| Metrik | Dante | AVB | RAVENNA |
|--------|-------|-----|---------|
| Latency | 0.15ms | 1ms | 0.25ms |
| Max Channels | 512 | 64 | 512 |
| Bandwidth | 1 Gbps | 1 Gbps | 1 Gbps |
| CPU | 2% | 1.5% | 2% |

### 4.5 · `k2-surucu/usb-audio-class.md`

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Adaptive** | USB host saatine senkronize | Varsayılan mod |
| **Async** | Device kendi saatini kullanır | Profesyonel cihazlar |

### 4.6 · `k2-surucu/usb-audio-class.md`

| Format | Bit Derinliği | Örnek Hızı | Kanal |
|--------|---------------|------------|-------|
| PCM | 16, 24, 32 | 44.1k-384k | 1-32 |
| IEEE Float | 32 | 44.1k-192k | 1-32 |
| DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8 |

### 4.7 · `k2-surucu/usb-audio-class.md`

| Hata | Neden | Çözüm |
|------|-------|-------|
| `USB_ERROR_STALL` | Transfer durduruldu | Endpoint'i resetle |
| `USB_ERROR_NAK` | Cihaz meşgul | Yeniden dene |
| `USB_ERROR_TIMEOUT` | Zaman aşımı | Bandwidth'i artır |
| `USB_ERROR_OVERFLOW` | Buffer taştı | Buffer boyutunu artır |

### 4.8 · `k2-surucu/usb-audio-class.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/network-audio-drivers.md` | H1 | Ağ Ses Sürücüleri (Dante, AVB, RAVENNA) |
| 2 | `k2-surucu/network-audio-drivers.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/network-audio-drivers.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/network-audio-drivers.md` | H3 | Ağ Ses Protokolleri Karşılaştırması |
| 5 | `k2-surucu/network-audio-drivers.md` | H3 | Dante Protokolü |
| 6 | `k2-surucu/network-audio-drivers.md` | H3 | AVB/TSN Protokolü |
| 7 | `k2-surucu/network-audio-drivers.md` | H3 | RAVENNA Protokolü |
| 8 | `k2-surucu/network-audio-drivers.md` | H3 | PTP Zamanlama |
| 9 | `k2-surucu/network-audio-drivers.md` | H3 | Jitter Buffer |
| 10 | `k2-surucu/network-audio-drivers.md` | H3 | Multicast Yönetim |
| 11 | `k2-surucu/network-audio-drivers.md` | H3 | Donanım Gereksinimleri |
| 12 | `k2-surucu/network-audio-drivers.md` | H2 | API / Arayüz |
| 13 | `k2-surucu/network-audio-drivers.md` | H2 | Performans Metrikleri |
| 14 | `k2-surucu/network-audio-drivers.md` | H2 | Bağımlılıklar |
| 15 | `k2-surucu/network-audio-drivers.md` | H2 | Durum: Implementasyon |
| 16 | `k2-surucu/usb-audio-class.md` | H1 | USB Audio Class 2.0 Sürücüsü |
| 17 | `k2-surucu/usb-audio-class.md` | H2 | Genel Bakış |
| 18 | `k2-surucu/usb-audio-class.md` | H2 | Teknik Detaylar |
| 19 | `k2-surucu/usb-audio-class.md` | H3 | UAC2 Mimarisi |
| 20 | `k2-surucu/usb-audio-class.md` | H3 | Isochronous Transfer |
| 21 | `k2-surucu/usb-audio-class.md` | H3 | Adaptive ve Async Modlar |
| 22 | `k2-surucu/usb-audio-class.md` | H3 | Clock Source Yönetimi |
| 23 | `k2-surucu/usb-audio-class.md` | H3 | Format Desteği |
| 24 | `k2-surucu/usb-audio-class.md` | H3 | Endpoint Yapılandırması |
| 25 | `k2-surucu/usb-audio-class.md` | H3 | Bandwidth Yönetimi |
| 26 | `k2-surucu/usb-audio-class.md` | H3 | Buffer Yönetimi |
| 27 | `k2-surucu/usb-audio-class.md` | H3 | Hata Yönetimi |
| 28 | `k2-surucu/usb-audio-class.md` | H2 | API / Arayüz |
| 29 | `k2-surucu/usb-audio-class.md` | H2 | Performans Metrikleri |
| 30 | `k2-surucu/usb-audio-class.md` | H2 | Bağımlılıklar |
| 31 | `k2-surucu/usb-audio-class.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md`


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


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md`


### USB Audio Class 2.0 Sürücüsü

#### Genel Bakış

USB Audio Class 2.0 (UAC2), USB üzerinden yüksek kaliteli ses giriş/çıkışı için evrensel bir standarttır. COREMUSIC, UAC2 protokolünü destekleyerek yüksek çözünürlüklü ses cihazlarıyla (DAC, ADC,ampler) doğrudan çalışır.

#### Teknik Detaylar

##### UAC2 Mimarisi

```
┌─────────────────────────────────────────────┐
│            COREMUSIC Engine (K3)            │
├─────────────────────────────────────────────┤
│         USB Audio Class 2.0 Driver          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Isoch    │  │ Control  │  │ Clock    │  │
│  │ Endpoint │  │ Request │  │ Source   │  │
│  └──────────┘  └──────────┘  └──────────┘  │
├─────────────────────────────────────────────┤
│         USB Host Controller Driver          │
│  ┌──────────┐  ┌──────────┐                │
│  │ xHCI     │  │ EHCI     │                │
│  └──────────┘  └──────────┘                │
├─────────────────────────────────────────────┤
│         USB Hardware                        │
│  ┌──────────────────────────────────────┐   │
│  │  USB Port → USB Cable → Audio Device │   │
│  └──────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

##### Isochronous Transfer

USB ses, isochronous transfer modunu kullanır:

**Isochronous Transfer Özellikleri**:
- Zaman duyarlı (time-sensitive) veri transferi
- Garantili bandwidth
- Hata düzeltme yok (veya sınırlı)
- Her frame'de sabit boyut veri

**Frame Yapısı**:
```
USB Frame (1ms @ Full Speed, 125μs @ High Speed)
┌─────────────────────────────────────────────────┐
│  Frame N     │  Frame N+1   │  Frame N+2        │
│  ┌─────────┐ │  ┌─────────┐ │  ┌─────────┐     │
│  │ Packet  │ │  │ Packet  │ │  │ Packet  │     │
│  │ (256B)  │ │  │ (256B)  │ │  │ (256B)  │     │
│  └─────────┘ │  └─────────┘ │  └─────────┘     │
└─────────────────────────────────────────────────┘
```

##### Adaptive ve Async Modlar

UAC2 iki zamanlama modu destekler:

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Adaptive** | USB host saatine senkronize | Varsayılan mod |
| **Async** | Device kendi saatini kullanır | Profesyonel cihazlar |

**Async Mod Avantajları**:
- Daha düşük jitter
- Daha iyi clock recovery
- Profesyonel ses cihazları için tercih edilen

##### Clock Source Yönetimi

UAC2, birden fazla clock source destekler:

```cpp
// Clock source listesini al
USB_AC2_CLOCK_SOURCE clocks[10];
uint32_t clockCount;
UAC2_GetClockSources(deviceId, clocks, &clockCount);

// Clock source seçimi
UAC2_SetClockSource(deviceId, 
                     clocks[0].sourceId, 
                     clocks[0].sampleRates[0]);

// Clock frequency sorgusu
double frequency;
UAC2_GetClockFrequency(deviceId, 
                        clocks[0].sourceId, 
                        &frequency);
```

##### Format Desteği

UAC2 aşağıdaki formatları destekler:

| Format | Bit Derinliği | Örnek Hızı | Kanal |
|--------|---------------|------------|-------|
| PCM | 16, 24, 32 | 44.1k-384k | 1-32 |
| IEEE Float | 32 | 44.1k-192k | 1-32 |
| DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8 |

##### Endpoint Yapılandırması

Her USB ses cihazı bir veya daha fazla endpoint içerir:

```
USB Audio Device
├── Input Terminal (Mikrofon)
│   └── Input Endpoint (Isoch IN)
├── Output Terminal (Hoparlör)
│   └── Output Endpoint (Isoch OUT)
├── Feature Unit (Volume, Mute)
└── Clock Source (Internal/External)
```

##### Bandwidth Yönetimi

USB bandwidth yönetimi kritiktir:

```
High Speed USB (480 Mbps)
├── Isochronous Bandwidth: 20% = 96 Mbps
├── Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch)
└── Headroom: ~72 Mbps

Full Speed USB (12 Mbps)
├── Isochronous Bandwidth: 90% = 10.8 Mbps
├── Max Audio Bandwidth: ~1.5 Mbps (16-bit 48kHz 2ch)
└── Headroom: ~9.3 Mbps
```

##### Buffer Yönetimi

USB Audio buffer yönetimi:

```cpp
// Isochronous buffer yapısı
struct USB_AudioBuffer {
    void* data;           // Buffer verisi
    uint32_t size;        // Buffer boyutu
    uint32_t frameSize;   // Frame boyutu
    uint32_t numFrames;   // Frame sayısı
    bool isochronous;     // Isochronous modu
};

// Double buffering
USB_AudioBuffer bufferA, bufferB;
USB_AudioBuffer* activeBuffer = &bufferA;
USB_AudioBuffer* backBuffer = &bufferB;

// Buffer değişimi (çift tamponlama)
void swapBuffers() {
    USB_AudioBuffer* temp = activeBuffer;
    activeBuffer = backBuffer;
    backBuffer = temp;
}
```

##### Hata Yönetimi

USB Audio hata yönetimi:

| Hata | Neden | Çözüm |
|------|-------|-------|
| `USB_ERROR_STALL` | Transfer durduruldu | Endpoint'i resetle |
| `USB_ERROR_NAK` | Cihaz meşgul | Yeniden dene |
| `USB_ERROR_TIMEOUT` | Zaman aşımı | Bandwidth'i artır |
| `USB_ERROR_OVERFLOW` | Buffer taştı | Buffer boyutunu artır |

#### API / Arayüz

```cpp
class USBAudioDriver {
public:
    bool initialize();
    void shutdown();
    
    // Cihaz keşfi
    std::vector<USBAudioDevice> enumerateDevices() const;
    bool connectToDevice(uint32_t deviceId);
    void disconnectDevice();
    
    // Akış yapılandırması
    bool setSampleRate(double rate);
    bool setBitDepth(uint32_t bits);
    bool setChannelCount(uint32_t channels);
    bool setBufferSize(uint32_t frames);
    
    // Transfer kontrolü
    bool startPlayback();
    bool stopPlayback();
    bool startCapture();
    bool stopCapture();
    
    // Async mod
    bool enableAsyncMode();
    bool setClockSource(uint32_t sourceId);
    
    // Buffer yönetimi
    bool submitBuffer(const USB_AudioBuffer& buffer);
    bool cancelPendingTransfers();
    
    //_durum
    bool isConnected() const;
    double getCurrentSampleRate() const;
    uint32_t getLatency() const;
};

// Kullanım örneği
USBAudioDriver driver;
driver.initialize();

auto devices = driver.enumerateDevices();
if (!devices.empty()) {
    driver.connectToDevice(devices[0].id);
    driver.setSampleRate(96000);
    driver.setBitDepth(32);
    driver.setChannelCount(2);
    driver.enableAsyncMode();
    driver.startPlayback();
}
```

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libusb | Sistem kütüphanesi |
| USB Host Controller Driver | Çekirdek |
| K1 USB Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Ağ senkronizasyon protokolü (ör. PTP) kullanımı vault'ta doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | USB-audio sınıf sürümü ve alt uçlar (alt-setting) listesi yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Ağ gecikme/jitter → buffer underrun (kaynak: `network-audio-drivers`).
2. USB Endpoint bandwidth yetersizliği → biçimsel reddetme (kaynak: `usb-audio-class`).

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

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K017 · Uzak Ses Sürücüleri (USB · Ağ) — SSOT: `.ai/architecture/k017-uzak-bluetooth-usb/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
