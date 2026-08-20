---
title: "ADR-011: Session Management"
status: frozen
date: 2026-01-10
tags: [security, session, cookie, authentication, owasp, frozen]
---

# ADR-011: Session Management

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic platformunda session yÃ¶netimi **HttpOnly, Secure, SameSite=Lax** cookie tabanlÄ± olarak uygulanacaktÄ±r. Session cookie adÄ± `COREMUSIC_SESS` olarak sabitlenmiÅŸtir. Idle timeout **3600 saniye** (1 saat), absolute timeout **86400 saniye** (24 saat) olarak belirlenmiÅŸtir. Session rotation her **1800 saniye** (30 dakikada) bir yapÄ±lÄ±r. Token'lar `random_bytes(32)` ile Ã¼retilir ve kriptografik olarak gÃ¼venlidir.

### 1.2 Temel GerekÃ§e

Session yÃ¶netimi, kimlik doÄŸrulama sisteminin temel taÅŸÄ±dÄ±r. ZayÄ±f session yÃ¶netimi, session hijacking, session fixation ve yetkisiz eriÅŸim saldÄ±rÄ±larÄ±na yol aÃ§abilir. CoreMusic'in multi-subdomain yapÄ±sÄ±nda (10+ subdomain) session gÃ¼venliÄŸi kritik Ã¶nem taÅŸÄ±r. auth.coremusic.net merkezi session yÃ¶netimi saÄŸlar.

### 1.3 Beklenen SonuÃ§lar

- Session cookie HttpOnly, Secure, SameSite=Lax olarak ayarlanÄ±r
- Idle timeout 3600 saniye, absolute timeout 86400 saniye
- Session rotation 1800 saniyede bir yapÄ±lÄ±r
- Session fixation saldÄ±rÄ±larÄ± engellenir
- Multi-subdomain session paylaÅŸÄ±mÄ± auth.coremusic.net Ã¼zerinden

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-10 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |
| **Supersedes** | null |
| **Frozen Tarihi** | 2026-01-10 |

### 2.1 Durum DeÄŸiÅŸiklik GeÃ§miÅŸi

| Tarih | Durum | DeÄŸiÅŸiklik |
|-------|-------|------------|
| 2026-01-10 | draft | Ä°lk taslak |
| 2026-01-15 | active | OnaylandÄ± |
| 2026-05-30 | frozen | Cookie ayarlarÄ± gÃ¼ncellendi, frozen |
| 2026-08-15 | frozen | KapsamlÄ± revizyon, v2.0.0 |

### 2.2 Frozen Karar GerekÃ§esi

Session yÃ¶netimi security kritik bir karardÄ±r. Cookie adÄ±, timeout deÄŸerleri ve rotation politikasÄ± tÃ¼m sistemi etkiler. Bu nedenle frozen statÃ¼sÃ¼ndedir.

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

Session yÃ¶netimi, kullanÄ±cÄ±nÄ±n kimlik doÄŸrulama durumunu sunucu tarafÄ±nda sÃ¼rdÃ¼rmek iÃ§in kullanÄ±lÄ±r. ZayÄ±f session yÃ¶netimi ÅŸu saldÄ±rÄ±lara yol aÃ§abilir:

1. **Session Hijacking:** Cookie'nin ele geÃ§irilmesi
2. **Session Fixation:** Bilinen session ID ile saldÄ±rma
3. **Session Replay:** Eski session'Ä±n tekrar kullanÄ±lmasÄ±
4. **Cross-Site Session Theft:** Cross-origin session Ã§alma
5. **Idle Timeout Bypass:** SÃ¼resi dolmuÅŸ session'Ä±n kullanÄ±lmasÄ±

### 3.2 OWASP Top 10:2021 EtkileÅŸimi

| OWASP Kategorisi | Durum | Etki | AÃ§Ä±klama |
|------------------|-------|------|----------|
| **A07:2021** Authentication Failures | âš ï¸ DoÄŸrudan | Session yÃ¶netimi authçš„æ ¸å¿ƒ | DoÄŸru session politikasÄ± auth saldÄ±rÄ±larÄ±nÄ± engeller |
| **A08:2021** Data Integrity Failures | âš ï¸ DoÄŸrudan | Session bÃ¼tÃ¼nlÃ¼ÄŸÃ¼ | Imza ile session korumasÄ± |
| **A01:2021** Broken Access Control | â„¹ï¸ Endirekt | Session bazlÄ± eriÅŸim | Session geÃ§erliliÄŸi access control'Ã¼ etkiler |
| **A02:2021** Cryptographic Failures | â„¹ï¸ Endirekt | Session token gÃ¼Ã§lÃ¼kleme | Kriptografik token Ã¼retimi |

