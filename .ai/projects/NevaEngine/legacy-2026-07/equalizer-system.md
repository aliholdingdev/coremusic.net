---
type: technical-spec
project: Neva Engine
component: equalizer
updated: 2026-06-03
refs:
  - Voicemeeter Potato: https://vb-audio.com/Voicemeeter/potato.htm
  - FX Sound: https://www.fxsound.com/ (open source since 2023)
  - JUCE DSP: https://github.com/juce-framework/JUCE
  - DPF: https://github.com/DISTRHO/DPF
  - FFmpeg: equalizer, firequalizer, superequalizer, anequalizer, adynamicequalizer
---

# Neva Engine — Professional Equalizer System

> 6 seviyeli band sistemi (2→31→Parametric). AI auto-EQ. 5 kullanıcı modu.
> Tema paket desteği. VST3/LV2/CLAP plugin hosting. Cross-platform (Win2 API + Metal + OpenGL).

---

## 1. System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    NEVA ENGINE EQ SYSTEM                     │
├─────────────────────────────────────────────────────────────┤
│  DSP Chain: Input → Denoiser → Gate → EQ → Compressor       │
│             → Chorus → Spatial → Limiter → Output            │
│             Send/Return: Reverb, Delay                       │
├─────────────────────────────────────────────────────────────┤
│  EQ Types:                                                   │
│    Basic (2-band Shelving)    Standard (5-band Graphic)       │
│    Enhanced (10-band Graphic) Pro (31-band Graphic)           │
│    Parametric (6-cell)        Custom (Mix)                    │
├─────────────────────────────────────────────────────────────┤
│  AI Auto-EQ:                                                  │
│    Genre Detection → Preset Load → ML Fine-Tune → Room Calib  │
├─────────────────────────────────────────────────────────────┤
│  User Modes: Home | Car | TV | Studio | Gamer                │
│  Theme System: .zip packages (JSON+CSS+JS+PNG)               │
│  Plugin Hosting: VST3 | LV2 | CLAP (per-strip + master bus)  │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Multi-Band EQ Architecture

### 2.1 Basic (2-Band Shelving EQ)

```
Target: Ev kullanıcısı (basit)
Type: First-order shelving filters
Controls: Bass (±12dB @ 100Hz), Treble (±12dB @ 10kHz)
Q: Fixed (0.707 Butterworth)
```

```cpp
// 2-band shelving — first-order IIR
struct BasicEQ {
    BiquadFilter lowShelf;   // fc=100Hz, Q=0.707, ±12dB
    BiquadFilter highShelf;  // fc=10kHz, Q=0.707, ±12dB

    void process(float* samples, int numSamples) {
        for (int i = 0; i < numSamples; ++i) {
            float s = samples[i];
            s = lowShelf.process(s);
            s = highShelf.process(s);
            samples[i] = s;
        }
    }
};
```

### 2.2 Standard (5-Band Graphic EQ)

```
Band | Frequency | Filter Type | Q
  1  |   60 Hz   | Low Shelf   | 0.7
  2  |  250 Hz   | Peaking     | 1.0
  3  |  1 kHz    | Peaking     | 1.0
  4  |  4 kHz    | Peaking     | 1.0
  5  |  12 kHz   | High Shelf  | 0.7
```

### 2.3 Enhanced (10-Band Graphic EQ)

```
Band | Frequency | Filter Type | Q    | Octave
  1  |   31 Hz   | Low Shelf   | 0.7  | —
  2  |   62 Hz   | Peaking     | 1.4  | 1 oct
  3  |  125 Hz   | Peaking     | 1.4  | 1 oct
  4  |  250 Hz   | Peaking     | 1.4  | 1 oct
  5  |  500 Hz   | Peaking     | 1.4  | 1 oct
  6  |  1 kHz    | Peaking     | 1.4  | 1 oct
  7  |  2 kHz    | Peaking     | 1.4  | 1 oct
  8  |  4 kHz    | Peaking     | 1.4  | 1 oct
  9  |  8 kHz    | Peaking     | 1.4  | 1 oct
 10  |  16 kHz   | High Shelf  | 0.7  | —
```

### 2.4 Professional (31-Band Graphic EQ)

