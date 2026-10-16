---
type: technical-spec
project: Neva Engine
component: dsp-fx-chain
updated: 2026-06-03
refs:
  - Voicemeeter Potato: 6-band Parametric + Compressor + Gate + Denoiser + Reverb + Delay
---

# Neva Engine — DSP FX Chain

> Voicemeeter Potato referans alınarak tasarlanmış profesyonel DSP zinciri.

---

## 1. FX Chain Order (Sabit Sıralama)

```
Input → [1] Denoiser → [2] Noise Gate → [3] EQ → [4] Compressor
      → [5] Chorus/Modulation → [6] Spatial/Panoramic (HRTF)
      → [7] Limiter → Output
      ↓              ↓
   [Reverb Send]   [Delay Send]
      ↓              ↓
   [Reverb Return] [Delay Return] → Mix
```

---

## 2. Channel Strip Processing

Her fiziksel/virtual input strip'te:

```
┌────────────────────────────────────────────────────┐
│  INPUT STRIP                                       │
│  ┌──────────┐ ┌────┐ ┌──────┐ ┌────────────┐      │
│  │ Denoiser │→│Gate│→│  EQ  │→│ Compressor │      │
│  │  (Knob)  │ │    │ │ 6/31 │ │   (Knob)   │      │
│  └──────────┘ └────┘ └──────┘ └────────────┘      │
│       ↓              ↓              ↓              │
│  ┌──────────┐ ┌────────────┐ ┌──────────────┐     │
│  │  Chorus  │→│Spatial/HRTF│→│   Limiter    │     │
│  │ (Mod FX) │ │  (Pan 3D)  │ │ (-40..+12dB) │     │
│  └──────────┘ └────────────┘ └──────────────┘     │
│       ↓ Send/Return                                │
│  ┌──────────┐ ┌──────────┐                         │
│  │  Reverb  │ │  Delay   │ (Internal FX)           │
│  └──────────┘ └──────────┘                         │
│  Controls: Gain / Mono / Mute / Solo / Pan          │
└────────────────────────────────────────────────────┘
```

---

## 3. Denoiser (Noise Reduction)

```cpp
// Spectral subtraction based denoiser
// Reference: Voicemeeter Denoiser Knob + RNNoise concepts

class Denoiser {
    float threshold;     // Noise floor threshold (dB)
    float reduction;     // Max reduction (dB), 0-40
    float attack_ms;     // Attack time
    float release_ms;    // Release time

    // Noise profile (learned during silence)
    float noiseProfile[FFT_BINS];
    bool profileLearned = false;

    void learnNoiseProfile(const float* spectrum) {
        // Silence detection → update noise profile
        if (isSilence(spectrum)) {
            for (int i = 0; i < FFT_BINS; ++i) {
                noiseProfile[i] = 0.9f * noiseProfile[i] + 0.1f * spectrum[i];
            }
        }
    }

    void process(float* spectrum) {
        for (int i = 0; i < FFT_BINS; ++i) {
            float snr = spectrum[i] - noiseProfile[i];
            float gain = snr > threshold ? 1.0f : std::pow(10.0f, snr / 20.0f);
            gain = std::max(gain, std::pow(10.0f, -reduction / 20.0f));
            spectrum[i] *= gain;
        }
    }
};
```

---

## 4. Noise Gate

```cpp
class NoiseGate {
    float threshold_dB;   // -60 to 0 dB
    float attack_ms;      // 0.1 - 500ms
    float hold_ms;        // 10 - 2000ms
    float release_ms;     // 10 - 2000ms
    float range_dB;       // -80 to 0 dB (max attenuation)

    enum State { OPEN, HOLD, CLOSED, RELEASE };
    State state = CLOSED;

    float process(float sample) {
        float level = std::abs(sample);
        float level_dB = 20.0f * std::log10(level + 1e-10f);

        switch (state) {
            case CLOSED:
                if (level_dB > threshold_dB) state = OPEN;
                return sample * std::pow(10.0f, range_dB / 20.0f);

            case OPEN:
                if (level_dB < threshold_dB) { state = HOLD; holdTimer = 0; }
                return sample;

            case HOLD:
                holdTimer += 1.0f / sampleRate;
                if (holdTimer >= hold_ms / 1000.0f) state = RELEASE;
                else if (level_dB > threshold_dB) state = OPEN;
                return sample;

            case RELEASE:
                // Smooth gain reduction
                releaseGain = std::max(releaseGain - releaseStep, range_dB);
                if (level_dB > threshold_dB) state = OPEN;
                return sample * std::pow(10.0f, releaseGain / 20.0f);
        }
    }
};
```

---

## 5. Compressor

```cpp
class Compressor {
    float threshold_dB;  // -60 to 0 dB
    float ratio;         // 1:1 to ∞:1
    float attack_ms;     // 0.1 - 200ms
    float release_ms;    // 10 - 2000ms
    float makeup_dB;     // 0 - 24dB
    float knee_dB;       // 0 - 20dB (soft knee)

    float gainReduction_dB = 0.0f;

    float process(float sample) {
        float level = std::abs(sample);
        float level_dB = 20.0f * std::log10(level + 1e-10f);

        float targetGain = 0.0f;
        if (level_dB > threshold_dB + knee_dB / 2.0f) {
            // Hard knee region
            targetGain = threshold_dB + (level_dB - threshold_dB) / ratio - level_dB;
        } else if (level_dB > threshold_dB - knee_dB / 2.0f) {
            // Soft knee region
            float overshoot = level_dB - (threshold_dB - knee_dB / 2.0f);
            targetGain = ((1.0f / ratio - 1.0f) * overshoot * overshoot) / (2.0f * knee_dB);
        }

        // Smooth gain change
        float coeff = targetGain > gainReduction_dB
            ? std::exp(-1.0f / (attack_ms / 1000.0f * sampleRate))
            : std::exp(-1.0f / (release_ms / 1000.0f * sampleRate));

        gainReduction_dB = coeff * gainReduction_dB + (1.0f - coeff) * targetGain;
        return sample * std::pow(10.0f, (gainReduction_dB + makeup_dB) / 20.0f);
    }
};
```

