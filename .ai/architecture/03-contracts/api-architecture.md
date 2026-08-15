---
title: "CoreMusic — API Architecture"
type: architecture
category: api
updated: 2026-08-12
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — API Architecture

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[ADR-039-7-service-platform-architecture]] · [[ADR-051-platform-rewrite-from-scratch]] · [[ADR-053-enterprise-router-architecture]]

---

## 1. Amaç

CoreMusic ekosisteminin API mimarisini tanımlar. API Gateway, API-First yaklaşımı, BFF pattern, standart response formatı ve Internal/Public API ayrımını kapsar.

---

## 2. API Gateway

### 2.1 Gateway Konumu

**API Gateway: `api.coremusic.net`**

Tüm istemciler tek giriş noktasından sisteme bağlanır. Gateway, auth offload, rate limit ve request validation işlemlerini merkezi olarak yönetir.

### 2.2 Gateway Mimarisi

```
                        INTERNET
                            │
                            ▼
                    ┌───────────────┐
                    │  API GATEWAY  │
                    │ api.coremusic │
                    │     .net      │
                    └───────┬───────┘
                            │
         ┌──────────────────┼──────────────────┐
         │                  │                  │
         ▼                  ▼                  ▼
   Public API          Internal API        WebSocket API
         │                  │                  │
         └──────────────────┼──────────────────┘
                            │
              ┌─────────────┼─────────────────┐
              │             │                 │
              ▼             ▼                 ▼
         Auth API      Media API        Audio API
         User API      Music API        Player API
         Admin API     Download API     DSP API
         Search API    Library API      Notification API
         AI API        Analytics API    System API
```

### 2.3 Gateway Sorumlulukları

| Görev | Açıklama | Katman |
|-------|----------|--------|
| **Routing** | İsteği doğru servise yönlendirme | L2 |
| **Authentication** | JWT/Session doğrulama (auth.coremusic.net) | L1 |
| **Authorization** | RBAC + Permission kontrolü | L1 |
| **Rate Limiting** | IP/User bazlı hız kısıtlaması | L1 |
| **Request Validation** | Body, Query, Header doğrulama | L1 |
| **Response Normalization** | Standart response formatı | L2 |
| **Versioning** | API sürüm yönetimi (/v1/, /v2/) | L2 |
| **Correlation ID** | İstek takibi | L0 |
| **Service Discovery** | Servis bulma ve yönlendirme | L0 |
| **Audit Log** | Tüm isteklerin loglanması | L0 |

---

## 3. API-First (Contract First)

> **Hiçbir zaman kod sözleşmeden önce yazılmaz.**

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

| Sıra | Adım | Zorunlu mu? |
|------|------|-------------|
| 1 | OpenAPI Spec yaz | ✅ |
| 2 | DTO'ları oluştur | ✅ |
| 3 | Contract'ı tanımla (Interface) | ✅ |
| 4 | Validation kurallarını yaz | ✅ |
| 5 | Use Case'i tasarla | ✅ |
| 6 | Test senaryolarını yaz | ✅ |
| 7 | Kodu implemente et | ✅ |

---

## 4. Internal + Public API Ayrımı

### 4.1 Public API

| Kullanım | Açıklama |
|----------|----------|
| SPA Browser | music.coremusic.net JS router'ı |
| Mobile uygulamalar | iOS/Android uygulamaları |
| Masaüstü uygulamaları | Windows/macOS uygulamaları |
| Üçüncü taraf geliştiriciler | Dış API tüketimi |
| SDK'lar | Resmi API client'ları |

### 4.2 Internal API

| Kullanım | Açıklama |
|----------|----------|
| SPA Router | CoreMusic panelleri |
| Background Workers | Queue consumer'lar |
| Cron Jobs | Zamanlanmış görevler |
| Download Worker | İndirme işlemleri |
| Media Processor | FFmpeg encode/decode |
| Auth Service | Servisler arası auth |
| Audio Service (C++ IPC) | Neva Engine iletişimi |
| WebSocket Handlers | Gerçek zamanlı iletişim |
| CLI Commands | Yönetim komutları |

### 4.3 API Tipi Ayrımı

