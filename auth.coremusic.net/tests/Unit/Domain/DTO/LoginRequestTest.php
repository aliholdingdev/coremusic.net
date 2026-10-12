<?php

declare(strict_types=1);

namespace CoreMusic\Auth\Test\Unit\Domain\DTO;

use CoreMusic\Auth\Domain\DTO\LoginRequest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LoginRequestTest extends TestCase
{
    #[Test]
    public function from_array_creates_request_with_email(): void
    {
        $post = ['email' => 'test@example.com', 'password' => 'secret'];
        $server = ['REMOTE_ADDR' => '192.168.1.1'];

        $request = LoginRequest::fromArray($post, $server, 'female');

        $this->assertSame('test@example.com', $request->identity);
        $this->assertSame('secret', $request->password);
        $this->assertSame('female', $request->visitorGender);
        $this->assertSame('192.168.1.1', $request->clientIp);
    }

    #[Test]
    public function from_array_creates_request_with_identity(): void
    {
        $post = ['identity' => 'username123', 'password' => 'pass'];
        $server = ['REMOTE_ADDR' => '10.0.0.1'];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('username123', $request->identity);
        $this->assertSame('pass', $request->password);
        $this->assertSame('neutral', $request->visitorGender);
        $this->assertSame('10.0.0.1', $request->clientIp);
    }

    #[Test]
    public function from_array_defaults_to_neutral_gender(): void
    {
        $post = ['email' => 'user@test.com', 'password' => 'pass'];
        $server = [];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('neutral', $request->visitorGender);
    }

    #[Test]
    public function from_array_defaults_to_localhost_ip(): void
    {
        $post = ['email' => 'user@test.com', 'password' => 'pass'];
        $server = [];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('127.0.0.1', $request->clientIp);
    }

    #[Test]
    public function from_array_trims_identity(): void
    {
        $post = ['email' => '  test@example.com  ', 'password' => 'pass'];
        $server = [];

        $request = LoginRequest::fromArray($post, $server);

        $this->assertSame('test@example.com', $request->identity);
    }
}
