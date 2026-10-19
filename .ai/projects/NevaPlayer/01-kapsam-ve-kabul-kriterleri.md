---
title: "Neva Player — Proje Kapsamı ve Kabul Kriterleri"
type: project
category: neva-player
status: active
date: 2026-10-19
version: 0.1.0
authority: "ADR-019 (accepted) + ADR-0105 (draft) + architecture/neva-player/index.md"
---

# Neva Player — Proje Kapsamı (01)

## 1. Vizyon

Aynı müziğin her yerde, kayıpsız ve kesintisiz: 32b/192kHz bit-perfect hi-res ses + 4K/HDR sinema kalitesinde video, tek C++20 motor (Neva Engine), açık kaynak stack, 25 dil erişimli C ABI.

## 2. Kapsam

**Aşama 1 (Windows, bu ADR döngüsü):**
- Ses decode: FLAC/WAV/MP3/Opus/DSD(DSF/DFF/DoP) + gapless
- WASAPI exclusive output, 32-bit float DSP zinciri
- 31-band parametrik EQ (RBJ, double katsayı) + preset servisi
- Video: ffmpeg decode + libplacebo D3D11 render, 4K60 HDR10, jinc/ewa scaling
- A/V sync: audio master clock
- C ABI FFI yüzeyi (Python + C# binding ilk iki tüketici)

**Aşama 2:** AI scaling (FSR) · WASM/AudioWorklet (hi-res iddiasız) · libvlc decode gateway opsiyonu · ASIO eklenti (lisans teyidi sonrası) · macOS CoreAudio / Linux PipeWire (ADR-019)

**Dışında:** transcoding/kodlama (enc_* yok), streaming server, skin/marketplace.

## 3. Kabul Kriterleri

| # | Kriter | Ölçüt | Kapı |
|---|---|---|---|
| AK-1 | Bit-perfect | WASAPI exclusive null-test: bit-eşitlik | zorunlu |
| AK-2 | EQ kalitesi | THD+N <%0.005 @1kHz · SNR >105dB | ADR-006 |
| AK-3 | Gecikme | <20ms WASAPI exclusive (ASIO <10ms hedef, teyit sonrası) | ADR-006/019 |
| AK-4 | Video | 4K60 HDR10, 1 saat stres, drop=0, xrun=0 | zorunlu |
| AK-5 | Format | FLAC/ALAC/WAV/DSF/DFF/DoP/MP3/Opus gapless | zorunlu |
| AK-6 | Lisans | LGPL dinamik link + MIT/BSD çekirdek; AGPL/BASS yasağı denetimi | compliance |
| AK-7 | FFI | C ABI üzerinden Python + C# smoke test | zorunlu |
| AK-8 | Bellek | RT callback'de zero-allocation (ADR-017) | hard-RT |

## 4. Ekip / Agent Routing

| Görev | Agent |
|---|---|
| DSP/RT kod | embedded-engineer (+ performance-engineer) |
| backend/k2 sürücü | embedded-engineer (windows-software-engineer WASAPI) |
| video pipeline | developer + performance-engineer |
| mimari denetim | architect + architect-reviewer |
| güvenlik/lisans | security-auditor + compliance-auditor |
| test | qa-engineer + test-writer |

## 5. Bağımlılıklar

- ADR-0105 draft → 3-tur debate → accepted (kapı: session kapanışı)
- ASIO SDK lisans teyidi (Steinberg) — Aşama 2'ye kadar bekler
- libplacebo D3D11 olgunluk doğrulaması (mpv gpu-next referans)

## 6. Durum

RE dump ✓ · Araştırma 69 kaynak ✓ · Dil matrisi 25 dil ✓ · Mimari ✓ · ADR-0105 draft ✓ · **KOD: 0 (PLANNED)**
