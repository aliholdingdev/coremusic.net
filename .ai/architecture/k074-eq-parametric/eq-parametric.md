---
title: "K074 Parametrik EQ — 31 Bantlı Biquad IIR Zinciri"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K074 — Parametrik EQ (31 Bant / Biquad / IIR)

> **K numarası:** K074 · **Klasör:** `k074-eq-parametric` · **Dosya:** `eq-parametric`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (CPU bütçesi), `qa-engineer` (tepki eğrisi)
> **İlgili ADR'ler:** `ADR-025` (31-band parametrik EQ) — kaynak: `_backup/.../k3-ses-motoru/README.md` §10 · `CLAUDE.md` §5

## 1. Genel Bakış

Parametrik EQ, DSP zincirinin 5. ve 11. (master) aşamalarında çalışan, **31 bantlı** bir frekans
şekillendirme bloğudur. Kaynak `eq-parametric.md` üç yapı taşı tanımlar: **EQ bant yapısı**
(frekans, Q, kazanç), **biquad filtre tipleri** (peaking, shelf, cutoff vb.) ve **31-bant EQ
implementasyonu**; ayrıca **preset sistemi** ile önceden tanımlı katsayı setleri desteklenir.

`README.md` §3 EQ band frekanslarını (§3.1) ve biquad katsayı tablosunu (§3.2) ayrıntılandırır;
`README.md` §Alt Katman Şeması K3.3 ise bu konuda 9 kanıtlı yaprak sayar. EQ'nun zincirdeki yeri
`README.md` §2.2 L68 ile sabitlenmiştir: `EQ → Compressor → Reverb → Limiter`.

Zincirin **5. aşaması** (sahne EQ) ile **11. aşaması** (master EQ) aynı biquad motorunu kullanır; iki
kez uygulama toplam tepkiyi değiştirir (bkz. §8-2). Katsayı üretimi double hassasiyetle yapılmalı
(kaynak: `k3 index.md` §Numerik Hassasiyet).

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Zincirin kendisi | `[[../k073-dsp-chain/dsp-chain]]` |
| Ölçüm/ doğrulama | `[[../k073-dsp-chain/analysis-spectrum]]` |
| Sonraki aşama | `[[../k082-dynamics-compressor/dynamics-compressor]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | Bant yapısı | Frekans, Q, kazanç parametreleri | `eq-parametric.md` §EQ Bant Yapısı |
| 2 | Biquad filtre tipleri | Peaking, low/high shelf, cutoff | §Biquad Filtre Tipleri |
| 3 | 31-bant implementasyonu | Bant dizisi ve yürütme | §31-Bant EQ Implementasyonu |
| 4 | Preset sistemi | Önceden tanımlı katsayı setleri | §Preset Sistemi |
| 5 | Frekans planı | Bant frekans listesi | `README.md` §3.1 |
| 6 | Katsayı tablosu | Biquad koefisyonları | `README.md` §3.2 |
| 7 | Master EQ (11. aşama) | Zincir sonu EQ | `k3 index.md` §DSP Pipeline |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | Otomatik EQ (AI kapalı döngü) | D11/AI domaini |
| 2 | Analiz ile EQ otomasyonu | `[[../k073-dsp-chain/analysis-spectrum]]` (ölçüm) |
| 3 | Limiter/dynamics | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 4 | Reverb tonlaması | `[[../k083-effects-reverb/effects-reverb]]` |
| 5 | Crossover ağ geçidi | Kardeş bileşen (README §2.1 L59), kapsam dışı |

## 3. Sinyal Akışı

```
 kare --> [5. asama: 31-band EQ] --> [6. Dynamics] --> [7. Reverb] ...
             |
             +-- bant 1 (peaking) --> bant 2 --> ... --> bant 31 --> cikis
             |
             +-- (ayni biquad motoru) [11. asama: master EQ] --> limiter

 paralel tap: cikti --> FFT (frequency response olcumu) --> [[../k073-dsp-chain/analysis-spectrum]]
