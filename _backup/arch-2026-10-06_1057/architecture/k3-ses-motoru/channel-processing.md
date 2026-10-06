---
title: "Channel Processing"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Channel Processing

## Genel Bakış

COREMUSIC Channel Processing, çoklu kanal ses formatları arasında dönüşüm sağlar. Mono, stereo, surround ve çoklu kanal formatları için kanal eşleme (mapping), downmix ve upmix işlemleri yapar.

## Teknik Detaylar

### Kanal Haritalama Tablosu

```
┌─────────────────────────────────────────────────────────┐
│              Kanal Haritalama Tablosu                   │
├──────────┬──────────┬──────────┬────────────────────────┤
│ Kanal ID │ İsim     │ Pozisyon │ WoW/Yükseklik          │
├──────────┼──────────┼──────────┼────────────────────────┤
│ 0        │ Left     │ -30°     │ 0°                     │
│ 1        │ Right    │ +30°     │ 0°                     │
│ 2        │ Center   │ 0°       │ 0°                     │
│ 3        │ LFE      │ 0°       │ -90°                   │
│ 4        │ SideL    │ -110°    │ 0°                     │
│ 5        │ SideR    │ +110°    │ 0°                     │
│ 6        │ RearL    │ -150°    │ 0°                     │
│ 7        │ RearR    │ +150°    │ 0°                     │
│ 8        │ TopL     │ -45°     │ +90°                   │
│ 9        │ TopR     │ +45°     │ +90°                   │
└──────────┴──────────┴──────────┴────────────────────────┘
```

### Mono/Stereo Dönüşümü

```cpp
class ChannelConverter {
public:
    // Mono → Stereo
    static void monoToStereo(const float* mono, float* stereo, 
                              uint32_t frames) {
        for (uint32_t i = 0; i < frames; i++) {
            stereo[i * 2] = mono[i];
            stereo[i * 2 + 1] = mono[i];
        }
    }
    
    // Stereo → Mono
    static void stereoToMono(const float* stereo, float* mono,
                              uint32_t frames) {
        for (uint32_t i = 0; i < frames; i++) {
            mono[i] = (stereo[i * 2] + stereo[i * 2 + 1]) * 0.5f;
        }
    }
    
    // Stereo → Mid/Side
    static void stereoToMidSide(const float* stereo, 
                                  float* midSide,
                                  uint32_t frames) {
        for (uint32_t i = 0; i < frames; i++) {
            float left = stereo[i * 2];
            float right = stereo[i * 2 + 1];
            midSide[i * 2] = (left + right) * 0.707f;      // Mid
            midSide[i * 2 + 1] = (left - right) * 0.707f;  // Side
        }
    }
    
    // Mid/Side → Stereo
    static void midSideToStereo(const float* midSide, 
                                  float* stereo,
                                  uint32_t frames) {
        for (uint32_t i = 0; i < frames; i++) {
            float mid = midSide[i * 2];
            float side = midSide[i * 2 + 1];
            stereo[i * 2] = mid + side;      // Left
            stereo[i * 2 + 1] = mid - side;  // Right
        }
    }
};
```

### Kanal Eşleme Matrisi

```cpp
class ChannelMappingMatrix {
public:
    ChannelMappingMatrix(uint32_t inputChannels, 
                         uint32_t outputChannels)
        : inputs(inputChannels), outputs(outputChannels) {
        matrix.resize(outputChannels, 
                     std::vector<float>(inputChannels, 0.0f));
        
        // Varsayılan: identity mapping (mümkünse)
        for (uint32_t i = 0; i < std::min(inputs, outputs); i++) {
            matrix[i][i] = 1.0f;
        }
    }
    
    // Haritalama ayarlama
    void setMapping(uint32_t input, uint32_t output, 
                    float gain = 1.0f) {
        if (input < inputs && output < outputs) {
            matrix[output][input] = gain;
        }
    }
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        // Çıkışları temizle
        for (uint32_t out = 0; out < outputs; out++) {
            std::fill(output[out], output[out] + frameCount, 0.0f);
        }
        
        // Matris çarpımı
        for (uint32_t out = 0; out < outputs; out++) {
            for (uint32_t in = 0; in < inputs; in++) {
                float gain = matrix[out][in];
                if (gain != 0.0f) {
                    for (uint32_t i = 0; i < frameCount; i++) {
                        output[out][i] += input[in][i] * gain;
                    }
                }
            }
        }
    }
    
    // Preset haritalamalar
    static ChannelMappingMatrix createDownmixMatrix(
        uint32_t inputChannels, uint32_t outputChannels) {
        
        ChannelMappingMatrix matrix(inputChannels, outputChannels);
        
        // 5.1 → Stereo örneği
        if (inputChannels == 6 && outputChannels == 2) {
            matrix.setMapping(0, 0, 1.0f);     // L → L
            matrix.setMapping(1, 1, 1.0f);     // R → R
            matrix.setMapping(2, 0, 0.707f);   // C → L (-3dB)
            matrix.setMapping(2, 1, 0.707f);   // C → R (-3dB)
            matrix.setMapping(4, 0, 0.707f);   // LS → L (-3dB)
            matrix.setMapping(5, 1, 0.707f);   // RS → R (-3dB)
            // LFE downmix edilmez
        }
        
        return matrix;
    }
    
private:
    uint32_t inputs;
    uint32_t outputs;
    std::vector<std::vector<float>> matrix;
};
```

### Downmix Implementasyonu

```cpp
class DownmixProcessor {
public:
    DownmixProcessor(uint32_t inputChannels, 
                     uint32_t outputChannels) {
        matrix = ChannelMappingMatrix::createDownmixMatrix(
            inputChannels, outputChannels);
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        matrix.process(input, output, frameCount);
    }
    
    void setInputGain(uint32_t channel, float gainDb) {
        inputGains[channel] = gainDb;
    }
    
    void setOutputGain(float gainDb) {
        outputGain = gainDb;
    }
    
private:
    ChannelMappingMatrix matrix;
    std::map<uint32_t, float> inputGains;
    float outputGain = 0.0f;
};
```

### Upmix Implementasyonu

```cpp
class UpmixProcessor {
public:
    UpmixProcessor(uint32_t inputChannels, 
                   uint32_t outputChannels)
        : inputChannels(inputChannels),
          outputChannels(outputChannels) {
        
        initializeUpmixMatrix();
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        // Temel upmix
        matrix.process(input, output, frameCount);
        
        // Phase shift (surround için)
        if (applyPhaseShift) {
            applySurroundPhase(output, frameCount);
        }
        
        // Bass management
        if (bassManagement) {
            extractBass(input, output, frameCount);
        }
    }
    
    void setPhaseShiftEnabled(bool enable) {
        applyPhaseShift = enable;
    }
    
    void setBassManagementEnabled(bool enable) {
        bassManagement = enable;
    }
    
    void setCrossoverFrequency(float freq) {
        crossoverFreq = freq;
    }
    
private:
    uint32_t inputChannels;
    uint32_t outputChannels;
    ChannelMappingMatrix matrix;
    bool applyPhaseShift = true;
    bool bassManagement = false;
    float crossoverFreq = 80.0f;
    
    void initializeUpmixMatrix() {
        // Stereo → 5.1 upmix
        if (inputChannels == 2 && outputChannels == 6) {
            matrix.setMapping(0, 0, 1.0f);     // L → L
            matrix.setMapping(1, 1, 1.0f);     // R → R
            matrix.setMapping(0, 2, 0.707f);   // L → C
            matrix.setMapping(1, 2, 0.707f);   // R → C
            matrix.setMapping(0, 3, 0.707f);   // L → LFE
            matrix.setMapping(1, 3, 0.707f);   // R → LFE
            matrix.setMapping(0, 4, 0.707f);   // L → LS
            matrix.setMapping(1, 5, 0.707f);   // R → RS
        }
    }
    
    void applySurroundPhase(float** output, uint32_t frameCount) {
        // Surround kanallarına 90° phase shift
        for (uint32_t ch = 4; ch < 6; ch++) {
            for (uint32_t i = 1; i < frameCount; i++) {
                float temp = output[ch][i];
                output[ch][i] = output[ch][i-1];
                output[ch][i-1] = temp;
            }
        }
    }
    
    void extractBass(float** input, float** output, 
                     uint32_t frameCount) {
        // Low-pass L+R → LFE
        for (uint32_t i = 0; i < frameCount; i++) {
            float bass = (input[0][i] + input[1][i]) * 0.5f;
            // Basit low-pass (ilk order)
            lpfState = lpfState * 0.99f + bass * 0.01f;
            output[3][i] += lpfState;
        }
    }
    
    float lpfState = 0.0f;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class ChannelModule {
public:
    ChannelModule(uint32_t inputChannels, uint32_t outputChannels,
                  double sampleRate);
    
    // Dönüşüm modu
    void setConversionMode(ChannelConversionMode mode);
    
    // Downmix/Upmix
    void setDownmixParams(const DownmixParams& params);
    void setUpmixParams(const UpmixParams& params);
    
    // Kanal haritalama
    void setChannelMapping(uint32_t input, uint32_t output, 
                           float gain);
    void clearChannelMapping();
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Bilgi
    uint32_t getInputChannels() const;
    uint32_t getOutputChannels() const;
    ChannelConversionMode getConversionMode() const;
    
private:
    DownmixProcessor downmix;
    UpmixProcessor upmix;
    ChannelMappingMatrix mapping;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.01ms | 0.005ms |
| CPU (5.1→2) | < 0.5% | 0.3% |
| CPU (2→5.1) | < 1% | 0.7% |
| Maks. Kanal | 128 | 128 |
| Bellek | < 2MB | 1.5MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Mono/stereo dönüşümü
- **Faz 2**: Kanal haritalama matrisi
- **Faz 3**: Downmix
- **Faz 4**: Upmix
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
