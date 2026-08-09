# API Standards — CoreMusic

**Authority:** ADR-001, ADR-002, ADR-042, ADR-051, ADR-053, ADR-054
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Contract First (Mandatory)

**Hiçbir zaman kod sözleşmeden önce yazılmaz.**

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

*Detay: [[architecture/03-contracts/api-design-rules]]*

## 2. API Gateway

Tüm istemciler tek giriş noktasından sisteme bağlanır:

```
Client → API Gateway → Middleware Pipeline → Service → Use Case → Domain → Repository → DB
```

**Gateway sorumlulukları:** Routing, Authentication, Authorization, Rate Limit, Request Validation, Response Normalization, Correlation ID, Audit Log

*Detay: [[architecture/03-contracts/api-architecture-master]]*

## 3. BFF Pattern

Her istemci tipi kendi DTO'sunu alır:

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri, tüm alanlar |
| Mobile | Mobile BFF | Minimal, sadece gerekli |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy, metadata |

*Detay: [[architecture/03-contracts/api-architecture-master]]*

## 4. CQRS

Okuma ve yazma tamamen ayrışık:

```php
// COMMAND (Yazma)
CreatePlaylistCommand → CreatePlaylistHandler → Repository → MySQL Master

// QUERY (Okuma)
GetPlaylistQuery → GetPlaylistHandler → Cache (Redis/APCu) → Response DTO
```

*Detay: [[architecture/03-contracts/api-architecture-master]]*

## 5. Event Driven

Servisler birbirini doğrudan çağırmaz:

```
Service A → Event Bus (PSR-14) → Service B
                            → Service C
                            → Service D
```

*Detay: [[architecture/03-contracts/api-event-system]]*

## 6. URL Kuralları

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| lowercase | `/api/v1/songs` | `/api/v1/Songs` |
| plural nouns | `/api/v1/songs` | `/api/v1/song` |
| kebab-case | `/api/v1/songs/{id}/cover-art` | `/api/v1/songs/{id}/coverArt` |
| no verbs | `/api/v1/songs` (GET) | `/api/v1/getSongs` |
| nested max 2 | `/api/v1/songs/{id}/albums` | `/api/v1/songs/{id}/albums/{id}/tracks/{id}` |

*Detay: [[architecture/03-contracts/api-design-rules]]*

## 7. Response Format

```json
{
  "success": true,
  "data": {},
  "meta": {
    "timestamp": "2026-08-09T12:00:00Z",
    "request_id": "req-abc-123",
    "service": "music-api",
    "version": "1.0.0"
  }
}
```

*Detay: [[architecture/03-contracts/api-design-rules]]*

## 8. Error Codes

Format: `{SERVICE}_{RESOURCE}_{ERROR_TYPE}`

```
AUTH_USER_NOT_FOUND
MUSIC_SONG_NOT_FOUND
MEDIA_FILE_TOO_LARGE
DOWNLOAD_QUEUE_FULL
RATE_LIMIT_EXCEEDED
```

*Detay: [[architecture/03-contracts/api-error-codes]]*

## 9. Versioning

- URL versioning: `/api/v1/`, `/api/v2/`
- Breaking change → yeni version
- Non-breaking change → mevcut version
- Deprecated version minimum 6 ay destek

*Detay: [[architecture/03-contracts/api-versioning]]*

## 10. Rate Limiting

| Endpoint | Limit |
|----------|-------|
| Login | 5/60s |
| Register | 3/300s |
| API (auth required) | 120/60s |
| Default | 60/60s |

*Detay: [[architecture/03-contracts/api-rate-limit]]*

## 11. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| URL | kebab-case | `/api/v1/songs/{id}/cover-art` |
| JSON Key | snake_case | `created_at`, `user_id` |
| PHP Class | PascalCase | `SongRepository` |
| PHP Method | camelCase | `findById()` |
| DB Column | snake_case | `created_at` |
| Error Code | UPPER_SNAKE | `SONG_NOT_FOUND` |

## 12. Modüler Shared Library

```
coremusic/contracts      ← DTO, Enums, ValueObjects
coremusic/http           ← HttpClient, ApiClient
coremusic/auth           ← Auth Client, JWT
coremusic/security       ← CSRF, RateLimiter
coremusic/cache          ← Cache Interface
coremusic/events         ← Event Dispatcher
coremusic/validation     ← Request Validation
coremusic/storage        ← Storage Interface
coremusic/logger         ← PSR-3 Logger
coremusic/monitoring     ← Metrics, Health Check
coremusic/websocket      ← WebSocket Client/Server
coremusic/sdk            ← Client SDK
coremusic/api-client     ← Typed API Client
```

*Detay: [[architecture/03-contracts/api-architecture-master]]*

## 13. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Kod öncesi yazma | Contract First |
| SPA → PDO | SPA → ApiClient → Gateway |
| Controller → Repository | Controller → Use Case → Repository |
| Tek monolitik shared | Modüler `coremusic/*` paketler |
| `SELECT *` | Explicit column list |
| ORM | PDO prepared statement |
| URL'de verb | REST resource |
| Hardcoded secrets | `.env` / credential vault |
| Tek API | Servis bazlı API |
| `POST /getSongs` | `GET /api/v1/songs` |

## 14. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Contract First: OpenAPI önce | Kod revert edilir |
| 2 | Response format standart | Tutarlılık bozulması |
| 3 | BFF Pattern | Gereksiz veri transferi |
| 4 | CQRS | Performans düşüklüğü |
| 5 | Event Driven | Bağımlılık artışı |
| 6 | Gateway: Tek giriş noktası | Güvenlik açığı |
| 7 | Rate limit her endpoint'te | Abuse riski |
| 8 | Versioning zorunlu | Breaking change riski |

---

*API Standards v1.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
