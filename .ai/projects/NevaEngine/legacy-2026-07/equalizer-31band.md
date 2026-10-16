---
type: technical-spec
project: Neva Engine
module: equalizer-31band
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — 31-Bant Parametrik Equalizer

## 1. Genel Bakış

Neva Engine'in parametrik equalizer modülü, ISO standart 1/3 oktav frekans
bantlarında çalışan, bant başına bağımsız filtre tipi ve parametre kontrolü
sunan profesyonel bir ses işleme birimidir.

### Tasarım Hedefleri

| Hedef | Değer |
|-------|-------|
| Bant sayısı | 31 (ISO 1/3 oktav) + Low Shelf + High Shelf |
| İşleme tipi | IIR Biquad (JUCE::dsp::IIR::Filter) |
| CPU optimizasyonu | SIMD (SSE/AVX/NEON), paralel bant |
| Faz doğrusallığı | Minimum phase (default), Linear phase (ops.) |
| Latency | 0 sample (min. phase), 512-4096 (lin. phase) |
| Zero crossing | Smooth parameter interpolation |

### ISO 1/3 Oktav Frekansları (Hz)

| Bant | Frekans | Bant | Frekans | Bant | Frekans | Bant | Frekans |
|------|---------|------|---------|------|---------|------|---------|
| 1 | 20 | 9 | 125 | 17 | 800 | 25 | 5000 |
| 2 | 25 | 10 | 160 | 18 | 1000 | 26 | 6300 |
| 3 | 31.5 | 11 | 200 | 19 | 1250 | 27 | 8000 |
| 4 | 40 | 12 | 250 | 20 | 1600 | 28 | 10000 |
| 5 | 50 | 13 | 315 | 21 | 2000 | 29 | 12500 |
| 6 | 63 | 14 | 400 | 22 | 2500 | 30 | 16000 |
| 7 | 80 | 15 | 500 | 23 | 3150 | 31 | 20000 |
| 8 | 100 | 16 | 630 | 24 | 4000 | — | — |

---

## 2. Filtre Tipleri

### 2.1 Bant Başına Seçilebilir Filtre Tipleri

| Filtre Tipi | Parametreler | Açıklama |
|-------------|-------------|----------|
| Peak/Bell | Frekans, Gain (±18 dB), Q (0.1-10) | Klasik parametrik EQ |
| Low Shelf | Frekans, Gain (±18 dB), Slope (12/24 dB) | Alçak frekans shelf |
| High Shelf | Frekans, Gain (±18 dB), Slope (12/24 dB) | Yüksek frekans shelf |
| Low Pass | Frekans, Slope (12/24/36/48 dB), Q | Alçak geçiren |
| High Pass | Frekans, Slope (12/24/36/48 dB), Q | Yüksek geçiren |
| Notch | Frekans, Q (5-100) | Dar bant kesme |
| Band Pass | Frekans, Q (0.1-10) | Bant geçiren |
| All Pass | Frekans, Q (0.1-10) | Faz kaydırıcı |

### 2.2 Kazanç (Gain) Hassasiyeti

```
Çözünürlük: 0.1 dB
Aralık: -18.0 dB ... 0.0 dB ... +18.0 dB
Toplam adım: 361 (36 dB / 0.1 dB)
İnterpolasyon: Smooth (20 ms crossfade)
```

### 2.3 Q Faktörü (Bandwidth)

```
Q = 0.1 → Bant genişliği: 4.29 oktav (geniş)
Q = 0.7 → Bant genişliği: 2.0 oktav
Q = 1.0 → Bant genişliği: 1.39 oktav
Q = 1.4 → Bant genişliği: 1.0 oktav (standart)
Q = 2.0 → Bant genişliği: 0.67 oktav
Q = 5.0 → Bant genişliği: 0.27 oktav
Q = 10.0 → Bant genişliği: 0.13 oktav (dar)
```

---

## 3. IIR Biquad Implementasyonu

### 3.1 JUCE::dsp::IIR::Filter

```cpp
class EQBand {
    juce::dsp::IIR::Filter<float> filter;
    juce::dsp::IIR::Coefficients<float>::Ptr coefficients;

public:
    void updateCoefficients(EQParameters params) {
        switch (params.filterType) {
            case FilterType::Peak:
                coefficients = juce::dsp::IIR::Coefficients<float>::makePeakFilter(
                    sampleRate, params.frequency, params.q, params.gain);
                break;
            case FilterType::LowShelf:
                coefficients = juce::dsp::IIR::Coefficients<float>::makeLowShelf(
                    sampleRate, params.frequency, params.q, params.gain);
                break;
            case FilterType::LowPass:
                coefficients = juce::dsp::IIR::Coefficients<float>::makeLowPass(
                    sampleRate, params.frequency, params.q);
                break;
            // ... diğer tipler
        }
        filter.coefficients = coefficients;
    }

    float processSample(float input) {
        return filter.processSample(input);
    }
};
```

