---
title: "Streaming Protocol Karşılaştırması"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Streaming Protocol Karşılaştırması

## Genel Bakış

Bu belge, desteklenen tüm streaming protokollerinin (HLS, DASH, Shoutcast, Icecast, RTSP) teknik karşılaştırmasını sunar. Her protokolün özelliklerini, avantajlarını ve kullanım alanlarını analiz ederek doğru teknoloji seçimini destekler.

## Protokol Genel Bakış

```
┌─────────────────────────────────────────────────────┐
│          Streaming Protocol Landscape               │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Adaptive Bitrate:                                  │
│  ├── HLS (Apple)                                   │
│  │   ├── TS segments (traditional)                 │
│  │   ├── fMP4 segments (CMAF)                      │
│  │   └── Low-Latency HLS                           │
│  ├── MPEG-DASH (MPEG)                              │
│  │   ├── Single segment (byte-range)               │
│  │   ├── Multiple segments (individual files)      │
│  │   └── Low-Latency DASH                          │
│  └── MSS (Microsoft Smooth Streaming)              │
│                                                     │
│  Progressive Streaming:                            │
│  ├── HTTP Progressive (simple download)            │
│  └── HTTP Range Request (seekable)                 │
│                                                     │
│  Live Streaming:                                   │
│  ├── SHOUTcast v2 (Nullsoft)                       │
│  ├── Icecast 2.x (Xiph)                           │
│  ├── RTSP (Real-time Streaming Protocol)           │
│  └── WebRTC (Real-time Communication)              │
│                                                     │
│  Legacy:                                           │
│  ├── MMS (Microsoft Media Server)                  │
│  └── RTMP (Real-time Messaging Protocol)           │
└─────────────────────────────────────────────────────┘
```

## Karşılaştırma Tablosu

### Temel Özellikler

```
┌──────────────┬──────────┬──────────┬──────────┬──────────┬──────────┐
│ Özellik      │ HLS      │ DASH     │ SHOUTcast│ Icecast  │ RTSP     │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ License      │ Open     │ Open     │ Open     │ Open     │ Open     │
│ Standard     │ RFC 8216 │ ISO      │ De facto │ De facto │ RFC 7826 │
│              │          │ 23009    │          │          │          │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ Container    │ TS/fMP4  │ fMP4     │ Raw      │ Raw/Ogg  │ Raw      │
│              │          │          │          │          │          │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ Manifest     │ M3U8     │ MPD      │ Headers  │ XSL/JSON │ SDP      │
│ Format       │ (text)   │ (XML)    │ (text)   │ (XML)    │ (text)   │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ Transport    │ HTTP     │ HTTP     │ TCP/UDP  │ TCP/UDP  │ UDP/TCP  │
│              │          │          │          │          │          │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ DRM Support  │ FairPlay │ Widevine │ None     │ None     │ SRTP     │
│              │ PlayReady│ PlayReady│          │          │          │
├──────────────┼──────────┼──────────┼──────────┼──────────┼──────────┤
│ Latency      │ 2-30s    │ 2-30s    │ < 1s     │ < 1s     │ < 1s     │
│              │ (LL: 2-5)│ (LL: 2-5)│          │          │          │
└──────────────┴──────────┴──────────┴──────────┴──────────┴──────────┘
```

### Adaptive Bitrate Karşılaştırması

```
ABR Protocol Comparison:
┌──────────────┬─────────────────────────────────────┐
│ Protocol     │ ABR Mekanizması                      │
├──────────────┼─────────────────────────────────────┤
│ HLS          │ Master/Variant playlist switching    │
│              │ Client-side bandwidth estimation     │
│              │ Segment download time measurement    │
│              │ Buffer-based adaptation              │
├──────────────┼─────────────────────────────────────┤
│ DASH         │ MPD Adaptation Set switching          │
│              │ Client-side bandwidth estimation     │
│              │ Quality switching at segment boundary│
│              │ Multiple adaptation algorithms       │
├──────────────┼─────────────────────────────────────┤
│ SHOUTcast    │ No ABR (single bitrate stream)      │
│ Icecast      │ No ABR (single bitrate stream)      │
│ RTSP         │ Limited (bandwidth adaptation)      │
└──────────────┴─────────────────────────────────────┘
```

