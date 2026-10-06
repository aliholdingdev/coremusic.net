---
title: "USB ve Ağ Ses Sürücüleri - k017-uzak-bluetooth-usb"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# USB ve Ağ Ses Sürücüleri

> Klasör: `k017-uzak-bluetooth-usb` · Dosya: `usb-ve-ag-ses-suruculeri.md`
> Sorumlu persona: `dsp-firmware-engineer` · `embedded-engineer`
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (1 satir / 6 bolum) + `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` (1 satir / 6 bolum) — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.
## Genel Bakış

Uzak/kablolu taşıma katmanındaki iki sürücü ailesi: USB Audio Class 2.0 (UAC2) ve ağ ses protokolleri (Dante, AVB/TSN, RAVENNA). USB kaynağı teknik detay + API bölümlerine, ağ kaynağı teknik detay bölümüne kadar aktarılmış; kalan aralıklar aşağıdaki tabloda L aralığıyla belirtilmiştir.


## Kapsam ve Sınırlar

- **Kapsam:** UAC2 mimarisi, isochronous transfer, adaptive/async modlar, clock source, format, endpoint, bandwidth, buffer, hata yönetimi, API; ağ protokolleri karşılaştırması, Dante, AVB/TSN, RAVENNA, PTP zamanlama, jitter buffer, multicast, donanım gereksinimleri.
- **Kapsam dışı:** aktarılmayan aralıklar (aşağıda listelenmiştir), Bluetooth A2DP (→ [[bluetooth-a2dp.md]]), I2S/donanım arayüzü (→ `k034-usb-audio`).
- **Bağlı olduğu klasör:** [[index.md]] (Uzak / Bluetooth / USB Ses)
- **Çapraz referanslar:** [[../k034-usb-audio/index]] · [[../k059-usb-audio/index]] · [[../k035-ag-ve-bluetooth-ses/index]] · [[../k014-surucu-yigin/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi (`#` → `###`); her blokta kanıt satırı kaynak dosyayı ve gerçek satır aralığını (`L<başlangıç>-L<bitiş>`) gösterir. Bloklar kaynaktan değiştirilmeden kopyalanmıştır.

### usb-audio-class.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (254 satir)


#### USB Audio Class 2.0 Sürücüsü

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md - L8-L13

### USB Audio Class 2.0 Sürücüsü

#### Genel Bakış

USB Audio Class 2.0 (UAC2), USB üzerinden yüksek kaliteli ses giriş/çıkışı için evrensel bir standarttır. COREMUSIC, UAC2 protokolünü destekleyerek yüksek çözünürlüklü ses cihazlarıyla (DAC, ADC,ampler) doğrudan çalışır.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md - L14-L175

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md - L176-L229

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

### network-audio-drivers.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` (246 satir)


#### Ağ Ses Sürücüleri (Dante, AVB, RAVENNA)

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md - L8-L13

### Ağ Ses Sürücüleri (Dante, AVB, RAVENNA)

#### Genel Bakış

Ağ ses protokolleri, profesyonel ses endüstrisinde ses sinyallerinin Ethernet üzerinden iletilmesini sağlar. COREMUSIC, Dante, AVB ve RAVENNA protokollerini destekleyerek yüksek kanallı, düşük gecikmeli ağ sesi sağlar.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md - L14-L156

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

## Aktarılmayan Kaynak Bölümleri

| Kaynak dosya | Satır aralığı | Not |
|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | L230-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | L254-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | L157-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | L246-L | [VERIFY REQUIRED] bu dosyanın satır sınırı nedeniyle aktarılmadı; içerik yalnız kaynakta mevcuttur. |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | USB Audio Class 2.0 Sürücüsü | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | Teknik Detaylar | L14-L175 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | API / Arayüz | L176-L229 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | Ağ Ses Sürücüleri (Dante, AVB, RAVENNA) | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | Teknik Detaylar | L14-L156 | ✓ verbatim |

## Belirsizlik Taraması (kaynak metin)

- Kaynaklarda `UNKNOWN` / `TODO` / `Belirlenecek` / `VERIFICATION REQUIRED` içeren satır **tespit edilmedi** (tam tarama).

## İlgili Dosyalar

[[index.md]] · [[bluetooth-a2dp.md]] · [[../k034-usb-audio/index]] · [[../k035-ag-ve-bluetooth-ses/index]] · [[../k059-usb-audio/index]]

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault yedeği (`_backup/arch-2026-10-06_1057/architecture/k2-surucu/`) kanıtına dayanır; diskteki uygulama kodu ile çapraz doğrulama yapılmamıştır.
2. ⚠️ VERIFICATION REQUIRED — kaynak frontmatter `date: 2026-09-20` / `layer: K2` değerleri ile bu dosyanın `updated: 2026-10-06` değeri arasındaki fark üst merci onayı bekler.