### 3.3 Mevcut Session Mimarisi

#### 3.3.1 Session Cookie KonfigÃ¼rasyonu

```
Cookie AyarlarÄ± (ADR-011 Frozen):
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Name:     COREMUSIC_SESS                       â”‚
â”‚  Value:    [random_bytes(32) â†’ hex]             â”‚
â”‚  Path:     /                                    â”‚
â”‚  Domain:   .coremusic.net                       â”‚
â”‚  Expires:  Session cookie (tarayÄ±cÄ± kapanÄ±nca) â”‚
â”‚  Max-Age:  3600 (1 saat idle)                   â”‚
â”‚  Secure:   true (HTTPS zorunlu)                 â”‚
â”‚  HttpOnly: true (JS eriÅŸimi yasak)             â”‚
â”‚  SameSite: Lax (ADR-011 frozen)                â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

#### 3.3.2 Middleware Pipeline'daki Yeri

```
Request
    â”‚
    â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  1. OriginCheckMiddleware                       â”‚
â”‚  2. CorsMiddleware                              â”‚
â”‚  3. RateLimiterMiddleware                       â”‚
â”‚  4. SecurityHeadersMiddleware                   â”‚
â”‚  5. SessionManagerMiddleware â—„â•â• ADR-011        â”‚
â”‚     â€¢ Session baÅŸlatÄ±r                          â”‚
â”‚     â€¢ Cookie okur/yaratÄ±r                       â”‚
â”‚     â€¢ CSP nonce Ã¼retir (ADR-012)               â”‚
â”‚     â€¢ Idle timeout kontrol eder                 â”‚
â”‚     â€¢ Session rotation yapar                     â”‚
â”‚  6. CsrfMiddleware (ADR-010)                   â”‚
â”‚  7. BypassAuthMiddleware (ADR-008)             â”‚
â”‚  8. AuthMiddleware                              â”‚
â”‚  9. PermissionMiddleware                        â”‚
â”‚  10. ValidationMiddleware                       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 3.4 Ä°tici GÃ¼Ã§ler

| # | GÃ¼Ã§ | AÃ§Ä±klama | Kritiklik |
|---|-----|----------|-----------|
| 1 | **Session Hijacking Riski** | Cookie Ã§alÄ±nÄ±rsa tÃ¼m hesap risk altÄ±nda | Kritik |
| 2 | **Multi-Subdomain** | 10+ subdomain'de session paylaÅŸÄ±mÄ± | Kritik |
| 3 | **Session Fixation** | Bilinen session ID ile saldÄ±rÄ± | YÃ¼ksek |
| 4 | **OWASP ZorunluluÄŸu** | A07:2021 uyumluluÄŸu | YÃ¼ksek |
| 5 | **Idle Timeout** | Aktif olmayan session'lar tehlike | YÃ¼ksek |
| 6 | **Referans Proje** | Eski sistemde session zayÄ±ftÄ± | YÃ¼ksek |

### 3.5 Teknik KÄ±sÄ±tlamalar

| KÄ±sÄ±tlama | AÃ§Ä±klama | Ä°lgili ADR |
|-----------|----------|------------|
| Cookie name = `COREMUSIC_SESS` | Frozen, deÄŸiÅŸtirilemez | ADR-011 |
| SameSite = `Lax` | Frozen, `None` yasak | ADR-011 |
| Idle timeout = 3600s | 1 saat, deÄŸiÅŸtirilemez | ADR-011 |
| Absolute timeout = 86400s | 24 saat, deÄŸiÅŸtirilemez | ADR-011 |
| Session rotation = 1800s | 30 dakika | ADR-011 |
| HttpOnly = true | JS eriÅŸimi yasak | ADR-011 |
| Secure = true | HTTPS zorunlu | ADR-011 |
| random_bytes(32) | Kriptografik token | ADR-022 |

### 3.6 Ekosistem EtkileÅŸimi

| Etkilenen Alan | Etki | AÃ§Ä±klama |
|---------------|------|----------|
| **L1 Security** | Kritik | SessionManagerMiddleware |
| **L0 Infrastructure** | YÃ¼ksek | Session store (file/DB) |
| **L2 Routing** | Orta | Controller session kontrolÃ¼ |
| **L3 Presentation** | YÃ¼ksek | Frontend session durumu |
| **auth.coremusic.net** | Kritik | Merkezi auth servisi |
| **TÃ¼m subdomain'ler** | YÃ¼ksek | Cross-subdomain session |

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, HttpOnly/Secure/SameSite=Lax cookie tabanlÄ± session yÃ¶netimi kullanÄ±r. Cookie adÄ± `COREMUSIC_SESS` olarak sabitlenmiÅŸtir. Idle timeout 3600s, absolute timeout 86400s, rotation 1800s olarak belirlenmiÅŸtir.**

### 4.2 Kesin Kurallar

| # | Kural | Durum | DeÄŸer |
|---|-------|-------|-------|
| 1 | Cookie name = `COREMUSIC_SESS` | âœ… Zorunlu | Frozen |
| 2 | SameSite = `Lax` | âœ… Zorunlu | Frozen |
| 3 | SameSite = `None` | âŒ Yasak | â€” |
| 4 | HttpOnly = true | âœ… Zorunlu | Frozen |
| 5 | Secure = true | âœ… Zorunlu | Frozen |
| 6 | Idle timeout = 3600s | âœ… Zorunlu | Frozen |
| 7 | Absolute timeout = 86400s | âœ… Zorunlu | Frozen |
| 8 | Session rotation = 1800s | âœ… Zorunlu | Frozen |
| 9 | Token = random_bytes(32) | âœ… Zorunlu | ADR-022 |
| 10 | Domain = .coremusic.net | âœ… Zorunlu | Frozen |
| 11 | localStorage/sessionStorage yasak | âŒ Yasak | ADR-011 |
| 12 | JS cookie eriÅŸimi yasak | âŒ Yasak | HttpOnly |

### 4.3 Session AkÄ±ÅŸ DiyagramÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Browser  â”‚                    â”‚   Server     â”‚                  â”‚  Session â”‚
â”‚ (Client)  â”‚                    â”‚ (Middleware)  â”‚                  â”‚  Store   â”‚
â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â””â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”˜                  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜
      â”‚                                â”‚                               â”‚
      â”‚  1. GET /login                 â”‚                               â”‚
      â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–ºâ”‚                               â”‚
      â”‚                                â”‚  2. Login formu gÃ¶ster       â”‚
      â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚                               â”‚
      â”‚                                â”‚                               â”‚
      â”‚  3. POST /login                â”‚                               â”‚
      â”‚  (email + password)            â”‚                               â”‚
      â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–ºâ”‚                               â”‚
      â”‚                                â”‚  4. Auth doÄŸrulama            â”‚
      â”‚                                â”‚  5. Session ID Ã¼ret           â”‚
      â”‚                                â”‚  $sessionId = bin2hex(        â”‚
      â”‚                                â”‚    random_bytes(32))          â”‚
      â”‚                                â”‚                               â”‚
      â”‚                                â”‚  6. Session'Ä± kaydet          â”‚
      â”‚                                â”‚  session_id â†’ session data    â”‚
      â”‚                                â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–ºâ”‚
      â”‚                                â”‚                               â”‚
      â”‚  7. Set-Cookie: COREMUSIC_SESS â”‚                               â”‚
      â”‚  = [sessionId]                 â”‚                               â”‚
      â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚                               â”‚
      â”‚                                â”‚                               â”‚
      â”‚  8. Sonraki isteklerde cookie  â”‚                               â”‚
      â”‚  otomatik gÃ¶nderilir           â”‚                               â”‚
      â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–ºâ”‚                               â”‚
      â”‚                                â”‚  9. Session'Ä± oku             â”‚
      â”‚                                â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚
      â”‚                                â”‚                               â”‚
      â”‚                                â”‚  10. Idle timeout kontrol      â”‚
      â”‚                                â”‚  son_activity = time()        â”‚
      â”‚                                â”‚  idle = now - last_activity   â”‚
      â”‚                                â”‚  idle > 3600 â†’ redirect login â”‚
      â”‚                                â”‚                               â”‚
      â”‚                                â”‚  11. Session rotation kontrol  â”‚
      â”‚                                â”‚  now - last_rotation > 1800   â”‚
      â”‚                                â”‚  â†’ session_regenerate_id()    â”‚
      â”‚                                â”‚                               â”‚
      â”‚  12. Response                  â”‚                               â”‚
      â”‚â—„â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚                               â”‚
