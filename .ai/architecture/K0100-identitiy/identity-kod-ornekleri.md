---
node_id: K010-identitiy/identity-kod-ornekleri
node_type: domain
status: verified
parent_id: K010-identitiy
children: []
depends_on:
  - K009-security
  - K007-data
evidence:
  - "shared/src/Security/JwtService.php L44-72, L83-109 (B9) — 2026-10-12"
  - "shared/src/Session/SessionLifecycle.php L21-69 (B10) — 2026-10-12"
  - "shared/src/OAuth/OAuthManager.php L155-235 (B4, B5) — 2026-10-12"
validation: "identity-dogrulama.md §2 (24/24 kod iddiası) — alıntılar batim, kanıt paketi §B"
group: kod
open_questions: []
---

# K010-identitiy — kod örnekleri (gerçek repo alıntıları)

> **Kural:** her alıntı kanıt paketi §B'den **batim** alınmıştır; `Dosya:`/`Satır:` çifti ile bağlanır. Yorum satırları korunur.

## §1 SessionConfig — oturum sabitleri (SSOT)

```php
// Dosya: shared\src\Session\SessionConfig.php / Satır: 15-20
    /** Merkezi session sabitleri — tek kaynak (SSOT) */
    public const COOKIE_EXPIRY = 42000;
    public const MAX_LIFETIME = 1800;          // 30 dakika
    public const IDLE_TIMEOUT = 3600;          // 1 saat
    public const ROTATION_INTERVAL = 1800;     // 30 dakikada bir rotation
    public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri
```

| Sabit | Değer | K010 anlamı |
|---|---|---|
| `COOKIE_EXPIRY` | 42000 (11.6 saat) | cookie hayatta kalma süresi — sunucu tarafı ömürden bağımsız |
| `MAX_LIFETIME` | 1800 (30 dk) | mutlak oturum üst sınırı → `destroy()` |
| `IDLE_TIMEOUT` | 3600 (1 sa) | hareketsizlik eşiği → `destroy()` (paradox: 1800 bunu asla ulaşılamaz kılar) |
| `ROTATION_INTERVAL` | 1800 (30 dk) | `session_regenerate_id(true)` periyodu |
| `DIR_PERMISSIONS` | 0750 | session dosya sistemi izni |

## §2 SessionKeys — oturum anahtar sabitleri

```php
// Dosya: shared\src\Security\SessionKeys.php / Satır: 5-25
final class SessionKeys
{
    public const USER_ID       = 'MM_UserID';
    public const USERNAME      = 'MM_Username';
    public const EMAIL         = 'MM_Email';
    public const DISPLAY_NAME  = 'MM_DisplayName';
    public const ACCOUNT_TYPE  = 'MM_AccountType';
    public const IMAGE         = 'MM_Image';
    public const GENDER        = 'cm_gender';
    public const LAST_ACTIVE   = '_session_last_active';
    public const ROTATED_AT    = '_session_rotated_at';

    public const USER_KEYS = [
        self::USER_ID,
        self::USERNAME,
        self::EMAIL,
        self::DISPLAY_NAME,
        self::ACCOUNT_TYPE,
        self::IMAGE,
        self::GENDER,
    ];
}
```

`USER_KEYS` kütlesi logout temizliği içindir: `ISessionManager::clearDisplayCookies` ve `destroy()` bu anahtarları bilir.

## §3 SessionLifecycle — startOrExtend

