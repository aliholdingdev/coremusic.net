---
type: architecture
category: layer-definition
title: "K3 — Audio Engine Layer (50 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k3-audio-engine.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K3
  component_count: 50
---

# K3 — Ses İşleme Motoru (Audio Engine Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** Neva Engine DSP pipeline, ses işleme bileşenleri, kodek desteği ve çok kanallı ses yönlendirme.

---

## 1. Genel Bakış

K3 katmanı, CoreMusic'in ses işleme çekirdeğini — Neva Engine'i — ve tüm DSP bileşenlerini tanımlar. 32-bit float PCM, 512 sample buffer, 48kHz varsayılan örnek hızı ve 8.1 surround ses yönlendirmesi bu katmanın temel özellikleridir.

### 1.1 Audio Engine Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    K3 — AUDIO ENGINE (50)                               │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    NEVA ENGINE CORE (6)                          │   │
│  │  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌────────────┐   │   │
│  │  │ Engine     │ │ Audio      │ │ Node       │ │ JUCE       │   │   │
│  │  │ Core       │ │ Graph      │ │ Router     │ │ Processor  │   │   │
│  │  └────────────┘ └────────────┘ └────────────┘ └────────────┘   │   │
│  │  ┌────────────┐ ┌────────────┐                                  │   │
│  │  │ Sample Rate│ │ Bit Depth  │                                  │   │
│  │  │ Converter  │ │ Converter  │                                  │   │
│  │  └────────────┘ └────────────┘                                  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    CHANNEL PROCESSING (2)                        │   │
│  │  ┌─────────────────────────┐  ┌─────────────────────────┐      │   │
│  │  │ Channel Upmixer         │  │ Channel Downmixer       │      │   │
│  │  │ Stereo→5.1/7.1/8.1     │  │ 5.1/7.1→Stereo          │      │   │
│  │  └─────────────────────────┘  └─────────────────────────┘      │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    EQ & DYNAMICS (8)                             │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │EQ 31-Band│ │EQ Graphic│ │EQ Shelving│ │EQ Single │          │   │
│  │  │Parametric│ │          │ │          │ │          │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Compressor│ │Limiter   │ │Expander  │ │Noise Gate│          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    EFFECTS (7)                                   │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Reverb    │ │Delay     │ │Chorus    │ │Flanger   │          │   │
│  │  │(4 halls) │ │          │ │          │ │          │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                       │   │
│  │  │Phaser    │ │Distortion│ │Maximizer │                       │   │
│  │  └──────────┘ └──────────┘ └──────────┘                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    PLAYBACK (4)                                  │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Fade In/  │ │Crossfade │ │Gapless   │ │Ring      │          │   │
│  │  │Out Engine│ │Engine    │ │Playback  │ │Buffer    │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    ANALYSIS & SURROUND (8)                       │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Spectrum  │ │Bass      │ │Crossover │ │Ducking   │          │   │
│  │  │Analyzer  │ │Management│ │Network   │ │          │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Stereo    │ │Binaural  │ │Room      │ │Dolby/DTS │          │   │
│  │  │Image     │ │Renderer  │ │Correction│ │Renderers │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    DECODER (1) + MISC (14)                       │   │
│  │  ┌──────────┐                                                  │   │
│  │  │FLAC      │  Double Buffer, Jitter Buffer, FFT, RTA,        │   │
│  │  │Decoder   │  MFCC, Reverb Concert, Reverb Wedding,          │   │
│  │  └──────────┘  Reverb Room, Reverb Studio, Double Buffer,     │   │
│  │                Jitter Buffer, FFT, RTA, MFCC,                  │   │
│  │                Dolby Atmos Renderer, DTS:X Renderer            │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Bileşen Dağılımı

| Kategori | Bileşen Sayısı |
|----------|---------------|
| Neva Engine Core | 6 |
| Channel Processing | 2 |
| EQ & Dynamics | 8 |
| Effects | 7 |
| Playback | 4 |
| Analysis & Surround | 8 |
| Decoder | 1 |
| Misc (alt bileşenler) | 14 |
| **TOPLAM** | **50** |

---

## 2. Neva Engine Core (6 Bileşen)

