# k6-k7-security — Güvenlik Katmanları

## Bağlam

Bu klasör, CoreMusic'in güvenlik ve middleware katmanlarını (K6-K7) içerir. Auth, CSRF, CSP, rate limiting ve tüm güvenlik mekanizmaları burada tanımlıdır.

## İlgili Dosyalar

| Dosya | Katman | İçerik |
|-------|--------|--------|
| k6-security.md | K6 | Auth, RBAC, CSRF, CSP, Encryption |
| k7-middleware.md | K7 | Middleware pipeline (10 katman) |
| k06-auth-layer.md | Detayı | JWT, Session, OAuth2, MFA |
| k07-security-detail.md | Detayı | OWASP Top 10, şifreleme |

## Komşu İlişkiler

| Yön | Hedef Klasör | İlişki |
|-----|-------------|--------|
| Yukarı | k8-k9-services/ | Servisleri korur |
| Aşağı | k0-k5-software/ | Veriyi korur |

## Kritik Kurallar

- Middleware sırası DEĞİŞTİRİLEMEZ (ADR-010/011/012/013/022)
- CSRF token key: `csrf_token` (NOT `_csrf_token`)
- CSP nonce: `base64_encode(random_bytes(32))`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
