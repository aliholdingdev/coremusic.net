---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K3 Ses İşleme Motoru Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K3: Ses İşleme Motoru (Neva Engine)

**Katman:** K3 (Ses Motoru)
**Kapsam:** Neva Engine, DSP Chain, Mixer, EQ, Reverb, Compressor, Analyzer
**Sorumlu Agent:** Embedded Engineer
**Bileşen Sayısı:** 50

---

## 1. Genel Bakış

K3 katmanı, CoreMusic'in kalbi olan Neva Engine'i içerir. Bu katman, dijital sinyal işleme (DSP) zincirini yönetir ve profesyonel ses kalitesinde işleme sağlar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Zero-Allocation** | Audio thread'de heap allocation yasak |
| **Lock-Free** | Audio thread'de mutex yasak |
| **Noexcept** | ASIO callback noexcept zorunlu |
| **SIMD** | SSE2/AVX2/NEON optimizasyonu |
| **Real-Time** | Gecikme <10ms |

---

## 2. Neva Engine Mimarisi

### 2.1 Ana Bileşenler

```
Neva Engine
├── DSP Chain (Kanal başına)
│   ├── 31-Band Parametric EQ
│   ├── Compressor
│   ├── Reverb (4 mod)
│   ├── Limiter
│   └── Analyzer
├── Mixer
│   ├── 8.1 Channel Routing
│   ├── Volume Control
│   ├── Pan Control
│   └── Mute/Solo
├── Crossover
│   ├── Linkwitz-Riley 4th Order
│   └── Bass Management
└── Master Output
    ├── Dithering
    ├── Sample Rate Conversion
    └── Bit-Perfect Output
```

### 2.2 DSP Chain Akışı

```
Input (32-bit float)
    → Gain Staging
    → 31-Band Parametric EQ
    → Compressor
    → Reverb (mix control)
    → Limiter
    → Output Gain
    → Dithering (optional)
Output (32-bit float)
```

---

## 3. 31-Band Parametric EQ

### 3.1 EQ Band Frekansları

| Band | Frekans | Q Factor | Aralık |
|------|---------|----------|--------|
| 1 | 20 Hz | 0.7 | Sub-bass |
| 2 | 25 Hz | 0.7 | Sub-bass |
| 3 | 31.5 Hz | 0.7 | Sub-bass |
| 4 | 40 Hz | 0.7 | Bass |
| 5 | 50 Hz | 0.7 | Bass |
| 6 | 63 Hz | 0.7 | Bass |
| 7 | 80 Hz | 0.7 | Bass |
| 8 | 100 Hz | 0.7 | Lower mid |
| 9 | 125 Hz | 0.7 | Lower mid |
| 10 | 160 Hz | 0.7 | Lower mid |
| 11 | 200 Hz | 0.7 | Mid |
| 12 | 250 Hz | 0.7 | Mid |
| 13 | 315 Hz | 0.7 | Mid |
| 14 | 400 Hz | 0.7 | Mid |
| 15 | 500 Hz | 0.7 | Mid |
| 16 | 630 Hz | 0.7 | Upper mid |
| 17 | 800 Hz | 0.7 | Upper mid |
| 18 | 1 kHz | 0.7 | Upper mid |
| 19 | 1.25 kHz | 0.7 | Presence |
| 20 | 1.6 kHz | 0.7 | Presence |
| 21 | 2 kHz | 0.7 | Presence |
| 22 | 2.5 kHz | 0.7 | Presence |
| 23 | 3.15 kHz | 0.7 | Brilliance |
| 24 | 4 kHz | 0.7 | Brilliance |
| 25 | 5 kHz | 0.7 | Brilliance |
| 26 | 6.3 kHz | 0.7 | Brilliance |
| 27 | 8 kHz | 0.7 | Air |
| 28 | 10 kHz | 0.7 | Air |
| 29 | 12.5 kHz | 0.7 | Air |
| 30 | 16 kHz | 0.7 | Air |
| 31 | 20 kHz | 0.7 | Air |

### 3.2 Biquad Filtre Koefisyonları

```cpp
// peakingEQ coefficients
struct EQCoefficients {
    float b0, b1, b2, a1, a2;
};

EQCoefficients calculatePeakingEQ(
    float sampleRate,
    float frequency,
    float gain,
    float Q
) {
    float A = powf(10.0f, gain / 40.0f);
    float w0 = 2.0f * M_PI * frequency / sampleRate;
    float alpha = sinf(w0) / (2.0f * Q);

    float b0 = 1.0f + alpha * A;
    float b1 = -2.0f * cosf(w0);
    float b2 = 1.0f - alpha * A;
    float a0 = 1.0f + alpha / A;
    float a1 = -2.0f * cosf(w0);
    float a2 = 1.0f - alpha / A;

    // Normalize
    b0 /= a0; b1 /= a0; b2 /= a0;
    a1 /= a0; a2 /= a0;

    return { b0, b1, b2, a1, a2 };
}
```

---

## 4. Reverb (4 Mod)

| Mod | Algoritma | Kullanım |
|-----|-----------|----------|
| Geniş Konser | FDN 16-line | Büyük mekan |
| Düğün Salonu | Plate reverb | Orta mekan |
| Oda | Room algorithm | Küçük mekan |
| Stüdyo | Algorithmic + convolution | Profesyonel |

### 4.1 FDN Reverb Yapısı

```cpp
// 16-line FDN Reverb
class FDNReverb {
    static constexpr int NUM_LINES = 16;
    static constexpr int MAX_DELAY = 48000; // 1 second @ 48kHz

    float _delayLines[NUM_LINES][MAX_DELAY];
    float _feedbackMatrix[NUM_LINES][NUM_LINES]; // Householder
    float _absorption[NUM_LINES]; // Frequency-dependent
    float _modulation[NUM_LINES]; // Lexicon-style

    void process(float* buffer, int samples) noexcept {
        for (int i = 0; i < samples; ++i) {
            float input = buffer[i];
            float output = 0.0f;

            for (int line = 0; line < NUM_LINES; ++line) {
                // Read from delay line
                float delayed = _delayLines[line][_readPos[line]];

                // Apply absorption (IIR)
                delayed *= _absorption[line];

                // Modulation (smooth random)
                delayed *= (1.0f + 0.01f * sinf(_modPhase[line]));

                // Accumulate output
                output += delayed;

                // Write to delay line
                _delayLines[line][_writePos[line]] =
                    input + feedbackSum(line);
            }

            buffer[i] = output / NUM_LINES;
        }
    }
};
```

---

## 5. Compressor

### 5.1 Compressor Parametreleri

| Parametre | Varsayılan | Aralık |
|-----------|-----------|--------|
| Threshold | -20 dB | -60 to 0 dB |
| Ratio | 4:1 | 1:1 to 20:1 |
| Attack | 10 ms | 0.1 to 100 ms |
| Release | 100 ms | 10 to 1000 ms |
| Knee | 6 dB | 0 to 12 dB |
| Makeup Gain | 0 dB | 0 to 24 dB |

### 5.2 Sidechain Detection

```cpp
// Peak detector with attack/release
class SidechainDetector {
    float _level = 0.0f;
    float _attackCoeff;
    float _releaseCoeff;

    void setAttack(float attackMs, float sampleRate) {
        _attackCoeff = expf(-1.0f / (attackMs * 0.001f * sampleRate));
    }

    void setRelease(float releaseMs, float sampleRate) {
        _releaseCoeff = expf(-1.0f / (releaseMs * 0.001f * sampleRate));
    }

    float process(float input) noexcept {
        float absInput = fabsf(input);
        if (absInput > _level) {
            _level = _attackCoeff * _level + (1.0f - _attackCoeff) * absInput;
        } else {
            _level = _releaseCoeff * _level + (1.0f - _releaseCoeff) * absInput;
        }
        return _level;
    }
};
```

---

## 6. Limiter

### 6.1 True Peak Limiter

| Parametre | Değer |
|-----------|-------|
| Ceiling | -0.3 dBTP |
| Release | Auto (ISP-aware) |
| Oversampling | 4x |

---

## 7. Mixer (8.1 Surround)

### 7.1 Kanal Routing Matrisi

```
Input Channel → Output Channel:
  CH1 (Front L)  → Output 1 (Front L)
  CH2 (Front R)  → Output 2 (Front R)
  CH3 (Center)   → Output 3 (Center)
  CH4 (LFE)      → Output 4 (Subwoofer)
  CH5 (Sur L)    → Output 5 (Surround L)
  CH6 (Sur R)    → Output 6 (Surround R)
  CH7 (Rear L)   → Output 7 (Rear L)
  CH8 (Rear R)   → Output 8 (Rear R)
```

### 7.2 Bass Management

```
Crossover: Linkwitz-Riley 4th Order
Frequency: 80Hz

Low-pass → Subwoofer (LFE)
High-pass → Main speakers (Front, Center, Surround, Rear)
```

---

## 8. Analyzer

### 8.1 Spectrum Analyzer

| Parametre | Değer |
|-----------|-------|
| FFT Size | 4096 |
| Window | Hann |
| Overlap | 50% |
| Bands | 31 (1/3 octave) |
| Update Rate | 30 fps |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k3-ses-motoru/README.md` | Bu dosya |
| `architecture/k3-ses-motoru/dsp-chain.md` | DSP zincir detayı |
| `architecture/k3-ses-motoru/31-band-eq.md` | EQ detayı |
| `architecture/k3-ses-motoru/reverb-modes.md` | Reverb modları |
| `architecture/k3-ses-motoru/mixer-architecture.md` | Mixer mimarisi |
| `architecture/k3-ses-motoru/ring-buffer.md` | Lock-free ring buffer |
| `architecture/k3-ses-motoru/zero-allocation.md` | Zero-allocation kuralları |
| `architecture/k3-ses-motoru/bit-perfect.md` | Bit-perfect aktarım |

---

## 10. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-025 | 31-band parametrik EQ |
| ADR-062 | DSP Pipeline Architecture |

---

## Alt Katman Şeması (K3.a.b.c)

> **Zincir Kuralı (kanıt: dsp-chain.md L16 · README §2.2 L68):** DSP zinciri EQ → Compressor → Reverb → Limiter sırasıyla akar. Crossover (Linkwitz-Riley 4. derece, README §2.1 L59) zincirin kardeş bileşenidir, aşama değildir.
> **Kapsam:** 18 Markdown dosyası · yalnız diskteki H2/H3 başlıkları + index.md Dosya Haritası tablo satırları · 210 başlık − 25 hariç + 15 tablo satırı = 200 kanıtlı yaprak · 0 uydurma değer.

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K3.1 | Neva Engine Çekirdeği | 3 | 12 | neva-engine-core.md |
| K3.2 | DSP Zinciri | 3 | 11 | dsp-chain.md |
| K3.3 | Parametrik EQ | 3 | 9 | eq-parametric.md |
| K3.4 | Dinamik & Efektler | 3 | 32 | dynamics-compressor.md · effects-reverb.md · effects-chorus-delay.md |
| K3.5 | Analiz & Çalma | 3 | 27 | analysis-spectrum.md · playback-gapless.md · stream-buffer.md |
| K3.6 | Format & Dönüşüm | 3 | 30 | format-decoder.md · sample-rate-conversion.md · bit-depth-conversion.md |
| K3.7 | Kanal & Mixer | 3 | 29 | channel-processing.md · mixer-routing.md · surround-decoder.md |
| K3.8 | Katman İndeksi | 2 | 28 | index.md |
| K3.9 | Taşıyıcı Dokümantasyon | 2 | 22 | README.md |
| **TOPLAM** | | **25** | **200** | 18 dosya |

### K3.1 — Neva Engine Çekirdeği

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.1.a** | **Mimari & İlkeler** | neva-engine-core.md · L10–L73 |
| K3.1.a.1 | Genel Bakış | neva-engine-core.md · L10 |
| K3.1.a.2 | Teknik Detaylar | neva-engine-core.md · L14 |
| K3.1.a.3 | Neva Engine Mimarisi | neva-engine-core.md · L16 |
| K3.1.a.4 | Real-Time Safe İlkeler | neva-engine-core.md · L46 |
| K3.1.a.5 | Lock-Free Veri Yapıları | neva-engine-core.md · L73 |
| **K3.1.b** | **Çalışma Zamanı Yapıları** | neva-engine-core.md · L113–L238 |
| K3.1.b.1 | Engine State Machine | neva-engine-core.md · L113 |
| K3.1.b.2 | Buffer Pool Sistemi | neva-engine-core.md · L174 |
| K3.1.b.3 | SIMD Optimizasyonu | neva-engine-core.md · L213 |
| K3.1.b.4 | Thread Yönetimi | neva-engine-core.md · L238 |
| **K3.1.c** | **Arayüz & Metrikler** | neva-engine-core.md · L278–L356 |
| K3.1.c.1 | API / Arayüz | neva-engine-core.md · L278 |
| K3.1.c.2 | Performans Metrikleri | neva-engine-core.md · L346 |
| K3.1.c.3 | Bağımlılıklar | neva-engine-core.md · L356 |

### K3.2 — DSP Zinciri

*Zincir sırası: EQ (K3.3) → Compressor (K3.4.a) → Reverb (K3.4.b) → Limiter (K3.4.a · dynamics-compressor.md L148); Crossover kardeş bileşendir.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.2.a** | **Pipeline Tasarımı** | dsp-chain.md · L10–L68 |
| K3.2.a.1 | Genel Bakış | dsp-chain.md · L10 |
| K3.2.a.2 | Teknik Detaylar | dsp-chain.md · L14 |
| K3.2.a.3 | 15-Aşamalı Pipeline | dsp-chain.md · L16 |
| K3.2.a.4 | DSP Stage Arabirimi | dsp-chain.md · L41 |
| K3.2.a.5 | Biquad Filtre Yapısı | dsp-chain.md · L68 |
| **K3.2.b** | **Aşamalar & Yönetim** | dsp-chain.md · L106–L283 |
| K3.2.b.1 | Stage Implementasyonları | dsp-chain.md · L106 |
| K3.2.b.2 | Ring Buffer (Inter-Stage) | dsp-chain.md · L242 |
| K3.2.b.3 | Pipeline Yönetimi | dsp-chain.md · L283 |
| **K3.2.c** | **Arayüz & Metrikler** | dsp-chain.md · L336–L383 |
| K3.2.c.1 | API / Arayüz | dsp-chain.md · L336 |
| K3.2.c.2 | Performans Metrikleri | dsp-chain.md · L373 |
| K3.2.c.3 | Bağımlılıklar | dsp-chain.md · L383 |

### K3.3 — Parametrik EQ

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.3.a** | **Filtre Çekirdeği** | eq-parametric.md · L10–L34 |
| K3.3.a.1 | Genel Bakış | eq-parametric.md · L10 |
| K3.3.a.2 | Teknik Detaylar | eq-parametric.md · L14 |
| K3.3.a.3 | EQ Bant Yapısı | eq-parametric.md · L16 |
| K3.3.a.4 | Biquad Filtre Tipleri | eq-parametric.md · L34 |
| **K3.3.b** | **31-Bant & Preset** | eq-parametric.md · L108–L258 |
| K3.3.b.1 | 31-Bant EQ Implementasyonu | eq-parametric.md · L108 |
| K3.3.b.2 | Preset Sistemi | eq-parametric.md · L258 |
| **K3.3.c** | **Arayüz & Metrikler** | eq-parametric.md · L290–L337 |
| K3.3.c.1 | API / Arayüz | eq-parametric.md · L290 |
| K3.3.c.2 | Performans Metrikleri | eq-parametric.md · L327 |
| K3.3.c.3 | Bağımlılıklar | eq-parametric.md · L337 |

### K3.4 — Dinamik & Efektler

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.4.a** | **Dinamik İşlemci** | dynamics-compressor.md · L10–L356 |
| K3.4.a.1 | Genel Bakış | dynamics-compressor.md · L10 |
| K3.4.a.2 | Teknik Detaylar | dynamics-compressor.md · L14 |
| K3.4.a.3 | Dinamik İşlemci Mimarisi | dynamics-compressor.md · L16 |
| K3.4.a.4 | Compressor Implementasyonu | dynamics-compressor.md · L32 |
| K3.4.a.5 | Limiter Implementasyonu | dynamics-compressor.md · L148 |
| K3.4.a.6 | Expander Implementasyonu | dynamics-compressor.md · L199 |
| K3.4.a.7 | Dinamik İşlemci Birleşimi | dynamics-compressor.md · L259 |
| K3.4.a.8 | API / Arayüz | dynamics-compressor.md · L302 |
| K3.4.a.9 | Performans Metrikleri | dynamics-compressor.md · L345 |
| K3.4.a.10 | Bağımlılıklar | dynamics-compressor.md · L356 |
| **K3.4.b** | **Reverb** | effects-reverb.md · L10–L323 |
| K3.4.b.1 | Genel Bakış | effects-reverb.md · L10 |
| K3.4.b.2 | Teknik Detaylar | effects-reverb.md · L14 |
| K3.4.b.3 | Freeverb Mimarisi | effects-reverb.md · L16 |
| K3.4.b.4 | Comb Filtre Implementasyonu | effects-reverb.md · L32 |
| K3.4.b.5 | Allpass Filtre Implementasyonu | effects-reverb.md · L76 |
| K3.4.b.6 | Delay Line (Ring Buffer) | effects-reverb.md · L108 |
| K3.4.b.7 | Freeverb Ana Yapı | effects-reverb.md · L143 |
| K3.4.b.8 | Oda Modelleme | effects-reverb.md · L233 |
| K3.4.b.9 | API / Arayüz | effects-reverb.md · L277 |
| K3.4.b.10 | Performans Metrikleri | effects-reverb.md · L313 |
| K3.4.b.11 | Bağımlılıklar | effects-reverb.md · L323 |
| **K3.4.c** | **Modülasyon Efektleri** | effects-chorus-delay.md · L10–L379 |
| K3.4.c.1 | Genel Bakış | effects-chorus-delay.md · L10 |
| K3.4.c.2 | Teknik Detaylar | effects-chorus-delay.md · L14 |
| K3.4.c.3 | Efekt Genel Yapısı | effects-chorus-delay.md · L16 |
| K3.4.c.4 | LFO Implementasyonu | effects-chorus-delay.md · L31 |
| K3.4.c.5 | Chorus Efekti | effects-chorus-delay.md · L93 |
| K3.4.c.6 | Delay Efekti | effects-chorus-delay.md · L149 |
| K3.4.c.7 | Flanger Efekti | effects-chorus-delay.md · L204 |
| K3.4.c.8 | Phaser Efekti | effects-chorus-delay.md · L258 |
| K3.4.c.9 | API / Arayüz | effects-chorus-delay.md · L322 |
| K3.4.c.10 | Performans Metrikleri | effects-chorus-delay.md · L371 |
| K3.4.c.11 | Bağımlılıklar | effects-chorus-delay.md · L379 |

### K3.5 — Analiz & Çalma

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.5.a** | **Spektrum Analiz** | analysis-spectrum.md · L10–L409 |
| K3.5.a.1 | Genel Bakış | analysis-spectrum.md · L10 |
| K3.5.a.2 | Teknik Detaylar | analysis-spectrum.md · L14 |
| K3.5.a.3 | FFT Algoritması | analysis-spectrum.md · L16 |
| K3.5.a.4 | Spectrum Analyzer | analysis-spectrum.md · L108 |
| K3.5.a.5 | Frequency Response | analysis-spectrum.md · L237 |
| K3.5.a.6 | Waterfall Display | analysis-spectrum.md · L301 |
| K3.5.a.7 | API / Arayüz | analysis-spectrum.md · L362 |
| K3.5.a.8 | Performans Metrikleri | analysis-spectrum.md · L399 |
| K3.5.a.9 | Bağımlılıklar | analysis-spectrum.md · L409 |
| **K3.5.b** | **Gapless Çalma** | playback-gapless.md · L10–L369 |
| K3.5.b.1 | Genel Bakış | playback-gapless.md · L10 |
| K3.5.b.2 | Teknik Detaylar | playback-gapless.md · L14 |
| K3.5.b.3 | Gapless Playback Yapısı | playback-gapless.md · L16 |
| K3.5.b.4 | Pre-decode Mekanizması | playback-gapless.md · L32 |
| K3.5.b.5 | Crossfade Implementasyonu | playback-gapless.md · L88 |
| K3.5.b.6 | Gapless Playback Manager | playback-gapless.md · L152 |
| K3.5.b.7 | API / Arayüz | playback-gapless.md · L314 |
| K3.5.b.8 | Performans Metrikleri | playback-gapless.md · L359 |
| K3.5.b.9 | Bağımlılıklar | playback-gapless.md · L369 |
| **K3.5.c** | **Stream Tampon** | stream-buffer.md · L10–L364 |
| K3.5.c.1 | Genel Bakış | stream-buffer.md · L10 |
| K3.5.c.2 | Teknik Detaylar | stream-buffer.md · L14 |
| K3.5.c.3 | Jitter Buffer Yapısı | stream-buffer.md · L16 |
| K3.5.c.4 | Adaptif Jitter Buffer | stream-buffer.md · L32 |
| K3.5.c.5 | Network Stream Handler | stream-buffer.md · L185 |
| K3.5.c.6 | Buffer Pool | stream-buffer.md · L252 |
| K3.5.c.7 | API / Arayüz | stream-buffer.md · L315 |
| K3.5.c.8 | Performans Metrikleri | stream-buffer.md · L354 |
| K3.5.c.9 | Bağımlılıklar | stream-buffer.md · L364 |

### K3.6 — Format & Dönüşüm

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.6.a** | **Codec Çözücüleri** | format-decoder.md · L10–L368 |
| K3.6.a.1 | Genel Bakış | format-decoder.md · L10 |
| K3.6.a.2 | Teknik Detaylar | format-decoder.md · L14 |
| K3.6.a.3 | Format Karşılaştırması | format-decoder.md · L16 |
| K3.6.a.4 | FLAC Decoder | format-decoder.md · L32 |
| K3.6.a.5 | MP3 Decoder | format-decoder.md · L146 |
| K3.6.a.6 | AAC Decoder | format-decoder.md · L199 |
| K3.6.a.7 | DSD Decoder | format-decoder.md · L241 |
| K3.6.a.8 | Format Otomatik Algılama | format-decoder.md · L282 |
| K3.6.a.9 | API / Arayüz | format-decoder.md · L315 |
| K3.6.a.10 | Performans Metrikleri | format-decoder.md · L360 |
| K3.6.a.11 | Bağımlılıklar | format-decoder.md · L368 |
| **K3.6.b** | **Örnekleme Dönüşümü** | sample-rate-conversion.md · L10–L276 |
| K3.6.b.1 | Genel Bakış | sample-rate-conversion.md · L10 |
| K3.6.b.2 | Teknik Detaylar | sample-rate-conversion.md · L14 |
| K3.6.b.3 | SRC Algoritması | sample-rate-conversion.md · L16 |
| K3.6.b.4 | Asenkron SRC | sample-rate-conversion.md · L32 |
| K3.6.b.5 | Zincir SRC (Multi-stage) | sample-rate-conversion.md · L152 |
| K3.6.b.6 | Quality Presetleri | sample-rate-conversion.md · L200 |
| K3.6.b.7 | API / Arayüz | sample-rate-conversion.md · L231 |
| K3.6.b.8 | Performans Metrikleri | sample-rate-conversion.md · L267 |
| K3.6.b.9 | Bağımlılıklar | sample-rate-conversion.md · L276 |
| **K3.6.c** | **Bit Derinliği** | bit-depth-conversion.md · L10–L321 |
| K3.6.c.1 | Genel Bakış | bit-depth-conversion.md · L10 |
| K3.6.c.2 | Teknik Detaylar | bit-depth-conversion.md · L14 |
| K3.6.c.3 | Bit Derinliği Dönüşüm Tablosu | bit-depth-conversion.md · L16 |
| K3.6.c.4 | Dithering Implementasyonu | bit-depth-conversion.md · L32 |
| K3.6.c.5 | Noise Shaping | bit-depth-conversion.md · L107 |
| K3.6.c.6 | Bit Depth Converter | bit-depth-conversion.md · L159 |
| K3.6.c.7 | Truncation vs Rounding | bit-depth-conversion.md · L258 |
| K3.6.c.8 | API / Arayüz | bit-depth-conversion.md · L280 |
| K3.6.c.9 | Performans Metrikleri | bit-depth-conversion.md · L311 |
| K3.6.c.10 | Bağımlılıklar | bit-depth-conversion.md · L321 |

### K3.7 — Kanal & Mixer

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.7.a** | **Kanal İşleme** | channel-processing.md · L10–L328 |
| K3.7.a.1 | Genel Bakış | channel-processing.md · L10 |
| K3.7.a.2 | Teknik Detaylar | channel-processing.md · L14 |
| K3.7.a.3 | Kanal Haritalama Tablosu | channel-processing.md · L16 |
| K3.7.a.4 | Mono/Stereo Dönüşümü | channel-processing.md · L37 |
| K3.7.a.5 | Kanal Eşleme Matrisi | channel-processing.md · L85 |
| K3.7.a.6 | Downmix Implementasyonu | channel-processing.md · L158 |
| K3.7.a.7 | Upmix Implementasyonu | channel-processing.md · L189 |
| K3.7.a.8 | API / Arayüz | channel-processing.md · L278 |
| K3.7.a.9 | Performans Metrikleri | channel-processing.md · L318 |
| K3.7.a.10 | Bağımlılıklar | channel-processing.md · L328 |
| **K3.7.b** | **Mixer & Routing** | mixer-routing.md · L10–L368 |
| K3.7.b.1 | Genel Bakış | mixer-routing.md · L10 |
| K3.7.b.2 | Teknik Detaylar | mixer-routing.md · L14 |
| K3.7.b.3 | Bus Mimarisi | mixer-routing.md · L16 |
| K3.7.b.4 | Channel Strip | mixer-routing.md · L34 |
| K3.7.b.5 | Bus Implementasyonu | mixer-routing.md · L111 |
| K3.7.b.6 | Routing Matrix | mixer-routing.md · L174 |
| K3.7.b.7 | Mixer Manager | mixer-routing.md · L244 |
| K3.7.b.8 | API / Arayüz | mixer-routing.md · L316 |
| K3.7.b.9 | Performans Metrikleri | mixer-routing.md · L358 |
| K3.7.b.10 | Bağımlılıklar | mixer-routing.md · L368 |
| **K3.7.c** | **Surround Çözücü** | surround-decoder.md · L10–L291 |
| K3.7.c.1 | Genel Bakış | surround-decoder.md · L10 |
| K3.7.c.2 | Teknik Detaylar | surround-decoder.md · L14 |
| K3.7.c.3 | Surround Formatları | surround-decoder.md · L16 |
| K3.7.c.4 | Downmix Matrisi | surround-decoder.md · L47 |
| K3.7.c.5 | Upmix Matrisi | surround-decoder.md · L107 |
| K3.7.c.6 | Surround Decoder Implementasyonu | surround-decoder.md · L160 |
| K3.7.c.7 | API / Arayüz | surround-decoder.md · L245 |
| K3.7.c.8 | Performans Metrikleri | surround-decoder.md · L281 |
| K3.7.c.9 | Bağımlılıklar | surround-decoder.md · L291 |

### K3.8 — Katman İndeksi

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.8.a** | **Yapı & Kapsam** | index.md · L10–L100 |
| K3.8.a.1 | Genel Bakış | index.md · L10 |
| K3.8.a.2 | Mimari Konum | index.md · L14 |
| K3.8.a.3 | Kapsam ve Kategoriler | index.md · L22 |
| K3.8.a.4 | Temel Motor | index.md · L24 |
| K3.8.a.5 | Efektler | index.md · L29 |
| K3.8.a.6 | Analiz | index.md · L35 |
| K3.8.a.7 | Format Desteği | index.md · L39 |
| K3.8.a.8 | İşleme | index.md · L44 |
| K3.8.a.9 | Temel İlkeler | index.md · L49 |
| K3.8.a.10 | DSP Pipeline | index.md · L73 |
| K3.8.a.11 | Performans Metrikleri | index.md · L90 |
| K3.8.a.12 | Bağımlılıklar | index.md · L100 |
| **K3.8.b** | **Dosya Haritası** | index.md · L108–L126 |
| K3.8.b.1 | Dosya Haritası (başlık) | index.md · L108 |
| K3.8.b.2 | neva-engine-core.md satırı | index.md · L112 |
| K3.8.b.3 | dsp-chain.md satırı | index.md · L113 |
| K3.8.b.4 | eq-parametric.md satırı | index.md · L114 |
| K3.8.b.5 | dynamics-compressor.md satırı | index.md · L115 |
| K3.8.b.6 | effects-reverb.md satırı | index.md · L116 |
| K3.8.b.7 | effects-chorus-delay.md satırı | index.md · L117 |
| K3.8.b.8 | analysis-spectrum.md satırı | index.md · L118 |
| K3.8.b.9 | surround-decoder.md satırı | index.md · L119 |
| K3.8.b.10 | format-decoder.md satırı | index.md · L120 |
| K3.8.b.11 | stream-buffer.md satırı | index.md · L121 |
| K3.8.b.12 | playback-gapless.md satırı | index.md · L122 |
| K3.8.b.13 | mixer-routing.md satırı | index.md · L123 |
| K3.8.b.14 | channel-processing.md satırı | index.md · L124 |
| K3.8.b.15 | sample-rate-conversion.md satırı | index.md · L125 |
| K3.8.b.16 | bit-depth-conversion.md satırı | index.md · L126 |

### K3.9 — Taşıyıcı Dokümantasyon

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K3.9.a** | **README Teknik Bölümleri** | README.md · L26–L296 |
| K3.9.a.1 | 1. Genel Bakış | README.md · L26 |
| K3.9.a.2 | 1.1 Temel İlkeler | README.md · L30 |
| K3.9.a.3 | 2. Neva Engine Mimarisi | README.md · L42 |
| K3.9.a.4 | 2.1 Ana Bileşenler | README.md · L44 |
| K3.9.a.5 | 2.2 DSP Chain Akışı | README.md · L68 |
| K3.9.a.6 | 3. 31-Band Parametric EQ | README.md · L84 |
| K3.9.a.7 | 3.1 EQ Band Frekansları | README.md · L86 |
| K3.9.a.8 | 3.2 Biquad Filtre Koefisyonları | README.md · L122 |
| K3.9.a.9 | 4. Reverb (4 Mod) | README.md · L157 |
| K3.9.a.10 | 4.1 FDN Reverb Yapısı | README.md · L166 |
| K3.9.a.11 | 5. Compressor | README.md · L210 |
| K3.9.a.12 | 5.1 Compressor Parametreleri | README.md · L212 |
| K3.9.a.13 | 5.2 Sidechain Detection | README.md · L223 |
| K3.9.a.14 | 6. Limiter | README.md · L254 |
| K3.9.a.15 | 6.1 True Peak Limiter | README.md · L256 |
| K3.9.a.16 | 7. Mixer (8.1 Surround) | README.md · L266 |
| K3.9.a.17 | 7.1 Kanal Routing Matrisi | README.md · L268 |
| K3.9.a.18 | 7.2 Bass Management | README.md · L282 |
| K3.9.a.19 | 8. Analyzer | README.md · L294 |
| K3.9.a.20 | 8.1 Spectrum Analyzer | README.md · L296 |
| **K3.9.b** | **README Referans Bölümleri** | README.md · L308–L323 |
| K3.9.b.1 | 9. İlgili Dosyalar | README.md · L308 |
| K3.9.b.2 | 10. İlgili ADR'ler | README.md · L323 |

## Kanıt Kataloğu (K3)

> **Yöntem:** 18 dosyanın tamamı grep (^#{2,3} , 210 eşleşme: 114 H2 + 96 H3) ile sayıldı; yaprak = 4. seviye kanıt (H2/H3 başlık satırı veya Dosya Haritası tablo satırı). Hariç tutmalar ve ek yapraklar "Katalog notları" 2–3'te gerekçelendirildi.

| # | Dosya | Rol | H2 | H3 | Kapsanan Yaprak | Satır Aralığı |
|---|-------|-----|----|----|-----------------|---------------|
| 1 | neva-engine-core.md | Motor çekirdeği | 6 | 7 | 12 | L10–L364 |
| 2 | dsp-chain.md | DSP pipeline | 6 | 6 | 11 | L10–L391 |
| 3 | eq-parametric.md | 31-bant EQ | 6 | 4 | 9 | L10–L344 |
| 4 | dynamics-compressor.md | Dinamik işlemci | 6 | 5 | 10 | L10–L363 |
| 5 | effects-reverb.md | Reverb | 6 | 6 | 11 | L10–L330 |
| 6 | effects-chorus-delay.md | Modülasyon efektleri | 6 | 6 | 11 | L10–L386 |
| 7 | analysis-spectrum.md | Spektrum analiz | 6 | 4 | 9 | L10–L416 |
| 8 | surround-decoder.md | Surround çözümleme | 6 | 4 | 9 | L10–L298 |
| 9 | format-decoder.md | Codec çözümleme | 6 | 6 | 11 | L10–L377 |
| 10 | stream-buffer.md | Akış tamponu | 6 | 4 | 9 | L10–L371 |
| 11 | playback-gapless.md | Gapless çalma | 6 | 4 | 9 | L10–L376 |
| 12 | mixer-routing.md | Mixer / routing | 6 | 5 | 10 | L10–L375 |
| 13 | channel-processing.md | Kanal işleme | 6 | 5 | 10 | L10–L335 |
| 14 | sample-rate-conversion.md | Örnekleme dönüşümü | 6 | 4 | 9 | L10–L283 |
| 15 | bit-depth-conversion.md | Bit derinliği | 6 | 5 | 10 | L10–L328 |
| 16 | index.md | Katman indeksi | 9 | 9 | 28 (13 başlık + 15 satır) | L10–L128 |
| 17 | README.md | Taşıyıcı doküman | 10 | 12 | 22 | L26–L335 |
| 18 | CLAUDE.md | Ajan kural dosyası | 5 | 0 | 0 (5 hariç) | L16–L57 |
| | **TOPLAM** | 18 dosya | **114** | **96** | **200** | |

Katalog notları:

1. **Sayım zinciri:** 210 başlık − 25 hariç + 15 tablo satırı = **200 kanıtlı yaprak**; hiyerarşi 9 × 2. katman · 25 × 3. katman · 200 × 4. katman. Onaylı hedef 9/6/200 karşılandı (3. katman tabanı 6'nın üzerinde).
2. **Hariç tutulan 25:** 16 × "Durum: Implementasyon" (içerik dosyalarının 16'sında birer kez — durum metni, katman kanıtı değil; K2 emsali); 4 × index.md numaralı ilke başlığı (L51, L57, L63, L68 — "Temel İlkeler" L49 altında birleşir, K2 emsali); 5 × CLAUDE.md başlığı (L16, L26, L41, L47, L57 — guardrail/kural dosyası, katman yaprağı değil).
3. **Eklenen 15 yaprak:** index.md Dosya Haritası tablo satırları (L112–L126); 15 satırın tamamı dizinde gerçekten var olan 15 .md dosyasına işaret eder (dosya listesiyle birebir örtüşür) — başlık yerine tablo satırı kanıtı.
4. **Katman kuralı:** DSP zinciri EQ → Compressor → Reverb → Limiter (dsp-chain.md L16 "15-Aşamalı Pipeline" + README §2.2 L68 akışı). Crossover (README §2.1 L59–L61) kardeş bileşendir; zincir aşaması olarak K3.2 altında sayılmadı, mixer/surround tarafında (K3.7) yer alır.
5. **Uydurma koruma:** README §9 tablosundaki diskte olmayan 6 ad (31-band-eq.md, reverb-modes.md, mixer-architecture.md, ring-buffer.md, zero-allocation.md, bit-perfect.md) yaprak sayılmadı; §9/§10 başlıklarının kendisi diskte olduğu için K3.9.b'de sayıldı.
6. **Tutarlılık kararı:** her dosyanın "Teknik Detaylar" H2'si ile altındaki H3 başlıkları ayrı yaprak sayıldı (başlık satırının tamamı disk kanıtıdır); "Bağımlılıklar" H2'leri gerçek katman bağımlılık tabloları taşıdığı için yaprak olarak korundu (K0–K2 ile aynı).

---

*K3 Ses İşleme Motoru Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
