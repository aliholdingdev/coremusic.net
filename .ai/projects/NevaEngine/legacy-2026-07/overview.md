---
type: technical-spec
project: Neva Engine
module: overview
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — Genel Bakış ve Mimari

## 1. Proje Tanımı

Neva Engine, C++20 ile yazılmış, JUCE framework ve ASIO SDK üzerine inşa edilmiş,
profesyonel ses işleme (DSP) motorudur. Canlı ses, stüdyo kaydı, yayın ve
kişisel dinleme senaryoları için tasarlanmıştır.

### Temel Özellikler

| Özellik | Detay |
|----------|-------|
| Dil | C++20 (concepts, ranges, coroutines) |
| Framework | JUCE 8.x (AudioDeviceManager, DSP modülü) |
| Ses API | ASIO 2.3 (birincil), WASAPI, CoreAudio, JACK, ALSA |
| Lisans | GPLv3 (JUCE + ASIO SDK uyumlu) |
| Platform | Windows 10/11, macOS 13+, Linux (Ubuntu 22.04+) |
| Mimariler | x86-64, ARM64 (Apple Silicon, Raspberry Pi 5) |
| Minimum RAM | 4 GB (headless), 8 GB (GUI + AI modelleri) |
| Depolama | 2 GB (motor + bağımlılıklar), +20 GB (AI modelleri) |

### Ses Kalitesi Hedefleri

| Metrik | Hedef |
|--------|-------|
| Sample Rate | 44.1 kHz - 384 kHz |
| Bit Derinliği | 32-bit float (internal), 64-bit float (master bus) |
| THD+N | < 0.001% @ 1 kHz |
| SNR | > 120 dB (A-ağırlıklı) |
| Dinamik Aralık | > 120 dB |
| Crosstalk | < -100 dB |
| Jitter | < 10 ps (ASIO clock) |
| Latency (ASIO) | < 2 ms (RTL, 32 sample buffer @ 96 kHz) |
| Latency (WASAPI) | < 10 ms (RTL, exclusive mode) |

---

## 2. Neva Ekosistemi

Neva Engine, CoreMusic projesinin 3 bileşenli ekosisteminin orta katmanıdır:

```
┌─────────────────────────────────────────────────────────┐
│  CoreMusic (Web)                                        │
│  music.coremusic.net — PHP 8.4 + Vanilla JS SPA         │
│  Kullanıcı arayüzü, playlist, katalog, auth              │
├─────────────────────────────────────────────────────────┤
│                      ↕ WebSocket (9742)                 │
├─────────────────────────────────────────────────────────┤
│  Neva Engine (DSP)                                      │
│  C++20 JUCE + ASIO — Ses işleme motoru                  │
│  EQ, routing, VST3 hosting, spatial audio, AI           │
├─────────────────────────────────────────────────────────┤
│                      ↕ IPC / Shared Memory              │
├─────────────────────────────────────────────────────────┤
│  Neva Player (Media)                                    │
│  media.coremusic.net — Ses dosyası depolama/sunma        │
│  FLAC, WAV, MP3, AAC streaming                          │
└─────────────────────────────────────────────────────────┘
```

| Proje | Rol | Teknoloji |
|-------|-----|-----------|
| CoreMusic | Web UI, katalog, kullanıcı yönetimi | PHP 8.4, MySQL 8, Vanilla JS |
| Neva Engine | Ses DSP motoru | C++20, JUCE, ASIO |
| Neva Player | Medya depolama ve streaming | Nginx, FFmpeg, PostgreSQL |

---

## 3. Mimari Genel Görünüm

### 3.1 Katmanlı Mimari

```
┌──────────────────────────────────────────┐
│  L3 — ARAYÜZ KATMANI                     │
│  JUCE GUI (opsiyonel) · WebSocket API    │
│  REST API · CLI · System Tray            │
├──────────────────────────────────────────┤
│  L2 — UYGULAMA KATMANI                   │
│  SessionManager · AudioEngine · Router   │
│  PluginManager · SceneManager · Recorder │
├──────────────────────────────────────────┤
│  L1 — DSP KATMANI                        │
│  EQProcessor · Spatializer · Compressor  │
│  ReverbEngine · DelayEngine · GateEngine │
│  AIInference · SampleRateConverter       │
├──────────────────────────────────────────┤
│  L0 — ALTYAPI KATMANI                    │
│  AudioDeviceManager · RingBuffer · FFT   │
│  MIDIManager · NetworkStack · FileIO     │
│  Logger · ConfigManager · CrashHandler   │
└──────────────────────────────────────────┘
```

