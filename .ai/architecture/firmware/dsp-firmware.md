---
title: "DSP Processing Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# DSP Processing Firmware

## Genel Bakış

DSP (Digital Signal Processing) firmware, COREMUSIC'ın ses işleme zincirini yönetir. Real-time EQ, dynamics processing, mixing ve efektler XMOS XU316'ın hardware DSP talimatları ile optimize edilmiştir. Block-based processing ile <5ms latency ve deterministik timing sağlar.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                  DSP PROCESSING FIRMWARE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Audio Input Pipeline                    │      │
│  │  USB RX ──► Ring Buffer ──► Format Convert ──► DSP  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              DSP Processing Chain                    │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Stage 1: Input Processing                  │    │      │
│  │  │  - DC offset removal (high-pass filter)     │    │      │
│  │  │  - Input gain adjustment                    │    │      │
│  │  │  - Phase alignment                          │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  │                     │                               │      │
│  │  ┌──────────────────▼──────────────────────────┐    │      │
│  │  │  Stage 2: Parametric EQ                     │    │      │
│  │  │  - 10-band parametric EQ                    │    │      │
│  │  │  - Low shelf / High shelf / Peaking          │    │      │
│  │  │  - Bandwidth/Q factor control                │    │      │
│  │  └──────────────────────────────────────────────┘    │      │
│  │                     │                               │      │
│  │  ┌──────────────────▼──────────────────────────┐    │      │
│  │  │  Stage 3: Dynamics Processing               │    │      │
│  │  │  - Compressor (feed-forward)                 │    │      │
│  │  │  - Limiter (brick-wall)                      │    │      │
│  │  │  - Noise gate                                │    │      │
│  │  └──────────────────────────────────────────────┘    │      │
│  │                     │                               │      │
│  │  ┌──────────────────▼──────────────────────────┐    │      │
│  │  │  Stage 4: Spatial Processing                │    │      │
│  │  │  - Stereo imaging                           │    │      │
│  │  │  - Crossfeed (headphone)                    │    │      │
│  │  │  - Reverb (hall algorithm)                  │    │      │
│  │  └──────────────────────────────────────────────┘    │      │
│  │                     │                               │      │
│  │  ┌──────────────────▼──────────────────────────┐    │      │
│  │  │  Stage 5: Output Processing                 │    │      │
│  │  │  - Volume control (master/channel)          │    │      │
│  │  │  - Mute/fade                                │    │      │
│  │  │  - Output limiter                            │    │      │
│  │  └──────────────────────────────────────────────┘    │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Audio Output Pipeline                   │      │
│  │  DSP ──► Format Convert ──► Ring Buffer ──► USB TX  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
dsp_firmware/
├── src/
│   ├── dsp_core.xc               # Ana DSP processing engine
│   ├── dsp_iir.c                 # IIR biquad filter
│   ├── dsp_fir.c                 # FIR filter
│   ├── dsp_eq.c                  # Parametric EQ
│   ├── dsp_compressor.c          # Dynamics compressor
│   ├── dsp_limiter.c             # Peak limiter
│   ├── dsp_gate.c                # Noise gate
│   ├── dsp_reverb.c              # Reverb algorithm
│   ├── dsp_crossfeed.c           # Headphone crossfeed
│   ├── dsp_volume.c              # Volume control
│   ├── dsp_format.c              # Format conversion
│   └── dsp_main.xc               # Ana program
│
├── include/
│   ├── dsp_config.h              # DSP konfigürasyonu
│   ├── dsp_types.h               # Veri tipleri
│   ├── dsp_params.h              # EQ/dynamics parametreleri
│   └── dsp_registers.h           # Hardware register tanımları
│
├── coefficients/
│   ├── eq_coefficients.c         # EQ filter katsayıları
│   └── reverb_presets.c          # Reverb preset değerleri
│
└── Makefile
```

## Teknik Detaylar

### DSP Processing Block Structure

```c
// Block-based processing
// Her block: 32 sample @ 48kHz = 0.667ms
// XMOS XU316 DSP instructions ile optimize

#define DSP_BLOCK_SIZE     32
#define DSP_NUM_CHANNELS   2    // Stereo
#define DSP_SAMPLE_RATE    48000

// Audio buffer yapısı
typedef struct {
    int32_t left[DSP_BLOCK_SIZE];
    int32_t right[DSP_BLOCK_SIZE];
    int sample_count;
} audio_block_t;

