---
type: architecture
category: contracts
title: "API Public Contract — External API Standards"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Public Contract — External API Standards

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[api-design-rules]] · [[api-internal-contract]] · [[api-sdk]]

---

## 1. Amaç

CoreMusic dış dünyaya açılan (third-party, mobil uygulama, harici entegrasyonlar) API'lerinin standartlarını, güvenlik kurallarını ve sözleşme formatını tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**. Bu belge, public API'nin güvenli, tutarlı ve ölçeklenebilir olmasını garanti altına alır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Public API endpoint'leri | Internal service-to-service iletişimi |
| OAuth2 hazırlık | Session yönetimi (detay) |
| Rate limit planları | Veritabanı sorguları |
| API dokümantasyonu | Frontend kodu |
| Webhook desteği | Deployment süreçleri |

---

## 3. Public API İlkeleri

| # | İlke | Açıklama | Zorunlu mu? |
|---|------|----------|-------------|
| 1 | **Security First** | OWASP API Security Top 10 uyumlu | ✅ |
| 2 | **Versioning** | URL path versioning (`/v1/`, `/v2/`) | ✅ |
| 3 | **Consistent** | Tüm endpoint'ler aynı format | ✅ |
| 4 | **Paginated** | Large result sets pagination | ✅ |
| 5 | **Rate Limited** | Plan bazlı rate limiting | ✅ |
| 6 | **Documented** | OpenAPI 3.1 spec zorunlu | ✅ |
| 7 | **Idempotent** | POST/PUT/DELETE idempotent | ✅ |
| 8 | **CORS** | Cross-origin izinleri tanımlı | ✅ |

---

## 4. Kimlik Doğrulama Stratejisi

### 4.1 Mevcut: API Key Auth

```http
Authorization: Bearer cm_pub_{random_32_chars}
X-API-Key: cm_pub_{random_32_chars}
```

### 4.2 Gelecek: OAuth2 Hazırlığı

| Aşama | Zamanlama | Durum |
|-------|-----------|-------|
| API Key auth (mevcut) | 2026 Q3 | ✅ Aktif |
| OAuth2 PKCE hazırlık | 2027 Q1 | Planlandı |
| OAuth2 Authorization Code | 2027 Q2 | Planlandı |
| OIDC entegrasyonu | 2027 Q3 | Planlandı |

### 4.3 API Key Yönetimi

| Özellik | Değer |
|---------|-------|
| Key uzunluğu | 32 byte (256 bit) |
| Format | `cm_pub_{32_chars}` |
| Rotation | İsteğe bağlı (önerilen: 90 gün) |
| Scope | Okuma/Yazma ayrımı |
| Revocation | Admin panelinden anlık |

---

## 5. Rate Limit Planları

| Plan | Limit | Burst | Max Concurrent | Fiyat |
|------|-------|-------|----------------|-------|
| **Free** | 60 req/60s | 10 req/s | 5 | Ücretsiz |
| **Premium** | 120 req/60s | 20 req/s | 10 | Aylık abonelik |
| **Enterprise** | 600 req/60s | 100 req/s | 50 | Yıllık abonelik |

### 5.1 Rate Limit Headers

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1691234567
Retry-After: 30
```

### 5.2 Rate Limit Aşımı Yanıtı

```http
HTTP/1.1 429 Too Many Requests
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1691234567
Retry-After: 30

{
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Rate limit exceeded. Try again in 30 seconds.",
    "retry_after": 30
  }
}
```

---

## 6. API Versioning

### 6.1 Version Stratejisi

```
/api/v1/users
/api/v1/media
/api/v2/media    ← Breaking change'ler için yeni versiyon
```

### 6.2 Version Lifecycle

| Aşama | Süre | Davranış |
|-------|------|----------|
| Active | Kalıcı | Destekleniyor |
| Deprecated | 6 ay | Uyarı header'ı ekle |
| Sunset | 3 ay | 410 Gone döndür |
| Removed | — | Endpoint kaldırıldı |

### 6.3 Version Header

```http
X-API-Version: 1.0
X-API-Deprecated: true
Sunset: 2027-03-01
Link: </api/v2/media>; rel="successor-version"
```

---

## 7. API Dokümantasyonu

### 7.1 OpenAPI 3.1 Spec

Tüm API'ler OpenAPI 3.1 formatında belgelenir:

```yaml
openapi: 3.1.0
info:
  title: CoreMusic API
  version: 1.0.0
  contact:
    name: CoreMusic API Support
    email: api@coremusic.net
```

### 7.2 Dokümantasyon Araçları

| Araç | Amaç | URL |
|------|------|-----|
| Swagger UI | Interaktif test | `/api/docs` |
| Redoc | Okunabilir doküman | `/api/redoc` |
| Postman Collection | Test koleksiyonu | Export |

### 7.3 Dokümantasyon Gereksinimleri

| Özellik | Zorunlu mu? |
|---------|-------------|
| Tüm endpoint'ler | ✅ |
| Request/Response örnekleri | ✅ |
| Hata kodları | ✅ |
| Authentication açıklaması | ✅ |
| Rate limit bilgisi | ✅ |
| Changelog | ✅ |

---

## 8. SDK Üretimi

### 8.1 Desteklenen Diller

| Dil | Paket Yöneticisi | Durum |
|-----|------------------|-------|
| PHP | Composer | ✅ |
| JavaScript | npm | ✅ |
| Python | PyPI | ✅ |
| Java | Maven | Planlandı |
| Go | Go Modules | Planlandı |

### 8.2 SDK Üretim Akışı

```
OpenAPI Spec → codegen config → SDK üreteci → Dil bazlı paket → Test → Yayın
```

Detaylar: [[api-sdk]]

---

## 9. Webhook Desteği

### 9.1 Webhook Eventleri

| Event | Trigger | Payload |
|-------|---------|---------|
| `media.downloaded` | Medya indirildi | media_id, user_id |
| `media.updated` | Metadata güncellendi | media_id, changes |
| `playlist.updated` | Çalma listesi değişti | playlist_id |
| `user.login` | Kullanıcı girişi | user_id, timestamp |
| `download.completed` | İndirme tamamlandı | download_id |
| `download.failed` | İndirme başarısız | download_id, error |

### 9.2 Webhook Kuralları

| Kural | Değer |
|-------|-------|
| URL | HTTPS zorunlu |
| Timeout | 30s |
| Max retry | 3 (exponential backoff) |
| Signature | HMAC-SHA256 |
| Format | JSON |
| Max payload | 1MB |

### 9.3 Webhook İmzası

```php
$signature = hash_hmac('sha256', $payload, $webhookSecret);
// Header: X-Webhook-Signature: sha256={signature}
```

---

## 10. Deprecation Politikası

### 10.1 Deprecation Bildirimleri

| Kanal | Zamanlama |
|-------|-----------|
| API Response Header | Anlık (`Deprecation: true`) |
| E-posta bildirimi | 30 gün önce |
| Blog duyurusu | 60 gün önce |
| Admin paneli | 90 gün önce |

### 10.2 Deprecation Header

```http
Deprecation: true
Sunset: Sat, 01 Mar 2027 00:00:00 GMT
Link: </api/v2/media>; rel="successor-version"
X-API-Warn: This endpoint is deprecated. Use /api/v2/media instead.
```

---

## 11. API Changelog

### 11.1 Changelog Formatı

```markdown
## [1.2.0] - 2026-08-09

### Added
- New endpoint: `GET /api/v1/playlists/{id}/tracks`
- Webhook support for `playlist.updated`

### Changed
- `GET /api/v1/media` now supports `genre` filter

### Deprecated
- `GET /api/v1/media/old-search` → Use `/api/v1/media/search`

### Removed
- None

