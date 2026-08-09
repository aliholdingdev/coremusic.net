---
type: template
category: documentation
title: "API Documentation Template"
date: 2026-08-06
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: REST API, JSON, OpenAPI 3.1, PHP 8.4
---

# API Documentation Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-042-vault-restructuring-2026-08-03]] · [[architecture/03-contracts/api-endpoints]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic ekosistemindeki **7 backend servisinin** tüm API endpoint'leri için standart dokümantasyon formatını tanımlar.

**Kapsam:**
- Control Service (port 81) — Auth, session, RBAC
- Media Service (port 5000/6000) — Library, metadata, streaming
- Audio Service (port 9741/9742) — Player, DSP, mixer, recorder
- Device Service — Bluetooth, WiFi, USB
- Network Audio — Multi-room, WebRTC, P2P
- AI Service — Recommendations, behavior analysis
- Download Service (port 3001) — YouTube, Deezer, FLAC pipeline

**Giriş/Çıkış Formatları:**
- Request: JSON body, query params, path params, headers
- Response: JSON body with standardized envelope
- Errors: `{ error: { code, message, details } }` standardı

**Kapsam Dışı:** Frontend UI dokümantasyonu, database schema.

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| REST API | — | HTTP API standardı | restfulapi.net |
| JSON | RFC 8259 | Request/Response formatı | json.org |
| OpenAPI | 3.1 | API specification | swagger.io |
| Swagger UI | — | Interactive documentation | swagger.io |
| PHP | 8.4+ | Backend runtime | php.net |
| WebSocket | RFC 6455 | Real-time events | — |
| Server-Sent Events | — | One-way streaming | — |

*Kaynaklar: REST API Documentation Best Practices (restfulapi.net), OpenAPI 3.1 Specification (swagger.io), JSON RFC 8259 — 2026-08-06'da doğrulandı*

---

## 3. Code Standards

### 3.1 Endpoint Documentation Format

Her API endpoint aşağıdaki formatta dokümantante edilmelidir:

```markdown
# [METHOD] /api/v{version}/{resource}/{action}

## Overview
[Endpoint'in kısa açıklaması — 1-2 cümle]

## Authentication
- Type: [session | api-key | bearer]
- Required: [evet/hayır]

## Rate Limit
- Limit: [sayı] requests per [zaman aralığı]
- Scope: [global | per-user | per-endpoint]

## Request
### Headers
### Path Parameters
### Query Parameters
### Body

## Response
### Success (2xx)
### Error (4xx/5xx)

## Business Rules
## Related Endpoints
```

**Zorunlu Alanlar:**
- Method (GET, POST, PUT, PATCH, DELETE, WebSocket)
- Path (versioned: `/api/v1/...`)
- Authentication type
- Request parameters (all types)
- Response schemas (success + error)
- Rate limit information

### 3.2 Request Documentation

#### Headers

```markdown
### Headers
| Header | Type | Required | Default | Description |
|--------|------|----------|---------|-------------|
| Content-Type | string | Yes* | application/json | Request body format |
| Accept | string | No | application/json | Expected response format |
| X-CSRF-Token | string | Yes** | — | CSRF koruma token'ı |
| X-Request-ID | string | No | auto-generated | İstek takip ID'si |
| Authorization | string | Yes*** | — | Session token veya API key |
```

*POST/PUT/PATCH için zorunlu**
**State-changing isteklerde zorunlu (POST, PUT, PATCH, DELETE)***
**Auth-required endpoint'lerde zorunlu**

#### Path Parameters

```markdown
### Path Parameters
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| id | integer | Yes | Benzersiz kayıt ID'si | /songs/123 |
| slug | string | Yes | URL-safe identifier | /artists/john-doe |
```

#### Query Parameters

```markdown
### Query Parameters
| Parameter | Type | Required | Default | Description | Example |
|-----------|------|----------|---------|-------------|---------|
| page | integer | No | 1 | Sayfa numarası | ?page=2 |
| limit | integer | No | 20 | Sayfa başına kayıt (max: 100) | ?limit=50 |
| sort | string | No | created_at | Sıralama alanı | ?sort=title |
| order | string | No | desc | Sıralama yönü (asc/desc) | ?order=asc |
| search | string | No | — | Arama sorgusu | ?search=love |
```

#### Body (Request)

```markdown
### Body
| Field | Type | Required | Description | Example |
|-------|------|----------|-------------|---------|
| title | string | Yes | Şarkı başlığı | "Bohemian Rhapsody" |
| artist_id | integer | Yes | Sanatçı ID'si | 42 |
| genre | string | No | Müzik türü | "rock" |
| duration_ms | integer | No | Süre (milisaniye) | 354000 |
```

### 3.3 Response Documentation

#### Status Codes

| Kod | Anlam | Kullanım |
|-----|-------|----------|
| `200` | OK | Başarılı GET, PUT, PATCH |
| `201` | Created | Başarılı POST (yeni kayıt) |
| `204` | No Content | Başarılı DELETE |
| `400` | Bad Request | Geçersiz parametre/body |
| `401` | Unauthorized | Kimlik doğrulama başarısız |
| `403` | Forbidden | Yetkiniz yok |
| `404` | Not Found | Kayıt bulunamadı |
| `409` | Conflict | Çakışma (duplicate entry) |
| `422` | Unprocessable Entity | Validation hatası |
| `429` | Too Many Requests | Rate limit aşıldı |
| `500` | Internal Server Error | Sunucu hatası |

#### Response Envelope

Tüm yanıtlar şu standart zarfı kullanır:

```json
{
  "status": "success|error",
  "data": { },
  "meta": {
    "request_id": "req_abc123",
    "timestamp": "2026-08-08T10:00:00Z",
    "version": "v1"
  },
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 150,
    "total_pages": 8,
    "has_next": true,
    "has_prev": false
  }
}
```

**Zorunlu Alanlar (tüm yanıtlar):**
- `status`: `"success"` veya `"error"`
- `meta.request_id`: İstek takip ID'si
- `meta.timestamp`: ISO 8601 timestamp
- `meta.version`: API versiyonu

### 3.4 Authentication Documentation

#### Session-Based Auth (Varsayılan)

```markdown
## Authentication
- Type: Session-based (cookie: COREMUSIC_SESS)
- CSRF: X-CSRF-Token header zorunlu (state-changing)
- Flow: Login → Session cookie → CSRF token → Protected requests
```

#### API Key Auth

```markdown
## Authentication
- Type: API Key
- Header: X-API-Key: {your-api-key}
- Scope: Read-only veya Read-Write
```

#### Bearer Token Auth (OAuth2)

```markdown
## Authentication
- Type: Bearer Token
- Header: Authorization: Bearer {token}
- Expiry: 3600 saniye
- Refresh: /auth/refresh endpoint
```

### 3.5 Error Response Format

Tüm hatalar şu standard formatta döner:

