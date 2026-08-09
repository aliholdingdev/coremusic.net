---
type: template
category: adr-security
title: "CoreMusic — ADR Security Template (OWASP/CSRF/CSP/Auth/Encryption)"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
usage: "Güvenlik ile ilgili ADR oluştururken bu dosyayı kopyalayın"
related:
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-012-csp-nonce-strict-dynamic]]"
  - "[[decisions/accepted/ADR-013-rate-limiting-apcu]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
tags: [template, adr, security, owasp, csrf, csp, auth, encryption]
---

# CoreMusic — ADR Security Template

**Bu dosya bir şablondur.** Güvenlik ile ilgili ADR oluştururken bu dosyayı kopyalayın.

**Kullanım:** `cp .ai/.templates/adr-security-template.md .ai/decisions/accepted/ADR-NNN-baslik.md`

---

## 📋 Security ADR Kullanım Kılavuzu

### Security ADR Ne Zaman Yazılır?

| Durum | Gerekli mi? | Açıklama |
|-------|-------------|----------|
| CSRF koruması değişikliği | ✅ Evet | ADR-010 etkileniyor |
| CSP policy değişikliği | ✅ Evet | ADR-012 etkileniyor |
| Session yönetimi değişikliği | ✅ Evet | ADR-011 etkileniyor |
| Rate limiting değişikliği | ✅ Evet | ADR-013 etkileniyor |
| Encryption değişikliği | ✅ Evet | ADR-022 etkileniyor |
| Auth flow değişikliği | ✅ Evet | ADR-043 etkileniyor |
| OWASP güncellemesi | ✅ Evet | Tüm güvenlik katmanlarını etkiliyor |
| Güvenlik açığı düzeltmesi | ✅ Evet | Kritik güvenlik değişikliği |

### Security ADR Yazarken Dikkat

1. **ADR-010:** CSRF token key = `csrf_token` (ZORUNLU)
2. **ADR-011:** Session management (ZORUNLU)
3. **ADR-012:** CSP nonce + strict-dynamic (ZORUNLU)
4. **ADR-013:** APCu rate limiting (ZORUNLU)
5. **ADR-022:** Database Hardened Security (ZORUNLU)
6. **ADR-043:** Auth subdomain consolidation (ZORUNLU)
7. **OWASP Top 10:** Tüm OWASP kuralları geçerli
8. **Zero Trust:** Hiçbir istek güvenilir değildir

---

## 📄 SECURITY ADR ŞABLONU

---

```yaml
---
type: decision
id: "NNN"
title: "ADR-NNN: [Güvenlik Karar Başlığı]"
category: "security"
status: "draft|active|frozen"
date: "YYYY-MM-DD"
updated: "YYYY-MM-DD"
authority: "Security Engineer"
governance: "Red Team • Human Mode • Truth Mode"
supersedes: null
version: 1.0.0
tags: [security, owasp, csrf, csp, auth, encryption]
risk-level: "critical|high|medium|low"
owasp-top10: ["A01:2021", "A02:2021", "A03:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-012-csp-nonce-strict-dynamic]]"
  - "[[decisions/accepted/ADR-013-rate-limiting-apcu]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l1-security]]"
---
```

---

## 1. Executive Summary

[Güvenlik kararının kısa özeti. Ne tehlikeli? Hangi OWASP kategorisi?]

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | draft / active / frozen |
| **Versiyon** | 1.0.0 |
| **Oluşturma** | YYYY-MM-DD |
| **Son Güncelleme** | YYYY-MM-DD |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical / high / medium / low |
| **Onay** | Red Team • Human Mode • Truth Mode |

---

## 3. Context

### 3.1 Güvenlik Problemi

[Güvenlik ile ilgili hangi sorun çözülüyor?]

### 3.2 OWASP Top 10 Etkileşimi

| OWASP Kategorisi | Durum | Etki |
|------------------|-------|------|
| **A01:2021** Broken Access Control | ⚠️ Etkileniyor | [Açıklama] |
| **A02:2021** Cryptographic Failures | ⚠️ Etkileniyor | [Açıklama] |
| **A03:2021** Injection | ⚠️ Etkileniyor | [Açıklama] |
| **A04:2021** Insecure Design | ⚠️ Etkileniyor | [Açıklama] |
| **A05:2021** Security Misconfiguration | ⚠️ Etkileniyor | [Açıklama] |
| **A06:2021** Vulnerable Components | ⚠️ Etkileniyor | [Açıklama] |
| **A07:2021** Auth Failures | ⚠️ Etkileniyor | [Açıklama] |
| **A08:2021** Data Integrity Failures | ⚠️ Etkileniyor | [Açıklama] |
| **A09:2021** Logging Failures | ⚠️ Etkileniyor | [Açıklama] |
| **A10:2021** SSRF | ⚠️ Etkileniyor | [Açıklama] |

### 3.3 Mevcut Güvenlik Katmanları

#### 3.3.1 Middleware Pipeline (Değişmez Sıra — ADR-010/011/012/013/022)

```
Request
    │
    ▼
┌─────────────────────────────────────────────────┐
│  1. SessionManagerMiddleware                    │
│     • Session başlat (CSP nonce üretimi)        │
│     • Cookie: COREMUSIC_SESS                     │
│     • SameSite=Lax (ADR-011)                     │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  2. BypassAuthMiddleware                        │
│     • Test bypass (prod'da devre dışı)           │
│     • ?_bypass=1 query parametresi              │
│     • Fail-open risk kapatıldı (ADR-043)        │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  3. RateLimiterMiddleware                       │
│     • APCu: 60 req/60s (ADR-013)                │
│     • IP bazlı                                    │
│     • Brute-force koruması                       │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  4. AuthMiddleware                              │
│     • Auth bilgisi inject                        │
│     • User rol kontrolü                          │
│     • Session timeout (3600s)                    │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  5. SecurityHeadersMiddleware                   │
│     • CSP strict-dynamic (ADR-012)               │
│     • X-Content-Type-Options: nosniff            │
│     • X-Frame-Options: DENY                      │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│  6. CsrfMiddleware                             │
│     • csrf_token doğrulama (ADR-010)             │
│     • POST/PUT/DELETE için zorunlu              │
│     • Session-bound tek token                    │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
                 Controller
```

### 3.4 İtici Güçler

| # | Güç | Açıklama | Kritiklik |
|---|-----|----------|-----------|
| 1 | [Güç 1] | [Açıklama] | Kritik/Yüksek/Orta/Düşük |
| 2 | [Güç 2] | [Açıklama] | Kritik/Yüksek/Orta/Düşük |

### 3.5 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| ADR-010 | CSRF token key = `csrf_token` | ADR-010 |
| ADR-011 | Session management | ADR-011 |
| ADR-012 | CSP nonce + strict-dynamic | ADR-012 |
| ADR-013 | Rate limiting APCu | ADR-013 |
| ADR-022 | DB Hardened Security | ADR-022 |
| ADR-043 | Auth subdomain consolidation | ADR-043 |

### 3.6 Ekosistem Etkileşimi

| Etkilenen Alan | Etki | Açıklama |
|---------------|------|----------|
| **L1 Security** | Doğrudan | Middleware pipeline |
| **L0 Infrastructure** | Doğrudan | DB, cache |
| **L2 Routing** | Doğrudan | Controller'lar |
| **L3 Presentation** | Doğrudan | Frontend CSRF |
| **Auth Subdomain** | Doğrudan | ADR-043 |

---

## 4. Decision

### 4.1 Karar Bildirimi

**[Net güvenlik karar cümlesi]**

### 4.2 Güvenlik Kuralları

| # | Kural | Durum | İlgili ADR |
|---|-------|-------|------------|
| 1 | CSRF token key = `csrf_token` | ✅ Zorunlu | ADR-010 |
| 2 | CSP nonce zorunlu | ✅ Zorunlu | ADR-012 |
| 3 | Rate limiting zorunlu | ✅ Zorunlu | ADR-013 |
| 4 | Session timeout 3600s | ✅ Zorunlu | ADR-011 |
| 5 | SameSite=Lax | ✅ Zorunlu | ADR-011 |
| 6 | Argon2id password hashing | ✅ Zorunlu | ADR-022 |
| 7 | AES-256-GCM encryption | ✅ Zorunlu | ADR-022 |
| 8 | Auth subdomain konsolidasyonu | ✅ Zorunlu | ADR-043 |

### 4.3 CSRF Koruması

