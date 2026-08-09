# Security Standards — CoreMusic

**Authority:** ADR-010, ADR-011, ADR-012, ADR-013, ADR-022, ADR-052, ADR-058, ADR-059
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. OWASP Top 10:2025 Compliance

All OWASP categories addressed through architecture and middleware.

## 2. Authentication (ADR-011 + ADR-052 + ADR-058)

### Hybrid Auth Architecture

CoreMusic ne sadece Session ne de sadece JWT kullanır. **Her ikisinin kombinasyonunu** kullanır.

*Kaynak: [[ADR-052-hybrid-auth-architecture]]*

**Session:**
- Cookie: `COREMUSIC_SESS`, HttpOnly, Secure, SameSite=Lax
- Domain: `.coremusic.net` (tüm subdomain'ler)
- Idle Timeout: 3600s
- Absolute Timeout: 86400s (24h)
- Regeneration: `session_regenerate_id(true)` on login

**JWT:**
- Algorithm: RS256 (asymmetric)
- Access Token: 15 min TTL
- Refresh Token: 7 days TTL
- Key Rotation: 90 days
- Token Blacklist: Redis/APCu

**Password:**
- Argon2id (RFC 9106): Memory=64MB, Time=4, Threads=2
- Pepper: .env dosyasında

### Auth Subdomain Priority (ADR-058)

**Faz 1 — Öncelikli:**
home, car, pro, studio, media.coremusic.net

**Faz 2 — Sonra yapılacak:**
music, admin, api, download.coremusic.net

**Auth Flow:**
```
home/car/pro/studio/media.coremusic.net → auth.coremusic.net → Login → Session + JWT → Redirect
```

### Development Mode (ADR-058)

- HTTP (not HTTPS) on ports 80/81
- BypassAuth: `?_bypass=1` (development only)
- Cookie Secure: false
- Rate Limit: disabled

## 3. Authorization

- Role-based access control (RBAC)
- Session-bound auth state: `window.CoreMusic.RouterConfig.user`
- `localStorage` for auth: FORBIDDEN

## 4. CSRF Protection (ADR-010)

- Token key: `csrf_token` (NOT `_csrf_token` — removed 2026-05-30)
- Validation: `hash_equals()` (timing-safe)
- Update timing: AFTER DOM patch in SPA router

## 5. CSP (Content Security Policy) (ADR-012)

- Nonce-based: `base64_encode(random_bytes(32))`
- Policy: `strict-dynamic`
- Generated per-request by SessionManager middleware

## 6. Encryption (ADR-022)

- AES-256-GCM for credential vault (96-bit IV, 16-byte tag)
- ChaCha20-Poly1305 for alternative encryption
- All API keys in `credential_vault` table, encrypted

## 7. Rate Limiting (ADR-013)

- APCu-based, 60 requests per 60 seconds
- IP-based tracking
- Graceful degradation if APCu unavailable

## 8. Secrets Management

- All secrets in `.env` file
- Never in code, logs, or vault markdown files
- `[REDACTED]` masking in logs
- Credential vault: AES-256-GCM encrypted in DB

## 9. Input Validation

- Server-side validation for all inputs
- Whitelist validation preferred
- SQL injection prevention via prepared statements
- XSS prevention via DOMParser + TrustedTypes

## 10. Middleware Pipeline (Immutable Order)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

**Order is FROZEN** (ADR-010/011/012/013/022). Never change.

## 11. BypassAuth Middleware

- Fail-open risk: `getenv('APP_ENV') ?: 'production'`
- Test bypass only: `?_bypass=1` query parameter
- Production: ALWAYS disabled

## 12. Forbidden

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `_csrf_token` | `csrf_token` |
| Hardcoded secrets | `.env` / credential vault |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `localStorage` for auth | Session-based auth |
| HTTP (plain) | HTTPS (production) |
| `eval()` | Safe alternatives |
| SQL concatenation | Prepared statements |

---

*Security Standards v2.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-06*