---

## 6. Chorus / Modulation

```cpp
class Chorus {
    float rate_hz;       // 0.1 - 10 Hz
    float depth_ms;      // 0.1 - 20ms
    float mix;           // 0.0 - 1.0 (dry/wet)
    float feedback;      // 0.0 - 0.9

    DelayLine delay;
    LFO lfo;

    float process(float sample) {
        float modDelay = depth_ms / 1000.0f * sampleRate
                       * (1.0f + lfo.process()); // LFO: sin/cos
        float wet = delay.read(modDelay);
        delay.write(sample + wet * feedback);

        return sample * (1.0f - mix) + wet * mix;
    }
};
```

---

## 7. Spatial / 3D Panoramic (HRTF Binaural)

```cpp
class SpatialPanoramic {
    float azimuth;       // -180 to 180 degrees
    float elevation;     // -90 to 90 degrees
    float distance;      // 0.1 - 10 meters
    bool hrtf_enabled;   // Binaural rendering

    // SOFA HRTF database (or simplified model)
    HRTFDatabase hrtf_db;

    void process(float* left, float* right) {
        if (hrtf_enabled && hrtf_db.isLoaded()) {
            auto [ir_left, ir_right] = hrtf_db.getHRIR(azimuth, elevation);
            *left  = convolve(*left,  ir_left);
            *right = convolve(*right, ir_right);
        }

        // Simple pan law (-3dB center)
        float panL = std::cos((azimuth + 90.0f) * M_PI / 180.0f);
        float panR = std::sin((azimuth + 90.0f) * M_PI / 180.0f);
        *left  *= panL / distance;
        *right *= panR / distance;
    }
};
```

---

## 8. Limiter (Brickwall)

```cpp
class Limiter {
    float ceiling_dB;    // -12 to 0 dB (default -0.3)
    float release_ms;    // 10 - 500ms
    float lookahead_ms;  // 0.5 - 5ms

    float gain = 1.0f;

    float process(float sample) {
        float targetGain = 1.0f;
        float peak = std::abs(sample);

        if (peak > ceiling) {
            targetGain = ceiling / peak;
        }

        // Smooth gain reduction
        float coeff = std::exp(-1.0f / (release_ms / 1000.0f * sampleRate));
        gain = targetGain < gain ? targetGain : coeff * gain + (1.0f - coeff) * targetGain;

        return sample * gain;
    }
};
```

---

## 9. Reverb (Internal FX — JustVerb 3 Style)

```cpp
class Reverb {
    float roomSize;      // 0.0 - 1.0
    float damping;       // 0.0 - 1.0
    float width;         // 0.0 - 1.0 (stereo spread)
    float wet_dB;        // -60 to 0 dB
    float dry_dB;        // -60 to 0 dB
    float preDelay_ms;   // 0 - 200ms

    // FDN (Feedback Delay Network) reverb
    static constexpr int NUM_DELAYS = 8;
    DelayLine delays[NUM_DELAYS];
    float decayGains[NUM_DELAYS];
    float feedbackMatrix[NUM_DELAYS][NUM_DELAYS]; // Hadamard matrix

    void process(float* left, float* right) {
        // ... FDN implementation
    }
};
```

---

## 10. Multitap Delay

```cpp
class MultitapDelay {
    struct Tap {
        float delay_ms;   // 10 - 5000ms
        float gain;       // 0.0 - 1.0
        float pan;        // -1.0 (L) to 1.0 (R)
        bool enabled;
    };

    static constexpr int MAX_TAPS = 16;
    Tap taps[MAX_TAPS];
    float feedback;   // 0.0 - 0.95
    float mix;        // 0.0 - 1.0

    void process(float* left, float* right) {
        // ... multi-tap delay with feedback
    }
};
```

---

## 11. WebSocket JSON Kontrol

```json
// EQ
{"cmd":"eq.set_type","params":{"type":"31band"}}
{"cmd":"eq.set_band","params":{"band":12,"gain_db":3.5}}
{"cmd":"eq.set_cell","params":{"cell":2,"freq":1000,"q":1.4,"gain_db":-2,"type":"peaking"}}

// FX
{"cmd":"fx.denoiser","params":{"enabled":true,"threshold":-40}}
{"cmd":"fx.gate","params":{"enabled":true,"threshold_dB":-50,"range_dB":-80}}
{"cmd":"fx.compressor","params":{"threshold_dB":-24,"ratio":4,"attack_ms":10,"release_ms":100}}
{"cmd":"fx.chorus","params":{"enabled":true,"rate":0.5,"depth":5,"mix":0.3}}
{"cmd":"fx.spatial","params":{"azimuth":0,"elevation":0,"distance":1,"hrtf":true}}
{"cmd":"fx.limiter","params":{"ceiling_dB":-0.3,"release_ms":50}}
{"cmd":"fx.reverb","params":{"roomSize":0.5,"damping":0.3,"wet_dB":-6,"dry_dB":0}}
{"cmd":"fx.delay","params":{"tap0_delay":250,"tap0_gain":0.5,"feedback":0.3,"mix":0.2}}
```

---

*Neva Engine DSP FX Chain v1 — Voicemeeter Potato Reference · 2026-06-03*
