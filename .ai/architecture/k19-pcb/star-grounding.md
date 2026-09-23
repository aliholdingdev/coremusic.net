---
title: "Star Topolojisi ve Ground Plane Bölme"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Star Topolojisi ve Ground Plane Bölme

## Genel Bakış

COREMUSIC audio platformunda analog ve dijital devrelerin bir arada bulunması, toprak döngüsü (ground loop) ve gürültü kepçeleme (noise coupling) sorunlarını beraberinde getirir. Star grounding topolojisi ile her devre grubu tek bir noktadan toprağa bağlanarak toprak geri besleme yolları izole edilir. Bu belge, ground plane splitting stratejilerini ve EMC etkilerini tanımlar.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Star point konumu | Tek nokta | Board ortası veya PSU yakını |
| Analog GND | Ayrı plane (L2) | Signal integrity için |
| Digital GND | Ayrı plane (L5) | Return path için |
| Single-point connection | Tek via | L2-L5 arası star point |
| Analog-Digital clearance | 2mm minimum | EMC için |
| Ferrite bead | 600Ω @ 100MHz | Star point'e seri |

## Teknik Detaylar

### Ground Plane Bölümleri

```
┌────────────────────────────────────────────────────────────────┐
│                      Top Layer (L1)                            │
│  ┌──────────────┐      ┌──────────────┐      ┌──────────────┐  │
│  │   Analog     │      │   Digital    │      │   Power      │  │
│  │   Section    │      │   Section    │      │   Section    │  │
│  │              │      │              │      │              │  │
│  │  ADC, DAC    │      │  DSP, MCU    │      │  Regulators  │  │
│  │  OP-AMP      │      │  Flash       │      │  Drivers     │  │
│  │  Ref Volt    │      │  USB         │      │  Connectors  │  │
│  └──────┬───────┘      └──────┬───────┘      └──────┬───────┘  │
│         │                     │                     │          │
└─────────┼─────────────────────┼─────────────────────┼──────────┘
          │                     │                     │
          ▼                     ▼                     ▼
┌────────────────────────────────────────────────────────────────┐
│                    Ground Plane (L2) - Analog                  │
│  ┌────────────────────────────────────────────────────────┐    │
│  │                    AGND Plane                          │    │
│  │         (Continuous copper pour)                       │    │
│  └───────────────────────┬────────────────────────────────┘    │
│                          │                                     │
│                     ★ STAR POINT                               │
│                          │                                     │
├──────────────────────────┼─────────────────────────────────────┤
│                    Ground Plane (L5) - Digital                 │
│  ┌────────────────────────────────────────────────────────┐    │
│  │                    DGND Plane                          │    │
│  │         (Continuous copper pour)                       │    │
│  └───────────────────────┬────────────────────────────────┘    │
│                          │                                     │
│                     Ferrite Bead                               │
│                          │                                     │
│                     To PSU GND                                 │
└────────────────────────────────────────────────────────────────┘
```

### Star Point Topolojisi

#### Tek Nokta Bağlantısı
```
                    Power Supply GND
                          │
                          │
                    ┌─────┴─────┐
                    │  Ferrite  │
                    │   Bead    │
                    │  600Ω     │
                    └─────┬─────┘
                          │
                          │
                    ★ STAR POINT
                    │     │     │
                    │     │     │
              ┌─────┘     │     └─────┐
              │           │           │
              ▼           ▼           ▼
         ┌─────────┐ ┌─────────┐ ┌─────────┐
         │  AGND   │ │  DGND   │ │  PGND   │
         │ (Analog)│ │(Digital)│ │ (Power) │
         └─────────┘ └─────────┘ └─────────┘
```

