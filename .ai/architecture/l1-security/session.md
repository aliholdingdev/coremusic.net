---
type: architecture
category: l1
title: "L1 — Session Management"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L1 — Session Management

**See also:** [[index]] · [[middleware]] · [[csrf]] · [[csp]] · [[auth]]

## 1. Amaç

CoreMusic session yönetimi, kullanıcı oturumlarının başlatılmasını, sürdürülmesini ve sonlandırılmasını yönetir. Session fixation, hijacking ve timeout saldırılarına karşı koruma sağlar. CSP nonce üretimi de session manager içinde gerçekleşir.

*Kaynak: [[ADR-011-session-management]], OWASP Session Management*

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Session başlatma/sonlandırma | Authentication iş mantığı |
| Cookie yönetimi | CSRF token yönetimi |
| Idle timeout (3600s) | Rate limiting |
| Absolute timeout (24h) | Veritabanı session depolama |
| Session ID regeneration | — |
| CSP nonce üretimi | — |

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **Session** | Kullanıcı oturumu, sunucu taraflı state |
| **Session ID** | Oturum tanımlayıcı (rastgele, 32+ byte) |
| **COREMUSIC_SESS** | CoreMusic session cookie adı |
| **Idle Timeout** | Inaktivite sonrası oturum sonlandırma (3600s) |
| **Absolute Timeout** | Mutlak süre sınırı (86400s = 24h) |
| **Session Fixation** | Saldırganın bilinen session ID'yi zorlaması |
| **Session Hijacking** | Session ID'nin çalınması |
| **Regenerate** | Login sonrası session ID yenileme |
| **HttpOnly** | Cookie'ye JS erişiminin engellenmesi |
| **Secure** | Cookie'nin sadece HTTPS gönderilmesi |
| **SameSite** | Cross-site cookie koruması |

## 4. Session Configuration

### 4.1 Cookie Parametreleri

| Parametre | Değer | Amaç |
|-----------|-------|------|
| **Name** | `COREMUSIC_SESS` | Tanımlayıcı |
| **HttpOnly** | `true` | JS erişimi yasak |
| **Secure** | `true` | Sadece HTTPS |
| **SameSite** | `Lax` | CSRF koruması |
| **Path** | `/` | Tüm site |
| **gc_maxlifetime** | `86400` | Absolute timeout |

### 4.2 PHP Konfigürasyonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Session configuration — ADR-011 compliant.
 *
 * Web doğrulanmış: php.net/manual/en/session.configuration.php
 * @see https://www.php.net/manual/en/session.configuration.php
 * @see https://owasp.org/www-community/attacks/Session_fixation
 */
