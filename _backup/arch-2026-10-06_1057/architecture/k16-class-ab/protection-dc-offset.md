---
title: "DC Offset Koruma"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# DC Offset Koruma

## Genel Bakış

DC offset koruma devresi, çıkışta DC voltajı algıladığında hoparlörleri korumak için röleyi devreden çıkarır. ±0.5V DC eşik değeri aşıldığında 100ms içinde bağlantı kesilir. Hoparlör voice coil'unu DC'den korur.

## Devre Şeması

```
                    +Vcc
                     │
                    [R15] 100kΩ
                     │
        Output ───[R16] 10kΩ ──┐
                                │
                           ┌────┴────┐
                           │   Q13   │  Q13: BC547 (NPN)
                           │  2N3904 │  DC sense
                           ├────┬────┘
                           │   C    │
                           │  [R17] 10kΩ
                           │    │
                           │  [C1] 1μF (delay)
                           │    │
                           │  [R18] 100kΩ
                           │    │
                           └────┤
                                │
                           ┌────┴────┐
                           │  Q14    │  Q14: BC547
                           │  Driver │  Relay driver
                           ├────┬────┘
                           │   C    │
                           │  [D1]  1N4148 (flyback)
                           │    │
                     ┌─────┤  [RL1] Relay (12V, 30A)
                     │     │    │
                    +Vcc   │  [R19] 100Ω
                     │     │    │
                    [R20]  │   GND
                    4.7kΩ  │
                     │     │
                    GND    │
                           │
                         Output → Speaker

Relay coil: 12V DC, 150Ω
Contact rating: 30A @ 250VAC
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| DC eşik voltajı | ±0.5V | Hoparlör koruması |
| Tepki süresi | <100ms | C1 delay ile |
| Reset süresi | ≤500ms | Otomatik reset |
| Röle tipi | SPDT | 30A kontak |
| Röle bobini | 12V DC, 150Ω | 80mA |
| Gecikme kapasitörü | 1μF | C1 |
| Algılama direnci | 10kΩ | R16 |
| Güç tüketimi | 0.96W | Röle bobini |

## Hesaplamalar

### DC Algılama Eşiği
```
V_threshold = V_BE(Q13) × (1 + R17/R16) + V_R18
V_threshold = 0.65V × (1 + 10kΩ/10kΩ) + 0V = 1.3V

Q13 Base voltajı: V_B = V_out × R17 / (R16 + R17)
V_B = V_out × 10 / 20 = V_out / 2

V_out(eşik) = 2 × V_BE = 2 × 0.65V = 1.3V (teorik)
Pratik: ±0.5V (trimmer ile ayarlanır)
```

### Gecikme Süresi (RC Time Constant)
```
τ = R18 × C1 = 100kΩ × 1μF = 100ms

%63_charge: 100ms
%95_charge: 300ms
%99_charge: 460ms

DC algılama gecikmesi: 100ms
Röle Pull-in süresi: 5-10ms
Toplam tepki: 105-110ms
```

### Röle Gücü
```
P_relay = V_coil² / R_coil
P_relay = 12² / 150 = 0.96W

R19 Power: P = I² × R = (80mA)² × 100Ω = 0.64W
R19 seçimi: 1W metal film (thermal margin)
```

### Flyback Koruması
```
Relay bobin indüktansı: L ≈ 500mH
I_coil = 80mA
V_flyback = L × dI/dt = 500mH × 0.08A / 10ms = 400V (teorik)

D1 (1N4148): V_R = 100V, I_F = 300mA
Flyback enerji: E = ½LI² = ½ × 0.5 × 0.08² = 1.6mJ
D1 energy absorption: yeterli
```

## Çalışma Modları

### Normal Çalışma
```
1. DC offset = 0V
2. Q13 cutoff (V_B < 0.65V)
3. Q14 cutoff
4. Relay energize (normalde açık contact)
5. Hoparlör bağlı
```

### DC Algılama
```
1. DC offset ≥ 0.5V (pozitif veya negatif)
2. Q13 iletime geçer (V_B > 0.65V)
3. C1 şarj olur (100ms gecikme)
4. Q14 iletime geçer
5. Relay de-energize
6. Hoparlör bağlantısı kesilir
```

### Otomatik Reset
```
1. DC offset normale döner (|V_out| < 0.5V)
2. Q13 cutoff olur
3. C1 deşarj olur (R18 via)
4. Q14 cutoff olur
5. Relay energize olur
6. Hoparlör yeniden bağlanır
```

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| Q13 | BC547 | NPN, TO-92 | hFE ≥ 200 |
| Q14 | BC547 | NPN, TO-92 | Relay driver |
| D1 | 1N4148 | Schottky | Flyback koruması |
| R16 | 10kΩ | Metal film %1 | Algılama |
| R17 | 10kΩ | Metal film %1 | |
| R18 | 100kΩ | Metal film %1 | Gecikme |
| C1 | 1μF | Nichicon | Elektrolit |
| RL1 | 30A SPDT | Omron G2R | 12V bobin |

## Layout Kuralları

1. Röle kontaktları geniş trace (30A için 5mm)
2. Flyback diode röle bobinine yakın
3. DC sensing trace'i çıkış terminaline yakın
4. Gecikme kapasitörü Q13 base'e yakın

## Durum: Implementasyon
