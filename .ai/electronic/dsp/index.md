---
type: system
category: dsp-engine
title: "CoreMusic Electronics â€” DSP Engine Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
---

# CoreMusic Electronics â€” DSP Engine

**Zorunlu BaÄŸlantÄ±lar:** [[electronic/index]] Â· [[brain.md]] Â· [[architecture/k0-k5-software/k3-audio-engine]]

---

## 1. AmaÃ§

DSP Engine, CoreMusic ELECTRONICS platformunun gerÃ§ek zamanlÄ± ses iÅŸleme motorudur. TÃ¼m EQ, compressor, limiter, crossover, reverb, delay ve filtreleme iÅŸlemleri bu katmanda yÃ¼rÃ¼tÃ¼lÃ¼r.

---

## 2. DSP Pipeline

```
Input Signal
    â†“
Input Gain
    â†“
Noise Gate
    â†“
High Pass Filter
    â†“
Low Pass Filter
    â†“
Parametric EQ
    â†“
Graphic EQ
    â†“
Compressor
    â†“
Limiter
    â†“
Loudness
    â†“
Crossover
    â†“
Delay
    â†“
Reverb
    â†“
Output Gain
    â†“
Output Routing
```

Detay: [[dsp-pipeline]]

---

## 3. DSP BileÅŸenleri

| BileÅŸen | Dosya | Kapsam |
|---------|-------|--------|
| DSP Pipeline | [[dsp-pipeline]] | Ä°ÅŸleme hattÄ± akÄ±ÅŸÄ± |
| Equalizer | [[equalizer]] | Graphic + Parametric EQ |
| Dynamics | [[dynamics]] | Compressor, Limiter, Gate |
| Filters | [[filters]] | FIR, IIR, FFT |
| Crossover | [[crossover]] | Frekans daÄŸÄ±tÄ±mÄ± |
| Effects | [[effects]] | Reverb, Delay, Room Correction |
| Loudness | [[loudness]] | Loudness, ReplayGain |

---

## 4. DSP Hardware

| bileÅŸen | Ã–zellik | Referans |
|---------|---------|----------|
| XMOS XU316 | USB Audio + DSP | [[ADR-017-dsp-hardware-mode]] |
| PCM3168A | 8-kanal DAC | [[ADR-038-8.1-sound-card-chip-selection]] |
| AK4458 | 8-kanal high-end DAC | [[electronic/hardware/index]] |

---

## 5. Equalizer Sistemi

### Graphic Equalizer
- 2 Band â†’ 31 Band arasÄ±
- KullanÄ±cÄ± tarafÄ±ndan ayarlanabilir

### Parametric Equalizer
- Frekans, Gain, Q Factor
- Tamamen Ã¶zelleÅŸtirilebilir

Detay: [[equalizer]]

---

## 6. Crossover Engine

```
20Hz    â†’ Subwoofer (LFE)
120Hz   â†’ Woofer
500Hz   â†’ Midrange
3500Hz  â†’ Tweeter
20kHz   â†’ Upper Limit
```

Her kanal baÄŸÄ±msÄ±z olarak yapÄ±landÄ±rÄ±labilir.

Detay: [[crossover]]

---

## 7. Audio Effects

| Kategori | Efektler |
|----------|----------|
| Dinamik | Compressor, Limiter, Gate, Expander |
| Frekans | Graphic EQ, Parametric EQ, FIR, IIR |
| MekÃ¢nsal | Reverb, Delay, Echo, Stereo Width |
| Bass | Bass Boost, Bass Management, LFE Routing |

Detay: [[effects]]

---

## 8. GerÃ§ek ZamanlÄ± Performans Hedefleri

| Metrik | Hedef |
|--------|-------|
| Latency | <10ms (ASIO), <20ms (WASAPI) |
| CPU KullanÄ±mÄ± | <%15 (8+1 kanal) |
| Bellek | <%50MB (tÃ¼m DSP chain) |
| Ã–rnekleme | 48kHz standart, 96/192kHz destek |
| Bit DerinliÄŸi | 32-bit float |

---

## 9. Zero-Allocation KurallarÄ± (C++)

Audio thread'de âŒ yasak:
- `malloc()`, `free()`, `new`, `delete`
- `std::make_shared`, `std::vector` push_back
- I/O blocking
- `throw`

âœ… Ä°zin:
- Stack tahsisi
- `std::atomic`
- SIMD (SSE2/AVX2/NEON)
- `constexpr`
- `alignas(64)`

Referans: [[brain.md]]#c++-audio-rules

---

## 10. ADR ReferanslarÄ±

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE, ASIO |
| [[ADR-025-professional-eq-system]] | 31-band EQ |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A |

---

## 11. Ã‡apraz Referanslar

| Kaynak | Hedef | Ä°liÅŸki |
|--------|-------|--------|
| DSP Engine | [[architecture/k0-k5-software/k3-audio-engine]] | Audio service |
| DSP Engine | [[electronic/drivers/index]] | Driver katmanÄ± |
| DSP Engine | [[electronic/amplifier/index]] | Amplifier Ã§Ä±kÄ±ÅŸÄ± |
| DSP Engine | [[electronic/hardware/index]] | DonanÄ±m platformu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team Â· Human Mode Â· Truth Mode

