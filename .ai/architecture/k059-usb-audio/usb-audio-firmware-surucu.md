---
title: "USB Audio Firmware & Sürücü Katmanı — Yedek Kaynak Entegrasyonu"
type: architecture
category: architecture
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# USB Audio Firmware & Sürücü Katmanı — Yedek Kaynak Entegrasyonu

## §1 Amaç & Kapsam

Bu belge, `k059-usb-audio` klasörünün **üçüncü dosyasıdır** ve klasördeki HENÜZ KULLANILMAMIŞ iki yedek kaynağı vault'a taşır:

- **Birincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/firmware/usb-audio-firmware.md` → **firmware katmanı** (mimari, descriptor, isochronous, clock recovery, ring buffer, format dönüşümü, control requests, latency, derleme, test).
- **İkincil kaynak:** `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` → **OS sürücü katmanı** (UAC2 sürücü mimarisi, adaptif/async mod, clock source API'si, format/endpoint/bandwidth/buffer yönetimi, hata yönetimi, sürücü API'si, performans metrikleri).

Kardeş dosya [[../k059-usb-audio/usb-audio-yolu]] **UAC2 yolunu ve descriptor'ü** anlatır (kaynak: `k1-donanim/usb-audio.md`); bu dosya onu **tekrar etmez** — bu dosyanın konusu **FIRMWARE + OS SÜRÜCÜ** katmanıdır ve çelişen iddiaları satır numarasıyla işaretler.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Firmware mimarisi, kaynak kod ağacı, UAC2 descriptor hiyerarşisi | UAC2 yolunun bant genişliği / DoP / USB-C anlatımı → [[../k059-usb-audio/usb-audio-yolu]] |
| Isochronous transfer mekanizması, clock recovery (PI), ring buffer | I2S/TDM zamanlaması → [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] |
| Audio format dönüşümü, USB control requests, latency kırılımı | XU316 pin/saat devresi → [[../k058-xmos-xu316/xu316-entegrasyon]] |
| Derleme, descriptor doğrulama (Wireshark/lsusb), Linux test | DAC/ADC iç dönüşüm → [[../k055-ak4458-dac/ak4458-dac-rehberi]] · [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] |
| OS sürücü katmanı: UAC2 mimarisi, modlar, clock source, API, performans | Fiziksel USB konektör envanteri (k066) — hedef dosya diskte yok, §10.1 |

**Kural:** kaynakta olmayan hiçbir komut / sürüm / bağımlılık / endpoint adresi üretilmez; doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir (Zero-Hallucination).

## §2 Kaynak Kapsamı

| # | Kaynak dosya (salt-okunur) | Rol | Satır | İşlendiği bölüm |
|---|---------------------------|-----|-------|-----------------|
| S1 | `_backup/arch-2026-10-06_1057/architecture/firmware/usb-audio-firmware.md` | **Birincil** — Firmware katmanı | 505 | §3 · §4 · §8 · §9 |
| S2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` | **İkincil** — OS sürücü katmanı | 254 | §5 · §7 · §13 |
| S3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | Çapraz kontrol — UAC2 yolu | 190 | §7 çelişki kaydı |
| S4 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` | Çapraz kontrol — XU316 çip | 99 | §7 çelişki kaydı |

**Stil kaynağı (birebir ön-yüz):** `.ai/architecture/k059-usb-audio/index.md` · [[../k059-usb-audio/usb-audio-yolu]] — frontmatter 7 alan, §1–§13 iskeleti, wiki-link envanteri ve doğrulama protokolü bu dosyalardan alınmıştır.

**Kaynak durum tabloları (S1 §Durum / S2 §Durum):** firmware modüllerinin tamamı `Planlandı` (S1 L496–L505); sürücü katmanı 4 fazlı plan + `Tahmini Süre: 3 hafta (120 adam-saat)` (S2 L248–L254). Buna karşılık S3 `Durum: 🟢 Hazır` (S3 L183) → **durum çelişkisi §7-11.**

## §3 Firmware Mimarisi & Descriptor

### §3.1 Firmware Mimarisi (kaynak: S1 L14–L60)

```
┌─────────────────────────────────────────────────────────────────┐
│              USB AUDIO CLASS 2.0 FIRMWARE                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              USB 2.0 High-Speed Controller           │      │
│  │              (480 Mbps, 125μs frame rate)            │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              USB Audio Class 2.0 Stack               │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Control Endpoint (EP0)                     │    │      │
│  │  │  - Device descriptor                        │    │      │
│  │  │  - Audio control interface                  │    │      │
│  │  │  - Clock source/routing                     │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Isochronous IN Endpoint (EP1)              │    │      │
│  │  │  - Microphone input                          │    │      │
│  │  │  - Adaptive/Asynchronous sync               │    │      │
│  │  │  - 16/24/32-bit PCM                         │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Isochronous OUT Endpoint (EP2)             │    │      │
│  │  │  - Speaker output                           │    │      │
│  │  │  - Adaptive/Asynchronous sync               │    │      │
│  │  │  - 16/24/32-bit PCM                         │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Interrupt Endpoint (EP3)                   │    │      │
│  │  │  - Notification (sample rate change)        │    │      │
│  │  │  - Status updates                            │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Audio Streaming Pipeline                │      │
│  │  USB ──► Ring Buffer ──► Format Convert ──► DSP     │      │
│  │  ◄── Ring Buffer ◄── Format Convert ◄── DSP        │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

Kaynak-derleme özeti (S1 L12): `Isochronous transfer modu ile jitter-free audio streaming, adaptive USB frame senkronizasyonu ve çoklu format desteği sağlar. XMOS XU316 üzerinde optimized edilmiş USB Audio endpoint handling ile <1ms round-trip latency hedeflenmektedir.`

Endpoint adresleri (kaynak değeri, S1 L29–L50): `EP0` control · `EP1` Isochronous IN (mikrofon) · `EP2` Isochronous OUT (hoparlör) · `EP3` interrupt (sample rate change bildirimi). **Kaynakta başka endpoint adresi yoktur.**

### §3.2 Kaynak Kod Yapısı (kaynak: S1 L62–L87)

```
usb_audio_firmware/
├── src/
│   ├── usb_audio_core.xc           # Ana USB Audio işleyici
│   ├── usb_descriptors.xc          # USB tanımlayıcılar
│   ├── usb_audio_control.xc        # Control endpoint handler
│   ├── usb_audio_streaming.xc      # Isochronous transfer
│   ├── usb_audio_clock.xc          # Clock source management
│   ├── usb_audio_format.xc         # Format conversion
│   ├── ring_buffer.xc              # Audio ring buffer
│   └── usb_audio_main.xc           # Ana program
│
├── include/
│   ├── usb_audio_config.h          # Konfigürasyon
│   ├── usb_audio_types.h           # Veri tipleri
│   └── usb_audio_registers.h       # USB register tanımları
│
├── descriptors/
│   ├── usb_audio_20_descriptors.h  # UAC2.0 descriptors
│   ├── usb_device_descriptor.h     # Device descriptor
│   └── usb_config_descriptor.h     # Config descriptor
│
└── Makefile
```

### §3.3 UAC2 Descriptor Hiyerarşisi (kaynak: S1 L91–L127)

```
Device Descriptor (bDeviceClass=0xEF, bDeviceSubClass=0x02)
├── Configuration Descriptor
│   ├── Interface 0: Audio Control
│   │   ├── Header Descriptor (UAC2.0)
│   │   ├── Clock Source Descriptor (Internal PLL)
│   │   ├── Input Terminal (USB Streaming)
│   │   ├── Output Terminal (Speaker)
│   │   ├── Feature Unit (Volume, Mute)
│   │   ├── Input Terminal (Microphone)
│   │   └── Output Terminal (USB Streaming)
│   │
│   ├── Interface 1: Audio Streaming OUT
│   │   ├── Alt Setting 0: Zero Bandwidth
│   │   └── Alt Setting 1: Operational
│   │       ├── Class-Specific AS Descriptor
│   │       ├── Format Type I (PCM)
│   │       │   - 16-bit, 44.1/48/96/192 kHz
│   │       │   - 24-bit, 44.1/48/96/192 kHz
│   │       │   - 32-bit, 44.1/48/96/192 kHz
│   │       └── Standard EP Descriptor (ISO OUT)
│   │
│   └── Interface 2: Audio Streaming IN
│       ├── Alt Setting 0: Zero Bandwidth
│       └── Alt Setting 1: Operational
│           ├── Class-Specific AS Descriptor
│           ├── Format Type I (PCM)
│           └── Standard EP Descriptor (ISO IN)
│
└── String Descriptors
    ├── Manufacturer: "COREMUSIC"
    ├── Product: "COREMUSIC Audio Interface"
    ├── Serial: Unique device ID
    └── Audio Control Interface Strings
```

### §3.4 Descriptor Karşılaştırması — `usb-audio-yolu.md` §3 ↔ firmware §3.3

> Çelişki #6 adayı. Sol sütun: `usb-audio-yolu.md` L42–L49 (= S3 L32–L60). Sağ sütun: S1 L93–L127.

| Öğe | usb-audio-yolu.md §3 (UAC2 yolu) | firmware S1 L93–L127 | Durum |
|---|---|---|---|
| Device Descriptor | `Device Descriptor` (sınıf yazmıyor) | `bDeviceClass=0xEF, bDeviceSubClass=0x02` | ⚠️ VERIFICATION REQUIRED |
| Interface 0 AC | AC Header · Input Terminal (USB Streaming) · Output Terminal (Speaker) · Feature Unit | + `Clock Source Descriptor (Internal PLL)` · + `Input Terminal (Microphone)` · + `Output Terminal (USB Streaming)` | ⚠️ VERIFICATION REQUIRED (3 fazladan düğüm) |
| Interface 1 AS OUT | AS General · Format Type I (PCM) · **Format Type III (DSD)** · Standard Endpoint (Iso OUT) | Alt 0/1 · CS AS · **Format Type I (PCM) yalnız** · Standard EP (ISO OUT) — Format Type III YOK | ⚠️ VERIFICATION REQUIRED (DSD descriptor var/yok) |
| Alt Setting yapısı | Yok (tek blok) | `Alt Setting 0: Zero Bandwidth` + `Alt Setting 1: Operational` | ⚠️ VERIFICATION REQUIRED (farklı gösterim) |
| Interface 2 AS IN | AS General · Format Type I · Standard Endpoint (Iso IN) | Alt 0/1 · CS AS · Format Type I · Standard EP (ISO IN) | ✅ uyuşuyor (yapı) |
| Interface 3 | `MIDI Streaming - Optional` | **YOK** | ⚠️ VERIFICATION REQUIRED |
| String: Product | `"COREMUSIC USB DAC"` | `"COREMUSIC Audio Interface"` | ⚠️ VERIFICATION REQUIRED (farklı ürün adı) |
| String: Serial | `"CM-001"` | `Unique device ID` | ⚠️ VERIFICATION REQUIRED |
| Manufacturer | `"COREMUSIC"` | `"COREMUSIC"` | ✅ uyuşuyor |

**Sonuç:** hiyerarşi birebir uyuşmuyor — 7 fark / 2 örtüşme. Karar: hangi descriptor'ün üretildiği doğrulanana kadar **ikisi de bağlayıcı değildir** → `⚠️ VERIFICATION REQUIRED`; etkilediği dosya: `usb-audio-yolu.md` §3 + bu dosya §3.3.

## §4 Isochronous / Clock Recovery / Ring Buffer

> Bu bölümün tamamı birincil kaynaktan (S1) **kaynak değeriyle** aktarılmıştır; kod blokları kaynaktaki haliyle korunmuştur.

### §4.1 Isochronous Transfer Mekanizması (kaynak: S1 L129–L185)

```xc
// USB Isochronous endpoint handling
// Her USB frame'de (125μs @ High-Speed) sabit miktarda veri transferi

// EP2 - Isochronous OUT (Speaker output)
// Her frame: 48 samples @ 48kHz = 1ms'de 48 sample
// Packet size: 48 * 4 bytes (32-bit) = 192 bytes

void usb_audio_out_handler(chan c_audio_out) {
    unsigned char audio_packet[192];  // 48 samples * 4 bytes
    int samples_received;

    while (1) {
        // USB frame başına bir kez çağrılır
        // 125μs High-Speed frame rate
        samples_received = usb_iso_out_receive(audio_packet, 192);

        if (samples_received > 0) {
            // Ring buffer'a yaz
            for (int i = 0; i < samples_received / 4; i++) {
                int32_t sample;
                sample = (audio_packet[i*4] << 0) |
                         (audio_packet[i*4+1] << 8) |
                         (audio_packet[i*4+2] << 16) |
                         (audio_packet[i*4+3] << 24);
                c_audio_out <: sample;
            }
        }
    }
}

// EP1 - Isochronous IN (Microphone input)
// Her frame: 48 samples @ 48kHz = 1ms'de 48 sample
// Packet size: 48 * 4 bytes (32-bit) = 192 bytes

void usb_audio_in_handler(chan c_audio_in) {
    unsigned char audio_packet[192];
    int sample_count = 0;

    while (1) {
        // Ring buffer'dan oku
        for (int i = 0; i < 48; i++) {
            int32_t sample;
            c_audio_in :> sample;
            audio_packet[i*4] = sample & 0xFF;
            audio_packet[i*4+1] = (sample >> 8) & 0xFF;
            audio_packet[i*4+2] = (sample >> 16) & 0xFF;
            audio_packet[i*4+3] = (sample >> 24) & 0xFF;
        }

        // USB frame başına gönder
        usb_iso_in_send(audio_packet, 192);
    }
}
```

Kaynak-not (S1 L133): `Her USB frame'de (125μs @ High-Speed) sabit miktarda veri transferi`.

### §4.2 Clock Recovery & Sync (kaynak: S1 L187–L263)

```xc
// USB adaptive clock recovery
// USB SOF (Start of Frame) ile audio clock senkronizasyonu

// Asynchronous mode: Device's own clock, host adapts
// Adaptive mode: Host's clock, device adapts
// Synchronous mode: Shared clock

typedef enum {
    SYNC_MODE_ASYNCHRONOUS,
    SYNC_MODE_ADAPTIVE,
    SYNC_MODE_SYNCHRONOUS
} sync_mode_t;

// Clock recovery PLL
// USB SOF pulse'ı ile audio MCLK senkronizasyonu
void clock_recovery_loop(chan c_sof, chan c_audio_clk) {
    timer t;
    unsigned sof_timestamp;
    unsigned last_sof = 0;
    unsigned sof_period;
    unsigned mclk_period;
    int32_t error;
    int32_t integral = 0;
    int32_t kp = 10;   // Proportional gain
    int32_t ki = 1;    // Integral gain

    while (1) {
        select {
            case c_sof :> sof_timestamp:
                // USB SOF pulse (1ms periyot)
                if (last_sof != 0) {
                    sof_period = sof_timestamp - last_sof;

                    // Beklenen period: 1ms @ 48MHz = 48000 cycles
                    // Actual period: measured

                    // PLL error hesaplama
                    error = sof_period - EXPECTED_SOF_PERIOD;

                    // PI controller
                    integral += error;
                    int32_t adjustment = (kp * error) + (ki * integral);

                    // Audio clock PLL ayarla
                    c_audio_clk <: adjustment;
                }
                last_sof = sof_timestamp;
                break;

            case c_audio_clk :> mclk_period:
                // MCLK period ölçümü
                break;
        }
    }
}

// Sample rate detection
int detect_sample_rate(unsigned sof_period) {
    // USB SOF period'dan sample rate hesapla
    // 1ms frame'de kaç sample sığar?

    if (sof_period >= 44 && sof_period <= 45) {
        return 44100;
    } else if (sof_period >= 48 && sof_period <= 49) {
        return 48000;
    } else if (sof_period >= 96 && sof_period <= 97) {
        return 96000;
    } else if (sof_period >= 192 && sof_period <= 193) {
        return 192000;
    }

    return 48000;  // Default
}
```

Üç mod kaynakta tanımlıdır (`SYNC_MODE_ASYNCHRONOUS` / `SYNC_MODE_ADAPTIVE` / `SYNC_MODE_SYNCHRONOUS`, S1 L197–L201); **hangi modun üretimde seçildiği kaynakta yazılmamıştır** → `⚠️ VERIFICATION REQUIRED` (§7-3).

### §4.3 Ring Buffer Implementasyonu (kaynak: S1 L265–L317)

```xc
// Lock-free ring buffer for USB audio
// Producer: USB endpoint handler
// Consumer: DSP processing chain

#define RING_BUFFER_SIZE 4096  // Must be power of 2
#define RING_BUFFER_MASK (RING_BUFFER_SIZE - 1)

typedef struct {
    int32_t buffer[RING_BUFFER_SIZE];
    volatile unsigned write_ptr;
    volatile unsigned read_ptr;
} ring_buffer_t;

// Producer: USB endpoint'ten ses verisi yazma
void ring_buffer_write(ring_buffer_t *rb, int32_t sample) {
    unsigned next_write = (rb->write_ptr + 1) & RING_BUFFER_MASK;

    // Buffer dolu mu kontrol et
    if (next_write == rb->read_ptr) {
        // Buffer dolu - sample atla (drop)
        return;
    }

    rb->buffer[rb->write_ptr] = sample;
    // Memory barrier - compiler optimization'a karşı
    __sync_synchronize();
    rb->write_ptr = next_write;
}

// Consumer: DSP processing için ses verisi okuma
int ring_buffer_read(ring_buffer_t *rb, int32_t *sample) {
    if (rb->read_ptr == rb->write_ptr) {
        // Buffer boş
        return 0;
    }

    *sample = rb->buffer[rb->read_ptr];
    rb->read_ptr = (rb->read_ptr + 1) & RING_BUFFER_MASK;
    return 1;
}

// Buffer durum kontrolü
int ring_buffer_level(ring_buffer_t *rb) {
    int level = rb->write_ptr - rb->read_ptr;
    if (level < 0) {
        level += RING_BUFFER_SIZE;
    }
    return level;
}
```

Kaynak değerleri: `RING_BUFFER_SIZE 4096` (power of 2) · dolu ise **sample drop** (S1 L287) · `__sync_synchronize()` memory barrier (S1 L293).

### §4.4 Audio Format Conversion (kaynak: S1 L319–L352)

```xc
// Format dönüşüm fonksiyonları
// USB'den gelen veriyi DSP formatına çevirme

// 16-bit PCM → 32-bit float (DSP için)
void convert_pcm16_to_float(const int16_t *input, float *output, int count) {
    for (int i = 0; i < count; i++) {
        output[i] = (float)input[i] / 32768.0f;
    }
}

// 24-bit PCM → 32-bit float
void convert_pcm24_to_float(const uint8_t *input, float *output, int count) {
    for (int i = 0; i < count; i++) {
        int32_t sample = (input[i*3] << 8) |
                         (input[i*3+1] << 16) |
                         (input[i*3+2] << 24);
        output[i] = (float)sample / 8388608.0f;
    }
}

// 32-bit float → 16-bit PCM
void convert_float_to_pcm16(const float *input, int16_t *output, int count) {
    for (int i = 0; i < count; i++) {
        float sample = input[i] * 32768.0f;
        // Clipping
        if (sample > 32767.0f) sample = 32767.0f;
        if (sample < -32768.0f) sample = -32768.0f;
        output[i] = (int16_t)sample;
    }
}
```

### §4.5 USB Audio Control Requests (kaynak: S1 L354–L406)

```c
// USB Audio Class 2.0 control requests
// SET_CUR, GET_CUR, GET_MIN, GET_MAX, GET_RES

// Volume kontrolü
typedef struct {
    int16_t current_volume;   // dB (Q1.14 format)
    int16_t min_volume;       // -60 dB
    int16_t max_volume;       // 0 dB
    int16_t resolution;       // 1 dB steps
} volume_control_t;

// Sample rate değiştirme
int set_sample_rate(uint32_t sample_rate) {
    // Clock source descriptor'a bildir
    // PLL'yi yeniden yapılandır
    // Ring buffer'ları sıfırla

    switch (sample_rate) {
        case 44100:
            configure_pll(44100);
            break;
        case 48000:
            configure_pll(48000);
            break;
        case 96000:
            configure_pll(96000);
            break;
        case 192000:
            configure_pll(192000);
            break;
        default:
            return -1;  // Desteklenmeyen sample rate
    }

    // USB interrupt endpoint ile host'a bildir
    notify_sample_rate_change(sample_rate);
    return 0;
}

// Mute kontrolü
void set_mute(uint8_t channel, uint8_t mute_state) {
    if (channel == 0) {
        // Master mute
        master_mute = mute_state;
    } else {
        // Channel-specific mute
        channel_mute[channel - 1] = mute_state;
    }
}
```

Control request kümesi (S1 L358): `SET_CUR, GET_CUR, GET_MIN, GET_MAX, GET_RES`. Volume aralığı: `-60 dB … 0 dB`, `1 dB steps`, `Q1.14` (S1 L362–L365). `set_sample_rate()` yalnız `44100/48000/96000/192000` kabul eder; diğerleri `-1` (S1 L374–L388) → **firmware sample-rate kapsamı 192 kHz ile sınırlıdır** (§7-4).

### §4.6 Latency Optimization (kaynak: S1 L408–L430)

```
USB Audio Round-Trip Latency Breakdown:

┌─────────────────────────────────────────────────────┐
│ Component              │ Latency (μs) │ % of Total │
├────────────────────────┼──────────────┼────────────┤
│ USB Frame Processing   │ 125          │ 25%        │
│ Ring Buffer            │ 50           │ 10%        │
│ Format Conversion      │ 20           │ 4%         │
│ DSP Processing         │ 100          │ 20%        │
│ I2S TX                 │ 50           │ 10%        │
│ DAC Processing         │ 50           │ 10%        │
│ ADC Processing         │ 50           │ 10%        │
│ I2S RX                 │ 50           │ 10%        │
│ USB IN Transfer        │ 50           │ 10%        │
├────────────────────────┼──────────────┼────────────┤
│ TOTAL                  │ < 500        │ 100%       │
└────────────────────────┴──────────────┴────────────┘

Hedef: < 1ms round-trip latency
```

**Kaynak-içi aritmetik tutarsızlık (Zero-Hallucination ile işaretli):** satırların toplamı `125+50+20+100+50+50+50+50+50 = 545 μs` ve yüzdelik toplamı `%109`; buna karşılık `TOTAL < 500 μs · 100%` yazılmıştır → `⚠️ VERIFICATION REQUIRED` (§7-8, §12-8).

## §5 OS Sürücü Katmanı (k2-surucu kaynağı)

> Bu bölümün tamamı ikincil kaynaktan (S2: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md`) **kaynak değeriyle** aktarılmıştır; kod blokları kaynaktaki haliyle korunmuştur. Satır indeksi §13'tedir.

### §5.1 UAC2 Mimarisi (S2 L16–L38)

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

Katmanlar (kaynak): `COREMUSIC Engine (K3)` → `USB Audio Class 2.0 Driver` (Isoch Endpoint · Control Request · Clock Source) → `USB Host Controller Driver` (xHCI · EHCI) → `USB Hardware`.

### §5.2 Isochronous Transfer (S2 L40–L60)

USB ses, isochronous transfer modunu kullanır:

**Isochronous Transfer Özellikleri** (S2 L44–L48):
- Zaman duyarlı (time-sensitive) veri transferi
- Garantili bandwidth
- Hata düzeltme yok (veya sınırlı)
- Her frame'de sabit boyut veri

**Frame Yapısı** (S2 L50–L60):

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

### §5.3 Adaptive ve Async Modlar (S2 L62–L74)

UAC2 iki zamanlama modu destekler (S2 L66–L69):

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| **Adaptive** | USB host saatine senkronize | Varsayılan mod |
| **Async** | Device kendi saatini kullanır | Profesyonel cihazlar |

**Async Mod Avantajları** (S2 L71–L74): daha düşük jitter · daha iyi clock recovery · profesyonel ses cihazları için tercih edilen.

**Mod seçimi belirsizliği:** S2 `Adaptive = Varsayılan mod` der; S3 ise `Async Transfer | Yes (device-sync)` (S3 L23) ve `Feedback | Adaptive/Synchronous` (S3 L24) der; S1 ise iki ucu da `Adaptive/Asynchronous sync` (S1 L37, L43) olarak etiketler ve üç modu da tanımlar. **Üretimde hangi modda çalışıldığı kaynaklarda tutarlı değildir** → `⚠️ VERIFICATION REQUIRED` (§7-3).

### §5.4 Clock Source Yönetimi (S2 L76–L96)

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

API imzaları (kaynak değeri): `UAC2_GetClockSources` · `UAC2_SetClockSource` · `UAC2_GetClockFrequency` · `USB_AC2_CLOCK_SOURCE clocks[10]`. **Bu API'nin hangi kütüphanede olduğu kaynakta yazmamıştır** → `⚠️ VERIFICATION REQUIRED`.

### §5.5 Format Desteği (S2 L98–L106)

| Format | Bit Derinliği | Örnek Hızı | Kanal |
|--------|---------------|------------|-------|
| PCM | 16, 24, 32 | 44.1k-384k | 1-32 |
| IEEE Float | 32 | 44.1k-192k | 1-32 |
| DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8 |

### §5.6 Endpoint Yapılandırması (S2 L108–L120)

```
USB Audio Device
├── Input Terminal (Mikrofon)
│   └── Input Endpoint (Isoch IN)
├── Output Terminal (Hoparlör)
│   └── Output Endpoint (Isoch OUT)
├── Feature Unit (Volume, Mute)
└── Clock Source (Internal/External)
```

Kaynakta endpoint **adresi yoktur** (yalnız `Isoch IN` / `Isoch OUT` yönü) → endpoint adresi için tek kaynak S1 §3.1'dir (`EP0`–`EP3`).

### §5.7 Bandwidth Yönetimi (S2 L122–L136)

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

**Çelişki #1 (kaynak-içi dahil):** `20% = 96 Mbps` iç tutarlı (480 × 0,20 = 96) ✓; `~1.5 Mbps` iç tutarlı (16 × 48000 × 2 = 1,536 Mbps) ✓; ancak `~24 Mbps (24-bit 192kHz 8ch)` rakamı aritmetikle uyuşmuyor (`24 × 192000 × 8 = 36,864 Mbps`) ve S3'ün `8ch × 32bit × 192kHz = 49.152Mbps` / `Utilization 81.9% (max)` değerleriyle de çelişiyor → `⚠️ VERIFICATION REQUIRED` (§7-1).

### §5.8 Buffer Yönetimi (S2 L138–L163)

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

### §5.9 Hata Yönetimi (S2 L165–L174)

| Hata | Neden | Çözüm |
|------|-------|-------|
| `USB_ERROR_STALL` | Transfer durduruldu | Endpoint'i resetle |
| `USB_ERROR_NAK` | Cihaz meşgul | Yeniden dene |
| `USB_ERROR_TIMEOUT` | Zaman aşımı | Bandwidth'i artır |
| `USB_ERROR_OVERFLOW` | Buffer taştı | Buffer boyutunu artır |

### §5.10 API / Arayüz (S2 L176–L228)

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

### §5.11 Performans Metrikleri (S2 L230–L238)

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (Async) | 1ms | 0.9ms |
| Latency (Adaptive) | 3ms | 2.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.5% |
| Maks. Kanal | 32 | 32 |

**Çelişki #2 (latency):** S3 `Latency | < 1ms` (S3 L25) ve `Latency: < 1ms measured` (S3 L189) koşulsuz der; S2 ise `Adaptive` modda `2.8ms` ölçmüştür → `<1ms` yalnız `Async` modda geçerlidir (`0.9ms`) → `⚠️ VERIFICATION REQUIRED` (§7-2).

### §5.12 Faz Planı (S2 L248–L254)

- **Faz 1**: USB Audio Class keşfi, temel yapılandırma
- **Faz 2**: Isochronous transfer implementasyonu
- **Faz 3**: Async mod, clock recovery
- **Faz 4**: DSD desteği, hata yönetimi
- **Tahmini Süre**: 3 hafta (120 adam-saat)

## §6 Bağımlılıklar

### §6.1 Firmware bağımlılıkları (kaynak: S1 L484–L492)

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_xua | >= 4.x | USB Audio Class kütüphanesi |
| lib_usb | >= 3.x | USB stack |
| lib_locks | >= 1.x | Channel synchronization |
| lib_xcore_math | >= 1.x | Math operations |
| lib_logging | >= 1.x | Debug logging |

### §6.2 Sürücü katmanı bağımlılıkları (kaynak: S2 L240–L246)

| Bağımlılık | Tür |
|------------|-----|
| libusb | Sistem kütüphanesi |
| USB Host Controller Driver | Çekirdek |
| K1 USB Core | İç katman |

### §6.3 UAC2 yolu bağımlılıkları (çapraz kontrol, S3 L172–L179)

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Ana bileşen | UAC2 firmware |
| K1 Konnektörler | Bağlantı | USB-C connector |
| K2 OS/Sürücüler | Üst | USB enumeration |
| K1 Güç Kaynağı | Alt | +5V VBUS |

**Uyuşma:** `lib_xua` S1 L488 ile S3 L185 (`lib_xua (XMOS open-source) kullanılıyor`) ve S4 L96 (`lib_xua` kütüphanesi mevcut) birebir örtüşür ✅. **Çelişki:** `lib_usb` (S1) ↔ `libusb` (S2) — iki farklı ad; hangisinin kullanıldığı `⚠️ VERIFICATION REQUIRED` (§7-12).

## §7 Kenar Durumlar & Çelişki Kaydı

> Biçim: `# | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar/⚠️ VERIFICATION REQUIRED | Etkilediği dosya`.
> A = UAC2 yolu (S3 = `k1-donanim/usb-audio.md`, birebir `usb-audio-yolu.md` içeriği) veya S4 (xmos-xu316). B = firmware (S1) veya sürücü (S2).

| # | İddia A (kaynak+satır) | İddia B (kaynak+satır) | Fark | Etki | Karar / ⚠️ VERIFICATION REQUIRED | Etkilediği dosya |
|---|---|---|---|---|---|---|
| 1 | **Bant genişliği:** `Audio Bandwidth \| 8ch × 32bit × 192kHz = 49.152Mbps` + `Utilization \| 81.9% (max)` + `Bandwidth \| 480Mbps / 8 = 60MB/s` (S3 L86–L88) | `Isochronous Bandwidth: 20% = 96 Mbps` · `Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch)` · `Headroom: ~72 Mbps` (S2 L127–L130) | 49,152 Mbps ↔ ~24 Mbps (Ayni 8ch senaryo; 24-bit×192k×8ch = 36,864 Mbps → ikisi de aritmetikle uyuşmuyor) · pay 60 MB/s ↔ 96 Mbps · util. %81,9 ↔ %25 (24/96) | Bant planı ve iso bütçesi | ⚠️ VERIFICATION REQUIRED — üç değer de birbirini doğrulamıyor; 49,152/60 = %81,92 yalnız **birim karıştırılarak** (Mbps ÷ MB/s) bulunur | `usb-audio-yolu.md` §4 · bu dosya §5.7 |
| 2 | **Latency:** `Latency \| < 1ms` (S3 L25) · `Latency: < 1ms measured` (S3 L189) | `Latency (Async) Hedef 1ms / Gerçek 0.9ms` · `Latency (Adaptive) Hedef 3ms / Gerçek 2.8ms` (S2 L234–L235) | Koşulsuz `<1ms` ↔ mod-bağımlı `0.9ms / 2.8ms` | Latency taahhüdü, ölçüm koşulu | ⚠️ VERIFICATION REQUIRED — `<1ms` yalnız Async modda geçerli; Adaptive'de 2,8 ms | `usb-audio-yolu.md` §2/§9 · bu dosya §5.11 |
| 3 | **Transfer modu:** `Async Transfer \| Yes (device-sync)` (S3 L23) + `Feedback \| Adaptive/Synchronous` (S3 L24) | `Adaptive … Varsayılan mod` / `Async … Profesyonel cihazlar` (S2 L68–L69) + `Adaptive/Asynchronous sync` (S1 L37, L43) + `SYNC_MODE_*` üç enum (S1 L197–L201) | device-sync async ↔ "varsayılan Adaptive" ↔ firmware'de üç mod tanımlı, seçim yok | Clock recovery davranışı, jitter, latency | ⚠️ VERIFICATION REQUIRED — hangi modda çalışıldığı hiçbir kaynakta kesin değil | `usb-audio-yolu.md` §2 · bu dosya §4.2/§5.3 |
| 4 | **Kanal/bit:** `Maks. Çözünürlük \| 32-bit / 768kHz PCM` + `Kanal Sayısı \| 8 stereo (16 single)` (S3 L20, L22; S4 L22, L24) | Firmware descriptor: `16/24/32-bit, 44.1/48/96/192 kHz` (S1 L109–L112) · `Sample Rate Support … 44.1k/48k/96k/192k` (S1 L505) · `CH_2=1 # Stereo` (S1 L450) | 768 kHz ↔ 192 kHz (firmware) ↔ 384k (S2 L104) · 16 single ↔ 32 kanal (S2 L106, L238) ↔ 2 kanal (S1 L450) | Maks. çözünürlük/kanal iddiası | ⚠️ VERIFICATION REQUIRED — firmware kaynağı 192 kHz üstünü desteklemiyor | `usb-audio-yolu.md` §5 · bu dosya §3.3/§5.5 |
| 5 | **OS/sürücü:** `Driver \| Class-compliant (no driver needed)` + `OS Desteği \| Windows 10+, macOS 10.9+, Linux 4.x+` (S3 L26–L27) + Driver Status tablosu `✅ Native` ×5 OS (S3 L152–L158) | S2: `USB Audio Class 2.0 Driver` + `xHCI/EHCI` katmanı (S2 L22–L31), `class USBAudioDriver` (S2 L179), bağımlılık `libusb` (S2 L244) | "driver gerekmez" ↔ kendi sürücü sınıfı + libusb katmanı | Kurulum/Yetki (Linux'ta libusb erişimi), ürün vaadi | ⚠️ VERIFICATION REQUIRED — class-compliant OS yolu mu, K2 uygulama katmanı mı ayrımı kaynaklarda yazılmıyor | `usb-audio-yolu.md` §7 · bu dosya §5.10 |
| 6 | **Descriptor hiyerarşisi:** `usb-audio-yolu.md` §3 (S3 L32–L60) | Firmware descriptor (S1 L93–L127) | 7 fark / 2 örtüşme — Clock Source, Mic terminalleri, Format Type III (DSD), Alt Settings, Interface 3 MIDI, Product adı, Serial (§3.4 tablosu) | Descriptor doğrulama, enumeration | ⚠️ VERIFICATION REQUIRED — birebir uyuşmuyor | `usb-audio-yolu.md` §3 · bu dosya §3.3 |
| 7 | **Frame süresi:** `Frame Size \| 1ms (USB 2.0)` (S3 L83) + `Fixed timing (1ms frames for USB 2.0)` (S3 L76) | `125μs frame rate` (S1 L23, L133, L145) + `USB Frame (1ms @ Full Speed, 125μs @ High Speed)` (S2 L52) | 1 ms ↔ 125 μs (High-Speed mikro-frame) | Paket başına örnek sayısı, latency hesabı | ⚠️ VERIFICATION REQUIRED — S3 High-Speed (480 Mbps) iddiasıyla 1 ms çerçevesi çelişkili; S2 iki değeri de doğru şartlandırıyor | `usb-audio-yolu.md` §4 · bu dosya §5.2 |
| 8 | **Packet boyutu:** `Packet Size \| 44.1 samples @ 44.1kHz` (S3 L84) · `Max Packet Size \| 1024 bytes` (S3 L85) | `48 samples @ 48kHz … = 192 bytes` (S1 L136–L137) | 44,1 örnek ↔ 48 örnek ↔ 256 B (S2 L57) | Iso paket planlaması | ⚠️ VERIFICATION REQUIRED — üç farklı örnek/paket tanımı | `usb-audio-yolu.md` §4 · bu dosya §4.1/§5.2 |
| 9 | **DSD:** `DSD Desteği \| DSD64/128/256 (DoP)` + `DSD: DoP64/128/256 destekleniyor` (S3 L21, L188) | Firmware descriptor'da yalnız `Format Type I (PCM)` (S1 L109, L119); `Format Type III (DSD)` YOK; firmware faz tablosunda DSD yok (S1 L494–L505) | DoP iddiası ↔ descriptor'da DSD düğümü yok | DSD/DoP desteği beyanı | ⚠️ VERIFICATION REQUIRED — DSD descriptor'sı firmware kaynakında mevcut değil | `usb-audio-yolu.md` §6 · bu dosya §3.3 |
| 10 | **Ring buffer boyutu:** `RING_BUFFER_SIZE 4096` (S1 L272) | `Buffer Boyutu Hedef 64-256 / Gerçek 128` (S2 L236) | 4096 ↔ 128 (farklı katman: ring vs period — kaynaklarda tanımsız) | Buffer latency payı (%10, 50 μs) | ⚠️ VERIFICATION REQUIRED — iki boyutun karşılıklığı kaynakta yazılmıyor | bu dosya §4.3/§5.11 |
| 11 | **Uygulama durumu:** `Durum: 🟢 Hazır` + `Enumeration: Windows/macOS/Linux test edildi` (S3 L183, L187) | Firmware: tüm modüller `Planlandı` (S1 L496–L505) · Sürücü: 4 faz, `Tahmini Süre: 3 hafta` (S2 L248–L254) | Hazır ↔ Planlandı/3 hafta | Proje durumu, test geçmişi | ⚠️ VERIFICATION REQUIRED — iki katman farklı olgunlukta beyan ediliyor | bu dosya §2 · `usb-audio-yolu.md` §8 |
| 12 | **USB stack adı:** `lib_usb \| >= 3.x` (S1 L489) | `libusb \| Sistem kütüphanesi` (S2 L244) | `lib_usb` ↔ `libusb` (farklı paket adları) | Bağımlılık envanteri | ⚠️ VERIFICATION REQUIRED — hangi paket kullanılıyor | bu dosya §6.1/§6.2 |
| 13 | **OS listesi iç tutarlılığı:** `OS Desteği \| Windows 10+, macOS 10.9+, Linux 4.x+` (S3 L26) | Driver Status: `Android 5.0+ ✅ Native` · `iOS 11.0+ ✅ Native` (S3 L157–L158) | Özet satırda Android/iOS yok, tabloda var | OS destek beyanı | ⚠️ VERIFICATION REQUIRED — kaynak-içi eksik listeleme | `usb-audio-yolu.md` §7 |
| 14 | **Latency kırılımı toplamı:** satırlar 545 μs / %109 (S1 L416–L424) | `TOTAL < 500 … 100%` (S1 L426) | 545 ↔ <500 μs · %109 ↔ %100 | Latency bütçesi geçerliliği | ⚠️ VERIFICATION REQUIRED — firmware kaynak-içi aritmetik tutarsız | bu dosya §4.6 |

### §7.1 Uyuşan Noktalar (çelişki yok)

| # | Konu | İddia A | İddia B | Durum |
|---|------|---------|---------|-------|
| U1 | Ana USB Audio kütüphanesi | `lib_xua (XMOS open-source)` (S3 L185) | `lib_xua \| >= 4.x` (S1 L488) · `lib_xua` (S4 L96) | ✅ uyuşuyor |
| U2 | Latency hedefi | `< 1ms` (S3 L25) | `Hedef: < 1ms round-trip latency` (S1 L429) · `Latency (Async) Hedef 1ms` (S2 L234) | ✅ hedef uyuşuyor (gerçek değer için §7-2) |
| U3 | Manufacturer string | `"COREMUSIC"` (S3 L58) | `"COREMUSIC"` (S1 L123) | ✅ uyuşuyor |
| U4 | Interface 0/1/2 sırası | AC → AS OUT → AS IN (S3 L36, L42, L49) | AC → AS OUT → AS IN (S1 L96, L105, L115) | ✅ uyuşuyor |
| U5 | Çip | `XMOS XU316` (S3 L164) | `XMOS XU316` (S4 L8–L27) · `XMOS XU316 üzerinde` (S1 L12) | ✅ uyuşuyor |
| U6 | Iso gerekçesi | `Reserved bandwidth` + `No retry` (S3 L74–L75) | `Garantili bandwidth` + `Hata düzeltme yok` (S2 L46–L47) | ✅ uyuşuyor |
| U7 | Format yelpazesi (bit) | 16/24/32-bit (S3 L44, L94–L101) | `PCM \| 16, 24, 32` (S2 L104) · `16/24/32-bit PCM` (S1 L38, L44) | ✅ uyuşuyor |
| U8 | Async modun avantajı | `Async Transfer: Yes` (S3 L23) | `Daha düşük jitter / Daha iyi clock recovery` (S2 L72–L73) | ✅ uyumlu |

## §8 Doğrulama & Kanıt

### §8.1 Kaynakta Tanımlı Derleme / Doğrulama / Test Prosedürleri

**USB Audio Firmware Derleme** (S1 L432–L451):

```bash
cd firmware/xmos/app_usb_audio_skc

# USB Audio firmware derleme
xmake clean
xmake all CONFIG=usb_audio_20

# Sample rate desteği seçimi
xmake all CONFIG=usb_audio_20 SRC_441=1 SRC_48=1 SRC_96=1 SRC_192=1

# Bit depth seçimi
xmake all CONFIG=usb_audio_20 BIT_16=1 BIT_24=1 BIT_32=1

# Channel sayısı
xmake all CONFIG=usb_audio_20 CH_2=1  # Stereo
```

> Kaynak-kanıt notu: `SRC_441/48/96/192` ve `CH_2=1` bayrakları firmware'in **192 kHz / stereo** sınırını doğrular (§7-4).

**USB Descriptor Doğrulama** (S1 L453–L465):

```bash
# USB descriptor doğrulama
# Wireshark ile USB trafiği analizi
wireshark -i usbmon0 -f "usb.transfer_type == 0x01"

# USB descriptor dump
lsusb -v -d 1209:xxxx

# USB Audio Class uyumluluk testi
# USB-IF Audio Class test suite
```

> `lsusb -v -d 1209:xxxx` içindeki VID/PID değeri kaynakta `xxxx` olarak verilmiştir; gerçek PID **kaynakta yok** → `⚠️ VERIFICATION REQUIRED` (§12-15).

**USB Audio Test** (S1 L467–L482):

```bash
# Linux ile test
arecord -l  # Input device listeleme
aplay -l    # Output device listeleme

# Audio capture test
arecord -D hw:1,0 -f S32_LE -r 48000 -c 2 test.wav

# Audio playback test
aplay -D hw:1,0 -f S32_LE -r 48000 -c 2 test.wav

# Latency test
# loopback.c ile round-trip latency ölçümü
```

### §8.2 Bu Dosyanın Doğrulama Sonuçları (repo kökünden)

> **Kapanış notu (orkestratör, 2026-10-06):** Alt-oturumda `shell` 3× reddedildiği için betik çalıştırılamamıştı; **orkestratör repo kökünden çalıştırdı ve kapı geçti** — `verify` → `hasBom:false` · `mojibake:0` · `cjk:0` · `hasNul:false` · `lines:1638` · `scan` → `ok:true` · `dirty:0`. Elle ölçümler bununla örtüştü. Ek bulgu: §8.2 satır 3'teki desen-tanımı literal mojibake taşıyordu ve ölçümü kendisi kirletiyordu — desen metinden çıkarıldı (k058'deki aynı hata). Bu dosyadaki `⚠️ VERIFICATION REQUIRED` kapanmıştır.

| # | Kapı | Yöntem | Sonuç |
|---|---|---|---|
| 1 | `verify --file …usb-audio-firmware-surucu` | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k059-usb-audio/usb-audio-firmware-surucu.md` | ✅ **orkestratör repo kökünden çalıştırdı** — `hasBom:false` · `mojibake:0` · `cjk:0` · `hasNul:false` · `lines:1638` |
| 2 | `scan --dir .ai/architecture/k059-usb-audio` | aynı betiğin `scan` alt komutu | ✅ **orkestratör çalıştırdı** — `ok:true` · `dirty:0` · `files:[]` |
| 3 | Mojibake (elle tarama) | `grep` deseni: çift-kodlama artığı ok / BOM / trademark / replacement-char desenleri — **bu satıra literal örnek yazılmaz**, yazılırsa ölçüm kendi kendini kirletir | Dosya gövdesinde ilk ölçümde 4 gerçek eşleşme vardı; **tek kaynak bu satırın kendi desen tanımıydı**, satır temizlendi → tekrar ölçümü `mojibake:0` ✅ |
| 4 | BOM (bayt-düzey) | `verify` betiği bayt-düzey okuma yapar (`hasBom` alanı) | ✅ `hasBom:false` — ilk iki bayt `-` `---` frontmatter; kesinlik betikten |
| 5 | Satır sayısı ≥500 | dosya son satırı (`read`) | **1577 → nihai 1637 satır** (§8.2/§12 tabloları eklendikten sonra ölçüldü) ✅ |
| 6 | Placeholder kalmadı | `grep DEVAM\|DOGRULAMA-SONUC\|UYARI-SATIRLARI` | doldurmadan önce **2** (L860, L1363) → bu sürümde ikisi de dolduruldu, artık yok ✅ (kalan tek eşleşme: bu satırın kendi metni) |
| 7 | Wiki-link hedefleri (12 benzersiz) | `glob .ai/architecture/**/*.md` | **12/12 hedef diskte; kırık link 0** ✅ |
| 8 | k066–k071 varlığı (§10 iddiası) | `glob` + `get_file_info` (k066 dosyası `created 12:36`, k071 dosyası `created 12:47`) | Hedefler **şu an diskte VAR** — bu dosyanın yazım anındaki "0 dosya" ölçümü geçersizleşti; §10.1 6 satırı düzeltildi (§12-17) ⚠️ VERIFICATION REQUIRED |

### §8.3 Kanıt Bağlantısı

| İddia | Kanıt (dosya + satır) |
|-------|----------------------|
| Firmware modüllerinin tamamı `Planlandı` | S1 L496–L505 |
| Latency hedefi `< 1ms round-trip` | S1 L429 · S3 L25 · S2 L234 |
| Firmware 192 kHz ile sınırlı | S1 L109–L112 · S1 L374–L388 · S1 L444 · S1 L505 |
| Async/Adaptive/Synchronous üç mod tanımlı | S1 L197–L201 · S2 L66–L69 · S3 L23–L24 |
| Bant genişliği iddiaları birbiriyle uyuşmuyor | S3 L86–L88 · S2 L127–L130 |
| Descriptor farkları (7 madde) | S1 L93–L127 ↔ S3 L32–L60 |

## §9 Kaynak Kanıt Dizini

> Üretim notu: bu tablo **salt-okunur** birincil kaynak dosyanın satır satır indeksidir; her satır diskteki gerçek içeriğe karşılık gelir. Kaynak: `_backup/arch-2026-10-06_1057/architecture/firmware/usb-audio-firmware.md` (505 satır; boş satırlar indekslenmemiştir).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | `---` frontmatter açılışı | ✅ disk |
| L2 | metin | `title: "USB Audio Class 2.0 Firmware"` | ✅ disk |
| L3 | metin | `layer: Firmware` | ✅ disk |
| L4 | metin | `category: "Firmware"` | ✅ disk |
| L5 | metin | `date: 2026-09-20` | ✅ disk |
| L6 | metin | `---` frontmatter kapanışı | ✅ disk |
| L8 | baslik | `# USB Audio Class 2.0 Firmware` | ✅ disk |
| L10 | baslik | `## Genel Bakış` | ✅ disk |
| L12 | metin | UAC2 firmware genel bakış · jitter-free ISO · `<1ms round-trip` hedefi | ✅ disk |
| L14 | baslik | `## Firmware Mimarisi` | ✅ disk |
| L16 | kod | ASCII blok açılışı ```` ``` ```` | ✅ disk |
| L17 | kod | ASCII üst kenar | ✅ disk |
| L18 | kod | `USB AUDIO CLASS 2.0 FIRMWARE` başlığı | ✅ disk |
| L19 | kod | Ayırıcı satır | ✅ disk |
| L20 | kod | Boş iç satır (`│`) | ✅ disk |
| L21 | kod | High-Speed Controller kutu açılışı | ✅ disk |
| L22 | kod | `USB 2.0 High-Speed Controller` | ✅ disk |
| L23 | kod | `(480 Mbps, 125μs frame rate)` | ✅ disk |
| L24 | kod | Kutu kapanışı + aşağı ok | ✅ disk |
| L25 | kod | Yön oku satırı | ✅ disk |
| L26 | kod | UAC2 Stack kutu açılışı | ✅ disk |
| L27 | kod | `USB Audio Class 2.0 Stack` | ✅ disk |
| L28 | kod | EP0 kutu açılışı | ✅ disk |
| L29 | kod | `Control Endpoint (EP0)` | ✅ disk |
| L30 | kod | `- Device descriptor` | ✅ disk |
| L31 | kod | `- Audio control interface` | ✅ disk |
| L32 | kod | `- Clock source/routing` | ✅ disk |
| L33 | kod | EP0 kutu kapanışı | ✅ disk |
| L34 | kod | EP1 kutu açılışı | ✅ disk |
| L35 | kod | `Isochronous IN Endpoint (EP1)` | ✅ disk |
| L36 | kod | `- Microphone input` | ✅ disk |
| L37 | kod | `- Adaptive/Asynchronous sync` | ✅ disk |
| L38 | kod | `- 16/24/32-bit PCM` | ✅ disk |
| L39 | kod | EP1 kutu kapanışı | ✅ disk |
| L40 | kod | EP2 kutu açılışı | ✅ disk |
| L41 | kod | `Isochronous OUT Endpoint (EP2)` | ✅ disk |
| L42 | kod | `- Speaker output` | ✅ disk |
| L43 | kod | `- Adaptive/Asynchronous sync` | ✅ disk |
| L44 | kod | `- 16/24/32-bit PCM` | ✅ disk |
| L45 | kod | EP2 kutu kapanışı | ✅ disk |
| L46 | kod | EP3 kutu açılışı | ✅ disk |
| L47 | kod | `Interrupt Endpoint (EP3)` | ✅ disk |
| L48 | kod | `- Notification (sample rate change)` | ✅ disk |
| L49 | kod | `- Status updates` | ✅ disk |
| L50 | kod | EP3 kutu kapanışı | ✅ disk |
| L51 | kod | Stack kutu kapanışı | ✅ disk |
| L52 | kod | Boş iç satır (`│`) | ✅ disk |
| L53 | kod | Pipeline kutu açılışı | ✅ disk |
| L54 | kod | `Audio Streaming Pipeline` | ✅ disk |
| L55 | kod | `USB → Ring Buffer → Format Convert → DSP` | ✅ disk |
| L56 | kod | `← Ring Buffer ← Format Convert ← DSP` | ✅ disk |
| L57 | kod | Pipeline kutu kapanışı | ✅ disk |
| L58 | kod | Boş iç satır (`│`) | ✅ disk |
| L59 | kod | ASCII alt kenar | ✅ disk |
| L60 | kod | ASCII blok kapanışı | ✅ disk |
| L62 | baslik | `## Kaynak Kod Yapısı` | ✅ disk |
| L64 | kod | Ağaç blok açılışı | ✅ disk |
| L65 | kod | `usb_audio_firmware/` kök dizini | ✅ disk |
| L66 | kod | `├── src/` | ✅ disk |
| L67 | kod | `usb_audio_core.xc # Ana USB Audio işleyici` | ✅ disk |
| L68 | kod | `usb_descriptors.xc # USB tanımlayıcılar` | ✅ disk |
| L69 | kod | `usb_audio_control.xc # Control endpoint handler` | ✅ disk |
| L70 | kod | `usb_audio_streaming.xc # Isochronous transfer` | ✅ disk |
| L71 | kod | `usb_audio_clock.xc # Clock source management` | ✅ disk |
| L72 | kod | `usb_audio_format.xc # Format conversion` | ✅ disk |
| L73 | kod | `ring_buffer.xc # Audio ring buffer` | ✅ disk |
| L74 | kod | `usb_audio_main.xc # Ana program` | ✅ disk |
| L76 | kod | `├── include/` | ✅ disk |
| L77 | kod | `usb_audio_config.h # Konfigürasyon` | ✅ disk |
| L78 | kod | `usb_audio_types.h # Veri tipleri` | ✅ disk |
| L79 | kod | `usb_audio_registers.h # USB register tanımları` | ✅ disk |
| L81 | kod | `├── descriptors/` | ✅ disk |
| L82 | kod | `usb_audio_20_descriptors.h # UAC2.0 descriptors` | ✅ disk |
| L83 | kod | `usb_device_descriptor.h # Device descriptor` | ✅ disk |
| L84 | kod | `usb_config_descriptor.h # Config descriptor` | ✅ disk |
| L86 | kod | `└── Makefile` | ✅ disk |
| L87 | kod | Ağaç blok kapanışı | ✅ disk |
| L89 | baslik | `## Teknik Detaylar` | ✅ disk |
| L91 | baslik | `### USB Audio Class 2.0 Descriptor Hiyerarşisi` | ✅ disk |
| L93 | kod | Descriptor blok açılışı | ✅ disk |
| L94 | kod | `Device Descriptor (bDeviceClass=0xEF, bDeviceSubClass=0x02)` | ✅ disk |
| L95 | kod | `├── Configuration Descriptor` | ✅ disk |
| L96 | kod | `Interface 0: Audio Control` | ✅ disk |
| L97 | kod | `Header Descriptor (UAC2.0)` | ✅ disk |
| L98 | kod | `Clock Source Descriptor (Internal PLL)` | ✅ disk |
| L99 | kod | `Input Terminal (USB Streaming)` | ✅ disk |
| L100 | kod | `Output Terminal (Speaker)` | ✅ disk |
| L101 | kod | `Feature Unit (Volume, Mute)` | ✅ disk |
| L102 | kod | `Input Terminal (Microphone)` | ✅ disk |
| L103 | kod | `Output Terminal (USB Streaming)` | ✅ disk |
| L105 | kod | `Interface 1: Audio Streaming OUT` | ✅ disk |
| L106 | kod | `Alt Setting 0: Zero Bandwidth` | ✅ disk |
| L107 | kod | `Alt Setting 1: Operational` | ✅ disk |
| L108 | kod | `Class-Specific AS Descriptor` | ✅ disk |
| L109 | kod | `Format Type I (PCM)` | ✅ disk |
| L110 | kod | `- 16-bit, 44.1/48/96/192 kHz` | ✅ disk |
| L111 | kod | `- 24-bit, 44.1/48/96/192 kHz` | ✅ disk |
| L112 | kod | `- 32-bit, 44.1/48/96/192 kHz` | ✅ disk |
| L113 | kod | `Standard EP Descriptor (ISO OUT)` | ✅ disk |
| L115 | kod | `Interface 2: Audio Streaming IN` | ✅ disk |
| L116 | kod | `Alt Setting 0: Zero Bandwidth` | ✅ disk |
| L117 | kod | `Alt Setting 1: Operational` | ✅ disk |
| L118 | kod | `Class-Specific AS Descriptor` | ✅ disk |
| L119 | kod | `Format Type I (PCM)` | ✅ disk |
| L120 | kod | `Standard EP Descriptor (ISO IN)` | ✅ disk |
| L122 | kod | `└── String Descriptors` | ✅ disk |
| L123 | kod | `Manufacturer: "COREMUSIC"` | ✅ disk |
| L124 | kod | `Product: "COREMUSIC Audio Interface"` | ✅ disk |
| L125 | kod | `Serial: Unique device ID` | ✅ disk |
| L126 | kod | `Audio Control Interface Strings` | ✅ disk |
| L127 | kod | Descriptor blok kapanışı | ✅ disk |
| L129 | baslik | `### Isochronous Transfer Mekanizması` | ✅ disk |
| L131 | kod | ` ```xc ` blok açılışı | ✅ disk |
| L132 | kod | `// USB Isochronous endpoint handling` | ✅ disk |
| L133 | kod | `// Her USB frame'de (125μs @ High-Speed) sabit miktarda veri` | ✅ disk |
| L135 | kod | `// EP2 - Isochronous OUT (Speaker output)` | ✅ disk |
| L136 | kod | `// Her frame: 48 samples @ 48kHz = 1ms'de 48 sample` | ✅ disk |
| L137 | kod | `// Packet size: 48 * 4 bytes (32-bit) = 192 bytes` | ✅ disk |
| L139 | kod | `void usb_audio_out_handler(chan c_audio_out) {` | ✅ disk |
| L140 | kod | `unsigned char audio_packet[192];` | ✅ disk |
| L141 | kod | `int samples_received;` | ✅ disk |
| L143 | kod | `while (1) {` | ✅ disk |
| L144 | kod | `// USB frame başına bir kez çağrılır` | ✅ disk |
| L145 | kod | `// 125μs High-Speed frame rate` | ✅ disk |
| L146 | kod | `samples_received = usb_iso_out_receive(audio_packet, 192);` | ✅ disk |
| L148 | kod | `if (samples_received > 0) {` | ✅ disk |
| L149 | kod | `// Ring buffer'a yaz` | ✅ disk |
| L150 | kod | `for (int i = 0; i < samples_received / 4; i++) {` | ✅ disk |
| L151 | kod | `int32_t sample;` | ✅ disk |
| L152 | kod | `sample = (audio_packet[i*4] << 0) \|` | ✅ disk |
| L153 | kod | `(audio_packet[i*4+1] << 8) \|` | ✅ disk |
| L154 | kod | `(audio_packet[i*4+2] << 16) \|` | ✅ disk |
| L155 | kod | `(audio_packet[i*4+3] << 24);` | ✅ disk |
| L156 | kod | `c_audio_out <: sample;` | ✅ disk |
| L157 | kod | `}` (for kapanış) | ✅ disk |
| L158 | kod | `}` (if kapanış) | ✅ disk |
| L159 | kod | `}` (while kapanış) | ✅ disk |
| L160 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L162 | kod | `// EP1 - Isochronous IN (Microphone input)` | ✅ disk |
| L163 | kod | `// Her frame: 48 samples @ 48kHz = 1ms'de 48 sample` | ✅ disk |
| L164 | kod | `// Packet size: 48 * 4 bytes (32-bit) = 192 bytes` | ✅ disk |
| L166 | kod | `void usb_audio_in_handler(chan c_audio_in) {` | ✅ disk |
| L167 | kod | `unsigned char audio_packet[192];` | ✅ disk |
| L168 | kod | `int sample_count = 0;` | ✅ disk |
| L170 | kod | `while (1) {` | ✅ disk |
| L171 | kod | `// Ring buffer'dan oku` | ✅ disk |
| L172 | kod | `for (int i = 0; i < 48; i++) {` | ✅ disk |
| L173 | kod | `int32_t sample;` | ✅ disk |
| L174 | kod | `c_audio_in :> sample;` | ✅ disk |
| L175 | kod | `audio_packet[i*4] = sample & 0xFF;` | ✅ disk |
| L176 | kod | `audio_packet[i*4+1] = (sample >> 8) & 0xFF;` | ✅ disk |
| L177 | kod | `audio_packet[i*4+2] = (sample >> 16) & 0xFF;` | ✅ disk |
| L178 | kod | `audio_packet[i*4+3] = (sample >> 24) & 0xFF;` | ✅ disk |
| L181 | kod | `// USB frame başına gönder` | ✅ disk |
| L182 | kod | `usb_iso_in_send(audio_packet, 192);` | ✅ disk |
| L183 | kod | `}` (while kapanış) | ✅ disk |
| L184 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L185 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L187 | baslik | `### Clock Recovery & Sync` | ✅ disk |
| L189 | kod | ` ```xc ` blok açılışı | ✅ disk |
| L190 | kod | `// USB adaptive clock recovery` | ✅ disk |
| L191 | kod | `// USB SOF (Start of Frame) ile audio clock senkronizasyonu` | ✅ disk |
| L193 | kod | `// Asynchronous mode: Device's own clock, host adapts` | ✅ disk |
| L194 | kod | `// Adaptive mode: Host's clock, device adapts` | ✅ disk |
| L195 | kod | `// Synchronous mode: Shared clock` | ✅ disk |
| L197 | kod | `typedef enum {` | ✅ disk |
| L198 | kod | `SYNC_MODE_ASYNCHRONOUS,` | ✅ disk |
| L199 | kod | `SYNC_MODE_ADAPTIVE,` | ✅ disk |
| L200 | kod | `SYNC_MODE_SYNCHRONOUS` | ✅ disk |
| L201 | kod | `} sync_mode_t;` | ✅ disk |
| L203 | kod | `// Clock recovery PLL` | ✅ disk |
| L204 | kod | `// USB SOF pulse'ı ile audio MCLK senkronizasyonu` | ✅ disk |
| L205 | kod | `void clock_recovery_loop(chan c_sof, chan c_audio_clk)` | ✅ disk |
| L206 | kod | `timer t;` | ✅ disk |
| L207 | kod | `unsigned sof_timestamp;` | ✅ disk |
| L208 | kod | `unsigned last_sof = 0;` | ✅ disk |
| L209 | kod | `unsigned sof_period;` | ✅ disk |
| L210 | kod | `unsigned mclk_period;` | ✅ disk |
| L211 | kod | `int32_t error;` | ✅ disk |
| L212 | kod | `int32_t integral = 0;` | ✅ disk |
| L213 | kod | `int32_t kp = 10; // Proportional gain` | ✅ disk |
| L214 | kod | `int32_t ki = 1; // Integral gain` | ✅ disk |
| L216 | kod | `while (1) {` | ✅ disk |
| L217 | kod | `select {` | ✅ disk |
| L218 | kod | `case c_sof :> sof_timestamp:` | ✅ disk |
| L219 | kod | `// USB SOF pulse (1ms periyot)` | ✅ disk |
| L220 | kod | `if (last_sof != 0) {` | ✅ disk |
| L221 | kod | `sof_period = sof_timestamp - last_sof;` | ✅ disk |
| L223 | kod | `// Beklenen period: 1ms @ 48MHz = 48000 cycles` | ✅ disk |
| L224 | kod | `// Actual period: measured` | ✅ disk |
| L226 | kod | `// PLL error hesaplama` | ✅ disk |
| L227 | kod | `error = sof_period - EXPECTED_SOF_PERIOD;` | ✅ disk |
| L229 | kod | `// PI controller` | ✅ disk |
| L230 | kod | `integral += error;` | ✅ disk |
| L231 | kod | `int32_t adjustment = (kp * error) + (ki * integral);` | ✅ disk |
| L233 | kod | `// Audio clock PLL ayarla` | ✅ disk |
| L234 | kod | `c_audio_clk <: adjustment;` | ✅ disk |
| L235 | kod | `}` (if kapanış) | ✅ disk |
| L236 | kod | `last_sof = sof_timestamp;` | ✅ disk |
| L237 | kod | `break;` | ✅ disk |
| L239 | kod | `case c_audio_clk :> mclk_period:` | ✅ disk |
| L240 | kod | `// MCLK period ölçümü` | ✅ disk |
| L241 | kod | `break;` | ✅ disk |
| L242 | kod | `}` (select kapanış) | ✅ disk |
| L243 | kod | `}` (while kapanış) | ✅ disk |
| L244 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L246 | kod | `// Sample rate detection` | ✅ disk |
| L247 | kod | `int detect_sample_rate(unsigned sof_period) {` | ✅ disk |
| L248 | kod | `// USB SOF period'dan sample rate hesapla` | ✅ disk |
| L249 | kod | `// 1ms frame'de kaç sample sığar?` | ✅ disk |
| L251 | kod | `if (sof_period >= 44 && sof_period <= 45) {` | ✅ disk |
| L252 | kod | `return 44100;` | ✅ disk |
| L253 | kod | `} else if (sof_period >= 48 && sof_period <= 49) {` | ✅ disk |
| L254 | kod | `return 48000;` | ✅ disk |
| L255 | kod | `} else if (sof_period >= 96 && sof_period <= 97) {` | ✅ disk |
| L256 | kod | `return 96000;` | ✅ disk |
| L257 | kod | `} else if (sof_period >= 192 && sof_period <= 193) {` | ✅ disk |
| L258 | kod | `return 192000;` | ✅ disk |
| L259 | kod | `}` | ✅ disk |
| L261 | kod | `return 48000; // Default` | ✅ disk |
| L262 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L263 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L265 | baslik | `### Ring Buffer Implementasyonu` | ✅ disk |
| L267 | kod | ` ```xc ` blok açılışı | ✅ disk |
| L268 | kod | `// Lock-free ring buffer for USB audio` | ✅ disk |
| L269 | kod | `// Producer: USB endpoint handler` | ✅ disk |
| L270 | kod | `// Consumer: DSP processing chain` | ✅ disk |
| L272 | kod | `#define RING_BUFFER_SIZE 4096 // Must be power of 2` | ✅ disk |
| L273 | kod | `#define RING_BUFFER_MASK (RING_BUFFER_SIZE - 1)` | ✅ disk |
| L275 | kod | `typedef struct {` | ✅ disk |
| L276 | kod | `int32_t buffer[RING_BUFFER_SIZE];` | ✅ disk |
| L277 | kod | `volatile unsigned write_ptr;` | ✅ disk |
| L278 | kod | `volatile unsigned read_ptr;` | ✅ disk |
| L279 | kod | `} ring_buffer_t;` | ✅ disk |
| L281 | kod | `// Producer: USB endpoint'ten ses verisi yazma` | ✅ disk |
| L282 | kod | `void ring_buffer_write(ring_buffer_t *rb, int32_t sample) {` | ✅ disk |
| L283 | kod | `unsigned next_write = (rb->write_ptr + 1) & RING_BUFFER_MASK;` | ✅ disk |
| L285 | kod | `// Buffer dolu mu kontrol et` | ✅ disk |
| L286 | kod | `if (next_write == rb->read_ptr) {` | ✅ disk |
| L287 | kod | `// Buffer dolu - sample atla (drop)` | ✅ disk |
| L288 | kod | `return;` | ✅ disk |
| L289 | kod | `}` | ✅ disk |
| L291 | kod | `rb->buffer[rb->write_ptr] = sample;` | ✅ disk |
| L292 | kod | `// Memory barrier - compiler optimization'a karşı` | ✅ disk |
| L293 | kod | `__sync_synchronize();` | ✅ disk |
| L294 | kod | `rb->write_ptr = next_write;` | ✅ disk |
| L295 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L297 | kod | `// Consumer: DSP processing için ses verisi okuma` | ✅ disk |
| L298 | kod | `int ring_buffer_read(ring_buffer_t *rb, int32_t *sample) {` | ✅ disk |
| L299 | kod | `if (rb->read_ptr == rb->write_ptr) {` | ✅ disk |
| L300 | kod | `// Buffer boş` | ✅ disk |
| L301 | kod | `return 0;` | ✅ disk |
| L302 | kod | `}` | ✅ disk |
| L304 | kod | `*sample = rb->buffer[rb->read_ptr];` | ✅ disk |
| L305 | kod | `rb->read_ptr = (rb->read_ptr + 1) & RING_BUFFER_MASK;` | ✅ disk |
| L306 | kod | `return 1;` | ✅ disk |
| L307 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L309 | kod | `// Buffer durum kontrolü` | ✅ disk |
| L310 | kod | `int ring_buffer_level(ring_buffer_t *rb) {` | ✅ disk |
| L311 | kod | `int level = rb->write_ptr - rb->read_ptr;` | ✅ disk |
| L312 | kod | `if (level < 0) {` | ✅ disk |
| L313 | kod | `level += RING_BUFFER_SIZE;` | ✅ disk |
| L314 | kod | `}` | ✅ disk |
| L315 | kod | `return level;` | ✅ disk |
| L316 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L317 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L319 | baslik | `### Audio Format Conversion` | ✅ disk |
| L321 | kod | ` ```xc ` blok açılışı | ✅ disk |
| L322 | kod | `// Format dönüşüm fonksiyonları` | ✅ disk |
| L323 | kod | `// USB'den gelen veriyi DSP formatına çevirme` | ✅ disk |
| L325 | kod | `// 16-bit PCM → 32-bit float (DSP için)` | ✅ disk |
| L326 | kod | `void convert_pcm16_to_float(const int16_t *input, ...)` | ✅ disk |
| L327 | kod | `for (int i = 0; i < count; i++) {` | ✅ disk |
| L328 | kod | `output[i] = (float)input[i] / 32768.0f;` | ✅ disk |
| L329 | kod | `}` | ✅ disk |
| L330 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L332 | kod | `// 24-bit PCM → 32-bit float` | ✅ disk |
| L333 | kod | `void convert_pcm24_to_float(const uint8_t *input, ...)` | ✅ disk |
| L334 | kod | `for (int i = 0; i < count; i++) {` | ✅ disk |
| L335 | kod | `int32_t sample = (input[i*3] << 8) \|` | ✅ disk |
| L336 | kod | `(input[i*3+1] << 16) \|` | ✅ disk |
| L337 | kod | `(input[i*3+2] << 24);` | ✅ disk |
| L338 | kod | `output[i] = (float)sample / 8388608.0f;` | ✅ disk |
| L339 | kod | `}` | ✅ disk |
| L340 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L342 | kod | `// 32-bit float → 16-bit PCM` | ✅ disk |
| L343 | kod | `void convert_float_to_pcm16(const float *input, ...)` | ✅ disk |
| L344 | kod | `for (int i = 0; i < count; i++) {` | ✅ disk |
| L345 | kod | `float sample = input[i] * 32768.0f;` | ✅ disk |
| L346 | kod | `// Clipping` | ✅ disk |
| L347 | kod | `if (sample > 32767.0f) sample = 32767.0f;` | ✅ disk |
| L348 | kod | `if (sample < -32768.0f) sample = -32768.0f;` | ✅ disk |
| L349 | kod | `output[i] = (int16_t)sample;` | ✅ disk |
| L350 | kod | `}` | ✅ disk |
| L351 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L352 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L354 | baslik | `### USB Audio Control Requests` | ✅ disk |
| L356 | kod | ` ```c ` blok açılışı | ✅ disk |
| L357 | kod | `// USB Audio Class 2.0 control requests` | ✅ disk |
| L358 | kod | `// SET_CUR, GET_CUR, GET_MIN, GET_MAX, GET_RES` | ✅ disk |
| L360 | kod | `// Volume kontrolü` | ✅ disk |
| L361 | kod | `typedef struct {` | ✅ disk |
| L362 | kod | `int16_t current_volume; // dB (Q1.14 format)` | ✅ disk |
| L363 | kod | `int16_t min_volume; // -60 dB` | ✅ disk |
| L364 | kod | `int16_t max_volume; // 0 dB` | ✅ disk |
| L365 | kod | `int16_t resolution; // 1 dB steps` | ✅ disk |
| L366 | kod | `} volume_control_t;` | ✅ disk |
| L368 | kod | `// Sample rate değiştirme` | ✅ disk |
| L369 | kod | `int set_sample_rate(uint32_t sample_rate) {` | ✅ disk |
| L370 | kod | `// Clock source descriptor'a bildir` | ✅ disk |
| L371 | kod | `// PLL'yi yeniden yapılandır` | ✅ disk |
| L372 | kod | `// Ring buffer'ları sıfırla` | ✅ disk |
| L374 | kod | `switch (sample_rate) {` | ✅ disk |
| L375 | kod | `case 44100:` | ✅ disk |
| L376 | kod | `configure_pll(44100);` | ✅ disk |
| L377 | kod | `break;` | ✅ disk |
| L378 | kod | `case 48000:` | ✅ disk |
| L379 | kod | `configure_pll(48000);` | ✅ disk |
| L380 | kod | `break;` | ✅ disk |
| L381 | kod | `case 96000:` | ✅ disk |
| L382 | kod | `configure_pll(96000);` | ✅ disk |
| L383 | kod | `break;` | ✅ disk |
| L384 | kod | `case 192000:` | ✅ disk |
| L385 | kod | `configure_pll(192000);` | ✅ disk |
| L386 | kod | `break;` | ✅ disk |
| L387 | kod | `default:` | ✅ disk |
| L388 | kod | `return -1; // Desteklenmeyen sample rate` | ✅ disk |
| L389 | kod | `}` (switch kapanış) | ✅ disk |
| L391 | kod | `// USB interrupt endpoint ile host'a bildir` | ✅ disk |
| L392 | kod | `notify_sample_rate_change(sample_rate);` | ✅ disk |
| L393 | kod | `return 0;` | ✅ disk |
| L394 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L396 | kod | `// Mute kontrolü` | ✅ disk |
| L397 | kod | `void set_mute(uint8_t channel, uint8_t mute_state) {` | ✅ disk |
| L398 | kod | `if (channel == 0) {` | ✅ disk |
| L399 | kod | `// Master mute` | ✅ disk |
| L400 | kod | `master_mute = mute_state;` | ✅ disk |
| L401 | kod | `} else {` | ✅ disk |
| L402 | kod | `// Channel-specific mute` | ✅ disk |
| L403 | kod | `channel_mute[channel - 1] = mute_state;` | ✅ disk |
| L404 | kod | `}` | ✅ disk |
| L405 | kod | `}` (fonksiyon kapanış) | ✅ disk |
| L406 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L408 | baslik | `### Latency Optimization` | ✅ disk |
| L410 | kod | ASCII tablo blok açılışı | ✅ disk |
| L411 | kod | `USB Audio Round-Trip Latency Breakdown:` | ✅ disk |
| L413 | kod | Tablo üst kenarı | ✅ disk |
| L414 | kod | Başlık: `Component / Latency (μs) / % of Total` | ✅ disk |
| L415 | kod | Ayırıcı satır | ✅ disk |
| L416 | kod | `USB Frame Processing 125 25%` | ✅ disk |
| L417 | kod | `Ring Buffer 50 10%` | ✅ disk |
| L418 | kod | `Format Conversion 20 4%` | ✅ disk |
| L419 | kod | `DSP Processing 100 20%` | ✅ disk |
| L420 | kod | `I2S TX 50 10%` | ✅ disk |
| L421 | kod | `DAC Processing 50 10%` | ✅ disk |
| L422 | kod | `ADC Processing 50 10%` | ✅ disk |
| L423 | kod | `I2S RX 50 10%` | ✅ disk |
| L424 | kod | `USB IN Transfer 50 10%` | ✅ disk |
| L425 | kod | Ayırıcı satır | ✅ disk |
| L426 | kod | `TOTAL < 500 100%` (satırlar toplamı 545 μs / %109) | ✅ disk |
| L427 | kod | Tablo alt kenarı | ✅ disk |
| L429 | kod | `Hedef: < 1ms round-trip latency` | ✅ disk |
| L430 | kod | ASCII tablo kapanışı | ✅ disk |
| L432 | baslik | `## Derleme & Yükleme` | ✅ disk |
| L434 | baslik | `### USB Audio Firmware Derleme` | ✅ disk |
| L436 | kod | ` ```bash ` blok açılışı | ✅ disk |
| L437 | kod | `cd firmware/xmos/app_usb_audio_skc` | ✅ disk |
| L439 | kod | `# USB Audio firmware derleme` | ✅ disk |
| L440 | kod | `xmake clean` | ✅ disk |
| L441 | kod | `xmake all CONFIG=usb_audio_20` | ✅ disk |
| L443 | kod | `# Sample rate desteği seçimi` | ✅ disk |
| L444 | kod | `xmake all CONFIG=usb_audio_20 SRC_441=1 SRC_48=1 SRC_96=1 SRC_192=1` | ✅ disk |
| L446 | kod | `# Bit depth seçimi` | ✅ disk |
| L447 | kod | `xmake all CONFIG=usb_audio_20 BIT_16=1 BIT_24=1 BIT_32=1` | ✅ disk |
| L449 | kod | `# Channel sayısı` | ✅ disk |
| L450 | kod | `xmake all CONFIG=usb_audio_20 CH_2=1  # Stereo` | ✅ disk |
| L451 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L453 | baslik | `### USB Descriptor Doğrulama` | ✅ disk |
| L455 | kod | ` ```bash ` blok açılışı | ✅ disk |
| L456 | kod | `# USB descriptor doğrulama` | ✅ disk |
| L457 | kod | `# Wireshark ile USB trafiği analizi` | ✅ disk |
| L458 | kod | `wireshark -i usbmon0 -f "usb.transfer_type == 0x01"` | ✅ disk |
| L460 | kod | `# USB descriptor dump` | ✅ disk |
| L461 | kod | `lsusb -v -d 1209:xxxx` (VID 1209, PID `xxxx` belirsiz) | ✅ disk |
| L463 | kod | `# USB Audio Class uyumluluk testi` | ✅ disk |
| L464 | kod | `# USB-IF Audio Class test suite` | ✅ disk |
| L465 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L467 | baslik | `### USB Audio Test` | ✅ disk |
| L469 | kod | ` ```bash ` blok açılışı | ✅ disk |
| L470 | kod | `# Linux ile test` | ✅ disk |
| L471 | kod | `arecord -l  # Input device listeleme` | ✅ disk |
| L472 | kod | `aplay -l    # Output device listeleme` | ✅ disk |
| L474 | kod | `# Audio capture test` | ✅ disk |
| L475 | kod | `arecord -D hw:1,0 -f S32_LE -r 48000 -c 2 test.wav` | ✅ disk |
| L477 | kod | `# Audio playback test` | ✅ disk |
| L478 | kod | `aplay -D hw:1,0 -f S32_LE -r 48000 -c 2 test.wav` | ✅ disk |
| L480 | kod | `# Latency test` | ✅ disk |
| L481 | kod | `# loopback.c ile round-trip latency ölçümü` | ✅ disk |
| L482 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L484 | baslik | `## Bağımlılıklar` | ✅ disk |
| L486 | tablo | `| Bağımlılık | Versiyon | Amaç |` | ✅ disk |
| L487 | tablo | `|-----------|----------|------|` | ✅ disk |
| L488 | tablo | `lib_xua | >= 4.x | USB Audio Class kütüphanesi` | ✅ disk |
| L489 | tablo | `lib_usb | >= 3.x | USB stack` | ✅ disk |
| L490 | tablo | `lib_locks | >= 1.x | Channel synchronization` | ✅ disk |
| L491 | tablo | `lib_xcore_math | >= 1.x | Math operations` | ✅ disk |
| L492 | tablo | `lib_logging | >= 1.x | Debug logging` | ✅ disk |
| L494 | baslik | `## Durum: Implementasyon` | ✅ disk |
| L496 | tablo | `| Modül | Durum | Açıklama |` | ✅ disk |
| L497 | tablo | `|-------|-------|----------|` | ✅ disk |
| L498 | tablo | `USB Audio Core | Planlandı | Ana USB Audio handler` | ✅ disk |
| L499 | tablo | `USB Descriptors | Planlandı | UAC2.0 descriptors` | ✅ disk |
| L500 | tablo | `Isochronous Transfer | Planlandı | ISO IN/OUT endpoints` | ✅ disk |
| L501 | tablo | `Clock Recovery | Planlandı | USB SOF sync` | ✅ disk |
| L502 | tablo | `Ring Buffer | Planlandı | Lock-free audio buffer` | ✅ disk |
| L503 | tablo | `Format Conversion | Planlandı | PCM ↔ Float conversion` | ✅ disk |
| L504 | tablo | `Audio Control | Planlandı | Volume, mute, routing` | ✅ disk |
| L505 | tablo | `Sample Rate Support | Planlandı | 44.1k/48k/96k/192k` | ✅ disk |

## §10 Bağımlılık Matrisi (D01 — k054…k071)

> D01 aralığının tamamı. **Wiki-link yazımı kuralı:** hedef `.ai/architecture/` altında **gerçek dosya olarak glob ile doğrulanmış** linklere `[[…]]` verilir; diskte olmayan klasörlere link yazılmaz (kırık link yasak) ve durum `⚠️ VERIFICATION REQUIRED` ile işaretlenir (ilk ölçüm: 2026-10-06 yazım anı · taze ölçüm: 2026-10-06 12:53 — `glob .ai/architecture/**/*.md`; k066–k071 bu aralıkta diskte belirdi, bkz. §8.2-8).

| Klasör | Wiki-link | İlişki (bu dosyaya) | Link durumu |
|---|---|---|---|
| `k054-dac-adc-zinciri` (DAC-ADC donusum zinciri) | [[../k054-dac-adc-zinciri/zincir-mimari]] | k055/k056 icin ust kapsayici; USB → I2S → DAC/ADC zincirinin üstü | ✅ hedef diskte |
| `k055-ak4458-dac` (AK4458 ana DAC) | [[../k055-ak4458-dac/ak4458-dac-rehberi]] | firmware DSP çıkışının gittiği DAC uç noktası | ✅ hedef diskte |
| `k056-pcm3168a-dac-adc` (PCM3168A DAC+ADC) | [[../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi]] | firmware ISOUT/ISIN çiftinin karşılığı (DAC çıkışı + ADC geri dönüşü) | ✅ hedef diskte |
| `k057-i2s-interface` (I2S / TDM haberlesme) | [[../k057-i2s-interface/i2s-ve-tdm-rehberi]] | Ring buffer → Format Convert → DSP sonrası I2S TX/RX (S1 latency satırları) | ✅ hedef diskte |
| `k058-xmos-xu316` (XMOS xu316 USB ses kokteyi) | [[../k058-xmos-xu316/xu316-entegrasyon]] | Firmware'in çalıştığı çip (S1 L12) | ✅ hedef diskte |
| `k059-usb-audio` (USB Audio Class yolu) | [[../k059-usb-audio/usb-audio-yolu]] | Kardeş dosya — UAC2 yolu/descriptor (§7 çelişkilerinin muhatabı) | ✅ hedef diskte |
| `k060-analog-sinyal-yolu` (Analog sinyal yolu) | [[../k060-analog-sinyal-yolu/analog-yol-rehberi]] | DAC/ADC sonrası analog yol | ✅ hedef diskte |
| `k061-diff-pair-input` (Differential pair giris) | [[../k061-diff-pair-input/diff-pair-tasarim]] | USB D± differential çifti (S3 L138–L139) | ✅ hedef diskte |
| `k062-vas-stage` (VAS) | [[../k062-vas-stage/vas-stage-tasarim]] | Analog zincir devamı (bu dosyanın doğrudan konusu değil) | ✅ hedef diskte |
| `k063-output-stage` (Çıkış asaması) | [[../k063-output-stage/output-stage-tasarim]] | Analog zincir devamı | ✅ hedef diskte |
| `k064-feedback-network` (Geri besleme agi) | [[../k064-feedback-network/feedback-tasarim]] | Analog zincir devamı | ✅ hedef diskte |
| `k065-mjle21194-93` (Güç transistörleri) | [[../k065-mjle21194-93/mjle-op-amp-kurulum]] | Analog zincir devamı | ✅ hedef diskte |
| `k066-konnektorler` (Konnektor envanteri) | link YAZILMADI | USB-C konektör envanteri (S3 L129–L148) | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 2026-10-06 12:53 yeniden ölçümünde hedef **VAR** (`konnektor-envanteri.md`); link bu sürümde yazılmadı |
| `k067-koruma-devreleri` (Koruma devreleri) | link YAZILMADI | USB ESD/koruma tarafı (S3 L144–L147) | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 12:53 yeniden ölçümünde hedef **VAR** (`koruma-rehberi.md`); link bu sürümde yazılmadı |
| `k068-guc-kaynagi-analog` (Analog guc kaynagi) | link YAZILMADI | Bus-powered 5V/500mA bütçesi (S3 L190) | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 12:53 yeniden ölçümünde hedef **VAR** (`analog-besleme.md`); link bu sürümde yazılmadı |
| `k069-hoparlor-dizilimi` (Hoparlor dizilimi) | link YAZILMADI | Çıkış yükü (bu dosyanın konusu değil) | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 12:53 yeniden ölçümünde hedef **VAR** (`hoparlor-dizilim.md`); link bu sürümde yazılmadı |
| `k070-pcb-tasarim` (PCB tasarimi) | link YAZILMADI | USB 90Ω differential + I2S 50Ω taşıyıcısı | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 12:53 yeniden ölçümünde hedef **VAR** (`pcb-rehber.md`); link bu sürümde yazılmadı |
| `k071-termal-yonetim` (Termal yonetim) | link YAZILMADI | XU316 < 1W ısıl yükü (S4 L25) | ⚠️ VERIFICATION REQUIRED — yazım anında `glob: 0 dosya`; 12:53 yeniden ölçümünde hedef **VAR** (`termal-rehber.md`, `created 12:47`); link bu sürümde yazılmadı |

### §10.1 Wiki-Link Envanteri

> Bu dosyadaki tüm `[[…]]` hedefleri ve disk doğrulaması. **Benzersiz `[[…]]` hedefi: 12** (18 kullanım). Kırık link: **0**; link yazmayan **6** satır §10 tablosunda `link YAZILMADI` olarak işaretlidir. Satır 2'deki `index` hedefi dosyada `[[…]]` olarak değil, **düz yol** olarak geçer (L42).

| # | Hedef (`[[…]]` içeriği) | Doğrulanan dosya | Durum |
|---|---|---|---|
| 1 | `../k059-usb-audio/usb-audio-yolu` | `.ai/architecture/k059-usb-audio/usb-audio-yolu.md` | ✅ var |
| 2 | `../k059-usb-audio/index` — `[[…]]` linki dosyada YOK, L42'de düz yol | `.ai/architecture/k059-usb-audio/index.md` | ✅ hedef var (link yok → kırık link 0) |
| 3 | `../k057-i2s-interface/i2s-ve-tdm-rehberi` | `.ai/architecture/k057-i2s-interface/i2s-ve-tdm-rehberi.md` | ✅ var |
| 4 | `../k058-xmos-xu316/xu316-entegrasyon` | `.ai/architecture/k058-xmos-xu316/xu316-entegrasyon.md` | ✅ var |
| 5 | `../k055-ak4458-dac/ak4458-dac-rehberi` | `.ai/architecture/k055-ak4458-dac/ak4458-dac-rehberi.md` | ✅ var |
| 6 | `../k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi` | `.ai/architecture/k056-pcm3168a-dac-adc/pcm3168a-dac-adc-rehberi.md` | ✅ var |
| 7 | `../k054-dac-adc-zinciri/zincir-mimari` | `.ai/architecture/k054-dac-adc-zinciri/zincir-mimari.md` | ✅ var |
| 8 | `../k060-analog-sinyal-yolu/analog-yol-rehberi` | `.ai/architecture/k060-analog-sinyal-yolu/analog-yol-rehberi.md` | ✅ var |
| 9 | `../k061-diff-pair-input/diff-pair-tasarim` | `.ai/architecture/k061-diff-pair-input/diff-pair-tasarim.md` | ✅ var |
| 10 | `../k062-vas-stage/vas-stage-tasarim` | `.ai/architecture/k062-vas-stage/vas-stage-tasarim.md` | ✅ var |
| 11 | `../k063-output-stage/output-stage-tasarim` | `.ai/architecture/k063-output-stage/output-stage-tasarim.md` | ✅ var |
| 12 | `../k064-feedback-network/feedback-tasarim` | `.ai/architecture/k064-feedback-network/feedback-tasarim.md` | ✅ var |
| 13 | `../k065-mjle21194-93/mjle-op-amp-kurulum` | `.ai/architecture/k065-mjle21194-93/mjle-op-amp-kurulum.md` | ✅ var |
| 14 | `../k066-konnektorler/*` … `../k071-termal-yonetim/*` | hedefler 12:53 ölçümünde diskte (`konnektor-envanteri.md` · `koruma-rehberi.md` · `analog-besleme.md` · `hoparlor-dizilim.md` · `pcb-rehber.md` · `termal-rehber.md`) | ⚠️ yazım anında YOKTU → link yazılmadı; taze ölçümde VAR (§10, §8.2-8) |

## §11 Doğrulama Protokolü

| # | Adım | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı (≥500 kapısı) | `[System.IO.File]::ReadAllLines('<bu dosya>').Length` | ≥500 (boş satır dahil) |
| 2 | UTF-8 / mojibake | `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k059-usb-audio/usb-audio-firmware-surucu` | `mojibake: 0`, `hasBom: false` |
| 3 | Klasör taraması | `node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/architecture/k059-usb-audio` | 0 bulgu |
| 4 | Wiki-link kırıklığı | `[[../k054-dac-adc-zinciri/zincir-mimari]]` hedefleri `.ai/architecture/` altında gerçek dosya mı | kırık 0 (§10.1) |
| 5 | Frontmatter | 7 zorunlu alan: title · type · category · date · updated · version · status · authority | eksik yok |
| 6 | Sürüm/tarih | `version: 4.0.0` · `updated: 2026-10-06` | birebir |
| 7 | Kaynak indeksi doğruluğu | §9 = S1 (505 satır) · §13 = S2 (254 satır) gerçek satır numaraları | uyuşmayan 0 |
| 8 | Commit kapısı | `git status --porcelain -- .ai/architecture/k059-usb-audio` | `??` (bu görevde commit ATILMAZ) |
| 9 | Kaynak dokunulmazlık | `git status --porcelain -- _backup/` | bu görevce değişiklik yok (salt-okunur) |
| 10 | REDACTED | dosyada secret/token/anahtar bulunmaması | 0 eşleşme |

## §12 Açık Kalemler (⚠️ işaretli)

> Tarama: bu dosyanın yazar gövdesinde `⚠️` içeren satır sayısı: **48** (grep ile ölçüldü, 2026-10-06; §9/§10/§13 tablo satırlarındaki tekrarlar dâhil). Aşağıdaki tablonun kendi satırları bu sayıya **dâhil değildir**.

| # | Satır | Bölüm | Ne işaretliyor |
|---|---|---|---|
| 1 | 31 | §1 | Zero-Hallucination kuralının işaret tanımı |
| 2 | 171 | §3.4 | Device Descriptor: sınıf firmware'de yazılmıyor |
| 3 | 172 | §3.4 | AC interface: firmware'de 3 fazladan düğüm |
| 4 | 173 | §3.4 | Format Type III (DSD) descriptor'ü var/yok |
| 5 | 174 | §3.4 | Alt Setting yapısı farklı gösterim |
| 6 | 176 | §3.4 | Interface 3 MIDI: firmware'de yok |
| 7 | 177 | §3.4 | String Product adı farklı |
| 8 | 178 | §3.4 | String Serial değeri farklı |
| 9 | 181 | §3.4 | Descriptor sonucu: 7 fark / 2 örtüşme |
| 10 | 325 | §4.2 | Üretimde hangi sync modunun seçildiği belirsiz |
| 11 | 498 | §4.6 | Latency kırılımı 545 μs · %109 ↔ TOTAL <500 μs · %100 |
| 12 | 564 | §5.3 | Mod seçimi: üç kaynak birbirini doğrulamıyor |
| 13 | 586 | §5.4 | UAC2 clock API'sinin kütüphanesi kaynakta yok |
| 14 | 624 | §5.7 | ~24 Mbps ↔ 49.152 Mbps ↔ 96 Mbps bant iddiaları |
| 15 | 724 | §5.11 | `<1ms` koşulsuz değil — Adaptive'de 2,8 ms |
| 16 | 763 | §6.3 | `lib_usb` ↔ `libusb` paket adı farkı |
| 17 | 767 | §7 | Çelişki tablosu biçim tanımı (bağlayıcı satır) |
| 18 | 770 | §7 | Çelişki tablosu başlık satırı (bağlayıcı satır) |
| 19 | 772 | §7 | Çelişki #1 — bant genişliği |
| 20 | 773 | §7 | Çelişki #2 — latency |
| 21 | 774 | §7 | Çelişki #3 — transfer modu |
| 22 | 775 | §7 | Çelişki #4 — kanal/bit çözünürlük |
| 23 | 776 | §7 | Çelişki #5 — OS/sürücü katmanı |
| 24 | 777 | §7 | Çelişki #6 — descriptor hiyerarşisi |
| 25 | 778 | §7 | Çelişki #7 — frame süresi (1 ms ↔ 125 μs) |
| 26 | 779 | §7 | Çelişki #8 — packet boyutu |
| 27 | 780 | §7 | Çelişki #9 — DSD/DoP descriptor'ü |
| 28 | 781 | §7 | Çelişki #10 — ring buffer boyutu |
| 29 | 782 | §7 | Çelişki #11 — uygulama durumu (Hazır ↔ Planlandı) |
| 30 | 783 | §7 | Çelişki #12 — USB stack adı |
| 31 | 784 | §7 | Çelişki #13 — OS listesi iç tutarsızlığı |
| 32 | 785 | §7 | Çelişki #14 — latency toplamı aritmetiği |
| 33 | 839 | §8.1 | VID/PID kaynakta `xxxx` — gerçek PID yok |
| 34 | 860 | §8.2 | `vault-utf8-writer.mjs` orkestratör repo kökünden çalıştırdı — `mojibake:0` · `dirty:0` |
| 35 | 864 | §8.2 | `verify --file` kapısı sonuçsuz |
| 36 | 865 | §8.2 | `scan --dir` kapısı sonuçsuz |
| 37 | 867 | §8.2 | BOM için bayt-düzey kesin ölçüm yok |
| 38 | 871 | §8.2 | k066–k071 yazım-anı ölçümü geçersizleşti |
| 39 | 1311 | §10 | Wiki-link kuralı + taze ölçüm notu |
| 40 | 1327 | §10 | `k066` hedefi yazım anında yoktu, şimdi diskte |
| 41 | 1328 | §10 | `k067` hedefi yazım anında yoktu, şimdi diskte |
| 42 | 1329 | §10 | `k068` hedefi yazım anında yoktu, şimdi diskte |
| 43 | 1330 | §10 | `k069` hedefi yazım anında yoktu, şimdi diskte |
| 44 | 1331 | §10 | `k070` hedefi yazım anında yoktu, şimdi diskte |
| 45 | 1332 | §10 | `k071` hedefi yazım anında yoktu, şimdi diskte |
| 46 | 1353 | §10.1 | Envanter 14. satır — yazım anında YOKTU |
| 47 | 1370 | §12 | Bu bölümün başlığı |
| 48 | 1372 | §12 | Sayım notu (48; tablo satırları hariç) |

## §13 Tam Kaynak Satır Dizini (ikincil kaynak)

> İkincil kaynağın satır satır indeksi — kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/usb-audio-class.md` (254 satır; boş satırlar indekslenmemiştir).

| Kaynak satır | Tür | İçerik özeti | Kanıt |
|---|---|---|---|
| L1 | metin | `---` frontmatter açılışı | ✅ disk |
| L2 | metin | `title: "USB Audio Class 2.0 Sürücüsü"` | ✅ disk |
| L3 | metin | `layer: K2` | ✅ disk |
| L4 | metin | `category: "Sürücü"` | ✅ disk |
| L5 | metin | `date: 2026-09-20` | ✅ disk |
| L6 | metin | `---` frontmatter kapanışı | ✅ disk |
| L8 | baslik | `# USB Audio Class 2.0 Sürücüsü` | ✅ disk |
| L10 | baslik | `## Genel Bakış` | ✅ disk |
| L12 | metin | UAC2 genel bakış · DAC/ADC/ampler ile doğrudan çalışma | ✅ disk |
| L14 | baslik | `## Teknik Detaylar` | ✅ disk |
| L16 | baslik | `### UAC2 Mimarisi` | ✅ disk |
| L18 | kod | ASCII blok açılışı | ✅ disk |
| L19 | kod | ASCII üst kenar | ✅ disk |
| L20 | kod | `COREMUSIC Engine (K3)` | ✅ disk |
| L21 | kod | Ayırıcı satır | ✅ disk |
| L22 | kod | `USB Audio Class 2.0 Driver` | ✅ disk |
| L23 | kod | Üç kutu açılışı (Isoch/Control/Clock) | ✅ disk |
| L24 | kod | `Isoch` · `Control` · `Clock` başlıkları | ✅ disk |
| L25 | kod | `Endpoint` · `Request` · `Source` | ✅ disk |
| L26 | kod | Kutu kapanışları | ✅ disk |
| L27 | kod | Ayırıcı satır | ✅ disk |
| L28 | kod | `USB Host Controller Driver` | ✅ disk |
| L29 | kod | İki kutu açılışı | ✅ disk |
| L30 | kod | `xHCI` · `EHCI` | ✅ disk |
| L31 | kod | Kutu kapanışları | ✅ disk |
| L32 | kod | Ayırıcı satır | ✅ disk |
| L33 | kod | `USB Hardware` | ✅ disk |
| L34 | kod | Alt kutu açılışı | ✅ disk |
| L35 | kod | `USB Port → USB Cable → Audio Device` | ✅ disk |
| L36 | kod | Alt kutu kapanışı | ✅ disk |
| L37 | kod | ASCII alt kenar | ✅ disk |
| L38 | kod | ASCII blok kapanışı | ✅ disk |
| L40 | baslik | `### Isochronous Transfer` | ✅ disk |
| L42 | metin | `USB ses, isochronous transfer modunu kullanır:` | ✅ disk |
| L44 | vurgu | `**Isochronous Transfer Özellikleri**:` | ✅ disk |
| L45 | madde | `- Zaman duyarlı (time-sensitive) veri transferi` | ✅ disk |
| L46 | madde | `- Garantili bandwidth` | ✅ disk |
| L47 | madde | `- Hata düzeltme yok (veya sınırlı)` | ✅ disk |
| L48 | madde | `- Her frame'de sabit boyut veri` | ✅ disk |
| L50 | vurgu | `**Frame Yapısı**:` | ✅ disk |
| L51 | kod | ASCII blok açılışı | ✅ disk |
| L52 | kod | `USB Frame (1ms @ Full Speed, 125μs @ High Speed)` | ✅ disk |
| L53 | kod | Çerçeve çizelgesi üst kenarı | ✅ disk |
| L54 | kod | `Frame N / Frame N+1 / Frame N+2` | ✅ disk |
| L55 | kod | Paket kutuları açılışı | ✅ disk |
| L56 | kod | `Packet` metinleri | ✅ disk |
| L57 | kod | `Packet (256B)` ×3 | ✅ disk |
| L58 | kod | Paket kutuları kapanışı | ✅ disk |
| L59 | kod | Çerçeve çizelgesi alt kenarı | ✅ disk |
| L60 | kod | ASCII blok kapanışı | ✅ disk |
| L62 | baslik | `### Adaptive ve Async Modlar` | ✅ disk |
| L64 | metin | `UAC2 iki zamanlama modu destekler:` | ✅ disk |
| L66 | tablo | `| Mod | Açıklama | Kullanım |` | ✅ disk |
| L67 | tablo | Ayırıcı satır | ✅ disk |
| L68 | tablo | `Adaptive | USB host saatine senkronize | Varsayılan mod` | ✅ disk |
| L69 | tablo | `Async | Device kendi saatini kullanır | Profesyonel cihazlar` | ✅ disk |
| L71 | vurgu | `**Async Mod Avantajları**:` | ✅ disk |
| L72 | madde | `- Daha düşük jitter` | ✅ disk |
| L73 | madde | `- Daha iyi clock recovery` | ✅ disk |
| L74 | madde | `- Profesyonel ses cihazları için tercih edilen` | ✅ disk |
| L76 | baslik | `### Clock Source Yönetimi` | ✅ disk |
| L78 | metin | `UAC2, birden fazla clock source destekler:` | ✅ disk |
| L80 | kod | ` ```cpp ` blok açılışı | ✅ disk |
| L81 | kod | `// Clock source listesini al` | ✅ disk |
| L82 | kod | `USB_AC2_CLOCK_SOURCE clocks[10];` | ✅ disk |
| L83 | kod | `uint32_t clockCount;` | ✅ disk |
| L84 | kod | `UAC2_GetClockSources(deviceId, clocks, &clockCount);` | ✅ disk |
| L86 | kod | `// Clock source seçimi` | ✅ disk |
| L87 | kod | `UAC2_SetClockSource(deviceId,` | ✅ disk |
| L88 | kod | `clocks[0].sourceId,` | ✅ disk |
| L89 | kod | `clocks[0].sampleRates[0]);` | ✅ disk |
| L91 | kod | `// Clock frequency sorgusu` | ✅ disk |
| L92 | kod | `double frequency;` | ✅ disk |
| L93 | kod | `UAC2_GetClockFrequency(deviceId,` | ✅ disk |
| L94 | kod | `clocks[0].sourceId,` | ✅ disk |
| L95 | kod | `&frequency);` | ✅ disk |
| L96 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L98 | baslik | `### Format Desteği` | ✅ disk |
| L100 | metin | `UAC2 aşağıdaki formatları destekler:` | ✅ disk |
| L102 | tablo | `| Format | Bit Derinliği | Örnek Hızı | Kanal |` | ✅ disk |
| L103 | tablo | Ayırıcı satır | ✅ disk |
| L104 | tablo | `PCM | 16, 24, 32 | 44.1k-384k | 1-32` | ✅ disk |
| L105 | tablo | `IEEE Float | 32 | 44.1k-192k | 1-32` | ✅ disk |
| L106 | tablo | `DSD | 1-bit | 2.8M, 5.6M, 11.2M | 1-8` | ✅ disk |
| L108 | baslik | `### Endpoint Yapılandırması` | ✅ disk |
| L110 | metin | `Her USB ses cihazı bir veya daha fazla endpoint içerir:` | ✅ disk |
| L112 | kod | ASCII ağaç açılışı | ✅ disk |
| L113 | kod | `USB Audio Device` | ✅ disk |
| L114 | kod | `├── Input Terminal (Mikrofon)` | ✅ disk |
| L115 | kod | `└── Input Endpoint (Isoch IN)` | ✅ disk |
| L116 | kod | `├── Output Terminal (Hoparlör)` | ✅ disk |
| L117 | kod | `└── Output Endpoint (Isoch OUT)` | ✅ disk |
| L118 | kod | `├── Feature Unit (Volume, Mute)` | ✅ disk |
| L119 | kod | `└── Clock Source (Internal/External)` | ✅ disk |
| L120 | kod | ASCII ağaç kapanışı | ✅ disk |
| L122 | baslik | `### Bandwidth Yönetimi` | ✅ disk |
| L124 | metin | `USB bandwidth yönetimi kritiktir:` | ✅ disk |
| L126 | kod | ASCII blok açılışı | ✅ disk |
| L127 | kod | `High Speed USB (480 Mbps)` | ✅ disk |
| L128 | kod | `Isochronous Bandwidth: 20% = 96 Mbps` | ✅ disk |
| L129 | kod | `Max Audio Bandwidth: ~24 Mbps (24-bit 192kHz 8ch)` | ✅ disk |
| L130 | kod | `Headroom: ~72 Mbps` | ✅ disk |
| L132 | kod | `Full Speed USB (12 Mbps)` | ✅ disk |
| L133 | kod | `Isochronous Bandwidth: 90% = 10.8 Mbps` | ✅ disk |
| L134 | kod | `Max Audio Bandwidth: ~1.5 Mbps (16-bit 48kHz 2ch)` | ✅ disk |
| L135 | kod | `Headroom: ~9.3 Mbps` | ✅ disk |
| L136 | kod | ASCII blok kapanışı | ✅ disk |
| L138 | baslik | `### Buffer Yönetimi` | ✅ disk |
| L140 | metin | `USB Audio buffer yönetimi:` | ✅ disk |
| L142 | kod | ` ```cpp ` blok açılışı | ✅ disk |
| L143 | kod | `// Isochronous buffer yapısı` | ✅ disk |
| L144 | kod | `struct USB_AudioBuffer {` | ✅ disk |
| L145 | kod | `void* data; // Buffer verisi` | ✅ disk |
| L146 | kod | `uint32_t size; // Buffer boyutu` | ✅ disk |
| L147 | kod | `uint32_t frameSize; // Frame boyutu` | ✅ disk |
| L148 | kod | `uint32_t numFrames; // Frame sayısı` | ✅ disk |
| L149 | kod | `bool isochronous; // Isochronous modu` | ✅ disk |
| L150 | kod | `};` | ✅ disk |
| L152 | kod | `// Double buffering` | ✅ disk |
| L153 | kod | `USB_AudioBuffer bufferA, bufferB;` | ✅ disk |
| L154 | kod | `USB_AudioBuffer* activeBuffer = &bufferA;` | ✅ disk |
| L155 | kod | `USB_AudioBuffer* backBuffer = &bufferB;` | ✅ disk |
| L157 | kod | `// Buffer değişimi (çift tamponlama)` | ✅ disk |
| L158 | kod | `void swapBuffers() {` | ✅ disk |
| L159 | kod | `USB_AudioBuffer* temp = activeBuffer;` | ✅ disk |
| L160 | kod | `activeBuffer = backBuffer;` | ✅ disk |
| L161 | kod | `backBuffer = temp;` | ✅ disk |
| L162 | kod | `}` | ✅ disk |
| L163 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L165 | baslik | `### Hata Yönetimi` | ✅ disk |
| L167 | metin | `USB Audio hata yönetimi:` | ✅ disk |
| L169 | tablo | `| Hata | Neden | Çözüm |` | ✅ disk |
| L170 | tablo | Ayırıcı satır | ✅ disk |
| L171 | tablo | `USB_ERROR_STALL | Transfer durduruldu | Endpoint'i resetle` | ✅ disk |
| L172 | tablo | `USB_ERROR_NAK | Cihaz meşgul | Yeniden dene` | ✅ disk |
| L173 | tablo | `USB_ERROR_TIMEOUT | Zaman aşımı | Bandwidth'i artır` | ✅ disk |
| L174 | tablo | `USB_ERROR_OVERFLOW | Buffer taştı | Buffer boyutunu artır` | ✅ disk |
| L176 | baslik | `## API / Arayüz` | ✅ disk |
| L178 | kod | ` ```cpp ` blok açılışı | ✅ disk |
| L179 | kod | `class USBAudioDriver {` | ✅ disk |
| L180 | kod | `public:` | ✅ disk |
| L181 | kod | `bool initialize();` | ✅ disk |
| L182 | kod | `void shutdown();` | ✅ disk |
| L184 | kod | `// Cihaz keşfi` | ✅ disk |
| L185 | kod | `std::vector<USBAudioDevice> enumerateDevices() const;` | ✅ disk |
| L186 | kod | `bool connectToDevice(uint32_t deviceId);` | ✅ disk |
| L187 | kod | `void disconnectDevice();` | ✅ disk |
| L189 | kod | `// Akış yapılandırması` | ✅ disk |
| L190 | kod | `bool setSampleRate(double rate);` | ✅ disk |
| L191 | kod | `bool setBitDepth(uint32_t bits);` | ✅ disk |
| L192 | kod | `bool setChannelCount(uint32_t channels);` | ✅ disk |
| L193 | kod | `bool setBufferSize(uint32_t frames);` | ✅ disk |
| L195 | kod | `// Transfer kontrolü` | ✅ disk |
| L196 | kod | `bool startPlayback();` | ✅ disk |
| L197 | kod | `bool stopPlayback();` | ✅ disk |
| L198 | kod | `bool startCapture();` | ✅ disk |
| L199 | kod | `bool stopCapture();` | ✅ disk |
| L201 | kod | `// Async mod` | ✅ disk |
| L202 | kod | `bool enableAsyncMode();` | ✅ disk |
| L203 | kod | `bool setClockSource(uint32_t sourceId);` | ✅ disk |
| L205 | kod | `// Buffer yönetimi` | ✅ disk |
| L206 | kod | `bool submitBuffer(const USB_AudioBuffer& buffer);` | ✅ disk |
| L207 | kod | `bool cancelPendingTransfers();` | ✅ disk |
| L209 | kod | `//_durum` | ✅ disk |
| L210 | kod | `bool isConnected() const;` | ✅ disk |
| L211 | kod | `double getCurrentSampleRate() const;` | ✅ disk |
| L212 | kod | `uint32_t getLatency() const;` | ✅ disk |
| L213 | kod | `};` | ✅ disk |
| L215 | kod | `// Kullanım örneği` | ✅ disk |
| L216 | kod | `USBAudioDriver driver;` | ✅ disk |
| L217 | kod | `driver.initialize();` | ✅ disk |
| L219 | kod | `auto devices = driver.enumerateDevices();` | ✅ disk |
| L220 | kod | `if (!devices.empty()) {` | ✅ disk |
| L221 | kod | `driver.connectToDevice(devices[0].id);` | ✅ disk |
| L222 | kod | `driver.setSampleRate(96000);` | ✅ disk |
| L223 | kod | `driver.setBitDepth(32);` | ✅ disk |
| L224 | kod | `driver.setChannelCount(2);` | ✅ disk |
| L225 | kod | `driver.enableAsyncMode();` | ✅ disk |
| L226 | kod | `driver.startPlayback();` | ✅ disk |
| L227 | kod | `}` | ✅ disk |
| L228 | kod | ` ``` ` blok kapanışı | ✅ disk |
| L230 | baslik | `## Performans Metrikleri` | ✅ disk |
| L232 | tablo | `| Metrik | Hedef | Gerçek |` | ✅ disk |
| L233 | tablo | Ayırıcı satır | ✅ disk |
| L234 | tablo | `Latency (Async) | 1ms | 0.9ms` | ✅ disk |
| L235 | tablo | `Latency (Adaptive) | 3ms | 2.8ms` | ✅ disk |
| L236 | tablo | `Buffer Boyutu | 64-256 | 128` | ✅ disk |
| L237 | tablo | `CPU (boşta) | < 1% | 0.5%` | ✅ disk |
| L238 | tablo | `Maks. Kanal | 32 | 32` | ✅ disk |
| L240 | baslik | `## Bağımlılıklar` | ✅ disk |
| L242 | tablo | `| Bağımlılık | Tür |` | ✅ disk |
| L243 | tablo | Ayırıcı satır | ✅ disk |
| L244 | tablo | `libusb | Sistem kütüphanesi` | ✅ disk |
| L245 | tablo | `USB Host Controller Driver | Çekirdek` | ✅ disk |
| L246 | tablo | `K1 USB Core | İç katman` | ✅ disk |
| L248 | baslik | `## Durum: Implementasyon` | ✅ disk |
| L250 | madde | `**Faz 1**: USB Audio Class keşfi, temel yapılandırma` | ✅ disk |
| L251 | madde | `**Faz 2**: Isochronous transfer implementasyonu` | ✅ disk |
| L252 | madde | `**Faz 3**: Async mod, clock recovery` | ✅ disk |
| L253 | madde | `**Faz 4**: DSD desteği, hata yönetimi` | ✅ disk |
| L254 | madde | `**Tahmini Süre**: 3 hafta (120 adam-saat)` | ✅ disk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
