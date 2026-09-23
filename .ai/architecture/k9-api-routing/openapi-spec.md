---
title: "OpenAPI 3.0 Specification"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# OpenAPI 3.0 Specification

## Genel Bakış

OpenAPI 3.0 specification, COREMUSIC REST API'lerinin standart, machine-readable format'ta tanımını sağlar. Auto-generated client SDK'ler, documentation ve contract testing bu spec üzerinden yapılır. Spec-driven development yaklaşımı ile önce spec yazılır, sonra implementasyon yapılır.

Her endpoint için request/response schema'ları, authentication gereksinimleri, error codes ve example'lar tanımlanır. Versioning strategy ile backward compatibility guarantee edilir.

## API Tanımı

### Specification Files

| Dosya | Version | Açıklama |
|-------|---------|----------|
| `openapi-user.yaml` | v1.0 | User Service API |
| `openapi-track.yaml` | v1.0 | Track Service API |
| `openapi-playlist.yaml` | v1.0 | Playlist Service API |
| `openapi-ai.yaml` | v1.0 | AI Service API |
| `openapi-audio.yaml` | v1.0 | Audio Stream API |
| `openapi-billing.yaml` | v1.0 | Billing Service API |

## Teknik Detaylar

### Base OpenAPI Spec

```yaml
# openapi-coremusic.yaml
openapi: 3.0.3
info:
  title: COREMUSIC API
  description: |
    COREMUSIC Hi-Fi Audio System API
    Tüm servisler için unified REST API specification.
  version: 1.0.0
  contact:
    name: COREMUSIC API Team
    email: api@coremusic.local
  license:
    name: MIT
    url: https://opensource.org/licenses/MIT

servers:
  - url: https://api.coremusic.com/v1
    description: Production
  - url: https://staging-api.coremusic.com/v1
    description: Staging
  - url: http://localhost:8080/v1
    description: Development

security:
  - BearerAuth: []
  - ApiKeyAuth: []

paths:
  /tracks:
    get:
      operationId: listTracks
      summary: Şarkı listesini getir
      tags: [Tracks]
      parameters:
        - $ref: '#/components/parameters/PageParam'
        - $ref: '#/components/parameters/PageSizeParam'
        - name: sort
          in: query
          schema:
            type: string
            enum: [title, artist, album, duration, playCount, createdAt]
            default: createdAt
        - name: order
          in: query
          schema:
            type: string
            enum: [asc, desc]
            default: desc
        - name: genre
          in: query
          schema:
            type: string
        - name: artist
          in: query
          schema:
            type: string
      responses:
        '200':
          description: Başarılı
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/PaginatedTrackList'
              example:
                items:
                  - id: "trk_123"
                    title: "Bohemian Rhapsody"
                    artist:
                      id: "art_456"
                      name: "Queen"
                    album:
                      id: "alb_789"
                      title: "A Night at the Opera"
                    duration: 354
                    genres: ["rock", "progressive rock"]
                    audioQuality:
                      format: "FLAC"
                      bitDepth: 24
                      sampleRate: 96000
                    artwork:
                      full: "https://cdn.coremusic.com/tracks/trk_123/full.jpg"
                      thumbnail: "https://cdn.coremusic.com/tracks/trk_123/thumb.jpg"
                total: 1500
                page: 1
                pageSize: 20
        '401':
          $ref: '#/components/responses/Unauthorized'
        '429':
          $ref: '#/components/responses/RateLimited'

  /tracks/{trackId}:
    get:
      operationId: getTrack
      summary: Şarkı detayını getir
      tags: [Tracks]
      parameters:
        - name: trackId
          in: path
          required: true
          schema:
            type: string
      responses:
        '200':
          description: Başarılı
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/TrackDetail'
        '404':
          $ref: '#/components/responses/NotFound'

  /playlists:
    get:
      operationId: listPlaylists
      summary: Kullanıcının playlist'lerini listele
      tags: [Playlists]
      parameters:
        - $ref: '#/components/parameters/PageParam'
        - $ref: '#/components/parameters/PageSizeParam'
      responses:
        '200':
          description: Başarılı
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/PaginatedPlaylistList'
    post:
      operationId: createPlaylist
      summary: Yeni playlist oluştur
      tags: [Playlists]
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CreatePlaylistRequest'
      responses:
        '201':
          description: Playlist oluşturuldu
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/Playlist'

  /playlists/{playlistId}/tracks:
    post:
      operationId: addTrackToPlaylist
      summary: Playlist'e şarkı ekle
      tags: [Playlists]
      parameters:
        - name: playlistId
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/AddTrackRequest'
      responses:
        '200':
          description: Şarkı eklendi
        '404':
          $ref: '#/components/responses/NotFound'

  /auth/login:
    post:
      operationId: login
      summary: Kullanıcı girişi
      tags: [Auth]
      security: []
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/LoginRequest'
      responses:
        '200':
          description: Giriş başarılı
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/AuthResponse'
        '401':
          description: Geçersizcredentials

  /ai/recommendations:
    get:
      operationId: getRecommendations
      summary: AI önerilerini getir
      tags: [AI]
      parameters:
        - name: limit
          in: query
          schema:
            type: integer
            default: 10
            maximum: 50
        - name: mood
          in: query
          schema:
            type: string
            enum: [energetic, calm, focus, workout, sleep]
        - name: seedTracks
          in: query
          schema:
            type: array
            items:
              type: string
          description: Öneri için referans şarkı ID'leri
      responses:
        '200':
          description: Öneriler
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/RecommendationsResponse'

components:
  securitySchemes:
    BearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
    ApiKeyAuth:
      type: apiKey
      in: header
      name: X-API-Key

  parameters:
    PageParam:
      name: page
      in: query
      schema:
        type: integer
        default: 1
        minimum: 1
    PageSizeParam:
      name: pageSize
      in: query
      schema:
        type: integer
        default: 20
        minimum: 1
        maximum: 100

  schemas:
    TrackDetail:
      type: object
      properties:
        id:
          type: string
        title:
          type: string
        artist:
          $ref: '#/components/schemas/ArtistSummary'
        album:
          $ref: '#/components/schemas/AlbumSummary'
        duration:
          type: integer
          description: Saniye cinsinden süre
        genres:
          type: array
          items:
            type: string
        audioQuality:
          $ref: '#/components/schemas/AudioQuality'
        artwork:
          $ref: '#/components/schemas/Artwork'
        analysis:
          $ref: '#/components/schemas/AudioAnalysis'
        lyrics:
          $ref: '#/components/schemas/Lyrics'

    ArtistSummary:
      type: object
      properties:
        id:
          type: string
        name:
          type: string
        imageUrl:
          type: string
          format: uri

    AlbumSummary:
      type: object
      properties:
        id:
          type: string
        title:
          type: string
        year:
          type: integer
        artworkUrl:
          type: string
          format: uri

    AudioQuality:
      type: object
      properties:
        format:
          type: string
          enum: [MP3, AAC, FLAC, ALAC, DSD, MQA]
        bitDepth:
          type: integer
          enum: [16, 24, 32]
        sampleRate:
          type: integer
          enum: [44100, 48000, 88200, 96000, 176400, 192000]
        bitrate:
          type: integer
          description: kbps cinsinden

    Artwork:
      type: object
      properties:
        full:
          type: string
          format: uri
        thumbnail:
          type: string
          format: uri

    AudioAnalysis:
      type: object
      properties:
        energy:
          type: number
          format: float
          minimum: 0
          maximum: 1
        danceability:
          type: number
          format: float
        acousticness:
          type: number
          format: float
        instrumentalness:
          type: number
          format: float
        tempo:
          type: number
          format: float
          description: BPM

    CreatePlaylistRequest:
      type: object
      required: [name]
      properties:
        name:
          type: string
          minLength: 1
          maxLength: 100
        description:
          type: string
          maxLength: 500
        isPublic:
          type: boolean
          default: false
        trackIds:
          type: array
          items:
            type: string

    LoginRequest:
      type: object
      required: [email, password]
      properties:
        email:
          type: string
          format: email
        password:
          type: string
          minLength: 8

    AuthResponse:
      type: object
      properties:
        accessToken:
          type: string
        refreshToken:
          type: string
        expiresIn:
          type: integer
          description: Saniye cinsinden
        tokenType:
          type: string
          enum: [Bearer]

    PaginatedTrackList:
      type: object
      properties:
        items:
          type: array
          items:
            $ref: '#/components/schemas/TrackDetail'
        total:
          type: integer
        page:
          type: integer
        pageSize:
          type: integer

    RecommendationsResponse:
      type: object
      properties:
        tracks:
          type: array
          items:
            $ref: '#/components/schemas/TrackDetail'
        reasons:
          type: array
          items:
            type: object
            properties:
              trackId:
                type: string
              reason:
                type: string
              confidence:
                type: number
                format: float

  responses:
    Unauthorized:
      description: Yetkilendirme hatası
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
          example:
            code: UNAUTHORIZED
            message: "Invalid or expired token"

    NotFound:
      description: Kaynak bulunamadı
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
          example:
            code: NOT_FOUND
            message: "Resource not found"

    RateLimited:
      description: Rate limit aşıldı
      headers:
        X-RateLimit-Remaining:
          schema:
            type: integer
        X-RateLimit-Reset:
          schema:
            type: integer
      content:
        application/json:
          schema:
            $ref: '#/components/schemas/Error'
          example:
            code: RATE_LIMITED
            message: "Too many requests"

    Error:
      type: object
      properties:
        code:
          type: string
        message:
          type: string
        details:
          type: object
```

## Konfigürasyon

```yaml
# openapi-config.yaml
openapi:
  spec_dir: "./specs"
  output_dir: "./generated"

  codegen:
    typescript:
      enabled: true
      output: "./src/generated/api-client"
      npm_package: "@coremusic/api-client"
    python:
      enabled: true
      output: "./sdk/python"
    go:
      enabled: false

  documentation:
    enabled: true
    output: "./docs/api"
    format: "redoc"  # redoc | swagger

  validation:
    request_validation: true
    response_validation: true  # development only
    strict_mode: false

  mock:
    enabled: true
    port: 8081
    latency_ms: 100
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K9 API Gateway**: Endpoint routing
- **K10 Services**: Domain logic

### Bağımlı Olan
- **K11 Web**: Generated API client
- **K12 Mobile**: Generated API client
- **K13 Desktop**: Generated API client
- **CI/CD**: Contract testing

## Durum: Implementasyon

- [x] OpenAPI base spec
- [x] Track endpoints
- [x] Playlist endpoints
- [x] Auth endpoints
- [x] User endpoints
- [x] AI endpoints
- [ ] Code generation (TypeScript)
- [ ] Code generation (Python)
- [ ] Code generation (Go)
- [ ] Contract testing
- [ ] Auto-documentation
- [ ] Mock server
- [ ] Spec validation middleware
