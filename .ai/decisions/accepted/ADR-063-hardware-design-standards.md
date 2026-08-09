---
type: decision
category: hardware-design
title: "ADR-063: Hardware Design Standards"
date: 2026-08-09
status: active
version: 1.0.0
---

# ADR-063: Hardware Design Standards

## Status

Active — 2026-08-09

## Context

CoreMusic ELECTRONICS platformu için donanım tasarım standartlarının tanımlanması gerekiyor. Audio interface, amplifier, PCB design, measurement standards.

## Decision

Donanım tasarım standartları:

| Standart | Değer |
|----------|-------|
| Audio Interface | XMOS XU316 + PCM3168A |
| Amplifier | Class AB, 100W @ 8Ω |
| 8+1 Surround | MAX 2000W 8Ω |
| PCB | 4-katman, analog/dijital ayırma |
| SNR | >100dB (A-wtd) |
| THD+N | <0.01% (1kHz, 1W) |
| Frekans Yanıtı | 20Hz-20kHz (±0.5dB) |

## Consequences

- PCM5122 REDDEDİLMİŞ (H001) — sadece PCM3168A
- Toroidal transformer (500VA, 8+1 kanal için)
- DC Offset koruma rölesi (±0.5V limit)
- OTA firmware update (RSA-2048 imza, AES-256-GCM şifreleme)

## Related

- [[ADR-038-8.1-sound-card-chip-selection]]
- [[ADR-017-dsp-hardware-mode]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
