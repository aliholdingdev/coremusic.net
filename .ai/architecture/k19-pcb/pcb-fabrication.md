---
title: "PCB Fabrikasyon Spesifikasyonları"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# PCB Fabrikasyon Spesifikasyonları

## Genel Bakış

COREMUSIC PCB'si, yüksek ses kalitesi ve güvenilirlik için ENIG (Electroless Nickel Immersion Gold) yüzey bitişi ve 2oz bakır ağırlığı ile üretilir. Bu belge, fabrikasyon parametrelerini, yüzey bitiş seçeneklerini,TestId gereksinimlerini ve kalite kontrol prosedürlerini tanımlar.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Board material | FR-4 TG170 | High Tg for lead-free |
| Board thickness | 1.6mm ±10% | Standard |
| Copper weight (outer) | 2oz (70μm) | High current capacity |
| Copper weight (inner) | 1oz (35μm) | Standard |
| Surface finish | ENIG | Nickel 3-6μm, Gold 0.05-0.1μm |
| Min annular ring | 0.15mm (6mil) | Via reliability |
| Min drill size | 0.2mm (8mil) | Mechanical drill |
| Min trace/space | 0.1/0.1mm (4/4mil) | Fine pitch routing |
| Solder mask | LPI Green | Liquid Photo Imageable |
| Silkscreen | White, 0.15mm min | Top + Bottom |

## Teknik Detaylar

### Malzeme Spesifikasyonları

#### FR-4 Substrat
```
Property              │ Value         │ Standard
──────────────────────┼───────────────┼──────────────
Base material         │ FR-4 (epoxy)  │ IPC-4101
Glass transition (Tg) │ 170°C         │ IPC-TM-650
Decomposition (Td)    │ 340°C         │ IPC-TM-650
CTE (x,y)             │ 14 ppm/°C     │ Below Tg
CTE (z-axis)          │ 35 ppm/°C     │ Below Tg
Dk @ 1MHz             │ 4.5           │ IPC-4101D
Dk @ 1GHz             │ 4.4           │ IPC-4101D
Df @ 1MHz             │ 0.02          │ IPC-4101D
Df @ 1GHz             │ 0.018         │ IPC-4101D
Peel strength         │ 1.4 N/mm      │ IPC-TM-650
```

#### Copper Foil
```
Property              │ Outer (L1,L6) │ Inner (L2-L5)
──────────────────────┼───────────────┼──────────────
Type                  │ RA copper     │ ED copper
Weight                │ 2oz (70μm)    │ 1oz (35μm)
Tensile strength      │ 30 ksi        │ 25 ksi
Elongation            │ 15%           │ 10%
Surface roughness     │ ≤ 2μm Rz      │ ≤ 5μm Rz
Conductivity          │ 100% IACS     │ 100% IACS
```

### ENIG Yüzey Bitişi

#### ENIG Katman Yapısı
```
┌─────────────────────────────────────┐
│  Gold Layer (Au)                     │
│  Thickness: 0.05 - 0.1μm            │
│  Purpose: Oxidation prevention       │
├─────────────────────────────────────┤
│  Nickel Layer (Ni)                   │
│  Thickness: 3 - 6μm                 │
│  Purpose: Barrier layer, hardness   │
├─────────────────────────────────────┤
│  Copper Pad (Cu)                     │
│  Base pad from PCB                   │
└─────────────────────────────────────┘

ENIG Advantages:
  - Excellent surface planarity
  - Fine pitch capability (0.4mm pitch)
  - Good solderability
  - Wire bonding capable
  - Long shelf life (> 12 months)

ENIG Disadvantages:
  - Higher cost than HASL
  - "Black pad" risk (if Ni corrupted)
  - Limited reflow cycles (< 3)
```

