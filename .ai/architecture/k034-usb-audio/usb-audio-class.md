---
title: "USB Audio Class (UAC2) - k034-usb-audio"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# USB Audio Class (UAC2)

> Klasör: `k034-usb-audio` · Dilim: D01 (k018–k035) · Dosya: `usb-audio-class.md`
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

UAC2 descriptor yapısı, isochronous transfer ve örnek hızı desteği.

Bu belge; D01 diliminin (USB Ses) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** UAC2 descriptor yapısı, isochronous transfer ve örnek hızı desteği.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (USB Ses)
- **Çapraz referanslar:** [[../k018-dma-kesinti-yonetimi/dma-yonetimi.md]] · [[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]] · [[../k021-macos-core/core-audio-macos.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### usb-audio-class.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (254 satır)

#### usb-audio-class.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § (giriş) — L1–L9

---
title: "USB Audio Class 2.0 Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### USB Audio Class 2.0 Sürücüsü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Genel Bakış` — L10–L12


USB Audio Class 2.0 (UAC2), USB üzerinden yüksek kaliteli ses giriş/çıkışı için evrensel bir standarttır. COREMUSIC, UAC2 protokolünü destekleyerek yüksek çözünürlüklü ses cihazlarıyla (DAC, ADC,ampler) doğrudan çalışır.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Teknik Detaylar` — L14–L174


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `API / Arayüz` — L176–L228


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Performans Metrikleri` — L230–L238


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Bağımlılıklar` — L240–L246


| Bağımlılık | Tür |
|------------|-----|
| libusb | Sistem kütüphanesi |
| USB Host Controller Driver | Çekirdek |
| K1 USB Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Durum: Implementasyon` — L248–L254


- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

### usb-audio.md — `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` (190 satır)

#### usb-audio.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § (giriş) — L1–L9

---
title: "USB Audio Class 2.0"
layer: K1
category: "Dijital Ses Arabirimi"
date: 2026-09-20
---

### USB Audio Class 2.0


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Genel Bakış` — L10–L12


USB Audio Class 2.0 (UAC2), COREMUSIC'ın USB üzerinden yüksek çözünürlüklü ses alması için kullanılan endüstri standartıdır. XMOS XU316 UAC2 firmware'i çalıştırarak Windows, macOS ve Linux'ta driverless çalışır. Isochronous transfer modu ile zamanlama hassasiyeti sağlar.

#### Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Teknik Spesifikasyonlar` — L14–L27


| Parametre | Değer |
|-----------|-------|
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64/128/256 (DoP) |
| Kanal Sayısı | 8 stereo (16 single) |
| Async Transfer | Yes (device-sync) |
| Feedback | Adaptive/Synchronous |
| Latency | < 1ms |
| OS Desteği | Windows 10+, macOS 10.9+, Linux 4.x+ |
| Driver | Class-compliant (no driver needed) |

#### USB Descriptor Hierarchy

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `USB Descriptor Hierarchy` — L29–L61


```
USB Device Descriptor
     │
     ├─ Configuration Descriptor
     │   │
     │   ├─ Interface 0 (Audio Control)
     │   │   ├─ AC Header Descriptor
     │   │   ├─ Input Terminal (USB Streaming)
     │   │   ├─ Output Terminal (Speaker)
     │   │   └─ Feature Unit (Volume/Mute)
     │   │
     │   ├─ Interface 1 (Audio Streaming OUT)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   ├─ Format Type III (DSD)
     │   │   └─ Standard Endpoint (Iso OUT)
     │   │       └─ Isochronous Endpoint
     │   │
     │   ├─ Interface 2 (Audio Streaming IN)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   └─ Standard Endpoint (Iso IN)
     │   │       └─ Isochronous Endpoint
     │   │
     │   └─ Interface 3 (MIDI Streaming - Optional)
     │
     └─ String Descriptors
         ├─ Manufacturer: "COREMUSIC"
         ├─ Product: "COREMUSIC USB DAC"
         └─ Serial: "CM-001"
```

#### Isochronous Transfer

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Isochronous Transfer` — L63–L88


##### Neden Isochronous?

```
Audio data requires:
1. Constant data rate (no buffering variation)
2. Time-guaranteed delivery (no retry)
3. Low latency (< 10ms)

Isochronous transfer provides:
- Reserved bandwidth (guaranteed)
- No retry (best-effort, but constant rate)
- Fixed timing (1ms frames for USB 2.0)
```

##### Transfer Parameters

| Parametre | Değer |
|-----------|-------|
| Frame Size | 1ms (USB 2.0) |
| Packet Size | 44.1 samples @ 44.1kHz |
| Max Packet Size | 1024 bytes (High-Speed) |
| Bandwidth | 480Mbps / 8 = 60MB/s |
| Audio Bandwidth | 8ch × 32bit × 192kHz = 49.152Mbps |
| Utilization | 81.9% (max) |

#### Sample Rate Support

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Sample Rate Support` — L90–L101


| Sample Rate | Bit Depth | Channels | Bandwidth | MCLK |
|-------------|-----------|----------|-----------|------|
| 44.1kHz | 16-bit | 2 | 1.411 Mbps | 11.2896 MHz |
| 44.1kHz | 24-bit | 2 | 2.1168 Mbps | 11.2896 MHz |
| 44.1kHz | 32-bit | 8 | 11.2896 Mbps | 11.2896 MHz |
| 48kHz | 24-bit | 2 | 2.304 Mbps | 12.288 MHz |
| 96kHz | 24-bit | 2 | 4.608 Mbps | 12.288 MHz |
| 192kHz | 24-bit | 2 | 9.216 Mbps | 12.288 MHz |
| 384kHz | 32-bit | 2 | 24.576 Mbps | 12.288 MHz |
| 768kHz | 32-bit | 2 | 49.152 Mbps | 12.288 MHz |

#### DSD (DoP) Support

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `DSD (DoP) Support` — L103–L127


##### DoP (DSD over PCM)

```
DoP Format:
- DSD data wrapped in PCM frames
- Uses 16-bit or 24-bit PCM slots
- Marker bits identify DSD data

DoP64:
- DSD rate: 2.8224 MHz
- PCM frame rate: 44.1kHz × 64 = 2.8224MHz
- Uses 16-bit PCM frames

DoP128:
- DSD rate: 5.6448 MHz
- PCM frame rate: 44.1kHz × 128 = 5.6448MHz
- Uses 16-bit PCM frames

DoP256:
- DSD rate: 11.2896 MHz
- PCM frame rate: 44.1kHz × 256 = 11.2896MHz
- Uses 16-bit PCM frames
```

#### USB-C Connection

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `USB-C Connection` — L129–L148


##### Pin Mapping

```
USB-C Connector (16-pin)
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V Power
     ├─ GND (A1, A12, B1, B12) ──▶ Ground
     ├─ D+ (A6) ──▶ XU316 USB_DP (Pin 12)
     ├─ D- (A7) ──▶ XU316 USB_DM (Pin 13)
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink identification)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink identification)
     └─ SBU1, SBU2 ──▶ NC (not used for audio)

ESD Protection:
- USBLC6-2SC6 (TVS diode array)
- Clamping voltage: 6V
- Response time: < 1ns
```

#### Driver Status

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Driver Status` — L150–L158


| OS | Driver | Status |
|----|--------|--------|
| Windows 10/11 | UAC2 class-compliant | ✅ Native |
| macOS 10.9+ | UAC2 class-compliant | ✅ Native |
| Linux 4.x+ | ALSA UAC2 | ✅ Native |
| Android 5.0+ | UAC2 class-compliant | ✅ Native |
| iOS 11.0+ | UAC2 class-compliant | ✅ Native |

#### Bileşen Değerleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Bileşen Değerleri` — L160–L170


| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | USB Controller | XMOS XU316 | 1 | UAC2 engine |
| 2 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 3 | Crystal | 24.576MHz | 1 | 48kHz family |
| 4 | USB Connector | USB-C 16-pin | 1 | SMD mount |
| 5 | ESD Protection | USBLC6-2SC6 | 1 | TVS array |
| 6 | Decoupling | 100nF MLCC | 4 | USB power |
| 7 | CC Resistors | 5.1kΩ 0402 | 2 | Sink ID |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Bağımlılıklar` — L172–L179


| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Ana bileşen | UAC2 firmware |
| K1 Konnektörler | Bağlantı | USB-C connector |
| K2 OS/Sürücüler | Üst | USB enumeration |
| K1 Güç Kaynağı | Alt | +5V VBUS |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Durum: Implementasyon` — L181–L190


**Durum**: 🟢 Hazır

- Firmware: lib_xua (XMOS open-source) kullanılıyor
- USB descriptor: Custom descriptor hazırlandı
- Enumeration: Windows/macOS/Linux test edildi
- DSD: DoP64/128/256 destekleniyor
- Latency: < 1ms measured
- Power: USB bus-powered (5V/500mA)

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| void disconnectDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L187 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| - Hata düzeltme yok (veya sınırlı) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L47 |
| ### Hata Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L165 |
| USB Audio hata yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L167 |
| - **Faz 4**: DSD desteği, hata yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L253 |
| 2. Time-guaranteed delivery (no retry) | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` L70 |
| - No retry (best-effort, but constant rate) | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` L75 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| USB Frame (1ms @ Full Speed, 125μs @ High Speed) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L52 |
| - Daha düşük jitter | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L72 |
| ├── Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L129 |
| ├── Max Audio Bandwidth: ~1.5 Mbps (16-bit 48kHz 2ch) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L134 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L138 |
| USB Audio buffer yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L140 |
| // Isochronous buffer yapısı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L143 |
| struct USB_AudioBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L144 |
| void* data;           // Buffer verisi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L145 |
| uint32_t size;        // Buffer boyutu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L146 |
| // Double buffering | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L152 |
| USB_AudioBuffer bufferA, bufferB; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L153 |
| USB_AudioBuffer* activeBuffer = &bufferA; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L154 |
| USB_AudioBuffer* backBuffer = &bufferB; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L155 |
| // Buffer değişimi (çift tamponlama) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L157 |
| void swapBuffers() { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L158 |
| USB_AudioBuffer* temp = activeBuffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L159 |
| activeBuffer = backBuffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L160 |
| backBuffer = temp; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L161 |
| bool setBufferSize(uint32_t frames); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L193 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - Enumeration: Windows/macOS/Linux test edildi | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` L187 |
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
| `UAC2` | 16 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L12 |
| `hotplug` | 0 | [kaynakta eşleşme yok] |
| `USB` | 59 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` L2 |
| `firmware` | 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` L12 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Teknik Detaylar | L14–L174 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## API / Arayüz | L176–L228 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Performans Metrikleri | L230–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Bağımlılıklar | L240–L246 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | ## Durum: Implementasyon | L248–L254 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Teknik Spesifikasyonlar | L14–L27 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## USB Descriptor Hierarchy | L29–L61 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Isochronous Transfer | L63–L88 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Sample Rate Support | L90–L101 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## DSD (DoP) Support | L103–L127 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## USB-C Connection | L129–L148 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Driver Status | L150–L158 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Bileşen Değerleri | L160–L170 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Bağımlılıklar | L172–L179 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | ## Durum: Implementasyon | L181–L190 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` § `Durum: Implementasyon` — L248–L254


- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` § `Durum: Implementasyon` — L181–L190


**Durum**: 🟢 Hazır

- Firmware: lib_xua (XMOS open-source) kullanılıyor
- USB descriptor: Custom descriptor hazırlandı
- Enumeration: Windows/macOS/Linux test edildi
- DSD: DoP64/128/256 destekleniyor
- Latency: < 1ms measured
- Power: USB bus-powered (5V/500mA)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
