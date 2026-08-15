---
type: template
category: adr-audio
title: "CoreMusic — ADR Audio Template (DSP/Buffer/Hardware/Latency/ASIO)"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
usage: "Audio/DSP/Hardware ile ilgili ADR oluştururken bu dosyayı kopyalayın"
related:
  - "[[decisions/accepted/ADR-017-dsp-hardware-mode]]"
  - "[[decisions/accepted/ADR-019-per-os-neva-player]]"
  - "[[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
tags: [template, adr, audio, dsp, buffer, hardware, latency, asio, juce]
---

# CoreMusic — ADR Audio Template

**Bu dosya bir şablondur.** Audio, DSP, Hardware veya Latency ile ilgili ADR oluştururken bu dosyayı kopyalayın.

**Kullanım:** `cp .ai/.templates/adr-audio-template.md .ai/decisions/accepted/ADR-NNN-baslik.md`

---

## 📋 Audio ADR Kullanım Kılavuzu

### Audio ADR Ne Zaman Yazılır?

| Durum | Gerekli mi? | Açıklama |
|-------|-------------|----------|
| DSP algoritması değişikliği | ✅ Evet | Ses kalitesini etkiliyor |
| Buffer boyutu değişikliği | ✅ Evet | Gecikmeyi etkiliyor |
| Hardware seçimi | ✅ Evet | ADR-038 etkileniyor |
| ASIO/WASAPI değişikliği | ✅ Evet | Sürücü uyumluluğu |
| Latency optimizasyonu | ✅ Evet | Gerçek zamanlı performans |
| Codec entegrasyonu | ✅ Evet | FLAC, MP3, AAC desteği |
| Multi-channel desteği | ✅ Evet | 8.1 surround |
| Plugin entegrasyonu | ✅ Evet | VST3, AU desteği |

### Audio ADR Yazarken Dikkat

1. **ADR-017:** DSP hardware mode (XMOS, JUCE)
2. **ADR-019:** Per-OS Neva Player
3. **ADR-038:** 8.1 ses donanımı (PCM3168A + XMOS XU316)
4. **Zero-Allocation:** Audio thread'de heap allocation YASAK
5. **Lock-Free:** Audio thread'de mutex YASAK
6. **32-bit float:** Ses processing precision

---

## 📄 AUDIO ADR ŞABLONU

---

```yaml
---
type: decision
id: "NNN"
title: "ADR-NNN: [Audio Karar Başlığı]"
category: "audio"
status: "draft|active|frozen"
date: "YYYY-MM-DD"
updated: "YYYY-MM-DD"
authority: "Embedded Engineer"
governance: "Red Team • Human Mode • Truth Mode"
supersedes: null
version: 1.0.0
tags: [audio, dsp, buffer, hardware, latency, asio, juce]
latency-target: "< Xms"
sample-rate: 48000
bit-depth: "32-bit float"
channels: "8.1 surround"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-017-dsp-hardware-mode]]"
  - "[[decisions/accepted/ADR-019-per-os-neva-player]]"
  - "[[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]"
  - "[[architecture/06-audio/coremusic-audio-service]]"
  - "[[architecture/06-audio/audio-pipeline]]"
  - "[[architecture/06-audio/audio-platform-decision]]"
---
```

---

## 1. Executive Summary

[Audio kararının kısa özeti. Ne değişiyor? Hangi donanım etkileniyor?]

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | draft / active / frozen |
| **Versiyon** | 1.0.0 |
| **Oluşturma** | YYYY-MM-DD |
| **Son Güncelleme** | YYYY-MM-DD |
| **Otorite** | Embedded Engineer |
| **Hedef Gecikme** | < Xms |
| **Örnek Oranı** | 48000 Hz |
| **Bit Derinliği** | 32-bit float |
| **Kanal** | 8.1 surround |
| **Onay** | Red Team • Human Mode • Truth Mode |

---

## 3. Context

### 3.1 Audio Problemi

[Audio ile ilgili hangi sorun çözülüyor?]

### 3.2 Mevcut Audio Durumu

