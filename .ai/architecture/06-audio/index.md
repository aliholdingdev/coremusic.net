---
type: architecture
category: audio
title: "Audio Architecture — CoreMusic Ses Mimarisi"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Audio Architecture — CoreMusic Ses Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-017-dsp-hardware-mode]] · [[decisions/accepted/ADR-019-per-os-neva-player]] · [[decisions/accepted/ADR-025-professional-eq-system]] · [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

CoreMusic'in ses mimarisi, DSP (Dijital Sinyal İşleme), donanım entegrasyonu, ses akışı (audio pipeline) ve çoklu platform desteği gibi ses ile ilgili tüm bileşenleri barındırır. Bu mimari, C++20 Audio Engine (Neva Engine), XMOS XU316 DSP çipi, PCM3168A DAC ve ASIO/WASAPI sürücülerini kapsar.

---

## 2. Terminoloji

| Terim | Tanım |
|-------|-------|
| **ASIO** | Audio Stream Input/Output — Düşük gecikmeli ses protokolü |
| **WASAPI** | Windows Audio Session API — Windows ses oturum yönetimi |
| **DSP** | Digital Signal Processing — Dijital sinyal işleme |
| **DAC** | Digital-to-Analog Converter — Dijital-analog dönüştürücü |
| **ADC** | Analog-to-Digital Converter — Analog-dijital dönüştürücü |
| **PCM** | Pulse-Code Modulation — Ham ses verisi formatı |
| **FLAC** | Free Lossless Audio Codec — Kayıpsız ses formatı |
| **LFE** | Low Frequency Effects — Subwoofer kanalı (8.1 surround) |
| **JUCE** | Jules' Utility Class Extension — C++ ses framework'ü |
| **XMOS** | XCore mimarisi — USB Audio Class 2.0 DSP çipi |
| **PCM3168A** | 8-kanal DAC — 24-bit, 192kHz, SNR 112dB |
| **PCM5122** | ❌ REDDEDİLMİŞ — Sadece 2 kanal, 8.1 için yetersiz (H001) |
| **Ring Buffer** | Dairesel tampon — Gerçek zamanlı ses veri akışı |
| **Zero-Allocation** | Audio thread'de heap allocation yasak |
| **Lock-Free** | Audio thread'de mutex yasak |

---

## 3. Ses Motoru (Neva Engine)

### 3.1 Motor Özellikleri

| Özellik | Değer | ADR |
|---------|-------|-----|
| Dil | C++20 | ADR-017 |
| Framework | JUCE 8 | ADR-017 |
| Sample Format | Float32 (32-bit) | ADR-017 |
| Sample Rate | 48kHz (standart) | ADR-017 |
| Kanal | 2.0 → 8.1 (7.1 surround) | ADR-038 |
| Latency Hedefi | <10ms (ASIO), <20ms (WASAPI) | ADR-017 |
| DSP Efektleri | EQ, Reverb, Compressor, Limiter | ADR-025 |
| Reverb Modları | Geniş Konser, Düğün Salonu, Oda, Stüdyo | ADR-025 |

### 3.2 C++ Guardrails

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|-------------|
| Zero-Allocation | Audio thread'de `malloc()` yasak | Ses takılması |
| Lock-Free | Audio thread'de mutex yasak | Deadlock |
| noexcept | ASIO callback zorunlu | Exception riski |
| cache-line alignment | `alignas(64)` zorunlu | False sharing |
| constexpr | Buffer boyutu compile-time | Performans |
| [[nodiscard]] | Return value kontrolü | Hata |

### 3.3 ASIO Callback

```cpp
void processAudioBlock(float** output, const float** input,
                       int channels, int samples) noexcept {
    for (int i = 0; i < samples; ++i)
        for (int ch = 0; ch < channels; ++ch) {
            float s = input[ch][i];
            s = dspChain[ch].processEQ(s);
            s = dspChain[ch].processCompressor(s);
            s = dspChain[ch].processLimiter(s);
            output[ch][i] = s;
        }
}
```

### 3.4 Thread & Cache

| Parametre | Değer |
|-----------|-------|
| Audio thread | `THREAD_PRIORITY_TIME_CRITICAL` |
| Normal thread | `THREAD_PRIORITY_NORMAL` |
| writeHead | `alignas(64) std::atomic<size_t>` |
| readHead | `alignas(64) std::atomic<size_t>` |
| Buffer size | 512 sample (64-1024 arası) |

---

## 4. Donanım

### 4.1 Ses Kartı Bileşenleri

| Bileşen | Özellik | Durum |
|---------|---------|-------|
| XMOS XU316 | USB Audio Class 2.0, zero-latency DSP | ✅ |
| PCM3168A | 8-kanal DAC, 24-bit, 192kHz, SNR 112dB | ✅ |
| AK4458 (opsiyonel) | 8-kanal high-end DAC, 32-bit, 768kHz | ✅ |
| PCM5122 | ❌ REDDEDİLMİŞ — Sadece 2 kanal, 8.1 için yetersiz | ❌ H001 |

### 4.2 PCM5122 Reddi (H001)

| Özellik | PCM5122 | PCM3168A | Sonuç |
|---------|---------|---------|-------|
| Kanal sayısı | 2 | 8 | PCM3168A kazanır |
| 8.1 surround | ❌ Desteklemiyor | ✅ Destekliyor | PCM3168A zorunlu |
| Bit depth | 32-bit | 24-bit | PCM5122 daha iyi ama yetersiz |
| SNR | 114dB | 112dB | Benzer |
| Fiyat | Düşük | Orta | PCM3168A daha iyi |

**Kural:** 8.1 surround için PCM5122 KESİNLİKLE KULLANILMAZ. Sadece PCM3168A veya AK4458 kullanılır.

### 4.3 Class AB Amplifikatör

| Parametre | Değer |
|-----------|-------|
| Güç | 100W @ 8Ω |
| THD+N | <0.01% |
| SNR | >100dB |
| DC Offset | ±42V |
| Koruma | Röle tabanlı (>0.5V DC cutoff) |

---

## 5. Ses Sürücüleri

### 5.1 Platform Bazlı Sürücüler

| Platform | Sürücü | Latency | ADR |
|----------|--------|---------|-----|
| Windows | ASIO | <10ms | ADR-017 |
| Windows | WASAPI (Exclusive) | <15ms | ADR-017 |
| Windows | WASAPI (Shared) | <20ms | ADR-017 |
| Linux | ALSA | <10ms | ADR-019 |
| Linux | PipeWire | <15ms | ADR-019 |
| macOS | CoreAudio | <10ms | ADR-019 |
| Raspberry Pi | I2S | <15ms | ADR-019 |

### 5.2 ASIO Kuralları

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|-------------|
| Exclusive Lock | Aynı anda sadece tek uygulama | Sürücü çökmesi |
| Buffer size | 64-1024 sample | Gecikme/artış |
| Sample rate | 48kHz (standart) | Uyumsuzluk |
| Bit depth | 32-bit float | Kalite düşüşü |
| Multi-client | ASIO4ALL ile mümkün | Karışıklık |

### 5.3 WASAPI Kuralları

| Kural | Açıklama |
|-------|----------|
| Exclusive mode | Düşük gecikme, tek uygulama |
| Shared mode | Yüksek gecikme, çoklu uygulama |
| Fallback | ASIO başarısızsa WASAPI'ye geç |
| USB çıkarılabilir | WASAPI otomatik geçiş |

---

## 6. 8.1 Surround (ADR-038)

### 6.1 Kanal Haritası

| # | Kanal | Kısaltma | Frekans |
|---|-------|----------|---------|
| 1 | Front Left | FL | 20Hz–20kHz |
| 2 | Front Right | FR | 20Hz–20kHz |
| 3 | Center | C | 100Hz–8kHz |
| 4 | Surround Left | SL | 100Hz–16kHz |
| 5 | Surround Right | SR | 100Hz–16kHz |
| 6 | Rear Left | RL | 100Hz–16kHz |
| 7 | Rear Right | RR | 100Hz–16kHz |
| 8 | Height Left | HL | 200Hz–16kHz |
| 9 | Height Right | HR | 200Hz–16kHz |
| 10 | Subwoofer (LFE) | SW | 20Hz–120Hz |

### 6.2 Bass Management

| Parametre | Değer |
|-----------|-------|
| Crossover | 80Hz (Linkwitz-Riley 4. nesil) |
| LFE cutoff | 120Hz |
| Phase | 0° (varsayılan) |
| Gain | 0dB (varsayılan) |

### 6.3 Surround Routing

```
Input (2.0/5.1/7.1/8.1)
  → Bass Management (80Hz crossover)
    → Channel Mapping (input → output)
      → DSP Chain (EQ, Compressor, Limiter)
        → Output (8.1)
```

---

## 7. DSP Zinciri

### 7.1 DSP Pipeline

```
Input → EQ (31-band) → Compressor → Limiter → Output
```

| # | DSP | Görev | Parametre |
|---|-----|-------|-----------|
| 1 | EQ | Frekans ayarlama | 31-band parametrik |
| 2 | Compressor | Dinamik aralık | Threshold, ratio, attack, release |
| 3 | Limiter | Peak koruma | Threshold, release |

### 7.2 EQ Sistemi (ADR-025)

| Özellik | Değer | ADR |
|---------|-------|-----|
| Band sayısı | 31 | ADR-025 |
| Tip | Parametrik | ADR-025 |
| Frekans aralığı | 20Hz–20kHz | ADR-025 |
| Q faktörü | 0.1–10 | ADR-025 |
| Gain | -12dB to +12dB | ADR-025 |
| Preset | Kullanıcı tanımlı | ADR-025 |
| AI Auto-EQ | Otomatik ayarlama | ADR-025 |

### 7.3 Reverb Efektleri

| Mod | Kullanım | ADR |
|-----|----------|-----|
| Geniş Konser | Büyük mekan | ADR-025 |
| Düğün Salonu | Orta mekan | ADR-025 |
| Oda | Küçük mekan | ADR-025 |
| Stüdyo | Profesyonel | ADR-025 |

---

## 8. Ses Akışı (Audio Pipeline)

### 8.1 Pipeline Mimarisi

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│ Input Source  │ → │ DSP Chain    │ → │ Output       │
│ (File/Stream) │     │ (EQ/Comp/Lim)│     │ (ASIO/WASAPI)│
└──────────────┘     └──────────────┘     └──────────────┘
```

### 8.2 Buffer Yönetimi

| Parametre | Değer |
|-----------|-------|
| Buffer type | Ring buffer |
| Buffer size | 512 sample (varsayılan) |
| Min buffer | 64 sample |
| Max buffer | 1024 sample |
| Underrun | Fade-out → 50ms sessizlik → restart |

### 8.3 Bit Depth Dönüşümü

| Kaynak | Hedef | Yöntem |
|--------|-------|--------|
| 16-bit | 32-bit float | Zero-padding |
| 24-bit | 32-bit float | Zero-padding |
| 32-bit float | 16-bit | Dithering + truncation |
| 32-bit float | 24-bit | Truncation |

---

## 9. Neva Player (ADR-019)

### 9.1 Player Özellikleri

| Özellik | Değer | ADR |
|---------|-------|-----|
| Platform | Per-OS (Windows, Linux, macOS) | ADR-019 |
| Codec | FLAC, MP3, AAC, WAV, OGG | ADR-019 |
| Video | FFmpeg entegrasyonu | ADR-019 |
| GPU | Hardware acceleration | ADR-019 |
| Streaming | WebRTC/P2P | ADR-019 |

### 9.2 Codec Matrisi

| Codec | Bit Depth | Sample Rate | Kanal | Durum |
|-------|-----------|-------------|-------|-------|
| FLAC | 16/24/32-bit | 44.1–192kHz | 2.0–8.1 | ✅ |
| MP3 | — | 44.1kHz | 2.0 | ✅ |
| AAC | — | 44.1–96kHz | 2.0–5.1 | ✅ |
| WAV | 16/24/32-bit | Herhangi | Herhangi | ✅ |
| OGG | — | 44.1kHz | 2.0 | ✅ |

---

## 10. AI Auto-Download (FLAC Kalitesi)

### 10.1 İndirme Kalitesi

| Format | Bit Depth | Sample Rate | Öncelik |
|--------|-----------|-------------|---------|
| FLAC 24-bit | 24-bit | 48kHz | 1. tercih |
| FLAC 32-bit | 32-bit | 48kHz | 2. tercih |
| FLAC 24-bit/96kHz | 24-bit | 96kHz | 3. tercih |
| MP3 320kbps | — | 44.1kHz | Fallback |

**Kural:** 16-bit FLAC KESİNLİKLE KULLANILMAZ. Minimum 24-bit.

### 10.2 Pipeline

```
YouTube URL → nova-search-engine → deemix PHP port (Deezer FLAC) → 24/32-bit FLAC → coremusic_musics DB metadata
```

---

## 11. Audio Organization (5 Bölüm)

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

---

## 12. Platform Tiers

| Tier | OS | Sürücü | Durum |
|------|-----|--------|-------|
| Tier 1 (Primary) | Windows (XP–11) | ASIO, WASAPI | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian) | ALSA, PipeWire | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | CoreAudio | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64) | I2S | ✅ Destekli |
| Tier 5 | ReactOS | Sınırlı | ⚠️ Experimental |

---

## 13. Servis Haritası

| # | Servis | Port | Protocol | Stack |
|---|--------|------|----------|-------|
| 1 | Audio Service | 9741 | REST | C++20 JUCE |
| 2 | Audio Service (WS) | 9742 | WebSocket | C++20 JUCE |
| 3 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 |
| 5 | Network Audio | — | WebRTC/P2P | C++20 |

---

## 14. Troubleshooting

| Sorun | Belirti | Çözüm | ADR |
|-------|---------|-------|-----|
| ASIO device kaybı | USB kopması | WASAPI fallback → Null Output | ADR-017 |
| Buffer underrun | Ses takılması | Buffer artır, CPU kontrol | — |
| PCM5122 kullanımı | 8.1 surround hatası | PCM3168A kullan | ADR-038 |
| Exclusive lock | ASIO başlatılamıyor | Diğer uygulamayı kapat | ADR-017 |
| DC offset | Amfide koruma rölesi | DC offset filtresi | — |
| EQ boost | Distorsiyon | Gain azalt | ADR-025 |
| Reverb tail | Uzun reverb | Decay azalt | ADR-025 |
| Codec hatası | Dosya oynatılamıyor | Fallback codec | ADR-019 |

---

## 15. Warnings

| # | Uyarı | Kategori | ADR |
|---|-------|----------|-----|
| 1 | **PCM5122 KESİNLİKLE YASAK** — 8.1 için yetersiz (H001) | Kritik | ADR-038 |
| 2 | **Zero-Allocation** — Audio thread'de malloc yasak | Kritik | ADR-017 |
| 3 | **Lock-Free** — Audio thread'de mutex yasak | Kritik | ADR-017 |
| 4 | **ASIO Exclusive Lock** — Tek uygulama | Yüksek | ADR-017 |
| 5 | **16-bit FLAC yasak** — Minimum 24-bit | Yüksek | — |
| 6 | **DC Offset Riski** — >0.5V koruma rölesi | Yüksek | — |
| 7 | **Buffer Underrun** — CPU %100 | Orta | — |
| 8 | **PCM5122 KULLANIMI** — H001 REJECT | Kritik | ADR-038 |
| 9 | **Middleware sırası** — CSP nonce bozulur | Kritik | ADR-010 |
| 10 | **ASIO Multi-client** — Sürücü çökmesi | Yüksek | ADR-017 |

---

## 16. Bağımlılıklar

| Bağımlılık | Tür | Versiyon | Zorunlu mu? |
|------------|-----|---------|-------------|
| C++ | Dil | C++20 | ✅ |
| JUCE | Audio Framework | 8.x | ✅ |
| ASIO SDK | Audio Driver | 2.3.4 | ✅ |
| FFmpeg | Media Processing | Latest | ✅ |
| XMOS | USB Audio | XU316 | ✅ |
| PCM3168A | DAC | — | ✅ |

---

## 17. Limitations

| Kısıt | Açıklama | Çözüm |
|-------|----------|-------|
| ASIO exclusive | Tek uygulama | WASAPI fallback |
| Buffer boyutu | Gecikme.trade-off | Adaptive buffer |
| CPU kullanımı | DSP zinciri yoğun | SIMD optimizasyonu |
| Bellek | Zero-allocation | Stack tahsis |
| Codec desteği | Tüm formatlar değil | FFmpeg fallback |

---

## 18. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v19.0 | 31-band AI auto-EQ | 2026 Q4 |
| v20.0 | Spatial audio (Dolby Atmos) | 2027 Q1 |
| v21.0 | Multi-room audio | 2027 Q2 |
| v22.0 | AI-powered mastering | 2027 Q3 |
| v23.0 | 128-channel support | 2027 Q4 |

## 18.1 Audio Quality Metrics

| Metrik | Hedef | Minimum |
|--------|-------|---------|
| **THD+N** | <0.001% | <0.01% |
| **SNR** | >110dB | >100dB |
| **Frequency Response** | 20Hz-20kHz ±0.5dB | 20Hz-20kHz ±1dB |
| **Channel Separation** | >80dB | >60dB |
| **Latency (ASIO)** | <5ms | <10ms |
| **Latency (WASAPI)** | <10ms | <20ms |

---

## 19. Cross References

| Dosya | Amaç | ADR |
|-------|------|-----|
| [[CLAUDE.md]] | Audio Engine kuralları | ADR-017 |
| [[brain.md]] | C++ kuralları, donanım | ADR-038 |
| [[AGENTS.md]] | Embedded Engineer sorumlulukları | — |
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP hardware | ADR-017 |
| [[decisions/accepted/ADR-019-per-os-neva-player]] | Per-OS player | ADR-019 |
| [[decisions/accepted/ADR-025-professional-eq-system]] | 31-band EQ | ADR-025 |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | PCM3168A | ADR-038 |
| [[electronic/hardware-roadmap]] | Hardware yol haritası | — |
| [[electronic/audio-interface-design]] | Audio interface | — |
| [[projects/NevaEngine/overview]] | Neva Engine | — |

## 21. Audio Division Sorumlulukları

| Division | Görev | Teknoloji |
|----------|-------|-----------|
| **Hardware Division** | DAC/ADC, DSP çipleri, amplifikatör tasarımı | PCM3168A, AK4458, XMOS XU316 |
| **Software Division** | C++ Audio Engine, DSP Engine, Mixer | C++20, JUCE 8, ASIO SDK |
| **Studio Division** | ASIO, WASAPI, kayıt, monitoring, routing | Pro audio drivers |
| **Consumer Division** | Bluetooth, WiFi Audio, müzik oynatma | BLE, mDNS, DLNA |
| **Research Division** | AI DSP, yeni codec, geleceğin audio donanımları | ML, neural codec |

## 22. Audio Workflow

| Aşama | Araç | Çıktı |
|-------|------|-------|
| 1. Requirements | Kullanıcı gereksinimleri | Feature list |
| 2. Design | Mimarisi tasarım | ADR |
| 3. Implementation | C++20 / PHP | Kod |
| 4. Testing | Google Test / PHPUnit | Test sonuçları |
| 5. Integration | Servis entegrasyonu | Çalışan sistem |
| 6. Deployment | Docker / Manual | Production |

---

## 23. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 19.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 23 |
| SSOT Authority | Audio Architecture |
| Last Updated | 2026-08-08 |
| ADR Coverage | ADR-017/019/025/038/039 |
| Audio Channels | 8+1 Surround |
| EQ Bands | 31 |
| Platform Tiers | 5 |
| Reverb Modes | 4 |
| Warnings | 10 |
| Troubleshooting | 8 senaryo |
| Cross References | 11 |
| Audio Divisions | 5 |
| Service Count | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode