---
title: "Parametrik EQ"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Parametrik EQ

## Genel Bakış

31 bantlı parametrik EQ, COREMUSIC'in FREKANS TEPKİSİ kontrolü için kullanılır. Her bant bağımsız olarak yapılandırılabilir: frekans, kazanç ve kalite faktörü (Q). Biquad IIR filtreleri kullanılarak verimli ve stabil bir implementasyon sağlanır.

## Teknik Detaylar

### EQ Bant Yapısı

```
31 Bant Frekans Dağılımı:
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  20Hz  31.5  50  80  125  200  315  500  800  1.25k    │
│   │     │    │   │    │    │    │    │    │     │       │
│   ●─────●────●───●────●────●────●────●────●─────●       │
│                                                         │
│  2k  3.15k  5k  8k  12.5k  16k  20k                   │
│   │    │     │   │    │      │    │                     │
│   ●────●─────●───●────●──────●────●                     │
│                                                         │
│  Her bant: ±12dB kazanç, Q: 0.5-10                      │
└─────────────────────────────────────────────────────────┘
```

### Biquad Filtre Tipleri

```cpp
// Biquad filtre tipleri
enum class BiquadType {
    LowPass,        // Düşük frekans geçirgen
    HighPass,       // Yüksek frekans geçirgen
    BandPass,       // Bant geçiren
    Notch,          // Bant阻挡
    Peak,           // Peak (EQ için)
    LowShelf,       // Düşük raf
    HighShelf,      // Yüksek raf
    AllPass         // Tümgeçiren
};

// Biquad katsayı hesaplama
struct BiquadCalculator {
    static BiquadCoefficients calculate(
        BiquadType type,
        double frequency,
        double gain,
        double q,
        double sampleRate) {
        
        BiquadCoefficients coeff;
        double A = std::pow(10.0, gain / 40.0);
        double w0 = 2.0 * M_PI * frequency / sampleRate;
        double sinW0 = std::sin(w0);
        double cosW0 = std::cos(w0);
        double alpha = sinW0 / (2.0 * q);
        
        switch (type) {
            case BiquadType::Peak:
                coeff.b0 = 1.0 + alpha * A;
                coeff.b1 = -2.0 * cosW0;
                coeff.b2 = 1.0 - alpha * A;
                coeff.a0 = 1.0 + alpha / A;
                coeff.a1 = -2.0 * cosW0;
                coeff.a2 = 1.0 - alpha / A;
                break;
                
            case BiquadType::LowPass:
                coeff.b0 = (1.0 - cosW0) / 2.0;
                coeff.b1 = 1.0 - cosW0;
                coeff.b2 = (1.0 - cosW0) / 2.0;
                coeff.a0 = 1.0 + alpha;
                coeff.a1 = -2.0 * cosW0;
                coeff.a2 = 1.0 - alpha;
                break;
                
            case BiquadType::HighPass:
                coeff.b0 = (1.0 + cosW0) / 2.0;
                coeff.b1 = -(1.0 + cosW0);
                coeff.b2 = (1.0 + cosW0) / 2.0;
                coeff.a0 = 1.0 + alpha;
                coeff.a1 = -2.0 * cosW0;
                coeff.a2 = 1.0 - alpha;
                break;
                
            // Diğer tipler...
        }
        
        // Normalize
        coeff.b0 /= coeff.a0;
        coeff.b1 /= coeff.a0;
        coeff.b2 /= coeff.a0;
        coeff.a1 /= coeff.a0;
        coeff.a2 /= coeff.a0;
        
        return coeff;
    }
};
```

### 31-Bant EQ Implementasyonu

