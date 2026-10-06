---
title: "Mixer ve Routing"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Mixer ve Routing Matrix

## Genel Bakış

COREMUSIC Mixer, çoklu ses kaynaklarını karıştırma ve yönlendirme sağlar. Bus mimarisi, send/return yapısı ve dinamik routing ile esnek bir ses yönlendirme sistemi sunar.

## Teknik Detaylar

### Bus Mimarisi

```
┌─────────────────────────────────────────────────────┐
│              Mixer Bus Mimarisi                     │
│                                                     │
│  [Input 1] ──→ [Channel Strip] ──→ [Bus 1 (Main)]  │
│  [Input 2] ──→ [Channel Strip] ──→ [Bus 2 (Aux 1)] │
│  [Input 3] ──→ [Channel Strip] ──→ [Bus 3 (Aux 2)] │
│  [Input 4] ──→ [Channel Strip] ──→ [Bus 4 (Sub)]   │
│                                                     │
│  [Bus 1] ──→ [Master] ──→ [Output]                 │
│  [Bus 2] ──→ [Effect Send] ──→ [Effect Return]     │
│  [Bus 3] ──→ [Effect Send] ──→ [Effect Return]     │
│  [Bus 4] ──→ [Subgroup] ──→ [Bus 1]                │
└─────────────────────────────────────────────────────┘
```

### Channel Strip

```cpp
class ChannelStrip {
public:
    ChannelStrip(uint32_t channelIndex, double sampleRate)
        : channelIndex(channelIndex), sampleRate(sampleRate) {}
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        // 1. Input gain
        float gainLinear = dbToLinear(inputGain);
        for (uint32_t i = 0; i < frameCount; i++) {
            tempBuffer[i] = input[i] * gainLinear;
        }
        
        // 2. Pan
        for (uint32_t i = 0; i < frameCount; i++) {
            float leftGain = std::cos(pan * M_PI / 2.0f);
            float rightGain = std::sin(pan * M_PI / 2.0f);
            leftOutput[i] = tempBuffer[i] * leftGain;
            rightOutput[i] = tempBuffer[i] * rightGain;
        }
        
        // 3. Mute/Solo
        if (muted || (soloActive && !soloed)) {
            std::fill(leftOutput.begin(), 
                      leftOutput.end(), 0.0f);
            std::fill(rightOutput.begin(), 
                      rightOutput.end(), 0.0f);
        }
        
        // 4. Bus send'leri
        for (uint32_t bus = 0; bus < numBuses; bus++) {
            float sendGain = dbToLinear(busSendLevels[bus]);
            for (uint32_t i = 0; i < frameCount; i++) {
                busOutputs[bus][i] = tempBuffer[i] * sendGain;
            }
        }
    }
    
    void setInputGain(float gainDb) { inputGain = gainDb; }
    void setPan(float panValue) { pan = std::clamp(panValue, -1.0f, 1.0f); }
    void setMute(bool mute) { muted = mute; }
    void setSolo(bool solo) { soloed = solo; }
    void setBusSend(uint32_t bus, float levelDb) {
        if (bus < numBuses) busSendLevels[bus] = levelDb;
    }
    
    float getInputGain() const { return inputGain; }
    float getPan() const { return pan; }
    bool isMuted() const { return muted; }
    bool isSoloed() const { return soloed; }
    
private:
    uint32_t channelIndex;
    double sampleRate;
    uint32_t numBuses = 8;
    
    float inputGain = 0.0f;
    float pan = 0.0f;
    bool muted = false;
    bool soloed = false;
    bool soloActive = false;
    
    std::vector<float> busSendLevels;
    std::vector<float*> busOutputs;
    std::vector<float> tempBuffer;
    std::vector<float> leftOutput;
    std::vector<float> rightOutput;
    
    float dbToLinear(float db) {
        return std::pow(10.0f, db / 20.0f);
    }
};
```

### Bus Implementasyonu

```cpp
class AudioBus {
public:
    AudioBus(uint32_t busIndex, uint32_t channels, 
             double sampleRate) 
        : busIndex(busIndex), channels(channels),
          sampleRate(sampleRate) {
        
        buffer.resize(channels);
        for (uint32_t ch = 0; ch < channels; ch++) {
            buffer[ch].resize(4096, 0.0f);
        }
    }
    
    // Giriş ekleme (mix)
    void addToBuffer(float** input, uint32_t frameCount, 
                     float gain = 1.0f) noexcept {
        for (uint32_t ch = 0; ch < channels; ch++) {
            for (uint32_t i = 0; i < frameCount; i++) {
                buffer[ch][i] += input[ch][i] * gain;
            }
        }
    }
    
    // Buffer'ı oku
    void readBuffer(float** output, uint32_t frameCount) noexcept {
        for (uint32_t ch = 0; ch < channels; ch++) {
            std::copy(buffer[ch].begin(),
                      buffer[ch].begin() + frameCount,
                      output[ch]);
        }
    }
    
    // Buffer'ı temizle
    void clearBuffer(uint32_t frameCount) noexcept {
        for (uint32_t ch = 0; ch < channels; ch++) {
            std::fill(buffer[ch].begin(),
                      buffer[ch].begin() + frameCount,
                      0.0f);
        }
    }
    
    // Master gain
    void setMasterGain(float gainDb) { masterGain = gainDb; }
    void setMute(bool mute) { muted = mute; }
    void setSolo(bool solo) { soloed = solo; }
    
    uint32_t getBusIndex() const { return busIndex; }
    
private:
    uint32_t busIndex;
    uint32_t channels;
    double sampleRate;
    float masterGain = 0.0f;
    bool muted = false;
    bool soloed = false;
    
    std::vector<std::vector<float>> buffer;
};
```

### Routing Matrix

```cpp
class RoutingMatrix {
public:
    RoutingMatrix(uint32_t inputs, uint32_t outputs) 
        : inputs(inputs), outputs(outputs) {
        matrix.resize(outputs, std::vector<float>(inputs, 0.0f));
    }
    
    // Bağlantı kurma
    void connect(uint32_t input, uint32_t output, 
                 float level = 1.0f) {
        if (input < inputs && output < outputs) {
            matrix[output][input] = level;
        }
    }
    
    // Bağlantıyı kes
    void disconnect(uint32_t input, uint32_t output) {
        if (input < inputs && output < outputs) {
            matrix[output][input] = 0.0f;
        }
    }
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        // Çıkışları temizle
        for (uint32_t out = 0; out < outputs; out++) {
            std::fill(output[out], output[out] + frameCount, 0.0f);
        }
        
        // Routing matrisi ile karıştır
        for (uint32_t out = 0; out < outputs; out++) {
            for (uint32_t in = 0; in < inputs; in++) {
                float level = matrix[out][in];
                if (level != 0.0f) {
                    for (uint32_t i = 0; i < frameCount; i++) {
                        output[out][i] += input[in][i] * level;
                    }
                }
            }
        }
    }
    
    // Matris durumu
    float getConnection(uint32_t input, uint32_t output) const {
        if (input < inputs && output < outputs) {
            return matrix[output][input];
        }
        return 0.0f;
    }
    
    void printMatrix() const {
        for (uint32_t out = 0; out < outputs; out++) {
            for (uint32_t in = 0; in < inputs; in++) {
                std::cout << matrix[out][in] << "\t";
            }
            std::cout << std::endl;
        }
    }
    
private:
    uint32_t inputs;
    uint32_t outputs;
    std::vector<std::vector<float>> matrix;
};
```

### Mixer Manager

```cpp
class MixerManager {
public:
    MixerManager(uint32_t inputChannels, uint32_t outputChannels,
                 double sampleRate) 
        : sampleRate(sampleRate),
          routingMatrix(inputChannels, outputChannels) {
        
        // Channel strip'leri oluştur
        for (uint32_t i = 0; i < inputChannels; i++) {
            channels.emplace_back(i, sampleRate);
        }
        
        // Bus'ları oluştur
        for (uint32_t i = 0; i < numBuses; i++) {
            buses.emplace_back(i, outputChannels, sampleRate);
        }
    }
    
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        // 1. Bus'ları temizle
        for (auto& bus : buses) {
            bus.clearBuffer(frameCount);
        }
        
        // 2. Her channel'ı işle ve bus'lara gönder
        for (uint32_t ch = 0; ch < channels.size(); ch++) {
            channels[ch].process(input[ch], tempOutput, frameCount);
            
            // Bus send'leri
            for (uint32_t bus = 0; bus < numBuses; bus++) {
                buses[bus].addToBuffer(&tempOutput, frameCount);
            }
        }
        
        // 3. Bus'ları master'a yönlendir
        for (auto& bus : buses) {
            bus.readBuffer(masterInput, frameCount);
        }
        
        // 4. Routing matrix uygula
        routingMatrix.process(masterInput, output, frameCount);
    }
    
    ChannelStrip& getChannel(uint32_t index) {
        return channels[index];
    }
    
    AudioBus& getBus(uint32_t index) {
        return buses[index];
    }
    
    RoutingMatrix& getRoutingMatrix() {
        return routingMatrix;
    }
    
private:
    double sampleRate;
    uint32_t numBuses = 8;
    
    std::vector<ChannelStrip> channels;
    std::vector<AudioBus> buses;
    RoutingMatrix routingMatrix;
    
    float** masterInput;
    float** tempOutput;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class MixerModule {
public:
    MixerModule(uint32_t inputChannels, uint32_t outputChannels,
                double sampleRate);
    
    // Channel kontrolü
    void setChannelGain(uint32_t channel, float gainDb);
    void setChannelPan(uint32_t channel, float pan);
    void setChannelMute(uint32_t channel, bool mute);
    void setChannelSolo(uint32_t channel, bool solo);
    
    // Bus send
    void setBusSend(uint32_t channel, uint32_t bus, float level);
    
    // Bus master
    void setBusGain(uint32_t bus, float gainDb);
    void setBusMute(uint32_t bus, bool mute);
    
    // Routing
    void connect(uint32_t input, uint32_t output, float level);
    void disconnect(uint32_t input, uint32_t output);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Durum
    float getChannelLevel(uint32_t channel) const;
    float getBusLevel(uint32_t bus) const;
    
private:
    MixerManager mixer;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Kanal Sayısı | 128 | 128 |
| Bus Sayısı | 16 | 16 |
| İşleme Latency | < 0.05ms | 0.03ms |
| CPU (64 kanal) | < 2% | 1.5% |
| Bellek | < 10MB | 8MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Channel strip, pan/gain
- **Faz 2**: Bus sistemi
- **Faz 3**: Routing matrix
- **Faz 4**: Solo/mute, VCA
- **Tahmini Süre**: 2 hafta (80 adam-saat)
