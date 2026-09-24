---
title: "Firmware Katmanı - Genel Bakış"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# Firmware Katmanı

## Genel Bakış

COREMUSIC Firmware katmanı, donanım ile yazılım arasındaki köprüyü oluşturur. XMOS XU316 tabanlı real-time audio işleme, USB Audio Class 2.0 desteği ve çoklu protokol yönetimi bu katmanın temel sorumlulukları arasındadır. Firmware, deterministic timing gerektiren ses uygulamaları için optimize edilmiş low-latency bir mimari sunar.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    FIRMWARE MİMARİSİ                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────┐    ┌──────────────────┐                  │
│  │   XMOS XU316     │    │   STM32/RPi      │                  │
│  │   (Audio Core)   │    │   (Control Plane) │                  │
│  └────────┬─────────┘    └────────┬─────────┘                  │
│           │                       │                             │
│  ┌────────▼─────────┐    ┌────────▼─────────┐                  │
│  │  USB Audio       │    │  GPIO Control    │                  │
│  │  Firmware        │    │  Firmware        │                  │
│  └────────┬─────────┘    └────────┬─────────┘                  │
│           │                       │                             │
│  ┌────────▼─────────┐    ┌────────▼─────────┐                  │
│  │  I2S Driver      │    │  SPI/I2C Comms   │                  │
│  │  Firmware        │    │  Firmware        │                  │
│  └────────┬─────────┘    └────────┬─────────┘                  │
│           │                       │                             │
│  ┌────────▼─────────┐    ┌────────▼─────────┐                  │
│  │  DSP Processing  │    │  Power Mgmt      │                  │
│  │  Firmware        │    │  Firmware        │                  │
│  └──────────────────┘    └──────────────────┘                  │
│                                                                 │
│  ┌──────────────────────────────────────────────────┐          │
│  │           Bootloader & DFU System                 │          │
│  │           (Firmware Update Infrastructure)        │          │
│  └──────────────────────────────────────────────────┘          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
firmware/
├── index.md                          # Bu dosya
├── xmos-firmware.md                  # XMOS XU316 firmware
├── bootloader.md                     # Bootloader & DFU
├── usb-audio-firmware.md             # USB Audio Class 2.0
├── i2s-driver.md                     # I2S driver
├── dsp-firmware.md                   # DSP processing
├── gpio-control.md                   # GPIO kontrol
├── mcu-support.md                    # STM32/RPi desteği
│
├── xmos/                             # XMOS kaynak kodları
│   ├── app_usb_audio_skc/           # USB Audio uygulaması
│   │   ├── src/
│   │   │   ├── main.xc              # AnaXC programı
│   │   │   ├── usb_audio.xc         # USB Audio endpointleri
│   │   │   ├── i2s_driver.xc        # I2S master driver
│   │   │   ├── dsp.xc               # DSP processing chain
│   │   │   └── gpio.xc              # GPIO control
│   │   ├── module_*.xn              # XN dosyaları (board config)
│   │   └── Makefile                 # xmos构建 sistemi
│   │
│   └── modules/                     # Ortak modüller
│       ├── usb_audio/               # USB Audio Class modülü
│       ├── i2s/                     # I2S modülü
│       └── dsp/                     # DSP modülleri
│
├── stm32/                           # STM32 firmware
│   ├── Core/Inc/                    # Header dosyaları
│   ├── Core/Src/                    # Kaynak dosyaları
│   └── Makefile
│
└── rpi/                             # Raspberry Pi firmware
    ├── src/
    └── CMakeLists.txt
