# Security Audit

Güvenlik denetimi yap, açıkları tespit et, düzeltmeleri öner.

## Denetim Alanları

| Alan | Kontroller |
|------|------------|
| Authentication | Login, register, session, password |
| Authorization | RBAC, yetki bypass, privilege escalation |
| CSRF | Token varlığı, SameSite, Double Submit |
| CSP | Nonce, strict-dynamic, script-src |
| XSS | innerHTML, eval, document.write |
| SQL Injection | Prepared statements, parameterized queries |
| Encryption | AES-256-GCM, Argon2id, key management |

## Kontrol Listesi

```
✅ CSRF token her form'da var mi?
✅ CSP nonce her script'te var mi?
✅ Session timeout uygulaniyor mu?
✅ Password hash Argon2id mi?
✅ PDO prepared statement kullaniliyor mu?
✅ innerHTML kullanimi var mi? (YASAK)
✅ Hardcoded secret var mi? (YASAK)
✅ Rate limiting aktif mi?
✅ CORS dogru yapilandirilmis mi?
✅ Security headers mevcut mu?
```

## OWASP Top 10:2025 Kontrolü

| Kod | Kategori | Kontrol |
|-----|----------|---------|
| A01 | Broken Access Control | Yetki denetimi, bypass kontrolü |
| A02 | Cryptographic Failures | AES-256-GCM, Argon2id kullanımı |
| A03 | Injection | SQL injection, XSS, command injection |
| A05 | Security Misconfiguration | CSP, CORS, security headers |
| A08 | Data Integrity | Software supply chain, deserialization |
| A10 | Exceptional Conditions | Error handling, edge cases |

## Middleware Pipeline Sırası (FROZEN)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

Sıra değiştirilirse CSP nonce üretimi bozulur → GÜVENLİK AÇIĞI
