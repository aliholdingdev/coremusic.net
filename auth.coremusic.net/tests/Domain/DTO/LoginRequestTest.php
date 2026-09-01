<?php declare(strict_types=1);

namespace CoreMusic\Auth\Test\Domain\DTO;

use CoreMusic\Auth\Domain\DTO\LoginRequest;
use PHPUnit\Framework\TestCase;

class LoginRequestTest extends TestCase
{
    public function testFromArrayCreatesLoginRequest(): void
    {
        $post = ['email' => 'test@example.com', 'password' => 'secret123'];
        $server = ['REMOTE_ADDR' => '192.168.1.1'];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('test@example.com', $request->identity);
        $this->assertSame('secret123', $request->password);
        $this->assertSame('neutral', $request->visitorGender);
        $this->assertSame('192.168.1.1', $request->clientIp);
    }

    public function testFromArrayWithIdentityField(): void
    {
        $post = ['identity' => 'testuser', 'password' => 'secret123'];
        $server = ['REMOTE_ADDR' => '127.0.0.1'];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('testuser', $request->identity);
    }

    public function testFromArrayTrimsWhitespace(): void
    {
        $post = ['email' => '  test@example.com  ', 'password' => 'secret123'];
        $server = ['REMOTE_ADDR' => '127.0.0.1'];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('test@example.com', $request->identity);
    }

    public function testFromArrayWithGender(): void
    {
        $post = ['email' => 'test@example.com', 'password' => 'secret123'];
        $server = ['REMOTE_ADDR' => '127.0.0.1'];

        $request = LoginRequest::fromArray($post, $server, 'male');

        $this->assertSame('male', $request->visitorGender);
    }
}
