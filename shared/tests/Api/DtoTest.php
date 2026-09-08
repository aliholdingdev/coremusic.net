<?php
declare(strict_types=1);

namespace CoreMusic\Test\Api;

use PHPUnit\Framework\TestCase;
use CoreMusic\Api\Dto\Request\LoginRequest;
use CoreMusic\Api\Dto\Request\RegisterRequest;
use CoreMusic\Api\Dto\Response\UserResponse;
use CoreMusic\Api\Dto\Response\MusicResponse;

final class DtoTest extends TestCase
{
    public function testLoginRequestCreation(): void
    {
        $request = new LoginRequest('user@example.com', 'secret123', true);
        $this->assertEquals('user@example.com', $request->getEmail());
        $this->assertEquals('secret123', $request->getPassword());
        $this->assertTrue($request->isRememberMe());
    }

    public function testLoginRequestDefaultRememberMe(): void
    {
        $request = new LoginRequest('user@example.com', 'secret123');
        $this->assertFalse($request->isRememberMe());
    }

    public function testLoginRequestFromArray(): void
    {
        $request = LoginRequest::fromArray([
            'email' => 'test@test.com',
            'password' => 'pass',
            'rememberMe' => true,
        ]);
        $this->assertEquals('test@test.com', $request->getEmail());
        $this->assertTrue($request->isRememberMe());
    }

    public function testLoginRequestToArray(): void
    {
        $request = new LoginRequest('a@b.com', 'pw', false);
        $array = $request->toArray();
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('password', $array);
        $this->assertArrayHasKey('rememberMe', $array);
        $this->assertEquals('a@b.com', $array['email']);
    }

    public function testRegisterRequestCreation(): void
    {
        $request = new RegisterRequest('john', 'john@test.com', 'John Doe', 'pass123', 'male');
        $this->assertEquals('john', $request->getUsername());
        $this->assertEquals('john@test.com', $request->getEmail());
        $this->assertEquals('John Doe', $request->getDisplayName());
        $this->assertEquals('male', $request->getGender());
    }

    public function testUserResponseCreation(): void
    {
        $response = new UserResponse('123', 'john', 'john@test.com', 'John', '/avatar.jpg', 'male', ['user', 'premium']);
        $this->assertEquals('123', $response->getId());
        $this->assertEquals('john', $response->getUsername());
        $this->assertEquals(['user', 'premium'], $response->getRoles());
    }

    public function testUserResponseFromArray(): void
    {
        $response = UserResponse::fromArray([
            'id' => '456',
            'username' => 'jane',
            'email' => 'jane@test.com',
            'displayName' => 'Jane',
            'avatar' => '',
            'gender' => 'female',
            'roles' => ['user'],
        ]);
        $this->assertEquals('456', $response->getId());
        $this->assertEquals('jane', $response->getUsername());
    }

    public function testMusicResponseCreation(): void
    {
        $response = new MusicResponse('m1', 'Song Title', 'Artist Name', 'Album', 240, '/cover.jpg', '/stream.mp3');
        $this->assertEquals('m1', $response->getId());
        $this->assertEquals('Song Title', $response->getTitle());
        $this->assertEquals(240, $response->getDuration());
    }

    public function testMusicResponseToArray(): void
    {
        $response = new MusicResponse('m1', 'Song', 'Artist', 'Album', 300, '/cover', '/stream');
        $array = $response->toArray();
        $this->assertEquals('m1', $array['id']);
        $this->assertEquals(300, $array['duration']);
    }
}
