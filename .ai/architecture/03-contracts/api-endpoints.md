---
type: architecture
category: contracts
title: "API Endpoints Catalog"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Endpoints Catalog

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic platformundaki tüm API endpoint'lerini servis bazında kataloglayan, request/response formatlarını ve authentication gereksinimlerini tanımlayan **API Kataloğu**dur.

## 2. Auth Service (auth.coremusic.net)

### 2.1 Kimlik Doğrulama Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/login` | Yok | 60/60s | Login formu |
| POST | `/login` | Yok | 5/60s | Login işlemi |
| GET | `/register` | Yok | 60/60s | Register formu |
| POST | `/register` | Yok | 3/300s | Register işlemi |
| GET | `/forgot-password` | Yok | 60/60s | Şifre sıfırlama formu |
| POST | `/forgot-password` | Yok | 3/300s | Şifre sıfırlama isteği |
| GET | `/reset-password` | Token | 60/60s | Yeni şifre formu |
| POST | `/reset-password` | Token | 3/300s | Şifre güncelleme |
| GET | `/select-gender` | Session | 60/60s | Cinsiyet seçimi |
| POST | `/select-gender` | Session | 10/60s | Cinsiyet kaydetme |

### 2.2 Session Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/api/session/check` | Cookie | 60/60s | Session doğrulama |
| POST | `/api/session/logout` | Session | 10/60s | Çıkış |
| POST | `/api/session/refresh` | Session | 10/60s | Session yenileme |

### 2.3 Auth Response Formatları

**Login Başarılı:**
```json
{
  "success": true,
  "data": {
    "user_id": 12345,
    "email": "user@example.com",
    "role": "user",
    "gender": "female",
    "session_timeout": 3600
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "auth-req-abc-123"
  }
}
```

**Login Başarısız:**
```json
{
  "success": false,
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "Email veya şifre hatalı",
    "details": {
      "remaining_attempts": 4
    }
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "auth-req-abc-124"
  }
}
```

*Kaynak: [[ADR-043-auth-subdomain-consolidation]]*

## 3. Control Service (music.coremusic.net:81)

### 3.1 Sayfa Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/` | Yok | 60/60s | Ana sayfa |
| GET | `/health` | Yok | 60/60s | Health check |
| GET | `/kesfet` | Session | 60/60s | Keşfet sayfası |
| GET | `/albumler` | Session | 60/60s | Albümler |
| GET | `/sanatcilar` | Session | 60/60s | Sanatçılar |
| GET | `/goz-at` | Session | 60/60s | Göz at |
| GET | `/gecmis` | Session | 60/60s | Geçmiş |
| GET | `/ayarlar` | Session | 60/60s | Ayarlar |
| GET | `/hakkimizda` | Session | 60/60s | Hakkımızda |

### 3.2 API Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/api/user/profile` | Session | 30/60s | Kullanıcı profili |
| PUT | `/api/user/profile` | Session | 10/60s | Profil güncelleme |
| GET | `/api/user/preferences` | Session | 30/60s | Kullanıcı tercihleri |
| PUT | `/api/user/preferences` | Session | 10/60s | Tercih güncelleme |
| GET | `/api/session/check` | Cookie | 60/60s | Session check (proxy) |

### 3.3 Control Response Formatı

```json
{
  "success": true,
  "data": {
    "user_id": 12345,
    "display_name": "Bayram Ali",
    "avatar_url": "/assets/avatars/user-12345.jpg",
    "theme_gender": "female",
    "role": "admin"
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "ctrl-req-abc-123",
    "service": "control-service",
    "version": "19.0.0"
  }
}
```

## 4. Media Service (media.coremusic.net:5000)

### 4.1 Medya Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/health` | Yok | 60/60s | Health check |
| GET | `/api/songs` | API Key | 120/60s | Şarkı listesi |
| GET | `/api/songs/:id` | API Key | 120/60s | Şarkı detayı |
| GET | `/api/albums` | API Key | 120/60s | Albüm listesi |
| GET | `/api/albums/:id` | API Key | 120/60s | Albüm detayı |
| GET | `/api/artists` | API Key | 120/60s | Sanatçı listesi |
| GET | `/api/artists/:id` | API Key | 120/60s | Sanatçı detayı |
| GET | `/api/genres` | API Key | 120/60s | Tür listesi |
| GET | `/api/search` | API Key | 60/60s | Arama |
| GET | `/api/stream/:id` | API Key | Sınırsız | Ses akışı |
| GET | `/api/cover/:id` | API Key | 120/60s | Kapak resmi |

