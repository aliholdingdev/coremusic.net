---
title: "Codec Karşılaştırma Matrisi"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Codec Karşılaştırma Matrisi

## Genel Bakış

Bu belge, COREMUSIC tarafından desteklenen tüm ses codec'lerinin kapsamlı karşılaştırmasını sunar. Her codec'in teknik özelliklerini, avantaj/dezavantajlarını ve kullanım alanlarını analiz ederek karar verme sürecini destekler.

## Codec Genel Bakış

```
┌─────────────────────────────────────────────────────────────┐
│              Audio Codec Family Tree                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Lossless:                                                  │
│  ├── FLAC (Free Lossless Audio Codec)                      │
│  ├── ALAC (Apple Lossless Audio Codec)                     │
│  └── APE (Monkey's Audio)                                  │
│                                                             │
│  Lossy:                                                     │
│  ├── MPEG Layer III (MP3)                                  │
│  │   ├── CBR (Constant Bitrate)                           │
│  │   ├── VBR (Variable Bitrate)                           │
│  │   └── ABR (Average Bitrate)                            │
│  ├── AAC (Advanced Audio Coding)                           │
│  │   ├── LC-AAC (Low Complexity)                          │
│  │   ├── HE-AAC v1 (SBR)                                  │
│  │   └── HE-AAC v2 (PS)                                  │
│  ├── Opus (MKR Opus)                                      │
│  │   ├── Narrowband (8kHz)                                │
│  │   ├── Wideband (16kHz)                                 │
│  │   └── Super-wideband (24kHz)                           │
│  └── Ogg Vorbis                                           │
│      ├── Quality 0 (-1)                                   │
│      └── Quality 10 (10)                                  │
└─────────────────────────────────────────────────────────────┘
```

## Karşılaştırma Tablosu

### Teknik Özellikler

```
┌──────────┬────────────┬────────────┬────────────┬────────────┬────────────┐
│ Özellik  │ FLAC       │ MP3        │ AAC (LC)   │ Opus       │ Vorbis     │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Tip      │ Lossless   │ Lossy      │ Lossy      │ Lossy      │ Lossy      │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Max      │ 655,350    │ 48,000     │ 96,000     │ 48,000     │ 192,000    │
│ Sample   │ Hz         │ Hz         │ Hz         │ Hz         │ Hz         │
│ Rate     │            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Max      │ 32         │ 16         │ 16         │ 32         │ 256        │
│ Bit      │ bit        │ bit        │ bit        │ bit        │ bit        │
│ Depth    │            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Max      │ 8          │ 2          │ 8          │ 255        │ 255        │
│ Channels │            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Bitrate  │ Variable   │ 32-320     │ 16-640     │ 6-510      │ 45-500     │
│ Range    │ (file)     │ kbps       │ kbps       │ kbps       │ kbps       │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Latency  │ N/A        │ 100-400ms  │ 20-120ms   │ 5-65ms     │ 30-100ms   │
│          │            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ License  │ Open       │ Open       │ Patented   │ Open       │ Open       │
│          │ (BSD)      │ (LGPL)     │ (Fraunhofer│ (BSD)      │ (BSD)      │
│          │            │            │  licensing)│            │            │
└──────────┴────────────┴────────────┴────────────┴────────────┴────────────┘
```

### Bitrate vs Kalite

```
Bitrate/Kalite Matrisi:
┌──────────┬────────────┬────────────┬────────────┬────────────┐
│ Kalite   │ FLAC       │ MP3        │ AAC        │ Opus       │
│ Seviyesi │ (Sıkıştırma│ (kbps)     │ (kbps)     │ (kbps)     │
│          │  oranı)    │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┤
│ Broadcast│ N/A        │ 128 (CBR)  │ 96 (HE)    │ 64         │
├──────────┼────────────┼────────────┼────────────┼────────────┤
│ Standard │ %50-60     │ 192 (V2)   │ 128 (LC)   │ 96         │
├──────────┼────────────┼────────────┼────────────┼────────────┤
│ High     │ %45-55     │ 256 (V0)   │ 192 (LC)   │ 128        │
├──────────┼────────────┼────────────┼────────────┼────────────┤
│ Hi-Fi    │ %40-50     │ 320        │ 256 (LC)   │ 192        │
├──────────┼────────────┼────────────┼────────────┼────────────┤
│ Archive  │ %35-45     │ N/A        │ 320 (LC)   │ 256        │
│ (Hi-Res) │            │            │            │            │
└──────────┴────────────┴────────────┴────────────┴────────────┘
```

### Performans Karşılaştırması

