---
title: "K073 DSP Chain — 15 Aşamalı İşleme Zinciri ve Analiz Dalları"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K073 — DSP Chain (15 Aşamalı Pipeline)

> **K numarası:** K073 · **Klasör:** `k073-dsp-chain` · **Dosya:** `dsp-chain`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (CPU bütçesi), `qa-engineer` (sıra doğruluğu)
> **İlgili ADR'ler:** `ADR-062` (DSP Pipeline Architecture) · `ADR-025` (31-band parametrik EQ) — kaynak: `_backup/.../k3-ses-motoru/README.md` §10

## 1. Genel Bakış

DSP Chain, Neva Engine Core'un yürüttüğü **15 aşamalı** gerçek zamanlı işleme pipeline'ıdır. Kaynak
`index.md` §DSP Pipeline şeması aşamaları şu sırayla verir: Input Gain → Channel Mapping → Format Convert
→ Sample Rate Convert → EQ Parametric → Dynamics Process → Reverb → Chorus/Delay → Surround Decode →
Mixer/Routing → Master EQ → Limiter → Dithering → Output Gain → Format Output.

İki ayrı kaynak aynı çekirdek sırayı doğrular: `CLAUDE.md` §3 kısa hali (`Input → Gain → 31-Band EQ →
Compressor → Reverb → Limiter → Output`) ve `README.md` §2.2 L68 zincir kuralı (`EQ → Compressor →
Reverb → Limiter`). **Crossover (Linkwitz-Riley 4. derece) zincirin kardeş bileşenidir, aşama değildir**
(kaynak: `README.md` §2.1 L59 — Alt Katman Şeması önsözü).

Her aşama bağımsız çalışır; aşama arası veri **ring buffer** ile taşınır; aşamalar `DSPStage`
arabirimini uygular. Zincirin ardışık doğası, her aşamanın girdisinin bir öncekinin çıktısı olmasını
zorunlu kılar; bu durum hata modlarını da belirler: bir aşama hata verirse ya bypass edilir ya da zincir
o noktada durur (bkz. §8).

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Kardeş (analiz dalı) | `[[analysis-spectrum]]` |
| Klasör dizini | `[[index]]` |
| Motor çekirdeği (yürütücü) | `[[../k072-neva-engine-core/neva-engine-core]]` |
| Aşama hedefleri | `[[../k074-eq-parametric/eq-parametric]]`, `[[../k082-dynamics-compressor/dynamics-compressor]]`, `[[../k083-effects-reverb/effects-reverb]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama |
|---|---|---|
| 1 | 15 aşama tanımı | Sıra, giriş/çıkış, sorumlu klasör |
| 2 | `DSPStage` arabirimi | Ortak aşama sözleşmesi |
| 3 | Biquad yapısı | IIR temel taşı (EQ, filtre) |
| 4 | Inter-stage ring buffer | Aşama arası lock-free taşıma |
| 5 | Pipeline yönetimi | Init/process/reset, bypass |
| 6 | Stage bypass / hata bayrağı | Aşama bazlı degradasyon |
| 7 | Analiz dalları | FFT spectrum, frequency response, waterfall (`[[analysis-spectrum]]`) |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | EQ katsayı üretimi | `[[../k074-eq-parametric/eq-parametric]]` |
| 2 | Compressor/limiter matematiği | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 3 | Reverb comb/allpass yapısı | `[[../k083-effects-reverb/effects-reverb]]` |
| 4 | SRC algoritması | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 5 | Downmix matrisi | `[[../k080-channel-processing/channel-processing]]` |
| 6 | Dither/noise shaping | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 7 | FFT pencere/akış detayı | `[[analysis-spectrum]]` |

## 3. Sinyal Akışı

### 3.1 15 Aşama (kaynak `index.md` §DSP Pipeline)

```
 1 Input Gain   -> 2 Channel Mapping  -> 3 Format Convert  -> 4 Sample Rate Convert
        |
 5 EQ Parametric -> 6 Dynamics Process -> 7 Reverb          -> 8 Chorus/Delay
        |
 9 Surround Decode -> 10 Mixer/Routing -> 11 Master EQ      -> 12 Limiter
        |
