---
type: adr
category: security
title: "ADR-058 Cross-Subdomain Auth Flow & Development Mode"
date: 2026-08-09
version: 1.1.0
status: active
author: Bayram Ali
governance: Red Team · Human Mode · Truth Mode
---

# ADR-058: Cross-Subdomain Auth Flow & Development Mode

## Bağlam

CoreMusic platformunda auth callback flow development modda çalışmıyor. Tüm subdomain'lerin auth isteklerini desteklemesi gerekiyor. Mevcut referans projede sadece home.coremusic.net auth kullanıyor, diğer subdomain'ler boş.

## Karar

### 1. Merkezi Auth Sunucusu

Kimlik doğrulama işlemleri yalnızca `auth.coremusic.net` üzerinden gerçekleştirilecektir.

```
ÖNCEMLI SUBDOMAIN'LER (Faz 1):
home.coremusic.net
car.coremusic.net
pro.coremusic.net
studio.coremusic.net
media.coremusic.net
        │
        ▼
auth.coremusic.net
        │
        ▼
Login → Session + JWT → AuthKey → Callback → Redirect
```

### 2. Desteklenen Subdomain'ler

#### Faz 1 — Öncelikli (Şimdi yapılacak)

| Subdomain | Port | Auth Kullanımı | Öncelik |
|-----------|------|----------------|---------|
| home.coremusic.net | 81 (dev), 80/443 (prod) | ✅ Zorunlu | **YÜKSEK** |
| car.coremusic.net | 80 | ✅ Zorunlu | **YÜKSEK** |
| pro.coremusic.net | 81 (dev), 80/443 (prod) | ✅ Zorunlu | **YÜKSEK** |
| studio.coremusic.net | 81 (dev), 80/443 (prod) | ✅ Zorunlu | **YÜKSEK** |
| media.coremusic.net | 5000/6000 | ✅ Zorunlu | **YÜKSEK** |

#### Faz 2 — Sonra yapılacak

| Subdomain | Port | Auth Kullanımı | Öncelik |
|-----------|------|----------------|---------|
| music.coremusic.net | 81 (dev), 80/443 (prod) | ✅ Zorunlu | ORTA |
| admin.coremusic.net | 80 | ✅ Zorunlu | ORTA |
| api.coremusic.net | 81 (dev), 80/443 (prod) | ✅ Zorunlu | DÜŞÜK |
| download.coremusic.net | 3001 | ✅ Zorunlu | DÜŞÜK |

#### Landing Page (Auth gerektirmez)

| Subdomain | Port | Auth Kullanımı |
|-----------|------|----------------|
| coremusic.net | 80 | ❌ Landing page |

### 3. Desteklenen Portlar

| Port | Kullanım | Environment |
|------|----------|-------------|
| 80 | HTTP production | Production |
| 81 | HTTP development | Development |
| 443 | HTTPS production | Production |
| 4433 | HTTPS alternatif | Development |
| 5000/6000 | Media service | Her ikisi |
| 3001 | Download service | Her ikisi |
| 9741/9742 | Audio service | Her ikisi |

### 4. Development vs Production

| Özellik | Development | Production |
|---------|-------------|------------|
| Protocol | HTTP | HTTPS |
| Port | 81 (music), 80 (admin) | 80/443 |
| Cookie Secure | false | true |
| Cookie SameSite | Lax | Strict |
| JWT Verify | HS256 (test key) | RS256 (production key) |
| Rate Limit | Devre dışı | Aktif |
| BypassAuth | Aktif (`?_bypass=1`) | Devre dışı |

