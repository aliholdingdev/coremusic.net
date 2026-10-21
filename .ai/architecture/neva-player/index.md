---
title: "Neva Player — Media Stack Mimari (AIMP+VLC RE + Araştırma Birleşimi)"
type: architecture
category: neva-player
status: active
date: 2026-10-19
updated: 2026-10-20
version: 1.1.7
authority: "SSOT: ADR-0105 (accepted, v1.4.0 — 3 tur / 20 persona debate 2026-10-19 + libmpv-hibrit red notu + VRAM/RAM/gecikme/A-V bütçeleri + gapless 5.12/fuzzing 5.13 kapıları 2026-10-20) + ADR-106 (accepted, v1.1.1 — 4 persona debate 2026-10-20: internet radio + seçilebilir bant EQ; VLC frekans teyidi işlendi) + ADR-019 (Per-OS Player, IAudioBackend)"
related:
  - "reports/neva-re-dump-aimp-vlc.md"
  - "research/neva-player-media-stack.md"
  - "research/neva-player-dil-uyumluluk-matrisi.md"
  - ".decisions/accepted/ADR-0105-neva-player-media-stack.md"
  - ".decisions/accepted/ADR-106-internet-radio-and-selectable-band-eq.md"
  - "projects/NevaPlayer/research/radio-and-hires.md"
  - "projects/NevaPlayer/research/lib-matrix.md"
  - "projects/NevaPlayer/research/media-stack-web-verification.md"
  - "projects/NevaPlayer/reverse/aimp-dump.md"
  - "projects/NevaPlayer/reverse/aimp-decompile-dump.md"
  - "projects/NevaPlayer/reverse/vlc-dump.md"
---

# Neva Player — Media Stack Mimari

> Durum: doküman var, kod iskeleti app-cpp-projects/neva-engine/ (2026-10-19 — benchmark kapısı §5.6 geçmeden IMPLEMENTED etiketi YOK). Bu sayfa RE dump + 69 kaynaklı web araştırması +
> 25 dil matrisinin birleşimidir. Bağlayıcı karar: **ADR-0105 (accepted v1.3.0)** — debate koşulları ADR §2.2-§2.7 ve §5 kapılarına işlendi (62/124 biquad, demux sorumluluğu, C ABI sürüm sözleşmesi, RT thread topolojisi, zero-copy video, ölçülebilir test kapıları). **v1.1.0 (2026-10-20): ADR-106 (accepted) ile radyo tuner katmanı + seçilebilir bant EQ eklendi.** **v1.1.2 (2026-10-20): Ghidra full-decompile dump (`aimp-decompile-dump.md`) — 20-band/EQ çekirdeği/ASIO/SOXR bulguları §3 ders 10-13'e işlendi; video "hibrit libmpv fallback" REDDEDİLDİ (ADR-0105 §3).** **v1.1.3 (2026-10-20): 112 kaynaklı web doğrulama turu (`projects/NevaPlayer/research/media-stack-web-verification.md`) — ADR-0105 v1.2.0 revizyonu işlendi: soxr MIT→LGPL düzeltmesi, ASIO dual-license doğrulaması, libdsd UNKNOWN, `--enable-version3`, IAudioClient3 shared fallback, miniaudio #1022 riski. Karar özü DEĞİŞMEDİ.** **v1.1.4 (2026-10-20): VRAM satırı §1'e eklendi (ADR-0105 §2.8 — P010 24,9 MB/kare düzeltmesi, ~400-700 MB bütçe, V0-V5 merdiven); açık #13.** **v1.1.5 (2026-10-20): sistem boşluk taraması — §1'e Gecikme + Sistem RAM satırları, A/V sync satırına sayısal eşik (P95 ≤20 ms); ADR-0105 v1.3.0 (§2.9 RAM bütçesi, §2.10 gecikme bütçesi + L1-L4 merdiven, kapı 5.10 gecikme + 5.11 A/V sync); açık #14. Kalan açık: gapless kapı↔AK-5 format boşluğu (4) + hostile-file fuzzing (5) — sonraki tur.**

## 1. Hedef Özellikler (Kabul Kapısı)

| Özellik | Hedef | Kanıt Kapısı |
|---|---|---|
| Ses format | FLAC/ALAC/WAV/DSF/DFF/DoP (DSD), MP3/Opus/MPC/TTA/WV | bit-perfect + gapless |
| Çözünürlük | 32-bit float DSP · 32-bit/192kHz'e kadar çıkış | WASAPI exclusive loopback ölçüm |
| Bit-perfect | OS mixer bypass, sample-rate mismatch yok (yalnız **yerel dosya** — radyo hariç) | null-test (bit-eşitlik) |
| EQ | 31-band parametrik (124 biquad, double-precision katsayı) + **seçilebilir grafik bant: 10/15/20/31 (maks 31, sabit ISO grid)** | THD+N/SNR ölçümü · 4×4 preset round-trip (ADR-106 Ş6) |
| **İnternet radyo** | **ICY/Shoutcast v1+v2 (özel tuner) + HLS (FFmpeg gateway) · reconnect backoff 1s→30s ±%20** | mock ICY sunucu + chunk-partition invariance + HLS golden vektör (ADR-106 §5.1) |
| Video | 4K60, HDR10 (P010/10-bit pipeline), BT.2020/DCI-P3 color | libplacebo tonemap + D3D11 |
| **GPU bellek (VRAM)** | **4K60 HDR10 bütçe ~400-700 MB (tasarım ≤600 MB peak): DPB 299-398 + FP16 ara RT 0-199 + swapchain 66-100 + LUT ~5 · P010 = **24,9 MB/kare** (NV12 12,4 değil) · tükenme merdiveni V0-V5 (ADR-0105 §2.8) | §5.6 `QueryVideoMemoryInfo` peak ölçümü (derived sayılar — ölçüm otoritedir) |
| Scaling | jinc/ewa_lanczos/spline36 (madVR sınıfı) + FSR opsiyonel | PSNR/SSIM kıyas |
| A/V sync | es_out- sınıfı clock (VLC referansı) · **master clock = cihaz** · **eşik: P95 \|A−V\| ≤20 ms · kilit ≤100 ms · drift ≤5 ms/10 dk (düzeltme-kapalı ölçüm)** | §5.11 timestamp offset ölçümü + §5.7 4K+192kHz eşzamanlı stres (ADR-0105 §2.10-§5.11) |
| **Gecikme** | **uçtan uca <20 ms (WASAPI exclusive)** · bütçe: 2×period + blok ≤20 ms · **blok ≥1024 @48k yasak (21,3 ms)** · aşım merdiveni L1-L4 | §5.10 DAC loopback cross-correlation + CI alt sınırı (ADR-0105 §2.10) |
| **Sistem belleği (RAM)** | **≤400 MB tam yük (ses-only ≤150 MB)**: ring/IR/decode/prefetch/playlist-LRU/UI · zero-copy video host RAM'i şişirmez | §5.7 peak RSS log (ADR-0105 §2.9) |
| Hedef OS | Windows ilk (WASAPI exclusive → ASIO fallback), sonra macOS/Linux | ADR-019 fallback zinciri |

## 2. Katmanlı Mimari (ADR-019 uyumlu)

