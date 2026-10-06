---
title: "I2S Driver Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# I2S Driver Firmware

## Genel Bakış

I2S (Inter-IC Sound) driver firmware, COREMUSIC'ın DAC/ADC ile olan ses iletişimini yönetir. Multi-channel I2S desteği, master/slave clock modları ve hardware-level bit-perfect audio transferi sağlar. XMOS XU316 üzerinde optimized edilmiş I2S driver ile <100μs input-to-output latency hedeflenmektedir.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    I2S DRIVER FIRMWARE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              XMOS XU316 I2S Interface                │      │
│  │              (Hardware I2S blocks)                    │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              I2S Master Driver                        │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Clock Generation                           │    │      │
│  │  │  - BCLK (Bit Clock): 2.822/3.072 MHz        │    │      │
│  │  │  - LRCLK (Word Clock): 44.1/48/96/192 kHz   │    │      │
│  │  │  - MCLK (Master Clock): 11.2896/12.288 MHz  │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Data Transfer                             │    │      │
│  │  │  - TX (DAC output)                          │    │      │
│  │  │  - RX (ADC input)                           │    │      │
│  │  │  - Frame sync (LRCLK)                       │    │      │
│  │  │  - Bit alignment (16/24/32-bit)             │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Configuration                             │    │      │
│  │  │  - Sample rate selection                    │    │      │
│  │  │  - Bit depth selection                      │    │      │
│  │  │  - Channel count (2-16)                     │    │      │
│  │  │  - Clock polarity                           │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              External DAC/ADC                        │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  PCM5242     │  │  AK4493      │  │  CS5368  │  │      │
│  │  │  (DAC)       │  │  (DAC)       │  │  (ADC)   │  │      │
│  │  │  32-bit      │  │  32-bit      │  │  24-bit  │  │      │
│  │  │  768kHz      │  │  768kHz      │  │  192kHz  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
i2s_driver/
├── src/
│   ├── i2s_master.xc               # I2S master driver
│   ├── i2s_slave.xc                # I2S slave driver
│   ├── i2s_clock.xc                # Clock generation & config
│   ├── i2s_frame_sync.xc           # Frame sync management
│   ├── i2s_multichannel.xc         # Multi-channel support
│   ├── i2s_config.xc               # Configuration handling
│   └── i2s_main.xc                 # Ana program
│
├── include/
│   ├── i2s_config.h                # Konfigürasyon dosyaları
│   ├── i2s_types.h                 # Veri tipleri
│   └── i2s_registers.h             # Register tanımları
│
└── Makefile
```

## Teknik Detaylar

### I2S Protocol Overview

```
I2S Bus Signals:
┌─────────────────────────────────────────────────────────┐
│  Signal    │ Description              │ Direction        │
├────────────┼──────────────────────────┼──────────────────┤
│  SCK/SCLK  │ Serial Clock (BCLK)     │ Master → Slave   │
│  WS/LRCLK  │ Word Select (LRCLK)     │ Master → Slave   │
│  SDOUT     │ Serial Data Out (RX)     │ Slave → Master   │
│  SDIN      │ Serial Data In (TX)      │ Master → Slave   │
│  MCLK      │ Master Clock             │ Master → Slave   │
└─────────────────────────────────────────────────────────┘

I2S Timing Diagram (32-bit, 2 channels):

SCK:   ─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─┐┌─
WS:    ──────────────────────────────────────────────────
        │ Left Channel (LRCLK=0)    │ Right Channel (LRCLK=1)
SDOUT: ─X D31 X D30 X ... X D0 X── X D31 X D30 X ... X D0 X─
        │ MSB first, LSB last       │ MSB first, LSB last
```

### Clock Configuration

```xc
// I2S clock generation
// Master mode: XMOS generates BCLK, LRCLK, MCLK

// Supported configurations:
// Sample Rate │ BCLK      │ LRCLK    │ MCLK      │ MCLK Ratio
// 44.1 kHz    │ 2.822 MHz │ 44.1 kHz │ 11.2896 MHz│ 256x
// 48 kHz      │ 3.072 MHz │ 48 kHz   │ 12.288 MHz │ 256x
// 96 kHz      │ 6.144 MHz │ 96 kHz   │ 24.576 MHz │ 256x
// 192 kHz     │ 12.288 MHz│ 192 kHz  │ 49.152 MHz │ 256x