#### ENIG Process Parameters
```
Process Step        │ Parameter       │ Specification
────────────────────┼─────────────────┼───────────────
Cleaning            │ Duration        │ 5 min
Activation          │ Pd catalyst     │ 0.1 g/L
Nickel plating      │ Temperature     │ 85°C ± 2°C
                    │ pH              │ 4.8 ± 0.2
                    │ Deposition rate │ 12-15 μm/hr
                    │ Duration        │ 20-25 min
Immersion gold      │ Temperature     │ 85°C ± 2°C
                    │ Au concentration│ 1-2 g/L
                    │ Duration        │ 7-10 min
```

### Bakır Ağırlığı ve Kapasite

#### 2oz Copper (Outer Layers)
```
Application: High-current traces, power planes

Current capacity (IPC-2221):
  Trace Width │ 2oz (70μm) │ ΔT=10°C │ ΔT=20°C
  ────────────┼────────────┼─────────┼─────────
  0.5mm       │ 1.6A       │ 2.2A    │
  1.0mm       │ 2.9A       │ 4.1A    │
  2.0mm       │ 5.0A       │ 7.1A    │
  3.0mm       │ 6.9A       │ 9.8A    │

Voltage drop (per mm):
  Width │ 2oz      │ 1oz
  ──────┼──────────┼──────────
  0.5mm │ 4.9mV/A  │ 9.8mV/A
  1.0mm │ 2.4mV/A  │ 4.9mV/A
  2.0mm │ 1.2mV/A  │ 2.4mV/A
```

### Delik ve Via Spesifikasyonları

#### Drill Sizes
```
Type                │ Drill Size  │ Pad Size  │ Annular Ring
────────────────────┼─────────────┼───────────┼─────────────
Micro via           │ 0.1mm       │ 0.25mm    │ 0.075mm
Standard via        │ 0.2mm       │ 0.4mm     │ 0.1mm
Medium via          │ 0.3mm       │ 0.55mm    │ 0.125mm
Large via           │ 0.5mm       │ 0.8mm     │ 0.15mm
Through-hole pin    │ 0.8mm       │ 1.1mm     │ 0.15mm
```

#### Via Plating
```
Parameter           │ Value
────────────────────┼──────────────
Plating thickness   │ 25μm min (1mil)
Barrel plating      │ 20μm min
Aspect ratio        │ 8:1 max
Via fill            │ Optional (epoxy)
Via cap             │ Required for via-in-pad
```

### Solder Mask Spesifikasyonları

#### LPI (Liquid Photo Imageable)
```
Property              │ Value
──────────────────────┼──────────────
Type                  │ LPI (photo-imageable)
Color                 │ Green (standard)
Thickness             │ 10-25μm
Dk @ 1GHz             │ 3.5
Solder mask dam       │ 0.075mm (3mil) min
Solder mask clearance │ 0.05mm (2mil) min
Registration          │ ±0.05mm
```

#### Solder Mask Opening
```
SMD Pads:
  Opening = Pad size + 0.05mm (per side)
  Example: 0.5mm pad → 0.6mm opening

BGA Pads:
  Opening = Pad size + 0.025mm (per side)
  Example: 0.3mm pad → 0.35mm opening

Via openings:
  Tenting: Yes (for vias not to be soldered)
  Opening: Only for soldering vias
```

### Silkscreen Spesifikasyonları

```
Parameter           │ Value
────────────────────┼──────────────
Method              │ Screen printing
Color               │ White
Min text height     │ 0.8mm (32mil)
Min stroke width    │ 0.15mm (6mil)
Registration        │ ±0.1mm
Silkscreen-to-pad   │ 0.1mm min clearance
```

### Board Outline ve Routing

#### CNC Routing
```
Parameter           │ Value
────────────────────┼──────────────
Tool size           │ 1.6mm (1/16")
Tolerance           │ ±0.1mm
Edge quality        │ Smooth, no burrs
V-score             │ Optional (panel)
Tab-route           │ Optional (panel)
Mouse bites         │ 0.5mm holes, 0.3mm web
```

#### Board Dimensions
```
COREMUSIC PCB:
  Width: 100mm ± 0.2mm
  Height: 80mm ± 0.2mm
  Thickness: 1.6mm ± 0.16mm
  Corner radius: 1mm (optional)

Panel dimensions:
  Width: 250mm (multiple boards)
  Height: 200mm
  Fiducials: 3 (top, bottom, side)
  Tooling holes: 4 (corners, 3.2mm)
```

### Test Gereksinimleri

#### Flying Probe Test
```
Test type: Flying probe (no fixture needed)
Coverage: 100% net connectivity
Test points: All pads > 0.3mm

Parameters:
  Probe type: 4-point (2 per side)
  Probe diameter: 0.35mm
  Test voltage: 10V DC
  Test current: 10mA
  Resistance threshold: 10Ω (open/short)

Test coverage:
  - Open circuit: 100%
  - Short circuit: 100%
  - Resistance: 100% (within tolerance)
  - Capacitance: Optional
```

#### ICT (In-Circuit Test)
```
If volume > 1000 boards:
  Fixture: Custom bed-of-nails
  Probe count: 200-500 probes
  Test time: 30-60 seconds per board

Coverage:
  - Component presence/absence
  - Component value (R, C, L)
  - Diode orientation
  - Transistor function
  - IC functional test
```

### Kalite Kontrol Prosedürleri

#### Visual Inspection
```
Check               │ Criteria           │ Method
────────────────────┼────────────────────┼────────────
Solder mask         │ No voids/bubbles   │ 10× magnifier
Silkscreen          │ Legible, aligned   │ Visual
Copper exposure     │ None               │ 20× magnifier
Scratches           │ < 25% trace width  │ 20× magnifier
Dents               │ < 0.1mm depth      │ Visual
Edge quality        │ No delamination    │ Visual
```

#### Dimensional Verification
```
Parameter           │ Spec           │ Tool
────────────────────┼────────────────┼────────────
Board thickness     │ 1.6mm ± 0.16mm │ Micrometer
Board width         │ 100mm ± 0.2mm  │ Caliper
Board height        │ 80mm ± 0.2mm   │ Caliper
Via drill size      │ ±0.05mm        │ Microscope
Trace width         │ ±10%           │ Microscope
```

#### Electrical Testing
```
Test                │ Equipment       │ Criteria
────────────────────┼─────────────────┼────────────
Continuity          │ Flying probe    │ 100% pass
Insulation          │ Flying probe    │ > 10MΩ
Impedance           │ TDR             │ 50Ω ±10%
Crosstalk           │ Network analyzer│ < -30dB
```

### Panelizasyon

#### Panel Tasarımı
```
┌──────────────────────────────────────────────────────────────┐
│  Panel (250mm × 200mm)                                       │
│                                                              │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐           │
│  │  Board  │ │  Board  │ │  Board  │ │  Board  │           │
│  │    1    │ │    2    │ │    3    │ │    4    │           │
│  │ 100×80  │ │ 100×80  │ │ 100×80  │ │ 100×80  │           │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘           │
│                                                              │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐           │
│  │  Board  │ │  Board  │ │  Board  │ │  Board  │           │
│  │    5    │ │    6    │ │    7    │ │    8    │           │
│  │ 100×80  │ │ 100×80  │ │ 100×80  │ │ 100×80  │           │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘           │
│                                                              │
│  ● O O O   ← Fiducials (3)                                  │
│  ◎       ◎  ← Tooling holes (2, 3.2mm)                     │
│                                                              │
└──────────────────────────────────────────────────────────────┘

Panel features:
  - 8 boards per panel
  - Breakaway tabs: 0.5mm web, 0.5mm holes
  - Fiducials: 1mm copper, 2mm opening
  - Tooling holes: 3.2mm, no copper
  - Rail width: 5mm (for handling)
```

### Dosya Formatları

