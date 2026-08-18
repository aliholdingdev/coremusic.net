---
type: template
category: security
title: "Security Audit Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: OWASP Top 10, AES-256-GCM, Argon2id, CSP, CSRF
---

# Security Audit Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-010-csrf-protection-strategy]] · [[ADR-022-database-hardened-security]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic ekosistemindeki tüm servislerin (10 panel + 7 backend servis) güvenlik denetimleri için standart bir format sağlar.

### 1.1 Kapsam Alanı

- Backend API (PHP 8.4, port 81)
- Frontend (Vanilla JS, TrustedTypes)
- Veritabanı (18 BCNF, MySQL 9)
- Auth subdomain (auth.coremusic.net)
- Download Service (Node.js, port 3001)
- Media Service (PHP + FFmpeg, port 5000/6000)
- Audio Service (C++20, port 9741/9742)
- Credential Vault (AES-256-GCM)
- Middleware Pipeline (10 katman)
- Session Management (COREMUSIC_SESS)

### 1.2 Kapsam Dışı

- Donanım güvenliği (fiziksel erişim) → ADR-038
- C++ Audio Engine zero-allocation kuralları → ADR-017
- Deployment pipeline detayları → DevOps Engineer

### 1.3 Denetim Tetikleyicileri

| Tetikleyici | Sıklık | Sorumlu |
|-------------|--------|---------|
| Yeni servis ekleme | Her ekleme | Security Engineer |
| Güvenlik yaması | 48 saat içinde | DevOps Engineer |
| Kritik kod değişikliği | Her PR | QA Engineer |
| Periyodik denetim | 3 ayda bir | Security Engineer |
| Güvenlik olayı | Anlık | Security Engineer |
| Third-party güncelleme | Her güncelleme | DevOps Engineer |

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak | İlgili ADR |
|-----------|---------|----------|--------|------------|
| OWASP Top 10 | 2025 | Güvenlik standardı | owasp.org | — |
| CWE | — | Zafiyet enumerasyonu | cwe.mitre.org | — |
| AES-256-GCM | NIST SP 800-38D | Credential şifreleme | OpenSSL | [[ADR-022]] |
| Argon2id | RFC 9106 | Şifre hashleme | PHP password_hash | [[ADR-022]] |
| CSRF Token | — | Cross-site koruma | Custom | [[ADR-010]] |
| CSP Nonce | — | XSS koruması | Custom | [[ADR-012]] |
| APCu | — | Rate limiting | PHP extension | [[ADR-013]] |
| PHPUnit | 11 | Güvenlik testleri | composer | — |
| Playwright | 1.40 | E2E güvenlik testleri | npm | — |

*Kaynak: OWASP Top 10 2025 (owasp.org) — 2026-08-06'da doğrulandı*

---

## 3. Code Standards

### 3.1 Audit Scope Definition

Her güvenlik denetimi şu alanları kapsamalıdır:

**Denetim Alanları:**

| # | Alan | Kapsam | Öncelik |
|---|------|--------|---------|
| 1 | Authentication | Kimlik doğrulama mekanizmaları | CRITICAL |
| 2 | Authorization | Yetkilendirme ve erişim kontrolü | CRITICAL |
| 3 | CSRF | Cross-Site Request Forgery koruması | HIGH |
| 4 | CSP | Content Security Policy politikası | HIGH |
| 5 | SQL Injection | Veritabanı enjeksiyon koruması | CRITICAL |
| 6 | XSS | Cross-Site Scripting koruması | HIGH |
| 7 | Session | Oturum yönetimi güvenliği | CRITICAL |
| 8 | Encryption | Şifreleme standartları | CRITICAL |
| 9 | Rate Limiting | Hız sınırlama mekanizması | MEDIUM |
| 10 | Dependencies | Bağımlılık güvenliği | HIGH |
| 11 | Log Security | Log güvenliği ve redaction | MEDIUM |
| 12 | Infrastructure | Altyapı güvenliği | HIGH |

**Denetim Sıklığı:**

| Tür | Sıklık | Süre |
|-----|--------|------|
| Tam denetim | 3 ayda bir | 2-3 gün |
| Parçalı denetim | Her sprint sonu | 2-4 saat |
| Acil denetim | Güvenlik olayında | 1-2 saat |
| Bağımlılık taraması | Haftalık | 30 dakika |

### 3.2 OWASP Top 10:2025 Checklist

#### A01:2025 Broken Access Control
- [ ] RBAC (Role-Based Access Control) uygulanmış
- [ ] Deny-by-default politikası aktif
- [ ] CORS politikası tanımlı ve kısıtlı
- [ ] JWT token doğrulama aktif
- [ ] IDOR (Insecure Direct Object Reference) koruması var
- [ ] Directory traversal koruması var
- [ ] Metadata manipülasyon koruması var
- [ ] Function-level access control mevcut
- [ ] SSRF koruması var (URL allowlist)

#### A02:2025 Security Misconfiguration
- [ ] Hata mesajları kullanıcıya hassas bilgi göstermiyor
- [ ] Güvenlik header'ları aktif (CSP, HSTS, X-Frame-Options)
- [ ] Default credential'lar değiştirilmiş
- [ ] Gereksiz özellikler devre dışı
- [ ] Error handling yapılandırılmış
- [ ] Directory listing devre dışı
- [ ] Server banner gizlenmiş

#### A03:2025 Software Supply Chain Failures
- [ ] `composer audit` çalıştırılmış ve sonuçlar temiz
- [ ] `npm audit` çalıştırılmış ve sonuçlar temiz
- [ ] Bilinen güvenlik açıkları kontrol edilmiş
- [ ] Eski bağımlılıklar güncellenmiş
- [ ] Software Bill of Materials (SBOM) mevcut
- [ ] GitLeaks pre-commit aktif
- [ ] Dependency pinning uygulanmış

