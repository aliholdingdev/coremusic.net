<?php declare(strict_types=1);

namespace CoreMusic\Test\Middleware;

use CoreMusic\Middleware\ValidationMiddleware;
use PHPUnit\Framework\TestCase;

/**
 * B-F-13 regresyonu — required_fields contract zinciri.
 *
 * ValidationMiddleware `routeMeta['required_fields']` okur ama kernel bu anahtarı
 * HİÇ ÜRETMİYORDU (resolveRouteMeta yalnız requiredRole/requiredPermission) →
 * middleware garantili no-op idi. Kernel artık route meta'dan geçirir; bu test
 * middleware'in doldurulmuş meta ile GERÇEKTEN çalıştığını kanıtlar.
 *
 * Beklenen kurallar biçimi: [alan => ['required' => bool, 'label' =>?,
 * 'min_length' => ?, 'max_length' => ?, 'email' => ?, 'enum' => ?]]
 */
final class ValidationMiddlewareTest extends TestCase
{
    private function handle(array $request): array
    {
        $result = (new ValidationMiddleware())->handle(
            $request,
            static fn (array $req): array => ['httpStatus' => 0, 'type' => 'json', 'halt' => false]
        );
        return $result;
    }

    public function testEmptyMeta_passesThrough(): void
    {
        $result = $this->handle([
            'method' => 'POST',
            'uri' => '/home',
            '_route_meta' => ['requiredRole' => null, 'requiredPermission' => null, 'required_fields' => []],
            'body' => [],
        ]);

        $this->assertSame(0, $result['httpStatus'], 'required_fields boşsa pasif kalmalı');
    }

    public function testMissingRequiredField_halt422(): void
    {
        $result = $this->handle([
            'method' => 'POST',
            'uri' => '/home',
            '_route_meta' => [
                'required_fields' => ['email' => ['required' => true, 'label' => 'E-posta', 'email' => true]],
            ],
            'body' => [],
        ]);

        $this->assertTrue($result['halt'] ?? false);
        $this->assertSame(422, $result['httpStatus']);
        $this->assertSame('validation_failed', $result['body']['error']);
        $this->assertArrayHasKey('email', $result['body']['errors']);
    }

    public function testValidBody_passesThrough(): void
    {
        $result = $this->handle([
            'method' => 'POST',
            'uri' => '/home',
            '_route_meta' => [
                'required_fields' => [
                    'email' => ['required' => true, 'email' => true],
                    'role'  => ['required' => true, 'enum' => ['user', 'admin']],
                ],
            ],
            'body' => ['email' => 'bayram@example.test', 'role' => 'admin'],
        ]);

        $this->assertSame(0, $result['httpStatus'], 'Geçerli gövde geçmeli');
    }

    public function testGetRequests_skipValidation(): void
    {
        $result = $this->handle([
            'method' => 'GET',
            'uri' => '/home',
            '_route_meta' => ['required_fields' => ['email' => ['required' => true]]],
            'body' => [],
        ]);

        $this->assertSame(0, $result['httpStatus'], 'GET state-changing değildir');
    }

    public function testEnumAndEmailRules_enforced(): void
    {
        $result = $this->handle([
            'method' => 'POST',
            'uri' => '/x',
            '_route_meta' => [
                'required_fields' => [
                    'email' => ['required' => true, 'email' => true],
                    'role'  => ['required' => true, 'enum' => ['user', 'admin']],
                ],
            ],
            'body' => ['email' => 'bozuk', 'role' => 'superuser'],
        ]);

        $this->assertTrue($result['halt'] ?? false);
        $this->assertArrayHasKey('email', $result['body']['errors']);
        $this->assertArrayHasKey('role', $result['body']['errors']);
    }
}
