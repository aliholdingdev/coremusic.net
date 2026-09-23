---
title: "USB Audio Class 2.0"
layer: K1
category: "Dijital Ses Arabirimi"
date: 2026-09-20
---

# USB Audio Class 2.0

## Genel Bakış

USB Audio Class 2.0 (UAC2), COREMUSIC'ın USB üzerinden yüksek çözünürlüklü ses alması için kullanılan endüstri standartıdır. XMOS XU316 UAC2 firmware'i çalıştırarak Windows, macOS ve Linux'ta driverless çalışır. Isochronous transfer modu ile zamanlama hassasiyeti sağlar.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64/128/256 (DoP) |
| Kanal Sayısı | 8 stereo (16 single) |
| Async Transfer | Yes (device-sync) |
| Feedback | Adaptive/Synchronous |
| Latency | < 1ms |
| OS Desteği | Windows 10+, macOS 10.9+, Linux 4.x+ |
| Driver | Class-compliant (no driver needed) |

## USB Descriptor Hierarchy

```
USB Device Descriptor
     │
     ├─ Configuration Descriptor
     │   │
     │   ├─ Interface 0 (Audio Control)
     │   │   ├─ AC Header Descriptor
     │   │   ├─ Input Terminal (USB Streaming)
     │   │   ├─ Output Terminal (Speaker)
     │   │   └─ Feature Unit (Volume/Mute)
     │   │
     │   ├─ Interface 1 (Audio Streaming OUT)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   ├─ Format Type III (DSD)
     │   │   └─ Standard Endpoint (Iso OUT)
     │   │       └─ Isochronous Endpoint
     │   │
     │   ├─ Interface 2 (Audio Streaming IN)
     │   │   ├─ AS General Descriptor
     │   │   ├─ Format Type I (PCM)
     │   │   └─ Standard Endpoint (Iso IN)
     │   │       └─ Isochronous Endpoint
     │   │
     │   └─ Interface 3 (MIDI Streaming - Optional)
     │
     └─ String Descriptors
         ├─ Manufacturer: "COREMUSIC"
         ├─ Product: "COREMUSIC USB DAC"
         └─ Serial: "CM-001"
```

## Isochronous Transfer

### Neden Isochronous?

```
Audio data requires:
1. Constant data rate (no buffering variation)
2. Time-guaranteed delivery (no retry)
3. Low latency (< 10ms)

Isochronous transfer provides:
- Reserved bandwidth (guaranteed)
- No retry (best-effort, but constant rate)
- Fixed timing (1ms frames for USB 2.0)
```

### Transfer Parameters

| Parametre | Değer |
|-----------|-------|
| Frame Size | 1ms (USB 2.0) |
| Packet Size | 44.1 samples @ 44.1kHz |
| Max Packet Size | 1024 bytes (High-Speed) |
| Bandwidth | 480Mbps / 8 = 60MB/s |
| Audio Bandwidth | 8ch × 32bit × 192kHz = 49.152Mbps |
| Utilization | 81.9% (max) |

## Sample Rate Support

| Sample Rate | Bit Depth | Channels | Bandwidth | MCLK |
|-------------|-----------|----------|-----------|------|
| 44.1kHz | 16-bit | 2 | 1.411 Mbps | 11.2896 MHz |
| 44.1kHz | 24-bit | 2 | 2.1168 Mbps | 11.2896 MHz |
| 44.1kHz | 32-bit | 8 | 11.2896 Mbps | 11.2896 MHz |
| 48kHz | 24-bit | 2 | 2.304 Mbps | 12.288 MHz |
| 96kHz | 24-bit | 2 | 4.608 Mbps | 12.288 MHz |
| 192kHz | 24-bit | 2 | 9.216 Mbps | 12.288 MHz |
| 384kHz | 32-bit | 2 | 24.576 Mbps | 12.288 MHz |
| 768kHz | 32-bit | 2 | 49.152 Mbps | 12.288 MHz |

## DSD (DoP) Support

### DoP (DSD over PCM)

```
DoP Format:
- DSD data wrapped in PCM frames
- Uses 16-bit or 24-bit PCM slots
- Marker bits identify DSD data

DoP64:
- DSD rate: 2.8224 MHz
- PCM frame rate: 44.1kHz × 64 = 2.8224MHz
- Uses 16-bit PCM frames

DoP128:
- DSD rate: 5.6448 MHz
- PCM frame rate: 44.1kHz × 128 = 5.6448MHz
- Uses 16-bit PCM frames

DoP256:
- DSD rate: 11.2896 MHz
- PCM frame rate: 44.1kHz × 256 = 11.2896MHz
- Uses 16-bit PCM frames
```

## USB-C Connection

### Pin Mapping

```
USB-C Connector (16-pin)
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V Power
     ├─ GND (A1, A12, B1, B12) ──▶ Ground
     ├─ D+ (A6) ──▶ XU316 USB_DP (Pin 12)
     ├─ D- (A7) ──▶ XU316 USB_DM (Pin 13)
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink identification)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink identification)
     └─ SBU1, SBU2 ──▶ NC (not used for audio)

ESD Protection:
- USBLC6-2SC6 (TVS diode array)
- Clamping voltage: 6V
- Response time: < 1ns
```

## Driver Status

| OS | Driver | Status |
|----|--------|--------|
| Windows 10/11 | UAC2 class-compliant | ✅ Native |
| macOS 10.9+ | UAC2 class-compliant | ✅ Native |
| Linux 4.x+ | ALSA UAC2 | ✅ Native |
| Android 5.0+ | UAC2 class-compliant | ✅ Native |
| iOS 11.0+ | UAC2 class-compliant | ✅ Native |

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | USB Controller | XMOS XU316 | 1 | UAC2 engine |
| 2 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 3 | Crystal | 24.576MHz | 1 | 48kHz family |
| 4 | USB Connector | USB-C 16-pin | 1 | SMD mount |
| 5 | ESD Protection | USBLC6-2SC6 | 1 | TVS array |
| 6 | Decoupling | 100nF MLCC | 4 | USB power |
| 7 | CC Resistors | 5.1kΩ 0402 | 2 | Sink ID |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Ana bileşen | UAC2 firmware |
| K1 Konnektörler | Bağlantı | USB-C connector |
| K2 OS/Sürücüler | Üst | USB enumeration |
| K1 Güç Kaynağı | Alt | +5V VBUS |

## Durum: Implementasyon

**Durum**: 🟢 Hazır

- Firmware: lib_xua (XMOS open-source) kullanılıyor
- USB descriptor: Custom descriptor hazırlandı
- Enumeration: Windows/macOS/Linux test edildi
- DSD: DoP64/128/256 destekleniyor
- Latency: < 1ms measured
- Power: USB bus-powered (5V/500mA)
