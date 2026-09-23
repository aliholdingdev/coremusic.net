---
title: "AAC Decoder Engine"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# AAC Decoder Engine

## Genel Bakış

AAC decoder, MPEG-2/MPEG-4 Advanced Audio Coding formatının decode işlemlerini FDK-AAC library üzerinden yönetir. HE-AAC (High-Efficiency) ve HE-AAC v2 desteği ile düşük bitrate'lerde yüksek kalite sağlar. Streaming ve podcast uygulamaları için optimize edilmiştir.

## AAC Format Detayı

```
AAC Profile Hierarchy:
┌──────────────────────────────────────────┐
│  LC-AAC (Low Complexity)                 │
│  ├── En yaygın profile                   │
│  ├── bitrate: 96-320 kbps               │
│  └── Coder: MDCT + TNS + PNS            │
├──────────────────────────────────────────┤
│  HE-AAC v1 (High Efficiency)             │
│  ├── LC-AAC + SBR (Spectral Band Rep.)  │
│  ├── bitrate: 48-96 kbps                │
│  └── Frekans genişletme tekniği          │
├──────────────────────────────────────────┤
│  HE-AAC v2 (Parametric Stereo)           │
│  ├── HE-AAC + PS (Parametric Stereo)    │
│  ├── bitrate: 24-48 kbps                │
│  └── Stereo genelleme tekniği            │
├──────────────────────────────────────────┤
│  LD-AAC (Low Delay)                      │
│  ├── Düşük gecikme (< 20ms)             │
│  ├── bitrate: 64-320 kbps               │
│  └── Real-time communication için        │
├──────────────────────────────────────────┤
│  HE-AAC v2 + xHE-AAC (USAC)             │
│  ├── Unified Speech and Audio Coding     │
│  ├── bitrate: 12-64 kbps                │
│  └── Speech + music optimizasyonu        │
└──────────────────────────────────────────┘
```

## Teknik Detaylar

### AAC Kodlama Pipeline

```
Encoder:
┌─────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│ PCM │─▶│ Filter   │─▶│ MDCT     │─▶│ TNS/PNS  │─▶│ Huffman  │
│     │  │ Bank     │  │          │  │          │  │ + Quant  │
└─────┘  └──────────┘  └──────────┘  └──────────┘  └──────────┘

Decoder (Ters):
┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌─────┐
│ Huffman  │─▶│ Dequant  │─▶│ IMDCT    │─▶│ Filter   │─▶│ PCM │
│ Decode   │  │ + PNS   │  │ + TNS   │  │ Bank     │  │     │
└──────────┘  └──────────┘  └──────────┘  └──────────┘  └─────┘
```

### SBR (Spectral Band Replication)

```
SBR İşleme Akışı:
┌────────────┐    ┌────────────┐    ┌────────────┐
│ Core AAC   │    │ SBR        │    │ HF         │
│ Decode     │───▶│ Analysis   │───▶│ Reconstruction│
│ (0-6kHz)   │    │            │    │ (6-20kHz)  │
└────────────┘    └────────────┘    └────────────┘

SBR Parametreleri:
- frequency: 0-6 kHz (core codec)
- extension: 6-20 kHz (SBR)
- noise: Alt band noise floor
- envelope: Spectral envelope
- startfreq: Başlangıç frekansı
- stopfreq: Bitiş frekansı
```

### Parametric Stereo (PS)

```
PS İşleme Akışı:
┌────────────┐    ┌────────────┐    ┌────────────┐
│ Mono       │    │ PS         │    │ Stereo     │
│ Core + SBR │───▶│ Decoder    │───▶│ Output     │
│            │    │            │    │            │
└────────────┘    └────────────┘    └────────────┘

PS Parametreleri:
- ipd: Inter-channel Phase Difference
- opd: Overall Phase Difference
- ms: Mid/Side stereo
- iid: Inter-channel Intensity Difference
```

### Container Desteği

```
AAC Container Formats:
┌────────────────┬────────────────────────────┐
│ Format         │ Kullanım Alanı              │
├────────────────┼────────────────────────────┤
│ ADTS           │ Raw streaming              │
│ ADIF           │ Dosya tabanlı              │
│ MP4/M4A        │ iTunes, web browsers       │
│ MKV/MKA        │ Multimedia container       │
│ FLV/F4A        │ Flash (eski)               │
│ LATM/LOAS      │ MPEG transport stream      │
│ 3GP            │ Mobile                     │
└────────────────┴────────────────────────────┘
```

### FDK-AAC Library Entegrasyonu

```
FDK-AAC API Calls:
1. aacDecoder_Open()
   └── Transport format seçimi

2. aacDecoder_ConfigRaw()
   └── Raw configuration yükleme

3. aacDecoder_Fill()
   └── Input buffer doldurma

4. aacDecoder_DecodeFrame()
   └── Frame decode işlemi

5. aacDecoder_GetStreamInfo()
   └── Stream bilgisi alma

6. aacDecoder_Close()
   └── Kaynakları serbest bırakma
```

## Kod / Konfigürasyon

### AAC Decode Konfigürasyonu

```yaml
aac_decoder:
  engine: "fdk-aac"            # fdk-aac, ffmpeg_aac
  max_threads: 2
  
  transport:
    format: "adts"             # adts, adif, latm, raw
    raw_config: null
  
  output:
    format: "pcm_f32le"        # pcm_s16le, pcm_f32le
    sample_rate: "auto"
    channels: "auto"
    bit_depth: 32
  
  sbr:
    enabled: true
    mode: "auto"               # auto, forced_sbr, forced_mps
    downsample_sbr: false
  
  ps:
    enabled: true
    mode: "auto"               # auto, forced_ps, off
```

### AAC Encode Konfigürasyonu

```yaml
fdk_aac_encoder:
  profile: "aac_he_v2"         # aac_low, aac_main, aac_he, aac_he_v2
  bitrate: 128                 # 16-640 kbps
  vbr: 3                       # 1-5 (1=en düşük, 5=en yüksek)
  
  options:
    afterburner: true          # Yüksek kalite ama yavaş
    aac_bits_per_sample: 0
    sbr_ratio: 0               # 0=auto, 1=half, 2=quarter
  
  transport:
    format: "adts"
    mux_config_period: 10
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| FDK-AAC | 2.0.x | Core AAC codec |
| FFmpeg aac | 6.1.x | Pipeline entegrasyonu |
| libfdk-aac | 2.0.x | Encoder/Decoder |

## Durum: Implementasyon

AAC decode/encode desteği tam olarak implemente edilmiştir. SBR ve PS desteği ile low-bitrate streaming optimize edilmiştir.

| Özellik | Durum |
|---------|-------|
| LC-AAC Decode/Encode | Tamamlandı |
| HE-AAC v1 (SBR) | Tamamlandı |
| HE-AAC v2 (PS) | Tamamlandı |
| ADTS Transport | Tamamlandı |
| MP4/M4A Container | Tamamlandı |
| LD-AAC | Tamamlandı |
| xHE-AAC (USAC) | Planlama |
