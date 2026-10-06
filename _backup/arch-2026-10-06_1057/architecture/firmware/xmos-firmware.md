---
title: "XMOS XU316 Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# XMOS XU316 Firmware

## Genel Bakış

XMOS XU316, COREMUSIC projesinin real-time ses işleme merkezidir. 8-core mimarisi ile eşzamanlı USB, I2S ve DSP işlemlerini hardware-level parallelism ile yönetir. XC dilinde geliştirilen firmware, deterministic timing ve low-latency audio processing sağlar.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                   XMOS XU316 FIRMWARE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ Thread 0 │ │ Thread 1 │ │ Thread 2 │ │ Thread 3 │          │
│  │ USB IN   │ │ USB OUT  │ │ I2S TX   │ │ I2S RX   │          │
│  │ (Mic)    │ │ (Spkr)   │ │ (DAC)    │ │ (ADC)    │          │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘          │
│       │             │             │             │                │
│  ┌────▼─────────────▼─────────────▼─────────────▼────┐          │
│  │              Channel Communication               │          │
│  │              (Inter-thread signaling)             │          │
│  └────┬─────────────┬─────────────┬─────────────┬────┘          │
│       │             │             │             │                │
│  ┌────▼─────┐ ┌─────▼─────┐ ┌────▼─────┐ ┌────▼─────┐         │
│  │ Thread 4 │ │ Thread 5  │ │ Thread 6 │ │ Thread 7 │         │
│  │ DSP      │ │ GPIO &    │ │ SPI/I2C  │ │ System   │         │
│  │ Engine   │ │ Control   │ │ Comms    │ │ Mgmt     │         │
│  └──────────┘ └───────────┘ └──────────┘ └──────────┘         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
xmos/
├── app_usb_audio_skc/
│   ├── src/
│   │   ├── main.xc                    # Ana program girişi
│   │   ├── usb_audio.xc               # USB Audio endpointleri
│   │   ├── usb_audio_descriptors.xc   # USB tanımlayıcıları
│   │   ├── i2s_driver.xc              # I2S master/slave driver
│   │   ├── dsp_processing.xc          # DSP processing chain
│   │   ├── gpio_control.xc            # GPIO buton/LED yönetimi
│   │   ├── i2c_master.xc             # I2C master (codec config)
│   │   ├── spi_master.xc             # SPI master (MCU comms)
│   │   ├── clock_config.xc           # Clock recovery & MCLK
│   │   └── platform.xc              # Platform konfigürasyonu
│   ├── module_*.xn                    # Board tanımlama dosyaları
│   ├── app_usb_audio_skc.xn          # XU316 board config
│   └── Makefile                       # xmake build sistemi
│
├── modules/
│   ├── lib_xua/                       # USB Audio Class modülü
│   │   ├── module_xua/               # Ana modül
│   │   ├── module_xua/src/           # Kaynak dosyaları
│   │   └── module_xua/api/           # Header dosyaları
│   ├── lib_i2s/                       # I2S driver modülü
│   ├── lib_dsp/                       # DSP kütüphanesi
│   └── lib_i2c_master/               # I2C master modülü
│
└── platform/
    ├── xmos_board_support/           # Board destek paketleri
    └── xs1/                          # XS1 Architecture tanımları
```

## Teknik Detaylar

### XC Dil Özellikleri

XC, XMOS'un real-time concurrency için özel olarak tasarlanmış programlama dilidir:

```xc
// Parallel processing - 8 thread eşzamanlı çalışır
par {
    on tile[0]: usb_audio_loopback();
    on tile[0]: i2s_transmit_loopback();
    on tile[0]: dsp_processing_chain();
    on tile[0]: gpio_control_loop();
}

// Channel communication - thread'ler arası veri transferi
chan c_usb_to_i2s;   // USB'den I2S'ye ses verisi
chan c_i2s_to_usb;   // I2S'den USB'ye ses verisi
chan c_dsp_to_gpio;  // DSP'den GPIO'ya durum bilgisi
```

### XMOS XU316 Özellikleri

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Clock Speed | 500 MHz | Ana saat frekansı |
| Thread Count | 8 | Eşzamanlı iş parçacığı |
| RAM | 512 KB | On-chip SRAM |
| Flash | 16 MB | On-board SPI Flash |
| USB | 2.0 HS | High-Speed USB 2.0 |
| I2S | 16 kanal | Multi-channel I2S desteği |
| GPIO | 32 pin | Genel amaçlı giriş/çıkış |
| SPI | 2x | SPI master/slave |
| I2C | 2x | I2C master/slave |

### Thread Zamanlama

```
Thread Period:          1ms (USB frame rate)
I2S Sample Period:      20.83μs (@ 48kHz, 1024 samples)
DSP Block Size:         32 samples (0.667ms)
GPIO Scan Rate:         1ms (debounce için)
SPI Transaction:        < 100μs
I2C Transaction:        < 500μs
```

### Memory Map

```
Address Range          Size    Kullanım
0x00000000 - 0x0000FFFF  64KB   Boot ROM
0x00010000 - 0x0007FFFF  448KB  Code Region (Flash)
0x00080000 - 0x000BFFFF  256KB  Data Region (SRAM)
0x000C0000 - 0x000FFFFF  256KB  Stack & Heap
0x01000000 - 0x01FFFFFF  16MB   External Flash
```

### USB Audio Pipeline

```
USB Host ──► USB Endpoint ──► Ring Buffer ──► DSP Chain ──► I2S TX
     ▲                                                      │
     │          ┌──────────────────────────────────────────┘
     │          │
     └──────────┴── I2S RX ──► Ring Buffer ──► DSP Chain ──► USB Endpoint