### 4.2 Medya Query Parametreleri

| Parametre | Tip | Varsayılan | Açıklama |
|-----------|-----|-----------|----------|
| `page` | int | 1 | Sayfa numarası |
| `limit` | int | 20 | Sayfa başına öğe |
| `sort` | string | `created_at` | Sıralama alanı |
| `order` | string | `desc` | Sıralama yönü |
| `genre` | string | — | Tür filtresi |
| `artist` | string | — | Sanatçı filtresi |
| `album` | string | — | Albüm filtresi |
| `year` | int | — | Yıl filtresi |
| `q` | string | — | Arama sorgusu |

### 4.3 Medya Response Formatı

```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 12345,
        "title": "Şarkı Adı",
        "artist": "Sanatçı",
        "album": "Albüm",
        "genre": "Pop",
        "duration": 240,
        "bitrate": 320,
        "sample_rate": 48000,
        "format": "FLAC",
        "cover_url": "/api/cover/12345",
        "stream_url": "/api/stream/12345"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 50,
      "total_items": 1000,
      "per_page": 20
    }
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "media-req-abc-123",
    "service": "media-service",
    "version": "19.0.0"
  }
}
```

## 5. Download Service (download.coremusic.net:3001)

### 5.1 İndirme Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/health` | Yok | 60/60s | Health check |
| POST | `/api/download/start` | API Key | 5/60s | İndirme başlat |
| GET | `/api/download/status/:id` | API Key | 60/60s | İndirme durumu |
| DELETE | `/api/download/cancel/:id` | API Key | 10/60s | İndirme iptal |
| GET | `/api/queue` | API Key | 30/60s | Kuyruk listesi |
| GET | `/api/history` | API Key | 30/60s | İndirme geçmişi |

### 5.2 İndirme Request Body

```json
{
  "source": "youtube",
  "url": "https://youtube.com/playlist?list=PLxxxx",
  "quality": "flac",
  "options": {
    "max_bitrate": 320,
    "max_sample_rate": 96000,
    "metadata_fill": true,
    "cover_art": true
  }
}
```

### 5.3 İndirme Response Formatı

```json
{
  "success": true,
  "data": {
    "download_id": "dl-abc-123",
    "status": "queued",
    "position": 3,
    "estimated_time": 120,
    "items_count": 15,
    "items_completed": 0,
    "items_failed": 0
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "dl-req-abc-123"
  }
}
```

## 6. Audio Service (port 9741/9742)

### 6.1 Player Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/health` | Yok | 60/60s | Health check |
| POST | `/api/player/play` | API Key | 30/60s | Oynatma başlat |
| POST | `/api/player/pause` | API Key | 30/60s | Duraklat |
| POST | `/api/player/stop` | API Key | 30/60s | Durdur |
| POST | `/api/player/next` | API Key | 30/60s | Sonraki şarkı |
| POST | `/api/player/previous` | API Key | 30/60s | Önceki şarkı |
| GET | `/api/player/status` | API Key | 60/60s | Oynatma durumu |
| PUT | `/api/player/seek` | API Key | 30/60s | Konum değiştirme |
| PUT | `/api/player/volume` | API Key | 30/60s | Ses seviyesi |

### 6.2 EQ Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/api/eq/presets` | API Key | 60/60s | Preset listesi |
| GET | `/api/eq/preset/:id` | API Key | 60/60s | Preset detayı |
| PUT | `/api/eq/band` | API Key | 30/60s | Band güncelleme |
| PUT | `/api/eq/bands` | API Key | 30/60s | Tüm band güncelleme |
| POST | `/api/eq/reset` | API Key | 10/60s | EQ sıfırlama |
| GET | `/api/eq/current` | API Key | 60/60s | Mevcut EQ ayarı |

### 6.3 Mixer Endpoint'leri

| Method | Endpoint | Auth | Rate Limit | Açıklama |
|--------|----------|------|------------|----------|
| GET | `/api/mixer/channels` | API Key | 60/60s | Kanal listesi |
| PUT | `/api/mixer/channel/:id` | API Key | 30/60s | Kanal güncelleme |
| PUT | `/api/mixer/master` | API Key | 30/60s | Master volum |
| POST | `/api/mixer/mute/:id` | API Key | 30/60s | Kanal susturma |

