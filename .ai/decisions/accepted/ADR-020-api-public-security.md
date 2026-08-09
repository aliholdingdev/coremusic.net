---
type: adr
category: security
title: "ADR-020: API Public Security"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-020: API Public Security

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Security
**İlgili Agent:** [[.agents/security-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun tüm public API endpoint'lerinin güvenlik stratejisini tanımlar. Token-based authentication, rate limiting, CORS politikaları, HTTPS zorunluluğu ve OWASP Top 10:2025 uyumluluğunu kapsar. Tüm external ve internal API iletişimi bu kurallara tabidir.

Bu ADR şu alanları kapsar:
- Authentication mekanizmaları (API Key, JWT)
- Rate limiting stratejisi
- CORS politikası
- HTTPS zorunluluğu
- Security headers
- API versioning
- Error handling
- Audit logging
- Deployment kontrol listesi

---

## 2. Bağlam

CoreMusic, 10 panel ve 7 backend servisinden oluşan bir platformdur. API'ler;
- Panel arası iletişim (music.coremusic.net ↔ download.coremusic.net)
- External istemciler (mobil uygulama, masaüstü istemci)
- Third-party entegrasyonlar (Deezer, YouTube)
- Audio service iletişimi (port 9741/9742)
- Auth servisi (auth.coremusic.net)

Bu kadar geniş kapsamlı bir ekosistemde API güvenliği kritik öneme sahiptir. Tek bir açık tüm sistemi tehlikeye atabilir.

### 2.1 Tehdit Analizi

| Tehdit | Açıklama | Risk Seviyesi |
|--------|----------|---------------|
| Kimlik doğrulama bypass | Yetkisiz erişim | KRİTİK |
| Veri sızıntısı | Hassas veri ifşası | KRİTİK |
| DDoS saldırısı | Servis reddi | YÜKSEK |
| Man-in-the-middle | Ara dinleme | YÜKSEK |
| CORS bypass | Cross-site saldırı | ORTA |
| Brute force | Şifre deneme | YÜKSEK |
| Token reuse | Çalıntı token kullanımı | YÜKSEK |

### 2.2 Platform Gereksinimleri

| Gereksinim | Değer | Kaynak |
|------------|-------|--------|
| Authentication | API Key / JWT | ADR-020 |
| Authorization | RBAC | ADR-020 |
| Rate limiting | APCu tabanlı | ADR-013 |
| Encryption | TLS 1.3 | ADR-020 |
| CORS | Whitelist | ADR-020 |
| CSRF | Token-based | ADR-010 |
| Headers | Security middleware | ADR-012 |

---

## 3. Karar

CoreMusic'te **token-based API** güvenliği kullanılacak. Tüm API istekleri aşağıdaki güvenlik katmanlarından geçmek zorundadır.

| Katman | Mekanizma | Zorunlu mu? |
|--------|-----------|-------------|
| Kimlik Doğrulama | API Key / JWT | ✅ Evet |
| Yetkilendirme | RBAC (Role-Based) | ✅ Evet |
| Hız Sınırlaması | APCu tabanlı rate limiting | ✅ Evet |
| Şifreleme | TLS 1.3 (HTTPS) | ✅ Evet |
| Kök Erişimi | CORS whitelist | ✅ Evet |
| İstek Doğrulama | CSRF token (state-changing) | ✅ Evet |
| Header Güvenliği | Security headers middleware | ✅ Evet |

---

## 4. Teknik Detaylar

### 4.1 Authentication Mekanizması

#### 4.1.1 API Key Kullanımı

```
Header: X-API-Key: <api_key>
```

- API Key'ler AES-256-GCM ile şifrelenerek credential vault'ta saklanır
- Her kullanıcıya_unique API key üretilir
- API Key rotasyonu: 90 günde bir zorunlu
- Sızıntı durumunda derhal iptal edilir

#### 4.1.2 JWT Kullanımı

```
Header: Authorization: Bearer <jwt_token>
```

- JWT secret: AES-256-GCM ile şifreli (credential vault)
- Token süresi: 3600 saniye (1 saat)
- Refresh token: 7 gün
- Issuer: `auth.coremusic.net`
- Audience: `*.coremusic.net`

#### 4.1.3 Token Oluşturma Akışı

```
Kullanıcı giriş yapar
  → AuthController kimlik doğrulaması yapar
    → Argon2id ile şifre doğrulaması (64MB/4/2)
      → JWT token üretilir (imzalı, time-limited)
        → Token istemciye gönderilir
          → İstemci her istekte token'ı ekler
```

### 4.2 Rate Limiting

#### 4.2.1 Genel Rate Limit

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Algoritma | Sliding window | [[ADR-013-rate-limiting-apcu]] |
| Limit | 60 istek/60 saniye | APCu |
| Eşik | %80'de uyarı | Log WARN |
| Aşım | 429 Too Many Requests | HTTP response |
| Reset | `X-RateLimit-Reset` header | Saniye |

#### 4.2.2 Endpoint Bazlı Rate Limit

| Endpoint Grubu | Limit | Süre |
|----------------|-------|------|
| Auth (/auth/*) | 10 istek/dakika | Brute force koruması |
| API Genel (/api/*) | 60 istek/dakika | Standart |
| Download (/download/*) | 5 istek/dakika | Kaynak koruması |
| Admin (/admin/*) | 30 istek/dakika | Yüksek yetki |
| Media (/media/*) | 120 istek/dakika | Streaming optimizasyonu |

#### 4.2.3 Kullanıcı Bazlı Rate Limit

| Rol | Limit | Açıklama |
|-----|-------|----------|
| Anonymous | 20 istek/dakika | Kimlik doğrulamasız |
| User | 60 istek/dakika | Standart kullanıcı |
| Premium | 120 istek/dakika | Yüksek limit |
| Admin | 300 istek/dakika | Sistem yöneticisi |
| API Partner | Özelleştirilebilir | Sözleşme bazlı |

### 4.3 CORS Politikası

#### 4.3.1 İzin Verilen Kökler (Whitelist)

| Kök | Erişim | Açıklama |
|-----|--------|----------|
| `https://music.coremusic.net` | Tam | Ana medya paneli |
| `https://admin.coremusic.net` | Tam | Yönetim paneli |
| `https://download.coremusic.net` | Tam | İndirme servisi |
| `https://media.coremusic.net` | Tam | Medya servisi |
| `https://home.coremusic.net` | Tam | Ev merkezi |
| `https://car.coremusic.net` | Tam | Araç içi |
| `https://studio.coremusic.net` | Tam | Stüdyo |
| `https://pro.coremusic.net` | Tam | Profesyonel |
| `https://coremusic.net` | Tam | Landing |

#### 4.3.2 CORS Headers

```
Access-Control-Allow-Origin: https://music.coremusic.net
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-CSRF-Token
Access-Control-Allow-Credentials: true
Access-Control-Max-Age: 86400
```

#### 4.3.3 CORS Kuralları

| Kural | Değer | İhlal Sonucu |
|-------|-------|-------------|
| Origin doğrulaması | Exact match (regex yasak) | CORS bypass riski |
| Credentials | `true` sadece whitelisted kökler | Cookie sızıntısı |
| Methods | Sadece gerekli yöntemler | Attack yüzeyi artışı |
| Headers | Sadece gerekli header'lar | Injection riski |
| Max-Age | 24 saat | Preflight cache |

### 4.4 HTTPS Zorunluluğu

#### 4.4.1 TLS Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| Minimum versiyon | TLS 1.3 |
| Cipher suites | `TLS_AES_256_GCM_SHA384`, `TLS_CHACHA20_POLY1305_SHA256` |
| HSTS | `max-age=31536000; includeSubDomains; preload` |
| OCSP Stapling | Aktif |
| Certificate | Let's Encrypt (otomatik yenileme) |

#### 4.4.2 HTTP → HTTPS Redirect

```
Tüm HTTP istekleri (port 80) → HTTPS (port 443) redirect
301 Permanent Redirect
HSTS header ekle
```

### 4.5 Security Headers

#### 4.5.1 Zorunlu Header'lar

| Header | Değer | Amaç |
|--------|-------|------|
| `Content-Security-Policy` | strict-dynamic, nonce-based | XSS koruması |
| `X-Frame-Options` | DENY | Clickjacking koruması |
| `X-Content-Type-Options` | nosniff | MIME sniffing koruması |
| `Referrer-Policy` | strict-origin-when-cross-origin | Bilgi sızıntısı |
| `Permissions-Policy` | camera=(), microphone=(), geolocation=() | Feature policy |
| `Strict-Transport-Security` | max-age=31536000; includeSubDomains | SSL stripping |
| `X-XSS-Protection` | 0 | Eski XSS koruması (devre dışı) |

#### 4.5.2 CSP Nonce Üretimi

```php
$nonce = base64_encode(random_bytes(32));
// Header: Content-Security-Policy: script-src 'nonce-{nonce}' 'strict-dynamic'
```

CSP nonce'u `SessionManagerMiddleware`'de üretilir. Sıra değiştirilirse CSP bozulur.

#### 4.5.3 CSP Politika Detayları

| Direktif | Değer | Açıklama |
|----------|-------|----------|
| `default-src` | `'self'` | Varsayılan kaynak |
| `script-src` | `'nonce-{n}' 'strict-dynamic'` | Script kaynağı |
| `style-src` | `'self' 'unsafe-inline'` | CSS kaynağı |
| `img-src` | `'self' data: https:` | Görsel kaynağı |
| `font-src` | `'self'` | Font kaynağı |
| `connect-src` | `'self'` | API bağlantısı |
| `media-src` | `'self'` | Medya kaynağı |
| `object-src` | `'none'` | Plugin yasak |
| `frame-src` | `'none'` | Frame yasak |
| `base-uri` | `'self'` | Base URL |
| `form-action` | `'self'` | Form action |
| `upgrade-insecure-requests` | — | HTTP→HTTPS upgrade |

#### 4.5.4 CSP Raporlama

```php
// CSP violation report endpoint
// Header: Content-Security-Policy-Report-Only: ...; report-uri /csp-report
```

CSP ihbarları `/csp-report` endpoint'ine gönderilir ve loglanır.

### 4.6 API Versioning

#### 4.6.1 Version Stratejisi

| Strateji | Kullanım | Örnek |
|----------|----------|-------|
| URL path | Birincil | `/api/v1/songs` |
| Header | İkincil | `Accept: application/vnd.coremusic.v1+json` |
| Query param | Desteklenmiyor | — |

#### 4.6.2 Version Lifecycle

| Aşama | Süre | Aksiyon |
|-------|------|---------|
| Active | 12 ay | Tam destek |
| Deprecated | 6 ay | Uyarı header'ı |
| Sunset | 3 ay | 410 Gone response |
| Removed | — | Endpoint kaldırılır |

### 4.7 Error Handling

#### 4.7.1 Güvenli Hata Formatı

```json
{
  "error": {
    "code": "AUTH_INVALID_TOKEN",
    "message": "Geçersiz veya süresi dolmuş token",
    "status": 401,
    "timestamp": "2026-08-08T12:00:00Z"
  }
}
```

#### 4.7.2 Yasak Bilgiler

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Stack trace | Genel hata mesajı |
| SQL query | "Veritabanı hatası" |
| Dosya yolu | "Sistem hatası" |
| Internal IP | "Sunucu hatası" |
| Version bilgisi | Gizli |

### 4.8 Audit Logging

#### 4.8.1 Loglanan Olaylar

| Olay | Seviye | Aksiyon |
|------|--------|---------|
| Başarılı auth | INFO | `AUTH_SUCCESS` |
| Başarısız auth | WARN | `AUTH_FAILURE` |
| Rate limit aşımı | WARN | `RATE_LIMIT_EXCEEDED` |
| Geçersiz token | WARN | `INVALID_TOKEN` |
| Güvenlik açığı | CRITICAL | `SECURITY_BREACH` |
| API key sızıntısı | CRITICAL | `API_KEY_LEAK` |

#### 4.8.2 Log Formatı

```
[2026-08-08 12:00:00] [INFO] [security-engineer] [AUTH_SUCCESS] Kullanıcı #1234 authenticated (IP: [REDACTED])
```

Hassas veriler ASLA loglanmaz: `password`, `api_key`, `secret`, `token` → `[REDACTED]`.

### 4.9 Test Senaryoları

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Security;

use PHPUnit\Framework\TestCase;

class ApiSecurityTest extends TestCase
{
    public function testHttpRedirectsToHttps(): void
    {
        // HTTP istekleri HTTPS'e redirect edilmeli
        $response = $this->makeRequest('http://music.coremusic.net/api/v1/songs');
        $this->assertEquals(301, $response['status']);
        $this->assertStringContainsString('https://', $response['headers']['Location']);
    }

    public function testCorsRejectsUnknownOrigin(): void
    {
        // Bilinmeyen origin CORS ile engellenmeli
        $response = $this->makeRequest('https://music.coremusic.net/api/v1/songs', [
            'Origin' => 'https://evil.com',
        ]);
        $this->assertArrayNotHasKey('Access-Control-Allow-Origin', $response['headers']);
    }

    public function testRateLimitReturns429(): void
    {
        // Rate limit aşıldığında 429 dönmeli
        for ($i = 0; $i < 61; $i++) {
            $this->makeRequest('https://music.coremusic.net/api/v1/songs');
        }
        $response = $this->makeRequest('https://music.coremusic.net/api/v1/songs');
        $this->assertEquals(429, $response['status']);
    }

    public function testSecurityHeadersPresent(): void
    {
        // Security headers mevcut olmalı
        $response = $this->makeRequest('https://music.coremusic.net/');
        $this->assertArrayHasKey('Content-Security-Policy', $response['headers']);
        $this->assertArrayHasKey('X-Frame-Options', $response['headers']);
        $this->assertArrayHasKey('Strict-Transport-Security', $response['headers']);
    }

    public function testApiVersionRequired(): void
    {
        // API version zorunlu
        $response = $this->makeRequest('https://music.coremusic.net/api/songs');
        $this->assertEquals(404, $response['status']);
    }
}
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| `_csrf_token` header'da | `csrf_token` (ADR-010) | CSRF bypass |
| HTTP (TLS olmayan) | HTTPS zorunlu | Veri sızıntısı |
| wildcard CORS (`*`) | Exact origin match | Yetkisiz erişim |
| API key kodda hardcoded | Credential vault | Secret sızıntısı |
| `SELECT *` API yanıtında | Explicit field selection | Veri sızıntısı |
| Debug mode production'da | Debug devre dışı | Bilgi ifşası |
| Error'da stack trace | Genel mesaj | Attack bilgisi |
| Token log'da düz metin | `[REDACTED]` | Token sızıntısı |
| Rate limit olmayan endpoint | Tüm endpoint'lerde aktif | DDoS riski |
| JWT secret kodda | AES-256-GCM şifreli | Token sahteciliği |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Token süresi doldu | 3600s geçti | 401 + refresh token denemesi |
| Rate limit aşımı | 60+ istek/dakika | 429 + `Retry-After` header |
| CORS reddi | Whitelist dışı kök | CORS hatası, istek reddedilir |
| API key sızıntısı | Güvenlik ihbarı | Derhal key iptal, yeniden üretim |
| TLS sertifika hatası | Süresi dolmuş sertifika | Otomatik yenileme (Let's Encrypt) |
| Brute force saldırısı | 10+ başarısız auth/dk | Account lockout (15 dk) |
| JWT token çalıntı | Token reuse tespiti | Token iptal, tüm session'lar sıfırlanır |
| Double submit CSRF | Eşzamanlı form | Token session-bound sabit |
| Header injection | Yeni satır karakterleri | Header sanitizasyonu |
| API version sunset | Eski version kullanımı | 410 Gone + migration rehberi |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | HTTPS zorunlu — HTTP istekleri kabul edilmez | Derhal redirect (301) |
| 2 | CORS whitelist — Sadece bilinen kökler | CORS hatası |
| 3 | Rate limit aktif — Tüm endpoint'lerde | DDoS riski |
| 4 | Token log'da REDACTED — ASLA düz metin | Güvenlik ihlali |
| 5 | CSRF token key = `csrf_token` — `_csrf_token` yasak | CSRF bypass |
| 6 | JWT secret credential vault'ta — Kodda yasak | Token sahteciliği |
| 7 | Error'da stack trace yasak — Genel mesaj | Bilgi ifşası |
| 8 | TLS 1.3 minimum — Eski versiyonlar yasak | Zayıf şifreleme |
| 9 | API key rotasyonu — 90 günde bir | Sızıntı riski |
| 10 | Brute force koruması — 10 başarısız hesap kilidi | Yetkisiz erişim |

---

## 8. Deployment Kontrol Listesi

### 8.1 Pre-Deployment Kontrolleri

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | TLS sertifikası aktif ve geçerli | ☐ |
| 2 | CORS whitelist doğru yapılandırılmış | ☐ |
| 3 | Rate limiting aktif ve test edilmiş | ☐ |
| 4 | CSRF token key doğru (`csrf_token`) | ☐ |
| 5 | JWT secret credential vault'ta | ☐ |
| 6 | Security headers middleware aktif | ☐ |
| 7 | CSP nonce üretimi çalışıyor | ☐ |
| 8 | Brute force koruması aktif | ☐ |
| 9 | API key rotasyonu planlanmış | ☐ |
| 10 | Audit logging aktif | ☐ |
| 11 | Error handling test edilmiş | ☐ |
| 12 | Rate limit testleri geçmiş | ☐ |
| 13 | CORS testleri geçmiş | ☐ |
| 14 | HTTPS redirect çalışıyor | ☐ |
| 15 | HSTS header aktif | ☐ |

### 8.2 Post-Deployment Kontrolleri

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | Health check endpoint çalışıyor | ☐ |
| 2 | API response süreleri hedeflerde | ☐ |
| 3 | Rate limit log'ları görünüyor | ☐ |
| 4 | Auth log'ları doğru çalışıyor | ☐ |
| 5 | CORS testleri production'da | ☐ |
| 6 | SSL Labs testi A+ | ☐ |
| 7 | OWASP ZAP taraması temiz | ☐ |
| 8 | Error rate <%1 | ☐ |
| 9 | Uptime %99.9+ | ☐ |

### 8.3 Rollback Prosedürü

| Adım | Aksiyon | Sorumlu |
|------|---------|---------|
| 1 | Sorun tespit edildi | Monitoring |
| 2 | Rollback kararı | Tech Lead |
| 3 | Eski versiyona dön | DevOps |
| 4 | Health check | DevOps |
| 5 | Kullanıcıya bildirim | Support |
| 6 | Root cause analizi | Security Engineer |
| 7 | Düzeltme planı | Security Engineer |
| 8 | Tekrar deployment | DevOps |

---

## 9. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi | CSRF token kullanımı |
| [[ADR-011-session-management]] | Session yönetimi | Session-based auth |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce | CSP header üretimi |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | APCu tabanlı limit |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware | Audio API güvenliği |
| [[ADR-019-per-os-neva-player]] | Per-OS player | Cross-platform API |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Şifreleme standartları |
| [[ADR-026-download-service-architecture]] | Download servisi | Download API endpointleri |
| [[ADR-028-anti-ban-system]] | Anti-ban | API abuse koruması |
| [[ADR-034-credential-vault-normalization]] | Credential vault | API key saklama |
| [[ADR-039-7-service-platform-architecture]] | 7 servis | Servisler arası API |
| [[ADR-040-database-authority]] | DB authority | Veri güvenliği |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu | Auth endpoint'leri |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect | Auth akışı |

---

## 10. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-010-csrf-protection-strategy]] | CSRF token key |
| § 3 Karar | [[ADR-011-session-management]] | Session-based auth |
| § 3 Karar | [[ADR-022-database-hardened-security]] | Encryption standards |
| § 4.1 Auth | [[ADR-022-database-hardened-security]] | Argon2id, AES-256-GCM |
| § 4.1 Auth | [[ADR-034-credential-vault-normalization]] | Secret storage |
| § 4.2 Rate | [[ADR-013-rate-limiting-apcu]] | APCu rate limit |
| § 4.3 CORS | [[architecture/l1-security]] | Middleware pipeline |
| § 4.3 CORS | [[ADR-043-auth-subdomain-consolidation]] | Auth domain |
| § 4.4 HTTPS | [[architecture/l2-routing]] | Port ve protokol |
| § 4.5 Headers | [[ADR-012-csp-nonce-strict-dynamic]] | CSP nonce |
| § 4.6 API Version | [[ADR-039-7-service-platform-architecture]] | Service API |
| § 4.8 Audit | [[ADR-004-multi-domain-spa]] | Log formatı |
| § 4.8 Audit | [[ADR-005-ultrathink-protocol]] | Audit quality |
| § 5 Yasak | [[ADR-002-pdo-mandatory-no-orm]] | SQL injection koruması |
| § 5 Yasak | [[ADR-001-vanilla-js-itcss]] | Frontend security |
| § 6 Edge Cases | [[ADR-026-download-service-architecture]] | Download security |
| § 6 Edge Cases | [[ADR-028-anti-ban-system]] | API abuse |
| § 6 Edge Cases | [[ADR-017-dsp-hardware-mode]] | Audio service security |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[ADR-034-credential-vault-normalization]] | Secret yönetimi |
| § 8 ADR | [[ADR-047-login-redirect-session-bridge]] | Login flow |

---

## 11. Sözlük

| Terim | Tanım |
|-------|-------|
| **API Key** | API istemcilerini tanımlayan benzersiz anahtar |
| **JWT** | JSON Web Token — stateless kimlik doğrulama tokenı |
| **RBAC** | Role-Based Access Control — Rol bazlı erişim kontrolü |
| **CORS** | Cross-Origin Resource Sharing — Kökler arası kaynak paylaşımı |
| **HSTS** | HTTP Strict Transport Security — Zorunlu HTTPS |
| **CSP** | Content Security Policy — İçerik güvenlik politikası |
| **CSRF** | Cross-Site Request Forgery — Siteler arası istek sahteciliği |
| **TLS** | Transport Layer Security — İletişim şifreleme protokolü |
| **OCSP** | Online Certificate Status Protocol — Sertifika durum sorgulama |
| **Sliding Window** | Hareketli pencere — Rate limiting algoritması |
| **Brute Force** | Kaba kuvvet saldırısı — Şifre deneme saldırısı |
| **DDoS** | Distributed Denial of Service — Dağıtık hizmet reddi |
| **OWASP** | Open Web Application Security Project — Güvenlik standartları |
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **AES-256-GCM** | Advanced Encryption Standard, 256-bit, Galois/Counter Mode |
| **Argon2id** | Şifreleme algoritması (64MB/4/2) |
| **Redaction** | Hassas veri maskelenme (`[REDACTED]`) |
| **SSL Labs** | SSL sertifika kalite test aracı |
| **OWASP ZAP** | Zed Attack Proxy — Güvenlik tarama aracı |
| **Penetration Test** | Sızma testi — Güvenlik açığı arama |
| **Attack Surface** | Saldırı yüzeyi — Erişilebilir noktalar |
| **Zero Trust** | Sıfır güven — Her istek doğrulanmalı |
| **Defense in Depth** | Derinlemesine savunma — Çok katmanlı koruma |
| **Security Audit** | Güvenlik denetimi — Periyodik kontrol |

---

## 12. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 12 |
| SSOT Authority | ADR-020 API Public Security |
| Last Updated | 2026-08-08 |
| ADR References | 14 |
| Cross References | 18 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 24 |
| Authentication Methods | 2 (API Key, JWT) |
| Rate Limit Tiers | 5 (Anonymous → API Partner) |
| CORS Whitelist | 9 kök |
| Security Headers | 7 |
| Error Codes | 5 |
| Audit Events | 6 |
| Test Senaryosu | 5 |

---

## 13. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode