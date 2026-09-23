---
title: "FFmpeg Processing Pipeline"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# FFmpeg Processing Pipeline

## Genel Bakış

FFmpeg pipeline, COREMUSIC'in merkezi medya işleme altyapısıdır. Tüm ses dosyalarının decode, transform ve encode süreçlerini FFmpeg 6.x API üzerinden yönetir. Hardware acceleration desteği ile real-time transcoding ve batch processing modlarında çalışır.

## FFmpeg Versiyon ve Derleme

```
FFmpeg 6.1.x (LTS)
├── --enable-libmp3lame
├── --enable-libfdk-aac
├── --enable-libopus
├── --enable-libflac
├── --enable-libvorbis
├── --enable-nvenc (NVIDIA GPU)
├── --enable-amf (AMD GPU)
└── --enable-videotoolbox (macOS)
```

## Pipeline Mimarisi

```
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Input   │───▶│ Decoder  │───▶│ Filter   │───▶│ Encoder  │
│  Demuxer │    │  (AVCodec)│    │ Graph    │    │  (AVCodec)│
└──────────┘    └──────────┘    └──────────┘    └──────────┘
      │               │               │               │
      ▼               ▼               ▼               ▼
  Format Probe   PCM Buffer    Processed PCM   Encoded Output
```

## Teknik Detaylar

### FFmpeg Context Yönetimi

Her encoding oturumu bağımsız bir FFmpeg context oluşturur:

```c
AVFormatContext *ifmt_ctx;   // Input format context
AVFormatContext *ofmt_ctx;   // Output format context
AVCodecContext *dec_ctx;      // Decoder context
AVCodecContext *enc_ctx;      // Encoder context
AVFilterGraph *filter_graph;  // Filter graph
AVFilterContext *buffersrc;   // Buffer source
AVFilterContext *buffersink;  // Buffer sink
```

### Transcoding Workflow

1. **Input Probing**: `avformat_open_input()` ile format tespiti
2. **Stream Selection**: `avformat_find_stream_info()` ile codec bulma
3. **Decoder Init**: `avcodec_alloc_context3()` + `avcodec_open2()`
4. **Filter Graph**: `avfilter_graph_parse()` ile filtre setup
5. **Encoder Init**: Hedef codec parametrelerini yükle
6. **Frame Loop**: `av_read_frame()` → `avcodec_send_frame()` → `av_buffersrc_add_frame()`
7. **Output Muxing**: `av_interleaved_write_frame()` ile yazma

### Hız Optimizasyonları

```
┌─────────────────────────────────────────┐
│ Optimizasyon Stratejisi                 │
├──────────────────┬──────────────────────┤
│ Teknik           │ Kazanç              │
├──────────────────┼──────────────────────┤
│ SIMD (SSE/AVX)   │ %40-60 decode hızı  │
│ Multi-threading   │ %200-300 parallel   │
│ GPU Offload       │ %500-800 (NVENC)   │
│ Zero-copy         │ %15-20 memory       │
│ Buffered I/O      │ %30-50 disk I/O     │
└──────────────────┴──────────────────────┘
```

### Buffer Yönetimi

```
Ring Buffer (Circular):
┌───┬───┬───┬───┬───┬───┬───┬───┐
│ R │ W │   │   │   │   │   │   │
└───┴───┴───┴───┴───┴───┴───┴───┘
  ▲                       ▲
  Read Position           Write Position

- Capacity: 16 MB (varsayılan)
- Underrun threshold: 256 KB
- Overrun policy: drop oldest frames
```

## Kod / Konfigürasyon

### FFmpeg Config

```yaml
ffmpeg_pipeline:
  version: "6.1"
  threads: 4
  hw_accel: "auto"
  max_concurrent: 8
  
  decoder:
    max_threads_per_decode: 2
    enable_directRendering: true
    buffer_size: 4096
  
  encoder:
    preset: "medium"        # ultrafast/fast/medium/slow/veryslow
    crf: 18                 # Kalite: 0=lossless, 23=varsayılan, 51=en kötü
    tune: "audio"           # zerolatency, film, animation
    profile: "aac_he"       # aac_low, aac_main, aac_he, aac_he_v2
  
  filter:
    graph_complexity: "auto"
    enable_async: true
    thread_type: "slice"
```

### Pipeline Konfigürasyonu

```yaml
transcode_presets:
  lossless:
    codec: "flac"
    compression_level: 8
    verify: true
  
  high_quality:
    codec: "libmp3lame"
    bitrate: "320k"
    vbr: "V0"
    channels: 2
  
  balanced:
    codec: "libfdk_aac"
    bitrate: "192k"
    profile: "aac_he_v2"
    channels: 2
  
  streaming:
    codec: "libfdk_aac"
    bitrate: "128k"
    profile: "aac_he"
    segments: 6
    keyframe_interval: 2
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg | 6.1.x | Core processing engine |
| libmp3lame | 3.100 | MP3 encoding |
| libfdk-aac | 2.0.x | AAC encoding/decoding |
| libopus | 1.4.x | Opus encoding/decoding |
| libflac | 1.4.x | FLAC encode/decode |
| libvorbis | 1.3.x | Vorbis encoding |

## Durum: Implementasyon

FFmpeg pipeline temel fonksiyonları ile tamamen çalışır durumdadır. GPU acceleration ve multi-stream concurrency desteği eklenmiştir.

| Özellik | Durum |
|---------|-------|
| Basic Transcoding | Tamamlandı |
| Multi-threading | Tamamlandı |
| Hardware Acceleration | Tamamlandı |
| Filter Graph | Tamamlandı |
| Dynamic Bitrate | Tamamlandı |
| Error Recovery | Tamamlandı |
| Progress Callback | Tamamlandı |
| Batch Processing | Geliştirme |