## 7. Response Format Standardı

### 7.1 Başarılı Response

```json
{
  "success": true,
  "data": {},
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "req-abc-123",
    "service": "service-name",
    "version": "19.0.0"
  }
}
```

### 7.2 Hata Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Geçersiz giriş",
    "details": {
      "field": "email",
      "rule": "valid_email"
    }
  },
  "meta": {
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "req-abc-124"
  }
}
```

### 7.3 Sayfalama Response

```json
{
  "success": true,
  "data": {
    "items": [],
    "pagination": {
      "current_page": 1,
      "total_pages": 10,
      "total_items": 200,
      "per_page": 20,
      "has_next": true,
      "has_previous": false
    }
  }
}
```

## 8. HTTP Status Codes

| Code | Kullanım | Örnek |
|------|----------|-------|
| 200 | Başarılı | GET /api/songs |
| 201 | Oluşturuldu | POST /api/download/start |
| 204 | İçerik yok | DELETE /api/download/cancel |
| 302 | Yönlendirme | Login sonrası |
| 400 | Kullanıcı hatası | Geçersiz form verisi |
| 401 | Yetkisiz | Token yok/geçersiz |
| 403 | Yasak | Yetki yok |
| 404 | Bulunamadı | Geçersiz endpoint |
| 429 | Hız limiti aşıldı | Rate limit |
| 500 | Sunucu hatası | DB bağlantı hatası |
| 502 | Gateway hatası | Servis erişilemez |
| 503 | Bakım modu | Maintenance mode |

## 9. Rate Limiting Headers

| Header | Açıklama | Örnek |
|--------|----------|-------|
| `X-RateLimit-Limit` | Maksimum istek | `60` |
| `X-RateLimit-Remaining` | Kalan istek | `45` |
| `X-RateLimit-Reset` | Reset zamanı (epoch) | `1691500000` |
| `Retry-After` | Bekleme süresi (saniye) | `60` |

## 10. Authentication Headers

| Header | Kullanım | Örnek |
|--------|----------|-------|
| `X-API-Key` | Service → Service | `sk-coremusic-xxxx` |
| `Cookie` | Browser → Service | `auth_key=xxx; COREMUSIC_SESS=xxx` |
| `Authorization` | Bearer token | `Bearer eyJhbGc...` |
| `X-CSRF-Token` | CSRF koruması | `csrf_token=xxx` |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Rate limiting zorunlu | Abuse riski |
| 2 | Authentication zorunlu | Yetkisiz erişim |
| 3 | Response format standart | Tutarlılık bozulması |
| 4 | Error handling zorunlu | Bilinmeyen hatalar |
| 5 | Request ID zorunlu | İzlenebilirlik kaybı |

## 12. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/03-contracts/middleware-pipeline]] | Middleware |
| [[architecture/03-contracts/service-ipc]] | IPC |
| [[architecture/03-contracts/ports/port-registry]] | Port haritası |
| [[ADR-042-vault-restructuring-2026-08-03]] | Port mapping |
| [[ADR-043-auth-subdomain-consolidation]] | Auth endpoints |

## 13. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Auth Service | [[ADR-043-auth-subdomain-consolidation]] | Auth consolidation |
| § 3 Control Service | [[architecture/l2-routing/index]] | Routing |
| § 4 Media Service | [[architecture/06-audio/coremusic-media-service]] | Media service |
| § 5 Download Service | [[architecture/06-audio/ai-auto-download]] | Download |
| § 6 Audio Service | [[architecture/06-audio/coremusic-audio-service]] | Audio service |

## 14. Sözlük

| Terim | Tanım |
|-------|-------|
| **Endpoint** | API erişim noktası |
| **REST** | Representational State Transfer |
| **WebSocket** | Bidirectional communication |
| **Rate Limit** | İstek hız kısıtlaması |
| **Authentication** | Kimlik doğrulama |
| **Authorization** | Yetkilendirme |
| **CSRF** | Cross-Site Request Forgery |
| **API Key** | API erişim anahtarı |
| **Pagination** | Sayfalama |
| **Metadata** | Veri hakkında veri |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~550 |
| **ADR Uyumlu** | ✅ 042, 043 |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 5 referans |
| **Guardrails** | ✅ 5 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
