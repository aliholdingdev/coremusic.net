# Neva Engine AGENTS.md

**AI Agent Domain Expertise for Audio DSP Development**

Specialized guidance for agents working on Neva Engine (DSP processor, JUCE, ASIO, VST3 hosting).

---

## Agent Role

**Specialization:** Audio DSP & Real-Time Systems

**Expertise:** JUCE framework, ASIO driver API, FFT/spectrum analysis, plugin hosting (VST3/LV2), signal processing (EQ, compression, spatial audio), lock-free ring buffers, thread safety in audio callbacks

**Responsibilities:**
- Design & maintain audio signal processing chain
- Manage ASIO device configuration & latency optimization
- Implement VST3 plugin hosting with parameter mapping
- Optimize CPU usage for DSP algorithms
- Ensure < 5ms round-trip latency (ASIO callback requirement)
- Test audio quality (THD, SNR, frequency response)

---

## Onboarding (5 minutes)

1. **Read:** `neva-engine/CLAUDE.md` (this directory) — 5 min
2. **Verify:** Audio device connected, CMake build successful
3. **Test:** Run health check: `curl http://localhost:9741/health`
4. **Explore:** Read `[[projects/neva-engine/equalizer-system]]` if adding EQ features

---

## Common Tasks

### Task 1: Tune EQ Band Gain

**Time:** 15 min | **Skill:** Medium

1. Locate: `neva-engine/src/dsp/EQProcessor.cpp` (line ~180)
2. Modify: `eq_bands[band].setGain(gainDb)` → gain calculation
3. Test: `ctest -R Equalizer::SetGain`
4. Verify: `wscat -c ws://localhost:9742` → send `{"cmd": "setEQBand", "band": 5, "gainDb": 3.5}`

### Task 2: Add VST3 Plugin Support

**Time:** 2-4 hours | **Skill:** High

1. Read: `[[projects/neva-engine/vst3-hosting]]` — plugin API spec
2. Implement: `PluginManager::loadPlugin(pluginPath)` in `src/plugin/PluginManager.cpp`
3. Test: `ctest -R VST3::LoadPlugin`
4. Verify: `curl -X POST http://localhost:9741/plugin/load?path=/path/to/plugin.vst3`

### Task 3: Optimize FFT Spectrum (< 10ms)

**Time:** 1-2 hours | **Skill:** High

1. Profile: `./tests/NevaEngine/FFTAnalyzerBench --benchmark_filter="2048point"`
2. Optimize: Reduce FFT size (2048 → 1024) or skip frames (30 FPS → 10 FPS)
3. Re-benchmark: Target < 10ms per computation
4. Verify WebSocket: `wscat` → monitor spectrum update latency

### Task 4: Add AI Auto-EQ (ONNX)

**Time:** 3-5 hours | **Skill:** Very High

1. Read: `[[projects/neva-engine/eq-ai-auto]]` — model spec
2. Integrate: ONNX Runtime in `CMakeLists.txt`
3. Implement: `AIAutoEQProcessor::infer(audioBuffer)` → return EQ gains
4. Test: `ctest -R AIAutoEQ::Inference`
5. Verify: `{"cmd": "aiAutoEq", "genre": "rock"}` via WebSocket

---

## Critical Gotchas (Audio-Specific)

### Gotcha 1: Audio Callback Cannot Allocate Memory

**Error Pattern:** `new`, `malloc`, `std::make_unique` in ASIO callback → **audio dropout**

**Example of WRONG code:**
```cpp
void AudioCallback(float** buffers, int numSamples) {
  auto temp = std::make_unique<float[]>(numSamples);  // ❌ ALLOCATION IN CALLBACK
  processEQ(buffers, temp.get(), numSamples);
}
```

**Correct Pattern:**
```cpp
class EQProcessor {
  std::vector<float> workBuffer;  // Pre-allocated in constructor
  
  void process(float** buffers, int numSamples) {
    // Use pre-allocated workBuffer
    processEQ(buffers, workBuffer.data(), numSamples);
  }
};
```

**How to Catch:** Add `NDEBUG` check or static analyzer that flags `new`/`malloc` in audio callbacks.

### Gotcha 2: Denormal Numbers Kill Performance

**Error Pattern:** Processing very small floats (e.g., noise) causes CPU spike

**Example of WRONG code:**
```cpp
void processAudio(float* buffer, int size) {
  for (int i = 0; i < size; ++i) {
    buffer[i] *= 0.999f;  // Can create denormals (< 1e-38)
    // CPU usage spikes if denormal flushing not enabled!
  }
}
```

**Correct Pattern:**
```cpp
// In Engine initialization
_mm_setcsr(_mm_getcsr() | 0x8040);  // Enable FTZ + DAZ (Intel)

// Or use JUCE's helper
juce::FloatVectorOperations::disableDenormalisedNumberSupport();

// Then safe to process small numbers
```

**How to Catch:** Run benchmarks with quiet audio (low level) — if CPU spikes unexpectedly, check denormal handling.

### Gotcha 3: Ring Buffer Contention Between Engine & Player

**Error Pattern:** Engine reads stale data, or Player overwrites unread samples

**Example of WRONG code:**
```cpp
class RingBuffer {
  void write(const float* data, int size) {
    memcpy(buffer.data() + writePos, data, size);  // Not atomic!
    writePos += size;  // Player might read while copying
  }
};
```

**Correct Pattern:**
```cpp
class LockFreeRingBuffer {
  std::atomic<uint32_t> writePos{0};
  std::atomic<uint32_t> readPos{0};
  
  void write(const float* data, int size) {
    uint32_t newWrite = (writePos.load() + size) % capacity;
    memcpy(buffer.data() + writePos.load(), data, size);
    writePos.store(newWrite, std::memory_order_release);
  }
  
  void read(float* data, int size) {
    uint32_t newRead = (readPos.load() + size) % capacity;
    memcpy(data, buffer.data() + readPos.load(), size);
    readPos.store(newRead, std::memory_order_release);
  }
};
```

**How to Catch:** Add ring buffer stress test — 1M write/read ops, verify no data corruption.

### Gotcha 4: VST3 Plugin Parameter Update Lag

**Error Pattern:** User changes gain slider → plugin updates 200ms later (not real-time)

**Example of WRONG code:**
```cpp
// Main thread
void setPluginParam(float value) {
  plugin->setParameterValue(value);  // Blocks? Uses mutex?
}
```

**Correct Pattern:**
```cpp
// Main thread (UI/WebSocket)
struct PluginParamUpdate {
  int paramId;
  float value;
};
LockFreeQueue<PluginParamUpdate> paramQueue;

void setPluginParam(float value) {
  paramQueue.enqueue({paramId, value});  // Non-blocking atomic enqueue
}

// Audio callback thread
void audioCallback(...) {
  PluginParamUpdate update;
  while (paramQueue.tryDequeue(update)) {
    plugin->setParameterValue(update.paramId, update.value);
  }
  plugin->process(...);
}
```

**How to Catch:** Test with oscilloscope or spectrum analyzer — parameter changes should affect output within next frame (< 20ms @ 512 samples).

### Gotcha 5: ASIO Latency Compensation Mismatch

**Error Pattern:** Neva Player sends audio, Engine processes with 100ms latency, SPA displays spectrum 200ms behind

**Example of WRONG code:**
```cpp
// No compensation for ASIO buffer latency
void sendSpectrum() {
  fft.compute(currentBuffer);  // This buffer is 512 samples old @ 48kHz
  ws.send(spectrum);  // No timestamp → WS client doesn't know age
}
```

**Correct Pattern:**
```cpp
struct SpectrumFrame {
  std::vector<float> bins;
  uint64_t timestamp_us;  // When this frame was captured
};

void sendSpectrum() {
  auto latencyMs = (bufferSize / sampleRate) * 1000.0;
  uint64_t captureTime = currentTime_us - (latencyMs * 1000);
  
  fft.compute(currentBuffer);
  ws.send({spectrum, captureTime});  // Client can sync video → spectrum
}
```

