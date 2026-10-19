# C++ Ecosystem AGENTS.md

**AI Agent Onboarding & Domain Expertise Guide**

This file defines the role, responsibilities, and critical knowledge for agents working on C++ projects within the CoreMusic ecosystem.

---

## Agent Role Definition

### Primary Role: C++ Backend Architect

**Expertise Domain:** Scalable C++ systems, multi-platform DSP/media, real-time audio, GPU programming, IPC/shared memory

**Primary Responsibility:** Design and maintain production-grade C++ architecture across 5 interconnected projects (Engine, Player, WirelessConnect, NevaConnect, shared core)

**Core Competencies:**
- JUCE audio framework, ASIO driver API
- FFmpeg media decoding, codec support
- GPU programming (Vulkan, NVIDIA CUDA, Metal, VAAPI)
- Real-time thread safety (lock-free, atomic primitives)
- Windows named objects, POSIX shared memory
- WebSocket protocol, REST API design
- CMake build system, cross-platform compilation

**Decision Authority:** Architecture decisions for C++ projects, port allocation, IPC contracts, performance budgets

---

## Onboarding Sequence

**Time Investment:** 15-20 minutes per new session

### 1. Read Master References (5 min)

```bash
# Files to read IN ORDER
1. C:\www\coremusic.net\cpp-projects\CLAUDE.md          (this directory's guide)
2. C:\www\coremusic.net\.ai\index.md                     (master vault catalog)
3. grep -i "neva\|wireless" C:\www\coremusic.net\.ai\brain.md  (recent decisions)
4. tail -20 C:\www\coremusic.net\.ai\log.md              (activity log)
```

### 2. Identify Project Scope (2 min)

| Project | Read First | Then (Optional) |
|---------|-----------|-----------------|
| **Neva Engine** | `neva-engine/CLAUDE.md` | `[[projects/neva-engine/equalizer-system]]`, ADR 025 |
| **Neva Player** | `neva-player/CLAUDE.md` | `[[decisions/019-per-os-neva-player]]`, codec matrix |
| **WirelessConnect** | `wireless-connect/CLAUDE.md` | `[[wireless/ui-hacker-mode]]`, ADR 026-027 |
| **NevaConnect** | `neva-connect/CLAUDE.md` | `[[decisions/017-rpi5-hardware]]`, PCM5122 I2S |
| **Shared IPC** | `shared/CLAUDE.md` | `[[decisions/031-032]]` (port, handshake) |

### 3. Verify CMake & Build (2 min)

```bash
# Confirm build system works
cd C:\www\coremusic.net\cpp-projects
cmake --version  # Must be 3.20+
g++ --version    # or clang++, MSVC cl.exe

# Quick smoke test
cmake -B build -DCMAKE_BUILD_TYPE=Debug
cmake --build build -j 4 --target help | grep -E "neva|wireless"
```

### 4. Run Health Check (3 min)

```bash
# Verify all 5 projects compile and link
cd build
ctest --output-on-failure -R "::Smoke" -j 4

# Check port allocation (no hardcoded conflicts)
grep -r "9741\|9742\|9743\|3001" ../ --include="*.hpp" --include="*.cpp" | wc -l
# Should be ~10-15 (config + tests), not 50+
```

### 5. Read Vault Sections by Task (3-5 min)

See CLAUDE.md "Vault Navigation" table — pick section matching your task.

---

## Common Tasks & Workflows

### Task 1: Add New DSP Effect to Engine

**Time:** 2-4 hours | **Complexity:** Medium

1. Read: `neva-engine/CLAUDE.md` → `[[equalizer-system]]` → `[[eq-dsp-chain]]`
2. Create header in `neva-engine/include/dsp/MyEffect.hpp`
3. Implement in `neva-engine/src/dsp/MyEffect.cpp`
4. Add unit test in `tests/NevaEngine/MyEffectTest.cpp`
5. Wire into `L1_DSP::SignalChain` in `neva-engine/src/AudioEngine.cpp`
6. Run: `cmake --build build && ctest -R MyEffect`
7. Update vault: `.ai/log.md` + `.ai/projects/neva-engine/eq-dsp-chain.md`

