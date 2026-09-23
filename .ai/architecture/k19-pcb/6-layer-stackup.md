---
title: "6 Katmanlı PCB Stackup Tasarımı"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# 6 Katmanlı PCB Stackup Tasarımı

## Genel Bakış

6 katmanlı PCB stackup, COREMUSIC audio platformu için optimize edilmiş bir kart yapısı sunar. Her katman belirli bir fonksiyona hizmet eder: sinyal yönlendirme, güç dağıtımı ve EMC kontrolü. Toplam kart kalınlığı 1.6mm standart FR-4 malzemesi ile gerçekleştirilir.

## Katman Yapısı

```
         ┌──────────────────────────────────┐
         │     Solder Mask (Top, Green)      │
         ├──────────────────────────────────┤
  L1     │  Signal + Components (Top)        │  35μm (1oz)
         │  185°C max operating temp         │
         ├──────────────────────────────────┤
         │  Prepreg (7628, 0.21mm)           │
         ├──────────────────────────────────┤
  L2     │  GND Plane (Inner 1)              │  35μm (1oz)
         │  Continuous copper pour           │
         ├──────────────────────────────────┤
         │  Core (FR-4, 0.36mm)              │
         ├──────────────────────────────────┤
  L3     │  Signal Routing (Inner 2)         │  35μm (1oz)
         │  High-speed signals only          │
         ├──────────────────────────────────┤
         │  Prepreg (2116, 0.12mm)           │
         ├──────────────────────────────────┤
  L4     │  Power Plane (Inner 3)            │  35μm (1oz)
         │  Split planes: 3.3V, 1.8V, ±5V   │
         ├──────────────────────────────────┤
         │  Core (FR-4, 0.36mm)              │
         ├──────────────────────────────────┤
  L5     │  GND Plane (Inner 4)              │  35μm (1oz)
         │  Return path reference            │
         ├──────────────────────────────────┤
         │  Prepreg (7628, 0.21mm)           │
         ├──────────────────────────────────┤
  L6     │  Signal + Components (Bottom)     │  35μm (1oz)
         │  SMD components, routing          │
         ├──────────────────────────────────┤
         │     Solder Mask (Bottom, Green)   │
         └──────────────────────────────────┘

         Toplam kalınlık: 1.6mm ±10%
```

## Katman Fonksiyonları

### L1: Top Signal Layer
- **Amaç**: Ana bileşen yerleşimi ve sinyal yönlendirme
- **Bileşenler**: DSP, ADC, DAC, OP-AMP, konnektörler
- **Kurallar**: 
  - Min trace: 0.1mm (4mil) genel
  - Min spacing: 0.1mm (4mil) genel
  - Via viajsız pads: through-hole bileşenler
- **Not**: Hassas analog sinyaller bu katmanda corrozive edilmeli

### L2: Ground Plane (Inner 1)
- **Amaç**: Sürekli referans düzlemi,低impedans dönüş yolu
- **Özellik**: Delik openings minimize edilmeli
- **Önem**: L1 sinyalleri için	return path
- **Kurallar**:
  - Pour topology: solid (not hatched)
  - Via stitching: board edge'de 2mm aralıkla
  - Split plane: yok (continuous)

### L3: Inner Signal Layer (Inner 2)
- **Amaç**: Yüksek hızlı sinyaller için izole edilmiş katman
- **Sinyaller**: I2S bus, USB differential pairs, SPI
- **Avantaj**: Her iki tarafta ground plane (L2/L4)
- **Kurallar**:
  - Differential pair spacing: 0.2mm (8mil)
  - Length matching: ±50mil tolerance
  - No vias except necessary transitions

### L4: Power Plane (Inner 3)
- **Amaç**: Güç dağıtımı için split plane yapısı
- **Planes**:
  ```
  ┌─────────────────────────────────┐
  │  VDD_IO (3.3V)    │  VDD_ANA (3.3V)  │
  │  60% alan         │  25% alan         │
  │                    │                   │
  │                    ├───────────────────┤
  │                    │  VDD_CORE (1.8V)  │
  │                    │  10% alan         │
  ├────────────────────┴───────────────────┤
  │         VDD_AMP (±5V)                 │
  │         5% alan                       │
  └────────────────────────────────────────┘
  ```
- **Kurallar**:
  - Plane clearance: 0.3mm (12mil)
  - Star-point connection: GND'a tek nokta
  - Ferrite bead placement: plane geçişlerinde

### L5: Ground Plane (Inner 4)
- **Amaç**: L6 sinyalleri için dönüş yolu
- **Özellik**: L2 ile via stitching ile bağlanmalı
- **Kurallar**:
  - Stitching vias: 2mm aralıkla
  - Board edge clearance: 0.5mm
  - Thermal relief pads

### L6: Bottom Signal Layer
- **Amaç**: SMD bileşenler ve ek routing
- **Bileşenler**: Pasifler, bypass kapasitörleri, konnektörler
- **Kurallar**:
  - Bottom-side components: max 40% board area
  - Thermal pad via stitching: minimum 4 vias

## Malzeme Spesifikasyonları

### FR-4 Dielectric
| Özellik | Değer | Not |
|---------|-------|-----|
| Tg (Glass Transition) | 170°C | Minimum |
| Dk @ 1GHz | 4.5 | ±0.5 |
| Df @ 1GHz | 0.02 | Max |
| CTE (z-axis) | 35 ppm/°C | Below Tg |
| Thermal conductivity | 0.3 W/mK | Typical |

