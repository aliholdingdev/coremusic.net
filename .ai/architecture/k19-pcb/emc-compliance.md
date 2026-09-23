---
title: "EMC Uyumluluğu ve EMC Test Prosedürleri"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# EMC Uyumluluğu ve EMC Test Prosedürleri

## Genel Bakış

COREMUSIC platformu, Avrupa ve Kuzey Amerika pazarlarında satılacak şekilde tasarlanmıştır. EMC (Electromagnetic Compatibility) uyumluluğu, cihazın electromagnetic interference (EMI) yaratmamasını ve dış EMI'ye karşı bağışıklık olmasını sağlar. Bu belge, CISPR 32/35 ve FCC Part 15 standartlarına uygun tasarım kurallarını ve test prosedürlerini tanımlar.

## Tasarım Kuralları

| Parametre | Değer | Standart |
|-----------|-------|----------|
| Radiated emissions (30-230MHz) | < 30dBμV/m | CISPR 32 Class B |
| Radiated emissions (230-1GHz) | < 37dBμV/m | CISPR 32 Class B |
| Conducted emissions (150kHz-30MHz) | < 48-30dBμV | CISPR 32 Class B |
| ESD immunity | ±8kV contact, ±15kV air | IEC 61000-4-2 |
| EFT immunity | ±1kV | IEC 61000-4-4 |
| Surge immunity | ±1kV line-line | IEC 61000-4-5 |
| Radiated immunity | 3V/m | IEC 61000-4-3 |

## Teknik Detaylar

### EMC Test Düzeneği

#### 3-Meter Chamber Setup
```
┌─────────────────────────────────────────────────────────┐
│                   3-Meter Chamber                        │
│                                                          │
│  ┌──────────────┐                ┌──────────────┐        │
│  │   Turntable  │                │  Receive     │        │
│  │   (DUT)      │    3 meter     │  Antenna     │        │
│  │              │◄───────────────►│              │        │
│  │  ┌────────┐  │                │  ┌────────┐  │        │
│  │  │ CORE-  │  │                │  │Biconical│  │        │
│  │  │ MUSIC  │  │                │  │+ Horn   │  │        │
│  │  │  PCB   │  │                │  │         │  │        │
│  │  └────────┘  │                │  └────────┘  │        │
│  └──────────────┘                └──────────────┘        │
│                                                          │
│  Height: 1-4m (scanning)                                │
│  Polarization: Horizontal + Vertical                    │
│  Frequency range: 30MHz - 1GHz                          │
└─────────────────────────────────────────────────────────┘
```

#### Test Equipment
```
Equipment              │ Model/Type        │ Specification
───────────────────────┼───────────────────┼──────────────────
Spectrum Analyzer       │ R&S FSW           │ 9kHz - 40GHz
Receive Antenna         │ Bilogical         │ 30MHz - 1GHz
Antenna Mast            │ 1-4m height adj   │ Auto-polarization
Turntable               │ 360° rotation     │ 1rpm
EMI Receiver            │ R&S ESR           │ CISPR compliant
LISN (Line)            │ 2-line, 50μH      │ 150kHz - 30MHz
Calibration Kit         │ OATS cal standard │ ±0.5dB
```

### EMC Tasarım Stratejileri

#### 1. Kaynak Bastırma
```
Clock oscillator:
  - Spread spectrum modulation: ±0.5% down-spread
  - Rise time control: 1-2ns (not faster)
  - Series termination: Reduce harmonics

Digital ICs:
  - Decoupling: 100nF + 10nF per power pin
  - Power/ground plane pairing: Reduce loop area
  - Edge rate control: Series resistors (22-33Ω)
```

#### 2. Propagasyon Azaltma
```
PCB Layout:
  - Signal return path: Continuous ground plane
  - High-speed routing: Inner layer (stripline)
  - Via stitching: Board edge, 2mm spacing
  - Copper pour: Fill unused areas

Cable Shielding:
  - Audio cables: Twisted pair + shield
  - USB cable: Ferrite core + shield
  - Power cable: Common-mode choke
```

#### 3. Kalkanlama ve Filtreleme
```
Shielding:
  - Metal enclosure: 2mm aluminum
  - Gasket: Conductive elastomer
  - Aperture: λ/20 at highest frequency

Filtering:
  - Power line: Pi filter (C-L-C)
  - Signal line: Common-mode choke
  - Audio output: EMI filter (ferrite + cap)
```

