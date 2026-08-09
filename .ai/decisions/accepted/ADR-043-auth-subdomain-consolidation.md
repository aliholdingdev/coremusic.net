---
type: adr
category: security
title: "ADR-043: Auth Subdomain Consolidation"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-043: Auth Subdomain Consolidation

**Status:** Active (güncellenebilir)
**Kategorisi:** Security
**İlgili Agent:** [[.agents/security-engineer]]
**İlgili Division:** Security Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki kimlik doğrulama (authentication) süreçlerinin tek bir auth.coremusic.net subdomain'inde konsolidasyonunu, cross-domain session mekanizmasını, RBAC (Role-Based Access Control) entegrasyonunu ve auth flow'unun tüm 10 panel ile nasıl entegre edileceğini tanımlar.

CoreMusic'in auth konsolidasyonu hedefi:
- Tek auth endpoint: auth.coremusic.net
- Unified flow: Login, register, password reset tek akışta
- Cross-domain session: 10 panel arası oturum paylaşımı
- RBAC: Rol bazlı erişim kontrolü
- Güvenlik: OWASP Top 10:2025 uyumluluğu

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic'te 10 farklı panel bulunmaktadır ve her panelin auth ihtiyacı vardır:

| # | Panel | Subdomain | Auth Durumu |
|---|-------|-----------|-------------|
| 1 | Landing | coremusic.net | Gerekli |
| 2 | Music | music.coremusic.net | Gerekli |
| 3 | Admin | admin.coremusic.net | Gerekli (yönetici) |
| 4 | Download | download.coremusic.net | Gerekli |
| 5 | Media | media.coremusic.net | Gerekli |
| 6 | Auth | auth.coremusic.net | Auth servisi |
| 7 | Home | home.coremusic.net | Gerekli |
| 8 | Car | car.coremusic.net | Gerekli |
| 9 | Studio | studio.coremusic.net | Gerekli |
| 10 | Pro | pro.coremusic.net | Gerekli |

### 2.2 Problemler