```php
<?php
// CSRF Token Üretimi (ADR-010 uyumlu)
// Dosya: include/Middleware/CsrfMiddleware.php

// ✅ DOĞRU — Key ismi: csrf_token (ADR-010 zorunlu)
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// HTML formda kullanım
echo '<input type="hidden" name="csrf_token" value="' . $token . '">';

// JavaScript'de kullanım
// ADR-021: fetch() POST'ta header'da gönderilir
// csrf_token key'i sabit ve değişmez
```

```php
<?php
// CSRF Token Doğrulama
function validateCsrfToken(string $token): bool
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
```

```javascript
// ❌ YANLIŞ — _csrf_token (2026-05-30'da kaldırıldı)
fetch('/api/data', {
    method: 'POST',
    headers: { 'X-CSRF-Token': _csrf_token } // ❌ YANLIŞ
});

// ✅ DOĞRU — csrf_token (ADR-010)
fetch('/api/data', {
    method: 'POST',
    headers: { 'X-CSRF-Token': csrf_token } // ✅ DOĞRU
});
```

### 4.4 CSP Policy

```php
<?php
// CSP Nonce Üretimi (ADR-012 uyumlu)
// Dosya: include/Middleware/SecurityHeadersMiddleware.php

// ✅ DOĞRU — 256-bit nonce
function generateCspNonce(): string
{
    return base64_encode(random_bytes(32));
}

// CSP Header
$nonce = generateCspNonce();
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}'; style-src 'self' 'nonce-{$nonce}';");
```

```html
<!-- HTML'de nonce kullanımı -->
<script nonce="<?php echo $nonce; ?>">
    // Sadece nonce'lu script'ler çalışır
</script>

<style nonce="<?php echo $nonce; ?>">
    /* Sadece nonce'lu style'lar çalışır */
</style>
```

### 4.5 Rate Limiting

```php
<?php
// Rate Limiting (ADR-013 uyumlu)
// Dosya: include/Middleware/RateLimiterMiddleware.php

// ✅ DOĞRU — APCu tabanlı
function isRateLimited(string $ip): bool
{
    $key = "rate_limit:{$ip}";
    $count = apcu_fetch($key) ?: 0;
    
    if ($count >= 60) { // 60 istek/60 saniye
        return true;
    }
    
    apcu_store($key, $count + 1, 60);
    return false;
}
```

### 4.6 Encryption

```php
<?php
// AES-256-GCM Encryption (ADR-022 uyumlu)
// Dosya: include/Security/EncryptionService.php

// ✅ DOĞRU — 96-bit IV, 16-byte tag
function encrypt(string $plaintext, string $key): array
{
    $iv = random_bytes(12); // 96-bit IV
    $tag = '';
    
    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',  // AAD
        16   // Tag length
    );
    
    return [
        'ciphertext' => $ciphertext,
        'iv' => $iv,
        'tag' => $tag
    ];
}

// Decrypt
function decrypt(array $data, string $key): string
{
    return openssl_decrypt(
        $data['ciphertext'],
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $data['iv'],
        $data['tag']
    );
}
```

### 4.7 Password Hashing

```php
<?php
// Argon2id Password Hashing (ADR-022 uyumlu)
// Dosya: include/Service/AuthService.php

// ✅ DOĞRU — Argon2id
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536, // 64 MB
        'time_cost' => 4,
        'threads' => 2
    ]);
}

function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}
```

### 4.8 Session Management

```php
<?php
// Session Management (ADR-011 uyumlu)
// Dosya: include/Middleware/SessionManagerMiddleware.php

// ✅ DOĞRU — Cookie settings
session_set_cookie_params([
    'lifetime' => 0, // Session cookie
    'path' => '/',
    'domain' => '.coremusic.net',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax' // ADR-011
]);

// Session timeout kontrolü
function isSessionExpired(): bool
{
    if (!isset($_SESSION['last_activity'])) {
        return true;
    }
    $idle = time() - $_SESSION['last_activity'];
    return $idle > 3600; // 3600 saniye
}

// Session rotation (her 30 dakikada)
function rotateSession(): void
{
    $lastRotation = $_SESSION['last_rotation'] ?? 0;
    if (time() - $lastRotation > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_rotation'] = time();
    }
}
```

---

## 5. Architecture

### 5.1 Güvenlik Mimarisi

```
┌─────────────────────────────────────────────────┐
│              Presentation Layer (L3)             │
│  ┌─────────────────────────────────────────────┐ │
│  │  • TrustedTypes policy                      │ │
│  │  • DOMParser (innerHTML yasak)              │ │
│  │  • CSRF token management                    │ │
│  │  • CSP nonce enforcement                    │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Routing Layer (L2)                  │
│  ┌─────────────────────────────────────────────┐ │
│  │  • SPA PageRouter                           │ │
│  │  • URL normalization                        │ │
│  │  • Subdomain routing                        │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Security Layer (L1)                 │
│  ┌─────────────────────────────────────────────┐ │
│  │  SessionManager → BypassAuth → RateLimiter  │ │
│  │  → Auth → SecurityHeaders → Csrf            │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Infrastructure Layer (L0)           │
│  ┌─────────────────────────────────────────────┐ │
│  │  • PDO (ORM yasak)                          │ │
│  │  • APCu cache                               │ │
│  │  • AES-256-GCM vault                        │ │
│  │  • Argon2id hashing                         │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

### 5.2 Auth Flow (ADR-043 Uyumlu)

```
┌──────────────┐     ┌──────────────────┐     ┌────────────────┐
│ music.       │────►│ auth.coremusic.  │────►│ music.coremusic│
│ coremusic.net│     │ net (auth)       │     │ .net (return)  │
└──────────────┘     └──────────────────┘     └────────────────┘
     │                      │                         │
     │  redirect_uri        │  login/register         │
     │  parameter           │  session creation       │
     │                      │  auth_key generation    │
     │                      │                         │
     │◄─────────────────────│◄────────────────────────│
     │                      │  auth_key (64-char hex) │
```

### 5.3 Encryption Architecture

```
┌─────────────────────────────────────────────────┐
│              Credential Vault                    │
│  ┌─────────────────────────────────────────────┐ │
│  │  DB Password     → AES-256-GCM encrypted   │ │
│  │  API Key         → AES-256-GCM encrypted   │ │
│  │  JWT Secret      → AES-256-GCM encrypted   │ │
│  │  Deezer ARL      → AES-256-GCM encrypted   │ │
│  └─────────────────────────────────────────────┘ │
│                                                  │
│  ┌─────────────────────────────────────────────┐ │
│  │  IV: 96-bit random                          │ │
│  │  Tag: 16-byte authentication tag            │ │
│  │  Key: 256-bit master key                    │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

---

## 6. Alternatives Considered

### 6.1 Alternatif 1: JWT Token Kullanımı (Reddedilen)

**Açıklama:** Session yerine JWT token kullanımı

**Avantajlar:**
- Stateless
- Scalable
- Mobile-friendly

**Dezavantajlar:**
- ADR-011 ile çelişiyor
- Token rotation zor
- Revocation zor

**Neden Reddedildi:** ADR-011 session-based auth

### 6.2 Alternatif 2: SameSite=None Kullanımı (Reddedilen)

**Açıklama:** Cross-site cookie desteği için SameSite=None

**Avantajlar:**
- Cross-site istekler çalışır

**Dezavantajlar:**
- CSRF riski artar
- ADR-011 ile çelişiyor

**Neden Reddedildi:** ADR-011 SameSite=Lax

### 6.3 Karar Matrisi

| Kriter | Ağırlık | JWT | Session | SameSite=None | SameSite=Lax |
|--------|---------|-----|---------|---------------|--------------|
| Güvenlik | %40 | Orta | Yüksek | Düşük | Yüksek |
| ADR Uyumu | %30 | ❌ | ✅ | ❌ | ✅ |
| Kolaylık | %20 | Yüksek | Orta | Yüksek | Yüksek |
| Scalability | %10 | Yüksek | Orta | Yüksek | Yüksek |

---

## 7. Consequences

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki |
|---|-------|------|
| 1 | [Olumlu sonuç 1] | Yüksek/Orta/Düşük |
| 2 | [Olumlu sonuç 2] | Yüksek/Orta/Düşük |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk | Mitigation |
|---|-------|------|------------|
| 1 | [Olumsuz sonuç 1] | Yüksek/Orta/Düşük | [Çözüm] |
| 2 | [Olumsuz sonuç 2] | Yüksek/Orta/Düşük | [Çözüm] |

---

## 8. Testing Strategy

### 8.1 Güvenlik Test Kapsamı

| Test Türü | Hedef | Araç |
|-----------|-------|------|
| **CSRF Test** | %100 | PHPUnit |
| **CSP Test** | %100 | PHPUnit |
| **Rate Limit Test** | %100 | PHPUnit |
| **Session Test** | %100 | PHPUnit |
| **Encryption Test** | %100 | PHPUnit |
| **OWASP ZAP** | Tüm OWASP | OWASP ZAP |

### 8.2 Test Senaryoları

| # | Senaryo | Türü | Beklenen Sonuç |
|---|---------|------|----------------|
| 1 | CSRF token eksik | CSRF | 403 Forbidden |
| 2 | CSRF token yanlış | CSRF | 403 Forbidden |
| 3 | CSP nonce eksik | CSP | Script çalışmaz |
| 4 | Rate limit aşımı | Rate | 429 Too Many Requests |
| 5 | Session timeout | Session | Redirect login |
| 6 | Şifre yanlış | Auth | Login başarısız |
| 7 | SQL injection denemesi | Security | Query başarısız |

### 8.3 Test Komutları

```bash
# Security Testler
cd auth.coremusic.net && vendor/bin/phpunit --testsuite security

# CSRF Test
cd auth.coremusic.net && vendor/bin/phpunit tests/Unit/Security/CsrfTest.php

# CSP Test
cd auth.coremusic.net && vendor/bin/phpunit tests/Unit/Security/CspTest.php

# Rate Limit Test
cd auth.coremusic.net && vendor/bin/phpunit tests/Unit/Security/RateLimitTest.php
```

---

## 9. OWASP Compliance

### 9.1 OWASP Top 10 (2021) Uyumluluk

| OWASP Kategorisi | Durum | Uygulama |
|------------------|-------|----------|
| **A01:2021** Broken Access Control | ✅ Uyumlu | RBAC, auth middleware |
| **A02:2021** Cryptographic Failures | ✅ Uyumlu | AES-256-GCM, Argon2id |
| **A03:2021** Injection | ✅ Uyumlu | PDO prepared statement |
| **A04:2021** Insecure Design | ✅ Uyumlu | Secure by design |
| **A05:2021** Security Misconfiguration | ✅ Uyumlu | CSP, headers |
| **A06:2021** Vulnerable Components | ✅ Uyumlu | Composer audit |
| **A07:2021** Auth Failures | ✅ Uyumlu | Rate limiting, lockout |
| **A08:2021** Data Integrity Failures | ✅ Uyumlu | CSRF, signature |
| **A09:2021** Logging Failures | ✅ Uyumlu | Audit trail |
| **A10:2021** SSRF | ✅ Uyumlu | SSRF protection |

### 9.2 SSRF Koruması

```php
<?php
// SSRF Protection (ADR-022 uyumlu)
// Dosya: include/Security/SsrfProtection.php

// ✅ DOĞRU — URL validation
function validateUrl(string $url): bool
{
    $parsed = parse_url($url);
    
    // Private IP ranges
    $privateRanges = [
        '127.0.0.0/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16'
    ];
    
    // Check for private IPs
    $ip = gethostbyname($parsed['host']);
    foreach ($privateRanges as $range) {
        if (ip_in_range($ip, $range)) {
            return false; // Private IP — blocked
        }
    }
    
    return true;
}
```

---

## 10. Performance Impact

### 10.1 Güvenlik Overhead

| İşlem | Overhead | Kabul Edilebilir mi? |
|-------|----------|---------------------|
| CSRF token | < 1ms | ✅ Evet |
| CSP nonce | < 1ms | ✅ Evet |
| Rate limit | < 1ms | ✅ Evet |
| Session check | < 5ms | ✅ Evet |
| Encryption | < 10ms | ✅ Evet |
| Password hash | 100ms+ | ✅ Evet (kasıtlı) |

### 10.2 Cache Impact

| Cache | Overhead | Kullanım |
|-------|----------|---------|
| **Session Cache** | Düşük | Session data |
| **Rate Limit Cache** | Düşük | IP counts |
| **CSP Nonce Cache** | Yok | Per-request |

---

## 11. Rollback Plan

| Senaryo | Tetikleyici | Geri Alma Adımları |
|---------|-------------|-------------------|
| CSRF bypass | CSRF token hatası | 1. Eski token'ı geri yükle 2. Session'ı invalidate et |
| CSP block | CSP policy hatası | 1. CSP policy'yi gevşet 2. Hatalı script'i düzelt |
| Rate limit block | Rate limit aşımı | 1. Rate limit'i artır 2. IP'yi whitelist'e al |
| Auth bypass | Auth hatası | 1. Auth flow'u durdur 2. Manual intervention |

