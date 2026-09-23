---
title: "Termal Via Yerleşimi ve Via-in-Pad"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Termal Via Yerleşimi ve Via-in-Pad

## Genel Bakış

COREMUSIC PCB tasarımında termal yönetimi kritik öneme sahiptir. Güç amplifikatörleri, DSP ve voltage regülatörleri yüksek ısı üretir. Termal via'lar bu ısıyı board'un iç katmanlarına ve alt yüzeyine ileterek sıcaklık dağılımını optimize eder. Via-in-pad teknolojisi ise BGA paketlerinde breakout ve termal yolları birleştirir.

## Tasarım Kuralları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Termal via çapı | 0.3mm (12mil) | Drill diameter |
| Via pad çapı | 0.6mm (24mil) | Annular ring: 0.15mm |
| Via aralığı | 1.0mm (40mil) | Merkezden merkeze |
| Via pattern | Grid/V-row | BGA pad altında |
| Copper filling | Epoxy filled | Via-in-pad için |
| Thermal relief | 4 spoke, 0.2mm | Through-hole pads |
| Min copper pour | 50% area | Thermal pad etrafında |

## Teknik Detaylar

### Termal Via Tipleri

#### 1. Through-Hole Termal Via
```
    ┌─────────────────────────────┐
    │         Top Copper          │
    │  ┌─────────────────────┐    │
    │  │   Thermal Pad       │    │
    │  │  ┌─┐ ┌─┐ ┌─┐ ┌─┐   │    │
    │  │  │V│ │V│ │V│ │V│   │    │  V = Via
    │  │  └─┘ └─┘ └─┘ └─┘   │    │
    │  └─────────────────────┘    │
    │            ││││             │
    │  ┌─────────┴┴┴┴───────────┐│
    │  │   Internal GND Plane   ││
    │  └────────────────────────┘│
    │            ││││             │
    │  ┌─────────┴┴┴┴───────────┐│
    │  │   Internal PWR Plane   ││
    │  └────────────────────────┘│
    │            ││││             │
    │  ┌─────────┴┴┴┴───────────┐│
    │  │   Bottom Copper        ││
    │  │   (heat spreading)     ││
    │  └────────────────────────┘│
    └─────────────────────────────┘
```

#### 2. Via-in-Pad (BGA Package)
```
    ┌─────────────────────────────┐
    │      BGA Pad (0.5mm)        │
    │  ┌─────────────────────┐    │
    │  │  ┌───────────────┐  │    │
    │  │  │ Filled Via     │  │    │
    │  │  │ (0.15mm drill) │  │    │
    │  │  └───────────────┘  │    │
    │  │  Solder mask opening│    │
    │  └─────────────────────┘    │
    │         │                   │
    │  ┌──────┴────────────────┐  │
    │  │  Inner Layer GND      │  │
    │  └───────────────────────┘  │
    └─────────────────────────────┘
```

### Termal Via Matris Tasarımı

#### BGA DSP (100-pin TQFP)
```
Thermal Pad Area: 8mm × 8mm
Via Size: 0.3mm drill, 0.6mm pad
Via Spacing: 1.0mm

Grid Pattern (8×8 = 64 vias):
┌──┬──┬──┬──┬──┬──┬──┬──┐
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
├──┼──┼──┼──┼──┼──┼──┼──┤
│V │V │V │V │V │V │V │V │
└──┴──┴──┴──┴──┴──┴──┴──┘

Termal direnç: ~25°C/W (64 via ile)
```

#### Güç Regülatör (TO-263)
```
Thermal Pad Area: 6mm × 4mm
Via Size: 0.3mm drill, 0.6mm pad
Via Spacing: 1.2mm

Pattern (4×3 = 12 vias):
┌──┬──┬──┐
│V │V │V │
├──┼──┼──┤
│V │V │V │
├──┼──┼──┤
│V │V │V │
├──┼──┼──┤
│V │V │V │
└──┴──┴──┘

Termal direnç: ~40°C/W (12 via ile)
```

### Termal Via Pattern Tipleri

#### V-Row Pattern
```
Used for: Linear IC packages (SOIC, TSSOP)

┌───┬───┬───┬───┬───┬───┬───┐
│ V │ V │ V │ V │ V │ V │ V │  ← Row 1
└───┴───┴───┴───┴───┴───┴───┘
      │   │   │   │   │
      ↓   ↓   ↓   ↓   ↓
    ┌───────────────────────┐
    │    Inner GND Plane    │
    └───────────────────────┘
```

#### Grid Pattern
```
Used for: BGA packages, QFN

┌───┬───┬───┬───┬───┐
│ V │ V │ V │ V │ V │  Row 1
├───┼───┼───┼───┼───┤
│ V │ V │ V │ V │ V │  Row 2
├───┼───┼───┼───┼───┤
│ V │ V │ V │ V │ V │  Row 3
├───┼───┼───┼───┼───┤
│ V │ V │ V │ V │ V │  Row 4
├───┼───┼───┼───┼───┤
│ V │ V │ V │ V │ V │  Row 5
└───┴───┴───┴───┴───┘
```

#### Radial Pattern
```
Used for: Power transistors, regulators

        V   V   V
      V   V   V   V
    V     V   V     V
   V      ┌───┐      V
  V       │IC │       V
   V      └───┘      V
    V     V   V     V
      V   V   V   V
        V   V   V
```

### Copper Pour Stratejisi

