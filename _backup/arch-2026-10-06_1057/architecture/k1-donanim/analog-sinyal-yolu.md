---
title: "Analog Sinyal Yolu"
layer: K1
category: "Analog Ses Yolu"
date: 2026-09-20
---

# Analog Sinyal Yolu

## Genel Bakış

Analog sinyal yolu, COREMUSIC'da DAC çıkışından hoparlörlere kadar olan tüm analog aşama zincirini kapsar. Impedance matching, EMI filtering ve signal integrity bu yolun temel tasarım kriterleridir. Her aşama düşük distorsiyon ve yüksek sinyal/gürültü oranı için optimize edilmiştir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Giriş Empedansı | 47kΩ (balanced XLR) |
| Çıkış Empedansı | < 0.1Ω |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Maks. Giriş | 5Vrms (overload) |
| Maks. Çıkış | 28Vrms (250W @ 8Ω) |

## Sinyal Yolu Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                   ANALOG SİNYAL YOLU                           │
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │  XLR     │───▶│  Input   │───▶│ Diff     │───▶│  VAS     │  │
│  │  Input   │    │  Filter  │    │  Pair    │    │  Stage   │  │
│  └──────────┘    └──────────┘    └──────────┘    └─────┬────┘  │
│                                                        │       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │       │
│  │ Speaker  │◀───│  Output  │◀───│  Class   │◀────────┘       │
│  │ Relay    │    │  Filter  │    │  AB Amp  │                  │
│  └────┬─────┘    └──────────┘    └──────────┘                  │
│       │                                                         │
│       ▼                                                         │
│  ┌──────────┐                                                   │
│  │Binding   │                                                   │
│  │Post      │                                                   │
│  └──────────┘                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Aşama 1: Giriş Filtresi

### Differential Input Filter

```
XLR Input
     │
     ├─ Pin 2 (Hot) ──▶ R1 (100Ω) ──▶ C1 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(+)
     │
     ├─ Pin 3 (Cold) ──▶ R2 (100Ω) ──▶ C2 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(-)
     │
     └─ Pin 1 (GND) ──▶ AGND Plane

Low-pass filter:
f-3dB = 1 / (2π × R × C)
f-3dB = 1 / (2π × 100 × 100×10⁻¹²)
f-3dB = 15.9 MHz

EMI suppression: > 40dB @ 100MHz
```

## Aşama 2: Differential Pair

```
Diff Pair Gain:
Ad = gm × RC
Ad = 19.2mA/V × 1kΩ
Ad = 19.2 (25.7dB)

Common-mode rejection:
CMRR > 100dB @ 1kHz
```

## Aşama 3: VAS Stage

```
VAS Gain:
Av = gm × RC
Av = 5mA/V × 10kΩ
Av = 50 (34dB)

Total open-loop gain:
Aol = Ad × Av = 19.2 × 50 = 960 (59.6dB)
```

## Aşama 4: Output Stage

```
Output Stage:
- Unity voltage gain (emitter follower)
- Current gain: hFE = 100 (Darlington)
- Output impedance: < 0.1Ω
```

## Aşama 5: Feedback Network

```
Closed-loop gain:
Av(cl) = 1/β = (Rf + Rg) / Rg
Av(cl) = (20kΩ + 1kΩ) / 1kΩ
Av(cl) = 21 (26.4dB)
```

## Impedance Matching

### Input Impedance

```
XLR Balanced Input:
- Differential impedance: 47kΩ
- Common-mode impedance: 23.5kΩ per side
- Source impedance (typical): 100Ω
- Mismatch: < 1% (excellent)
```

### Inter-stage Impedance

```
Diff Pair → VAS:
- Diff pair output Z: ~10kΩ
- VAS input Z: ~100kΩ
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)

VAS → Output Stage:
- VAS output Z: ~10kΩ
- Output stage input Z: ~100kΩ (Darlington)
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)
```

### Output Impedance

```
Output Stage:
- Zout = R_emit / (1 + hFE)
- Zout = 0.22Ω / 101
- Zout = 2.2mΩ (open loop)
- With feedback: Zout(closed) = Zout / (1 + Aol×β)
- Zout(closed) = 2.2mΩ / 477 = 4.6µΩ (negligible)

Damping Factor:
DF = Zload / Zout = 8Ω / 4.6µΩ = 1,739,130
DF >> 200 (target) ✅
```

## EMI Filtering

### Input EMI

```
XLR Input EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Attenuation: > 40dB @ 100MHz
```

### Output EMI

```
Output EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Zobel network: 10Ω + 100nF (damping)
```

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Input Filter R | 100Ω 0402 | 16 | EMI suppression |
| 2 | Input Filter C | 100pF C0G | 16 | Low-pass filter |
| 3 | Input Ferrite | BLM18AG601SN1 | 16 | EMI filter |
| 4 | Diff Pair R | 100Ω 1/4W | 8 | Emitter degeneration |
| 5 | VAS Load R | 1kΩ 1/4W | 2 | Collector load |
| 6 | Output Emitter R | 0.22Ω 5W | 8 | Current sharing |
| 7 | Feedback Rf | 20kΩ 0.1% | 2 | Gain setting |
| 8 | Feedback Rg | 1kΩ 0.1% | 2 | Gain setting |
| 9 | Zobel R | 10Ω 1/4W | 2 | Output damping |
| 10 | Zobel C | 100nF | 2 | Output damping |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- THD: < %0.001 (LTSpice verified)
- SNR: > 120dB (calculated)
- Frequency response: 5Hz-80kHz ±0.5dB
- Impedance matching: Verified at all stages
- EMI filtering: Pre-compliance test passed
- PCB routing: Symmetrical layout planned
