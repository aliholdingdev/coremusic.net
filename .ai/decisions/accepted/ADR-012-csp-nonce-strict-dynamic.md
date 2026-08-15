---
type: decision
id: "012"
title: "ADR-012: CSP Nonce Strict-Dynamic"
category: "security"
status: "frozen"
date: "2026-01-15"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, csp, nonce, strict-dynamic, xss, owasp, frozen]
risk-level: "critical"
owasp-top10: ["A03:2021", "A05:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-013-rate-limiting-apcu]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l1-security]]"
---

# ADR-012: CSP Nonce Strict-Dynamic

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic platformunda Content Security Policy (CSP), **nonce-based strict-dynamic** stratejisi ile uygulanacaktır. `unsafe-inline` ve `unsafe-eval` kesinlikle yasaktır. Her HTTP isteği için benzersiz bir CSP nonce üretilir ve sadece nonce'lu script/style'lar çalışır. CSP nonce üretimi `random_bytes(32)` ile yapılır ve `base64_encode` ile 44 karakterlik string'e dönüştürülür.

### 1.2 Temel Gerekçe

CSP, XSS (Cross-Site Scripting) saldırılarının en etkili korumasıdır. `strict-dynamic` keyword'ü, nonce ile işaretlenmiş script'lerin oluşturduğu tüm alt script'lerin çalışmasına izin verir, böylece karmaşık SPA uygulamalarında bile güvenli CSP politikası uygulanabilir.

### 1.3 Beklenen Sonuçlar

- `unsafe-inline` ve `unsafe-eval` kesinlikle yasak
- Her istek için benzersiz CSP nonce
- strict-dynamic ile script zinciri koruması
- XSS saldırıları engellenir
- SPA uygulamaları ile uyumlu

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-01-15 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |
| **Onay** | Red Team · Human Mode · Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

XSS saldırıları, zararlı script'lerin kullanıcının tarayıcısında çalışmasını sağlar. CSP, hangi kaynaklardan script/style yüklenebileceğini kısıtlayarak XSS'i engeller. Ancak geleneksel CSP politikaları SPA uygulamalarıyla uyumsuzdur. `strict-dynamic` keyword'ü bu sorunu çözer.

### 3.2 OWASP Top 10:2021 Etkileşimi

| OWASP Kategorisi | Durum | Etki |
|------------------|-------|------|
| **A03:2021** Injection | ⚠️ Doğrudan | CSP, XSS injection'ı engeller |
| **A05:2021** Security Misconfiguration | ⚠️ Doğrudan | CSP policy yapılandırması |

### 3.3 CSP Policy Yapısı

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

### 3.4 İtici Güçler

| # | Güç | Kritiklik |
|---|-----|-----------|
| 1 | XSS saldırıları | Kritik |
| 2 | SPA uygulama gereksinimi | Yüksek |
| 3 | OWASP A03:2021 | Yüksek |
| 4 | TrustedTypes entegrasyonu | Yüksek |

### 3.5 Teknik Kısıtlamalar

| Kısıtlama | Değer |
|-----------|-------|
| `unsafe-inline` | ❌ Yasak |
| `unsafe-eval` | ❌ Yasak |
| Nonce boyutu | 32 byte (256-bit) |
| Nonce formatı | base64_encode(random_bytes(32)) |
| Nonce ömrü | Tek istek (per-request) |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, nonce-based strict-dynamic CSP politikası kullanır. `unsafe-inline` ve `unsafe-eval` kesinlikle yasaktır. Her HTTP isteği için benzersiz CSP nonce üretilir.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | `unsafe-inline` yasak | ❌ Yasak |
| 2 | `unsafe-eval` yasak | ❌ Yasak |
| 3 | Per-request nonce | ✅ Zorunlu |
| 4 | random_bytes(32) | ✅ Zorunlu |
| 5 | base64_encode format | ✅ Zorunlu |
| 6 | strict-dynamic | ✅ Zorunlu |
| 7 | object-src 'none' | ✅ Zorunlu |
| 8 | base-uri 'self' | ✅ Zorunlu |
| 9 | frame-ancestors 'none' | ✅ Zorunlu |

### 4.3 Kod Örnekleri

#### 4.3.1 CSP Nonce Üretimi (PHP)

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * CSP Nonce Service
 *
 * ADR-012 uyumlu CSP nonce üretimi ve yönetimi.
 * Per-request nonce: Her istek için benzersiz
 * strict-dynamic: Script zinciri koruması
 */
final class CspNonceService
{
    private const NONCE_LENGTH = 32; // 256-bit entropy

    /**
     * Benzersiz CSP nonce üretir.
     *
     * @return string 44 karakterlik base64 nonce
     */
    public function generateNonce(): string
    {
        // ADR-022: Kriptografik rastgelelik
        return base64_encode(random_bytes(self::NONCE_LENGTH));
    }

    /**
     * CSP header'ını oluşturur.
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
 * ADR-012 uyumlu CSP header üretimi.
 * Nonce per-request: Her istek için benzersiz
 * strict-dynamic: Script zinciri koruması
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
        // Per-request nonce üret
        $nonce = $this->cspService->generateNonce();

        // Response'u al
        $response = $handler->handle($request);

        // CSP header ekle (ADR-012)
        $cspHeader = $this->cspService->buildCspHeader($nonce);
        $response = $response->withHeader('Content-Security-Policy', $cspHeader);

        // Ek güvenlik header'ları
        $response = $response->withHeader('X-Content-Type-Options', 'nosniff');
        $response = $response->withHeader('X-Frame-Options', 'DENY');
        $response = $response->withHeader('X-XSS-Protection', '0'); // Modern tarayıcılar için devre dışı
        $response = $response->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response = $response->withHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response = $response->withHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
```

#### 4.3.3 Frontend Nonce Kullanımı (JavaScript)

```javascript
/**
 * CSP Nonce Manager
 *
 * ADR-012 uyumlu frontend nonce yönetimi.
 * Dynamic script yükleme için nonce kullanılır.
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
     * Dynamic script yükler (nonce ile).
     */
    function loadScript(src) {
        const nonce = getCurrentNonce();
        if (!nonce) {
            console.error('[CSP] Nonce bulunamadı');
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
     * Dynamic style yükler (nonce ile).
     */
    function loadStyle(href) {
        const nonce = getCurrentNonce();
        if (!nonce) {
            console.error('[CSP] Nonce bulunamadı');
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

### 4.4 Konfigürasyon Değişiklikleri

| Dosya | Eski Değer | Yeni Değer |
|-------|-----------|-----------|
| `shared/config/csp.php` | — | strict-dynamic policy |
| `shared/config/middleware.php` | — | SecurityHeaders 4. sıra |

---

## 5. Architecture

### 5.1 CSP Akış Diyagramı

```
┌──────────┐                    ┌──────────────┐
│  Browser  │                    │   Server     │
└─────┬────┘                    └──────┬───────┘
      │                                │
      │  1. GET /page                  │
      │───────────────────────────────►│
      │                                │  2. Nonce üret
      │                                │  random_bytes(32)
      │                                │  base64_encode()
      │                                │
      │                                │  3. CSP header oluştur
      │                                │  Content-Security-Policy:
      │                                │  script-src 'self'
      │                                │  'nonce-ABC123...'
      │                                │  'strict-dynamic'
      │                                │
      │  4. Response + CSP header      │
      │◄───────────────────────────────│
      │                                │
      │  5. Nonce'lu script çalıştır   │
      │  <script nonce="ABC123...">    │
      │                                │
      │  6. Nonce'suz script engellenir│
      │  <script src="evil.js">        │
      │  → BLOCKED by CSP              │
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| unsafe-inline | XSS riski, OWASP ihlali |
| unsafe-eval | XSS riski, OWASP ihlali |
| Hash-based CSP | SPA'da zor yönetilir |
| Report-only | Üretimde yetersiz |

---

## 7. Consequences

### Olumlu
- XSS saldırıları engellenir
- OWASP A03:2021 uyumluluğu
- SPA uyumluluğu

### Olumsuz
- Per-request nonce overhead (~1ms)
- Dynamic script loading karmaşıklığı

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Nonce üretimi | %100 |
| CSP header | %100 |
| Script blocking | %100 |
| XSS prevention | %100 |

---

## 9. OWASP Compliance

| OWASP | Durum |
|-------|-------|
| A03:2021 Injection | ✅ CSP ile engellenir |
| A05:2021 Misconfiguration | ✅ Doğru CSP policy |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |
| **OWASP** | ✅ |

---

*ADR-012: CSP Nonce Strict-Dynamic v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
