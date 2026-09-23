---
title: "Push-Pull Output Stage"
layer: K1
category: "Güç Çıkış Evresi"
date: 2026-09-20
---

# Push-Pull Output Stage

## Genel Bakış

Push-pull output stage, amplifikatörün son güçlendirme evresidir. VAS'tan gelen sinyali düşük empedanslı çıkışa dönüştürerek hoparlörü sürer. Darlington configuration ile yüksek akım kazancı, thermal tracking ile crossover distortion minimizasyonu sağlanır.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Empedansı | < 0.1Ω |
| Damaping Faktörü | > 200 (8Ω) |
| Maks. Çıkış Akımı | 16A (peak) |
| Idle Bias Akımı | 50mA (Class AB) |
| Çıkış Gücü | 250W RMS @ 8Ω |
| Crossover Distortion | < %0.001 (1kHz) |
| Slew Rate | > 100V/µs |

## Push-Pull Konfigürasyon

### Tek transistor (Single-ended output)

```
Tek transistor output stage sadece Class A veya Class B modunda
çalışabilir. Class AB için push-pull (takviyeli) konfigürasyon gerekir.
```

### Push-Pull Output

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R5   │  0.22Ω 5W (Emitter)
                        └───┬───┘
                            │
                        ┌───┴───┐
                        │  Q1   │  MJL21194 (NPN)
                        │       │  250W/200V/16A
                        └───┬───┘
                            │
                            │
                    ┌───────┴───────┐
                    │   OUTPUT NODE │──── To Speaker
                    │               │
                        ┌───┴───┐
                        │  Q2   │  MJL21193 (PNP)
                        │       │  250W/200V/16A
                        └───┬───┘
                            │
                        ┌───┴───┐
                        │  R6   │  0.22Ω 5W (Emitter)
                        └───┬───┘
                            │
                           -35V
```

## Darlington Configuration

### Neden Darlington?

```
Tek bir power transistör (MJL21194) için gereken base akımı:

IB = IC / hFE
IB = 8A / 100 = 80mA

Bu akım VAS'tan doğrudan sağlanamaz.
Darlington konfigürasyonu ile equivalent hFE yükseltilir:
hFE(total) = hFE1 × hFE2 = 100 × 100 = 10,000

IB = 8A / 10,000 = 0.8mA (VAS'tan kolayca sağlanabilir)
```

### Darlington Emitters Follower

```
                    Collector (Common)
                         │
                    ┌────┴────┐
                    │  Q_drv  │  Driver Transistor
                    │ (BD139) │  NPN
                    └────┬────┘
                         │
                         │  Base
                         │
                    ┌────┴────┐
                    │  Q_out  │  Output Transistor
                    │(MJL21194)│  NPN Power
                    └────┬────┘
                         │
                         │  Emitter
                         │
                    ┌────┴────┐
                    │  R_emit │  0.22Ω
                    └────┬────┘
                         │
                       Output
```

## Thermal Tracking

### Crossover Distortion Sorunu

```
Class AB amplifikatörde, crossover bölgesinde her iki transistör
de yarı-iletken modunda çalışır. Sıcaklık değişimleri VBE'yi
değiştirir ve bias akımını etkiler.

Sıcaklık artışı → VBE azalır → Bias akımı artar → Termal runaway riski
```

### Çözüm: Thermal Tracking

```
                    +35V
                     │
                 ┌───┴───┐
                 │  R7   │  10kΩ
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │  Q_th │  BD139 (NPN)
                 │       │  Soğutucuya monte edilmiş
                 └───┬───┘
                     │
                     ├────────────────── Bias Point
                     │
                 ┌───┴───┐
                 │  R8   │  100Ω
                 └───┬───┘
                     │
                    -35V

Q_th, soğutucu ile aynı sıcaklıktadır.
Sıcaklık arttığında VBE azalır ve bias akımı otomatik olarak ayarlanır.
```

## Bileşen Değerleri

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q1 | MJL21194 | NPN Power | Çıkış (+) |
| Q2 | MJL21193 | PNP Power | Çıkış (-) |
| Q3 | BD139 | NPN Driver | Darlington driver (+) |
| Q4 | BD140 | PNP Driver | Darlington driver (-) |
| Q5 | BD139 | NPN | Thermal tracking |
| R5, R6 | 0.22Ω 5W | Wirewound | Emitter dirençleri |
| R7 | 10kΩ 1/4W | Metal Film | Bias network |
| R8 | 100Ω 1/4W | Metal Film | Bias network |

## Akım Yolu Analizi

### Pozitif Yarım Döngü (MJL21194 Active)

```
+35V → R5 → Q1(MJL21194) Collector → Q1 Emitter → R_out → Speaker → GND

Peak akım: IC = 5.6A (250W @ 8Ω)
DC akım: IC = 3.5A (100W @ 8Ω)
```

### Negatif Yarım Döngü (MJL21193 Active)

```
GND → Speaker → R_out → Q2(MJL21193) Emitter → Q2 Collector → R6 → -35V

Peak akım: IC = 5.6A
DC akım: IC = 3.5A
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 VAS Stage | Giriş | VAS çıkış sinyali |
| K1 Feedback | Geri besleme | Çıkış geri besleme noktası |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Bağlantı | Overcurrent, DC offset |
| K1 Hoparlör | Çıkış | Speaker binding posts |
| K1 Güç Kaynağı | Alt | ±35V dual rail |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı
- Thermal runaway analizi: Stabil (dTC/dt < 0)
- Emitter dirençleri: 0.22Ω wirewound (5W, %1 tolerance)
- PCB placement: Symmetrical, short traces to output connector
- Heatsink: Alüminyum ekstrüzyon, 150mm, 2°C/W