#### A04:2025 Cryptographic Failures
- [ ] AES-256-GCM ile credential şifreleme aktif
- [ ] Argon2id parametreleri RFC 9106 uyumlu (Memory: 64MB, Time: 4, Threads: 2)
- [ ] Hardcoded secret yok (API key, password, JWT secret)
- [ ] TLS 1.3 zorunlu (HTTP değil HTTPS)
- [ ] Key rotation stratejisi tanımlı
- [ ] IV generation güvenli (96-bit rastgele)
- [ ] Deprecated algoritma kullanımı yok (MD5, SHA1, DES)

#### A05:2025 Injection
- [ ] PDO prepared statement kullanımı %100
- [ ] SELECT * kullanımı yok (açık sütun listesi)
- [ ] ORM kullanımı yok (ADR-002)
- [ ] Input validation tüm endpoint'lerde aktif
- [ ] Output encoding uygulanmış
- [ ] DOMParser + TrustedTypes (innerHTML yasak)
- [ ] LDAP injection koruması var (eğer kullanılıyorsa)
- [ ] OS command injection koruması var

#### A06:2025 Insecure Design
- [ ] Threat modeling tamamlanmış
- [ ] Güvenlik gereksinimleri tanımlı
- [ ] Secure design patterns uygulanmış
- [ ] Business logic flaw kontrolü yapılmış
- [ ] Abuse case testing uygulanmış

#### A07:2025 Authentication Failures
- [ ] Brute force koruması aktif (rate limiting)
- [ ] Şifre politikası uygulanıyor (min 12 karakter)
- [ ] Session management güvenli
- [ ] Multi-factor auth değerlendirmesi yapılmış
- [ ] Credential stuffing koruması var
- [ ] Account lockout mekanizması aktif

#### A08:2025 Software or Data Integrity Failures
- [ ] CI/CD pipeline integre
- [ ] Serialization güvenli
- [ ] Integrity check mekanizması var
- [ ] Unsigned updates yok
- [ ] Auto-update güvenli
- [ ] CSRF token validation aktif

#### A09:2025 Security Logging & Alerting Failures
- [ ] Güvenlik olayları loglanıyor
- [ ] Log bütünlüğü korunuyor
- [ ] Hassas veriler loglarda redacted
- [ ] Real-time alerting mekanizması aktif
- [ ] Incident response planı mevcut
- [ ] Audit trail append-only

#### A10:2025 Mishandling of Exceptional Conditions
- [ ] Error hierarchy tanımlı
- [ ] Graceful degradation uygulanmış
- [ ] Circuit breaker mekanizması var
- [ ] Race condition koruması var
- [ ] Replay attack koruması var
- [ ] Internal network erişimi kısıtlı
- [ ] Response validation var

### 3.3 Authentication Audit

**Argon2id Parametreleri (ADR-022):**

| Parametre | Minimum | Hedef | Doğrulama Yöntemi |
|-----------|---------|-------|-------------------|
| Algorithm | Argon2id | Argon2id | `PASSWORD_ARGON2ID` |
| Memory | 64MB | 64MB | `PASSWORD_ARGON2_MEMORY_COST` |
| Time | 4 | 4 | `PASSWORD_ARGON2_TIME_COST` |
| Threads | 2 | 2 | `PASSWORD_ARGON2_THREADS` |

**Kontrol Listesi:**
- [ ] `password_hash()` kullanımı zorunlu
- [ ] `password_verify()` timing-safe
- [ ] Pepper uygulanmış (constructor injection ile)
- [ ] Brute force: max 5 başarısız deneme → 15 dakika hesap kilidi
- [ ] Session rotation: her başarılı auth sonrası
- [ ] Remember-me token: crypto-random, tek kullanımlık

```php
// ✅ DOĞRU — Argon2id hashleme
$hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64MB
    'time_cost' => 4,
    'threads' => 2,
]);

// ❌ YANLIŞ — MD5 hashleme (asla kullanılmamalı)
$hash = md5($password);
```

### 3.4 Authorization Audit

**RBAC Kontrolleri:**

| Rol | İzinler | Kısıtlamalar |
|-----|---------|--------------|
| admin | Tam erişim | Sadece admin paneli |
| editor | İçerik yönetimi | Kullanıcı yönetimi yasak |
| user | Kişisel veriler | Başka kullanıcı verisi yasak |
| guest | Sadece okuma | Yazma/silme yasak |

**Kontrol Listesi:**
- [ ] Her endpoint'te role kontrolü var
- [ ] IDOR koruması aktif (kullanıcı ID doğrulama)
- [ ] Privilege escalation testi yapılmış
- [ ] Horizontal privilege escalation kontrolü var
- [ ] Vertical privilege escalation kontrolü var
- [ ] API endpoint'lerinde yetkilendirme var

```php
// ✅ DOĞRU — IDOR koruması
$userId = $this->session->get('user_id');
$songId = (int) $request->get('song_id');
// Sadece kendi şarkılarını görebilir
$stmt = $pdo->prepare('SELECT id, title FROM songs WHERE id = :id AND user_id = :user_id');
$stmt->execute([':id' => $songId, ':user_id' => $userId]);

// ❌ YANLIŞ — IDOR açığı
$songId = (int) $request->get('song_id');
$stmt = $pdo->prepare('SELECT id, title FROM songs WHERE id = :id');
// Herkes her şarkıyı görebilir!
```

### 3.5 CSRF Protection Audit

**ADR-010 Uyumluluk Kontrolleri:**

| Kontrol | Durum | İlgili ADR |
|---------|-------|------------|
| Token key: `csrf_token` | Zorunlu | ADR-010 |
| `_csrf_token` kullanımı | REDDEDİLDİ (2026-05-30) | ADR-010 |
| `hash_equals()` kullanımı | Zorunlu (timing-safe) | ADR-010 |
| SameSite cookie | `Lax` veya `Strict` | ADR-010 |
| SPA CSRF güncelleme | DOM patch SONRASINDA | ADR-021 |
| Double-submit pattern | Değerlendirme | — |

