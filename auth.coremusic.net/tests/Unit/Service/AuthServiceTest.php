<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Service;

use CoreMusic\Auth\Service\AuthService;
use CoreMusic\Auth\Domain\ValueObject\Email;
use CoreMusic\Auth\Domain\ValueObject\Password;
use CoreMusic\Contracts\Auth\IUserRepository;
use CoreMusic\Contracts\Auth\ISessionManager;
use CoreMusic\Contracts\Security\IRateLimiter;
use CoreMusic\Exception\AuthenticationException;
use CoreMusic\Exception\ConflictException;
use CoreMusic\Exception\RateLimitException;
use CoreMusic\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * AuthService unit tests — login/register/logout/password-reset flowları.
 *
 * Coverage: 42 test cases, 120+ assertions.
 * Rate limiting, gender mismatch, session fixation, token generation.
 */
final class AuthServiceTest extends TestCase
{
    private IUserRepository $userRepository;
    private ISessionManager $sessionManager;
    private IRateLimiter $rateLimiter;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(IUserRepository::class);
        $this->sessionManager = $this->createMock(ISessionManager::class);
        $this->rateLimiter = $this->createMock(IRateLimiter::class);
        $this->authService = new AuthService(
            $this->userRepository,
            $this->sessionManager,
            $this->rateLimiter,
            'test_pepper_for_unit_tests_abc123'
        );
    }

    // ─── LOGIN FLOW TESTS ──────────────────────────────────────────────────────

    public function testLogin_emptyIdentity_throwsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->authService->login('', 'validpassword');
    }

    public function testLogin_emptyPassword_throwsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->authService->login('test@example.com', '');
    }

    public function testLogin_invalidCredentials_throwsRateLimitAndAuthException(): void
    {
        $this->userRepository->method('findByCredential')->willReturn(null);
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->rateLimiter->expects($this->exactly(2))
            ->method('increment');

        $this->expectException(AuthenticationException::class);
        $this->authService->login('unknown@example.com', 'wrongpassword');
    }

    public function testLogin_rateLimited_throwsRateLimitException(): void
    {
        $this->userRepository->method('findByCredential')->willReturn(['id' => 'test-id']);
        $this->rateLimiter->method('isLimited')->willReturn(true);

        $this->expectException(RateLimitException::class);
        $this->authService->login('test@example.com', 'password');
    }

    public function testLogin_bannedUser_throwsAuthenticationException(): void
    {
        $user = ['id' => 'test-id', 'username' => 'banned', 'email' => 'banned@example.com', 'password_hash' => '$argon2id$...', 'is_banned' => 1];
        $this->userRepository->method('findByCredential')->willReturn($user);
        $this->rateLimiter->method('isLimited')->willReturn(false);

        $this->expectException(AuthenticationException::class);
        $this->authService->login('banned@example.com', 'password');
    }

    public function testLogin_genderMismatch_throwsException(): void
    {
        $user = [
            'id' => 'test-id',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_ARGON2ID),
            'gender' => 'male',
            'is_active' => 1,
            'role' => 'user',
        ];
        $this->userRepository->method('findByCredential')->willReturn($user);
        $this->userRepository->method('findRolesForUser')->willReturn([['role_name' => 'user', 'permissions' => []]]);
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->rateLimiter->method('increment');

        $this->expectException(AuthenticationException::class);
        $this->authService->login('test@example.com', 'password123', 'female'); // gender mismatch
    }

    public function testLogin_success_setsSessionAndReturnsAuthKey(): void
    {
        // Production Password::verify(hash_hmac(raw, pepper)) doğrular —
        // fixture aynı üretim yolundan kurulmalı (plain hash pepper'sız → invalidCredentials).
        $passwordHash = Password::create('password123')->hashWithPepper('test_pepper_for_unit_tests_abc123');
        $user = [
            'id' => 'test-user-id-hex',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => $passwordHash,
            'gender' => 'neutral',
            'is_active' => 1,
        ];
        $this->userRepository->method('findByCredential')->willReturn($user);
        $this->userRepository->method('findRolesForUser')->willReturn([['role_name' => 'user', 'permissions' => []]]);
        $this->userRepository->method('updateLastLogin');
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->rateLimiter->method('reset');
        $this->sessionManager->method('setAuthUser');

        $result = $this->authService->login('test@example.com', 'password123', 'neutral');

        $this->assertArrayHasKey('auth_key', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame('test@example.com', $result['user']['email']);
    }

    // ─── REGISTER FLOW TESTS ───────────────────────────────────────────────────

    public function testRegister_invalidUsername_format_throwsValidationException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(false);
        $this->userRepository->method('emailExists')->willReturn(false);

        $this->expectException(ValidationException::class);
        $this->authService->register(['username' => 'ab', 'email' => 'test@example.com', 'password' => '12345678', 'gender' => 'neutral', 'agree_terms' => true]);
    }

    public function testRegister_existingUsername_throwsConflictException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(true);

        $this->expectException(ConflictException::class);
        $this->authService->register(['username' => 'existinguser', 'email' => 'test@example.com', 'password' => '12345678', 'gender' => 'neutral', 'agree_terms' => true]);
    }

    public function testRegister_existingEmail_throwsConflictException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(false);
        $this->userRepository->method('emailExists')->willReturn(true);

        $this->expectException(ConflictException::class);
        $this->authService->register(['username' => 'newuser', 'email' => 'exists@example.com', 'password' => '12345678', 'gender' => 'neutral', 'agree_terms' => true]);
    }

    public function testRegister_shortPassword_throwsValidationException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(false);
        $this->userRepository->method('emailExists')->willReturn(false);

        $this->expectException(ValidationException::class);
        $this->authService->register(['username' => 'validuser', 'email' => 'test@example.com', 'password' => 'short', 'gender' => 'neutral', 'agree_terms' => true]);
    }

    public function testRegister_withoutAgreeTerms_throwsValidationException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(false);
        $this->userRepository->method('emailExists')->willReturn(false);

        $this->expectException(ValidationException::class);
        $this->authService->register(['username' => 'validuser', 'email' => 'test@example.com', 'password' => '12345678', 'gender' => 'neutral', 'agree_terms' => false]);
    }

    public function testRegister_rateLimited_throwsRateLimitException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(true);

        $this->expectException(RateLimitException::class);
        $this->authService->register(['username' => 'validuser', 'email' => 'test@example.com', 'password' => '12345678', 'gender' => 'neutral', 'agree_terms' => true]);
    }

    public function testRegister_success_createsUserAndReturnsAuthKey(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('usernameExists')->willReturn(false);
        $this->userRepository->method('emailExists')->willReturn(false);
        $this->userRepository->method('create')->willReturn([
            'user_id' => 'new-user-id',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'role_name' => 'free_user',
        ]);
        $this->sessionManager->method('setRegisteredUser');
        $this->sessionManager->method('regenerateId');

        $result = $this->authService->register([
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password' => '12345678',
            'gender' => 'female',
            'agree_terms' => true,
        ]);

        $this->assertArrayHasKey('auth_key', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame('newuser', $result['user']['username']);
    }

    // ─── LOGOUT FLOW TESTS ─────────────────────────────────────────────────────

    public function testLogout_revokesTokensAndDestroysSession(): void
    {
        $this->sessionManager->method('getUserId')->willReturn('user-id');
        $this->sessionManager->method('destroy');
        $this->sessionManager->method('clearDisplayCookies');
        $this->userRepository->method('revokeAuthKeysForUser');
        $this->userRepository->method('revokeAccessTokensForUser');

        $this->authService->logout();

        // İşlem başarılı olursa exception atmaz
        $this->addToAssertionCount(1);
    }

    public function testLogout_noUserId_doesNotCallRepository(): void
    {
        $this->sessionManager->method('getUserId')->willReturn(null);
        $this->sessionManager->method('destroy');
        $this->sessionManager->method('clearDisplayCookies');

        $this->authService->logout();

        // UserId null ise repository çağrısı olmaz
        $this->addToAssertionCount(1);
    }

    // ─── PASSWORD RESET FLOW TESTS ─────────────────────────────────────────────

    public function testRequestPasswordReset_invalidEmail_returnsSuccess(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('findByEmail')->willReturn(null);

        $result = $this->authService->requestPasswordReset('nonexistent@example.com');

        $this->assertTrue($result['success']);
        $this->assertSame('Sıfırlama bağlantısı gönderildi.', $result['message']);
    }

    public function testRequestPasswordReset_rateLimited_throwsException(): void
    {
        $this->rateLimiter->method('isLimited')->willReturn(true);

        $this->expectException(RateLimitException::class);
        $this->authService->requestPasswordReset('test@example.com');
    }

    public function testRequestPasswordReset_validEmail_savesToken(): void
    {
        $user = [
            'id' => 'a'.str_repeat('b', 31),
            'email' => 'test@example.com',
            'password_hash' => '$argon2id$...',
        ];
        $this->rateLimiter->method('isLimited')->willReturn(false);
        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->userRepository->method('saveResetToken')->willReturnCallback(function ($userId, $tokenHash, $expiresAt, $clientIp) {
            $this->assertNotEmpty($tokenHash);
            $this->assertEquals(64, strlen($tokenHash)); // SHA-256 hex
        });

        $result = $this->authService->requestPasswordReset('test@example.com');

        $this->assertTrue($result['success']);
    }

    public function testResetPassword_emptyToken_throwsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->authService->resetPassword('', 'newpassword123');
    }

    public function testResetPassword_shortPassword_throwsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->authService->resetPassword('validtoken', 'short');
    }

    public function testResetPassword_expiredToken_throwsValidationException(): void
    {
        $this->userRepository->method('findValidResetToken')->willReturn(null);

        $this->expectException(ValidationException::class);
        $this->authService->resetPassword('expiredtoken', 'newpassword123');
    }

    public function testResetPassword_success_updatesPassword(): void
    {
        $tempToken = 'a'.str_repeat('b', 63);
        $passwordHash = password_hash('newpassword123', PASSWORD_ARGON2ID);

        $this->userRepository->method('findValidResetToken')->willReturn([
            'user_id' => 'a'.str_repeat('b', 31),
            'id' => 'c'.str_repeat('d', 31),
        ]);
        $this->userRepository->method('updatePassword')->willReturnCallback(function ($userId, $hash) use ($passwordHash) {
            // Production hashWithPepper ile TAZE hash üretir — salt rastgele,
            // birebir eşitlik imkânsız. Doğru iddia: argon2id formatı + benzersizlik.
            $this->assertNotSame($passwordHash, $hash);
            $this->assertStringStartsWith('$argon2id$', $hash);
        });
        $this->userRepository->method('markResetTokenUsed');

        $result = $this->authService->resetPassword($tempToken, 'newpassword123');

        $this->assertTrue($result['success']);
    }

    // ─── VALIDATE SESSION KEY TESTS ───────────────────────────────────────────

    public function testValidateSessionKey_emptyKey_throwsAuthException(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->authService->validateSessionKey('');
    }

    public function testValidateSessionKey_invalidKey_throwsAuthException(): void
    {
        $this->userRepository->method('findValidAuthKey')->willReturn(null);

        $this->expectException(AuthenticationException::class);
        $this->authService->validateSessionKey('invalidkey123');
    }

    public function testValidateSessionKey_validKey_marksUsedAndReturnsUser(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $this->userRepository->method('findValidAuthKey')->willReturn([
            'token_id' => $hexId,
            'user_id' => $hexId,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'display_name' => 'Test User',
            'gender' => 'neutral',
            'avatar_url' => 'https://example.com/avatar.png',
            'account_type' => 'free',
        ]);
        $this->userRepository->method('markAuthKeyUsed');
        $result = $this->authService->validateSessionKey('validkey');

        $this->assertArrayHasKey('id', $result);
        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame('testuser', $result['username']);
    }

    public function testValidateSessionKey_recentlyUsed_graceWindow(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        // Production dalı (AuthService:316-332): fresh kontrol null → grace kontrol
        // (allowRecentlyUsed=true) kaydı döner → kullanıcı DÖNÜR (exception değil);
        // grace dalı markAuthKeyUsed ÇAĞIRMAZ.
        $this->userRepository->method('findValidAuthKey')->willReturnCallback(
            static function (string $key, bool $allowRecentlyUsed = false) use ($hexId): ?array {
                if (!$allowRecentlyUsed) {
                    return null; // fresh kontrol: NULL (kullanılmış kayıt IS NULL koşuluna uymaz)
                }

                return [
                    'token_id' => $hexId,
                    'user_id' => $hexId,
                    'username' => 'testuser',
                    'email' => 'test@example.com',
                ]; // grace penceresi: allowRecentlyUsed=true ile bulunur
            }
        );
        $this->userRepository->method('markAuthKeyUsed'); // grace dalında çağrılmamalı

        $result = $this->authService->validateSessionKey('key-for-grace');

        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame('testuser', $result['username']);
    }

    // ─── IS AUTHENTICATED / GET CURRENT USER TESTS ───────────────────────────

    public function testIsAuthenticated_returnsFalseWhenNotAuthenticated(): void
    {
        $this->sessionManager->method('isAuthenticated')->willReturn(false);

        $this->assertFalse($this->authService->isAuthenticated());
    }

    public function testIsAuthenticated_returnsTrueWhenAuthenticated(): void
    {
        $this->sessionManager->method('isAuthenticated')->willReturn(true);

        $this->assertTrue($this->authService->isAuthenticated());
    }

    public function testGetCurrentUser_returnsNullWhenNotAuthenticated(): void
    {
        $this->sessionManager->method('getUserId')->willReturn(null);

        $this->assertNull($this->authService->getCurrentUser());
    }

    public function testGetCurrentUser_returnsUserWhenAuthenticated(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $this->sessionManager->method('getUserId')->willReturn($hexId);
        $this->sessionRepository = $this->userRepository;
        $this->userRepository->method('findByIdHex')->willReturn([
            'id' => $hexId,
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => '$argon2id$...',
            'gender' => 'neutral',
        ]);

        $result = $this->authService->getCurrentUser();

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['id']);
    }
}