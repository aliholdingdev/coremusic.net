---
title: "Chorus, Delay, Flanger, Phaser Efektleri"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Chorus, Delay, Flanger, Phaser Efektleri

## Genel Bakış

COREMUSIC, modülasyon ve gecikme tabanlı efektler sağlar. Chorus, delay, flanger ve phaser efektleri, ses sinyaline derinlik ve hareket katar. Her efekt, Low Frequency Oscillator (LFO) ile modüle edilebilir.

## Teknik Detaylar

### Efekt Genel Yapısı

```
┌─────────────────────────────────────────────────────┐
│              Modülasyon Efekt Akışı                 │
│                                                     │
│  Input ──→ [Dry Signal] ─────────────→ [Mixer]     │
│      │                                ↓             │
│      └────→ [Modulator/LFO] ──→ [Effect] ──→ [Wet]│
│                                                     │
│  LFO ──→ [Delay Time Modulation]                   │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### LFO Implementasyonu

```cpp
// Low Frequency Oscillator
class LFO {
public:
    enum Waveform {
        Sine,
        Triangle,
        Sawtooth,
        Square
    };
    
    LFO(double sampleRate) : sampleRate(sampleRate) {}
    
    void setParams(float frequency, Waveform waveform, 
                   float depth) {
        this->frequency = frequency;
        this->waveform = waveform;
        this->depth = depth;
        phaseIncrement = 2.0 * M_PI * frequency / sampleRate;
    }
    
    float process() noexcept {
        float output = 0.0f;
        
        switch (waveform) {
            case Sine:
                output = std::sin(phase);
                break;
            case Triangle:
                output = 2.0f * std::abs(2.0f * (phase / 
                         (2.0 * M_PI)) - 1.0f) - 1.0f;
                break;
            case Sawtooth:
                output = 2.0f * (phase / (2.0 * M_PI)) - 1.0f;
                break;
            case Square:
                output = (phase < M_PI) ? 1.0f : -1.0f;
                break;
        }
        
        phase += phaseIncrement;
        if (phase >= 2.0 * M_PI) phase -= 2.0 * M_PI;
        
        return output * depth;
    }
    
    void reset() {
        phase = 0.0;
    }
    
private:
    double sampleRate;
    float frequency = 1.0f;
    float depth = 1.0f;
    Waveform waveform = Sine;
    double phase = 0.0;
    double phaseIncrement = 0.0;
};
```

### Chorus Efekti

```cpp
class Chorus {
public:
    Chorus(double sampleRate) 
        : sampleRate(sampleRate),
          lfo(sampleRate),
          delayLine(static_cast<uint32_t>(sampleRate * 0.05)) {}
    
    void setParams(float rate, float depth, float mix, 
                   float delay) {
        lfo.setParams(rate, LFO::Sine, depth);
        this->mix = mix;
        baseDelay = delay;
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float dry = input[i];
            
            // LFO ile delay modülasyonu
            float lfoValue = lfo.process();
            float modDelay = baseDelay + lfoValue;
            
            // Fractional delay (linear interpolation)
            uint32_t delayInt = static_cast<uint32_t>(modDelay);
            float delayFrac = modDelay - delayInt;
            
            float sample1 = delayLine.readAt(delayInt);
            float sample2 = delayLine.readAt(delayInt + 1);
            float wet = sample1 + delayFrac * (sample2 - sample1);
            
            // Delay line'a yaz
            delayLine.write(dry);
            
            // Wet/Dry karıştırma
            output[i] = dry * (1.0f - mix) + wet * mix;
        }
    }
    
    void reset() {
        lfo.reset();
        delayLine.reset();
    }
    
private:
    double sampleRate;
    LFO lfo;
    DelayLine delayLine;
    float baseDelay = 0.02f;  // 20ms
    float mix = 0.5f;
};
```

### Delay Efekti

```cpp
class Delay {
public:
    Delay(double sampleRate) 
        : sampleRate(sampleRate) {}
    
    void setParams(float delayTime, float feedback, 
                   float mix, float damping) {
        this->feedback = feedback;
        this->mix = mix;
        this->damping = damping;
        
        uint32_t delaySamples = static_cast<uint32_t>(
            sampleRate * delayTime);
        delayLine = DelayLine(delaySamples);
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float dry = input[i];
            
            // Delay'den oku
            float delayed = delayLine.read();
            
            // Damping
            filterState = delayed * (1.0f - damping) + 
                         filterState * damping;
            
            // Feedback
            float feedbackSignal = dry + filterState * feedback;
            delayLine.write(feedbackSignal);
            
            // Wet/Dry karıştırma
            output[i] = dry * (1.0f - mix) + filterState * mix;
        }
    }
    
    void reset() {
        delayLine.reset();
        filterState = 0.0f;
    }
    
private:
    double sampleRate;
    DelayLine delayLine{0};
    float feedback = 0.5f;
    float mix = 0.5f;
    float damping = 0.5f;
    float filterState = 0.0f;
};
```

### Flanger Efekti

```cpp
class Flanger {
public:
    Flanger(double sampleRate) 
        : sampleRate(sampleRate),
          lfo(sampleRate),
          delayLine(static_cast<uint32_t>(sampleRate * 0.01)) {}
    
    void setParams(float rate, float depth, float delay, 
                   float feedback) {
        lfo.setParams(rate, LFO::Triangle, depth);
        this->baseDelay = delay;
        this->feedback = feedback;
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float dry = input[i];
            
            // LFO ile delay modülasyonu (çok kısa delay)
            float lfoValue = lfo.process();
            float modDelay = baseDelay + lfoValue * 0.005f;
            
            // Fractional delay
            uint32_t delayInt = static_cast<uint32_t>(
                modDelay * sampleRate);
            float delayed = delayLine.readAt(delayInt);
            
            // Feedback
            float feedbackSignal = dry + delayed * feedback;
            delayLine.write(feedbackSignal);
            
            // Flanger (dry + wet)
            output[i] = dry + delayed;
        }
    }
    
    void reset() {
        lfo.reset();
        delayLine.reset();
    }
    
private:
    double sampleRate;
    LFO lfo;
    DelayLine delayLine;
    float baseDelay = 0.003f;  // 3ms
    float feedback = 0.7f;
};
```

### Phaser Efekti

```cpp
class Phaser {
public:
    Phaser(double sampleRate) : sampleRate(sampleRate) {
        // 4 allpass filter
        for (uint32_t i = 0; i < NUM_STAGES; i++) {
            allpassFilters[i] = AllpassFilter(1000);
        }
    }
    
    void setParams(float rate, float depth, float stages, 
                   float feedback) {
        lfo.setParams(rate, LFO::Sine, depth);
        this->numStages = static_cast<uint32_t>(stages);
        this->feedback = feedback;
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float dry = input[i];
            float wet = dry;
            
            // LFO ile allpass modülasyonu
            float lfoValue = lfo.process();
            
            for (uint32_t s = 0; s < numStages; s++) {
                // Allpass delay'ini modüle et
                float modDelay = baseDelays[s] + 
                               lfoValue * 0.002f;
                allpassFilters[s].setDelay(
                    static_cast<uint32_t>(modDelay * sampleRate));
                
                wet = allpassFilters[s].process(wet);
            }
            
            // Feedback
            wet = wet + dry * feedback;
            
            // Wet/Dry karıştırma
            output[i] = dry * (1.0f - mix) + wet * mix;
        }
    }
    
    void reset() {
        lfo.reset();
        for (auto& filter : allpassFilters) filter.reset();
    }
    
private:
    static const uint32_t NUM_STAGES = 4;
    
    double sampleRate;
    LFO lfo;
    std::array<AllpassFilter, NUM_STAGES> allpassFilters;
    float baseDelays[NUM_STAGES] = {0.003f, 0.004f, 0.005f, 0.006f};
    float feedback = 0.5f;
    float mix = 0.5f;
    uint32_t numStages = 4;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class ModulationEffects {
public:
    ModulationEffects(double sampleRate);
    
    // Chorus
    void setChorusParams(float rate, float depth, 
                         float mix, float delay);
    void enableChorus(bool enable);
    
    // Delay
    void setDelayParams(float time, float feedback, 
                        float mix, float damping);
    void enableDelay(bool enable);
    
    // Flanger
    void setFlangerParams(float rate, float depth, 
                          float delay, float feedback);
    void enableFlanger(bool enable);
    
    // Phaser
    void setPhaserParams(float rate, float depth, 
                         float stages, float feedback);
    void enablePhaser(bool enable);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Durum
    bool isChorusEnabled() const;
    bool isDelayEnabled() const;
    bool isFlangerEnabled() const;
    bool isPhaserEnabled() const;
    
private:
    Chorus chorus;
    Delay delay;
    Flanger flanger;
    Phaser phaser;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Chorus | Delay | Flanger | Phaser |
|--------|--------|-------|---------|--------|
| Latency | < 5ms | Değişken | < 10ms | < 10ms |
| CPU | 0.5% | 0.3% | 0.5% | 0.8% |
| Bellek | 1MB | 2MB | 1MB | 1MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: LFO, delay line
- **Faz 2**: Chorus, delay
- **Faz 3**: Flanger, phaser
- **Faz 4**: Birleşik modülasyon efektleri
- **Tahmini Süre**: 1 hafta (40 adam-saat)
