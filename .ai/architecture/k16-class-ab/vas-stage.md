---
title: "Voltage Amplification Stage (VAS)"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Voltage Amplification Stage (VAS)

## Genel Bakış

VAS katı (Voltage Amplification Stage), diferansiyel giriş katından gelen düşük genlikli sinyali çıkış katı için yeterli gerilim genliğine yükseltir. Miller kompanzasyonu ile birlikte, amplifikatörün frekans kararlılığını ve bant genişliğini kontrol eder.

## Devre Şeması

```
       +Vcc (+35V)
        │
       [R7] 10kΩ  (VAS yük direnci)
        │
        ├───────────────── Output (to Vbe Mult)
        │
   ┌────┴────┐
   │   Q7    │  Q7: 2N5551 (NPN)
   │ (CE     │  gm = 38mA/V @ 1mA
   │ amplifier)│ r_o = 100kΩ
   ├─────────┤
   │   Cc    │  Cc: 100pF (Miller compensation)
   │  100pF  │  pole splitting için
   └────┬────┘
        │
        │ ◀── from Current Mirror output
        │
   ┌────┴────┐
   │  R8     │  R8: 100Ω (emitter degeneration)
   └────┬────┘
        │
      -Vee (-35V)
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Transistör | 2N5551 | NPN, Vceo=160V |
| Collector akımı | 1mA | |
| Gerilim kazancı (Ad) | 1000 (60dB) | gm × R_C |
| Miller kapasitansı | 100pF | Cc |
| Bant genişliği | 1.6MHz | gm / (2π × Cc) |
| Çıkış empedansı | 100kΩ | r_o |
| DC offset çıkışı | 0V (dengede) | |
| Slew rate | 20V/μs | I_charge / Cc |

## Hesaplamalar

### Kazanç Hesabı
```
Ad = gm_Q7 × (R7 || r_o_Q7)
Ad = 38mA/V × (10kΩ || 100kΩ)
Ad = 38mA/V × 9.1kΩ = 345 (50.8dB)

Tam kazanç: 345 × diferansiyel kazanç (330) = 113,850
Kapalı döngü kazancı: 1 + Rf/Rg ≈ 27 (28.6dB)
```

### Miller Kompanzasyon
```
Cc = 100pF

Faz marjini ≥60° için:
f Unity = gm / (2π × Cc) = 38mA/V / (2π × 100pF)
f Unity = 60.5MHz

Dominant kutup: f_p1 = 1 / (2π × R_C × Cc)
f_p1 = 1 / (2π × 100kΩ × 100pF) = 15.9Hz
```

### Slew Rate
```
SR = I_Q7 / Cc
SR = 1mA / 100pF = 10V/μs (minimum)

Tam genlik için:
SR_max = I_tail / Cc = 1.7mA / 100pF = 17V/μs
```

### DC Kazanç (Open-Loop)
```
A_OL = A_diff × A_VAS × A_output
A_OL = 330 × 345 × 1 = 113,850 (101.1dB)
```

## Pole-Splitting Analizi

```
Pole 1 (dominant): f_p1 = 1 / (2π × R_out × Cc)
f_p1 = 15.9Hz @ 100kΩ

Pole 2 (non-dominant): f_p2 ≈ gm / (2π × C_L)
f_p2 ≈ 60MHz (yüksek çıkış kapasitesi nedeniyle)

Pole 3: f_p3 = 1 / (2π × R_π × C_π)
f_p3 ≈ 10MHz (giriş kapasitesi)
```

## Termal Sınırlar

- **Q7 Maksimum Güç**: P = (Vcc+Vee) × Ic = 70V × 1mA = 70mW
- **TO-92 Isı Dağılımı**: θ_ja = 200°C/W → ΔT = 0.014°C
- **Termal Equivalent Noise**: ≤3nV/√Hz @ 1kHz

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| Q7 | 2N5551 | NPN | Vceo=160V, hFE≥100 |
| R7 | 10kΩ | Metal film %1 | Düşük termal drift |
| R8 | 100Ω | Metal film %1 | Emitter degeneration |
| Cc | 100pF | C0G/NP0 | Sıcaklık kararlılığı |

##/Layout Notları

1. Cc DC drive olmadan mounts edilmeli (DC blokaj için)
2. Q7 collector isolated thermal pad
3. R7 Vcc trace'i kısa ve geniş (1A pulse dayanımı)

## Durum: Implementasyon
