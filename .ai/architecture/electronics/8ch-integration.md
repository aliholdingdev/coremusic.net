---
type: electronic
category: 8ch-integration
title: "CoreMusic — 8-Channel Class AB Amplifier Integration"
date: 2026-09-18
updated: 2026-09-18
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/8ch-integration.md"
  source_of_truth:
    - ".ai/architecture/amplifier-classab-circuit.md"
    - ".ai/architecture/power-supply-classab.md"
    - ".ai/architecture/thermal-design-classab.md"
    - ".ai/architecture/bom-classab.md"
    - ".ai/architecture/pcb-classab.md"
    - ".ai/brain.md"
    - "ADR-061-electronics-architecture"
  related:
    - ".ai/architecture/test-fixture.md"
    - ".ai/architecture/test-protocol.md"
---

# CoreMusic — 8-Channel Class AB Amplifier Integration

**Zorunlu Bağlantılar:** [[amplifier-classab-circuit]] · [[power-supply-classab]] · [[thermal-design-classab]] · [[bom-classab]] · [[pcb-classab]] · [[brain.md]]

---

## 1. Sistem Spesifikasyonları

| Parametre | Değer | Not |
|-----------|-------|-----|
| Toplam Kanal | 8 (7.1 surround) | Class AB Darlington |
| Kanal Başına Güç | 50W RMS @ 8Ω | ±35V DC besleme |
| Toplam Çıkış Gücü | 400W | 8 × 50W |
| Toplam Sistem Gücü | 728W | 8 × 91W (ISIP dahil) |
| Verimlilik | %55 tam yükte | ISIP dahil |
| Besleme | ±35V DC | 4× LM5122 interleaved boost |
| Giriş Hassasiyeti | 1.2V RMS | Tam çıkış için |
| Giriş Empedansı | 47kΩ | Non-inverting |
| Çıkış Empedansı | <0.08Ω | Damping factor >100 |
| Çalışma Sıcaklığı | 0°C – 50°C | Ortam |
| Koruma | Overcurrent, Thermal, DC Offset, Speaker Relay | 8 kanal bağımsız |
| MCU Telemetri | STM32F103 / RP2040 | I2C/UART durum izleme |
| Toplam Boyut | 400 × 300 × 100mm | Standart amplifikatör şasesi |
| Ağırlık | ~4.5kg | Tahmini (PCB + heatsink + şase) |

---

## 2. Sistem Mimari Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    8 KANAL CLASS AB SİSTEMİ                             │
│                    Boyut: 400×300×100mm                                 │
│                                                                         │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │  GÜÇ KAYNAĞI KARTI (320×100mm)                                  │  │
│  │                                                                   │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐        │  │
│  │  │ LM5122   │  │ LM5122   │  │ LM5122   │  │ LM5122   │        │  │
│  │  │ +35V Ch1 │  │ +35V Ch2 │  │ -35V Ch1 │  │ -35V Ch2 │        │  │
│  │  │ 180°     │  │ 180°     │  │ 180°     │  │ 180°     │        │  │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘        │  │
│  │                                                                   │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌────────────────────────┐ │  │
│  │  │ 4× XAL6060   │  │ 8× SS36     │  │ 8× 4700µF/50V Bulk    │ │  │
│  │  │ 4.7µH/30A    │  │ Schottky    │  │ + 8× 100nF MLCC       │ │  │
│  │  └──────────────┘  └──────────────┘  └────────────────────────┘ │  │
│  │                                                                   │  │
│  │  ┌────────────────────────────────────────────────────────────┐  │  │
│  │  │ Koruma: LM74670 OR-ing | NTC 5Ω+Relay soft-start         │  │  │
│  │  │         LM393 UVP/OVP   | P6KE36A TVS                    │  │  │
│  │  └────────────────────────────────────────────────────────────┘  │  │
│  │                                                                   │  │
│  │  Çıkış: +35V bus (15A) ──── ve ──── -35V bus (15A)             │  │
│  └───────────────────────────┬───────────────────────────────────────┘  │
│                              │                                          │
│  ┌───────────────────────────▼───────────────────────────────────────┐  │
│  │  GÜÇ DAĞITIM KARTI (320×60mm)                                   │  │
│  │                                                                   │  │
│  │  +35V ──┬──┬──┬──┬──┬──┬──┬──┬── Fuse Bank (8× 3A PTC)         │  │
│  │         │  │  │  │  │  │  │  │                                   │  │
│  │  -35V ──┬──┬──┬──┬──┬──┬──┬──┬── Fuse Bank (8× 3A PTC)         │  │
│  │         │  │  │  │  │  │  │  │                                   │  │
│  │  ┌──────┴──┴──┴──┴──┴──┴──┴──┴──────────────────────────────┐   │  │
│  │  │  8× IRLZ44N MOSFET Enable Switch                          │   │  │
│  │  │  8× LED (Green=Active, Red=Fault)                          │   │  │
│  │  │  8× Phoenix Contact 4pin (32009147: +V, -V, GND, EN)      │   │  │
│  │  └──────┬──┬──┬──┬──┬──┬──┬──┬──────────────────────────────┘   │  │
│  │         │  │  │  │  │  │  │  │                                   │  │
│  │  Giriş: 2× Molex Mini-Fit Jr (8pin, ±35V bus)                  │  │
│  └─────────┼──┼──┼──┼──┼──┼──┼──┼──────────────────────────────────┘  │
│            │  │  │  │  │  │  │  │                                      │
│  ┌─────────▼──▼──▼──▼──▼──▼──▼──▼──────────────────────────────────┐  │
│  │  AMPLİFİKATÖR KARTLARI (8× 200×100mm)                           │  │
│  │                                                                   │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐               │  │
│  │  │  CH 1   │ │  CH 2   │ │  CH 3   │ │  CH 4   │               │  │
│  │  │ MJL21194│ │ MJL21194│ │ MJL21194│ │ MJL21194│               │  │
│  │  │ MJL21193│ │ MJL21193│ │ MJL21193│ │ MJL21193│               │  │
│  │  │ BD139/140│ │ BD139/140│ │ BD139/140│ │ BD139/140│             │  │
│  │  │ KSC3503 │ │ KSC3503 │ │ KSC3503 │ │ KSC3503 │               │  │
│  │  │ BC546B×2│ │ BC546B×2│ │ BC546B×2│ │ BC546B×2│               │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘               │  │
│  │                                                                   │  │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐               │  │
│  │  │  CH 5   │ │  CH 6   │ │  CH 7   │ │  CH 8   │               │  │
│  │  │ MJL21194│ │ MJL21194│ │ MJL21194│ │ MJL21194│               │  │
│  │  │ MJL21193│ │ MJL21193│ │ MJL21193│ │ MJL21193│               │  │
│  │  │ BD139/140│ │ BD139/140│ │ BD139/140│ │ BD139/140│             │  │
│  │  │ KSC3503 │ │ KSC3503 │ │ KSC3503 │ │ KSC3503 │               │  │
│  │  │ BC546B×2│ │ BC546B×2│ │ BC546B×2│ │ BC546B×2│               │  │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘               │  │
│  │                                                                   │  │
│  │  Her kart: 6-layer PCB, Fischer SK82-150-SA heatsink             │  │
│  │            Noctua NF-A8 PWM fan (2200 RPM max)                   │  │
│  │            Berger-Werk TGP 4500 termal pad (0.25°C/W)            │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │  KORUMA KARTI (320×60mm)                                         │  │
│  │                                                                   │  │
│  │  ┌──────────────────────────────────────────────────────────┐    │  │
│  │  │ 8× Overcurrent Sense: 0.47Ω/5W + LM393 comparator       │    │  │
│  │  │ 8× Thermal Sense: KSD301 85°C snap-action                │    │  │
│  │  │ 8× Speaker Relay: DPDT 5A (Panasonic AGQ200)             │    │  │
│  │  │ 8× DC Offset Detection: LM393 (threshold: ±50mV)         │    │  │
│  │  │ Master Enable/Disable + Soft-start Sequencer              │    │  │
│  │  └──────────────────────────────────────────────────────────┘    │  │
│  │                                                                   │  │
│  │  Bağlantı: Her kanal için 2× 4pin ribbon cable (signal+power)   │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │  MCU TELEMETRI KARTI (80×60mm)                                  │  │
│  │                                                                   │  │
│  │  ┌──────────────────────────────────────────────────────────┐    │  │
│  │  │ MCU: STM32F103C8T6 veya RP2040                           │    │  │
│  │  │                                                           │    │  │
│  │  │ I2C Bus (400kHz):                                        │    │  │
│  │  │ ├── INA219 ×4 (güç ölçümü: ±35V bus, kanal güçleri)    │    │  │
│  │  │ ├── TMP102 ×4 (sıcaklık: heatsink hotspot)              │    │  │
│  │  │ └── ADS1115 ×2 (analog: DC offset, overcurrent sense)   │    │  │
│  │  │                                                           │    │  │
│  │  │ UART (115200 baud):                                       │    │  │
│  │  │ └── ESP32-C3 bridge → WiFi telemetry dashboard           │    │  │
│  │  │                                                           │    │  │
│  │  │ GPIO:                                                     │    │  │
│  │  │ ├── 8× relay control output                               │    │  │
│  │  │ ├── 8× MOSFET enable output                               │    │  │
│  │  │ ├── 8× fan PWM output (Noctua NF-A8)                     │    │  │
│  │  │ ├── 4× status LED (power, fault, thermal, clip)           │    │  │
│  │  │ ├── 1× master enable input                                │    │  │
│  │  │ └── 1× emergency shutdown input                           │    │  │
│  │  │                                                           │    │  │
│  │  │ ADC (12-bit):                                             │    │  │
│  │  │ ├── 8× output DC offset sense                             │    │  │
│  │  │ ├── 8× speaker current sense                              │    │  │
│  │  │ └── 2× supply voltage sense (+35V, -35V)                 │    │  │
│  │  └──────────────────────────────────────────────────────────┘    │  │
│  │                                                                   │  │
│  │  Besleme: +5V (LM7805) ve +3.3V (LM1117)                       │  │
│  │  UART Çıkış: USB-C veya pin header                              │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Bağlantı Haritası

### 3.1 Güç Akışı

```
DC Input (22.2V/19-24V)
    │
    ▼
┌──────────────────────┐
│  PSU Board           │
│  OR-ing → Boost      │
│  ±35V DC @ 15A/rail  │
└──────────┬───────────┘
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
+35V bus        -35V bus
    │             │
    ▼             ▼
┌──────────────────────┐
│  Distribution Board  │
│  8× PTC fuse (3A)   │
│  8× MOSFET enable   │
└──────────┬───────────┘
           │
    ┌──────┼──────┬──────┬──────┬──────┬──────┬──────┬──────┐
    │      │      │      │      │      │      │      │      │
    ▼      ▼      ▼      ▼      ▼      ▼      ▼      ▼      ▼
   CH1    CH2    CH3    CH4    CH5    CH6    CH7    CH8
  50W    50W    50W    50W    50W    50W    50W    50W
```

### 3.2 Sinyal Akışı

```
Audio Input (RCA/XLR 1.2V RMS)
    │
    ├── CH1 ──┐
    ├── CH2 ──┤
    ├── CH3 ──┤
    ├── CH4 ──┤── Input Buffer (47kΩ) ── Diff Pair ── VAS ── Driver ── Output
    ├── CH5 ──┤
    ├── CH6 ──┤
    ├── CH7 ──┤
    └── CH8 ──┘
                                    │
                                    ▼
                              Speaker Output (8Ω, 50W)
                                    │
                                    ▼
                              Protection Board
                              ├── DC Offset Check
                              ├── Overcurrent Check
                              ├── Thermal Check
                              └── Speaker Relay
```

### 3.3 MCU Telemetri Akışı

