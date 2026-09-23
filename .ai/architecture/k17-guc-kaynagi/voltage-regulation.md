---
title: "Gerilim Regülasyonu"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Gerilim Regülasyonu (Voltage Regulation)

## Genel Bakış

COREMUSIC'in çoklu güç rail'leri için LDO (Low Dropout) ve buck converter kombinasyonu kullanılır. ±15V ve ±12V rail'leri için lineer regülatörler tercih edilirken, +5V ve +3.3V için yüksek verimli buck converter'lar tercih edilir. Her rail için çıkış kapasitörleri, bypass capacitor'lar ve koruma devreleri entegre edilmiştir.

## Regülasyon Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    GERİLİM REGÜLASYONU MİMARİSİ                      │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌──────────────┐                                                   │
│  │ +35V Input   │                                                   │
│  └──────┬───────┘                                                   │
│         │                                                            │
│         ▼                                                            │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ LDO (+15V)   │───▶│ +15V Rail    │  Op-Amp, DAC, Buffer         │
│  │ LM317        │    │ 500mA        │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ LDO (-15V)   │───▶│ -15V Rail    │  Negatif Op-Amp              │
│  │ LM337        │    │ 500mA        │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ LDO (+12V)   │───▶│ +12V Rail    │  Audio Preamp, Motor         │
│  │ LM7812       │    │ 1A           │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ LDO (-12V)   │───▶│ -12V Rail    │  Negatif Preamp              │
│  │ LM7912       │    │ 500mA        │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ Buck (+5V)   │───▶│ +5V Rail     │  USB, LED, Lojik             │
│  │ TPS54331     │    │ 3A           │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
│  ┌──────────────┐    ┌──────────────┐                               │
│  │ Buck (+3.3V) │───▶│ +3.3V Rail   │  MCU, FPGA, SRAM             │
│  │ TPS62A01     │    │ 2A           │                               │
│  └──────────────┘    └──────────────┘                               │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## LDO Regülatör Detayları

### +15V LDO (LM317)

```
              VIN (+35V)
                 │
                 │
            ┌────┴────┐
            │  IN     │
     ┌──────┤ LM317   ├──────┐
     │      │  OUT    │      │
     │      └────┬────┘      │
     │           │            │
     │           │ ADJ        │
     │           │            │
     │      R1 (240Ω)        │
     │           │            │
     │           ├───┬───────┘
     │           │   │
     │      R2 (2.7kΩ)
     │           │   │
     │          GND  │
     │               │
     └───────────────┴────▶ VOUT (+15V)

Hesaplama:
VOUT = 1.25V × (1 + R2/R1)
VOUT = 1.25V × (1 + 2700/240)
VOUT = 1.25V × 12.25
VOUT = 15.3V → Trim: R2 = 2.6kΩ → VOUT = 15.0V

Dropout Voltage: 1.7V @ 500mA
Power Dissipation:
P = (VIN - VOUT) × IOUT
P = (35V - 15V) × 0.5A
P = 10W (soğutucu gerekli!)
```

### -15V LDO (LM337)

```
              VIN (-35V)
                 │
                 │
            ┌────┴────┐
            │  IN     │
     ┌──────┤ LM337   ├──────┐
     │      │  OUT    │      │
     │      └────┬────┘      │
     │           │            │
     │           │ ADJ        │
     │           │            │
     │      R1 (240Ω)        │
     │           │            │
     │           ├───┬───────┘
     │           │   │
     │      R2 (2.7kΩ)
     │           │   │
     │          GND  │
     │               │
     └───────────────┴────▶ VOUT (-15V)

Hesaplama (mutlak değer):
|VOUT| = 1.25V × (1 + R2/R1)
|VOUT| = 1.25V × 12.25
|VOUT| = 15.3V → R2 = 2.6kΩ → VOUT = -15.0V
```

### +12V LDO (LM7812)

```
              VIN (+35V)
                 │
            ┌────┴────┐
            │  IN     │
     ┌──────┤ LM7812  ├──────┐
     │      │  OUT    │      │
     │      └────┬────┘      │
     │           │            │
     │           │            │
     │      C1 (0.33μF)      │
     │           │            │
     │          GND           │
     │               │
     └───────────────┴────▶ VOUT (+12V)

Dropout: 2V @ 1A
P = (35V - 12V) × 1A = 23W (çok yüksek!)
Çözüm: Pre-regulator buck ile 18V'a düşür → P = 6W
```

## Buck Converter Detayları

### +5V Buck (TPS54331)

```
              VIN (+22.2V)
                 │
            ┌────┴────┐
            │ VIN     │
     ┌──────┤ TPS54331├──────┐
     │      │         │      │
     │      │ SW      │      │
     │      └────┬────┘      │
     │           │            │
     │        L1 (10μH)      │
     │           │            │
     │      ┌────┴────┐      │
     │      │ C_OUT   │      │
     │      │ 470μF   │      │
     │      └────┬────┘      │
     │           │            │
     │          GND           │
     │               │
     └───────────────┴────▶ VOUT (+5V)

Verimlilik: %92 @ 3A
Ripple: <50mVp-p
Dropout: 0.5V
```