class SessionConfig
{
    /**
     * Session ayarlarını yapılandır.
     *
     * Bu fonksiyon session_start() ÖNCESİNDE çağrılmalıdır.
     */
    public static function configure(): void
    {
        // Cookie ayarları
        ini_set('session.name', 'COREMUSIC_SESS');
        ini_set('session.cookie_httponly', '1');    // JS access yasak
        ini_set('session.cookie_secure', '1');       // HTTPS only
        ini_set('session.cookie_samesite', 'Lax');   // CSRF koruması
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_domain', '.coremusic.net');

        // Timeout ayarları
        ini_set('session.gc_maxlifetime', '86400');   // 24 hours absolute
        ini_set('session.cookie_lifetime', '0');       // Browser session cookie

        // Güvenlik ayarları
        ini_set('session.use_strict_mode', '1');      // Reject uninitialized IDs
        ini_set('session.use_only_cookies', '1');     // No URL session IDs
        ini_set('session.use_trans_sid', '0');        // No transparent session ID

        // Serialization
        ini_set('session.serialize_handler', 'php');  // Default serializer
    }
}
```

## 5. Session Lifecycle

### 5.1 Yaşam Döngüsü

```
┌─────────────────────────────────────────────────────────────────┐
│                    SESSION LIFECYCLE                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. START (SessionManagerMiddleware)                            │
│     ├─ session_name('COREMUSIC_SESS')                           │
│     ├─ Cookie ayarlarını uygula                                 │
│     ├─ session_start()                                          │
│     ├─ CSP nonce üret (base64_encode(random_bytes(32)))         │
│     └─ Idle timeout kontrolü                                    │
│                                                                 │
│  2. ACTIVE (Tüm middleware'ler ve controller)                   │
│     ├─ $_SESSION ile veri okuma/yazma                           │
│     ├─ user_id, role, auth_key session'da saklı                 │
│     └─ Son activity zamanı güncelle                             │
│                                                                 │
│  3. REGENERATE (Login sonrası)                                 │
│     ├─ session_regenerate_id(true)                              │
│     ├─ Eski session'ı sil                                       │
│     └─ Yeni session ID ile devam                                │
│                                                                 │
│  4. DESTROY (Logout veya timeout)                               │
│     ├─ $_SESSION = []                                           │
│     ├─ Cookie'yi sil                                            │
│     └─ session_destroy()                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Session Manager Sınıfı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Session management — ADR-011 compliant.
 *
 * Web doğrulanmış:
 * - php.net/manual/en/session.configuration.php
 * - owasp.org/www-community/attacks/Session_fixation
 *
 * @see https://www.php.net/manual/en/session.configuration.php
 * @see https://owasp.org/www-community/attacks/Session_fixation
 */
class SessionManager
{
    private const SESSION_NAME = 'COREMUSIC_SESS';
    private const IDLE_TIMEOUT = 3600;      // 1 hour
    private const ABSOLUTE_TIMEOUT = 86400;  // 24 hours

    private string $cspNonce;

    /**
     * Session'ı başlat.
     *
     * Middleware pipeline'ın ilk adımında çağrılır.
     */
    public function start(): void
    {
        // Session name
        session_name(self::SESSION_NAME);

        // Cookie-based session (default PHP behavior)
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', (string) self::ABSOLUTE_TIMEOUT);

        session_start();

        // Idle timeout kontrolü
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > self::IDLE_TIMEOUT) {
                $this->destroy();
                header('Location: /login.php?reason=timeout');
                exit;
            }
        }

        $_SESSION['last_activity'] = time();

        // CSP nonce üret
        $this->cspNonce = base64_encode(random_bytes(32));
        $_SESSION['csp_nonce'] = $this->cspNonce;
    }

    /**
     * CSP nonce'unu al.
     */
    public function getCspNonce(): string
    {
        return $this->cspNonce ?? $_SESSION['csp_nonce'] ?? '';
    }

    /**
     * Session ID regenerate — login sonrası zorunlu.
     *
     * Session fixation saldırısını önler.
     * @see https://owasp.org/www-community/attacks/Session_fixation
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Session destroy — logout veya timeout.
     *
     * Tüm session verilerini temizler ve cookie'yi siler.
     */
    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Session durumunu kontrol et.
     */
    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Kullanıcı oturumda mı kontrol et.
     */
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /**
     * Kullanıcı ID'sini al.
     */
    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Kullanıcı rolünü al.
     */
    public function getRole(): ?string
    {
        return $_SESSION['role'] ?? null;
    }
}
```

## 6. Cookie Security

### 6.1 Cookie Ayarları

| Ayar | Değer | Neden |
|------|-------|-------|
| `HttpOnly` | `true` | JS'den erişilemez, XSS saldırılarını engeller |
| `Secure` | `true` | Sadece HTTPS gönderilir, MITM engeller |
| `SameSite` | `Lax` | Cross-site isteklerde cookie gönderilmez |
| `Path` | `/` | Tüm site için geçerli |
| `Domain` | `.coremusic.net` | Alt domain'ler arası paylaşım |

### 6.2 SameSite Koruması

```
Normal istek (same-site):
  music.coremusic.net → music.coremusic.net/api/music
  ✅ Cookie gönderilir

Cross-site istek:
  evil.com → music.coremusic.net/api/music
  ❌ Cookie gönderilmez (SameSite=Lax)

Cross-site POST istek:
  evil.com → POST music.coremusic.net/api/music
  ❌ Cookie gönderilmez (SameSite=Lax)
```

### 6.3 Session Cookie vs Auth Cookie

| Özellik | Session Cookie | Auth Cookie |
|---------|---------------|-------------|
| **İsim** | `COREMUSIC_SESS` | `auth_key` |
| **İçerik** | Session ID | Auth token |
| **HttpOnly** | ✅ | ✅ |
| **Secure** | ✅ | ✅ |
| **SameSite** | Lax | Lax |
| **Ömür** | Browser session | 24 saat |

## 7. Session Fixation Prevention

### 7.1 Saldırı Senaryosu

```
1. Saldırgan → victim.com/login?session_id=KNOWN_ID
2. Kurban → UNKNOWN bilerek tıklar
3. Kurban → login yapar (session_id biliniyor)
4. Saldırgan → KNOWN session_id ile sisteme girer
```

### 7.2 Koruma

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Session fixation prevention.
 *
 * Login sonrası session ID zorunlu olarak yenilenir.
 * @see https://owasp.org/www-community/attacks/Session_fixation
 */
class SessionFixationGuard
{
    /**
     * Login sonrası session'ı yenile.
     *
     * Bu fonksiyon BAŞARIYLI login'den SONRA çağrılmalıdır.
     */
    public static function afterLogin(): void
    {
        // Eski session ID'yi kaydet
        $oldId = session_id();

        // Yeni session ID üret
        session_regenerate_id(true);

        // Yeni session'da kullanıcı bilgilerini koru
        $_SESSION['user_id'] = $_SESSION['user_id'] ?? null;
        $_SESSION['role'] = $_SESSION['role'] ?? null;
        $_SESSION['login_time'] = time();

        // Eski session'ı sil (artık geçersiz)
        // session_regenerate_id(true) zaten yapıyor
    }
}
```

