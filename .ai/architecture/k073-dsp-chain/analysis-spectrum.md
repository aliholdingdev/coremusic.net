---
title: "K073 Analiz — FFT Analizi, Spectrum Analyzer ve Waterfall Display"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K073 — Analiz: FFT / Spectrum Analyzer / Frequency Response

> **K numarası:** K073 · **Klasör:** `k073-dsp-chain` · **Dosya:** `analysis-spectrum`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (FFT maliyeti), `qa-engineer` (ölçüm doğruluğu)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (analiz için ayrı ADR numarası kaynakta yok; katman ADR'leri `ADR-025`, `ADR-062`)

## 1. Genel Bakış

Analiz dalı, DSP zincirinin **paralel** olarak beslediği ölçüm/akış bileşenleridir: FFT tabanlı
spectrum analyzer, frequency response ölçümü ve waterfall (su düşüşü) display. Kaynak
`analysis-spectrum.md` bunları dört başlıkta toplar: FFT Algoritması, Spectrum Analyzer, Frequency
Response, Waterfall Display.

Analiz, ses yolunun (audio path) bir aşaması değildir: ses yolu 15 aşamayı izler (`[[dsp-chain]]` §3),
analiz ise bu yolun bir **tap** (yan kolu) noktasından okuma yapar. Bu ayrım, analiz maliyetinin
RT latency'yi doğrudan artırmaması için kritiktir; ancak kaynağın analiz tap'ının öncelik/Thread
ayrıntısını vermediğini belirtmek gerekir → `⚠️ VERIFICATION REQUIRED`.

Kaynak `README.md` §8 Analyzer bölümü spectrum analyzer'ı ayrıca tanımlar ve `README.md` §Alt Katman
Şeması K3.5 "Analiz & Çalma" başlığında analiz + çalma birlikte anılır (27 kanıtlı yaprak).

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Kardeş (zincir) | `[[dsp-chain]]` |
| Klasör dizini | `[[index]]` |
| Çalma zamanlaması | `[[../k078-playback-gapless/playback-gapless]]` |
| Sinyal kaynağı (post-EQ vb.) | `[[../k074-eq-parametric/eq-parametric]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | FFT algoritması | Pencereleme, bin ayrımı, büyüklük hesabı | `analysis-spectrum.md` §FFT Algoritması |
| 2 | Spectrum analyzer | Gerçek zamanlı genlik akışı | §Spectrum Analyzer |
| 3 | Frequency response | Sistem/filtre tepkisi ölçümü | §Frequency Response |
| 4 | Waterfall display | Zaman-frekans görünümü | §Waterfall Display |
| 5 | API / arayüz | Analiz çıktısının tüketilmesi | §API / Arayüz |
| 6 | Performans metrikleri | FFT maliyeti ve gecikme | §Performans Metrikleri |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | EQ/efekt katsayıları | `[[../k074-eq-parametric/eq-parametric]]` |
| 2 | 15 aşamanın yürütülmesi | `[[dsp-chain]]` |
| 3 | Parça geçişi/crossfade | `[[../k078-playback-gapless/playback-gapless]]` |
| 4 | UI render/iş parçacığı | Uygulama katmanı (kapsam dışı) |
| 5 | Otomatik EQ kapalı döngüsü | D11/AI domaini (kapsam dışı) |

## 3. Sinyal Akışı

```
        +------------------ k073-dsp-chain ------------------------+
        |                                                          |
kare -->|-- [5 EQ] -- [6 Dynamics] -- ... -- [12 Limiter] -- cikis |
        |       |                                                   |
        |       +--> TAP (ara nokta) --> pencereleme --> FFT --> bins
        |                                                        |
        +--------------------------------------------------------+
                 |                       |                      |
          spectrum (dB)         frequency response        waterfall (t x f)
                 |                       |                      |
                 +----------- telemetri/UI (RT-disi) ------------+
```

| # | Yön | Veri | Not |
|---|---|---|---|
| 1 | Zincir → tap | Kare örneği | Tap noktası ⚠️ VERIFICATION REQUIRED |
| 2 | Tap → pencere | Sabit uzunluk blok | Pencere tipi ⚠️ VERIFICATION REQUIRED |
| 3 | Pencere → FFT | Karmaşık/gerçek spektrum | Kaynakta FFT algoritması tanımlı |
| 4 | FFT → bin | Genlik (dB) | Logaritmik gösterim |
| 5 | Bin → display | Spectrum / waterfall | RT-dışı çizim |
| 6 | Sistem → frequency response | Uyarım-cevap | Ölçüm modu |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `analysis-spectrum.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/analysis-spectrum.md` (417 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### FFT Analizi ve Spectrum Analyzer

##### Genel Bakış

COREMUSIC, FFT (Fast Fourier Transform) tabanlı spektrum analizi ile gerçek zamanlı frekans analizi sağlar. Frequency response grafikleri, spectrum analyzer ve phase analizi için kullanılır.

##### Teknik Detaylar

###### FFT Algoritması

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

###### Spectrum Analyzer

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

###### Frequency Response

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

###### Waterfall Display

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

##### API / Arayüz

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

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| FFT Boyutu | 4096-16384 | 8192 |
| İşleme Süresi | < 1ms | 0.8ms |
| CPU | < 2% | 1.5% |
| Bellek | < 5MB | 3.2MB |
| Frekans Çözünürlüğü | < 5Hz @ 96kHz | 2.3Hz |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

##### Durum: Implementasyon

- **Faz 1**: FFT algoritması
- **Faz 2**: Spectrum analyzer
- **Faz 3**: Frequency response
- **Faz 4**: Waterfall display
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)



## 5. İşleme Aşamaları

| # | Aşama | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Tap seçimi | Zincir noktaları | Tek tap noktası | Varsayılan noktaya düş |
| 2 | Blok tamponlama | Örnekler | N-örnekli blok | Kısmi blok reddi |
| 3 | Pencereleme | Blok | Pencereleme katsayıları | Dikdörtgen pencere |
| 4 | FFT | Pencere + blok | Spektrum | Hata → son değer korunur |
| 5 | Genlik/bins | Spektrum | dB dizisi | — |
| 6 | Smoothing | dB dizisi | Yumuşak akış | Devre dışı |
| 7 | Spectrum render | dB dizisi | Ekran | Son kare tekrarı |
| 8 | Waterfall update | Yeni satır | Zaman-frekans haritası | Satır atlanır |
| 9 | Frequency response | Uyarım/cevap | Tepki eğrisi | Ölçüm iptali |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | DSP zinciri | yukarı (tap kaynağı) | `[[dsp-chain]]` |
| 2 | Klasör dizini | yukarı | `[[index]]` |
| 3 | Motor çekirdeği (RT thread) | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 4 | EQ (post-EQ tap tercihi) | yandan | `[[../k074-eq-parametric/eq-parametric]]` |
| 5 | Dynamics (yan etki ölçümü) | yandan | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 6 | Reverb (yankı analizi) | yandan | `[[../k083-effects-reverb/effects-reverb]]` |
| 7 | Gapless (parça değişiminde analiz durumu) | yandan | `[[../k078-playback-gapless/playback-gapless]]` |
| 8 | Mixer (bus tap) | yandan | `[[../k076-mixer-routing/mixer-routing]]` |
| 9 | Stream buffer (underrun göstergesi) | yandan | `[[../k077-stream-buffer/stream-buffer]]` |
| 10 | Channel processing (kanal başına analiz) | yandan | `[[../k080-channel-processing/channel-processing]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Girdi sessizliği (mute) | 0 dB altı, ekran log eşiğinde | Logaritmik gösterim |
| 2 | Nyquist'e yakın bin | Son bin kırpılır/alias | Pencereleme etkisi |
| 3 | Blok taşması (yavaş çizim) | Frame düşürme | Audio yolu etkilenmemeli |
| 4 | Parça geçişi | Analiz tap akışı kesintisiz kalır | `[[../k078-playback-gapless/playback-gapless]]` |
| 5 | Örnekleme hızı değişimi | FFT boyutu/ bin aralığı yeniden kurulur | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 6 | 128 kanal | Kanal başına analiz maliyeti ölçeklenir | Hedef `k3 index.md` |
| 7 | Yanlış tap noktası (pre-gain) | Yanlış seviye okuması | Tap noktası ⚠️ |
| 8 | Denormal girdi | CPU şişmesi | Flush-to-zero |
| 9 | Ölçüm modu aktifken çalma | Frequency response ölçümü çakışır | Kilitlenme politikası ⚠️ |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Spektrum kaymış görünür | Pencere uygulanmadı/yanlış bin | Yanlış yorum | Pencere + bin doğrulama |
| 2 | Tepe takılıyor (alias) | Örnekleme hızı uyuşmazlığı | Bozuk ölçüm | `[[../k075-sample-rate-conversion/index]]` kontrolü |
| 3 | Waterfall eksik satır | Frame drop | Görsel boşluk | Buffer boyutu |
| 4 | Ekran gecikmesi | Smoothing fazlası | UI gecikmesi | Smoothing ayarı |
| 5 | CPU sıçraması | FFT boyutu çok büyük | CPU > %10 hedefi | Blok boyutu/fft ayarı ⚠️ |
| 6 | RT latency artışı | Analiz RT thread'de | Glitch riski | RT-dışı öncelik (kanıt ⚠️) |
| 7 | Yanlış dB ölçeği | Tepe/ortalam hesap hatası | Yanlış okuma | Ölçüm testi |
| 8 | Frekans ekseni kayması | Örnekleme hızı değişimi | Yanlış etiket | Yeniden kalibrasyon |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak | Not |
|---|---|---|---|---|
| 1 | Zincir latency | < 0.1 ms | `k3 index.md` | Analiz RT yoluna dahil edilmezse |
| 2 | CPU | < %10 | `k3 index.md` | Analiz dâhil |
| 3 | Kanal | 128 | `k3 index.md` | Kapasite |
| 4 | Örnekleme | 384 kHz | `k3 index.md` | Kapasite |
| 5 | Bellek | < 100 MB | `k3 index.md` | Analiz tamponları dâhil |
| 6 | K3.5 yaprak sayısı | 27 | `README.md` §Alt Katman Şeması | Analiz + çalma |
| 7 | Spectrum analyzer açıklaması | `README.md` §8.1 | Kaynak | Metin |
| 8 | FFT boyutu / pencere tipi | **UNKNOWN** | — | ⚠️ VERIFICATION REQUIRED |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedef (ölçüm yok); 8 tamamen kanıtsızdır — sayı uydurulmaz.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | FFT tamponu RT'de tahsis edilemez (önceden ayrılmalı) | `CLAUDE.md` §1-1 türevi |
| 2 | Analiz kuyruğunda mutex yok | `CLAUDE.md` §1-2 |
| 3 | Callback/analiz kesme noktası `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Analiz state'i `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Pencere katsayıları `constexpr`/statik | `CLAUDE.md` §1-5 |
| 6 | Log yazımı RT'de yasak | `k3 index.md` §Temel İlkeller (sistem çağrısı yasak) |
| 7 | Telemetri sayaçları atomik | Lock-free ilke |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Sinüs girdisi → tepe bin | Unit | Doğru bin ±1 |
| 2 | Bilinen genlik → dB okuması | Unit | ± tolerans (değer ⚠️) |
| 3 | Pencere etkisi (leakage) kontrolü | Unit | Sızıntı sınırlı |
| 4 | Frequency response eğrisi | Integration | Düz/istağe bağlı eğri |
| 5 | Waterfall kararlılığı | UI test | Zaman ekseni monoton |
| 6 | Frame drop toleransı | Stress | Audio yolu etkilenmez |
| 7 | CPU ölçümü (analiz açık/kapalı) | Perf | Fark ≤ bütçe ⚠️ ölçüm |
| 8 | 128 kanal analiz | Integration | Kabul |
| 9 | 384 kHz analiz | Integration | Kabul |
| 10 | Örnekleme hızı değişimi | Integration | Eksen yeniden kalibre |
| 11 | Parça geçişinde analiz | Integration | Kesintisiz |
| 12 | Denormal girdi | Unit | CPU sabit |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | FFT boyutu/pencere tipi kanıtsız | Yüksek | ⚠️ VERIFICATION REQUIRED — sayı yok |
| 2 | Tap noktası belirsiz | Orta | Ölçüm yanlışlığı |
| 3 | Analiz önceliği kanıtsız | Orta | RT riski |
| 4 | Ölçüm kanıtı yok | Yüksek | §9 hedef olarak kaldı |
| 5 | `README.md` §8 ile `analysis-spectrum.md` kapsam farkı | Düşük | İkisi de aynı kaynağı kullanır |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | FFT/spectrum/frequency response/wallfall başlıkları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/analysis-spectrum.md` | Kanıtlı |
| 2 | §4 verbatim (423 satır) | Aynı dosya | Kanıtlı |
| 3 | Spectrum analyzer tanımı | `_backup/.../k3-ses-motoru/README.md` §8.1 | Kanıtlı |
| 4 | K3.5 Analiz & Çalma (27 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 5 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` §Performans Metrikleri | Kanıtlı (hedef) |
| 6 | RT kuralları | `_backup/.../k3-ses-motoru/CLAUDE.md` §1 | Kanıtlı |
| 7 | FFT boyutu, pencere tipi, tap noktası, öncelik | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/analysis-spectrum.md`
(salt-okunur, 423 satır) + `README.md` §8, §Alt Katman Şeması + `index.md` §Performans Metrikleri.
