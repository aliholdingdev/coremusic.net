---
title: "MPEG-DASH Streaming"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# MPEG-DASH Streaming

## Genel Bakış

MPEG-DASH (Dynamic Adaptive Streaming over HTTP), MPEG tarafından geliştirilen açık standart adaptive bitrate streaming protokolüdür. XML tabanlı manifest yapısı ile platformlar arası uyumluluk sağlar. COREMUSIC, DASH.js player entegrasyonu ile hem VOD hem Live streaming desteği sunar.

## DASH Mimarisi

```
┌─────────────────────────────────────────────────┐
│              DASH Architecture                  │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │ Encoder  │───▶│ Packager │───▶│ Origin   │  │
│  │ (AAC/    │    │ (DASH    │    │ Server   │  │
│  │  Opus)   │    │  MPD)    │    │          │  │
│  └──────────┘    └──────────┘    └────┬─────┘  │
│                                       │        │
│  MPD (Media Presentation Description) │        │
│  ├── Period 0 ────────────────────────┤        │
│  │   ├── Adaptation Set 0 (Audio)    │        │
│  │   │   ├── Representation 0 (64k)  │        │
│  │   │   ├── Representation 1 (128k) │        │
│  │   │   └── Representation 2 (256k) │        │
│  │   └── Adaptation Set 1 (Subs)     │        │
│  └── Period 1 ────────────────────────┘        │
│      └── ...                                   │
│                                                 │
│  Segment │ Segment │ Segment │ Segment         │
│  init.mp4│001.m4s  │002.m4s  │003.m4s          │
└─────────────────────────────────────────────────┘
```

## Teknik Detaylar

### MPD (Media Presentation Description)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<MPD xmlns="urn:mpeg:dash:schema:mpd:2011"
     type="static"
     mediaPresentationDuration="PT3M30S"
     minBufferTime="PT2S"
     profiles="urn:mpeg:dash:profile:isoff-on-demand:2011">

  <Period id="0" start="PT0S">
    <AdaptationSet id="0" mimeType="audio/mp4" 
                   lang="en" contentType="audio">
      
      <Representation id="64k" bandwidth="64000"
                      codecs="mp4a.40.5">
        <SegmentTemplate timescale="44100" 
                         initialization="audio_64k_init.m4s"
                         media="audio_64k_$Number$.m4s"
                         startNumber="1"/>
      </Representation>
      
      <Representation id="128k" bandwidth="128000"
                      codecs="mp4a.40.2">
        <SegmentTemplate timescale="44100"
                         initialization="audio_128k_init.m4s"
                         media="audio_128k_$Number$.m4s"
                         startNumber="1"/>
      </Representation>
      
    </AdaptationSet>
  </Period>
</MPD>
```

### Segment Tipleri

```
┌──────────────────┬────────────────────────────────┐
│ Segment Tipi     │ Kullanım                       │
├──────────────────┼────────────────────────────────┤
│ Single Segment   │ Byte-range, VOD optimizasyonu  │
│ Multiple Segment │ Individual dosyalar, Live       │
│ SegmentTemplate  │ URL template-based             │
│ SegmentTimeline  │ Değişken süreli segmentler     │
│ SegmentBase      │ Byte-range addressing          │
└──────────────────┴────────────────────────────────┘
```

### Adaptation Set Seçimi

```
Adaptation Selection Logic:
1. MimeType eşleşmesi
2. Codec desteği kontrolü
3. Bandwidth tercihi
4. Language/role eşleşmesi
5. Supplemental descriptor eşleşmesi

Representation Selection:
if (player_bandwidth >= 256kbps)
    → Representation@bandwidth=256000
if (player_bandwidth >= 128kbps)
    → Representation@bandwidth=128000
else
    → Representation@bandwidth=64000