```

| # | Yön | Veri | Not |
|---|---|---|---|
| 1 | Girdi | Kare (float32) | 5. aşama |
| 2 | Bant sırası | Bant 1 → 31 | Sıra toplam tepkiyi etkiler (faz) |
| 3 | Katsayılar | double | `k3 index.md` §Numerik Hassasiyet |
| 4 | Çıkış | Şekillendirilmiş kare | 6. aşamaya |
| 5 | Master EQ | Ayırık uygulama | 11. aşama |
| 6 | Tepki ölçümü | Frequency response | `[[../k073-dsp-chain/analysis-spectrum]]` |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `eq-parametric.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/eq-parametric.md` (345 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Parametrik EQ

##### Genel Bakış

31 bantlı parametrik EQ, COREMUSIC'in FREKANS TEPKİSİ kontrolü için kullanılır. Her bant bağımsız olarak yapılandırılabilir: frekans, kazanç ve kalite faktörü (Q). Biquad IIR filtreleri kullanılarak verimli ve stabil bir implementasyon sağlanır.

##### Teknik Detaylar

###### EQ Bant Yapısı

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

###### Biquad Filtre Tipleri

```cpp
// Biquad filtre tipleri
enum class BiquadType {
    LowPass,        // Düşük frekans geçirgen
    HighPass,       // Yüksek frekans geçirgen
    BandPass,       // Bant geçiren
    Notch,          // Bant engelleme
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

###### 31-Bant EQ Implementasyonu

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

###### Preset Sistemi

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

##### API / Arayüz

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

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.01ms | 0.008ms |
| CPU (31 aktif bant) | < 2% | 1.5% |
| Bellek | < 1MB | 0.8MB |
| Frekans Aralığı | 20Hz-20kHz | 20Hz-20kHz |
| Kazanç Aralığı | ±12dB | ±12dB |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

##### Durum: Implementasyon

- **Faz 1**: Biquad filtreler, temel EQ
- **Faz 2**: 31-bant implementasyonu
- **Faz 3**: Frekans tepkisi hesaplama
- **Faz 4**: Preset sistemi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)



## 5. İşleme Aşamaları

