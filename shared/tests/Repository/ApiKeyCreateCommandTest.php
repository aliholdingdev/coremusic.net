<?php declare(strict_types=1);

namespace CoreMusic\Test\Repository;

use CoreMusic\Console\ApiKeyCreateCommand;
use CoreMusic\Console\SqliteFileGateway;
use CoreMusic\Console\UsageError;
use CoreMusic\Repository\ApiKeyRepository;
use PHPUnit\Framework\TestCase;

/**
 * `bin/api-key-create.php` — ADR-020 §2.1 / §2.2-1 uçtan test.
 *
 * Kanıtlar:
 *   - CLI gerçek bir alt süreç olarak çalışır (exit 0 / 2 ayrımı)
 *   - Ham anahtar EKRANA TAM BİR KEZ basılır
 *   - DB'ye yalnız sha256 hash + key_prefix yazılır (dosya baytlarında yok)
 *   - ApiKeyRepository::verify() ile uçtan doğrulanır:
 *     prefix→hash eşleşmesi · yanlış key · pasif key · süresi dolmuş key ·
 *     scope yetmezliği · IP allowlist
 *
 * Canlı MySQL bu ortamda yok; CLI'ın offline ucu `--sqlite=<yol>` ile sınanır
 * (şema keşfi YOK — DDL, migration'daki `coremusic_auth.api_keys` ile birebir).
 */
final class ApiKeyCreateCommandTest extends TestCase
{
    private const BIN = __DIR__ . '/../../../bin/api-key-create.php';

    private string $dbPath = '';
    private SqliteFileGateway $gateway;
    private ApiKeyRepository $repository;

    protected function setUp(): void
    {
        require_once self::BIN;

        $this->dbPath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR . 'cm-apikey-cli-' . bin2hex(random_bytes(6)) . '.sqlite';

        $this->gateway    = new SqliteFileGateway($this->dbPath);
        $this->repository = new ApiKeyRepository($this->gateway);
    }

    protected function tearDown(): void
    {
        unset($this->gateway, $this->repository);

        foreach ([$this->dbPath, $this->dbPath . '-wal', $this->dbPath . '-shm'] as $file) {
            if ($file !== '' && is_file($file)) {
                @unlink($file);
            }
        }
    }

    /* --------------------------------------------------- parse / ttl --- */

    public function testParseAcceptsEqualsAndSpaceForms(): void
    {
        $opts = ApiKeyCreateCommand::parse([
            'bin', '--name', 'ci-runner', '--type=service', '--scopes', 'api.access,auth.read',
            '--ip=10.0.0.5', '--ttl=30d', '--user=0194f1e2000070008000000000000001',
        ]);

        $this->assertSame('ci-runner', $opts['name']);
        $this->assertSame('service', $opts['type']);
        $this->assertSame('api.access,auth.read', $opts['scopes']);
        $this->assertSame('10.0.0.5', $opts['ip']);
        $this->assertSame('30d', $opts['ttl']);
        $this->assertSame('0194f1e2000070008000000000000001', $opts['user']);
    }

    public function testParseRejectsUnknownOption(): void
    {
        $this->expectException(UsageError::class);
        ApiKeyCreateCommand::parse(['bin', '--nmae=x']);
    }

    public function testParseRejectsOptionWithoutValue(): void
    {
        $this->expectException(UsageError::class);
        ApiKeyCreateCommand::parse(['bin', '--name']);
    }

