---
title: "Thermal Shutdown Protection"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Thermal Shutdown Protection

## Genel Bakış

Termal kapatma koruması, heatsink sıcaklığı belirli bir eşiği aştığında amplifikatörü devreden çıkarır. KSD301 termostat sensörü, 85°C'de tetiklenerek röleyi de-enerjize eder. Isı dağılımı design'a göre termal senaryo analizi dahildir.

## Devre Şeması

```
       +12V (standby)
        │
       [R25] 4.7kΩ
        │
        ├─────────────────────────── To Relay Driver Q14 Base
        │
   ┌────┴────┐
   │  KSD301 │  Normally Closed (NC) termostat
   │  85°C   │  Opens at 85°C
   ├─────────┤
   │         │
   │  [R26]  │  R26: 10kΩ (pull-up)
   │    │    │
   │  [C2]   │  C2: 100nF (debounce)
   │    │    │
   └────┬────┘
        │
       GND

Heatsink mounted:
   ┌──────────────────┐
   │    KSD301        │  ← Thermal compound
   │  ┌──────────┐    │
   │  │  Sensor  │    │
   │  └──────────┘    │
   │    Heatsink      │
   │  ┌──────────┐    │
   │  │ MJL21194 │    │
   │  └──────────┘    │
   └──────────────────┘
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Sensör tipi | KSD301 | Bimetallic thermostat |
| Tetik sıcaklığı | 85°C ± 3°C | NC contact opens |
| Reset sıcaklığı | 65°C ± 5°C | Otomatik reset |
| Hysteresis | 20°C | False trigger önleme |
| Contact rating | 2A @ 250VAC | |
| Mounting | Heatsink yüzeyi | Thermal compound |
| Tepki süresi | 1-5 saniye | Termal zaman sabiti |
| Çalışma sıcaklığı | -30°C – +150°C | |

## Hesaplamalar

### Termal Senaryo (100W Continous)
```
Her transistör için ortalama güç:
P_avg = P_total / 2 = 50W (her biri)

Termal dirençler (seri):
R_θ(j-c) = 0.41°C/W  (transistor junction-case)
R_θ(c-s) = 0.50°C/W  (case-sink, thermal compound)
R_θ(s-a) = 1.50°C/W  (sink-ambient, heatsink)
R_θ_total = 2.41°C/W

Sıcaklık farkı:
ΔT = P × R_θ = 50W × 2.41°C/W = 120.5°C

Junction sıcaklığı:
T_j = T_ambient + ΔT = 25 + 120.5 = 145.5°C

Heatsink sıcaklığı:
T_s = T_ambient + P × (R_θ(c-s) + R_θ(s-a))
T_s = 25 + 50 × (0.5 + 1.5) = 25 + 100 = 125°C

KSD301 tetik: 85°C < 125°C → Koruma aktif ✓
```

### Termal Zaman Sabiti
```
Thermal capacity of heatsink:
C_θ = mass × specific_heat = 0.5kg × 900J/(kg·K) = 450J/K

Termal zaman sabiti:
τ = C_θ × R_θ(s-a) = 450J/K × 1.5°C/W = 675 saniye ≈ 11 dakika

KSD301 tetik süresi: ~1-5 saniye (bimetallic response)
```

### Koruma Aktifleme Senaryosu
```
t=0:    Fan durur veya load artar
t=30sn: T_sink = 25 + (50W/450J/K) × 30 = 28.3°C
t=60sn: T_sink = 31.7°C
t=5dk:  T_sink = 25 + 50 × 1.5 × (1 - e^(-300/675)) = 47.8°C
t=10dk: T_sink = 25 + 50 × 1.5 × (1 - e^(-600/675)) = 67.2°C
t=15dk: T_sink = 25 + 50 × 1.5 × (1 - e^(-900/675)) = 80.1°C
t=17dk: T_sink ≈ 85°C → KSD301 tetik ✓
```

## Çalışma Diyagramı

```
Sıcaklık (°C)
  150│× ← Junction max (150°C)
     │
  125│  × ← Heatsink normal çalışma
     │
  100│     ×
     │
   85│──────×────── KSD301 Tetik Sıcaklığı
     │       │
   65│───────│×──── KSD301 Reset Sıcaklığı
     │       │
   40│       │  ×  ← Normal çalışma
     │       │
   25│───────┼──── ×─── Oda sıcaklığı
     │       │
     └───────┼──────────▶ Zaman
          17dk  (örnek)

Koruma döngüsü:
1. T_sink > 85°C → Relay aç → Amplifikatör durur
2. T_sink < 65°C → Relay kapan → Amplifikatör çalışır
```

## Sensör Alternatifleri

| Sensör | Tetik | Paket | Avantaj |
|---|---|---|---|
| KSD301 | 85°C NC | TO-92 benzeri | Ucuz, güvenilir |
| KSD301 | 100°C NC | TO-92 | Yüksek eşik |
| NTC 10kΩ | Analog | THT/SMD | Continous monitoring |
| LM35 | Analog | TO-92 | ±0.5°C doğruluk |
| TMP36 | Analog | TO-92 | 10mV/°C çıkışı |

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| KSD301 | 85°C NC | G dây/cıvata mount | Heatsink montajı |
| R25 | 4.7kΩ | Metal film %1 | Pull-up |
| R26 | 10kΩ | Metal film %1 | |
| C2 | 100nF | Ceramic | Debounce |

## Layout Kuralları

1. KSD301 heatsink'in sıcak bölgesine mount
2. Thermal compound uygula (Arctic MX-6)
3. Sensor lead tellleri minimum 5cm (thermal isolation)
4. Vida montajı için heatsink'te M3 tap delik

## Durum: Implementasyon