```
31-band 1/3-octave ISO standard:
20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,
200, 250, 315, 400, 500, 630, 800, 1k, 1.25k,
1.6k, 2k, 2.5k, 3.15k, 4k, 5k, 6.3k, 8k,
10k, 12.5k, 16k, 20k Hz

Range: ±12dB per band
Filter: Biquad peaking, Q optimized for 1/3 octave
```

```cpp
class GraphicEQ31 {
    static constexpr int NUM_BANDS = 31;
    static constexpr float FREQS[NUM_BANDS] = {
        20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,
        200, 250, 315, 400, 500, 630, 800, 1000, 1250,
        1600, 2000, 2500, 3150, 4000, 5000, 6300, 8000,
        10000, 12500, 16000, 20000
    };

    BiquadFilter bands[NUM_BANDS];
    float gains[NUM_BANDS]; // dB

    void setBand(int band, float gain_dB) {
        gains[band] = std::clamp(gain_dB, -12.0f, 12.0f);
        bands[band].setPeaking(FREQS[band], 1.414f, gains[band], sampleRate);
    }

    void process(float* samples, int numSamples) {
        for (int b = 0; b < NUM_BANDS; ++b) {
            for (int i = 0; i < numSamples; ++i) {
                samples[i] = bands[b].process(samples[i]);
            }
        }
    }
};
```

### 2.5 Parametric (6-Cell Full Parametric)

```
Cell | Parameter       | Range
  1  | Frequency       | 20Hz - 300Hz   (Low)
  2  | Frequency       | 100Hz - 1kHz   (Low-Mid)
  3  | Frequency       | 500Hz - 3kHz   (Mid)
  4  | Frequency       | 2kHz - 8kHz    (High-Mid)
  5  | Frequency       | 5kHz - 20kHz   (High)
  6  | Frequency       | 10Hz - 20kHz   (Flexible)

Per Cell:
  Filter Type: Peaking / Low Shelf / High Shelf / Low Pass / High Pass / Notch
  Frequency: 10Hz - 20kHz (continuous)
  Q: 0.1 - 40.0
  Gain: ±18dB
```

```cpp
struct ParametricCell {
    enum FilterType { Peaking, LowShelf, HighShelf, LowPass, HighPass, Notch, BandPass, AllPass };

    FilterType type = Peaking;
    float frequency = 1000.0f;
    float q = 1.0f;
    float gain_dB = 0.0f;
    bool bypass = false;

    BiquadFilter filter;

    void updateCoefficients(float sampleRate) {
        switch (type) {
            case Peaking:   filter.setPeaking(frequency, q, gain_dB, sampleRate); break;
            case LowShelf:  filter.setLowShelf(frequency, q, gain_dB, sampleRate); break;
            case HighShelf: filter.setHighShelf(frequency, q, gain_dB, sampleRate); break;
            case LowPass:   filter.setLowPass(frequency, q, sampleRate); break;
            case HighPass:  filter.setHighPass(frequency, q, sampleRate); break;
            case Notch:     filter.setNotch(frequency, q, sampleRate); break;
            case BandPass:  filter.setBandPass(frequency, q, sampleRate); break;
            case AllPass:   filter.setAllPass(frequency, q, sampleRate); break;
        }
    }
};

class ParametricEQ6 {
    ParametricCell cells[6];

    void process(float* samples, int numSamples) {
        for (auto& cell : cells) {
            if (cell.bypass) continue;
            for (int i = 0; i < numSamples; ++i) {
                samples[i] = cell.filter.process(samples[i]);
            }
        }
    }
};
```

### 2.6 Custom (Mix Mode)

Kullanıcı istediği band sayısını ve tipini seçebilir. Graphic + Parametric karışımı.
Max 31 bant sınırı, her bant kendi filter tipini seçebilir.

---

## 3. Biquad Filter Core (C++ Reference Implementation)

