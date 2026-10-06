---
title: "Ağ Ses Sürücüleri - k035-ag-ve-bluetooth-ses"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Ağ Ses Sürücüleri

> Klasör: `k035-ag-ve-bluetooth-ses` · Dilim: D01 (k018–k035) · Dosya: `network-audio-drivers.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Ağ üzerinden ses aktarımının sürücü tarafı, paketleme ve metrikler.

Bu belge; D01 diliminin (Ağ ve Bluetooth Ses) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Ağ üzerinden ses aktarımının sürücü tarafı, paketleme ve metrikler.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Ağ ve Bluetooth Ses)
- **Çapraz referanslar:** [[../k034-usb-audio/usb-audio-class.md]] · [[../k032-latency-optimization/latency-optimization.md]] · [[../k031-buffer-management/buffer-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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

### index.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` (91 satır)

#### index.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § (giriş) — L1–L9

---
title: "K2 Sürücü Katmanı"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### K2 Sürücü Katmanı


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Genel Bakış` — L10–L12


K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DAC'ler, ağı ses cihazları) için platform-bağımsız bir soyutlama sağlar. Bu katman, gerçek zamanlı ses akışı için gereken düşük gecikmeli (low-latency) veri yollarını yönetir.

#### Mimari Konum

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Mimari Konum` — L14–L20


```
K0 (Donanım) → K1 (OS/Core) → K2 (Sürücü) → K3 (Ses Motoru)
```

K2 katmanı, K1'in sağladığı çekirdek hizmetleri (DMA, kesinti, bellek yönetimi) üzerine inşa edilir ve K3'ün DSP zincirine ham ses verisini iletir.

#### Kapsam ve Kategoriler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Kapsam ve Kategoriler` — L22–L39


##### Platform Sürücüleri
- **ASIO Drivers**: Windows profesyonel ses, Exclusive mode
- **WASAPI**: Windows Audio Session API, modern Windows ses
- **ALSA Native**: Linux çekirdek seviyesi ses
- **PipeWire**: Modern Linux ses yönlendirmesi
- **CoreAudio**: macOS/iOS ses altyapısı

##### Donanım Arabirimleri
- **USB Audio Class 2.0**: USB ses cihazları için evrensel protokol
- **Network Audio**: Dante, AVB, RAVENNA Protokolleri
- **Bluetooth A2DP**: Kablosuz ses iletimi (LDAC, aptX HD, LC3)

##### Mimari Bileşenler
- **Driver Stack**: Çok katmanlı sürücü yığını
- **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar
- **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi

#### Temel İlkeler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Temel İlkeler` — L41–L53


##### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety)
Tüm K2 kodu, kesme.Context içinde çalışabilir: bellek ayırma yasak, kilitlenme (blocking) yasak, sistem çağrısı yasak.

##### 2. Donanım Bağımsızlığı
Aynı API, ASIO, WASAPI, ALSA ve CoreAudio üzerinde çalışır. Üst katmanlar hangi sürücünün kullanıldığını bilmez.

##### 3. Minimum Gecikme
Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında.

##### 4. Hata Toleransı
Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır.

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Bağımlılıklar` — L55–L61


| Katman | İlişki |
|--------|--------|
| K0 | Donanım kaynaklarını kullanır (DMA, IRQ) |
| K1 | İşletim sistemi hizmetlerini çağırır |
| K3 | Ham ses verisini iletir |

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Performans Metrikleri` — L63–L71


| Metrik | Hedef |
|--------|-------|
| Round-trip Latency | < 0.5ms (ASIO Exclusive) |
| Buffer Boyutu | 32-64 sample @ 96kHz |
| CPU Kullanımı | < 5% (boşta) |
| Maksimum Kanal Sayısı | 128 giriş + 128 çıkış |
| Desteklenen Örnekleme Hızları | 44.1k, 48k, 88.2k, 96k, 176.4k, 192k, 352.8k, 384k |

#### Dosya Haritası

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Dosya Haritası` — L73–L87


| Dosya | İçerik |
|-------|--------|
| asio-drivers.md | ASIO Exclusive mode, low latency, buffer yönetimi |
| wasapi-exclusive.md | WASAPI Exclusive/Shared, Windows audio session |
| alsa-native.md | ALSA native audio, PCM aygıtları |
| pipewire-modern.md | PipeWire modern Linux ses, SPA pluginleri |
| core-audio-macos.md | CoreAudio macOS, AudioUnits, HAL |
| usb-audio-class.md | USB Audio Class 2.0, isochronous mode |
| network-audio-drivers.md | Dante, AVB, RAVENNA ağ sesi |
| bluetooth-a2dp.md | Bluetooth A2DP, LDAC, aptX HD, LC3 |
| driver-stack-mimari.md | Çok katmanlı sürücü yığını, HAL soyutlama |
| buffer-management.md | Ring buffer, double buffering, lock-free queues |
| latency-optimization.md | Latency zincirleri, buffer seçimi, RT planlama |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| [kaynakta `## Bağımlılıklar` bölümü yok — liste derlenemedi] | ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| // Overrun/underrun kontrolü | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L131 |
| void disconnectDevice(); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L168 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| ### 4. Hata Toleransı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L52 |
| Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L53 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| Ağ ses protokolleri, profesyonel ses endüstrisinde ses sinyallerinin Ethernet üzerinden iletilmesini sağlar. COREMUSIC, Dante, AVB ve RAVEN… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L12 |
| │ Latency     │ 0.15-5ms    │ 1-10ms      │ 0.25-5ms      │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L27 |
| │  [Clock Recovery]  [Audio Decode]  [Buffering]      │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L81 |
| - Donanım timestamp: < 1μs | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L108 |
| - Yazılım timestamp: < 100μs | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L109 |
| ### Jitter Buffer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L111 |
| Ağ sesi için jitter buffer kritiktir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L113 |
| // Jitter buffer yapısı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L116 |
| struct NetworkJitterBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L117 |
| void** slots;           // Buffer slotları | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L118 |
| uint32_t latencyMs;     // Hedef gecikme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L122 |
| // Adaptif jitter buffer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L126 |
| void updateJitterBuffer(NetworkJitterBuffer* buf, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L127 |
| uint32_t currentJitter) { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L128 |
| // Jitter istatistiğini güncelle | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L129 |
| // Buffer boyutunu ayarla | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L130 |
| K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DA… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L12 |
| - **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L38 |
| - **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L39 |
| ### 3. Minimum Gecikme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L49 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| │ Standart    │ Proprietary │ IEEE 802.1  │ AES67         │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L25 |
| ALC Networks tarafından AES67 standardı üzerine inşa edilmiştir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L72 |
| │  [Audio Source] → [AES67 Packet] → [Ethernet]     │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L77 |
| - **Faz 3**: RAVENNA/AES67 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L244 |
| ### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L43 |
| K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASI… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L91 |

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
| `network` | 21 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L24 |
| `latency` | 9 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L27 |
| `jitter` | 8 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L111 |
| `buffer` | 16 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` L81 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Teknik Detaylar | L14–L155 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## API / Arayüz | L157–L220 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Performans Metrikleri | L222–L229 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Bağımlılıklar | L231–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` | ## Durum: Implementasyon | L240–L246 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/network-audio-drivers.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: Dante SDK entegrasyonu
- **Faz 2**: AVB/TSN desteği
- **Faz 3**: RAVENNA/AES67
- **Faz 4**: Multicast ve PTP optimizasyonu
- **Tahmini Süre**: 4 hafta (160 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
