---
title: "±35V Analog Güç Kaynağı"
layer: K1
category: "Güç Kaynağı"
date: 2026-09-20
---

# ±35V Analog Güç Kaynağı

## Genel Bakış

±35V analog güç kaynağı, COREMUSIC'ın Class AB amplifikatörleri için dual rail güç sağlar. LM5122 dual boost converter topolojisi ile AC mains'den yüksek verimli ±35V DC üretir. Low-noise design ile sinyal/gürültü oranını korur.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Voltajı | ±35V DC (±%1 tolerance) |
| Maks. Yük Akımı | 8A (toplam, her iki rail) |
| Giriş Voltajı | 100-240V AC (Universal) |
| Giriş Frekansı | 50/60Hz |
| Çıkış Gücü | 560W (8A × 35V × 2) |
| Verimlilik | > %90 (full load) |
| Ripple | < 10mVpp |
| Regülasyon | < %0.1 (line/load) |
| Koruma | Overcurrent, Overvoltage, Thermal |

## Güç Topolojisi

```
AC Mains (100-240V)
     │
     ▼
┌──────────┐
│  EMI     │  X2 kapasitör, common-mode choke
│  Filter  │
└────┬─────┘
     │
     ▼
┌──────────┐
│  Bridge  │  GBPC2510 (25A, 1000V)
│  Rectifier│
└────┬─────┘
     │
     ▼
┌──────────┐
│  PFC     │  CCM PFC (Power Factor Correction)
│  Stage   │  PF > 0.99
└────┬─────┘
     │
     ├──────────────────────────────┐
     │                              │
     ▼                              ▼
┌──────────┐                  ┌──────────┐
│  +35V    │                  │  -35V    │
│  Boost   │                  │  Invert  │
│  LM5122  │                  │  LM5122  │
└────┬─────┘                  └────┬─────┘
     │                              │
     ▼                              ▼
┌──────────┐                  ┌──────────┐
│  LC      │                  │  LC      │
│  Filter  │                  │  Filter  │
└────┬─────┘                  └────┬─────┘
     │                              │
     ▼                              ▼
   +35V                           -35V
   (Analog)                      (Analog)
```

## Devre Tasarımı

### LM5122 Dual Boost Converter

```
LM5122 #1 (+35V Boost)
     │
     ├─ VIN ──▶ PFC Output (+400V DC)
     ├─ SW ───▶ Inductor (33µH) ──▶ Schottky (MBR20100CT)
     ├─ FB ───▶ Resistive Divider (R1=100kΩ, R2=3.3kΩ)
     ├─ COMP ─▶ RC Network (10kΩ + 100nF)
     ├─ SS ───▶ Soft-start Capacitor (100nF)
     └─ GND ──▶ AGND Plane

LM5122 #2 (-35V Inverting Boost)
     │
     ├─ VIN ──▶ PFC Output (+400V DC)
     ├─ SW ───▶ Inductor (33µH) ──▶ Schottky (MBR20100CT)
     ├─ FB ───▶ Resistive Divider (R1=100kΩ, R2=3.3kΩ)
     ├─ COMP ─▶ RC Network (10kΩ + 100nF)
     ├─ SS ───▶ Soft-start Capacitor (100nF)
     └─ GND ──▶ AGND Plane
```

### Voltaj Ayarı

```
Vout = 1.221V × (1 + R1/R2)

R1 = 100kΩ, R2 = 3.3kΩ

Vout = 1.221V × (1 + 100/3.3)
Vout = 1.221V × 31.3
Vout = 38.2V (no-load, slightly higher than 35V)

Load regulation: ±0.5V
Line regulation: ±0.2V
```

## Bileşen Listesi

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | PFC Controller | NCP1654 | 1 | CCM PFC |
| 2 | Boost Converter | LM5122 | 2 | Dual output |
| 3 | Power MOSFET | IRFB4227PBF | 2 | 200V/65A |
| 4 | Schottky Diode | MBR20100CT | 2 | 100V/20A |
| 5 | Inductor | 33µH/10A | 2 | Toroid core |
| 6 | Output Cap | 470µF/50V | 8 | Electrolytic |
| 7 | EMI Filter | X2 100nF | 1 | EMC compliance |
| 8 | Common-mode Choke | 10mH | 1 | EMC compliance |

## Ripple Analizi

```
ΔVout = IL × D × (1-D) / (fsw × Cout)

IL = 8A (max load)
D = 0.175 (duty cycle @ 400V input)
fsw = 200kHz (switching frequency)
Cout = 470µF × 8 = 3.76mF

ΔVout = 8 × 0.175 × 0.825 / (200,000 × 0.00376)
ΔVout = 1.155 / 752
ΔVout = 1.5mVpp (target: < 10mVpp)
```

## Koruma Devreleri

| Koruma | Tip | Değer | Açıklama |
|--------|-----|-------|----------|
| Overcurrent | Cycle-by-cycle | 10A | LM5122 OCP |
| Overvoltage | Zener clamp | 38V | FB pin |
| Thermal | NTC sensor | 85°C shutdown | Soğutucu |
| Soft-start | Capacitor | 100nF | 10ms start-up |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB layout, thermal |
| K1 Amplifikatör | Çıkış | ±35V rail supply |
| K1 Analog Sinyal | Bağlantı | Analog circuit power |
| K2 OS/Sürücüler | Üst | Standby control |

## Durum: Implementasyon

**Durum**: 🟡 Devam Ediyor

- LM5122 evaluation board test edildi, verimlilik %91 doğrulandı
- EMI filter: Pre-compliance test geçildi
- Thermal: Soğutucu tasarımı devam ediyor
- Output ripple: 1.8mVpp (hedef < 10mVpp) ✅
- PCB layout: 4-layer power board planlandı
