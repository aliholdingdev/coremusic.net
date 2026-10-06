---
title: "Audio Transcoding Engine"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Audio Transcoding Engine

## Genel Bakış

Audio transcoding engine, real-time ve batch modlarda ses format dönüşümlerini yönetir. Circular buffer ile jitt absorption, sample rate conversion, ve bitdepth conversion işlemlerini optimize eder. Multi-stream concurrency desteği ile paralel transcoding sağlar.

## Transcoding Pipeline Mimarisi

```
┌─────────────────────────────────────────────────────┐
│           Audio Transcoding Architecture            │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Input Stream                                      │
│  └── Format Detection                              │
│      └── Source Decoder                            │
│          ├── FLAC Decode ─────────────────────┐    │
│          ├── MP3 Decode ──────────────────────┤    │
│          ├── AAC Decode ──────────────────────┤    │
│          └── Opus Decode ─────────────────────┤    │
│                                               │    │
│  PCM Buffer (Circular) ◀──────────────────────┘    │
│  └── Sample Rate Conversion                        │
│      └── Bitdepth Conversion                       │
│          └── Channel Mapping                        │
│              └── DSP Processing                     │
│                  └── ReplayGain                     │
│                      └── Limiter/AGC                │
│                          └── Dithering              │
│                              └── Target Encoder     │
│                                  ├── FLAC Encode    │
│                                  ├── MP3 Encode     │
│                                  ├── AAC Encode     │
│                                  └── Opus Encode    │
│                                      └── Output     │
└─────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Buffer Yönetimi

```
Circular Buffer Layout:
┌──────────────────────────────────────────────────────┐
│                                                      │
│  ┌─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┐ │
│  │     │     │     │     │     │     │     │     │ │
│  └─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┘ │
│    ▲                               ▲                  │
│    Read Pointer                    Write Pointer      │
│                                                      │
│  Capacity: 16 MB (default)                          │
│  Frame Size: 4096 samples (default)                 │
│  Channels: 2 (stereo)                              │
│  Bytes per frame: 4096 × 2 × 4 = 32,768 bytes      │
│                                                      │
│  Buffer States:                                     │
│  ├── Empty: read == write                           │
│  ├── Full: (write + 1) % capacity == read          │
│  ├── Underrun: read catches write (real-time)      │
│  └── Overrun: write catches read (overflow)        │
└──────────────────────────────────────────────────────┘
```

### Sample Rate Conversion

```
SRC Quality Levels:
┌──────────┬────────────┬──────────────────────────┐
│ Level    │ Algoritma  │ Kullanım Alanı            │
├──────────┼────────────┼──────────────────────────┤
│ 0        │ Nearest    │ Test, debug              │
│ 1        │ Linear     │ Düşük kalite, hız kritik │
│ 2        │ Quadratic  │ Orta kalite              │
│ 3        │ Sinc       │ Yüksek kalite             │
│ 4        │ Sinc+Window│ En yüksek kalite          │
└──────────┴────────────┴──────────────────────────┘

SRC Pipeline:
Input Rate → Anti-aliasing Filter → Interpolation → Decimation → Output Rate

Örnek Dönüşümler:
44100 Hz → 48000 Hz (common for video)
48000 Hz → 44100 Hz (common for audio)
96000 Hz → 44100 Hz (hi-res → CD)
16000 Hz → 48000 Hz (voice → broadcast)
```

### Bitdepth Conversion

```
Bitdepth Conversion Matrix:
┌────────────────┬────────────┬────────────────────────┐
│ Kaynak         │ Hedef      │ Yöntem                  │
├────────────────┼────────────┼────────────────────────┤
│ 16-bit         │ 24-bit     │ Zero-padding (üst bits)│
│ 16-bit         │ 32-bit     │ Zero-padding            │
│ 24-bit         │ 16-bit     │ Truncation + Dither    │
│ 24-bit         │ 32-bit     │ Zero-padding            │
│ 32-bit float   │ 16-bit     │ Scale + Dither          │
│ 32-bit float   │ 24-bit     │ Scale + Truncation     │
└────────────────┴────────────┴────────────────────────┘

