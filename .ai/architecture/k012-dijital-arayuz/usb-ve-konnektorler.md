---
title: "USB Audio ve Konnektörler - k012-dijital-arayuz"
type: architecture
category: mimari
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı"
updated: 2026-10-06
---

# USB Audio ve Konnektörler

- **Amaç:** USB Audio Class 2.0 (descriptor hierarchy, isochronous transfer, sample rate, DoP/DSD, USB-C pin mapping) ve fiziksel konnektör seti (XLR, RCA, USB-C, Optical/Toslink, HDMI ARC, binding posts).
- **Persona:** `audio-hardware-engineer`
- **Wiki-link:** [[index.md]]
- **Çapraz referanslar:** [[../k010-class-ab-cikis/index]] · [[../k013-pcb-hoparlor/index]]

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` | tüm başlıklar (H1 + ##/###) | L8-L194 | verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md` | tüm başlıklar (H1 + ##/###) | L8-L190 | verbatim |

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md - L8-L194

# Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC

## Genel Bakış

COREMUSIC konnektör sistemi, çeşitli ses ve kontrol arabirimleri için endüstri standartlarında bağlantı noktaları sağlar. Balanced XLR, unbalanced RCA, dijital USB-C, Optical (Toslink) ve HDMI ARC konnektörleri kullanılır.

## Teknik Spesifikasyonlar

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

## XLR Konnektörü

### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    XLR Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ 1 │       │  Pin 1: Ground (Shield)
    │    /│   │\      │  Pin 2: Hot (+, Non-inverting)
    │   / │ 2 │ \     │  Pin 3: Cold (-, Inverting)
    │  /  │   │  \    │
    │ / 3 │   │   \   │
    │/    └───┘    \  │
    │               │ │
    └─────────────────┘
```

### XLR Devre Bağlantısı

```
XLR Input (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──▶ 100Ω ──▶ ADC AINL+ (PCM3168A)
     └─ Pin 3 (Cold) ─▶ 100Ω ──▶ ADC AINL- (PCM3168A)

XLR Output (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──◀ DAC OUTL+ (AK4458)
     └─ Pin 3 (Cold) ─◀ DAC OUTL- (AK4458)
```

## RCA Konnektörü

### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    RCA Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ + │       │  Center Pin: Signal (+)
    │     └───┘       │  Outer Ring: Ground (Shield)
    │    ╱     ╲      │
    │   ╱       ╲     │
    │  ╱─────────╲    │
    │                 │
    └─────────────────┘
```

## USB-C Konnektörü

### Pin Konfigürasyonu

```
USB-C 16-Pin Connector
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V (VBUS)
     ├─ GND (A1, A12, B1, B12) ──▶ DGND
     ├─ D+ (A6) ──▶ XU316 USB_DP
     ├─ D- (A7) ──▶ XU316 USB_DM
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink)
     ├─ SBU1 (A8) ──▶ NC (not used)
     └─ SBU2 (B8) ──▶ NC (not used)
```

## Optical (Toslink)

### Pin Konfigürasyonu

```
Toslink Connector
     │
     ├─ TX ──▶ LED Transmitter (TOS1103)
     ├─ RX ──▶ Photodiode Receiver
     └─ GND ──▶ Shield

Not: Optik izolasyon sağlar, ground loop engeller.
```

## HDMI ARC

### Pin Konfigürasyonu

```
HDMI ARC (Audio Return Channel)
     │
     ├─ TMDS Data0+ ──▶ LVDS Receiver
     ├─ TMDS Data0- ──▶ LVDS Receiver
     ├─ TMDS Data1+ ──▶ LVDS Receiver
     ├─ TMDS Data1- ──▶ LVDS Receiver
     ├─ TMDS Data2+ ──▶ LVDS Receiver
     ├─ TMDS Data2- ──▶ LVDS Receiver
     ├─ TMDS Clock+ ──▶ LVDS Receiver
     ├─ TMDS Clock- ──▶ LVDS Receiver
     ├─ CEC ──▶ CEC Controller (I2C)
     ├─ SCL ──▶ EDID I2C Clock
     ├─ SDA ──▶ EDID I2C Data
     ├─ +5V ──▶ Power
     ├─ HPD ──▶ Hot Plug Detect
     └─ GND ──▶ Shield

ARC Modu: TV'den ses sinyalini HDMI kablo üzerinden alır.
```

## Binding Posts (Hoparlör Çıkışları)

### Pin Konfigürasyonu

```
Binding Post (4mm Banana Compatible)
     │
     ├─ Red (+) ──▶ Amplifikatör Output (+)
     ├─ Black (-) ──▶ Amplifikatör Output (-)
     └─ Banana Plug / Spade / Bare Wire uyumlu

Torque: 1.5 Nm (tightening)
Maks. Kablo Kesiti: 4 AWG (21.2mm²)
```

## Panel Düzeni

```
┌─────────────────────────────────────────────────────────┐
│                     ARKA PANEL                          │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 1│ │XLR 2│ │XLR 3│ │XLR 4│ │XLR 5│ │XLR 6│      │
│  │FL   │ │FR   │ │C    │ │SL   │ │SR   │ │SBL  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 7│ │RCA 1│ │RCA 2│ │USB-C│ │TosLk│ │HDMI │      │
│  │SBR  │ │L    │ │R    │ │In   │ │In   │ │ARC  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS                       │    │
│  │  (+FL) (-FL) (+FR) (-FR) (+C) (-C) (+SL) (-SL) │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS (Devam)               │    │
│  │  (+SR) (-SR) (+SBL)(-SBL)(+SBR)(-SBR)(+LFE)(-LFE)│   │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐                               │
│  │IEC │ │Fuse │ │SWITCH│                               │
│  │Inlet│ │     │ │     │                               │
│  └─────┘ └─────┘ └─────┘                               │
└─────────────────────────────────────────────────────────┘
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

## Durum: Implementasyon

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/usb-audio.md - L8-L190

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