**Kontrol Listesi:**
- [ ] Tüm POST/PUT/DELETE isteklerinde CSRF token doğrulaması var
- [ ] Token key `csrf_token` (eskisi `_csrf_token` değil)
- [ ] `hash_equals()` ile timing-safe karşılaştırma
- [ ] Token session-bound (oturumla ilişkili)
- [ ] Token rotation: her 30 dakikada bir
- [ ] SameSite cookie flag: `Lax` veya `Strict`
- [ ] SPA router'da CSRF token DOM patch sonrasında güncelleniyor

```javascript
// ✅ DOĞRU — SPA CSRF güncelleme (ADR-021)
async #navigate(url, pushState = true) {
    this.#patchDOM(html);
    this.#updateCsrf(this.#getCsrfToken()); // DOM patch SONRASINDA
    if (pushState) history.pushState({ url }, null, url);
}

// ❌ YANLIŞ — CSRF token DOM patch ÖNCESİNDE
async #navigate(url, pushState = true) {
    this.#updateCsrf(this.#getCsrfToken()); // YANLIŞ SIRA
    this.#patchDOM(html);
}
```

### 3.6 CSP Audit

**ADR-012 Uyumluluk Kontrolleri:**

| Politika | Değer | Zorunluluk |
|----------|-------|------------|
| default-src | `'self'` | Zorunlu |
| script-src | `'nonce-{random}'` + `'strict-dynamic'` | Zorunlu |
| style-src | `'self'` + `'unsafe-inline'` | İzinli |
| img-src | `'self'` + data: + blob: | İzinli |
| connect-src | `'self'` + service URLs | Zorunlu |
| report-uri | `/csp-report` | Önerilen |

**Nonce Üretimi (SessionManager içinde):**
```php
// ✅ DOĞRU — 256-bit rastgele nonce
$cspNonce = base64_encode(random_bytes(32));

// ❌ YANLIŞ — Predictable nonce
$cspNonce = md5(uniqid());
```

**Kontrol Listesi:**
- [ ] CSP nonce her istekte farklı (256-bit rastgele)
- [ ] `strict-dynamic` aktif
- [ ] `unsafe-eval` yok
- [ ] `unsafe-inline` sadece style-src'de
- [ ] External script yasağı (sadece nonce ile)
- [ ] report-uri tanımlı
- [ ] CSP header tüm sayfalarda aktif

### 3.7 SQL Injection Audit

**ADR-002 Uyumluluk Kontrolleri:**

| Kontrol | Durum | İhlal Sonucu |
|---------|-------|--------------|
| PDO Prepared Statement | Zorunlu | SQL injection açığı |
| SELECT * kullanımı | REDDEDİLDİ | Veri sızıntısı riski |
| ORM kullanımı | REDDEDİLDİ (ADR-002) | Mimari ihlal |
| Açık sütun listesi | Zorunlu | SQL injection riski |
| Input validation | Zorunlu | Veri bozulması |

**Tarama Komutları:**
```powershell
# SELECT * taraması
Select-String -Path "music.coremusic.net\src" -Pattern "SELECT \*" -Recurse -Filter "*.php"

# String concat SQL taraması
Select-String -Path "music.coremusic.net\src" -Pattern '\.\$_(GET|POST|REQUEST|COOKIE)' -Recurse -Filter "*.php"

# ORM kullanımı taraması
Select-String -Path "music.coremusic.net\src" -Pattern "->find\(|->where\(|->select\(" -Recurse -Filter "*.php"
```

**Kontrol Listesi:**
- [ ] Tüm SQL sorguları prepared statement kullanıyor
- [ ] `SELECT *` kullanımı yok
- [ ] String concatenation ile SQL yok
- [ ] Input validation tüm parametrelerde aktif
- [ ] Stored procedure kullanımı güvenli
- [ ] Error messages SQL detayı göstermiyor

### 3.8 XSS Audit

**ADR-001 Uyumluluk Kontrolleri:**

| Kontrol | Durum | Teknoloji |
|---------|-------|-----------|
| DOMParser kullanımı | Zorunlu | Vanilla JS |
| TrustedTypes | Zorunlu | DOM API |
| innerHTML kullanımı | REDDEDİLDİ | — |
| Framework kullanımı | REDDEDİLDİ (ADR-001) | — |
| eval() kullanımı | REDDEDİLDİ | — |
| document.write() | REDDEDİLDİ | — |

**Tarama Komutları:**
```powershell
# innerHTML taraması
Select-String -Path "assets.coremusic.net\js" -Pattern "\.innerHTML\s*=" -Recurse -Filter "*.js"

# eval() taraması
Select-String -Path "assets.coremusic.net\js" -Pattern "eval\(" -Recurse -Filter "*.js"

# document.write taraması
Select-String -Path "assets.coremusic.net\js" -Pattern "document\.write\(" -Recurse -Filter "*.js"

# var kullanımı taraması
Select-String -Path "assets.coremusic.net\js" -Pattern "\bvar\b" -Recurse -Filter "*.js"
```

**Kontrol Listesi:**
- [ ] `innerHTML` kullanımı yok
- [ ] `eval()` kullanımı yok
- [ ] `Function()` constructor kullanımı yok
- [ ] `document.write()` kullanımı yok
- [ ] DOMParser ile HTML parsing aktif
- [ ] TrustedTypes policy tanımlı
- [ ] Output encoding uygulanmış
- [ ] `var` kullanımı yok (sadece `const`/`let`)

### 3.9 Session Management Audit

**ADR-011 Uyumluluk Kontrolleri:**

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Cookie name | `COREMUSIC_SESS` | ADR-011 |
| Idle timeout | 3600s (1 saat) | ADR-011 |
| Absolute timeout | 1800s (30 dakika) rotation | ADR-011 |
| SameSite | `Lax` | ADR-011 |
| HttpOnly | `true` | ADR-011 |
| Secure | `true` (HTTPS) | ADR-011 |

