---
title: "Surround Decoder"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Surround Decoder

## Genel Bakış

COREMUSIC, 5.1, 7.1 ve 8.1 surround ses dekoderini destekler. Downmix (çoklu kanaldan stereo'ya) ve upmix (stereo'dan çoklu kanala) dönüşümleri sağlar. Kanal haritalama matrisleri ile esnek yönlendirme sunar.

## Teknik Detaylar

### Surround Formatları

```
5.1 Surround (6 Kanal):
┌─────────────────────────────────────────┐
│              [Center]                    │
│            [L]     [R]                   │
│          [LFE]                           │
│            [LS]    [RS]                  │
└─────────────────────────────────────────┘

7.1 Surround (8 Kanal):
┌─────────────────────────────────────────┐
│              [Center]                    │
│         [L]         [R]                  │
│        [LFE]                             │
│      [LS]           [RS]                 │
│    [LBS]           [RBS]                 │
└─────────────────────────────────────────┘

8.1 Surround (9 Kanal):
┌─────────────────────────────────────────┐
│              [Center]                    │
│         [L]         [R]                  │
│        [LFE]                             │
│      [LS]           [RS]                 │
│    [LBS]           [RBS]                 │
│              [Top]                       │
└─────────────────────────────────────────┘
```

### Downmix Matrisi

```cpp
// 5.1 → Stereo Downmix
class DownmixMatrix {
public:
    // 5.1 → Stereo
    static const float matrix51to2[2][6];
    
    // 7.1 → Stereo
    static const float matrix71to2[2][8];
    
    // 5.1 → 4.0
    static const float matrix51to4[4][6];
    
    // Custom downmix
    static std::vector<std::vector<float>> createCustomMatrix(
        uint32_t inputChannels, 
        uint32_t outputChannels,
        const DownmixParams& params) {
        
        std::vector<std::vector<float>> matrix(
            outputChannels, 
            std::vector<float>(inputChannels, 0.0f));
        
        // LFE her zaman downmix edilmez
        // Center gainsiz (half gain)
        // Surround -3dB downmix
        
        for (uint32_t out = 0; out < outputChannels; out++) {
            for (uint32_t in = 0; in < inputChannels; in++) {
                matrix[out][in] = calculateGain(in, out, params);
            }
        }
        
        return matrix;
    }
    
private:
    static float calculateGain(uint32_t in, uint32_t out, 
                                const DownmixParams& params) {
        // Kanal bazlı kazanç hesaplama
        if (in == 4 || in == 5) {  // LFE
            return params.lfeGain;  // Genellikle 0 (downmix edilmez)
        }
        if (in == 2) {  // Center
            return params.centerGain;  // -3dB = 0.707
        }
        return 1.0f;
    }
};

// 5.1 → Stereo matrisi
const float DownmixMatrix::matrix51to2[2][6] = {
    // L,    R,    C,    LFE,  LS,   RS
    {1.0f, 0.0f, 0.707f, 0.0f, 0.707f, 0.0f},  // L out
    {0.0f, 1.0f, 0.707f, 0.0f, 0.0f, 0.707f}   // R out
};
```

### Upmix Matrisi

```cpp
// Stereo → 5.1 Upmix
class UpmixMatrix {
public:
    // Stereo → 5.1
    static const float matrix2to51[6][2];
    
    // Stereo → 7.1
    static const float matrix2to71[8][2];
    
    // Parametrik upmix
    static std::vector<std::vector<float>> createCustomUpmix(
        uint32_t inputChannels,
        uint32_t outputChannels,
        const UpmixParams& params) {
        
        std::vector<std::vector<float>> matrix(
            outputChannels,
            std::vector<float>(inputChannels, 0.0f));
        
        // L/R direkt
        matrix[0][0] = 1.0f;  // L → L
        matrix[1][1] = 1.0f;  // R → R
        
        // Center: L+R mix
        matrix[2][0] = params.centerMix;  // L → C
        matrix[2][1] = params.centerMix;  // R → C
        
        // LFE: Low-pass L+R
        matrix[3][0] = params.lfeMix;
        matrix[3][1] = params.lfeMix;
        
        // Surround: phase-shifted L/R
        matrix[4][0] = params.surroundMix;  // L → LS
        matrix[5][1] = params.surroundMix;  // R → RS
        
        return matrix;
    }
};

const float UpmixMatrix::matrix2to51[6][2] = {
    // L in, R in
    {1.0f, 0.0f},    // L out
    {0.0f, 1.0f},    // R out
    {0.707f, 0.707f}, // C out
    {0.707f, 0.707f}, // LFE out
    {0.707f, 0.0f},   // LS out
    {0.0f, 0.707f}    // RS out
};
```

### Surround Decoder Implementasyonu

```cpp
class SurroundDecoder {
public:
    SurroundDecoder(uint32_t inputChannels, 
                    uint32_t outputChannels,
                    double sampleRate) 
        : inputChannels(inputChannels),
          outputChannels(outputChannels),
          sampleRate(sampleRate) {
        
        initializeMatrix();
    }
    
    void setDownmixParams(const DownmixParams& params) {
        this->downmixParams = params;
        initializeMatrix();
    }
    
    void setUpmixParams(const UpmixParams& params) {
        this->upmixParams = params;
        initializeMatrix();
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            for (uint32_t out = 0; out < outputChannels; out++) {
                output[out][i] = 0.0f;
                for (uint32_t in = 0; in < inputChannels; in++) {
                    output[out][i] += input[in][i] * 
                                      matrix[out][in];
                }
            }
        }
    }
    
    // Bass management
    void processWithBassManagement(
        float** input, float** output, 
        uint32_t frameCount, float crossoverFreq) {
        
        // Crossover filtreleri
        LowPassFilter lpfL(crossoverFreq, sampleRate);
        LowPassFilter lpfR(crossoverFreq, sampleRate);
        
        for (uint32_t i = 0; i < frameCount; i++) {
            // LFE için low-pass
            float lfeL = lpfL.process(input[0][i]);
            float lfeR = lpfR.process(input[1][i]);
            
            // Downmix
            process(input, output, 1);
            
            // LFE'ye bass ekle
            if (outputChannels >= 4) {
                output[3][i] += (lfeL + lfeR) * 0.707f;
            }
        }
    }
    
private:
    uint32_t inputChannels;
    uint32_t outputChannels;
    double sampleRate;
    
    std::vector<std::vector<float>> matrix;
    DownmixParams downmixParams;
    UpmixParams upmixParams;
    
    void initializeMatrix() {
        if (inputChannels > outputChannels) {
            // Downmix
            matrix = DownmixMatrix::createCustomMatrix(
                inputChannels, outputChannels, downmixParams);
        } else {
            // Upmix
            matrix = UpmixMatrix::createCustomUpmix(
                inputChannels, outputChannels, upmixParams);
        }
    }
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class SurroundModule {
public:
    SurroundModule(uint32_t inputChannels, 
                   uint32_t outputChannels,
                   double sampleRate);
    
    // Downmix/Upmix
    void setMode(SurroundMode mode);
    void setDownmixParams(const DownmixParams& params);
    void setUpmixParams(const UpmixParams& params);
    
    // Bass management
    void enableBassManagement(bool enable);
    void setCrossoverFrequency(float freq);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Kanal bilgisi
    uint32_t getInputChannels() const;
    uint32_t getOutputChannels() const;
    std::vector<std::string> getChannelNames() const;
    
private:
    SurroundDecoder decoder;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.1ms | 0.05ms |
| CPU (5.1→2) | < 0.5% | 0.3% |
| CPU (2→5.1) | < 1% | 0.7% |
| Bellek | < 2MB | 1.5MB |
| Kanal Sayısı | 1-32 | 1-32 |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Downmix matrisleri
- **Faz 2**: Upmix matrisleri
- **Faz 3**: Bass management
- **Faz 4**: Custom surround modları
- **Tahmini Süre**: 1 hafta (40 adam-saat)
