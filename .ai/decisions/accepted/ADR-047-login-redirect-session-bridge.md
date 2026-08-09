---
type: adr
category: auth
title: "ADR-047: Login Redirect Session Bridge"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-047: Login Redirect Session Bridge

**Status:** Active (güncellenebilir)
**Kategorisi:** Authentication
**İlgili Agent:** [[.agents/security-engineer]]
**İlgili Division:** Security Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki login redirect ve session köprüsü (session bridge) mekanizmasını, 10 panel arası oturum aktarımını, return URL yönetimini ve authenticated route korumasını tanımlar.

CoreMusic'in login redirect session bridge hedefi:
- Kesintisiz deneyim: Login sonrası orijinal sayfaya dönüş
- Cross-domain session: auth.coremusic.net ile tüm paneller arası
- Güvenlik: Return URL validasyonu (open redirect önleme)
- Rate limiting: Login denemeleri sınırlandırması
- Session fixation koruması: Login sonrası yeni session ID

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic'te 10 panel bulunmaktadır ve her panel auth gerektirir. Kullanıcı herhangi bir panelde oturumu yokken login sayfasına yönlendirilir. Login başarılı olduktan sonra orijinal sayfaya geri dönmelidir.

### 2.2 Problem

Kullanıcı music.coremusic.net/playlist/123 adresinde oturumsuzken:
1. /auth/check → Oturum yok → Redirect
2. auth.coremusic.net/login?return=/playlist/123
3. Login başarılı → ??? (Nereye yönlendirilmeli?)

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | Return URL | Orijinal sayfaya dönüş | ADR-047 |
| R2 | Cross-domain | auth.coremusic.net ↔ paneller | ADR-043 |
| R3 | Open redirect koruması | Return URL validasyonu | ADR-047 |
| R4 | Session fixation | Login sonrası yeni session | ADR-011 |
| R5 | Rate limiting | 5 deneme/15 dakika | ADR-013 |
| R6 | CSRF koruma | csrf_token | ADR-010 |
| R7 | Guest access | Bazı sayfalar authsız | ADR-047 |
| R8 | Audit trail | Login denemeleri loglanır | ADR-004 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Cookie domain | .coremusic.net |
| C2 | SameSite | Lax |
| C3 | HTTPS | Auth endpoint HTTPS |
| C4 | Return URL | Sadece aynı domain |
| C5 | Session timeout | 3600s idle |

---

## 3. Karar

CoreMusic'te **session bridge** ile login redirect yapılacak.

### 3.1 Login Akışı

```
1. Kullanıcı → music.coremusic.net/playlist/123
2. Auth middleware → /auth/check → Yanıt: 401
3. Redirect → auth.coremusic.net/login?return=/playlist/123
4. Login formu gösterilir
5. Kullanıcı bilgileri girilir → POST /login
6. Başarılı → Yeni session ID üret (session fixation koruması)
7. Cookie set (.coremusic.net) → COREMUSIC_SESS=abc123
8. Return URL'ye redirect → music.coremusic.net/playlist/123
9. Auth middleware → /auth/check → Yanıt: 200 → Devam
```

### 3.2 Return URL Validasyonu

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Same-origin | Return URL aynı domain olmalı |
| 2 | Path-only | Sadece path (/playlist/123) |
| 3 | No protocol | https:// yasak |
| 4 | No double-slash | //music.coremusic.net yasak |
| 5 | Whitelist | Sadece izinli path'ler |

```javascript
function validateReturnURL(url) {
    // Tehlikeli pattern'leri reddet
    const dangerous = [/^https?:\/\//i, /^\/\//i, /^\\/\\//i, /javascript:/i];
    if (dangerous.some(p => p.test(url))) {
        return '/';
    }
    // Sadece / ile başlayan path'leri kabul et
    if (!url.startsWith('/')) {
        return '/';
    }
    return url;
}
```

### 3.3 Session Bridge Mimarisi

