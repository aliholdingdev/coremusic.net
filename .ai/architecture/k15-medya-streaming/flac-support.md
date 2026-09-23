---
title: "FLAC Lossless Audio Support"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# FLAC Lossless Audio Support

## Genel Bakış

FLAC (Free Lossless Audio Codec), COREMUSIC'in birincil lossless ses formatıdır. Hi-Fi kalitesinde kayıpsız sıkıştırma sağlar ve %50-60 oranında dosya boyutu küçültme ile orijinal sinyal bütünlüğünü korur. Metadata desteğinin yanı sıra ReplayGain entegrasyonu ile dinamik sesame kontrol sağlar.

## FLAC Format Detayı

```
FLAC File Structure:
┌──────────────────────────────────────────┐
│  "fLaC" Header (4 bytes)                │
├──────────────────────────────────────────┤
│  METADATA_BLOCK (bir veya daha fazla)   │
│  ├── STREAMINFO (zorunlu)                │
│  ├── PADDING (opsiyonel)                 │
│  ├── APPLICATION (opsiyonel)             │
│  ├── SEEKTABLE (opsiyonel)               │
│  ├── VORBIS_COMMENT (opsiyonel)          │
│  └── PICTURE (opsiyonel)                 │
├──────────────────────────────────────────┤
│  FRAME (bir veya daha fazla)             │
│  ├── Frame Header                        │
│  ├── Subframes (bir veya daha fazla)     │
│  └── Frame Footer (CRC-16)              │
└──────────────────────────────────────────┘
```

## Teknik Detaylar

### FLAC Bitdepth ve Sample Rate Desteği

| Parametre | Minimum | Maksimum | Varsayılan |
|-----------|---------|----------|------------|
| Bit Depth | 4-bit | 32-bit | 16-bit / 24-bit |
| Sample Rate | 1 Hz | 655,350 Hz | 44,100 Hz / 96,000 Hz |
| Kanal | 1 | 8 | 2 (Stereo) |
| Max Dosya Boyutu | - | 4 GB | - |

### Sıkıştırma Seviyeleri

```
Level 0: Hızlı encode, düşük sıkıştırma (%50)
Level 1: Hafif optimizasyon (%48)
Level 2: Dengeli (%46)
Level 3: Orta (%44)
Level 4: İyi sıkıştırma (%42)
Level 5: Yüksek sıkıştırma (%40)
Level 6: Yüksek kalite (%39) ← varsayılan
Level 7: Çok yüksek (%38)
Level 8: Maksimum (%37)
Level 9: En yüksek, yavaş (%36)
```

### Konseptual Kodlama

```
FLAC Encoder Pipeline:
┌─────────┐   ┌──────────┐   ┌────────────┐   ┌─────────┐
│  PCM    │──▶│  LPC     │──▶│  Rice      │──▶│  Bit    │
│  Input  │   │  Predictor│   │  Coder    │   │  Stream │
└─────────┘   └──────────┘   └────────────┘   └─────────┘

1. Subframe Prediction:
   - Verbatim: Ham veri (düşük sıkıştırma)
   - Constant: Tek değer (sabit tone)
   - Fixed: Sabit polinom (0-4. mertebe)
   - LPC: Lineer Prediktif Kodlama

2. Residual Coding:
   - Rice coding ile kalan sinyaller sıkıştırılır
   - Partition size: 2^n samples
```

### ReplayGain Entegrasyonu

```
ReplayGain Tags (Vorbis Comments):
replaygain_track_gain: -6.54 dB
replaygain_track_peak: 0.98234567
replaygain_album_gain: -3.21 dB
replaygain_album_peak: 0.99876543
replaygain_reference_loudness: 89.0 dB

İşlem Akışı:
1. Dosyanın RMS loudness'u hesaplanır
2. Referans seviyeye (89 dB) göre gain farkı bulunur
3. Peak değeri clipping önleme için kaydedilir
4. Playback sırasında gain uygulanır
```

### Metadata Desteği

```
Vorbis Comments (FLAC Metadata):
TITLE=Şarkı Adı
ARTIST=Sanatçı
ALBUM=Albüm
DATE=2026
TRACKNUMBER=3
GENRE=Rock
ALBUMARTIST=Ana Sanatçı
COMMENT=Studio recording
COMPOSER=Besteci
DISCNUMBER=1
TOTALDISCS=2
ISRC=USRUL1234567
BPM=120

Embedded Album Art:
- Format: JPEG/PNG
- Max boyut: 1024x1024 px
- Tip: Cover (3), Back (4), Artist (6)
```

## Kod / Konfigürasyon

### FLAC Encode Ayarları

```yaml
flac_encoder:
  compression_level: 8
  block_size: 4096          # 576-65535 arası
  max_lpc_order: 12         # 0-32 arası (0=auto)
  rice_partition_order: 3   # 0-15 arası
  verify: true              # Encode sonrası checksum kontrolü
  do_mid_side: true         # Mid-Side stereo encoding
  extra_mid_side: true      # Ekstra mid-side optimizasyonu
  avoid_escape: true        # Rice coding kaçışını önle
```

### FLAC Decode Ayarları

```yaml
flac_decoder:
  max_threads: 4
  seek_accuracy_ms: 10
  buffer_size: 65536
  enable_replaygain: true
  enable_r128_tracking: true
  output_format: "pcm_s32le"  # Float output için
```

### ReplayGain Konfigürasyonu

```yaml
replaygain:
  enabled: true
  reference_loudness: 89.0    # dB
  target_loudness: 89.0
  prevent_clipping: true
  mode: "track"               # track, album, auto
  preamp: 0.0                 # dB offset
  sound_check: true           # R128 ile güncelleme
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| libFLAC | 1.4.x | Core FLAC encode/decode |
| FFmpeg FLAC | 6.1.x | Pipeline entegrasyonu |
| libvorbis | 1.3.x | Vorbis Comment okuma |
| libchromaprint | 1.5.x | Audio fingerprinting |

## Durum: Implementasyon

FLAC desteği tam olarak implemente edilmiştir. Tüm encode/decode seviyeleri ve metadata yönetimi çalışır durumdadır.

| Özellik | Durum |
|---------|-------|
| FLAC Decode | Tamamlandı |
| FLAC Encode (L0-L9) | Tamamlandı |
| Vorbis Comments | Tamamlandı |
| ReplayGain | Tamamlandı |
| Album Art Embed | Tamamlandı |
| Seek Table | Tamamlandı |
| Multi-channel | Tamamlandı |
| Hi-Res (24/96+) | Tamamlandı |