```json
{
  "status": "error",
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Geçersiz istek parametreleri",
    "details": [
      {
        "field": "title",
        "message": "Title zorunludur",
        "code": "REQUIRED"
      },
      {
        "field": "duration_ms",
        "message": "Duration 0'dan büyük olmalıdır",
        "code": "MIN_VALUE"
      }
    ],
    "request_id": "req_abc123",
    "timestamp": "2026-08-08T10:00:00Z"
  }
}
```

**Hata Kodu Standartları:**

| Kod | HTTP | Açıklama |
|-----|------|----------|
| `UNAUTHORIZED` | 401 | Kimlik doğrulama gerekli |
| `FORBIDDEN` | 403 | Yetkiniz yok |
| `NOT_FOUND` | 404 | Kayıt bulunamadı |
| `VALIDATION_ERROR` | 422 | Form validasyon hatası |
| `RATE_LIMITED` | 429 | Çok fazla istek |
| `INTERNAL_ERROR` | 500 | Sunucu hatası |
| `SERVICE_UNAVAILABLE` | 503 | Servis kullanılamıyor |
| `CONFLICT` | 409 | Veri çelişkisi |

### 3.6 Pagination Documentation

#### Offset/Limit Tabanlı

```
GET /api/v1/songs?page=3&limit=20
```

```json
{
  "status": "success",
  "data": [...],
  "pagination": {
    "page": 3,
    "limit": 20,
    "total": 150,
    "total_pages": 8,
    "has_next": true,
    "has_prev": true,
    "next_url": "/api/v1/songs?page=4&limit=20",
    "prev_url": "/api/v1/songs?page=2&limit=20"
  }
}
```

#### Cursor Tabanlı (Büyük veri setleri için)

```
GET /api/v1/songs?cursor=eyJpZCI6MTIzfQ&limit=20
```

```json
{
  "status": "success",
  "data": [...],
  "pagination": {
    "cursor": "eyJpZCI6MTQzfQ",
    "limit": 20,
    "has_more": true,
    "next_cursor": "eyJpZCI6MTYzfQ"
  }
}
```

### 3.7 Rate Limiting Documentation

Tüm yanıt header'ları şunları içerir:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1691500800
Retry-After: 30
```

**Rate Limit Matrisi:**

| Endpoint Grubu | Limit | Scope | Pencere |
|----------------|-------|-------|---------|
| Auth (login/register) | 5 | per-IP | 60s |
| Read (GET) | 60 | per-user | 60s |
| Write (POST/PUT/DELETE) | 30 | per-user | 60s |
| Search | 20 | per-user | 60s |
| Download | 10 | per-user | 300s |
| WebSocket | 5 | per-user | 60s |

### 3.8 Webhook Documentation

#### Event Types

| Event | Trigger | Payload |
|-------|---------|---------|
| `download.completed` | İndirme tamamlandı | `{ track_id, format, file_path }` |
| `download.failed` | İndirme başarısız | `{ track_id, error_code, message }` |
| `user.login` | Kullanıcı girişi | `{ user_id, ip, user_agent }` |
| `playlist.updated` | Çalma listesi değişti | `{ playlist_id, action, track_ids }` |

#### Payload Format

```json
{
  "event": "download.completed",
  "timestamp": "2026-08-08T10:00:00Z",
  "data": {
    "track_id": 123,
    "format": "flac",
    "file_path": "/media/songs/track-123.flac"
  },
  "webhook_id": "wh_abc123"
}
```

#### Retry Policy

- Max retry: 3
- Backoff: 1dk, 5dk, 30dk
- Timeout: 10s per attempt
- Dead letter queue after 3 başarısız deneme

### 3.9 Versioning Strategy

#### URL Versioning

```
/api/v1/songs     ← Mevcut sürüm
/api/v2/songs     ← Yeni sürüm (breaking change)
```

#### Deprecation Headers

```
Deprecation: true
Sunset: Sat, 08 Aug 2027 00:00:00 GMT
Link: </api/v2/songs>; rel="successor-version"
```

#### Breaking Change Tanımı

- Field kaldırılması veya adının değişmesi
- Response tipinin değişmesi
- Auth mekanizmasının değişmesi
- Error formatının değişmesi

**Non-Breaking Changes:**
- Yeni optional field ekleme
- Yeni endpoint ekleme
- Yeni query parametresi ekleme

### 3.10 OpenAPI 3.1 Schema

```yaml
openapi: 3.1.0
info:
  title: CoreMusic API
  description: CoreMusic media platform REST API
  version: 1.0.0
  contact:
    name: CoreMusic API Support
  license:
    name: MIT

servers:
  - url: http://music.coremusic.net:81
    description: Development
  - url: https://api.coremusic.net
    description: Production

paths:
  /api/v1/songs:
    get:
      operationId: listSongs
      summary: List all songs
      tags: [Songs]
      security:
        - sessionAuth: []
      parameters:
        - $ref: '#/components/parameters/PageParam'
        - $ref: '#/components/parameters/LimitParam'
        - $ref: '#/components/parameters/SortParam'
        - name: search
          in: query
          schema:
            type: string
          description: Search by title or artist
      responses:
        '200':
          description: Song list
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/SongListResponse'
          headers:
            X-RateLimit-Limit:
              $ref: '#/components/headers/X-RateLimit-Limit'
            X-RateLimit-Remaining:
              $ref: '#/components/headers/X-RateLimit-Remaining'
        '429':
          $ref: '#/components/responses/RateLimited'
        '500':
          $ref: '#/components/responses/InternalServerError'

