---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K2 Sürücü Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
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
| K2-02 | WASAPI Driver | Windows genel amaçlı audio | HIGH |
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

## Alt Katman Şeması (K2.a.b.c)

> **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** Bağlantı zorunludur: `K2` → `K2.a` (2. katman) → `K2.a.b` (3. katman — her `K2.a` için zorunlu) → `K2.a.b.c` (4. katman — **yalnızca diskte kanıtla** açılır: bir MD başlık satırı veya README tablo satırı; uydurma numara yok). Adlandırma ilkeleri: lowercase-hyphen klasör/dosya adları, belgede K numarası taşınır. Birleşim notu (ADR-024): `k-surucu/` klasörü `k2-surucu/` altına birleşti — eski klasör boş, 14 kanıt dosyasının tamamı bu klasördedir. Bu katmanda hedef: 2. katman **8** · 3. katman **≥6** (fiilen 14) · 4. katman **170** kanıtlı yaprak (fiilen 170).

### K2 Şema Özeti

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K2.1 | Windows Ses Sürücüleri | 2 | 25 | asio-drivers.md, wasapi-exclusive.md |
| K2.2 | Linux Ses Sürücüleri | 2 | 24 | alsa-native.md, pipewire-modern.md |
| K2.3 | macOS Ses Sürücüsü | 1 | 12 | core-audio-macos.md |
| K2.4 | Ağ & Kablosuz Ses Sürücüleri | 2 | 26 | network-audio-drivers.md, bluetooth-a2dp.md |
| K2.5 | Donanım Arayüz Sınıfı | 1 | 14 | usb-audio-class.md |
| K2.6 | Sürücü Yığın Altyapısı | 2 | 23 | driver-stack-mimari.md, buffer-management.md |
| K2.7 | Gecikme Optimizasyonu | 1 | 11 | latency-optimization.md |
| K2.8 | İdari & İndeks | 3 | 35 | README.md, index.md, CLAUDE.md |
| **TOPLAM** | | **14** | **170** | |

### K2.1 — Windows Ses Sürücüleri

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.1.1** | ASIO Sürücüsü (3. katman) | asio-drivers.md — 12 yaprak |
| K2.1.1.1 | Genel Bakış | asio-drivers.md L10 |
| K2.1.1.2 | Teknik Detaylar | asio-drivers.md L14 |
| K2.1.1.3 | ASIO Mimarisi | asio-drivers.md L16 |
| K2.1.1.4 | ASIO Exclusive Mode | asio-drivers.md L24 |
| K2.1.1.5 | Buffer Yönetimi | asio-drivers.md L41 |
| K2.1.1.6 | ASIO Callback Zinciri | asio-drivers.md L61 |
| K2.1.1.7 | Donanım Abstraction | asio-drivers.md L79 |
| K2.1.1.8 | Latency Hesaplama | asio-drivers.md L90 |
| K2.1.1.9 | Hata Yönetimi | asio-drivers.md L105 |
| K2.1.1.10 | API / Arayüz | asio-drivers.md L114 |
| K2.1.1.11 | Performans Metrikleri | asio-drivers.md L154 |
| K2.1.1.12 | Bağımlılıklar | asio-drivers.md L165 |
| **K2.1.2** | WASAPI Exclusive (3. katman) | wasapi-exclusive.md — 13 yaprak |
| K2.1.2.1 | Genel Bakış | wasapi-exclusive.md L10 |
| K2.1.2.2 | Teknik Detaylar | wasapi-exclusive.md L14 |
| K2.1.2.3 | WASAPI Çalışma Modları | wasapi-exclusive.md L16 |
| K2.1.2.4 | Exclusive Mode Implementasyonu | wasapi-exclusive.md L33 |
| K2.1.2.5 | Exclusive Mode Avantajları | wasapi-exclusive.md L51 |
| K2.1.2.6 | Buffer Yönetimi | wasapi-exclusive.md L61 |
| K2.1.2.7 | Windows Audio Session | wasapi-exclusive.md L88 |
| K2.1.2.8 | Format Desteği | wasapi-exclusive.md L99 |
| K2.1.2.9 | Latency Optimizasyonu | wasapi-exclusive.md L113 |
| K2.1.2.10 | Hata Durumları | wasapi-exclusive.md L122 |
| K2.1.2.11 | API / Arayüz | wasapi-exclusive.md L131 |
| K2.1.2.12 | Performans Metrikleri | wasapi-exclusive.md L175 |
| K2.1.2.13 | Bağımlılıklar | wasapi-exclusive.md L185 |