```
┌────────────────────────────────────────────────────────────┐
│ UI Katmanı (C++20/Qt veya platform UI — ADR-0105 §4)       │
├────────────────────────────────────────────────────────────┤
│ Neva Session API (C ABI export — 25 dil FFI kapısı)        │
├──────────────────────────┬─────────────────────────────────┤
│ AUDIO PIPELINE           │ VIDEO PIPELINE                  │
│ kaynak:                  │ demux/decode:                   │
│  · dosya demux/decode:   │  · ffmpeg libavcodec (LGPL)     │
│   dr_flac/dr_wav (PD)    │ HW: D3D11VA/DXVA2/MFT          │
│   · libdsd (MIT, DSD)    │ render: libplacebo (LGPL)       │
│   · mpg123/opus (LGPL/   │  · D3D11 swapchain              │
│     BSD) · ffmpeg (ops.) │  · BT.2390 tonemap, jinc/ewa    │
│  · RADIO TUNER (ADR-106):│  · HDR10 P010 10-bit           │
│   libcurl+ICY özel parser│ video_clock: es_out-sınıfı      │
│   + HLS (ffmpeg gateway) │ sync → audio master clock       │
│   → decode → soxr → ring │                                 │
│ DSP zinciri (Neva core): │                                 │
│  · RBJ biquad: sabit     │                                 │
│    62/124 allocate,      │                                 │
│    10/15/20/31 bant      │                                 │
│    seçimi = bypass       │                                 │
│    maskesi + ramp        │                                 │
│  · Linkwitz-Riley XO     │                                 │
│  · EBU R128 + true-peak  │                                 │
│  · convolution reverb    │                                 │
│  · lock-free SPSC ring   │                                 │
│  (EQ/limiter = RT        │                                 │
│   callback — ADR-0105    │                                 │
│   §2.5 / ADR-106 §2.2a)  │                                 │
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
| 8 | AIMP: 20-band EQ (`EQPresetsLibrary.ini` Band0..19 — dump 2026-10-19) | Seçilebilir bant üstü emsal (ADR-106 §2.2b); 20-band grid ⚠️ frekans listesi teyidi bekliyor |
| 9 | AIMP: radyo ayrı katalog katmanı (`System\Catalogs-Radio.ini`) + HLS (`bass_hls`) | Neva radio tuner ayrı modül (`neva::radio`), HLS FFmpeg gateway ile (ADR-106 §2.2a) — `bass_hls` kopyalanmaz |
| 10 | **Ghidra decompile: `SetEQBandsArr(StaticArray<double,20>)` + `ResetBands` FastZeroMem(0xa0) = 20 bant × double** (`aimp-decompile-dump.md` §2.1, ev_58b49482/ev_8a671c7d) | 20-band kanıtı artık **pseudocode seviyesinde** — depolama: double dizi + dirty flag + lazy rebuild; Neva 31-band aynı stratejiyi kullanır |
| 11 | **Ghidra decompile: EQ clamp ±15 dB (MinMax 0xc1700000..0x41700000) + preamp dB→DBToLevel→lineer çarpım + bypass flag** (§2.2-2.3) | Neva preamp deseni: bypass flag (gain≈1.0 → skip) + ±15 dB aralık ADR-0105'e eklenebilir |
| 12 | **Ghidra decompile: FFT EQ çekirdeği (`TASOFFTEqualizerCore`, 1024-örnek blok, overlap-add, 10^(dB/20) lineerleşim)** (§2.4) | AIMP EQ **FFT domain** — Neva RBJ biquad alternatifiyle "EQ mode" ikilisi olarak sunulabilir (FFT=düz çizgi, biquad=sıfır-gecikme); karar: benchmark kapısı §5.6 sonrasında |
| 13 | **Ghidra decompile: ASIO çift-tampon (`DoSwitchBuffer` InRange(0,1) + TryEnter + kanal stride)** + **SOXR double-ratio + gecikme defteri** (§3-4) | WASAPI exclusive backend aynı çift-tampon/TryEnter desenini uygular; soxr gecikme defteri gapless/A-V senkron için zorunlu |

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
2. ~~AIMP EQ band sayısı dump'da doğrulanmadı~~ → **KAPANDI (2026-10-19): 20-band** (`EQPresetsLibrary.ini` Band0..19, `projects/NevaPlayer/reverse/aimp-dump.md` §3); kalan boşluk: 20-band'ın **frekans listesi** (yalnız band index'leri dump edildi) → ⚠️ VERIFICATION REQUIRED (ADR-106 §2.2b kural 3)
3. 124 biquad @192k gerçek benchmark yok (%1-3 tahmini) → kodda doğrulanacak
4. AI upscaling (FSR video modu) olgunluğu → Aşama 2
5. macOS/Linux adaptasyon sırası → Windows bit-perfect kanıtından sonra
6. ~~VLC `libequalizer` 10-band frekans listesi (62.5 mi 63 mü)~~ → **KAPANDI (2026-10-20): VLC dump** (`projects/nevaplayer/reverse/vlc-dump.md` §5): VLC 3.0.23 = **10-band** ("Equalizer with 10 bands") · ISO seti (31.25, **62.5**, 125, 250, 500, 1000, 2000, 4000, 8000, 16000) + VLC preset seti (60, 170, 310, 600, 1000, 3000, 6000, 12000, 14000, 16000) · `63` = **0 isabet** → Neva ISO 63'te kalır, VLC preset'leri kendi frekans setiyle okunur (ADR-106 §2.2b kural 3, v1.1.1)
7. 15-band alt küme frekans listesi → ⚠️ VERIFICATION REQUIRED (ADR-106 §2.2b kural 3)
8. FDK-AAC lisans teyidi (radyo AAC decode alternatifi) → lib-matrix R7 SPDX doğrulamasına eklendi (ADR-106 §2.2a kural 9)
9. `Aso::Types::EQ_BANDS` sabit tablosu (AIMP.Shared.dll 0x668390) içerik doğrulaması (20-band frekans listesi — Ghidra decompile dump §6.2, ev_58b49482 üzerinden okunacak) → kalan tek EQ boşluğu
10. WASAPI render döngüsü AIMP.Shared.dll'de YOK (yalnız cihaz tescili §5) → gerçek render sınıfı `AIMP.Runtime.dll` içinde beklenir, decompile EDİLMEDİ
11. `AIMP.exe` (7.1MB Delphi) full decompile → YAPILMADI (kullanıcı hedefi, sonraki oturum)
12. ~~Video motoru "hibrit (libmpv fallback)" seçeneği~~ → **KAPANDI (2026-10-20): REDDEDİLDİ** (ADR-0105 v1.1.1 §3 — mpv GPL-2.0 copyleft + black-box + çift-motor tutarsızlığı; fallback ihtiyacı ADR-019 `IAudioBackend` zinciriyle karşılanır)
13. **VRAM peak ölçümü gerçek GPU'da YAPILMADI** → ADR-0105 §2.8 bütçesi (~400-700 MB) birinci-ilkellerden **türetilmiş (derived)**, §5.6 `QueryVideoMemoryInfo` kapısı henüz çalıştırılmadı; ölçüm bu tabloyu doğrular/revize eder → ⚠️ VERIFICATION REQUIRED (**daraltıldı 2026-10-20 — üçüncü denetim:** artakalan tek eksik **ölçümün kendisi**; ortam artık sabit) · **v1.3.1:** merdiven V0-V5 doğrulama testi §5.6'ya eklendi · AK-4'e VRAM ≤600 MB ölçütü işlendi (kapsam v0.2.2) · **v1.3.2:** **bench atandı** — `coremusic-dev` = GTX 1660 · 6144 MB · sürücü 610.62 (PnP 32.0.16.1062) · Win10 19045 (3 kaynak teyitli: `nvidia-smi` + `qwMemorySize` + PnP) + masaüstü **baseline** (1511 MiB / 1706 MB) + bench≠hedef-sınıf notu (1060 3 GB'ta 600 MB = %19,5) → §2.8/§5.6; NevaPlayer derlenince §5.6 ölçümü bu sabit ortamda yapılır
14. **Gecikme / A-V sync / RAM ölçümü gerçek cihazda YAPILMADI** → ADR-0105 v1.3.0 §2.9 (≤400 MB), §2.10 (≤20 ms), kapı 5.10/5.11 sayıları **derived**; WASAPI sürücü min period (§2.10 UNKNOWN) + standart sync eşik atıfları (ITU-R BT.1359 / EBU R37) doğrulanmadı → ⚠️ VERIFICATION REQUIRED (gerçek DAC + sürücü ölçümü bekleniyor) · ayrıca: gapless kapı 5.4 ↔ AK-5 format boşluğu (DoP/DSF vektörü yok) + hostile-file fuzzing kapısı yok → sonraki tur