### Segment Süre Karşılaştırması

```
Segment Duration Comparison:
┌──────────────┬─────────────────────────────────────┐
│ Protocol     │ Önerilen Segment Süresi              │
├──────────────┼─────────────────────────────────────┤
│ HLS          │ 2-10 saniye (varsayılan: 6s)         │
│              │ LL-HLS: 0.5-2 saniye                │
├──────────────┼─────────────────────────────────────┤
│ DASH         │ 2-10 saniye (varsayılan: 4s)         │
│              │ LL-DASH: 0.5-2 saniye               │
├──────────────┼─────────────────────────────────────┤
│ SHOUTcast    │ N/A (continuous stream)              │
│ Icecast      │ N/A (continuous stream)              │
│ RTSP         │ N/A (packet-based)                  │
└──────────────┴─────────────────────────────────────┘
```

## Teknik Analiz

### HLS vs DASH

```
HLS vs DASH Karşılaştırması:
┌──────────────────┬─────────────────┬─────────────────┐
│ Kriter           │ HLS             │ DASH            │
├──────────────────┼────────────────-┼─────────────────┤
│ Browser Desteği  │ Tümü (native)  │ Çoğu (JS player)│
│ Mobile Desteği   │ iOS (native)    │ Android (native)│
│ DRM              │ FairPlay        │ Widevine        │
│ Manifest         │ M3U8 (basit)    │ MPD (detaylı)  │
│ Segment Format   │ TS / fMP4       │ fMP4            │
│ Live Streaming   │ LL-HLS          │ LL-DASH         │
│ Offline          │ Vanilla HLS     │ DASH.js         │
│ Ekosistem        │ Apple主导        │ Open standard   │
│ phức tạplık      │ Düşük           │ Orta-Yüksek     │
└──────────────────┴─────────────────┴─────────────────┘
```

### Low-Latency Karşılaştırması

```
Low-Latency Protocol Comparison:
┌──────────────┬─────────────────────────────────────┐
│ Protokol     │ Low-Latency Özelliği                 │
├──────────────┼─────────────────────────────────────┤
│ LL-HLS       │ Partial segments, chunked transfer  │
│              │ Preload hints, blocking playlists   │
│              │ Latency: 2-5 saniye                  │
├──────────────┼─────────────────────────────────────┤
│ LL-DASH      │ Chunked CMAF, low-latency MPD      │
│              │ Availability time offset             │
│              │ Latency: 2-5 saniye                  │
├──────────────┼─────────────────────────────────────┤
│ WebRTC       │ Sub-second latency                  │
│              │ Peer-to-peer capable                 │
│              │ Latency: < 500ms                    │
├──────────────┼─────────────────────────────────────┤
│ SHOUTcast    │ Constant bitrate, low overhead     │
│              │ Latency: < 1 saniye                 │
└──────────────┴─────────────────────────────────────┘
```

## COREMUSIC Protokol Politikası

### Kullanım Alanına Göre Seçim

```yaml
protocol_selection:
  music_streaming:
    primary: "hls"
    fallback: "dash"
    segment_duration: 6
    encryption: "aes-128"
    
  podcast:
    primary: "progressive_http"
    backup: "hls"
    segment_duration: 0
    
  live_radio:
    primary: "icecast"
    fallback: "shoutcast"
    buffer_ms: 3000
    
  low_latency:
    primary: "ll-hls"
    fallback: "ll-dash"
    segment_duration: 1
    
  video_audio:
    primary: "dash"
    fallback: "hls"
    segment_duration: 4
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg | 6.1.x | All protocol support |
| HLS.js | 1.5.x | Browser HLS player |
| DASH.js | 4.7.x | Browser DASH player |
| libshout | 2.4.x | SHOUTcast/Icecast client |
| libcurl | 8.x | HTTP transport |

## Durum: Uygulama

Streaming protocol karşılaştırması ve seçimi tam olarak implemente edilmiştir. Tüm protokoller desteklenmektedir.

| Protokol | Durum |
|----------|-------|
| HLS | Tamamlandı |
| MPEG-DASH | Tamamlandı |
| SHOUTcast | Tamamlandı |
| Icecast | Tamamlandı |
| RTSP | Tamamlandı |
| WebRTC | Planlama |
| Progressive HTTP | Tamamlandı |
