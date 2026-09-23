---
title: "Güç Dağıtımı ve Güç Düzlemi Tasarımı"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Güç Dağıtımı ve Güç Düzlemi Tasarımı

## Genel Bakış

COREMUSIC platformunda güç dağıtımı, hassas analog devreler için temiz ve kararlı besleme sağlar. 12V DC girişten alınan güç, birden fazla LDO regülatör ile 3.3V, 1.8V ve ±5V seviyelerine dönüştürülür. Her güç hattı için filtreleme, regülasyon ve bypass kapasitörleri plateau design ile optimize edilmiştir.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Copper weight (L4) | 1oz (35μm) | Power plane |
| Copper weight (L1,L6) | 2oz (70μm) | High-current traces |
| Min trace width (power) | 0.5mm (20mil) | Per amp rule |
| Plane clearance | 0.3mm (12mil) | Between planes |
| Bypass cap to pin | ≤3mm (120mil) | Decoupling |
| Bulk cap to regulator | ≤10mm (400mil) | Input/output |
| Via current capacity | 0.5A per via | Standard via |

## Teknik Detaylar

### Güç Mimarisi Şeması

```
┌─────────────────────────────────────────────────────────────────┐
│                    POWER ARCHITECTURE                            │
│                                                                  │
│  DC Input (12V, 2A)                                             │
│       │                                                          │
│       ▼                                                          │
│  ┌────────────┐                                                  │
│  │  ESD/Fuse  │  TVS + 2A PTC fuse                             │
│  │  Protection│                                                  │
│  └─────┬──────┘                                                  │
│        │                                                         │
│  ┌─────┴──────┐                                                  │
│  │   Bulk     │  470μF electrolytic                              │
│  │   Cap      │  + 10μF ceramic                                 │
│  └─────┬──────┘                                                  │
│        │                                                         │
│  ┌─────┴──────┐                                                  │
│  │  Ferrite   │  600Ω @ 100MHz                                  │
│  │  Bead      │  1A rated                                       │
│  └─────┬──────┘                                                  │
│        │                                                         │
│        ├──────────────┬──────────────┬──────────────┐            │
│        │              │              │              │            │
│        ▼              ▼              ▼              ▼            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │ LDO 1    │  │ LDO 2    │  │ LDO 3    │  │ LDO 4    │        │
│  │ 3.3V     │  │ 1.8V     │  │ 3.3V_ANA │  │ ±5V      │        │
│  │ 500mA    │  │ 300mA    │  │ 200mA    │  │ 150mA    │        │
│  │ AMS1117  │  │ AP2112   │  │ LP5907   │  │ TPS7A49  │        │
│  └─────┬────┘  └─────┬────┘  └─────┬────┘  └─────┬────┘        │
│        │              │              │              │            │
│        ▼              ▼              ▼              ▼            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │
│  │ VDD_IO   │  │ VDD_CORE │  │ VDD_ANA  │  │ VDD_AMP  │        │
│  │ 3.3V     │  │ 1.8V     │  │ 3.3V     │  │ +5V/-5V  │        │
│  │ Digital  │  │ DSP Core │  │ ADC/DAC  │  │ OP-AMP   │        │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Güç Plane Tasarımı (L4)

#### Plane Bölümleri
```
┌─────────────────────────────────────────────────────────────────┐
│                    POWER PLANE (L4)                              │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                                                            │ │
│  │  ┌──────────────────────────────────────────────────┐     │ │
│  │  │           VDD_IO (3.3V) - 55% area               │     │ │
│  │  │                                                  │     │ │
│  │  │  ┌─────────┐ ┌─────────┐ ┌─────────┐            │     │ │
│  │  │  │  DSP    │ │  MCU    │ │  USB    │            │     │ │
│  │  │  │  VDD    │ │  VDD    │ │  PHY    │            │     │ │
│  │  │  └─────────┘ └─────────┘ └─────────┘            │     │ │
│  │  │                                                  │     │ │
│  │  └──────────────────────────────────────────────────┘     │ │
│  │                                                            │ │
│  │  ┌──────────────────────────┐  ┌──────────────────────┐  │ │
│  │  │   VDD_ANA (3.3V) - 25%  │  │  VDD_CORE (1.8V)    │  │ │
│  │  │                          │  │   15% area           │  │ │
│  │  │  ┌────────┐ ┌────────┐   │  │  ┌────────┐         │  │ │
│  │  │  │  ADC   │ │  DAC   │   │  │  │  DSP   │         │  │ │
│  │  │  │  AVDD  │ │  AVDD  │   │  │  │  CORE  │         │  │ │
│  │  │  └────────┘ └────────┘   │  │  └────────┘         │  │ │
│  │  └──────────────────────────┘  └──────────────────────┘  │ │
│  │                                                            │ │
│  │  ┌──────────────────────────────────────────────────┐     │ │
│  │  │           VDD_AMP (±5V) - 5% area                │     │ │
│  │  │                                                  │     │ │
│  │  │  ┌──────────────────────────────────────┐       │     │ │
│  │  │  │  +5V Rail    │    -5V Rail           │       │     │ │
│  │  │  │  (OP-AMP+)   │    (OP-AMP-)          │       │     │ │
│  │  │  └──────────────────────────────────────┘       │     │ │
│  │  └──────────────────────────────────────────────────┘     │ │
│  │                                                            │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Güç Dağıtım Ağı (PDN) Analizi

