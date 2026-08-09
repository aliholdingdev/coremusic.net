---
type: adr
category: audio
title: "ADR-025: Professional EQ System"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-025: Professional EQ System

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Audio
**İlgili Agent:** [[.agents/embedded-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun profesyonel EQ (Equalizer) sistemini tanımlar. 31-band parametrik EQ tasarımı, DSP uygulaması, bant frekansları, preset sistemi ve performans optimizasyonlarını kapsar. Tüm ses işleme zincirinde EQ bu spesifikasyona göre uygulanır.

---

## 2. Bağlam

CoreMusic, bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi bilgi-eğlence ve ev medya merkezleri için tasarlanmış bir medya platformudur. Profesyonel kullanıcılar hassas EQ kontrolü bekler:

- Stüdyo üretimi: 31-band EQ ile mastering
- DJ performansı: Canlı EQ ayarlama
- Araç içi: Ortam sesine göre EQ
- Ev medya: Oda akustiğine göre EQ

31-band parametrik EQ, profesyonel ses endüstrisi standardıdır.

---

## 3. Karar

CoreMusic'te **31-band parametrik EQ** kullanılacak. Her bant bağımsız olarak ayarlanabilir olacak, DSP zincirinde real-time işlenecek ve zero-allocation prensibine uygun olacaktır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| 31-band | Profesyonel standart | Bu ADR |
| Parametrik | Frekans, gain, Q ayarlanabilir | Bu ADR |
| Real-time | DSP callback'de işlenir | [[ADR-017-dsp-hardware-mode]] |
| Zero-allocation | Audio thread'de heap allocation yasak | [[ADR-017-dsp-hardware-mode]] |
| Preset sistemi | Kaydedilebilir/yüklenebilir ayarlar | Bu ADR |

---

## 4. Teknik Detaylar

### 4.1 EQ Bant Frekansları

31-band EQ, oktav aralıklarında aşağıdaki frekanslara sahiptir:

| # | Frekans (Hz) | Oktav | Kullanım |
|---|-------------|-------|----------|
| 1 | 20 | Sub-bass | Derinlik, hissedilen bass |
| 2 | 25 | Sub-bass | Sub-bass detayı |
| 3 | 31.5 | Sub-bass | Sub-bass vücudu |
| 4 | 40 | Bass | Bass başlangıcı |
| 5 | 50 | Bass | Bass detayı |
| 6 | 63 | Bass | Bass vücudu |
| 7 | 80 | Bass | Bass tınısı |
| 8 | 100 | Low-mid bass | Bass geçiş |
| 9 | 125 | Low-mid | Düşük orta frekans |
| 10 | 160 | Low-mid | Düşük orta detay |
| 11 | 200 | Low-mid | Düşük orta vücudu |
| 12 | 250 | Low-mid | Düşük orta tınısı |
| 13 | 315 | Mid | Orta frekans başlangıcı |
| 14 | 400 | Mid | Orta frekans detayı |
| 15 | 500 | Mid | Orta frekans vücudu |
| 16 | 630 | Mid | Orta frekans tınısı |
| 17 | 800 | Mid | Orta frekans geçişi |
| 18 | 1k | Upper-mid | Üst orta başlangıcı |
| 19 | 1.25k | Upper-mid | Üst orta detayı |
| 20 | 1.6k | Upper-mid | Üst orta vücudu |
| 21 | 2k | Upper-mid | Üst orta tınısı |
| 22 | 2.5k | Presence | Varlık frekansı |
| 23 | 3.15k | Presence | Presence detayı |
| 24 | 4k | Presence | Presence vücudu |
| 25 | 5k | Brilliance | Parlaklık başlangıcı |
| 26 | 6.3k | Brilliance | Parlaklık detayı |
| 27 | 8k | Brilliance | Parlaklık vücudu |
| 28 | 10k | Brilliance | Parlaklık tınısı |
| 29 | 12.5k | Air | Hava frekansı |
| 30 | 16k | Air | Hava detayı |
| 31 | 20k | Air | Üst limit |

### 4.2 Parametrik EQ Parametreleri

#### 4.2.1 Bant Parametreleri

| Parametre | Min | Max | Varsayılan | Adım |
|-----------|-----|-----|-----------|------|
| Frekans | 20 Hz | 20 kHz | Band'a göre | 1 Hz |
| Gain | -12 dB | +12 dB | 0 dB | 0.1 dB |
| Q Factor | 0.1 | 10.0 | 1.0 | 0.1 |
| Filter Type | LPF, HPF, BPF, Notch, Peaking | Peaking | — |

#### 4.2.2 Filter Tipleri

| Tip | Kullanım | Uygulama |
|-----|----------|----------|
| Peaking (Bell) | Geniş bant boost/cut | Ana EQ ayarı |
| Low Shelf | Düşük frekans lift/cut | Bass kontrolü |
| High Shelf | Yüksek frekans lift/cut | Brilliance kontrolü |
| Low Pass | Düşük frekans geçişi | Bass yönetimi |
| High Pass | Yüksek frekans geçişi | Rumble filtresi |
| Notch | Dar bant kesme | Rezonans kaldırma |
| Band Pass | Orta band geçişi | Freq analiz |

### 4.3 DSP Uygulaması

#### 4.3.1 Biquad Filtre Yapısı

```cpp
struct alignas(64) EQBand {
    float frequency;    // Hz
    float gain;         // dB
    float q;            // Q factor
    float sampleRate;   // Hz
    
    // Biquad katsayıları
    float b0, b1, b2;   // Giriş katsayıları
    float a1, a2;       // Geri besleme katsayıları
    
    // Durum değişkenleri (zero-allocation)
    float x1, x2;       // Giriş gecikmeleri
    float y1, y2;       // Çıkış gecikmeleri
    
    void calculateCoefficients() noexcept;
    float processSample(float input) noexcept;
};
```

#### 4.3.2 Biquad Hesaplama

```cpp
void EQBand::calculateCoefficients() noexcept {
    const float A = std::pow(10.0f, gain / 40.0f);
    const float w0 = 2.0f * M_PI * frequency / sampleRate;
    const float alpha = std::sin(w0) / (2.0f * q);
    
    const float cosw0 = std::cos(w0);
    
    // Peaking EQ
    b0 = 1.0f + alpha * A;
    b1 = -2.0f * cosw0;
    b2 = 1.0f - alpha * A;
    a0 = 1.0f + alpha / A;
    a1 = -2.0f * cosw0;
    a2 = 1.0f - alpha / A;
    
    // Normalize
    b0 /= a0; b1 /= a0; b2 /= a0;
    a1 /= a0; a2 /= a0;
}
```

#### 4.3.3 Real-time İşleme

```cpp
float EQBand::processSample(float input) noexcept {
    // Direct Form II Transposed
    float output = b0 * input + x1;
    x1 = b1 * input - a1 * output + x2;
    x2 = b2 * input - a2 * output;
    return output;
}
```

### 4.4 DSP Zinciri

#### 4.4.1 Sıralama

```
Input → EQ (31-band) → Compressor → Limiter → Output
```

#### 4.4.2 EQ Öncelik Sıralaması

| Sıra | İşlem | Gerekçe |
|------|-------|---------|
| 1 | High Pass (20Hz) | Rumble temizliği |
| 2 | Parametric EQ (31-band) | Ana equalization |
| 3 | Low Shelf | Bass yönetim |
| 4 | High Shelf | Brilliance yönetim |

### 4.5 Preset Sistemi

#### 4.5.1 Yerleşik Preset'ler

| # | Preset | Açıklama | Kullanım |
|---|--------|----------|----------|
| 1 | Flat | Tüm bantlar 0dB | Varsayılan |
| 2 | Rock | Bass boost, presence boost | Rock müzik |
| 3 | Pop | Orta boost, bass hafif boost | Pop müzik |
| 4 | Jazz | Warm, presence boost | Jazz müzik |
| 5 | Classical | Flat, hafif presence | Klasik müzik |
| 6 | Electronic | Sub-bass boost, brilliance | EDM |
| 7 | Hip-Hop | Sub-bass boost, mid cut | Hip-Hop |
| 8 | Acoustic | Warm, natural | Akustik |
| 9 | Vocal | Presence boost, bass cut | Vokal |
| 10 | Bass Boost | +6dB below 100Hz | Bass ağırlıklı |
| 11 | Treble Boost | +6dB above 4kHz | Parlaklık |
| 12 | Podcast | Voice optimized | Konuşma |
| 13 | Car | Araç içi optimizasyonu | Araç |
| 14 | Studio Flat | Profesyonel flat | Stüdyo |
| 15 | Live | Canlı performans | Konser |

#### 4.5.2 Preset Formatı

```json
{
  "name": "Rock",
  "description": "Rock müzik için optimize edilmiş EQ",
  "bands": [
    {"frequency": 63, "gain": 3.0, "q": 1.0},
    {"frequency": 250, "gain": -1.0, "q": 1.2},
    {"frequency": 3150, "gain": 2.0, "q": 1.5},
    {"frequency": 8000, "gain": 1.5, "q": 1.0}
  ],
  "category": "genre",
  "version": "1.0"
}
```

#### 4.5.3 Kullanıcı Presetleri

| Özellik | Değer |
|---------|-------|
| Kaydetme | Kullanıcı tanımlı preset |
| Paylaşma | Export/Import (JSON) |
| Synchronization | Cloud sync (opsiyonel) |
| Kategori | Genre, Custom, Artist |
| Version | Semver formatı |

### 4.6 Performans Metrikleri

#### 4.6.1 Hedefler

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| CPU kullanımı | <5% (31-band) | Toplam CPU |
| Latency | <1ms ek | ASIO buffer'a ek |
| Bellek | <10KB (31-band) | Stack allocation |
| Gecikme | 0 samples (zero-latency) | Phase response |

#### 4.6.2 Optimizasyonlar

| Optimizasyon | Açıklama |
|-------------|----------|
| SIMD | SSE2/AVX2 ile vektörel işleme |
| Cache alignment | `alignas(64)` ile false sharing önleme |
|constexpr | Katsayılar compile-time'da hesaplanabilir |
| Branch prediction | `[[likely]]` / `[[unlikely]]` |
| Loop unrolling | 4-sample unrolling |

### 4.7 UI Entegrasyonu

#### 4.7.1 31-Band Grafik

```javascript
const eqBands = document.querySelectorAll('.eq-band');
eqBands.forEach(band => {
  band.addEventListener('input', (e) => {
    const frequency = band.dataset.frequency;
    const gain = e.target.value;
    AudioService.setEQBand(frequency, gain);
  });
});
```

#### 4.7.2 Görsel Geri Bildirim

| Özellik | Açıklama |
|---------|----------|
| Frekans response çizimi | Gerçek zamanlı grafik |
| Drag & drop | Bant taşıma |
| Solo/Mute | Tek bant dinleme |
| A/B karşılaştırma | İki preset arası geçiş |
| Undo/Redo | Son 20 işlem |

#### 4.7.3 Keyboard Shortcuts

| Kısayol | Aksiyon |
|---------|---------|
| `Ctrl+Z` | Undo |
| `Ctrl+Y` | Redo |
| `Ctrl+S` | Preset kaydet |
| `Ctrl+L` | Preset yükle |
| `Space` | Solo (seçili bant) |
| `M` | Mute (seçili bant) |
| `↑/↓` | Gain artır/azalt (0.1dB) |
| `←/→` | Önceki/sonraki bant |
| `A` | A/B karşılaştırma |
| `R` | Flat'e sıfırla |

#### 4.7.4 Responsive Tasarım

| Ekran | Bant genişliği | Görünüm |
|-------|---------------|---------|
| Desktop (>1200px) | 30px | Tam grafik |
| Tablet (768-1200px) | 20px | Dar grafik |
| Mobil (<768px) | — | Slider modu |

### 4.8 EQ Analiz Araçları

#### 4.8.1 Frekans Spektrumu Analizi

| Özellik | Açıklama |
|---------|----------|
| Real-time FFT | Gerçek zamanlı frekans analizi |
| Peak hold | Maksimum frekans değeri |
| RMS ölçümü | Ortalama ses seviyesi |
| Spectrum overlay | EQ eğrisinin üstüne bindirme |

#### 4.8.2 A/B Karşılaştırma

```
A: Orijinal ses → EQ uygulanmamış
B: EQ uygulanmış ses
Geçiş: Anlık A/B karşılaştırma
```

#### 4.8.3 Metering

| Meter | Açıklama |
|-------|----------|
| Peak | Maksimum seviye |
| RMS | Ortalama seviye |
| LUFS | Loudness Units (broadcast standardı) |
| Stereo | Kanal dengesi göstergesi |

### 4.9 Çoklu Kanal Desteği

#### 4.9.1 Kanal Yapısı

| Kanal | EQ Bağımsız mı? | Varsayılan |
|-------|-----------------|-----------|
| Front Left | ✅ | Flat |
| Front Right | ✅ | Flat |
| Center | ✅ | Flat |
| Surround Left | ✅ | Flat |
| Surround Right | ✅ | Flat |
| Rear Left | ✅ | Flat |
| Rear Right | ✅ | Flat |
| Height Left | ✅ | Flat |
| Height Right | ✅ | Flat |
| LFE (Sub) | ✅ | Low-pass 120Hz |

#### 4.9.2 Kanal Gruplama

| Grup | Kullanım |
|------|----------|
| Stereo (L+R) | Ana pair |
| Center only | Vokal optimizasyonu |
| Surround group | Surround kanallar |
| Height group | Yükseklik kanalları |
| All channels | Tüm kanallar (master) |

#### 4.9.3 Copy/Paste

| İşlem | Açıklama |
|-------|----------|
| Copy channel | Bir kanalın EQ'sunu kopyala |
| Paste channel | Başka bir kanala yapıştır |
| Copy to all | Tüm kanallara uygula |
| Link channels | İki kanalı senkronize et |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Audio thread'de malloc | Zero-allocation (stack) | Ses takılması |
| Audio thread'de mutex | Lock-free (atomics) | Deadlock |
| Denormalized float | Flush to zero | CPU spike |
| Hardcoded frekanslar | Konfigüre edilebilir | Esneklik eksikliği |
| Blocking I/O | Non-blocking | Gecikme |
| throw / exception | noexcept | Crash |
| Global state | Thread-local state | Race condition |
| Unaligned access | `alignas(64)` | Performance penalty |
| float64 (double) | float32 (PCM standardı) | Performans düşüşü |
| Manuel buffer yönetimi | Stack/Ring buffer | Bellek sızıntısı |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Denormalized input | Sessizlik sonrası ses | Flush to zero |
| Sample rate değişimi | 44.1kHz → 48kHz | Coefficient recalculation |
| Clipping | +12dB boost | Limiter koruması |
| NaN/Inf | Hesaplama hatası | Safe division + check |
| CPU spike | Yüksek yük | Adaptive quality |
| Buffer underrun | CPU %100 | Fade-out → restart |
| Cihaz değişikliği | ASIO → WASAPI | Preset migration |
| Eşzamanlı preset değişimi | UI + API | Atomic update |
| Maksimum band sayısı | 31+ band denemesi | Hard limit: 31 |
| Preset boyutu aşımı | >100 band | Dosya boyutu kontrolü |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero-allocation — Audio thread'de heap allocation yasak | Ses takılması |
| 2 | Lock-free — Audio thread'de mutex yasak | Deadlock |
| 3 | noexcept — ASIO callback'de exception yasak | Crash |
| 4 | alignas(64) — Cache line alignment zorunlu | Performance penalty |
| 5 | float32 — PCM standardı, double yasak | Performans düşüşü |
| 6 | 31-band limit — Max band sayısı | Sınır aşıldı |
| 7 | ±12dB limit — Gain sınırlaması | Clipping riski |
| 8 | Sample rate awareness — Coefficient recalculation | Yanlış EQ |
| 9 | Denormalized flush — Zero'a yakın float temizliği | CPU spike |
| 10 | CPU <5% — 31-band için üst sınır | Performans düşüşü |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode | XMOS, JUCE, ASIO |
| [[ADR-019-per-os-neva-player]] | Per-OS Neva Player | Cross-platform EQ |
| [[ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses donanımı | Multi-band EQ |
| [[ADR-006-performance-targets]] | Performans hedefleri | CPU limitleri |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| § 4.1 Frekanslar | [[projects/NevaEngine/equalizer-system]] | EQ sistemi |
| § 4.3 DSP | [[projects/NevaEngine/eq-dsp-chain]] | DSP zinciri |
| § 4.5 Preset | [[projects/NevaEngine/eq-presets]] | Preset sistemi |
| § 5 Yasak | [[brain.md]] §7 | C++ kuralları |
| § 6 Edge | [[ADR-038-8.1-sound-card-chip-selection]] | Donanım |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **EQ** | Equalizer — Frekans dengeleme |
| **Parametric** | Her bant bağımsız ayarlanabilir |
| **Biquad** | İki kutuplu dijital filtre |
| **Q Factor** | Filtre genişliği (dar/geniş) |
| **Gain** | Kazanç (boost/cut) |
| **Peaking** | Çan şekilli filtre |
| **Shelf** | Raf şekilli filtre |
| **Low Pass** | Düşük frekans geçişi |
| **High Pass** | Yüksek frekans geçişi |
| **Notch** | Dar bant kesme |
| **DSP** | Digital Signal Processing — Dijital sinyal işleme |
| **Zero-allocation** | Audio thread'de heap allocation olmaması |
| **Lock-free** | Mutex kullanılmadan eşzamanlılık |
| **SIMD** | Single Instruction Multiple Data |
| **PCM** | Pulse-Code Modulation — Ham ses formatı |
| **ASIO** | Audio Stream Input/Output |
| **Flush to zero** | Denormalized float'ları sıfırlama |
| **Presets** | Kaydedilmiş EQ ayarları |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-025 Professional EQ System |
| Last Updated | 2026-08-08 |
| ADR References | 4 |
| Cross References | 7 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 18 |
| EQ Bands | 31 |
| Preset Count | 15 yerleşik |
| Filter Types | 7 |
| Performance Target | <5% CPU |
| Memory Target | <10KB |
| Latency Target | 0 samples |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
