# C++ Ecosystem CLAUDE.md

**C++ Backend Architecture Guide for CoreMusic Ecosystem**

This file provides comprehensive guidance for all C++ projects within the CoreMusic ecosystem: Neva Engine, Neva Player, WirelessConnect, NevaConnect, and shared utilities.

See root `../CLAUDE.md` for the 7-service orchestration overview (CoreMusic PHP, Download Service, etc.).

---

## Quick Start — All C++ Projects

### Prerequisites (All Platforms)

```bash
# 1. Install vcpkg (one-time setup)
git clone https://github.com/Microsoft/vcpkg.git
cd vcpkg
./bootstrap-vcpkg.bat          # Windows
./bootstrap-vcpkg.sh           # macOS/Linux

# 2. Integrate with CMake
./vcpkg integrate install

# 3. Verify vcpkg can find packages
./vcpkg list | grep -E "juce|ffmpeg|vulkan|curl"
```

### Windows (MSVC 2022)

```bash
# Build all projects
cd /c/www/coremusic.net/cpp-projects
cmake -B build \
  -DCMAKE_TOOLCHAIN_FILE="path/to/vcpkg/scripts/buildsystems/vcpkg.cmake" \
  -DCMAKE_BUILD_TYPE=Release \
  -G "Visual Studio 17 2022"

cmake --build build --config Release -j 8

# Run individual projects
./build/Release/NevaEngine.exe --config neva-engine.json
./build/Release/NevaPlayer.exe
./build/Release/WirelessConnect.exe --mode gui
./build/Release/NevaConnect.exe --systemd

# Health check
curl http://localhost:9741/health
curl http://localhost:3001/health
```

### macOS (Apple Clang)

```bash
# Build
cmake -B build \
  -DCMAKE_TOOLCHAIN_FILE="$HOME/vcpkg/scripts/buildsystems/vcpkg.cmake" \
  -DCMAKE_BUILD_TYPE=Release \
  -DCMAKE_OSX_ARCHITECTURES="arm64;x86_64" \
  -G "Unix Makefiles"

cmake --build build -j $(sysctl -n hw.logicalcpu)

# Run as daemon
./build/Release/NevaEngine --daemon
open /Applications/WirelessConnect.app

# Test with wscat
brew install node-wscat
wscat -c ws://localhost:9742
```

### Linux (GCC 12+)

```bash
# Build for native x86-64
cmake -B build \
  -DCMAKE_TOOLCHAIN_FILE="$HOME/vcpkg/scripts/buildsystems/vcpkg.cmake" \
  -DCMAKE_BUILD_TYPE=Release \
  -G "Unix Makefiles"

cmake --build build -j $(nproc)

# Run with systemd
sudo systemctl start neva-engine
sudo systemctl status neva-engine
journalctl -u neva-engine -f

# Docker build (optional)
docker build -f Dockerfile.linux -t neva-engine:latest .
docker run -d --name neva --network host neva-engine:latest
```

### ARM64 (Raspberry Pi 5 / Apple Silicon)

```bash
# Cross-compile for ARMv8 (RPi5 or M1/M2/M3)
cmake -B build-arm64 \
  -DCMAKE_TOOLCHAIN_FILE="$HOME/vcpkg/scripts/buildsystems/vcpkg.cmake" \
  -DCMAKE_BUILD_TYPE=Release \
  -DCMAKE_SYSTEM_PROCESSOR=ARM64 \
  -DRPI5_HARDWARE_MODE=ON \
  -G "Unix Makefiles"

cmake --build build-arm64 -j 4

# On RPi5: configure hardware
# - Enable I2C in raspi-config
# - Mount NevaConnect as systemd service
sudo systemctl start neva-connect
```

### Test All Projects

```bash
# Run unit tests (Google Test framework)
cd build
ctest --output-on-failure -j 8

# Per-project tests
ctest -R NevaEngine::Equalizer
ctest -R NevaPlayer::Codec
ctest -R WirelessConnect::WiFi
ctest -R NevaConnect::I2C

# Coverage report
ctest --coverage
lcov --capture --directory . --output-file coverage.info
genhtml coverage.info --output-directory coverage_html
```

---

## Ecosystem Architecture

### 5 C++ Projects + Shared Core

```
cpp-projects/
├── CLAUDE.md                    (this file)
├── AGENTS.md                    (agent guidance)
├── CMakeLists.txt               (umbrella CMake)
├── shared/
│   ├── CLAUDE.md                (shared utilities, IPC)
│   ├── AGENTS.md
│   ├── CMakeLists.txt
│   ├── include/
│   │   ├── ipc/
│   │   │   ├── NevaAudioRingBuffer.hpp
│   │   │   ├── NevaEngineStatus.hpp
│   │   │   └── WebSocketHandshake.hpp
│   │   ├── platform/
│   │   │   ├── WindowsNamedObjects.hpp
│   │   │   ├── LinuxSharedMemory.hpp
│   │   │   └── MacOSMemory.hpp
│   │   └── version.hpp
│   └── src/
├── neva-engine/
│   ├── CLAUDE.md                (audio DSP processor)
│   ├── AGENTS.md
│   ├── CMakeLists.txt
│   ├── neva-engine.json         (default config)
│   ├── include/
│   └── src/
├── neva-player/
│   ├── CLAUDE.md                (media player)
│   ├── AGENTS.md
│   ├── CMakeLists.txt
│   ├── include/
│   └── src/
├── wireless-connect/
│   ├── CLAUDE.md                (WiFi/BT forensics)
│   ├── AGENTS.md
│   ├── CMakeLists.txt
│   ├── include/
│   └── src/
└── neva-connect/
    ├── CLAUDE.md                (embedded WiFi/BT)
    ├── AGENTS.md
    ├── CMakeLists.txt
    └── src/
```

### Network Topology & IPC

```
┌────────────────────────────────────────────────────┐
│ Browser (http://localhost:8000)                    │
│ CoreMusic SPA + PHP                                │
├────────────────────────────────────────────────────┤
│           ↓ REST (9741) + WS (9742) + WebRTC       │
├────────────────────────────────────────────────────┤
│ Neva Engine                                        │
│ Port 9741 (REST): health, config, presets          │
│ Port 9742 (WS): DSP control, FFT spectrum          │
│ Shared Memory: NevaAudioRingBuffer, NevaEngineStatus│
├────────────────────────────────────────────────────┤
│           ↓ Shared Memory (IPC)                     │
├────────────────────────────────────────────────────┤
│ Neva Player                                        │
│ Port 9743 (WS): video, RTC signaling, playback     │
│ WebRTC Media: UDP 49152-65535                      │
└────────────────────────────────────────────────────┘
```

### Port Allocation Strategy (ADR 031)

| Service | Primary | Fallback | Protocol | Purpose |
|---------|---------|----------|----------|---------|
| **Engine REST** | 9741 | 9741-9840 | HTTP | Health, config, presets |
| **Engine WS** | 9742 | 9742-9841 | WebSocket | DSP control, spectrum |
| **Player WS** | 9743 | 9743-9842 | WebSocket | Video, RTC, playback |
| **Download** | 3001 | 3001-3100 | REST/WS | Music queue mgmt |
| **CoreMusic** | 8000 | 8000-8100 | HTTP | SPA entry point |

**Discovery:** Engine writes actual ports to `NevaEngineStatus` (Windows named object) on startup.

### Startup Sequence (MANDATORY ORDER)

```
1. Neva Engine (9741 REST + 9742 WS)
   - Initialize ASIO device
   - Create NevaAudioRingBuffer shared memory
   - Write NevaEngineStatus with actual ports
   
2. Neva Player (9743 WS)
   - Attach NevaAudioRingBuffer
   - Initialize GPU (NVIDIA CUDA / Metal / VAAPI)
   - Listen for video/RTC streams
   
3. Download Service (3001 REST/WS)
   - Initialize SQLite
   - Listen for queue management
   
4. CoreMusic PHP (8000 HTTP)
   - Read NevaEngineStatus
   - Connect to Engine @ actual ports
   - Load routes, auth, etc.
   
5. Browser (http://localhost:8000)
   - Navigate, authenticate
   - Open WebSocket to Engine/Player
```

---

## Build System — CMake 3.20+, C++20, Cross-Platform

### CMake Configuration

**Root CMakeLists.txt:**

```cmake
cmake_minimum_required(VERSION 3.20)
project(CoreMusicCppEcosystem
  VERSION 1.0.0
  LANGUAGES CXX
  HOMEPAGE_URL "https://coremusic.net"
)

# C++20 standard
set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_CXX_FLAGS_RELEASE "-O3 -DNDEBUG")
set(CMAKE_CXX_FLAGS_DEBUG "-g -O0")

# vcpkg toolchain (passed via CLI)
# -DCMAKE_TOOLCHAIN_FILE="path/to/vcpkg/scripts/buildsystems/vcpkg.cmake"

# Platform detection
if(MSVC)
  set(PLATFORM_WINDOWS ON)
  add_compile_options(/W4 /WX /permissive-)
elseif(APPLE)
  set(PLATFORM_MACOS ON)
  add_compile_options(-Wall -Werror -fPIC)
else()
  set(PLATFORM_LINUX ON)
  add_compile_options(-Wall -Werror -fPIC)
endif()

# ARM64 detection
if(CMAKE_SYSTEM_PROCESSOR MATCHES "arm64|aarch64")
  set(PLATFORM_ARM64 ON)
  message(STATUS "Building for ARM64 (RPi5 or Apple Silicon)")
endif()

# RPi5 hardware mode
option(RPI5_HARDWARE_MODE "Enable RPi5 I2C/I2S DSP" OFF)
if(RPI5_HARDWARE_MODE AND PLATFORM_ARM64 AND PLATFORM_LINUX)
  add_definitions(-DRPI5_HARDWARE_MODE=1)
  message(STATUS "RPi5 Hardware Mode ENABLED")
endif()

# Add subdirectories
add_subdirectory(shared)
add_subdirectory(neva-engine)
add_subdirectory(neva-player)
add_subdirectory(wireless-connect)
add_subdirectory(neva-connect)

# Testing
enable_testing()
add_subdirectory(tests)
```

### Dependencies Management (vcpkg)

**vcpkg.json** (manifest-based):

```json
{
  "name": "coremusic-cpp",
  "version-string": "1.0.0",
  "dependencies": [
    {
      "name": "juce",
      "version>=": "8.0.0"
    },
    {
      "name": "ffmpeg",
      "version>=": "6.0"
    },
    {
      "name": "vulkan-hpp",
      "version>=": "1.3.0"
    },
    {
      "name": "curl",
      "version>=": "8.0.0"
    },
    {
      "name": "spdlog",
      "version>=": "1.12.0"
    },
    {
      "name": "nlohmann-json",
      "version>=": "3.11.0"
    },
    {
      "name": "gtest",
      "version>=": "1.14.0"
    },
    {
      "name": "onnxruntime",
      "version>=": "1.16.0"
    }
  ]
}
```

### Platform-Specific Setup

**Windows (NVIDIA GPU + ASIO):**
```cmake
if(PLATFORM_WINDOWS)
  find_package(CUDA REQUIRED)
  set(NEVA_PLAYER_GPU_BACKEND "NVIDIA")
  add_definitions(-D_WIN32_WINNT=0x0A00)  # Windows 10+
endif()
```