### K2.2 — Linux Ses Sürücüleri

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.2.1** | ALSA Native (3. katman) | alsa-native.md — 13 yaprak |
| K2.2.1.1 | Genel Bakış | alsa-native.md L10 |
| K2.2.1.2 | Teknik Detaylar | alsa-native.md L14 |
| K2.2.1.3 | ALSA Mimarisi | alsa-native.md L16 |
| K2.2.1.4 | PCM Aygıtları | alsa-native.md L30 |
| K2.2.1.5 | Donanım Parametreleri | alsa-native.md L45 |
| K2.2.1.6 | Buffer Yönetimi | alsa-native.md L73 |
| K2.2.1.7 | Period Size (Periyot Boyutu) | alsa-native.md L92 |
| K2.2.1.8 | MMAP Mod Implementasyonu | alsa-native.md L107 |
| K2.2.1.9 | XRUN Yönetimi | alsa-native.md L131 |
| K2.2.1.10 | Hardware Tasarım Dosyaları (HWDEP) | alsa-native.md L150 |
| K2.2.1.11 | API / Arayüz | alsa-native.md L164 |
| K2.2.1.12 | Performans Metrikleri | alsa-native.md L222 |
| K2.2.1.13 | Bağımlılıklar | alsa-native.md L232 |
| **K2.2.2** | PipeWire Modern (3. katman) | pipewire-modern.md — 11 yaprak |
| K2.2.2.1 | Genel Bakış | pipewire-modern.md L10 |
| K2.2.2.2 | Teknik Detaylar | pipewire-modern.md L14 |
| K2.2.2.3 | PipeWire Mimarisi | pipewire-modern.md L16 |
| K2.2.2.4 | SPA Plugin Sistemi | pipewire-modern.md L39 |
| K2.2.2.5 | Grafik İşleme | pipewire-modern.md L69 |
| K2.2.2.6 | Real-Time Zamanlama | pipewire-modern.md L89 |
| K2.2.2.7 | Buffer Yönetimi | pipewire-modern.md L106 |
| K2.2.2.8 | Port ve Donanım Yönetimi | pipewire-modern.md L146 |
| K2.2.2.9 | API / Arayüz | pipewire-modern.md L164 |
| K2.2.2.10 | Performans Metrikleri | pipewire-modern.md L229 |
| K2.2.2.11 | Bağımlılıklar | pipewire-modern.md L239 |

### K2.3 — macOS Ses Sürücüsü

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.3.1** | CoreAudio macOS (3. katman) | core-audio-macos.md — 12 yaprak |
| K2.3.1.1 | Genel Bakış | core-audio-macos.md L10 |
| K2.3.1.2 | Teknik Detaylar | core-audio-macos.md L14 |
| K2.3.1.3 | CoreAudio Mimarisi | core-audio-macos.md L16 |
| K2.3.1.4 | AudioDevice Kullanımı | core-audio-macos.md L44 |
| K2.3.1.5 | AudioUnit Implementasyonu | core-audio-macos.md L67 |
| K2.3.1.6 | Callback Yapısı | core-audio-macos.md L114 |
| K2.3.1.7 | HAL Device Properties | core-audio-macos.md L148 |
| K2.3.1.8 | Zamanlama ve Senkronizasyon | core-audio-macos.md L161 |
| K2.3.1.9 | Output Device Seçimi | core-audio-macos.md L184 |
| K2.3.1.10 | API / Arayüz | core-audio-macos.md L211 |
| K2.3.1.11 | Performans Metrikleri | core-audio-macos.md L266 |
| K2.3.1.12 | Bağımlılıklar | core-audio-macos.md L276 |

