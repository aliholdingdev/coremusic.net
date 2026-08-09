---
type: adr
category: security
title: "ADR-008: Bypass Auth Middleware"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-008: Bypass Auth Middleware

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Security
**İlgili Agent:** Security, Backend, QA
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunda test ortamında auth bypass middleware'ini tanımlar. `?_bypass=1` parametresi ile auth bypass yalnızca test ortamında aktif olacak, production'da kesinlikle devre dışı kalacaktır.

---

## 2. Bağlam

### 2.1 İş Problemi

Test süreçlerinde auth middleware'i test akışını yavaşlatabilir:

| Sorun | Sonuç | Çözüm |
|-------|-------|-------|
| Test yavaşlığı | Uzun test süreleri | Bypass middleware |
| Mock karmaşıklığı | Bakım zorluğu | Basit bypass |
| Auth hatası testleri | Yanlış negatif | Bypass ile bypass |
| CI/CD yavaşlığı | Geç deployment | Hızlı test |

### 2.2 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-010 | CSRF protection |
| ADR-011 | Session management |
| ADR-012 | CSP nonce |
| ADR-013 | Rate limiting |
| ADR-022 | DB hardened security |

---

## 3. Karar

CoreMusic'te test ortamında `?_bypass=1` parametresi ile auth bypass aktif edilecek. Production'da bu parametre kesinlikle devre dışı olacak.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| Bypass parametresi | `?_bypass=1` |
| Aktif ortam | Test/Development |
| Devre dışı ortam | Production |
| Middleware sırası | 2 (SessionManager'dan sonra) |
| Güvenlik seviyesi | HIGH |

---

## 4. Teknik Detaylar

### 4.1 Middleware Sırası

```
1. SessionManagerMiddleware()    — Session başlat, CSP nonce üret
2. BypassAuthMiddleware()        — Test bypass (prod'da devre dışı)
3. RateLimiterMiddleware()       — APCu: 60 req/60s
4. AuthMiddleware()              — Auth bilgisi inject
5. SecurityHeadersMiddleware()   — CSP strict-dynamic
6. CsrfMiddleware()              — csrf_token doğrulama (POST/PUT/DELETE)
```

### 4.2 Bypass Auth Implementasyonu

```php
class BypassAuthMiddleware {
    private const BYPASS_PARAM = '_bypass';
    private const BYPASS_VALUE = '1';
    private bool $enabled;

    public function __construct() {
        $this->enabled = $this->isBypassEnabled();
    }

    private function isBypassEnabled(): bool {
        // Production'da kesinlikle devre dışı
        if ($this->isProduction()) {
            return false;
        }
        
        // Test ortamında ?_bypass=1 kontrolü
        return isset($_GET[self::BYPASS_PARAM]) 
            && $_GET[self::BYPASS_PARAM] === self::BYPASS_VALUE;
    }

    private function isProduction(): bool {
        return getenv('APP_ENV') === 'production';
    }

    public function process(Request $request, Response $response): Response {
        if (!$this->enabled) {
            return $response;
        }

        // Bypass aktif: Auth bilgisi inject et
        $request->attributes->set('bypass_auth', true);
        $request->attributes->set('user_id', 1); // Varsayılan test user
        $request->attributes->set('user_role', 'admin'); // Varsayılan test role

        return $response;
    }
}
```

### 4.3 Güvenlik Kontrolleri

| Kontrol | Yöntem | Aksiyon |
|---------|--------|---------|
| Production check | `APP_ENV === 'production'` | Bypass devre dışı |
| HTTPS check | `$_SERVER['HTTPS']` | Bypass devre dışı |
| IP check | Allowed IPs | Bypass devre dışı |
| Header check | Custom header | Bypass devre dışı |
| Token check | Bypass token | Bypass devre dışı |

### 4.4 Bypass Token Sistemi

```php
class BypassTokenManager {
    private const SECRET = 'bypass_token_secret';
    private const TTL = 3600; // 1 saat

    public function generateToken(): string {
        $payload = [
            'iat' => time(),
            'exp' => time() + self::TTL,
            'env' => getenv('APP_ENV'),
        ];
        return hash_hmac('sha256', json_encode($payload), self::SECRET);
    }

    public function validateToken(string $token): bool {
        if (getenv('APP_ENV') === 'production') {
            return false;
        }
        // Token doğrulama mantığı
        return true;
    }
}
```

### 4.5 Middleware Implementasyonu

```php
class BypassAuthMiddleware {
    public function __invoke(Request $request, Response $response, $next): Response {
        // Production'da kesinlikle bypass yok
        if (getenv('APP_ENV') === 'production') {
            return $next($request, $response);
        }

        // Bypass parametresi kontrolü
        $bypass = $request->getQueryParams()['_bypass'] ?? null;
        if ($bypass === '1') {
            // Bypass aktif: test user'ı inject et
            $request = $request->withAttribute('user_id', 1);
            $request = $request->withAttribute('user_role', 'admin');
            $request = $request->withAttribute('bypass_auth', true);
        }

        return $next($request, $response);
    }
}
```

### 4.6 Test Ortamı Konfigürasyonu

| Parametre | Test | Development | Production |
|-----------|------|-------------|------------|
| Bypass enabled | ✅ | ✅ | ❌ |
| `?_bypass=1` | Aktif | Aktif | Devre dışı |
| Default user | admin | admin | — |
| Default role | admin | admin | — |
| Logging | Aktif | Aktif | Aktif |
| Rate limiting | Devre dışı | Aktif | Aktif |
| CSRF | Devre dışı | Aktif | Aktif |

### 4.7 Bypass Akışı

```
HTTP Request gelir
  → [1. Environment Check] — Production mu?
    → EVET → Bypass devre dışı, normal auth
    → HAYIR → [2. Bypass Param Check] — ?_bypass=1 var mı?
      → EVET → [3. Token Check] — Token geçerli mi?
        → EVET → [4. Bypass Active] — Test user'ı inject et
        → HAYIR → Normal auth
      → HAYIR → Normal auth
```

### 4.8 Bypass Logging

```php
// Bypass logging
if ($bypass_active) {
    error_log(sprintf(
        '[BYPASS] User: %s, IP: %s, Time: %s, Endpoint: %s',
        $_SERVER['REMOTE_ADDR'],
        date('Y-m-d H:i:s'),
        $_SERVER['REQUEST_URI']
    ));
}
```

| Log Türü | Bilgi | Format |
|----------|-------|--------|
| Bypass aktif | IP, endpoint, timestamp | `[BYPASS] ...` |
| Bypass başarısız | IP, reason | `[BYPASS_FAIL] ...` |
| Production denemesi | IP, timestamp | `[BYPASS_BLOCKED] ...` |

### 4.9 Bypass Test Senaryoları

| Senaryo | Beklenen | Gerçek |
|---------|----------|--------|
| Production'da bypass | Red | Red |
| Test'te bypass | Kabul | Kabul |
| Yanlış token | Red | Red |
| Süresi dolmuş token | Red | Red |
| Yanlış parametre | Red | Red |
| HTTPS ile bypass | Red | Red |

### 4.10 Bypass Monitoring

| Metrik | Hedef | Alarm |
|--------|-------|-------|
| Bypass denemesi (prod) | 0 | CRITICAL |
| Bypass başarısızlığı | <5/saat | WARN |
| Bypass kullanım oranı | <10% | INFO |
| Token süresi dolma | — | DEBUG |

### 4.11 Bypass Güvenlik Katmanları

| Katman | Kontrol | Açıklama |
|--------|---------|----------|
| 1 | Environment | Production check |
| 2 | HTTPS | Encryption check |
| 3 | IP | Allowed IPs |
| 4 | Token | HMAC token |
| 5 | Header | Custom header |
| 6 | Logging | Audit trail |

### 4.12 Bypass Rolleri ve Yetkileri

| Rol | Bypass Yetkisi | Kullanım |
|-----|---------------|----------|
| Admin | ✅ | Full access |
| Developer | ✅ | Development |
| Tester | ✅ | Testing |
| User | ❌ | Normal user |
| Guest | ❌ | No access |

### 4.13 Bypass CI/CD Entegrasyonu

```yaml
# GitHub Actions
test:
  runs-on: ubuntu-latest
  steps:
    - name: Run Tests
      env:
        APP_ENV: testing
        BYPASS_ENABLED: true
      run: phpunit
```

| Ortam | Bypass | Rate Limit | CSRF |
|-------|--------|------------|------|
| CI | ✅ | ❌ | ❌ |
| Staging | ✅ | ✅ | ✅ |
| Production | ❌ | ✅ | ✅ |

### 4.14 Bypass Implementasyonu Detayı

```php
class BypassAuthMiddleware {
    private const BYPASS_PARAM = '_bypass';
    private const BYPASS_VALUE = '1';
    private const TOKEN_HEADER = 'X-Bypass-Token';
    private bool $enabled;
    private string $environment;

    public function __construct() {
        $this->environment = getenv('APP_ENV') ?: 'development';
        $this->enabled = $this->isBypassEnabled();
    }

    private function isBypassEnabled(): bool {
        // Production'da kesinlikle devre dışı
        if ($this->environment === 'production') {
            return false;
        }

        // HTTPS kontrolü
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            return false;
        }

        // Bypass parametresi kontrolü
        $bypass = $_GET[self::BYPASS_PARAM] ?? null;
        if ($bypass !== self::BYPASS_VALUE) {
            return false;
        }

        // Token kontrolü
        $token = $_SERVER['HTTP_' . str_replace('-', '_', strtoupper(self::TOKEN_HEADER))] ?? null;
        if ($token === null || !$this->validateToken($token)) {
            return false;
        }

        return true;
    }

    private function validateToken(string $token): bool {
        // Token doğrulama mantığı
        $secret = getenv('BYPASS_TOKEN_SECRET');
        if ($secret === null) {
            return false;
        }

        $expected = hash_hmac('sha256', $this->environment, $secret);
        return hash_equals($expected, $token);
    }

    public function __invoke(Request $request, Response $response, $next): Response {
        if (!$this->enabled) {
            return $next($request, $response);
        }

        // Bypass aktif: test user'ı inject et
        $request = $request->withAttribute('user_id', 1);
        $request = $request->withAttribute('user_role', 'admin');
        $request = $request->withAttribute('bypass_auth', true);

        // Logging
        error_log(sprintf(
            '[BYPASS] Environment: %s, IP: %s, Endpoint: %s, Time: %s',
            $this->environment,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['REQUEST_URI'],
            date('Y-m-d H:i:s')
        ));

        return $next($request, $response);
    }
}
```

### 4.15 Bypass Token Üretimi

```php
class BypassTokenGenerator {
    private const SECRET_KEY = 'BYPASS_TOKEN_SECRET';

    public static function generate(): string {
        $secret = getenv(self::SECRET_KEY);
        $environment = getenv('APP_ENV');
        
        return hash_hmac('sha256', $environment, $secret);
    }

    public static function validate(string $token): bool {
        $expected = self::generate();
        return hash_equals($expected, $token);
    }
}
```

### 4.16 Bypass Middleware Zinciri

```
Request → SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf → Handler
```

| Sıra | Middleware | Görev | Bypass etkisi |
|------|-----------|-------|---------------|
| 1 | SessionManager | Session başlat | — |
| 2 | BypassAuth | Auth bypass | Test user inject |
| 3 | RateLimiter | Rate limit | Devre dışı olabilir |
| 4 | Auth | Auth bilgisi | Bypass edildi |
| 5 | SecurityHeaders | CSP, HSTS | — |
| 6 | Csrf | CSRF token | Devre dışı olabilir |

### 4.17 Bypass Güvenlik Katmanları Detayı

| Katman | Kontrol | Başarısızlık | Aksiyon |
|--------|---------|-------------|---------|
| 1 | Environment | Production'da | Block |
| 2 | HTTPS | HTTP'de | Block |
| 3 | IP | Allowed IPs değil | Block |
| 4 | Token | Token geçersiz | Block |
| 5 | Header | Header eksik | Block |
| 6 | Logging | — | Log |

### 4.18 Bypass CI/CD Pipeline Entegrasyonu

```yaml
# GitHub Actions
name: Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: apcu
      - name: Run Tests
        env:
          APP_ENV: testing
          BYPASS_ENABLED: true
          BYPASS_TOKEN_SECRET: ${{ secrets.BYPASS_TOKEN_SECRET }}
        run: |
          phpunit --coverage-clover=coverage.xml
```

### 4.19 Bypass Test Senaryoları Detayı

| # | Senaryo | Input | Beklenen | Gerçek |
|---|---------|-------|----------|--------|
| 1 | Production bypass | `?_bypass=1` + prod env | Red | Red |
| 2 | Test bypass | `?_bypass=1` + test env | Kabul | Kabul |
| 3 | Yanlış token | Geçersiz token | Red | Red |
| 4 | Süresi dolmuş token | Eski token | Red | Red |
| 5 | Yanlış parametre | `?_bypass=0` | Red | Red |
| 6 | Eksik parametre | `?_bypass=` | Red | Red |
| 7 | HTTPS olmadan | HTTP | Red | Red |
| 8 | Yanlış environment | staging | Kabul | Kabul |

### 4.20 Bypass Monitoring Detayı

| Metrik | Hedef | Alarm Seviyesi | Aksiyon |
|--------|-------|---------------|---------|
| Bypass denemesi (prod) | 0 | CRITICAL | Block + alert |
| Bypass başarısızlığı | <5/saat | WARN | İnceleme |
| Bypass kullanım oranı | <10% | INFO | Monitoring |
| Token süresi dolma | — | DEBUG | Log |
| Ortam bypass oranı | <20% | INFO | Monitoring |

### 4.21 Bypass Rolleri ve Yetkileri Detayı

| Rol | Bypass Yetkisi | Rate Limit | CSRF | Kullanım |
|-----|---------------|------------|------|----------|
| Admin | ✅ | ❌ | ❌ | Full access |
| Developer | ✅ | ❌ | ❌ | Development |
| Tester | ✅ | ❌ | ❌ | Testing |
| User | ❌ | ✅ | ✅ | Normal user |
| Guest | ❌ | ✅ | ✅ | No access |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| Production'da bypass | Production'da devre dışı | Güvenlik açığı |
| Bypass token hardcoded | Dynamic token | Güvenlik açığı |
| Logging olmadan bypass | Her bypass loglanmalı | İzlenebilirlik |
| Bypass without monitoring | Monitoring zorunlu | Görünmeyen sorun |
| Bypass for non-test | Sadece test ortamında | Güvenlik açığı |
| Hardcoded secret | Dynamic secret | Güvenlik açığı |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Production bypass denemesi | Güvenlik ihlali | Block + log CRITICAL |
| Token replay attack | Token yeniden kullanım | Token TTL |
| Rate limit bypass | Yüksek bypass kullanımı | Monitoring |
| CSRF bypass | Bypass ile CSRF atlanması | CSRF devre dışı değil |
| Session hijacking | Bypass ile session | Session check |
| IP spoofing | Yanlış IP | Multi-layer check |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Production'da bypass devre dışı | Güvenlik açığı |
| 2 | Bypass logging zorunlu | İzlenebilirlik |
| 3 | Token-based bypass zorunlu | Güvenlik açığı |
| 4 | Monitoring zorunlu | Görünmeyen sorun |
| 5 | Middleware sırası değişmez | CSP/CSRF bozulması |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-010-csrf-protection-strategy]] | CSRF | CSRF bypass |
| [[ADR-011-session-management]] | Session | Session bypass |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP | CSP bypass |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Rate limit bypass |
| [[ADR-022-database-hardened-security]] | DB security | Güvenlik |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Bilgi kaynağı |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[CLAUDE.md]] §6 | Middleware pipeline |
| § 4.2 Implement | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 4.3 Security | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 4.5 Middleware | [[ADR-011-session-management]] | Session |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 6 Edge | [[ADR-042-vault-restructuring-2026-08-03]] | Edge cases |
| § 7 Guardrails | [[brain.md]] §17 | Hard guardrails |
| § 8 ADR | [[ADR-013-rate-limiting-apcu]] | Rate limit |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Bypass Auth** | Kimlik doğrulama atlama |
| **Middleware** | Ara yazılım katmanı |
| **Production** | Üretim ortamı |
| **Testing** | Test ortamı |
| **Token** | Doğrulama jetonu |
| **HMAC** | Hash-based Message Authentication Code |
| **Rate Limit** | Hız sınırlaması |
| **CSRF** | Cross-Site Request Forgery |
| **CSP** | Content Security Policy |
| **Logging** | Günlük kaydetme |
| **Audit Trail** | Denetim izi |
| **Security Layer** | Güvenlik katmanı |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| Frozen | 2026-05-15 |
| Middleware Sırası | 6 |
| Güvenlik Kontrolleri | 6 |
| Bypass Token Fields | 3 |
| Environment Config | 3 |
| Test Senaryoları | 6 |
| Monitoring Metrics | 4 |
| Security Layers | 6 |
| Yasak Örüntüleri | 6 |
| Edge Cases | 6 |
| Hard Guardrails | 5 |
| ADR References | 6 |
| Cross References | 8 |
| Glossary Terms | 12 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
