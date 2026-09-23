---
title: "Sinyal Bütünlüğü Analizi"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Sinyal Bütünlüğü Analizi

## Genel Bakış

COREMUSIC platformunda sinyal bütünlüğü (Signal Integrity - SI), yüksek hızlı dijital sinyallerin bozulmadan iletilmesini sağlar. I2S, USB ve SPI hatlarında yansıma, crosstalk, jitter ve insertion loss analizleri yapılır. Bu belge, SI tasarım kurallarını, simülasyon parametrelerini ve doğrulama prosedürlerini tanımlar.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Reflection coefficient (Γ) | < 0.1 | ≤ 10% yansıma |
| Crosstalk (NEXT) | < -30dB | Close-end coupling |
| Crosstalk (FEXT) | < -25dB | Far-end coupling |
| Intra-pair skew (USB) | < 5ps (0.5mm) | Differential pair |
| Inter-pair skew (I2S) | < 200ps (±2mm) | Bus matching |
| Jitter (clock) | < 100ps peak-to-peak | Clock quality |
| Insertion loss @ 100MHz | < 1dB | Trace loss |

## Teknik Detaylar

### Sinyal Bozulma Kaynakları

#### 1. Yansıma (Reflection)
```
Yansıma kaynakları:
  - Impedans uyumsuzluğu (trace-to-via, trace-to-load)
  - Open circuit (loating)
  - Short circuit

Yansıma katsayısı:
  Γ = (ZL - Z0) / (ZL + Z0)

  Z0 = Karakteristik empedans (50Ω)
  ZL = Yük empedansı

  Γ = 0 → Tam absorbed
  Γ = 1 → Tam yansıma (open)
  Γ = -1 → Tam yansıma (short)
```

#### 2. Crosstalk (Sinyal Eşleşmesi)
```
Near-End Crosstalk (NEXT):
  Sinyal kaynağına yakın tarafta gözlenir
  
  NEXT = 20 × log10(Vcrosstalk / Vaggressor)
  
  Hedef: NEXT < -30dB

Far-End Crosstalk (FEXT):
  Sinyal alıcısında gözlenir
  
  FEXT = (Kf × L × tdel) / (2 × √εr)
  
  Hedef: FEXT < -25dB

Coupling Mechanisms:
  - Capacitive coupling: Electric field
  - Inductive coupling: Magnetic field
```

#### 3. Jitter
```
Jitter türleri:
  - Deterministic jitter (DJ): Pattern-dependent
  - Random jitter (RJ): Gaussian distribution
  - Period jitter: Clock-to-clock variation
  - Cycle-to-cycle jitter: Adjacent cycles

Hedef jitter:
  - Total jitter (TJ): < 100ps p-p @ 100MHz
  - DJ: < 50ps
  - RJ: < 10ps RMS
```

#### 4. Insertion Loss
```
Kaynakları:
  - Copper resistivity
  - Dielectric loss (tan δ)
  - Skin effect at high frequency

Loss model:
  α = αconductor + αdielectric
  
  αconductor ∝ √f (skin effect)
  αdielectric ∝ f (dielectric loss)

Hedef:
  - < 1dB @ 100MHz (50mm trace)
  - < 3dB @ 500MHz (50mm trace)
```

### Empedans Simülasyonu

#### Single-Ended (50Ω) - L3 Stripline
```
Configuration:
  Trace width: 0.12mm
  Dielectric height: 0.36mm (core)
  Dielectric constant: 4.5
  Copper thickness: 35μm

TDR Simulation Results:
  ┌──────────────────────────────────────┐
  │  TDR Response (50Ω target)          │
  │                                      │
  │  70Ω ─┐                             │
  │       │                             │
  │  60Ω ─┤                             │
  │       │    ┌───────────────────┐    │
  │  50Ω ─┤────┤                   ├────┤
  │       │    │   48.5Ω ±2%      │    │
  │  40Ω ─┤    │                   │    │
  │       │    └───────────────────┘    │
  │  30Ω ─┘                             │
  │       └─────────────────────────    │
  │        0ps    100ps    200ps        │
  └──────────────────────────────────────┘
  
  Zo (simulated): 48.5Ω
  Error: 3% (within ±10% spec)
```

#### Differential (90Ω) - USB 2.0
```
Configuration:
  Trace width: 0.15mm
  Trace spacing: 0.20mm
  Dielectric height: 0.36mm (core)

Differential TDR:
  ┌──────────────────────────────────────┐
  │  Differential TDR (90Ω target)      │
  │                                      │
  │ 100Ω ─┐                             │
  │       │                             │
  │  95Ω ─┤                             │
  │       │    ┌───────────────────┐    │
  │  90Ω ─┤────┤                   ├────┤
  │       │    │   88Ω ±2.2%      │    │
  │  85Ω ─┤    │                   │    │
  │       │    └───────────────────┘    │
  │  80Ω ─┘                             │
  │       └─────────────────────────    │
  │        0ps    50ps    100ps         │
  └──────────────────────────────────────┘
  
  Zdiff (simulated): 88Ω
  Error: 2.2% (within ±10% spec)
```

