---
title: "K19 PCB Tasarım Katmanı - Genel Bakış"
layer: K19
category: "PCB Tasarım"
date: 2026-09-20
version: "1.0.0"
status: "active"
---

# K19 PCB Tasarım Katmanı - Genel Bakış

## Genel Bakış

K19 PCB Tasarım katmanı, COREMUSIC platformunun donanım bileşenlerinin fiziksel kart tasarımından sorumudur. 6 katmanlı (6-layer) PCB mimarisi, yüksek hızlı ses sinyalleri ve hassas analog devreler için optimizedir. Katman, EMC uyumluluğu, sinyal bütünlüğü ve termal yönetim konularında endüstri standartlarına uygun tasarımlar sağlar.

## PCB Stackup Diyagramı

```
┌─────────────────────────────────────────────────────┐
│                  Solder Mask (Top)                   │
├─────────────────────────────────────────────────────┤
│  L1: Signal + Components (Top Layer)                 │
│  ► Component pads, signal routing, copper pours      │
├─────────────────────────────────────────────────────┤
│  L2: Ground Plane (Inner 1)                          │
│  ► Continuous ground reference, low impedance        │
├─────────────────────────────────────────────────────┤
│  L3: Signal Routing (Inner 2)                        │
│  ► High-speed signals, differential pairs            │
├─────────────────────────────────────────────────────┤
│  L4: Power Plane (Inner 3)                           │
│  ► VDD_IO, VDD_ADC, VDD_DAC, VDD_ANA planes        │
├─────────────────────────────────────────────────────┤
│  L5: Ground Plane (Inner 4)                          │
│  ► Secondary ground reference, return paths           │
├─────────────────────────────────────────────────────┤
│  L6: Signal + Components (Bottom Layer)              │
│  ► SMD components, additional routing                │
├─────────────────────────────────────────────────────┤
│                  Solder Mask (Bottom)                │
└─────────────────────────────────────────────────────┘
```

## Tasarım Kuralları

| Kural | Değer | Açıklama |
|-------|-------|----------|
| Min trace width | 0.1mm (4mil) | Genel sinyal rotasyonu |
| Power trace width | 0.3-1.0mm | Akım kapasitesine göre |
| Min trace spacing | 0.1mm (4mil) | Genel kurallar |
| High-speed spacing | 0.2mm (8mil) | Impedans kontrollü hatlar |
| Min drill diameter | 0.2mm (8mil) | Vias için |
| Min annular ring | 0.15mm | Via güvenilirliği |
| Min copper to edge | 0.3mm | Board edge clearance |
| Solder mask dam | 0.075mm | Pad arası mask |

## Mimari Bileşenler

### Analog Ses Devresi
```
ADC (PCM1808) ──► I2S Bus ──► DSP/ARM
DAC (PCM5102A) ◄── I2S Bus ◄── DSP/ARM
OPA2134 ──► Low-pass Filter ──► Line Out
```

### Güç Dağıtımı
```
DC 12V Input
    ├──► LDO 3.3V (VDD_IO)
    ├──► LDO 1.8V (VDD_CORE)
    ├──► LDO 3.3V (VDD_ADC/DAC Analog)
    └──► LDO ±5V (OP Amp Supply)
```

###clock Mimarisi
```
TCXO 24.576MHz ──► PLL Multiplier
                      ├──► 49.152MHz (MCLK)
                      ├──► 12.288MHz (BCLK)
                      └──► 48kHz (LRCK)
```

## Teknik Detaylar

### Sinyal Sınıflandırması

PCB üzerindeki sinyaller üç ana kategoride sınıflandırılır:

**1. Yüksek Hızlı Dijital Sinyaller**
- I2S Bus: BCLK (12.288 MHz), LRCK (48 kHz), SDATA
- SPI Flash:高达50 MHz clock
- USB 2.0: 480 Mbps differential
- GPIO expander communication

**2. Hassas Analog Sinyaller**
- Microphone preamp output
- Line-level audio input/output
- Reference voltage (VREF)
- ADC/DAC analog supply decoupling

**3. Güç Hatları**
- Main supply rail (12V)
- Regulated rails (3.3V, 1.8V)
- Analog supply (3.3V_ANA)
- Op-amp dual supply (±5V)

### Tabaka Bağımlılıkları

| Sinyal Tipi | Kaynak Katman | Hedef Katman | Not |
|-------------|---------------|--------------|-----|
| I2S Audio | L1 (Top) | L3 (Inner 2) | Ground reference: L2 |
| USB 2.0 | L1 (Top) | L3 (Inner 2) | Differential pair |
| Power (3.3V) | L4 (Inner 3) | L1/L6 | Via stitching |
| Ground | L2 (Inner 1) | L5 (Inner 4) | Via stitching |

## KiCad/Altium Ayarları

### KiCad 8.0
```
Board Setup > Stackup:
  Layers: 6
  Dielectric 1: FR-4 1.6mm (1.0mm core)
  Copper 1: 35μm (1oz) - Signal
  Copper 2: 35μm (1oz) - Ground
  Copper 3: 35μm (1oz) - Signal
  Copper 4: 35μm (1oz) - Power
  Copper 5: 35μm (1oz) - Ground
  Copper 6: 35μm (1oz) - Signal

Board Setup > Design Rules:
  Min clearance: 0.1mm
  Min trace width: 0.1mm
  Via drill: 0.2mm
  Via diameter: 0.4mm
```

### Altium Designer
```
Layer Stack Manager:
  Layer Count: 6
  Top Layer: 1oz Copper
  GND1: 1oz Copper
  SIG1: 1oz Copper
  PWR1: 1oz Copper
  GND2: 1oz Copper
  Bottom Layer: 1oz Copper

Design Rules:
  Clearance: 4mil
  Width: 4mil (min)
  Via: 8/16mil (min)
```

## Bağımlılıklar

### Üst Katman Bağımlılıkları (K19 → Diğer)
| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K08 Sinyal İşleme | Fiziksel | DSP ve codec entegrasyonu |
| K09 Güç Yönetimi | Fiziksel | Güç devresi bileşenleri |
| K14 Test | Fiziksel | Test point yerleşimi |
| K20 Mekanik | Fiziksel | Kart mekanik sınırları |

### Alt Katman Bağımlılıkları (Diğer → K19)
| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K04 Donanım | Elektriksel | Bileşen seçimi ve spesifikasyonlar |
| K10 Uygulama | Yazılım | GPIO ve interface haritalama |
| K13 CI/CD | Üretim | PCB üretim dosyaları |

## Durum: Implementasyon

- [x] 6-layer stackup tasarımı
- [x] Sinyal sınıflandırması
- [x] Güç dağıtımı planı
- [x] EMC önlemleri
- [ ] Detaya routed design
- [ ] DRC/DRC checks
- [ ] Prototype doğrulama
- [ ] EMC sertifikasyon testi
