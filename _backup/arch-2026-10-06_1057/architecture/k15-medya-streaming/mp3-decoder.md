---
title: "MP3 Decoder Engine"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# MP3 Decoder Engine

## Genel Bakış

MP3 decoder, MPEG-1 Audio Layer III formatının decode işlemlerini yönetir. LAME encoder entegrasyonu ile hem decode hem encode desteği sağlar. ID3v1/v2 metadata yönetimi, VBR/CBR bitrate desteği ile geniş format uyumluluğu sunar.

## MP3 Format Detayı

```
MP3 Frame Structure:
┌──────────────────────────────────────┐
│  MPEG Audio Frame Header (4 bytes)   │
│  ├── Sync Word: 11 bits (0x7FF)     │
│  ├── MPEG Version: 2 bits           │
│  ├── Layer: 2 bits                   │
│  ├── Protection: 1 bit              │
│  ├── Bitrate Index: 4 bits          │
│  ├── Sample Rate: 2 bits            │
│  ├── Padding: 1 bit                 │
│  ├── Private: 1 bit                 │
│  ├── Channel Mode: 2 bits           │
│  ├── Mode Extension: 2 bits         │
│  ├── Copyright: 1 bit               │
│  ├── Original: 1 bit                │
│  └── Emphasis: 2 bits               │
├──────────────────────────────────────┤
│  Side Information                    │
│  ├── Main Data Begin: 9 bits        │
│  ├── Scfsi: 4 × 1 bit              │
│  ├── Granule 0/1 params             │
│  └── Huffman Table Select            │
├──────────────────────────────────────┤
│  Main Data (Huffman Coded)           │
│  ├── Scalefactors                    │
│  ├── Huffman Coded Samples          │
│  └── Antialias                      │
├──────────────────────────────────────┤
│  Ancillary Data (opsiyonel)          │
└──────────────────────────────────────┘
```

## Teknik Detaylar

### MPEG Versiyon Karşılaştırması

```
┌──────────┬──────────┬──────────┬──────────┐
│          │ MPEG-1   │ MPEG-2   │ MPEG-2.5 │
├──────────┼──────────┼──────────┼──────────┤
│ Sample   │ 44100    │ 48000    │ 22050    │
│ Rate     │ 48000    │ 44100    │ 24000    │
│          │ 32000    │ 32000    │ 16000    │
├──────────┼──────────┼──────────┼──────────┤
│ Max      │ 320      │ 256      │ 160      │
│ Bitrate  │ kbps     │ kbps     │ kbps     │
├──────────┼──────────┼──────────┼──────────┤
│ Layer III│ Ster/Jo  │ Stereo   │ Stereo   │
│ Modes    │ n/Stereo │ Dual/Joi│ Dual/Jo  │
└──────────┴──────────┴──────────┴──────────┘
```

### Bitrate Tablosu (MPEG-1 Layer III)

```
Index │ Bitrate (kbps)
  0   │ Free
  1   │ 32
  2   │ 40
  3   │ 48
  4   │ 56
  5   │ 64
  6   │ 80
  7   │ 96
  8   │ 112
  9   │ 128
 10   │ 160
 11   │ 192
 12   │ 224
 13   │ 256
 14   │ 320
 15   │ Bad
```

### LAME Encoder Entegrasyonu

```
LAME Pipeline:
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│  PCM     │──▶│  Psycho- │──▶│  MDCT    │──▶│  Bit     │
│  Input   │   │  Acoustic│   │  + Huffman│   │  Stream  │
└──────────┘   └──────────┘   └──────────┘   └──────────┘

VBR Modları:
- V0: En yüksek kalite (~245 kbps)
- V1: Yüksek kalite (~225 kbps)
- V2: İyi kalite (~190 kbps) ← varsayılan
- V3: Orta kalite (~170 kbps)
- V4: Dengeli (~165 kbps)
- V5: Hafif (~130 kbps)
- V6: Düşük (~115 kbps)
- V7: Çok düşük (~100 kbps)
- V8: Minimal (~85 kbps)
- V9: En düşük (~65 kbps)
```