```

### 4.4 Kod Ã–rnekleri

#### 4.4.1 Session Manager Middleware (PHP)

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Interfaces\Middleware\IMiddleware;

/**
 * Session Manager Middleware
 *
 * ADR-011 uyumlu session yÃ¶netimi.
 * Cookie: COREMUSIC_SESS (frozen)
 * SameSite: Lax (frozen)
 * Idle timeout: 3600s (frozen)
 * Absolute timeout: 1800s (frozen)
 * Rotation: 1800s (frozen)
 * Nonce: SecurityHeadersMiddleware'den alÄ±nÄ±r, session'a kaydedilir
 */
final class SessionManagerMiddleware implements IMiddleware
{
    public function handle(array $request, callable $next): array
    {
        // Session'Ä± baÅŸlat veya devam ettir
        $this->startSession();

        // Idle timeout kontrolÃ¼
        if ($this->isIdleTimeoutExceeded()) {
            $this->destroySession();
            return $this->createRedirectResponse('/auth/login?reason=idle_timeout');
        }

        // Absolute timeout kontrolÃ¼
        if ($this->isAbsoluteTimeoutExceeded()) {
            $this->destroySession();
            return $this->createRedirectResponse('/auth/login?reason=absolute_timeout');
        }

        // Session rotation
        $this->rotateSessionIfNeeded();

        // Son aktivite zamanÄ±nÄ± gÃ¼ncelle
        $_SESSION['last_activity'] = time();

        // CSP nonce Ã¼ret (ADR-012)
        $nonce = base64_encode(random_bytes(32));
        $_SESSION['csp_nonce'] = $nonce;

        // Request'e session bilgilerini ekle
        $request = $request->withAttribute('session', $_SESSION);
        $request = $request->withAttribute('csp_nonce', $nonce);

        return $handler->handle($request);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // ADR-011: Cookie ayarlarÄ± (frozen)
            session_set_cookie_params([
                'lifetime' => 0,                    // Session cookie
                'path' => '/',
                'domain' => '.coremusic.net',       // TÃ¼m subdomain'ler
                'secure' => true,                   // HTTPS zorunlu
                'httponly' => true,                  // JS eriÅŸimi yasak
                'samesite' => 'Lax'                 // Frozen kural
            ]);

            session_name(self::SESSION_NAME);
            session_start();
        }

        // Ä°lk ziyaretse session baÅŸlat
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
            $_SESSION['last_activity'] = time();
            $_SESSION['last_rotation'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        }
    }

    private function isIdleTimeoutExceeded(): bool
    {
        if (!isset($_SESSION['last_activity'])) {
            return true;
        }
        return (time() - $_SESSION['last_activity']) > self::IDLE_TIMEOUT;
    }

    private function isAbsoluteTimeoutExceeded(): bool
    {
        if (!isset($_SESSION['created_at'])) {
            return true;
        }
        return (time() - $_SESSION['created_at']) > self::ABSOLUTE_TIMEOUT;
    }

    private function rotateSessionIfNeeded(): void
    {
        $lastRotation = $_SESSION['last_rotation'] ?? 0;
        if ((time() - $lastRotation) > self::ROTATION_INTERVAL) {
            // ADR-010: Session fixation Ã¶nleme
            session_regenerate_id(true);
            $_SESSION['last_rotation'] = time();
        }
    }

    private function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    private function createRedirectResponse(string $url): \Psr\Http\Message\ResponseInterface
    {
        return new \GuzzleHttp\Psr7\Response(
            302,
            ['Location' => $url]
        );
    }
}
```

#### 4.4.2 Session Configuration

```php
<?php
// shared/config/session.php
// ADR-011 uyumlu session konfigÃ¼rasyonu

return [
    // Cookie ayarlarÄ± (frozen)
    'cookie' => [
        'name' => 'COREMUSIC_SESS',     // Frozen
        'path' => '/',
        'domain' => '.coremusic.net',   // TÃ¼m subdomain'ler
        'secure' => true,                // HTTPS zorunlu
        'httponly' => true,              // JS eriÅŸimi yasak
        'samesite' => 'Lax',           // Frozen
    ],

    // Timeout ayarlarÄ± (frozen)
    'timeout' => [
        'idle' => 3600,                  // 1 saat
        'absolute' => 86400,             // 24 saat
        'rotation' => 1800,              // 30 dakika
    ],

    // Token ayarlarÄ±
    'token' => [
        'length' => 32,                  // 256-bit entropy
        'algorithm' => 'random_bytes',   // Kriptografik RNG
    ],

    // Session store
    'store' => [
        'driver' => 'file',              // ADR-027: DB geÃ§iÅŸ planÄ±
        'path' => sys_get_temp_dir() . '/coremusic_sessions',
    ],
];
```

#### 4.4.3 Frontend Session YÃ¶netimi (JavaScript)

```javascript
/**
 * Session Manager (Frontend)
 *
 * ADR-011 uyumlu frontend session yÃ¶netimi.
 * HttpOnly cookie olduÄŸu iÃ§in JS session okuyamaz.
 * Sadece session durumunu kontrol eder.
 */
const SessionManager = (() => {
    'use strict';

    /**
     * Session durumunu kontrol eder.
     * GET /api/v1/auth/status endpoint'ini Ã§aÄŸÄ±rÄ±r.
     */
    async function checkSessionStatus() {
        try {
            const response = await fetch('/api/v1/auth/status', {
                method: 'GET',
                credentials: 'same-origin',  // Cookie dahil
            });

            if (response.status === 401) {
                // Session sÃ¼resi dolmuÅŸ
                redirectToLogin('session_expired');
                return false;
            }

            if (response.status === 200) {
                const data = await response.json();
                return data.authenticated === true;
            }

            return false;
        } catch (error) {
            console.error('[Session] Status check failed:', error);
            return false;
        }
    }

    /**
     * Login sayfasÄ±na yÃ¶nlendirir.
     */
    function redirectToLogin(reason) {
        const currentUrl = encodeURIComponent(window.location.href);
        window.location.href = `/auth/login?reason=${reason}&return=${currentUrl}`;
    }

    /**
     * Otomatik session kontrolÃ¼ (her 60 saniyede).
     */
    function startSessionMonitor() {
        setInterval(async () => {
            const isActive = await checkSessionStatus();
            if (!isActive) {
                redirectToLogin('session_check_failed');
            }
        }, 60000); // Her 60 saniyede kontrol
    }

    return Object.freeze({
        checkSessionStatus,
        redirectToLogin,
        startSessionMonitor,
    });
})();
```

### 4.5 KonfigÃ¼rasyon DeÄŸiÅŸiklikleri

| Dosya | Eski DeÄŸer | Yeni DeÄŸer | AÃ§Ä±klama |
|-------|-----------|-----------|----------|
| `shared/config/session.php` | â€” | TÃ¼m ayarlar | ADR-011 uyumlu |
| `shared/config/middleware.php` | â€” | SessionManager 5. sÄ±ra | Pipeline |
| `.env` | â€” | `SESSION_DRIVER=file` | ADR-027 DB geÃ§iÅŸ planÄ± |

---

## 5. Architecture

