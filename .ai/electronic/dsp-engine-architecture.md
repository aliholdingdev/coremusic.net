---
title: "CoreMusic — DSP Engine Architecture"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — DSP Engine Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

DSP Motoru, gerçek zamanlı ses işlemenin kalbidir. 15 aşamalı DSP pipeline, equalizer, compressor, limiter, crossover, FIR/IIR filtreler, FFT analizi, delay, reverb, room correction, loudness processing ve AI destekli DSP optimizasyonunu kapsar.

---

## 2. DSP Engine Pipeline (15 Aşama)

```
Input Signal ──▶ Input Gain ──▶ Noise Gate ──▶ HPF ──▶ LPF ──▶ Parametric EQ ──▶ Graphic EQ ──▶ Compressor ──▶ Limiter ──▶ Loudness ──▶ Crossover ──▶ Delay ──▶ Reverb ──▶ Output Gain ──▶ Output Routing
```

### 2.1 ASCII: 15 Aşamalı DSP Pipeline
```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    COREMUSIC DSP ENGINE — 15 AŞAMALI PIPELINE                   │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐         │
│  │1.Input  │──▶│2.Input  │──▶│3.Noise  │──▶│4. HPF   │──▶│5. LPF   │         │
│  │ Signal  │   │  Gain   │   │  Gate   │   │High-Pass│   │ Low-Pass│         │
│  │         │   │  <0.01ms│   │  <0.1ms │   │  <0.01ms│   │  <0.01ms│         │
│  └─────────┘   └─────────┘   └─────────┘   └─────────┘   └─────────┘         │
│                                                                                 │
│  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐         │
│  │6.Param. │──▶│7.Graphic│──▶│8.Compres│──▶│9.Limiter│──▶│10.Loud- │         │
│  │   EQ    │   │   EQ    │   │  sor    │   │  <0.1ms │   │  ness   │         │
│  │  <0.1ms │   │  <0.1ms │   │  <0.5ms │   │ KRİTİK  │   │  <0.5ms │         │
│  └─────────┘   └─────────┘   └─────────┘   └─────────┘   └─────────┘         │
│                                                                                 │
│  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐         │
│  │11.Cross-│──▶│12.Delay │──▶│13.Reverb│──▶│14.Output│──▶│15.Output│         │
│  │  over   │   │ 0-1000ms│   │  <5ms   │   │  Gain   │   │ Routing │         │
│  │  <0.1ms │   │  Orta   │   │ Düşük   │   │  <0.01ms│   │  <0.01ms│         │
│  └─────────┘   └─────────┘   └─────────┘   └─────────┘   └─────────┘         │
│                                                                                 │
│  Toplam Gecikme: <10ms (ASIO 512 sample @ 48kHz ≈ 10.67ms)                    │
│                                                                                 │
│  ═══════════════════════════════════════════════════════════════════════════   │
│  KATMANLAR:                                                                    │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  GİRDİ       │  │  FİLTRE      │  │  DİNAMİK     │  │  UZAMSAL     │      │
│  │──────────────│  │──────────────│  │──────────────│  │──────────────│      │
│  │ Input Gain   │  │ HPF          │  │ Compressor   │  │ Reverb       │      │
│  │ Noise Gate   │  │ LPF          │  │ Limiter      │  │ Delay        │      │
│  │              │  │ Parametric EQ│  │ Gate         │  │ Echo         │      │
│  │              │  │ Graphic EQ   │  │ Expander     │  │ Stereo Width │      │
│  └──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                                                 │
│  ┌──────────────┐                                                             │
│  │  ÇIKIŞ       │                                                             │
│  │──────────────│                                                             │
│  │ Output Gain  │                                                             │
│  │ Output Route │                                                             │
│  │ Crossover    │                                                             │
│  └──────────────┘                                                             │
└─────────────────────────────────────────────────────────────────────────────────┘
```