### ID3 Tag Yönetimi

```
ID3v1 (128 bytes):
┌──────────┬──────────┬──────────┬──────┬──────────┐
│ Title    │ Artist   │ Album    │ Year │ Comment  │
│ 30 bytes │ 30 bytes │ 30 bytes │4 byte│ 30 bytes │
└──────────┴──────────┴──────────┴──────┴──────────┘

ID3v2 (Değişken boyut):
┌──────────┬──────────┬──────────┬──────────────┐
│ Header   │ Frames   │ Padding  │ Body         │
│ 10 bytes │ Değişken │ Değişken │ Değişken     │
└──────────┴──────────┴──────────┴──────────────┘

Desteklenen ID3v2 Frame'leri:
TIT2: Title                    TPE1: Artist
TALB: Album                    TDRC: Year
TRCK: Track Number             TCON: Genre
TPOS: Disc Number              TCOM: Composer
TCOP: Copyright                TPUB: Publisher
APIC: Attached Picture         USLT: Lyrics
COMM: Comment                  TBPM: BPM
TCOM: Composer                 TKEY: Key
```

### Decode Algoritması

```
MP3 Decode Adımları:
1. Bitstream Parsing
   - Sync word bulma
   - Header decode
   - Side information okuma

2. Huffman Decoding
   - big_values × 2 sample decode
   - region0_count, region1_count
   - Table selection (0-31)

3. Dequantization
   - Scalefactor application
   - Subband gain normalization

4. Reordering
   - Short block: long→short reorder
   - Intensity stereo decoding

5. IMDCT + Windowing
   - 36→12 (long) veya 12→36 (short)
   - sine window application

6. Polyphase Filterbank
   - 32-band synthesis filter
   - Overlap-add

7. Output: PCM samples
```

## Kod / Konfigürasyon

### MP3 Decode Konfigürasyonu

```yaml
mp3_decoder:
  engine: "libmpg123"          # mpg123 veya ffmpeg
  max_threads: 2
  seek_accuracy_ms: 20
  buffer_size: 65536
  
  output:
    format: "pcm_s16le"        # pcm_s16le, pcm_s24le, pcm_f32le
    sample_rate: "auto"        # auto, 44100, 48000
    channels: "auto"           # auto, 1, 2
  
  normalization:
    enabled: true
    target_level: -3.0         # dB
    peak_limit: -0.5           # dB
```

### LAME Encode Konfigürasyonu

```yaml
lame_encoder:
  algorithm_quality: 2         # 0-9 (0=en hızlı, 9=en yavaş)
  mode: "auto"                 # auto, cbr, vbr, abr
  bitrate: 192                 # CBR/ABR: 32-320 kbps
  vbr_quality: "V2"            # V0-V9
  
  options:
    strict_iso: false
    disable_reservoir: false
    no_gap: true
    replaygain: true
    normalize: false
  
  psychoacoustic:
    model: 2                   # 0-4 (2=varsayılan)
    temporal_masking: true
    spatial_masking: true
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| LAME | 3.100 | MP3 encoding engine |
| libmpg123 | 1.31.x | MP3 decoding engine |
| FFmpeg mp3 | 6.1.x | Pipeline entegrasyonu |

## Durum: Implementasyon

MP3 decode/encode desteği tam olarak implemente edilmiştir. LAME encoder ve ID3 metadata yönetimi çalışır durumdadır.

| Özellik | Durum |
|---------|-------|
| MP3 Decode (MPEG-1/2/2.5) | Tamamlandı |
| MP3 Encode (CBR/VBR/ABR) | Tamamlandı |
| ID3v1 Reading | Tamamlandı |
| ID3v2 Reading/Writing | Tamamlandı |
| ReplayGain Tags | Tamamlandı |
| Album Art | Tamamlandı |
| Gapless Playback | Tamamlandı |
| Xing/VBR Header | Tamamlandı |