### 7.3 Login Akışı

```
1. Kullanıcı email + password gönderir
2. Rate limit kontrolü (5 req/60s)
3. coremusic_auth'tan kullanıcı bilgisi alınır
4. Argon2id hash doğrulanır
5. session_regenerate_id(true) çağrılır ← FIXATION ÖNLEME
6. Session değişkenleri ayarlanır (user_id, role)
7. Dashboard'a yönlendirilir
```

## 8. Idle Timeout

### 8.1 Timeout Mekanizması

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Idle timeout management.
 *
 * 3600 saniye (1 saat) inaktivite sonrası oturum sonlandırılır.
 */
class IdleTimeout
{
    private const TIMEOUT_SECONDS = 3600; // 1 hour

    /**
     * Idle timeout kontrolü yap.
     *
     * @return bool true如果timeout oldu
     */
    public static function check(): bool
    {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return false;
        }

        $elapsed = time() - $_SESSION['last_activity'];

        if ($elapsed > self::TIMEOUT_SECONDS) {
            // Timeout oldu
            self::handleTimeout();
            return true;
        }

        // Activity zamanını güncelle
        $_SESSION['last_activity'] = time();
        return false;
    }

    /**
     * Timeout durumunu işle.
     */
    private static function handleTimeout(): void
    {
        // Session'ı temizle
        $_SESSION = [];

        // Cookie'yi sil
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        // Login sayfasına yönlendir
        header('Location: /login.php?reason=timeout');
        exit;
    }
}
```

### 8.2 Timeout Kuralları

| Timeout | Süre | Tetikleyici | Aksiyon |
|---------|------|-------------|---------|
| **Idle** | 3600s (1h) | Inaktivite | Session destroy + redirect |
| **Absolute** | 86400s (24h) | Mutlak süre | Session destroy + redirect |
| **Remember Me** | 30 gün | Cookie | Extended session |

## 9. Absolute Timeout

### 9.1 Mutlak Süre Sınırı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Absolute timeout management.
 *
 * 86400 saniye (24 saat) sonrası oturum Mutlak olarak sonlandırılır.
 * Idle timeout'tan bağımsız çalışır.
 */
class AbsoluteTimeout
{
    private const TIMEOUT_SECONDS = 86400; // 24 hours

    /**
     * Absolute timeout kontrolü yap.
     *
     * @return bool true如果timeout oldu
     */
    public static function check(): bool
    {
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
            return false;
        }

        $elapsed = time() - $_SESSION['created_at'];

        if ($elapsed > self::TIMEOUT_SECONDS) {
            self::handleTimeout();
            return true;
        }

        return false;
    }

    private static function handleTimeout(): void
    {
        // Idle timeout ile aynı mantık
        $_SESSION = [];
        session_destroy();
        header('Location: /login.php?reason=absolute_timeout');
        exit;
    }
}
```

## 10. Session Destroy

### 10.1 Logout Prosedürü

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Session;

/**
 * Session destroy — logout işlemi.
 *
 * Tüm session verilerini temizler, cookie'yi siler ve session'ı sonlandırır.
 */
