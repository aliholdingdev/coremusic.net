---
title: "PipeWire Modern Ses Yolu - k033-platform-ses-suruculeri"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# PipeWire Modern Ses Yolu

> Klasör: `k033-platform-ses-suruculeri` · Dilim: D01 (k018–k035) · Dosya: `pipewire-modern.md`
> Sorumlu persona: `windows-software-engineer` (Windows Software Mühendisi) · `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

PipeWire ile modern Linux ses yolu, ALSA köprüsü ve metrikler.

Bu belge; D01 diliminin (Platform Ses Sürücüleri) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** PipeWire ile modern Linux ses yolu, ALSA köprüsü ve metrikler.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Platform Ses Sürücüleri)
- **Çapraz referanslar:** [[../k029-cross-platform-api/cross-platform-api.md]] · [[../k030-driver-stack/driver-stack-mimari.md]] · [[../k020-linux-core/alsa-native.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### pipewire-modern.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` (253 satır)

#### pipewire-modern.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § (giriş) — L1–L9

---
title: "PipeWire Modern Sürücü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### PipeWire Modern Sürücü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Genel Bakış` — L10–L12


PipeWire, modern Linux ses yönlendirmesi için geliştirilen bir framework'tür. ALSA, PulseAudio ve JACK'ın yerini alarak tek bir unified API ile tüm ses ihtiyaçlarını karşılar. COREMUSIC, PipeWire SPA (Simple Plugin API) plugin mimarisini kullanarak yüksek performanslı ses işleme sağlar.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Teknik Detaylar` — L14–L162


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `API / Arayüz` — L164–L227


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Performans Metrikleri` — L229–L237


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency | 1ms | 0.7ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 2% | 1.2% |
| Maks. Node | 1000+ | 1000+ |
| Değişim Süresi | < 1ms | 0.5ms |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Bağımlılıklar` — L239–L245


| Bağımlılık | Tür |
|------------|-----|
| libpipewire-0.3 | Sistem kütüphanesi |
| SPA SDK | PipeWire plugin sistemi |
| K1 Linux Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Durum: Implementasyon` — L247–L253


- **Faz 1**: PipeWire SDK entegrasyonu, temel node oluşturma
- **Faz 2**: SPA plugin implementasyonu
- **Faz 3**: Grafik optimizasyonu, zamanlama
- **Faz 4**: Donanım keşfi, çoklu cihaz desteği
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)

### alsa-native.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` (246 satır)

#### alsa-native.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § (giriş) — L1–L9

---
title: "ALSA Native Sürücü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### ALSA Native Sürücü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Genel Bakış` — L10–L12


ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformunda minimum gecikme ile ses giriş/çıkışı sağlar. ALSA, PipeWire ile birlikte kullanılabildiği gibi tek başına da çalışabilir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Teknik Detaylar` — L14–L162


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `API / Arayüz` — L164–L220


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

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Performans Metrikleri` — L222–L230


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Bağımlılıklar` — L232–L238


| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| ### XRUN Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L131 |
| XRUN (buffer underrun/overrun) yönetimi kritiktir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L133 |
| // XRUN kontrolü | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L142 |
| // XRUN yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L195 |
| - **Faz 3**: XRUN yönetimi, performans optimizasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L244 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| bool setDefaultDevice(uint32_t deviceId); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L188 |
| int recover(int error); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L196 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| 3. Buffer boyutu uyumsuzluklarını çözme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L86 |
| // CPU affinity | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L99 |
| cpu_set_t cpuset; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L100 |
| CPU_ZERO(&cpuset); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L101 |
| CPU_SET(2, &cpuset);  // Çekirdek 2'ye ata | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L102 |
| pthread_setaffinity_np(pthread_self(), sizeof(cpuset), &cpuset); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L103 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L106 |
| PipeWire buffer yönetimi esnek ve yapılandırılabilir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L108 |
| Buffer Akışı: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L111 |
| **Buffer Parametreleri**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L120 |
| F32LE 48kHz   →  otomatik → S24LE 96kHz | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L136 |
| bool setCpuAffinity(int core); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L192 |
| uint32_t bufferSize; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L207 |
| config.bufferSize = 256; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L222 |
| ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformund… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L12 |
| - `SND_PCM_ACCESS_MMAP_INTERLEAVED`: Doğrudan bellek haritalama (en düşük latency) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L40 |
| // Buffer boyutu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L62 |
| snd_pcm_hw_params_set_buffer_size_near(handle, hw_params, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L63 |
| &bufferSize); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L64 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L73 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| config.bitsPerSample = 32; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L207 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| uint32_t createStream(const PipeWireStreamConfig& config); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L195 |
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
| `PipeWire` | 27 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L2 |
| `ALSA` | 18 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L12 |
| `epoll` | 0 | [kaynakta eşleşme yok] |
| `buffer` | 37 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` L86 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## Teknik Detaylar | L14–L162 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## API / Arayüz | L164–L227 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## Performans Metrikleri | L229–L237 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## Bağımlılıklar | L239–L245 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` | ## Durum: Implementasyon | L247–L253 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Teknik Detaylar | L14–L162 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## API / Arayüz | L164–L220 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Performans Metrikleri | L222–L230 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Bağımlılıklar | L232–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Durum: Implementasyon | L240–L246 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/pipewire-modern.md` § `Durum: Implementasyon` — L247–L253


- **Faz 1**: PipeWire SDK entegrasyonu, temel node oluşturma
- **Faz 2**: SPA plugin implementasyonu
- **Faz 3**: Grafik optimizasyonu, zamanlama
- **Faz 4**: Donanım keşfi, çoklu cihaz desteği
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
