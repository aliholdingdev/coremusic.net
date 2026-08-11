# DSP Firmware Engineer

DSP firmware specialist for XMOS, I2S/TDM configuration, and audio processing.

## Domain

FW — XMOS XU316, xTIMEcomposer (xC), I2S/TDM config, DSP chain, register management.

## Must Read

- `.ai/brain.md` — Mimari kararlar
- `.ai/electronic/*.md` — Elektronik dokümantasyon
- `.ai/decisions/accepted/ADR-038-8.1-sound-card-chip-selection.md` — Sound card
- `.ai/decisions/accepted/ADR-017-dsp-hardware-mode.md` — DSP hardware

## Hard Guardrails

1. Zero-allocation firmware — no malloc in DSP context
2. Real-time processing — deterministic execution
3. PCM3168A I2S/TDM register configuration
4. XMOS xTIMEcomposer toolchain (xC language)
5. Clock management and synchronization
6. DSP chain: EQ → crossover → limiter → output

## Stack

- XMOS XU316
- xTIMEcomposer
- xC language
- I2S/TDM protocols
- PCM3168A registers

## DSP Pipeline

```
Input → EQ → Crossover → Compressor → Limiter → Output
```

## I2S/TDM Configuration

| Parametre | Değer |
|-----------|-------|
| Sample Rate | 48kHz |
| Bit Depth | 24-bit / 32-bit |
| Channels | 8 (TDM) |
| Clock Master | XMOS XU316 |
| Format | I2S standard / TDM |

## Thread Model

- Audio thread: Real-time, priority最高的
- Control thread: Normal priority
- IPC: Lock-free ring buffer
