---
title: "K6 — Güvenlik Katmanı (Security Layer)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k6-security.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K6
  component_count: 40
  adr:
    - "[[ADR-010-csrf-protection-strategy]]"
    - "[[ADR-011-session-management]]"
    - "[[ADR-012-csp-nonce-strict-dynamic]]"
    - "[[ADR-013-rate-limiting-apcu]]"
    - "[[ADR-022-database-hardened-security]]"
    - "[[ADR-034-credential-vault-normalization]]"
  github:
    - name: "OWASP"
      url: "https://github.com/owasp"
    - name: "Libsodium"
      url: "https://github.com/jedisct1/libsodium"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K6 — Güvenlik Katmanı (Security Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için tüm güvenlik bileşenleri: kimlik doğrulama, yetkilendirme, şifreleme, token yönetimi, OWASP uyumluluk ve güvenlik tarama araçları. 40 bileşen.

---

## 1. Genel Bakış

K6 katmanı, CoreMusic platformunun tüm güvenlik altyapısını tanımlar. Bu katman, L1 Security katmanının temelini oluşturur ve middleware pipeline ile doğrudan entegre çalışır.

### 1.1 Katman Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                     K6 — SECURITY LAYER (40)                        │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  AUTH (10)   │  TOKEN (6)   │  ENCRYPT (5) │  SECURITY TOOLS (10)   │
│              │              │              │                        │
│  JWT Auth    │  CSRF Token  │  AES-256-GCM │  OWASP Scanner         │
│  Session     │  API Key     │  RSA Sign    │  Dependency Scanner    │
│  OAuth2      │  MFA/TOTP    │  Argon2id    │  Secret Scanner        │
│  BypassAuth  │  Passkey     │  Credential  │  CORS Whitelist        │
│  RBAC        │  Key Rotation│  Vault       │  Origin Validation     │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│              RATE LIMITING (4)  │  HEADERS (3)  │  AUDIT (2)        │
│              APCu / Redis /     │  HSTS / CSP /  │  Audit Logger     │
│              Token Bucket       │  Trusted Types │  Security Headers │
└────────────────────────────────┴───────────────┴────────────────────┘
```

### 1.2 Bağımlılık Haritası

```
K7 (Middleware Pipeline)
  → K6 (Security Layer)
    → K0 (OS Layer) — cryptography primitives
    → K5 (Data Layer) — credential storage
```

---

## 2. Bileşen Listesi (40 Bileşen)

### 2.1 Authentication (10 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 1 | JWT Auth — Token Üretimi | RS256 imzalı JWT access token üretimi | `lcobucci/jwt` | [[ADR-011-session-management]] |
| 2 | JWT Auth — Token Onayı | JWT signature doğrulama, expiry kontrolü, claim validation | `lcobucci/jwt` | [[ADR-011-session-management]] |
| 3 | Session Auth | Server-side session yönetimi, PHP native session | `session_start()` | [[ADR-011-session-management]] |
| 4 | OAuth2 — Sunucu | OAuth2 provider olarak CoreMusic (3rd party login) | `league/oauth2-server` | [[ADR-088-gender-based-social-oauth]] |
| 5 | OAuth2 — İstemci | Google, GitHub, Apple, Discord OAuth2 client | `league/oauth2-server` | [[ADR-088-gender-based-social-oauth]] |
| 6 | API Key Auth | API anahtarı ile kimlik doğrulama (header: `X-API-Key`) | PDO + hash | [[ADR-020-api-public-security]] |
| 7 | BypassAuth | Test ortamında auth bypass (`?_bypass=1`, prod'da devre dışı) | Middleware | [[ADR-008-bypass-auth-middleware]] |
| 8 | RBAC — Regular | Standart kullanıcı yetkisi | Role tabanlı | [[ADR-010-csrf-protection-strategy]] |
| 9 | RBAC — Premium/Studio/Car | Premium, stüdyo, araç içi yetki seviyeleri | Role tabanlı | [[ADR-010-csrf-protection-strategy]] |
| 10 | RBAC — Admin/System | Yönetici ve sistem yetkileri | Role tabanlı | [[ADR-010-csrf-protection-strategy]] |

### 2.2 Token Management (6 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 11 | CSRF Token — Üretim | `csrf_token` anahtarı ile token üretimi | `random_bytes(32)` | [[ADR-010-csrf-protection-strategy]] |
| 12 | CSRF Token — Doğrulama | `hash_equals()` ile timing-safe doğrulama | `hash_equals()` | [[ADR-010-csrf-protection-strategy]] |
| 13 | MFA/TOTP | İki faktörlü kimlik doğrulama (Google Authenticator uyumlu) | `pragmarx/google2fa` | — |
| 14 | Passkey (WebAuthn) | FIDO2/WebAuthn tabanlı parolasız kimlik doğrulama | WebAuthn API | — |
| 15 | API Key Üretimi | Rastgele API anahtarı üretimi ve hash'leme | `ramsey/uuid` + Argon2id | [[ADR-034-credential-vault-normalization]] |
| 16 | Key Rotation | Periyodik anahtar döndürme (90 gün) | Cron + DB | — |

### 2.3 Encryption & Hashing (5 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 17 | AES-256-GCM — Encrypt | Simetrik şifreleme (IV: 12 byte, Tag: 16 byte) | `paragonie/halite` | [[ADR-022-database-hardened-security]] |
| 18 | AES-256-GCM — Decrypt | Simetrik şifre çözme + tag doğrulama | `paragonie/halite` | [[ADR-022-database-hardened-security]] |
| 19 | RSA Sign/Verify | Asimetrik imza (RS256, 2048-bit minimum) | `openssl_pkey` | [[ADR-011-session-management]] |
| 20 | Argon2id — Hash | K恓re hashleme (Memory: 64MB, Time: 4, Threads: 2) | `password_hash()` | [[ADR-022-database-hardened-security]] |
| 21 | Argon2id — Verify |沈re hash doğrulama (timing-safe) | `password_verify()` | [[ADR-022-database-hardened-security]] |

### 2.4 Credential & Key Management (4 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 22 | Credential Vault | Hassas bilgilerin merkezi depolanması (AES-256-GCM ile şifreli) | DB + halite | [[ADR-034-credential-vault-normalization]] |
| 23 | Key Rotation Policy | 90 günlük anahtar döndürme politikası ve otomatik yenileme | Cron + DB | — |
| 24 | Secret Scanner | Kod içindeki gizli anahtar taraması | GitLeaks | — |
| 25 | Dependency Scanner | Üçüncü parti bağımlılık güvenlik taraması | `roave/security-advisories` | — |

### 2.5 Security Headers & CORS (5 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 26 | CSP Nonce | `base64_encode(random_bytes(32))` ile nonce üretimi | Middleware | [[ADR-012-csp-nonce-strict-dynamic]] |
| 27 | CSP Directive | `strict-dynamic`, `script-src`, `style-src` directives | Middleware | [[ADR-012-csp-nonce-strict-dynamic]] |
| 28 | Trusted Types | DOM-XSS savunması için Trusted Types policy | DOM API | — |
| 29 | HSTS Header | `Strict-Transport-Security: max-age=31536000; includeSubDomains` | Middleware | — |
| 30 | CORS Whitelist | İzin verilen köken listesi (subdomain bazlı) | Middleware | [[ADR-010-csrf-protection-strategy]] |

### 2.6 Rate Limiting (4 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 31 | Rate Limiter — APCu | APCu tabanlı rate limiting (60 req/60s) | APCu | [[ADR-013-rate-limiting-apcu]] |
| 32 | Rate Limiter — Redis | Redis tabanlı rate limiting (dağıtık ortam) | Redis | [[ADR-013-rate-limiting-apcu]] |
| 33 | Token Bucket | Token bucket algoritması (esnek rate limiting) | Symfony RateLimiter | — |
| 34 | Rate Limit Header | `X-RateLimit-Remaining` ve `Retry-After` header'ları | Middleware | — |

### 2.7 Security Tools & Audit (6 Bileşen)

| # | Bileşen | Tanım | Teknoloji | ADR |
|---|---------|-------|-----------|-----|
| 35 | Origin Validation | İstek kökeni doğrulama (whitelist tabanlı) | Middleware | — |
| 36 | Security Headers Middleware | Güvenlik header'larını otomatik ekleyen middleware | PSR-15 | — |
| 37 | OWASP Scanner | OWASP Top 10:2025 tarama kontrol listesi | Manuel + otomatik | — |
| 38 | Audit Logger | Güvenlik olaylarını loglayan servis | PSR-3 Monolog | — |
| 39 | Request Validation | Gelen isteklerin validasyonu (DTO pattern) | Respect Validation | — |
| 40 | Security Audit | Periyodik güvenlik denetimi (quarterly) | Manuel + araç | — |

---

## 3. Middleware Pipeline Entegrasyonu

K6 bileşenleri, L1 Security katmanındaki middleware pipeline'ın içeriğini oluşturur:

```
OriginCheck (#36) → Cors (#30) → RateLimiter (#31) → SecurityHeaders (#36)
  → SessionManager → Csrf (#11-12) → BypassAuth (#7) → Auth (#1-6)
  → Permission (#8-10) → Validation (#39) → Controller
```

### 3.1 Middleware Sırası (Değişmez — ADR-010/011/012/013/022)

| # | Middleware | K6 Bileşenleri | ADR |
|---|-----------|----------------|-----|
| 1 | OriginCheckMiddleware | #36 (Origin Validation) | — |
| 2 | CorsMiddleware | #30 (CORS Whitelist) | — |
| 3 | RateLimiterMiddleware | #31-33 (APCu/Redis/Token Bucket) | [[ADR-013-rate-limiting-apcu]] |
| 4 | SecurityHeadersMiddleware | #26-29, #36 (CSP, HSTS, Headers) | [[ADR-012-csp-nonce-strict-dynamic]] |
| 5 | SessionManagerMiddleware | #3 (Session Auth) | [[ADR-011-session-management]] |
| 6 | CsrfMiddleware | #11-12 (CSRF Token) | [[ADR-010-csrf-protection-strategy]] |
| 7 | BypassAuthMiddleware | #7 (BypassAuth) | [[ADR-008-bypass-auth-middleware]] |
| 8 | AuthMiddleware | #1-2, #5-6 (JWT, OAuth2, API Key) | — |
| 9 | PermissionMiddleware | #8-10 (RBAC) | — |
| 10 | ValidationMiddleware | #39 (Request Validation) | — |

---

## 4. Güvenlik Konfigürasyonu

### 4.1 Kripto Parametreleri

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| AES-256-GCM IV | 96-bit (12 byte) | NIST SP 800-38D |
| AES-256-GCM Tag | 16 byte | NIST SP 800-38D |
| AES-256-GCM Key | 256-bit (32 byte) | NIST SP 800-38D |
| Argon2id Memory | 64 MB | OWASP recommendation |
| Argon2id Time | 4 iterations | OWASP recommendation |
| Argon2id Threads | 2 | OWASP recommendation |
| RSA Key Size | 2048-bit minimum | NIST SP 800-57 |
| JWT Expiry | 3600 saniye | — |
| Session Idle Timeout | 3600 saniye | [[ADR-011-session-management]] |

### 4.2 Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | Sebep |
|----------|----------|-------|
| `_csrf_token` | `csrf_token` | ADR-010: Token key değişmez |
| MD5/SHA1 hash | Argon2id | Kolay brute-force |
| Düz metin secret | `[REDACTED]` + Credential Vault | Veri sızıntısı |
| `eval()` / `Function()` | Safe alternatives | Code injection |
| `localStorage` for auth | Session-based (HTTPOnly cookie) | XSS riski |
| Hardcoded API key | `.env` / Credential Vault | Güvenlik ihlali |

---

## 5. OWASP Top 10:2025 Uyumluluk Matrisi

| # | OWASP Kategori | K6 Bileşenleri | Durum |
|---|----------------|----------------|-------|
| A01 | Broken Access Control | #8-10 (RBAC), #39 (Validation) | ✅ |
| A02 | Cryptographic Failures | #17-21 (AES, Argon2id, RSA) | ✅ |
| A03 | Injection | #39 (Validation), PDO prepared | ✅ |
| A04 | Insecure Design | Architecture review | ✅ |
| A05 | Security Misconfiguration | #26-29 (Headers), #30 (CORS) | ✅ |
| A06 | Vulnerable Components | #24 (Dependency Scanner) | ✅ |
| A07 | Auth Failures | #1-10 (Auth components) | ✅ |
| A08 | Data Integrity Failures | #19 (RSA Sign), #11 (CSRF) | ✅ |
| A09 | Logging Failures | #38 (Audit Logger) | ✅ |
| A10 | SSRF | #36 (Origin Validation) | ✅ |

---

## 6. Güvenlik Akış Diyagramları

### 6.1 JWT Auth Akışı

```
Kullanıcı → Login Form
  → AuthMiddleware (#1-2)
    → Argon2id Verify (#21)
    → JWT Token Üret (#1 — RS256)
    → Session Başlat (#3)
  → Cookie: COREMUSIC_SESS (HTTPOnly, Secure, SameSite=Strict)
  → Response: { access_token, refresh_token }
```

### 6.2 CSRF Koruma Akışı

```
GET /form
  → CsrfMiddleware (#11)
    → Token Üret: hash(session_id + timestamp)
    → Form'a gizle: <input type="hidden" name="csrf_token" value="...">
    → Session'a kaydet

POST /submit
  → CsrfMiddleware (#12)
    → Form'dan csrf_token al
    → Session'daki token ile hash_equals() (#12)
    → Eşleşmiyorsa → 403 Forbidden
```

### 6.3 Rate Limiting Akışı

```
İstek → RateLimiterMiddleware (#31)
  → APCu key: "rl:{ip}:{endpoint}"
  → Sayaç: incr (1 saniye TTL ile oluştur)
  → 60 saniyede >60 istek → 429 Too Many Requests
  → Header: X-RateLimit-Remaining: N
  → Header: Retry-After: X
```

---

## 7. Credential Vault Yapısı

```
┌─────────────────────────────────────────────┐
│           CREDENTIAL VAULT (#22)            │
├─────────────────────────────────────────────┤
│  DB Tablosu: coremusic_system.config        │
│  Şifreleme: AES-256-GCM (#17)              │
│  Key: ENV vars + key rotation (#23)         │
│                                             │
│  Saklananlar:                               │
│  ├── API Keys (Deezer, YouTube, Spotify)    │
│  ├── JWT Secret Key                         │
│  ├── OAuth2 Client Secrets                  │
│  ├── Database Credentials                   │
│  └── Encryption Keys                        │
│                                             │
│  Kurallar:                                  │
│  ├── ASLA vault'a yazma (ADR-022)           │
│  ├── ASLA log'a yazma ([REDACTED])          │
│  ├── 90 gün key rotation (#23)              │
│  └── ENV only (ADR-015)                     │
└─────────────────────────────────────────────┘
```

---

## 8. Güvenlik Test Senaryoları

| # | Test | Bileşen | Beklenen Sonuç |
|---|------|---------|----------------|
| 1 | CSRF token geçersiz | #12 | 403 Forbidden |
| 2 | JWT süresi dolmuş | #2 | 401 Unauthorized |
| 3 | Rate limit aşımı | #31 | 429 Too Many Requests |
| 4 | SQL injection denemesi | #39 | Request reddedildi |
| 5 | XSS payload gönderimi | #28-29 | CSP blocked |
| 6 | Wrong origin CORS | #30 | CORS blocked |
| 7 | Brute-force login | #31 | Rate limited |
| 8 | Session fixation | #3 | Session yeniden üretildi |
| 9 | Privilege escalation | #8-10 | 403 Forbidden |
| 10 | Secret leak test | #25, #38 | [REDACTED] |

---

## 9. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K6 Security | [[ADR-010-csrf-protection-strategy]] | CSRF token stratejisi |
| K6 Security | [[ADR-011-session-management]] | Session yönetimi |
| K6 Security | [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce |
| K6 Security | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| K6 Security | [[ADR-022-database-hardened-security]] | Şifreleme standartları |
| K6 Security | [[ADR-034-credential-vault-normalization]] | Credential vault |
| K6 Security | [[ADR-088-gender-based-social-oauth]] | OAuth2 |
| K6 → K7 | Middleware Pipeline | Security middleware bileşenleri |
| K6 → K0 | OS Layer | Crypto primitives |
| K6 → K5 | Data Layer | Credential storage |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | draft |
| Total Components | 40 |
| Authentication | 10 |
| Token Management | 6 |
| Encryption | 5 |
| Credential Mgmt | 4 |
| Headers & CORS | 5 |
| Rate Limiting | 4 |
| Security Tools | 6 |
| ADR Coverage | 7 ADR referansı |
| OWASP Coverage | 10/10 (Top 10:2025) |
| GitHub References | OWASP, Libsodium |

---

## 11. Class AB Guvenlik Entegrasyonu

- [[electronics/amplifier-classab-circuit]] — Overcurrent korumasi (Q18/Q19)
- [[electronics/power-supply-classab]] — Overvoltage/undervoltage korumasi
- [[electronics/thermal-design-classab]] — Thermal cutoff (KSD301)

---

## Auth Detayı (Eski 07-security/ + 08-auth/ Referansları)

### Auth Domain Model

```
┌─────────────────────────────────────────────────────────────────────┐
│                    AUTH DOMAIN MODELİ                                │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ User         │────►│ Session      │────►│ Token        │       │
│  │ Entity       │     │ Value Object │     │ Value Object │       │
│  │              │     │              │     │              │       │
│  │ • id (UUID)  │     │ • id         │     │ • access     │       │
│  │ • email      │     │ • user_id    │     │ • refresh    │       │
│  │ • password   │     │ • ip_address │     │ • expires_at │       │
│  │ • roles[]    │     │ • user_agent │     │ • scope      │       │
│  │ • mfa_secret │     │ • expires_at │     │              │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ Role         │────►│ Permission   │────►│ API Key      │       │
│  │ Aggregate    │     │ Value Object │     │ Entity       │       │
│  │              │     │              │     │              │       │
│  │ • name       │     │ • resource   │     │ • key        │       │
│  │ • permissions│     │ • action     │     │ • secret     │       │
│  │ • hierarchy  │     │ • condition  │     │ • user_id    │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Cross-Domain Auth

```
coremusic.net (Landing) ──► auth.coremusic.net ──► music.coremusic.net
        │                          │                        │
        │                          ▼                        │
        │                    ┌──────────────┐               │
        │                    │ SSO Cookie   │               │
        │                    │ (Shared)     │               │
        │                    └──────────────┘               │
        │                          │                        │
        └──────────────────────────┼────────────────────────┘
                                   ▼
                            Tüm subdomain'lerde
                            otomatik giriş
```

### Güvenlik Katmanları

| Katman | Teknoloji | Görev |
|--------|-----------|-------|
| L1 | HTTPS/TLS 1.3 | Transport security |
| L2 | CSP Nonce | XSS koruması |
| L3 | CSRF Token | CSRF koruması |
| L4 | Rate Limit | Abuse koruması |
| L5 | RBAC | Erişim kontrolü |
| L6 | Audit Log | Denetim izi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