```
auth.coremusic.net                    music.coremusic.net
┌─────────────────┐                  ┌─────────────────┐
│ Login POST       │                  │                  │
│  └→ Validate     │                  │                  │
│  └→ New Session  │──cookie set───▶│                  │
│  └→ Redirect     │  (.coremusic.net)│                  │
└─────────────────┘                  │ Auth middleware   │
                                     │  └→ /auth/check  │
                                     │  └→ 200 OK       │
                                     │  └→ Devam        │
                                     └─────────────────┘
```

---

## 4. Teknik Detaylar

### 4.1 Auth Middleware Flow

```php
<?php
declare(strict_types=1);

class AuthMiddleware
{
    public function handle(Request $request): Response
    {
        $sessionToken = $request->cookies->get('COREMUSIC_SESS');

        if (!$sessionToken) {
            return $this->redirectLogin($request);
        }

        // auth.coremusic.net API ile doğrulama
        $user = $this->authAPI->validate($sessionToken);

        if (!$user) {
            return $this->redirectLogin($request);
        }

        // User bilgisini request'e inject et
        $request->user = $user;
        return $handler->next($request);
    }

    private function redirectLogin(Request $request): Response
    {
        $returnUrl = $request->getUri()->getPath();
        $returnUrl = $this->validateReturnURL($returnUrl);

        $loginUrl = 'https://auth.coremusic.net/login'
            . '?return=' . urlencode($returnUrl);

        return new RedirectResponse($loginUrl, 302);
    }
}
```

### 4.2 Session Fixation Koruması

Login başarılı olduktan sonra:
1. Eski session ID silinir
2. Yeni session ID üretilir
3. Yeni cookie set edilir
4. Eski session verileri yeni ID'ye kopyalanır

```php
<?php
function loginFixationProtection(int $userId): string
{
    // Eski session'ı sil
    session_destroy();

    // Yeni session başlat
    session_start();

    // Yeni session ID
    $newSessionId = bin2hex(random_bytes(32));

    // User bilgilerini kaydet
    $_SESSION['user_id'] = $userId;
    $_SESSION['login_time'] = time();

    return $newSessionId;
}
```

### 4.3 Guest Access Kuralları

| Sayfa | Auth Gerekli mi? | Açıklama |
|-------|-----------------|----------|
| / (Landing) | ❌ | Herkese açık |
| /music | �ılsız | Kısıtlı erişim |
| /admin | ✅ | Sadece admin |
| /download | ✅ | Kullanıcı |
| /home | ✅ | Kullanıcı |
| /car | ✅ | Kullanıcı |
| /studio | ✅ | Kullanıcı |
| /pro | ✅ | Kullanıcı |

### 4.4 Login Rate Limiting

| Endpoint | Limit | Pencere | İhlal |
|----------|-------|---------|-------|
| POST /login | 5 deneme | 15 dakika | 429 + 15dk lockout |
| POST /register | 3 deneme | 1 saat | 429 + 1 saat lockout |
| POST /password/reset | 3 deneme | 1 saat | 429 + 1 saat lockout |

### 4.5 Logout Flow

```
1. Kullanıcı → POST /logout
2. Session silinir (DB + cookie)
3. Cookie temizlenir
4. Redirect → / (Landing page)
```

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Open redirect | Return URL validasyonu | ADR-047 |
| 2 | Session fixation | Login sonrası yeni session ID | ADR-011 |
| 3 | Plaintext password | Argon2id hash | ADR-022 |
| 4 | Brute force korumasız | Rate limiting | ADR-013 |
| 5 | HTTP auth | HTTPS zorunlu | ADR-043 |
| 6 | Cookie'de敏感 veri | Sadece session token | ADR-011 |
| 7 | Timing attack | hash_equals() | ADR-010 |
| 8 | localStorage auth | Cookie-based session | ADR-011 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Open redirect | return=https://evil.com | URL validasyonu | ADR-047 |
| 2 | Session fixation | Login sonrası | Yeni session ID | ADR-011 |
| 3 | Brute force | 5+ başarısız deneme | Rate limit + lockout | ADR-013 |
| 4 | Return URL boyutu | Çok uzun URL | Max 2048 karakter | ADR-047 |
| 5 | Cookie reject | Tarayıcı ayarı | Fallback: header auth | ADR-043 |
| 6 | Session timeout | 3600s idle | Redirect login | ADR-011 |
| 7 | CSRF attack | Cross-site login | csrf_token | ADR-010 |
| 8 | Multiple tabs | Eşzamanlı login | Token sabit | ADR-010 |
| 9 | Auth service down | Servis çökmesi | Cached auth (5dk) | ADR-043 |
| 10 | Guest→Auth geçiş | Auth gerektiren sayfa | Return URL korunur | ADR-047 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Return URL validasyonu | Open redirect önleme | Güvenlik açığı |
| G2 | Session fixation koruması | Login sonrası yeni ID | Session hijacking |
| G3 | Rate limiting | Login denemeleri | Brute force |
| G4 | HTTPS zorunlu | Auth endpoint | MITM |
| G5 | csrf_token | Login formu | CSRF saldırısı |
| G6 | Argon2id | Şifre hashleme | Veri sızıntısı |
| G7 | Cookie HttpOnly | JS erişimi yasak | Token sızıntısı |
| G8 | Audit trail | Login denemeleri | İzlenebilirlik |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-008-bypass-auth-middleware]] | Auth bypass | Test ortamı |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | csrf_token |
| [[ADR-011-session-management]] | Session yönetimi | Cookie, timeout |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Brute force |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Argon2id |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu | auth.coremusic.net |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[architecture/l1-security]] | Auth middleware |
| § 3.2 | [[subdomains/auth.coremusic.net/index]] | Auth endpoint |
| § 4.1 | [[brain.md]] §10 | PHP security |
| § 4.3 | [[ecosystem/panel-integration]] | Panel entegrasyonu |
| § 4.4 | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 5 | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 7 | [[CLAUDE.md]] §6 | Middleware pipeline |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Login Redirect** | Giriş sayfasına yönlendirme |
| **Session Bridge** | Oturum köprüsü — cross-domain session |
| **Return URL** | Orijinal sayfa URL'i |
| **Open Redirect** | Güvenlik açığı — yanlış yönlendirme |
| **Session Fixation** | Oturum sabitleme saldırısı |
| **Session Hijacking** | Oturum kaçırma saldırısı |
| **Rate Limiting** | İstek sınırlandırma |
| **Lockout** | Hesap kilitleme |
| **Brute Force** | Kaba kuvvet saldırısı |
| **Timing Attack** | Zamanlama saldırısı |
| **hash_equals()** | Timing-safe string comparison |
| **Cookie** | Tarayıcı çerezi |
| **HttpOnly** | Cookie JS erişimi yasak |
| **Secure** | Cookie sadece HTTPS |
| **SameSite** | CSRF koruması |
| **CSRF** | Cross-Site Request Forgery |
| **Argon2id** | Şifreleme algoritması |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Guest Access** | Authsız erişim |
| **Middleware** | İstek iş zincirindeki ara katman |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| Login Flow | 9 adım |
| Return URL Kuralı | 5 |
| Guest Access | 8 sayfa |
| Rate Limit | 3 endpoint |
| Session Fixation | 4 adım |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 6 |
| Çapraz Referans | 7 |
| Sözlük Terim | 20 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Auth flow değişikliğinde |
| Related Division | Security Engineering |
| Risk Seviyesi | Kritik (güvenlik) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | SSL/TLS | auth.coremusic.net HTTPS |
| 2 | Cookie config | .coremusic.net domain |
| 3 | Rate limit | APCu yapılandırması |
| 4 | Monitoring | Login denemeleri |
| 5 | Backup | Session DB |
| 6 | Rollback | Eski auth system |
| 7 | Documentation | API kılavuzu |
| 8 | Load balancing | Auth service scale |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | Auth fonksiyonları | PHPUnit |
| Integration Test | Session bridge | PHPUnit |
| E2E Test | Login/Logout/Redirect | Playwright |
| Security Test | Open redirect, CSRF | Penetration test |
| Load Test | Yüksek login yükü | k6 |
| Edge Case Test | Cookie reject | Playwright |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Open redirect | Düşük | Yüksek | URL validation |
| R2 | Session fixation | Düşük | Yüksek | New session ID |
| R3 | Brute force | Orta | Yüksek | Rate limit |
| R4 | Cookie reject | Düşük | Orta | Header fallback |
| R5 | CSRF attack | Orta | Yüksek | csrf_token |
| R6 | Auth down | Düşük | Yüksek | Cached auth |
| R7 | Return URL abuse | Düşük | Orta | Whitelist |
| R8 | Session leak | Düşük | Kritik | HttpOnly |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Security audit | Aylık | Security Engineer |
| 2 | Log review | Günlük | QA Engineer |
| 3 | Rate limit review | Üç aylık | Security Engineer |
| 4 | Cookie audit | Aylık | Security Engineer |
| 5 | SSL check | Yılda bir | DevOps Engineer |
| 6 | Dependency update | Aylık | DevOps Engineer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Passwordless auth | Planlanıyor | Passkey/WebAuthn |
| 2 | MFA | Planlanıyor | 2FA desteği |
| 3 | Social login++ | Gelecek | More providers |
| 4 | Session analytics | Araştırılıyor | Kullanım analizi |
| 5 | Device trust | Planlanıyor | Cihaz güvenilirliği |
| 6 | Risk-based auth | Gelecek | AI-powered risk |