#### Impedans Hedefleri
```
Frequency Range    │ Target Impedance │ Method
───────────────────┼──────────────────┼────────────────
DC - 1kHz          │ < 10mΩ          │ Copper plane
1kHz - 100kHz      │ < 50mΩ          │ Bulk caps
100kHz - 10MHz     │ < 100mΩ         │ Ceramic caps
10MHz - 100MHz     │ < 500mΩ         │ HF caps + plane
100MHz - 1GHz      │ < 1Ω            │ Plane + via
```

#### Bypass Stratejisi
```
Component         │ Value    │ Package │ Location     │ Count
──────────────────┼──────────┼─────────┼──────────────┼──────
Bulk Electrolytic │ 470μF    │ Φ8×12   │ Near LDO in  │ 1
Bulk Ceramic      │ 10μF     │ 1206    │ Near LDO in  │ 1
LDO Output        │ 10μF     │ 1206    │ Near LDO out │ 4
LDO Output        │ 100nF    │ 0805    │ Near LDO out │ 4
DSP VDD           │ 100nF    │ 0402    │ Per pin      │ 16
DSP VDD           │ 10nF     │ 0402    │ Per pin      │ 8
ADC/DAC AVDD      │ 100nF    │ 0402    │ Per pin      │ 4
OP-AMP            │ 100nF    │ 0402    │ Per pin      │ 2
```

### Yüksek Akım Yolları

#### 12V Input Path
```
DC Jack ──► TVS ──► PTC Fuse ──► Bulk Cap ──► Ferrite ──► LDO Input
                                 │
  Trace width: 1.0mm (40mil)     │ 470μF
  Current: 2A max               │
  Via count: 2 parallel         │
```

#### 3.3V Digital Rail
```
LDO Output ──► Ferrite ──► Distribution
    │              │           │
    │         100nF ceramic    ├──► DSP VDD (×8 pins)
    │                         ├──► MCU VDD (×4 pins)
    │                         ├──► USB PHY (×2 pins)
    │                         └──► Flash VDD (×1 pin)
    │
  Trace width: 0.5mm (20mil)
  Current: 500mA total
  Via stitching: Every 5mm
```

