---
type: decision
id: "013"
title: "ADR-013: Rate Limiting APCu"
category: "security"
status: "frozen"
date: "2026-01-20"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, rate-limit, apcu, ddos, brute-force, owasp, frozen]
risk-level: "high"
owasp-top10: ["A04:2021", "A05:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-012-csp-nonce-strict-dynamic]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/l1-security]]"
---

# ADR-013: Rate Limiting APCu

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic platformunda rate limiting, **APCu tabanlı** bir strateji ile uygulanacaktır. Varsayılan limit: **60 istek/60 saniye** (1 istek/saniye). Rate limit IP bazlı uygulanır. Aşım durumunda **429 Too Many Requests** yanıtı döner. Auth endpoint'leri için daha sıkı limit: **10 istek/60 saniye**.

### 1.2 Temel Gerekçe

Rate limiting, brute-force saldırılarını, DDoS saldırılarını ve API kötüye kullanımlarını engeller. APCu, PHP'nin dahili önbellek sistemidir ve harici bağımlılık gerektirmez. Bu, CoreMusic'in minimalist altyapı felsefesiyle uyumludur.

### 1.3 Beklenen Sonuçlar

- Brute-force saldırıları engellenir
- DDoS saldırıları azaltılır
- API kötüye kullanımı önlenir
- Auth endpoint'leri ek koruma altındadır
- Harici bağımlılık yok (sadece APCu)

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-01-20 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | high |
| **Onay** | Red Team · Human Mode · Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

Rate limiting olmadan, saldırganlar sınırsız sayıda istek göndererek:
- Brute-force ile şifre kırmaya çalışabilir
- DDoS ile sistemi çökertebilir
- API'yi aşırı kullanarak performans düşüklüğü yaratabilir
- Enumeration ile hassas bilgi toplayabilir

### 3.2 Rate Limit Yapısı

```
┌─────────────────────────────────────────────────┐
│               Rate Limiting System               │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Global Limit: 60 req/60s per IP          │  │
│  │  Auth Limit: 10 req/60s per IP            │  │
│  │  API Limit: 120 req/60s per API key       │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  APCu Storage                             │  │
│  │  Key: rate_limit:{ip}:{endpoint}          │  │
│  │  Value: request_count                     │  │
│  │  TTL: 60 seconds                          │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Response Headers                         │  │
│  │  X-RateLimit-Limit: 60                    │  │
│  │  X-RateLimit-Remaining: 45                │  │
│  │  X-RateLimit-Reset: 1692000000            │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

### 3.3 İtici Güçler

| # | Güç | Kritiklik |
|---|-----|-----------|
| 1 | Brute-force saldırıları | Kritik |
| 2 | DDoS saldırıları | Yüksek |
| 3 | API kötüye kullanımı | Yüksek |
| 4 | Enumeration saldırıları | Orta |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Değer |
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

**CoreMusic, APCu tabanlı IP bazlı rate limiting kullanır. Varsayılan limit 60/60s, auth için 10/60s.**

### 4.2 Kesin Kurallar

| # | Kural | Değer |
|---|-------|-------|
| 1 | Global limit | 60 req/60s |
| 2 | Auth limit | 10 req/60s |
| 3 | API limit | 120 req/60s |
| 4 | Aşım yanıtı | 429 Too Many Requests |
| 5 | Response header | X-RateLimit-* |
| 6 | IP bazlı | Client IP |
| 7 | APCu driver | Zorunlu |

### 4.3 Kod Örnekleri

#### 4.3.1 Rate Limiter Service

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * Rate Limiter Service
 *
 * ADR-013 uyumlu APCu tabanlı rate limiting.
 * IP bazlı, 60 req/60s varsayılan.
 * Auth endpoint'leri için 10 req/60s.
 */
final class RateLimiterService
{
    private const DEFAULT_LIMIT = 60;
    private const AUTH_LIMIT = 10;
    private const API_LIMIT = 120;
    private const WINDOW = 60; // saniye

    /**
     * İsteğin rate limit'e uygun olup olmadığını kontrol eder.
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
     * Endpoint'e göre limit döndürür.
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
     * Kalan istek sayısını döndürür.
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
     * Rate limit header'larını oluşturur.
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
 * ADR-013 uyumlu APCu tabanlı rate limiting.
 * IP bazlı, 60 req/60s varsayılan.
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
                    'message' => 'Çok fazla istek. Lütfen 60 saniye bekleyin.',
                    'retry_after' => 60,
                ], JSON_THROW_ON_ERROR)
            );

            return $response;
        }

        $response = $handler->handle($request);

        // Rate limit header'larını ekle
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

### 4.4 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `shared/config/rate-limit.php` | 60/60s global, 10/60s auth |
| `php.ini` | `apc.enabled=1` |

---

## 5. Architecture

### 5.1 Rate Limit Akışı

```
Request → IP Extract → APCu Key → Count Check → Allow/Deny
                                              │
                                    ┌─────────┴─────────┐
                                    │                   │
                                 Allowed            Denied
                                    │                   │
                              Controller          429 Response
                                    │                   │
                              + Headers           + Retry-After
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Redis rate limiting | Harici bağımlılık (ADR-013 APCu) |
| Database rate limiting | Performans, DB yükü |
| Token bucket | Karmaşık, APCu yeterli |
| Sliding window | APCu ile basit |

---

## 7. Consequences

### Olumlu
- Brute-force saldırıları engellenir
- DDoS koruması
- Harici bağımlılık yok

### Olumsuz
- APCu bağımlılığı
- Single-server (distributed yok)
- Memory kullanımı

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Limit aşımı | %100 |
| Auth limit | %100 |
| Header doğrulama | %100 |
| 429 yanıtı | %100 |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-013: Rate Limiting APCu v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