```
┌─────────────────────────────────────────────────────────────┐
│                    MCU TELEMETRI SİSTEMİ                    │
│                                                             │
│  ┌─────────────┐    I2C (400kHz)    ┌──────────────────┐  │
│  │  INA219 ×4  │◄──────────────────►│                  │  │
│  │  (Güç)      │                    │                  │  │
│  └─────────────┘                    │                  │  │
│  ┌─────────────┐                    │   STM32F103      │  │
│  │  TMP102 ×4  │◄──────────────────►│   veya          │  │
│  │  (Sıcaklık) │                    │   RP2040         │  │
│  └─────────────┘                    │                  │  │
│  ┌─────────────┐                    │   ┌──────────┐  │  │
│  │  ADS1115 ×2 │◄──────────────────►│   │ ESP32-C3 │  │  │
│  │  (ADC)      │                    │   │ UART→WiFi│  │  │
│  └─────────────┘                    │   └──────────┘  │  │
│                                     │                  │  │
│  ┌─────────────┐    GPIO            │                  │  │
│  │  8× Relay   │◄──────────────────►│                  │  │
│  │  8× MOSFET  │                    │                  │  │
│  │  8× Fan PWM │                    │                  │  │
│  │  4× LED     │                    │                  │  │
│  └─────────────┘                    └──────────────────┘  │
│                                                             │
│  Veri Akışı:                                               │
│  Sensor → I2C → MCU → UART → ESP32-C3 → WiFi → Dashboard  │
│                                                             │
│  Telemetry Paketi (JSON, 100ms interval):                  │
│  {                                                         │
│    "v_supply_pos": 35.2,    // V                          │
│    "v_supply_neg": -34.8,   // V                          │
│    "i_total": 8.5,          // A                          │
│    "p_total": 595.0,        // W                          │
│    "channels": [                                           │
│      { "temp": 62.3, "dc_offset": 12.5, "i_out": 1.2 }, │
│      ...                                                   │
│    ],                                                      │
│    "relay_state": [1,1,1,1,1,1,1,1],                     │
│    "fan_rpm": [2200,2200,2200,2200,2200,2200,2200,2200],  │
│    "fault": 0                                              │
│  }                                                         │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Kart Boyutları ve Yerleşim

### 4.1 Kart Listesi

| # | Kart | Boyut (mm) | Katman | Ağırlık (tahmini) |
|---|------|-----------|--------|-------------------|
| 1 | Güç Kaynağı | 320 × 100 | 4 | 350g |
| 2 | Güç Dağıtımı | 320 × 60 | 2 | 80g |
| 3 | Amplifikatör CH1-8 | 200 × 100 (her biri) | 6 | 120g × 8 = 960g |
| 4 | Koruma | 320 × 60 | 2 | 100g |
| 5 | MCU Telemetri | 80 × 60 | 2 | 25g |
| | **Toplam PCB** | | | **~1.5kg** |

### 4.2 Şase Yerleşimi (400×300×100mm)

```
┌──────────────────────────────────────────────────────────┐
│  ÜST GÖRÜNÜM (400×300mm)                                │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Güç Kaynağı Kartı (320×100mm)                    │ │
│  │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐             │ │
│  │  │LM5122│ │LM5122│ │LM5122│ │LM5122│             │ │
│  │  └──────┘ └──────┘ └──────┘ └──────┘             │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Güç Dağıtımı Kartı (320×60mm)                    │ │
│  │  ┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐┌────┐│ │
│  │  │FUSE││FUSE││FUSE││FUSE││FUSE││FUSE││FUSE││FUSE││ │
│  │  └────┘└────┘└────┘└────┘└────┘└────┘└────┘└────┘│ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ┌──────────┐┌──────────┐┌──────────┐┌──────────┐     │
│  │  CH 1    ││  CH 2    ││  CH 3    ││  CH 4    │     │
│  │ 200×100  ││ 200×100  ││ 200×100  ││ 200×100  │     │
│  └──────────┘└──────────┘└──────────┘└──────────┘     │
│                                                          │
│  ┌──────────┐┌──────────┐┌──────────┐┌──────────┐     │
│  │  CH 5    ││  CH 6    ││  CH 7    ││  CH 8    │     │
│  │ 200×100  ││ 200×100  ││ 200×100  ││ 200×100  │     │
│  └──────────┘└──────────┘└──────────┘└──────────┘     │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Koruma Kartı (320×60mm)                           │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ┌──────────┐                                           │
│  │ MCU Kart │  (80×60mm)                                │
│  └──────────┘                                           │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 5. Kablo ve Bağlantı Detayı

