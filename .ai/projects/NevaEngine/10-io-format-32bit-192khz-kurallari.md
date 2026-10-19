---
title: "Neva Engine — I/O Format & Örnekleme Hızı Kuralları (32-bit Float · 192 kHz)"
type: doc
category: audio-engine
version: 1.0.0
status: active
authority: "Kullanıcı direktifi 2026-10-15 — SSOT: bu dosya · CLAUDE.md §19 · 06-gereksinimler R4"
updated: 2026-10-15
---

# Neva Engine — I/O Format & Örnekleme Hızı Kuralları (32-bit Float · 192 kHz)

**Zorunlu Bağlantılar:** [[06-gereksinimler-ve-farkliliklar]] (R3/R4/R18) · [[01-gelistirme-plani]] (§0 Format) · [[02-dsp-zincir-mimari]] · `os/02-windows-wasapi` · `os/03-linux-alsa-pipewire` · `os/04-macos-coreaudio`

## §1 Kullanıcı Direktifi (bağlayıcı — 2026-10-15)

> **"32 bit hem input hem output, en yüksek kHz'e kadar destek — 192 kHz'e kadar vs."**

Bu dosya I/O format sözleşmesinin **SSOT**'dur; çelişkide bu dosya kazanır.

| # | Kural | Değer |
|---|-------|-------|
| **K1** | **Input (capture/loopback)** | **32-bit float** — ham kaynak ≥24-bit tamsa **kayıpsız upcast**; float ise bit-perfect aktarım |
| **K2** | **Internal zincir** | **32-bit float** (R4 — CLAUDE §19; tüm DSP biquad/comp/limiter bu formatta çalışır) |
| **K3** | **Output (render)** | **32-bit float** — cihaz destekliyorsa bit-perfect; desteklemiyorsa **24-bit int → TPDF dither** (düşüş dönüşümünde dither zorunlu) |
| **K4** | **Örnekleme hızı** | Destek listesi: **44.1 / 48 / 88.2 / 96 / 176.4 / 192 kHz** · **üst sınır = 192 kHz** · varsayılan = 48 kHz (CLAUDE §19 standart) |
| **K5** | **Cihaz kapasitesi > spec** | Yazılım 192 kHz'i *destekler*; cihaz sunmuyorsa zincir **cihazın en yüksek yerel SR'sinde** açılır (R18 otomatik tespit) — zorlama yok |
| **K6** | **Bit-perfect kapısı** | Kaynak SR == cihaz SR **ve** format float32 → dönüşüm **yok**; aksi halde tek noktada SRC/dither uygulanır |

## §2 OS/API Format Destek Matrisi (os type/version — OS direktifi 2.0)

