---
title: "K078 Gapless Playback — Pre-decode, Crossfade ve Parça Geçişleri"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K078 — Gapless Playback ve Crossfade

> **K numarası:** K078 · **Klasör:** `k078-playback-gapless` · **Dosya:** `playback-gapless`
> **Sorumlu persona:** `embedded-engineer` (birincil), `qa-engineer` (geçiş testleri), `performance-engineer` (pre-decode maliyeti)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (gapless için ayrı ADR numarası kaynakta yok; katman ADR'leri `ADR-025`, `ADR-062`)

## 1. Genel Bakış

Gapless Playback, parçalar arasındaki **boşluksuz** geçişi sağlayan çalma katmanıdır; crossfade ise
bilinçli olarak iki parçanın üst üste bindirilmesidir. Kaynak `playback-gapless.md` dört yapı tanımlar:
**gapless playback yapısı**, **pre-decode mekanizması**, **crossfade implementasyonu** ve **gapless
playback manager**.

Pre-decode, bir sonraki parçanın çalma anından önce çözümlenip tampona alınmasıdır; böylece decode
gecikmesi çalma boşluğuna dönüşmez. Crossfade, kenarlardaki sert geçişleri yumuşatır; ancak iki
parçanın aynı anda işlenmesini gerektirdiğinden CPU ve bellek maliyeti ikiye katlanır.

Kaynak `README.md` §Alt Katman Şeması K3.5 "Analiz & Çalma" başlığında gapless, stream buffer ve
analiz birlikte sayılır (27 kanıtlı yaprak). Çalma sırasındaki zincir etkileşimi (parça değişimi anında
EQ/dynamics state'inin ne olduğu) kaynakta açıkça ele alınmaz → `⚠️ VERIFICATION REQUIRED`.

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Girdi tamponu | `[[../k077-stream-buffer/stream-buffer]]` |
| Çözümleyici | `[[../k081-format-decoder/format-decoder]]` |
| Motor (durum makinesi) | `[[../k072-neva-engine-core/neva-engine-core]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | Gapless yapı | Boşluksuz geçiş kurgusu | `playback-gapless.md` §Gapless Playback Yapısı |
| 2 | Pre-decode | Sonraki parçanın önceden çözümlenmesi | §Pre-decode Mekanizması |
| 3 | Crossfade | Üst üste bindirme | §Crossfade Implementasyonu |
| 4 | Manager | Çalma listesi/geçiş yönetimi | §Gapless Playback Manager |
| 5 | API / arayüz | Kontrol ve durum | §API / Arayüz |
| 6 | Performans metrikleri | Geçiş maliyeti | §Performans Metrikleri |
| 7 | Bağımlılıklar | Çalma zinciri | §Bağımlılıklar |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | Decode algoritması | `[[../k081-format-decoder/format-decoder]]` |
| 2 | Tampon eşikleri/underrun | `[[../k077-stream-buffer/stream-buffer]]` |
| 3 | Mix/loop/shuffle politikası | Uygulama katmanı |
| 4 | Aşama state yönetimi (EQ/dynamics) | `[[../k074-eq-parametric/eq-parametric]]`, `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 5 | UI/Zaman çizelgesi | Uygulama katmanı |

## 3. Sinyal Akışı

```
 parca A (caliyor) ------------------+                                   |
                                     |                                   |
 parca B (pre-decode) --> [decode] --+--> [crossfade bolgesi] --> cikis   |
                                     |          |                         |
                                     |   fade-out A (kazanc dususu)       |
                                     |   fade-in  B (kazanc artisi)       |
                                     |                                   |
 [gapless manager] --gecis karari--> kesme noktasi / bolge suresi         |
        |                                                                   |
        +--> tampon doldurma: [[../k077-stream-buffer/stream-buffer]] -----+
```

| # | Yön | Veri | Not |
|---|---|---|---|
| 1 | Parça A | Çalıyor (giriş) | Normal zincir |
| 2 | Manager | Sonraki parça seçimi | Önceden tetikleme |
| 3 | Pre-decode | Parça B PCM | Tahsis RT-dışı |
| 4 | Geçiş modu | Gapless mi crossfade mi? | Yapılandırma |
| 5 | Crossfade | Kazanç eğrileri (A↓, B↑) | Eğri tipi ⚠️ |
| 6 | Çıkış | Birleşik sinyal | 10. aşamaya (mixer) |
| 7 | Tampon | A→B doldurma | `[[../k077-stream-buffer/index]]` |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `playback-gapless.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/playback-gapless.md` (377 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Gapless Playback ve Crossfade

##### Genel Bakış

COREMUSIC, kesintisiz müzik reproduksiyonu için gapless playback ve crossfade desteği sağlar. Track geçişlerinde sessizlik oluşmasını engeller ve smooth geçişler sunar.

##### Teknik Detaylar

###### Gapless Playback Yapısı

```
┌─────────────────────────────────────────────────────┐
│              Gapless Playback Akışı                 │
│                                                     │
│  [Track N] ──→ [Decode Buffer A] ──→ [Output]      │
│                     ↓                               │
│              [Pre-decode N+1]                       │
│                     ↓                               │
│  [Track N+1] ──→ [Decode Buffer B] ──→ [Output]    │
│                                                     │
│  Gap = 0 samples (aralıksız)                           │
└─────────────────────────────────────────────────────┘
```

###### Pre-decode Mekanizması

```cpp
class PreDecoder {
public:
    PreDecoder(uint32_t bufferMs) 
        : bufferFrames(bufferMs * 48000 / 1000) {
        buffer.resize(bufferFrames * 2);  // stereo
    }
    
    // Bir sonraki track'i önceden decode et
    void preDecode(FormatDecoder& decoder, 
                   uint32_t lookaheadFrames) {
        uint32_t framesDecoded = 0;
        
        while (framesDecoded < bufferFrames) {
            uint32_t frames = decoder.read(
                buffer.data() + framesDecoded * 2,
                bufferFrames - framesDecoded
            );
            
            if (frames == 0) break;  // EOF
            framesDecoded += frames;
        }
        
        readyFrames = framesDecoded;
    }
    
    // Pre-decoded buffer'dan oku
    void read(float* output, uint32_t frames) {
        uint32_t framesToRead = std::min(frames, readyFrames);
        
        std::copy(buffer.data(), 
                  buffer.data() + framesToRead * 2,
                  output);
        
        // Kalan veriyi başa kaydır
        if (framesToRead < readyFrames) {
            std::memmove(buffer.data(),
                        buffer.data() + framesToRead * 2,
                        (readyFrames - framesToRead) * 2 * sizeof(float));
        }
        
        readyFrames -= framesToRead;
    }
    
    bool isReady() const { return readyFrames > 0; }
    uint32_t getReadyFrames() const { return readyFrames; }
    
private:
    uint32_t bufferFrames;
    std::vector<float> buffer;
    uint32_t readyFrames = 0;
};
```

###### Crossfade Implementasyonu

```cpp
class Crossfader {
public:
    enum CrossfadeCurve {
        Linear,
        EqualPower,
        Exponential
    };
    
    Crossfader(double sampleRate) : sampleRate(sampleRate) {}
    
    void setParams(uint32_t durationMs, CrossfadeCurve curve) {
        this->durationMs = durationMs;
        this->curve = curve;
        fadeSamples = static_cast<uint32_t>(
            sampleRate * durationMs / 1000.0);
    }
    
    void process(float** trackA, float** trackB, 
                 float** output, uint32_t frameCount,
                 uint32_t crossfadePosition) noexcept {
        
        for (uint32_t i = 0; i < frameCount; i++) {
            uint32_t pos = crossfadePosition + i;
            float progress = static_cast<float>(pos) / fadeSamples;
            progress = std::clamp(progress, 0.0f, 1.0f);
            
            float gainA = calculateGain(1.0f - progress);
            float gainB = calculateGain(progress);
            
            for (uint32_t ch = 0; ch < channels; ch++) {
                output[ch][i] = trackA[ch][i] * gainA + 
                                trackB[ch][i] * gainB;
            }
        }
    }
    
    float calculateGain(float progress) {
        switch (curve) {
            case Linear:
                return progress;
            case EqualPower:
                return std::sqrt(progress);
            case Exponential:
                return progress * progress;
            default:
                return progress;
        }
    }
    
    bool isCrossfading() const { return crossfadeActive; }
    
private:
    double sampleRate;
    uint32_t durationMs = 3000;  // 3 saniye varsayılan
    uint32_t fadeSamples;
    uint32_t channels = 2;
    CrossfadeCurve curve = EqualPower;
    bool crossfadeActive = false;
};
```

###### Gapless Playback Manager

```cpp
class GaplessPlaybackManager {
public:
    GaplessPlaybackManager(double sampleRate) 
        : sampleRate(sampleRate),
          crossfader(sampleRate) {
        crossfader.setParams(3000, Crossfader::EqualPower);
    }
    
    // Playlist yönetimi
    void setPlaylist(const std::vector<std::string>& tracks) {
        this->tracks = tracks;
        currentTrackIndex = 0;
        loadTrack(currentTrackIndex);
    }
    
    // İşleme döngüsü
    void process(float** output, uint32_t frameCount) {
        if (!currentDecoder) return;
        
        if (crossfader.isCrossfading()) {
            // Crossfade modu
            processCrossfade(output, frameCount);
        } else {
            // Normal gapless mod
            processGapless(output, frameCount);
        }
        
        // Bir sonraki track'e geçiş kontrolü
        checkTrackTransition();
    }
    
    // Kontrol
    void nextTrack() {
        pendingTransition = true;
    }
    
    void previousTrack() {
        if (currentDecoder && 
            currentDecoder->getCurrentPosition() > 3 * sampleRate) {
            // 3 saniye geçmişse başa dön
            currentDecoder->seek(0);
        } else if (currentTrackIndex > 0) {
            currentTrackIndex--;
            loadTrack(currentTrackIndex);
        }
    }
    
    void setCrossfadeDuration(uint32_t ms) {
        crossfader.setParams(ms, Crossfader::EqualPower);
    }
    
    // Durum
    uint32_t getCurrentTrackIndex() const { 
        return currentTrackIndex; 
    }
    uint64_t getCurrentPosition() const {
        return currentDecoder ? 
               currentDecoder->getCurrentPosition() : 0;
    }
    uint64_t getTrackDuration() const {
        return currentDecoder ? 
               currentDecoder->getTotalFrames() : 0;
    }
    
private:
    double sampleRate;
    uint32_t channels = 2;
    
    std::vector<std::string> tracks;
    uint32_t currentTrackIndex = 0;
    
    std::unique_ptr<FormatDecoder> currentDecoder;
    std::unique_ptr<FormatDecoder> nextDecoder;
    PreDecoder preDecoder;
    Crossfader crossfader;
    
    bool pendingTransition = false;
    bool crossfadeActive = false;
    uint32_t crossfadePosition = 0;
    
    void loadTrack(uint32_t index) {
        if (index >= tracks.size()) return;
        
        currentDecoder = std::make_unique<FormatDecoder>();
        currentDecoder->openFile(tracks[index]);
        
        // Bir sonraki track'i önceden decode et
        if (index + 1 < tracks.size()) {
            nextDecoder = std::make_unique<FormatDecoder>();
            nextDecoder->openFile(tracks[index + 1]);
            preDecoder.preDecode(*nextDecoder, 0);
        }
    }
    
    void processGapless(float** output, uint32_t frameCount) {
        // Mevcut track'ten oku
        uint32_t framesRead = currentDecoder->read(
            output[0], frameCount);
        
        // EOF kontrolü
        if (framesRead < frameCount) {
            // Kalan frame'leri bir sonraki track'ten doldur
            if (nextDecoder) {
                nextDecoder->read(
                    output[0] + framesRead * channels,
                    frameCount - framesRead
                );
            }
        }
    }
    
    void processCrossfade(float** output, uint32_t frameCount) {
        float** trackA = new float*[channels];
        float** trackB = new float*[channels];
        
        for (uint32_t ch = 0; ch < channels; ch++) {
            trackA[ch] = new float[frameCount];
            trackB[ch] = new float[frameCount];
        }
        
        currentDecoder->read(trackA[0], frameCount);
        nextDecoder->read(trackB[0], frameCount);
        
        crossfader.process(trackA, trackB, output, frameCount, 
                          crossfadePosition);
        
        crossfadePosition += frameCount;
        
        // Crossfade tamamlandı mı?
        if (crossfadePosition >= crossfader.getDuration()) {
            currentDecoder = std::move(nextDecoder);
            nextDecoder = nullptr;
            crossfadeActive = false;
            currentTrackIndex++;
        }
        
        for (uint32_t ch = 0; ch < channels; ch++) {
            delete[] trackA[ch];
            delete[] trackB[ch];
        }
        delete[] trackA;
        delete[] trackB;
    }
    
    void checkTrackTransition() {
        if (pendingTransition || 
            currentDecoder->getCurrentPosition() >= 
            currentDecoder->getTotalFrames() - 48000) {
            // Crossfade başlat veya gapless geçiş
            if (nextDecoder) {
                crossfadeActive = true;
                crossfadePosition = 0;
            }
            pendingTransition = false;
        }
    }
};
```

##### API / Arayüz

```cpp
namespace neva::dsp {

class PlaybackModule {
public:
    PlaybackModule(double sampleRate);
    
    // Playlist
    void setPlaylist(const std::vector<std::string>& tracks);
    void addTrack(const std::string& track);
    void clearPlaylist();
    
    // Kontrol
    void play();
    void pause();
    void stop();
    void nextTrack();
    void previousTrack();
    
    // Seek
    void seekToFrame(uint64_t frame);
    void seekToTime(double seconds);
    
    // Crossfade
    void setCrossfadeDuration(uint32_t ms);
    void enableCrossfade(bool enable);
    
    // İşleme
    void process(float** output, uint32_t frameCount);
    
    // Durum
    PlaybackState getState() const;
    uint32_t getCurrentTrackIndex() const;
    uint64_t getCurrentPosition() const;
    uint64_t getTrackDuration() const;
    
private:
    GaplessPlaybackManager manager;
};

} // namespace neva::dsp
```

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Gap Boyutu | 0 samples | 0 samples |
| Crossfade Süresi | 0-10s | 3s |
| Pre-decode Buffer | 100ms | 100ms |
| CPU (crossfade) | < 1% | 0.5% |
| Bellek | < 5MB | 3.5MB |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 Format Decoder | İç |

##### Durum: Implementasyon

- **Faz 1**: Gapless playback
- **Faz 2**: Crossfade
- **Faz 3**: Pre-decode mekanizması
- **Faz 4**: Playlist yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)



## 5. İşleme Aşamaları

| # | Adım | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Çalma listesi okuma | Sıra | Sonraki parça | Liste sonu → dur |
| 2 | Pre-decode tetikleme | Mesafe eşiği | Tampon B | Zamanında olmazsa underrun |
| 3 | Decode B | Dosya | PCM B | Hata → gapless kapat |
| 4 | Geçiş modu kararı | Yapılandırma | Mod | Varsayılan (gapless) |
| 5 | Gapless kesme | A sonu | B başlangıcı | Boşluk riski |
| 6 | Crossfade yürütme | A + B | Birleşik | CPU artışı |
| 7 | Kazanç eğrisi | Süre | Kazanç değerleri | Sabit eğri |
| 8 | State temizliği | Parça değişimi | Yeni state | Kalıcı state ⚠️ |
| 9 | Tampon devri | B → A | Devir | Taşma |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | Stream buffer | aşağı/aşağı (doldurma) | `[[../k077-stream-buffer/stream-buffer]]` |
| 2 | Format decoder (pre-decode) | yukarı | `[[../k081-format-decoder/format-decoder]]` |
| 3 | Motor durum makinesi | yukarı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 4 | DSP zinciri (state geçişi) | yandan | `[[../k073-dsp-chain/dsp-chain]]` |
| 5 | EQ state | yandan | `[[../k074-eq-parametric/eq-parametric]]` |
| 6 | Dynamics state (release) | yandan | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 7 | Reverb tail (kuyruk) | yandan | `[[../k083-effects-reverb/effects-reverb]]` |
| 8 | Mixer (bus) | aşağı | `[[../k076-mixer-routing/mixer-routing]]` |
| 9 | SRC (hız değişimi) | yandan | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 10 | Analiz (geçiş ölçümü) | yandan | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 11 | Bit depth (çıkış) | aşağı | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 12 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Gapless parça (ör. canlı kayıt) | Boşluksuz kesme | Temel durum |
| 2 | Crossfade devrede | Üst üste bindirme | CPU 2× |
| 3 | Son parça | Geçiş yok → dur | — |
| 4 | Pre-decode zamanında bitemezse | Underrun riski | `[[../k077-stream-buffer/index]]` |
| 5 | Parça boyutu crossfade'den kısa | Kısaltılmış bölge | Politika ⚠️ |
| 6 | Örnekleme hızı farklı (parçalar arası) | Yeniden plan | `[[../k075-sample-rate-conversion/index]]` |
| 7 | Kanal konfigürasyonu farklı | Uyarlama | `[[../k080-channel-processing/index]]` |
| 8 | Reverb tail kesilir | Kuyruk anında biter | Politika ⚠️ |
| 9 | Compressor release ortada | Yeni parçaya taşar mı? | Politika ⚠️ |
| 10 | Liste duraklat/ileri-geri | Manager state | — |
| 11 | Aynı anda ileri + crossfade | Yarış | Sıralı ejecyon |
| 12 | Dosya sonu okuma hatası | Geçiş iptal | Hata modu |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Parçalar arası boşluk | Pre-decode geç/kuyruk yetersiz | Duyulabilir kesme | Erken tetikleme |
| 2 | Tıklama (kenar) | Sert kesme | Click | Crossfade/dc block |
| 3 | CPU sıçraması | Crossfade 2× işleme | Performans | Kısa bölge |
| 4 | Bellek artışı | İki parça tamponu | Bellek hedefi | Havuz paylaşımı |
| 5 | Kalıcı efekt (reverb tail) | State temizlenmedi | Kirli geçiş | State reset |
| 6 | Yarış (duraklat + geçiş) | Eşzamanlı çağrı | Bozuk durum | Sıralama |
| 7 | Bozuk decode | Dosya hatası | Geçiş iptali | Hata yönetimi |
| 8 | Underrun | Pre-decode yetersiz | Tıklama | `[[../k077-stream-buffer/index]]` |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | Latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Geçiş/crossfade metrikleri | `playback-gapless.md` §Performans Metrikleri | Kaynak |
| 7 | K3.5 yaprak | 27 | `README.md` §Alt Katman Şeması |
| 8 | Crossfade süresi/eğrileri | ⚠️ VERIFICATION REQUIRED | Kanıt yok |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir (ölçüm yok); 8'deki süre/eğri değerleri uydurulmadı.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Pre-decode tahsisi RT-dışı | `CLAUDE.md` §1-1 |
| 2 | Manager geçişlerinde mutex yok | `CLAUDE.md` §1-2 |
| 3 | Geçiş yürütmesi `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Kazanç eğrisi tamponu `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Eğri sabitleri `constexpr` mümkünse | `CLAUDE.md` §1-5 |
| 6 | Crossfade kazançları float32 | `k3 index.md` §Numerik Hassasiyet |
| 7 | Geçişte dosya okuma (sistem çağrısı) RT'de yasak | `k3 index.md` §Temel İlkeller |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Gapless iki parça (kesintisiz) | Integration | Boşluk ≈ 0 (eşik ⚠️) |
| 2 | Crossfade eğri doğruluğu | Unit | Beklenen kazanç eğrisi |
| 3 | CPU (crossfade açık/kapalı) | Perf | Fark ölçümlü ⚠️ |
| 4 | Pre-decode zamanlaması | Integration | Underrun yok |
| 5 | Parça boyutu < crossfade süresi | Unit | Kısaltma davranışı |
| 6 | Hız farklılığı (parçalar arası) | Integration | Yeniden plan |
| 7 | Reverb tail | Unit | Politikaya uyar |
| 8 | Compressor release | Unit | Yeni state |
| 9 | Duraklat + ileri-geri yarışı | Stress | Yarış yok |
| 10 | Dosya sonu hatası | Failure | Tanımlı iptal |
| 11 | 128 kanal / 384 kHz | Integration | Kabul |
| 12 | Uzun süreli çalma | Soak | Bellek sabit |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Crossfade süresi/eğrileri kanıtsız | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Efekt state geçiş politikası kaynakta yok | Orta | §7-8/9 ⚠️ |
| 3 | Ölçüm kanıtı yok | Yüksek | §9 |
| 4 | Ayrı ADR yok | Orta | ⚠️ VERIFICATION REQUIRED |
| 5 | Manager'ın liste politikası kapsam dışı | Düşük | §2.2 |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Gapless/pre-decode/crossfade/manager yapıları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/playback-gapless.md` | Kanıtlı |
| 2 | §4 verbatim (383 satır) | Aynı dosya | Kanıtlı |
| 3 | K3.5 Analiz & Çalma (27 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` | Kanıtlı (hedef) |
| 5 | RT kuralları | `_backup/.../k3-ses-motoru/CLAUDE.md` §1 | Kanıtlı |
| 6 | Crossfade süresi/eğrileri, efekt state politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/playback-gapless.md`
(salt-okunur, 383 satır) + `index.md` + `README.md` §Alt Katman K3.5.
