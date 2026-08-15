---
type: decision
id: "025"
title: "ADR-025: Professional EQ System (31-band)"
category: "audio"
status: "frozen"
date: "2026-05-10"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [audio, eq, equalizer, 31-band, professional, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-017-dsp-hardware-mode]]"
  - "[[projects/NevaEngine/equalizer-system]]"
---

# ADR-025: Professional EQ System

---

## 1. Executive Summary

CoreMusic'te **31-band parametrik EQ** sistemi bulunur. Her kanal için bağımsız EQ ayarları yapılabilir. AI ile otomatik EQ önerisi sunulur.

## 2. Decision

### EQ Özellikleri

| Özellik | Değer |
|---------|-------|
| Band sayısı | 31 |
| EQ tipi | Parametrik |
| Frekans aralığı | 20Hz - 20kHz |
| Q-factor | 0.1 - 10 |
| Gain | -24dB to +24dB |
| Preset desteği | ✅ |
| AI auto-EQ | ✅ |

### EQ Bantları

| Band | Frekans | Q |
|------|---------|---|
| 1 | 20Hz | 0.5 |
| 2 | 25Hz | 0.5 |
| 3 | 31.5Hz | 0.5 |
| ... | ... | ... |
| 31 | 20kHz | 0.5 |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 31-band parametrik EQ | ✅ Zorunlu |
| 2 | Per-channel EQ | ✅ Zorunlu |
| 3 | AI auto-EQ | ✅ Zorunlu |
| 4 | Preset yönetimi | ✅ Zorunlu |
| 5 | Real-time adjustment | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-025: Professional EQ System v2.0.0 — CoreMusic Audio*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
