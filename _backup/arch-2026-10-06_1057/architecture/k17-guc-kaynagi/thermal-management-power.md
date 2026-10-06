---
title: "Güç Aşaması Termal Yönetimi"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Güç Aşaması Termal Yönetimi

## Genel Bakış

Termal yönetim, COREMUSIC'in güç aşama bileşenlerinin (MOSFET, indüktör, LDO, Sense resistor) güvenli sıcaklık aralığında çalışmasını sağlar. Aşırı sıcaklık, bileşen ömrünü kısaltır, verimliliği düşürür ve termal runaway riski oluşturur. Çok katmanlı termal tasarım ile junction sıcaklıkları 100°C altında tutulur.

## Termal Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    TERMAL YÖNETİM MİMARİSİ                            │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  ISI KAYNAKLARI (Heat Sources)                                │   │
│  │                                                                │   │
│  │  Bileşen        Güç Kaybı     Junction T   Max T             │   │
│  │  ──────────     ──────────     ──────────   ─────             │   │
│  │  Q1-Q4 MOSFET   29.2mW        55°C         150°C             │   │
│  │  L1, L2 Ind.    500mW         65°C         125°C             │   │
│  │  D1, D2 Diode   200mW         58°C         150°C             │   │
│  │  LM317 (+15V)   10W           105°C        125°C             │   │
│  │  LM337 (-15V)   10W           105°C        125°C             │   │
│  │  LM7812 (+12V)  6W            85°C         125°C             │   │
│  │  LM7912 (-12V)  6W            85°C         125°C             │   │
│  │  TPS54331 (+5V) 2.5W          72°C         125°C             │   │
│  │  TPS62A01(+3.3V)0.34W        45°C         125°C             │   │
│  │                                                                │   │
│  │  Toplam Sistem: ~36W (max)                                   │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  ISI YOLU (Thermal Path)                                     │   │
│  │                                                                │   │
│  │  Junction ──▶ Die Attach ──▶ Lead Frame ──▶ PCB Pad          │   │
│  │     │              │              │              │              │   │
│  │   Rθ_JC        Rθ_CS          Rθ_SA         Rθ_SA            │   │
│  │  (3°C/W)      (1°C/W)        (20°C/W)      (50°C/W)        │   │
│  │                                                                │   │
│  │  PCB ──▶ Copper Pour ──▶ Thermal Via ──▶ Bottom Layer        │   │
│  │     │          │              │                │               │   │
│  │   Rθ_PB     Rθ_CP          Rθ_TV          Rθ_LA             │   │
│  │  (5°C/W)    (2°C/W)        (1°C/W)        (10°C/W)         │   │
│  │                                                                │   │
│  │  ──▶ Heatsink ──▶ Air (Convection)                          │   │
│  │         │                   │                                  │   │
│  │       Rθ_SH              Rθ_CA                                │   │
│  │     (5°C/W)            (25°C/W)                              │   │
│  │                                                                │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Termal Model

### LDO için Termal Hesap (LM317, +15V)

```
┌──────────────────────────────────────────────────────────────┐
│  TERMAL HESAP - LM317 (+15V LDO)                             │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  Güç Kaybı:                                                   │
│  P = (VIN - VOUT) × IOUT                                     │
│  P = (35V - 15V) × 0.5A = 10W                               │
│                                                                │
│  Termal Dirençler (TO-220 paket):                            │
│  Rθ_JC = 3°C/W (junction-case)                               │
│  Rθ_CS = 1°C/W (case-sink)                                   │
│  Rθ_SA = 5°C/W (sink-ambient, heatsink ile)                 │
│                                                                │
│  Junction Sıcaklığı:                                          │
│  TJ = TA + P × (Rθ_JC + Rθ_CS + Rθ_SA)                     │
│  TJ = 25°C + 10W × (3 + 1 + 5)°C/W                         │
│  TJ = 25°C + 90°C = 115°C                                   │
│                                                                │
│  Limit: TJ_MAX = 125°C                                       │
│  Marj: 10°C (yeterli değil!)                                  │
│                                                                │
│  Çözüm: Daha büyük heatsink veya pre-regulator               │
│  Rθ_SA_needed = (125 - 25) / 10 - 4 = 6°C/W                │
│  Heatsink seçimi: Rθ_SA ≤ 6°C/W                             │
│                                                                │
└──────────────────────────────────────────────────────────────┘
```

### MOSFET için Termal Hesap

```
┌──────────────────────────────────────────────────────────────┐
│  TERMAL HESAP - IRF3205 (Boost MOSFET)                       │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  Güç Kaybı (Tek MOSFET):                                     │
│  P = I² × RDS(on) = (2A)² × 10mΩ = 40mW                   │
│                                                                │
│  Termal Dirençler (D2PAK paket):                             │
│  Rθ_JC = 1.5°C/W                                             │
│  Rθ_CS = 0.5°C/W                                             │
│  Rθ_SA = 40°C/W (PCB-only, heatsink yok)                    │
│                                                                │
│  Junction Sıcaklığı:                                          │
│  TJ = 25°C + 0.04W × (1.5 + 0.5 + 40)°C/W                 │
│  TJ = 25°C + 1.68°C = 26.68°C                               │
│                                                                │
│  Sonuç: Çok düşük, heatsink gerekmez ✅                      │
│                                                                │
└──────────────────────────────────────────────────────────────┘
```

## PCB Termal Tasarımı

### Thermal Via Matrisi

```
┌─────────────────────────────────────────────────────────────┐
│  THERMAL VİA MATRİSİ (LDO Pad Altı)                         │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Top Copper Pad (LM317 soğutma pad)                              │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  ○   ○   ○   ○   ○   ○   ○   ○   ○   ○              │  │
│  │    ○   ○   ○   ○   ○   ○   ○   ○   ○                │  │
│  │  ○   ○   ○   ○   ○   ○   ○   ○   ○   ○              │  │
│  │    ○   ○   ○   ○   ○   ○   ○   ○   ○                │  │
│  │  ○   ○   ○   ○   ○   ○   ○   ○   ○   ○              │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                               │
│  Via Specs:                                                  │
│  • Çap: 0.3mm (12mil) drill, 0.5mm (20mil) pad            │
│  • Spacing: 1.0mm (40mil) center-to-center                 │  │
│  • Fill: Copper filled (not tented)                         │
│  • Count: 50 via per LDO pad                               │
│  • Layer: Top to Bottom (4-layer PCB)                       │
│                                                               │
│  Thermal Resistance Improvement:                            │
│  Without vias: Rθ_SA = 50°C/W                               │
│  With 50 vias: Rθ_SA = 10°C/W (5× improvement)             │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Copper Pour Stratejisi

```
┌─────────────────────────────────────────────────────────────┐
│  COPPER POUR KURALLARI                                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Layer 1 (Top):                                              │
│  • Power component pads: 2oz copper                         │
│  • Thermal pour: ±35V, ±15V rail areas                      │
│  • Clearance: 8mil to signal traces                         │
│                                                               │
│  Layer 2 (Inner 1 - GND):                                   │
│  • Solid ground plane: 2oz copper                           │
│  • Thermal via connection: Direct pad                        │
│  • No signal routing in power areas                         │
│                                                               │
│  Layer 3 (Inner 2 - Power):                                 │
│  • Power distribution: 2oz copper                           │
│  • Rail pour: Matched to component placement                │
│  • Thermal relief: For through-hole components              │
│                                                               │
│  Layer 4 (Bottom):                                           │
│  • Thermal pour: Bottom-side heatsinking                    │
│  • Heatsink mounting area                                   │
│  • Test points: Accessible                                  │
│                                                               │
│  Total Copper: 8oz (2oz × 4 layers)                        │
│  Thermal Conductivity: 385 W/m·K (copper)                  │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Heatsink Seçimi

### LDO'lar için Heatsink

```
┌─────────────────────────────────────────────────────────────┐
│  HEATSINK SEÇİMİ (LM317/LM337 için)                         │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  Gereksinim:                                                 │
│  • Rθ_SA ≤ 6°C/W (10W için TJ < 125°C)                    │
│  • Boyut: ≤ 40×20×15mm (PCB constraint)                    │
│  • Ağırlık: <50g                                             │
│                                                               │
│  Seçenek 1: Extruded Alüminyum                              │
│  • Model: Aavid 531002B00000G                              │
│  • Rθ_SA: 5.5°C/W (@10W, natural convection)               │
│  • Boyut: 38.1×19.05×12.7mm                                │
│  • Ağırlık: 25g                                             │
│  • Maliyet: $2.50                                            │
│                                                               │
│  Seçenek 2: PCB Heatsink (Dragonya)                         │
│  • Copper pour: 40×40mm, 2oz                                │
│  • Rθ_SA: 8°C/W (natural convection)                       │
│  • Ek avantaj: PCB entegre, maliyet yok                    │
│                                                               │
│  Seçenek 3: Active Cooling (fan)                            │
│  • Rθ_SA: 1.5°C/W (fan ile)                                │
│  • Gereklilik: Sadece 10W+ yükler için                     │
│  • Dezavantaj: Gürültü, güç tüketimi                       │
│                                                               │
│  Seçim: Seçenek 1 (extruded heatsink)                      │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Sıcaklık İzleme

### NTC Thermistor Ağı

```
              VCC (3.3V)
                │
           R_PULL (10kΩ)
                │
                ├──── ADC Input
                │
              NTC (10kΩ @ 25°C)
                │
               GND

Steinhart-Hart Denklemi:
1/T = A + B×ln(R) + C×(ln(R))³

A = 1.1276e-3
B = 2.3425e-4
C = 8.7730e-8

Lookup Table:
Sıcaklık    NTC Direnci    ADC (12-bit)
─────────   ───────────    ────────────
  -20°C     100.6kΩ        4032
    0°C      32.65kΩ       3688
   25°C      10.0kΩ        3000
   50°C       3.601kΩ      2048
   85°C       1.167kΩ       945
  125°C       0.378kΩ       348
```

### Sıcaklık Alarm Eşikleri

```
┌──────────────────────────────────────────────────────────────┐
│  SICAKLIK ALARM EŞİKLERİ                                       │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  Board Sıcaklığı:                                            │
│  • Uyarı: 65°C → LED sarı, fan hızı artır                   │
│  • Alarm: 75°C → Sistem yükü azalt                          │
│  • Kritik: 85°C → Acil kapatma                              │
│                                                                │
│  Junction Sıcaklığı:                                         │
│  • Uyarı: 100°C → PWM duty azalt                           │
│  • Alarm: 110°C → Rail kapatma                              │
│  • Kritik: 120°C → Tüm sistem kapatma                       │
│                                                                │
│  Hysteresis: 10°C (cooldown için)                            │
│                                                                │
└──────────────────────────────────────────────────────────────┘
```

## Sıcaklık Dağılım Diyagramı

```
Sıcaklık (°C)
  120│          ╔══════╗  TJ_MAX
     │          ║      ║
  110│     ●────║──────║──── LM317 (10W load)
     │     │    ║      ║
  100│─────│────║──────║──── TJ Warning
     │     │    ║      ║
   90│     │    ║      ║
     │     │    ║      ║
   80│     │    ║      ║
     │     │    ║      ║
   70│  ●──│────║──────║──── LM7812 (6W)
     │  │  │    ║      ║
   60│  │  │    ║      ║
     │  │  │    ║      ║
   50│  │  │    ║      ║    TPS54331 (2.5W)
     │  │  │    ║      ║
   40│  │  │    ║      ║
     │  │  │    ║      ║
   30│──│──│────║──────║──── TA (Ambient)
     │  │  │    ║      ║
   20│  │  │    ║      ║
     └──┴──┴────╚══════╝──────
        │  │     │
        │  │    Heatsink
        │  │
        │  PCB (copper pour)
        │
     Component (no heatsink)
```

## Spesifikasyonlar

| Parametre | Hedef | Gerçek | Durum |
|-----------|-------|--------|-------|
| Junction T (MOSFET) | <100°C | 27°C | ✅ |
| Junction T (LDO+HS) | <125°C | 115°C | ⚠️ Marjdar |
| Board T (max) | <85°C | 72°C | ✅ |
| Ambient T | 25°C | 25°C | ✅ |
| Thermal Shutdown | 85°C board | Ayarlandı | ✅ |
| Total Power Dissipation | <40W | 36W | ✅ |

## Termal Malzeme

| Malzeme | Kullanım | Therm. Cond. | Thickness |
|---------|----------|--------------|-----------|
| Thermal Paste | Heatsink-pad | 5 W/m·K | 0.1mm |
| Thermal Pad | MOSFET-PCB | 3 W/m·K | 0.5mm |
| FR-4 PCB | Substrate | 0.3 W/m·K | 1.6mm |
| Copper | Pour/via | 385 W/m·K | 2oz (70μm) |
| Alüminyum | Heatsink | 237 W/m·K | - |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | MOSFET termal tasarım |
| K17-VoltageReg | LDO termal yönetim |
| K17-PowerEfficiency | Kayıp analizi |
| K0-PCB | PCB termal tasarımı |
| K0-Mekanik | Heatsink montaj |

## Durum: Implementasyon

✅ Termal kaynaklar belirlendi (36W toplam)  
✅ Junction sıcaklık hesaplamaları yapıldı  
✅ PCB thermal via matrisi tasarlandı (50 via/LDO)  
✅ Heatsink seçimi tamamlandı (Aavid 531002B00000G)  
✅ NTC sıcaklık izleme devresi entegre edildi  
✅ Sıcaklık alarm eşikleri ayarlandı (65/75/85°C)  
✅ Copper pour stratejisi belirlendi (2oz, 4-layer)  
⚠️ Gerçek termal ölçüm bekleniyor (termal kamera ile)  
⚠️ Long-term termal cycling testi devam ediyor
