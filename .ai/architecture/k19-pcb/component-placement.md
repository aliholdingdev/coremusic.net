---
title: "Bileşen Yerleşimi Stratejisi"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Bileşen Yerleşimi Stratejisi

## Genel Bakış

COREMUSIC PCB tasarımında bileşen yerleşimi, sinyal akışı, termal yönetim ve üretim kolaylığı için kritik öneme sahiptir. Bileşenler fonksiyonel gruplara ayrılarak, sinyal yolunu kısaltacak ve gürültü eşleşmesini azaltacak şekilde yerleştirilir. Bu belge, placement kurallarını, component grouping stratejilerini ve floorplan haritasını tanımlar.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Component orientation | 0°, 90°, 180°, 270° | Pick-and-place uyumlu |
| Min component spacing | 0.5mm (20mil) | Genel |
| SMD pad spacing | 0.25mm (10mil) | Yan yana padler |
| BGA clearance | 0.8mm (32mil) | Under BGA routing |
| Thermal component gap | 2mm (80mil) | Isı kaynakları arası |
| Connector placement | Board edge | ±0.5mm tolerance |
| Decoupling cap placement | ≤3mm to IC pin | VDD pinleri |

## Teknik Detaylar

### Fonksiyonel Bölge Haritası

```
┌─────────────────────────────────────────────────────────────┐
│                    PCB TOP VIEW (L1)                         │
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │  POWER INPUT │  │  DIGITAL     │  │  ANALOG      │       │
│  │  SECTION     │  │  SECTION     │  │  SECTION     │       │
│  │              │  │              │  │              │       │
│  │  DC Jack     │  │  DSP         │  │  ADC         │       │
│  │  ESD Prot    │  │  MCU         │  │  DAC         │       │
│  │  LDO Reg     │  │  Flash       │  │  OP-AMP      │       │
│  │  Ferrites    │  │  USB/SPI     │  │  Ref Voltage │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │  I/O SECTION │  │  CLOCK       │  │  CONNECTORS  │       │
│  │              │  │  SECTION     │  │              │       │
│  │  Audio In    │  │  TCXO        │  │  Audio Jacks │       │
│  │  Audio Out   │  │  PLL         │  │  USB         │       │
│  │  Display     │  │  Oscillator  │  │  Power       │       │
│  │  Buttons     │  │              │  │  Debug       │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Sinyal Akışı Prensibi

#### Analog Ses Yolu
```
Input Path:
  XLR/Line In ──► ESD ──► Coupling Cap ──► ADC (PCM1808)
                                              │
  ┌───────────────────────────────────────────┘
  │  I2S Bus
  ▼
  DSP Processing
  │
  │  I2S Bus
  ▼
  DAC (PCM5102A) ──► LPF ──► OP-AMP ──► Line Out ──► XLR/Line Out

Placement Rules:
  - ADC near input connector
  - DAC near output connector
  - OP-AMP between DAC and output
  - Minimize trace length: < 25mm analog path
```

#### Digital Sinyal Yolu
```
Control Path:
  USB Connector ──► ESD ──► USB PHY ──► MCU/DSP
                                            │
                                            ├──► SPI Flash
                                            ├──► GPIO Expander
                                            └──► I2S to DAC

Placement Rules:
  - USB connector at board edge
  - ESD protection at entry point
  - MCU/DSP central position
  - Flash adjacent to MCU
```

### Bileşen Gruplama Stratejisi

#### Grup 1: Güç Yönetimi
```
┌─────────────────────────────────────┐
│  POWER GROUP                         │
│                                      │
│  ┌─────────┐  ┌─────────┐           │
│  │ DC Jack │  │  ESD    │           │
│  │   J1    │  │  TVS    │           │
│  └────┬────┘  └────┬────┘           │
│       │            │                │
│  ┌────┴────────────┴────┐           │
│  │    Bulk Capacitor     │           │
│  │    470μF / 25V        │           │
│  └──────────┬───────────┘           │
│             │                       │
│  ┌──────────┴───────────┐           │
│  │   Ferrite Bead       │           │
│  │   BLM31PG601         │           │
│  └──────────┬───────────┘           │
│             │                       │
│  ┌──────────┴───────────┐           │
│  │   LDO Regulators     │           │
│  │   AMS1117-3.3        │           │
│  │   AMS1117-1.8        │           │
│  └──────────┬───────────┘           │
│             │                       │
│  ┌──────────┴───────────┐           │
│  │   Output Caps        │           │
│  │   10μF + 100nF       │           │
│  └──────────────────────┘           │
└─────────────────────────────────────┘
```

#### Grup 2: DSP ve Kontrol
```
┌─────────────────────────────────────┐
│  DSP GROUP                           │
│                                      │
│  ┌─────────────────────────────┐    │
│  │      DSP (TMS320C5535)      │    │
│  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐  │    │
│  │  │VDD│ │VDD│ │VDD│ │VDD│  │    │
│  │  └─┬─┘ └─┬─┘ └─┬─┘ └─┬─┘  │    │
│  │    │     │     │     │     │    │
│  └────┼─────┼─────┼─────┼─────┘    │
│       │     │     │     │          │
│  ┌────┴─┐┌──┴──┐┌─┴──┐┌─┴──┐     │
│  │100nF ││100nF││10nF││10nF│     │
│  └──────┘└─────┘└────┘└────┘     │
│                                      │
│  ┌─────────┐  ┌─────────┐           │
│  │  Flash  │  │  RTC    │           │
│  │ W25Q64  │  │ DS3231  │           │
│  └─────────┘  └─────────┘           │
└─────────────────────────────────────┘
```

#### Grup 3: Analog Ses
```
┌─────────────────────────────────────┐
│  ANALOG GROUP                        │
│                                      │
│  ┌─────────┐  ┌─────────┐           │
│  │   ADC   │  │   DAC   │           │
│  │ PCM1808 │  │ PCM5102A│           │
│  └────┬────┘  └────┬────┘           │
│       │            │                │
│  ┌────┴────┐  ┌────┴────┐           │
│  │  LPF    │  │  LPF    │           │
│  │ (Input) │  │ (Output)│           │
│  └────┬────┘  └────┬────┘           │
│       │            │                │
│  ┌────┴────────────┴────┐           │
│  │      OP-AMP          │           │
│  │      OPA2134         │           │
│  └──────────┬───────────┘           │
│             │                       │
│  ┌──────────┴───────────┐           │
│  │  Reference Voltage   │           │
│  │  TLE2426 (VREF)      │           │
│  └──────────────────────┘           │
└─────────────────────────────────────┘
```

### Bileşen Yerleşim Kuralları

#### 1. Decoupling Capacitor Placement
```
Rule: Capacitors ≤ 3mm from IC power pins

  ┌──────────────────────────────┐
  │         IC Package           │
  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐  │
  │  │V1 │ │V2 │ │V3 │ │V4 │  │
  │  └─┬─┘ └─┬─┘ └─┬─┘ └─┬─┘  │
  │    │     │     │     │     │
  │    ▼     ▼     ▼     ▼     │
  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐  │
  │  │C1 │ │C2 │ │C3 │ │C4 │  │  ← 100nF ceramic
  │  └───┘ └───┘ └───┘ └───┘  │
  │     ≤ 3mm to pin            │
  └──────────────────────────────┘

Capacitor Types:
  - Bulk: 10μF tantalum (≤ 5mm)
  - Ceramic: 100nF X7R (≤ 3mm)
  - HF: 10nF X7R (≤ 2mm)
```

#### 2. Connector Placement
```
Rule: Connectors at board edge

  ┌──────────────────────────────────────┐
  │  Board Edge                          │
  │  ┌────┐  ┌────┐  ┌────┐  ┌────┐    │
  │  │DC  │  │USB │  │LIN │  │OUT │    │
  │  │Jack│  │Type│  │ In │  │    │    │
  │  │ J1 │  │C   │  │J3  │  │J4  │    │
  │  └────┘  └────┘  └────┘  └────┘    │
  │                                      │
  │  Edge clearance: 0.5mm minimum       │
  │  Connector overhang: 0mm (flush)     │
  └──────────────────────────────────────┘
```

#### 3. Thermal Component Spacing
```
Rule: Heat sources separated by 2mm minimum

  ┌─────────┐     2mm     ┌─────────┐
  │  LDO    │◄───────────►│  DSP    │
  │ (2W)    │             │ (2.5W)  │
  └─────────┘             └─────────┘

  ┌─────────┐     2mm     ┌─────────┐
  │  Power  │◄───────────►│  ADC    │
  │  MOSFET │             │ (0.5W)  │
  └─────────┘             └─────────┘
```

#### 4. Sensitive Component Isolation
```
Rule: Analog components away from digital noise

  ┌───────────────────────────────────────────┐
  │                                           │
  │   Digital Zone        │    Analog Zone    │
  │   ┌─────┐            │    ┌─────┐        │
  │   │ MCU │◄─── 5mm ──►│    │ ADC │        │
  │   └─────┘   min gap   │    └─────┘        │
  │                       │                   │
  │   ┌─────┐            │    ┌─────┐        │
  │   │ USB │◄─── 5mm ──►│    │ DAC │        │
  │   └─────┘            │    └─────┘        │
  │                       │                   │
  │          GND plane split at boundary      │
  └───────────────────────────────────────────┘
```

### Floorplan Detayları

#### Top Layer (L1) Placement
```
┌─────────────────────────────────────────────────────────────┐
│  TOP LAYER COMPONENT PLACEMENT                              │
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │   DC     │ │  USB     │ │  Audio   │ │  Audio   │       │
│  │  Jack    │ │  Type-C  │ │  Input   │ │  Output  │       │
│  │   J1     │ │   J2     │ │   J3     │ │   J4     │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  ┌────────┐  ┌────────┐  ┌────────┐  ┌────────┐     │   │
│  │  │  LDO   │  │  LDO   │  │  LDO   │  │  LDO   │     │   │
│  │  │ 3.3V   │  │ 1.8V   │  │ 3.3VA  │  │ ±5V    │     │   │
│  │  │ U1     │  │ U2     │  │ U3     │  │ U4/U5  │     │   │
│  │  └────────┘  └────────┘  └────────┘  └────────┘     │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  ┌──────────────────────────────────────────────┐    │   │
│  │  │                                              │    │   │
│  │  │              DSP (TMS320C5535)               │    │   │
│  │  │                  U6                          │    │   │
│  │  │              ┌──────────┐                    │    │   │
│  │  │              │  BGA-96  │                    │    │   │
│  │  │              │  Package │                    │    │   │
│  │  │              └──────────┘                    │    │   │
│  │  │                                              │    │   │
│  │  └──────────────────────────────────────────────┘    │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │   ADC    │ │   DAC    │ │  OP-AMP  │ │  VREF    │       │
│  │ PCM1808  │ │ PCM5102A │ │ OPA2134  │ │ TLE2426  │       │
│  │   U7     │ │   U8     │ │   U9     │ │   U10    │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

#### Bottom Layer (L6) Placement
```
┌─────────────────────────────────────────────────────────────┐
│  BOTTOM LAYER COMPONENT PLACEMENT                           │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Decoupling Capacitors (per IC)                      │   │
│  │                                                      │   │
│  │  C1-C4: DSP VDD decoupling                          │   │
│  │  C5-C8: ADC/DAC AVDD decoupling                     │   │
│  │  C9-C12: LDO output decoupling                      │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Passive Components                                  │   │
│  │                                                      │   │
│  │  R1-R8: Series termination resistors                 │   │
│  │  R9-R16: Pull-up/pull-down resistors                 │   │
│  │  L1-L4: Ferrite beads                               │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Crystal/Oscillator                                  │   │
│  │                                                      │   │
│  │  Y1: TCXO 24.576MHz                                 │   │
│  │  Y2: 32.768kHz RTC crystal                          │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Design for Manufacturing (DFM)

#### Pick-and-Place Considerations
```
Component Type    │ Min Spacing │ Rotation │ Height
──────────────────┼─────────────┼──────────┼────────
0402 SMD          │ 0.3mm       │ Any      │ 0.2mm
0603 SMD          │ 0.4mm       │ Any      │ 0.35mm
0805 SMD          │ 0.5mm       │ Any      │ 0.45mm
SOT-23            │ 0.8mm       │ Any      │ 1.1mm
TSSOP             │ 0.5mm       │ 0° only  │ 1.1mm
QFP               │ 0.5mm       │ 0° only  │ 2.0mm
BGA               │ 1.0mm       │ Any      │ 1.5mm
```

#### Reflow Profile Considerations
```
Component Placement:
  - Large components: Center of board
  - Small components: Board edges
  - Heavy components: Support with adhesive
  - Thermal pads: Via-in-pad required

Board Warpage:
  - Max deflection: 0.5mm per 50mm
  - Balanced placement: Symmetrical
  - Copper balance: Equal density
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Footprint Placement:
  Grid: 0.1mm (fine placement)
  Rotation: 0°, 90°, 180°, 270°
  Clearance: Board rules

Component Classes:
  Class: POWER
    Members: U1-U5, J1, C_bulk
    
  Class: DSP
    Members: U6, Y1, C_dsp
    
  Class: ANALOG
    Members: U7-U10, C_analog

Placement Filters:
  By value: R, C, L
  By footprint: TQFP, BGA
  By net class: VDD, GND
```

### Altium Designer
```
Design > Classes:
  Component Class: POWER_GROUP
    Filter: Comment = 'LDO*' OR Comment = 'AMS*'
    
  Component Class: DSP_GROUP
    Filter: Designator = 'U6'
    
  Component Class: ANALOG_GROUP
    Filter: Comment = 'PCM*' OR Comment = 'OPA*'

Tools > Component Placement:
  Arrange Within Room: Yes
  Room Definition: By component class
  Push To Board: Yes

Design > Rules > Placement:
  Component Clearance: 10mil (0.25mm)
  Component Height: 4mm (max)
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:6-layer-stackup | Reference | Katman yapısı |
| K19:thermal-vias | Detail | Termal via yerleşimi |
| K04 Donanım | Input | Bileşen boyutları |
| K20 Mekanik | Constraint | Kart boyutları |

## Durum: Implementasyon

- [x] Bölge haritası tanımlandı
- [x] Sinyal akışı planlandı
- [x] Bileşen grupları oluşturuldu
- [x] Yerleşim kuralları belirlendi
- [ ] Floorplan simülasyonu
- [ ] DFM kontrolü
- [ ] Prototype doğrulama
