# Neva Engine — CLAUDE.md

**Professional Audio DSP Processor for CoreMusic Ecosystem**

Audio signal processing engine with 31-band EQ, VST3 plugin hosting, spatial audio, and AI-powered auto-EQ. JUCE 8.x + ASIO 2.3, <5ms latency target.

See `../CLAUDE.md` for ecosystem overview and cross-project setup.

---

## Project Scope

**What it does:**
- Processes real-time audio from Neva Player via shared memory ring buffer
- Applies DSP effects (31-band EQ, compressor, spatial audio, AI auto-EQ)
- Hosts VST3/LV2/CLAP plugins for extensibility
- Streams FFT spectrum to CoreMusic SPA (30 FPS via WebSocket)
- Provides REST API (health, config, presets) on port 9741
- Listens for DSP control commands on WebSocket port 9742

**What it doesn't do:**
- Decode audio files (that's Neva Player's job)
- Store user data (stateless — config via JSON)
- Render UI (optional JUCE GUI, headless by default)

**Quality Targets:**
- Latency: < 5 ms (RTL ASIO @ 32 samples, 96 kHz)
- THD+N: < 0.001% @ 1 kHz
- SNR: > 120 dB (A-weighted)
- CPU: < 30% on i7 @ 44.1 kHz, 512-sample buffer

---

## Quick Start

### Build (Windows, MSVC 2022)

```bash
cd neva-engine
cmake -B build \
  -DCMAKE_TOOLCHAIN_FILE="path/to/vcpkg/scripts/buildsystems/vcpkg.cmake" \
  -DCMAKE_BUILD_TYPE=Release \
  -DCMAKE_GENERATOR="Visual Studio 17 2022"

cmake --build build --config Release -j 8
```

### Run (Default: Headless ASIO)

```bash
cd build/Release
./NevaEngine.exe

# View logs
tail -f logs/neva-engine.log

# In another terminal, test REST API
curl http://localhost:9741/health
# Response: {"status": "ready", "asioDevice": "Dante Via", "sampleRate": 48000}
```

### Test

```bash
cd build
ctest --output-on-failure -j 8

# Per-component tests
ctest -R Equalizer
ctest -R Compressor
ctest -R VST3::PluginHost
ctest -R WebSocket::Handshake
```

### Configure

```bash
# Edit config file
cat > neva-engine.json <<EOF
{
  "asioDevice": "Dante Via",
  "sampleRate": 48000,
  "bufferSize": 512,
  "masterGain": 0.95,
  "eqMode": "31band",
  "compressor": {
    "enabled": true,
    "threshold": -20.0,
    "ratio": 4.0,
    "attack": 5.0,
    "release": 50.0
  },
  "spatializer": {
    "enabled": true,
    "preset": "livingroom"
  }
}
EOF

# Start Engine with config
./NevaEngine.exe --config neva-engine.json
```

### Cross-Platform

**macOS:**
```bash
cmake -B build -DCMAKE_OSX_ARCHITECTURES="arm64;x86_64" -DCMAKE_BUILD_TYPE=Release
cmake --build build -j $(sysctl -n hw.logicalcpu)
./build/Release/NevaEngine.app/Contents/MacOS/NevaEngine
```

**Linux (ALSA + PulseAudio):**
```bash
cmake -B build -DCMAKE_BUILD_TYPE=Release
cmake --build build -j $(nproc)
./build/NevaEngine
```

**Linux (JACK):**
```bash
# Install JACK
sudo apt-get install libjack-dev

# Start JACK daemon
jackd -d alsa -r 48000 -p 512 &

# Start Engine (auto-connects to JACK)
./build/NevaEngine
```

**ARM64 (Raspberry Pi 5):**
```bash
# Cross-compile on host machine
cmake -B build-arm64 \
  -DCMAKE_TOOLCHAIN_FILE="arm-toolchain.cmake" \
  -DRPI5_HARDWARE_MODE=ON \
  -DCMAKE_BUILD_TYPE=Release

cmake --build build-arm64 -j 4

# Copy to RPi5 and install as service
scp build-arm64/NevaEngine pi@raspberrypi:/usr/local/bin/
# See ../CLAUDE.md for systemd setup
```

---

## Architecture

### Signal Chain (L1 DSP Layer)

