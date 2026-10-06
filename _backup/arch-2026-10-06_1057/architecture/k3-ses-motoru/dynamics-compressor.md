---
title: "Dinamik İşlemci"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Dinamik İşlemci (Compressor/Limiter/Expander)

## Genel Bakış

Dinamik işlemci, ses sinyalinin dinamik aralığını kontrol eder. COREMUSIC, compressor, limiter ve expander modülleri ile ses seviyesini otomatik olarak yönetir. Attack/release zamanları, ratio ve threshold parametreleri ile hassas kontrol sağlar.

## Teknik Detaylar

### Dinamik İşlemci Mimarisi

```
┌─────────────────────────────────────────────────────┐
│              Dinamik İşlemci Akışı                  │
│                                                     │
│  Input ──→ [Level Detector] ──→ [Gain Computer]    │
│      │           ↓                      ↓           │
│      │     [Envelope]             [Gain Table]     │
│      │           ↓                      ↓           │
│      └──────→ [Multiplier] ←──── [Sidechain]       │
│                    ↓                                │
│               [Output]                              │
└─────────────────────────────────────────────────────┘
```

### Compressor Implementasyonu

```cpp
class Compressor {
public:
    struct CompressorParams {
        float threshold;    // Eşik seviyesi (dB)
        float ratio;        // Sıkıştırma oranı
        float attack;       // Saldırı süresi (ms)
        float release;      // Serbest bırakma süresi (ms)
        float knee;         // Diz bükülme genişliği (dB)
        float makeupGain;   // Makyaj kazancı (dB)
    };
    
    Compressor(double sampleRate) : sampleRate(sampleRate) {
        reset();
    }
    
    void setParams(const CompressorParams& params) {
        this->params = params;
        
        // Time constant'ları hesapla
        attackCoeff = std::exp(-1.0 / (sampleRate * params.attack / 1000.0));
        releaseCoeff = std::exp(-1.0 / (sampleRate * params.release / 1000.0));
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float inputLevel = std::abs(input[i]);
            
            // Level detection (peak veya RMS)
            float detectedLevel = detectLevel(inputLevel);
            
            // Gain computation
            float gain = computeGain(detectedLevel);
            
            // Smoothing
            gain = smoothGain(gain);
            
            // Apply gain
            float makeupLinear = dbToLinear(params.makeupGain);
            output[i] = input[i] * gain * makeupLinear;
        }
    }
    
private:
    CompressorParams params;
    double sampleRate;
    
    float level = 0.0f;
    float gain = 1.0f;
    float gainSmoothed = 1.0f;
    
    float attackCoeff;
    float releaseCoeff;
    
    float detectLevel(float input) {
        // Peak detection
        return input;
    }
    
    float computeGain(float level) {
        float levelDb = linearToDb(level);
        float gainDb = 0.0f;
        
        if (levelDb > params.threshold) {
            // Soft knee
            if (params.knee > 0) {
                float kneeStart = params.threshold - params.knee / 2;
                float kneeEnd = params.threshold + params.knee / 2;
                
                if (levelDb < kneeEnd) {
                    // Knee bölgesinde
                    float x = levelDb - kneeStart;
                    gainDb = (1.0f / params.ratio - 1.0f) * 
                             (x * x) / (2 * params.knee);
                } else {
                    // Hard compression
                    gainDb = (levelDb - params.threshold) * 
                             (1.0f / params.ratio - 1.0f);
                }
            } else {
                // Hard knee
                gainDb = (levelDb - params.threshold) * 
                         (1.0f / params.ratio - 1.0f);
            }
        }
        
        return dbToLinear(gainDb);
    }
    
    float smoothGain(float newGain) {
        float coeff = (newGain < gainSmoothed) ? 
                      attackCoeff : releaseCoeff;
        gainSmoothed = coeff * gainSmoothed + 
                       (1.0f - coeff) * newGain;
        return gainSmoothed;
    }
    
    float linearToDb(float linear) {
        return 20.0f * std::log10(std::max(linear, 1e-10f));
    }
    
    float dbToLinear(float db) {
        return std::pow(10.0f, db / 20.0f);
    }
    
    void reset() {
        level = 0.0f;
        gain = 1.0f;
        gainSmoothed = 1.0f;
    }
};
```

### Limiter Implementasyonu

```cpp
class Limiter {
public:
    Limiter(double sampleRate) : sampleRate(sampleRate) {}
    
    void setParams(float threshold, float release) {
        this->threshold = threshold;
        this->releaseCoeff = std::exp(
            -1.0 / (sampleRate * release / 1000.0));
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float inputLevel = std::abs(input[i]);
            float inputLevelDb = linearToDb(inputLevel);
            
            // Threshold kontrolü
            float gainDb = 0.0f;
            if (inputLevelDb > threshold) {
                gainDb = threshold - inputLevelDb;
            }
            
            float gain = dbToLinear(gainDb);
            
            // Smoothing (sadece release)
            gainSmoothed = releaseCoeff * gainSmoothed + 
                          (1.0f - releaseCoeff) * gain;
            
            output[i] = input[i] * gainSmoothed;
        }
    }
    
private:
    double sampleRate;
    float threshold = 0.0f;
    float releaseCoeff;
    float gainSmoothed = 1.0f;
    
    float linearToDb(float linear) {
        return 20.0f * std::log10(std::max(linear, 1e-10f));
    }
    
    float dbToLinear(float db) {
        return std::pow(10.0f, db / 20.0f);
    }
};
```

### Expander Implementasyonu

```cpp
class Expander {
public:
    Expander(double sampleRate) : sampleRate(sampleRate) {}
    
    void setParams(float threshold, float ratio, 
                   float attack, float release) {
        this->threshold = threshold;
        this->ratio = ratio;
        this->attackCoeff = std::exp(
            -1.0 / (sampleRate * attack / 1000.0));
        this->releaseCoeff = std::exp(
            -1.0 / (sampleRate * release / 1000.0));
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float inputLevel = std::abs(input[i]);
            float inputLevelDb = linearToDb(inputLevel);
            
            // Threshold altında expansion
            float gainDb = 0.0f;
            if (inputLevelDb < threshold) {
                gainDb = (threshold - inputLevelDb) * 
                         (1.0f / ratio - 1.0f);
            }
            
            float gain = dbToLinear(-gainDb);
            
            // Smoothing
            float coeff = (gain < gainSmoothed) ? 
                         attackCoeff : releaseCoeff;
            gainSmoothed = coeff * gainSmoothed + 
                          (1.0f - coeff) * gain;
            
            output[i] = input[i] * gainSmoothed;
        }
    }
    
private:
    double sampleRate;
    float threshold = -60.0f;
    float ratio = 2.0f;
    float attackCoeff;
    float releaseCoeff;
    float gainSmoothed = 1.0f;
    
    float linearToDb(float linear) {
        return 20.0f * std::log10(std::max(linear, 1e-10f));
    }
    
    float dbToLinear(float db) {
        return std::pow(10.0f, db / 20.0f);
    }
};
```

### Dinamik İşlemci Birleşimi

```cpp
class DynamicsProcessor {
public:
    DynamicsProcessor(double sampleRate) 
        : compressor(sampleRate),
          limiter(sampleRate),
          expander(sampleRate) {}
    
    void setCompressorParams(const Compressor::CompressorParams& params) {
        compressor.setParams(params);
    }
    
    void setLimiterParams(float threshold, float release) {
        limiter.setParams(threshold, release);
    }
    
    void setExpanderParams(float threshold, float ratio,
                           float attack, float release) {
        expander.setParams(threshold, ratio, attack, release);
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t ch = 0; ch < channelCount; ch++) {
            // Sıra: Expander → Compressor → Limiter
            expander.process(input[ch], tempBuffer[ch], frameCount);
            compressor.process(tempBuffer[ch], output[ch], frameCount);
            limiter.process(output[ch], output[ch], frameCount);
        }
    }
    
private:
    Compressor compressor;
    Limiter limiter;
    Expander expander;
    
    float** tempBuffer;
    uint32_t channelCount = 2;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class DynamicsModule {
public:
    DynamicsModule(double sampleRate);
    
    // Compressor
    void setCompressorThreshold(float thresholdDb);
    void setCompressorRatio(float ratio);
    void setCompressorAttack(float attackMs);
    void setCompressorRelease(float releaseMs);
    void setCompressorKnee(float kneeDb);
    void setCompressorMakeupGain(float gainDb);
    
    // Limiter
    void setLimiterThreshold(float thresholdDb);
    void setLimiterRelease(float releaseMs);
    
    // Expander
    void setExpanderThreshold(float thresholdDb);
    void setExpanderRatio(float ratio);
    void setExpanderAttack(float attackMs);
    void setExpanderRelease(float releaseMs);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Gain reduction metering
    float getGainReduction() const;
    float getPeakLevel() const;
    float getRMSLevel() const;
    
private:
    DynamicsProcessor processor;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.01ms | 0.005ms |
| CPU (Compressor) | < 1% | 0.5% |
| CPU (Limiter) | < 0.5% | 0.3% |
| CPU (Expander) | < 1% | 0.4% |
| Attack Aralığı | 0.1-100ms | 0.1-100ms |
| Release Aralığı | 10-2000ms | 10-2000ms |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Compressor implementasyonu
- **Faz 2**: Limiter
- **Faz 3**: Expander
- **Faz 4**: Birleşik dinamik işlemci
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