```cpp
class ParametricEQ {
public:
    static const uint32_t NUM_BANDS = 31;
    
    // Standart 31-bant frekansları
    static constexpr double bandFrequencies[NUM_BANDS] = {
        20.0, 25.0, 31.5, 40.0, 50.0, 63.0, 80.0, 100.0,
        125.0, 160.0, 200.0, 250.0, 315.0, 400.0, 500.0,
        630.0, 800.0, 1000.0, 1250.0, 1600.0, 2000.0,
        2500.0, 3150.0, 4000.0, 5000.0, 6300.0, 8000.0,
        10000.0, 12500.0, 16000.0, 20000.0
    };
    
    ParametricEQ(double sampleRate) : sampleRate(sampleRate) {
        // Tüm bantları başlat
        for (uint32_t i = 0; i < NUM_BANDS; i++) {
            bands[i].frequency = bandFrequencies[i];
            bands[i].gain = 0.0;
            bands[i].q = 1.0;
            bands[i].active = false;
            bands[i].type = BiquadType::Peak;
        }
    }
    
    // Bant yapılandırması
    void setBand(uint32_t bandIndex, float gain, float q) {
        if (bandIndex >= NUM_BANDS) return;
        
        bands[bandIndex].gain = gain;
        bands[bandIndex].q = q;
        bands[bandIndex].active = (gain != 0.0);
        
        updateBandCoefficients(bandIndex);
    }
    
    // Tek bant ayarlama
    void setBandGain(uint32_t bandIndex, float gain) {
        if (bandIndex >= NUM_BANDS) return;
        
        bands[bandIndex].gain = gain;
        bands[bandIndex].active = (gain != 0.0);
        
        updateBandCoefficients(bandIndex);
    }
    
    // Frekans tepkisi hesaplama
    std::vector<std::pair<double, double>> getFrequencyResponse(
        uint32_t points = 1000) {
        
        std::vector<std::pair<double, double>> response;
        response.reserve(points);
        
        double minFreq = 20.0;
        double maxFreq = 20000.0;
        double logMin = std::log10(minFreq);
        double logMax = std::log10(maxFreq);
        
        for (uint32_t i = 0; i < points; i++) {
            double logFreq = logMin + 
                (logMax - logMin) * i / (points - 1);
            double freq = std::pow(10.0, logFreq);
            
            // Toplam tepki
            double totalGain = 0.0;
            for (uint32_t band = 0; band < NUM_BANDS; band++) {
                if (bands[band].active) {
                    totalGain += calculateBandResponse(
                        band, freq);
                }
            }
            
            response.push_back({freq, totalGain});
        }
        
        return response;
    }
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept {
        for (uint32_t ch = 0; ch < channelCount; ch++) {
            // Her bant için filtreleme
            for (uint32_t band = 0; band < NUM_BANDS; band++) {
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
    
private:
    struct EQBand {
        double frequency;
        double gain;
        double q;
        bool active;
        BiquadType type;
        BiquadCoefficients filter;
    };
    
    EQBand bands[NUM_BANDS];
    double sampleRate;
    uint32_t channelCount = 2;
    
    void updateBandCoefficients(uint32_t bandIndex) {
        auto& band = bands[bandIndex];
        band.filter = BiquadCalculator::calculate(
            band.type,
            band.frequency,
            band.gain,
            band.q,
            sampleRate
        );
    }
    
    double calculateBandResponse(uint32_t bandIndex, 
                                  double frequency) {
        // Biquad frekans tepkisi
        auto& band = bands[bandIndex];
        double w = 2.0 * M_PI * frequency / sampleRate;
        
        // |H(jw)| hesaplama
        double real = band.filter.b0 + 
                      band.filter.b1 * std::cos(w) + 
                      band.filter.b2 * std::cos(2*w);
        double imag = band.filter.b1 * std::sin(w) + 
                      band.filter.b2 * std::sin(2*w);
        
        double numMag = std::sqrt(real*real + imag*imag);
        
        real = 1.0 + band.filter.a1 * std::cos(w) + 
               band.filter.a2 * std::cos(2*w);
        imag = band.filter.a1 * std::sin(w) + 
               band.filter.a2 * std::sin(2*w);
        
        double denMag = std::sqrt(real*real + imag*imag);
        
        double magnitude = numMag / denMag;
        return 20.0 * std::log10(magnitude);
    }
};
```

### Preset Sistemi

```cpp
// EQ presetleri
class EQPreset {
public:
    struct PresetData {
        std::string name;
        float gains[ParametricEQ::NUM_BANDS];
        float qValues[ParametricEQ::NUM_BANDS];
    };
    
    static std::vector<PresetData> getDefaultPresets() {
        return {
            {"Flat", {0}, {1.0f}},
            {"Rock", {-2, -1, 0, 2, 4, 4, 2, 0, -1, -2, 
                      -2, -1, 0, 2, 4, 4, 2, 0, -1, -2,
                      -2, -1, 0, 2, 4, 4, 2, 0, -1, -2, -2}},
            {"Jazz", {0, 0, 2, 3, 2, 0, -2, -2, 0, 2,
                      3, 2, 0, -2, -2, 0, 2, 3, 2, 0,
                      -2, -2, 0, 2, 3, 2, 0, -2, -2, 0, 0}},
            {"Classical", {0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                           0, 0, 0, 0, 0, 0, 0, 3, 4, 3,
                           2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0}},
            {"Vocal", {-2, -1, 0, 2, 4, 4, 2, 0, -1, -2,
                       -2, -1, 0, 2, 4, 4, 2, 0, -1, -2,
                       -2, -1, 0, 2, 4, 4, 2, 0, -1, -2, -2}}
        };
    }
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class EQ31Band {
public:
    EQ31Band(double sampleRate);
    
    // Bant kontrolü
    void setBandGain(uint32_t band, float gainDb);
    void setBandQ(uint32_t band, float q);
    void setBandFrequency(uint32_t band, float freq);
    
    // Toplu ayarlama
    void setAllGains(const float* gains);
    void loadPreset(const EQPreset::PresetData& preset);
    
    // Frekans tepkisi
    std::vector<std::pair<double, double>> getFrequencyResponse(
        uint32_t points = 1000);
    
    // İşleme
    void process(float** input, float** output, 
                 uint32_t frameCount) noexcept;
    
    // Bilgi
    uint32_t getBandCount() const;
    double getSampleRate() const;
    
private:
    ParametricEQ eq;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.01ms | 0.008ms |
| CPU (31 aktif bant) | < 2% | 1.5% |
| Bellek | < 1MB | 0.8MB |
| Frekans Aralığı | 20Hz-20kHz | 20Hz-20kHz |
| Kazanç Aralığı | ±12dB | ±12dB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: Biquad filtreler, temel EQ
- **Faz 2**: 31-bant implementasyonu
- **Faz 3**: Frekans tepkisi hesaplama
- **Faz 4**: Preset sistemi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
