---
type: decision
id: "017"
title: "ADR-017: DSP Hardware Mode (XMOS, JUCE, ASIO)"
category: "audio"
status: "frozen"
date: "2026-04-05"
updated: "2026-08-15"
authority: "Embedded Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [audio, dsp, hardware, xmos, juce, asio, frozen]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]"
  - "[[architecture/l6-electronics]]"
---

# ADR-017: DSP Hardware Mode

---

## 1. Executive Summary

CoreMusic DSP hardware modu **XMOS XU316** ve **JUCE 9** framework'ü ile uygulanır. **ASIO SDK 2.3.4** ile düşük gecikmeli ses iletişimi sağlanır. C++20 standardı kullanılır.

## 2. Decision

### Hardware Stack

| Bileşen | Değer |
|---------|-------|
| DSP Chip | XMOS XU316 |
| Framework | JUCE 9 |
| Audio Driver | ASIO SDK 2.3.4 |
| Language | C++20 |
| Sample Rate | 48kHz |
| Bit Depth | 32-bit float |
| Buffer Size | 512 sample (64-1024) |
| Latency Target | < 10ms (ASIO) |

### C++ Audio Kuralları

| # | Kural | Durum |
|---|-------|-------|
| 1 | Zero-allocation (audio thread) | ✅ Zorunlu |
| 2 | Lock-free (audio thread) | ✅ Zorunlu |
| 3 | noexcept (callback) | ✅ Zorunlu |
| 4 | alignas(64) (cache-line) | ✅ Zorunlu |
| 5 | constexpr (buffer size) | ✅ Zorunlu |
| 6 | SIMD (SSE2/AVX2/NEON) | ⚠️ Tercih |
| 7 | float32 (32-bit PCM) | ✅ Zorunlu |
| 8 | 48kHz sample rate | ✅ Zorunlu |

### ASIO Callback

```cpp
void processAudioBlock(float** output, const float** input,
                       int channels, int samples) noexcept {
    for (int i = 0; i < samples; ++i)
        for (int ch = 0; ch < channels; ++ch) {
            float s = input[ch][i];
            s = dspChain[ch].processEQ(s);
            s = dspChain[ch].processCompressor(s);
            s = dspChain[ch].processLimiter(s);
            output[ch][i] = s;
        }
}
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-017: DSP Hardware Mode v2.0.0 — CoreMusic Audio*
*Authority: Embedded Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