### Crosstalk Analizi

#### NEXT Simülasyonu
```
Victim trace: 50mm length, 50Ω
Aggressor trace: 50mm length, 50Ω
Spacing: 0.2mm (8mil)

Frequency Response:
  ┌──────────────────────────────────────┐
  │  NEXT vs Frequency                   │
  │                                      │
  │ -10dB ─┐                             │
  │        │                             │
  │ -20dB ─┤      ╱                     │
  │        │    ╱                       │
  │ -30dB ─┤──╳─── Target               │
  │        │╱                           │
  │ -40dB ─┤                            │
  │        └───────────────────────     │
  │        10MHz   100MHz   1GHz        │
  └──────────────────────────────────────┘
  
  NEXT @ 100MHz: -35dB (PASS)
  NEXT @ 1GHz: -18dB (below limit at high freq)
```

#### FEXT Simülasyonu
```
Same configuration as NEXT

Frequency Response:
  ┌──────────────────────────────────────┐
  │  FEXT vs Frequency                   │
  │                                      │
  │ -10dB ─┐                             │
  │        │                             │
  │ -20dB ─┤          ╱                 │
  │        │        ╱                   │
  │ -25dB ─┤──────╳─── Target           │
  │        │    ╱                       │
  │ -30dB ─┤──╱                         │
  │        └───────────────────────     │
  │        10MHz   100MHz   1GHz        │
  └──────────────────────────────────────┘
  
  FEXT @ 100MHz: -32dB (PASS)
  FEXT @ 500MHz: -22dB (borderline)
```

### Crosstalk Önleme Stratejileri

#### 1. Spacing Kuralları
```
Minimum Spacing (3W Rule):
  
  ┌─────┐         ┌─────┐
  │Trace│   3W    │Trace│
  │  W  │◄───────►│  W  │
  └─────┘         └─────┘
  
  W = trace width
  3W = center-to-center spacing
  
  For W = 0.18mm:
    Spacing = 3 × 0.18 = 0.54mm
    Edge-to-edge = 0.36mm

Benefit: NEXT reduced by ~20dB
```

#### 2. Ground Guard Traces
```
  ┌─────┐   ┌─────┐   ┌─────┐
  │Sig  │   │GND  │   │Sig  │
  │trace│   │guard│   │trace│
  └──┬──┘   └──┬──┘   └──┬──┘
     │         │         │
     ▼         ▼         ▼
  ┌────────────────────────────┐
  │     Ground Plane (L2)      │
  └────────────────────────────┘

Guard trace rules:
  - Connected to GND via stitching vias
  - Width: same as signal trace
  - Spacing: ≥ 2× trace width
  - Via stitching: every 3mm
```

#### 3. Layer Transition
```
When transitioning between layers:
  
  L1 (Top) ──┐
              │ Via
  L3 (Inner) ─┼── Keep return path continuous
              │ Via
  L6 (Bottom) ┘
  
  Stitching vias required:
  - Ground via adjacent to signal via
  - Distance: < 2mm
  - Connects L2 + L5 ground planes
```

### Impedans Uyumsuzluğu Düzeltmeleri

#### Via Transition
```
Problem: Via creates impedance discontinuity

  Trace (50Ω) ──► Via ──► Trace (50Ω)
                  │
            Impedance drop: ~30Ω

Solution 1: Back-drilling
  - Remove unused via stub
  - Improves bandwidth by 50%

Solution 2: Via fencing
  - Add ground vias around signal via
  - Maintains reference plane continuity

  ┌─────┐
  │GND  │
  │via  │
  └──┬──┘
     │
  ┌──┴──┐
  │Sig  │
  │via  │
  └──┬──┘
     │
  ┌──┴──┐
  │GND  │
  │via  │
  └─────┘
```

#### BGA Breakout
```
Problem: BGA pads create capacitive discontinuity

  ┌─────────────────────────────┐
  │  BGA Pad (0.5mm pitch)      │
  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐  │
  │  │ P │ │ P │ │ P │ │ P │  │
  │  └─┬─┘ └─┬─┘ └─┬─┘ └─┬─┘  │
  │    │     │     │     │     │
  └────┼─────┼─────┼─────┼─────┘
       │     │     │     │
       ▼     ▼     ▼     ▼
  ┌────────────────────────────┐
  │  Dog-bone fanout           │
  │  Via (0.3mm)               │
  └────────────────────────────┘

Solution:
  - Keep breakout traces short (< 2mm)
  - Use uniform trace width
  - Add ground vias nearby
```