| # | Problem | Açıklama |
|---|---------|----------|
| P1 | Dağınık auth | Her panel kendi auth mekanizmasını kullanabilir |
| P2 | Session sharing | Cross-domain session paylaşımı zor |
| P3 | Bakım maliyeti | Birden fazla auth kodu = bakım yükü |
| P4 | Güvenlik açığı | Dağınık auth = yüzey alanı artışı |
| P5 | Tutarlılık eksikliği | Farklı panellerde farklı auth davranışları |

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | Tek endpoint | auth.coremusic.net | ADR-043 |
| R2 | Unified flow | Login/Register/Password Reset | ADR-043 |
| R3 | Cross-domain | 10 panel arası session | ADR-047 |
| R4 | RBAC | Rol bazlı erişim | ADR-043 |
| R5 | CSRF koruma | csrf_token key | ADR-010 |
| R6 | Session yönetimi | COREMUSIC_SESS, 3600s | ADR-011 |
| R7 | Rate limiting | 60 req/60s | ADR-013 |
| R8 | OWASP uyumlu | Top 10:2025 | ADR-043 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Cookie domain | .coremusic.net (tüm subdomain'ler) |
| C2 | SameSite | Lax veya Strict |
| C3 | HTTPS zorunlu | Auth endpoint HTTPS |
| C4 | CORS | Sadece izinli domain'ler |
| C5 | Token süresi | 3600s idle timeout |

---

## 3. Karar

CoreMusic'te **auth.coremusic.net** tek auth endpoint olacak.

### 3.1 Auth Flow Mimarisi

```
Kullanıcı → Panel (music.coremusic.net)
  → /auth/check (session var mı?)
    → Evet → Devam
    → Hayır → Redirect auth.coremusic.net/login?return=/music
      → Login formu
        → POST auth.coremusic.net/login
          → Başarılı → Cookie set (.coremusic.net) → Redirect return URL
          → Başarısız → Hata mesajı → Tekrar dene
```

### 3.2 Auth Endpoint'leri

| # | Endpoint | Method | Amaç |
|---|----------|--------|------|
| 1 | /login | GET | Login formu göster |
| 2 | /login | POST | Login doğrulama |
| 3 | /register | GET | Kayıt formu göster |
| 4 | /register | POST | Kayıt işleme |
| 5 | /logout | POST | Oturum kapatma |
| 6 | /password/reset | GET | Şifre sıfırlama formu |
| 7 | /password/reset | POST | Şifre sıfırlama işlemi |
| 8 | /check | GET | Session doğrulama (API) |
| 9 | /token/refresh | POST | Token yenileme |
| 10 | /oauth/{provider} | GET | Sosyal login redirect |
| 11 | /oauth/{provider}/callback | GET | Sosyal login callback |

### 3.3 Cookie Yapılandırması

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| Name | COREMUSIC_SESS | Session cookie adı |
| Domain | .coremusic.net | Tüm subdomain'ler |
| Path | / | Tüm yollar |
| Secure | true | Sadece HTTPS |
| HttpOnly | true | JS erişimi yasak |
| SameSite | Lax | CSRF koruması |
| MaxAge | 3600 | 1 saat idle timeout |

### 3.4 RBAC Roller

| # | Rol | Yetki | Panel Erişimi |
|---|-----|-------|---------------|
| 1 | guest | Temel erişim | Landing, Music (sınırlı) |
| 2 | user | Normal kullanıcı | Music, Download, Home, Car, Studio, Pro |
| 3 | premium | Üyelik | Tüm user panelleri + ek özellikler |
| 4 | admin | Yönetici | Admin + tüm paneller |
| 5 | super_admin | Süper yönetici | Tüm sistem |

### 3.5 Session Bridge (Cross-Domain)

```
auth.coremusic.net (set cookie .coremusic.net)
  ↓
music.coremusic.net (oku cookie, /auth/check ile doğrula)
  ↓
home.coremusic.net (aynı cookie, /auth/check ile doğrula)
  ↓
car.coremusic.net (aynı cookie, /auth/check ile doğrula)
```

---

## 4. Teknik Detaylar

### 4.1 Auth Middleware Pipeline

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

Her panel bu pipeline'ı kullanır. Auth middleware'ı:
1. Cookie'den session token'ı çıkarır
2. auth.coremusic.net'e API çağrısı yapar
3. Yanıt gelirse user bilgisini inject eder
4. Gelmezse login redirect

### 4.2 Şifreleme

| Parametre | Değer |
|-----------|-------|
| Hash Algorithm | Argon2id |
| Memory | 64MB |
| Time | 4 iterations |
| Threads | 2 |
| Pepper | .env dosyasında (credential vault) |

### 4.3 JWT Token Yapısı

```json
{
  "sub": "user_id",
  "email": "user@example.com",
  "roles": ["user", "premium"],
  "iat": 1691234567,
  "exp": 1691238167,
  "iss": "auth.coremusic.net"
}
```

### 4.4 Rate Limiting

| Endpoint | Limit | Pencere |
|----------|-------|---------|
| /login | 5 deneme | 15 dakika |
| /register | 3 deneme | 1 saat |
| /password/reset | 3 deneme | 1 saat |
| /check | 60 istek | 60 saniye |
| /token/refresh | 10 istek | 60 saniye |

### 4.5 Güvenlik Header'ları

| Header | Değer |
|--------|-------|
| Strict-Transport-Security | max-age=31536000; includeSubDomains |
| X-Content-Type-Options | nosniff |
| X-Frame-Options | DENY |
| X-XSS-Protection | 0 |
| Referrer-Policy | strict-origin-when-cross-origin |
| Content-Security-Policy | strict-dynamic, nonce-based |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | _csrf_token | csrf_token key | ADR-010 |
| 2 | localStorage'da auth token | Cookie-based session | ADR-011 |
| 3 | Plaintext password | Argon2id hash | ADR-022 |
| 4 | Hardcoded secret | .env / credential vault | ADR-034 |
| 5 | HTTP auth endpoint | HTTPS zorunlu | ADR-043 |
| 6 | Cookie'de敏感 veri | Sadece session token | ADR-011 |
| 7 | CORS wildcard | Sadece izinli domain'ler | ADR-043 |
| 8 | Brute force korumasız | Rate limiting zorunlu | ADR-013 |
| 9 | Session fixation | Yeni session ID login sonrası | ADR-011 |
| 10 | Timing attack | hash_equals() kullan | ADR-010 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Cookie rejection | Tarayıcı ayarı | Fallback: header-based auth | ADR-043 |
| 2 | Session fixation | Login sonrası | Yeni session ID üret | ADR-011 |
| 3 | Brute force | Şifre denemesi | Rate limit + account lockout | ADR-013 |
| 4 | CSRF attack | Cross-site istek | csrf_token doğrulama | ADR-010 |
| 5 | Token expiration | 3600s idle | Otomatik refresh | ADR-011 |
| 6 | Cross-domain cookie | SameSite sorunu | CSRF token header'da | ADR-010 |
| 7 | Multiple tabs | Eşzamanlı erişim | Token session-bound sabit | ADR-010 |
| 8 | Password leak | Güvenlik ihlali | Argon2id + pepper | ADR-022 |
| 9 | Session hijacking | Cookie çalınması | HttpOnly + Secure + SameSite | ADR-011 |
| 10 | Auth service down | Servis çökmesi | Fallback: cached auth (5dk) | ADR-043 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Tek auth endpoint | auth.coremusic.net | Auth分散liği |
| G2 | csrf_token key | _csrf_token yasak | CSRF bozulması |
| G3 | Argon2id zorunlu | Şifre hashleme | Güvenlik açığı |
| G4 | HttpOnly cookie | JS erişimi yasak | Token sızıntısı |
| G5 | HTTPS zorunlu | Auth endpoint'leri | MITM saldırısı |
| G6 | Rate limiting | Login denemeleri | Brute force |
| G7 | Session fixation koruması | Yeni session ID | Session hijacking |
| G8 | RBAC zorunlu | Her endpoint'te rol kontrolü | Yetkisiz erişim |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-008-bypass-auth-middleware]] | Auth bypass | Test ortamı |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | csrf_token key |
| [[ADR-011-session-management]] | Session yönetimi | Cookie ve timeout |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce | Security headers |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Brute force koruması |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Argon2id, AES-256-GCM |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Secret yönetimi |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect | Session bridge |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[architecture/l1-security]] | Middleware pipeline |
| § 3.3 | [[ADR-011-session-management]] | Cookie yapılandırması |
| § 3.4 | [[subdomains/auth.coremusic.net/index]] | Auth subdomain |
| § 4.1 | [[brain.md]] §10 | PHP security |
| § 4.2 | [[ADR-022-database-hardened-security]] | Şifreleme |
| § 4.4 | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 4.5 | [[ADR-012-csp-nonce-strict-dynamic]] | CSP headers |
| § 7 | [[CLAUDE.md]] §6 | Middleware pipeline |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Auth** | Authentication — Kimlik doğrulama |
| **RBAC** | Role-Based Access Control — Rol bazlı erişim |
| **Session** | Oturum — Kullanıcı oturum verisi |
| **Cookie** | Tarayıcı çerezi — Oturum depolama |
| **CSRF** | Cross-Site Request Forgery — Siteler arası sahte istek |
| **CORS** | Cross-Origin Resource Sharing — Kaynak paylaşımı |
| **JWT** | JSON Web Token — Dijital jeton |
| **Argon2id** | Şifreleme algoritması (64MB/4/2) |
| **SameSite** | Cookie SameSite policy — CSRF koruması |
| **HttpOnly** | Cookie JS erişimi yasak |
| **Secure** | Cookie sadece HTTPS |
| **Brute force** | Kaba kuvvet saldırısı |
| **Session fixation** | Oturum sabitleme saldırısı |
| **Session hijacking** | Oturum kaçırma saldırısı |
| **Timing attack** | Zamanlama saldırısı |
| **hash_equals()** | Timing-safe string comparison |
| **Pepper** | Ek şifreleme katmanı (server-side) |
| **Account lockout** | Hesap kilitleme |
| **Password reset** | Şifre sıfırlama |
| **Sosyal login** | OAuth tabanlı giriş |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| Auth Endpoint | 11 |
| RBAC Roller | 5 |
| Cookie Parametresi | 7 |
| Rate Limit Kuralı | 5 |
| Security Header | 6 |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 10 |
| İlgili ADR | 8 |
| Çapraz Referans | 8 |
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
| Next Review | OWASP güncellemesi olduğunda |
| Related Division | Security Engineering |
| Risk Seviyesi | Kritik (güvenlik) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | SSL/TLS | auth.coremusic.net HTTPS zorunlu |
| 2 | Cookie domain | .coremusic.net wildcard |
| 3 | CORS config | Sadece izinli domain'ler |
| 4 | Rate limit | APCu yapılandırması |
| 5 | Monitoring | Login denemeleri izleme |
| 6 | Backup | Session DB yedekleme |
| 7 | Rollback | Eski auth system |
| 8 | Documentation | API dokümantasyonu |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | Auth fonksiyonları | PHPUnit |
| Integration Test | Cross-domain session | PHPUnit |
| E2E Test | Login/Logout akışı | Playwright |
| Security Test | OWASP Top 10 | Penetration test |
| Load Test | Yüksek login yükü | k6 |
| Edge Case Test | Session fixation | PHPUnit |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Session fixation | Düşük | Yüksek | Yeni session ID |
| R2 | Brute force | Orta | Yüksek | Rate limit |
| R3 | Open redirect | Düşük | Yüksek | URL validation |
| R4 | Cookie theft | Düşük | Yüksek | HttpOnly + Secure |
| R5 | CSRF attack | Orta | Yüksek | csrf_token |
| R6 | Auth service down | Düşük | Yüksek | Cached auth |
| R7 | Password leak | Düşük | Kritik | Argon2id |
| R8 | Session hijacking | Düşük | Yüksek | SameSite |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Security audit | Aylık | Security Engineer |
| 2 | Log analizi | Günlük | QA Engineer |
| 3 | Rate limit review | Üç aylık | Security Engineer |
| 4 | Cookie rotation | İhtiyaca göre | Security Engineer |
| 5 | SSL renewal | Yılda bir | DevOps Engineer |
| 6 | Dependency update | Aylık | DevOps Engineer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | OAuth 2.1 | Planlanıyor | Güncel standart |
| 2 | Passkey support | Araştırılıyor | Şifresiz giriş |
| 3 | MFA entegrasyonu | Planlanıyor | Ek güvenlik |
| 4 | SAML federation | Gelecek | Kurumsal |
| 5 | Device fingerprint | Araştırılıyor | Fraud detection |
| 6 | Biometric auth | Gelecek | Parmak izi/ yüz |