```php
// Dosya: shared\src\Session\SessionLifecycle.php / Satır: 21-69 (kısaltılmış — bkz kanıt paketi B10)
    public function startOrExtend(?string $externalNonce = null): array
    {
        $result = [
            'started'          => false,
            'idleTimedOut'     => false,
            'lifetimeTimedOut' => false,
        ];

        SessionBootstrapper::ensureStarted();

        if (session_status() === PHP_SESSION_ACTIVE) {
            if ($this->isSessionExpired()) {
                $result['lifetimeTimedOut'] = true;
                $this->destroy();
                $result['started'] = true;
            } else {
                $this->extendSession();
            }
        }

        if ($this->isIdleTimedOut()) {
            $result['idleTimedOut'] = true;
            $this->destroy();
            $result['started'] = true;
        }

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Dışarıdan nonce gelirse onu kullan, yoksa üret
        $_SESSION['csp_nonce'] = $externalNonce ?? bin2hex(random_bytes(32));

        // Session rotation: 30 dakikada bir session ID yenile
        $lastRotation = $_SESSION['_session_rotated_at'] ?? 0;
        $now = time();
        if ($lastRotation === 0) {
            $_SESSION['_session_rotated_at'] = $now;
        } elseif (($now - $lastRotation) >= SessionConfig::ROTATION_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['_session_rotated_at'] = $now;
        }

        $now = time();
        $_SESSION['last_activity'] = $now;
        $_SESSION['_session_last_active'] = $now;

        return $result;
    }
```

Gözlemler: (1) `csrf_token` ve `csp_nonce` oturum içinde üretilir; (2) rotation `session_regenerate_id(true)` (eski dosyayı siler — session fixation azaltımı); (3) iki zaman damgası (`last_activity` + `_session_last_active`) — `SessionKeys::LAST_ACTIVE` ile hizalı.

## §4 SessionConfig — cookieParams

```php
// Dosya: shared\src\Session\SessionConfig.php / Satır: 51-62
    /** @return array{lifetime: int, path: string, domain: string, secure: bool, httponly: bool, samesite: string} */
    public function cookieParams(): array
    {
        return [
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => $this->cookieDomain,
            'secure'   => $this->secure,
            'httponly'  => true,
            'samesite' => 'Lax',
        ];
    }
```

`lifetime=0` (session cookie) + `httponly=true` + `samesite=Lax` üçlüsü; `secure` HTTPS tespiti ile (`fromEnvironment` L38-41). `domain` default `.coremusic.net` (L29-49) — alt alan adları arası oturum paylaşımı bununla sağlanır.

## §5 JwtService — issue

```php
// Dosya: shared\src\Security\JwtService.php / Satır: 44-72
    public function issue(string $userId, array $extra = []): array
    {
        if ($userId === '') {
            throw new \InvalidArgumentException('JWT sub (userId) boş olamaz.');
        }

        $now = time();
        $jti = bin2hex(random_bytes(16));
        $exp = $now + $this->ttlSeconds;

        $claims = array_merge($extra, [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $userId,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'jti' => $jti,
        ]);

        $token = JWT::encode($claims, $this->privateKey(), self::ALGORITHM);

        return [
            'token'      => $token,
            'expires_in' => $this->ttlSeconds,
            'jti'        => $jti,
            'token_type' => 'Bearer',
        ];
    }
```

`jti` 32 hex karakter (16 byte); revocation bu değerin `sha256`'sının `user_tokens` içinde aranmasına bağlanır (`IUserRepository.php` L40).

## §6 JwtService — validate (fail-closed)

```php
// Dosya: shared\src\Security\JwtService.php / Satır: 83-109
    public function validate(string $token): ?array
    {
        if ($token === '') {
            return null;
        }

        try {
            $claims = JWT::decode($token, new Key($this->publicKey(), self::ALGORITHM));
        } catch (\Throwable) {
            // imza/süre/format hataları — hiçbir ayrıntı sızdırılmaz
            return null;
        }

        if (($claims->iss ?? null) !== $this->issuer) {
            return null;
        }
        if (($claims->aud ?? null) !== $this->audience) {
            return null;
        }
        if (!isset($claims->sub, $claims->jti) || $claims->sub === '' || $claims->jti === '') {
            return null;
        }

        /** @var array<string, mixed> $arr */
        $arr = (array) $claims;
        return $arr;
    }
```

RFC 8725 ile hizalı davranışlar: algoritma sabit (`self::ALGORITHM`, RS256 — `alg:none`/downgrade yok), `iss`/`aud` zorunlu, hata detayı sızmaz.

## §7 AuthenticationMiddleware — public route + Bearer