| Aşama | Görev | Gecikme | Öncelik |
|-------|-------|---------|---------|
| 1. Input Signal | Ham ses girdisi | 0 | — |
| 2. Input Gain | Girdi seviye ayarı | <0.01ms | Yüksek |
| 3. Noise Gate | Arka plan gürültüsü engelleme | <0.1ms | Yüksek |
| 4. HPF | Düşük frekans kesme | <0.01ms | Yüksek |
| 5. LPF | Yüksek frekans kesme | <0.01ms | Yüksek |
| 6. Parametric EQ | Serbest ayarlanabilir EQ | <0.1ms | Yüksek |
| 7. Graphic EQ | Sabit bantlı EQ | <0.1ms | Yüksek |
| 8. Compressor | Dinamik aralık sıkıştırma | <0.5ms | Yüksek |
| 9. Limiter | Aşırı koruma | <0.1ms | Kritik |
| 10. Loudness | LUFS hedefleme | <0.5ms | Orta |
| 11. Crossover | Frekans bandı ayırma | <0.1ms | Yüksek |
| 12. Delay | Kanal gecikme | 0–1000ms | Orta |
| 13. Reverb | Uzamsal efekt | <5ms | Düşük |
| 14. Output Gain | Çıkış seviye ayarı | <0.01ms | Yüksek |
| 15. Output Routing | Çıkış yönlendirme | <0.01ms | Yüksek |

**Toplam Pipeline Gecikmesi:** < 10ms (ASIO buffer: 512 sample @ 48kHz ≈ 10.67ms)

---

## 3. Equalizer (Ekvalizör)

### 3.1 Graphic Equalizer

| Bant Sayısı | Frekans Aralığı | Kullanım |
|-------------|------------------|----------|
| 2 | Bas, Tiz | Basit ton kontrolü |
| 3 | Bas, Orta, Tiz | Genel ayarlama |
| 6 | Geniş bant | Hızlı ayarlama |
| 10 | Orta detay | Orta seviye |
| 15 | İyi detay | Profesyonel |
| **31** | **Tam detay** | **Stüdyo standartı** |

| Parametre | Aralık | Varsayılan |
|-----------|--------|------------|
| Bant Sayısı | 2/3/4/5/6/8/10/15/31 | 31 |
| Frekanslar | Önceden tanımlı (ISO) | ISO 266:2007 |
| Kazanç | -∞ dB ile +18 dB | 0 dB |
| Q Faktörü | Sabit (bandwidth) | 1.0 |

### 3.2 Parametric Equalizer

| Parametre | Aralık | Varsayılan | Çözünürlük |
|-----------|--------|------------|-----------|
| Bant Sayısı | 2–31 | 10 | 1 |
| Frekans | 20Hz–20kHz | Değişken | 0.1Hz |
| Kazanç | -∞ dB ile +18 dB | 0 dB | 0.1dB |
| Q Faktörü | 0.1–10 | 1.0 | 0.01 |

**EQ Tipleri:**
| Tip | Kullanım | Avantaj |
|-----|----------|---------|
| Peak (Bell) | Belirli frekans | Hassas ayarlama |
| Low Shelf | Düşük frekans | Bas boost/cut |
| High Shelf | Yüksek frekans | Tiz boost/cut |
| Notch | Belirli frekans kesme | Gürültü temizleme |
| All Pass | Faz düzeltme | Room correction |

---

## 4. Compressor (Sıkıştırıcı)

### 4.1 Temel Parametreler

| Parametre | Aralık | Varsayılan | Açıklama |
|-----------|--------|------------|----------|
| Threshold | -80 dB ile 0 dB | -20 dB | Sıkıştırma başlangıcı |
| Ratio | 1:1 ile ∞:1 | 4:1 | Sıkıştırma oranı |
| Attack | 0.1ms ile 100ms | 10ms | Eşik aşımında tepki |
| Release | 10ms ile 1000ms | 100ms | Eşik altına düşme süresi |
| Knee | 0 ile 1 | 0.5 | yumuşak geçiş |
| Makeup Gain | 0 ile +24 dB | 0 dB | Seviye telafisi |

### 4.2 Sıkıştırıcı Akışı

```
Sinyal ──▶ {Eşik Aşımı?}
              │
         Evet │ Hayır
              ▼     ▼
     Oran ile Azalt  Doğrudan
              │     │
              ▼     ▼
         Makeup Gain
              │
              ▼
           Çıkış
```

### 4.3 Sıkıştırıcı Tipleri

| Tip | Özellik | Kullanım |
|-----|---------|----------|
| Feed-forward | Eşik öncesi ölçüm | Modern, hassas |
| Feedback | Eşik sonrası ölçüm | Klasik, sıcak |
| Multi-band | Bant bazlı sıkıştırma | Mastering |
| Side-chain | Dış sinyal tetikleme | Ducking, efekt |

---

## 5. Limiter

### 5.1 Limiter Parametreleri

| Parametre | Aralık | Varsayılan |
|-----------|--------|------------|
| Threshold | -∞ dB ile 0 dB | -1 dB |
| Attack | 0.1ms ile 1ms | 0.1ms |
| Release | 10ms ile 500ms | 100ms |
| Ceiling | -0.3 dB ile 0 dB | -0.3 dB |

### 5.2 Limiter Tipleri

| Tip | Özellik | Kullanım |
|-----|---------|----------|
| Brick Wall | Kesin eşik, asla aşılmaz | Son koruma |
| True Peak | Gerçek tepe değeri (inter-sample) | Broadcast |
| Soft Knee | Yumuşak geçiş | Müzik için |
| Multi-band | Bant bazlı limiter | Mastering |

**CoreMusic Varsayılanı:** True Peak limiter, -1 dB threshold, -0.3 dB ceiling.

---

## 6. Crossover (Geçirgen)

### 6.1 Crossover Parametreleri

| Parametre | Değer |
|-----------|-------|
| Topoloji | Linkwitz-Riley 4. nesil |
| Varsayılan Geçiş Frekansı | 80Hz |
| Per-Channel | Bağımsız çıkış |
| Slope | 24dB/oct (LR4) |
| Phase | Doğrusal faz (FIR) veya minimum faz (IIR) |

### 6.2 Crossover Motoru Diyagramı

```
Sinyal ──▶ {Geçiş Noktası 80Hz}
              │
         Düşük Frekans    Yüksek Frekans
              ▼                  ▼
        Subwoofer        Ana Hoparlörler
         (20-120Hz)       (100Hz-20kHz)
```

### 6.3 Crossover Konfigürasyonları

| Konfigürasyon | Geçiş | Hoparlör |
|---------------|-------|----------|
| 2.0 | — | FL/FR |
| 2.1 | 80Hz | FL/FR + Sub |
| 5.1 | 80Hz | FL/FR/C/SL/SR + Sub |
| 7.1 | 80Hz | FL/FR/C/SL/SR/RL/RR + Sub |
| **7.1** | **80Hz** | **FL/FR/C/SL/SR/RL/RR + Sub** |

---

## 7. Filtreler

### 7.1 FIR (Finite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Faz | Doğrusal faz |
| Gecikme | Yüksek (taps sayısına bağlı) |
| Kararlılık | Her zaman kararlı |
| Taps | 32–16384 |
| Kullanım | Room correction, crossover, EQ |

**FIR Avantajları:**
- Doğrusal faz → zaman domeninde bozulma yok
- Her zaman kararlı
- Kesin frekans yanıtı

**FIR Dezavantajları:**
- Yüksek gecikme (taps / sample rate)
- Yüksek hesaplama maliyeti

### 7.2 IIR (Infinite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Faz | Minimum faz |
| Gecikme | Düşük |
| Kararlılık | Koşullu (pole placement) |
| Topoloji | Biquad cascade |
| Kullanım | Real-time EQ, low-latency filtering |

**IIR Avantajları:**
- Düşük gecikme
- Düşük hesaplama maliyeti
- Az taps ile etkili

**IIR Dezavantajları:**
- Faz bozulması
- Kararlılık sorunu olabilir (high Q)

### 7.3 Filtre Tipleri

| Tip | Kullanım | Frekans |
|-----|----------|---------|
| Yüksek Geçiren (HPF) | Bas kesme | 20Hz–1kHz |
| Düşük Geçiren (LPF) | Tiz kesme | 1kHz–20kHz |
| Band Geçiren (BPF) | Belirli frekans | 20Hz–20kHz |
| Band Durduran (BSF) | Belirli frekans kesme | 20Hz–20kHz |
| All Pass | Faz düzeltme | Tam aralık |
| Shelf (Low/High) | Geniş bant boost/cut | 20Hz–20kHz |

---

## 8. FFT Analizi

| Özellik | Değer |
|---------|-------|
| Gerçek Zamanlı | Canlı spektrum gösterimi |
| FFT Boyutu | 1024–16384 nokta |
| Pencere | Hann, Hamming, Blackman-Harris |
| Çözünürlük | Sample Rate / FFT Size |
| Şelale Ekranı | Frekans-zaman intensity |
| Frekans Yanıtı | Magnitüd + faz |
| RTA | Real-Time Analyzer |
| Waterfall | 3D frekans-zaman-güç |