### 5. Auth Flow (Detaylı)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. Kullanıcı home or car or pro or studio or media .coremusic.net'de /login'e gider             │
│    → Front controller session kontrolü yapar                                                    │
│    → Session yok → Redirect to auth.coremusic.net/login                                         │
│      ?client_id=music                                                                           │
│      &redirect_uri=http://home or car or pro or studio or media .coremusic.net:81/auth/callback │
│      &state={random_token}                                                                      │
└──────────────────────────┬──────────────────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. auth.coremusic.net/login sayfası gösterilir                 │
│    → Login formu: username/email + password                     │
│    → CSRF token üret                                              │
│    → Client state kaydedilir (session'da)                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. Kullanıcı formu doldurur ve submit eder                     │
│    → POST /login (fetch + X-CSRF-Token header)                 │
│    → AuthController::handleLogin                                │
│    → AuthService::login                                         │
│      → pepper(HMAC-SHA256, APP_PEPPER)                         │
│      → Argon2id verify                                         │
│      → Rate limit kontrol (5 deneme/15dk)                      │
│      → SessionManager::setAuthUser($_SESSION)                  │
│      → Auth key üret (bin2hex(random_bytes(32)))               │
│    → { redirect: "http://auth.coremusic.net/?auth_key=..." }   │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. auth.coremusic.net/?auth_key=... sayfası                    │
│    → JS auth_key'i alır                                         │
│    → Redirect to client: redirect_uri?auth_key={key}&state={s} │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 5. home or car or pro or studio or media .coremusic.net/auth/callback?auth_key=...&state=...    │
│    → AuthCallbackController::handleCallback                                                     │
│    → AuthKeyService::validate(auth_key)                                                         │
│      → user_tokens tablosunda ara                                                               │
│      →.used_at kontrolü (tek kullanımlık)                                                       │
│      → expires_at kontrolü (5 dakika TTL)                                                       │
│    → Session oluştur                                                                            │
│    → JWT üret (Access + Refresh)                                                                │
│    → Cookie ayarla (COREMUSIC_SESS)                                                             │
│    → Auth key'i kullanıldı olarak işaretle                                                      │
│    → Redirect to /home veya fallback URL                                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 6. Auth Key Mekanizması

| Özellik | Değer |
|---------|-------|
| Üretim | `bin2hex(random_bytes(32))` = 64 hex karakter |
| TTL | 300 saniye (5 dakika) |
| Saklama | `user_tokens` tablosu (`token_type = 'auth_key'`) |
| Hash | SHA-256 ile hashlenmiş olarak saklanır |
| Kullanım | Tek kullanımlık (`used_at` field) |
| Validation | JOIN ile user bilgisi yüklenir |

### 7. Open Redirect Prevention

```php
private const ALLOWED_REDIRECT_HOSTS = [
    'music.coremusic.net',
    'admin.coremusic.net',
    'auth.coremusic.net',
    'home.coremusic.net',
    'studio.coremusic.net',
    'pro.coremusic.net',
    'car.coremusic.net',
    'api.coremusic.net',
    'media.coremusic.net',
    'coremusic.net',
    'localhost',
    '127.0.0.1',
];

private const ALLOWED_PORTS = [80, 443, 81, 4433, 3001, 5000, 6000, 9741, 9742, 9743];
```

### 8. Development Auth Bypass

Development ortamında `?_bypass=1` parametresi ile auth bypass aktif edilebilir.

```php
// BypassAuthMiddleware
if (APP_ENV === 'development' && isset($_GET['_bypass'])) {
    // Test kullanıcısı ile session oluştur
    $_SESSION['MM_UserID'] = 1;
    $_SESSION['MM_Username'] = 'testuser';
    // Devam et
}
```

**Production'da kesinlikle devre dışı.**

### 9. Session Sharing (Cross-Subdomain)

```php
// Tüm subdomain'ler ortak session kullanır
session_name('COREMUSIC_SESS');
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '.coremusic.net',  // Tüm subdomain'ler
    'secure' => true,              // HTTPS'de
    'httponly' => true,
    'samesite' => 'Lax',
]);
```

### 10. JWT Token Yapısı

```php
// Access Token (15 dakika)
$payload = [
    'iss' => 'auth.coremusic.net',
    'sub' => $userId,
    'iat' => time(),
    'exp' => time() + 900,        // 15 dakika
    'aud' => 'coremusic',
    'jti' => bin2hex(random_bytes(16)),
    'roles' => ['user'],
];

// Refresh Token (7 gün)
$payload = [
    'iss' => 'auth.coremusic.net',
    'sub' => $userId,
    'iat' => time(),
    'exp' => time() + 604800,     // 7 gün
    'aud' => 'coremusic',
    'jti' => bin2hex(random_bytes(16)),
];
```

## Sonuçlar

### Olumlu
- Tüm subdomain'ler tek auth sunucusuna bağlanır
- Development modda test edilebilir
- Production'da güvenlik sağlar
- Open redirect engellenir
- Session sharing çalışır

### Olumsuz
- Auth sunucusu çökerse tüm sistem etkilenir
- Cross-origin cookie paylaşımı gerektirir
- Stateful auth key mekanizması ek karmaşıklık yaratır

## İlgili ADR'ler

- [[ADR-043-auth-subdomain-consolidation]] — Auth subdomain konsolidasyonu
- [[ADR-052-hybrid-auth-architecture]] — Hybrid Auth (Session + JWT)
- [[ADR-010-csrf-protection-strategy]] — CSRF koruması
- [[ADR-011-session-management]] — Session yönetimi
- [[ADR-022-database-hardened-security]] — DB güvenlik

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