### 3.2 Biquad Katsayıları (12 Adet)

```
Direct Form II (Transposed):

         b0 + b1*z^-1 + b2*z^-2
H(z) = ─────────────────────────
         1 + a1*z^-1 + a2*z^-2

Katsayılar: b0, b1, b2, a0(=1), a1, a2
Her kanal için: b0, b1, b2, a1, a2 → 5 float × N kanal
```

### 3.3 Biquad Tipleri (JUCE)

| Metod | Filtre |
|-------|--------|
| `makePeakFilter` | Peak/Bell |
| `makeLowShelf` | Low Shelf |
| `makeHighShelf` | High Shelf |
| `makeLowPass` | Low Pass |
| `makeHighPass` | High Pass |
| `makeNotch` | Notch |
| `makeBandPass` | Band Pass |
| `makeAllPass` | All Pass |

---

## 4. EQ Zinciri Mimarisi

### 4.1 Paralel Bant İşleme (SIMD)

```cpp
class EQProcessor {
    std::array<EQBand, 33> bands;  // 31 bant + Low Shelf + High Shelf

    void processBlock(float* buffer, int numSamples) {
        // Bantları 4'lü gruplar halinde SIMD işle
        for (size_t i = 0; i < bands.size(); i += 4) {
            processBandGroupSIMD(bands[i], bands[i+1],
                                  bands[i+2], bands[i+3],
                                  buffer, numSamples);
        }
    }

    void processBandGroupSIMD(EQBand& b0, EQBand& b1,
                               EQBand& b2, EQBand& b3,
                               float* buffer, int numSamples) {
        #ifdef __AVX2__
        for (int i = 0; i < numSamples; i += 8) {
            __m256 samples = _mm256_loadu_ps(&buffer[i]);
            samples = b0.processSIMD(samples);
            samples = b1.processSIMD(samples);
            samples = b2.processSIMD(samples);
            samples = b3.processSIMD(samples);
            _mm256_storeu_ps(&buffer[i], samples);
        }
        #else
        // Scalar fallback
        for (int i = 0; i < numSamples; ++i) {
            buffer[i] = b0.processSample(buffer[i]);
            buffer[i] = b1.processSample(buffer[i]);
            buffer[i] = b2.processSample(buffer[i]);
            buffer[i] = b3.processSample(buffer[i]);
        }
        #endif
    }
};
```

### 4.2 SIMD Hedef Mimarileri

| ISA | Platform | Kayıt Genişliği | Float/Lane | Hız (relatif) |
|-----|----------|----------------|------------|---------------|
| NEON | ARM64 (Apple M1/M2, RPi5) | 128-bit | 4 | 1.0x |
| SSE4.1 | x86-64 (base) | 128-bit | 4 | 1.0x |
| AVX | x86-64 (2011+) | 256-bit | 8 | 2.0x |
| AVX2+FMA | x86-64 (2013+) | 256-bit | 8 | 2.5x |
| AVX-512 | x86-64 (2017+) | 512-bit | 16 | 4.0x |

### 4.3 Faz Doğrusallığı Seçenekleri

| Mod | Latency | Faz Yanıtı | CPU Maliyeti |
|-----|---------|-----------|-------------|
| Minimum Phase | 0 sample | Doğrusal değil | 1x (baz) |
| Linear Phase (FIR) | 512-4096 sample | Doğrusal | 3-8x |
| Mixed Phase | Ayarlanabilir | Kısmi | 2-5x |

---

## 5. EQ Preset Sistemi

### 5.1 Hazır Presetler

| Preset | Açıklama | Karakteristik |
|--------|----------|--------------|
| Rock | Gitar ve vokal ön planı | +3 dB @ 63, 3.15k; -2 dB @ 315 |
| Pop | Dengeli, parlak | +2 dB @ 100, 10k |
| Jazz | Sıcak, doğal | +1.5 dB @ 80; -1 dB @ 4k |
| Klasik | Orkestra dengesi | Flat +0.5 dB @ 40, 16k |
| Arabesk | Bağlama ve vokal | +4 dB @ 200, 3k; -2 dB @ 500 |
| Düğün Salonu | Davul-zurna dengesi | +6 dB @ 63, 200; +3 dB @ 8k |
| Vokal Boost | Vokal netliği | +3 dB @ 2k-5k; -3 dB @ 250 |
| Bass Boost | Sub-bas güçlendirme | +6 dB @ 40-80 Hz |
| Flat | Sıfırlama | Tüm bantlar 0 dB |
| Custom 1-10 | Kullanıcı tanımlı | Kaydedilebilir |