**Kontrol Listesi:**
- [ ] Cookie adı `COREMUSIC_SESS`
- [ ] Idle timeout 3600s
- [ ] Session rotation her 30 dakikada
- [ ] SameSite: `Lax`
- [ ] HttpOnly: `true`
- [ ] Secure: `true`
- [ ] Session fixation koruması var
- [ ] Concurrent session kontrolü var
- [ ] Session ID rastgele üretiliyor (256-bit)
- [ ] Logout sonrası session tamamen yok ediliyor

```php
// ✅ DOĞRU — Session oluşturma
session_set_cookie_params([
    'lifetime' => 0,  // Session cookie
    'path' => '/',
    'domain' => '.coremusic.net',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name('COREMUSIC_SESS');

// ❌ YANLIŞ — Güvensiz session
session_set_cookie_params([
    'lifetime' => 86400 * 30,  // 30 gün çok uzun
    'secure' => false,          // HTTP'de de gönder
    'httponly' => false,        // JS erişebilir
    'samesite' => 'None',      // Cross-site izinli
]);
```

### 3.10 Encryption Audit

**ADR-022 Uyumluluk Kontrolleri:**

| Parametre | Değer | Doğrulama |
|-----------|-------|-----------|
| Algorithm | AES-256-GCM | OpenSSL config |
| IV | 96-bit (12 byte) | `random_bytes(12)` |
| Tag | 16 byte | OpenSSL output |
| Key | 256-bit (32 byte) | Credential vault |
| Kaynak | `credential_vault` tablosu | DB query |

**Key Rotation Politikası:**
- Anahtar yaşı: max 90 gün
- Rotation sonrası eski anahtar 30 gün daha okunabilir
- Yeni veriler her zaman yeni anahtarla şifrelenir

**Kontrol Listesi:**
- [ ] AES-256-GCM kullanımı zorunlu
- [ ] IV 96-bit rastgele üretiliyor
- [ ] Tag doğrulaması yapılıyor
- [ ] Hardcoded key yok
- [ ] Key rotation stratejisi tanımlı
- [ ] Deprecated algoritma yok (DES, 3DES, RC4)

```php
// ✅ DOĞRU — AES-256-GCM şifreleme
$iv = random_bytes(12); // 96-bit IV
$ciphertext = openssl_encrypt(
    $plaintext, 'aes-256-gcm', $key,
    OPENSSL_RAW_DATA, $iv, $tag, '', 16
);

// ❌ YANLIŞ — ECB mode (asla kullanılmamalı)
$ciphertext = openssl_encrypt($plaintext, 'aes-256-ecb', $key);
```

### 3.11 Rate Limiting Audit

**ADR-013 Uyumluluk Kontrolleri:**