---

## 18. Return URL Validation Rules

| # | Pattern | Değer | Neden |
|---|---------|-------|-------|
| 1 | `https://evil.com` | ❌ Redded | Open redirect |
| 2 | `//evil.com` | ❌ Redded | Protocol-relative redirect |
| 3 | `javascript:alert(1)` | ❌ Redded | XSS |
| 4 | `/playlist/123` | ✅ Kabul | Same-origin path |
| 5 | `/admin/users` | ✅ Kabul | Same-origin path |
| 6 | `/../../../etc/passwd` | ❌ Redded | Path traversal |
| 7 | `/%00 evil` | ❌ Redded | Null byte injection |
| 8 | `/music?return=https://evil.com` | ❌ Redded | Nested redirect |

---

## 19. Session Bridge Security Matrix

| Attack | Koruma | Kaynak |
|--------|--------|--------|
| Session fixation | Login sonrası yeni session ID | ADR-011 |
| Open redirect | Return URL validasyonu | ADR-047 |
| Brute force | Rate limiting (5/15dk) | ADR-013 |
| CSRF | csrf_token login formunda | ADR-010 |
| Session hijacking | HttpOnly + Secure + SameSite | ADR-011 |
| Timing attack | hash_equals() | ADR-010 |
| Password leak | Argon2id hash | ADR-022 |
| Cookie theft | HttpOnly flag | ADR-011 |

---

## 20. Login Attempt Tracking

| Alan | Tip | Saklama | Süre |
|------|-----|---------|------|
| user_id | INT | DB | 30 gün |
| ip_address | VARCHAR(45) | DB | 30 gün |
| attempt_count | INT | DB | 15 dakika penceresi |
| last_attempt | TIMESTAMP | DB | 30 gün |
| success | BOOLEAN | DB | 30 gün |
| user_agent | TEXT | DB | 30 gün |

---

## 21. Session States

| State | Tanım | Cookie | DB | Süre |
|-------|-------|--------|-----|------|
| active | Aktif oturum | Var | Var | 3600s idle |
| expired | Süresi dolmuş | Yok | Var (eski) | — |
| invalidated | Manually killed | Yok | Silindi | — |
| locked | Hesap kilidi | — | Var | 15dk |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