#### 3.3V Analog Rail
```
LDO Output ──► LC Filter ──► Distribution
    │              │           │
    │         10μH + 10μF     ├──► ADC AVDD (×2 pins)
    │                         ├──► DAC AVDD (×2 pins)
    │                         └──► VREF buffer
    │
  Trace width: 0.3mm (12mil)
  Current: 200mA total
  Isolated pour: Yes
```

### Regülatör Detayları

#### LDO 1: AMS1117-3.3 (VDD_IO)
```
Specifications:
  Input: 4.5V - 12V
  Output: 3.3V ± 1%
  Current: 500mA max
  Dropout: 1.3V @ 800mA
  PSRR: 72dB @ 120Hz
  Package: SOT-223

External Components:
  Input: 22μF ceramic (X7R)
  Output: 22μF ceramic (X7R)
  Bypass: 100nF ceramic (0805)

Thermal:
  θJA: 90°C/W (SOT-223)
  Power dissipation: (12V - 3.3V) × 0.5A = 4.35W
  Tj = 25 + (4.35 × 90) = 416°C → FAIL
  
  Solution: Add thermal vias + copper pour
  With thermal: θJA ≈ 40°C/W
  Tj = 25 + (4.35 × 40) = 199°C → PASS (margin)
```

#### LDO 2: AP2112K-1.8 (VDD_CORE)
```
Specifications:
  Input: 2.5V - 5.5V
  Output: 1.8V ± 1%
  Current: 300mA max
  Dropout: 250mA @ 250mV
  PSRR: 70dB @ 100kHz
  Package: SOT-23-5

External Components:
  Input: 1μF ceramic (X7R)
  Output: 10μF ceramic (X5R)
  Bypass: 100nF ceramic (0402)

Thermal:
  θJA: 200°C/W (SOT-23)
  Power dissipation: (3.3V - 1.8V) × 0.3A = 0.45W
  Tj = 25 + (0.45 × 200) = 115°C → PASS
```

#### LDO 3: LP5907MFX-3.3 (VDD_ANA)
```
Specifications:
  Input: 2.2V - 5.5V
  Output: 3.3V ± 0.5%
  Current: 200mA max
  Dropout: 120mV @ 200mA
  PSRR: 85dB @ 1kHz (excellent for audio)
  Package: SOT-23-5

External Components:
  Input: 1μF ceramic (X7R)
  Output: 10μF ceramic (X5R)
  Noise: 4μVRMS (very low)

Application: ADC/DAC analog supply
  - Ultra-low noise for audio performance
  - High PSRR rejects digital noise
```

#### LDO 4: TPS7A4901/TPS7A4933 (±5V)
```
Specifications:
  +5V LDO:
    Input: 6V - 36V
    Output: 5.0V ± 1%
    Current: 150mA max
    PSRR: 68dB @ 100kHz
    
  -5V LDO (inverting):
    Input: 5V
    Output: -5.0V ± 1%
    Current: 150mA max
    Topology: Charge pump inverter

External Components:
  Input: 10μF ceramic
  Output: 10μF ceramic
  Charge pump: 1μF flyback cap

Application: OP-AMP dual supply
  - Clean ±5V for audio op-amps
```

### Copper Weight ve Trace Hesapları

#### Trace Current Capacity (IPC-2221)
```
Trace Width │ 1oz (35μm) │ 2oz (70μm)
────────────┼────────────┼───────────
0.2mm       │ 0.5A       │ 0.8A
0.3mm       │ 0.7A       │ 1.1A
0.5mm       │ 1.0A       │ 1.6A
1.0mm       │ 1.8A       │ 2.9A
2.0mm       │ 3.1A       │ 5.0A
```

#### Voltage Drop Hesabı
```
Formula: V_drop = I × R
R = (ρ × L) / (W × T)

ρ (copper) = 1.72 × 10⁻⁶ Ω·cm
T (1oz) = 35μm
T (2oz) = 70μm

Example: 1A through 0.5mm width, 50mm length, 1oz
R = (1.72e-6 × 5) / (0.05 × 0.0035) = 0.49Ω
V_drop = 1 × 0.49 = 0.49V (too high!)

Solution: Use power plane (L4) or wider trace
With plane: R ≈ 0.01Ω
V_drop = 1 × 0.01 = 0.01V (acceptable)
```

