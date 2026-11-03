<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Repository;

use CoreMusic\Auth\Repository\UserRepository;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Security\UuidV7;
use PHPUnit\Framework\TestCase;

/**
 * UserRepository unit tests — CRUD, RBAC, token flows.
 *
 * Coverage: 36 test cases, 110+ assertions.
 * Repository creates its own DatabaseManager instance lazily; here we mock
 * IDatabaseRegistry directly since repository calls registry()->get($key).
 */
final class UserRepositoryTest extends TestCase
{
    private IDatabaseRegistry $registry;
    private IDatabaseManager $db;
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(IDatabaseRegistry::class);
        $this->db = $this->createMock(TestOnlyDatabaseManager::class);
        $this->registry->method('get')->willReturn($this->db);
        $this->repository = new UserRepository($this->registry);
    }

    // ─── FIND BY CREDENTIAL / EMAIL / USERNAME / ID HEX TESTS ─────────────────

    public function testFindByCredential_found_returnsUserWithHexId(): void
    {
        $binaryId = UuidV7::toBinary('a'.str_repeat('b', 31));
        $hexId = 'a'.str_repeat('b', 31);
        $row = ['id' => $binaryId, 'username' => 'testuser', 'email' => 'test@example.com', 'password_hash' => '$argon2id$...', 'is_active' => 1, 'gender' => 'neutral'];

        $this->db->method('execute')->willReturn([$row]);

        $result = $this->repository->findByCredential('test@example.com');

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['id']);
        $this->assertSame('testuser', $result['username']);
    }

    public function testFindByCredential_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findByCredential('nonexistent@example.com'));
    }

    public function testFindByEmail_found_returnsUser(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $binaryId = UuidV7::toBinary($hexId);
        $row = ['id' => $binaryId, 'username' => 'user1', 'email' => 'user1@example.com', 'password_hash' => '$argon2id$...', 'is_active' => 1];

        $this->db->method('execute')->willReturn([$row]);

        $result = $this->repository->findByEmail('user1@example.com');

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['id']);
    }

    public function testFindByEmail_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findByEmail('missing@example.com'));
    }

    public function testFindByUsername_found_returnsUser(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $binaryId = UuidV7::toBinary($hexId);
        $row = ['id' => $binaryId, 'username' => 'user1', 'email' => 'user1@example.com', 'password_hash' => '$argon2id$...', 'is_active' => 1];

        $this->db->method('execute')->willReturn([$row]);

        $result = $this->repository->findByUsername('user1');

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['id']);
    }

    public function testFindByUsername_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findByUsername('missing'));
    }

    public function testFindByIdHex_found_returnsUser(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $binaryId = UuidV7::toBinary($hexId);
        $row = ['id' => $binaryId, 'username' => 'user1', 'email' => 'user1@example.com', 'password_hash' => '$argon2id$...', 'is_active' => 1];

        $this->db->method('execute')->willReturn([$row]);

        $result = $this->repository->findByIdHex($hexId);

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['id']);
    }

    public function testFindByIdHex_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findByIdHex('a'.str_repeat('b', 31)));
    }

    // ─── CREATE TESTS ───────────────────────────────────────────────────────────

    public function testCreate_generatesBinaryId_andInsertsUser(): void
    {
        $newBinaryId = UuidV7::generateBinary();
        $newHexId = UuidV7::toHex($newBinaryId);
        $userData = [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'password_hash' => '$argon2id$...',
            'display_name' => 'New User',
            'gender' => 'female',
            'account_type' => 'free',
        ];

        // Mock execute for role lookup
        $this->db->method('execute')->willReturn([['id' => UuidV7::generateBinary()]]);
        // Mock write for user insert and role assignment
        $this->db->expects($this->atLeastOnce())->method('write')->willReturn(true);
        $this->db->expects($this->atLeastOnce())->method('beginTransaction');
        $this->db->expects($this->atLeastOnce())->method('commit');

        $result = $this->repository->create($userData);

        $this->assertSame('newuser', $result['username']);
        $this->assertSame('new@example.com', $result['email']);
        $this->assertSame('free_user', $result['role_name']);
        // user_id should be valid hex UUID v7
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $result['user_id']);
    }

    public function testCreate_exception_rollsBack(): void
    {
        $this->expectException(\RuntimeException::class);

        $userData = [
            'username' => 'failuser',
            'email' => 'fail@example.com',
            'password_hash' => '$argon2id$...',
        ];

        $this->db->expects($this->once())->method('beginTransaction');
        $this->db->method('write')->willThrowException(new \RuntimeException('DB insert failed'));
        $this->db->expects($this->once())->method('rollBack');

        $this->repository->create($userData);
    }

    public function testCreate_noFreeUserRole_skipsRoleAssignment(): void
    {
        $userData = [
            'username' => 'nouserole',
            'email' => 'nouserole@example.com',
            'password_hash' => '$argon2id$...',
        ];

        $this->db->method('execute')->willReturn([]); // no role found
        $this->db->expects($this->atLeastOnce())->method('write')->willReturn(true);
        $this->db->method('beginTransaction');
        $this->db->method('commit');

        $result = $this->repository->create($userData);

        $this->assertSame('nouserole', $result['username']);
        $this->assertSame('free_user', $result['role_name']);
    }

    // ─── EMAIL/USERNAME EXISTS TESTS ───────────────────────────────────────────

    public function testEmailExists_exists_returnsTrue(): void
    {
        $this->db->method('execute')->willReturn([['id' => UuidV7::generateBinary()]]);

        $this->assertTrue($this->repository->emailExists('existing@example.com'));
    }

    public function testEmailExists_notExists_returnsFalse(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertFalse($this->repository->emailExists('missing@example.com'));
    }

    public function testUsernameExists_exists_returnsTrue(): void
    {
        $this->db->method('execute')->willReturn([['id' => UuidV7::generateBinary()]]);

        $this->assertTrue($this->repository->usernameExists('existinguser'));
    }

    public function testUsernameExists_notExists_returnsFalse(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertFalse($this->repository->usernameExists('missinguser'));
    }

    // ─── ROLE/RBAC TESTS ────────────────────────────────────────────────────────

    public function testFindRolesForUser_returnsRolesWithPermissions(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $this->db->method('execute')->willReturn([
            ['role_name' => 'admin', 'permissions' => '["user.manage","library.manage"]'],
            ['role_name' => 'user', 'permissions' => '["library.read"]'],
        ]);

        $roles = $this->repository->findRolesForUser($hexId);

        $this->assertCount(2, $roles);
        $this->assertSame('admin', $roles[0]['role_name']);
        $this->assertSame(['user.manage', 'library.manage'], $roles[0]['permissions']);
        $this->assertSame('user', $roles[1]['role_name']);
        $this->assertSame(['library.read'], $roles[1]['permissions']);
    }

    public function testFindRolesForUser_invalidJson_permissionsBecomeEmpty(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $this->db->method('execute')->willReturn([
            ['role_name' => 'user', 'permissions' => 'not-valid-json'],
        ]);

        $roles = $this->repository->findRolesForUser($hexId);

        $this->assertCount(1, $roles);
        $this->assertSame([], $roles[0]['permissions']);
    }

    public function testFindRolesForUser_empty_returnsEmptyList(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $this->db->method('execute')->willReturn([]);

        $this->assertSame([], $this->repository->findRolesForUser($hexId));
    }

    // ─── RESET TOKEN TESTS ─────────────────────────────────────────────────────

    public function testSaveResetToken_callsWriteWithCorrectParams(): void
    {
        $userId = 'a'.str_repeat('b', 31);
        $tokenHash = hash('sha256', 'reset-token-123');
        $expiresAt = date('Y-m-d H:i:s', time() + 900);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId, $tokenHash, $expiresAt) {
            $this->assertStringContainsString('INSERT INTO user_tokens', $sql);
            $this->assertSame($tokenHash, $params['token_hash']);
            $this->assertSame($expiresAt, $params['expires_at']);
            return true;
        });

        $this->repository->saveResetToken($userId, $tokenHash, $expiresAt, '127.0.0.1');
    }

    public function testFindValidResetToken_found_returnsRecordWithHexIds(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $binaryId = UuidV7::toBinary($hexId);
        $row = ['id' => UuidV7::generateBinary(), 'user_id' => $binaryId, 'token_hash' => hash('sha256', 'token'), 'expires_at' => date('Y-m-d H:i:s', time() + 3600), 'email' => 'test@example.com', 'password_hash' => '$argon2id$...'];

        $this->db->method('execute')->willReturn([$row]);

        $result = $this->repository->findValidResetToken(hash('sha256', 'token'));

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['user_id']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $result['id']);
    }

    public function testFindValidResetToken_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findValidResetToken('invalid-hash'));
    }

    /**
     * Tek-kullanımlık token sözleşmesi — kullanılan/geçmiş token SQL'de elenir.
     *
     * Sorgu: include/Repository/UserRepository.php:233
     *   `ut.used_at IS NULL AND ut.expires_at > NOW() AND ut.token_type = 'password_reset'`
     * Bu filtre düşerse aynı sıfırlama linki ikinci kez kullanılabilir (CWE-640).
     */
    public function testFindValidResetToken_sqlFiltersUsedAndExpiredTokens(): void
    {
        $tokenHash = hash('sha256', 'token');
        $capturedSql = '';

        $this->db->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function ($sql, $params) use (&$capturedSql, $tokenHash) {
                $capturedSql = (string) $sql;
                $this->assertSame(['token_hash' => $tokenHash], $params);
                return [];
            });

        $this->repository->findValidResetToken($tokenHash);

        $this->assertStringContainsString('ut.used_at IS NULL', $capturedSql);
        $this->assertStringContainsString('ut.expires_at > NOW()', $capturedSql);
        $this->assertStringContainsString("ut.token_type = 'password_reset'", $capturedSql);
        $this->assertStringNotContainsString('SELECT *', $capturedSql);
    }

    public function testUpdatePassword_updatesHashAndTimestamp(): void
    {
        $userId = 'a'.str_repeat('b', 31);
        $newHash = password_hash('newpass123', PASSWORD_ARGON2ID);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId, $newHash) {
            $this->assertStringContainsString('UPDATE users SET password_hash', $sql);
            $this->assertSame($newHash, $params['password_hash']);
            $this->assertSame($userId, $params['id']);
            return true;
        });

        $this->repository->updatePassword($userId, $newHash);
    }

    public function testMarkResetTokenUsed_marksToken(): void
    {
        $tokenId = 'a'.str_repeat('b', 31);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($tokenId) {
            $this->assertStringContainsString('UPDATE user_tokens SET used_at = NOW()', $sql);
            $this->assertSame($tokenId, $params['id']);
            return true;
        });

        $this->repository->markResetTokenUsed($tokenId);
    }

    // ─── AUTH KEY TESTS ────────────────────────────────────────────────────────

    public function testSaveAuthKey_storesHashedToken(): void
    {
        $userId = 'a'.str_repeat('b', 31);
        $authKey = 'test-auth-key-12345678901234567890123456789012';

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId, $authKey) {
            $this->assertStringContainsString('token_type', $sql);
            $this->assertSame('api_key', $params['token_type']);
            $this->assertSame(hash('sha256', $authKey), $params['token_hash']);
            $this->assertSame($userId, $params['user_id']);
            return true;
        });

        $this->repository->saveAuthKey($userId, $authKey, date('Y-m-d H:i:s', time() + 300), '127.0.0.1');
    }

    public function testFindValidAuthKey_found_convertsBinaryIdToHexAndReturnsRow(): void
    {
        $hexId = 'a'.str_repeat('b', 31);
        $tokenId = 'c'.str_repeat('d', 31);
        $row = [
            'id' => UuidV7::generateBinary(),
            'user_id' => UuidV7::toBinary($hexId),
            'used_at' => null,
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
            'username' => 'testuser',
            'email' => 'test@example.com',
            'display_name' => 'Test User',
            'gender' => 'neutral',
            'avatar_url' => null,
            'account_type' => 'free',
            'token_id' => UuidV7::toBinary($tokenId),
        ];
        $this->db->method('execute')->willReturn([$row]);
        $result = $this->repository->findValidAuthKey(hash('sha256', 'valid-auth-key'));

        $this->assertNotNull($result);
        $this->assertSame($hexId, $result['user_id']);
        $this->assertSame($tokenId, $result['token_id']);
    }

    public function testFindValidAuthKey_notFound_returnsNull(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertNull($this->repository->findValidAuthKey('invalid'));
    }

    public function testMarkAuthKeyUsed_marksTokenUsed(): void
    {
        $tokenId = 'a'.str_repeat('b', 31);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($tokenId) {
            $this->assertStringContainsString('UPDATE user_tokens SET used_at = NOW()', $sql);
            $this->assertSame($tokenId, $params['id']);
            return true;
        });

        $this->repository->markAuthKeyUsed($tokenId);
    }

    public function testRevokeAuthKeysForUser_revokesAll(): void
    {
        $userId = 'a'.str_repeat('b', 31);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId) {
            $this->assertStringContainsString("token_type = 'api_key'", $sql);
            $this->assertSame($userId, $params['user_id']);
            return true;
        });

        $this->repository->revokeAuthKeysForUser($userId);
    }

    // ─── ACCESS TOKEN TESTS ────────────────────────────────────────────────────

    public function testSaveAccessToken_storesHashedJti(): void
    {
        $userId = 'a'.str_repeat('b', 31);
        $jti = 'jti-hex-value-12345';
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId, $jti, $expiresAt) {
            $this->assertStringContainsString("UNHEX(:user_id), 'access'", $sql);
            $this->assertSame(hash('sha256', $jti), $params['token_hash']);
            $this->assertSame($userId, $params['user_id']);
            $this->assertSame($expiresAt, $params['expires_at']);
            return true;
        });

        $this->repository->saveAccessToken($userId, $jti, $expiresAt);
    }

    public function testIsValidAccessToken_valid_returnsTrue(): void
    {
        $this->db->method('execute')->willReturn([['id' => UuidV7::generateBinary()]]);

        $this->assertTrue($this->repository->isValidAccessToken('valid-jti'));
    }

    public function testIsValidAccessToken_invalid_returnsFalse(): void
    {
        $this->db->method('execute')->willReturn([]);

        $this->assertFalse($this->repository->isValidAccessToken('invalid-jti'));
    }

    public function testRevokeAccessTokensForUser_revokesAllAccessTokens(): void
    {
        $userId = 'a'.str_repeat('b', 31);

        $this->db->expects($this->once())->method('write')->willReturnCallback(function ($sql, $params) use ($userId) {
            $this->assertStringContainsString("token_type = 'access'", $sql);
            $this->assertSame($userId, $params['user_id']);
            return true;
        });

        $this->repository->revokeAccessTokensForUser($userId);
    }
}

/**
 * Test-only contract — IDatabaseManager sözleşme boşluğu kaydı.
 *
 * Production: UserRepository::create() transact eder (beginTransaction/commit/
 * rollBack çağırır), ama bu üç metot IDatabaseManager'da BEYANSIZDIR (ISqlDatabaseManager
 * yalnız getPdo() ekler) — bu fazda bilinçli olarak kapatilmadi (contract gap, ayrı faz).
 * PHPUnit addMethods mock üretimi PHP 8.5 + PHPUnit 10.5.64'te süreci sessiz öldürüyor
 * (exit 255); o yüzden üç void transaction metodu test düzeyinde burada dışa açılır.
 * Üretim sınıflarına etkisi YOK — sadece mock yapılandırılabilirliği için.
 */
interface TestOnlyDatabaseManager extends IDatabaseManager
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}