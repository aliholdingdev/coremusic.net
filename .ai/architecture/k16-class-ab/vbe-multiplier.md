---
title: "Vbe Multiplier Bias"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Vbe Multiplier Bias

## Genel Bakış

Vbe çoğaltıcı (Vbe multiplier), çıkış transistörleri için Class AB bias noktasını belirler. Çıkış stage'indeki crossover distorsiyonunu önlemek için gerekli DC offset voltajını üretir. Termal izleme ile sıcaklık değişimlerinde bias akımını otomatik olarak ayarlar.

## Devre Şeması

```
       +Vcc (+35V)
        │
       [R9] 2.2kΩ  (bias source direnci)
        │
        ├───────────────────── To Output Base (+)
        │
   ┌────┴────┐
   │   Q8    │  Q8: 2N5551 (NPN)
   │  C├─B   │  (Vbe multiplier)
   │    │    │
   │   [Rb]  │  Rb: 200Ω trimpot
   │    │    │  (bias ayarı)
   │   GND   │
   └────┬────┘
        │
        ├───────────────────── To Output Base (-)
        │
       [R10] 2.2kΩ  (bias sink direnci)
        │
      -Vee (-35V)

V_bias = Vbe_Q8 × (1 + Rb/R_π)
V_bias ≈ 0.65V × (1 + 200/∞) = 0.65V × N
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Topoloji | Vbe multiplier | Aktif bias |
| Q8 tipi | 2N5551 / MPSA06 | NPN, TO-92 |
| V_bias çıkış | 1.3V | 2 × Vbe |
| Bias akımı (idle) | 50-100mA | Trimpot ile ayarlanır |
| Termal katsayı | -2.2mV/°C | Isı izleme |
| Trimpot | 200Ω ± %10 | Bourns 3296W |
| R_bias | 2.2kΩ ± %1 | Metal film |
| Stabilite süresi | ≤30sn | Termal denge |

## Hesaplamalar

### Vbe Multiplier Kazancı
```
N = 1 + Rb / r_π
N = 1 + 200Ω / 3.1kΩ = 1.065

V_bias = Vbe × N = 0.65V × 1.065 ≈ 0.69V

Not: N=2 için 2×Vbe = 1.3V (iki transistör için)
```

### Idle Akımı Ayarı
```
I_idle = V_bias / (2 × R_emitter)
I_idle = 1.3V / (2 × 6.8Ω) ≈ 95.6mA

R_emitter: Çıkış transistörü emitter direnci (6.8Ω)
```

### Termal İzleme
```
ΔVbe/ΔT = -2.2mV/°C (silicon)
ΔI_bias/ΔT = -2.2mV/°C × N / R_total

Sıcaklık artışı → Vbe düşer → Bias düşer → Termal koruma
```

### DC Çalışma Noktası
```
Q8 Vce = Vcc - Vee = 70V (tamamen açık)
Q8 P = Vce × Ic = 70V × (I_bias/R9) = 70V × 0.32mA = 22mW
```

## Bias Ayar Prosedürü

1. **Ön koşul**: Amplifikatör ısısız (oda sıcaklığı)
2. **Giriş**: 0V DC, hiçbir sinyal yok
3. **Trimpot**: Rb'yi saat yönünde döndürerek I_idle'yi artır
4. **Ölçüm**: Emitter direnci üzerinde voltaj (V = I × R)
5. **Hedef**: V_R = 650mV → I_idle = 650mV/6.8Ω = 95.6mA
6. **Stabilite**: 30dk Termal denge beklenmeli
7. **Yeniden ölçüm**: I_idle ≤ 100mA olmalı

## Termal Koruma Mekanizması

```
Sıcaklık ↑
    ↓
Vbe ↓ (-2.2mV/°C)
    ↓
V_bias ↓
    ↓
I_idle ↓
    ↓
Güç ↑ engellenir
```

## Bileşen Seçimi

| Bileşen | Değer | Not |
|---|---|---|
| Q8 | 2N5551 | Vceo=160V, hFE≥100 |
| Rb (trimpot) | 200Ω | Bourns 3296W, ±%10 |
| R9 | 2.2kΩ | Metal film %1 |
| R10 | 2.2kΩ | Metal film %1 |

## Layout Kuralları

1. Q8 output transistörleri ile termal temas (thermal compound)
2. Trimpot kolay erişilebilir konumda
3. R9 ve R10 simetrik placement
4. Bias ayar vidalarına Seramik spacer

## Durum: Implementasyon
