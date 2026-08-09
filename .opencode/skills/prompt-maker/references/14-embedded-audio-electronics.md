---
title: EMBEDDED, AUDIO & ELECTRONICS
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# EMBEDDED, AUDIO & ELECTRONICS
# Prompt Maker v7.2.0 | 2026-06-11

## Audio Specifications

**Sample Rate:**
- CD: 44.1 kHz
- Professional: 48 kHz
- Hi-res: 96 kHz, 192 kHz

**Bit Depth:**
- CD: 16-bit
- Professional: 24-bit
- Floating point: 32-bit (internal processing)

**Quality Metrics:**
- SNR (Signal-to-Noise Ratio): > 100 dB (professional target)
- THD+N (Total Harmonic Distortion + Noise): < 0.01% @ 1kHz

**Latency:**
- ASIO/WASAPI callback: < 5ms (real-time audio)
- Total E2E: < 50ms (user noticeable)

## DSP Chain (Processing Order)

1. **Input Normalization:** Range 0-1 or -1 to +1
2. **Preamp:** Gain staging (input level)
3. **Equalization:** 31-band (standard audio) or parametric
4. **Compression/Limiting:** Dynamic range control
5. **Reverb/Delay:** Spatial effects
6. **Limiter:** Peak protection (prevent clipping)
7. **Output Gain:** Final output level

## 31-Band Equalizer

**Standard Frequencies (ISO 226):**
```
20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160, 200, 250, 315, 400, 500,
630, 800, 1k, 1.25k, 1.6k, 2k, 2.5k, 3.15k, 4k, 5k, 6.3k, 8k, 10k,
12.5k, 16k, 20k Hz
```

**Per-Band Specs:**
- Gain range: ±12 dB
- Filter type: Peaking (Q factor ~1.0)
- Interaction: Additive (overlapping gain)

## Audio Codecs

| Codec | Type | Quality | Bitrate | Use Case |
|-------|------|---------|---------|----------|
| FLAC | Lossless | Bitperfect | 400-700 kbps | Archival |
| WAV | Uncompressed | Bitperfect | 1.4 Mbps (44.1kHz) | Editing |
| MP3 | Lossy | Good | 192-320 kbps | Legacy compatibility |
| AAC | Lossy | Better | 128-256 kbps | Streaming |
| Vorbis | Lossy | Excellent | 128-320 kbps | Free alternative |
| **Opus** | Lossy | Excellent | 20-64 kbps | Modern streaming |

**Modern Choice:** Opus (20ms frame, <64kbps, transparent quality)

## Embedded Systems (RTOS)

**Task Scheduling (FreeRTOS Example):**
```c
// Task with priority 2 (higher = more important)
xTaskCreate(audioCallback, "Audio", stackSize, NULL, 2, &audioHandle);
xTaskCreate(uiUpdate, "UI", stackSize, NULL, 1, NULL);
```

**Priority Inversion Prevention:**
- Audio callback: highest priority (real-time)
- UI updates: lower priority (can be delayed)
- Use mutexes carefully (can block high-priority tasks)

**Interrupt Handlers (ISR):**
```c
// Lock-free, atomic, pre-allocated buffers
void USART_IRQ_Handler(void) {
    uint8_t data = USART->DATA;
    ringBuffer_write(&uart_rx, data);  // Atomic push
    xSemaphoreGiveFromISR(uart_semaphore, NULL);
}
```

**NEVER in ISR:**
- malloc/free (use pre-allocated pools)
- Blocking I/O (UART, I2C)
- Long computations (should be in task)

## Hardware Platforms

**STM32 (ARM Cortex-M):**
- Microcontroller, low power
- ADC/DAC (16-bit), SPI/I2C/UART
- CubeMX code generator, HAL library

**ESP32 (WiFi + Bluetooth):**
- Dual-core, WiFi/BLE/Classic Bluetooth
- Good for IoT, audio over network
- Arduino IDE, ESP-IDF

**Raspberry Pi 5 (ARM64):**
- Full Linux, 64-bit processor
- GPIO, I2C, SPI, audio HAT support
- PCM5122 I2C DAC (CoreMusic RPi5 mode)

## Audio Interfaces

**ASIO (Windows, professional):**
- Low-latency direct driver access
- Callback-based: `void audioCallback(float *in, float *out, int frames)`
- Requires device-specific driver

**WASAPI (Windows, universal):**
- OS-level audio API
- Shared mode (multiple apps) or exclusive
- Lower priority than ASIO

**CoreAudio (macOS):**
- HAL (Hardware Abstraction Layer)
- AudioUnit plugins (AU format)

**ALSA (Linux):**
- Low-level hardware control
- No abstraction layer
- Requires careful buffer management

## Memory Constraints

**STM32F407 Example:**
- RAM: 192 KB
- Flash: 1 MB
- Strategy: Stack/heap analysis, DMA for data movement, no dynamic allocation in ISR

**Ring Buffer (Lock-Free):**
```c
struct RingBuffer {
    volatile uint32_t writeIndex;    // Producer
    volatile uint32_t readIndex;     // Consumer
    uint32_t capacity;
    float samples[capacity];
};

// Writer (producer, e.g., FFmpeg decoder)
void write(RingBuffer *rb, float *data, int frames) {
    for (int i = 0; i < frames; ++i) {
        rb->samples[rb->writeIndex] = data[i];
        rb->writeIndex = (rb->writeIndex + 1) % rb->capacity;
    }
}

// Reader (consumer, e.g., DSP engine)
void read(RingBuffer *rb, float *out, int frames) {
    for (int i = 0; i < frames; ++i) {
        out[i] = rb->samples[rb->readIndex];
        rb->readIndex = (rb->readIndex + 1) % rb->capacity;
    }
}
```

## Real-Time Constraints

**Hard Real-Time:** Audio callback (< 5ms, no jitter)
**Soft Real-Time:** Sensor reading (100ms tolerance)
**Non-Real-Time:** Logging, configuration (seconds tolerance)

## Testing Embedded Code

- **Hardware debugger:** ST-Link, J-Link (breakpoints, stepping)
- **Logic analyzer:** Capture I2C/SPI signals
- **Oscilloscope:** Audio signal verification
- **Audio testing:** Frequency response, THD, SNR measurement

---

Quality Score (2026-06-11): 98.7%