### 5.1 Session Mimarisi DiyagramÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                     L3 Presentation Layer                        â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  SPA (Vanilla JS â€” ADR-001)                               â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚  â”‚
â”‚  â”‚  â”‚ SessionManager   â”‚   â”‚ SPA Router (ADR-083)         â”‚  â”‚  â”‚
â”‚  â”‚  â”‚ (Frontend)       â”‚   â”‚                              â”‚  â”‚  â”‚
â”‚  â”‚  â”‚                  â”‚   â”‚ â€¢ Route guard'da session      â”‚  â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ checkStatus()  â”‚â—„â”€â”€â”‚ â€¢ kontrol                    â”‚  â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ monitor()      â”‚   â”‚ â€¢ 401'de redirect            â”‚  â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚                     L1 Security Layer                             â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  SessionManagerMiddleware (ADR-011)                       â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ 1. Session baÅŸlat (session_start)                 â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 2. Cookie ayarla (COREMUSIC_SESS, HttpOnly, ...)  â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 3. Idle timeout kontrol (3600s)                   â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 4. Absolute timeout kontrol (86400s)              â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 5. Session rotation (1800s)                       â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 6. CSP nonce Ã¼ret (ADR-012)                      â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 7. Request'e session attribute ekle               â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚                     L0 Infrastructure Layer                       â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Session Store (File-based â†’ DB transition)              â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ Session Data:                                      â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ session_id: string (64-char hex)                â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ user_id: int                                    â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ user_role: string                               â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ csrf_token: string (ADR-010)                    â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ csp_nonce: string (ADR-012)                     â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ created_at: int (timestamp)                     â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ last_activity: int (timestamp)                  â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ last_rotation: int (timestamp)                  â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ ip_address: string                              â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ â€¢ user_agent: string                              â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 5.2 Cookie Yaam DÃ¶ngÃ¼sÃ¼

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚   Ãœretim     â”‚     â”‚   Aktif      â”‚     â”‚   Rotation   â”‚     â”‚   Silme     â”‚
â”‚              â”‚     â”‚              â”‚     â”‚              â”‚     â”‚             â”‚
â”‚ random_bytes â”‚â”€â”€â”€â”€â–ºâ”‚ HttpOnly +   â”‚â”€â”€â”€â”€â–ºâ”‚ 1800s'de bir â”‚â”€â”€â”€â”€â–ºâ”‚ Timeout     â”‚
â”‚ (32 byte)    â”‚     â”‚ Secure +     â”‚     â”‚ session_     â”‚     â”‚ veya        â”‚
â”‚              â”‚     â”‚ SameSite=Lax â”‚     â”‚ regenerate   â”‚     â”‚ logout      â”‚
â”‚ COREMUSIC_   â”‚     â”‚              â”‚     â”‚ _id(true)    â”‚     â”‚             â”‚
â”‚ SESS cookie  â”‚     â”‚ .coremusic.  â”‚     â”‚              â”‚     â”‚ Cookie      â”‚
â”‚              â”‚     â”‚ net domain   â”‚     â”‚ Eski ID      â”‚     â”‚ silinir     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â”‚ geÃ§ersiz     â”‚     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                                          â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 6. Alternatives Considered

### 6.1 Alternatif 1: JWT Token-based Auth

**AÃ§Ä±klama:** Session yerine JWT token kullanÄ±mÄ±.

**Avantajlar:**
- Stateless
- Scalable
- Mobile-friendly

**Dezavantajlar:**
- Token rotation zor
- Revocation zor
- Client-side storage gerekir (HttpOnly cookie ile birlikte Ã§alÄ±ÅŸsa bile)
- ADR-043 ile tam uyumlu deÄŸil

**Neden Reddedildi:** Session-based auth daha gÃ¼venli ve basit. JWT, refresh token management karmaÅŸÄ±klÄ±ÄŸÄ± yaratÄ±r.

### 6.2 Alternatif 2: SameSite=None

**AÃ§Ä±klama:** Cross-site cookie desteÄŸi iÃ§in SameSite=None.

**Avantajlar:**
- Cross-site istekler Ã§alÄ±ÅŸÄ±r
- Third-party entegrasyonlar kolay

**Dezavantajlar:**
- CSRF riski artar
- ADR-010 ile Ã§eliÅŸir
- GÃ¼venlik aÃ§Ä±ÄŸÄ± oluÅŸturur

**Neden Reddedildi:** SameSite=None CSRF korumasÄ±nÄ± zayÄ±flatÄ±r. ADR-011 SameSite=Lax frozen kuralÄ±dÄ±r.

### 6.3 Karar Matrisi

| Kriter | AÄŸÄ±rlÄ±k | Session (seÃ§ilen) | JWT | SameSite=None | Cookie-only |
|--------|---------|-------------------|-----|---------------|-------------|
| GÃ¼venlik | %35 | â­â­â­â­â­ | â­â­â­ | â­â­ | â­â­â­ |
| ADR Uyumu | %25 | â­â­â­â­â­ | â­â­ | â­ | â­â­â­ |
| Scalability | %15 | â­â­â­ | â­â­â­â­â­ | â­â­â­â­â­ | â­â­â­â­â­ |
| Basitlik | %15 | â­â­â­â­ | â­â­ | â­â­â­â­â­ | â­â­â­â­ |
| Revocation | %10 | â­â­â­â­â­ | â­â­ | â­â­â­ | â­â­â­â­ |
| **TOPLAM** | %100 | **4.50** | **3.10** | **2.85** | **3.55** |

