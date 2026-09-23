---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — API Documentation Template"
type: api-doc-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{API_NAME}} API

**Base URL:** `https://api.coremusic.net/v1`
**Authentication:** JWT Bearer Token
**Format:** JSON
**Rate Limit:** 60 req/60s (APCu)

---

## 1. Endpoint Listesi

| Method | Endpoint | Açıklama | Auth |
|--------|----------|----------|------|
| GET | `/{{MODULE}}` | Tüm kayıtları listele | ✅ |
| GET | `/{{MODULE}}/:id` | Tek kayıt getir | ✅ |
| POST | `/{{MODULE}}` | Yeni kayıt oluştur | ✅ |
| PUT | `/{{MODULE}}/:id` | Kaydı güncelle | ✅ |
| DELETE | `/{{MODULE}}/:id` | Kaydı sil | ✅ |

---

## 2. Request/Response Formatları

### GET /api/v1/{{MODULE}}

**Request:**
```http
GET /api/v1/{{MODULE}} HTTP/1.1
Host: api.coremusic.net
Authorization: Bearer <token>
X-CSRF-Token: <csrf_token>
X-Requested-With: XMLHttpRequest
```

**Response (200 OK):**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "{{COLUMN_1}}": "value1",
            "{{COLUMN_2}}": "value2",
            "created_at": "2026-09-20T10:00:00Z",
            "updated_at": "2026-09-20T10:00:00Z"
        }
    ],
    "meta": {
        "total": 100,
        "page": 1,
        "per_page": 20
    }
}
```

### GET /api/v1/{{MODULE}}/:id

**Response (200 OK):**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "{{COLUMN_1}}": "value1",
        "{{COLUMN_2}}": "value2",
        "created_at": "2026-09-20T10:00:00Z"
    }
}
```

**Response (404 Not Found):**
```json
{
    "status": "error",
    "error": {
        "code": "NOT_FOUND",
        "message": "{{MODULE}} not found"
    }
}
```

### POST /api/v1/{{MODULE}}

**Request:**
```http
POST /api/v1/{{MODULE}} HTTP/1.1
Content-Type: application/json
Authorization: Bearer <token>
X-CSRF-Token: <csrf_token>
```

```json
{
    "{{COLUMN_1}}": "new value",
    "{{COLUMN_2}}": 123
}
```

**Response (201 Created):**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "{{COLUMN_1}}": "new value",
        "{{COLUMN_2}}": 123,
        "created_at": "2026-09-20T10:00:00Z"
    }
}
```

---

## 3. Error Codes

| Code | HTTP Status | Açıklama |
|------|------------|----------|
| `VALIDATION_ERROR` | 400 | Geçersiz veri |
| `UNAUTHORIZED` | 401 | Kimlik doğrulama başarısız |
| `FORBIDDEN` | 403 | Erişim engellendi |
| `NOT_FOUND` | 404 | Kayıt bulunamadı |
| `CONFLICT` | 409 | Çakışma |
| `RATE_LIMITED` | 429 | Çok fazla istek |
| `SERVER_ERROR` | 500 | Sunucu hatası |

---

## 4. Hata Formatı

```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Validation failed",
        "details": {
            "{{COLUMN_1}}": "This field is required",
            "{{COLUMN_2}}": "Must be a positive integer"
        }
    }
}
```

---

*API Documentation Template v1.0.0 — CoreMusic API Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
