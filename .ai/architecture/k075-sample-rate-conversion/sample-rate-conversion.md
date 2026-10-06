---
title: "K075 Sample Rate Conversion — Interpolasyon, Decimation ve Anti-Aliasing"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K075 — Sample Rate Conversion (SRC)

> **K numarası:** K075 · **Klasör:** `k075-sample-rate-conversion` · **Dosya:** `sample-rate-conversion`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (maliyet), `qa-engineer` (alias ölçümü)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (SRC için ayrı ADR numarası kaynakta yok; katman ADR'leri `ADR-025`, `ADR-062`)

## 1. Genel Bakış

Sample Rate Conversion (SRC), DSP zincirinin 4. aşamasında çalışan, girdi örnekleme hızını hedef
hıza çeviren dönüşüm bloğudur. Kaynak `sample-rate-conversion.md` dört yapı taşı tanımlar: **SRC
algoritması**, **asenkron SRC**, **çok kademeli (multi-stage) zincir SRC** ve **kalite preset'leri**.

SRC'nin varlık nedeni örnekleme teoremi: hıza uyarlanmazsa **alias** (frekans sarması) oluşur. Bu
nedenle SRC yalnız bir sayısal dönüşüm değil, **anti-aliasing filtreleme** ile birliktedir. Zincirde
SRC'nin yeri sabittir: 3. Format Convert'ten sonra, 5. EQ'dan önce (`k3 index.md` §DSP Pipeline).

Kaynak `index.md` §Temel İlkeller başlığı altındaki "Numerik Hassasiyet" kuralı (float32/float64 çift
hassasiyet, biquad katsayıları double, denormal flush-to-zero) SRC filtre katsayıları için de geçerlidir.

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Zincirin kendisi (4. aşama) | `[[../k073-dsp-chain/dsp-chain]]` |
| Alias ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |
| Bit derinliği (dönüşüm çifti) | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | Temel SRC algoritması | Örnekleme hızı oranı hesabı | `sample-rate-conversion.md` §SRC Algoritması |
| 2 | Asenkron SRC | Girdi/hedef hızı birbirinden bağımsız (kaynak saati) | §Asenkron SRC |
| 3 | Zincir SRC (multi-stage) | Kademeli oran ayrımı | §Zincir SRC (Multi-stage) |
| 4 | Kalite preset'leri | Düşük/orta/yüksek kalite kademeleri | §Quality Presetleri |
| 5 | API / arayüz | Yapılandırma ve çalışma | §API / Arayüz |
| 6 | Performans metrikleri | Gecikme, CPU | §Performans Metrikleri |
| 7 | Bağımlılıklar | Öncesi/sonrası bloklar | §Bağımlılıklar |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | Donanım saat kaynakları (word clock, ASIO) | Sürücü/donanım katmanı |
| 2 | Bit derinliği dönüşümü (dither) | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 3 | Kanal eşleme/downmix | `[[../k080-channel-processing/channel-processing]]` |
| 4 | EQ katsayı üretimi | `[[../k074-eq-parametric/eq-parametric]]` |
| 5 | Format çözme (FLAC/MP3) | `[[../k081-format-decoder/format-decoder]]` |

## 3. Sinyal Akışı

```
  kaynak hiz (orn. 44.1 kHz) ---> [4. asama SRC] ---> hedef hiz (orn. 96/192/384 kHz) --> [5. EQ]
                                       |
                 +---------------------+---------------------+
                 |                                           |
          asenkron SRC                                zincir SRC (multi-stage)
   (kaynak saat bagimsiz)                     (oran asamalari ayrilir: 44.1->2 x 48)
                 |                                           |
                 +--> anti-alias / interpolasyon filtresi ---+--> cikis
                                        |
                              preset: dusuk / orta / yuksek
```

| # | Yön | Veri | Not |
|---|---|---|---|
| 1 | Girdi | Kaynak hızlı kare | Öncesi: 3. aşama |
| 2 | Oran hesap | hedef/kaynak | Rasyonel yaklaşım ⚠️ detay kaynakta |
| 3 | Filtre | Anti-alias / interpolasyon | Kalite preset'e bağlı |
| 4 | Asenkron mod | Kaynak saat bağımsız | Faz kilitli olmayan durum |
| 5 | Zincir SRC | Kademeli oran | Her kademede filtre |
| 6 | Çıkış | Hedef hızlı kare | Sonrası: 5. aşama (EQ) |
| 7 | Ölçüm | Alias kontrolü | `[[../k073-dsp-chain/analysis-spectrum]]` |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `sample-rate-conversion.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md` (284 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Sample Rate Conversion (SRC)

##### Genel Bakış

COREMUSIC, farklı örnekleme hızları arasında asenkron dönüşüm sağlar. Yüksek kaliteli interpolasyon ve anti-aliasing filtreleri ile 44.1kHz ↔ 48kHz ↔ 96kHz ↔ 192kHz dönüşümleri yapar.

##### Teknik Detaylar

###### SRC Algoritması

```
┌─────────────────────────────────────────────────────┐
│              Sample Rate Conversion Akışı           │
│                                                     │
│  Input (44.1kHz) ──→ [Upsample] ──→ [Filter]      │
│                          ↓              ↓           │
│                     [2x Interpolate] [Anti-alias]  │
│                          ↓              ↓           │
│                     [Downsample] ←──── [Filter]    │
│                          ↓                          │
│  Output (48kHz) ───────────────────────────────────│
└─────────────────────────────────────────────────────┘
```

###### Asenkron SRC

```cpp
class AsyncSRC {
public:
    AsyncSRC(double inputRate, double outputRate) 
        : inputRate(inputRate), outputRate(outputRate) {
        
        // Upsample/downsample oranlarını hesapla
        calculateRatios();
        
        // Filtre katsayılarını hesapla
        designFilter();
    }
    
    void process(const float* input, uint32_t inputFrames,
                 float* output, uint32_t* outputFrames) {
        
        uint32_t outIndex = 0;
        double phase = 0.0;
        
        while (phase < inputFrames - 1) {
            uint32_t index = static_cast<uint32_t>(phase);
            double frac = phase - index;
            
            // Linear interpolation (basit)
            if (index + 1 < inputFrames) {
                output[outIndex] = input[index] * (1.0 - frac) + 
                                   input[index + 1] * frac;
            } else {
                output[outIndex] = input[index];
            }
            
            // Filtre uygula
            output[outIndex] = filter.process(output[outIndex]);
            
            outIndex++;
            phase += ratio;
        }
        
        *outputFrames = outIndex;
    }
    
    // High-quality Sinc interpolation
    void processSinc(const float* input, uint32_t inputFrames,
                     float* output, uint32_t* outputFrames) {
        
        uint32_t outIndex = 0;
        double phase = 0.0;
        
        while (phase < inputFrames - sincTaps) {
            uint32_t index = static_cast<uint32_t>(phase);
            double frac = phase - index;
            
            float sample = 0.0f;
            
            // Sinc interpolation
            for (int tap = -sincTaps; tap <= sincTaps; tap++) {
                int sampleIndex = index + tap;
                if (sampleIndex >= 0 && 
                    sampleIndex < static_cast<int>(inputFrames)) {
                    double sinc = sincFunction(
                        (tap - frac) * M_PI);
                    double window = windowFunction(
                        tap - frac, sincTaps);
                    sample += input[sampleIndex] * sinc * window;
                }
            }
            
            output[outIndex] = sample;
            outIndex++;
            phase += ratio;
        }
        
        *outputFrames = outIndex;
    }
    
private:
    double inputRate;
    double outputRate;
    double ratio;
    
    static const int sincTaps = 8;
    
    void calculateRatios() {
        // Basit kesirli oran
        ratio = outputRate / inputRate;
    }
    
    double sincFunction(double x) {
        if (std::abs(x) < 1e-10) return 1.0;
        return std::sin(x) / x;
    }
    
    double windowFunction(double x, int taps) {
        // Blackman window
        double n = x / taps;
        return 0.42 - 0.50 * std::cos(2.0 * M_PI * n) + 
               0.08 * std::cos(4.0 * M_PI * n);
    }
    
    // Anti-aliasing low-pass filtre
    class LowPassFilter {
    public:
        float process(float input) {
            y0 = b0 * input + b1 * x1 + b2 * x2 - a1 * y1 - a2 * y2;
            x2 = x1; x1 = input;
            y2 = y1; y1 = y0;
            return y0;
        }
    private:
        float b0 = 0.1f, b1 = 0.2f, b2 = 0.1f;
        float a1 = -0.8f, a2 = 0.2f;
        float x1 = 0, x2 = 0, y1 = 0, y2 = 0;
    };
    
    LowPassFilter filter;
};
```

###### Zincir SRC (Multi-stage)

```cpp
class MultiStageSRC {
public:
    // 44.1k → 48k için zincir dönüşüm
    // 44100 × 160 = 7056000
    // 7056000 / 147 = 48000
    void process441to48(const float* input, uint32_t inputFrames,
                        float* output, uint32_t* outputFrames) {
        
        // Aşama 1: 44100 → 7056000 (x160 upsample)
        stage1.process(input, inputFrames, 
                       tempBuffer, &tempFrames);
        
        // Aşama 2: 7056000 → 48000 (/147 downsample)
        stage2.process(tempBuffer, tempFrames,
                       output, outputFrames);
    }
    
    // 48k → 96k için (2x upsample)
    void process48to96(const float* input, uint32_t inputFrames,
                       float* output, uint32_t* outputFrames) {
        stage2x.process(input, inputFrames, 
                        output, outputFrames);
    }
    
    // 96k → 44.1k için
    void process96to441(const float* input, uint32_t inputFrames,
                        float* output, uint32_t* outputFrames) {
        // 96000 × 147 = 14112000
        // 14112000 / 320 = 44100
        stage1.process(input, inputFrames,
                       tempBuffer, &tempFrames);
        stage2.process(tempBuffer, tempFrames,
                       output, outputFrames);
    }
    
private:
    AsyncSRC stage1{44100.0, 7056000.0};
    AsyncSRC stage2{7056000.0, 48000.0};
    AsyncSRC stage2x{48000.0, 96000.0};
    
    std::vector<float> tempBuffer;
    uint32_t tempFrames;
};
```

###### Quality Presetleri

```cpp
enum class SRCQuality {
    Draft,      // Hız öncelikli, düşük kalite
    Standard,   // Dengeli
    High,       // Yüksek kalite
    Ultra       // En yüksek kalite
};

struct SRCProfile {
    int filterTaps;
    bool useSinc;
    uint32_t oversampling;
};

SRCProfile getProfile(SRCQuality quality) {
    switch (quality) {
        case SRCQuality::Draft:
            return {4, false, 2};
        case SRCQuality::Standard:
            return {8, true, 4};
        case SRCQuality::High:
            return {16, true, 8};
        case SRCQuality::Ultra:
            return {32, true, 16};
    }
    return {8, true, 4};
}
```

##### API / Arayüz

```cpp
namespace neva::dsp {

class SampleRateConverter {
public:
    SampleRateConverter(double inputRate, double outputRate,
                        SRCQuality quality = SRCQuality::High);
    
    // Dönüşüm
    uint32_t process(const float* input, uint32_t inputFrames,
                     float* output, uint32_t maxOutputFrames);
    
    // Ratio sorgusu
    double getRatio() const;
    double getInputRate() const;
    double getOutputRate() const;
    
    // Kalite
    void setQuality(SRCQuality quality);
    SRCQuality getQuality() const;
    
    // Delay bilgisi
    uint32_t getLatency() const;
    
private:
    MultiStageSRC converter;
    SRCQuality quality;
    double inputRate;
    double outputRate;
};

} // namespace neva::dsp
```

##### Performans Metrikleri

| Metrik | Draft | Standard | High | Ultra |
|--------|-------|----------|------|-------|
| Kalite (SNR) | 60dB | 80dB | 100dB | 120dB |
| CPU | 0.5% | 1% | 2% | 5% |
| Latency | 2ms | 5ms | 10ms | 20ms |
| Bellek | 10KB | 50KB | 200KB | 1MB |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

##### Durum: Implementasyon

- **Faz 1**: Temel linear interpolation
- **Faz 2**: Sinc interpolation
- **Faz 3**: Multi-stage SRC
- **Faz 4**: Quality presetleri
- **Tahmini Süre**: 2 hafta (80 adam-saat)



## 5. İşleme Aşamaları

| # | Adım | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Hız okuma | Kaynak/hedef hız | Oran | Uyumsuzsa red |
| 2 | Mod seçimi | Senkron/asenkron | Çalışma modu | Varsayılan mod |
| 3 | Kademe planı | Oran | Zincir adımları | Tek kademe (düşük kalite) |
| 4 | Filtre katsayısı üretimi | Kalite preset | Filtre katsayıları | Varsayılan preset |
| 5 | Anti-alias uygulama | Girdi kare | Filtrelenmiş kare | Bypass (alias riski) |
| 6 | Interpolasyon | Filtrelenmiş | Hedef hızlı | Bozuk çıktı |
| 7 | Çıkış tamponu | Yeni örnekler | Hedef kare | Drop/mute |
| 8 | Alias doğrulama | Çıkış spektrumu | Rapor | Test başarısızlığı |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | DSP zinciri (4. aşama) | yukarı | `[[../k073-dsp-chain/dsp-chain]]` |
| 2 | Analiz (alias ölçümü) | yandan | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 3 | Bit depth conversion (ardışık dönüşüm) | aşağı | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 4 | EQ (sonraki aşama) | aşağı | `[[../k074-eq-parametric/eq-parametric]]` |
| 5 | Format decoder (girdi hızı) | besleyici | `[[../k081-format-decoder/format-decoder]]` |
| 6 | Stream buffer (akış hızı) | besleyici | `[[../k077-stream-buffer/stream-buffer]]` |
| 7 | Channel processing (kanal başına) | yandan | `[[../k080-channel-processing/channel-processing]]` |
| 8 | Mixer (bus hızı) | aşağı | `[[../k076-mixer-routing/mixer-routing]]` |
| 9 | Motor çekirdeği | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 10 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Kaynak = hedef hız | Passthrough (bypass) | İşlem maliyeti 0 olmalı |
| 2 | Kademeli olmayan oran (44.1 → 48) | Asenkron/çok kademeli yol | Klasik zor durum |
| 3 | Kaynak saat drift (asenkron) | Sürekli faz düzeltmesi | Asenkron SRC |
| 4 | 384 kHz hedef | En yüksek maliyet | Hedef `k3 index.md` |
| 5 | 128 kanal | Maliyet kanal ile ölçeklenir | Hedef `k3 index.md` |
| 6 | Düşük kalite preset | CPU düşük, alias eşiği düşük | Preset değişimi |
| 7 | Girdi sessizliği | Çıkış sessiz (filtre durulması) | Ring yan etkisi |
| 8 | Bit derinliği uyuşmazlığı | Ayrı dönüşüm (3. aşama) | `[[../k079-bit-depth-conversion/index]]` |
| 9 | Parça geçişi / hız değişimi | Yeniden plan gerekir | `[[../k078-playback-gapless/index]]` |
| 10 | Denormal girdi | CPU şişmesi | Flush-to-zero |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Frekans sarması (alias) | Anti-alias filtresi yok/yanlış | Bozuk ses | Filtre + alias ölçümü |
| 2 | Hafif ton kayması | Faz/DC kayması | Ton değişimi | Filtre tasarımı |
| 3 | Dropouts | Tampon yetersiz | Tıklama | Tampon boyutu |
| 4 | CPU > %10 | Tek kademeli yüksek oran | Performans | Zincir SRC |
| 5 | Asenkron modda artefakt | Faz düzeltme yok | Gürültü | Asenkron SRC |
| 6 | Kalite düşüşü | Düşük preset seçimi | Alias | Preset seçimi |
| 7 | Yanlış hedef hız | Yapılandırma hatası | Uyumsuz zincir | Prepare doğrulaması |
| 8 | Ölçüm belirsizliği | Test vektörü yok | Güven kaybı | Test (§11) |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Kalite preset'leri | Kaynakta tanımlı (düşük/orta/yüksek) | `sample-rate-conversion.md` §Quality Presetleri |
| 7 | SRC gecikme/CPU değerleri | Kaynakta §Performans Metrikleri | `sample-rate-conversion.md` |
| 8 | K3.6 yaprak sayısı | 30 (format + SRC + bit depth) | `README.md` §Alt Katman Şeması |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir (ölçüm yok); 7'deki sayısal değerlerin birebir kaynağı
> okunarak teyit edilmesi gerekir — bu dosyada sayı tekrarlanmadı (uydurma yok).

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Filtre/tampon tahsisi RT dışında | `CLAUDE.md` §1-1 |
| 2 | Hız değişiminde mutex yok | `CLAUDE.md` §1-2 |
| 3 | Uygulama `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Filtre state'i `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Kademe/preset sabitleri `constexpr` | `CLAUDE.md` §1-5 |
| 6 | Katsayılar double | `k3 index.md` §Numerik Hassasiyet |
| 7 | Denormal flush-to-zero | `k3 index.md` §Numerik Hassasiyet |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Sinüs → SRC → spektrum | Unit | Yeni hızda tepe, alias < eşik (eşik ⚠️) |
| 2 | 44.1 → 48 kHz (klasik oran) | Integration | Çalışır, artefakt yok |
| 3 | 48 → 384 kHz | Integration | Kabul |
| 4 | Passthrough (hız eşit) | Unit | Bayt eşitliği |
| 5 | Asenkron drift testi | Integration | Sürekli çalışma |
| 6 | Zincir SRC vs tek kademe | Perf | CPU karşılaştırma ⚠️ ölçüm |
| 7 | Kalite preset farkı | Unit | Alias eşiği değişir |
| 8 | Bit-perfect (hız eşit, efekt kapalı) | Integration | Bayt eşitliği |
| 9 | CPU ölçümü | Perf | ≤ %10 ⚠️ |
| 10 | Latency ölçümü | Perf | < 0.1 ms ⚠️ |
| 11 | 128 kanal | Integration | Kabul |
| 12 | Sessiz girdi → sessiz çıkış | Unit | DC/durulma yok |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | SRC gecikme/CPU sayılarının bu dosyada tekrarlanmaması | Düşük | Kaynak okunarak teyit edilmeli |
| 3 | Asenkron modun saat kaynağı vault'ta tanımsız | Orta | Kapsam dışı (donanım katmanı) |
| 4 | Ayrı ADR bulunmaması | Orta | `⚠️ VERIFICATION REQUIRED` |
| 5 | 4. aşamanın bypass edilebilirliği kanıtsız | Orta | §7-1 varsayımı |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Algoritmalar (temel/asenkron/çok kademeli) ve preset'ler | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md` | Kanıtlı |
| 2 | §4 verbatim (290 satır) | Aynı dosya | Kanıtlı |
| 3 | SRC'nin 4. aşamadaki yeri | `_backup/.../k3-ses-motoru/index.md` §DSP Pipeline | Kanıtlı |
| 4 | K3.6 Format & Dönüşüm (30 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 5 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` §Performans Metrikleri | Kanıtlı (hedef) |
| 6 | Numerik hassasiyet kuralları | `_backup/.../k3-ses-motoru/index.md` §Temel İlkeller | Kanıtlı |
| 7 | Ölçüm değerleri, alias eşiği, saat kaynağı | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/sample-rate-conversion.md`
(salt-okunur, 290 satır) + `index.md` §DSP Pipeline, §Temel İlkeller + `README.md` §Alt Katman Şeması.
