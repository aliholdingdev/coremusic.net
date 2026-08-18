---
type: decision
id: "043"
title: "ADR-043: Auth Subdomain Consolidation"
category: "security"
status: "active"
date: "2026-08-04"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, auth, subdomain, consolidation, central-auth, active]
risk-level: "critical"
owasp-top10: ["A01:2021", "A07:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[architecture/l1-security]]"
---

# ADR-043: Auth Subdomain Consolidation

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic'te tüm kimlik doğrulama işlemleri **auth.coremusic.net** merkezi subdomain'i üzerinden yürütülür. Diğer tüm subdomain'ler (music, admin, home, car, studio, pro, media, download) kendi auth sistemini taşımaz, auth.coremusic.net'e güvenir. Session paylaşımı `.coremusic.net` domain'i üzerinden yapılır.

### 1.2 Temel Gerekçe

Dağınık auth sistemleri güvenlik açığı yaratır. Her subdomain'in kendi auth'unu yönetmesi:
- Güvenlik tutarsızlığı
- Session yönetimi karmaşıklığı
- Bakım yükü artırır
- CSRF korumasını zayıflatır

Merkezi auth, tüm bu sorunları çözer.

### 1.3 Beklenen Sonuçlar

- Tek auth noktası: auth.coremusic.net
- Cross-subdomain session paylaşımı
- Tek CSRF token tüm subdomain'lerde geçerli
- Merkezi RBAC yönetimi
- Single sign-on (SSO) benzeri yapı

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-08-04 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem Tanımı

10+ subdomain'in her birinin kendi auth sistemini taşıması:
- Güvenlik açığı yaratır (her biri ayrı attack surface)
- Session yönetimi karmaşıktır
- CSRF koruması zayıflar
- Bakım maliyeti yüksektir

### 3.2 Auth Akış Diyagramı

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

### 3.3 Session Paylaşımı

Cross-domain session transfer **auth_key + /validate-key cURL POST** ile yapılır. Her subdomain kendi session store'unu tutar, cookie domain `.coremusic.net` üzerinden paylaşılır.

```
auth.coremusic.net                    home.coremusic.net
      │                                      │
  [1] login() → auth_key (64-char hex)       │
  [2] return {redirect: ".../?auth_key=X"}   │
      │                                      │
  [3] browser → auth.coremusic.net/?auth_key=X
      │                                      │
  [4] Root handler: auth_key validate        │
      │  → SessionManager::setAuthUser()     │
      │  → header('Location: /home')         │
      │                                      │
  VEYA (alternatif flow):                    │
  [4] browser → home.coremusic.net/auth/callback?auth_key=X
      │                                      │
      │  [5] HomeAuthBridge::validateAndCreateSession()
      │      → cURL POST → auth.coremusic.net/validate-key
      │      → HomeSessionManager::setAuthUser()
      │      → session_write_close()
      │  [6] header('Location: /home')       │
      │                                      │
      └──────────────────────────────────────┘

Cookie Domain: .coremusic.net
├── music.coremusic.net    ← Aynı cookie domain, ayrı session store
├── admin.coremusic.net    ← Aynı cookie domain, ayrı session store
├── home.coremusic.net     ← Aynı cookie domain, ayrı session store
├── car.coremusic.net      ← Aynı cookie domain, ayrı session store
├── studio.coremusic.net   ← Aynı cookie domain, ayrı session store
├── pro.coremusic.net      ← Aynı cookie domain, ayrı session store
├── media.coremusic.net    ← Aynı cookie domain, ayrı session store
├── download.coremusic.net ← Aynı cookie domain, ayrı session store
└── auth.coremusic.net     ← Auth merkezi
```

### 3.3.1 Auth Root Behavior

`auth.coremusic.net` root URL'inde iki davranış vardır:

1. **`?auth_key=XXX` mevcutsa:** auth_key validate edilir → session oluşturulur → `/home`'e redirect
2. **`?auth_key` yoksa:** `/select-gender?client_id=coremusic-web&response_type=session&redirect_uri=...` redirect

### 3.4 İtici Güçler

| # | Güç | Kritiklik |
|---|-----|-----------|
| 1 | Güvenlik tutarlılığı | Kritik |
| 2 | Session yönetimi basitliği | Yüksek |
| 3 | CSRF koruması gücü | Yüksek |
| 4 | Bakım kolaylığı | Orta |

### 3.5 Teknik Kısıtlamalar

| Kısıtlama | Değer |
|-----------|-------|
| Auth domain | auth.coremusic.net |
| Cookie domain | .coremusic.net |
| Session sharing | Cross-subdomain |
| CSRF token | Session-bound |
| RBAC | Centralized |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te tüm auth işlemleri auth.coremusic.net üzerinden yürütülür. Diğer subdomain'ler auth.coremusic.net'e güvenir.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Auth sadece auth.coremusic.net | ✅ Zorunlu |
| 2 | Subdomain'lerde auth yok | ✅ Zorunlu |
| 3 | Session .coremusic.net'de paylaşılır | ✅ Zorunlu |
| 4 | RBAC merkezi yönetilir | ✅ Zorunlu |
| 5 | CSRF token session-bound | ✅ Zorunlu |

### 4.3 RBAC Roller

| Rol | Seviye | Erişim |
|-----|--------|--------|
| **admin** | 1000-1999 | Tam sistem yönetimi |
| **system** | 1900-1999 | Sistem servisleri |
| **studio** | 800-899 | Stüdyo modu |
| **premium** | 700-799 | Yüksek kalite |
| **car** | 500-599 | Araç içi mod |
| **regular** | 100-199 | Temel erişim |
| **guest** | 0 | Sadece genel |

### 4.4 Kod Örnekleri

#### 4.4.1 Cross-Subdomain Auth Client

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Client;

/**
 * Auth Client
 *
 * ADR-043 uyumlu cross-subdomain auth client.
 * auth.coremusic.net üzerinden auth işlemleri.
 */
final class AuthClient
{
    private const AUTH_DOMAIN = 'auth.coremusic.net';
    private const REDIRECT_TIMEOUT = 10;

    /**
     * Auth durumunu kontrol eder.
     */
    public function checkAuth(): ?array
    {
        $authUrl = "https://" . self::AUTH_DOMAIN . "/api/v1/auth/status";

        $response = $this->httpClient->get($authUrl, [
            'cookies' => $this->getCookies(),
            'timeout' => self::REDIRECT_TIMEOUT,
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        return null;
    }

    /**
     * Login sayfasına yönlendirir.
     */
    public function redirectToLogin(string $returnUrl): string
    {
        $params = http_build_query([
            'redirect_uri' => $returnUrl,
            'client' => $_SERVER['HTTP_HOST'] ?? 'unknown',
        ]);

        return "https://" . self::AUTH_DOMAIN . "/login?" . $params;
    }

    /**
     * Logout işlemi yapar.
     */
    public function logout(): void
    {
        $authUrl = "https://" . self::AUTH_DOMAIN . "/api/v1/auth/logout";
        $this->httpClient->post($authUrl, [
            'cookies' => $this->getCookies(),
        ]);
    }
}
```

#### 4.4.2 RBAC Middleware

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

/**
 * Permission Middleware
 *
 * ADR-043 uyumlu RBAC middleware'i.
 * Merkezi auth.coremusic.net'den rol bilgisi.
 */
final class PermissionMiddleware implements MiddlewareInterface
{
    private const ROLE_HIERARCHY = [
        'system' => 1900,
        'admin' => 1000,
        'studio' => 800,
        'premium' => 700,
        'car' => 500,
        'regular' => 100,
        'guest' => 0,
    ];

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $userRole = $request->getAttribute('user_role', 'guest');
        $requiredRole = $this->getRequiredRole($request);

        $userLevel = self::ROLE_HIERARCHY[$userRole] ?? 0;
        $requiredLevel = self::ROLE_HIERARCHY[$requiredRole] ?? 0;

        if ($userLevel < $requiredLevel) {
            return new \GuzzleHttp\Psr7\Response(
                403,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'status' => 'error',
                    'code' => 'INSUFFICIENT_PERMISSION',
                    'message' => 'Yetersiz yetki seviyesi',
                    'required' => $requiredRole,
                    'current' => $userRole,
                ], JSON_THROW_ON_ERROR)
            );
        }

        return $handler->handle($request);
    }

    private function getRequiredRole(ServerRequestInterface $request): string
    {
        // Route attribute'dan gerekli rolü oku
        return $request->getAttribute('required_role', 'regular');
    }
}
```

### 4.5 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `shared/config/auth.php` | auth.coremusic.net URL |
| `shared/config/cookie.php` | `.coremusic.net` domain |
| `shared/config/rbac.php` | Rol hiyerarşisi |

---

## 5. Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     Auth Architecture                             │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  auth.coremusic.net (Merkezi Auth)                        │  │
│  │                                                            │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │  │
│  │  │ Login        │  │ Register     │  │ Logout       │    │  │
│  │  │ /login       │  │ /register    │  │ /logout      │    │  │
│  │  └──────────────┘  └──────────────┘  └──────────────┘    │  │
│  │                                                            │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │  │
│  │  │ Session      │  │ CSRF Token   │  │ RBAC         │    │  │
│  │  │ Management   │  │ Management   │  │ Management   │    │  │
│  │  └──────────────┘  └──────────────┘  └──────────────┘    │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  Subdomain Clients:                                        │  │
│  │                                                            │  │
│  │  music.coremusic.net  ──┐                                  │  │
│  │  admin.coremusic.net  ──┤                                  │  │
│  │  home.coremusic.net   ──┼──► auth.coremusic.net           │  │
│  │  car.coremusic.net    ──┤    (Auth Merkezi)                │  │
│  │  studio.coremusic.net ──┤                                  │  │
│  │  pro.coremusic.net    ──┤                                  │  │
│  │  media.coremusic.net  ──┤                                  │  │
│  │  download.coremusic.net┘                                  │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Her subdomain'de auth | Güvensiz, karmaşık |
| Third-party auth (Firebase) | Harici bağımlılık |
| OAuth2 | Over-engineering |

---

## 7. Consequences

### Olumlu
- Güvenlik tutarlılığı
- Tek auth noktası
- Basit session yönetimi
- CSRF koruması gücü

### Olumsuz
- Single point of failure
- Auth domain bağımlılığı

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| Cross-subdomain auth | E2E |
| Session sharing | E2E |
| RBAC | Unit + Integration |
| CSRF cross-origin | Security |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-043: Auth Subdomain Consolidation v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
