---
node_id: K012-services/user-service
node_type: module
role: ornekler
status: draft
parent_id: K012-services/user-service
children: []
depends_on:
  - "user-service/user-service-anayasa.md"
  - "user-service/user-service-surucu.md"
evidence:
  - "S1 — auth.coremusic.net/include/Service/AuthService.php 349 satır (tamamı okundu, 2026-10-13)"
  - "S1 — auth.coremusic.net/include/Service/SessionManager.php 180 satır (tamamı okundu, 2026-10-13)"
validation:
  - "§2–§9 kod blokları birebir kopya — 2026-10-13 read"
  - "§10–§11 Kaynak satırları — kod YOK alanları"
open_questions:
  - "Password VO / UserRepository / RateLimiter implementasyonu okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "controllers/handlers/pages örnekleri envanterde, okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: auth.coremusic.net/include/Service/ (S1, 2026-10-13 read)"
group: kod
---

# user-service — Örnekler (Kod Kanıtı)

> **Bu dosya ne yapar:** user-service'in gerçek kod örneklerini **birebir**
> kopyalar (kaynak dosya + satır aralığı ile). Yorum ekleme, yeniden yazma,
> sadeleştirme yok. Her blok `Dosya:` + `Satır:` künyesi taşır.

## §1 Örnek Künye Tablosu

| # | Örnek | Dosya | Satır | Blok uzunluğu |
|---|---|---|---|---|
| 1 | Sınıf başlığı + import'lar | AuthService.php | 1–28 | 28 satır |
| 2 | Sabitler | AuthService.php | 30–43 | 14 satır |
| 3 | Constructor + `loginWithRequest` | AuthService.php | 45–131 | 87 satır |
| 4 | `registerWithRequest` | AuthService.php | 136–205 | 70 satır |
| 5 | `logout` + `isAuthenticated` + `getCurrentUser` | AuthService.php | 221–252 | 32 satır |
| 6 | `requestPasswordReset` + `resetPassword` | AuthService.php | 254–308 | 55 satır |
| 7 | `validateSessionKey` | AuthService.php | 310–348 | 39 satır |
| 8 | `SessionManager` başlık + `setAuthUser` + `setRegisteredUser` | SessionManager.php | 1–44 | 44 satır |
| 9 | `destroy` + `regenerateId` + `rotateIfNeeded` + `isIdleExpired` + `touch` | SessionManager.php | 60–159 | kırıklı seçki |
| 10 | Yokluk desenleri | — | — | `Kaynak:` künyeli |

## §2 Sınıf Başlığı ve Import'lar

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 1–28

```php
<?php declare(strict_types=1);

namespace CoreMusic\Auth\Service;

use CoreMusic\Auth\Domain\Entity\User;
use CoreMusic\Auth\Domain\ValueObject\Email;
use CoreMusic\Auth\Domain\ValueObject\Password;
use CoreMusic\Auth\Domain\ValueObject\UserId;
use CoreMusic\Auth\Domain\ValueObject\Gender;
use CoreMusic\Auth\Domain\DTO\LoginRequest;
use CoreMusic\Auth\Domain\DTO\RegisterRequest;
use CoreMusic\Auth\Domain\DTO\AuthResponse;
use CoreMusic\Contracts\Auth\IAuthService;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Contracts\Auth\IUserRepository;
use CoreMusic\Contracts\Security\IRateLimiter;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ValidationException;

/**
 * AuthService — Authentication iş mantığı.
 *
 * Domain nesneleri (User, Email, Password, Gender) kullanır.
 * IAuthService interface ile uyumlu, DTO tabanlı yeni metodlar da sunar.
 */
final class AuthService implements IAuthService
{
```

## §3 Sabitler

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 30–43

