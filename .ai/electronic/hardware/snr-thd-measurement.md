---
type: electronic
category: snr-thd-measurement
title: "CoreMusic — SNR & THD Measurement"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — SNR & THD Measurement

**See also:** [[electronic/hardware/index]] · [[electronic/hardware/test-protocols]]

---

## 1. Amaç

SNR & THD Measurement, CoreMusic ELECTRONICS platformunun sinyal-gürültü oranı ve toplam bozulma ölçüm protokollerini tanımlar.

---

## 2. SNR (Signal-to-Noise Ratio)

| Parametre | Hedef |
|-----------|-------|
| SNR (A-wtd) | >100dB |
| Ölçüm | 1kHz, 0dBFS referans |
| Gürültü | Tüm frekanslarda |

### SNR Hesaplama

```
SNR = 20 × log10(Sinyal / Gürültü)
```

---

## 3. THD+N (Total Harmonic Distortion + Noise)

| Parametre | Hedef |
|-----------|-------|
| THD+N | <0.01% (1kHz, 1W) |
| THD+N | <0.001% (1kHz, 10W) |
| Harmonics | 2., 3., 5. harmonik |
| Noise floor | <-100dB |

---

## 4. Ölçüm Setupı

```
PC (Signal Generator) → DAC → Amplifier → Load (8Ω)
                        │
                    Audio Interface
                        │
                    PC (Analyzer)
```

---

## 5. Ölçüm Prosedürü

| Adım | Aksiyon |
|------|---------|
| 1 | Sinyal üret: 1kHz sine, 0dBFS |
| 2 | Amplifier'ı çalıştır |
| 3 | Çıkış sinyalini ölç |
| 4 | FFT analizi yap |
| 5 | THD+N hesapla |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-006-performance-targets]] | Performans hedefleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