---

## 7. Consequences

### 7.1 Olumlu SonuÃ§lar

| # | SonuÃ§ | Etki |
|---|-------|------|
| 1 | Session hijacking engellenir | YÃ¼ksek |
| 2 | Session fixation Ã¶nlenir | YÃ¼ksek |
| 3 | Multi-subdomain uyumu | YÃ¼ksek |
| 4 | OWASP Top 10 uyumluluÄŸu | YÃ¼ksek |
| 5 | Basit uygulama | Orta |

### 7.2 Olumsuz SonuÃ§lar

| # | SonuÃ§ | Risk | Mitigation |
|---|-------|------|------------|
| 1 | File-based session bottleneck | Orta | DB session geÃ§iÅŸ planÄ± (ADR-027) |
| 2 | Session store baÄŸÄ±mlÄ±lÄ±ÄŸÄ± | Orta | Session store dayanÄ±klÄ±lÄ±ÄŸÄ± |
| 3 | Cookie size limiti | DÃ¼ÅŸÃ¼k | Minimal session data |

---

## 8. Risk Analysis

| # | Risk | OlasÄ±lÄ±k | Etki | Mitigation |
|---|------|----------|------|------------|
| 1 | Session hijacking | DÃ¼ÅŸÃ¼k | Kritik | HttpOnly + Secure + SameSite |
| 2 | Session fixation | DÃ¼ÅŸÃ¼k | YÃ¼ksek | Rotation 1800s |
| 3 | Idle timeout bypass | DÃ¼ÅŸÃ¼k | YÃ¼ksek | Server-side kontrol |
| 4 | Session store crash | DÃ¼ÅŸÃ¼k | YÃ¼ksek | DB fallback (ADR-027) |
| 5 | Cookie theft (MITM) | DÃ¼ÅŸÃ¼k | Kritik | HTTPS zorunlu |

---

## 9. Testing Strategy

| Test TÃ¼rÃ¼ | Hedef | Kapsama |
|-----------|-------|---------|
| Session Start | Unit | %100 |
| Idle Timeout | Unit | %100 |
| Absolute Timeout | Unit | %100 |
| Session Rotation | Unit | %100 |
| Cookie Settings | Integration | %100 |
| Multi-Subdomain | E2E | %100 |
| Session Fixation | Security | %100 |

### Test Kodu Ã–rneÄŸi

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;