#### Multi-Point Star (Geniş Board'lar)
```
                    Power Supply GND
                          │
                          ▼
                    ★ PRIMARY STAR
                    │     │     │
                    │     │     │
              ┌─────┘     │     └─────┐
              │           │           │
              ▼           ▼           ▼
         ┌─────────┐ ┌─────────┐ ┌─────────┐
         │  AGND   │ │  DGND   │ │  PGND   │
         │         │ │         │ │         │
         │  ★──┐   │ │  ★──┐   │ │  ★──┐   │
         │     │   │ │     │   │ │     │   │
         │   ┌─┘   │ │   ┌─┘   │ │   ┌─┘   │
         │   │     │ │   │     │ │   │     │
         │   ▼     │ │   ▼     │ │   ▼     │
         │ Sub-    │ │ Sub-    │ │ Sub-    │
         │ star    │ │ star    │ │ star    │
         └─────────┘ └─────────┘ └─────────┘
```

### Ground Plane Bölümleme Stratejisi

#### Bölge 1: Analog GND (L2)
```
┌────────────────────────────────────────────┐
│              AGND PLANE                     │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  ADC Section (PCM1808)               │  │
│  │  - AVDD decoupling                   │  │
│  │  - Reference voltage                 │  │
│  │  - Input filtering                   │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  DAC Section (PCM5102A)              │  │
│  │  - AVDD decoupling                   │  │
│  │  - Output filtering                  │  │
│  │  - I2S interface                     │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  Op-Amp Section (OPA2134)            │  │
│  │  - ±5V supply decoupling             │  │
│  │  - Feedback ground                   │  │
│  │  - Output stage                      │  │
│  └──────────────────────────────────────┘  │
│                                            │
└────────────────────────────────────────────┘
```

#### Bölge 2: Digital GND (L5)
```
┌────────────────────────────────────────────┐
│              DGND PLANE                     │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  DSP Section                         │  │
│  │  - VDD_CORE decoupling               │  │
│  │  - VDD_IO decoupling                 │  │
│  │  - Clock circuitry                   │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  MCU Section                         │  │
│  │  - VDD decoupling                    │  │
│  │  - Debug interface                   │  │
│  │  - GPIO expansion                    │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  Communication Section               │  │
│  │  - USB interface                     │  │
│  │  - SPI Flash                         │  │
│  │  - I2C expander                      │  │
│  └──────────────────────────────────────┘  │
│                                            │
└────────────────────────────────────────────┘
```

#### Bölge 3: Power GND (L4-L5)
```
┌────────────────────────────────────────────┐
│              PGND PLANE                     │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  Voltage Regulators                  │  │
│  │  - LDO 3.3V (VDD_IO)                │  │
│  │  - LDO 1.8V (VDD_CORE)              │  │
│  │  - LDO 3.3V (VDD_ANA)               │  │
│  │  - LDO ±5V (OP-AMP)                 │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  Input Protection                    │  │
│  │  - ESD protection                    │  │
│  │  - Reverse polarity                  │  │
│  │  - Input filtering                   │  │
│  └──────────────────────────────────────┘  │
│                                            │
│  ┌──────────────────────────────────────┐  │
│  │  Output Stage                        │  │
│  │  - Power MOSFETs                     │  │
│  │  - Driver circuits                   │  │
│  │  - Protection circuits               │  │
│  └──────────────────────────────────────┘  │
│                                            │
└────────────────────────────────────────────┘
```

### Ferrite Bead Seçimi

#### Star Point Ferrite
```
Parametre         │ Değer
──────────────────┼─────────────
Impedans @ 100MHz │ 600Ω
DC Resistance     │ 0.05Ω max
Rated Current     │ 500mA
Package           │ 1206 (3216 metric)
Model             │ Murata BLM31PG601
```

#### Analog Supply Ferrite
```
Parametre         │ Değer
──────────────────┼─────────────
Impedans @ 100MHz │ 120Ω
DC Resistance     │ 0.02Ω max
Rated Current     │ 1A
Package           │ 1206 (3216 metric)
Model             │ Murata BLM31AG121
```

### Ground Loop Önleme

#### Problem: Toprak Döngüsü
```
      PSU GND ◄──────────────────┐
         │                       │
         │                       │
    ┌────┴────┐             ┌────┴────┐
    │   GND   │◄────────────│   GND   │
    │  (AGND) │             │  (DGND) │
    └────┬────┘             └────┬────┘
         │                       │
         │    Gürültü akımı      │
         └───────────────────────┘
              (istenmeyen)
```

