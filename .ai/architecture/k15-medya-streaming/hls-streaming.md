---
title: "HLS Streaming Protocol"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# HLS Streaming Protocol

## Genel Bakış

HTTP Live Streaming (HLS), Apple tarafından geliştirilen adaptive bitrate streaming protokolüdür. Medya dosyalarını küçük segmentlere bölerek HTTP üzerinden güvenilir bir streaming sağlar. COREMUSIC, HLS 8.x destekli segmenter ve manifest yönetimini uygular.

## HLS Mimarisi

```
┌─────────────────────────────────────────────────┐
│                  HLS Architecture               │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌─────────┐    ┌──────────┐    ┌──────────┐  │
│  │ Encoder │───▶│ Segmenter│───▶│ Web Server│  │
│  │ (H.264/ │    │          │    │ (Nginx/  │  │
│  │  AAC)   │    │          │    │  Apache) │  │
│  └─────────┘    └──────────┘    └────┬─────┘  │
│                                       │        │
│  Master Playlist (.m3u8)              │        │
│  ├── Variant Stream 0 (128k) ────────┤        │
│  ├── Variant Stream 1 (256k) ────────┤        │
│  ├── Variant Stream 2 (512k) ────────┤        │
│  └── Variant Stream 3 (1024k) ───────┤        │
│                                       │        │
│  Media Playlist (.m3u8)               │        │
│  ├── segment_000.ts (6s) ────────────┘        │
│  ├── segment_001.ts (6s)                       │
│  ├── segment_002.ts (6s)                       │
│  └── ...                                       │
└─────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Master Playlist Formato

```m3u8
#EXTM3U
#EXT-X-VERSION:8
#EXT-X-INDEPENDENT-SEGMENTS

#EXT-X-STREAM-INF:BANDWIDTH=128000,CODECS="mp4a.40.2",RESOLUTION=0
low/index.m3u8

#EXT-X-STREAM-INF:BANDWIDTH=256000,CODECS="mp4a.40.2",RESOLUTION=0
mid/index.m3u8

#EXT-X-STREAM-INF:BANDWIDTH=512000,CODECS="mp4a.40.2",RESOLUTION=0
high/index.m3u8

#EXT-X-STREAM-INF:BANDWIDTH=1024000,CODECS="mp4a.40.5",RESOLUTION=0
ultra/index.m3u8
```

### Media Playlist Formato

```m3u8
#EXTM3U
#EXT-X-VERSION:8
#EXT-X-TARGETDURATION:6
#EXT-X-MEDIA-SEQUENCE:42
#EXT-X-PLAYLIST-TYPE:EVENT
#EXT-X-KEY:METHOD=AES-128,URI="/keys/enc.key",IV=0x00000000000000000000000000000001
#EXTINF:6.000,
segment_042.ts
#EXTINF:6.000,
segment_043.ts
#EXTINF:6.000,
segment_044.ts
#EXT-X-ENDLIST
```

### Segment Desteği

```
┌────────────────┬────────────────────────────────┐
│ Segment Format │ Özellikler                     │
├────────────────┼────────────────────────────────┤
│ MPEG-2 TS      │ Geleneksel,广泛的destek         │
│ fMP4 (CMAF)    │ Modern, low overhead           │
│ AAC-hls        │ Audio-only segments            │
│ AC-3-hls       │ Dolby Digital segments         │
│ EC-3-hls       │ Dolby Digital Plus segments    │
└────────────────┴────────────────────────────────┘
```

### Adaptive Bitrate Algoritması

```
Bandwidth Measurement:
1. TCP Throughput hesapla
2. Segment download time ölç
3. Available bitrate seç

Decision Tree:
if (bandwidth > 320kbps) → Ultra (AAC 192k)
if (bandwidth > 192kbps) → High (AAC 128k)
if (bandwidth > 128kbps) → Mid (AAC 96k)
else → Low (AAC 64k)

Buffer Seviyeleri:
- Healthy: 3+ segment
- Warning: 2 segment
- Critical: < 2 segment
- Emergency: 1 segment (rebuffer)
```

### Encryption ve DRM

```
HLS Encryption Methods:
┌──────────────┬────────────────────────────────┐
│ Method       │ Kullanım                       │
├──────────────┼────────────────────────────────┤
│ AES-128      │ Segment-level encryption       │
│ SAMPLE-AES   │ Sample-level encryption        │
│ FairPlay     │ Apple DRM (iOS/tvOS)           │
│ Widevine     │ Google DRM (Android/Chrome)    │
│ PlayReady    │ Microsoft DRM (Edge/Xbox)      │
└──────────────┴────────────────────────────────┘
```

### HLS Version Özellikleri

```
┌─────┬───────────────────────────────────────┐
│ Ver │ Özellik                               │
├─────┼───────────────────────────────────────┤
│  1  │ Basic HLS (audio/video)               │
│  2  │ ADTS audio support                    │
│  3  │ Variable playlists                    │
│  4  │ EXT-X-BYTERANGE                       │
│  5  │ EXT-X-KEY with IV                     │
│  6  │ SUBTITLES support                     │
│  7  │ Alternative audio/video               │
│  8  │ EXT-X-DEFINE, variable substitution  │
│  9  │ Content steering                      │
│ 10  │ Server control, pre-load hints        │
│ 11  │ Partial segments, low-latency         │
│ 12  │ CMAF independent segments             │
└─────┴───────────────────────────────────────┘
```

## Kod / Konfigürasyon

### HLS Segmenter Konfigürasyonu

```yaml
hls_segmenter:
  segment_duration: 6          # saniye (2-10 arası)
  list_size: 0                 # 0=sınırsız (VOD), 5=live window
  start_number: 1
  output_format: "fmp4"        # ts, fmp4 (CMAF)
  
  transport:
    individual: true           # Her segment ayrı dosya
    single_file: false         # Byte range manifest
    
  encryption:
    enabled: false
    method: "AES-128"
    key_uri: "/keys/{segment}.key"
    key_rotation: 0
    
  archive:
    type: "rolling"            # rolling, flat
    max_segments: 10
    cleanup_interval: 60
```

### Master Playlist Konfigürasyonu

```yaml
hls_master:
  version: 12
  independent_segments: true
  
  variants:
    - name: "low"
      bandwidth: 128000
      codecs: "mp4a.40.2"
      resolution: 0
      frame_rate: 0
      
    - name: "mid"
      bandwidth: 256000
      codecs: "mp4a.40.2"
      resolution: 0
      frame_rate: 0
      
    - name: "high"
      bandwidth: 512000
      codecs: "mp4a.40.2"
      resolution: 0
      frame_rate: 0
      
  alternate_audio:
    - name: "English"
      language: "en"
      default: true
      uri: "audio/eng/index.m3u8"
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg HLS | 6.1.x | Segmenter engine |
| Nginx | 1.25.x | CDN origin server |
| HLS.js | 1.5.x | Browser player |
| Apple HLS SDK | 14.x | iOS/macOS support |

## Durum: Implementasyon

HLS streaming implementasyonu aktif geliştirme aşamasındadır. Temel segmenter ve player entegrasyonu tamamlanmıştır.

| Özellik | Durum |
|---------|-------|
| TS Segmentation | Tamamlandı |
| fMP4/CMAF | Tamamlandı |
| Master Playlist | Tamamlandı |
| Adaptive Bitrate | Tamamlandı |
| AES-128 Encryption | Tamamlandı |
| Low-Latency HLS | Geliştirme |
| DRM (FairPlay) | Planlama |
| Subtitle Support | Tamamlandı |
