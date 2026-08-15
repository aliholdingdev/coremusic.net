---
type: architecture
category: auth
title: "Enterprise Auth — Authentication Flow"
date: 2026-08-09
updated: 2026-08-12
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Auth — Authentication Flow

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Authentication lifecycle'ı tanımlar: login, logout, session refresh, ve cross-domain auth akışları.

## 2. Login Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        LOGIN FLOW                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → herhangi bir subdomain'e erişir                 │
│     │                                                           │
│     ▼                                                           │
│  2. Subdomain → auth.coremusic.net'e redirect                   │
│     │                                                           │
│     ▼                                                           │
│  3. auth.coremusic.net/login sayfası gösterilir                 │
│     │                                                           │
│     ▼                                                           │
│  4. Kullanıcı email + şifre girer                               │
│     │                                                           │
│     ▼                                                           │
│  5. JavaScript → Form doğrulaması yapar (frontend)              │
│     │                                                           │
│     ▼                                                           │
│  6. POST /api/login → Auth Service                              │
│     │                                                           │
│     ├── Rate limit kontrolü (5 req/60s)                        │
│     │                                                           │
│     ├── UserRepository → findByEmail()                          │
│     │                                                           │
│     ├── Password verify (Argon2id)                              │
│     │                                                           │
│     ├── Session create (Server-side)                            │
│     │                                                           │
│     ├── Cookie set (HTTPOnly, Secure, SameSite=Lax)            │
│     │                                                           │
│     ▼                                                           │
│  7. 302 Redirect → Orijin subdomain'e                           │
│     │                                                           │
│     ▼                                                           │
│  8. Subdomain → Dashboard gösterilir                            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 3. Logout Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                       LOGOUT FLOW                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → Logout butonuna tıklar                          │
│     │                                                           │
│     ▼                                                           │
│  2. POST /api/logout → Auth Service                             │
│     │                                                           │
│     ├── Session invalidate (Server-side)                        │
│     │                                                           │
│     ├── Cookie clear (HTTPOnly, Secure, SameSite=Lax)          │
│     │                                                           │
│     ▼                                                           │
│  3. 302 Redirect → auth.coremusic.net/login                     │
│     │                                                           │
│     ▼                                                           │
│  4. Kullanıcı login sayfasına yönlendirilir                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 4. Session Validation Flow (Cross-Domain)

