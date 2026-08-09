---
type: architecture
category: contracts
title: "API Validation Rules"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Validation Rules

## 1. Purpose

Defines server-side validation requirements for all API inputs in CoreMusic. Validation is mandatory, never trust client input.

---

## 2. Core Principles

| Principle | Rule |
|-----------|------|
| Server-side mandatory | All validation runs on server, never trust client |
| Whitelist validation | Allow only expected field names and types |
| Fail fast | Return 422 on first validation failure |
| No silent defaults | Missing required fields return explicit error |
| Sanitize + validate | Strip input, then validate against rules |

---

## 3. Validation Sources

| Source | Validation Type | Example |
|--------|----------------|---------|
| Request body (JSON) | Schema validation | `{"title": "Song"}` |
| Query parameters | Type + range validation | `?page=1&per_page=20` |
| Headers | Format validation | `Authorization: Bearer <token>` |
| Route parameters | Format validation | `/api/songs/{id}` |

---

## 4. Validation Rules Catalog

| Rule | Description | Example |
|------|-------------|---------|
| `required` | Field must be present | `title: required` |
| `email` | Valid email format | `user@email.com` |
| `min_length:N` | Minimum string length | `min_length:3` |
| `max_length:N` | Maximum string length | `max_length:255` |
| `regex:PATTERN` | Pattern match | `regex:/^[a-z]+$/` |
| `enum:[v1,v2]` | Allowed values | `enum:[pop,rock,jazz]` |
| `numeric` | Numeric value | `42` or `"42"` |
| `integer` | Integer only | `42` (not `42.5`) |
| `float` | Floating point | `42.5` |
| `date` | ISO 8601 date | `2024-01-15` |
| `datetime` | ISO 8601 datetime | `2024-01-15T10:30:00Z` |
| `uuid` | UUID v4 format | `550e8400-e29b-41d4-a716-446655440000` |
| `url` | Valid URL | `https://example.com` |
| `boolean` | True/false | `true` or `false` |
| `in:array` | Value in allowed list | `in:[1,2,3]` |
| `json` | Valid JSON object | `{"key": "value"}` |
| `file` | Uploaded file | Multipart form data |
| `array` | Array value | `[1,2,3]` |

---

## 5. Validation Error Format

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Request validation failed",
    "details": [
      {
        "field": "title",
        "rule": "required",
        "message": "Title is required"
      },
      {
        "field": "email",
        "rule": "email",
        "message": "Invalid email format"
      },
      {
        "field": "duration",
        "rule": "numeric",
        "message": "Duration must be numeric"
      }
    ]
  }
}
```

**HTTP Status:** `422 Unprocessable Entity`

---

## 6. Validation Groups

| Group | When Applied | Rules |
|-------|-------------|-------|
| `create` | POST /api/resource | All required fields |
| `update` | PUT /api/resource | All fields optional (partial update) |
| `patch` | PATCH /api/resource | Only provided fields validated |
| `search` | GET /api/resource?q= | Query parameter rules |
| `auth` | Login/Register | Auth-specific rules |

---

## 7. Nested Object Validation

```json
{
  "artist": {
    "name": "required|max_length:255",
    "bio": "max_length:2000",
    "social": {
      "website": "url",
      "twitter": "max_length:15"
    }
  }
}
```

**Rules:**
- Nested objects validated recursively
- Dot-notation for error paths: `artist.social.website`
- Missing nested objects return parent field error

---

## 8. Custom Validators

| Validator | Logic |
|-----------|-------|
| `unique:table,column` | Check DB uniqueness (exclude current ID on update) |
| `exists:table,column` | Check foreign key exists |
| `strong_password` | Min 8 chars, uppercase, lowercase, digit, special |
| `no_html` | Strip all HTML tags |
| `safe_filename` | Allow alphanumeric, dash, underscore, dot only |
| `file_size:max_mb` | Max file upload size |
| `mime_type:allowed` | Validate file MIME type |

---

## 9. Security Validation

| Rule | Purpose |
|------|---------|
| Strip HTML tags | Prevent XSS in stored content |
| Limit string length | Prevent memory exhaustion |
| Limit array size | Prevent DoS via large payloads |
| Reject null bytes | Prevent path traversal |
| Reject control chars | Prevent injection attacks |
| Validate Content-Type | Ensure correct content type header |

---

## 10. Cross References

| Document | Relationship |
|----------|-------------|
| [[api-architecture-master]] | Parent API architecture |
| [[api-design-rules]] | API design conventions |
| [[api-error-codes]] | Error code catalog |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode