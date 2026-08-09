---
type: architecture
category: contracts
title: "API Design Rules — Enterprise Standards"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Design Rules

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic API tasarım kurallarını, standartlarını ve zorunluluklarını tanımlayan **Tasarım Rehberi**dir. Her API endpoint'i bu kurallara uymak zorundadır.

## 2. Contract First (Zorunlu)

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

**Hiçbir zaman kod sözleşmeden önce yazılmaz.**

| Adım | Sıra | Zorunlu mu? | Çıktı |
|------|------|-------------|-------|
| 1 | OpenAPI Spec yaz | ✅ | `openapi.yaml` |
| 2 | DTO'ları oluştur | ✅ | `*Request.php`, `*Response.php` |
| 3 | Contract'ı tanımla | ✅ | `*Interface.php` |
| 4 | Validation kurallarını yaz | ✅ | `*Validator.php` |
| 5 | Use Case'i tasarla | ✅ | `*UseCase.php` |
| 6 | Test senaryolarını yaz | ✅ | `*Test.php` |
| 7 | Kodu implemente et | ✅ | `*Handler.php` |

## 3. URL Yapısı

### 3.1 Kurallar

| Kural | Doğru | Yanlış |
|-------|-------|--------|
| lowercase | `/api/v1/songs` | `/api/v1/Songs` |
| plural nouns | `/api/v1/songs` | `/api/v1/song` |
| kebab-case | `/api/v1/songs/{id}/cover-art` | `/api/v1/songs/{id}/coverArt` |
| no verbs | `/api/v1/songs` (GET) | `/api/v1/getSongs` |
| nested max 2 | `/api/v1/songs/{id}/albums` | `/api/v1/songs/{id}/albums/{id}/tracks/{id}` |
| trailing slash yok | `/api/v1/songs` | `/api/v1/songs/` |

### 3.2 URL Kalıpları

```
# Collection
GET    /api/v1/songs              → Listele
POST   /api/v1/songs              → Oluştur

# Resource
GET    /api/v1/songs/{id}         → Detayı al
PUT    /api/v1/songs/{id}         → Güncelle (tam)
PATCH  /api/v1/songs/{id}         → Kısmi güncelleme
DELETE /api/v1/songs/{id}         → Sil

# Nested Resource
GET    /api/v1/songs/{id}/albums  → İlişkili albümler
POST   /api/v1/songs/{id}/albums  → İlişkili albüm ekle

# Actions (POST ile)
POST   /api/v1/songs/{id}/play    → Eylem başlat
POST   /api/v1/songs/{id}/like    → Eylem gerçekleştir

# Bulk Operations
POST   /api/v1/songs/bulk-delete  → Toplu silme
POST   /api/v1/songs/bulk-update  → Toplu güncelleme
```

## 4. HTTP Method Kuralları

| Method | Kullanım | Idempotent | Body | Cache |
|--------|----------|------------|------|-------|
| `GET` | Kaynak oku | ✅ Evet | ❌ Hayır | ✅ Evet |
| `POST` | Kaynak oluştur / eylem | ❌ Hayır | ✅ Evet | ❌ Hayır |
| `PUT` | Tam güncelleme | ✅ Evet | ✅ Evet | ❌ Hayır |
| `PATCH` | Kısmi güncelleme | ⚠️ Conditionally | ✅ Evet | ❌ Hayır |
| `DELETE` | Kaynak sil | ✅ Evet | ⚠️ Opsiyonel | ❌ Hayır |

## 5. Request/Response Formatı

### 5.1 Standart Response

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

### 5.2 Hata Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Geçersiz giriş",
    "details": [
      {
        "field": "email",
        "rule": "valid_email",
        "message": "Geçerli bir email adresi giriniz"
      }
    ]
  },
  "meta": {
    "timestamp": "2026-08-09T12:00:00Z",
    "request_id": "req-abc-124"
  }
}
```

### 5.3 Sayfalama Response

```json
{
  "success": true,
  "data": {
    "items": [],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total_items": 1000,
      "total_pages": 50,
      "has_next": true,
      "has_previous": false
    }
  }
}
```

## 6. Header Standartları

### 6.1 Request Headers

| Header | Zorunlu mu? | Açıklama | Örnek |
|--------|-------------|----------|-------|
| `Authorization` | ✅ (auth gerektiren) | Bearer token | `Bearer eyJhbGc...` |
| `X-API-Key` | ⚠️ (service-to-service) | API anahtarı | `sk-coremusic-xxxx` |
| `X-CSRF-Token` | ✅ (POST/PUT/DELETE) | CSRF koruması | `csrf_token=xxx` |
| `X-Request-ID` | ⚠️ (opsiyonel) | İstek takibi | `req-abc-123` |
| `Accept` | ⚠️ (opsiyonel) | Accept format | `application/json` |
| `Content-Type` | ✅ (body varsa) | İçerik tipi | `application/json` |
| `X-BFF-Type` | ⚠️ (BFF seçimi) | BFF tipi | `spa`, `mobile`, `embedded` |

### 6.2 Response Headers

| Header | Zorunlu mu? | Açıklama | Örnek |
|--------|-------------|----------|-------|
| `X-Request-ID` | ✅ | İstek takibi | `req-abc-123` |
| `X-RateLimit-Limit` | ✅ | Maks istek | `60` |
| `X-RateLimit-Remaining` | ✅ | Kalan istek | `45` |
| `X-RateLimit-Reset` | ✅ | Reset zamanı | `1691500000` |
| `X-Response-Time` | ⚠️ | Yanıt süresi | `45ms` |
| `X-Powered-By` | ❌ Yasak | Güvenlik | — |
| `Server` | ❌ Yasak | Güvenlik | — |

## 7. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| **URL** | kebab-case | `/api/v1/songs/{id}/cover-art` |
| **JSON Key** | snake_case | `created_at`, `user_id` |
| **PHP Class** | PascalCase | `SongRepository`, `CreateSongUseCase` |
| **PHP Method** | camelCase | `findById()`, `createSong()` |
| **PHP Constant** | UPPER_SNAKE | `MAX_RETRY_COUNT` |
| **CSS Class** | BEM | `song-card__title--active` |
| **JS Variable** | camelCase | `songTitle`, `isActive` |
| **DB Column** | snake_case | `created_at`, `user_id` |
| **DB Table** | snake_case plural | `songs`, `user_roles` |
| **Enum** | PascalCase | `SongStatus::Published` |
| **Error Code** | UPPER_SNAKE | `VALIDATION_ERROR`, `SONG_NOT_FOUND` |

## 8. HTTP Status Code Kuralları

| Code | Kullanım | Body var mı? |
|------|----------|-------------|
| `200` | Başarılı GET/PUT/PATCH | ✅ |
| `201` | Başarılı POST (oluşturma) | ✅ |
| `204` | Başarılı DELETE (içerik yok) | ❌ |
| `302` | Yönlendirme (login sonrası) | — |
| `400` | Kullanıcı hatası (validation) | ✅ |
| `401` | Kimlik doğrulama hatası | ✅ |
| `403` | Yetki hatası | ✅ |
| `404` | Kaynak bulunamadı | ✅ |
| `405` | Method izni yok | ✅ |
| `409` | Çelişki (duplicate) | ✅ |
| `422` | İşlem hatası (unprocessable) | ✅ |
| `429` | Rate limit aşıldı | ✅ |
| `500` | Sunucu hatası | ⚠️ prod'da minimal |
| `502` | Gateway hatası | ✅ |
| `503` | Bakım modu | ✅ |

## 9. Güvenlik Kuralları

| Kural | Uygulama |
|-------|----------|
| HTTPS zorunlu (prod) | HTTP → HTTPS redirect |
| HSTS header | `max-age=31536000; includeSubDomains` |
| CORS whitelist | Sadece tanımlı domain'ler |
| Rate limit | Her endpoint için |
| Input validation | Server-side, whitelist |
| Output encoding | JSON response, no HTML |
| No sensitive data in URL | Token body'de, URL'de değil |
| No sensitive data in logs | `[REDACTED]` maskesi |
| Request size limit | Max 10MB body |
| Timeout | Max 30s request |

## 10. Versioning Kuralları

| Kural | Değer |
|-------|-------|
| URL versioning | `/api/v1/`, `/api/v2/` |
| Breaking change → yeni version | Field silme, rename |
| Non-breaking change → mevcut version | Yeni field ekleme |
| Deprecated version | Minimum 6 ay destek |
| Version header | `X-API-Version: 1.0.0` |

*Detay: [[api-versioning]]*

## 11. Error Code Formatı

```
{SERVICE}_{RESOURCE}_{ERROR_TYPE}

Örnekler:
AUTH_USER_NOT_FOUND
MUSIC_SONG_NOT_FOUND
MEDIA_FILE_TOO_LARGE
DOWNLOAD_QUEUE_FULL
AUDIO_DEVICE_UNAVAILABLE
```

*Detay: [[api-error-codes]]*

## 12. Idempotency

| Method | Idempotent mi? | Nasıl? |
|--------|----------------|--------|
| `GET` | ✅ Evet | Doğal olarak |
| `PUT` | ✅ Evet | Aynı body → aynı sonuç |
| `DELETE` | ✅ Evet | Zaten silinmiş → 204 |
| `POST` | ❌ Hayır | `Idempotency-Key` header ile |
| `PATCH` | ⚠️ Conditionally | Operation sequence ile |

*Detay: [[api-idempotency]]*

## 13. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Contract First: OpenAPI önce | Kod revert edilir |
| 2 | Response format standart | Tutarlılık bozulması |
| 3 | Error code standardı | Debug zorluğu |
| 4 | snake_case JSON key | Uyumsuzluk |
| 5 | No verb in URL | REST ihlali |
| 6 | Plural nouns | Tutarlılık |
| 7 | HTTPS prod'da zorunlu | Güvenlik açığı |
| 8 | Rate limit her endpoint'te | Abuse riski |
| 9 | No sensitive data in logs | Veri sızıntısı |
| 10 | Versioning zorunlu | Breaking change riski |

## 14. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[api-architecture-master]] | Ana mimari |
| [[api-versioning]] | Sürüm yönetimi |
| [[api-error-codes]] | Hata kodları |
| [[api-security]] | Güvenlik |
| [[api-validation]] | Doğrulama |
| [[api-idempotency]] | İdempotency |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | ~250 |
| **ADR Uyumlu** | ✅ 001, 002, 042, 051 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