#### 3.2.1 Neva Engine Durumu

| Bileşen | Durum | Versiyon |
|---------|-------|---------|
| **Neva Engine** | ✅ Aktif | [versiyon] |
| **JUCE** | ✅ Kullanılıyor | 7+ |
| **ASIO SDK** | ✅ Kullanılıyor | 2.3 |
| **WASAPI** | ✅ Destekleniyor | — |
| **CoreAudio** | ✅ Destekleniyor | — |

#### 3.2.2 Donanım Durumu

| Donanım | Durum | Özellik |
|---------|-------|---------|
| **PCM3168A** | ✅ Seçildi | 8-kanal, 24-bit, 192kHz |
| **XMOS XU316** | ✅ Seçildi | Zero-latency DSP |
| **AK4458** | ⚠️ Opsiyonel | 8-kanal, 32-bit |
| **Class AB Amp** | ✅ Planlandı | 100W, ±42V DC |

### 3.3 İtici Güçler

| # | Güç | Açıklama | Kritiklik |
|---|-----|----------|-----------|
| 1 | [Güç 1] | [Açıklama] | Yüksek/Orta/Düşük |
| 2 | [Güç 2] | [Açıklama] | Yüksek/Orta/Düşük |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| **Zero-Allocation** | Audio thread'de malloc() yasak | ADR-017 |
| **Lock-Free** | Audio thread'de mutex yasak | ADR-017 |
| **32-bit float** | Processing precision | ADR-017 |
| **8.1 Surround** | Multi-channel zorunlu | ADR-038 |
| **PCM3168A** | DAC seçimi (PCM5122 REDDED) | ADR-038 |

### 3.5 Gecikme Hedefleri

| Hedef | Değer | Durum |
|-------|-------|-------|
| **ASIO Buffer** | 512 sample (10.67ms @ 48kHz) | ✅ |
| **DSP Latency** | < 5ms | ✅ |
| **End-to-End** | < 25ms | ✅ |
| **Mixer Latency** | < 2ms | ✅ |

### 3.6 Ekosistem Etkileşimi

| Etkilenen Alan | Etki | Açıklama |
|---------------|------|----------|
| **Audio Service** | Doğrudan | Port 9741/9742 |
| **Device Service** | Doğrudan | Bluetooth, WiFi |
| **Network Audio** | Doğrudan | WebRTC streaming |
| **Media Service** | Endirekt | FFmpeg decode |
| **Download Service** | Endirekt | FLAC/MP3/AAC |

---

## 4. Decision

### 4.1 Karar Bildirimi

**[Net audio karar cümlesi]**

### 4.2 Audio Kuralları

| # | Kural | Durum | İlgili ADR |
|---|-------|-------|------------|
| 1 | Zero-Allocation | ✅ Zorunlu | ADR-017 |
| 2 | Lock-Free | ✅ Zorunlu | ADR-017 |
| 3 | 32-bit float | ✅ Zorunlu | ADR-017 |
| 4 | 8.1 Surround | ✅ Zorunlu | ADR-038 |
| 5 | PCM3168A | ✅ Zorunlu | ADR-038 |
| 6 | XMOS XU316 | ✅ Zorunlu | ADR-038 |
| 7 | PCM5122 REDDEDİLDİ | ❌ Yasak | ADR-038 |

### 4.3 DSP Algoritması

```cpp
// C++ DSP Algoritması (ADR-017 uyumlu)
// Dosya: projects/neva-engine/source/DspProcessor.h

// ✅ DOĞRU — Zero-allocation, lock-free
class DspProcessor {
public:
    // Stack tahsisi serbest
    void processAudioBlock(
        float** outputChannels,
        const float** inputChannels,
        int numChannels,
        int numSamples
    ) noexcept {
        // ❌ YASAK: malloc(), new, delete
        // ❌ YASAK: mutex, lock_guard
        // ✅ SERBEST: Stack tahsisi, atomics, SIMD, sabit boyutlu tablolar
        
        for (int ch = 0; ch < numChannels; ++ch) {
            for (int i = 0; i < numSamples; ++i) {
                // DSP processing
                outputChannels[ch][i] = processSample(
                    inputChannels[ch][i], ch
                );
            }
        }
    }

private:
    // Member değişkenleri — heap allocation yok
    std::atomic<float> gain_[8]{};  // 8.1 surround
    float filterState_[8][4]{};     // 4-pole filter
};
```

