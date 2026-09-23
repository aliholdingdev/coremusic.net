---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — C++ Audio Engine Template"
type: cpp-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** C++20, JUCE 9, ASIO SDK 2.3.4
**Katman:** K3 (Ses Motoru) / K2 (Sürücü)
**Sorumlu Agent:** Embedded Engineer

---

## 1. Hard Guardrails (Zero-Allocation)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Audio thread'de mutex yasak | Deadlock |
| 3 | `noexcept` (ASIO callback) zorunlu | Crash |
| 4 | `alignas(64)` zorunlu (cache-line) | False sharing |
| 5 | `constexpr` (buffer) zorunlu | Compile-time allocation |
| 6 | `std::atomic` (read/write head) zorunlu | Race condition |
| 7 | SIMD (SSE2/AVX2/NEON) kullanımı teşvik | Performans |

---

## 2. Yasaklı Örüntüler (Audio Thread'de)

```cpp
// ❌ YASAK — Heap allocation
void processBlock(float** output, const float** input, int channels, int samples) {
    std::vector<float> buffer(samples);  // ❌ YASAK
    float* temp = new float[samples];    // ❌ YASAK
    auto ptr = std::make_shared<float>(); // ❌ YASAK
}

// ✅ DOĞRU — Stack veya member değişken
class AudioProcessor {
    static constexpr int MAX_SAMPLES = 4096;
    alignas(64) float _buffer[MAX_SAMPLES]; // ✅ Stack/member

    void processBlock(float** output, const float** input, int channels, int samples) noexcept {
        for (int i = 0; i < samples; ++i) {
            for (int ch = 0; ch < channels; ++ch) {
                float s = input[ch][i];
                s = _dspChain[ch].processEQ(s);
                s = _dspChain[ch].processCompressor(s);
                s = _dspChain[ch].processLimiter(s);
                output[ch][i] = s;
            }
        }
    }
};
```

---

## 3. DSP Chain Şablonu

```cpp
// src/dsp/DSPChain.h
#pragma once

#include <array>
#include <atomic>
#include <cstddef>
#include <cstdint>

namespace coremusic::dsp {

// 31-band parametric EQ
struct EQBand {
    float frequency;
    float gain;
    float q;
    float coefficients[5]; // b0, b1, b2, a1, a2
};

class DSPChain {
public:
    static constexpr int MAX_BANDS = 31;
    static constexpr int MAX_REVERB_LINES = 16;

    DSPChain() noexcept;
    ~DSPChain() = default;

    // Zero-allocation, lock-free, noexcept
    void process(float* buffer, int samples) noexcept;

    void setEQBand(int band, float frequency, float gain, float q) noexcept;
    void setReverbMix(float mix) noexcept;
    void setCompressorThreshold(float threshold) noexcept;
    void setLimiterCeiling(float ceiling) noexcept;

    // Denormal suppression
    void enableFlushToZero() noexcept;

private:
    // Pre-allocated buffers (zero-allocation)
    static constexpr int BUFFER_SIZE = 4096;
    alignas(64) float _tempBuffer[BUFFER_SIZE];

    // EQ
    std::array<EQBand, MAX_BANDS> _eqBands;

    // Compressor state
    float _compressorThreshold;
    float _compressorRatio;
    float _compressorAttack;
    float _compressorRelease;
    float _compressorGain;

    // Limiter state
    float _limiterCeiling;
    float _limiterRelease;

    // Reverb (FDN)
    std::array<float, MAX_REVERB_LINES * BUFFER_SIZE> _reverbBuffers;
    std::array<std::atomic<size_t>, MAX_REVERB_LINES> _readHeads;
    std::array<std::atomic<size_t>, MAX_REVERB_LINES> _writeHeads;

    // Internal processing
    void processEQ(float* buffer, int samples) noexcept;
    void processCompressor(float* buffer, int samples) noexcept;
    void processLimiter(float* buffer, int samples) noexcept;
    void processReverb(float* buffer, int samples) noexcept;

    // Biquad filter
    void processBiquad(float* buffer, int samples, const float coefficients[5]) noexcept;
};

} // namespace coremusic::dsp
```

---

## 4. Ring Buffer (Lock-Free)