### 3.2 Thread Modeli

```
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  Audio Thread    │  │  GUI Thread      │  │  Network Thread  │
│  (Real-Time)     │  │  (60 FPS)        │  │  (I/O)           │
│                  │  │                  │  │                  │
│  · ProcessBlock  │  │  · Component     │  │  · WebSocket     │
│  · EQ/DSP chain  │  │    paint()       │  │    handler       │
│  · Buffer swap   │  │  · Meter         │  │  · REST API      │
│  · MIDI callback │  │    updates       │  │  · HTTP server   │
│  · Lock-free ops │  │  · User input    │  │  · TCP/UDP       │
└──────────────────┘  └──────────────────┘  └──────────────────┘
         ↕                      ↕                      ↕
┌──────────────────────────────────────────────────────────┐
│              Lock-Free Ring Buffer (SPSC)                │
│          Audio ↔ GUI · Audio ↔ Network · 3x buffer       │
└──────────────────────────────────────────────────────────┘
```

### 3.3 Sinyal Zinciri

```
Giriş Kaynağı
    │
    ▼
┌─────────────┐
│ Input Stage │  (ASIO/WASAPI/CoreAudio/JACK cihazı)
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│ Routing Matrix  │  (N×M kanal yönlendirme, bus atama)
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│ Bus Processing  │
│ ┌─────────────┐ │
│ │ EQ (31-band)│ │  (Her bus bağımsız zincir)
│ │ Compressor  │ │
│ │ Gate        │ │
│ │ VST3 Chain  │ │
│ │ Spatializer │ │
│ └─────────────┘ │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│ Master Bus      │
│ ┌─────────────┐ │
│ │ Limiter      │ │  (True Peak, -1 dBFS)
│ │ Loudness     │ │  (LUFS, EBU R128)
│ │ Dither       │ │  (TPDF, noise shaping)
│ └─────────────┘ │
└──────┬──────────┘
       │
       ▼
┌─────────────────┐
│ Output Stage    │  (ASIO/WASAPI/CoreAudio/JACK cihazı)
└─────────────────┘
```

---

## 4. API Mimarisi

### 4.1 WebSocket API (Port 9742)

Protokol: JSON-RPC 2.0 over WebSocket
Kimlik doğrulama: API Key (SHA-256 HMAC)
Bağlantı: Kalıcı, bidirectional

```json
{
  "jsonrpc": "2.0",
  "method": "eq.setBand",
  "params": {
    "bandIndex": 10,
    "frequency": 1000,
    "gain": 3.5,
    "q": 1.4,
    "filterType": "peak"
  },
  "id": 1
}
```

### 4.2 REST API (Port 9741)