#### Thermal Pad Copper Pour
```
    ┌────────────────────────────────┐
    │  Copper Pour (2oz preferred)   │
    │  ┌──────────────────────────┐  │
    │  │                          │  │
    │  │    ┌────────────────┐    │  │
    │  │    │  Component     │    │  │
    │  │    │  Thermal Pad   │    │  │
    │  │    │    ┌─┐┌─┐     │    │  │
    │  │    │    │V││V│     │    │  │
    │  │    │    └─┘└─┘     │    │  │
    │  │    └────────────────┘    │  │
    │  │                          │  │
    │  └──────────────────────────┘  │
    │           │  │  │  │           │
    │    To internal GND planes      │
    └────────────────────────────────┘
```

### Fanout Stratejileri

#### BGA Fanout (0.5mm pitch)
```
    ┌─────────────────────────────────┐
    │         Top Layer               │
    │                                 │
    │  ○─┐   ○─┐   ○─┐   ○─┐        │
    │  │ │   │ │   │ │   │ │        │
    │  ○─┼───○─┼───○─┼───○─┼──       │
    │  │ │   │ │   │ │   │ │        │
    │  ○─┘   ○─┘   ○─┘   ○─┘        │
    │                                 │
    │  ○ = BGA pad                    │
    │  ┘ = Dog-bone fanout            │
    │  │ = Via (0.3mm)                │
    └─────────────────────────────────┘
```

#### QFN Exposed Pad Fanout
```
    ┌─────────────────────────────────┐
    │         Top Layer               │
    │                                 │
    │    ┌───────────────────┐        │
    │    │  ○  ○  ○  ○  ○   │        │
    │    │                   │        │
    │    │  ○  V  V  V  ○   │        │
    │    │                   │        │
    │    │  ○  V  V  V  ○   │        │
    │    │                   │        │
    │    │  ○  V  V  V  ○   │        │
    │    │                   │        │
    │    │  ○  ○  ○  ○  ○   │        │
    │    └───────────────────┘        │
    │                                 │
    │  ○ = Signal pad                 │
    │  V = Thermal via (filled)       │
    └─────────────────────────────────┘
```

## Simülasyon Verileri

### Termal Analiz (Ansys Icepak)
```
Component: DSP (TQFP-100)
  Power dissipation: 2.5W
  Ambient temperature: 25°C
  
Without thermal vias:
  θJA = 65°C/W
  Tj = 25 + (2.5 × 65) = 187.5°C (FAIL)

With 32 thermal vias (grid pattern):
  θJA = 35°C/W
  Tj = 25 + (2.5 × 35) = 112.5°C (PASS)

With 64 thermal vias (dense grid):
  θJA = 25°C/W
  Tj = 25 + (2.5 × 25) = 87.5°C (PASS, margin)
```

### Termal Direnç Karşılaştırması
```
Via Count │ θJA (°C/W) │ Tj Max (°C) │ Status
──────────┼────────────┼─────────────┼───────
0         │ 65         │ 187.5       │ FAIL
16        │ 45         │ 137.5       │ WARN
32        │ 35         │ 112.5       │ PASS
48        │ 28         │ 95.0        │ PASS
64        │ 25         │ 87.5        │ PASS+
```

## Üretim Notları

### Via-in-Pad İşlemi
```
Step 1: Via drilling (0.15mm drill)
Step 2: Copper plating (barrel)
Step 3: Epoxy filling (non-conductive)
Step 4: Capping (copper overfill)
Step 5: Planarization (flat surface)
Step 6: Surface finish (ENIG)

Cost impact: +15-25% per via
Lead time: +2-3 days
```

### Via Fill Malzemeleri
```
Type              │ Dk    │ Df    │ Thermal │ Use Case
──────────────────┼───────┼───────┼─────────┼──────────
Epoxy (non-cond)  │ 3.5   │ 0.02  │ 0.2     │ Standard
Silver-filled     │ 5.0   │ 0.01  │ 2.5     │ High thermal
Copper-filled     │ 4.0   │ 0.008 │ 3.8     │ Best thermal
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Footprint Editor:
  Via-in-Pad footprint:
    Pad type: SMD
    Shape: Circle
    Size: 0.6mm
    Drill: 0.3mm (via definition)
    Pad connection: solid (no thermal relief)

  Thermal via footprint:
    Pad type: Through-hole
    Shape: Circle
    Size: 0.6mm
    Drill: 0.3mm
    Clearance: 0.2mm to copper pour
    Thermal relief: 4 spoke, 0.2mm width

Copper Pour Settings:
  Min thermal relief spoke: 0.2mm
  Thermal relief connection: solid (for thermal vias)
  Clearance to via pad: 0.15mm
```

### Altium Designer
```
Pad Properties (Via-in-Pad):
  Pad Stack: Multi-layer
  Size: 24mil (0.6mm)
  Drill: 12mil (0.3mm)
  Plating: Yes
  Cap: Yes (via-in-pad)
  
Thermal Relief Settings:
  Relief Air Gap: 8mil (0.2mm)
  Relief Spoke Width: 8mil (0.2mm)
  Spoke Count: 4
  Connection: Direct (for thermal vias)

Design Rules > Manufacturing:
  Via-in-pad: Enabled
  Via fill: Epoxy
  Via cap: Yes
  Planarization: Required
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:index | Reference | Genel PCB tasarım referansı |
| K19:6-layer-stackup | Reference | Katman yapısı |
| K19:power-distribution | Detail | Güç dağıtımı |
| K04 Donanım | Input | Termal gereksinimler |
| K20 Mekanik | Constraint | Card outline |

## Durum: Implementasyon

- [x] Via boyutları belirlendi
- [x] Termal via pattern'leri tanımlandı
- [x] Via-in-pad prosedürü yazıldı
- [x] Simülasyon verileri mevcut
- [ ] Footprint kütüphanesi güncellendi
- [ ] Prototype termal test
- [ ] Production rule check
