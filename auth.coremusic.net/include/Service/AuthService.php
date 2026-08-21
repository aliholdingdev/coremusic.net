<?php declare(strict_types=1);

namespace CoreMusic\Auth\Service;

use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Interfaces\Auth\IUserRepository;
use CoreMusic\Interfaces\Security\IRateLimiter;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ServerException;
use CoreMusic\Exception\ValidationException;

final class AuthService implements IAuthService
{
    private const MIN_PASSWORD_LENGTH = 8;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_WINDOW_SECONDS = 900;
    private const MAX_REGISTER_ATTEMPTS = 3;
    private const REGISTER_WINDOW_SECONDS = 3600;
    private const AUTH_KEY_TTL = 300;
    private const ARGON2_OPTIONS = ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2];
    private const ALLOWED_GENDERS = ['male', 'female', 'neutral'];
    private const USERNAME_PATTERN = '/^[a-zA-Z0-9_]{3,30}$/';
    private const LOGIN_RATE_KEY_PREFIX = 'rate_limit:login:';
    private const REGISTER_RATE_KEY_PREFIX = 'rate_limit:register:';
    private const PASSWORD_RESET_RATE_KEY_PREFIX = 'rate_limit:password_reset:';
    private const PASSWORD_RESET_MAX_ATTEMPTS = 3;
    private const PASSWORD_RESET_WINDOW_SECONDS = 3600;

    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly ISessionManager $session,
        private readonly IRateLimiter $rateLimiter,
        private readonly string $pepper = '',
    ) {}

    private function pepperPassword(string $password): string
    {
        if ($this->pepper === '') {
            throw ServerException::configError('APP_PEPPER');
        }
        return hash_hmac('sha256', $password, $this->pepper);
    }

    public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array
    {
        $identity = trim($identity);
        $failedKey = self::LOGIN_RATE_KEY_PREFIX . $clientIp;

        if ($this->rateLimiter->isLimited($failedKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)) {
            throw RateLimitException::loginRateLimited(self::LOGIN_WINDOW_SECONDS);
        }

        if ($identity === '' || $password === '') {
            throw ValidationException::emptyFields();
        }

        $user = $this->userRepository->findByCredential($identity);
        if ($user === null) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
            throw AuthenticationException::invalidCredentials();
        }

        $pepperedPassword = $this->pepperPassword($password);
        if (!password_verify($pepperedPassword, $user['password_hash'])) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
            throw AuthenticationException::invalidCredentials();
        }

        if (!empty($user['is_banned'])) {
            throw AuthenticationException::banned();
        }

        // Cinsiyet tabanlı erişim kontrolü
        $allowedGender = strtolower($visitorGender);
        if ($allowedGender !== 'neutral' && isset($user['gender']) && $user['gender'] !== $allowedGender) {
            throw AuthenticationException::genderMismatch($allowedGender);
        }

        $this->rateLimiter->reset($failedKey);
        $this->userRepository->updateLastLogin($user['id']);

        $this->session->setAuthUser([
            'id'           => $user['id'],
            'username'     => $user['username'],
            'email'        => $user['email'],
            'display_name' => $user['display_name'] ?? $user['username'],
            'account_type' => $user['account_type'] ?? 'free',
            'avatar_url'   => $user['avatar_url'] ?? '',
            'gender'       => $user['gender'] ?? 'neutral',
        ]);

        $authKey = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::AUTH_KEY_TTL);
        $this->userRepository->saveAuthKey($user['id'], $authKey, $expiresAt, $clientIp);

        return [
            'success'  => true,
            'redirect' => '/home',
            'auth_key' => $authKey,
            'user'     => [
                'id'           => $user['id'],
                'username'     => $user['username'],
                'email'        => $user['email'],
                'display_name' => $user['display_name'],
                'account_type' => $user['account_type'],
            ],
        ];
    }

    public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array
    {
        $rateKey = self::REGISTER_RATE_KEY_PREFIX . $clientIp;
        if ($this->rateLimiter->isLimited($rateKey, self::MAX_REGISTER_ATTEMPTS, self::REGISTER_WINDOW_SECONDS)) {
            throw RateLimitException::registerRateLimited(self::REGISTER_WINDOW_SECONDS);
        }

        $username   = trim($data['username'] ?? '');
        $email      = trim($data['email'] ?? '');
        $password   = $data['password'] ?? '';
        $agreeTerms = !empty($data['agree_terms']);
        // Kayıt formu gender göndermiyorsa (register sayfasında gender seçimi yok),
        // session'daki ziyaretçi cinsiyetini kullan (select-gender sayfasından gelir)
        $gender = match ($data['gender'] ?? $visitorGender) {
            'male', 'female' => $data['gender'] ?? $visitorGender,
            default          => 'neutral',
        };

        $errors = [];
        if ($username === '' || !preg_match(self::USERNAME_PATTERN, $username)) {
            $errors['username'] = 'Geçersiz kullanıcı adı.';
        } elseif ($this->userRepository->usernameExists($username)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ConflictException::usernameAlreadyExists();
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Geçerli e-posta girin.';
        } elseif ($this->userRepository->emailExists($email)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ConflictException::emailAlreadyExists();
        }

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'Şifre en az ' . self::MIN_PASSWORD_LENGTH . ' karakter.';
        }

        if (!$agreeTerms) {
            $errors['agree_terms'] = 'Koşulları kabul etmelisiniz.';
        }

        if (!empty($errors)) {
            $this->rateLimiter->increment($rateKey, self::REGISTER_WINDOW_SECONDS);
            throw ValidationException::multiple($errors);
        }

        $this->rateLimiter->reset($rateKey);

        $passwordHash = password_hash($this->pepperPassword($password), PASSWORD_ARGON2ID, self::ARGON2_OPTIONS);

        $created = $this->userRepository->create([
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'display_name'  => $username,
            'gender'        => $gender,
            'account_type'  => 'free',
        ]);

        $this->session->setRegisteredUser($created);
        $this->session->regenerateId();

        $authKey = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::AUTH_KEY_TTL);
        $this->userRepository->saveAuthKey($created['user_id'], $authKey, $expiresAt, $clientIp);

        return [
            'success'  => true,
            'redirect' => '/home',
            'auth_key' => $authKey,
            'user'     => [
                'id'           => $created['user_id'],
                'username'     => $username,
                'email'        => $email,
                'display_name' => $username,
                'account_type' => 'free',
                'role'         => $created['role_name'],
            ],
        ];
    }

    public function logout(): void
    {
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
        $user = $this->userRepository->findByIdHex($userId);
        if ($user !== null) {
            unset($user['password_hash']);
        }
        return $user;
    }

    public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array
    {
        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::invalidEmail();
        }

        $rateKey = self::PASSWORD_RESET_RATE_KEY_PREFIX . $clientIp;
        if ($this->rateLimiter->isLimited($rateKey, self::PASSWORD_RESET_MAX_ATTEMPTS, self::PASSWORD_RESET_WINDOW_SECONDS)) {
            throw RateLimitException::passwordResetRateLimited(self::PASSWORD_RESET_WINDOW_SECONDS);
        }

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            $this->rateLimiter->increment($rateKey, self::PASSWORD_RESET_WINDOW_SECONDS);
            return ['success' => true, 'message' => 'Sıfırlama bağlantısı gönderildi.'];
        }

        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);
        $expiresAt = date('Y-m-d H:i:s', time() + self::PASSWORD_RESET_WINDOW_SECONDS);

        $this->userRepository->saveResetToken($user['id'], $tokenHash, $expiresAt, $clientIp);
        $this->rateLimiter->increment($rateKey, self::PASSWORD_RESET_WINDOW_SECONDS);

        error_log('[AuthService] Password reset requested for user_id: ' . $user['id']);

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

        $newHash = password_hash($this->pepperPassword($newPassword), PASSWORD_ARGON2ID, self::ARGON2_OPTIONS);
        $this->userRepository->updatePassword($record['user_id'], $newHash);
        $this->userRepository->markResetTokenUsed($record['id']);

        error_log('[AuthService] Password reset completed for user_id: ' . $record['user_id']);

        return ['success' => true, 'message' => 'Şifreniz güncellendi.'];
    }

    public function validateSessionKey(string $authKey): array
    {
        if ($authKey === '') {
            throw AuthenticationException::invalidCredentials();
        }

        // İlk olarak strict modda dene (used_at IS NULL)
        $record = $this->userRepository->findValidAuthKey($authKey, false);

        // Strict başarısızsa — grace window ile dene (30s içinde tekrar kullanım)
        if ($record === null) {
            $record = $this->userRepository->findValidAuthKey($authKey, true);
            if ($record === null) {
                throw AuthenticationException::invalidCredentials();
            }
            // Grace window: key zaten kullanıldı ama 30s içinde — idempotent tekrar kullanım
            return [
                'user_id'      => $record['user_id'],
                'username'     => $record['username'],
                'email'        => $record['email'],
                'display_name' => $record['display_name'] ?? $record['username'],
                'gender'       => $record['gender'] ?? 'neutral',
                'avatar_url'   => $record['avatar_url'] ?? null,
                'account_type' => $record['account_type'] ?? 'free',
            ];
        }

        // İlk kullanım — key'i işaretle
        $this->userRepository->markAuthKeyUsed($record['token_id']);

        return [
            'user_id'      => $record['user_id'],
            'username'     => $record['username'],
            'email'        => $record['email'],
            'display_name' => $record['display_name'] ?? $record['username'],
            'gender'       => $record['gender'] ?? 'neutral',
            'avatar_url'   => $record['avatar_url'] ?? null,
            'account_type' => $record['account_type'] ?? 'free',
        ];
    }
}
