---
type: electronic
category: audio-interface
title: "CoreMusic — Audio Interface Design (XMOS + PCM3168A)"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Audio Interface Design

**See also:** [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Audio Interface Design, CoreMusic ELECTRONICS platformunun USB ses arayüzü tasarımını tanımlar. XMOS XU316 + PCM3168A kombinasyonu.

---

## 2. XMOS XU316

| Parametre | Değer |
|-----------|-------|
| Chip | XMOS XU316 |
| Core | 16 tile, 32-bit RISC |
| USB | USB 2.0 High-Speed |
| I2S | 8-kanal TX/RX |
| DSP | Zero-latency |
| Clock | PLL ile senkron |
| Güç | 1.0V/3.3V |

---

## 3. PCM3168A

| Parametre | Değer |
|-----------|-------|
| Chip | PCM3168A |
| Kanal | 8-kanal DAC, 6-kanal ADC |
| Bit | 24-bit |
| Örnekleme | 192kHz max |
| SNR | 112dB (DAC) |
| THD+N | -100dB (DAC) |
| Giriş | I2S, TDM |
| Çıkış | I2S, TDM |
| Güç | 5V, 3.3V |

---

## 4. Devre Şeması

```
USB (PC) → XMOS XU316 → I2S Bus → PCM3168A → Analog Out → Amplifier
                │                        │
                ├── Clock (24.576MHz)     ├── VREF
                ├── GPIO (Control)        ├── VCOM
                └── SPI (Config)          └── VOUT (8-ch)
```

---

## 5. Clock Ağacı

| Kaynak | Frekans | Kullanım |
|--------|---------|----------|
| Master Clock | 24.576MHz | 48kHz multiples |
| Bit Clock | 3.072MHz | 48kHz × 64-bit |
| Word Select | 48kHz | LRCLK |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | XMOS XU316, PCM3168A |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
