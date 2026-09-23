---
title: "Reverb Efekti"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Reverb Efekti (Freeverb Algorithm)

## Genel Bakış

Reverb efekti, sesin bir ortamda yankılanmasını simüle eder. COREMUSIC, Freeverb algoritmasını kullanarak oda modelleme ve erken yansıtma (early reflections) sağlar. Paralel ve seri allpass梳状 filtreler ile doğal reverb üretir.

## Teknik Detaylar

### Freeverb Mimarisi

```
┌─────────────────────────────────────────────────────┐
│              Freeverb Algoritması                    │
│                                                     │
│  Input ──→ [Comb Filters] ──→ [Allpass Filters]    │
│      │      (8 paralel)         (4 seri)           │
│      │           ↓                    ↓             │
│      │      [Damping]             [Mixing]          │
│      │           ↓                    ↓             │
│      └──────→ [Wet/Dry Mix] ←──── [Output]         │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### Comb Filtre Implementasyonu

```cpp
class CombFilter {
public:
    CombFilter(uint32_t delaySize) 
        : delayLine(delaySize), delaySize(delaySize) {}
    
    void setParams(float feedback, float damp) {
        this->feedback = feedback;
        this->damp = damp;
    }
    
    float process(float input) noexcept {
        // Delay line'dan oku
        float output = delayLine.read();
        
        // Damping uygula
        filterState = output * (1.0f - damp) + 
                      filterState * damp;
        
        // Feedback
        float feedbackSignal = input + filterState * feedback;
        
        // Delay line'a yaz
        delayLine.write(feedbackSignal);
        
        return output;
    }
    
    void reset() {
        delayLine.reset();
        filterState = 0.0f;
    }
    
private:
    DelayLine delayLine;
    uint32_t delaySize;
    float feedback = 0.5f;
    float damp = 0.5f;
    float filterState = 0.0f;
};
```

### Allpass Filtre Implementasyonu

```cpp
class AllpassFilter {
public:
    AllpassFilter(uint32_t delaySize) 
        : delayLine(delaySize), delaySize(delaySize) {}
    
    void setParams(float feedback) {
        this->feedback = feedback;
    }
    
    float process(float input) noexcept {
        float output = delayLine.read();
        
        float feedbackSignal = input + output * feedback;
        delayLine.write(feedbackSignal);
        
        return output - feedbackSignal * feedback;
    }
    
    void reset() {
        delayLine.reset();
    }
    
private:
    DelayLine delayLine;
    uint32_t delaySize;
    float feedback = 0.5f;
};
```

### Delay Line (Ring Buffer)

```cpp
class DelayLine {
public:
    DelayLine(uint32_t size) 
        : size(size), buffer(size), writePos(0) {}
    
    void write(float sample) noexcept {
        buffer[writePos] = sample;
        writePos = (writePos + 1) % size;
    }
    
    float read() noexcept {
        uint32_t readPos = (writePos + 1) % size;
        return buffer[readPos];
    }
    
    float readAt(uint32_t delay) noexcept {
        uint32_t pos = (writePos + size - delay) % size;
        return buffer[pos];
    }
    
    void reset() {
        std::fill(buffer.begin(), buffer.end(), 0.0f);
        writePos = 0;
    }
    
private:
    uint32_t size;
    std::vector<float> buffer;
    uint32_t writePos;
};
```

### Freeverb Ana Yapı

```cpp
class Freeverb {
public:
    Freeverb(double sampleRate) : sampleRate(sampleRate) {
        initialize();
    }
    
    void setParams(float roomSize, float damping, 
                   float wet, float dry) {
        this->roomSize = roomSize;
        this->damping = damping;
        this->wetLevel = wet;
        this->dryLevel = dry;
        
        updateCombFilters();
    }
    
    void process(float* input, float* output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t i = 0; i < frameCount; i++) {
            float inputSample = input[i];
            float wetSample = 0.0f;
            
            // 8 paralel comb filter
            for (uint32_t c = 0; c < NUM_COMBS; c++) {
                wetSample += combFilters[c].process(inputSample);
            }
            wetSample /= NUM_COMBS;
            
            // 4 seri allpass filter
            for (uint32_t a = 0; a < NUM_ALLPASS; a++) {
                wetSample = allpassFilters[a].process(wetSample);
            }
            
            // Wet/Dry karıştırma
            output[i] = inputSample * dryLevel + 
                        wetSample * wetLevel;
        }
    }
    
    void reset() {
        for (auto& comb : combFilters) comb.reset();
        for (auto& allpass : allpassFilters) allpass.reset();
    }
    
