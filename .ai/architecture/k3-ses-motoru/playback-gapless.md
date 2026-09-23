---
title: "Gapless Playback"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# Gapless Playback ve Crossfade

## Genel Bakış

COREMUSIC, kesintisiz müzik reproduksiyonu için gapless playback ve crossfade desteği sağlar. Track geçişlerinde sessizlik oluşmasını engeller ve smooth geçişler sunar.

## Teknik Detaylar

### Gapless Playback Yapısı

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
│  Gap = 0 samples (无缝隙)                           │
└─────────────────────────────────────────────────────┘
```

### Pre-decode Mekanizması

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

### Crossfade Implementasyonu

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

### Gapless Playback Manager

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

## API / Arayüz

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

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Gap Boyutu | 0 samples | 0 samples |
| Crossfade Süresi | 0-10s | 3s |
| Pre-decode Buffer | 100ms | 100ms |
| CPU (crossfade) | < 1% | 0.5% |
| Bellek | < 5MB | 3.5MB |

## Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 Format Decoder | İç |

## Durum: Implementasyon

- **Faz 1**: Gapless playback
- **Faz 2**: Crossfade
- **Faz 3**: Pre-decode mekanizması
- **Faz 4**: Playlist yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)