### 8.1 FFT Uygulama Alanları

| Alan | Kullanım |
|------|----------|
| Spektrum Gösterimi | Canlı frekans analizi |
| Room Correction | Oda akustiği ölçümü |
| EQ Oluşturma | Otomatik EQ eğrisi |
| Anomali Tespiti | Çarpma, rezonans |
| Loudness Ölçümü | LUFS hesaplama |

---

## 9. Uzamsal Efektler

### 9.1 Reverb (Yankı)

| Mod | Kullanım | Süre | Ön Yıkama |
|-----|----------|------|-----------|
| Geniş Konser | Büyük konser salonu | 2.5s | 25ms |
| Düğün Salonu | Orta boy salon | 1.8s | 15ms |
| Oda | Küçük oda | 0.8s | 5ms |
| Stüdyo | Akustik stüdyo | 0.5s | 2ms |
| Plaka | Metalik | 1.2s | 0ms |
| Chamber | Yankılı oda | 1.5s | 10ms |

| Parametre | Aralık | Varsayılan |
|-----------|--------|------------|
| Room Size | 0–100% | 50% |
| Damping | 0–100% | 50% |
| Wet/Dry | 0–100% | 20% |
| Pre-delay | 0–100ms | 10ms |
| Diffusion | 0–100% | 70% |
| Decay | 0.1–10s | 1.0s |

### 9.2 Delay (Gecikme)

| Tip | Kullanım | Maksimum |
|-----|----------|----------|
| Mono | Tek kanal gecikme | 1000ms |
| Stereo | İki kanal bağımsız | 1000ms |
| Tap | Çoklu gecikme (slapback) | 1000ms × N |
| Ping-pong | Sağ-sol alternans | 1000ms |
| Tape | Analojik bant simülasyonu | 1000ms |

| Parametre | Aralık | Varsayılan |
|-----------|--------|------------|
| Time | 0–1000ms | 0ms |
| Feedback | 0–95% | 0% |
| Mix | 0–100% | 0% |
| Modulation | 0–100% | 0% |

### 9.3 Room Correction

| Özellik | Açıklama |
|---------|----------|
| Ölçüm | Mikrofon ile oda akustiği ölçümü |
| Analiz | RT60, frekans yanıtı, odak noktaları |
| Düzeltme | FIR/IIR filtre ile oda düzeltme |
| Hedef | Düz frekans yanıtı, minimal resonance |

---

## 10. Loudness Processing

### 10.1 LUFS Hedefleme

| Standart | Hedef LUFS | Kullanım |
|----------|-----------|----------|
| EBU R128 | -23 LUFS | Yayın |
| ATSC A/85 | -24 LUFS | ABD yayın |
| ITU-R BS.1770 | -24 LUFS | Uluslararası |
| Streaming | -14 LUFS | Spotify, Apple Music |
| CoreMusic Varsayılan | -14 LUFS | Genel kullanım |

### 10.2 ReplayGain

| Özellik | Açıklama |
|---------|----------|
| Katalog Tutarlılığı | Tüm şarkılar aynı seviyede |
| Ölçüm | Albüm + şarkı bazlı |
| Uygulama | Oynatma sırasında |
| Hedef | -18 LUFS (şarkı) / -17 LUFS (albüm) |

### 10.3 Loudness War Önleme

| Özellik | Açıklama |
|---------|----------|
| Tespit | Aşırı sıkıştırma algılama |
| Uyarı | LUFS + True Peak monitoring |
| Koruma | Soft limiter ile aşırı engelleme |

---

## 11. DSP Performans Metrikleri

| Metrik | Hedef | Açıklama |
|--------|-------|----------|
| Bellek Tahsisi | Sıfır (zero-allocation) | Runtime heap yasak |
| Kilit | Kilit yok (lock-free) | Atomic operations |
| İstisna | noexcept | ASIO callback |
| Hizalama | cache-line aligned (64 byte) | False sharing önleme |
| İşlem | 32-bit float | IEEE 754 |
| Örnekleme | 48kHz standart | 96k/192k+ desteği |
| SIMD | SSE2/AVX2/NEON | Hızlandırma |
| İş Parçacığı | TIME_CRITICAL | Audio thread |

### 11.1 Performans Optimizasyonları