```

## Teknik Detaylar

### XMOS XU316 Seçim Gerekçesi

XMOS XU316, COREMUSIC projesinin real-time ses işleme gereksinimlerini karşılamak için seçilmiştir:

- **Deterministic Timing**: Hardware-level thread scheduling ile jitter-free audio processing
- **8-Core Architecture**: Eşzamanlı USB, I2S ve DSP processing için yeterli kaynak
- **XC Language**: Real-time concurrency için özel programlama dili
- **Hardware-Optimized**: Audio-specific DSP instructions ve MAC operasyonları

### Firmware Katmanları

| Katman | Sorumluluk | Latency Hedefi |
|--------|-----------|-----------------|
| USB Audio | Sınıf uyumluluğu, veri aktarımı | < 1ms |
| I2S Driver | DAC/ADC iletişimi, clock recovery | < 100μs |
| DSP | EQ, mixing, efektler | < 5ms |
| GPIO | Buton/LED kontrolü | < 10ms |
| Bootloader | Firmware güncelleme | - |
| MCU Support | Control plane iletişimi | < 50ms |

### Real-Time Gereksinimleri

```
Audio Sample Rate:     44.1 kHz / 48 kHz / 96 kHz / 192 kHz
Bit Depth:             16-bit / 24-bit / 32-bit
USB Frame Rate:        1ms (Full-Speed) / 125μs (High-Speed)
I2S BCLK:              2.822 MHz (44.1kHz) / 3.072 MHz (48kHz)
I2S LRCLK:             44.1 kHz / 48 kHz
Processing Latency:    < 3 samples (< 63μs @ 48kHz)
```

### Thread Kullanımı (XMOS)

```
Thread 0: USB Audio IN endpoint (microphone input)
Thread 1: USB Audio OUT endpoint (speaker output)
Thread 2: I2S TX (DAC veri gönderimi)
Thread 3: I2S RX (ADC veri okuma)
Thread 4: DSP Processing (EQ, mixing, volume)
Thread 5: GPIO & Control (buton, LED, encoder)
Thread 6: SPI/I2C Communication (MCU ile iletişim)
Thread 7: System Management (watchdog, diagnostics)
```

## Derleme & Yükleme

### XMOS Firmware Derleme

```bash
# XTC Tools kurulumu
source /opt/XMOS/xtimecomposer/<version>/SetEnv

# XMOS firmware derleme
cd firmware/xmos/app_usb_audio_skc
xmake clean
xmake all

# Firmware yükleme
xflash --target XU316 app_usb_audio_skc.xe
```

### STM32 Firmware Derleme

```bash
# STM32CubeIDE veya make ile derleme
cd firmware/stm32
make clean
make all

# ST-Link ile yükleme
openocd -f interface/stlink.cfg -f target/stm32f4x.cfg \
  -c "program build/coremusic_stm32.elf verify reset exit"
```

### DFU ile Güncelleme

```bash
# DFU moduna geçiş
dfu-util -l
dfu-util -a firmware -D coremusic_firmware.bin

# XMOS DFU
xflash --target XU316 -o coremusic.bin
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| XTC Tools | >= 15.x | XMOS derleme ve yükleme |
| XC Language | - | XMOS firmware programlama |
| lib_xua | >= 4.x | USB Audio Class kütüphanesi |
| lib_i2s | >= 2.x | I2S driver kütüphanesi |
| lib_dsp | >= 3.x | DSP processing kütüphanesi |
| STM32 HAL | >= 1.25.x | STM32 donanım soyutlama |
| FreeRTOS | >= 10.x | STM32 gerçek zamanlı işletim sistemi |
| CMake | >= 3.15 | RPi derleme sistemi |
| GCC ARM | >= 10.x | ARM cross-compiler |

## Durum: Implementasyon

| Dosya | Durum | Açıklama |
|-------|-------|----------|
| xmos-firmware.md | Planlandı | XMOS XU316 firmware detayları |
| bootloader.md | Planlandı | Bootloader & DFU sistemi |
| usb-audio-firmware.md | Planlandı | USB Audio Class 2.0 |
| i2s-driver.md | Planlandı | I2S driver detayları |
| dsp-firmware.md | Planlandı | DSP processing zinciri |
| gpio-control.md | Planlandı | GPIO kontrol sistemi |
| mcu-support.md | Planlandı | STM32/RPi desteği |
