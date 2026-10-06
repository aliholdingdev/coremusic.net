---
title: "DAC → ADC Sinyal Zinciri"
layer: K1
category: "Sinyal İşleme"
date: 2026-09-20
---

# DAC → ADC Sinyal Zinciri

## Genel Bakış

DAC → ADC sinyal zinciri, COREMUSIC'da dijital sinyalin analog forma dönüştürülmesinden sonra tekrar dijital alana dönüştürülmesine kadar olan süreci tanımlar. Clock synchronization, impedance matching ve EMI filtering bu zincirde kritik öneme sahiptir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| DAC | AK4458 (32-bit, 8-kanal) |
| ADC | PCM3168A (32-bit, 8-kanal) |
| Clock Frequency | 22.5792MHz (44.1kHz family) / 24.576MHz (48kHz family) |
| I2S Bit Clock | 1.4112MHz (44.1kHz) / 1.536MHz (48kHz) |
| Word Select | 44.1kHz / 48kHz |
| MCLK | 256fs = 11.2896MHz / 12.288MHz |
| Sinyal Seviyesi | 2.1Vrms (differential) |
| Empedans | 100Ω differential |

## Sinyal Yolu Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                     DAC → ADC SİNYAL ZİNCİRİ                   │
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │  XMOS    │───▶│  I2S     │───▶│   DAC    │───▶│  Analog  │  │
│  │  XU316   │    │  Bus     │    │  AK4458  │    │  Output  │  │
│  └────┬─────┘    └──────────┘    └──────────┘    └─────┬────┘  │
│       │                                                 │       │
│       │         ┌──────────┐    ┌──────────┐           │       │
│       └────────▶│  Clock   │◀───│  Crystal │           │       │
│                 │  Sync    │    │  Osc.    │           │       │
│                 └──────────┘    └──────────┘           │       │
│                                                        │       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │       │
│  │  ADC     │◀───│  I2S     │◀───│  Analog  │◀────────┘       │
│  │ PCM3168A │    │  Bus     │    │  Input   │                  │
│  └────┬─────┘    └──────────┘    └──────────┘                  │
│       │                                                         │
│       ▼                                                         │
│  ┌──────────┐                                                   │
│  │  DSP     │  Dijital İşleme                                  │
│  │  Engine  │                                                   │
│  └──────────┘                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Clock Synchronization

### Master/Slave Konfigürasyonu

```
Clock Hierarchy:

Primary Clock Source: XMOS XU316 (Master)
     │
     ├─ MCLK Output ──▶ AK4458 SCKI (DAC Master Clock)
     │                   PCM3168A SCKI (ADC Master Clock)
     │
     ├─ SCK Output ──▶ AK4458 TDMCLK (Bit Clock)
     │                  PCM3168A BCK (Bit Clock)
     │
     ├─ WS Output ──▶ AK4458 TDMFS (Word Select)
     │                 PCM3168A LRCK (LR Clock)
     │
     └─ SD0-SD3 ──▶ AK4458 TDMD0-3 (Data)
                     PCM3168A DOUTA/B (Data)

Clock Distribution:
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  XMOS XU316 (Master)                                   │
│  ├─ MCLK (22.5792MHz) ──────────────────────────┐     │
│  ├─ SCK (1.4112MHz) ────────────────────────┐   │     │
│  ├─ WS (44.1kHz) ──────────────────────┐   │   │     │
│  └─ SD[0:3] ──────────────────────┐   │   │   │     │
│                                   │   │   │   │     │
│  AK4458 (Slave) ◀────────────────┘   │   │   │     │
│  ├─ SCKI ◀──────────────────────────┘   │   │     │
│  ├─ TDMCLK ◀────────────────────────────┘   │     │
│  ├─ TDMFS ◀──────────────────────────────────┘     │
│  └─ TDMD[0:3] ◀─────────────────────────────────────┘
│                                                         │
│  PCM3168A (Slave)                                      │
│  ├─ SCKI ◀──────────────────────────────────────────────┘
│  ├─ BCK ◀───────────────────────────────────────────────┘
│  ├─ LRCK ◀──────────────────────────────────────────────┘
│  └─ DOUTA/B ───────────────────────────────────────────▶ XMOS
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Clock Accuracy

| Clock | Frequency | Tolerance | Jitter |
|-------|-----------|-----------|--------|
| MCLK | 22.5792MHz | ±50ppm | < 100ps RMS |
| SCK | 1.4112MHz | ±50ppm | < 100ps RMS |
| WS | 44.1kHz | ±50ppm | < 100ps RMS |

## EMI Filtreleme

### I2S Hat Filtresi

```
XMOS Output ──▶ Ferrite Bead (600Ω @ 100MHz) ──▶ 100nF ──▶ DAC/ADC

Her I2S hattı için:
- Ferrite bead: BLM18AG601SN1 (600Ω, 0603)
- Decoupling: 100nF MLCC (0402)
- Trace length: < 50mm
- Impedans: 90Ω differential
```

## Empedans Eşleşme

| Junction | Source Z | Load Z | Matched? |
|----------|----------|--------|----------|
| XMOS → DAC | 50Ω | 100Ω | No (high-Z input) |
| DAC → ADC | 25Ω | 10kΩ | No (voltage mode) |
| ADC → DSP | 100Ω | 50Ω | Yes (differential) |

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Decoupling Cap | 100nF MLCC | 16 | I2S dekuplajı |
| 3 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 4 | Crystal | 24.576MHz | 1 | 48kHz family |
| 5 | Load Cap | 18pF C0G | 4 | Crystal load |
| 6 | Termination | 100Ω | 8 | I2S termination |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock | Master clock source |
| K1 DAC | Çıkış | AK4458 analog output |
| K1 ADC | Giriş | PCM3168A digital output |
| K3 DSP | Üst | Dijital sinyal işleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Clock synchronization: LTSpice ile simulate edildi
- I2S timing: Eye diagram analizi yapıldı
- EMI: Pre-compliance test ile ferrite bead seçimi doğrulandı
- Crystal: Dual crystal (22.5792MHz + 24.576MHz) seçildi
- PCB routing: I2S traces length-matched (±1mm tolerance)