| Teknik | Kullanım | Kazanç |
|--------|----------|--------|
| SIMD Intrinsics | EQ, filter hesaplama | 4–8× |
| Loop Unrolling | Filtering loop | 1.5–2× |
| Cache Prefetch | Büyük buffer processing | 1.2× |
| Branch Prediction | `[[likely]]`/`[[unlikely]]` | 1.1× |
| Constexpr | Compile-time hesaplama | Runtime sıfır |

---

## 12. C++ Uygulama Kuralları

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|-------------|
| Zero-Allocation | Audio thread'de `malloc()` yasak | Ses takılması / crash |
| Lock-Free | Audio thread'de mutex yasak | Deadlock |
| noexcept | ASIO callback noexcept olmalı | std::terminate |
| alignas(64) | writeHead/readHead atomikleri | False sharing |
| SIMD | SSE2/AVX2/NEON hızlandırma | Yavaş processing |
| Thread Priority | TIME_CRITICAL audio thread | Gecikme |

### 12.1 ASIO Callback Örneği

```cpp
void processAudioBlock(float** output, const float** input,
                       int channels, int samples) noexcept {
    for (int i = 0; i < samples; ++i)
        for (int ch = 0; ch < channels; ++ch) {
            float s = input[ch][i];
            s = dspChain[ch].processEQ(s);        // Aşama 6-7
            s = dspChain[ch].processCompressor(s); // Aşama 8
            s = dspChain[ch].processLimiter(s);    // Aşama 9
            output[ch][i] = s;
        }
}
```

---

## 13. DSP Motoru Modüler Yapısı

```
Girdi Katmanı:   Girdi ──▶ Kazanç ──▶ Gürültü Kapısı
Filtre Katmanı:  HPF ──▶ LPF ──▶ Parametrik EQ ──▶ Grafik EQ
Dinamik Katmanı: Sıkıştırıcı ──▶ Limiter ──▶ Kapı ──▶ Genişletici
Uzamsal Katmanı: Yankı ──▶ Gecikme ──▶ Echo ──▶ Stereo Genişliği
Çıktı Katmanı:   Çıktı Kazancı ──▶ Çıktı Yönlendirmesi ──▶ Geçirgen
```

---

## 14. AI DSP Optimization

### 14.1 AI Destekli DSP Analizi

| Analiz | Veri Kaynağı | Çıktı |
|--------|-------------|-------|
| Otomatik EQ | Frekans yanıtı ölçümü | EQ eğrisi önerisi |
| Oda Analizi | Mikrofon verisi, RT60 | Room correction filtresi |
| Optimizasyon | DSP kullanımı, bellek | Performans iyileştirme |
| Loudness Analizi | LUFS ölçümü | Sıkıştırma/limiter ayarı |
| Anomali Tespiti | Spektral analiz | Rezonans/çarpma düzeltme |

### 14.2 AI Otomatik EQ

| Adım | İşlem |
|------|-------|
| 1 | Oda akustik ölçümü (mikrofon) |
| 2 | Frekans yanıtı analizi |
| 3 | Hedef eğri ile karşılaştırma |
| 4 | Parametrik EQ katsayıları üretme |
| 5 | Doğrulama ölçümü |
| 6 | Kabul/ret |

---

## 15. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP donanım modu (XMOS, JUCE, ASIO) |
| [[decisions/accepted/ADR-025-professional-eq-system]] | Profesyonel EQ sistemi (31-band) |
| [[decisions/accepted/ADR-062-dsp-pipeline-architecture]] | DSP hattı mimarisi |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi |

---

## 16. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/dsp/index]] | DSP motoru |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/driver-framework]] | Sürücü çerçevesi |
| [[projects/NevaEngine/equalizer-system]] | EQ sistemi detayı |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 17 |
| ADR References | 4 |
| ASCII Art Diagrams | 4 (DSP Pipeline, Sıkıştırıcı, Crossover, Modüler Yapı) |
| DSP Stages | 15 (Input → Output) |
| EQ Types | 2 (Graphic + Parametric) |
| Dynamics | 4 (Compressor, Limiter, Gate, Expander) |
| Filters | 6 (HPF, LPF, BPF, BSF, All Pass, Shelf) |
| Spatial Effects | 3 (Reverb, Delay, Room Correction) |
| Loudness Standards | 4 (EBU, ATSC, ITU, Streaming) |
| AI Analysis Types | 5 |
| C++ Rules | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
