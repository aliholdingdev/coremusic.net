---
title: "Koruma Devreleri"
layer: K1
category: "Güvenlik Sistemleri"
date: 2026-09-20
---

# Koruma Devreleri

## Genel Bakış

Koruma devreleri, COREMUSIC amplifikatörünü ve bağlı hoparlörleri DC offset, overcurrent, thermal runaway ve short circuit gibi zararlı durumlardan korur. Her koruma katmanı bağımsız çalışır ve fail-safe prensibine göre tasarlanmıştır.

## Teknik Spesifikasyonlar

| Koruma Tipi | Tetikleme | Gecikme | Yanıt |
|-------------|-----------|---------|-------|
| DC Offset | > ±1V DC | 0.5s | Speaker disconnect |
| Overcurrent | > 10A peak | Anında | Current limiting |
| Thermal Shutdown | > 85°C | 5s | Speaker disconnect |
| Short Circuit | 0Ω load | Anında | Current limiting |
| Overvoltage | > ±40V | 1ms | PSU shutdown |
| Mains Fuse | > 3A AC | 10ms | Fuse blown |

## DC Offset Koruması

### Devre Şeması

```
Output Node (Amplifier Output)
     │
     ├─ C1 ──▶ 10µF (DC block)
     │           │
     │       ┌───┴───┐
     │       │  R1   │  100kΩ
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │  D1   │  BAT54S (±0.7V clamp)
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │ Comp  │  Comparator (LM393)
     │       │       │
     │       └───┬───┘
     │           │
     │           ├────▶ Relay Driver (BC337)
     │           │         │
     │           │         ▼
     │           │    Speaker Relay
     │           │    (disconnect)
     │           │
     │           └────▶ Fault LED (Red)
     │
     └─ R2 ──▶ Feedback Network
```

### Çalışma Prensibi

```
1. Amplifikatör çıkışı DC coupled olarak comparator girişine bağlanır
2. C1 (10µF) DC component'i ayırır
3. Comparator, çıkış voltajını ±1V referans ile karşılaştırır
4. |Vout| > 1V olduğunda comparator çıkışı HIGH olur
5. BC337 MOSFET'i aktif eder ve speaker relay'ı açar
6. 0.5s gecikme (RC time constant) ile yanlış tetikleme önlenir
```

## Overcurrent Koruması

### Devre Şeması

```
Current Sense Resistor (R_sense = 0.22Ω)
     │
     ├─ V_sense = I_load × R_sense
     │
     │   @ 10A: V_sense = 2.2V
     │
     └─▶ Current Sense Amplifier (INA213)
              │
              ├─ Gain = 50
              │   Vout = 50 × V_sense
              │
              │   @ 10A: Vout = 110V (exceeds comparator ref)
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 2.5V (5A limit)
                      │
                      └─▶ Current Limit Driver
                              │
                              ▼
                         MOSFET Gate
                         (reduce drive)
```

### Current Limiting Profile

| Akım Seviyesi | Davranış | Süre |
|---------------|----------|------|
| < 5A | Normal çalışma | Sürekli |
| 5-8A | Soft limiting | 10ms |
| 8-10A | Hard limiting | Anında |
| > 10A | Shutdown | 100µs |

## Thermal Shutdown

### Devre Şeması

```
NTC Thermistor (10kΩ @ 25°C)
     │
     └─▶ Voltage Divider
              │
              ├─ R_pullup = 10kΩ
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 0.35V (85°C)
                      │
                      └─▶ RC Delay (5s)
                              │
                              └─▶ Latch Circuit
                                      │
                                      ├─▶ Speaker Relay (disconnect)
                                      │
                                      └─▶ PSU Enable (shutdown)
```

### Thermal Profile

| Sıcaklık (°C) | Davranış |
|---------------|----------|
| < 70 | Normal çalışma |
| 70-80 | Uyarı LED (sarı) |
| 80-85 | Fan maksimum hız |
| > 85 | Thermal shutdown |
| < 75 | Otomatik restart |

## Short Circuit Koruması

### Çift Koruma

```
1. Output Current Limiting:
   - R_sense = 0.22Ω
   - Max current: 10A
   - Response time: < 1µs

2. Foldback Current Limiting:
   - Vout < 5V: Full current allowed (10A)
   - Vout = 0V (short): Current reduced to 3A
   - Vout = 35V: Current = 0A (open circuit)
```

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Current Sense | 0.22Ω 5W | 2 | Output sensing |
| 2 | Current Amp | INA213 | 2 | Current sense amplifier |
| 3 | Comparator | LM393 | 4 | Dual comparator |
| 4 | Relay | Finder 40.52 | 2 | 30A DPDT |
| 5 | Relay Driver | BC337 | 2 | NPN driver |
| 6 | NTC Sensor | 10kΩ NTC | 2 | Temperature sensing |
| 7 | Fuse | 3A AC | 1 | Mains protection |
| 8 | TVS Diode | SMBJ36A | 2 | Overvoltage clamp |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Amplifikatör | Bağlantı | Output sensing |
| K1 Termal | Bağlantı | NTC sensor |
| K1 Güç Kaynağı | Bağlantı | PSU shutdown |
| K1 Hoparlör | Çıkış | Speaker relay |
| K2 OS/Sürücüler | Üst | Fault reporting |

## Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- DC offset koruması: Şematik hazır, layout yok
- Overcurrent: INA213 evaluation board test edildi
- Thermal shutdown: NTC sensor seçimi yapıldı
- Short circuit: Foldback design LTSpice'da simulate edildi
- Relay: Finder 40.52 (30A DPDT) seçildi
- PCB: Koruma devresi için ayrı area planlandı
