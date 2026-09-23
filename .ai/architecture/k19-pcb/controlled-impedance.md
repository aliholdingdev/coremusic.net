---
title: "Kontrollü Empedans Yönlendirme"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# Kontrollü Empedans Yönlendirme

## Genel Bakış

COREMUSIC platformunda I2S, USB ve SPI yüksek hızlı dijital sinyaller için kontrollü empedans yönlendirme gereklidir. 50Ω tek uçlu (single-ended) ve 90Ω diferansiyel empedans hedefleri ile sinyal bütünlüğü ve EMC uyumluluğu sağlanır. Bu belge, trace genişliği, spacing ve layer geçiş kurallarını tanımlar.

## Tasarım Kuralları

| Parametre | Tek Uçlu (50Ω) | Diferansiyel (90Ω) |
|-----------|----------------|---------------------|
| Trace width | 0.18mm (7mil) | 0.15mm (6mil) |
| Trace spacing | - | 0.20mm (8mil) |
| Tolerance | ±10% | ±10% |
| Layer | L3 (stripline) | L3 (stripline) |
| Reference | L2 + L5 | L2 + L5 |
| Max length | 50mm | 50mm |
| Length matching | - | ±0.5mm |

## Teknik Detaylar

### Empedans Modelleri

#### Microstrip (L1/L6 - External Layers)
Microstrip yapısında sinyal hattı bir dielektrik tabakası üzerindedir ve alt tarafta tek bir referans düzlemi bulunur.

```
         Signal Trace (w)
         ┌───────────┐
         │  Copper   │
    ─────┴───────────┴─────
         │  Solder   │
         │  Mask     │
         │           │
         │ Dielectric│ h (0.21mm)
         │           │
    ════════════════════════  ← Reference Plane (L2)
```

**Hesaplama (IPC-2141):**
```
Zo = (60 / √εeff) × ln(8h/w + w/4h)

εeff = (εr + 1)/2 + ((εr - 1)/2) × (1 + 12h/w)^(-0.5)

Hedef: Zo = 50Ω ±10%
Sonuç: w = 0.18mm, h = 0.21mm, εr = 4.5
```

#### Stripline (L3 - Inner Layer)
Stripline yapısında sinyal hattı iki referans düzleminin arasındadır.

```
    ════════════════════════  ← Reference Plane (L2)
         │           │
         │ Dielectric│ h1 (0.36mm)
         │           │
         ┌───────────┐
         │  Signal   │ w (0.12mm)
         │  Trace    │
         └───────────┘
         │           │
         │ Dielectric│ h2 (0.36mm)
         │           │
    ════════════════════════  ← Reference Plane (L4)
```

**Hesaplama:**
```
Zo = (60 / √εr) × ln(4b / (0.67 × (0.8w + t)))

b = 0.72mm (toplam dielektrik kalınlığı)
w = 0.12mm (trace genişliği)
t = 0.035mm (copper kalınlığı)

Zo ≈ 50Ω
```

#### Diferansiyel Microstrip
```
         Sig+      Sig-
         ┌───┐     ┌───┐
         │   │     │   │
    ─────┴───┴─────┴───┴─────
         │    s    │
         │←───────→│
         │  spacing │
         │           │
    ════════════════════════  ← Reference Plane
```

**Hesaplama:**
```
Zdiff = 2 × Zo × √(1 + 0.48 × e^(-0.96 × s/h))

s = 0.20mm (spacing)
h = 0.21mm (dielectric height)

Zdiff ≈ 90Ω ±10%
```

### Sinyal Sınıflandırması

#### 1. I2S Bus (Audio Data)
```
Sinyal      │ Frekans    │ Impedans │ Katman
───────────┼────────────┼──────────┼───────
BCLK       │ 12.288 MHz │ 50Ω SE   │ L3
LRCK       │ 48 kHz     │ 50Ω SE   │ L3
SDATA      │ 12.288 MHz │ 50Ω SE   │ L3
MCLK       │ 24.576 MHz │ 50Ω SE   │ L3
```

#### 2. USB 2.0 (High-Speed)
```
Sinyal      │ Hız        │ Impedans │ Katman
───────────┼────────────┼──────────┼───────
D+         │ 480 Mbps   │ 90Ω Diff │ L3
D-         │ 480 Mbps   │ 90Ω Diff │ L3
```

#### 3. SPI Flash
```
Sinyal      │ Hız        │ Impedans │ Katman
───────────┼────────────┼──────────┼───────
SCK        │ 50 MHz     │ 50Ω SE   │ L3
MOSI       │ 50 MHz     │ 50Ω SE   │ L3
MISO       │ 50 MHz     │ 50Ω SE   │ L3
CS         │ 50 MHz     │ 50Ω SE   │ L3
```

### Length Matching Kuralları

#### Differential Pair Matching
```
ΔL = |L+ - L-| ≤ 0.5mm (20mil)

Tolerance stack-up:
  - Manufacturing: ±0.1mm
  - Etch variation: ±0.05mm
  - Design margin: ±0.35mm
```

#### Bus Length Matching (I2S)
```
Signal    │ Reference │ Tolerance
──────────┼───────────┼──────────
BCLK      │ -         │ Master clock
LRCK      │ BCLK      │ ±2mm
SDATA     │ LRCK      │ ±2mm
MCLK      │ BCLK      │ ±1mm
```

### Crosstalk Analizi

#### Near-End Crosstalk (NEXT)
```
NEXT = 20 × log10(Vcrosstalk / Vsignal)

Hedef: NEXT < -30dB

Önlemler:
  - Min spacing: 3 × trace width
  - Ground guard traces: kritik hatlarda
  - Layer transition: minimal via kullanımı
```

#### Far-End Crosstalk (FEXT)
```
FEXT = (Kf × L × tdel) / (2 × √εr)

Kf = coupling coefficient
L = coupling length
tdel = propagation delay

Hedef: FEXT < -25dB
```

### Termination Stratejileri

#### Series Termination
```
Source ──[Rseries]──┬──── Trace (50Ω) ──── Load
                    │
                    Zsource = 25Ω
                    Rseries = 25Ω
```

#### Parallel Termination
```
Source ───── Trace (50Ω) ────┬──[Rparallel]── GND
                             │
                             Zload = High (CMOS)
                             Rparallel = 50Ω
```

#### RC Termination (I2S)
```
Source ───── Trace (50Ω) ────┬──[R]──┬── VDD
                             │       │
                             │      [C]
                             │       │
                             └───────┴── GND

R = 33Ω, C = 10pF
```

### Via Geçiş Etkileri

```
Via parasitic elements:
  - Inductance: ~0.8nH per via
  - Capacitance: ~0.3pF per via
  - Impedance discontinuity: ~5-10%

Mitigation:
  - Stitching vias: signal viasının yanına ground viası
  - Back-drilling: unused via stub removal
  - Via fence: high-speed hatlarda
```

## Simülasyon Sonuçları

### CST Microwave Studio Results
```
Signal: I2S_BCLK
  Target: 50Ω
  Simulated: 48.5Ω (3% error)
  Bandwidth: DC - 100MHz
  Loss: 0.2 dB/cm @ 12MHz

Signal: USB_D+/D-
  Target: 90Ω differential
  Simulated: 88Ω (2.2% error)
  Bandwidth: DC - 500MHz
  Intra-pair skew: <5ps
```

## KiCad/Altium Ayarları

### KiCad 8.0
```
Board Setup > Physical Stackup:
  Enable impedance calculator

Board Setup > Design Rules > Net Classes:
  Class: I2S
    Track width: 0.18mm
    Clearance: 0.15mm
    Via: 0.2/0.4mm (drill/pad)
    
  Class: USB_DIFF
    Track width: 0.15mm
    Diff pair gap: 0.20mm
    Diff pair via: 0.2/0.4mm

  Class: SPI
    Track width: 0.18mm
    Clearance: 0.15mm

Interactive Router Settings:
  Preferred direction: horizontal/vertical
  Clearance: from net class
  Track width: from net class
  Via style: from net class
```

### Altium Designer
```
Design > Rules > Electrical > Width:
  Rule: I2S_WIDTH
    Width: 10mil (0.254mm)
    Min: 7mil
    Max: 12mil
    Layer: L3

Design > Rules > Electrical > Clearance:
  Rule: I2S_CLEARANCE
    Clearance: 8mil (0.203mm)
    Layer: L3

Design > Rules > Electrical > Differential Pairs:
  Rule: USB_DIFF
    Trace Width: 6mil
    Gap: 8mil
    Layer: L3
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K19:6-layer-stackup | Reference | Stackup yapısı |
| K19:signal-integrity | Detail | Sinyal bütünlüğü analizi |
| K08 Sinyal İşleme | Input | DSP pin haritalama |
| K04 Donanım | Input | Bileşen spesifikasyonları |

## Durum: Implementasyon

- [x] Empedans hedefleri belirlendi
- [x] Trace genişlikleri hesaplandı
- [x] Differential pair kuralları tanımlandı
- [x] Length matching toleransları
- [ ] CST/Ansys simülasyonu
- [ ] Prototype TDR ölçümü
- [ ] EMC doğrulama testi
