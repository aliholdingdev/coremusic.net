---
title: "Media Metadata Engine"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Media Metadata Engine

## Genel Bakış

Media metadata engine, ses dosyalarından tüm metadata formatlarını (ID3, Vorbis Comments, APE, iTunes) okur, yazar ve yönetir. Berkeley DB tabanlı metadata cache ile hızlı erişim sağlar. ElectronicBrain Database tabanlı metadata enrichment desteği mevcuttur.

## Metadata Format Mimarisi

```
┌─────────────────────────────────────────────────────┐
│              Metadata Format Hierarchy              │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Container Based:                                   │
│  ├── MP3 → ID3v1, ID3v2 (ID3v2.4)                 │
│  ├── FLAC → Vorbis Comments, Pictures              │
│  ├── OGG → Vorbis Comments, Pictures               │
│  ├── MP4/M4A → iTunes Metadata, Free-Form         │
│  ├── WMA → ASF Metadata                            │
│  ├── APE → APE Tags                                │
│  └── WAV → ID3v2 (RIFF chunk)                      │
│                                                     │
│  Application Based:                                │
│  ├── ReplayGain Tags                               │
│  ├── R128 Loudness Tags (EBU R 128)               │
│  ├── MusicBrainz Tags                              │
│  ├── AcoustID Fingerprint                          │
│  └── Discogs Metadata                              │
└─────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### ID3v2 Frame Haritası

```
ID3v2.4 Frame Types:
┌──────────────────┬────────────────────────────────────┐
│ Frame            │ Açıklama                           │
├──────────────────┼────────────────────────────────────┤
│ T***             │ Text frames (0-19)                 │
│ TIT2             │ Title                              │
│ TPE1             │ Lead artist/soloist                │
│ TPE2             │ Band/orchestra/accompaniment       │
│ TALB             │ Album                              │
│ TDRC             │ Recording date (year)              │
│ TRCK             │ Track number                       │
│ TPOS             │ Part of a set (disc number)        │
│ TCON             │ Content type (genre)               │
│ TCOM             │ Composer                           │
│ TPUB             │ Publisher                          │
│ TCOP             │ Copyright                          │
│ TBPM             │ BPM                                │
│ TKEY             │ Musical key                        │
│ TSRC             │ ISRC                               │
├──────────────────┼────────────────────────────────────┤
│ APIC             │ Attached picture                   │
│ USLT             │ Unsynchronized lyrics              │
│ SYLT             │ Synchronized lyrics                │
│ COMM             │ Comments                           │
│ PRIV             │ Private frame                      │
│ UFID             │ Unique file identifier             │
├──────────────────┼────────────────────────────────────┤
│ W***             │ URL frames (20-27)                 │
│ WCOM             │ Commercial URL                     │
│ WOAR             │ Official audio file URL            │
│ WPUB             │ Publisher URL                      │
└──────────────────┴────────────────────────────────────┘
```

### Vorbis Comments

```
Vorbis Comment Format (FLAC/OGG):
┌──────────────────────────────────────────────────────┐
│  Header: "vorbis"                                    │
│  Vendor String: "COREMUSIC FLAC Encoder 1.0"        │
│  Comment Count: N                                    │
│  Comments:                                           │
│    TITLE=Song Title                                  │
│    ARTIST=Artist Name                                │
│    ALBUM=Album Name                                  │
│    DATE=2026                                         │
│    TRACKNUMBER=3                                     │
│    GENRE=Rock                                        │
│    ALBUMARTIST=Main Artist                           │
│    DISCNUMBER=1                                      │
│    TOTALDISCS=2                                      │
│    ISRC=USRUL1234567                                 │
│    BPM=120                                           │
│    COMMENT=Studio recording                          │
│    REPLAYGAIN_TRACK_GAIN=-6.54 dB                    │
│    REPLAYGAIN_TRACK_PEAK=0.98234567                  │
│    REPLAYGAIN_ALBUM_GAIN=-3.21 dB                    │
│    REPLAYGAIN_ALBUM_PEAK=0.99876543                  │
│    R128_TRACK_GAIN=-1234                             │
│    R128_ALBUM_GAIN=-892                              │
│    MUSICBRAINZ_TRACKID=abc123-def456                 │
│    ACOUSTID_ID=fingerprint123                        │
└──────────────────────────────────────────────────────┘
```

### Metadata Extraction Pipeline

```
Metadata Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ File     │───▶│ Container│───▶│ Tag      │───▶│ Metadata │
│ Reader   │    │ Parser   │    │ Extractor│    │ Store    │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  File Header   Container Type  Tag Frames     MySQL + Cache
  Magic Bytes   ID3/Vorbis/MP4  Artwork        ElasticSearch