Protokol: HTTP/1.1 + JSON
Kimlik doğrulama: Bearer Token (JWT)
Rate limit: 100 req/sn

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/status` | GET | Motor durumu |
| `/api/v1/eq/preset` | GET/POST | EQ preset yönetimi |
| `/api/v1/routing/scene` | GET/POST | Routing scene yönetimi |
| `/api/v1/plugin/list` | GET | Plugin listesi |
| `/api/v1/spatial/preset` | GET/POST | Spatial preset yönetimi |
| `/api/v1/midi/profile` | GET/POST | MIDI mapping profili |
| `/api/v1/ai/upscale` | POST | AI ses iyileştirme (batch) |
| `/api/v1/recorder/start` | POST | Kayıt başlat |
| `/api/v1/recorder/stop` | POST | Kayıt durdur |

### 4.3 CLI Komutları

```bash
neva-engine --headless --port 9742           # Headless servis
neva-engine --config config.json             # Özel config
neva-engine --list-devices                   # Ses cihazlarını listele
neva-engine --test-audio --duration 30       # Ses testi
neva-engine --export-preset preset.json      # Preset export
neva-engine --import-preset preset.json      # Preset import
neva-engine --scan-plugins                   # Plugin tarama
neva-engine --version                        # Versiyon bilgisi
```

### 4.4 Yapılandırma (neva-engine.json)

```json
{
  "audio": {
    "deviceType": "ASIO",
    "deviceName": "Focusrite USB ASIO",
    "sampleRate": 48000,
    "bufferSize": 128,
    "inputChannels": 2,
    "outputChannels": 8
  },
  "network": {
    "websocketPort": 9742,
    "restPort": 9741,
    "authRequired": true,
    "apiKeyHash": "$argon2id$v=19$m=65536,t=4,p=2$..."
  },
  "plugins": {
    "scanPaths": [
      "C:/Program Files/Common Files/VST3",
      "/Library/Audio/Plug-Ins/VST3"
    ],
    "blacklist": ["unstable_plugin.vst3"],
    "sandboxEnabled": true
  },
  "ai": {
    "device": "cuda:0",
    "precision": "fp16",
    "maxVRAM": 8192,
    "modelPath": "./models/"
  }
}
```

---

## 5. Headless Servis Modu

### 5.1 Çalışma Modları

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| GUI | Tam JUCE arayüzü | Masaüstü kullanımı |
| Headless | Arka plan servisi | Sunucu, Raspberry Pi |
| System Tray | Minimize edilmiş | Masaüstü arka plan |
| CLI | Komut satırı | Otomasyon, test |

### 5.2 Headless Özellikleri

- Windows Service / macOS LaunchDaemon / Linux systemd unit
- WebSocket + REST API üzerinden tam kontrol
- Sıfır GUI bağımlılığı (JUCE GUI modülü linklenmez)
- RAM kullanımı: < 200 MB (AI modelsiz)
- Auto-restart (watchdog timer)
- Log rotasyonu (günlük, 30 gün)
- Health check endpoint: `/api/v1/health`

---

## 6. Geliştirme Fazları

### F1 — Core Audio (6 ay)
- JUCE::AudioDeviceManager entegrasyonu
- ASIO/WASAPI/CoreAudio sürücü desteği
- Lock-free ring buffer
- Temel sinyal zinciri (gain, pan, mute)
- Headless servis modu

### F2 — DSP Engine (6 ay)
- 31-band parametrik equalizer
- Compressor, Gate, Limiter
- N×M routing matrix
- Bus sistemi
- WebSocket API (JSON-RPC)

### F3 — VST3 Hosting (4 ay)
- VST3 plugin host
- Plugin sandbox
- Plugin chain per bus
- Plugin parameter management
- Plugin GUI hosting

### F4 — Spatial Audio (4 ay)
- Binaural rendering (HRTF)
- Room simulation
- Dolby Atmos downmix
- Ambisonics decoder
- Head tracking

### F5 — MIDI + AI (6 ay)
- MIDI 2.0 desteği
- MIDI Learn ve mapping
- AI Auto-EQ
- Ses upscaling (ONNX)
- Genre detection

### F6 — Production Hardening (3 ay)
- Performans optimizasyonu (SIMD)
- Crash report sistemi
- Auto-update mekanizması
- Belgeler ve örnekler
- CI/CD pipeline (GitHub Actions)

### Geliştirme Ortamı

| Araç | Kullanım |
|------|----------|
| IDE | Visual Studio 2022 (Windows), CLion (cross-platform) |
| Build | CMake 3.27+ |
| Compiler | MSVC 2022 (Windows), Clang 17+ (Mac/Linux) |
| Profiler | Intel VTune, perf, Xcode Instruments |
| Test | Google Test, Catch2 |
| CI/CD | GitHub Actions (Windows/Mac/Linux matrix) |
| Code Quality | clang-tidy, cppcheck, SonarQube |

---

## 7. Güvenlik Mimarisi

| Katman | Önlem |
|--------|-------|
| API Auth | JWT + API Key (SHA-256 HMAC) |
| Transport | TLS 1.3 (wss://, https://) |
| Plugin Sandbox | İzole process, seccomp/sandbox |
| Memory | ASLR, DEP, stack canaries |
| Config | AES-256-GCM ile şifreli hassas alanlar |
| Logging | Hassas veri maskeleme (API key, token) |
| Rate Limit | 100 req/sn REST, 1000 msg/sn WebSocket |
| Input Valid. | JSON schema doğrulama, buffer overflow kontrolü |

---

## 8. Performans Hedefleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| CPU (idle) | < 0.5% @ Ryzen 7 | Process Explorer |
| CPU (full DSP) | < 15% @ Ryzen 7 (48kHz, 256 buffer) | Intel VTune |
| RAM (headless) | < 200 MB | Valgrind massif |
| RAM (GUI + AI) | < 4 GB | Valgrind massif |
| Disk IO | < 5 MB/s (streaming) | Resource Monitor |
| Boot time | < 3 sn (cold start) | Ölçüm logu |
| Latency (ASIO RTL) | < 2 ms | Oblique RTL Utility |
| Audio dropouts | 0 @ 1 saat test | ASIO driver report |

---

## 9. Bağımlılıklar

| Kütüphane | Versiyon | Lisans | Kullanım |
|-----------|----------|--------|----------|
| JUCE | 8.x | GPLv3 / Commercial | Audio, GUI, DSP |
| ASIO SDK | 2.3 | Steinberg Proprietary | Windows ses sürücüsü |
| PortAudio | 19.7 | MIT | Fallback ses API |
| libsamplerate | 0.2 | BSD 2-Clause | Sample rate conversion |
| ONNX Runtime | 1.17+ | MIT | AI inference |
| libcurl | 8.x | MIT | HTTP/WebSocket client |
| json | 3.11 | MIT | JSON parsing |
| spdlog | 1.13+ | MIT | Structured logging |
| Google Test | 1.14 | BSD 3-Clause | Unit testing |
| FFTW | 3.3 | GPLv2 | FFT hesaplama |

---

## 10. Dizin Yapısı

```
neva-engine/
├── CMakeLists.txt
├── neva-engine.json              (varsayılan config)
├── src/
│   ├── core/                     (L0 altyapı)
│   │   ├── AudioDeviceManager.cpp
│   │   ├── RingBuffer.cpp
│   │   ├── Logger.cpp
│   │   └── ConfigManager.cpp
│   ├── dsp/                      (L1 DSP)
│   │   ├── Equalizer/
│   │   ├── Compressor/
│   │   ├── Spatializer/
│   │   ├── Reverb/
│   │   └── AIInference/
│   ├── app/                      (L2 uygulama)
│   │   ├── AudioEngine.cpp
│   │   ├── RoutingMatrix.cpp
│   │   ├── PluginManager.cpp
│   │   └── SceneManager.cpp
│   ├── ui/                       (L3 arayüz)
│   │   ├── MainWindow.cpp
│   │   ├── EQComponent.cpp
│   │   └── RoutingGrid.cpp
│   ├── network/                  (API)
│   │   ├── WebSocketServer.cpp
│   │   ├── RestApiServer.cpp
│   │   └── JsonRpcHandler.cpp
│   └── midi/
│       ├── MidiManager.cpp
│       └── MidiLearn.cpp
├── include/                      (Header dosyaları)
├── models/                       (AI modelleri)
├── presets/                      (EQ/Routing presetleri)
├── plugins/                      (VST3 scan path)
├── tests/                        (Unit ve entegrasyon testleri)
├── docs/                         (Doxygen dokümantasyonu)
└── scripts/                      (Build/deploy scriptleri)
```

---

## 11. İletişim Protokolleri Özeti

| Protokol | Port | Format | Kullanım |
|----------|------|--------|----------|
| WebSocket | 9742 | JSON-RPC 2.0 | Gerçek zamanlı kontrol |
| REST API | 9741 | HTTP/JSON | CRUD işlemleri, preset yönetimi |
| MIDI | — | MIDI 1.0/2.0 | Hardware controller |
| OSC | 9743 | Open Sound Control | DAW entegrasyonu (ops.) |
| IPC | — | Shared Memory | Player ↔ Engine iletişim |
| RTP-MIDI | — | RFC 6295 | Ağ MIDI |

---

*Neva Engine v1.0.0 — Teknik Spesifikasyon*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