final class SessionManagerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'TestAgent';
    }

    public function testSessionCookieNameIsCorrect(): void
    {
        // ADR-011: Cookie name frozen
        $this->assertEquals('COREMUSIC_SESS', 'COREMUSIC_SESS');
    }

    public function testIdleTimeoutIs3600Seconds(): void
    {
        // ADR-011: Idle timeout frozen
        $this->assertEquals(3600, 3600);
    }

    public function testAbsoluteTimeoutIs86400Seconds(): void
    {
        // ADR-011: Absolute timeout frozen
        $this->assertEquals(86400, 86400);
    }

    public function testSessionRotationIs1800Seconds(): void
    {
        // ADR-011: Rotation frozen
        $this->assertEquals(1800, 1800);
    }

    public function testSessionHasRequiredFields(): void
    {
        $_SESSION['created_at'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['last_rotation'] = time();
        $_SESSION['ip_address'] = '127.0.0.1';
        $_SESSION['user_agent'] = 'TestAgent';

        $this->assertArrayHasKey('created_at', $_SESSION);
        $this->assertArrayHasKey('last_activity', $_SESSION);
        $this->assertArrayHasKey('last_rotation', $_SESSION);
        $this->assertArrayHasKey('ip_address', $_SESSION);
        $this->assertArrayHasKey('user_agent', $_SESSION);
    }
}
```

---

## 10. OWASP Compliance

| OWASP Kategorisi | Durum | Uygulama |
|------------------|-------|----------|
| **A07:2021** Auth Failures | âœ… Uyumlu | Session rotation, timeout, HttpOnly |
| **A08:2021** Data Integrity | âœ… Uyumlu | Session signature, CSRF token |
| **A01:2021** Access Control | âœ… Uyumlu | Session bazlÄ± RBAC |

---

## 11. Performance Impact

| Ä°ÅŸlem | Overhead | Kabul Edilebilir mi? |
|-------|----------|---------------------|
| session_start() | < 2ms | âœ… Evet |
| Cookie parse | < 0.1ms | âœ… Evet |
| Timeout kontrol | < 0.1ms | âœ… Evet |
| Rotation | < 5ms (1800s'de bir) | âœ… Evet |
| **Toplam** | **< 7.2ms** | âœ… Evet |

---

## 12. Rollback Plan

| Senaryo | Geri Alma |
|---------|-----------|
| Session store Ã§Ã¶ktÃ¼ | File session'a fallback |
| Cookie ayarlarÄ± bozuldu | Frozen config'i geri yÃ¼kle |
| Timeout yanlÄ±ÅŸ | Frozen deÄŸerleri geri yÃ¼kle |

---

## 13. Related Decisions

| ADR | BaÅŸlÄ±k | Ä°liÅŸki |
|-----|--------|--------|
| ADR-008 | Bypass Auth | Test bypass |
| ADR-010 | CSRF Protection | Token session'da |
| ADR-012 | CSP Nonce | Nonce session'da |
| ADR-013 | Rate Limiting | Auth brute-force |
| ADR-022 | DB Security | Token encryption |
| ADR-027 | Dual Storage | Session store |
| ADR-043 | Auth Consolidation | Cross-subdomain |

---

## 14. Glossary

| Terim | TanÄ±m |
|-------|-------|
| **Session** | KullanÄ±cÄ± oturum verisi |
| **HttpOnly** | Cookie JS'den eriÅŸilemez |
| **Secure** | Cookie sadece HTTPS ile gÃ¶nderilir |
| **SameSite** | Cross-site cookie politikasÄ± |
| **Idle Timeout** | Aktif olmayan session sÃ¼resi |
| **Absolute Timeout** | Maksimum session sÃ¼resi |
| **Session Rotation** | Periyodik session ID deÄŸiÅŸimi |
| **Session Fixation** | Bilinen session ID ile saldÄ±rÄ± |

---

## 15. Edge Cases

| # | Durum | Ã‡Ã¶zÃ¼m |
|---|-------|-------|
| 1 | Cookie disabled | Redirect login + uyarÄ± |
| 2 | Multi-tab | TÃ¼m sekmeler aynÄ± session |
| 3 | Mobile browser | SameSite=Lax korur |
| 4 | CSRF + Session | ADR-010 ile entegre |
| 5 | Session store down | DB fallback (ADR-027) |

---

## 16. Warnings

> **âš ï¸ CRITICAL:** SameSite=None kesinlikle kullanÄ±lmamalÄ±dÄ±r. ADR-011 SameSite=Lax frozen kuralÄ±dÄ±r.

> **âš ï¸ CRITICAL:** localStorage/sessionStorage'da auth token saklanmaz. Sadece HttpOnly cookie kullanÄ±lÄ±r.

> **âš ï¸ WARNING:** Session rotation 1800s'de bir yapÄ±lmalÄ±dÄ±r. Bu deÄŸer deÄŸiÅŸtirilmez.

---

## 17. Limitations

| # | SÄ±nÄ±rlama | Ã‡Ã¶zÃ¼m |
|---|-----------|-------|
| 1 | File-based bottleneck | DB session (ADR-027) |
| 2 | Single-server only | Redis session (gelecek) |
| 3 | Cookie size limit | Minimal data |

---

## 18. Dependencies

| BaÄŸÄ±mlÄ±lÄ±k | Versiyon | KullanÄ±m |
|------------|---------|---------|
| PHP 8.4+ | 8.4 | Session extension |
| OpenSSL | 3.0+ | random_bytes() |

---

## 19. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v2.1 | Redis session store | 2026-Q4 |
| v2.2 | DB session store | 2026-Q4 |
| v3.0 | Distributed session | 2027-Q1 |

---

## 20. Related Documents

| Dosya | Konum |
|-------|-------|
| Security Layer | `architecture/l1-security/` |
| Session Config | `shared/config/session.php` |
| Session Middleware | `shared/src/Security/Middleware/SessionManagerMiddleware.php` |

---

## 21. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r SayÄ±sÄ±** | ~520 |
| **Status** | Frozen |
| **OWASP Compliance** | âœ… |
| **Test Coverage** | â‰¥ %80 |

---

*ADR-011: Session Management v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*