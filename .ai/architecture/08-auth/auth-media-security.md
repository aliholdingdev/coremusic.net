---
type: architecture
category: auth
title: "Enterprise Auth — Media Vault Security"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Media Vault Security

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

media.coremusic.net'in güvenlik modelini tanımlar. Medya deposu (vault) sıradan bir web sayfası olarak değil, kapalı bir medya deposu olarak tasarlanmıştır.

## 2. Media Vault Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     MEDIA VAULT ARCHITECTURE                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                    KULLANICI                             │   │
│  │  (music.coremusic.net'de müzik dinliyor)                │   │
│  └─────────────────────────────────────────────────────────┘   │
│                          │                                      │
│                          ▼                                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                 AUTH SERVİSİ                             │   │
│  │  auth.coremusic.net                                     │   │
│  │  → Kullanıcının hakkını kontrol eder                    │   │
│  │  → Yetki token'ı üretir                                 │   │
│  └─────────────────────────────────────────────────────────┘   │
│                          │                                      │
│                          ▼                                      │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │               MEDYA DEPOSU (VAULT)                       │   │
│  │  media.coremusic.net                                    │   │
│  │  → Token doğrulama                                       │   │
│  │  → Medya akışı (streaming)                              │   │
│  │  → Doğrudan dosya erişimi YASAK                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 3. Media Access Rules

| Kural | Durum | Açıklama |
|-------|-------|----------|
| Doğrudan dosya yolu erişimi | ❌ YASAK | `/media/music/song.mp3` erişilemez |
| Dizin listeleme | ❌ YASAK | `/media/music/` listelenemez |
| Statik dosya paylaşımı | ❌ YASAK | Dosya indirme yok |
| Yetki anahtarı ile erişim | ✅ İZİN | Sadece yetkili kullanıcılar |
| Auth servisi doğrulaması | ✅ ZORUNLU | Her istekte kontrol |
| Streaming-only erişim | ✅ ZORUNLU | Sadece akış |

## 4. Media Access Flow

```
Kullanıcı → Müzik dinlemek ister
    │
    ▼
Panel (music/home/studio) → auth.coremusic.net'e istek atar
    │
    ▼
Auth Service → Kullanıcının hakkını kontrol eder
    │
    ├── Hakkı yok → 403 FORBIDDEN
    │
    └── Hakkı var → Yetki token'ı üretir
                        │
                        ▼
              media.coremusic.net → Token doğrular
                        │
                        ▼
              Medya akışı (Streaming) başlatılır
```

## 5. Media API Endpoints

### 5.1 GET /api/stream/{fileId}

Medya akışı (streaming).

**Request Headers:**
```
Authorization: Bearer {media-token}
Cookie: COREMUSIC_SESS=session-uuid
```

**Response (200 OK):**
```
Content-Type: audio/mpeg
Content-Length: 5242880
Content-Disposition: inline
Transfer-Encoding: chunked
```

### 5.2 GET /api/metadata/{fileId}

Medya metadata bilgisi.

**Request Headers:**
```
Cookie: COREMUSIC_SESS=session-uuid
```

**Response (200 OK):**
```json
{
  "file_id": "file-uuid",
  "title": "Song Title",
  "artist": "Artist Name",
  "album": "Album Name",
  "duration": 240,
  "format": "flac",
  "bitrate": "1411 kbps",
  "sample_rate": "44100 Hz"
}
```

### 5.3 GET /api/cover/{fileId}

Kapak görseli.

**Response (200 OK):**
```
Content-Type: image/jpeg
Content-Length: 500000
```

## 6. Media Security Layers

| Katman | KorumA | Teknoloji |
|--------|--------|-----------|
| **Auth** | Kullanıcı doğrulama | Session cookie |
| **Token** | Medya yetkisi | JWT token |
| **Streaming** | Dosya erişimi | Chunked transfer |
| **Vault** | Dosya koruması | Kapalı dizin |
| **Rate Limit** | Abuse koruması | APCu |
| **CORS** | Cross-domain | Whitelist |

## 7. Media Storage Structure

```
media.coremusic.net/
├── media/                          ← Fiziki medya dosyaları
│   ├── music/                      ← Müzik dosyaları
│   │   ├── flac/                   ← FLAC dosyaları
│   │   ├── mp3/                    ← MP3 dosyaları
│   │   └── wav/                    ← WAV dosyaları
│   ├── covers/                     ← Kapak görselleri
│   │   ├── thumbnails/             ← Küçük boyutlu görseller
│   │   └── full/                   ← Tam boyutlu görseller
│   └── metadata/                   ← Metadata dosyaları
│       └── json/                   ← JSON formatında metadata
├── index.php                       ← Entry point
├── include/
│   └── Controller/
│       ├── StreamController.php    ← Streaming endpoint
│       ├── MetadataController.php  ← Metadata API
│       └── CoverController.php     ← Kapak görseli
└── config/
    └── routes.php
```

## 8. Media Security Rules

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Doğrudan dosya erişimi yasak | Hiçbir dosya yolu doğrudan erişilemez |
| 2 | Dizin listeleme kapalı | Apache/Nginx ile dizin listeleme devre dışı |
| 3 | Token zorunlu | Her medya isteği için token gerekli |
| 4 | Streaming-only | Dosya indirme yok, sadece akış |
| 5 | Rate limiting | Medya istekleri için hız sınırlaması |
| 6 | Audit trail | Tüm medya erişimleri loglanır |

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Endpoints | 3 (Stream, Metadata, Cover) |
| Security Layers | 6 |
| Storage Rules | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
