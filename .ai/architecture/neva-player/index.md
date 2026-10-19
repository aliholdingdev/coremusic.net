---
title: "Neva Player — Media Stack Mimari (AIMP+VLC RE + Araştırma Birleşimi)"
type: architecture
category: neva-player
status: active
date: 2026-10-19
version: 1.0.0
authority: "SSOT: ADR-0105 (accepted, v1.1 — 3 tur / 20 persona debate 2026-10-19) + ADR-019 (Per-OS Player, IAudioBackend)"
related:
  - "reports/neva-re-dump-aimp-vlc.md"
  - "research/neva-player-media-stack.md"
  - "research/neva-player-dil-uyumluluk-matrisi.md"
  - ".decisions/accepted/ADR-019-per-os-neva-player.md"
---

# Neva Player — Media Stack Mimari

> Durum: doküman var, kod iskeleti app-cpp-projects/neva-engine/ (2026-10-19 — benchmark kapısı §5.6 geçmeden IMPLEMENTED etiketi YOK). Bu sayfa RE dump + 69 kaynaklı web araştırması +
> 25 dil matrisinin birleşimidir. Bağlayıcı karar: **ADR-0105 (accepted v1.1)** — debate koşulları ADR §2.2-§2.7 ve §5 kapılarına işlendi (62/124 biquad, demux sorumluluğu, C ABI sürüm sözleşmesi, RT thread topolojisi, zero-copy video, ölçülebilir test kapıları).

## 1. Hedef Özellikler (Kabul Kapısı)

| Özellik | Hedef | Kanıt Kapısı |
|---|---|---|
| Ses format | FLAC/ALAC/WAV/DSF/DFF/DoP (DSD), MP3/Opus/MPC/TTA/WV | bit-perfect + gapless |
| Çözünürlük | 32-bit float DSP · 32-bit/192kHz'e kadar çıkış | WASAPI exclusive loopback ölçüm |
| Bit-perfect | OS mixer bypass, sample-rate mismatch yok | null-test (bit-eşitlik) |
| EQ | 31-band parametrik (124 biquad, double-precision katsayı) | THD+N/SNR ölçümü |
| Video | 4K60, HDR10 (P010/10-bit pipeline), BT.2020/DCI-P3 color | libplacebo tonemap + D3D11 |
| Scaling | jinc/ewa_lanczos/spline36 (madVR sınıfı) + FSR opsiyonel | PSNR/SSIM kıyas |
| A/V sync | es_out- sınıfı clock (VLC referansı) | 4K + 192kHz eşzamanlı stres |
| Hedef OS | Windows ilk (WASAPI exclusive → ASIO fallback), sonra macOS/Linux | ADR-019 fallback zinciri |

## 2. Katmanlı Mimari (ADR-019 uyumlu)

```
┌────────────────────────────────────────────────────────────┐
│ UI Katmanı (C++20/Qt veya platform UI — ADR-0105 §4)       │
├────────────────────────────────────────────────────────────┤
│ Neva Session API (C ABI export — 25 dil FFI kapısı)        │
├──────────────────────────┬─────────────────────────────────┤
│ AUDIO PIPELINE           │ VIDEO PIPELINE                  │
│ demux/decode:            │ demux/decode:                   │
│  · dr_flac/dr_wav (PD)   │  · ffmpeg libavcodec (LGPL)     │
│  · libdsd (MIT, DSD)     │ HW: D3D11VA/DXVA2/MFT          │
│  · mpg123/opus (LGPL/BSD)│ render: libplacebo (LGPL)       │
│  · ffmpeg (opsiyonel)    │  · D3D11 swapchain              │
│                          │  · BT.2390 tonemap, jinc/ewa    │
│ DSP zinciri (Neva core): │  · HDR10 P010 10-bit           │
│  · 31-band RBJ biquad    │ video_clock: es_out-sınıfı      │
│  · Linkwitz-Riley XO     │ sync → audio master clock       │
│  · EBU R128 + true-peak  │                                 │
│  · convolution reverb    │                                 │
│  · lock-free SPSC ring   │                                 │
├──────────────────────────┴─────────────────────────────────┤
│ IAudioBackend (ADR-019):                                   │
│  Windows: WASAPI exclusive (birincil, bit-perfect)         │
│           → ASIO (opsiyonel, sürücü-donanımlı low-latency) │
│           → shared fallback → null (kill-switch)           │
│  macOS: CoreAudio · Linux: PipeWire→ALSA                   │
└────────────────────────────────────────────────────────────┘
```

## 3. RE Dump'dan Öğrenilen Yapısal Dersler

| # | Ders (kanıt) | Neva uygulaması |
|---|---|---|
| 1 | AIMP: decode ≠ cihaz yönetimi ayrı katman (`Aso::Devices::Asio` vs BASS) | `IAudioBackend` sınırı ADR-019 ile aynı — doğrulandı |
| 2 | AIMP: FFmpeg opsiyonel plugin (`aimp_inputFFmpeg`) | ffmpeg çekirdek değil, genişletme — GPL izolasyonu |
| 3 | AIMP: DSD talebi var (`aimp_sacd` container parser) | libdsd ile DSF/DFF/DoP birincil decode |
| 4 | VLC: `libvlc_audio_set_callbacks` ile PCM custom DSP'e aktarılabilir | Opsiyonel "libvlc decode gateway" modu (hızlı başlangıç) |
| 5 | VLC: EQ service'ten bağımsız (EQ + preset + band API ayrı) | EQ ayrı servis, player'dan bağımsız — k3 spec ile uyumlu |
| 6 | VLC: 10-band EQ + 3-band parametric (`libparam_eq`) | Neva 31-band parametrik — bant topolojisi referans |
| 7 | VLC: P010 chroma + D3D11VA 4K/HDR yolu hazır | libplacebo D3D11 yolu endüstri doğrulaması |

## 4. Dil/FFI Stratejisi (matris özeti: 25 dil, ★★★=11)

- **Çekirdek:** C++20 (tek kod tabanı, ADR-019 adapter'ları)
- **UI:** C++20 (Qt/ platform native) — ★★★ paket: C, C++, Rust, Python, C#, Java, Kotlin, Swift, JS/TS, Dart
- **FFI:** tek C ABI yüzeyi (`neva_session_*`) → pybind11/nanobind (Python), CsWinRT/P-Invoke (C#), bindgen/cxx (Rust), JNA (Java)
- **WASM:** "Aşama 2" — float32 + AudioWorklet var ama exclusive/192kHz bit-perfect YOK; hi-res iddiası olmadan yalnız playback
- **IPC:** ayrı süreç UI ↔ engine (\\.\pipe\CoreMusicIPC, windows-core.md referansı) opsiyonel izolasyon yolu

## 5. Lisans Stratejisi (araştırma §7)

Ticari serbest: ffmpeg **LGPL build** (--enable-gpl YOK) · libplacebo **LGPLv2.1+** (dinamik link + atıf) · dr_libs/libdsd **MIT/PD** · libFLAC **BSD** · mpg123 **LGPL** · opus **BSD** · libsoxr **MIT** · libebur128 **MIT** · libsamplerate **BSD-2**.
**YASAK:** JUCE (AGPL) · BASS (shareware €950, AIMP'in yolu) · madVR (kapalı) · ASIO SDK birincil yol OLAMAZ (lisans belirsiz — ⚠️ VERIFICATION REQUIRED: 2025 çift lisans iddiası Steinberg teyidi şart).

## 6. Açık Konular (UNKNOWN)

1. ASIO SDK 2025 lisans değişikliği (GPLv3 OR ticari) doğrulanmadı → ASIO yalnız opsiyonel eklenti, karar teyitten sonra
2. AIMP EQ band sayısı dump'da doğrulanmadı (API mevcut, sayı yok)
3. 124 biquad @192k gerçek benchmark yok (%1-3 tahmini) → kodda doğrulanacak
4. AI upscaling (FSR video modu) olgunluğu → Aşama 2
5. macOS/Linux adaptasyon sırası → Windows bit-perfect kanıtından sonra
