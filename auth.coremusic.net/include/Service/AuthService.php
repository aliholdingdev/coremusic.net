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
use CoreMusic\Interfaces\Auth\IAuthService;
use CoreMusic\Interfaces\Auth\ISessionManager;
use CoreMusic\Interfaces\Auth\IUserRepository;
use CoreMusic\Interfaces\Security\IRateLimiter;
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
        $failedKey = self::LOGIN_RATE_KEY_PREFIX . $request->clientIp;

        if ($this->rateLimiter->isLimited($failedKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)) {
            throw RateLimitException::loginRateLimited(self::LOGIN_WINDOW_SECONDS);
        }

        if ($request->identity === '' || $request->password === '') {
            throw ValidationException::emptyFields();
        }

        $row = $this->userRepository->findByCredential($request->identity);
        if ($row === null) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
            throw AuthenticationException::invalidCredentials();
        }

        $user = User::fromRow($row);

        if ($user->isBanned()) {
            throw AuthenticationException::banned();
        }

        $password = Password::create($request->password);
        if (!$password->verify($user->passwordHash, $this->pepper)) {
            $this->rateLimiter->increment($failedKey, self::LOGIN_WINDOW_SECONDS);
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
        $this->userRepository->updateLastLogin($user->id);

        $this->session->setAuthUser($user->toArray());

        $authKey = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::AUTH_KEY_TTL);
        $this->userRepository->saveAuthKey($user->id, $authKey, $expiresAt, $request->clientIp);

        return AuthResponse::success(
            redirect: '/home',
            authKey: $authKey,
            user: $user->toArray(),
        );
    }

    /**
     * Kullanıcı kaydı — RegisterRequest DTO alır, AuthResponse döndürür.
     */
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

    // ─── IAuthService Interface Uyumluluğu ───

    public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array
    {
        $request = new LoginRequest($identity, $password, $visitorGender, $clientIp);
        return $this->loginWithRequest($request)->toArray();
    }

    public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array
    {
        $request = RegisterRequest::fromArray($data, ['REMOTE_ADDR' => $clientIp], $visitorGender);
        return $this->registerWithRequest($request)->toArray();
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
        $row = $this->userRepository->findByIdHex($userId);
        if ($row === null) {
            return null;
        }
        $user = User::fromRow($row);
        return $user->toArray();
    }

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
        $expiresAt = date('Y-m-d H:i:s', time() + self::PASSWORD_RESET_WINDOW_SECONDS);

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
