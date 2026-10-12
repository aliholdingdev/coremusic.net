<?php declare(strict_types=1);

namespace CoreMusic\Test\Repository;

use CoreMusic\Repository\ApiKeyRepository;
use CoreMusic\Security\UuidV7;
use CoreMusic\Test\Repository\Sqlite\SqliteDatabaseManager;
use CoreMusic\Test\Repository\Sqlite\SqliteDatabaseRegistry;
use PHPUnit\Framework\TestCase;

/**
 * ApiKeyRepository — ADR-020 §2.2-1 doğrulama akışı (SSOT: coremusic_auth.api_keys)
 *
 * Canlı MySQL'e bağlanmadan, aynı şemayı SQLite in-memory üzerinde sınar:
 * prefix index → hash_equals → is_active/is_deleted/expiry → IP → scope → touch.
 */
final class ApiKeyRepositoryTest extends TestCase
{
    private SqliteDatabaseManager $db;
    private ApiKeyRepository $repository;
    private string $userId;

    protected function setUp(): void
    {
        $this->db = new SqliteDatabaseManager();
        $this->db->createApiKeysSchema();

        $this->repository = new ApiKeyRepository(new SqliteDatabaseRegistry($this->db));
        $this->userId     = UuidV7::generateHex();
    }

    public function testCreateStoresOnlyHashAndPrefixNeverRawKey(): void
    {
        $created = $this->repository->create([
            'user_id'  => $this->userId,
            'key_name' => 'ci-runner',
            'scopes'   => ['api.access', 'auth.read'],
            'key_type' => 'service',
        ]);

        $this->assertMatchesRegularExpression(ApiKeyRepository::RAW_KEY_PATTERN, $created['raw_key']);
        $this->assertStringStartsWith('cm_live_', $created['raw_key']);
        $this->assertNotSame('', $created['key_prefix']);

        $row = $this->firstRow();

        // DB'de yalnız hash + prefix; ham anahtar hiçbir kolonda yok.
        $this->assertSame(hash('sha256', $created['raw_key']), $row['key_hash']);
        $this->assertSame($created['key_prefix'], $row['key_prefix']);
        $encoded = (string) json_encode($row, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        $this->assertStringNotContainsString($created['raw_key'], $encoded);
        $this->assertStringNotContainsString(substr($created['raw_key'], -20), (string) $row['key_hash']);
        $this->assertSame('service', $row['key_type']);
    }

    public function testVerifyAcceptsKeyMatchingPrefixAndHash(): void
    {
        $created = $this->createKey();

        $context = $this->repository->verify($created['raw_key']);

        $this->assertIsArray($context, 'Doğru anahtar verify() ile geçmeli');
        $this->assertSame($created['key_prefix'], $context['key_prefix']);
        $this->assertSame($this->userId, $context['user_id']);
        $this->assertContains('api.access', $context['scopes']);
    }

    public function testVerifyRejectsWrongKeyWithSamePrefix(): void
    {
        $created = $this->createKey();

        // Prefix doğru, secret bozulmuş → hash_equals başarısız.
        $tampered = 'cm_live_' . $created['key_prefix'] . '_' . str_repeat('A', 43);

        $this->assertNull($this->repository->verify($tampered));
        $this->assertSame('hash_mismatch', $this->repository->inspect($tampered)['reason']);
    }

    public function testVerifyRejectsUnknownAndMalformedKeys(): void
    {
        $this->assertNull($this->repository->verify('cm_live_ZZZZZZZZ_' . str_repeat('b', 43)));
        $this->assertSame('unknown_prefix', $this->repository->inspect('cm_live_ZZZZZZZZ_' . str_repeat('b', 43))['reason']);

        $this->assertNull($this->repository->verify('not-a-key'));
        $this->assertSame('malformed_key', $this->repository->inspect('not-a-key')['reason']);
    }

    public function testVerifyRejectsInactiveKey(): void
    {
        $created = $this->createKey();
        $this->db->write('UPDATE api_keys SET is_active = 0 WHERE key_hash = :hash', [
            'hash' => hash('sha256', $created['raw_key']),
        ]);

        $this->assertNull($this->repository->verify($created['raw_key']));
        $this->assertSame('inactive', $this->repository->inspect($created['raw_key'])['reason']);
    }

    public function testVerifyRejectsDeletedKey(): void
    {
        $created = $this->createKey();
        $this->db->write('UPDATE api_keys SET is_deleted = 1 WHERE key_hash = :hash', [
            'hash' => hash('sha256', $created['raw_key']),
        ]);

        $this->assertNull($this->repository->verify($created['raw_key']));
    }

    public function testVerifyRejectsExpiredKey(): void
    {
        $created = $this->repository->create([
            'user_id'    => $this->userId,
            'key_name'   => 'short-lived',
            'scopes'     => ['api.access'],
            'expires_at' => date('Y-m-d H:i:s', time() - 60),
        ]);

        $this->assertNull($this->repository->verify($created['raw_key']));
        $this->assertSame('expired', $this->repository->inspect($created['raw_key'])['reason']);
    }

    public function testVerifyAcceptsKeyBeforeExpiry(): void
    {
        $created = $this->repository->create([
            'user_id'    => $this->userId,
            'key_name'   => 'valid-window',
            'scopes'     => ['api.access'],
            'expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        $this->assertIsArray($this->repository->verify($created['raw_key']));
    }

    public function testVerifyEnforcesScope(): void
    {
        $created = $this->createKey(['scopes' => ['api.access', 'auth.read']]);

        $this->assertIsArray($this->repository->verify($created['raw_key'], null, 'api.access'));
        $this->assertNull($this->repository->verify($created['raw_key'], null, 'music.write'));

        $result = $this->repository->inspect($created['raw_key'], null, 'music.write');
        $this->assertFalse($result['ok']);
        $this->assertSame('scope_denied', $result['reason']);
    }

    public function testVerifyEnforcesIpAllowlistWhenPresent(): void
    {
        $created = $this->repository->create([
            'user_id'     => $this->userId,
            'key_name'    => 'locked',
            'scopes'      => ['api.access'],
            'allowed_ips' => ['10.0.0.5'],
        ]);

        $this->assertIsArray($this->repository->verify($created['raw_key'], '10.0.0.5'));
        $this->assertNull($this->repository->verify($created['raw_key'], '10.0.0.6'));
        $this->assertNull($this->repository->verify($created['raw_key']));
        $this->assertSame('ip_not_allowed', $this->repository->inspect($created['raw_key'], '10.0.0.6')['reason']);
    }

    public function testVerifyUpdatesLastUsedAtOnlyOnSuccess(): void
    {
        $created = $this->createKey();

        $this->assertNull($this->firstRow()['last_used_at']);

        $this->repository->verify($created['raw_key']);
        $this->assertNotNull($this->firstRow()['last_used_at']);

        $touchedAt = (string) $this->firstRow()['last_used_at'];

        // Başarısız deneme last_used_at'ı bozmamalı.
        $this->repository->verify('cm_live_' . $created['key_prefix'] . '_' . str_repeat('C', 43));
        $this->assertSame($touchedAt, $this->firstRow()['last_used_at']);
    }

    public function testVerifyContextNeverContainsRawKeyOrHash(): void
    {
        $created = $this->createKey();

        $context = $this->repository->verify($created['raw_key']);
        $this->assertIsArray($context);

        $encoded = (string) json_encode($context, JSON_UNESCAPED_SLASHES);
        $this->assertStringNotContainsString($created['raw_key'], $encoded);
        $this->assertStringNotContainsString(hash('sha256', $created['raw_key']), $encoded);
        $this->assertArrayHasKey('key_prefix', $context);
    }

    public function testInspectFailureNeverLeaksRawKey(): void
    {
        $created = $this->createKey();
        $tampered = 'cm_live_' . $created['key_prefix'] . '_' . str_repeat('D', 43);

        $result = $this->repository->inspect($tampered);
        $encoded = (string) json_encode($result, JSON_UNESCAPED_SLASHES);

        $this->assertStringNotContainsString($tampered, $encoded);
        $this->assertNull($result['context'], 'Başarısız inceleme bağlam (ve anahtar) döndürmemeli');
    }

    public function testCreateRejectsInvalidUserAndKeyType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repository->create(['user_id' => 'not-a-uuid']);
    }

    public function testCreateRejectsUnknownKeyType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repository->create(['user_id' => $this->userId, 'key_type' => 'root']);
    }

    public function testPrefixFromRawMatchesAdr020Pattern(): void
    {
        $created = $this->repository->create([
            'user_id'    => $this->userId,
            'key_name'   => 'pattern',
            'scopes'     => ['api.access'],
            'environment' => 'test',
        ]);

        $this->assertStringStartsWith('cm_test_', $created['raw_key']);
        $this->assertSame($created['key_prefix'], ApiKeyRepository::prefixFromRaw($created['raw_key']));
        $this->assertNull(ApiKeyRepository::prefixFromRaw('cm_live_short_x'));
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function createKey(array $overrides = []): array
    {
        return $this->repository->create(array_merge([
            'user_id'  => $this->userId,
            'key_name' => 'test-key',
            'scopes'   => ['api.access', 'auth.read'],
            'key_type' => 'user',
        ], $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    private function firstRow(): array
    {
        $rows = $this->db->execute('SELECT * FROM api_keys LIMIT 1');
        $this->assertNotSame([], $rows);

        return $rows[0];
    }
}