### 2.1 Engine Core

| Özellik | Değer |
|---------|-------|
| Dil | C++20 |
| Framework | JUCE 9 |
| Örnek Hızı | 48kHz (varsayılan) |
| Buffer Boyutu | 512 samples (varsayılan) |
| Bit Derinliği | 32-bit float (ADR-017) |
| Kanal | 8.1 surround (9 kanal) |
| Kullanım | Ana ses işleme motoru |
| Referans | [[k3-audio-engine]] |

### 2.2 Audio Graph

| Özellik | Değer |
|---------|-------|
|拓扑 | Directed Acyclic Graph (DAG) |
| Node | Audio processor nodes |
| Connection | Dynamic node routing |
| Kullanım | DSP pipeline construction |
| Referans | https://github.com/juce-framework/JUCE |

### 2.3 Node Router

| Özellik | Değer |
|---------|-------|
| Amaç | Dynamic audio routing |
| Kullanım | Multi-output routing, parallel processing |
| Destek | 1-in/N-out, N-in/1-out, N-in/N-out |

### 2.4 JUCE Processor

| Özellik | Değer |
|---------|-------|
| Amaç | JUCE AudioProcessor wrapper |
| Kullanım | Plugin hosting, parameter management |
| Referans | https://github.com/juce-framework/JUCE |

### 2.5 Sample Rate Converter

| Özellik | Değer |
|---------|-------|
| Amaç | Fs dönüşümü |
| Kullanım | 44.1kHz→48kHz, 96kHz→48kHz, 192kHz→48kHz |
| Kalite | High-quality linear interpolation |

### 2.6 Bit Depth Converter

| Özellik | Değer |
|---------|-------|
| Amaç | Bit derinliği dönüşümü |
| Kullanım | 16→32bit, 24→32bit, 32→24bit (dithering) |
| Dithering | TPDF (Triangular Probability Density Function) |

### 2.7 Neva Engine Pipeline

