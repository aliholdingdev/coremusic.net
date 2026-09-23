---
title: "K3 Ses Motoru Katmanı"
layer: K3
category: "Ses Motoru"
date: 2026-09-20
---

# K3 Ses Motoru Katmanı

## Genel Bakış

K3 Ses Motoru Katmanı, COREMUSIC'in temel ses işleme bileşenidir. Neva Engine C++20 tabanlı, gerçek zamanlı güvenli (real-time safe) ve kilit-free (lock-free) bir mimari ile tasarlanmıştır. 15 aşamalı DSP zinciri, efektler, analiz ve format desteği sağlar.

## Mimari Konum

```
K0 (Donanım) → K1 (OS) → K2 (Sürücü) → K3 (Ses Motoru) → K4 (Uygulama)
```

K3, K2'den ham ses verisini alır, dijital sinyal işleme (DSP) uygular ve işlenmiş sinyali K2'ye geri iletir.

## Kapsam ve Kategoriler

### Temel Motor
- **Neva Engine Core**: C++20, real-time safe, lock-free
- **DSP Chain**: 15 aşamalı DSP pipeline
- **Mixer/Routing**: Ses karıştırma ve yönlendirme

### Efektler
- **EQ Parametric**: 31 bantlı parametrik EQ
- **Dynamics**: Compressor, limiter, expander
- **Reverb**: Freeverb algoritması
- **Chorus/Delay**: Chorus, delay, flanger, phaser

### Analiz
- **Spectrum Analyzer**: FFT tabanlı spectrum analizi
- **Frequency Response**: Frekans tepkisi ölçümü

### Format Desteği
- **Format Decoder**: FLAC, MP3, AAC, WAV, DSD
- **Stream Buffer**: Jitter buffer, adaptif buffering
- **Playback**: Gapless, crossfade

### İşleme
- **Channel Processing**: Kanal eşleme, downmix
- **Sample Rate Conversion**: SRC algoritmaları
- **Bit Depth Conversion**: Dithering, noise shaping

## Temel İlkeller

### 1. Gerçek Zamanlı Güvenlik
- Bellek ayırma yasak (real-time context'te)
- Kilitlenme (blocking) yasak
- Sistem çağrısı yasak
- Bellek serbest bırakma yasak

### 2. Lock-Free Tasarım
- Tüm kritik yollar lock-free
- SPSC/MPMC queue'lar
- Atomic operations
- Wait-free algoritmalar

### 3. Statelessness
- İşleme stateless olmalı
- Her frame bağımsız işlenebilmeli
- Persistent state minimal tutulmalı

### 4. Numerik Hassasiyet
- Float32/Float64 çift hassasiyet
- Biquad katsayıları double precision
- Denormal sayılar flush-to-zero

## DSP Pipeline

```
┌─────────────────────────────────────────────────────┐
│              15-Aşamalı DSP Pipeline                │
│                                                     │
│  1. Input Gain      → 2. Channel Mapping            │
│  3. Format Convert  → 4. Sample Rate Convert        │
│  5. EQ Parametric   → 6. Dynamics Process           │
│  7. Reverb          → 8. Chorus/Delay               │
│  9. Surround Decode → 10. Mixer/Routing             │
│  11. Master EQ      → 12. Limiter                   │
│  13. Dithering      → 14. Output Gain               │
│  15. Format Output                                       │
└─────────────────────────────────────────────────────┘
```

## Performans Metrikleri

| Metrik | Hedef |
|--------|-------|
| İşleme Latency | < 0.1ms |
| CPU Kullanımı | < 10% (tam kapasite) |
| Maksimum Kanal | 128 |
| Maksimum Örnekleme Hızı | 384kHz |
| Bellek Kullanımı | < 100MB |

## Bağımlılıklar

| Katman | İlişki |
|--------|--------|
| K2 | Ham ses verisini alır/iletir |
| K4 | İşlenmiş sesi sunar |
| K1 | Sistem hizmetlerini kullanır |

## Dosya Haritası

| Dosya | İçerik |
|-------|--------|
| neva-engine-core.md | Ana motor, C++20, real-time safe |
| dsp-chain.md | 15 aşamalı DSP pipeline |
| eq-parametric.md | 31 bantlı parametrik EQ |
| dynamics-compressor.md | Compressor, limiter, expander |
| effects-reverb.md | Freeverb algoritması |
| effects-chorus-delay.md | Chorus, delay, flanger, phaser |
| analysis-spectrum.md | FFT analizi, spectrum analyzer |
| surround-decoder.md | 5.1/7.1/8.1 surround |
| format-decoder.md | FLAC, MP3, AAC, WAV, DSD |
| stream-buffer.md | Jitter buffer, adaptif buffering |
| playback-gapless.md | Gapless, crossfade |
| mixer-routing.md | Mixer, routing matrix |
| channel-processing.md | Kanal mapping, downmix |
| sample-rate-conversion.md | SRC algoritmaları |
| bit-depth-conversion.md | Dithering, noise shaping |

## Durum: Implementasyon

K3 Katmanı, K2 sürücüleri tamamlandıktan sonra implemente edilecektir. Önce Neva Engine Core ve DSP Chain, ardından efektler ve analiz araçları yazılacaktır.