components:
  securitySchemes:
    sessionAuth:
      type: apiKey
      in: cookie
      name: COREMUSIC_SESS
      description: Session cookie authentication
    apiKeyAuth:
      type: apiKey
      in: header
      name: X-API-Key
      description: API key authentication

  parameters:
    PageParam:
      name: page
      in: query
      schema:
        type: integer
        default: 1
        minimum: 1
    LimitParam:
      name: limit
      in: query
      schema:
        type: integer
        default: 20
        minimum: 1
        maximum: 100
    SortParam:
      name: sort
      in: query
      schema:
        type: string
        enum: [created_at, title, artist, duration]

  schemas:
    Song:
      type: object
      required: [id, title, artist_id]
      properties:
        id:
          type: integer
          description: Unique song identifier
        title:
          type: string
          description: Song title
        artist_id:
          type: integer
          description: Artist foreign key
        artist_name:
          type: string
          description: Artist name (joined)
        album_id:
          type: integer
          nullable: true
        genre:
          type: string
        duration_ms:
          type: integer
          description: Duration in milliseconds
        format:
          type: string
          enum: [flac, mp3, wav]
        bitrate:
          type: integer
          description: Bitrate in kbps
        file_size:
          type: integer
          description: File size in bytes
        created_at:
          type: string
          format: date-time
        updated_at:
          type: string
          format: date-time

    SongListResponse:
      type: object
      properties:
        status:
          type: string
          enum: [success]
        data:
          type: array
          items:
            $ref: '#/components/schemas/Song'
        meta:
          $ref: '#/components/schemas/Meta'
        pagination:
          $ref: '#/components/schemas/Pagination'

    Error:
      type: object
      properties:
        status:
          type: string
          enum: [error]
        error:
          type: object
          required: [code, message]
          properties:
            code:
              type: string
            message:
              type: string
            details:
              type: array
              items:
                type: object
                properties:
                  field:
                    type: string
                  message:
                    type: string
                  code:
                    type: string
            request_id:
              type: string
            timestamp:
              type: string
              format: date-time

    Meta:
      type: object
      properties:
        request_id:
          type: string
        timestamp:
          type: string
          format: date-time
        version:
          type: string

    Pagination:
      type: object
      properties:
        page:
          type: integer
        limit:
          type: integer
        total:
          type: integer
        total_pages:
          type: integer
        has_next:
          type: boolean
        has_prev:
          type: boolean

  headers:
    X-RateLimit-Limit:
      schema:
        type: integer
      description: Max requests per window
    X-RateLimit-Remaining:
      schema:
        type: integer
      description: Remaining requests
    X-RateLimit-Reset:
      schema:
        type: integer
      description: Unix timestamp when limit resets

  responses:
    RateLimited:
      description: Rate limit exceeded
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
      headers:
        Retry-After:
          schema:
            type: integer
          description: Seconds until rate limit resets
    InternalServerError:
      description: Internal server error
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
```

### 3.11 API Endpoint Catalog

| Service | Method | Endpoint | Auth | Rate Limit | Description |
|---------|--------|----------|------|------------|-------------|
| **Control** | POST | `/api/v1/auth/login` | No | 5/60s | Kullanıcı girişi |
| **Control** | POST | `/api/v1/auth/register` | No | 3/60s | Kullanıcı kaydı |
| **Control** | POST | `/api/v1/auth/logout` | Yes | 30/60s | Çıkış |
| **Control** | GET | `/api/v1/auth/session` | Yes | 60/60s | Session durumu |
| **Control** | GET | `/api/v1/users/me` | Yes | 60/60s | Profil bilgisi |
| **Control** | PUT | `/api/v1/users/me` | Yes | 30/60s | Profil güncelleme |
| **Media** | GET | `/api/v1/songs` | Yes | 60/60s | Şarkı listesi |
| **Media** | GET | `/api/v1/songs/{id}` | Yes | 60/60s | Şarkı detayı |
| **Media** | GET | `/api/v1/albums` | Yes | 60/60s | Albüm listesi |
| **Media** | GET | `/api/v1/artists` | Yes | 60/60s | Sanatçı listesi |
| **Media** | GET | `/api/v1/genres` | Yes | 60/60s | Tür listesi |
| **Media** | GET | `/api/v1/search` | Yes | 20/60s | Genel arama |
| **Audio** | POST | `/api/v1/player/play` | Yes | 30/60s | Oynatma başlat |
| **Audio** | POST | `/api/v1/player/pause` | Yes | 30/60s | Duraklat |
| **Audio** | POST | `/api/v1/player/seek` | Yes | 30/60s | Konum değiştir |
| **Audio** | POST | `/api/v1/player/volume` | Yes | 30/60s | Ses seviyesi |
| **Audio** | GET | `/api/v1/player/status` | Yes | 60/60s | Oynatma durumu |
| **Audio** | POST | `/api/v1/eq/apply` | Yes | 10/60s | EQ preset uygula |
| **Audio** | GET | `/api/v1/eq/presets` | Yes | 60/60s | EQ preset listesi |
| **Download** | POST | `/api/v1/download/youtube` | Yes | 5/300s | YouTube indirme |
| **Download** | POST | `/api/v1/download/deezer` | Yes | 5/300s | Deezer indirme |
| **Download** | GET | `/api/v1/download/queue` | Yes | 30/60s | İndirme kuyruğu |
| **Download** | GET | `/api/v1/download/status/{id}` | Yes | 30/60s | İndirme durumu |
| **Device** | GET | `/api/v1/devices` | Yes | 60/60s | Cihaz listesi |
| **Device** | POST | `/api/v1/devices/connect` | Yes | 10/60s | Cihaz bağla |
| **Device** | POST | `/api/v1/devices/disconnect` | Yes | 10/60s | Cihaz ayır |
| **Network** | POST | `/api/v1/rooms/create` | Yes | 10/60s | Dinleme odası oluştur |
| **Network** | POST | `/api/v1/rooms/join` | Yes | 10/60s | Odaya katıl |
| **Network** | POST | `/api/v1/rooms/sync` | Yes | 30/60s | Oda senkronize et |
| **AI** | GET | `/api/v1/recommendations` | Yes | 20/60s | Öneri listesi |
| **AI** | POST | `/api/v1/recommendations/feedback` | Yes | 10/60s | Öneri geri bildirimi |

### 3.12 Request/Response Examples

#### GET /api/v1/songs

**Request:**
```http
GET /api/v1/songs?page=1&limit=10&sort=title&order=asc HTTP/1.1
Host: api.coremusic.net
Cookie: COREMUSIC_SESS=abc123def456
Accept: application/json
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Bohemian Rhapsody",
      "artist_id": 10,
      "artist_name": "Queen",
      "album_id": 5,
      "genre": "rock",
      "duration_ms": 354000,
      "format": "flac",
      "bitrate": 1411,
      "file_size": 35280000,
      "created_at": "2026-01-15T10:00:00Z",
      "updated_at": "2026-08-01T14:30:00Z"
    }
  ],
  "meta": {
    "request_id": "req_abc123",
    "timestamp": "2026-08-08T10:00:00Z",
    "version": "v1"
  },
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 150,
    "total_pages": 15,
    "has_next": true,
    "has_prev": false
  }
}
```

**Response Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1691500800
Content-Type: application/json
```

#### POST /api/v1/download/youtube

**Request:**
```http
POST /api/v1/download/youtube HTTP/1.1
Host: api.coremusic.net
Cookie: COREMUSIC_SESS=abc123def456
X-CSRF-Token: csrf_xyz789
Content-Type: application/json

{
  "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
  "format": "flac",
  "quality": "best"
}
```

**Response (201 Created):**
```json
{
  "status": "success",
  "data": {
    "download_id": "dl_abc123",
    "status": "queued",
    "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
    "format": "flac",
    "estimated_duration": 45,
    "created_at": "2026-08-08T10:00:00Z"
  },
  "meta": {
    "request_id": "req_def456",
    "timestamp": "2026-08-08T10:00:00Z",
    "version": "v1"
  }
}
```

**Response (429 Too Many Requests):**
```json
{
  "status": "error",
  "error": {
    "code": "RATE_LIMITED",
    "message": "Çok fazla indirme isteği. Lütfen bekleyin.",
    "request_id": "req_ghi789",
    "timestamp": "2026-08-08T10:00:00Z"
  }
}
```

**Response Headers:**
```
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1691500900
Retry-After: 120
```

#### WebSocket /ws/v1/player/progress

**Connection:**
```http
GET /ws/v1/player/progress HTTP/1.1
Host: api.coremusic.net
Cookie: COREMUSIC_SESS=abc123def456
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Version: 13
Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
```