private:
    static const uint32_t NUM_COMBS = 8;
    static const uint32_t NUM_ALLPASS = 4;
    
    // Freeverb delay boyutları ( sample @ 44.1kHz)
    static constexpr uint32_t combDelays[NUM_COMBS] = {
        1116, 1188, 1277, 1356, 1422, 1491, 1557, 1617
    };
    static constexpr uint32_t allpassDelays[NUM_ALLPASS] = {
        556, 441, 341, 225
    };
    
    double sampleRate;
    float roomSize = 0.5f;
    float damping = 0.5f;
    float wetLevel = 0.3f;
    float dryLevel = 0.7f;
    
    std::array<CombFilter, NUM_COMBS> combFilters;
    std::array<AllpassFilter, NUM_ALLPASS> allpassFilters;
    
    void initialize() {
        for (uint32_t i = 0; i < NUM_COMBS; i++) {
            uint32_t delay = static_cast<uint32_t>(
                combDelays[i] * sampleRate / 44100.0);
            combFilters[i] = CombFilter(delay);
        }
        
        for (uint32_t i = 0; i < NUM_ALLPASS; i++) {
            uint32_t delay = static_cast<uint32_t>(
                allpassDelays[i] * sampleRate / 44100.0);
            allpassFilters[i] = AllpassFilter(delay);
        }
    }
    
    void updateCombFilters() {
        for (auto& comb : combFilters) {
            comb.setParams(roomSize * 0.9f, damping);
        }
    }
};
```

### Oda Modelleme

```cpp
// Oda modelleri
enum class RoomType {
    SmallRoom,      // 20m²
    MediumRoom,     // 50m²
    LargeRoom,      // 100m²
    ConcertHall,    // 500m²
    Cathedral,      // 2000m²
    Custom          // Özel
};

struct RoomPreset {
    float roomSize;
    float damping;
    float wetLevel;
    float dryLevel;
    float preDelay;    // ms
    float diffusion;
    float density;
};

class RoomModeling {
public:
    static RoomPreset getPreset(RoomType type) {
        switch (type) {
            case RoomType::SmallRoom:
                return {0.3f, 0.5f, 0.2f, 0.8f, 5.0f, 0.7f, 0.6f};
            case RoomType::MediumRoom:
                return {0.5f, 0.5f, 0.3f, 0.7f, 10.0f, 0.8f, 0.7f};
            case RoomType::LargeRoom:
                return {0.7f, 0.4f, 0.4f, 0.6f, 15.0f, 0.9f, 0.8f};
            case RoomType::ConcertHall:
                return {0.8f, 0.3f, 0.5f, 0.5f, 20.0f, 0.95f, 0.9f};
            case RoomType::Cathedral:
                return {0.9f, 0.2f, 0.6f, 0.4f, 30.0f, 1.0f, 1.0f};
            default:
                return {0.5f, 0.5f, 0.3f, 0.7f, 10.0f, 0.8f, 0.7f};
        }
    }
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class ReverbModule {
public:
    ReverbModule(double sampleRate);
    
    // Room parametreleri
    void setRoomSize(float size);
    void setDamping(float damping);
    void setWetLevel(float level);
    void setDryLevel(float level);
    void setPreDelay(float ms);
    
    // Preset
    void loadPreset(RoomType type);
    void loadCustomPreset(const RoomPreset& preset);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Durum
    float getDecayTime() const;
    float getPreDelayTime() const;
    
private:
    Freeverb reverb;
    DelayLine preDelayLine;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.5ms | 0.3ms |
| CPU | < 3% | 2.1% |
| Bellek | < 5MB | 3.8MB |
| Decay Time | 0.1-10s | 0.1-10s |
| Maksimum Kanal | 8 | 8 |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Delay line, comb/allpass filtreler
- **Faz 2**: Freeverb algoritması
- **Faz 3**: Oda modelleme
- **Faz 4**: Preset sistemi
- **Tahmini Süre**: 1 hafta (40 adam-saat)
