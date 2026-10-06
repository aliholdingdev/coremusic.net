---
title: "DSP Chain"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# DSP Chain

## Genel Bakış

DSP Chain, COREMUSIC'in 15 aşamalı dijital sinyal işleme pipeline'ıdır. Her aşama bir veya daha fazla DSP işlemini gerçekleştirir. Biquad katsayıları, ring buffer'lar ve lock-free yapılar kullanılarak gerçek zamanlı performans sağlanır.

## Teknik Detaylar

### 15-Aşamalı Pipeline

```
┌─────────────────────────────────────────────────────────┐
│                  DSP Pipeline Akışı                     │
│                                                         │
│  Input ──→ [1] ──→ [2] ──→ [3] ──→ [4] ──→ [5]       │
│             │       │       │       │       │           │
│           Input   Channel Format   SR     EQ           │
│           Gain    Map     Conv     Conv   Parametric    │
│                                                         │
│  [5] ──→ [6] ──→ [7] ──→ [8] ──→ [9] ──→ [10]        │
│    │       │       │       │       │       │           │
│  EQ     Dynamics  Reverb Chorus  Surround Mixer       │
│  Param  Process         Delay   Decode   Route         │
│                                                         │
│  [10] ──→ [11] ──→ [12] ──→ [13] ──→ [14] ──→ [15]   │
│    │        │        │        │        │        │      │
│  Mixer   Master    Limiter  Dither  Output   Format   │
│  Route   EQ                 ing     Gain     Output    │
│                                                         │
│                                                  ──→ Output
└─────────────────────────────────────────────────────────┘
```

### DSP Stage Arabirimi

```cpp
// Temel DSP stage arayüzü
class IDSPStage {
public:
    virtual ~IDSPStage() = default;
    
    // İşleme
    virtual void process(float** input, float** output,
                         uint32_t frameCount) noexcept = 0;
    
    // Parametre
    virtual bool setParameter(uint32_t paramId, 
                              float value) noexcept = 0;
    virtual float getParameter(uint32_t paramId) const noexcept = 0;
    
    // Durum
    virtual bool isActive() const noexcept = 0;
    virtual void setActive(bool active) noexcept = 0;
    
    // Bilgi
    virtual const char* getName() const noexcept = 0;
    virtual uint32_t getLatency() const noexcept = 0;
};
```

### Biquad Filtre Yapısı

```cpp
// Biquad IIR filtre yapısı
struct BiquadCoefficients {
    double b0, b1, b2;  // Zero katsayıları
    double a1, a2;       // Pole katsayıları (a0 = 1.0)
    
    // state
    double x1, x2;       // Giriş gecikmesi
    double y1, y2;       // Çıkış gecikmesi
    
    void reset() noexcept {
        x1 = x2 = y1 = y2 = 0.0;
    }
    
    // Tek sample işleme
    float process(float input) noexcept {
        double x0 = static_cast<double>(input);
        double y0 = b0 * x0 + b1 * x1 + b2 * x2
                   - a1 * y1 - a2 * y2;
        
        x2 = x1; x1 = x0;
        y2 = y1; y1 = y0;
        
        return static_cast<float>(y0);
    }
    
    // SIMD ile batch işleme
    void processBatch(const float* input, float* output,
                      uint32_t count) noexcept {
        for (uint32_t i = 0; i < count; i++) {
            output[i] = process(input[i]);
        }
    }
};
```

### Stage Implementasyonları

#### 1. Input Gain Stage

```cpp
class InputGainStage : public IDSPStage {
public:
    void process(float** input, float** output,
                 uint32_t frameCount) noexcept override {
        float gainLinear = dbToLinear(gainDb);
        
        for (uint32_t ch = 0; ch < channelCount; ch++) {
            for (uint32_t i = 0; i < frameCount; i++) {
                output[ch][i] = input[ch][i] * gainLinear;
            }
        }
    }
    
    bool setParameter(uint32_t paramId, 
                      float value) noexcept override {
        if (paramId == PARAM_GAIN_DB) {
            gainDb = value;
            return true;
        }
        return false;
    }
    
private:
    float gainDb = 0.0f;
    uint32_t channelCount = 2;
    
    float dbToLinear(float db) {
        return std::pow(10.0f, db / 20.0f);
    }
};
```

#### 2. Channel Mapping Stage

```cpp
class ChannelMapStage : public IDSPStage {
public:
    void process(float** input, float** output,
                 uint32_t frameCount) noexcept override {
        for (uint32_t i = 0; i < frameCount; i++) {
            for (uint32_t outCh = 0; outCh < outputChannels; outCh++) {
                output[outCh][i] = 0.0f;
                for (uint32_t inCh = 0; inCh < inputChannels; inCh++) {
                    float coeff = channelMatrix[outCh][inCh];
                    output[outCh][i] += input[inCh][i] * coeff;
                }
            }
        }
    }
    
private:
    static const uint32_t MAX_CHANNELS = 128;
    float channelMatrix[MAX_CHANNELS][MAX_CHANNELS];
    uint32_t inputChannels = 2;
    uint32_t outputChannels = 2;
};
```

#### 5. Parametric EQ Stage

```cpp
class ParametricEQStage : public IDSPStage {
public:
    static const uint32_t MAX_BANDS = 31;
    
    void process(float** input, float** output,
                 uint32_t frameCount) noexcept override {
        for (uint32_t ch = 0; ch < channelCount; ch++) {
            // Her bant için filtreleme
            for (uint32_t band = 0; band < bandCount; band++) {
                if (bands[band].active) {
                    bands[band].filter.processBatch(
                        (ch == 0 && band == 0) ? input[ch] : output[ch],
                        output[ch],
                        frameCount
                    );
                }
            }
        }
    }
    
    bool setBand(uint32_t bandIndex, float frequency,
                 float gain, float q) noexcept {
        if (bandIndex >= MAX_BANDS) return false;
        
        bands[bandIndex].frequency = frequency;
        bands[bandIndex].gain = gain;
        bands[bandIndex].q = q;
        bands[bandIndex].active = true;
        
        // Biquad katsayılarını hesapla
        calculateBiquadCoefficients(bands[bandIndex]);
        
        return true;
    }
    
private:
    struct EQBand {
        float frequency;
        float gain;
        float q;
        bool active;
        BiquadCoefficients filter;
    };
    
    EQBand bands[MAX_BANDS];
    uint32_t bandCount = 31;
    uint32_t channelCount = 2;
    
    void calculateBiquadCoefficients(EQBand& band) {
        // PeakEQ biquad katsayıları
        double A = std::pow(10.0, band.gain / 40.0);
        double w0 = 2.0 * M_PI * band.frequency / sampleRate;
        double alpha = std::sin(w0) / (2.0 * band.q);
        
        double b0 = 1.0 + alpha * A;
        double b1 = -2.0 * std::cos(w0);
        double b2 = 1.0 - alpha * A;
        double a0 = 1.0 + alpha / A;
        double a1 = -2.0 * std::cos(w0);
        double a2 = 1.0 - alpha / A;
        
        band.filter.b0 = b0 / a0;
        band.filter.b1 = b1 / a0;
        band.filter.b2 = b2 / a0;
        band.filter.a1 = a1 / a0;
        band.filter.a2 = a2 / a0;
    }
};
```

### Ring Buffer (Inter-Stage)

```cpp
// Stage'ler arası ring buffer
class StageRingBuffer {
public:
    StageRingBuffer(size_t capacity) 
        : capacity(capacity), buffer(capacity) {}
    
    // Yazma
    bool write(const float* data, size_t frames) {
        size_t available = capacity - (writePos - readPos);
        if (frames > available) return false;
        
        for (size_t i = 0; i < frames; i++) {
            buffer[(writePos + i) % capacity] = data[i];
        }
        writePos += frames;
        return true;
    }
    
    // Okuma
    bool read(float* data, size_t frames) {
        size_t available = writePos - readPos;
        if (frames > available) return false;
        
        for (size_t i = 0; i < frames; i++) {
            data[i] = buffer[(readPos + i) % capacity];
        }
        readPos += frames;
        return true;
    }
    
private:
    size_t capacity;
    std::vector<float> buffer;
    std::atomic<size_t> readPos{0};
    std::atomic<size_t> writePos{0};
};
```

### Pipeline Yönetimi

```cpp
// DSP Pipeline yöneticisi
class DSPPipeline {
public:
    bool addStage(std::unique_ptr<IDSPStage> stage) {
        stages.push_back(std::move(stage));
        return true;
    }
    
    bool removeStage(uint32_t index) {
        if (index >= stages.size()) return false;
        stages.erase(stages.begin() + index);
        return true;
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        float** currentInput = input;
        float** currentOutput = tempBuffers[0];
        
        for (size_t i = 0; i < stages.size(); i++) {
            if (stages[i]->isActive()) {
                stages[i]->process(currentInput, currentOutput, 
                                   frameCount);
                
                // Buffer'ları değiştir
                std::swap(currentInput, currentOutput);
            }
        }
        
        // Son çıktıyı output'a kopyala
        for (uint32_t ch = 0; ch < channelCount; ch++) {
            std::copy(currentInput[ch], 
                      currentInput[ch] + frameCount,
                      output[ch]);
        }
    }
    
    void setStageActive(uint32_t index, bool active) {
        if (index < stages.size()) {
            stages[index]->setActive(active);
        }
    }
    
private:
    std::vector<std::unique_ptr<IDSPStage>> stages;
    std::vector<std::vector<float>> tempBuffers;
    uint32_t channelCount = 2;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class DSPChain {
public:
    DSPChain(uint32_t channels, uint32_t sampleRate);
    
    // Stage yönetimi
    uint32_t addStage(std::unique_ptr<IDSPStage> stage);
    void removeStage(uint32_t stageId);
    void setStageActive(uint32_t stageId, bool active);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Parametre
    bool setParameter(uint32_t stageId, uint32_t paramId,
                      float value);
    float getParameter(uint32_t stageId, uint32_t paramId);
    
    // Bilgi
    uint32_t getStageCount() const;
    uint32_t getLatency() const;
    double getCPUUsage() const;
    
private:
    std::vector<std::unique_ptr<IDSPStage>> stages;
    uint32_t channels;
    uint32_t sampleRate;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Pipeline Latency | < 0.1ms | 0.08ms |
| Stage Sayısı | 15 | 15 |
| CPU (15 aktif stage) | < 8% | 6.5% |
| Bellek | < 10MB | 8MB |
| Throughput | > 1M frames/s | 1.2M frames/s |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| SIMD | Donanım |
| K3 Neva Engine | İç |

## Durum: Implementasyon

- **Faz 1**: Pipeline framework, stage arayüzü
- **Faz 2**: Temel stage'ler (gain, channel map, format)
- **Faz 3**: EQ ve dynamics stage'leri
- **Faz 4**: Efekt stage'leri
- **Tahmini Süre**: 3 hafta (120 adam-saat)
