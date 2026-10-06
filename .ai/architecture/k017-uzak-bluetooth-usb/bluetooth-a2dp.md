---
title: "Bluetooth A2DP - k017-uzak-bluetooth-usb"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# Bluetooth A2DP

> Klasör: `k017-uzak-bluetooth-usb` · Dosya: `bluetooth-a2dp.md`
> Sorumlu persona: `embedded-engineer` (Embedded Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` (1 satir / 6 bolum) — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.
## Genel Bakış

Kablosuz ses profili A2DP: Bluetooth ses akışı, codec desteği (SBC, LDAC, aptX HD, LC3), codec seçimi, LDAC ve LC3 codec detayları, packet yapısı, buffer yönetimi, A2DP state machine, API / arayüz, performans metrikleri ve bağımlılıklar. Kaynak dosyanın tamamı (L8–L250) aktarılmıştır.


## Kapsam ve Sınırlar

- **Kapsam:** A2DP profili, codec müzakeresi ve seçimi, packet yapısı, buffer, state machine, API, metrikler, implementasyon durumu.
- **Kapsam dışı:** USB Audio Class ve ağ (network) ses sürücüleri (→ [[usb-ve-ag-ses-suruculeri.md]]), Bluetooth radyo/donanım tarafı (→ [[../k035-ag-ve-bluetooth-ses/index]]).
- **Bağlı olduğu klasör:** [[index.md]] (Uzak / Bluetooth / USB Ses)
- **Çapraz referanslar:** [[../k035-ag-ve-bluetooth-ses/index]] · [[../k014-surucu-yigin/index]] · [[usb-ve-ag-ses-suruculeri.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi (`#` → `###`); her blokta kanıt satırı kaynak dosyayı ve gerçek satır aralığını (`L<başlangıç>-L<bitiş>`) gösterir. Bloklar kaynaktan değiştirilmeden kopyalanmıştır.

### bluetooth-a2dp.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` (250 satir)


#### Bluetooth A2DP Sürücüsü

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md - L8-L13

### Bluetooth A2DP Sürücüsü

#### Genel Bakış

Bluetooth A2DP (Advanced Audio Distribution Profile), kablosuz ses iletimi için standart profildir. COREMUSIC, yüksek kaliteli Bluetooth ses codecs'lerini (LDAC, aptX HD, LC3) destekleyerek kablosuz ses kalitesini artırır.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md - L14-L178

#### Teknik Detaylar

##### Bluetooth Ses Akışı

```
┌─────────────────────────────────────────────────────┐
│            Bluetooth A2DP Akışı                    │
│                                                     │
│  [Kaynak] → [Codec] → [L2CAP] → [HCI] → [RFCOMM]  │
│      ↓         ↓         ↓         ↓         ↓       │
│  [Audio]  [Encode]  [Packet]  [Transfer]  [Air]     │
│                                                     │
│         ↓ Air Interface (Bluetooth) ↓              │
│                                                     │
│  [RFCOMM] → [HCI] → [L2CAP] → [Codec] → [Sink]    │
│      ↓         ↓         ↓         ↓         ↓       │
│  [Receive] [Decode] [Reassemble] [Process] [Output] │
└─────────────────────────────────────────────────────┘
```

##### Codec Desteği

COREMUSIC aşağıdaki Bluetooth ses codecs'lerini destekler:

| Codec | Bit Hızı | Örnekleme Hızı | Bit Derinliği | Latency |
|-------|----------|----------------|---------------|---------|
| SBC | 328 kbps | 48 kHz | 16-bit | 100ms |
| LDAC | 990 kbps | 96 kHz | 24-bit | 200ms |
| aptX HD | 576 kbps | 48 kHz | 24-bit | 120ms |
| LC3 | 345 kbps | 48 kHz | 24-bit | 30ms |

##### Codec Seçimi

```cpp
// Desteklenen codec listesi
enum BluetoothCodec {
    CODEC_SBC,      // Varsayılan (her cihaz destekler)
    CODEC_LDAC,     // Sony (yüksek kalite)
    CODEC_APTX_HD,  // Qualcomm (yüksek kalite)
    CODEC_LC3,      // Bluetooth 5.2 (düşük gecikme)
    CODEC_AAC       // Apple (iyi kalite)
};

// Codec seçimi
BluetoothCodec selectCodec(const BluetoothDevice& device) {
    // Cihazın desteklediği codec'leri kontrol et
    if (device.supportsCodec(CODEC_LC3)) return CODEC_LC3;
    if (device.supportsCodec(CODEC_LDAC)) return CODEC_LDAC;
    if (device.supportsCodec(CODEC_APTX_HD)) return CODEC_APTX_HD;
    if (device.supportsCodec(CODEC_AAC)) return CODEC_AAC;
    return CODEC_SBC;
}
```

##### LDAC Codec Detayları

Sony LDAC, yüksek çözünürlüklü Bluetooth ses için:

```
LDAC Modları:
┌──────────────┬────────────┬──────────────┬────────────┐
│    Mod       │  Bit Hızı  │ Örnekleme    │ Kalite     │
├──────────────┼────────────┼──────────────┼────────────┤
│ Quality      │ 990 kbps   │ 96 kHz       │ En İyi     │
│ Standard     │ 660 kbps   │ 48 kHz       │ İyi        │
│ Mobile       │ 330 kbps   │ 44.1 kHz     │ Orta       │
└──────────────┴────────────┴──────────────┴────────────┘
```

##### LC3 Codec Detayları

Bluetooth 5.2 ile gelen LC3 (Low Complexity Communication Codec):

```
LC3 Özellikleri:
- Düşük gecikme: 30ms (10ms frame)
- Esnek bit hızı: 160-345 kbps
- Yüksek verimlilik: SBC'den %50 daha iyi
- Esnek örnekleme hızı: 8k-48kHz
```

##### Packet Yapısı

Bluetooth ses paketleri:

```cpp
// A2DP Media Packet
struct A2DPMediaPacket {
    uint8_t version;        // 2 bits
    uint8_t padding;        // 1 bit
    uint8_t extension;      // 1 bit
    uint8_t cc;             // 4 bits (codec dependent)
    uint8_t marker;         // 1 bit
    uint8_t pt;             // 7 bits (payload type)
    uint16_t sequenceNumber;
    uint32_t timestamp;
    uint32_t ssrc;
    // Payload (codec encoded data)
    uint8_t payload[];      // Değişken boyut
};

// Packet boyutu hesaplama
uint32_t calculatePacketSize(uint32_t frameSize, 
                              BluetoothCodec codec) {
    switch (codec) {
        case CODEC_SBC:    return 79;    // max
        case CODEC_LDAC:   return 690;   // 990kbps
        case CODEC_APTX_HD: return 680;  // 576kbps
        case CODEC_LC3:    return 155;   // 345kbps
        default:           return 79;
    }
}
```

##### Buffer Yönetimi

Bluetooth ses buffer yönetimi:

```cpp
// Bluetooth Jitter Buffer
struct BT_JitterBuffer {
    void** slots;
    uint32_t slotCount;     // Genellikle 3-5
    uint32_t latencyMs;     // 50-200ms
    bool adaptive;
    uint32_t jitterStats;   // Jitter istatistiği
};

// Buffer boyutu hesaplama
uint32_t calculateBTBuffer(BluetoothCodec codec, 
                            uint32_t targetLatency) {
    uint32_t frameSize;
    switch (codec) {
        case CODEC_SBC:    frameSize = 128; break;
        case CODEC_LDAC:   frameSize = 256; break;
        case CODEC_APTX_HD: frameSize = 256; break;
        case CODEC_LC3:    frameSize = 120; break;
        default:           frameSize = 128;
    }
    return (targetLatency * frameSize) / 1000;
}
```

##### A2DP State Machine

A2DP bağlantı yönetimi:

```
A2DP States:
┌─────────────────────────────────────────────┐
│                                             │
│  [Idle] → [Connecting] → [Configuring]     │
│                              ↓              │
│  [Disconnected] ← [Streaming] ← [Ready]    │
│                                             │
└─────────────────────────────────────────────┘

State Transitions:
- Idle → Connecting: Cihaz keşfi
- Connecting → Configuring: Codec seçimi
- Configuring → Ready: Yapılandırma tamam
- Ready → Streaming: Akış başlat
- Streaming → Disconnected: Bağlantı kesildi
```

#### API / Arayüz

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md - L179-L250

#### API / Arayüz

```cpp
class BluetoothA2DPDriver {
public:
    bool initialize();
    void shutdown();
    
    // Cihaz yönetimi
    std::vector<BTDevice> scanDevices() const;
    bool pairDevice(const std::string& deviceId);
    bool connectDevice(const std::string& deviceId);
    void disconnectDevice();
    
    // Codec yapılandırması
    bool setCodec(BluetoothCodec codec);
    BluetoothCodec getCurrentCodec() const;
    std::vector<BluetoothCodec> getSupportedCodecs(
        const std::string& deviceId) const;
    
    // Akış kontrolü
    bool startPlayback();
    bool stopPlayback();
    bool isStreaming() const;
    
    // Buffer yapılandırması
    bool setBufferSize(uint32_t frames);
    bool setLatency(uint32_t ms);
    
    // Durum
    BTConnectionState getConnectionState() const;
    uint32_t getSignalStrength() const;
    double getCurrentBitrate() const;
};

// Kullanım örneği
BluetoothA2DPDriver driver;
driver.initialize();

auto devices = driver.scanDevices();
if (!devices.empty()) {
    driver.connectDevice(devices[0].id);
    driver.setCodec(CODEC_LDAC);
    driver.setBufferSize(256);
    driver.startPlayback();
}
```

#### Performans Metrikleri

| Metrik | SBC | LDAC | aptX HD | LC3 |
|--------|-----|------|---------|-----|
| Bit Hızı | 328k | 990k | 576k | 345k |
| Latency | 100ms | 200ms | 120ms | 30ms |
| Kalite | İyi | Mükemmel | İyi | İyi |
| CPU | 1% | 3% | 2% | 1.5% |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| BlueZ | Linux Bluetooth stack |
| libbluetooth | Sistem kütüphanesi |
| K1 BT Core | İç katman |

#### Durum: Implementasyon

- **Faz 1**: SBC codec, temel A2DP
- **Faz 2**: LDAC, aptX HD desteği
- **Faz 3**: LC3 (Bluetooth 5.2)
- **Faz 4**: Buffer optimizasyonu, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | Bluetooth A2DP Sürücüsü | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | Teknik Detaylar | L14-L178 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/bluetooth-a2dp.md` | API / Arayüz | L179-L250 | ✓ verbatim |

## Belirsizlik Taraması (kaynak metin)

- Kaynaklarda `UNKNOWN` / `TODO` / `Belirlenecek` / `VERIFICATION REQUIRED` içeren satır **tespit edilmedi** (tam tarama).

## İlgili Dosyalar

[[index.md]] · [[usb-ve-ag-ses-suruculeri.md]] · [[../k035-ag-ve-bluetooth-ses/index]] · [[../k014-surucu-yigin/index]]

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault yedeği (`_backup/arch-2026-10-06_1057/architecture/k2-surucu/`) kanıtına dayanır; diskteki uygulama kodu ile çapraz doğrulama yapılmamıştır.
2. ⚠️ VERIFICATION REQUIRED — kaynak frontmatter `date: 2026-09-20` / `layer: K2` değerleri ile bu dosyanın `updated: 2026-10-06` değeri arasındaki fark üst merci onayı bekler.
