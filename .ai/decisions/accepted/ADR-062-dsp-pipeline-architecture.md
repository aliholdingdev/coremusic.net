---
type: decision
category: dsp-pipeline
title: "ADR-062: DSP Pipeline Architecture"
date: 2026-08-09
status: active
version: 1.0.0
---

# ADR-062: DSP Pipeline Architecture

## Status

Active — 2026-08-09

## Context

CoreMusic ELECTRONICS platformu için DSP processing pipeline'ın tanımlanması gerekiyor. 7 bağımsız modül: pipeline, equalizer, dynamics, filters, crossover, effects, loudness.

## Decision

DSP Pipeline 13 aşamalı bir processing chain olarak tanımlandı:

```
Input Gain → Noise Gate → High Pass → Low Pass → Parametric EQ → Graphic EQ → Compressor → Limiter → Loudness → Crossover → Delay → Reverb → Output
```

Her modül bağımsız olarak geliştirilebilir. Zero-allocation kuralı audio thread'de zorunlu.

## Consequences

- 31-band parametrik ve graphic EQ desteği
- Linkwitz-Riley 4. nesil crossover (80Hz)
- EBU R128 loudness normalizasyonu (-23 LUFS)
- ASIO latency <10ms, WASAPI latency <20ms

## Related

- [[ADR-017-dsp-hardware-mode]]
- [[ADR-025-professional-eq-system]]
- [[ADR-006-performance-targets]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