```php
// Dosya: shared\src\Api\Middleware\AuthenticationMiddleware.php / Satır: 33-38
    public const PUBLIC_ROUTE_PATTERNS = [
        '/api/v1/auth/login',
        '/api/v1/auth/register',
        '/api/v1/auth/forgot-password',
        '/api/v1/public',
    ];
```

```php
// Dosya: shared\src\Api\Middleware\AuthenticationMiddleware.php / Satır: 83-100
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ') && $this->jwt !== null) {
            $claims = $this->jwt->validate(substr($authHeader, 7));

            if ($claims !== null) {
                $revoked = $this->accessTokenValidator !== null
                    && !($this->accessTokenValidator)((string) $claims['jti']);
                if (!$revoked) {
                    $request['_auth_user'] = [
                        'id'           => (string) $claims['sub'],
                        'authenticated' => true,
                        'method'       => 'jwt',
                    ];
                    return $next($request);
                }
                // revocation edilmiş jti → 401'e düşer (fail-closed)
            }
        }
```

## §8 PermissionMiddleware — RBAC kararı

```php
// Dosya: shared\src\Middleware\PermissionMiddleware.php / Satır: 27-45
        $userRole = $auth['role'] ?? 'user';

        // Role kontrolü
        $requiredRole = $routeMeta['requiredRole'] ?? null;
        if ($requiredRole !== null && $userRole !== $requiredRole) {
            return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
        }

        // Permission kontrolü
        $requiredPermission = $routeMeta['requiredPermission'] ?? null;
        if ($requiredPermission !== null) {
            $userPermissions = $auth['permissions'] ?? [];
            if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
                return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
            }
        }

        return $next($request);
```

`in_array(..., true)` strict eşleşme — string/int karışması tip zehirlenmesini engeller.

## §9 AuthGuard — check zinciri

```php
// Dosya: shared\src\PageRouter\AuthGuard.php / Satır: 15-48
    public function check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array
    {
        return $this->checkAuthRequired($uri, $route, $isSpaRequest)
            ?? $this->checkRole($uri, $route)
            ?? $this->checkPermission($uri, $route)
            ?? $this->checkAuthRedirectRoute($uri, $isSpaRequest)
            ?? $this->checkAuthenticatedUserOnAuthPage($uri, $isSpaRequest)
            ?? $this->checkLogout($uri, $isSpaRequest)
            ?? null;
    }
```

Null-coalescing zinciri: ilk `null` dönmeyen adım kararı verir; `null` = "bu guard bu istekle ilgilenmiyor".

## §10 OAuthManager — state (tek kullanımlık)

```php
// Dosya: shared\src\OAuth\OAuthManager.php / Satır: 155-183
    public function saveState(string $state, string $provider, int $userId, ?string $codeVerifier = null): void
    {
        $this->repository->insertState([
            'state' => hash('sha256', $state),
            'provider' => $provider,
            'user_id' => $userId,
            'code_verifier' => $codeVerifier,
        ]);
    }

    public function validateState(string $state, string $provider, int $userId): ?array
    {
        $hash = hash('sha256', $state);

        $row = $this->repository->findValidState($hash, $provider, $userId);
        if ($row === null) {
            return null;
        }

        // State'i sil (tek kullanımlık)
        $this->repository->deleteState($hash);

        return ['code_verifier' => $row['code_verifier'] ?? null];
    }
```

## §11 OAuthManager — AES-256-GCM encrypt

```php
// Dosya: shared\src\OAuth\OAuthManager.php / Satır: 185-205
    private function encrypt(string $plaintext): string
    {
        $iv = random_bytes(12);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        return base64_encode($iv . $tag . $ciphertext);
    }
```

IV=12 byte (GCM standardı), tag=16 byte (128-bit authentication tag); `iv.tag.ciphertext` birleşimi base64 ile saklanır.

## §12 OAuthRepository — upsert + state TTL

