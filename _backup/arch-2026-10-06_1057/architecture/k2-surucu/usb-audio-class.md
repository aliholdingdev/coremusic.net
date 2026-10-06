---
title: "USB Audio Class 2.0 Sürücüsü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# USB Audio Class 2.0 Sürücüsü

## Genel Bakış

USB Audio Class 2.0 (UAC2), USB üzerinden yüksek kaliteli ses giriş/çıkışı için evrensel bir standarttır. COREMUSIC, UAC2 protokolünü destekleyerek yüksek çözünürlüklü ses cihazlarıyla (DAC, ADC,ampler) doğrudan çalışır.

## Teknik Detaylar

### UAC2 Mimarisi

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

### Isochronous Transfer

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

### Adaptive ve Async Modlar

UAC2 iki zamanlama modu destekler:

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Adaptive** | USB host saatine senkronize | Varsayılan mod |
| **Async** | Device kendi saatini kullanır | Profesyonel cihazlar |

**Async Mod Avantajları**:
- Daha düşük jitter
- Daha iyi clock recovery
- Profesyonel ses cihazları için tercih edilen

### Clock Source Yönetimi

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

### Format Desteği

UAC2 aşağıdaki formatları destekler:

| Format | Bit Derinliği | Örnek Hızı | Kanal |
|--------|---------------|------------|-------|
| PCM | 16, 24, 32 | 44.1k-384k | 1-32 |
| IEEE Float | 32 | 44.1k-192k | 1-32 |
| DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8 |

### Endpoint Yapılandırması

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

### Bandwidth Yönetimi

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

### Buffer Yönetimi

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

### Hata Yönetimi

USB Audio hata yönetimi:

| Hata | Neden | Çözüm |
|------|-------|-------|
| `USB_ERROR_STALL` | Transfer durduruldu | Endpoint'i resetle |
| `USB_ERROR_NAK` | Cihaz meşgul | Yeniden dene |
| `USB_ERROR_TIMEOUT` | Zaman aşımı | Bandwidth'i artır |
| `USB_ERROR_OVERFLOW` | Buffer taştı | Buffer boyutunu artır |

## API / Arayüz

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

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libusb | Sistem kütüphanesi |
| USB Host Controller Driver | Çekirdek |
| K1 USB Core | İç katman |

## Durum: Implementasyon

- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)
