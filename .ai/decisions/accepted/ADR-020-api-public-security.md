---
type: decision
id: "020"
title: "ADR-020: API Public Security"
category: "security"
status: "frozen"
date: "2026-02-20"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, api, public, jwt, rate-limit, frozen]
risk-level: "high"
owasp-top10: ["A01:2021", "A07:2021", "A10:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-013-rate-limiting-apcu]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
  - "[[architecture/l1-security]]"
---

# ADR-020: API Public Security

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic API güvenlik stratejisi, **multi-layer** koruma ile uygulanır: API key authentication, rate limiting, input validation ve CORS policy. Public API endpoint'leri için API key zorunludur. Internal API endpoint'leri için session-based auth kullanılır.

### 1.2 Temel Gerekçe

API'ler, sistemin dışa açılan kapısıdır. Zayıf API güvenliği, veri sızıntısı ve yetkisiz erişim saldırılarına yol açar. CoreMusic'in multi-service yapısında API güvenliği kritik önem taşır.

### 1.3 Beklenen Sonuçlar

- Public API için API key authentication
- Rate limiting tüm API endpoint'lerinde
- Input validation her istekte
- CORS policy whitelist tabanlı
- SSRF koruması

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-02-20 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | high |

---

## 3. Context

### 3.1 Problem Tanımı

API saldırıları:
- Unauthorized access (yetkisiz erişim)
- Data leakage (veri sızıntısı)
- Abuse (kötüye kullanım)
- SSRF (Server-Side Request Forgery)

### 3.2 API Güvenlik Katmanları

```
┌─────────────────────────────────────────────────┐
│              API Security Layers                  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 1: Origin Check                     │  │
│  │  • Whitelist tabanlı CORS                  │  │
│  │  • Sadece izin verilen domain'ler          │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 2: Rate Limiting (ADR-013)         │  │
│  │  • 60 req/60s global                       │  │
│  │  • 120 req/60s API key bazlı              │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 3: Authentication                   │  │
│  │  • API key (public)                        │  │
│  │  • JWT/Session (internal)                  │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 4: Input Validation                 │  │
│  │  • Request schema validation               │  │
│  │  • SQL injection prevention                │  │
│  │  • XSS prevention                          │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 5: SSRF Protection                  │  │
│  │  • URL validation                          │  │
│  │  • Private IP blocking                     │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic API'leri multi-layer güvenlik ile korunur: Origin Check → Rate Limit → Auth → Validation → SSRF Protection.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | API key authentication | ✅ Zorunlu (public) |
| 2 | Session-based auth | ✅ Zorunlu (internal) |
| 3 | Rate limiting | ✅ Zorunlu |
| 4 | Input validation | ✅ Zorunlu |
| 5 | CORS whitelist | ✅ Zorunlu |
| 6 | SSRF protection | ✅ Zorunlu |
| 7 | HTTPS only | ✅ Zorunlu |
| 8 | API versioning | ✅ Zorunlu |

### 4.3 Kod Örnekleri

#### 4.3.1 API Key Authentication

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

/**
 * API Key Authentication Middleware
 *
 * ADR-020 uyumlu API key authentication.
 * Public API için zorunlu.
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
        // DB'den API key doğrulama
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
 * ADR-020 uyumlu SSRF koruması.
 * Private IP aralıklarını engeller.
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
     * URL'nin güvenli olup olmadığını kontrol eder.
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

        // Private IP kontrolü
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
        // CIDR notation kontrolü
        [$subnet, $mask] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
```

### 4.4 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `shared/config/cors.php` | Whitelist origins |
| `shared/config/api.php` | API key settings |

---

## 5. Architecture

```
Client → Origin Check → Rate Limit → API Key Auth → Validation → Controller
                                    │
                                    ├──► Valid → Continue
                                    └──► Invalid → 401
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| OAuth2 only | Karmaşık, API key yeterli |
| IP whitelist | Dynamic IP'ler için uygun değil |
| No auth | Güvensiz |

---

## 7. Consequences

### Olumlu
- API güvenliği sağlanır
- OWASP uyumluluğu
- Multi-layer koruma

### Olumsuz
- API key yönetimi karmaşık
- Rate limit overhead

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-020: API Public Security v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
