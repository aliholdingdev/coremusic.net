---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K3 Ses İşleme Motoru Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K3: Ses İşleme Motoru (Neva Engine)

**Katman:** K3 (Ses Motoru)
**Kapsam:** Neva Engine, DSP Chain, Mixer, EQ, Reverb, Compressor, Analyzer
**Sorumlu Agent:** Embedded Engineer
**Bileşen Sayısı:** 50

---

## 1. Genel Bakış

K3 katmanı, CoreMusic'in kalbi olan Neva Engine'i içerir. Bu katman, dijital sinyal işleme (DSP) zincirini yönetir ve profesyonel ses kalitesinde işleme sağlar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Zero-Allocation** | Audio thread'de heap allocation yasak |
| **Lock-Free** | Audio thread'de mutex yasak |
| **Noexcept** | ASIO callback noexcept zorunlu |
| **SIMD** | SSE2/AVX2/NEON optimizasyonu |
| **Real-Time** | Gecikme <10ms |

---

## 2. Neva Engine Mimarisi

### 2.1 Ana Bileşenler

```
Neva Engine
├── DSP Chain (Kanal başına)
│   ├── 31-Band Parametric EQ
│   ├── Compressor
│   ├── Reverb (4 mod)
│   ├── Limiter
│   └── Analyzer
├── Mixer
│   ├── 8.1 Channel Routing
│   ├── Volume Control
│   ├── Pan Control
│   └── Mute/Solo
├── Crossover
│   ├── Linkwitz-Riley 4th Order
│   └── Bass Management
└── Master Output
    ├── Dithering
    ├── Sample Rate Conversion
    └── Bit-Perfect Output
```

### 2.2 DSP Chain Akışı

```
Input (32-bit float)
    → Gain Staging
    → 31-Band Parametric EQ
    → Compressor
    → Reverb (mix control)
    → Limiter
    → Output Gain
    → Dithering (optional)
Output (32-bit float)
```

---

## 3. 31-Band Parametric EQ

### 3.1 EQ Band Frekansları

| Band | Frekans | Q Factor | Aralık |
|------|---------|----------|--------|
| 1 | 20 Hz | 0.7 | Sub-bass |
| 2 | 25 Hz | 0.7 | Sub-bass |
| 3 | 31.5 Hz | 0.7 | Sub-bass |
| 4 | 40 Hz | 0.7 | Bass |
| 5 | 50 Hz | 0.7 | Bass |
| 6 | 63 Hz | 0.7 | Bass |
| 7 | 80 Hz | 0.7 | Bass |
| 8 | 100 Hz | 0.7 | Lower mid |
| 9 | 125 Hz | 0.7 | Lower mid |
| 10 | 160 Hz | 0.7 | Lower mid |
| 11 | 200 Hz | 0.7 | Mid |
| 12 | 250 Hz | 0.7 | Mid |
| 13 | 315 Hz | 0.7 | Mid |
| 14 | 400 Hz | 0.7 | Mid |
| 15 | 500 Hz | 0.7 | Mid |
| 16 | 630 Hz | 0.7 | Upper mid |
| 17 | 800 Hz | 0.7 | Upper mid |
| 18 | 1 kHz | 0.7 | Upper mid |
| 19 | 1.25 kHz | 0.7 | Presence |
| 20 | 1.6 kHz | 0.7 | Presence |
| 21 | 2 kHz | 0.7 | Presence |
| 22 | 2.5 kHz | 0.7 | Presence |
| 23 | 3.15 kHz | 0.7 | Brilliance |
| 24 | 4 kHz | 0.7 | Brilliance |
| 25 | 5 kHz | 0.7 | Brilliance |
| 26 | 6.3 kHz | 0.7 | Brilliance |
| 27 | 8 kHz | 0.7 | Air |
| 28 | 10 kHz | 0.7 | Air |
| 29 | 12.5 kHz | 0.7 | Air |
| 30 | 16 kHz | 0.7 | Air |
| 31 | 20 kHz | 0.7 | Air |

### 3.2 Biquad Filtre Koefisyonları

```cpp
// peakingEQ coefficients
struct EQCoefficients {
    float b0, b1, b2, a1, a2;
};

EQCoefficients calculatePeakingEQ(
    float sampleRate,
    float frequency,
    float gain,
    float Q
) {
    float A = powf(10.0f, gain / 40.0f);
    float w0 = 2.0f * M_PI * frequency / sampleRate;
    float alpha = sinf(w0) / (2.0f * Q);

    float b0 = 1.0f + alpha * A;
    float b1 = -2.0f * cosf(w0);
    float b2 = 1.0f - alpha * A;
    float a0 = 1.0f + alpha / A;
    float a1 = -2.0f * cosf(w0);
    float a2 = 1.0f - alpha / A;

    // Normalize
    b0 /= a0; b1 /= a0; b2 /= a0;
    a1 /= a0; a2 /= a0;

    return { b0, b1, b2, a1, a2 };
}
```

---

## 4. Reverb (4 Mod)