```

### Metadata Write Pipeline

```
Metadata Write Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ Metadata │───▶│ Frame    │───▶│ Container│───▶│ File     │
│ Input    │    │ Builder  │    │ Writer   │    │ Updater  │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  User Input    ID3v2/Vorbis   MP3/FLAC/MP4   Atomic Write
  API Input     Frame Create   ID3 Update     Backup Original
  Import        Picture Frame  Vorbis Write   Checksum Verify
```

### Artwork Management

```
Artwork Specifications:
┌──────────────────┬────────────────────────────────┐
│ Container        │ Artwork Support                 │
├──────────────────┼────────────────────────────────┤
│ FLAC             │ Multiple pictures (unlimited)  │
│                  │ MIME: JPEG, PNG, GIF           │
│                  │ Size: No limit                  │
│                  │ Location: Metadata block       │
├──────────────────┼────────────────────────────────┤
│ MP3 (ID3v2)      │ Multiple APIC frames           │
│                  │ MIME: JPEG, PNG                │
│                  │ Type: Cover, Back, Artist...   │
│                  │ Size: No limit                  │
├──────────────────┼────────────────────────────────┤
│ MP4/M4A          │ Single cover art               │
│                  │ Box: 'covr'                     │
│                  │ MIME: JPEG, PNG                │
│                  │ Size: No limit                  │
├──────────────────┼────────────────────────────────┤
│ OGG Vorbis       │ Single picture (METADATA_BLOCK)│
│                  │ MIME: JPEG, PNG                │
│                  │ Size: No limit                  │
└──────────────────┴────────────────────────────────┘
```

## Kod / Konfigürasyon

### Metadata Engine Konfigürasyonu

```yaml
metadata_engine:
  reader:
    cache_enabled: true
    cache_ttl: 86400             # 24 saat
    max_file_size: 524288000     # 500MB
    timeout_ms: 5000
    
  writer:
    backup_original: true
    atomic_write: true
    verify_after_write: true
    
  artwork:
    max_width: 1024
    max_height: 1024
    format: "jpeg"
    quality: 90
    embed_default: true
    
  enrichment:
    musicbrainz: true
    acoustid: true
    discogs: false
    
  search:
    engine: "elastic"            # elastic, mysql, sqlite
    index_fields:
      - "title"
      - "artist"
      - "album"
      - "genre"
      - "comment"
```

### ID3v2 Writer Konfigürasyonu

```yaml
id3v2_writer:
  version: "2.4"
  encoding: "UTF-8"
  
  text_frames:
    TIT2: true
    TPE1: true
    TALB: true
    TDRC: true
    TRCK: true
    TPOS: true
    TCON: true
    TCOM: true
    TPUB: true
    TCOP: true
    TBPM: true
    TKEY: true
    TSRC: true
    
  picture_frames:
    APIC: true
    max_pictures: 5
    
  url_frames:
    WOAR: true
    WCOM: true
    WPUB: true
    
  priv_frames:
    enabled: false
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| TagLib | 2.0.x | Metadata read/write |
| ID3Lib | 3.8.x | ID3 specific operations |
| libFLAC | 1.4.x | FLAC metadata |
| MySQL | 8.0.x | Metadata storage |
| Redis | 7.x | Metadata cache |
| ElasticSearch | 8.x | Full-text search |

## Durum: Uygulama

Metadata engine tam olarak implemente edilmiştir. Tüm formatlar okuma/yazma desteği ile entegre edilmiştir.

| Format | Read | Write | Artwork | Search |
|--------|------|-------|---------|--------|
| ID3v1 | Tamamlandı | Tamamlandı | Hayır | Tamamlandı |
| ID3v2.4 | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| Vorbis Comments | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| APE Tags | Tamamlandı | Tamamlandı | Hayır | Tamamlandı |
| iTunes MP4 | Tamamlandı | Tamamlandı | Tamamlandı | Tamamlandı |
| ReplayGain | Tamamlandı | Tamamlandı | N/A | Tamamlandı |
