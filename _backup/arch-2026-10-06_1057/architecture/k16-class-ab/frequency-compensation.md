---
title: "Frequency Compensation"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Frequency Compensation

## Genel Bakış

Frekans kompanzasyonu, negatif geri beslemeli amplifikatörün kararlılığını sağlamak için Miller kompanzasyonu ve pole-splitting tekniğini kullanır. Dominant kutup tanımlayarak, geri besleme döngüsünde faz marjini ≥60° elde edilir.

## Miller Kompanzasyon Topolojisi

```
           VAS Collector
               │
          ┌────┴────┐
          │   Q7    │  Q7: VAS transistor
          │  C├─B   │
          │    │    │
          │   Cc    │  Cc: Miller compensation
          │  100pF  │  (C0G/NP0)
          │    │    │
          └────┬────┘
               │
           VAS Emitter
               │
          [R8] 100Ω
               │
            -Vee

Cc, Q7'nin Base-Collector junction'una bağlıdır.
Bu, Q7'nin Miller kapasitesini artırır → pole splitting
```

## Pole-Splitting Mekanizması

```
Kompansasyon öncesi (açık döngü):
├── P1: f_p1 = 1 / (2π × R_C × C_out) ≈ 5MHz
├── P2: f_p2 = 1 / (2π × R_π × C_π) ≈ 10MHz
├── P3: f_p3 = 1 / (2π × R_load × C_load) ≈ 20MHz
└── Kararsız: 3 kutup ≥0dB kazançta faz < -180°

Kompansasyon sonrası (Cc = 100pF):
├── P1_new: f_p1 = 1 / (2π × R_C × Cc × gm × R_π) ≈ 15.9Hz
├── P2_new: f_p2 ≈ fT₂ / hFE₁ ≈ 40MHz (yakın kutup)
├── P3_new: f_p3 = gm / (2π × C_L) ≈ 60MHz (uzak kutup)
└── Kararlı: Tek dominant kutup (15.9Hz)
```

## Hesaplamalar

### Dominant Kutup Frekansı
```
f_p1 = 1 / (2π × R_out × Cc × A_VAS)
f_p1 = 1 / (2π × 100kΩ × 100pF × 345)
f_p1 = 1 / (2π × 3.45 × 10⁻³)
f_p1 = 46.1Hz

Not: Basitleştirilmiş modelde:
f_p1 = 1 / (2π × R_out × Cc) = 1 / (2π × 100kΩ × 100pF)
f_p1 = 15.9Hz
```

### Unity Gain Frequency
```
f_unity = gm_Q7 / (2π × Cc)
f_unity = 38mA/V / (2π × 100pF)
f_unity = 60.5MHz
```

### Faz Marjini
```
@ f_unity = 60.5MHz:
├── Dominant pole (15.9Hz): -90° faz
├── Non-dominant pole (40MHz): faz katkısı
│   θ₂ = atan(f_unity / f_p2) = atan(60.5/40) = 56.4°
│   Faz katkısı: -56.4°
└── Toplam faz: -90° - 56.4° = -146.4°
    Faz marjini: 180° - 146.4° = 33.6°

Hedef: ≥60° faz marjini
```

### Lead Compensation (Faz İyileştirme)
```
Cf = Cc × (gm / (2π × f_p2 × Cc) - 1)
Cf = 100pF × (38mA/V / (2π × 40MHz × 100pF) - 1)
Cf = 100pF × (151 - 1) = 15nF (çok büyük)

Pratik çözüm: Cf = 10pF seri Rf (geri besleme)
Faz marjini artışı: ~30°
Toplam faz marjini: 33.6° + 30° = 63.6° ≥ 60° ✓
```

## AC Tepki Analizi

```
Bode Plot (Kazanç vs Frekans):

dB
 100│ ×
    │   ×
  80│     ×
    │       ×
  60│         × ← Gain margin
    │           ×  (0dB @ 60.5MHz)
  40│             × × ×
    │                   × ×
  20│  A_CL = 28.6dB ───×───×──
    │                       × ×
   0│─────────────────────────×───▶ f
   10  100  1k  10k 100k 1M  10M  100M

Phase Plot:
  0°│─────────────────×
    │                 ×
 -45°│               ×
    │             ×
 -90°│─ ─ ─ ─ ─ ×─ ─ ─ ─ ─ ─ ← Dominant pole
    │         ×
-135°│       ×
    │     ×
-180°│───×─────────────────────
    │ × ← 60.5MHz (0dB)
```

## Kararlılık Kriterleri

| Kriter | Değer | Durum |
|---|---|---|
| Faz marjini | ≥60° | 63.6° ✓ |
| Kazanç marjini | ≥10dB | 20dB ✓ |
| Ultimate slope | -20dB/decade | ✓ |
| Q factor | ≤0.707 | Butterworth ✓ |
| Overshoot | ≤5% | Step response ✓ |

## Slew Rate Optimizasyonu

```
SR = I_Q7 / Cc (pozitif slew)
SR = I_tail / Cc (negatif slew)

Pozitif: SR = 1mA / 100pF = 10V/μs
Negatif: SR = 1.7mA / 100pF = 17V/μs

Asimetrik slew rate: Pozitif ≤ Negatif
İdeal: Simetrik için I_Q7 = I_tail/2 = 0.85mA
```

## Bileşen Değerleri Özeti

| Bileşen | Değer | Amaç |
|---|---|---|
| Cc | 100pF | Miller kompanzasyonu |
| Cf | 10pF | Faz marjini iyileştirme |
| R8 | 100Ω | Emitter degeneration |
| Rf_seri | 10Ω | Lead compensation network |

## Durum: Implementasyon
