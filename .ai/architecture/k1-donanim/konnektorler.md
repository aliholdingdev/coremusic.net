---
title: "Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC"
layer: K1
category: "Giriş/Çıkış Konnektörleri"
date: 2026-09-20
---

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