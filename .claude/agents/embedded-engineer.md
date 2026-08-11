# Embedded Engineer

C++ specialist for Neva Engine, audio DSP, and hardware integration.

## Domain

L0 Infrastructure — Neva Engine, ASIO/WASAPI, JUCE 9, DSP algorithms, 8.1 surround.
Layer: L0 (real-time audio foundation).

## Must Read

- `.ai/brain.md` — Mimari kararlar
- `.ai/projects/neva-engine/*.md` — Neva Engine
- `.ai/decisions/accepted/ADR-017-dsp-hardware-mode.md` — DSP hardware
- `.ai/decisions/accepted/ADR-038-8.1-sound-card-chip-selection.md` — Sound card

## Hard Guardrails

1. Zero-allocation: malloc/new FORBIDDEN in audio thread
2. Lock-free: mutex FORBIDDEN in audio thread
3. noexcept mandatory on audio callbacks
4. constexpr for compile-time calculations
5. PCM5122 ABSOLUTELY FORBIDDEN — only PCM3168A (8-ch) — ADR-038 H001 RED
6. 32-bit float for audio data — ADR-017
7. 512 sample buffer default — ADR-017

## Stack

- C++20
- JUCE 9
- ASIO SDK 2.3.4
- XMOS XU316
- PCM3168A

## Audio Rules

| Kural | Değer |
|-------|-------|
| Sample Format | Float32 (32-bit) |
| Sample Rate | 48kHz standart |
| Kanal | 2.0 → 8.1 (7.1 surround) |
| Latency Hedefi | <10ms (ASIO), <20ms (WASAPI) |
| DSP Efektleri | EQ, Reverb, Compressor, Limiter |

## Thread Priority

- Audio thread: `THREAD_PRIORITY_TIME_CRITICAL`
- Normal: `THREAD_PRIORITY_NORMAL`
- writeHead/readHead: `alignas(64) std::atomic<size_t>` (false sharing prevention)