// Processing chain entry point
void dsp_process_block(audio_block_t *input, audio_block_t *output) {
    audio_block_t temp1, temp2;

    // Stage 1: Input processing
    dsp_high_pass_filter(input, &temp1, DC_CUTOFF_FREQ);
    dsp_input_gain(&temp1, input_gain);

    // Stage 2: Parametric EQ
    dsp_parametric_eq(&temp1, &temp2, eq_params);

    // Stage 3: Dynamics processing
    dsp_compressor(&temp2, &temp1, compressor_params);

    // Stage 4: Spatial processing
    dsp_stereo_imaging(&temp1, &temp2, imaging_params);

    // Stage 5: Output processing
    dsp_volume_control(&temp2, &temp1, master_volume);
    dsp_output_limiter(&temp1, output, limiter_params);
}
```

### IIR Biquad Filter

```c
// IIR Biquad filter implementasyonu
// 2nd order IIR filter: H(z) = (b0 + b1*z^-1 + b2*z^-2) / (1 + a1*z^-1 + a2*z^-2)

typedef struct {
    float b0, b1, b2;      // Numerator coefficients
    float a1, a2;           // Denominator coefficients
    float x1, x2;           // Input history
    float y1, y2;           // Output history
} iir_biquad_t;

// Tek sample IIR processing
float iir_biquad_process(iir_biquad_t *filter, float input) {
    float output;

    // Difference equation
    output = filter->b0 * input
           + filter->b1 * filter->x1
           + filter->b2 * filter->x2
           - filter->a1 * filter->y1
           - filter->a2 * filter->y2;

    // State update
    filter->x2 = filter->x1;
    filter->x1 = input;
    filter->y2 = filter->y1;
    filter->y1 = output;

    return output;
}

// Block processing - optimized
void iir_biquad_process_block(iir_biquad_t *filter,
                               const float *input,
                               float *output,
                               int block_size) {
    for (int i = 0; i < block_size; i++) {
        output[i] = iir_biquad_process(filter, input[i]);
    }
}

// EQ filter katsayıları hesaplama
// Peaking EQ: biquad coefficients from frequency, gain, Q
void calculate_peaking_eq(iir_biquad_t *filter,
                          float frequency,
                          float gain_dB,
                          float Q,
                          float sample_rate) {
    float A = powf(10.0f, gain_dB / 40.0f);
    float w0 = 2.0f * M_PI * frequency / sample_rate;
    float alpha = sinf(w0) / (2.0f * Q);

    filter->b0 = 1.0f + alpha * A;
    filter->b1 = -2.0f * cosf(w0);
    filter->b2 = 1.0f - alpha * A;
    filter->a1 = -2.0f * cosf(w0);
    filter->a2 = 1.0f - alpha / A;

    // Normalize
    float a0_inv = 1.0f / filter->a0;
    filter->b0 *= a0_inv;
    filter->b1 *= a0_inv;
    filter->b2 *= a0_inv;
    filter->a1 *= a0_inv;
    filter->a2 *= a0_inv;
}
```

### Parametric EQ

```c
// 10-band parametric EQ
// Her band: frequency, gain, Q (bandwidth)

#define EQ_NUM_BANDS 10

typedef struct {
    iir_biquad_t bands[EQ_NUM_BANDS];
    float band_gains[EQ_NUM_BANDS];
    float band_frequencies[EQ_NUM_BANDS];
    float band_q[EQ_NUM_BANDS];
} parametric_eq_t;

// EQ preset değerleri
typedef enum {
    EQ_PRESET_FLAT,
    EQ_PRESET_ROCK,
    EQ_PRESET_JAZZ,
    EQ_PRESET_CLASSICAL,
    EQ_PRESET_POP,
    EQ_PRESET_CUSTOM
} eq_preset_t;

// EQ parametreleri
eq_preset_t eq_presets[] = {
    [EQ_PRESET_FLAT] = {
        .band_frequencies = {31, 62, 125, 250, 500, 1000, 2000, 4000, 8000, 16000},
        .band_gains = {0, 0, 0, 0, 0, 0, 0, 0, 0, 0},
        .band_q = {0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7}
    },
    [EQ_PRESET_ROCK] = {
        .band_frequencies = {31, 62, 125, 250, 500, 1000, 2000, 4000, 8000, 16000},
        .band_gains = {5, 4, 3, 1, -1, -1, 2, 3, 4, 5},
        .band_q = {0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7, 0.7}
    }
};