```cpp
// src/dsp/RingBuffer.h
#pragma once

#include <atomic>
#include <cstddef>
#include <cstdint>

namespace coremusic::dsp {

template<typename T, size_t Capacity>
class LockFreeRingBuffer {
    static_assert((Capacity & (Capacity - 1)) == 0, "Capacity must be power of 2");

public:
    LockFreeRingBuffer() noexcept
        : _readHead(0), _writeHead(0) {}

    // Lock-free write
    bool write(const T* data, size_t count) noexcept {
        const size_t currentWrite = _writeHead.load(std::memory_order_relaxed);
        const size_t currentRead = _readHead.load(std::memory_order_acquire);
        const size_t available = Capacity - (currentWrite - currentRead);

        if (count > available) return false;

        const size_t mask = Capacity - 1;
        for (size_t i = 0; i < count; ++i) {
            _buffer[(currentWrite + i) & mask] = data[i];
        }

        _writeHead.store(currentWrite + count, std::memory_order_release);
        return true;
    }

    // Lock-free read
    bool read(T* data, size_t count) noexcept {
        const size_t currentRead = _readHead.load(std::memory_order_relaxed);
        const size_t currentWrite = _writeHead.load(std::memory_order_acquire);
        const size_t available = currentWrite - currentRead;

        if (count > available) return false;

        const size_t mask = Capacity - 1;
        for (size_t i = 0; i < count; ++i) {
            data[i] = _buffer[(currentRead + i) & mask];
        }

        _readHead.store(currentRead + count, std::memory_order_release);
        return true;
    }

    size_t availableRead() const noexcept {
        return _writeHead.load(std::memory_order_acquire) -
               _readHead.load(std::memory_order_acquire);
    }

    size_t availableWrite() const noexcept {
        return Capacity - availableRead();
    }

private:
    alignas(64) std::atomic<size_t> _readHead;
    alignas(64) std::atomic<size_t> _writeHead;
    alignas(64) T _buffer[Capacity];
};

} // namespace coremusic::dsp
```

---

## 5. ASIO Callback Şablonu

```cpp
// src/engine/ASIOCallback.h
#pragma once

#include "../dsp/DSPChain.h"
#include "../dsp/RingBuffer.h"

namespace coremusic::engine {

class ASIOCallback {
public:
    static constexpr int MAX_CHANNELS = 8; // 8.1 Surround
    static constexpr int BUFFER_SIZE = 512; // ASIO buffer
    static constexpr double SAMPLE_RATE = 48000.0;

    ASIOCallback() noexcept;
    ~ASIOCallback() = default;

    // ASIO callback — MUST be noexcept
    void processAudioBlock(
        float** output,
        const float** input,
        int channels,
        int samples
    ) noexcept;

    void setEQBand(int band, float frequency, float gain, float q) noexcept;
    void setReverbMix(float mix) noexcept;

private:
    // Pre-allocated DSP chains (one per channel)
    alignas(64) dsp::DSPChain _dspChains[MAX_CHANNELS];

    // Lock-free ring buffer for input
    dsp::LockFreeRingBuffer<float, 8192> _inputRing;

    // Lock-free ring buffer for output
    dsp::LockFreeRingBuffer<float, 8192> _outputRing;
};

} // namespace coremusic::engine
```

---

## 6. Build Sistemi (CMake)

```cmake
# CMakeLists.txt
cmake_minimum_required(VERSION 3.22)
project(NevaEngine VERSION 1.0.0 LANGUAGES CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

# ASIO SDK
set(ASIO_SDK_PATH "${CMAKE_SOURCE_DIR}/libs/asio" CACHE PATH "ASIO SDK path")

# JUCE
find_package(JUCE REQUIRED)

# Neva Engine Library
add_library(NevaEngine STATIC
    src/dsp/DSPChain.cpp
    src/dsp/RingBuffer.cpp
    src/engine/ASIOCallback.cpp
    src/engine/NevaEngine.cpp
)

target_include_directories(NevaEngine PRIVATE
    ${CMAKE_SOURCE_DIR}/src
    ${ASIO_SDK_PATH}/common
)

target_compile_definitions(NevaEngine PRIVATE
    JUCE_ASIO=1
    JUCE_WASAPI=1
    JUCE_ALSA=1
    JUCE_COREAUDIO=1
)

# Compiler warnings
target_compile_options(NevaEngine PRIVATE
    -Wall -Wextra -Wpedantic
    -Werror
    -Wno-unused-parameter
)

# Platform-specific
if(WIN32)
    target_link_libraries(NevaEngine PRIVATE ws2_32 winmm)
elseif(UNIX AND NOT APPLE)
    target_link_libraries(NevaEngine PRIVATE pthread asound)
endif()
```

---

## 7. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-025 | 31-band parametrik EQ |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-062 | DSP Pipeline Architecture |

---

*C++ Audio Engine Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