### K2.4 — Ağ & Kablosuz Ses Sürücüleri

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.4.1** | Network Audio Drivers (3. katman) | network-audio-drivers.md — 13 yaprak |
| K2.4.1.1 | Genel Bakış | network-audio-drivers.md L10 |
| K2.4.1.2 | Teknik Detaylar | network-audio-drivers.md L14 |
| K2.4.1.3 | Ağ Ses Protokolleri Karşılaştırması | network-audio-drivers.md L16 |
| K2.4.1.4 | Dante Protokolü | network-audio-drivers.md L35 |
| K2.4.1.5 | AVB/TSN Protokolü | network-audio-drivers.md L58 |
| K2.4.1.6 | RAVENNA Protokolü | network-audio-drivers.md L70 |
| K2.4.1.7 | PTP Zamanlama | network-audio-drivers.md L87 |
| K2.4.1.8 | Jitter Buffer | network-audio-drivers.md L111 |
| K2.4.1.9 | Multicast Yönetim | network-audio-drivers.md L135 |
| K2.4.1.10 | Donanım Gereksinimleri | network-audio-drivers.md L145 |
| K2.4.1.11 | API / Arayüz | network-audio-drivers.md L157 |
| K2.4.1.12 | Performans Metrikleri | network-audio-drivers.md L222 |
| K2.4.1.13 | Bağımlılıklar | network-audio-drivers.md L231 |
| **K2.4.2** | Bluetooth A2DP (3. katman) | bluetooth-a2dp.md — 13 yaprak |
| K2.4.2.1 | Genel Bakış | bluetooth-a2dp.md L10 |
| K2.4.2.2 | Teknik Detaylar | bluetooth-a2dp.md L14 |
| K2.4.2.3 | Bluetooth Ses Akışı | bluetooth-a2dp.md L16 |
| K2.4.2.4 | Codec Desteği | bluetooth-a2dp.md L34 |
| K2.4.2.5 | Codec Seçimi | bluetooth-a2dp.md L45 |
| K2.4.2.6 | LDAC Codec Detayları | bluetooth-a2dp.md L68 |
| K2.4.2.7 | LC3 Codec Detayları | bluetooth-a2dp.md L83 |
| K2.4.2.8 | Packet Yapısı | bluetooth-a2dp.md L95 |
| K2.4.2.9 | Buffer Yönetimi | bluetooth-a2dp.md L128 |
| K2.4.2.10 | A2DP State Machine | bluetooth-a2dp.md L157 |
| K2.4.2.11 | API / Arayüz | bluetooth-a2dp.md L179 |
| K2.4.2.12 | Performans Metrikleri | bluetooth-a2dp.md L227 |
| K2.4.2.13 | Bağımlılıklar | bluetooth-a2dp.md L236 |

### K2.5 — Donanım Arayüz Sınıfı

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.5.1** | USB Audio Class 2.0 (3. katman) | usb-audio-class.md — 14 yaprak |
| K2.5.1.1 | Genel Bakış | usb-audio-class.md L10 |
| K2.5.1.2 | Teknik Detaylar | usb-audio-class.md L14 |
| K2.5.1.3 | UAC2 Mimarisi | usb-audio-class.md L16 |
| K2.5.1.4 | Isochronous Transfer | usb-audio-class.md L40 |
| K2.5.1.5 | Adaptive ve Async Modlar | usb-audio-class.md L62 |
| K2.5.1.6 | Clock Source Yönetimi | usb-audio-class.md L76 |
| K2.5.1.7 | Format Desteği | usb-audio-class.md L98 |
| K2.5.1.8 | Endpoint Yapılandırması | usb-audio-class.md L108 |
| K2.5.1.9 | Bandwidth Yönetimi | usb-audio-class.md L122 |
| K2.5.1.10 | Buffer Yönetimi | usb-audio-class.md L138 |
| K2.5.1.11 | Hata Yönetimi | usb-audio-class.md L165 |
| K2.5.1.12 | API / Arayüz | usb-audio-class.md L176 |
| K2.5.1.13 | Performans Metrikleri | usb-audio-class.md L230 |
| K2.5.1.14 | Bağımlılıklar | usb-audio-class.md L240 |

### K2.6 — Sürücü Yığın Altyapısı

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.6.1** | Driver Stack Mimari (3. katman) | driver-stack-mimari.md — 12 yaprak |
| K2.6.1.1 | Genel Bakış | driver-stack-mimari.md L10 |
| K2.6.1.2 | Teknik Detaylar | driver-stack-mimari.md L14 |
| K2.6.1.3 | Çok Katmanlı Yapı | driver-stack-mimari.md L16 |
| K2.6.1.4 | HAL Interface Tanımı | driver-stack-mimari.md L50 |
| K2.6.1.5 | Driver Factory Pattern | driver-stack-mimari.md L91 |
| K2.6.1.6 | Katmanlı Soyutlama | driver-stack-mimari.md L136 |
| K2.6.1.7 | DriverLifecycle | driver-stack-mimari.md L148 |
| K2.6.1.8 | Hata Yönetimi | driver-stack-mimari.md L212 |
| K2.6.1.9 | Plugin Sistemi | driver-stack-mimari.md L262 |
| K2.6.1.10 | API / Arayüz | driver-stack-mimari.md L301 |
| K2.6.1.11 | Performans Metrikleri | driver-stack-mimari.md L354 |
| K2.6.1.12 | Bağımlılıklar | driver-stack-mimari.md L364 |
| **K2.6.2** | Buffer Management (3. katman) | buffer-management.md — 11 yaprak |
| K2.6.2.1 | Genel Bakış | buffer-management.md L10 |
| K2.6.2.2 | Teknik Detaylar | buffer-management.md L14 |
| K2.6.2.3 | Ring Buffer Yapısı | buffer-management.md L16 |
| K2.6.2.4 | Lock-Free Ring Buffer | buffer-management.md L34 |
| K2.6.2.5 | Double Buffering | buffer-management.md L100 |
| K2.6.2.6 | SPSC Queue (Single Producer Single Consumer) | buffer-management.md L178 |
| K2.6.2.7 | Buffer Boyut Optimizasyonu | buffer-management.md L228 |
| K2.6.2.8 | Adapte Buffer Yönetimi | buffer-management.md L260 |
| K2.6.2.9 | API / Arayüz | buffer-management.md L303 |
| K2.6.2.10 | Performans Metrikleri | buffer-management.md L357 |
| K2.6.2.11 | Bağımlılıklar | buffer-management.md L367 |

### K2.7 — Gecikme Optimizasyonu

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.7.1** | Latency Optimization (3. katman) | latency-optimization.md — 11 yaprak |
| K2.7.1.1 | Genel Bakış | latency-optimization.md L10 |
| K2.7.1.2 | Teknik Detaylar | latency-optimization.md L14 |
| K2.7.1.3 | Latency Zinciri | latency-optimization.md L16 |
| K2.7.1.4 | Buffer Boyut Seçimi | latency-optimization.md L42 |
| K2.7.1.5 | Real-Time Scheduling | latency-optimization.md L85 |
| K2.7.1.6 | Donanım Clock Optimizasyonu | latency-optimization.md L135 |
| K2.7.1.7 | Latency Monitoring | latency-optimization.md L180 |
| K2.7.1.8 | Latency Testleri | latency-optimization.md L259 |
| K2.7.1.9 | API / Arayüz | latency-optimization.md L315 |
| K2.7.1.10 | Performans Metrikleri | latency-optimization.md L361 |
| K2.7.1.11 | Bağımlılıklar | latency-optimization.md L371 |

### K2.8 — İdari & İndeks

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K2.8.1** | README Bölümleri (3. katman) | README.md — 21 yaprak |
| K2.8.1.1 | Genel Bakış (§1) | README.md L26 |
| K2.8.1.2 | Sürücü Hiyerarşisi (§1.1) | README.md L30 |
| K2.8.1.3 | Bileşen Haritası (§2) | README.md L50 |
| K2.8.1.4 | ASIO Driver Detayı (§3) | README.md L69 |
| K2.8.1.5 | ASIO Latency Hesaplama (§3.1) | README.md L71 |
| K2.8.1.6 | ASIO Buffer Yönetimi (§3.2) | README.md L85 |
| K2.8.1.7 | WASAPI Driver Detayı (§4) | README.md L111 |
| K2.8.1.8 | WASAPI Mod Karşılaştırması (§4.1) | README.md L113 |
| K2.8.1.9 | WASAPI Akış Diyagramı (§4.2) | README.md L123 |
| K2.8.1.10 | ALSA Driver Detayı (§5) | README.md L135 |
| K2.8.1.11 | ALSA PCM Akışı (§5.1) | README.md L137 |
| K2.8.1.12 | CoreAudio Driver Detayı (§6) | README.md L159 |
| K2.8.1.13 | CoreAudio Akışı (§6.1) | README.md L161 |
| K2.8.1.14 | I2S Interface (§7) | README.md L173 |
| K2.8.1.15 | I2S Protokolü (§7.1) | README.md L175 |
| K2.8.1.16 | I2S Pin Bağlantısı (§7.2) | README.md L187 |
| K2.8.1.17 | USB Audio Class 2.0 (§8) | README.md L199 |
| K2.8.1.18 | UAC2 Descriptors (§8.1) | README.md L201 |
| K2.8.1.19 | Bluetooth Audio (§9) | README.md L214 |
| K2.8.1.20 | Desteklenen Codec'ler (§9.1) | README.md L216 |
| K2.8.1.21 | İlgili ADR'ler (§10) | README.md L228 |
| **K2.8.2** | İndeks (3. katman) | index.md — 10 yaprak |
| K2.8.2.1 | Genel Bakış | index.md L10 |
| K2.8.2.2 | Mimari Konum | index.md L14 |
| K2.8.2.3 | Kapsam ve Kategoriler | index.md L22 |
| K2.8.2.4 | Platform Sürücüleri | index.md L24 |
| K2.8.2.5 | Donanım Arabirimleri | index.md L31 |
| K2.8.2.6 | Mimari Bileşenler | index.md L36 |
| K2.8.2.7 | Temel İlkeler | index.md L41 |
| K2.8.2.8 | Bağımlılıklar | index.md L55 |
| K2.8.2.9 | Performans Metrikleri | index.md L63 |
| K2.8.2.10 | Dosya Haritası | index.md L73 |
| **K2.8.3** | Guardrails (3. katman) | CLAUDE.md — 4 yaprak |
| K2.8.3.1 | Hard Guardrails | CLAUDE.md L16 |
| K2.8.3.2 | Sürücü Öncelik Sırası | CLAUDE.md L25 |
| K2.8.3.3 | Latency Hesaplama | CLAUDE.md L36 |
| K2.8.3.4 | İlgili ADR'ler | CLAUDE.md L45 |

