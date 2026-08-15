---
type: decision
id: "061"
title: "ADR-061: Electronics Architecture (L6 Layer)"
category: "audio"
status: "active"
date: "2026-08-09"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [electronics, architecture, l6, hardware, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-064-electronics-platform-architecture]]"
  - "[[architecture/l6-electronics]]"
---

# ADR-061: Electronics Architecture (L6 Layer)

---

## 1. Executive Summary

CoreMusic **L6 Electronics** katmanı, tüm donanım, firmware, driver ve DSP bileşenlerini yönetir. 5 cihaz ailesi ve 13 servis desteklenir.

## 2. Decision

### L6 Katman Bileşenleri

| Alt Katman | Kapsam |
|------------|--------|
| Hardware | PCB, DAC/ADC, amplifer |
| Firmware | XMOS, RTOS, OTA |
| Driver | ASIO, WASAPI, ALSA |
| DSP | EQ, Compressor, Limiter |
| Audio Engine | JUCE, ring buffer |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | L6 → L5 bağımlılığı | ✅ İzinli |
| 2 | L0 → L6 bağımlılığı | ❌ Yasak |
| 3 | Zero-allocation (audio thread) | ✅ Zorunlu |
| 4 | Lock-free (audio thread) | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-061: Electronics Architecture v1.0.0 — CoreMusic Electronics*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
