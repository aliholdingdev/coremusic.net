---
title: "USB Audio Class 2.0 Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# USB Audio Class 2.0 Firmware

## Genel Bakış

USB Audio Class 2.0 (UAC2) firmware, COREMUSIC'ın USB üzerinden yüksek kaliteli ses传输ını yönetir. Isochronous transfer modu ile jitter-free audio streaming, adaptive USB frame senkronizasyonu ve çoklu format desteği sağlar. XMOS XU316 üzerinde optimized edilmiş USB Audio endpoint handling ile <1ms round-trip latency hedeflenmektedir.

## Firmware Mimarisi

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

## Kaynak Kod Yapısı

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

## Teknik Detaylar

### USB Audio Class 2.0 Descriptor Hiyerarşisi

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

### Isochronous Transfer Mekanizması

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

### Clock Recovery & Sync

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

### Ring Buffer Implementasyonu

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

### Audio Format Conversion

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

### USB Audio Control Requests

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

### Latency Optimization

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

## Derleme & Yükleme

### USB Audio Firmware Derleme

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

### USB Descriptor Doğrulama

```bash
# USB descriptor doğrulama
# Wireshark ile USB trafiği analizi
wireshark -i usbmon0 -f "usb.transfer_type == 0x01"

# USB descriptor dump
lsusb -v -d 1209:xxxx

# USB Audio Class uyumluluk testi
# USB-IF Audio Class test suite
```

### USB Audio Test

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

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_xua | >= 4.x | USB Audio Class kütüphanesi |
| lib_usb | >= 3.x | USB stack |
| lib_locks | >= 1.x | Channel synchronization |
| lib_xcore_math | >= 1.x | Math operations |
| lib_logging | >= 1.x | Debug logging |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| USB Audio Core | Planlandı | Ana USB Audio handler |
| USB Descriptors | Planlandı | UAC2.0 descriptors |
| Isochronous Transfer | Planlandı | ISO IN/OUT endpoints |
| Clock Recovery | Planlandı | USB SOF sync |
| Ring Buffer | Planlandı | Lock-free audio buffer |
| Format Conversion | Planlandı | PCM ↔ Float conversion |
| Audio Control | Planlandı | Volume, mute, routing |
| Sample Rate Support | Planlandı | 44.1k/48k/96k/192k |
