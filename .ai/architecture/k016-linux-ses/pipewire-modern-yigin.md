---
title: "K016 PipeWire Modern Yığın"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K016 — PipeWire Modern Yığın

> **K numarası:** K016 · **Klasör:** `k016-linux-ses` · **Dosya:** `pipewire-modern-yigin`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** ALSA native PCM arayüzü ile modern PipeWire yığınının katmanlarını, buffer/period/xrun davranışını ve donanım parametrelerini tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **PipeWire Modern Yığın** konusunu ele alır. Kapsamı: PipeWire'nin üst katman konumu, ALSA'ya inişi, otomatik yeniden eşleme ve düşük gecikme modu.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Uygulama (JACK/PA uyumluluk) ]
            │
            ▼
      [ PipeWire ]
            │
            ▼
      [ ALSA PCM (hw/plughw) ]
            │
            ▼
      [ Kernel + DMA ]
            │
            ▼
      [ Ses kartı (donanım) ]
```

**Akış notları:**

1. **Uygulama (JACK/PA uyumluluk)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **PipeWire** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **ALSA PCM (hw/plughw)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Kernel + DMA** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Ses kartı (donanım)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | 254 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | 247 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/pipewire-modern.md`

| Tip | Açıklama |
|-----|----------|
| `spa/support` | Destekleyici pluginler (logger, dict) |
| `spa/clock` | Zamanlama servisleri |
| `spa/node` | İşlem node'ları |
| `spa/device` | Donanım cihazları |
| `spa/lib` | Yardımcı kütüphaneler |

### 4.2 · `k2-surucu/pipewire-modern.md`

| Parametre | Varsayılan | Açıklama |
|-----------|------------|----------|
| `SPA_PARAM_BUFFERS_size` | 1024 | Buffer boyutu |
| `SPA_PARAM_BUFFERS_blocks` | 1 | Blok sayısı |
| `SPA_PARAM_BUFFERS_stride` | 4 | Byte stride |
| `SPA_PARAM_BUFFERS_align` | 16 | Bellek hizalama |

### 4.3 · `k2-surucu/pipewire-modern.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.7ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 2% | 1.2% |
| Maks. Node | 1000+ | 1000+ |
| Değişim Süresi | < 1ms | 0.5ms |

### 4.4 · `k2-surucu/pipewire-modern.md`

| Bağımlılık | Tür |
|------------|-----|
| libpipewire-0.3 | Sistem kütüphanesi |
| SPA SDK | PipeWire plugin sistemi |
| K1 Linux Core | İç katman |

### 4.5 · `k2-surucu/alsa-native.md`

| XRUN Tipi | Neden | Çözüm |
|-----------|-------|-------|
| Underrun | Buffer yetersiz | Buffer boyutunu artır |
| Overrun | Buffer taştı | Period sayısını azalt |
| Suspended | Donanım durdu | `snd_pcm_prepare()` çağır |

### 4.6 · `k2-surucu/alsa-native.md`

| HW Parametresi | Açıklama |
|-----------------|----------|
| `SND_PCM_HW_PARAM_ACCESS` | Erişim yöntemi |
| `SND_PCM_HW_PARAM_FORMAT` | Ses formatı (PCM, FLOAT) |
| `SND_PCM_HW_PARAM_CHANNELS` | Kanal sayısı |
| `SND_PCM_HW_PARAM_RATE` | Örnekleme hızı |
| `SND_PCM_HW_PARAM_BUFFER_SIZE` | Toplam buffer |
| `SND_PCM_HW_PARAM_PERIOD_SIZE` | Periyot boyutu |
| `SND_PCM_HW_PARAM_PERIODS` | Periyot sayısı |

### 4.7 · `k2-surucu/alsa-native.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

### 4.8 · `k2-surucu/alsa-native.md`

| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | İç katman |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/pipewire-modern.md` | H1 | PipeWire Modern Sürücü |
| 2 | `k2-surucu/pipewire-modern.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/pipewire-modern.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/pipewire-modern.md` | H3 | PipeWire Mimarisi |
| 5 | `k2-surucu/pipewire-modern.md` | H3 | SPA Plugin Sistemi |
| 6 | `k2-surucu/pipewire-modern.md` | H3 | Grafik İşleme |
| 7 | `k2-surucu/pipewire-modern.md` | H3 | Real-Time Zamanlama |
| 8 | `k2-surucu/pipewire-modern.md` | H3 | Buffer Yönetimi |
| 9 | `k2-surucu/pipewire-modern.md` | H3 | Port ve Donanım Yönetimi |
| 10 | `k2-surucu/pipewire-modern.md` | H2 | API / Arayüz |
| 11 | `k2-surucu/pipewire-modern.md` | H2 | Performans Metrikleri |
| 12 | `k2-surucu/pipewire-modern.md` | H2 | Bağımlılıklar |
| 13 | `k2-surucu/pipewire-modern.md` | H2 | Durum: Implementasyon |
| 14 | `k2-surucu/alsa-native.md` | H1 | ALSA Native Sürücü |
| 15 | `k2-surucu/alsa-native.md` | H2 | Genel Bakış |
| 16 | `k2-surucu/alsa-native.md` | H2 | Teknik Detaylar |
| 17 | `k2-surucu/alsa-native.md` | H3 | ALSA Mimarisi |
| 18 | `k2-surucu/alsa-native.md` | H3 | PCM Aygıtları |
| 19 | `k2-surucu/alsa-native.md` | H3 | Donanım Parametreleri |
| 20 | `k2-surucu/alsa-native.md` | H3 | Buffer Yönetimi |
| 21 | `k2-surucu/alsa-native.md` | H3 | Period Size (Periyot Boyutu) |
| 22 | `k2-surucu/alsa-native.md` | H3 | MMAP Mod Implementasyonu |
| 23 | `k2-surucu/alsa-native.md` | H3 | XRUN Yönetimi |
| 24 | `k2-surucu/alsa-native.md` | H3 | Hardware Tasarım Dosyaları (HWDEP) |
| 25 | `k2-surucu/alsa-native.md` | H2 | API / Arayüz |
| 26 | `k2-surucu/alsa-native.md` | H2 | Performans Metrikleri |
| 27 | `k2-surucu/alsa-native.md` | H2 | Bağımlılıklar |
| 28 | `k2-surucu/alsa-native.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md`


### PipeWire Modern Sürücü

#### Genel Bakış

PipeWire, modern Linux ses yönlendirmesi için geliştirilen bir framework'tür. ALSA, PulseAudio ve JACK'ın yerini alarak tek bir unified API ile tüm ses ihtiyaçlarını karşılar. COREMUSIC, PipeWire SPA (Simple Plugin API) plugin mimarisini kullanarak yüksek performanslı ses işleme sağlar.

#### Teknik Detaylar

##### PipeWire Mimarisi

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

##### SPA Plugin Sistemi

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

##### Grafik İşleme

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

##### Real-Time Zamanlama

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

##### Buffer Yönetimi

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

##### Port ve Donanım Yönetimi

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

#### API / Arayüz

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

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.7ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 2% | 1.2% |
| Maks. Node | 1000+ | 1000+ |
| Değişim Süresi | < 1ms | 0.5ms |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libpipewire-0.3 | Sistem kütüphanesi |
| SPA SDK | PipeWire plugin sistemi |
| K1 Linux Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: PipeWire SDK entegrasyonu, temel node oluşturma
- **Faz 2**: SPA plugin implementasyonu
- **Faz 3**: Grafik optimizasyonu, zamanlama
- **Faz 4**: Donanım keşfi, çoklu cihaz desteği
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md`


### ALSA Native Sürücü

#### Genel Bakış

ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformunda minimum gecikme ile ses giriş/çıkışı sağlar. ALSA, PipeWire ile birlikte kullanılabildiği gibi tek başına da çalışabilir.

#### Teknik Detaylar

##### ALSA Mimarisi

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

##### PCM Aygıtları

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

##### Donanım Parametreleri

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

##### Buffer Yönetimi

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

##### Period Size (Periyot Boyutu)

Period size, kesme (interrupt) aralığını belirler:

```
Period Size = Buffer Size / Number of Periods

Örnek:
Buffer: 1024 samples
Periods: 4
Period Size: 256 samples @ 48kHz = 5.33ms

Latency: Period Size / SampleRate = 5.33ms
```

##### MMAP Mod Implementasyonu

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

##### XRUN Yönetimi

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

##### Hardware Tasarım Dosyaları (HWDEP)

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

#### API / Arayüz

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

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | PipeWire sürümü ve yapılandırması vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Ölçülmüş uçtan uca gecikme yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Yanlış eşleme (quantum/rate) durumunda örnek oranı dönüşümü (kaynak: `pipewire-modern`).
2. Daemon yeniden başlatıldığında akışların kopması (kaynak: `pipewire-modern`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k001-linux-rpi5/index]]` | ↑ | Linux çekirdek + RPi5 platform zemini | `.ai/architecture/k001-linux-rpi5/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↑ | Genel sürücü/buffer kuralları | `.ai/architecture/k014-surucu-yigin/index.md` |
| `[[../k015-platform-suruculeri/index]]` | ↔ | Windows/macOS eşdeğer sürücüleri | `.ai/architecture/k015-platform-suruculeri/index.md` |
| `[[../k017-uzak-bluetooth-usb/index]]` | ↓ | Ağ/BT/USB uç cihazları | `.ai/architecture/k017-uzak-bluetooth-usb/index.md` |

Yerel dosyalar:

- `[[linux-ses-yigini]]` — Linux Ses Yığını (ALSA Native)
- `[[pipewire-modern-yigin]]` — PipeWire Modern Yığın

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K016 · PipeWire Modern Yığın — SSOT: `.ai/architecture/k016-linux-ses/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