| OS (type/version) | API | Input format | Output format | Üst SR | Kanıt |
|---|---|---|---|---|---|
| **Windows 7 (modern dal)** | WASAPI **exclusive** | **Float32** ✓ | **Float32** ✓ (`WAVE_FORMAT_EXTENSIBLE`, 32 bit) | cihaz native (⚠️ 192k cihaza bağlı) | `os/02` §1 `wasapiInitExclusive` — vault kod kanıtı |
| Windows 7 | WASAPI **shared** + loopback | `GetMixFormat()` = OS mix formatı (float olabilir ⚠️) | mix format | OS mix SR (engine SRC uygular) | `os/02` §1 `wasapiInitLoopback` — `GetMixFormat` |
| Windows 7 | **ASIO** | **Float32** ✓ (sürücü beyanı) | **Float32** ✓ | cihaz beyanı | FlexASIO: **default 32-bit float / 20 ms** (09 §3 #3 — README) |
| Windows 7 | KS / MME / DirectSound | KS native ⚠️ · MME/DS 16/24-bit | aynı | cihaz ⚠️ | ⚠️ VERIFICATION REQUIRED (Faz 1 ölçüm) |
| **Windows XP (legacy dal)** | **WASAPI — YOK** (Vista+ ile geldi) | — | — | — | FlexASIO "Vista min" → XP ✗ (09 §3 #3) |
| XP legacy | **ASIO** | **Float32** ✓ (sürücüye bağlı) | **Float32** ✓ | cihaz beyanı | Msvad deseni / ASIO SDK (03 §8) |
| XP legacy | MME / DirectSound / KS | 16/24-bit ağırlıklı ⚠️ | aynı | genelde 48 kHz ⚠️ | ⚠️ VERIFICATION REQUIRED (XP legacy modül — ayrı şartname) |
| Linux (kernel/PipeWire) | ALSA | **`SND_PCM_FORMAT_FLOAT_LE`** ✓ | ✓ | cihaz beyanı | `os/03` §1 `snd_pcm_set_params` — vault kod kanıtı |
| macOS | CoreAudio AUHAL | **Float32** ✓ (`mBitsPerChannel = 32`) | ✓ | cihaz ASBD | `os/04` §1 — vault kod kanıtı |

**Çıkarım:** Win7/Linux/macOS üçü de 32-bit float I/O'yu **yerel** destekler; XP yalnız ASIO hattında float sunar (donanım sürücüsüne bağlı).

## §3 192 kHz — Kanıt Durumu

| Kaynak | İddia | Dayanak |
|---|---|---|
| **[VirtualDrivers/Virtual-Audio-Driver](https://github.com/VirtualDrivers/Virtual-Audio-Driver)** (MIT) | `8-bit/8 kHz → 32-bit/192 kHz` format tablosu | 09 §3 #1 — README (⚠️ Win10 1903+ x64, Win7 taşınabilirliği doğrulanmadı) |
| **VB-Audio VMR** (RE) | `48000/96000/192000 Hz` string'leri | 04 §65 — ⚠️ 192k destek **cihaza bağlı** (VERIFICATION REQUIRED) |
| **FlexASIO** (MIT) | default **32-bit float** / 20 ms | 09 §3 #3 — SR üst sınırı cihaz beyanı |
| CLAUDE §19 (vault spec) | Float32 · 48 kHz standart · <10 ms ASIO | bağlayıcı |
| 01 §0 (plan) | `Float32 (32-bit) · 48/96/192 kHz` | plan tabelası — bu dosya ile hizalı |

**Sonuç:** 192 kHz **cihaz kapasitesi**'dir; Neva tarafı taahhüdü = "192 kHz'e kadar uyar, en yüksek yerel SR'yi R18 ile otomatik seç". Cihaz 192 sunmuyorsa 96 → 48 → 44.1 düşer (K5).

## §4 Buffer / Gecikme Tablosu (tek yön — örnek ÷ SR)

| Buffer | 44.1 kHz | 48 kHz | 88.2 kHz | 96 kHz | 176.4 kHz | **192 kHz** |
|---|---|---|---|---|---|---|
| 128 smp | 2.90 ms | 2.67 ms | 1.45 ms | 1.33 ms | 0.73 ms | **0.67 ms** |
| 256 smp | 5.80 ms | 5.33 ms | 2.90 ms | 2.67 ms | 1.45 ms | **1.33 ms** |
| 512 smp | 11.61 ms | 10.67 ms | 5.80 ms | 5.33 ms | 2.90 ms | **2.67 ms** |
| 1024 smp | 23.22 ms | 21.33 ms | 11.61 ms | 10.67 ms | 5.80 ms | **5.33 ms** |

- **Roundtrip** ≈ in + out + sürücü/hw kuyruğu → R3 (<10 ms ASIO) hesabı bunu içerir.
- **Çıkarım:** yüksek SR = aynı örnekte **yarı gecikme**; 192 kHz'te 512 smp = 2.67 ms/yon → R3 kolay karşılanır, ama CPU/DSP yükü ~4× (48 kHz'e göre) — buffer büyütme kılıfı hazır tutulur (02 §kural: zero-allocation).
- Varsayılan buffer 512 smp @48 kHz ≈ 10.67 ms (brain.md §8 — `os/01` §kayıt ile aynı).

## §5 SRC (Sample Rate Conversion) Kuralı

1. **Zincir SR'si = cihaz SR'si** (native) — DSP zinciri I/O SR'sinde çalışır; ara katmanda gizli SRC **yok**.
2. Kaynak dosya/akış SR'si ≠ cihaz SR'si ise **tek noktada** SRC (I/O sınırında, bir kez) — zincir içinde tekrarlanmaz.
3. Bit-perfect iddiası yalnız K6 koşulunda yapılır; koşul sağlanmıyorsa "bit-perfect" etiketi kullanılmaz.
4. 192 → 48 düşüşü (K5) de aynı kurala tabidir: sınırda tek SRC.

## §6 Doğrulama Kriterleri (Faz 1 ölçüm kapısı)

| # | Test | Kabul |
|---|------|-------|
| V1 | 32f loopback (in↔out) — DC/kare dalga bit-eşitliği | K6 koşulunda **%100 bit eşit** |
| V2 | SR geçiş matrisi 44.1→48→96→192 (cihaz kapasitesi içinde) | yeniden negotiate başarılı, zincir SR = cihaz SR |
| V3 | 24-bit int çıkışa düşüş + TPDF dither | dither yoksa K3 ihlali → RED |
| V4 | 192 kHz'te 512 smp roundtrip | R3 <10 ms (ASIO) |
| V5 | XP legacy: MME/ASIO format envanteri | ⚠️ ayrı XP modül şartnamesi — bu dosyanın kapsamı dışı |

## §7 Açık Maddeler (⚠️ VERIFICATION REQUIRED)

1. WASAPI **shared** mix formatının cihaz bazlı bit derinliği/SR'si (`GetMixFormat` anlık okuma — Faz 1).
2. XP legacy MME/DirectSound gerçek format/sr aralığı (XP modül şartnamesi — 07 mimari diyagram xp dali).
3. 192 kHz cihaz saha yaygınlığı (R18 telemetry'siz — "192'e kadar" taahhüdü cihaz raporlamasına dayanır).
4. KS/DirectKS Exclusive 32f XP+Win7 kapsamları (03-os ks-raw kısa devre — Faz 1).

---

**Status:** K1–K6 BAĞLAYICI (kullanıcı direktifi 2026-10-15) · v1.0.0 · Faz 0/kod YOK
**İlgili:** [[09-open-source-virtual-audio-driver-karsilastirma]] · [[04-reverse-voicemeeter-fxsound]] · [[07-mimari-diyagram-kontrol-yuzeyleri]]