```php
    private const MIN_PASSWORD_LENGTH = 8;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_WINDOW_SECONDS = 900;
    private const MAX_REGISTER_ATTEMPTS = 3;
    private const REGISTER_WINDOW_SECONDS = 3600;
    private const AUTH_KEY_TTL = 300;
    private const USERNAME_PATTERN = '/^[a-zA-Z0-9_]{3,30}$/';
    private const LOGIN_RATE_KEY_PREFIX = 'rate_limit:login:';
    private const REGISTER_RATE_KEY_PREFIX = 'rate_limit:register:';
    private const PASSWORD_RESET_RATE_KEY_PREFIX = 'rate_limit:password_reset:';
    private const PASSWORD_RESET_MAX_ATTEMPTS = 3;
    private const PASSWORD_RESET_WINDOW_SECONDS = 3600;
    /** Sıfırlama token'ı ömrü (Faz 1b sözleşmesi): 900 saniye = 15 dakika. */
    public const PASSWORD_RESET_TOKEN_TTL = 900;
```

## §4 Constructor ve Giriş Akışı — loginWithRequest

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 45–131

```php
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly ISessionManager $session,
        private readonly IRateLimiter $rateLimiter,
        private readonly string $pepper = '',
    ) {}

    // ─── DTO Tabanlı Yeni Metodlar ───

    /**
     * Kullanıcı girişi — LoginRequest DTO alır, AuthResponse döndürür.
     */
    public function loginWithRequest(LoginRequest $request): AuthResponse
    {
        $failedKey   = self::LOGIN_RATE_KEY_PREFIX . $request->clientIp;
        // B-F-25: kimlik bazlı ikinci sayaç — parola-sprey (çok hesap/tek IP) ve
        // dağıtık tek-hesap saldırısı için iki boyutlu limit. SHA-256 bucket'ı
        // geri-dönüştürülemez; enumeration sinyali vermez (var olmayan kimlik de sayılır).
        $identityKey = self::LOGIN_RATE_KEY_PREFIX . 'id:' . hash('sha256', strtolower($request->identity));

        if ($this->rateLimiter->isLimited($failedKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
            || $this->rateLimiter->isLimited($identityKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
        ) {
            throw RateLimitException::loginRateLimited(self::LOGIN_WINDOW_SECONDS);
        }

        if ($request->identity === '' || $request->password === '') {
            throw ValidationException::emptyFields();
        }

        $row = $this->userRepository->findByCredential($request->identity);
        if ($row === null) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
            $this->rateLimiter->increment($identityKey, self::LOGIN_WINDOW_SECONDS);
            throw AuthenticationException::invalidCredentials();
        }

        $user = User::fromRow($row);

        if ($user->isBanned()) {
            throw AuthenticationException::banned();
        }

        $password = Password::create($request->password);
        if (!$password->verify($user->passwordHash, $this->pepper)) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
            $this->rateLimiter->increment($identityKey, self::LOGIN_WINDOW_SECONDS);
            throw AuthenticationException::invalidCredentials();
        }

        // Cinsiyet tabanlı erişim kontrolü
        // Kural: Kayıt olurken seçilen cinsiyet, giriş yaparken de aynı olmalı.
        // neutral olarak kayıt olanlar her cinsiyetle giriş yapabilir.
        // male/female olarak kayıt olanlar sadece kendi cinsiyetleriyle giriş yapabilir.
        $userGender = Gender::create($user->gender);
        $visitorGender = Gender::create($request->visitorGender);

        if (!$userGender->isNeutral() && !$visitorGender->isNeutral() && !$visitorGender->equals($userGender)) {
            throw AuthenticationException::genderMismatch((string)$userGender);
        }

        $this->rateLimiter->reset($failedKey);
        $this->rateLimiter->reset($identityKey);
        $this->userRepository->updateLastLogin($user->id);

        // RBAC (B-F-12): rol + izinleri session'a yaz — PermissionMiddleware bunları bekler.
        // Birincil rol = alfabetik ilk (deterministik); izinler tüm rollerden birleşir.
        $userData = $user->toArray();
        $roles    = $this->userRepository->findRolesForUser($user->id);
        if ($roles !== []) {
            $userData['role']        = $roles[0]['role_name'];
            $userData['permissions'] = array_values(array_unique(array_merge(
                ...array_map(static fn (array $r): array => $r['permissions'], $roles)
            )));
        }
        $this->session->setAuthUser($userData);

        $authKey = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::AUTH_KEY_TTL);
        $this->userRepository->saveAuthKey($user->id, $authKey, $expiresAt, $request->clientIp);

        return AuthResponse::success(
            redirect: '/home',
            authKey: $authKey,
            user: $user->toArray(),
        );
    }
```