**macOS (Metal + VideoToolbox):**
```cmake
if(PLATFORM_MACOS)
  find_library(Metal Metal REQUIRED)
  find_library(MetalKit MetalKit REQUIRED)
  find_library(VideoToolbox VideoToolbox REQUIRED)
  set(NEVA_PLAYER_GPU_BACKEND "Metal")
  target_link_libraries(neva-player Metal MetalKit VideoToolbox)
endif()
```

**Linux (VAAPI + Vulkan):**
```cmake
if(PLATFORM_LINUX)
  find_package(Vulkan REQUIRED)
  set(NEVA_PLAYER_GPU_BACKEND "VAAPI")
  target_link_libraries(neva-player Vulkan::Vulkan va va-drm)
endif()
```

**RPi5 (V4L2 + Vulkan):**
```cmake
if(RPI5_HARDWARE_MODE)
  find_package(unofficial-opencv REQUIRED)
  target_link_libraries(neva-connect v4l2 i2c)
endif()
```

---

## Shared Dependencies

### Core Libraries

| Library | Version | License | Purpose | Platforms |
|---------|---------|---------|---------|-----------|
| **JUCE** | 8.x | GPL v3 | Audio framework, GUI | Win/Mac/Linux |
| **FFmpeg** | 6.x | LGPL 2.1+ | Media decoding | Win/Mac/Linux/ARM |
| **Vulkan** | 1.3+ | Apache 2.0 | GPU graphics | Win/Mac/Linux/ARM |
| **NVIDIA CUDA** | 12.x | Proprietary | GPU audio DSP | Windows/Linux |
| **libcurl** | 8.x | MIT | HTTP/REST | All |
| **spdlog** | 1.12+ | MIT | Logging | All |
| **nlohmann/json** | 3.11+ | MIT | JSON parsing | All |
| **Google Test** | 1.14+ | BSD 3-Clause | Unit testing | All |
| **ONNX Runtime** | 1.16+ | MIT | AI inference | All |

### Installation (per platform)

**vcpkg:**
```bash
./vcpkg install juce ffmpeg vulkan-hpp curl spdlog nlohmann-json gtest onnxruntime:x64-windows
```

**Conan (alternative):**
```bash
conan install . --build=missing --settings os=Windows --settings compiler="Visual Studio" --settings compiler.version=17
```

---

## Testing Framework

### Unit Testing (Google Test)

```cpp
// tests/NevaEngine/EqualizerTest.cpp
#include <gtest/gtest.h>
#include "neva-engine/Equalizer.hpp"

class EqualizerTest : public ::testing::Test {
protected:
  void SetUp() override {
    eq = std::make_unique<NevaEngine::Equalizer>(48000, 512);
  }
  std::unique_ptr<NevaEngine::Equalizer> eq;
};

TEST_F(EqualizerTest, AppliesGain) {
  eq->setGain(1.0f, 6.0f);  // 1kHz band, +6 dB
  std::vector<float> input(512, 1.0f);
  std::vector<float> output(512);
  eq->process(input.data(), output.data(), 512);
  EXPECT_GT(output[0], 1.5f);  // Expect gain applied
}
```

### Integration Testing (Custom)

```cpp
// tests/integration/EnginePlayerIntegration.cpp
TEST(EnginePlayerIntegration, RingBufferSharedMemory) {
  // Start Engine (creates NevaAudioRingBuffer)
  auto engine = NevaEngine::start();
  ASSERT_TRUE(engine.ringBuffer != nullptr);
  
  // Start Player (attaches to ring buffer)
  auto player = NevaPlayer::start();
  EXPECT_EQ(player.ringBuffer.capacity(), engine.ringBuffer.capacity());
  
  // Write PCM → Read FFT
  std::vector<float> pcm(512);
  engine.ringBuffer.write(pcm.data(), 512);
  auto spectrum = player.getFFTSpectrum();
  EXPECT_EQ(spectrum.size(), 256);
}
```

### Performance Benchmarking

```bash
# Run benchmarks (per project)
cd build
./tests/NevaEngine/EqualizerBench --benchmark_filter=".*31Band.*"
./tests/NevaPlayer/CodecBench --benchmark_filter=".*HEVC.*"

# Profile with perf (Linux)
perf record -g ./build/NevaEngine --profile
perf report

# Profile with Xcode Instruments (macOS)
xcode-select -s /Applications/Xcode.app/Contents/Developer
./build/Release/NevaEngine --instruments=CPU
```

---

## Deployment

### Windows Service Installation

```bash
# Build
cmake --build build --config Release

# Install as service
sc create NevaEngine \
  binPath= "C:\www\coremusic.net\cpp-projects\build\Release\NevaEngine.exe --service" \
  start= auto \
  DisplayName= "Neva Audio Engine"

# Start/stop
net start NevaEngine
net stop NevaEngine

# View logs (Event Viewer or)
wevtutil qe System /q:"*[System[EventID=1000]]" /format:text
```

### macOS LaunchDaemon

```bash
# Create plist
sudo cat > /Library/LaunchDaemons/net.coremusic.neva-engine.plist <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
  <key>Label</key>
  <string>net.coremusic.neva-engine</string>
  <key>Program</key>
  <string>/usr/local/bin/neva-engine</string>
  <key>RunAtLoad</key>
  <true/>
  <key>StandardOutPath</key>
  <string>/var/log/neva-engine.log</string>
  <key>StandardErrorPath</key>
  <string>/var/log/neva-engine-error.log</string>
</dict>
</plist>
EOF

sudo launchctl load /Library/LaunchDaemons/net.coremusic.neva-engine.plist
sudo launchctl start net.coremusic.neva-engine
```

### Linux systemd

```bash
# Create service file
sudo cat > /etc/systemd/system/neva-engine.service <<EOF
[Unit]
Description=Neva Audio Engine
After=network.target

[Service]
Type=simple
ExecStart=/usr/local/bin/neva-engine --config /etc/neva-engine.json
Restart=on-failure
RestartSec=5s
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable neva-engine
sudo systemctl start neva-engine
journalctl -u neva-engine -f
```

### Docker (Optional)

```dockerfile
FROM ubuntu:22.04 AS builder
RUN apt-get update && apt-get install -y \
  cmake g++ git libasound2-dev libx11-dev xorg-dev
WORKDIR /build
COPY . .
RUN cmake -B . && cmake --build . --config Release

FROM ubuntu:22.04
COPY --from=builder /build/neva-engine /usr/local/bin/
EXPOSE 9741 9742
CMD ["neva-engine"]
```

---

## Troubleshooting

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| `Port already in use (9741)` | Another Engine instance | Kill: `lsof -i :9741 \| grep LISTEN \| awk '{print $2}' \| xargs kill` |
| `vcpkg: command not found` | vcpkg not integrated | Run: `./vcpkg integrate install` |
| `JUCE: Missing CMake module` | FindJUCE.cmake not found | Copy from `shared/cmake/FindJUCE.cmake` |
| `CUDA: Not found` | NVIDIA SDK missing | Install CUDA 12.x from nvidia.com |
| `Metal framework not found` | Xcode not installed | Install Xcode: `xcode-select --install` |
| `WebSocket 403: CORS issue` | Missing CORS headers | Add: `Access-Control-Allow-Origin: *` in REST API |

### Debugging Tips

**Windows (Visual Studio Debugger):**
```bash
cmake --build build --config Debug
# Open build/CoreMusicCppEcosystem.sln in VS2022
# Set breakpoint in Engine.cpp, press F5
```

**Linux (gdb):**
```bash
cmake -B build -DCMAKE_BUILD_TYPE=Debug
cmake --build build
gdb ./build/neva-engine
(gdb) break Equalizer.cpp:45
(gdb) run --config neva-engine.json
(gdb) next
```

**macOS (lldb):**
```bash
cmake -B build -DCMAKE_BUILD_TYPE=Debug
cmake --build build
lldb ./build/Release/NevaEngine.app/Contents/MacOS/NevaEngine
(lldb) breakpoint set -f Equalizer.cpp -l 45
(lldb) run
```

### Performance Profiling

**GPU profiling (NVIDIA):**
```bash
# Install NVIDIA Nsight Systems
nsys profile -t cuda,nvtx -o profile.qdrep ./build/neva-player
```

**CPU profiling (macOS):**
```bash
instruments -t "System Trace" -o profile.trace ./build/Release/NevaEngine
```

**Memory leaks (Valgrind, Linux):**
```bash
valgrind --leak-check=full --show-leak-kinds=all ./build/neva-engine
```

---

## Cross-References

**Vault Documentation:**
- [[decisions/018-cpp-multiplatform-ecosystem]] — Project structure
- [[decisions/019-per-os-neva-player]] — Platform-specific optimizations
- [[decisions/025-professional-eq-system]] — EQ architecture
- [[decisions/031-ecosystem-port-allocation]] — Port strategy & discovery
- [[decisions/032-ipc-contract-versioning]] — WebSocket & shared memory contracts
- [[ecosystem/cpp-projects]] — Integration overview

**Project-Specific CLAUDE.md:**
- [[neva-engine/CLAUDE.md]] — Audio DSP processor
- [[neva-player/CLAUDE.md]] — Media player
- [[wireless-connect/CLAUDE.md]] — WiFi/BT forensics
- [[neva-connect/CLAUDE.md]] — Embedded hardware integration
- [[shared/CLAUDE.md]] — Shared utilities & IPC

**Related PHP/JS:**
- [[../CLAUDE.md]] — CoreMusic root (7-service ecosystem)
- [[../music.coremusic.net/]] — PHP backend
- [[../assets.coremusic.net/]] — JavaScript frontend

---

## Key Standards & Immutable Rules

### C++20 Coding Standards

- **RAII Always:** Resource acquisition = initialization. No naked pointers.
- **Const-Correctness:** Mark all non-mutating methods `const`. Use `const&` for parameters.
- **Smart Pointers:** Use `std::unique_ptr` (ownership), `std::shared_ptr` (shared ownership), never `new`/`delete`.
- **Ranges & Views:** Use `std::ranges::` for loop operations, prefer views over copies.
- **Concepts:** Define type constraints with `requires` clauses for template functions.
- **Error Handling:** Exceptions for errors, `std::optional<T>` for "no value", `std::expected<T, E>` for recoverable errors.
- **Thread Safety:** Lock-free ring buffer for audio callback (atomic primitives). Mutex-protected for UI.
- **Naming:** `CamelCase` for classes, `camelCase` for functions/variables, `SCREAMING_SNAKE_CASE` for macros.

### Audio Callback Safety

Audio callbacks (ASIO, CoreAudio, ALSA) run in real-time thread — NO:
- Memory allocation
- File I/O
- Mutex locking (use lock-free atomics)
- Virtual function calls (cache-miss risk)

Use **pre-allocated buffers**, **lock-free ring buffers**, **atomic flags**.

### Platform-Specific Gotchas

| Platform | Gotcha | Fix |
|----------|--------|-----|
| **Windows** | ASIO driver issues | Test with multiple ASIO devices (Dante, RME, etc.) |
| **macOS** | Metal GPU context thread-affinity | Run GPU calls on single dispatch queue |
| **Linux** | ALSA configuration per distro | Test on Ubuntu 22.04, Fedora 38, Raspberry Pi OS |
| **ARM64** | Endianness & unaligned access | Use `std::memcpy` for SIMD, avoid unaligned loads |

---

**Generated by Backend Architect Agent**  
**Last Updated:** 2026-06-23  
**Version:** 1.0.0
