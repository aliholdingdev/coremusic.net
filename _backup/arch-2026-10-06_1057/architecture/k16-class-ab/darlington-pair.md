---
title: "Darlington Configuration"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Darlington Configuration

## Genel Bakış

Darlington yapılandırması, iki transistörün seri bağlı collector-emitter konfigürasyonuyla yüksek akım kazancı elde eder. COREMUSIC K16'da çıkış transistörlerinin sürücü katı olarak kullanılır. Toplam hFE = hFE₁ × hFE₂ ile empedans dönüşümü sağlar.

## Devre Şeması

```
         Input (from Vbe Mult / VAS)
          │
          ▼
     ┌────┴────┐
     │  Q11a   │  Driver transistor (BD139)
     │  NPN    │  hFE₁ = 100
     │  B──C───┤─────────────┐
     │    E    │              │
     └────┬────┘              │
          │                   │
     [R13] 100Ω              │
          │                   │
     ┌────┴────┐              │
     │  Q9     │  MJL21194   │
     │ (Power) │  hFE₂ = 100
     │  B──C───┤─────────────┼──── +Vcc
     │    E    │              │
     └────┬────┘              │
          │                   │
         [R14] 0.22Ω          │
          │                   │
         Output               │
          │                   │
         Load ────────────────┘

Darlington Pair: hFE_total = hFE₁ × hFE₂
hFE_total = 100 × 100 = 10,000
```

## Teknik Spesifikasyonlar

| Parametre | Q11a (Driver) | Q9 (Power) | Not |
|---|---|---|---|
| Tip | BD139 | MJL21194 | NPN |
| Vceo | 80V | 250V | |
| Ic max | 1.5A | 16A | |
| hFE | 100-250 | 75-180 | @IC=1A |
| fT | 190MHz | 4MHz | |
| Paket | TO-126 | TO-264 | |
| P_max | 12.5W | 250W | |

## Hesaplamalar

### Toplam Akım Kazancı
```
hFE_total = hFE₁ × hFE₂
hFE_total = 100 × 100 = 10,000 (minimum)

Input akımı: I_B = I_out / hFE_total
I_B = 5A / 10,000 = 0.5mA (çıkış için gerekli)

VBE total = VBE₁ + VBE₂ = 0.65V + 0.65V = 1.3V
```

### Empedans Dönüşümü
```
R_in(Darlington) = hFE_total × R_emitter
R_in(Darlington) = 10,000 × 0.22Ω = 2,200Ω

Geri besleme giriş empedansı: 47kΩ
Empedans dönüşüm oranı: 47kΩ / 2.2kΩ = 21.4
```

### Slew Rate Etkisi
```
Darlington pole: f_p = fT₂ / hFE₁
f_p = 4MHz / 100 = 40kHz

Bu pole, Miller pole'den sonra gelir (15.9Hz)
Faz kaybı @ 40kHz: 90° (kritik)
Faz marjini ≥ 60° için compensation gerekli
```

### Güç Kaybı
```
Q11a güç: P₁ = Vce₁ × Ic₁ = (35V - 0.65V - 0.65V) × 1mA
P₁ = 33.7V × 1mA = 33.7mW

Q9 güç: P₂ = Vce₂ × Ic₂ = (35V - 1.1V) × 4A (peak)
P₂ = 33.9V × 4A = 135.6W (peak pulse)
Ortalama: P₂_avg ≈ 25W (Class AB, 50% duty)
```

## Alternatif Driver Transistörleri

| Transistör | Vceo | Ic | hFE | fT | Paket |
|---|---|---|---|---|---|
| BD139 | 80V | 1.5A | 100-250 | 190MHz | TO-126 |
| 2SA1837 | 230V | 0.5A | 100-320 | 200MHz | TO-220 |
| 2SC4883 | 230V | 0.5A | 100-320 | 200MHz | TO-220 |

## Ek Pedal Transistörleri (Baker Clamp)

```
     Q11a Collector
          │
          ▼
     ┌────┴────┐
     │  Q12    │  Baker clamp transistor
     │  1N4148│  (schottky diode equivalent)
     └────┬────┘
          │
         GND

Baker clamp: Collector-base junction'u
forward biased → saturated transistor'ı önler
→ Slew rate artırır
```

## Layout Kuralları

1. Q11a ve Q9 arasında kısa collector trace (minimum inductance)
2. Q11a base-emitter parasitic kapasitansını minimize
3. R13 ve R14 thermal runaway'e karşı close mounting
4. Darlington pair heatsink ile termal bağlantı

## Durum: Implementasyon
