---
title: "Power Supply Requirements"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Power Supply Requirements

## Genel Bakış

±35V DC besleme sistemi, K16 Class AB amplifikatörün tüm katmanlarına güç sağlar. Toroid trafolar, aktif regüle ve büyük kapasitörlü filtreleme ile düşük ripple ve yüksek akım kapasitesi sağlanır. 8 kanal için toplam ±7A DC akım kapasitesi gerekir.

## Güç Kaynağı Blok Diyagramı

```
AC Mains (220V 50Hz)
       │
  ┌────┴────┐
  │ Fuse 3A │
  │ 250VAC  │
  └────┬────┘
       │
  ┌────┴────┐
  │ Toroid  │ 500VA (8 kanal için)
  │ Trafo   │ 2×25V AC secondary
  │ 2×25VAC │ (±35V DC için)
  └────┬────┘
       │
  ┌────┴────┐
  │ Bridge  │ 35A, 200V (KBPC3510)
  │ Rectifier│
  └────┬────┘
       │
  ┌────┴────┐    ┌──────────┐
  │  C_F    │    │  R_ripple │
  │ 10,000μF│───▶│  0.1Ω    │
  │  50V    │    │  (ESR)   │
  └────┬────┘    └────┬─────┘
       │              │
       ├──────────────┼──────────────▶ +35V DC (regüle)
       │              │
       │         [78xx/79xx]  (opsiyonel regüle)
       │              │
       ├──────────────┼──────────────▶ -35V DC (regüle)
       │              │
  ┌────┴────┐    ┌──────────┐
  │  C_F2   │    │  R_ripple │
  │ 10,000μF│───▶│  0.1Ω    │
  │  50V    │    │          │
  └────┬────┘    └────┬─────┘
       │              │
      GND             GND
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Çıkış gerilimi | ±35V DC | Regülesiz (raw DC) |
| Maks.ripple | ≤100mV pp | Full load |
| Maks. akım (per rail) | ±7A DC | 8 kanal için |
| Toroid trafosu | 500VA | 2×25V AC, 10A |
| Bridge rectifier | 35A, 200V | KBPC3510 |
| Ana filtre | 10,000μF × 4 | 50V elektrolitik |
| ESR | ≤0.1Ω | Low-ESR tip |
| Ses bandı ripple | -80dB (100Hz) | |
| PSRR | ≥80dB | Aktif regüle |

## Hesaplamalar

### DC Çıkış Gerilimi
```
V_DC = V_AC(rms) × √2 - 2 × V_diode - I_load × R_trafo
V_DC = 25V × 1.414 - 2 × 0.7V - 3A × 0.5Ω
V_DC = 35.35 - 1.4 - 1.5 = 32.45V (full load)

No-load: V_DC = 25 × 1.414 - 1.4 = 34.0V
Regülasyon: ΔV/V = (34.0 - 32.45) / 32.45 = 4.8%
```

### Ripple Hesabı
```
Ripple voltajı (full-wave rectified):
V_ripple = I_load / (2 × f × C)
V_ripple = 7A / (2 × 50Hz × 10,000μF)
V_ripple = 7 / 1.0 = 7.0V (teorik, tek kapasitör)

Çift kapasitör (her birinde 10,000μF):
V_ripple = 7A / (2 × 50Hz × 20,000μF)
V_ripple = 7 / 2.0 = 3.5V

Aktif regüle ile: V_ripple ≤ 100mV (80dB azaltma)
```

### Toroid Trafo Seçimi
```
Gereken güç:
P_out = 8 × 100W = 800W (peak)
P_avg = 8 × 50W = 400W (Class AB %50 verim)
P_trafo = P_avg / η = 400W / 0.85 = 470VA

Trafo seçimi: 500VA toroid (margin dahil)
├── Primer: 220V 50Hz, 2.27A
├── Sekonder: 2×25V AC, 10A
├── Gövde: Toroid (düşük manyetik interference)
└── Montaj: Center bolt, rubber grommet
```

### Filtre Kapasitörü Boyutlandırma
```
Ripple voltajı hedef: ≤100mV @ 7A
C_min = I_load / (2 × f × V_ripple)
C_min = 7A / (2 × 50Hz × 0.1V)
C_min = 7 / 10 = 0.7F = 700,000μF

Pratik: 4 × 10,000μF = 40,000μF (paralel)
Neden yetersiz: Aktif regüle gerekli

Çözüm: Pre-regulator + active filtering
```

### PSU Hız Tepkisi
```
Bandwidth: DC – 100kHz (audio band)
Slew rate: ≥10V/μs (100W @ 8Ω için)
Transient response: <100μs recovery

Output impedance: ≤0.05Ω (DC – 100kHz)
Capacitor ESR contribution: 0.1Ω / 4 = 0.025Ω
Total: 0.025Ω (yeterli)
```

## Regüle Devre

```
+35V Raw DC ──┬──[R30] 0.1Ω ──┬── Output +35V
              │                │
         [C3] 100μF      [C4] 100μF
              │                │
             GND              GND

-35V Raw DC ──┬──[R31] 0.1Ω ──┬── Output -35V
              │                │
         [C5] 100μF      [C6] 100μF
              │                │
             GND              GND

Aktif regüle (opsiyonel):
LM317 (+ rail), LM337 (- rail)
or discrete: BD139/BD140 pass transistor
```

## Kapasitör Seçimi

| Konum | Kapasitör | Tip | Volt | Not |
|---|---|---|---|---|
| Ana filtre | 10,000μF × 4 | Nichicon KG | 50V | Audio grade |
| Bypass | 100μF × 4 | Nichicon FW | 50V | Hızlı tepki |
| HF bypass | 1μF × 4 | Panasonic ECW | 63V | Film tip |
| HF bypass | 100nF × 8 | Ceramic X7R | 50V | Her kanal |
| Polarity protect | 1N4007 × 4 | Si diode | 100V | Reverse polarity |

## Güvenlik

| Kriter | Değer |
|---|---|
| Fuse primer | 3A slo-blow, 250VAC |
| Fuse sekonder | 5A slo-blow, 50V per rail |
| Bleeder resistor | 10kΩ 5W (her kapasitör) |
| Discharge time | ≤30s (50V → 50V) |
| LED indicator | Power on (her rail) |

## Durum: Implementasyon