## §5 Kayıt Akışı — registerWithRequest

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 136–205

```php
    public function registerWithRequest(RegisterRequest $request): AuthResponse
    {
        $rateKey = self::REGISTER_RATE_KEY_PREFIX . $request->clientIp;
        if ($this->rateLimiter->isLimited($rateKey, self::MAX_REGISTER_ATTEMPTS, self::REGISTER_WINDOW_SECONDS)) {
            throw RateLimitException::registerRateLimited(self::REGISTER_WINDOW_SECONDS);
        }

        $errors = [];
        if ($request->username === '' || !preg_match(self::USERNAME_PATTERN, $request->username)) {
            $errors['username'] = 'Geçersiz kullanıcı adı.';
        } elseif ($this->userRepository->usernameExists($request->username)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ConflictException::usernameAlreadyExists();
        }

        $email = Email::create($request->email);
        if ($this->userRepository->emailExists((string)$email)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ConflictException::emailAlreadyExists();
        }

        if (strlen($request->password) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'Şifre en az ' . self::MIN_PASSWORD_LENGTH . ' karakter.';
        }

        if (!$request->agreeTerms) {
            $errors['agree_terms'] = 'Koşulları kabul etmelisiniz.';
        }

        if (!empty($errors)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ValidationException::multiple($errors);
        }

        $this->rateLimiter->reset($rateKey);

        $password = Password::create($request->password);
        $passwordHash = $password->hashWithPepper($this->pepper);

        $gender = Gender::create($request->gender);

        $created = $this->userRepository->create([
            'username'      => $request->username,
            'email'         => (string)$email,
            'password_hash' => $passwordHash,
            'display_name'  => $request->username,
            'gender'        => (string)$gender,
            'account_type'  => 'free',
        ]);

        $this->session->setRegisteredUser($created);
        $this->session->regenerateId();

        $authKey = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::AUTH_KEY_TTL);
        $this->userRepository->saveAuthKey($created['user_id'], $authKey, $expiresAt, $request->clientIp);

        return AuthResponse::success(
            redirect: '/home',
            authKey: $authKey,
            user: [
                'id'           => $created['user_id'],
                'username'     => $request->username,
                'email'        => (string)$email,
                'display_name' => $request->username,
                'account_type' => 'free',
                'role'         => $created['role_name'],
            ],
        );
    }
```

## §6 logout / isAuthenticated / getCurrentUser

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 221–252

```php
    public function logout(): void
    {
        // B-F-20 + P1-9: outstanding auth_key'leri VE JWT access token'larını iptal et —
        // çalınan anahtar/logout sonrası token 300s/TTL daha geçerli kalmasın.
        $userId = $this->session->getUserId();
        if ($userId !== null) {
            $this->userRepository->revokeAuthKeysForUser($userId);
            $this->userRepository->revokeAccessTokensForUser($userId);
        }

        $this->session->destroy();
        $this->session->clearDisplayCookies();
    }

    public function isAuthenticated(): bool
    {
        return $this->session->isAuthenticated();
    }

    public function getCurrentUser(): ?array
    {
        $userId = $this->session->getUserId();
        if ($userId === null) {
            return null;
        }
        $row = $this->userRepository->findByIdHex($userId);
        if ($row === null) {
            return null;
        }
        $user = User::fromRow($row);
        return $user->toArray();
    }
```