```
┌─────────────────────────────────────────────────────────────┐
│                     PUBLIC API                               │
│  ├── SPA Browser (music, admin, home, pro, studio, car)    │
│  ├── Mobile uygulamaları                                    │
│  ├── Masaüstü uygulamaları                                  │
│  ├── Üçüncü taraf geliştiriciler                            │
│  └── SDK'lar                                                │
├─────────────────────────────────────────────────────────────┤
│                     INTERNAL API                             │
│  ├── SPA Router (coremusic paneller)                        │
│  ├── Background Workers                                     │
│  ├── Queue Consumers                                        │
│  ├── Download Worker                                        │
│  ├── Media Processor                                        │
│  ├── Auth Service (inter-service)                           │
│  └── CLI Commands                                           │
├─────────────────────────────────────────────────────────────┤
│                     ADMIN API                                │
│  ├── Kullanıcı yönetimi                                     │
│  ├── İçerik yönetimi                                        │
│  ├── Sistem konfigürasyonu                                  │
│  └── Audit Trail                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. BFF (Backend for Frontend)

### 5.1 BFF Neden Gerekli?

Her istemci tipinin farklı ihtiyaçları vardır:
- **SPA:** Tam veri, tüm alanlar
- **Mobile:** Minimal JSON, sadece gerekli alanlar
- **Embedded (RPi5):** Ultra-minimal, gzip zorunlu
- **Desktop:** Orta boy, gelişmiş metadata

### 5.2 BFF Yapısı

```
┌─────────────────────────────────────────────────────────────┐
│                    BFF LAYER                                  │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  SPA BFF                                                     │
│  ├── Tam response body                                       │
│  ├── Tüm alanlar dahil                                       │
│  ├── Pagination detaylı                                      │
│  └── WebSocket desteği                                       │
│                                                              │
│  Mobile BFF                                                  │
│  ├── Minimal response body                                   │
│  ├── Sadece gerekli alanlar                                  │
│  ├── Daha küçük sayfa boyutları                              │
│  └── Offline cache headers                                   │
│                                                              │
│  Embedded BFF (RPi5)                                         │
│  ├── Ultra-minimal JSON                                      │
│  ├── Sadece ID + Name + Status                               │
│  ├── Düşük bandwidth optimizasyonu                           │
│  └── gzip zorunlu                                            │
│                                                              │
│  Desktop BFF                                                 │
│  ├── Orta boy response                                       │
│  ├── Gelişmiş metadata                                       │
│  ├── Batch endpoint desteği                                  │
│  └── WebSocket + SSE desteği                                 │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 5.3 BFF Implementasyonu

```
Client
    │
    ▼
BFF Layer (Router'da eşleme)
    │
    ├── SPA → /api/v1/spa/songs?fields=id,title,artist,album,duration,cover_url
    ├── Mobile → /api/v1/mobile/songs?fields=id,title,artist,duration
    ├── Embedded → /api/v1/embedded/songs?fields=id,title
    └── Desktop → /api/v1/desktop/songs?fields=id,title,artist,album,duration,cover_url,bitrate,format
    │
    ▼
Internal API (tam veri)
    │
    ▼
Service Layer
```

---

## 6. Standart Response Format

### 6.1 Başarılı Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Bohemian Rhapsody",
    "artist": "Queen"
  },
  "meta": {
    "timestamp": "2026-08-12T12:00:00Z",
    "request_id": "req-abc-123",
    "service": "music-api",
    "version": "1.0.0"
  }
}
```

### 6.2 List Response (Pagination)

```json
{
  "success": true,
  "data": [...],
  "meta": {
    "total": 150,
    "page": 1,
    "per_page": 20,
    "total_pages": 8,
    "has_next": true,
    "has_prev": false,
    "timestamp": "2026-08-12T12:00:00Z",
    "request_id": "req-abc-456"
  }
}
```

### 6.3 Hata Response

```json
{
  "success": false,
  "error": {
    "code": "MUSIC_SONG_NOT_FOUND",
    "message": "Song not found",
    "details": []
  },
  "meta": {
    "timestamp": "2026-08-12T12:00:00Z",
    "request_id": "req-abc-789"
  }
}
```

### 6.4 Hata Kodu Formatı

Format: `{SERVICE}_{RESOURCE}_{ERROR_TYPE}`

| HTTP | Code | Açıklama |
|------|------|----------|
| 400 | INVALID_REQUEST | Geçersiz istek |
| 401 | UNAUTHORIZED | Kimlik doğrulama başarısız |
| 403 | FORBIDDEN | Yetki yok |
| 404 | NOT_FOUND | Kaynak bulunamadı |
| 409 | CONFLICT | Çakışma |
| 429 | RATE_LIMITED | Rate limit aşıldı |
| 500 | INTERNAL_ERROR | Sunucu hatası |

---

## 7. API Endpoints

| Servis | Subdomain | Port | Protocol | Auth |
|--------|-----------|------|----------|------|
| Control Service | music.coremusic.net | 81 | HTTP | Session/JWT |
| Media Service | media.coremusic.net | 5000/6000 | HTTP | JWT |
| Download Service | download.coremusic.net | 3001 | HTTP/WS | JWT |
| Audio Service | — | 9741/9742 | REST/WS | JWT |
| API Gateway | api.coremusic.net | 81 | HTTP | JWT |

---

## 8. API Standards

| Kural | Değer |
|-------|-------|
| Format | JSON |
| Versioning | URL (/v1/, /v2/) |
| Pagination | Cursor-based veya Page-based |
| Rate Limit | 60 req/60s |
| Timeout | 30s |
| Contract | OpenAPI 3.1 |

---

## 9. URL Kuralları

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| lowercase | `/api/v1/songs` | `/api/v1/Songs` |
| plural nouns | `/api/v1/songs` | `/api/v1/song` |
| kebab-case | `/api/v1/songs/{id}/cover-art` | `/api/v1/songs/{id}/coverArt` |
| no verbs | `GET /api/v1/songs` | `POST /api/v1/getSongs` |
| nested max 2 | `/api/v1/songs/{id}/albums` | `/api/v1/songs/{id}/albums/{id}/tracks/{id}` |

---

## 10. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| URL | kebab-case | `/api/v1/songs/{id}/cover-art` |
| JSON Key | snake_case | `created_at`, `user_id` |
| PHP Class | PascalCase | `SongRepository` |
| PHP Method | camelCase | `findById()` |
| DB Column | snake_case | `created_at` |
| Error Code | UPPER_SNAKE | `SONG_NOT_FOUND` |

---

## 11. CORS Configuration

| Setting | Değer |
|---------|-------|
| Origins | Whitelist |
| Methods | GET, POST, PUT, DELETE, PATCH |
| Headers | Authorization, Content-Type, X-CSRF-Token |
| Credentials | true |

---

## 12. Rate Limiting

| Endpoint | Limit | Pencere |
|----------|-------|---------|
| Login | 5 | 60s |
| Register | 3 | 300s |
| Password Reset | 3 | 300s |
| MFA Attempt | 5 | 300s |
| Token Refresh | 10 | 60s |
| API (auth required) | 120 | 60s |
| Default | 60 | 60s |

---

## 13. Pagination

| Parametre | Varsayılan | Maksimum | Açıklama |
|-----------|-----------|----------|----------|
| `page` | 1 | — | Sayfa numarası |
| `per_page` | 20 | 100 | Sayfa başına kayıt |
| `cursor` | — | — | Cursor-based pagination (alternatif) |

---

## 14. Filtering & Sorting

| Parametre | Format | Örnek |
|-----------|--------|-------|
| `filter[field]` | `filter[genre]=Rock` | `?filter[genre]=Rock&filter[year]=2024` |
| `sort` | `sort=field` veya `sort=-field` | `?sort=-created_at` (azalan) |
| `search` | `search=term` | `?search=bohemian` |

---

## 15. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Gateway | [[ADR-051-platform-rewrite-from-scratch]] | Platform yapısı |
| § 3 Contract First | [[architecture/03-contracts/api-design-rules]] | Tasarım kuralları |
| § 5 BFF | [[architecture/03-contracts/api-architecture-master]] | BFF detayı |
| § 6 Response | [[architecture/03-contracts/api-design-rules]] | Response formatı |
| § 9 URL | [[architecture/03-contracts/api-design-rules]] | URL kuralları |
| § 12 Rate | [[architecture/03-contracts/api-rate-limit]] | Rate limit detayı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode
