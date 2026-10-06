---
title: "Push-Pull Output Stage"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Push-Pull Output Stage

## Genel Bakış

Push-pull çıkış katı, tamamlayıcı (complementary) NPN/PNP transistör çifti ile Class AB modunda çalışır. MJL21194 (NPN) ve MJL21193 (PNP) güç transistörleri, düşük empedanslı (>200 damping factor) ve yüksek güçlü (100W+) çıkış sağlar.

## Devre Şeması

```
       +Vcc (+35V)
        │
       [R11] 0.22Ω  (emitter direnci, crowbar)
        │
   ┌────┴────┐
   │  Q9     │  Q9: MJL21194 (NPN, Darlington)
   │ MJL21194│  hFE = 75-180 @ 5A
   ├────┬────┤
   │  Q10    │  Q10: MJL21193 (PNP, Darlington)
   │ MJL21193│  hFE = 75-180 @ 5A
   └────┬────┘
        │
       [R12] 0.22Ω  (emitter direnci, crowbar)
        │
      -Vee (-35V)

         │
         ├────── Output (to speaker)
         │
       [R_out] 10Ω (damping test)
         │
        GND

Input: Vbe Multiplier Base drive
Feedback: From output to Diff Pair
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| NPN transistör | MJL21194 | 250W, 250V, 16A |
| PNP transistör | MJL21193 | 250W, 250V, 16A |
| Topoloji | Complementary Darlington | Ek pedal transistörleri |
| Çıkış gücü | 100W RMS @ 8Ω | ±35V besleme ile |
| Maks. akım | ±5A rms | Sınır koruması ile |
| Çıkış empedansı | ≤0.01Ω | Geri besleme ile |
| Damping faktörü | ≥200 | 8Ω yük |
| THD @ 1W | ≤0.008% | |
| Frekans tepkisi | DC–80kHz | -0.5dB |
| Emitter direnci | 0.22Ω ± %5 | Metal halj |

## Hesaplamalar

### Maksimum Çıkış Voltajı
```
V_out_peak = Vcc - V_CE(sat) - V_R_emitter
V_out_peak = 35V - 1.5V - 0.22Ω × 5A = 35 - 1.5 - 1.1 = 32.4V

V_out_rms = V_out_peak / √2 = 32.4 / 1.414 = 22.9V
P_out = V_out_rms² / R_L = 22.9² / 8 = 65.6W (tek transistor)

Push-Pull: P_total = 2 × 65.6 = 131W (teorik)
Pratik: 100W RMS @ 8Ω (sıcaklık ve pratik sınırlar)
```

### Akım Sınırı
```
I_peak = V_out_peak / R_L = 32.4V / 8Ω = 4.05A
I_rms = I_peak / √2 = 4.05 / 1.414 = 2.86A

Emitter direnci üzerinde voltaj:
V_R = I_peak × R_emitter = 4.05A × 0.22Ω = 0.891V
```

### Empedans Dönüşümü
```
R_out(open loop) = 0.22Ω (emitter direnci)
R_out(closed loop) = R_out(OL) / (1 + A_OL × β)

A_OL = 113,850 (101dB)
β = R_f / (R_f + R_g) = 26kΩ / (26kΩ + 1kΩ) = 0.963

R_out(CL) = 0.22 / (1 + 113850 × 0.963) ≈ 0.002Ω = 2mΩ
```

### Damping Faktörü
```
DF = R_load / R_out(CL)
DF = 8Ω / 0.002Ω = 4000 (teorik)

Pratik: DF ≥ 200 (kablo empedansı dahil)
```

## Class AB Çalışma Modu

```
Idle durumu: Her iki transistör hafif iletimde (50-100mA)
├── Q9 (NPN): Vbe = 0.65V, Ic = 50mA
├── Q10 (PNP): Vbe = -0.65V, Ic = 50mA
└── Toplam idle: 100mA @ ±35V = 3.5W heating

Pozitif half-cycle: Q9 aktif, Q10 cutoff
├── Q9 collector akımı: 0 → 4A peak
└── Q10 reverse biased (cutoff)

Negatif half-cycle: Q10 aktif, Q9 cutoff
├── Q10 collector akımı: 0 → 4A peak
└── Q9 reverse biased (cutoff)

Crossover region: Her iki transistör hafif iletimde
└── Crossover distorsiyonu minimized (bias ayarı ile)
```

## Güç Kaybı ve Verimlilik

```
Verimlilik (Class AB, 8Ω):
η = (π/4) × (V_out / V_cc) × 100%
η = 0.785 × (22.9 / 35) × 100% = 51.4%

DC güç girişi: P_DC = V_cc × I_avg = 35V × (2 × I_peak / π)
P_DC = 35V × (2 × 4.05 / π) = 35 × 2.58 = 90.3W

Isı kaybı: P_heat = P_DC - P_out = 90.3 - 65.6 = 24.7W (per transistor)
```

## Durum: Implementasyon
