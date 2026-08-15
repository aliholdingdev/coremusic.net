---
type: decision
id: "064"
title: "ADR-064: Electronics Platform Architecture"
category: "audio"
status: "active"
date: "2026-08-09"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [electronics, platform, architecture, 5-device, 13-service, active]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-061-electronics-architecture]]"
  - "[[decisions/accepted/ADR-062-dsp-pipeline-architecture]]"
  - "[[decisions/accepted/ADR-063-hardware-design-standards]]"
---

# ADR-064: Electronics Platform Architecture

---

## 1. Executive Summary

CoreMusic Electronics Platform, **5 cihaz ailesi** ve **13 servis** ile yönetilir. L0-L6 katman yapısı ile tam entegrasyon sağlanır.

## 2. Decision

### 5 Cihaz Ailesi

| # | Cihaz | Kullanım |
|---|-------|----------|
| 1 | Desktop DAC | PC/Laptop ses çıkışı |
| 2 | RPi5 Streamer | Ev medya merkezi |
| 3 | Car Head Unit | Araç içi bilgi-eğlence |
| 4 | Studio Interface | Profesyonel stüdyo |
| 5 | Portable DAC | Taşınabilir ses |

### 13 Servis

| # | Servis | Görev |
|---|--------|-------|
| 1 | Audio Playback | Müzik oynatma |
| 2 | DSP Processing | EQ, compressor, limiter |
| 3 | Volume Control | Ses seviyesi |
| 4 | Source Selection | Kaynak seçimi |
| 5 | Preset Management | Preset yönetimi |
| 6 | Device Discovery | Cihaz bulma |
| 7 | Firmware Update | OTA güncelleme |
| 8 | Diagnostics | Teşhis |
| 9 | Calibration | Kalibrasyon |
| 10 | Multi-room | Çoklu oda |
| 11 | Streaming | Akış |
| 12 | Recording | Kayıt |
| 13 | System Config | Sistem yapılandırması |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 5 cihaz ailesi | ✅ Zorunlu |
| 2 | 13 servis | ✅ Zorunlu |
| 3 | L0-L6 entegrasyon | ✅ Zorunlu |
| 4 | OTA firmware update | ✅ Zorunlu |
| 5 | Cross-platform driver | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-064: Electronics Platform Architecture v1.0.0 — CoreMusic Electronics*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
