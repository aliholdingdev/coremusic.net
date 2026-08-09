---
type: electronic
category: frequency-response
title: "CoreMusic — Frequency Response Measurement"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Frequency Response

**See also:** [[electronic/hardware/index]] · [[electronic/hardware/test-protocols]]

---

## 1. Amaç

Frequency Response Measurement, CoreMusic ELECTRONICS platformunun frekans yanıtı ölçüm protokollerini tanımlar.

---

## 2. Ölçüm Parametreleri

| Parametre | Değer |
|-----------|-------|
| Frekans Aralığı | 20Hz - 20kHz |
| Ölçüm Noktası | 1/3 oktav |
| Düzey | -3dB'den fazla sapma yok |
| Referans | 0dB @ 1kHz |

---

## 3. Ölçüm Setupı

```
PC (Signal Generator) → DAC → Amplifier → Hoparlör → Measurement Mic
                                                         │
                                                    Audio Interface
                                                         │
                                                    PC (Analyzer)
```

---

## 4. Frekans Yanıtı Hedefleri

| Frekans | Hedef | Tolerans |
|---------|-------|----------|
| 20Hz | 0dB | ±1dB |
| 100Hz | 0dB | ±0.5dB |
| 1kHz | 0dB | ±0.5dB |
| 10kHz | 0dB | ±0.5dB |
| 20kHz | 0dB | ±1dB |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-006-performance-targets]] | Performans hedefleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
