---
title: "ADR-012: CSP Nonce Strict-Dynamic"
status: frozen
date: 2026-01-15
tags: [security, csp, nonce, strict-dynamic, xss, owasp, frozen]
---

# ADR-012: CSP Nonce Strict-Dynamic

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic platformunda Content Security Policy (CSP), **nonce-based strict-dynamic** stratejisi ile uygulanacaktÄ±r. `unsafe-inline` ve `unsafe-eval` kesinlikle yasaktÄ±r. Her HTTP isteÄŸi iÃ§in benzersiz bir CSP nonce Ã¼retilir ve sadece nonce'lu script/style'lar Ã§alÄ±ÅŸÄ±r. CSP nonce Ã¼retimi `random_bytes(32)` ile yapÄ±lÄ±r ve `base64_encode` ile 44 karakterlik string'e dÃ¶nÃ¼ÅŸtÃ¼rÃ¼lÃ¼r.

### 1.2 Temel GerekÃ§e

CSP, XSS (Cross-Site Scripting) saldÄ±rÄ±larÄ±nÄ±n en etkili korumasÄ±dÄ±r. `strict-dynamic` keyword'Ã¼, nonce ile iÅŸaretlenmiÅŸ script'lerin oluÅŸturduÄŸu tÃ¼m alt script'lerin Ã§alÄ±ÅŸmasÄ±na izin verir, bÃ¶ylece karmaÅŸÄ±k SPA uygulamalarÄ±nda bile gÃ¼venli CSP politikasÄ± uygulanabilir.

### 1.3 Beklenen SonuÃ§lar

- `unsafe-inline` ve `unsafe-eval` kesinlikle yasak
- Her istek iÃ§in benzersiz CSP nonce
- strict-dynamic ile script zinciri korumasÄ±
- XSS saldÄ±rÄ±larÄ± engellenir
- SPA uygulamalarÄ± ile uyumlu

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-15 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

XSS saldÄ±rÄ±larÄ±, zararlÄ± script'lerin kullanÄ±cÄ±nÄ±n tarayÄ±cÄ±sÄ±nda Ã§alÄ±ÅŸmasÄ±nÄ± saÄŸlar. CSP, hangi kaynaklardan script/style yÃ¼klenebileceÄŸini kÄ±sÄ±tlayarak XSS'i engeller. Ancak geleneksel CSP politikalarÄ± SPA uygulamalarÄ±yla uyumsuzdur. `strict-dynamic` keyword'Ã¼ bu sorunu Ã§Ã¶zer.

### 3.2 OWASP Top 10:2021 EtkileÅŸimi

| OWASP Kategorisi | Durum | Etki |
|------------------|-------|------|
| **A03:2021** Injection | âš ï¸ DoÄŸrudan | CSP, XSS injection'Ä± engeller |
| **A05:2021** Security Misconfiguration | âš ï¸ DoÄŸrudan | CSP policy yapÄ±landÄ±rmasÄ± |

### 3.3 CSP Policy YapÄ±sÄ±

```
Content-Security-Policy:
    default-src 'self';
    script-src 'self' 'nonce-{BASE64_NONCE}' 'strict-dynamic';
    style-src 'self' 'nonce-{BASE64_NONCE}';
    img-src 'self' data: https:;
    font-src 'self';
    connect-src 'self' https://api.coremusic.net;
    media-src 'self' https://media.coremusic.net;
    object-src 'none';
    base-uri 'self';
    form-action 'self';
    frame-ancestors 'none';
    upgrade-insecure-requests;
```

### 3.4 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | Kritiklik |
|---|-----|-----------|
| 1 | XSS saldÄ±rÄ±larÄ± | Kritik |
| 2 | SPA uygulama gereksinimi | YÃ¼ksek |
| 3 | OWASP A03:2021 | YÃ¼ksek |
| 4 | TrustedTypes entegrasyonu | YÃ¼ksek |

### 3.5 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | DeÄŸer |
|-----------|-------|
| `unsafe-inline` | âŒ Yasak |
| `unsafe-eval` | âŒ Yasak |
| Nonce boyutu | 32 byte (256-bit) |
| Nonce formatÄ± | base64_encode(random_bytes(32)) |
| Nonce Ã¶mrÃ¼ | Tek istek (per-request) |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, nonce-based strict-dynamic CSP politikasÄ± kullanÄ±r. `unsafe-inline` ve `unsafe-eval` kesinlikle yasaktÄ±r. Her HTTP isteÄŸi iÃ§in benzersiz CSP nonce Ã¼retilir.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | `unsafe-inline` yasak | âŒ Yasak |
| 2 | `unsafe-eval` yasak | âŒ Yasak |
| 3 | Per-request nonce | âœ… Zorunlu |
| 4 | random_bytes(32) | âœ… Zorunlu |
| 5 | base64_encode format | âœ… Zorunlu |
| 6 | strict-dynamic | âœ… Zorunlu |
| 7 | object-src 'none' | âœ… Zorunlu |
| 8 | base-uri 'self' | âœ… Zorunlu |
| 9 | frame-ancestors 'none' | âœ… Zorunlu |

### 4.3 Kod Ã–rnekleri

#### 4.3.1 CSP Nonce Ãœretimi (PHP)

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * CSP Nonce Service
 *
 * ADR-012 uyumlu CSP nonce Ã¼retimi ve yÃ¶netimi.
 * Per-request nonce: Her istek iÃ§in benzersiz
 * strict-dynamic: Script zinciri korumasÄ±
 */
final class CspNonceService
{
    private const NONCE_LENGTH = 32; // 256-bit entropy

    /**
     * Benzersiz CSP nonce Ã¼retir.
     *
     * @return string 44 karakterlik base64 nonce
     */
    public function generateNonce(): string
    {
        // ADR-022: Kriptografik rastgelelik
        return base64_encode(random_bytes(self::NONCE_LENGTH));
    }

    /**
     * CSP header'Ä±nÄ± oluÅŸturur.
     */
    public function buildCspHeader(string $nonce): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self' https://api.coremusic.net",
            "media-src 'self' https://media.coremusic.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests",
        ]);
    }

    /**
     * Script tag'ine nonce ekler.
     */
    public function scriptTag(string $nonce, string $src): string
    {
        return "<script nonce=\"{$nonce}\" src=\"{$src}\"></script>";
    }

    /**
     * Style tag'ine nonce ekler.
     */
    public function styleTag(string $nonce): string
    {
        return "<style nonce=\"{$nonce}\">";
    }
}
```

#### 4.3.2 SecurityHeaders Middleware

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Security\Service\CspNonceService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Security Headers Middleware
 *
 * ADR-012 uyumlu CSP header Ã¼retimi.
 * Nonce per-request: Her istek iÃ§in benzersiz
 * strict-dynamic: Script zinciri korumasÄ±
 */
final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly CspNonceService $cspService,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Per-request nonce Ã¼ret
        $nonce = $this->cspService->generateNonce();

        // Response'u al
        $response = $handler->handle($request);

        // CSP header ekle (ADR-012)
        $cspHeader = $this->cspService->buildCspHeader($nonce);
        $response = $response->withHeader('Content-Security-Policy', $cspHeader);

        // Ek gÃ¼venlik header'larÄ±
        $response = $response->withHeader('X-Content-Type-Options', 'nosniff');
        $response = $response->withHeader('X-Frame-Options', 'DENY');
        $response = $response->withHeader('X-XSS-Protection', '0'); // Modern tarayÄ±cÄ±lar iÃ§in devre dÄ±ÅŸÄ±
        $response = $response->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response = $response->withHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response = $response->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
```

#### 4.3.3 Frontend Nonce KullanÄ±mÄ± (JavaScript)