---

## 12. Related Decisions

| ADR | Başlık | İlişki |
|-----|--------|--------|
| ADR-010 | CSRF Protection | Ana kural |
| ADR-011 | Session Management | Ana kural |
| ADR-012 | CSP Nonce | Ana kural |
| ADR-013 | Rate Limiting | Ana kural |
| ADR-022 | DB Hardened Security | Ana kural |
| ADR-043 | Auth Consolidation | Ana kural |
| ADR-008 | Bypass Auth Middleware | Test bypass |

---

## 13. Glossary

| Terim | Tanım |
|-------|-------|
| **CSRF** | Cross-Site Request Forgery |
| **CSP** | Content Security Policy |
| **OWASP** | Open Web Application Security Project |
| **XSS** | Cross-Site Scripting |
| **SSRF** | Server-Side Request Forgery |
| **Argon2id** | Password hashing algorithm |
| **AES-256-GCM** | Authenticated encryption |
| **SameSite** | Cookie cross-site policy |

---

## 14. Edge Cases

| Durum | Belirti | Çözüm |
|-------|---------|-------|
| Multi-tab CSRF | Token farklı | Session-bound tek token |
| Session hijack | Cookie theft | HTTPS + SameSite |
| Rate limit bypass | Header spoofing | IP-based limiting |
| CSP bypass | Nonce leak | Per-request nonce |
| Auth bypass | Cookie manipulation | Signed cookies |

---

## 15. Warnings

> [!WARNING]
> **CSRF Token Key:** `_csrf_token` 2026-05-30'da kaldırıldı. `csrf_token` kullanılmalıdır (ADR-010).

> [!WARNING]
> **SameSite=None:** Kesinlikle kullanılmamalıdır. ADR-011 SameSite=Lax zorunlu.

> [!WARNING]
> **CSP strict-dynamic:** `unsafe-inline` ve `unsafe-eval` kesinlikle yasaktır (ADR-012).

---

## 16. Limitations

| # | Sınırlama | Etki | Gelecek Çözüm |
|---|-----------|------|---------------|
| 1 | APCu bağımlılığı | Orta | Redis fallback |
| 2 | Session-based | Düşük | JWT option (gelecek) |
| 3 | Single-domain | Düşük | Multi-domain support |

---

## 17. Dependencies

| Bağımlılık | Versiyon | Kullanım |
|------------|---------|---------|
| PHP 8.4+ | 8.4 | Backend runtime |
| OpenSSL | 3.0+ | AES-256-GCM |
| APCu | 5.1+ | Rate limiting |
| Argon2id | — | Password hashing |

---

## 18. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v1.1 | Redis rate limiting | 2026-Q4 |
| v2.0 | MFA support | 2027-Q1 |
| v2.1 | WebAuthn integration | 2027-Q2 |

---

## 19. Related Documents

| Dosya | Amaç |
|-------|------|
| [[architecture/l1-security]] | Security layer doc |
| [[architecture/07-security/middleware-security]] | Middleware security |
| [[architecture/07-security/encryption]] | Encryption standards |
| [[architecture/07-security/session-management]] | Session management |

---

## 20. Cross References

```
ADR-NNN (Security)
    │
    ├─► decisions/accepted/ADR-010-csrf-protection-strategy (CSRF)
    │
    ├─► decisions/accepted/ADR-011-session-management (Session)
    │
    ├─► decisions/accepted/ADR-012-csp-nonce-strict-dynamic (CSP)
    │
    ├─► decisions/accepted/ADR-013-rate-limiting-apcu (Rate Limit)
    │
    ├─► decisions/accepted/ADR-022-database-hardened-security (DB Security)
    │
    ├─► decisions/accepted/ADR-043-auth-subdomain-consolidation (Auth)
    │
    └─► architecture/l1-security (Security Layer)
```

---

## 21. Approval

| Rol | Kişi | Onay | Tarih |
|-----|------|------|-------|
| Security Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Backend Architect | [İsim] | ✅/❌ | YYYY-MM-DD |
| Vault Steward | [İsim] | ✅/❌ | YYYY-MM-DD |

---

*CoreMusic ADR Security Template v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
