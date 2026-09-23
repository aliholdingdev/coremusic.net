---
title: "Sample Rate Conversion"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Sample Rate Conversion (SRC)

## Genel Bakış

COREMUSIC, farklı örnekleme hızları arasında asenkron dönüşüm sağlar. Yüksek kaliteli interpolasyon ve anti-aliasing filtreleri ile 44.1kHz ↔ 48kHz ↔ 96kHz ↔ 192kHz dönüşümleri yapar.

## Teknik Detaylar

### SRC Algoritması

```
┌─────────────────────────────────────────────────────┐
│              Sample Rate Conversion Akışı           │
│                                                     │
│  Input (44.1kHz) ──→ [Upsample] ──→ [Filter]      │
│                          ↓              ↓           │
│                     [2x Interpolate] [Anti-alias]  │
│                          ↓              ↓           │
│                     [Downsample] ←──── [Filter]    │
│                          ↓                          │
│  Output (48kHz) ───────────────────────────────────│
└─────────────────────────────────────────────────────┘
```

### Asenkron SRC

```cpp
class AsyncSRC {
public:
    AsyncSRC(double inputRate, double outputRate) 
        : inputRate(inputRate), outputRate(outputRate) {
        
        // Upsample/downsample oranlarını hesapla
        calculateRatios();
        
        // Filtre katsayılarını hesapla
        designFilter();
    }
    
    void process(const float* input, uint32_t inputFrames,
                 float* output, uint32_t* outputFrames) {
        
        uint32_t outIndex = 0;
        double phase = 0.0;
        
        while (phase < inputFrames - 1) {
            uint32_t index = static_cast<uint32_t>(phase);
            double frac = phase - index;
            
            // Linear interpolation (basit)
            if (index + 1 < inputFrames) {
                output[outIndex] = input[index] * (1.0 - frac) + 
                                   input[index + 1] * frac;
            } else {
                output[outIndex] = input[index];
            }
            
            // Filtre uygula
            output[outIndex] = filter.process(output[outIndex]);
            
            outIndex++;
            phase += ratio;
        }
        
        *outputFrames = outIndex;
    }
    
    // High-quality Sinc interpolation
    void processSinc(const float* input, uint32_t inputFrames,
                     float* output, uint32_t* outputFrames) {
        
        uint32_t outIndex = 0;
        double phase = 0.0;
        
        while (phase < inputFrames - sincTaps) {
            uint32_t index = static_cast<uint32_t>(phase);
            double frac = phase - index;
            
            float sample = 0.0f;
            
            // Sinc interpolation
            for (int tap = -sincTaps; tap <= sincTaps; tap++) {
                int sampleIndex = index + tap;
                if (sampleIndex >= 0 && 
                    sampleIndex < static_cast<int>(inputFrames)) {
                    double sinc = sincFunction(
                        (tap - frac) * M_PI);
                    double window = windowFunction(
                        tap - frac, sincTaps);
                    sample += input[sampleIndex] * sinc * window;
                }
            }
            
            output[outIndex] = sample;
            outIndex++;
            phase += ratio;
        }
        
        *outputFrames = outIndex;
    }
    
private:
    double inputRate;
    double outputRate;
    double ratio;
    
    static const int sincTaps = 8;
    
    void calculateRatios() {
        // Basit kesirli oran
        ratio = outputRate / inputRate;
    }
    
    double sincFunction(double x) {
        if (std::abs(x) < 1e-10) return 1.0;
        return std::sin(x) / x;
    }
    
    double windowFunction(double x, int taps) {
        // Blackman window
        double n = x / taps;
        return 0.42 - 0.50 * std::cos(2.0 * M_PI * n) + 
               0.08 * std::cos(4.0 * M_PI * n);
    }
    
    // Anti-aliasing low-pass filtre
    class LowPassFilter {
    public:
        float process(float input) {
            y0 = b0 * input + b1 * x1 + b2 * x2 - a1 * y1 - a2 * y2;
            x2 = x1; x1 = input;
            y2 = y1; y1 = y0;
            return y0;
        }
    private:
        float b0 = 0.1f, b1 = 0.2f, b2 = 0.1f;
        float a1 = -0.8f, a2 = 0.2f;
        float x1 = 0, x2 = 0, y1 = 0, y2 = 0;
    };
    
    LowPassFilter filter;
};
```

