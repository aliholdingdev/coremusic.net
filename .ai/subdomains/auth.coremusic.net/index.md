---
type: subdomain
title: "Auth Subdomain — auth.coremusic.net"
category: "authentication"
date: "2026-08-17"
updated: "2026-08-17"
status: "active"
version: "1.0.0"
authority: "SSOT"
references:
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[architecture/l1-security]]"
---

# Auth Subdomain — auth.coremusic.net

## 1. Genel Bakış

| Alan | Değer |
|------|-------|
| Entry Point | `auth.coremusic.net/index.php` |
| Port | 80 (default HTTP) |
| Stack | PHP 8.4, IMiddleware pipeline, 10 katman |
| Session Cookie | `COREMUSIC_SESS`, domain `.coremusic.net` |
| CSRF Token | `csrf_token` (frozen) |
| Gender Cookie | `cm_gender`, domain `.coremusic.net` |

## 2. Auth Flow

```
Root / → auth_key var mı?
  ├── EVET → validate → session → /home redirect
  └── HAYIR → /select-gender redirect

/select-gender → gender seç → POST /set-gender → /login redirect
/login → form doldur → POST /login → auth_key üret → redirect URL
redirect URL → auth_key query string'de → home.coremusic.net/auth/callback?auth_key=XXX
```

## 3. Entry Point Davranışları

| URL | Davranış |
|-----|----------|
| `/` | auth_key varsa validate, yoksa `/select-gender` redirect |
| `/login` (GET) | Gender gate → login formu |
| `/login` (POST) | AuthController::handleLogin() → auth_key → redirect |
| `/register` (GET) | Gender gate → register formu |
| `/register` (POST) | AuthController::handleRegister() → auth_key → redirect |
| `/set-gender` (POST) | AuthController::handleSetGender() → session + cookie |
| `/logout` (POST) | AuthController::handleLogout() → session destroy |
| `/validate-key` (POST/GET) | AuthController::handleValidateKey() → JSON |
| `/health` | Health check → JSON |
| `/session` | Session check → JSON |

## 4. CSRF Bypass

`set-gender` route'u `CsrfMiddleware` bypass listesinde. Gender seçimi hassas session change içermez.

## 5. Cinsiyet Gate

`/login` ve `/register` sayfalarına erişimden önce `cm_gender` session'da veya cookie'de olmalı. Yoksa `/select-gender`'a redirect.

## 6. Middleware Pipeline

10 katmanlı pipeline (ADR-010 frozen sıra):
```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

## 7. Güvenlik

- Argon2id password hashing (64MB/4/2)
- Rate limiting: 5 başarısız deneme / 15 dakika
- auth_key: 64-char hex, 300s TTL, tek kullanımlık (30s grace window)
- Session cookie: HttpOnly, SameSite=Lax
- CORS: OriginCheck + CorsMiddleware (whitelist tabanlı)

---

*Auth Subdomain v1.0.0 — CoreMusic Vault*
*Last Updated: 2026-08-17*
