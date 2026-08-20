---
title: "ADR-043: Auth Subdomain Consolidation"
status: active
date: 2026-08-04
tags: [security, auth, subdomain, consolidation, central-auth, active]
---

# ADR-043: Auth Subdomain Consolidation

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic'te tÃ¼m kimlik doÄŸrulama iÅŸlemleri **auth.coremusic.net** merkezi subdomain'i Ã¼zerinden yÃ¼rÃ¼tÃ¼lÃ¼r. DiÄŸer tÃ¼m subdomain'ler (music, admin, home, car, studio, pro, media, download) kendi auth sistemini taÅŸÄ±maz, auth.coremusic.net'e gÃ¼venir. Session paylaÅŸÄ±mÄ± `.coremusic.net` domain'i Ã¼zerinden yapÄ±lÄ±r.

### 1.2 Temel GerekÃ§e

DaÄŸÄ±nÄ±k auth sistemleri gÃ¼venlik aÃ§Ä±ÄŸÄ± yaratÄ±r. Her subdomain'in kendi auth'unu yÃ¶netmesi:
- GÃ¼venlik tutarsÄ±zlÄ±ÄŸÄ±
- Session yÃ¶netimi karmaÅŸÄ±klÄ±ÄŸÄ±
- BakÄ±m yÃ¼kÃ¼ artÄ±rÄ±r
- CSRF korumasÄ±nÄ± zayÄ±flatÄ±r

Merkezi auth, tÃ¼m bu sorunlarÄ± Ã§Ã¶zer.

### 1.3 Beklenen SonuÃ§lar

- Tek auth noktasÄ±: auth.coremusic.net
- Cross-subdomain session paylaÅŸÄ±mÄ±
- Tek CSRF token tÃ¼m subdomain'lerde geÃ§erli
- Merkezi RBAC yÃ¶netimi
- Single sign-on (SSO) benzeri yapÄ±

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-08-04 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

10+ subdomain'in her birinin kendi auth sistemini taÅŸÄ±masÄ±:
- GÃ¼venlik aÃ§Ä±ÄŸÄ± yaratÄ±r (her biri ayrÄ± attack surface)
- Session yÃ¶netimi karmaÅŸÄ±ktÄ±r
- CSRF korumasÄ± zayÄ±flar
- BakÄ±m maliyeti yÃ¼ksektir

### 3.2 Auth AkÄ±ÅŸ DiyagramÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ music.       â”‚â”€â”€â”€â”€â–ºâ”‚ auth.coremusic.  â”‚â”€â”€â”€â”€â–ºâ”‚ music.coremusicâ”‚
â”‚ coremusic.netâ”‚     â”‚ net (auth)       â”‚     â”‚ .net (return)  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
     â”‚                      â”‚                         â”‚
     â”‚  redirect_uri        â”‚  login/register         â”‚
     â”‚  parameter           â”‚  session creation       â”‚
     â”‚                      â”‚  auth_key generation    â”‚
     â”‚                      â”‚                         â”‚
     â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚
     â”‚                      â”‚  auth_key (64-char hex) â”‚
```

### 3.3 Session PaylaÅŸÄ±mÄ±

Cross-domain session transfer **auth_key + /validate-key cURL POST** ile yapÄ±lÄ±r. Her subdomain kendi session store'unu tutar, cookie domain `.coremusic.net` Ã¼zerinden paylaÅŸÄ±lÄ±r.

```
auth.coremusic.net                    home.coremusic.net
      â”‚                                      â”‚
  [1] login() â†’ auth_key (64-char hex)       â”‚
  [2] return {redirect: ".../?auth_key=X"}   â”‚
      â”‚                                      â”‚
  [3] browser â†’ auth.coremusic.net/?auth_key=X
      â”‚                                      â”‚
  [4] Root handler: auth_key validate        â”‚
      â”‚  â†’ SessionManager::setAuthUser()     â”‚
      â”‚  â†’ header('Location: /home')         â”‚
      â”‚                                      â”‚
  VEYA (alternatif flow):                    â”‚
  [4] browser â†’ home.coremusic.net/auth/callback?auth_key=X
      â”‚                                      â”‚
      â”‚  [5] HomeAuthBridge::validateAndCreateSession()
      â”‚      â†’ cURL POST â†’ auth.coremusic.net/validate-key
      â”‚      â†’ HomeSessionManager::setAuthUser()
      â”‚      â†’ session_write_close()
      â”‚  [6] header('Location: /home')       â”‚
      â”‚                                      â”‚
      â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜

Cookie Domain: .coremusic.net
â”œâ”€â”€ music.coremusic.net    â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ admin.coremusic.net    â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ home.coremusic.net     â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ car.coremusic.net      â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ studio.coremusic.net   â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ pro.coremusic.net      â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ media.coremusic.net    â† AynÄ± cookie domain, ayrÄ± session store
â”œâ”€â”€ download.coremusic.net â† AynÄ± cookie domain, ayrÄ± session store
â””â”€â”€ auth.coremusic.net     â† Auth merkezi
```

### 3.3.1 Auth Root Behavior

`auth.coremusic.net` root URL'inde iki davranÄ±ÅŸ vardÄ±r:

1. **`?auth_key=XXX` mevcutsa:** auth_key validate edilir â†’ session oluÅŸturulur â†’ `/home`'e redirect
2. **`?auth_key` yoksa:** `/select-gender?client_id=coremusic-web&response_type=session&redirect_uri=...` redirect

### 3.4 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | Kritiklik |
|---|-----|-----------|
| 1 | GÃ¼venlik tutarlÄ±lÄ±ÄŸÄ± | Kritik |
| 2 | Session yÃ¶netimi basitliÄŸi | YÃ¼ksek |
| 3 | CSRF korumasÄ± gÃ¼cÃ¼ | YÃ¼ksek |
| 4 | BakÄ±m kolaylÄ±ÄŸÄ± | Orta |

### 3.5 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | DeÄŸer |
|-----------|-------|
| Auth domain | auth.coremusic.net |
| Cookie domain | .coremusic.net |
| Session sharing | Cross-subdomain |
| CSRF token | Session-bound |
| RBAC | Centralized |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic'te tÃ¼m auth iÅŸlemleri auth.coremusic.net Ã¼zerinden yÃ¼rÃ¼tÃ¼lÃ¼r. DiÄŸer subdomain'ler auth.coremusic.net'e gÃ¼venir.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Auth sadece auth.coremusic.net | âœ… Zorunlu |
| 2 | Subdomain'lerde auth yok | âœ… Zorunlu |
| 3 | Session .coremusic.net'de paylaÅŸÄ±lÄ±r | âœ… Zorunlu |
| 4 | RBAC merkezi yÃ¶netilir | âœ… Zorunlu |
| 5 | CSRF token session-bound | âœ… Zorunlu |

### 4.3 RBAC Roller

| Rol | Seviye | EriÅŸim |
|-----|--------|--------|
| **admin** | 1000-1999 | Tam sistem yÃ¶netimi |
| **system** | 1900-1999 | Sistem servisleri |
| **studio** | 800-899 | StÃ¼dyo modu |
| **premium** | 700-799 | YÃ¼ksek kalite |
| **car** | 500-599 | AraÃ§ iÃ§i mod |
| **regular** | 100-199 | Temel eriÅŸim |
| **guest** | 0 | Sadece genel |

### 4.4 Kod Ã–rnekleri

#### 4.4.1 Cross-Subdomain Auth Client

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Client;

/**
 * Auth Client
 *
 * ADR-043 uyumlu cross-subdomain auth client.
 * auth.coremusic.net Ã¼zerinden auth iÅŸlemleri.
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
     * Login sayfasÄ±na yÃ¶nlendirir.
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
     * Logout iÅŸlemi yapar.
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
        // Route attribute'dan gerekli rolÃ¼ oku
        return $request->getAttribute('required_role', 'regular');
    }
}
```

### 4.5 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `shared/config/auth.php` | auth.coremusic.net URL |
| `shared/config/cookie.php` | `.coremusic.net` domain |
| `shared/config/rbac.php` | Rol hiyerarÅŸisi |

---

## 5. Architecture

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                     Auth Architecture                             â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  auth.coremusic.net (Merkezi Auth)                        â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ Login        â”‚  â”‚ Register     â”‚  â”‚ Logout       â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ /login       â”‚  â”‚ /register    â”‚  â”‚ /logout      â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ Session      â”‚  â”‚ CSRF Token   â”‚  â”‚ RBAC         â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ Management   â”‚  â”‚ Management   â”‚  â”‚ Management   â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Subdomain Clients:                                        â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  music.coremusic.net  â”€â”€â”                                  â”‚  â”‚
â”‚  â”‚  admin.coremusic.net  â”€â”€â”¤                                  â”‚  â”‚
â”‚  â”‚  home.coremusic.net   â”€â”€â”¼â”€â”€â–º auth.coremusic.net           â”‚  â”‚
â”‚  â”‚  car.coremusic.net    â”€â”€â”¤    (Auth Merkezi)                â”‚  â”‚
â”‚  â”‚  studio.coremusic.net â”€â”€â”¤                                  â”‚  â”‚
â”‚  â”‚  pro.coremusic.net    â”€â”€â”¤                                  â”‚  â”‚
â”‚  â”‚  media.coremusic.net  â”€â”€â”¤                                  â”‚  â”‚
â”‚  â”‚  download.coremusic.netâ”˜                                  â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Her subdomain'de auth | GÃ¼vensiz, karmaÅŸÄ±k |
| Third-party auth (Firebase) | Harici baÄŸÄ±mlÄ±lÄ±k |
| OAuth2 | Over-engineering |

---

## 7. Consequences

### Olumlu
- GÃ¼venlik tutarlÄ±lÄ±ÄŸÄ±
- Tek auth noktasÄ±
- Basit session yÃ¶netimi
- CSRF korumasÄ± gÃ¼cÃ¼

### Olumsuz
- Single point of failure
- Auth domain baÄŸÄ±mlÄ±lÄ±ÄŸÄ±

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

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-043: Auth Subdomain Consolidation v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*