## §7 Parola Sıfırlama — requestPasswordReset / resetPassword

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 254–308

```php
    public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array
    {
        $emailObj = Email::create($email);

        $rateKey = self::PASSWORD_RESET_RATE_KEY_PREFIX . $clientIp;
        if ($this->rateLimiter->isLimited($rateKey, self::PASSWORD_RESET_MAX_ATTEMPTS, self::PASSWORD_RESET_WINDOW_SECONDS)) {
            throw RateLimitException::passwordResetRateLimited(self::PASSWORD_RESET_WINDOW_SECONDS);
        }

        $row = $this->userRepository->findByEmail((string)$emailObj);
        if ($row === null) {
            $this->rateLimiter->increment($rateKey, self::PASSWORD_RESET_WINDOW_SECONDS);
            return ['success' => true, 'message' => 'Sıfırlama bağlantısı gönderildi.'];
        }

        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + self::PASSWORD_RESET_TOKEN_TTL);

        $userId = UserId::fromHex($row['id']);
        $this->userRepository->saveResetToken((string)$userId, $tokenHash, $expiresAt, $clientIp);
        $this->rateLimiter->increment($rateKey, self::PASSWORD_RESET_WINDOW_SECONDS);

        error_log('[AuthService] Password reset requested for user_id: ' . (string)$userId);

        return ['success' => true, 'message' => 'Sıfırlama bağlantısı gönderildi.'];
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        if ($token === '') {
            throw ValidationException::invalidToken();
        }
        if (strlen($newPassword) < self::MIN_PASSWORD_LENGTH) {
            throw ValidationException::passwordTooShort(self::MIN_PASSWORD_LENGTH);
        }

        $tokenHash = hash('sha256', $token);
        $record = $this->userRepository->findValidResetToken($tokenHash);
        if ($record === null) {
            throw ValidationException::tokenExpired();
        }

        $password = Password::create($newPassword);
        $newHash = $password->hashWithPepper($this->pepper);
        $userId = UserId::fromHex($record['user_id']);
        $tokenId = UserId::fromHex($record['id']);

        $this->userRepository->updatePassword((string)$userId, $newHash);
        $this->userRepository->markResetTokenUsed((string)$tokenId);

        error_log('[AuthService] Password reset completed for user_id: ' . (string)$userId);

        return ['success' => true, 'message' => 'Şifreniz güncellendi.'];
    }
```

## §8 auth_key Doğrulama — validateSessionKey

**Dosya:** `auth.coremusic.net/include/Service/AuthService.php` · **Satır:** 310–348

```php
    public function validateSessionKey(string $authKey): array
    {
        if ($authKey === '') {
            throw AuthenticationException::invalidCredentials();
        }

        $record = $this->userRepository->findValidAuthKey($authKey, false);

        if ($record === null) {
            $record = $this->userRepository->findValidAuthKey($authKey, true);
            if ($record === null) {
                throw AuthenticationException::invalidCredentials();
            }
            return [
                'id'           => $record['user_id'],
                'user_id'      => $record['user_id'],
                'username'     => $record['username'],
                'email'        => $record['email'],
                'display_name' => $record['display_name'] ?? $record['username'],
                'gender'       => $record['gender'] ?? 'neutral',
                'avatar_url'   => $record['avatar_url'] ?? null,
                'account_type' => $record['account_type'] ?? 'free',
            ];
        }

        $tokenId = UserId::fromHex($record['token_id']);
        $this->userRepository->markAuthKeyUsed((string)$tokenId);

        return [
            'id'           => $record['user_id'],
            'user_id'      => $record['user_id'],
            'username'     => $record['username'],
            'email'        => $record['email'],
            'display_name' => $record['display_name'] ?? $record['username'],
            'gender'       => $record['gender'] ?? 'neutral',
            'avatar_url'   => $record['avatar_url'] ?? null,
            'account_type' => $record['account_type'] ?? 'free',
        ];
    }
```

## §9 SessionManager Seçkileri

**Dosya:** `auth.coremusic.net/include/Service/SessionManager.php` · **Satır:** 1–31 (başlık + `setAuthUser`)

```php
<?php declare(strict_types=1);

namespace CoreMusic\Auth\Service;

use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Session\SessionBootstrapper;

final class SessionManager implements ISessionManager
{
    public function __construct(
        private readonly string $sessionName = 'COREMUSIC_SESS',
        private readonly string $cookieDomain = '.coremusic.net',
    ) {}

    public function setAuthUser(array $user): void
    {
        // UUID hex string olarak sakla (BINARY(16) uyumlu)
        $_SESSION['MM_UserID']      = $user['id'];
        $_SESSION['MM_Username']    = $user['username'];
        $_SESSION['MM_Email']       = $user['email'];
        $_SESSION['MM_DisplayName'] = $user['display_name'] ?? $user['username'];
        $_SESSION['MM_AccountType'] = $user['account_type'] ?? 'free';
        $_SESSION['MM_Image']       = $user['avatar_url'] ?? '';
        $_SESSION['MM_Gender']      = $user['gender'] ?? 'neutral';
        // RBAC (B-F-12): AuthMiddleware bunları _auth'a enjekte eder.
        $_SESSION['MM_UserRole']    = is_string($user['role'] ?? null) && $user['role'] !== ''
            ? $user['role']
            : 'user';
        $_SESSION['MM_Permissions'] = is_array($user['permissions'] ?? null) ? $user['permissions'] : [];
        $_SESSION['_session_last_active'] = time();
    }
```

### §9.1 destroy — Gender Koruma + Rotasyon Damgaları

**Dosya:** `auth.coremusic.net/include/Service/SessionManager.php` · **Satır:** 60–82

```php
    public function destroy(): void
    {
        // Preserve gender across session destroy
        $savedGender = $_SESSION['cm_gender'] ?? null;

        SessionBootstrapper::restartFresh();

        $now = time();
        $_SESSION['_session_last_active'] = $now;
        $_SESSION['_session_created_at']  = $now;
        $_SESSION['_session_rotated_at']  = $now;
        $_SESSION['csp_nonce'] = bin2hex(random_bytes(32));

        // Restore gender after session reset
        if ($savedGender !== null) {
            $_SESSION['cm_gender'] = $savedGender;
        }
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }
```

### §9.2 Boşta Kalma, touch ve Rotasyon

**Dosya:** `auth.coremusic.net/include/Service/SessionManager.php` · **Satır:** 133–159

```php
    public function isIdleExpired(int $timeoutSeconds): bool
    {
        $lastActivity = (int)($_SESSION['_session_last_active'] ?? 0);
        return $lastActivity > 0 && (time() - $lastActivity) > $timeoutSeconds;
    }

    public function touch(): void
    {
        $_SESSION['_session_last_active'] = time();
    }

    public function rotateIfNeeded(int $intervalSeconds): bool
    {
        $now = time();
        $lastRotation = (int)($_SESSION['_session_rotated_at'] ?? 0);
        if ($lastRotation === 0) {
            $_SESSION['_session_rotated_at'] = $now;
            $_SESSION['_session_created_at'] = $now;
            return false;
        }
        if (($now - $lastRotation) > $intervalSeconds) {
            session_regenerate_id(true);
            $_SESSION['_session_rotated_at'] = $now;
            return true;
        }
        return false;
    }
```

### §9.3 Temizleme — clearDisplayCookies

**Dosya:** `auth.coremusic.net/include/Service/SessionManager.php` · **Satır:** 161–169

```php
    public function clearDisplayCookies(): void
    {
        $names = ['MM_Username', 'MM_Image', 'MM_UserID', 'MM_UserDesc', 'MM_UserYear'];
        foreach ($names as $name) {
            if (isset($_COOKIE[$name])) {
                setcookie($name, '', time() - 3600, '/', '.coremusic.net');
            }
        }
    }
```