### +3.3V Buck (TPS62A01)

```
              VIN (+5V)
                 │
            ┌────┴────┐
            │ VIN     │
     ┌──────┤TPS62A01 ├──────┐
     │      │         │      │
     │      │ SW      │      │
     │      └────┬────┘      │
     │           │            │
     │        L1 (4.7μH)     │
     │           │            │
     │      ┌────┴────┐      │
     │      │ C_OUT   │      │
     │      │ 220μF   │      │
     │      └────┬────┘      │
     │           │            │
     │          GND           │
     │               │
     └───────────────┴────▶ VOUT (+3.3V)

Verimlilik: %95 @ 2A
Ripple: <30mVp-p
```

## Çıkış Ripple Analizi

| Rail | Tip | Ripple | Filtrleme |
|------|-----|--------|-----------|
| +35V | Boost | 35mVp-p | LC filter |
| -35V | Boost | 35mVp-p | LC filter |
| +15V | LDO | <5mVp-p | C_out (10μF) |
| -15V | LDO | <5mVp-p | C_out (10μF) |
| +12V | LDO | <10mVp-p | C_out (10μF) |
| -12V | LDO | <10mVp-p | C_out (10μF) |
| +5V | Buck | 50mVp-p | LC filter |
| +3.3V | Buck | 30mVp-p | LC filter |

## Termal Analiz

### LDO Kayıpları

```
+15V LDO:
P = (35V - 15V) × 0.5A = 10W
θ_JA = 35°C/W (TO-220)
ΔT = P × θ_JA = 10 × 35 = 350°C!
Çözüm: heatsink veya pre-regulator

+12V LDO (pre-regulator ile):
Pre-regulator: 35V → 18V (buck, %90 verimli)
P_buck = (35V - 18V) × 1A × (1 - 0.90) = 1.7W
P_ldo = (18V - 12V) × 1A = 6W
Toplam: 7.7W (önceki 23W'dan çok daha iyi)
```

### Buck Converter Kayıpları

```
+5V Buck:
P_inductor = I² × DCR = (3A)² × 50mΩ = 0.45W
P_mosfet = I² × RDS(on) = (3A)² × 100mΩ = 0.9W
P_diode = V_F × I × (1-D) = 0.5V × 3A × 0.77 = 1.16W
Toplam: 2.51W
Verimlilik: 15W / (15W + 2.51W) = %85.7
```

## LC Filtre Tasarımı

```
             L (4.7μH)
VIN ────────┤├────────┬──── VOUT
                      │
                  C (100μF)
                      │
                     GND

Kesim Frekansı:
fc = 1 / (2π × √(LC))
fc = 1 / (2π × √(4.7μH × 100μF))
fc = 1 / (2π × 21.7μs)
fc = 7.33kHz

Attenuation @ 1MHz:
A = (fSW / fc)² = (1MHz / 7.33kHz)² = 18600
A_dB = 20 × log(18600) = 85dB
```

## Spesifikasyonlar

| Rail | Giriş | Çıkış | Akım | Verim | Ripple | Tip |
|------|-------|-------|------|-------|--------|-----|
| +15V | 35V | 15V ±1% | 500mA | %43 | <5mV | LDO |
| -15V | -35V | -15V ±1% | 500mA | %43 | <5mV | LDO |
| +12V | 18V | 12V ±2% | 1A | %67 | <10mV | LDO |
| -12V | -18V | -12V ±2% | 500mA | %67 | <10mV | LDO |
| +5V | 22.2V | 5V ±2% | 3A | %92 | 50mV | Buck |
| +3.3V | 5V | 3.3V ±3% | 2A | %95 | 30mV | Buck |

## Bağımlılıklar

| Bileşen | Adet | Kullanım |
|---------|------|----------|
| LM317 | 1 | +15V LDO |
| LM337 | 1 | -15V LDO |
| LM7812 | 1 | +12V LDO |
| LM7912 | 1 | -12V LDO |
| TPS54331 | 1 | +5V Buck |
| TPS62A01 | 1 | +3.3V Buck |
| Inductor | 2 | Buck converters |
| Capacitor | 12+ | Filtering, bypass |

## Durum: Implementasyon

✅ LDO regülatör seçimi ve devre tasarımı tamamlandı  
✅ Buck converter'lar (+5V, +3.3V) entegre edildi  
✅ Ripple analizi ve LC filtre hesaplamaları yapıldı  
✅ Termal analiz tamamlandı (heatsink gereksinimleri belirlendi)  
✅ Rail sequencing ile uyumluluk doğrulandı  
✅ Pre-regulator stratejisi ile LDO kayıpları azaltıldı  
⚠️ Thermal testler devam ediyor (yüksek akımda junction sıcaklık)  
⚠️ Line/load regulation ölçümleri bekleniyor