```php
// Dosya: shared\src\OAuth\OAuthRepository.php / Satır: 27-44
        $sql = 'INSERT INTO oauth_connections
                    (user_id, provider, provider_user_id, provider_username,
                     access_token_encrypted, refresh_token_encrypted,
                     token_expires_at, scopes, profile_data)
                 VALUES
                    (:user_id, :provider, :provider_user_id, :provider_username,
                     :access_token, :refresh_token,
                     :expires_at, :scopes, :profile_data)
                 ON DUPLICATE KEY UPDATE
                     access_token_encrypted = VALUES(access_token_encrypted),
                     refresh_token_encrypted = VALUES(refresh_token_encrypted),
                     token_expires_at = VALUES(token_expires_at),
                     scopes = VALUES(scopes),
                     profile_data = VALUES(profile_data),
                     is_active = 1,
                     updated_at = CURRENT_TIMESTAMP';
```

```php
// Dosya: shared\src\OAuth\OAuthRepository.php / Satır: 85-92
        $sql = 'INSERT INTO oauth_states (state_token, provider, user_id, code_verifier, expires_at)
                 VALUES (:state, :provider, :user_id, :code_verifier, DATE_ADD(NOW(), INTERVAL 10 MINUTE))';
```

## §13 ApiSessionManager — stateless köprü

```php
// Dosya: shared\src\Api\Auth\ApiSessionManager.php / Satır: 47-70
    public function getUserId(): ?string
    {
        if ($this->userId !== null) {
            return $this->userId;
        }
        $user = $this->read('user');

        if (isset($user['id'])) {
            return (string) $user['id'];
        }

        // Web oturumu (auth.coremusic.net SessionManager) MM_* anahtarları yazar;
        // aynı cookie/paylaşılan save path ile API de o oturumu tanımalı (Faz 1b).
        $legacy = $this->read('MM_UserID');

        return (is_string($legacy) && $legacy !== '') ? $legacy : null;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated
            || $this->read('user') !== null
            || $this->getUserId() !== null;
    }
```

`MM_UserID` fallback'i cross-domain SSO köprüsünün somut kanıtıdır: `auth.coremusic.net` oturumu `MM_*` anahtarlarını yazar, API bunu okur.

## §14 AuthRouteConfig — SSO redirect

```php
// Dosya: shared\src\Config\AuthRouteConfig.php / Satır: 47-70 (kısaltılmış — bkz kanıt paketi B8)
    public static function buildAuthRedirectUrl(
        string $route,
        string $returnUrl = '',
        string $authDomain = '',
        string $callbackDomain = '',
        ?DomainConfig $domain = null,
    ): string {
        if ($authDomain === '') {
            $authDomain = self::getAuthUrl('http', $domain);
        }
        if ($callbackDomain === '') {
            $callbackDomain = self::getHomeUrl('http', $domain);
        }
        $callbackUrl = $callbackDomain . '/auth/callback';
        $params = [
            'client_id'     => self::CLIENT_ID,
            'response_type' => 'session',
            'redirect_uri'  => $returnUrl !== ''
                ? $callbackUrl . '?return=' . urlencode($returnUrl)
                : $callbackUrl,
        ];

        return $authDomain . '/' . $route . '?' . http_build_query($params);
    }
```

`response_type='session'` OIDC/OAuth2 standart değerlerinden (`code`, `id_token`, `token`) farklıdır → özel SSO protokolü (`⚠️ VERIFICATION REQUIRED`, ADR-097'ye devredildi).

## §15 Parola hashleme kapıları (PHP `password` uzantısı çapraz kontrolü)

| # | Kapı | Standart karşılığı | Repo karşılığı | Durum |
|---|---|---|---|---|
| P1 | Parola `password_hash()` ile mi üretiliyor | `password_hash()` / `PASSWORD_ARGON2ID` | bu pakette okunmadı | ⚠️ VERIFICATION REQUIRED |
| P2 | Kaldırma zamanında `password_needs_rehash()` var mı | salt/maliyet değişikliği | bu pakette okunmadı | ⚠️ VERIFICATION REQUIRED |
| P3 | Hash düz metniyle eşleştiriliyor mu (timing-safe) | `password_verify()` | bu pakette okunmadı | ⚠️ VERIFICATION REQUIRED |
| P4 | Argon2id parametreleri (memory · time · threads) yazılı mı | PHP 7.2+ `PASSWORD_ARGON2ID` | — | UNKNOWN — okunmadı |
| P5 | `password_hash()` asgari sürümü | PHP 7.2+ (Argon2id) | repo PHP 8.4 hedefi | ⚠️ VERIFICATION REQUIRED — composer.json okunmadı |

**Standart künyesi:** PHP `password` uzantısı (`book.password.php`) — parola hashleme, doğrulama ve yeniden-hashleme kapılarını bu uzantı belirler. CoreMusic'te `K009-security` bu kapıların sahibidir; bu dosya yalnız kod-paketiyle eşlemeyi taşır ve kapıyı kendisi **kapatmaz**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.** — P1-P5 hiçbiri repo'da okunmadı; eklenen tablo yalnız kapı TANIMIDIR, dosyanın doğrulanmış kod iddiası sayısı (24/24, `validation:` satırı) aynı kalır.

## §16 Kod paketi ↔ kapı eşleme özeti (§9-§14)

| Bölüm | Kod nesnesi | Bağlı kapı | Bu dosyadaki durum |
|---|---|---|---|
| §9 | AuthGuard check zinciri | session içinde otorite zinciri | kod alıntısı mevcut (B-paket) |
| §10 | OAuthManager state (tek kullanımlık) | tekrar kullanım reddi | kod alıntısı mevcut |
| §11 | OAuthManager AES-256-GCM | AEAD nonce/etiket doğrulaması | kod alıntısı mevcut |
| §12 | OAuthRepository upsert + TTL | state TTL kapısı | kod alıntısı mevcut |
| §13 | ApiSessionManager stateless köprü | imza doğrulama (JwtService) | kod alıntısı mevcut |
| §14 | AuthRouteConfig SSO redirect | `response_type='session'` standart dışı (§D.5 Q3) | ADR-097'ye devredildi |
| §15 | parola hashleme | P1-P5 | **okunmadı — açık** |

**Eşleme kuralı:** Bir satır yalnız iki durumda kapanır — (1) kod alıntısı `Dosya:/Satır:` künyesiyle dosyada ve (2) bağlı kapısı kendi dosyasında `kapalı`. §15 satırı (1)'i karşılamadığı için açık kalır.

## §17 Red Team gözden geçirmesi (bu ek)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "P kapıları eklendi" | 2026-10-13 HEAD-200 kaydı var (§18) | kabul |
| 2 | "parola kodu doğrulandı" | okunmadı | ❌ iddia reddedildi — `⚠️` taşındı |
| 3 | "24/24 kod iddiası değişti" | §9-§14'e dokunulmadı | kabul — sayı aynı |
| 4 | "§16 yeni kanıt" | yalnız mevcut kanıtların eşlemesi | kabul — yeni iddia yok |
| 5 | "envanter kesişimi 0" | tek URL için sayım yapıldı | kabul (0) |

## §18 Web Kaynakları (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Parola hashleme, doğrulama ve yeniden-hashleme kapıları PHP `password` uzantısında tanımlıdır | https://www.php.net/manual/en/book.password.php | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |

## §19 Ölçüm kapanışı (bu ek)

| Ölçüm | Hedef | Sonuç |
|---|---|---|
| Eklenen kapı (P1-P5) | 5 | §15 tablosunda yazılı |
| Eklenen standart çaprazı | 1 | PHP `password` uzantısı |
| Yeni URL | 1 | 2026-10-13 HEAD 200 · kesişim 0 |
| Değişen kod iddiası | 0 | §9-§14'e dokunulmadı |
| `**Authority:**` son satır mı | evet | satır karşılaştırma |

Altı ölçüm de sayısal olarak yazılır; "yeterli" gibi niteliksel değer kullanılmaz. §15 kapıları açık kaldığı sürece dosya `status: verified` iddiasını bu kapılar için genişletmez.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
