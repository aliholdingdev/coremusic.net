---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Backend Reference"
type: reference
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/05-backend-reference.md"
  source_of_truth: ".ai/CLAUDE.md §18C · .ai/ui-design/reference/01-php-source-architecture.md"
---

# CoreMusic — Backend Reference

**Zorunlu Bağlantılar:** [[01-php-source-architecture]] · [[10-device-specific-guidelines]]

---

## 1. Amaç

Frontend geliştirme sırasında **backend API endpoint'leri ve session/auth** referansıdır.

---

## 2. API Endpoint'leri

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | `/api/v1/songs` | Şarkı listesi |
| GET | `/api/v1/songs/{id}` | Şarkı detayı |
| GET | `/api/v1/albums` | Albüm listesi |
| GET | `/api/v1/artists` | Sanatçı listesi |
| GET | `/api/v1/playlists` | Çalma listesi |
| POST | `/api/v1/playlists` | Çalma listesi oluştur |
| GET | `/api/v1/search?q=` | Arama |
| GET | `/api/v1/radio` | Radyo istasyonları |
| GET | `/api/v1/podcasts` | Podcast listesi |
| POST | `/api/v1/auth/login` | Giriş |
| POST | `/api/v1/auth/logout` | Çıkış |
| GET | `/api/v1/user/profile` | Kullanıcı profili |
| PUT | `/api/v1/user/preferences` | Tercihler |

---

## 3. Session & Auth

```javascript
// Cookie: cm_session (HTTPOnly, Secure, SameSite=Strict)
// CSRF Token: csrf_token (NOT _csrf_token)
// Auth Header: Authorization: Bearer <jwt>
```

---

## 4. Response Format

```json
{
  "status": "success",
  "data": { ... },
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 100
  }
}
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| API Endpoints | 13 |
| Cross References | 2 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
