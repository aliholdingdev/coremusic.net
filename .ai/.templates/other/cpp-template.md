---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — C++ Audio Engine Template"
type: cpp-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — C++ Audio Engine Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic Neva Engine'in C++20/JUCE audio kodunu standartlaştırmaktır: zero-allocation DSP chain, lock-free ring buffer, ASIO callback ve CMake build iskeletlerini; noexcept/zero-allocation kurallarını ve yasaklı örüntüleri tek şablon içinde sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.cpp` / `*.h` audio engine (K3 Ses Motoru / K2 Sürücü) | PHP/JS uygulama kodu |
| DSP chain, ring buffer, ASIO callback, CMake | Web前端 / CSS |
| Zero-allocation, lock-free, noexcept kuralları | Donanım PCB tasarımı (bkz. hardware-template) |

- **Dosya tipi:** C++ header/kaynak + Markdown şablon dokümanı
- **Teknoloji:** C++20, JUCE 9, ASIO SDK 2.3.4
- **Kullanan agent:** Embedded Engineer (sorumlu agent · dosya başlığı; AGENTS.md §6: C++, ASIO, JUCE, audio, DSP, Neva Engine), Windows Software Engineer (ikincil — WASAPI)
- **Katman:** K3 (Ses Motoru) / K2 (Sürücü) · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}`, C++/CMake kod blokları ve `//` yorum satırları birebir korunmuştur. Zero-allocation guardrails ve yasaklı örüntüler §4.1-§4.2'dedir.

### {{TITLE}}

**Teknoloji:** C++20, JUCE 9, ASIO SDK 2.3.4
**Katman:** K3 (Ses Motoru) / K2 (Sürücü)
**Sorumlu Agent:** Embedded Engineer

---

#### 3.1 DSP Chain Şablonu

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

#### 3.2 Ring Buffer (Lock-Free)

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

#### 3.3 ASIO Callback Şablonu

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

#### 3.4 Build Sistemi (CMake)

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

## 4. Kurallar

Zorunlu / yasak kurallar (kod standartları dahil):

#### 4.1 Hard Guardrails (Zero-Allocation)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Audio thread'de mutex yasak | Deadlock |
| 3 | `noexcept` (ASIO callback) zorunlu | Crash |
| 4 | `alignas(64)` zorunlu (cache-line) | False sharing |
| 5 | `constexpr` (buffer) zorunlu | Compile-time allocation |
| 6 | `std::atomic` (read/write head) zorunlu | Race condition |
| 7 | SIMD (SSE2/AVX2/NEON) kullanımı teşvik | Performans |

#### 4.2 Yasaklı Örüntüler (Audio Thread'de)

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

Ek kurallar:

- **Zorunlu:** header'lar `#pragma once` + `namespace coremusic::dsp` / `coremusic::engine` kullanır; tüm audio thread fonksiyonları `noexcept` imzalıdır (§3.1-§3.3).
- **Zorunlu:** buffer'lar `static constexpr` + `alignas(64)` ile önceden ayrılır (`_tempBuffer`, `_dspChains`, `_buffer[Capacity]`) — zero-allocation.
- **Zorunlu:** ring buffer read/write head'leri `std::memory_order_relaxed/acquire/release` ile `std::atomic` yönetilir (lock-free).
- **Yasak:** audio thread'de `std::vector`, `new`, `std::make_shared`, mutex (§4.2).
- **Yasak:** `{{TITLE}}` placeholder'ı doldurulmadan dosya commit edilemez.
- **Standart:** C++20, `-Wall -Wextra -Wpedantic -Werror` (§3.4 CMake); derleme hatası → AGENTS.md §24.4 "FIX IMMEDIATELY", devam yasak.
- **Uyarı:** doğrulanamayan API/sınıf `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/other/cpp-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1-§3.3 header'ları `src/dsp/` ve `src/engine/` altına; §3.4'ü kök `CMakeLists.txt`'e kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** yalnızca `{{TITLE}}`; kod iskeletini proje ihtiyaçına göre sınıf/fonksiyon ekleerek uyarla (guardrails ihlal etmeden).
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1/§4.2 zero-allocation/noexcept ihlali yok + C++20 derleme temiz (`-Werror`).
5. **COMMIT:** kodu commit et; ADR-017/ADR-025/ADR-038/ADR-062 ile çelişki yoksa onayla, `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §4.1 guardrails + §4.2 yasaklı örüntüler geçti; audio thread'de heap/mutex yok

**REFACTOR REPORT:** FILE: cpp-template.md · PURPOSE: C++ Audio Engine Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: C++/ASIO/JUCE/DSP → Embedded Engineer), kalite standardı §16 (zero-allocation, lock-free, noexcept)
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)

İlgili ADR'ler:

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-025 | 31-band parametrik EQ |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-062 | DSP Pipeline Architecture |

---

*C++ Audio Engine Template v2.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
