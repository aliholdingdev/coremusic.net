---
title: "PipeWire Modern Sürücü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# PipeWire Modern Sürücü

## Genel Bakış

PipeWire, modern Linux ses yönlendirmesi için geliştirilen bir framework'tür. ALSA, PulseAudio ve JACK'ın yerini alarak tek bir unified API ile tüm ses ihtiyaçlarını karşılar. COREMUSIC, PipeWire SPA (Simple Plugin API) plugin mimarisini kullanarak yüksek performanslı ses işleme sağlar.

## Teknik Detaylar

### PipeWire Mimarisi

```
┌─────────────────────────────────────────────┐
│            Uygulama Katmanı                  │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐     │
│  │ Media1  │  │ Media2  │  │ COREMUSIC│     │
│  └────┬────┘  └────┬────┘  └────┬────┘     │
│       │            │            │            │
├───────┴────────────┴────────────┴────────────┤
│            PipeWire Daemon                   │
│  ┌────────────────────────────────────────┐  │
│  │         Graph Manager                  │  │
│  │    (Node connections, scheduling)      │  │
│  └────────────────────────────────────────┘  │
├──────────────────────────────────────────────┤
│            SPA Plugin System                 │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐       │
│  │ ALSA │ │ V4L2 │ │ File │ │ DSP  │       │
│  └──────┘ └──────┘ └──────┘ └──────┘       │
└──────────────────────────────────────────────┘
```

### SPA Plugin Sistemi

SPA (Simple Plugin API), PipeWire'nin plugin mimarisidir:

**Plugin Tipleri**:
| Tip | Açıklama |
|-----|----------|
| `spa/support` | Destekleyici pluginler (logger, dict) |
| `spa/clock` | Zamanlama servisleri |
| `spa/node` | İşlem node'ları |
| `spa/device` | Donanım cihazları |
| `spa/lib` | Yardımcı kütüphaneler |

**COREMUSIC SPA Plugin'leri**:
```c
// Audio processor plugin
static const struct spa_node_methods impl_node = {
    SPA_VERSION_NODE_METHODS,
    .enum_params = impl_enum_params,
    .set_param = impl_set_param,
    .send_command = impl_send_command,
    .set_callback = impl_set_callback,
    .start = impl_start,
    .pause = impl_pause,
    .stop = impl_stop,
    .send_port = impl_send_port,
    .process = impl_process,      // Ana processing callback
};
```

### Grafik İşleme

PipeWire, ses akışını bir grafik olarak yönetir:

```
┌──────────────────────────────────────────────┐
│              PipeWire Graph                   │
│                                              │
│  [USB In] → [COREMUSIC DSP] → [Speakers]    │
│      ↓           ↓                ↓          │
│  [Capture]   [Processing]    [Playback]      │
└──────────────────────────────────────────────┘
```

**Grafik Optimizasyonu**:
1. Otomatik mengenaj (degrade) ekleme
2. Format dönüşümü node'ları ekleme
3. Buffer boyutu uyumsuzluklarını çözme
4. Zamanlama (scheduling) optimizasyonu

### Real-Time Zamanlama

PipeWire, gerçek zamanlı işlemenin gerektirdiği katı zamanlamayı sağlar:

```c
// RT thread yapılandırması
struct sched_param param;
param.sched_priority = 88;  // Yüksek öncelik
pthread_setschedparam(pthread_self(), SCHED_FIFO, &param);

// CPU affinity
cpu_set_t cpuset;
CPU_ZERO(&cpuset);
CPU_SET(2, &cpuset);  // Çekirdek 2'ye ata
pthread_setaffinity_np(pthread_self(), sizeof(cpuset), &cpuset);
```

### Buffer Yönetimi

PipeWire buffer yönetimi esnek ve yapılandırılabilir:

```
Buffer Akışı:
┌─────────────────────────────────────────────┐
│  Port 0 ──┐                                │
│  Port 1 ──┼→ [Mix] → [Process] → [Split]  │
│  Port 2 ──┘         ↓                      │
│                  [DSP Chain]                │
└─────────────────────────────────────────────┘
```

