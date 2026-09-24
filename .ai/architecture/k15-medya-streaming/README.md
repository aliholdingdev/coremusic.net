---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K15 Medya & Streaming Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
son_guncelleme: "2026-09-24, kaynak: 3 turlu agent tartışması"
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
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*


---

## Alt Katman Şeması (K15.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K15 katmanını onaylı şema biçiminde (K15 → K15.a → K15.a.b → K15.a.b.c) belgeler. Alanlar (a) medya işleme aileleri, alt alanlar (b) dosyalardaki H2 bölüm başlıkları, yapraklar (c) k15-medya-streaming/ altındaki kanonik MD dosyalarındaki gerçek H2/H3 bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosya ve bölümünü gösterir. Uydurma düğüm yoktur.

**Şema kuralları:**

1. Zorunlu şema: `K15` → `K15.a` → `K15.a.b`; seviye-4 (`K15.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K15 pipeline/protokol sahibidir (FFmpeg, transcode, HLS/DASH, ffmpeg-pipeline.md tümü K15'tedir); K15'ten K16–K20'ye ve K5'e yazmak yasaktır.
5. Bağımlılık bağlamı: K15 → K14 (TEK hedef — matris §2.2, §1.3 K2). K15 → K5 yasaktır (matris §5.1 #6); K15 → K16–K20 yasaktır (matris §5.2 #17).

> | K8 → K15 | FFmpeg/transcode K15'tedir; K15 yalnızca K14 üzerinden iletişim kurar → K8→K15 doğrudan çağrı SINIR DIŞI | CLAUDE §5 K15 |

6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K15` (a alan) | 9 | 9 | 9 | +0 |
| `K15.a.b` (a×b alt alan) | 54 | 54 | — | 0 |
| `K15.a.b.c` (c yaprak) | 170 | 170 | 176 | +6 |
| **Toplam düğüm** | **224** | **224** | **230** | **+6** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K15.1` | FFmpeg Hattı & Transcoding | 6 | 26 | `ffmpeg-pipeline.md`, `audio-transcoding.md` |
| `K15.2` | Kayıpsız Codec (FLAC) | 6 | 14 | `flac-support.md` |
| `K15.3` | Kayıplı Codec'ler (MP3/AAC) | 6 | 25 | `mp3-decoder.md`, `aac-decoder.md` |
| `K15.4` | Codec Karşılaştırma & Politika | 6 | 14 | `codec-comparison.md` |
| `K15.5` | HLS Streaming | 6 | 13 | `hls-streaming.md` |
| `K15.6` | DASH Streaming | 6 | 13 | `dash-streaming.md` |
| `K15.7` | Protokol Politikası (HLS vs DASH) | 6 | 13 | `streaming-protocol.md` |
| `K15.8` | Radyo & Podcast Akışı | 6 | 25 | `radio-streaming.md`, `podcast-support.md` |
| `K15.9` | Dağıtım & Metadata | 6 | 27 | `content-delivery.md`, `media-metadata.md` |

### K15.1 — FFmpeg Hattı & Transcoding

**Sorumluluk:** FFmpeg 6.x ile decode→işleme→encode hattı, context/workflow yönetimi, worker havuzu, gerçek zamanlı ve batch transcode.
**Kanıt dosyaları:** `ffmpeg-pipeline.md`, `audio-transcoding.md` — havuz 27 başlık, kullanıldı 26, seçim dışı 1.

#### K15.1.1 — Genel Bakış

- **Sorumluluk:** FFmpeg pipeline, COREMUSIC'in merkezi medya işleme altyapısıdır. Tüm ses dosyalarının decode, transform ve encode süreçlerini FFmpeg 6.x API üzerinden yönetir.…
- **Kanıt:** `ffmpeg-pipeline.md`, `audio-transcoding.md` § Genel Bakış (2 bölüm başlığı)

- **K15.1.1.1 — Genel Bakış**
  - Sorumluluk: FFmpeg pipeline, COREMUSIC'in merkezi medya işleme altyapısıdır. Tüm ses dosyalarının decode, transform ve encode süreçlerini FFmpeg 6.x API üzerinden yönetir. Hardware acceleration desteği ile…
  - Kanıt: `ffmpeg-pipeline.md` § Genel Bakış
- **K15.1.1.2 — Genel Bakış**
  - Sorumluluk: Audio transcoding engine, real-time ve batch modlarda ses format dönüşümlerini yönetir. Circular buffer ile jitt absorption, sample rate conversion, ve bitdepth conversion işlemlerini optimize eder.…
  - Kanıt: `audio-transcoding.md` § Genel Bakış

#### K15.1.2 — FFmpeg Versiyon ve Derleme / Pipeline Mimarisi

- **Sorumluluk:** «FFmpeg Versiyon ve Derleme» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
- **Kanıt:** `ffmpeg-pipeline.md` § FFmpeg Versiyon ve Derleme / Pipeline Mimarisi (2 bölüm başlığı)

- **K15.1.2.3 — FFmpeg Versiyon ve Derleme**
  - Sorumluluk: «FFmpeg Versiyon ve Derleme» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § FFmpeg Versiyon ve Derleme
- **K15.1.2.4 — Pipeline Mimarisi**
  - Sorumluluk: «Pipeline Mimarisi» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Pipeline Mimarisi

#### K15.1.3 — Teknik Detaylar

- **Sorumluluk:** Her encoding oturumu bağımsız bir FFmpeg context oluşturur:
- **Kanıt:** `ffmpeg-pipeline.md`, `audio-transcoding.md` § Teknik Detaylar (12 bölüm başlığı)

- **K15.1.3.5 — Teknik Detaylar**
  - Sorumluluk: Her encoding oturumu bağımsız bir FFmpeg context oluşturur:
  - Kanıt: `ffmpeg-pipeline.md` § Teknik Detaylar
- **K15.1.3.6 — FFmpeg Context Yönetimi**
  - Sorumluluk: Her encoding oturumu bağımsız bir FFmpeg context oluşturur:
  - Kanıt: `ffmpeg-pipeline.md` § Teknik Detaylar > FFmpeg Context Yönetimi
- **K15.1.3.7 — Transcoding Workflow**
  - Sorumluluk: Input Probing: avformat_open_input() ile format tespiti Stream Selection: avformat_find_stream_info() ile codec bulma
  - Kanıt: `ffmpeg-pipeline.md` § Teknik Detaylar > Transcoding Workflow
- **K15.1.3.8 — Hız Optimizasyonları**
  - Sorumluluk: «Hız Optimizasyonları» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Teknik Detaylar > Hız Optimizasyonları
- **K15.1.3.9 — Buffer Yönetimi**
  - Sorumluluk: «Buffer Yönetimi» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Teknik Detaylar > Buffer Yönetimi
- **K15.1.3.10 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar
- **K15.1.3.11 — Buffer Yönetimi**
  - Sorumluluk: «Buffer Yönetimi» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Buffer Yönetimi
- **K15.1.3.12 — Sample Rate Conversion**
  - Sorumluluk: «Sample Rate Conversion» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Sample Rate Conversion
- **K15.1.3.13 — Bitdepth Conversion**
  - Sorumluluk: «Bitdepth Conversion» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Bitdepth Conversion
- **K15.1.3.14 — Channel Mapping**
  - Sorumluluk: «Channel Mapping» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Channel Mapping
- **K15.1.3.15 — Real-time Transcoding**
  - Sorumluluk: «Real-time Transcoding» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Real-time Transcoding
- **K15.1.3.16 — Batch Transcoding**
  - Sorumluluk: «Batch Transcoding» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Teknik Detaylar > Batch Transcoding

#### K15.1.4 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
- **Kanıt:** `ffmpeg-pipeline.md`, `audio-transcoding.md` § Kod / Konfigürasyon (6 bölüm başlığı)

- **K15.1.4.17 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Kod / Konfigürasyon
- **K15.1.4.18 — FFmpeg Config**
  - Sorumluluk: «FFmpeg Config» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Kod / Konfigürasyon > FFmpeg Config
- **K15.1.4.19 — Pipeline Konfigürasyonu**
  - Sorumluluk: «Pipeline Konfigürasyonu» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Kod / Konfigürasyon > Pipeline Konfigürasyonu
- **K15.1.4.20 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Kod / Konfigürasyon
- **K15.1.4.21 — Transcoding Engine Konfigürasyonu**
  - Sorumluluk: «Transcoding Engine Konfigürasyonu» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Kod / Konfigürasyon > Transcoding Engine Konfigürasyonu
- **K15.1.4.22 — Worker Pool Konfigürasyonu**
  - Sorumluluk: «Worker Pool Konfigürasyonu» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Kod / Konfigürasyon > Worker Pool Konfigürasyonu

#### K15.1.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
- **Kanıt:** `ffmpeg-pipeline.md`, `audio-transcoding.md` § Bağımlılıklar (2 bölüm başlığı)

- **K15.1.5.23 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — ffmpeg-pipeline.md dosyasında belgelenen bölüm.
  - Kanıt: `ffmpeg-pipeline.md` § Bağımlılıklar
- **K15.1.5.24 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Bağımlılıklar

#### K15.1.6 — Durum: Implementasyon / Transcoding Pipeline Mimarisi

- **Sorumluluk:** FFmpeg pipeline temel fonksiyonları ile tamamen çalışır durumdadır. GPU acceleration ve multi-stream concurrency desteği eklenmiştir.
- **Kanıt:** `ffmpeg-pipeline.md`, `audio-transcoding.md` § Durum: Implementasyon / Transcoding Pipeline Mimarisi (2 bölüm başlığı)

- **K15.1.6.25 — Durum: Implementasyon**
  - Sorumluluk: FFmpeg pipeline temel fonksiyonları ile tamamen çalışır durumdadır. GPU acceleration ve multi-stream concurrency desteği eklenmiştir.
  - Kanıt: `ffmpeg-pipeline.md` § Durum: Implementasyon
- **K15.1.6.26 — Transcoding Pipeline Mimarisi**
  - Sorumluluk: «Transcoding Pipeline Mimarisi» — audio-transcoding.md dosyasında belgelenen bölüm.
  - Kanıt: `audio-transcoding.md` § Transcoding Pipeline Mimarisi

### K15.2 — Kayıpsız Codec (FLAC)

**Sorumluluk:** FLAC bitdepth/sample-rate desteği, sıkıştırma seviyeleri, ReplayGain ve metadata entegrasyonu.
**Kanıt dosyaları:** `flac-support.md` — havuz 14 başlık, kullanıldı 14.

#### K15.2.1 — Genel Bakış

- **Sorumluluk:** FLAC (Free Lossless Audio Codec), COREMUSIC'in birincil lossless ses formatıdır. Hi-Fi kalitesinde kayıpsız sıkıştırma sağlar ve %50-60 oranında dosya boyutu…
- **Kanıt:** `flac-support.md` § Genel Bakış (1 bölüm başlığı)

- **K15.2.1.1 — Genel Bakış**
  - Sorumluluk: FLAC (Free Lossless Audio Codec), COREMUSIC'in birincil lossless ses formatıdır. Hi-Fi kalitesinde kayıpsız sıkıştırma sağlar ve %50-60 oranında dosya boyutu küçültme ile orijinal sinyal bütünlüğünü…
  - Kanıt: `flac-support.md` § Genel Bakış

#### K15.2.2 — FLAC Format Detayı

- **Sorumluluk:** «FLAC Format Detayı» — flac-support.md dosyasında belgelenen bölüm.
- **Kanıt:** `flac-support.md` § FLAC Format Detayı (1 bölüm başlığı)

- **K15.2.2.2 — FLAC Format Detayı**
  - Sorumluluk: «FLAC Format Detayı» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § FLAC Format Detayı

#### K15.2.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — flac-support.md dosyasında belgelenen bölüm.
- **Kanıt:** `flac-support.md` § Teknik Detaylar (6 bölüm başlığı)

- **K15.2.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar
- **K15.2.3.4 — FLAC Bitdepth ve Sample Rate Desteği**
  - Sorumluluk: «FLAC Bitdepth ve Sample Rate Desteği» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar > FLAC Bitdepth ve Sample Rate Desteği
- **K15.2.3.5 — Sıkıştırma Seviyeleri**
  - Sorumluluk: «Sıkıştırma Seviyeleri» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar > Sıkıştırma Seviyeleri
- **K15.2.3.6 — Konseptual Kodlama**
  - Sorumluluk: «Konseptual Kodlama» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar > Konseptual Kodlama
- **K15.2.3.7 — ReplayGain Entegrasyonu**
  - Sorumluluk: «ReplayGain Entegrasyonu» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar > ReplayGain Entegrasyonu
- **K15.2.3.8 — Metadata Desteği**
  - Sorumluluk: «Metadata Desteği» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Teknik Detaylar > Metadata Desteği

#### K15.2.4 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — flac-support.md dosyasında belgelenen bölüm.
- **Kanıt:** `flac-support.md` § Kod / Konfigürasyon (4 bölüm başlığı)

- **K15.2.4.9 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Kod / Konfigürasyon
- **K15.2.4.10 — FLAC Encode Ayarları**
  - Sorumluluk: «FLAC Encode Ayarları» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Kod / Konfigürasyon > FLAC Encode Ayarları
- **K15.2.4.11 — FLAC Decode Ayarları**
  - Sorumluluk: «FLAC Decode Ayarları» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Kod / Konfigürasyon > FLAC Decode Ayarları
- **K15.2.4.12 — ReplayGain Konfigürasyonu**
  - Sorumluluk: «ReplayGain Konfigürasyonu» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Kod / Konfigürasyon > ReplayGain Konfigürasyonu

#### K15.2.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — flac-support.md dosyasında belgelenen bölüm.
- **Kanıt:** `flac-support.md` § Bağımlılıklar (1 bölüm başlığı)

- **K15.2.5.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — flac-support.md dosyasında belgelenen bölüm.
  - Kanıt: `flac-support.md` § Bağımlılıklar

#### K15.2.6 — Durum: Implementasyon

- **Sorumluluk:** FLAC desteği tam olarak implemente edilmiştir. Tüm encode/decode seviyeleri ve metadata yönetimi çalışır durumdadır.
- **Kanıt:** `flac-support.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K15.2.6.14 — Durum: Implementasyon**
  - Sorumluluk: FLAC desteği tam olarak implemente edilmiştir. Tüm encode/decode seviyeleri ve metadata yönetimi çalışır durumdadır.
  - Kanıt: `flac-support.md` § Durum: Implementasyon

### K15.3 — Kayıplı Codec'ler (MP3/AAC)

**Sorumluluk:** MPEG Layer III ve AAC/HE-AAC decode-encode hatları; LAME ve FDK-AAC entegrasyonu, SBR/PS.
**Kanıt dosyaları:** `mp3-decoder.md`, `aac-decoder.md` — havuz 26 başlık, kullanıldı 25, seçim dışı 1.

#### K15.3.1 — Genel Bakış

- **Sorumluluk:** MP3 decoder, MPEG-1 Audio Layer III formatının decode işlemlerini yönetir. LAME encoder entegrasyonu ile hem decode hem encode desteği sağlar. ID3v1/v2…
- **Kanıt:** `mp3-decoder.md`, `aac-decoder.md` § Genel Bakış (2 bölüm başlığı)

- **K15.3.1.1 — Genel Bakış**
  - Sorumluluk: MP3 decoder, MPEG-1 Audio Layer III formatının decode işlemlerini yönetir. LAME encoder entegrasyonu ile hem decode hem encode desteği sağlar. ID3v1/v2 metadata yönetimi, VBR/CBR bitrate desteği ile…
  - Kanıt: `mp3-decoder.md` § Genel Bakış
- **K15.3.1.2 — Genel Bakış**
  - Sorumluluk: AAC decoder, MPEG-2/MPEG-4 Advanced Audio Coding formatının decode işlemlerini FDK-AAC library üzerinden yönetir. HE-AAC (High-Efficiency) ve HE-AAC v2 desteği ile düşük bitrate'lerde yüksek kalite…
  - Kanıt: `aac-decoder.md` § Genel Bakış

#### K15.3.2 — MP3 Format Detayı

- **Sorumluluk:** «MP3 Format Detayı» — mp3-decoder.md dosyasında belgelenen bölüm.
- **Kanıt:** `mp3-decoder.md` § MP3 Format Detayı (1 bölüm başlığı)

- **K15.3.2.3 — MP3 Format Detayı**
  - Sorumluluk: «MP3 Format Detayı» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § MP3 Format Detayı

#### K15.3.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — mp3-decoder.md dosyasında belgelenen bölüm.
- **Kanıt:** `mp3-decoder.md`, `aac-decoder.md` § Teknik Detaylar (12 bölüm başlığı)

- **K15.3.3.4 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar
- **K15.3.3.5 — MPEG Versiyon Karşılaştırması**
  - Sorumluluk: «MPEG Versiyon Karşılaştırması» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar > MPEG Versiyon Karşılaştırması
- **K15.3.3.6 — Bitrate Tablosu (MPEG-1 Layer III)**
  - Sorumluluk: «Bitrate Tablosu (MPEG-1 Layer III)» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar > Bitrate Tablosu (MPEG-1 Layer III)
- **K15.3.3.7 — LAME Encoder Entegrasyonu**
  - Sorumluluk: «LAME Encoder Entegrasyonu» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar > LAME Encoder Entegrasyonu
- **K15.3.3.8 — ID3 Tag Yönetimi**
  - Sorumluluk: «ID3 Tag Yönetimi» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar > ID3 Tag Yönetimi
- **K15.3.3.9 — Decode Algoritması**
  - Sorumluluk: «Decode Algoritması» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Teknik Detaylar > Decode Algoritması
- **K15.3.3.10 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar
- **K15.3.3.11 — AAC Kodlama Pipeline**
  - Sorumluluk: «AAC Kodlama Pipeline» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar > AAC Kodlama Pipeline
- **K15.3.3.12 — SBR (Spectral Band Replication)**
  - Sorumluluk: «SBR (Spectral Band Replication)» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar > SBR (Spectral Band Replication)
- **K15.3.3.13 — Parametric Stereo (PS)**
  - Sorumluluk: «Parametric Stereo (PS)» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar > Parametric Stereo (PS)
- **K15.3.3.14 — Container Desteği**
  - Sorumluluk: «Container Desteği» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar > Container Desteği
- **K15.3.3.15 — FDK-AAC Library Entegrasyonu**
  - Sorumluluk: «FDK-AAC Library Entegrasyonu» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Teknik Detaylar > FDK-AAC Library Entegrasyonu

#### K15.3.4 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — mp3-decoder.md dosyasında belgelenen bölüm.
- **Kanıt:** `mp3-decoder.md`, `aac-decoder.md` § Kod / Konfigürasyon (6 bölüm başlığı)

- **K15.3.4.16 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Kod / Konfigürasyon
- **K15.3.4.17 — MP3 Decode Konfigürasyonu**
  - Sorumluluk: «MP3 Decode Konfigürasyonu» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Kod / Konfigürasyon > MP3 Decode Konfigürasyonu
- **K15.3.4.18 — LAME Encode Konfigürasyonu**
  - Sorumluluk: «LAME Encode Konfigürasyonu» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Kod / Konfigürasyon > LAME Encode Konfigürasyonu
- **K15.3.4.19 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Kod / Konfigürasyon
- **K15.3.4.20 — AAC Decode Konfigürasyonu**
  - Sorumluluk: «AAC Decode Konfigürasyonu» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Kod / Konfigürasyon > AAC Decode Konfigürasyonu
- **K15.3.4.21 — AAC Encode Konfigürasyonu**
  - Sorumluluk: «AAC Encode Konfigürasyonu» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Kod / Konfigürasyon > AAC Encode Konfigürasyonu

#### K15.3.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — mp3-decoder.md dosyasında belgelenen bölüm.
- **Kanıt:** `mp3-decoder.md`, `aac-decoder.md` § Bağımlılıklar (2 bölüm başlığı)

- **K15.3.5.22 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — mp3-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `mp3-decoder.md` § Bağımlılıklar
- **K15.3.5.23 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § Bağımlılıklar

#### K15.3.6 — Durum: Implementasyon / AAC Format Detayı

- **Sorumluluk:** MP3 decode/encode desteği tam olarak implemente edilmiştir. LAME encoder ve ID3 metadata yönetimi çalışır durumdadır.
- **Kanıt:** `mp3-decoder.md`, `aac-decoder.md` § Durum: Implementasyon / AAC Format Detayı (2 bölüm başlığı)

- **K15.3.6.24 — Durum: Implementasyon**
  - Sorumluluk: MP3 decode/encode desteği tam olarak implemente edilmiştir. LAME encoder ve ID3 metadata yönetimi çalışır durumdadır.
  - Kanıt: `mp3-decoder.md` § Durum: Implementasyon
- **K15.3.6.25 — AAC Format Detayı**
  - Sorumluluk: «AAC Format Detayı» — aac-decoder.md dosyasında belgelenen bölüm.
  - Kanıt: `aac-decoder.md` § AAC Format Detayı

### K15.4 — Codec Karşılaştırma & Politika

**Sorumluluk:** Bitrate-kalite, performans ve kullanım matrisleri; COREMUSIC codec politikası ve varsayılan seçimler.
**Kanıt dosyaları:** `codec-comparison.md` — havuz 14 başlık, kullanıldı 14.

#### K15.4.1 — Genel Bakış / Codec Genel Bakış

- **Sorumluluk:** Bu belge, COREMUSIC tarafından desteklenen tüm ses codec'lerinin kapsamlı karşılaştırmasını sunar. Her codec'in teknik özelliklerini, avantaj/dezavantajlarını…
- **Kanıt:** `codec-comparison.md` § Genel Bakış / Codec Genel Bakış (2 bölüm başlığı)

- **K15.4.1.1 — Genel Bakış**
  - Sorumluluk: Bu belge, COREMUSIC tarafından desteklenen tüm ses codec'lerinin kapsamlı karşılaştırmasını sunar. Her codec'in teknik özelliklerini, avantaj/dezavantajlarını ve kullanım alanlarını analiz ederek…
  - Kanıt: `codec-comparison.md` § Genel Bakış
- **K15.4.1.2 — Codec Genel Bakış**
  - Sorumluluk: «Codec Genel Bakış» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Codec Genel Bakış

#### K15.4.2 — Karşılaştırma Tablosu

- **Sorumluluk:** «Karşılaştırma Tablosu» — codec-comparison.md dosyasında belgelenen bölüm.
- **Kanıt:** `codec-comparison.md` § Karşılaştırma Tablosu (5 bölüm başlığı)

- **K15.4.2.3 — Karşılaştırma Tablosu**
  - Sorumluluk: «Karşılaştırma Tablosu» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Karşılaştırma Tablosu
- **K15.4.2.4 — Teknik Özellikler**
  - Sorumluluk: «Teknik Özellikler» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Karşılaştırma Tablosu > Teknik Özellikler
- **K15.4.2.5 — Bitrate vs Kalite**
  - Sorumluluk: «Bitrate vs Kalite» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Karşılaştırma Tablosu > Bitrate vs Kalite
- **K15.4.2.6 — Performans Karşılaştırması**
  - Sorumluluk: «Performans Karşılaştırması» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Karşılaştırma Tablosu > Performans Karşılaştırması
- **K15.4.2.7 — Kullanım Alanları**
  - Sorumluluk: «Kullanım Alanları» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Karşılaştırma Tablosu > Kullanım Alanları

#### K15.4.3 — Teknik Analiz

- **Sorumluluk:** «Teknik Analiz» — codec-comparison.md dosyasında belgelenen bölüm.
- **Kanıt:** `codec-comparison.md` § Teknik Analiz (3 bölüm başlığı)

- **K15.4.3.8 — Teknik Analiz**
  - Sorumluluk: «Teknik Analiz» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Teknik Analiz
- **K15.4.3.9 — Frekans Yanıtı Analizi**
  - Sorumluluk: «Frekans Yanıtı Analizi» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Teknik Analiz > Frekans Yanıtı Analizi
- **K15.4.3.10 — Joint Stereo Desteği**
  - Sorumluluk: «Joint Stereo Desteği» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Teknik Analiz > Joint Stereo Desteği

#### K15.4.4 — COREMUSIC Codec Politikası

- **Sorumluluk:** «COREMUSIC Codec Politikası» — codec-comparison.md dosyasında belgelenen bölüm.
- **Kanıt:** `codec-comparison.md` § COREMUSIC Codec Politikası (2 bölüm başlığı)

- **K15.4.4.11 — COREMUSIC Codec Politikası**
  - Sorumluluk: «COREMUSIC Codec Politikası» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § COREMUSIC Codec Politikası
- **K15.4.4.12 — Varsayılan Codec Seçimleri**
  - Sorumluluk: «Varsayılan Codec Seçimleri» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § COREMUSIC Codec Politikası > Varsayılan Codec Seçimleri

#### K15.4.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — codec-comparison.md dosyasında belgelenen bölüm.
- **Kanıt:** `codec-comparison.md` § Bağımlılıklar (1 bölüm başlığı)

- **K15.4.5.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — codec-comparison.md dosyasında belgelenen bölüm.
  - Kanıt: `codec-comparison.md` § Bağımlılıklar

#### K15.4.6 — Durum: Uygulama

- **Sorumluluk:** Codec karşılaştırma ve seçimi tam olarak implemente edilmiştir. Tüm codec'ler encode/decode desteği ile entegre edilmiştir.
- **Kanıt:** `codec-comparison.md` § Durum: Uygulama (1 bölüm başlığı)

- **K15.4.6.14 — Durum: Uygulama**
  - Sorumluluk: Codec karşılaştırma ve seçimi tam olarak implemente edilmiştir. Tüm codec'ler encode/decode desteği ile entegre edilmiştir.
  - Kanıt: `codec-comparison.md` § Durum: Uygulama

### K15.5 — HLS Streaming

**Sorumluluk:** Master/media playlist formatı, segment desteği, adaptive bitrate algoritması, şifreleme/DRM.
**Kanıt dosyaları:** `hls-streaming.md` — havuz 14 başlık, kullanıldı 13, seçim dışı 1.

#### K15.5.1 — Genel Bakış

- **Sorumluluk:** HTTP Live Streaming (HLS), Apple tarafından geliştirilen adaptive bitrate streaming protokolüdür. Medya dosyalarını küçük segmentlere bölerek HTTP üzerinden…
- **Kanıt:** `hls-streaming.md` § Genel Bakış (1 bölüm başlığı)

- **K15.5.1.1 — Genel Bakış**
  - Sorumluluk: HTTP Live Streaming (HLS), Apple tarafından geliştirilen adaptive bitrate streaming protokolüdür. Medya dosyalarını küçük segmentlere bölerek HTTP üzerinden güvenilir bir streaming sağlar. COREMUSIC,…
  - Kanıt: `hls-streaming.md` § Genel Bakış

#### K15.5.2 — HLS Mimarisi

- **Sorumluluk:** «HLS Mimarisi» — hls-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `hls-streaming.md` § HLS Mimarisi (1 bölüm başlığı)

- **K15.5.2.2 — HLS Mimarisi**
  - Sorumluluk: «HLS Mimarisi» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § HLS Mimarisi

#### K15.5.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — hls-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `hls-streaming.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K15.5.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar
- **K15.5.3.4 — Master Playlist Formato**
  - Sorumluluk: «Master Playlist Formato» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > Master Playlist Formato
- **K15.5.3.5 — Media Playlist Formato**
  - Sorumluluk: «Media Playlist Formato» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > Media Playlist Formato
- **K15.5.3.6 — Segment Desteği**
  - Sorumluluk: «Segment Desteği» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > Segment Desteği

#### K15.5.4 — Teknik Detaylar (II)

- **Sorumluluk:** «Adaptive Bitrate Algoritması» — hls-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `hls-streaming.md` § Teknik Detaylar (II) (3 bölüm başlığı)

- **K15.5.4.7 — Adaptive Bitrate Algoritması**
  - Sorumluluk: «Adaptive Bitrate Algoritması» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > Adaptive Bitrate Algoritması
- **K15.5.4.8 — Encryption ve DRM**
  - Sorumluluk: «Encryption ve DRM» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > Encryption ve DRM
- **K15.5.4.9 — HLS Version Özellikleri**
  - Sorumluluk: «HLS Version Özellikleri» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Teknik Detaylar > HLS Version Özellikleri

#### K15.5.5 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — hls-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `hls-streaming.md` § Kod / Konfigürasyon (3 bölüm başlığı)

- **K15.5.5.10 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Kod / Konfigürasyon
- **K15.5.5.11 — HLS Segmenter Konfigürasyonu**
  - Sorumluluk: «HLS Segmenter Konfigürasyonu» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Kod / Konfigürasyon > HLS Segmenter Konfigürasyonu
- **K15.5.5.12 — Master Playlist Konfigürasyonu**
  - Sorumluluk: «Master Playlist Konfigürasyonu» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Kod / Konfigürasyon > Master Playlist Konfigürasyonu

#### K15.5.6 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — hls-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `hls-streaming.md` § Bağımlılıklar (1 bölüm başlığı)

- **K15.5.6.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — hls-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `hls-streaming.md` § Bağımlılıklar

### K15.6 — DASH Streaming

**Sorumluluk:** MPD yapısı, adaptation set seçimi, low-latency DASH, DASH.js ve DRM entegrasyonu.
**Kanıt dosyaları:** `dash-streaming.md` — havuz 14 başlık, kullanıldı 13, seçim dışı 1.

#### K15.6.1 — Genel Bakış

- **Sorumluluk:** MPEG-DASH (Dynamic Adaptive Streaming over HTTP), MPEG tarafından geliştirilen açık standart adaptive bitrate streaming protokolüdür. XML tabanlı manifest…
- **Kanıt:** `dash-streaming.md` § Genel Bakış (1 bölüm başlığı)

- **K15.6.1.1 — Genel Bakış**
  - Sorumluluk: MPEG-DASH (Dynamic Adaptive Streaming over HTTP), MPEG tarafından geliştirilen açık standart adaptive bitrate streaming protokolüdür. XML tabanlı manifest yapısı ile platformlar arası uyumluluk…
  - Kanıt: `dash-streaming.md` § Genel Bakış

#### K15.6.2 — DASH Mimarisi

- **Sorumluluk:** «DASH Mimarisi» — dash-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `dash-streaming.md` § DASH Mimarisi (1 bölüm başlığı)

- **K15.6.2.2 — DASH Mimarisi**
  - Sorumluluk: «DASH Mimarisi» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § DASH Mimarisi

#### K15.6.3 — Teknik Detaylar (I)

- **Sorumluluk:** «Teknik Detaylar» — dash-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `dash-streaming.md` § Teknik Detaylar (I) (4 bölüm başlığı)

- **K15.6.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar
- **K15.6.3.4 — MPD (Media Presentation Description)**
  - Sorumluluk: «MPD (Media Presentation Description)» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > MPD (Media Presentation Description)
- **K15.6.3.5 — Segment Tipleri**
  - Sorumluluk: «Segment Tipleri» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > Segment Tipleri
- **K15.6.3.6 — Adaptation Set Seçimi**
  - Sorumluluk: «Adaptation Set Seçimi» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > Adaptation Set Seçimi

#### K15.6.4 — Teknik Detaylar (II)

- **Sorumluluk:** «DASH.js Player Entegrasyonu» — dash-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `dash-streaming.md` § Teknik Detaylar (II) (3 bölüm başlığı)

- **K15.6.4.7 — DASH.js Player Entegrasyonu**
  - Sorumluluk: «DASH.js Player Entegrasyonu» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > DASH.js Player Entegrasyonu
- **K15.6.4.8 — Low-Latency DASH (LL-DASH)**
  - Sorumluluk: «Low-Latency DASH (LL-DASH)» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > Low-Latency DASH (LL-DASH)
- **K15.6.4.9 — DRM Entegrasyonu**
  - Sorumluluk: «DRM Entegrasyonu» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Teknik Detaylar > DRM Entegrasyonu

#### K15.6.5 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — dash-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `dash-streaming.md` § Kod / Konfigürasyon (3 bölüm başlığı)

- **K15.6.5.10 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Kod / Konfigürasyon
- **K15.6.5.11 — DASH Packager Konfigürasyonu**
  - Sorumluluk: «DASH Packager Konfigürasyonu» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Kod / Konfigürasyon > DASH Packager Konfigürasyonu
- **K15.6.5.12 — DASH.js Player Konfigürasyonu**
  - Sorumluluk: «DASH.js Player Konfigürasyonu» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Kod / Konfigürasyon > DASH.js Player Konfigürasyonu

#### K15.6.6 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — dash-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `dash-streaming.md` § Bağımlılıklar (1 bölüm başlığı)

- **K15.6.6.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — dash-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `dash-streaming.md` § Bağımlılıklar

### K15.7 — Protokol Politikası (HLS vs DASH)

**Sorumluluk:** Protokol karşılaştırması: segment/bandwidth/gecikme farkları ve katman bazlı seçim politikası.
**Kanıt dosyaları:** `streaming-protocol.md` — havuz 13 başlık, kullanıldı 13.

#### K15.7.1 — Genel Bakış / Protokol Genel Bakış

- **Sorumluluk:** Bu belge, desteklenen tüm streaming protokollerinin (HLS, DASH, Shoutcast, Icecast, RTSP) teknik karşılaştırmasını sunar. Her protokolün özelliklerini,…
- **Kanıt:** `streaming-protocol.md` § Genel Bakış / Protokol Genel Bakış (2 bölüm başlığı)

- **K15.7.1.1 — Genel Bakış**
  - Sorumluluk: Bu belge, desteklenen tüm streaming protokollerinin (HLS, DASH, Shoutcast, Icecast, RTSP) teknik karşılaştırmasını sunar. Her protokolün özelliklerini, avantajlarını ve kullanım alanlarını analiz…
  - Kanıt: `streaming-protocol.md` § Genel Bakış
- **K15.7.1.2 — Protokol Genel Bakış**
  - Sorumluluk: «Protokol Genel Bakış» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Protokol Genel Bakış

#### K15.7.2 — Karşılaştırma Tablosu

- **Sorumluluk:** «Karşılaştırma Tablosu» — streaming-protocol.md dosyasında belgelenen bölüm.
- **Kanıt:** `streaming-protocol.md` § Karşılaştırma Tablosu (4 bölüm başlığı)

- **K15.7.2.3 — Karşılaştırma Tablosu**
  - Sorumluluk: «Karşılaştırma Tablosu» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Karşılaştırma Tablosu
- **K15.7.2.4 — Temel Özellikler**
  - Sorumluluk: «Temel Özellikler» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Karşılaştırma Tablosu > Temel Özellikler
- **K15.7.2.5 — Adaptive Bitrate Karşılaştırması**
  - Sorumluluk: «Adaptive Bitrate Karşılaştırması» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Karşılaştırma Tablosu > Adaptive Bitrate Karşılaştırması
- **K15.7.2.6 — Segment Süre Karşılaştırması**
  - Sorumluluk: «Segment Süre Karşılaştırması» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Karşılaştırma Tablosu > Segment Süre Karşılaştırması

#### K15.7.3 — Teknik Analiz

- **Sorumluluk:** «Teknik Analiz» — streaming-protocol.md dosyasında belgelenen bölüm.
- **Kanıt:** `streaming-protocol.md` § Teknik Analiz (3 bölüm başlığı)

- **K15.7.3.7 — Teknik Analiz**
  - Sorumluluk: «Teknik Analiz» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Teknik Analiz
- **K15.7.3.8 — HLS vs DASH**
  - Sorumluluk: «HLS vs DASH» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Teknik Analiz > HLS vs DASH
- **K15.7.3.9 — Low-Latency Karşılaştırması**
  - Sorumluluk: «Low-Latency Karşılaştırması» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Teknik Analiz > Low-Latency Karşılaştırması

#### K15.7.4 — COREMUSIC Protokol Politikası

- **Sorumluluk:** «COREMUSIC Protokol Politikası» — streaming-protocol.md dosyasında belgelenen bölüm.
- **Kanıt:** `streaming-protocol.md` § COREMUSIC Protokol Politikası (2 bölüm başlığı)

- **K15.7.4.10 — COREMUSIC Protokol Politikası**
  - Sorumluluk: «COREMUSIC Protokol Politikası» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § COREMUSIC Protokol Politikası
- **K15.7.4.11 — Kullanım Alanına Göre Seçim**
  - Sorumluluk: «Kullanım Alanına Göre Seçim» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § COREMUSIC Protokol Politikası > Kullanım Alanına Göre Seçim

#### K15.7.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — streaming-protocol.md dosyasında belgelenen bölüm.
- **Kanıt:** `streaming-protocol.md` § Bağımlılıklar (1 bölüm başlığı)

- **K15.7.5.12 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — streaming-protocol.md dosyasında belgelenen bölüm.
  - Kanıt: `streaming-protocol.md` § Bağımlılıklar

#### K15.7.6 — Durum: Uygulama

- **Sorumluluk:** Streaming protocol karşılaştırması ve seçimi tam olarak implemente edilmiştir. Tüm protokoller desteklenmektedir.
- **Kanıt:** `streaming-protocol.md` § Durum: Uygulama (1 bölüm başlığı)

- **K15.7.6.13 — Durum: Uygulama**
  - Sorumluluk: Streaming protocol karşılaştırması ve seçimi tam olarak implemente edilmiştir. Tüm protokoller desteklenmektedir.
  - Kanıt: `streaming-protocol.md` § Durum: Uygulama

### K15.8 — Radyo & Podcast Akışı

**Sorumluluk:** SHOUTcast v2/Icecast 2.x radyo aktarımı; podcast RSS parse, episode metadata, progressive download.
**Kanıt dosyaları:** `radio-streaming.md`, `podcast-support.md` — havuz 26 başlık, kullanıldı 25, seçim dışı 1.

#### K15.8.1 — Genel Bakış

- **Sorumluluk:** Radio streaming desteği, internet radyo istasyonlarını Shoutcast/Icecast protokolleri üzerinden bağlar ve çalar. COREMUSIC, SHOUTcast v2 ve Icecast 2.x…
- **Kanıt:** `radio-streaming.md`, `podcast-support.md` § Genel Bakış (2 bölüm başlığı)

- **K15.8.1.1 — Genel Bakış**
  - Sorumluluk: Radio streaming desteği, internet radyo istasyonlarını Shoutcast/Icecast protokolleri üzerinden bağlar ve çalar. COREMUSIC, SHOUTcast v2 ve Icecast 2.x protokolleri ile uyumlu bir radio client…
  - Kanıt: `radio-streaming.md` § Genel Bakış
- **K15.8.1.2 — Genel Bakış**
  - Sorumluluk: Podcast desteği, RSS tabanlı podcast beslemelerini yönetir, bölüm depolamayı ve oynatmayı sağlar. COREMUSIC, podcast RSS parsing, episode metadata extraction ve progressive download streaming sunar.…
  - Kanıt: `podcast-support.md` § Genel Bakış

#### K15.8.2 — Radio Streaming Mimarisi

- **Sorumluluk:** «Radio Streaming Mimarisi» — radio-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `radio-streaming.md` § Radio Streaming Mimarisi (1 bölüm başlığı)

- **K15.8.2.3 — Radio Streaming Mimarisi**
  - Sorumluluk: «Radio Streaming Mimarisi» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Radio Streaming Mimarisi

#### K15.8.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — radio-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `radio-streaming.md`, `podcast-support.md` § Teknik Detaylar (12 bölüm başlığı)

- **K15.8.3.4 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar
- **K15.8.3.5 — SHOUTcast v2 Protocol**
  - Sorumluluk: «SHOUTcast v2 Protocol» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar > SHOUTcast v2 Protocol
- **K15.8.3.6 — Metadata Parsing**
  - Sorumluluk: «Metadata Parsing» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar > Metadata Parsing
- **K15.8.3.7 — Icecast 2.x Protocol**
  - Sorumluluk: «Icecast 2.x Protocol» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar > Icecast 2.x Protocol
- **K15.8.3.8 — Station Discovery**
  - Sorumluluk: «Station Discovery» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar > Station Discovery
- **K15.8.3.9 — Stream Error Handling**
  - Sorumluluk: «Stream Error Handling» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Teknik Detaylar > Stream Error Handling
- **K15.8.3.10 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar
- **K15.8.3.11 — RSS Parsing Pipeline**
  - Sorumluluk: «RSS Parsing Pipeline» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar > RSS Parsing Pipeline
- **K15.8.3.12 — Episode Metadata**
  - Sorumluluk: «Episode Metadata» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar > Episode Metadata
- **K15.8.3.13 — Podcast Feed Yönetimi**
  - Sorumluluk: «Podcast Feed Yönetimi» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar > Podcast Feed Yönetimi
- **K15.8.3.14 — Progressive Download**
  - Sorumluluk: «Progressive Download» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar > Progressive Download
- **K15.8.3.15 — Offline Support**
  - Sorumluluk: «Offline Support» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Teknik Detaylar > Offline Support

#### K15.8.4 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — radio-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `radio-streaming.md`, `podcast-support.md` § Kod / Konfigürasyon (6 bölüm başlığı)

- **K15.8.4.16 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Kod / Konfigürasyon
- **K15.8.4.17 — Radio Client Konfigürasyonu**
  - Sorumluluk: «Radio Client Konfigürasyonu» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Kod / Konfigürasyon > Radio Client Konfigürasyonu
- **K15.8.4.18 — Radio Database Schema**
  - Sorumluluk: «Radio Database Schema» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Kod / Konfigürasyon > Radio Database Schema
- **K15.8.4.19 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Kod / Konfigürasyon
- **K15.8.4.20 — Podcast Manager Konfigürasyonu**
  - Sorumluluk: «Podcast Manager Konfigürasyonu» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Kod / Konfigürasyon > Podcast Manager Konfigürasyonu
- **K15.8.4.21 — RSS Parser Konfigürasyonu**
  - Sorumluluk: «RSS Parser Konfigürasyonu» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Kod / Konfigürasyon > RSS Parser Konfigürasyonu

#### K15.8.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — radio-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `radio-streaming.md`, `podcast-support.md` § Bağımlılıklar (2 bölüm başlığı)

- **K15.8.5.22 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — radio-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `radio-streaming.md` § Bağımlılıklar
- **K15.8.5.23 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Bağımlılıklar

#### K15.8.6 — Durum: Implementasyon / Podcast RSS Formatı

- **Sorumluluk:** Radio streaming desteği aktif geliştirme aşamasındadır. SHOUTcast/Icecast temel desteği tamamlanmıştır.
- **Kanıt:** `radio-streaming.md`, `podcast-support.md` § Durum: Implementasyon / Podcast RSS Formatı (2 bölüm başlığı)

- **K15.8.6.24 — Durum: Implementasyon**
  - Sorumluluk: Radio streaming desteği aktif geliştirme aşamasındadır. SHOUTcast/Icecast temel desteği tamamlanmıştır.
  - Kanıt: `radio-streaming.md` § Durum: Implementasyon
- **K15.8.6.25 — Podcast RSS Formatı**
  - Sorumluluk: «Podcast RSS Formatı» — podcast-support.md dosyasında belgelenen bölüm.
  - Kanıt: `podcast-support.md` § Podcast RSS Formatı

### K15.9 — Dağıtım & Metadata

**Sorumluluk:** CDN/edge caching, origin shield, token auth ile içerik dağıtım; ID3v2/Vorbis metadata okuma-yazma hattı.
**Kanıt dosyaları:** `content-delivery.md`, `media-metadata.md` — havuz 28 başlık, kullanıldı 27, seçim dışı 1.

#### K15.9.1 — Genel Bakış

- **Sorumluluk:** Content Delivery Network entegrasyonu, medya içeriklerini coğrafi olarak dağıtılmış edge sunucuları üzerinden sunar. Origin shield, token authentication ve…
- **Kanıt:** `content-delivery.md`, `media-metadata.md` § Genel Bakış (2 bölüm başlığı)

- **K15.9.1.1 — Genel Bakış**
  - Sorumluluk: Content Delivery Network entegrasyonu, medya içeriklerini coğrafi olarak dağıtılmış edge sunucuları üzerinden sunar. Origin shield, token authentication ve edge caching stratejileri ile düşük gecikme…
  - Kanıt: `content-delivery.md` § Genel Bakış
- **K15.9.1.2 — Genel Bakış**
  - Sorumluluk: Media metadata engine, ses dosyalarından tüm metadata formatlarını (ID3, Vorbis Comments, APE, iTunes) okur, yazar ve yönetir. Berkeley DB tabanlı metadata cache ile hızlı erişim sağlar.…
  - Kanıt: `media-metadata.md` § Genel Bakış

#### K15.9.2 — CDN Mimarisi

- **Sorumluluk:** «CDN Mimarisi» — content-delivery.md dosyasında belgelenen bölüm.
- **Kanıt:** `content-delivery.md` § CDN Mimarisi (1 bölüm başlığı)

- **K15.9.2.3 — CDN Mimarisi**
  - Sorumluluk: «CDN Mimarisi» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § CDN Mimarisi

#### K15.9.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — content-delivery.md dosyasında belgelenen bölüm.
- **Kanıt:** `content-delivery.md`, `media-metadata.md` § Teknik Detaylar (14 bölüm başlığı)

- **K15.9.3.4 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar
- **K15.9.3.5 — Origin Shield**
  - Sorumluluk: «Origin Shield» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Origin Shield
- **K15.9.3.6 — Token Authentication**
  - Sorumluluk: «Token Authentication» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Token Authentication
- **K15.9.3.7 — Edge Caching Stratejisi**
  - Sorumluluk: «Edge Caching Stratejisi» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Edge Caching Stratejisi
- **K15.9.3.8 — Cache Headers**
  - Sorumluluk: «Cache Headers» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Cache Headers
- **K15.9.3.9 — CDN Configuration Example**
  - Sorumluluk: «CDN Configuration Example» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > CDN Configuration Example
- **K15.9.3.10 — Multi-CDN Stratejisi**
  - Sorumluluk: «Multi-CDN Stratejisi» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Multi-CDN Stratejisi
- **K15.9.3.11 — Rate Limiting**
  - Sorumluluk: «Rate Limiting» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Teknik Detaylar > Rate Limiting
- **K15.9.3.12 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar
- **K15.9.3.13 — ID3v2 Frame Haritası**
  - Sorumluluk: «ID3v2 Frame Haritası» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar > ID3v2 Frame Haritası
- **K15.9.3.14 — Vorbis Comments**
  - Sorumluluk: «Vorbis Comments» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar > Vorbis Comments
- **K15.9.3.15 — Metadata Extraction Pipeline**
  - Sorumluluk: «Metadata Extraction Pipeline» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar > Metadata Extraction Pipeline
- **K15.9.3.16 — Metadata Write Pipeline**
  - Sorumluluk: «Metadata Write Pipeline» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar > Metadata Write Pipeline
- **K15.9.3.17 — Artwork Management**
  - Sorumluluk: «Artwork Management» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Teknik Detaylar > Artwork Management

#### K15.9.4 — Kod / Konfigürasyon

- **Sorumluluk:** «Kod / Konfigürasyon» — content-delivery.md dosyasında belgelenen bölüm.
- **Kanıt:** `content-delivery.md`, `media-metadata.md` § Kod / Konfigürasyon (6 bölüm başlığı)

- **K15.9.4.18 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Kod / Konfigürasyon
- **K15.9.4.19 — CDN Manager Konfigürasyonu**
  - Sorumluluk: «CDN Manager Konfigürasyonu» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Kod / Konfigürasyon > CDN Manager Konfigürasyonu
- **K15.9.4.20 — Nginx Edge Cache Konfigürasyonu**
  - Sorumluluk: «Nginx Edge Cache Konfigürasyonu» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Kod / Konfigürasyon > Nginx Edge Cache Konfigürasyonu
- **K15.9.4.21 — Kod / Konfigürasyon**
  - Sorumluluk: «Kod / Konfigürasyon» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Kod / Konfigürasyon
- **K15.9.4.22 — Metadata Engine Konfigürasyonu**
  - Sorumluluk: «Metadata Engine Konfigürasyonu» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Kod / Konfigürasyon > Metadata Engine Konfigürasyonu
- **K15.9.4.23 — ID3v2 Writer Konfigürasyonu**
  - Sorumluluk: «ID3v2 Writer Konfigürasyonu» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Kod / Konfigürasyon > ID3v2 Writer Konfigürasyonu

#### K15.9.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — content-delivery.md dosyasında belgelenen bölüm.
- **Kanıt:** `content-delivery.md`, `media-metadata.md` § Bağımlılıklar (2 bölüm başlığı)

- **K15.9.5.24 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — content-delivery.md dosyasında belgelenen bölüm.
  - Kanıt: `content-delivery.md` § Bağımlılıklar
- **K15.9.5.25 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Bağımlılıklar

#### K15.9.6 — Durum: Uygulama / Metadata Format Mimarisi

- **Sorumluluk:** CDN entegrasyonu aktif geliştirme aşamasındadır. Temel edge caching ve token authentication tamamlanmıştır.
- **Kanıt:** `content-delivery.md`, `media-metadata.md` § Durum: Uygulama / Metadata Format Mimarisi (2 bölüm başlığı)

- **K15.9.6.26 — Durum: Uygulama**
  - Sorumluluk: CDN entegrasyonu aktif geliştirme aşamasındadır. Temel edge caching ve token authentication tamamlanmıştır.
  - Kanıt: `content-delivery.md` § Durum: Uygulama
- **K15.9.6.27 — Metadata Format Mimarisi**
  - Sorumluluk: «Metadata Format Mimarisi» — media-metadata.md dosyasında belgelenen bölüm.
  - Kanıt: `media-metadata.md` § Metadata Format Mimarisi

---

## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k15-medya-streaming/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayımın hangi kısmını desteklediğini listeler. 13 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `ffmpeg-pipeline.md` | FFmpeg Processing Pipeline | `K15.1` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 2 | `audio-transcoding.md` | Audio Transcoding Engine | `K15.1` alanı (1), 5 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 3 | `flac-support.md` | FLAC Lossless Audio Support | `K15.2` alanı (1), 6 alt alan, 14 yaprak → `K15` toplam 224 içine katkı |
| 4 | `mp3-decoder.md` | MP3 Decoder Engine | `K15.3` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 5 | `aac-decoder.md` | AAC Decoder Engine | `K15.3` alanı (1), 5 alt alan, 12 yaprak → `K15` toplam 224 içine katkı |
| 6 | `codec-comparison.md` | Codec Karşılaştırma Matrisi | `K15.4` alanı (1), 6 alt alan, 14 yaprak → `K15` toplam 224 içine katkı |
| 7 | `hls-streaming.md` | HLS Streaming Protocol | `K15.5` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 8 | `dash-streaming.md` | MPEG-DASH Streaming | `K15.6` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 9 | `streaming-protocol.md` | Streaming Protocol Karşılaştırması | `K15.7` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 10 | `radio-streaming.md` | Radio Streaming Support | `K15.8` alanı (1), 6 alt alan, 13 yaprak → `K15` toplam 224 içine katkı |
| 11 | `podcast-support.md` | Podcast Support | `K15.8` alanı (1), 5 alt alan, 12 yaprak → `K15` toplam 224 içine katkı |
| 12 | `content-delivery.md` | Content Delivery Network | `K15.9` alanı (1), 6 alt alan, 15 yaprak → `K15` toplam 224 içine katkı |
| 13 | `media-metadata.md` | Media Metadata Engine | `K15.9` alanı (1), 5 alt alan, 12 yaprak → `K15` toplam 224 içine katkı |
| 14 | `index.md` | Katman ana sayfası: diyagram, tablolar, bağımlılıklar | `K15.a) alan tanımı bağlamı |
| 15 | `README.md` | Katman künyesi, özet tablolar | `K15.a`/`K15.a.b` doğrulaması |
| 16 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 13 dosyadan çıkarılan 176 kanıt havuzu içinden hedef c=170 için 6 başlık orantılı olarak seçimin dışında bırakıldı; alan/alt alan sayıları a=9, b=6 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Durum: Uygulama | `audio-transcoding.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `aac-decoder.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `hls-streaming.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `dash-streaming.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Implementasyon | `podcast-support.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |
| Durum: Uygulama | `media-metadata.md` | hedef c=170 aşıldı; havuz 176 > hedef — sayım fazlalığı, içerik dosyada korunmuştur |

*K15 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