| Mod | Algoritma | Kullanım |
|-----|-----------|----------|
| Geniş Konser | FDN 16-line | Büyük mekan |
| Düğün Salonu | Plate reverb | Orta mekan |
| Oda | Room algorithm | Küçük mekan |
| Stüdyo | Algorithmic + convolution | Profesyonel |

### 4.1 FDN Reverb Yapısı

```cpp
// 16-line FDN Reverb
class FDNReverb {
    static constexpr int NUM_LINES = 16;
    static constexpr int MAX_DELAY = 48000; // 1 second @ 48kHz

    float _delayLines[NUM_LINES][MAX_DELAY];
    float _feedbackMatrix[NUM_LINES][NUM_LINES]; // Householder
    float _absorption[NUM_LINES]; // Frequency-dependent
    float _modulation[NUM_LINES]; // Lexicon-style

    void process(float* buffer, int samples) noexcept {
        for (int i = 0; i < samples; ++i) {
            float input = buffer[i];
            float output = 0.0f;

            for (int line = 0; line < NUM_LINES; ++line) {
                // Read from delay line
                float delayed = _delayLines[line][_readPos[line]];

                // Apply absorption (IIR)
                delayed *= _absorption[line];

                // Modulation (smooth random)
                delayed *= (1.0f + 0.01f * sinf(_modPhase[line]));

                // Accumulate output
                output += delayed;

                // Write to delay line
                _delayLines[line][_writePos[line]] =
                    input + feedbackSum(line);
            }

            buffer[i] = output / NUM_LINES;
        }
    }
};
```

---

## 5. Compressor

### 5.1 Compressor Parametreleri

| Parametre | Varsayılan | Aralık |
|-----------|-----------|--------|
| Threshold | -20 dB | -60 to 0 dB |
| Ratio | 4:1 | 1:1 to 20:1 |
| Attack | 10 ms | 0.1 to 100 ms |
| Release | 100 ms | 10 to 1000 ms |
| Knee | 6 dB | 0 to 12 dB |
| Makeup Gain | 0 dB | 0 to 24 dB |

### 5.2 Sidechain Detection

```cpp
// Peak detector with attack/release
class SidechainDetector {
    float _level = 0.0f;
    float _attackCoeff;
    float _releaseCoeff;

    void setAttack(float attackMs, float sampleRate) {
        _attackCoeff = expf(-1.0f / (attackMs * 0.001f * sampleRate));
    }

    void setRelease(float releaseMs, float sampleRate) {
        _releaseCoeff = expf(-1.0f / (releaseMs * 0.001f * sampleRate));
    }

    float process(float input) noexcept {
        float absInput = fabsf(input);
        if (absInput > _level) {
            _level = _attackCoeff * _level + (1.0f - _attackCoeff) * absInput;
        } else {
            _level = _releaseCoeff * _level + (1.0f - _releaseCoeff) * absInput;
        }
        return _level;
    }
};
```

---

## 6. Limiter

### 6.1 True Peak Limiter

| Parametre | Değer |
|-----------|-------|
| Ceiling | -0.3 dBTP |
| Release | Auto (ISP-aware) |
| Oversampling | 4x |

---

## 7. Mixer (8.1 Surround)

### 7.1 Kanal Routing Matrisi

```
Input Channel → Output Channel:
  CH1 (Front L)  → Output 1 (Front L)
  CH2 (Front R)  → Output 2 (Front R)
  CH3 (Center)   → Output 3 (Center)
  CH4 (LFE)      → Output 4 (Subwoofer)
  CH5 (Sur L)    → Output 5 (Surround L)
  CH6 (Sur R)    → Output 6 (Surround R)
  CH7 (Rear L)   → Output 7 (Rear L)
  CH8 (Rear R)   → Output 8 (Rear R)
```

### 7.2 Bass Management

```
Crossover: Linkwitz-Riley 4th Order
Frequency: 80Hz

Low-pass → Subwoofer (LFE)
High-pass → Main speakers (Front, Center, Surround, Rear)
```

---

## 8. Analyzer

### 8.1 Spectrum Analyzer

| Parametre | Değer |
|-----------|-------|
| FFT Size | 4096 |
| Window | Hann |
| Overlap | 50% |
| Bands | 31 (1/3 octave) |
| Update Rate | 30 fps |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k3-ses-motoru/README.md` | Bu dosya |
| `architecture/k3-ses-motoru/dsp-chain.md` | DSP zincir detayı |
| `architecture/k3-ses-motoru/31-band-eq.md` | EQ detayı |
| `architecture/k3-ses-motoru/reverb-modes.md` | Reverb modları |
| `architecture/k3-ses-motoru/mixer-architecture.md` | Mixer mimarisi |
| `architecture/k3-ses-motoru/ring-buffer.md` | Lock-free ring buffer |
| `architecture/k3-ses-motoru/zero-allocation.md` | Zero-allocation kuralları |
| `architecture/k3-ses-motoru/bit-perfect.md` | Bit-perfect aktarım |

---

## 10. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-025 | 31-band parametrik EQ |
| ADR-062 | DSP Pipeline Architecture |

---

*K3 Ses İşleme Motoru Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