#### Çözüm: Star Grounding
```
      PSU GND
         │
    ┌────┴────┐
    │ Ferrite │
    │  Bead   │
    └────┬────┘
         │
    ★ Star Point
    │         │
    ▼         ▼
 ┌─────┐   ┌─────┐
 │ AGND│   │DGND │
 └─────┘   └─────┘
    │         │
    │    Toprak döngüsü
    │    yok (izole)
    │         │
    └────┬────┘
         │
      Tek nokta
      bağlantı
```

### Toprak Plane Bölümleme Kuralları

#### 1. Keskin Bölüm (Split Plane)
```
┌──────────────────────────────────────┐
│  AGND         │         DGND         │
│               │                      │
│  Analog       │  Digital             │
│  components   │  components          │
│               │                      │
└──────────────────────────────────────┘
         │
    Clearance: 2mm
    No copper跨越
```

#### 2. Yumuşak Bölüm (Soft Split)
```
┌──────────────────────────────────────┐
│  AGND     ···  DGND                  │
│           ···                       │
│  Analog   ···  Digital              │
│           ···                       │
│  components ··· components          │
│           ···                       │
└──────────────────────────────────────┘
         │
    Hatched copper pour
    Gradual transition
```

#### 3. Moat (Hendek)
```
┌──────────────────────────────────────┐
│  AGND  ═══════════════  DGND        │
│        │ Moat │                     │
│  Analog│2mm   │ Digital             │
│        │      │                     │
└──────────────────────────────────────┘
         │
    No copper in moat
    Bridge at star point only
```

### EMC Etki Analizi

#### Toprak Plane Bütünlüğü
```
Senaryo                 │ EMC Etkisi │ SNR Etkisi
────────────────────────┼────────────┼───────────
Continuous plane        │ İyi        │ İyi
Split plane (temiz)     │ Orta       │ İyi
Split plane (bozuk)     │ Kötü       │ Kötü
No plane                │ Çok kötü   │ Çok kötü
```

#### Return Path Analizi
```
High-speed signal return current:
  
  Continuous GND plane:
    Path: Directly under trace
    Impedance: Low
    EMI: Low
  
  Split GND plane:
    Path: Must go around split
    Impedance: High
    EMI: High
  
  Solution: Keep return path continuous
            Use via stitching at splits
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Board Setup > Net Classes:
  Class: ANALOG_GND
    Net: AGND
    Clearance: 0.3mm (to DGND)
    
  Class: DIGITAL_GND
    Net: DGND
    Clearance: 0.3mm (to AGND)

Copper Pour Settings:
  Pour type: Solid
  Clearance: 0.3mm
  Min thickness: 0.25mm
  Thermal relief: Yes (4 spoke)
  Spoke width: 0.25mm
  Air gap: 0.25mm

Net Classes Assignment:
  Select pour → Properties → Net: AGND or DGND
```

### Altium Designer
```
Design > Rules > Routing > Routing Style:
  Rule: ANALOG_GND
    Style: Pour
    Net: AGND
    Pour clearance: 12mil (0.3mm)
    
  Rule: DIGITAL_GND
    Style: Pour
    Net: DGND
    Pour clearance: 12mil (0.3mm)

Design > Rules > Manufacturing > Copper Pour:
  Pour type: Solid
  Min primitive width: 10mil
  Thermal relief:
    Air gap: 10mil
    Spoke width: 10mil
    Spoke count: 4

Tools > Polygon Pours > Pour Manager:
  Order: AGND first, then DGND
  Net: Assign from netlist
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:6-layer-stackup | Reference | Katman yapısı |
| K19:emc-compliance | Detail | EMC uyumluluğu |
| K09 Güç Yönetimi | Input | Güç devresi topolojisi |
| K08 Sinyal İşleme | Input | Sinyal gürültüsü gereksinimleri |

## Durum: Implementasyon

- [x] Star point topolojisi tanımlandı
- [x] Ground plane bölgelere ayrıldı
- [x] Ferrite bead seçimi yapıldı
- [x] Toprak döngüsü önleme stratejisi
- [ ] Simülasyon (gürültü analizi)
- [ ] Prototype SNR ölçümü
- [ ] EMC testi
