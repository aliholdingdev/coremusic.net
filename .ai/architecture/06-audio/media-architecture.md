---
title: "CoreMusic — Media Architecture"
type: architecture
category: media
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Media Architecture

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[ADR-026-download-service-architecture]]

---

## 1. Amaç

Medya yönetim altyapısını tanımlar. Music library, metadata, cover art ve media processing.

---

## 2. Media Components

| Bileşen | Sorumluluk | Port |
|---------|------------|------|
| Media Service | Library, metadata, streaming | 5000/6000 |
| Download Service | İndirme yönetimi | 3001 |
| Audio Service | Playback, DSP | 9741/9742 |
| File Storage | Dosya sistemi | — |

---

## 3. Media Pipeline

```
Upload/Download → Metadata Extract → DB Store → Index → Search → Play
      ↓                ↓                ↓          ↓        ↓       ↓
   File I/O       FFprobe/FFmpeg    MySQL     Fulltext  Query   PCM
```

---

## 4. Supported Formats

| Format | Type | Codec |
|--------|------|-------|
| FLAC | Lossless | FLAC |
| MP3 | Lossy | MPEG Layer 3 |
| AAC | Lossy | AAC-LC |
| WAV | Lossless | PCM |
| ALAC | Lossless | Apple Lossless |
| OGG | Lossy | Vorbis/Opus |

---

## 5. Metadata Fields

| Alan | Kaynak | Zorunlu |
|------|--------|---------|
| title | ID3/Vorbis | ✅ |
| artist | ID3/Vorbis | ✅ |
| album | ID3/Vorbis | ✅ |
| genre | ID3/Vorbis | ❌ |
| year | ID3/Vorbis | ❌ |
| track_number | ID3/Vorbis | ❌ |
| cover_art | Embedded/URL | ❌ |
| bpm | Audio analysis | ❌ |
| key | Audio analysis | ❌ |

---

## 6. Storage Strategy

| Seviye | Konum | Kullanım |
|--------|-------|----------|
| Primary | MySQL BCNF | Metadata |
| Secondary | File system | Audio files |
| Cache | APCu/Redis | Hot data |
| Backup | Git/Cloud | Yedekleme |

---

## 7. Security

| Kural | Açıklama |
|-------|----------|
| File validation | MIME type check |
| Size limit | Max file size |
| Path traversal | Prevention |
| Rate limiting | Upload abuse |

---

## 8. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Components | [[ADR-026-download-service-architecture]] | Download service |
| § 4 Formats | [[ADR-038-8.1-sound-card-chip-selection]] | Audio codec |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