### 5.2 Preset Dosya Formatı (JSON)

```json
{
  "name": "Arabesk",
  "description": "Bağlama ve vokal ön planlı Arabesk müzik EQ",
  "version": "1.0",
  "bands": [
    {"index": 1, "frequency": 20, "type": "lowShelf", "gain": -2.0, "q": 0.7},
    {"index": 2, "frequency": 25, "type": "peak", "gain": -1.5, "q": 1.0},
    // ... 29 bant daha
    {"index": 32, "frequency": 16000, "type": "peak", "gain": -1.0, "q": 1.0},
    {"index": 33, "frequency": 20000, "type": "highShelf", "gain": -3.0, "q": 0.7}
  ]
}
```

### 5.3 Preset Yönetim API'si

```json
// Preset listesi
{"method": "eq.listPresets", "params": {}, "id": 1}

// Preset yükle
{"method": "eq.loadPreset", "params": {"name": "Arabesk"}, "id": 2}

// Preset kaydet
{"method": "eq.savePreset", "params": {"name": "BenimEQ", "description": "..."}, "id": 3}

// Preset sil
{"method": "eq.deletePreset", "params": {"name": "EskiPreset"}, "id": 4}

// Preset export
{"method": "eq.exportPreset", "params": {"name": "Arabesk", "path": "export.json"}, "id": 5}
```

---

## 6. AI Auto-EQ

### 6.1 Çalışma Prensibi

```
Giriş Sesi
    │
    ▼
┌──────────────────┐
│ FFT Analizi      │  (4096-point, Hann window, %50 overlap)
│ (Spektrum çıkar) │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Genre Detection  │  (CNN/SVM sınıflandırıcı)
│ (Müzik türü)     │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Referans Eğri    │  (Genre-specific target curve)
│ Seçimi           │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Spektrum         │  (Giriş vs Referans fark analizi)
│ Karşılaştırma    │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ EQ Katsayı       │  (Otomatik bant ayarı)
│ Optimizasyonu    │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ EQ Zincirine     │  (Bant değerlerini uygula)
│ Uygula           │
└──────────────────┘
```

### 6.2 Referans Eğrileri

| Eğri | Açıklama | Kullanım |
|------|----------|----------|
| Harman Target (2018) | Kulaklık frekans hedefi | Kulaklık dinleme |
| Diffuse Field | Dağınık alan eşitleme | Referans dinleme |
| Free Field | Serbest alan eşitleme | Hoparlör kalibrasyonu |
| X-Curve (ISO 2969) | Sinema ses standardı | Film/Dizi |
| EBU R128 Loudness | Yayın loudness normu | Radyo/TV yayını |
| Custom User Curve | Kullanıcı tanımlı eğri | İşitme kaybı telafisi |

### 6.3 Auto-EQ API

```json
// Auto-EQ başlat (mevcut çalan parça için)
{"method": "eq.autoAnalyze", "params": {
    "reference": "harman2018",
    "aggressiveness": 0.7
}, "id": 1}

// Auto-EQ durdur
{"method": "eq.autoAnalyzeStop", "params": {}, "id": 2}

// Auto-EQ sonucu
// Yanıt: {"result": {
//   "detectedGenre": "arabesk",
//   "confidence": 0.87,
//   "suggestedBands": [...],
//   "applied": true
// }}
```

---

## 7. Parametrik EQ API

### 7.1 Bant Güncelleme

```json
// Tek bant güncelleme
{"method": "eq.setBand", "params": {
    "bandIndex": 10,
    "type": "peak",
    "frequency": 1000,
    "gain": 3.5,
    "q": 1.4
}, "id": 1}

// Çoklu bant güncelleme (tek mesaj)
{"method": "eq.setBands", "params": {
    "bands": [
        {"index": 10, "gain": 3.5, "q": 1.4},
        {"index": 15, "frequency": 500, "gain": -2.0},
        {"index": 25, "type": "highShelf", "gain": -1.5}
    ]
}, "id": 2}

// Tüm bantları sıfırla
{"method": "eq.reset", "params": {}, "id": 3}

// Mevcut durumu al
{"method": "eq.getState", "params": {}, "id": 4}
```

### 7.2 EQ Durum Sorgulama Yanıtı

```json
{
  "result": {
    "enabled": true,
    "preEQGain": 0.0,
    "postEQGain": 0.0,
    "phaseMode": "minimum",
    "activePreset": "Arabesk",
    "bands": [
      {"index": 1, "frequency": 20, "type": "lowShelf", "gain": -2.0, "q": 0.7, "enabled": true},
      // ... tüm bantlar
    ],
    "spectrumData": {
      "fftSize": 4096,
      "magnitudes": [0.1, 0.15, 0.2, ...],
      "frequencies": [20, 21.5, 23.4, ...]
    }
  }
}
```

