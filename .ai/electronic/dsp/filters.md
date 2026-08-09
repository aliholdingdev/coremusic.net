---
type: electronic
category: dsp-filters
title: "CoreMusic — Filter Systems (FIR, IIR, FFT)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Filter Systems

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Filter Systems, CoreMusic DSP Engine'deki FIR, IIR ve FFT tabanlı filtreleme mekanizmalarını tanımlar.

---

## 2. High Pass Filter

Düşük frekansları keser.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Cutoff Frequency | 20Hz - 200Hz | 80Hz |
| Order | 1 - 8 | 2 |
| Slope | 6dB/oct - 48dB/oct | 12dB/oct |
| Type | Butterworth / Linkwitz-Riley | Butterworth |

---

## 3. Low Pass Filter

Yüksek frekansları keser.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Cutoff Frequency | 2kHz - 20kHz | 18kHz |
| Order | 1 - 8 | 2 |
| Slope | 6dB/oct - 48dB/oct | 12dB/oct |
| Type | Butterworth / Linkwitz-Riley | Butterworth |

---

## 4. FIR Filter (Finite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Taps | 64 - 4096 |
| Phase | Linear phase |
| Kullanım | High-precision EQ, room correction |
| CPU | Yüksek (convolution) |
| Latency | Taps/2 samples |

---

## 5. IIR Filter (Infinite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Order | 1 - 8 |
| Phase | Non-linear |
| Kullanım | Real-time EQ, crossover |
| CPU | Düşük |
| Latency | Minimum |

---

## 6. FFT (Fast Fourier Transform)

| Özellik | Değer |
|---------|-------|
| Size | 512 - 8192 |
| Window | Hanning, Hamming, Blackman |
| Overlap | 50% - 75% |
| Kullanım | Frequency analysis, spectral EQ |

---

## 7. Crossover Filters

| Özellik | Değer |
|---------|-------|
| Type | Linkwitz-Riley 4th order |
| Slope | 24dB/octave |
| Crossover Point | 80Hz (varsayılan) |
| Phase | Coherent (sum flat) |

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-025-professional-eq-system]] | EQ sistemi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
