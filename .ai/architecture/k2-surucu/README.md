---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K2 Sürücü Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K2: Sürücü Layer

**Katman:** K2 (Donanım Sürücüsü)
**Kapsam:** ASIO, WASAPI, ALSA, PipeWire, CoreAudio, I2S, USB, MIDI
**Sorumlu Agent:** Embedded Engineer / Windows Software Engineer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K2 katmanı, donanım ile yazılım arasındaki köprüyü kuran ses sürücülerini içerir. Bu katman, K1 donanım katmanından ham ses verisini alır ve K3 ses motoruna iletir.

### 1.1 Sürücü Hiyerarşisi

```
K3 (Ses Motoru)
    ↓
K2 (Sürücü)
    ├→ ASIO (Windows - Low-latency)
    ├→ WASAPI (Windows - General)
    ├→ ALSA (Linux - Kernel)
    ├→ PipeWire (Linux - Modern)
    ├→ CoreAudio (macOS)
    ├→ I2S (Donanım)
    ├→ USB Audio (Donanım)
    └→ MIDI (Müzik enstrümanı)
    ↓
K1 (Donanım)
```

---

## 2. Bileşen Haritası

| # | Bileşen | Amaç | Öncelik |
|---|---------|------|---------|
| K2-01 | ASIO Driver | Low-latency Windows audio | HIGH |
| K2-02 | WASAPI Driver | Windows通用 audio | HIGH |
| K2-03 | ALSA Driver | Linux kernel audio | HIGH |
| K2-04 | PipeWire Driver | Linux modern audio | MEDIUM |
| K2-05 | CoreAudio Driver | macOS audio | HIGH |
| K2-06 | I2S Interface | Donanım sinyal arayüzü | HIGH |
| K2-07 | USB Audio Class | USB ses protokolü | HIGH |
| K2-08 | MIDI Interface | Müzik enstrümanı | MEDIUM |
| K2-09 | Bluetooth Audio | Kablosuz ses | LOW |
| K2-10 | DLNA/UPnP | Ağ medya | LOW |
| K2-11 | AirPlay | Apple kablosuz | LOW |
| K2-12 | mDNS | Ağ keşfi | LOW |

---

## 3. ASIO Driver Detayı

### 3.1 ASIO Latency Hesaplama

```
Latency = Buffer Size / Sample Rate

Örnekler:
  512 samples / 48000 Hz = 10.67ms (tek yön)
  256 samples / 48000 Hz = 5.33ms (tek yön)
  128 samples / 48000 Hz = 2.67ms (tek yön)
  64 samples / 48000 Hz = 1.33ms (tek yön)

Toplam gecikme (çift yön): ~2.67ms - 21.33ms
```

### 3.2 ASIO Buffer Yönetimi

```cpp
// ASIO Buffer Switch
void bufferSwitch(long doubleBufferIndex, ASIOBool directProcess) {
    // 1. Input buffer'ı oku
    ASIOInputInfo* inputInfo = &gBufferInfo[doubleBufferIndex];
    float* inputBuffer = (float*)inputInfo->buffers[0];

    // 2. DSP processing
    for (int ch = 0; ch < channels; ++ch) {
        float* channelBuffer = inputBuffer + (ch * bufferSize);
        dspChain[ch].process(channelBuffer, bufferSize);
    }

    // 3. Output buffer'ı yaz
    ASIOOutputInfo* outputInfo = &gBufferInfo[doubleBufferIndex];
    float* outputBuffer = (float*)outputInfo->buffers[0];

    // 4. Buffer swap
    ASIOOutputReady();
}
```

---

## 4. WASAPI Driver Detayı

### 4.1 WASAPI Mod Karşılaştırması

| Özellik | Shared Mode | Exclusive Mode |
|---------|------------|----------------|
| Gecikme | ~15ms | ~3ms |
| Format | PCM, 16/24-bit | PCM, 16/24/32-bit |
| Öncelik | Normal | Yüksek |
| Erişim | Tüm uygulamalar | Tek uygulama |
| Bit-perfect | Hayır | Evet |

### 4.2 WASAPI Akış Diyagramı

```
Application → Audio Client → Audio Session → Audio Engine → Hardware
                                    ↑
                              Event Callback
                                    ↓
                            Buffer Switch Event
```

---

## 5. ALSA Driver Detayı

### 5.1 ALSA PCM Akışı

```c
// ALSA PCM Setup
snd_pcm_t *handle;
snd_pcm_hw_params_t *params;

snd_pcm_open(&handle, "hw:0,0", SND_PCM_STREAM_PLAYBACK, 0);
snd_pcm_hw_params_malloc(&params);
snd_pcm_hw_params_any(handle, params);
snd_pcm_hw_params_set_access(handle, params, SND_PCM_ACCESS_RW_INTERLEAVED);
snd_pcm_hw_params_set_format(handle, params, SND_PCM_FORMAT_FLOAT_LE);
snd_pcm_hw_params_set_channels(handle, params, 8); // 8.1
snd_pcm_hw_params_set_rate_near(handle, params, &rate, 0);
snd_pcm_hw_params_set_period_size_near(handle, params, &periodSize, 0);

snd_pcm_hw_params(handle, params);
snd_pcm_hw_params_free(params);
```

---

## 6. CoreAudio Driver Detayı

### 6.1 CoreAudio Akışı

```
Application → Audio Unit → CoreAudio HAL → Audio Driver → Hardware
                      ↓
              Audio Callback (Real-time)
                      ↓
              processAudioBlock()
```

---

## 7. I2S Interface

### 7.1 I2S Protokolü

```
BCK (Bit Clock): 64fs = 3.072MHz @ 48kHz
LRCK (Word Clock): fs = 48kHz
DATA: Serial data (MSB first)

Frame Structure (32-bit per channel):
  [31:0] Left Channel (24-bit data + 8-bit padding)
  [31:0] Right Channel (24-bit data + 8-bit padding)
```

### 7.2 I2S Pin Bağlantısı

```
XMOS XU316 → PCM3168A:
  X0D00 (BCK)  → BCK
  X0D01 (LRCK) → LRCK
  X0D02 (DATA) → DIN
  X0D03 (SCKI) → SCKI
```

---

## 8. USB Audio Class 2.0

### 8.1 UAC2 Descriptors

| Descriptor | Değer |
|------------|-------|
| USB Version | 2.0 |
| Audio Class | 2.0 |
| Channels | 8 |
| Bit Depth | 24-bit |
| Sample Rates | 44.1k, 48k, 88.2k, 96k, 176.4k, 192k |
| Max Packet Size | 1024 bytes |

---

## 9. Bluetooth Audio

### 9.1 Desteklenen Codec'ler

| Codec | Bitrate | Gecikme | Durum |
|-------|---------|---------|-------|
| SBC | 328 kbps | ~150ms | ✅ Destekli |
| AAC | 256 kbps | ~200ms | ✅ Destekli |
| aptX | 352 kbps | ~70ms | ⚠️ Sınırlı |
| LDAC | 990 kbps | ~200ms | ⚠️ Sınırlı |
| LC3 | 160 kbps | ~30ms | ❌ Gelecek |

---

## 10. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-019 | Per-OS Neva Player |

---

*K2 Sürücü Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
