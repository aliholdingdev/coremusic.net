---
title: "ADR-017: DSP Hardware Mode (XMOS, JUCE, ASIO)"
status: frozen
date: 2026-04-05
tags: [audio, dsp, hardware, xmos, juce, asio, frozen]
---

# ADR-017: DSP Hardware Mode

---

## 1. Executive Summary

CoreMusic DSP hardware modu **XMOS XU316** ve **JUCE 9** framework'Ã¼ ile uygulanÄ±r. **ASIO SDK 2.3.4** ile dÃ¼ÅŸÃ¼k gecikmeli ses iletiÅŸimi saÄŸlanÄ±r. C++20 standardÄ± kullanÄ±lÄ±r.

## 2. Decision

### Hardware Stack

| BileÅŸen | DeÄŸer |
|---------|-------|
| DSP Chip | XMOS XU316 |
| Framework | JUCE 9 |
| Audio Driver | ASIO SDK 2.3.4 |
| Language | C++20 |
| Sample Rate | 48kHz |
| Bit Depth | 32-bit float |
| Buffer Size | 512 sample (64-1024) |
| Latency Target | < 10ms (ASIO) |

### C++ Audio KurallarÄ±

| # | Kural | Durum |
|---|-------|-------|
| 1 | Zero-allocation (audio thread) | âœ… Zorunlu |
| 2 | Lock-free (audio thread) | âœ… Zorunlu |
| 3 | noexcept (callback) | âœ… Zorunlu |
| 4 | alignas(64) (cache-line) | âœ… Zorunlu |
| 5 | constexpr (buffer size) | âœ… Zorunlu |
| 6 | SIMD (SSE2/AVX2/NEON) | âš ï¸ Tercih |
| 7 | float32 (32-bit PCM) | âœ… Zorunlu |
| 8 | 48kHz sample rate | âœ… Zorunlu |

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

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-017: DSP Hardware Mode v2.0.0 â€” CoreMusic Audio*
*Authority: Embedded Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*