```
┌─────────────────────────────────────────────────────────────────┐
│                  SESSION VALIDATION FLOW                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → home.coremusic.net'e erişir                     │
│     │                                                           │
│     ▼                                                           │
│  2. home.coremusic.net → Session cookie kontrolü                │
│     │                                                           │
│     ├── Cookie yok → auth.coremusic.net/login'e redirect        │
│     │                                                           │
│     ├── Cookie var → auth.coremusic.net/api/session/check       │
│     │                                                           │
│     ▼                                                           │
│  3. Auth Service → Session doğrulama                            │
│     │                                                           │
│     ├── Session geçersiz → 401 UNAUTHORIZED                     │
│     │                                                           │
│     ├── Session süresi dolmuş → 401 UNAUTHORIZED                │
│     │                                                           │
│     ├── Session geçerli → Kullanıcı bilgisi döner               │
│     │                                                           │
│     ▼                                                           │
│  4. home.coremusic.net → Kullanıcı bilgisi ile devam            │
│     │                                                           │
│     ▼                                                           │
│  5. Dashboard gösterilir                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 5. Session Refresh Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                   SESSION REFRESH FLOW                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → Aktif olarak çalışırken                         │
│     │                                                           │
│     ▼                                                           │
│  2. JavaScript → Periyodik olarak session kontrolü               │
│     │                                                           │
│     ├── Her 5 dakikada bir → GET /api/session/check             │
│     │                                                           │
│     ▼                                                           │
│  3. Auth Service → Session son 5 dakika içinde mi?              │
│     │                                                           │
│     ├── Evet → Session geçerli, devam                           │
│     │                                                           │
│     ├── Hayır → POST /api/session/refresh                       │
│     │                                                           │
│     ▼                                                           │
│  4. Yeni session ID üretilir                                    │
│     │                                                           │
│     ▼                                                           │
│  5. Yeni cookie set edilir                                      │
│     │                                                           │
│     ▼                                                           │
│  6. Devam edilir                                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 6. Media Access Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                   MEDIA ACCESS FLOW                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → music.coremusic.net'de müzik dinlemek ister     │
│     │                                                           │
│     ▼                                                           │
│  2. music.coremusic.net → auth.coremusic.net'den token alır     │
│     │                                                           │
│     ▼                                                           │
│  3. Auth Service → Kullanıcının hakkını kontrol eder            │
│     │                                                           │
│     ├── Hakkı yok → 403 FORBIDDEN                               │
│     │                                                           │
│     ├── Hakkı var → Yetki token'ı üretir                        │
│     │                                                           │
│     ▼                                                           │
│  4. music.coremusic.net → media.coremusic.net'e istek atar      │
│     │                                                           │
│     ▼                                                           │
│  5. media.coremusic.net → Token doğrular                        │
│     │                                                           │
│     ├── Token geçersiz → 403 FORBIDDEN                          │
│     │                                                           │
│     ├── Token geçerli → Medya akışı başlatır                    │
│     │                                                           │
│     ▼                                                           │
│  6. Kullanıcı → Müzik dinler                                    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 7. Development Standalone Auth Callback Flow

**Kaynak:** [[ADR-058-cross-subdomain-auth-flow]]

Development modunda auth.coremusic.net yerel sunucuda çalışır (port 81, HTTP).

```
┌─────────────────────────────────────────────────────────────────┐
│              DEVELOPMENT STANDALONE AUTH FLOW                    │
│              (auth.coremusic.net local, port 81, HTTP)          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → home.coremusic.net:81'e erişir                  │
│     │                                                           │
│     ▼                                                           │
│  2. Cookie yok → auth.coremusic.net:81/login'e redirect         │
│     │              (?return_url=http://home.coremusic.net:81/)  │
│     ▼                                                           │
│  3. auth.coremusic.net:81/login sayfası gösterilir              │
│     │              (HTTP, Cookie Secure=false)                   │
│     ▼                                                           │
│  4. Kullanıcı email + şifre girer                               │
│     │                                                           │
│     ▼                                                           │
│  5. POST /api/login → Auth Service                               │
│     │                                                           │
│     ├── BypassAuth devre dışı (prod'da olduğu gibi)             │
│     │                                                           │
│     ├── Rate limit: dev'de de aktif (5 req/60s)                │
│     │                                                           │
│     ├── Argon2id password verify                                │
│     │                                                           │
│     ├── Session create (server-side)                            │
│     │                                                           │
│     ├── Session cookie set                                      │
│     │   (HttpOnly=true, Secure=false, SameSite=Lax)            │
│     │                                                           │
│     ├── JWT Access Token üretilir (15dk, RS256)                 │
│     │                                                           │
│     ├── JWT Refresh Token üretilir (7 gün, RS256)               │
│     │                                                           │
│     ├── auth_key cookie ayarla (Access JWT)                     │
│     │                                                           │
│     ├── refresh_key cookie ayarla (Refresh JWT)                 │
│     │                                                           │
│     ▼                                                           │
│  6. 302 Redirect → return_url                                   │
│     │              (http://home.coremusic.net:81/)              │
│     ▼                                                           │
│  7. home.coremusic.net:81 → auth_key cookie'yi okur             │
│     │                                                           │
│     ├── JWT'yi yerel olarak doğrula (API çağrısı YOK)           │
│     │   ├─ RS256 publicKey ile imza kontrolü                   │
│     │   ├─ exp claim süre kontrolü                              │
│     │   └─ Payload çıkar (user_id, role, email)                 │
│     │                                                           │
│     ├── Geçerli → $_SESSION['user_id'] = payload['user_id']    │
│     │             $_SESSION['role'] = payload['role']           │
│     │             Kullanıcı authenticated                       │
│     │                                                           │
│     └── Geçersiz → auth.coremusic.net:81/login'e redirect       │
│                                                                 │
│  8. Dashboard gösterilir                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

**Development vs Production Farkları:**

| Özellik | Development | Production |
|---------|-------------|------------|
| Protokol | HTTP | HTTPS |
| Cookie Secure | false | true |
| auth.coremusic.net port | 81 | 80/443 |
| Return URL | `http://*.coremusic.net:81/` | `https://*.coremusic.net/` |
| JWT Algorithm | RS256 (test key) | RS256 (prod key) |
| Rate Limit | Aktif (dev'de de) | Aktif |

## 8. Auth Lifecycle

```
Login
 │
 ▼
Validate Credentials
 │
 ▼
Password Verify (Argon2id)
 │
 ▼
Session Create (Server-side)
 │
 ▼
Session Cookie Set (HttpOnly, Secure, SameSite=Lax)
 │
 ▼
JWT Access Token Üret (15dk, RS256)
 │
 ▼
JWT Refresh Token Üret (7 gün, RS256)
 │
 ▼
auth_key Cookie Set (Access JWT)
 │
 ▼
refresh_key Cookie Set (Refresh JWT)
 │
 ▼
Authenticated
 │
 ▼
Session/JWT Check (Every Request)
 │
 ├── Access Token geçerli → Devam
 │
 └── Access Token süresi dolmuş → Refresh Token ile yenile
      │
      ├── Refresh Token geçerli → Yeni Access + Refresh üret
      │
      └── Refresh Token süresi dolmuş → auth.coremusic.net/login'e redirect
 │
 ▼
Logout
 │
 ▼
Destroy Session + Clear Cookies
 │
 ▼
Redirect to Login
```

## 8. Auth Security Layers

| Katman | KorumA | Teknoloji |
|--------|--------|-----------|
| **Frontend** | Form doğrulama | JavaScript |
| **Middleware** | Rate limiting | APCu |
| **Auth Service** | Password verify | Argon2id |
| **Session** | Server-side storage | PHP Session |
| **Cookie** | HTTPOnly, Secure | PHP ini_set |
| **CSRF** | Token doğrulama | hash_equals |
| **CORS** | Whitelist | Origin check |
| **RBAC** | Rol kontrolü | Permission matrix |

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Flows | 7 (Login, Logout, Validation, Refresh, Media, Dev Callback, JWT Refresh) |
| Security Layers | 8 |
| Clean Architecture | ✅

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