## Kanıt Kataloğu (K2)

> Bu dizin, K2 şemasındaki 170 yaprağın **dosya bazlı** kaynağını listeler. "Yaprak" sütunu dosyanın şemaya giren H2+H3 başlıklarının toplamını, "Kapsanan" sütunu bunlardan şemaya alınanları gösterir (ayrıntı: Alt Katman Şeması bölümü). Satır aralığı = dosyadaki ilk–son başlık satırıdır.

| # | Dosya | Rol | H2 | H3 | Kapsanan Yaprak | Satır Aralığı |
|---|-------|-----|----|----|-----------------|---------------|
| 1 | README.md | Katman özeti + sürücü detayları | 10 | 11 | 21 | L26–L228 |
| 2 | CLAUDE.md | Hard guardrails | 4 | 0 | 4 | L16–L45 |
| 3 | index.md | Mimari indeks | 8 | 7 | 10 | L10–L89 |
| 4 | asio-drivers.md | Windows düşük gecikmeli sürücü | 6 | 7 | 12 | L10–L173 |
| 5 | wasapi-exclusive.md | WASAPI exclusive mod | 6 | 8 | 13 | L10–L193 |
| 6 | alsa-native.md | Linux kernel ALSA | 6 | 8 | 13 | L10–L240 |
| 7 | pipewire-modern.md | Linux modern ses sunucusu | 6 | 6 | 11 | L10–L247 |
| 8 | core-audio-macos.md | macOS CoreAudio | 6 | 7 | 12 | L10–L284 |
| 9 | network-audio-drivers.md | Ağ ses protokolleri | 6 | 8 | 13 | L10–L240 |
| 10 | bluetooth-a2dp.md | Bluetooth A2DP codec | 6 | 8 | 13 | L10–L244 |
| 11 | usb-audio-class.md | USB Audio Class 2.0 | 6 | 9 | 14 | L10–L248 |
| 12 | driver-stack-mimari.md | Sürücü yığın soyutlaması | 6 | 7 | 12 | L10–L372 |
| 13 | buffer-management.md | Ring/double buffer altyapısı | 6 | 6 | 11 | L10–L375 |
| 14 | latency-optimization.md | Gecikme zinciri optimizasyonu | 6 | 6 | 11 | L10–L379 |

**Katalog notları:**

1. **Şema toplamı:** 14 kanıt dosyası → 2. katman 8 · 3. katman 14 (hedef ≥6) · 4. katman 170 yaprak (hedef 170/170).
2. **Bilinçli çıkarım (16 başlık):** 12 "Durum: Implementasyon" şablon satırı (12 içerik dosyası) + index.md "Temel İlkeler" (L41) altındaki 4 numaralı ilke maddesi (L43–L52) ana başlıkta birleştirildi; katalog H2+H3 toplamı 186 → şemaya giren 170.
3. **Birleşim (ADR-024):** k-surucu/ klasörü boş — 14 dosyanın tamamı k2-surucu/ altındadır; şema birleşik klasörü kapsar.
4. **Bağımlılıklar başlıkları şemada kaldı** (12 dosyada): sürücü katmanının alt/üst bağ kanıtı olarak bilinçli olarak yaprak sayıldı.
5. **Kaynak sayımı:** H2/H3 sayıları 2026-09-24 taramasıyla (grep '^## ' ve '^### ') doğrulandı; frontend-restructuring-plan.md §2.1-2.2 satırları bu katmanda gerekmedi (kanıt yeterli).

---

*K2 Sürücü Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