### 4.4 Buffer Stratejisi

```cpp
// Ring Buffer (ADR-017 uyumlu)
// Dosya: projects/neva-engine/source/RingBuffer.h

// ✅ DOĞRU — Lock-free ring buffer
template<size_t Capacity>
class RingBuffer {
public:
    bool write(const float* data, size_t count) noexcept {
        const size_t writePos = writePos_.load(std::memory_order_relaxed);
        const size_t newWritePos = (writePos + count) % Capacity;
        
        if (newWritePos == readPos_.load(std::memory_order_acquire)) {
            return false; // Buffer full
        }
        
        // Copy data
        for (size_t i = 0; i < count; ++i) {
            buffer_[(writePos + i) % Capacity] = data[i];
        }
        
        writePos_.store(newWritePos, std::memory_order_release);
        return true;
    }
    
    bool read(float* data, size_t count) noexcept {
        const size_t readPos = readPos_.load(std::memory_order_relaxed);
        const size_t available = (writePos_.load(std::memory_order_acquire) 
                                  - readPos + Capacity) % Capacity;
        
        if (available < count) {
            return false; // Not enough data
        }
        
        for (size_t i = 0; i < count; ++i) {
            data[i] = buffer_[(readPos + i) % Capacity];
        }
        
        readPos_.store((readPos + count) % Capacity, std::memory_order_release);
        return true;
    }

private:
    float buffer_[Capacity]{};
    std::atomic<size_t> writePos_{0};
    std::atomic<size_t> readPos_{0};
};
```

### 4.5 ASIO Callback

```cpp
// ASIO Callback (ADR-017 uyumlu)
// Dosya: projects/neva-engine/source/AsioCallback.h

// ✅ DOĞRU — ASIO callback
class AsioCallback {
public:
    void bufferSwitch(
        long doubleBufferIndex,
        long processMeasurements
    ) noexcept {
        // ❌ YASAK: I/O blocking (file, network)
        // ❌ YASAK: Memory allocation
        // ❌ YASAK: Lock acquisition
        
        // ✅ DOĞRU: Process audio
        float* inputBuffers[8];   // 8.1 surround
        float* outputBuffers[8];
        
        // Get ASIO buffers
        for (int ch = 0; ch < 8; ++ch) {
            inputBuffers[ch] = asioBufferInfo_[ch].buffers[doubleBufferIndex];
            outputBuffers[ch] = asioBufferInfo_[ch + 8].buffers[doubleBufferIndex];
        }
        
        // Process audio block
        dspProcessor_.processAudioBlock(
            outputBuffers,
            const_cast<const float**>(inputBuffers),
            8,          // 8.1 surround
            bufferSize_ // 512 samples
        );
    }

private:
    DspProcessor dspProcessor_;
    ASIOBufferInfo asioBufferInfo_[16]; // 8 in + 8 out
    long bufferSize_ = 512;
};
```

### 4.6 Mixing Stratejisi

```cpp
// Multi-channel Mixer (8.1 Surround)
// Dosya: projects/neva-engine/source/Mixer.h

// ✅ DOĞRU — 8.1 surround mixer
class Mixer {
public:
    // Kanal sırası: FL, FR, C, LFE, SL, SR, SBL, SBR
    enum Channel {
        FRONT_LEFT = 0,
        FRONT_RIGHT = 1,
        CENTER = 2,
        LFE = 3,
        SURROUND_LEFT = 4,
        SURROUND_RIGHT = 5,
        SURROUND_BACK_LEFT = 6,
        SURROUND_BACK_RIGHT = 7,
        NUM_CHANNELS = 8
    };
    
    void mix(float** output, const float** input, int samples) noexcept {
        for (int i = 0; i < samples; ++i) {
            float mix[8] = {0.0f};
            
            // Downmix from 8.1 to stereo (if needed)
            mix[FRONT_LEFT] = input[FRONT_LEFT][i] * gain_[FRONT_LEFT];
            mix[FRONT_RIGHT] = input[FRONT_RIGHT][i] * gain_[FRONT_RIGHT];
            // ... other channels
            
            // Output
            for (int ch = 0; ch < 8; ++ch) {
                output[ch][i] = mix[ch];
            }
        }
    }

private:
    float gain_[8] = {1.0f, 1.0f, 1.0f, 1.0f, 1.0f, 1.0f, 1.0f, 1.0f};
};
```

---

## 5. Architecture

### 5.1 Audio Mimarisi

```
┌─────────────────────────────────────────────────┐
│              Application Layer                   │
│  ┌─────────────────────────────────────────────┐ │
│  │  Music Player UI (Web)                      │ │
│  │  • Playlist management                      │ │
│  │  • Transport controls                       │ │
│  │  • EQ visualization                         │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Audio Service (Port 9741/9742)      │
│  ┌─────────────────────────────────────────────┐ │
│  │  REST API (9741) / WebSocket (9742)         │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Neva Engine (C++20)                 │
│  ┌─────────────────────────────────────────────┐ │
│  │  • DSP Processor (Zero-alloc, Lock-free)    │ │
│  │  • Ring Buffer (8.1 channels)               │ │
│  │  • Mixer (Multi-channel)                    │ │
│  │  • EQ (31-band parametric)                  │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Driver Layer                        │
│  ┌─────────────────────────────────────────────┐ │
│  │  ASIO (Windows) / WASAPI / CoreAudio        │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Hardware Layer                      │
│  ┌─────────────────────────────────────────────┐ │
│  │  XMOS XU316 → PCM3168A (8-ch DAC)         │ │
│  │  → Class AB Amp → Speakers                  │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

### 5.2 DSP Chain

```
Input (8.1)
    │
    ▼
┌─────────────────────────────────────────────────┐
│  1. Pre-amp (Gain Control)                      │
│     • Per-channel gain                          │
│     • Master volume                             │
│     • Balance                                   │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  2. EQ (31-band Parametric)                     │
│     • 31 bands per channel                      │
│     • Parametric EQ                             │
│     • Preset support                            │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  3. Dynamics                                    │
│     • Compressor                                │
│     • Limiter                                   │
│     • Gate                                      │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  4. Spatial Effects                             │
│     • Reverb                                    │
│     • Delay                                     │
│     • Chorus                                    │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  5. Output Stage                                │
│     • 32-bit float → 24-bit PCM                 │
│     • Dithering                                 │
│     • Clipping protection                       │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
                 Output (8.1)
```

### 5.3 Hardware Signal Flow

```
┌──────────────────┐
│  USB Audio       │
│  (XMOS XU316)    │
└────────┬─────────┘
         │ I2S (8 channels)
         ▼
┌──────────────────┐
│  PCM3168A        │
│  (8-ch DAC)      │
│  24-bit/192kHz   │
└────────┬─────────┘
         │ Analog (8 channels)
         ▼
┌──────────────────┐
│  Class AB Amp    │
│  100W × 8        │
│  ±42V DC         │
└────────┬─────────┘
         │ Amplified
         ▼
┌──────────────────┐
│  Speakers        │
│  (8.1 setup)     │
└──────────────────┘
```

---

## 6. Alternatives Considered

### 6.1 Alternatif 1: PCM5122 Kullanımı (Reddedilen)

**Açıklama:** 2-kanal PCM5122 DAC kullanımı

**Avantajlar:**
- Daha ucuz
- Daha basit tasarım

**Dezavantajlar:**
- Sadece 2 kanal (8.1 desteklemiyor)
- ADR-038 ile çelişiyor

**Neden Reddedildi:** ADR-038 H001 REDDİLDİ — PCM5122 2 kanal destekler, 8.1 surround için PCM3168A zorunlu

### 6.2 Alternatif 2: WASAPI Exclusive Mode (Reddedilen)

**Açıklama:** WASAPI exclusive mode kullanımı

**Avantajlar:**
- Düşük gecikme
- Driver bağımsızlığı

**Dezavantajlar:**
- Sadece Windows
- ASIO kadar güvenilir değil
- ADR-017 ile çelişiyor

**Neden Reddedildi:** ADR-017 ASIO birincil sürücü, WASAPI fallback

### 6.3 Karar Matrisi

| Kriter | Ağırlık | PCM5122 | PCM3168A | WASAPI | ASIO |
|--------|---------|---------|----------|--------|------|
| Kanal Desteği | %30 | 2 | 8 | 8 | 8 |
| Gecikme | %25 | Orta | Düşük | Orta | Düşük |
| ADR Uyumu | %25 | ❌ | ✅ | ❌ | ✅ |
| Maliyet | %20 | Düşük | Yüksek | — | — |

---

## 7. Consequences

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki |
|---|-------|------|
| 1 | [Olumlu sonuç 1] | Yüksek/Orta/Düşük |
| 2 | [Olumlu sonuç 2] | Yüksek/Orta/Düşük |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk | Mitigation |
|---|-------|------|------------|
| 1 | [Olumsuz sonuç 1] | Yüksek/Orta/Düşük | [Çözüm] |
| 2 | [Olumsuz sonuç 2] | Yüksek/Orta/Düşük | [Çözüm] |

---

## 8. Testing Strategy

### 8.1 Audio Test Kapsamı

| Test Türü | Hedef | Araç |
|-----------|-------|------|
| **Unit Test** | %80+ | Google Test |
| **Integration Test** | %70+ | Google Test |
| **Latency Test** | %100 | ASIO diagnostics |
| **Listening Test** | %100 | Human evaluation |

### 8.2 Test Senaryoları

| # | Senaryo | Türü | Beklenen Sonuç |
|---|---------|------|----------------|
| 1 | Buffer overflow | Unit | Başarılı |
| 2 | Latency ölçümü | Integration | < 25ms |
| 3 | 8.1 surround test | Integration | Tüm kanallar |
| 4 | DSP processing | Unit | Sıfır hata |
| 5 | ASIO callback | Integration | Zamanlama uyumu |

### 8.3 Test Komutları

```bash
# C++ Neva Engine Testleri
cd projects/neva-engine && cmake --build build
cd projects/neva-engine && ./build/tests

# ASIO Latency Test
cd projects/neva-engine && ./build/latency_test

