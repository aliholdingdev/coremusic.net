---
title: "ADR-013: Rate Limiting APCu"
status: frozen
date: 2026-01-20
tags: [security, rate-limit, apcu, ddos, brute-force, owasp, frozen]
---

# ADR-013: Rate Limiting APCu

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic platformunda rate limiting, **APCu tabanlÄ±** bir strateji ile uygulanacaktÄ±r. VarsayÄ±lan limit: **60 istek/60 saniye** (1 istek/saniye). Rate limit IP bazlÄ± uygulanÄ±r. AÅŸÄ±m durumunda **429 Too Many Requests** yanÄ±tÄ± dÃ¶ner. Auth endpoint'leri iÃ§in daha sÄ±kÄ± limit: **10 istek/60 saniye**.

### 1.2 Temel GerekÃ§e

Rate limiting, brute-force saldÄ±rÄ±larÄ±nÄ±, DDoS saldÄ±rÄ±larÄ±nÄ± ve API kÃ¶tÃ¼ye kullanÄ±mlarÄ±nÄ± engeller. APCu, PHP'nin dahili Ã¶nbellek sistemidir ve harici baÄŸÄ±mlÄ±lÄ±k gerektirmez. Bu, CoreMusic'in minimalist altyapÄ± felsefesiyle uyumludur.

### 1.3 Beklenen SonuÃ§lar

- Brute-force saldÄ±rÄ±larÄ± engellenir
- DDoS saldÄ±rÄ±larÄ± azaltÄ±lÄ±r
- API kÃ¶tÃ¼ye kullanÄ±mÄ± Ã¶nlenir
- Auth endpoint'leri ek koruma altÄ±ndadÄ±r
- Harici baÄŸÄ±mlÄ±lÄ±k yok (sadece APCu)

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-20 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | high |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

Rate limiting olmadan, saldÄ±rganlar sÄ±nÄ±rsÄ±z sayÄ±da istek gÃ¶ndererek:
- Brute-force ile ÅŸifre kÄ±rmaya Ã§alÄ±ÅŸabilir
- DDoS ile sistemi Ã§Ã¶kertebilir
- API'yi aÅŸÄ±rÄ± kullanarak performans dÃ¼ÅŸÃ¼klÃ¼ÄŸÃ¼ yaratabilir
- Enumeration ile hassas bilgi toplayabilir

### 3.2 Rate Limit YapÄ±sÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚               Rate Limiting System               â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Global Limit: 60 req/60s per IP          â”‚  â”‚
â”‚  â”‚  Auth Limit: 10 req/60s per IP            â”‚  â”‚
â”‚  â”‚  API Limit: 120 req/60s per API key       â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  APCu Storage                             â”‚  â”‚
â”‚  â”‚  Key: rate_limit:{ip}:{endpoint}          â”‚  â”‚
â”‚  â”‚  Value: request_count                     â”‚  â”‚
â”‚  â”‚  TTL: 60 seconds                          â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Response Headers                         â”‚  â”‚
â”‚  â”‚  X-RateLimit-Limit: 60                    â”‚  â”‚
â”‚  â”‚  X-RateLimit-Remaining: 45                â”‚  â”‚
â”‚  â”‚  X-RateLimit-Reset: 1692000000            â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 3.3 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | Kritiklik |
|---|-----|-----------|
| 1 | Brute-force saldÄ±rÄ±larÄ± | Kritik |
| 2 | DDoS saldÄ±rÄ±larÄ± | YÃ¼ksek |
| 3 | API kÃ¶tÃ¼ye kullanÄ±mÄ± | YÃ¼ksek |
| 4 | Enumeration saldÄ±rÄ±larÄ± | Orta |

### 3.4 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | DeÄŸer |
|-----------|-------|
| Driver | APCu (PHP extension) |
| Global limit | 60 req/60s |
| Auth limit | 10 req/60s |
| API limit | 120 req/60s |
| Storage | Key-value (APCu) |
| TTL | 60 saniye |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, APCu tabanlÄ± IP bazlÄ± rate limiting kullanÄ±r. VarsayÄ±lan limit 60/60s, auth iÃ§in 10/60s.**

### 4.2 Kesin Kurallar

| # | Kural | DeÄŸer |
|---|-------|-------|
| 1 | Global limit | 60 req/60s |
| 2 | Auth limit | 10 req/60s |
| 3 | API limit | 120 req/60s |
| 4 | AÅŸÄ±m yanÄ±tÄ± | 429 Too Many Requests |
| 5 | Response header | X-RateLimit-* |
| 6 | IP bazlÄ± | Client IP |
| 7 | APCu driver | Zorunlu |

### 4.3 Kod Ã–rnekleri

#### 4.3.1 Rate Limiter Service

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * Rate Limiter Service
 *
 * ADR-013 uyumlu APCu tabanlÄ± rate limiting.
 * IP bazlÄ±, 60 req/60s varsayÄ±lan.
 * Auth endpoint'leri iÃ§in 10 req/60s.
 */
final class RateLimiterService
{
    private const DEFAULT_LIMIT = 60;
    private const AUTH_LIMIT = 10;
    private const API_LIMIT = 120;
    private const WINDOW = 60; // saniye

    /**
     * Ä°steÄŸin rate limit'e uygun olup olmadÄ±ÄŸÄ±nÄ± kontrol eder.
     */
    public function isAllowed(
        string $ip,
        string $endpoint = 'global',
        ?int $limit = null
    ): bool {
        $effectiveLimit = $limit ?? $this->getLimitForEndpoint($endpoint);
        $key = "rate_limit:{$ip}:{$endpoint}";

        $currentCount = apcu_fetch($key);
        if ($currentCount === false) {
            $currentCount = 0;
        }

        if ($currentCount >= $effectiveLimit) {
            return false;
        }

        apcu_store($key, $currentCount + 1, self::WINDOW);
        return true;
    }

    /**
     * Endpoint'e gÃ¶re limit dÃ¶ndÃ¼rÃ¼r.
     */
    private function getLimitForEndpoint(string $endpoint): int
    {
        return match ($endpoint) {
            'login', 'register', 'forgot-password' => self::AUTH_LIMIT,
            'api' => self::API_LIMIT,
            default => self::DEFAULT_LIMIT,
        };
    }

    /**
     * Kalan istek sayÄ±sÄ±nÄ± dÃ¶ndÃ¼rÃ¼r.
     */
    public function getRemaining(
        string $ip,
        string $endpoint = 'global'
    ): int {
        $limit = $this->getLimitForEndpoint($endpoint);
        $key = "rate_limit:{$ip}:{$endpoint}";
        $currentCount = apcu_fetch($key) ?: 0;

        return max(0, $limit - $currentCount);
    }

    /**
     * Rate limit header'larÄ±nÄ± oluÅŸturur.
     */
    public function getHeaders(
        string $ip,
        string $endpoint = 'global'
    ): array {
        $limit = $this->getLimitForEndpoint($endpoint);
        $remaining = $this->getRemaining($ip, $endpoint);
        $reset = time() + self::WINDOW;

        return [
            'X-RateLimit-Limit' => (string) $limit,
            'X-RateLimit-Remaining' => (string) $remaining,
            'X-RateLimit-Reset' => (string) $reset,
        ];
    }
}
```

#### 4.3.2 Rate Limiter Middleware

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use CoreMusic\Security\Service\RateLimiterService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Rate Limiter Middleware
 *
 * ADR-013 uyumlu APCu tabanlÄ± rate limiting.
 * IP bazlÄ±, 60 req/60s varsayÄ±lan.
 */
final class RateLimiterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RateLimiterService $rateLimiter,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $endpoint = $this->getEndpointCategory($request);

        if (!$this->rateLimiter->isAllowed($ip, $endpoint)) {
            $headers = $this->rateLimiter->getHeaders($ip, $endpoint);

            $response = new \GuzzleHttp\Psr7\Response(
                429,
                array_merge($headers, [
                    'Content-Type' => 'application/json',
                    'Retry-After' => '60',
                ]),
                json_encode([
                    'status' => 'error',
                    'code' => 'RATE_LIMIT_EXCEEDED',
                    'message' => 'Ã‡ok fazla istek. LÃ¼tfen 60 saniye bekleyin.',
                    'retry_after' => 60,
                ], JSON_THROW_ON_ERROR)
            );

            return $response;
        }

        $response = $handler->handle($request);

        // Rate limit header'larÄ±nÄ± ekle
        $headers = $this->rateLimiter->getHeaders($ip, $endpoint);
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    private function getEndpointCategory(ServerRequestInterface $request): string
    {
        $path = $request->getUri()->getPath();

        if (preg_match('#^/auth/(login|register|forgot-password)#', $path)) {
            return 'auth';
        }

        if (str_starts_with($path, '/api/')) {
            return 'api';
        }

        return 'global';
    }
}
```

### 4.4 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `shared/config/rate-limit.php` | 60/60s global, 10/60s auth |
| `php.ini` | `apc.enabled=1` |

---

## 5. Architecture

### 5.1 Rate Limit AkÄ±ÅŸÄ±

```
Request â†’ IP Extract â†’ APCu Key â†’ Count Check â†’ Allow/Deny
                                              â”‚
                                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
                                    â”‚                   â”‚
                                 Allowed            Denied
                                    â”‚                   â”‚
                              Controller          429 Response
                                    â”‚                   â”‚
                              + Headers           + Retry-After
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Redis rate limiting | Harici baÄŸÄ±mlÄ±lÄ±k (ADR-013 APCu) |
| Database rate limiting | Performans, DB yÃ¼kÃ¼ |
| Token bucket | KarmaÅŸÄ±k, APCu yeterli |
| Sliding window | APCu ile basit |

---

## 7. Consequences

### Olumlu
- Brute-force saldÄ±rÄ±larÄ± engellenir
- DDoS korumasÄ±
- Harici baÄŸÄ±mlÄ±lÄ±k yok

### Olumsuz
- APCu baÄŸÄ±mlÄ±lÄ±ÄŸÄ±
- Single-server (distributed yok)
- Memory kullanÄ±mÄ±

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Limit aÅŸÄ±mÄ± | %100 |
| Auth limit | %100 |
| Header doÄŸrulama | %100 |
| 429 yanÄ±tÄ± | %100 |

---

## 9. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-013: Rate Limiting APCu v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*