Dithering Methods:
- None: Truncation only
- Rectangular: Uniform distribution noise
- Triangular: Triangular distribution (recommended)
- PDF: Probability density function dithering
```

### Channel Mapping

```
Channel Mapping Matrix:
┌──────────────┬──────────────┬────────────────────────┐
│ Kaynak       │ Hedef        │ Yöntem                  │
├──────────────┼──────────────┼────────────────────────┤
│ Stereo       │ Mono         │ (L+R)/2                │
│ Stereo       │ Joint Stereo │ Mid/Side kodlama            │
│ 5.1          │ Stereo       │ Downmix matrix          │
│ 5.1          │ Mono         │ 5.1→Stereo→Mono         │
│ 7.1          │ 5.1          │ Side channel removal    │
│ Stereo       │ 5.1          │ Upmix (matrix/phase)    │
│ Mono         │ Stereo       │ Dual mono (L=R)         │
│ Mono         │ Stereo       │ Pseudo stereo (phase)   │
└──────────────┴──────────────┴────────────────────────┘

5.1 to Stereo Downmix Matrix:
L = L + 0.707 × C + 0.707 × Ls
R = R + 0.707 × C + 0.707 × Rs
LFE: ignored or summed to both channels
```

### Real-time Transcoding

```
Real-time Requirements:
┌──────────────────┬────────────────────────────────┐
│ Parametre        │ Değer                          │
├──────────────────┼────────────────────────────────┤
│ Max Latency      │ 50ms (audio), 200ms (stream)  │
│ Buffer Latency   │ 10-50ms                        │
│ Decode Speed     │ ≥ 1.0x realtime               │
│ Encode Speed     │ ≥ 0.5x realtime (default)     │
│ CPU Limit        │ ≤ 80% (1 core)                │
│ Memory Limit     │ ≤ 256 MB per stream           │
│ Jitter Tolerance │ ≤ 5ms                          │
└──────────────────┴────────────────────────────────┘

Real-time Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Decoder  │───▶│ Circular │───▶│ Encoder  │
│ Thread   │    │ Buffer   │    │ Thread   │
└──────────┘    └──────────┘    └──────────┘
     │               │               │
     ▼               ▼               ▼
  Input I/O     Buffer Stats    Output I/O
  Non-blocking  Monitor Thread  Non-blocking
```

### Batch Transcoding

```
Batch Processing Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ Queue    │───▶│ Worker   │───▶│ Monitor  │───▶│ Callback │
│ Manager  │    │ Pool     │    │ Thread   │    │          │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  Job Queue     8 Workers      Progress Stats    Completion
  Priority      Per-file       CPU/Memory        Error/Success
  Retry Logic   Per-format     ETA               Log
```

## Kod / Konfigürasyon

### Transcoding Engine Konfigürasyonu

```yaml
transcoding_engine:
  mode: "auto"                   # real-time, batch, auto
  
  buffer:
    size_bytes: 16777216         # 16 MB
    frame_size: 4096
    underrun_threshold: 1024
    overrun_policy: "drop_oldest"
    
  sample_rate_conversion:
    enabled: true
    quality: 3                   # 0-4
    async: true                  # Parallel processing
    
  bitdepth_conversion:
    dithering: "triangular"      # none, rectangular, triangular
    scale_mode: "peak"           # peak, rms
    
  channel_mapping:
    downmix_matrix: "standard"   # standard, nfo, custom
    center_level: 0.707
    surround_level: 0.707
    
  limits:
    max_concurrent_streams: 8
    max_memory_mb: 2048
    max_cpu_percent: 80
    timeout_seconds: 300
```

### Worker Pool Konfigürasyonu

```yaml
worker_pool:
  size: 8
  priority: "normal"
  
  per_worker:
    buffer_size: 4194304         # 4 MB
    max_retries: 3
    retry_delay_ms: 1000
    
  queue:
    max_size: 1000
    priority_levels: 3
    
  monitoring:
    stats_interval: 5000         # ms
    health_check: true
    log_level: "info"
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg | 6.1.x | Core transcoding engine |
| libsamplerate | 0.2.x | Sample rate conversion |
| libsoxr | 0.2.x | High-quality SRC |
| libavcodec | 6.1.x | Audio encoding/decoding |
| libavfilter | 6.1.x | Audio filtering |

## Durum: Uygulama

Audio transcoding engine tam olarak implemente edilmiştir. Real-time ve batch modları çalışır durumdadır.

| Özellik | Durum |
|---------|-------|
| Format Detection | Tamamlandı |
| FLAC/MP3/AAC/Opus Decode | Tamamlandı |
| FLAC/MP3/AAC/Opus Encode | Tamamlandı |
| Sample Rate Conversion | Tamamlandı |
| Bitdepth Conversion | Tamamlandı |
| Channel Mapping | Tamamlandı |
| Circular Buffer | Tamamlandı |
| Real-time Mode | Tamamlandı |
| Batch Mode | Tamamlandı |
| Worker Pool | Tamamlandı |
| Progress Monitoring | Tamamlandı |
