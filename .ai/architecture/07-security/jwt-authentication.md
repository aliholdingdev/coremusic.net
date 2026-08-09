---
title: "CoreMusic — JWT Authentication"
type: architecture
category: security
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — JWT Authentication

**Zorunlu Bağlantılar:** [[index]] · [[ADR-011-session-management]] · [[ADR-022-database-hardened-security]]

---

## 1. Amaç

JWT tabanlı kimlik doğrulama sistemini tanımlar. Token yönetimi, refresh mekanizması ve security best practices.

---

## 2. JWT Structure

```
Header.Payload.Signature
```

| Component | İçerik |
|-----------|--------|
| Header | alg: HS256, typ: JWT |
| Payload | sub, iss, exp, iat, jti, roles |
| Signature | HMAC-SHA256(secret, header.payload) |

---

## 3. Token Lifecycle

```
Generate → Issue → Use → Refresh → Expire → Revoke
    ↓         ↓       ↓        ↓         ↓        ↓
  Login    Response  API     Refresh   Timeout   Logout
```

---

## 4. Token Configuration

| Parametre | Değer |
|-----------|-------|
| Access Token TTL | 15 dakika |
| Refresh Token TTL | 7 gün |
| Algorithm | HS256 |
| Issuer | coremusic.net |
| Audience | coremusic-api |

---

## 5. Token Storage

| Konum | Kullanım | Güvenlik |
|-------|----------|----------|
| HttpOnly Cookie | Web frontend | XSS-safe |
| Memory | SPA runtime |临时 |
| Secure Storage | Mobile | OS-level |

---

## 6. Refresh Flow

```
Access Token Expired → Send Refresh Token → Validate → Issue New Pair
         ↓                    ↓               ↓            ↓
      401 Response       /auth/refresh    DB check     New tokens
```

---

## 7. Security Rules

| Kural | Açıklama |
|-------|----------|
| No localStorage | Auth tokens localStorage'da saklanmaz |
| HttpOnly | Cookie'ler HttpOnly |
| Secure | HTTPS zorunlu |
| SameSite | CSRF koruması |
| Short TTL | Access token 15 dk |
| Rotation | Refresh token rotation |

---

## 8. Token Revocation

| Durum | Aksiyon |
|-------|---------|
| Logout | Token blacklist'e ekle |
| Password change | Tüm token'ları sil |
| Security breach | All tokens revoke |
| Admin action | User token revoke |

---

## 9. Error Handling

| Hata | HTTP | Çözüm |
|------|------|-------|
| Invalid token | 401 | Yeniden login |
| Expired token | 401 | Refresh token |
| Revoked token | 401 | Yeniden login |
| Invalid signature | 401 | Security alert |

---

## 10. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 JWT | [[ADR-011-session-management]] | Session management |
| § 7 Security | [[ADR-022-database-hardened-security]] | Encryption |
| § 8 Revocation | [[ADR-010-csrf-protection-strategy]] | CSRF |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