// Clock divider hesaplama
// XMOS clock: 500 MHz (100 MHz x 5 PLL)
// BCLK divider: 500 / (2 * BCLK_freq)

typedef struct {
    uint32_t sample_rate;
    uint32_t bclk_freq;
    uint32_t lrclk_freq;
    uint32_t mclk_freq;
    uint32_t mclk_ratio;
    uint32_t bit_depth;
} i2s_clock_config_t;

// Clock ayarlama fonksiyonu
void configure_i2s_clocks(i2s_clock_config_t *config) {
    // PLL konfigürasyonu
    uint32_t pll_n = config->mclk_freq / 1000000;
    uint32_t pll_r = 1;
    uint32_t pll_f = config->mclk_freq % 1000000;

    // XMOS PLL register ayarla
    write_register(0x00, pll_n);  // PLL_N
    write_register(0x01, pll_r);  // PLL_R
    write_register(0x02, pll_f);  // PLL_F

    // BCLK divider
    uint32_t bclk_div = 500000000 / (2 * config->bclk_freq);
    write_register(0x10, bclk_div);

    // LRCLK divider (BCLK / (bit_depth * 2))
    uint32_t lrclk_div = config->bclk_freq / (config->bit_depth * 2 * config->lrclk_freq);
    write_register(0x11, lrclk_div);

    // MCLK divider (MCLK / BCLK)
    uint32_t mclk_div = config->mclk_freq / config->bclk_freq;
    write_register(0x12, mclk_div);
}
```

### I2S Master Driver

```xc
// I2S Master Driver - XMOS XS1 port mapping
// XS1 ports: 32-bit wide, one port per signal

// Port konfigürasyonu
on tile[0]: port p_i2s_bclk  = XS1_PORT_1A;   // Bit clock
on tile[0]: port p_i2s_lrclk = XS1_PORT_1B;   // Word select
on tile[0]: port p_i2s_dout  = XS1_PORT_1C;   // Data out (to DAC)
on tile[0]: port p_i2s_din   = XS1_PORT_1D;   // Data in (from ADC)
on tile[0]: port p_i2s_mclk  = XS1_PORT_1E;   // Master clock

// I2S transmit - DAC'a ses verisi gönderme
// 32-bit I2S, stereo (2 channel)
// Her sample için 64 BCLK cycle (32 left + 32 right)

void i2s_transmit(chan c_dac, i2s_config_t *config) {
    int32_t left_sample;
    int32_t right_sample;
    unsigned bclk_val;
    unsigned lrclk_val;
    unsigned data_val;

    // I2S format ayarla
    int bit_depth = config->bit_depth;      // 16, 24, veya 32
    int mclk_ratio = config->mclk_ratio;   // 256x veya 512x

    // Port initial values
    p_i2s_bclk <: 0;
    p_i2s_lrclk <: 0;  // Left channel first
    p_i2s_dout <: 0;

    while (1) {
        // Left channel (LRCLK = 0)
        p_i2s_lrclk <: 0;

        // DSP'den sol sample al
        c_dac :> left_sample;

        // MSB-first olarak transmit et
        for (int i = bit_depth - 1; i >= 0; i--) {
            data_val = (left_sample >> i) & 1;
            p_i2s_dout <: data_val;

            // BCLK falling edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // Right channel (LRCLK = 1)
        p_i2s_lrclk <: 1;

        // DSP'den sağ sample al
        c_dac :> right_sample;

        // MSB-first olarak transmit et
        for (int i = bit_depth - 1; i >= 0; i--) {
            data_val = (right_sample >> i) & 1;
            p_i2s_dout <: data_val;

            // BCLK falling edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // Kalan BCLK cycle'larını doldur
        for (int i = 0; i < (32 - bit_depth); i++) {
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }
    }
}

// I2S receive - ADC'den ses verisi okuma
void i2s_receive(chan c_adc, i2s_config_t *config) {
    int32_t left_sample;
    int32_t right_sample;
    unsigned data_in;

    int bit_depth = config->bit_depth;

    while (1) {
        left_sample = 0;
        right_sample = 0;

        // Left channel oku (LRCLK = 0)
        p_i2s_lrclk <: 0;

        for (int i = 0; i < bit_depth; i++) {
            // BCLK rising edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;

            // Data oku
            data_in = p_i2s_din :> data_in;
            left_sample = (left_sample << 1) | (data_in & 1);
        }

        // Right channel oku (LRCLK = 1)
        p_i2s_lrclk <: 1;

        for (int i = 0; i < bit_depth; i++) {
            // BCLK rising edge
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;

            // Data oku
            data_in = p_i2s_din :> data_in;
            right_sample = (right_sample << 1) | (data_in & 1);
        }

        // Kalan BCLK cycle'larını doldur
        for (int i = 0; i < (32 - bit_depth); i++) {
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }

        // ADC'ye sample'ları gönder
        c_adc <: left_sample;
        c_adc <: right_sample;
    }
}
```

### Multi-Channel I2S

```xc
// Multi-channel I2S - TDM (Time Division Multiplexing)
// 8 channel I2S, 32-bit per channel
// Frame length: 8 * 32 = 256 BCLK cycles

#define I2S_CHANNELS     8
#define I2S_BIT_DEPTH    32
#define I2S_FRAME_LENGTH (I2S_CHANNELS * I2S_BIT_DEPTH)

// TDM transmit - 8 channel output
void i2s_tdm_transmit(chan c_dac[I2S_CHANNELS]) {
    int32_t samples[I2S_CHANNELS];
    unsigned frame[I2S_FRAME_LENGTH];

    while (1) {
        // DSP'den tüm kanalları al
        for (int ch = 0; ch < I2S_CHANNELS; ch++) {
            c_dac[ch] :> samples[ch];
        }

        // TDM frame oluştur
        for (int ch = 0; ch < I2S_CHANNELS; ch++) {
            for (int bit = 0; bit < I2S_BIT_DEPTH; bit++) {
                frame[ch * I2S_BIT_DEPTH + bit] =
                    (samples[ch] >> (I2S_BIT_DEPTH - 1 - bit)) & 1;
            }
        }

        // Frame'i transmit et
        for (int i = 0; i < I2S_FRAME_LENGTH; i++) {
            p_i2s_dout <: frame[i];
            p_i2s_bclk <: 1;
            p_i2s_bclk <: 0;
        }
    }
}
```

### Sample Rate Conversion

```xc
// Basit sample rate conversion
// 44.1kHz → 48kHz dönüşümü

// Conversion ratio: 48000 / 44100 = 1.088435
// Linear interpolation ile upsampling

#define SRC_BUFFER_SIZE 256

typedef struct {
    int32_t buffer[SRC_BUFFER_SIZE];
    int write_pos;
    int read_pos;
    int ratio_num;      // 48000
    int ratio_den;      // 44100
    int phase;
} src_state_t;

void src_process(src_state_t *state, int32_t input, int32_t *output) {
    // Input sample'ı buffer'a yaz
    state->buffer[state->write_pos] = input;
    state->write_pos = (state->write_pos + 1) % SRC_BUFFER_SIZE;

    // Phase accumulator ile okuma
    state->phase += state->ratio_den;
    if (state->phase >= state->ratio_num) {
        state->phase -= state->ratio_num;
        state->read_pos = (state->read_pos + 1) % SRC_BUFFER_SIZE;
    }

    // Linear interpolation
    int32_t sample0 = state->buffer[state->read_pos];
    int32_t sample1 = state->buffer[(state->read_pos + 1) % SRC_BUFFER_SIZE];
    int32_t frac = state->phase * 256 / state->ratio_num;

    *output = sample0 + ((sample1 - sample0) * frac >> 8);
}
```

### Clock Recovery (Slave Mode)

```xc
// I2S Slave Mode - External clock recovery
// BCLK ve LRCLK dış kaynaktan geliyor
// MCLK'ı recovered clock'a senkronize etme

void i2s_slave_clock_recovery(chan c_mclk_adj) {
    timer t;
    unsigned bclk_edges;
    unsigned lrclk_edges;
    unsigned last_lrclk = 0;
    unsigned lrclk_period;
    unsigned target_period;

    while (1) {
        // LRCLK edges say
        select {
            case p_i2s_lrclk when pinsneq(last_lrclk) :> lrclk_edges:
                // LRCLK değişti
                lrclk_period = lrclk_edges - last_lrclk;
                last_lrclk = lrclk_edges;

                // MCLK period hedefi
                target_period = lrclk_period / (I2S_BIT_DEPTH * 2);

                // MCLK adjustment hesapla
                int32_t error = lrclk_period - expected_period;
                c_mclk_adj <: error;
                break;

            case c_mclk_adj :> int adjustment:
                // MCLK PLL ayarla
                adjust_mclk_pll(adjustment);
                break;
        }
    }
}
```

### Audio Quality Metrics

```
I2S Audio Quality Parameters:

┌─────────────────────────────────────────────────────────┐
│ Parameter                │ Target          │ Typical    │
├──────────────────────────┼─────────────────┼────────────┤
│ SNR (Signal-to-Noise)   │ > 110 dB        │ 115 dB     │
│ THD+N                    │ < -100 dB       │ -105 dB    │
│ Dynamic Range            │ > 115 dB        │ 120 dB     │
│ Crosstalk Rejection      │ > 100 dB        │ 110 dB     │
│ Clock Jitter             │ < 50 ps RMS     │ 30 ps RMS  │
│ Input Impedance          │ 10kΩ typical    │ 10kΩ       │
│ Output Impedance         │ < 100Ω          │ 50Ω        │
│ Sample Rate Accuracy     │ ±10 ppm         │ ±5 ppm     │
└──────────────────────────┴─────────────────┴────────────┘
```

## Derleme & Yükleme

### I2S Driver Derleme

```bash
cd firmware/xmos/app_usb_audio_skc

# I2S driver ile derleme
xmake clean
xmake all CONFIG=i2s_master

# Multi-channel I2S
xmake all CONFIG=i2s_master CHANNELS=8

# I2S slave mode
xmake all CONFIG=i2s_slave

# Sample rate conversion desteği
xmake all CONFIG=i2s_master SRC=1
```

### I2S Test

```bash
# I2S timing test
# Logic analyzer ile BCLK, LRCLK, DATA monitoring
# Saleae Logic ile I2S decode

# Audio quality test
# THD+N ölçümü (Audio Precision ile)
# SNR ölçümü
# Crosstalk ölçümü
```

### DAC/ADC Konfigürasyonu

```bash
# I2C ile DAC register ayarlama
# PCM5242 için I2C address: 0x94
i2cset -y 0 0x94 0x00 0x00  # Reset
i2cset -y 0 0x94 0x01 0x00  # Mode: I2S
i2cset -y 0 0x94 0x02 0x00  # Format: 32-bit
i2cset -y 0 0x94 0x03 0x00  # MCLK ratio: 256x

# AK4493 için I2C address: 0x10
i2cset -y 0 0x10 0x00 0x00  # Reset
i2cset -y 0 0x10 0x01 0x0C  # Mode: I2S, 32-bit
i2cset -y 0 0x10 0x02 0x00  # Sound mode: Normal
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_i2s | >= 2.x | XMOS I2S driver |
| lib_i2c_master | >= 2.x | DAC/ADC register config |
| lib_locks | >= 1.x | Channel synchronization |
| lib_xcore_math | >= 1.x | Math operations |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| I2S Master Driver | Planlandı | BCLK/LRCLK/MCLK generation |
| I2S Slave Driver | Planlandı | External clock recovery |
| Multi-Channel TDM | Planlandı | 8+ channel support |
| Clock Configuration | Planlandı | 44.1k/48k/96k/192k |
| Sample Rate Conversion | Planlandı | SRC algorithm |
| Format Support | Planlandı | 16/24/32-bit |
| DAC/ADC Config | Planlandı | I2C register configuration |
| Audio Quality | Planlandı | SNR/THD metrics |
