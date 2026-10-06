---
title: "K076 Mixer / Routing — Bus Mimarisi, Channel Strip ve Yönlendirme Matrisi"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K076 — Mixer / Routing Matrix

> **K numarası:** K076 · **Klasör:** `k076-mixer-routing` · **Dosya:** `mixer-routing`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (toplama maliyeti), `qa-engineer` (matris doğruluğu)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (mixer için ayrı ADR numarası kaynakta yok; katman ADR'leri `ADR-025`, `ADR-062`)

## 1. Genel Bakış

Mixer / Routing, DSP zincirinin 10. aşamasında çalışan yönlendirme ve toplama (summing) bloğudur.
Kaynak `mixer-routing.md` beş yapı taşı tanımlar: **bus mimarisi**, **channel strip**, **bus
implementasyonu**, **routing matrix** ve **mixer manager**.

Mixer, çoklu kaynak/kanalın hedef bus'lara (stereo, surround, subwoofer vb.) yönlendirilip
toplanmasını sağlar. `README.md` §7 "Mixer (8.1 Surround)" konuyu iki alt başlıkta işler: **§7.1
Kanal Routing Matrisi** ve **§7.2 Bass Management**; `README.md` §Alt Katman Şeması K3.7 ise
"Kanal & Mixer" başlığında **29 kanıtlı yaprak** sayar (channel-processing, mixer-routing,
surround-decoder birlikte).

Dönüşüm sırasında **gain staging** kritiktir: toplama sonrası seviye artışı clip'e yol açabilir; bu
nedenle zincirde mixer (10) ile limiter (12) arasında master EQ (11) bulunur (`k3 index.md` §DSP Pipeline).

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Zincir (10. aşama) | `[[../k073-dsp-chain/dsp-chain]]` |
| Kanal eşleme/downmix | `[[../k080-channel-processing/channel-processing]]` |
| Surround çözümleme | `[[../k080-channel-processing/surround-decoder]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | Bus mimarisi | Bus sınıfları ve hiyerarşi | `mixer-routing.md` §Bus Mimarisi |
| 2 | Channel strip | Kanal başına süreç zinciri | §Channel Strip |
| 3 | Bus implementasyonu | Toplama mantığı | §Bus Implementasyonu |
| 4 | Routing matrix | Kaynak → hedef matrisi | §Routing Matrix |
| 5 | Mixer manager | Yönetim/yaşam döngüsü | §Mixer Manager |
| 6 | 8.1 surround routing | Kanal routing matrisi | `README.md` §7.1 |
| 7 | Bass management | Subwoofer yönlendirme | `README.md` §7.2 |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | Downmix/upmix matrisi hesapları | `[[../k080-channel-processing/channel-processing]]` |
| 2 | Surround format çözümleme | `[[../k080-channel-processing/surround-decoder]]` |
| 3 | Limiter/master seviye güvenliği | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 4 | EQ bant katsayıları | `[[../k074-eq-parametric/eq-parametric]]` |
| 5 | Fiziksel hoparlör yerleşimi | Donanım/PCB katmanı |

## 3. Sinyal Akışı

```
 kaynaklar: k081 (decode) / k077 (stream) / kanallar (k080)
        |
        v
 [channel strip]  x N  ----gain/pan/mute---->  [routing matrix]
                                                      |
                        +-----------------------------+-----------------------------+
                        |                             |                             |
                   bus: stereo (L/R)             bus: surround                 bus: sub (LFE)
                        |                             |                             |
                        +----------------> [summing / toplama] <---------------------+
                                                |
                                    [10. asama cikisi] --> [11 master EQ] --> [12 limiter]
```

| # | Yön | Veri | Not |
|---|---|---|---|
| 1 | Kaynak → channel strip | Kanal sinyali | Gain/pan/mute |
| 2 | Strip → matrix | İşlenmiş kanal | Pan ağırlıkları |
| 3 | Matrix → bus | Yönlendirilmiş sinyal | Matris katsayıları |
| 4 | Bus → summing | Toplanmış sinyal | Clip riski |
| 5 | Summing → master EQ | Bus çıkışı | 11. aşama |
| 6 | Bass management | LFE/sub yönlendirme | `README.md` §7.2 |
| 7 | Analiz tap | Bus ölçümü | `[[../k073-dsp-chain/analysis-spectrum]]` |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `mixer-routing.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/mixer-routing.md` (376 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Mixer ve Routing Matrix

##### Genel Bakış

COREMUSIC Mixer, çoklu ses kaynaklarını karıştırma ve yönlendirme sağlar. Bus mimarisi, send/return yapısı ve dinamik routing ile esnek bir ses yönlendirme sistemi sunar.

##### Teknik Detaylar

###### Bus Mimarisi

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

###### Channel Strip

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

###### Bus Implementasyonu

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

###### Routing Matrix

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

###### Mixer Manager

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

##### API / Arayüz

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

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Kanal Sayısı | 128 | 128 |
| Bus Sayısı | 16 | 16 |
| İşleme Latency | < 0.05ms | 0.03ms |
| CPU (64 kanal) | < 2% | 1.5% |
| Bellek | < 10MB | 8MB |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

##### Durum: Implementasyon

- **Faz 1**: Channel strip, pan/gain
- **Faz 2**: Bus sistemi
- **Faz 3**: Routing matrix
- **Faz 4**: Solo/mute, VCA
- **Tahmini Süre**: 2 hafta (80 adam-saat)



## 5. İşleme Aşamaları

| # | Adım | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Mixer init | Konfigürasyon (bus/kanal) | Manager | Hata → varsayılan |
| 2 | Channel strip kurulumu | Kanal parametreleri | Strip durumu | Varsayılan strip |
| 3 | Routing matrisi yükleme | Matris katsayıları | Aktif matris | Boş matris (sessiz) |
| 4 | Gain/pan uygulama | Kanal sinyali | Yönlü sinyal | Bypass |
| 5 | Bus toplama | Bus üyeleri | Toplanmış sinyal | Taşma (clip) |
| 6 | Bass management | LFE + sub | Sub çıkışı | Sub kapalı |
| 7 | Master EQ | Bus çıkışı | Şekillendirilmiş | Bypass |
| 8 | Çıkış doğrulama | Seviye | Rapor | Limiter çağrısı |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | DSP zinciri (10. aşama) | yukarı | `[[../k073-dsp-chain/dsp-chain]]` |
| 2 | Channel processing (girdi) | besleyici | `[[../k080-channel-processing/channel-processing]]` |
| 3 | Surround decoder (girdi) | besleyici | `[[../k080-channel-processing/surround-decoder]]` |
| 4 | Master EQ (11. aşama) | aşağı | `[[../k074-eq-parametric/eq-parametric]]` |
| 5 | Limiter (12. aşama) | aşağı | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 6 | Analiz (bus tap) | yandan | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 7 | Reverb send (efekt bus'ı) | aşağı | `[[../k083-effects-reverb/effects-reverb]]` |
| 8 | Motor çekirdeği | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 9 | Stream buffer (kaynak) | besleyici | `[[../k077-stream-buffer/stream-buffer]]` |
| 10 | Format decoder (kaynak) | besleyici | `[[../k081-format-decoder/format-decoder]]` |
| 11 | Bit depth (çıkış) | aşağı | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 12 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Tek kaynak aktif | Tek kanal normal akış | — |
| 2 | 0 kaynak aktif | Sessiz bus | Tanımlı davranış |
| 3 | Tüm kaynaklar tam kazançta | Toplama clip riski | Limiter (12) güvencesi |
| 4 | 8.1 çıkış | 8 kanal + LFE | `README.md` §7 |
| 5 | Bass management kapalı | Sub sinyali yok | Yapılandırma |
| 6 | Matris hücresi 0 | Kaynak → hedef bağlantısı yok | Beklenen |
| 7 | Pan ortada | Eşit L/R dağılımı | −3 dB per kanal kuralı ⚠️ |
| 8 | Kanal mute | Sessiz, matris korunur | — |
| 9 | Bus'lar arası çakışma | Yanlış yönlendirme | Matris doğrulaması |
| 10 | Parça geçişi | Bus durumu korunur | `[[../k078-playback-gapless/index]]` |
| 11 | 128 kanal | Matris × kanal maliyeti | Hedef `k3 index.md` |
| 12 | Reverb send açık | Islak bus toplanır | `[[../k083-effects-reverb/index]]` |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Aşırı seviye / clip | Toplama kazancı fazla | Bozuk ses | Gain staging + limiter |
| 2 | Kanal kayması | Matris hücre hatası | Yanlış yerleşim | Matris doğrulama testi |
| 3 | Sub'ta bozulma | Bass management kuralı ihlali | Bozuk bas | `README.md` §7.2 kuralı |
| 4 | Sessiz bus | Matris boş | Sessizlik | Matris yükleme |
| 5 | CPU aşımı | Kanal × bus çarpımı | CPU > %10 | Sparse matris ⚠️ |
| 6 | Faz iptali | Pan/faz hatası | Sönümleme | Faz doğrulama |
| 7 | DC birikimi | Kanal DC offset | Hoparlör riski | DC block ⚠️ |
| 8 | Taşan bellek | Bus tamponu yetersiz | Çökme | Tampon boyutu |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Kanal routing matrisi | `README.md` §7.1 | Kaynak |
| 7 | Bass management kuralları | `README.md` §7.2 | Kaynak |
| 8 | K3.7 yaprak | 29 | `README.md` §Alt Katman Şeması |
| 9 | Bus sayımı / kanal şeridi parametreleri | `mixer-routing.md` §Performans Metrikleri | Kaynak |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir; ölçüm kanıtı diskte yoktur. 9'daki sayısal değerler kaynak
> dosyanın ilgili bölümünden teyit edilmelidir — bu dosyada sayı tekrarlanmadı.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Bus tamponları önceden ayrılmalı | `CLAUDE.md` §1-1 |
| 2 | Routing değişimi mutex içermemeli | `CLAUDE.md` §1-2 |
| 3 | Summing `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Matris state'i `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Matris boyutları `constexpr` | `CLAUDE.md` §1-5 |
| 6 | Toplama float32, gerekirse biriktirici | `k3 index.md` §Numerik Hassasiyet |
| 7 | Denormal flush-to-zero | `k3 index.md` §Numerik Hassasiyet |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Tek kanal → tek bus | Unit | Birebir geçiş |
| 2 | İki kaynak eşit kazanç toplama | Unit | Beklenen seviye artışı |
| 3 | Matris doğruluğu (birim matris) | Unit | Passthrough |
| 4 | Pan uç değerleri | Unit | Beklenen dağılım |
| 5 | Bass management | Integration | Sub yalnız LF içerikte |
| 6 | 8.1 routing | Integration | Kanal yerleşimi doğru |
| 7 | Clip/reducer testi | Unit | Limiter devrede koruma |
| 8 | Bit-perfect (mixer basit mod) | Integration | Bayt eşitliği |
| 9 | CPU ölçümü | Perf | ≤ %10 ⚠️ |
| 10 | Latency ölçümü | Perf | < 0.1 ms ⚠️ |
| 11 | 128 kanal | Integration | Kabul |
| 12 | Sessiz bus testi | Unit | Sessizlik |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Ölçüm kanıtı yok | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Pan katsayıları (−3 dB kuralı) kanıtsız | Orta | Uydurulmadı |
| 3 | Sparse matris optimizasyonu kanıtsız | Orta | §8-5 |
| 4 | Ayrı ADR yok | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | 8.1 matrisin k080 ile sınırı | Orta | Bölüm 2.2 işaretli |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Bus/strip/matrix/manager yapıları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/mixer-routing.md` | Kanıtlı |
| 2 | §4 verbatim (382 satır) | Aynı dosya | Kanıtlı |
| 3 | 8.1 routing + bass management | `_backup/.../k3-ses-motoru/README.md` §7.1, §7.2 | Kanıtlı |
| 4 | K3.7 Kanal & Mixer (29 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 5 | 10. aşama sırası | `_backup/.../k3-ses-motoru/index.md` §DSP Pipeline | Kanıtlı |
| 6 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 7 | Pan katsayıları, ölçüm değerleri, sparse optimizasyon | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/mixer-routing.md`
(salt-okunur, 382 satır) + `README.md` §7, §Alt Katman Şeması + `index.md` §DSP Pipeline.
