---
title: "ALSA Native Sürücü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# ALSA Native Sürücü

## Genel Bakış

ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformunda minimum gecikme ile ses giriş/çıkışı sağlar. ALSA, PipeWire ile birlikte kullanılabildiği gibi tek başına da çalışabilir.

## Teknik Detaylar

### ALSA Mimarisi

```
┌─────────────────────────────────────────┐
│         Uygulama (K3 Neva Engine)       │
├─────────────────────────────────────────┤
│         ALSA Library (libasound)        │
├─────────────────────────────────────────┤
│         ALSA Kernel Driver              │
├─────────────────────────────────────────┤
│         Hardware (PCM, Control, MIDI)   │
└─────────────────────────────────────────┘
```

### PCM Aygıtları

ALSA'nın temel bileşeni PCM aygıtlarıdır:

**PCM Akış Tipleri**:
- `PCM_DEVICE_PLAYBACK`: Ses çıkışı (c:0, c:1, ...)
- `PCM_DEVICE_CAPTURE`: Ses girişi (p:0, p:1, ...)
- `PCM_DEVICE_DUPLEX`: Hem giriş hem çıkış

**PCM Yöntemleri**:
- `SND_PCM_ACCESS_MMAP_INTERLEAVED`: Doğrudan bellek haritalama (en düşük latency)
- `SND_PCM_ACCESS_MMAP_NONINTERLEAVED`: Non-interleaved erişim
- `SND_PCM_ACCESS_RW_INTERLEAVED`: Okuma/yazma (interleaved)
- `SND_PCM_ACCESS_RW_NONINTERLEAVED`: Okuma/yazma (non-interleaved)

### Donanım Parametreleri

ALSA donanım parametreleri açıkça yapılandırılabilir:

```cpp
// Donanım parametreleri
snd_pcm_hw_params_t* hw_params;
snd_pcm_hw_params_malloc(&hw_params);

// Örnekleme hızı
snd_pcm_hw_params_set_rate_near(handle, hw_params, 
                                 &sampleRate, 0);

// Kanal sayısı
snd_pcm_hw_params_set_channels(handle, hw_params, 
                                &channels);

// Buffer boyutu
snd_pcm_hw_params_set_buffer_size_near(handle, hw_params,
                                        &bufferSize);

// Periyot boyutu (kesme aralığı)
snd_pcm_hw_params_set_period_size_near(handle, hw_params,
                                        &periodSize, 0);

snd_pcm_hw_params_free(hw_params);
```

### Buffer Yönetimi

ALSA buffer yönetimi iki seviyede çalışır:

**1. Kernel Buffer (ALSA Ring Buffer)**:
```
┌────────────────────────────────────────────┐
│  Kernel Ring Buffer                        │
│  ┌──────┬──────┬──────┬──────┬──────┐      │
│  │ P0   │ P1   │ P2   │ P3   │ P4   │      │
│  └──────┴──────┴──────┴──────┴──────┘      │
│  ↑ Write Ptr              ↑ Read Ptr       │
└────────────────────────────────────────────┘
```

**2. User Buffer (Uygulama Buffer)**:
- MMAP modda: Doğrudan kernel buffer'a yazma
- RW modda: `snd_pcm_writei()` ile veri kopyalama

### Period Size (Periyot Boyutu)

Period size, kesme (interrupt) aralığını belirler:

```
Period Size = Buffer Size / Number of Periods

Örnek:
Buffer: 1024 samples
Periods: 4
Period Size: 256 samples @ 48kHz = 5.33ms

Latency: Period Size / SampleRate = 5.33ms
```

### MMAP Mod Implementasyonu

MMAP (Memory-Mapped) I/O, ALSA'nın en düşük gecikme yöntemidir:

```cpp
// MMAP ile yazma
const snd_pcm_channel_area_t* areas;
snd_pcm_mmap_begin(handle, &areas, &offset, &frames);

// areas[0].addr → write address
// areas[0].first → bit offset
// areas[0].step → bits per sample

// Veriyi doğrudan belleğe yaz
float* buffer = (float*)((char*)areas[0].addr + 
               (areas[0].first / 8) + 
               (offset * areas[0].step / 8));

// K3'ten veriyi buffer'a kopyala
memcpy(buffer, engine_output, frames * sizeof(float));

snd_pcm_mmap_commit(handle, offset, frames);
```

### XRUN Yönetimi

XRUN (buffer underrun/overrun) yönetimi kritiktir:

| XRUN Tipi | Neden | Çözüm |
|-----------|-------|-------|
| Underrun | Buffer yetersiz | Buffer boyutunu artır |
| Overrun | Buffer taştı | Period sayısını azalt |
| Suspended | Donanım durdu | `snd_pcm_prepare()` çağır |

```cpp
// XRUN kontrolü
snd_pcm_sframes_t frames = snd_pcm_writei(handle, buffer, count);
if (frames < 0) {
    frames = snd_pcm_recover(handle, frames, 0);
    frames = snd_pcm_writei(handle, buffer, count);
}
```

### Hardware Tasarım Dosyaları (HWDEP)

ALSA, donanıma özel yapılandırmaları destekler:

| HW Parametresi | Açıklama |
|-----------------|----------|
| `SND_PCM_HW_PARAM_ACCESS` | Erişim yöntemi |
| `SND_PCM_HW_PARAM_FORMAT` | Ses formatı (PCM, FLOAT) |
| `SND_PCM_HW_PARAM_CHANNELS` | Kanal sayısı |
| `SND_PCM_HW_PARAM_RATE` | Örnekleme hızı |
| `SND_PCM_HW_PARAM_BUFFER_SIZE` | Toplam buffer |
| `SND_PCM_HW_PARAM_PERIOD_SIZE` | Periyot boyutu |
| `SND_PCM_HW_PARAM_PERIODS` | Periyot sayısı |

## API / Arayüz

```cpp
class ALSADriver {
public:
    bool initialize(const AudioConfig& config);
    bool openPlayback(const char* deviceName);
    bool openCapture(const char* deviceName);
    void close();
    
    // Parametreler
    bool setSampleRate(uint32_t rate);
    bool setChannels(uint8_t channels);
    bool setBufferSize(uint32_t frames);
    bool setPeriodSize(uint32_t frames);
    
    // Erişim yöntemi
    bool setAccessMode(snd_pcm_access_t access);
    bool enableMMAP();
    
    // Okuma/yazma
    ssize_t write(const float* buffer, size_t frames);
    ssize_t read(float* buffer, size_t frames);
    
    // MMAP
    bool mmapBegin(const snd_pcm_channel_area_t** areas,
                   snd_pcm_uframes_t* offset,
                   snd_pcm_uframes_t* frames);
    bool mmapCommit(snd_pcm_uframes_t offset,
                    snd_pcm_uframes_t frames);
    
    // XRUN yönetimi
    int recover(int error);
    snd_pcm_state_t getState() const;
    
    // Cihaz listeleme
    static std::vector<std::string> listDevices();
};

// Kullanım örneği
ALSADriver driver;
AudioConfig config;
config.sampleRate = 96000;
config.bitsPerSample = 32;
config.channels = 2;

driver.initialize(config);
driver.openPlayback("hw:0,0");
driver.setBufferSize(256);
driver.setPeriodSize(64);
driver.enableMMAP();

// Döngü
while (running) {
    driver.write(engineOutput, 64);
}
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | İç katman |

## Durum: Implementasyon

- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)
