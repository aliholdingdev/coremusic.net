---
type: architecture
category: contracts
title: "API Security Architecture"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Security Architecture

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[middleware-pipeline]] · [[ADR-010-csrf-protection-strategy]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic API güvenlik mimarisini, OWASP uyumluluğunu, Gateway güvenlik katmanlarını, token yönetimi ve credential management'i tanımlayan **Tek Doğruluk Kaynağıdır**.

## 2. OWASP Top 10:2025 Uyumluluğu

| # | OWASP Riski | CoreMusic Karşılığı | Katman |
|---|-------------|---------------------|--------|
| A01 | Broken Access Control (SSRF dahil) | RBAC + Permission Matrix + URL Allowlist | L1 |
| A02 | Security Misconfiguration | Hardened Defaults, Security Headers | L1 |
| A03 | Software Supply Chain Failures | Dependency Audit, Composer Audit, GitLeaks | CI/CD |
| A04 | Cryptographic Failures | AES-256-GCM, Argon2id, HTTPS, TLS 1.3 | L1 |
| A05 | Injection | Prepared Statement, Input Validation, DOMParser | L0 |
| A06 | Insecure Design | Threat Modeling, Security Review, DDD | Tasarım |
| A07 | Authentication Failures | Hybrid Auth (Session + JWT RS256), MFA, Lockout | L1 |
| A08 | Software/Data Integrity Failures | CSRF Token, Signed URLs, CI Integrity | L1 |
| A09 | Security Logging & Alerting Failures | Audit Trail, Structured Logging, Real-time Alerting | L0 |
| A10 | Mishandling of Exceptional Conditions | Error Hierarchy, Graceful Degradation, Circuit Breaker | Tüm Katmanlar |

*Kaynak: [[architecture/07-security/security/owasp-compliance]]*

## 3. API Gateway Security

### 3.1 Gateway Güvenlik Katmanları

```
┌─────────────────────────────────────────────────────────────┐
│                    SECURITY LAYERS                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Layer 1: Transport (HTTPS/HSTS)                             │
│  ├── TLS 1.3 zorunlu                                         │
│  ├── HSTS header (max-age=31536000)                          │
│  └── Certificate pinning (mobile)                            │
│                                                              │
│  Layer 2: Rate Limiting (APCu)                               │
│  ├── IP bazlı: 60 req/60s                                    │
│  ├── User bazlı: 120 req/60s                                 │
│  └── Endpoint bazlı: özel limitler                           │
│                                                              │
│  Layer 3: CORS                                              │
│  ├── Whitelist: 10 subdomain                                 │
│  ├── Origin doğrulama                                        │
│  └── Preflight cache (86400s)                                │
│                                                              │
│  Layer 4: Authentication                                     │
│  ├── JWT RS256 (service-to-service)                          │
│  ├── Session (browser clients)                               │
│  └── API Key (third-party)                                   │
│                                                              │
│  Layer 5: Authorization                                      │
│  ├── RBAC (7 rol)                                            │
│  ├── Permission matrix                                       │
│  └── Resource-level access                                   │
│                                                              │
│  Layer 6: Input Validation                                   │
│  ├── Request body validation                                 │
│  ├── Query parameter validation                              │
│  └── Header validation                                       │
│                                                              │
│  Layer 7: Output Encoding                                    │
│  ├── JSON response encoding                                  │
│  ├── XSS prevention                                          │
│  └── Content-Type enforcement                                │
│                                                              │
│  Layer 8: Security Headers                                  │
│  ├── CSP (strict-dynamic, nonce-based)                       │
│  ├── X-Frame-Options: DENY                                   │
│  ├── X-Content-Type-Options: nosniff                         │
│  └── Referrer-Policy: strict-origin                          │
│                                                              │
│  Layer 9: Audit Logging                                     │
│  ├── Tüm istekler loglanır                                  │
│  ├── Güvenlik olayları                                     │
│  └── Anomali tespiti                                        │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 CORS Whitelist (10 Subdomain)

| Subdomain | Origin | Credentials | Max-Age |
|-----------|--------|-------------|---------|
| `music.coremusic.net` | ✅ | ✅ | 86400 |
| `admin.coremusic.net` | ✅ | ✅ | 86400 |
| `auth.coremusic.net` | ✅ | ✅ | 86400 |
| `media.coremusic.net` | ✅ | ✅ | 86400 |
| `download.coremusic.net` | ✅ | ✅ | 86400 |
| `home.coremusic.net` | ✅ | ✅ | 86400 |
| `car.coremusic.net` | ✅ | ✅ | 86400 |
| `studio.coremusic.net` | ✅ | ✅ | 86400 |
| `pro.coremusic.net` | ✅ | ✅ | 86400 |
| `coremusic.net` | ✅ | ✅ | 86400 |

```php
$allowedOrigins = [
    'https://music.coremusic.net',
    'https://admin.coremusic.net',
    'https://auth.coremusic.net',
    'https://media.coremusic.net',
    'https://download.coremusic.net',
    'https://home.coremusic.net',
    'https://car.coremusic.net',
    'https://studio.coremusic.net',
    'https://pro.coremusic.net',
    'https://coremusic.net',
];
```

## 4. Token-Based Auth

### 4.1 JWT RS256 (Service-to-Service)

| Özellik | Değer |
|---------|-------|
| Algorithm | RS256 (RSA + SHA-256) |
| Public Key | `.env` / credential vault |
| Private Key | `.env` / credential vault |
| Expiry | 15 dakika |
| Refresh Token | 7 gün |
| Issuer | `coremusic.net` |
| Audience | Hedef servis adı |

```json
{
  "header": {
    "alg": "RS256",
    "typ": "JWT",
    "kid": "key-2026-08"
  },
  "payload": {
    "iss": "coremusic.net",
    "aud": "music-api",
    "sub": "user:12345",
    "exp": 1754764800,
    "iat": 1754763900,
    "roles": ["premium_user"],
    "permissions": ["music:read", "music:write"]
  }
}
```

### 4.2 API Key (Third-Party)

| Özellik | Değer |
|---------|-------|
| Format | `cm_live_` + 48 char hex |
| Storage | AES-256-GCM encrypted |
| Rotation | 90 günde bir |
| Rate Limit | 30 req/60s |
| Scope | Endpoint bazlı izin |

## 5. Input Validation

### 5.1 Validation Kuralları

| Input Tipi | Kurallar | Örnek |
|------------|----------|-------|
| `string` | max 255 char, HTML tags yok | Song title |
| `email` | RFC 5322 format, lowercase | user@example.com |
| `integer` | min/max range | 1 ≤ id ≤ 2147483647 |
| `uuid` | UUID v4 format | 550e8400-e29b-41d4-a716-446655440000 |
| `date` | ISO 8601 format | 2026-08-09T12:00:00Z |
| `enum` | Sadece tanımlı değerler | status: active/inactive |
| `json` | Valid JSON, max 1MB | Request body |
| `url` | Valid URL, HTTPS zorunlu | Redirect URL |

### 5.2 Validation Katmanı

```
Request → Gateway Validation → Controller Validation → Service Validation → Repository
             │                      │                       │
             ├── Schema check       ├── Business rules      ├── DB constraints
             ├── Type check         ├── Custom validators   ├── Unique check
             └── Size check         └── Sanitization        └── Foreign key
```

## 6. Output Encoding

| Durum | Encoding | Amaç |
|-------|----------|------|
| JSON response | `Content-Type: application/json; charset=utf-8` | MIME sniffer önleme |
| HTML response | `Content-Type: text/html; charset=utf-8` | XSS önleme |
| Error message | HTML entity encoding | Injection önleme |
| User input | Sanitize + encode | Stored XSS önleme |
| URL parameter | `urlencode()` | URL injection önleme |

## 7. Transport Security

### 7.1 HTTPS/HSTS

| Header | Değer | Amaç |
|--------|-------|------|
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains; preload` | HTTPS zorunlu |
| `X-Content-Type-Options` | `nosniff` | MIME sniffing önleme |
| `X-Frame-Options` | `DENY` | Clickjacking önleme |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Referrer sızıntısı |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=()` | Feature policy |

### 7.2 TLS Konfigürasyonu

```
TLS 1.3 (zorunlu)
├── Cipher Suite: TLS_AES_256_GCM_SHA384
├── Key Exchange: X25519
└── Certificate: RSA 2048+ veya ECDSA P-256+

TLS 1.2 (fallback)
├── Cipher Suite: TLS_ECDHE_RSA_WITH_AES_256_GCM_SHA384
└── Key Exchange: ECDHE
```

## 8. Request Size & Timeout

| Parametre | Değer | Uygulama |
|-----------|-------|----------|
| Max request body | 10 MB | Tüm endpoint'ler |
| Max file upload | 50 MB | Media upload |
| Max URL length | 2048 byte | Tüm istekler |
| Max header size | 8 KB | Tüm istekler |
| Connection timeout | 30 saniye | Gateway |
| Read timeout | 60 saniye | Servisler |
| Idle timeout | 300 saniye | WebSocket |

## 9. Secrets Management

### 9.1 Credential Hierarchy

| Seviye | Konum | Erişim |
|--------|-------|--------|
| L0 | `.env` dosyası | Sadece server |
| L1 | Credential vault (AES-256-GCM) | Encrypted |
| L2 | Environment variable | Runtime |
| L3 | `.gitignore` | Version control dışı |

### 9.2 Yasaklanan Yerler

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Kodda hardcoded secret | `.env` dosyası |
| Log'da düz metin key | `[REDACTED]` |
| Git'te secret | `.gitignore` |
| Client-side secret | Server-side only |
| Flat text password | AES-256-GCM encrypted |

## 10. Audit Logging

### 10.1 Loglanan Olaylar

| Olay | Log Seviyesi | Detay |
|------|-------------|-------|
| Başarılı login | INFO | IP, User-Agent, timestamp |
| Başarısız login | WARN | IP, reason, attempt count |
| Rate limit ihlali | WARN | IP, endpoint, count |
| CSRF token hatası | WARN | IP, endpoint |
| Yetkisiz erişim | ERROR | IP, user, endpoint |
| Güvenlik açığı tespiti | CRITICAL | IP, payload, endpoint |
| Admin işlemi | INFO | Admin ID, action, target |

### 10.2 Log Formatı

```json
{
  "timestamp": "2026-08-09T12:00:00Z",
  "level": "INFO",
  "service": "auth-api",
  "event": "login_success",
  "user_id": 12345,
  "ip": "192.168.1.100",
  "user_agent": "Mozilla/5.0...",
  "endpoint": "POST /api/v1/auth/login",
  "duration_ms": 45
}
```

## 11. Security Checklist

| # | Kontrol | Durum | Katman |
|---|---------|-------|--------|
| 1 | HTTPS zorunlu (HSTS) | ✅ | Transport |
| 2 | TLS 1.3 tercih ediliyor | ✅ | Transport |
| 3 | CORS whitelist tanımlı | ✅ | Gateway |
| 4 | Rate limiting aktif | ✅ | Gateway |
| 5 | Input validation uygulanıyor | ✅ | Gateway |
| 6 | Output encoding uygulanıyor | ✅ | Application |
| 7 | JWT RS256 kullanılıyor | ✅ | Auth |
| 8 | CSRF token koruması | ✅ | L1 |
| 9 | CSP nonce-based | ✅ | Headers |
| 10 | Secrets `.env`'da | ✅ | Config |
| 11 | Audit logging aktif | ✅ | Logging |
| 12 | Error'lar hassas veri içermiyor | ✅ | Error |
| 13 | SQL injection koruması (PDO) | ✅ | Database |
| 14 | XSS koruması (TrustedTypes) | ✅ | Frontend |
| 15 | Clickjacking koruması | ✅ | Headers |

## 12. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| HTTP (TLS'siz) | HTTPS zorunlu |
| `http://` redirect | `https://` redirect |
| CORS: `*` | CORS: whitelist |
| API Key kodda | Credential vault |
| JWT secret log'da | `[REDACTED]` |
| Validation'sız input | Multi-layer validation |
| Düz metin password | Argon2id hash |
| `eval()` input'ta | Safe parsing |
| `innerHTML` user data | DOMParser + TrustedTypes |
| `SELECT *` | Explicit columns |

## 13. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | HTTPS zorunlu | Sistem durdurulur |
| 2 | JWT RS256 zorunlu (service-to-service) | Auth bypass |
| 3 | CORS whitelist sadece 10 subdomain | Güvenlik açığı |
| 4 | Secrets asla kodda/log'da | Veri sızıntısı |
| 5 | Input validation her endpoint'te | Injection riski |
| 6 | CSRF token zorunlu (POST/PUT/DELETE) | CSRF saldırısı |
| 7 | CSP nonce-based zorunlu | XSS riski |
| 8 | Rate limiting zorunlu | DDoS riski |

## 14. Cross References

| Dosya | İlişki |
|-------|--------|
| [[api-architecture-master]] | Ana API mimarisi |
| [[api-design-rules]] | Tasarım kuralları |
| [[api-versioning]] | Sürüm yönetimi |
| [[api-authentication]] | Kimlik doğrulama |
| [[api-error-codes]] | Hata kodları |
| [[middleware-pipeline]] | Middleware sırası |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruması |
| [[ADR-022-database-hardened-security]] | Şifreleme |
| [[ADR-034-credential-vault-normalization]] | Credential vault |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 010, 022, 034, 042, 051, 052 |
| **OWASP Coverage** | ✅ A01-A10 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