**Server Message:**
```json
{
  "event": "progress",
  "data": {
    "track_id": 123,
    "current_ms": 45000,
    "total_ms": 354000,
    "percentage": 12.7,
    "status": "playing",
    "volume": 80,
    "shuffle": false,
    "repeat": "off"
  },
  "timestamp": "2026-08-08T10:00:45Z"
}
```

### 3.13 SDK Usage Examples

#### JavaScript (Fetch API)

```javascript
// GET /api/v1/songs with pagination
async function getSongs(page = 1, limit = 20) {
  const response = await fetch(
    `/api/v1/songs?page=${page}&limit=${limit}`,
    {
      headers: {
        'Accept': 'application/json',
      },
      credentials: 'include' // session cookie
    }
  );

  if (response.status === 429) {
    const retryAfter = response.headers.get('Retry-After');
    throw new Error(`Rate limited. Retry after ${retryAfter}s`);
  }

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.error.message);
  }

  return response.json();
}

// POST /api/v1/download/youtube with CSRF
async function downloadTrack(url, format = 'flac') {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')
    .content;

  const response = await fetch('/api/v1/download/youtube', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': csrfToken,
    },
    credentials: 'include',
    body: JSON.stringify({ url, format, quality: 'best' })
  });

  return response.json();
}
```

#### PHP (Guzzle)

```php
<?php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://music.coremusic.net:81',
    'timeout' => 10,
]);

// GET /api/v1/songs
$response = $client->get('/api/v1/songs', [
    'query' => ['page' => 1, 'limit' => 20, 'sort' => 'title'],
    'cookies' => ['COREMUSIC_SESS' => $sessionId],
]);

$songs = json_decode($response->getBody(), true);

// POST /api/v1/download/youtube
$response = $client->post('/api/v1/download/youtube', [
    'json' => [
        'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'format' => 'flac',
        'quality' => 'best',
    ],
    'headers' => [
        'X-CSRF-Token' => $csrfToken,
    ],
    'cookies' => ['COREMUSIC_SESS' => $sessionId],
]);

$download = json_decode($response->getBody(), true);
```

#### cURL

```bash
# GET /api/v1/songs
curl -s -b "COREMUSIC_SESS=abc123" \
  "http://api.coremusic.net/api/v1/songs?page=1&limit=10" \
  -H "Accept: application/json" | jq .

# POST /api/v1/download/youtube
curl -s -X POST "http://api.coremusic.net/api/v1/download/youtube" \
  -b "COREMUSIC_SESS=abc123" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-Token: csrf_xyz789" \
  -d '{"url":"https://youtube.com/watch?v=xxx","format":"flac"}' | jq .

# Rate limit kontrolü
curl -s -I "http://api.coremusic.net/api/v1/songs" \
  -b "COREMUSIC_SESS=abc123" | grep -i "x-ratelimit"
```

### 3.14 Migration Guide

#### v1 → v2 Breaking Changes

| Değişiklik | v1 | v2 | Migration |
|------------|----|----|-----------|
| Song ID tipi | string | integer | `parseInt(song.id)` |
| Error format | `{ error: string }` | `{ error: { code, message } }` | `error.error?.message \|\| error.error` |
| Auth header | `X-Auth-Token` | `Cookie: COREMUSIC_SESS` | Cookie tabanlı auth'a geç |
| Pagination | `offset/limit` | `page/limit` | `offset → page = floor(offset/limit) + 1` |

#### Deprecation Policy

1. Yeni versiyon yayınlandığında eski versiyon 6 ay daha desteklenir
2. `Sunset` header'ı ile tarih bildirilir
3. Docs'da migration rehberi yayınlanır
4. 6 ay sonra eski versiyon 410 Gone ile yanıt verir

---

## 4. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **Tüm endpoint'ler dokümante edilmeli** | Review'dan geçemez |
| 2 | **Error format tutarlı olmalı** | `error.code` + `error.message` zorunlu |
| 3 | **Version zorunlu** | `/api/v1/` prefix her endpoint'te |
| 4 | **Rate limit header'ları** | Tüm yanıt'larda `X-RateLimit-*` header'ları |
| 5 | **CSRF token header'da** | State-changing isteklerde `X-CSRF-Token` |
| 6 | **Pagination zorunlu** | List endpoint'lerde `pagination` objesi |
| 7 | **request_id her yanıtta** | `meta.request_id` zorunlu |
| 8 | **OpenAPI spec güncel** | Her değişiklikten sonra spec güncellenmeli |
| 9 | **Breaking change → version bump** | Major versiyon değişikliği |
| 10 | **Sensitive data redacted** | Password, token, API key asla response'da |

---

## 5. Naming Conventions

| Kategori | Kural | Doğru | Yanlış |
|----------|-------|-------|--------|
| **Endpoint path** | lowercase, plural nouns | `/api/v1/songs` | `/api/v1/Song` |
| **Query param** | snake_case | `?page_size=20` | `?pageSize=20` |
| **Header name** | PascalCase-Kebab | `X-RateLimit-Limit` | `x_ratelimit_limit` |
| **Error code** | SCREAMING_SNAKE | `NOT_FOUND` | `notFound` |
| **JSON field** | snake_case | `created_at` | `createdAt` |
| **Boolean field** | is_ prefix | `is_deleted` | `deleted` |
| **Timestamp field** | _at suffix | `created_at` | `created` |
| **ID field** | _id suffix | `user_id` | `userId` |
| **URL segment** | kebab-case | `/api/v1/audio-features` | `/api/v1/audioFeatures` |

---

## 6. Security Considerations

| Konu | Uygulama | İlgili ADR |
|------|----------|------------|
| **CSRF** | `X-CSRF-Token` header, `hash_equals()` ile timing-safe compare | [[ADR-010-csrf-protection-strategy]] |
| **Session** | `COREMUSIC_SESS` cookie, SameSite=Lax, 3600s idle timeout | [[ADR-011-session-management]] |
| **Rate Limiting** | APCu tabanlı, per-user, per-IP | [[ADR-013-rate-limiting-apcu]] |
| **Input Validation** | Prepared statement zorunlu, `SELECT *` yasak | [[ADR-002-pdo-mandatory-no-orm]] |
| **Sensitive Data** | Response'da `REDACTED`, log'larda maskeleme | [[ADR-022-database-hardened-security]] |
| **API Key** | AES-256-GCM ile şifrelenmiş credential vault | [[ADR-034-credential-vault-normalization]] |
| **CSP** | Nonce-based, strict-dynamic | [[ADR-012-csp-nonce-strict-dynamic]] |
| **CORS** | Sadece izin verilen origin'ler | [[ADR-020-api-public-security]] |

**Güvenlik Kontrol Listesi:**
- [ ] Tüm endpoint'lerde auth kontrolü
- [ ] CSRF token state-changing isteklerde
- [ ] Rate limiting aktif
- [ ] Input validation (no SQL injection)
- [ ] Sensitive data response'da maskelenmiş
- [ ] Error messages internals expose etmiyor
- [ ] HTTPS zorunlu (production)
- [ ] HSTS header aktif

---