```

### DASH.js Player Entegrasyonu

```
DASH.js Player Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ MPD      │───▶│ Stream   │───▶│ Buffer   │───▶│ Render   │
│ Parser   │    │ Selector │    │ Manager  │    │ Engine   │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  XML Parse    ABR Algorithm   Buffer Level   Media Source
  Segment URL  Quality Switch  Stalling       Playback
```

### Low-Latency DASH (LL-DASH)

```
LL-DASH Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Encoder  │───▶│ Partial  │───▶│ Chunk    │
│ (Low     │    │ Segment  │    │ Transfer │
│  Latency)│    │          │    │          │
└──────────┘    └──────────┘    └──────────┘

Partial Segments:
- chunk_duration: 100-500ms
- transfer_encoding: chunked
- available_time: PT0S
- time_shift_buffer: PT30S

Latency Hedefleri:
- Traditional DASH: 6-12 saniye
- Low-Latency DASH: 2-5 saniye
- Ultra-Low-Latency: < 2 saniye
```

### DRM Entegrasyonu

```
DASH DRM Methods:
┌──────────────┬────────────────────────────────┐
│ DRM System   │ Entegrasyon                    │
├──────────────┼────────────────────────────────┤
│ Widevine     │ Chrome, Android, Firefox       │
│ PlayReady    │ Edge, Windows, Xbox            │
│ FairPlay     │ Safari, iOS, tvOS              │
│ ClearKey     │ Open source testing            │
└──────────────┴────────────────────────────────┘

PSSH (Protection System Specific Header):
- Widevine PSSH: Init segment'te
- PlayReady PSSH: Header encoding
- FairPlay PSSH: HLS key exchange
```

## Kod / Konfigürasyon

### DASH Packager Konfigürasyonu

```yaml
dash_packager:
  output_format: "fmp4"
  segment_duration: 4
  segment_alignment: true
  time_shift_buffer_depth: 30
  
  mpd:
    type: "static"              # static (VOD), dynamic (Live)
    min_buffer_time: "PT2S"
    profiles: "urn:mpeg:dash:profile:isoff-on-demand:2011"
    
  audio_adaptation:
    id: "audio"
    mime_type: "audio/mp4"
    lang: "en"
    representations:
      - id: "64k"
        bandwidth: 64000
        codecs: "mp4a.40.5"
      - id: "128k"
        bandwidth: 128000
        codecs: "mp4a.40.2"
      - id: "256k"
        bandwidth: 256000
        codecs: "mp4a.40.2"
```

### DASH.js Player Konfigürasyonu

```yaml
dashjs_player:
  streaming:
    abr:
      autoSwitchBitrate: true
      maxBitrate: 512000
      minBitrate: 32000
      initialBitrate: 128000
      
    buffer:
      fastSwitchEnabled: true
      bufferTimeAtTopQuality: 30
      bufferTimeWhileBuffering: 15
      
    delay:
      liveDelay: 12
      liveCatchupEnabled: true
      liveCatchupPlaybackRate: 1.5
      
  protection:
    enabled: false
    drm: "widevine"
    serverURL: "https://drm.example.com/license"
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| DASH.js | 4.7.x | Browser player |
| FFmpeg DASH | 6.1.x | Packager engine |
| Bento4 | 1.6.x | MP4/FMP4 tools |
| MPEG-DASH Schema | 2011 | MPD validation |

## Durum: Implementasyon

DASH streaming implementasyonu aktif geliştirme aşamasındadır. Temel MPD generation ve VOD segmentasyonu tamamlanmıştır.

| Özellik | Durum |
|---------|-------|
| Static MPD (VOD) | Tamamlandı |
| Dynamic MPD (Live) | Tamamlandı |
| SegmentTemplate | Tamamlandı |
| SegmentTimeline | Tamamlandı |
| ABR Algorithm | Tamamlandı |
| Low-Latency DASH | Geliştirme |
| DRM (Widevine) | Tamamlandı |
| DRM (FairPlay) | Planlama |
