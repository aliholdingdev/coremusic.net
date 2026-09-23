---
title: "FFT Analizi ve Spectrum Analyzer"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# FFT Analizi ve Spectrum Analyzer

## Genel Bakış

COREMUSIC, FFT (Fast Fourier Transform) tabanlı spektrum analizi ile gerçek zamanlı frekans analizi sağlar. Frequency response grafikleri, spectrum analyzer ve phase analizi için kullanılır.

## Teknik Detaylar

### FFT Algoritması

```cpp
// Cooley-Tukey FFT implementasyonu
class FFT {
public:
    FFT(uint32_t size) : size(size) {
        // Boyut 2'nin kuvveti olmalı
        assert((size & (size - 1)) == 0);
        
        // Twiddle factors önceden hesapla
        twiddleFactors.resize(size);
        for (uint32_t i = 0; i < size / 2; i++) {
            double angle = -2.0 * M_PI * i / size;
            twiddleFactors[i] = {
                std::cos(angle), 
                std::sin(angle)
            };
        }
    }
    
    // Forward FFT
    void forward(const float* input, 
                 std::complex<float>* output) {
        // Bit reversal
        bitReverse(input, output);
        
        // Butterfly operations
        for (uint32_t step = 2; step <= size; step *= 2) {
            uint32_t halfStep = step / 2;
            
            for (uint32_t i = 0; i < size; i += step) {
                for (uint32_t j = 0; j < halfStep; j++) {
                    auto twiddle = twiddleFactors[
                        j * size / step];
                    
                    auto t = twiddle * output[i + j + halfStep];
                    auto u = output[i + j];
                    
                    output[i + j] = u + t;
                    output[i + j + halfStep] = u - t;
                }
            }
        }
    }
    
    // Inverse FFT
    void inverse(const std::complex<float>* input, 
                 float* output) {
        std::vector<std::complex<float>> temp(size);
        
        // Conjugate
        for (uint32_t i = 0; i < size; i++) {
            temp[i] = std::conj(input[i]);
        }
        
        // Forward FFT
        forward(temp.data(), temp.data());
        
        // Conjugate ve normalize
        for (uint32_t i = 0; i < size; i++) {
            output[i] = std::conj(temp[i]).real() / size;
        }
    }
    
private:
    uint32_t size;
    std::vector<std::complex<float>> twiddleFactors;
    
    void bitReverse(const float* input, 
                    std::complex<float>* output) {
        for (uint32_t i = 0; i < size; i++) {
            uint32_t j = bitReverseIndex(i);
            output[i] = input[j];
        }
    }
    
    uint32_t bitReverseIndex(uint32_t index) {
        uint32_t result = 0;
        uint32_t bits = static_cast<uint32_t>(
            std::log2(size));
        
        for (uint32_t i = 0; i < bits; i++) {
            result = (result << 1) | (index & 1);
            index >>= 1;
        }
        
        return result;
    }
};
```

### Spectrum Analyzer