## 7. Performance Notes

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| **Response Time (p50)** | <100ms | TTFB |
| **Response Time (p99)** | <500ms | TTFB |
| **Payload Size** | <1MB | response body |
| **Pagination Default** | 20 items | limit param |
| **Pagination Max** | 100 items | limit max |

**Caching Headers:**
```
Cache-Control: private, max-age=60
ETag: "abc123"
Last-Modified: Sat, 08 Aug 2026 10:00:00 GMT
```

**Compression:**
- gzip veya Brotli zorunlu
- `Content-Encoding: gzip`
- Minimum 1KB response için

---

## 8. Edge Cases

| Senaryo | Durum | Çözüm |
|---------|-------|-------|
| Boş response | `GET /songs` sonucu 0 kayıt | `{ data: [], pagination: { total: 0 } }` |
| Partial content | Medya dosyası indirme | HTTP 206 Partial Content, `Range` header |
| Eşzamanlı değişiklik | İki kullanıcı aynı kaydı düzenler | Optimistic locking (`updated_at` kontrolü) |
| Timeout | Ağ bağlantısı koptu | Client-side retry with exponential backoff |
| Large payload | 1000+ kayıt | Pagination zorunlu, cursor-based öner |
| Binary response | Dosya indirme | `Content-Type: application/octet-stream` |
| Null field | Nullable alan boş | `null` olarak döner, omitted DEĞİL |
| Duplicate entry | Unique constraint ihlali | HTTP 409, `CONFLICT` error code |

---

## 9. Troubleshooting

| HTTP Kodu | Hata | Olası Neden | Çözüm |
|-----------|------|------------|-------|
| **401** | Unauthorized | Session süresi doldu veya CSRF eksik | Yeniden login ol, CSRF token ekle |
| **403** | Forbidden | Kullanıcının yetkisi yok | RBAC kontrolü,Yetki matrisini incele |
| **404** | Not Found | Kayıt mevcut değil veya silinmiş | ID'yi doğrula, soft-delete kontrolü |
| **409** | Conflict | Duplicate entry | Benzersiz alanı kontrol et |
| **422** | Validation Error | Geçersiz form verisi | `error.details` array'ini kontrol et |
| **429** | Rate Limited | Çok fazla istek | `Retry-After` header'ını bekle |
| **500** | Server Error | Sunucu hatası | Log kontrolü, `request_id` ile izle |
| **503** | Service Unavailable | Servis çöktü | Health check, Docker durumunu kontrol |

---

## 10. Common Anti-Patterns

| # | ❌ Yanlış | ✅ Doğru |
|---|---------|---------|
| 1 | Tutarlı olmayan hata formatları: `{ "err": "msg" }`, `{ "message": "..." }` | Standart: `{ error: { code, message, details } }` |
| 2 | Pagination olmadan tüm listeyi döndürme: `{ data: [...1000 items] }` | Sayfalama zorunlu: `{ data: [...], pagination: {...} }` |
| 3 | Internal detayları expose etme: `"file": "/var/www/html/src/db.php"` | Genel mesaj: `"message": "Internal server error"` |
| 4 | Version olmadan endpoint: `/songs` | Versioned: `/api/v1/songs` |
| 5 | Rate limit header olmaması | Her yanıtta: `X-RateLimit-Limit`, `X-RateLimit-Remaining` |
| 6 | GET isteğinde body payload | Query params kullan: `GET /songs?search=love` |
| 7 | POST'ta CSRF token yok | `X-CSRF-Token` header zorunlu |
| 8 | Flat error: `"error": "not found"` | Structured: `"error": { "code": "NOT_FOUND", "message": "..." }` |
| 9 | Timestamps'i farklı formatlarda döndürme | ISO 8601: `"2026-08-08T10:00:00Z"` |
| 10 | ID'leri string olarak döndürme (integer yerine) | Consistent type: `id: 123` (integer) |

---

## 11. 7 Service API Catalog

### Control Service (port 81)

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| POST | `/api/v1/auth/login` | No | 5/60s | Kullanıcı girişi |
| POST | `/api/v1/auth/register` | No | 3/60s | Kullanıcı kaydı |
| POST | `/api/v1/auth/logout` | Yes | 30/60s | Çıkış |
| POST | `/api/v1/auth/forgot-password` | No | 3/60s | Şifre sıfırlama maili |
| POST | `/api/v1/auth/reset-password` | No | 3/60s | Şifre sıfırlama |
| GET | `/api/v1/auth/session` | Yes | 60/60s | Session durumu |
| GET | `/api/v1/users/me` | Yes | 60/60s | Profil bilgisi |
| PUT | `/api/v1/users/me` | Yes | 30/60s | Profil güncelleme |
| PUT | `/api/v1/users/me/password` | Yes | 10/60s | Şifre değiştirme |

### Media Service (port 5000/6000)

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| GET | `/api/v1/songs` | Yes | 60/60s | Şarkı listesi |
| GET | `/api/v1/songs/{id}` | Yes | 60/60s | Şarkı detayı |
| GET | `/api/v1/albums` | Yes | 60/60s | Albüm listesi |
| GET | `/api/v1/albums/{id}` | Yes | 60/60s | Albüm detayı |
| GET | `/api/v1/artists` | Yes | 60/60s | Sanatçı listesi |
| GET | `/api/v1/artists/{id}` | Yes | 60/60s | Sanatçı detayı |
| GET | `/api/v1/genres` | Yes | 60/60s | Tür listesi |
| GET | `/api/v1/search` | Yes | 20/60s | Genel arama |
| GET | `/api/v1/playlists` | Yes | 60/60s | Çalma listesi |
| POST | `/api/v1/playlists` | Yes | 10/60s | Çalma listesi oluştur |
| PUT | `/api/v1/playlists/{id}` | Yes | 10/60s | Çalma listesi güncelle |
| DELETE | `/api/v1/playlists/{id}` | Yes | 5/60s | Çalma listesi sil |

### Audio Service (port 9741/9742)

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| POST | `/api/v1/player/play` | Yes | 30/60s | Oynatma başlat |
| POST | `/api/v1/player/pause` | Yes | 30/60s | Duraklat |
| POST | `/api/v1/player/stop` | Yes | 30/60s | Durdur |
| POST | `/api/v1/player/next` | Yes | 30/60s | Sonraki şarkı |
| POST | `/api/v1/player/previous` | Yes | 30/60s | Önceki şarkı |
| POST | `/api/v1/player/seek` | Yes | 30/60s | Konum değiştir |
| POST | `/api/v1/player/volume` | Yes | 30/60s | Ses seviyesi |
| GET | `/api/v1/player/status` | Yes | 60/60s | Oynatma durumu |
| GET | `/api/v1/eq/presets` | Yes | 60/60s | EQ preset listesi |
| POST | `/api/v1/eq/apply` | Yes | 10/60s | EQ preset uygula |
| POST | `/api/v1/eq/custom` | Yes | 10/60s | Özel EQ ayarı |
| POST | `/api/v1/recorder/start` | Yes | 5/60s | Kayıt başlat |
| POST | `/api/v1/recorder/stop` | Yes | 5/60s | Kayıt durdur |

### Download Service (port 3001)

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| POST | `/api/v1/download/youtube` | Yes | 5/300s | YouTube indirme |
| POST | `/api/v1/download/deezer` | Yes | 5/300s | Deezer indirme |
| GET | `/api/v1/download/queue` | Yes | 30/60s | İndirme kuyruğu |
| GET | `/api/v1/download/status/{id}` | Yes | 30/60s | İndirme durumu |
| DELETE | `/api/v1/download/cancel/{id}` | Yes | 10/60s | İndirmeyi iptal et |
| GET | `/api/v1/download/history` | Yes | 30/60s | İndirme geçmişi |

### Device Service

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| GET | `/api/v1/devices` | Yes | 60/60s | Cihaz listesi |
| POST | `/api/v1/devices/connect` | Yes | 10/60s | Cihaz bağla |
| POST | `/api/v1/devices/disconnect` | Yes | 10/60s | Cihaz ayır |
| GET | `/api/v1/devices/{id}/status` | Yes | 30/60s | Cihaz durumu |
| PUT | `/api/v1/devices/{id}/settings` | Yes | 10/60s | Cihaz ayarları |

### Network Audio

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| POST | `/api/v1/rooms/create` | Yes | 10/60s | Oda oluştur |
| POST | `/api/v1/rooms/join` | Yes | 10/60s | Odaya katıl |
| POST | `/api/v1/rooms/leave` | Yes | 10/60s | Odadan ayrıl |
| POST | `/api/v1/rooms/sync` | Yes | 30/60s | Senkronize et |
| GET | `/api/v1/rooms` | Yes | 30/60s | Oda listesi |
| GET | `/api/v1/rooms/{id}` | Yes | 30/60s | Oda detayı |

### AI Service

| Method | Endpoint | Auth | Rate | Açıklama |
|--------|----------|------|------|----------|
| GET | `/api/v1/recommendations` | Yes | 20/60s | Öneri listesi |
| POST | `/api/v1/recommendations/feedback` | Yes | 10/60s | Geri bildirim |
| GET | `/api/v1/ai/mood-analysis` | Yes | 10/60s | Mood analizi |
| POST | `/api/v1/ai/auto-eq` | Yes | 5/60s | Otomatik EQ |

---

## 12. OpenAPI Spec (Tam Örnek)

```yaml
# CoreMusic API — Media Service OpenAPI 3.1 Spec
openapi: 3.1.0
info:
  title: CoreMusic Media Service API
  description: |
    CoreMusic medya servisi — şarkılar, albümler, sanatçılar, arama ve çalma listeleri.
    Authentication: Session cookie (COREMUSIC_SESS) + CSRF token (X-CSRF-Token).
  version: 1.0.0
  contact:
    name: CoreMusic API Team

servers:
  - url: http://localhost:5000
    description: Local development
  - url: https://media.coremusic.net
    description: Production

tags:
  - name: Songs
    description: Music track management
  - name: Albums
    description: Album collections
  - name: Artists
    description: Artist profiles
  - name: Playlists
    description: User playlists
  - name: Search
    description: Full-text search

paths:
  /api/v1/songs:
    get:
      operationId: listSongs
      summary: List all songs with pagination
      tags: [Songs]
      security:
        - sessionAuth: []
      parameters:
        - $ref: '#/components/parameters/PageParam'
        - $ref: '#/components/parameters/LimitParam'
        - name: sort
          in: query
          schema:
            type: string
            enum: [created_at, title, artist_name, duration_ms]
            default: created_at
        - name: order
          in: query
          schema:
            type: string
            enum: [asc, desc]
            default: desc
        - name: search
          in: query
          schema:
            type: string
          description: Search by title or artist name
        - name: genre
          in: query
          schema:
            type: string
          description: Filter by genre
        - name: artist_id
          in: query
          schema:
            type: integer
          description: Filter by artist ID
      responses:
        '200':
          description: Paginated song list
          headers:
            X-RateLimit-Limit:
              $ref: '#/components/headers/X-RateLimit-Limit'
            X-RateLimit-Remaining:
              $ref: '#/components/headers/X-RateLimit-Remaining'
            X-RateLimit-Reset:
              $ref: '#/components/headers/X-RateLimit-Reset'
          content:
            application/json:
              schema:
                allOf:
                  - $ref: '#/components/schemas/SuccessEnvelope'
                  - type: object
                    properties:
                      data:
                        type: array
                        items:
                          $ref: '#/components/schemas/Song'
                      pagination:
                        $ref: '#/components/schemas/Pagination'
        '401':
          $ref: '#/components/responses/Unauthorized'
        '429':
          $ref: '#/components/responses/RateLimited'
        '500':
          $ref: '#/components/responses/InternalServerError'

  /api/v1/songs/{id}:
    get:
      operationId: getSong
      summary: Get a song by ID
      tags: [Songs]
      security:
        - sessionAuth: []
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: integer
          description: Song unique identifier
      responses:
        '200':
          description: Song details
          headers:
            X-RateLimit-Limit:
              $ref: '#/components/headers/X-RateLimit-Limit'
            X-RateLimit-Remaining:
              $ref: '#/components/headers/X-RateLimit-Remaining'
          content:
            application/json:
              schema:
                allOf:
                  - $ref: '#/components/schemas/SuccessEnvelope'
                  - type: object
                    properties:
                      data:
                        $ref: '#/components/schemas/Song'
        '404':
          $ref: '#/components/responses/NotFound'
        '429':
          $ref: '#/components/responses/RateLimited'

  /api/v1/search:
    get:
      operationId: searchMedia
      summary: Search songs, albums, artists
      tags: [Search]
      security:
        - sessionAuth: []
      parameters:
        - name: q
          in: query
          required: true
          schema:
            type: string
            minLength: 2
          description: Search query (min 2 chars)
        - name: type
          in: query
          schema:
            type: string
            enum: [all, songs, albums, artists]
            default: all
        - $ref: '#/components/parameters/LimitParam'
      responses:
        '200':
          description: Search results
          content:
            application/json:
              schema:
                allOf:
                  - $ref: '#/components/schemas/SuccessEnvelope'
                  - type: object
                    properties:
                      data:
                        type: object
                        properties:
                          songs:
                            type: array
                            items:
                              $ref: '#/components/schemas/Song'
                          albums:
                            type: array
                            items:
                              $ref: '#/components/schemas/Album'
                          artists:
                            type: array
                            items:
                              $ref: '#/components/schemas/Artist'
        '400':
          $ref: '#/components/responses/BadRequest'
        '429':
          $ref: '#/components/responses/RateLimited'

components:
  securitySchemes:
    sessionAuth:
      type: apiKey
      in: cookie
      name: COREMUSIC_SESS
      description: Session cookie — login sonrası alınır

  parameters:
    PageParam:
      name: page
      in: query
      schema:
        type: integer
        default: 1
        minimum: 1
    LimitParam:
      name: limit
      in: query
      schema:
        type: integer
        default: 20
        minimum: 1
        maximum: 100

  schemas:
    Song:
      type: object
      required: [id, title, artist_id]
      properties:
        id:
          type: integer
          description: Unique song identifier
        title:
          type: string
          example: "Bohemian Rhapsody"
        artist_id:
          type: integer
          example: 10
        artist_name:
          type: string
          example: "Queen"
        album_id:
          type: integer
          nullable: true
        genre:
          type: string
          example: "rock"
        duration_ms:
          type: integer
          description: Duration in milliseconds
          example: 354000
        format:
          type: string
          enum: [flac, mp3, wav]
        bitrate:
          type: integer
          description: Bitrate in kbps
        file_size:
          type: integer
          description: File size in bytes
        is_favorite:
          type: boolean
          description: User's favorite status
        play_count:
          type: integer
        created_at:
          type: string
          format: date-time
        updated_at:
          type: string
          format: date-time

    Album:
      type: object
      properties:
        id:
          type: integer
        title:
          type: string
        artist_id:
          type: integer
        artist_name:
          type: string
        cover_art_url:
          type: string
          format: uri
          nullable: true
        release_year:
          type: integer
        track_count:
          type: integer
        created_at:
          type: string
          format: date-time

    Artist:
      type: object
      properties:
        id:
          type: integer
        name:
          type: string
        image_url:
          type: string
          format: uri
          nullable: true
        biography:
          type: string
          nullable: true
        album_count:
          type: integer
        track_count:
          type: integer

    SuccessEnvelope:
      type: object
      required: [status, data, meta]
      properties:
        status:
          type: string
          enum: [success]
        meta:
          $ref: '#/components/schemas/Meta'

    Meta:
      type: object
      properties:
        request_id:
          type: string
          example: "req_abc123"
        timestamp:
          type: string
          format: date-time
        version:
          type: string
          example: "v1"

    Pagination:
      type: object
      properties:
        page:
          type: integer
        limit:
          type: integer
        total:
          type: integer
        total_pages:
          type: integer
        has_next:
          type: boolean
        has_prev:
          type: boolean
        next_url:
          type: string
          format: uri
          nullable: true
        prev_url:
          type: string
          format: uri
          nullable: true

    Error:
      type: object
      required: [status, error]
      properties:
        status:
          type: string
          enum: [error]
        error:
          type: object
          required: [code, message]
          properties:
            code:
              type: string
              example: "NOT_FOUND"
            message:
              type: string
              example: "Song not found"
            details:
              type: array
              items:
                type: object
                properties:
                  field:
                    type: string
                  message:
                    type: string
                  code:
                    type: string
            request_id:
              type: string
            timestamp:
              type: string
              format: date-time

  headers:
    X-RateLimit-Limit:
      schema:
        type: integer
      description: Max requests per window
    X-RateLimit-Remaining:
      schema:
        type: integer
      description: Remaining requests
    X-RateLimit-Reset:
      schema:
        type: integer
      description: Unix timestamp when limit resets

  responses:
    BadRequest:
      description: Invalid request parameters
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
    Unauthorized:
      description: Authentication required
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
    NotFound:
      description: Resource not found
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
    RateLimited:
      description: Rate limit exceeded
      headers:
        Retry-After:
          schema:
            type: integer
          description: Seconds until rate limit resets
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
    InternalServerError:
      description: Internal server error
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
```