#### Gerber Files
```
Layer       │ File extension │ Format
────────────┼────────────────┼──────────
Top copper  │ .gtl           │ RS-274X
Bottom copper │ .gbl         │ RS-274X
Inner 1 (GND) │ .g1          │ RS-274X
Inner 2 (SIG) │ .g2          │ RS-274X
Inner 3 (PWR) │ .g3          │ RS-274X
Inner 4 (GND) │ .g4          │ RS-274X
Top silk    │ .gto           │ RS-274X
Bottom silk │ .gbo           │ RS-274X
Top mask    │ .gts           │ RS-274X
Bottom mask │ .gbs           │ RS-274X
Drill       │ .drl           │ Excellon
Board outline │ .gko         │ RS-274X
```

#### Drill File Format
```
Format: Excellon
Units: Metric (mm)
Zero suppression: Leading
Coordinate format: 3.3 (X.XXX)

Header:
  M48           ; Start of header
  METRIC        ; Units
  T1C0.200      ; Tool 1: 0.2mm
  T2C0.300      ; Tool 2: 0.3mm
  T3C0.500      ; Tool 3: 0.5mm
  %             ; End of header

Body:
  T1            ; Select tool 1
  X12.500Y8.250 ; Drill at X=12.5, Y=8.25
  X15.000Y8.250 ; Next drill
  ...
  M30           ; End of file
```

### Fabrikasyon Notları

#### Critical Dimensions
```
Dimension           │ Tolerance    │ Impact
────────────────────┼──────────────┼────────────────
Trace width         │ ±10%         │ Impedance control
Trace spacing       │ ±10%         │ Crosstalk
Via drill           │ ±0.05mm      │ Annular ring
Via pad             │ ±0.05mm      │ Connection
Board outline       │ ±0.2mm       │ Mechanical fit
Layer registration  │ ±0.05mm      │ Via connection
```

#### Special Instructions
```
1. Via-in-pad: Epoxy filled, plated over
2. Impedance control: 50Ω ±10% on L3
3. Copper balance: Equal density per layer
4. Breakaway tabs: Mouse bite style
5. Fiducials: 3 per side (top/bottom)
6. Marking: UL mark + date code
7. Packaging: Vacuum sealed, desiccant
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
File > Fabrication Outputs:
  Gerber files:
    Format: RS-274X
    Units: mm
    Precision: 4.4
    Drill format: Excellon, metric
    Gerber job file: Yes
    
  Drill files:
    Format: Excellon
    Units: mm
    Drill map: PDF format
    PTH/NPTH: Separate files

Board Setup > Output Settings:
  Gerber:
    Use Protel filename extensions
    Subtract soldermask from silkscreen
    Use auxiliary axis as origin
  Drill:
    Generate map file
    Drill units: mm
    Zeros: Leading
```

### Altium Designer
```
File > Fabrication Outputs > Gerber:
  Units: Metric
  Format: 4:4 (leading zeros omitted)
  Layers: Select all
  
  Layer assignment:
    Top Layer → Top Copper
    Bottom Layer → Bottom Copper
    Mid1 Layer → GND Plane
    Mid2 Layer → Signal
    Mid3 Layer → Power
    Mid4 Layer → GND Plane
    Top Overlay → Silkscreen
    Bottom Overlay → Silkscreen
    Top Solder → Solder Mask
    Bottom Solder → Solder Mask

File > Fabrication Outputs > NC Drill:
  Units: Metric
  Format: 4:4
  Leading zeros: Suppress
  Drill map: Separate file

File > Assembly Outputs:
  Pick and Place: Yes
  BOM: Yes
  3D model: STEP export
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:6-layer-stackup | Reference | Katman yapısı |
| K19:thermal-vias | Detail | Via spesifikasyonları |
| K13 CI/CD | Output | Üretim dosyaları |
| K14 Test | Output | Test prosedürleri |

## Durum: Implementasyon

- [x] Malzeme spesifikasyonları tanımlandı
- [x] ENIG yüzey bitişi belirlendi
- [x] Delik ve via kuralları yazıldı
- [x] Test gereksinimleri tanımlandı
- [ ] Fabrikasyon dosyaları üretildi
- [ ] Prototype siparişi
- [ ] Kalite kontrol doğrulaması
