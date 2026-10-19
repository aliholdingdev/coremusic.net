---
title: "Neva Engine — SDK/Lib Geliştirme Planı (Faz 0–5)"
type: plan
category: audio-engine
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md §19 · §24 — Zero Code Before Plan (ADR-007)"
updated: 2026-10-14
---

# Neva Engine — Geliştirme Planı (Faz 0–5)

**Zorunlu Bağlantılar:** [[00-sdk-lib-envanter]] · [[02-dsp-zincir-mimari]] · [[CLAUDE.md]] · [[brain.md]]

> Plan: 32-bit float profesyonel audio motoru (EQ/limiter/maximizer/reverb/LUFS/RTA)
> için GitHub kaynaklı SDK/lib klonlama + entegrasyon sırası.
> **Bu plan onay gerektirir (ADR-007): onaylanmadan kod yazılmaz.**

## §0 Hedef & Kapsam

| Alan | Değer |
|------|-------|
| Format | Float32 (32-bit) · 48/96/192kHz |
| Gecikme | <10ms (ASIO) · <20ms (WASAPI) |
| Kalite | THD+N <0.005% · SNR >105dB |
| DSP | 31-band EQ · 4 reverb · compressor · true-peak limiter · maximizer · LR4 crossover |
| Metering | LUFS (BS.1770-4) · RTA spectrum · peak/RMS |
| Kanal | 1.0 → 8.1 surround |
| Guardrail | Zero-allocation · lock-free · noexcept · 64-byte align · Google Test ≥80% |

## §1 Faz 0 - Repo Kurulumu (clone listesi)

> **Faz 0 durum (2026-10-14):** ⛔ BAŞLAMADI — `neva.engine/` diskte YOK (ilk clone denemesi
> tool interruption ile iptal, kalıntı 0) · cmake makinede YOK (iskelet yazılınca kurulum notu
> düşülecek) · ASIO SDK zip INDIRİLMEDİ (steinberg EULA manuel olabilir) · mod: **planlama**
> (kullanıcı kararı — kodlama/clone yok).
>
> **OS/dil hedefi netleşti (Q1, 2026-10-15):** **modern dal = min Windows 7 TAM destek**
> (C++20 · JUCE 9 · ASIO <10ms · Win11 SDK'ya kadar) · **legacy dal = Windows XP ayrı modül**
> (eski API: DirectSound/WaveOut, sınırlı özellik seti, ayrı derleme hedefi, ayrı binary).
> Çekirdek dil: modern dal C++20 · XP legacy modülü C++14/17 (XP runtime C++20 library'siz).
> **Player modu (Freelancer doc):** native player **hem GUI hem CLI** (headless servis).
> **64-bit float:** yol haritası — Faz 5+ AR-GE (double precision çekirdek).

Kök dizin: `neva.engine/` (öneri - onay bekliyor; mevcut repo kökünde `bin/`, `shared/` var).

```
neva.engine/
├── vendor/                    # clone edilecek 3. parti
│   ├── JUCE/                  # github.com/juce-framework/JUCE (submodule veya shallow clone)
│   ├── asiosdk/               # Steinberg ASIO SDK (indirme — GitHub'da resmi repo yok, web zip)
│   ├── miniaudio/             # github.com/mackron/miniaudio (fallback I/O)
│   ├── libsamplerate/         # github.com/libsndfile/libsamplerate (SRC)
│   ├── kissfft/               # github.com/mborgerding/kissfft (RTA doğrulama)
│   ├── dr_libs/               # github.com/mackron/dr_libs (decode)
│   ├── airwindows/            # github.com/airwindows/airwindows (MIT referans — klon ama gömme)
│   └── libebur128/            # github.com/jiixyj/libebur128 (LUFS)
├── src/                       # Neva Engine kaynak kodu (Embedded Engineer)
│   ├── engine/                # K3: DSP zinciri
│   ├── drivers/               # K2: ASIO/WASAPI/ALSA backend
│   └── metering/              # LUFS/RTA
├── tests/                     # Google Test
└── CMakeLists.txt             # C++20 · MSVC/Clang/GCC
```

**Clone sırası (bağımlılık yok — tek komut seti):**

```powershell
git clone --depth 1 https://github.com/juce-framework/JUCE.git vendor/JUCE
git clone --depth 1 https://github.com/mackron/miniaudio.git vendor/miniaudio
git clone --depth 1 https://github.com/libsndfile/libsamplerate.git vendor/libsamplerate
git clone --depth 1 https://github.com/mborgerding/kissfft.git vendor/kissfft
git clone --depth 1 https://github.com/mackron/dr_libs.git vendor/dr_libs
git clone --depth 1 https://github.com/airwindows/airwindows.git vendor/airwindows
git clone --depth 1 https://github.com/jiixyj/libebur128.git vendor/libebur128
# ASIO SDK: https://www.steinberg.net/developers/asiosdk-open/ → zip → vendor/asiosdk/
```

> ~~⚠️ ASIO SDK lisans varyantı seçimi (GPLv3 vs tescilli) **kullanıcı kararı** — §4.~~
> ✅ **Kabul edildi (2026-10-14): GPLv3 açık varyant** — §7 karar #1.

## §2 Faz 1 — K2 Sürücü Spike (1–2 hafta)

| Görev | Kabul kriteri | Agent |
|-------|---------------|-------|
| JUCE AudioDeviceManager + ASIO backend | 48kHz/Float32 buffer 64–1024, roundtrip gecikme ölçümü | Embedded |
| WASAPI exclusive fallback | ASIO cihaz yokken otomatik geçiş (ADR-017) | Embedded + win-sw |
| miniaudio bağımsız harness | Aynı callback arayüzü, JUCE dışı bağımsız test | Embedded |
| Loopback gecikme ölçümü | ASIO <10ms kanıtı (ölçüm logu) | Embedded |

## §3 Faz 2 — K3 DSP Çekirdek (3–5 hafta)

| Görev | Referans | Kabul kriteri |
|-------|----------|---------------|
| 31-band parametric EQ (TPT biquad) | JUCE dsp IIR + Airwindows EQ | THD+N <0.005%; ±12dB band |
| Compressor (feed-forward) | Airwindows dynamics | Attack 0.1–100ms, ratio 1:1–20:1 |
| True-Peak Limiter (4x oversample) | Airwindows limiter + BS.1770 TP | -0.1dBTP ceiling, overshoot yok |
| Maximizer (psychoacoustic gain) | Airwindows maximize benzeri | A/B test |
| 4 Reverb modu | JUCE Reverb (Freeverb) + FDN | Konser/Düğün/Oda/Stüdyo (§19) |
| LR4 Crossover 80Hz | JUCE IIR | 8.1 bass management (§9) |
| Gain/Group kontrol | — | Lock-free param atomics |

Ayrıntı: [[02-dsp-zincir-mimari]].

## §4 Faz 3 — Metering + Sanal Ses (2 hafta)

| Görev | Kütüphane | Kabul kriteri |
|-------|-----------|---------------|
| LUFS metering | libebur128 (MIT) | Integrated/ShortTerm/Momentary + TruePeak |
| RTA spectrum | juce::dsp::FFT (+ kissfft doğrulama) | 60-band analyzer, 30fps UI update |
| Sanal ses kartı | WASAPI loopback capture | Tüm uygulama sesini Neva'ya yönlendirme (Windows) |
| Grup/çıkış kontrolü | CoreMusic PHP↔C++ köprüsü | `coremusic_neva` DB preset senkronu |

## §5 Faz 4 — Test & Ölçüm (sürekli)

- Google Test: her DSP bloğu için unit + impulse/THD testi (coverage ≥80% min)
- Ölçüm: gerçek DAC'ta loopback THD+N/SNR testi (K1 donanım + PCM3168A)
- Regression: golden reference WAV'lar (Airwindows karşılaştırmalı)

## §6 Faz 5 — UI & Entegrasyon (CoreMusic web/native)

- EQ/limiter UI: `.ai/ui-design/screens/T07-embedded/` mockup'ları (Gate §13)
- PHP ↔ Engine: `coremusic_neva` (preset, DSP ayarları, yönlendirme matrisi, spektrum)

## §7 Kararlar (Kullanıcı direktifi 2026-10-14 — ÖNERİLER KABUL EDİLDİ)

| # | Karar | Kabul edilen değer | Etki |
|---|-------|--------------------|------|
| 1 | **ASIO SDK lisansı** | ✅ **GPLv3 açık varyant** (steinberg.net/developers/asiosdk-open) | Ürün GPLv3 lisans modeline hizalanır (JUCE AGPLv3 ile tutarlı) |
| 2 | **JUCE lisansı** | ✅ **AGPLv3 uyum** (CoreMusic dağıtım modeli: açık kaynak) | Commercial lisans gerekmiyor |
| 3 | **Kod yolu** | ✅ **`neva.engine/`** kök dizini + `vendor/` klon yapısı | Faz 0 clone bu yapıya göre |

**Not:** Kararlar planlama düzeyindedir (2026-10-14); repo'ya kod taşınması Faz 0–1'de,
onaylı plana göre yapılır (ADR-007 — bu plan zaten onaydır).

## §8 Zaman Çizelgesi (özet)

| Faz | Süre | Çıktı |
|-----|------|-------|
| 0 | 1 gün | vendor/ klon + CMake build |
| 1 | 1–2 hafta | ASIO <10ms spike |
| 2 | 3–5 hafta | DSP zinciri + unit test |
| 3 | 2 hafta | LUFS/RTA + sanal ses |
| 4 | Sürekli | Golden test + ölçüm |
| 5 | 4+ hafta | UI + PHP köprü |

**Authority:** Master Orchestrator · **Status:** ONAY BEKLİYOR (ADR-007)
