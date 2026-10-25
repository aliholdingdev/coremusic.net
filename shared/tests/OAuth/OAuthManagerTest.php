<?php declare(strict_types=1);

namespace CoreMusic\Test\OAuth;

use PHPUnit\Framework\TestCase;
use CoreMusic\OAuth\OAuthManager;

/**
 * OAuthManager Test — ADR-088 compliant.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 */
final class OAuthManagerTest extends TestCase
{
    private array $platformConfig;

    protected function setUp(): void
    {
        $this->platformConfig = require __DIR__ . '/../../../shared/config/oauth-platforms.php';
    }

    /**
     * Female gender için doğru platform sayısını test et.
     */
    public function testGetPlatformsByGenderFemale(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');

        $platforms = $manager->getPlatformsByGender('female');

        $this->assertCount(5, $platforms);
        $this->assertEquals('pinterest', $platforms[0]['provider']);
        $this->assertEquals('instagram', $platforms[1]['provider']);
        $this->assertEquals('tiktok', $platforms[2]['provider']);
        $this->assertEquals('snapchat', $platforms[3]['provider']);
        $this->assertEquals('youtube', $platforms[4]['provider']);
    }

    /**
     * Male gender için doğru platform sayısını test et.
     */
    public function testGetPlatformsByGenderMale(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');

        $platforms = $manager->getPlatformsByGender('male');

        $this->assertCount(5, $platforms);
        $this->assertEquals('discord', $platforms[0]['provider']);
        $this->assertEquals('reddit', $platforms[1]['provider']);
        $this->assertEquals('x', $platforms[2]['provider']);
        $this->assertEquals('linkedin', $platforms[3]['provider']);
        $this->assertEquals('youtube', $platforms[4]['provider']);
    }

    /**
     * Neutral gender için fallback test et.
     */
    public function testGetPlatformsByGenderNeutral(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');

        $platforms = $manager->getPlatformsByGender('neutral');

        $this->assertCount(2, $platforms);
        $this->assertEquals('youtube', $platforms[0]['provider']);
        $this->assertEquals('facebook', $platforms[1]['provider']);
    }

    /**
     * Bilinmeyen gender için neutral fallback test et.
     */
    public function testGetPlatformsByGenderUnknownFallsBackToNeutral(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');

        $platforms = $manager->getPlatformsByGender('other');

        $this->assertCount(2, $platforms);
    }

    /**
     * Encryption/decryption roundtrip test et.
     */
    public function testEncryptDecryptRoundtrip(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $key = 'test-key-32-bytes-long-for-aes256';
        $manager = new OAuthManager($pdo, $this->platformConfig, $key);

        $reflection = new \ReflectionClass($manager);
        $encryptMethod = $reflection->getMethod('encrypt');

        $plaintext = 'my-super-secret-access-token-12345';
        $encrypted = $encryptMethod->invoke($manager, $plaintext);

        $this->assertNotEquals($plaintext, $encrypted);

        $decrypted = $manager->decrypt($encrypted);
        $this->assertEquals($plaintext, $decrypted);
    }

    /**
     * Farklı plaintext'ler için farklı ciphertext üretildiğini test et.
     */
    public function testEncryptProducesDifferentCiphertexts(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $key = 'test-key-32-bytes-long-for-aes256';
        $manager = new OAuthManager($pdo, $this->platformConfig, $key);

        $reflection = new \ReflectionClass($manager);
        $encryptMethod = $reflection->getMethod('encrypt');

        $text = 'same-token';
        $encrypted1 = $encryptMethod->invoke($manager, $text);
        $encrypted2 = $encryptMethod->invoke($manager, $text);

        $this->assertNotEquals($encrypted1, $encrypted2, 'IV ensures different ciphertexts');
    }

