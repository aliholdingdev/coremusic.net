---
title: "Differential Pair Input Stage"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Differential Pair Input Stage

## Genel Bakış

Fark (differansiyel) çift giriş katı, amplifikatörün giriş empedansını belirler, CMRR (Ortak Mod Reddi) sağlar ve geri besleme sinyali ile giriş sinyalini karşılaştırır. Long-tail pair topolojisi, yüksek giriş empedansı ve düşük gürültü sağlar.

## Devre Şeması

```
          +Vcc (+35V)
           │
          [R5] 1mA
           │
           ├─────────────────────── Output (to VAS)
           │
      ┌────┴────┐
      │   Q1    │  Q1: 2SA1015 (PNP, hFE≥100)
      │  (NPN)  │  Vbe = -0.65V @ 1mA
      ├────┬────┤
      │   Q2    │  Q2: 2SA1015 (PNP, hFE≥100)
      └────┬────┘
           │
          [R4] 100Ω  (tail resistor, 1.7mA)
           │
          -Vee (-35V)

Input + ──[Rin 47kΩ]──▶ Q1 Base
Input - ──[Rin 47kΩ]──▶ Q2 Base  (Feedback from output)

          C_in = 10pF (stray + compensation)
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Transistör tipi | 2SA1015 / 2N5401 | PNP low-noise |
| Tail akımı (Iss) | 1.7mA | R4 = 100Ω × (35V-0.65V) / R4 |
| Transistör akımı (Ic) | 0.85mA her biri | Iss/2 |
| Transconductance (gm) | 33mA/V @ 0.85mA | gm = Ic/Vt |
| Giriş empedansı | 47kΩ | R_in parallel dif. pair |
| CMRR | ≥80dB | Simetrik layout ile |
| Giriş offset voltajı | ≤5mV | Vbe eşleştirme |
| Gürültü voltajı | ≤1nV/√Hz | Low-noise PNP seçimi |
| PSRR | ≥90dB | Tail akım kaynağı |

## Hesaplamalar

### Tail Akımı
```
I_tail = (Vee - Vbe_Q1) / R_tail
I_tail = (35 - 0.65) / 20.2kΩ ≈ 1.7mA
```

### Transconductance (gm)
```
gm = Ic / Vt
gm = 0.85mA / 26mV ≈ 33mA/V

Vt = kT/q = 26mV @ 25°C (termal gerilim)
```

### Diferansiyel Kazanç (Ad)
```
Ad = gm × (R_C || r_o)
Ad = 33mA/V × 10kΩ ≈ 330 (50.4dB)
```

### CMRR Hesabı
```
CMRR = Ad / Acm
Acm ≈ gm × R_C / (2 × gm × R_tail × Δgm/gm)

Δgm/gm tolerans: ≤%5 (eşleştirme)
CMRR ≥ 80dB hedef
```

### Giriş Empedansı
```
R_in = r_π = β × Vt / Ic
r_π = 100 × 26mV / 0.85mA ≈ 3.1kΩ (transistör içi)
R_in_toplam = R_source || r_π || R_bias_network ≈ 47kΩ
```

## Bileşen Seçim Kriterleri

| Bileşen | Seçim Nedeni |
|---|---|
| 2SA1015 | Düşük gürültü (4nV/√Hz), PNP, TO-92, hFE 100-600 |
| R_tail (100Ω) | Düşük tolerans %1, metal film |
| R_in (47kΩ) | %1 tolerans, metal film, düşük termal drift |

## Termal Hassasiyet

- **ΔVbe/ΔT**: -2.2mV/°C (her transistör için)
- **Eşleştirme**: Q1 ve Q2 sıcaklıkta eşit olmalı (termal layout)
- **Drift**: ≤50μV/°C giriş offset (eşlenmiş pair)

## Layout Kuralları

1. Q1 ve Q2 arasında minimum 2mm mesafe (termal eşitlik)
2. Symetrik traces, eşit uzunluk
3. Tail resistor'a yakın GND plane bağlantısı
4. Giriş pad'lerinden-guard ring (guard ring for leakage)

## Durum: Implementasyon