### Fixed
- Pagination bug in `GET /api/v1/users/history`
```

### 11.2 Semantic Versioning

| Değişiklik | Version Bump | Örnek |
|------------|-------------|-------|
| Yeni endpoint | Minor | 1.0.0 → 1.1.0 |
| Breaking change | Major | 1.0.0 → 2.0.0 |
| Bug fix | Patch | 1.0.0 → 1.0.1 |

---

## 12. Kullanım Koşulları

### 12.1 API Kullanım Sözleşmesi

| Kural | Açıklama |
|-------|----------|
| Adil kullanım | Rate limit'lere uygun |
| Veri gizliliği | Kullanıcı verisi 3. partiyle paylaşılmaz |
| Attribution | "Powered by CoreMusic" (isteğe bağlı) |
| Uygunluk | OWASP ve yerel yasalara uygun |
| Kötüye kullanım | Taranan key'ler iptal edilir |

### 12.2 Yasal Sorumluluk

- API kullanımı CoreMusic Kullanım Koşullarına tabidir
- Harici entegrasyonlar için ayrı sözleşme gerekebilir
- Veri koruma (GDPR) uyumluluğu zorunlu

---

## 13. Yanıt Süresi SLA'ları

| Endpoint Grubu | Hedef | Maksimum | Ölçüm |
|----------------|-------|----------|-------|
| Auth (login/logout) | <100ms | <500ms | p95 |
| Media listeleme | <200ms | <1s | p95 |
| Medya arama | <300ms | <2s | p95 |
| Metadata sorgulama | <100ms | <500ms | p95 |
| İndirme başlatma | <500ms | <3s | p95 |
| Webhook teslimi | <5s | <30s | p99 |

### 13.1 SLA Kırılma Davranışı

| Durum | Aksiyon |
|-------|---------|
| p95 > 2x hedef | WARN log, monitoring alert |
| p95 > 5x hedef | CRITICAL log, escalation |
| Availability < 99.9% | Otomatik fallback |

---

## 14. CORS Politikası

### 14.1 İzinli Origin'ler

```php
$allowedOrigins = [
    'https://coremusic.net',
    'https://music.coremusic.net',
    'https://admin.coremusic.net',
    'https://home.coremusic.net',
    // Development
    'http://localhost:3000',
    'http://localhost:8080',
];
```

### 14.2 CORS Headers

```http
Access-Control-Allow-Origin: https://music.coremusic.net
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key
Access-Control-Max-Age: 86400
Access-Control-Allow-Credentials: true
```

### 14.3 CORS Kuralları

| Kural | Değer |
|-------|-------|
| Credentials | `true` (cookie-based auth) |
| Max-age | 86400s (24 saat) |
| Preflight cache | Tarayıcıya bağlı |
| Wildcard `*` | ❌ Yasak |

---

## 15. Hata Formatı Standartları

### 15.1 Hata Yanıtı Formatı

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid request parameters",
    "details": [
      {
        "field": "email",
        "message": "Invalid email format"
      }
    ],
    "request_id": "req_abc123",
    "documentation_url": "https://docs.coremusic.net/api/errors#VALIDATION_ERROR"
  }
}
```

### 15.2 HTTP Hata Kodları

| Kod | Anlam | Örnek |
|-----|-------|-------|
| 400 | Bad Request | Geçersiz parametre |
| 401 | Unauthorized | Geçersiz API key |
| 403 | Forbidden | Yetki yok |
| 404 | Not Found | Kaynak bulunamadı |
| 409 | Conflict | Çakışma |
| 422 | Unprocessable | İşlenemeyen veri |
| 429 | Too Many Requests | Rate limit |
| 500 | Server Error | Sunucu hatası |
| 503 | Service Unavailable | Servis kullanılamıyor |

---

## 16. Public API Kataloğu

### 16.1 Auth Endpoints

| Endpoint | Method | Amaç | Rate Limit |
|----------|--------|------|------------|
| `/api/v1/auth/login` | POST | Kullanıcı girişi | 10/min |
| `/api/v1/auth/logout` | POST | Kullanıcı çıkışı | 30/min |
| `/api/v1/auth/register` | POST | Yeni kullanıcı | 5/min |
| `/api/v1/auth/refresh` | POST | Token yenileme | 30/min |