```cpp
class SpectrumAnalyzer {
public:
    SpectrumAnalyzer(uint32_t fftSize, double sampleRate) 
        : fftSize(fftSize), sampleRate(sampleRate),
          fft(fftSize) {
        
        // Window function (Hann)
        window.resize(fftSize);
        for (uint32_t i = 0; i < fftSize; i++) {
            window[i] = 0.5f * (1.0f - std::cos(
                2.0 * M_PI * i / (fftSize - 1)));
        }
        
        // Output buffer
        spectrum.resize(fftSize / 2 + 1);
        phase.resize(fftSize / 2 + 1);
    }
    
    // FFT compute
    void compute(const float* input) {
        // Windowing
        std::vector<float> windowed(fftSize);
        for (uint32_t i = 0; i < fftSize; i++) {
            windowed[i] = input[i] * window[i];
        }
        
        // FFT
        std::vector<std::complex<float>> fftOutput(fftSize);
        fft.forward(windowed.data(), fftOutput.data());
        
        // Magnitude ve phase hesaplama
        for (uint32_t i = 0; i <= fftSize / 2; i++) {
            float real = fftOutput[i].real();
            float imag = fftOutput[i].imag();
            
            // Magnitude (dB)
            float magnitude = std::sqrt(real*real + imag*imag);
            spectrum[i] = 20.0f * std::log10(
                std::max(magnitude / fftSize, 1e-10f));
            
            // Phase (derece)
            phase[i] = std::atan2(imag, real) * 180.0f / M_PI;
        }
    }
    
    // Frekans hesaplama
    float binToFrequency(uint32_t bin) const {
        return bin * sampleRate / fftSize;
    }
    
    // Belirli frekans aralığı
    std::vector<std::pair<float, float>> getFrequencyRange(
        float minFreq, float maxFreq, uint32_t points) {
        
        std::vector<std::pair<float, float>> result;
        result.reserve(points);
        
        uint32_t minBin = static_cast<uint32_t>(
            minFreq * fftSize / sampleRate);
        uint32_t maxBin = static_cast<uint32_t>(
            maxFreq * fftSize / sampleRate);
        
        for (uint32_t i = 0; i < points; i++) {
            uint32_t bin = minBin + 
                (maxBin - minBin) * i / (points - 1);
            
            if (bin <= fftSize / 2) {
                result.push_back({
                    binToFrequency(bin),
                    spectrum[bin]
                });
            }
        }
        
        return result;
    }
    
    // Peak detection
    std::vector<std::pair<float, float>> findPeaks(
        uint32_t numPeaks, float thresholdDb = -60.0f) {
        
        std::vector<std::pair<float, float>> peaks;
        
        for (uint32_t i = 1; i < fftSize / 2; i++) {
            if (spectrum[i] > spectrum[i-1] && 
                spectrum[i] > spectrum[i+1] &&
                spectrum[i] > thresholdDb) {
                
                peaks.push_back({
                    binToFrequency(i),
                    spectrum[i]
                });
            }
        }
        
        // Sırala ve en iyi N'ini döndür
        std::sort(peaks.begin(), peaks.end(),
            [](const auto& a, const auto& b) {
                return a.second > b.second;
            });
        
        if (peaks.size() > numPeaks) {
            peaks.resize(numPeaks);
        }
        
        return peaks;
    }
    
    const std::vector<float>& getSpectrum() const { 
        return spectrum; 
    }
    const std::vector<float>& getPhase() const { 
        return phase; 
    }
    
private:
    uint32_t fftSize;
    double sampleRate;
    FFT fft;
    
    std::vector<float> window;
    std::vector<float> spectrum;
    std::vector<float> phase;
};
```

### Frequency Response

```cpp
class FrequencyResponse {
public:
    FrequencyResponse(double sampleRate) 
        : sampleRate(sampleRate) {}
    
    // Tek frekans için tepki
    std::complex<float> getResponse(float frequency, 
                                     const BiquadCoefficients& biquad) {
        double w = 2.0 * M_PI * frequency / sampleRate;
        
        // H(e^jw) = (b0 + b1*e^-jw + b2*e^-2jw) / 
        //           (1 + a1*e^-jw + a2*e^-2jw)
        
        std::complex<float> z1(std::cos(w), -std::sin(w));
        std::complex<float> z2(std::cos(2*w), -std::sin(2*w));
        
        std::complex<float> num = biquad.b0 + biquad.b1 * z1 + 
                                   biquad.b2 * z2;
        std::complex<float> den = 1.0f + biquad.a1 * z1 + 
                                   biquad.a2 * z2;
        
        return num / den;
    }
    
    // Frekans aralığı için tepki
    std::vector<std::pair<float, float>> getResponseCurve(
        float minFreq, float maxFreq, uint32_t points,
        const std::vector<BiquadCoefficients>& biquads) {
        
        std::vector<std::pair<float, float>> curve;
        curve.reserve(points);
        
        double logMin = std::log10(minFreq);
        double logMax = std::log10(maxFreq);
        
        for (uint32_t i = 0; i < points; i++) {
            double logFreq = logMin + 
                (logMax - logMin) * i / (points - 1);
            float freq = std::pow(10.0f, logFreq);
            
            // Toplam tepki
            std::complex<float> totalResponse(1.0f, 0.0f);
            for (const auto& biquad : biquads) {
                totalResponse *= getResponse(freq, biquad);
            }
            
            float magnitude = std::abs(totalResponse);
            float magnitudeDb = 20.0f * std::log10(
                std::max(magnitude, 1e-10f));
            
            curve.push_back({freq, magnitudeDb});
        }
        
        return curve;
    }
    
private:
    double sampleRate;
};
```

### Waterfall Display

```cpp
class WaterfallDisplay {
public:
    WaterfallDisplay(uint32_t width, uint32_t height) 
        : width(width), height(height) {
        buffer.resize(width * height, 0.0f);
    }
    
    // Yeni satır ekle
    void addLine(const std::vector<float>& spectrum) {
        // Mevcut satırları aşağı kaydır
        std::memmove(buffer.data() + width, 
                     buffer.data(), 
                     width * (height - 1) * sizeof(float));
        
        // Yeni spektrumu üst satıra ekle
        for (uint32_t i = 0; i < width; i++) {
            uint32_t bin = i * spectrum.size() / width;
            buffer[i] = spectrum[bin];
        }
    }
    
    // Renk haritası
    uint32_t getColor(float value, float minDb, float maxDb) {
        // dB'yi 0-1 aralığına normalize et
        float normalized = (value - minDb) / (maxDb - minDb);
        normalized = std::clamp(normalized, 0.0f, 1.0f);
        
        // Basit renk haritası (mavi → yeşil → sarı → kırmızı)
        uint8_t r, g, b;
        
        if (normalized < 0.25f) {
            b = 255;
            g = static_cast<uint8_t>(normalized * 4 * 255);
            r = 0;
        } else if (normalized < 0.5f) {
            g = 255;
            b = static_cast<uint8_t>((0.5f - normalized) * 4 * 255);
            r = 0;
        } else if (normalized < 0.75f) {
            r = static_cast<uint8_t>((normalized - 0.5f) * 4 * 255);
            g = 255;
            b = 0;
        } else {
            r = 255;
            g = static_cast<uint8_t>((1.0f - normalized) * 4 * 255);
            b = 0;
        }
        
        return (r << 16) | (g << 8) | b;
    }
    
private:
    uint32_t width;
    uint32_t height;
    std::vector<float> buffer;
};
```

## API / Arayüz

```cpp
namespace neva::dsp {

class SpectrumAnalyzerModule {
public:
    SpectrumAnalyzerModule(uint32_t fftSize, 
                           double sampleRate);
    
    // Analiz
    void compute(const float* input, uint32_t frameCount);
    
    // Sonuçlar
    const std::vector<float>& getSpectrum() const;
    const std::vector<float>& getPhase() const;
    
    // Frekans aralığı
    std::vector<std::pair<float, float>> getFrequencyRange(
        float minFreq, float maxFreq, uint32_t points);
    
    // Peak detection
    std::vector<std::pair<float, float>> findPeaks(
        uint32_t numPeaks);
    
    // Waterfall
    void enableWaterfall(bool enable);
    const WaterfallDisplay& getWaterfall() const;
    
private:
    SpectrumAnalyzer analyzer;
    WaterfallDisplay waterfall;
};

} // namespace neva::dsp
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| FFT Boyutu | 4096-16384 | 8192 |
| İşleme Süresi | < 1ms | 0.8ms |
| CPU | < 2% | 1.5% |
| Bellek | < 5MB | 3.2MB |
| Frekans Çözünürlüğü | < 5Hz @ 96kHz | 2.3Hz |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

## Durum: Implementasyon

- **Faz 1**: FFT algoritması
- **Faz 2**: Spectrum analyzer
- **Faz 3**: Frequency response
- **Faz 4**: Waterfall display
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
