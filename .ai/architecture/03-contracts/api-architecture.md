---
title: "CoreMusic — API Architecture"
type: architecture
category: api
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — API Architecture

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[ADR-039-7-service-platform-architecture]]

---

## 1. Amaç

API mimarisini ve standartlarını tanımlar. REST endpoints, authentication, rate limiting.

---

## 2. API Endpoints

| Servis | Port | Protocol | Auth |
|--------|------|----------|------|
| Control Service | 81 | HTTP | Session/JWT |
| Media Service | 5000/6000 | HTTP | API Key |
| Download Service | 3001 | HTTP/WS | API Key |
| Audio Service | 9741/9742 | REST/WS | JWT |

---

## 3. API Standards

| Kural | Değer |
|-------|-------|
| Format | JSON |
| Versioning | URL (/v1/, /v2/) |
| Pagination | Cursor-based |
| Rate Limit | 60 req/60s |
| Timeout | 30s |

---

## 4. Authentication Flow

```
Client → Auth Service → Validate → Token → API Gateway → Service
  ↓          ↓            ↓          ↓           ↓           ↓
Request    Login      Argon2id    JWT       CORS check   Response
```

---

## 5. Error Responses

| HTTP | Code | Açıklama |
|------|------|----------|
| 400 | INVALID_REQUEST | Geçersiz istek |
| 401 | UNAUTHORIZED | Kimlik doğrulama başarısız |
| 403 | FORBIDDEN | Yetki yok |
| 404 | NOT_FOUND | Kaynak bulunamadı |
| 429 | RATE_LIMITED | Rate limit aşıldı |
| 500 | INTERNAL_ERROR | Sunucu hatası |

---

## 6. CORS Configuration

| Setting | Değer |
|---------|-------|
| Origins | Whitelist |
| Methods | GET, POST, PUT, DELETE |
| Headers | Authorization, Content-Type |
| Credentials | true |

---

## 7. Rate Limiting

| Layer | Kural |
|-------|-------|
| Global | 60 req/60s |
| Per-user | 100 req/60s |
| Per-endpoint | Custom |
| Per-IP | 30 req/60s |

---

## 8. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Endpoints | [[ADR-039-7-service-platform-architecture]] | 7 servis |
| § 4 Auth | [[ADR-043-auth-subdomain-consolidation]] | Auth |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
