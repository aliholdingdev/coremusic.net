---
type: decision
category: electronics-architecture
title: "ADR-061: Electronics Architecture (L6 Layer)"
date: 2026-08-09
status: active
version: 1.0.0
---

# ADR-061: Electronics Architecture

## Status

Active — 2026-08-09

## Context

CoreMusic ELECTRONICS platformu için L6 Electronics katmanı oluşturulması gerekiyor. DSP Engine, Driver Framework, Amplifier Architecture, Hardware Design ve Firmware Architecture modülleri tanımlanmalı.

## Decision

L6 Electronics katmanı `electronic/` dizininde 5 alt modül olarak yapılandırıldı:

| Modül | Dizin | Amaç |
|-------|-------|------|
| DSP Engine | `electronic/dsp/` | Ses işleme pipeline'ı |
| Driver Framework | `electronic/drivers/` | Sürücü entegrasyonu |
| Amplifier Architecture | `electronic/amplifier/` | Amfi topolojisi |
| Hardware Design | `electronic/hardware/` | Donanım tasarımı |
| Firmware Architecture | `electronic/firmware/` | Gömülü yazılım |

## Consequences

- L6 katmanı L0-L5'ten bağımsız geliştirilebilir
- Zero-allocation kuralı audio thread'de zorunlu
- 8+1 surround (MAX 2000W 8Ω) varsayılan amplifikatör
- XMOS XU316 + PCM3168A donanım seçimi

## Related

- [[ADR-017-dsp-hardware-mode]]
- [[ADR-038-8.1-sound-card-chip-selection]]
- [[ADR-025-professional-eq-system]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