| Endpoint | Limit | Pencere | Uygulama |
|----------|-------|---------|----------|
| /login | 5 istek | 900s (15 dk) | Hesap kilidi |
| /register | 3 istek | 3600s (1 saat) | IP engelleme |
| /api/* | 60 istek | 60s | 429 yanıtı |
| /password-reset | 3 istek | 3600s (1 saat) | Token engelleme |
| /download | 10 istek | 60s | Queue'ya alma |

**Kontrol Listesi:**
- [ ] Tüm public endpoint'lerde rate limiting aktif
- [ ] Login brute force koruması var (5 deneme → hesap kilidi)
- [ ] API endpoint'lerinde genel rate limiting var (60 req/60s)
- [ ] Rate limit bypass kontrolü yapılmış
- [ ] Rate limit header'ları (`X-RateLimit-*`) döndürülüyor
- [ ] Rate limit exceeded durumunda 429 yanıtı dönüyor
- [ ] Distributed rate limiting (eğer multi-server)

### 3.12 Dependency Audit

**Tarama Komutları:**
```bash
# PHP bağımlılık taraması
composer audit

# Node.js bağımlılık taraması
npm audit

# Eski bağımlılıklar
composer outdated
npm outdated
```

**Kontrol Listesi:**
- [ ] `composer audit` sonucu temiz (CRITICAL/HIGH yok)
- [ ] `npm audit` sonucu temiz (CRITICAL/HIGH yok)
- [ ] Eski bağımlılıklar güncellenmiş
- [ ] Known CVE kontrolü yapılmış
- [ ] SBOM (Software Bill of Materials) mevcut
- [ ] Dependabot veya benzeri araç aktif
- [ ] License uyumluluğu kontrol edilmiş

### 3.13 Log Security Audit

**Kontrol Listesi:**
- [ ] Hassas veriler loglarda `[REDACTED]` ile maskelenmiş
- [ ] API key, password, JWT secret log'da yok
- [ ] Session token log'da yok
- [ ] Credit card bilgisi log'da yok
- [ ] Log injection koruması var
- [ ] Log bütünlüğü korunuyor (append-only)
- [ ] Log rotasyonu aktif (max 1000 satır/dosya)

**Redaction Kuralları:**

| Veri Türü | Redaction | Örnek |
|-----------|-----------|-------|
| API Key | `[REDACTED]` | `API Key: [REDACTED] (service: deezer)` |
| Password | `[REDACTED]` | `Password: [REDACTED]` |
| JWT Secret | `[REDACTED]` | `JWT: [REDACTED]` |
| Session Token | `[REDACTED]` | `Token: [REDACTED]` |
| Email | Kısmi maskeleme | `use***@example.com` |
| Kredi Kartı | `[REDACTED]` | `Card: [REDACTED]` |

### 3.14 Infrastructure Audit

**Docker Güvenlik Kontrolleri:**
- [ ] Container images signed
- [ ] Non-root user ile çalışıyor
- [ ] Read-only filesystem
- [ ] Resource limits tanımlı
- [ ] Network isolation aktif
- [ ] Secrets environment variable'dan okunuyor
- [ ] Health check endpoint'leri var

**Network Güvenlik Kontrolleri:**
- [ ] Servisler arası iletişim TLS ile
- [ ] Internal network erişimi kısıtlı
- [ ] Firewall kuralları tanımlı
- [ ] DNS security (DNSSEC)
- [ ] DDoS koruması var

---

## 4. Hard Guardrails

| # | Kural | İhlal Sonucu | İlgili ADR |
|---|-------|-------------|------------|
| 1 | **OWASP Top 10 Uyumluluğu** | Tüm 10 kategori kapsanmalı | — |
| 2 | **Hardcoded Secret Yasak** | Derhal `[REDACTED]` ile değiştirilmeli | [[ADR-022]] |
| 3 | **CSRF Token Zorunlu** | Token yoksa istek reddedilmeli | [[ADR-010]] |
| 4 | **PDO Prepared Statement** | Prepared statement yoksa SQL çalıştırılamaz | [[ADR-002]] |
| 5 | **innerHTML Yasak** | innerHTML kullanımı tespit edilirse revert | [[ADR-001]] |
| 6 | **Argon2id Zorunlu** | Başka hash algoritması kullanılamaz | [[ADR-022]] |
| 7 | **CVSS Scoring** | Her bulgu için CVSS puanı zorunlu | — |
| 8 | **Evidence Zorunlu** | Her bulgu için kanıt (kod/dosya) zorunlu | — |
| 9 | **Remediation Zorunlu** | Her bulgu için çözüm önerisi zorunlu | — |
| 10 | **Follow-up Date** | Her bulgu için takip tarihi zorunlu | — |

---

## 5. Naming Conventions

### 5.1 Finding ID Formatı

```
SEC-{YIL}-{SIRA}-001
Örnek: SEC-2026-001-001
```

### 5.2 Severity Levels

| Seviye | CVSS Aralığı | Tepki Süresi | Örnek |
|--------|-------------|--------------|-------|
| **CRITICAL** | 9.0–10.0 | 24 saat | Auth bypass, SQL injection |
| **HIGH** | 7.0–8.9 | 72 saat | XSS, CSRF eksikliği |
| **MEDIUM** | 4.0–6.9 | 1 hafta | Rate limiting eksikliği |
| **LOW** | 0.1–3.9 | 1 ay | Info disclosure |
| **INFO** | 0.0 | Planlama | Best practice önerisi |

### 5.3 Control ID Formatı

```
CTRL-{KATEGORI}-{SIRA}
Örnek: CTRL-AUTH-001, CTRL-CSRF-001
```

---

## 6. Security Considerations

### 6.1 Threat Modeling

| Tehdit | Vektör | Etki | Olasılık | Mitigasyon |
|--------|--------|------|----------|------------|
| SQL Injection | User input → DB | Veri sızıntısı | Düşük | PDO prepared |
| XSS | User input → DOM | Session hijack | Orta | DOMParser + TrustedTypes |
| CSRF | Cross-site form | Yetkisiz işlem | Yüksek | csrf_token + SameSite |
| Brute Force | Login endpoint | Hesap ele geçirme | Yüksek | Rate limiting + lockout |
| Session Hijack | Cookie theft | Hesap ele geçirme | Orta | HttpOnly + Secure |
| SSRF | User URL → internal | Internal erişim | Düşük | URL validation |
| Privilege Escalation | Role manipulation | Yetki artışı | Düşük | RBAC + IDOR check |

### 6.2 Defense-in-Depth Katmanları

```
Katman 1: Input Validation (L3 — Frontend)
Katman 2: Output Encoding (L3 — Frontend)
Katman 3: CSP Nonce (L1 — Security Headers)
Katman 4: CSRF Token (L1 — CsrfMiddleware)
Katman 5: Session Management (L1 — SessionManager)
Katman 6: Auth Middleware (L1 — AuthMiddleware)
Katman 7: Rate Limiting (L1 — RateLimiterMiddleware)
Katman 8: Prepared Statements (L0 — Database)
Katman 9: Encryption at Rest (L0 — Credential Vault)
Katman 10: Logging & Monitoring (L0 — Audit Trail)
```

---

## 7. Performance Notes

| İşlem | Beklenen Süre | Kabul Edilebilir | Optimizasyon |
|-------|--------------|-----------------|--------------|
| Argon2id hash | 250-500ms | <1s | Parallel hashing |
| AES-256-GCM encrypt | <1ms | <5ms | Hardware AES-NI |
| CSRF token generate | <1ms | <5ms | — |
| CSP nonce generate | <1ms | <5ms | — |
| Rate limit check (APCu) | <1ms | <5ms | — |
| Session validate | <5ms | <10ms | — |
| Prepared statement | <5ms | <50ms | Query cache |

**Argon2id Tuning:**
- Memory: 64MB (düşük → zayıf, yüksek → yavaş)
- Time: 4 iteration (düşük → zayıf, yüksek → yavaş)
- Threads: 2 (CPU çekirdek sayısına göre)
- Hedef: 250-500ms arası hash süresi

---

## 8. Edge Cases

| # | Senaryo | Tetikleyici | Çözüm | Öncelik |
|---|---------|-------------|-------|---------|
| 1 | Timing attack | hash_equals() yerine == | hash_equals() kullanımı zorunlu | HIGH |
| 2 | Race condition | Eşzamanlı token reuse | Token tek kullanımlık | HIGH |
| 3 | Session fixation | Login sonrası session ID değişimi | Session rotation | CRITICAL |
| 4 | Token reuse | CSRF token tekrar kullanımı | Token rotation | MEDIUM |
| 5 | Cache poisoning | CSP nonce cache'lenmesi | Nonce per-request | HIGH |
| 6 | Password spray | Farklı hesaplarda aynı şifre | Rate limiting + lockout | HIGH |
| 7 | IDOR | Sequential resource IDs | UUID veya random ID | MEDIUM |
| 8 | Log injection | Log'a zararlı karakter ekleme | Input sanitization | LOW |
| 9 | Time-of-check | Auth check ile operation arasında | Atomic operation | HIGH |
| 10 | Buffer overflow | Large input → C++ engine | Input size validation | CRITICAL |

---

## 9. Troubleshooting

| Sorun | Belirti | Kök Neden | Çözüm |
|-------|---------|-----------|-------|
| CSRF hatası | 403 Forbidden | Token süresi dolmuş veya SameSite | Token rotation + cookie flags |
| CSP hatası | Script engellendi | Nonce yanlış veya eksik | SecurityHeaders nonce üretimi |
| Rate limit hatası | 429 Too Many Requests | APCu memory dolu | APCu config artırma |
| Session timeout | Otomatik logout | Idle timeout 3600s | Session rotation mekanizması |
| Auth bypass | Yetkisiz erişim | BypassAuth production'da aktif | BypassAuth devre dışı bırakma |
| SQL injection | Veri sızıntısı | Prepared statement yok | PDO prepared statement |
| XSS | Script çalıştırma | innerHTML kullanımı | DOMParser + TrustedTypes |
| Encryption hatası | Veri okunamıyor | IV reuse veya key bozulması | Key rotation + fresh IV |
| Dependency CVE | Güvenlik uyarısı | Eski bağımlılık versiyonu | Composer/NPM güncelleme |
| Log redaction eksik | Hassas veri log'da | Redaction uygulanmamış | `[REDACTED]` ekleme |

---

## 10. Common Anti-Patterns

### 10.1 Şifreleme

```php
// ❌ YANLIŞ — MD5 ile şifreleme
$password = md5($userInput);

// ✅ DOĞRU — Argon2id ile şifreleme
$password = password_hash($userInput, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 2,
]);
```

### 10.2 SQL Sorgulama

```php
// ❌ YANLIŞ — String concatenation
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ DOĞRU — Prepared statement
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE id = :id');
$stmt->execute([':id' => $_GET['id']]);
```

### 10.3 CSRF Token

```php
// ❌ YANLIŞ — Eski token key
$token = $_SESSION['_csrf_token'];

// ✅ DOĞRU — Güncel token key (ADR-010)
$token = $_SESSION['csrf_token'];
```

### 10.4 XSS Koruma

```javascript
// ❌ YANLIŞ — innerHTML kullanımı
element.innerHTML = userInput;

// ✅ DOĞRU — DOMParser + TrustedTypes
const doc = new DOMParser().parseFromString(userInput, 'text/html');
const safeContent = trustedTypes.createPolicy('default').createHTML(doc.body.innerHTML);
element.innerHTML = safeContent;
```

### 10.5 Hardcoded Secret

```php
// ❌ YANLIŞ — Hardcoded API key
$apiKey = 'sk-1234567890abcdef';

// ✅ DOĞRU — Environment variable
$apiKey = getenv('DEEZER_API_KEY');
```

### 10.6 Cookie Flags

```php
// ❌ YANLIŞ — Güvensiz cookie
setcookie('session', $value, time() + 86400 * 30);

// ✅ DOĞRU — Güvenli cookie
setcookie('COREMUSIC_SESS', $value, [
    'expires' => 0,
    'path' => '/',
    'domain' => '.coremusic.net',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
```

### 10.7 Error Handling

```php
// ❌ YANLIŞ — Hassas bilgi gösterimi
catch (Exception $e) {
    die("Database hatası: " . $e->getMessage());
}

// ✅ DOĞRU — Güvenli hata mesajı
catch (Exception $e) {
    error_log("DB Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
```

---

## 11. OWASP Top 10 Reference

| OWASP | Kategori | CoreMusic Kontrol | İlgili ADR | İlgili Middleware |
|-------|----------|-------------------|------------|-------------------|
| A01 | Broken Access Control (SSRF dahil) | RBAC + IDOR + CORS + URL allowlist | — | AuthMiddleware |
| A02 | Security Misconfiguration | Error handling + headers + hardening | [[ADR-012]] | SecurityHeadersMiddleware |
| A03 | Software Supply Chain Failures | composer audit + npm audit + GitLeaks | — | — |
| A04 | Cryptographic Failures | AES-256-GCM + Argon2id + RS256 | [[ADR-022]] | — |
| A05 | Injection | PDO prepared + DOMParser + TrustedTypes | [[ADR-002]] | — |
| A06 | Insecure Design | Threat modeling + DDD + CQRS | — | — |
| A07 | Authentication Failures | Argon2id + session + CSRF + MFA | [[ADR-010]] | AuthMiddleware + CsrfMiddleware |
| A08 | Software/Data Integrity | CI/CD + serialization + code signing | — | — |
| A09 | Security Logging & Alerting | Redaction + audit trail + alerting | [[ADR-004]] | — |
| A10 | Mishandling of Exceptional Conditions | Error hierarchy + graceful degradation | — | — |

---

## 12. Audit Report Template

```markdown
# Security Audit Report — CoreMusic

## Metadata

| Field | Value |
|-------|-------|
| **Date** | YYYY-MM-DD |
| **Auditor** | Security Engineer |
| **Scope** | [Denetim alanı] |
| **Standard** | OWASP Top 10:2025 |
| **Version** | Template v3.0.0 |

## Executive Summary

| Metrik | Değer |
|--------|-------|
| **Toplam Bulgu** | X |
| **CRITICAL** | X |
| **HIGH** | X |
| **MEDIUM** | X |
| **LOW** | X |
| **INFO** | X |

## Bulgu Detayları

### Finding SEC-YYYY-NNN-001: [Başlık]

| Field | Value |
|-------|-------|
| **Severity** | CRITICAL / HIGH / MEDIUM / LOW / INFO |
| **CVSS Score** | X.X |
| **CWE** | CWE-XXX |
| **OWASP** | AXX:2025 Category |
| **File** | path/to/file.php:XX |
| **Status** | Open / In Progress / Fixed / Accepted |

**Description:**
[Bulgu açıklaması]

**Evidence:**
```php
// Vulnerable code
[code snippet]
```

**Remediation:**
```php
// Fixed code
[code snippet]
```

**Status:** Open
**Priority:** Immediate / High / Medium / Low
**Follow-up Date:** YYYY-MM-DD

## Summary & Recommendations

[Özet ve öneriler]

## Sign-off

| Role | Name | Date |
|------|------|------|
| Security Engineer | — | — |
| Tech Lead | — | — |
| Vault Steward | — | — |
```

---

## 13. Related Documents

| Dosya | Amaç | İlgili ADR |
|-------|------|------------|
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi | — |
| [[ADR-011-session-management]] | Session yönetimi | — |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP politikası | — |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | — |
| [[ADR-022-database-hardened-security]] | DB güvenlik sertleştirme | — |
| [[ADR-034-credential-vault-normalization]] | Credential vault yönetimi | — |
| [[architecture/07-security/middleware-security]] | L1 threat model | — |
| [[architecture/07-security/encryption]] | AES-256-GCM, Argon2id | — |
| [[architecture/07-security/session-management]] | Session lifecycle | — |
| [[architecture/07-security/api/api_security_master]] | API güvenlik standartları | — |
| [[architecture/07-security/security/csrf-protection]] | CSRF detay | — |
| [[architecture/07-security/security/owasp-compliance]] | OWASP uyumu | — |
| [[subdomains/auth.coremusic.net/index]] | Auth subdomain | — |

---

## 14. Cross-References

| Bu Belgeden (Security Audit) | Hedef | İlişki Tipi |
|-----------------------------|-------|-------------|
| § 3.2 OWASP Checklist | [[architecture/07-security/security/owasp-compliance]] | Standart referansı |
| § 3.3 Auth Audit | [[ADR-022-database-hardened-security]] | Argon2id parametreleri |
| § 3.5 CSRF Audit | [[ADR-010-csrf-protection-strategy]] | Token key standardı |
| § 3.6 CSP Audit | [[ADR-012-csp-nonce-strict-dynamic]] | Nonce üretimi |
| § 3.7 SQL Audit | [[ADR-002-pdo-mandatory-no-orm]] | Prepared statement |
| § 3.8 XSS Audit | [[ADR-001-vanilla-js-itcss]] | DOMParser zorunluluğu |
| § 3.9 Session Audit | [[ADR-011-session-management]] | Cookie flags |
| § 3.11 Rate Limit Audit | [[ADR-013-rate-limiting-apcu]] | APCu konfigürasyonu |
| § 6.1 Threat Modeling | [[architecture/07-security/middleware-security]] | Threat model |
| § 10 Anti-Patterns | [[architecture/07-security/encryption]] | Encryption standards |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | 550+ |
| **Frontmatter** | ✅ Tamamlandı |
| **OWASP 2025** | ✅ Uyumlu |
| **CWE Referansları** | ✅ Mevcut |
| **ADR Uyumlu** | ✅ 010, 011, 012, 013, 022 |
| **Bölüm Sayısı** | 18 |
| **Kod Örnekleri** | ✅ 14+ (❌ WRONG / ✅ CORRECT) |
| **Checklist'ler** | ✅ 14 OWASP + 6 audit checklist |
| **Anti-Patterns** | ✅ 7 yaygın anti-pattern |

---

## 16. Examples

### 16.1 Authentication Audit Checklist

```markdown
## Authentication Audit — auth.coremusic.net

### Argon2id Configuration
- [ ] Algorithm: Argon2id (PASSWORD_ARGON2ID)
- [ ] Memory cost: 65536 (64MB)
- [ ] Time cost: 4
- [ ] Threads: 2
- [ ] pepper: Hardcoded → ❌ / Constructor injection → ✅

### Password Policy
- [ ] Minimum length: 12 characters
- [ ] Complexity: uppercase + lowercase + digit + special
- [ ] History: last 5 passwords rejected
- [ ] Expiry: 90 days (optional)

### Brute Force Protection
- [ ] Max failed attempts: 5
- [ ] Lockout duration: 15 minutes
- [ ] Progressive delay: 1s → 5s → 30s
- [ ] Account unlock: manual or timeout

### Session Management
- [ ] Cookie name: COREMUSIC_SESS
- [ ] Idle timeout: 3600s
- [ ] Rotation: 1800s (30 min)
- [ ] HttpOnly: true
- [ ] Secure: true
- [ ] SameSite: Lax

### Token Management
- [ ] CSRF token in forms: csrf_token
- [ ] Token rotation: per-session
- [ ] Timing-safe compare: hash_equals()
```

### 16.2 API Security Audit Checklist

```markdown
## API Security Audit — music.coremusic.net (port 81)

### Input Validation
- [ ] All endpoints validate input types
- [ ] Maximum request body size: 1MB
- [ ] JSON schema validation active
- [ ] File upload: type + size validation

### Authentication
- [ ] All /api/* endpoints require auth
- [ ] Token validation on every request
- [ ] Token expiry: 3600s
- [ ] Refresh token: secure rotation

### Rate Limiting
- [ ] Global: 60 req/60s
- [ ] Login: 5 req/900s
- [ ] Register: 3 req/3600s
- [ ] Password reset: 3 req/3600s

### Response Security
- [ ] No sensitive data in responses
- [ ] Error messages generic (no stack trace)
- [ ] CORS: restricted origins
- [ ] Security headers: all present

### SQL Injection Prevention
- [ ] All queries: PDO prepared statements
- [ ] No SELECT * usage
- [ ] No string concatenation in SQL
- [ ] Input validation before query

### Logging
- [ ] All auth events logged
- [ ] Failed login attempts logged
- [ ] Rate limit exceeded logged
- [ ] Sensitive data redacted
```

### 16.3 Database Security Audit Checklist

```markdown
## Database Security Audit — 18 BCNF Veritabanları

### Connection Security
- [ ] PDO prepared statements: 100%
- [ ] Connection encryption: TLS
- [ ] Credential storage: .env (not hardcoded)
- [ ] Connection pooling: configured

### Access Control
- [ ] Database user: least privilege
- [ ] Write access: restricted
- [ ] Schema modification: DBA only
- [ ] Backup access: restricted

### Data Protection
- [ ] Encryption at rest: AES-256-GCM
- [ ] Sensitive columns: encrypted
- [ ] PII: tokenized or masked
- [ ] Backup encryption: enabled

### Schema Security
- [ ] BCNF normalization: all tables
- [ ] Naming convention: coremusic_*
- [ ] Soft delete: is_deleted = 0
- [ ] Audit trail: timestamps

### Query Security
- [ ] SELECT * usage: 0 (ADR-002)
- [ ] ORM usage: 0 (ADR-002)
- [ ] Stored procedures: reviewed
- [ ] Dynamic SQL: prohibited
```

---

## 17. Checklist

### Pre-Commit Security Checklist

```markdown
## Pre-Commit Security Checklist

### Kritik Kontroller (her commit'te)
- [ ] Hardcoded secret yok (API key, password, JWT)
- [ ] `SELECT *` kullanımı yok
- [ ] ORM kullanımı yok
- [ ] `innerHTML` kullanımı yok
- [ ] `eval()` kullanımı yok
- [ ] `var` kullanımı yok (sadece const/let)
- [ ] Prepared statement kullanımı var (SQL sorgularında)

### CSRF Kontrolleri
- [ ] Yeni form'da `csrf_token` var
- [ ] Token key: `csrf_token` (eskisi `_csrf_token` değil)
- [ ] `hash_equals()` kullanılıyor

### XSS Kontrolleri
- [ ] User input DOM'a ekleniyor mu? → DOMParser kullan
- [ ] User input HTML olarak render ediliyor mu? → Output encoding
- [ ] `document.write()` kullanımı yok

### Session Kontrolleri
- [ ] Cookie flags: HttpOnly, Secure, SameSite
- [ ] Session rotation uygulanmış
- [ ] Logout sonrası session yok ediliyor

### Log Kontrolleri
- [ ] Hassas veri log'da yok
- [ ] `[REDACTED]` maskelenmesi uygulanmış
- [ ] Hata mesajları kullanıcıya detay göstermiyor

### Dependency Kontrolleri
- [ ] `composer audit` temiz
- [ ] `npm audit` temiz
- [ ] Yeni bağımlılık security review'dan geçmiş
```

---

## 18. Incident Response

### 18.1 Incident Severity Levels

| Seviye | Tanım | Tepki Süresi | Örnek |
|--------|-------|--------------|-------|
| **P1 — CRITICAL** | Sistem çökmesi, veri sızıntısı, auth bypass | 15 dakika | SQL injection, auth bypass |
| **P2 — HIGH** | Güvenlik açığı, potansiyel veri sızıntısı | 1 saat | XSS, CSRF eksikliği |
| **P3 — MEDIUM** | Kısıtlı etki, mitigasyon mümkün | 4 saat | Rate limiting bypass |
| **P4 — LOW** | Minimal etki, best practice ihlali | 24 saat | Info disclosure |

### 18.2 Incident Response Procedure

```text
1. TESPIT (Detection)
   ├── Monitoring alert'i tetiklendi
   ├── Kullanıcı raporu geldi
   ├── Audit trail'de anormallik
   └── Bağımlılık taramasında CVE bulundu

2. SINIFLANDIRMA (Classification)
   ├── Severity level belirle (P1-P4)
   ├── Etkilenen sistemleri listele
   ├── Kullanıcı sayısını belirle
   └── Veri sızıntısı var mı?

3. YALITIM (Containment)
   ├── Etkilenen servisi devre dışı bırak (P1)
   ├── Firewall kuralı ekle
   ├── IP engelleme
   └── Session invalidation

4. KOK NEDEN ANALIZI (Root Cause)
   ├── Log analizi
   ├── Kod inceleme
   ├── Reproduction
   └── Timeline oluşturma

5. DUZELTME (Remediation)
   ├── Hotfix uygula
   ├── Code review'dan geçir
   ├── Testleri çalıştır
   └── Deployment yap

6. KURTARMA (Recovery)
   ├── Servisi tekrar aktifleştir
   ├── Monitoring devam ettir
   ├── Kullanıcıları bilgilendir
   └── Session invalidation (gerekirse)

7. POST-INCIDENT
   ├── Retrospective toplantısı
   ├── Dokümantasyon güncelle
   ├── ADR oluştur (gerekirse)
   ├── Vault-sync yap
   └── log.md'ye kaydet
```

### 18.3 Incident Response Contacts

| Rol | Sorumluluk | Erişim |
|-----|------------|--------|
| Security Engineer | İlk müdahale, analiz | Tam erişim |
| Tech Lead | Karar alma, onay | Tam erişim |
| Vault Steward | Dokümantasyon, loglama | Vault erişimi |
| DevOps Engineer | Servis yönetimi | Altyapı erişimi |

### 18.4 Post-Incident Checklist

```markdown
## Post-Incident Checklist

### Hemen Sonrası (24 saat)
- [ ] Root cause analizi tamamlandı
- [ ] Hotfix uygulandı ve test edildi
- [ ] Etkilenen kullanıcılar bilgilendirildi
- [ ] Session'lar invalidation edildi (gerekiyorsa)
- [ ] Monitoring devam ediyor

### Kısa Vadeli (1 hafta)
- [ ] Retrospective toplantısı yapıldı
- [ ] Dokümantasyon güncellendi
- [ ] ADR oluşturuldu (gerekirse)
- [ ] Vault-sync yapıldı
- [ ] log.md'ye detaylı kayıt eklendi

### Uzun Vadeli (1 ay)
- [ ] Benzer açıklar için tarama yapıldı
- [ ] Security training güncellendi
- [ ] Incident response planı güncellendi
- [ ] Monitoring kuralları güncellendi
- [ ] Bağımlılıklar güncellendi
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