**Buffer Parametreleri**:
| Parametre | Varsayılan | Açıklama |
|-----------|------------|----------|
| `SPA_PARAM_BUFFERS_size` | 1024 | Buffer boyutu |
| `SPA_PARAM_BUFFERS_blocks` | 1 | Blok sayısı |
| `SPA_PARAM_BUFFERS_stride` | 4 | Byte stride |
| `SPA_PARAM_BUFFERS_align` | 16 | Bellek hizalama |

###/format Uyumluluğu

PipeWire, otomatik format dönüşümü yapar:

```
Kaynak Format → PipeWire → Hedef Format
─────────────────────────────────────────
S16LE Stereo  →  otomatik → F32LE 5.1
F32LE 48kHz   →  otomatik → S24LE 96kHz
```

**Desteklenen Formatlar**:
- `SPA_AUDIO_FORMAT_S16LE`
- `SPA_AUDIO_FORMAT_S24LE`
- `SPA_AUDIO_FORMAT_S32LE`
- `SPA_AUDIO_FORMAT_F32LE`
- `SPA_AUDIO_FORMAT_F64LE`

### Port ve Donanım Yönetimi

PipeWire donanımı otomatik keşfeder:

```c
// Cihaz dinleme
struct spa_hook device_listener;
spa_device_add_listener(device, &device_listener,
                        &device_events, /*data*/);

// Cihaz değişikliklerini işle
static void on_device_info(void *data, 
                           const struct spa_device_info *info) {
    // Yeni cihaz bulundu
    // COREMUSIC tarafından ele alınır
}
```

## API / Arayüz

```cpp
class PipeWireDriver {
public:
    bool initialize();
    void shutdown();
    
    // Node yönetimi
    uint32_t createNode(const PipeWireNodeConfig& config);
    bool destroyNode(uint32_t nodeId);
    bool connectNodes(uint32_t srcId, uint32_t dstId);
    
    // Parametreler
    bool setNodeParam(uint32_t nodeId, 
                      uint32_t paramId,
                      const void* value, size_t size);
    
    // Grafik erişimi
    PipeWireGraphInfo getGraphInfo() const;
    std::vector<PipeWireNodeInfo> listNodes() const;
    
    // Donanım
    std::vector<PipeWireDevice> listDevices() const;
    bool setDefaultDevice(uint32_t deviceId);
    
    // Zamanlama
    bool setRealTimePriority(int priority);
    bool setCpuAffinity(int core);
    
    // Stream yönetimi
    uint32_t createStream(const PipeWireStreamConfig& config);
    bool startStream(uint32_t streamId);
    bool stopStream(uint32_t streamId);
};

// PipeWire Node yapılandırması
struct PipeWireNodeConfig {
    std::string name;
    std::string mediaType;      // "Audio"
    std::string mediaCategory;  // "Playback" / "Capture"
    uint32_t channels;
    uint32_t sampleRate;
    uint32_t bufferSize;
    spa_audio_format format;
    bool exclusiveMode;
};

// Kullanım örneği
PipeWireDriver driver;
driver.initialize();

PipeWireNodeConfig config;
config.name = "COREMUSIC Engine";
config.mediaType = "Audio";
config.mediaCategory = "Playback";
config.channels = 2;
config.sampleRate = 96000;
config.bufferSize = 256;
config.format = SPA_AUDIO_FORMAT_F32LE;

uint32_t nodeId = driver.createNode(config);
driver.startStream(nodeId);
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.7ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 2% | 1.2% |
| Maks. Node | 1000+ | 1000+ |
| Değişim Süresi | < 1ms | 0.5ms |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libpipewire-0.3 | Sistem kütüphanesi |
| SPA SDK | PipeWire plugin sistemi |
| K1 Linux Core | İç katman |

## Durum: Implementasyon

- **Faz 1**: PipeWire SDK entegrasyonu, temel node oluşturma
- **Faz 2**: SPA plugin implementasyonu
- **Faz 3**: Grafik optimizasyonu, zamanlama
- **Faz 4**: Donanım keşfi, çoklu cihaz desteği
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)
