<?php declare(strict_types=1);

namespace CoreMusic\Api\Tests\Unit;

use CoreMusic\Api\Controller\UserController;
use CoreMusic\Api\Service\IUserService;
use PHPUnit\Framework\TestCase;

/**
 * UserController — profil uçları (stub IUserService).
 * Faz 4: mass-assignment serviste (whitelist display_name/avatar_url).
 */
final class UserControllerTest extends TestCase
{
    private const USER = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

    /** @param array<string, mixed> $overrides */
    private function service(array $overrides = []): IUserService
    {
        return new class ($overrides) implements IUserService {
            /** @param array<string, mixed> $config */
            public function __construct(private readonly array $config) {}

            public function getProfile(string $userId): ?array
            {
                // `??` tuzağı: null coalescing null'ı default'a çevirir → array_key_exists şart.
                return array_key_exists('profile', $this->config)
                    ? $this->config['profile']
                    : ['user_id_hex' => $userId, 'display_name' => 'Demo'];
            }

            public function updateProfile(string $userId, array $data): ?array
            {
                if (array_key_exists('null_update', $this->config)) {
                    return null;
                }

                return ['user_id_hex' => $userId, 'display_name' => $data['display_name'] ?? 'Demo'];
            }
        };
    }

    /** @param array<string, mixed> $body */
    private function handle(IUserService $service, string $action, ?string $userId = self::USER, array $body = []): array
    {
        $request = ['body' => $body];
        if ($userId !== null) {
            $request['_auth_user'] = ['id' => $userId, 'authenticated' => true, 'method' => 'session'];
        }

        return (new UserController($service))->handle(['action' => $action], $request);
    }

    public function testProfileReturns401WithoutAuth(): void
    {
        $result = $this->handle($this->service(), 'profile', null);

        $this->assertSame('UNAUTHORIZED', $result['error']['code']);
        $this->assertSame(401, http_response_code());
    }

    public function testProfileReturnsProfile(): void
    {
        $result = $this->handle($this->service(), 'profile');

        $this->assertTrue($result['success']);
        $this->assertSame('Demo', $result['data']['profile']['display_name']);
    }

    public function testProfileReturns404WhenMissing(): void
    {
        $result = $this->handle($this->service(['profile' => null]), 'profile');

        $this->assertSame('NOT_FOUND', $result['error']['code']);
        $this->assertSame(404, http_response_code());
    }

    public function testUpdateProfileAppliesBodyAndReturnsUpdated(): void
    {
        $result = $this->handle($this->service(), 'updateProfile', self::USER, ['display_name' => 'Yeni Ad']);

        $this->assertTrue($result['success']);
        $this->assertSame('Yeni Ad', $result['data']['profile']['display_name']);
    }

    public function testUpdateProfileReturns404WhenUserMissing(): void
    {
        $result = $this->handle($this->service(['null_update' => true]), 'updateProfile', self::USER, []);

        $this->assertSame('NOT_FOUND', $result['error']['code']);
    }
}