---

## 8. CPU Optimizasyonları

### 8.1 Paralel Bant Stratejisi

| Optimizasyon | Kazanım | Açıklama |
|-------------|---------|----------|
| SIMD bant gruplama | 2-4x | 4 bant paralel işleme |
| Tembel güncelleme | 1.2x | Sadece değişen bantları yeniden hesapla |
| Bypass optimizasyonu | 1.5x | Kazancı 0 olan bantları atla |
| Buffer önbellek | 1.3x | Ardışık erişim için cache-line dostu |
| Frekans önbelleği | 1.1x | Aynı frekanstaki bantlar için ortak hesaplama |

### 8.2 CPU Profili (31 bant, 48 kHz, 128 buffer)

| İşlemci | SSE | AVX2 | NEON | CPU % |
|---------|-----|------|------|-------|
| Ryzen 7 5800X | ✓ | ✓ | — | 4.2% |
| Core i9-13900K | ✓ | ✓ | — | 2.8% |
| Apple M2 Pro | — | — | ✓ | 3.5% |
| Raspberry Pi 5 | — | — | ✓ | 18% |
| Core i5-8250U | ✓ | ✓ | — | 8.5% |

---

## 9. Görsel Geri Bildirim

### 9.1 Spektrum Analizör

```
Frekans Ekseni (log): 20 Hz ─────────────────────── 20 kHz
                       │                              │
Genlik Ekseni:        ── -60 dB ──────────────────────
                       │   ┌──┐          ┌───┐        │
                       │   │  │  ┌───┐   │   │  ┌──┐  │
                       │ ──┤  ├──┤   ├───┤   ├──┤  ├──│
                       │   │  │  └───┘   │   │  └──┘  │
                       │───┘  └──────────┘   └────────│
                       └──────────────────────────────┘
```

### 9.2 EQ Eğrisi Overlay

- Gerçek zamanlı FFT spektrumu (yeşil)
- EQ filtre eğrisi (beyaz, kalın)
- Referans hedef eğrisi (sarı, kesikli)
- Peak hold (kırmızı, 3 sn)

### 9.3 Metre Göstergeleri

| Metre | Birim | Aralık |
|-------|-------|--------|
| Peak | dBFS | -60 ... 0 |
| RMS | dBFS | -60 ... 0 |
| LUFS (Integrated) | LUFS | -40 ... 0 |
| True Peak | dBTP | -60 ... 0 |
| Crest Factor | dB | 0 ... 30 |

---

## 10. Yapılandırma

### 10.1 neva-engine.json EQ Bölümü

```json
{
  "eq": {
    "enabled": true,
    "defaultPreset": "Flat",
    "phaseMode": "minimum",
    "preEQGain": 0.0,
    "postEQGain": 0.0,
    "spectrumAnalyzer": {
      "enabled": true,
      "fftSize": 4096,
      "windowType": "hann",
      "overlap": 0.5,
      "refreshRate": 30,
      "decay": 0.85
    },
    "autoEQ": {
      "enabled": false,
      "defaultReference": "harman2018",
      "aggressiveness": 0.7,
      "maxGainPerBand": 6.0,
      "updateInterval": 30
    }
  }
}
```

---

## 11. Test Stratejisi

### 11.1 EQ Doğruluk Testleri

| Test | Metodoloji |
|------|-----------|
| Frekans yanıtı | Beyaz gürültü → FFT → teorik eğri ile karşılaştır |
| Faz yanıtı | Sine sweep → cross-correlation |
| Distorsiyon | Sine wave → THD+N @ 1 kHz |
| Bant izolasyonu | Tek bant aktif → komşu bant etkisi |
| Kazanç doğruluğu | ±0.1 dB tolerans |

### 11.2 Performans Testleri

```
Test: 31 bant aktif, Peak tipi, Q=1.4, rastgele kazanç
Süre: 10 dakika
Metrik: CPU %, bellek, dropout sayısı
Geçme: < %5 CPU, 0 dropout
```

---

## 12. Gelecek Genişletmeler

| Özellik | Faz | Açıklama |
|---------|-----|----------|
| Dynamic EQ | F3+ | Threshold-tabanlı dinamik EQ |
| Mid/Side EQ | F3+ | Stereo genişliği kontrolü |
| FIR linear phase | F4 | Daha yüksek kalite linear phase |
| Convolution EQ | F5 | IR tabanlı eşitleme |
| 64-bit EQ pipeline | F5 | Double precision DSP zinciri |
| AI Room Correction | F6 | Oda akustiği düzeltme |

---

*Neva Engine v1.0.0 — 31-Bant Parametrik EQ Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
