---
type: decision
id: "062"
title: "ADR-062: DSP Pipeline Architecture"
category: "audio"
status: "active"
date: "2026-08-09"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [electronics, dsp, pipeline, architecture, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-017-dsp-hardware-mode]]"
  - "[[decisions/accepted/ADR-025-professional-eq-system]]"
---

# ADR-062: DSP Pipeline Architecture

---

## 1. Executive Summary

CoreMusic DSP pipeline'ı **sıralı işlenme** stratejisi ile çalışır. Sinyal akışı: Input → EQ → Compressor → Limiter → Output.

## 2. Decision

### DSP Pipeline Akışı

```
Input → Gain → EQ (31-band) → Compressor → Limiter → Output
```

### DSP Modülleri

| # | Modül | Görev |
|---|-------|-------|
| 1 | Input Gain | Sinyal seviyesi |
| 2 | EQ (31-band) | Frekans ayarı |
| 3 | Compressor | Dinamik aralık |
| 4 | Limiter | Pik koruması |
| 5 | Output Gain | Çıkış seviyesi |
| 6 | Crossover | Bass management |
| 7 | Spatial Audio | Surround işleme |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Sıralı işlenme | ✅ Zorunlu |
| 2 | Float32 processing | ✅ Zorunlu |
| 3 | 48kHz sample rate | ✅ Zorunlu |
| 4 | Zero-allocation | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-062: DSP Pipeline Architecture v1.0.0 — CoreMusic Electronics*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