### 5.1 Güç Kabloları

| Bağlantı | Tip | Uzunluk | Not |
|----------|-----|---------|-----|
| PSU → Distribution | Molex Mini-Fit Jr 8pin | 50mm | ±35V bus, 15A |
| Distribution → CH1-8 | Phoenix Contact 4pin | 100mm | +V, -V, GND, EN |
| Distribution → Protection | Ribbon cable 16pin | 80mm | Sense lines |
| Distribution → MCU | Ribbon cable 8pin | 80mm | Voltage sense |

### 5.2 Sinyal Kabloları

| Bağlantı | Tip | Uzunluk | Not |
|----------|-----|---------|-----|
| Input → CH1-8 | RCA jack (8× stereo) | PCB mount | Input buffer |
| CH1-8 → Output | Binding post (8×) | PCB mount | Speaker out |
| Protection → Speaker | 12AWG silicone | 150mm | Post-relay |
| MCU → I2C bus | Ribbon cable 10pin | 60mm | Sensor bus |
| MCU → UART | USB-C cable | 300mm | Telemetry |

### 5.3 Sensör Bağlantıları

| Sensör | Konum | Bağlantı | ADC/Protokol |
|--------|-------|----------|--------------|
| INA219 (CH1-4 güç) | PSU board | I2C (0x40-0x43) | I2C 400kHz |
| INA219 (CH5-8 güç) | PSU board | I2C (0x44-0x47) | I2C 400kHz |
| TMP102 (heatsink 1-4) | CH1-4 heatsink | I2C (0x48-0x4B) | I2C 400kHz |
| TMP102 (heatsink 5-8) | CH5-8 heatsink | I2C (0x4C-0x4F) | I2C 400kHz |
| ADS1115 (DC offset) | Protection board | I2C (0x48, 0x49) | I2C 400kHz |
| KSD301 (thermal) | Heatsink (each) | Normally closed → MCU GPIO | Digital |

---

## 6. Entegrasyon BOM

### 6.1 Kartlar (PCB)

| # | Kart | Miktar | Birim Fiyat (tahmini) | Toplam |
|---|------|--------|----------------------|--------|
| 1 | Güç Kaynağı PCB (4 katman) | 1 | $25 | $25 |
| 2 | Güç Dağıtımı PCB (2 katman) | 1 | $8 | $8 |
| 3 | Amplifikatör PCB (6 katman) | 8 | $15 | $120 |
| 4 | Koruma PCB (2 katman) | 1 | $10 | $10 |
| 5 | MCU Telemetri PCB (2 katman) | 1 | $5 | $5 |
| | **Toplam PCB** | **12** | | **$168** |

### 6.2 Mekanik

| # | Bileşen | Miktar | Birim Fiyat | Toplam | Not |
|---|---------|--------|-------------|--------|-----|
| 1 | Fischer SK82-150-SA heatsink | 8 | $12 | $96 | 400×100×60mm |
| 2 | Berger-Werk TGP 4500 termal pad | 16 | $3 | $48 | 0.25°C/W |
| 3 | Noctua NF-A8 PWM fan | 6 | $15 | $90 | 2200 RPM |
| 4 | Alüminyum şase (400×300×100mm) | 1 | $80 | $80 | Anodize siyah |
| 5 | Binding post (8× çift) | 16 | $1.5 | $24 | Kırmızı/siyah |
| 6 | RCA jack (8× çift) | 16 | $0.8 | $12.8 | Giriş |
| 7 | M3 vidalar + distans | 1 set | $15 | $15 | Tüm kartlar için |
| | **Toplam Mekanik** | | | **$365.8** | |

### 6.3 Bağlantı

| # | Bileşen | Miktar | Birim Fiyat | Toplam | Not |
|---|---------|--------|-------------|--------|-----|
| 1 | Molex Mini-Fit Jr 8pin | 2 | $3 | $6 | PSU çıkışı |
| 2 | Phoenix Contact 4pin | 8 | $1.5 | $12 | Kanal çıkışı |
| 3 | Ribbon cable 16pin | 1 | $2 | $2 | Protection |
| 4 | Ribbon cable 10pin | 1 | $1.5 | $1.5 | I2C bus |
| 5 | USB-C connector | 1 | $1 | $1 | UART |
| 6 | 12AWG silicone wire (1m) | 1 | $5 | $5 | Speaker out |
| | **Toplam Bağlantı** | | | **$27.5** | |

### 6.4 MCU & Sensör

| # | Bileşen | Miktar | Birim Fiyat | Toplam | Not |
|---|---------|--------|-------------|--------|-----|
| 1 | STM32F103C8T6 | 1 | $3 | $3 | MCU |
| 2 | RP2040 (alternatif) | 1 | $1 | $1 | Alternatif MCU |
| 3 | ESP32-C3 | 1 | $2 | $2 | WiFi bridge |
| 4 | INA219 | 8 | $1.5 | $12 | Güç ölçümü |
| 5 | TMP102 | 8 | $1 | $8 | Sıcaklık |
| 6 | ADS1115 | 2 | $2 | $4 | 16-bit ADC |
| 7 | LM7805 | 1 | $0.5 | $0.5 | 5V reg |
| 8 | LM1117-3.3 | 1 | $0.3 | $0.3 | 3.3V reg |
| 9 | USB-C breakout | 1 | $1 | $1 | UART |
| | **Toplam MCU** | | | **$31.8** | |

### 6.5 Koruma Kartı

| # | Bileşen | Miktar | Birim Fiyat | Toplam | Not |
|---|---------|--------|-------------|--------|-----|
| 1 | LM393 | 9 | $0.5 | $4.5 | Comparator (8+1 master) |
| 2 | Panasonic AGQ200 relay | 8 | $2 | $16 | DPDT 5A |
| 3 | 0.47Ω/5W wirewound | 8 | $0.8 | $6.4 | Overcurrent sense |
| 4 | KSD301 85°C | 8 | $0.5 | $4 | Thermal switch |
| 5 | IRLZ44N MOSFET | 8 | $1 | $8 | Enable switch |
| 6 | 3A PTC fuse | 16 | $0.3 | $4.8 | +V and -V per channel |
| 7 | LED (green/red) | 16 | $0.1 | $1.6 | Status indicators |
| | **Toplam Koruma** | | | **$45.3** | |

---

## 7. Toplam Sistem BOM Özeti

| Kategori | Toplam Fiyat |
|----------|-------------|
| PCB'ler (12 adet) | $168.0 |
| Mekanik (şase, heatsink, fan) | $365.8 |
| Bağlantı (konnektör, kablo) | $27.5 |
| MCU & Sensör | $31.8 |
| Koruma Kartı | $45.3 |
| Amplifikatör Kartı B bileşenleri (bom-classab) | ~$415.0 |
| **GENEL TOPLAM** | **~$1,053.4** |

**Not:** Amplifikatör kartı başına ~$52 (8 kart: ~$415), toplam sistem ~$1,053.

---

## 8. Isı Yönetimi Entegrasyyonu

### 8.1 Fan Kontrolü (MCU PWM)

```
MCU PWM Output (25kHz)
    │
    ├── CH1 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH2 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH3 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH4 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH5 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH6 Fan ── Noctua NF-A8 PWM (4-pin)
    ├── CH7 Fan ── Noctua NF-A8 PWM (4-pin)
    └── CH8 Fan ── Noctua NF-A8 PWM (4-pin)

Fan Hız Haritası (PID kontrollü):
┌────────────────────┬──────────────┬──────────────┐
│ Sıcaklık (Tj)     │ Fan Hızı     │ PWM Duty     │
├────────────────────┼──────────────┼──────────────┤
│ <50°C              │ Min (800RPM) │ %35          │
│ 50-65°C            │ Orta         │ %55          │
│ 65-75°C            │ Yüksek       │ %80          │
│ >75°C              │ Max (2200RPM)│ %100         │
│ >85°C              │ ACİL KAPAT   │ —            │
└────────────────────┴──────────────┴──────────────┘
```

### 8.2 Termal Koruma Sırası

```
Sıcaklık Artışı
    │
    ▼
┌─────────┐   Tj < 85°C    ┌─────────────────┐
│  Normal │────────────────►│ Fan hızı artır  │
│  Çalışma│                 └─────────────────┘
└─────────┘
    │
    │ Tj ≥ 85°C
    ▼
┌─────────┐                 ┌─────────────────┐
│  Uyarı  │────────────────►│ LED kırmızı     │
│  Seviyesi│                │ UART alarm      │
└─────────┘                 └─────────────────┘
    │
    │ Tj ≥ 95°C
    ▼
┌─────────┐                 ┌─────────────────┐
│  Koruma │────────────────►│ Kanal kapat     │
│  Seviyesi│                │ Speaker relay   │
└─────────┘                 └─────────────────┘
    │
    │ Tj ≥ 105°C
    ▼
┌─────────┐                 ┌─────────────────┐
│  ACİL   │────────────────►│ Tüm sistem kapat│
│  KAPATMA│                 │ PSU MOSFET off  │
└─────────┘                 └─────────────────┘
```

---

## 9. EMI/EMC Tasarım Kuralları

| Kural | Açıklama |
|-------|----------|
| GND plane | Kesintisiz, slot yok, star ground |
| Input kablosu | Twist-pair veya shielded,尽可能 kısa |
| Güç kablosu | Twist-pair (+35V ile -35V twist) |
| Fan kablosu | Ferrite bead + 100nF bypass |
| MCU bypass | 100nF MLCC, her bacak için |
| Switching noise | Post-filter 10µH µMetal choke |
| Kişisel topraklama | Chassis ground, tek bağlantı noktası |

---

## 10. Montaj Sırası

| # | Adım | Açıklama | Tahmini Süre |
|---|------|----------|-------------|
| 1 | Şase hazırlığı | Delik aç, stand-off monte et | 30dk |
| 2 | PSU kartı montaj | Vidala, güç kablosu bağla | 15dk |
| 3 | Distribution kartı montaj | Vidala, fuseleri tak | 10dk |
| 4 | Amplifikatör kartları (×8) | Her kartı vidala, heatsink pad'le | 60dk |
| 5 | Koruma kartı montaj | Vidala, ribbon cable bağla | 15dk |
| 6 | MCU kartı montaj | Vidala, sensör kablolarını bağla | 20dk |
| 7 | Fan montajı (×6) | Noctua NF-A8'leri vidala | 15dk |
| 8 | Binding post / RCA montaj | Şase üzerine monte et | 20dk |
| 9 | Tüm kabloları bağla | Güç, sinyal, sensör | 30dk |
| 10 | İlk test (güç yok) | Continuity, kısa devre kontrolü | 15dk |
| 11 | İlk güç | Düşük voltaj ile besleme testi | 15dk |
| 12 | Calibrate | Vbe trimpot, offset trim | 30dk |
| **Toplam** | | | **~4.5 saat** |

---

## 11. Güvenlik Uyarıları

| # | Uyarı | Açıklama |
|---|-------|----------|
| 1 | Yüksek voltaj | ±35V DC, guitar shock risk |
| 2 | Yüksek güç | 400W çıkış, yanık riski |
| 3 | Isı | Heatsink 65°C'ye kadar çıkabilir |
| 4 | Kapasitör | 4700µF × 8 = 37,600µF toplam |
| 5 | Soft-start | İlk açılışta 100ms gecikme |
| 6 | Koruma | Overcurrent ve thermal zorunlu |
| 7 | Topraklama | Chassis ground tek noktada |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-09-18  
**Version:** 1.0.0  
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