### 16.2 Media Endpoints

| Endpoint | Method | Amaç | Rate Limit |
|----------|--------|------|------------|
| `/api/v1/media` | GET | Medya listesi | 60/min |
| `/api/v1/media/{id}` | GET | Medya detayı | 60/min |
| `/api/v1/media/search` | GET | Medya arama | 30/min |
| `/api/v1/media/{id}/stream` | GET | Medya streaming | 30/min |
| `/api/v1/artists` | GET | Sanatçı listesi | 60/min |
| `/api/v1/genres` | GET | Tür listesi | 60/min |

### 16.3 Playlist Endpoints

| Endpoint | Method | Amaç | Rate Limit |
|----------|--------|------|------------|
| `/api/v1/playlists` | GET | Çalma listeleri | 60/min |
| `/api/v1/playlists` | POST | Yeni çalma listesi | 10/min |
| `/api/v1/playlists/{id}` | GET | Çalma listesi detayı | 60/min |
| `/api/v1/playlists/{id}` | PUT | Çalma listesi güncelle | 10/min |
| `/api/v1/playlists/{id}` | DELETE | Çalma listesi sil | 5/min |
| `/api/v1/playlists/{id}/tracks` | GET | Çalma listesi şarkıları | 60/min |
| `/api/v1/playlists/{id}/tracks` | POST | Şarkı ekle | 10/min |

### 16.4 User Endpoints

| Endpoint | Method | Amaç | Rate Limit |
|----------|--------|------|------------|
| `/api/v1/users/me` | GET | Mevcut kullanıcı | 30/min |
| `/api/v1/users/me` | PUT | Profil güncelle | 10/min |
| `/api/v1/users/me/history` | GET | Dinleme geçmişi | 30/min |
| `/api/v1/users/me/preferences` | GET | Tercihler | 30/min |
| `/api/v1/users/me/preferences` | PUT | Tercih güncelle | 10/min |

### 16.5 Download Endpoints

| Endpoint | Method | Amaç | Rate Limit |
|----------|--------|------|------------|
| `/api/v1/downloads` | GET | İndirme listesi | 30/min |
| `/api/v1/downloads` | POST | İndirme başlat | 5/min |
| `/api/v1/downloads/{id}` | GET | İndirme durumu | 30/min |
| `/api/v1/downloads/{id}` | DELETE | İndirmeyi iptal | 5/min |

---

## 17. Hızlı Referans

| İhtiyaç | İlk Adım |
|---------|----------|
| API key almak | §4 Kimlik Doğrulama |
| Rate limit öğrenmek | §5 Rate Limit Planları |
| Version değişikliği | §6 API Versioning |
| SDK kullanmak | §8 SDK Üretimi |
| Webhook kurmak | §9 Webhook Desteği |
| Deprecation bilgisi | §10 Deprecation Politikası |
| Hata kodları | §15 Hata Formatı |
| Endpoint bulmak | §16 Public API Kataloğu |

---

## 18. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Bu dosya | [[api-architecture-master]] | Ana API mimarisi |
| Bu dosya | [[api-design-rules]] | Tasarım kuralları |
| Bu dosya | [[api-internal-contract]] | Internal API farkları |
| Bu dosya | [[api-sdk]] | SDK üretimi |
| §4 OAuth2 | [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |
| §9 Webhook | [[ADR-026-download-service-architecture]] | Download servisi |
| §12 Kullanım | [[ADR-020-api-public-security]] | API güvenlik |
| §16 Katalog | [[architecture/06-audio/]] | Audio servisleri |

---

## 19. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 19 |
| Rate Limit Tiers | 3 (Free/Premium/Enterprise) |
| Public Endpoints | 20+ |
| Webhook Events | 6 |
| Error Codes | 9 |
| CORS Rules | 4 |
| SLA Groups | 6 |
| Cross References | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode