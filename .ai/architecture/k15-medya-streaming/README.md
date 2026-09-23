---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K15 Medya & Streaming Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K15: Medya & Streaming Layer

**Katman:** K15 (Medya & Streaming)
**Kapsam:** FFmpeg, FLAC, MP3, HLS, DASH, Podcast, Radio
**Sorumlu Agent:** Media Engineer
**Bileşen Sayısı:** 35

---

## 1. Genel Bakış

K15 katmanı, CoreMusic'in medya işleme ve streaming altyapısını içerir.

---

## 2. Desteklenen Formatlar

| Format | Tipo | Kullanım |
|--------|------|----------|
| FLAC | Lossless | Ana format (24/32-bit) |
| WAV | Lossless | Ham ses |
| MP3 | Lossy | Uyumluluk |
| AAC | Lossy | Streaming |
| OGG | Lossy | Open source |
| ALAC | Lossless | Apple uyumluluk |

---

## 3. FFmpeg Pipeline

### 3.1 Transcode Pipeline

```
Input → Demux → Decode → Process → Encode → Mux → Output

Örnekler:
  FLAC 24/96 → FLAC 24/48 (sample rate conversion)
  FLAC 24/96 → MP3 320kbps (lossy compression)
  FLAC 24/96 → AAC 256kbps (streaming)
```

### 3.2 FFmpeg Komutları

```bash
# FLAC to MP3
ffmpeg -i input.flac -codec:a libmp3lame -b:a 320k output.mp3

# Sample rate conversion
ffmpeg -i input.flac -ar 48000 output.flac

# 8.1 Surround downmix to stereo
ffmpeg -i input_8.1.flac -ac 2 output_stereo.flac
```

---

## 4. HLS Streaming

### 4.1 HLS Segment Yapısı

```
playlist.m3u8
├── segment_000.ts (2-10 seconds)
├── segment_001.ts
├── segment_002.ts
└── ...

Bitrate Ladder:
  - 96kbps (low)
  - 160kbps (medium)
  - 320kbps (high)
  - 1411kbps (lossless/FLAC)
```

---

## 5. ID3 Metadata

### 5.1 Desteklenen Tag'ler

| Tag | Alan |
|-----|------|
| Title | Şarkı adı |
| Artist | Sanatçı |
| Album | Albüm |
| Year | Yıl |
| Genre | Tür |
| Track | Parça numarası |
| Disc | Disk numarası |
| Artwork | Kapak görseli |
| Lyrics | Sözler |
| BPM | Tempo |

---

## 6. Podcast & Radio

### 6.1 Podcast RSS

```xml
<rss version="2.0">
  <channel>
    <title>CoreMusic Podcast</title>
    <item>
      <title>Episode 1</title>
      <enclosure url="https://..." type="audio/mpeg"/>
      <duration>00:30:00</duration>
    </item>
  </channel>
</rss>
```

---

*K15 Medya & Streaming Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
