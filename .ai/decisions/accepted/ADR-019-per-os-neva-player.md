---
title: "ADR-019: Per-OS Neva Player"
status: frozen
date: 2026-04-15
tags: [audio, player, neva, cross-platform, frozen]
---

# ADR-019: Per-OS Neva Player

---

## 1. Executive Summary

CoreMusic Neva Player, her iÅŸletim sistemi iÃ§in **platform-ses sÃ¼rÃ¼cÃ¼sÃ¼** kullanÄ±r: Windowsâ†’ASIO/WASAPI, Linuxâ†’ALSA/PipeWire, macOSâ†’CoreAudio, RPi5â†’I2S.

## 2. Decision

### Platform Ses SÃ¼rÃ¼cÃ¼leri

| Platform | SÃ¼rÃ¼cÃ¼ | Ã–ncelik |
|----------|--------|---------|
| Windows | ASIO, WASAPI | Tier 1 |
| Linux | ALSA, PipeWire | Tier 2 |
| macOS | CoreAudio | Tier 3 |
| RPi5 | I2S | Tier 4 |
| ReactOS | SÄ±nÄ±rlÄ± | Tier 5 |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | ASIO Windows'ta birincil | âœ… Zorunlu |
| 2 | WASAPI fallback | âœ… Zorunlu |
| 3 | Cross-platform audio API | âœ… Zorunlu |
| 4 | PCM5122 yasak (ADR-038) | âŒ Yasak |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-019: Per-OS Neva Player v2.0.0 â€” CoreMusic Audio*
*Authority: Embedded Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*