```javascript
/**
 * CSP Nonce Manager
 *
 * ADR-012 uyumlu frontend nonce yÃ¶netimi.
 * Dynamic script yÃ¼kleme iÃ§in nonce kullanÄ±lÄ±r.
 */
const CspNonceManager = (() => {
    'use strict';

    /**
     * Sayfadaki mevcut nonce'u okur.
     */
    function getCurrentNonce() {
        const metaTag = document.querySelector('meta[name="csp-nonce"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        return null;
    }

    /**
     * Dynamic script yÃ¼kler (nonce ile).
     */
    function loadScript(src) {
        const nonce = getCurrentNonce();
        if (!nonce) {
            console.error('[CSP] Nonce bulunamadÄ±');
            return Promise.reject(new Error('No CSP nonce'));
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.nonce = nonce;  // ADR-012: Nonce zorunlu
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Dynamic style yÃ¼kler (nonce ile).
     */
    function loadStyle(href) {
        const nonce = getCurrentNonce();
        if (!nonce) {
            console.error('[CSP] Nonce bulunamadÄ±');
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.nonce = nonce;  // ADR-012: Nonce zorunlu
        document.head.appendChild(link);
    }

    return Object.freeze({
        getCurrentNonce,
        loadScript,
        loadStyle,
    });
})();
```

### 4.4 KonfigÃ¼rasyon DeÄŸiÅŸiklikleri

| Dosya | Eski DeÄŸer | Yeni DeÄŸer |
|-------|-----------|-----------|
| `shared/config/csp.php` | â€” | strict-dynamic policy |
| `shared/config/middleware.php` | â€” | SecurityHeaders 4. sÄ±ra |

---

## 5. Architecture

### 5.1 CSP AkÄ±ÅŸ DiyagramÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Browser  â”‚                    â”‚   Server     â”‚
â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â””â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”˜
      â”‚                                â”‚
      â”‚  1. GET /page                  â”‚
      â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–ºâ”‚
      â”‚                                â”‚  2. Nonce Ã¼ret
      â”‚                                â”‚  random_bytes(32)
      â”‚                                â”‚  base64_encode()
      â”‚                                â”‚
      â”‚                                â”‚  3. CSP header oluÅŸtur
      â”‚                                â”‚  Content-Security-Policy:
      â”‚                                â”‚  script-src 'self'
      â”‚                                â”‚  'nonce-ABC123...'
      â”‚                                â”‚  'strict-dynamic'
      â”‚                                â”‚
      â”‚  4. Response + CSP header      â”‚
      â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚
      â”‚                                â”‚
      â”‚  5. Nonce'lu script Ã§alÄ±ÅŸtÄ±r   â”‚
      â”‚  <script nonce="ABC123...">    â”‚
      â”‚                                â”‚
      â”‚  6. Nonce'suz script engellenirâ”‚
      â”‚  <script src="evil.js">        â”‚
      â”‚  â†’ BLOCKED by CSP              â”‚
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| unsafe-inline | XSS riski, OWASP ihlali |
| unsafe-eval | XSS riski, OWASP ihlali |
| Hash-based CSP | SPA'da zor yÃ¶netilir |
| Report-only | Ãœretimde yetersiz |

---

## 7. Consequences

### Olumlu
- XSS saldÄ±rÄ±larÄ± engellenir
- OWASP A03:2021 uyumluluÄŸu
- SPA uyumluluÄŸu

### Olumsuz
- Per-request nonce overhead (~1ms)
- Dynamic script loading karmaÅŸÄ±klÄ±ÄŸÄ±

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Nonce Ã¼retimi | %100 |
| CSP header | %100 |
| Script blocking | %100 |
| XSS prevention | %100 |

---

## 9. OWASP Compliance

| OWASP | Durum |
|-------|-------|
| A03:2021 Injection | âœ… CSP ile engellenir |
| A05:2021 Misconfiguration | âœ… DoÄŸru CSP policy |

---

## 10. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |
| **OWASP** | âœ… |

---

*ADR-012: CSP Nonce Strict-Dynamic v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*