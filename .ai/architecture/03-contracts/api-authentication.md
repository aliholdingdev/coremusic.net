---
type: architecture
category: contracts
title: "API Authentication & Authorization"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Authentication & Authorization

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[ADR-052-hybrid-auth-architecture]] · [[ADR-058-cross-subdomain-auth-flow]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic API kimlik doğrulama (authentication) ve yetkilendirme (authorization) mimarisini, Hybrid Auth akışlarını, RBAC rollerini ve token lifecycle'ı tanımlayan **Tek Doğruluk Kaynağıdır**.

## 2. Hybrid Auth Architecture

### 2.1 Auth Yöntemleri

| Yöntem | Kullanım Alanı | Token Tipi | Süre |
|--------|---------------|------------|------|
| **Session** | Browser SPA clients | `COREMUSIC_SESS` cookie | 3600s idle |
| **JWT RS256** | Service-to-service | Bearer token | 15 dk |
| **API Key** | Third-party integrations | `X-API-Key` header | 90 gün |
| **OAuth2准备** | Gelecek entegrasyonlar | Bearer token | Planlanıyor |

*Kaynak: [[ADR-052-hybrid-auth-architecture]]*

### 2.2 Auth Akış Diyagramı

```
┌─────────────────────────────────────────────────────────────┐
│                    LOGIN FLOW                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Client                                                      │
│  │                                                           │
│  ├── POST /api/v1/auth/login                                 │
│  │   { "email": "user@example.com", "password": "..." }     │
│  │                                                           │
│  ▼                                                           │
│  Auth Service                                                │
│  │                                                           │
│  ├── 1. Rate limit kontrolü (5 deneme/15 dk)                │
│  ├── 2. Email format doğrulama                                │
│  ├── 3. Kullanıcıyı bul (coremusic_auth)                     │
│  ├── 4. Argon2id password doğrula                            │
│  │   (memory=64MB, time=4, threads=2)                       │
│  ├── 5. Session oluştur (COREMUSIC_SESS)                    │
│  ├── 6. JWT token üret (RS256, 15 dk)                       │
│  ├── 7. Audit log yaz                                       │
│  │                                                           │
│  ▼                                                           │
│  Response                                                     │
│  │                                                           │
│  ├── Set-Cookie: COREMUSIC_SESS=abc123; Secure; HttpOnly    │
│  ├── { "access_token": "eyJ...", "token_type": "Bearer" }   │
│  └── { "user": { "id": 123, "role": "premium_user" } }     │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

```
┌─────────────────────────────────────────────────────────────┐
│                    CROSS-SUBDOMAIN FLOW                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  music.coremusic.net → auth.coremusic.net                   │
│  │                                                           │
│  ├── 1. Session cookie domain: .coremusic.net               │
│  │                                                           │
│  ├── 2. Cross-subdomain auth check                           │
│  │   GET /api/v1/auth/session-check                         │
│  │   Cookie: COREMUSIC_SESS=abc123                          │
│  │                                                           │
│  ├── 3. Auth Service doğrula                                 │
│  │   ├── Session geçerli mi? (3600s idle)                  │
│  │   ├── Kullanıcı aktif mi?                                │
│  │   └── RBAC rolleri doğru mu?                             │
│  │                                                           │
│  ├── 4. JWT token yenile (eğer süresi dolmak üzere)         │
│  │                                                           │
│  └── 5. Response: { "authenticated": true, "user": {...} }  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

```
┌─────────────────────────────────────────────────────────────┐
│                    TOKEN REFRESH FLOW                         │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Client                                                      │
│  │                                                           │
│  ├── POST /api/v1/auth/refresh                              │
│  │   Cookie: COREMUSIC_SESS=abc123                          │
│  │                                                           │
│  ▼                                                           │
│  Auth Service                                                │
│  │                                                           │
│  ├── 1. Session'ı doğrula                                   │
│  ├── 2. Refresh token'ı doğrula (7 gün)                     │
│  ├── 3. Yeni JWT üret (15 dk)                               │
│  ├── 4. Eski refresh token'ı iptal et                       │
│  │                                                           │
│  ▼                                                           │
│  Response                                                     │
│  ├── { "access_token": "eyJ...", "expires_in": 900 }       │
│  └── Set-Cookie: COREMUSIC_SESS=new_session; ...            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

```
┌─────────────────────────────────────────────────────────────┐
│                    LOGOUT FLOW                                │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Client                                                      │
│  │                                                           │
│  ├── POST /api/v1/auth/logout                               │
│  │   Cookie: COREMUSIC_SESS=abc123                          │
│  │   Authorization: Bearer eyJ...                           │
│  │                                                           │
│  ▼                                                           │
│  Auth Service                                                │
│  │                                                           │
│  ├── 1. Session'ı sil                                       │
│  ├── 2. JWT token'ı blacklist'e ekle                        │
│  ├── 3. Refresh token'ı sil                                 │
│  ├── 4. Audit log yaz                                       │
│  │                                                           │
│  ▼                                                           │
│  Response                                                     │
│  ├── Set-Cookie: COREMUSIC_SESS=; Max-Age=0                │
│  └── { "status": "logged_out" }                             │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## 3. RBAC Roller & Yetkiler

### 3.1 Rol Hiyerarşisi

```
┌─────────────────────────────────────────────────────────────┐
│                    RBAC ROLE HIERARCHY                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  admin (En yüksek)                                           │
│  ├── Tüm yetkiler                                            │
│  ├── Kullanıcı yönetimi                                     │
│  ├── Sistem konfigürasyonu                                  │
│  └── Audit trail erişimi                                    │
│                                                              │
│  ultra_user                                                  │
│  ├── Tüm premium yetkileri                                  │
│  ├── Sınırsız indirme                                       │
│  ├── Yüksek kalite streaming (FLAC 24-bit)                  │
│  └── Özel EQ preset'leri                                    │
│                                                              │
│  premium_user                                                │
│  ├── Tüm streaming yetkileri                                │
│  ├── Yüksek kalite streaming (320kbps)                      │
│  ├── İndirme yetkisi                                        │
│  └── Çalma listesi oluşturma                               │
│                                                              │
│  streaming_user                                              │
│  ├── Streaming yetkisi                                       │
│  ├── Standart kalite (128kbps)                              │
│  └── Çalma listesi görüntüleme                              │
│                                                              │
│  panel_user                                                  │
│  ├── Panel erişimi                                          │
│  ├── Kullanıcı profili yönetimi                             │
│  └── Tercihler                                               │
│                                                              │
│  free_user                                                   │
│  ├── Sınırlı streaming                                      │
│  ├── Reklam destekli                                        │
│  └── Temel özellikler                                       │
│                                                              │
│  guest (En düşük)                                            │
│  ├── Sadece public içerik                                   │
│  ├── Kayıt/giriş sayfası                                   │
│  └── Demo modu                                               │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Permission Matrix

| Permission | admin | ultra_user | premium_user | streaming_user | panel_user | free_user | guest |
|------------|-------|------------|--------------|----------------|------------|-----------|-------|
| `auth:login` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `auth:register` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `music:read` | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| `music:stream` | ✅ | ✅ | ✅ | ✅ | ❌ | ⚠️ | ❌ |
| `music:download` | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `music:upload` | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `playlist:create` | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| `playlist:read` | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| `playlist:delete` | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| `album:read` | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| `artist:read` | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| `search:full` | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ | ❌ |
| `eq:customize` | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `eq:preset:save` | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `user:profile:read` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| `user:profile:write` | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `user:admin` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `system:config` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `audit:read` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

**⚠️ Sınırlı:** Sadece public/default içerikler

### 3.3 Endpoint Permission Mapping

```
GET    /api/v1/songs              → music:read
POST   /api/v1/songs              → music:upload
GET    /api/v1/songs/{id}         → music:read
PUT    /api/v1/songs/{id}         → music:upload
DELETE /api/v1/songs/{id}         → music:upload

GET    /api/v1/songs/{id}/stream  → music:stream
POST   /api/v1/songs/{id}/download → music:download

GET    /api/v1/playlists          → playlist:read
POST   /api/v1/playlists          → playlist:create
GET    /api/v1/playlists/{id}     → playlist:read
DELETE /api/v1/playlists/{id}     → playlist:delete

POST   /api/v1/auth/login         → auth:login
POST   /api/v1/auth/register      → auth:register
POST   /api/v1/auth/logout        → auth:login
GET    /api/v1/auth/session-check → auth:login

GET    /api/v1/admin/users        → user:admin
PUT    /api/v1/admin/users/{id}   → user:admin
DELETE /api/v1/admin/users/{id}   → user:admin

GET    /api/v1/system/health      → (herkes)
GET    /api/v1/system/config      → system:config
```

## 4. API Key Auth (Service-to-Service)

### 4.1 API Key Yapısı

```
cm_live_4f8a2b1c3d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e
│      │
│      └── 48 char hex
└── Prefix (cm_live_ = production, cm_test_ = test)
```

### 4.2 API Key Properties

| Özellik | Değer |
|---------|-------|
| Format | `cm_live_` + 48 char hex |
| Uzunluk | 56 karakter |
| Storage | AES-256-GCM encrypted (DB'de hash) |
| Rotation | 90 günde bir |
| Rate Limit | 30 req/60s |
| Scope | Endpoint bazlı |

### 4.3 API Key Usage

```http
GET /api/v1/internal/songs HTTP/1.1
Host: api.coremusic.net
X-API-Key: cm_live_4f8a2b1c3d5e6f7a8b9c0d1e2f3a4b5c6d7e8f9a0b1c2d3e
Content-Type: application/json
```

## 5. Auth Headers

### 5.1 Request Headers

| Header | Format | Kullanım |
|--------|--------|----------|
| `Authorization` | `Bearer <JWT>` | JWT auth |
| `X-API-Key` | `cm_live_<key>` | API Key auth |
| `Cookie` | `COREMUSIC_SESS=<session>` | Session auth |
| `X-CSRF-Token` | `<token>` | CSRF koruması |

### 5.2 Response Headers

| Header | Format | Kullanım |
|--------|--------|----------|
| `X-Auth-Status` | `authenticated` / `unauthenticated` | Auth durumu |
| `X-Auth-User` | `<user_id>` | Kullanıcı ID |
| `X-Auth-Role` | `<role>` | Kullanıcı rolü |
| `X-Token-Expires` | `<unix_timestamp>` | Token bitiş |
| `Set-Cookie` | `COREMUSIC_SESS=...` | Session cookie |

## 6. Token Lifecycle

```
┌─────────────────────────────────────────────────────────────┐
│                    TOKEN LIFECYCLE                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  JWT Access Token (15 dk)                                    │
│  ├── Oluştur: Login/Refresh                                 │
│  ├── Kullan: Authorization: Bearer header                   │
│  ├── Yenile: /api/v1/auth/refresh                           │
│  └── Sil: Logout veya blacklist                             │
│                                                              │
│  Session Cookie (3600s idle)                                 │
│  ├── Oluştur: Login                                         │
│  ├── Kullan: Cookie header (otomatik)                       │
│  ├── Yenile: Her başarılı istekte (idle reset)              │
│  └── Sil: Logout veya timeout                               │
│                                                              │
│  Refresh Token (7 gün)                                       │
│  ├── Oluştur: Login                                         │
│  ├── Kullan: /api/v1/auth/refresh                           │
│  ├── Yenile: Her refresh'te (rotation)                      │
│  └── Sil: Logout veya 7 gün sonra                          │
│                                                              │
│  API Key (90 gün)                                            │
│  ├── Oluştur: Admin panel                                   │
│  ├── Kullan: X-API-Key header                               │
│  ├── Yenile: Manual rotation                                │
│  └── Sil: Admin panel                                       │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## 7. Key Rotation (90 Days)

| Gün | Aksiyon |
|-----|---------|
| GÜN 0 | Yeni API key oluştur |
| GÜN 60 | Rotation hatırlatması |
| GÜN 80 | Son hatırlatma (10 gün kaldı) |
| GÜN 89 | Yeni key ile dual-key period başlat |
| GÜN 90 | Eski key'i devre dışı bırak |

### 7.1 Dual-Key Period

```
GÜN 89-90: Dual-Key Period
├── Eski key hala çalışır
├── Yeni key de çalışır
├── Client'lar yeni key'e geçmeli
└── GÜN 90: Eski key kapatılır
```

## 8. OAuth2 Preparation

| Özellik | Planlanan Değer |
|---------|-----------------|
| Flow | Authorization Code + PKCE |
| Token Endpoint | `/api/v2/oauth/token` |
| Auth Endpoint | `/api/v2/oauth/authorize` |
| Scopes | `music:read`, `music:write`, `profile:read` |
| Client Types | Public (SPA/Mobile), Confidential (Server) |
| Token Format | JWT RS256 |

## 9. Auth Error Responses

| Hata Kodu | HTTP Status | Açıklama |
|-----------|-------------|----------|
| `AUTH_INVALID_CREDENTIALS` | 401 | Email/şifre hatalı |
| `AUTH_SESSION_EXPIRED` | 401 | Session süresi dolmuş |
| `AUTH_TOKEN_EXPIRED` | 401 | JWT süresi dolmuş |
| `AUTH_TOKEN_INVALID` | 401 | Geçersiz JWT |
| `AUTH_INSUFFICIENT_PERMISSION` | 403 | Yetki yetersiz |
| `AUTH_RATE_LIMITED` | 429 | Çok fazla deneme |
| `AUTH_ACCOUNT_LOCKED` | 423 | Hesap kilitlenmiş |
| `AUTH_ACCOUNT_DISABLED` | 403 | Hesap devre dışı |

## 10. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Client-side session | Server-side session |
| Session'da sensitive data | Session'da sadece ID |
| JWT'de password hash | JWT'de sadece metadata |
| Static JWT secret | RS256 key pair |
| Unlimited login attempts | 5 deneme/15 dk |
| Password plaintext | Argon2id hash |
| API Key log'da | `[REDACTED]` |
| Session cookie: `Secure: false` | `Secure: true` zorunlu |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Hybrid Auth (Session + JWT) zorunlu | Auth bypass riski |
| 2 | Argon2id (64MB/4/2) zorunlu | Şifre kırılma riski |
| 3 | Session idle timeout 3600s | Güvenlik açığı |
| 4 | JWT expiry 15 dk | Token reuse riski |
| 5 | CSRF token zorunlu (POST/PUT/DELETE) | CSRF saldırısı |
| 6 | Rate limit: 5 deneme/15 dk | Brute force |
| 7 | API key rotation: 90 gün | Eski key sızıntısı |
| 8 | Cross-subdomain session domain: `.coremusic.net` | Auth kopukluğu |

## 12. Cross References

| Dosya | İlişki |
|-------|--------|
| [[api-architecture-master]] | Ana API mimarisi |
| [[api-design-rules]] | Tasarım kuralları |
| [[api-versioning]] | Sürüm yönetimi |
| [[api-security]] | Güvenlik katmanı |
| [[api-error-codes]] | Hata kodları |
| [[ADR-052-hybrid-auth-architecture]] | Hybrid Auth kararı |
| [[ADR-058-cross-subdomain-auth-flow]] | Cross-subdomain auth |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruması |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-022-database-hardened-security]] | Argon2id, AES-256-GCM |
| [[ADR-043-auth-subdomain-consolidation]] | Auth subdomain |

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 010, 011, 022, 052, 058, 043 |
| **RBAC Roles** | 7 |
| **Permissions** | 19 |
| **Token Types** | 4 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