### Zincir SRC (Multi-stage)

```cpp
class MultiStageSRC {
public:
    // 44.1k → 48k için zincir dönüşüm
    // 44100 × 160 = 7056000
    // 7056000 / 147 = 48000
    void process441to48(const float* input, uint32_t inputFrames,
                        float* output, uint32_t* outputFrames) {
        
        // Aşama 1: 44100 → 7056000 (x160 upsample)
        stage1.process(input, inputFrames, 
                       tempBuffer, &tempFrames);
        
        // Aşama 2: 7056000 → 48000 (/147 downsample)
        stage2.process(tempBuffer, tempFrames,
                       output, outputFrames);
    }
    
    // 48k → 96k için (2x upsample)
    void process48to96(const float* input, uint32_t inputFrames,
                       float* output, uint32_t* outputFrames) {
        stage2x.process(input, inputFrames, 
                        output, outputFrames);
    }
    
    // 96k → 44.1k için
    void process96to441(const float* input, uint32_t inputFrames,
                        float* output, uint32_t* outputFrames) {
        // 96000 × 147 = 14112000
        // 14112000 / 320 = 44100
        stage1.process(input, inputFrames,
                       tempBuffer, &tempFrames);
        stage2.process(tempBuffer, tempFrames,
                       output, outputFrames);
    }
    
private:
    AsyncSRC stage1{44100.0, 7056000.0};
    AsyncSRC stage2{7056000.0, 48000.0};
    AsyncSRC stage2x{48000.0, 96000.0};
    
    std::vector<float> tempBuffer;
    uint32_t tempFrames;
};
```

### Quality Presetleri

```cpp
enum class SRCQuality {
    Draft,      // Hız öncelikli, düşük kalite
    Standard,   // Dengeli
    High,       // Yüksek kalite
    Ultra       // En yüksek kalite
};

struct SRCProfile {
    int filterTaps;
    bool useSinc;
    uint32_t oversampling;
};

SRCProfile getProfile(SRCQuality quality) {
    switch (quality) {
        case SRCQuality::Draft:
            return {4, false, 2};
        case SRCQuality::Standard:
            return {8, true, 4};
        case SRCQuality::High:
            return {16, true, 8};
        case SRCQuality::Ultra:
            return {32, true, 16};
    }
    return {8, true, 4};
}
```

## API / Arayüz

```cpp
namespace neva::dsp {

class SampleRateConverter {
public:
    SampleRateConverter(double inputRate, double outputRate,
                        SRCQuality quality = SRCQuality::High);
    
    // Dönüşüm
    uint32_t process(const float* input, uint32_t inputFrames,
                     float* output, uint32_t maxOutputFrames);
    
    // Ratio sorgusu
    double getRatio() const;
    double getInputRate() const;
    double getOutputRate() const;
    
    // Kalite
    void setQuality(SRCQuality quality);
    SRCQuality getQuality() const;
    
    // Delay bilgisi
    uint32_t getLatency() const;
    
private:
    MultiStageSRC converter;
    SRCQuality quality;
    double inputRate;
    double outputRate;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Draft | Standard | High | Ultra |
|--------|-------|----------|------|-------|
| Kalite (SNR) | 60dB | 80dB | 100dB | 120dB |
| CPU | 0.5% | 1% | 2% | 5% |
| Latency | 2ms | 5ms | 10ms | 20ms |
| Bellek | 10KB | 50KB | 200KB | 1MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Temel linear interpolation
- **Faz 2**: Sinc interpolation
- **Faz 3**: Multi-stage SRC
- **Faz 4**: Quality presetleri
- **Tahmini Süre**: 2 hafta (80 adam-saat)
