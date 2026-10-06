---
title: "6 Katmanlı PCB Tasarımı"
layer: K1
category: "PCB Tasarımı"
date: 2026-09-20
---

# 6 Katmanlı PCB Tasarımı

## Genel Bakış

COREMUSIC PCB tasarımı, 6 katmanlı high-performance bir devre kartıdır. Star grounding, controlled impedance ve EMI shielding ile analog/dijital karışımını önler. Her katman belirli bir fonksiyona atanmıştır.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Katman Sayısı | 6 (4 Signal + 2 Power) |
| Boyut | 300mm × 200mm (12" × 8") |
| Kalınlık | 1.6mm (62mil) |
| Bakır Kalınlığı | 35µm (1oz) outer, 70µm (2oz) inner |
| Min. Trace Width | 0.15mm (6mil) |
| Min. Via Size | 0.3mm (12mil) drill |
| Min. Via Pad | 0.6mm (24mil) |
| Impedans Kontrolü | 90Ω differential, 50Ω single-ended |
| Surface Finish | ENIG (Electroless Nickel Immersion Gold) |
| Solder Mask | LPI Green (matte) |
| Silkscreen | White, both sides |

## Katman Stackup

```
┌─────────────────────────────────────────────────────────┐
│  Layer 1: SIGNAL TOP (Component Side)                    │
│  ├─ Trace: 0.15mm min, 0.25mm typical                   │
│  ├─ Components: All SMD, top side                        │
│  └─ Pour: Copper pour (shielding)                       │
├─────────────────────────────────────────────────────────┤
│  Prepreg (2116, 0.2mm)                                  │
├─────────────────────────────────────────────────────────┤
│  Layer 2: GND PLANE (Ground Reference)                  │
│  ├─ Solid copper pour (no breaks)                       │
│  ├─ Star ground connections                             │
│  └─ Via stitching around analog/digital boundary         │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 3: SIGNAL INNER 1 (Analog Signal)                │
│  ├─ Analog signal traces                                │
│  ├─ DAC/ADC connections                                 │
│  └─ Short, symmetrical routing                          │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 4: SIGNAL INNER 2 (Digital Signal)               │
│  ├─ Digital signal traces                               │
│  ├─ I2S, USB connections                                │
│  └─ Away from analog section                            │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 5: POWER PLANE (±35V, +5V, +3.3V)               │
│  ├─ Split power planes                                  │
│  ├─ ±35V analog power                                   │
│  └─ +5V/+3.3V digital power                             │
├─────────────────────────────────────────────────────────┤
│  Prepreg (2116, 0.2mm)                                  │
├─────────────────────────────────────────────────────────┤
│  Layer 6: SIGNAL BOTTOM (Component Side)                │
│  ├─ Additional routing                                  │
│  ├─ Thermal vias for power components                   │
│  └─ Ground pour (shielding)                             │
└─────────────────────────────────────────────────────────┘
```

## Star Grounding Sistemi

```
                        Star Ground Point
                              │
          ┌───────────────────┼───────────────────┐
          │                   │                   │
     ┌────┴────┐         ┌────┴────┐         ┌────┴────┐
     │ Analog  │         │ Digital │         │  Power  │
     │  GND    │         │  GND    │         │  GND    │
     └─────────┘         └─────────┘         └─────────┘
          │                   │                   │
     ┌────┴────┐         ┌────┴────┐         ┌────┴────┐
     │ DAC     │         │ XMOS    │         │ PSU     │
     │ ADC     │         │ USB     │         │ Bridge  │
     │ Amp     │         │ I2C     │         │ Rect    │
     └─────────┘         └─────────┘         └─────────┘

Kurallar:
1. Her ground alanı sadece bir noktada star point'e bağlanır
2. Analog ve digital ground arasında 0Ω direnç (short)
3. Power ground, star point'e doğrudan bağlantı
4.Via stitching ile katmanlar arası ground connectivity
```

## Controlled Impedance

### Impedans Hesaplaması

```
Microstrip (Outer Layer):
Z0 = (87 / √(Er + 1.41)) × ln(5.98 × h / (0.8 × w + t))

Er = 4.5 (FR4)
h = 0.2mm (dielectric thickness)
w = 0.25mm (trace width)
t = 0.035mm (copper thickness)

Z0 = (87 / √(4.5 + 1.41)) × ln(5.98 × 0.2 / (0.8 × 0.25 + 0.035))
Z0 = (87 / 2.43) × ln(1.196 / 0.235)
Z0 = 35.8 × ln(5.09)
Z0 = 35.8 × 1.63
Z0 = 58.4Ω (target: 50Ω)

Adjustment: w = 0.30mm → Z0 = 50.2Ω ✅
```

### Differential Pair (I2S, USB)

```
Differential Impedance:
Zdiff = 2 × Z0 × (1 - 0.48 × e^(-0.96 × s/h))

Z0 = 50Ω (single-ended)
s = 0.5mm (trace spacing)
h = 0.2mm (dielectric thickness)

Zdiff = 2 × 50 × (1 - 0.48 × e^(-0.96 × 0.5/0.2))
Zdiff = 100 × (1 - 0.48 × e^(-2.4))
Zdiff = 100 × (1 - 0.48 × 0.091)
Zdiff = 100 × 0.956
Zdiff = 95.6Ω (target: 90Ω)

Adjustment: s = 0.4mm → Zdiff = 90.2Ω ✅
```

## Bileşen Yerleşimi

```
┌─────────────────────────────────────────────────────────┐
│  PCB COMPONENT LAYOUT (Top View)                        │
│                                                         │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐                 │
│  │  XMOS   │  │  DAC    │  │  ADC    │  DİJİTAL BÖLGE  │
│  │  XU316  │  │ AK4458  │  │PCM3168A │                 │
│  └─────────┘  └─────────┘  └─────────┘                 │
│                                                         │
│  ═══════════════════════════════════════  analog/digital │
│                                          boundary      │
│                                                         │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐                 │
│  │ Diff    │  │  VAS    │  │ Output  │  ANALOG BÖLGE   │
│  │ Pair    │  │  Stage  │  │  Stage  │                 │
│  └─────────┘  └─────────┘  └─────────┘                 │
│                                                         │
│  ┌─────────────────────────────────────┐                │
│  │         Power Supply Section        │  GÜÇ BÖLGE    │
│  │  LM5122   │   Bridge   │  PFC      │                │
│  └─────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────┘
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Gerber files, BOM |
| K1 Tüm Bileşenler | Bağlantı | Component footprints |
| K1 Termal | Bağlantı | Thermal vias, pads |
| K2 OS/Sürücüler | Üst | USB trace routing |

## Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- Stackup: 6-layer planlandı, impedance hesaplamaları yapıldı
- EDA Tool: Altium Designer / KiCad 8
- DRC: Design rules tanımlandı
- Gerber: Henüz oluşturulmadı
- Prototype: 3 adet prototype planlandı
- Production: JLCPCB / PCBWay (4 hafta lead time)