### Copper Foil
| Özellik | Değer | Not |
|---------|-------|-----|
| Weight (L1,L6) | 1oz (35μm) | Standard |
| Weight (L2-L5) | 1oz (35μm) | Standard |
| Tensile strength | 30 ksi | Min |
| Elongation | 15% | Min |

### Solder Mask
| Özellik | Değer | Not |
|---------|-------|-----|
| Type | LPI (Liquid Photo Imageable) | Green |
| Thickness | 10-25μm | Per layer |
| Dk @ 1GHz | 3.5 | Typical |
| Solder mask dam | 0.075mm | Min between pads |

## Impedans Hesaplamaları

### Microstrip (L1, L6)
```
Zo = (87 / √(εr + 1.41)) × ln(5.98h / (0.8w + t))

εr = 4.5 (FR-4)
h = 0.21mm (prepreg thickness)
w = 0.18mm (trace width for 50Ω)
t = 0.035mm (copper thickness)

Zo ≈ 50Ω ±10%
```

### Stripline (L3)
```
Zo = (60 / √εr) × ln(4h / (0.67 × (0.8w + t)))

h = 0.36mm (core thickness to ground)
w = 0.12mm (trace width for 50Ω)

Zo ≈ 50Ω ±10%
```

## Via Yapıları

### Through-Hole Via
```
    ┌───┐
    │   │◄─── Copper barrel
    │   │
    └───┘
  L1 ████ L6
    Via drill: 0.2mm
    Pad diameter: 0.4mm
    Annular ring: 0.1mm
```

### Via-in-Pad (BGA)
```
    ┌───┐
    │   │◄─── Filled via
    │   │     (epoxy filled)
    └───┘
  L1 ████ L6
  Via: 0.15mm drill
  Pad: 0.35mm diameter
  Used for: BGA breakout
```

## Stitching Via Kuralları

| Bölge | Aralık | Amaç |
|-------|--------|------|
| Board edge | 2mm | EMI containment |
| Ground planes | 5mm | Plane stitching |
| High-speed area | 3mm | Return path |
| Thermal pad | 2mm | Heat dissipation |

## EMC Tasarım Kuralları

### Ground Plane Bütünlüğü
- L2 ve L5 sürekli copper pour olmalı
- Slot opening: max 2mm
- Split plane: sadece L4 (power)
- Edge clearance: 0.5mm minimum

### Sinyal Koridorları
- High-speed signals: L3 only (stripline)
- Analog signals: L1 top, away from digital
- Return current path: ground plane via stitching

## KiCad/Altium Ayarları

### KiCad 8.0 - Layer Setup
```
File > Board Setup > Layer Stackup

Layer 1: F.Cu
  Type: signal
  Thickness: 0.035mm

Dielectric 1:
  Material: FR-4
  Thickness: 0.21mm (prepreg 7628)
  Dk: 4.5

Layer 2: In1.Cu
  Type: signal
  Thickness: 0.035mm

Core 1:
  Material: FR-4
  Thickness: 0.36mm
  Dk: 4.5

Layer 3: In2.Cu
  Type: signal
  Thickness: 0.035mm

Dielectric 2:
  Material: FR-4
  Thickness: 0.12mm (prepreg 2116)
  Dk: 4.5

Layer 4: In3.Cu
  Type: signal
  Thickness: 0.035mm

Core 2:
  Material: FR-4
  Thickness: 0.36mm
  Dk: 4.5

Layer 5: In4.Cu
  Type: signal
  Thickness: 0.035mm

Dielectric 3:
  Material: FR-4
  Thickness: 0.21mm (prepreg 7628)
  Dk: 4.5

Layer 6: B.Cu
  Type: signal
  Thickness: 0.035mm
```

### Altium Designer
```
Layer Stack Manager:
  Stack: 6-Layer Standard
  Material: FR-4 TG170

  Layer   | Type      | Copper | Dielectric
  --------|-----------|--------|-----------
  Top     | Signal    | 1oz    | -
  Prepreg | -         | -      | 0.21mm 7628
  GND1    | Plane     | 1oz    | -
  Core    | -         | -      | 0.36mm FR4
  SIG     | Signal    | 1oz    | -
  Prepreg | -         | -      | 0.12mm 2116
  PWR     | Plane     | 1oz    | -
  Core    | -         | -      | 0.36mm FR4
  GND2    | Plane     | 1oz    | -
  Prepreg | -         | -      | 0.21mm 7628
  Bottom  | Signal    | 1oz    | -
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Parent | Genel PCB tasarım referansı |
| K19:controlled-impedance | Detail | Impedans hesaplamaları |
| K19:thermal-vias | Detail | Termal via yerleşimi |
| K04 Donanım | Input | Bileşen seçimi |
| K20 Mekanik | Constraint | Kart mekanik sınırları |

## Durum: Implementasyon

- [x] Stackup yapısı tanımlandı
- [x] Malzeme spesifikasyonları belirlendi
- [x] Impedans hesaplamaları yapıldı
- [x] Via yapıları tanımlandı
- [ ] Impedans simülasyonu
- [ ] Fabrikasyon onayı
- [ ] Prototype doğrulama