---

## 18. Auth Flow Diagrams

### 18.1 Login Başarılı Akışı

```
Kullanıcı → music.coremusic.net/playlist/123
  → Auth middleware: Cookie yok → 302 Redirect
    → auth.coremusic.net/login?return=/playlist/123
      → Kullanıcı: email + password girer
        → POST /login (CSRF token doğrulandı)
          → Argon2id hash karşılaştır → Başarılı
            → Yeni session ID üret (session fixation koruması)
              → Cookie set (.coremusic.net, HttpOnly, Secure, SameSite=Lax)
                → Return URL'ye redirect → /playlist/123
                  → Auth middleware: /auth/check → 200 OK
                    → Devam
```

### 18.2 Login Başarısız Akışı

```
Kullanıcı → auth.coremusic.net/login
  → POST /login (yanlış şifre)
    → Rate limit kontrolü (5 deneme/15dk)
      → Başarısız → Hata mesajı + kalan deneme hakkı
        → 5. deneme → 15dk hesap kilidi
          → 429 Too Many Requests
```

### 18.3 Session Expired Akışı

```
Kullanıcı → music.coremusic.net (3600s+ idle)
  → Auth middleware: /auth/check → 401
    → Redirect → auth.coremusic.net/login?return=/current-page
      → Kullanıcı tekrar giriş yapar
        → Yeni session başlatılır
```