```
┌─────────────────────────────────────────┐
│ NevaAudioRingBuffer (L0 IPC)            │
│ Input: Player writes PCM (shared mem)   │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ INPUT ROUTING (L2 Application)           │
│ - Channel splitter (mono, stereo, 5.1)   │
│ - Pre-gain, noise gate                   │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ 31-BAND EQUALIZER (L1 DSP)               │
│ - Nyquist: 20 Hz - 20 kHz (31 bands)     │
│ - Filter type: 2nd-order Butterworth     │
│ - Precision: 32-bit float internal       │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ COMPRESSOR (L1 DSP)                      │
│ - Threshold, ratio, attack, release      │
│ - Lookahead (10ms) for smooth knee       │
│ - Makeup gain auto-calculation           │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ VST3 PLUGIN HOSTING (L2 App)             │
│ - Load plugins from .vst3 directories    │
│ - Parameter mapping, MIDI CC control     │
│ - Bypass, latency compensation           │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ SPATIAL AUDIO (L1 DSP)                   │
│ - Room simulation (convolver IRs)        │
│ - HRTF spatializer (binaural)            │
│ - Object-based panning                   │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ AI AUTO-EQ (L1 DSP + ML)                 │
│ - ONNX Runtime inference                 │
│ - Real-time EQ suggestion                │
│ - Genre-adaptive profiles                │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ MASTER LIMITER (L1 DSP)                  │
│ - Prevent clipping on master bus         │
│ - True peak metering (ITU-R BS.1770)     │
│ - Ceiling: -0.1 dBFS (headroom)          │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ FFT SPECTRUM ANALYZER (L1 DSP)           │
│ - 2048-point FFT @ 30 FPS                │
│ - dB scale, A-weighting option           │
│ - Sends to SPA via WS (binary frame)     │
└──────────────┬──────────────────────────┘
               ↓
┌──────────────────────────────────────────┐
│ OUTPUT ROUTING & METERING (L2 App)       │
│ - Channel mixer (5.1 → stereo)           │
│ - Level meters, clipping detection       │
│ - Writes to AudioDevice output           │
└──────────────────────────────────────────┘
```

### Layer Structure (L0–L3)

```
┌────────────────────────────────────┐
│ L3 — PRESENTATION (Optional)       │
│ - JUCE GUI (optional build flag)   │
│ - System tray widget               │
│ - VS Code debug adapter            │
├────────────────────────────────────┤
│ L2 — APPLICATION                   │
│ - SessionManager                   │
│ - AudioEngine (main processor)     │
│ - PluginManager (VST3 hosting)     │
│ - AudioRouter (channel mapping)    │
│ - PresetManager (config + save)    │
│ - WebSocket server (port 9742)     │
│ - REST API server (port 9741)      │
├────────────────────────────────────┤
│ L1 — DSP ALGORITHMS                │
│ - EQProcessor (31 bands)           │
│ - CompressorProcessor              │
│ - SpatializerProcessor             │
│ - AIInferenceProcessor (ONNX)      │
│ - ReverbEngine (convolver)         │
│ - FFTAnalyzer                      │
├────────────────────────────────────┤
│ L0 — INFRASTRUCTURE                │
│ - AudioDeviceManager (ASIO, etc.)  │
│ - NevaAudioRingBuffer (IPC)        │
│ - NevaEngineStatus (discovery)     │
│ - Logger (spdlog)                  │
│ - ConfigParser (JSON)              │
│ - ThreadPool                       │
└────────────────────────────────────┘
```

### Threading Model

```
Main Thread (GUI, config updates)
  ↓
  ├─ WebSocket Thread (port 9742, handles incoming commands)
  │  └─ Parse JSON → enqueue to lock-free control ring
  │
  ├─ REST API Thread (port 9741, HTTP handlers)
  │  └─ Health check, preset list, stat queries
  │
  └─ Audio Callback Thread (ASIO, real-time)
     └─ Read control ring (lock-free atomic swap)
     └─ Process DSP
     └─ Write output to AudioDevice
```

**Critical:** Audio callback thread must NOT:
- Allocate memory
- Lock mutex
- Call virtual functions
- Perform I/O

Use **lock-free ring buffer** for IPC with callback thread.

---

## APIs

### REST API (Port 9741)

```
GET /health
  Response: {
    "status": "ready|processing|error",
    "uptime": 1234567,
    "asioDevice": "Dante Via",
    "sampleRate": 48000,
    "bufferSize": 512,
    "cpuUsage": 15.3,
    "memoryMB": 256
  }

GET /config
  Response: { full current configuration }

POST /config
  Body: { updated config fields }
  Response: { new config, or error }

GET /eqs
  Response: [
    {"id": "flat", "name": "Flat", "locked": true},
    {"id": "rock", "name": "Rock", "userCreated": true}
  ]

POST /eq/:id
  Body: (ignored, just load preset)
  Response: {"loaded": "rock"}

GET /fft
  Response: binary float32 array (256 bins, dB scale)
```

### WebSocket API (Port 9742)

**Handshake (Client → Server):**
```json
{
  "type": "handshake",
  "apiVersion": 2,
  "clientType": "spa|download|player"
}
```

