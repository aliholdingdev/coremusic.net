---
type: architecture
category: auth
title: "Enterprise Auth — API Endpoints"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — API Endpoints

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Auth API endpoint'lerini ve request/response formatlarını tanımlar. Tüm subdomain'ler bu API'yi kullanarak session doğrulaması yapar.

## 2. API Endpoints

### 2.1 POST /api/login

Kullanıcı girişi.

**Request:**
```json
{
  "email": "user@coremusic.net",
  "password": "securepassword",
  "redirect_url": "https://home.coremusic.net"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user_id": "uuid-v4",
    "session_id": "session-uuid",
    "email": "user@coremusic.net",
    "roles": ["standard"],
    "redirect_url": "https://home.coremusic.net"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "success": false,
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "Invalid email or password"
  }
}
```

**Response (429 Too Many Requests):**
```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many login attempts",
    "retry_after": 60
  }
}
```

### 2.2 POST /api/logout

Kullanıcı çıkışı.

**Request:**
```json
{
  "session_id": "session-uuid"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### 2.3 POST /api/register

Kullanıcı kaydı.

**Request:**
```json
{
  "email": "newuser@coremusic.net",
  "password": "securepassword",
  "password_confirmation": "securepassword"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registration successful"
}
```

**Response (422 Unprocessable Entity):**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Email already registered"
  }
}
```

### 2.4 GET /api/session/check

Session doğrulama (tüm subdomain'ler tarafından kullanılır).

**Request Headers:**
```
Cookie: COREMUSIC_SESS=session-uuid
```

**Response (200 OK):**
```json
{
  "valid": true,
  "data": {
    "session_id": "session-uuid",
    "user_id": "uuid-v4",
    "email": "user@coremusic.net",
    "roles": ["standard"],
    "permissions": ["music:read", "media:read"],
    "last_activity": "2026-08-09T12:00:00Z"
  }
}
```

**Response (401 Unauthorized):**
```json
{
  "valid": false,
  "error": {
    "code": "INVALID_SESSION",
    "message": "Session expired or invalid"
  }
}
```

### 2.5 POST /api/session/refresh

Session yenileme.

**Request:**
```json
{
  "session_id": "session-uuid"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "session_id": "new-session-uuid",
    "expires_at": "2026-08-10T12:00:00Z"
  }
}
```

### 2.6 GET /api/permissions

Kullanıcı izinlerini listeleme.

**Request Headers:**
```
Cookie: COREMUSIC_SESS=session-uuid
```

**Response (200 OK):**
```json
{
  "permissions": [
    {
      "name": "music:read",
      "resource": "music",
      "action": "read"
    },
    {
      "name": "media:read",
      "resource": "media",
      "action": "read"
    }
  ]
}
```

## 3. API Rate Limits

| Endpoint | Limit | Pencere | Cezalandırma |
|----------|-------|---------|-------------|
| POST /api/login | 5 req | 60s | 15dk lockout |
| POST /api/register | 3 req | 300s | 1 saat ban |
| GET /api/session/check | 60 req | 60s | 429 Too Many |
| POST /api/session/refresh | 10 req | 60s | 429 Too Many |
| GET /api/permissions | 60 req | 60s | 429 Too Many |

## 4. API Security

| Özellik | Değer |
|---------|-------|
| **Authentication** | Cookie-based (HTTPOnly) |
| **CSRF** | csrf_token (POST/PUT/DELETE) |
| **CORS** | Whitelist tabanlı |
| **Rate Limit** | APCu sliding window |
| **HTTPS** | Zorunlu |
| **Input Validation** | Respect Validation |

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Endpoints | 6 |
| Rate Limits | 5 |
| Security Features | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