```

### Clock Recovery

```xc
// XMOS clock recovery - USB SOF ile senkronizasyon
// USB Start-of-Frame (SOF) pulse'ı ile clock recovered
void clock_recovery(chan c_sof, chan c_mclk) {
    timer t;
    unsigned sof_time, mclk_time;
    unsigned period;

    while (1) {
        select {
            case c_sof :> sof_time:
                // USB SOF pulse geldi
                // MCLK period hesapla
                period = mclk_time - sof_time;
                // Audio clock PLL'yi ayarla
                adjust_audio_clock(period);
                break;

            case c_mclk :> mclk_time:
                // MCLK pulse geldi
                // SOF ile karşılaştır
                break;
        }
    }
}
```

### DSP Processing Chain

```xc
// DSP processing - real-time EQ, volume, mixing
// 32 sample block processing @ 48kHz
// Total latency: < 1ms

void dsp_process(chan c_in, chan c_out) {
    int32_t audio_buffer[32];
    int32_t output_buffer[32];

    while (1) {
        // Input'dan 32 sample oku
        c_in :> audio_buffer[0];
        // ... (32 sample transfer)

        // Processing zinciri
        high_pass_filter(audio_buffer, 80.0);    // DC removal
        parametric_eq(audio_buffer);              // 10-band EQ
        dynamics_compressor(audio_buffer);        // Compression
        volume_control(audio_buffer, master_vol); // Master volume
        limiter(audio_buffer, output_buffer);     // Peak limiting

        // Output'a gönder
        c_out <: output_buffer[0];
        // ... (32 sample transfer)
    }
}
```

### USB Audio Class 2.0 Entegrasyonu

```xc
// USB Audio Class 2.0 descriptors
// Supported formats: PCM, PCM8, IEEE_FLOAT
// Sample rates: 44.1k, 48k, 96k, 192k
// Channels: 2 (stereo)

unsigned char usb_audio_descriptors[] = {
    // USB Audio Class Interface Descriptor
    0x09,           // bLength
    0x04,           // bDescriptorType (Interface)
    0x01,           // bInterfaceNumber
    0x00,           // bAlternateSetting
    0x00,           // bNumEndpoints
    0x01,           // bInterfaceClass (Audio)
    0x02,           // bInterfaceSubClass (AudioStreaming)
    0x00,           // bInterfaceProtocol
    0x00,           // iInterface

    // Audio Streaming Interface Descriptor
    0x07,           // bLength
    0x24,           // bDescriptorType (CS_INTERFACE)
    0x01,           // bDescriptorSubtype (AS_GENERAL)
    0x01,           // bTerminalLink
    0x00,           // bDelay
    0x01, 0x00,     // wFormatTag (PCM)
};
```

### Error Handling & Recovery

```xc
// Watchdog ve hata yönetimi
// Her 100ms'de bir watchdog reset
// USB disconnect/reconnect mekanizması

void system_monitor(chan c_watchdog) {
    timer t;
    unsigned timeout;

    while (1) {
        // Watchdog timeout ayarla
        t :> timeout;
        timeout += 100000000; // 100ms @ 500MHz

        select {
            case t when timerafter(timeout) :> void:
                // Watchdog reset
                watchdog_reset();
                break;

            case c_watchdog :> int status:
                // Watchdog beat geldi
                watchdog_clear();
                break;
        }
    }
}
```

## Derleme & Yükleme

### Ortam Kurulumu

```bash
# XTC Tools kurulumu
wget https://www.xmos.com/file-tools/XTC-Tools-15.3.0
chmod +x XTC-Tools-15.3.0
./XTC-Tools-15.3.0 --prefix /opt/XMOS

# Ortam değişkenlerini ayarla
source /opt/XMOS/xtimecomposer/15.3.0/SetEnv
```

### Firmware Derleme

```bash
cd firmware/xmos/app_usb_audio_skc

# Clean build
xmake clean

# Derleme
xmake all

# Yalnızca belirli bir target
xmake --target XU316

# Release build
xmake CONFIG=release all
```

### Firmware Yükleme

```bash
# Debug yükleme
xflash --target XU316 app_usb_audio_skc.xe

# DFU modunda yükleme
xflash --target XU316 --upgrade app_usb_audio_skc.xe

# Trace ile yükleme
xrun --target XU316 app_usb_audio_skc.xe
```

### Debug & Trace

```bash
# xgdb ile debug
xgdb app_usb_audio_skc.xe

# Console output
xsim --target XU316 app_usb_audio_skc.xe

# Performance trace
xtrace --target XU316 app_usb_audio_skc.xe
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| XTC Tools | >= 15.x | Derleme toolchain |
| XC Compiler | - | XC→XS1 assembly çevirici |
| lib_xua | >= 4.x | USB Audio Class kütüphanesi |
| lib_i2s | >= 2.x | I2S driver |
| lib_dsp | >= 3.x | DSP processing |
| lib_i2c_master | >= 2.x | I2C communication |
| lib_spi_master | >= 1.x | SPI communication |
| lib_locks | >= 1.x | Mutex & channel locks |
| lib_logging | >= 1.x | Debug logging |
| lib_tile | >= 1.x | Tile management |
| platform_xmos | >= 1.x | Board support |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| XMOS XU316 Board Config | Planlandı | XU316 board tanımlama |
| USB Audio Endpoint | Planlandı | UAC2.0 sink/source |
| I2S Master Driver | Planlandı | Multi-channel I2S |
| DSP Processing Chain | Planlandı | Real-time audio processing |
| GPIO Control | Planlandı | Buton/LED management |
| I2C Communication | Planlandı | Codec configuration |
| SPI Communication | Planlandı | MCU communication |
| Clock Recovery | Planlandı | USB SOF sync |
| Watchdog | Planlandı | System health monitoring |
