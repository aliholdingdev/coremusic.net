---
title: "Bit Depth Conversion"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Bit Depth Conversion

## Genel Bakış

COREMUSIC, farklı bit derinlikleri arasında dönüşüm sağlar. 16-bit, 24-bit ve 32-bit formatları arasında dithering ve noise shaping uygulayarak dijital ses kalitesini korur.

## Teknik Detaylar

### Bit Derinliği Dönüşüm Tablosu

```
┌─────────────────────────────────────────────────────────┐
│              Bit Derinliği Dönüşümü                    │
├──────────┬──────────┬──────────┬────────────────────────┤
│ Kaynak   │ Hedef    │ Dither   │ Noise Shaping          │
├──────────┼──────────┼──────────┼────────────────────────┤
│ 32-bit   │ 16-bit   │ Evet     │ 1st order              │
│ 32-bit   │ 24-bit   │ Evet     │ 1st order              │
│ 24-bit   │ 16-bit   │ Evet     │ 2nd order              │
│ 32-bit   │ 24-bit   │ Hayır    │ -                      │
│ 16-bit   │ 24-bit   │ Hayır    │ -                      │
└──────────┴──────────┴──────────┴────────────────────────┘
```

### Dithering Implementasyonu

```cpp
class DitherProcessor {
public:
    enum DitherType {
        Rectangular,    // RPDF (düzgün olasılık yoğunluğu dağılımı)
        Triangular,     // TPDF (üçgen olasılık yoğunluğu dağılımı)
        HP              // HP dither (high-pass dither)
    };
    
    DitherProcessor(uint32_t bitsPerSample, DitherType type) 
        : bitsPerSample(bitsPerSample), type(type) {
        
        // Dither seviyesi hesapla
        ditherLevel = 1.0f / std::pow(2.0f, bitsPerSample);
    }
    
    float process(float input) noexcept {
        float dither = 0.0f;
        
        switch (type) {
            case Rectangular:
                dither = rectangularDither();
                break;
            case Triangular:
                dither = triangularDither();
                break;
            case HP:
                dither = hpDither();
                break;
        }
        
        return input + dither * ditherLevel;
    }
    
    void processBatch(const float* input, float* output,
                      uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            output[i] = process(input[i]);
        }
    }
    
private:
    uint32_t bitsPerSample;
    DitherType type;
    float ditherLevel;
    
    std::mt19937 rng{std::random_device{}()};
    
    float rectangularDither() {
        std::uniform_real_distribution<float> dist(-1.0f, 1.0f);
        return dist(rng);
    }
    
    float triangularDither() {
        // TPDF = sum of two uniform distributions
        std::uniform_real_distribution<float> dist(-1.0f, 1.0f);
        return (dist(rng) + dist(rng)) * 0.5f;
    }
    
    float hpDither() {
        // High-pass dither (noise shaping)
        float current = triangularDither();
        float output = current - prevDither + 0.5f * prevOutput;
        prevDither = current;
        prevOutput = output;
        return output;
    }
    
    float prevDither = 0.0f;
    float prevOutput = 0.0f;
};
```

### Noise Shaping

```cpp
class NoiseShaper {
public:
    // 1st order noise shaping
    float process1stOrder(float input) noexcept {
        float quantized = quantize(input);
        float error = input - quantized;
        
        // Hata biriktirici
        accumulator += error * feedbackGain;
        
        return quantized;
    }
    
    // 2nd order noise shaping
    float process2ndOrder(float input) noexcept {
        float quantized = quantize(input);
        float error = input - quantized;
        
        // İki aşamalı hata biriktirici
        float filtered = error * feedbackGain1 + 
                        prevError * feedbackGain2;
        accumulator += filtered;
        
        prevError = error;
        
        return quantized;
    }
    
    void reset() {
        accumulator = 0.0f;
        prevError = 0.0f;
    }
    
private:
    float accumulator = 0.0f;
    float prevError = 0.0f;
    float feedbackGain = 0.5f;
    float feedbackGain1 = 0.7f;
    float feedbackGain2 = -0.3f;
    
    float quantize(float input) {
        // 16-bit quantization
        float scaled = input * 32767.0f;
        float quantized = std::round(scaled);
        return quantized / 32767.0f;
    }
};
```

### Bit Depth Converter

```cpp
class BitDepthConverter {
public:
    BitDepthConverter(uint32_t sourceBits, uint32_t targetBits) 
        : sourceBits(sourceBits), targetBits(targetBits) {
        
        dither = std::make_unique<DitherProcessor>(
            targetBits, DitherProcessor::TPDF);
        noiseShaper = std::make_unique<NoiseShaper>();
    }
    
    // 32-bit float → 16-bit PCM
    void convert32to16(const float* input, int16_t* output,
                       uint32_t frameCount) {
        for (uint32_t i = 0; i < frameCount; i++) {
            float sample = input[i];
            
            // Dither uygula
            if (enableDither) {
                sample = dither->process(sample);
            }
            
            // Quantize
            sample = std::clamp(sample, -1.0f, 1.0f);
            float scaled = sample * 32767.0f;
            
            // Round to nearest
            output[i] = static_cast<int16_t>(std::round(scaled));
        }
    }
    
    // 32-bit float → 24-bit PCM
    void convert32to24(const float* input, uint8_t* output,
                       uint32_t frameCount) {
        for (uint32_t i = 0; i < frameCount; i++) {
            float sample = input[i];
            
            if (enableDither) {
                sample = dither->process(sample);
            }
            
            sample = std::clamp(sample, -1.0f, 1.0f);
            int32_t scaled = static_cast<int32_t>(
                sample * 8388607.0f);  // 2^23 - 1
            
            // 3 byte'a yaz (big-endian)
            output[i * 3] = (scaled >> 16) & 0xFF;
            output[i * 3 + 1] = (scaled >> 8) & 0xFF;
            output[i * 3 + 2] = scaled & 0xFF;
        }
    }
    
    // 16-bit PCM → 32-bit float
    void convert16to32(const int16_t* input, float* output,
                       uint32_t frameCount) {
        for (uint32_t i = 0; i < frameCount; i++) {
            output[i] = input[i] / 32768.0f;
        }
    }
    
    // 24-bit PCM → 32-bit float
    void convert24to32(const uint8_t* input, float* output,
                       uint32_t frameCount) {
        for (uint32_t i = 0; i < frameCount; i++) {
            int32_t sample = (input[i * 3] << 16) |
                            (input[i * 3 + 1] << 8) |
                            input[i * 3 + 2];
            
            // Sign extend
            if (sample & 0x800000) {
                sample |= 0xFF000000;
            }
            
            output[i] = sample / 8388608.0f;
        }
    }
    
    void setDitherEnabled(bool enable) { enableDither = enable; }
    void setDitherType(DitherProcessor::DitherType type) {
        dither = std::make_unique<DitherProcessor>(
            targetBits, type);
    }
    void setNoiseShapingEnabled(bool enable) {
        enableNoiseShaping = enable;
    }
    
private:
    uint32_t sourceBits;
    uint32_t targetBits;
    bool enableDither = true;
    bool enableNoiseShaping = false;
    
    std::unique_ptr<DitherProcessor> dither;
    std::unique_ptr<NoiseShaper> noiseShaper;
};
```

### Truncation vs Rounding

```cpp
// Truncation (daha basit, daha fazla hata)
int16_t truncate(float sample) {
    float scaled = sample * 32767.0f;
    return static_cast<int16_t>(scaled);  // Alt bağlantılama
}

// Round to nearest (daha iyi)
int16_t roundToNearest(float sample) {
    float scaled = sample * 32767.0f;
    return static_cast<int16_t>(std::round(scaled));
}

// Dithered rounding (en iyi)
int16_t ditheredRound(float sample, float dither) {
    float scaled = sample * 32767.0f + dither;
    return static_cast<int16_t>(std::round(scaled));
}
```

## API / Arayüz

```cpp
namespace neva::dsp {

class BitDepthModule {
public:
    BitDepthModule(uint32_t sourceBits, uint32_t targetBits);
    
    // Dither
    void setDitherEnabled(bool enable);
    void setDitherType(DitherProcessor::DitherType type);
    
    // Noise shaping
    void setNoiseShapingEnabled(bool enable);
    
    // Dönüşüm
    void convert(const float* input, void* output,
                 uint32_t frameCount);
    
    // Bilgi
    uint32_t getSourceBits() const;
    uint32_t getTargetBits() const;
    
private:
    BitDepthConverter converter;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.001ms | 0.0005ms |
| CPU (32→16) | < 0.5% | 0.3% |
| CPU (32→24) | < 0.3% | 0.2% |
| SNR (dithered) | > 96dB | 98dB |
| Bellek | < 1MB | 0.5MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Temel truncation/rounding
- **Faz 2**: Dithering (RPDF, TPDF)
- **Faz 3**: Noise shaping
- **Faz 4**: Tüm format dönüşümleri
- **Tahmini Süre**: 1 hafta (40 adam-saat)