### Task 2: Fix GPU Codec on Multiple Platforms

**Time:** 4-8 hours | **Complexity:** High

1. Read: `neva-player/CLAUDE.md` → `[[decisions/019-per-os-neva-player]]` → `[[codec-matrix]]`
2. Identify broken codec (e.g., H.265 on Linux)
3. Write platform-specific test in `tests/NevaPlayer/CodecLinuxTest.cpp`
4. Implement in `neva-player/src/gpu/VAAPIDecoder.cpp` (Linux) or equivalent
5. Verify on target platform (Windows/macOS/Linux/RPi5)
6. Run full codec test suite: `ctest -R CodecMatrix`
7. Update: `.ai/decisions/019-*.md`, `.ai/log.md`

### Task 3: Optimize RingBuffer for Lower Latency

**Time:** 6-10 hours | **Complexity:** High

1. Read: `shared/CLAUDE.md` → `[[decisions/032-ipc-contract-versioning]]` → RingBuffer design
2. Write benchmark: `tests/Shared/RingBufferBench.cpp`
3. Profile with perf/Instruments: measure contention, memory bandwidth
4. Optimize: reduce atomic operations, improve cache locality
5. Validate: ensure zero data loss, latency < 5ms ASIO target
6. Run: `ctest -R RingBuffer && ./tests/RingBufferBench`
7. Commit with performance numbers in message

### Task 4: Add New CLI Command to WirelessConnect

**Time:** 3-5 hours | **Complexity:** Medium

1. Read: `wireless-connect/CLAUDE.md` → `[[wireless/ui-hacker-mode]]` (6500+ LOC reference)
2. Define command in `wireless-connect/src/cli/Commands.hpp`
3. Implement in `wireless-connect/src/cli/Commands.cpp`
4. Add unit test in `tests/WirelessConnect/CommandTest.cpp`
5. Update help text: `--help`
6. Run: `cmake --build build && ./WirelessConnect --help`
7. Update vault: `.ai/decisions/028-phase-f-cli.md` (Phase F command list)

### Task 5: Deploy Engine to Raspberry Pi 5

**Time:** 2-3 hours | **Complexity:** Medium

1. Read: `neva-connect/CLAUDE.md` → `[[decisions/017-rpi5-hardware]]`
2. Cross-compile: `cmake -B build-arm64 -DRPI5_HARDWARE_MODE=ON`
3. Install on RPi5: `scp build-arm64/neva-engine pi@raspberrypi:/usr/local/bin/`
4. Configure systemd: `sudo systemctl start neva-engine`
5. Test I2C: `i2cdetect -y 1` (should show PCM5122 @ 0x4D)
6. Verify WebSocket: `wscat -c ws://raspberrypi:9742`
7. Log results in `.ai/log.md`

---

## Critical Gotchas (Things Agents Get Wrong)

### Gotcha 1: Audio Callback Thread Safety

**Error:** Calling `std::cout`, allocating memory, or locking mutex in ASIO callback.

**Why It's Wrong:** Audio callbacks run in real-time thread. ANY blocking/allocation = audio dropout.

**Correct Pattern:**
```cpp
// ❌ WRONG
void audioCallback(float** buffer, int numSamples) {
  std::lock_guard<std::mutex> lock(mutex);  // BLOCKS → DROPOUT
  std::cout << "Processing...";             // I/O → DROPOUT
  void* ptr = malloc(numSamples * 4);       // ALLOC → DROPOUT
  processDSP(buffer, numSamples);
}

// ✅ RIGHT
void audioCallback(float** buffer, int numSamples) {
  // Pre-allocated buffers only
  // Lock-free ring buffer for communication
  ringBuffer.write(buffer, numSamples);     // atomic swap, no allocation
  // NEVER: malloc, mutex, file I/O, virtual calls
  processDSP_PreAllocated(buffer, numSamples);
}
```

**How to Avoid:** Always mark audio callbacks with `REALTIME_THREAD_ONLY` macro + add static analyzer check.