// EQ processing
void parametric_eq_process(parametric_eq_t *eq,
                           audio_block_t *input,
                           audio_block_t *output) {
    float temp_left[DSP_BLOCK_SIZE];
    float temp_right[DSP_BLOCK_SIZE];

    // Input'u float'a çevir
    int32_to_float_array(input->left, temp_left, DSP_BLOCK_SIZE);
    int32_to_float_array(input->right, temp_right, DSP_BLOCK_SIZE);

    // Her band için processing
    for (int band = 0; band < EQ_NUM_BANDS; band++) {
        iir_biquad_process_block(&eq->bands[band],
                                  temp_left, temp_left,
                                  DSP_BLOCK_SIZE);
        iir_biquad_process_block(&eq->bands[band],
                                  temp_right, temp_right,
                                  DSP_BLOCK_SIZE);
    }

    // Float'tan int32'ye çevir
    float_to_int32_array(temp_left, output->left, DSP_BLOCK_SIZE);
    float_to_int32_array(temp_right, output->right, DSP_BLOCK_SIZE);
}
```

### Dynamics Compressor

```c
// Feed-forward dynamics compressor
// Threshold, ratio, attack, release, makeup gain

typedef struct {
    float threshold_dB;      // Compressor threshold (dB)
    float ratio;             // Compression ratio (2:1, 4:1, etc.)
    float attack_ms;         // Attack time (ms)
    float release_ms;        // Release time (ms)
    float makeup_gain_dB;    // Makeup gain (dB)
    float envelope;          // Current envelope
} compressor_t;

// Compressor processing
void compressor_process(compressor_t *comp,
                        audio_block_t *input,
                        audio_block_t *output) {
    float left[DSP_BLOCK_SIZE];
    float right[DSP_BLOCK_SIZE];

    // Float'a çevir
    int32_to_float_array(input->left, left, DSP_BLOCK_SIZE);
    int32_to_float_array(input->right, right, DSP_BLOCK_SIZE);

    // Attack/release coefficients
    float attack_coeff = expf(-1.0f / (comp->attack_ms * 0.001f * DSP_SAMPLE_RATE));
    float release_coeff = expf(-1.0f / (comp->release_ms * 0.001f * DSP_SAMPLE_RATE));

    for (int i = 0; i < DSP_BLOCK_SIZE; i++) {
        // Peak level hesapla (L/R max)
        float level = fmaxf(fabsf(left[i]), fabsf(right[i]));
        float level_dB = 20.0f * log10f(level + 1e-10f);

        // Gain reduction hesapla
        float gain_reduction = 0.0f;
        if (level_dB > comp->threshold_dB) {
            gain_reduction = (comp->threshold_dB - level_dB) * (1.0f - 1.0f / comp->ratio);
        }

        // Envelope follower
        float target = powf(10.0f, gain_reduction / 20.0f);
        if (target < comp->envelope) {
            comp->envelope = attack_coeff * comp->envelope + (1.0f - attack_coeff) * target;
        } else {
            comp->envelope = release_coeff * comp->envelope + (1.0f - release_coeff) * target;
        }

        // Makeup gain
        float makeup = powf(10.0f, comp->makeup_gain_dB / 20.0f);

        // Apply gain
        left[i] *= comp->envelope * makeup;
        right[i] *= comp->envelope * makeup;
    }

    // Int32'ye çevir
    float_to_int32_array(left, output->left, DSP_BLOCK_SIZE);
    float_to_int32_array(right, output->right, DSP_BLOCK_SIZE);
}
```

### Peak Limiter

```c
// Brick-wall peak limiter
// True peak detection ile oversampling

typedef struct {
    float threshold;          // Limiter threshold (0.0 - 1.0)
    float release_ms;         // Release time (ms)
    float envelope;           // Current envelope
    float peak_left;          // Left channel peak
    float peak_right;         // Right channel peak
} limiter_t;

