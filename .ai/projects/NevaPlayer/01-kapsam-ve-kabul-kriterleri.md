---
title: "Neva Player — Proje Kapsamı ve Kabul Kriterleri"
type: project
category: neva-player
status: active
date: 2026-10-19
updated: 2026-10-20
version: 0.2.3
authority: "ADR-019 (accepted) + ADR-0105 (accepted v1.4.0) + ADR-0106 (accepted v1.1.1) + architecture/neva-player/index.md (v1.1.5)"
---

# Neva Player — Proje Kapsamı (01)

## 1. Vizyon

Aynı müziğin her yerde, kayıpsız ve kesintisiz: 32b/192kHz bit-perfect hi-res ses + 4K/HDR sinema kalitesinde video + internet radyosu, tek C++20 motor (Neva Engine), açık kaynak stack, 25 dil erişimli C ABI.

## 2. Kapsam

**Aşama 1 (Windows, bu ADR döngüsü):**
- Ses decode: FLAC/WAV/MP3/Opus/DSD(DSF/DFF/DoP) + gapless
- WASAPI exclusive output, 32-bit float DSP zinciri
- 31-band parametrik EQ (RBJ, double katsayı) + preset servisi + **kullanıcı-seçimli grafik bant (10/15/20/31, maks 31 — ADR-106)**
- **İnternet radyosu: ICY/Shoutcast v1+v2 özel tuner + HLS (FFmpeg gateway), reconnect backoff, radyo kataloğu (ADR-0106)**
- Video: ffmpeg decode + libplacebo D3D11 render, 4K60 HDR10, jinc/ewa scaling
- A/V sync: audio master clock (radyoda master clock = cihaz)
- C ABI FFI yüzeyi (Python + C# binding ilk iki tüketici)

**Aşama 2:** AI scaling (FSR) · WASM/AudioWorklet (hi-res iddiasız) · libvlc decode gateway opsiyonu · ASIO eklenti (lisans teyidi sonrası) · DASH radyo · macOS CoreAudio / Linux PipeWire (ADR-019)

**Dışında:** transcoding/kodlama (enc_* yok), streaming server, skin/marketplace.

## 3. Kabul Kriterleri

| # | Kriter | Ölçüt | Kapı |
|---|---|---|---|
| AK-1 | Bit-perfect | WASAPI exclusive null-test: bit-eşitlik (yalnız yerel dosya — radyo hariç) | zorunlu |
| AK-2 | EQ kalitesi | THD+N <%0.005 @1kHz · SNR >105dB | ADR-006 |
| AK-3 | Gecikme | <20ms WASAPI exclusive (ASIO <10ms hedef, teyit sonrası) · bütçe + ölçüm: ADR-0105 §2.10 (2×period+blok, blok ≥1024@48k yasak) | ADR-006/019 · **ADR-0105 §5.10** (DAC loopback + CI alt sınırı) |
| AK-4 | Video | 4K60 HDR10, 1 saat stres, drop=0, xrun=0 · **A/V sync: P95 \|A−V\| ≤20ms, kilit ≤100ms, drift ≤5ms/10dk (düzeltme-kapalı)** · **VRAM peak ≤600 MB** (ADR-0105 §2.8 bütçesi; rapor zorunlu alan: GPU model + toplam VRAM + sürücü — §5.6) | zorunlu · ADR-0105 §5.7 + **§5.11** + **§5.6** |
| AK-5 | Format | FLAC/ALAC/WAV/DSF/DFF/DoP/MP3/Opus gapless — **format-özel telafi vektörü:** MP3 LAME/Xing encoder delay/padding · FLAC seektable + ilk/son frame · Opus granule position · DSF/DFF/DoP DoP-marker sürekliliği (DSD'de PCM crossfade YOK; DSD→PCM dönüşümünde PCM-domain telafi); sahne: dosya→dosya + akış→akış (radyo); ölçüm: DAC loopback ±5 ms korelasyon penceresi, sapma >20 ms = FAIL (derived) | zorunlu · **ADR-0105 §5.12** (vektör; §5.4 temel set) |
| AK-6 | Lisans | LGPL dinamik link + MIT/BSD çekirdek; AGPL/BASS yasağı denetimi | compliance |
| AK-7 | FFI | C ABI üzerinden Python + C# smoke test | zorunlu |
| AK-8 | Bellek | RT callback'de zero-allocation (ADR-017) | hard-RT |
| AK-9 | İnternet radyo | Mock ICY sunucuda stream + metadata + reconnect (fake-clock backoff); HLS golden vektör (FFmpeg 9.0.2 pin, skip yasak); SSRF/XSS/playlist-bomb security testleri | ADR-106 §5.1 |
| AK-10 | Seçilebilir EQ | 4×4 preset round-trip (10/15/20/31) + band-count-change click ölçümü (≤0.5 dB kayıp) | ADR-106 §5.1 |
| AK-11 | Hostile corpus | Her decode formatı için ≥1000 sentetik bozuk dosya (header truncation · chunk-size overflow · UTF-16 BOM istismarı · negatif sample count · chunk döngüsü · bozuk seektable): 0 sanitizer crash (ASan/UBSan) · 0 OOB · CI 10 dk süre sınırı | zorunlu · **ADR-0105 §5.13** |

## 4. Ekip / Agent Routing

| Görev | Agent |
|---|---|
| DSP/RT kod | embedded-engineer (+ performance-engineer) |
| radio tuner (AĞ) | embedded-engineer (+ backend-developer curl) |
| backend/k2 sürücü | embedded-engineer (windows-software-engineer WASAPI) |
| video pipeline | developer + performance-engineer |
| mimari denetim | architect + architect-reviewer |
| güvenlik/lisans | security-auditor + compliance-auditor |
| test | qa-engineer + test-writer |

## 5. Bağımlılıklar

- ADR-0105 → 3-tur debate → **accepted v1.4.0 ✓ (2026-10-19 kabul; 2026-10-20 v1.2.x-v1.4.0 revizyonları: VRAM/RAM/gecikme/A-V bütçeleri + kapı 5.6-5.13 — 5.12 gapless format vektörü + 5.13 hostile fuzzing)**
- ADR-106 (radyo + seçilebilir bant) → 4 persona debate → **accepted v1.1 ✓ (2026-10-20)**
- ASIO SDK lisans teyidi (Steinberg) — Aşama 2'ye kadar bekler
- libplacebo D3D11 olgunluk doğrulaması (mpv gpu-next referans)
- lib-matrix R7 SPDX teyidi (libvorbis/opusfile/libFLAC/mpg123/RtMidi/mbedTLS + **FDK-AAC** — ADR-106 eklentisi)

## 6. Durum

RE dump (AIMP 20-band EQ + radyo kataloğu ✓ · VLC dump ✓) · Araştırma 69+112 kaynak ✓ · Dil matrisi 25 dil ✓ · Mimari v1.1.5 ✓ · ADR-0105 accepted ✓ · **ADR-0106 accepted ✓ (2026-10-20)** · **KOD: 0 (PLANNED)**