### 18.4 Logout Akışı

```
Kullanıcı → POST /logout
  → Session silinir (DB + cookie)
    → Cookie temizlenir
      → Redirect → / (Landing page)
```

---

## 19. Security Checklist

| # | Kontrol | Durum | Kaynak |
|---|---------|-------|--------|
| 1 | CSRF token login formunda | ✅ Zorunlu | ADR-010 |
| 2 | Rate limiting aktif | ✅ Zorunlu | ADR-013 |
| 3 | Session fixation koruması | ✅ Zorunlu | ADR-011 |
| 4 | Open redirect koruması | ✅ Zorunlu | ADR-047 |
| 5 | Brute force koruması | ✅ Zorunlu | ADR-013 |
| 6 | HTTPS auth endpoint | ✅ Zorunlu | ADR-043 |
| 7 | Cookie HttpOnly | ✅ Zorunlu | ADR-011 |
| 8 | Cookie Secure | ✅ Zorunlu | ADR-011 |
| 9 | Cookie SameSite | ✅ Zorunlu | ADR-011 |
| 10 | Argon2id hash | ✅ Zorunlu | ADR-022 |
| 11 | Pepper kullanılıyor | ✅ Zorunlu | ADR-022 |
| 12 | Timing-safe comparison | ✅ Zorunlu | ADR-010 |
| 13 | CORS restricted | ✅ Zorunlu | ADR-043 |
| 14 | HSTS header | ✅ Zorunlu | ADR-012 |
| 15 | CSP nonce | ✅ Zorunlu | ADR-012 |
| 16 | X-Frame-Options DENY | ✅ Zorunlu | ADR-012 |
| 17 | X-Content-Type-Options | ✅ Zorunlu | ADR-012 |
| 18 | Audit trail aktif | ✅ Zorunlu | ADR-004 |
| 19 | Password complexity | ✅ Zorunlu | ADR-043 |
| 20 | Account lockout | ✅ Zorunlu | ADR-043 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