    /**
     * Yanlış key ile decryption'ın başarısız olduğunu test et.
     */
    public function testDecryptWithWrongKeyFails(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager1 = new OAuthManager($pdo, $this->platformConfig, 'correct-key-32-bytes-long!!!!');
        $manager2 = new OAuthManager($pdo, $this->platformConfig, 'wrong-key--32-bytes-long!!!!');

        $reflection = new \ReflectionClass($manager1);
        $encryptMethod = $reflection->getMethod('encrypt');

        $encrypted = $encryptMethod->invoke($manager1, 'secret');

        $this->expectException(\RuntimeException::class);
        $manager2->decrypt($encrypted);
    }

    /**
     * Priority sıralamasını test et.
     */
    public function testPlatformPriorityOrder(): void
    {
        $pdo = $this->createMock(\PDO::class);
        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');

        $femalePlatforms = $manager->getPlatformsByGender('female');
        $priorities = array_column($femalePlatforms, 'priority');

        // Priority should be in ascending order
        for ($i = 1; $i < count($priorities); $i++) {
            $this->assertLessThanOrEqual($priorities[$i], $priorities[$i - 1]);
        }
    }

    /**
     * PRD-OAU-003: state doğrulama. Sahte PDO ile repository davranışı sürülür;
     * süre kontrolü SQL'de (expires_at > NOW()) olduğundan yalnız SQL'in varlığı doğrulanır.
     *
     * @param \ArrayObject<int, array{sql: string, params: array}> $log
     */
    private function managerWithFetchResult(\ArrayObject $log, mixed $fetchResult): OAuthManager
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($fetchResult);

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturnCallback(function (string $sql) use ($stmt, $log): \PDOStatement {
            $log->append(['sql' => $sql, 'params' => []]);
            return $stmt;
        });

        return new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');
    }

    public function testValidateStateRejectsMissingState(): void
    {
        $log = new \ArrayObject();
        $manager = $this->managerWithFetchResult($log, false);

        $this->assertNull($manager->validateState('never-saved', 'discord', 1));
        $this->assertCount(1, $log, 'Yalnız SELECT çalışmalı; state yoksa DELETE yapılmaz');
        $this->assertStringContainsString('SELECT code_verifier FROM oauth_states', $log[0]['sql']);
    }

    public function testValidateStateRejectsWrongState(): void
    {
        $log = new \ArrayObject();
        // Yanlış state için repository eşleşme bulamaz (hash farklı)
        $manager = $this->managerWithFetchResult($log, false);

        $this->assertNull($manager->validateState('wrong-state-value', 'discord', 1));
        $this->assertCount(1, $log);
    }

    public function testStateIsHashedBeforeLookupEvenWhenRejected(): void
    {
        // Ham state değeri DB'ye gitmez; yalnız SHA-256 hash'i (saklanan biçim) aranır.
        $raw = 'raw-state-token';
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->with($this->callback(
            fn(array $p): bool => $p['state'] === hash('sha256', 'raw-state-token')
                && $p['state'] !== 'raw-state-token'
        ))->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $manager = new OAuthManager($pdo, $this->platformConfig, 'test-key-32-bytes-long-for-aes256');
        $this->assertNull($manager->validateState($raw, 'discord', 1));
    }

    public function testValidateStateSqlEnforcesExpiry(): void
    {
        $log = new \ArrayObject();
        $manager = $this->managerWithFetchResult($log, false);

        $this->assertNull($manager->validateState('expired-state', 'discord', 1));
        $this->assertStringContainsString('expires_at > NOW()', $log[0]['sql']);
    }

    public function testValidateStateAcceptsValidStateAndDeletesIt(): void
    {
        $log = new \ArrayObject();
        $manager = $this->managerWithFetchResult($log, ['code_verifier' => 'verifier-abc']);

        $result = $manager->validateState('good-state', 'discord', 1);

        $this->assertSame(['code_verifier' => 'verifier-abc'], $result);
        $this->assertCount(2, $log, 'Geçerli state SELECT sonrası silinmeli (tek kullanımlık)');
        $this->assertStringContainsString('DELETE FROM oauth_states', $log[1]['sql']);
    }
}