// Limiter processing
void limiter_process(limiter_t *lim,
                     audio_block_t *input,
                     audio_block_t *output) {
    float left[DSP_BLOCK_SIZE];
    float right[DSP_BLOCK_SIZE];

    int32_to_float_array(input->left, left, DSP_BLOCK_SIZE);
    int32_to_float_array(input->right, right, DSP_BLOCK_SIZE);

    float release_coeff = expf(-1.0f / (lim->release_ms * 0.001f * DSP_SAMPLE_RATE));

    for (int i = 0; i < DSP_BLOCK_SIZE; i++) {
        // Peak detection
        float level = fmaxf(fabsf(left[i]), fabsf(right[i]));

        // Envelope following
        if (level > lim->envelope) {
            lim->envelope = level;  // Instant attack
        } else {
            lim->envelope = release_coeff * lim->envelope;
        }

        // Gain calculation
        float gain = 1.0f;
        if (lim->envelope > lim->threshold) {
            gain = lim->threshold / lim->envelope;
        }

        // Apply gain
        left[i] *= gain;
        right[i] *= gain;

        // Clip protection (safety)
        left[i] = fmaxf(-1.0f, fminf(1.0f, left[i]));
        right[i] = fmaxf(-1.0f, fminf(1.0f, right[i]));
    }

    float_to_int32_array(left, output->left, DSP_BLOCK_SIZE);
    float_to_int32_array(right, output->right, DSP_BLOCK_SIZE);
}
```

### Noise Gate

```c
// Noise gate - signal below threshold'i kesme
typedef struct {
    float threshold_dB;       // Gate threshold (dB)
    float attack_ms;          // Attack time (ms)
    float hold_ms;            // Hold time (ms)
    float release_ms;         // Release time (ms)
    float envelope;           // Current envelope
    int hold_counter;         // Hold counter
    int is_open;              // Gate state
} gate_t;

void gate_process(gate_t *gate,
                  audio_block_t *input,
                  audio_block_t *output) {
    float left[DSP_BLOCK_SIZE];
    float right[DSP_BLOCK_SIZE];

    int32_to_float_array(input->left, left, DSP_BLOCK_SIZE);
    int32_to_float_array(input->right, right, DSP_BLOCK_SIZE);

    float attack_coeff = expf(-1.0f / (gate->attack_ms * 0.001f * DSP_SAMPLE_RATE));
    float release_coeff = expf(-1.0f / (gate->release_ms * 0.001f * DSP_SAMPLE_RATE));
    int hold_samples = (int)(gate->hold_ms * 0.001f * DSP_SAMPLE_RATE);

    for (int i = 0; i < DSP_BLOCK_SIZE; i++) {
        float level = fmaxf(fabsf(left[i]), fabsf(right[i]));
        float level_dB = 20.0f * log10f(level + 1e-10f);

        // Gate state machine
        if (level_dB > gate->threshold_dB) {
            gate->is_open = 1;
            gate->hold_counter = hold_samples;
            gate->envelope = attack_coeff * gate->envelope + (1.0f - attack_coeff) * 1.0f;
        } else {
            if (gate->hold_counter > 0) {
                gate->hold_counter--;
            } else {
                gate->is_open = 0;
                gate->envelope = release_coeff * gate->envelope;
            }
        }

        // Apply gate
        left[i] *= gate->envelope;
        right[i] *= gate->envelope;
    }

    float_to_int32_array(left, output->left, DSP_BLOCK_SIZE);
    float_to_int32_array(right, output->right, DSP_BLOCK_SIZE);
}
```

### Stereo Crossfeed (Headphone)

```c
// Crossfeed - stereo separation'ı azaltarak
// headphone dinleme konforunu artırma

typedef struct {
    float mix_level;          // Crossfeed mix (0.0 - 1.0)
    float delay_ms;           // Delay between channels (ms)
    int delay_samples;        // Delay in samples
    float *delay_buffer_left;
    float *delay_buffer_right;
    int write_pos;
    int read_pos;
} crossfeed_t;