### Gotcha 2: GPU Context Thread Affinity (macOS Metal)

**Error:** Creating Metal texture/encoder from different thread than render thread.

**Why It's Wrong:** Metal GPU context is thread-affine. Wrong thread = crash or silent failure.

**Correct Pattern:**
```cpp
// ❌ WRONG
void Player::decodeFrame(Frame& frame) {
  auto texture = commandBuffer.makeRenderPassDescriptor();  // WRONG THREAD
}

// ✅ RIGHT
void Player::decodeFrame(Frame& frame) {
  dispatch_async(dispatchQueue, ^{
    auto texture = commandBuffer.makeRenderPassDescriptor();  // CORRECT THREAD
  });
}
```

**How to Avoid:** Document which thread each GPU function must run on. Add thread assertions.

### Gotcha 3: Shared Memory Lifetime & Cleanup

**Error:** Engine crashes → Player can't detach from stale ring buffer. NevaEngineStatus corrupted.

**Why It's Wrong:** Shared memory persists after process crash. Stale status struct causes discovery failures.

**Correct Pattern:**
```cpp
// ❌ WRONG
CreateFileMapping(...);  // Never cleaned up if crash

// ✅ RIGHT
// 1. Use RAII wrapper with CloseHandle in destructor
class NevaAudioRingBufferRAII {
  ~NevaAudioRingBufferRAII() { CloseHandle(handle); }
};

// 2. On startup: verify NevaEngineStatus PID is alive
DWORD pid = status.pid;
HANDLE process = OpenProcess(PROCESS_QUERY_INFORMATION, FALSE, pid);
if (!process) {
  // Engine dead, clean up old shared memory
  UnmapViewOfFile(status);
  CloseHandle(hMapFile);
}
```

**How to Avoid:** Add process-alive check + stale detection timeout (5 seconds heartbeat).

### Gotcha 4: IPC Port Fallback Logic (ADR 031)

**Error:** Hardcoding port 9741. If busy, service fails silently instead of trying fallback ports.

**Why It's Wrong:** Local dev may have multiple instances. Production needs graceful degradation.

**Correct Pattern:**
```cpp
// ❌ WRONG
const int PORT = 9741;
socket.bind(PORT);  // Fails if 9741 taken

// ✅ RIGHT
int tryPort(int basePort, int maxAttempts = 100) {
  for (int attempt = 0; attempt < maxAttempts; ++attempt) {
    int port = basePort + attempt;
    if (socket.tryBind(port)) {
      logger.info("Bound to port {}", port);
      writeToNamedObject("NevaEngineStatus", {.port = port});
      return port;
    }
  }
  logger.error("No available port in range [{}, {}]", basePort, basePort + maxAttempts);
  return -1;
}
```

**How to Avoid:** Always implement fallback logic + write actual port to discovery mechanism.

### Gotcha 5: WebSocket Handshake Protocol Versioning

**Error:** Client sends old handshake format, server expects new. Silent desync.

**Why It's Wrong:** IPC protocol evolves. Old/new versions must coexist (backward compatibility).

**Correct Pattern:**
```cpp
// ❌ WRONG
if (msg["type"] != "handshake") throw std::runtime_error("Invalid handshake");

// ✅ RIGHT
struct Handshake {
  uint8_t apiVersion;        // 1, 2, 3, ...
  std::string clientType;    // "spa", "download", "player"
};

void validateHandshake(const Handshake& hs) {
  if (hs.apiVersion > SUPPORTED_VERSION) {
    logger.warn("Client apiVersion {} newer than server {}", 
                hs.apiVersion, SUPPORTED_VERSION);
    // Fall back to common protocol version
  }
  // Version negotiation ensures backward compat
}
```

**How to Avoid:** Document protocol version in `.ai/decisions/032-*`, add version field to ALL handshakes.

---

## Wiki Sync Checklist

After every code change to C++ projects, update vault:

### Per-Commit Checklist

- [ ] Run `ctest` — all tests pass
- [ ] Run `cmake --build build --target format` — code formatted
- [ ] Grep for hardcoded ports — should be few (`9741/9742/9743/3001`)
- [ ] Check for audio callback violations — no malloc, mutex, I/O
- [ ] Update `.ai/log.md` — one-liner of what changed
- [ ] If architecture changed → new ADR in `.ai/decisions/`
- [ ] If project-specific → update `./[project]/CLAUDE.md` or vault

### New Feature → ADR

If adding major feature (new codec, new GUI mode, new IPC contract):

1. Create `.ai/decisions/NNN-feature-name.md`
2. Document decision, trade-offs, implementation plan
3. Link in `.ai/decisions/index.md`
4. Reference in relevant project's CLAUDE.md

### Bug Fix → Project CLAUDE.md

If fixing known issue (Gotcha 1-5 above):

1. Update troubleshooting section in project CLAUDE.md
2. Add code example (✅ correct pattern)
3. Document how to avoid in future

---

## Quick Lookup Table

**File Locations (grep-friendly):**

| What | Where | Grep |
|------|-------|------|
| Port definitions | `shared/include/ipc/Ports.hpp` | `grep -n "REST_PORT\|WS_PORT"` |
| Ring buffer | `shared/include/ipc/NevaAudioRingBuffer.hpp` | `grep -n "class RingBuffer"` |
| ASIO setup | `neva-engine/src/asio/ASIOManager.cpp` | `grep -n "createAudioDevice"` |
| GPU decoders | `neva-player/src/gpu/{VAAPIDecoder,CUDADecoder,MetalDecoder}.cpp` | `ls -1 neva-player/src/gpu/` |
| CLI commands | `wireless-connect/src/cli/Commands.cpp` | `grep -n "registerCommand"` |
| I2C drivers | `neva-connect/src/hardware/I2CDriver.cpp` | `grep -n "readRegister\|writeRegister"` |
| CMake modules | `shared/cmake/Find*.cmake` | `ls shared/cmake/` |
| Tests | `tests/[ProjectName]/` | `find tests -name "*.cpp"` |

---

## Standards & Immutable Rules (Copy from CLAUDE.md)

### C++20 Rules

- **RAII Always:** Acquisition = Initialization. No naked pointers.
- **Const-Correctness:** Mark const methods. Use `const&` for params.
- **Smart Pointers:** `unique_ptr` (owner), `shared_ptr` (shared), never `new`/`delete`.
- **Lock-Free:** Audio callbacks use atomic primitives, no mutex.
- **Error Handling:** Exceptions for errors, `std::optional<>` for no-value, `std::expected<>` for recoverable.
- **Naming:** `CamelCase` classes, `camelCase` functions, `SCREAMING_SNAKE_CASE` macros.

### Architecture Constraints

- **Layer Violations:** Forbidden (see CLAUDE.md)
- **Thread Affinity:** GPU calls must run on correct thread (documented)
- **Real-Time Safety:** Audio callbacks have zero heap alloc, zero I/O, zero locks
- **Platform Support:** Windows/macOS/Linux/ARM64 (RPi5) — all paths tested

### Testing Requirements

- **Unit Tests:** All public APIs have test coverage ≥ 80%
- **Integration Tests:** IPC + shared memory tested on all platforms
- **Performance Benchmarks:** Latency/CPU targets verified before merge
- **Cross-Platform:** Code must compile & run on Windows + 1 other (macOS or Linux)

---

## When to Escalate

**Stop and ask for human guidance if:**

1. **Security Decision:** Encryption algorithm, credential storage, network auth
2. **Hardware Integration:** GPIO, I2C, ASIO driver-specific behavior
3. **Performance**: Threading model change, buffer size reduction, CPU budget increase
4. **API Breaking Change:** IPC protocol, REST endpoint, shared memory layout
5. **Multi-Project Impact:** Changes affecting Engine/Player/WirelessConnect coordination

Escalate to: **Backend Architect** (human decision-maker)

---

**Generated by Backend Architect Agent**  
**Last Updated:** 2026-06-23  
**Version:** 1.0.0

**Next Steps:** Read project-specific CLAUDE.md files (neva-engine, neva-player, etc.)