```cpp
// Robert Bristow-Johnson EQ Cookbook formulas
// Reference: https://www.w3.org/TR/audio-eq-cookbook/

struct BiquadFilter {
    float b0, b1, b2; // feedforward
    float a1, a2;     // feedback
    float x1, x2;     // input history
    float y1, y2;     // output history

    void setPeaking(float fc, float Q, float dBgain, float Fs) {
        float A  = std::pow(10.0f, dBgain / 40.0f);
        float w0 = 2.0f * M_PI * fc / Fs;
        float cos_w0 = std::cos(w0);
        float sin_w0 = std::sin(w0);
        float alpha = sin_w0 / (2.0f * Q);

        float a0_inv = 1.0f / (1.0f + alpha / A);
        b0 = (1.0f + alpha * A) * a0_inv;
        b1 = -2.0f * cos_w0 * a0_inv;
        b2 = (1.0f - alpha * A) * a0_inv;
        a1 = -b1;
        a2 = -(1.0f - alpha / A) * a0_inv;
    }

    void setLowShelf(float fc, float Q, float dBgain, float Fs) {
        float A  = std::pow(10.0f, dBgain / 40.0f);
        float w0 = 2.0f * M_PI * fc / Fs;
        float cos_w0 = std::cos(w0);
        float sin_w0 = std::sin(w0);
        float alpha = sin_w0 / (2.0f * Q);
        float beta = std::sqrt(A) * 2.0f * alpha;

        float a0_inv = 1.0f / ((A+1) + (A-1)*cos_w0 + beta);
        b0 = A * ((A+1) - (A-1)*cos_w0 + beta) * a0_inv;
        b1 = 2*A * ((A-1) - (A+1)*cos_w0) * a0_inv;
        b2 = A * ((A+1) - (A-1)*cos_w0 - beta) * a0_inv;
        a1 = -2 * ((A-1) + (A+1)*cos_w0) * a0_inv;
        a2 = ((A+1) + (A-1)*cos_w0 - beta) * a0_inv;
    }

    // ... high shelf, low pass, high pass, notch, band pass, all pass

    float process(float x) {
        float y = b0*x + b1*x1 + b2*x2 - a1*y1 - a2*y2;
        x2 = x1; x1 = x;
        y2 = y1; y1 = y;
        return y;
    }

    void reset() { x1 = x2 = y1 = y2 = 0.0f; }
};
```

---

## 4. Spectrum Analyzer (Real-Time FFT)

```cpp
class SpectrumAnalyzer {
    static constexpr int FFT_SIZE = 2048; // veya 4096/8192
    static constexpr int BINS = FFT_SIZE / 2;

    float fftBuffer[FFT_SIZE];
    int bufferIndex = 0;
    float magnitudes[BINS];
    float smoothedMags[BINS]; // exponential smoothing

    void feedSample(float sample) {
        fftBuffer[bufferIndex++] = sample;
        if (bufferIndex >= FFT_SIZE) {
            bufferIndex = 0;
            computeFFT();
        }
    }

    void computeFFT() {
        // Hann window
        for (int i = 0; i < FFT_SIZE; ++i) {
            float w = 0.5f * (1.0f - std::cos(2.0f * M_PI * i / (FFT_SIZE - 1)));
            fftBuffer[i] *= w;
        }

        // Real FFT (kiss_fft veya pffft)
        kiss_fftr(fftConfig, fftBuffer, fftOutput);

        // Magnitude spectrum (dB)
        for (int i = 0; i < BINS; ++i) {
            float mag = std::sqrt(fftOutput[i].r * fftOutput[i].r
                                + fftOutput[i].i * fftOutput[i].i);
            float mag_dB = 20.0f * std::log10(mag + 1e-10f);

            // Smoothing (α = 0.3)
            smoothedMags[i] = 0.3f * mag_dB + 0.7f * smoothedMags[i];
        }
    }

    const float* getSpectrum() const { return smoothedMags; }
    int getBinCount() const { return BINS; }
};
```

---

## 5. Performance Targets

| Metrik | Target |
|--------|--------|
| 31-band EQ processing (per sample) | < 0.5ms @ 48kHz/512 buffer |
| 6-cell Parametric processing | < 0.1ms |
| Biquad IIR precision | 32-bit float (IEEE 754) |
| THD+N (EQ bypass) | < 0.001% |
| SNR | > 110dB |
| Latency (input→output) | < 5ms (ASIO), < 10ms (WASAPI) |
| CPU (31-band, idle curve) | < 2% @ 3GHz |
| RAM (full EQ state) | < 1MB |

---

*Neva Engine Professional EQ System v1 — 2026-06-03*
*Refs: Voicemeeter Potato, FX Sound, JUCE DSP, RBJ Cookbook, FFmpeg EQ filters*
