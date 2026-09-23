---
title: "8 Channel Amplifier Design"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# 8 Channel Amplifier Design

## Genel Bakış

COREMUSIC K16, 8 bağımsız kanaldan oluşan çok kanallı Class AB güç amplifikatörüdür. Her kanal 100W @ 8Ω çıkış sağlar. Toplam çıkış gücü 800W continuous'dur. Dual-mono güç kaynağı, her 2 kanal için ayrı regüle sağlar.

## Sistem Blok Diyagramı

```
                    ┌──────────────────────────────────────┐
                    │         500VA Toroid Trafo            │
                    │      2×25V AC, 10A (center-tap)      │
                    └──────────┬───────────┬───────────────┘
                               │           │
                    ┌──────────┴───┐   ┌───┴──────────┐
                    │  Bridge #1   │   │  Bridge #2    │
                    │  KBPC3510    │   │  KBPC3510     │
                    └──────┬──────┘   └──────┬────────┘
                           │                 │
              ┌────────────┴──┐    ┌─────────┴──────────┐
              │ Cap Bank #1   │    │  Cap Bank #2        │
              │ 2×10,000μF   │    │  2×10,000μF         │
              │ 50V each rail │    │  50V each rail      │
              └──────┬───────┘    └──────┬──────────────┘
                     │                   │
        ┌────────────┼──────┐    ┌───────┼────────────┐
        │            │      │    │       │            │
   ┌────┴───┐  ┌────┴───┐  │  ┌────┴───┐  ┌────┴───┐
   │ CH 1   │  │ CH 2   │  │  │ CH 5   │  │ CH 6   │
   │ 100W   │  │ 100W   │  │  │ 100W   │  │ 100W   │
   └────┬───┘  └────┬───┘  │  └────┬───┘  └────┬───┘
        │           │      │       │           │
   ┌────┴───┐  ┌────┴───┐  │  ┌────┴───┐  ┌────┴───┐
   │ CH 3   │  │ CH 4   │  │  │ CH 7   │  │ CH 8   │
   │ 100W   │  │ 100W   │  │  │ 100W   │  │ 100W   │
   └────────┘  └────────┘  │  └────────┘  └────────┘
                           │
                    ┌──────┴──────┐
                    │  Speaker    │
                    │  Relays     │
                    └─────────────┘
```

## Kanal Özellikleri

| Parametre | Değer (her kanal) | 8 Kanal Toplam |
|---|---|---|
| Çıkış gücü | 100W RMS @ 8Ω | 800W |
| Çıkış gücü | 160W RMS @ 4Ω | 1280W |
| Frekans tepkisi | 20Hz – 80kHz | (±0.5dB) |
| THD+N @ 1W | ≤0.008% | ≤0.008% |
| Sinyal/Gürültü | ≥110dB (A-wt) | ≥110dB |
| Damping faktörü | ≥200 | ≥200 |
| Kazanç | 28.6dB (×27) | Ayarlanabilir |
| Giriş empedansı | 47kΩ (SE) | 10kΩ (bal) |

## Güç Dağılımı

```
Kanal Grubu 1 (CH1-4):
├── Besleme: ±35V DC (Bridge #1)
├── Toplam akım: 4 × 2.86A = 11.4A (peak)
├── Ortalama: 4 × 1.5A = 6A
└── Güç: 4 × 90.3W = 361W (DC giriş)

Kanal Grubu 2 (CH5-8):
├── Besleme: ±35V DC (Bridge #2)
├── Toplam akım: 4 × 2.86A = 11.4A (peak)
├── Ortalama: 4 × 1.5A = 6A
└── Güç: 4 × 90.3W = 361W (DC giriş)

Toplam DC giriş: 722W
Toplam AC çıkış: 800W
Verimlilik: 800/722 = 110.8% (teorik, Class AB)
```

## Kanal Topolojisi (Her Kanal)

```
Input ──[47kΩ]──▶ Diff Pair ──▶ VAS ──▶ Vbe Mult ──▶ Output ──▶ Speaker
    │                │            │          │           │
    │           Current Mirror  Miller    Darlington  MJL21194
    │                             │         Pair       /21193
    │                         100pF                  0.22Ω
    │                                                   │
    └───────────────── NFB ◀────────────────────────────┘
                     26kΩ/1kΩ
```

## Koruma Devresi (Her Kanal)

| Koruma | Sensör | Tetik | Aksiyon |
|---|---|---|---|
| DC Offset | Voltage divider | ±0.5V | Relay disconnect |
| Overcurrent | 0.22Ω sense | 2.95A | Foldback |
| Thermal | KSD301 | 85°C | System shutdown |
| Short Circuit | SOA limiter | 5A peak | Current limit |
| Overtemp | PTC fuse | 130°C | Permanent disconnect |

## PCB Layout Stratejisi

```
┌─────────────────────────────────────────────────────┐
│                    FRONT PANEL                        │
│  [Input 1] [Input 2] [Input 3] [Input 4] [Input 5] │
│  [Input 6] [Input 7] [Input 8]                      │
├─────────────────────────────────────────────────────┤
│                                                       │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                │
│  │ CH1  │ │ CH2  │ │ CH3  │ │ CH4  │                │
│  │ PCB  │ │ PCB  │ │ PCB  │ │ PCB  │                │
│  └──────┘ └──────┘ └──────┘ └──────┘                │
│                                                       │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                │
│  │ CH5  │ │ CH6  │ │ CH7  │ │ CH8  │                │
│  │ PCB  │ │ PCB  │ │ PCB  │ │ PCB  │                │
│  └──────┘ └──────┘ └──────┘ └──────┘                │
│                                                       │
├─────────────────────────────────────────────────────┤
│                                                       │
│  ┌───────────────┐  ┌───────────────┐                │
│  │  PSU #1       │  │  PSU #2       │                │
│  │  Bridge+Caps  │  │  Bridge+Caps  │                │
│  └───────────────┘  └───────────────┘                │
│                                                       │
│  ┌───────────────┐  ┌───────────────┐                │
│  │  Trafo 500VA  │  │  Protection   │                │
│  │  Toroid       │  │  Board        │                │
│  └───────────────┘  └───────────────┘                │
│                                                       │
├─────────────────────────────────────────────────────┤
│                   REAR PANEL                          │
│  [Speaker 1] [Speaker 2] ... [Speaker 8]            │
│  [AC Input]  [Fuse]  [Power Switch]                  │
└─────────────────────────────────────────────────────┘
```

## Termal Tasarım Özeti

```
Her kanal için:
├── Power dissipation: 24.7W
├── Heatsink: 100×60×40mm, R_θ=3.5°C/W
├── Junction temp: 145.5°C (fan ile: 49.7°C)
└── Fan: 60mm, 20CFM, 12V DC

Toplam:
├── 8 heatsink (veya 2 büyük)
├── 8 fan (opsiyonel)
├── Toplam airflow: 160 CFM
└── Toplam fan gücü: 8 × 1.2W = 9.6W
```

## Liste Fiyat Tahmini

| Bileşen | Adet | Birim Fiyat | Toplam |
|---|---|---|---|
| MJL21194 | 8 | $8.50 | $68 |
| MJL21193 | 8 | $8.50 | $68 |
| BD139/140 | 16 | $0.50 | $8 |
| Toroid 500VA | 1 | $180 | $180 |
| Bridge 35A | 2 | $12 | $24 |
| 10,000μF 50V | 8 | $15 | $120 |
| Heatsink | 8 | $25 | $200 |
| Fan 60mm | 8 | $8 | $64 |
| PCB (8 layer) | 1 | $150 | $150 |
| **Toplam** | | | **$882** |

## Durum: Implementasyon