**Commands (Client → Server):**
```json
{"cmd": "setEQ", "preset": "rock"}
{"cmd": "setEQBand", "band": 5, "gainDb": 3.5}
{"cmd": "setCompressor", "enabled": true, "threshold": -20}
{"cmd": "setSpatializer", "preset": "livingroom"}
{"cmd": "setMasterGain", "value": 0.95}
{"cmd": "loadPlugin", "path": "/path/to/plugin.vst3"}
{"cmd": "bypassPlugin", "name": "ReverbPlugin", "bypass": true}
{"cmd": "setPluginParam", "plugin": "ReverbPlugin", "param": "RoomSize", "value": 0.7}
{"cmd": "getPresets"}
```

**Responses (Server → Client):**
```json
{"type": "spectrum", "data": "base64_float32_array"}
{"type": "status", "cpuUsage": 15.3, "latency_ms": 4.2}
{"type": "error", "message": "Unknown preset: xyz"}
{"type": "presets", "items": [...]}
```

### Configuration File (neva-engine.json)

```json
{
  "general": {
    "asioDevice": "Dante Via",
    "sampleRate": 48000,
    "bufferSize": 512,
    "latencyMode": "low|normal|high"
  },
  "routing": {
    "inputChannels": 2,
    "outputChannels": 2,
    "masterGain": 0.95
  },
  "equalizer": {
    "enabled": true,
    "mode": "31band|graphic|parametric",
    "bands": [
      {"freq": 20, "gain": 0, "q": 0.71},
      ...
    ]
  },
  "compressor": {
    "enabled": true,
    "threshold": -20.0,
    "ratio": 4.0,
    "attackMs": 5.0,
    "releaseMs": 50.0,
    "lookaheadMs": 10.0
  },
  "spatializer": {
    "enabled": true,
    "preset": "neutral|headphones|livingroom|studio",
    "irPath": "/path/to/convolver.wav"
  },
  "plugins": [
    {
      "name": "MyReverb",
      "path": "/path/to/reverb.vst3",
      "enabled": true,
      "parameters": {
        "RoomSize": 0.7,
        "Decay": 2.0
      }
    }
  ],
  "aiAutoEq": {
    "enabled": true,
    "modelPath": "/models/eq-autotuner.onnx",
    "confidenceThreshold": 0.8
  },
  "metering": {
    "showTruePeak": true,
    "loudnessAlgorithm": "ITU-R BS.1770"
  }
}
```

---

## Performance Targets

| Metric | Target | How to Measure |
|--------|--------|----------------|
| ASIO Round-Trip Latency | < 5 ms | `latencyMs` in `/status` endpoint |
| CPU Usage (31-band EQ) | < 10% @ 44.1 kHz | Monitor `/health` → cpuUsage |
| Memory Footprint | < 300 MB | `rss` in system monitor |
| FFT Calculation (2048pt) | < 10 ms | Benchmark suite |
| Plugin Load Time | < 500 ms per plugin | Measure in PluginManager |
| WebSocket Frame Rate | 30 FPS spectrum | `/fft` response time < 33 ms |

---

## Testing

### Unit Tests (Google Test)

```bash
cd build
ctest -R "NevaEngine::" --output-on-failure

# Per-component
ctest -R "Equalizer::"        # 31-band filtering
ctest -R "Compressor::"       # Dynamics processing
ctest -R "Spatializer::"      # Convolver & HRTF
ctest -R "VST3::"             # Plugin hosting
ctest -R "AIAutoEQ::"         # ONNX inference
ctest -R "FFTAnalyzer::"      # Spectrum analysis
```

### Integration Tests

```bash
# Test IPC with Player
ctest -R "Integration::EnginePlayerRingBuffer"

# Test WebSocket handshake + control
ctest -R "Integration::WebSocketHandshake"

# Test REST API endpoints
ctest -R "Integration::RestAPI"
```

### Benchmarks

```bash
# Audio processing speed
./tests/NevaEngine/EqualizerBench --benchmark_filter="31Band"
./tests/NevaEngine/CompressorBench

# Plugin load performance
./tests/NevaEngine/VST3LoadBench

# FFT spectrum generation
./tests/NevaEngine/FFTAnalyzerBench
```

---

## Deployment

### Windows Service

```bash
# Build release
cmake --build build --config Release

# Install as Windows service
sc create NevaEngine \
  binPath="C:\path\to\NevaEngine.exe --service" \
  DisplayName="Neva Audio Engine" \
  start=auto

# Start
net start NevaEngine

# View logs (Event Viewer)
eventvwr.exe
```

### macOS LaunchDaemon

```bash
# Copy binary
cp build/Release/NevaEngine.app /Applications/

# Create plist (see ../CLAUDE.md for template)
sudo cp neva-engine.plist /Library/LaunchDaemons/
sudo launchctl load /Library/LaunchDaemons/net.coremusic.neva-engine.plist

# Verify
sudo launchctl start net.coremusic.neva-engine
log stream --predicate 'eventMessage contains[cd] "neva-engine"'
```

### Linux systemd

```bash
# Copy binary
sudo cp build/NevaEngine /usr/local/bin/

# Copy config
sudo cp neva-engine.json /etc/neva-engine.json

# Create service file (see ../CLAUDE.md for template)
sudo systemctl enable neva-engine
sudo systemctl start neva-engine
journalctl -u neva-engine -f
```

---

## Troubleshooting

| Issue | Cause | Solution |
|-------|-------|----------|
| **ASIO: No devices found** | Missing ASIO drivers | Install driver (Dante, RME, Focusrite) |
| **Port 9741 already in use** | Another Engine instance | `lsof -i :9741` + kill old process |
| **FFT spectrum not updating** | WebSocket disconnected | Check `/ws` endpoint, verify WS frame send rate |
| **Compressor distorting** | Makeup gain too high | Check `ratio` × `threshold` math, reduce ratio |
| **Plugin won't load (VST3)** | Path incorrect or plugin corrupted | Verify `.vst3` folder exists, check plugin logs |
| **Memory grows indefinitely** | Plugin leak or ring buffer not draining | Add memory profiler, check plugin deallocation |
| **Audio dropout (glitch)** | Audio callback memory allocation | Check lock-free ring buffer contention |

### Debugging

**Visual Studio:**
```bash
# Build debug version
cmake --build build --config Debug

# Attach debugger, set breakpoint in src/AudioEngine.cpp
# Or use "Debug" configuration in VS
```

**Linux (gdb):**
```bash
cmake -B build -DCMAKE_BUILD_TYPE=Debug
gdb ./build/NevaEngine
(gdb) break AudioEngine.cpp:123
(gdb) run --config neva-engine.json
(gdb) next
```

**macOS (lldb):**
```bash
lldb ./build/Release/NevaEngine.app/Contents/MacOS/NevaEngine
(lldb) breakpoint set -f AudioEngine.cpp -l 123
(lldb) run
```

### Performance Profiling

**CPU Profiling (Linux perf):**
```bash
perf record -g ./build/NevaEngine
perf report
```

**GPU Profiling (NVIDIA):**
```bash
nsys profile -o profile.qdrep ./build/NevaEngine
nsys-ui profile.qdrep
```

**Memory Profiling (Valgrind):**
```bash
valgrind --leak-check=full ./build/NevaEngine --config neva-engine.json
```

---

## Cross-References

**Vault Documentation:**
- [[projects/neva-engine/overview]] — Architecture overview
- [[projects/neva-engine/equalizer-system]] — 31-band EQ spec
- [[projects/neva-engine/eq-dsp-chain]] — Signal processing chain
- [[projects/neva-engine/eq-plugin-hosting]] — VST3/LV2 hosting
- [[projects/neva-engine/spatial-audio]] — Room simulation, HRTF
- [[projects/neva-engine/eq-ai-auto]] — AI auto-EQ (ONNX)
- [[decisions/025-professional-eq-system]] — EQ system design
- [[decisions/031-ecosystem-port-allocation]] — Port allocation & fallback
- [[decisions/032-ipc-contract-versioning]] — WebSocket handshake

**Ecosystem Integration:**
- [[../CLAUDE.md]] — Root C++ ecosystem setup
- [[../shared/CLAUDE.md]] — Shared IPC utilities
- [[../neva-player/CLAUDE.md]] — Player (ring buffer consumer)
- [[../../CLAUDE.md]] — CoreMusic PHP (REST/WS client)

---

## Key Standards

### Audio Processing

- **32-bit float** internal processing (32-bit IEEE 754)
- **64-bit double** for accumulators (reduce quantization error)
- **No denormals:** Flush-to-zero (FTZ) and denormals-are-zero (DAZ) enabled
- **SIMD:** Use SSE4.1/AVX2 for fast convolution (pre-optimized by JUCE)

### Real-Time Thread

```cpp
// ❌ WRONG (in audio callback)
void processAudio(...) {
  auto buf = std::make_unique<float[]>(size);  // NO ALLOCATION
  file.read(buf.get());                        // NO I/O
  std::lock_guard lock(mutex);                 // NO MUTEX
  logger.info("Processing...");                // NO I/O
}

// ✅ RIGHT
void processAudio(...) {
  // Only pre-allocated buffers, lock-free reads
  controlRing.tryRead(command);    // atomic swap, no alloc
  dsp.process(input, output);
}
```

### C++20 Rules (from ../AGENTS.md)

- RAII always
- Const-correctness
- Smart pointers only
- No exceptions in callbacks
- Thread-safe for callback thread

---

**Generated by Backend Architect Agent**  
**Last Updated:** 2026-06-23  
**Version:** 1.0.0