# 8.1 Surround Test
cd projects/neva-engine && ./build/surround_test
```

---

## 9. Performance Metrics

### 9.1 Gecikme Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| **ASIO Buffer** | 512 sample | [değer] |
| **DSP Latency** | < 5ms | [değer] |
| **End-to-End** | < 25ms | [değer] |
| **Mixer Latency** | < 2ms | [değer] |

### 9.2 CPU Kullanımı

| İşlem | CPU | Kabul Edilebilir mi? |
|-------|-----|---------------------|
| **DSP Processing** | [%] | ✅ < %30 |
| **Mixing** | [%] | ✅ < %10 |
| **EQ** | [%] | ✅ < %20 |

### 9.3 Bellek Kullanımı

| Bileşen | Bellek | Kabul Edilebilir mi? |
|---------|--------|---------------------|
| **Ring Buffer** | [MB] | ✅ < 10MB |
| **DSP State** | [KB] | ✅ < 1MB |
| **EQ Tables** | [KB] | ✅ < 1MB |

---

## 10. Rollback Plan

| Senaryo | Tetikleyici | Geri Alma Adımları |
|---------|-------------|-------------------|
| ASIO failure | Driver hatası | 1. WASAPI'ye geç 2. ASIO'yu yeniden yükle |
| DSP crash | Memory hatası | 1. Engine'i restart et 2. Log'ları kontrol et |
| Latency spike | Performans | 1. Buffer'ı artır 2. DSP'yi basitleştir |

---

## 11. Related Decisions

| ADR | Başlık | İlişki |
|-----|--------|--------|
| ADR-017 | DSP Hardware Mode | Ana kural |
| ADR-019 | Per-OS Neva Player | OS desteği |
| ADR-038 | 8.1 Sound Card | Donanım seçimi |

---

## 12. Glossary

| Terim | Tanım |
|-------|-------|
| **DSP** | Digital Signal Processing |
| **ASIO** | Audio Stream Input/Output |
| **WASAPI** | Windows Audio Session API |
| **I2S** | Inter-IC Sound (digital audio) |
| **TDM** | Time Division Multiplexing |
| **PCM3168A** | 8-kanal 24-bit DAC |
| **XMOS XU316** | DSP denetleyicisi |
| **Ring Buffer** | Dairesel tampon |
| **Zero-Allocation** | Heap allocation yok |
| **Lock-Free** | Kilitleme mekanizması yok |

---

## 13. Edge Cases

| Durum | Belirti | Çözüm |
|-------|---------|-------|
| ASIO device loss | USB kopması | WASAPI fallback |
| Buffer underrun | Ses kesilmesi | Buffer boyutunu artır |
| DSP overflow | distortion | Clipping koruması |
| Multi-room sync | Gecikme farkı | PTP clock sync |

---

## 14. Warnings

> [!WARNING]
> **PCM5122 REDDİLDİ:** PCM5122 ile 8.1 surround YAPILAMAZ (sadece 2 kanal). ADR-038 H001.

> [!WARNING]
> **Zero-Allocation:** Audio thread'de `malloc()`, `new` KESİNLİKLE yasaktır (ADR-017).

> [!WARNING]
> **Lock-Free:** Audio thread'de `mutex`, `lock_guard` KESİNLİKLE yasaktır (ADR-017).

---

## 15. Limitations

| # | Sınırlama | Etki | Gelecek Çözüm |
|---|-----------|------|---------------|
| 1 | ASIO sadece Windows | Orta | CoreAudio (macOS) |
| 2 | 8.1 surround karmaşık | Orta | 7.1 fallback |
| 3 | XMOS bağımlılığı | Yüksek | DSP firmware update |

---

## 16. Dependencies

| Bağımlılık | Versiyon | Kullanım |
|------------|---------|---------|
| C++20 | 20 | Programlama |
| JUCE | 7+ | Audio framework |
| ASIO SDK | 2.3 | Sürücü |
| XMOS XU316 | — | DSP |
| PCM3168A | — | DAC |

---

## 17. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v1.1 | Multi-room support | 2026-Q4 |
| v2.0 | VST3 plugin hosting | 2027-Q1 |
| v2.1 | AI-powered EQ | 2027-Q2 |

---

## 18. Related Documents

| Dosya | Amaç |
|-------|------|
| [[architecture/06-audio/coremusic-audio-service]] | Audio service doc |
| [[architecture/06-audio/audio-pipeline]] | Audio pipeline |
| [[architecture/06-audio/audio-platform-decision]] | Platform decision |
| [[electronic/audio-interface-design]] | Hardware design |
| [[electronic/amplifier-design]] | Amplifier design |

---

## 19. Cross References

```
ADR-NNN (Audio)
    │
    ├─► decisions/accepted/ADR-017-dsp-hardware-mode (DSP)
    │
    ├─► decisions/accepted/ADR-019-per-os-neva-player (OS)
    │
    ├─► decisions/accepted/ADR-038-8.1-sound-card-chip-selection (Hardware)
    │
    ├─► architecture/06-audio/coremusic-audio-service (Service)
    │
    ├─► architecture/06-audio/audio-pipeline (Pipeline)
    │
    └─► electronic/audio-interface-design (Hardware Design)
```

---

## 20. Approval

| Rol | Kişi | Onay | Tarih |
|-----|------|------|-------|
| Embedded Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Audio Hardware Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Vault Steward | [İsim] | ✅/❌ | YYYY-MM-DD |

---

*CoreMusic ADR Audio Template v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