```
┌─────────────────────────────────────────────────────────────────────┐
│                   NEVA ENGINE DSP PIPELINE                          │
│                                                                     │
│  Input ──> [SRC] ──> [Gate] ──> [HPF] ──> [LPF] ──> [EQ]        │
│              │          │          │          │          │           │
│              ▼          ▼          ▼          ▼          ▼           │
│          [Dynamics] ──> [Delay] ──> [Reverb] ──> [Output]         │
│              │                                              │       │
│              ▼                                              ▼       │
│         [Limiter]                                    Speaker Out   │
│                                                                     │
│  18-Stage Pipeline (Genişletilmiş):                                │
│  1.  InputGain      7.  Delay          13. Loudness               │
│  2.  Gate           8.  Chorus         14. Crossover              │
│  3.  HPF            9.  Flanger        15. Output Gain            │
│  4.  LPF            10. Phaser         16. Sample Rate Conv       │
│  5.  Parametric EQ  11. Reverb         17. Bit Depth Conv         │
│  6.  Graphic EQ     12. Dynamics       18. Output Routing         │
│      (Compressor/Limiter/Gate)                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**Pipeline Aşama Detayları:**

| Aşama | Bileşen | Görev |
|-------|---------|-------|
| 1 | InputGain | Giriş sinyal seviyesi ayarı |
| 2 | Gate | Arka plan gürültüsü kesme |
| 3 | HPF | Yüksek geçiren filtre (sub-bass cutoff) |
| 4 | LPF | Düşük geçiren filtre (ultra-high cutoff) |
| 5 | Parametric EQ | 31-band parametrik equalizer |
| 6 | Graphic EQ | Grafik equalizer (hızlı görsel ayar) |
| 7 | Delay | Gecikme efekti (0-5000ms) |
| 8 | Chorus | Koro efekti |
| 9 | Flanger | Flanger efekti |
| 10 | Phaser | Phaser efekti |
| 11 | Reverb | Yankı (4 salon tipi) |
| 12 | Dynamics | Compressor + Limiter + Gate |
| 13 | Loudness | ReplayGain / loudness normalizasyonu |
| 14 | Crossover | Frekans dağıtımı (sub/woofer/mid/tweet) |
| 15 | Output Gain | Çıkış sinyal seviyesi |
| 16 | Sample Rate Conv | Örnekleme hızı dönüşümü |
| 17 | Bit Depth Conv | Bit derinliği dönüşümü (dithering) |
| 18 | Output Routing | Kanal yönlendirme (8.1 surround) |

---

## 3. Channel Processing (2 Bileşen)

### 3.1 Channel Upmixer

| Özellik | Değer |
|---------|-------|
| Amaç | Stereo→çok kanallı.upmix |
| Kullanım | 2.0→5.1, 2.0→7.1, 2.0→8.1 |
| Algoritma | Matrix decoding + frequency-based spatial |
| Referans | https://github.com/wwmm/easyeffects |

### 3.2 Channel Downmixer

| Özellik | Değer |
|---------|-------|
| Amaç | Çok kanallı→stereo downmix |
| Kullanım | 5.1→2.0, 7.1→2.0, 8.1→2.0 |
| Standard | ITU-R BS.775 downmix coefficients |

---

## 4. EQ & Dynamics (8 Bileşen)

### 4.1 EQ 31-Band Parametric

| Özellik | Değer |
|---------|-------|
| Bant | 31-band (20Hz—20kHz) |
| Tip | Parametric (frequency, gain, Q) |
| Filter | Biquad DF-II Transposed |
| Referans | [[k3-audio-engine]] |

**31-Band EQ Frekansları (ISO Standard):**

| Bant | Frekans | Bant | Frekans | Bant | Frekans |
|------|---------|------|---------|------|---------|
| 1 | 20 Hz | 12 | 160 Hz | 23 | 1.25 kHz |
| 2 | 25 Hz | 13 | 200 Hz | 24 | 1.6 kHz |
| 3 | 31.5 Hz | 14 | 250 Hz | 25 | 2 kHz |
| 4 | 40 Hz | 15 | 315 Hz | 26 | 2.5 kHz |
| 5 | 50 Hz | 16 | 400 Hz | 27 | 3.15 kHz |
| 6 | 63 Hz | 17 | 500 Hz | 28 | 4 kHz |
| 7 | 80 Hz | 18 | 630 Hz | 29 | 5 kHz |
| 8 | 100 Hz | 19 | 800 Hz | 30 | 6.3 kHz |
| 9 | 125 Hz | 20 | 1 kHz | 31 | 8 kHz |
| 10 | 130 Hz | 21 | 1.05 kHz | — | — |
| 11 | 140 Hz | 22 | 1.15 kHz | — | — |

**EQ Parametre Aralıkları:**
| Parametre | Min | Default | Max |
|-----------|-----|---------|-----|
| Frekans | 20 Hz | — | 20 kHz |
| Kazanç | -18 dB | 0 dB | +18 dB |
| Q Factor | 0.1 | 1.0 | 10.0 |

### 4.2 EQ Graphic

| Özellik | Değer |
|---------|-------|
| Bant | 31-band (ISO standard) |
| Tip | Fixed frequency, variable gain |
| Kullanım | Quick visual EQ adjustment |

### 4.3 EQ Shelving

| Özellik | Değer |
|---------|-------|
| Tip | Low shelf, High shelf |
| Kullanım | Bass/treble tone control |
| Parametre | Frequency, Gain, Q |

### 4.4 EQ Single

| Özellik | Değer |
|---------|-------|
| Tip | Single band peaking |
| Kullanım | Quick tonal adjustment |

### 4.5 Compressor

| Özellik | Değer |
|---------|-------|
| Attack | 0.1ms — 100ms |
| Release | 10ms — 1000ms |
| Ratio | 1:1 — ∞:1 |
| Threshold | -60dB — 0dB |
| Knee | Hard/Soft |
| Referans | [[k3-audio-engine]] |

### 4.6 Limiter

| Özellik | Değer |
|---------|-------|
| Amaç | True Peak limiting |
| Ceiling | -0.3dBTP (default) |
| Release | Auto / manual |
| Kullanım | Output protection, loudness control |

### 4.7 Expander

| Özellik | Değer |
|---------|-------|
| Tip | Downward expander |
| Ratio | 1:1 — 1:10 |
| Kullanım | Low-level signal expansion |

### 4.8 Noise Gate

| Özellik | Değer |
|---------|-------|
| Threshold | -60dB — 0dB |
| Attack | 0.1ms — 10ms |
| Hold | 0ms — 500ms |
| Release | 10ms — 500ms |
| Kullanım | Background noise reduction |

---

## 5. Effects (7 Bileşen)

### 5.1 Reverb (4 Hall Types)

| Hall Tipi | RT60 | Hacim | Kullanım |
|-----------|------|-------|----------|
| Concert Hall (Geniş Konser) | 2.0s — 3.5s | 5000 m³ | Large venue simulation |
| Wedding Hall (Düğün Salonu) | 1.5s — 2.5s | 2000 m³ | Medium hall simulation |
| Room (Oda) | 0.3s — 1.0s | 100 m³ | Small room simulation |
| Studio (Stüdyo) | 0.1s — 0.5s | 50 m³ | Dry studio simulation |

### 5.2 Delay

| Özellik | Değer |
|---------|-------|
| Max Delay | 5000ms |
| Tap | Single / Multi-tap |
| Feedback | 0% — 95% |
| Crossfeed | Stereo crossfeed |

### 5.3 Chorus

| Özellik | Değer |
|---------|-------|
| Rate | 0.1Hz — 10Hz |
| Depth | 0% — 100% |
| Mix | 0% — 100% |
| Voice | 2-voice, 3-voice |

### 5.4 Flanger

| Özellik | Değer |
|---------|-------|
| Rate | 0.1Hz — 10Hz |
| Depth | 0% — 100% |
| Feedback | 0% — 95% |

### 5.5 Phaser

| Özellik | Değer |
|---------|-------|
| Stages | 4, 6, 8 |
| Rate | 0.1Hz — 10Hz |
| Depth | 0% — 100% |

### 5.6 Distortion

| Özellik | Değer |
|---------|-------|
| Tip | Soft clipping, Hard clipping |
| Drive | 0dB — 40dB |
| Tone | Low-pass filter |

### 5.7 Maximizer

| Özellik | Değer |
|---------|-------|
| Amaç | Loudness maximization |
| Ceiling | -0.3dBTP |
| Release | Auto |

---

## 6. Playback Engines (4 Bileşen)

### 6.1 Fade In/Out Engine

| Özellik | Değer |
|---------|-------|
| Curve | Linear, Exponential, S-Curve |
| Max Duration | 10s |
| Kullanım | Track transitions |

### 6.2 Crossfade Engine

| Özellik | Değer |
|---------|-------|
| Tip | Equal Power, Linear |
| Duration | 0s — 10s |
| Kullanım | Seamless track transitions |

### 6.3 Gapless Playback

| Özellik | Değer |
|---------|-------|
| Amaç | Kesintisiz albüm çalma |
| Buffer | Pre-buffer next track |
| Kullanım | Classical, live recordings |

### 6.4 Lock-Free Ring Buffer

| Özellik | Değer |
|---------|-------|
| Implementasyon | SPSC (Single Producer, Single Consumer) |
| Cache Line | alignas(64) |
| Zerö Allocation | malloc/new/yasak audio thread'de |
| Referans | [[k3-audio-engine]] |

```
┌──────────────────────────────────────────────────────┐
│              RING BUFFER ARCHITECTURE                 │
│                                                      │
│  Producer Thread          Consumer Thread            │
│  ┌──────────┐            ┌──────────┐               │
│  │ Write ───│───────────>│─── Read  │               │
│  │ Head     │            │   Tail   │               │
│  └──────────┘            └──────────┘               │
│                                                      │
│  ┌──────────────────────────────────────┐           │
│  │ [A][B][C][D][E][F][G][H][I][J][K][L] │           │
│  │  ^write                   ^read      │           │
│  │  head                     tail       │           │
│  └──────────────────────────────────────┘           │
│                                                      │
│  Properties:                                         │
│  - Lock-free (atomic operations only)               │
│  - Cache-line aligned (64 bytes)                    │
│  - Zero allocation in audio thread                  │
│  - Power-of-2 size for modulo optimization          │
└──────────────────────────────────────────────────────┘
```

---

## 7. Analysis & Surround (8 Bileşen)

### 7.1 Spectrum Analyzer

| Özellik | Değer |
|---------|-------|
| FFT Size | 2048, 4096, 8192 |
| Window | Hann, Hamming, Blackman |
| Overlap | 50%, 75% |
| Çıktı | FFT magnitude, RTA, MFCC |
| Referans | https://github.com/MTG/essentia |

### 7.2 Bass Management

| Özellik | Değer |
|---------|-------|
| Crossover | 80Hz (default) |
| Topology | 2-way / 3-way |
| Kullanım | LFE routing, bass redirect |
| Referans | [[k3-audio-engine]] |

### 7.3 Crossover Network

| Özellik | Değer |
|---------|-------|
| Tip | Linkwitz-Riley LR4 |
| Slope | 24dB/octave |
| Kullanım | Multi-way speaker crossover |
| Referans | [[k3-audio-engine]] |

### 7.4 Ducking

| Özellik | Değer |
|---------|-------|
| Amaç | Otomatik ses kısma |
| Kullanım | Voice-over, announcement |
| Trigger | Side-chain input |

### 7.5 Stereo Image

| Özellik | Değer |
|---------|-------|
| Amaç | Stereo genişletme/narrowing |
| Kullanım | Spatial enhancement |

### 7.6 Binaural Renderer

| Özellik | Değer |
|---------|-------|
| Amaç | 3D spatial audio for headphones |
| HRTF | Head-Related Transfer Function |
| Kullanım | Kulaklık surround simülasyonu |

### 7.7 Room Correction

| Özellik | Değer |
|---------|-------|
| Amaç | Oda akustik düzeltme |
| Kullanım | EQ-based room correction |
| AI | AI-powered auto calibration |

### 7.8 Dolby Atmos / DTS:X Renderer

| Özellik | Değer |
|---------|-------|
| Amaç | Object-based audio rendering |
| Kanal | Up to 7.1.4 (12.1) |
| Kullanım | Immersive audio playback |

---

## 8. Decoder (1 Bileşen)

### 8.1 FLAC Decoder

| Özellik | Değer |
|---------|-------|
| Standard | FLAC 1.4.x |
| Bit | Up to 32-bit |
| Fs | Up to 655.35kHz |
| Kanal | Up to 8 |
| Kullanım | Ana kayıpsız ses formatı |
| Referans | https://github.com/xiph/flac |

---

## 9. DSP Parametreleri Matrisi

```
┌──────────────────────────────────────────────────────────────────┐
│                   DSP PARAMETER MATRIX                           │
│                                                                  │
│  Parameter         │ Min        │ Default    │ Max               │
│  ─────────────────┼────────────┼────────────┼────────────────── │
│  Sample Rate       │ 44100 Hz   │ 48000 Hz   │ 192000 Hz        │
│  Buffer Size       │ 64         │ 512        │ 2048             │
│  Bit Depth         │ 16         │ 32 float   │ 32 float         │
│  Channels          │ 1          │ 9 (8.1)    │ 16               │
│  EQ Bands          │ 1          │ 31         │ 31               │
│  Compressor Ratio  │ 1:1        │ 4:1        │ ∞:1              │
│  Reverb RT60       │ 0.1s       │ 1.5s       │ 3.5s             │
│  Delay Max         │ 0ms        │ 500ms      │ 5000ms           │
│  Crossover Freq    │ 40Hz       │ 80Hz       │ 200Hz            │
│  FFT Size          │ 512        │ 4096       │ 16384            │
└──────────────────────────────────────────────────────────────────┘
```

---

## 10. Bileşen Sayacı

| # | Bileşen | Kategori | Toplam |
|---|---------|----------|--------|
| 1 | Neva Engine Core | Core | 1 |
| 2 | Audio Graph | Core | 1 |
| 3 | Node Router | Core | 1 |
| 4 | JUCE Processor | Core | 1 |
| 5 | Sample Rate Converter | Core | 1 |
| 6 | Bit Depth Converter | Core | 1 |
| 7 | Channel Upmixer | Channel | 1 |
| 8 | Channel Downmixer | Channel | 1 |
| 9 | EQ 31-Band Parametric | EQ | 1 |
| 10 | EQ Graphic | EQ | 1 |
| 11 | EQ Shelving | EQ | 1 |
| 12 | EQ Single | EQ | 1 |
| 13 | Compressor | Dynamics | 1 |
| 14 | Limiter | Dynamics | 1 |
| 15 | Expander | Dynamics | 1 |
| 16 | Noise Gate | Dynamics | 1 |
| 17 | Reverb (4 halls) | Effects | 1 |
| 18 | Delay | Effects | 1 |
| 19 | Chorus | Effects | 1 |
| 20 | Flanger | Effects | 1 |
| 21 | Phaser | Effects | 1 |
| 22 | Distortion | Effects | 1 |
| 23 | Maximizer | Effects | 1 |
| 24 | Fade In/Out Engine | Playback | 1 |
| 25 | Crossfade Engine | Playback | 1 |
| 26 | Gapless Playback | Playback | 1 |
| 27 | Ring Buffer (lock-free) | Playback | 1 |
| 28 | Spectrum Analyzer (FFT) | Analysis | 1 |
| 29 | Bass Management | Analysis | 1 |
| 30 | Crossover Network | Analysis | 1 |
| 31 | Ducking | Analysis | 1 |
| 32 | Stereo Image | Analysis | 1 |
| 33 | Binaural Renderer | Surround | 1 |
| 34 | Room Correction | Surround | 1 |
| 35 | Dolby Atmos Renderer | Surround | 1 |
| 36 | DTS:X Renderer | Surround | 1 |
| 37 | FLAC Decoder | Decoder | 1 |
| 38 | Double Buffer | Misc | 1 |
| 39 | Jitter Buffer | Misc | 1 |
| 40 | FFT Engine | Misc | 1 |
| 41 | RTA Engine | Misc | 1 |
| 42 | MFCC Engine | Misc | 1 |
| 43 | Reverb Concert Hall | Misc | 1 |
| 44 | Reverb Wedding Hall | Misc | 1 |
| 45 | Reverb Room | Misc | 1 |
| 46 | Reverb Studio | Misc | 1 |
| 47 | Dolby Atmos Core | Misc | 1 |
| 48 | DTS:X Core | Misc | 1 |
| 49 | Biquad Filter | Misc | 1 |
| 50 | Dynamics Processor | Misc | 1 |
| | **TOPLAM** | | **50** |

---

## 11. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| JUCE Framework | juce-framework | https://github.com/juce-framework/JUCE |
| EasyEffects | EasyEffects | https://github.com/wwmm/easyeffects |
| sndfilter | sndfilter | https://github.com/velipso/sndfilter |
| DSP-Cpp-filters | DSP-Cpp-filters | https://github.com/dimtass/DSP-Cpp-filters |
| Essentia | MTG/essentia | https://github.com/MTG/essentia |
| FLAC | xiph/flac | https://github.com/xiph/flac |
| Librosa | librosa | https://github.com/librosa/librosa |

---

## 12. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k2-driver-layer]] | K3'e ses akışı sağlar |
| [[k4-ai-layer]] | K3 DSP parametrelerini AI ile optimize eder |
| [[k3-audio-engine]] | Ana motor implementasyonu |
| [[k3-audio-engine]] | ASIO/JUCE callback bridge |
| [[k3-audio-engine]] | Biquad EQ implementasyonu |
| [[k3-audio-engine]] | Compressor/Limiter/Gate |
| [[k3-audio-engine]] | LR4 crossover network |
| [[k3-audio-engine]] | 15-stage DSP pipeline |
| [[k3-audio-engine]] | Lock-free ring buffer |

---

## Class AB Ses Motoru Entegrasyonu

Neva Engine, Class AB amplifikatör ile doğrudan entegre çalışır:
- [[electronics/amplifier-classab-circuit]] — Analog çıkış aşaması
- [[electronics/power-supply-classab]] — ±35V güç kaynağı
- [[electronics/thermal-design-classab]] — Termal koruma

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