```
Performans Metrikleri:
┌──────────┬────────────┬────────────┬────────────┬────────────┬────────────┐
│ Metrik   │ FLAC       │ MP3        │ AAC        │ Opus       │ Vorbis     │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Encode   │ Orta       │ Hızlı      │ Orta       │ Hızlı      │ Orta       │
│ Hızı     │ (L8:0.3x) │ (CBR:1.5x)│ (LC:0.8x) │ (1.0x)     │ (0.9x)     │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Decode   │ Çok Hızlı  │ Çok Hızlı  │ Hızlı      │ Çok Hızlı  │ Hızlı      │
│ Hızı     │ (3.0x)     │ (5.0x)     │ (2.5x)     │ (4.0x)     │ (3.0x)     │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ CPU      │ Düşük      │ Düşük      │ Orta       │ Düşük      │ Düşük      │
│ Kullanımı│            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Memory   │ 5-15 MB    │ 2-8 MB     │ 5-20 MB    │ 3-10 MB    │ 5-15 MB    │
│ Usage    │            │            │            │            │            │
├──────────┼────────────┼────────────┼────────────┼────────────┼────────────┤
│ Latency  │ N/A        │ 100-400ms  │ 20-120ms   │ 5-65ms     │ 30-100ms   │
│ (Decode) │            │            │            │            │            │
└──────────┴────────────┴────────────┴────────────┴────────────┴────────────┘
```

### Kullanım Alanları

```
Codec Seçim Rehberi:
┌──────────────────┬─────────────────────────────────────────┐
│ Kullanım Alanı   │ Önerilen Codec                          │
├──────────────────┼─────────────────────────────────────────┤
│ Hi-Fi Listening  │ FLAC (lossless) veya AAC-LC 256+       │
│ Podcast          │ MP3 128 (compatibility) veya HE-AAC 64 │
│ Music Streaming  │ AAC-LC 128-192 veya Opus 96-128        │
│ Voice/Conference │ Opus 32-64 (düşük gecikme)             │
│ Broadcast        │ AAC-LC 128 CBR veya Opus 96            │
│ Archive          │ FLAC (lossless, metadata)               │
│ Mobile/Weak HW   │ HE-AAC v2 32-64 veya Opus 32-64       │
│ Game Audio       │ Opus 64-128 (düşük gecikme)            │
│ Web Apps         │ Opus (WebRTC) veya AAC-LC (Safari)     │
│ VoIP             │ Opus 32 (düşük gecikme, jitter)        │
└──────────────────┴─────────────────────────────────────────┘
```

## Teknik Analiz

### Frekans Yanıtı Analizi

```
Frequency Response Comparison:
┌──────────────────────────────────────────────────────┐
│                                                      │
│  FLAC: 20Hz - 96kHz (16bit: ~96dB SNR)             │
│  ████████████████████████████████████████████         │
│                                                      │
│  MP3 320: 20Hz - 20kHz (SNR ~100dB)                │
│  ████████████████████████████████████                 │
│                                                      │
│  AAC-LC 192: 20Hz - 20kHz (SNR ~95dB)              │
│  ███████████████████████████████████                  │
│                                                      │
│  Opus 128: 20Hz - 20kHz (SNR ~100dB)               │
│  ████████████████████████████████████                 │
│                                                      │
│  AAC-HE 64: 20Hz - 14kHz (SBR extension)           │
│  ███████████████████████████                          │
│                                                      │
│  MP3 128: 20Hz - 16kHz (SNR ~85dB)                 │
│  █████████████████████████████                        │
│                                                      │
└──────────────────────────────────────────────────────┘
```

### Joint Stereo Desteği

```
Stereo Coding Techniques:
┌──────────┬────────────┬────────────┬─────────────────────┐
│ Codec    │ Mid/Side   │ Intensity  │ Parametric Stereo   │
├──────────┼────────────┼────────────┼─────────────────────┤
│ FLAC     │ Evet       │ Hayır      │ Hayır               │
│ MP3      │ Evet       │ Evet       │ Hayır               │
│ AAC-LC   │ Evet       │ Evet       │ Hayır               │
│ HE-AAC   │ Evet       │ Evet       │ Evet (v2)           │
│ Opus     │ Evet       │ Evet       │ Evet                │
│ Vorbis   │ Evet       │ Evet       │ Hayır               │
└──────────┴────────────┴────────────┴─────────────────────┘
```

## COREMUSIC Codec Politikası

### Varsayılan Codec Seçimleri

```yaml
codec_policy:
  default_lossless: "flac"
  default_lossy: "aac_he"
  streaming:
    low_bandwidth: "opus@64"
    medium_bandwidth: "aac_lc@128"
    high_bandwidth: "aac_lc@192"
    
  mobile:
    offline: "aac_he_v2@64"
    streaming: "opus@64"
    
  broadcast:
    format: "aac_lc@128"
    backup: "mp3@128"
    
  archive:
    format: "flac"
    backup: "mp3@320"
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FFmpeg | 6.1.x | Tüm codec'ler |
| libFLAC | 1.4.x | FLAC encode/decode |
| LAME | 3.100 | MP3 encode |
| FDK-AAC | 2.0.x | AAC encode/decode |
| libopus | 1.4.x | Opus encode/decode |
| libvorbis | 1.3.x | Vorbis encode/decode |

## Durum: Uygulama

Codec karşılaştırma ve seçimi tam olarak implemente edilmiştir. Tüm codec'ler encode/decode desteği ile entegre edilmiştir.

| Codec | Encode | Decode | Metadata | Streaming |
|-------|--------|--------|----------|-----------|
| FLAC | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| MP3 | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| AAC | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| Opus | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| Vorbis | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
