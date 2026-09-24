---
title: "WASAPI Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# WASAPI Sürücüleri

## Genel Bakış

WASAPI (Windows Audio Session API), Windows Vista ve sonrası için Microsoft'un modern ses API'sidir. COREMUSIC, WASAPI'yi hem Exclusive hem de Shared modda kullanarak Windows platformunda profesyonel ses desteği sağlar.

## Teknik Detaylar

### WASAPI Çalışma Modları

```
┌─────────────────────────────────────────────────────────┐
│                WASAPI Working Modes                     │
├─────────────────────┬───────────────────────────────────┤
│   Exclusive Mode    │         Shared Mode               │
├─────────────────────┼───────────────────────────────────┤
│ Doğrudan HW erişimi │ Windows Audio Service üzerinden   │
│ Mix organizer yok   │ Mix organizer devrede             │
│ Tek uygulama        │ Çoklu uygulama                    │
│ Bit-perfect output  │ DSP eklenebilir                   │
│ Düşük latency       │ Yüksek latency                    │
│ Donanım formatı     │ Win formats (PCM, IEEE float)     │
└─────────────────────┴───────────────────────────────────┘
```

### Exclusive Mode Implementasyonu

Exclusive mode, uygulamanın ses kartına doğrudan erişmesini sağlar:

1. **IAudioClient arayüzü**: Ana kontrol noktası
   - `Initialize()`: Exclusive mode ile başlatma
   - `GetBufferDuration()`: Buffer süresi sorgusu
   - `Start()` / `Stop()`: Akış kontrolü

2. **IAudioCaptureClient**: Giriş verisi okuma
   - `GetBuffer()`: Ham veri erişimi
   - `ReleaseBuffer()`: Buffer'ı serbest bırak
   - `GetNextPacketSize()`: Bir sonraki paket boyutu

3. **IAudioRenderClient**: Çıkış verisi yazma
   - `GetBuffer()`: Yazma alanı alma
   - `ReleaseBuffer()`: Veriyi donanıma iletme

### Exclusive Mode Avantajları

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Latency | 1-3ms | 10-40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP | Donanım | Windows |

### Buffer Yönetimi

WASAPI buffer yönetimi ASIO'dan farklıdır:

```
Exclusive Mode:
┌──────────────────────────────────────┐
│  App Buffer → Driver Buffer → HW     │
│  (definite)   (definite)             │
└──────────────────────────────────────┘

Shared Mode:
┌──────────────────────────────────────┐
│  App Buffer → Audio Engine → HW      │
│  (definite)   (indefinite)           │
└──────────────────────────────────────┘
```

**Buffer Boyut Hesaplama**:
```
BufferDuration = (BufferSize / SampleRate) * 1,000,000 (μs)

Örnek:
Buffer: 288 samples @ 48kHz
Duration: (288 / 48000) * 1,000,000 = 6000μs = 6ms
```

### Windows Audio Session

Her WASAPI oturumu aşağıdaki bileşenleri içerir:

| Bileşen | Açıklama |
|---------|----------|
| AudioSessionControl | Oturum kontrolü (ses seviyesi, durdurma) |
| AudioSessionManager | Oturum yönetimi (tercihler, efektler) |
| AudioMeterInformation | Gerçek zamanlı ses seviyesi metering |
| AudioEndpointVolume | Donanım ses seviyesi kontrolü |

### Format Desteği

WASAPI aşağıdaki ses formatlarını destekler:

```cpp
// Desteklenen formatlar
WAVEFORMATEX formats[] = {
    {WAVE_FORMAT_PCM,     16, 2, 48000},  // 16-bit PCM
    {WAVE_FORMAT_PCM,     24, 2, 96000},  // 24-bit PCM
    {WAVE_FORMAT_IEEE_FLOAT, 32, 2, 192000}, // 32-bit Float
    {WAVE_FORMAT_EXTENSIBLE, 32, 8, 384000}, // 8-kanal 32-bit
};
```

### Latency Optimizasyonu

WASAPI Exclusive mode'da minimum latency için:

1. **Buffer Süresi**: 3-6ms arası (donanıma bağlı)
2. **Period Goddessi**: `IAudioClient::SetEventHandle()` ile kesme zamanlaması
3. **Thread Önceliği**: `THREAD_PRIORITY_TIME_CRITICAL` ile yüksek öncelik
4. **CPU Affinity**: Ses thread'ini belirli CPU çekirdeğine ata

### Hata Durumları

| Hata | Kod | Çözüm |
|------|-----|-------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Shared mode'a geç |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Formatı değiştir |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Yetki kontrolü |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer boyutunu ayarla |

## API / Arayüz

```cpp
class WASAPIDriver {
public:
    bool initialize(const AudioConfig& config);
    bool startExclusive();
    bool startShared();
    void stop();
    
    // Buffer yönetimi
    bool setBufferDuration(uint32_t durationMs);
    uint32_t getBufferDuration() const;
    
    // Format yapılandırması
    bool setFormat(const WAVEFORMATEX& format);
    bool isFormatSupported(const WAVEFORMATEX& format, 
                           EDataFlow dataFlow);
    
    // Session yönetimi
    bool setSessionName(const wchar_t* name);
    bool setSessionIconPath(const wchar_t* path);
    
    // Volume kontrolü
    bool setMasterVolume(float volume);
    bool setMute(bool mute);
    
    // Metering
    float getPeakLevel() const;
    float getRMSLevel() const;
};

// Exclusive mode başlatma örneği
WASAPIDriver driver;
AudioConfig config;
config.sampleRate = 96000;
config.bitsPerSample = 32;
config.channels = 2;
config.bufferDurationMs = 4;

driver.initialize(config);
driver.startExclusive();
```

## Performans Metrikleri

| Metrik | Exclusive | Shared |
|--------|-----------|--------|
| Input Latency | 1.5ms | 15ms |
| Output Latency | 1.5ms | 15ms |
| Round-trip | 3ms | 30ms |
| CPU (boşta) | 0.5% | 2% |
| Bit-perfect | Evet | Hayır |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Windows SDK | Dış |
| K1 Windows Core | İç |
| K3 Engine | İç |

## Durum: Implementasyon

- **Faz 1**: WASAPI SDK entegrasyonu, temel Exclusive mode
- **Faz 2**: Shared mode, session yönetimi
- **Faz 3**: Metering ve volume kontrolü
- **Faz 4**: Format dönüşümleri, hata yönetimi
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)
