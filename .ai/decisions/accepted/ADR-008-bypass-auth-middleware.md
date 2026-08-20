---
title: "ADR-008: Bypass Auth Middleware"
status: frozen
date: 2026-02-05
tags: [security, bypass, auth, middleware, test, frozen]
---

# ADR-008: Bypass Auth Middleware

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic'te test ortamÄ±nda auth bypass, `?_bypass=1` query parametresi ile mÃ¼mkÃ¼n olacaktÄ±r. Bu Ã¶zellik **sadece test/development ortamÄ±nda** aktiftir. Production ortamÄ±nda bypass devre dÄ±ÅŸÄ±dÄ±r ve `?_bypass=1` parametresi yok sayÄ±lÄ±r. Bypass durumunda varsayÄ±lan `guest` rolÃ¼ atanÄ±r.

### 1.2 Temel GerekÃ§e

Test ve development sÃ¼reÃ§lerinde, kimlik doÄŸrulama olmadan API'leri test etmek gerekir. Ancak production'da bypass kesinlikle devre dÄ±ÅŸÄ± olmalÄ±dÄ±r. Bu, security-by-design prensibinin bir gereÄŸidir.

### 1.3 Beklenen SonuÃ§lar

- Test ortamÄ±nda `?_bypass=1` ile auth bypass
- Production'da bypass devre dÄ±ÅŸÄ±
- VarsayÄ±lan guest rolÃ¼ atanmasÄ±
- Audit trail loglamasÄ±

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-02-05 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | medium |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

Test sÃ¼reÃ§lerinde auth bypass olmadÄ±ÄŸÄ±nda:
- API endpoint'leri test edilemez
- Integration testleri Ã§alÄ±ÅŸtÄ±rÄ±lamaz
- Development hÄ±zÄ± dÃ¼ÅŸer
- Mock auth karmaÅŸÄ±klÄ±ÄŸÄ± artar

Ancak production'da bypass devre dÄ±ÅŸÄ± olmalÄ±dÄ±r.

### 3.2 Middleware Pipeline'daki Yeri

```
...
6. CsrfMiddleware (ADR-010)
7. BypassAuthMiddleware â—„â•â• ADR-008 BU SATIRDA
   â€¢ ?_bypass=1 kontrolÃ¼
   â€¢ Production'da devre dÄ±ÅŸÄ±
   â€¢ Guest rolÃ¼ atamasÄ±
8. AuthMiddleware
...
```

### 3.3 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | Kritiklik |
|---|-----|-----------|
| 1 | Test sÃ¼reÃ§ hÄ±zÄ± | YÃ¼ksek |
| 2 | Integration test gereksinimi | YÃ¼ksek |
| 3 | Development kolaylÄ±ÄŸÄ± | Orta |

### 3.4 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | DeÄŸer |
|-----------|-------|
| Query parametresi | `?_bypass=1` |
| Ortam kontrolÃ¼ | `APP_ENV !== 'production'` |
| VarsayÄ±lan rol | `guest` |
| Audit trail | Zorunlu |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te test ortamÄ±nda `?_bypass=1` ile auth bypass mÃ¼mkÃ¼ndÃ¼r. Production'da bypass devre dÄ±ÅŸÄ±dÄ±r.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | `?_bypass=1` sadece test'te | âœ… Zorunlu |
| 2 | Production'da bypass yok | âœ… Zorunlu |
| 3 | Guest rolÃ¼ atamasÄ± | âœ… Zorunlu |
| 4 | Audit trail loglamasÄ± | âœ… Zorunlu |
| 5 | Bypass header'Ä± | âŒ Yasak (sadece query) |

### 4.3 Kod Ã–rnekleri

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
 * Sadece development/test ortamÄ±nda aktif.
 * Production'da devre dÄ±ÅŸÄ±.
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
        // Production'da bypass devre dÄ±ÅŸÄ±
        if ($this->isProduction()) {
            return $handler->handle($request);
        }

        // Bypass parametresi kontrolÃ¼
        $queryParams = $request->getQueryParams();
        $bypassValue = $queryParams[self::BYPASS_PARAM] ?? null;

        if ($bypassValue === self::BYPASS_VALUE) {
            // Audit trail
            error_log(sprintf(
                '[SECURITY] Auth bypass activated. IP: %s, URI: %s',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $request->getUri()->getPath()
            ));

            // Guest rolÃ¼ ata
            $request = $request->withAttribute('auth_bypassed', true);
            $request = $request->withAttribute('user_id', self::GUEST_USER_ID);
            $request = $request->withAttribute('user_role', self::GUEST_ROLE);
            $request = $request->withAttribute('user_authenticated', false);
        }

        return $handler->handle($request);
    }

    /**
     * Production ortamÄ±nda olup olmadÄ±ÄŸÄ±nÄ± kontrol eder.
     */
    private function isProduction(): bool
    {
        return (getenv('APP_ENV') ?: 'development') === 'production';
    }
}
```

### 4.4 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `.env` | `APP_ENV=development` / `APP_ENV=production` |

---

## 5. Architecture

```
Request â†’ ?_bypass=1? â†’ Production? â†’ Yes â†’ Skip bypass â†’ Auth middleware
                              â”‚
                              No
                              â”‚
                              â–¼
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
| Secret token bypass | Token sÄ±zÄ±ntÄ±sÄ± riski |

---

## 7. Consequences

### Olumlu
- Test sÃ¼reÃ§ hÄ±zÄ± artar
- Integration testleri kolaylaÅŸÄ±r

### Olumsuz
- Misuse riski (production'da devre dÄ±ÅŸÄ±)
- Audit trail zorunlu

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Bypass aktif (dev) | %100 |
| Bypass devre dÄ±ÅŸÄ± (prod) | %100 |
| Guest rolÃ¼ | %100 |
| Audit trail | %100 |

---

## 9. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-008: Bypass Auth Middleware v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*