### Frekans Spektrumu Analizi

#### Temel Frekanslar ve Harmonikler
```
Source         │ Fundamental │ 3rd Harmonic │ 5th Harmonic
───────────────┼─────────────┼──────────────┼─────────────
MCLK (24.576M) │ 24.576 MHz │ 73.728 MHz   │ 122.88 MHz
BCLK (12.288M) │ 12.288 MHz │ 36.864 MHz   │ 61.44 MHz
USB (480M)     │ 480 MHz    │ 1.44 GHz     │ 2.4 GHz
SPI (50M)      │ 50 MHz     │ 150 MHz      │ 250 MHz
```

#### EMC Limitleri ve Margin Analizi
```
Frequency    │ CISPR 32 Limit │ Measured* │ Margin
─────────────┼─────────────────┼───────────┼───────
30-88 MHz    │ 30 dBμV/m     │ 25 dBμV/m │ 5 dB
88-230 MHz   │ 30 dBμV/m     │ 22 dBμV/m │ 8 dB
230-1GHz     │ 37 dBμV/m     │ 28 dBμV/m │ 9 dB

* Simulated values, prototype verification required
```

### Topraklama ve Kalkanlama Stratejisi

#### Enclosure Grounding
```
┌─────────────────────────────────────────────┐
│              Metal Enclosure                 │
│  ┌─────────────────────────────────────┐    │
│  │  ┌─────────────────────────────┐    │    │
│  │  │         PCB Ground          │    │    │
│  │  │            │                │    │    │
│  │  │     ┌──────┴──────┐        │    │    │
│  │  │     │ Star Point  │        │    │    │
│  │  │     │ (Ferrite)   │        │    │    │
│  │  │     └──────┬──────┘        │    │    │
│  │  │            │                │    │    │
│  │  └────────────┼────────────────┘    │    │
│  │               │                     │    │
│  │          ┌────┴────┐                │    │
│  │          │Chassis  │                │    │
│  │          │ Ground  │                │    │
│  │          │ (Screw) │                │    │
│  │          └────┬────┘                │    │
│  │               │                     │    │
│  └───────────────┼─────────────────────┘    │
│                  │                          │
│             Earth Ground                    │
└─────────────────────────────────────────────┘
```

#### Cable Entry Filtering
```
Power Entry:
  ┌─────────────────────────────────────────┐
  │  AC/DC Input                            │
  │  │                                      │
  │  ▼                                      │
  │  ┌──────────┐                           │
  │  │  EMI     │  Pi Filter               │
  │  │  Filter  │  C: 100nF                │
  │  │  (CM)    │  L: 100μH                │
  │  └────┬─────┘  C: 100nF                │
  │       │                                 │
  │  ┌────┴────┐                            │
  │  │  TVS    │  Surge Protection          │
  │  │  Diode  │  ±1kV                     │
  │  └────┬────┘                            │
  │       │                                 │
  │  To PCB                                  │
  └─────────────────────────────────────────┘

Audio Output:
  ┌─────────────────────────────────────────┐
  │  From DAC                               │
  │  │                                      │
  │  ▼                                      │
  │  ┌──────────┐                           │
  │  │  Ferrite │  Common-mode choke        │
  │  │  Bead    │  600Ω @ 100MHz            │
  │  └────┬─────┘                           │
  │       │                                 │
  │  ┌────┴────┐                            │
  │  │  RC     │  EMI Filter                │
  │  │  Filter │  R: 22Ω, C: 10pF           │
  │  └────┬────┘                            │
  │       │                                 │
  │  To Connector                            │
  └─────────────────────────────────────────┘
```

### EMC Bileşen Seçimi

#### EMI Filters
```
Type              │ Component       │ Rating     │ Package
──────────────────┼─────────────────┼────────────┼────────
Common-mode choke │ Murata DLW32MH │ 600Ω/2A    │ 1206
Pi filter         │ TDK MEM2012S   │ 100μH/2A   │ 0805
Ferrite bead      │ Murata BLM31PG │ 600Ω/500mA │ 1206
Feedthrough cap   │ TDK B39102      │ 100pF/2kV  │ SMD
```

#### TVS Diodes
```
Application       │ Component       │ Rating      │ Package
──────────────────┼─────────────────┼─────────────┼────────
Power input       │ Littelfuse SM712│ 12V/12kV    │ SMA
USB data          │ Nexperia PRTR5  │ 5V/15kV     │ SOT-23
Audio output      │ Nexperia PESD5  │ 5V/8kV      │ SOT-23
```

### EMC Layout Kuralları

#### 1. Filter Placement
```
Rule: Filters must be at cable entry point

  Cable ───► Filter ───► PCB
           (immediate)

Anti-pattern:
  Cable ───► Wire ───► Filter ───► PCB
              ↑
         Increases loop area
```

#### 2. Grounding Rules
```
Rule: Single-point ground at star point

  ┌──────────────────────────────────────┐
  │  Digital Section                     │
  │       │                              │
  │       ▼                              │
  │  ┌────────┐                          │
  │  │  DGND  │                          │
  │  └───┬────┘                          │
  │      │                               │
  │  ★ Star Point (Ferrite)             │
  │      │                               │
  │  ┌───┴────┐                          │
  │  │  AGND  │                          │
  │  └────────┘                          │
  │       ▲                              │
  │       │                              │
  │  Analog Section                      │
  └──────────────────────────────────────┘
```

#### 3. Via Stitching
```
Board edge stitching:
  Spacing: 2mm (λ/20 @ 3GHz)
  Connects: L2 + L5 ground planes
  
  ┌─────────────────────────────────┐
  │V V V V V V V V V V V V V V V V│
  │V  ┌─────────────────────────┐  V│
  │V  │                         │  V│
  │V  │    Signal Routing       │  V│
  │V  │                         │  V│
  │V  └─────────────────────────┘  V│
  │V V V V V V V V V V V V V V V V│
  └─────────────────────────────────┘
  V = Via stitching
```

### EMC Doğrulama Testleri

#### Pre-Compliance Test
```
Test                │ Equipment        │ Pass Criteria
────────────────────┼──────────────────┼─────────────────
Spectrum scan        │ SDR + antenna    │ No spikes > limit
Near-field probe    │ H-field probe   │ Identify hot spots
Current probe       │ Clamp meter     │ < 30dBμA
Bulk current inject │ BCI clamp       │ No upset
```

#### Full Compliance Test
```
Test                │ Standard         │ Limit
────────────────────┼──────────────────┼─────────────────
Radiated emissions  │ CISPR 32 Class B │ 30-37dBμV/m
Conducted emissions │ CISPR 32 Class B │ 48-30dBμV
ESD immunity        │ IEC 61000-4-2    │ ±8kV/±15kV
EFT immunity        │ IEC 61000-4-4    │ ±1kV
Surge immunity      │ IEC 61000-4-5    │ ±1kV
Radiated immunity   │ IEC 61000-4-3    │ 3V/m
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Board Setup > Design Rules > Clearance:
  EMC class: HIGH
  Clearance: 0.3mm (min)
  Via clearance to edge: 0.5mm

Board Setup > Design Rules > Routing:
  High-speed net class:
    Trace width: 0.18mm
    Via: 0.2/0.4mm
    Impedance: 50Ω

Copper Pour:
  Pour type: Solid (ground planes)
  Clearance: 0.3mm
  Min width: 0.25mm
  Thermal relief: Yes

Rules > Manufacturing > Silkscreen:
  Min text: 0.8mm height
  Min stroke: 0.15mm
```

### Altium Designer
```
Design > Rules > Electrical > Clearance:
  EMC Clearance: 12mil (0.3mm)
  Applies to: All nets
  Exception: GND to GND = 5mil

Design > Rules > Manufacturing > Silkscreen:
  Min text height: 32mil (0.8mm)
  Min stroke width: 6mil (0.15mm)

Design > Rules > Manufacturing > Via Stitching:
  Enable: Yes
  Spacing: 80mil (2mm)
  Connect to: GND plane
  Layers: All ground layers

Design > Rules > Electrical > Length:
  USB differential pair:
    Length tolerance: 5mil
    Intra-pair skew: 2mil
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:star-grounding | Detail | Topraklama stratejisi |
| K19:signal-integrity | Detail | Sinyal bütünlüğü |
| K14 Test | Output | EMC test prosedürleri |
| K20 Mekanik | Input | Kalkan tasarımı |

## Durum: Implementasyon

- [x] EMC limitleri belirlendi
- [x] Test düzeneği tanımlandı
- [x] Tasarım stratejileri yazıldı
- [x] Bileşen seçimi yapıldı
- [ ] Pre-compliance test
- [ ] Full compliance test
- [ ] EMC sertifikası alınması
