---
type: decision
id: "008"
title: "ADR-008: Bypass Auth Middleware"
category: "security"
status: "frozen"
date: "2026-02-05"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, bypass, auth, middleware, test, frozen]
risk-level: "medium"
owasp-top10: ["A01:2021", "A07:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l1-security]]"
---

# ADR-008: Bypass Auth Middleware

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic'te test ortamında auth bypass, `?_bypass=1` query parametresi ile mümkün olacaktır. Bu özellik **sadece test/development ortamında** aktiftir. Production ortamında bypass devre dışıdır ve `?_bypass=1` parametresi yok sayılır. Bypass durumunda varsayılan `guest` rolü atanır.

### 1.2 Temel Gerekçe

Test ve development süreçlerinde, kimlik doğrulama olmadan API'leri test etmek gerekir. Ancak production'da bypass kesinlikle devre dışı olmalıdır. Bu, security-by-design prensibinin bir gereğidir.

### 1.3 Beklenen Sonuçlar

- Test ortamında `?_bypass=1` ile auth bypass
- Production'da bypass devre dışı
- Varsayılan guest rolü atanması
- Audit trail loglaması

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-02-05 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | medium |
| **Onay** | Red Team · Human Mode · Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

Test süreçlerinde auth bypass olmadığında:
- API endpoint'leri test edilemez
- Integration testleri çalıştırılamaz
- Development hızı düşer
- Mock auth karmaşıklığı artar

Ancak production'da bypass devre dışı olmalıdır.

### 3.2 Middleware Pipeline'daki Yeri

```
...
6. CsrfMiddleware (ADR-010)
7. BypassAuthMiddleware ◄══ ADR-008 BU SATIRDA
   • ?_bypass=1 kontrolü
   • Production'da devre dışı
   • Guest rolü ataması
8. AuthMiddleware
...
```

### 3.3 İtici Güçler

| # | Güç | Kritiklik |
|---|-----|-----------|
| 1 | Test süreç hızı | Yüksek |
| 2 | Integration test gereksinimi | Yüksek |
| 3 | Development kolaylığı | Orta |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Değer |
|-----------|-------|
| Query parametresi | `?_bypass=1` |
| Ortam kontrolü | `APP_ENV !== 'production'` |
| Varsayılan rol | `guest` |
| Audit trail | Zorunlu |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te test ortamında `?_bypass=1` ile auth bypass mümkündür. Production'da bypass devre dışıdır.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | `?_bypass=1` sadece test'te | ✅ Zorunlu |
| 2 | Production'da bypass yok | ✅ Zorunlu |
| 3 | Guest rolü ataması | ✅ Zorunlu |
| 4 | Audit trail loglaması | ✅ Zorunlu |
| 5 | Bypass header'ı | ❌ Yasak (sadece query) |

### 4.3 Kod Örnekleri

#### 4.3.1 Bypass Auth Middleware

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Bypass Auth Middleware
 *
 * ADR-008 uyumlu test bypass middleware'i.
 * Sadece development/test ortamında aktif.
 * Production'da devre dışı.
 */
final class BypassAuthMiddleware implements MiddlewareInterface
{
    private const BYPASS_PARAM = '_bypass';
    private const BYPASS_VALUE = '1';
    private const GUEST_ROLE = 'guest';
    private const GUEST_USER_ID = 0;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Production'da bypass devre dışı
        if ($this->isProduction()) {
            return $handler->handle($request);
        }

        // Bypass parametresi kontrolü
        $queryParams = $request->getQueryParams();
        $bypassValue = $queryParams[self::BYPASS_PARAM] ?? null;

        if ($bypassValue === self::BYPASS_VALUE) {
            // Audit trail
            error_log(sprintf(
                '[SECURITY] Auth bypass activated. IP: %s, URI: %s',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $request->getUri()->getPath()
            ));

            // Guest rolü ata
            $request = $request->withAttribute('auth_bypassed', true);
            $request = $request->withAttribute('user_id', self::GUEST_USER_ID);
            $request = $request->withAttribute('user_role', self::GUEST_ROLE);
            $request = $request->withAttribute('user_authenticated', false);
        }

        return $handler->handle($request);
    }

    /**
     * Production ortamında olup olmadığını kontrol eder.
     */
    private function isProduction(): bool
    {
        return (getenv('APP_ENV') ?: 'development') === 'production';
    }
}
```

### 4.4 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `.env` | `APP_ENV=development` / `APP_ENV=production` |

---

## 5. Architecture

```
Request → ?_bypass=1? → Production? → Yes → Skip bypass → Auth middleware
                              │
                              No
                              │
                              ▼
                        Guest role assigned
                        Audit trail logged
                        Continue to Auth
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Header-based bypass | Header spoofing riski |
| IP-based bypass | IP spoofing riski |
| Secret token bypass | Token sızıntısı riski |

---

## 7. Consequences

### Olumlu
- Test süreç hızı artar
- Integration testleri kolaylaşır

### Olumsuz
- Misuse riski (production'da devre dışı)
- Audit trail zorunlu

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Bypass aktif (dev) | %100 |
| Bypass devre dışı (prod) | %100 |
| Guest rolü | %100 |
| Audit trail | %100 |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-008: Bypass Auth Middleware v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