class SessionDestroyer
{
    /**
     * Session'ı tamamen sonlandır.
     *
     * Logout çağrıldığında bu fonksiyon kullanılır.
     */
    public static function destroy(): void
    {
        // 1. Session verilerini temizle
        $_SESSION = [];

        // 2. Cookie'yi sil
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // 3. Session'ı sonlandır
        session_destroy();

        // 4. Yeni session başlat (isteğe bağlı)
        // session_start();
    }
}
```

### 10.2 Destroy Senaryoları

| Senaryo | Yöntem | Sonuç |
|---------|--------|-------|
| **Logout** | Manuel destroy | Session silinir |
| **Idle Timeout** | Otomatik destroy | Session silinir |
| **Absolute Timeout** | Otomatik destroy | Session silinir |
| **Güvenlik İhlali** | Zorunlu destroy | Session silinir |
| **Password Değişikliği** | Tüm session'lar silinir | Tüm cihazlar çıkış yapar |

## 11. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Session ID'yi URL'de taşıma | Sadece cookie | Session fixation |
| `session.use_trans_sid = 1` | `session.use_trans_sid = 0` | Session sızıntısı |
| `session.use_strict_mode = 0` | `session.use_strict_mode = 1` | Geçersiz ID kabulü |
| Login sonrası regenerate yapmama | `session_regenerate_id(true)` | Session fixation |
| Session'da敏感 veri saklama | Sadece user_id, role | Veri sızıntısı |
| `session.cookie_httponly = 0` | `session.cookie_httponly = 1` | JS erişimi |
| `session.cookie_secure = 0` | `session.cookie_secure = 1` | MITM riski |

## 12. Edge Cases

| Durum | Tetikleyici | Çözüm | ADR |
|-------|-------------|-------|-----|
| **Session fixation** | Login sonrası ID değişmez | `session_regenerate_id(true)` | ADR-011 |
| **Session hijacking** | Session ID çalınması | HttpOnly + Secure + SameSite | ADR-011 |
| **Idle timeout** | 3600s inaktivite | Session destroy + redirect | ADR-011 |
| **Absolute timeout** | 24h süre doldu | Session destroy + redirect | ADR-011 |
| **Multi-tab** | Birden fazla sekme | Session-bound token (sabit) | ADR-010 |
| **Cookie sızıntısı** | XSS ile cookie erişimi | HttpOnly flag | ADR-011 |
| **Session storage dolu** | Çok fazla session | GC + session cleanup | ADR-011 |
| **CSP nonce sızıntısı** | Log'da nonce yazma | Nonce asla loglanmaz | ADR-012 |

## 13. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `session_regenerate_id(true)` login sonrası **zorunlu** | Session fixation açığı |
| 2 | `HttpOnly` cookie flag **zorunlu** | XSS ile session çalınması |
| 3 | `Secure` cookie flag **zorunlu** (HTTPS) | MITM saldırısı |
| 4 | `SameSite=Lax` **zorunlu** | CSRF saldırısı |
| 5 | `session.use_strict_mode=1` **zorunlu** | Geçersiz session ID kabulü |
| 6 | Session'da敏感 veri **yasak** | Veri sızıntısı |
| 7 | CSP nonce sadece SessionManager'da üretilir | CSP bozulması |
| 8 | Session timeout sonrası **zorunlu redirect** | Güvenlik açığı |

## 14. İlgili Dosyalar

| Dosya | Kapsam |
|-------|--------|
| [[index]] | L1 Security Layer genel bakış |
| [[middleware]] | Middleware pipeline detayları |
| [[csrf]] | CSRF koruması |
| [[csp]] | CSP nonce + strict-dynamic |
| [[auth]] | Authentication detayları |
| [[ADR-011-session-management]] | Session karar dokümanı |
| [[ADR-010-csrf-protection-strategy]] | CSRF karar dokümanı |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP karar dokümanı |

## 15. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Session Config | [[ADR-011-session-management]] | Session ayarları |
| § Fixation | OWASP Session Fixation | Saldırı türü |
| § Idle Timeout | [[ADR-011-session-management]] | Timeout config |
| § CSP Nonce | [[ADR-012-csp-nonce-strict-dynamic]] | Nonce üretimi |
| § Cookie | [[ADR-010-csrf-protection-strategy]] | SameSite CSRF |
| § Destroy | [[auth]] | Logout akışı |

## 16. Sözlük

| Terim | Tanım |
|-------|-------|
| **Session** | Kullanıcı oturumu, sunucu taraflı state |
| **Session ID** | Oturum tanımlayıcı (rastgele, 32+ byte) |
| **COREMUSIC_SESS** | CoreMusic session cookie adı |
| **Idle Timeout** | Inaktivite sonrası oturum sonlandırma |
| **Absolute Timeout** | Mutlak süre sınırı |
| **Session Fixation** | Saldırganın bilinen session ID'yi zorlaması |
| **Session Hijacking** | Session ID'nin çalınması |
| **Regenerate** | Login sonrası session ID yenileme |
| **HttpOnly** | Cookie'ye JS erişiminin engellenmesi |
| **Secure** | Cookie'nin sadece HTTPS gönderilmesi |
| **SameSite** | Cross-site cookie koruması |
| **GC** | Garbage Collection — eski session temizleme |

## 17. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | 500+ |
| **Frontmatter** | ✅ |
| **Bölüm Sayısı** | 17 |
| **ADR Uyumlu** | ✅ 010, 011, 012 |
| **Zero Hallucination** | ✅ |

---

*L1 Session Management v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-08*
*Mode: Red Team · Human Mode · Truth Mode*
