---
type: architecture
category: security
title: "Enterprise Authentication & Security Standards"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Authentication & Security Standards

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic platformunda kimlik doğrulama (Authentication), yetkilendirme (Authorization) ve güvenlik (Security) katmanları; **PSR standartlarına uygun**, **Composer üzerinden yönetilen**, **aktif olarak geliştirilen**, **güvenlik denetiminden geçmiş**, **Enterprise seviyede kabul görmüş** bileşenler kullanılarak geliştirilecektir.

## 2. Temel İlke

> **Öncelik sırası:** PHP Native → PSR Standardı → Composer Paketi → Kuruma özel Domain Logic

Sadece projeye özgü iş kuralları (Business Logic) özel olarak geliştirilecektir.

## 3. Hybrid Authentication Architecture

CoreMusic yalnızca Session veya yalnızca JWT kullanan bir sistem olmayacaktır.

```
                Browser
                    │
                    ▼
      HttpOnly Secure Session Cookie
                    │
                    ▼
            Access JWT Token (15min)
                    │
                    ▼
           Refresh JWT Token (long-lived)
                    │
                    ▼
         auth.coremusic.net
                    │
                    ▼
             Protected Services
```

## 4. Merkezi Authentication Sunucusu

Kimlik doğrulama işlemleri yalnızca **auth.coremusic.net** üzerinden gerçekleştirilecektir. Hiçbir servis kendi Login sistemini geliştirmeyecektir.

## 5. Authentication Kullanan Servisler

```
coremusic.net
music.coremusic.net
admin.coremusic.net
api.coremusic.net
media.coremusic.net
download.coremusic.net
home.coremusic.net
studio.coremusic.net
pro.coremusic.net
car.coremusic.net
```

## 6. Session Politikası

Session sistemi aşağıdaki özellikleri desteklemek zorundadır:

- HttpOnly Cookie
- Secure Cookie
- SameSite Cookie
- Session Rotation
- Session Regeneration
- Session Fingerprint
- Idle Timeout
- Absolute Timeout
- Device Binding
- IP Validation (Opsiyonel)
- User Agent Validation
- Session Revocation
- Session Invalidation
- Session Expiration
- Session Lock
- Session Audit Log

## 7. JWT Politikası

JWT sistemi aşağıdaki özellikleri desteklemek zorundadır:

- Short-Lived Access Token (15min)
- Long-Lived Refresh Token
- Token Rotation
- Token Revocation
- Token Blacklist
- Key Rotation
- Audience Validation
- Issuer Validation
- Subject Validation
- Signature Validation
- Expiration Validation
- Refresh Token Revocation
- Refresh Token Rotation
- Device Binding
- Token Versioning

## 8. Güvenlik Politikası

### 8.1 Authentication Security

- Argon2id (64MB/4/2)
- Password Rehash
- Password Policy
- Password History
- Password Expiration
- Account Lockout

### 8.2 Request Security

- CSRF Protection (`csrf_token`)
- CSP (nonce + strict-dynamic)
- HSTS
- CORS (Whitelist tabanlı)
- Origin Validation
- Referer Validation
- Host Validation

### 8.3 Injection Protection

- SQL Injection Protection (PDO prepared)
- XSS Protection (DOMParser + TrustedTypes)
- Command Injection Protection
- Path Traversal Protection
- SSRF Protection
- File Upload Validation

### 8.4 Session Security

- Session Fixation Protection
- Session Hijacking Protection
- Session Rotation
- Session Timeout
- Replay Attack Protection

### 8.5 API Security

- API Key Validation
- JWT Validation
- Rate Limiting
- Request Signing
- Nonce Validation
- Timestamp Validation

### 8.6 Cookie Security

- HttpOnly
- Secure
- SameSite
- Cookie Prefix
- Cookie Encryption

### 8.7 Audit

- Login Audit
- Logout Audit
- Failed Login Audit
- Password Reset Audit
- Session Audit
- Token Audit
- Security Event Audit

## 9. Cross-Origin (CORS) Politikası

### 9.1 Whitelist

Sadece aşağıdaki domainlere izin verilir:

```
https://coremusic.net
https://music.coremusic.net
https://admin.coremusic.net
https://api.coremusic.net
https://media.coremusic.net
https://download.coremusic.net
https://auth.coremusic.net
https://home.coremusic.net
https://studio.coremusic.net
https://pro.coremusic.net
https://car.coremusic.net
```

### 9.2 Development Mode

```
http://localhost:*
http://127.0.0.1:*
```

### 9.3 Port İzinleri

- 80 (HTTP)
- 81 (Music Service)
- 443 (HTTPS)
- 4433 (Alternate HTTPS)

## 10. Middleware Pipeline (Frozen Sıra)

```
HTTP Request
      │
      ▼
Origin Check
      │
      ▼
CORS
      │
      ▼
Rate Limit
      │
      ▼
Security Headers
      │
      ▼
Session
      │
      ▼
CSRF
      │
      ▼
Authentication
      │
      ▼
Authorization
      │
      ▼
Controller
```

## 11. RBAC Roller

| Rol | ID Aralığı | Yetki |
|-----|------------|-------|
| admin | 1000-1999 | Tam sistem yönetimi |
| ultra_user | 800-899 | Yüksek yetki |
| premium_user | 700-799 | Yüksek kalite, offline |
| streaming_user | 600-699 | Streaming erişimi |
| panel_user | 500-599 | Panel erişimi |
| free_user | 100-199 | Temel erişim |
| guest | 0 | Sadece genel |

## 12. Yasaklar

- Kendi JWT algoritmasını yazmak
- Kendi şifreleme algoritmasını yazmak
- Kendi Hash algoritmasını yazmak
- MD5 kullanmak
- SHA1 kullanmak
- mcrypt kullanmak
- Güvenliği kanıtlanmamış Composer paketleri kullanmak
- Bakımsız Composer paketleri kullanmak
- `SELECT *` kullanmak
- ORM kullanmak (Doctrine, Eloquent, Propel vb.)
- Güvenlik açısından kritik bileşenleri yeniden geliştirmek

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Güvenlik Katmanı** | 8 |
| **RBAC Rolü** | 7 |
| **CORS Whitelist** | 11 domain |
| **Port** | 4 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
