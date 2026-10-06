---
title: "K15 Medya & Streaming Katmanı"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# K15 Medya & Streaming Katmanı

## Genel Bakış

K15, COREMUSIC'in medya işleme ve streaming altyapısını yöneten katmandır. Bu katman, ses dosyalarının decode/transcode işlemlerini, adaptive bitrate streaming protokollerini ve içerik dağıtım stratejilerini merkezi olarak koordine eder. Tüm medya formatları (FLAC, MP3, AAC, Opus) için unifiable bir pipeline sunar.

## Mimari Diyagram

```
┌─────────────────────────────────────────────────────────────┐
│                    K15 Medya & Streaming                    │
├─────────────┬─────────────┬──────────────┬─────────────────┤
│  FFmpeg     │  Codec      │  Streaming   │  Content        │
│  Pipeline   │  Engine     │  Protokol    │  Delivery       │
├─────────────┼─────────────┼──────────────┼─────────────────┤
│ Transcoding │ FLAC Decoder│ HLS Segmenter│ CDN Manager     │
│ Muxing      │ MP3 Decoder │ DASH Packager│ Edge Cache      │
│ Filtering   │ AAC Decoder │ Radio Stream │ Origin Shield   │
│ Encoding    │ Opus Decoder│ Podcast RSS  │ Token Auth      │
└──────┬──────┴──────┬──────┴──────┬───────┴────────┬────────┘
       │             │             │                │
  ┌────▼────┐  ┌─────▼─────┐  ┌───▼────┐    ┌──────▼──────┐
  │ Input   │  │ Metadata  │  │ Output │    │ Monitoring  │
  │ Buffer  │  │ Extractor │  │ Buffer │    │ & Metrics   │
  └─────────┘  └───────────┘  └────────┘    └─────────────┘
```

## Katman Bileşenleri

| Bileşen | Dosya | Sorumluluk |
|---------|-------|------------|
| FFmpeg Pipeline | ffmpeg-pipeline.md | Transcoding, muxing, filtering |
| FLAC Support | flac-support.md | Lossless decode, ReplayGain |
| MP3 Decoder | mp3-decoder.md | MPEG Layer III decode, ID3 |
| AAC Decoder | aac-decoder.md | AAC/HE-AAC decode, FDK-AAC |
| HLS Streaming | hls-streaming.md | Apple HLS, adaptive bitrate |
| DASH Streaming | dash-streaming.md | MPEG-DASH, DASH.js |
| Podcast Support | podcast-support.md | RSS feed, episode CRUD |
| Radio Streaming | radio-streaming.md | Shoutcast/Icecast relay |
| Codec Comparison | codec-comparison.md | Format karşılaştırma matrisi |
| Media Metadata | media-metadata.md | ID3, Vorbis, APE tag okuma |
| Audio Transcoding | audio-transcoding.md | Real-time transcode, buffer |
| Streaming Protocol | streaming-protocol.md | Protokol karşılaştırma |
| Content Delivery | content-delivery.md | CDN, edge caching |

## Veri Akışı

```
Kaynak Dosya → Decode → İşleme → Encode → Segmentasyon → Streaming
     │              │        │        │            │            │
     ▼              ▼        ▼        ▼            ▼            ▼
  FLAC/MP3/AAC  PCM Buffer  EQ/AGC  FLAC/MP3   HLS/DASH    CDN/Edge
```

## Teknik Detaylar

- **Format Desteği**: FLAC, MP3, AAC, HE-AAC, Opus, OGG Vorbis
- **Streaming Protokolü**: HLS (Apple), MPEG-DASH, Shoutcast, Icecast
- **Transcoding**: FFmpeg 6.x wrapper ile real-time ve batch processing
- **Adaptive Bitrate**: Otomatik kalite seçimi (128kbps → 320kbps → lossless)
- **Buffer Yönetimi**: Circular buffer, jittter absorption, underrun prevention
- **Metadata**: ID3v1/v2, Vorbis Comments, APE tags, ReplayGain
- **Güvenlik**: DRM-free watermarking, token-based stream authentication
- **Monitoring**: Prometheus metrics, latency tracking, error rate alerts

## Bağımlılıklar

| Katman | İlişki |
|--------|--------|
| K0 - Operating System | Process isolation, memory management |
| K1 - Hardware | DAC/ADC signal chain, I2S bus |
| K2 - Driver | ALSA/CoreAudio driver entegrasyonu |
| K3 - Sound Engine | Audio processing, mixing, EQ |
| K5 - Data Management | MySQL metadata, Redis cache |
| K7 - Middleware | Rate limiting, session management |
| K8 - Service | Media API, device service |

## Konfigürasyon

```yaml
k15_media:
  ffmpeg:
    threads: 4
    hw_accel: auto
    max_concurrent_transcodes: 8
  streaming:
    hls_segment_duration: 6
    dash_segment_duration: 4
    default_bitrate: 192
  buffer:
    input_buffer_ms: 500
    output_buffer_ms: 1000
    jitter_buffer_ms: 200
  cache:
    segment_ttl: 3600
    metadata_ttl: 86400
```

## Durum: Implementasyon

K15 katmanı aktif geliştirme aşamasındadır. Temel codec desteği ve FFmpeg pipeline tamamlanmıştır. HLS/DASH streaming implementasyonu devam etmektedir.

| Alt Katman | Durum |
|------------|-------|
| FFmpeg Pipeline | Tamamlandı |
| FLAC/MP3/AAC Decode | Tamamlandı |
| HLS Streaming | Geliştirme |
| DASH Streaming | Planlama |
| Podcast Support | Tamamlandı |
| Radio Streaming | Geliştirme |
| CDN Integration | Planlama |