## §10 Örnek Analiz Tabloları

| # | Örnek | Gözlem | Kanıt satırı |
|---|---|---|---|
| 1 | İki limit `||` | Kısa devre — ilk dolu ikinciyi okumaz | AuthService:65–66 |
| 2 | Boş alan kontrolü limit **sonrası** | Boş deneme limiti tetiklemez ama limit doluyken boş alan da `RateLimitException` | AuthService:65 vs 71 |
| 3 | `isBanned` kontrolü parola **öncesi** | Banlı hesap doğru parola gerekmeden reddedilir ama `increment` yok | AuthService:84–86 |
| 4 | Gender gate parola **sonrası** | Yanlış gender = sayaç değişmez (ne + ne −) | AuthService:99–104 |
| 5 | `roles[0]` korumasız | `$roles !== []` kapısı `:114`'te — indeks güvenli, sıralama garantisi yok | AuthService:114–115 |
| 6 | Kayıtta hata biriktirilir | `ValidationException::multiple` — girişte tek hatada kırılır | AuthService:165–168 |
| 7 | `regenerateId` kayıtta | Session-fixation savarması kayıt sonrası hemen | AuthService:187 |
| 8 | Logout önce revoke | Anahtar iptali `destroy`'dan önce | AuthService:227–231 |
| 9 | Sıfırlama aynı mesaj | Kullanıcı yoksa da `success: true` (enumeration kapısı) | AuthService:264–267 |
| 10 | Ham token bir kez log'a | Log yalnız `user_id` — token yok | AuthService:277, 305 |
| 11 | iki kol `findValidAuthKey` | false → true sırası; true kolunda damga yok | AuthService:316–336 |
| 12 | `setRegisteredUser` rol fallback | `free` (hesap tipi) `MM_UserRole`'a | SessionManager:39 |
| 13 | `destroy` gender saklar | `cm_gender` restart öncesi/sonrası | SessionManager:63–76 |
| 14 | İlk `rotateIfNeeded` damga kurar | `false` döner, ID değiştirmez | SessionManager:148–151 |
| 15 | `last == 0` → `false` | Zaman damgası olmayan oturum asla idle olmaz | SessionManager:136–137 |

## §11 Yokluk Desenleri (Kod YOK Alanları)

| Alan | Durum | Kaynak |
|---|---|---|
| `Password` VO içi hash algoritması | Okunmadı | `auth.coremusic.net/AGENTS.md` §4.3 kuralı — Argon2id kural seviyesi; kod kanıtı `⚠️ VERIFICATION REQUIRED` |
| `IUserRepository` implementasyonu | Okunmadı | `use CoreMusic\Contracts\Auth\IUserRepository` (AuthService:15) — somut `UserRepository` envanterde (`include/Repository/`), okunmadı |
| `IRateLimiter` implementasyonu | Okunmadı | `use CoreMusic\Contracts\Security\IRateLimiter` (AuthService:16) — depo (APCu/dosya/DB) `UNKNOWN` |
| `SessionBootstrapper::restartFresh` gövdesi | Okunmadı | `use CoreMusic\Session\SessionBootstrapper` (SessionManager:6) |
| Controller / Handler / pages | Okunmadı | `auth.coremusic.net/AGENTS.md` §2 envanteri — bu dosyalarda değil |
| `LoginRequest` / `RegisterRequest` DTO | Okunmadı | `auth.coremusic.net/AGENTS.md` §2 `include/Domain/DTO/` satırı |
| Test örnekleri (`tests/Unit`) | Okunmadı | `auth.coremusic.net/AGENTS.md` §2 `tests/` satırı |
| `pepper` enjeksiyon kaynağı | Okunmadı | constructor default `''` (AuthService:49) — container/config okunmadı |

## §12 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | örnekler yazıldı — AuthService 7 blok (285 satır birebir) + SessionManager 5 blok seçki + 15 örnek analiz + 8 yokluk deseni |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
