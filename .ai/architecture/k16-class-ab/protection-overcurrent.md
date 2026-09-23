---
title: "Overcurrent Protection"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Overcurrent Protection

## Genel Bakış

Aşırı akım koruması, çıkış transistörlerini ve hoparlörleri kısa devre veya aşırı yükten korur. Current sensing dirençleri üzerinden çıkış akımını izler, eşik değeri aşıldığında akımı sınırlar (foldback). MJL21194/MJL21193'ün SOA sınırlarını korur.

## Devre Şeması

```
       +Vcc (+35V)
        │
       [R11] 0.22Ω  (current sense, positive rail)
        │
        ├───────────────────────────── To MJL21194 Emitter
        │
   ┌────┴────┐
   │  Q15    │  Q15: BC547 (NPN)
   │  Sense  │  V_BE trigger = 0.65V
   ├────┬────┤
   │  [R21]  │  R21: 470Ω (base bias)
   │    │    │
   │  [R22]  │  R22: 1kΩ (foldback divider)
   │    │    │
   │  [R23]  │  R23: 47Ω (foldback feedback)
   │    │    │
   └────┬────┘
        │
        ├─────────── To Bias clamp (Vbe mult)
        │
       [R24] 10kΩ
        │
       GND

Negative rail mirror circuit (Q16)
Same topology, PNP (BC557)
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Current sense | 0.22Ω ± %5 | Metal film 5W |
| Trigger voltage | 0.65V (V_BE) | Q15 |
| Trigger akımı | 0.65V / 0.22Ω = 2.95A | |
| Foldback ratio | 3:1 | Akım azaltma |
| Foldback akımı | 1A | Short circuit durumunda |
| Tepki süresi | ≤10μs | Hızlı koruma |
| Hysteresis | 0.1V | False trigger önleme |
| Koruma tipi | Foldback current limiting | |

## Hesaplamalar

### Akım Sınırı (Current Limit)
```
I_limit = V_BE(Q15) / R_sense
I_limit = 0.65V / 0.22Ω = 2.95A (peak)

RMS akım: I_rms = I_peak / √2 = 2.95 / 1.414 = 2.09A
Maks. çıkış gücü @ 8Ω: P = I_rms² × R = 2.09² × 8 = 34.9W
```

### Foldback Mekanizması
```
Q15 Base voltajı:
V_B = I_out × R_sense × (R22 / (R22 + R23))
V_B = I_out × 0.22 × (1000 / 1047)
V_B = I_out × 0.21 × 1 = 0.21 × I_out

Trigger: V_B = 0.65V
I_out(trigger) = 0.65 / 0.21 = 3.1A (peak)

Short circuit durumunda (R_L = 0):
V_out = 0V
I_foldback = V_BE × R23 / (R_sense × R22)
I_foldback = 0.65 × 47 / (0.22 × 1000) = 0.14A

Foldback oranı: 3.1A / 0.14A = 22:1
```

### Güç Sınırı
```
Maksimum çıkış gücü @ foldback:
P_max = I_limit × V_out(min)

V_out(min) = I_limit × R_L = 2.95A × 8Ω = 23.6V
P_max = 2.95 × 23.6 = 69.6W

Short circuit:
P_sc = I_foldback × V_cc = 0.14A × 35V = 4.9W
MJL21194 rating: 250W → Güvenli ✓
```

### Termal Koruma Entegrasyyonu
```
Q15 collector akımı:
I_C15 = (V_cc - V_BE - I_out × R_sense) / R24
I_C15 = (35 - 0.65 - 0.65) / 10kΩ = 3.37mA

Q15 güç: P = V_CE × I_C = 33.7V × 3.37mA = 113mW
TO-92 rating: 500mW → Güvenli ✓
```

## Foldback Karakteristiği

```
I_out (A)
  4 │          × ← Normal operation
    │        ×
  3 │──────×──── I_limit = 2.95A
    │      │
  2 │      │  ×
    │      │    ×
  1 │      │      × × × I_foldback = 0.14A
    │      │
  0 │──────┼──────────────────▶ V_out (V)
    0     10    20    25    35

Foldback region: V_out < 23.6V
Akım azaltma: 2.95A → 0.14A
```

## Alternatif Topolojiler

### 1. Simple Current Limit
```
V_BE trigger → Base drive cutoff
Avantaj: Basit
Dezavantaj: Sabit akım sınırı, foldback yok
```

### 2. Foldback (Mevcut)
```
V_BE trigger + voltage divider feedback
Avantaj: Short circuit'te güç çok düşük
Dezavantaj: Daha karmaşık
```

### 3. Electronic Circuit Breaker
```
Latch-up mekanizması
Avantaj: Hızlı kesme
Dezavantaj: Reset gerekli
```

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| Q15 | BC547 | NPN, TO-92 | Sense transistor |
| Q16 | BC557 | PNP, TO-92 | Negative rail sense |
| R_sense | 0.22Ω | 5W wirewound | ±%5 |
| R21 | 470Ω | Metal film %1 | |
| R22 | 1kΩ | Metal film %1 | |
| R23 | 47Ω | Metal film %1 | |
| R24 | 10kΩ | Metal film %1 | |

## Durum: Implementasyon
