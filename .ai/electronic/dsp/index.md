---
type: system
category: dsp-engine
title: "CoreMusic Electronics — DSP Engine Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — DSP Engine

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[architecture/06-audio/index]]

---

## 1. Amaç

DSP Engine, CoreMusic ELECTRONICS platformunun gerçek zamanlı ses işleme motorudur. Tüm EQ, compressor, limiter, crossover, reverb, delay ve filtreleme işlemleri bu katmanda yürütülür.

---

## 2. DSP Pipeline

```
Input Signal
    ↓
Input Gain
    ↓
Noise Gate
    ↓
High Pass Filter
    ↓
Low Pass Filter
    ↓
Parametric EQ
    ↓
Graphic EQ
    ↓
Compressor
    ↓
Limiter
    ↓
Loudness
    ↓
Crossover
    ↓
Delay
    ↓
Reverb
    ↓
Output Gain
    ↓
Output Routing
```

Detay: [[dsp-pipeline]]

---

## 3. DSP Bileşenleri

| Bileşen | Dosya | Kapsam |
|---------|-------|--------|
| DSP Pipeline | [[dsp-pipeline]] | İşleme hattı akışı |
| Equalizer | [[equalizer]] | Graphic + Parametric EQ |
| Dynamics | [[dynamics]] | Compressor, Limiter, Gate |
| Filters | [[filters]] | FIR, IIR, FFT |
| Crossover | [[crossover]] | Frekans dağıtımı |
| Effects | [[effects]] | Reverb, Delay, Room Correction |
| Loudness | [[loudness]] | Loudness, ReplayGain |

---

## 4. DSP Hardware

| bileşen | Özellik | Referans |
|---------|---------|----------|
| XMOS XU316 | USB Audio + DSP | [[ADR-017-dsp-hardware-mode]] |
| PCM3168A | 8-kanal DAC | [[ADR-038-8.1-sound-card-chip-selection]] |
| AK4458 | 8-kanal high-end DAC | [[electronic/hardware/index]] |

---

## 5. Equalizer Sistemi

### Graphic Equalizer
- 2 Band → 31 Band arası
- Kullanıcı tarafından ayarlanabilir

### Parametric Equalizer
- Frekans, Gain, Q Factor
- Tamamen özelleştirilebilir

Detay: [[equalizer]]

---

## 6. Crossover Engine

```
20Hz    → Subwoofer (LFE)
120Hz   → Woofer
500Hz   → Midrange
3500Hz  → Tweeter
20kHz   → Upper Limit
```

Her kanal bağımsız olarak yapılandırılabilir.

Detay: [[crossover]]

---

## 7. Audio Effects

| Kategori | Efektler |
|----------|----------|
| Dinamik | Compressor, Limiter, Gate, Expander |
| Frekans | Graphic EQ, Parametric EQ, FIR, IIR |
| Mekânsal | Reverb, Delay, Echo, Stereo Width |
| Bass | Bass Boost, Bass Management, LFE Routing |

Detay: [[effects]]

---

## 8. Gerçek Zamanlı Performans Hedefleri

| Metrik | Hedef |
|--------|-------|
| Latency | <10ms (ASIO), <20ms (WASAPI) |
| CPU Kullanımı | <%15 (8+1 kanal) |
| Bellek | <%50MB (tüm DSP chain) |
| Örnekleme | 48kHz standart, 96/192kHz destek |
| Bit Derinliği | 32-bit float |

---

## 9. Zero-Allocation Kuralları (C++)

Audio thread'de ❌ yasak:
- `malloc()`, `free()`, `new`, `delete`
- `std::make_shared`, `std::vector` push_back
- I/O blocking
- `throw`

✅ İzin:
- Stack tahsisi
- `std::atomic`
- SIMD (SSE2/AVX2/NEON)
- `constexpr`
- `alignas(64)`

Referans: [[brain.md]]#c++-audio-rules

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE, ASIO |
| [[ADR-025-professional-eq-system]] | 31-band EQ |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A |

---

## 11. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| DSP Engine | [[architecture/06-audio/index]] | Audio service |
| DSP Engine | [[electronic/drivers/index]] | Driver katmanı |
| DSP Engine | [[electronic/amplifier/index]] | Amplifier çıkışı |
| DSP Engine | [[electronic/hardware/index]] | Donanım platformu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