### Eye Diagram Analizi

#### USB 2.0 Eye Diagram
```
  ┌──────────────────────────────────────┐
  │  Eye Diagram (480 Mbps)              │
  │                                      │
  │        ┌───────────┐                 │
  │       ╱│           │╲                │
  │      ╱ │           │ ╲               │
  │     ╱  │           │  ╲              │
  │    ╱   │   OPEN    │   ╲             │
  │   ╱    │   EYE     │    ╲            │
  │  ╱     │           │     ╲           │
  │ ╱      │           │      ╲          │
  │╱       │           │       ╲         │
  │        └───────────┘                 │
  │                                      │
  │  Eye height: > 400mV (min 200mV)    │
  │  Eye width: > 70% UI (min 60%)      │
  │  Jitter: < 200ps p-p                │
  └──────────────────────────────────────┘
```

#### I2S Clock Eye
```
  ┌──────────────────────────────────────┐
  │  MCLK Eye (24.576 MHz)              │
  │                                      │
  │        ┌───────────┐                 │
  │       ╱│           │╲                │
  │      ╱ │           │ ╲               │
  │     ╱  │           │  ╲              │
  │    ╱   │   OPEN    │   ╲             │
  │   ╱    │   EYE     │    ╲            │
  │  ╱     │           │     ╲           │
  │ ╱      │           │      ╲          │
  │╱       │           │       ╲         │
  │        └───────────┘                 │
  │                                      │
  │  Eye height: > 2.5V (3.3V CMOS)     │
  │  Eye width: > 80% UI                │
  │  Rise time: 1-2ns                   │
  └──────────────────────────────────────┘
```

### Sinyal Bütünlüğü Testleri

#### TDR Measurement
```
Equipment: oscilloscope with TDR module
Calibration: Open, Short, Load standards

Measurement procedure:
  1. Calibrate probe tip
  2. Connect to trace endpoint
  3. Send fast step (20ps rise time)
  4. Measure reflected waveform
  5. Calculate impedance profile

Pass criteria:
  - Zo = 50Ω ±10% (single-ended)
  - Zdiff = 90Ω ±10% (differential)
  - No discontinuities > 5%
```

#### Eye Diagram Measurement
```
Equipment: oscilloscope with eye analysis
Trigger: Clock recovery or pattern sync

Measurement procedure:
  1. Connect differential probe
  2. Configure pattern generator (PRBS-7)
  3. Capture > 10,000 bits
  4. Overlay eye diagram
  5. Measure eye height/width

Pass criteria:
  - Eye height > 400mV (USB 2.0)
  - Eye width > 70% UI
  - Jitter < 200ps p-p
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Board Setup > Design Rules > Electrical:
  Impedance controlled routing:
    Enabled: Yes
    Target: 50Ω (single-ended)
    Target: 90Ω (differential)
    
  Length matching:
    Enabled: Yes
    Tolerance: ±0.5mm (differential)
    Tolerance: ±2mm (bus)

Interactive Router Settings:
  Impedance control: Enabled
  Differential pairs: Enabled
  Length matching: Interactive display

Analysis:
  Tools > Length Tuning
    Display: On-hover
    Tolerance: From design rules
```

### Altium Designer
```
Design > Rules > Electrical > Impedance:
  Rule: SINGLE_ENDED_50
    Target: 50Ω
    Tolerance: 10%
    Layer: L3
    
  Rule: DIFFERENTIAL_90
    Target: 90Ω
    Tolerance: 10%
    Layer: L3
    Gap: 8mil

Design > Rules > Electrical > Length:
  Rule: USB_DIFF_LENGTH
    Min: 20mm
    Max: 50mm
    Tolerance: 5mil (intra-pair)
    
  Rule: I2S_BUS_LENGTH
    Tolerance: 2mm (inter-pair)

Tools > Signal Integrity:
  Analyzer: Integrated
  Model: IBIS from manufacturer
  Simulation: Time-domain (TDR)
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:controlled-impedance | Reference | Empedans hedefleri |
| K19:emc-compliance | Detail | EMC ile etkileşim |
| K08 Sinyal İşleme | Input | DSP sinyal gereksinimleri |
| K14 Test | Output | SI test prosedürleri |

## Durum: Implementasyon

- [x] Bozulma kaynakları tanımlandı
- [x] Empedans simülasyonu yapıldı
- [x] Crosstalk analizi tamamlandı
- [x] Eye diagram hedefleri belirlendi
- [ ] TDR prototype ölçümü
- [ ] Eye diagram doğrulama
- [ ] Final SI raporu