### Power Sequencing

#### Startup Sequence
```
Time    │ Event                    │ Voltage
────────┼──────────────────────────┼────────
T0      │ DC input applied         │ 12V
T0+50ms │ Bulk cap charged         │ 12V stable
T0+100ms│ Ferrite bead saturation  │ 12V filtered
T0+150ms│ LDO 3.3V enabled         │ 3.3V rising
T0+200ms│ LDO 3.3V stable          │ 3.3V OK
T0+250ms│ LDO 1.8V enabled         │ 1.8V rising
T0+300ms│ LDO 1.8V stable          │ 1.8V OK
T0+350ms│ LDO 3.3V_ANA enabled     │ 3.3V_A rising
T0+400ms│ LDO 3.3V_ANA stable      │ 3.3V_A OK
T0+450ms│ LDO ±5V enabled          │ ±5V rising
T0+500ms│ LDO ±5V stable           │ ±5V OK
T0+550ms│ DSP reset released       │ DSP booting
T0+1s   │ DSP ready                │ System ready
```

#### Sequencing Circuit
```
  3.3V ──────┬──────────────────┐
              │                  │
             [R]              ┌──┴──┐
              │              │Delay │
              ▼              │ RC   │
         ┌────────┐         └──┬──┘
         │Enable  │            │
         │ Logic  │◄───────────┘
         └────────┘
              │
              ▼
         1.8V Enable
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Net Classes:
  Class: VCC_3V3
    Track width: 0.5mm (min)
    Via: 0.3/0.6mm (drill/pad)
    Clearance: 0.2mm
    
  Class: VCC_1V8
    Track width: 0.3mm (min)
    Via: 0.2/0.5mm
    Clearance: 0.2mm
    
  Class: VCC_5V
    Track width: 0.5mm (min)
    Via: 0.3/0.6mm
    Clearance: 0.3mm
    
  Class: VCC_12V
    Track width: 1.0mm (min)
    Via: 0.4/0.8mm
    Clearance: 0.3mm

Copper Pour Settings:
  Pour type: Solid
  Net: VDD_IO, VDD_ANA, etc.
  Clearance: 0.3mm
  Min width: 0.25mm
  Thermal relief: 4 spoke, 0.25mm
```

### Altium Designer
```
Design > Rules > Width:
  Rule: POWER_TRACE
    Width: 20mil (0.5mm)
    Min: 15mil
    Max: 40mil
    Layer: All
    
  Rule: HIGH_CURRENT
    Width: 40mil (1.0mm)
    Min: 30mil
    Max: 80mil
    Layer: L1, L4, L6

Design > Rules > Plane:
  Rule: POWER_PLANE
    Connect Style: Relief
    Air Gap: 10mil
    Spoke Width: 10mil
    Spoke Count: 4

Design > Classes:
  Net Class: POWER_3V3
    Members: VDD_IO, VDD_ANA
    Width: 20mil
    
  Net Class: POWER_1V8
    Members: VDD_CORE
    Width: 15mil
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:6-layer-stackup | Reference | Katman yapısı |
| K19:thermal-vias | Detail | Termal yönetimi |
| K09 Güç Yönetimi | Input | Regülatör spesifikasyonları |
| K04 Donanım | Input | Bileşen seçimleri |

## Durum: Implementasyon

- [x] Güç mimarisi tanımlandı
- [x] LDO seçimleri yapıldı
- [x] PDN analizi hedefleri belirlendi
- [x] Bypass stratejisi yazıldı
- [ ] PDN impedance simülasyonu
- [ ] Thermal analiz (güç kayıpları)
- [ ] Prototype voltage ripple ölçümü