void crossfeed_process(crossfeed_t *cf,
                       audio_block_t *input,
                       audio_block_t *output) {
    float left[DSP_BLOCK_SIZE];
    float right[DSP_BLOCK_SIZE];
    float left_out[DSP_BLOCK_SIZE];
    float right_out[DSP_BLOCK_SIZE];

    int32_to_float_array(input->left, left, DSP_BLOCK_SIZE);
    int32_to_float_array(input->right, right, DSP_BLOCK_SIZE);

    for (int i = 0; i < DSP_BLOCK_SIZE; i++) {
        // Delayed versions
        float delayed_left = cf->delay_buffer_left[cf->read_pos];
        float delayed_right = cf->delay_buffer_right[cf->read_pos];

        // Crossfeed: mix delayed opposite channel
        left_out[i] = left[i] + cf->mix_level * delayed_right;
        right_out[i] = right[i] + cf->mix_level * delayed_left;

        // Update delay buffers
        cf->delay_buffer_left[cf->write_pos] = left[i];
        cf->delay_buffer_right[cf->write_pos] = right[i];
        cf->write_pos = (cf->write_pos + 1) % cf->delay_samples;
        cf->read_pos = (cf->read_pos + 1) % cf->delay_samples;

        // Clip protection
        left_out[i] = fmaxf(-1.0f, fminf(1.0f, left_out[i]));
        right_out[i] = fmaxf(-1.0f, fminf(1.0f, right_out[i]));
    }

    float_to_int32_array(left_out, output->left, DSP_BLOCK_SIZE);
    float_to_int32_array(right_out, output->right, DSP_BLOCK_SIZE);
}
```

### DSP Performance Optimization

```
XMOS XU316 DSP Talimatları:

┌─────────────────────────────────────────────────────────────┐
│ Instruction        │ Description           │ Cycles         │
├────────────────────┼───────────────────────┼────────────────┤
│ MAC                │ Multiply-Accumulate   │ 1              │
│ LMAC               │ Long MAC (48-bit)     │ 1              │
│ MUL                │ Multiply              │ 1              │
│ CLZ                │ Count Leading Zeros   │ 1              │
│ BYTEREV            │ Byte Reverse          │ 1              │
│ BITREV             │ Bit Reverse           │ 1              │
│ ASHIFT             │ Arithmetic Shift      │ 1              │
│ LASHIFT            │ Long Arithmetic Shift │ 1              │
│ EQ                 │ Equal comparison      │ 1              │
│ LSUB               │ Long Subtract         │ 1              │
└────────────────────┴───────────────────────┴────────────────┘

DSP Processing Budget (per 32-sample block):

┌─────────────────────────────────────────────────────────────┐
│ Stage              │ Max Cycles   │ Available    │ Margin   │
├────────────────────┼──────────────┼──────────────┼──────────┤
│ Input Processing   │ 5,000        │ 16,667       │ 70%      │
│ Parametric EQ      │ 15,000       │ 16,667       │ 10%      │
│ Dynamics           │ 8,000        │ 16,667       │ 52%      │
│ Spatial            │ 10,000       │ 16,667       │ 40%      │
│ Output Processing  │ 3,000        │ 16,667       │ 82%      │
├────────────────────┼──────────────┼──────────────┼──────────┤
│ TOTAL              │ 41,000       │ 83,333       │ 51%      │
└────────────────────────────────────┴──────────────┴──────────┘

Block size: 32 samples @ 48kHz = 667μs
Available cycles per block: 333,333 (500MHz / 48kHz * 32)
```

## Derleme & Yükleme

### DSP Firmware Derleme

```bash
cd firmware/xmos/app_usb_audio_skc

# DSP firmware derleme
xmake clean
xmake all CONFIG=dsp_enabled

# EQ bands seçimi
xmake all CONFIG=dsp_enabled EQ_BANDS=10

# Dynamics processing
xmake all CONFIG=dsp_enabled COMPRESSOR=1 LIMITER=1

# Spatial effects
xmake all CONFIG=dsp_enabled REVERB=1 CROSSFEED=1
```

### DSP Test

```bash
# Test tone ile doğrulama
# 1kHz sine wave, -20dBFS
# THD+N ölçümü

# EQ test
# Pink noise → EQ → FFT analizi
# Frequency response doğrulama

# Dynamics test
# Step response ile compressor/limiter test
# Attack/release time doğrulama
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| lib_dsp | >= 3.x | DSP processing kütüphanesi |
| lib_xcore_math | >= 1.x | Fixed-point math |
| lib_locks | >= 1.x | Channel synchronization |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| DSP Core | Planlandı | Block processing engine |
| IIR Biquad | Planlandı | EQ filter implementation |
| Parametric EQ | Planlandı | 10-band EQ |
| Compressor | Planlandı | Feed-forward dynamics |
| Limiter | Planlandı | Brick-wall limiter |
| Noise Gate | Planlandı | Signal gating |
| Reverb | Planlandı | Hall algorithm |
| Crossfeed | Planlandı | Headphone spatial |
| Volume Control | Planlandı | Master/channel volume |
