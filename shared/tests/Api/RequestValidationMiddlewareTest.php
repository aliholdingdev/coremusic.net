<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use CoreMusic\Api\Middleware\RequestValidationMiddleware;
use CoreMusic\Api\Middleware\Validator;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * ADR-020 §2.2 istek doğrulama — 415/400/422 akışı ve fatal-risk kapanışı.
 *
 * Kritik regresyon pinleri:
 *   - bozuk JSON → 400 INVALID_JSON (422'ye düşmez, exception fırlatmaz)
 *   - JSON olmayan Content-Type → 415
 *   - kural ihlali / boş gövde + required → 422 + alan hataları
 *   - return type'ları sınıf tipinde olmamalı (vendor kaybında fatal imkânsız)
 */
final class RequestValidationMiddlewareTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    /** @return array<string, mixed> */
    private function next(): callable
    {
        return static fn (array $request): array => ['httpStatus' => 200, 'passthrough' => true];
    }

    public function testRouteWithoutValidationRulesPassesThrough(): void
    {
        $middleware = new RequestValidationMiddleware();

        $response = $middleware(
            ['method' => 'POST', 'server' => [], 'body' => '{broken json'],
            $this->next()
        );

        $this->assertTrue($response['passthrough'] ?? false, 'Kural yoksa gövde hiç okunmamalı');
    }

    public function testValidJsonBodyPassesThrough(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => ['CONTENT_TYPE' => 'application/json'],
            'body'   => '{"email":"a@b.co"}',
            '_route' => ['validation' => ['email' => ['required' => true, 'type' => 'email']]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertTrue($response['passthrough'] ?? false);
    }

    public function testMalformedJsonReturns400InvalidJson(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => ['CONTENT_TYPE' => 'application/json'],
            'body'   => '{"email": ',
            '_route' => ['validation' => ['email' => ['required' => true, 'type' => 'email']]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('INVALID_JSON', $response['error']['code'] ?? null);
        $this->assertArrayNotHasKey('passthrough', $response);
    }

    public function testUnsupportedContentTypeReturns415(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => ['CONTENT_TYPE' => 'text/plain'],
            'body'   => 'email=a@b.co',
            '_route' => ['validation' => ['email' => ['required' => true]]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('UNSUPPORTED_MEDIA_TYPE', $response['error']['code'] ?? null);
    }

    public function testMissingContentTypeReturns415(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => [],
            'body'   => '{"email":"a@b.co"}',
            '_route' => ['validation' => ['email' => ['required' => true]]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('UNSUPPORTED_MEDIA_TYPE', $response['error']['code'] ?? null);
    }

    public function testEmptyBodyWithRequiredFieldReturns422(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => ['CONTENT_TYPE' => 'application/json'],
            'body'   => '{}',
            '_route' => ['validation' => ['email' => ['required' => true, 'type' => 'email']]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('VALIDATION_ERROR', $response['error']['code'] ?? null);
        $details = $response['error']['details'] ?? [];
        $this->assertArrayHasKey('email', $details);
        $this->assertStringContainsString('required', (string) $details['email']);
    }

    public function testTypeMismatchReturns422WithFieldMessage(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'POST',
            'server' => ['CONTENT_TYPE' => 'application/json'],
            'body'   => '{"email":"not-an-email"}',
            '_route' => ['validation' => ['email' => ['required' => true, 'type' => 'email']]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('VALIDATION_ERROR', $response['error']['code'] ?? null);
        $this->assertStringContainsString(
            'email',
            (string) ($response['error']['details']['email'] ?? '')
        );
    }

    public function testQueryValidationAppliesToGetRequests(): void
    {
        $middleware = new RequestValidationMiddleware();
        $request = [
            'method' => 'GET',
            'server' => [],
            'query'  => ['page' => 'abc'],
            '_route' => ['validation' => ['page' => ['type' => 'int']]],
        ];

        $response = $middleware($request, $this->next());

        $this->assertSame('VALIDATION_ERROR', $response['error']['code'] ?? null);
    }

    /**
     * FATAL RISK KAPANIŞI: middleware ve kural motorunun hiçbir method
     * return type'ı sınıf adı göstermemeli — vendor/Respect kaybında bile
     * TypeError (fatal) mümkün olmamalı.
     */
    public function testNoClassTypedReturnTypesOnValidationChain(): void
    {
        foreach ([RequestValidationMiddleware::class, Validator::class] as $class) {
            $reflection = new \ReflectionClass($class);
            $this->assertTrue($reflection->isInstantiable(), "{$class} instantiate edilebilmeli");

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $type = $method->getReturnType();
                if ($type === null) {
                    continue;
                }

                // Builtin (array/string/bool/int/void/...) harici hiçbir tip olmamalı.
                $names = $type instanceof \ReflectionNamedType
                    ? [$type->getName()]
                    : [];

                foreach ($names as $name) {
                    $this->assertTrue(
                        $type->isBuiltin(),
                        "{$class}::{$method->getName()}() non-builtin return type '{$name}' → fatal riski"
                    );
                }
            }
        }

        // Ek güvence: bağımlılık sınıfları var olmalı (autoload sağlıklı).
        $this->assertTrue(class_exists(RequestValidationMiddleware::class));
        $this->assertTrue(class_exists(Validator::class));
    }

    public function testValidatorStandaloneProducesFieldErrors(): void
    {
        $validator = new Validator();

        $errors = $validator->validate(
            ['age' => 15],
            ['age' => ['required' => true, 'type' => 'int', 'min' => 18]]
        );

        $this->assertArrayHasKey('age', $errors);
        $this->assertStringContainsString('18', $errors['age']);
    }
}