| # | Adım | Girdi | Çıktı | Hata |
|---|---|---|---|---|
| 1 | Parametre kabulü | Frekans/Q/kazanç | Bant durumu | Geçersiz → red |
| 2 | Katsayı üretimi | Bant parametreleri | b0,b1,b2,a1,a2 | Kayan nokta sapması |
| 3 | Bant doğrulama | Katsayılar | NaN/Inf kontrolü | NaN → bant bypass |
| 4 | Uygulama sırası | 31 bant | Ardışık filtreleme | — |
| 5 | Preset yükleme | Preset ID | Katsayı seti | Bulunamadı → varsayılan |
| 6 | Master EQ uygulama | 11. aşama parametreleri | Toplam tepki | Çift uygulama riski |
| 7 | Tepki ölçümü | Frekans taraması | Eğri | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 8 | Bypass | Anahtar | Passthrough | Bayt eşitliği korunmalı |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | DSP zinciri (5, 11. aşama) | yukarı | `[[../k073-dsp-chain/dsp-chain]]` |
| 2 | Analiz (tepki ölçümü) | yandan | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 3 | Motor çekirdeği | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 4 | Dynamics (sıradaki) | aşağı | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 5 | Reverb (tonlama) | aşağı | `[[../k083-effects-reverb/effects-reverb]]` |
| 6 | Mixer (bus EQ) | aşağı | `[[../k076-mixer-routing/mixer-routing]]` |
| 7 | Sample-rate (EQ öncesi/sonrası) | yandan | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 8 | Channel processing (kanal bazlı EQ) | yandan | `[[../k080-channel-processing/channel-processing]]` |
| 9 | Format decoder (girdi) | yandan | `[[../k081-format-decoder/format-decoder]]` |
| 10 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Kazanç = 0 dB tüm bantlar | Pasiftonuz (düz tepki) | Bypass ile eşitlenmeli |
| 2 | Q çok yüksek | Dar bant, katsayı hassasiyeti artar | NaN riski |
| 3 | Frekans = DC / Nyquist | Filtre tanımı sınırlanır | Sınır kontrolü ⚠️ |
| 4 | 31 bant + master EQ | Toplam tepki iki kez şekillenir | §8-2 |
| 5 | Örnekleme hızı değişimi | Bant frekansları sabit, katsayılar yeniden üretilir | `[[../k075-sample-rate-conversion/index]]` |
| 6 | 8.1 kanal | Kanal bazlı EQ maliyeti ölçeklenir | 128 kanal hedefi |
| 7 | Preset değişimi canlı | Katsayı takası atomik olmalı | Yarış riski ⚠️ |
| 8 | Denormal çıkış | CPU şişmesi | Flush-to-zero |
| 9 | Bit derinliği ≤16 | Ölçme gürültüsü eklenir | `[[../k079-bit-depth-conversion/index]]` |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Bozuk/çarpık tepki | Katsayı hesap hatası | Yanlış ses | Double hassasiyet + test |
| 2 | Beklenmeyen tonlama | 5. + 11. aşama çakışması | Aşırı EQ | Master EQ varsayılan bypass |
| 3 | NaN/Inf çıktısı | Sınır dışı parametre | Motor hata | Doğrulama (§5-3) |
| 4 | CPU sıçraması | 31 bant × kanal | CPU > %10 | SIMD/optimizasyon ⚠️ |
| 5 | Faz kayması | Ardışık IIR | Grup gecikmesi | Bilinçli kabul / FIR alternatif |
| 6 | Click (preset değişimi) | Katsayı ani değişimi | Tıklama | Crossfade/kademeli geçiş ⚠️ |
| 7 | Ölçüm yanlışlığı | Tap noktası EQ öncesi | Yanlış doğrulama | Tap noktası seçimi |
| 8 | Alias | Örnekleme uyuşmazlığı | Frekans sarması | SRC zorunlu |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency (zincir) | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Bant sayısı | 31 | `eq-parametric.md` · `README.md` §3 |
| 7 | K3.3 yaprak sayısı | 9 | `README.md` §Alt Katman Şeması |
| 8 | Katsayı hassasiyeti | double | `k3 index.md` §Numerik Hassasiyet |
| 9 | Bant frekansları | `README.md` §3.1 tablosu | Kaynak |
| 10 | Biquad koefisyonları | `README.md` §3.2 tablosu | Kaynak |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Katsayı üretimi RT dışında (parametre değişiminde) | `CLAUDE.md` §1-1 türevi |
| 2 | EQ state'inde tahsis yok | `CLAUDE.md` §1-1 |
| 3 | Preset değişiminde mutex yok | `CLAUDE.md` §1-2 |
| 4 | Filtre state'i `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Bant/katsayı sabitleri `constexpr` mümkünse | `CLAUDE.md` §1-5 |
| 6 | NaN/Inf temizliği | RT güvenliği (§8-3) |
| 7 | Denormal flush-to-zero | `k3 index.md` §Numerik Hassasiyet |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Tek bant impulse cevabı | Unit | Analitik eğri ile eşleşme |
| 2 | Frekans taraması (magnitude) | Unit | Beklenen tepe/dip |
| 3 | Kazanç = 0 passthrough | Unit | Bayt eşitliği |
| 4 | 31 bant toplam tepki | Integration | Beklenen eğri |
| 5 | Master EQ ayrı etkisi | Integration | Sıralı uygulama tutarlı |
| 6 | Preset yükleme | Unit | Katsayı eşitliği |
| 7 | NaN/Inf girdi | Unit | Bypass |
| 8 | CPU ölçümü | Perf | ≤ %10 ⚠️ ölçüm |
| 9 | Latency ölçümü | Perf | < 0.1 ms ⚠️ ölçüm |
| 10 | 128 kanal / 384 kHz | Integration | Kabul |
| 11 | Bit-perfect (EQ kapalı) | Integration | Bayt eşitliği |
| 12 | Uzun süre testi | Soak | Bellek sabit |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | 5. ve 11. aşama etkileşiminin kaynakta ayrı ele alınmaması | Orta | Yorum §8-2 |
| 3 | Preset geçiş politikası kanıtsız | Orta | ⚠️ VERIFICATION REQUIRED |
| 4 | `README.md` §9'da `31-band-eq.md` adı geçer; diskte `eq-parametric.md` var | Düşük | Ad uyuşmazlığı |
| 5 | ADR-025 dondurulmuş aralık (001-037) içinde mi? | Orta | ⚠️ VERIFICATION REQUIRED |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Bant/biquad/preset yapıları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/eq-parametric.md` | Kanıtlı |
| 2 | §4 verbatim (351 satır) | Aynı dosya | Kanıtlı |
| 3 | Frekans planı + katsayı tablosu | `_backup/.../k3-ses-motoru/README.md` §3.1, §3.2 | Kanıtlı |
| 4 | EQ → Compressor sırası | `_backup/.../k3-ses-motoru/README.md` §2.2 L68 | Kanıtlı |
| 5 | K3.3 (9 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 6 | ADR-025 | `_backup/.../k3-ses-motoru/README.md` §10 | Kanıtlı |
| 7 | Ölçüm değerleri, preset geçiş politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/eq-parametric.md`
(salt-okunur, 351 satır) + `README.md` §2.2, §3, §10 + `index.md` §Performans Metrikleri.