13 Dithering     -> 14 Output Gain     -> 15 Format Output
        |
   +----+----+
   |  ANALIZ (paralel dal): FFT spectrum / frequency response / waterfall  -> [[analysis-spectrum]]
   +---------+
```

### 3.2 Aşama → Sorumlu Klasör Eşlemesi

| # | Aşama | Klasör | Wiki-link |
|---:|---|---|---|
| 1 | Input Gain | k072 (motor) | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 2 | Channel Mapping | k080 | `[[../k080-channel-processing/channel-processing]]` |
| 3 | Format Convert | k079 / k081 | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 4 | Sample Rate Convert | k075 | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 5 | EQ Parametric | k074 | `[[../k074-eq-parametric/eq-parametric]]` |
| 6 | Dynamics Process | k082 | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 7 | Reverb | k083 | `[[../k083-effects-reverb/effects-reverb]]` |
| 8 | Chorus/Delay | k083 | `[[../k083-effects-reverb/effects-chorus-delay]]` |
| 9 | Surround Decode | k080 | `[[../k080-channel-processing/surround-decoder]]` |
| 10 | Mixer/Routing | k076 | `[[../k076-mixer-routing/mixer-routing]]` |
| 11 | Master EQ | k074 | `[[../k074-eq-parametric/eq-parametric]]` |
| 12 | Limiter | k082 | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 13 | Dithering | k079 | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 14 | Output Gain | k072 (motor) | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 15 | Format Output | k081 | `[[../k081-format-decoder/format-decoder]]` |

> **⚠️ VERIFICATION REQUIRED:** 15 aşamanın tamamının klasör eşlemesi bu dosyanın yorumudur; kaynak
> dosyalar aşama sırasını verir, klasör atamasını vermez. Aşama sırası kanıtlıdır, atama yorumdur.

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `dsp-chain.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/dsp-chain.md` (392 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### DSP Chain

##### Genel Bakış

DSP Chain, COREMUSIC'in 15 aşamalı dijital sinyal işleme pipeline'ıdır. Her aşama bir veya daha fazla DSP işlemini gerçekleştirir. Biquad katsayıları, ring buffer'lar ve lock-free yapılar kullanılarak gerçek zamanlı performans sağlanır.

##### Teknik Detaylar

###### 15-Aşamalı Pipeline

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

###### DSP Stage Arabirimi

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

###### Biquad Filtre Yapısı

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

###### Stage Implementasyonları

###### 1. Input Gain Stage

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

###### 2. Channel Mapping Stage

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

###### 5. Parametric EQ Stage

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

###### Ring Buffer (Inter-Stage)

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

###### Pipeline Yönetimi

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

##### API / Arayüz

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

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Pipeline Latency | < 0.1ms | 0.08ms |
| Stage Sayısı | 15 | 15 |
| CPU (15 aktif stage) | < 8% | 6.5% |
| Bellek | < 10MB | 8MB |
| Throughput | > 1M frames/s | 1.2M frames/s |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| SIMD | Donanım |
| K3 Neva Engine | İç |

##### Durum: Implementasyon

- **Faz 1**: Pipeline framework, stage arayüzü
- **Faz 2**: Temel stage'ler (gain, channel map, format)
- **Faz 3**: EQ ve dynamics stage'leri
- **Faz 4**: Efekt stage'leri
- **Tahmini Süre**: 3 hafta (120 adam-saat)



## 5. İşleme Aşamaları (Adımlar)

| # | Adım | Girdi | Çıktı | Bağlı Aşama |
|---|---|---|---|---|
| 1 | Pipeline init | Konfigürasyon | 15 stage nesnesi | — |
| 2 | Stage connect | Stage listesi | Ring buffer'lar | — |
| 3 | Kare alımı | Motor kuyruğu | Ham kare | 1 |
| 4 | Gain + mapping | Ham kare | Kanal eşlenmiş kare | 1-2 |
| 5 | Format/SRC | Kanal kare | Hedef format/hız | 3-4 |
| 6 | EQ | Katsayı seti | Şekillendirilmiş kare | 5 |
| 7 | Dynamics | Eşik/ratio | Sıkıştırılmış kare | 6 |
| 8 | Efektler (reverb/chorus) | Efekt parametreleri | Islak sinyal | 7-8 |
| 9 | Surround + mixer | Matris | Bus çıkışı | 9-10 |
| 10 | Master EQ + limiter | Master ayar | Güvenli seviye | 11-12 |
| 11 | Dither + output gain | Bit derinliği | Nihai kare | 13-14 |
| 12 | Format output | Nihai kare | K2 teslimi | 15 |
| 13 | Analiz tap (paralel) | Ara nokta ölçümü | FFT kareleri | `[[analysis-spectrum]]` |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | Motor çekirdeği (yürütücü) | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 2 | Parametrik EQ | aşağı (5, 11) | `[[../k074-eq-parametric/eq-parametric]]` |
| 3 | Sample-rate conversion | aşağı (4) | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 4 | Mixer/routing | aşağı (10) | `[[../k076-mixer-routing/mixer-routing]]` |
| 5 | Bit depth conversion | aşağı (3, 13) | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 6 | Channel processing | aşağı (2, 9) | `[[../k080-channel-processing/channel-processing]]` |
| 7 | Format decoder | aşağı (3, 15) | `[[../k081-format-decoder/format-decoder]]` |
| 8 | Dynamics | aşağı (6, 12) | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 9 | Efektler | aşağı (7, 8) | `[[../k083-effects-reverb/effects-reverb]]` |
| 10 | Stream buffer | yandan (girdi) | `[[../k077-stream-buffer/stream-buffer]]` |
| 11 | Gapless playback | yandan (kesme) | `[[../k078-playback-gapless/playback-gapless]]` |
| 12 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Aşama bypass edilmişse | Girdi doğrudan çıktığa | Kaynakta bypass kavramı ⚠️ VERIFICATION REQUIRED |
| 2 | Aşama hata bayrağı set edilirse | Sayaç artar, zincir akışını sürdürür | §8 |
| 3 | Ring buffer dolu | Üretici bekler/taşar | Lock-free tasarım gereği drop |
| 4 | 4. aşama (SRC) devre dışı | Örnekleme hızı uyuşmazlığı | Çıkış bozulur |
| 5 | 13. aşama (dither) atlanır | Ölçme (truncation) hatası | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 6 | 12. aşama (limiter) atlanır | Clip riski | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 7 | Crossover zincire eklenirse | Sıra kuralı ihlali | Kardeş bileşendir |
| 8 | Analiz dalı senkron değilse | Ekran gecikmesi (audio yolu etkilenmez) | `[[analysis-spectrum]]` |
| 9 | 8 kanal (8.1) girdi | 9. aşama aktif | `[[../k080-channel-processing/surround-decoder]]` |
| 10 | 384 kHz girdi | 4. aşama + CPU artışı | Hedefler `k3 index.md` |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Sıra kayması | Stage connect sırası hatalı | Yanlış sonuç (mantık hatası) | Sabit sıra tanımı + birim test |
| 2 | Çift uygulama (double EQ) | Master EQ + stage EQ çakışması | Aşırı şekillendirme | Sıra denetimi (5 ve 11 farkı) |
| 3 | Alias girdisi | 4. aşama atlanmış | Frekans sarması | SRC zorunlu kural |
| 4 | DC/offset birikimi | Gain aşaması yanlışı | Hoparlör koruması riski | DC block ⚠️ VERIFICATION REQUIRED |
| 5 | Denormal CPU şişmesi | Flush-to-zero kapalı | CPU > %10 | `k3 index.md` §Numerik Hassasiyet |
| 6 | Ring buffer yarışması | Lock-free ihlal | Bozuk kare | SPSC/MPMC atomikleri |
| 7 | Aşama bypass sessizliği | Hata→bypass geçişi tanımsız | Sessizlik | Politika + test (§7-1) |
| 8 | Analiz bloğu RT yolunu kilitleyse | FFT RT thread'de | Latency artışı | Paralel dal, RT-dışı öncelik |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | İşleme latency (tüm zincir) | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal kapasitesi | 128 | `k3 index.md` |
| 4 | Örnekleme hızı | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | K3.2 yaprak sayısı | 11 | `README.md` §Alt Katman Şeması |
| 7 | Zincir kuralı | EQ → Compressor → Reverb → Limiter | `README.md` §2.2 L68 |
| 8 | Crossover derecesi | Linkwitz-Riley 4. derece | `README.md` §2.1 L59 |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Aşama yürütmesinde tahsis yok | `CLAUDE.md` §1-1 |
| 2 | Aşama geçişlerinde mutex yok | `CLAUDE.md` §1-2 |
| 3 | Stage process `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Stage state `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Buffer boyutu `constexpr` | `CLAUDE.md` §1-5 |
| 6 | Biquad katsayıları double | `k3 index.md` §Numerik Hassasiyet |
| 7 | Denormal flush-to-zero | `k3 index.md` §Numerik Hassasiyet |
| 8 | Aşama başına stateless (kare bazlı) | `k3 index.md` §Statelessness |
| 9 | Ring buffer tek yön (SPSC) | `dsp-chain.md` §Ring Buffer |
| 10 | FFT analiz dalı RT yolundan ayrı | Yorum — ⚠️ VERIFICATION REQUIRED |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Sıra doğrulama (stage sırası) | Unit | 15 aşama sırayla çağrılır |
| 2 | Her aşama için impulse cevabı | Unit | Beklenen kernel |
| 3 | Bypass eşitliği | Unit | Bypass = passthrough bayt eşit |
| 4 | Ring buffer taşma/boşluk | Unit | Drop/mute davranışı tanımlı |
| 5 | Bit-perfect uçtan uca (efekt kapalı) | Integration | Bayt eşitliği |
| 6 | CPU bütçesi (tüm zincir) | Perf | ≤ %10 ⚠️ ölçüm gerekli |
| 7 | Latency ölçümü | Perf | < 0.1 ms ⚠️ ölçüm gerekli |
| 8 | 128 kanal kapasite | Integration | Kabul |
| 9 | 384 kHz kapasite | Integration | Kabul |
| 10 | Denormal girdi | Unit | CPU sabit kalır |
| 11 | Analiz tap doğruluğu | Unit | FFT girdisi doğru noktadan alınır |
| 12 | Crossover kardeş kontrolü | Statik/inceleme | Zincir listesinde aşama değil |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | 15 aşamanın klasör eşlemesinin yorum olması | Orta | §3.2 ⚠️ VERIFICATION REQUIRED |
| 2 | `CLAUDE.md` §3 kısa sırası ile `index.md` 15 aşama listesinin farklı detay seviyesinde olması | Düşük | İkisi de aynı çekirdek sırayı verir |
| 3 | Crossover'ın nerede uygulandığının vault'ta olmaması | Orta | Kardeş bileşen; konum ⚠️ VERIFICATION REQUIRED |
| 4 | Ölçüm kanıtı yok | Yüksek | §9 hedef olarak kaldı |
| 5 | Bypass/hata politikasının kaynakta açık olmaması | Orta | §7-1 ⚠️ VERIFICATION REQUIRED |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | 15 aşamalı sıra | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/index.md` §DSP Pipeline | Kanıtlı |
| 2 | Çekirdek sıra kuralı | `_backup/.../k3-ses-motoru/README.md` §2.2 L68 · `dsp-chain.md` L16 | Kanıtlı |
| 3 | Crossover kardeş kuralı | `_backup/.../k3-ses-motoru/README.md` §2.1 L59 | Kanıtlı |
| 4 | Stage/biquad/ring buffer detayı | `_backup/.../k3-ses-motoru/dsp-chain.md` (398 satır) | Kanıtlı |
| 5 | ADR-062 | `_backup/.../k3-ses-motoru/README.md` §10 | Kanıtlı |
| 6 | §4 verbatim | `_backup/.../k3-ses-motoru/dsp-chain.md` | Kanıtlı |
| 7 | Bypass/hata politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/dsp-chain.md` (salt-okunur,
398 satır) + `index.md` §DSP Pipeline + `README.md` §2.1-§2.2, §10 + `CLAUDE.md` §1-§3.