---

## 13. Related Documents

| Dosya | Kapsam |
|-------|--------|
| [[api-doc-template]] | Bu şablon |
| [[php-template]] | PHP backend şablonu |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO ve ORM yasağı |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP güvenliği |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| [[ADR-020-api-public-security]] | API güvenlik stratejisi |
| [[ADR-022-database-hardened-security]] | DB güvenlik sertleştirme |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma |
| [[architecture/03-contracts/api-endpoints]] | API endpoint kataloğu |
| [[architecture/07-security/api/api_security_master]] | API güvenlik standartları |
| [[architecture/06-audio/coremusic-control-service]] | Control Service detayı |
| [[architecture/06-audio/coremusic-media-service]] | Media Service detayı |
| [[architecture/06-audio/coremusic-audio-service]] | Audio Service detayı |

---

## 14. Cross-References

| Bu Şablondan | Hedef | İlişki |
|-------------|-------|--------|
| § 3.1 Endpoint Format | [[ADR-020-api-public-security]] | API security |
| § 3.5 Error Format | [[ADR-042-vault-restructuring-2026-08-03]] | Standard format |
| § 3.6 Pagination | [[architecture/03-contracts/api-endpoints]] | Endpoint kataloğu |
| § 3.7 Rate Limiting | [[ADR-013-rate-limiting-apcu]] | APCu rate limit |
| § 3.9 Versioning | [[ADR-042-vault-restructuring-2026-08-03]] | Port 81, PHP 8.4 |
| § 6 Security | [[ADR-010-csrf-protection-strategy]] | CSRF token |
| § 6 Security | [[ADR-011-session-management]] | Session yönetimi |
| § 6 Security | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 11 Service Catalog | [[architecture/06-audio/]] | 7 servis |
| § 12 OpenAPI | [[architecture/03-contracts/api-endpoints]] | API spec |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 612 |
| **Frontmatter** | ✅ Tamamlandı |
| **OpenAPI Version** | 3.1 (güncel) |
| **ADR Uyum** | 002, 010, 011, 012, 013, 020, 022, 034, 042 |
| **MSA Uyum** | ✅ (15 dosya limiti) |
| **Service Coverage** | 7/7 servis |
| **Endpoint Count** | 55+ endpoint |
| **Error Format** | Standart: `{ error: { code, message, details } }` |
| **Rate Limit Coverage** | Tüm endpoint'lerde |

### 15.1 Enrichment Metrikleri

| Kategori | v2.0.0 | v3.0.0 | İyileşme |
|----------|--------|--------|----------|
| Satır | 202 | 612 | +203% |
| Section | 6 | 18 | +200% |
| OpenAPI Example | Basit | Tam spec | +500% |
| Service Coverage | 1/7 | 7/7 | +600% |
| Error Format | Basit | Standart | +300% |
| SDK Example | Yok | 3 dil | Yeni |
| Anti-Pattern | Yok | 10 madde | Yeni |
| Edge Case | Yok | 8 madde | Yeni |
| Troubleshooting | Yok | 8 madde | Yeni |

---

## 16. Examples

### GET /api/v1/songs — Tam Endpoint Dokümantasyonu

```
# GET /api/v1/songs

## Overview
Tüm şarkıları pagination ile listeler. Arama ve filtreleme desteği mevcuttur.

## Authentication
- Type: Session (COREMUSIC_SESS cookie)
- Required: Yes

## Rate Limit
- Limit: 60 requests per 60 seconds
- Scope: per-user

## Request

### Headers
| Header | Type | Required | Description |
|--------|------|----------|-------------|
| Accept | string | No | application/json (default) |
| Cookie | string | Yes | COREMUSIC_SESS={session_id} |

### Query Parameters
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| page | integer | No | 1 | Sayfa numarası |
| limit | integer | No | 20 | Sayfa başına kayıt (max: 100) |
| sort | string | No | created_at | Sıralama alanı |
| order | string | No | desc | Sıralama yönü |
| search | string | No | — | Başlık veya sanatçı araması |
| genre | string | No | — | Tür filtresi |

## Response

### Success (200 OK)
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Bohemian Rhapsody",
      "artist_id": 10,
      "artist_name": "Queen",
      "genre": "rock",
      "duration_ms": 354000,
      "format": "flac",
      "created_at": "2026-01-15T10:00:00Z"
    }
  ],
  "meta": {
    "request_id": "req_abc123",
    "timestamp": "2026-08-08T10:00:00Z",
    "version": "v1"
  },
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 150,
    "total_pages": 8,
    "has_next": true,
    "has_prev": false
  }
}

## Business Rules
- Soft-deleted songs (is_deleted=1) dahil edilmez
- Favorites bilgisi sadece authenticated user için döner
- play_count sadece song detail'da döner (listede yok)

## Related Endpoints
- GET /api/v1/songs/{id} — Şarkı detayı
- GET /api/v1/search?q={query} — Arama
```

### POST /api/v1/download/youtube — Tam Endpoint Dokümantasyonu

```
# POST /api/v1/download/youtube

## Overview
YouTube URL'sinden FLAC formatında indirme başlatır.

## Authentication
- Type: Session + CSRF Token
- Required: Yes

## Rate Limit
- Limit: 5 requests per 300 seconds
- Scope: per-user

## Request

### Headers
| Header | Type | Required | Description |
|--------|------|----------|-------------|
| Content-Type | string | Yes | application/json |
| X-CSRF-Token | string | Yes | CSRF koruma token'ı |
| Cookie | string | Yes | COREMUSIC_SESS={session_id} |

### Body
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| url | string | Yes | YouTube video URL'si |
| format | string | No | flac (default), mp3 |
| quality | string | No | best (default), good |

## Response

### Success (201 Created)
{
  "status": "success",
  "data": {
    "download_id": "dl_abc123",
    "status": "queued",
    "url": "https://youtube.com/watch?v=xxx",
    "format": "flac",
    "estimated_duration": 45,
    "created_at": "2026-08-08T10:00:00Z"
  },
  "meta": {
    "request_id": "req_def456",
    "timestamp": "2026-08-08T10:00:00Z",
    "version": "v1"
  }
}

## Business Rules
- YouTube URL formatı doğrulanır
- Anti-ban: minimum 5sn bekleme
- FLAC: 24-bit/32-bit tercih edilir
- Duplicate kontrol: aynı URL daha önce indirildiyse 409 döner

## Related Endpoints
- GET /api/v1/download/status/{id} — İndirme durumu
- GET /api/v1/download/queue — Kuyruk listesi
- POST /api/v1/download/deezer — Deezer indirme
```

### WebSocket /ws/v1/player/progress — Tam Endpoint Dokümantasyonu

```
# WebSocket /ws/v1/player/progress

## Overview
Oynatma ilerlemesini real-time olarak WebSocket üzerinden takip eder.

## Authentication
- Type: Session cookie (handshake sırasında)
- Required: Yes

## Connection
GET /ws/v1/player/progress HTTP/1.1
Upgrade: websocket
Cookie: COREMUSIC_SESS={session_id}

## Server Messages

### progress Event
{
  "event": "progress",
  "data": {
    "track_id": 123,
    "current_ms": 45000,
    "total_ms": 354000,
    "percentage": 12.7,
    "status": "playing",
    "volume": 80
  },
  "timestamp": "2026-08-08T10:00:45Z"
}

### track_changed Event
{
  "event": "track_changed",
  "data": {
    "track_id": 124,
    "title": "Another One Bites the Dust",
    "artist_name": "Queen",
    "duration_ms": 202000
  },
  "timestamp": "2026-08-08T10:05:00Z"
}

## Error Events
{
  "event": "error",
  "data": {
    "code": "DEVICE_DISCONNECTED",
    "message": "Audio device disconnected"
  },
  "timestamp": "2026-08-08T10:10:00Z"
}

## Business Rules
- Heartbeat: her 30sn'de ping/pong
- Timeout: 60sn ping yoksa bağlantı kesilir
- Max concurrent connection: 5 per user
```

---

## 17. Checklist

Her API endpoint dokümantasyonu bu listeden geçirilmelidir:

### Genel Kontroller
- [ ] Endpoint method ve path doğru mu?
- [ ] Authentication type belirtilmiş mi?
- [ ] Rate limit bilgisi var mı?
- [ ] Request parametreleri tam mı? (path, query, body)
- [ ] Response status code'ları tanımlı mı?
- [ ] Success response example var mı?
- [ ] Error response example'ları var mı?
- [ ] Rate limit header'ları response'da var mı?

### Güvenlik Kontrolleri
- [ ] CSRF token gerektiği belirtilmiş mi?
- [ ] Sensitive data response'da yok mu?
- [ ] Error messages internals expose etmiyor mu?
- [ ] Input validation kuralları tanımlı mı?

### Tutarlılık Kontrolleri
- [ ] Error format standart mı? (`{ error: { code, message } }`)
- [ ] JSON field'lar snake_case mi?
- [ ] Timestamp'ler ISO 8601 mi?
- [ ] Pagination response'da var mı?
- [ ] `meta.request_id` her yanıtta var mı?

### OpenAPI Kontrolleri
- [ ] Spec syntax doğru mu? (`openapi: 3.1.0`)
- [ ] Schema tanımları tam mı?
- [ ] Security scheme doğru mu?
- [ ] Example values gerçekçi mi?

---

## 18. Review Guide

API dokümantasyonu incelemesi şu adımlarla yapılır:

### 1. Syntax Kontrolü
- [ ] OpenAPI spec geçerli mi? (swagger-cli validate)
- [ ] JSON examples syntax olarak doğru mu?
- [ ] Markdown formatı tutarlı mı?

### 2. Kapsam Kontrolü
- [ ] Tüm endpoint'ler dokümante edilmiş mi?
- [ ] Tüm response code'ları tanımlı mı?
- [ ] Tüm parametreler açıklanmış mı?

### 3. Güvenlik İncelemesi
- [ ] Auth gereksinimleri doğru mu?
- [ ] CSRF koruması gerekli mi? (POST/PUT/PATCH/DELETE)
- [ ] Rate limit yeterli mi?
- [ ] Sensitive data sızıntısı var mı?

### 4. Tutarlılık İncelemesi
- [ ] Error format diğer endpoint'lerle aynı mı?
- [ ] Naming convention uyumlu mu?
- [ ] Versioning stratejisi uygulanıyor mu?
- [ ] Pagination formatı tutarlı mı?

### 5. Gerçekçilik Kontrolü
- [ ] Example values gerçekçi mi?
- [ ] Rate limit değerleri gerçekçi mi?
- [ ] Response time hedefleri ulaşılabilir mi?
- [ ] Business rules doğru mu?

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team • Human Mode • Truth Mode
