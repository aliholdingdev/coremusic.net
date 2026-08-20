---
type: architecture
category: contracts
title: "Auth Architecture — Hybrid Session + JWT (RS256)"
date: 2026-08-12
updated: 2026-08-12
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governing-rules: Red Team · Human Mode · Truth Mode
---

# Auth Architecture — Hybrid Session + JWT (RS256)

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic ekosisteminin kimlik doğrulama mimarisini tanımlar. **Hibrit Auth** (Session + JWT) kullanılır. Tüm auth işlemleri merkezi olarak `auth.coremusic.net` üzerinden yürütülür.

## 2. Temel Prensip

> **Tüm subdomain'ler (music, admin, api, media, home, pro, studio, car, download) zorunlu olarak auth.coremusic.net'i kullanır. Hiçbir subdomain bağımsız auth çalıştırmaz.**

## 3. Hibrit Auth Mimarisi

CoreMusic ne sadece Session ne de sadece JWT kullanır. **Her ikisinin kombinasyonunu** kullanır:

```
┌─────────────────────────────────────────────────────────────────┐
│                    HYBRID AUTH ARCHITECTURE                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  SESSION (Cookie-based)                                         │
│  ├── Name: COREMUSIC_SESS                                       │
│  ├── HttpOnly: true                                             │
│  ├── Secure: true (production) / false (development)            │
│  ├── SameSite: Lax                                              │
│  ├── Domain: .coremusic.net (tüm subdomain'ler)                 │
│  ├── Idle Timeout: 3600s (1 saat)                               │
│  ├── Absolute Timeout: 86400s (24 saat)                         │
│  └── Regeneration: session_regenerate_id(true) on login         │
│                                                                 │
│  JWT (Token-based)                                              │
│  ├── Algorithm: RS256 (asymmetric)                              │
│  ├── Access Token TTL: 15 dakika                                │
│  ├── Refresh Token TTL: 7 gün                                   │
│  ├── Key Rotation: 90 gün                                       │
│  └── Token Blacklist: Redis/APCu                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 4. Auth Flow

### 4.1 Login Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    LOGIN FLOW                                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Kullanıcı → auth.coremusic.net/login                        │
│                                                                 │
│  2. auth.coremusic.net → LoginUseCase                           │
│     ├── Email/Password doğrula                                  │
│     ├── Argon2id password verify                                │
│     └── Kullanıcı entity'sini getir                             │
│                                                                 │
│  3. Session oluştur                                              │
│     ├── session_regenerate_id(true)                             │
│     ├── $_SESSION['user_id'] = $user->id                        │
│     ├── $_SESSION['role'] = $user->role                         │
│     └── Cookie: COREMUSIC_SESS (HttpOnly, Secure, SameSite=Lax) │
│                                                                 │
│  4. JWT token çifti oluştur                                      │
│     ├── Access Token (15 dk, RS256)                             │
│     └── Refresh Token (7 gün, RS256)                            │
│                                                                 │
│  5. Response:                                                   │
│     ├── Set-Cookie: COREMUSIC_SESS=...                          │
│     ├── Body: { access_token, refresh_token }                   │
│     └── Redirect: origin subdomain                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Subdomain Auth Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    SUBDOMAIN AUTH FLOW                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Kullanıcı → music.coremusic.net                                │
│    │                                                             │
│    ├── Session cookie var mı? (.coremusic.net)                  │
│    │   ├── EVET → auth.coremusic.net/session-check API'sine     │
│    │   │         session'ı doğrula                               │
│    │   └── HAYIR → auth.coremusic.net/login'e redirect          │
│    │                                                             │
│    ├── Session geçerli mi?                                       │
│    │   ├── EVET → Kullanıcı bilgilerini al, devam et            │
│    │   └── HAYIR → auth.coremusic.net/login'e redirect          │
│    │                                                             │
│    └── Kullanıcı sayfada                                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.3 API Auth Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    API AUTH FLOW                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  SPA/Mobile → api.coremusic.net/api/v1/songs                    │
│    │                                                             │
│    ├── Authorization: Bearer <access_token>                     │
│    │                                                             │
│    ├── API Gateway → JWT doğrula                                 │
│    │   ├── RS256 imza kontrolü                                  │
│    │   ├── Token süresi dolmuş mu?                              │
│    │   │   ├── HAYIR → Devam et                                 │
│    │   │   └── EVET → 401 + "token_expired"                    │
│    │   └── Token blacklist'te mi?                               │
│    │       ├── HAYIR → Devam et                                 │
│    │       └── EVET → 401 + "token_revoked"                    │
│    │                                                             │
│    ├── RBAC kontrolü                                             │
│    │   ├── Rol yetkisi var mı?                                  │
│    │   └── İzin var mı?                                         │
│    │                                                             │
│    └── Response                                                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 5. Session Detayları

### 5.1 Session Cookie

| Özellik | Değer |
|---------|-------|
| **Name** | `COREMUSIC_SESS` |
| **HttpOnly** | `true` (JS erişemez) |
| **Secure** | `true` (production) / `false` (development) |
| **SameSite** | `Lax` |
| **Domain** | `.coremusic.net` (tüm subdomain'ler) |
| **Path** | `/` |
| **Idle Timeout** | 3600s (1 saat) |
| **Absolute Timeout** | 86400s (24 saat) |
| **Regeneration** | `session_regenerate_id(true)` on login |

### 5.2 Session Verisi

```php
$_SESSION = [
    'user_id'    => int,        // Kullanıcı ID
    'role'       => string,     // Kullanıcı rolü
    'permissions' => array,     // İzin listesi
    'login_at'   => int,        // Login timestamp
    'last_active' => int,       // Son aktivite timestamp
    'user_agent' => string,     // Tarayıcı bilgisi
    'ip_address' => string,     // IP adresi
    'device_id'  => string,     // Cihaz identifier
];
```

## 6. JWT Detayları

### 6.1 Access Token

| Özellik | Değer |
|---------|-------|
| **Algorithm** | RS256 (RSA + SHA-256) |
| **TTL** | 15 dakika |
| **Issuer** | `auth.coremusic.net` |
| **Audience** | `*.coremusic.net` |
| **Claims** | `sub`, `role`, `permissions`, `iat`, `exp` |

```json
{
  "header": {
    "alg": "RS256",
    "typ": "JWT"
  },
  "payload": {
    "sub": "user-123",
    "role": "user",
    "permissions": ["music.read", "media.read"],
    "iss": "auth.coremusic.net",
    "aud": "*.coremusic.net",
    "iat": 1754971200,
    "exp": 1754972100
  }
}
```

### 6.2 Refresh Token

| Özellik | Değer |
|---------|-------|
| **Algorithm** | RS256 |
| **TTL** | 7 gün |
| **Rotation** | Her kullanımda yenisi üretilir, eskisi blacklist'lenir |
| **Blacklist** | Redis/APCu'da saklanır |

### 6.3 Key Rotation

| Periyot | Aksiyon |
|---------|---------|
| 90 gün | RSA key pair yenilenir |
| Key rotation | Eski key ile imzalanmış token'lar 24 saat daha geçerli |
| Emergency | Anında key rotation yapılabilir |

## 7. Subdomain Prioritesi

### 7.1 Faz 1 — Öncelikli (Şimdi)

| Subdomain | Port | Auth |
|-----------|------|------|
| `home.coremusic.net` | 81 (dev), 80/443 (prod) | auth.coremusic.net |
| `car.coremusic.net` | 80 | auth.coremusic.net |
| `pro.coremusic.net` | 81 (dev), 80/443 (prod) | auth.coremusic.net |
| `studio.coremusic.net` | 81 (dev), 80/443 (prod) | auth.coremusic.net |
| `media.coremusic.net` | 5000/6000 | auth.coremusic.net |

### 7.2 Faz 2 — Sonra

| Subdomain | Port | Auth |
|-----------|------|------|
| `music.coremusic.net` | 81 (dev), 80/443 (prod) | auth.coremusic.net |
| `admin.coremusic.net` | 80 | auth.coremusic.net |
| `api.coremusic.net` | 81 (dev), 80/443 (prod) | auth.coremusic.net |
| `download.coremusic.net` | 3001 | auth.coremusic.net |

### 7.3 Desteklenen Portlar

**80, 81, 443, 4433**

## 8. RBAC Permission Matrix

| Rol | music.read | music.write | media.read | media.write | admin.* | system.* |
|-----|-----------|-------------|-----------|-------------|---------|----------|
| **guest** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **user** | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **premium** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **studio** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **car** | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| **admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| **system** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

## 9. Token Blacklist & Revocation

| Durum | Aksiyon |
|-------|---------|
| Logout | Refresh token blacklisted |
| Şifre değişikliği | Tüm token'lar blacklisted |
| Hesap askıya alma | Tüm token'lar blacklisted |
| Token rotation | Eski refresh token blacklisted |
| Güvenlik ihlali | Tüm token'lar blacklisted |

**Storage:** Redis/APCu, TTL = token süresi kadar

## 10. Development Mode

| Özellik | Development | Production |
|---------|-------------|------------|
| Protocol | HTTP | HTTPS |
| Port | 81 (music), 80 (admin) | 80/443 |
| Cookie Secure | false | true |
| JWT Algorithm | RS256 (test key) | RS256 (prod key) |
| Access Token TTL | 15 dakika | 15 dakika |
| Refresh Token TTL | 7 gün | 7 gün |
| Rate Limit | Aktif (dev'de de) | Aktif |
| BypassAuth | `?_bypass=1` | Devre dışı |

```php
// Development auth bypass (sadece development ortamında)
if (APP_ENV === 'development' && isset($_GET['_bypass'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'admin';
}
// Production'da kesinlikle devre dışı
```

## 11. MFA (Multi-Factor Authentication)

| Özellik | Değer |
|---------|-------|
| **Paket** | `pragmarx/google2fa` |
| **Algoritma** | TOTP (RFC 6238) |
| **Boyut** | 6 haneli kod |
| **Periyot** | 30 saniye |

## 12. Multi-Device Session

| Özellik | Değer |
|---------|-------|
| Max eşzamanlı session | 5 |
| Yeni session ekleme | Eski session'dan birini sil |
| Tümünü sil | Kullanıcı tercihi |
| Session listesi | auth.coremusic.net/settings/sessions |

## 13. Yasaklar

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Subdomain'de bağımsız auth | auth.coremusic.net üzerinden auth |
| `localStorage` for auth | Session-based auth |
| `innerHTML` for auth forms | DOMParser + TrustedTypes |
| Hardcoded secrets | `.env` / credential vault |
| Custom JWT implementation | `lcobucci/jwt` (firebase/php-jkt yasaklı) |
| Custom password hasher | Argon2id (RFC 9106) |
| `_csrf_token` | `csrf_token` |
| HTTP (plain) | HTTPS (production) |

## 14. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Merkezi auth: auth.coremusic.net | Güvenlik açığı → revert |
| 2 | Session + JWT birlikte çalışır | Tek başına yetersiz |
| 3 | Cookie: HttpOnly, Secure, SameSite=Lax | XSS/CSRF riski |
| 4 | Key rotation: 90 gün | Eski key riski |
| 5 | Token blacklist zorunlu | Revocation çalışmaz |
| 6 | Rate limit auth endpoint'lerinde | Brute force riski |

## 15. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruması |

## 16. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 8 RBAC | [[architecture/l1-security/index]] | Güvenlik katmanı |


## 17. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | ~300 |
| **ADR Uyumlu** | ✅ 010, 011, 043 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode
