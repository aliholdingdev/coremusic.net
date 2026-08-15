---
type: decision
id: "019"
title: "ADR-019: Per-OS Neva Player"
category: "audio"
status: "frozen"
date: "2026-04-15"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [audio, player, neva, cross-platform, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-017-dsp-hardware-mode]]"
---

# ADR-019: Per-OS Neva Player

---

## 1. Executive Summary

CoreMusic Neva Player, her işletim sistemi için **platform-ses sürücüsü** kullanır: Windows→ASIO/WASAPI, Linux→ALSA/PipeWire, macOS→CoreAudio, RPi5→I2S.

## 2. Decision

### Platform Ses Sürücüleri

| Platform | Sürücü | Öncelik |
|----------|--------|---------|
| Windows | ASIO, WASAPI | Tier 1 |
| Linux | ALSA, PipeWire | Tier 2 |
| macOS | CoreAudio | Tier 3 |
| RPi5 | I2S | Tier 4 |
| ReactOS | Sınırlı | Tier 5 |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | ASIO Windows'ta birincil | ✅ Zorunlu |
| 2 | WASAPI fallback | ✅ Zorunlu |
| 3 | Cross-platform audio API | ✅ Zorunlu |
| 4 | PCM5122 yasak (ADR-038) | ❌ Yasak |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-019: Per-OS Neva Player v2.0.0 — CoreMusic Audio*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
