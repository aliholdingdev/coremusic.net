---
title: "ADR-020: API Public Security"
status: frozen
date: 2026-02-20
tags: [security, api, public, jwt, rate-limit, frozen]
---

# ADR-020: API Public Security

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic API gÃ¼venlik stratejisi, **multi-layer** koruma ile uygulanÄ±r: API key authentication, rate limiting, input validation ve CORS policy. Public API endpoint'leri iÃ§in API key zorunludur. Internal API endpoint'leri iÃ§in session-based auth kullanÄ±lÄ±r.

### 1.2 Temel GerekÃ§e

API'ler, sistemin dÄ±ÅŸa aÃ§Ä±lan kapÄ±sÄ±dÄ±r. ZayÄ±f API gÃ¼venliÄŸi, veri sÄ±zÄ±ntÄ±sÄ± ve yetkisiz eriÅŸim saldÄ±rÄ±larÄ±na yol aÃ§ar. CoreMusic'in multi-service yapÄ±sÄ±nda API gÃ¼venliÄŸi kritik Ã¶nem taÅŸÄ±r.

### 1.3 Beklenen SonuÃ§lar

- Public API iÃ§in API key authentication
- Rate limiting tÃ¼m API endpoint'lerinde
- Input validation her istekte
- CORS policy whitelist tabanlÄ±
- SSRF korumasÄ±

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-02-20 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | high |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

API saldÄ±rÄ±larÄ±:
- Unauthorized access (yetkisiz eriÅŸim)
- Data leakage (veri sÄ±zÄ±ntÄ±sÄ±)
- Abuse (kÃ¶tÃ¼ye kullanÄ±m)
- SSRF (Server-Side Request Forgery)

### 3.2 API GÃ¼venlik KatmanlarÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              API Security Layers                  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 1: Origin Check                     â”‚  â”‚
â”‚  â”‚  â€¢ Whitelist tabanlÄ± CORS                  â”‚  â”‚
â”‚  â”‚  â€¢ Sadece izin verilen domain'ler          â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 2: Rate Limiting (ADR-013)         â”‚  â”‚
â”‚  â”‚  â€¢ 60 req/60s global                       â”‚  â”‚
â”‚  â”‚  â€¢ 120 req/60s API key bazlÄ±              â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 3: Authentication                   â”‚  â”‚
â”‚  â”‚  â€¢ API key (public)                        â”‚  â”‚
â”‚  â”‚  â€¢ JWT/Session (internal)                  â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 4: Input Validation                 â”‚  â”‚
â”‚  â”‚  â€¢ Request schema validation               â”‚  â”‚
â”‚  â”‚  â€¢ SQL injection prevention                â”‚  â”‚
â”‚  â”‚  â€¢ XSS prevention                          â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 5: SSRF Protection                  â”‚  â”‚
â”‚  â”‚  â€¢ URL validation                          â”‚  â”‚
â”‚  â”‚  â€¢ Private IP blocking                     â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic API'leri multi-layer gÃ¼venlik ile korunur: Origin Check â†’ Rate Limit â†’ Auth â†’ Validation â†’ SSRF Protection.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | API key authentication | âœ… Zorunlu (public) |
| 2 | Session-based auth | âœ… Zorunlu (internal) |
| 3 | Rate limiting | âœ… Zorunlu |
| 4 | Input validation | âœ… Zorunlu |
| 5 | CORS whitelist | âœ… Zorunlu |
| 6 | SSRF protection | âœ… Zorunlu |
| 7 | HTTPS only | âœ… Zorunlu |
| 8 | API versioning | âœ… Zorunlu |

### 4.3 Kod Ã–rnekleri

#### 4.3.1 API Key Authentication

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

/**
 * API Key Authentication Middleware
 *
 * ADR-020 uyumlu API key authentication.
 * Public API iÃ§in zorunlu.
 */
final class ApiKeyMiddleware implements MiddlewareInterface
{
    private const API_KEY_HEADER = 'X-API-Key';
    private const API_KEY_QUERY = 'api_key';

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $apiKey = $this->extractApiKey($request);

        if ($apiKey === null) {
            return $this->createErrorResponse(401, 'API_KEY_MISSING');
        }

        if (!$this->isValidApiKey($apiKey)) {
            return $this->createErrorResponse(401, 'API_KEY_INVALID');
        }

        $request = $request->withAttribute('api_key_valid', true);
        return $handler->handle($request);
    }

    private function extractApiKey(ServerRequestInterface $request): ?string
    {
        // Header'dan oku
        $headerKey = $request->getHeaderLine(self::API_KEY_HEADER);
        if (!empty($headerKey)) {
            return $headerKey;
        }

        // Query'den oku
        $queryParams = $request->getQueryParams();
        return $queryParams[self::API_KEY_QUERY] ?? null;
    }

    private function isValidApiKey(string $apiKey): bool
    {
        // DB'den API key doÄŸrulama
        $db = DatabaseConnection::getInstance();
        $stmt = $db->prepare(
            'SELECT id FROM api_keys WHERE key_hash = :hash AND is_active = 1'
        );
        $stmt->execute(['hash' => hash('sha256', $apiKey)]);
        return $stmt->fetch() !== false;
    }
}
```

#### 4.3.2 SSRF Protection

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * SSRF Protection Service
 *
 * ADR-020 uyumlu SSRF korumasÄ±.
 * Private IP aralÄ±klarÄ±nÄ± engeller.
 */
final class SsrfProtectionService
{
    private const PRIVATE_RANGES = [
        '127.0.0.0/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '169.254.0.0/16',
        '::1/128',
        'fc00::/7',
        'fe80::/10',
    ];

    /**
     * URL'nin gÃ¼venli olup olmadÄ±ÄŸÄ±nÄ± kontrol eder.
     */
    public function isUrlSafe(string $url): bool
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        // Sadece http/https izin ver
        $scheme = $parsed['scheme'] ?? '';
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        // Private IP kontrolÃ¼
        $host = $parsed['host'] ?? '';
        $ip = gethostbyname($host);

        foreach (self::PRIVATE_RANGES as $range) {
            if ($this->ipInRange($ip, $range)) {
                return false;
            }
        }

        return true;
    }

    private function ipInRange(string $ip, string $range): bool
    {
        // CIDR notation kontrolÃ¼
        [$subnet, $mask] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
```

### 4.4 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `shared/config/cors.php` | Whitelist origins |
| `shared/config/api.php` | API key settings |

---

## 5. Architecture

```
Client â†’ Origin Check â†’ Rate Limit â†’ API Key Auth â†’ Validation â†’ Controller
                                    â”‚
                                    â”œâ”€â”€â–º Valid â†’ Continue
                                    â””â”€â”€â–º Invalid â†’ 401
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| OAuth2 only | KarmaÅŸÄ±k, API key yeterli |
| IP whitelist | Dynamic IP'ler iÃ§in uygun deÄŸil |
| No auth | GÃ¼vensiz |

---

## 7. Consequences

### Olumlu
- API gÃ¼venliÄŸi saÄŸlanÄ±r
- OWASP uyumluluÄŸu
- Multi-layer koruma

### Olumsuz
- API key yÃ¶netimi karmaÅŸÄ±k
- Rate limit overhead

---

## 8. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-020: API Public Security v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*