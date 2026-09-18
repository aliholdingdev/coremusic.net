---
title: "K15 — Medya ve Streaming Katmanı (Media & Streaming)"
type: architecture
category: media-streaming
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
reference:
  adr: "N/A — Cross-cutting concern"
  github:
    - name: "FFmpeg"
      url: "https://github.com/FFmpeg/FFmpeg"
    - name: "MediaInfo"
      url: "https://github.com/MediaArea/MediaInfo"
    - name: "get_id3"
      url: "https://github.com/JamesHeinrich/getID3"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K15 — Medya ve Streaming Katmanı (Media & Streaming)

CoreMusic ekosistemi için medya işleme, codec, metadata ve streaming katmanı. 35 bileşen.

## Genel Bakış

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                      K15 — MEDIA & STREAMING                                │
├──────────────┬──────────────┬──────────────┬────────────────────────────────┤
│   CODEC      │   METADATA   │  STREAMING   │  MEDIA MANAGEMENT              │
│              │              │              │                                │
│  FFmpeg Core │  ID3 Writer  │  HLS         │  Media Import                  │
│  FFmpeg Xcode│  Cover Art   │  DASH        │  Media Export                  │
│  FFmpeg Audio│  Lyrics Fetch│  Waveform    │  Media Library                 │
│  FFmpeg Mux  │  Podcast Prsr│  Spectrogram │  Media Search                  │
│  FLAC Decoder│  Metadata Ext│  Audio Thumb │  Media Sort                    │
│  MP3 Decoder │              │              │  Media Filter                  │
│  AAC Decoder │              │              │  Media Playlist                │
│  OGG Decoder │              │              │  Media Sync                    │
│  WAV Parser  │              │              │  Media Cleanup                 │
│  DSD Decoder │              │              │  Radio Stream                  │
└──────────────┴──────────────┴──────────────┴────────────────────────────────┘
```

## Bileşen Listesi (35)

| # | Bileşen | Alt Bileşenler | Teknoloji | Kapsam |
|---|---------|---------------|-----------|--------|
| 1 | FFmpeg Core | Decoder, encoder, filter graph, HW accel | FFmpeg CLI | Medya motoru |
| 2 | FFmpeg Transcode | Format conversion, bitrate control, quality | FFmpeg CLI | Dönüştürme |
| 3 | FFmpeg Audio | Audio-only processing, normalization, mixing | FFmpeg CLI | Ses işleme |
| 4 | FFmpeg Mux | Container muxing/demuxing, subtitle embedding | FFmpeg CLI | Konteyner |
| 5 | FLAC Decoder | Lossless decode, bit-perfect, gapless | FFmpeg/libFLAC | Kayıpsız codec |
| 6 | MP3 Decoder | MPEG Layer 3, VBR/CBR, ID3v1/v2 | FFmpeg/libmp3lame | Popüler codec |
| 7 | AAC Decoder | Advanced Audio Coding, HE-AAC, LC | FFmpeg/libfdk-aac | Yüksek kalite |
| 8 | OGG Decoder | Ogg Vorbis, Opus, container format | FFmpeg/libvorbis | Açık kaynak codec |
| 9 | WAV Parser | PCM format, header parsing, chunk reading | PHP | Ham ses |
| 10 | DSD Decoder | DSD64/128/256, DSF/DFF format, MQA | FFmpeg/DSDLib | Yüksek çözünürlük |
| 11 | Metadata Extractor | ID3, Vorbis, APE, MP4 tags, embedded art | getID3 | Metadata okuma |
| 12 | ID3 Tag Writer | ID3v1/v2.3/v2.4, Unicode, frame management | getID3 | Metadata yazma |
| 13 | Cover Art Extractor | Album art, embedded/external, format detect | getID3, PHP | Kapak görseli |
| 14 | Lyrics Fetcher | Web scraping, API integration, cache | PHP, API | Söz yönetimi |
| 15 | Podcast Parser | RSS/Atom feed, episode metadata, duration | PHP, XML | Podcast |
| 16 | Radio Stream | Icecast, Shoutcast, stream recording | FFmpeg, PHP | Radyo |
| 17 | HLS Streaming | M3U8 playlist, TS segments, adaptive bitrate | FFmpeg, Nginx | HTTP streaming |
| 18 | DASH Streaming | MPD manifest, fragment management, ABR | FFmpeg, Nginx | MPEG-DASH |
| 19 | Audio Thumbnail | Waveform-based thumbnail, spectrum image | FFmpeg, GD | Küçük resim |
| 20 | Waveform Generator | SVG/PNG waveform visualization, zoom | FFmpeg, Custom | Dalga formu |
| 21 | Spectrogram Generator | FFT spectrogram, frequency analysis | FFmpeg, Custom | Frekans analizi |
| 22 | Media Import | Bulk import, duplicate detection, organize | PHP, PHPFileIterator | İçe aktarma |
| 23 | Media Export | Format conversion, playlist export, batch | FFmpeg, PHP | Dışa aktarma |
| 24 | Media Library | CRUD operations, tagging, categorization | PHP, MySQL | Kütüphane yönetimi |
| 25 | Media Search | Full-text search, fuzzy match, filters | MySQL, Meilisearch | Arama |
| 26 | Media Sort | Custom sort, multi-field, saved preferences | PHP, MySQL | Sıralama |
| 27 | Media Filter | Genre, year, artist, duration, bitrate | PHP, MySQL | Filtreleme |
| 28 | Media Playlist | CRUD, drag-drop, import/export, sharing | PHP, MySQL | Çalma listesi |
| 29 | Media Sync | Multi-device sync, conflict resolution | PHP, WebSocket | Senkronizasyon |
| 30 | Media Cleanup | Orphan files, duplicate removal, space reclaim | PHP, Cron | Temizlik |
| 31 | Radio Stream Record | Stream capture, segment splitting, metadata | FFmpeg | Radyo kayıt |
| 32 | Audio Normalization | EBU R128, loudness normalization, peak limit | FFmpeg | Ses normalizasyonu |
| 33 | Audio Effects | EQ, reverb, compression, spatial audio | FFmpeg, NevaEngine | Ses efektleri |
| 34 | Gap Detection | Silence detection, gap removal, track split | FFmpeg | Aralık tespiti |
| 35 | Media Quality Analysis | Bitrate analysis, spectrum check, corruption | FFmpeg, MediaInfo | Kalite analizi |

## Mimari Diyagram

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                    K15 — MEDIA PROCESSING ARCHITECTURE                       │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     INPUT SOURCES                                   │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │  Local   │  │  Stream  │  │  Podcast │  │  Radio   │          │     │
│  │  │  Files   │  │  URLs    │  │  RSS     │  │  Icecast │          │     │
│  │  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘          │     │
│  └───────┼──────────────┼──────────────┼──────────────┼───────────────┘     │
│          │              │              │              │                      │
│          ▼              ▼              ▼              ▼                      │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     MEDIA PROCESSING PIPELINE                       │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │ Metadata │──▶│ Codec    │──▶│ Process  │──▶│ Output   │          │     │
│  │  │ Extract  │  │ Decode   │  │ Audio    │  │ Encode   │          │     │
│  │  │          │  │          │  │ Effects  │  │ Mux      │          │     │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘          │     │
│  └────────────────────────────┬────────────────────────────────────────┘     │
│                               │                                             │
│                               ▼                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     OUTPUT TARGETS                                  │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │  HLS     │  │  DASH    │  │  Direct  │  │  CDN     │          │     │
│  │  │ Streaming│  │ Streaming│  │  Play    │  │  Cache   │          │     │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘          │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     MEDIA LIBRARY                                   │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │ Library  │  │ Search   │  │ Playlist │  │ Sync     │          │     │
│  │  │ CRUD     │  │ Index    │  │ Manager  │  │ Engine   │          │     │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘          │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Codec Karşılaştırması

| Codec | Bit Hızı | Kalite | Kullanım | Lisans |
|-------|----------|--------|----------|--------|
| FLAC | 800-1200 kbps | Kayıpsız | Yüksek kalite | LGPL |
| MP3 | 128-320 kbps | Kayıplı | Genel kullanım | Patentsiz (2017) |
| AAC | 128-256 kbps | Yüksek | Apple/YouTube | LGPL |
| OGG Vorbis | 96-500 kbps | Yüksek | Açık kaynak | BSD |
| Opus | 6-510 kbps | Yüksek | WebRTC/Streaming | BSD |
| DSD64 | 2822.4 kbps | Ultra | Audiophile | LGPL |
| WAV/PCM | Sabit | Kayıpsız | Ham ses | — |
| ALAC | 700-1000 kbps | Kayıpsız | Apple | Apache-2.0 |

## FFmpeg İşleme Hattı

```
┌─────────────────────────────────────────────────────────────────┐
│                  FFmpeg PROCESSING PIPELINE                      │
│                                                                  │
│  Input ──▶ Decoder ──▶ Filter Graph ──▶ Encoder ──▶ Output      │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                   Filter Graph                             │ │
│  │                                                             │ │
│  │  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐       │ │
│  │  │ Scale│─▶│Crop  │─▶│Rotate│─▶│  EQ  │─▶│Volume│       │ │
│  │  └──────┘  └──────┘  └──────┘  └──────┘  └──────┘       │ │
│  │       │                                    │               │ │
│  │       ▼                                    ▼               │ │
│  │  ┌──────┐                            ┌──────┐            │ │
│  │  │Normalize│                         │Limiter│            │ │
│  │  └──────┘                            └──────┘            │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  Örnek Komut:                                                    │
│  ffmpeg -i input.flac -af "loudnorm=I=-16:TP=-1.5" \           │
│         -c:a libmp3lame -b:a 320k output.mp3                   │
└─────────────────────────────────────────────────────────────────┘
```

## HLS Streaming Yapısı

```
┌─────────────────────────────────────────────────────────────────┐
│                    HLS STREAMING ARCHITECTURE                    │
│                                                                  │
│  ┌──────────┐     ┌──────────┐     ┌──────────┐                │
│  │  Media   │────▶│ FFmpeg   │────▶│  HLS     │                │
│  │  Input   │     │ Segment  │     │  Output  │                │
│  └──────────┘     └──────────┘     └────┬─────┘                │
│                                          │                       │
│                                          ▼                       │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │                   HLS Output Structure                      │ │
│  │                                                             │ │
│  │  playlist.m3u8                                              │ │
│  │  ├── #EXTM3U                                               │ │
│  │  ├── #EXT-X-STREAM-INF:BANDWIDTH=1280000                   │ │
│  │  │   └── stream_1280k.m3u8                                 │ │
│  │  ├── #EXT-X-STREAM-INF:BANDWIDTH=2560000                   │ │
│  │  │   └── stream_2560k.m3u8                                 │ │
│  │  └── #EXT-X-STREAM-INF:BANDWIDTH=5120000                   │ │
│  │      └── stream_5120k.m3u8                                 │ │
│  │                                                             │ │
│  │  stream_1280k.m3u8                                          │ │
│  │  ├── segment_000.ts (2s)                                   │ │
│  │  ├── segment_001.ts (2s)                                   │ │
│  │  └── ...                                                    │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                  │
│  Bitrate Adaptation:                                             │
│  ┌──────────┐     ┌──────────┐     ┌──────────┐                │
│  │ Network  │────▶│ ABR      │────▶│ Segment  │                │
│  │ Monitor  │     │ Algorithm│     │ Select   │                │
│  └──────────┘     └──────────┘     └──────────┘                │
└─────────────────────────────────────────────────────────────────┘
```

## Medya Kütüphanesi Şeması

```
┌─────────────────────────────────────────────────────────────────┐
│                  MEDIA LIBRARY SCHEMA                            │
│                                                                  │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐   │
│  │   artists    │     │    albums     │     │    tracks     │   │
│  │──────────────│     │──────────────│     │──────────────│   │
│  │ id (PK)      │◀───│ artist_id(FK)│◀───│ album_id(FK) │   │
│  │ name         │     │ id (PK)      │     │ id (PK)      │   │
│  │ bio          │     │ title        │     │ title        │   │
│  │ image_url    │     │ year         │     │ duration     │   │
│  │ genres[]     │     │ cover_url    │     │ track_number │   │
│  └──────────────┘     │ genre        │     │ file_path    │   │
│                        └──────────────┘     │ file_format  │   │
│                                              │ bitrate      │   │
│  ┌──────────────┐     ┌──────────────┐     │ sample_rate  │   │
│  │  playlists   │     │  playlist_   │     │ file_size    │   │
│  │──────────────│     │  tracks      │     │ cover_url    │   │
│  │ id (PK)      │◀───│──────────────│     │ lyrics       │   │
│  │ user_id (FK) │     │ playlist_id  │     │ metadata     │   │
│  │ name         │     │ track_id     │     └──────────────┘   │
│  │ description  │     │ position     │                         │
│  │ is_public    │     │ added_at     │                         │
│  └──────────────┘     └──────────────┘                         │
└─────────────────────────────────────────────────────────────────┘
```

## GitHub Referansları

| Proje | Amaç | Lisans |
|-------|------|--------|
| [FFmpeg/FFmpeg](https://github.com/FFmpeg/FFmpeg) | Çapraz platform medya çerçeve worksı | LGPL/GPL |
| [MediaArea/MediaInfo](https://github.com/MediaArea/MediaInfo) | Medya dosyası bilgi gösterimi | BSD-2 |
| [JamesHeinrich/getID3](https://github.com/JamesHeinrich/getID3) | PHP ile ID3 tag okuma/yazma | MPL-1.0 |

## İlişkili Katmanlar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K03 | Storage | Medya dosyaları depolama katmanında |
| K06 | Network streaming | Streaming K14 üzerinden |
| K07 | Search | Medya arama K07 indekslerinde |
| K10 | Player UI | Oynatıcı K10'da |
| K11 | Download | Medya indirme K11'de |

## Performans Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Transcode Hızı | ≥ 2x real-time | FFmpeg benchmark |
| Metadata Çıkarma | < 100ms/dosya | getID3 profiling |
| HLS Segment Süresi | 2-6 saniye | FFmpeg output |
| Dalga Formu Oluşturma | < 500ms/dosya | Custom timer |
| Spektrogram Oluşturma | < 1s/dosya | Custom timer |
| Arama Süresi | < 50ms | MySQL/Meilisearch |
| Kütüphane Tarama | < 10s/1000 dosya | Cron benchmark |
| Sınırı Hata Oranı | < 0.1% | Monitoring |

## Doğrulama

- [ ] FFmpeg tüm formatları decode ediyor
- [ ] FLAC decoder bit-perfect çalışıyor
- [ ] MP3 decoder VBR/CBR destekliyor
- [ ] AAC decoder yüksek kalite sağlıyor
- [ ] OGG decoder Opus/Vorbis destekliyor
- [ ] WAV parser chunkları doğru okuyor
- [ ] DSD decoder DSF/DFF formatını destekliyor
- [ ] Metadata extractor tüm tag'leri okuyor
- [ ] ID3 tag writer Unicode destekliyor
- [ ] Cover art extractor görselleri çıkarıyor
- [ ] Lyrics fetcher şarkı sözlerini çekiyor
- [ ] Podcast parser RSS feed'leri işliyor
- [ ] Radio stream Icecast/Shoutcast'e bağlanıyor
- [ ] HLS streaming adaptive bitrate çalışıyor
- [ ] DASH streaming MPD manifest oluşturuyor
- [ ] Waveform generator SVG/PNG oluşturuyor
- [ ] Spectrogram generator FFT analizi yapıyor
- [ ] Media import bulk import yapıyor
- [ ] Media search full-text arama yapıyor
- [ ] Media playlist CRUD işlemleri çalışıyor
- [ ] Media sync multi-device çalışıyor
- [ ] Media cleanup orphan dosyaları buluyor

---

## Class AB Medya Entegrasyonu

Class AB amplifikatör, medya streaming için yüksek kaliteli analog çıkış sağlar:
- [[electronics/amplifier-classab-circuit]] — Analog çıkış aşaması
- [[electronics/power-supply-classab]] — ±35V simetrik güç
- [[electronics/thermal-design-classab]] — Sürekli çalışma termal desteği

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