**How to Catch:** Monitor SPA visualization — spectrum should align with audio/video in real-time, no visible lag.

---

## Quick Lookup

| Component | Location | Key Files |
|-----------|----------|-----------|
| ASIO Setup | `src/asio/` | `ASIOManager.cpp`, `ASIOCallback.cpp` |
| 31-Band EQ | `src/dsp/` | `EQProcessor.hpp`, `EQProcessor.cpp` |
| Compressor | `src/dsp/` | `CompressorProcessor.hpp` |
| FFT Spectrum | `src/dsp/` | `FFTAnalyzer.hpp` |
| VST3 Hosting | `src/plugin/` | `PluginManager.hpp`, `PluginHost.cpp` |
| Spatial Audio | `src/dsp/` | `SpatializerProcessor.hpp`, `ConvolverEngine.hpp` |
| Ring Buffer IPC | `../shared/include/ipc/` | `NevaAudioRingBuffer.hpp` |
| REST API | `src/api/` | `RestServer.cpp` |
| WebSocket | `src/api/` | `WebSocketServer.cpp` |
| Tests | `tests/NevaEngine/` | `*Test.cpp`, `*Bench.cpp` |

---

## Wiki Sync Checklist (After Every Commit)

- [ ] All tests pass: `ctest --output-on-failure`
- [ ] No audio callback violations (grep `-R "(new\|malloc)" src/asio/`)
- [ ] CPU usage < 30% (check benchmark results)
- [ ] ASIO latency < 5ms (measure via `/health` endpoint)
- [ ] FFT updates 30 FPS (WebSocket frame rate test)
- [ ] VST3 plugins load correctly (if changed plugin system)
- [ ] Ring buffer has no data corruption (stress test)
- [ ] Update `.ai/log.md` with one-liner
- [ ] If architectural change → create ADR in `.ai/decisions/`

---

## Standards

### C++20 Audio Code

```cpp
// ✅ Correct pattern for audio processing

// L0: Infrastructure (pre-allocated, reusable)
class AudioBuffer {
  std::vector<float> data;  // Pre-allocated once
public:
  float* getWritePointer(int channel) { return data.data() + channel * stride; }
};

// L1: DSP Algorithm (const correctness, no allocation)
class EQProcessor {
  std::vector<BiquadFilter> filters;  // Per-band, pre-created
  
  void process(const float* input, float* output, int size) const {
    std::memcpy(output, input, size * sizeof(float));  // No exceptions
    for (const auto& filter : filters) {
      filter.process(output, output, size);  // In-place safe
    }
  }
};

// L2: Application (manage threading, IPC)
class AudioEngine {
  AudioBuffer workBuffer;
  EQProcessor eq;
  LockFreeRingBuffer ringBuffer;  // For Player ↔ Engine IPC
  
  void audioCallback(float** buffer, int numSamples) noexcept {
    // Pre-allocated, no allocation, no exceptions
    ringBuffer.read(workBuffer.data(), numSamples);
    eq.process(workBuffer.data(), buffer[0], numSamples);
  }
};

// L3: Presentation (REST/WebSocket, can allocate)
class RestServer {
  void handleRequest(const Request& req) {
    auto response = std::make_unique<Response>();  // OK to allocate here
    response->json["cpuUsage"] = getEngineStats().cpuUsage;
    send(response);
  }
};
```

### Naming Conventions

- `EQProcessor` — DSP algorithm (PascalCase)
- `processAudio` — function (camelCase)
- `SAMPLE_RATE` — constant (SCREAMING_SNAKE_CASE)
- `m_buffer` — member (optional prefix, avoid in modern C++)
- `gain_db` — variable with unit in name (good practice)

### Testing Requirements

- Unit tests for all public DSP functions (target: 80% coverage)
- Integration tests for ASIO callback + ring buffer
- Benchmarks for FFT, compressor, plugin load
- Audio quality tests (THD, SNR measurements)

---

**Generated by Backend Architect Agent**  
**Last Updated:** 2026-06-23  
**Version:** 1.0.0