    public function testTtlToExpiresAtSupportsUnits(): void
    {
        $cases = [
            [null, null],
            ['never', null],
            ['900', 900],
            ['900s', 900],
            ['30m', 1800],
            ['12h', 43200],
            ['30d', 2592000],
            ['7w', 604800 * 7],
        ];

        foreach ($cases as [$ttl, $seconds]) {
            $result = ApiKeyCreateCommand::ttlToExpiresAt($ttl);

            if ($seconds === null) {
                $this->assertNull($result, var_export($ttl, true) . ' → süresiz olmalı');
                continue;
            }

            $this->assertIsString($result);
            // `date()` + `time()` aynı saniyede kalmayabilir → 3 sn payı.
            $this->assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl);
        }
    }

    public function testTtlToExpiresAtRejectsGarbage(): void
    {
        foreach (['yesterday', '-1h', '12x', '3.5d'] as $bad) {
            try {
                ApiKeyCreateCommand::ttlToExpiresAt($bad);
                $this->fail("ttl '{$bad}' reddedilmeliydi");
            } catch (UsageError) {
                $this->addToAssertionCount(1);
            }
        }
    }

    /* -------------------------------------------------- CLI uçtan test --- */

    public function testCliCreatesKeyAndRepositoryVerifiesItEndToEnd(): void
    {
        $userId = '0194f1e2-0000-7000-8000-000000000001';

        $run = $this->runCli([
            '--sqlite=' . $this->dbPath,
            '--type=service',
            '--user=' . $userId,
            '--name=e2e-runner',
            '--scopes=api.access,auth.read',
            '--ip=10.0.0.5',
            '--ttl=1d',
        ]);

        $this->assertSame(0, $run['exit'], "CLI exit 0 olmalı; stderr: {$run['err']}");
        $this->assertSame('', trim($run['err']), 'Başarılı üretimde STDERR sessiz olmalı');

        $rawKey = $this->rawKeyFrom($run['out']);

        // 1) Ham anahtar EKRANA TAM BİR KEZ basılır.
        $this->assertStringStartsWith('cm_live_', $rawKey);
        $this->assertMatchesRegularExpression(ApiKeyRepository::RAW_KEY_PATTERN, $rawKey);
        $this->assertSame(1, substr_count($run['out'], $rawKey), 'Raw key yalnızca bir kez basılmalı');
        // Status satırları yalnız prefix içerir (prefix_secret birleşimi yalnızca
        // RAW KEY bloğunda geçer).
        $this->assertStringContainsString('key_prefix : ' . ApiKeyRepository::prefixFromRaw($rawKey), $run['out']);
        $this->assertStringNotContainsString(
            ApiKeyRepository::prefixFromRaw($rawKey) . '_',
            str_replace($rawKey, '', $run['out']),
            'Raw key bloğu dışında secret\'ın parçası basılmamalı'
        );

        // 2) DB dosyasında ham anahtar YOK (yalnız hash + prefix).
        $dbBytes = (string) file_get_contents($this->dbPath);
        $this->assertStringNotContainsString($rawKey, $dbBytes, 'DB\'de ham anahtar saklanmamalı');
        $this->assertStringContainsString(hash('sha256', $rawKey), $dbBytes, 'DB\'de sha256 hash bulunmalı');

        // 3) Repo verify() — prefix → hash eşleşmesi.
        $context = $this->repository->verify($rawKey, '10.0.0.5');
        $this->assertIsArray($context, 'Doğru anahtar verify() ile geçmeli');
        $this->assertSame(ApiKeyRepository::prefixFromRaw($rawKey), $context['key_prefix']);
        $this->assertSame(str_replace('-', '', strtolower($userId)), $context['user_id']);
        $this->assertSame('service', $context['key_type']);
        $this->assertContains('api.access', $context['scopes']);
        $this->assertContains('auth.read', $context['scopes']);

        // 4) Yanlış key (aynı prefix, bozuk secret) → hash_equals reddeder.
        $tampered = 'cm_live_' . ApiKeyRepository::prefixFromRaw($rawKey) . '_' . str_repeat('A', 43);
        $this->assertNull($this->repository->verify($tampered, '10.0.0.5'));
        $this->assertSame('hash_mismatch', $this->repository->inspect($tampered, '10.0.0.5')['reason']);

        // 5) Scope yetmezliği.
        $this->assertNull($this->repository->verify($rawKey, '10.0.0.5', 'music.write'));
        $this->assertSame('scope_denied', $this->repository->inspect($rawKey, '10.0.0.5', 'music.write')['reason']);

        // 6) IP allowlist ihlali.
        $this->assertNull($this->repository->verify($rawKey, '10.0.0.6'));
        $this->assertSame('ip_not_allowed', $this->repository->inspect($rawKey, '10.0.0.6')['reason']);

        // 7) Süresi dolmuş key.
        $this->gateway->write('UPDATE api_keys SET expires_at = :past WHERE key_prefix = :prefix', [
            'past'   => date('Y-m-d H:i:s', time() - 60),
            'prefix' => ApiKeyRepository::prefixFromRaw($rawKey),
        ]);
        $this->assertNull($this->repository->verify($rawKey, '10.0.0.5'));
        $this->assertSame('expired', $this->repository->inspect($rawKey, '10.0.0.5')['reason']);

        // 8) Pasif key (üretilen test anahtarı rapor sonrası pasifleştirilir).
        $this->gateway->write('UPDATE api_keys SET is_active = 0, expires_at = NULL WHERE key_prefix = :prefix', [
            'prefix' => ApiKeyRepository::prefixFromRaw($rawKey),
        ]);
        $this->assertNull($this->repository->verify($rawKey, '10.0.0.5'));
        $this->assertSame('inactive', $this->repository->inspect($rawKey, '10.0.0.5')['reason']);
    }

    public function testCliUsageErrorsExitTwo(): void
    {
        $cases = [
            ['--sqlite=' . $this->dbPath],                              // --name yok
            ['--sqlite=' . $this->dbPath, '--name=x', '--type=root'],   // geçersiz tip
            ['--sqlite=' . $this->dbPath, '--name=x'],                  // user tipi, --user yok
            ['--sqlite=' . $this->dbPath, '--name=x', '--ttl=banana'],  // geçersiz ttl
            ['--unknown=1'],                                            // bilinmeyen seçenek
        ];

        foreach ($cases as $argv) {
            $run = $this->runCli($argv);
            $this->assertSame(2, $run['exit'], 'exit 2 bekleniyordu: ' . implode(' ', $argv));
            $this->assertStringContainsString('HATA:', $run['err']);
            $this->assertStringNotContainsString('cm_live_', $run['out'] . $run['err']);
        }
    }

    public function testCliHelpExitsZeroWithoutTouchingDatabase(): void
    {
        $fresh = $this->dbPath . '.fresh';

        $run = $this->runCli(['--help', '--sqlite=' . $fresh]);

        $this->assertSame(0, $run['exit']);
        $this->assertStringContainsString('Kullanım:', $run['out']);
        $this->assertFileDoesNotExist($fresh, '--help veri tabanı açmamalı');
    }

    public function testCliNeverReadsEnvFiles(): void
    {
        $source = (string) file_get_contents(self::BIN);

        // Guardrail: `.env` dosyası okunmaz; yalnızca ortam değişkenleri.
        $this->assertDoesNotMatchRegularExpression(
            '/(file_get_contents|parse_ini_file|fopen|require|require_once|include|include_once)\s*\([^)]*\.env/',
            $source,
            'CLI .env dosyası okumamalı'
        );
        $this->assertStringNotContainsString('EnvParser', $source, 'CLI .env parser çağırmamalı');
    }

    /* ---------------------------------------------------------- yardımcı --- */

    /**
     * @param list<string> $args
     * @return array{exit: int, out: string, err: string}
     */
    private function runCli(array $args): array
    {
        $command = array_merge([PHP_BINARY, self::BIN], $args);

        $pipes  = [];
        $handle = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__, 3)
        );

        $this->assertIsResource($handle, 'CLI alt süreci açılamadı');

        fclose($pipes[0]);
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];
    }

    private function rawKeyFrom(string $output): string
    {
        $matched = preg_match('/--- RAW KEY[^\n]*---\R(\S+)\R--- end ---/u', $output, $m);

        $this->assertSame(1, $matched, "Raw key bloğu bulunamadı:\n{$output}");

        return (string) $m[1];
    }
}
